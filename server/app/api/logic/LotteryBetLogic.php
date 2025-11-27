<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 彩票投注逻辑层(基于 x_user 和 x_lib 表)
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-27
// +----------------------------------------------------------------------

namespace app\api\logic;

use think\facade\Db;
use think\Exception;
use app\common\service\BetLimitService;
use app\common\service\ZodiacYearService;

/**
 * 彩票投注逻辑
 * Class LotteryBetLogic
 * @package app\api\logic
 */
class LotteryBetLogic
{
    /**
     * 错误信息
     */
    private static $error = '';

    /**
     * 获取错误信息
     * @return string
     */
    public static function getError(): string
    {
        return self::$error;
    }

    /**
     * 设置错误信息
     * @param string $error
     */
    private static function setError(string $error): void
    {
        self::$error = $error;
    }


    /**
     * @notes 下单投注(完整事务流程,参考老系统makelib.php)
     * @param array $params 投注参数
     * @return array|false
     * @author Claude
     * @date 2025/11/27
     *
     * 参数说明:
     * @param int params.legacy_userid 老系统用户ID(从x_user表)
     * @param int params.gid 游戏ID(200=新澳门六合彩)
     * @param string params.qishu 期号(如2025112)
     * @param int params.pid 玩法ID(如21401=特码)
     * @param string params.bet_content 投注内容(如"08"表示号码08)
     * @param float params.bet_amount 投注金额
     * @param string params.ip 用户IP地址
     *
     * 流程(参考makelib.php):
     * 1. 验证用户状态(status=1, yingdeny=0)
     * 2. 检查余额(kmoney >= bet_amount)
     * 3. 开启事务
     * 4. 查询玩法配置(x_play表)
     * 5. 插入投注记录(x_lib表)
     * 6. 扣除余额(x_user.kmoney)
     * 7. 记录资金流水(x_user_money_log表)
     * 8. 提交事务
     *
     * 返回:
     * {
     *   "tid": "订单号",
     *   "balance": 剩余余额,
     *   "qishu": "期号",
     *   "prize": 预计中奖金额
     * }
     */
    public static function placeBet(array $params)
    {
        // 参数提取
        $legacyUserId = $params['legacy_userid'];
        $gid = $params['gid'];
        $qishu = $params['qishu'];
        $pid = $params['pid'];
        $betContent = $params['bet_content'];
        $betAmount = $params['bet_amount'];
        $ip = $params['ip'] ?? request()->ip();

        // 参数验证
        if (empty($legacyUserId) || empty($gid) || empty($qishu) || empty($pid)) {
            self::setError('参数缺失');
            return false;
        }

        if ($betAmount <= 0) {
            self::setError('投注金额必须大于0');
            return false;
        }

        // 金额必须为整数(参考makelib.php:208)
        if ($betAmount != floor($betAmount)) {
            self::setError('投注金额必须为整数');
            return false;
        }

        // ========== 新增: 投注限额验证 ==========
        $limitCheck = BetLimitService::validateBetAmount($betAmount);
        if (!$limitCheck['valid']) {
            self::setError($limitCheck['error']);
            return false;
        }

        try {
            // ========== 阶段1: 验证用户状态与余额 ==========
            // 参考makelib.php:112-175

            $user = Db::table('x_user')
                ->where('userid', $legacyUserId)
                ->field('kmoney,status,yingdeny,fudong,pan')
                ->lock(true)  // 行级锁,防止并发
                ->find();

            if (!$user) {
                self::setError('用户不存在');
                return false;
            }

            // 检查用户状态
            if ($user['status'] != 1) {
                self::setError('账户已被停用');
                return false;
            }

            // 检查是否禁止盈利
            if ($user['yingdeny'] == 1) {
                self::setError('账户暂不可用');
                return false;
            }

            // 检查余额
            if ($user['kmoney'] < $betAmount) {
                self::setError('余额不足');
                return false;
            }


            // ========== 阶段2: 查询玩法配置 ==========
            // 参考makelib.php:225-238

            $play = Db::table('x_play')
                ->where('gid', $gid)
                ->where('pid', $pid)
                ->field('bid,sid,cid,peilv1,peilv2,ifok,name,ztype,znum1,znum2')
                ->find();

            if (!$play) {
                self::setError('玩法不存在');
                return false;
            }

            // 检查玩法是否开放
            if ($play['ifok'] != 1) {
                self::setError('玩法已关闭');
                return false;
            }

            // 检查期号是否有效(当前期号)
            $currentQishu = self::getCurrentQishu($gid);
            if ($qishu != $currentQishu) {
                self::setError('期号已过期,当前期号:' . $currentQishu);
                return false;
            }

            // ========== 新增: 完整的开盘/封盘/开奖检查 ==========
            $periodCheck = self::checkPeriodStatus($gid, $qishu);
            if (!$periodCheck['can_bet']) {
                self::setError($periodCheck['message']);
                return false;
            }


            // ========== 阶段3: 开启事务 ==========
            // 参考makelib.php:204

            Db::startTrans();

            try {
                // ========== 新增: 风控预警检查 ==========
                // 查询用户本期已投注总额
                $periodTotalBet = Db::table('x_lib')
                    ->where('userid', $legacyUserId)
                    ->where('qishu', $qishu)
                    ->where('bs', 1)  // 有效投注
                    ->sum('je');

                $riskAlert = BetLimitService::checkRiskAlert($betAmount, $periodTotalBet);
                if ($riskAlert['alert']) {
                    // 记录风控日志(不阻止投注,仅记录)
                    \think\facade\Log::warning('投注风控预警', [
                        'userid' => $legacyUserId,
                        'qishu' => $qishu,
                        'bet_amount' => $betAmount,
                        'period_total' => $periodTotalBet,
                        'alert_type' => $riskAlert['type'],
                        'message' => $riskAlert['message'],
                    ]);
                }

                // ========== 阶段4: 生成订单号 ==========
                // 格式: YmdHis + 3位随机数
                $tid = date('YmdHis') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);


                // ========== 阶段5: 插入投注记录 ==========
                // 参考makelib.php:557-641

                $insertData = [
                    'tid' => $tid,
                    'userid' => $legacyUserId,
                    'dates' => date('Y-m-d'),  // 日期字段(判奖时从这里提取年份)
                    'qishu' => $qishu,
                    'gid' => $gid,
                    'bid' => $play['bid'],
                    'sid' => $play['sid'],
                    'cid' => $play['cid'],
                    'pid' => $pid,
                    'content' => $betContent,
                    'je' => $betAmount,
                    'peilv1' => $play['peilv1'],
                    'peilv2' => $play['peilv2'] ?? 0,
                    'xtype' => 0,  // 0=正常投注
                    'z' => 9,      // 9=未开奖
                    'bs' => 1,     // 倍数=1
                    'time' => date('Y-m-d H:i:s'),
                    'ip' => $ip,
                ];

                $insertResult = Db::table('x_lib')->insert($insertData);

                if (!$insertResult) {
                    throw new Exception('插入投注记录失败');
                }


                // ========== 阶段6: 扣除余额 ==========
                // 参考makelib.php:653-661

                $updateResult = Db::table('x_user')
                    ->where('userid', $legacyUserId)
                    ->dec('kmoney', $betAmount)
                    ->update();

                if (!$updateResult) {
                    throw new Exception('扣除余额失败');
                }

                // 计算扣款后余额
                $newBalance = $user['kmoney'] - $betAmount;


                // ========== 阶段7: 记录资金流水 ==========
                // 参考makelib.php:659行的usermoneylog函数

                $logData = [
                    'userid' => $legacyUserId,
                    'money' => -$betAmount,  // 负数表示扣除
                    'aftermoney' => $newBalance,
                    'remark' => '投注',
                    'status' => 1,  // 1=成功
                    'ip' => $ip,
                    'time' => date('Y-m-d H:i:s'),
                ];

                $logResult = Db::table('x_user_money_log')->insert($logData);

                if (!$logResult) {
                    throw new Exception('记录资金流水失败');
                }


                // ========== 阶段8: 提交事务 ==========
                Db::commit();


                // ========== 返回成功数据 ==========
                return [
                    'tid' => $tid,
                    'balance' => number_format($newBalance, 2, '.', ''),
                    'qishu' => $qishu,
                    'bet_amount' => number_format($betAmount, 2, '.', ''),
                    'bet_content' => $betContent,
                    'play_name' => $play['name'],
                    'peilv' => $play['peilv1'],
                    'expected_prize' => number_format($betAmount * $play['peilv1'], 2, '.', ''),  // 预计中奖金额
                ];

            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                self::setError('投注失败: ' . $e->getMessage());
                return false;
            }

        } catch (\Exception $e) {
            self::setError('系统错误: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * @notes 获取当前期号
     * @param int $gid 游戏ID
     * @return string
     * @author Claude
     * @date 2025/11/27
     */
    private static function getCurrentQishu(int $gid): string
    {
        // 查询x_game表获取当前期号
        $game = Db::table('x_game')
            ->where('gid', $gid)
            ->field('thisqishu')
            ->find();

        return $game['thisqishu'] ?? '';
    }


    /**
     * @notes 检查期号状态(开盘/封盘/开奖)
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @return array{can_bet: bool, message: string, countdown: int, period_info: array}
     * @author Claude
     * @date 2025/11/27
     *
     * 返回:
     * {
     *   "can_bet": 是否可以投注,
     *   "message": 提示信息,
     *   "countdown": 倒计时(秒),
     *   "period_info": 期号详细信息
     * }
     */
    private static function checkPeriodStatus(int $gid, string $qishu): array
    {
        // 查询期号信息(参考makelib.php:182-184, 731-742)
        $period = Db::table('x_kj')
            ->where('gid', $gid)
            ->where('qishu', $qishu)
            ->field('opentime, closetime, kjtime, m1, m2, m3, m4, m5, m6, m7, m8')
            ->find();

        if (!$period) {
            return [
                'can_bet' => false,
                'message' => '期号不存在',
                'countdown' => 0,
                'period_info' => [],
            ];
        }

        $now = time();
        $opentime = strtotime($period['opentime']);
        $closetime = strtotime($period['closetime']);
        $kjtime = strtotime($period['kjtime']);

        // 检查1: 是否已开奖
        // m1不为空表示已开奖
        if (!empty($period['m1'])) {
            return [
                'can_bet' => false,
                'message' => '该期已开奖',
                'countdown' => 0,
                'period_info' => $period,
            ];
        }

        // 检查2: 是否到开盘时间
        if ($now < $opentime) {
            return [
                'can_bet' => false,
                'message' => '未到开盘时间',
                'countdown' => $opentime - $now,
                'period_info' => $period,
            ];
        }

        // 检查3: 是否已封盘
        // 参考makelib.php:734-736, 提前封盘时间(默认5分钟=300秒)
        $advanceCloseSeconds = 300;  // 可以从配置读取

        $actualClosetime = $closetime - $advanceCloseSeconds;

        if ($now >= $actualClosetime) {
            return [
                'can_bet' => false,
                'message' => '已封盘',
                'countdown' => 0,
                'period_info' => $period,
            ];
        }

        // 检查4: 是否已过开奖时间(理论上不应该出现)
        if ($now >= $kjtime) {
            return [
                'can_bet' => false,
                'message' => '已过开奖时间',
                'countdown' => 0,
                'period_info' => $period,
            ];
        }

        // 可以投注
        return [
            'can_bet' => true,
            'message' => '可以投注',
            'countdown' => $actualClosetime - $now,
            'period_info' => $period,
        ];
    }


    /**
     * @notes 查询投注记录
     * @param int $legacyUserId 老系统用户ID
     * @param array $params 查询参数
     * @return array
     * @author Claude
     * @date 2025/11/27
     *
     * 参数说明:
     * @param int params.page 页码(默认1)
     * @param int params.limit 每页数量(默认20)
     * @param string params.qishu 期号(可选)
     * @param int params.gid 游戏ID(可选)
     * @param int params.z 中奖状态(可选: 9=未开奖, 1=中奖, 0=未中)
     *
     * 返回:
     * {
     *   "list": [...],
     *   "total": 总记录数,
     *   "page": 当前页,
     *   "limit": 每页数量
     * }
     */
    public static function getBetList(int $legacyUserId, array $params): array
    {
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 20;

        // 构建查询
        $query = Db::table('x_lib')
            ->where('userid', $legacyUserId)
            ->order('time', 'desc');

        // 筛选条件
        if (!empty($params['qishu'])) {
            $query->where('qishu', $params['qishu']);
        }

        if (!empty($params['gid'])) {
            $query->where('gid', $params['gid']);
        }

        if (isset($params['z']) && $params['z'] !== '') {
            $query->where('z', $params['z']);
        }

        // 查询总数
        $total = $query->count();

        // 分页查询
        $list = $query->page($page, $limit)
            ->field('tid,qishu,gid,content,je,peilv1,z,prize,time')
            ->select()
            ->toArray();

        // 关联查询玩法名称
        foreach ($list as &$item) {
            $play = Db::table('x_play')
                ->where('pid', $item['pid'] ?? 0)
                ->field('name')
                ->find();

            $item['play_name'] = $play['name'] ?? '';

            // 格式化中奖状态
            $item['status_text'] = self::getStatusText($item['z']);
        }

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }


    /**
     * @notes 获取中奖状态文本
     * @param int $z 中奖状态
     * @return string
     */
    private static function getStatusText(int $z): string
    {
        $statusMap = [
            9 => '未开奖',
            1 => '已中奖',
            0 => '未中奖',
        ];

        return $statusMap[$z] ?? '未知';
    }


    /**
     * @notes 查询开奖结果
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @return array|false
     * @author Claude
     * @date 2025/11/27
     */
    public static function getKjResult(int $gid, string $qishu)
    {
        $kj = Db::table('x_kj')
            ->where('gid', $gid)
            ->where('qishu', $qishu)
            ->find();

        if (!$kj) {
            self::setError('开奖记录不存在');
            return false;
        }

        // 解析开奖号码(kj1-kj8字段)
        $numbers = [];
        for ($i = 1; $i <= 8; $i++) {
            $key = 'kj' . $i;
            if (isset($kj[$key]) && $kj[$key] > 0) {
                $numbers[] = str_pad($kj[$key], 2, '0', STR_PAD_LEFT);
            }
        }

        return [
            'qishu' => $kj['qishu'],
            'numbers' => $numbers,
            'kj_time' => $kj['dates'] ?? '',
            'status' => count($numbers) > 0 ? 1 : 0,  // 1=已开奖, 0=未开奖
        ];
    }


    /**
     * @notes 获取可投注的号码序列(含赔率、生肖信息)
     * @param int $year 年份
     * @return array|false
     * @author Claude
     * @date 2025/11/27
     *
     * 返回数据结构:
     * {
     *   "year": 2025,
     *   "year_zodiac": "蛇",
     *   "numbers": [
     *     {
     *       "number": "01",
     *       "zodiac": "蛇",
     *       "is_current_year_zodiac": true,
     *       "odds": {"special_number": "47", "normal_number": "8"}
     *     },
     *     ...
     *   ],
     *   "zodiacs": {
     *     "鼠": {
     *       "numbers": ["06", "18", "30", "42"],
     *       "count": 4,
     *       "odds": {
     *         "special_zodiac": "12",
     *         "three_zodiac": "7",
     *         "four_zodiac": "5",
     *         "five_zodiac": "4",
     *         "six_zodiac": "3"
     *       }
     *     },
     *     ...
     *   },
     *   "summary": {
     *     "total_numbers": 49,
     *     "total_zodiacs": 12,
     *     "special_number_49_zodiac": "蛇"
     *   }
     * }
     */
    public static function getBetNumbers(int $year)
    {
        try {
            // 1. 获取当年生肖
            $yearZodiac = ZodiacYearService::getYearZodiac($year);

            // 2. 获取当年的生肖对应表
            $zodiacTable = ZodiacYearService::getZodiacTableByYear($year);
            $numberMap = ZodiacYearService::getNumberMapByYear($year);

            // 3. 查询赔率配置(从x_play表)
            // TODO: 这里需要根据实际的pid来查询,暂时使用固定赔率
            $odds = [
                'special_number_normal' => 42,  // 普通号码特码赔率
                'special_number_49' => 47,      // 49号特码赔率(客户确认)
                'normal_number' => 8,           // 平码赔率
                'special_zodiac' => 12,         // 特肖赔率
                'three_zodiac' => 7,            // 三肖赔率
                'four_zodiac' => 5,             // 四肖赔率
                'five_zodiac' => 4,             // 五肖赔率
                'six_zodiac' => 3,              // 六肖赔率
            ];

            // 4. 构建号码数据(01-49)
            $numbers = [];
            for ($i = 1; $i <= 49; $i++) {
                $numberStr = str_pad($i, 2, '0', STR_PAD_LEFT);
                $zodiac = $numberMap[$i] ?? '';
                $isCurrentYearZodiac = ($zodiac === $yearZodiac);

                // 特殊处理49号的赔率
                $specialNumberOdds = ($i === 49) ? $odds['special_number_49'] : $odds['special_number_normal'];

                $numbers[] = [
                    'number' => $numberStr,
                    'number_int' => $i,
                    'zodiac' => $zodiac,
                    'is_current_year_zodiac' => $isCurrentYearZodiac,
                    'odds' => [
                        'special_number' => (string)$specialNumberOdds,  // 特码赔率
                        'normal_number' => (string)$odds['normal_number'],  // 平码赔率
                    ],
                ];
            }

            // 5. 构建生肖数据
            $zodiacs = [];
            foreach ($zodiacTable as $zodiac => $zodiacNumbers) {
                $numbersFormatted = array_map(function($num) {
                    return str_pad($num, 2, '0', STR_PAD_LEFT);
                }, $zodiacNumbers);

                $zodiacs[$zodiac] = [
                    'name' => $zodiac,
                    'numbers' => $numbersFormatted,
                    'count' => count($zodiacNumbers),
                    'is_current_year' => ($zodiac === $yearZodiac),
                    'odds' => [
                        'special_zodiac' => (string)$odds['special_zodiac'],   // 特肖赔率
                        'three_zodiac' => (string)$odds['three_zodiac'],       // 三肖赔率
                        'four_zodiac' => (string)$odds['four_zodiac'],         // 四肖赔率
                        'five_zodiac' => (string)$odds['five_zodiac'],         // 五肖赔率
                        'six_zodiac' => (string)$odds['six_zodiac'],           // 六肖赔率
                    ],
                ];
            }

            // 6. 返回完整数据
            return [
                'year' => $year,
                'year_zodiac' => $yearZodiac,
                'year_zodiac_numbers' => array_map(function($num) {
                    return str_pad($num, 2, '0', STR_PAD_LEFT);
                }, $zodiacTable[$yearZodiac] ?? []),
                'numbers' => $numbers,
                'zodiacs' => $zodiacs,
                'summary' => [
                    'total_numbers' => 49,
                    'total_zodiacs' => 12,
                    'special_number_49_zodiac' => $numberMap[49] ?? '',
                    'special_number_49_odds' => (string)$odds['special_number_49'],
                ],
                'odds_config' => $odds,  // 返回完整赔率配置供前端参考
            ];

        } catch (\Exception $e) {
            self::setError('获取号码数据失败: ' . $e->getMessage());
            return false;
        }
    }
}
