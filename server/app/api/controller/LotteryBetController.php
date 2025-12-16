<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 彩票投注控制器(基于 x_user 和 x_lib 表)
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-27
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\LotteryBetLogic;
use app\api\logic\LotteryLoginLogic;
use think\response\Json;

/**
 * 彩票投注控制器
 * Class LotteryBetController
 * @package app\api\controller
 */
class LotteryBetController extends BaseApiController
{
    /**
     * 不需要登录的接口
     */
    public array $notNeedLogin = ['getKjResult', 'getBetNumbers', 'getCurrentQishu', 'getPlayList'];


    /**
     * @notes 投注下单接口(支持批量，原子性操作：全部成功或全部失败)
     * @return Json
     * @author Claude
     * @date 2025/11/30
     *
     * 请求参数(JSON格式):
     * {
     *   "gid": 200,
     *   "qishu": "2025334",
     *   "orders": [
     *     {"pid": 1, "bet_content": "26", "bet_amount": 1},
     *     {"pid": 1, "bet_content": "08", "bet_amount": 2},
     *     {"pid": 5, "bet_content": "鼠,牛,虎,兔", "bet_amount": 5}
     *   ]
     * }
     *
     * 参数说明:
     * - gid: 游戏ID (必填, 200=新澳门六合彩)
     * - qishu: 期号 (必填, 如 "2025334")
     * - orders: 投注订单数组 (必填, 支持批量)
     *   - pid: 玩法ID (必填, la_play_method.id)
     *   - bet_content: 投注内容 (必填, 号码或生肖，多个用逗号分隔)
     *   - bet_amount: 投注金额 (必填, 必须>0)
     *
     * ⚠️ 业务规则说明:
     * - 用户只能投注'win'类型(即押号码中奖)
     * - 后端强制设置bet_type='win',忽略前端传值
     * - '不中'是平台盈利逻辑,不是用户投注选项
     *
     * 响应示例(成功):
     * {
     *   "code": 1,
     *   "msg": "投注成功",
     *   "data": {
     *     "success_count": 3,
     *     "total_amount": "8.00",
     *     "balance": "92.00",
     *     "results": [
     *       {"tid": 20000001, "bet_content": "26", "bet_amount": "1.00"},
     *       {"tid": 20000002, "bet_content": "08", "bet_amount": "2.00"},
     *       {"tid": 20000003, "bet_content": "鼠,牛,虎,兔", "bet_amount": "5.00"}
     *     ]
     *   }
     * }
     *
     * 响应示例(失败 - 任何一个订单失败则全部失败):
     * {
     *   "code": 0,
     *   "msg": "第2注投注失败: 余额不足"
     * }
     */
    public function placeBet()
    {
        // 获取新系统的用户ID(来自token验证)
        $userId = $this->userId;

        // 获取请求参数
        $gid = $this->request->param('gid/d', 0);
        $qishu = $this->request->param('qishu', '');
        $orders = $this->request->param('orders/a', []);  // 订单数组

        // 参数验证
        if (empty($gid)) {
            return $this->fail('请选择游戏');
        }

        if (empty($qishu)) {
            return $this->fail('请输入期号');
        }

        if (empty($orders) || !is_array($orders)) {
            return $this->fail('请输入投注订单');
        }

        if (count($orders) > 100) {
            return $this->fail('单次最多投注100注');
        }

        // 预处理订单数据
        $ip = $this->request->ip();
        $parsedOrders = [];

        foreach ($orders as $index => $order) {
            // 解析 pid 参数（玩法ID）
            $pidParam = $order['pid'] ?? '';
            $betContent = $order['bet_content'] ?? '';
            $betAmount = (float)($order['bet_amount'] ?? 0);
            $betType = $order['bet_type'] ?? 'win';  // 新增: 投注类型，默认为"中"

            // 解析 pid 格式: 直接使用数字ID（对应 la_play_method.id）
            $pid = 0;
            if (!empty($pidParam)) {
                $pid = (int)$pidParam;
            }

            // 基础参数验证（在事务外进行，快速失败）
            if (empty($pid)) {
                return $this->fail('第' . ($index + 1) . '注投注失败: 请选择玩法');
            }

            if (empty($betContent)) {
                return $this->fail('第' . ($index + 1) . '注投注失败: 请输入投注内容');
            }

            if ($betAmount <= 0) {
                return $this->fail('第' . ($index + 1) . '注投注失败: 投注金额必须大于0');
            }

            // ⚠️ 业务规则: 用户只能投注'win'类型(押号码中奖)
            // 'not_win'是平台盈利逻辑(用户未押的号码开出时平台赚钱)
            // 前端已移除'中/不中'选项,后端强制设为'win'
            $betType = 'win';  // 强制为'win',忽略前端传值

            $parsedOrders[] = [
                'index' => $index,
                'user_id' => $userId,
                'gid' => $gid,
                'qishu' => $qishu,
                'pid' => $pid,
                'bet_content' => $betContent,
                'bet_amount' => $betAmount,
                'bet_type' => $betType,  // 固定为'win'
                'ip' => $ip,
            ];
        }

        // 调用批量投注逻辑（原子性操作）
        $result = LotteryBetLogic::placeBetBatch($parsedOrders);

        if ($result === false) {
            return $this->fail(LotteryBetLogic::getError());
        }

        return $this->success('投注成功', $result);
    }


