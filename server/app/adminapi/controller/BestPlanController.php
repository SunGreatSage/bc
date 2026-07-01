<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 管理端最佳控盘计划控制器
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-12-11
// +----------------------------------------------------------------------

namespace app\adminapi\controller;

use app\adminapi\logic\BestPlanLogic;
use think\response\Json;

/**
 * 管理端最佳控盘计划控制器
 * Class BestPlanController
 * @package app\adminapi\controller
 */
class BestPlanController extends BaseAdminController
{
    /**
     * 不需要登录的接口
     */
    public array $notNeedLogin = ['getCurrentQishu', 'getPlateList'];


    /**
     * @notes 获取盘口列表
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param int gid 游戏ID（默认200）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "获取成功",
     *   "data": [
     *     {"id": 1, "code": "A", "name": "A盘"},
     *     {"id": 2, "code": "B", "name": "B盘"},
     *     {"id": 3, "code": "C", "name": "C盘"}
     *   ]
     * }
     */
    public function getPlateList(): Json
    {
        $gid = (int)$this->request->get('gid', 200);

        $plates = BestPlanLogic::getPlateList($gid);

        return $this->success('获取成功', $plates);
    }


    /**
     * @notes 获取当前期号信息
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param int gid 游戏ID（默认200）
     * @param string plate_code 盘口代码（默认A）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "获取成功",
     *   "data": {
     *     "qishu": "2025334",
     *     "plate_code": "A",
     *     "opentime": "2025-12-01 06:00:00",
     *     "closetime": "2025-12-01 09:30:00",
     *     "kjtime": "2025-12-01 09:50:00",
     *     "is_opened": false
     *   }
     * }
     */
    public function getCurrentQishu(): Json
    {
        $gid = (int)$this->request->get('gid', 200);
        $plateCode = $this->request->get('plate_code', 'A');

        $qishu = BestPlanLogic::getCurrentQishu($gid, $plateCode);

        if (!$qishu) {
            return $this->fail('暂无可分析的期号');
        }

        return $this->success('获取成功', $qishu);
    }


    /**
     * @notes 实时计算分析（不保存到数据库）
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param int gid 游戏ID（默认200）
     * @param string qishu 期号
     * @param string plate_code 盘口代码（如A、B、C）
     * @param int year 年份（可选）
     * @param float target_rate 目标利润率（可选，如10表示10%，为空则最大化利润）
     * @param float tolerance 允许误差（可选，默认5%）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "计算完成",
     *   "data": {
     *     "summary": {
     *       "total_bets": 100000,
     *       "total_orders": 50,
     *       "best_numbers": [1,2,3,4,5,6,7],
     *       "best_m7": 7,
     *       "best_m1_m6": [1,2,3,4,5,6],
     *       "best_profit": 85000,
     *       "best_profit_rate": 85.0
     *     },
     *     "best_solution": {...},
     *     "top_solutions": [...]
     *   }
     * }
     */
    public function calculateRealtime(): Json
    {
        $gid = (int)$this->request->post('gid', 200);
        $qishu = $this->request->post('qishu', '');
        $plateCode = $this->request->post('plate_code', 'A');  // 新增：盘口代码
        $year = $this->request->post('year');
        $targetRate = $this->request->post('target_rate');  // 新增：目标利润率
        $tolerance = $this->request->post('tolerance');
        $sortBy = $this->request->post('sort_by');
        $limit = $this->request->post('limit');
        $maxConsecutive = $this->request->post('max_consecutive');

        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        $year = $year ? (int)$year : null;
        $targetRate = $targetRate !== '' && $targetRate !== null ? (float)$targetRate : null;
        $tolerance = $tolerance !== '' && $tolerance !== null ? (float)$tolerance : 5.0;
        $sortBy = is_string($sortBy) ? trim($sortBy) : null;
        if ($sortBy === '') {
            $sortBy = null;
        }
        $limit = $limit !== '' && $limit !== null ? (int)$limit : null;
        if ($limit !== null && $limit <= 0) {
            $limit = null;
        }
        $maxConsecutive = $maxConsecutive !== '' && $maxConsecutive !== null ? (int)$maxConsecutive : null;
        if ($maxConsecutive !== null && $maxConsecutive <= 0) {
            $maxConsecutive = null;
        }

        $result = BestPlanLogic::calculateRealtime($gid, $qishu, $plateCode, $year, $targetRate, $tolerance, $sortBy, $limit, $maxConsecutive);

        if ($result === false) {
            return $this->fail(BestPlanLogic::getError());
        }

        return $this->success('计算完成', $result);
    }


    /**
     * @notes 执行分析并保存结果
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param int gid 游戏ID（默认200）
     * @param string qishu 期号
     * @param string plate_code 盘口代码（默认A）
     * @param int year 年份（可选）
     */
    /**
     * @notes 分析控盘计划(别名方法)
     * @return Json
     * @author Claude
     * @date 2025/12/12
     */
    public function analyze(): Json
    {
        return $this->analyzeAndSave();
    }

