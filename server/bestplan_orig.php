<?php
declare(strict_types=1);

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\service\BestPlanService;
use think\facade\Db;

/**
 * 最佳控盘计划 - 业务逻辑类
 *
 * @package app\api\logic
 * @author Claude AI
 * @date 2025-12-01
 */
class BestPlanLogic extends BaseLogic
{
    /**
     * 执行分析并保存结果
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param int|null $year 年份（可选，默认当前年份）
     * @return array|false
     */
    public static function analyze(int $gid, string $qishu, ?int $year = null)
    {
        try {
            $year = $year ?? (int)date('Y');

            // 创建计算服务
            $service = new BestPlanService($gid, $qishu, $year);

            // 检查是否有投注数据
            if ($service->getBetCount() === 0) {
                self::setError('该期暂无投注数据');
                return false;
            }

            // 获取所有号码的利润数据
            $allResults = $service->getAllProfits();

            // 获取摘要
            $summary = $service->getSummary();

            // 将49个号码详情转为JSON
            $numberDetails = json_encode($allResults, JSON_UNESCAPED_UNICODE);

            // 准备保存数据
            $data = [
                'gid' => $gid,
                'qishu' => $qishu,
                'analyze_time' => date('Y-m-d H:i:s'),
                'total_bets' => $summary['total_bets'],
                'total_orders' => $summary['total_orders'],
                'best_number' => $summary['best_number'],
                'best_profit' => $summary['best_profit'],
                'best_profit_rate' => $summary['best_profit_rate'],
                'worst_number' => $summary['worst_number'],
                'worst_profit' => $summary['worst_profit'],
                'worst_profit_rate' => $summary['worst_profit_rate'],
                'avg_profit' => $summary['avg_profit'],
                'number_details' => $numberDetails,
                'status' => 0,  // 未开奖
            ];

            // 检查是否已存在
            $exists = Db::table('x_best_plan_history')
                ->where('gid', $gid)
                ->where('qishu', $qishu)
                ->find();

            if ($exists) {
                // 更新现有记录
                Db::table('x_best_plan_history')
                    ->where('id', $exists['id'])
                    ->update($data);
            } else {
                // 插入新记录
                Db::table('x_best_plan_history')->insert($data);
            }

            return [
                'summary' => $summary,
                'details' => $allResults,
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 实时计算（不保存到数据库）
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param int|null $year 年份
     * @return array|false
     */
    public static function calculateRealtime(int $gid, string $qishu, ?int $year = null)
    {
        try {
            $year = $year ?? (int)date('Y');

            $service = new BestPlanService($gid, $qishu, $year);

            if ($service->getBetCount() === 0) {
                self::setError('该期暂无投注数据');
                return false;
            }

            return [
                'summary' => $service->getSummary(),
                'details' => $service->getAllProfits(),
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 根据目标利润率查找号码
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param float $targetRate 目标利润率
     * @param float $tolerance 允许误差
     * @param int|null $year 年份
     * @return array|false
     */
    public static function findByTargetRate(
        int $gid,
        string $qishu,
        float $targetRate,
        float $tolerance = 1.0,
        ?int $year = null
    ) {
        try {
            $year = $year ?? (int)date('Y');

            $service = new BestPlanService($gid, $qishu, $year);

            if ($service->getBetCount() === 0) {
                self::setError('该期暂无投注数据');
                return false;
            }

            $matched = $service->findByTargetRate($targetRate, $tolerance);

            return [
                'target_rate' => $targetRate,
                'tolerance' => $tolerance,
                'matched_count' => count($matched),
                'matched_numbers' => $matched,
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 获取分析历史列表
     *
     * @param int $gid 游戏ID
     * @param int $limit 返回条数
     * @return array
     */
    public static function getHistoryList(int $gid, int $limit = 10): array
    {
        return Db::table('x_best_plan_history')
            ->field('id, gid, qishu, analyze_time, total_bets, total_orders,
                     best_number, best_profit, best_profit_rate,
                     worst_number, worst_profit, worst_profit_rate,
                     avg_profit, status, actual_number, actual_profit')
            ->where('gid', $gid)
            ->order('analyze_time', 'desc')
            ->limit($limit)
            ->select()
            ->toArray();
    }

    /**
     * 获取分析详情
     *
     * @param int $id 记录ID
     * @return array|null
     */
    public static function getDetail(int $id): ?array
    {
        $record = Db::table('x_best_plan_history')
            ->where('id', $id)
            ->find();

        if (!$record) {
            return null;
        }

        // 解析JSON字段
        $record['number_details'] = json_decode($record['number_details'], true);

        // 按利润排序
        if (is_array($record['number_details'])) {
            usort($record['number_details'], fn($a, $b) => $b['profit'] <=> $a['profit']);
        }

        // 添加风险等级文本
        foreach ($record['number_details'] as &$item) {
            $item['risk_level_text'] = BestPlanService::getRiskLevelText($item['risk_level']);
        }

        return $record;
    }

    /**
     * 获取当前可分析的期号
     *
     * @param int $gid 游戏ID
     * @return array|null
     */
    public static function getCurrentQishu(int $gid): ?array
    {
        // 从 x_game 表获取当前期号
        $game = Db::table('x_game')
            ->field('thisqishu')
            ->where('gid', $gid)
            ->find();

        if (!$game || empty($game['thisqishu'])) {
            return null;
        }

        $qishu = $game['thisqishu'];

        // 从 x_kj 表获取期号详细信息
        $kjInfo = Db::table('x_kj')
            ->field('qishu, opentime, closetime, kjtime, m1')
            ->where('gid', $gid)
            ->where('qishu', $qishu)
            ->find();

        if (!$kjInfo) {
            return [
                'qishu' => $qishu,
                'opentime' => null,
                'closetime' => null,
                'kjtime' => null,
                'is_opened' => false,
            ];
        }

        return [
            'qishu' => $kjInfo['qishu'],
            'opentime' => $kjInfo['opentime'],
            'closetime' => $kjInfo['closetime'],
            'kjtime' => $kjInfo['kjtime'],
            'is_opened' => !empty($kjInfo['m1']),  // m1不为空表示已开奖
        ];
    }

    /**
     * 更新实际开奖结果
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @param int $actualNumber 实际开出的特码
     * @return bool
     */
    public static function updateActualResult(int $gid, string $qishu, int $actualNumber): bool
    {
        try {
            // 查找记录
            $record = Db::table('x_best_plan_history')
                ->where('gid', $gid)
                ->where('qishu', $qishu)
                ->find();

            if (!$record) {
                self::setError('分析记录不存在');
                return false;
            }

            // 从JSON中解析该号码的预测利润
            $details = json_decode($record['number_details'], true);
            $actualProfit = 0;

            foreach ($details as $item) {
                if ($item['number'] == $actualNumber) {
                    $actualProfit = $item['profit'];
                    break;
                }
            }

            // 更新记录
            Db::table('x_best_plan_history')
                ->where('id', $record['id'])
                ->update([
                    'status' => 1,  // 已开奖
                    'actual_number' => $actualNumber,
                    'actual_profit' => $actualProfit,
                ]);

            return true;

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 获取投注汇总统计（按玩法分类）
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @return array
     */
    public static function getBetSummaryByPlay(int $gid, string $qishu): array
    {
        // 按玩法大类统计投注
        $summary = Db::table('x_lib')
            ->alias('l')
            ->leftJoin('x_bclass b', 'l.bid = b.bid')
            ->field('b.name as play_name, COUNT(*) as bet_count, SUM(l.je) as total_amount')
            ->where('l.gid', $gid)
            ->where('l.qishu', $qishu)
            ->where('l.z', 9)
            ->where('l.bs', 1)
            ->group('l.bid')
            ->order('total_amount', 'desc')
            ->select()
            ->toArray();

        return $summary;
    }

    /**
     * 获取号码投注分布（特码玩法）
     *
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @return array
     */
    public static function getNumberBetDistribution(int $gid, string $qishu): array
    {
        // 查询特码玩法的投注分布
        $distribution = Db::table('x_lib')
            ->alias('l')
            ->leftJoin('x_bclass b', 'l.bid = b.bid')
            ->field('l.content as number, COUNT(*) as bet_count, SUM(l.je) as total_amount')
            ->where('l.gid', $gid)
            ->where('l.qishu', $qishu)
            ->where('l.z', 9)
            ->where('l.bs', 1)
            ->whereRaw("(b.name LIKE '%特码%' OR b.name LIKE '%特碼%')")
            ->group('l.content')
            ->order('total_amount', 'desc')
            ->select()
            ->toArray();

        return $distribution;
    }
}
