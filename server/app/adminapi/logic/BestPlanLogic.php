<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 管理端最佳控盘计划逻辑（基于新表）
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-12-11
// +----------------------------------------------------------------------

namespace app\adminapi\logic;

use app\common\logic\BaseLogic;
use think\facade\Db;

/**
 * 管理端最佳控盘计划逻辑（基于新表 la_lottery_issue）
 * Class BestPlanLogic
 * @package app\adminapi\logic
 */
class BestPlanLogic extends BaseLogic
{
    /**
     * @notes 获取盘口列表
     * @param int $gid 游戏ID
     * @return array
     * @author Claude
     * @date 2025/12/11
     */
    public static function getPlateList(int $gid): array
    {
        // 使用新的盘口表 la_plate
        $plates = Db::table('la_plate')
            ->field('id, code, name')
            ->where('game_id', $gid)
            ->where('status', 1)  // 只返回启用的盘口
            ->whereNull('deleted_at')  // 排除软删除的记录
            ->order('id', 'asc')  // 按ID正序排列
            ->select()
            ->toArray();

        return $plates;
    }


    /**
     * @notes 获取当前期号信息（基于新表 la_lottery_issue）
     * @param int $gid 游戏ID
     * @param string $plateCode 盘口代码（默认A）
     * @return array|null
     * @author Claude
     * @date 2025/12/11
     *
     * 返回格式：
     * {
     *   "qishu": "2025334",          // 期号
     *   "opentime": "2025-12-01 06:00:00",
     *   "closetime": "2025-12-01 09:30:00",
     *   "kjtime": "2025-12-01 09:50:00",
     *   "is_opened": false           // 是否已开奖
     * }
     */
    public static function getCurrentQishu(int $gid, string $plateCode = 'A'): ?array
    {
        // 查询当前可投注的期号（status=2 表示投注中）
        $issue = Db::table('la_lottery_issue')
            ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
            ->where('game_id', $gid)
            ->where('plate_code', $plateCode)
            ->where('status', 2)  // 2=投注中
            ->order('draw_time', 'asc')
            ->find();

        // 如果没有投注中的期号，查询最新的待开盘期号（status=1）
        if (!$issue) {
            $issue = Db::table('la_lottery_issue')
                ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)
                ->where('status', 1)  // 1=待开盘
                ->order('draw_time', 'asc')
                ->find();
        }

