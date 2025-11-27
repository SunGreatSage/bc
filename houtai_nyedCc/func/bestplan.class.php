<?php
/**
 * 最佳控盘计划 - 核心计算类
 * 计算每个开奖号码(1-49)对应的平台盈利
 *
 * @date 2025-11-10
 */
class BestPlanCalculator {

    private $msql;           // 数据库连接对象
    private $redis;          // Redis 连接对象
    private $tb_kj;          // 开奖表名
    private $tb_lib;         // 投注记录表名
    private $tb_play;        // 玩法表名
    private $tb_best_plan;   // 最佳计划表名
    private $tb_best_plan_detail; // 最佳计划明细表名
    private $tb_best_plan_config; // 最佳计划配置表名
    private $gid;            // 游戏ID（默认300=澳门六合彩）

    // 配置参数
    private $enabled = true;          // 是否启用分析
    private $analyze_time_before = 5; // 开奖前多少分钟分析（分钟）
    private $analyze_depth = 'full';  // 分析深度：full=全量, hot=热门号

    /**
     * 构造函数
     * @param object $msql 数据库连接对象
     * @param object $redis Redis 连接对象
     * @param int $gid 游戏ID（默认300）
     */
    public function __construct($msql, $redis, $gid = 300) {
        global $tb_kj, $tb_lib, $tb_play;

        $this->msql = $msql;
        $this->redis = $redis;
        $this->gid = $gid;

        // 表名
        $this->tb_kj = $tb_kj;
        $this->tb_lib = $tb_lib;
        $this->tb_play = $tb_play;
        $this->tb_best_plan = 'x_best_plan';
        $this->tb_best_plan_detail = 'x_best_plan_detail';
        $this->tb_best_plan_config = 'x_best_plan_config';

        // 加载配置
        $this->loadConfig();
    }

    /**
     * 加载系统配置
     */
    private function loadConfig() {
        $sql = "SELECT * FROM `{$this->tb_best_plan_config}` WHERE id=1 LIMIT 1";
        $result = $this->msql->query($sql);

        if ($result && $this->msql->next_record()) {
            $this->enabled = $this->msql->f('enabled') == 1;
            $this->analyze_time_before = intval($this->msql->f('analyze_time_before'));
            $this->analyze_depth = $this->msql->f('analyze_depth');
        } else {
            // 配置不存在，创建默认配置
            $this->initConfig();
        }
    }

    /**
     * 初始化默认配置
     */
    private function initConfig() {
        $sql = "INSERT INTO `{$this->tb_best_plan_config}` SET
                id=1,
                enabled=1,
                analyze_time_before=5,
                analyze_depth='full',
                auto_analyze=1,
                created_at=NOW(),
                updated_at=NOW()
                ON DUPLICATE KEY UPDATE updated_at=NOW()";

        $this->msql->query($sql);

        // 重新加载配置
        $this->enabled = true;
        $this->analyze_time_before = 5;
        $this->analyze_depth = 'full';
    }

