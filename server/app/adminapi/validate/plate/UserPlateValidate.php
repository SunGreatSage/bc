<?php
declare(strict_types=1);

namespace app\adminapi\validate\plate;

use app\common\model\plate\UserPlate;
use app\common\validate\BaseValidate;

/**
 * 用户盘口验证器
 * Class UserPlateValidate
 * @package app\adminapi\validate\plate
 */
class UserPlateValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkUserPlate',
        'user_id' => 'require|integer',
        'plate_id' => 'require|integer',
        'is_agent' => 'in:0,1',
        'agent_level' => 'integer|between:0,3',
        'commission_rate' => 'float|egt:0|elt:100',
        'status' => 'in:0,1',
        'user_ids' => 'require|array',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'user_id.require' => '请选择用户',
        'user_id.integer' => '用户ID格式错误',
        'plate_id.require' => '请选择盘口',
        'plate_id.integer' => '盘口ID格式错误',
        'is_agent.in' => '代理标识错误',
        'agent_level.integer' => '代理等级格式错误',
        'agent_level.between' => '代理等级范围0-3',
        'commission_rate.float' => '佣金比例格式错误',
        'commission_rate.egt' => '佣金比例不能为负数',
        'commission_rate.elt' => '佣金比例不能超过100',
        'status.in' => '状态值错误',
        'user_ids.require' => '请选择用户',
        'user_ids.array' => '用户ID列表格式错误',
    ];

    /**
     * 添加场景
     */
    public function sceneAdd()
    {
        return $this->remove('id', true)
            ->only(['user_id', 'plate_id', 'is_agent', 'agent_level', 'commission_rate', 'status']);
    }

    /**
     * 详情场景
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    /**
     * 编辑场景
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'is_agent', 'agent_level', 'commission_rate', 'status']);
    }

    /**
     * 删除场景
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }

    /**
     * 状态场景
     */
    public function sceneStatus()
    {
        return $this->only(['id', 'status']);
    }

    /**
     * 批量分配场景
     */
    public function sceneBatchAssign()
    {
        return $this->only(['user_ids', 'plate_id', 'is_agent', 'agent_level', 'commission_rate']);
    }

    /**
     * 校验用户盘口关系是否存在
     */
    public function checkUserPlate($value)
    {
        $userPlate = UserPlate::findOrEmpty($value);
        if ($userPlate->isEmpty()) {
            return '用户盘口关系不存在';
        }
        return true;
    }
}
