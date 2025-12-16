<?php

namespace app\common\model\lottery;

use app\common\model\BaseModel;

/**
 * 开奖期次模型
 */
class LotteryIssue extends BaseModel
{
    protected $name = 'lottery_issue';

    /**
     * 获取当前可投注期次
     */
    public static function getCurrentIssue($gameId, $plateId)
    {
        $now = time();
        return self::where([
            'game_id' => $gameId,
            'plate_id' => $plateId,
            'status' => 2 // 投注中
        ])
        ->where('close_time', '>', $now)
        ->order('issue', 'asc')
        ->find();
    }

    /**
     * 获取待开奖期次
     */
    public static function getPendingDrawIssues()
    {
        $now = time();
        return self::where('status', 3) // 已封盘
            ->where('draw_time', '<=', $now)
            ->select();
    }

    /**
     * 检查期次是否可投注
     */
    public function canBet()
    {
        $now = time();
        return $this->status == 2 && $now < $this->close_time;
    }
}
