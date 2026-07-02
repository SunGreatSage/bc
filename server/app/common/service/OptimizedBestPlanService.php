<?php
declare(strict_types=1);

namespace app\common\service;

use think\facade\Db;

/**
 * 新版最佳方案服务：统一下注模拟、49退款、生肖判断等逻辑
 */
class OptimizedBestPlanService
{
    private const KEYWORDS_SPECIAL_NUMBER = ['特码', '特碼', '特号', '特號'];
    private const KEYWORDS_NORMAL_NUMBER = ['正码', '正碼', '平码', '平碼'];
    private const KEYWORDS_SPECIAL_ZODIAC = ['特肖', '特肖连'];
    private const KEYWORDS_ANY_ZODIAC = ['平肖'];
    private const KEYWORDS_MULTI_ZODIAC = ['三肖', '四肖', '五肖', '六肖'];
    private const KEYWORDS_POSITIVE_ZODIAC = ['正肖'];
    private const KEYWORDS_SPECIAL_ODD_EVEN = ['特码单双', '特碼單雙', '特碼单双'];
    private const KEYWORDS_DIGIT_ODD_EVEN = ['合数单双', '合數單雙', '合數单双'];
    private const KEYWORDS_TOTAL_ODD_EVEN = ['总和单双', '總和單雙', '總和单双'];
    private const ODD_KEYWORDS = ['单', '單', '奇', 'odd', 'dan'];
    private const EVEN_KEYWORDS = ['双', '雙', '偶', 'even', 'shuang'];

    private const SPECIAL_CANDIDATE_LIMIT = 20;
    private const NORMAL_POOL_LIMIT = 30;
    private const MAX_COMBOS_PER_SPECIAL = 800;
    private const TOP_SOLUTION_LIMIT = 100;

    // 随机采样参数
    private const RANDOM_SAMPLE_COUNT = 200;
    private const LAYERED_SAMPLE_PER_TIER = 50;

    protected int $gid;
    protected string $qishu;
    protected int $year;
    protected string $plateCode = '';
    protected array $allBets = [];
    protected float $totalBetAmount = 0.0;
    protected array $zodiacMap = [];
    protected array $zodiacTable = [];
    protected array $playNameCache = [];
    protected array $specialCodeWeights = [];
    protected array $normalCodeWeights = [];

    /** 搜索参数（可按下注量动态调整） */
    protected int $specialCandidateLimit = self::SPECIAL_CANDIDATE_LIMIT;
    protected int $normalPoolLimit = self::NORMAL_POOL_LIMIT;
    protected int $maxCombosPerSpecial = self::MAX_COMBOS_PER_SPECIAL;

    /** 玩法缓存，避免重复解析 */
    protected array $specialNumberBets = [];
    protected array $normalNumberBets = [];
    protected array $numberComboBets = [];
    protected array $numberMissBets = [];
    protected array $specialZodiacBets = [];
    protected array $anyZodiacBets = [];
    protected array $positiveZodiacBets = [];
    protected array $multiZodiacBets = [];
    protected array $otherBets = [];

    public function __construct(int $gid, string $qishu, int $year, string $plateCode = '')
    {
        $this->gid = $gid;
        $this->qishu = $qishu;
        $this->year = $year;
        $this->plateCode = $plateCode;

        $this->loadAllBets();
        $this->loadZodiacMap();
        $this->loadZodiacTable();
        $this->loadPlayNameCache();
        $this->calculateWeights();
        $this->preprocessBets();
        $this->adjustSearchStrategy();
    }

    /**
     * 按玩法分桶，缓存已解析数据，减少重复工作
     */
    protected function preprocessBets(): void
    {
        $this->specialNumberBets = [];
        $this->normalNumberBets = [];
        $this->numberComboBets = [];
        $this->numberMissBets = [];
        $this->specialZodiacBets = [];
        $this->anyZodiacBets = [];
        $this->positiveZodiacBets = [];
        $this->multiZodiacBets = [];
        $this->otherBets = [];

        foreach ($this->allBets as $bet) {
            $methodName = $bet['method_name'] ?? ($this->playNameCache[$bet['bid']] ?? '');
            $betType = $bet['bet_type'] ?? 'win';
            $amount = (float)$bet['je'];
            $odds = (float)$bet['peilv1'];
            $content = (string)($bet['content'] ?? '');
            $items = array_values(array_filter(array_map('trim', explode(',', $content)), 'strlen'));

            $base = [
                'amount' => $amount,
                'odds' => $odds,
                'bet_type' => $betType,
            ];

            if ($this->containsKeyword($methodName, self::KEYWORDS_SPECIAL_NUMBER)) {
                $numbers = array_values(array_unique(array_map('intval', $items)));
                if (!empty($numbers)) {
                    $this->specialNumberBets[] = $base + ['numbers' => $numbers];
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_NORMAL_NUMBER)) {
                $numbers = array_values(array_unique(array_map('intval', $items)));
                if (!empty($numbers)) {
                    $this->normalNumberBets[] = $base + ['numbers' => $numbers];
                }
                continue;
            }

            $missRule = $this->getNumberMissRule($methodName);
            if ($missRule) {
                $numbers = $this->parseNumberSelections($content);
                if (count($numbers) === $missRule['select_count']) {
                    $this->numberMissBets[] = $base + [
                        'numbers' => $numbers,
                        'select_count' => $missRule['select_count'],
                    ];
                }
                continue;
            }

            $comboRule = $this->getNumberComboRule($methodName);
            if ($comboRule) {
                $numbers = $this->parseNumberSelections($content);
                if (count($numbers) === $comboRule['select_count']) {
                    $this->numberComboBets[] = $base + [
                        'numbers' => $numbers,
                        'select_count' => $comboRule['select_count'],
                        'hit_count' => $comboRule['hit_count'],
                    ];
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_ANY_ZODIAC)) {
                $zodiacs = ZodiacService::normalizeZodiacSelections($items, $this->year);
                if (!empty($zodiacs)) {
                    $this->anyZodiacBets[] = $base + ['zodiacs' => $zodiacs];
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_SPECIAL_ZODIAC)) {
                $zodiacs = ZodiacService::normalizeZodiacSelections($items, $this->year);
                if (!empty($zodiacs)) {
                    $this->specialZodiacBets[] = $base + ['zodiacs' => $zodiacs];
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_MULTI_ZODIAC)) {
                $zodiacs = ZodiacService::normalizeZodiacSelections($items, $this->year);
                if (!empty($zodiacs)) {
                    $this->multiZodiacBets[] = $base + ['zodiacs' => $zodiacs];
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_POSITIVE_ZODIAC)) {
                $zodiacs = ZodiacService::normalizeZodiacSelections($items, $this->year);
                if (!empty($zodiacs)) {
                    $this->positiveZodiacBets[] = $base + ['zodiacs' => $zodiacs];
                }
                continue;
            }

            $this->otherBets[] = $bet;
        }
    }

