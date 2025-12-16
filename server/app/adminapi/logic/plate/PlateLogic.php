<?php
declare(strict_types=1);

namespace app\adminapi\logic\plate;

use app\common\logic\BaseLogic;
use app\common\model\plate\Plate;

/**
 * 盘口管理逻辑
 * Class PlateLogic
 * @package app\adminapi\logic\plate
 */
class PlateLogic extends BaseLogic
{
    /**
     * 盘口列表
     * @param array $params
     * @return array
     */
    public static function lists(array $params): array
    {
        $where = [];

        // 盘口代码搜索
        if (!empty($params['code'])) {
            $where[] = ['code', 'like', '%' . $params['code'] . '%'];
        }

        // 盘口名称搜索
        if (!empty($params['name'])) {
            $where[] = ['name', 'like', '%' . $params['name'] . '%'];
        }

        // 状态筛选
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', $params['status']];
        }

        // 游戏ID筛选
        if (!empty($params['game_id'])) {
            $where[] = ['game_id', '=', $params['game_id']];
        }

        // 分页参数(强制转换为整数)
        $page = (int)($params['page'] ?? 1);
        $limit = (int)($params['limit'] ?? 15);

        // 查询数据
        $lists = Plate::where($where)
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->page($page, $limit)
            ->select()
            ->toArray();

        // 统计总数
        $count = Plate::where($where)->count();

        return [
            'lists' => $lists,
            'count' => $count,
            'page_no' => $page,
            'page_size' => $limit,
        ];
    }

    /**
     * 盘口详情
     * @param array $params
     * @return array
     */
    public static function detail(array $params): array
    {
        return Plate::findOrEmpty($params['id'])->toArray();
    }

    /**
     * 添加盘口
     * @param array $params
     * @return void
     */
    public static function add(array $params): void
    {
        // 检查盘口代码是否已存在
        $exists = Plate::where('code', $params['code'])->count();
        if ($exists > 0) {
            throw new \Exception('盘口代码已存在');
        }

        Plate::create([
            'code' => $params['code'],
            'name' => $params['name'],
            'game_id' => $params['game_id'] ?? 200,
            'open_time' => $params['open_time'] ?? '06:00',
            'close_time' => $params['close_time'] ?? '09:30',
            'draw_time' => $params['draw_time'] ?? '09:50',
            'close_advance' => $params['close_advance'] ?? 5,
            'status' => $params['status'] ?? 1,
            'sort' => $params['sort'] ?? 0,
            'remark' => $params['remark'] ?? '',
        ]);
    }

    /**
     * 编辑盘口
     * @param array $params
     * @return bool
     */
    public static function edit(array $params): bool
    {
        try {
            $plate = Plate::findOrEmpty($params['id']);
            if ($plate->isEmpty()) {
                throw new \Exception('盘口不存在');
            }

            // 如果修改了盘口代码,检查是否与其他盘口重复
            if (isset($params['code']) && $params['code'] != $plate->code) {
                $exists = Plate::where('code', $params['code'])
                    ->where('id', '<>', $params['id'])
                    ->count();
                if ($exists > 0) {
                    throw new \Exception('盘口代码已存在');
                }
            }

            $plate->save([
                'code' => $params['code'] ?? $plate->code,
                'name' => $params['name'] ?? $plate->name,
                'game_id' => $params['game_id'] ?? $plate->game_id,
                'open_time' => $params['open_time'] ?? $plate->open_time,
                'close_time' => $params['close_time'] ?? $plate->close_time,
                'draw_time' => $params['draw_time'] ?? $plate->draw_time,
                'close_advance' => $params['close_advance'] ?? $plate->close_advance,
                'status' => $params['status'] ?? $plate->status,
                'sort' => $params['sort'] ?? $plate->sort,
                'remark' => $params['remark'] ?? $plate->remark,
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 删除盘口
     * @param array $params
     * @return void
     */
    public static function delete(array $params): void
    {
        $plate = Plate::findOrEmpty($params['id']);
        if ($plate->isEmpty()) {
            throw new \Exception('盘口不存在');
        }

        // 检查是否有用户使用该盘口
        $userCount = $plate->userPlates()->count();
        if ($userCount > 0) {
            throw new \Exception('该盘口下存在用户,无法删除');
        }

        // 软删除
        $plate->delete();
    }

    /**
     * 修改盘口状态
     * @param array $params
     * @return void
     */
    public static function status(array $params): void
    {
        $plate = Plate::findOrEmpty($params['id']);
        if ($plate->isEmpty()) {
            throw new \Exception('盘口不存在');
        }

        $plate->status = $params['status'];
        $plate->save();
    }

    /**
     * 获取所有启用的盘口
     * @return array
     */
    public static function getAllData(): array
    {
        return Plate::where('status', 1)
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();
    }
}
