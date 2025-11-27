<?php
declare(strict_types=1);

namespace app\common\service;

/**
 * 投注限额服务类
 *
 * 提供投注限额验证和查询功能
 *
 * @package app\common\service
 * @date 2025-11-27
 */
class BetLimitService
{
    /**
     * 限额配置
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
            self::$config = require root_path() . 'config/lottery_bet_limits.php';
        }
        return self::$config;
    }

    /**
     * 验证投注金额是否符合限额要求
     *
     * @param float $amount 投注金额
     * @param string $playType 玩法类型 (可选)
     * @param string $userLevel 用户级别 (可选)
     * @return array{valid: bool, error: string|null, min: float, max: float|null}
     *
     * @example
     * BetLimitService::validateBetAmount(100);
     * // 返回: ['valid' => true, 'error' => null, 'min' => 1, 'max' => null]
     *
     * BetLimitService::validateBetAmount(0.5);
     * // 返回: ['valid' => false, 'error' => '投注金额不能低于1元', ...]
     */
    public static function validateBetAmount(
        float $amount,
        string $playType = 'global',
        string $userLevel = 'normal'
    ): array {
        $config = self::getConfig();

        // 获取限额配置
        $limits = self::getLimits($playType, $userLevel);

        $minAmount = $limits['min_bet_amount'];
        $maxAmount = $limits['max_bet_amount'];

        // 验证金额格式
        $validationRules = $config['validation_rules'];

        // 检查是否为整数
        if ($validationRules['integer_only'] && $amount != floor($amount)) {
            return [
                'valid' => false,
                'error' => '投注金额必须是整数',
                'min' => $minAmount,
                'max' => $maxAmount,
            ];
        }

        // 检查是否为正数
        if ($validationRules['positive_only'] && $amount <= 0) {
            return [
                'valid' => false,
                'error' => '投注金额必须大于0',
                'min' => $minAmount,
                'max' => $maxAmount,
            ];
        }

        // 检查最小金额
        if ($amount < $minAmount) {
            return [
                'valid' => false,
                'error' => "投注金额不能低于{$minAmount}元",
                'min' => $minAmount,
                'max' => $maxAmount,
            ];
        }

        // 检查最大金额
        if ($maxAmount !== null && $amount > $maxAmount) {
            return [
                'valid' => false,
                'error' => "投注金额不能超过{$maxAmount}元",
                'min' => $minAmount,
                'max' => $maxAmount,
            ];
        }

        return [
            'valid' => true,
            'error' => null,
            'min' => $minAmount,
            'max' => $maxAmount,
        ];
    }

    /**
     * 获取指定玩法和用户级别的限额配置
     *
     * @param string $playType 玩法类型
     * @param string $userLevel 用户级别
     * @return array{min_bet_amount: float, max_bet_amount: float|null, max_period_amount: float|null}
     */
    public static function getLimits(string $playType = 'global', string $userLevel = 'normal'): array
    {
        $config = self::getConfig();

        // 优先级: 玩法限额 > 用户级别限额 > 全局限额
        $limits = $config['global'];

        // 检查用户级别限额
        if (isset($config['by_user_level'][$userLevel])) {
            $limits = array_merge($limits, $config['by_user_level'][$userLevel]);
        }

        // 检查玩法限额
        if ($playType !== 'global' && isset($config['by_play_type'][$playType])) {
            $limits = array_merge($limits, $config['by_play_type'][$playType]);
        }

        return $limits;
    }

    /**
     * 检查是否触发风控预警
     *
     * @param float $amount 投注金额
     * @param float $periodTotalAmount 本期已投注总额
     * @return array{alert: bool, type: string|null, message: string|null}
     *
     * @example
     * BetLimitService::checkRiskAlert(15000, 60000);
     * // 返回: ['alert' => true, 'type' => 'single_bet', 'message' => '单笔投注金额超过预警线']
     */
    public static function checkRiskAlert(float $amount, float $periodTotalAmount = 0): array
    {
        $config = self::getConfig();
        $riskControl = $config['risk_control'];

        // 检查单笔预警
        if ($amount >= $riskControl['alert_amount']) {
            return [
                'alert' => true,
                'type' => 'single_bet',
                'message' => "单笔投注金额({$amount}元)超过预警线({$riskControl['alert_amount']}元)",
            ];
        }

        // 检查单期总额预警
        $newPeriodTotal = $periodTotalAmount + $amount;
        if ($newPeriodTotal >= $riskControl['alert_period_amount']) {
            return [
                'alert' => true,
                'type' => 'period_total',
                'message' => "单期投注总额({$newPeriodTotal}元)超过预警线({$riskControl['alert_period_amount']}元)",
            ];
        }

        return [
            'alert' => false,
            'type' => null,
            'message' => null,
        ];
    }

    /**
     * 获取投注限额说明文本
     *
     * @param string $playType 玩法类型
     * @param string $userLevel 用户级别
     * @return string
     *
     * @example
     * BetLimitService::getLimitDescription();
     * // 返回: "最小投注1元,不限制上限"
     */
    public static function getLimitDescription(string $playType = 'global', string $userLevel = 'normal'): string
    {
        $limits = self::getLimits($playType, $userLevel);

        $minText = "最小投注{$limits['min_bet_amount']}元";

        if ($limits['max_bet_amount'] === null) {
            $maxText = "不限制上限";
        } else {
            $maxText = "最大投注{$limits['max_bet_amount']}元";
        }

        return "{$minText},{$maxText}";
    }

    /**
     * 获取所有玩法的限额配置
     *
     * @return array
     */
    public static function getAllPlayTypeLimits(): array
    {
        $config = self::getConfig();
        return $config['by_play_type'];
    }

    /**
     * 获取风控配置
     *
     * @return array
     */
    public static function getRiskControlConfig(): array
    {
        $config = self::getConfig();
        return $config['risk_control'];
    }

    /**
     * 格式化金额显示
     *
     * @param float|null $amount 金额
     * @return string
     */
    public static function formatAmount(?float $amount): string
    {
        if ($amount === null) {
            return '不限';
        }

        return number_format($amount, 2) . '元';
    }
}
