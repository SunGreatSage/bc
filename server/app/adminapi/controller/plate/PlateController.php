<?php
declare(strict_types=1);

namespace app\adminapi\controller\plate;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\plate\PlateLogic;
use app\adminapi\validate\plate\PlateValidate;

/**
 * 盘口管理控制器
 * Class PlateController
 * @package app\adminapi\controller\plate
 */
class PlateController extends BaseAdminController
{
    /**
     * 盘口列表
     * @return \think\response\Json
     */
    public function lists()
    {
        $params = $this->request->get();
        $result = PlateLogic::lists($params);
        return $this->success('', $result);
    }

    /**
     * 盘口详情
     * @return \think\response\Json
     */
    public function detail()
    {
        $params = (new PlateValidate())->goCheck('detail');
        $result = PlateLogic::detail($params);
        return $this->data($result);
    }

    /**
     * 添加盘口
     * @return \think\response\Json
     */
    public function add()
    {
        $params = (new PlateValidate())->post()->goCheck('add');
        PlateLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * 编辑盘口
     * @return \think\response\Json
     */
    public function edit()
    {
        $params = (new PlateValidate())->post()->goCheck('edit');
        $result = PlateLogic::edit($params);
        if (false !== $result) {
            return $this->success('编辑成功', $result, 1, 1);
        }
        return $this->fail(PlateLogic::getError());
    }

    /**
     * 删除盘口
     * @return \think\response\Json
     */
    public function delete()
    {
        $params = (new PlateValidate())->post()->goCheck('delete');
        PlateLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * 修改盘口状态
     * @return \think\response\Json
     */
    public function status()
    {
        $params = (new PlateValidate())->post()->goCheck('status');
        PlateLogic::status($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 获取所有启用的盘口
     * @return \think\response\Json
     */
    public function all()
    {
        $result = PlateLogic::getAllData();
        return $this->data($result);
    }
}
