<?php
declare(strict_types=1);

namespace app\common\service;

/**
 * 生肖服务类
 *
 * 提供生肖相关的工具方法:
 * - 号码转生肖
 * - 生肖转号码
 * - 判断生肖投注是否中奖
 *
 * @package app\common\service
 * @date 2025-11-27
 */
class ZodiacService
{
    /**
     * 生肖→号码映射表
     * @var array<string, array<int>>|null
     */
    private static ?array $zodiacTable = null;

    /**
     * 号码→生肖映射表
     * @var array<int, string>|null
     */
    private static ?array $numberMap = null;

    /**
     * 获取生肖→号码映射表
     *
     * @return array<string, array<int>>
     */
    public static function getZodiacTable(): array
    {
        if (self::$zodiacTable === null) {
            self::$zodiacTable = require root_path() . 'config/zodiac_table.php';
        }
        return self::$zodiacTable;
    }

    /**
     * 获取号码→生肖映射表
     *
     * @return array<int, string>
     */
    public static function getNumberMap(): array
    {
        if (self::$numberMap === null) {
            self::$numberMap = require root_path() . 'config/zodiac_number_map.php';
        }
        return self::$numberMap;
    }

    /**
     * 根据号码获取对应生肖
     *
     * ⚠️ 已废弃: 此方法不支持年份参数,请使用 ZodiacYearService::getZodiacByNumberAndYear()
     *
     * @param int $number 号码(1-49)
     * @return string|null 生肖名称,无效号码返回null
     *
     * @deprecated 使用 ZodiacYearService::getZodiacByNumberAndYear() 替代
     * @see ZodiacYearService::getZodiacByNumberAndYear()
     */
    public static function getZodiacByNumber(int $number): ?string
    {
        // 默认使用2025年的数据(向后兼容)
        return ZodiacYearService::getZodiacByNumberAndYear($number, 2025);
    }

    /**
     * 根据生肖获取对应号码列表
     *
     * ⚠️ 已废弃: 此方法不支持年份参数,请使用 ZodiacYearService::getNumbersByZodiacAndYear()
     *
     * @param string $zodiac 生肖名称
     * @return array<int> 号码列表,无效生肖返回空数组
     *
     * @deprecated 使用 ZodiacYearService::getNumbersByZodiacAndYear() 替代
     * @see ZodiacYearService::getNumbersByZodiacAndYear()
     */
    public static function getNumbersByZodiac(string $zodiac): array
    {
        // 默认使用2025年的数据(向后兼容)
        return ZodiacYearService::getNumbersByZodiacAndYear($zodiac, 2025);
    }

    /**
     * 将号码数组转换为生肖数组(去重)
     *
     * ⚠️ 已废弃: 此方法不支持年份参数,请使用 convertNumbersToZodiacsWithYear()
     *
     * @param array<int> $numbers 号码数组
     * @return array<string> 生肖数组(已去重)
     *
     * @deprecated 使用 convertNumbersToZodiacsWithYear() 替代
     */
    public static function convertNumbersToZodiacs(array $numbers): array
    {
        // 默认使用2025年的数据(向后兼容)
        return self::convertNumbersToZodiacsWithYear($numbers, 2025);
    }

    /**
     * 将号码数组转换为生肖数组(去重) - 支持年份
     *
     * @param array<int> $numbers 号码数组
     * @param int $year 年份
     * @return array<string> 生肖数组(已去重)
     *
     * @example
     * ZodiacService::convertNumbersToZodiacsWithYear([12, 24, 36], 2025);
     * // 返回: ['马'] (2025年 12,24,36都是马)
     *
     * ZodiacService::convertNumbersToZodiacsWithYear([10, 22, 34, 46], 2026);
     * // 返回: ['鸡'] (2026年 10,22,34,46都是鸡)
     */
    public static function convertNumbersToZodiacsWithYear(array $numbers, int $year): array
    {
        $numberMap = ZodiacYearService::getNumberMapByYear($year);
        $zodiacs = [];

        foreach ($numbers as $number) {
            if (isset($numberMap[$number])) {
                $zodiacs[] = $numberMap[$number];
            }
        }

        // 去重并重新索引
        return array_values(array_unique($zodiacs));
    }

