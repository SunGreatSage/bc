<?php

namespace app\common\model\lottery;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 彩票盘口模型（使用新表 la_plate）
 */
class LotteryPlate extends BaseModel
{
    use SoftDelete;

    protected $name = 'plate';  // 对应表 la_plate
    protected $deleteTime = 'deleted_at';

    /**
     * 字段类型转换
     */
    protected $type = [
        'created_at' => 'int',
        'updated_at' => 'int',
        'deleted_at' => 'int',
    ];

    /**
     * 自动时间戳
     */
    protected $autoWriteTimestamp = 'int';

    /**
     * 获取启用的盘口列表
     */
    public static function getEnabledPlates($gameId = 0)
    {
        $where = ['status' => 1];
        if ($gameId > 0) {
            $where['game_id'] = $gameId;
        }
        return self::where($where)->order('id', 'asc')->select();  // 改为按ID排序
    }

    /**
     * 根据盘口代码获取盘口信息
     * 注意：新表字段名为 code，不是 plate_code
     */
    public static function getByCode($plateCode, $gameId)
    {
        return self::where([
            'code' => $plateCode,  // 新表字段名为 code
            'game_id' => $gameId,
            'status' => 1
        ])->find();
    }
}
