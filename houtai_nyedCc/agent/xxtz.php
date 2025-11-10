<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');

switch ($_REQUEST['xtype']) {
    case 'show':
        $qishu = array();
        $qishu[0] = $config['thisqishu'];
        $time = strtotime(date('Y-m-d ') . '03:00:01');
        $msql->query("select qishu from `{$tb_kj}` where gid='{$gid}' and m1!='' and baostatus=1   order by qishu desc");
        //and kjtime>$time
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        if ($config['fenlei'] != 'loto') {
            $qishu = fast3qishu($qishu);
        }
        $tpl->assign('qishu', $qishu);
        $tpl->assign('b', getb());
        $tpl->assign('topid', $userid);
        $tpl->assign('layer', transuser($userid, 'layer'));
        $tpl->assign('username', transuser($userid, 'username'));
        $tpl->assign('layername', $config['layer']);
        $tpl->assign('maxlayer', $config['maxlayer']);
        $tpl->assign('topuser', topuser($userid));
        $sdate = week();
        $tpl->assign('sdate', $sdate);
        $tpl->display('xxtz.html');
        break;
    case 'getb':
        $gid = $_POST['gid'];
	
        $msql->query("select * from `{$tb_bclass}` where gid='{$gid}' and ifok=1 order by xsort ");
        $b = array();
        $i = 0;
        while ($msql->next_record()) {
            $b[$i]['bid'] = $msql->f('bid');
            $b[$i]['name'] = $msql->f('name');
            $b[$i]['ifok'] = $msql->f('ifok');
            $b[$i]['xsort'] = $msql->f('xsort');
            $i++;
        }
        echo json_encode($b);
        break;
    case 'gets':
        if ($_POST['gid'] != '') {
            $gid = $_POST['gid'];
        }
        $bid = $_POST['bid'];
        $msql->query("select * from `{$tb_sclass}` where gid='{$gid}' and bid='{$bid}'  order by bid,xsort");
        $s = array();
        $i = 0;
        while ($msql->next_record()) {
            $s[$i]['sid'] = $msql->f('sid');
            $s[$i]['name'] = $msql->f('name');
            $s[$i]['ifok'] = $msql->f('ifok');
            $s[$i]['xsort'] = $msql->f('xsort');
            $i++;
        }
        echo json_encode($s);
        break;
    case 'getc':
        if ($_POST['gid'] != '') {
            $gid = $_POST['gid'];
        }
        $bid = $_POST['bid'];
        $sid = $_POST['sid'];
        $msql->query("select * from `{$tb_class}`  where gid='{$gid}' and bid='{$bid}' and sid='{$sid}' order by bid,sid,xsort ");
        $i = 0;
        $c = array();
        while ($msql->next_record()) {
            $c[$i]['cid'] = $msql->f('cid');
            $c[$i]['name'] = $msql->f('name');
            $i++;
        }
        echo json_encode($c);
        unset($c);
        break;
    case 'getuser':
        $bid = $_POST['bid'];
        $cid = $_POST['cid'];
        $sid = $_POST['sid'];
        if ($config['fenlei'] != 'loto') {
            $qishu = explode('~', $_POST['qishu']);
            $q1 = trim($qishu[0]);
            $q2 = trim($qishu[1]);
        } else {
            $q1 = $_POST['qishu'];
            $q2 = $_POST['qishu'];
        }
        $uid = $_POST['uid'];
        $fs = $_POST['fs'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        if ($fs == 1) {
            $whi = " and B.gid='{$gid}' and B.qishu>={$q2} and B.qishu<={$q1} and B.baostatus=1 ";
        } else {

            $whi = " and B.gid='{$gid}' and B.dates>='$start' and B.dates<='$end' and B.baostatus=1 ";
        }
        $yq = ' and A.xtype!=2 ';
        if ($bid != '' & $bid != 'null') {
            $yq .= " and A.bid='{$bid}' ";
        }
        if ($sid != '' & $sid != 'null') {
            $yq .= " and A.sid='{$sid}' ";
        }
        if ($cid != '' & $cid != 'null') {
            $yq .= " and A.cid='{$cid}' ";
        }
        $join = " from `{$tb_lib}` as A  Left join `{$tb_kj}` as B on A.qishu=B.qishu and A.gid=B.gid where 1=1 {$yq} {$whi} ";
        $user = topuser($uid);
        $layer = transuser($uid, 'layer');
        $cu = count($user);
        for ($i = 0; $i < $cu; $i++) {
            if ($user[$i]['ifagent'] == 0) {
                //$user[$i]['layer']==5
                $msql->query("select count(A.id) {$join} and  userid='" . $user[$i]['userid'] . '\'');
            } else {
                $msql->query("select count(A.id) {$join} and (A.uid" . $user[$i]['layer'] . '=\'' . $user[$i]['userid'] . '\' or A.userid=\'' . $user[$i]['userid'] . '\')');
            }
            $msql->next_record();
            if ($msql->f(0) == 0) {
                unset($user[$i]);
                continue;
            }
            if ($user[$i]['ifagent'] == 0) {
                //$user[$i]['layer']==5
                $sql = "select sum(A.je),count(A.id) {$join} and  A.userid='" . $user[$i]['userid'] . '\'';
                $msql->query($sql);
                $msql->next_record();
                $user[$i]['z'][0]['uje'] = pr2($msql->f(0));
                $user[$i]['z'][0]['uzs'] = pr2($msql->f(1));
                $user[$i]['z'][0]['cje'] = 0;
                $user[$i]['z'][0]['czs'] = 0;
                $tmp = $sql;
                $user[$i]['z'][0]['layer'] = $user[$i]['layer'];
                continue;
            }
            $user[$i]['z'] = array();
            $p = 0;
            $zcstr = '';
            for ($k = $config['maxlayer'] - 1; $k >= $user[$i]['layer']; $k--) {
                $zcstr .= '-A.zc' . $k;
            }
            //$ugroup = "|".$user[$i]['userid']."|";
            $ugroup = $user[$i]['userid'];
            $sql = 'select sum((100' . $zcstr . ")*je/100),count(A.id) {$join} and ";
            $sql .= ' ( A.uid' . $user[$i]['layer'] . '=\'' . $ugroup . '\' or A.userid=\'' . $ugroup . '\')';
            $msql->query($sql);
            $msql->next_record();
            $user[$i]['z'][$p]['layer'] = $user[$i]['layer'];
            $user[$i]['z'][$p]['uje'] = pr2($msql->f(0));
            $user[$i]['z'][$p]['uzs'] = pr2($msql->f(1));
            $zcstr2 = 'A.zc' . $user[$i]['layer'];
            $sql = "select sum({$zcstr2}*A.je/100),count(A.id) {$join} and ";
            $sql .= '  ( A.uid' . $user[$i]['layer'] . '=\'' . $ugroup . '\') ';
            $msql->query($sql);
            $msql->next_record();
            $user[$i]['z'][$p]['cje'] = pr2($msql->f(0));
            $user[$i]['z'][$p]['czs'] = pr2($msql->f(1));
            for ($j = $user[$i]['layer'] + 1, $p = 1; $j <= $config['maxlayer']; $j++, $p++) {
                $ugroup = getusergroup3($user[$i]['userid'], $j);
                if ($ugroup == '') {
                    $ugroup = 11111111;
                }
                if ($j == $config['maxlayer']) {
                    if ($ugroup == '') {
                        $user[$i]['z'][$p]['uje'] = 0;
                        $user[$i]['z'][$p]['uzs'] = 0;
                        $user[$i]['z'][$p]['layer'] = $config['maxlayer'];
                        continue;
                    }
                    $sql = "select sum(A.je),count(A.id) {$join} and  A.userid in ({$ugroup})";
                    $msql->query($sql);
                    $tmp = $sql;
                    $msql->next_record();
                    $user[$i]['z'][$p]['uje'] = pr2($msql->f(0));
                    $user[$i]['z'][$p]['uzs'] = pr2($msql->f(1));
                    $user[$i]['z'][$p]['layer'] = $j;
                } else {
                    $zcstr = '';
                    for ($k = $config['maxlayer'] - 1; $k >= $j; $k--) {
                        $zcstr .= '-zc' . $k;
                    }
                    $sql = 'select sum((100' . $zcstr . ")*A.je/100),count(A.id) {$join} and ";
                    $sql .= ' ( A.uid' . $j . " in ({$ugroup}) or A.userid in ({$ugroup}))";
                    //if($j==3) echo $sql;
                    $msql->query($sql);
                    $msql->next_record();
                    $user[$i]['z'][$p]['layer'] = $j;
                    $user[$i]['z'][$p]['uje'] = pr2($msql->f(0));
                    $user[$i]['z'][$p]['uzs'] = pr2($msql->f(1));
                    $zcstr2 = 'zc' . $j;
                    $sql = "select sum({$zcstr2}*A.je/100),count(A.id) {$join} and ";
                    $sql .= '  A.uid' . $j . " in ({$ugroup}) ";
                    $msql->query($sql);
                    $msql->next_record();
                    $user[$i]['z'][$p]['cje'] = pr2($msql->f(0));
                    $user[$i]['z'][$p]['czs'] = pr2($msql->f(1));
                }
            }
        }
        if ($user == null) {
            $user = array();
        }
        foreach ($user as $tmp) {
            $mm[] = $tmp['ifagent'];
        }
        array_multisort($mm, SORT_DESC, SORT_NUMERIC, $user);
        //print_r($user);
        //$tmp=ob_get_contents();
        //ob_end_clean();
        $e = array('u' => $user, 'm' => $tmp);
        echo json_encode($e);
        unset($e);
        break;
    case 'gettzxx':
        $bid = $_POST['bid'];
        $cid = $_POST['cid'];
        $sid = $_POST['sid'];
        if ($config['fenlei'] != 'loto') {
            $qishu = explode('~', $_POST['qishu']);
            $q1 = trim($qishu[0]);
            $q2 = trim($qishu[1]);
        } else {
            $q1 = $_POST['qishu'];
            $q2 = $_POST['qishu'];
        }
        $uid = $_POST['uid'];


        $page = $_POST['page'];
        if (!is_numeric($page)) {
            $page = 1;
        }
        $psize = 100;
        $fs = $_POST['fs'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        if ($fs == 1) {
            $whi = " and B.gid='{$gid}' and B.qishu>={$q2} and B.qishu<={$q1} and B.baostatus=1 ";
        } else {
            $start = strtotime($start . ' 06:00:00');
            $end = strtotime($end . ' 05:00:00') + 86400;
            $whi = " and B.gid='{$gid}' and B.kjtime>={$start} and B.kjtime<={$end} and B.baostatus=1 ";
        }
        $yq = '  ';
        if ($bid != '' & $bid != 'null') {
            $yq .= " and A.bid='{$bid}' ";
        }
        if ($sid != '' & $sid != 'null') {
            $yq .= " and A.sid='{$sid}' ";
        }
        if ($cid != '' & $cid != 'null') {
            $yq .= " and A.cid='{$cid}' ";
        }
        $join = " from `{$tb_lib}` as A  Left join `{$tb_kj}` as B on A.qishu=B.qishu and A.gid=B.gid where 1=1 {$yq} {$whi} ";
        $sql = " select count(A.id) {$join} and A.userid='{$uid}'";
        $msql->query($sql);
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : ($rcount - $rcount % $psize) / $psize + 1;
        $pstr = page($pcount, $page);
        $sql = " select A.* {$join} and A.userid='{$uid}' order by A.xtype,A.time desc,A.id desc ";
        $sql .= ' limit ' . ($page - 1) * $psize . ',' . $psize;
        $msql->query($sql);
        $tz = array();
        $i = 0;
		$tmp=array();
        while ($msql->next_record()) {
/***********HELLO*******/
			if($tmp['jj'.$msql->f('userid')]=='' &  in_array($msql->f('userid'),$jkarr)){
				$fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='gettzxx".$_SESSION['hides']."',time=NOW(),jkuser='".$msql->f('userid')."',qishu='".$msql->f('qishu')."'");
				  $tmp['jj'.$msql->f('userid')]=1;
            }
			/***********HELLO*******/
            $tz[$i]['xtype'] = transxtype($msql->f('xtype'));
            $tz[$i]['tid'] = $msql->f('tid');
            $tz[$i]['qishu'] = $msql->f('qishu');
            $tz[$i]['je'] = (double) $msql->f('je');
            if ($uid == $msql->f('userid')) {
                $tz[$i]['me'] = 1;
            }
            $tz[$i]['zcje'] = pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (double) $msql->f('peilv1');
            if ($msql->f('peilv2') > 1) {
                $tz[$i]['peilv1'] .= '/' . (double) $msql->f('peilv2');
            }
            $tz[$i]['points'] = (double) $msql->f('points');
            $tz[$i]['con'] = $msql->f('content');
            $tz[$i]['bid'] = transb('name', $msql->f('bid'));
            $tz[$i]['sid'] = transs('name', $msql->f('sid'));
            $tz[$i]['cid'] = transc('name', $msql->f('cid'));
            if ($tz[$i]['sid'] == $tz[$i]['bid']) {
                $tz[$i]['sid'] = '';
            }
            if ($tz[$i]['cid'] == $tz[$i]['sid']) {
                $tz[$i]['cid'] = '';
            }
            $tz[$i]['pid'] = transp('name', $msql->f('pid'));
            $tz[$i]['time'] = $msql->f('time');
            $tz[$i]['xtime'] = $msql->f('time');
            $tz[$i]['user'] = transu($msql->f('userid'));
            if ($layer < $config['maxlayer']) {
                if ($msql->f('uid' . ($layer + 1)) == 0) {
                    $tz[$i]['duser'] = transu($msql->f('userid'));
                } else {
                    $tz[$i]['duser'] = transu($msql->f('uid' . ($layer + 1)));
                }
            }
            //$tz[$i]['xtype'] = $msql->f('xtype');
            for ($j = 0; $j < $config['maxlayer']; $j++) {
                $tz[$i]['zc' . $j] = pr2($msql->f('je') * $msql->f('zc' . $j) / 100);
                if ($j != 0) {
                    $tz[$i]['points' . $j] = (double) $msql->f('points' . $j);
                    $tz[$i]['peilv1' . $j] = (double) $msql->f('peilv1' . $j);
                    if ($msql->f('peilv2' . $j) > 1) {
                        $tz[$i]['peilv1' . $j] .= '/' . (double) $msql->f('peilv2' . $j);
                    }
                }
            }
            if (strpos('|A|B|C|D', $msql->f('abcd'))) {
                $tz[$i]['abcd'] = $msql->f('abcd');
            } else {
                $tz[$i]['abcd'] = '';
            }
            if (strpos('|A|B|', $msql->f('ab'))) {
                $tz[$i]['ab'] = $msql->f('ab');
            } else {
                $tz[$i]['ab'] = '';
            }
            $i++;
        }
        $e = array('tz' => $tz, 'page' => $pstr, 'sql' => $sql, 'layer' => $layer);
        echo json_encode($e);
        unset($e);
        break;
    case 'getfid':
        $uid = $_POST['uid'];
        $msql->query("select fid from `{$tb_user}` where userid='{$uid}'");
        $msql->next_record();
        if ($msql->f('fid') == 99999999) {
            $user = array('uid' => 99999999, 'layer' => 0);
            echo json_encode($user);
            die;
        }
        $msql->query("select layer,userid,username,name from `{$tb_user}` where userid=(select fid from `{$tb_user}` where userid='{$uid}')");
        $msql->next_record();
        if ($msql->f('layer') == '') {
            $tmp = array('err' => 1);
            echo json_encode($tmp);
            die;
        }
        $user = array('uid' => $msql->f('userid'), 'layer' => $msql->f('layer'), 'name' => $msql->f('username') . '[' . $msql->f('name') . '][' . $config['layer'][$msql->f('layer') - 1] . ']', 'err' => 0);
        /*		$tmp='level'.$msql->f('layer');
        		$name = $msql->f('username').'['.$msql->f('name').']['.$$tmp.']'
        		$user=array("uid"=>$msql->f('userid'),"layer"=>$msql->f('layer'),"name"=>$name,"err"=>0);*/
        echo json_encode($user);
        unset($user);
        break;

}
function getusergroup3($uid, $layer)
{
    global $tb_user, $tsql;
    $str = $uid;
    $melayer = transuser($uid, 'layer') + 1;
    for ($i = $melayer; $i <= $layer; $i++) {
        $tsql->query("select userid from `{$tb_user}` where fid in ({$str}) and layer='{$i}'");
        while ($tsql->next_record()) {
            if ($tsql->f('userid') != '') {
                if ($i == $layer) {
                    $xout .= ',' . $tsql->f('userid');
                } else {
                    if (!strpos($str, $tsql->f('userid'))) {
                        $str .= ',' . $tsql->f('userid');
                    }
                }
            }
        }
    }
    return substr($xout, 1);
}