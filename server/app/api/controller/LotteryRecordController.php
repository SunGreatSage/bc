<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 彩票记录控制器(历史下注、输赢流水)
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-27
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\LotteryLoginLogic;
use think\facade\Db;
use think\response\Json;

/**
 * 彩票记录控制器
 * Class LotteryRecordController
 * @package app\api\controller
 */
class LotteryRecordController extends BaseApiController
{
    /**
     * @notes 历史下注记录(已开奖或未开奖的投注记录)
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 功能说明:
     * 查询用户的投注记录,包含已开奖和未开奖的注单
     * 参考老系统: /uxj/lib.php case "show"
     *
     * 请求参数:
     * @param int page 页码(可选, 默认1)
     * @param int limit 每页数量(可选, 默认20)
     * @param int gid 游戏ID(可选, 筛选指定游戏)
     * @param string qishu 期号(可选, 筛选指定期号)
     * @param int z 中奖状态(可选: 9=未开奖, 7=已结算, 1=中奖, 0=未中)
     * @param string start_date 开始日期(可选, 格式:YYYY-MM-DD)
     * @param string end_date 结束日期(可选, 格式:YYYY-MM-DD)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "success",
     *   "data": {
     *     "list": [
     *       {
     *         "tid": "20251127141530001",
     *         "qishu": "2025112",
     *         "game_name": "新澳门六合彩",
     *         "play_name": "特码",
     *         "content": "08",
     *         "je": "100.00",
     *         "peilv1": "42.0000",
     *         "z": 1,
     *         "z_text": "已中奖",
     *         "prize": "4200.00",
     *         "win_lose": "4100.00",
     *         "time": "2025-11-27 14:15:30"
     *       }
     *     ],
     *     "summary": {
     *       "total_count": 100,
     *       "total_bet": "10000.00",
     *       "total_prize": "8500.00",
     *       "total_win_lose": "-1500.00"
     *     },
     *     "page": 1,
     *     "limit": 20,
     *     "total": 100
     *   }
     * }
     */
    public function getBetHistory()
    {
        // 获取新系统的用户ID
        $newUserId = $this->userId;

        // 映射到老系统用户
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($newUserId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

        $legacyUserId = $legacyUser['userid'];

        // 获取查询参数
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 20);
        $gid = $this->request->param('gid/d', 0);
        $qishu = $this->request->param('qishu', '');
        $z = $this->request->param('z', '');
        $startDate = $this->request->param('start_date', '');
        $endDate = $this->request->param('end_date', '');

        // 构建查询条件
        $where = [
            ['userid', '=', $legacyUserId],
            ['bs', '=', 1],  // bs=1 表示有效注单
        ];

        // 参考老系统lib.php:17-22, 查询z in(7,9)和z=9的记录
        // z=9未开奖, z=7已结算, z=1中奖, z=0未中
        if ($z !== '') {
            $where[] = ['z', '=', $z];
        } else {
            // 默认查询已结算或未开奖的记录
            $where[] = ['z', 'in', [0, 1, 7, 9]];
        }

        if ($gid > 0) {
            $where[] = ['gid', '=', $gid];
        }

        if (!empty($qishu)) {
            $where[] = ['qishu', '=', $qishu];
        }

        if (!empty($startDate) && !empty($endDate)) {
            $where[] = ['dates', 'between', [$startDate, $endDate]];
        }

        // 查询总数
        $total = Db::table('x_lib')
            ->where($where)
            ->count();

        // 查询列表
        $list = Db::table('x_lib')
            ->where($where)
            ->order('time', 'desc')
            ->order('id', 'desc')
            ->page($page, $limit)
            ->field('tid,qishu,gid,bid,sid,cid,pid,content,je,peilv1,peilv2,z,prize,time,dates')
            ->select()
            ->toArray();

        // 查询统计数据(参考lib.php:23-28)
        $summary = Db::table('x_lib')
            ->where($where)
            ->field([
                'COUNT(id) as total_count',
                'SUM(je) as total_bet',
                'SUM(prize) as total_prize',
                'SUM(prize - je) as total_win_lose',
            ])
            ->find();

        // 关联查询游戏和玩法名称(参考lib.php:54-71)
        foreach ($list as &$item) {
            // 查询游戏名称
            $game = Db::table('x_game')
                ->where('gid', $item['gid'])
                ->field('name as gname')
                ->find();
            $item['game_name'] = $game['gname'] ?? '';

            // 查询玩法名称
            $play = Db::table('x_play')
                ->where('pid', $item['pid'])
                ->field('name')
                ->find();
            $item['play_name'] = $play['name'] ?? '';

            // 格式化中奖状态
            $item['z_text'] = $this->getZStatusText($item['z']);

            // 计算输赢(中奖金额 - 投注金额)
            $item['win_lose'] = number_format($item['prize'] - $item['je'], 2, '.', '');

            // 格式化金额
            $item['je'] = number_format($item['je'], 2, '.', '');
            $item['prize'] = number_format($item['prize'], 2, '.', '');
            $item['peilv1'] = number_format($item['peilv1'], 4, '.', '');
        }

        return $this->success('获取成功', [
            'list' => $list,
            'summary' => [
                'total_count' => $summary['total_count'] ?? 0,
                'total_bet' => number_format($summary['total_bet'] ?? 0, 2, '.', ''),
                'total_prize' => number_format($summary['total_prize'] ?? 0, 2, '.', ''),
                'total_win_lose' => number_format($summary['total_win_lose'] ?? 0, 2, '.', ''),
            ],
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }


    /**
     * @notes 输赢流水记录(资金变动记录)
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 功能说明:
     * 查询用户的资金变动流水,包括投注、中奖、充值、提款等
     * 参考老系统: /uxj/member.php case "jyjl" (交易记录)
     *
     * 请求参数:
     * @param int page 页码(可选, 默认1)
     * @param int limit 每页数量(可选, 默认20)
     * @param int mtype 类型(可选: 0=充值, 1=提款)
     * @param int status 状态(可选: 0=待审核, 1=已通过, 2=已拒绝)
     * @param string start_date 开始日期(可选, 格式:YYYY-MM-DD)
     * @param string end_date 结束日期(可选, 格式:YYYY-MM-DD)
     * @param string keyword 关键词搜索(可选, 搜索备注)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "success",
     *   "data": {
     *     "list": [
     *       {
     *         "id": 1,
     *         "mtype": 0,
     *         "mtype_text": "充值",
     *         "money": "1000.00",
     *         "aftermoney": "11000.00",
     *         "remark": "银行卡充值",
     *         "status": 1,
     *         "status_text": "已通过",
     *         "time": "2025-11-27 14:15:30"
     *       }
     *     ],
     *     "summary": {
     *       "total_in": "5000.00",
     *       "total_out": "2000.00",
     *       "net_amount": "3000.00"
     *     },
     *     "page": 1,
     *     "limit": 20,
     *     "total": 50
     *   }
     * }
     */
    public function getMoneyLog()
    {
        // 获取新系统的用户ID
        $newUserId = $this->userId;

        // 映射到老系统用户
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($newUserId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

        $legacyUserId = $legacyUser['userid'];

        // 获取查询参数
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 20);
        $mtype = $this->request->param('mtype', '');
        $status = $this->request->param('status', '');
        $startDate = $this->request->param('start_date', '');
        $endDate = $this->request->param('end_date', '');
        $keyword = $this->request->param('keyword', '');

        // 构建查询条件
        $where = [
            ['userid', '=', $legacyUserId],
        ];

        // 查询x_user_money_log表(资金流水表)
        // 该表记录所有资金变动:投注、中奖、充值、提款等

        if ($mtype !== '') {
            // 通过remark字段判断类型
            // "投注"=投注扣款, "中奖"=中奖入账, "充值"=充值, "提款"=提款
            if ($mtype == 0) {
                $where[] = ['remark', 'like', '%充值%'];
            } elseif ($mtype == 1) {
                $where[] = ['remark', 'like', '%提款%'];
            }
        }

        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }

        if (!empty($startDate) && !empty($endDate)) {
            $where[] = ['time', 'between', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']];
        }

        if (!empty($keyword)) {
            $where[] = ['remark', 'like', '%' . $keyword . '%'];
        }

        // 查询总数
        $total = Db::table('x_user_money_log')
            ->where($where)
            ->count();

        // 查询列表
        $list = Db::table('x_user_money_log')
            ->where($where)
            ->order('time', 'desc')
            ->page($page, $limit)
            ->field('id,money,aftermoney,remark,status,ip,time')
            ->select()
            ->toArray();

        // 查询统计数据
        $summary = Db::table('x_user_money_log')
            ->where($where)
            ->field([
                'SUM(CASE WHEN money > 0 THEN money ELSE 0 END) as total_in',
                'SUM(CASE WHEN money < 0 THEN ABS(money) ELSE 0 END) as total_out',
                'SUM(money) as net_amount',
            ])
            ->find();

        // 格式化数据
        foreach ($list as &$item) {
            // 判断类型
            $item['mtype_text'] = $this->getMoneyTypeText($item['remark']);

            // 状态文本
            $item['status_text'] = $this->getStatusText($item['status']);

            // 格式化金额
            $item['money'] = number_format($item['money'], 2, '.', '');
            $item['aftermoney'] = number_format($item['aftermoney'], 2, '.', '');
        }

        return $this->success('获取成功', [
            'list' => $list,
            'summary' => [
                'total_in' => number_format($summary['total_in'] ?? 0, 2, '.', ''),
                'total_out' => number_format($summary['total_out'] ?? 0, 2, '.', ''),
                'net_amount' => number_format($summary['net_amount'] ?? 0, 2, '.', ''),
            ],
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }


    /**
     * @notes 交易记录(充值/提款记录)
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 功能说明:
     * 查询用户的充值和提款申请记录
     * 参考老系统: /uxj/member.php case "jyjl"
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "success",
     *   "data": {
     *     "list": [
     *       {
     *         "id": 1,
     *         "mtype": 0,
     *         "mtype_text": "充值",
     *         "money": "1000.00",
     *         "sxfei": "0.00",
     *         "fs": "银行卡",
     *         "bank": "工商银行",
     *         "status": 1,
     *         "status_text": "已通过",
     *         "tjtime": "2025-11-27 14:15:30",
     *         "bz": "备注"
     *       }
     *     ],
     *     "page": 1,
     *     "limit": 20,
     *     "total": 50
     *   }
     * }
     */
    public function getTransactionList()
    {
        // 获取新系统的用户ID
        $newUserId = $this->userId;

        // 映射到老系统用户
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($newUserId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

        $legacyUserId = $legacyUser['userid'];

        // 获取查询参数
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 20);
        $mtype = $this->request->param('mtype', '');
        $status = $this->request->param('status', '');
        $startDate = $this->request->param('start_date', '');
        $endDate = $this->request->param('end_date', '');
        $keyword = $this->request->param('keyword', '');

        // 构建查询条件(参考member.php:241-246)
        $where = [
            ['userid', '=', $legacyUserId],
        ];

        if ($mtype !== '' && ($mtype == 0 || $mtype == 1)) {
            $where[] = ['mtype', '=', $mtype];
        }

        if ($status !== '' && in_array($status, [0, 1, 2])) {
            $where[] = ['status', '=', $status];
        }

        if (!empty($keyword)) {
            $where[] = ['bz', 'like', '%' . $keyword . '%'];
        }

        if (!empty($startDate) && !empty($endDate)) {
            $where[] = ['tjtime', 'between', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']];
        }

        // 查询总数
        $total = Db::table('x_money')
            ->where($where)
            ->count();

        // 查询列表(参考member.php:255)
        $list = Db::table('x_money')
            ->where($where)
            ->order('tjtime', 'desc')
            ->page($page, $limit)
            ->field('id,mtype,money,sxfei,fs,bank,sname,snum,uname,unum,cuntime,pass,status,tjtime,bz,ms')
            ->select()
            ->toArray();

        // 格式化数据(参考member.php:259-275)
        foreach ($list as &$item) {
            // 类型文本
            $item['mtype_text'] = $item['mtype'] == 0 ? '充值' : '提款';

            // 状态文本
            $item['status_text'] = $this->getStatusText($item['status']);

            // 方式文本
            $item['fs_text'] = $this->getFsText($item['fs']);

            // 格式化金额
            $item['money'] = number_format($item['money'], 2, '.', '');
            $item['sxfei'] = number_format($item['sxfei'], 2, '.', '');

            // 隐藏敏感信息(参考member.php:268-271)
            if (!empty($item['sname'])) {
                $item['sname'] = mb_substr($item['sname'], 0, 1) . '**';
            }
            if (!empty($item['snum'])) {
                $item['snum'] = '****' . substr($item['snum'], -4);
            }
            if (!empty($item['uname'])) {
                $item['uname'] = mb_substr($item['uname'], 0, 1) . '**';
            }
            if (!empty($item['unum'])) {
                $item['unum'] = '****' . substr($item['unum'], -4);
            }
        }

        return $this->success('获取成功', [
            'list' => $list,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }


    /**
     * @notes 获取中奖状态文本
     * @param int $z
     * @return string
     */
    private function getZStatusText(int $z): string
    {
        $map = [
            0 => '未中奖',
            1 => '已中奖',
            2 => '和局',
            3 => '特殊中奖',
            7 => '已结算',
            9 => '未开奖',
        ];

        return $map[$z] ?? '未知';
    }


    /**
     * @notes 获取资金类型文本
     * @param string $remark
     * @return string
     */
    private function getMoneyTypeText(string $remark): string
    {
        if (strpos($remark, '投注') !== false) {
            return '投注';
        } elseif (strpos($remark, '中奖') !== false) {
            return '中奖';
        } elseif (strpos($remark, '充值') !== false) {
            return '充值';
        } elseif (strpos($remark, '提款') !== false || strpos($remark, '提取') !== false) {
            return '提款';
        } else {
            return '其他';
        }
    }


    /**
     * @notes 获取状态文本
     * @param int $status
     * @return string
     */
    private function getStatusText(int $status): string
    {
        $map = [
            0 => '待审核',
            1 => '已通过',
            2 => '已拒绝',
        ];

        return $map[$status] ?? '未知';
    }


    /**
     * @notes 获取支付方式文本
     * @param string $fs
     * @return string
     */
    private function getFsText(string $fs): string
    {
        $map = [
            'bankonline' => '网银',
            'bankatm' => '银行卡',
            'weixin' => '微信',
            'alipay' => '支付宝',
            'usdt' => 'USDT',
        ];

        return $map[$fs] ?? $fs;
    }
}
