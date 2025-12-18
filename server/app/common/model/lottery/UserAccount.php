<?php

namespace app\common\model\lottery;

use app\common\model\BaseModel;

/**
 * 用户账户模型
 */
class UserAccount extends BaseModel
{
    protected $name = 'user_account';

    // 开启自动时间戳
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    /**
     * 获取用户账户(带锁)
     */
    public static function getAccountWithLock($userId)
    {
        return self::where('user_id', $userId)->lock(true)->find();
    }

    /**
     * 扣减余额并冻结
     */
    public function deductBalance($amount)
    {
        $this->balance -= $amount;
        $this->frozen_amount += $amount;
        $this->total_bet += $amount;
        $this->version += 1;
        return $this->save();
    }

    /**
     * 解冻并派奖
     */
    public function unfreezeAndPrize($frozenAmount, $prizeAmount = 0)
    {
        $this->frozen_amount -= $frozenAmount;
        if ($prizeAmount > 0) {
            $this->balance += $prizeAmount;
            $this->total_prize += $prizeAmount;
        }
        $this->version += 1;
        return $this->save();
    }
}
