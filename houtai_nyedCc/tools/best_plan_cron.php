<?php
/**
 * 最佳控盘计划 - 定时任务脚本
 * 每分钟执行一次，自动检查是否需要进行分析
 *
 * @author Claude AI
 * @date 2025-11-10
 *
 * 使用方法：
 * 1. 在 crontab 中添加：
 *    * * * * * /usr/bin/php /path/to/best_plan_cron.php >> /path/to/logs/best_plan_cron.log 2>&1
 *
 * 2. 或者在现有的开奖脚本中调用：
 *    include('../tools/best_plan_cron.php');
 */

// 错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 引入依赖
include('../data/config.inc.php');
include('../global/db.inc.php');
include('../func/bestplan.class.php');

// 输出日志函数
function log_message($message) {
    $time = date('Y-m-d H:i:s');
    echo "[{$time}] {$message}\n";
}

log_message("========== 最佳控盘计划定时任务开始 ==========");

try {
    // 初始化计算器对象
    $calculator = new BestPlanCalculator($msql, $redis, 300);

    // 检查配置是否启用
    $config_sql = "SELECT * FROM `x_best_plan_config` WHERE id=1 LIMIT 1";
    $msql->query($config_sql);
    if (!$msql->next_record()) {
        log_message("错误：未找到系统配置，请先初始化配置表");
        exit;
    }

    $enabled = $msql->f('enabled');
    $auto_analyze = $msql->f('auto_analyze');

    if ($enabled != 1) {
        log_message("系统未启用，跳过分析");
        exit;
    }

    if ($auto_analyze != 1) {
        log_message("自动分析未开启，跳过分析");
        exit;
    }

    log_message("系统配置检查通过，开始分析...");

    // 获取所有未开奖的期次
    $sql = "SELECT qishu, dates, endtime FROM `x_kj` WHERE gid=300 AND js=0 ORDER BY qishu ASC";
    $list = $msql->arr($sql, 1);

    if (empty($list)) {
        log_message("未找到待开奖的期次");
        exit;
    }

    log_message("找到 " . count($list) . " 个待开奖期次");

    // 逐个检查是否需要分析
    $analyzed_count = 0;
    $skipped_count = 0;

    foreach ($list as $kj) {
        $qishu = $kj['qishu'];
        $dates = $kj['dates'];
        $endtime = $kj['endtime'];

        log_message("检查期次：{$qishu}，开奖时间：{$dates} {$endtime}");

        // 检查是否已经分析过
        $check_sql = "SELECT id FROM `x_best_plan` WHERE gid=300 AND qishu='$qishu' LIMIT 1";
        $msql->query($check_sql);
        if ($msql->next_record()) {
            log_message("期次 {$qishu} 已分析，跳过");
            $skipped_count++;
            continue;
        }

        // 执行分析
        log_message("开始分析期次：{$qishu}");
        $result = $calculator->analyze($qishu);

        if ($result['success']) {
            log_message("✓ 期次 {$qishu} 分析成功");
            log_message("  - 总投注额：" . $result['data']['total_bet_amount']);
            log_message("  - 最大盈利：" . $result['data']['max_profit'] . "（号码：" . implode(',', $result['data']['best_numbers']) . "）");
            log_message("  - 最小盈利：" . $result['data']['min_profit'] . "（号码：" . implode(',', $result['data']['worst_numbers']) . "）");
            log_message("  - 盈利差：" . $result['data']['profit_range']);
            $analyzed_count++;
        } else {
            log_message("✗ 期次 {$qishu} 分析失败：" . $result['message']);
        }
    }

    log_message("分析完成：成功 {$analyzed_count} 个，跳过 {$skipped_count} 个");

} catch (Exception $e) {
    log_message("发生异常：" . $e->getMessage());
    log_message("异常堆栈：" . $e->getTraceAsString());
}

log_message("========== 最佳控盘计划定时任务结束 ==========\n");
