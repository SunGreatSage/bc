<?php
/**
 * 六合彩投注限额配置
 *
 * ✅ 客户确认数据 (2025-11-27)
 *
 * @date 2025-11-27
 */
return [
    // 全局限额设置
    'global' => [
        'min_bet_amount' => 1,           // 最小投注金额: 1元
        'max_bet_amount' => null,        // 最大单笔投注: 不限制(null表示无上限)
        'max_period_amount' => null,     // 单期总投注限额: 不限制
    ],

    // 按玩法类型的限额设置 (如需要可单独配置)
    'by_play_type' => [
        // 特码
        'special_number' => [
            'min_bet_amount' => 1,
            'max_bet_amount' => null,
            'max_period_amount' => null,
        ],

        // 特肖
        'special_zodiac' => [
            'min_bet_amount' => 1,
            'max_bet_amount' => null,
            'max_period_amount' => null,
        ],

        // 平码
        'normal_number' => [
            'min_bet_amount' => 1,
            'max_bet_amount' => null,
            'max_period_amount' => null,
        ],

        // 三肖
        'three_zodiac' => [
            'min_bet_amount' => 1,
            'max_bet_amount' => null,
            'max_period_amount' => null,
        ],

        // 四肖
        'four_zodiac' => [
            'min_bet_amount' => 1,
            'max_bet_amount' => null,
            'max_period_amount' => null,
        ],

        // 五肖
        'five_zodiac' => [
            'min_bet_amount' => 1,
            'max_bet_amount' => null,
            'max_period_amount' => null,
        ],

        // 六肖
        'six_zodiac' => [
            'min_bet_amount' => 1,
            'max_bet_amount' => null,
            'max_period_amount' => null,
        ],
    ],

    // 按用户级别的限额设置 (如需要可单独配置VIP等级)
    'by_user_level' => [
        // 普通用户
        'normal' => [
            'min_bet_amount' => 1,
            'max_bet_amount' => null,
            'max_period_amount' => null,
        ],

        // VIP用户 (预留,可后续扩展)
        'vip' => [
            'min_bet_amount' => 1,
            'max_bet_amount' => null,
            'max_period_amount' => null,
        ],
    ],

    // 风控设置 (建议配置,防止异常投注)
    'risk_control' => [
        // 是否启用单笔投注上限
        'enable_max_bet' => false,

        // 是否启用单期总额上限
        'enable_max_period' => false,

        // 异常投注预警金额 (建议设置,用于风控监控)
        'alert_amount' => 10000,  // 单笔超过10000元预警

        // 单期异常投注总额预警
        'alert_period_amount' => 50000,  // 单期超过50000元预警
    ],

    // 投注金额验证规则
    'validation_rules' => [
        // 金额必须是整数(不允许小数)
        'integer_only' => true,

        // 金额必须是正数
        'positive_only' => true,

        // 金额单位(元)
        'currency' => 'CNY',
    ],
];
