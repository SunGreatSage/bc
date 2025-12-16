<?php
/**
 * 实时调试控盘算法
 */

require __DIR__ . '/vendor/autoload.php';

// 启动应用
$app = new think\App();
$app->initialize();

// 参数
$gid = isset($argv[1]) ? (int)$argv[1] : 200;
$qishu = isset($argv[2]) ? $argv[2] : '';
$year = (int)date('Y');

if (empty($qishu)) {
    echo "❌ 用法: php debug_realtime.php [gid] [qishu]\n";
    echo "示例: php debug_realtime.php 200 2025123\n";
    exit(1);
}

echo "===========================================\n";
echo "🔍 控盘算法实时调试 - v2.0\n";
echo "===========================================\n";
echo "游戏ID: {$gid}\n";
echo "期号: {$qishu}\n";
echo "年份: {$year}\n";
echo "===========================================\n\n";

// 查询投注数据
echo "📊 查询投注数据...\n";
$allBets = \think\facade\Db::table('la_betting_record')
    ->alias('b')
    ->field('b.id, b.user_id, b.total_amount as je, b.bet_content as content,
             b.method_id as bid, b.bet_type, b.method_name, b.odds as peilv1')
    ->where('b.game_id', $gid)
    ->where('b.issue', $qishu)
    ->where('b.status', 0)
    ->select()
    ->toArray();

if (empty($allBets)) {
    echo "❌ 未找到投注记录！\n";
    echo "可能原因:\n";
    echo "1. 期号不存在\n";
    echo "2. 该期还没有投注\n";
    echo "3. 投注已经被结算(status != 0)\n";
    exit(1);
}

echo "✅ 找到 " . count($allBets) . " 笔投注\n\n";

// 显示投注明细
echo "投注明细:\n";
echo str_repeat("-", 100) . "\n";
printf("%-5s %-8s %-12s %-8s %-20s %10s %8s %12s\n",
    "ID", "用户ID", "玩法", "类型", "内容", "金额", "赔率", "加权金额");
echo str_repeat("-", 100) . "\n";

$totalBet = 0;
foreach ($allBets as $bet) {
    $weighted = $bet['je'] * $bet['peilv1'];
    $totalBet += $bet['je'];
    $typeText = $bet['bet_type'] === 'not_win' ? '不中' : '中';

    printf("%-5d %-8d %-12s %-8s %-20s %10.2f %8.2f %12.2f\n",
        $bet['id'],
        $bet['user_id'],
        $bet['method_name'],
        $typeText,
        $bet['content'],
        $bet['je'],
        $bet['peilv1'],
        $weighted
    );
}
echo str_repeat("-", 100) . "\n";
echo "总投注额: " . number_format($totalBet, 2) . " 元\n\n";

// 执行算法
echo "⚙️ 执行v2.0算法...\n\n";

try {
    $service = new \app\common\service\OptimizedBestPlanService($gid, $qishu, $year);
    $result = $service->findBest7Numbers();

    // 显示权重分析
    echo "⚖️ 权重分析:\n";
    echo str_repeat("-", 60) . "\n";

    $weightsAnalysis = $service->getWeightsAnalysis();

    echo "特码权重 TOP 5 (最优选择):\n";
    $rank = 1;
    foreach (array_slice($weightsAnalysis['special_weights']['top_10_best'], 0, 5, true) as $num => $weight) {
        printf("  #%d: 号码%02d - 权重 %s 元\n", $rank++, $num, number_format($weight, 2));
    }

    echo "\n特码权重 BOTTOM 5 (最差选择):\n";
    $rank = 1;
    foreach (array_slice($weightsAnalysis['special_weights']['top_10_worst'], 0, 5, true) as $num => $weight) {
        printf("  倒数#%d: 号码%02d - 权重 %s 元\n", $rank++, $num, number_format($weight, 2));
    }

    echo str_repeat("-", 60) . "\n\n";

    // 最佳方案
    if (!empty($result['best_solution'])) {
        $best = $result['best_solution'];

        echo "🎯 最佳开奖方案:\n";
        echo str_repeat("=", 60) . "\n";
        echo "特码(m7): " . str_pad($best['m7'], 2, '0', STR_PAD_LEFT) . "\n";
        echo "正码(m1-m6): ";
        foreach ($best['m1_m6'] as $num) {
            echo str_pad($num, 2, '0', STR_PAD_LEFT) . " ";
        }
        echo "\n";
        echo str_repeat("-", 60) . "\n";
        printf("总投注:   %12.2f 元\n", $best['bet_amount']);
        printf("预计赔付: %12.2f 元\n", $best['total_prize']);
        printf("预计利润: %12.2f 元 (%s%.2f%%)\n",
            $best['total_profit'],
            $best['total_profit'] >= 0 ? '+' : '',
            $best['profit_rate']
        );
        echo str_repeat("=", 60) . "\n";

        // 利润状态
        if ($best['total_profit'] >= 0) {
            echo "✅ 状态: 盈利\n";
        } else {
            echo "❌ 状态: 亏损\n";
        }

        // 风控警告
        if (!empty($result['risk_warning'])) {
            echo "\n";
            echo str_repeat("!", 60) . "\n";
            echo "⚠️ 风控预警 [{$result['risk_warning']['level']}]\n";
            echo str_repeat("!", 60) . "\n";
            echo $result['risk_warning']['message'] . "\n";
            echo "建议: " . $result['risk_warning']['suggestion'] . "\n";
            echo str_repeat("!", 60) . "\n";
        }
    }

    // TOP 5方案
    echo "\n📋 TOP 5 方案对比:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-5s %-6s %-30s %12s %12s %10s\n",
        "排名", "特码", "正码", "赔付", "利润", "利润率");
    echo str_repeat("-", 80) . "\n";

    $rank = 1;
    foreach (array_slice($result['top_solutions'], 0, 5) as $solution) {
        $normalCodes = implode(' ', array_map(function($n) {
            return str_pad($n, 2, '0', STR_PAD_LEFT);
        }, $solution['m1_m6']));

        printf("%-5s %02d     %-30s %12.2f %12.2f %9.2f%%\n",
            "#{$rank}",
            $solution['m7'],
            $normalCodes,
            $solution['total_prize'],
            $solution['total_profit'],
            $solution['profit_rate']
        );
        $rank++;
    }
    echo str_repeat("-", 80) . "\n";

} catch (\Exception $e) {
    echo "❌ 算法执行出错:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✅ 调试完成!\n";
