<?php
declare(strict_types=1);

namespace app\common\service;

/**
 * 统一处理扩展玩法规则，供下单、结算、实时分析共用。
 */
class LotteryPlayRuleService
{
    private const ZODIAC_ALIASES = [
        '馬' => '马',
        '雞' => '鸡',
        '龍' => '龙',
        '豬' => '猪',
    ];

    private const OPTION_ALIASES = [
        '單' => '单',
        '雙' => '双',
        '兰' => '蓝',
        '兰波' => '蓝波',
        '兰单' => '蓝单',
        '兰双' => '蓝双',
        '蘭' => '蓝',
        '蘭波' => '蓝波',
        '蘭單' => '蓝单',
        '蘭双' => '蓝双',
        '蘭雙' => '蓝双',
    ];

    private const DOMESTIC_ZODIACS = ['牛', '马', '羊', '鸡', '狗', '猪'];
    private const WILD_ZODIACS = ['鼠', '虎', '兔', '龙', '蛇', '猴'];
    private const SIX_SPECIAL_ZODIAC_ODDS = 1.95;

    private const COLOR_MAP = [
        'red' => [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46],
        'blue' => [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48],
        'green' => [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49],
    ];

    public static function getLianXiaoRule(string $methodName, string $methodCode = ''): ?array
    {
        $code = strtolower(trim($methodCode));
        $codeMap = [
            'lianxiao2' => [2, 4.96, 4.00],
            'lianxiao3' => [3, 12.70, 10.00],
            'lianxiao4' => [4, 36.70, 30.00],
            'lianxiao5' => [5, 128.00, 97.90],
        ];
        if (isset($codeMap[$code])) {
            return self::buildLianXiaoRule($codeMap[$code]);
        }

        $normalized = str_replace([' ', '　', '-', '_'], '', trim($methodName));
        $nameMap = [
            '2连肖' => [2, 4.96, 4.00],
            '二连肖' => [2, 4.96, 4.00],
            '2連肖' => [2, 4.96, 4.00],
            '二連肖' => [2, 4.96, 4.00],
            '3连肖' => [3, 12.70, 10.00],
            '三连肖' => [3, 12.70, 10.00],
            '3連肖' => [3, 12.70, 10.00],
            '三連肖' => [3, 12.70, 10.00],
            '4连肖' => [4, 36.70, 30.00],
            '四连肖' => [4, 36.70, 30.00],
            '4連肖' => [4, 36.70, 30.00],
            '四連肖' => [4, 36.70, 30.00],
            '5连肖' => [5, 128.00, 97.90],
            '五连肖' => [5, 128.00, 97.90],
            '5連肖' => [5, 128.00, 97.90],
            '五連肖' => [5, 128.00, 97.90],
        ];

        foreach ($nameMap as $keyword => $rule) {
            if ($normalized === $keyword || strpos($normalized, $keyword) !== false) {
                return self::buildLianXiaoRule($rule);
            }
        }

        return null;
    }

    public static function getSpecialOptionPlay(string $methodName, string $methodCode = ''): ?array
    {
        $code = strtolower(trim($methodCode));
        $normalized = str_replace([' ', '　', '-', '_'], '', trim($methodName));

        $map = [
            'tema_daxiao_danshuang' => ['type' => 'tema_daxiao_danshuang', 'name' => '特码大小单双'],
            'jiaqin_yeshou' => ['type' => 'jiaqin_yeshou', 'name' => '家禽野兽'],
            'hedan_heshuang' => ['type' => 'hedan_heshuang', 'name' => '合单合双'],
            'bose' => ['type' => 'bose', 'name' => '波色'],
            'banbo' => ['type' => 'banbo', 'name' => '半波'],
        ];

        if (isset($map[$code])) {
            return $map[$code];
        }

        $nameMap = [
            '特码大小单双' => 'tema_daxiao_danshuang',
            '特碼大小單雙' => 'tema_daxiao_danshuang',
            '特碼大小单双' => 'tema_daxiao_danshuang',
            '家禽野兽' => 'jiaqin_yeshou',
            '家禽野獸' => 'jiaqin_yeshou',
            '合单合双' => 'hedan_heshuang',
            '合單合雙' => 'hedan_heshuang',
            '波色' => 'bose',
            '半波' => 'banbo',
        ];

        foreach ($nameMap as $keyword => $mappedCode) {
            if ($normalized === $keyword || strpos($normalized, $keyword) !== false) {
                return $map[$mappedCode];
            }
        }

        return null;
    }