    /**
     * @notes 分析控盘计划并保存
     * @return Json
     * @author Claude
     * @date 2025/12/11
     */
    public function analyzeAndSave(): Json
    {
        $gid = (int)$this->request->post('gid', 200);
        $qishu = $this->request->post('qishu', '');
        $plateCode = $this->request->post('plate_code', 'A');
        $year = $this->request->post('year');

        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        $year = $year ? (int)$year : null;

        $result = BestPlanLogic::analyze($gid, $qishu, $plateCode, $year);

        if ($result === false) {
            return $this->fail(BestPlanLogic::getError());
        }

        return $this->success('分析完成', $result);
    }


    /**
     * @notes 根据目标利润率查找号码
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param int gid 游戏ID
     * @param string qishu 期号
     * @param float target_rate 目标利润率（如10表示10%）
     * @param float tolerance 允许误差（默认1，表示±1%）
     * @param int year 年份（可选）
     */
    public function findByTargetRate(): Json
    {
        $gid = (int)$this->request->post('gid', 200);
        $qishu = $this->request->post('qishu', '');
        $plateCode = $this->request->post('plate_code', 'A');  // 新增：盘口代码
        $targetRate = (float)$this->request->post('target_rate', 10.0);
        $tolerance = (float)$this->request->post('tolerance', 1.0);
        $year = $this->request->post('year');

        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        $year = $year ? (int)$year : null;

        $result = BestPlanLogic::findByTargetRate($gid, $qishu, $plateCode, $targetRate, $tolerance, $year);

        if ($result === false) {
            return $this->fail(BestPlanLogic::getError());
        }

        return $this->success('查询成功', $result);
    }


    /**
     * @notes 获取历史分析记录列表
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param int gid 游戏ID
     * @param int page 页码（默认1）
     * @param int limit 每页数量（默认15）
     */
    public function getHistoryList(): Json
    {
        $gid = (int)$this->request->get('gid', 200);
        $page = (int)$this->request->get('page', 1);
        $limit = (int)$this->request->get('limit', 15);

        $result = BestPlanLogic::getHistoryList($gid, $page, $limit);

        return $this->success('获取成功', $result);
    }


    /**
     * @notes 获取历史分析详情
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param int id 记录ID
     */
    public function getDetail(): Json
    {
        $id = (int)$this->request->get('id', 0);

        if (empty($id)) {
            return $this->fail('记录ID不能为空');
        }

        $result = BestPlanLogic::getDetail($id);

        if ($result === false) {
            return $this->fail(BestPlanLogic::getError());
        }

        return $this->success('获取成功', $result);
    }


    /**
     * @notes 获取用户历史下单记录
     * @return Json
     */
    public function getOrderHistory(): Json
    {
        $params = $this->request->get();
        $result = BestPlanLogic::getOrderHistory($params);

        return $this->success('获取成功', $result);
    }


    /**
     * @notes 对比新旧算法(增强版 vs 原始版)
     * @return Json
     * @author Claude
     * @date 2025/12/12
     *
     * 请求参数：
     * @param int gid 游戏ID（默认200）
     * @param string qishu 期号
     * @param string plate_code 盘口代码（默认A）
     * @param string strategy 策略选择: 'max_profit' | 'avoid_hot' | 'balanced' (默认balanced)
     * @param int year 年份（可选）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "对比完成",
     *   "data": {
     *     "original_algorithm": {
     *       "best_solution": {...},
     *       "profit_rate": -75.00
     *     },
     *     "enhanced_algorithm": {
     *       "best_solution": {...},
     *       "profit_rate": -20.00,
     *       "risk_assessment": {...},
     *       "recommendations": [...]
     *     },
     *     "improvement": {
     *       "profit_increased": 55.00,
     *       "is_better": true
     *     }
     *   }
     * }
     */
    public function compareAlgorithms(): Json
    {
        $gid = (int)$this->request->post('gid', 200);
        $qishu = $this->request->post('qishu', '');
        $plateCode = $this->request->post('plate_code', 'A');
        $strategy = $this->request->post('strategy', 'balanced');
        $year = $this->request->post('year');

        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        $year = $year ? (int)$year : (int)date('Y');

        try {
            // 原始算法
            $originalService = new \app\common\service\BestPlanService($gid, $qishu, $year);
            $originalResult = $originalService->findBest7Numbers();

            // 增强算法
            $enhancedService = new \app\common\service\EnhancedBestPlanService($gid, $qishu, $year);
            $enhancedResult = $enhancedService->findBest7NumbersEnhanced(null, 5.0, $strategy);

            // 号码密度报告
            $densityReport = $enhancedService->getNumberDensityReport();

            // 计算改进幅度
            $originalProfit = $originalResult['best_solution']['profit_rate'] ?? 0;
            $enhancedProfit = $enhancedResult['best_solution']['profit_rate'] ?? 0;
            $improvement = $enhancedProfit - $originalProfit;

            return $this->success('对比完成', [
                'original_algorithm' => [
                    'name' => '原始算法(贪心策略)',
                    'best_solution' => $originalResult['best_solution'],
                    'top_solutions' => $originalResult['top_solutions'],
                ],
                'enhanced_algorithm' => [
                    'name' => '增强算法(多策略优化)',
                    'best_solution' => $enhancedResult['best_solution'],
                    'top_solutions' => $enhancedResult['top_solutions'],
                    'strategy_used' => $enhancedResult['strategy_used'],
                    'risk_assessment' => $enhancedResult['risk_assessment'],
                    'recommendations' => $enhancedResult['recommendations'],
                ],
                'improvement' => [
                    'profit_increased' => $improvement,
                    'profit_increased_percent' => number_format($improvement, 2) . '%',
                    'is_better' => $improvement > 0,
                    'comparison_text' => $improvement > 0
                        ? "增强算法利润提升了 {$improvement}%"
                        : ($improvement < 0
                            ? "增强算法利润下降了 " . abs($improvement) . "%"
                            : "两种算法利润相同"),
                ],
                'number_density_report' => array_slice($densityReport, 0, 10),  // 前10个热点号码
            ]);

        } catch (\Exception $e) {
            return $this->fail('对比失败: ' . $e->getMessage());
        }
    }


