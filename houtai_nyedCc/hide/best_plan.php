<?php
/**
 * 最佳控盘计划 - 后台管理页面
 * 用于查看每期的最佳开奖方案，帮助管理员制定控盘策略
 *
 * @date 2025-11-10
 */

include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../func/bestplan.class.php');

include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');

// 初始化计算器对象
$calculator = new BestPlanCalculator($msql, $redis, 300);

switch ($_REQUEST['xtype']) {
    case "show":
        // 显示最佳控盘计划列表
        $gid = isset($_GET['gid']) ? intval($_GET['gid']) : 300;

        // 获取日期筛选参数
        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : date('Y-m-d', strtotime('-7 days'));
        $end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : date('Y-m-d');

        // 分页参数
        $page_num = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $page_size = 20;
        $offset = ($page_num - 1) * $page_size;

        // 查询总数
        $count_sql = "SELECT COUNT(*) as total FROM `x_best_plan` WHERE gid=$gid
                      AND DATE(analyze_time) >= '$start_date'
                      AND DATE(analyze_time) <= '$end_date'";
        $msql->query($count_sql);
        $msql->next_record();
        $total = $msql->f('total');
        $total_pages = ceil($total / $page_size);

        // 查询列表
        $sql = "SELECT p.*, k.dates, k.endtime, k.js as is_opened, k.kj1 as result
                FROM `x_best_plan` p
                LEFT JOIN `{$tb_kj}` k ON p.qishu = k.qishu AND p.gid = k.gid
                WHERE p.gid=$gid
                AND DATE(p.analyze_time) >= '$start_date'
                AND DATE(p.analyze_time) <= '$end_date'
                ORDER BY p.qishu DESC
                LIMIT $offset, $page_size";

        $list = $msql->arr($sql, 1);

        // 处理数据
        foreach ($list as $key => $item) {
            // 计算盈利率
            if ($item['total_bet_amount'] > 0) {
                $list[$key]['max_profit_rate'] = round(($item['max_profit'] / $item['total_bet_amount']) * 100, 2);
                $list[$key]['min_profit_rate'] = round(($item['min_profit'] / $item['total_bet_amount']) * 100, 2);
            } else {
                $list[$key]['max_profit_rate'] = 0;
                $list[$key]['min_profit_rate'] = 0;
            }

            // 格式化金额
            $list[$key]['total_bet_amount_fmt'] = number_format($item['total_bet_amount'], 2);
            $list[$key]['max_profit_fmt'] = number_format($item['max_profit'], 2);
            $list[$key]['min_profit_fmt'] = number_format($item['min_profit'], 2);
            $list[$key]['profit_range_fmt'] = number_format($item['profit_range'], 2);

            // 判断是否已开奖
            $list[$key]['is_opened'] = $item['is_opened'] == 1;

            // 如果已开奖，标记实际结果是否在最佳号码中
            if ($list[$key]['is_opened'] && !empty($item['result'])) {
                $best_numbers = explode(',', $item['best_numbers']);
                $list[$key]['result_in_best'] = in_array($item['result'], $best_numbers);
            } else {
                $list[$key]['result_in_best'] = false;
            }
        }

        // 查询系统配置
        $config_sql = "SELECT * FROM `x_best_plan_config` WHERE id=1 LIMIT 1";
        $msql->query($config_sql);
        $msql->next_record();
        $config = [
            'enabled' => $msql->f('enabled'),
            'analyze_time_before' => $msql->f('analyze_time_before'),
            'analyze_depth' => $msql->f('analyze_depth'),
            'auto_analyze' => $msql->f('auto_analyze')
        ];

        // 获取当前未开奖期次
        $current_qishu_sql = "SELECT qishu, dates, endtime FROM `{$tb_kj}` WHERE gid=$gid AND js=0 ORDER BY qishu ASC LIMIT 1";
        $msql->query($current_qishu_sql);
        $current_qishu = null;
        if ($msql->next_record()) {
            $current_qishu = [
                'qishu' => $msql->f('qishu'),
                'dates' => $msql->f('dates'),
                'endtime' => $msql->f('endtime')
            ];
        }

        // 分配数据到模板
        $tpl->assign('list', $list);
        $tpl->assign('config', $config);
        $tpl->assign('current_qishu', $current_qishu);
        $tpl->assign('gid', $gid);
        $tpl->assign('start_date', $start_date);
        $tpl->assign('end_date', $end_date);
        $tpl->assign('page_num', $page_num);
        $tpl->assign('total_pages', $total_pages);
        $tpl->assign('total', $total);

        $tpl->display('best_plan.html');
        break;

    case "detail":
        // 查看某期的详细分析
        $qishu = trim($_GET['qishu']);
        $gid = isset($_GET['gid']) ? intval($_GET['gid']) : 300;

        // 获取分析结果
        $result = $calculator->getAnalyzeResult($qishu);

        if (!$result) {
            echo json_encode([
                'success' => false,
                'message' => '未找到该期次的分析数据'
            ]);
            exit;
        }

        // 获取开奖信息
        $kj_sql = "SELECT * FROM `{$tb_kj}` WHERE gid=$gid AND qishu='$qishu' LIMIT 1";
        $msql->query($kj_sql);
        $msql->next_record();
        $kj_info = [
            'qishu' => $msql->f('qishu'),
            'dates' => $msql->f('dates'),
            'endtime' => $msql->f('endtime'),
            'js' => $msql->f('js'),
            'kj1' => $msql->f('kj1')
        ];

        // 处理明细数据
        foreach ($result['details'] as $key => $item) {
            // 计算盈利率
            if ($result['total_bet_amount'] > 0) {
                $result['details'][$key]['profit_rate'] = round(($item['profit'] / $result['total_bet_amount']) * 100, 2);
            } else {
                $result['details'][$key]['profit_rate'] = 0;
            }

            // 格式化金额
            $result['details'][$key]['total_prize_fmt'] = number_format($item['total_prize'], 2);
            $result['details'][$key]['profit_fmt'] = number_format($item['profit'], 2);

            // 判断是否为最佳号码
            $best_numbers = explode(',', $result['best_numbers']);
            $result['details'][$key]['is_best'] = in_array($item['number'], $best_numbers);

            // 判断是否为最差号码
            $worst_numbers = explode(',', $result['worst_numbers']);
            $result['details'][$key]['is_worst'] = in_array($item['number'], $worst_numbers);

            // 判断是否为实际开奖号码
            if ($kj_info['js'] == 1) {
                $result['details'][$key]['is_result'] = $item['number'] == $kj_info['kj1'];
            } else {
                $result['details'][$key]['is_result'] = false;
            }
        }

        // 格式化主记录金额
        $result['total_bet_amount_fmt'] = number_format($result['total_bet_amount'], 2);
        $result['max_profit_fmt'] = number_format($result['max_profit'], 2);
        $result['min_profit_fmt'] = number_format($result['min_profit'], 2);
        $result['profit_range_fmt'] = number_format($result['profit_range'], 2);

        // 分配数据到模板
        $tpl->assign('result', $result);
        $tpl->assign('kj_info', $kj_info);
        $tpl->assign('gid', $gid);

        $tpl->display('best_plan_detail.html');
        break;

    case "analyze_now":
        // 手动触发分析
        $qishu = trim($_POST['qishu']);
        $gid = isset($_POST['gid']) ? intval($_POST['gid']) : 300;

        // 执行分析
        $result = $calculator->analyze($qishu);

        echo json_encode($result);
        break;

    case "find_by_rate":
        // 根据盈利率范围查找最佳号码
        $qishu = trim($_POST['qishu']);
        $min_rate = floatval($_POST['min_rate']);
        $max_rate = floatval($_POST['max_rate']);
        $gid = isset($_POST['gid']) ? intval($_POST['gid']) : 300;

        $matched = $calculator->findNumbersByRate($qishu, $min_rate, $max_rate);

        echo json_encode([
            'success' => true,
            'data' => $matched
        ]);
        break;

    case "config_save":
        // 保存配置
        $enabled = isset($_POST['enabled']) ? intval($_POST['enabled']) : 0;
        $analyze_time_before = isset($_POST['analyze_time_before']) ? intval($_POST['analyze_time_before']) : 5;
        $analyze_depth = isset($_POST['analyze_depth']) ? trim($_POST['analyze_depth']) : 'full';
        $auto_analyze = isset($_POST['auto_analyze']) ? intval($_POST['auto_analyze']) : 0;

        $sql = "UPDATE `x_best_plan_config` SET
                enabled='$enabled',
                analyze_time_before='$analyze_time_before',
                analyze_depth='$analyze_depth',
                auto_analyze='$auto_analyze',
                updated_at=NOW()
                WHERE id=1";

        $result = $msql->query($sql);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => '配置保存成功'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '配置保存失败'
            ]);
        }
        break;

    case "delete":
        // 删除分析记录
        $id = intval($_POST['id']);

        // 开始事务
        $msql->query("START TRANSACTION");

        try {
            // 删除主记录
            $sql1 = "DELETE FROM `x_best_plan` WHERE id=$id";
            $result1 = $msql->query($sql1);
            if (!$result1) {
                throw new Exception("删除主记录失败");
            }

            // 删除明细记录
            $sql2 = "DELETE FROM `x_best_plan_detail` WHERE plan_id=$id";
            $result2 = $msql->query($sql2);
            if (!$result2) {
                throw new Exception("删除明细记录失败");
            }

            $msql->query("COMMIT");

            echo json_encode([
                'success' => true,
                'message' => '删除成功'
            ]);
        } catch (Exception $e) {
            $msql->query("ROLLBACK");
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    default:
        // 默认跳转到 show
        header("Location: best_plan.php?xtype=show");
        break;
}
