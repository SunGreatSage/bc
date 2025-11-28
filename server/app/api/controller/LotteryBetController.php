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
     * @notes 投注下单接口
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 请求参数:
     * @param int gid 游戏ID(必填, 200=新澳门六合彩)
     * @param string qishu 期号(必填, 如2025112)
     * @param int pid 玩法ID(必填, 如21401=特码)
     * @param string bet_content 投注内容(必填, 如"08")
     * @param float bet_amount 投注金额(必填, 必须为正整数)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "投注成功",
     *   "data": {
     *     "tid": "20251127141530001",
     *     "balance": "9900.00",
     *     "qishu": "2025112",
     *     "expected_prize": "4200.00"
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
        $pid = $this->request->param('pid/d', 0);
        $betContent = $this->request->param('bet_content', '');
        $betAmount = $this->request->param('bet_amount/f', 0);

        // 参数验证
        if (empty($gid)) {
            return $this->fail('请选择游戏');
        }

        if (empty($qishu)) {
            return $this->fail('请输入期号');
        }

        if (empty($pid)) {
            return $this->fail('请选择玩法');
        }

        if (empty($betContent)) {
            return $this->fail('请输入投注内容');
        }

        if ($betAmount <= 0) {
            return $this->fail('投注金额必须大于0');
        }

        // 调用投注逻辑
        $result = LotteryBetLogic::placeBet([
            'legacy_userid' => $legacyUser['userid'],
            'gid' => $gid,
            'qishu' => $qishu,
            'pid' => $pid,
            'bet_content' => $betContent,
            'bet_amount' => $betAmount,
            'ip' => $this->request->ip(),
        ]);

        if ($result === false) {
            return $this->fail(LotteryBetLogic::getError());
        }

        return $this->success('投注成功', $result);
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
     * @notes 获取可投注的号码序列(含赔率、生肖等信息)
     * @return Json
     * @author Claude
     * @date 2025/11/27
     *
     * 请求参数:
     * @param int year 年份(可选,默认当前年份)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "success",
     *   "data": {
     *     "year": 2025,
     *     "year_zodiac": "蛇",
     *     "numbers": [
     *       {
     *         "number": "01",
     *         "zodiac": "蛇",
     *         "is_current_year_zodiac": true,
     *         "odds": {
     *           "special_number": "47",
     *           "normal_number": "8"
     *         }
     *       },
     *       ...
     *     ],
     *     "zodiacs": {
     *       "鼠": {
     *         "numbers": ["06", "18", "30", "42"],
     *         "odds": {
     *           "special_zodiac": "12",
     *           "three_zodiac": "7",
     *           "four_zodiac": "5",
     *           "five_zodiac": "4",
     *           "six_zodiac": "3"
     *         }
     *       },
     *       ...
     *     }
     *   }
     * }
     */
    public function getBetNumbers()
    {
        // 获取年份参数
        $year = $this->request->param('year/d', 0);
        if (empty($year)) {
            $year = (int)date('Y');
        }

        // 调用逻辑层获取数据
        $result = LotteryBetLogic::getBetNumbers($year);

        if ($result === false) {
            return $this->fail(LotteryBetLogic::getError());
        }

        return $this->success('获取成功', $result);
    }
}
