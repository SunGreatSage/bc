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
    private const MAX_TOP_SOLUTION_LIMIT = 1000;

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

            // 如果没有投注数据,生成至少20个随机方案
            if ($service->getBetCount() === 0) {
                // 生成20个随机方案供选择
                $randomSolutions = self::generateRandomSolutions(20);

                if (empty($randomSolutions)) {
                    // 如果生成失败,返回单个随机方案
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
                        'profit_rate' => 100.0,
                        'bet_amount' => 0,
                        'prize_amount' => 0,
                    ];

                    $randomSolutions = [$bestSolution];
                } else {
                    $bestSolution = $randomSolutions[0];
                }

                $selectedNumbers = array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]);
                $summary = [
                    'total_bets' => 0,
                    'total_orders' => 0,
                    'best_numbers' => $selectedNumbers,
                    'best_m7' => $bestSolution['m7'],
                    'best_m1_m6' => $bestSolution['m1_m6'],
                    'best_profit' => 0,
                    'best_profit_rate' => 100.0,
                    'has_bets' => false,  // 标记：无投注数据
                ];

                // 构造号码详情（使用最佳方案的号码）
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

                $topSolutions = $result['top_solutions'];
            if ($sortBy !== null || $limit !== null) {
                $sortKey = self::normalizeSortBy($sortBy);
                $solutions = $result['all_solutions'] ?? $topSolutions;
                $solutions = self::sortSolutions($solutions, $sortKey);
                $sliceLimit = $limit ?? count($topSolutions);
                if ($sliceLimit > self::MAX_TOP_SOLUTION_LIMIT) {
                    $sliceLimit = self::MAX_TOP_SOLUTION_LIMIT;
                }
                if ($sliceLimit > 0) {
                    $solutions = array_slice($solutions, 0, $sliceLimit);
                }
                $topSolutions = $solutions;
            }

            return [
                    'summary' => $summary,
                    'best_solution' => $bestSolution,
                    'top_solutions' => $randomSolutions,  // ✅ 返回20个方案
                    'message' => '该期暂无投注数据,已生成' . count($randomSolutions) . '个随机开奖方案',
                ];
            }

            // 获取最佳7个号码组合 - 使用优化版方法
            $result = $service->findBest7Numbers(null, 5.0, true);
            $rateBuckets = self::buildRateBuckets($result['all_solutions'] ?? $result['top_solutions'], $year);
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
                'top_solutions' => $topSolutions,
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
        float $tolerance = 5.0,
        ?string $sortBy = null,
        ?int $limit = null
    ) {
        try {
            $year = $year ?? (int)date('Y');
            $sortBy = is_string($sortBy) ? trim($sortBy) : null;
            if ($sortBy === '') {
                $sortBy = null;
            }
            $limit = self::normalizeSolutionLimit($limit);

            // 使用优化版算法(统一"中"与"不中"投注)
            $service = new \app\common\service\OptimizedBestPlanService($gid, $qishu, $year, $plateCode);

            // 如果没有投注数据,生成至少20个随机方案
            if ($service->getBetCount() === 0) {
                // 生成20个随机方案供选择
                $randomLimit = $limit ?? 20;
                if ($randomLimit > self::MAX_TOP_SOLUTION_LIMIT) {
                    $randomLimit = self::MAX_TOP_SOLUTION_LIMIT;
                }
                $randomSolutions = self::generateRandomSolutions($randomLimit);

                if (empty($randomSolutions)) {
                    // 如果生成失败,返回单个随机方案
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
                        'profit_rate' => 100.0,
                        'bet_amount' => 0,
                        'prize_amount' => 0,
                    ];

                    $randomSolutions = [$bestSolution];
                    $selectedNumbers = array_merge($m1_m6, [$m7]);
                } else {
                    // 使用第一个方案作为最佳方案
                    $bestSolution = $randomSolutions[0];
                    $selectedNumbers = array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]);
                }

                $sortKey = self::normalizeSortBy($sortBy);
                $randomSolutions = self::sortSolutions($randomSolutions, $sortKey);
                $bestSolution = $randomSolutions[0];
                $selectedNumbers = array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]);

                // ✅ 构建利润率档位
                $rateBuckets = self::buildRateBuckets($randomSolutions, $year);

                return [
                    'summary' => [
                        'total_bets' => 0,
                        'total_orders' => 0,
                        'best_numbers' => $selectedNumbers,
                        'best_m7' => $bestSolution['m7'],
                        'best_m1_m6' => $bestSolution['m1_m6'],
                        'best_profit' => 0,
                        'best_profit_rate' => 100.0,
                        'has_bets' => false,  // 标记：无投注数据
                    ],
                    'best_solution' => $bestSolution,
                    'top_solutions' => $randomSolutions,  // ✅ 返回20个方案
                    'rate_buckets' => $rateBuckets,  // ✅ 返回档位数据
                    'risk_assessment' => [
                        'risk_level' => 'safe',
                        'description' => '该期无投注数据,无风险',
                    ],
                    'recommendations' => ['该期暂无投注数据,已生成' . count($randomSolutions) . '个随机号码供开奖选择'],
                    'strategy_used' => 'random',
                    'message' => '该期暂无投注数据,已生成随机开奖方案',
                ];
            }

            // ✅ 获取最佳7个号码组合
            $result = $service->findBest7Numbers(null, 5.0, true);
            $rateBuckets = self::buildRateBuckets($result['all_solutions'] ?? $result['top_solutions'], $year);

            // ✅ 如果指定了目标利润率,使用智能扩展搜索
            $searchResult = null;
            if ($targetRate !== null && !empty($result['top_solutions'])) {
                trace("🎯 目标利润率: {$targetRate}%, 初始误差: ±{$tolerance}%", 'info');
                trace("📊 生成方案数量: " . count($result['top_solutions']), 'info');

                // 第一次尝试：使用当前方案库搜索
                $searchResult = self::findTargetRateSolutionByExpansion(
                    $result['top_solutions'],
                    $targetRate,
                    $tolerance
                );

                // 如果找不到,判断是否需要扩展搜索空间
                $searchSpaceExpanded = false;
                if (!isset($searchResult['solution']) || $searchResult['solution'] === null) {
                    trace("⚠️ 初始搜索空间无法覆盖目标利润率,尝试扩展搜索范围...", 'warning');

                    // 检查当前方案的覆盖范围
                    $rates = array_column($result['top_solutions'], 'profit_rate');
                    $minRate = min($rates);
                    $maxRate = max($rates);
                    $coverageRange = $maxRate - $minRate;

                    trace("📈 当前覆盖范围: [{$minRate}%, {$maxRate}%], 跨度: {$coverageRange}%", 'debug');

                    // 如果覆盖范围太小（<50%），扩展搜索空间重新生成方案
                    if ($coverageRange < 50) {
                        trace("🔍 覆盖范围过小,正在扩展搜索空间重新生成方案...", 'info');
                        $result = self::expandSearchSpaceAndFindBest(
                            $service,
                            $targetRate,
                            $tolerance
                        );
                        $searchSpaceExpanded = true;

                        // 用新方案重试搜索
                        if (!empty($result['top_solutions'])) {
                            trace("✅ 扩展后方案数: " . count($result['top_solutions']), 'info');
                            $searchResult = self::findTargetRateSolutionByExpansion(
                                $result['top_solutions'],
                                $targetRate,
                                $tolerance
                            );
                        }
                    }
                }

                if ($searchResult['solution']) {
                    $bestSolution = $searchResult['solution'];
                    $matchedSolution = $searchResult['solution'];

                    // 输出搜索过程日志
                    trace("✅ 搜索成功!", 'info');
                    trace("   找到范围: [{$searchResult['range']['min']}%, {$searchResult['range']['max']}%]", 'info');
                    trace("   扩展级别: {$searchResult['expansion_level']}", 'info');
                    trace("   符合方案数: " . count($searchResult['all_matched']), 'info');
                    trace("   选中利润率: {$bestSolution['profit_rate']}%", 'info');

                    // 输出详细的搜索过程
                    foreach ($searchResult['search_process'] as $step) {
                        if ($step['found_count'] > 0) {
                            trace("   └─ Level {$step['level']}: 范围 [{$step['range']['min']}%, {$step['range']['max']}%] - ✅ 找到 {$step['found_count']} 个方案", 'debug');
                        } else {
                            trace("   └─ Level {$step['level']}: 范围 [{$step['range']['min']}%, {$step['range']['max']}%] - ❌ 0个方案", 'debug');
                        }
                    }
                } else {
                    // 不应该发生，因为搜索会一直扩展到 [10%-100%]
                    trace("⚠️ 未找到任何方案", 'warning');
                    $bestSolution = $result['best_solution'];
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

            $topSolutions = $result['top_solutions'];
            if ($sortBy === null && $limit === null) {
                $bucketed = self::flattenRateBuckets($rateBuckets, 10);
                if (!empty($bucketed)) {
                    $topSolutions = $bucketed;
                }
            } else {
                $sortKey = self::normalizeSortBy($sortBy);
                $solutions = $result['all_solutions'] ?? $topSolutions;
                $solutions = self::sortSolutions($solutions, $sortKey);
                $sliceLimit = $limit ?? count($topSolutions);
                if ($sliceLimit > self::MAX_TOP_SOLUTION_LIMIT) {
                    $sliceLimit = self::MAX_TOP_SOLUTION_LIMIT;
                }
                if ($sliceLimit > 0) {
                    $solutions = array_slice($solutions, 0, $sliceLimit);
                }
                $topSolutions = $solutions;
            }

            return [
                'summary' => $summary,
                'best_solution' => $bestSolution,
                'top_solutions' => $topSolutions,
                'rate_buckets' => $rateBuckets,
                'risk_assessment' => $result['risk_assessment'] ?? null,
                'recommendations' => $result['recommendations'] ?? [],
                'strategy_used' => $targetRate !== null ? 'target_rate' : 'balanced',  // 标记使用的策略
                'target_rate_config' => $targetRate !== null && $searchResult !== null ? [
                    'target' => $targetRate,
                    'tolerance' => $tolerance,
                    'achieved' => $bestSolution['profit_rate'] ?? 0,
                    'matched' => isset($matchedSolution),
                    'search_space_expanded' => $searchSpaceExpanded,  // 是否扩展了搜索空间
                    // ✨ 搜索过程详解
                    'search_result' => [
                        'expansion_level' => $searchResult['expansion_level'],
                        'found_range' => $searchResult['range'],
                        'matched_count' => count($searchResult['all_matched'] ?? []),
                        'initial_solution_count' => count($result['top_solutions'] ?? []),  // 方案数量
                        'search_process' => array_map(function($step) {
                            return [
                                'level' => $step['level'],
                                'tolerance' => $step['tolerance'],
                                'range' => $step['range'],
                                'found_count' => $step['found_count'],
                                'status' => $step['found_count'] > 0 ? 'found' : 'not_found',
                            ];
                        }, $searchResult['search_process'] ?? []),
                    ],
                ] : ($targetRate !== null ? [
                    'target' => $targetRate,
                    'tolerance' => $tolerance,
                    'achieved' => $bestSolution['profit_rate'] ?? 0,
                    'matched' => false,
                    'search_space_expanded' => false,
                    'note' => 'No search result available',
                ] : null),
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 扩展搜索空间重新生成方案
     *
     * 场景：当前方案库覆盖范围太小，无法满足目标利润率要求
     * 解决方案：动态增加候选特码数和每个特码的组合数
     *
     * @param \app\common\service\OptimizedBestPlanService $service
     * @param float $targetRate 目标利润率
     * @param float $tolerance 容差范围
     * @return array 扩展后的方案结果
     */
    private static function expandSearchSpaceAndFindBest(
        \app\common\service\OptimizedBestPlanService $service,
        float $targetRate,
        float $tolerance
    ): array {
        trace("🚀 启动扩展搜索空间...", 'info');

        // 通过反射或动态调用增加搜索参数
        // 第一轮：3倍扩展
        $originalSpecialLimit = 20;     // 原始: 20个特码
        $originalComboLimit = 800;       // 原始: 800个组合/特码

        $expandedSpecial = $originalSpecialLimit * 2;      // 扩展到 40 个特码
        $expandedCombo = $originalComboLimit * 2;          // 扩展到 1600 个组合/特码

        trace("📍 扩展参数：特码候选数 {$originalSpecialLimit} → {$expandedSpecial}，组合数 {$originalComboLimit} → {$expandedCombo}", 'debug');

        try {
            // 使用反射设置私有属性（如果支持）
            $reflection = new \ReflectionClass($service);

            if ($reflection->hasProperty('specialCandidateLimit')) {
                $prop = $reflection->getProperty('specialCandidateLimit');
                $prop->setAccessible(true);
                $prop->setValue($service, $expandedSpecial);
            }

            if ($reflection->hasProperty('maxCombosPerSpecial')) {
                $prop = $reflection->getProperty('maxCombosPerSpecial');
                $prop->setAccessible(true);
                $prop->setValue($service, $expandedCombo);
            }

            // 重新计算最佳方案
            $result = $service->findBest7Numbers(null, 5.0, true);

            trace("✅ 扩展搜索完成，生成方案数: " . count($result['top_solutions']), 'info');

            // 检查新的覆盖范围
            if (!empty($result['top_solutions'])) {
                $rates = array_column($result['top_solutions'], 'profit_rate');
                $newMin = min($rates);
                $newMax = max($rates);
                trace("📊 扩展后覆盖范围: [{$newMin}%, {$newMax}%], 跨度: " . ($newMax - $newMin) . "%", 'debug');
            }

            return $result;
        } catch (\Exception $e) {
            trace("❌ 扩展搜索空间失败: " . $e->getMessage(), 'error');
            // 降级处理：返回原始结果
            return $service->findBest7Numbers(null, 5.0, true);
        }
    }

    /**
     * 智能目标利润率搜索 - 逐步扩展范围
     *
     * 算法逻辑：
     * 1. 首先在 [target - tolerance, target + tolerance] 范围内搜索
     * 2. 如果找不到，逐步扩展范围：
     *    - 扩展1倍：[target - 2*tolerance, target + 2*tolerance]
     *    - 扩展2倍：[target - 3*tolerance, target + 3*tolerance]
     *    - ...以此类推，直到覆盖整个 [10%, 100%] 范围
     * 3. 最终按利润率从高到低返回符合条件的方案
     *
     * @param array $solutions 所有方案列表（已按利润率排序）
     * @param float $targetRate 目标利润率（如 50%）
     * @param float $tolerance 初始误差范围（如 10%）
     * @return array ['solution' => 最佳方案, 'range' => 查找范围, 'expansion_level' => 扩展级别]
     */
    private static function findTargetRateSolutionByExpansion(
        array $solutions,
        float $targetRate,
        float $tolerance
    ): array {
        if (empty($solutions)) {
            return [
                'solution' => null,
                'range' => null,
                'expansion_level' => 0,
                'search_process' => [],
            ];
        }

        $searchProcess = [];
        $expansionLevel = 0;
        $currentTolerance = $tolerance;

        // 逐步扩展搜索范围，直到找到方案或覆盖整个范围
        while (true) {
            // 计算当前搜索范围
            $rangeMin = max(10.0, $targetRate - $currentTolerance);
            $rangeMax = min(100.0, $targetRate + $currentTolerance);

            // 在当前范围内搜索
            $matched = array_filter($solutions, function ($solution) use ($rangeMin, $rangeMax) {
                $rate = $solution['profit_rate'];
                return $rate >= $rangeMin && $rate <= $rangeMax;
            });

            // 记录搜索过程
            $searchStep = [
                'level' => $expansionLevel,
                'tolerance' => $currentTolerance,
                'range' => [
                    'min' => round($rangeMin, 2),
                    'max' => round($rangeMax, 2),
                ],
                'found_count' => count($matched),
            ];
            $searchProcess[] = $searchStep;

            // 如果找到匹配的方案，按利润率降序排列并返回第一个
            if (!empty($matched)) {
                // 按利润率降序排列
                usort($matched, function ($a, $b) {
                    return $b['profit_rate'] <=> $a['profit_rate'];
                });

                return [
                    'solution' => reset($matched),
                    'range' => [
                        'min' => round($rangeMin, 2),
                        'max' => round($rangeMax, 2),
                    ],
                    'expansion_level' => $expansionLevel,
                    'all_matched' => $matched,  // 返回所有匹配的方案
                    'search_process' => $searchProcess,
                ];
            }

            // 检查是否已经覆盖整个范围 [10%, 100%]
            if ($rangeMin <= 10.0 && $rangeMax >= 100.0) {
                // 已覆盖全范围但仍未找到，返回整个列表按降序
                usort($solutions, function ($a, $b) {
                    return $b['profit_rate'] <=> $a['profit_rate'];
                });
                return [
                    'solution' => reset($solutions),
                    'range' => [
                        'min' => 10.0,
                        'max' => 100.0,
                    ],
                    'expansion_level' => $expansionLevel,
                    'all_matched' => $solutions,
                    'search_process' => $searchProcess,
                    'note' => 'Covered full range [10%-100%], returning highest profit rate',
                ];
            }

            // 扩展容差值以进行下一轮搜索
            $expansionLevel++;
            $currentTolerance = $tolerance * ($expansionLevel + 1);
        }
    }

    /**
     * 生成固定的10%档位（100% 到 10%）
     * 确保客户后台可以从全吃到低利润的所有档位中自由选择
     *
     * @param array $solutions 已规范化的方案列表
     * @return array 固定档位列表：[100, 90, 80, 70, 60, 50, 40, 30, 20, 10]
     */
    private static function generateDynamicRates(array $solutions): array
    {
        // 返回固定的10%档位，覆盖100%-10%的全范围
        return [100, 90, 80, 70, 60, 50, 40, 30, 20, 10];
    }

    private static function buildRateBuckets(array $solutions, int $year): array
    {
        $normalized = self::normalizeBucketSolutions($solutions, $year);

        if (empty($normalized)) {
            $normalized = self::normalizeBucketSolutions(self::generateRandomSolutions(160), $year);
        }
        if (empty($normalized)) {
            $normalized = self::normalizeBucketSolutions(self::generateRandomSolutions(200), $year);
        }

        // 生成动态档位而不是固定的10档
        $rates = self::generateDynamicRates($normalized);
        $totalNeeded = count($rates) * 10;
        $allowDuplicates = count($normalized) < $totalNeeded;
        $usedCounts = [];
        $buckets = [];

        foreach ($rates as $rate) {
            $baseRange = self::getRateRange($rate);
            $inRange = array_values(array_filter($normalized, function ($solution) use ($baseRange) {
                return self::isRateInRange($solution['profit_rate_rounded'], $baseRange);
            }));
            $inRangeCount = count($inRange);

            $selected = [];
            $selectedKeys = [];

            self::appendCandidates($inRange, 10, $selected, $selectedKeys, $usedCounts, $allowDuplicates);

            $effectiveRange = $baseRange;
            $usedExpansion = false;
            $usedNearest = false;
            $usedDuplicate = false;

            if (count($selected) < 10) {
                $usedExpansion = true;
                $step = 2.0;
                $range = $effectiveRange;

                while (count($selected) < 10) {
                    $expanded = self::expandRange($rate, $range, $step);
                    if ($expanded['min'] == $range['min'] && $expanded['max'] == $range['max']) {
                        break;
                    }
                    $range = $expanded;

                    $candidates = array_values(array_filter($normalized, function ($solution) use ($range) {
                        return self::isRateInRange($solution['profit_rate_rounded'], $range);
                    }));
                    self::appendCandidates($candidates, 10, $selected, $selectedKeys, $usedCounts, $allowDuplicates);

                    if ($range['min'] <= 10.0 && $range['max'] >= 100.0) {
                        break;
                    }
                }

                $effectiveRange = $range;
            }

            if (count($selected) < 10) {
                $usedNearest = true;
                $center = $rate === 100 ? 100.0 : $rate + 5.0;
                self::appendNearestCandidates($normalized, $center, 10, $selected, $selectedKeys, $usedCounts, $allowDuplicates);
            }

            if (count($selected) < 10 && $allowDuplicates && !empty($selected)) {
                $usedDuplicate = true;
                $idx = 0;
                while (count($selected) < 10) {
                    $dup = $selected[$idx % count($selected)];
                    $dup['duplicate'] = true;
                    $selected[] = $dup;
                    $idx++;
                }
            }

            $bucket = [
                'rate' => $rate,
                'range' => self::formatRateRange($baseRange),
                'effective_range' => self::formatRateRange($effectiveRange),
                'in_range_count' => $inRangeCount,
                'count' => count($selected),
                'filled' => ($inRangeCount < 10) || $usedExpansion || $usedNearest || $usedDuplicate,
                'solutions' => self::formatBucketSolutions($selected),
            ];

            if ($bucket['filled']) {
                if ($usedNearest) {
                    $bucket['fill_reason'] = 'nearest_profit_rate';
                } elseif ($usedExpansion) {
                    $bucket['fill_reason'] = 'expanded_range';
                } elseif ($usedDuplicate) {
                    $bucket['fill_reason'] = 'duplicate_fill';
                }
            }

            usort($bucket['solutions'], function ($a, $b) {
                // 第一优先级：利润率（降序）
                if ($a['profit_rate'] != $b['profit_rate']) {
                    return $b['profit_rate'] <=> $a['profit_rate'];
                }

                // 第二优先级：混乱度/多样性（降序）- 优先返回混乱度高的号码
                $diversityA = $a['diversity_score'] ?? 0;
                $diversityB = $b['diversity_score'] ?? 0;
                if ($diversityA != $diversityB) {
                    return $diversityB <=> $diversityA;
                }

                // 第三优先级：总利润（降序）
                return $b['total_profit'] <=> $a['total_profit'];
            });

            $buckets[] = $bucket;
        }

        return $buckets;
    }

    private static function normalizeBucketSolutions(array $solutions, int $year): array
    {
        if (empty($solutions)) {
            return [];
        }

        $zodiacMap = ZodiacYearService::getNumberMapByYear($year);
        $normalized = [];

        foreach ($solutions as $solution) {
            $m1_m6 = $solution['m1_m6'] ?? [];
            $m7 = $solution['m7'] ?? null;
            if (!is_array($m1_m6) || $m7 === null) {
                continue;
            }

            $m1_m6 = array_values(array_unique(array_map('intval', $m1_m6)));
            if (count($m1_m6) !== 6) {
                continue;
            }
            $m7 = (int)$m7;
            if ($m7 < 1 || $m7 > 49) {
                continue;
            }
            if (in_array($m7, $m1_m6, true)) {
                continue;
            }

            sort($m1_m6);
            if (!self::isZodiacValid($m1_m6, $zodiacMap)) {
                continue;
            }

            $profitRate = isset($solution['profit_rate']) ? (float)$solution['profit_rate'] : 0.0;
            $profitRateRounded = round($profitRate, 2);
            $key = self::buildSolutionKey($m1_m6, $m7);
            $maxConsecutive = self::getMaxConsecutive($m1_m6);

            $normalized[$key] = [
                'm1_m6' => $m1_m6,
                'm7' => $m7,
                'numbers' => array_merge($m1_m6, [$m7]),
                'profit_rate' => $profitRate,
                'profit_rate_rounded' => $profitRateRounded,
                'total_profit' => isset($solution['total_profit']) ? (float)$solution['total_profit'] : 0.0,
                'total_prize' => isset($solution['total_prize']) ? (float)$solution['total_prize'] : (float)($solution['prize_amount'] ?? 0),
                'bet_amount' => isset($solution['bet_amount']) ? (float)$solution['bet_amount'] : (float)($solution['total_bets'] ?? 0),
                'strategy' => $solution['strategy'] ?? null,
                'distance_to_target' => isset($solution['distance_to_target']) ? (float)$solution['distance_to_target'] : null,
                'solution_key' => $key,
                'diversity_score' => self::calculateDiversityScore($m1_m6, $maxConsecutive),
                'is_sequential' => $maxConsecutive >= 5,
            ];
        }

        return array_values($normalized);
    }

    private static function isZodiacValid(array $m1_m6, array $zodiacMap): bool
    {
        $counts = [];
        foreach ($m1_m6 as $num) {
            $zodiac = $zodiacMap[$num] ?? '';
            if ($zodiac === '') {
                continue;
            }
            $counts[$zodiac] = ($counts[$zodiac] ?? 0) + 1;
            // 允许同一生肖最多4个号码，支持重肖需求
            if ($counts[$zodiac] > 4) {
                return false;
            }
        }
        return true;
    }

    private static function buildSolutionKey(array $m1_m6, int $m7): string
    {
        return implode('-', $m1_m6) . '-' . $m7;
    }

    private static function getRateRange(int $rate): array
    {
        if ($rate >= 100) {
            return [
                'min' => 100.0,
                'max' => 100.0,
                'max_inclusive' => true,
                'exact' => true,
            ];
        }

        return [
            'min' => (float)$rate,
            'max' => (float)($rate + 10),
            'max_inclusive' => false,
            'exact' => false,
        ];
    }

    private static function expandRange(int $rate, array $range, float $step): array
    {
        if ($rate >= 100) {
            $min = max(10.0, $range['min'] - $step);
            $max = 100.0;
        } else {
            $min = max(10.0, $range['min'] - $step);
            $max = min(100.0, $range['max'] + $step);
        }

        return [
            'min' => $min,
            'max' => $max,
            'max_inclusive' => $max >= 100.0,
            'exact' => $min == $max,
        ];
    }

    private static function isRateInRange(float $rate, array $range): bool
    {
        if ($range['exact']) {
            return abs($rate - $range['min']) < 0.005;
        }
        if ($range['max_inclusive']) {
            return $rate >= $range['min'] && $rate <= $range['max'];
        }
        return $rate >= $range['min'] && $rate < $range['max'];
    }

    private static function formatRateRange(array $range): string
    {
        if ($range['exact']) {
            return sprintf('=%.2f', $range['min']);
        }
        $symbol = $range['max_inclusive'] ? '<=' : '<';
        return sprintf('%.2f <= profit_rate_rounded %s %.2f', $range['min'], $symbol, $range['max']);
    }

    private static function appendCandidates(
        array $candidates,
        int $limit,
        array &$selected,
        array &$selectedKeys,
        array &$usedCounts,
        bool $allowDuplicates
    ): void {
        usort($candidates, function ($a, $b) use ($usedCounts) {
            $usedA = $usedCounts[$a['solution_key']] ?? 0;
            $usedB = $usedCounts[$b['solution_key']] ?? 0;
            if ($usedA !== $usedB) {
                return $usedA <=> $usedB;
            }
            if ($a['is_sequential'] !== $b['is_sequential']) {
                return (int)$a['is_sequential'] <=> (int)$b['is_sequential'];
            }
            if ($a['diversity_score'] !== $b['diversity_score']) {
                return $b['diversity_score'] <=> $a['diversity_score'];
            }
            if ($a['profit_rate'] !== $b['profit_rate']) {
                return $b['profit_rate'] <=> $a['profit_rate'];
            }
            return $b['total_profit'] <=> $a['total_profit'];
        });

        foreach ($candidates as $candidate) {
            if (count($selected) >= $limit) {
                break;
            }

            $key = $candidate['solution_key'];
            if (isset($selectedKeys[$key])) {
                continue;
            }
            $usedCount = $usedCounts[$key] ?? 0;
            if (!$allowDuplicates && $usedCount > 0) {
                continue;
            }

            $item = $candidate;
            if ($usedCount > 0) {
                $item['duplicate'] = true;
            }

            $selected[] = $item;
            $selectedKeys[$key] = true;
            $usedCounts[$key] = $usedCount + 1;
        }
    }

    private static function appendNearestCandidates(
        array $candidates,
        float $center,
        int $limit,
        array &$selected,
        array &$selectedKeys,
        array &$usedCounts,
        bool $allowDuplicates
    ): void {
        usort($candidates, function ($a, $b) use ($center, $usedCounts) {
            $distA = abs($a['profit_rate'] - $center);
            $distB = abs($b['profit_rate'] - $center);
            if ($distA < $distB) {
                return -1;
            }
            if ($distA > $distB) {
                return 1;
            }
            $usedA = $usedCounts[$a['solution_key']] ?? 0;
            $usedB = $usedCounts[$b['solution_key']] ?? 0;
            if ($usedA !== $usedB) {
                return $usedA <=> $usedB;
            }
            if ($a['is_sequential'] !== $b['is_sequential']) {
                return (int)$a['is_sequential'] <=> (int)$b['is_sequential'];
            }
            if ($a['diversity_score'] !== $b['diversity_score']) {
                return $b['diversity_score'] <=> $a['diversity_score'];
            }
            if ($a['profit_rate'] !== $b['profit_rate']) {
                return $b['profit_rate'] <=> $a['profit_rate'];
            }
            return $b['total_profit'] <=> $a['total_profit'];
        });

        foreach ($candidates as $candidate) {
            if (count($selected) >= $limit) {
                break;
            }

            $key = $candidate['solution_key'];
            if (isset($selectedKeys[$key])) {
                continue;
            }
            $usedCount = $usedCounts[$key] ?? 0;
            if (!$allowDuplicates && $usedCount > 0) {
                continue;
            }

            $item = $candidate;
            if ($usedCount > 0) {
                $item['duplicate'] = true;
            }

            $selected[] = $item;
            $selectedKeys[$key] = true;
            $usedCounts[$key] = $usedCount + 1;
        }
    }

    private static function formatBucketSolutions(array $solutions): array
    {
        $formatted = [];
        foreach ($solutions as $solution) {
            $item = [
                'numbers' => $solution['numbers'] ?? [],
                'm1_m6' => $solution['m1_m6'] ?? [],
                'm7' => $solution['m7'] ?? 0,
                'profit_rate' => round((float)($solution['profit_rate'] ?? 0), 2),
                'total_profit' => (float)($solution['total_profit'] ?? 0),
                'total_prize' => (float)($solution['total_prize'] ?? 0),
                'bet_amount' => (float)($solution['bet_amount'] ?? 0),
                'strategy' => $solution['strategy'] ?? null,
                'distance_to_target' => isset($solution['distance_to_target']) ? (float)$solution['distance_to_target'] : null,
            ];
            if (!empty($solution['duplicate'])) {
                $item['duplicate'] = true;
            }
            $formatted[] = $item;
        }
        return $formatted;
    }

    private static function flattenRateBuckets(array $buckets, int $perBucket = 10): array
    {
        $flat = [];
        foreach ($buckets as $bucket) {
            $solutions = $bucket['solutions'] ?? [];
            if (empty($solutions)) {
                continue;
            }
            if ($perBucket > 0) {
                $solutions = array_slice($solutions, 0, $perBucket);
            }
            $flat = array_merge($flat, $solutions);
        }
        return $flat;
    }

    private static function calculateDiversityScore(array $numbers, int $maxConsecutive): int
    {
        if (empty($numbers)) {
            return 0;
        }

        $oddCount = 0;
        foreach ($numbers as $num) {
            if ($num % 2 !== 0) {
                $oddCount++;
            }
        }

        $score = 0;
        if ($oddCount >= 2 && $oddCount <= 4) {
            $score += 2;
        } elseif ($oddCount === 1 || $oddCount === 5) {
            $score += 1;
        }

        $min = min($numbers);
        $max = max($numbers);
        $spread = $max - $min;
        if ($spread >= 20) {
            $score += 2;
        } elseif ($spread >= 12) {
            $score += 1;
        }

        $segments = [0, 0, 0];
        foreach ($numbers as $num) {
            if ($num <= 16) {
                $segments[0] = 1;
            } elseif ($num <= 33) {
                $segments[1] = 1;
            } else {
                $segments[2] = 1;
            }
        }
        $score += array_sum($segments);

        if ($maxConsecutive <= 3) {
            $score += 1;
        } elseif ($maxConsecutive >= 5) {
            $score -= 1;
        }

        return $score;
    }

    /**
     * 检查是否存在连续序列（5个或以上连续号码）
     *
     * @param array $numbers 号码数组（已排序）
     * @return int 最大连续号码数
     */
    private static function getMaxConsecutive(array $numbers): int
    {
        if (empty($numbers)) {
            return 0;
        }

        sort($numbers);
        $max = 1;
        $current = 1;
        $count = count($numbers);
        for ($i = 1; $i < $count; $i++) {
            if ($numbers[$i] === $numbers[$i - 1] + 1) {
                $current++;
            } else {
                $current = 1;
            }
            if ($current > $max) {
                $max = $current;
            }
        }
        return $max;
    }

    private static function generateRandomSolutions(int $count): array
    {
        $solutions = [];
        $used = [];
        $attempts = 0;
        $maxAttempts = $count * 12;

        while (count($solutions) < $count && $attempts < $maxAttempts) {
            $pool = range(1, 49);
            shuffle($pool);
            $numbers = array_slice($pool, 0, 7);
            $m7Index = array_rand($numbers);
            $m7 = $numbers[$m7Index];
            unset($numbers[$m7Index]);
            $m1_m6 = array_values($numbers);
            sort($m1_m6);

            // 排除顺序号码（连续5个或以上）
            $maxConsecutive = self::getMaxConsecutive($m1_m6);
            if ($maxConsecutive >= 5) {
                $attempts++;
                continue;
            }

            $key = self::buildSolutionKey($m1_m6, $m7);
            if (isset($used[$key])) {
                $attempts++;
                continue;
            }

            $used[$key] = true;
            $solutions[] = [
                'm1_m6' => $m1_m6,
                'm7' => $m7,
                'profit_rate' => 100.0,
                'total_profit' => 0.0,
                'total_prize' => 0.0,
                'bet_amount' => 0.0,
            ];

            $attempts++;
        }

        return $solutions;
    }

    private static function normalizeSolutionLimit($limit): ?int
    {
        if ($limit === null || $limit === '') {
            return null;
        }
        $limit = (int)$limit;
        if ($limit <= 0) {
            return null;
        }
        return $limit > self::MAX_TOP_SOLUTION_LIMIT ? self::MAX_TOP_SOLUTION_LIMIT : $limit;
    }

    private static function normalizeSortBy(?string $sortBy): string
    {
        $sortBy = strtolower(trim((string)$sortBy));
        if ($sortBy === 'total_profit' || $sortBy === 'profit') {
            return 'total_profit';
        }
        return 'profit_rate';
    }

    private static function sortSolutions(array $solutions, string $sortBy): array
    {
        if (empty($solutions)) {
            return $solutions;
        }
        if ($sortBy === 'total_profit') {
            usort($solutions, function ($a, $b) {
                $profitA = (float)($a['total_profit'] ?? 0);
                $profitB = (float)($b['total_profit'] ?? 0);
                if ($profitA === $profitB) {
                    $rateA = (float)($a['profit_rate'] ?? 0);
                    $rateB = (float)($b['profit_rate'] ?? 0);
                    return $rateB <=> $rateA;
                }
                return $profitB <=> $profitA;
            });
            return $solutions;
        }

        usort($solutions, function ($a, $b) {
            $rateA = (float)($a['profit_rate'] ?? 0);
            $rateB = (float)($b['profit_rate'] ?? 0);
            if ($rateA === $rateB) {
                $profitA = (float)($a['total_profit'] ?? 0);
                $profitB = (float)($b['total_profit'] ?? 0);
                return $profitB <=> $profitA;
            }
            return $rateB <=> $rateA;
        });

        return $solutions;
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
                Db::rollback();
                self::setError('期号不存在');
                return false;
            }

            // Check if planning window is open (must be after close_time)
            $closeTimeRaw = $issue['close_time'] ?? 0;
            $closeTimeTs = is_numeric($closeTimeRaw) ? (int)$closeTimeRaw : (int)strtotime((string)$closeTimeRaw);
            $currentTime = time();
            if ($closeTimeTs > 0 && $currentTime < $closeTimeTs) {
                Db::rollback();
                self::setError('未到封盘时间，不能提交计划');
                return false;
            }

            if (!empty($issue['result'])) {
                Db::rollback();
                self::setError('本期已开奖，不能重复提交计划');
                return false;
            }

            if (!empty($issue['is_settled'])) {
                Db::rollback();
                self::setError('本期已结算，不能重复提交计划');
                return false;
            }

            // Prevent duplicate submission of manual plan
            if (!empty($issue['planned_result']) && $issue['planned_source'] == 1) {
                Db::rollback();
                self::setError('本期已设置计划，不能重复提交');
                return false;
            }

            $numbers = array_values(array_map('intval', $bestNumbers));
            if (count($numbers) !== 7) {
                Db::rollback();
                self::setError('必须提交7个开奖号码');
                return false;
            }
            foreach ($numbers as $num) {
                if ($num < 1 || $num > 49) {
                    Db::rollback();
                    self::setError('号码范围必须在1-49之间');
                    return false;
                }
            }

            $m1_m6 = array_slice($numbers, 0, 6);
            $m7 = $numbers[6];

            // Use lockForUpdate to maintain exclusive lock during update
            Db::table('la_lottery_issue')
                ->where('id', $issue['id'])
                ->lockForUpdate()
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

            // 支持跨年份生肖：7th号码可以匹配任意年份的同生肖
            $allPossibleZodiacs = self::getAllPossibleZodiacs($m7);
            $hit = !empty(array_intersect($betZodiacs, $allPossibleZodiacs));
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

    /**
     * 获取号码在所有年份中可能的生肖
     *
     * 例如：号码3在不同年份中可能是：
     * - 1981年的鸡
     * - 1993年的鸡
     * - 2005年的鸡
     * - 2017年的鸡
     * - 2029年的鸡
     *
     * 这允许特肖投注在跨年份范围内生效，
     * 只要7th号码的生肖在任何年份中匹配投注的生肖即可
     *
     * @param int $number 号码（1-49）
     * @return array 该号码在所有年份中对应的生肖列表（去重）
     */
    private static function getAllPossibleZodiacs(int $number): array
    {
        if ($number < 1 || $number > 49) {
            return [];
        }

        static $cache = [];

        // 优先检查缓存
        if (isset($cache[$number])) {
            return $cache[$number];
        }

        $zodiacs = [];

        // 扫描一个完整的生肖轮转周期（12年）
        // 在这个周期内可以获得该号码的所有可能生肖
        $baseYear = 2000;
        for ($offset = 0; $offset < 12; $offset++) {
            $year = $baseYear + $offset;
            try {
                $numberMap = ZodiacYearService::getNumberMapByYear($year);
                if (isset($numberMap[$number])) {
                    $zodiac = $numberMap[$number];
                    if (!empty($zodiac) && !in_array($zodiac, $zodiacs, true)) {
                        $zodiacs[] = $zodiac;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // 缓存结果
        $cache[$number] = $zodiacs;

        return $zodiacs;
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
