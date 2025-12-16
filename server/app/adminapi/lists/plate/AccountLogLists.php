<?php
declare(strict_types=1);

namespace app\adminapi\lists\plate;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\lottery\AccountLog;

/**
 * 账户流水列表
 * Class AccountLogLists
 * @package app\adminapi\lists\plate
 */
class AccountLogLists extends BaseAdminDataLists implements ListsSearchInterface
{
    /**
     * 搜索条件
     */
    public function setSearch(): array
    {
        return [
            '=' => ['user_id', 'admin_id', 'change_type'],  // 添加 admin_id 支持代理查询
        ];
    }

    /**
     * 查询列表
     */
    public function lists(): array
    {
        $field = [
            'id',
            'sn',
            'user_id',
            'admin_id',  // 添加 admin_id 字段
            'change_type',
            'change_amount',
            'balance_before',
            'balance_after',
            'frozen_before',
            'frozen_after',
            'related_sn',
            'related_type',
            'remark',
            'operator_id',
            'ip',
            'created_at',
        ];

        $lists = AccountLog::field($field)
            ->where($this->searchWhere)
            ->order(['id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        // 添加变动类型文本
        $changeTypeMap = [
            1 => '充值',
            2 => '提现',
            3 => '投注',
            4 => '中奖',
            5 => '退款',
            6 => '佣金',
            7 => '调整',
            8 => '冻结',
            9 => '解冻',
        ];

        foreach ($lists as &$item) {
            $item['change_type_text'] = $changeTypeMap[$item['change_type']] ?? '未知';
            $item['created_time'] = date('Y-m-d H:i:s', $item['created_at']);
        }

        return $lists;
    }

    /**
     * 查询数量
     */
    public function count(): int
    {
        return AccountLog::where($this->searchWhere)->count();
    }
}
