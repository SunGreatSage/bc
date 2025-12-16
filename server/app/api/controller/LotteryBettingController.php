<?php

namespace app\api\controller;

use app\api\logic\LotteryBettingLogic;
use think\response\Json;

/**
 * 彩票投注控制器
 */
class LotteryBettingController extends BaseApiController
{
    /**
     * 投注下单
     * @return Json
     */
    public function placeBet()
    {
        try {
            $params = $this->request->post();
            $userId = $this->userId; // 从中间件获取

            $result = LotteryBettingLogic::placeBet($params, $userId);

            return $this->success('投注成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取用户余额
     * @return Json
     */
    public function getBalance()
    {
        try {
            $userId = $this->userId;
            $result = LotteryBettingLogic::getUserBalance($userId);

            return $this->success('获取成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取投注记录
     * @return Json
     */
    public function getBettingRecords()
    {
        try {
            $userId = $this->userId;
            $page = $this->request->get('page', 1);
            $limit = $this->request->get('limit', 20);
            $plateCode = $this->request->get('plate_code', '');

            $result = LotteryBettingLogic::getBettingRecords($userId, $page, $limit, $plateCode);

            return $this->success('获取成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取当前期号
     * @return Json
     */
    public function getCurrentIssue()
    {
        try {
            $gameId = $this->request->get('game_id', 200);
            $plateCode = $this->request->get('plate_code', 'A');

            $result = LotteryBettingLogic::getCurrentIssue($gameId, $plateCode);

            return $this->success('获取成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取盘口列表
     * @return Json
     */
    public function getPlateList()
    {
        try {
            $gameId = $this->request->get('game_id', 200);
            $result = LotteryBettingLogic::getPlateList($gameId);

            return $this->success('获取成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取中奖记录
     * @return Json
     */
    public function getWinningRecords()
    {
        try {
            $userId = $this->userId;
            $page = $this->request->get('page', 1);
            $limit = $this->request->get('limit', 20);
            $plateCode = $this->request->get('plate_code', '');

            $result = LotteryBettingLogic::getWinningRecords($userId, $page, $limit, $plateCode);

            return $this->success('获取成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取账户流水
     * @return Json
     */
    public function getAccountLogs()
    {
        try {
            $userId = $this->userId;
            $page = $this->request->get('page', 1);
            $limit = $this->request->get('limit', 20);
            $changeType = $this->request->get('change_type', 0);

            $result = LotteryBettingLogic::getAccountLogs($userId, $page, $limit, $changeType);

            return $this->success('获取成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取开奖结果
     * @return Json
     */
    public function getDrawResult()
    {
        try {
            $gameId = $this->request->get('game_id', 200);
            $plateCode = $this->request->get('plate_code', 'A');
            $issue = $this->request->get('issue', '');

            $result = LotteryBettingLogic::getDrawResult($gameId, $plateCode, $issue);

            return $this->success('获取成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
