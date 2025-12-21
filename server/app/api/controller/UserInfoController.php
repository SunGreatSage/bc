<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 用户信息控制器
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-28
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\LotteryLoginLogic;
use app\common\service\LotteryIssueService;
use think\response\Json;
use think\facade\Db;

/**
 * 用户信息控制器
 * Class UserInfoController
 * @package app\api\controller
 */
class UserInfoController extends BaseApiController
{
    /**
     * 不需要登录的接口
     */
    public array $notNeedLogin = [];


    /**
     * @notes 获取用户账户信息
     * @return Json
     * @author Claude
     * @date 2025/11/28
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "获取成功",
     *   "data": {
     *     "balance": "10000.00",
     *     "frozen_amount": "0.00",
     *     "total_bet": "500.00",
     *     "total_prize": "0.00",
     *     "period_bet_amount": "100.00",
     *     "time_info": {
     *       "issue": "2025121201",
     *       "game_id": 200,
     *       "plate_code": "A",
     *       "open_time": "2025-12-12 06:00:00",
     *       "close_time": "2025-12-12 09:25:00",
     *       "draw_time": "2025-12-12 09:30:00",
     *       "seconds_to_open": -36000,
     *       "seconds_to_close": 8100,
     *       "seconds_to_draw": 8400,
     *       "status": "betting",
     *       "status_code": 1,
     *       "result": ""
     *     }
     *   }
     * }
     */
   public function getUserInfo()
    {
        try {
            // 获取用户ID
            $userId = $this->userId;

            // 获取游戏ID(默认200=新澳门六合彩)
            $gid = $this->request->param('gid/d', 200);

            // 获取盘口参数(默认A)
            $plateCode = $this->request->param('plate_code', 'A');

            // 1. 获取用户账户信息(使用新系统表 la_user_account)
            $account = Db::table('la_user_account')
                ->where('user_id', $userId)
                ->find();

            if (!$account) {
                return $this->fail('用户账户不存在');
            }

            // 2. 【只读模式】获取当前期号(不创建新期号,安全原则)
            // ⚠️ 前端API只能查询期号,不能创建期号
            // ⚠️ 只有开奖接口才能创建新期号
            trace("🎯 [UserInfo] 开始获取期号(只读): gid=$gid, plate_code=$plateCode", 'info');
            $currentIssue = LotteryIssueService::getCurrentIssueReadOnly($gid, $plateCode);

            // 初始化期号相关变量
            $timeInfo = null;
            $periodBetAmount = 0;

            if (!$currentIssue) {
                // 没有可用期号时,返回空的时间信息,但仍然返回余额数据
                trace("⚠️ [UserInfo] 暂无可用期号,返回余额数据(时间信息为空)", 'warning');
                $timeInfo = [
                    'issue' => '',
                    'game_id' => $gid,
                    'plate_code' => $plateCode,
                    'open_time' => '',
                    'close_time' => '',
                    'draw_time' => '',
                    'seconds_to_open' => 0,
                    'seconds_to_close' => 0,
                    'seconds_to_draw' => 0,
                    'status' => 'waiting',
                    'status_code' => 0,
                    'result' => '',
                    'message' => '暂无可投注期号,请等待开奖后自动创建'
                ];
            } else {
                // 有可用期号时,正常处理
                trace("✅ [UserInfo] 获取期号成功: issue={$currentIssue['issue']}, status={$currentIssue['status']}, result=" . ($currentIssue['result'] ?: '空'), 'info');

                // 3. 获取期号详情(包含倒计时)
                $timeInfo = LotteryIssueService::getIssueWithCountdown($currentIssue);

                // 4. 查询用户当期下注金额
                $periodBetAmount = Db::table('la_betting_record')
                    ->where('user_id', $userId)
                    ->where('game_id', $gid)
                    ->where('issue_id', $currentIssue['id'])
                    ->where('status', '<>', 3)  // 排除已撤单
                    ->sum('total_amount');
            }

            // 5. 组装返回数据
            $result = [
                'balance' => number_format((float)$account['balance'], 2, '.', ''),  // 可用余额
                'frozen_amount' => number_format((float)$account['frozen_amount'], 2, '.', ''),  // 冻结金额
                'total_bet' => number_format((float)$account['total_bet'], 2, '.', ''),  // 累计投注
                'total_prize' => number_format((float)$account['total_prize'], 2, '.', ''),  // 累计中奖
                'period_bet_amount' => number_format((float)$periodBetAmount, 2, '.', ''),  // 当期下注金额
                'time_info' => $timeInfo,
            ];

            return $this->success('获取成功', $result);

        } catch (\Exception $e) {
            return $this->fail('获取失败: ' . $e->getMessage());
        }
    }