    /**
     * 根据下注笔数动态缩小搜索空间
     */
    protected function adjustSearchStrategy(): void
    {
        $betCount = $this->getBetCount();

        if ($betCount > 1000) {
            $this->specialCandidateLimit = 10;
            $this->normalPoolLimit = 20;
            $this->maxCombosPerSpecial = 200;
        } elseif ($betCount > 500) {
            $this->specialCandidateLimit = 15;
            $this->normalPoolLimit = 25;
            $this->maxCombosPerSpecial = 400;
        } elseif ($betCount > 200) {
            $this->specialCandidateLimit = 18;
            $this->normalPoolLimit = 28;
            $this->maxCombosPerSpecial = 600;
        } else {
            $this->specialCandidateLimit = self::SPECIAL_CANDIDATE_LIMIT;
            $this->normalPoolLimit = self::NORMAL_POOL_LIMIT;
            $this->maxCombosPerSpecial = self::MAX_COMBOS_PER_SPECIAL;
        }
    }

    protected function loadAllBets(): void
    {
        $query = Db::table('la_betting_record')
            ->alias('b')
            ->field('b.id as tid, b.user_id as userid, b.total_amount as je,
                     b.bet_content as content, b.method_id as bid,
                     b.method_name, b.bet_type, b.odds as peilv1, b.status as bs')
            ->where('b.game_id', $this->gid)
            ->where('b.issue', $this->qishu)
            ->where('b.status', 0)
        ;

        if ($this->plateCode !== '') {
            $query->where('b.plate_code', $this->plateCode);
        }

        $this->allBets = $query->select()->toArray();

        $this->totalBetAmount = array_sum(array_column($this->allBets, 'je'));
    }

    protected function loadZodiacMap(): void
    {
        $zodiacService = new \app\common\service\ZodiacYearService();
        $this->zodiacMap = $zodiacService->getNumberMapByYear($this->year);
    }

    protected function loadZodiacTable(): void
    {
        $zodiacService = new \app\common\service\ZodiacYearService();
        $this->zodiacTable = $zodiacService->getZodiacTableByYear($this->year);
    }

    protected function loadPlayNameCache(): void
    {
        $playMethods = Db::table('la_play_method')
            ->where('game_id', $this->gid)
            ->select()
            ->toArray();

        foreach ($playMethods as $method) {
            $this->playNameCache[$method['id']] = $method['name'];
        }
    }

    /**
     * 根据当前投注计算每个号码的风险权重，后续用于优先级排序
     */
    protected function calculateWeights(): void
    {
        for ($i = 1; $i <= 49; $i++) {
            $this->specialCodeWeights[$i] = 0;
            $this->normalCodeWeights[$i] = 0;
        }

        foreach ($this->allBets as $bet) {
            $methodName = $bet['method_name'] ?? ($this->playNameCache[$bet['bid']] ?? '');
            $betType = $bet['bet_type'] ?? 'win';
            $amount = (float)$bet['je'];
            $odds = (float)$bet['peilv1'];
            $weightedAmount = $amount * $odds;
            if ($weightedAmount <= 0) {
                continue;
            }

            $content = (string)($bet['content'] ?? '');
            $items = array_values(array_filter(array_map('trim', explode(',', $content)), 'strlen'));
            $direction = $betType === 'not_win' ? -1 : 1;

            if ($this->containsKeyword($methodName, self::KEYWORDS_SPECIAL_NUMBER)) {
                $betNumbers = array_map('intval', $items);
                foreach ($betNumbers as $num) {
                    if ($num >= 1 && $num <= 49) {
                        $this->specialCodeWeights[$num] += $direction * $weightedAmount;
                    }
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_NORMAL_NUMBER)) {
                $betNumbers = array_map('intval', $items);
                foreach ($betNumbers as $num) {
                    if ($num >= 1 && $num <= 49) {
                        $this->normalCodeWeights[$num] += $direction * $weightedAmount;
                    }
                }
                continue;
            }

            $missRule = $this->getNumberMissRule($methodName);
            if ($missRule) {
                $betNumbers = $this->parseNumberSelections($content);
                foreach ($betNumbers as $num) {
                    $this->normalCodeWeights[$num] -= $direction * $weightedAmount;
                    $this->specialCodeWeights[$num] -= $direction * $weightedAmount;
                }
                continue;
            }

            $comboRule = $this->getNumberComboRule($methodName);
            if ($comboRule) {
                $betNumbers = $this->parseNumberSelections($content);
                foreach ($betNumbers as $num) {
                    $this->normalCodeWeights[$num] += $direction * $weightedAmount;
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_ANY_ZODIAC)) {
                $betZodiacs = ZodiacService::normalizeZodiacSelections($items, $this->year);
                $numbers = $this->expandZodiacsToNumbers($betZodiacs);
                foreach ($numbers as $num) {
                    $this->normalCodeWeights[$num] += $direction * $weightedAmount;
                    $this->specialCodeWeights[$num] += $direction * $weightedAmount;
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_SPECIAL_ZODIAC)) {
                $betZodiacs = ZodiacService::normalizeZodiacSelections($items, $this->year);
                $numbers = $this->expandZodiacsToNumbers($betZodiacs);
                foreach ($numbers as $num) {
                    $this->specialCodeWeights[$num] += $direction * $weightedAmount;
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_MULTI_ZODIAC)) {
                $betZodiacs = ZodiacService::normalizeZodiacSelections($items, $this->year);
                $numbers = $this->expandZodiacsToNumbers($betZodiacs);
                foreach ($numbers as $num) {
                    $this->normalCodeWeights[$num] += $direction * $weightedAmount;
                }
                continue;
            }

            if ($this->containsKeyword($methodName, self::KEYWORDS_POSITIVE_ZODIAC)) {
                $betZodiacs = ZodiacService::normalizeZodiacSelections($items, $this->year);
                $numbers = $this->expandZodiacsToNumbers($betZodiacs);
                foreach ($numbers as $num) {
                    $this->normalCodeWeights[$num] += $direction * $weightedAmount;
                }
            }
        }
    }

    /**
     * 寻找最佳开奖组合
     */
    public function findBest7Numbers(
        ?float $targetRate = null,
        float $tolerance = 5.0,
        bool $includeAllSolutions = false,
        ?int $maxConsecutive = null
    ): array
    {
        if (empty($this->allBets)) {
            $result = [
                'best_solution' => null,
                'top_solutions' => [],
                'total_bets' => 0,
                'total_orders' => 0,
                'risk_warning' => null,
                'risk_assessment' => null,
                'recommendations' => ['暂无投注数据,可任意开奖'],
                'strategy_used' => 'none',
            ];
            if ($includeAllSolutions) {
                $result['all_solutions'] = [];
            }
            return $result;
        }


        $specialCandidates = $this->getSortedNumbers($this->specialCodeWeights, $this->specialCandidateLimit);
        if (empty($specialCandidates)) {
            $specialCandidates = range(1, 49);
        }
        if (!in_array(49, $specialCandidates, true)) {
            $specialCandidates[] = 49;
        }

        $normalSorted = $this->getSortedNumbers($this->normalCodeWeights, $this->normalPoolLimit);
        if (empty($normalSorted)) {
            $normalSorted = range(1, 49);
        }

        $solutions = [];
        $seen = [];
        foreach ($specialCandidates as $specialCode) {
            $normalPool = array_values(array_filter($normalSorted, fn($num) => $num !== $specialCode));
            $normalPool = $this->ensureNormalPool($normalPool, $specialCode);
            if (count($normalPool) < 6) {
                continue;
            }

            $combinations = $this->generateCombinationsLimited($normalPool, 6, $this->maxCombosPerSpecial);
            foreach ($combinations as $combo) {
                sort($combo);
                $key = implode('-', $combo) . '-' . $specialCode;
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;

                if (!$this->isComboWithinMaxConsecutive($combo, $specialCode, $maxConsecutive)) {
                    continue;
                }

                $combined = $this->calculateCombinedProfit($combo, $specialCode);
                $profit = $combined['total_profit'];
                $profitRate = $this->totalBetAmount > 0
                    ? round(($profit / $this->totalBetAmount) * 100, 2)
                    : 0.0;

                $solutions[] = [
                    'm1_m6' => $combo,
                    'm7' => $specialCode,
                    'total_profit' => round($profit, 2),
                    'total_prize' => round($combined['total_prize'], 2),
                    'bet_amount' => $this->totalBetAmount,
                    'profit_rate' => $profitRate,
                    'special_weight' => $this->specialCodeWeights[$specialCode] ?? 0,
                    'normal_weights' => array_sum(array_map(fn($n) => $this->normalCodeWeights[$n] ?? 0, $combo)),
                    'strategy' => $targetRate !== null ? 'target_rate' : 'max_profit',
                    'distance_to_target' => $targetRate !== null ? abs($profitRate - $targetRate) : 0,
                ];
            }
        }

        if (empty($solutions)) {
            $fallbackSpecial = $specialCandidates[0] ?? 1;
            $fallbackNormals = $this->ensureNormalPool(array_slice($normalSorted, 0, 6), $fallbackSpecial);
            $fallbackNormals = array_slice($fallbackNormals, 0, 6);

            $combined = $this->calculateCombinedProfit($fallbackNormals, $fallbackSpecial);
            $profit = $combined['total_profit'];
            $profitRate = $this->totalBetAmount > 0
                ? round(($profit / $this->totalBetAmount) * 100, 2)
                : 0.0;

            $solutions[] = [
                'm1_m6' => $fallbackNormals,
                'm7' => $fallbackSpecial,
                'total_profit' => round($profit, 2),
                'total_prize' => round($combined['total_prize'], 2),
                'bet_amount' => $this->totalBetAmount,
                'profit_rate' => $profitRate,
                'special_weight' => $this->specialCodeWeights[$fallbackSpecial] ?? 0,
                'normal_weights' => array_sum(array_map(fn($n) => $this->normalCodeWeights[$n] ?? 0, $fallbackNormals)),
                'strategy' => $targetRate !== null ? 'target_rate' : 'max_profit',
                'distance_to_target' => $targetRate !== null ? abs($profitRate - $targetRate) : 0,
            ];
        }

        // Always sort by profit_rate descending to maintain consistent ordering (100% → 10%)
        usort($solutions, function ($a, $b) {
            if ($a['profit_rate'] === $b['profit_rate']) {
                return $b['total_profit'] <=> $a['total_profit'];
            }
            return $b['profit_rate'] <=> $a['profit_rate'];
        });

        // If target rate is specified, find the best solution that matches the target
        $bestSolution = $solutions[0] ?? null;
        if ($targetRate !== null && !empty($solutions)) {
            $targetMin = $targetRate - 5.0;  // tolerance default 5%
            $targetMax = $targetRate + 5.0;

            // First, try to find a solution within the target range
            $matchedSolution = null;
            foreach ($solutions as $solution) {
                if ($solution['profit_rate'] >= $targetMin && $solution['profit_rate'] <= $targetMax) {
                    $matchedSolution = $solution;
                    break;
                }
            }

            // If found a match within target range, use it as best solution
            if ($matchedSolution) {
                $bestSolution = $matchedSolution;
            } else {
                // Otherwise, use the solution with highest profit rate
                $bestSolution = $solutions[0];
            }
        }
        $riskAssessment = $this->buildRiskAssessment($bestSolution);
        $riskWarning = $this->buildRiskWarning($bestSolution);
        $recommendations = $this->buildRecommendations($bestSolution);

        $result = [
            'best_solution' => $bestSolution,
            'top_solutions' => array_slice($solutions, 0, self::TOP_SOLUTION_LIMIT),
            'total_bets' => $this->totalBetAmount,
            'total_orders' => count($this->allBets),
            'risk_warning' => $riskWarning,
            'risk_assessment' => $riskAssessment,
            'recommendations' => $recommendations,
            'strategy_used' => $targetRate !== null ? 'target_rate' : 'max_profit',
        ];
        if ($includeAllSolutions) {
            $result['all_solutions'] = $solutions;
        }
        return $result;
    }

    public function buildSolutionFromNumbers(array $m1_m6, int $m7, ?int $maxConsecutive = null): ?array
    {
        $m1_m6 = array_values(array_unique(array_map('intval', $m1_m6)));
        if (count($m1_m6) !== 6) {
            return null;
        }
        $m7 = (int)$m7;
        if ($m7 < 1 || $m7 > 49) {
            return null;
        }
        if (in_array($m7, $m1_m6, true)) {
            return null;
        }
        sort($m1_m6);
        if (!$this->isComboWithinMaxConsecutive($m1_m6, $m7, $maxConsecutive)) {
            return null;
        }

        $combined = $this->calculateCombinedProfit($m1_m6, $m7);
        $profit = $combined['total_profit'];
        $profitRate = $this->totalBetAmount > 0
            ? round(($profit / $this->totalBetAmount) * 100, 2)
            : 0.0;

        return [
            'm1_m6' => $m1_m6,
            'm7' => $m7,
            'total_profit' => round($profit, 2),
            'total_prize' => round($combined['total_prize'], 2),
            'bet_amount' => $this->totalBetAmount,
            'profit_rate' => $profitRate,
            'special_weight' => $this->specialCodeWeights[$m7] ?? 0,
            'normal_weights' => array_sum(array_map(fn($n) => $this->normalCodeWeights[$n] ?? 0, $m1_m6)),
        ];
    }

    protected function calculateCombinedProfit(array $normalCodes, int $specialCode): array
    {
        $totalPrize = 0.0;
        $all7Numbers = array_merge($normalCodes, [$specialCode]);
        $specialZodiac = $this->zodiacMap[$specialCode] ?? '';
        $all7Zodiacs = ZodiacService::convertNumbersToZodiacsWithYear($all7Numbers, $this->year);
        $isSpecial49 = ($specialCode === 49);

        foreach ($this->specialNumberBets as $bet) {
            $hit = in_array($specialCode, $bet['numbers'], true);
            $result = $this->resolveResult($hit, $bet['bet_type']);
            $this->accumulatePrize($result, $bet['amount'], $bet['odds'], $totalPrize);
        }

        foreach ($this->normalNumberBets as $bet) {
            $hit = !empty(array_intersect($bet['numbers'], $all7Numbers));
            $result = $this->resolveResult($hit, $bet['bet_type']);
            $this->accumulatePrize($result, $bet['amount'], $bet['odds'], $totalPrize);
        }

        foreach ($this->numberComboBets as $bet) {
            $hitCount = count(array_intersect($bet['numbers'], $normalCodes));
            $result = $this->resolveResult($hitCount >= $bet['hit_count'], $bet['bet_type']);
            $this->accumulatePrize($result, $bet['amount'], $bet['odds'], $totalPrize);
        }

        foreach ($this->numberMissBets as $bet) {
            $hitCount = count(array_intersect($bet['numbers'], $all7Numbers));
            $result = $this->resolveResult($hitCount === 0, $bet['bet_type']);
            $this->accumulatePrize($result, $bet['amount'], $bet['odds'], $totalPrize);
        }

        foreach ($this->specialZodiacBets as $bet) {
            $hit = $specialZodiac !== '' && in_array($specialZodiac, $bet['zodiacs'], true);
            $result = $this->resolveResult($hit, $bet['bet_type']);
            $this->accumulatePrize($result, $bet['amount'], $bet['odds'], $totalPrize);
        }

        foreach ($this->anyZodiacBets as $bet) {
            $hit = !empty(array_intersect($bet['zodiacs'], $all7Zodiacs));
            $result = $this->resolveResult($hit, $bet['bet_type']);
            $this->accumulatePrize($result, $bet['amount'], $bet['odds'], $totalPrize);
        }

        foreach ($this->positiveZodiacBets as $bet) {
            $hit = !empty(array_intersect($bet['zodiacs'], $all7Zodiacs));
            $result = $this->resolveResult($hit, $bet['bet_type']);
            $this->accumulatePrize($result, $bet['amount'], $bet['odds'], $totalPrize);
        }

        foreach ($this->multiZodiacBets as $bet) {
            if ($isSpecial49) {
                $this->accumulatePrize('draw', $bet['amount'], $bet['odds'], $totalPrize);
                continue;
            }
            $hit = !empty(array_intersect($bet['zodiacs'], $all7Zodiacs));
            $result = $this->resolveResult($hit, $bet['bet_type']);
            $this->accumulatePrize($result, $bet['amount'], $bet['odds'], $totalPrize);
        }

        foreach ($this->otherBets as $bet) {
            $amount = (float)$bet['je'];
            $odds = (float)$bet['peilv1'];
            $resultType = $this->determineBetResult($bet, $normalCodes, $specialCode, $specialZodiac, $all7Numbers);
            $this->accumulatePrize($resultType, $amount, $odds, $totalPrize);
        }

        return [
            'total_profit' => $this->totalBetAmount - $totalPrize,
            'total_prize' => $totalPrize,
        ];
    }

    protected function accumulatePrize(string $resultType, float $amount, float $odds, float &$totalPrize): void
    {
        if ($resultType === 'win') {
            $totalPrize += $amount * $odds;
        } elseif ($resultType === 'draw') {
            $totalPrize += $amount;
        }
    }

    protected function determineBetResult(array $bet, array $normalCodes, int $specialCode, string $specialZodiac, array $allNumbers): string
    {
        $methodName = $bet['method_name'] ?? ($this->playNameCache[$bet['bid']] ?? '');
        $betType = $bet['bet_type'] ?? 'win';
        $content = (string)($bet['content'] ?? '');
        $rawItems = array_values(array_filter(array_map('trim', explode(',', $content)), 'strlen'));

        if ($this->containsKeyword($methodName, self::KEYWORDS_SPECIAL_NUMBER)) {
            $betNumbers = array_map('intval', $rawItems);
            $hit = in_array($specialCode, $betNumbers, true);
            return $this->resolveResult($hit, $betType);
        }

        if ($this->containsKeyword($methodName, self::KEYWORDS_NORMAL_NUMBER)) {
            $betNumbers = array_map('intval', $rawItems);
            $hit = !empty(array_intersect($betNumbers, $allNumbers));
            return $this->resolveResult($hit, $betType);
        }

        $missRule = $this->getNumberMissRule($methodName);
        if ($missRule) {
            $betNumbers = $this->parseNumberSelections($content);
            if (count($betNumbers) !== $missRule['select_count']) {
                return 'lose';
            }
            $hitCount = count(array_intersect($betNumbers, $allNumbers));
            return $this->resolveResult($hitCount === 0, $betType);
        }

        $comboRule = $this->getNumberComboRule($methodName);
        if ($comboRule) {
            $betNumbers = $this->parseNumberSelections($content);
            if (count($betNumbers) !== $comboRule['select_count']) {
                return 'lose';
            }
            $normalCodes = array_values(array_diff($allNumbers, [$specialCode]));
            $hitCount = count(array_intersect($betNumbers, $normalCodes));
            return $this->resolveResult($hitCount >= $comboRule['hit_count'], $betType);
        }

        if ($this->containsKeyword($methodName, self::KEYWORDS_SPECIAL_ZODIAC)) {
            $betZodiacs = ZodiacService::normalizeZodiacSelections($rawItems, $this->year);
            if (empty($betZodiacs)) {
                return 'lose';
            }
            $hit = $specialZodiac !== '' && in_array($specialZodiac, $betZodiacs, true);
            return $this->resolveResult($hit, $betType);
        }

        if ($this->containsKeyword($methodName, self::KEYWORDS_ANY_ZODIAC)) {
            $betZodiacs = ZodiacService::normalizeZodiacSelections($rawItems, $this->year);
            if (empty($betZodiacs)) {
                return 'lose';
            }
            $drawnZodiacs = ZodiacService::convertNumbersToZodiacsWithYear($allNumbers, $this->year);
            $hit = !empty(array_intersect($betZodiacs, $drawnZodiacs));
            return $this->resolveResult($hit, $betType);
        }

        if ($this->containsKeyword($methodName, self::KEYWORDS_SPECIAL_ODD_EVEN)) {
            $selection = $this->normalizeOddEvenSelection($rawItems, $content);
            if (!$selection) {
                return 'lose';
            }
            if ($specialCode === 49) {
                return 'draw';
            }
            $actual = ($specialCode % 2 === 0) ? 'even' : 'odd';
            return $this->resolveResult($selection === $actual, $betType);
        }

        if ($this->containsKeyword($methodName, self::KEYWORDS_DIGIT_ODD_EVEN)) {
            $selection = $this->normalizeOddEvenSelection($rawItems, $content);
            if (!$selection) {
                return 'lose';
            }
            if ($specialCode === 49) {
                return 'draw';
            }
            $hesu = $this->calculateHesuValue($specialCode);
            $actual = ($hesu % 2 === 0) ? 'even' : 'odd';
            return $this->resolveResult($selection === $actual, $betType);
        }

        if ($this->containsKeyword($methodName, self::KEYWORDS_TOTAL_ODD_EVEN)) {
            $selection = $this->normalizeOddEvenSelection($rawItems, $content);
            if (!$selection) {
                return 'lose';
            }
            $totalSum = array_sum($allNumbers);
            $actual = ($totalSum % 2 === 0) ? 'even' : 'odd';
            return $this->resolveResult($selection === $actual, $betType);
        }

        if ($this->containsKeyword($methodName, self::KEYWORDS_MULTI_ZODIAC)) {
            $betZodiacs = ZodiacService::normalizeZodiacSelections($rawItems, $this->year);
            if (empty($betZodiacs)) {
                return 'lose';
            }
            $multiResult = ZodiacService::checkMultiZodiacWin($betZodiacs, $allNumbers, $this->year);
            if ($specialCode === 49) {
                return 'draw';
            }
            return $this->resolveResult($multiResult['is_win'], $betType);
        }

        if ($this->containsKeyword($methodName, self::KEYWORDS_POSITIVE_ZODIAC)) {
            $betZodiacs = ZodiacService::normalizeZodiacSelections($rawItems, $this->year);
            if (empty($betZodiacs)) {
                return 'lose';
            }
            $drawnZodiacs = ZodiacService::convertNumbersToZodiacsWithYear($allNumbers, $this->year);
            $hit = !empty(array_intersect($betZodiacs, $drawnZodiacs));
            return $this->resolveResult($hit, $betType);
        }

        return 'lose';
    }

    protected function resolveResult(bool $hit, string $betType): string
    {
        if ($betType === 'not_win') {
            return $hit ? 'lose' : 'win';
        }
        return $hit ? 'win' : 'lose';
    }

    protected function getNumberComboRule(string $methodName): ?array
    {
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

    protected function getNumberMissRule(string $methodName): ?array
    {
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
            ];
        }

        return null;
    }

