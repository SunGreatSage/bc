<?php

namespace app\common\model\lottery;

use app\common\model\BaseModel;

/**
 * 中奖记录模型
 */
class WinningRecord extends BaseModel
{
    protected $name = 'winning_record';

    /**
     * 获取用户中奖记录
     */
    public static function getUserWinnings($userId, $page = 1, $limit = 20, $plateCode = '')
    {
        $where = ['user_id' => $userId];
        if ($plateCode) {
            $where['plate_code'] = $plateCode;
        }

        return self::where($where)
            ->order('created_at', 'desc')
            ->page($page, $limit)
            ->select();
    }

    /**
     * 创建中奖记录
     *
     * @param array $betting 投注记录
     * @param float $prizeAmount 中奖金额
     * @return bool|int
     */
    public static function recordWin($betting, $prizeAmount)
    {
        return self::create([
            'sn' => 'WIN' . date('YmdHis') . rand(1000, 9999),
            'betting_id' => $betting['id'],
            'user_id' => $betting['user_id'],
            'game_id' => $betting['game_id'],
            'plate_id' => $betting['plate_id'],
            'plate_code' => $betting['plate_code'],
            'issue_id' => $betting['issue_id'],
            'issue' => $betting['issue'],
            'method_name' => $betting['method_name'],
            'bet_amount' => $betting['total_amount'],
            'odds' => $betting['odds'],
            'prize_amount' => $prizeAmount,
            'net_profit' => $prizeAmount - $betting['total_amount'],
            'is_paid' => 1,
            'paid_at' => time(),
            'created_at' => time()
        ]);
    }
}
