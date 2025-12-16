<?php
declare(strict_types=1);

namespace app\adminapi\validate\plate;

use app\common\model\plate\Plate;
use app\common\validate\BaseValidate;

/**
 * 盘口验证器
 * Class PlateValidate
 * @package app\adminapi\validate\plate
 */
class PlateValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkPlate',
        'code' => 'require|length:1,10',
        'name' => 'require|length:1,50',
        'game_id' => 'integer',
        'open_time' => 'regex:/^\d{2}:\d{2}$/',
        'close_time' => 'regex:/^\d{2}:\d{2}$/',
        'draw_time' => 'regex:/^\d{2}:\d{2}$/',
        'close_advance' => 'integer|egt:0',
        'status' => 'in:0,1',
        'sort' => 'integer|egt:0',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'code.require' => '请填写盘口代码',
        'code.length' => '盘口代码长度须在1-10位字符',
        'name.require' => '请填写盘口名称',
        'name.length' => '盘口名称长度须在1-50位字符',
        'game_id.integer' => '游戏ID格式错误',
        'open_time.regex' => '开盘时间格式错误(HH:mm)',
        'close_time.regex' => '封盘时间格式错误(HH:mm)',
        'draw_time.regex' => '开奖时间格式错误(HH:mm)',
        'close_advance.integer' => '提前封盘时间格式错误',
        'close_advance.egt' => '提前封盘时间不能为负数',
        'status.in' => '状态值错误',
        'sort.integer' => '排序值格式错误',
        'sort.egt' => '排序值不能为负数',
    ];

    /**
     * 添加场景
     */
    public function sceneAdd()
    {
        return $this->remove('id', true);
    }

    /**
     * 详情场景
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    /**
     * 编辑场景
     */
    public function sceneEdit()
    {
        return $this;
    }

    /**
     * 删除场景
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }

    /**
     * 状态场景
     */
    public function sceneStatus()
    {
        return $this->only(['id', 'status']);
    }

    /**
     * 校验盘口是否存在
     */
    public function checkPlate($value)
    {
        $plate = Plate::findOrEmpty($value);
        if ($plate->isEmpty()) {
            return '盘口不存在';
        }
        return true;
    }
}
