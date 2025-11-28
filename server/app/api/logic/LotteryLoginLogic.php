<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 基于老表 (x_user) 的登录与下单逻辑
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-27
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\common\logic\BaseLogic;
use think\facade\Db;

/**
 * 彩票系统登录逻辑（基于 x_user 表）
 * Class LotteryLoginLogic
 * @package app\api\logic
 */
class LotteryLoginLogic extends BaseLogic
{
    /**
     * 密码盐值（需要与老系统保持一致）
     * 老系统：md5(明文密码 + 盐值)
     * 盐值定义在 houtai_nyedCc/data/pan.inc.php: $config['upass'] = "puhh8kik"
     */
    const PASSWORD_SALT = 'puhh8kik';

    /**
     * @notes 用户登录（基于 x_user 表）
     * @param array $params ['username'=>'用户名', 'password'=>'明文密码', 'terminal'=>'终端类型']
     * @return array|false
     * @author Claude
     * @date 2025/11/27
     *
     * 登录流程（完全按照老系统逻辑）：
     * 1. 验证用户名格式
     * 2. 检查密码错误次数（超过5次禁止登录）
     * 3. 查询 x_user 表验证密码
     * 4. 验证账号状态（status=1）
     * 5. 更新登录信息（登录次数、最后登录时间、IP）
     * 6. 生成 token 返回
     */
    public static function loginByXUser(array $params)
    {
        try {
            $username = strtoupper($params['username']); // 转大写
            $password = $params['password'];
            $ip = request()->ip();

            // 步骤1: 验证用户名格式（1-12位字母数字下划线）
            if (!preg_match("/^[a-zA-Z0-9._]{1,12}$/", $username)) {
                throw new \Exception('用户名格式不正确');
            }

            // 步骤2: 检查密码错误次数
            $errorTimes = Db::table('x_user')
                ->where('username', $username)
                ->value('errortimes');

            if ($errorTimes >= 5) {
                throw new \Exception('密码错误次数超过5次，请联系管理员重置');
            }

            // 步骤3: 加密密码（与老系统一致）
            $encryptedPassword = self::encryptPassword($password);

            // 步骤4: 查询用户
            $user = Db::table('x_user')
                ->where('username', $username)
                ->where('userpass', $encryptedPassword)
                ->where('ifagent', 0) // 非代理
                ->where('ifson', 0)   // 非子账号
                ->find();

            if (!$user) {
                // 记录失败日志
                self::logLoginFail($username, $ip);

                // 增加错误次数
                Db::table('x_user')
                    ->where('username', $username)
                    ->inc('errortimes')
                    ->update();

                throw new \Exception('用户名或密码不正确');
            }

            // 步骤5: 检查账号状态
            if ($user['status'] != 1) {
                throw new \Exception('账号已被禁用');
            }

            // 步骤6: 更新登录信息
            Db::table('x_user')
                ->where('userid', $user['userid'])
                ->update([
                    'errortimes' => 0,
                    'logintimes' => Db::raw('logintimes+1'),
                    'lastloginip' => $ip,
                    'lastlogintime' => date('Y-m-d H:i:s'),
                    'online' => 1,
                ]);

            // 步骤7: 记录成功日志
            self::logLoginSuccess($username, $ip);

            // 步骤8: 生成 token（直接基于 userid）
            $token = self::generateSimpleToken($user['userid']);

            // 步骤9: 返回用户信息
            return [
                'userInfo' => [
                    'id' => $user['userid'],
                    'username' => $user['username'],
                    'nickname' => $user['name'] ?: $user['username'],
                    'avatar' => $user['avatar'] ?: '/static/default_avatar.png',
                    'mobile' => $user['tel'] ?: '',
                    'money' => $user['kmoney'] ?? 0,
                    'status' => $user['status'],
                ],
                'token' => $token,
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 加密密码（与老系统一致）
     * @param string $password 明文密码
     * @return string MD5加密后的密码
     * @author Claude
     * @date 2025/11/27
     *
     * 老系统加密规则（来自 houtai_nyedCc/mxj/login.php 第18行）：
     * $pass = md5($_POST['pass'] . $config['upass']);
     * 即：md5(明文密码 + 'puhh8kik')
     */
    private static function encryptPassword(string $password): string
    {
        return md5($password . self::PASSWORD_SALT);
    }


    /**
     * @notes 生成简单的Token（基于 userid）
     * @param int $userid x_user 表的用户ID
     * @return string Token字符串
     * @author Claude
     * @date 2025/11/27
     */
    private static function generateSimpleToken(int $userid): string
    {
        // 生成简单的token: md5(userid + 时间戳 + 随机数)
        $token = md5($userid . time() . uniqid());

        // 将token存入 x_user 表的 token 字段(如果有的话)
        // 或者存入 session/缓存
        // 这里简化处理,直接返回token

        return $token;
    }




    /**
     * @notes 记录登录失败日志
     * @param string $username
     * @param string $ip
     * @return void
     * @author Claude
     * @date 2025/11/27
     */
    private static function logLoginFail(string $username, string $ip)
    {
        try {
            Db::table('x_user_login')->insert([
                'xtype' => 2, // 用户端
                'ip' => $ip,
                'time' => date('Y-m-d H:i:s'),
                'ifok' => 0, // 失败
                'username' => $username,
                'userpass' => 'FAIL',
                'server' => $_SERVER['SERVER_NAME'] ?? '',
                'os' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ]);
        } catch (\Exception $e) {
            // 日志失败不影响主流程
        }
    }


    /**
     * @notes 记录登录成功日志
     * @param string $username
     * @param string $ip
     * @return void
     * @author Claude
     * @date 2025/11/27
     */
    private static function logLoginSuccess(string $username, string $ip)
    {
        try {
            Db::table('x_user_login')->insert([
                'xtype' => 2,
                'ip' => $ip,
                'time' => date('Y-m-d H:i:s'),
                'ifok' => 1, // 成功
                'username' => $username,
                'userpass' => 'OK',
                'server' => $_SERVER['SERVER_NAME'] ?? '',
                'os' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ]);
        } catch (\Exception $e) {
            // 日志失败不影响主流程
        }
    }


    /**
     * @notes 根据用户ID获取老系统用户信息
     * @param int $userid x_user 表的用户ID
     * @return array|null x_user 表的用户信息
     * @author Claude
     * @date 2025/11/27
     */
    public static function getLegacyUserByNewUserId(int $userid)
    {
        // 直接查询 x_user 表
        $legacyUser = Db::table('x_user')
            ->where('userid', $userid)
            ->where('status', 1)
            ->find();

        return $legacyUser;
    }


    /**
     * @notes 获取用户余额（从 x_user 表）
     * @param int $userid
     * @return float
     * @author Claude
     * @date 2025/11/27
     */
    public static function getUserBalance(int $userid): float
    {
        $balance = Db::table('x_user')
            ->where('userid', $userid)
            ->value('kmoney');

        return $balance ? (float)$balance : 0.00;
    }


    /**
     * @notes 修改密码
     * @param int $userid
     * @param string $oldPassword 旧密码（明文）
     * @param string $newPassword 新密码（明文）
     * @return bool
     * @author Claude
     * @date 2025/11/27
     */
    public static function changePassword(int $userid, string $oldPassword, string $newPassword): bool
    {
        try {
            // 验证旧密码
            $user = Db::table('x_user')
                ->where('userid', $userid)
                ->find();

            if (!$user) {
                throw new \Exception('用户不存在');
            }

            $encryptedOldPassword = self::encryptPassword($oldPassword);
            if ($user['userpass'] !== $encryptedOldPassword) {
                throw new \Exception('旧密码不正确');
            }

            // 更新为新密码
            $encryptedNewPassword = self::encryptPassword($newPassword);
            Db::table('x_user')
                ->where('userid', $userid)
                ->update([
                    'userpass' => $encryptedNewPassword,
                    'passtime' => date('Y-m-d H:i:s'),
                ]);

            return true;

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
