<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        // 定时任务
        'crontab' => 'app\common\command\Crontab',
        // 开奖两阶段任务：封盘预生成 → 到点公布并结算
        'draw:lottery' => 'app\common\command\DrawLottery',
        // 退款查询
        'query_refund' => 'app\common\command\QueryRefund',
        // 最佳控盘计划分析
        'best_plan:analyze' => 'app\command\BestPlanCommand',
    ],
];
