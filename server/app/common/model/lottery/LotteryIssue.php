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
        return self::where('draw_time', '<=', $now)
            ->whereRaw('(is_settled IS NULL OR is_settled = 0)')
            ->select();
    }

    /**
     * 获取待预生成(封盘后未写入planned_result)的期次
     */
    public static function getPendingPlanIssues()
    {
        $now = time();
        return self::where('close_time', '<=', $now)
            ->where('draw_time', '>', $now)
            ->whereRaw("(result IS NULL OR result = '')")
            ->whereRaw("(planned_result IS NULL OR planned_result = '')")
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