    /**
     * @notes 查询投注记录
     * @return Json
     * @author Claude
     * @date 2025/11/29
     *
     * 请求参数:
     * @param int page 页码(可选, 默认1)
     * @param int limit 每页数量(可选, 默认20)
     * @param string qishu 期号(可选)
     * @param int gid 游戏ID(可选)
     * @param int z 中奖状态(可选: 9=未开奖, 1=中奖, 0=未中)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "获取成功",
     *   "data": {
     *     "list": [
     *       {
     *         "tid": 20000001,
     *         "qishu": "2025334",
     *         "gid": 200,
     *         "bid": 24926,
     *         "pid": 97000108,
     *         "content": "08",
     *         "je": "100.00",
     *         "peilv1": "42.0000",
     *         "z": 9,
     *         "prize": "0.00",
     *         "time": "2025-11-29 14:15:30",
     *         "game_name": "新澳門六合彩",
     *         "bclass_name": "特碼",
     *         "play_name": "08",
     *         "play_display": "特碼",
     *         "status_text": "未开奖",
     *         "expected_prize": "4200.00"
     *       },
     *       {
     *         "tid": 20000002,
     *         "qishu": "2025334",
     *         "gid": 200,
     *         "bid": 24950,
     *         "pid": 21365,
     *         "content": "鼠-牛-虎-兔-龙-蛇",
     *         "je": "50.00",
     *         "peilv1": "1.9700",
     *         "z": 9,
     *         "prize": "0.00",
     *         "time": "2025-11-29 14:10:00",
     *         "game_name": "新澳門六合彩",
     *         "bclass_name": "連肖",
     *         "play_name": "六肖",
     *         "play_display": "連肖 - 六肖",
     *         "status_text": "未开奖",
     *         "expected_prize": "98.50"
     *       }
     *     ],
     *     "total": 100,
     *     "page": 1,
     *     "limit": 20
     *   }
     * }
     *
     * 字段说明:
     * - game_name: 游戏名称(如"新澳門六合彩")
     * - bclass_name: 玩法大类名称(如"特碼"、"連肖")
     * - play_name: 具体玩法名称(如"08"、"六肖")
     * - play_display: 组合显示名称,用于前端展示
     * - status_text: 中奖状态文本(未开奖/已中奖/未中奖)
     * - expected_prize: 预期中奖金额(je * peilv1)
     */
    public function getBetList()
    {
        // 获取新系统的用户ID
        $userId = $this->userId;

        // 获取查询参数
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 20);
        $qishu = $this->request->param('qishu', '');
        $gid = $this->request->param('gid/d', 0);
        $z = $this->request->param('z', '');
        $plateCode = $this->request->param('plate_code', '');

        // 调用查询逻辑
        $result = LotteryBetLogic::getBetList($userId, [
            'page' => $page,
            'limit' => $limit,
            'qishu' => $qishu,
            'gid' => $gid,
            'z' => $z,
            'plate_code' => $plateCode,
        ]);

