<?php
declare(strict_types=1);

namespace app\adminapi\controller\plate;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\plate\UserPlateLogic;
use app\adminapi\validate\plate\UserPlateValidate;

/**
 * 用户盘口管理控制器
 * Class UserPlateController
 * @package app\adminapi\controller\plate
 */
class UserPlateController extends BaseAdminController
{
    /**
     * 用户盘口列表
     * @return \think\response\Json
     */
    public function lists()
    {
        $params = $this->request->get();
        $result = UserPlateLogic::lists($params);
        return $this->success('', $result);
    }

    /**
     * 用户盘口详情
     * @return \think\response\Json
     */
    public function detail()
    {
        $params = (new UserPlateValidate())->goCheck('detail');
        $result = UserPlateLogic::detail($params);
        return $this->data($result);
    }

    /**
     * 添加用户到盘口
     * @return \think\response\Json
     */
    public function add()
    {
        $params = (new UserPlateValidate())->post()->goCheck('add');
        UserPlateLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * 编辑用户盘口信息
     * @return \think\response\Json
     */
    public function edit()
    {
        $params = (new UserPlateValidate())->post()->goCheck('edit');
        $result = UserPlateLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(UserPlateLogic::getError());
    }

    /**
     * 删除用户盘口关系
     * @return \think\response\Json
     */
    public function delete()
    {
        $params = (new UserPlateValidate())->post()->goCheck('delete');
        UserPlateLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * 修改用户盘口状态
     * @return \think\response\Json
     */
    public function status()
    {
        $params = (new UserPlateValidate())->post()->goCheck('status');
        UserPlateLogic::status($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 批量分配用户到盘口
     * @return \think\response\Json
     */
    public function batchAssign()
    {
        $params = (new UserPlateValidate())->post()->goCheck('batchAssign');
        UserPlateLogic::batchAssign($params);
        return $this->success('分配成功', [], 1, 1);
    }

    /**
     * 获取盘口下的用户列表
     * @return \think\response\Json
     */
    public function getUsersByPlate()
    {
        $params = $this->request->get();
        $result = UserPlateLogic::getUsersByPlate($params);
        return $this->success('', $result);
    }

    /**
     * 获取用户的盘口列表
     * @return \think\response\Json
     */
    public function getPlatesByUser()
    {
        $params = $this->request->get();
        $result = UserPlateLogic::getPlatesByUser($params);
        return $this->success('', $result);
    }
}
