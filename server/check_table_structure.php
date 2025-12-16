<?php
// 检查数据库表结构
require __DIR__ . '/vendor/autoload.php';

use think\facade\Db;

// 初始化 ThinkPHP
$app = new \think\App();
$app->initialize();

echo "=== 检查 la_play_method 表结构 ===\n";
$columns = Db::query("SHOW COLUMNS FROM la_play_method");
foreach ($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . "\n";
}

echo "\n=== 检查 la_betting_record 表结构 ===\n";
$columns = Db::query("SHOW COLUMNS FROM la_betting_record");
foreach ($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . "\n";
}

echo "\n=== 检查 la_play_category 表结构 ===\n";
$columns = Db::query("SHOW COLUMNS FROM la_play_category");
foreach ($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . "\n";
}
