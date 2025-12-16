<?php
declare(strict_types=1);

namespace app\adminapi\lists\plate;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\user\User;

/**
 * 用户列表
 * Class UserLists
 * @package app\adminapi\lists\plate
 */
class UserLists extends BaseAdminDataLists implements ListsSearchInterface
{
    /**
     * 搜索条件
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['username', 'nickname', 'mobile'],
            '=' => ['status'],  // user_type 不在这里配置,在 lists() 和 count() 中单独处理
        ];
    }

    /**
     * 查询列表
     */
    public function lists(): array
    {
        // 从请求参数中直接获取用户类型,不经过 searchWhere
        $userType = request()->param('user_type', 'user');

        // 获取当前登录管理员信息
        $adminInfo = request()->adminInfo ?? [];
        $adminRoot = $adminInfo['root'] ?? 1;  // 1=总管理, 2=代理
        $adminId = $adminInfo['admin_id'] ?? 0;

        // 如果筛选代理用户
        if ($userType === 'agent') {
            $lists = \think\facade\Db::table('la_admin')
                ->alias('adm')
                ->field([
                    'adm.id',
                    'adm.account as username',
                    'adm.name as nickname',
                    '"" as mobile',  // la_admin 表没有 mobile 字段,返回空字符串
                    'IF(adm.disable=0, 1, 0) as status',
                    'adm.create_time',
                    'adm.update_time',
                    'IFNULL(adm.credit_limit, 0) as user_money',
                    '0 as frozen_amount',
                    '"agent" as user_type',
                    'adm.root',
                ])
                ->where('adm.root', '=', 2)  // 代理账户
                ->where(function($query) {
                    // 处理其他搜索条件
                    if (!empty($this->searchWhere['username'])) {
                        $query->where('adm.account', 'like', '%' . $this->searchWhere['username'] . '%');
                    }
                    if (!empty($this->searchWhere['nickname'])) {
                        $query->where('adm.name', 'like', '%' . $this->searchWhere['nickname'] . '%');
                    }
                    // mobile 字段不存在,不进行搜索
                    if (isset($this->searchWhere['status']) && $this->searchWhere['status'] !== '') {
                        $query->where('adm.disable', '=', $this->searchWhere['status'] == 1 ? 0 : 1);
                    }
                })
                ->order(['adm.id' => 'desc'])
                ->limit($this->limitOffset, $this->limitLength)
                ->select()
                ->toArray();
        } else {
            // 普通用户查询
            $query = User::alias('u')
                ->field([
                    'u.id',
                    'u.username',
                    'u.nickname',
                    'u.mobile',
                    'u.status',
                    'u.fid',  // 添加 fid 字段
                    'u.create_time',
                    'u.update_time',
                    'IFNULL(a.balance, 0) as user_money',
                    'IFNULL(a.frozen_amount, 0) as frozen_amount',
                    '"user" as user_type',
                ])
                ->leftJoin('user_account a', 'u.id = a.user_id')
                ->where($this->searchWhere);

            // 如果是代理登录,只显示自己创建的用户(fid = 自己的ID)
            if ($adminRoot == 2) {
                $query->where('u.fid', $adminId);
            }

            $lists = $query
                ->order(['u.id' => 'desc'])
                ->limit($this->limitOffset, $this->limitLength)
                ->select()
                ->toArray();
        }

        return $lists;
    }

    /**
     * 查询数量
     */
    public function count(): int
    {
        // 从请求参数中直接获取用户类型,不经过 searchWhere
        $userType = request()->param('user_type', 'user');

        // 获取当前登录管理员信息
        $adminInfo = request()->adminInfo ?? [];
        $adminRoot = $adminInfo['root'] ?? 1;
        $adminId = $adminInfo['admin_id'] ?? 0;

        if ($userType === 'agent') {
            $query = \think\facade\Db::table('la_admin')
                ->where('root', '=', 2);

            // 处理搜索条件
            if (!empty($this->searchWhere['username'])) {
                $query->where('account', 'like', '%' . $this->searchWhere['username'] . '%');
            }
            if (!empty($this->searchWhere['nickname'])) {
                $query->where('name', 'like', '%' . $this->searchWhere['nickname'] . '%');
            }
            // mobile 字段不存在,不进行搜索
            if (isset($this->searchWhere['status']) && $this->searchWhere['status'] !== '') {
                $query->where('disable', '=', $this->searchWhere['status'] == 1 ? 0 : 1);
            }

            return $query->count();
        }

        // 普通用户计数
        $query = User::where($this->searchWhere);

        // 如果是代理登录,只统计自己创建的用户
        if ($adminRoot == 2) {
            $query->where('fid', $adminId);
        }

        return $query->count();
    }
}
