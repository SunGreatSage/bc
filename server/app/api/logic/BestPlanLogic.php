<?php
declare(strict_types=1);

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\service\BestPlanService;
use app\common\service\ZodiacService;
use app\common\service\ZodiacYearService;
use think\facade\Db;

/**
 * 最佳控盘计划 - 业务逻辑类
 *
 * @package app\api\logic
 * @author Claude AI
 * @date 2025-12-01
 */
class BestPlanLogic extends BaseLogic
{
    /**
     * 执行分析并保存结果
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param string $plateCode 盘口代码
     * @param int|null $year 年份（可选，默认当前年份）
     * @return array|false
     */
    public static function analyze(int $gid, string $qishu, string $plateCode, ?int $year = null)
    {
        try {
            $year = $year ?? (int)date('Y');

            // 创建计算服务 - 使用优化版算法(统一"中"与"不中"投注)
            $service = new \app\common\service\OptimizedBestPlanService($gid, $qishu, $year, $plateCode);

            // 如果没有投注数据,生成随机号码并显示100%利润
            if ($service->getBetCount() === 0) {
                // 生成随机的7个不重复号码(1-49)
                $randomNumbers = range(1, 49);
                shuffle($randomNumbers);
                $selectedNumbers = array_slice($randomNumbers, 0, 7);
                sort($selectedNumbers);

                $m1_m6 = array_slice($selectedNumbers, 0, 6);
                $m7 = $selectedNumbers[6];

                $bestSolution = [
                    'm1_m6' => $m1_m6,
                    'm7' => $m7,
                    'total_profit' => 0,
                    'profit_rate' => 100.0,  // 没有投注,利润率100%
                    'bet_amount' => 0,
                    'prize_amount' => 0,
                ];

                $summary = [
                    'total_bets' => 0,
                    'total_orders' => 0,
                    'best_numbers' => $selectedNumbers,
                    'best_m7' => $m7,
                    'best_m1_m6' => $m1_m6,
                    'best_profit' => 0,
                    'best_profit_rate' => 100.0,
                    'has_bets' => false,  // 标记：无投注数据
                ];

                // 构造号码详情
                $numberDetails = [];
                foreach ($selectedNumbers as $number) {
                    $numberDetails[] = [
                        'number' => $number,
                        'profit' => 0,
                        'profit_rate' => 100.0,
                        'prize_amount' => 0,
                        'bet_count' => 0,
                        'risk_level' => 0,
                    ];
                }

                // 准备保存数据
                $data = [
                    'gid' => $gid,
                    'qishu' => $qishu,
                    'plate_code' => $plateCode,
                    'analyze_time' => date('Y-m-d H:i:s'),
                    'total_bets' => 0,
                    'total_orders' => 0,
                    'best_numbers' => implode(',', $selectedNumbers),
                    'best_profit' => 0,
                    'best_profit_rate' => 100.0,
                    'worst_number' => 0,
                    'worst_profit' => 0,
                    'worst_profit_rate' => 0,
                    'avg_profit' => 0,
                    'number_details' => json_encode($numberDetails, JSON_UNESCAPED_UNICODE),
                    'status' => 0,
                ];

                // 检查是否已存在
                $exists = Db::table('la_best_plan_history')
                    ->where('gid', $gid)
                    ->where('qishu', $qishu)
                    ->where('plate_code', $plateCode)
                    ->find();

                if ($exists) {
                    Db::table('la_best_plan_history')
                        ->where('id', $exists['id'])
                        ->update($data);
                } else {
                    Db::table('la_best_plan_history')->insert($data);
                }

                return [
                    'summary' => $summary,
                    'best_solution' => $bestSolution,
                    'top_solutions' => [$bestSolution],
                    'message' => '该期暂无投注数据,已生成随机开奖号码',
                ];
            }

            // 获取最佳7个号码组合 - 使用优化版方法
            $result = $service->findBest7Numbers();
            $bestSolution = $result['best_solution'];

            // 获取摘要
            $summary = [
                'total_bets' => $result['total_bets'],
                'total_orders' => $result['total_orders'],
                'best_numbers' => $bestSolution ? array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]) : [],
                'best_m7' => $bestSolution['m7'] ?? 0,
                'best_m1_m6' => $bestSolution['m1_m6'] ?? [],
                'best_profit' => $bestSolution['total_profit'] ?? 0,
                'best_profit_rate' => $bestSolution['profit_rate'] ?? 0,
            ];

