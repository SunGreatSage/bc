<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 用户信息控制器
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-28
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\LotteryLoginLogic;
use think\response\Json;

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
     *     "credit_limit": "10000.00",
     *     "bet_amount": "100.00",
     *     "credit_balance": "9900.00",
     *     "time_info": {
     *       "current_qishu": "2025112",
     *       "open_time": "2025-11-28 09:00:00",
     *       "close_time": "2025-11-28 21:25:00",
     *       "kj_time": "2025-11-28 21:30:00",
     *       "seconds_to_open": -36000,
     *       "seconds_to_close": 8100,
     *       "seconds_to_kj": 8400,
     *       "status": "betting"
     *     }
     *   }
     * }
     */
    public function getUserInfo()
    {
        // 获取新系统的用户ID
        $newUserId = $this->userId;

        // 映射到老系统用户
        $legacyUser = LotteryLoginLogic::getLegacyUserByNewUserId($newUserId);

        if (!$legacyUser) {
            return $this->fail('用户信息不存在');
        }

        $legacyUserId = $legacyUser['userid'];

        // 获取游戏ID(默认200=新澳门六合彩)
        $gid = $this->request->param('gid/d', 200);

        // 1. 获取用户账户信息
        $userInfo = \think\facade\Db::table('x_user')
            ->where('userid', $legacyUserId)
            ->field('kmaxmoney,kmoney,sy,jzkmoney,money,maxmoney')
            ->find();

        if (!$userInfo) {
            return $this->fail('用户账户不存在');
        }

        // ========== 字段含义(参考老系统 uxj/top.php) ==========
        // kmaxmoney: 信用额度上限
        // kmoney: 可用余额(用于投注判断的余额)
        // sy: 上水/返点金额
        // jzkmoney: 冻结金额
        // kmoneyuse: 剩余可用额度 = kmaxmoney + sy - jzkmoney - kmoney (计算值)

        $creditLimit = (float)$userInfo['kmaxmoney'];  // 信用额度上限
        $availableBalance = (float)$userInfo['kmoney'];  // 可用余额(可投注金额)
        $sy = (float)$userInfo['sy'];  // 上水/返点
        $frozenMoney = (float)$userInfo['jzkmoney'];  // 冻结金额

        // 剩余可用额度(参考老系统 uxj/top.php 第22-24行)
        if ($creditLimit == 0) {
            // 如果信用额度为0
            $remainingCredit = $sy - $frozenMoney - $availableBalance;
        } else {
            // 使用信用账户
            $remainingCredit = $creditLimit + $sy - $frozenMoney - $availableBalance;
        }

        // 2. 获取当前期号和时间信息
        $gameInfo = \think\facade\Db::table('x_game')
            ->where('gid', $gid)
            ->field('thisqishu')
            ->find();

        if (!$gameInfo) {
            return $this->fail('游戏不存在');
        }

        $currentQishu = $gameInfo['thisqishu'];

        // 获取当前期的时间信息
        $kjInfo = \think\facade\Db::table('x_kj')
            ->where('gid', $gid)
            ->where('qishu', $currentQishu)
            ->field('opentime,closetime,kjtime')
            ->find();

        $timeInfo = [
            'current_qishu' => $currentQishu,
            'open_time' => null,
            'close_time' => null,
            'kj_time' => null,
            'seconds_to_open' => null,
            'seconds_to_close' => null,
            'seconds_to_kj' => null,
            'status' => 'unknown',  // unknown/before_open/betting/closed/settled
        ];

        if ($kjInfo) {
            $now = time();
            $openTime = strtotime($kjInfo['opentime']);
            $closeTime = strtotime($kjInfo['closetime']);
            $kjTime = strtotime($kjInfo['kjtime']);

            $timeInfo['open_time'] = $kjInfo['opentime'];
            $timeInfo['close_time'] = $kjInfo['closetime'];
            $timeInfo['kj_time'] = $kjInfo['kjtime'];
            $timeInfo['seconds_to_open'] = $openTime - $now;
            $timeInfo['seconds_to_close'] = $closeTime - $now;
            $timeInfo['seconds_to_kj'] = $kjTime - $now;

            // 判断当前状态
            if ($now < $openTime) {
                $timeInfo['status'] = 'before_open';  // 未开盘
            } elseif ($now >= $openTime && $now < $closeTime) {
                $timeInfo['status'] = 'betting';  // 投注中
            } elseif ($now >= $closeTime && $now < $kjTime) {
                $timeInfo['status'] = 'closed';  // 已封盘
            } else {
                $timeInfo['status'] = 'settled';  // 已开奖
            }
        }

        // 3. 组装返回数据
        $result = [
            'credit_limit' => number_format($creditLimit, 2, '.', ''),  // 信用额度上限
            'available_balance' => number_format($availableBalance, 2, '.', ''),  // 可用余额(可投注金额,与placeBet一致)
            'remaining_credit' => number_format($remainingCredit, 2, '.', ''),  // 剩余可用额度
            'sy' => number_format($sy, 2, '.', ''),  // 上水/返点
            'frozen_money' => number_format($frozenMoney, 2, '.', ''),  // 冻结金额
            'time_info' => $timeInfo,
        ];

        return $this->success('获取成功', $result);
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