        // 如果还是没有，查询最新的任意期号
        if (!$issue) {
            $issue = Db::table('la_lottery_issue')
                ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)
                ->order('id', 'desc')
                ->find();
        }

        if (!$issue) {
            return null;
        }

        // 检查result字段的值
        $resultValue = $issue['result'] ?? '';

        // 转换时间戳为日期时间格式
        // ✅ 始终包含所有字段，避免前端undefined
        $result = [
            'qishu' => $issue['issue'],
            'plate_code' => $issue['plate_code'],
            'opentime' => $issue['open_time'] ? date('Y-m-d H:i:s', $issue['open_time']) : '',
            'closetime' => $issue['close_time'] ? date('Y-m-d H:i:s', $issue['close_time']) : '',
            'kjtime' => $issue['draw_time'] ? date('Y-m-d H:i:s', $issue['draw_time']) : '',
            'status' => (int)$issue['status'],  // ✅ 状态字段
            'is_opened' => ($issue['status'] == 3 && !empty($resultValue)),  // status=3且result不为空才是已开奖
            'draw_numbers' => [],  // ✅ 默认空数组
            'draw_numbers_text' => '',  // ✅ 默认空字符串
        ];

        // 如果result字段有值，解析开奖号码
        if (!empty($resultValue) && is_string($resultValue)) {
            $result['draw_numbers'] = explode(',', $resultValue);
            $result['draw_numbers_text'] = $resultValue;
        }

        return $result;
    }


    /**
     * @notes 实时计算分析（不保存，基于新表）
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param string $plateCode 盘口代码
     * @param int|null $year 年份
     * @param float|null $targetRate 目标利润率
     * @param float $tolerance 误差范围
     * @return array|false
     * @author Claude
     * @date 2025/12/11
     */
    public static function calculateRealtime(
        int $gid,
        string $qishu,
        string $plateCode = 'A',
        ?int $year = null,
        ?float $targetRate = null,
        float $tolerance = 5.0,
        ?string $sortBy = null,
        ?int $limit = null,
        ?int $maxConsecutive = null
    ) {
        // ✅ 检查期号是否已经开奖
        $issue = Db::table('la_lottery_issue')
            ->field('status, result')
            ->where('game_id', $gid)
            ->where('issue', $qishu)
            ->where('plate_code', $plateCode)
            ->find();

        if ($issue) {
            // 如果status=3(已开奖)且result不为空，阻止计算
            if ($issue['status'] == 3 && !empty($issue['result'])) {
                self::setError('当前期号已经开奖，请开设新盘口');
                return false;
            }
        }

        // 调用 API 的 BestPlanLogic（因为计算逻辑是通用的）
        return \app\api\logic\BestPlanLogic::calculateRealtime($gid, $qishu, $plateCode, $year, $targetRate, $tolerance, $sortBy, $limit, $maxConsecutive);
    }


    /**
     * @notes 执行分析并保存结果
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param string $plateCode 盘口代码
     * @param int|null $year 年份
     * @return array|false
     * @author Claude
     * @date 2025/12/11
     */
    public static function analyze(int $gid, string $qishu, string $plateCode, ?int $year = null)
    {
        return \app\api\logic\BestPlanLogic::analyze($gid, $qishu, $plateCode, $year);
    }


    /**
     * @notes 根据目标利润率查找号码
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param string $plateCode 盘口代码
     * @param float $targetRate 目标利润率
     * @param float $tolerance 允许误差
     * @param int|null $year 年份
     * @return array|false
     * @author Claude
     * @date 2025/12/11
     */
    public static function findByTargetRate(int $gid, string $qishu, string $plateCode, float $targetRate, float $tolerance, ?int $year = null)
    {
        return \app\api\logic\BestPlanLogic::findByTargetRate($gid, $qishu, $plateCode, $targetRate, $tolerance, $year);
    }


    /**
     * @notes 获取历史分析记录列表
     * @param int $gid 游戏ID
     * @param int $page 页码
     * @param int $limit 每页数量
     * @return array
     * @author Claude
     * @date 2025/12/11
     */
    public static function getHistoryList(int $gid, int $page, int $limit): array
    {
        return \app\api\logic\BestPlanLogic::getHistoryList($gid, $page, $limit);
    }


    /**
     * @notes 获取历史分析详情
     * @param int $id 记录ID
     * @return array|false
     * @author Claude
     * @date 2025/12/11
     */
    public static function getDetail(int $id)
    {
        return \app\api\logic\BestPlanLogic::getDetail($id);
    }


    /**
     * @notes 获取用户历史下单记录
     * @param array $params 查询参数
     * @return array
     */
    public static function getOrderHistory(array $params): array
    {
        $gid = (int)($params['gid'] ?? 200);
        $page = max(1, (int)($params['page'] ?? 1));
        $limit = (int)($params['limit'] ?? 20);
        $limit = $limit > 0 ? min($limit, 100) : 20;
        $username = trim((string)($params['username'] ?? ''));
        $userType = trim((string)($params['user_type'] ?? ''));
        $plateCode = trim((string)($params['plate_code'] ?? ''));
        $issue = trim((string)($params['issue'] ?? ''));

        $lists = self::buildOrderHistoryQuery($gid, $username, $userType, $plateCode, $issue)
            ->field([
                'b.id',
                'b.sn',
                'b.user_id',
                'IFNULL(u.username, "") as username',
                'IFNULL(u.nickname, "") as nickname',
                'IFNULL(u.mobile, "") as mobile',
                'IFNULL(ue.is_agent, 0) as is_agent',
                'b.game_id',
                'b.plate_code',
                'b.issue',
                'b.method_id',
                'b.method_name',
                'b.bet_type',
                'b.bet_content',
                'b.bet_amount',
                'b.bet_multiple',
                'b.total_amount',
                'b.odds',
                'b.status',
                'b.prize_amount',
                'b.is_settled',
                'b.created_at',
                'b.updated_at',
            ])
            ->order('b.id', 'desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['is_agent'] = (int)($item['is_agent'] ?? 0);
            $item['user_type'] = $item['is_agent'] === 1 ? 'agent' : 'user';
            $item['user_type_text'] = $item['is_agent'] === 1 ? '代理用户' : '普通用户';
            $item['bet_amount'] = number_format((float)$item['bet_amount'], 2, '.', '');
            $item['total_amount'] = number_format((float)$item['total_amount'], 2, '.', '');
            $item['prize_amount'] = number_format((float)$item['prize_amount'], 2, '.', '');
            $item['created_time'] = !empty($item['created_at']) ? date('Y-m-d H:i:s', (int)$item['created_at']) : '';
            $item['status_text'] = self::getBetStatusText((int)($item['status'] ?? 0));
        }
        unset($item);

        $summary = self::buildOrderHistoryQuery($gid, $username, $userType, $plateCode, $issue)
            ->field([
                'COUNT(b.id) as order_count',
                'IFNULL(SUM(b.total_amount), 0) as total_amount',
                'IFNULL(SUM(b.prize_amount), 0) as total_prize_amount',
            ])
            ->find();

        return [
            'lists' => $lists,
            'count' => self::buildOrderHistoryQuery($gid, $username, $userType, $plateCode, $issue)->count('b.id'),
            'page_no' => $page,
            'page_size' => $limit,
            'summary' => [
                'order_count' => (int)($summary['order_count'] ?? 0),
                'total_amount' => number_format((float)($summary['total_amount'] ?? 0), 2, '.', ''),
                'total_prize_amount' => number_format((float)($summary['total_prize_amount'] ?? 0), 2, '.', ''),
            ],
        ];
    }

    /**
     * @notes 构造历史下单查询，列表/总数/汇总分别调用，避免 field/order/page 状态互相污染
     * @param int $gid 游戏ID
     * @param string $username 用户名/昵称
     * @param string $userType 用户类型
     * @param string $plateCode 盘口代码
     * @param string $issue 期号
     * @return \think\db\Query
     */
    private static function buildOrderHistoryQuery(
        int $gid,
        string $username,
        string $userType,
        string $plateCode,
        string $issue
    ) {
        $query = Db::table('la_betting_record')
            ->alias('b')
            ->leftJoin('la_user u', 'u.id = b.user_id')
            ->leftJoin('la_user_extend ue', 'ue.user_id = b.user_id')
            ->where('b.game_id', $gid);

        if ($username !== '') {
            $query->where(function ($query) use ($username) {
                $keyword = '%' . $username . '%';
                $query->where('u.username', 'like', $keyword)
                    ->whereOr('u.nickname', 'like', $keyword);
            });
        }

        if ($userType === 'agent') {
            $query->where('ue.is_agent', 1);
        } elseif ($userType === 'user') {
            $query->whereRaw('IFNULL(ue.is_agent, 0) = 0');
        }

        if ($plateCode !== '') {
            $query->where('b.plate_code', $plateCode);
        }

        if ($issue !== '') {
            $query->where('b.issue', 'like', '%' . $issue . '%');
        }

        return $query;
    }


    /**
     * @notes 执行开奖（使用最佳方案）
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param string $plateCode 盘口代码
     * @param array $bestNumbers 7个开奖号码 [m1,m2,m3,m4,m5,m6,m7]
     * @param int $year 年份
     * @return array|false
     * @author Claude
     * @date 2025/12/11
     */
    public static function executeDrawing(
        int $gid,
        string $qishu,
        string $plateCode,
        array $bestNumbers,
        int $year,
        int $operatorId = 0
    )
    {
        return \app\api\logic\BestPlanLogic::executeDrawing($gid, $qishu, $plateCode, $bestNumbers, $year, $operatorId);
    }

    /**
     * @notes 自定义开奖号码并立即开奖结算
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param string $plateCode 盘口代码
     * @param array $drawNumbers 7个开奖号码 [m1,m2,m3,m4,m5,m6,m7]
     * @param int $year 年份
     * @param int $operatorId 操作员ID
     * @return array|false
     */
    public static function customDrawing(
        int $gid,
        string $qishu,
        string $plateCode,
        array $drawNumbers,
        int $year,
        int $operatorId = 0
    )
    {
        return \app\api\logic\BestPlanLogic::customDrawing($gid, $qishu, $plateCode, $drawNumbers, $year, $operatorId);
    }


    /**
     * @notes 预览手动创建的新期号
     * @param int $gid 游戏ID
     * @param string $plateCode 盘口代码
     * @param string $strategy 创建策略
     * @return array|false
     */
    public static function previewNewIssue(int $gid, string $plateCode = 'A', string $strategy = 'plate_config')
    {
        try {
            $currentIssue = self::getLatestIssue($gid, $plateCode);
            if (!self::canCreateNextIssue($currentIssue)) {
                self::setError('无法创建新期数，必须当前期号开奖完成后才可以启动新盘口');
                return false;
            }

            $newIssue = \app\common\service\LotteryIssueService::previewNextIssue($gid, $plateCode, $strategy);

            if (!$newIssue) {
                self::setError('预览新期号失败，请稍后重试');
                return false;
            }

            return self::formatNewIssueResult($newIssue, $currentIssue);
        } catch (\Exception $e) {
            self::setError('预览失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @notes 手动创建新期号
     * @param int $gid 游戏ID
     * @param string $plateCode 盘口代码
     * @param string $strategy 创建策略
     * @return array|false
     * @author Claude
     * @date 2025/12/12
     */
    public static function createNewIssue(int $gid, string $plateCode = 'A', string $strategy = 'plate_config')
    {
        try {
            $currentIssue = self::getLatestIssue($gid, $plateCode);
            if (!self::canCreateNextIssue($currentIssue)) {
                self::setError('无法创建新期数，必须当前期号开奖完成后才可以启动新盘口');
                return false;
            }

            // 调用LotteryIssueService创建新期号
            $newIssue = \app\common\service\LotteryIssueService::getOrCreateCurrentIssue($gid, $plateCode, $strategy);

            if (!$newIssue) {
                self::setError('创建新期号失败,请稍后重试');
                return false;
            }

            return self::formatNewIssueResult($newIssue, $currentIssue);
        } catch (\Exception $e) {
            self::setError('创建失败: ' . $e->getMessage());
            return false;
        }
    }

    public static function clearTodayData(int $gid, string $plateCode = 'A', int $operatorId = 0)
    {
        $plateCode = trim($plateCode) ?: 'A';
        $today = date('Ymd');
        $dayStart = strtotime(date('Y-m-d 00:00:00'));
        $dayEnd = strtotime(date('Y-m-d 23:59:59'));

        Db::startTrans();
        try {
            $issues = Db::table('la_lottery_issue')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)
                ->where('issue', 'like', $today . '%')
                ->lock(true)
                ->select()
                ->toArray();

            $issueIds = array_values(array_unique(array_map('intval', array_column($issues, 'id'))));
            $issueNos = array_values(array_unique(array_map('strval', array_column($issues, 'issue'))));

            $betQuery = Db::table('la_betting_record')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)
                ->where(function ($query) use ($today, $dayStart, $dayEnd) {
                    $query->where('issue', 'like', $today . '%')
                        ->whereOr(function ($subQuery) use ($dayStart, $dayEnd) {
                            $subQuery->where('created_at', '>=', $dayStart)
                                ->where('created_at', '<=', $dayEnd);
                        });
                })
                ->lock(true);

            $bets = $betQuery->select()->toArray();
            $betIds = array_values(array_unique(array_map('intval', array_column($bets, 'id'))));
            $betSns = array_values(array_unique(array_filter(array_map('strval', array_column($bets, 'sn')))));

            $userSummaries = [];
            foreach ($bets as $bet) {
                $userId = (int)$bet['user_id'];
                if ($userId <= 0) {
                    continue;
                }

                if (!isset($userSummaries[$userId])) {
                    $userSummaries[$userId] = [
                        'balance_delta' => 0.0,
                        'frozen_delta' => 0.0,
                        'total_bet_delta' => 0.0,
                        'total_prize_delta' => 0.0,
                    ];
                }

                $totalAmount = (float)$bet['total_amount'];
                $prizeAmount = (float)($bet['prize_amount'] ?? 0);
                $status = (int)$bet['status'];

                $userSummaries[$userId]['total_bet_delta'] += $totalAmount;
                if ($status === 0) {
                    $userSummaries[$userId]['balance_delta'] += $totalAmount;
                    $userSummaries[$userId]['frozen_delta'] += $totalAmount;
                } elseif ($status === 1) {
                    $userSummaries[$userId]['balance_delta'] += $totalAmount - $prizeAmount;
                    $userSummaries[$userId]['total_prize_delta'] += $prizeAmount;
                } elseif ($status === 2) {
                    $userSummaries[$userId]['balance_delta'] += $totalAmount;
                }
            }

            foreach ($userSummaries as $userId => $summary) {
                Db::table('la_user_account')->where('user_id', $userId)->lock(true)->find();
                Db::table('la_user_account')
                    ->where('user_id', $userId)
                    ->update([
                        'balance' => Db::raw('balance + ' . self::formatDecimal($summary['balance_delta'])),
                        'frozen_amount' => Db::raw('GREATEST(frozen_amount - ' . self::formatDecimal($summary['frozen_delta']) . ', 0)'),
                        'total_bet' => Db::raw('GREATEST(total_bet - ' . self::formatDecimal($summary['total_bet_delta']) . ', 0)'),
                        'total_prize' => Db::raw('GREATEST(total_prize - ' . self::formatDecimal($summary['total_prize_delta']) . ', 0)'),
                        'version' => Db::raw('version + 1'),
                        'updated_at' => time(),
                    ]);
            }

            $commissionCount = 0;
            if (!empty($betIds)) {
                $commissions = Db::table('la_agent_commission')
                    ->whereIn('betting_id', $betIds)
                    ->lock(true)
                    ->select()
                    ->toArray();

                $commissionSummaries = [];
                foreach ($commissions as $commission) {
                    $agentId = (int)$commission['user_id'];
                    if ($agentId <= 0) {
                        continue;
                    }
                    if (!isset($commissionSummaries[$agentId])) {
                        $commissionSummaries[$agentId] = 0.0;
                    }
                    $commissionSummaries[$agentId] += (float)$commission['commission_amount'];
                }

                foreach ($commissionSummaries as $agentId => $amount) {
                    Db::table('la_user_account')->where('user_id', $agentId)->lock(true)->find();
                    Db::table('la_user_account')
                        ->where('user_id', $agentId)
                        ->update([
                            'balance' => Db::raw('balance - ' . self::formatDecimal($amount)),
                            'total_commission' => Db::raw('GREATEST(total_commission - ' . self::formatDecimal($amount) . ', 0)'),
                            'version' => Db::raw('version + 1'),
                            'updated_at' => time(),
                        ]);
                }

                $commissionCount = Db::table('la_agent_commission')
                    ->whereIn('betting_id', $betIds)
                    ->delete();
            }

            $winningCount = 0;
            if (!empty($betIds)) {
                $winningCount = Db::table('la_winning_record')
                    ->whereIn('betting_id', $betIds)
                    ->delete();
            }

            $accountLogCount = 0;
            if (!empty($betSns)) {
                $accountLogCount = Db::table('la_account_log')
                    ->whereIn('related_sn', $betSns)
                    ->delete();
            }

            $bettingCount = 0;
            if (!empty($betIds)) {
                $bettingCount = Db::table('la_betting_record')
                    ->whereIn('id', $betIds)
                    ->delete();
            }

            $historyCount = 0;
            if (!empty($issueNos)) {
                $historyCount = Db::table('la_best_plan_history')
                    ->where('gid', $gid)
                    ->where('plate_code', $plateCode)
                    ->whereIn('qishu', $issueNos)
                    ->delete();
            }

            $issueCount = 0;
            if (!empty($issueIds)) {
                $issueCount = Db::table('la_lottery_issue')
                    ->whereIn('id', $issueIds)
                    ->delete();
            }

            Db::table('la_operation_log')->insert([
                'admin_id' => $operatorId,
                'admin_name' => '',
                'account' => '',
                'action' => '清空今日期数',
                'type' => 'POST',
                'url' => request()->url(true),
                'params' => json_encode([
                    'gid' => $gid,
                    'plate_code' => $plateCode,
                    'date' => $today,
                ], JSON_UNESCAPED_UNICODE),
                'result' => json_encode([
                    'issue_count' => $issueCount,
                    'betting_count' => $bettingCount,
                    'winning_count' => $winningCount,
                    'account_log_count' => $accountLogCount,
                    'commission_count' => $commissionCount,
                    'history_count' => $historyCount,
                    'affected_users' => count($userSummaries),
                ], JSON_UNESCAPED_UNICODE),
                'ip' => request()->ip(),
                'create_time' => time(),
            ]);

            Db::commit();

            return [
                'date' => $today,
                'plate_code' => $plateCode,
                'issue_count' => $issueCount,
                'betting_count' => $bettingCount,
                'winning_count' => $winningCount,
                'account_log_count' => $accountLogCount,
                'commission_count' => $commissionCount,
                'history_count' => $historyCount,
                'affected_users' => count($userSummaries),
            ];
        } catch (\Throwable $e) {
            Db::rollback();
            self::setError('清空失败: ' . $e->getMessage());
            return false;
        }
    }

    private static function formatDecimal(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    private static function getLatestIssue(int $gid, string $plateCode): ?array
    {
        $issue = Db::table('la_lottery_issue')
            ->field('issue, status, result, open_time, close_time, draw_time')
            ->where('game_id', $gid)
            ->where('plate_code', $plateCode)
            ->order('id', 'desc')
            ->find();

        return $issue ?: null;
    }

    private static function canCreateNextIssue(?array $currentIssue): bool
    {
        if (!$currentIssue) {
            return true;
        }

        return (int)$currentIssue['status'] === 3 && !empty($currentIssue['result']);
    }

    private static function formatNewIssueResult(array $newIssue, ?array $currentIssue = null): array
    {
        $status = (int)($newIssue['status'] ?? 0);

        $result = [
            'issue' => $newIssue['issue'],
            'open_time' => date('Y-m-d H:i:s', (int)$newIssue['open_time']),
            'close_time' => date('Y-m-d H:i:s', (int)$newIssue['close_time']),
            'draw_time' => date('Y-m-d H:i:s', (int)$newIssue['draw_time']),
            'status' => $status,
            'status_text' => self::getIssueStatusText($status),
            'strategy' => $newIssue['strategy'] ?? 'plate_config',
            'strategy_text' => self::getCreationStrategyText($newIssue['strategy'] ?? 'plate_config'),
            'source_text' => $newIssue['source_text'] ?? '',
        ];

        if ($currentIssue) {
            $result['current_issue'] = [
                'issue' => $currentIssue['issue'],
                'open_time' => date('Y-m-d H:i:s', (int)$currentIssue['open_time']),
                'close_time' => date('Y-m-d H:i:s', (int)$currentIssue['close_time']),
                'draw_time' => date('Y-m-d H:i:s', (int)$currentIssue['draw_time']),
                'status' => (int)$currentIssue['status'],
                'status_text' => self::getIssueStatusText((int)$currentIssue['status']),
            ];
        }

        return $result;
    }


    /**
     * @notes 获取期号状态文本
     * @param int $status 状态值
     * @return string 状态文本
     * @author Claude
     * @date 2025/12/12
     */
    private static function getIssueStatusText(int $status): string
    {
        $statusMap = [
            0 => '待开盘',
            1 => '投注中',
            2 => '已封盘',
            3 => '已开奖',
            4 => '已结算',
            5 => '已取消',
        ];

        return $statusMap[$status] ?? '未知';
    }

    private static function getBetStatusText(int $status): string
    {
        $statusMap = [
            0 => '待开奖',
            1 => '已中奖',
            2 => '未中奖',
            3 => '已撤单',
        ];

        return $statusMap[$status] ?? '未知';
    }

    private static function getCreationStrategyText(string $strategy): string
    {
        $strategyMap = [
            'plate_config' => '按盘口配置时间',
            'immediate' => '立即开盘',
            'continuous' => '按上一期连续创建',
        ];

        return $strategyMap[$strategy] ?? $strategyMap['plate_config'];
    }
}
