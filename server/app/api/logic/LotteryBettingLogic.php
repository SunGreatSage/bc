<?php

namespace app\api\logic;

use app\common\model\lottery\{LotteryPlate, LotteryIssue, UserAccount, BettingRecord, WinningRecord, AccountLog, UserExtend};
use app\common\service\OrderSnService;
use think\facade\Db;
use think\Exception;

/**
 * 彩票投注业务逻辑
 */
class LotteryBettingLogic
{
    /**
     * 投注下单
     */
    public static function placeBet($params, $userId)
    {
        // 1. 参数验证
        self::validateParams($params);

        $gameId = $params['game_id'];
        $plateCode = $params['plate_code'];
        $issue = $params['issue'];
        $methodId = $params['method_id'];
        $betContent = $params['bet_content'];
        $betAmount = $params['bet_amount'];
        $betMultiple = $params['bet_multiple'] ?? 1;

        // 2. 验证盘口
        $plate = LotteryPlate::getByCode($plateCode, $gameId);
        if (!$plate) {
            throw new Exception('盘口不存在或已停用');
        }

        // 3. 验证期次
        $issueModel = LotteryIssue::where([
            'game_id' => $gameId,
            'plate_id' => $plate->id,
            'issue' => $issue
        ])->find();

        if (!$issueModel) {
            throw new Exception('期号不存在');
        }

        if (!$issueModel->canBet()) {
            throw new Exception('当前期次已封盘或不可投注');
        }

        // 4. 计算总金额
        $totalAmount = $betAmount * $betMultiple;

        // 5. 开启事务
        Db::startTrans();
        try {
            // 6. 锁定用户账户
            $account = UserAccount::getAccountWithLock($userId);
            if (!$account) {
                throw new Exception('用户账户不存在');
            }

            // 7. 检查余额
            if ($account->balance < $totalAmount) {
                throw new Exception('账户余额不足');
            }

            // 8. 扣减余额并冻结
            $account->deductBalance($totalAmount);

            // 9. 获取用户扩展信息
            $userExtend = UserExtend::where('user_id', $userId)->find();

            // 10. 生成投注记录
            $sn = BettingRecord::generateSn();
            $betting = BettingRecord::create([
                'sn' => $sn,
                'user_id' => $userId,
                'game_id' => $gameId,
                'plate_id' => $plate->id,
                'plate_code' => $plateCode,
                'issue_id' => $issueModel->id,
                'issue' => $issue,
                'method_id' => $methodId,
                'method_name' => $params['method_name'] ?? '',
                'bet_content' => $betContent,
                'bet_amount' => $betAmount,
                'bet_multiple' => $betMultiple,
                'total_amount' => $totalAmount,
                'odds' => $params['odds'] ?? 0,
                'status' => 0,
                'parent_id' => $userExtend->parent_id ?? 0,
                'ancestor_ids' => $userExtend->ancestor_ids ?? '',
                'ip' => request()->ip(),
                'created_at' => time()
            ]);

            // 11. 记录账户流水
            AccountLog::create([
                'sn' => OrderSnService::generateLogSn($userId),  // ✅ 使用微秒级唯一编号
                'user_id' => $userId,
                'change_type' => 3, // 投注
                'change_amount' => -$totalAmount,
                'balance_before' => $account->balance + $totalAmount,
                'balance_after' => $account->balance,
                'frozen_before' => $account->frozen_amount - $totalAmount,
                'frozen_after' => $account->frozen_amount,
                'related_sn' => $sn,
                'related_type' => 1,
                'remark' => "投注扣款: {$plateCode}盘 {$issue}期",
                'ip' => request()->ip(),
                'created_at' => time()
            ]);

            // 12. 更新期次统计
            $issueModel->total_bet_amount += $totalAmount;
            $issueModel->save();

            Db::commit();

            return [
                'sn' => $sn,
                'balance' => $account->balance,
                'frozen_amount' => $account->frozen_amount
            ];

        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 获取用户余额
     */
    public static function getUserBalance($userId)
    {
        $account = UserAccount::where('user_id', $userId)->find();
        if (!$account) {
            return [
                'balance' => 0,
                'frozen_amount' => 0,
                'total_bet' => 0,
                'total_prize' => 0
            ];
        }

        return [
            'balance' => $account->balance,
            'frozen_amount' => $account->frozen_amount,
            'total_bet' => $account->total_bet,
            'total_prize' => $account->total_prize,
            'total_commission' => $account->total_commission
        ];
    }

    /**
     * 获取投注记录
     */
    public static function getBettingRecords($userId, $page = 1, $limit = 20, $plateCode = '')
    {
        $list = BettingRecord::getUserBettings($userId, $page, $limit, $plateCode);
        $total = BettingRecord::where('user_id', $userId)->count();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
    }

    /**
     * 获取当前期号
     */
    public static function getCurrentIssue($gameId, $plateCode)
    {
        $plate = LotteryPlate::getByCode($plateCode, $gameId);
        if (!$plate) {
            throw new Exception('盘口不存在');
        }

        $issue = LotteryIssue::getCurrentIssue($gameId, $plate->id);
        if (!$issue) {
            throw new Exception('暂无可投注期次');
        }

        return [
            'issue' => $issue->issue,
            'plate_code' => $plateCode,
            'plate_name' => $plate->name,  // 新表字段名为 name
            'open_time' => $issue->open_time,
            'close_time' => $issue->close_time,
            'draw_time' => $issue->draw_time,
            'status' => $issue->status,
            'can_bet' => $issue->canBet()
        ];
    }

    /**
     * 获取盘口列表
     */
    public static function getPlateList($gameId)
    {
        return LotteryPlate::getEnabledPlates($gameId);
    }

    /**
     * 获取中奖记录
     */
    public static function getWinningRecords($userId, $page = 1, $limit = 20, $plateCode = '')
    {
        $list = WinningRecord::getUserWinnings($userId, $page, $limit, $plateCode);
        $total = WinningRecord::where('user_id', $userId)->count();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
    }

    /**
     * 获取账户流水
     */
    public static function getAccountLogs($userId, $page = 1, $limit = 20, $changeType = 0)
    {
        $list = AccountLog::getUserLogs($userId, $page, $limit, $changeType);
        $total = AccountLog::where('user_id', $userId)->count();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
    }

    /**
     * 获取开奖结果
     */
    public static function getDrawResult($gameId, $plateCode, $issue = '')
    {
        $plate = LotteryPlate::getByCode($plateCode, $gameId);
        if (!$plate) {
            throw new Exception('盘口不存在');
        }

        $where = [
            'game_id' => $gameId,
            'plate_id' => $plate->id,
            'status' => ['in', [4, 5]] // 已开奖或已结算
        ];

        if ($issue) {
            $where['issue'] = $issue;
            $result = LotteryIssue::where($where)->find();
            return $result ? [$result] : [];
        } else {
            // 返回最近10期
            return LotteryIssue::where($where)
                ->order('issue', 'desc')
                ->limit(10)
                ->select();
        }
    }

    /**
     * 参数验证
     */
    private static function validateParams($params)
    {
        if (empty($params['game_id'])) {
            throw new Exception('游戏ID不能为空');
        }
        if (empty($params['plate_code'])) {
            throw new Exception('盘口代码不能为空');
        }
        if (empty($params['issue'])) {
            throw new Exception('期号不能为空');
        }
        if (empty($params['method_id'])) {
            throw new Exception('玩法ID不能为空');
        }
        if (empty($params['bet_content'])) {
            throw new Exception('投注内容不能为空');
        }
        if (empty($params['bet_amount']) || $params['bet_amount'] <= 0) {
            throw new Exception('投注金额必须大于0');
        }
    }
}
