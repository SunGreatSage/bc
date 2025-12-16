<?php
/**
 * 算法对比测试脚本
 *
 * 功能: 对比原始算法 vs 增强算法的效果
 * 用法: php test_algorithm_compare.php
 */

// 引入 ThinkPHP 框架
namespace think;

require __DIR__ . '/vendor/autoload.php';

// 启动应用
$app = (new App())->initialize();

use think\facade\Db;
use app\common\service\BestPlanService;
use app\common\service\EnhancedBestPlanService;

// 测试参数
$gid = 200;
$qishu = '2025121102';
$year = 2025;
$plateCode = 'A';

echo "========================================\n";
echo "控盘算法对比测试\n";
echo "========================================\n";
echo "游戏ID: {$gid}\n";
echo "期号: {$qishu}\n";
echo "盘口: {$plateCode}\n";
echo "年份: {$year}\n";
echo "========================================\n\n";

try {
    // 检查投注数据
    $betCount = Db::table('la_betting_record')
        ->where('game_id', $gid)
        ->where('issue', $qishu)
        ->where('plate_code', $plateCode)
        ->where('status', 0)
        ->count();

    if ($betCount === 0) {
        echo "❌ 错误: 该期暂无投注数据\n";
        echo "提示: 请确认期号 {$qishu} 和盘口 {$plateCode} 是否正确\n";
        exit(1);
    }

    echo "✅ 找到 {$betCount} 笔投注记录\n\n";

    // ==================== 原始算法 ====================
    echo "【1】原始算法 (贪心策略)\n";
    echo "----------------------------------------\n";

    $startTime = microtime(true);
    $originalService = new BestPlanService($gid, $qishu, $year);
    $originalResult = $originalService->findBest7Numbers();
    $originalTime = microtime(true) - $startTime;

    if ($originalResult['best_solution']) {
        $original = $originalResult['best_solution'];
        $originalNumbers = array_merge($original['m1_m6'], [$original['m7']]);

        echo "推荐号码: " . implode(',', $originalNumbers) . "\n";
        echo "  - 正码(m1-m6): " . implode(',', $original['m1_m6']) . "\n";
        echo "  - 特码(m7): {$original['m7']}\n";
        echo "总利润: ¥" . number_format($original['total_profit'], 2) . "\n";
        echo "利润率: " . number_format($original['profit_rate'], 2) . "%\n";
        echo "总投注额: ¥" . number_format($original['total_bets'], 2) . "\n";
        echo "执行时间: " . number_format($originalTime * 1000, 2) . "ms\n";
    } else {
        echo "❌ 无有效方案\n";
    }

    echo "\n";

    // ==================== 增强算法 ====================
    echo "【2】增强算法 (多策略优化)\n";
    echo "----------------------------------------\n";

    $strategies = ['max_profit', 'avoid_hot', 'balanced'];
    $enhancedResults = [];

    foreach ($strategies as $strategy) {
        $startTime = microtime(true);
        $enhancedService = new EnhancedBestPlanService($gid, $qishu, $year);
        $enhancedResult = $enhancedService->findBest7NumbersEnhanced(null, 5.0, $strategy);
        $enhancedTime = microtime(true) - $startTime;

        $enhancedResults[$strategy] = [
            'result' => $enhancedResult,
            'time' => $enhancedTime
        ];

        echo "\n策略: {$strategy}\n";
        echo "  ------------\n";

        if ($enhancedResult['best_solution']) {
            $enhanced = $enhancedResult['best_solution'];
            $enhancedNumbers = array_merge($enhanced['m1_m6'], [$enhanced['m7']]);

            echo "  推荐号码: " . implode(',', $enhancedNumbers) . "\n";
            echo "    - 正码(m1-m6): " . implode(',', $enhanced['m1_m6']) . "\n";
            echo "    - 特码(m7): {$enhanced['m7']}\n";
            echo "  总利润: ¥" . number_format($enhanced['total_profit'], 2) . "\n";
            echo "  利润率: " . number_format($enhanced['profit_rate'], 2) . "%\n";
            echo "  执行时间: " . number_format($enhancedTime * 1000, 2) . "ms\n";

            // 风险评估
            if (isset($enhancedResult['risk_assessment'])) {
                $risk = $enhancedResult['risk_assessment'];
                echo "  风险评估:\n";
                echo "    - 风险等级: {$risk['risk_level_text']}\n";
                echo "    - 热点号码数: {$risk['hot_number_count']}\n";
                echo "    - 冷门号码数: {$risk['cold_number_count']}\n";
                echo "    - 是否可盈利: " . ($risk['can_profit'] ? '✅ 是' : '❌ 否') . "\n";
                echo "    - 最佳利润率: " . number_format($risk['best_case_profit_rate'], 2) . "%\n";
            }

            // 建议措施
            if (!empty($enhancedResult['recommendations'])) {
                echo "  建议措施:\n";
                foreach ($enhancedResult['recommendations'] as $idx => $recommendation) {
                    echo "    " . ($idx + 1) . ". {$recommendation}\n";
                }
            }
        } else {
            echo "  ❌ 无有效方案\n";
        }
    }

    echo "\n";

    // ==================== 对比分析 ====================
    echo "【3】对比分析\n";
    echo "========================================\n";

    if ($originalResult['best_solution']) {
        $originalProfit = $originalResult['best_solution']['profit_rate'];

        echo sprintf("%-20s %12s %12s %12s\n", "算法", "利润率", "总利润", "改进幅度");
        echo str_repeat("-", 60) . "\n";

        echo sprintf("%-20s %11s%% %11s¥ %12s\n",
            "原始算法(贪心)",
            number_format($originalProfit, 2),
            number_format($originalResult['best_solution']['total_profit'], 2),
            "-"
        );

        foreach ($strategies as $strategy) {
            if (isset($enhancedResults[$strategy]['result']['best_solution'])) {
                $enhanced = $enhancedResults[$strategy]['result']['best_solution'];
                $enhancedProfit = $enhanced['profit_rate'];
                $improvement = $enhancedProfit - $originalProfit;
                $improvementText = $improvement > 0 ? "+{$improvement}%" : "{$improvement}%";

                echo sprintf("%-20s %11s%% %11s¥ %12s\n",
                    "增强({$strategy})",
                    number_format($enhancedProfit, 2),
                    number_format($enhanced['total_profit'], 2),
                    number_format($improvement, 2) . "%"
                );
            }
        }
    }

    echo "\n";

    // ==================== 号码密度分析 ====================
    echo "【4】号码投注密度分析 (Top 10)\n";
    echo "========================================\n";

    $enhancedService = new EnhancedBestPlanService($gid, $qishu, $year);
    $densityReport = $enhancedService->getNumberDensityReport();

    echo sprintf("%-6s %10s %12s %10s\n", "号码", "投注密度", "投注金额", "风险等级");
    echo str_repeat("-", 50) . "\n";

    foreach (array_slice($densityReport, 0, 10) as $item) {
        echo sprintf("%-6d %9s%% %11s¥ %10s\n",
            $item['number'],
            $item['density_percent'],
            number_format($item['bet_amount'], 2),
            $item['risk_level_text']
        );
    }

    echo "\n========================================\n";
    echo "✅ 测试完成\n";
    echo "========================================\n";

} catch (\Exception $e) {
    echo "\n❌ 错误: " . $e->getMessage() . "\n";
    echo "堆栈跟踪:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