    /**
     * @notes 获取用户详细信息(包含现金账户和信用账户)
     * @return Json
     * @author Claude
     * @date 2025/11/28
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "获取成功",
     *   "data": {
     *     "cash_account": {
     *       "max_money": "10000.00",
     *       "used_money": "1000.00",
     *       "balance": "9000.00"
     *     },
     *     "credit_account": {
     *       "credit_limit": "10000.00",
     *       "bet_amount": "100.00",
     *       "credit_balance": "9900.00"
     *     },
     *     "account_type": "credit",
     *     "sy": "100.00",
     *     "frozen_money": "0.00"
     *   }
     * }
     */
    public function getAccountDetail()
    {
        // 获取新系统的用户ID
        $newUserId = $this->userId;

        // 映射到老系统用户
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($newUserId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

        $legacyUserId = $legacyUser['userid'];

        // 获取用户账户信息
        $userInfo = \think\facade\Db::table('x_user')
            ->where('userid', $legacyUserId)
            ->field('maxmoney,money,kmaxmoney,kmoney,sy,jzkmoney,fudong')
            ->find();

        if (!$userInfo) {
            return $this->fail('用户账户不存在');
        }

        // 现金账户
        $cashAccount = [
            'max_money' => number_format((float)$userInfo['maxmoney'], 2, '.', ''),  // 现金额度
            'used_money' => number_format((float)$userInfo['money'], 2, '.', ''),  // 已使用现金
            'balance' => number_format((float)$userInfo['maxmoney'] - (float)$userInfo['money'], 2, '.', ''),  // 现金余额
        ];

        // 信用账户(参考老系统 uxj/top.php)
        // kmoney: 可用余额(用于投注判断)
        // kmoneyuse: 剩余可用额度 = kmaxmoney + sy - jzkmoney - kmoney
        $creditLimit = (float)$userInfo['kmaxmoney'];  // 信用额度上限
        $availableBalance = (float)$userInfo['kmoney'];  // 可用余额
        $sy = (float)$userInfo['sy'];  // 上水/返点
        $frozenMoney = (float)$userInfo['jzkmoney'];  // 冻结金额

        if ($creditLimit == 0) {
            $remainingCredit = $sy - $frozenMoney - $availableBalance;
        } else {
            $remainingCredit = $creditLimit + $sy - $frozenMoney - $availableBalance;
        }

        $creditAccount = [
            'credit_limit' => number_format($creditLimit, 2, '.', ''),  // 信用额度上限
            'available_balance' => number_format($availableBalance, 2, '.', ''),  // 可用余额(可投注金额)
            'remaining_credit' => number_format($remainingCredit, 2, '.', ''),  // 剩余可用额度
        ];

        // 账户类型 (0=固定额度, 1=信用额度)
        $accountType = $userInfo['fudong'] == 1 ? 'credit' : 'cash';

        $result = [
            'cash_account' => $cashAccount,
            'credit_account' => $creditAccount,
            'account_type' => $accountType,  // cash=现金账户, credit=信用账户
            'sy' => number_format((float)$userInfo['sy'], 2, '.', ''),  // 上水/返点
            'frozen_money' => number_format((float)$userInfo['jzkmoney'], 2, '.', ''),  // 冻结金额
        ];

        return $this->success('获取成功', $result);
    }
}
