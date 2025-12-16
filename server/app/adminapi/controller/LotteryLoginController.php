<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 管理员彩票登录控制器
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-12-11
// +----------------------------------------------------------------------

namespace app\adminapi\controller;

use app\adminapi\logic\LoginLogic;
use app\adminapi\validate\LoginValidate;

/**
 * 管理员彩票登录控制器
 * Class LotteryLoginController
 * @package app\adminapi\controller
 */
class LotteryLoginController extends BaseAdminController
{
    /**
     * 不需要登录的接口
     */
    public array $notNeedLogin = ['adminLogin'];

    /**
     * 需要登录的接口（getAdminInfo 需要验证 token）
     */


    /**
     * @notes 管理员登录接口（基于 la_admin 表）
     * @return \think\response\Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param string username 用户名（必填） - 前端使用此字段
     * @param string account 账号（必填） - 后端验证使用此字段
     * @param string password 密码（必填，明文）
     * @param int terminal 终端类型，默认1：PC管理后台
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "登录成功",
     *   "data": {
     *     "name": "admin",
     *     "avatar": "http://...",
     *     "role_name": "超级管理员",
     *     "token": "abc123..."
     *   }
     * }
     */
    public function adminLogin()
    {
        // 获取请求参数
        $username = $this->request->param('username', '');
        $password = $this->request->param('password', '');
        $terminal = $this->request->param('terminal', 1);

        // 参数验证：前端使用 username，后端验证器期待 account
        // 因此需要将 username 映射为 account
        $params = [
            'account' => $username ?: $this->request->param('account', ''),
            'password' => $password,
            'terminal' => $terminal,
        ];

        // 验证参数
        (new LoginValidate())->goCheck(null, $params);

        // 调用现有登录逻辑（复用 LoginLogic）
        $loginResult = (new LoginLogic())->login($params);

        // 获取管理员详细信息以返回前端期待的格式
        $admin = \app\common\model\auth\Admin::where('account', $params['account'])->find();

        // 转换为前端期待的格式
        $result = [
            'token' => $loginResult['token'],
            'adminInfo' => [
                'id' => $admin->id,
                'adminid' => $admin->id,
                'adminname' => $admin->name ?: $admin->account,
                'is_super' => $admin->root == 1,
                'logintimes' => 1, // 可以从数据库获取实际登录次数
                'root' => $admin->root,  // 角色类型: 1=总管理, 2=代理
                'admin_id' => $admin->id,  // 管理员ID
                'credit_limit' => $admin->credit_limit ?? 0,  // 信用额度(代理)
            ],
        ];

        return $this->success('登录成功', $result);
    }


    /**
     * @notes 获取管理员信息接口
     * @return \think\response\Json
     * @author Claude
     * @date 2025/12/11
     *
     * 说明：
     * - 此接口需要登录（需要 token 验证）
     * - 从 BaseAdminController 继承，$this->adminId 已自动获取
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "success",
     *   "data": {
     *     "new_user_id": 1,
     *     "adminid": 1,
     *     "adminname": "admin",
     *     "logintimes": 10,
     *     "lastloginip": "127.0.0.1",
     *     "lastlogintime": "2025-12-11 15:30:00"
     *   }
     * }
     */
    public function getAdminInfo()
    {
        // 获取当前登录管理员信息
        $admin = \app\common\model\auth\Admin::where('id', $this->adminId)->find();

        if (!$admin) {
            return $this->fail('管理员信息不存在');
        }

        // 返回前端期待的格式
        $result = [
            'new_user_id' => $admin->id,
            'adminid' => $admin->id,
            'adminname' => $admin->name ?: $admin->account,
            'logintimes' => 1, // 可以从其他表获取实际登录次数
            'lastloginip' => $admin->login_ip ?: '',
            'lastlogintime' => $admin->login_time ? date('Y-m-d H:i:s', (int)$admin->login_time) : '',
            'root' => $admin->root,  // 角色类型: 1=总管理, 2=代理
            'admin_id' => $admin->id,  // 管理员ID
            'credit_limit' => $admin->credit_limit ?? 0,  // 信用额度(代理)
        ];

        return $this->success('获取成功', $result);
    }
}
