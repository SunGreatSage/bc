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
     * @notes 投注下单接口(支持批量)
     * @return Json
     * @author Claude
     * @date 2025/11/29
     *
     * 请求参数(JSON格式):
     * {
     *   "gid": 200,
     *   "qishu": "2025334",
     *   "orders": [
     *     {"pid": "bclass_24927", "bet_content": "26", "bet_amount": 1},
     *     {"pid": "bclass_24927", "bet_content": "08", "bet_amount": 2},
     *     {"pid": "play_97000135", "bet_content": "鼠,牛,虎,兔", "bet_amount": 5}
     *   ]
     * }
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "投注成功",
     *   "data": {
     *     "success_count": 3,
     *     "fail_count": 0,
     *     "total_amount": "8.00",
     *     "balance": "92.00",
     *     "results": [
     *       {"tid": 20000001, "status": "success", "bet_content": "26", "bet_amount": "1.00"},
     *       {"tid": 20000002, "status": "success", "bet_content": "08", "bet_amount": "2.00"},
     *       {"tid": 20000003, "status": "success", "bet_content": "鼠,牛,虎,兔", "bet_amount": "5.00"}
     *     ]
     *   }
     * }
     */
    public function placeBet()
    {
        // 获取新系统的用户ID(来自token验证)
        $newUserId = $this->userId;

        // 映射到老系统用户
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($newUserId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

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

        // 处理每个订单
        $results = [];
        $successCount = 0;
        $failCount = 0;
        $totalAmount = 0;
        $lastBalance = 0;
        $ip = $this->request->ip();

        foreach ($orders as $index => $order) {
            // 解析 pid 参数
            $pidParam = $order['pid'] ?? '';
            $betContent = $order['bet_content'] ?? '';
            $betAmount = (float)($order['bet_amount'] ?? 0);

            // 解析 pid 格式: "play_123", "bclass_456", "123"
            $pid = 0;
            $pidType = 'play';
            if (!empty($pidParam)) {
                if (strpos($pidParam, 'play_') === 0) {
                    $pid = (int)substr($pidParam, 5);
                    $pidType = 'play';
                } elseif (strpos($pidParam, 'bclass_') === 0) {
                    $pid = (int)substr($pidParam, 7);
                    $pidType = 'bclass';
                } else {
                    $pid = (int)$pidParam;
                    $pidType = 'play';
                }
            }

            // 单项验证
            if (empty($pid)) {
                $results[] = [
                    'index' => $index,
                    'status' => 'fail',
                    'error' => '请选择玩法',
                    'bet_content' => $betContent,
                ];
                $failCount++;
                continue;
            }

            if (empty($betContent)) {
                $results[] = [
                    'index' => $index,
                    'status' => 'fail',
                    'error' => '请输入投注内容',
                    'bet_content' => $betContent,
                ];
                $failCount++;
                continue;
            }

            if ($betAmount <= 0) {
                $results[] = [
                    'index' => $index,
                    'status' => 'fail',
                    'error' => '投注金额必须大于0',
                    'bet_content' => $betContent,
                ];
                $failCount++;
                continue;
            }

            // 调用投注逻辑
            $result = LotteryBetLogic::placeBet([
                'legacy_userid' => $legacyUser['userid'],
                'gid' => $gid,
                'qishu' => $qishu,
                'pid' => $pid,
                'pid_type' => $pidType,
                'bet_content' => $betContent,
                'bet_amount' => $betAmount,
                'ip' => $ip,
            ]);

            if ($result === false) {
                $results[] = [
                    'index' => $index,
                    'status' => 'fail',
                    'error' => LotteryBetLogic::getError(),
                    'bet_content' => $betContent,
                    'bet_amount' => number_format($betAmount, 2, '.', ''),
                ];
                $failCount++;
            } else {
                $results[] = [
                    'index' => $index,
                    'status' => 'success',
                    'tid' => $result['tid'],
                    'bet_content' => $betContent,
                    'bet_amount' => $result['bet_amount'],
                    'play_name' => $result['play_name'],
                    'peilv' => $result['peilv'],
                    'expected_prize' => $result['expected_prize'],
                ];
                $successCount++;
                $totalAmount += $betAmount;
                $lastBalance = $result['balance'];
            }
        }

        // 全部失败
        if ($successCount == 0) {
            return $this->fail('投注失败', [
                'success_count' => 0,
                'fail_count' => $failCount,
                'results' => $results,
            ]);
        }

        return $this->success('投注成功', [
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'total_amount' => number_format($totalAmount, 2, '.', ''),
            'balance' => $lastBalance,
            'qishu' => $qishu,
            'results' => $results,
        ]);
    }


    /**
     * @notes 查询投注记录
     * @return Json
     * @author Claude
     * @date 2025/11/27
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
     *   "msg": "success",
     *   "data": {
     *     "list": [
     *       {
     *         "tid": "20251127141530001",
     *         "qishu": "2025112",
     *         "gid": 200,
     *         "content": "08",
     *         "je": "100.00",
     *         "peilv1": "42.0000",
     *         "z": 9,
     *         "status_text": "未开奖",
     *         "prize": "0.00",
     *         "time": "2025-11-27 14:15:30"
     *       }
     *     ],
     *     "total": 100,
     *     "page": 1,
     *     "limit": 20
     *   }
     * }
     */
    public function getBetList()
    {
        // 获取新系统的用户ID
        $newUserId = $this->userId;

        // 映射到老系统用户
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($newUserId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

        // 获取查询参数
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 20);
        $qishu = $this->request->param('qishu', '');
        $gid = $this->request->param('gid/d', 0);
        $z = $this->request->param('z', '');

        // 调用查询逻辑
        $result = LotteryBetLogic::getBetList($legacyUser['userid'], [
            'page' => $page,
            'limit' => $limit,
            'qishu' => $qishu,
            'gid' => $gid,
            'z' => $z,
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

        // 参数验证
        if (empty($gid)) {
            return $this->fail('请输入游戏ID');
        }

        if (empty($qishu)) {
            return $this->fail('请输入期号');
        }

        // 查询开奖结果
        $result = LotteryBetLogic::getKjResult($gid, $qishu);

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

        // 参数验证
        if (empty($gid)) {
            return $this->fail('请输入游戏ID');
        }

        // 查询游戏信息
        $game = \think\facade\Db::table('x_game')
            ->where('gid', $gid)
            ->field('thisqishu,gname')
            ->find();

        if (!$game) {
            return $this->fail('游戏不存在');
        }

        return $this->success('获取成功', [
            'qishu' => $game['thisqishu'],
            'game_name' => $game['gname'],
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

        // 定义支持的玩法名称
        // 大类玩法(在 x_bclass 表中): 特碼、正碼(平码)、特肖
        $bclassPlays = ['特码', '特碼', '特肖', '平码', '平碼', '正码', '正碼'];

        // 具体玩法(在 x_play 表中): 三肖、四肖、五肖、六肖
        $playPlays = ['三肖', '四肖', '五肖', '六肖'];

        // 1. 从 x_bclass 表查询玩法大类
        $bclassList = \think\facade\Db::table('x_bclass')
            ->where('gid', $gid)
            ->where('ifok', 1)
            ->whereIn('name', $bclassPlays)
            ->field('id,name,xsort')
            ->select()
            ->toArray();

        // 2. 从 x_play 表查询具体玩法
        $playList = \think\facade\Db::table('x_play')
            ->where('gid', $gid)
            ->where('ifok', 1)
            ->whereIn('name', $playPlays)
            ->field('pid as id,name,peilv1')
            ->select()
            ->toArray();

        // 3. 合并结果并标记类型
        $result = [];

        // 添加大类玩法
        foreach ($bclassList as $item) {
            $result[] = [
                'id' => 'bclass_' . $item['id'],
                'name' => $item['name'],
                'type' => 'bclass',
                'sort' => $item['xsort']
            ];
        }

        // 添加具体玩法
        foreach ($playList as $item) {
            $result[] = [
                'id' => 'play_' . $item['id'],
                'name' => $item['name'],
                'type' => 'play',
                'peilv1' => $item['peilv1'],
                'sort' => 99  // 默认排在后面
            ];
        }

        // 4. 按排序号排序
        usort($result, function($a, $b) {
            return $a['sort'] - $b['sort'];
        });

        // 5. 移除排序字段
        foreach ($result as &$item) {
            unset($item['sort']);
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

        // 参数验证
        if (empty($playName)) {
            return $this->fail('请输入玩法名称');
        }

        // 年份默认值
        if (empty($year)) {
            $year = (int)date('Y');
        }

        // 调用逻辑层获取数据
        $result = LotteryBetLogic::getBetOptions($playName, $gid, $year);

        if ($result === false) {
            return $this->fail(LotteryBetLogic::getError());
        }

        return $this->success('获取成功', $result);
    }
}
