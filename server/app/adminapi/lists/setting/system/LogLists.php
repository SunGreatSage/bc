<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\adminapi\lists\setting\system;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\OperationLog;

/**
 * 日志列表
 * Class LogLists
 * @package app\adminapi\lists\setting\system
 */
class LogLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExcelInterface
{
    private const ACTION_NEGATIVE_PLAN = '选择负盈利开奖计划';
    private const ACTION_WIPEOUT_PLAN = '选择通杀/近似通杀开奖计划';

    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author ljj
     * @date 2021/8/3 4:21 下午
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['admin_name','action','url','ip','type'],
            'between_time' => 'create_time',
        ];
    }

    /**
     * @notes 查看系统日志列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/8/3 4:21 下午
     */
    public function lists(): array
    {
        $lists = $this->buildQuery()
            ->field('id,action,admin_name,admin_id,account,url,type,params,issue,plate_code,ip,create_time')
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['create_time' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item = $this->formatLogItem($item);
        }
        unset($item);

        return $lists;
    }

    /**
     * @notes 查看系统日志总数
     * @return int
     * @author ljj
     * @date 2021/8/3 4:23 下午
     */
    public function count(): int
    {
        return $this->buildQuery()->count();
    }

    /**
     * @notes 设置导出字段
     * @return string[]
     * @author ljj
     * @date 2021/8/3 4:48 下午
     */
    public function setExcelFields(): array
    {
        return [
            // '数据库字段名(支持别名) => 'Excel表字段名'
            'id' => '记录ID',
            'risk_tag' => '风控标记',
            'risk_summary' => '风控摘要',
            'risk_detail_text' => '风控详情',
            'action' => '操作',
            'admin_name' => '管理员',
            'admin_id' => '管理员ID',
            'account' => '管理员账号',
            'url' => '访问链接',
            'type' => '访问方式',
            'params' => '访问参数',
            'ip' => '来源IP',
            'create_time' => '日志时间',
        ];
    }

    /**
     * @notes 设置默认表名
     * @return string
     * @author ljj
     * @date 2021/8/3 4:48 下午
     */
    public function setFileName(): string
    {
        return '系统日志';
    }

    public static function riskOptions(): array
    {
        return [
            ['label' => '全部风控日志', 'value' => 'all'],
            ['label' => '负盈利方案', 'value' => 'negative'],
            ['label' => '通杀/近似通杀方案', 'value' => 'wipeout'],
        ];
    }

    private function buildQuery()
    {
        $query = OperationLog::where($this->searchWhere);

        $riskType = strtolower(trim((string)($this->params['risk_type'] ?? '')));
        if ($riskType !== '') {
            $riskActions = $this->getRiskActionsByType($riskType);
            if (!empty($riskActions)) {
                $query->where('action', 'in', $riskActions);
            }
        }

        $issue = trim((string)($this->params['issue'] ?? ''));
        if ($issue !== '') {
            $query->where('issue', $issue);
        }

        $plateCode = trim((string)($this->params['plate_code'] ?? ''));
        if ($plateCode !== '') {
            $query->where('plate_code', $plateCode);
        }

        return $query;
    }

    private function getRiskActionsByType(string $riskType): array
    {
        switch ($riskType) {
            case 'all':
            case 'risk':
            case 'risk_control':
                return [self::ACTION_NEGATIVE_PLAN, self::ACTION_WIPEOUT_PLAN];
            case 'negative':
            case 'loss':
                return [self::ACTION_NEGATIVE_PLAN];
            case 'wipeout':
            case 'full':
            case 'near':
                return [self::ACTION_WIPEOUT_PLAN];
            default:
                return [];
        }
    }

    private function formatLogItem(array $item): array
    {
        $params = $this->decodeJsonObject((string)($item['params'] ?? ''));
        $riskInfo = $this->buildRiskInfo(
            trim((string)($item['action'] ?? '')),
            $params,
            (string)($item['issue'] ?? ''),
            (string)($item['plate_code'] ?? '')
        );

        $item['risk_type'] = $riskInfo['risk_type'];
        $item['risk_tag'] = $riskInfo['risk_tag'];
        $item['risk_level'] = $riskInfo['risk_level'];
        $item['risk_summary'] = $riskInfo['risk_summary'];
        $item['risk_detail'] = $riskInfo['risk_detail'];
        $item['risk_detail_text'] = $riskInfo['risk_detail_text'];
        $item['parsed_params'] = $params;

        return $item;
    }

    private function buildRiskInfo(
        string $action,
        array $params,
        string $fallbackIssue = '',
        string $fallbackPlateCode = ''
    ): array {
        $info = [
            'risk_type' => '',
            'risk_tag' => '',
            'risk_level' => 'normal',
            'risk_summary' => '',
            'risk_detail' => [],
            'risk_detail_text' => '',
        ];

        if ($action === self::ACTION_NEGATIVE_PLAN) {
            $info['risk_type'] = 'negative';
            $info['risk_tag'] = '负盈利';
            $info['risk_level'] = 'danger';
            $info['risk_summary'] = sprintf(
                '预计亏损 %s，亏损率 %s%%',
                $this->formatMoney(abs((float)($params['expected_loss'] ?? $params['expected_profit'] ?? 0))),
                $this->formatNumber(abs((float)($params['expected_profit_rate'] ?? 0)))
            );
        } elseif ($action === self::ACTION_WIPEOUT_PLAN) {
            $wipeoutType = (string)($params['wipeout_type'] ?? '');
            $label = $wipeoutType === 'full' ? '通杀' : '近似通杀';
            $info['risk_type'] = 'wipeout';
            $info['risk_tag'] = $label;
            $info['risk_level'] = 'danger';
            $info['risk_summary'] = sprintf(
                '%s，实际利润率 %s%%，预计赔付 %s',
                $label,
                $this->formatNumber((float)($params['expected_profit_rate'] ?? 0)),
                $this->formatMoney((float)($params['expected_payout'] ?? 0))
            );
        }

        if ($info['risk_type'] === '') {
            return $info;
        }

        $detail = [
            'issue' => (string)($params['issue'] ?? $params['qishu'] ?? $fallbackIssue),
            'plate_code' => (string)($params['plate_code'] ?? $fallbackPlateCode),
            'numbers' => $this->formatNumbers($params['numbers'] ?? []),
            'plan_status' => (string)($params['plan_status'] ?? ''),
            'selection_source' => (string)($params['selection_source'] ?? ''),
            'expected_profit' => $this->formatMoney((float)($params['expected_profit'] ?? 0)),
            'expected_profit_rate' => $this->formatNumber((float)($params['expected_profit_rate'] ?? 0)) . '%',
            'expected_payout' => $this->formatMoney((float)($params['expected_payout'] ?? 0)),
            'total_bet_amount' => $this->formatMoney((float)($params['total_bet_amount'] ?? 0)),
            'total_orders' => (int)($params['total_orders'] ?? 0),
        ];

        if ($info['risk_type'] === 'negative') {
            $detail['negative_confirmed'] = !empty($params['negative_confirmed']) ? '是' : '否';
            $detail['expected_loss'] = $this->formatMoney((float)($params['expected_loss'] ?? 0));
        }

        if ($info['risk_type'] === 'wipeout') {
            $detail['wipeout_type'] = (string)($params['wipeout_type'] ?? '');
            $detail['wipeout_label'] = (string)($params['wipeout_label'] ?? $info['risk_tag']);
            $detail['wipeout_confirmed'] = !empty($params['wipeout_confirmed']) ? '是' : '否';
        }

        $info['risk_detail'] = $detail;
        $info['risk_detail_text'] = $this->formatRiskDetailText($detail);

        return $info;
    }

    private function decodeJsonObject(string $value): array
    {
        if ($value === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function formatRiskDetailText(array $detail): string
    {
        $labels = [
            'issue' => '期号',
            'plate_code' => '盘口',
            'numbers' => '开奖号码',
            'plan_status' => '计划状态',
            'selection_source' => '选择来源',
            'negative_confirmed' => '负盈利确认',
            'wipeout_type' => '通杀类型',
            'wipeout_label' => '通杀标记',
            'wipeout_confirmed' => '通杀确认',
            'expected_loss' => '预计亏损',
            'expected_profit' => '预计利润',
            'expected_profit_rate' => '实际利润率',
            'expected_payout' => '预计赔付',
            'total_bet_amount' => '本期总投注',
            'total_orders' => '本期订单数',
        ];

        $lines = [];
        foreach ($detail as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $lines[] = ($labels[$key] ?? $key) . '：' . $value;
        }

        return implode("\n", $lines);
    }

    private function formatNumbers($numbers): string
    {
        if (!is_array($numbers)) {
            return (string)$numbers;
        }

        return implode(',', array_map(static function ($number) {
            return (string)(int)$number;
        }, $numbers));
    }

    private function formatMoney(float $value): string
    {
        return '¥' . number_format($value, 2, '.', '');
    }

    private function formatNumber(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