        return $this->success('获取成功', $result);
    }


    /**
     * @notes 查询开奖结果(公开接口,无需登录)
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 请求参数:
     * @param int gid 游戏ID(必填, 200=新澳门六合彩)
     * @param string qishu 期号(必填, 如2025112)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "success",
     *   "data": {
     *     "qishu": "2025112",
     *     "numbers": ["01", "12", "23", "34", "35", "46", "08"],
     *     "kj_time": "2025-11-27 21:30:00",
     *     "status": 1
     *   }
     * }
     */
    public function getKjResult()
    {
        // 获取请求参数
        $gid = $this->request->param('gid/d', 0);
        $qishu = $this->request->param('qishu', '');
        $plateCode = $this->request->param('plate_code', '');  // 新增：盘口参数(可选)

        // 参数验证
        if (empty($gid)) {
            return $this->fail('请输入游戏ID');
        }

        if (empty($qishu)) {
            return $this->fail('请输入期号');
        }

        // 查询开奖结果
        $result = LotteryBetLogic::getKjResult($gid, $qishu, $plateCode);

        if ($result === false) {
            return $this->fail(LotteryBetLogic::getError());
        }

        return $this->success('获取成功', $result);
    }


    /**
     * @notes 查询当前期号(公开接口,无需登录)
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 请求参数:
     * @param int gid 游戏ID(必填, 200=新澳门六合彩)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "success",
     *   "data": {
     *     "qishu": "2025112",
     *     "game_name": "新澳门六合彩"
     *   }
     * }
     */
    public function getCurrentQishu()
    {
        // 获取请求参数
        $gid = $this->request->param('gid/d', 0);
        $plateCode = $this->request->param('plate_code', '');  // 新增：获取盘口参数

        // 参数验证
        if (empty($gid)) {
            return $this->fail('请输入游戏ID');
        }

        // 构建查询
        $query = \think\facade\Db::table('la_lottery_issue')
            ->where('game_id', $gid)
            ->where('status', 1);  // 1=投注中

        // 新增：如果传了盘口参数，则按盘口筛选
        if (!empty($plateCode)) {
            $query->where('plate_code', $plateCode);
        }

        // 查询当前可投注的期号
        $currentIssue = $query->order('draw_time', 'asc')->find();

        if (!$currentIssue) {
            // 如果没有投注中的期号，查询该盘口最新的期号
            $query2 = \think\facade\Db::table('la_lottery_issue')
                ->where('game_id', $gid);

            // 如果有盘口参数，也要筛选
            if (!empty($plateCode)) {
                $query2->where('plate_code', $plateCode);
            }

            $currentIssue = $query2->order('draw_time', 'desc')->find();
        }

        if (!$currentIssue) {
            return $this->fail('暂无可用期号');
        }

        // 游戏名称映射
        $gameNames = [
            200 => '马来六合彩',
        ];

        return $this->success('获取成功', [
            'qishu' => $currentIssue['issue'],
            'game_name' => $gameNames[$gid] ?? '未知游戏',
            'plate_code' => $currentIssue['plate_code'],
            'status' => $currentIssue['status'],
            'open_time' => date('Y-m-d H:i:s', $currentIssue['open_time']),
            'close_time' => date('Y-m-d H:i:s', $currentIssue['close_time']),
            'draw_time' => date('Y-m-d H:i:s', $currentIssue['draw_time']),
        ]);
    }


    /**
     * @notes 查询玩法列表
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 请求参数:
     * @param int gid 游戏ID(必填, 200=新澳门六合彩)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "获取成功",
     *   "data": {
     *     "list": [
     *       {
     *         "id": "bclass_24926",
     *         "name": "特碼",
     *         "type": "bclass"
     *       },
     *       {
     *         "id": "play_21365",
     *         "name": "四肖",
     *         "type": "play",
     *         "peilv1": "11.0000"
     *       }
     *     ]
     *   }
     * }
     */
    public function getPlayList()
    {
        // 获取请求参数
        $gid = $this->request->param('gid/d', 0);

        // 参数验证
        if (empty($gid)) {
            return $this->fail('请输入游戏ID');
        }

        // 从 la_play_method 表查询玩法列表
        $playList = \think\facade\Db::table('la_play_method')
            ->where('game_id', $gid)
            ->where('is_enabled', 1)
            ->field('id,name,code,odds_default,sort')
            ->order('sort', 'asc')
            ->select()
            ->toArray();

        if (empty($playList)) {
            return $this->success('获取成功', [
                'list' => [],
            ]);
        }

        // 格式化返回数据
        $result = [];
        foreach ($playList as $item) {
            $result[] = [
                'id' => $item['id'],
                'pid' => $item['id'],  // 兼容前端
                'name' => $item['name'],
                'code' => $item['code'],
                'peilv1' => number_format($item['odds_default'], 4, '.', ''),  // 赔率（包本金）
                'label' => $item['name'],  // 兼容前端
            ];
        }

        return $this->success('获取成功', [
            'list' => $result,
        ]);
    }


    /**
     * @notes 获取可投注的号码/生肖数据(含赔率)
     * @return Json
     * @author Claude
     * @date 2025/11/28
     *
     * 请求参数:
     * @param string play_name 玩法名称(必填, 如"特碼","正碼","特肖","三肖","四肖","五肖","六肖")
     * @param int gid 游戏ID(可选, 默认200)
     * @param int year 年份(可选, 默认当前年份, 仅生肖玩法需要)
     *
     * 响应示例(特碼/正碼):
     * {
     *   "code": 1,
     *   "msg": "获取成功",
     *   "data": {
     *     "play_name": "特碼",
     *     "play_type": "number",
     *     "year": 2025,
     *     "total_options": 49,
     *     "options": [
     *       {
     *         "value": "01",
     *         "label": "01",
     *         "odds": "42.0000",
     *         "zodiac": "蛇"
     *       }
     *     ]
     *   }
     * }
     *
     * 响应示例(生肖玩法 - 特肖/三肖/四肖/五肖/六肖):
     * {
     *   "code": 1,
     *   "msg": "获取成功",
     *   "data": {
     *     "play_name": "六肖",
     *     "play_type": "zodiac",
     *     "year": 2025,
     *     "year_zodiac": "蛇",
     *     "total_options": 12,
     *     "options": [
     *       {
     *         "value": "鼠",
     *         "label": "鼠",
     *         "odds": "1.9700",
     *         "odds_win": "0.0000",
     *         "odds_not_win": "1.9680",
     *         "numbers": ["06", "18", "30", "42"],
     *         "count": 4,
     *         "is_current_year": false,
     *         "category": "wild",
     *         "category_label": "野兽"
     *       }
     *     ],
     *     "category_groups": [
     *       {
     *         "type": "domestic",
     *         "label": "家禽",
     *         "zodiacs": ["牛", "馬", "羊", "雞", "狗", "豬"],
     *         "numbers": ["01","03","04",...],
     *         "total_numbers": 25,
     *         "description": "牛、马、羊、鸡、狗、猪(共25个号码)"
     *       },
     *       {
     *         "type": "wild",
     *         "label": "野兽",
     *         "zodiacs": ["鼠", "虎", "兔", "龍", "蛇", "猴"],
     *         "numbers": ["02","05","06",...],
     *         "total_numbers": 24,
     *         "description": "鼠、虎、兔、龙、蛇、猴(共24个号码)"
     *       }
     *     ],
     *     "odds_types": [
     *       {"type": "normal", "label": "普通", "odds": "1.9700"},
     *       {"type": "win", "label": "中", "odds": "0.0000"},
     *       {"type": "not_win", "label": "不中", "odds": "1.9680"}
     *     ],
     *     "special_rules": {
     *       "rule_49": "开出49号视为和局,投注金额退还"
     *     }
     *   }
     * }
     */
    public function getBetNumbers()
    {
        // 获取请求参数
        $playName = $this->request->param('play_name', '');
        $gid = $this->request->param('gid/d', 200);
        $year = $this->request->param('year/d', 0);
        $plateCode = $this->request->param('plate_code', '');

        // 参数验证
        if (empty($playName)) {
            return $this->fail('请输入玩法名称');
        }

        // 年份默认值
        if (empty($year)) {
            $year = (int)date('Y');
        }

        // 调用逻辑层获取数据
        $result = LotteryBetLogic::getBetOptions($playName, $gid, $year, $plateCode);

        if ($result === false) {
            return $this->fail(LotteryBetLogic::getError());
        }

        return $this->success('获取成功', $result);
    }
}