    /**
     * 执行分析（主函数）
     * @param string $qishu 期数（可选，不传则自动获取当前未开奖期次）
     * @return array 返回分析结果
     */
    public function analyze($qishu = null) {
        // 检查是否启用
        if (!$this->enabled) {
            return ['success' => false, 'message' => '最佳控盘计划功能未启用'];
        }

        // 获取要分析的期数
        if (empty($qishu)) {
            $qishu = $this->getCurrentQishu();
            if (!$qishu) {
                return ['success' => false, 'message' => '未找到可分析的期次'];
            }
        }

        // 检查是否已经开奖
        if ($this->isOpened($qishu)) {
            return ['success' => false, 'message' => '期次 ' . $qishu . ' 已开奖，无需分析'];
        }

        // ✅ 修改：去掉分析时间限制，只要未开奖就可以分析
        // 原来的逻辑：必须在开奖前N分钟内才能分析
        // 新的逻辑：只要未开奖，任何时候都可以分析
        // if (!$this->isTimeToAnalyze($qishu)) {
        //     $time_left = $this->getTimeLeft($qishu);
        //     return ['success' => false, 'message' => '未到分析时间，距离分析还有 ' . $time_left . ' 分钟'];
        // }

        try {
            // 开始事务
            $this->msql->query("START TRANSACTION");

            // 获取本期所有投注数据
            $bets = $this->getAllBets($qishu);
            if (empty($bets)) {
                $this->msql->query("ROLLBACK");
                return ['success' => false, 'message' => '期次 ' . $qishu . ' 暂无投注数据'];
            }

            // 计算总投注额
            $total_bet_amount = $this->calculateTotalBetAmount($bets);

            // 对 1-49 每个号码进行盈利计算
            $results = [];
            for ($number = 1; $number <= 49; $number++) {
                $result = $this->calculateProfitForNumber($number, $bets, $total_bet_amount);
                $results[$number] = $result;
            }

            // 找出最大盈利和最小盈利号码
            $max_profit = max(array_column($results, 'profit'));
            $min_profit = min(array_column($results, 'profit'));

            $best_numbers = [];
            $worst_numbers = [];
            foreach ($results as $num => $data) {
                if ($data['profit'] == $max_profit) {
                    $best_numbers[] = $num;
                }
                if ($data['profit'] == $min_profit) {
                    $worst_numbers[] = $num;
                }
            }

            // 保存主记录
            $plan_id = $this->savePlanRecord($qishu, $total_bet_amount, $max_profit, $min_profit, $best_numbers, $worst_numbers);

            // 保存明细记录（49条）
            $this->savePlanDetails($plan_id, $qishu, $results);

            // 提交事务
            $this->msql->query("COMMIT");

            // 清除缓存
            $this->clearCache($qishu);

            return [
                'success' => true,
                'message' => '分析完成',
                'data' => [
                    'qishu' => $qishu,
                    'total_bet_amount' => $total_bet_amount,
                    'max_profit' => $max_profit,
                    'min_profit' => $min_profit,
                    'best_numbers' => $best_numbers,
                    'worst_numbers' => $worst_numbers,
                    'profit_range' => $max_profit - $min_profit
                ]
            ];

        } catch (Exception $e) {
            $this->msql->query("ROLLBACK");
            error_log("[BestPlanCalculator] 分析失败: " . $e->getMessage());
            return ['success' => false, 'message' => '分析失败: ' . $e->getMessage()];
        }
    }

    /**
     * 获取当前未开奖的期次（最新的未开奖期次）
     */
    private function getCurrentQishu() {
        $sql = "SELECT qishu FROM `{$this->tb_kj}` WHERE gid={$this->gid} AND js=0 ORDER BY qishu DESC LIMIT 1";
        $this->msql->query($sql);
        if ($this->msql->next_record()) {
            return $this->msql->f('qishu');
        }
        return null;
    }

    /**
     * 检查期次是否已开奖
     */
    private function isOpened($qishu) {
        $sql = "SELECT js FROM `{$this->tb_kj}` WHERE gid={$this->gid} AND qishu='$qishu' LIMIT 1";
        $this->msql->query($sql);
        if ($this->msql->next_record()) {
            return $this->msql->f('js') == 1;
        }
        return false;
    }

    /**
     * 检查是否可以进行分析
     * ✅ 修改：去掉时间限制，只要未开奖就可以分析
     * 原逻辑：必须在开奖前N分钟内才能分析
     * 新逻辑：只要未开奖（kjtime > 当前时间），就可以分析
     */
    private function isTimeToAnalyze($qishu) {
        $sql = "SELECT kjtime FROM `{$this->tb_kj}` WHERE gid={$this->gid} AND qishu='$qishu' LIMIT 1";
        $this->msql->query($sql);
        if ($this->msql->next_record()) {
            $kjtime = $this->msql->f('kjtime');
            $open_timestamp = strtotime($kjtime);
            $now_timestamp = time();

            // 只要当前时间小于开奖时间（未开奖），就可以分析
            return $now_timestamp < $open_timestamp;
        }
        return false;
    }