    /**
     * @notes 提交开奖计划（封盘后写入 planned_result，不公布、不结算）
     * @return Json
     * @author Claude
     * @date 2025/12/11
     *
     * 请求参数：
     * @param int gid 游戏ID
     * @param string qishu 期号
     * @param string plate_code 盘口代码（如A、B、C）
     * @param array best_numbers 7个号码数组 [m1,m2,m3,m4,m5,m6,m7]
     * @param int year 年份（可选）
     *
     * 流程：
     * 1. 校验已封盘（close_time 到达）
     * 2. 写入 la_lottery_issue.planned_result（仅后台可见）
     * 3. 不触发对外开奖展示、不做结算/派奖/分成
     * 4. 到 draw_time 由定时任务发布 result 并结算
     */
    public function executeDrawing(): Json
    {
        $gid = (int)$this->request->post('gid', 200);
        $qishu = $this->request->post('qishu', '');
        $plateCode = $this->request->post('plate_code', 'A');  // 新增：盘口代码
        $bestNumbers = $this->request->post('best_numbers', '');
        $year = $this->request->post('year');

        // 验证参数
        if (empty($qishu)) {
            return $this->fail('期号不能为空');
        }

        if (empty($bestNumbers)) {
            return $this->fail('开奖号码不能为空');
        }

        // 解析号码（可能是数组或逗号分隔字符串）
        if (is_string($bestNumbers)) {
            $bestNumbers = explode(',', $bestNumbers);
        }
        $bestNumbers = array_map('intval', $bestNumbers);

        // 验证号码数量
        if (count($bestNumbers) !== 7) {
            return $this->fail('必须提供7个开奖号码');
        }

        // 验证号码范围
        foreach ($bestNumbers as $num) {
            if ($num < 1 || $num > 49) {
                return $this->fail('号码必须在1-49之间');
            }
        }

        $year = $year ? (int)$year : (int)date('Y');

        // 提交计划（记录操作员ID，便于审计）
        $result = BestPlanLogic::executeDrawing($gid, $qishu, $plateCode, $bestNumbers, $year, $this->adminId);

        if ($result === false) {
            return $this->fail(BestPlanLogic::getError());
        }

        return $this->success('提交计划成功', $result);
    }


    /**
     * @notes 预览手动创建的新期号
     * @return Json
     */
    public function previewNewIssue(): Json
    {
        $gid = (int)$this->request->post('gid', 200);
        $plateCode = $this->request->post('plate_code', 'A');
        $strategy = $this->request->post('strategy', 'plate_config');

        try {
            $result = BestPlanLogic::previewNewIssue($gid, $plateCode, $strategy);

            if ($result === false) {
                return $this->fail(BestPlanLogic::getError());
            }

            return $this->success('预览成功', $result);
        } catch (\Exception $e) {
            return $this->fail('预览失败: ' . $e->getMessage());
        }
    }

    /**
     * @notes 手动创建新期号
     * @return Json
     * @author Claude
     * @date 2025/12/12
     *
     * 请求参数：
     * @param int gid 游戏ID（默认200）
     * @param string plate_code 盘口代码（默认A）
     *
     * 响应示例：
     * {
     *   "code": 1,
     *   "msg": "新期号创建成功",
     *   "data": {
     *     "issue": "2025113",
     *     "open_time": "2025-12-12 06:00:00",
     *     "close_time": "2025-12-12 09:30:00",
     *     "draw_time": "2025-12-12 09:50:00",
     *     "status": 0,
     *     "status_text": "待开盘"
     *   }
     * }
     */
    public function createNewIssue(): Json
    {
        $gid = (int)$this->request->post('gid', 200);
        $plateCode = $this->request->post('plate_code', 'A');
        $strategy = $this->request->post('strategy', 'plate_config');

        try {
            $result = BestPlanLogic::createNewIssue($gid, $plateCode, $strategy);

            if ($result === false) {
                return $this->fail(BestPlanLogic::getError());
            }

            return $this->success('新期号创建成功', $result);
        } catch (\Exception $e) {
            return $this->fail('创建失败: ' . $e->getMessage());
        }
    }
}
