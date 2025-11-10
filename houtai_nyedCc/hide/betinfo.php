<?php
include '../data/comm.inc.php';
include '../data/myadminvar.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/adminfunc.php';
include '../include.php';
include './checklogin.php';
switch ($_REQUEST['xtype']) {
    case "show":
	    $game = getgamecs($userid);
        $game = getgamename($game);
		$tpl->assign("game", $game);
        $tpl->display("betinfo.html");
        break;
	  case "period":
	    $gid = intval($_POST['gid']); 
		$msql->query("select thisqishu from `{$tb_game}` where gid='{$gid}'");
		$msql->next_record();
		$thisqishu =$msql->f('thisqishu');
		$msql->query("select js from `{$tb_kj}` where gid='{$gid}' and qishu='{$thisqishu}'");
		$msql->next_record();
		$status =$msql->f('js');
		$result = array(
    'thisqishu' => $thisqishu,
    'status' => $status
);

echo json_encode($result);
        break;
	case 'downfast':
        $layer = 0;
		$msql->query("select thisqishu from `{$tb_game}` where gid='{$gid}'");
		$msql->next_record();
		$qishu =$msql->f('thisqishu');
		//$qishu= intval($_GET['qishu']); 
		$gid = intval($_GET['gid']);
        $status = intval($_GET['s']);		
		$agentId =strtoupper($_GET['agentId']); 
        if (!preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){1,12}\$/", $agentId)) {
            $agentId = "";
        }
        $condition = "";
        if ($agentId != "") {
            $msql->query("select userid from `{$tb_user}` where username='{$agentId}'");
            $msql->next_record();
            $condition = "and userid={$msql->f("userid")}";
        } 
        $time  = "zd" . date("mdHis");
		$msql->query("select gid,gname,thisqishu,fenlei from `{$tb_game}` where gid={$gid} order by gid");
        header('Content-type: text/html; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: filename={$time}.xls");
        $td1 = '<td width=\'120\'>';
        $td2 = '</td>';
        echo '<table border=1><tr>';
        echo $td1, '序号', $td2;
        echo $td1, '彩别', $td2;
        echo $td1, '期数', $td2;
        echo $td1, '会员', $td2;
        echo $td1, '下单时间', $td2;
        echo $td1, '盘类', $td2;
        echo $td1, '类别', $td2;
        echo $td1, '内容', $td2;
        echo $td1, '金额', $td2;
        echo $td1, '赔率', $td2;
        echo $td1, '退水', $td2;
		if ($status == 1) {
		echo $td1, '中奖', $td2;
		echo $td1, '结果', $td2;
        }
		echo '</tr>';
        $i = 1;
        while ($msql->next_record()) {
            $gid   = $msql->f('gid');            
			 $fsql->query("select * from `{$tb_lib}` where gid='{$gid}' and qishu='{$qishu}' {$condition} order by time desc");
            while ($fsql->next_record()) {
                echo '<tr>';
                echo $td1, $i, $td2;
                echo $td1, $msql->f('gname'), $td2;
                echo $td1, $qishu, $td2;
                echo $td1, transu($fsql->f('userid')), $td2;
                echo $td1;
                echo substr($fsql->f('time'),-8);
                echo $td2;
                echo $td1, $fsql->f('abcd'), $td2;
                if ($tmp['b' . $fsql->f('gid') . $fsql->f('bid')] == '') {
                    $tmp['b' . $fsql->f('gid') . $fsql->f('bid')] = transb8('name', $fsql->f('bid'), $fsql->f('gid'));
                }
                if ($tmp['s' . $fsql->f('gid') . $fsql->f('sid')] == '') {
                    $tmp['s' . $fsql->f('gid') . $fsql->f('sid')] = transs8('name', $fsql->f('sid'), $fsql->f('gid'));
                }
                if ($tmp['c' . $fsql->f('gid') . $fsql->f('cid')] == '') {
                    $tmp['c' . $fsql->f('gid') . $fsql->f('cid')] = transc8('name', $fsql->f('cid'), $fsql->f('gid'));
                }
                if ($tmp['p' . $fsql->f('gid') . $fsql->f('pid')] == '') {
                    $tmp['p' . $fsql->f('gid') . $fsql->f('pid')] = transp8('name', $fsql->f('pid'), $fsql->f('gid'));
                }
                
                $wf = wfuser($msql->f("fenlei"), $tmp['b' . $fsql->f('gid') . $fsql->f('bid')], $tmp['s' . $fsql->f('gid') . $fsql->f('sid')], $tmp['c' . $fsql->f('gid') . $fsql->f('cid')], $tmp['p' . $fsql->f('gid') . $fsql->f('pid')]);
                
                echo $td1, $wf, $td2;
                echo $td1, $fsql->f('content'), $td2;
                echo $td1, $fsql->f('je'), $td2;
                echo $td1, $fsql->f('peilv1'), $td2;
				$upoints = $fsql->f('points');
            if (in_array($fsql->f('userid'), $poarr)) {
                if ($fsql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $upoints -= 10;
                }
            }
                 echo $td1, $upoints, $td2;
				if ($status == 1) {
				$uz = $fsql->f('z');
                $result = ($uz == 1) ? '中' : '不中';
                echo $td1, $result, $td2;
				if ($uz == 1) {
				$ulement = (float)($fsql->f('peilv1')*$fsql->f('je') - $fsql->f('je')*(1-$fsql->f('points')/100));
				} else {
				$ulement = (float)(0-$fsql->f('je')*(1-$fsql->f('points')/100));
				}
				echo $td1, $ulement, $td2;
				}
                echo '</tr>';
                $i++;
            }
        }
        echo '</table>';
        break;
}