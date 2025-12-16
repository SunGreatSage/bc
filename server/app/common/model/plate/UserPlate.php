<?php
declare(strict_types=1);

namespace app\common\model\plate;

use app\common\model\BaseModel;
use app\common\model\user\User;
use think\model\concern\SoftDelete;

/**
 * 用户盘口关系模型
 * Class UserPlate
 * @package app\common\model\plate
 */
class UserPlate extends BaseModel
{
    use SoftDelete;

    protected $name = 'user_plate';
    protected $deleteTime = 'deleted_at';

    /**
     * 字段类型转换
     */
    protected $type = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * 自动时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * 创建时间字段
     */
    protected $createTime = 'created_at';

    /**
     * 更新时间字段
     */
    protected $updateTime = 'updated_at';

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 关联盘口
     */
    public function plate()
    {
        return $this->belongsTo(Plate::class, 'plate_id', 'id');
    }

    /**
     * 用户ID搜索器
     */
    public function searchUserIdAttr($query, $value)
    {
        if ($value) {
            $query->where('user_id', '=', $value);
        }
    }

    /**
     * 盘口ID搜索器
     */
    public function searchPlateIdAttr($query, $value)
    {
        if ($value) {
            $query->where('plate_id', '=', $value);
        }
    }

    /**
     * 盘口代码搜索器
     */
    public function searchPlateCodeAttr($query, $value)
    {
        if ($value) {
            $query->where('plate_code', '=', $value);
        }
    }

    /**
     * 是否代理搜索器
     */
    public function searchIsAgentAttr($query, $value)
    {
        if ($value !== '' && $value !== null) {
            $query->where('is_agent', '=', $value);
        }
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
     * 获取用户类型文本
     */
    public function getUserTypeTextAttr($value, $data)
    {
        $isAgent = $data['is_agent'] ?? 0;
        return $isAgent == 1 ? '代理' : '普通会员';
    }

    /**
     * 获取代理等级文本
     */
    public function getAgentLevelTextAttr($value, $data)
    {
        $level = $data['agent_level'] ?? 0;
        $map = [
            0 => '普通会员',
            1 => '一级代理',
            2 => '二级代理',
            3 => '三级代理',
        ];
        return $map[$level] ?? '未知';
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
