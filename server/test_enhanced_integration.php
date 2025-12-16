<?php
/**
 * 测试增强算法集成效果
 *
 * 用法: php test_enhanced_integration.php
 */

namespace think;

require __DIR__ . '/vendor/autoload.php';

$app = (new App())->initialize();

use app\api\logic\BestPlanLogic;

echo "========================================\n";
echo "测试增强算法集成\n";
echo "========================================\n\n";

// 测试参数
$gid = 200;
$qishu = '2025121102';
$plateCode = 'A';
$year = 2025;

echo "【测试1】实时计算 (calculateRealtime)\n";
echo "----------------------------------------\n";

try {
    $result = BestPlanLogic::calculateRealtime($gid, $qishu, $plateCode, $year);

    if ($result) {
        echo "✅ 成功\n";
        echo "推荐号码: " . implode(',', $result['summary']['best_numbers']) . "\n";
        echo "  - 正码: " . implode(',', $result['summary']['best_m1_m6']) . "\n";
        echo "  - 特码: " . $result['summary']['best_m7'] . "\n";
        echo "总利润: ¥" . number_format($result['summary']['best_profit'], 2) . "\n";
        echo "利润率: " . number_format($result['summary']['best_profit_rate'], 2) . "%\n";
        echo "使用策略: " . ($result['strategy_used'] ?? 'unknown') . "\n";

        if (isset($result['risk_assessment'])) {
            echo "\n风险评估:\n";
            echo "  - 风险等级: " . $result['risk_assessment']['risk_level_text'] . "\n";
            echo "  - 热点号码数: " . $result['risk_assessment']['hot_number_count'] . "\n";
            echo "  - 是否可盈利: " . ($result['risk_assessment']['can_profit'] ? '✅ 是' : '❌ 否') . "\n";
        }

        if (!empty($result['recommendations'])) {
            echo "\n建议措施:\n";
            foreach ($result['recommendations'] as $idx => $rec) {
                echo "  " . ($idx + 1) . ". {$rec}\n";
            }
        }
    } else {
        echo "❌ 失败: " . BestPlanLogic::getError() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ 异常: " . $e->getMessage() . "\n";
}

echo "\n";
echo "【测试2】分析并保存 (analyze)\n";
echo "----------------------------------------\n";

try {
    $result = BestPlanLogic::analyze($gid, $qishu, $plateCode, $year);

    if ($result) {
        echo "✅ 成功\n";
        $summary = $result['summary'];
        echo "推荐号码: " . implode(',', $summary['best_numbers']) . "\n";
        echo "总利润: ¥" . number_format($summary['best_profit'], 2) . "\n";
        echo "利润率: " . number_format($summary['best_profit_rate'], 2) . "%\n";
        echo "\n已保存到数据库 (la_best_plan_history 表)\n";
    } else {
        echo "❌ 失败: " . BestPlanLogic::getError() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ 异常: " . $e->getMessage() . "\n";
}

echo "\n";
echo "【测试3】目标利润率查找 (findByTargetRate)\n";
echo "----------------------------------------\n";

try {
    $targetRate = 50.0;
    $tolerance = 10.0;

    $result = BestPlanLogic::findByTargetRate($gid, $qishu, $plateCode, $targetRate, $tolerance, $year);

    if ($result) {
        echo "✅ 成功\n";
        echo "目标利润率: {$targetRate}% (误差±{$tolerance}%)\n";

        if ($result['best_solution']) {
            $best = $result['best_solution'];
            $numbers = array_merge($best['m1_m6'], [$best['m7']]);
            echo "推荐号码: " . implode(',', $numbers) . "\n";
            echo "实际利润率: " . number_format($best['profit_rate'], 2) . "%\n";
            echo "与目标差距: " . number_format(abs($best['profit_rate'] - $targetRate), 2) . "%\n";
        }
    } else {
        echo "❌ 失败: " . BestPlanLogic::getError() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ 异常: " . $e->getMessage() . "\n";
}

echo "\n========================================\n";
echo "✅ 集成测试完成\n";
echo "========================================\n";
