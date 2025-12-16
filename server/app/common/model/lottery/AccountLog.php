<?php

namespace app\common\model\lottery;

use app\common\model\BaseModel;
use app\common\service\OrderSnService;

/**
 * 账户流水模型
 */
class AccountLog extends BaseModel
{
    protected $name = 'account_log';

    /**
     * 获取用户流水记录
     */
    public static function getUserLogs($userId, $page = 1, $limit = 20, $changeType = 0)
    {
        $where = ['user_id' => $userId];
        if ($changeType > 0) {
            $where['change_type'] = $changeType;
        }

        return self::where($where)
            ->order('created_at', 'desc')
            ->page($page, $limit)
            ->select();
    }

    /**
     * 变动类型文本
     */
    public function getChangeTypeTextAttr($value, $data)
    {
        $types = [
            1 => '充值',
            2 => '提现',
            3 => '投注',
            4 => '中奖',
            5 => '退款',
            6 => '佣金',
            7 => '调整',
            8 => '冻结',
            9 => '解冻'
        ];
        return $types[$data['change_type']] ?? '未知';
    }

    /**
     * 记录投注流水
     *
     * @param int $userId 用户ID
     * @param float $amount 投注金额
     * @param array $account 账户信息(balance, frozen_amount)
     * @param string $relatedSn 关联单号
     * @param string $remark 备注
     * @param string $ip IP地址
     * @return bool|int
     */
    public static function recordBetting($userId, $amount, $account, $relatedSn, $remark, $ip = '')
    {
        return self::create([
            'sn' => OrderSnService::generateLogSn($userId),  // ✅ 使用微秒级唯一编号
            'user_id' => $userId,
            'change_type' => 3, // 投注
            'change_amount' => -$amount,
            'balance_before' => $account['balance'],
            'balance_after' => $account['balance'] - $amount,
            'frozen_before' => $account['frozen_amount'],
            'frozen_after' => $account['frozen_amount'] + $amount,
            'related_sn' => $relatedSn,
            'related_type' => 1, // 投注单
            'remark' => $remark,
            'ip' => $ip ?: request()->ip(),
            'created_at' => time()
        ]);
    }

    /**
     * 记录中奖流水
     *
     * @param int $userId 用户ID
     * @param float $prizeAmount 中奖金额
     * @param float $frozenAmount 解冻金额
     * @param array $account 账户信息(balance, frozen_amount)
     * @param string $relatedSn 关联单号
     * @param string $remark 备注
     * @param string $ip IP地址
     * @return bool|int
     */
    public static function recordWinning($userId, $prizeAmount, $frozenAmount, $account, $relatedSn, $remark, $ip = '')
    {
        return self::create([
            'sn' => OrderSnService::generateLogSn($userId),  // ✅ 使用微秒级唯一编号
            'user_id' => $userId,
            'change_type' => 4, // 中奖
            'change_amount' => $prizeAmount,
            'balance_before' => $account['balance'],
            'balance_after' => $account['balance'] + $prizeAmount,
            'frozen_before' => $account['frozen_amount'],
            'frozen_after' => $account['frozen_amount'] - $frozenAmount,
            'related_sn' => $relatedSn,
            'related_type' => 1, // 投注单
            'remark' => $remark,
            'ip' => $ip ?: request()->ip(),
            'created_at' => time()
        ]);
    }

    /**
     * 记录和局退款流水
     *
     * @param int $userId 用户ID
     * @param float $refundAmount 退款金额
     * @param array $account 账户信息(balance, frozen_amount)
     * @param string $relatedSn 关联单号
     * @param string $remark 备注
     * @param string $ip IP地址
     * @return bool|int
     */
    public static function recordRefund($userId, $refundAmount, $account, $relatedSn, $remark, $ip = '')
    {
        return self::create([
            'sn' => OrderSnService::generateLogSn($userId),
            'user_id' => $userId,
            'change_type' => 5, // 退款
            'change_amount' => $refundAmount,
            'balance_before' => $account['balance'],
            'balance_after' => $account['balance'] + $refundAmount,
            'frozen_before' => $account['frozen_amount'],
            'frozen_after' => $account['frozen_amount'] - $refundAmount,
            'related_sn' => $relatedSn,
            'related_type' => 1,
            'remark' => $remark,
            'ip' => $ip ?: request()->ip(),
            'created_at' => time()
        ]);
    }

    /**
     * 记录解冻流水(未中奖)
     *
     * @param int $userId 用户ID
     * @param float $frozenAmount 解冻金额
     * @param array $account 账户信息(balance, frozen_amount)
     * @param string $relatedSn 关联单号
     * @param string $remark 备注
     * @param string $ip IP地址
     * @return bool|int
     */
    public static function recordUnfreeze($userId, $frozenAmount, $account, $relatedSn, $remark, $ip = '')
    {
        return self::create([
            'sn' => OrderSnService::generateLogSn($userId),  // ✅ 使用微秒级唯一编号
            'user_id' => $userId,
            'change_type' => 9, // 解冻
            'change_amount' => 0,
            'balance_before' => $account['balance'],
            'balance_after' => $account['balance'],
            'frozen_before' => $account['frozen_amount'],
            'frozen_after' => $account['frozen_amount'] - $frozenAmount,
            'related_sn' => $relatedSn,
            'related_type' => 1, // 投注单
            'remark' => $remark,
            'ip' => $ip ?: request()->ip(),
            'created_at' => time()
        ]);
    }

    /**
     * 记录佣金流水
     *
     * @param int $userId 代理用户ID
     * @param float $commissionAmount 佣金金额
     * @param array $account 账户信息(balance)
     * @param string $relatedSn 关联单号
     * @param string $remark 备注
     * @return bool|int
     */
    public static function recordCommission($userId, $commissionAmount, $account, $relatedSn, $remark)
    {
        return self::create([
            'sn' => OrderSnService::generateLogSn($userId),  // ✅ 使用微秒级唯一编号
            'user_id' => $userId,
            'change_type' => 6, // 佣金
            'change_amount' => $commissionAmount,
            'balance_before' => $account['balance'] - $commissionAmount,
            'balance_after' => $account['balance'],
            'related_sn' => $relatedSn,
            'related_type' => 4, // 佣金记录
            'remark' => $remark,
            'created_at' => time()
        ]);
    }
}
