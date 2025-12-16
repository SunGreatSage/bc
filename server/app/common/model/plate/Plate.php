<?php
declare(strict_types=1);

namespace app\common\model\plate;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 盘口模型
 * Class Plate
 * @package app\common\model\plate
 */
class Plate extends BaseModel
{
    use SoftDelete;

    protected $name = 'plate';
    protected $deleteTime = 'deleted_at';

    /**
     * 字段类型转换(使用整数时间戳,匹配数据库int类型)
     */
    protected $type = [
        'created_at' => 'int',
        'updated_at' => 'int',
        'deleted_at' => 'int',
    ];

    /**
     * 自动时间戳(使用整数时间戳,不含微秒)
     */
    protected $autoWriteTimestamp = 'int';

    /**
     * 创建时间字段
     */
    protected $createTime = 'created_at';

    /**
     * 更新时间字段
     */
    protected $updateTime = 'updated_at';

    /**
     * 关联用户盘口
     */
    public function userPlates()
    {
        return $this->hasMany(UserPlate::class, 'plate_id', 'id');
    }

    /**
     * 状态搜索器
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value !== '' && $value !== null) {
            $query->where('status', '=', $value);
        }
    }

    /**
     * 盘口代码搜索器
     */
    public function searchCodeAttr($query, $value)
    {
        if ($value) {
            $query->where('code', 'like', '%' . $value . '%');
        }
    }

    /**
     * 盘口名称搜索器
     */
    public function searchNameAttr($query, $value)
    {
        if ($value) {
            $query->where('name', 'like', '%' . $value . '%');
        }
    }

    /**
     * 游戏ID搜索器
     */
    public function searchGameIdAttr($query, $value)
    {
        if ($value) {
            $query->where('game_id', '=', $value);
        }
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = $data['status'] ?? 0;
        return $status == 1 ? '启用' : '禁用';
    }
}
