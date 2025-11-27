<?php
declare(strict_types=1);

namespace app\common\service;

/**
 * 生肖年份服务类
 *
 * 处理生肖与年份的动态映射关系
 *
 * 核心规律:
 * - 号码固定不变
 * - 生肖每年向前移动一位
 * - 12年为一个完整循环
 * - 当年生肖始终对应起始号1
 *
 * @package app\common\service
 * @date 2025-11-27
 */
class ZodiacYearService
{
    /**
     * 基准年份配置
     * @var array|null
     */
    private static ?array $config = null;

    /**
     * 获取配置
     *
     * @return array
     */
    private static function getConfig(): array
    {
        if (self::$config === null) {
            self::$config = require root_path() . 'config/zodiac_base_year.php';
        }
        return self::$config;
    }

    /**
     * 获取指定年份对应的生肖
     *
     * @param int $year 年份 (如2025, 2026)
     * @return string 生肖名称
     *
     * @example
     * ZodiacYearService::getYearZodiac(2025); // 返回: '蛇'
     * ZodiacYearService::getYearZodiac(2026); // 返回: '马'
     */
    public static function getYearZodiac(int $year): string
    {
        $config = self::getConfig();
        $zodiacOrder = $config['zodiac_order'];
        $offset = $config['year_offset'];

        // 算法: (year - offset) % 12
        $index = ($year - $offset) % 12;

        return $zodiacOrder[$index];
    }

    /**
     * 获取指定年份的生肖对应表
     *
     * @param int $year 年份
     * @return array<string, array<int>> 生肖对应表
     *
     * @example
     * ZodiacYearService::getZodiacTableByYear(2025);
     * // 返回: ['鼠' => [6,18,30,42], '牛' => [5,17,29,41], ...]
     *
     * ZodiacYearService::getZodiacTableByYear(2026);
     * // 返回: ['鼠' => [7,19,31,43], '牛' => [6,18,30,42], ...]
     */
    public static function getZodiacTableByYear(int $year): array
    {
        $config = self::getConfig();
        $baseYear = $config['base_year'];
        $baseTable = $config['base_table'];
        $zodiacOrder = $config['zodiac_order'];

        // 如果是基准年份,直接返回
        if ($year === $baseYear) {
            return $baseTable;
        }

        // 计算年份差
        $yearDiff = $year - $baseYear;

        // 生肖向前移动yearDiff位
        $newTable = [];

        foreach ($baseTable as $zodiac => $numbers) {
            // 找到当前生肖在顺序中的位置
            $currentIndex = array_search($zodiac, $zodiacOrder);

            // 计算新生肖位置 (向前移动yearDiff位)
            // 向前移动 = 索引减小,需要处理负数和循环
            $newIndex = (($currentIndex - $yearDiff) % 12 + 12) % 12;

            // 获取新生肖
            $newZodiac = $zodiacOrder[$newIndex];

            // 号码不变,生肖改变
            $newTable[$newZodiac] = $numbers;
        }

        return $newTable;
    }

    /**
     * 获取指定年份的号码→生肖映射表
     *
     * @param int $year 年份
     * @return array<int, string> 号码→生肖映射
     *
     * @example
     * ZodiacYearService::getNumberMapByYear(2025);
     * // 返回: [1 => '蛇', 2 => '龙', ..., 10 => '猴', ...]
     *
     * ZodiacYearService::getNumberMapByYear(2026);
     * // 返回: [1 => '马', 2 => '蛇', ..., 10 => '鸡', ...]
     */
    public static function getNumberMapByYear(int $year): array
    {
        $zodiacTable = self::getZodiacTableByYear($year);

        $numberMap = [];
        foreach ($zodiacTable as $zodiac => $numbers) {
            foreach ($numbers as $number) {
                $numberMap[$number] = $zodiac;
            }
        }

        // 按号码排序
        ksort($numberMap);

        return $numberMap;
    }

    /**
     * 根据号码和年份获取对应生肖
     *
     * @param int $number 号码(1-49)
     * @param int $year 年份
     * @return string|null 生肖名称
     *
     * @example
     * ZodiacYearService::getZodiacByNumberAndYear(10, 2025); // 返回: '猴'
     * ZodiacYearService::getZodiacByNumberAndYear(10, 2026); // 返回: '鸡'
     */
    public static function getZodiacByNumberAndYear(int $number, int $year): ?string
    {
        $numberMap = self::getNumberMapByYear($year);
        return $numberMap[$number] ?? null;
    }

