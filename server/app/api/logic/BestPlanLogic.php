<?php
declare(strict_types=1);

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\service\BestPlanService;
use app\common\service\ZodiacService;
use app\common\service\ZodiacYearService;
use think\facade\Db;

/**
 * 鏈€浣虫帶鐩樿鍒?- 涓氬姟閫昏緫绫?
 *
 * @package app\api\logic
 * @author Claude AI
 * @date 2025-12-01
 */
class BestPlanLogic extends BaseLogic
{
    private const MAX_TOP_SOLUTION_LIMIT = 1000;

    /**
     * 鎵ц鍒嗘瀽骞朵繚瀛樼粨鏋?
     *
     * @param int $gid 娓告垙ID
     * @param string $qishu 鏈熷彿
     * @param string $plateCode 鐩樺彛浠ｇ爜
     * @param int|null $year 骞翠唤锛堝彲閫夛紝榛樿褰撳墠骞翠唤锛?
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

            $result = $service->findBest7Numbers(null, 5.0, true, $maxConsecutive);
            if ($maxConsecutive !== null) {
                $filteredAll = self::filterSolutionsByMaxConsecutive(
                    $result['all_solutions'] ?? $result['top_solutions'],
                    $maxConsecutive
                );
                $filteredTop = self::filterSolutionsByMaxConsecutive($result['top_solutions'], $maxConsecutive);
                if (array_key_exists('all_solutions', $result)) {
                    $result['all_solutions'] = $filteredAll;
                }
                $result['top_solutions'] = $filteredTop;
            }
            $rateBuckets = self::buildRateBuckets($result['all_solutions'] ?? $result['top_solutions'], $year);
            $bestSolution = $result['best_solution'];

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
     * 瀹炴椂璁＄畻锛堜笉淇濆瓨鍒版暟鎹簱锛?
     *
     * @param int $gid 娓告垙ID
     * @param string $qishu 鏈熷彿
     * @param string $plateCode 鐩樺彛浠ｇ爜锛堝A銆丅銆丆锛?
     * @param int|null $year 骞翠唤
     * @param float|null $targetRate 鐩爣鍒╂鼎鐜囷紙濡?0琛ㄧず10%锛宯ull琛ㄧず鏈€澶у寲鍒╂鼎锛?
     * @param float $tolerance 璇樊鑼冨洿锛堥粯璁?%锛?
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
        ?int $maxConsecutive = null
    ) {
        try {
            $year = $year ?? (int)date('Y');
            $sortBy = is_string($sortBy) ? trim($sortBy) : null;
            if ($sortBy === '') {
                $sortBy = null;
            }
            $limit = self::normalizeSolutionLimit($limit);
            $maxConsecutive = self::normalizeMaxConsecutive($maxConsecutive);

            // 浣跨敤浼樺寲鐗堢畻娉?缁熶竴"涓?涓?涓嶄腑"鎶曟敞)
            $service = new \app\common\service\OptimizedBestPlanService($gid, $qishu, $year, $plateCode);

            // 濡傛灉娌℃湁鎶曟敞鏁版嵁,鐢熸垚鑷冲皯20涓殢鏈烘柟妗?
                        if ($service->getBetCount() === 0) {
                $randomLimit = $limit ?? 20;
                if ($randomLimit > self::MAX_TOP_SOLUTION_LIMIT) {
                    $randomLimit = self::MAX_TOP_SOLUTION_LIMIT;
                }
                $randomSolutions = self::generateRandomSolutions($randomLimit, $maxConsecutive);

                if (empty($randomSolutions)) {
                    if ($maxConsecutive !== null) {
                        $randomSolutions = self::generateRandomSolutions(1, $maxConsecutive);
                        if (empty($randomSolutions)) {
                            self::setError('Unable to generate random solution within max consecutive constraint.');
                            return false;
                        }
                        $bestSolution = $randomSolutions[0];
                    } else {
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
                    }
                } else {
                    $bestSolution = $randomSolutions[0];
                }

                $sortKey = self::normalizeSortBy($sortBy);
                $randomSolutions = self::sortSolutions($randomSolutions, $sortKey);
                $bestSolution = $randomSolutions[0];
                if ($maxConsecutive !== null) {
                    $bestSolutionWithConstraint = self::pickBestSolutionWithMaxConsecutive($randomSolutions, $maxConsecutive);
                    if ($bestSolutionWithConstraint !== null) {
                        $bestSolution = $bestSolutionWithConstraint;
                    }
                }
                $selectedNumbers = array_merge($bestSolution['m1_m6'], [$bestSolution['m7']]);

                $rateBuckets = self::buildRateBuckets($randomSolutions, $year, $maxConsecutive);

                return [
                    'summary' => [
                        'total_bets' => 0,
                        'total_orders' => 0,
                        'best_numbers' => $selectedNumbers,
                        'best_m7' => $bestSolution['m7'],
                        'best_m1_m6' => $bestSolution['m1_m6'],
                        'best_profit' => 0,
                        'best_profit_rate' => 100.0,
                        'has_bets' => false,
                    ],
                    'best_solution' => $bestSolution,
                    'top_solutions' => $randomSolutions,
                    'rate_buckets' => $rateBuckets,
                    'risk_assessment' => [
                        'risk_level' => 'safe',
                        'description' => 'No bets: no risk.',
                    ],
                    'recommendations' => ['No bets: random numbers generated for draw.'],
                    'strategy_used' => 'random',
                    'message' => 'No bets: random draw generated.',
                ];
            }
            $result = $service->findBest7Numbers(null, 5.0, true, $maxConsecutive);

            $bucketSource = $result['all_solutions'] ?? $result['top_solutions'];
            if ($maxConsecutive !== null) {
                $bucketSource = self::filterSolutionsByMaxConsecutive($bucketSource, $maxConsecutive);
                $bucketSource = self::fillSolutionsToMinimum($service, $bucketSource, 100, $maxConsecutive);
            }
            $rateBuckets = self::buildRateBuckets($bucketSource, $year, $maxConsecutive);

            // 鉁?濡傛灉鎸囧畾浜嗙洰鏍囧埄娑︾巼,浣跨敤鏅鸿兘鎵╁睍鎼滅储
            $searchResult = null;
            if ($targetRate !== null && !empty($result['top_solutions'])) {
                trace("馃幆 鐩爣鍒╂鼎鐜? {$targetRate}%, 鍒濆璇樊: 卤{$tolerance}%", 'info');
                trace("馃搳 鐢熸垚鏂规鏁伴噺: " . count($result['top_solutions']), 'info');

                // 绗竴娆″皾璇曪細浣跨敤褰撳墠鏂规搴撴悳绱?
                $searchResult = self::findTargetRateSolutionByExpansion(
                    $result['top_solutions'],
                    $targetRate,
                    $tolerance
                );

                // 濡傛灉鎵句笉鍒?鍒ゆ柇鏄惁闇€瑕佹墿灞曟悳绱㈢┖闂?
                $searchSpaceExpanded = false;
                if (!isset($searchResult['solution']) || $searchResult['solution'] === null) {
                    trace("No solution found after expansion; using best solution.", 'warning');

                    // 妫€鏌ュ綋鍓嶆柟妗堢殑瑕嗙洊鑼冨洿
                    $rates = array_column($result['top_solutions'], 'profit_rate');
                    $minRate = min($rates);
                    $maxRate = max($rates);
                    $coverageRange = $maxRate - $minRate;

                    trace("馃搱 褰撳墠瑕嗙洊鑼冨洿: [{$minRate}%, {$maxRate}%], 璺ㄥ害: {$coverageRange}%", 'debug');

                    // 濡傛灉瑕嗙洊鑼冨洿澶皬锛?50%锛夛紝鎵╁睍鎼滅储绌洪棿閲嶆柊鐢熸垚鏂规
                    if ($coverageRange < 50) {
                        trace("馃攳 瑕嗙洊鑼冨洿杩囧皬,姝ｅ湪鎵╁睍鎼滅储绌洪棿閲嶆柊鐢熸垚鏂规...", 'info');
                        $result = self::expandSearchSpaceAndFindBest(
                            $service,
                            $targetRate,
                            $tolerance,
                            $maxConsecutive
                        );
                        $searchSpaceExpanded = true;

                        // 鐢ㄦ柊鏂规閲嶈瘯鎼滅储
                        if (!empty($result['top_solutions'])) {
                            trace("鉁?鎵╁睍鍚庢柟妗堟暟: " . count($result['top_solutions']), 'info');
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

                    // 杈撳嚭鎼滅储杩囩▼鏃ュ織
                    trace("鉁?鎼滅储鎴愬姛!", 'info');
                    trace("   鎵惧埌鑼冨洿: [{$searchResult['range']['min']}%, {$searchResult['range']['max']}%]", 'info');
                    trace("   鎵╁睍绾у埆: {$searchResult['expansion_level']}", 'info');
                    trace("   绗﹀悎鏂规鏁? " . count($searchResult['all_matched']), 'info');
                    trace("   閫変腑鍒╂鼎鐜? {$bestSolution['profit_rate']}%", 'info');

                    // 杈撳嚭璇︾粏鐨勬悳绱㈣繃绋?
                    foreach ($searchResult['search_process'] as $step) {
                        if ($step['found_count'] > 0) {
                            trace("   Level {$step['level']}: range [{$step['range']['min']}%, {$step['range']['max']}%] - found {$step['found_count']} solutions", 'debug');
                        } else {
                            trace("   Level {$step['level']}: range [{$step['range']['min']}%, {$step['range']['max']}%] - found 0 solutions", 'debug');
                        }
                    }
                } else {
                    // 涓嶅簲璇ュ彂鐢燂紝鍥犱负鎼滅储浼氫竴鐩存墿灞曞埌 [10%-100%]
                    trace("No solution found after expansion; using best solution.", 'warning');
                    $bestSolution = $result['best_solution'];
                }
            } else {
                // 娌℃湁鎸囧畾鐩爣鍒╂鼎鐜?浣跨敤鏈€澶у埄娑︽柟妗?鏁扮粍鏈€鍚庝竴涓厓绱?
                $bestSolution = $result['best_solution'];
            }

            // 鏋勫缓鎽樿锛堜娇鐢ㄨ绠楃粨鏋滀腑鐨勬暟鎹級
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
                $bestSolutionWithConstraint = self::pickBestSolutionWithMaxConsecutive($mixCandidates, $maxConsecutive);
                if ($bestSolutionWithConstraint === null) {
                    $expanded = self::expandSearchSpaceForMaxConsecutive($service, $maxConsecutive);
                    $bestSolutionWithConstraint = $expanded['best_solution'] ?? null;
                }
                if ($bestSolutionWithConstraint === null) {
                    $bestSolutionWithConstraint = self::findBestSolutionBySampling($service, $maxConsecutive, 3);
                }
                if ($bestSolutionWithConstraint === null) {
                    self::setError('鏃犳硶鎵惧埌婊¤冻杩炵画鍙烽檺鍒剁殑鏂规');
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
                $rateBuckets = self::buildRateBuckets([$bestSolution], $year, $maxConsecutive);
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

            return [
                'summary' => $summary,
                'best_solution' => $bestSolution,
                'top_solutions' => $topSolutions,
                'rate_buckets' => $rateBuckets,
                'risk_assessment' => $result['risk_assessment'] ?? null,
                'recommendations' => $result['recommendations'] ?? [],
                'strategy_used' => $targetRate !== null ? 'target_rate' : 'balanced',  // 鏍囪浣跨敤鐨勭瓥鐣?
                'target_rate_config' => $targetRate !== null && $searchResult !== null ? [
                    'target' => $targetRate,
                    'tolerance' => $tolerance,
                    'achieved' => $bestSolution['profit_rate'] ?? 0,
                    'matched' => $targetMatched,
                    'search_space_expanded' => $searchSpaceExpanded,  // 鏄惁鎵╁睍浜嗘悳绱㈢┖闂?
                    // 鉁?鎼滅储杩囩▼璇﹁В
                    'search_result' => [
                        'expansion_level' => $searchResult['expansion_level'],
                        'found_range' => $searchResult['range'],
                        'matched_count' => count($searchResult['all_matched'] ?? []),
                        'initial_solution_count' => count($result['top_solutions'] ?? []),  // 鏂规鏁伴噺
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
     * 鎵╁睍鎼滅储绌洪棿閲嶆柊鐢熸垚鏂规
     *
     * 鍦烘櫙锛氬綋鍓嶆柟妗堝簱瑕嗙洊鑼冨洿澶皬锛屾棤娉曟弧瓒崇洰鏍囧埄娑︾巼瑕佹眰
     * 瑙ｅ喅鏂规锛氬姩鎬佸鍔犲€欓€夌壒鐮佹暟鍜屾瘡涓壒鐮佺殑缁勫悎鏁?
     *
     * @param \app\common\service\OptimizedBestPlanService $service
     * @param float $targetRate 鐩爣鍒╂鼎鐜?
     * @param float $tolerance 瀹瑰樊鑼冨洿
     * @return array 鎵╁睍鍚庣殑鏂规缁撴灉
     */
    private static function expandSearchSpaceAndFindBest(
        \app\common\service\OptimizedBestPlanService $service,
        float $targetRate,
        float $tolerance,
        ?int $maxConsecutive = null
    ): array {
        trace("馃殌 鍚姩鎵╁睍鎼滅储绌洪棿...", 'info');

        // 閫氳繃鍙嶅皠鎴栧姩鎬佽皟鐢ㄥ鍔犳悳绱㈠弬鏁?
        // 绗竴杞細3鍊嶆墿灞?
        $originalSpecialLimit = 20;     // 鍘熷: 20涓壒鐮?
        $originalComboLimit = 800;       // 鍘熷: 800涓粍鍚?鐗圭爜

        $expandedSpecial = $originalSpecialLimit * 2;      // 鎵╁睍鍒?40 涓壒鐮?
        $expandedCombo = $originalComboLimit * 2;          // 鎵╁睍鍒?1600 涓粍鍚?鐗圭爜

        trace("馃搷 鎵╁睍鍙傛暟锛氱壒鐮佸€欓€夋暟 {$originalSpecialLimit} 鈫?{$expandedSpecial}锛岀粍鍚堟暟 {$originalComboLimit} 鈫?{$expandedCombo}", 'debug');

        try {
            // 浣跨敤鍙嶅皠璁剧疆绉佹湁灞炴€э紙濡傛灉鏀寔锛?
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

            // 閲嶆柊璁＄畻鏈€浣虫柟妗?
            $result = $service->findBest7Numbers(null, 5.0, true, $maxConsecutive);

            trace("鉁?鎵╁睍鎼滅储瀹屾垚锛岀敓鎴愭柟妗堟暟: " . count($result['top_solutions']), 'info');

            // 妫€鏌ユ柊鐨勮鐩栬寖鍥?
            if (!empty($result['top_solutions'])) {
                $rates = array_column($result['top_solutions'], 'profit_rate');
                $newMin = min($rates);
                $newMax = max($rates);
                trace("馃搳 鎵╁睍鍚庤鐩栬寖鍥? [{$newMin}%, {$newMax}%], 璺ㄥ害: " . ($newMax - $newMin) . "%", 'debug');
            }

            return $result;
        } catch (\Exception $e) {
            trace("鉂?鎵╁睍鎼滅储绌洪棿澶辫触: " . $e->getMessage(), 'error');
            // 闄嶇骇澶勭悊锛氳繑鍥炲師濮嬬粨鏋?
            return $service->findBest7Numbers(null, 5.0, true, $maxConsecutive);
        }
    }