    /**
     * 判断多肖投注是否中奖
     *
     * 规则: 7个开奖号码中,只要有任意1个号码的生肖在用户选择的生肖中,就算中奖
     *
     * @param array<string> $userZodiacs 用户选择的生肖列表
     * @param array<int> $drawnNumbers 开奖号码(7个球)
     * @param int $year 开奖年份
     * @return array{is_win: bool, matched_zodiacs: array<string>, drawn_zodiacs: array<string>}
     *
     * @example
     * // 用户投注: 三肖-牛,马,羊
     * // 2025年开奖号码: 5(牛), 12(马), 23(羊), 31(猪), 36(马), 42(鼠), 49(蛇)
     * $result = ZodiacService::checkMultiZodiacWin(
     *     ['牛', '马', '羊'],
     *     [5, 12, 23, 31, 36, 42, 49],
     *     2025
     * );
     * // 返回:
     * // [
     * //   'is_win' => true,
     * //   'matched_zodiacs' => ['牛', '马', '羊'],
     * //   'drawn_zodiacs' => ['牛', '马', '羊', '猪', '鼠', '蛇']
     * // ]
     */
    public static function checkMultiZodiacWin(array $userZodiacs, array $drawnNumbers, int $year): array
    {
        // 将开奖号码转换为生肖并去重
        $drawnZodiacs = self::convertNumbersToZodiacsWithYear($drawnNumbers, $year);

        // 计算匹配的生肖
        $matchedZodiacs = array_values(array_intersect($userZodiacs, $drawnZodiacs));

        // 只要有1个匹配就中奖
        $isWin = count($matchedZodiacs) > 0;

        return [
            'is_win' => $isWin,
            'matched_zodiacs' => $matchedZodiacs,
            'drawn_zodiacs' => $drawnZodiacs,
        ];
    }

    /**
     * 判断特肖投注是否中奖
     *
     * 规则: 只看特码(第7个号码)的生肖是否匹配
     *
     * @param string $userZodiac 用户选择的生肖
     * @param int $specialNumber 特码号码
     * @param int $year 开奖年份
     * @return bool 是否中奖
     *
     * @example
     * ZodiacService::checkSpecialZodiacWin('马', 36, 2025); // 返回: true (2025年36是马)
     * ZodiacService::checkSpecialZodiacWin('马', 36, 2026); // 返回: false (2026年36不是马)
     */
    public static function checkSpecialZodiacWin(string $userZodiac, int $specialNumber, int $year): bool
    {
        $specialZodiac = ZodiacYearService::getZodiacByNumberAndYear($specialNumber, $year);
        return $specialZodiac === $userZodiac;
    }

    /**
     * 验证生肖名称是否有效
     *
     * @param string $zodiac 生肖名称
     * @return bool 是否有效
     *
     * @example
     * ZodiacService::isValidZodiac('马');  // 返回: true
     * ZodiacService::isValidZodiac('鼠');  // 返回: true
     * ZodiacService::isValidZodiac('熊');  // 返回: false
     */
    public static function isValidZodiac(string $zodiac): bool
    {
        $table = self::getZodiacTable();
        return isset($table[$zodiac]);
    }

    /**
     * 验证号码是否有效
     *
     * @param int $number 号码
     * @return bool 是否有效(1-49)
     *
     * @example
     * ZodiacService::isValidNumber(36);  // 返回: true
     * ZodiacService::isValidNumber(49);  // 返回: true
     * ZodiacService::isValidNumber(50);  // 返回: false
     * ZodiacService::isValidNumber(0);   // 返回: false
     */
    public static function isValidNumber(int $number): bool
    {
        return $number >= 1 && $number <= 49;
    }

    /**
     * 获取所有生肖列表
     *
     * @return array<string> 生肖列表
     *
     * @example
     * ZodiacService::getAllZodiacs();
     * // 返回: ['鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪']
     */
    public static function getAllZodiacs(): array
    {
        return array_keys(self::getZodiacTable());
    }

    /**
     * 获取生肖统计信息
     *
     * @return array{total_zodiacs: int, total_numbers: int, zodiacs_with_4_numbers: int, zodiacs_with_5_numbers: int}
     *
     * @example
     * ZodiacService::getStatistics();
     * // 返回:
     * // [
     * //   'total_zodiacs' => 12,
     * //   'total_numbers' => 49,
     * //   'zodiacs_with_4_numbers' => 11,
     * //   'zodiacs_with_5_numbers' => 1
     * // ]
     */
    public static function getStatistics(): array
    {
        $table = self::getZodiacTable();

        $totalZodiacs = count($table);
        $totalNumbers = 0;
        $zodiacsWith4 = 0;
        $zodiacsWith5 = 0;

        foreach ($table as $numbers) {
            $count = count($numbers);
            $totalNumbers += $count;

            if ($count === 4) {
                $zodiacsWith4++;
            } elseif ($count === 5) {
                $zodiacsWith5++;
            }
        }

        return [
            'total_zodiacs' => $totalZodiacs,
            'total_numbers' => $totalNumbers,
            'zodiacs_with_4_numbers' => $zodiacsWith4,
            'zodiacs_with_5_numbers' => $zodiacsWith5,
        ];
    }
}
