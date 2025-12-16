<?php
declare(strict_types=1);

namespace app\adminapi\logic\plate;

use app\common\logic\BaseLogic;
use app\common\model\user\User;
use app\common\model\lottery\AccountLog;
use app\common\model\lottery\UserAccount;
use think\facade\Db;

/**
 * 用户管理逻辑
 * Class UserLogic
 * @package app\adminapi\logic\plate
 */
class UserLogic extends BaseLogic
{
    /**
     * 用户详情
     */
    public static function detail(array $params): array
    {
        $user = User::findOrEmpty($params['id'])->toArray();

        if (empty($user)) {
            throw new \Exception('用户不存在');
        }

        return $user;
    }

    /**
     * 新增用户
     */
    public static function add(array $params): void
    {
        // 获取当前管理员信息
        $adminInfo = request()->adminInfo ?? [];
        $adminRoot = $adminInfo['root'] ?? 1;
        $adminId = $adminInfo['admin_id'] ?? 0;
        $initialMoney = (float)($params['user_money'] ?? 0.00);

        $creditLimit = 0; // 代理信用额度

        // 只有代理创建用户时才需要检查信用额度
        if ($adminRoot == 2 && $initialMoney > 0) {
            $agent = Db::table('la_admin')
                ->where('id', $adminId)
                ->where('root', 2)
                ->find();

            if (!$agent) {
                throw new \Exception('代理账户不存在');
            }

            $creditLimit = (float)($agent['credit_limit'] ?? 0);
            if ($creditLimit < $initialMoney) {
                throw new \Exception('信用额度不足,当前可用额度:' . $creditLimit);
            }
        }

        // 检查用户名是否已存在
        $exists = User::where('username', $params['username'])->count();
        if ($exists > 0) {
            throw new \Exception('用户名已存在');
        }

        // 检查手机号是否已存在
        if (!empty($params['mobile'])) {
            $mobileExists = User::where('mobile', $params['mobile'])->count();
            if ($mobileExists > 0) {
                throw new \Exception('手机号已被使用');
            }
        }

        Db::startTrans();
        try {
            // 密码加密(MD5)
            $password = md5($params['password']);

            // 创建用户,fid 设置为创建者的 admin_id
            $user = User::create([
                'username' => $params['username'],
                'password' => $password,
                'nickname' => $params['nickname'] ?? '',
                'mobile' => $params['mobile'] ?? '',
                'status' => $params['status'] ?? 1,
                'fid' => $adminId,  // fid 设置为当前管理员ID(包括总管理和代理)
                'create_time' => time(),
            ]);

            // ✅ 始终创建用户账户(即使余额为0)
            UserAccount::create([
                'user_id' => $user->id,
                'balance' => $initialMoney,
                'frozen_amount' => 0,
                'version' => 1,
            ]);

            // 如果有初始余额,记录流水并扣除代理额度
            if ($initialMoney > 0) {
                // 只有代理创建用户才扣除额度
                if ($adminRoot == 2) {
                    $newCredit = $creditLimit - $initialMoney;
                    Db::table('la_admin')
                        ->where('id', $adminId)
                        ->update(['credit_limit' => $newCredit]);

                    // 记录代理额度扣除流水
                    $sn = self::generateSn('AGT');
                    AccountLog::create([
                        'sn' => $sn,
                        'user_id' => 0,
                        'admin_id' => $adminId,
                        'change_type' => 7,
                        'change_amount' => -$initialMoney,
                        'balance_before' => $creditLimit,
                        'balance_after' => $newCredit,
                        'frozen_before' => 0,
                        'frozen_after' => 0,
                        'related_sn' => '',
                        'related_type' => 0,
                        'remark' => '开设用户账户[' . $params['username'] . '],扣除额度',
                        'operator_id' => $adminId,
                        'ip' => request()->ip(),
                        'created_at' => time(),
                    ]);

                    // 代理创建用户的充值流水
                    $userSn = self::generateSn('USR');
                    AccountLog::create([
                        'sn' => $userSn,
                        'user_id' => $user->id,
                        'admin_id' => 0,
                        'change_type' => 1,  // 1=充值
                        'change_amount' => $initialMoney,
                        'balance_before' => 0,
                        'balance_after' => $initialMoney,
                        'frozen_before' => 0,
                        'frozen_after' => 0,
                        'related_sn' => '',
                        'related_type' => 0,
                        'remark' => '代理[' . $adminId . ']开户充值',
                        'operator_id' => $adminId,
                        'ip' => request()->ip(),
                        'created_at' => time(),
                    ]);
                } else {
                    // 总管理员创建用户的充值流水(不扣除额度)
                    $userSn = self::generateSn('USR');
                    AccountLog::create([
                        'sn' => $userSn,
                        'user_id' => $user->id,
                        'admin_id' => 0,
                        'change_type' => 1,  // 1=充值
                        'change_amount' => $initialMoney,
                        'balance_before' => 0,
                        'balance_after' => $initialMoney,
                        'frozen_before' => 0,
                        'frozen_after' => 0,
                        'related_sn' => '',
                        'related_type' => 0,
                        'remark' => '管理员开户充值',
                        'operator_id' => $adminId,
                        'ip' => request()->ip(),
                        'created_at' => time(),
                    ]);
                }
            }

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 编辑用户
     */
    public static function edit(array $params): bool
    {
        try {
            $user = User::findOrEmpty($params['id']);
            if ($user->isEmpty()) {
                throw new \Exception('用户不存在');
            }

            // 如果修改了用户名,检查是否与其他用户重复
            if (isset($params['username']) && $params['username'] != $user->username) {
                $exists = User::where('username', $params['username'])
                    ->where('id', '<>', $params['id'])
                    ->count();
                if ($exists > 0) {
                    throw new \Exception('用户名已存在');
                }
            }

            // 如果修改了手机号,检查是否与其他用户重复
            if (isset($params['mobile']) && !empty($params['mobile']) && $params['mobile'] != $user->mobile) {
                $mobileExists = User::where('mobile', $params['mobile'])
                    ->where('id', '<>', $params['id'])
                    ->count();
                if ($mobileExists > 0) {
                    throw new \Exception('手机号已被使用');
                }
            }

            $updateData = [
                'nickname' => $params['nickname'] ?? $user->nickname,
                'mobile' => $params['mobile'] ?? $user->mobile,
                'status' => $params['status'] ?? $user->status,
                'update_time' => time(),
            ];

            // 如果需要修改密码
            if (!empty($params['password'])) {
                $updateData['password'] = md5($params['password']);
            }

            // 如果需要修改用户名
            if (isset($params['username'])) {
                $updateData['username'] = $params['username'];
            }

            $user->save($updateData);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 删除用户
     */
    public static function delete(array $params): void
    {
        $user = User::findOrEmpty($params['id']);
        if ($user->isEmpty()) {
            throw new \Exception('用户不存在');
        }

        // 检查用户账户是否有余额或冻结金额
        $account = UserAccount::where('user_id', $params['id'])->find();
        if ($account) {
            if ($account->balance > 0) {
                throw new \Exception('用户还有余额,无法删除');
            }
            if ($account->frozen_amount > 0) {
                throw new \Exception('用户有冻结金额,无法删除');
            }
        }

        // 检查用户是否有未结算的投注
        $hasPendingBets = Db::table('la_betting_record')
            ->where('user_id', $params['id'])
            ->whereIn('status', [1, 2]) // 1=待开奖, 2=已中奖待派奖
            ->count();

        if ($hasPendingBets > 0) {
            throw new \Exception('用户有未结算的投注,无法删除');
        }

        // 软删除
        $user->delete();
    }

    /**
     * 修改用户状态
     */
    public static function status(array $params): void
    {
        $user = User::findOrEmpty($params['id']);
        if ($user->isEmpty()) {
            throw new \Exception('用户不存在');
        }

        $user->status = $params['status'];
        $user->update_time = time();
        $user->save();
    }

    /**
     * 调整用户余额
     */
    public static function adjustBalance(array $params): bool
    {
        try {
            Db::startTrans();

            // 获取当前管理员信息
            $adminInfo = request()->adminInfo ?? [];
            $adminRoot = $adminInfo['root'] ?? 1;
            $adminId = $adminInfo['admin_id'] ?? 0;

            $user = User::findOrEmpty($params['id']);
            if ($user->isEmpty()) {
                throw new \Exception('用户不存在');
            }

            // 代理只能调整自己创建的用户
            if ($adminRoot == 2 && $user->fid != $adminId) {
                throw new \Exception('无权操作此用户');
            }

            // 获取用户账户(使用悲观锁)
            $account = UserAccount::where('user_id', $user->id)->lock(true)->find();
            if (!$account) {
                throw new \Exception('用户账户不存在');
            }

            $changeAmount = (float)$params['change_amount'];
            $changeType = (int)$params['change_type']; // 1=增加, 2=减少
            $remark = $params['remark'] ?? '';
            $operatorId = $params['operator_id'] ?? 0;

            $creditLimit = 0; // 代理信用额度

            // 只有代理增加用户余额时才需要检查信用额度
            if ($adminRoot == 2 && $changeType == 1) {
                $agent = Db::table('la_admin')
                    ->where('id', $adminId)
                    ->where('root', 2)
                    ->find();

                if (!$agent) {
                    throw new \Exception('代理账户不存在');
                }

                $creditLimit = (float)($agent['credit_limit'] ?? 0);
                if ($creditLimit < $changeAmount) {
                    throw new \Exception('信用额度不足,当前可用额度:' . $creditLimit);
                }
            }

            // 计算变动后余额
            $balanceBefore = (float)$account->balance;
            $frozenBefore = (float)$account->frozen_amount;

            if ($changeType == 1) {
                // 增加
                $balanceAfter = $balanceBefore + $changeAmount;
            } else {
                // 减少
                if ($balanceBefore < $changeAmount) {
                    throw new \Exception('用户余额不足');
                }
                $balanceAfter = $balanceBefore - $changeAmount;
                $changeAmount = -$changeAmount; // 减少时记录为负数
            }

            // 更新账户余额
            $account->balance = $balanceAfter;
            $account->version += 1; // 乐观锁版本号
            $account->updated_at = time();
            $account->save();

            // 只有代理增加余额时才扣除代理额度
            if ($adminRoot == 2 && $changeType == 1) {
                $newCredit = $creditLimit - $changeAmount;
                Db::table('la_admin')
                    ->where('id', $adminId)
                    ->update(['credit_limit' => $newCredit]);

                // 记录代理额度扣除流水
                $agentSn = self::generateSn('AGT');
                AccountLog::create([
                    'sn' => $agentSn,
                    'user_id' => 0,
                    'admin_id' => $adminId,
                    'change_type' => 7,
                    'change_amount' => -abs($changeAmount),
                    'balance_before' => $creditLimit,
                    'balance_after' => $newCredit,
                    'frozen_before' => 0,
                    'frozen_after' => 0,
                    'related_sn' => '',
                    'related_type' => 0,
                    'remark' => '给用户[' . $user->username . ']增加余额,扣除额度',
                    'operator_id' => $adminId,
                    'ip' => request()->ip(),
                    'created_at' => time(),
                ]);
            }
            // 总管理员增加余额不需要扣除额度

            // 记录用户流水
            $sn = self::generateSn('ADJ');
            AccountLog::create([
                'sn' => $sn,
                'user_id' => $user->id,
                'admin_id' => 0,
                'change_type' => 7, // 7=调整
                'change_amount' => $changeAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'frozen_before' => $frozenBefore,
                'frozen_after' => $frozenBefore,
                'related_sn' => '',
                'related_type' => 0,
                'remark' => $remark ?: '管理员调整余额',
                'operator_id' => $operatorId,
                'ip' => request()->ip(),
                'created_at' => time(),
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
     * 生成流水单号
     */
    private static function generateSn(string $prefix = 'ADJ'): string
    {
        return $prefix . date('YmdHis') . rand(1000, 9999);
    }

    /**
     * 开设代理账户
     */
    public static function createAgent(array $params): bool
    {
        try {
            Db::startTrans();

            // 检查账号是否已存在
            $exists = Db::table('la_admin')
                ->where('account', $params['username'])
                ->count();

            if ($exists > 0) {
                throw new \Exception('账号已存在');
            }

            // 密码加密 - 使用MD5(与旧系统保持一致,password字段varchar(32))
            $password = md5($params['password']);

            // 创建代理账户(la_admin 表没有 mobile 字段)
            $adminId = Db::table('la_admin')->insertGetId([
                'root' => 2,  // 代理账户
                'account' => $params['username'],
                'password' => $password,
                'name' => $params['nickname'] ?? '',
                // 'mobile' => $params['mobile'] ?? '',  // la_admin 表中没有此字段
                'credit_limit' => $params['credit_limit'] ?? 0,
                'disable' => $params['status'] == 1 ? 0 : 1,
                'create_time' => time(),
                'update_time' => time(),
            ]);

            // 如果有预充信用额度,记录到流水
            if (!empty($params['credit_limit']) && $params['credit_limit'] > 0) {
                $sn = self::generateSn('AGENT');
                AccountLog::create([
                    'sn' => $sn,
                    'user_id' => 0,  // 系统操作
                    'admin_id' => $adminId,  // 代理ID
                    'change_type' => 7,  // 7=调整
                    'change_amount' => $params['credit_limit'],
                    'balance_before' => 0,
                    'balance_after' => $params['credit_limit'],
                    'frozen_before' => 0,
                    'frozen_after' => 0,
                    'related_sn' => '',
                    'related_type' => 0,
                    'remark' => '开设代理账户-预充信用额度',
                    'operator_id' => $params['operator_id'] ?? 0,
                    'ip' => request()->ip(),
                    'created_at' => time(),
                ]);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 调整代理信用额度
     */
    public static function adjustAgentCredit(array $params): bool
    {
        try {
            Db::startTrans();

            // 获取代理信息
            $agent = Db::table('la_admin')
                ->where('id', $params['id'])
                ->where('root', 2)
                ->find();

            if (!$agent) {
                throw new \Exception('代理账户不存在');
            }

            $changeAmount = (float)$params['change_amount'];
            $changeType = (int)$params['change_type']; // 1=增加, 2=减少
            $remark = $params['remark'] ?? '';
            $operatorId = $params['operator_id'] ?? 0;

            // 计算变动后额度
            $creditBefore = (float)($agent['credit_limit'] ?? 0);

            if ($changeType == 1) {
                // 增加
                $creditAfter = $creditBefore + $changeAmount;
            } else {
                // 减少
                if ($creditBefore < $changeAmount) {
                    throw new \Exception('信用额度不足');
                }
                $creditAfter = $creditBefore - $changeAmount;
                $changeAmount = -$changeAmount; // 减少时记录为负数
            }

            // 更新信用额度
            Db::table('la_admin')
                ->where('id', $params['id'])
                ->update([
                    'credit_limit' => $creditAfter,
                    'update_time' => time(),
                ]);

            // 记录流水
            $sn = self::generateSn('AGT');
            AccountLog::create([
                'sn' => $sn,
                'user_id' => 0,
                'admin_id' => $params['id'],  // 代理ID
                'change_type' => 7,  // 7=调整
                'change_amount' => $changeAmount,
                'balance_before' => $creditBefore,
                'balance_after' => $creditAfter,
                'frozen_before' => 0,
                'frozen_after' => 0,
                'related_sn' => '',
                'related_type' => 0,
                'remark' => $remark ?: '管理员调整信用额度',
                'operator_id' => $operatorId,
                'ip' => request()->ip(),
                'created_at' => time(),
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }
}