    protected function parseNumberSelections(string $content): array
    {
        $parts = preg_split('/[,\s，、;-]+/u', $content);
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

    protected function containsKeyword(string $haystack, array $keywords): bool
    {
        $haystack = (string)$haystack;
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

    protected function normalizeOddEvenSelection(array $items, string $originalContent): ?string
    {
        $candidates = $items;
        if (empty($candidates) && trim($originalContent) !== '') {
            $candidates = [trim($originalContent)];
        }

        foreach ($candidates as $candidate) {
            $candidate = trim($candidate);
            if ($candidate === '') {
                continue;
            }
            if ($this->containsKeyword($candidate, self::ODD_KEYWORDS)) {
                return 'odd';
            }
            if ($this->containsKeyword($candidate, self::EVEN_KEYWORDS)) {
                return 'even';
            }
        }

        return null;
    }

    protected function calculateHesuValue(int $number): int
    {
        $hesu = intdiv($number, 10) + ($number % 10);
        while ($hesu >= 10) {
            $hesu = intdiv($hesu, 10) + ($hesu % 10);
        }
        return $hesu;
    }

    protected function getSortedNumbers(array $weights, int $limit = 0): array
    {
        asort($weights, SORT_ASC);
        $sorted = array_keys($weights);
        if ($limit > 0) {
            $sorted = array_slice($sorted, 0, $limit);
        }
        return $sorted;
    }

    protected function ensureNormalPool(array $current, int $specialCode): array
    {
        $current = array_values(array_unique($current));
        $current = array_filter($current, fn($num) => $num >= 1 && $num <= 49 && $num !== $specialCode);

        if (count($current) >= $this->normalPoolLimit) {
            return array_slice(array_values($current), 0, $this->normalPoolLimit);
        }

        for ($i = 1; $i <= 49; $i++) {
            if ($i === $specialCode) {
                continue;
            }
            if (!in_array($i, $current, true)) {
                $current[] = $i;
            }
            if (count($current) >= $this->normalPoolLimit) {
                break;
            }
        }

        return array_slice(array_values($current), 0, $this->normalPoolLimit);
    }

    protected function generateCombinationsLimited(array $numbers, int $choose, int $limit): array
    {
        $result = [];
        $count = count($numbers);
        if ($choose <= 0 || $count < $choose) {
            return $result;
        }

        $indexes = range(0, $choose - 1);
        while (true) {
            $combo = [];
            foreach ($indexes as $idx) {
                $combo[] = $numbers[$idx];
            }
            $result[] = $combo;
            if ($limit > 0 && count($result) >= $limit) {
                break;
            }

            $i = $choose - 1;
            while ($i >= 0 && $indexes[$i] === $i + $count - $choose) {
                $i--;
            }
            if ($i < 0) {
                break;
            }
            $indexes[$i]++;
            for ($j = $i + 1; $j < $choose; $j++) {
                $indexes[$j] = $indexes[$j - 1] + 1;
            }
        }

        return $result;
    }

    protected function isComboWithinMaxConsecutive(array $combo, int $specialCode, ?int $maxConsecutive): bool
    {
        if ($maxConsecutive === null || $maxConsecutive <= 0) {
            return true;
        }

        $numbers = $combo;
        $numbers[] = $specialCode;
        return $this->getMaxConsecutive($numbers, $maxConsecutive) <= $maxConsecutive;
    }

    protected function getMaxConsecutive(array $numbers, ?int $limit = null): int
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
                if ($current > $max) {
                    $max = $current;
                    if ($limit !== null && $max > $limit) {
                        return $max;
                    }
                }
            } else {
                $current = 1;
            }
        }

