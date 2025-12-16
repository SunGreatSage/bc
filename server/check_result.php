<?php
/**
 * 检查la_lottery_issue表的result字段
 */

require __DIR__ . '/vendor/autoload.php';

// 初始化ThinkPHP应用
$app = new \think\App();
$app->initialize();

// 查询最近的期号
$issues = \think\facade\Db::table('la_lottery_issue')
    ->field('id, issue, game_id, status, result, draw_time')
    ->where('game_id', 200)
    ->order('draw_time', 'desc')
    ->limit(5)
    ->select()
    ->toArray();

echo "=== 最近5期数据 ===\n\n";

foreach ($issues as $issue) {
    echo "期号: {$issue['issue']}\n";
    echo "状态: {$issue['status']} (0=待开盘 1=待开盘 2=投注中 3=已开奖)\n";
    echo "result字段: " . (empty($issue['result']) ? '【空】' : $issue['result']) . "\n";
    echo "result类型: " . gettype($issue['result']) . "\n";
    echo "result长度: " . strlen($issue['result']) . "\n";
    echo "开奖时间: " . ($issue['draw_time'] ? date('Y-m-d H:i:s', $issue['draw_time']) : '无') . "\n";
    echo str_repeat('-', 50) . "\n";
}

// 特别检查已开奖的期号
$opened = \think\facade\Db::table('la_lottery_issue')
    ->field('id, issue, game_id, status, result')
    ->where('game_id', 200)
    ->where('status', 3)
    ->order('draw_time', 'desc')
    ->find();

echo "\n=== 最新已开奖期号 ===\n\n";
if ($opened) {
    echo "期号: {$opened['issue']}\n";
    echo "result原始值: ";
    var_dump($opened['result']);
    echo "result是否为空: " . (empty($opened['result']) ? '是' : '否') . "\n";

    if (!empty($opened['result'])) {
        $numbers = explode(',', $opened['result']);
        echo "解析后的号码数组: ";
        print_r($numbers);
    }
} else {
    echo "没有找到已开奖的期号\n";
}