    /**
     * 获取距离分析时间还有多少分钟
     */
    private function getTimeLeft($qishu) {
        $sql = "SELECT dates, closetime, kjtime FROM `{$this->tb_kj}` WHERE gid={$this->gid} AND qishu='$qishu' LIMIT 1";
        $this->msql->query($sql);
        if ($this->msql->next_record()) {
            $dates = $this->msql->f('dates');
            $closetime = $this->msql->f('closetime');
            $kjtime = $this->msql->f('kjtime');
            $open_timestamp = strtotime($kjtime);
            $analyze_timestamp = $open_timestamp - ($this->analyze_time_before * 60);
            $time_left_seconds = $analyze_timestamp - time();
            return max(0, ceil($time_left_seconds / 60));
        }
        return 0;
    }

    /**
     * 获取指定期次的所有投注数据
     * ✅ 修改：去掉 z 字段限制，因为在 analyze() 函数开头已经检查了是否开奖
     * 原因：可能存在数据不一致的情况（开奖表未开奖但投注表 z=9）
     * z 字段含义：0=未开奖, 1=中奖, 9=未中奖
     */
    private function getAllBets($qishu) {
        $sql = "SELECT * FROM `{$this->tb_lib}` WHERE gid={$this->gid} AND qishu='$qishu'";
        return $this->msql->arr($sql, 1);
    }

    /**
     * 计算总投注额
     */
    private function calculateTotalBetAmount($bets) {
        $total = 0;
        foreach ($bets as $bet) {
            $total += floatval($bet['je']);
        }
        return $total;
    }

    /**
     * 计算指定号码的平台盈利
     * @param int $number 开奖号码（1-49）
     * @param array $bets 所有投注数据
     * @param float $total_bet_amount 总投注额
     * @return array
     */
    private function calculateProfitForNumber($number, $bets, $total_bet_amount) {
        $total_prize = 0;  // 该号码开出时的总派奖金额
        $win_count = 0;    // 中奖注单数

        foreach ($bets as $bet) {
            // 判断这个号码是否会让该注单中奖
            if ($this->checkIfWin($number, $bet)) {
                // 中奖金额 = 投注金额 * 赔率
                $prize = floatval($bet['je']) * floatval($bet['peilv1']);
                $total_prize += $prize;
                $win_count++;
            }
        }

        // 平台盈利 = 总投注额 - 总派奖额
        $profit = $total_bet_amount - $total_prize;

        return [
            'number' => $number,
            'total_prize' => round($total_prize, 2),
            'profit' => round($profit, 2),
            'win_count' => $win_count
        ];
    }

    /**
     * 判断指定号码是否会让该注单中奖
     * ✅ 修改：基于 pid 差值计算号码（规律：号码 = pid - 基准pid）
     * @param int $number 开奖号码
     * @param array $bet 注单数据
     * @return bool
     */
    private function checkIfWin($number, $bet) {
        $pid = intval($bet['pid']);
        $bid = intval($bet['bid']);

        // 获取该游戏的基准 pid（通过 bid 查询最小的 pid）
        $base_pid = $this->getBasePid($bid);

        if ($base_pid > 0) {
            // 计算号码：号码 = pid - 基准pid + 1
            // 例如：基准pid=23378685, pid=23378686 → 号码1
            //       基准pid=23378685, pid=23378689 → 号码4
            $play_number = $pid - $base_pid;

            // 如果计算出的号码在 1-49 范围内，则判断是否中奖
            if ($play_number >= 1 && $play_number <= 49) {
                return $play_number == $number;
            }
        }

        return false;
    }

    /**
     * 获取指定 bid 的基准 pid
     * @param int $bid 大盘分类ID
     * @return int 基准 pid
     */
    private function getBasePid($bid) {
        static $cache = [];

        if (isset($cache[$bid])) {
            return $cache[$bid];
        }

        // 查询该 bid 下最小的 pid 作为基准
        $sql = "SELECT MIN(pid) as min_pid FROM `{$this->tb_play}` WHERE bid=$bid AND gid={$this->gid}";
        $this->msql->query($sql);

        if ($this->msql->next_record()) {
            $min_pid = intval($this->msql->f('min_pid'));
            $cache[$bid] = $min_pid;
            return $min_pid;
        }

        $cache[$bid] = 0;
        return 0;
    }

