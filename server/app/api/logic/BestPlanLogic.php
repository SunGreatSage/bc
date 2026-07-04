<?php
declare(strict_types=1);

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\service\BestPlanService;
use app\common\service\DrawPlanEvaluationService;
use app\common\service\LotteryPlayRuleService;
use app\common\service\OperationLogContentService;
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

            $service = new \app\common\service\OptimizedBestPlanService($gid, $qishu, $year, $plateCode);

            if ($service->getBetCount() === 0) {
                $randomSolutions = self::generateRandomSolutions(20);

                if (empty($randomSolutions)) {
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
                    'has_bets' => false,
                ];

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
                    'top_solutions' => $randomSolutions,
                    'message' => 'No bets: random solutions generated.',
                ];
            }

            $result = $service->findBest7Numbers(null, 5.0, true);

            $allSolutions = array_key_exists('all_solutions', $result)
                ? self::filterSolutionsByNonNegative($result['all_solutions'])
                : null;
            $topSolutions = self::filterSolutionsByNonNegative($result['top_solutions']);
            if ($allSolutions !== null) {
                $result['all_solutions'] = $allSolutions;
            }
            $result['top_solutions'] = $topSolutions;

            $bucketSource = $allSolutions ?? $topSolutions;
            $rateBuckets = self::buildRateBuckets($bucketSource, $year);
            $bestSolution = $result['best_solution'];
            if ($bestSolution !== null && self::getSolutionTotalProfit($bestSolution) < 0) {
                $bestSolution = self::pickBestNonNegativeSolution($bucketSource);
            }

            $summary = [
                'total_bets' => $result['total_bets'],
                'total_orders' => $result['total_orders'],
                'best_numbers' => $bestSolution ? array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]) : [],
                'best_m7' => $bestSolution['m7'] ?? 0,
                'best_m1_m6' => $bestSolution['m1_m6'] ?? [],
                'best_profit' => $bestSolution['total_profit'] ?? 0,
                'best_profit_rate' => $bestSolution['profit_rate'] ?? 0,
            ];

            $numberDetails = [];
            if ($bestSolution) {
                $allNumbers = array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]);
                foreach ($allNumbers as $number) {
                    $numberDetails[] = [
                        'number' => $number,
                        'profit' => $bestSolution['total_profit'] / 7,
                        'profit_rate' => $bestSolution['profit_rate'],
                        'prize_amount' => 0,
                        'bet_count' => 0,
                        'risk_level' => 0,
                    ];
                }
            }

            $data = [
                'gid' => $gid,
                'qishu' => $qishu,
                'plate_code' => $plateCode,
                'analyze_time' => date('Y-m-d H:i:s'),
                'total_bets' => $summary['total_bets'],
                'total_orders' => $summary['total_orders'],
                'best_numbers' => implode(',', $summary['best_numbers'] ?? []),
                'best_profit' => $summary['best_profit'],
                'best_profit_rate' => $summary['best_profit_rate'],
                'worst_number' => 0,
                'worst_profit' => 0,
                'worst_profit_rate' => 0,
                'avg_profit' => 0,
                'number_details' => json_encode($numberDetails, JSON_UNESCAPED_UNICODE),
                'status' => 0,
            ];

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
     * @param string $plateCode 盘口代码（如 A、B、C）
     * @param int|null $year 年份
     * @param float|null $targetRate 目标利润率（如 10 表示 10%，null 表示最大化利润）
     * @param float $tolerance 误差范围（默认 5%）
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
        ?int $limit = null,
        ?int $maxConsecutive = null,
        bool $includeNegative = true
    ) {
        try {
            $year = $year ?? (int)date('Y');
            $sortBy = is_string($sortBy) ? trim($sortBy) : null;
            if ($sortBy === '') {
                $sortBy = null;
            }
            $limit = self::normalizeSolutionLimit($limit);
            $maxConsecutive = self::normalizeMaxConsecutive($maxConsecutive);

            // 使用优化版算法，统一处理中/不中投注。
            $service = new \app\common\service\OptimizedBestPlanService($gid, $qishu, $year, $plateCode);

            $issue = Db::table('la_lottery_issue')
                ->field('close_time, draw_time, status, result')
                ->where('game_id', $gid)
                ->where('issue', $qishu)
                ->where('plate_code', $plateCode)
                ->find();
            if ($issue) {
                $closeTime = self::normalizeTimestamp($issue['close_time'] ?? 0);
                if ($closeTime > 0 && time() < $closeTime) {
                    self::setError('当前期号尚未封盘，封盘后才生成开奖方案');
                    return false;
                }
            }

            // 如果没有投注数据，不提前生成随机计划；到开奖时间由定时任务自动随机开奖并续期。
            if ($service->getBetCount() === 0) {
                return [
                    'summary' => [
                        'total_bets' => 0,
                        'total_orders' => 0,
                        'best_numbers' => [],
                        'best_m7' => 0,
                        'best_m1_m6' => [],
                        'best_profit' => 0,
                        'best_profit_rate' => 0.0,
                        'has_bets' => false,
                    ],
                    'best_solution' => null,
                    'top_solutions' => [],
                    'rate_buckets' => [],
                    'positive_plans' => [],
                    'negative_plans' => [],
                    'risk_assessment' => [
                        'risk_level' => 'safe',
                        'description' => '本期暂无投注。',
                    ],
                    'recommendations' => ['本期暂无投注，到开奖时间系统会自动随机开奖并创建下一期。'],
                    'strategy_used' => 'no_bets',
                    'message' => '本期暂无投注，到开奖时间系统会自动随机开奖。',
                ];
            }
            $result = $service->findBest7Numbers(null, 5.0, true, $maxConsecutive);

            $allSolutions = array_key_exists('all_solutions', $result)
                ? ($includeNegative ? $result['all_solutions'] : self::filterSolutionsByNonNegative($result['all_solutions']))
                : null;
            $topSolutions = $includeNegative
                ? ($result['top_solutions'] ?? [])
                : self::filterSolutionsByNonNegative($result['top_solutions'] ?? []);
            if ($maxConsecutive !== null) {
                if ($allSolutions !== null) {
                    $allSolutions = self::filterSolutionsByMaxConsecutive($allSolutions, $maxConsecutive);
                }
                $topSolutions = self::filterSolutionsByMaxConsecutive($topSolutions, $maxConsecutive);
            }
            if ($allSolutions !== null) {
                $result['all_solutions'] = $allSolutions;
            }
            $result['top_solutions'] = $topSolutions;

            $bucketSource = $allSolutions ?? $topSolutions;
            if ($maxConsecutive !== null) {
                $bucketSource = self::fillSolutionsToMinimum($service, $bucketSource, 100, $maxConsecutive);
            }
            $rateBuckets = self::buildRateBuckets($bucketSource, $year, $maxConsecutive, false);
            $positivePlans = self::buildRepresentativePlans($bucketSource, [10, 20, 30, 40, 50, 60, 70, 80, 90, 100], $year, $maxConsecutive, false);
            $negativePlans = $includeNegative
                ? self::buildRepresentativePlans($bucketSource, [-5, -10, -15, -20], $year, $maxConsecutive, true)
                : [];

            // 如果指定了目标利润率，使用智能扩展搜索。
            $searchResult = null;
            if ($targetRate !== null && !empty($result['top_solutions'])) {
                trace("目标利润率: {$targetRate}%, 初始误差: ±{$tolerance}%", 'info');
                trace("生成方案数量: " . count($result['top_solutions']), 'info');

                // 第一次尝试：使用当前方案库搜索。
                $searchResult = self::findTargetRateSolutionByExpansion(
                    $result['top_solutions'],
                    $targetRate,
                    $tolerance
                );

                // 如果找不到，判断是否需要扩展搜索空间。
                $searchSpaceExpanded = false;
                if (!isset($searchResult['solution']) || $searchResult['solution'] === null) {
                    trace("No solution found after expansion; using best solution.", 'warning');

                    // 检查当前方案的覆盖范围。
                    $rates = array_column($result['top_solutions'], 'profit_rate');
                    $minRate = min($rates);
                    $maxRate = max($rates);
                    $coverageRange = $maxRate - $minRate;

                    trace("当前覆盖范围: [{$minRate}%, {$maxRate}%], 跨度: {$coverageRange}%", 'debug');

                    // 如果覆盖范围太小（小于 50%），扩展搜索空间重新生成方案。
                    if ($coverageRange < 50) {
                        trace("覆盖范围过小，正在扩展搜索空间重新生成方案...", 'info');
                        $result = self::expandSearchSpaceAndFindBest(
                            $service,
                            $targetRate,
                            $tolerance,
                            $maxConsecutive
                        );
                        $searchSpaceExpanded = true;

                        // 用新方案重试搜索。
                        if (!empty($result['top_solutions'])) {
                            trace("扩展后方案数: " . count($result['top_solutions']), 'info');
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

                    trace("搜索成功", 'info');
                    trace("   找到范围: [{$searchResult['range']['min']}%, {$searchResult['range']['max']}%]", 'info');
                    trace("   扩展级别: {$searchResult['expansion_level']}", 'info');
                    trace("   符合方案数: " . count($searchResult['all_matched']), 'info');
                    trace("   选中利润率: {$bestSolution['profit_rate']}%", 'info');

                    // 输出详细搜索过程。
                    foreach ($searchResult['search_process'] as $step) {
                        if ($step['found_count'] > 0) {
                            trace("   Level {$step['level']}: range [{$step['range']['min']}%, {$step['range']['max']}%] - found {$step['found_count']} solutions", 'debug');
                        } else {
                            trace("   Level {$step['level']}: range [{$step['range']['min']}%, {$step['range']['max']}%] - found 0 solutions", 'debug');
                        }
                    }
                } else {
                    // 正常不应发生，搜索会一直扩展到 [10%-100%]。
                    trace("No solution found after expansion; using best solution.", 'warning');
                    $bestSolution = $result['best_solution'];
                }
            } else {
                // 没有指定目标利润率，使用当前最佳方案。
                $bestSolution = $result['best_solution'];
            }

            // 构建摘要（使用计算结果中的数据）。
            if ($bestSolution !== null && self::getSolutionTotalProfit($bestSolution) < 0) {
                $bestSolution = self::pickBestNonNegativeSolution($bucketSource);
            }

            $summary = [
                'total_bets' => $result['total_bets'],
                'total_orders' => $result['total_orders'],
                'best_numbers' => $bestSolution ? array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]) : [],
                'best_m7' => $bestSolution['m7'] ?? 0,
                'best_m1_m6' => $bestSolution['m1_m6'] ?? [],
                'best_profit' => $bestSolution['total_profit'] ?? 0,
                'best_profit_rate' => $bestSolution['profit_rate'] ?? 0,
            ];

            if ($maxConsecutive !== null) {
                $mixCandidates = $result['all_solutions'] ?? $result['top_solutions'];
                if ($searchResult && !empty($searchResult['all_matched'])) {
                    $mixCandidates = $searchResult['all_matched'];
                }
                $mixCandidates = self::filterSolutionsByNonNegative($mixCandidates);
                $bestSolutionWithConstraint = self::pickBestSolutionWithMaxConsecutive($mixCandidates, $maxConsecutive);
                if ($bestSolutionWithConstraint === null) {
                    $expanded = self::expandSearchSpaceForMaxConsecutive($service, $maxConsecutive);
                    $bestSolutionWithConstraint = $expanded['best_solution'] ?? null;
                }
                if ($bestSolutionWithConstraint === null) {
                    $bestSolutionWithConstraint = self::findBestSolutionBySampling($service, $maxConsecutive, 3);
                }
                if ($bestSolutionWithConstraint === null) {
                    self::setError('无法找到满足连续号码限制的方案');
                    return false;
                }
                $bestSolution = $bestSolutionWithConstraint;
                $summary['best_numbers'] = $bestSolution ? array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]) : [];
                $summary['best_m7'] = $bestSolution['m7'] ?? 0;
                $summary['best_m1_m6'] = $bestSolution['m1_m6'] ?? [];
                $summary['best_profit'] = $bestSolution['total_profit'] ?? 0;
                $summary['best_profit_rate'] = $bestSolution['profit_rate'] ?? 0;
            }
            if ($maxConsecutive !== null && empty($rateBuckets) && $bestSolution) {
                $rateBuckets = self::buildRateBuckets([$bestSolution], $year, $maxConsecutive, false);
            }

            $targetMatched = false;
            if ($targetRate !== null && $bestSolution) {
                $achieved = (float)($bestSolution['profit_rate'] ?? 0);
                $targetMatched = $achieved >= ($targetRate - $tolerance) && $achieved <= ($targetRate + $tolerance);
            }

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
            if ($maxConsecutive !== null) {
                $topSolutions = self::filterSolutionsByMaxConsecutive($topSolutions, $maxConsecutive);
                if (empty($topSolutions) && $bestSolution) {
                    $topSolutions = [$bestSolution];
                }
            }
            if ($includeNegative) {
                $topSolutions = self::mergeRepresentativePlans($topSolutions, $positivePlans, $negativePlans);
            }

            return [
                'summary' => $summary,
                'best_solution' => $bestSolution,
                'top_solutions' => $topSolutions,
                'rate_buckets' => $rateBuckets,
                'positive_plans' => $positivePlans,
                'negative_plans' => $negativePlans,
                'risk_assessment' => $result['risk_assessment'] ?? null,
                'recommendations' => $result['recommendations'] ?? [],
                'strategy_used' => $targetRate !== null ? 'target_rate' : 'balanced',  // 标记使用的策略
                'target_rate_config' => $targetRate !== null && $searchResult !== null ? [
                    'target' => $targetRate,
                    'tolerance' => $tolerance,
                    'achieved' => $bestSolution['profit_rate'] ?? 0,
                    'matched' => $targetMatched,
                    'search_space_expanded' => $searchSpaceExpanded,  // 是否扩展了搜索空间
                    // 搜索过程详情
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
                    'matched' => $targetMatched,
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
     * 场景：当前方案库覆盖范围太小，无法满足目标利润率要求。
     * 方案：动态增加候选特码数和每个特码的组合数。
     *
     * @param \app\common\service\OptimizedBestPlanService $service
     * @param float $targetRate 目标利润率
     * @param float $tolerance 容差范围
     * @return array 扩展后的方案结果
     */
    private static function expandSearchSpaceAndFindBest(
        \app\common\service\OptimizedBestPlanService $service,
        float $targetRate,
        float $tolerance,
        ?int $maxConsecutive = null
    ): array {
        trace("启动扩展搜索空间...", 'info');

        // 通过反射动态增加搜索参数。
        $originalSpecialLimit = 20;     // 原始: 20 个特码
        $originalComboLimit = 800;      // 原始: 每个特码 800 个组合

        $expandedSpecial = $originalSpecialLimit * 2;      // 扩展到 40 个特码
        $expandedCombo = $originalComboLimit * 2;          // 扩展到每个特码 1600 个组合

        trace("扩展参数: 特码候选数 {$originalSpecialLimit} -> {$expandedSpecial}, 组合数 {$originalComboLimit} -> {$expandedCombo}", 'debug');

        try {
            // 使用反射设置私有属性（如果支持）。
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

            // 重新计算最佳方案。
            $result = $service->findBest7Numbers(null, 5.0, true, $maxConsecutive);

            trace("扩展搜索完成，生成方案数: " . count($result['top_solutions']), 'info');

            // 检查新的覆盖范围。
            if (!empty($result['top_solutions'])) {
                $rates = array_column($result['top_solutions'], 'profit_rate');
                $newMin = min($rates);
                $newMax = max($rates);
                trace("扩展后覆盖范围: [{$newMin}%, {$newMax}%], 跨度: " . ($newMax - $newMin) . "%", 'debug');
            }

            return $result;
        } catch (\Exception $e) {
            trace("扩展搜索空间失败: " . $e->getMessage(), 'error');
            // 降级处理：返回原始结果。
            return $service->findBest7Numbers(null, 5.0, true, $maxConsecutive);
        }
    }

    /**
     * 智能目标利润率搜索 - 逐步扩展范围
     *
     * 算法逻辑：
     * 1. 首先在 [target - tolerance, target + tolerance] 范围内搜索
     * 2. 如果找不到，逐步扩展范围
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

        // 逐步扩展搜索范围，直到找到方案或覆盖整个范围。
        while (true) {
            // 计算当前搜索范围。
            $rangeMin = max(10.0, $targetRate - $currentTolerance);
            $rangeMax = min(100.0, $targetRate + $currentTolerance);

            // 在当前范围内搜索。
            $matched = array_filter($solutions, function ($solution) use ($rangeMin, $rangeMax) {
                $rate = $solution['profit_rate'];
                return $rate >= $rangeMin && $rate <= $rangeMax;
            });

            // 记录搜索过程。
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

            // 如果找到匹配方案，按利润率降序排列并返回第一个。
            if (!empty($matched)) {
                // 按利润率降序排列。
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

            // 检查是否已经覆盖整个范围 [10%, 100%]。
            if ($rangeMin <= 10.0 && $rangeMax >= 100.0) {
                // 已覆盖全范围但仍未找到，返回按利润率降序的整个列表。
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

            // 扩展容差值以进行下一轮搜索。
            $expansionLevel++;
            $currentTolerance = $tolerance * ($expansionLevel + 1);
        }
    }

    /**
     * 生成固定的 10% 档位（100% 到 10%）。
     * 确保后台可以从高到低的利润档位中自由选择。
     *
     * @param array $solutions 已规范化的方案列表
     * @return array 固定档位列表：[100, 90, 80, 70, 60, 50, 40, 30, 20, 10]
     */
    private static function generateDynamicRates(array $solutions): array
    {
        // 返回固定的 10% 档位，覆盖 100%-10% 的全范围。
        return [100, 90, 80, 70, 60, 50, 40, 30, 20, 10];
    }

    private static function buildRateBuckets(
        array $solutions,
        int $year,
        ?int $maxConsecutive = null,
        bool $allowRandomFallback = true
    ): array
    {
        $normalized = self::normalizeBucketSolutions($solutions, $year, $maxConsecutive);

        if (empty($normalized) && !$allowRandomFallback) {
            return [];
        }

        if (empty($normalized)) {
            $normalized = self::normalizeBucketSolutions(self::generateRandomSolutions(160, $maxConsecutive), $year, $maxConsecutive);
        }
        if (empty($normalized)) {
            $normalized = self::normalizeBucketSolutions(self::generateRandomSolutions(200, $maxConsecutive), $year, $maxConsecutive);
        }

        // 生成动态档位，而不是固定 10 档。
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
                // 第一优先级：利润率（降序）。
                if ($a['profit_rate'] != $b['profit_rate']) {
                    return $b['profit_rate'] <=> $a['profit_rate'];
                }

                // 第二优先级：多样性（降序），优先返回混合度高的号码。
                $diversityA = $a['diversity_score'] ?? 0;
                $diversityB = $b['diversity_score'] ?? 0;
                if ($diversityA != $diversityB) {
                    return $diversityB <=> $diversityA;
                }

                // 第三优先级：总利润（降序）。
                return $b['total_profit'] <=> $a['total_profit'];
            });

            $buckets[] = $bucket;
        }

        return $buckets;
    }

    private static function normalizeBucketSolutions(
        array $solutions,
        int $year,
        ?int $maxConsecutiveLimit = null,
        bool $allowNegative = false
    ): array
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
            $totalProfit = self::getSolutionTotalProfit($solution);
            $totalPrize = isset($solution['total_prize']) ? (float)$solution['total_prize'] : (float)($solution['prize_amount'] ?? 0);
            $betAmount = isset($solution['bet_amount']) ? (float)$solution['bet_amount'] : (float)($solution['total_bets'] ?? 0);
            $wipeoutType = self::getWipeoutPlanType([
                'profit_rate' => $profitRate,
                'total_prize' => $totalPrize,
                'bet_amount' => $betAmount,
            ]);
            if (!$allowNegative && $totalProfit < 0) {
                continue;
            }
            $key = self::buildSolutionKey($m1_m6, $m7);
            $comboNumbers = array_merge($m1_m6, [$m7]);
            $comboMaxConsecutive = self::getMaxConsecutive($comboNumbers);
            if ($maxConsecutiveLimit !== null && $comboMaxConsecutive > $maxConsecutiveLimit) {
                continue;
            }

            $normalized[$key] = [
                'm1_m6' => $m1_m6,
                'm7' => $m7,
                'numbers' => array_merge($m1_m6, [$m7]),
                'profit_rate' => $profitRate,
                'profit_rate_rounded' => $profitRateRounded,
                'total_profit' => $totalProfit,
                'total_prize' => $totalPrize,
                'bet_amount' => $betAmount,
                'strategy' => $solution['strategy'] ?? null,
                'distance_to_target' => isset($solution['distance_to_target']) ? (float)$solution['distance_to_target'] : null,
                'is_wipeout_plan' => $wipeoutType !== '',
                'wipeout_type' => $wipeoutType,
                'solution_key' => $key,
                'diversity_score' => self::calculateDiversityScore($m1_m6, $comboMaxConsecutive),
                'is_sequential' => $comboMaxConsecutive >= 5,
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
            // 允许同一生肖最多 4 个号码，支持重肖需求。
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
            $wipeoutType = self::getWipeoutPlanType($item);
            $item['is_wipeout_plan'] = $wipeoutType !== '';
            $item['wipeout_type'] = $wipeoutType;
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

    private static function buildRepresentativePlans(
        array $solutions,
        array $targets,
        int $year,
        ?int $maxConsecutive = null,
        bool $negativeOnly = false
    ): array {
        $normalized = self::normalizeBucketSolutions($solutions, $year, $maxConsecutive, $negativeOnly);
        if (empty($normalized)) {
            return [];
        }

        $plans = [];
        $used = [];
        foreach ($targets as $target) {
            $target = (float)$target;
            $candidates = array_values(array_filter($normalized, function ($solution) use ($negativeOnly) {
                $rate = (float)($solution['profit_rate'] ?? 0);
                return $negativeOnly ? $rate < 0 : $rate >= 0;
            }));
            if (empty($candidates)) {
                continue;
            }

            usort($candidates, function ($a, $b) use ($target) {
                $distA = abs((float)$a['profit_rate'] - $target);
                $distB = abs((float)$b['profit_rate'] - $target);
                if ($distA !== $distB) {
                    return $distA <=> $distB;
                }
                if ((float)$a['profit_rate'] !== (float)$b['profit_rate']) {
                    return (float)$b['profit_rate'] <=> (float)$a['profit_rate'];
                }
                return (float)$b['total_profit'] <=> (float)$a['total_profit'];
            });

            $selected = null;
            foreach ($candidates as $candidate) {
                $key = $candidate['solution_key'] ?? self::buildSolutionKey($candidate['m1_m6'], (int)$candidate['m7']);
                if (isset($used[$key])) {
                    continue;
                }
                $selected = $candidate;
                $used[$key] = true;
                break;
            }
            if ($selected === null) {
                $selected = $candidates[0];
            }

            $plan = self::formatBucketSolutions([$selected])[0];
            $plan['target_rate'] = $target;
            $plan['rate_type'] = $negativeOnly ? 'negative' : 'positive';
            $plan['distance_to_target'] = round(abs((float)$plan['profit_rate'] - $target), 2);
            $plans[] = $plan;
        }

        return $plans;
    }

    private static function mergeRepresentativePlans(array $topSolutions, array $positivePlans, array $negativePlans): array
    {
        $merged = [];
        foreach ([$positivePlans, $negativePlans, $topSolutions] as $group) {
            foreach ($group as $solution) {
                $m1m6 = $solution['m1_m6'] ?? [];
                $m7 = $solution['m7'] ?? null;
                if (!is_array($m1m6) || $m7 === null) {
                    continue;
                }
                $m1m6 = array_values(array_map('intval', $m1m6));
                sort($m1m6);
                $key = self::buildSolutionKey($m1m6, (int)$m7);
                if (isset($merged[$key])) {
                    continue;
                }
                $solution['m1_m6'] = $m1m6;
                $solution['m7'] = (int)$m7;
                if (!isset($solution['numbers'])) {
                    $solution['numbers'] = array_merge($m1m6, [(int)$m7]);
                }
                $merged[$key] = $solution;
            }
        }

        return array_values($merged);
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
     * 检查是否存在连续号码序列。
     *
     * @param array $numbers 号码数组
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

    private static function generateRandomSolutions(int $count, ?int $maxConsecutive = null): array
    {
        $solutions = [];
        $used = [];
        $attempts = 0;
        $maxAttempts = $count * 12;
        if ($maxConsecutive !== null) {
            $multiplier = $maxConsecutive <= 2 ? 80 : ($maxConsecutive <= 3 ? 40 : 20);
            $maxAttempts = max($maxAttempts, $count * $multiplier);
        }
        $allowedMax = $maxConsecutive !== null ? $maxConsecutive : 4;

        while (count($solutions) < $count && $attempts < $maxAttempts) {
            $pool = range(1, 49);
            shuffle($pool);
            $numbers = array_slice($pool, 0, 7);
            $m7Index = array_rand($numbers);
            $m7 = $numbers[$m7Index];
            unset($numbers[$m7Index]);
            $m1_m6 = array_values($numbers);
            sort($m1_m6);

            // 排除超出连续号码限制的组合。
            $comboMaxConsecutive = self::getMaxConsecutive(array_merge($m1_m6, [(int)$m7]));
            if ($allowedMax > 0 && $comboMaxConsecutive > $allowedMax) {
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

    private static function fillSolutionsToMinimum(
        \app\common\service\OptimizedBestPlanService $service,
        array $solutions,
        int $minCount,
        ?int $maxConsecutive = null
    ): array {
        if ($minCount <= 0) {
            return $solutions;
        }

        $unique = [];
        foreach ($solutions as $solution) {
            $m1_m6 = $solution['m1_m6'] ?? null;
            $m7 = $solution['m7'] ?? null;
            if (!is_array($m1_m6) || $m7 === null) {
                continue;
            }
            $m1_m6 = array_values(array_unique(array_map('intval', $m1_m6)));
            if (count($m1_m6) !== 6) {
                continue;
            }
            $m7 = (int)$m7;
            if ($m7 < 1 || $m7 > 49 || in_array($m7, $m1_m6, true)) {
                continue;
            }
            $comboNumbers = array_merge($m1_m6, [$m7]);
            if ($maxConsecutive !== null && self::getMaxConsecutive($comboNumbers) > $maxConsecutive) {
                continue;
            }
            if (self::getSolutionTotalProfit($solution) < 0) {
                continue;
            }
            sort($m1_m6);
            $key = self::buildSolutionKey($m1_m6, $m7);
            $solution['m1_m6'] = $m1_m6;
            $solution['m7'] = $m7;
            $unique[$key] = $solution;
        }

        if (count($unique) >= $minCount) {
            return array_values($unique);
        }

        $attempts = 0;
        $maxAttempts = max(200, $minCount * 20);
        while (count($unique) < $minCount && $attempts < $maxAttempts) {
            $random = self::generateRandomSolutions(1, $maxConsecutive);
            if (empty($random)) {
                $attempts++;
                continue;
            }
            $candidate = $random[0];
            $m1_m6 = $candidate['m1_m6'] ?? [];
            $m7 = (int)($candidate['m7'] ?? 0);

            $built = $service->buildSolutionFromNumbers($m1_m6, $m7, $maxConsecutive);
            if ($built === null) {
                $attempts++;
                continue;
            }
            if (self::getSolutionTotalProfit($built) < 0) {
                $attempts++;
                continue;
            }
            $key = self::buildSolutionKey($built['m1_m6'], $built['m7']);
            if (!isset($unique[$key])) {
                $unique[$key] = $built;
            }
            $attempts++;
        }

        return array_values($unique);
    }

    private static function getSolutionTotalProfit(array $solution): float
    {
        if (array_key_exists('total_profit', $solution)) {
            return (float)$solution['total_profit'];
        }
        if (array_key_exists('profit', $solution)) {
            return (float)$solution['profit'];
        }
        return 0.0;
    }

    private static function filterSolutionsByNonNegative(array $solutions): array
    {
        if (empty($solutions)) {
            return [];
        }

        $filtered = [];
        foreach ($solutions as $solution) {
            if (self::getSolutionTotalProfit($solution) < 0) {
                continue;
            }
            $filtered[] = $solution;
        }
        return $filtered;
    }

    private static function pickBestNonNegativeSolution(array $solutions): ?array
    {
        if (empty($solutions)) {
            return null;
        }
        $filtered = self::filterSolutionsByNonNegative($solutions);
        if (empty($filtered)) {
            return null;
        }
        $sorted = self::sortSolutions($filtered, 'profit_rate');
        return $sorted[0] ?? null;
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

    private static function normalizeMaxConsecutive($maxConsecutive): ?int
    {
        if ($maxConsecutive === null || $maxConsecutive === '') {
            return 2;
        }
        $maxConsecutive = (int)$maxConsecutive;
        if ($maxConsecutive <= 0) {
            return null;
        }
        if ($maxConsecutive > 7) {
            return 7;
        }
        return $maxConsecutive;
    }

    private static function pickBestSolutionWithMaxConsecutive(array $solutions, int $maxConsecutive): ?array
    {
        if ($maxConsecutive <= 0 || empty($solutions)) {
            return null;
        }

        $candidates = [];
        foreach ($solutions as $solution) {
            $m1_m6 = $solution['m1_m6'] ?? null;
            if (!is_array($m1_m6)) {
                continue;
            }
            $m1_m6 = array_values(array_unique(array_map('intval', $m1_m6)));
            if (count($m1_m6) !== 6) {
                continue;
            }
            $comboNumbers = $m1_m6;
            $m7 = $solution['m7'] ?? null;
            if ($m7 !== null) {
                $comboNumbers[] = (int)$m7;
            }
            $maxSeq = self::getMaxConsecutive($comboNumbers);
            if ($maxSeq > $maxConsecutive) {
                continue;
            }
            $diversityScore = self::calculateDiversityScore($m1_m6, $maxSeq);
            $solution['m1_m6'] = $m1_m6;
            $candidates[] = [
                'solution' => $solution,
                'max_seq' => $maxSeq,
                'diversity_score' => $diversityScore,
            ];
        }

        if (empty($candidates)) {
            return null;
        }

        usort($candidates, function ($a, $b) {
            $rateA = (float)($a['solution']['profit_rate'] ?? 0);
            $rateB = (float)($b['solution']['profit_rate'] ?? 0);
            if ($rateA !== $rateB) {
                return $rateB <=> $rateA;
            }
            $seqA = (int)$a['max_seq'];
            $seqB = (int)$b['max_seq'];
            if ($seqA !== $seqB) {
                return $seqA <=> $seqB;
            }
            $divA = (int)$a['diversity_score'];
            $divB = (int)$b['diversity_score'];
            if ($divA !== $divB) {
                return $divB <=> $divA;
            }
            $profitA = (float)($a['solution']['total_profit'] ?? 0);
            $profitB = (float)($b['solution']['total_profit'] ?? 0);
            return $profitB <=> $profitA;
        });

        return $candidates[0]['solution'];
    }

    private static function filterSolutionsByMaxConsecutive(array $solutions, int $maxConsecutive): array
    {
        if ($maxConsecutive <= 0 || empty($solutions)) {
            return $solutions;
        }

        $filtered = [];
        foreach ($solutions as $solution) {
            $m1_m6 = $solution['m1_m6'] ?? null;
            if (!is_array($m1_m6)) {
                continue;
            }
            $m1_m6 = array_values(array_unique(array_map('intval', $m1_m6)));
            if (count($m1_m6) !== 6) {
                continue;
            }
            sort($m1_m6);
            $comboNumbers = $m1_m6;
            $m7 = $solution['m7'] ?? null;
            if ($m7 !== null) {
                $comboNumbers[] = (int)$m7;
            }
            if (self::getMaxConsecutive($comboNumbers) > $maxConsecutive) {
                continue;
            }
            $solution['m1_m6'] = $m1_m6;
            $filtered[] = $solution;
        }

        return $filtered;
    }

    private static function expandSearchSpaceForMaxConsecutive(
        \app\common\service\OptimizedBestPlanService $service,
        int $maxConsecutive
    ): array {
        try {
            $reflection = new \ReflectionClass($service);
            $specialProp = $reflection->getProperty('specialCandidateLimit');
            $normalProp = $reflection->getProperty('normalPoolLimit');
            $comboProp = $reflection->getProperty('maxCombosPerSpecial');
            $specialProp->setAccessible(true);
            $normalProp->setAccessible(true);
            $comboProp->setAccessible(true);

            $baseSpecial = (int)$specialProp->getValue($service);
            $baseNormal = (int)$normalProp->getValue($service);
            $baseCombos = (int)$comboProp->getValue($service);

            $comboCap = 20000;
            $levels = [
                [
                    'special' => min(49, max($baseSpecial, (int)ceil($baseSpecial * 2))),
                    'normal' => min(49, max($baseNormal, (int)ceil($baseNormal * 1.5))),
                    'combos' => min($comboCap, max($baseCombos, $baseCombos * 2)),
                ],
                [
                    'special' => min(49, max($baseSpecial, (int)ceil($baseSpecial * 3))),
                    'normal' => min(49, max($baseNormal, (int)ceil($baseNormal * 2))),
                    'combos' => min($comboCap, max($baseCombos, $baseCombos * 4)),
                ],
                [
                    'special' => 49,
                    'normal' => 49,
                    'combos' => min($comboCap, max($baseCombos, $baseCombos * 6)),
                ],
            ];

            $lastResult = null;
            foreach ($levels as $levelIndex => $level) {
                $specialProp->setValue($service, $level['special']);
                $normalProp->setValue($service, $level['normal']);
                $comboProp->setValue($service, $level['combos']);

                $result = $service->findBest7Numbers(null, 5.0, true, $maxConsecutive);
                $lastResult = $result;
                $solutions = $result['all_solutions'] ?? $result['top_solutions'] ?? [];
                $solutions = self::filterSolutionsByNonNegative($solutions);
                $best = self::pickBestSolutionWithMaxConsecutive($solutions, $maxConsecutive);
                if ($best !== null) {
                    return [
                        'best_solution' => $best,
                        'result' => $result,
                        'level' => $levelIndex + 1,
                    ];
                }
            }

            return [
                'best_solution' => null,
                'result' => $lastResult,
                'level' => count($levels),
            ];
        } catch (\ReflectionException $e) {
            return [
                'best_solution' => null,
                'result' => null,
                'level' => 0,
            ];
        }
    }

    private static function findBestSolutionBySampling(
        \app\common\service\OptimizedBestPlanService $service,
        int $maxConsecutive,
        int $attempts = 3
    ): ?array {
        for ($i = 0; $i < $attempts; $i++) {
            $sample = $service->findByProfitRange(null, null, true, $maxConsecutive);
            $solutions = $sample['all_solutions'] ?? ($sample['matched_solutions'] ?? []);
            $solutions = self::filterSolutionsByNonNegative($solutions);
            $best = self::pickBestSolutionWithMaxConsecutive($solutions, $maxConsecutive);
            if ($best !== null) {
                return $best;
            }
        }
        return null;
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
     * @param string $plateCode 盘口代码（如 A、B、C）
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

            // 使用增强版算法。
            $service = new \app\common\service\EnhancedBestPlanService($gid, $qishu, $year, $plateCode);

            if ($service->getBetCount() === 0) {
                self::setError('该期暂无投注数据');
                return false;
            }

            // 使用增强版方法查找接近目标利润率的方案。
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

        // 解析 JSON 字段。
        $record['number_details'] = json_decode($record['number_details'], true);

        // 确保是数组。
        if (!is_array($record['number_details'])) {
            $record['number_details'] = [];
        }

        // 按利润排序。
        if (!empty($record['number_details'])) {
            usort($record['number_details'], function($a, $b) {
                $profitA = $a['profit'] ?? 0;
                $profitB = $b['profit'] ?? 0;
                return $profitB <=> $profitA;
            });
        }

        // 添加风险等级文本。
        foreach ($record['number_details'] as &$item) {
            $item['risk_level_text'] = BestPlanService::getRiskLevelText($item['risk_level'] ?? 0);
        }

        return $record;
    }

    /**
     * 获取当前可分析的期号
     *
     * @param int $gid 游戏ID
     * @param string $plateCode 盘口代码（如 A、B、C）
     * @return array|null
     */
    public static function getCurrentQishu(int $gid, string $plateCode = 'am'): ?array
    {
        trace("[getCurrentQishu] 查询参数: gid=$gid, plateCode=$plateCode", 'info');

        // 优先查询投注中的期号 (status=2)。
        $issue = Db::table('la_lottery_issue')
            ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
            ->where('game_id', $gid)
            ->where('plate_code', $plateCode)
            ->where('status', 2)  // 2=投注中
            ->order('draw_time', 'asc')
            ->find();

        // 如果没有投注中的期号，查询待开盘的期号 (status=1)。
        if (!$issue) {
            $issue = Db::table('la_lottery_issue')
                ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)
                ->where('status', 1)  // 1=待开盘
                ->order('draw_time', 'asc')
                ->find();
        }

        // 如果还没有，查询最新的已开奖期号 (status=3)，用于展示开奖结果。
        if (!$issue) {
            $issue = Db::table('la_lottery_issue')
                ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)
                ->where('status', 3)  // 3=已开奖
                ->order('draw_time', 'desc')
                ->find();
        }

        if (!$issue) {
            trace("[getCurrentQishu] 未找到可用期号", 'warning');
            return null;
        }

        trace("查询到期号数据: " . json_encode($issue, JSON_UNESCAPED_UNICODE), 'info');

        $resultValue = $issue['result'] ?? null;
        trace("result 字段值: [" . var_export($resultValue, true) . "] 类型: " . gettype($resultValue), 'info');

        // 始终包含所有字段，避免前端 undefined。
        $result = [
            'qishu' => $issue['issue'],
            'plate_code' => $issue['plate_code'],
            'opentime' => $issue['open_time'] ? date('Y-m-d H:i:s', $issue['open_time']) : '',
            'closetime' => $issue['close_time'] ? date('Y-m-d H:i:s', $issue['close_time']) : '',
            'kjtime' => $issue['draw_time'] ? date('Y-m-d H:i:s', $issue['draw_time']) : '',
            'status' => (int)$issue['status'],
            'is_opened' => ($issue['status'] == 3 && !empty($resultValue)),
            'draw_numbers' => [],
            'draw_numbers_text' => '',
        ];

        // 如果 result 字段有值，解析开奖号码。
        if (!empty($resultValue) && is_string($resultValue)) {
            trace("开奖号码原始数据: " . $resultValue, 'info');
            $result['draw_numbers'] = explode(',', $resultValue);
            $result['draw_numbers_text'] = $resultValue;
        } else {
            trace("No result value or non-string result; returning empty draw numbers.", 'warning');
        }

        trace("最终返回数据: " . json_encode($result, JSON_UNESCAPED_UNICODE), 'info');

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
            // 查找记录（使用新表 la_best_plan_history）。
            $record = Db::table('la_best_plan_history')
                ->where('gid', $gid)
                ->where('qishu', $qishu)
                ->find();

            if (!$record) {
                self::setError('Record not found.');
                return false;
            }

            // 从 JSON 中解析该号码的预测利润。
            $details = json_decode($record['number_details'], true);
            $actualProfit = 0;

            foreach ($details as $item) {
                if ($item['number'] == $actualNumber) {
                    $actualProfit = $item['profit'];
                    break;
                }
            }

            // 更新记录。
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
     * 获取号码投注分布（特码/正码/平码）
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
            ->whereRaw("(pm.name LIKE '%特码%' OR pm.name LIKE '%特碼%' OR pm.name LIKE '%正码%' OR pm.name LIKE '%正碼%' OR pm.name LIKE '%平码%' OR pm.name LIKE '%平碼%')")
            ->group('br.bet_content')
            ->order('total_amount', 'desc')
            ->select()
            ->toArray();

        return $distribution;
    }

    /**
     * 锁定开奖计划（使用最佳方案）
     */
    public static function executeDrawing(
        int $gid,
        string $qishu,
        string $plateCode,
        array $bestNumbers,
        int $year,
        int $operatorId = 0,
        bool $negativeConfirmed = false,
        bool $wipeoutConfirmed = false
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
                self::setError('期号不存在或盘口不匹配，请检查参数');
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

            $now = time();
            $closeTime = self::normalizeTimestamp($issue['close_time'] ?? 0);
            $drawTime = self::normalizeTimestamp($issue['draw_time'] ?? 0);
            if ($closeTime > 0 && $now < $closeTime) {
                Db::rollback();
                self::setError('当前期号尚未封盘，不能选择开奖计划');
                return false;
            }
            $numbers = array_values(array_map('intval', $bestNumbers));
            try {
                $numbers = DrawPlanEvaluationService::normalizeNumbers($numbers);
            } catch (\InvalidArgumentException $e) {
                Db::rollback();
                self::setError($e->getMessage());
                return false;
            }

            $evaluation = DrawPlanEvaluationService::evaluateIssue($gid, $qishu, $plateCode, $numbers, $year);
            $isNegativePlan = (float)($evaluation['expected_profit'] ?? 0) < 0;
            $wipeoutType = self::getWipeoutPlanType($evaluation);
            $isWipeoutPlan = $wipeoutType !== '';
            if ($isNegativePlan && !$negativeConfirmed) {
                Db::rollback();
                self::setError('负盈利方案必须二次确认后才能选择');
                return false;
            }
            if ($isWipeoutPlan && !$wipeoutConfirmed) {
                Db::rollback();
                self::setError('通杀/近似通杀方案必须二次确认后才能选择');
                return false;
            }

            if ($drawTime > 0 && $now < $drawTime) {
                // Row lock is already held by the FOR UPDATE select above.
                Db::table('la_lottery_issue')
                    ->where('id', $issue['id'])
                    ->update([
                        'planned_result' => implode(',', $numbers),
                        'planned_at' => time(),
                        'planned_source' => 1,
                        'planned_operator_id' => max(0, (int)$operatorId),
                        'updated_at' => time(),
                    ]);

                $result = [
                    'issue' => $qishu,
                    'plate_code' => $plateCode,
                    'numbers' => $numbers,
                    'win_count' => $evaluation['win_count'],
                    'lose_count' => $evaluation['lose_count'],
                    'draw_count' => $evaluation['draw_count'],
                    'total_orders' => $evaluation['total_orders'],
                    'total_bet_amount' => $evaluation['total_bet_amount'],
                    'expected_payout' => $evaluation['expected_payout'],
                    'expected_profit' => $evaluation['expected_profit'],
                    'expected_profit_rate' => $evaluation['expected_profit_rate'],
                    'planned_at' => $now,
                    'plan_status' => 'locked',
                    'is_negative_plan' => $isNegativePlan,
                    'negative_confirmed' => $isNegativePlan ? $negativeConfirmed : false,
                    'is_wipeout_plan' => $isWipeoutPlan,
                    'wipeout_type' => $wipeoutType,
                    'wipeout_confirmed' => $isWipeoutPlan ? $wipeoutConfirmed : false,
                ];

                if ($isNegativePlan) {
                    self::writeNegativePlanOperationLog($operatorId, $result);
                }
                if ($isWipeoutPlan) {
                    self::writeWipeoutPlanOperationLog($operatorId, $result);
                }

                Db::commit();
                return $result;
            }

            $result = self::publishAndSettleIssue($issue, $qishu, $plateCode, $numbers, $evaluation, $operatorId);
            $result['is_negative_plan'] = $isNegativePlan;
            $result['negative_confirmed'] = $isNegativePlan ? $negativeConfirmed : false;
            $result['is_wipeout_plan'] = $isWipeoutPlan;
            $result['wipeout_type'] = $wipeoutType;
            $result['wipeout_confirmed'] = $isWipeoutPlan ? $wipeoutConfirmed : false;
            if ($isNegativePlan) {
                self::writeNegativePlanOperationLog($operatorId, $result);
            }
            if ($isWipeoutPlan) {
                self::writeWipeoutPlanOperationLog($operatorId, $result);
            }
            Db::commit();
            return $result;

        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function revokeDrawingPlan(
        int $gid,
        string $qishu,
        string $plateCode,
        int $operatorId = 0
    ) {
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
                self::setError('期号不存在或盘口不匹配，请检查参数');
                return false;
            }
            if (!empty($issue['result']) || !empty($issue['is_settled'])) {
                Db::rollback();
                self::setError('本期已开奖或已结算，不能撤销计划');
                return false;
            }

            $drawTime = self::normalizeTimestamp($issue['draw_time'] ?? 0);
            if ($drawTime > 0 && time() >= $drawTime) {
                Db::rollback();
                self::setError('当前期号已到开奖时间，不能撤销计划');
                return false;
            }
            if (empty($issue['planned_result']) || (int)$issue['planned_source'] !== 1) {
                Db::rollback();
                self::setError('本期没有可撤销的人工计划');
                return false;
            }

            Db::table('la_lottery_issue')
                ->where('id', $issue['id'])
                ->update([
                    'planned_result' => '',
                    'planned_at' => 0,
                    'planned_source' => 0,
                    'planned_operator_id' => max(0, (int)$operatorId),
                    'updated_at' => time(),
                ]);

            Db::commit();
            return [
                'issue' => $qishu,
                'plate_code' => $plateCode,
                'plan_status' => 'revoked',
            ];
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 自定义开奖号码并立即开奖结算
     */
    public static function previewCustomDrawing(
        int $gid,
        string $qishu,
        string $plateCode,
        array $drawNumbers,
        int $year
    ) {
        try {
            $issue = Db::table('la_lottery_issue')
                ->where('game_id', $gid)
                ->where('issue', $qishu)
                ->where('plate_code', $plateCode)
                ->find();

            if (!$issue) {
                self::setError('期号不存在或盘口不匹配，请检查参数');
                return false;
            }

            if (!empty($issue['result']) || (int)$issue['status'] === 3) {
                self::setError('本期已开奖，不能重复开奖');
                return false;
            }

            if (!empty($issue['is_settled'])) {
                self::setError('本期已结算，不能重复开奖');
                return false;
            }

            $closeTime = self::normalizeTimestamp($issue['close_time'] ?? 0);
            if ($closeTime > 0 && time() < $closeTime) {
                self::setError('当前期号尚未封盘，不能预估自定义开奖');
                return false;
            }
            $drawTime = self::normalizeTimestamp($issue['draw_time'] ?? 0);
            if ($drawTime > 0 && time() < $drawTime) {
                self::setError('未到开奖时间，只能先选择并锁定开奖方案');
                return false;
            }

            try {
                $numbers = DrawPlanEvaluationService::normalizeNumbers($drawNumbers);
            } catch (\InvalidArgumentException $e) {
                self::setError($e->getMessage());
                return false;
            }

            $evaluation = DrawPlanEvaluationService::evaluateIssue($gid, $qishu, $plateCode, $numbers, $year);
            $isNegativePlan = (float)($evaluation['expected_profit'] ?? 0) < 0;
            $wipeoutType = self::getWipeoutPlanType($evaluation);

            return [
                'issue' => $qishu,
                'plate_code' => $plateCode,
                'numbers' => $numbers,
                'draw_numbers' => $numbers,
                'win_count' => $evaluation['win_count'],
                'lose_count' => $evaluation['lose_count'],
                'draw_count' => $evaluation['draw_count'],
                'total_orders' => $evaluation['total_orders'],
                'total_bet_amount' => $evaluation['total_bet_amount'],
                'expected_payout' => $evaluation['expected_payout'],
                'expected_profit' => $evaluation['expected_profit'],
                'expected_profit_rate' => $evaluation['expected_profit_rate'],
                'total_payout' => $evaluation['expected_payout'],
                'total_win_amount' => $evaluation['expected_payout'],
                'platform_profit' => $evaluation['expected_profit'],
                'is_negative_plan' => $isNegativePlan,
                'is_wipeout_plan' => $wipeoutType !== '',
                'wipeout_type' => $wipeoutType,
                'plan_status' => 'preview',
            ];
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function customDrawing(
        int $gid,
        string $qishu,
        string $plateCode,
        array $drawNumbers,
        int $year,
        int $operatorId = 0,
        bool $negativeConfirmed = false,
        bool $wipeoutConfirmed = false
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
                self::setError('期号不存在或盘口不匹配，请检查参数');
                return false;
            }

            if (!empty($issue['result']) || (int)$issue['status'] === 3) {
                Db::rollback();
                self::setError('本期已开奖，不能重复开奖');
                return false;
            }

            if (!empty($issue['is_settled'])) {
                Db::rollback();
                self::setError('本期已结算，不能重复开奖');
                return false;
            }

            $closeTime = is_numeric($issue['close_time'] ?? 0)
                ? (int)$issue['close_time']
                : (int)strtotime((string)($issue['close_time'] ?? ''));
            if ($closeTime > 0 && time() < $closeTime) {
                Db::rollback();
                self::setError('当前期号尚未封盘，不能自定义开奖');
                return false;
            }
            $drawTime = self::normalizeTimestamp($issue['draw_time'] ?? 0);
            if ($drawTime > 0 && time() < $drawTime) {
                Db::rollback();
                self::setError('未到开奖时间，只能先选择并锁定开奖方案');
                return false;
            }

            try {
                $numbers = DrawPlanEvaluationService::normalizeNumbers($drawNumbers);
            } catch (\InvalidArgumentException $e) {
                Db::rollback();
                self::setError($e->getMessage());
                return false;
            }

            $evaluation = DrawPlanEvaluationService::evaluateIssue($gid, $qishu, $plateCode, $numbers, $year);
            $isNegativePlan = (float)($evaluation['expected_profit'] ?? 0) < 0;
            $wipeoutType = self::getWipeoutPlanType($evaluation);
            $isWipeoutPlan = $wipeoutType !== '';
            if ($isNegativePlan && !$negativeConfirmed) {
                Db::rollback();
                self::setError('负盈利自定义开奖必须二次确认后才能提交');
                return false;
            }
            if ($isWipeoutPlan && !$wipeoutConfirmed) {
                Db::rollback();
                self::setError('通杀/近似通杀自定义开奖必须二次确认后才能提交');
                return false;
            }

            $result = self::publishAndSettleIssue($issue, $qishu, $plateCode, $numbers, $evaluation, $operatorId);
            $result['is_negative_plan'] = $isNegativePlan;
            $result['negative_confirmed'] = $isNegativePlan ? $negativeConfirmed : false;
            $result['is_wipeout_plan'] = $isWipeoutPlan;
            $result['wipeout_type'] = $wipeoutType;
            $result['wipeout_confirmed'] = $isWipeoutPlan ? $wipeoutConfirmed : false;
            if ($isNegativePlan) {
                $result['selection_source'] = 'custom_drawing';
                self::writeNegativePlanOperationLog($operatorId, $result);
            }
            if ($isWipeoutPlan) {
                $result['selection_source'] = 'custom_drawing';
                self::writeWipeoutPlanOperationLog($operatorId, $result);
            }

            Db::commit();
            return $result;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    private static function publishAndSettleIssue(
        array $issue,
        string $qishu,
        string $plateCode,
        array $numbers,
        array $evaluation,
        int $operatorId
    ): array {
        $now = time();
        Db::table('la_lottery_issue')
            ->where('id', $issue['id'])
            ->update([
                'result' => implode(',', $numbers),
                'planned_result' => implode(',', $numbers),
                'planned_at' => $now,
                'planned_source' => 1,
                'planned_operator_id' => max(0, (int)$operatorId),
                'status' => 3,
                'updated_at' => $now,
            ]);

        LotteryBetLogic::settleBetting((int)$issue['id'], $numbers);

        $pendingCount = Db::table('la_betting_record')
            ->where('issue_id', $issue['id'])
            ->where('status', 0)
            ->count();

        if ($pendingCount > 0) {
            throw new \Exception('仍有未结算订单，请检查用户账户或投注记录');
        }

        Db::table('la_lottery_issue')
            ->where('id', $issue['id'])
            ->update([
                'is_settled' => 1,
                'settled_at' => $now,
                'updated_at' => time(),
            ]);

        return [
            'issue' => $qishu,
            'plate_code' => $plateCode,
            'numbers' => $numbers,
            'draw_numbers' => $numbers,
            'win_count' => $evaluation['win_count'],
            'lose_count' => $evaluation['lose_count'],
            'draw_count' => $evaluation['draw_count'],
            'total_orders' => $evaluation['total_orders'],
            'total_bet_amount' => $evaluation['total_bet_amount'],
            'expected_payout' => $evaluation['expected_payout'],
            'expected_profit' => $evaluation['expected_profit'],
            'expected_profit_rate' => $evaluation['expected_profit_rate'],
            'total_payout' => $evaluation['expected_payout'],
            'total_win_amount' => $evaluation['expected_payout'],
            'platform_profit' => $evaluation['expected_profit'],
            'settled_at' => $now,
            'plan_status' => 'settled',
        ];
    }

    private static function getWipeoutPlanType(array $result): string
    {
        $totalBetAmount = (float)($result['total_bet_amount'] ?? $result['bet_amount'] ?? $result['total_bets'] ?? 0);
        if ($totalBetAmount <= 0) {
            return '';
        }

        $payout = $result['expected_payout']
            ?? $result['total_payout']
            ?? $result['total_win_amount']
            ?? $result['total_prize']
            ?? $result['prize_amount']
            ?? null;
        $profitRate = (float)($result['expected_profit_rate'] ?? $result['profit_rate'] ?? 0);

        if ($payout !== null && round((float)$payout, 2) <= 0.01) {
            return 'full';
        }
        if ($profitRate >= 99.0) {
            return 'near';
        }

        return '';
    }

    private static function getWipeoutPlanLabel(string $wipeoutType): string
    {
        return $wipeoutType === 'full' ? '通杀方案' : '近似通杀方案';
    }

    private static function writeNegativePlanOperationLog(int $operatorId, array $result): void
    {
        $admin = null;
        if ($operatorId > 0) {
            $admin = Db::table('la_admin')
                ->where('id', $operatorId)
                ->find();
        }

        $expectedProfit = round((float)($result['expected_profit'] ?? $result['platform_profit'] ?? 0), 2);
        $expectedLoss = round(abs(min(0, $expectedProfit)), 2);
        $profitRate = round((float)($result['expected_profit_rate'] ?? 0), 2);
        $numbers = array_values(array_map('intval', $result['numbers'] ?? $result['draw_numbers'] ?? []));

        Db::table('la_operation_log')->insert([
            'admin_id' => $operatorId,
            'admin_name' => $admin['name'] ?? '',
            'account' => $admin['account'] ?? '',
            'action' => '选择负盈利开奖计划',
            'type' => 'POST',
            'url' => request()->url(true),
            'params' => OperationLogContentService::encodeParams([
                'issue' => $result['issue'] ?? '',
                'plate_code' => $result['plate_code'] ?? '',
                'numbers' => $numbers,
                'plan_status' => $result['plan_status'] ?? '',
                'selection_source' => $result['selection_source'] ?? 'plan_selection',
                'negative_confirmed' => (bool)($result['negative_confirmed'] ?? false),
                'expected_loss' => $expectedLoss,
                'expected_profit' => $expectedProfit,
                'expected_profit_rate' => $profitRate,
                'expected_payout' => round((float)($result['expected_payout'] ?? $result['total_payout'] ?? 0), 2),
                'total_bet_amount' => round((float)($result['total_bet_amount'] ?? 0), 2),
                'total_orders' => (int)($result['total_orders'] ?? 0),
            ]),
            'result' => OperationLogContentService::encodeResult([
                'message' => '负盈利方案已二次确认',
                'expected_loss' => $expectedLoss,
                'expected_profit_rate' => $profitRate,
                'plan_status' => $result['plan_status'] ?? '',
            ]),
            'ip' => request()->ip(),
            'create_time' => time(),
        ]);
    }

    private static function writeWipeoutPlanOperationLog(int $operatorId, array $result): void
    {
        $wipeoutType = (string)($result['wipeout_type'] ?? self::getWipeoutPlanType($result));
        if ($wipeoutType === '') {
            return;
        }

        $admin = null;
        if ($operatorId > 0) {
            $admin = Db::table('la_admin')
                ->where('id', $operatorId)
                ->find();
        }

        $label = self::getWipeoutPlanLabel($wipeoutType);
        $expectedProfit = round((float)($result['expected_profit'] ?? $result['platform_profit'] ?? 0), 2);
        $profitRate = round((float)($result['expected_profit_rate'] ?? 0), 2);
        $expectedPayout = round((float)($result['expected_payout'] ?? $result['total_payout'] ?? 0), 2);
        $numbers = array_values(array_map('intval', $result['numbers'] ?? $result['draw_numbers'] ?? []));

        Db::table('la_operation_log')->insert([
            'admin_id' => $operatorId,
            'admin_name' => $admin['name'] ?? '',
            'account' => $admin['account'] ?? '',
            'action' => '选择通杀/近似通杀开奖计划',
            'type' => 'POST',
            'url' => request()->url(true),
            'params' => OperationLogContentService::encodeParams([
                'issue' => $result['issue'] ?? '',
                'plate_code' => $result['plate_code'] ?? '',
                'numbers' => $numbers,
                'plan_status' => $result['plan_status'] ?? '',
                'selection_source' => $result['selection_source'] ?? 'plan_selection',
                'wipeout_type' => $wipeoutType,
                'wipeout_label' => $label,
                'wipeout_confirmed' => (bool)($result['wipeout_confirmed'] ?? false),
                'expected_profit' => $expectedProfit,
                'expected_profit_rate' => $profitRate,
                'expected_payout' => $expectedPayout,
                'total_bet_amount' => round((float)($result['total_bet_amount'] ?? 0), 2),
                'total_orders' => (int)($result['total_orders'] ?? 0),
            ]),
            'result' => OperationLogContentService::encodeResult([
                'message' => $label . '已二次确认',
                'wipeout_type' => $wipeoutType,
                'expected_profit_rate' => $profitRate,
                'expected_payout' => $expectedPayout,
                'plan_status' => $result['plan_status'] ?? '',
            ]),
            'ip' => request()->ip(),
            'create_time' => time(),
        ]);
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
        $extendedResult = LotteryPlayRuleService::determineResult(
            $methodName,
            $methodCode,
            $betContent,
            $allNumbers,
            $year,
            $betType
        );
        if ($extendedResult !== null) {
            return $extendedResult;
        }

        $missRule = self::getNumberMissRule($methodName, $methodCode);
        if ($missRule !== null) {
            $betNumbers = self::parseNumberSelections($betContent);
            if (count($betNumbers) !== $missRule['select_count']) {
                return 'lose';
            }
            $hitCount = count(array_intersect($betNumbers, $allNumbers));
            return self::resolveResult($hitCount === 0, $betType);
        }

        $comboRule = self::getNumberComboRule($methodName, $methodCode);
        if ($comboRule !== null) {
            $betNumbers = self::parseNumberSelections($betContent);
            if (count($betNumbers) !== $comboRule['select_count']) {
                return 'lose';
            }
            $hitCount = count(array_intersect($betNumbers, $m1_m6));
            return self::resolveResult($hitCount >= $comboRule['hit_count'], $betType);
        }

        if ($methodCode === 'tema' || self::containsKeyword($methodName, ['特码', '特碼'])) {
            $betNumbers = array_map('intval', $betItems);
            $hit = in_array($m7, $betNumbers, true);
            return self::resolveResult($hit, $betType);
        }

        if (
            $methodCode === 'zhengma'
            || self::containsKeyword($methodName, ['正码', '正碼', '平码', '平碼'])
        ) {
            $betNumbers = array_map('intval', $betItems);
            $hit = !empty(array_intersect($betNumbers, $allNumbers));
            return self::resolveResult($hit, $betType);
        }

        if ($methodCode === 'texiao' || self::containsKeyword($methodName, ['特肖'])) {
            $betZodiacs = ZodiacService::normalizeZodiacSelections($betItems, $year);
            if (empty($betZodiacs)) {
                return 'lose';
            }

            // 支持跨年份生肖：第 7 个号码可以匹配任意年份的同生肖。
            $allPossibleZodiacs = self::getAllPossibleZodiacs($m7);
            $hit = !empty(array_intersect($betZodiacs, $allPossibleZodiacs));
            return self::resolveResult($hit, $betType);
        }

        if ($methodCode === 'pingxiao' || self::containsKeyword($methodName, ['平肖'])) {
            $betZodiacs = ZodiacService::normalizeZodiacSelections($betItems, $year);
            if (empty($betZodiacs)) {
                return 'lose';
            }
            $drawnZodiacs = ZodiacService::convertNumbersToZodiacsWithYear($allNumbers, $year);
            $hit = !empty(array_intersect($betZodiacs, $drawnZodiacs));
            return self::resolveResult($hit, $betType);
        }

        if (
            in_array($methodCode, ['sanxiao', 'sixiao', 'wuxiao', 'liuxiao'], true)
            || self::containsKeyword($methodName, ['三肖', '四肖', '五肖', '六肖'])
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

    private static function normalizeTimestamp($value): int
    {
        if (is_numeric($value)) {
            return (int)$value;
        }
        $timestamp = strtotime((string)$value);
        return $timestamp !== false ? (int)$timestamp : 0;
    }

    /**
     * 获取号码在所有年份中可能对应的生肖。
     *
     * 这允许特肖投注在跨年份范围内生效，只要第 7 个号码的生肖在任意年份中
     * 匹配投注的生肖即可。
     *
     * @param int $number 号码（1-49）
     * @return array 去重后的生肖列表
     */
    private static function getAllPossibleZodiacs(int $number): array
    {
        if ($number < 1 || $number > 49) {
            return [];
        }

        static $cache = [];

        // 优先检查缓存。
        if (isset($cache[$number])) {
            return $cache[$number];
        }

        $zodiacs = [];

        // 扫描一个完整的生肖轮转周期（12年）。
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

        // 缓存结果。
        $cache[$number] = $zodiacs;

        return $zodiacs;
    }

    private static function getNumberComboRule(string $methodName, string $methodCode): ?array
    {
        $rules = [
            'erzhonger' => ['select_count' => 2, 'hit_count' => 2],
            'sanzhonger' => ['select_count' => 3, 'hit_count' => 2],
            'sanzhongsan' => ['select_count' => 3, 'hit_count' => 3],
        ];

        if (isset($rules[$methodCode])) {
            return $rules[$methodCode];
        }

        if (self::containsKeyword($methodName, ['二中二'])) {
            return $rules['erzhonger'];
        }
        if (self::containsKeyword($methodName, ['三中二'])) {
            return $rules['sanzhonger'];
        }
        if (self::containsKeyword($methodName, ['三中三'])) {
            return $rules['sanzhongsan'];
        }

        return null;
    }

    private static function getNumberMissRule(string $methodName, string $methodCode): ?array
    {
        $rules = [
            'wubuzhong' => ['select_count' => 5, 'hit_count' => 0],
            'liubuzhong' => ['select_count' => 6, 'hit_count' => 0],
            'qibuzhong' => ['select_count' => 7, 'hit_count' => 0],
            'babuzhong' => ['select_count' => 8, 'hit_count' => 0],
            'jiubuzhong' => ['select_count' => 9, 'hit_count' => 0],
            'shibuzhong' => ['select_count' => 10, 'hit_count' => 0],
        ];

        if (isset($rules[$methodCode])) {
            return $rules[$methodCode];
        }

        $nameMap = [
            '五不中' => 'wubuzhong',
            '六不中' => 'liubuzhong',
            '七不中' => 'qibuzhong',
            '八不中' => 'babuzhong',
            '九不中' => 'jiubuzhong',
            '十不中' => 'shibuzhong',
        ];

        foreach ($nameMap as $keyword => $ruleKey) {
            if (self::containsKeyword($methodName, [$keyword])) {
                return $rules[$ruleKey];
            }
        }

        return null;
    }

    private static function parseNumberSelections(string $content): array
    {
        preg_match_all('/\d{1,2}/', $content, $matches);
        $numbers = array_map('intval', $matches[0] ?? []);
        $numbers = array_filter($numbers, function ($number) {
            return $number >= 1 && $number <= 49;
        });
        return array_values(array_unique($numbers));
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
     * @return array|false 返回新期号信息或 false
     */
    public static function manualCreateNewIssue(int $gid, string $plateCode = 'am')
    {
        try {
            // 调用 LotteryIssueService 创建新期号。
            $newIssue = \app\common\service\LotteryIssueService::getOrCreateCurrentIssue($gid, $plateCode);

            if (!$newIssue) {
                self::$error = 'Failed to create new issue.';
                trace("手动创建新期号失败: gid=$gid, plateCode=$plateCode", 'error');
                return false;
            }

            trace("手动创建新期号成功: " . json_encode($newIssue, JSON_UNESCAPED_UNICODE), 'info');

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
            trace("手动创建新期号异常: " . $e->getMessage(), 'error');
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
            0 => 'pending',
            1 => 'betting',
            2 => 'closed',
            3 => 'drawn',
            4 => 'settled',
            5 => 'cancelled',
        ];

        return $statusMap[$status] ?? 'unknown';
    }
}
