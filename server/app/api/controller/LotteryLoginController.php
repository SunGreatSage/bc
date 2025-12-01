<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 彩票登录控制器（基于 x_user 表）
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-27
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\LotteryLoginLogic;
use think\response\Json;

/**
 * 彩票登录控制器
 * Class LotteryLoginController
 * @package app\api\controller
 */
class LotteryLoginController extends BaseApiController
{
    /**
     * 不需要登录的接口
     */
    public array $notNeedLogin = ['login', 'adminLogin', 'test'];


    /**
     * @notes 彩票用户登录接口
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 请求参数：
     * @param string username 用户名（必填，1-12位字母数字下划线点）
     * @param string password 密码（必填，明文）
     * @param int terminal 终端类型（可选，默认1：H5）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "登录成功",
     *   "data": {
     *     "userid": 10000001,
     *     "username": "TEST001",
     *     "nickname": "测试用户",
     *     "mobile": "13800138000",
     *     "kmoney": 1000.00,
     *     "token": "abc123..."
     *   }
     * }
     */
    public function login()
    {
        // 获取请求参数
        $username = $this->request->param('username', '');
        $password = $this->request->param('password', '');
        $terminal = $this->request->param('terminal/d', 1);

        // 参数验证
        if (empty($username)) {
            return $this->fail('请输入用户名');
        }

        if (empty($password)) {
            return $this->fail('请输入密码');
        }

        // 调用登录逻辑
        $result = LotteryLoginLogic::loginByXUser([
            'username' => $username,
            'password' => $password,
            'terminal' => $terminal,
        ]);

        if ($result === false) {
            return $this->fail(LotteryLoginLogic::getError());
        }

        return $this->success('登录成功', $result);
    }


    /**
     * @notes 获取用户信息（使用新系统的 token 校验）
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 说明：
     * - BaseApiController 会自动校验 token
     * - $this->userId 是新系统 la_user 表的 ID
     * - 需要通过映射关系获取老系统的用户信息
     */
    public function getUserInfo()
    {
        // 获取老系统用户信息
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($this->userId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

        return $this->success('获取成功', [
            'new_user_id' => $this->userId,              // 新系统ID
            'legacy_userid' => $legacyUser['userid'],    // 老系统ID
            'username' => $legacyUser['username'],
            'nickname' => $legacyUser['name'] ?: $legacyUser['username'],
            'mobile' => $legacyUser['tel'] ?: '',
            'kmoney' => $legacyUser['kmoney'] ?? 0,
            'status' => $legacyUser['status'],
        ]);
    }


    /**
     * @notes 获取用户余额（使用新系统的 token 校验）
     * @return Json
     * @author Claude
     * @date 2025/11/27
     */
    public function getBalance()
    {
        // 获取老系统用户信息
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($this->userId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

        return $this->success('获取成功', [
            'kmoney' => $legacyUser['kmoney'] ?? 0,
        ]);
    }


    /**
     * @notes 修改密码（使用新系统的 token 校验）
     * @return Json
     * @author Claude
     * @date 2025/11/27
     */
    public function changePassword()
    {
        // 获取老系统用户信息
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($this->userId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

        $oldPassword = $this->request->param('old_password', '');
        $newPassword = $this->request->param('new_password', '');

        if (empty($oldPassword) || empty($newPassword)) {
            return $this->fail('请输入旧密码和新密码');
        }

        if (strlen($newPassword) < 6) {
            return $this->fail('新密码长度不能少于6位');
        }

        $result = LotteryLoginLogic::changePassword(
            $legacyUser['userid'],
            $oldPassword,
            $newPassword
        );

        if ($result === false) {
            return $this->fail(LotteryLoginLogic::getError());
        }

        return $this->success('密码修改成功');
    }


    /**
     * @notes 管理员登录接口（基于 x_admins 表）
     * @return Json
     * @author Claude
     * @date 2025/12/01
     *
     * 请求参数：
     * @param string username 管理员用户名（必填）
     * @param string password 密码（必填，明文）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "登录成功",
     *   "data": {
     *     "adminInfo": {
     *       "adminid": 10000,
     *       "adminname": "admin",
     *       "is_super": true
     *     },
     *     "token": "abc123..."
     *   }
     * }
     */
    public function adminLogin()
    {
        // 获取请求参数
        $username = $this->request->param('username', '');
        $password = $this->request->param('password', '');

        // 参数验证
        if (empty($username)) {
            return $this->fail('请输入用户名');
        }

        if (empty($password)) {
            return $this->fail('请输入密码');
        }

        // 调用管理员登录逻辑
        $result = LotteryLoginLogic::adminLogin([
            'username' => $username,
            'password' => $password,
        ]);

        if ($result === false) {
            return $this->fail(LotteryLoginLogic::getError());
        }

        return $this->success('登录成功', $result);
    }


    /**
     * @notes 获取管理员信息（需登录）
     * @return Json
     * @author Claude
     * @date 2025/12/01
     */
    public function getAdminInfo()
    {
        // 获取管理员信息
        $adminInfo = LotteryLoginLogic::getAdminByNewUserId($this->userId);

        if (!$adminInfo) {
            return $this->fail('管理员信息不存在');
        }

        return $this->success('获取成功', [
            'new_user_id' => $this->userId,
            'adminid' => $adminInfo['adminid'],
            'adminname' => $adminInfo['adminname'],
            'logintimes' => $adminInfo['logintimes'],
            'lastloginip' => $adminInfo['lastloginip'],
            'lastlogintime' => $adminInfo['lastlogintime'],
        ]);
    }


    /**
     * @notes 测试接口（验证数据库连接）
     * @return Json
     * @author Claude
     * @date 2025/11/27
     */
    public function test()
    {
        try {
            $count = \think\facade\Db::table('x_user')->count();

            return $this->success('数据库连接正常', [
                'table' => 'x_user',
                'count' => $count,
                'message' => "x_user 表共有 {$count} 条记录",
            ]);

        } catch (\Exception $e) {
            return $this->fail('数据库连接失败: ' . $e->getMessage());
        }
    }
}
