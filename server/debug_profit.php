<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = new think\App(__DIR__ . '/');
$app->initialize();

use think\facade\Db;

// 查询最新期号的投注数据
$latestIssue = Db::table('la_betting_record')
    ->where('status', 0)
    ->order('issue', 'desc')
    ->value('issue');

if (!$latestIssue) {
    echo "没有找到待开奖期号\n";
    exit;
}

echo "=== 分析期号: {$latestIssue} ===\n\n";

// 查询所有投注
$bets = Db::table('la_betting_record')
    ->where('issue', $latestIssue)
    ->where('status', 0)
    ->select()
    ->toArray();

echo "总投注数: " . count($bets) . "\n";

$totalAmount = array_sum(array_column($bets, 'bet_amount'));
echo "总投注额: {$totalAmount}元\n\n";

// 按投注类型分组统计
$winBets = [];
$notWinBets = [];

foreach ($bets as $bet) {
    $type = $bet['bet_type'] ?? 'win';
    $method = $bet['method_name'] ?? '未知';
    $content = $bet['bet_content'];
    $amount = (float)$bet['bet_amount'];
    $odds = (float)$bet['odds'];
    
    $item = [
        'method' => $method,
        'content' => $content,
        'amount' => $amount,
        'odds' => $odds,
        'max_prize' => $amount * $odds,
    ];
    
    if ($type === 'not_win') {
        $notWinBets[] = $item;
    } else {
        $winBets[] = $item;
    }
}

echo "「中」投注: " . count($winBets) . "笔\n";
$winAmount = array_sum(array_column($winBets, 'amount'));
$winMaxPrize = array_sum(array_column($winBets, 'max_prize'));
echo "  金额: {$winAmount}元\n";
echo "  最大赔付: {$winMaxPrize}元\n\n";

echo "「不中」投注: " . count($notWinBets) . "笔\n";
$notWinAmount = array_sum(array_column($notWinBets, 'amount'));
$notWinMaxPrize = array_sum(array_column($notWinBets, 'max_prize'));
echo "  金额: {$notWinAmount}元\n";
echo "  最大赔付: {$notWinMaxPrize}元\n\n";

// 详细列出投注内容
echo "--- 「中」投注详情 ---\n";
foreach ($winBets as $bet) {
    printf("  %s: %s, %d元 × %s = 最大赔付%d元\n", 
        $bet['method'], $bet['content'], $bet['amount'], $bet['odds'], $bet['max_prize']);
}

echo "\n--- 「不中」投注详情 ---\n";
foreach ($notWinBets as $bet) {
    printf("  %s: %s, %d元 × %s = 最大赔付%d元\n", 
        $bet['method'], $bet['content'], $bet['amount'], $bet['odds'], $bet['max_prize']);
}

// 提取所有号码
$winNumbers = [];
$notWinNumbers = [];

foreach ($bets as $bet) {
    $type = $bet['bet_type'] ?? 'win';
    $method = $bet['method_name'] ?? '';
    
    if (in_array($method, ['特码', '正码'])) {
        $numbers = explode(',', $bet['bet_content']);
        $numbers = array_map('intval', $numbers);
        $numbers = array_filter($numbers, fn($n) => $n >= 1 && $n <= 49);
        
        if ($type === 'not_win') {
            $notWinNumbers = array_merge($notWinNumbers, $numbers);
        } else {
            $winNumbers = array_merge($winNumbers, $numbers);
        }
    }
}

$winNumbers = array_unique($winNumbers);
$notWinNumbers = array_unique($notWinNumbers);

echo "\n--- 号码分布 ---\n";
echo "「中」投注号码: [" . implode(', ', $winNumbers) . "]\n";
echo "「不中」投注号码: [" . implode(', ', $notWinNumbers) . "]\n";

// 计算最佳策略
echo "\n=== 策略分析 ===\n";
echo "问题: 如果所有号码都有投注,无论开什么号码都会赔付\n\n";

// 计算每个号码开出的总赔付
$allNumbers = range(1, 49);
$payoutMap = [];

foreach ($allNumbers as $num) {
    $payout = 0;
    
    foreach ($bets as $bet) {
        $type = $bet['bet_type'] ?? 'win';
        $method = $bet['method_name'] ?? '';
        $content = $bet['bet_content'];
        $amount = (float)$bet['bet_amount'];
        $odds = (float)$bet['odds'];
        
        if ($method === '特码') {
            $numbers = array_map('intval', explode(',', $content));
            $hit = in_array($num, $numbers);
            
            if ($type === 'not_win') {
                // 不中:号码未命中则赔付
                if (!$hit) {
                    $payout += $amount * $odds;
                }
            } else {
                // 中:号码命中则赔付
                if ($hit) {
                    $payout += $amount * $odds;
                }
            }
        }
    }
    
    $payoutMap[$num] = $payout;
}

// 找出赔付最少的10个号码
asort($payoutMap);
$bestNumbers = array_slice($payoutMap, 0, 10, true);

echo "赔付最少的10个号码(作为特码):\n";
foreach ($bestNumbers as $num => $payout) {
    $profit = $totalAmount - $payout;
    $rate = $totalAmount > 0 ? ($profit / $totalAmount * 100) : 0;
    printf("  号码%02d: 赔付%.2f元, 利润%.2f元 (利润率%.2f%%)\n", 
        $num, $payout, $profit, $rate);
}