    /**
     * 鏅鸿兘鐩爣鍒╂鼎鐜囨悳绱?- 閫愭鎵╁睍鑼冨洿
     *
     * 绠楁硶閫昏緫锛?
     * 1. 棣栧厛鍦?[target - tolerance, target + tolerance] 鑼冨洿鍐呮悳绱?
     * 2. 濡傛灉鎵句笉鍒帮紝閫愭鎵╁睍鑼冨洿锛?
     *    - 鎵╁睍1鍊嶏細[target - 2*tolerance, target + 2*tolerance]
     *    - 鎵╁睍2鍊嶏細[target - 3*tolerance, target + 3*tolerance]
     *    - ...浠ユ绫绘帹锛岀洿鍒拌鐩栨暣涓?[10%, 100%] 鑼冨洿
     * 3. 鏈€缁堟寜鍒╂鼎鐜囦粠楂樺埌浣庤繑鍥炵鍚堟潯浠剁殑鏂规
     *
     * @param array $solutions 鎵€鏈夋柟妗堝垪琛紙宸叉寜鍒╂鼎鐜囨帓搴忥級
     * @param float $targetRate 鐩爣鍒╂鼎鐜囷紙濡?50%锛?
     * @param float $tolerance 鍒濆璇樊鑼冨洿锛堝 10%锛?
     * @return array ['solution' => 鏈€浣虫柟妗? 'range' => 鏌ユ壘鑼冨洿, 'expansion_level' => 鎵╁睍绾у埆]
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

        // 閫愭鎵╁睍鎼滅储鑼冨洿锛岀洿鍒版壘鍒版柟妗堟垨瑕嗙洊鏁翠釜鑼冨洿
        while (true) {
            // 璁＄畻褰撳墠鎼滅储鑼冨洿
            $rangeMin = max(10.0, $targetRate - $currentTolerance);
            $rangeMax = min(100.0, $targetRate + $currentTolerance);

            // 鍦ㄥ綋鍓嶈寖鍥村唴鎼滅储
            $matched = array_filter($solutions, function ($solution) use ($rangeMin, $rangeMax) {
                $rate = $solution['profit_rate'];
                return $rate >= $rangeMin && $rate <= $rangeMax;
            });

            // 璁板綍鎼滅储杩囩▼
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

            // 濡傛灉鎵惧埌鍖归厤鐨勬柟妗堬紝鎸夊埄娑︾巼闄嶅簭鎺掑垪骞惰繑鍥炵涓€涓?
            if (!empty($matched)) {
                // 鎸夊埄娑︾巼闄嶅簭鎺掑垪
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
                    'all_matched' => $matched,  // 杩斿洖鎵€鏈夊尮閰嶇殑鏂规
                    'search_process' => $searchProcess,
                ];
            }

            // 妫€鏌ユ槸鍚﹀凡缁忚鐩栨暣涓寖鍥?[10%, 100%]
            if ($rangeMin <= 10.0 && $rangeMax >= 100.0) {
                // 宸茶鐩栧叏鑼冨洿浣嗕粛鏈壘鍒帮紝杩斿洖鏁翠釜鍒楄〃鎸夐檷搴?
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

            // 鎵╁睍瀹瑰樊鍊间互杩涜涓嬩竴杞悳绱?
            $expansionLevel++;
            $currentTolerance = $tolerance * ($expansionLevel + 1);
        }
    }

    /**
     * 鐢熸垚鍥哄畾鐨?0%妗ｄ綅锛?00% 鍒?10%锛?
     * 纭繚瀹㈡埛鍚庡彴鍙互浠庡叏鍚冨埌浣庡埄娑︾殑鎵€鏈夋。浣嶄腑鑷敱閫夋嫨
     *
     * @param array $solutions 宸茶鑼冨寲鐨勬柟妗堝垪琛?
     * @return array 鍥哄畾妗ｄ綅鍒楄〃锛歔100, 90, 80, 70, 60, 50, 40, 30, 20, 10]
     */
    private static function generateDynamicRates(array $solutions): array
    {
        // 杩斿洖鍥哄畾鐨?0%妗ｄ綅锛岃鐩?00%-10%鐨勫叏鑼冨洿
        return [100, 90, 80, 70, 60, 50, 40, 30, 20, 10];
    }