        return $max;
    }

    protected function expandZodiacsToNumbers(array $zodiacs): array
    {
        $numbers = [];
        foreach ($zodiacs as $zodiac) {
            if (isset($this->zodiacTable[$zodiac])) {
                $numbers = array_merge($numbers, $this->zodiacTable[$zodiac]);
            }
        }
        return array_values(array_unique($numbers));
    }

    protected function buildRiskWarning(?array $bestSolution): ?array
    {
        if (!$bestSolution) {
            return null;
        }
        if ($bestSolution['total_profit'] >= 0) {
            return null;
        }
        return [
            'level' => 'danger',
            'message' => '当前最佳组合仍为亏损，请谨慎开奖',
            'suggestion' => '建议延迟开奖或调整赔率 / 封盘策略',
        ];
    }

    protected function buildRecommendations(?array $bestSolution): array
    {
        if (!$bestSolution) {
            return ['该期暂无有效方案, 请等待更多投注'];
        }

        $profitRate = $bestSolution['profit_rate'];
        if ($profitRate < 0) {
            return [
                '当前方案为亏损(' . number_format($profitRate, 2) . '%)，建议延迟开奖或继续吸收投注',
                '也可接受本次亏损作为活动成本，记得通知风控关注热号',
            ];
        }
        if ($profitRate < 10) {
            return [
                '利润率偏低(' . number_format($profitRate, 2) . '%)，可考虑调低热号赔率或提高投注门槛',
            ];
        }
        return [
            '利润率正常(' . number_format($profitRate, 2) . '%)，可以正常开奖',
        ];
    }

    protected function buildRiskAssessment(?array $bestSolution): ?array
    {
        if (!$bestSolution) {
            return null;
        }

        $profitRate = $bestSolution['profit_rate'];
        $level = 'danger';
        $text = '高风险';
        $overall = 0.8;
        if ($profitRate >= 30) {
            $level = 'safe';
            $text = '低风险';
            $overall = 0.1;
        } elseif ($profitRate >= 10) {
            $level = 'warning';
            $text = '中风险';
            $overall = 0.35;
        } elseif ($profitRate >= 0) {
            $level = 'warning';
            $text = '中风险';
            $overall = 0.6;
        }

        return [
            'risk_level' => $level,
            'risk_level_text' => $text,
            'overall_risk' => $overall,
            'profit_rate' => $profitRate,
            'can_profit' => $bestSolution['total_profit'] >= 0,
            'total_bets' => $this->totalBetAmount,
            'total_orders' => count($this->allBets),
        ];
    }

    public function getBetCount(): int
    {
        return count($this->allBets);
    }

    public function getWeightsAnalysis(): array
    {
        $specialSorted = $this->specialCodeWeights;
        asort($specialSorted);

        $normalSorted = $this->normalCodeWeights;
        asort($normalSorted);

        return [
            'special_weights' => [
                'top_10_best' => array_slice($specialSorted, 0, 10, true),
                'top_10_worst' => array_slice(array_reverse($specialSorted, true), 0, 10, true),
            ],
            'normal_weights' => [
                'top_10_best' => array_slice($normalSorted, 0, 10, true),
                'top_10_worst' => array_slice(array_reverse($normalSorted, true), 0, 10, true),
            ],
        ];
    }

    /**
     * 区间筛选模式：查找利润率在指定范围内的所有方案
     * 结合扩大采样策略，包含权重排序 + 分层随机 + 纯随机采样
     *
     * @param float|null $minRate 最小利润率（百分比），null表示不限
     * @param float|null $maxRate 最大利润率（百分比），null表示不限
     * @return array
     */
    public function findByProfitRange(
        ?float $minRate = null,
        ?float $maxRate = null,
        bool $includeAll = false,
        ?int $maxConsecutive = null
    ): array
    {
        if (empty($this->allBets)) {
            return [
                'matched_solutions' => [],
                'total_evaluated' => 0,
                'total_bets' => 0,
                'total_orders' => 0,
                'filter_criteria' => ['min_rate' => $minRate, 'max_rate' => $maxRate],
                'recommendations' => ['no_bets' => true, 'message' => 'No betting data available'],
            ];
        }

        $specialCandidates = $this->getSortedNumbers($this->specialCodeWeights, $this->specialCandidateLimit);
        if (empty($specialCandidates)) {
            $specialCandidates = range(1, 49);
        }
        if (!in_array(49, $specialCandidates, true)) {
            $specialCandidates[] = 49;
        }

        $normalSorted = $this->getSortedNumbers($this->normalCodeWeights, $this->normalPoolLimit);
        if (empty($normalSorted)) {
            $normalSorted = range(1, 49);
        }

        $allSolutions = [];
        $seen = [];

        // Phase 1: Weight-based systematic search
        foreach ($specialCandidates as $specialCode) {
            $normalPool = array_values(array_filter($normalSorted, fn($num) => $num !== $specialCode));
            $normalPool = $this->ensureNormalPool($normalPool, $specialCode);
            if (count($normalPool) < 6) {
                continue;
            }

            $combinations = $this->generateCombinationsLimited($normalPool, 6, $this->maxCombosPerSpecial);
            foreach ($combinations as $combo) {
                $solution = $this->evaluateSolutionWithRange(
                    $combo,
                    $specialCode,
                    $minRate,
                    $maxRate,
                    $seen,
                    $maxConsecutive
                );
                if ($solution !== null) {
                    $allSolutions[] = $solution;
                }
            }
        }

        // Phase 2: Layered random sampling (by risk tier)
        $this->addLayeredSamples($allSolutions, $seen, $minRate, $maxRate, $maxConsecutive);

        // Phase 3: Pure random sampling
        $this->addRandomSamples($allSolutions, $seen, $minRate, $maxRate, $maxConsecutive);

        // Sort by distance to target range center
        $targetCenter = null;
        if ($minRate !== null && $maxRate !== null) {
            $targetCenter = ($minRate + $maxRate) / 2;
        } elseif ($minRate !== null) {
            $targetCenter = $minRate + 10;
        } elseif ($maxRate !== null) {
            $targetCenter = $maxRate - 10;
        }

        if ($targetCenter !== null) {
            usort($allSolutions, function ($a, $b) use ($targetCenter) {
                $distA = abs($a['profit_rate'] - $targetCenter);
                $distB = abs($b['profit_rate'] - $targetCenter);
                if ($distA === $distB) {
                    return $b['total_profit'] <=> $a['total_profit'];
                }
                return $distA <=> $distB;
            });
        } else {
            usort($allSolutions, fn($a, $b) => $b['total_profit'] <=> $a['total_profit']);
        }

        $matchedSolutions = array_filter($allSolutions, function ($sol) use ($minRate, $maxRate) {
            $rate = $sol['profit_rate'];
            if ($minRate !== null && $rate < $minRate) {
                return false;
            }
            if ($maxRate !== null && $rate > $maxRate) {
                return false;
            }
            return true;
        });

        $matchedSolutions = array_values($matchedSolutions);
        $recommendations = $this->buildRecommendationsWithRange($matchedSolutions, $minRate, $maxRate, count($allSolutions));

        $response = [
            'matched_solutions' => array_slice($matchedSolutions, 0, self::TOP_SOLUTION_LIMIT),
            'total_matched' => count($matchedSolutions),
            'total_evaluated' => count($allSolutions),
            'total_bets' => $this->totalBetAmount,
            'total_orders' => count($this->allBets),
            'filter_criteria' => ['min_rate' => $minRate, 'max_rate' => $maxRate],
            'best_in_range' => $matchedSolutions[0] ?? null,
            'recommendations' => $recommendations,
        ];

        if ($includeAll) {
            $response['all_solutions'] = $allSolutions;
        }

        return $response;
    }

    /**
     * Evaluate a solution and check if it falls within range
     */
    protected function evaluateSolutionWithRange(
        array $combo,
        int $specialCode,
        ?float $minRate,
        ?float $maxRate,
        array &$seen,
        ?int $maxConsecutive = null
    ): ?array {
        if (!$this->isComboWithinMaxConsecutive($combo, $specialCode, $maxConsecutive)) {
            return null;
        }
        sort($combo);
        $key = implode('-', $combo) . '-' . $specialCode;
        if (isset($seen[$key])) {
            return null;
        }
        $seen[$key] = true;

        $combined = $this->calculateCombinedProfit($combo, $specialCode);
        $profit = $combined['total_profit'];
        $profitRate = $this->totalBetAmount > 0
            ? round(($profit / $this->totalBetAmount) * 100, 2)
            : 0.0;

        return [
            'm1_m6' => $combo,
            'm7' => $specialCode,
            'total_profit' => round($profit, 2),
            'total_prize' => round($combined['total_prize'], 2),
            'bet_amount' => $this->totalBetAmount,
            'profit_rate' => $profitRate,
            'special_weight' => $this->specialCodeWeights[$specialCode] ?? 0,
            'normal_weights' => array_sum(array_map(fn($n) => $this->normalCodeWeights[$n] ?? 0, $combo)),
            'in_range' => ($minRate === null || $profitRate >= $minRate) && ($maxRate === null || $profitRate <= $maxRate),
        ];
    }

    /**
     * Add layered random samples based on risk tiers
     */
    protected function addLayeredSamples(
        array &$solutions,
        array &$seen,
        ?float $minRate,
        ?float $maxRate,
        ?int $maxConsecutive = null
    ): void
    {
        $specialWeightsSorted = $this->specialCodeWeights;
        asort($specialWeightsSorted);
        $sortedKeys = array_keys($specialWeightsSorted);

        $tiers = [
            'low' => array_slice($sortedKeys, 0, 16),
            'medium' => array_slice($sortedKeys, 16, 17),
            'high' => array_slice($sortedKeys, 33, 16),
        ];

        foreach ($tiers as $tierName => $tierNumbers) {
            for ($i = 0; $i < self::LAYERED_SAMPLE_PER_TIER; $i++) {
                if (empty($tierNumbers)) {
                    continue;
                }
                $specialCode = $tierNumbers[array_rand($tierNumbers)];
                $combo = $this->generateRandomCombo($specialCode);
                if ($combo === null) {
                    continue;
                }

                $solution = $this->evaluateSolutionWithRange(
                    $combo,
                    $specialCode,
                    $minRate,
                    $maxRate,
                    $seen,
                    $maxConsecutive
                );
                if ($solution !== null) {
                    $solution['sample_tier'] = $tierName;
                    $solutions[] = $solution;
                }
            }
        }
    }

    /**
     * Add pure random samples
     */
    protected function addRandomSamples(
        array &$solutions,
        array &$seen,
        ?float $minRate,
        ?float $maxRate,
        ?int $maxConsecutive = null
    ): void
    {
        for ($i = 0; $i < self::RANDOM_SAMPLE_COUNT; $i++) {
            $specialCode = mt_rand(1, 49);
            $combo = $this->generateRandomCombo($specialCode);
            if ($combo === null) {
                continue;
            }

            $solution = $this->evaluateSolutionWithRange(
                $combo,
                $specialCode,
                $minRate,
                $maxRate,
                $seen,
                $maxConsecutive
            );
            if ($solution !== null) {
                $solution['sample_tier'] = 'random';
                $solutions[] = $solution;
            }
        }
    }

    /**
     * Generate a random 6-number combination excluding the special code
     */
    protected function generateRandomCombo(int $specialCode): ?array
    {
        $pool = range(1, 49);
        $pool = array_values(array_filter($pool, fn($n) => $n !== $specialCode));

        if (count($pool) < 6) {
            return null;
        }

        shuffle($pool);
        $combo = array_slice($pool, 0, 6);
        sort($combo);

        return $combo;
    }

    /**
     * Build recommendations for range-based filtering
     */
    protected function buildRecommendationsWithRange(array $matched, ?float $minRate, ?float $maxRate, int $totalEvaluated): array
    {
        $recommendations = [];

        if (empty($matched)) {
            $recommendations[] = [
                'type' => 'no_match',
                'message' => "No solutions found within range [{$minRate}% - {$maxRate}%]",
                'suggestion' => 'Consider widening the profit rate range or adjusting odds',
            ];

            if ($minRate !== null && $minRate > 50) {
                $recommendations[] = [
                    'type' => 'high_target',
                    'message' => 'Target profit rate is very high',
                    'suggestion' => 'Current betting distribution may not support such high profit rates',
                ];
            }

            return $recommendations;
        }

        $bestMatch = $matched[0];
        $avgRate = count($matched) > 0
            ? array_sum(array_column($matched, 'profit_rate')) / count($matched)
            : 0;

        $recommendations[] = [
            'type' => 'summary',
            'matched_count' => count($matched),
            'total_evaluated' => $totalEvaluated,
            'best_rate' => $bestMatch['profit_rate'],
            'average_rate' => round($avgRate, 2),
        ];

        if ($bestMatch['profit_rate'] < 0) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'Best matching solution still results in loss',
                'suggestion' => 'Review betting patterns or adjust odds',
            ];
        } elseif ($bestMatch['profit_rate'] < 10) {
            $recommendations[] = [
                'type' => 'caution',
                'message' => 'Profit rate is relatively low',
                'suggestion' => 'Consider if this meets business requirements',
            ];
        } else {
            $recommendations[] = [
                'type' => 'ok',
                'message' => 'Solution within acceptable profit range',
            ];
        }

        return $recommendations;
    }
}
