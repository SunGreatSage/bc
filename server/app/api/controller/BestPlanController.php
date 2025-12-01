<?php
declare(strict_types=1);

namespace app\api\controller;

use app\api\logic\BestPlanLogic;
use app\api\logic\LotteryLoginLogic;
use think\response\Json;

/**
 * 最佳控盘计划 - API控制器
 *
 * 功能说明：
 * 在开奖前分析所有投注数据，计算每个号码（1-49）作为特码开出时的平台盈亏。
 * 为管理员提供开奖参考，推荐利润最高的号码。
 *
 * 注意：此接口仅供管理员使用，需要管理员权限验证。
 *
 * @package app\api\controller
 * @author Claude AI
 * @date 2025-12-01
 */
class BestPlanController extends BaseApiController
{
    /**
     * 需要登录验证的方法
     */
    public array $needLogin = [
        'analyze',
        'getHistoryList',
        'getDetail',
        'calculateRealtime',
        'findByTargetRate',
        'getBetSummary',
        'getNumberDistribution'
    ];

    /**
     * 执行分析并保存结果
     *
     * @return Json
     * @route POST /api/best_plan/analyze
     *
     * 请求参数：
     * - gid: 游戏ID（默认200=新澳门六合彩）
     * - qishu: 期号
     * - year: 年份（可选，默认当前年份）
     *
     * 返回示例：
     * {
     *   "code": 1,
     *   "msg": "分析完成",
     *   "data": {
     *     "summary": {
     *       "total_bets": 100000,
     *       "best_number": 49,
     *       "best_profit": 85000,
     *       "worst_number": 7,
     *       "worst_profit": -50000
     *     },
     *     "details": [...]
     *   }
     * }
     */
    public function analyze(): Json
    {
        // 验证是否是管理员
        if (!$this->isAdmin()) {
            return $this->fail('需要管理员权限');
        }

        $gid = (int)$this->request->post('gid', 200);
        $qishu = $this->request->post('qishu', '');
        $year = $this->request->post('year');

        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        $year = $year ? (int)$year : null;

        $result = BestPlanLogic::analyze($gid, $qishu, $year);

        if ($result === false) {
            return $this->fail(BestPlanLogic::getError());
        }

        return $this->success('分析完成', $result);
    }

    /**
     * 实时计算（不保存到数据库）
     *
     * @return Json
     * @route POST /api/best_plan/calculate_realtime
     */
    public function calculateRealtime(): Json
    {
        if (!$this->isAdmin()) {
            return $this->fail('需要管理员权限');
        }

        $gid = (int)$this->request->post('gid', 200);
        $qishu = $this->request->post('qishu', '');
        $year = $this->request->post('year');

        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        $year = $year ? (int)$year : null;

        $result = BestPlanLogic::calculateRealtime($gid, $qishu, $year);

        if ($result === false) {
            return $this->fail(BestPlanLogic::getError());
        }

        return $this->success('计算完成', $result);
    }

    /**
     * 根据目标利润率查找号码
     *
     * @return Json
     * @route POST /api/best_plan/find_by_rate
     *
     * 请求参数：
     * - gid: 游戏ID
     * - qishu: 期号
     * - target_rate: 目标利润率（如10表示10%）
     * - tolerance: 允许误差（默认1，表示±1%）
     */
    public function findByTargetRate(): Json
    {
        if (!$this->isAdmin()) {
            return $this->fail('需要管理员权限');
        }

        $gid = (int)$this->request->post('gid', 200);
        $qishu = $this->request->post('qishu', '');
        $targetRate = (float)$this->request->post('target_rate', 10.0);
        $tolerance = (float)$this->request->post('tolerance', 1.0);
        $year = $this->request->post('year');

        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        $year = $year ? (int)$year : null;

        $result = BestPlanLogic::findByTargetRate($gid, $qishu, $targetRate, $tolerance, $year);

        if ($result === false) {
            return $this->fail(BestPlanLogic::getError());
        }

        return $this->success('查找完成', $result);
    }

    /**
     * 获取分析历史列表
     *
     * @return Json
     * @route GET /api/best_plan/history_list
     */
    public function getHistoryList(): Json
    {
        if (!$this->isAdmin()) {
            return $this->fail('需要管理员权限');
        }

        $gid = (int)$this->request->get('gid', 200);
        $limit = (int)$this->request->get('limit', 10);

        $list = BestPlanLogic::getHistoryList($gid, $limit);

        return $this->success('获取成功', $list);
    }

    /**
     * 获取分析详情
     *
     * @return Json
     * @route GET /api/best_plan/detail
     */
    public function getDetail(): Json
    {
        if (!$this->isAdmin()) {
            return $this->fail('需要管理员权限');
        }

        $id = (int)$this->request->get('id', 0);

        if ($id <= 0) {
            return $this->fail('ID不能为空');
        }

        $detail = BestPlanLogic::getDetail($id);

        if (!$detail) {
            return $this->fail('记录不存在');
        }

        return $this->success('获取成功', $detail);
    }

    /**
     * 获取当前可分析的期号
     *
     * @return Json
     * @route GET /api/best_plan/current_qishu
     */
    public function getCurrentQishu(): Json
    {
        $gid = (int)$this->request->get('gid', 200);

        $qishu = BestPlanLogic::getCurrentQishu($gid);

        if (!$qishu) {
            return $this->fail('暂无可分析的期号');
        }

        return $this->success('获取成功', $qishu);
    }

    /**
     * 获取投注汇总统计（按玩法分类）
     *
     * @return Json
     * @route GET /api/best_plan/bet_summary
     */
    public function getBetSummary(): Json
    {
        if (!$this->isAdmin()) {
            return $this->fail('需要管理员权限');
        }

        $gid = (int)$this->request->get('gid', 200);
        $qishu = $this->request->get('qishu', '');

        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        $summary = BestPlanLogic::getBetSummaryByPlay($gid, $qishu);

        return $this->success('获取成功', $summary);
    }

    /**
     * 获取号码投注分布（特码玩法）
     *
     * @return Json
     * @route GET /api/best_plan/number_distribution
     */
    public function getNumberDistribution(): Json
    {
        if (!$this->isAdmin()) {
            return $this->fail('需要管理员权限');
        }

        $gid = (int)$this->request->get('gid', 200);
        $qishu = $this->request->get('qishu', '');

        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        $distribution = BestPlanLogic::getNumberBetDistribution($gid, $qishu);

        return $this->success('获取成功', $distribution);
    }

    /**
     * 检查当前用户是否是管理员
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        // 检查是否登录
        if (empty($this->userId)) {
            return false;
        }

        // 检查是否是管理员账号（account 以 "admin_" 开头）
        $admin = LotteryLoginLogic::getAdminByNewUserId($this->userId);

        return $admin !== null;
    }
}
