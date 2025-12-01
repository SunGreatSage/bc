<?php
declare(strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use app\api\logic\BestPlanLogic;
use app\common\service\BestPlanService;

/**
 * 最佳控盘计划 - 定时任务命令
 *
 * 使用说明：
 * 1. 命令行执行：php think best_plan:analyze
 * 2. Cron配置：* * * * * cd /path/to/server && php think best_plan:analyze
 *
 * 功能说明：
 * 每分钟检查是否有即将开奖的期号（开奖前5分钟），
 * 如果有则自动执行分析并保存结果。
 *
 * @package app\command
 * @author Claude AI
 * @date 2025-12-01
 */
class BestPlanCommand extends Command
{
    /**
     * 配置命令
     */
    protected function configure(): void
    {
        $this->setName('best_plan:analyze')
            ->setDescription('最佳控盘计划 - 定时分析任务');
    }

    /**
     * 执行命令
     *
     * @param Input $input
     * @param Output $output
     * @return int
     */
    protected function execute(Input $input, Output $output): int
    {
        $output->writeln('[' . date('Y-m-d H:i:s') . '] 开始执行最佳控盘分析任务...');

        $now = time();
        $gameIds = BestPlanService::CONFIG['game_ids'];
        $beforeMinutes = BestPlanService::CONFIG['analyze_before_minutes'];

        foreach ($gameIds as $gid) {
            $output->writeln("  检查游戏 {$gid}...");

            // 从 x_game 表获取当前期号
            $game = Db::table('x_game')
                ->field('thisqishu')
                ->where('gid', $gid)
                ->find();

            if (!$game || empty($game['thisqishu'])) {
                $output->writeln("    游戏 {$gid}: 无当前期号");
                continue;
            }

            $qishu = $game['thisqishu'];

            // 从 x_kj 表获取开奖时间
            $kjInfo = Db::table('x_kj')
                ->field('qishu, kjtime, closetime, m1')
                ->where('gid', $gid)
                ->where('qishu', $qishu)
                ->find();

            if (!$kjInfo) {
                $output->writeln("    游戏 {$gid} 期号 {$qishu}: 无开奖信息");
                continue;
            }

            // 检查是否已开奖
            if (!empty($kjInfo['m1'])) {
                $output->writeln("    游戏 {$gid} 期号 {$qishu}: 已开奖，跳过");
                continue;
            }

            // 计算触发时间（开奖前N分钟）
            $kjtime = strtotime($kjInfo['kjtime']);
            $triggerTime = $kjtime - ($beforeMinutes * 60);

            // 判断是否在触发时间范围内（±2分钟）
            if ($now >= ($triggerTime - 120) && $now <= ($triggerTime + 120)) {

                // 检查是否已经分析过
                $exists = Db::table('x_best_plan_history')
                    ->where('gid', $gid)
                    ->where('qishu', $qishu)
                    ->find();

                if ($exists) {
                    $output->writeln("    游戏 {$gid} 期号 {$qishu}: 已分析过，跳过");
                    continue;
                }

                $output->writeln("    游戏 {$gid} 期号 {$qishu}: 开始分析...");

                try {
                    $result = BestPlanLogic::analyze($gid, $qishu);

                    if ($result) {
                        $summary = $result['summary'];
                        $output->writeln("      ✓ 分析完成");
                        $output->writeln("      - 总投注: " . number_format($summary['total_bets'], 2) . " 元");
                        $output->writeln("      - 投注笔数: " . $summary['total_orders'] . " 笔");
                        $output->writeln("      - 最佳号码: " . $summary['best_number'] . " (利润: " . number_format($summary['best_profit'], 2) . " 元, " . $summary['best_profit_rate'] . "%)");
                        $output->writeln("      - 最差号码: " . $summary['worst_number'] . " (利润: " . number_format($summary['worst_profit'], 2) . " 元, " . $summary['worst_profit_rate'] . "%)");
                    } else {
                        $output->writeln("      ✗ 分析失败: " . BestPlanLogic::getError());
                    }

                } catch (\Exception $e) {
                    $output->writeln("      ✗ 异常: " . $e->getMessage());
                }
            } else {
                // 计算距离触发时间还有多久
                $diff = $triggerTime - $now;
                if ($diff > 0) {
                    $minutes = floor($diff / 60);
                    $seconds = $diff % 60;
                    $output->writeln("    游戏 {$gid} 期号 {$qishu}: 未到分析时间（还有 {$minutes}分{$seconds}秒）");
                } else {
                    $output->writeln("    游戏 {$gid} 期号 {$qishu}: 已过分析时间");
                }
            }
        }

        $output->writeln('[' . date('Y-m-d H:i:s') . '] 任务执行完毕');

        return 0;
    }
}
