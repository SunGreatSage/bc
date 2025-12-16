<?php

namespace app\common\model\lottery;

use app\common\model\BaseModel;

/**
 * 投注记录模型
 */
class BettingRecord extends BaseModel
{
    protected $name = 'betting_record';

    /**
     * 生成投注单号
     */
    public static function generateSn()
    {
        return 'BET' . date('YmdHis') . rand(1000, 9999);
    }

    /**
     * 获取用户投注记录
     */
    public static function getUserBettings($userId, $page = 1, $limit = 20, $plateCode = '')
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
     * 获取期次投注记录(分批)
     */
    public static function getIssueBettings($issueId, $page = 1, $pageSize = 1000)
    {
        return self::where([
            'issue_id' => $issueId,
            'status' => 0 // 待开奖
        ])
        ->page($page, $pageSize)
        ->select();
    }
}