    /**
     * 判断特码是否中奖
     */
    private function checkTeWin($number, $content, $pid) {
        // 特码直选：内容为具体号码，如 "01,05,12"
        $numbers = explode(',', $content);
        $numbers = array_map(function($n) { return intval(trim($n)); }, $numbers);

        if (in_array($number, $numbers)) {
            return true;
        }

        // 特码大小：大=25-49，小=1-24
        if ($content == '大' && $number >= 25) return true;
        if ($content == '小' && $number <= 24) return true;

        // 特码单双
        if ($content == '单' && $number % 2 == 1) return true;
        if ($content == '双' && $number % 2 == 0) return true;

        // 特码合单双（号码各位数之和的单双）
        if ($content == '合单' && (floor($number / 10) + $number % 10) % 2 == 1) return true;
        if ($content == '合双' && (floor($number / 10) + $number % 10) % 2 == 0) return true;

        return false;
    }

    /**
     * 判断正码是否中奖（需要模拟6个正码位置）
     * 注意：这里简化处理，只判断特码位置，完整实现需要模拟整个开奖结果
     */
    private function checkZhengWin($number, $content, $pid) {
        $numbers = explode(',', $content);
        $numbers = array_map(function($n) { return intval(trim($n)); }, $numbers);
        return in_array($number, $numbers);
    }

    /**
     * 判断波色是否中奖
     * 红波：01,02,07,08,12,13,18,19,23,24,29,30,34,35,40,45,46
     * 蓝波：03,04,09,10,14,15,20,25,26,31,36,37,41,42,47,48
     * 绿波：05,06,11,16,17,21,22,27,28,32,33,38,39,43,44,49
     */
    private function checkBoSeWin($number, $content, $pid) {
        $red = [1,2,7,8,12,13,18,19,23,24,29,30,34,35,40,45,46];
        $blue = [3,4,9,10,14,15,20,25,26,31,36,37,41,42,47,48];
        $green = [5,6,11,16,17,21,22,27,28,32,33,38,39,43,44,49];

        if ($content == '红波' && in_array($number, $red)) return true;
        if ($content == '蓝波' && in_array($number, $blue)) return true;
        if ($content == '绿波' && in_array($number, $green)) return true;

        return false;
    }

    /**
     * 判断生肖是否中奖
     * 生肖对照表（2024年为龙年）
     */
    private function checkShengXiaoWin($number, $content, $pid) {
        // 生肖号码对照表（根据当前年份计算）
        $year = intval(date('Y'));
        $base_year = 2024;  // 龙年
        $offset = ($year - $base_year) % 12;

        // 2024年生肖对照表（龙年）
        $shengxiao_map = [
            '鼠' => [4,16,28,40],
            '牛' => [3,15,27,39],
            '虎' => [2,14,26,38],
            '兔' => [1,13,25,37,49],
            '龙' => [12,24,36,48],
            '蛇' => [11,23,35,47],
            '马' => [10,22,34,46],
            '羊' => [9,21,33,45],
            '猴' => [8,20,32,44],
            '鸡' => [7,19,31,43],
            '狗' => [6,18,30,42],
            '猪' => [5,17,29,41]
        ];

        // 根据年份偏移调整生肖
        // 这里简化处理，实际应该根据当前年份动态计算

        if (isset($shengxiao_map[$content]) && in_array($number, $shengxiao_map[$content])) {
            return true;
        }

        return false;
    }

