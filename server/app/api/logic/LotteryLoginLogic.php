<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 基于老表 (x_user) 的登录与下单逻辑
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-27
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\model\user\User;
use app\api\service\UserTokenService;
use think\facade\Db;
use think\facade\Config;

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
     * 登录流程：
     * 1. 验证用户名格式
     * 2. 检查密码错误次数（超过5次禁止登录）
     * 3. 查询 x_user 表验证密码
     * 4. 验证账号状态（status=1）
     * 5. 更新老系统登录信息
     * 6. 同步/创建新系统 la_user 用户
     * 7. 通过 UserTokenService 生成正规 token（写入 la_user_session）
     * 8. 返回用户信息
     */
    public static function loginByXUser(array $params)
    {
        try {
            $username = strtoupper($params['username']); // 转大写
            $password = $params['password'];
            $terminal = $params['terminal'] ?? 1;
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

            // 步骤4: 查询老系统用户
            $legacyUser = Db::table('x_user')
                ->where('username', $username)
                ->where('userpass', $encryptedPassword)
                ->where('ifagent', 0) // 非代理
                ->where('ifson', 0)   // 非子账号
                ->find();

            if (!$legacyUser) {
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
            if ($legacyUser['status'] != 1) {
                throw new \Exception('账号已被禁用');
            }

            // 步骤6: 更新老系统登录信息
            Db::table('x_user')
                ->where('userid', $legacyUser['userid'])
                ->update([
                    'errortimes' => 0,
                    'logintimes' => Db::raw('logintimes+1'),
                    'lastloginip' => $ip,
                    'lastlogintime' => date('Y-m-d H:i:s'),
                    'online' => 1,
                ]);

            // 步骤7: 记录成功日志
            self::logLoginSuccess($username, $ip);

            // 步骤8: 同步/创建新系统用户（la_user 表）
            $newUser = self::syncToNewUser($legacyUser);

            // 步骤9: 通过 UserTokenService 生成正规 token（写入 la_user_session）
            $userInfo = UserTokenService::setToken($newUser['id'], $terminal);

            // 步骤10: 返回用户信息
            return [
                'userInfo' => [
                    'id' => $newUser['id'],              // 新系统 ID
                    'legacy_userid' => $legacyUser['userid'], // 老系统 userid
                    'username' => $legacyUser['username'],
                    'nickname' => $userInfo['nickname'] ?? ($legacyUser['name'] ?: $legacyUser['username']),
                    'mobile' => $userInfo['mobile'] ?? ($legacyUser['tel'] ?: ''),
                    'money' => $legacyUser['kmoney'] ?? 0,
                    'status' => $legacyUser['status'],
                ],
                'token' => $userInfo['token'],
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 同步老系统用户到新系统（la_user 表）
     * @param array $legacyUser x_user 表的用户数据
     * @return array 新系统用户数据
     * @author Claude
     * @date 2025/11/29
     *
     * 策略：
     * - 使用 account 字段存储老系统 username（唯一索引）
     * - 使用 sn 字段存储老系统 userid（便于映射查询）
     */
    private static function syncToNewUser(array $legacyUser): array
    {
        // 使用老系统 username 作为 account 查找是否已存在
        $newUser = User::where('account', $legacyUser['username'])->find();

        if ($newUser) {
            // 已存在，更新登录信息和 sn（确保老系统 userid 映射正确）
            $newUser->login_time = time();
            $newUser->login_ip = request()->ip();
            // 修复：确保 sn 存储老系统 userid（之前可能是0或其他值）
            if (empty($newUser->sn) || $newUser->sn != $legacyUser['userid']) {
                $newUser->sn = $legacyUser['userid'];
            }
            $newUser->save();
            return $newUser->toArray();
        }

        // 不存在，创建新用户
        // sn 使用老系统 userid，确保一一对应
        $newUser = User::create([
            'sn' => $legacyUser['userid'],  // 使用老系统 userid 作为 sn
            'account' => $legacyUser['username'],  // 老系统用户名
            'nickname' => $legacyUser['name'] ?: $legacyUser['username'],
            'mobile' => $legacyUser['tel'] ?: '',
            'avatar' => '',
            'password' => '',  // 密码验证走老系统，这里不存
            'channel' => 3,    // H5
            'login_time' => time(),
            'login_ip' => request()->ip(),
            'create_time' => time(),
            'update_time' => time(),
        ]);

        return $newUser->toArray();
    }


    /**
     * @notes 加密密码（与老系统一致）
     * @param string $password 明文密码
     * @return string MD5加密后的密码
     * @author Claude
     * @date 2025/11/27
     *
     * 老系统有两种加密方式:
     * 1. 添加用户时: md5(明文密码 + 'puhh8kik')  - hide/suser.php 第6行
     * 2. 修改密码时: md5(md5(明文密码) + 'puhh8kik') - hide/suser.php 第59行
     *
     * 根据用户提供的测试数据:
     * 明文 Aa123456 -> fdbac61e316fff1d2156a26b31318c32
     * 应该使用第2种方式（两次MD5）
     */
    private static function encryptPassword(string $password): string
    {
        return md5(md5($password) . self::PASSWORD_SALT);
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
     * @notes 根据新系统用户ID获取老系统用户信息
     * @param int $newUserId la_user 表的用户ID
     * @return array|null x_user 表的用户信息
     * @author Claude
     * @date 2025/11/29
     *
     * 映射关系：
     * - la_user.sn 存储的是 x_user.userid
     * - la_user.account 存储的是 x_user.username
     */
    public static function getLegacyUserByNewUserId(int $newUserId)
    {
        // 首先从 la_user 获取 sn（存储的是老系统 userid）
        $newUser = User::where('id', $newUserId)->find();

        if (!$newUser) {
            return null;
        }

        // 方法1：通过 sn（老系统 userid）查询
        if ($newUser->sn) {
            $legacyUser = Db::table('x_user')
                ->where('userid', $newUser->sn)
                ->where('status', 1)
                ->find();

            if ($legacyUser) {
                return $legacyUser;
            }
        }

        // 方法2：通过 account（老系统 username）查询
        if ($newUser->account) {
            $legacyUser = Db::table('x_user')
                ->where('username', $newUser->account)
                ->where('status', 1)
                ->find();

            if ($legacyUser) {
                return $legacyUser;
            }
        }

        return null;
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
     * @notes 管理员登录（基于 x_admins 表）
     * @param array $params ['username'=>'用户名', 'password'=>'明文密码']
     * @return array|false
     * @author Claude
     * @date 2025/12/01
     *
     * 登录流程（参考 hide/login.php）：
     * 1. 用户名转小写
     * 2. 密码加密：md5(密码 + 盐值)（注意：管理员是一次MD5，用户是两次MD5）
     * 3. 查询 x_admins 表验证
     * 4. 更新登录信息
     * 5. 同步/创建新系统用户
     * 6. 生成 token
     */
    public static function adminLogin(array $params)
    {
        try {
            $username = strtolower($params['username']); // 转小写（与老系统一致）
            $password = $params['password'];
            $ip = request()->ip();

            // 步骤1: 加密密码（管理员使用一次MD5，与老系统一致）
            // 老系统：md5($_POST['pass'] . $config['upass'])
            $encryptedPassword = md5($password . self::PASSWORD_SALT);

            // 步骤2: 查询管理员
            // 注意: x_admins 表只有 adminid, adminname, adminpass, regtime 等基础字段
            $admin = Db::table('x_admins')
                ->where('adminname', $username)
                ->where('adminpass', $encryptedPassword)
                ->find();

            if (!$admin) {
                throw new \Exception('用户名或密码不正确');
            }

            // 步骤3: 同步到新系统用户（la_user 表）
            $newUser = self::syncAdminToNewUser($admin);

            // 步骤4: 生成 token
            $userInfo = UserTokenService::setToken($newUser['id'], 1);

            // 步骤5: 返回管理员信息
            // 注意: x_admins 表只有基础字段 adminid, adminname, adminpass, regtime
            return [
                'adminInfo' => [
                    'id' => $newUser['id'],              // 新系统 ID
                    'adminid' => $admin['adminid'],      // 老系统管理员 ID
                    'adminname' => $admin['adminname'],
                ],
                'token' => $userInfo['token'],
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 同步管理员到新系统用户（la_user 表）
     * @param array $admin x_admins 表的管理员数据
     * @return array 新系统用户数据
     * @author Claude
     * @date 2025/12/01
     */
    private static function syncAdminToNewUser(array $admin): array
    {
        // 使用 "admin_" + adminname 作为 account，区分普通用户
        $account = 'admin_' . $admin['adminname'];

        $newUser = User::where('account', $account)->find();

        if ($newUser) {
            // 已存在，更新登录信息
            $newUser->login_time = time();
            $newUser->login_ip = request()->ip();
            if (empty($newUser->sn) || $newUser->sn != $admin['adminid']) {
                $newUser->sn = $admin['adminid'];
            }
            $newUser->save();
            return $newUser->toArray();
        }

        // 不存在，创建新用户
        $newUser = User::create([
            'sn' => $admin['adminid'],
            'account' => $account,
            'nickname' => $admin['adminname'],
            'mobile' => '',
            'avatar' => '',
            'password' => '',
            'channel' => 4,    // 4=管理后台
            'login_time' => time(),
            'login_ip' => request()->ip(),
            'create_time' => time(),
            'update_time' => time(),
        ]);

        return $newUser->toArray();
    }


    /**
     * @notes 根据新系统用户ID获取管理员信息
     * @param int $newUserId la_user 表的用户ID
     * @return array|null x_admins 表的管理员信息
     * @author Claude
     * @date 2025/12/01
     */
    public static function getAdminByNewUserId(int $newUserId)
    {
        $newUser = User::where('id', $newUserId)->find();

        if (!$newUser) {
            return null;
        }

        // 检查是否是管理员账号（account 以 "admin_" 开头）
        if (strpos($newUser->account, 'admin_') !== 0) {
            return null;
        }

        // 通过 sn（老系统 adminid）查询
        if ($newUser->sn) {
            $admin = Db::table('x_admins')
                ->where('adminid', $newUser->sn)
                ->find();

            if ($admin) {
                return $admin;
            }
        }

        // 通过 adminname 查询
        $adminname = substr($newUser->account, 6); // 去掉 "admin_" 前缀
        $admin = Db::table('x_admins')
            ->where('adminname', $adminname)
            ->find();

        return $admin;
    }


    /**
     * @notes 记录管理员登录失败日志
     * @param string $username
     * @param string $ip
     * @param string $password 加密后的密码
     * @return void
     * @author Claude
     * @date 2025/12/01
     */
    private static function logAdminLoginFail(string $username, string $ip, string $password)
    {
        try {
            Db::table('x_admins_login')->insert([
                'adminname' => $username,
                'adminpass' => $password,
                'ifok' => 0,
                'time' => date('Y-m-d H:i:s'),
                'ip' => $ip,
                'server' => '',
                'os' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ]);
        } catch (\Exception $e) {
            // 日志失败不影响主流程
        }
    }


    /**
     * @notes 记录管理员登录成功日志
     * @param string $username
     * @param string $ip
     * @return void
     * @author Claude
     * @date 2025/12/01
     */
    private static function logAdminLoginSuccess(string $username, string $ip)
    {
        try {
            Db::table('x_admins_login')->insert([
                'adminname' => $username,
                'adminpass' => 'OK',
                'ifok' => 1,
                'time' => date('Y-m-d H:i:s'),
                'ip' => $ip,
                'server' => '',
                'os' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ]);
        } catch (\Exception $e) {
            // 日志失败不影响主流程
        }
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
