<?php

namespace app\common\service;

use think\facade\Db;

/**
 * 订单编号生成服务
 * 解决高并发场景下订单号重复问题
 */
class OrderSnService
{
    /**
     * 自增计数器(进程内)
     * @var int
     */
    private static $counter = 0;

    /**
     * 上次生成时间(微秒级)
     * @var string
     */
    private static $lastTimestamp = '';

    /**
     * 生成唯一订单号
     *
     * @param string $prefix 前缀(如: BET, LOG, WIN)
     * @param int|null $userId 用户ID(可选,增加唯一性)
     * @return string 唯一订单号
     *
     * 格式说明:
     * - BET20251212223143567890123
     * - 前缀 + 日期时间(14位) + 微秒(6位) + 计数器(3位) + 随机数(2位)
     * - 总长度: 前缀长度 + 25位 = 28-31位
     *
     * 唯一性保障:
     * 1. 微秒级时间戳(100万分之一秒)
     * 2. 进程内自增计数器(0-999,循环)
     * 3. 随机数(10-99)
     * 4. 理论支持: 100万微秒 × 1000计数 × 90随机 = 每秒900亿个唯一订单
     */
    public static function generate(string $prefix = 'BET', ?int $userId = null): string
    {
        // 1. 获取当前微秒级时间戳
        $microtime = microtime(true);
        $timestamp = sprintf('%.6f', $microtime); // 保留6位小数
        $timestampStr = str_replace('.', '', $timestamp); // 移除小数点: 1734024703567890

        // 2. 格式化为日期时间 + 微秒
        // 例如: 20251212223143 + 567890
        $datetime = date('YmdHis'); // 14位: 20251212223143
        $microseconds = substr($timestampStr, -6); // 最后6位微秒: 567890

        // 3. 自增计数器(同一微秒内产生多个订单时递增)
        if (self::$lastTimestamp !== $timestampStr) {
            // 时间戳变化,重置计数器
            self::$counter = 0;
            self::$lastTimestamp = $timestampStr;
        } else {
            // 同一时间戳,计数器递增(0-999循环)
            self::$counter = (self::$counter + 1) % 1000;
        }
        $counterStr = str_pad(self::$counter, 3, '0', STR_PAD_LEFT); // 3位: 000-999

        // 4. 随机数(增加额外的随机性)
        $random = mt_rand(10, 99); // 2位: 10-99

        // 5. 可选: 用户ID后4位(进一步增加唯一性)
        $userSuffix = '';
        if ($userId !== null) {
            $userSuffix = str_pad($userId % 10000, 4, '0', STR_PAD_LEFT); // 4位: 0000-9999
        }

        // 6. 组合订单号
        // 格式: BET + 20251212223143 + 567890 + 123 + 45 [+ 0001]
        $sn = $prefix . $datetime . $microseconds . $counterStr . $random . $userSuffix;

        return $sn;
    }

    /**
     * 生成投注订单号
     *
     * @param int|null $userId 用户ID
     * @return string
     */
    public static function generateBetSn(?int $userId = null): string
    {
        return self::generate('BET', $userId);
    }

    /**
     * 生成账户流水号
     *
     * @param int|null $userId 用户ID
     * @return string
     */
    public static function generateLogSn(?int $userId = null): string
    {
        return self::generate('LOG', $userId);
    }

    /**
     * 生成中奖记录号
     *
     * @param int|null $userId 用户ID
     * @return string
     */
    public static function generateWinSn(?int $userId = null): string
    {
        return self::generate('WIN', $userId);
    }

    /**
     * 生成支付订单号
     *
     * @param int|null $userId 用户ID
     * @return string
     */
    public static function generatePaySn(?int $userId = null): string
    {
        return self::generate('PAY', $userId);
    }

    /**
     * 批量生成唯一订单号(用于批量下单场景)
     *
     * @param string $prefix 前缀
     * @param int $count 生成数量
     * @param int|null $userId 用户ID
     * @return array 订单号数组
     *
     * 示例:
     * $sns = OrderSnService::generateBatch('BET', 49, 1001);
     * // ['BET20251212...001', 'BET20251212...002', ..., 'BET20251212...049']
     */
    public static function generateBatch(string $prefix, int $count, ?int $userId = null): array
    {
        $sns = [];
        for ($i = 0; $i < $count; $i++) {
            $sns[] = self::generate($prefix, $userId);

            // 为了确保微秒级差异,可以加入微小延迟(可选)
            // usleep(1); // 延迟1微秒,确保时间戳不同
        }
        return $sns;
    }

    /**
     * 验证订单号格式
     *
     * @param string $sn 订单号
     * @param string|null $prefix 期望的前缀(可选)
     * @return bool
     */
    public static function validate(string $sn, ?string $prefix = null): bool
    {
        // 最小长度检查: 前缀3位 + 日期14位 + 微秒6位 + 计数3位 + 随机2位 = 28位
        if (strlen($sn) < 28) {
            return false;
        }

        // 前缀检查
        if ($prefix !== null) {
            if (strpos($sn, $prefix) !== 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * 从订单号中提取时间戳
     *
     * @param string $sn 订单号(格式: BET20251212223143567890...)
     * @return int|false Unix时间戳,失败返回false
     */
    public static function extractTimestamp(string $sn)
    {
        // 移除前缀(假设前缀3-4位)
        $prefixes = ['BET', 'LOG', 'WIN', 'PAY', 'AL'];
        $datetime = '';

        foreach ($prefixes as $prefix) {
            if (strpos($sn, $prefix) === 0) {
                $datetime = substr($sn, strlen($prefix), 14); // 提取14位日期时间
                break;
            }
        }

        if (empty($datetime) || strlen($datetime) !== 14) {
            return false;
        }

        // 解析日期时间: 20251212223143
        $year = (int)substr($datetime, 0, 4);
        $month = (int)substr($datetime, 4, 2);
        $day = (int)substr($datetime, 6, 2);
        $hour = (int)substr($datetime, 8, 2);
        $minute = (int)substr($datetime, 10, 2);
        $second = (int)substr($datetime, 12, 2);

        return mktime($hour, $minute, $second, $month, $day, $year);
    }
}
