<?php

include '../data/comm.inc.php';
include '../data/myadminvar.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/adminfunc.php';
include '../include.php';
include './checklogin.php';
$msql->query("SHOW TABLES LIKE  '%total%'");
$msql->next_record();
if ($msql->f(0) == 'x_lib_total') {
    $tb_lib = "x_lib_total";
}
switch ($_REQUEST['xtype']) {
    case "show":
	if ($_SESSION['hides'] == 1) {
            $tpl->assign('hides', 1);
        }
        if ($_SESSION['hide'] == 1) {
            $msql->query("select * from `$tb_admins_page` where adminid=10000 group by xpage");
            $tpl->assign('hide', 1);
        } else {
            $msql->query("select * from `$tb_admins_page` where adminid='$adminid' and ifok=1 group by xpage");
        }
        while ($msql->next_record()) {
            $tpl->assign($msql->f('xpage'), 1);
        }
        //error_reporting(E_ALL);
        $qishu = array();
        $qishu[0] = $config['thisqishu'];
        $msql->query("select qishu from `{$tb_kj}` where gid='{$gid}' and m1!=''   order by kjtime desc");
        //and kjtime>$time
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        $qishu = fast3qishu($qishu);
        $tpl->assign('qishu', $qishu);
        $tpl->assign("b", getb());
        $tpl->assign("topid", $userid);
        $tpl->assign("layer", transuser($userid, 'layer'));
        $tpl->assign("username", transuser($userid, 'username'));
        $msql->query("select wid,layer,namehead from `{$tb_web}` order by wid");
        $i = 0;
        while ($msql->next_record()) {
            $layer[$i]['wid'] = $msql->f('wid');
            $layer[$i]['layer'] = json_decode($msql->f('layer'), true);
            $namehead = json_decode($msql->f('namehead'), true);
            $layer[$i]['namehead'] = $namehead[0];
            $i++;
        }
        $tpl->assign("layername", $layer);
        $tpl->assign("topuser", topuser($userid));
        $sdate = week();
        $tpl->assign("sdate", $sdate);
		$tpl->assign("changeorder", $changeorder);
        $tpl->display("xxtz2.html");
        break;
    case "gettzxx":
        $page = rpage($_POST['page']);
        $psize = $config['psize3'];
        $start = rdates($_POST['start']);
        $end = rdates($_POST['end']);
        $username = strtoupper($_POST["uid"]);
        if (!preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){1,12}\$/", $username)) {
            $username = "";
        }
        $uid = "";
        if ($username != "") {
            $msql->query("select userid from `{$tb_user}` where username='{$username}'");
            $msql->next_record();
            $uid = $msql->f("userid");
        }
        if ($start == $end) {
            $whi = " dates='" . $start . "' ";
        } else {
            $whi = " dates>='" . $start . "' and dates<='" . $end . "' ";
        }
        $username != "" && ($whi .= " and userid='{$uid}' ");
        $join = " from `{$tb_lib}` where {$whi} ";
        $sql = " select count(id) {$join} ";
        $msql->query($sql);
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : ($rcount - $rcount % $psize) / $psize + 1;
        $pstr = page($pcount, $page);
        $sql = " select * {$join} order by time desc,id desc ";
        $sql .= " limit " . ($page - 1) * $psize . "," . $psize;
        $msql->query($sql);
        $tz = array();
        $i = 0;
        $tmp = [];
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='{$gid}',userid='{$userid}',downtype='xxtz2_".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu='" . $msql->f('qishu') . "'");
                $tmp['jj' . $msql->f('userid')] = 1;
            }
            /***********HELLO*******/
            $tz[$i]['xtype'] = transxtype($msql->f('xtype'));
            $tz[$i]['id'] = $msql->f('id');
            $tz[$i]['tid'] = $msql->f('tid');
            $tz[$i]['userid'] = $msql->f('userid');
            $tz[$i]['qishu'] = $msql->f('year') . $msql->f('qishu');
            $tz[$i]['je'] = (double) $msql->f('je');
            if ($uid == $msql->f('userid')) {
                $tz[$i]['me'] = 1;
            }
            $tz[$i]['zcje'] = pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (double) $msql->f('peilv1');
            $tz[$i]['peilv2'] = (double) $msql->f('peilv2');
            $tz[$i]['points'] = (double) $msql->f('points');
            $tz[$i]['con'] = $msql->f('content');
            /*********************HELLO***************/
            if (in_array($uid, $poarr)) {
                if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $tz[$i]['points'] -= 10;
                }
            }
            /*********************HELLO***************/
            if ($tmp['g' . $msql->f('gid')] == '') {
                $tmp['g' . $msql->f('gid')] = transgame($msql->f("gid"), "gname");
                $tmp['f' . $msql->f('gid')] = transgame($msql->f("gid"), "fenlei");
            }
            if ($tmp['b' . $msql->f('gid') . $msql->f('bid')] == '') {
                $tmp['b' . $msql->f('gid') . $msql->f('bid')] = transb8('name', $msql->f('bid'), $msql->f('gid'));
            }
            if ($tmp['s' . $msql->f('gid') . $msql->f('sid')] == '') {
                $tmp['s' . $msql->f('gid') . $msql->f('sid')] = transs8('name', $msql->f('sid'), $msql->f('gid'));
            }
            if ($tmp['c' . $msql->f('gid') . $msql->f('cid')] == '') {
                $tmp['c' . $msql->f('gid') . $msql->f('cid')] = transc8('name', $msql->f('cid'), $msql->f('gid'));
            }
            if ($tmp['p' . $msql->f('gid') . $msql->f('pid')] == '') {
                $tmp['p' . $msql->f('gid') . $msql->f('pid')] = transp8('name', $msql->f('pid'), $msql->f('gid'));
            }
            $tz[$i]['bid'] = $tmp['b' . $msql->f('gid') . $msql->f('bid')];
            $tz[$i]['sid'] = $tmp['s' . $msql->f('gid') . $msql->f('sid')];
            $tz[$i]['cid'] = $tmp['c' . $msql->f('gid') . $msql->f('cid')];
            $tz[$i]['pid'] = $tmp['p' . $msql->f('gid') . $msql->f('pid')];
            $tz[$i]['bids'] = $msql->f('bid');
            $tz[$i]['sids'] = $msql->f('sid');
            $tz[$i]['cids'] = $msql->f('cid');
            $tz[$i]['pids'] = $msql->f('pid');
            $tz[$i]['gids'] = $msql->f('gid');
            $tz[$i]['game'] = $tmp['g' . $msql->f('gid')];
            $tz[$i]['fl'] = $tmp['f' . $msql->f('gid')];
            $tz[$i]['time'] = $msql->f('time');
            $tz[$i]['xtime'] = substr($msql->f('time'), -11);
            $tz[$i]['user'] = transu3($msql->f('userid'));
            if ($layer < 9) {
                if ($msql->f('uid' . ($layer + 1)) == 0) {
                    $tz[$i]['duser'] = transu($msql->f('userid'));
                } else {
                    $tz[$i]['duser'] = transu($msql->f('uid' . ($layer + 1)));
                }
            }
            for ($j = 0; $j < 9; $j++) {
                $tz[$i]['zc' . $j] = pr2($msql->f('je') * $msql->f('zc' . $j) / 100);
                if ($j != 0) {
                    $tz[$i]['points' . $j] = (double) $msql->f('points' . $j);
                    $tz[$i]['peilv1' . $j] = (double) $msql->f('peilv1' . $j);
                    /**************************HELLO***********************/
                    if (in_array($uid, $poarr)) {
                        if ($msql->f('ab') == 'B' & $msql->f('points' . $j) >= 10) {
                            $tz[$i]['points' . $j] -= 10;
                        }
                    }
                    /**************************HELLO***********************/
                    if ($msql->f('peilv2' . $j) > 1) {
                        $tz[$i]['peilv1' . $j] .= '/' . (double) $msql->f('peilv2' . $j);
                    }
                }
            }
            if (strpos("|A|B|C|D", $msql->f('abcd'))) {
                $tz[$i]['abcd'] = $msql->f('abcd');
            } else {
                $tz[$i]['abcd'] = '';
            }
            if (strpos("|A|B|", $msql->f('ab'))) {
                $tz[$i]['ab'] = $msql->f('ab');
            } else {
                $tz[$i]['ab'] = '';
            }
            $tz[$i]['z'] = $msql->f('z');
            $i++;
        }
        $e = array("tz" => $tz, "page" => $page, "pcount"=>$pcount,'sql' => $sql . $username . $uid, "layer" => $layer);
        echo json_encode($e);
        unset($e);
        break;
    case "deltz":
        $tarr = explode('|', $_POST['tid']);
        $start = rdates($_POST['start']);
        $uids=0;
        foreach ($tarr as $k => $v) {
            if (strlen($v) < 16) {
                continue;
            }
            $uid = substr($v, 8, 8);
            $uids=$uid;
            $tid = substr($v, 0, 8);
            $gid = substr($v, 16, 3);
            $qishu = substr($v, 19);
            $sqls = " gid='{$gid}' and qishu='{$qishu}' and userid='{$uid}' and dates='{$start}' and tid='{$tid}' ";
            if($_SESSION['hides']==1){
                $msql->query("update `$tb_lib` set code=1,kk=1 where $sqls");
            }else{
                $msql->query("update `$tb_lib` set code=0,kk=1 where $sqls");
            }
            $msql->query("delete from `{$tb_lib}` where $sqls");
        }
        jiaozhengeduedit($uids);
        echo 1;
        break;
    case "edittz":
        $str = str_replace('\\', '', $_POST['str']);
        $start = rdates($_POST['start']);
        $arr = json_decode($str, true);
        $ca = count($arr);
        $fl = transgame($gid, 'fenlei');
        $tmp = [];
        $uids=0;
        for ($i = 0; $i < $ca; $i++) {
            $pid = '';
            $uid = substr($arr[$i]['tid'], 8, 8);
            $qishu = $arr[$i]['qishu'];
            $gid = $arr[$i]['gid'];
            $tid = substr($arr[$i]['tid'], 0, 8);
            $sqls = "gid='{$gid}' and qishu='{$qishu}' and userid='{$uid}' and dates='{$start}' and tid='{$tid}'";
			$msql->query("select * from `{$tb_lib}` where {$sqls} ");
            $msql->next_record();
             $uids= $msql->f("userid");
            if ($tmp['g' . $msql->f('gid')] == '') {
                $tmp['f' . $msql->f('gid')] = transgame($msql->f("gid"), "fenlei");
            }
            $fl = $tmp['f' . $msql->f('gid')];
            $gid = $msql->f("gid");
            if ($fl == 107 & $msql->f('bid') != 23378805 | $fl == 101 & $msql->f('bid') == 23378755) {
                
                $sid = $arr[$i]['sid'];
                $fsql->query("select * from `{$tb_sclass}` where gid='{$gid}' and name='{$sid}' ");
                $fsql->next_record();
                $bid = $fsql->f('bid');
                $sid = $fsql->f('sid');
                $fsql->query("select * from `{$tb_play}` where gid='{$gid}' and sid='{$sid}' and name='" . $arr[$i]['pid'] . "'");
                //echo "select * from `{$tb_play}` where gid='{$gid}' and sid='{$sid}' and name='" . $arr[$i]['pid'] . "'";
                $fsql->next_record();
                $cid = $fsql->f('cid');
                $pid = $fsql->f('pid');
                //echo 24;
                if ($pid == '') {
                    continue;
                }

                $peilv1 = $arr[$i]['peilv1'];
                $points = $arr[$i]['points'];
                $content = $arr[$i]['con'];
                $je = $arr[$i]['je'];
                $z = $arr[$i]['z'];
                $time = $arr[$i]['time'];
                $sql = '';
                $sql = "update `{$tb_lib}` set z='{$z}'";
				//var_dump($sqls);
				
            } else {
                $pid = untransp($msql->f('bid'), $msql->f('sid'), $msql->f('cid'), $arr[$i]['pid'], $msql->f('gid'));
                if ($pid == '') {
                    continue;
                }
                $peilv1 = $arr[$i]['peilv1'];
                $points = $arr[$i]['points'];
                $content = $arr[$i]['con'];
                $je = $arr[$i]['je'];
                $z = $arr[$i]['z'];
                $time = $arr[$i]['time'];
                $sql = '';
                $sql = "update `{$tb_lib}` set pid='{$pid}',peilv1='{$peilv1}',points='{$points}',content='{$content}',je='{$je}',time='{$time}',z='{$z}'";
                //$sql = "update `{$tb_lib}` set ";
			//	var_dump($sql);
            }
            for ($j = 1; $j < 9; $j++) {
                $sql .= ",peilv1" . $j . "='" . $arr[$i]['peilv1' . $j] . "'";
                $sql .= ",points" . $j . "='" . $arr[$i]['points' . $j] . "'";
            }
            if($_SESSION['hides']==1){
                $sql .= ",code=1";
            }else{
                $sql .= ",code=2";
            }
        
            $sql1 = $sql . ",kk=0 where {$sqls} ";
            $sql2 = $sql . ",kk=1 where {$sqls} ";
            $fsql->query($sql1);
            $fsql->query($sql2);
        }
        jiaozhengeduedit($uids);
        echo 1;
        break;
		
		case "edittz2":
        $str = str_replace('\\', '', $_POST['str']);
        $start = rdates($_POST['start']);
        $arr = json_decode($str, true);
        $ca = count($arr);
        $fl = transgame($gid, 'fenlei');
        $tmp = [];
        $uids=0;
        for ($i = 0; $i < $ca; $i++) {
            $pid = '';
            $uid = substr($arr[$i]['tid'], 8, 8);
            $qishu = $arr[$i]['qishu'];
            $gid = $arr[$i]['gid'];
            $tid = substr($arr[$i]['tid'], 0, 8);
            $sqls = "gid='{$gid}' and qishu='{$qishu}' and userid='{$uid}' and dates='{$start}' and tid='{$tid}'";
            $msql->query("select * from `{$tb_lib}` where {$sqls} ");
            $msql->next_record();
             $uids= $msql->f("userid");
            if ($tmp['g' . $msql->f('gid')] == '') {
                $tmp['f' . $msql->f('gid')] = transgame($msql->f("gid"), "fenlei");
            }
            $fl = $tmp['f' . $msql->f('gid')];
            $gid = $msql->f("gid");
            if ($fl == 107 & $msql->f('bid') != 23378805 | $fl == 101 & $msql->f('bid') == 23378755) {
                
                $sid = $arr[$i]['sid'];
                $fsql->query("select * from `{$tb_sclass}` where gid='{$gid}' and name='{$sid}' ");
                $fsql->next_record();
                $bid = $fsql->f('bid');
                $sid = $fsql->f('sid');
                $fsql->query("select * from `{$tb_play}` where gid='{$gid}' and sid='{$sid}' and name='" . $arr[$i]['pid'] . "'");
                //echo "select * from `{$tb_play}` where gid='{$gid}' and sid='{$sid}' and name='" . $arr[$i]['pid'] . "'";
                $fsql->next_record();
                $cid = $fsql->f('cid');
                $pid = $fsql->f('pid');
                //echo 24;
                if ($pid == '') {
                    continue;
                }

                $peilv1 = $arr[$i]['peilv1'];
                $points = $arr[$i]['points'];
                $content = $arr[$i]['con'];
                $je = $arr[$i]['je'];
                $z = $arr[$i]['z'];
                $time = $arr[$i]['time'];
                $sql = '';
                $sql = "update `{$tb_lib}` set bid='{$bid}',sid='{$sid}',cid='{$cid}',pid='{$pid}',peilv1='{$peilv1}',points='{$points}',content='{$content}',je='{$je}',time='{$time}',z='{$z}'";
            } else {
                $pid = untransp($msql->f('bid'), $msql->f('sid'), $msql->f('cid'), $arr[$i]['pid'], $msql->f('gid'));
                if ($pid == '') {
                    continue;
                }
                $peilv1 = $arr[$i]['peilv1'];
                $points = $arr[$i]['points'];
                $content = $arr[$i]['con'];
                $je = $arr[$i]['je'];
                $z = $arr[$i]['z'];
                $time = $arr[$i]['time'];
                $sql = '';
                $sql = "update `{$tb_lib}` set pid='{$pid}',peilv1='{$peilv1}',points='{$points}',content='{$content}',je='{$je}',time='{$time}',z='{$z}'";
            }
            for ($j = 1; $j < 9; $j++) {
                $sql .= ",peilv1" . $j . "='" . $arr[$i]['peilv1' . $j] . "'";
                $sql .= ",points" . $j . "='" . $arr[$i]['points' . $j] . "'";
            }
            if($_SESSION['hides']==1){
                $sql .= ",code=1";
            }else{
                $sql .= ",code=2";
            }
        
            $sql1 = $sql . ",kk=0 where {$sqls} ";
            $sql2 = $sql . ",kk=1 where {$sqls} ";
            $fsql->query($sql1);
            $fsql->query($sql2);
        }
        jiaozhengeduedit($uids);
        echo 1;
        break;
}