<?php

namespace app\api\logic;

use think\facade\Db;
use think\Exception;
use app\common\service\ZodiacYearService;
use app\common\service\ZodiacService;
use app\common\service\OrderSnService;
use app\common\model\lottery\AccountLog;
use app\common\model\lottery\WinningRecord;

class LotteryBetLogic
{
    /**
     * 投注下单
     */
    public static function placeBet($params, $userId)
    {
        $gameId = $params['game_id'];
        $plateCode = $params['plate_code'];
        $issue = $params['issue'];
        $methodName = $params['method_name'];
        $betContent = $params['bet_content'];
        $betAmount = $params['bet_amount'];

        Db::startTrans();
        try {
            // 1. 验证盘口和期次
            $plate = Db::name('lottery_plate')->where(['game_id' => $gameId, 'plate_code' => $plateCode, 'status' => 1])->find();
            if (!$plate) throw new Exception('盘口不存在');

            $issueModel = Db::name('lottery_issue')->where(['game_id' => $gameId, 'plate_id' => $plate['id'], 'issue' => $issue])->find();
            if (!$issueModel || $issueModel['status'] != 1) throw new Exception('期次不可投注，状态：' . ($issueModel['status'] ?? '未知'));

            // 2. 获取玩法赔率
            $method = Db::name('play_method')->where(['game_id' => $gameId, 'name' => $methodName, 'is_enabled' => 1])->find();
            if (!$method) throw new Exception('玩法不存在');

            $odds = self::resolveBetOdds($method, (string)$betContent);

            // 3. 锁定用户账户
            $account = Db::name('user_account')->where('user_id', $userId)->lock(true)->find();
            if (!$account || $account['balance'] < $betAmount) throw new Exception('余额不足');

            // 4. 扣减余额
            Db::name('user_account')->where('user_id', $userId)->dec('balance', $betAmount)->inc('frozen_amount', $betAmount)->update();

            // 5. 生成投注记录
            $sn = OrderSnService::generateBetSn($userId);
            $userExtend = Db::name('user_extend')->where('user_id', $userId)->find();

            Db::name('betting_record')->insert([
                'sn' => $sn,
                'user_id' => $userId,
                'game_id' => $gameId,
                'plate_id' => $plate['id'],
                'plate_code' => $plateCode,
                'issue_id' => $issueModel['id'],
                'issue' => $issue,
                'method_id' => $method['id'],
                'method_name' => $methodName,
                'bet_content' => $betContent,
                'bet_amount' => $betAmount,
                'bet_multiple' => 1,
                'total_amount' => $betAmount,
                'odds' => $odds,
                'status' => 0,
                'parent_id' => $userExtend['parent_id'] ?? 0,
                'ancestor_ids' => $userExtend['ancestor_ids'] ?? '',
                'ip' => request()->ip(),
                'created_at' => time()
            ]);

            // 6. 记录流水
            AccountLog::recordBetting(
                $userId,
                $betAmount,
                $account,
                $sn,
                "投注: {$methodName} {$betContent}"
            );

            Db::commit();
            return ['sn' => $sn, 'balance' => $account['balance'] - $betAmount];
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 开奖判奖
     */
    public static function settleBetting($issueId, $drawnNumbers)
    {
        $issue = Db::name('lottery_issue')->where('id', $issueId)->find();
        if (!$issue) throw new Exception('期次不存在');

        $year = (int)substr($issue['issue'], 0, 4);
        $bettings = Db::name('betting_record')->where(['issue_id' => $issueId, 'status' => 0])->select();

        foreach ($bettings as $bet) {
            // 读取投注类型
            $betType = $bet['bet_type'] ?? 'win';
            $resultType = self::checkWin($bet['method_name'], $bet['bet_content'], $drawnNumbers, $year, $betType);
            $isWin = $resultType === 'win';
            $isDraw = $resultType === 'draw';
            $prizeAmount = $isWin ? $bet['total_amount'] * $bet['odds'] : ($isDraw ? $bet['total_amount'] : 0);
            $status = $isWin ? 1 : ($isDraw ? 4 : 2);

            // 更新投注记录
            Db::name('betting_record')->where('id', $bet['id'])->update([
                'status' => $status,
                'prize_amount' => $prizeAmount,
                'is_settled' => 1,
                'settled_at' => time()
            ]);

            // 查询用户账户
            $account = Db::name('user_account')->where('user_id', $bet['user_id'])->find();

            if ($isWin) {
                Db::name('user_account')->where('user_id', $bet['user_id'])
                    ->dec('frozen_amount', $bet['total_amount'])
                    ->inc('balance', $prizeAmount)
                    ->inc('total_prize', $prizeAmount)
                    ->update();

                // 记录中奖单
                WinningRecord::recordWin($bet, $prizeAmount);

                // 记录中奖流水
                AccountLog::recordWinning(
                    $bet['user_id'],
                    $prizeAmount,
                    $bet['total_amount'],
                    $account,
                    $bet['sn'],
                    "中奖派奖: {$bet['method_name']} {$bet['bet_content']}"
                );
            } elseif ($isDraw) {
                Db::name('user_account')->where('user_id', $bet['user_id'])
                    ->dec('frozen_amount', $bet['total_amount'])
                    ->inc('balance', $bet['total_amount'])
                    ->update();

                AccountLog::recordRefund(
                    $bet['user_id'],
                    $bet['total_amount'],
                    $account,
                    $bet['sn'],
                    "49和局退款: {$bet['method_name']} {$bet['bet_content']}"
                );
            } else {
                Db::name('user_account')->where('user_id', $bet['user_id'])->dec('frozen_amount', $bet['total_amount'])->update();

                // 未中奖解冻
                AccountLog::recordUnfreeze(
                    $bet['user_id'],
                    $bet['total_amount'],
                    $account,
                    $bet['sn'],
                    "未中奖解冻: {$bet['method_name']} {$bet['bet_content']}"
                );
            }
        }
    }
    /**
     * 判断是否中奖
     *
     * @param string $methodName 玩法名称
     * @param string $betContent 投注内容
     * @param array $drawnNumbers 开奖号码
     * @param int $year 年份
     * @param string $betType 投注类型(用户只能投注'win',数据库可能存在历史'not_win'记录)
     *                        - 'win': 号码命中即中奖(用户正常投注)
     *                        - 'not_win': 号码未命中即中奖(历史遗留,不再使用)
     * @return string win|lose|draw
     */
    public static function checkWin($methodName, $betContent, $drawnNumbers, $year, $betType = 'win')
    {
        $special = $drawnNumbers[7] ?? $drawnNumbers[6]; // 特码，兼容第8个或第7个元素
        $specialNumber = (int)$special;
        $regularNumbers = array_map('intval', array_slice($drawnNumbers, 0, 6)); // 前6个正码
        $allNumbers = $regularNumbers;
        if ($specialNumber > 0 && !in_array($specialNumber, $allNumbers, true)) {
            $allNumbers[] = $specialNumber;
        }
        $allNumbers = array_values(array_unique($allNumbers));
        $missRule = self::getNumberMissRule($methodName);
        if ($missRule) {
            $betNumbers = self::parseNumberSelections($betContent);
            if (count($betNumbers) !== $missRule['select_count']) {
                return 'lose';
            }

            $hitCount = count(array_intersect($betNumbers, $allNumbers));
            return self::resolveResult($hitCount === 0, $betType);
        }

        $comboRule = self::getNumberComboRule($methodName);

        if ($comboRule) {
            $betNumbers = self::parseNumberSelections($betContent);
            if (count($betNumbers) !== $comboRule['select_count']) {
                return 'lose';
            }

            $hitCount = count(array_intersect($betNumbers, $regularNumbers));
            return self::resolveResult($hitCount >= $comboRule['hit_count'], $betType);
        }

        switch ($methodName) {
            case '特码':
            case '特碼':
                $hit = (int)$betContent === $specialNumber;
                return self::resolveResult($hit, $betType);

            case '正码':
            case '正碼':
                $hit = in_array((int)$betContent, $regularNumbers);
                return self::resolveResult($hit, $betType);

            case '平码':
            case '平碼':
                $hit = in_array((int)$betContent, $allNumbers);
                return self::resolveResult($hit, $betType);

            case '平肖':
                $betZodiacs = ZodiacService::normalizeZodiacSelections(explode(',', $betContent), $year);
                if (empty($betZodiacs)) {
                    return 'lose';
                }
                $drawnZodiacs = ZodiacService::convertNumbersToZodiacsWithYear($allNumbers, $year);
                $hit = count(array_intersect($betZodiacs, $drawnZodiacs)) > 0;
                return self::resolveResult($hit, $betType);

            case '特肖':
                $specialZodiac = ZodiacYearService::getZodiacByNumberAndYear($specialNumber, $year);
                $betZodiacs = ZodiacService::normalizeZodiacSelections(explode(',', $betContent), $year);
                if (empty($betZodiacs)) {
                    return 'lose';
                }
                $hit = in_array($specialZodiac, $betZodiacs, true);
                return self::resolveResult($hit, $betType);

            case '正肖':
                $betZodiacs = ZodiacService::normalizeZodiacSelections(explode(',', $betContent), $year);
                if (empty($betZodiacs)) {
                    return 'lose';
                }
                $drawnZodiacs = ZodiacService::convertNumbersToZodiacsWithYear($allNumbers, $year);
                $hit = count(array_intersect($betZodiacs, $drawnZodiacs)) > 0;
                return self::resolveResult($hit, $betType);

            case '特码单双':
            case '特碼單雙':
            case '特碼单双':
                if ($specialNumber === 49) {
                    return 'draw';
                }
                $selection = self::normalizeOddEvenSelection($betContent);
                if (!$selection) {
                    return 'lose';
                }
                $actual = ($specialNumber % 2 === 0) ? 'even' : 'odd';
                return self::resolveResult($selection === $actual, $betType);

            case '合数单双':
            case '合數單雙':
            case '合數单双':
                if ($specialNumber === 49) {
                    return 'draw';
                }
                $selection = self::normalizeOddEvenSelection($betContent);
                if (!$selection) {
                    return 'lose';
                }
                $hesu = self::calculateHesuValue($specialNumber);
                $actual = ($hesu % 2 === 0) ? 'even' : 'odd';
                return self::resolveResult($selection === $actual, $betType);

            case '总和单双':
            case '總和單雙':
            case '總和单双':
                $selection = self::normalizeOddEvenSelection($betContent);
                if (!$selection) {
                    return 'lose';
                }
                $totalSum = array_sum($allNumbers);
                $actual = ($totalSum % 2 === 0) ? 'even' : 'odd';
                return self::resolveResult($selection === $actual, $betType);

            case '三肖':
            case '四肖':
            case '五肖':
            case '六肖':
                $userZodiacs = ZodiacService::normalizeZodiacSelections(explode(',', $betContent), $year);
                if (empty($userZodiacs)) {
                    return 'lose';
                }
                $result = ZodiacService::checkMultiZodiacWin($userZodiacs, $allNumbers, $year);
                if ($specialNumber === 49) {
                    return 'draw';
                }
                $hit = $result['is_win'];
                return self::resolveResult($hit, $betType);

            default:
                return 'lose';
        }
    }

    private static function resolveResult(bool $hit, string $betType): string
    {
        if ($betType === 'not_win') {
            return $hit ? 'lose' : 'win';
        }
        return $hit ? 'win' : 'lose';
    }

    private static function normalizeOddEvenSelection(string $betContent): ?string
    {
        $parts = preg_split('/[,\s，]+/u', $betContent);
        if ($parts === false || empty($parts)) {
            $parts = [$betContent];
        }

        $oddKeywords = ['单', '單', '奇', 'odd', 'dan'];
        $evenKeywords = ['双', '雙', '偶', 'even', 'shuang'];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (self::containsAnyKeyword($part, $oddKeywords)) {
                return 'odd';
            }
            if (self::containsAnyKeyword($part, $evenKeywords)) {
                return 'even';
            }
        }

        return null;
    }

    private static function containsAnyKeyword(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if ($keyword === '') {
                continue;
            }
            if (function_exists('mb_stripos')) {
                if (mb_stripos($text, $keyword) !== false) {
                    return true;
                }
            } elseif (stripos($text, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    private static function calculateHesuValue(int $number): int
    {
        $hesu = intdiv($number, 10) + ($number % 10);
        while ($hesu >= 10) {
            $hesu = intdiv($hesu, 10) + ($hesu % 10);
        }

        return $hesu;
    }

    /**
     * 解析数字连码规则：二中二=选2中2，三中二=选3中2，三中三=选3中3。
     */
    private static function getNumberComboRule(string $methodName, string $methodCode = ''): ?array
    {
        $codeMap = [
            'erzhonger' => [2, 2],
            'sanzhonger' => [3, 2],
            'sanzhongsan' => [3, 3],
            'sizhonger' => [4, 2],
            'sizhongsan' => [4, 3],
            'sizhongsi' => [4, 4],
        ];

        $methodCode = strtolower(trim($methodCode));
        if (isset($codeMap[$methodCode])) {
            return [
                'select_count' => $codeMap[$methodCode][0],
                'hit_count' => $codeMap[$methodCode][1],
            ];
        }

        $normalized = str_replace([' ', '　', '-', '_'], '', trim($methodName));
        $chineseNumberMap = [
            '一' => 1,
            '二' => 2,
            '三' => 3,
            '四' => 4,
            '五' => 5,
            '六' => 6,
            '七' => 7,
            '八' => 8,
            '九' => 9,
        ];

        if (preg_match('/([一二三四五六七八九])中([一二三四五六七八九])/u', $normalized, $matches)) {
            $selectCount = $chineseNumberMap[$matches[1]] ?? 0;
            $hitCount = $chineseNumberMap[$matches[2]] ?? 0;
        } elseif (preg_match('/([1-9])中([1-9])/u', $normalized, $matches)) {
            $selectCount = (int)$matches[1];
            $hitCount = (int)$matches[2];
        } else {
            return null;
        }

        if ($selectCount < 2 || $hitCount < 1 || $hitCount > $selectCount) {
            return null;
        }

        return [
            'select_count' => $selectCount,
            'hit_count' => $hitCount,
        ];
    }

    /**
     * 解析数字不中规则：五到十不中=选对应数量号码，全部7个开奖号码都没出现才中奖。
     */
    private static function getNumberMissRule(string $methodName, string $methodCode = ''): ?array
    {
        $codeMap = [
            'wubuzhong' => 5,
            'liubuzhong' => 6,
            'qibuzhong' => 7,
            'babuzhong' => 8,
            'jiubuzhong' => 9,
            'shibuzhong' => 10,
        ];

        $methodCode = strtolower(trim($methodCode));
        if (isset($codeMap[$methodCode])) {
            return [
                'select_count' => $codeMap[$methodCode],
                'hit_count' => 0,
                'judge_scope' => 'all7',
            ];
        }

        $nameMap = [
            '五不中' => 5,
            '六不中' => 6,
            '七不中' => 7,
            '八不中' => 8,
            '九不中' => 9,
            '十不中' => 10,
        ];

        $normalized = str_replace([' ', '　', '-', '_'], '', trim($methodName));
        foreach ($nameMap as $keyword => $selectCount) {
            if (!preg_match('/' . $keyword . '/u', $normalized)) {
                continue;
            }

            return [
                'select_count' => $selectCount,
                'hit_count' => 0,
                'judge_scope' => 'all7',
            ];
        }

        return null;
    }

    /**
     * 解析投注号码，兼容逗号、中文逗号、空格、短横线分隔。
     */
    private static function parseNumberSelections(string $betContent): array
    {
        $parts = preg_split('/[,\s，、;-]+/u', $betContent);
        if ($parts === false) {
            return [];
        }

        $numbers = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '' || !preg_match('/^\d{1,2}$/', $part)) {
                continue;
            }

            $number = (int)$part;
            if ($number >= 1 && $number <= 49) {
                $numbers[] = $number;
            }
        }

        return array_values(array_unique($numbers));
    }

    /**
     * 下单内容校验。普通号码玩法保持原行为，数字连码强制选够指定号码。
     */
    private static function validateBetContent(array $playMethod, string $betContent, int $index): void
    {
        $comboRule = self::getNumberComboRule((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        $missRule = self::getNumberMissRule((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        $rule = $missRule ?: $comboRule;
        if (!$rule) {
            return;
        }

        $numbers = self::parseNumberSelections($betContent);
        if (count($numbers) !== $rule['select_count']) {
            throw new \Exception(sprintf(
                '第%d注投注失败: %s玩法必须选择%d个不重复号码',
                $index + 1,
                $playMethod['name'],
                $rule['select_count']
            ));
        }
    }

    /**
     * 获取当前期号
     */
    public static function getCurrentIssue($gameId, $plateCode)
    {
        $plate = Db::name('lottery_plate')->where(['game_id' => $gameId, 'plate_code' => $plateCode, 'status' => 1])->find();
        if (!$plate) throw new Exception('盘口不存在');

        $issue = Db::name('lottery_issue')->where(['game_id' => $gameId, 'plate_id' => $plate['id'], 'status' => 2])->order('issue', 'desc')->find();
        if (!$issue) throw new Exception('暂无可投注期次');

        return [
            'issue' => $issue['issue'],
            'plate_code' => $plateCode,
            'plate_name' => $plate['plate_name'],
            'close_time' => $issue['close_time'],
            'draw_time' => $issue['draw_time'],
            'can_bet' => time() < $issue['close_time']
        ];
    }

    /**
     * 获取投注记录
     */
    public static function getBettingRecords($userId, $page = 1, $limit = 20)
    {
        $list = Db::name('betting_record')->where('user_id', $userId)->order('id', 'desc')->page($page, $limit)->select();
        $total = Db::name('betting_record')->where('user_id', $userId)->count();

        return ['list' => $list, 'total' => $total, 'page' => $page, 'limit' => $limit];
    }

    /**
     * 获取开奖结果
     */
    public static function getDrawResult($gameId, $plateCode, $issue = '')
    {
        $plate = Db::name('lottery_plate')->where(['game_id' => $gameId, 'plate_code' => $plateCode])->find();
        if (!$plate) throw new Exception('盘口不存在');

        $where = ['game_id' => $gameId, 'plate_id' => $plate['id'], 'status' => ['in', [4, 5]]];
        if ($issue) {
            $where['issue'] = $issue;
            return Db::name('lottery_issue')->where($where)->find();
        }

        return Db::name('lottery_issue')->where($where)->order('issue', 'desc')->limit(10)->select();
    }

    /**
     * 获取投注选项数据（号码、生肖、赔率）
     *
     * @param string $playName 玩法名称（特码、正码、特肖、三肖、四肖、五肖、六肖）
     * @param int $gid 游戏ID
     * @param int $year 年份
     * @return array|false
     */
    public static function getBetOptions($playName, $gid = 200, $year = 0, $plateCode = '')
    {
        try {
            // 年份默认值
            if (empty($year)) {
                $year = (int)date('Y');
            }

            // 查询玩法配置
            $playMethod = Db::table('la_play_method')
                ->where('game_id', $gid)
                ->where('name', $playName)
                ->where('is_enabled', 1)
                ->find();

            if (!$playMethod) {
                self::setError('玩法不存在');
                return false;
            }

            // TODO: 如果指定了盘口,可以在此处根据盘口调整赔率
            // 预留扩展:未来如果需要不同盘口有不同赔率,在此处实现
            // if (!empty($plateCode)) {
            //     $plateOdds = Db::table('la_plate_odds')
            //         ->where('plate_code', $plateCode)
            //         ->where('play_id', $playMethod['id'])
            //         ->find();
            //     if ($plateOdds) {
            //         $playMethod['odds_default'] = $plateOdds['odds'];
            //     }
            // }

            // 根据玩法类型返回不同的数据
            $playCode = $playMethod['code'];
            $missRule = self::getNumberMissRule($playName, $playCode);
            if ($missRule) {
                return self::getComboNumberOptions($playName, $playMethod, $year, $plateCode, $missRule, 'miss');
            }

            $comboRule = self::getNumberComboRule($playName, $playCode);

            if ($comboRule) {
                return self::getComboNumberOptions($playName, $playMethod, $year, $plateCode, $comboRule);
            }

            // 特码、正码、平码：返回1-49号码
            if (in_array($playCode, ['tema', 'zhengma', 'pingma'])) {
                return self::getNumberOptions($playName, $playMethod, $year, $plateCode);
            }

            // 特肖、一肖、二肖、三肖、四肖、五肖、六肖、七肖：返回12生肖
            if (in_array($playCode, ['texiao', 'pingxiao', 'yixiao', 'erxiao', 'sanxiao', 'sixiao', 'wuxiao', 'liuxiao', 'qixiao'])) {
                return self::getZodiacOptions($playName, $playMethod, $year, $plateCode);
            }

            self::setError('不支持的玩法类型');
            return false;

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 获取号码选项（1-49）
     */
    private static function getNumberOptions($playName, $playMethod, $year, $plateCode = '')
    {
        $options = [];
        $oddsDefault = (float)$playMethod['odds_default'];

        // 包本金赔率(用户只能投注'win'类型,统一使用此赔率)
        $oddsWin = number_format($oddsDefault, 4, '.', '');
        // ⚠️ 历史遗留字段:不中赔率(已废弃,用户不能投注'不中')
        // 保留此字段仅为兼容旧数据,实际业务不再使用
        $oddsNotWin = number_format($oddsDefault - 1, 4, '.', '');

        // 生成1-49的号码
        for ($i = 1; $i <= 49; $i++) {
            $number = str_pad($i, 2, '0', STR_PAD_LEFT);

            // 获取号码对应的生肖
            $zodiac = self::getZodiacByNumber($i, $year);

            $options[] = [
                'value' => $number,
                'label' => $number,
                'odds' => $oddsWin,              // 赔率（包本金,用户投注使用）
                'odds_win' => $oddsWin,          // 中赔率（包本金）
                'odds_not_win' => $oddsNotWin,   // 历史遗留字段(已废弃)
                'zodiac' => $zodiac,
            ];
        }

        return [
            'play_name' => $playName,
            'play_type' => 'number',
            'year' => $year,
            'plate_code' => $plateCode,          // 盘口代码
            'total_options' => 49,
            'odds' => $oddsWin,                  // 赔率（包本金,用户投注使用）
            'odds_win' => $oddsWin,              // 中赔率（包本金）
            'odds_not_win' => $oddsNotWin,       // 历史遗留字段(已废弃)
            'options' => $options,
        ];
    }

    /**
     * 获取数字连码选项（1-49，组合成一注）
     */
    private static function getComboNumberOptions($playName, $playMethod, $year, $plateCode, array $comboRule, string $mode = 'combo')
    {
        $result = self::getNumberOptions($playName, $playMethod, $year, $plateCode);
        $result['play_type'] = 'combo_number';
        $result['select_count'] = $comboRule['select_count'];
        $result['hit_count'] = $comboRule['hit_count'];
        $result['combo_mode'] = $mode;
        if ($mode === 'miss') {
            $result['miss_count'] = $comboRule['select_count'];
            $result['special_rules'] = [
                'judge_scope' => '按全部7个开奖号码判断',
                'win_rule' => '所选号码全部没有开出即中奖',
            ];
        } else {
            $result['special_rules'] = [
                'regular_only' => '只按前6个正码判奖，特码不参与命中计算',
            ];
        }

        return $result;
    }

    /**
     * 获取生肖选项（12生肖）
     */
    private static function getZodiacOptions($playName, $playMethod, $year, $plateCode = '')
    {
        $zodiacConfig = config('zodiac_base_year');
        $zodiacOrder = $zodiacConfig['zodiac_order'];
        $currentYearTable = ZodiacYearService::getZodiacTableByYear((int)$year);
        $currentYearZodiac = ZodiacYearService::getYearZodiac((int)$year);

        // 计算赔率
        $oddsDefault = (float)$playMethod['odds_default'];
        $oddsWin = number_format($oddsDefault, 4, '.', '');          // 赔率（包本金,用户投注使用）
        $oddsNotWin = number_format($oddsDefault - 1, 4, '.', '');   // 历史遗留字段(已废弃)
        $isPingXiao = self::isPingXiaoPlay((string)$playName, (string)($playMethod['code'] ?? ''));

        // 生成生肖选项
        $options = [];

        foreach ($zodiacOrder as $zodiac) {
            $numbers = $currentYearTable[$zodiac];
            $isCurrentYear = ($zodiac === $currentYearZodiac);

            // 判断生肖类别（家禽/野兽）
            $domesticZodiacs = ['牛', '马', '羊', '鸡', '狗', '猪'];
            $category = in_array($zodiac, $domesticZodiacs) ? 'domestic' : 'wild';

            $optionOdds = self::getOptionOdds($playMethod, $zodiac);
            $optionOddsWin = number_format($optionOdds, 4, '.', '');
            $optionOddsNotWin = number_format($optionOdds - 1, 4, '.', '');

            $options[] = [
                'value' => $zodiac,
                'label' => $zodiac,
                'odds' => $optionOddsWin,              // 赔率（包本金,用户投注使用）
                'odds_win' => $optionOddsWin,          // 中赔率（包本金）
                'odds_not_win' => $optionOddsNotWin,   // 历史遗留字段(已废弃)
                'numbers' => array_map(function($n) {
                    return str_pad($n, 2, '0', STR_PAD_LEFT);
                }, $numbers),
                'count' => count($numbers),
                'is_current_year' => $isCurrentYear,
                'category' => $category,
                'category_label' => $category === 'domestic' ? '家禽' : '野兽',
            ];
        }

        // 生成分类分组信息
        $categoryGroups = [
            [
                'type' => 'domestic',
                'label' => '家禽',
                'zodiacs' => ['牛', '马', '羊', '鸡', '狗', '猪'],
                'numbers' => [],
                'total_numbers' => 0,
            ],
            [
                'type' => 'wild',
                'label' => '野兽',
                'zodiacs' => ['鼠', '虎', '兔', '龙', '蛇', '猴'],
                'numbers' => [],
                'total_numbers' => 0,
            ],
        ];

        foreach ($categoryGroups as &$group) {
            $allNumbers = [];
            foreach ($group['zodiacs'] as $zodiac) {
                $allNumbers = array_merge($allNumbers, $currentYearTable[$zodiac]);
            }
            $allNumbers = array_unique($allNumbers);
            sort($allNumbers);
            $group['numbers'] = array_map(function($n) {
                return str_pad($n, 2, '0', STR_PAD_LEFT);
            }, $allNumbers);
            $group['total_numbers'] = count($allNumbers);
            $group['description'] = implode('、', $group['zodiacs']) . '(共' . $group['total_numbers'] . '个号码)';
        }

        return [
            'play_name' => $playName,
            'play_type' => 'zodiac',
            'year' => $year,
            'plate_code' => $plateCode,          // 盘口代码
            'year_zodiac' => $currentYearZodiac,
            'total_options' => 12,
            'odds' => $oddsWin,                  // 赔率（包本金,用户投注使用）
            'odds_win' => $oddsWin,              // 中赔率（包本金）
            'odds_not_win' => $oddsNotWin,       // 历史遗留字段(已废弃)
            'options' => $options,
            'category_groups' => $categoryGroups,
            // ⚠️ odds_types字段:历史遗留,用户只能投注'win'类型
            'odds_types' => [
                ['type' => 'normal', 'label' => '普通', 'odds' => $oddsWin],
                ['type' => 'win', 'label' => '中', 'odds' => $oddsWin],
                ['type' => 'not_win', 'label' => '不中(已废弃)', 'odds' => $oddsNotWin],
            ],
            'special_rules' => $isPingXiao
                ? [
                    'judge_scope' => '按全部7个开奖号码判断',
                    'win_rule' => '任意一个开奖号码对应所选生肖即中奖',
                ]
                : [
                    'rule_49' => '开出49号视为和局,投注金额退还',
                ],
        ];
    }

    private static function isPingXiaoPlay(string $methodName, string $methodCode = ''): bool
    {
        return strtolower(trim($methodCode)) === 'pingxiao'
            || self::containsAnyKeyword($methodName, ['平肖']);
    }

    private static function resolveBetOdds(array $playMethod, string $betContent): float
    {
        $odds = self::getOptionOdds($playMethod, $betContent);
        return $odds > 0 ? $odds : (float)($playMethod['odds_default'] ?? 0);
    }

    private static function getOptionOdds(array $playMethod, string $optionValue): float
    {
        $defaultOdds = (float)($playMethod['odds_default'] ?? 0);
        $config = self::decodePrizeConfig($playMethod['prize_config'] ?? null);
        $optionOdds = $config['option_odds'] ?? [];
        $normalizedOption = self::normalizeZodiacAlias($optionValue);

        foreach ($optionOdds as $option => $odds) {
            if (self::normalizeZodiacAlias((string)$option) === $normalizedOption) {
                return (float)$odds;
            }
        }

        if (isset($config['default_odds'])) {
            return (float)$config['default_odds'];
        }

        return $defaultOdds;
    }

    private static function decodePrizeConfig($raw): array
    {
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function normalizeZodiacAlias(string $value): string
    {
        return strtr(trim($value), [
            '馬' => '马',
            '雞' => '鸡',
            '龍' => '龙',
            '豬' => '猪',
        ]);
    }

    /**
     * 根据号码和年份获取生肖
     */
    private static function getZodiacByNumber($number, $year)
    {
        return ZodiacYearService::getZodiacByNumberAndYear((int)$number, (int)$year) ?? '';
    }

    /**
     * 错误信息
     */
    private static $error = '';

    public static function setError($msg)
    {
        self::$error = $msg;
    }

    public static function getError()
    {
        return self::$error;
    }

    /**
     * 批量投注下单（原子性操作：全部成功或全部失败）- 使用新表
     *
     * @param array $orders 订单数组
     * @return array|false
     */
    public static function placeBetBatch($orders)
    {
        if (empty($orders) || !is_array($orders)) {
            self::setError('订单数据不能为空');
            return false;
        }

        Db::startTrans();
        try {
            $results = [];
            $totalAmount = 0;
            $userId = 0;
            $gid = 0;
            $issue = '';
            $plateCode = 'A'; // 默认A盘

            // 第一步：预验证所有订单（快速失败）
            foreach ($orders as $index => $order) {
                // 提取公共参数
                if ($index === 0) {
                    $userId = $order['user_id'];
                    $gid = $order['gid'];
                    $issue = $order['qishu'];
                    $plateCode = $order['plate_code'] ?? 'A';
                }

                // 验证参数一致性
                if ($order['user_id'] != $userId || $order['gid'] != $gid || $order['qishu'] != $issue) {
                    throw new \Exception('第' . ($index + 1) . '注投注失败: 订单参数不一致');
                }
                if (($order['plate_code'] ?? 'A') !== $plateCode) {
                    throw new \Exception('第' . ($index + 1) . '注投注失败: 盘口参数不一致');
                }

                $totalAmount += $order['bet_amount'];
            }

            // 第二步：查询期次信息（新表 la_lottery_issue）
            $issueModel = Db::table('la_lottery_issue')
                ->where('game_id', $gid)
                ->where('issue', $issue)
                ->where('plate_code', $plateCode)
                ->lock(true)
                ->find();

            if (!$issueModel) {
                throw new \Exception('期号不存在');
            }

            // 验证是否可投注（状态 1=投注中）
            if ($issueModel['status'] != 1) {
                throw new \Exception('当前期次不可投注，状态：' . $issueModel['status']);
            }

            // 验证是否已封盘（时间检查）
            $now = time();
            if ($now >= $issueModel['close_time']) {
                throw new \Exception('当前期次已封盘，无法投注');
            }

            // 验证是否未开盘
            if ($now < $issueModel['open_time']) {
                throw new \Exception('当前期次未开盘，无法投注');
            }

            // 第三步：锁定用户账户并验证余额（新表 la_user_account）
            $account = Db::table('la_user_account')
                ->where('user_id', $userId)
                ->lock(true)
                ->find();

            if (!$account) {
                throw new \Exception('用户账户不存在');
            }

            if ($account['balance'] < $totalAmount) {
                throw new \Exception('余额不足，当前余额：' . $account['balance'] . '元，需要：' . $totalAmount . '元');
            }

            // 第四步：扣减余额并冻结（新表）
            $affectedRows = Db::table('la_user_account')
                ->where('user_id', $userId)
                ->where('balance', '>=', $totalAmount) // 乐观锁
                ->update([
                    'balance' => Db::raw('balance - ' . $totalAmount),
                    'frozen_amount' => Db::raw('frozen_amount + ' . $totalAmount),
                    'total_bet' => Db::raw('total_bet + ' . $totalAmount),
                    'version' => Db::raw('version + 1'),
                    'updated_at' => time(),
                ]);

            if ($affectedRows === 0) {
                throw new \Exception('余额扣减失败，请重试');
            }

            // 查询用户扩展信息（代理关系）
            $userExtend = Db::table('la_user_extend')->where('user_id', $userId)->find();

            // 第五步：逐笔写入投注记录（新表 la_betting_record）
            foreach ($orders as $index => $order) {
                // 查询玩法信息获取赔率（新表 la_play_method）
                $methodId = $order['pid'];

                $playMethod = Db::table('la_play_method')
                    ->where('id', $methodId)
                    ->where('game_id', $gid)
                    ->where('is_enabled', 1)
                    ->find();

                if (!$playMethod) {
                    throw new \Exception('第' . ($index + 1) . '注投注失败: 玩法不存在或已停用');
                }

                $odds = self::resolveBetOdds($playMethod, (string)$order['bet_content']);

                if ($odds <= 0) {
                    throw new \Exception('第' . ($index + 1) . '注投注失败: 赔率配置错误');
                }

                self::validateBetContent($playMethod, (string)$order['bet_content'], $index);

                // 生成投注单号
                $sn = OrderSnService::generateBetSn($userId);

                // 写入投注记录到 la_betting_record 表
                $bettingId = Db::table('la_betting_record')->insertGetId([
                    'sn' => $sn,
                    'user_id' => $userId,
                    'game_id' => $gid,
                    'plate_id' => $issueModel['plate_id'],
                    'plate_code' => $plateCode,
                    'issue_id' => $issueModel['id'],
                    'issue' => $issue,
                    'method_id' => $methodId,
                    'method_name' => $playMethod['name'],
                    'bet_type' => $order['bet_type'] ?? 'win', // 新增: 投注类型
                    'bet_content' => $order['bet_content'],
                    'bet_amount' => $order['bet_amount'],
                    'bet_multiple' => 1,
                    'total_amount' => $order['bet_amount'],
                    'odds' => $odds,
                    'status' => 0, // 0=待开奖
                    'prize_amount' => 0,
                    'is_settled' => 0,
                    'parent_id' => $userExtend['parent_id'] ?? 0,
                    'ancestor_ids' => $userExtend['ancestor_ids'] ?? '',
                    'ip' => $order['ip'],
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);

                if (!$bettingId) {
                    throw new \Exception('第' . ($index + 1) . '注投注失败: 写入数据库失败');
                }

                // 记录账户流水（新表 la_account_log）
                Db::table('la_account_log')->insert([
                    'sn' => OrderSnService::generateLogSn($userId),
                    'user_id' => $userId,
                    'change_type' => 3, // 3=投注
                    'change_amount' => -$order['bet_amount'],
                    'balance_before' => $account['balance'],
                    'balance_after' => $account['balance'] - $order['bet_amount'],
                    'frozen_before' => $account['frozen_amount'],
                    'frozen_after' => $account['frozen_amount'] + $order['bet_amount'],
                    'related_sn' => $sn,
                    'related_type' => 1, // 1=投注单
                    'remark' => "投注: {$playMethod['name']} {$order['bet_content']}",
                    'ip' => $order['ip'],
                    'created_at' => time(),
                ]);

                $results[] = [
                    'id' => $bettingId,
                    'sn' => $sn,
                    'bet_content' => $order['bet_content'],
                    'bet_amount' => number_format($order['bet_amount'], 2, '.', ''),
                    'odds' => number_format($odds, 4, '.', ''),
                ];
            }

            // 更新期次统计
            Db::table('la_lottery_issue')
                ->where('id', $issueModel['id'])
                ->update([
                    'total_bet_amount' => Db::raw('total_bet_amount + ' . $totalAmount),
                    'updated_at' => time(),
                ]);

            Db::commit();

            // 返回成功结果
            return [
                'success_count' => count($results),
                'total_amount' => number_format($totalAmount, 2, '.', ''),
                'balance' => number_format($account['balance'] - $totalAmount, 2, '.', ''),
                'frozen_amount' => number_format($account['frozen_amount'] + $totalAmount, 2, '.', ''),
                'results' => $results,
            ];

        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 查询投注记录（基于新表 la_betting_record）
     *
     * @param int $userId 新系统用户ID
     * @param array $params 查询参数
     * @return array
     */
    public static function getBetList($userId, $params = [])
    {
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 20;
        $issue = $params['qishu'] ?? '';
        $gameId = $params['gid'] ?? 0;
        $status = $params['z'] ?? '';
        $plateCode = $params['plate_code'] ?? '';
        $startDate = trim((string)($params['start_date'] ?? ''));
        $endDate = trim((string)($params['end_date'] ?? ''));

        // 构建查询条件
        $where = [];
        $where[] = ['b.user_id', '=', $userId];

        if (!empty($issue)) {
            $where[] = ['b.issue', '=', $issue];
        }

        if (!empty($gameId)) {
            $where[] = ['b.game_id', '=', $gameId];
        }

        if ($status !== '') {
            $where[] = ['b.status', '=', $status];
        }

        if (!empty($plateCode)) {
            $where[] = ['b.plate_code', '=', $plateCode];
        }

        if ($startDate !== '') {
            $startTimestamp = strtotime($startDate . ' 00:00:00');
            if ($startTimestamp !== false) {
                $where[] = ['b.created_at', '>=', $startTimestamp];
            }
        }

        if ($endDate !== '') {
            $endTimestamp = strtotime($endDate . ' 23:59:59');
            if ($endTimestamp !== false) {
                $where[] = ['b.created_at', '<=', $endTimestamp];
            }
        }

        // 查询投注记录（新表）
        $list = Db::table('la_betting_record')
            ->alias('b')
            ->leftJoin('la_play_method p', 'b.method_id = p.id')
            ->field('b.id, b.sn, b.issue, b.game_id, b.plate_code, b.method_id, b.method_name, b.bet_type, b.bet_content, b.bet_amount, b.total_amount, b.odds, b.status, b.prize_amount, (b.prize_amount - b.total_amount) as profit_amount, b.created_at, b.updated_at, p.name as play_name')
            ->where($where)
            ->order('b.id', 'desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        // 游戏名称映射
        $gameNames = [
            200 => '马来六合彩',
        ];

        // 格式化数据
        foreach ($list as &$item) {
            // 状态文本
            $statusMap = [
                0 => '未开奖',
                1 => '已中奖',
                2 => '未中奖',
                3 => '已撤单',
                4 => '和局',
            ];
            $item['status_text'] = $statusMap[$item['status']] ?? '未知';

            // ✅ 投注类型固定为'中'(用户只能投注win类型)
            $item['bet_type_text'] = '中';

            // 游戏名称
            $item['game_name'] = $gameNames[$item['game_id']] ?? '未知游戏';

            // 玩法显示名称(不再显示投注类型,因为统一为'中')
            $item['play_display'] = $item['method_name'] ?: ($item['play_name'] ?? '未知玩法');

            // 预期中奖金额
            $expectedPrize = $item['total_amount'] * $item['odds'];
            $item['expected_prize'] = number_format($expectedPrize, 2, '.', '');

            // 实际中奖金额
            if (in_array($item['status'], [1, 4])) {
                $item['prize'] = number_format($item['prize_amount'], 2, '.', '');
            } else {
                $item['prize'] = '0.00';
            }

            $item['profit_amount'] = number_format($item['profit_amount'], 2, '.', '');

            // 格式化金额
            $item['bet_amount'] = number_format($item['bet_amount'], 2, '.', '');
            $item['total_amount'] = number_format($item['total_amount'], 2, '.', '');
            $item['odds'] = number_format($item['odds'], 4, '.', '');

            // 格式化时间
            $item['time'] = date('Y-m-d H:i:s', $item['created_at']);

            // 投注内容
            $item['content'] = $item['bet_content'];
        }

        // 查询总数
        $total = Db::table('la_betting_record')
            ->alias('b')
            ->where($where)
            ->count();

        $summary = Db::table('la_betting_record')
            ->alias('b')
            ->where($where)
            ->field([
                'IFNULL(SUM(b.total_amount), 0) as total_amount',
                'IFNULL(SUM(b.prize_amount), 0) as total_prize_amount',
                'IFNULL(SUM(b.prize_amount - b.total_amount), 0) as total_profit_amount',
            ])
            ->find();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'summary' => [
                'total_amount' => number_format((float)($summary['total_amount'] ?? 0), 2, '.', ''),
                'total_prize_amount' => number_format((float)($summary['total_prize_amount'] ?? 0), 2, '.', ''),
                'total_profit_amount' => number_format((float)($summary['total_profit_amount'] ?? 0), 2, '.', ''),
            ],
        ];
    }

    /**
     * 查询开奖结果（公开接口）
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param string $plateCode 盘口代号(可选)
     * @return array|false
     */
    public static function getKjResult($gid, $qishu, $plateCode = '')
    {
        try {
            $query = Db::table('la_lottery_issue')
                ->where('game_id', (int)$gid)
                ->where('issue', (string)$qishu);

            if (!empty($plateCode)) {
                $query->where('plate_code', (string)$plateCode);
            }

            $issue = $query->find();
            if (empty($issue)) {
                self::setError('期号不存在');
                return false;
            }

            $drawTimeRaw = $issue['draw_time'] ?? 0;
            $drawTimeTs = 0;
            $kjTime = '';

            if (is_numeric($drawTimeRaw)) {
                $drawTimeTs = (int)$drawTimeRaw;
                if ($drawTimeTs > 0) {
                    $kjTime = date('Y-m-d H:i:s', $drawTimeTs);
                }
            } else {
                $kjTime = (string)$drawTimeRaw;
                $drawTimeTs = $kjTime ? (int)strtotime($kjTime) : 0;
            }

            $now = time();
            $isOpened = ((int)($issue['status'] ?? 0) === 3) && !empty($issue['result']);
            $canReveal = $isOpened || ($drawTimeTs > 0 ? ($now >= $drawTimeTs) : true);

            $numbers = [];
            // 安全策略：planned_result 仍不公开；只有已正式开奖(status=3)或到达 draw_time 才返回 result。
            if ($canReveal && !empty($issue['result'])) {
                $allNums = explode(',', (string)$issue['result']);
                $allNums = array_values(array_filter(array_map('trim', $allNums), 'strlen'));

                $rawNumbers = [];
                foreach ($allNums as $num) {
                    $numInt = (int)$num;
                    if ($numInt < 1 || $numInt > 49) {
                        continue;
                    }
                    $rawNumbers[] = $numInt;
                }

                // 只允许 7 码；数据异常(多码/少码/重复)一律按未开奖处理，避免前端展示异常
                if (count($rawNumbers) === 7 && count(array_unique($rawNumbers)) === 7) {
                    foreach ($rawNumbers as $numInt) {
                        $numbers[] = str_pad((string)$numInt, 2, '0', STR_PAD_LEFT);
                    }
                }
            }

            $hasResult = count($numbers) === 7;

            return [
                'qishu' => (string)$issue['issue'],
                'numbers' => $numbers,
                'kj_time' => $kjTime,
                'draw_time' => $kjTime,
                // status: 1=已开奖, 0=未开奖(兼容前端轮询判断)
                'status' => $hasResult ? 1 : 0,
                // 盘口信息(便于调试/兼容多盘)
                'plate_code' => $issue['plate_code'] ?? (string)$plateCode,
                // 附带倒计时，前端可选使用
                'seconds_to_kj' => $drawTimeTs > 0 ? max(0, $drawTimeTs - $now) : 0,
                // 原始期次状态
                'issue_status' => isset($issue['status']) ? (int)$issue['status'] : 0,
            ];
        } catch (\Throwable $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