    /**
     * 根据生肖和年份获取对应号码列表
     *
     * @param string $zodiac 生肖名称
     * @param int $year 年份
     * @return array<int> 号码列表
     *
     * @example
     * ZodiacYearService::getNumbersByZodiacAndYear('鸡', 2025); // 返回: [9,21,33,45]
     * ZodiacYearService::getNumbersByZodiacAndYear('鸡', 2026); // 返回: [10,22,34,46]
     */
    public static function getNumbersByZodiacAndYear(string $zodiac, int $year): array
    {
        $zodiacTable = self::getZodiacTableByYear($year);
        return $zodiacTable[$zodiac] ?? [];
    }

    /**
     * 获取当年生肖的号码 (始终是1,13,25,37,49)
     *
     * @param int $year 年份
     * @return array{zodiac: string, numbers: array<int>}
     *
     * @example
     * ZodiacYearService::getCurrentYearZodiacNumbers(2025);
     * // 返回: ['zodiac' => '蛇', 'numbers' => [1,13,25,37,49]]
     *
     * ZodiacYearService::getCurrentYearZodiacNumbers(2026);
     * // 返回: ['zodiac' => '马', 'numbers' => [1,13,25,37,49]]
     */
    public static function getCurrentYearZodiacNumbers(int $year): array
    {
        $yearZodiac = self::getYearZodiac($year);
        $numbers = self::getNumbersByZodiacAndYear($yearZodiac, $year);

        return [
            'zodiac' => $yearZodiac,
            'numbers' => $numbers,
        ];
    }

    /**
     * 验证年份和生肖对应表的正确性
     *
     * @param int $year 年份
     * @return array{valid: bool, errors: array<string>, statistics: array}
     *
     * @example
     * ZodiacYearService::validateYearZodiacTable(2026);
     * // 返回验证结果和统计信息
     */
    public static function validateYearZodiacTable(int $year): array
    {
        $errors = [];
        $zodiacTable = self::getZodiacTableByYear($year);
        $numberMap = self::getNumberMapByYear($year);

        // 验证1: 检查是否有12个生肖
        if (count($zodiacTable) !== 12) {
            $errors[] = "生肖数量错误: 应该是12个,实际是" . count($zodiacTable) . "个";
        }

        // 验证2: 检查号码1-49是否全部覆盖
        $allNumbers = array_keys($numberMap);
        sort($allNumbers);
        $expectedNumbers = range(1, 49);
        $missingNumbers = array_diff($expectedNumbers, $allNumbers);
        $extraNumbers = array_diff($allNumbers, $expectedNumbers);

        if (!empty($missingNumbers)) {
            $errors[] = "缺少号码: " . implode(',', $missingNumbers);
        }
        if (!empty($extraNumbers)) {
            $errors[] = "多余号码: " . implode(',', $extraNumbers);
        }

        // 验证3: 检查当年生肖是否对应1,13,25,37,49
        $currentYearInfo = self::getCurrentYearZodiacNumbers($year);
        $expectedCurrentNumbers = [1, 13, 25, 37, 49];
        if ($currentYearInfo['numbers'] !== $expectedCurrentNumbers) {
            $errors[] = "当年生肖({$currentYearInfo['zodiac']})号码错误: " .
                "应该是[1,13,25,37,49], 实际是[" .
                implode(',', $currentYearInfo['numbers']) . "]";
        }

        // 统计信息
        $statistics = [
            'year' => $year,
            'year_zodiac' => self::getYearZodiac($year),
            'total_zodiacs' => count($zodiacTable),
            'total_numbers' => count($numberMap),
            'zodiacs_with_4_numbers' => 0,
            'zodiacs_with_5_numbers' => 0,
        ];

        foreach ($zodiacTable as $numbers) {
            if (count($numbers) === 4) {
                $statistics['zodiacs_with_4_numbers']++;
            } elseif (count($numbers) === 5) {
                $statistics['zodiacs_with_5_numbers']++;
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'statistics' => $statistics,
        ];
    }

    /**
     * 生成多年份对照表 (用于调试和验证)
     *
     * @param int $startYear 起始年份
     * @param int $years 年份数量
     * @return array<int, array> 多年份对照数据
     *
     * @example
     * ZodiacYearService::generateYearComparison(2025, 3);
     * // 返回2025-2027年的生肖对照表
     */
    public static function generateYearComparison(int $startYear, int $years): array
    {
        $comparison = [];

        for ($i = 0; $i < $years; $i++) {
            $year = $startYear + $i;
            $yearZodiac = self::getYearZodiac($year);
            $zodiacTable = self::getZodiacTableByYear($year);

            $comparison[$year] = [
                'year' => $year,
                'year_zodiac' => $yearZodiac,
                'zodiac_table' => $zodiacTable,
                'validation' => self::validateYearZodiacTable($year),
            ];
        }

        return $comparison;
    }
}
