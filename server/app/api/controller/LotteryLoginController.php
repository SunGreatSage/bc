<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 彩票登录控制器（基于新表 la_user）
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-12-11
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
    public array $notNeedLogin = ['login', 'register', 'adminLogin'];


    /**
     * @notes 获取管理员信息（需登录，用于前端路由守卫）
     * @return Json
     * @author Claude
     * @date 2025/12/12
     */
    public function getAdminInfo()
    {
        try {
            // 从token获取管理员ID
            $adminId = $this->adminInfo['admin_id'] ?? 0;

            if (!$adminId) {
                return $this->fail('未登录');
            }

            // 查询管理员信息
            $admin = \app\common\model\auth\Admin::find($adminId);

            if (!$admin) {
                return $this->fail('管理员不存在');
            }

            // 返回管理员信息
            return $this->success('获取成功', [
                'new_user_id' => $admin->id,
                'adminid' => $admin->id,
                'adminname' => $admin->name,
                'logintimes' => $admin->login_count ?? 0,
                'lastloginip' => $admin->login_ip ?? '',
                'lastlogintime' => $admin->login_time ? date('Y-m-d H:i:s', $admin->login_time) : '',
                'root' => $admin->root,  // 关键字段: 角色类型
                'admin_id' => $admin->id,  // 关键字段: 管理员ID
                'credit_limit' => $admin->credit_limit ?? 0,  // 关键字段: 信用额度
            ]);
        } catch (\Exception $e) {
            return $this->fail('获取失败: ' . $e->getMessage());
        }
    }


    /**
     * @notes 彩票用户登录接口
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param string username 用户名（必填）
     * @param string password 密码（必填，明文）
     * @param int terminal 终选，默认1：H5）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "登录成功",
     *   "data": {
     *     "userInfo": {
     *       "id": 1,
     *       "username": "test001",
     *       "nickname": "测试用户1",
     *       "mobile": "",
     *       "balance": 10000.00,
     *       "status": 1
     *     },
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
        $result = LotteryLoginLogic::login([
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
     * @notes 用户注册接口
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param string username 用户名（必填，4-20个字符）
     * @param string password 密码（必填，至少6位）
     * @param string nickname 昵称（可选）
     * @param string mobile 手机号（可选）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "注册成功",
     *   "data": {
     *     "id": 1,
     *     "username": "test001",
     *     "nickname": "测试用户1"
     *   }
     * }
     */
    public function register()
    {
        // 获取请求参数
        $username = $this->request->param('username', '');
        $password = $this->request->param('password', '');
        $nickname = $this->request->param('nickname', '');
        $mobile = $this->request->param('mobile', '');

        // 调用注册逻辑
        $result = LotteryLoginLogic::register([
            'username' => $username,
            'password' => $password,
            'nickname' => $nickname,
            'mobile' => $mobile,
        ]);

        if ($result === false) {
            return $this->fail(LotteryLoginLogic::getError());
        }

        return $this->success('注册成功', $result);
    }


    /**
     * @notes 获取用户信息（需登录）
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 说明：
     * - BaseApiController 会自动校验 token
     * - $this->userId 是 la_user 表的 ID
     */
    public function getUserInfo()
    {
        // 获取用户信息
        $userInfo = LotteryLoginLogic::getUserById($this->userId);

        if (!$userInfo) {
            return $this->fail('用户信息不存在');
        }

        return $this->success('获取成功', $userInfo);
    }


    /**
     * @notes 获取用户余额（需登录）
     * @return Json
     * @author Claude
     * @date 2025/12/11
     */
    public function getBalance()
    {
        $balance = LotteryLoginLogic::getUserBalance($this->userId);

        return $this->success('获取成功', [
            'balance' => $balance,
        ]);
    }


    /**
     * @notes 修改密码（需登录）
     * @return Json
     * @author Claude
     * @date 2025/12/11
     */
    public function changePassword()
    {
        $oldPassword = $this->request->param('old_password', '');
        $newPassword = $this->request->param('new_password', '');

        if (empty($oldPassword) || empty($newPassword)) {
            return $this->fail('请输入旧密码和新密码');
        }

        if (strlen($newPassword) < 6) {
            return $this->fail('新密码长度不能少于6位');
        }

        $result = LotteryLoginLogic::changePassword(
            $this->userId,
            $oldPassword,
            $newPassword
        );

        if ($result === false) {
            return $this->fail(LotteryLoginLogic::getError());
        }

        return $this->success('密码修改成功');
    }


    /**
     * @notes 管理员登录接口（用于Vue管理后台）
     * @return Json
     * @author Claude
     * @date 2025/12/12
     *
     * 请求参数：
     * @param string username 管理员账号（必填）
     * @param string password 密码（必填）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "登录成功",
     *   "data": {
     *     "token": "abc123...",
     *     "adminInfo": {
     *       "id": 1,
     *       "adminid": 1,
     *       "adminname": "admin",
     *       "is_super": true,
     *       "root": 1,
     *       "admin_id": 1,
     *       "credit_limit": 0
     *     }
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
            return $this->fail('请输入账号');
        }

        if (empty($password)) {
            return $this->fail('请输入密码');
        }

        // 调用管理端登录逻辑
        try {
            // 先验证账号密码
            $admin = \app\common\model\auth\Admin::where('account', '=', $username)->find();

            if (!$admin) {
                return $this->fail('账号不存在');
            }

            // 验证密码 - 使用与 adminapi 相同的加密方式
            $passwordSalt = \think\facade\Config::get('project.unique_identification');
            if ($admin->password !== create_password($password, $passwordSalt)) {
                return $this->fail('密码错误');
            }

            // 调用登录逻辑获取token
            $loginLogic = new \app\adminapi\logic\LoginLogic();
            $result = $loginLogic->login([
                'account' => $username,
                'password' => $password,
                'terminal' => 1,
            ]);

            if (!$result) {
                return $this->fail('登录失败');
            }

            // 构造返回数据(直接从 $admin 对象获取完整信息)
            $adminInfo = [
                'id' => $admin->id,
                'adminid' => $admin->id,
                'adminname' => $admin->name,
                'is_super' => $admin->root == 1,
                'logintimes' => $admin->login_count ?? 0,
                'root' => $admin->root,  // 关键字段: 角色类型
                'admin_id' => $admin->id,  // 关键字段: 管理员ID
                'credit_limit' => $admin->credit_limit ?? 0,  // 关键字段: 信用额度
            ];

            // 调试输出
            \think\facade\Log::info('adminLogin 返回数据:', $adminInfo);

            return $this->success('登录成功', [
                'token' => $result['token'],
                'adminInfo' => $adminInfo,
            ]);
        } catch (\Exception $e) {
            return $this->fail('登录失败: ' . $e->getMessage());
        }
    }
}
