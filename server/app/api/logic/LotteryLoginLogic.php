<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 基于新表 (la_user) 的登录逻辑
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-12-11
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\model\user\User;
use app\api\service\UserTokenService;
use think\facade\Db;

/**
 * 彩票系统登录逻辑（基于新表 la_user）
 * Class LotteryLoginLogic
 * @package app\api\logic
 */
class LotteryLoginLogic extends BaseLogic
{
    /**
     * @notes 用户登录（基于 la_user 表）
     * @param array $params ['username'=>'用户名', 'password'=>'明文密码', 'terminal'=>'终端类型']
     * @return array|false
     * @author Claude
     * @date 2025/12/11
     *
     * 登录流程：
     * 1. 验证用户名格式
     * 2. 查询 la_user 表验证密码（MD5）
     * 3. 验证账号状态（status=1）
     * 4. 更新登录信息
     * 5. 通过 UserTokenService 生成 token（写入 la_user_session）
     * 6. 返回用户信息
     */
    public static function login(array $params)
    {
        try {
            $username = trim($params['username']);
            $password = $params['password'];
            $terminal = $params['terminal'] ?? 1;
            $ip = request()->ip();

            // 步骤1: 验证用户名格式
            if (empty($username)) {
                throw new \Exception('请输入用户名');
            }

            if (empty($password)) {
                throw new \Exception('请输入密码');
            }

            // 步骤2: 加密密码（简单 MD5）
            $encryptedPassword = md5($password);

            // 步骤3: 查询用户
            $user = User::where('username', $username)
                ->where('password', $encryptedPassword)
                ->find();

            if (!$user) {
                throw new \Exception('用户名或密码不正确');
            }

            // 步骤4: 检查账号状态
            if ($user->status != 1) {
                throw new \Exception('账号已被禁用');
            }

            // 步骤5: 更新登录信息
            $user->login_time = time();
            $user->login_ip = $ip;
            $user->save();

            // 步骤6: 通过 UserTokenService 生成 token（写入 la_user_session）
            $userInfo = UserTokenService::setToken($user->id, $terminal);

            // 步骤7: 获取账户余额
            $account = Db::table('la_user_account')
                ->where('user_id', $user->id)
                ->find();

            // 步骤8: 返回用户信息
            return [
                'userInfo' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nickname' => $userInfo['nickname'] ?? ($user->nickname ?: $user->username),
                    'mobile' => $userInfo['mobile'] ?? ($user->mobile ?: ''),
                    'avatar' => $userInfo['avatar'] ?? ($user->avatar ?: ''),
                    'balance' => $account ? (float)$account['balance'] : 0.00,
                    'status' => $user->status,
                ],
                'token' => $userInfo['token'],
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 根据用户ID获取用户信息
     * @param int $userId la_user 表的用户ID
     * @return array|null
     * @author Claude
     * @date 2025/12/11
     */
    public static function getUserById(int $userId)
    {
        $user = User::where('id', $userId)
            ->where('status', 1)
            ->find();

        if (!$user) {
            return null;
        }

        // 获取账户信息
        $account = Db::table('la_user_account')
            ->where('user_id', $userId)
            ->find();

        return [
            'id' => $user->id,
            'username' => $user->username,
            'nickname' => $user->nickname ?: $user->username,
            'mobile' => $user->mobile ?: '',
            'avatar' => $user->avatar ?: '',
            'balance' => $account ? (float)$account['balance'] : 0.00,
            'frozen_amount' => $account ? (float)$account['frozen_amount'] : 0.00,
            'total_bet' => $account ? (float)$account['total_bet'] : 0.00,
            'total_prize' => $account ? (float)$account['total_prize'] : 0.00,
            'status' => $user->status,
        ];
    }


    /**
     * @notes 获取用户余额
     * @param int $userId
     * @return float
     * @author Claude
     * @date 2025/12/11
     */
    public static function getUserBalance(int $userId): float
    {
        $account = Db::table('la_user_account')
            ->where('user_id', $userId)
            ->find();

        return $account ? (float)$account['balance'] : 0.00;
    }


    /**
     * @notes 修改密码
     * @param int $userId
     * @param string $oldPassword 旧密码（明文）
     * @param string $newPassword 新密码（明文）
     * @return bool
     * @author Claude
     * @date 2025/12/11
     */
    public static function changePassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        try {
            // 验证旧密码
            $user = User::where('id', $userId)->find();

            if (!$user) {
                throw new \Exception('用户不存在');
            }

            $encryptedOldPassword = md5($oldPassword);
            if ($user->password !== $encryptedOldPassword) {
                throw new \Exception('旧密码不正确');
            }

            // 更新为新密码
            $encryptedNewPassword = md5($newPassword);
            $user->password = $encryptedNewPassword;
            $user->update_time = time();
            $user->save();

            return true;

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 扣除用户余额（投注时调用）
     * @param int $userId 用户ID
     * @param float $amount 扣除金额
     * @param string $remark 备注
     * @return bool
     * @author Claude
     * @date 2025/12/11
     */
    public static function deductBalance(int $userId, float $amount, string $remark = '投注扣款'): bool
    {
        try {
            Db::startTrans();

            // 查询账户（加锁）
            $account = Db::table('la_user_account')
                ->where('user_id', $userId)
                ->lock(true)
                ->find();

            if (!$account) {
                throw new \Exception('账户不存在');
            }

            // 检查余额
            if ($account['balance'] < $amount) {
                throw new \Exception('余额不足');
            }

            // 扣除余额
            $balanceBefore = $account['balance'];
            $balanceAfter = $balanceBefore - $amount;

            Db::table('la_user_account')
                ->where('user_id', $userId)
                ->update([
                    'balance' => $balanceAfter,
                    'total_bet' => Db::raw('total_bet + ' . $amount),
                    'updated_at' => time(),
                ]);

            // 记录流水
            self::addAccountLog([
                'user_id' => $userId,
                'change_type' => 3, // 3=投注
                'change_amount' => -$amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'remark' => $remark,
            ]);

            Db::commit();
            return true;

        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 增加用户余额（中奖派奖时调用）
     * @param int $userId 用户ID
     * @param float $amount 增加金额
     * @param string $remark 备注
     * @return bool
     * @author Claude
     * @date 2025/12/11
     */
    public static function addBalance(int $userId, float $amount, string $remark = '中奖派奖'): bool
    {
        try {
            Db::startTrans();

            // 查询账户（加锁）
            $account = Db::table('la_user_account')
                ->where('user_id', $userId)
                ->lock(true)
                ->find();

            if (!$account) {
                throw new \Exception('账户不存在');
            }

            // 增加余额
            $balanceBefore = $account['balance'];
            $balanceAfter = $balanceBefore + $amount;

            Db::table('la_user_account')
                ->where('user_id', $userId)
                ->update([
                    'balance' => $balanceAfter,
                    'total_prize' => Db::raw('total_prize + ' . $amount),
                    'updated_at' => time(),
                ]);

            // 记录流水
            self::addAccountLog([
                'user_id' => $userId,
                'change_type' => 4, // 4=中奖
                'change_amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'remark' => $remark,
            ]);

            Db::commit();
            return true;

        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 记录账户流水
     * @param array $data
     * @return void
     * @author Claude
     * @date 2025/12/11
     */
    private static function addAccountLog(array $data)
    {
        try {
            // 生成流水单号
            $sn = 'AL' . date('YmdHis') . mt_rand(1000, 9999);

            Db::table('la_account_log')->insert([
                'sn' => $sn,
                'user_id' => $data['user_id'],
                'change_type' => $data['change_type'],
                'change_amount' => $data['change_amount'],
                'balance_before' => $data['balance_before'],
                'balance_after' => $data['balance_after'],
                'frozen_before' => $data['frozen_before'] ?? 0,
                'frozen_after' => $data['frozen_after'] ?? 0,
                'related_sn' => $data['related_sn'] ?? '',
                'related_type' => $data['related_type'] ?? 0,
                'remark' => $data['remark'] ?? '',
                'operator_id' => $data['operator_id'] ?? 0,
                'ip' => request()->ip(),
                'created_at' => time(),
            ]);
        } catch (\Exception $e) {
            // 流水记录失败不影响主流程
        }
    }


    /**
     * @notes 注册新用户
     * @param array $params ['username'=>'用户名', 'password'=>'明文密码', 'nickname'=>'昵称', 'mobile'=>'手机号']
     * @return array|false
     * @author Claude
     * @date 2025/12/11
     */
    public static function register(array $params)
    {
        try {
            $username = trim($params['username']);
            $password = $params['password'];
            $nickname = $params['nickname'] ?? $username;
            $mobile = $params['mobile'] ?? '';

            // 验证用户名
            if (empty($username)) {
                throw new \Exception('请输入用户名');
            }

            if (strlen($username) < 4 || strlen($username) > 20) {
                throw new \Exception('用户名长度为4-20个字符');
            }

            // 验证密码
            if (empty($password)) {
                throw new \Exception('请输入密码');
            }

            if (strlen($password) < 6) {
                throw new \Exception('密码长度不能少于6位');
            }

            // 检查用户名是否已存在
            $exists = User::where('username', $username)->find();
            if ($exists) {
                throw new \Exception('用户名已存在');
            }

            Db::startTrans();

            // 创建用户
            $user = User::create([
                'username' => $username,
                'password' => md5($password),
                'nickname' => $nickname,
                'mobile' => $mobile,
                'avatar' => '',
                'status' => 1,
                'create_time' => time(),
                'update_time' => time(),
            ]);

            // 创建账户
            Db::table('la_user_account')->insert([
                'user_id' => $user->id,
                'balance' => 0.00,
                'frozen_amount' => 0.00,
                'total_recharge' => 0.00,
                'total_withdraw' => 0.00,
                'total_bet' => 0.00,
                'total_prize' => 0.00,
                'total_commission' => 0.00,
                'status' => 1,
                'version' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ]);

            // 创建用户扩展
            Db::table('la_user_extend')->insert([
                'user_id' => $user->id,
                'parent_id' => 0,
                'ancestor_ids' => '',
                'level' => 0,
                'is_agent' => 0,
                'agent_rate' => 0.00,
                'total_downlines' => 0,
                'direct_downlines' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ]);

            Db::commit();

            return [
                'id' => $user->id,
                'username' => $user->username,
                'nickname' => $user->nickname,
            ];

        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }
}
