<?php
declare(strict_types=1);

namespace app\common\service;

use app\api\logic\LotteryBetLogic;
use think\facade\Db;

/**
 * Simulates settlement for a candidate draw result using the same rule entry
 * point as real settlement.
 */
class DrawPlanEvaluationService
{
    public static function evaluateIssue(
        int $gameId,
        string $issue,
        string $plateCode,
        array $numbers,
        int $year
    ): array {
        $numbers = self::normalizeNumbers($numbers);

        $orders = Db::table('la_betting_record')
            ->alias('br')
            ->leftJoin('la_play_method pm', 'br.method_id = pm.id')
            ->field('br.*, pm.name as play_method_name, pm.code as method_code')
            ->where('br.game_id', $gameId)
            ->where('br.issue', $issue)
            ->where('br.plate_code', $plateCode)
            ->where('br.status', 0)
            ->select()
            ->toArray();

        return self::evaluateOrders($orders, $numbers, $year);
    }

    public static function evaluateOrders(array $orders, array $numbers, int $year): array
    {
        $numbers = self::normalizeNumbers($numbers);

        $winCount = 0;
        $loseCount = 0;
        $drawCount = 0;
        $totalPayout = 0.0;
        $totalBetAmount = 0.0;
        $details = [];
        $unsupported = [];

        foreach ($orders as $order) {
            $amount = (float)($order['total_amount'] ?? 0);
            $odds = (float)($order['odds'] ?? 0);
            $totalBetAmount += $amount;

            $methodName = (string)(($order['play_method_name'] ?? '') ?: ($order['method_name'] ?? ''));
            $methodCode = (string)($order['method_code'] ?? '');

            $resultType = LotteryBetLogic::checkWin(
                $methodName,
                (string)($order['bet_content'] ?? ''),
                $numbers,
                $year,
                (string)($order['bet_type'] ?? 'win'),
                $methodCode
            );

            if (!in_array($resultType, ['win', 'lose', 'draw'], true)) {
                $unsupported[] = [
                    'id' => $order['id'] ?? 0,
                    'method_id' => $order['method_id'] ?? 0,
                    'method_name' => $methodName,
                    'method_code' => $methodCode,
                    'result' => $resultType,
                ];
                $resultType = 'lose';
            }

            $payout = 0.0;
            if ($resultType === 'win') {
                $winCount++;
                $payout = $amount * $odds;
            } elseif ($resultType === 'draw') {
                $drawCount++;
                $payout = $amount;
            } else {
                $loseCount++;
            }

            $totalPayout += $payout;
            $details[] = [
                'order_id' => $order['id'] ?? 0,
                'method_id' => $order['method_id'] ?? 0,
                'method_name' => $methodName,
                'method_code' => $methodCode,
                'bet_content' => $order['bet_content'] ?? '',
                'bet_amount' => round($amount, 2),
                'odds' => $odds,
                'result' => $resultType,
                'payout' => round($payout, 2),
            ];
        }

        $profit = $totalBetAmount - $totalPayout;
        $profitRate = $totalBetAmount > 0 ? round(($profit / $totalBetAmount) * 100, 2) : 0.0;

        return [
            'numbers' => $numbers,
            'total_orders' => count($orders),
            'win_count' => $winCount,
            'lose_count' => $loseCount,
            'draw_count' => $drawCount,
            'total_bet_amount' => round($totalBetAmount, 2),
            'expected_payout' => round($totalPayout, 2),
            'expected_profit' => round($profit, 2),
            'expected_profit_rate' => $profitRate,
            'details' => $details,
            'unsupported' => $unsupported,
        ];
    }

    public static function normalizeNumbers(array $numbers): array
    {
        $numbers = array_values(array_map('intval', $numbers));
        if (count($numbers) !== 7) {
            throw new \InvalidArgumentException('开奖号码数量必须为7个');
        }
        if (count(array_unique($numbers)) !== 7) {
            throw new \InvalidArgumentException('开奖号码不能重复');
        }
        foreach ($numbers as $number) {
            if ($number < 1 || $number > 49) {
                throw new \InvalidArgumentException('号码范围必须在1-49之间');
            }
        }

        return $numbers;
    }
}
