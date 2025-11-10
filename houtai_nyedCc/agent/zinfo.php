<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
     case "show":
        $msql->query("select ljs,fid1 from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $fid1 = $msql->f('fid1');
        $ljs  = $msql->f('ljs');
        if ($thelayer > 1) {
            $msql->query("select ljs from `$tb_user` where userid='$fid1'");
            $msql->next_record();
            $ljs = $msql->f('ljs');
        }
        $qishu = array();
        $msql->query("select qishu from `$tb_kj` where gid='$gid' and baostatus=1 and js=1   order by qishu desc limit 120");
        $i = 0;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        $tpl->assign("qishu", $qishu);
        if (is_numeric($_REQUEST['qishu'])) {
            $q = $_REQUEST['qishu'];
        } else {
            $q = $qishu[0];
        }
        if ($ljs == 1) {
            $msql->query("select userid,content,prize,z1 from `$tb_lib` where gid='$gid' and qishu='$q' and uid1='$fid1' and z1=1");
        } else {
            $msql->query("select userid,content,prize,z1 from `$tb_lib` where gid='$gid' and qishu='$q' and z1=1");
        }
        $i = 0;
        while ($msql->next_record()) {
            $ydeng[$i]['con']   = $msql->f('content');
            $ydeng[$i]['prize'] = $msql->f('prize');
			$edeng[$i]['z1'] = $msql->f('z1');
            $ydeng[$i]['user']  = transuser($msql->f('userid'), 'username');
            $i++;
        }
        if ($ljs == 1) {
            $msql->query("select userid,content,prize,z2 from `$tb_lib` where gid='$gid' and qishu='$q' and uid1='$fid1' and z2>0");
        } else {
            $msql->query("select userid,content,prize,z2 from `$tb_lib` where gid='$gid' and qishu='$q' and z2>0");
        }
        $i = 0;
        while ($msql->next_record()) {
            $edeng[$i]['con']   = $msql->f('content');
            $edeng[$i]['prize'] = $msql->f('prize');
			$edeng[$i]['z2'] = $msql->f('z2');
            $edeng[$i]['user']  = transuser($msql->f('userid'), 'username');
            $i++;
        }
		$tpl->assign('q', $q);
        $tpl->assign('ydeng', $ydeng);
        $tpl->assign('edeng', $edeng);
        $tpl->display($mobi . "zinfo.html");
	 break;
}
?>