    public static function getSixSpecialZodiacRule(string $methodName, string $methodCode = ''): ?array
    {
        $code = strtolower(trim($methodCode));
        if ($code === 'liuxiao') {
            return self::buildSixSpecialZodiacRule();
        }

        $normalized = str_replace([' ', '　', '-', '_'], '', trim($methodName));
        $nameMap = ['六肖', '6肖', '6肖中特', '六肖中特'];
        foreach ($nameMap as $keyword) {
            if ($normalized === $keyword || strpos($normalized, $keyword) !== false) {
                return self::buildSixSpecialZodiacRule();
            }
        }

        return null;
    }

    public static function getNumberComboRule(string $methodName, string $methodCode = ''): ?array
    {
        $code = strtolower(trim($methodCode));
        if (in_array($code, ['liuzhongyi', 'six_pick_one'], true)) {
            return self::buildNumberComboRule(6, 1, 'all7');
        }

        $normalized = str_replace([' ', '　', '-', '_'], '', trim($methodName));
        $nameMap = ['6中1', '六中一'];
        foreach ($nameMap as $keyword) {
            if ($normalized === $keyword || strpos($normalized, $keyword) !== false) {
                return self::buildNumberComboRule(6, 1, 'all7');
            }
        }

        return null;
    }

    public static function getSpecialOptionList(array $playMethod): array
    {
        $play = self::getSpecialOptionPlay((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        if (!$play) {
            return [];
        }

        $config = self::decodePrizeConfig($playMethod['prize_config'] ?? null);
        $optionOdds = $config['option_odds'] ?? [];

        $optionsByType = [
            'tema_daxiao_danshuang' => ['单', '双', '大', '小'],
            'jiaqin_yeshou' => ['家禽', '野兽'],
            'hedan_heshuang' => ['合单', '合双'],
            'bose' => ['红波', '蓝波', '绿波'],
            'banbo' => ['绿双', '红单', '蓝单', '蓝双', '绿单', '红双'],
        ];

        $options = [];
        foreach ($optionsByType[$play['type']] ?? [] as $option) {
            $odds = self::getConfiguredOptionOdds($optionOdds, $option, (float)($playMethod['odds_default'] ?? 0));
            $options[] = [
                'value' => $option,
                'label' => $option,
                'odds' => number_format($odds, 4, '.', ''),
                'odds_win' => number_format($odds, 4, '.', ''),
                'odds_not_win' => number_format(max(0, $odds - 1), 4, '.', ''),
            ];
        }

        return $options;
    }

    public static function resolveBetOdds(array $playMethod, string $betContent): ?float
    {
        $sixSpecialRule = self::getSixSpecialZodiacRule((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        if ($sixSpecialRule) {
            return (float)$sixSpecialRule['odds'];
        }

        $numberComboRule = self::getNumberComboRule((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        if ($numberComboRule) {
            return (float)($playMethod['odds_default'] ?? 0);
        }

        $lianXiaoRule = self::getLianXiaoRule((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        if ($lianXiaoRule) {
            $zodiacs = self::parseZodiacSelections($betContent, (int)date('Y'));
            return in_array('马', $zodiacs, true)
                ? (float)$lianXiaoRule['with_horse_odds']
                : (float)$lianXiaoRule['odds'];
        }

        $specialPlay = self::getSpecialOptionPlay((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        if ($specialPlay) {
            $config = self::decodePrizeConfig($playMethod['prize_config'] ?? null);
            return self::getConfiguredOptionOdds($config['option_odds'] ?? [], $betContent, (float)($playMethod['odds_default'] ?? 0));
        }

        return null;
    }

    public static function validateBetContent(array $playMethod, string $betContent): ?string
    {
        $sixSpecialRule = self::getSixSpecialZodiacRule((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        if ($sixSpecialRule) {
            $zodiacs = self::parseZodiacSelections($betContent, (int)date('Y'));
            if (count($zodiacs) !== $sixSpecialRule['select_count']) {
                return sprintf('%s玩法必须选择%d个不重复生肖', $playMethod['name'], $sixSpecialRule['select_count']);
            }
            return null;
        }

        $numberComboRule = self::getNumberComboRule((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        if ($numberComboRule) {
            $numbers = self::parseNumberSelections($betContent);
            if (count($numbers) !== $numberComboRule['select_count']) {
                return sprintf('%s玩法必须选择%d个不重复号码', $playMethod['name'], $numberComboRule['select_count']);
            }
            return null;
        }

        $lianXiaoRule = self::getLianXiaoRule((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        if ($lianXiaoRule) {
            $zodiacs = self::parseZodiacSelections($betContent, (int)date('Y'));
            if (count($zodiacs) !== $lianXiaoRule['select_count']) {
                return sprintf('%s玩法必须选择%d个不重复生肖', $playMethod['name'], $lianXiaoRule['select_count']);
            }
            return null;
        }

        $specialPlay = self::getSpecialOptionPlay((string)$playMethod['name'], (string)($playMethod['code'] ?? ''));
        if ($specialPlay) {
            $options = array_column(self::getSpecialOptionList($playMethod), 'value');
            $normalized = self::normalizeOption($betContent);
            $validOptions = array_map([self::class, 'normalizeOption'], $options);
            if (!in_array($normalized, $validOptions, true)) {
                return sprintf('%s玩法不支持选项: %s', $playMethod['name'], $betContent);
            }
        }

        return null;
    }

    public static function determineResult(
        string $methodName,
        string $methodCode,
        string $betContent,
        array $drawnNumbers,
        int $year,
        string $betType = 'win'
    ): ?string {
        $sixSpecialRule = self::getSixSpecialZodiacRule($methodName, $methodCode);
        if ($sixSpecialRule) {
            $special = self::getSpecialNumber($drawnNumbers);
            if ($special === 49) {
                return 'draw';
            }

            $betZodiacs = self::parseZodiacSelections($betContent, $year);
            if (count($betZodiacs) !== $sixSpecialRule['select_count']) {
                return 'lose';
            }

            $specialZodiac = ZodiacYearService::getZodiacByNumberAndYear($special, $year);
            return self::resolveResult(in_array($specialZodiac, $betZodiacs, true), $betType);
        }

        $numberComboRule = self::getNumberComboRule($methodName, $methodCode);
        if ($numberComboRule) {
            $betNumbers = self::parseNumberSelections($betContent);
            if (count($betNumbers) !== $numberComboRule['select_count']) {
                return 'lose';
            }

            $drawNumbers = self::normalizeDrawnNumbers($drawnNumbers);
            $hitCount = count(array_intersect($betNumbers, $drawNumbers));
            return self::resolveResult($hitCount >= $numberComboRule['hit_count'], $betType);
        }

        $specialPlay = self::getSpecialOptionPlay($methodName, $methodCode);
        if ($specialPlay) {
            $special = self::getSpecialNumber($drawnNumbers);
            $hitResult = self::checkSpecialOptionWin($specialPlay['type'], $betContent, $special, $year);
            if ($hitResult === 'draw') {
                return 'draw';
            }
            return self::resolveResult($hitResult === 'win', $betType);
        }

        $lianXiaoRule = self::getLianXiaoRule($methodName, $methodCode);
        if ($lianXiaoRule) {
            $betZodiacs = self::parseZodiacSelections($betContent, $year);
            if (count($betZodiacs) !== $lianXiaoRule['select_count']) {
                return 'lose';
            }

            $allNumbers = self::normalizeDrawnNumbers($drawnNumbers);
            $drawnZodiacs = ZodiacService::convertNumbersToZodiacsWithYear($allNumbers, $year);
            $hit = count(array_intersect($betZodiacs, $drawnZodiacs)) === $lianXiaoRule['select_count'];
            return self::resolveResult($hit, $betType);
        }

        return null;
    }

    public static function getLianXiaoOptions(string $playName, array $playMethod, int $year, string $plateCode = ''): array
    {
        $rule = self::getLianXiaoRule($playName, (string)($playMethod['code'] ?? ''));
        $zodiacConfig = config('zodiac_base_year');
        $zodiacOrder = $zodiacConfig['zodiac_order'];
        $currentYearTable = ZodiacYearService::getZodiacTableByYear($year);
        $currentYearZodiac = ZodiacYearService::getYearZodiac($year);

        $options = [];
        foreach ($zodiacOrder as $zodiac) {
            $normalized = self::normalizeZodiac($zodiac);
            $odds = $normalized === '马' ? (float)$rule['with_horse_odds'] : (float)$rule['odds'];
            $options[] = [
                'value' => $normalized,
                'label' => $normalized,
                'odds' => number_format($odds, 4, '.', ''),
                'odds_win' => number_format($odds, 4, '.', ''),
                'odds_not_win' => number_format(max(0, $odds - 1), 4, '.', ''),
                'numbers' => array_map(fn($n) => str_pad((string)$n, 2, '0', STR_PAD_LEFT), $currentYearTable[$zodiac] ?? []),
                'count' => count($currentYearTable[$zodiac] ?? []),
                'is_current_year' => $normalized === $currentYearZodiac,
                'category' => in_array($normalized, self::DOMESTIC_ZODIACS, true) ? 'domestic' : 'wild',
                'category_label' => in_array($normalized, self::DOMESTIC_ZODIACS, true) ? '家禽' : '野兽',
            ];
        }

        return [
            'play_name' => $playName,
            'play_type' => 'zodiac_combo',
            'year' => $year,
            'plate_code' => $plateCode,
            'year_zodiac' => $currentYearZodiac,
            'select_count' => $rule['select_count'],
            'hit_count' => $rule['select_count'],
            'combo_mode' => 'zodiac_combo',
            'total_options' => 12,
            'odds' => number_format((float)$rule['odds'], 4, '.', ''),
            'with_horse_odds' => number_format((float)$rule['with_horse_odds'], 4, '.', ''),
            'options' => $options,
            'special_rules' => [
                'judge_scope' => '按全部7个开奖号码判断',
                'win_rule' => '所选每个生肖在7个开奖号码中都至少出现一次即中奖',
                'rule_49' => '49按正常生肖号码判断，若选马且开49则算中奖',
            ],
        ];
    }

    public static function getSixSpecialZodiacOptions(string $playName, array $playMethod, int $year, string $plateCode = ''): array
    {
        $rule = self::getSixSpecialZodiacRule($playName, (string)($playMethod['code'] ?? ''));
        $zodiacConfig = config('zodiac_base_year');
        $zodiacOrder = $zodiacConfig['zodiac_order'];
        $currentYearTable = ZodiacYearService::getZodiacTableByYear($year);
        $currentYearZodiac = ZodiacYearService::getYearZodiac($year);
        $odds = number_format((float)($rule['odds'] ?? self::SIX_SPECIAL_ZODIAC_ODDS), 4, '.', '');

        $options = [];
        foreach ($zodiacOrder as $zodiac) {
            $normalized = self::normalizeZodiac($zodiac);
            $options[] = [
                'value' => $normalized,
                'label' => $normalized,
                'odds' => $odds,
                'odds_win' => $odds,
                'odds_not_win' => number_format(max(0, (float)$odds - 1), 4, '.', ''),
                'numbers' => array_map(fn($n) => str_pad((string)$n, 2, '0', STR_PAD_LEFT), $currentYearTable[$zodiac] ?? []),
                'count' => count($currentYearTable[$zodiac] ?? []),
                'is_current_year' => $normalized === $currentYearZodiac,
                'category' => in_array($normalized, self::DOMESTIC_ZODIACS, true) ? 'domestic' : 'wild',
                'category_label' => in_array($normalized, self::DOMESTIC_ZODIACS, true) ? '家禽' : '野兽',
            ];
        }

        return [
            'play_name' => $playName,
            'play_type' => 'zodiac',
            'zodiac_mode' => 'six_special_zodiac',
            'year' => $year,
            'plate_code' => $plateCode,
            'year_zodiac' => $currentYearZodiac,
            'select_count' => $rule['select_count'] ?? 6,
            'hit_count' => 1,
            'total_options' => 12,
            'odds' => $odds,
            'odds_win' => $odds,
            'odds_not_win' => number_format(max(0, (float)$odds - 1), 4, '.', ''),
            'options' => $options,
            'special_rules' => [
                'judge_scope' => '只按第7个开奖号特码判断',
                'win_rule' => '特码生肖命中所选6个生肖中的任意一个即中奖',
                'rule_49' => '特码开49视为和局，退还本金',
            ],
        ];
    }

    public static function getSpecialOptionResponse(string $playName, array $playMethod, int $year, string $plateCode = ''): array
    {
        $specialPlay = self::getSpecialOptionPlay($playName, (string)($playMethod['code'] ?? ''));
        return [
            'play_name' => $playName,
            'play_type' => 'option',
            'option_mode' => $specialPlay['type'] ?? '',
            'year' => $year,
            'plate_code' => $plateCode,
            'total_options' => count(self::getSpecialOptionList($playMethod)),
            'odds' => number_format((float)($playMethod['odds_default'] ?? 0), 4, '.', ''),
            'options' => self::getSpecialOptionList($playMethod),
            'special_rules' => self::getSpecialOptionRules($specialPlay['type'] ?? ''),
        ];
    }

    public static function applyWeights(string $methodName, string $methodCode, string $betContent, float $weightedAmount, string $betType, int $year, array &$normalWeights, array &$specialWeights): bool
    {
        $direction = $betType === 'not_win' ? -1 : 1;
        $sixSpecialRule = self::getSixSpecialZodiacRule($methodName, $methodCode);
        if ($sixSpecialRule) {
            $zodiacs = self::parseZodiacSelections($betContent, $year);
            foreach ($zodiacs as $zodiac) {
                foreach (ZodiacYearService::getNumbersByZodiacAndYear($zodiac, $year) as $number) {
                    if ((int)$number === 49) {
                        continue;
                    }
                    $specialWeights[$number] += $direction * $weightedAmount;
                }
            }
            $specialWeights[49] += $direction * ($weightedAmount * 0.1);
            return true;
        }

        $specialPlay = self::getSpecialOptionPlay($methodName, $methodCode);
        if ($specialPlay) {
            for ($number = 1; $number <= 49; $number++) {
                $result = self::checkSpecialOptionWin($specialPlay['type'], $betContent, $number, $year);
                if ($result === 'win') {
                    $specialWeights[$number] += $direction * $weightedAmount;
                } elseif ($result === 'draw') {
                    $specialWeights[$number] += $direction * ($weightedAmount * 0.1);
                }
            }
            return true;
        }

        $rule = self::getLianXiaoRule($methodName, $methodCode);
        if ($rule) {
            $zodiacs = self::parseZodiacSelections($betContent, $year);
            foreach ($zodiacs as $zodiac) {
                foreach (ZodiacYearService::getNumbersByZodiacAndYear($zodiac, $year) as $number) {
                    $normalWeights[$number] += $direction * $weightedAmount;
                    $specialWeights[$number] += $direction * $weightedAmount;
                }
            }
            return true;
        }

        return false;
    }

    public static function normalizeOption(string $value): string
    {
        $value = trim($value);
        $value = strtr($value, self::OPTION_ALIASES + self::ZODIAC_ALIASES);
        return str_replace([' ', '　'], '', $value);
    }

    public static function normalizeZodiac(string $value): string
    {
        return strtr(trim($value), self::ZODIAC_ALIASES);
    }

    private static function checkSpecialOptionWin(string $type, string $betContent, int $specialNumber, int $year): string
    {
        $selection = self::normalizeOption($betContent);

        if (in_array($type, ['tema_daxiao_danshuang', 'jiaqin_yeshou', 'hedan_heshuang'], true) && $specialNumber === 49) {
            return 'draw';
        }

        switch ($type) {
            case 'tema_daxiao_danshuang':
                $actuals = [
                    $specialNumber % 2 === 0 ? '双' : '单',
                    $specialNumber >= 25 ? '大' : '小',
                ];
                return in_array($selection, $actuals, true) ? 'win' : 'lose';

            case 'jiaqin_yeshou':
                $zodiac = ZodiacYearService::getZodiacByNumberAndYear($specialNumber, $year);
                $actual = in_array($zodiac, self::DOMESTIC_ZODIACS, true) ? '家禽' : '野兽';
                return $selection === $actual ? 'win' : 'lose';

            case 'hedan_heshuang':
                $sum = intdiv($specialNumber, 10) + ($specialNumber % 10);
                $actual = ($sum % 2 === 0) ? '合双' : '合单';
                return $selection === $actual ? 'win' : 'lose';

            case 'bose':
                $actual = self::getColorLabel($specialNumber) . '波';
                return $selection === $actual ? 'win' : 'lose';

            case 'banbo':
                $actual = self::getColorLabel($specialNumber) . ($specialNumber % 2 === 0 ? '双' : '单');
                return $selection === $actual ? 'win' : 'lose';
        }

        return 'lose';
    }

    private static function getColorLabel(int $number): string
    {
        foreach (self::COLOR_MAP as $color => $numbers) {
            if (in_array($number, $numbers, true)) {
                return ['red' => '红', 'blue' => '蓝', 'green' => '绿'][$color];
            }
        }
        return '';
    }

    private static function getSpecialOptionRules(string $type): array
    {
        if (in_array($type, ['tema_daxiao_danshuang', 'jiaqin_yeshou', 'hedan_heshuang'], true)) {
            return ['rule_49' => '特码开49视为和局，退还本金'];
        }
        if ($type === 'bose') {
            return ['rule_49' => '49按绿波正常判奖'];
        }
        if ($type === 'banbo') {
            return ['rule_49' => '49按绿单正常判奖'];
        }
        return [];
    }

    private static function parseZodiacSelections(string $betContent, int $year): array
    {
        $parts = preg_split('/[,\s，、;-]+/u', $betContent);
        if ($parts === false) {
            return [];
        }
        $parts = array_map([self::class, 'normalizeZodiac'], $parts);
        $zodiacs = ZodiacService::normalizeZodiacSelections($parts, $year);
        $validZodiacs = array_map([self::class, 'normalizeZodiac'], config('zodiac_base_year')['zodiac_order'] ?? []);
        if (empty($validZodiacs)) {
            return $zodiacs;
        }
        return array_values(array_intersect($zodiacs, $validZodiacs));
    }

    private static function parseNumberSelections(string $betContent): array
    {
        $parts = preg_split('/[,\s，、;-]+/u', $betContent);
        if ($parts === false) {
            return [];
        }

        $numbers = [];
        foreach ($parts as $part) {
            $part = trim((string)$part);
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

    private static function normalizeDrawnNumbers(array $drawnNumbers): array
    {
        $regular = array_map('intval', array_slice($drawnNumbers, 0, 6));
        $special = self::getSpecialNumber($drawnNumbers);
        if ($special > 0) {
            $regular[] = $special;
        }
        return array_values(array_unique($regular));
    }

    private static function getSpecialNumber(array $drawnNumbers): int
    {
        return (int)($drawnNumbers[7] ?? $drawnNumbers[6] ?? 0);
    }

    private static function buildLianXiaoRule(array $rule): array
    {
        return [
            'select_count' => $rule[0],
            'hit_count' => $rule[0],
            'odds' => $rule[1],
            'with_horse_odds' => $rule[2],
        ];
    }

    private static function buildSixSpecialZodiacRule(): array
    {
        return [
            'select_count' => 6,
            'hit_count' => 1,
            'odds' => self::SIX_SPECIAL_ZODIAC_ODDS,
            'judge_scope' => 'special',
        ];
    }

    private static function buildNumberComboRule(int $selectCount, int $hitCount, string $judgeScope): array
    {
        return [
            'select_count' => $selectCount,
            'hit_count' => $hitCount,
            'judge_scope' => $judgeScope,
        ];
    }

    private static function getConfiguredOptionOdds(array $optionOdds, string $optionValue, float $defaultOdds): float
    {
        $normalizedOption = self::normalizeOption($optionValue);
        foreach ($optionOdds as $option => $odds) {
            if (self::normalizeOption((string)$option) === $normalizedOption) {
                return (float)$odds;
            }
        }
        return $defaultOdds;
    }

    private static function resolveResult(bool $hit, string $betType): string
    {
        if ($betType === 'not_win') {
            return $hit ? 'lose' : 'win';
        }
        return $hit ? 'win' : 'lose';
    }

    private static function decodePrizeConfig($raw): array
    {
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}
