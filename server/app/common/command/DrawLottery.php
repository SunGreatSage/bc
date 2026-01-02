<?php

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\api\logic\LotteryBetLogic;
use app\common\model\lottery\{LotteryIssue, BettingRecord, UserAccount, WinningRecord, AccountLog, AgentCommission, UserExtend};
use app\common\service\OptimizedBestPlanService;
use think\facade\Db;
use think\facade\Log;

/**
 * Auto draw command
 * Usage: php think draw:lottery
 */
class DrawLottery extends Command
{
    protected function configure()
    {
        $this->setName('draw:lottery')
            ->setDescription('Lottery draw scheduler (multi-plate)');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('Start lottery draw task...');

        $this->planClosedIssues($output);
        $this->publishAndSettleDueIssues($output);

        $output->writeln('Lottery draw task completed');
    }

    /**
     * 兜底生成7个不重复号码
     */
    private function generateFallbackResult(): array
    {
        $numbers = range(1, 49);
        shuffle($numbers);
        return array_slice($numbers, 0, 7);
    }

    private function parseNumbersString($value): array
    {
        $text = trim((string)$value);
        if ($text === '') {
            return [];
        }
        $parts = array_values(array_filter(array_map('trim', explode(',', $text)), 'strlen'));

        $numbers = [];
        foreach ($parts as $part) {
            $num = (int)$part;
            if ($num < 1 || $num > 49) {
                continue;
            }
            $numbers[] = $num;
        }

        if (count($numbers) !== 7) {
            return [];
        }

        if (count(array_unique($numbers)) !== 7) {
            return [];
        }

        return $numbers;
    }

    private function getIssueYear($issue): int
    {
        $raw = (string)($issue->issue ?? '');
        $year = (int)substr($raw, 0, 4);
        return $year > 0 ? $year : (int)date('Y');
    }

    /**
     * 封盘后预生成 planned_result（不公开、不结算）
     *
     * 优先级：
     * 1. 后台手动设置的 planned_result（planned_source=1）不会被覆盖
     * 2. 只有未设置 planned_result 的期次才会自动计算
     */
    /**
     * 自动计算提前时间（秒）：开奖前多久自动计算
     * 设置为60秒（1分钟），即开奖前1分钟才自动计算
     * 这样管理员有更多时间手动选择号码（封盘后到开奖前1分钟都可以选择）
     */
    private const AUTO_PLAN_BEFORE_DRAW_SECONDS = 60;

