<?php

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\lottery\{LotteryIssue, BettingRecord, UserAccount, WinningRecord, AccountLog, AgentCommission, UserExtend};
use app\common\service\ZodiacService;
use app\common\service\ZodiacYearService;
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
     */
    private function planClosedIssues(Output $output): void
    {
        $issues = LotteryIssue::getPendingPlanIssues();
        if ($issues->isEmpty()) {
            return;
        }

        foreach ($issues as $issue) {
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
                $issue->planned_source = 0;
                $issue->planned_operator_id = 0;
                $issue->save();

                $output->writeln('Planned result ' . $issue->planned_result);
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
                $result = $this->parseNumbersString($issue->result);

                if (count($result) !== 7) {
                    $result = $this->parseNumbersString($issue->planned_result);
                }

                if (count($result) !== 7) {
                    $result = $this->generateFallbackResult();
                }

                $issue->result = implode(',', $result);
                $issue->status = 3;
                $issue->save();

                $output->writeln('Draw result ' . $issue->result);

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
        $page = 1;
        $pageSize = 1000;
        $totalSettled = 0;

        while (true) {
            $bettings = BettingRecord::getIssueBettings($issue->id, $page, $pageSize);
            if ($bettings->isEmpty()) {
                break;
            }

            foreach ($bettings as $betting) {
                try {
                    $this->settleSingleBetting($betting, $result);
                    $totalSettled++;
                } catch (\Exception $e) {
                    Log::error('single_settle_failed', [
                        'betting_id' => $betting->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $page++;
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
            $resultType = $this->checkWin($betting, $result);
            $isWin = $resultType === 'win';
            $isDraw = $resultType === 'draw';
            $prizeAmount = $isWin ? $betting->total_amount * $betting->odds : ($isDraw ? $betting->total_amount : 0);

            $account = UserAccount::getAccountWithLock($betting->user_id);
            $balanceBefore = $account->balance;
            $frozenBefore = $account->frozen_amount;

            if ($isWin) {
                $account->unfreezeAndPrize($betting->total_amount, $prizeAmount);
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
                'change_type' => $isWin ? 4 : 9,
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
     * Determine win/lose/draw
     */
    private function checkWin($betting, $result)
    {
        $betType = $betting->bet_type ?? 'win';
        $year = (int)substr($betting->issue, 0, 4);
        $specialNumber = (int)($result[6] ?? 0);
        $regularNumbers = array_map('intval', array_slice($result, 0, 6));
        $allNumbers = $regularNumbers;
        if ($specialNumber > 0 && !in_array($specialNumber, $allNumbers, true)) {
            $allNumbers[] = $specialNumber;
        }
        $allNumbers = array_values(array_unique($allNumbers));

        switch ($betting->method_name) {
            case '??':
                $hit = (int)$betting->bet_content === $specialNumber;
                return $this->resolveBetResult($hit, $betType);

            case '??':
                $hit = in_array((int)$betting->bet_content, $regularNumbers, true);
                return $this->resolveBetResult($hit, $betType);

            case '??':
                $hit = in_array((int)$betting->bet_content, $allNumbers, true);
                return $this->resolveBetResult($hit, $betType);

            case '??':
                $specialZodiac = ZodiacYearService::getZodiacByNumberAndYear($specialNumber, $year);
                $betZodiacs = ZodiacService::normalizeZodiacSelections(explode(',', $betting->bet_content), $year);
                if (empty($betZodiacs)) {
                    return 'lose';
                }
                $hit = in_array($specialZodiac, $betZodiacs, true);
                return $this->resolveBetResult($hit, $betType);

            case '??':
                $betZodiacs = ZodiacService::normalizeZodiacSelections(explode(',', $betting->bet_content), $year);
                if (empty($betZodiacs)) {
                    return 'lose';
                }
                $drawnZodiacs = ZodiacService::convertNumbersToZodiacsWithYear($allNumbers, $year);
                $hit = count(array_intersect($betZodiacs, $drawnZodiacs)) > 0;
                return $this->resolveBetResult($hit, $betType);

            case '??':
            case '??':
            case '??':
            case '??':
                $userZodiacs = ZodiacService::normalizeZodiacSelections(explode(',', $betting->bet_content), $year);
                if (empty($userZodiacs)) {
                    return 'lose';
                }
                if ($specialNumber === 49) {
                    return 'draw';
                }
                $checkResult = ZodiacService::checkMultiZodiacWin($userZodiacs, $allNumbers, $year);
                return $this->resolveBetResult($checkResult['is_win'], $betType);

            default:
                return 'lose';
        }
    }

    private function resolveBetResult(bool $hit, string $betType): string
    {
        if ($betType === 'not_win') {
            return $hit ? 'lose' : 'win';
        }
        return $hit ? 'win' : 'lose';
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
