<?php

namespace app\common\model\lottery;

use app\common\model\BaseModel;

/**
 * 代理佣金模型
 */
class AgentCommission extends BaseModel
{
    protected $name = 'agent_commission';

    /**
     * 获取代理佣金记录
     */
    public static function getAgentCommissions($userId, $page = 1, $limit = 20)
    {
        return self::where('user_id', $userId)
            ->order('created_at', 'desc')
            ->page($page, $limit)
            ->select();
    }
}
