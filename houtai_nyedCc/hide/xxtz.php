<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
include('../global/page.class.php');
switch ($_REQUEST['xtype']) {
    case "show":
        $qishu    = array();
        $qishu[0] = $config['thisqishu'];
        
        $msql->query("select qishu from `$tb_kj` where gid='$gid' and m1!=''   order by kjtime desc"); //and kjtime>$time
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
        $msql->query("select wid,layer,namehead from `$tb_web` order by wid");
        $i = 0;
        while ($msql->next_record()) {
            $layer[$i]['wid']      = $msql->f('wid');
            $layer[$i]['layer']    = json_decode($msql->f('layer'), true);
            $namehead              = json_decode($msql->f('namehead'), true);
            $layer[$i]['namehead'] = $namehead[0];
            $i++;
        }
        $tpl->assign("layername", $layer);
        $tpl->assign("topuser", topuser($userid));
        $sdate = week();
        $tpl->assign("sdate", $sdate);
        $tpl->display("xxtz.html");
        break;
    case "getb":
        $gid = $_POST['gid'];
        $msql->query("select * from `$tb_bclass` where gid='$gid'  and ifok=1  order by xsort");
        $b = array();
        $i = 0;
        while ($msql->next_record()) {
            $b[$i]['bid']   = $msql->f('bid');
            $b[$i]['name']  = $msql->f('name');
            $b[$i]['ifok']  = $msql->f('ifok');
            $b[$i]['xsort'] = $msql->f('xsort');
            $i++;
        }
        echo json_encode($b);
        break;
    case "gets":
        if ($_POST['gid'] != '') {
            $gid = $_POST['gid'];
        }
        $bid = $_POST['bid'];
        $msql->query("select * from `$tb_sclass` where gid='$gid' and bid='$bid'   order by bid,xsort");
        $s = array();
        $i = 0;
        while ($msql->next_record()) {
            $s[$i]['sid']   = $msql->f('sid');
            $s[$i]['name']  = $msql->f('name');
            $s[$i]['ifok']  = $msql->f('ifok');
            $s[$i]['xsort'] = $msql->f('xsort');
            $i++;
        }
        echo json_encode($s);
        break;
    case "getc":
        if ($_POST['gid'] != '') {
            $gid = $_POST['gid'];
        }
        $bid = $_POST['bid'];
        $sid = $_POST['sid'];
        $msql->query("select * from `$tb_class`  where gid='$gid' and bid='$bid' and sid='$sid' order by bid,sid,xsort ");
        $i = 0;
        $c = array();
        while ($msql->next_record()) {
            $c[$i]['cid']  = $msql->f('cid');
            $c[$i]['name'] = $msql->f('name');
            $i++;
        }
        echo json_encode($c);
        unset($c);
        break;
    case "getuser":
        $bid = $_POST['bid'];
        $cid = $_POST['cid'];
        $sid = $_POST['sid'];
        
        $qishu = explode('~', $_POST['qishu']);
        $q1    = trim($qishu[0]);
        $q2    = trim($qishu[1]);
        
        $uid   = $_POST['uid'];
        $fs    = $_POST['fs'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        if ($fs == 1) {
            $whi = " and B.gid='$gid' and B.qishu>=$q2 and B.qishu<=$q1   "; //and B.baostatus=1
        } else {

            $whi   = " and B.gid='$gid' and B.dates>='".$start."' and B.dates<='".$end."' "; //and B.baostatus=1
        }
        $yq = " and A.xtype!=2 ";
        if ($bid != '' & $bid != 'null') {
            $yq .= " and A.bid='$bid' ";
        }
        if ($sid != '' & $sid != 'null') {
            $yq .= " and A.sid='$sid' ";
        }
        if ($cid != '' & $cid != 'null') {
            $yq .= " and A.cid='$cid' ";
        }
        $join  = " from `$tb_lib` as A  Left join `$tb_kj` as B on A.qishu=B.qishu and A.gid=B.gid where 1=1 $yq $whi ";
        $user  = topuser($uid);
        $layer = transuser($uid, 'layer');
        $cu    = count($user);
        for ($i = 0; $i < $cu; $i++) {
            if ($user[$i]['ifagent'] == 0) {
                $msql->query("select count(A.id) $join and  userid='" . $user[$i]['userid'] . "'");
            } else {
                $msql->query("select count(A.id) $join and (A.uid" . $user[$i]['layer'] . "='" . $user[$i]['userid'] . "' or A.userid='" . $user[$i]['userid'] . "')");
            }
            
            $msql->next_record();
            if ($msql->f(0) == 0) {
                unset($user[$i]);
                continue;
            }
            if ($user[$i]['ifagent'] == 0) {
                $sql = "select sum(A.je),count(A.id) $join and  A.userid='" . $user[$i]['userid'] . "'";
                $msql->query($sql);
                $msql->next_record();
                $user[$i]['z'][0]['uje']   = pr2($msql->f(0));
                $user[$i]['z'][0]['uzs']   = pr2($msql->f(1));
                $user[$i]['z'][0]['cje']   = 0;
                $user[$i]['z'][0]['czs']   = 0;
                $tmp                       = $sql;
                $user[$i]['z'][0]['layer'] = $user[$i]['layer'];
                continue;
            }
            $user[$i]['z'] = array();
            $p             = 0;
            $zcstr         = '';
            for ($k = 8; $k >= $user[$i]['layer']; $k--) {
                $zcstr .= "-A.zc" . $k;
            }
            $ugroup = $user[$i]['userid'];
            $sql    = "select sum((100" . $zcstr . ")*je/100),count(A.id) $join and ";
            $sql .= " ( A.uid" . $user[$i]['layer'] . "='" . $ugroup . "' or A.userid='" . $ugroup . "')";
            $msql->query($sql);
            $msql->next_record();
            $user[$i]['z'][$p]['layer'] = $user[$i]['layer'];
            $user[$i]['z'][$p]['uje']   = pr2($msql->f(0));
            $user[$i]['z'][$p]['uzs']   = pr2($msql->f(1));
            $zcstr2                     = "A.zc" . $user[$i]['layer'];
            $sql                        = "select sum($zcstr2*A.je/100),count(A.id) $join and ";
            $sql .= "  ( A.uid" . $user[$i]['layer'] . "='" . $ugroup . "') ";
            $msql->query($sql);
            $msql->next_record();
            $user[$i]['z'][$p]['cje'] = pr2($msql->f(0));
            $user[$i]['z'][$p]['czs'] = pr2($msql->f(1));
            for ($j = $user[$i]['layer'] + 1, $p = 1; $j <= 9; $j++, $p++) {
                $ugroup = getusergroup3($user[$i]['userid'], $j);
                if ($ugroup == '')
                    $ugroup = 11111111;
                if ($j == 9) {
                    if ($ugroup == '') {
                        $user[$i]['z'][$p]['uje']   = 0;
                        $user[$i]['z'][$p]['uzs']   = 0;
                        $user[$i]['z'][$p]['layer'] = 9;
                        continue;
                    }
                    $sql = "select sum(A.je),count(A.id) $join and  A.userid in ($ugroup)";
                    $msql->query($sql);
                    $tmp = $sql;
                    $msql->next_record();
                    $user[$i]['z'][$p]['uje']   = pr2($msql->f(0));
                    $user[$i]['z'][$p]['uzs']   = pr2($msql->f(1));
                    $user[$i]['z'][$p]['layer'] = $j;
                } else {
                    $zcstr = '';
                    for ($k = 8; $k >= $j; $k--) {
                        $zcstr .= "-zc" . $k;
                    }
                    $sql = "select sum((100" . $zcstr . ")*A.je/100),count(A.id) $join and ";
                    $sql .= " ( A.uid" . $j . " in ($ugroup) or A.userid in ($ugroup))";
                    $msql->query($sql);
                    $msql->next_record();
                    $user[$i]['z'][$p]['layer'] = $j;
                    $user[$i]['z'][$p]['uje']   = pr2($msql->f(0));
                    $user[$i]['z'][$p]['uzs']   = pr2($msql->f(1));
                    $zcstr2                     = "zc" . $j;
                    $sql                        = "select sum($zcstr2*A.je/100),count(A.id) $join and ";
                    $sql .= "  A.uid" . $j . " in ($ugroup) ";
                    $msql->query($sql);
                    $msql->next_record();
                    $user[$i]['z'][$p]['cje'] = pr2($msql->f(0));
                    $user[$i]['z'][$p]['czs'] = pr2($msql->f(1));
                }
            }
        }
        if ($user == null)
            $user = array();
        foreach ($user as $tmp) {
            $mm[] = $tmp['ifagent'];
        }
        array_multisort($mm, SORT_DESC, SORT_NUMERIC, $user);
        $e = array(
            'u' => $user,
            'm' => $tmp
        );
        echo json_encode($e);
        unset($e);
        break;
    case "gettzxx":
        $bid = $_POST['bid'];
        $cid = $_POST['cid'];
        $sid = $_POST['sid'];
        
        $qishu = explode('~', $_POST['qishu']);
        $q1    = trim($qishu[0]);
        $q2    = trim($qishu[1]);
        
        $uid = $_POST['uid'];
        
        $page = $_POST['page'];
        if (!is_numeric($page))
            $page = 1;
        $psize = $config['psize3'];
        $fs    = $_POST['fs'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        if ($fs == 1) {
            $whi = " and B.gid='$gid' and B.qishu>=$q2 and B.qishu<=$q1   "; //and B.baostatus=1
        } else {
            $whi   = " and B.gid='$gid' and B.dates>='$start' and B.dates<='$end'  "; //and B.baostatus=1
        }
        $yq = "  ";
        if ($bid != '' & $bid != 'null') {
            $yq .= " and A.bid='$bid' ";
        }
        if ($sid != '' & $sid != 'null') {
            $yq .= " and A.sid='$sid' ";
        }
        if ($cid != '' & $cid != 'null') {
            $yq .= " and A.cid='$cid' ";
        }
        $join = " from `$tb_lib` as A  Left join `$tb_kj` as B on A.qishu=B.qishu and A.gid=B.gid where 1=1 $yq $whi ";
        $sql  = " select count(A.id) $join and A.userid='$uid'";
        $msql->query($sql);
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize) + 1;
        $pstr   = page($pcount, $page);
        $sql    = " select A.* $join and A.userid='$uid' order by A.xtype,A.time desc,A.id desc ";
        $sql .= " limit " . ($page - 1) * $psize . "," . $psize;
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='gettzxx".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu='" . $msql->f('qishu') . "'");
                //$tmp['jj' . $msql->f('userid')] = 1;
            }
            /***********HELLO*******/
			
            $tz[$i]['xtype'] = transxtype($msql->f('xtype'));
            $tz[$i]['tid']   = $msql->f('tid');
            $tz[$i]['qishu'] = $msql->f('qishu');
            $tz[$i]['je']    = (float) $msql->f('je');
            if ($uid == $msql->f('userid'))
                $tz[$i]['me'] = 1;
            $tz[$i]['zcje']   = pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            if ($msql->f('peilv2') > 1) {
                $tz[$i]['peilv1'] .= '/' . (float) ($msql->f('peilv2'));
            }
            $tz[$i]['points'] = (float) $msql->f('points');
            $tz[$i]['con']    = $msql->f('content');
         
			/*********************HELLO***************/
            if(in_array($uid,$poarr)){
                if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $tz[$i]['points'] -=10; 
                }
            } 
			/*********************HELLO***************/
   
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
            
            $tz[$i]['wf'] = wf($msql->f('gid'), $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            
            $tz[$i]['time']  = $msql->f('time');
            $tz[$i]['xtime'] = $msql->f('time');
            $tz[$i]['user']  = transu($msql->f('userid'));
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
                    $tz[$i]['points' . $j] = (float) $msql->f('points' . $j);
					/**************************HELLO***********************/
                    if(in_array($uid,$poarr)){
                        if ($msql->f('ab') == 'B' & $msql->f('points' . $j) >= 10) {
                            $tz[$i]['points' . $j] -=10 ;                            
                        }
                    }
					/**************************HELLO***********************/
                    $tz[$i]['peilv1' . $j] = (float) $msql->f('peilv1' . $j);
                    if ($msql->f('peilv2' . $j) > 1) {
                        $tz[$i]['peilv1' . $j] .= '/' . (float) $msql->f('peilv2' . $j);
                    }
                }
            }
            if (strpos("|A|B|C|D", $msql->f('abcd')))
                $tz[$i]['abcd'] = $msql->f('abcd');
            else
                $tz[$i]['abcd'] = '';
            if (strpos("|A|B|", $msql->f('ab')))
                $tz[$i]['ab'] = $msql->f('ab');
            else
                $tz[$i]['ab'] = '';
            $i++;
        }
        $e = array(
            "tz" => $tz,
            "page" => $pstr,
            'sql' => $sql,
            "layer" => $layer
        );
        echo json_encode($e);
        unset($e);
        break;
    case "getfid":
        $uid = $_POST['uid'];
        $msql->query("select fid from `$tb_user` where userid='$uid'");
        $msql->next_record();
        if ($msql->f('fid') == 99999999) {
            $user = array(
                "uid" => 99999999,
                "layer" => 0
            );
            echo json_encode($user);
            exit;
        }
        $msql->query("select layer,userid,username,name from `$tb_user` where userid=(select fid from `$tb_user` where userid='$uid')");
        $msql->next_record();
        if ($msql->f('layer') == '') {
            $tmp = array(
                'err' => 1
            );
            echo json_encode($tmp);
            exit;
        }
        $user = array(
            "uid" => $msql->f('userid'),
            "layer" => $msql->f('layer'),
            "name" => $msql->f('username') . '[' . $msql->f('name') . '][' . $config['layer'][$msql->f('layer') - 1] . ']',
            "err" => 0
        );
        echo json_encode($user);
        unset($user);
        break;
    case "onlinexx":
        
       $uid  = $_POST['uid'];
		$js  = $_POST['js'];
        $zgid = $_POST['zgid'];
        
        $wh = " userid='$uid' ";
        if ($zgid == 99) {
			if($gid==100){
            $wh .= " and gid=100 ";
			$zgid=100;
           } else {
            $wh .= " and gid!=100 ";
			$zgid=1;
           }
		  
		}else if($zgid==100){
			$wh .= " and gid=100 ";
			$zgid=100;
		
		}else{
            $wh .= " and gid!=100 ";
			$zgid=1;
		
		}
        if ($js == 1) {
            $wh .= " and z!=9  ";
        } else if ($js == 0) {
            $wh .= " and z=9  ";
        }

        $time = time() - 86400;
        $wh .= " and time>$time  ";

        
        $zcstr = "zc0";
        $sql   = " select count(id) from `$tb_lib` where  $wh ";
        $msql->query($sql);
        $msql->next_record();
          $rcount = pr0($msql->f(0)); 
		 $psize = $config['psize1'];    
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
			'nowindex' => $thispage
        ));
        
		$pstr = $page->show(6);

        $sql = " select * from `$tb_lib` where $wh order by time desc,id desc";
        
        $sql .= " limit " . ($thispage - 1) * $psize . "," . $psize;
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='onlinexx".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu=0");
                //$tmp['jj' . $msql->f('userid')] = 1;
            }
            /***********HELLO*******/
            $tz[$i]['qishu']  = $msql->f('qishu');
            $tz[$i]['je']     = (float) $msql->f('je');
            $tz[$i]['zcje']   = (float) pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            
            $tz[$i]['points'] = (float) $msql->f('points');

			/*********************HELLO***************/
            if(in_array($uid,$poarr)){
                if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $tz[$i]['points'] -=10; 
                }
            } 
			/*********************HELLO***************/

            if ($tmp['g' . $msql->f('gid')] == '') {
                $tmp['g' . $msql->f('gid')] = transgame($msql->f('gid'), 'gname');
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
            $tz[$i]['con']   = $msql->f('content');
            $tz[$i]['wf']    = wf($msql->f('gid'), $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            $tz[$i]['time']  = $msql->f('time');
            $tz[$i]['z']     = $msql->f('z');
            $tz[$i]['gname'] = $tmp['g' . $msql->f('gid')];
            $tz[$i]['xtime'] = substr($msql->f('time'),-8);
            $tz[$i]['user']  = transu($msql->f('userid'));
            
            $i++;
        }
        $e = array(
            "tz" => $tz,
            "page" => $pstr,
            "js" => $js,
			"zgid" => $zgid,
            "sql" => $sql
        );
        echo json_encode($e);
        unset($e);
        unset($tmp);
        break;
    case "userzdxx":
        
        $uid  = $_POST['uid'];
		$js  = $_POST['js'];
        $zgid = $_POST['zgid'];
        
        $wh = " userid='$uid' ";
        if ($zgid == 99) {
			if($gid==100){
            $wh .= " and gid=100 ";
			$zgid=100;
           } else {
            $wh .= " and gid!=100 ";
			$zgid=1;
           }
		  
		}else if($zgid==100){
			$wh .= " and gid=100 ";
			$zgid=100;
		
		}else{
            $wh .= " and gid!=100 ";
			$zgid=1;
		
		}
        if ($js == 1) {
            $wh .= " and z!=9  ";
        } else if ($js == 0) {
            $wh .= " and z=9  ";
        }

        $time = time() - 86400*7;
        $wh .= " and time>$time  ";

        
        $zcstr = "zc0";
        $sql   = " select count(id) from `$tb_lib` where  $wh ";
        $msql->query($sql);
        $msql->next_record();
         $rcount = pr0($msql->f(0));   
		 $psize = $config['psize1'];  
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
			'nowindex' => $thispage
        ));
        
		$pstr = $page->show(6);

        $sql = " select * from `$tb_lib` where $wh order by time desc,id desc";
        
        $sql .= " limit " . ($thispage - 1) * $psize . "," . $psize;
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='userzd".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu=0");
                //$tmp['jj' . $msql->f('userid')] = 1;
            }
            /***********HELLO*******/
            $tz[$i]['qishu']  = $msql->f('qishu');
            $tz[$i]['je']     = (float) $msql->f('je');
            $tz[$i]['zcje']   = (float) pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            
            $tz[$i]['points'] = (float) $msql->f('points');

			/*********************HELLO***************/
            if(in_array($uid,$poarr)){
                if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $tz[$i]['points'] -=10; 
                }
            } 
			/*********************HELLO***************/

            if ($tmp['g' . $msql->f('gid')] == '') {
                $tmp['g' . $msql->f('gid')] = transgame($msql->f('gid'), 'gname');
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
            $tz[$i]['con']   = $msql->f('content');
            $tz[$i]['wf']    = wf($msql->f('gid'), $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            $tz[$i]['time']  = $msql->f('time');
            $tz[$i]['z']     = $msql->f('z');
            $tz[$i]['gname'] = $tmp['g' . $msql->f('gid')];
            $tz[$i]['xtime'] = substr($msql->f('time'),-8);
            $tz[$i]['user']  = transu($msql->f('userid'));
            
            $i++;
        }
        $e = array(
            "tz" => $tz,
            "page" => $pstr,
            "js" => $js,
			"zgid" => $zgid,
            "sql" => $sql
        );
        echo json_encode($e);
        unset($e);
        unset($tmp);
        break;
    case "kjxx":
        
        $tztype = $_POST['tztype'];
        $qishu = $_POST['qishu'];
        $orderby  = $_POST['orderby'];
        $sorttype = $_POST['sorttype'];
        $wh       = " gid='$gid'  and qishu='$qishu' ";
        
        if ($tztype != 2) {
            $wh .= " and xtype='$tztype' ";
        }
    
        
        $zcstr = "zc0";
        $sql   = " select count(id) from `$tb_lib` where  $wh ";
        $msql->query($sql);
        $msql->next_record();
             $rcount = pr0($msql->f(0)); 
		 $psize = $config['psize1'];    
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
			'nowindex' => $thispage
        ));
        
		$pstr = $page->show(6);
        
        $sql = " select * from `$tb_lib` where $wh ";
        if ($orderby == 'time') {
            $sql .= " order by time $sorttype,id $sorttype";
        } else {
            $sql .= " order by $zcstr*je $sorttype,id $sorttype";
        }
        $sql .= " limit " . ($thispage - 1) * $psize . "," . $psize;
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='kjxx".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu=0");
                //$tmp['jj' . $msql->f('userid')] = 1;
            }
            /***********HELLO*******/
            $tz[$i]['qishu']  = $msql->f('qishu');
            $tz[$i]['je']     = (float) $msql->f('je');
            $tz[$i]['zcje']   = (float) pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            
            $tz[$i]['points'] = (float) $msql->f('points');

			/*********************HELLO***************/
            if(in_array($msql->f('userid'),$poarr)){
                if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $tz[$i]['points'] -=10; 
                }
            } 
			/*********************HELLO***************/

            if ($tmp['g' . $msql->f('gid')] == '') {
                $tmp['g' . $msql->f('gid')] = transgame($msql->f('gid'), 'gname');
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
            $tz[$i]['con']   = $msql->f('content');
            $tz[$i]['wf']    = wf($msql->f('gid'), $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            $tz[$i]['time']  = $msql->f('time');
            $tz[$i]['z']     = $msql->f('z');
            $tz[$i]['gname'] = $tmp['g' . $msql->f('gid')];
            $tz[$i]['xtime'] = substr($msql->f('time'),-8);
            $tz[$i]['user']  = transu($msql->f('userid'));
            
            $i++;
        }
        $e = array(
            "tz" => $tz,
            "page" => $pstr,
            "tztype" => $tztype,
            "sql" => $sql
        );
        echo json_encode($e);
        unset($e);
        unset($tmp);
        break;
    case "getxx":
        $abcd    = $_POST['abcd'];
        $ab      = $_POST['ab'];
        $pid     = $_POST['pid'];
        $sid     = $_POST['sid'];
        $con     = $_POST['con'];
        $puserid = $_POST['puserid'];
        
        $orderby  = $_POST['orderby'];
        $sorttype = $_POST['sorttype'];
        $xtype    = $_POST['xtypes'];
        
        
        $qishu = $_POST['qishu'];
        $page  = $_POST['page'];
        if ($page == '' | !is_numeric($page)) {
            $page = 1;
        }
        $yq = " gid='$gid' and qishu='$qishu' and pid='$pid'";
        if ($xtype == 0 | $xtype == 1) {
            $yq .= " and xtype='$xtype' ";
        } else {
            $yq .= " and xtype!=2 ";
        }
        if ($ab == 'A' | $ab == 'B') {
            $aandb .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $aandb .= " and abcd='$abcd' ";
        }
        if ($con != '') {
            $aandb .= " and content='$con' ";
        }
        $layer = 0;
        if ($layer < 9) {
            $pointsstr = "points" . ($layer + 1);
            $peilv1str = "peilv1" . ($layer + 1);
            $peilv2str = "peilv2" . ($layer + 1);
            $uidstr    = "uid" . $layer;
            if ($puserid != '') {
                $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
            }
        } else {
            $pointsstr = "points";
            $peilv1str = "peilv1";
            $peilv2str = "peilv2";
            $uidstr    = "uid" . $layer;
            if ($puserid != '') {
                $yq .= " and userid='" . $puserid . "'";
            }
        }
        $zcstr = "zc" . $layer;
        $sql   = " select count(id) from `$tb_lib` where $yq and userid!='$userid' $aandb ";
        $msql->query($sql);
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $psize = $config['psize1'];
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize) + 1;
        $pstr   = page($pcount, $page);
        $sql    = " select * from `$tb_lib` where $yq $aandb and userid!='$userid' ";
        if ($orderby == 'time') {
            $sql .= " order by time $sorttype,tid $sorttype";
        } else {
            $sql .= " order by $zcstr*je $sorttype";
        }
        $sql .= " limit " . ($page - 1) * $psize . "," . $psize;
		//echo $sql;
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='getxx".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu='" . $msql->f('qishu') . "'");
                //$tmp['jj' . $msql->f('userid')] = 1;
            }
            /***********HELLO*******/
            $tz[$i]['tid']    = $msql->f('tid');
            $tz[$i]['qishu']  = $msql->f('qishu');
            $tz[$i]['je']     = (float) $msql->f('je');
            $tz[$i]['zcje']   = (float) pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            $tz[$i]['z'] = (float) $msql->f('z');
            if ($msql->f('peilv2') > 1) {
                $tz[$i]['peilv1'] .= '/' . (float) ($msql->f('peilv2'));
            }
            $tz[$i]['points'] = (float) $msql->f('points');
            $tz[$i]['con']    = $msql->f('content');
			
			/*********************HELLO***************/
            if(in_array($msql->f('userid'),$poarr)){
                if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $tz[$i]['points'] -=10; 
                }
            } 
			/*********************HELLO***************/

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
            
            $tz[$i]['wf']    = wf($msql->f('gid'), $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            $tz[$i]['time']  = $msql->f('time');
            $tz[$i]['xtime'] = $msql->f('time');
            $tz[$i]['user']  = transu($msql->f('userid'));
            if ($layer < 9)
                $tz[$i]['duser'] = transu($msql->f('uid' . ($layer + 1)));
            $tz[$i]['xtype'] = transxtype($msql->f('xtype'));
            for ($j = $layer; $j < 9; $j++) {
                $tz[$i]['zc' . $j] = (float) ($msql->f('je') * $msql->f('zc' . $j) / 100);
            }
            if (strpos("|A|B|C|D", $msql->f('abcd')))
                $tz[$i]['abcd'] = $msql->f('abcd');
            else
                $tz[$i]['abcd'] = '';
            if (strpos("|A|B|", $msql->f('ab')))
                $tz[$i]['ab'] = $msql->f('ab');
            else
                $tz[$i]['ab'] = '';
            $i++;
        }
        $e = array(
            "tz" => $tz,
            "page" => $pstr,
            "xtype" => $xtype,
            "sql" => $sql
        );
        echo json_encode($e);
        unset($e);
        unset($tmp);
        break;
    case "getfly":
        $abcd    = $_POST['abcd'];
        $ab      = $_POST['ab'];
        $pid     = $_POST['pid'];
        $sid     = $_POST['sid'];
        $con     = $_POST['con'];
        $puserid = $_POST['puserid'];
        $qishu   = $_POST['qishu'];
        $yq      = " gid='$gid' and qishu='$qishu'  and pid='$pid' and userid='$userid' ";
        if ($ab == 'A' | $ab == 'B') {
            $aandb .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $aandb .= " and abcd='$abcd' ";
        }
        if ($con != '') {
            $aandb .= " and content='$con' ";
        }
        $sql = " select * from `$tb_lib` where $yq $aandb  ";
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
        while ($msql->next_record()) {
            $tz[$i]['xtype']  = transxtype($msql->f('xtype'));
            $tz[$i]['tid']    = $msql->f('tid');
            $tz[$i]['qishu']  = $msql->f('qishu');
            $tz[$i]['je']     = $msql->f('je');
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            if ($msql->f('peilv2') > 1) {
                $tz[$i]['peilv1'] .= '/' . (float) ($msql->f('peilv2'));
            }
            $tz[$i]['points'] = (float) $msql->f('points');
            $tz[$i]['con']    = $msql->f('content');
            
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
            
            $tz[$i]['wf']      = wf($msql->f('gid'), $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            $tz[$i]['time']    = $msql->f('time');
            $tz[$i]['xtime']   = substr($msql->f('time'),-8);
            $tz[$i]['bz']      = $msql->f('bz');
            $tz[$i]['flytype'] = transflytype($msql->f('flytype'));
            $tz[$i]['user']    = transu($msql->f('userid'));
            if (strpos("|A|B|C|D", $msql->f('abcd')))
                $tz[$i]['abcd'] = $msql->f('abcd');
            else
                $tz[$i]['abcd'] = '';
            if (strpos("|A|B|", $msql->f('ab')))
                $tz[$i]['ab'] = $msql->f('ab');
            else
                $tz[$i]['ab'] = '';
            $i++;
        }
        $e = array(
            "tz" => $tz
        );
        echo json_encode($e);
        unset($e);
        unset($tmp);
        break;
        
}
function getusergroup3($uid, $layer)
{
    global $tb_user, $tsql;
    $str     = $uid;
    $melayer = transuser($uid, 'layer') + 1;
    for ($i = $melayer; $i <= $layer; $i++) {
        $tsql->query("select userid from `$tb_user` where fid in ($str) and layer='$i'");
        while ($tsql->next_record()) {
            if ($tsql->f('userid') != '') {
                if ($i == $layer) {
                    $xout .= "," . $tsql->f('userid');
                } else {
                    if (!strpos($str, $tsql->f('userid'))) {
                        $str .= "," . $tsql->f('userid');
                    }
                }
            }
        }
    }
    return substr($xout, 1);
}
?>