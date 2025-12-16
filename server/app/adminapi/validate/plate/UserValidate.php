<?php
declare(strict_types=1);

namespace app\adminapi\validate\plate;

use app\common\validate\BaseValidate;

/**
 * 用户验证器
 * Class UserValidate
 * @package app\adminapi\validate\plate
 */
class UserValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
        'username' => 'require|length:3,32',
        'password' => 'length:6,32',
        'nickname' => 'max:32',
        'mobile' => 'mobile',
        'status' => 'in:0,1',
        'user_money' => 'float|>=:0',
        'change_amount' => 'require|float|>:0',
        'change_type' => 'require|in:1,2',
        'remark' => 'max:255',
        'credit_limit' => 'float|>=:0',
    ];

    protected $message = [
        'id.require' => '用户ID不能为空',
        'id.integer' => '用户ID格式错误',
        'username.require' => '用户名不能为空',
        'username.length' => '用户名长度必须在3-32个字符之间',
        'password.length' => '密码长度必须在6-32个字符之间',
        'nickname.max' => '昵称最多32个字符',
        'mobile.mobile' => '手机号格式错误',
        'status.in' => '状态值错误',
        'user_money.float' => '余额格式错误',
        'user_money.>=' => '余额不能为负数',
        'change_amount.require' => '调整金额不能为空',
        'change_amount.float' => '调整金额格式错误',
        'change_amount.>' => '调整金额必须大于0',
        'change_type.require' => '调整类型不能为空',
        'change_type.in' => '调整类型错误',
        'remark.max' => '备注最多255个字符',
    ];

    /**
     * 详情场景
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    /**
     * 新增场景
     */
    public function sceneAdd()
    {
        return $this->only(['username', 'password', 'nickname', 'mobile', 'status', 'user_money'])
            ->append('password', 'require');
    }

    /**
     * 编辑场景
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'username', 'password', 'nickname', 'mobile', 'status']);
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
     * 调整余额场景
     */
    public function sceneAdjustBalance()
    {
        return $this->only(['id', 'change_amount', 'change_type', 'remark']);
    }

    /**
     * 开设代理账户场景
     */
    public function sceneCreateAgent()
    {
        return $this->only(['username', 'password', 'nickname', 'mobile', 'status', 'credit_limit'])
            ->append('password', 'require');
    }

    /**
     * 调整代理信用额度场景
     */
    public function sceneAdjustAgentCredit()
    {
        return $this->only(['id', 'change_amount', 'change_type', 'remark']);
    }
}