    /**
     * 保存主记录
     */
    private function savePlanRecord($qishu, $total_bet_amount, $max_profit, $min_profit, $best_numbers, $worst_numbers) {
        // 先删除旧记录（如果有）
        $sql_delete = "DELETE FROM `{$this->tb_best_plan}` WHERE qishu='$qishu' AND gid={$this->gid}";
        $this->msql->query($sql_delete);

        // 插入新记录
        $best_numbers_str = implode(',', $best_numbers);
        $worst_numbers_str = implode(',', $worst_numbers);
        $analyze_time = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `{$this->tb_best_plan}` SET
                gid={$this->gid},
                qishu='$qishu',
                total_bet_amount='$total_bet_amount',
                max_profit='$max_profit',
                min_profit='$min_profit',
                profit_range='" . ($max_profit - $min_profit) . "',
                best_numbers='$best_numbers_str',
                worst_numbers='$worst_numbers_str',
                analyze_time='$analyze_time',
                created_at=NOW()";

        $result = $this->msql->query($sql);
        if (!$result) {
            throw new Exception("保存主记录失败");
        }

        return $this->msql->last_id();
    }

    /**
     * 保存明细记录（49条）
     */
    private function savePlanDetails($plan_id, $qishu, $results) {
        // 先删除旧记录
        $sql_delete = "DELETE FROM `{$this->tb_best_plan_detail}` WHERE plan_id='$plan_id'";
        $this->msql->query($sql_delete);

        // 批量插入
        foreach ($results as $number => $data) {
            $sql = "INSERT INTO `{$this->tb_best_plan_detail}` SET
                    plan_id='$plan_id',
                    qishu='$qishu',
                    gid={$this->gid},
                    number='$number',
                    total_prize='{$data['total_prize']}',
                    profit='{$data['profit']}',
                    win_count='{$data['win_count']}',
                    created_at=NOW()";

            $result = $this->msql->query($sql);
            if (!$result) {
                throw new Exception("保存明细记录失败，号码: $number");
            }
        }
    }

    /**
     * 清除缓存
     */
    private function clearCache($qishu) {
        try {
            // 清除控盘相关缓存
            $keys = $this->redis->keys("caopan:{$this->gid}:*");
            if (!empty($keys)) {
                $this->redis->del($keys);
            }

            // 清除期次相关缓存
            $keys2 = $this->redis->keys("bestplan:{$this->gid}:{$qishu}*");
            if (!empty($keys2)) {
                $this->redis->del($keys2);
            }
        } catch (Exception $e) {
            error_log("[BestPlanCalculator] 清除缓存失败: " . $e->getMessage());
        }
    }

    /**
     * 获取指定期次的分析结果
     */
    public function getAnalyzeResult($qishu) {
        $sql = "SELECT * FROM `{$this->tb_best_plan}` WHERE gid={$this->gid} AND qishu='$qishu' LIMIT 1";
        $result = $this->msql->arr($sql, 1);

        if (empty($result)) {
            return null;
        }

        $plan = $result[0];

        // 获取明细
        $sql_detail = "SELECT * FROM `{$this->tb_best_plan_detail}` WHERE plan_id='{$plan['id']}' ORDER BY number ASC";
        $details = $this->msql->arr($sql_detail, 1);

        $plan['details'] = $details;

        return $plan;
    }

    /**
     * 根据赔率范围查找最佳开奖号码
     * @param string $qishu 期数
     * @param float $min_rate 最小赔率（如 30）
     * @param float $max_rate 最大赔率（如 50）
     * @return array 符合条件的号码列表
     */
    public function findNumbersByRate($qishu, $min_rate, $max_rate) {
        $result = $this->getAnalyzeResult($qishu);
        if (!$result) {
            return [];
        }

        $total_bet = $result['total_bet_amount'];
        if ($total_bet <= 0) {
            return [];
        }

        $matched = [];
        foreach ($result['details'] as $detail) {
            // 计算盈利率 = (平台盈利 / 总投注额) * 100
            $rate = ($detail['profit'] / $total_bet) * 100;

            if ($rate >= $min_rate && $rate <= $max_rate) {
                $matched[] = [
                    'number' => $detail['number'],
                    'profit' => $detail['profit'],
                    'rate' => round($rate, 2)
                ];
            }
        }

        return $matched;
    }
}
