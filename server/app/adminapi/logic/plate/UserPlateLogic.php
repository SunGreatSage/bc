<?php
declare(strict_types=1);

namespace app\adminapi\logic\plate;

use app\common\logic\BaseLogic;
use app\common\model\plate\UserPlate;
use app\common\model\plate\Plate;
use app\common\model\user\User;

/**
 * 用户盘口管理逻辑
 * Class UserPlateLogic
 * @package app\adminapi\logic\plate
 */
class UserPlateLogic extends BaseLogic
{
    /**
     * 用户盘口列表
     * @param array $params
     * @return array
     */
    public static function lists(array $params): array
    {
        $where = [];

        // 用户ID筛选
        if (!empty($params['user_id'])) {
            $where[] = ['user_id', '=', $params['user_id']];
        }

        // 盘口ID筛选
        if (!empty($params['plate_id'])) {
            $where[] = ['plate_id', '=', $params['plate_id']];
        }

        // 盘口代码筛选
        if (!empty($params['plate_code'])) {
            $where[] = ['plate_code', '=', $params['plate_code']];
        }

        // 是否代理筛选
        if (isset($params['is_agent']) && $params['is_agent'] !== '') {
            $where[] = ['is_agent', '=', $params['is_agent']];
        }

        // 状态筛选
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', $params['status']];
        }

        // 分页参数
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 15;

        // 查询数据,关联用户和盘口信息
        $lists = UserPlate::with(['user', 'plate'])
            ->where($where)
            ->order(['id' => 'desc'])
            ->page($page, $limit)
            ->select()
            ->toArray();

        // 统计总数
        $count = UserPlate::where($where)->count();

        return [
            'lists' => $lists,
            'count' => $count,
            'page_no' => $page,
            'page_size' => $limit,
        ];
    }

    /**
     * 用户盘口详情
     * @param array $params
     * @return array
     */
    public static function detail(array $params): array
    {
        return UserPlate::with(['user', 'plate'])
            ->findOrEmpty($params['id'])
            ->toArray();
    }

    /**
     * 添加用户到盘口
     * @param array $params
     * @return void
     */
    public static function add(array $params): void
    {
        // 验证用户是否存在
        $user = User::findOrEmpty($params['user_id']);
        if ($user->isEmpty()) {
            throw new \Exception('用户不存在');
        }

        // 验证盘口是否存在
        $plate = Plate::findOrEmpty($params['plate_id']);
        if ($plate->isEmpty()) {
            throw new \Exception('盘口不存在');
        }

        // 检查用户是否已分配到该盘口
        $exists = UserPlate::where('user_id', $params['user_id'])
            ->where('plate_id', $params['plate_id'])
            ->count();
        if ($exists > 0) {
            throw new \Exception('用户已分配到该盘口');
        }

        UserPlate::create([
            'user_id' => $params['user_id'],
            'plate_id' => $params['plate_id'],
            'plate_code' => $plate->code,
            'is_agent' => $params['is_agent'] ?? 0,
            'agent_level' => $params['agent_level'] ?? 0,
            'commission_rate' => $params['commission_rate'] ?? 0.00,
            'status' => $params['status'] ?? 1,
        ]);
    }

    /**
     * 编辑用户盘口信息
     * @param array $params
     * @return bool
     */
    public static function edit(array $params): bool
    {
        try {
            $userPlate = UserPlate::findOrEmpty($params['id']);
            if ($userPlate->isEmpty()) {
                throw new \Exception('用户盘口关系不存在');
            }

            $userPlate->save([
                'is_agent' => $params['is_agent'] ?? $userPlate->is_agent,
                'agent_level' => $params['agent_level'] ?? $userPlate->agent_level,
                'commission_rate' => $params['commission_rate'] ?? $userPlate->commission_rate,
                'status' => $params['status'] ?? $userPlate->status,
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 删除用户盘口关系
     * @param array $params
     * @return void
     */
    public static function delete(array $params): void
    {
        $userPlate = UserPlate::findOrEmpty($params['id']);
        if ($userPlate->isEmpty()) {
            throw new \Exception('用户盘口关系不存在');
        }

        // 软删除
        $userPlate->delete();
    }

    /**
     * 修改用户盘口状态
     * @param array $params
     * @return void
     */
    public static function status(array $params): void
    {
        $userPlate = UserPlate::findOrEmpty($params['id']);
        if ($userPlate->isEmpty()) {
            throw new \Exception('用户盘口关系不存在');
        }

        $userPlate->status = $params['status'];
        $userPlate->save();
    }

    /**
     * 批量分配用户到盘口
     * @param array $params
     * @return void
     */
    public static function batchAssign(array $params): void
    {
        $userIds = $params['user_ids'];
        $plateId = $params['plate_id'];

        // 验证盘口是否存在
        $plate = Plate::findOrEmpty($plateId);
        if ($plate->isEmpty()) {
            throw new \Exception('盘口不存在');
        }

        $successCount = 0;
        $skipCount = 0;

        foreach ($userIds as $userId) {
            // 检查用户是否存在
            $user = User::find($userId);
            if (!$user) {
                $skipCount++;
                continue;
            }

            // 检查是否已分配
            $exists = UserPlate::where('user_id', $userId)
                ->where('plate_id', $plateId)
                ->count();
            if ($exists > 0) {
                $skipCount++;
                continue;
            }

            // 创建关系
            UserPlate::create([
                'user_id' => $userId,
                'plate_id' => $plateId,
                'plate_code' => $plate->code,
                'is_agent' => $params['is_agent'] ?? 0,
                'agent_level' => $params['agent_level'] ?? 0,
                'commission_rate' => $params['commission_rate'] ?? 0.00,
                'status' => 1,
            ]);

            $successCount++;
        }

        if ($successCount == 0) {
            throw new \Exception('未成功分配任何用户,可能已全部存在');
        }
    }

    /**
     * 获取盘口下的用户列表
     * @param array $params
     * @return array
     */
    public static function getUsersByPlate(array $params): array
    {
        $plateId = $params['plate_id'] ?? 0;

        // 分页参数
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 15;

        $where = [['plate_id', '=', $plateId]];

        // 用户类型筛选
        if (isset($params['is_agent']) && $params['is_agent'] !== '') {
            $where[] = ['is_agent', '=', $params['is_agent']];
        }

        $lists = UserPlate::with(['user'])
            ->where($where)
            ->order(['id' => 'desc'])
            ->page($page, $limit)
            ->select()
            ->toArray();

        $count = UserPlate::where($where)->count();

        return [
            'lists' => $lists,
            'count' => $count,
            'page_no' => $page,
            'page_size' => $limit,
        ];
    }

    /**
     * 获取用户的盘口列表
     * @param array $params
     * @return array
     */
    public static function getPlatesByUser(array $params): array
    {
        $userId = $params['user_id'] ?? 0;

        return UserPlate::with(['plate'])
            ->where('user_id', $userId)
            ->where('status', 1)
            ->select()
            ->toArray();
    }
}
