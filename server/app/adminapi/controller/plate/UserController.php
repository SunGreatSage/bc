<?php
declare(strict_types=1);

namespace app\adminapi\controller\plate;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\plate\UserLists;
use app\adminapi\lists\plate\AccountLogLists;
use app\adminapi\logic\plate\UserLogic;
use app\adminapi\validate\plate\UserValidate;

/**
 * 用户管理控制器
 * Class UserController
 * @package app\adminapi\controller\plate
 */
class UserController extends BaseAdminController
{
    /**
     * 用户列表
     */
    public function lists()
    {
        return $this->dataLists(new UserLists());
    }

    /**
     * 用户详情
     */
    public function detail()
    {
        $params = (new UserValidate())->get()->goCheck('detail');
        $result = UserLogic::detail($params);
        return $this->success('获取成功', $result);
    }

    /**
     * 新增用户
     */
    public function add()
    {
        $params = (new UserValidate())->post()->goCheck('add');
        UserLogic::add($params);
        return $this->success('新增成功');
    }

    /**
     * 编辑用户
     */
    public function edit()
    {
        $params = (new UserValidate())->post()->goCheck('edit');
        $result = UserLogic::edit($params);
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('编辑成功');
    }

    /**
     * 删除用户
     */
    public function delete()
    {
        $params = (new UserValidate())->post()->goCheck('delete');
        UserLogic::delete($params);
        return $this->success('删除成功');
    }

    /**
     * 修改用户状态
     */
    public function status()
    {
        $params = (new UserValidate())->post()->goCheck('status');
        UserLogic::status($params);
        return $this->success('修改成功');
    }

    /**
     * 调整用户余额
     */
    public function adjustBalance()
    {
        $params = (new UserValidate())->post()->goCheck('adjustBalance');
        $result = UserLogic::adjustBalance($params);
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('调整成功');
    }

    /**
     * 用户账户流水列表
     */
    public function accountLogs()
    {
        return $this->dataLists(new AccountLogLists());
    }

    /**
     * 开设代理账户
     */
    public function createAgent()
    {
        $params = (new UserValidate())->post()->goCheck('createAgent');
        $params['operator_id'] = $this->adminId;  // 当前管理员ID

        $result = UserLogic::createAgent($params);
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('开设成功');
    }

    /**
     * 调整代理信用额度
     */
    public function adjustAgentCredit()
    {
        $params = (new UserValidate())->post()->goCheck('adjustAgentCredit');
        $params['operator_id'] = $this->adminId;

        $result = UserLogic::adjustAgentCredit($params);
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('调整成功');
    }
}
