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
    public array $notNeedLogin = ['getKjResult'];


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
            ->field('thisqishu,name')
            ->find();

        if (!$game) {
            return $this->fail('游戏不存在');
        }

        return $this->success('获取成功', [
            'qishu' => $game['thisqishu'],
            'game_name' => $game['name'],
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
     * @param int ftype_class 玩法分类(可选, 0=特码, 1=平码, 5=特肖)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "success",
     *   "data": {
     *     "list": [
     *       {
     *         "pid": 21401,
     *         "name": "特码",
     *         "peilv1": "42.0000",
     *         "ifok": 1
     *       }
     *     ]
     *   }
     * }
     */
    public function getPlayList()
    {
        // 获取请求参数
        $gid = $this->request->param('gid/d', 0);
        $ftypeClass = $this->request->param('ftype_class', '');

        // 参数验证
        if (empty($gid)) {
            return $this->fail('请输入游戏ID');
        }

        // 构建查询
        $query = \think\facade\Db::table('x_play')
            ->where('gid', $gid)
            ->where('ifok', 1);  // 只查询开放的玩法

        if ($ftypeClass !== '') {
            // 通过ftype字段关联查询(需要先查x_game表获取ftype配置)
            // 这里简化处理,后续可以优化
        }

        $list = $query->field('pid,name,peilv1,peilv2,ifok')
            ->order('pid', 'asc')
            ->select()
            ->toArray();

        return $this->success('获取成功', [
            'list' => $list,
        ]);
    }
}