    private function planClosedIssues(Output $output): void
    {
        $issues = LotteryIssue::getPendingPlanIssues();
        if ($issues->isEmpty()) {
            return;
        }

        $now = time();

        foreach ($issues as $issue) {
            // 检查是否已有后台手动设置的计划（planned_source=1 表示后台手动设置）
            $existingPlanned = $this->parseNumbersString($issue->planned_result);
            if (count($existingPlanned) === 7 && $issue->planned_source == 1) {
                $output->writeln("Skip {$issue->plate_code}-{$issue->issue}: already has manual planned_result");
                continue;
            }

            // 延迟自动计算：只在开奖前N秒才自动计算，给后台管理员足够时间选择号码
            $drawTime = is_numeric($issue->draw_time) ? (int)$issue->draw_time : strtotime($issue->draw_time);
            $autoPlannedTime = $drawTime - self::AUTO_PLAN_BEFORE_DRAW_SECONDS;

            if ($now < $autoPlannedTime) {
                $waitSeconds = $autoPlannedTime - $now;
                $output->writeln("Wait {$issue->plate_code}-{$issue->issue}: auto-plan in {$waitSeconds}s (waiting for manual selection)");
                continue;
            }

            $output->writeln("Planning issue {$issue->plate_code}-{$issue->issue}");

            try {
                $year = $this->getIssueYear($issue);
                $service = new OptimizedBestPlanService((int)$issue->game_id, (string)$issue->issue, $year, (string)$issue->plate_code);
                $plan = $service->findBest7Numbers();

                $best = $plan['best_solution'] ?? null;
                $numbers = $best ? array_merge($best['m1_m6'], [$best['m7']]) : [];
                $numbers = array_values(array_filter(array_map('intval', $numbers), fn($n) => $n >= 1 && $n <= 49));
                $numbers = array_values(array_unique($numbers));

                if (count($numbers) !== 7) {
                    $numbers = $this->generateFallbackResult();
                }

                $issue->planned_result = implode(',', $numbers);
                $issue->planned_at = time();
                $issue->planned_source = 0;  // 0=自动计算
                $issue->planned_operator_id = 0;
                $issue->save();

                $output->writeln('Auto planned result ' . $issue->planned_result);
            } catch (\Exception $e) {
                $output->writeln('Plan failed ' . $e->getMessage());
                Log::error('plan_failed', [
                    'issue_id' => $issue->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * 到 draw_time：发布 result 并结算
     *
     * 开奖号码优先级：
     * 1. result 字段（如果已有值）
     * 2. planned_result 字段（后台手动设置或自动计算）
     * 3. 随机生成（兜底）
     */
    private function publishAndSettleDueIssues(Output $output): void
    {
        $issues = LotteryIssue::getPendingDrawIssues();
        if ($issues->isEmpty()) {
            $output->writeln('No pending issues');
            return;
        }

        foreach ($issues as $issue) {
            $output->writeln("Processing issue {$issue->plate_code}-{$issue->issue}");

            try {
                $resultSource = 'unknown';

                // 优先级1: 使用已有的 result
                $result = $this->parseNumbersString($issue->result);
                if (count($result) === 7) {
                    $resultSource = 'existing_result';
                }

                // 优先级2: 使用 planned_result（后台手动或自动计算）
                if (count($result) !== 7) {
                    $result = $this->parseNumbersString($issue->planned_result);
                    if (count($result) === 7) {
                        $resultSource = $issue->planned_source == 1 ? 'manual_planned' : 'auto_planned';
                    }
                }

                // 优先级3: 兜底随机生成
                if (count($result) !== 7) {
                    $result = $this->generateFallbackResult();
                    $resultSource = 'fallback_random';
                }

                $issue->result = implode(',', $result);
                $issue->status = 3;
                $issue->save();

                $output->writeln("Draw result: {$issue->result} (source: {$resultSource})");

                $this->settleBetting($issue, $result, $output);
            } catch (\Exception $e) {
                $output->writeln('Draw failed ' . $e->getMessage());
                Log::error('draw_failed', [
                    'issue_id' => $issue->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Settle all bets for the issue
     */
    private function settleBetting($issue, $result, $output)
    {
        $lastId = 0;
        $pageSize = 1000;
        $totalSettled = 0;

        while (true) {
            $bettings = BettingRecord::getIssueBettings($issue->id, $lastId, $pageSize);
            $output->writeln("  Batch after id {$lastId}: found " . count($bettings) . " bettings for issue_id={$issue->id}");
            if ($bettings->isEmpty()) {
                break;
            }

            foreach ($bettings as $betting) {
                $lastId = $betting->id;
                try {
                    $this->settleSingleBetting($betting, $result);
                    $totalSettled++;
                    $output->writeln("  Settled betting #{$betting->id}, user_id: {$betting->user_id}");
                } catch (\Exception $e) {
                    $output->writeln("  <error>Failed betting #{$betting->id}: {$e->getMessage()}</error>");
                    Log::error('single_settle_failed', [
                        'betting_id' => $betting->id,
                        'user_id' => $betting->user_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        }

        $pendingCount = BettingRecord::where([
            'issue_id' => $issue->id,
            'status' => 0,
        ])->count();
        if ($pendingCount > 0) {
            $output->writeln("Settlement incomplete, settled {$totalSettled}, pending {$pendingCount} for issue_id={$issue->id}");
            Log::warning('issue_settle_incomplete', [
                'issue_id' => $issue->id,
                'pending_count' => $pendingCount,
            ]);
            return;
        }

        $issue->status = 3;
        $issue->is_settled = 1;
        $issue->settled_at = time();
        $issue->save();

        $output->writeln("Settlement done, total bets: {$totalSettled}");
    }

    /**
     * Settle a single bet
     */
    private function settleSingleBetting($betting, $result)
    {
        Db::startTrans();
        try {
            $betType = $betting->bet_type ?? 'win';
            $year = (int)substr((string)$betting->issue, 0, 4);
            $resultType = LotteryBetLogic::checkWin(
                (string)$betting->method_name,
                (string)$betting->bet_content,
                $result,
                $year,
                $betType
            );
            $isWin = $resultType === 'win';
            $isDraw = $resultType === 'draw';
            $prizeAmount = $isWin ? $betting->total_amount * $betting->odds : ($isDraw ? $betting->total_amount : 0);

            $account = UserAccount::getAccountWithLock($betting->user_id);
            if (!$account) {
                throw new \Exception("User account not found for user_id: {$betting->user_id}");
            }
            $balanceBefore = $account->balance;
            $frozenBefore = $account->frozen_amount;

            if ($isWin) {
                Log::info('settle_win_before', [
                    'betting_id' => $betting->id,
                    'user_id' => $betting->user_id,
                    'balance_before' => $balanceBefore,
                    'frozen_before' => $frozenBefore,
                    'total_amount' => $betting->total_amount,
                    'prize_amount' => $prizeAmount,
                ]);
                $saveResult = $account->unfreezeAndPrize($betting->total_amount, $prizeAmount);
                Log::info('settle_win_after', [
                    'betting_id' => $betting->id,
                    'save_result' => $saveResult,
                    'balance_after' => $account->balance,
                    'frozen_after' => $account->frozen_amount,
                ]);
            } elseif ($isDraw) {
                $account->frozen_amount -= $betting->total_amount;
                $account->balance += $betting->total_amount;
                $account->version += 1;
                $account->save();
            } else {
                $account->unfreezeAndPrize($betting->total_amount, 0);
            }

            $betting->status = $isWin ? 1 : ($isDraw ? 4 : 2);
            $betting->prize_amount = $prizeAmount;
            $betting->is_settled = 1;
            $betting->settled_at = time();
            $betting->save();

            if ($isWin) {
                WinningRecord::recordWin($betting->toArray(), $prizeAmount);
            }

            AccountLog::create([
                'sn' => 'LOG' . date('YmdHis') . rand(1000, 9999),
                'user_id' => $betting->user_id,
                'change_type' => $isWin ? 4 : ($isDraw ? 5 : 9),
                'change_amount' => $isWin ? $prizeAmount : ($isDraw ? $betting->total_amount : 0),
                'balance_before' => $balanceBefore,
                'balance_after' => $account->balance,
                'frozen_before' => $frozenBefore,
                'frozen_after' => $account->frozen_amount,
                'related_sn' => $betting->sn,
                'related_type' => 1,
                'remark' => $isWin
                    ? "Winning payout: {$betting->plate_code}-{$betting->issue}"
                    : ($isDraw ? "Draw refund: {$betting->plate_code}-{$betting->issue}" : "Lost bet unfreeze"),
                'created_at' => time(),
            ]);

            // 代理分成：按你的要求暂不在本轮实现（后续按下注金额百分比规划）

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * Calculate agent commission
     */
    private function calculateCommission($betting)
    {
        $ancestorIds = explode(',', $betting->ancestor_ids);

        foreach ($ancestorIds as $agentId) {
            if (empty($agentId)) {
                continue;
            }

            $extend = UserExtend::where('user_id', $agentId)->find();

            if ($extend && $extend->is_agent && $extend->agent_rate > 0) {
                $commissionAmount = $betting->total_amount * ($extend->agent_rate / 100);

                $agentAccount = UserAccount::getAccountWithLock($agentId);
                $agentAccount->balance += $commissionAmount;
                $agentAccount->total_commission += $commissionAmount;
                $agentAccount->save();

                AgentCommission::create([
                    'user_id' => $agentId,
                    'downline_user_id' => $betting->user_id,
                    'betting_id' => $betting->id,
                    'game_id' => $betting->game_id,
                    'plate_id' => $betting->plate_id,
                    'issue_id' => $betting->issue_id,
                    'issue' => $betting->issue,
                    'bet_amount' => $betting->total_amount,
                    'commission_rate' => $extend->agent_rate,
                    'commission_amount' => $commissionAmount,
                    'commission_type' => 1,
                    'status' => 1,
                    'settled_at' => time(),
                    'created_at' => time(),
                ]);

                AccountLog::create([
                    'sn' => 'LOG' . date('YmdHis') . rand(1000, 9999),
                    'user_id' => $agentId,
                    'change_type' => 6,
                    'change_amount' => $commissionAmount,
                    'balance_before' => $agentAccount->balance - $commissionAmount,
                    'balance_after' => $agentAccount->balance,
                    'related_sn' => $betting->sn,
                    'related_type' => 4,
                    'remark' => "Agent commission: {$betting->plate_code}-{$betting->issue}",
                    'created_at' => time(),
                ]);
            }
        }
    }
}