    private static function buildRateBuckets(array $solutions, int $year, ?int $maxConsecutive = null): array
    {
        $normalized = self::normalizeBucketSolutions($solutions, $year, $maxConsecutive);

        if (empty($normalized)) {
            $normalized = self::normalizeBucketSolutions(self::generateRandomSolutions(160, $maxConsecutive), $year, $maxConsecutive);
        }
        if (empty($normalized)) {
            $normalized = self::normalizeBucketSolutions(self::generateRandomSolutions(200, $maxConsecutive), $year, $maxConsecutive);
        }

        // 鐢熸垚鍔ㄦ€佹。浣嶈€屼笉鏄浐瀹氱殑10妗?
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
                // 绗竴浼樺厛绾э細鍒╂鼎鐜囷紙闄嶅簭锛?
                if ($a['profit_rate'] != $b['profit_rate']) {
                    return $b['profit_rate'] <=> $a['profit_rate'];
                }

                // 绗簩浼樺厛绾э細娣蜂贡搴?澶氭牱鎬э紙闄嶅簭锛? 浼樺厛杩斿洖娣蜂贡搴﹂珮鐨勫彿鐮?
                $diversityA = $a['diversity_score'] ?? 0;
                $diversityB = $b['diversity_score'] ?? 0;
                if ($diversityA != $diversityB) {
                    return $diversityB <=> $diversityA;
                }

                // 绗笁浼樺厛绾э細鎬诲埄娑︼紙闄嶅簭锛?
                return $b['total_profit'] <=> $a['total_profit'];
            });

            $buckets[] = $bucket;
        }

        return $buckets;
    }

    private static function normalizeBucketSolutions(array $solutions, int $year, ?int $maxConsecutiveLimit = null): array
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
                'total_profit' => isset($solution['total_profit']) ? (float)$solution['total_profit'] : 0.0,
                'total_prize' => isset($solution['total_prize']) ? (float)$solution['total_prize'] : (float)($solution['prize_amount'] ?? 0),
                'bet_amount' => isset($solution['bet_amount']) ? (float)$solution['bet_amount'] : (float)($solution['total_bets'] ?? 0),
                'strategy' => $solution['strategy'] ?? null,
                'distance_to_target' => isset($solution['distance_to_target']) ? (float)$solution['distance_to_target'] : null,
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
            // 鍏佽鍚屼竴鐢熻倴鏈€澶?涓彿鐮侊紝鏀寔閲嶈倴闇€姹?
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
     * 妫€鏌ユ槸鍚﹀瓨鍦ㄨ繛缁簭鍒楋紙5涓垨浠ヤ笂杩炵画鍙风爜锛?
     *
     * @param array $numbers 鍙风爜鏁扮粍锛堝凡鎺掑簭锛?
     * @return int 鏈€澶ц繛缁彿鐮佹暟
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

            // 鎺掗櫎椤哄簭鍙风爜锛堣繛缁?涓垨浠ヤ笂锛?
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
            $key = self::buildSolutionKey($built['m1_m6'], $built['m7']);
            if (!isset($unique[$key])) {
                $unique[$key] = $built;
            }
            $attempts++;
        }

        return array_values($unique);
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
     * 鏍规嵁鐩爣鍒╂鼎鐜囨煡鎵惧彿鐮?
     *
     * @param int $gid 娓告垙ID
     * @param string $qishu 鏈熷彿
     * @param string $plateCode 鐩樺彛浠ｇ爜锛堝A銆丅銆丆锛?
     * @param float $targetRate 鐩爣鍒╂鼎鐜?
     * @param float $tolerance 鍏佽璇樊
     * @param int|null $year 骞翠唤
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

            // 浣跨敤澧炲己鐗堢畻娉?
            $service = new \app\common\service\EnhancedBestPlanService($gid, $qishu, $year, $plateCode);

            if ($service->getBetCount() === 0) {
                self::setError('璇ユ湡鏆傛棤鎶曟敞鏁版嵁');
                return false;
            }

            // 浣跨敤澧炲己鐗堟柟娉曟煡鎵炬帴杩戠洰鏍囧埄娑︾巼鐨勬柟妗?
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
     * 鑾峰彇鍒嗘瀽鍘嗗彶鍒楄〃
     *
     * @param int $gid 娓告垙ID
     * @param int $limit 杩斿洖鏉℃暟
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
     * 鑾峰彇鍒嗘瀽璇︽儏
     *
     * @param int $id 璁板綍ID
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

        // 瑙ｆ瀽JSON瀛楁
        $record['number_details'] = json_decode($record['number_details'], true);

        // 纭繚鏄暟缁?
        if (!is_array($record['number_details'])) {
            $record['number_details'] = [];
        }

        // 鎸夊埄娑︽帓搴忥紙浠呭綋鏁扮粍闈炵┖涓斿寘鍚玴rofit瀛楁锛?
        if (!empty($record['number_details'])) {
            usort($record['number_details'], function($a, $b) {
                $profitA = $a['profit'] ?? 0;
                $profitB = $b['profit'] ?? 0;
                return $profitB <=> $profitA;
            });
        }

        // 娣诲姞椋庨櫓绛夌骇鏂囨湰
        foreach ($record['number_details'] as &$item) {
            $item['risk_level_text'] = BestPlanService::getRiskLevelText($item['risk_level'] ?? 0);
        }

        return $record;
    }

    /**
     * 鑾峰彇褰撳墠鍙垎鏋愮殑鏈熷彿
     *
     * @param int $gid 娓告垙ID
     * @param string $plateCode 鐩樺彛浠ｇ爜锛堝锛欰銆丅銆丆锛?
     * @return array|null
     */
    public static function getCurrentQishu(int $gid, string $plateCode = 'am'): ?array
    {
        trace("馃攳 [getCurrentQishu] 鏌ヨ鍙傛暟: gid=$gid, plateCode=$plateCode", 'info');

        // 浼樺厛鏌ヨ鎶曟敞涓殑鏈熷彿 (status=2)
        $issue = Db::table('la_lottery_issue')
            ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
            ->where('game_id', $gid)
            ->where('plate_code', $plateCode)  // 鉁?娣诲姞鐩樺彛绛涢€?
            ->where('status', 2)  // 2=鎶曟敞涓?
            ->order('draw_time', 'asc')
            ->find();

        // 濡傛灉娌℃湁鎶曟敞涓殑鏈熷彿锛屾煡璇㈠緟寮€鐩樼殑鏈熷彿锛坰tatus=1锛?
        if (!$issue) {
            $issue = Db::table('la_lottery_issue')
                ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)  // 鉁?娣诲姞鐩樺彛绛涢€?
                ->where('status', 1)  // 1=寰呭紑鐩?
                ->order('draw_time', 'asc')
                ->find();
        }

        // 濡傛灉杩樻病鏈夛紝鏌ヨ鏈€鏂扮殑宸插紑濂栨湡鍙凤紙status=3锛? 鐢ㄤ簬鏄剧ず寮€濂栫粨鏋?
        if (!$issue) {
            $issue = Db::table('la_lottery_issue')
                ->field('issue, plate_code, open_time, close_time, draw_time, status, result')
                ->where('game_id', $gid)
                ->where('plate_code', $plateCode)  // 鉁?娣诲姞鐩樺彛绛涢€?
                ->where('status', 3)  // 3=宸插紑濂?
                ->order('draw_time', 'desc')  // 闄嶅簭锛屽彇鏈€鏂扮殑
                ->find();
        }

        if (!$issue) {
                    trace("No solution found after expansion; using best solution.", 'warning');
            return null;
        }

        // 璋冭瘯鏃ュ織
        trace("馃搵 鏌ヨ鍒版湡鍙锋暟鎹? " . json_encode($issue, JSON_UNESCAPED_UNICODE), 'info');

        // 妫€鏌esult瀛楁鐨勫疄闄呭€?
        $resultValue = $issue['result'] ?? null;
        trace("馃攳 result瀛楁鍊? [" . var_export($resultValue, true) . "] 绫诲瀷: " . gettype($resultValue), 'info');

        // 杞崲鏃堕棿鎴充负鏃ユ湡鏃堕棿鏍煎紡
        // 鉁?濮嬬粓鍖呭惈鎵€鏈夊瓧娈碉紝閬垮厤鍓嶇undefined
        $result = [
            'qishu' => $issue['issue'],
            'plate_code' => $issue['plate_code'],
            'opentime' => $issue['open_time'] ? date('Y-m-d H:i:s', $issue['open_time']) : '',
            'closetime' => $issue['close_time'] ? date('Y-m-d H:i:s', $issue['close_time']) : '',
            'kjtime' => $issue['draw_time'] ? date('Y-m-d H:i:s', $issue['draw_time']) : '',
            'status' => (int)$issue['status'],  // 鉁?鐘舵€佸瓧娈?
            'is_opened' => ($issue['status'] == 3 && !empty($resultValue)),  // status=3涓攔esult涓嶄负绌烘墠鏄凡寮€濂?
            'draw_numbers' => [],  // 鉁?榛樿绌烘暟缁?
            'draw_numbers_text' => '',  // 鉁?榛樿绌哄瓧绗︿覆
        ];

        // 濡傛灉result瀛楁鏈夊€硷紝瑙ｆ瀽寮€濂栧彿鐮?
        if (!empty($resultValue) && is_string($resultValue)) {
            trace("馃幇 寮€濂栧彿鐮佸師濮嬫暟鎹? " . $resultValue, 'info');
            $result['draw_numbers'] = explode(',', $resultValue);
            $result['draw_numbers_text'] = $resultValue;
        } else {
            trace("No result value or non-string result; returning empty draw numbers.", 'warning');
        }

        trace("鉁?鏈€缁堣繑鍥炴暟鎹? " . json_encode($result, JSON_UNESCAPED_UNICODE), 'info');

        return $result;
    }

    /**
     * 鏇存柊瀹為檯寮€濂栫粨鏋?
     *
     * @param int $gid 娓告垙ID
     * @param string $qishu 鏈熷彿
     * @param int $actualNumber 瀹為檯寮€鍑虹殑鐗圭爜
     * @return bool
     */
    public static function updateActualResult(int $gid, string $qishu, int $actualNumber): bool
    {
        try {
            // 鏌ユ壘璁板綍锛堜娇鐢ㄦ柊琛?la_best_plan_history锛?
            $record = Db::table('la_best_plan_history')
                ->where('gid', $gid)
                ->where('qishu', $qishu)
                ->find();

            if (!$record) {
                self::setError('Record not found.');
                return false;
            }

            // 浠嶫SON涓В鏋愯鍙风爜鐨勯娴嬪埄娑?
            $details = json_decode($record['number_details'], true);
            $actualProfit = 0;

            foreach ($details as $item) {
                if ($item['number'] == $actualNumber) {
                    $actualProfit = $item['profit'];
                    break;
                }
            }

            // 鏇存柊璁板綍
            Db::table('la_best_plan_history')
                ->where('id', $record['id'])
                ->update([
                    'status' => 1,  // 宸插紑濂?
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
     * 鑾峰彇鎶曟敞姹囨€荤粺璁★紙鎸夌帺娉曞垎绫伙級
     *
     * @param int $gid 娓告垙ID
     * @param string $qishu 鏈熷彿
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
                self::setError('Operation failed.');
                return false;
            }

            // Check if planning window is open (must be after close_time)
            $closeTimeRaw = $issue['close_time'] ?? 0;
            $closeTimeTs = is_numeric($closeTimeRaw) ? (int)$closeTimeRaw : (int)strtotime((string)$closeTimeRaw);
            $currentTime = time();
            if ($closeTimeTs > 0 && $currentTime < $closeTimeTs) {
                Db::rollback();
                self::setError('Operation failed.');
                return false;
            }

            if (!empty($issue['result'])) {
                Db::rollback();
                self::setError('鏈湡宸插紑濂栵紝涓嶈兘閲嶅鎻愪氦璁″垝');
                return false;
            }

            if (!empty($issue['is_settled'])) {
                Db::rollback();
                self::setError('鏈湡宸茬粨绠楋紝涓嶈兘閲嶅鎻愪氦璁″垝');
                return false;
            }

            // Prevent duplicate submission of manual plan
            if (!empty($issue['planned_result']) && $issue['planned_source'] == 1) {
                Db::rollback();
                self::setError('Plan already set for this issue.');
                return false;
            }

            $numbers = array_values(array_map('intval', $bestNumbers));
            if (count($numbers) !== 7) {
                Db::rollback();
                self::setError('Operation failed.');
                return false;
            }
            foreach ($numbers as $num) {
                if ($num < 1 || $num > 49) {
                    Db::rollback();
                    self::setError('Numbers must be between 1 and 49.');
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

            // 鏀寔璺ㄥ勾浠界敓鑲栵細7th鍙风爜鍙互鍖归厤浠绘剰骞翠唤鐨勫悓鐢熻倴
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
     * 鑾峰彇鍙风爜鍦ㄦ墍鏈夊勾浠戒腑鍙兘鐨勭敓鑲?
     *
     * 渚嬪锛氬彿鐮?鍦ㄤ笉鍚屽勾浠戒腑鍙兘鏄細
     * - 1981骞寸殑楦?
     * - 1993骞寸殑楦?
     * - 2005骞寸殑楦?
     * - 2017骞寸殑楦?
     * - 2029骞寸殑楦?
     *
     * 杩欏厑璁哥壒鑲栨姇娉ㄥ湪璺ㄥ勾浠借寖鍥村唴鐢熸晥锛?
     * 鍙7th鍙风爜鐨勭敓鑲栧湪浠讳綍骞翠唤涓尮閰嶆姇娉ㄧ殑鐢熻倴鍗冲彲
     *
     * @param int $number 鍙风爜锛?-49锛?
     * @return array 璇ュ彿鐮佸湪鎵€鏈夊勾浠戒腑瀵瑰簲鐨勭敓鑲栧垪琛紙鍘婚噸锛?
     */
    private static function getAllPossibleZodiacs(int $number): array
    {
        if ($number < 1 || $number > 49) {
            return [];
        }

        static $cache = [];

        // 浼樺厛妫€鏌ョ紦瀛?
        if (isset($cache[$number])) {
            return $cache[$number];
        }

        $zodiacs = [];

        // 鎵弿涓€涓畬鏁寸殑鐢熻倴杞浆鍛ㄦ湡锛?2骞达級
        // 鍦ㄨ繖涓懆鏈熷唴鍙互鑾峰緱璇ュ彿鐮佺殑鎵€鏈夊彲鑳界敓鑲?
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

        // 缂撳瓨缁撴灉
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
     * 鎵嬪姩鍒涘缓鏂版湡鍙?
     *
     * @param int $gid 娓告垙ID
     * @param string $plateCode 鐩樺彛浠ｇ爜
     * @return array|false 杩斿洖鏂版湡鍙蜂俊鎭垨false
     */
    public static function manualCreateNewIssue(int $gid, string $plateCode = 'am')
    {
        try {
            // 璋冪敤LotteryIssueService鍒涘缓鏂版湡鍙?
            $newIssue = \app\common\service\LotteryIssueService::getOrCreateCurrentIssue($gid, $plateCode);

            if (!$newIssue) {
                self::$error = 'Failed to create new issue.';
                trace("鉂?鎵嬪姩鍒涘缓鏂版湡鍙峰け璐? gid=$gid, plateCode=$plateCode", 'error');
                return false;
            }

            trace("鉁?鎵嬪姩鍒涘缓鏂版湡鍙锋垚鍔? " . json_encode($newIssue, JSON_UNESCAPED_UNICODE), 'info');

            return [
                'issue' => $newIssue['issue'],
                'open_time' => $newIssue['open_time'],
                'close_time' => $newIssue['close_time'],
                'draw_time' => $newIssue['draw_time'],
                'status' => $newIssue['status'] ?? 0,
                'status_text' => self::getIssueStatusText($newIssue['status'] ?? 0),
            ];
        } catch (\Exception $e) {
            self::$error = '鍒涘缓澶辫触: ' . $e->getMessage();
            trace("鉂?鎵嬪姩鍒涘缓鏂版湡鍙峰紓甯? " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * 鑾峰彇鏈熷彿鐘舵€佹枃鏈?
     *
     * @param int $status 鐘舵€佸€?
     * @return string 鐘舵€佹枃鏈?
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