            // 构造简化的 number_details（仅包含最佳方案的号码）
            $numberDetails = [];
            if ($bestSolution) {
                $allNumbers = array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]);
                foreach ($allNumbers as $number) {
                    $numberDetails[] = [
                        'number' => $number,
                        'profit' => $bestSolution['total_profit'] / 7,  // 平均利润
                        'profit_rate' => $bestSolution['profit_rate'],
                        'prize_amount' => 0,  // 占位符
                        'bet_count' => 0,  // 占位符
                        'risk_level' => 0,  // 安全
                    ];
                }
            }

            // 准备保存数据
            $data = [
                'gid' => $gid,
                'qishu' => $qishu,
                'plate_code' => $plateCode,  // 新增：盘口代码
                'analyze_time' => date('Y-m-d H:i:s'),
                'total_bets' => $summary['total_bets'],
                'total_orders' => $summary['total_orders'],
                'best_numbers' => implode(',', $summary['best_numbers'] ?? []),  // 修正：字段名改为复数形式
                'best_profit' => $summary['best_profit'],
                'best_profit_rate' => $summary['best_profit_rate'],
                'worst_number' => 0,  // 新算法不计算worst
                'worst_profit' => 0,
                'worst_profit_rate' => 0,
                'avg_profit' => 0,
                'number_details' => json_encode($numberDetails, JSON_UNESCAPED_UNICODE),  // 保存号码详情
                'status' => 0,  // 未开奖
            ];

            // 检查是否已存在（使用新表 la_best_plan_history）
            $exists = Db::table('la_best_plan_history')
                ->where('gid', $gid)
                ->where('qishu', $qishu)
                ->where('plate_code', $plateCode)  // 新增：盘口维度
                ->find();

            if ($exists) {
                // 更新现有记录
                Db::table('la_best_plan_history')
                    ->where('id', $exists['id'])
                    ->update($data);
            } else {
                // 插入新记录
                Db::table('la_best_plan_history')->insert($data);
            }

            return [
                'summary' => $summary,
                'best_solution' => $bestSolution,
                'top_solutions' => $result['top_solutions'],
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 实时计算（不保存到数据库）
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param string $plateCode 盘口代码（如A、B、C）
     * @param int|null $year 年份
     * @param float|null $targetRate 目标利润率（如10表示10%，null表示最大化利润）
     * @param float $tolerance 误差范围（默认5%）
     * @return array|false
     */
    public static function calculateRealtime(
        int $gid,
        string $qishu,
        string $plateCode = 'A',
        ?int $year = null,
        ?float $targetRate = null,
        float $tolerance = 5.0
    ) {
        try {
            $year = $year ?? (int)date('Y');

            // 使用优化版算法(统一"中"与"不中"投注)
            $service = new \app\common\service\OptimizedBestPlanService($gid, $qishu, $year, $plateCode);

            // 如果没有投注数据,生成随机号码并显示100%利润
            if ($service->getBetCount() === 0) {
                // 生成随机的7个不重复号码(1-49)
                $randomNumbers = range(1, 49);
                shuffle($randomNumbers);
                $selectedNumbers = array_slice($randomNumbers, 0, 7);
                sort($selectedNumbers);

                $m1_m6 = array_slice($selectedNumbers, 0, 6);
                $m7 = $selectedNumbers[6];

                $bestSolution = [
                    'm1_m6' => $m1_m6,
                    'm7' => $m7,
                    'total_profit' => 0,
                    'profit_rate' => 100.0,  // 没有投注,利润率100%
                    'bet_amount' => 0,
                    'prize_amount' => 0,
                ];

                return [
                    'summary' => [
                        'total_bets' => 0,
                        'total_orders' => 0,
                        'best_numbers' => $selectedNumbers,
                        'best_m7' => $m7,
                        'best_m1_m6' => $m1_m6,
                        'best_profit' => 0,
                        'best_profit_rate' => 100.0,
                        'has_bets' => false,  // 标记：无投注数据
                    ],
                    'best_solution' => $bestSolution,
                    'top_solutions' => [$bestSolution],
                    'risk_assessment' => [
                        'risk_level' => 'safe',
                        'description' => '该期无投注数据,无风险',
                    ],
                    'recommendations' => ['该期暂无投注数据,已生成随机号码供开奖使用'],
                    'strategy_used' => 'random',
                    'message' => '该期暂无投注数据,已生成随机开奖号码',
                ];
            }

            // ✅ 获取最佳7个号码组合
            $result = $service->findBest7Numbers();

            // ✅ 如果指定了目标利润率,尝试找到符合条件的号码组合
            if ($targetRate !== null && !empty($result['top_solutions'])) {
                trace("🎯 目标利润率: {$targetRate}%, 误差: ±{$tolerance}%", 'info');
                trace("📊 生成方案数量: " . count($result['top_solutions']), 'info');

                $targetMin = $targetRate - $tolerance;
                $targetMax = $targetRate + $tolerance;

                // ✨ 优化: top_solutions已按利润率升序排列,优先查找范围内的方案
                $matchedSolution = null;
                $closestSolution = null;
                $closestDiff = 999999;

                foreach ($result['top_solutions'] as $solution) {
                    $profitRate = $solution['profit_rate'];
                    $diff = abs($profitRate - $targetRate);

                    // 记录最接近的方案
                    if ($diff < $closestDiff) {
                        $closestDiff = $diff;
                        $closestSolution = $solution;
                    }

                    // 检查是否在目标范围内
                    if ($profitRate >= $targetMin && $profitRate <= $targetMax) {
                        $matchedSolution = $solution;
                        trace("✅ 找到符合目标利润率的方案: {$profitRate}% (目标: {$targetMin}%-{$targetMax}%, 策略: {$solution['strategy']})", 'info');
                        break;
                    }
                }

                // 如果找到符合条件的方案,使用它
                if ($matchedSolution) {
                    $bestSolution = $matchedSolution;
                    trace("✅ 使用符合目标利润率的方案", 'info');
                } else {
                    // 🔍 输出方案范围统计,帮助调试
                    $profitRates = array_column($result['top_solutions'], 'profit_rate');
                    $minRate = min($profitRates);
                    $maxRate = max($profitRates);
                    trace("📈 方案利润率范围: {$minRate}% ~ {$maxRate}%", 'info');

                    // ⚠️ 没有找到符合条件的方案
                    trace("⚠️ 未找到完全符合的方案 (目标: {$targetMin}%-{$targetMax}%)", 'warning');

                    // 🎯 智能选择策略:
                    // 规则1: 如果目标利润率 > 0,必须优先选择盈利方案(利润率 ≥ 0)
                    // 规则2: 如果没有盈利方案,则使用最接近目标的方案(可能为负)
                    if ($targetRate > 0) {
                        // 查找所有盈利方案
                        $profitableSolutions = array_filter($result['top_solutions'], function($s) {
                            return $s['profit_rate'] >= 0;
                        });

                        if (!empty($profitableSolutions)) {
                            // 从盈利方案中选择最接近目标的
                            $closestProfitable = null;
                            $minDiff = 999999;
                            foreach ($profitableSolutions as $solution) {
                                $diff = abs($solution['profit_rate'] - $targetRate);
                                if ($diff < $minDiff) {
                                    $minDiff = $diff;
                                    $closestProfitable = $solution;
                                }
                            }
                            $bestSolution = $closestProfitable;
                            trace("💡 目标利润率>0,强制选择盈利方案: {$bestSolution['profit_rate']}% (差距: {$minDiff}%)", 'info');
                        } else {
                            // 没有任何盈利方案,使用亏损最小的方案
                            $bestSolution = end($result['top_solutions']); // 数组最后一个(利润率最高)
                            trace("⚠️ 无盈利方案!使用亏损最小方案: {$bestSolution['profit_rate']}%", 'warning');
                        }
                    } else {
                        // 目标利润率 ≤ 0,使用最接近的方案(可能为负)
                        $bestSolution = $closestSolution ?: $result['best_solution'];
                        trace("💡 使用最接近的方案: {$bestSolution['profit_rate']}% (差距: {$closestDiff}%)", 'info');
                    }

                    // 给出优化建议
                    if (abs($bestSolution['profit_rate'] - $targetRate) > 20) {
                        trace("💡 建议: 当前投注数据无法实现目标利润率,建议:", 'info');
                        trace("   1. 调整目标利润率到 {$minRate}%-{$maxRate}% 范围内", 'info');
                        trace("   2. 或增加更多投注数据以扩大可选方案范围", 'info');
                    }
                }
            } else {
                // 没有指定目标利润率,使用最大利润方案(数组最后一个元素)
                $bestSolution = $result['best_solution'];
            }

            // 构建摘要（使用计算结果中的数据）
            $summary = [
                'total_bets' => $result['total_bets'],
                'total_orders' => $result['total_orders'],
                'best_numbers' => $bestSolution ? array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]) : [],
                'best_m7' => $bestSolution['m7'] ?? 0,
                'best_m1_m6' => $bestSolution['m1_m6'] ?? [],
                'best_profit' => $bestSolution['total_profit'] ?? 0,
                'best_profit_rate' => $bestSolution['profit_rate'] ?? 0,
            ];

            return [
                'summary' => $summary,
                'best_solution' => $bestSolution,
                'top_solutions' => $result['top_solutions'],
                'risk_assessment' => $result['risk_assessment'] ?? null,
                'recommendations' => $result['recommendations'] ?? [],
                'strategy_used' => $targetRate !== null ? 'target_rate' : 'balanced',  // 标记使用的策略
                'target_rate_config' => $targetRate !== null ? [
                    'target' => $targetRate,
                    'tolerance' => $tolerance,
                    'achieved' => $bestSolution['profit_rate'] ?? 0,
                    'matched' => isset($matchedSolution),
                ] : null,
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 根据目标利润率查找号码
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param string $plateCode 盘口代码（如A、B、C）
     * @param float $targetRate 目标利润率
     * @param float $tolerance 允许误差
     * @param int|null $year 年份
     * @return array|false
     */
    public static function findByTargetRate(
        int $gid,
        string $qishu,
        string $plateCode,
        float $targetRate,
        float $tolerance = 1.0,
        ?int $year = null
    ) {
        try {
            $year = $year ?? (int)date('Y');

            // 使用增强版算法
            $service = new \app\common\service\EnhancedBestPlanService($gid, $qishu, $year, $plateCode);

            if ($service->getBetCount() === 0) {
                self::setError('该期暂无投注数据');
                return false;
            }

            // 使用增强版方法查找接近目标利润率的方案
            $result = $service->findBest7NumbersEnhanced($targetRate, $tolerance, 'balanced');

            return [
                'target_rate' => $targetRate,
                'tolerance' => $tolerance,
                'best_solution' => $result['best_solution'],
                'top_solutions' => $result['top_solutions'],
                'risk_assessment' => $result['risk_assessment'] ?? null,
                'recommendations' => $result['recommendations'] ?? [],
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 获取分析历史列表
     *
     * @param int $gid 游戏ID
     * @param int $limit 返回条数
     * @return array
     */
    public static function getHistoryList(int $gid, int $limit = 10): array
    {
        return Db::table('la_best_plan_history')
            ->field('id, gid, qishu, plate_code, analyze_time, total_bets, total_orders,
                     best_numbers, best_profit, best_profit_rate,
                     worst_number, worst_profit, worst_profit_rate,
                     avg_profit, status, actual_number, actual_profit')
            ->where('gid', $gid)
            ->order('analyze_time', 'desc')
            ->limit($limit)
            ->select()
            ->toArray();
    }

    /**
     * 获取分析详情
     *
     * @param int $id 记录ID
     * @return array|null
     */
    public static function getDetail(int $id): ?array
    {
        $record = Db::table('la_best_plan_history')
            ->where('id', $id)
            ->find();

        if (!$record) {
            return null;
        }

        // 解析JSON字段
        $record['number_details'] = json_decode($record['number_details'], true);

        // 确保是数组
        if (!is_array($record['number_details'])) {
            $record['number_details'] = [];
        }

        // 按利润排序（仅当数组非空且包含profit字段）
        if (!empty($record['number_details'])) {
            usort($record['number_details'], function($a, $b) {
                $profitA = $a['profit'] ?? 0;
                $profitB = $b['profit'] ?? 0;
                return $profitB <=> $profitA;
            });
        }

        // 添加风险等级文本
        foreach ($record['number_details'] as &$item) {
            $item['risk_level_text'] = BestPlanService::getRiskLevelText($item['risk_level'] ?? 0);
        }

        return $record;
    }

    /**
     * 获取当前可分析的期号
     *
     * @param int $gid 游戏ID
     * @param string $plateCode 盘口代码（如：A、B、C）
     * @return array|null
     */
    public static function getCurrentQishu(int $gid, string $plateCode = 'am'): ?array
    {
        trace("🔍 [getCurrentQishu] 查询参数: gid=$gid, plateCode=$plateCode", 'info');

        // 优先查询投注中的期号 (status=2)
        $issue = Db::table('la_lottery_issue')
            ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
            ->where('game_id', $gid)
            ->where('plate_code', $plateCode)  // ✅ 添加盘口筛选
            ->where('status', 2)  // 2=投注中
            ->order('draw_time', 'asc')
            ->find();

        // 如果没有投注中的期号，查询待开盘的期号（status=1）
        if (!$issue) {
            $issue = Db::table('la_lottery_issue')
                ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)  // ✅ 添加盘口筛选
                ->where('status', 1)  // 1=待开盘
                ->order('draw_time', 'asc')
                ->find();
        }

        // 如果还没有，查询最新的已开奖期号（status=3）- 用于显示开奖结果
        if (!$issue) {
            $issue = Db::table('la_lottery_issue')
                ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)  // ✅ 添加盘口筛选
                ->where('status', 3)  // 3=已开奖
                ->order('draw_time', 'desc')  // 降序，取最新的
                ->find();
        }

        if (!$issue) {
            trace("⚠️ [getCurrentQishu] 没有找到期号: gid=$gid, plateCode=$plateCode", 'warning');
            return null;
        }

        // 调试日志
        trace("📋 查询到期号数据: " . json_encode($issue, JSON_UNESCAPED_UNICODE), 'info');

        // 检查result字段的实际值
        $resultValue = $issue['result'] ?? null;
        trace("🔍 result字段值: [" . var_export($resultValue, true) . "] 类型: " . gettype($resultValue), 'info');

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
            trace("🎰 开奖号码原始数据: " . $resultValue, 'info');
            $result['draw_numbers'] = explode(',', $resultValue);
            $result['draw_numbers_text'] = $resultValue;
        } else {
            trace("⚠️ result字段为空或不是字符串, 返回空数组", 'warning');
        }

        trace("✅ 最终返回数据: " . json_encode($result, JSON_UNESCAPED_UNICODE), 'info');

        return $result;
    }

    /**
     * 更新实际开奖结果
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param int $actualNumber 实际开出的特码
     * @return bool
     */
    public static function updateActualResult(int $gid, string $qishu, int $actualNumber): bool
    {
        try {
            // 查找记录（使用新表 la_best_plan_history）
            $record = Db::table('la_best_plan_history')
                ->where('gid', $gid)
                ->where('qishu', $qishu)
                ->find();

            if (!$record) {
                self::setError('分析记录不存在');
                return false;
            }

            // 从JSON中解析该号码的预测利润
            $details = json_decode($record['number_details'], true);
            $actualProfit = 0;

            foreach ($details as $item) {
                if ($item['number'] == $actualNumber) {
                    $actualProfit = $item['profit'];
                    break;
                }
            }

            // 更新记录
            Db::table('la_best_plan_history')
                ->where('id', $record['id'])
                ->update([
                    'status' => 1,  // 已开奖
                    'actual_number' => $actualNumber,
                    'actual_profit' => $actualProfit,
                ]);

            return true;

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 获取投注汇总统计（按玩法分类）
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @return array
     */
        public static function getBetSummaryByPlay(int $gid, string $qishu): array
    {
        $summary = Db::table('la_betting_record')
            ->alias('br')
            ->leftJoin('la_play_method pm', 'br.method_id = pm.id')
            ->field('pm.name as play_name, COUNT(*) as bet_count, SUM(br.total_amount) as total_amount')
            ->where('br.game_id', $gid)
            ->where('br.issue', $qishu)
            ->where('br.status', 0)
            ->group('br.method_id')
            ->order('total_amount', 'desc')
            ->select()
            ->toArray();

        return $summary;
    }

    /**
     * ??????????????
     */
    public static function getNumberBetDistribution(int $gid, string $qishu): array
    {
        $distribution = Db::table('la_betting_record')
            ->alias('br')
            ->leftJoin('la_play_method pm', 'br.method_id = pm.id')
            ->field('br.bet_content as number, COUNT(*) as bet_count, SUM(br.total_amount) as total_amount')
            ->where('br.game_id', $gid)
            ->where('br.issue', $qishu)
            ->where('br.status', 0)
            ->where('br.status', '<>', 3)
            ->whereRaw("(pm.name LIKE '%??%' OR pm.name LIKE '%??%')")
            ->group('br.bet_content')
            ->order('total_amount', 'desc')
            ->select()
            ->toArray();

        return $distribution;
    }

    /**
     * ????????????
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
        try {
            Db::startTrans();

            $issue = Db::table('la_lottery_issue')
                ->where('game_id', $gid)
                ->where('issue', $qishu)
                ->where('plate_code', $plateCode)
                ->lock(true)
                ->find();

            if (!$issue) {
                self::setError('期号不存在');
                return false;
            }

            $closeTimeRaw = $issue['close_time'] ?? 0;
            $closeTimeTs = is_numeric($closeTimeRaw) ? (int)$closeTimeRaw : (int)strtotime((string)$closeTimeRaw);
            if ($closeTimeTs > 0 && time() < $closeTimeTs) {
                self::setError('未到封盘时间，不能提交计划');
                return false;
            }

            if (!empty($issue['result'])) {
                self::setError('本期已开奖，不能重复提交计划');
                return false;
            }

            if (!empty($issue['is_settled'])) {
                self::setError('本期已结算，不能重复提交计划');
                return false;
            }

            $numbers = array_values(array_map('intval', $bestNumbers));
            if (count($numbers) !== 7) {
                self::setError('必须提交7个开奖号码');
                return false;
            }
            foreach ($numbers as $num) {
                if ($num < 1 || $num > 49) {
                    self::setError('号码范围必须在1-49之间');
                    return false;
                }
            }

            $m1_m6 = array_slice($numbers, 0, 6);
            $m7 = $numbers[6];

            Db::table('la_lottery_issue')
                ->where('id', $issue['id'])
                ->update([
                    'planned_result' => implode(',', $numbers),
                    'planned_at' => time(),
                    'planned_source' => 1,
                    'planned_operator_id' => max(0, (int)$operatorId),
                    'updated_at' => time(),
                ]);

            $orders = Db::table('la_betting_record')
                ->where('game_id', $gid)
                ->where('issue', $qishu)
                ->where('plate_code', $plateCode)
                ->where('status', 0)
                ->select()
                ->toArray();

            $winCount = 0;
            $loseCount = 0;
            $drawCount = 0;
            $totalWinAmount = 0.0;
            $totalBetAmount = 0.0;

            foreach ($orders as $order) {
                $totalBetAmount += (float)$order['total_amount'];

                $resultType = self::checkWin($order, $m1_m6, $m7, $year);
                $isWin = $resultType === 'win';
                $isDraw = $resultType === 'draw';
                $winAmount = $isWin ? $order['total_amount'] * $order['odds'] : ($isDraw ? $order['total_amount'] : 0);

                if ($isWin) {
                    $winCount++;
                    $totalWinAmount += $winAmount;
                } elseif ($isDraw) {
                    $drawCount++;
                    $totalWinAmount += (float)$order['total_amount'];
                } else {
                    $loseCount++;
                }
            }

            Db::commit();

            return [
                'issue' => $qishu,
                'plate_code' => $plateCode,
                'numbers' => $numbers,
                'win_count' => $winCount,
                'lose_count' => $loseCount,
                'draw_count' => $drawCount,
                'total_orders' => count($orders),
                'total_bet_amount' => round($totalBetAmount, 2),
                'total_payout' => round($totalWinAmount, 2),
                'platform_profit' => round($totalBetAmount - $totalWinAmount, 2),
                'planned_at' => time(),
            ];

        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    private static function checkWin(array $order, array $m1_m6, int $m7, int $year): string
    {
        static $methodCache = [];

        $methodId = (int)($order['method_id'] ?? 0);
        if ($methodId > 0) {
            if (!isset($methodCache[$methodId])) {
                $methodCache[$methodId] = Db::table('la_play_method')
                    ->where('id', $methodId)
                    ->find();
            }
            $playMethod = $methodCache[$methodId];
        } else {
            $playMethod = null;
        }

        $methodName = $playMethod['name'] ?? ($order['method_name'] ?? '');
        $methodCode = strtolower((string)($playMethod['code'] ?? ''));
        $betContent = (string)($order['bet_content'] ?? '');
        $betItems = array_values(array_filter(array_map('trim', explode(',', $betContent)), 'strlen'));
        $betType = $order['bet_type'] ?? 'win';

        $allNumbers = array_merge($m1_m6, [$m7]);
        $numberMap = ZodiacYearService::getNumberMapByYear($year);
        $specialZodiac = $numberMap[$m7] ?? '';

        if ($methodCode === 'tema' || self::containsKeyword($methodName, ['??', '??'])) {
            $betNumbers = array_map('intval', $betItems);
            $hit = in_array($m7, $betNumbers, true);
            return self::resolveResult($hit, $betType);
        }

        if (
            $methodCode === 'zhengma'
            || self::containsKeyword($methodName, ['??', '??', '??', '??'])
        ) {
            $betNumbers = array_map('intval', $betItems);
            $hit = !empty(array_intersect($betNumbers, $allNumbers));
            return self::resolveResult($hit, $betType);
        }

        if ($methodCode === 'texiao' || self::containsKeyword($methodName, ['??'])) {
            if ($m7 == 49) {
                return 'draw';
            }
            $betZodiacs = ZodiacService::normalizeZodiacSelections($betItems, $year);
            if (empty($betZodiacs)) {
                return 'lose';
            }
            $hit = $specialZodiac !== '' && in_array($specialZodiac, $betZodiacs, true);
            return self::resolveResult($hit, $betType);
        }

        if (
            in_array($methodCode, ['sanxiao', 'sixiao', 'wuxiao', 'liuxiao'], true)
            || self::containsKeyword($methodName, ['??', '??', '??', '??'])
        ) {
            if ($m7 == 49) {
                return 'draw';
            }
            $betZodiacs = ZodiacService::normalizeZodiacSelections($betItems, $year);
            if (empty($betZodiacs)) {
                return 'lose';
            }
            $result = ZodiacService::checkMultiZodiacWin($betZodiacs, $allNumbers, $year);
            $hit = $result['is_win'];
            return self::resolveResult($hit, $betType);
        }

        return 'lose';
    }

    private static function containsKeyword(string $haystack, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if ($keyword === '') {
                continue;
            }
            if (function_exists('mb_stripos')) {
                if (mb_stripos($haystack, $keyword) !== false) {
                    return true;
                }
            } elseif (stripos($haystack, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    private static function resolveResult(bool $hit, string $betType): string
    {
        if ($betType === 'not_win') {
            return $hit ? 'lose' : 'win';
        }
        return $hit ? 'win' : 'lose';
    }

    /**
     * 手动创建新期号
     *
     * @param int $gid 游戏ID
     * @param string $plateCode 盘口代码
     * @return array|false 返回新期号信息或false
     */
    public static function manualCreateNewIssue(int $gid, string $plateCode = 'am')
    {
        try {
            // 调用LotteryIssueService创建新期号
            $newIssue = \app\common\service\LotteryIssueService::getOrCreateCurrentIssue($gid, $plateCode);

            if (!$newIssue) {
                self::$error = '创建新期号失败,请稍后重试';
                trace("❌ 手动创建新期号失败: gid=$gid, plateCode=$plateCode", 'error');
                return false;
            }

            trace("✅ 手动创建新期号成功: " . json_encode($newIssue, JSON_UNESCAPED_UNICODE), 'info');

            return [
                'issue' => $newIssue['issue'],
                'open_time' => $newIssue['open_time'],
                'close_time' => $newIssue['close_time'],
                'draw_time' => $newIssue['draw_time'],
                'status' => $newIssue['status'] ?? 0,
                'status_text' => self::getIssueStatusText($newIssue['status'] ?? 0),
            ];
        } catch (\Exception $e) {
            self::$error = '创建失败: ' . $e->getMessage();
            trace("❌ 手动创建新期号异常: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * 获取期号状态文本
     *
     * @param int $status 状态值
     * @return string 状态文本
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
}
