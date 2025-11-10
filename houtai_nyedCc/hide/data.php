<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
$msql->query("SHOW TABLES LIKE  '%total%'");
$msql->next_record();
if ($msql->f(0) == 'x_lib_total') {
    $tb_lib = "x_lib_total";
}
switch ($_REQUEST['xtype']) {
    case "show":
	if(in_array($_REQUEST['gid'],$garr)){
	       $gid= $_REQUEST['gid'];
		}
	    $game = getgamecs($userid);
        $game = getgamename($game);
		$tpl->assign("game", $game);
		$tpl->assign("gid", $gid);
		$msql->query("select dataje,datatime,number from `{$tb_data}` where gid='{$gid}'");
        $msql->next_record();
		$dataje = $msql->f('dataje');
        $datatime = $msql->f('datatime');
		$number = $msql->f('number');
		$tpl->assign("dataje", $dataje);
		$tpl->assign("datatime", $datatime);
		$tpl->assign("number", $number);
        $tpl->display("data.html");
        break;
    case "data":
    $gid = intval($_POST['gid']);
    // 获取当天日期并设置查询时间范围
    $msql->query("select dataje,datatime,number from `{$tb_data}` where gid='{$gid}'");
    $msql->next_record();
    $dataje = $msql->f('dataje');
    $datatime = $msql->f('datatime');
    $number = $msql->f('number');
    $today = date("Y-m-d");
    $start_time = $today . " " . $datatime;

    // 准备查询语句（添加了DISTINCT关键字以获取唯一的用户）
    $sql = "SELECT DISTINCT userid, COUNT(*) as bet_count FROM $tb_lib WHERE time >= '$start_time' AND je >= $dataje AND xtype = 0 AND gid = $gid AND bid = 23378685 GROUP BY userid HAVING bet_count >= $number";

    // 执行查询
    $result = $msql->query($sql);

    // 检查结果
    if ($result->num_rows > 0) {
        // 输出结果
        $data = [];
        while($row = $result->fetch_assoc()) {
            $userid = $row['userid'];
            $bet_count = $row['bet_count'];

            // 查询真实的 username 和 name
            $user_sql = "SELECT username, name ,kmoney FROM `$tb_user` WHERE userid = $userid";
            $user_result = $msql->query($user_sql);
            if ($user_result->num_rows > 0) {
                $user_row = $user_result->fetch_assoc();
                $username = $user_row['username'];
                $name = $user_row['name'];
				$kmoney = pr1($user_row['kmoney']);

                $data[] = array('username' => $username, 'name' => $name, 'bet_count' => $bet_count, 'kmoney' => $kmoney, 'userid' => $userid);
            }
        }
        echo json_encode($data);
    } else {
        echo "0";
    }
    break;
	case "update":
    $gid = intval($_POST['gid']);
    $datatime = $_POST['datatime'];
    $dataje = floatval($_POST['dataje']);
    $number = intval($_POST['number']);
  
    // 更新 x_data 表
    $sql = "UPDATE `{$tb_data}` SET datatime = '$datatime', dataje = $dataje, number = $number WHERE gid = $gid";
    if ($msql->query($sql)) {
        echo "1";
    } else {
        echo "0";
    }
    break;
	case "details":
    $gid = intval($_POST['gid']);
    $userid = intval($_POST['userid']);
    
    // 获取 gname
    $msql->query("SELECT gname FROM `{$tb_game}` WHERE gid='{$gid}'");
    $msql->next_record();
    $gname = $msql->f('gname');
    
    // 获取当天日期并设置查询时间范围
    $msql->query("select dataje,datatime from `{$tb_data}` where gid='{$gid}'");
    $msql->next_record();
    $dataje = $msql->f('dataje');
    $datatime = $msql->f('datatime');
    $today = date("Y-m-d");
    $start_time = $today . " " . $datatime;
    
    // 准备查询语句
$sql = "SELECT tid,userid,gid, qishu, bid ,z,cid, pid, je, zc0,peilv1, points, time FROM $tb_lib WHERE userid = $userid AND time >= '$start_time' AND je >= $dataje AND xtype = 0 AND gid = $gid ORDER BY time DESC";


    // 执行查询
    $result = $msql->query($sql);

    // 检查结果
    if ($result->num_rows > 0) {
        // 输出结果
        $data = [];
        while($row = $result->fetch_assoc()) {
    $row['gname'] = $gname; // 将 gname 添加到每个结果行中
    $row['bid'] = transb8('name', $row['bid'], $row['gid']); 
	$row['cid'] = transc8('name', $row['cid'], $row['gid']); 
	$row['pid'] = transp8('name', $row['pid'], $row['gid']); 
	$row['peilv1'] = (float) $row['peilv1'];
	$row['zc'] = pr2(($row['je'])* $row['zc0'] / 100);
    $data[] = $row;
}

        echo json_encode($data);
    } else {
        echo "0";
    }
    break;
	case "cancel":
    $tid = intval($_POST['tid']);
    $gid = intval($_POST['gid']);
    $qishu = intval($_POST['qishu']);
    $userid = intval($_POST['userid']);
	$z = intval($_POST['z']);

    $sql = "UPDATE $tb_lib SET z = $z WHERE tid = $tid AND gid = $gid AND qishu = $qishu AND userid = $userid";
    $result = $msql->query($sql);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    break;
	case "toggle":
   $rowsData = json_decode(json_encode($_POST['rowsData']), true);
    $success = true;

    // 显示接收到的数据
 //   var_dump("收到的数据:");
   //var_dump($rowsData);

    foreach ($rowsData as $index => $rowData) {
        $rowsData[$index] = json_decode($rowData, true);
        $tid = intval($rowData['tid']);
        $gid = intval($rowData['gid']);
        $qishu = intval($rowData['qishu']);
        $userid = intval($rowData['userid']);
        $z = intval($rowData['z']);

        $sql = "UPDATE $tb_lib SET z = $z WHERE tid = $tid AND gid = $gid AND qishu = $qishu AND userid = $userid";
        
        // 显示执行的SQL查询
       // var_dump("执行的SQL查询:");
       // var_dump($sql);
        
        $result = $msql->query($sql);
        if (!$result) {
            $success = false;
            break;
        }
    }

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    break;










	
}
?>