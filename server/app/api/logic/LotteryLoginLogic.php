<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 基于老表 (x_user) 的登录与下单逻辑
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-27
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\api\service\UserTokenService;
use app\common\logic\BaseLogic;
use think\facade\{Db, Config};

/**
 * 彩票系统登录逻辑（基于 x_user 表）
 * Class LotteryLoginLogic
 * @package app\api\logic
 */
class LotteryLoginLogic extends BaseLogic
{
    /**
     * 密码盐值（需要与老系统保持一致）
     * 老系统：md5(明文密码 . 'puhh8kik')
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

            // 步骤8: 生成 token（使用新系统的 UserTokenService）
            $userInfo = self::generateToken($user, $params['terminal'] ?? 1);

            // 步骤9: 合并老系统的用户信息（保留 kmoney 等字段）
            $userInfo['legacy_userid'] = $user['userid'];     // 老系统的 userid
            $userInfo['legacy_username'] = $user['username']; // 老系统的 username
            $userInfo['kmoney'] = $user['kmoney'] ?? 0;       // 老系统的余额
            $userInfo['legacy_status'] = $user['status'];     // 老系统的状态

            // 返回登录信息
            return $userInfo;

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
     * 老系统加密规则：
     * 1. 先对明文密码 MD5
     * 2. 再拼接盐值后再 MD5
     * 即：md5(md5(password) . 'puhh8kik')
     */
    private static function encryptPassword(string $password): string
    {
        $step1 = md5($password);
        $step2 = md5($step1 . self::PASSWORD_SALT);
        return $step2;
    }


    /**
     * @notes 生成 Token（使用新系统的 UserTokenService）
     * @param array $legacyUser x_user 表的用户数据
     * @param int $terminal 终端类型
     * @return array 包含 token 和用户信息
     * @throws \Exception
     * @author Claude
     * @date 2025/11/27
     */
    private static function generateToken(array $legacyUser, int $terminal): array
    {
        // 步骤1: 查找或创建 la_user 映射记录
        $newUserId = self::findOrCreateUserMapping($legacyUser);

        // 步骤2: 使用新系统的 UserTokenService 生成 token
        $userInfo = UserTokenService::setToken($newUserId, $terminal);

        return $userInfo;
    }


    /**
     * @notes 查找或创建用户映射（x_user -> la_user）
     * @param array $legacyUser x_user 表的用户数据
     * @return int la_user 表的用户ID
     * @throws \Exception
     * @author Claude
     * @date 2025/11/27
     */
    private static function findOrCreateUserMapping(array $legacyUser): int
    {
        // 方案A: 在 la_user 表中查找 account 相同的记录
        $newUser = User::where('account', $legacyUser['username'])->find();

        if ($newUser) {
            return $newUser->id;
        }

        // 方案B: 创建新用户映射记录
        $userSn = User::createUserSn();
        $avatar = ConfigService::get('default_image', 'user_avatar');

        $newUser = User::create([
            'sn' => $userSn,
            'avatar' => $avatar,
            'nickname' => $legacyUser['name'] ?: ('用户' . $legacyUser['userid']),
            'account' => $legacyUser['username'],
            'mobile' => $legacyUser['tel'] ?: '',
            // 密码字段可以留空或设置一个随机密码（因为实际验证在 x_user）
            'password' => md5(uniqid()),
            'channel' => 1,
            'sex' => self::convertSex($legacyUser['sex'] ?? ''),
            'create_time' => strtotime($legacyUser['regtime'] ?? 'now'),
            'status' => $legacyUser['status'] ?? 1,
        ]);

        return $newUser->id;
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
     * @date 2025/11/27
     */
    public static function getLegacyUserByNewUserId(int $newUserId)
    {
        // 通过 account 字段关联查询
        $newUser = User::find($newUserId);

        if (!$newUser) {
            return null;
        }

        // 查询老系统用户
        $legacyUser = Db::table('x_user')
            ->where('username', $newUser->account)
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
