<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

include('../data/comm.inc.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>测试基准 PID</title>
</head>
<body>
<pre>
=== 测试获取基准 PID ===

<?php
$gid = 200;
$bid = 23378685;

echo "gid: $gid\n";
echo "bid: $bid\n\n";

echo "查询该 bid 下最小的 pid:\n";
$sql = "SELECT MIN(pid) as min_pid FROM `x_play` WHERE bid=$bid AND gid=$gid";
echo "SQL: $sql\n\n";

$msql->query($sql);

if ($msql->next_record()) {
    $min_pid = $msql->f('min_pid');
    echo "结果: min_pid = $min_pid\n\n";

    if ($min_pid > 0) {
        echo "✓ 基准 pid 获取成功\n\n";

        echo "测试号码计算:\n";
        $test_pids = [23378686, 23378689, 23378690, 23378734];
        foreach ($test_pids as $pid) {
            $number = $pid - $min_pid;
            echo "  pid $pid - $min_pid = $number";
            if ($number >= 1 && $number <= 49) {
                echo " ✓ 有效号码";
            } else {
                echo " ✗ 无效";
            }
            echo "\n";
        }
    } else {
        echo "✗ min_pid 为 NULL 或 0\n";
    }
} else {
    echo "✗ 查询失败\n";
}
?>
</pre>
</body>
</html>
