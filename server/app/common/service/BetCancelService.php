<?php
declare(strict_types=1);

namespace app\common\service;

use app\common\service\OrderSnService;
use think\facade\Db;

/**
 * 封盘前撤单服务。
 */
class BetCancelService
{
    public static function cancelBeforeClose(
        int $betId,
        int $operatorId,
        string $operatorType,
        int $ownerUserId = 0
    ): array {
        if ($betId <= 0) {
            throw new \InvalidArgumentException('注单ID不能为空');
        }

        Db::startTrans();
        try {
            $bet = Db::table('la_betting_record')
                ->where('id', $betId)
                ->lock(true)
                ->find();

            if (!$bet) {
                throw new \RuntimeException('注单不存在');
            }

            if ($ownerUserId > 0 && (int)$bet['user_id'] !== $ownerUserId) {
                throw new \RuntimeException('只能撤销自己的注单');
            }

            if ((int)$bet['status'] !== 0 || (int)($bet['is_settled'] ?? 0) !== 0) {
                throw new \RuntimeException('只有未开奖、未结算注单可以撤单');
            }

            $issue = Db::table('la_lottery_issue')
                ->where('id', (int)$bet['issue_id'])
                ->lock(true)
                ->find();

            if (!$issue) {
                throw new \RuntimeException('注单对应期号不存在');
            }

            $now = time();
            $closeTime = (int)($issue['close_time'] ?? 0);
            if ($closeTime <= 0 || $now >= $closeTime) {
                throw new \RuntimeException('当前期号已封盘，不能撤单');
            }

            if (!empty($issue['result']) || (int)($issue['is_settled'] ?? 0) === 1) {
                throw new \RuntimeException('当前期号已开奖或已结算，不能撤单');
            }

            $account = Db::table('la_user_account')
                ->where('user_id', (int)$bet['user_id'])
                ->lock(true)
                ->find();

            if (!$account) {
                throw new \RuntimeException('用户账户不存在');
            }

            $amount = round((float)$bet['total_amount'], 2);
            if ($amount <= 0) {
                throw new \RuntimeException('注单金额异常，不能撤单');
            }

            Db::table('la_user_account')
                ->where('user_id', (int)$bet['user_id'])
                ->update([
                    'balance' => Db::raw('balance + ' . self::decimal($amount)),
                    'frozen_amount' => Db::raw('GREATEST(frozen_amount - ' . self::decimal($amount) . ', 0)'),
                    'total_bet' => Db::raw('GREATEST(total_bet - ' . self::decimal($amount) . ', 0)'),
                    'version' => Db::raw('version + 1'),
                    'updated_at' => $now,
                ]);

            Db::table('la_lottery_issue')
                ->where('id', (int)$issue['id'])
                ->update([
                    'total_bet_amount' => Db::raw('GREATEST(total_bet_amount - ' . self::decimal($amount) . ', 0)'),
                    'updated_at' => $now,
                ]);

            Db::table('la_betting_record')
                ->where('id', $betId)
                ->update([
                    'status' => 3,
                    'updated_at' => $now,
                ]);

            Db::table('la_account_log')->insert([
                'sn' => OrderSnService::generateLogSn((int)$bet['user_id']),
                'user_id' => (int)$bet['user_id'],
                'change_type' => 5,
                'change_amount' => $amount,
                'balance_before' => $account['balance'],
                'balance_after' => (float)$account['balance'] + $amount,
                'frozen_before' => $account['frozen_amount'],
                'frozen_after' => max(0, (float)$account['frozen_amount'] - $amount),
                'related_sn' => (string)$bet['sn'],
                'related_type' => 1,
                'remark' => ($operatorType === 'admin' ? '后台撤单退款: ' : '用户撤单退款: ')
                    . ($bet['method_name'] ?? '') . ' ' . ($bet['bet_content'] ?? ''),
                'ip' => request()->ip(),
                'created_at' => $now,
            ]);

            $logResult = [
                'operator_type' => $operatorType,
                'operator_id' => $operatorId,
                'bet_id' => $betId,
                'sn' => $bet['sn'] ?? '',
                'user_id' => $bet['user_id'] ?? 0,
                'issue' => $bet['issue'] ?? '',
                'plate_code' => $bet['plate_code'] ?? '',
                'amount' => $amount,
            ];
            self::writeOperationLog($operatorType === 'admin' ? '封盘前撤单' : '用户封盘前撤单', $operatorId, $logResult);

            Db::commit();

            return [
                'id' => $betId,
                'sn' => (string)$bet['sn'],
                'status' => 3,
                'status_text' => '已撤单',
                'refund_amount' => number_format($amount, 2, '.', ''),
            ];
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }

    private static function writeOperationLog(string $action, int $operatorId, array $result): void
    {
        Db::table('la_operation_log')->insert([
            'admin_id' => (string)($result['operator_type'] ?? '') === 'admin' ? $operatorId : 0,
            'admin_name' => '',
            'account' => '',
            'action' => $action,
            'type' => request()->method(),
            'url' => request()->url(true),
            'params' => OperationLogContentService::encodeParams(request()->param()),
            'result' => OperationLogContentService::encodeResult($result),
            'ip' => request()->ip(),
            'create_time' => time(),
        ]);
    }

    private static function decimal(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
