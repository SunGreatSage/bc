<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "oldshow":
        $sdate = week();
        $tpl->assign("sdate", $sdate);
        $gamecs = getgamecs($userid);
        $gamecs = getgamename($gamecs);
        $fl     = array();
        foreach ($gamecs as $key => $val) {
            if (!in_array($val['flname'], $fl) & $val['gid'] != 107 & $val['gid'] != 100) {
                $fl[$val['fenlei']] = $val['flname'];
            }
        }
        
        $tpl->assign("gamecs", $gamecs);
        $tpl->assign("fl", $fl);
        $tpl->assign("topid", $userid);
        $tpl->assign("gid", $gid);
        $tpl->assign("layer", transuser($userid, 'layer'));
        $tpl->assign("username", transu($userid));
        $tpl->assign("layername", $config['layer']);
        $tpl->display("baox.html");
        break;
    case "show":
        $sdate = week();
        $tpl->assign("sdate", $sdate);
        $gamecs = getgamecs($userid);
        $gamecs = getgamename($gamecs);
        $fl     = array();
        foreach ($gamecs as $key => $val) {
            if (!in_array($val['flname'], $fl) & $val['gid'] != 107 & $val['gid'] != 100) {
                $fl[$val['fenlei']] = $val['flname'];
            }
        }
        
        $tpl->assign("gamecs", $gamecs);
        $tpl->assign("fl", $fl);
        $tpl->assign("topid", $userid);
        $tpl->assign("gid", $gid);
        $tpl->assign("layer", transuser($userid, 'layer'));
        $tpl->assign("username", transu($userid));
        $tpl->assign("layername", $config['layer']);
		$tpl->assign("config", $config);
        $tpl->display("baonew.html");
        break;
	case "getqishu":
		$gid = $_POST['gid'];
		$start = rdates($_POST['start']);
		$end = rdates($_POST['end']);
		if (!in_array($gid, $garr)) exit;
		$qishu = $msql->arr("select qishu from `$tb_lib` where gid='$gid' and dates>='$start' and dates<='$end' group by qishu desc limit 600", 1);
		echo json_encode($qishu);
		break;
	case "userbao":
	//print_r($_POST);
		$game = $_POST['game'];
		$uid = $_POST['uid'];
		$start = rdates($_POST['start']);
		$end = rdates($_POST['end']);
		$qishu = $_POST['qishu'];
		$types = $_POST['types'];
		$jsstatus = $_POST['jsstatus'];
		$username = trim($_POST['username']);
		$yk = $_POST['yk'];
		$je = $_POST['je'];
		$type = $_POST['type'];
		$v1 = $_POST['v1'];
		$v2 = $_POST['v2'];
		$v3 = $_POST['v3'];
		$page = $_POST['page'];
		$fly = $_POST['fly'];
		$ttype = $_POST['ttype'];
		if (!checkfid($uid) & $uid != $userid) {
			exit;
		}
		if($page=='' | !is_numeric($page)) $page=1;
		if($type=='fl'){
			$gstr = " gid='$v2' and bid='$v3' ";
		}else{
			if(is_numeric($v2) & $v2!=0){
				$gstr = " gid='$v2' ";
			}else{
		$game = explode('|', $game);
		foreach ($game as $k => $v) {
			if ($v == '') unset($game[$k]);
		}
		$gstr = ' gid in (' . implode(',', $game) . ')';
			}
		}
		if ($qishu == '' | !is_numeric($qishu)) {
			if ($start == $end) {
				$whi = " dates='$start'";
			} else {
				$whi = " dates>='$start' and dates<='$end' ";
			}
		} else {
			$whi = " qishu='$qishu' ";
		}
		if ($jsstatus != 'true' & $jsstatus != 'false') {
			$jsstatus = 'true';
		}
		if ($jsstatus == 'true') {
			$jsstatus = 1;
		} else {
			$jsstatus = 0;
		}
		if ($jsstatus == 1) {
			$zstr = " z!=9 ";
		} else {
			$zstr = " z=9 ";
		}
		if($fly==2){
		   $xstr = " xtype=2 ";
		}else{
		   $xstr = " xtype!=2 ";
		}
		$msql->query("select layer,plc from `$tb_user` where userid='$userid'");
		$msql->next_record();
		$layer = $msql->f('layer');
		$plc = $msql->f('plc');
		$myid = 'uid' . $layer;
		$myzcstr = 'zc' . $layer;
		$mypointsstr = 'points' . $layer;
		$mypeilv1str = 'peilv1' . $layer;
		$mypeilv2str = 'peilv2' . $layer;
		$zcstrdown = '';
		for ($k = 8;$k > $layer;$k--) {
			$zcstrdown.= '-zc' . $k;
		}
		$zcstrup = $zcstrdown . '-zc' . $k;
		if ($layer < 8) {
			$uidstrdown = 'uid' . ($layer + 1);
			$pointsstrdown = 'points' . ($layer + 1);
			$peilv1strdown = 'peilv1' . ($layer + 1);
			$peilv2strdown = 'peilv2' . ($layer + 1);
		} else {
			$uidstrdown = 'userid';
			$pointsstrdown = 'points';
			$peilv1strdown = 'peilv1';
			$peilv2strdown = 'peilv2';
		}
		$starts = microtime(true);
		
		if($type=='fl'){
			if($layer>0){
		       $join = " from `$tb_lib` where $gstr and uid".$layer."='$userid' and $whi and bs=1 and $xstr and $zstr";
			}else{
		       $join = " from `$tb_lib` where $gstr and $whi and bs=1 and $xstr and $zstr";   
			}
		}else{
			if($v1==$userid){
		        $join = " from `$tb_lib` where userid='$v1' and $gstr and $whi and bs=1 and $xstr and $zstr";
			}else{
				if($layer>0){
				    $join = " from `$tb_lib` where userid='$v1' and uid".$layer."='$userid' and $gstr and $whi and bs=1 and $xstr and $zstr";
				}else{
				    $join = " from `$tb_lib` where userid='$v1' and $gstr and $whi and bs=1 and $xstr and $zstr";	
				}
		        
			}
		}
		$sql = "select count(id) $join ";
		$sqls = $sql;
		$msql->query($sql);
		$msql->next_record();
		$rcount = pr0($msql->f(0));
		$psize = $config['psize2'];
		$pcount = $rcount%$psize==0 ? $rcount/$psize : (($rcount-$rcount%$psize)/$psize+1);

        $sql = "select * $join order by time desc,id desc  limit ".($page-1)*$psize.",".$psize;
		$msql->query($sql);
		$i=0;
		$bao=array();
		$tmp=array();
		while($msql->next_record()){
			/***********HELLO*******/
			if ($tmp['u' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
				$fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='bao',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu=0");
				$tmp['u' . $msql->f('userid') ] = 1;
			}
			/***********HELLO*******/
			
			if($tmp['g'.$msql->f('gid')]==''){
                $fsql->query("select gname,fenlei from `$tb_game` where gid='".$msql->f('gid')."'");
                $fsql->next_record();
                $tmp['g' . $msql->f('gid')] = $fsql->f('gname');
                $tmp['f' . $msql->f('gid')] = $fsql->f('fenlei');
			}
			if($tmp['u'.$msql->f('userid')]==''){
			    $tmp['u'.$msql->f('userid')] = transu2($msql->f('userid'));
			}
			if($tmp['b'.$msql->f('gid').$msql->f('bid')]==''){
				$tmp['b'.$msql->f('gid').$msql->f('bid')] = transb8('name', $msql->f('bid'),$msql->f('gid'));
			}
			if($tmp['s'.$msql->f('gid').$msql->f('sid')]==''){
				$tmp['s'.$msql->f('gid').$msql->f('sid')] = transs8('name', $msql->f('sid'),$msql->f('gid'));
			}
			if($tmp['c'.$msql->f('gid').$msql->f('cid')]==''){
				$tmp['c'.$msql->f('gid').$msql->f('cid')] = transc8('name', $msql->f('cid'),$msql->f('gid'));
			}
			if($tmp['p'.$msql->f('gid').$msql->f('pid')]==''){
				$tmp['p'.$msql->f('gid').$msql->f('pid')] = transp8('name', $msql->f('pid'),$msql->f('gid'));
			}
			$bao[$i]['tid'] = substr($msql->f('userid'),-3).$msql->f('tid');
			$bao[$i]['time'] = $msql->f('time');
			$bao[$i]['week'] = rweek(date("w",strtotime($msql->f('time'))));
            $bao[$i]['game'] = $tmp['g'.$msql->f('gid')];
			$bao[$i]['user'] = $tmp['u'.$msql->f('userid')];
			$bao[$i]['qishu'] = $msql->f('qishu');
			$bao[$i]['abcd'] = $msql->f('abcd');
			$bao[$i]['wf'] = wf($tmp['f' . $msql->f('gid')],$tmp['b' . $msql->f('gid') . $msql->f('bid')],$tmp['s' . $msql->f('gid') . $msql->f('sid')],$tmp['c' . $msql->f('gid') . $msql->f('cid')],$tmp['p' . $msql->f('gid') . $msql->f('pid')]);
			$bao[$i]['peilv1'] = (float)$msql->f('peilv1');
			$bao[$i]['peilv2'] = (float)$msql->f('peilv2');
			$bao[$i]['je'] = (float)$msql->f('je');
			$bao[$i]['points'] = (float)$msql->f('points');
			
			$bao[$i]['xtype'] = transxtype($msql->f('xtype'));

			if($msql->f('z')==1){
			    $bao[$i]['rs'] = (float)($msql->f('peilv1')*$msql->f('je')-$msql->f('prize')-$msql->f('je')*(1-$msql->f('points')/100));
			}else if($msql->f('z')==3){
			    $bao[$i]['rs'] = (float)($msql->f('peilv2')*$msql->f('je')-$msql->f('je')*(1-$msql->f('points')/100));
			}else if($msql->f('z')==2){
			    $bao[$i]['rs'] = 0;
			}else if($msql->f('z')==5){
			    $bao[$i]['rs'] = (float)($msql->f('prize')-$msql->f('je')+$msql->f('je')*$msql->f('points')/100);
			}else{
			    $bao[$i]['rs'] = (float)(0-$msql->f('je')*(1-$msql->f('points')/100));
			}
			$bao[$i]['con'] = $msql->f('content');
			$bao[$i]['rs'] = pr1($bao[$i]['rs']);
			$bao[$i]['mezc'] = $msql->f($myzcstr);
			$bao[$i]['mers'] = pr1(0-$bao[$i]['rs']*$bao[$i]['mezc']/100);
			for($j=8;$j>=$layer;$j--){
			   $bao[$i]['up'][$j]['uid'] = $msql->f('uid'.$j);
			   if($tmp['u'.$msql->f('uid'.$j)]=='' & $msql->f('uid'.$j)!=0){
			       $tmp['u'.$msql->f('uid'.$j)] = transu2($msql->f('uid'.$j));
			   }
			   $bao[$i]['up'][$j]['user'] = $tmp['u'.$msql->f('uid'.$j)];
			   $bao[$i]['up'][$j]['peilv1'] = (float)$msql->f('peilv1'.$j);
			   $bao[$i]['up'][$j]['peilv2'] = (float)$msql->f('peilv2'.$j);
			   $bao[$i]['up'][$j]['zc'] = $msql->f('zc'.$j);
			   $bao[$i]['up'][$j]['layer'] = $config['layer'][$j-1];
			   $bao[$i]['up'][$j]['points'] = $msql->f('points'.$j);
			}
		    $i++;
		}

		$bao = array("bao" => $bao, "plc" => $plc, "sql" => $sqls, "time" => $times,"pcount"=>$pcount,"rcount"=>$rcount,"page"=>$page);
		echo json_encode($bao);
		unset($bao);
		break;
		break;
	case "agentfl":
		$game = $_POST['game'];
		$uid = $_POST['uid'];
		$start = rdates($_POST['start']);
		$end = rdates($_POST['end']);
		$qishu = $_POST['qishu'];
		$types = $_POST['types'];
		$jsstatus = $_POST['jsstatus'];
		$username = trim($_POST['username']);
		$yk = $_POST['yk'];
		$je = $_POST['je'];
		if (!checkfid($uid) & $uid != $userid) {
			exit;
		}
		$game = explode('|', $game);
		foreach ($game as $k => $v) {
			if ($v == '') unset($game[$k]);
		}
		$gstr = ' gid in (' . implode(',', $game) . ')';
		if ($qishu == '' | !is_numeric($qishu)) {
			if ($start == $end) {
				$whi = " dates='$start'";
			} else {
				$whi = " dates>='$start' and dates<='$end' ";
			}
		} else {
			$whi = " qishu='$qishu' ";
		}
		if ($jsstatus != 'true' & $jsstatus != 'false') {
			$jsstatus = 'true';
		}
		if ($jsstatus == 'true') {
			$jsstatus = 1;
		} else {
			$jsstatus = 0;
		}
		if ($jsstatus == 1) {
			$zstr = " z not in(2,7,9) ";
		} else {
			$zstr = " z=9 ";
		}
		$msql->query("select layer,plc from `$tb_user` where userid='$uid'");
		$msql->next_record();
		$layer = $msql->f('layer');
		$plc = $msql->f('plc');
		if($layer==0){
		   $myid = '';
		   $myzcstr = 'zc' . $layer;
		   $mypointsstr = '';
		   $mypeilv1str = '';
		   $mypeilv2str = '';
		}else{
		   $myid = 'uid' . $layer;
		   $myzcstr = 'zc' . $layer;
		   $mypointsstr = 'points' . $layer;
		   $mypeilv1str = 'peilv1' . $layer;
		   $mypeilv2str = 'peilv2' . $layer;
		}
		$zcstrdown = '';
		for ($k = 8;$k > $layer;$k--) {
			$zcstrdown.= '-zc' . $k;
		}
		$zcstrup = $zcstrdown . '-zc' . $k;
		if ($layer < 8) {
			$uidstrdown = 'uid' . ($layer + 1);
			$pointsstrdown = 'points' . ($layer + 1);
			$peilv1strdown = 'peilv1' . ($layer + 1);
			$peilv2strdown = 'peilv2' . ($layer + 1);
		} else {
			$uidstrdown = 'userid';
			$pointsstrdown = 'points';
			$peilv1strdown = 'peilv1';
			$peilv2strdown = 'peilv2';
		}
		$starts = microtime(true);
		if($layer>0){
		   $sql = "select gid,bid from `$tb_lib` where  uid" . $layer . "='$uid' and $gstr group by gid,bid";
		}else{
		   $sql = "select gid,bid from `$tb_lib` where $gstr group by gid,bid";
		}
		$sqls = $sql;
		$fsql->query($sql);
		$tmp = array();
		$i = 0;
		while ($fsql->next_record()) {
			$bao[$i]['gid'] = $fsql->f('gid');
			$bao[$i]['bid'] = $fsql->f('bid');
			$gid = $bao[$i]['gid'];
			$bid = $bao[$i]['bid'];
			if ($tmp['g' . $gid] == '') {
				$tmp['g' . $gid] = transgame($gid, 'gname');
			}
			$bao[$i]['gname'] = $tmp['g' . $gid];
			$msql->query("select name from `$tb_bclass` where gid='$gid' and bid='$bid'");
			$msql->next_record();
			$bao[$i]['bname'] = $msql->f('name');
			
			if($layer>0){
				$join = " from `$tb_lib` where  uid" . $layer . "='$uid' and gid='$gid' and bid='$bid' and $whi and bs=1 and xtype!=2 ";
			$sql = "select count(id)
			               ,sum(je)
			               ,sum(je*points/100)
						   ,sum((100 $zcstrdown)*je/100)
						   ,sum(if($uidstrdown=0,(points*je/100),$pointsstrdown*je*(100 $zcstrdown)/(100*100)))
						   ,sum($myzcstr*je/100)
						   ,sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100)))
						   ,sum((100 $zcstrup)*je/100)
						   ,sum($mypointsstr*(100 $zcstrup)*je/(100*100))
						   ,sum($mypointsstr*$myzcstr*je/(100*100))
						    $join and $zstr";
			}else{
				$join = " from `$tb_lib` where  gid='$gid' and bid='$bid' and $whi and bs=1 and xtype!=2 ";
			    $sql = "select count(id)
			               ,sum(je)
			               ,sum(je*points/100)
						   ,sum((100 $zcstrdown)*je/100)
						   ,sum(if($uidstrdown=0,(points*je/100),$pointsstrdown*je*(100 $zcstrdown)/(100*100)))
						   ,sum($myzcstr*je/100)
						   ,sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100)))
						    $join and $zstr";
			}
			$msql->query($sql);
			$msql->next_record();
			$bao[$i]['uje'] = pr1($msql->f(1));
			$bao[$i]['zje'] = pr1($msql->f(3));
			$bao[$i]['upje'] = pr1($msql->f(3));
			$bao[$i]['zs'] = pr1($msql->f(0));
			$bao[$i]['mezc'] = pr1($msql->f(5));
			$bao[$i]['mezcp'] = getzcp($myzcstr,"$join and $zstr");
			if ($jsstatus == 1) {
				$bao[$i]['shui'] = pr1($msql->f(4));
				$bao[$i]['ushui'] = pr1($msql->f(2));				
				if($layer==0) $bao[$i]['meshui'] = pr1($msql->f(6));
				else  $bao[$i]['meshui'] = pr1($msql->f(9));
				$bao[$i]['sendje'] = pr1($msql->f(7));
				$bao[$i]['sendshui'] = pr1($msql->f(8));
			} else {
				$bao[$i]['shui'] = 0;
				$bao[$i]['ushui'] = 0;
				$bao[$i]['meshui'] = 0;
				$bao[$i]['sendje'] = 0;
				$bao[$i]['sendshui'] = 0;
			}
			//echo 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';exit;
			if ($jsstatus == 1) {
				if($layer>0){
				    $sql = "select sum(je*peilv1)
			               ,sum(if($uidstrdown=0,(peilv1*je),$peilv1strdown*(100 $zcstrdown)*je/100))
						   ,sum(if($uidstrdown=0,(peilv1*$myzcstr)*je/100,$peilv1strdown*$myzcstr*je/100))
						   ,sum((100 $zcstrup)*je*$mypeilv1str/100)
						   ,sum(prize) as sprize 
						    $join and z=1";
				}else{
			 	    $sql = "select sum(je*peilv1)
			               ,sum(if($uidstrdown=0,(peilv1*je),$peilv1strdown*(100 $zcstrdown)*je/100))
						   ,sum(if($uidstrdown=0,(peilv1*$myzcstr)*je/100,$peilv1strdown*$myzcstr*je/100))
						   ,sum(prize) as sprize 
						    $join and z=1";	
				}
				//echo $sql;
				$msql->query($sql);
				$msql->next_record();
				$bao[$i]['uzhong'] = pr1($msql->f(0))-pr1($msql->f('sprize'));
				$bao[$i]['zhong'] = pr1($msql->f(1))-pr1($msql->f('sprize'));
				$bao[$i]['mezhong'] = pr1($msql->f(2));
				$bao[$i]['sendzhong'] = pr1($msql->f(3));
                if($layer>0) {
                    $sql = "select sum(je*peilv2)
			               ,sum(if($uidstrdown=0,(peilv2*je),$peilv2strdown*(100 $zcstrdown)*je/100))
						   ,sum(if($uidstrdown=0,(peilv2*$myzcstr)*je/100,$peilv2strdown*$myzcstr*je/100))
						   ,sum((100 $zcstrup)*je*$mypeilv2str/100)
						    $join and z=3";
                }else{
                    $sql = "select sum(je*peilv2)
			               ,sum(if($uidstrdown=0,(peilv2*je),$peilv1strdown*(100 $zcstrdown)*je/100))
						   ,sum(if($uidstrdown=0,(peilv2*$myzcstr)*je/100,$peilv1strdown*$myzcstr*je/100))
						    $join and z=3";
                }
				//echo $sql;
				$msql->query($sql);
				$msql->next_record();
				$bao[$i]['uzhong']+= pr1($msql->f(0));
				$bao[$i]['zhong']+= pr1($msql->f(1));
				$bao[$i]['mezhong']+= pr1($msql->f(2));
				$bao[$i]['sendzhong']+= pr1($msql->f(3));
                if($layer>0) {
                    $sql = "select sum(prize)
			               ,sum(prize*(100 $zcstrdown)/100)
						   ,sum(prize*$myzcstr*je/100)
						   ,sum(prize*(100 $zcstrup)/100)
						    $join and z=5";
                }else{
                    $sql = "select sum(prize)
			               ,sum(prize*(100 $zcstrdown)/100)
						   ,sum(prize*$myzcstr*je/100)
						    $join and z=5";
                }
				//echo $sql;
				$msql->query($sql);
				$msql->next_record();
				$bao[$i]['uzhong']+= pr1($msql->f(0));
				$bao[$i]['zhong']+= pr1($msql->f(1));
				$bao[$i]['mezhong']+= pr1($msql->f(2));
				$bao[$i]['sendzhong']+= pr1($msql->f(3));
				$bao[$i]['yk'] = pr1($bao[$i]['upje'] - $bao[$i]['shui'] - $bao[$i]['zhong']);
				$bao[$i]['uyk'] = pr1($bao[$i]['uzhong'] + $bao[$i]['ushui'] - $bao[$i]['uje']);
				$bao[$i]['meyk'] = pr1($bao[$i]['mezc'] - $bao[$i]['meshui'] - $bao[$i]['mezhong']);
				$bao[$i]['sendyk'] = pr1($bao[$i]['sendzhong'] + $bao[$i]['sendshui'] - $bao[$i]['sendje']);
			} else {
				$bao[$i]['uzhong'] = 0;
				$bao[$i]['zhong'] = 0;
				$bao[$i]['mezhong'] = 0;
				$bao[$i]['sendzhong'] = 0;
				$bao[$i]['yk'] = 0;
				$bao[$i]['uyk'] = 0;
				$bao[$i]['meyk'] = 0;
				$bao[$i]['sendyk'] = 0;
			}
			$i++;
		}
		$times = round(microtime(true) - $starts, 3);
		sort($bao);
		$bao = array("bao" => $bao, "plc" => $plc, "sql" => $sqls, "time" => $times);
		echo json_encode($bao);
		unset($bao);
		break;
	case "agentsearch":
		$game = $_POST['game'];
		$uid = $_POST['uid'];
		$start = rdates($_POST['start']);
		$end = rdates($_POST['end']);
		$qishu = $_POST['qishu'];
		$types = $_POST['types'];
		$jsstatus = $_POST['jsstatus'];
		$username = trim($_POST['username']);
		$yk = $_POST['yk'];
		$je = $_POST['je'];
		if (!is_numeric($je) | $je == '') $je = 0;
		if (!is_numeric($yk) | $yk == '') $yk = 0;
		if ($je > 0) {
			$yk = 0;
		}
		if (!checkfid($uid) & $uid != $userid) {
			exit;
		}
		$game = explode('|', $game);
		foreach ($game as $k => $v) {
			if ($v == '') unset($game[$k]);
		}
		$gstr = ' gid in (' . implode(',', $game) . ')';
		if ($qishu == '' | !is_numeric($qishu)) {
			if ($start == $end) {
				$whi = " dates='$start'";
			} else {
				$whi = " dates>='$start' and dates<='$end' ";
			}
		} else {
			$whi = " qishu='$qishu' ";
		}
		if ($jsstatus != 'true' & $jsstatus != 'false') {
			$jsstatus = 'true';
		}
		if ($jsstatus == 'true') {
			$jsstatus = 1;
		} else {
			$jsstatus = 0;
		}
		if ($jsstatus == 1) {
			$zstr = " z not in(2,7,9) ";
		} else {
			$zstr = " z=9 ";
		}
		$zbao = array();
		$msql->query("select layer,plc from `$tb_user` where userid='$uid'");
		$msql->next_record();
		$layer = $msql->f('layer');
		$start = microtime();
		$bao = getuser($uid, $layer);
		$cb = count($bao);
		$cg = count($game);
		for ($i = 0;$i < $cb;$i++) {
			$join = " from `$tb_lib` where  userid='" . $bao[$i]['userid'] . "' and $gstr and $whi and bs=1 and xtype!=2 ";
			$sql = "select sum(je)
				               ,sum(je*points/100)
							   ,count(id) 
							   $join and $zstr ";
			$msql->query($sql);
			$msql->next_record();
			if ($je > 0 & $msql->f(0) < $je) {
				unset($bao[$i]);
				continue;
			}
			$bao[$i]['je'] = pr1($msql->f(0));
			$bao[$i]['zs'] = pr1($msql->f(2));
			if ($jsstatus == 1) {

			$bao[$i]['shui'] = pr1($msql->f(1));
			$msql->query("select sum(peilv1*je),sum(prize) $join and z=1 ");
			$msql->next_record();
			$bao[$i]['zhong'] = pr1($msql->f(0))-pr1($msql->f(1));
			$msql->query("select sum(peilv2*je) $join and z=3");
			$msql->next_record();
			$bao[$i]['zhong']+= pr1($msql->f(0));
			$sql = "select sum(prize) $join and gid=100 and z=5 ";
			$msql->query($sql);
			$msql->next_record();
			$bao[$i]['zhong']+= pr1($msql->f(0));
			$bao[$i]['yk'] = pr1($bao[$i]['zhong'] + $bao[$i]['shui'] - $bao[$i]['je']);
			}else{
			   $bao[$i]['shui']= 0;
			   $bao[$i]['zhong']=0;
			   $bao[$i]['yk'] =0;
			   
			}
			if ($yk > 0 & $bao[$i]['yk'] < $yk) {
				unset($bao[$i]);
				continue;
			}
			if ($yk < 0 & $bao[$i]['yk'] > $yk) {
				unset($bao[$i]);
				continue;
			}
		}
		sort($bao);
		$bao = array("bao" => $bao, "plc" => $plc, "sql" => $sqls, "status" => 1);
		echo json_encode($bao);
		unset($bao);
		break;
	case "agentsearchgame":
		$game = $_POST['game'];
		$uid = $_POST['uid'];
		$uidstr = $_POST['uidstr'];
		$start = rdates($_POST['start']);
		$end = rdates($_POST['end']);
		$qishu = $_POST['qishu'];
		$types = $_POST['types'];
		$jsstatus = $_POST['jsstatus'];
		$username = trim($_POST['username']);
		$yk = $_POST['yk'];
		$je = $_POST['je'];
		if (!is_numeric($je) | $je == '') $je = 0;
		if (!is_numeric($yk) | $yk == '') $yk = 0;
		if ($je > 0) {
			$yk = 0;
		}
		if (!checkfid($uid) & $uid != $userid) {
			exit;
		}
		$uidarr = explode('|',$uidstr);
		foreach ($uidarr as $k => $v) {
			if ($v == '') unset($uidarr[$k]);
		}
		sort($uidarr);
		$game = explode('|', $game);
		foreach ($game as $k => $v) {
			if ($v == '') unset($game[$k]);
		}
		$gstr = ' gid in (' . implode(',', $game) . ')';
		if ($qishu == '' | !is_numeric($qishu)) {
			if ($start == $end) {
				$whi = " dates='$start'";
			} else {
				$whi = " dates>='$start' and dates<='$end' ";
			}
		} else {
			$whi = " qishu='$qishu' ";
		}
		if ($jsstatus != 'true' & $jsstatus != 'false') {
			$jsstatus = 'true';
		}
		if ($jsstatus == 'true') {
			$jsstatus = 1;
		} else {
			$jsstatus = 0;
		}
		if ($jsstatus == 1) {
			$zstr = " z not in(2,7,9) ";
		} else {
			$zstr = " z=9 ";
		}
		$zbao = array();
		$msql->query("select layer,plc from `$tb_user` where userid='$uid'");
		$msql->next_record();
		$layer = $msql->f('layer');
		$start = microtime();
		$bao = getuser($uid, $layer);
		$cb = count($bao);
		sort($game);
		$cg = count($game);
		
	for($j=0;$j<$cg;$j++){
		$gstr = " gid='".$game[$j]."' ";
		$tbao = $bao;	
		for ($i = 0;$i < $cb;$i++) {
			
			if(!in_array($tbao[$i]['userid'] ,$uidarr)) {
				unset($tbao[$i]);
				continue;
			}
			$join = " from `$tb_lib` where  userid='" . $tbao[$i]['userid'] . "' and $gstr and $whi and bs=1 and xtype!=2 ";
			$sql = "select sum(je)
				               ,sum(je*points/100)
							   ,count(id) 
							   $join and $zstr ";
							
			$msql->query($sql);
			$msql->next_record();
			if($msql->f(2)==0){
								unset($tbao[$i]);
				continue;
			}
			$tbao[$i]['je'] = pr1($msql->f(0));
			$tbao[$i]['zs'] = pr1($msql->f(2));
			$tbao[$i]['shui'] = pr1($msql->f(1));
			$msql->query("select sum(peilv1*je),sum(prize) $join and z=1 ");
			$msql->next_record();
			$tbao[$i]['zhong'] = pr1($msql->f(0))-pr1($msql->f(1));
			$msql->query("select sum(peilv2*je) $join and z=3");
			$msql->next_record();
			$tbao[$i]['zhong']+= pr1($msql->f(0));
			$sql = "select sum(prize) $join and gid=100 and z=5 ";
			$msql->query($sql);
			$msql->next_record();
			$tbao[$i]['zhong']+= pr1($msql->f(0));
			$tbao[$i]['yk'] = pr1($tbao[$i]['zhong'] + $tbao[$i]['shui'] - $tbao[$i]['je']);
		}
			sort($tbao);
			if (count($tbao) > 0) {
				$zbao[$j]['bao'] = $tbao;
				unset($tbao);
				$zbao[$j]['gid'] = $game[$j];
				$zbao[$j]['gname'] = transgame($game[$j], 'gname');
			}
	}
		sort($zbao);
		$bao = array("game" => $zbao, "plc" => $plc, "sql" => $sqls, "status" => 1);
		echo json_encode($bao);
		unset($bao);
		break;

	case "agentnew":
		$game = $_POST['game'];
		$uid = $_POST['uid'];
		$start = rdates($_POST['start']);
		$end = rdates($_POST['end']);
		$qishu = $_POST['qishu'];
		$types = $_POST['types'];
		$jsstatus = $_POST['jsstatus'];
		$username = trim($_POST['username']);
		if ($username != '') {
			$msql->query("select userid,fid from `$tb_user` where username='$username'");
			$msql->next_record();
			$uid = $msql->f('fid');
			$uidson = $msql->f('userid');
			if ($msql->f('userid') == '') {
				$arr = array("status" => 0);
				echo json_encode($arr);
				exit;
			}
		}
		$yk = $_POST['yk'];
		$je = $_POST['je'];
		if (!checkfid($uid) & $uid != $userid) {
			exit;
		}
		$game = explode('|', $game);
		foreach ($game as $k => $v) {
			if ($v == '') unset($game[$k]);
		}
		$gstr = ' gid in (' . implode(',', $game) . ')';
		if ($qishu == '' | !is_numeric($qishu)) {
			if ($start == $end) {
				$whi = " dates='$start'";
			} else {
				$whi = " dates>='$start' and dates<='$end' ";
			}
		} else {
			$whi = " qishu='$qishu' ";
		}
		if ($jsstatus != 'true' & $jsstatus != 'false') {
			$jsstatus = 'true';
		}
		if ($jsstatus == 'true') {
			$jsstatus = 1;
		} else {
			$jsstatus = 0;
		}
		if ($jsstatus == 1) {
			$zstr = " z not in(2,7,9) ";
		} else {
			$zstr = " z=9 ";
		}
		$bao = topuser($uid);
		//print_r($bao);
		$cb = count($bao);
		$cg = count($game);
		$zbao = array();
		$msql->query("select layer,plc from `$tb_user` where userid='$uid'");
		$msql->next_record();
		$layer = $msql->f('layer');
		$plc = $msql->f('plc');
		if($layer==0){
		   $myid = '';
		   $myzcstr = 'zc' . $layer;
		   $mypointsstr = '';
		   $mypeilv1str = '';
		   $mypeilv2str = '';
		}else{
		   $myid = 'uid' . $layer;
		   $myzcstr = 'zc' . $layer;
		   $mypointsstr = 'points' . $layer;
		   $mypeilv1str = 'peilv1' . $layer;
		   $mypeilv2str = 'peilv2' . $layer;
		}
		$start = microtime();
		for ($i = 0;$i < $cb;$i++) {
            if ($uidson != '' & $uidson != $bao[$i]['userid']) {
                unset($bao[$i]);
                continue;
            }
            if ($layer < 8) {
                $uidstrdown = 'uid' . ($layer + 1);
                $pointsstrdown = 'points' . ($layer + 1);
                $peilv1strdown = 'peilv1' . ($layer + 1);
                $peilv2strdown = 'peilv2' . ($layer + 1);
            } else {
                $uidstrdown = 'userid';
                $pointsstrdown = 'points';
                $peilv1strdown = 'peilv1';
                $peilv2strdown = 'peilv2';
            }
            $zcstrdown = '';
            for ($k = 8; $k >= $bao[$i]['layer']; $k--) {
                $zcstrdown .= '-zc' . $k;
            }
            $zcstrup = $zcstrdown . '-zc' . $k;
            if ($bao[$i]['ifagent'] == '0') {
                $join = " from `$tb_lib` where  userid='" . $bao[$i]['userid'] . "' and $gstr and $whi and bs=1 and xtype!=2 ";
                $sql = "select sum(je)
				               ,sum(je*points/100)
							   ,count(id) 
							   $join and $zstr ";
                $sqls = $sql;
                $msql->query($sql);
                $msql->next_record();
                $bao[$i]['upje'] = pr1($msql->f(0));
                $bao[$i]['zje'] = pr1($msql->f(0));
                $bao[$i]['uje'] = $bao[$i]['zje'];
                $bao[$i]['zs'] = pr0($msql->f(2));
                //$bao[$i]['zje'] = $sqls;
                if ($bao[$i]['upje'] == 0) {
                    unset($bao[$i]);
                    continue;
                }

                if ($jsstatus == 1) {
                    $bao[$i]['shui'] = pr1($msql->f(1));
                    $msql->query("select sum(peilv1*je),sum(prize) $join and z=1 ");
                    $msql->next_record();
                    $bao[$i]['zhong'] = pr1($msql->f(0))-pr1($msql->f(1));
                    $msql->query("select sum(peilv2*je) $join and z=3");
                    $msql->next_record();
                    $bao[$i]['zhong'] += pr1($msql->f(0));
                    $sql = "select sum(prize) $join and gid=100 and z=5 ";
                    $msql->query($sql);
                    $msql->next_record();
                    $bao[$i]['zhong'] += pr1($msql->f(0));
                    $bao[$i]['yk'] = pr1($bao[$i]['upje'] - $bao[$i]['shui'] - $bao[$i]['zhong']);
                    $bao[$i]['uyk'] = 0 - $bao[$i]['yk'];
                    $bao[$i]['ushui'] = $bao[$i]['shui'];
                    $bao[$i]['uzhong'] = $bao[$i]['zhong'];
                } else {

                    $bao[$i]['shui'] = 0;
                    $bao[$i]['zhong'] = 0;
                    $bao[$i]['yk'] = 0;
                    $bao[$i]['uyk'] = 0;
                    $bao[$i]['ushui'] = 0;
                    $bao[$i]['uzhong'] = 0;
                }
                $bao[$i]['ttype'] = 0;
            } else {
                $join = " from `$tb_lib` where $gstr and (userid='{$bao[$i]['userid']}' or $uidstrdown='{$bao[$i]['userid']}') and $whi and bs=1 and xtype!=2 ";
                $sql = "select sum((100" . $zcstrdown . ")*je/100)
				               ,count(id)
							   ,sum(if($uidstrdown=0,(points*je/100),$pointsstrdown*je*(100 $zcstrdown)/(100*100)))
							   ,sum(je)
							   ,sum(je*points/100) $join and $zstr ";
                $sqls = $sql;
                $msql->query($sql);
                $msql->next_record();
                $bao[$i]['zs'] = pr0($msql->f(1));
                $bao[$i]['upje'] = pr1($msql->f(0));
                $bao[$i]['uje'] = pr1($msql->f(3));
                $bao[$i]['ushui'] = pr1($msql->f(4));
                //$bao[$i]['uje']  = $sqls;
                if ($bao[$i]['zs'] == 0) {
                    unset($bao[$i]);
                    continue;
                }
                if ($jsstatus == 1) {
                    $bao[$i]['shui'] = pr1($msql->f(2));
                    $sql = "select sum(if($uidstrdown=0,(peilv1*je),$peilv1strdown*(100 $zcstrdown)*je/100)),sum(prize) $join and z=1";
                    $msql->query($sql);
                    $msql->next_record();
                    $bao[$i]['zhong'] = pr1($msql->f(0))-pr1($msql->f(1));
                    $sql = "select sum(if($uidstrdown=0,(peilv2*je),$peilv2strdown*(100 $zcstrdown)*je/100)) $join and z=3";
                    $msql->query($sql);
                    $msql->next_record();
                    $bao[$i]['zhong'] += pr1($msql->f(0));
                    $sql = "select sum(prize*(100 $zcstrdown)/100) $join and gid=100 and z=5";
                    $msql->query($sql);
                    $msql->next_record();
                    $bao[$i]['zhong'] += pr1($msql->f(0));
                    $bao[$i]['yk'] = pr1($bao[$i]['upje'] - $bao[$i]['shui'] - $bao[$i]['zhong']);
                    $sql = "select sum(peilv1*je),sum(prize) $join and z=1";
                    $msql->query($sql);
                    $msql->next_record();
                    $bao[$i]['uzhong'] = pr1($msql->f(0))-pr1($msql->f(1));
                    $sql = "select sum(peilv2*je) $join and z=3";
                    $msql->query($sql);
                    $msql->next_record();
                    $bao[$i]['uzhong'] += pr1($msql->f(0));
                    $sql = "select sum(prize) $join and gid=100 and z=5";
                    $msql->query($sql);
                    $msql->next_record();
                    $bao[$i]['uzhong'] += pr1($msql->f(0));
                    $bao[$i]['uyk'] = pr1($bao[$i]['uzhong'] + $bao[$i]['ushui'] - $bao[$i]['uje']);
                } else {
                    $bao[$i]['shui'] = 0;
                    $bao[$i]['zhong'] = 0;
                    $bao[$i]['yk'] = 0;
                    $bao[$i]['uyk'] = 0;
                    $bao[$i]['ushui'] = 0;
                    $bao[$i]['uzhong'] = 0;
                }
            }
            if ($uidstrdown == 'userid') {
                $join = " from `$tb_lib` where $gstr and userid='{$bao[$i]['userid']}' and $whi and bs=1 and xtype!=2 ";
            } else {
                $join = " from `$tb_lib` where $gstr and (userid='{$bao[$i]['userid']}' or $uidstrdown='{$bao[$i]['userid']}') and $whi and bs=1 and xtype!=2 ";
            }

            if ($layer > 0){
                $sql = "select sum($myzcstr*je/100)
			               ,sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100)))
			               ,sum($mypointsstr*$myzcstr*je/(100*100))  
						   $join and $zstr";
            }else{
                $sql = "select sum($myzcstr*je/100)
			               ,sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100)))
			               ,sum(points1*$myzcstr*je/(100*100))  
						   $join and $zstr";
            }
			$msql->query($sql);
			$msql->next_record();
			$bao[$i]['mezc'] = pr1($msql->f(0));
			$bao[$i]['mezcp'] = getzcp($myzcstr,"$join and $zstr");
			
			if ($jsstatus == 1) {
				if($layer==0) $bao[$i]['meshui'] = pr1($msql->f(1));
                else $bao[$i]['meshui'] = pr1($msql->f(2));
				$sql = "select sum(if($uidstrdown=0,(peilv1*$myzcstr)*je/100,$peilv1strdown*$myzcstr*je/100)) $join and z=1";
				$msql->query($sql);
				$msql->next_record();
				$bao[$i]['mezhong'] = pr1($msql->f(0));
				$sql = "select sum(if($uidstrdown=0,(peilv2*$myzcstr)*je/100,$peilv2strdown*$myzcstr*je/100)) $join and z=3";
				$msql->query($sql);
				$msql->next_record();
				$bao[$i]['mezhong']+= pr1($msql->f(0));
				$sql = "select sum(prize*$myzcstr/100) $join and gid=100 and z=5";
				$msql->query($sql);
				$msql->next_record();
				$bao[$i]['mezhong']+= pr1($msql->f(0));
				$bao[$i]['meyk'] = pr1($bao[$i]['mezc'] - $bao[$i]['meshui'] - $bao[$i]['mezhong']);
			} else {
				$bao[$i]['meshui'] = 0;
				$bao[$i]['mezhong'] = 0;
				$bao[$i]['meyk'] = 0;
			}
			if ($uidstrdown == 'userid') {
				$join = " from `$tb_lib` where $gstr and userid='{$bao[$i]['userid']}' and $whi and bs=1 and xtype!=2 ";
			} else {
				$join = " from `$tb_lib` where $gstr and (userid='{$bao[$i]['userid']}' or $uidstrdown='{$bao[$i]['userid']}') and $whi and bs=1 and xtype!=2 ";
			}
		  if($layer>0){	
			$sql = "select sum((100 $zcstrup)*je/100)
			              ,sum($mypointsstr*(100 $zcstrup)*je/(100*100)) 
						  $join and $zstr ";
			$msql->query($sql);
			$msql->next_record();
			$bao[$i]['sendje'] = pr1($msql->f(0));
			if ($jsstatus == 1) {
				$bao[$i]['sendshui'] = pr1($msql->f(1));
				$sql = "select sum((100 $zcstrup)*je*$mypeilv1str/100),sum(prize)  $join and z=1";
				$msql->query($sql);
				$msql->next_record();
				$bao[$i]['sendzhong'] = pr1($msql->f(0))-pr1($msql->f(1));
				$sql = "select sum((100 $zcstrup)*je*$mypeilv2str/100) $join and z=3 ";
				$msql->query($sql);
				$msql->next_record();
				$bao[$i]['sendzhong']+= pr1($msql->f(0));
				$sql = "select sum(prize*(100 $zcstrup)/100) $join and z=5 ";
				$msql->query($sql);
				$msql->next_record();
				$bao[$i]['sendzhong']+= pr1($msql->f(0));
				$bao[$i]['sendyk'] = pr1($bao[$i]['sendshui'] + $bao[$i]['sendzhong'] - $bao[$i]['sendje']);
			} else {
				$bao[$i]['sendshui'] = 0;
				$bao[$i]['sendzhong'] = 0;
				$bao[$i]['sendyk'] = 0;
			}
		  }
		}
		$bao = array_values($bao);
	  if($layer>0){	
		$join = " from `$tb_lib` where userid='$uid' and $gstr and $whi and bs=1 and xtype!=2 ";
		$sql = "select sum(je),count(id),sum(je*points/100) $join and $zstr";
		$sqls = $sql;
		$msql->query($sql);
		$msql->next_record();
		if ($msql->f(1) > 0) {
			$i = count($bao);
			$bao[$i]['username'] = "fly1";			
			$bao[$i]['fly'] = 1;
			$bao[$i]['userid'] = $uid;
			$fsql->query("select * from `$tb_user` where userid='$uid'");
			$fsql->next_record();
			if (transuser($uid, 'fudong') == 1) {
				$bao[$i]['username'] = transu($uid) . "-自投";
				$bao[$i]['layername'] = "自投";
				
			} else {
				$bao[$i]['username'] = transu($uid) . "-内补";
				$bao[$i]['layername'] = "内补";
			}
			$bao[$i]['user'] = $fsql->f('username');
			$bao[$i]['name'] = $fsql->f('name');
			$bao[$i]['money'] = $fsql->f('money')+$fsql->f('kmoney');
			$bao[$i]['zs'] = pr0($msql->f(1));
			$bao[$i]['mezc'] = pr1($msql->f(0));
			$bao[$i]['mezcp'] = '-100%';
			$bao[$i]['uje'] = $bao[$i]['mezc'];
			if ($jsstatus == 1) {
				$bao[$i]['meshui'] = pr1($msql->f(2));
				$msql->query("select sum(peilv1*je) $join and z=1");
				$msql->next_record();
				$bao[$i]['mezhong'] = pr1($msql->f(0));
				$msql->query("select sum(peilv2*je) $join and z=3");
				$msql->next_record();
				$bao[$i]['mezhong']+= pr1($msql->f(0));
				$msql->query("select sum(prize) $join and z=5");
				$msql->next_record();
				$bao[$i]['mezhong']+= pr1($msql->f(0));
				$bao[$i]['meyk'] = pr1($bao[$i]['meshui'] + $bao[$i]['mezhong'] - $bao[$i]['mezc']);
				$bao[$i]['sendje'] = $bao[$i]['mezc'];
				$bao[$i]['sendshui'] = $bao[$i]['meshui'];
				$bao[$i]['sendzhong'] = $bao[$i]['mezhong'];
				$bao[$i]['sendyk'] = pr1($bao[$i]['meshui'] + $bao[$i]['mezhong'] - $bao[$i]['mezc']);				
				$bao[$i]['meshui'] = 0 - $bao[$i]['meshui'];
				$bao[$i]['mezhong'] = 0 - $bao[$i]['mezhong'];
			} else {
				$bao[$i]['meshui'] = 0;
				$bao[$i]['mezhong'] = 0;
				$bao[$i]['meyk'] = 0;
				$bao[$i]['sendje'] = 0;
				$bao[$i]['sendshui'] = 0;
				$bao[$i]['sendzhong'] = 0;
				$bao[$i]['sendyk'] = 0;
				$bao[$i]['meshui'] = 0;
				$bao[$i]['mezhong'] = 0;
			}
			$bao[$i]['mezc'] = 0 - $bao[$i]['mezc'];
			$bao[$i]['upje'] = 0;
			$bao[$i]['zje'] = 0;
			$bao[$i]['shui'] = 0;
			$bao[$i]['zhong'] = 0;
			$bao[$i]['yk'] = 0;
			//$bao[$i]['uje'] = 0;
			$bao[$i]['ushui'] = 0;
			$bao[$i]['uzhong'] = 0;
			$bao[$i]['uyk'] = 0;
			$bao[$i]['ttype'] = 1;
			$bao[$i]['ifagent'] = 0;
		}
	  }
		$join = " from `$tb_lib` where userid='$uid' and $gstr and $whi and bs=1 and xtype=2 ";
		$sql = "select sum(je),count(id),sum(je*points/100) $join and $zstr";
		$sqls = $sql;
		$msql->query($sql);
		$msql->next_record();
		if ($msql->f(1) > 0) {
			$i = count($bao);
			$bao[$i]['username'] = "fly2";
			$bao[$i]['fly'] = 2;
			$bao[$i]['userid'] = $uid;
			$fsql->query("select * from `$tb_user` where userid='$uid'");
			$fsql->next_record();
				$bao[$i]['username'] = transu($uid) . "-外补";
				$bao[$i]['layername'] = "外补";		
			$bao[$i]['user'] = $fsql->f('username');
			$bao[$i]['name'] = $fsql->f('name');
			$bao[$i]['money'] = $fsql->f('money')+$fsql->f('kmoney');

			$bao[$i]['zs'] = pr0($msql->f(1));
			$bao[$i]['mezc'] = pr1($msql->f(0));
			$bao[$i]['mezcp'] = '-100%';
			$bao[$i]['uje'] = $bao[$i]['mezc'];
			if ($jsstatus == 1) {
				$bao[$i]['meshui'] = pr1($msql->f(2));
				$msql->query("select sum(peilv1*je) $join and z=1 ");
				$msql->next_record();
				$bao[$i]['mezhong'] = pr1($msql->f(0));
				$msql->query("select sum(peilv2*je) $join and z=3");
				$msql->next_record();
				$bao[$i]['mezhong']+= pr1($msql->f(0));
				$msql->query("select sum(prize) $join and z=5");
				$msql->next_record();
				$bao[$i]['mezhong']+= pr1($msql->f(0));
				$bao[$i]['meyk'] = pr1($bao[$i]['meshui'] + $bao[$i]['mezhong'] - $bao[$i]['mezc']);
			} else {
				$bao[$i]['meshui'] = 0;
				$bao[$i]['mezhong'] = 0;
				$bao[$i]['meyk'] =0;
			}
			$bao[$i]['sendje'] = 0;
			$bao[$i]['sendshui'] = 0;
			$bao[$i]['sendzhong'] = 0;
			$bao[$i]['mezc'] = 0 - $bao[$i]['mezc'];
			$bao[$i]['meshui'] = 0- $bao[$i]['meshui'];
			$bao[$i]['upje'] = 0;
			$bao[$i]['zje'] = 0;
			$bao[$i]['shui'] = 0;
			$bao[$i]['zhong'] = 0;
			$bao[$i]['yk'] = 0;
			//$bao[$i]['uje'] = 0;
			$bao[$i]['ushui'] = 0;
			$bao[$i]['uzhong'] = 0;
			$bao[$i]['uyk'] = 0;
			$bao[$i]['sendyk'] = 0;
			$bao[$i]['ttype'] = 2;
			$bao[$i]['ifagent'] = 0;
		}
		//sort($bao);
		$bao = array("bao" => $bao, "plc" => $plc, "sql" => $sqls, "status" => 1);
		echo json_encode($bao);
		unset($bao);
		break;
	case "agentnewgame":
		$game = $_POST['game'];
		$uid = $_POST['uid'];
		$start = rdates($_POST['start']);
		$end = rdates($_POST['end']);
		$qishu = $_POST['qishu'];
		$types = $_POST['types'];
		$jsstatus = $_POST['jsstatus'];
		$username = trim($_POST['username']);
		if ($username != '') {
			$msql->query("select userid,fid from `$tb_user` where username='$username'");
			$msql->next_record();
			$uid = $msql->f('fid');
			$uidson = $msql->f('userid');
			if ($msql->f('userid') == '') {
				$arr = array("status" => 0);
				echo json_encode($arr);
				exit;
			}
		}
		$yk = $_POST['yk'];
		$je = $_POST['je'];
		if (!checkfid($uid) & $uid != $userid) {
			exit;
		}
		$game = explode('|', $game);
		foreach ($game as $k => $v) {
			if ($v == '') unset($game[$k]);
		}
		array_unique($game);
		$gstr = ' gid in (' . implode(',', $game) . ')';
		if ($qishu == '' | !is_numeric($qishu)) {
			if ($start == $end) {
				$whi = " dates='$start'";
			} else {
				$whi = " dates>='$start' and dates<='$end' ";
			}
		} else {
			$whi = " qishu='$qishu' ";
		}
		if ($jsstatus != 'true' & $jsstatus != 'false') {
			$jsstatus = 'true';
		}
		if ($jsstatus == 'true') {
			$jsstatus = 1;
		} else {
			$jsstatus = 0;
		}
		if ($jsstatus == 1) {
			$zstr = " z not in(2,7,9) ";
		} else {
			$zstr = " z=9 ";
		}
		$bao = topuser($uid);
		//print_r($bao);
		$cb = count($bao);
		sort($game);
		$cg = count($game);
		$zbao = array();
		$msql->query("select layer,plc from `$tb_user` where userid='$uid'");
		$msql->next_record();
		$layer = $msql->f('layer');
		$plc = $msql->f('plc');
		if($layer==0){
		   $myid = '';
		   $myzcstr = 'zc' . $layer;
		   $mypointsstr = '';
		   $mypeilv1str = '';
		   $mypeilv2str = '';
		}else{
		   $myid = 'uid' . $layer;
		   $myzcstr = 'zc' . $layer;
		   $mypointsstr = 'points' . $layer;
		   $mypeilv1str = 'peilv1' . $layer;
		   $mypeilv2str = 'peilv2' . $layer;
		}
		$start = microtime();
		for ($j = 0;$j < $cg;$j++) {
			$gstr = " gid='" . $game[$j] . "' ";
			$tbao = $bao;
			for ($i = 0;$i < $cb;$i++) {
				if ($uidson != '' & $uidson != $tbao[$i]['userid']) {
					unset($tbao[$i]);
					continue;
				}
				if ($layer < 8) {
					$uidstrdown = 'uid' . ($layer + 1);
					$pointsstrdown = 'points' . ($layer + 1);
					$peilv1strdown = 'peilv1' . ($layer + 1);
					$peilv2strdown = 'peilv2' . ($layer + 1);
				} else {
					$uidstrdown = 'userid';
					$pointsstrdown = 'points';
					$peilv1strdown = 'peilv1';
					$peilv2strdown = 'peilv2';
				}
				$zcstrdown = '';
				for ($k = 8;$k >= $tbao[$i]['layer'];$k--) {
					$zcstrdown.= '-zc' . $k;
				}
				$zcstrup = $zcstrdown . '-zc' . $k;
				if ($tbao[$i]['ifagent'] == '0') {
					$join = " from `$tb_lib` where  userid='" . $tbao[$i]['userid'] . "' and $gstr and $whi and bs=1 and xtype!=2 ";
					$sql = "select sum(je)
				               ,sum(je*points/100)
							   ,count(id) 
							   $join and $zstr ";
					//echo $sql;exit;
					$msql->query($sql);
					$msql->next_record();
					$tbao[$i]['upje'] = pr1($msql->f(0));
					$tbao[$i]['zje'] = pr1($msql->f(0));
					if ($tbao[$i]['upje'] == 0) {
						unset($tbao[$i]);
						continue;
					}
					$tbao[$i]['uje'] = $tbao[$i]['zje'];
					$tbao[$i]['zs'] = pr0($msql->f(2));
					if ($jsstatus == 1) {
						$tbao[$i]['shui'] = pr1($msql->f(1));
						
						$msql->query("select sum(peilv1*je),sum(prize) $join and z=1 ");
						$msql->next_record();
						$tbao[$i]['zhong'] = pr1($msql->f(0))-pr1($msql->f(1));
						$msql->query("select sum(peilv2*je) $join and z=3");
						$msql->next_record();
						$tbao[$i]['zhong']+= pr1($msql->f(0));
						$sql = "select sum(prize) $join and gid=100 and z=5 ";
						$msql->query($sql);
						$msql->next_record();
						$tbao[$i]['zhong']+= pr1($msql->f(0));
						$tbao[$i]['yk'] = pr1($tbao[$i]['upje'] - $tbao[$i]['shui'] - $tbao[$i]['zhong']);
						$tbao[$i]['uyk'] = 0-$tbao[$i]['yk'];
						$tbao[$i]['ushui'] = $tbao[$i]['shui'];
						$tbao[$i]['uzhong'] = $tbao[$i]['zhong'];
					} else {
						$tbao[$i]['shui'] = 0;
						$tbao[$i]['zhong'] = 0;
						$tbao[$i]['yk'] = 0;
						$tbao[$i]['uyk'] = 0;
						$tbao[$i]['ushui'] = 0;
						$tbao[$i]['uzhong'] = 0;
					}
					$tbao[$i]['ttype'] = 0;
				} else {
					$join = " from `$tb_lib` where $gstr and (userid='{$tbao[$i]['userid']}' or $uidstrdown='{$tbao[$i]['userid']}') and $whi and bs=1 and xtype!=2 ";
					$sql = "select sum((100" . $zcstrdown . ")*je/100)
				               ,count(id)
							   ,sum(if($uidstrdown=0,(points*je/100),$pointsstrdown*je*(100 $zcstrdown)/(100*100)))
							   ,sum(je)
							   ,sum(je*points/100) $join and $zstr ";
					$sqls = $sql;
					$msql->query($sql);
					$msql->next_record();
					$tbao[$i]['zs'] = pr0($msql->f(1));
					$tbao[$i]['upje'] = pr1($msql->f(0));
					$tbao[$i]['uje'] = pr1($msql->f(3));
					$tbao[$i]['ushui'] = pr1($msql->f(4));
					if ($tbao[$i]['zs'] == 0) {
						//unset($tbao[$i]);
						continue;
					}
					if ($jsstatus == 1) {
						$tbao[$i]['shui'] = pr1($msql->f(2));
						$sql = "select sum(if($uidstrdown=0,(peilv1*je),$peilv1strdown*(100 $zcstrdown)*je/100)),sum(prize) $join and z=1";
						$msql->query($sql);
						$msql->next_record();
						$tbao[$i]['zhong'] = pr1($msql->f(0))-pr1($msql->f(1));
						$sql = "select sum(if($uidstrdown=0,(peilv2*je),$peilv2strdown*(100 $zcstrdown)*je/100)) $join and z=3";
						$msql->query($sql);
						$msql->next_record();
						$tbao[$i]['zhong']+= pr1($msql->f(0));
						$sql = "select sum(prize*(100 $zcstrdown)/100) $join and gid=100 and z=5";
						$msql->query($sql);
						$msql->next_record();
						$tbao[$i]['zhong']+= pr1($msql->f(0));
						$tbao[$i]['yk'] = pr1($tbao[$i]['upje'] - $tbao[$i]['shui'] - $tbao[$i]['zhong']);						
						$sql = "select sum(peilv1*je),sum(prize) $join and z=1";
						$msql->query($sql);
						$msql->next_record();
						$tbao[$i]['uzhong'] = pr1($msql->f(0))-pr1($msql->f(1));
						$sql = "select sum(peilv2*je) $join and z=3";
						$msql->query($sql);
						$msql->next_record();
						$tbao[$i]['uzhong']+= pr1($msql->f(0));
						$sql = "select sum(prize) $join and gid=100 and z=5";
						$msql->query($sql);
						$msql->next_record();
						$tbao[$i]['uzhong']+= pr1($msql->f(0));
						//$tbao[$i]['uje'] = "select sum(je),sum(je*points/100) $join and $zstr";
						$tbao[$i]['uyk'] = pr1($tbao[$i]['uzhong'] + $tbao[$i]['ushui'] - $tbao[$i]['uje']);
					} else {
						$tbao[$i]['shui'] = 0;
						$tbao[$i]['zhong'] = 0;
						$tbao[$i]['yk'] = 0;
						$tbao[$i]['uyk'] = 0;
						$tbao[$i]['ushui'] = 0;
						$tbao[$i]['uzhong'] = 0;
					$sql = "select sum(je) $join and $zstr";
					$msql->query($sql);
					$msql->next_record();
					$tbao[$i]['uje'] = pr1($msql->f(0));
					}
				}
				if ($uidstrdown == 'userid') {
					$join = " from `$tb_lib` where $gstr and userid='{$tbao[$i]['userid']}' and $whi and bs=1 and xtype!=2 ";
				} else {
					$join = " from `$tb_lib` where $gstr and (userid='{$tbao[$i]['userid']}' or $uidstrdown='{$tbao[$i]['userid']}') and $whi and bs=1 and xtype!=2 ";
				}

                if ($layer > 0){
                    $sql = "select sum($myzcstr*je/100)
			               ,sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100))) 
			               ,sum($mypointsstr*$myzcstr*je/(100*100))  
						   $join and $zstr";
                }else{
                    $sql = "select sum($myzcstr*je/100)
			               ,sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100))) 
			               ,sum(points1*$myzcstr*je/(100*100))  
						   $join and $zstr";
                }
				$msql->query($sql);
				$msql->next_record();
				$tbao[$i]['mezc'] = pr1($msql->f(0));
				$tbao[$i]['mezcp'] = getzcp($myzcstr,"$join and $zstr");
				if ($jsstatus == 1) {
					if($layer==0) $tbao[$i]['meshui'] = pr1($msql->f(1));
					else  $tbao[$i]['meshui'] = pr1($msql->f(2));
					$sql = "select sum(if($uidstrdown=0,(peilv1*$myzcstr)*je/100,$peilv1strdown*$myzcstr*je/100)) $join and z=1";
					$msql->query($sql);
					$msql->next_record();
					$tbao[$i]['mezhong'] = pr1($msql->f(0));
					$sql = "select sum(if($uidstrdown=0,(peilv2*$myzcstr)*je/100,$peilv2strdown*$myzcstr*je/100)) $join and z=3";
					$msql->query($sql);
					$msql->next_record();
					$tbao[$i]['mezhong']+= pr1($msql->f(0));
					$sql = "select sum(prize*$myzcstr/100) $join and gid=100 and z=5";
					$msql->query($sql);
					$msql->next_record();
					$tbao[$i]['mezhong']+= pr1($msql->f(0));
					$tbao[$i]['meyk'] = pr1($tbao[$i]['mezc'] - $tbao[$i]['meshui'] - $tbao[$i]['mezhong']);
				} else {
					$tbao[$i]['meshui'] = 0;
					$tbao[$i]['mezhong'] = 0;
					$tbao[$i]['meyk'] = 0;
				}
				if ($uidstrdown == 'userid') {
					$join = " from `$tb_lib` where $gstr and userid='{$tbao[$i]['userid']}' and $whi and bs=1 and xtype!=2 ";
				} else {
					$join = " from `$tb_lib` where $gstr and (userid='{$tbao[$i]['userid']}' or $uidstrdown='{$tbao[$i]['userid']}') and $whi and bs=1 and xtype!=2 ";
				}
			if($layer>0){	
				$sql = "select sum((100 $zcstrup)*je/100)
			              ,sum($mypointsstr*(100 $zcstrup)*je/(100*100)) 
						  $join and $zstr ";
				$msql->query($sql);
				$msql->next_record();
				$tbao[$i]['sendje'] = pr1($msql->f(0));
				if ($jsstatus == 1) {
					$tbao[$i]['sendshui'] = pr1($msql->f(1));
					$sql = "select sum((100 $zcstrup)*je*$mypeilv1str/100),sum(prize)  $join and z=1";
					$msql->query($sql);
					$msql->next_record();
					$tbao[$i]['sendzhong'] = pr1($msql->f(0))-pr1($msql->f(1));
					$sql = "select sum((100 $zcstrup)*je*$mypeilv2str/100) $join and z=3 ";
					$msql->query($sql);
					$msql->next_record();
					$tbao[$i]['sendzhong']+= pr1($msql->f(0));
					$sql = "select sum(prize*(100 $zcstrup)/100) $join and z=5 ";
					$msql->query($sql);
					$msql->next_record();
					$tbao[$i]['sendzhong']+= pr1($msql->f(0));
					$tbao[$i]['sendyk'] = pr1($tbao[$i]['sendshui'] + $tbao[$i]['sendzhong'] - $tbao[$i]['sendje']);
				} else {
					$tbao[$i]['sendshui'] = 0;
					$tbao[$i]['sendzhong'] = 0;
					$tbao[$i]['sendyk'] = 0;
				}
			}
			}
			$tbao = array_values($tbao);
		 if($layer>0){	
			$join = " from `$tb_lib` where userid='$uid' and $gstr and $whi and bs=1 and xtype!=2 ";
			$msql->query("select sum(je),count(id),sum(je*points/100) $join and $zstr");
			$msql->next_record();
			if ($msql->f(1) > 0) {
				$i = count($tbao);
				$tbao[$i]['username'] = "fly1";
				$tbao[$i]['fly'] = 1;
				$tbao[$i]['userid'] = $uid;

			$fsql->query("select * from `$tb_user` where userid='$uid'");
			$fsql->next_record();
				if (transuser($uid, 'fudong') == 1) {
					$tbao[$i]['username'] = transu($uid) . "-自投";
				} else {
					$tbao[$i]['username'] = transu($uid) . "-内补";
				}
				$tbao[$i]['layername'] = "内补";		
			$tbao[$i]['user'] = $fsql->f('username');
			$tbao[$i]['name'] = $fsql->f('name');
			$tbao[$i]['money'] = $fsql->f('money')+$fsql->f('kmoney');
			
				$tbao[$i]['zs'] = pr0($msql->f(1));
				$tbao[$i]['mezc'] = pr1($msql->f(0));
				$tbao[$i]['mezcp'] = '-100%';
				$tbao[$i]['uje'] = $tbao[$i]['mezc'];
				if ($jsstatus == 1) {
					$tbao[$i]['meshui'] = pr1($msql->f(2));
					$msql->query("select sum(peilv1*je) $join and z=1");
					$msql->next_record();
					$tbao[$i]['mezhong'] = pr1($msql->f(0));
					$msql->query("select sum(peilv2*je) $join and z=3");
					$msql->next_record();
					$tbao[$i]['mezhong']+= pr1($msql->f(0));
					$msql->query("select sum(prize) $join and z=5");
					$msql->next_record();
					$tbao[$i]['mezhong']+= pr1($msql->f(0));
					$tbao[$i]['meyk'] = pr1($tbao[$i]['meshui'] + $tbao[$i]['mezhong'] - $tbao[$i]['mezc']);
					$tbao[$i]['sendje'] = $tbao[$i]['mezc'];
					$tbao[$i]['sendshui'] = $tbao[$i]['meshui'];
					$tbao[$i]['sendzhong'] = $tbao[$i]['mezhong'];
					$tbao[$i]['sendyk'] = pr1($tbao[$i]['meshui'] + $tbao[$i]['mezhong'] - $tbao[$i]['mezc']);
					$tbao[$i]['meshui'] = 0 - $tbao[$i]['meshui'];
					$tbao[$i]['mezhong'] = 0 - $tbao[$i]['mezhong'];
				} else {
					$tbao[$i]['meshui'] = 0;
					$tbao[$i]['mezhong'] = 0;
					$tbao[$i]['meyk'] = 0;
					$tbao[$i]['sendje'] = 0;
					$tbao[$i]['sendshui'] = 0;
					$tbao[$i]['sendzhong'] = 0;
					$tbao[$i]['sendyk'] = 0;
					$tbao[$i]['meshui'] = 0;
					$tbao[$i]['mezhong'] = 0;
				}
				$tbao[$i]['mezc'] = 0 - $tbao[$i]['mezc'];
				$tbao[$i]['upje'] = 0;
				$tbao[$i]['zje'] = 0;
				$tbao[$i]['shui'] = 0;
				$tbao[$i]['zhong'] = 0;
				$tbao[$i]['yk'] = 0;
				//$tbao[$i]['uje'] = 0;
				$tbao[$i]['ushui'] = 0;
				$tbao[$i]['uzhong'] = 0;
				$tbao[$i]['uyk'] = 0;
				$tbao[$i]['ttype'] = 1;
				$tbao[$i]['ifagent'] = 0;
			}
		 }
			$join = " from `$tb_lib` where userid='$uid' and $gstr and $whi and bs=1 and xtype=2 ";
			$msql->query("select sum(je),count(id),sum(je*points/100) $join and $zstr");
			$msql->next_record();
			if ($msql->f(1) > 0) {
				$i = count($tbao);
				$tbao[$i]['username'] = "fly2";
				$tbao[$i]['fly'] = 2;
				$tbao[$i]['userid'] = $uid;
				
			$fsql->query("select * from `$tb_user` where userid='$uid'");
			$fsql->next_record();
				$tbao[$i]['username'] = transu($uid) . "-外补";
				$tbao[$i]['layername'] = "外补";		
			$tbao[$i]['user'] = $fsql->f('username');
			$tbao[$i]['name'] = $fsql->f('name');
			$tbao[$i]['money'] = $fsql->f('money')+$fsql->f('kmoney');
			
				$tbao[$i]['zs'] = pr0($msql->f(1));
				$tbao[$i]['mezc'] = pr1($msql->f(0));
				$tbao[$i]['mezcp'] = '-100%';
				$tbao[$i]['uje'] = $tbao[$i]['mezc'];
				if ($jsstatus == 1) {
					$tbao[$i]['meshui'] = pr1($msql->f(2));
					$msql->query("select sum(peilv1*je) $join and z=1 ");
					$msql->next_record();
					$tbao[$i]['mezhong'] = pr1($msql->f(0));
					$msql->query("select sum(peilv2*je) $join and z=3");
					$msql->next_record();
					$tbao[$i]['mezhong']+= pr1($msql->f(0));
					$msql->query("select sum(prize) $join and z=5");
					$msql->next_record();
					$tbao[$i]['mezhong']+= pr1($msql->f(0));
					$tbao[$i]['meyk'] = pr1($tbao[$i]['meshui'] + $tbao[$i]['mezhong'] - $tbao[$i]['mezc']);
				} else {
					$tbao[$i]['meshui'] = 0;
					$tbao[$i]['mezhong'] = 0;
					$tbao[$i]['meyk'] = 0;
				}
				$tbao[$i]['sendje'] = 0;
				$tbao[$i]['sendshui'] = 0;
				$tbao[$i]['sendzhong'] = 0;
				$tbao[$i]['mezc'] = 0 - $tbao[$i]['mezc'];
				$tbao[$i]['meshui'] = 0 - $tbao[$i]['meshui'];
				$tbao[$i]['upje'] = 0;
				$tbao[$i]['zje'] = 0;
				$tbao[$i]['shui'] = 0;
				$tbao[$i]['zhong'] = 0;
				$tbao[$i]['yk'] = 0;
				//$tbao[$i]['uje'] = 0;
				$tbao[$i]['ushui'] = 0;
				$tbao[$i]['uzhong'] = 0;
				$tbao[$i]['uyk'] = 0;
				$tbao[$i]['sendyk'] = 0;
				$tbao[$i]['ttype'] = 2;
				$tbao[$i]['ifagent'] = 0;
			}
			//sort($tbao);
			if (count($tbao) > 0) {
				$zbao[$j]['bao'] = $tbao;
				unset($tbao);
				$zbao[$j]['gid'] = $game[$j];
				$zbao[$j]['gname'] = transgame($game[$j], 'gname');
			}
		}
		sort($zbao);
		$bao = array("game" => $zbao, "plc" => $plc, "sql" => $sqls, "status" => 1);
		//print_r($bao);exit;
		echo json_encode($bao);
		unset($bao);
		break;
    case "bao":
	    $jsstatus   = $_POST['jsstatus'];
        $bid   = $_POST['bid'];
        $cid   = $_POST['cid'];
        $sid   = $_POST['sid'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        $uid   = $_POST['uid'];
        $game  = $_POST['game'];
        $game  = explode('|', $game);
        array_pop($game);


        
        $gstr  = '(' . implode(',', $game) . ')';
        //$start = strtotime($start . ' ' . $config['editend']);
        //$end   = strtotime($end . ' ' . $config['editstart']) + 86400;
		
		//$start =sqltime($start);
		//$end =sqltime($end);
        $whi   = "  and dates>='$start' and dates<='$end' ";
        $yq    = " and xtype!=2 $whi and bs=1";
        $yq2   = " and xtype=2  $whi and bs=1";
        if (is_numeric($bid)) {
            $yq .= " and bid='$bid' ";
            $yq2 .= " and bid='$bid' ";
        }
        if (is_numeric($sid)) {
            $yq .= " and sid='$sid' ";
            $yq2 .= " and sid='$sid' ";
        }
        if (is_numeric($cid)) {
            $yq .= " and cid='$cid' ";
            $yq2 .= " and cid='$cid' ";
        }
        $cg    = count($game);
        $bao   = topuser($userid);
        $cb    = count($bao);
        $zbao  = array();
        $start = microtime();
        for ($j = 0; $j < $cg; $j++) {
            $join               = " from `$tb_lib` where  gid='" . $game[$j] . "' ";
			$jstr='';
			if($jsstatus==1){
				$jstr .= " and z!=9 ";
			}else if($jsstatus==0){
				$jstr .= " and z=9 ";
			}
            $ustr               = 'uid1';
            $gbao[$j][0]['gid'] = $game[$j];
            $msql->query("select gname,class from `$tb_game` where gid='{$game[$j]}'");
            $msql->next_record();
            $gbao[$j][0]['gname'] = $msql->f('gname');
            $gbao[$j][0]['style'] = $msql->f('class');
            for ($i = 0; $i < $cb; $i++) {
                $whi2 = " and ($ustr='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') $yq $jstr";
                $sql  = "select sum(je),count(id),sum(points*je/100) $join $whi2";
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['userzs']     = pr0($msql->f(1));
                $gbao[$j][$i]['userje']     = pr2($msql->f(0));
                $gbao[$j][$i]['userpoints'] = pr2($msql->f(2));
                if ($bao[$j][$i]['userzs'] == 0) {
                }
                $sql = "select sum(je*zc0/100),count(id),sum(if($ustr=0,points,points1)*je*zc0/(100*100)) $join and z!=2 and z!=7   $whi2 ";
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['zs']     = pr0($msql->f(1));
                $gbao[$j][$i]['upje']   = pr2($msql->f(0));
                $gbao[$j][$i]['points'] = pr2($msql->f(2));
                
                $sql = "select sum(if($ustr=0,peilv1,peilv11)*je*zc0/(100)) $join and z=1 $whi2 ";
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['zhong'] = pr2($msql->f(0));
                $sql                   = "select sum(if($ustr=0,peilv2,peilv21)*je*zc0/(100)) $join and z=3 $whi2  ";
                
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['zhong'] += pr2($msql->f(0));
                if ($game[$j] == 100) {
                    $sql = "select sum(prize*zc0/100) $join and z=5 $whi2";
                    $msql->query($sql);
                    $msql->next_record();
                    $gbao[$j][$i]['zhong'] += pr2($msql->f(0));
                }
                $gbao[$j][$i]['yk']       = pr2($gbao[$j][$i]['upje'] - $gbao[$j][$i]['points'] - $gbao[$j][$i]['zhong']);
                $gbao[$j][$i]['userid']   = $bao[$i]['userid'];
                $gbao[$j][$i]['username'] = $bao[$i]['username'];
                $gbao[$j][$i]['ifagent']  = $bao[$i]['ifagent'];
                $gbao[$j][$i]['layer']    = $bao[$i]['layer'];
                $zbao[$i]['userzs'] += $gbao[$j][$i]['userzs'];
                $zbao[$i]['userje'] += $gbao[$j][$i]['userje'];
                $zbao[$i]['userpoints'] += $gbao[$j][$i]['userpoints'];
                $zbao[$i]['zs'] += $gbao[$j][$i]['zs'];
                $zbao[$i]['upje'] += $gbao[$j][$i]['upje'];
                $zbao[$i]['points'] += $gbao[$j][$i]['points'];
                $zbao[$i]['zhong'] += $gbao[$j][$i]['zhong'];
                $zbao[$i]['yk'] += $gbao[$j][$i]['yk'];
                $zbao[$i]['userid']   = $bao[$i]['userid'];
                $zbao[$i]['username'] = $bao[$i]['username'];
                $zbao[$i]['ifagent']  = $bao[$i]['ifagent'];
                $zbao[$i]['layer']    = $bao[$i]['layer'];
            }
            
            $gbao[$j][$i]['username'] = "fly2";
            $gbao[$j][$i]['fly']      = 2;
            $gbao[$j][$i]['ifagent']  = 0;
            $gbao[$j][$i]['userid']   = $userid;
            $gbao[$j][$i]['username'] = "公司外补";
            $msql->query("select sum(je),count(id),sum(je*points/100) $join  and userid='$userid' and z!=2 and z!=7  $yq2 ");
            $msql->next_record();
            $gbao[$j][$i]['zs']     = pr0($msql->f(1));
            $gbao[$j][$i]['upje']   = pr2($msql->f(0));
            $gbao[$j][$i]['points'] = pr2($msql->f(2));
            $msql->query("select sum(peilv1*je)  $join  and userid='$userid' and z=1 $yq2 ");
            $msql->next_record();
            $gbao[$j][$i]['zhong'] = pr2($msql->f(0));
            $msql->query("select sum(peilv2*je)  $join  and userid='$userid' and z=3 $yq2 ");
            $msql->next_record();
            $gbao[$j][$i]['zhong'] += pr2($msql->f(0));
            
            $gbao[$j][$i]['yk']         = pr2($gbao[$j][$i]['points'] + $gbao[$j][$i]['zhong'] - $gbao[$j][$i]['upje']);
            $gbao[$j][$i]['upje']       = 0 - $gbao[$j][$i]['upje'];
            $gbao[$j][$i]['userje']     = 0;
            $gbao[$j][$i]['userzs']     = $gbao[$j][$i]['zs'];
            $gbao[$j][$i]['userpoints'] = 0;
            $zbao[$i]['username']       = "fly2";
            $zbao[$i]['zs'] += $gbao[$j][$i]['zs'];
            $zbao[$i]['upje'] += $gbao[$j][$i]['upje'];
            $zbao[$i]['points'] += $gbao[$j][$i]['points'];
            $zbao[$i]['zhong'] += $gbao[$j][$i]['zhong'];
            $zbao[$i]['yk'] += $gbao[$j][$i]['yk'];
            $zbao[$i]['userje']     = 0;
            $zbao[$i]['userzs']     = $zbao[$i]['zs'];
            $zbao[$i]['userpoints'] = 0;
            $zbao[$i]['fly']        = 2;
            $zbao[$i]['ifagent']    = 0;
            $zbao[$i]['userid']     = $userid;
            $zbao[$i]['username']   = $gbao[$j][$i]['username'];
        }
        $end = microtime();
        sort($bao);
        $bao = array(
            "gbao" => $gbao,
            "zbao" => $zbao
        );
        echo json_encode($bao);
        unset($bao);
        unset($zbao);
        break;

    case "getfid":
        $uid = $_POST['uid'];
        if (!checkfid($uid)) exit;
        if($uid==$userid){
            $arr = array('1', $userid, 0, 0);
            echo json_encode($arr);
            exit;
        }
        $msql->query("select fid from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $msql->query("select userid,layer,username from `$tb_user` where userid='{$msql->f('fid') }'");
        $msql->next_record();
        $arr = array('1', $msql->f('userid'), $msql->f('layer'), $msql->f('username'));
        echo json_encode($arr);
        break;
    case "agent":
	    $jsstatus   = $_POST['jsstatus'];
        $bid   = $_POST['bid'];
        $cid   = $_POST['cid'];
        $sid   = $_POST['sid'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        $uid   = $_POST['uid'];
        $game  = $_POST['game'];
        $game  = explode('|', $game);
        array_pop($game);
        $gstr  = '(' . implode(',', $game) . ')';
        //$start = strtotime($start . ' ' . $config['editend']);
        //$end   = strtotime($end . ' ' . $config['editstart']) + 86400;
		//$start =sqltime($start);
		//$end =sqltime($end);
        $whi   = "  and dates>='$start' and dates<='$end' ";
        $yq    = " and xtype!=2 $whi and bs=1";
        $yq2   = " and xtype=2  $whi and bs=1";
        if (is_numeric($bid)) {
            $yq .= " and bid='$bid' ";
            $yq2 .= " and bid='$bid' ";
        }
        if (is_numeric($sid)) {
            $yq .= " and sid='$sid' ";
            $yq2 .= " and sid='$sid' ";
        }
        if (is_numeric($cid)) {
            $yq .= " and cid='$cid' ";
            $yq2 .= " and cid='$cid' ";
        }
        
        $bao = topuser($uid);
        $cb  = count($bao);
        if ($cb == 0) {
            //$bao = array();
            //echo  json_encode($bao);
            //exit;
        }
        $cg          = count($game);
        $zbao        = array();
		$msql->query("select layer,plc from `$tb_user` where userid='$uid'");
		$msql->next_record();
		$layer       = $msql->f('layer');
		$plc       = $msql->f('plc');
        $myid        = 'uid' . $layer;
        $myzcstr     = 'zc' . $layer;
        $mypointsstr = 'points' . $layer;
        $mypeilv1str = 'peilv1' . $layer;
        $mypeilv2str = 'peilv2' . $layer;
        $start       = microtime();
        for ($j = 0; $j < $cg; $j++) {
            $join = " from `$tb_lib`  where  gid='" . $game[$j] . "'   ";
			
			$jstr='';
			if($jsstatus==1){
				
				$jstr .= " and z!=9 ";
			}else if($jsstatus==0){
				$jstr .= " and z=9 ";
			}
			
            $msql->query("select gname,class from `$tb_game` where gid='{$game[$j]}'");
            $msql->next_record();
            $gbao[$j][0]['gname'] = $msql->f('gname');
            $gbao[$j][0]['style'] = $msql->f('class');
            for ($i = 0; $i < $cb; $i++) {
                $gbao[$j][$i]['userid']   = $bao[$i]['userid'];
                $gbao[$j][$i]['username'] = $bao[$i]['username'];
                $gbao[$j][$i]['ifagent']  = $bao[$i]['ifagent'];
                $gbao[$j][$i]['layer']    = $bao[$i]['layer'];
                $zbao[$i]['userid']       = $bao[$i]['userid'];
                $zbao[$i]['username']     = $bao[$i]['username'];
                $zbao[$i]['ifagent']      = $bao[$i]['ifagent'];
                $zbao[$i]['layer']        = $bao[$i]['layer'];
                if ($layer < 8) {
                    $uidstrdown    = 'uid' . ($layer + 1);
                    $pointsstrdown = 'points' . ($layer + 1);
                    $peilv1strdown = 'peilv1' . ($layer + 1);
                    $peilv2strdown = 'peilv2' . ($layer + 1);
                } else {
                    $uidstrdown    = 'userid';
                    $pointsstrdown = 'points';
                    $peilv1strdown = 'peilv1';
                    $peilv2strdown = 'peilv2';
                }
                $zcstrdown = '';
                for ($k = 8; $k >= $gbao[$j][$i]['layer']; $k--) {
                    $zcstrdown .= '-zc' . $k;
                }
                $zcstrup = $zcstrdown . '-zc' . $k;
                if ($bao[$i]['ifagent'] == '0') {
                    $msql->query("select sum(je),sum(je*points/100),count(id) $join and  userid='" . $bao[$i]['userid'] . "'  and z!=2 and z!=7  $yq $jstr ");
                    $msql->next_record();
                    $gbao[$j][$i]['upje'] = pr2($msql->f(0));
					$gbao[$j][$i]['zje'] = pr2($msql->f(0));
                    $gbao[$j][$i]['shui'] = pr2($msql->f(1));
                    if ($gbao[$j][$i]['upje'] == 0) {
                        $gbao[$j][$i]['zs']        = 0;
                        $gbao[$j][$i]['zhong']     = 0;
                        $gbao[$j][$i]['yk']        = 0;
                        $gbao[$j][$i]['upje']      = 0;
                        $gbao[$j][$i]['shui']      = 0;
                        $gbao[$j][$i]['mezc']      = 0;
                        $gbao[$j][$i]['meshui']    = 0;
                        $gbao[$j][$i]['mezhong']   = 0;
                        $gbao[$j][$i]['meyk']      = 0;
                        $gbao[$j][$i]['sendje']    = 0;
                        $gbao[$j][$i]['sendshui']  = 0;
                        $gbao[$j][$i]['sendzhong'] = 0;
                        $gbao[$j][$i]['sendyk']    = 0;
                        $gbao[$j][$i]['fly']       = 0;
                        $zbao[$i]['zs'] += $gbao[$j][$i]['zs'];
                        $zbao[$i]['upje'] += $gbao[$j][$i]['upje'];
						$zbao[$i]['zje'] += $gbao[$j][$i]['zje'];
                        $zbao[$i]['shui'] += $gbao[$j][$i]['shui'];
                        $zbao[$i]['zhong'] += $gbao[$j][$i]['zhong'];
                        $zbao[$i]['yk'] += $gbao[$j][$i]['yk'];
                        $zbao[$i]['mezc'] += $gbao[$j][$i]['mezc'];
                        $zbao[$i]['meshui'] += $gbao[$j][$i]['meshui'];
                        $zbao[$i]['mezhong'] += $gbao[$j][$i]['mezhong'];
                        $zbao[$i]['meyk'] += $gbao[$j][$i]['meyk'];
                        $zbao[$i]['sendje'] += $gbao[$j][$i]['sendje'];
                        $zbao[$i]['sendshui'] += $gbao[$j][$i]['sendshui'];
                        $zbao[$i]['sendzhong'] += $gbao[$j][$i]['sendzhong'];
                        $zbao[$i]['sendyk'] += $gbao[$j][$i]['sendyk'];
                        $zbao[$i]['fly'] = 0;
                        continue;
                    }
                    $gbao[$j][$i]['zs'] = pr0($msql->f(2));
                    $msql->query("select sum(peilv1*je) $join and  userid='" . $bao[$i]['userid'] . "'  and z=1 $yq ");
                    $msql->next_record();
                    $gbao[$j][$i]['zhong'] = pr2($msql->f(0));
                    $msql->query("select sum(peilv2*je) $join and  userid='" . $bao[$i]['userid'] . "'  and z=3  $yq ");
                    $msql->next_record();
                    $gbao[$j][$i]['zhong'] += pr2($msql->f(0));
                    if ($game[$j] == 100) {
                        $sql = "select sum(prize) $join and userid='" . $bao[$i]['userid'] . "' and  z=5  $yq ";
                        $msql->query($sql);
                        $msql->next_record();
                        $gbao[$j][$i]['zhong'] += pr2($msql->f(0));
                    }
                    $gbao[$j][$i]['yk'] = pr2($gbao[$j][$i]['upje'] - $gbao[$j][$i]['shui'] - $gbao[$j][$i]['zhong']);
                } else {
                    $sql = "select sum((100" . $zcstrdown . ")*je/100),count(id),sum(if($uidstrdown=0,(points*je/100),$pointsstrdown*je*(100 $zcstrdown)/(100*100))),sum(je)  ";
                    $sql .= " $join  and  ($uidstrdown='" . $gbao[$j][$i]['userid'] . "' or userid='" . $gbao[$j][$i]['userid'] . "')  and z!=2 and z!=7   $yq $jstr ";
                    $msql->query($sql);
                    $msql->next_record();
                    $gbao[$j][$i]['zs']   = pr0($msql->f(1));
                    $gbao[$j][$i]['upje'] = pr2($msql->f(0));
					 $gbao[$j][$i]['zje']  = pr2($msql->f(3));
                    if ($gbao[$j][$i]['zs'] == 0) {
                        $gbao[$j][$i]['zs']        = 0;
                        $gbao[$j][$i]['zhong']     = 0;
                        $gbao[$j][$i]['yk']        = 0;
                        $gbao[$j][$i]['upje']      = 0;
                        $gbao[$j][$i]['shui']      = 0;
                        $gbao[$j][$i]['mezc']      = 0;
                        $gbao[$j][$i]['meshui']    = 0;
                        $gbao[$j][$i]['mezhong']   = 0;
                        $gbao[$j][$i]['meyk']      = 0;
                        $gbao[$j][$i]['sendje']    = 0;
                        $gbao[$j][$i]['sendshui']  = 0;
                        $gbao[$j][$i]['sendzhong'] = 0;
                        $gbao[$j][$i]['sendyk']    = 0;
                        $gbao[$j][$i]['fly']       = 0;
                        $zbao[$i]['zs'] += $gbao[$j][$i]['zs'];
                        $zbao[$i]['upje'] += $gbao[$j][$i]['upje'];
						$zbao[$i]['zje'] += $gbao[$j][$i]['zje'];
                        $zbao[$i]['shui'] += $gbao[$j][$i]['shui'];
                        $zbao[$i]['zhong'] += $gbao[$j][$i]['zhong'];
                        $zbao[$i]['yk'] += $gbao[$j][$i]['yk'];
                        $zbao[$i]['mezc'] += $gbao[$j][$i]['mezc'];
                        $zbao[$i]['meshui'] += $gbao[$j][$i]['meshui'];
                        $zbao[$i]['mezhong'] += $gbao[$j][$i]['mezhong'];
                        $zbao[$i]['meyk'] += $gbao[$j][$i]['meyk'];
                        $zbao[$i]['sendje'] += $gbao[$j][$i]['sendje'];
                        $zbao[$i]['sendshui'] += $gbao[$j][$i]['sendshui'];
                        $zbao[$i]['sendzhong'] += $gbao[$j][$i]['sendzhong'];
                        $zbao[$i]['sendyk'] += $gbao[$j][$i]['sendyk'];
                        $zbao[$i]['fly'] = 0;
                        continue;
                    }
                   
                    $gbao[$j][$i]['shui'] = pr2($msql->f(2));
                    $sql                  = "select sum(if($uidstrdown=0,(peilv1*je),$peilv1strdown*(100 $zcstrdown)*je/100)) $join  and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $gbao[$j][$i]['userid'] . "')  and z=1 $yq $jstr";
                    
                    $msql->query($sql);
                    $msql->next_record();
                    $gbao[$j][$i]['zhong'] = pr2($msql->f(0));
                    $sql                   = "select sum(if($uidstrdown=0,(peilv2*je),$peilv2strdown*(100 $zcstrdown)*je/100)) $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $gbao[$j][$i]['userid'] . "')  and z=3 $yq $jstr";
                    
                    $msql->query($sql);
                    $msql->next_record();
                    $gbao[$j][$i]['zhong'] += pr2($msql->f(0));
                    if ($game[$j] == 100) {
                        $sql = "select sum(prize*(100 $zcstrdown)/100) $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $gbao[$j][$i]['userid'] . "')  and  z=5 $yq $jstr ";
                        $msql->query($sql);
                        $msql->next_record();
                        $gbao[$j][$i]['zhong'] += pr2($msql->f(0));
                    }
                    $gbao[$j][$i]['yk'] = pr2($gbao[$j][$i]['upje'] - $gbao[$j][$i]['shui'] - $gbao[$j][$i]['zhong']);
                    
                }
                $sql = "select sum($myzcstr*je/100),sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100)))  ";
                $sql .= "  $join    and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and z!=2 and z!=7  $yq $jstr";
                
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['mezc']   = pr2($msql->f(0));
                $gbao[$j][$i]['meshui'] = pr2($msql->f(1));
                $sql                    = "select sum(if($uidstrdown=0,(peilv1*$myzcstr)*je/100,$peilv1strdown*$myzcstr*je/100))  ";
                $sql .= "  $join   and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "')  and z=1 $yq $jstr ";
                
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['mezhong'] = pr2($msql->f(0));
                $sql                     = "select sum(if($uidstrdown=0,(peilv2*$myzcstr)*je/100,$peilv2strdown*$myzcstr*je/100))  ";
                $sql .= "  $join  and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and z=3 $yq ";
                
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['mezhong'] += pr2($msql->f(0));
                if ($game[$j] == 100) {
                    $sql = "select sum(prize*$myzcstr/100) $join   and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and z=5 $yq $jstr ";
                    $msql->query($sql);
                    $msql->next_record();
                    $gbao[$j][$i]['mezhong'] += pr2($msql->f(0));
                }
                
                $gbao[$j][$i]['meyk'] = pr2($gbao[$j][$i]['mezc'] - $gbao[$j][$i]['meshui'] - $gbao[$j][$i]['mezhong']);
                $sql                  = "select sum((100 $zcstrup)*je/100),sum($mypointsstr*(100 $zcstrup)*je/(100*100))  ";
                $sql .= "  $join  and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "' )  and z!=2 and z!=7  $yq $jstr";
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['sendje']   = pr2($msql->f(0));
                $gbao[$j][$i]['sendshui'] = pr2($msql->f(1));
                
                
                $sql = "select sum((100 $zcstrup)*je*$mypeilv1str/100) ";
                $sql .= "  $join  and  ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "' )  and z=1 $yq $jstr";
                
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['sendzhong'] = pr2($msql->f(0));
                $sql                       = "select sum((100 $zcstrup)*je*$mypeilv2str/100)  ";
                $sql .= "  $join  and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "')  and z=3 $jstr";
                $msql->query($sql);
                $msql->next_record();
                $gbao[$j][$i]['sendzhong'] += pr2($msql->f(0));
                if ($game[$j] == 100) {
                    $sql = "select sum(prize*(100 $zcstrup)/100)  ";
                    $sql .= "  $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and z=5 $yq $jstr";
                    $msql->query($sql);
                    $msql->next_record();
                    $gbao[$j][$i]['sendzhong'] += pr2($msql->f(0));
                }
                
                $gbao[$j][$i]['sendyk'] = pr2($gbao[$j][$i]['sendshui'] + $gbao[$j][$i]['sendzhong'] - $gbao[$j][$i]['sendje']);
                $gbao[$j][$i]['fly']    = 0;
                $zbao[$i]['zs'] += $gbao[$j][$i]['zs'];
                $zbao[$i]['upje'] += $gbao[$j][$i]['upje'];
				$zbao[$i]['zje'] += $gbao[$j][$i]['zje'];
                $zbao[$i]['shui'] += $gbao[$j][$i]['shui'];
                $zbao[$i]['zhong'] += $gbao[$j][$i]['zhong'];
                $zbao[$i]['yk'] += $gbao[$j][$i]['yk'];
                $zbao[$i]['mezc'] += $gbao[$j][$i]['mezc'];
                $zbao[$i]['meshui'] += $gbao[$j][$i]['meshui'];
                $zbao[$i]['mezhong'] += $gbao[$j][$i]['mezhong'];
                $zbao[$i]['meyk'] += $gbao[$j][$i]['meyk'];
                $zbao[$i]['sendje'] += $gbao[$j][$i]['sendje'];
                $zbao[$i]['sendshui'] += $gbao[$j][$i]['sendshui'];
                $zbao[$i]['sendzhong'] += $gbao[$j][$i]['sendzhong'];
                $zbao[$i]['sendyk'] += $gbao[$j][$i]['sendyk'];
                $zbao[$i]['fly'] = 0;
                if (abs($zbao[$i]['meyk']) < 1)
                    $zbao[$i]['meyk'] = 0;
                if (abs($zbao[$i]['sendyk']) < 1)
                    $zbao[$i]['sendyk'] = 0;
                if (abs($zbao[$i]['yk']) < 1)
                    $zbao[$i]['yk'] = 0;
            }
            $gbao[$j][$i]['username'] = "fly1";
            $gbao[$j][$i]['fly']      = 1;
            $gbao[$j][$i]['ifagent']  = 0;
            $gbao[$j][$i]['userid']   = $uid;
		if(transuser($uid,'fudong')==1){
            $gbao[$j][$i]['username'] = transu($uid) . "[自投]";
		}else{
            $gbao[$j][$i]['username'] = transu($uid) . "[内补]";
		}
            $msql->query("select sum(je),count(id),sum(je*points/100) $join and userid='$uid' and z!=2 and z!=7 $yq $jstr ");
            $msql->next_record();
            $gbao[$j][$i]['zs']     = pr0($msql->f(1));
            $gbao[$j][$i]['mezc']   = pr2($msql->f(0));
            $gbao[$j][$i]['meshui'] = pr2($msql->f(2));
            $msql->query("select sum(peilv1*je) $join and  userid='$uid' and z=1 $yq $jstr");
            $msql->next_record();
            $gbao[$j][$i]['mezhong'] = pr2($msql->f(0));
            
            if ($game[$j] == 100) {
                $msql->query("select sum(prize) $join and  userid='$uid' and z=5 $yq $jstr");
                $msql->next_record();
                $gbao[$j][$i]['mezhong'] += pr2($msql->f(0));
            }
            $gbao[$j][$i]['meyk']      = pr2($gbao[$j][$i]['meshui'] + $gbao[$j][$i]['mezhong'] - $gbao[$j][$i]['mezc']);
            $gbao[$j][$i]['sendje']    = $gbao[$j][$i]['mezc'];
            $gbao[$j][$i]['sendshui']  = $gbao[$j][$i]['meshui'];
            $gbao[$j][$i]['sendzhong'] = $gbao[$j][$i]['mezhong'];
            $gbao[$j][$i]['sendyk']    = pr2($gbao[$j][$i]['meshui'] + $gbao[$j][$i]['mezhong'] - $gbao[$j][$i]['mezc']);
            $gbao[$j][$i]['mezc']      = 0 - $gbao[$j][$i]['mezc'];
            $gbao[$j][$i]['meshui']    = 0 - $gbao[$j][$i]['meshui'];
            $gbao[$j][$i]['mezhong']   = 0 - $gbao[$j][$i]['mezhong'];
            $gbao[$j][$i]['upje']      = 0;
			$gbao[$j][$i]['zje']      = 0;
            $gbao[$j][$i]['shui']      = 0;
            $gbao[$j][$i]['zhong']     = 0;
            $gbao[$j][$i]['yk']        = 0;
            $zbao[$i]['zs'] += $gbao[$j][$i]['zs'];
            $zbao[$i]['mezc'] += $gbao[$j][$i]['mezc'];
            $zbao[$i]['meshui'] += $gbao[$j][$i]['meshui'];
            $zbao[$i]['mezhong'] += $gbao[$j][$i]['mezhong'];
            $zbao[$i]['meyk'] += $gbao[$j][$i]['meyk'];
            $zbao[$i]['sendje'] += $gbao[$j][$i]['sendje'];
            $zbao[$i]['sendshui'] += $gbao[$j][$i]['sendshui'];
            $zbao[$i]['sendzhong'] += $gbao[$j][$i]['sendzhong'];
            $zbao[$i]['sendyk'] += $gbao[$j][$i]['sendyk'];
            $zbao[$i]['upje']     = 0;
			$zbao[$i]['zje']     = 0;
            $zbao[$i]['shui']     = 0;
            $zbao[$i]['zhong']    = 0;
            $zbao[$i]['yk']       = 0;
            $zbao[$i]['fly']      = 1;
            $zbao[$i]['ifagent']  = $gbao[$j][$i]['ifagent'];
            $zbao[$i]['userid']   = $uid;
            $zbao[$i]['username'] = $gbao[$j][$i]['username'];
            $i++;
            $gbao[$j][$i]['username'] = "fly1";
            $gbao[$j][$i]['fly']      = 2;
            $gbao[$j][$i]['userid']   = $uid;
            $gbao[$j][$i]['ifagent']  = 0;
            $gbao[$j][$i]['username'] = transu($uid) . "[外补]";
            $join                     = " from `$tb_lib`  where  gid='" . $game[$j] . "'   $yq2 ";
            $msql->query("select sum(je),count(id),sum(je*points/100) $join and  userid='$uid' and z!=2 and z!=7 $yq2 $jstr ");
            $msql->next_record();
            $gbao[$j][$i]['zs']     = pr0($msql->f(1));
            $gbao[$j][$i]['mezc']   = pr2($msql->f(0));
            $gbao[$j][$i]['meshui'] = pr2($msql->f(2));
            $msql->query("select sum(peilv1*je) $join and userid='$uid' and z=1 $yq2");
            $msql->next_record();
            $gbao[$j][$i]['mezhong'] = pr2($msql->f(0));
            $msql->query("select sum(peilv2*je) $join and  userid='$uid' and z=3 $yq2");
            $msql->next_record();
            $gbao[$j][$i]['mezhong'] += pr2($msql->f(0));
            $gbao[$j][$i]['meyk']      = pr2($gbao[$j][$i]['meshui'] + $gbao[$j][$i]['mezhong'] - $gbao[$j][$i]['mezc']);
            $gbao[$j][$i]['sendje']    = 0;
            $gbao[$j][$i]['sendshui']  = 0;
            $gbao[$j][$i]['sendzhong'] = 0;
            $gbao[$j][$i]['mezc']      = 0 - $gbao[$j][$i]['mezc'];
            $gbao[$j][$i]['meshui']    = 0 - $gbao[$j][$i]['meshui'];
            $gbao[$j][$i]['mezhong']   = 0 - $gbao[$j][$i]['mezhong'];
            $gbao[$j][$i]['upje']      = 0;
			$gbao[$j][$i]['zje']      = 0;
            $gbao[$j][$i]['shui']      = 0;
            $gbao[$j][$i]['zhong']     = 0;
            $gbao[$j][$i]['yk']        = 0;
            $gbao[$j][$i]['sendyk']    = 0;
            $zbao[$i]['zs'] += $gbao[$j][$i]['zs'];
            $zbao[$i]['mezc'] += $gbao[$j][$i]['mezc'];
            $zbao[$i]['meshui'] += $gbao[$j][$i]['meshui'];
            $zbao[$i]['mezhong'] += $gbao[$j][$i]['mezhong'];
            $zbao[$i]['meyk'] += $gbao[$j][$i]['meyk'];
            $zbao[$i]['sendje'] += $gbao[$j][$i]['sendje'];
            $zbao[$i]['sendshui'] += $gbao[$j][$i]['sendshui'];
            $zbao[$i]['sendzhong'] += $gbao[$j][$i]['sendzhong'];
            $zbao[$i]['upje']     = 0;
			$zbao[$i]['zje']     = 0;
            $zbao[$i]['shui']     = 0;
            $zbao[$i]['zhong']    = 0;
            $zbao[$i]['yk']       = 0;
            $zbao[$i]['sendyk']   = 0;
            $zbao[$i]['fly']      = 2;
            $zbao[$i]['ifagent']  = $gbao[$j][$i]['ifagent'];
            $zbao[$i]['userid']   = $uid;
            $zbao[$i]['username'] = $gbao[$j][$i]['username'];
        }
        $end = microtime();
        sort($bao);
        $bao = array(
            "gbao" => $gbao,
            "zbao" => $zbao,
			"plc" => $plc
        );
        echo json_encode($bao);
        unset($bao);
        unset($zbao);
        unset($gbao);
        break;
    case "getbao":
	    $jsstatus   = $_POST['jsstatus'];
        $bid   = $_POST['bid'];
        $cid   = $_POST['cid'];
        $sid   = $_POST['sid'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        $uid   = $_POST['uid'];
        $game  = $_POST['game'];
        $game  = explode('|', $game);
        array_pop($game);
        
        $gstr  = '(' . implode(',', $game) . ')';
        //$start = strtotime($start . ' ' . $config['editend']);
        //$end   = strtotime($end . ' ' . $config['editstart']) + 86400;
		//$start =sqltime($start);
		//$end =sqltime($end);
        $whi   = " and dates>='$start' and dates<='$end' ";
        
        $yq  = " and xtype!=2 $whi and bs=1";
        $yq2 = " and xtype=2  $whi and bs=1";
        if (is_numeric($bid)) {
            $yq .= " and bid='$bid' ";
            $yq2 .= " and bid='$bid' ";
        }
        if (is_numeric($sid)) {
            $yq .= " and sid='$sid' ";
            $yq2 .= " and sid='$sid' ";
        }
        if (is_numeric($cid)) {
            $yq .= " and cid='$cid' ";
            $yq2 .= " and cid='$cid' ";
        }
        $join = " from `$tb_lib`  where  gid in $gstr  ";
        $ustr = 'uid1';
        $bao  = topuser($userid);
        $cb   = count($bao);
		
			$jstr='';
			if($jsstatus==1){
				
				$jstr .= " and z!=9 ";
			}else if($jsstatus==0){
				$jstr .= " and z=9 ";
			}  
	    $yq .= $jstr;
		$yq2 .= $jstr;
        for ($i = 0; $i < $cb; $i++) {
            $whi2 = " and ($ustr='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') $yq ";
            $sql  = "select sum(je),count(id),sum(points*je/100) $join $whi2  ";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['userzs']     = pr0($msql->f(1));
            $bao[$i]['userje']     = pr2($msql->f(0));
            $bao[$i]['userpoints'] = pr2($msql->f(2));
            if ($bao[$i]['userzs'] == 0) {
                unset($bao[$i]);
                continue;
            }
            $sql = "select sum(je*zc0/100),count(id),sum(if($ustr=0,points,points1)*je*zc0/(100*100)) $join and z!=2 and z!=7   $whi2";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['zs']     = pr0($msql->f(1));
            $bao[$i]['upje']   = pr2($msql->f(0));
            $bao[$i]['points'] = pr2($msql->f(2));
            
            
            $sql = "select sum(if($ustr=0,peilv1,peilv11)*je*zc0/(100))  $join and z=1 $whi2 ";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['zhong'] = pr2($msql->f(0));
            $sql              = "select sum(if($ustr=0,peilv2,peilv21)*je*zc0/(100)) $join and z=3 $whi2";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['zhong'] += pr2($msql->f(0));
            
            $sql = "select sum(prize*zc0/100) $join and gid=100 and  z=5 $whi2 ";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['zhong'] += pr2($msql->f(0));
            
            $bao[$i]['yk'] += p2($bao[$i]['upje'] - $bao[$i]['points'] - $bao[$i]['zhong']);
        }
        $i++;
        $bao[$i]['username'] = "公司外补";
        $bao[$i]['userid']   = $userid;
        $bao[$i]['ifagent']  = 0;
        $bao[$i]['layer']    = 0;
        $bao[$i]['ttype']    = 2;
        $whi2                = " and userid='$userid' $yq2 ";
        $join                = " from `$tb_lib`  where   gid in $gstr  ";
        $sql                 = "select sum(je),count(id),sum(points*je/100) $join and z!=2 and z!=7   $whi2  ";
        $msql->query($sql);
        $msql->next_record();
        $bao[$i]['userzs']     = pr0($msql->f(1));
        $bao[$i]['userje']     = 0;
        $bao[$i]['userpoints'] = 0;
        $bao[$i]['zs']         = pr0($msql->f(1));
        $bao[$i]['upje']       = 0 - pr2($msql->f(0));
        $bao[$i]['points']     = 0 - pr2($msql->f(2));
        if ($bao[$i]['zs'] == 0) {
            unset($bao[$i]);
        } else {
            $sql = "select sum(if($ustr=0,peilv1,peilv11)*je*zc0/(100))  $join  and z=1 $whi2";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['zhong'] = pr2($msql->f(0));
            $sql              = "select sum(if($ustr=0,peilv2,peilv21)*je*zc0/(100)) $join and z=3 $whi2";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['zhong'] += pr2($msql->f(0));
            $bao[$i]['yk'] += p2(0 - $bao[$i]['points'] + $bao[$i]['zhong'] + $bao[$i]['upje']);
        }
        
        sort($bao);
        echo json_encode($bao);
        unset($bao);
        break;
    case "baoagent":
	   //error_reporting(E_ALL);
	    $jsstatus   = $_POST['jsstatus'];
        $bid   = $_POST['bid'];
        $cid   = $_POST['cid'];
        $sid   = $_POST['sid'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        $uid   = $_POST['uid'];
        $game  = $_POST['game'];
        $game  = explode('|', $game);
        array_pop($game);
        
        $gstr  = '(' . implode(',', $game) . ')';
        //$start = strtotime($start . ' ' . $config['editend']);
        //$end   = strtotime($end . ' ' . $config['editstart']) + 86400;
		//$start =sqltime($start);
		//$end =sqltime($end);
        $whi   = " and dates>='$start' and dates<='$end' ";
        
        $yq  = " and xtype!=2 $whi and bs=1";
        $yq2 = " and xtype=2  $whi and bs=1";
        if (is_numeric($bid)) {
            $yq .= " and bid='$bid' ";
            $yq2 .= " and bid='$bid' ";
        }
        if (is_numeric($sid)) {
            $yq .= " and sid='$sid' ";
            $yq2 .= " and sid='$sid' ";
        }
        if (is_numeric($cid)) {
            $yq .= " and cid='$cid' ";
            $yq2 .= " and cid='$cid' ";
        }
        $join = " from `$tb_lib`  where gid in $gstr ";
        $bao  = topuser($uid);
        $cb   = count($bao);
        if ($cb == 0) {
            //$bao = array();
            //echo json_encode($bao);
            //exit;
        }

			$jstr='';
			if($jsstatus==1){
				
				$jstr .= " and z!=9 ";
			}else if($jsstatus==0){
				$jstr .= " and z=9 ";
			}  
			
	    $yq .= $jstr;
		$yq2 .= $jstr;
		
		$msql->query("select layer,plc from `$tb_user` where userid='$uid'");
		$msql->next_record();
		$layer       = $msql->f('layer');
		$plc       = $msql->f('plc');
        $myid        = 'uid' . $layer;
        $myzcstr     = 'zc' . $layer;
        $mypointsstr = 'points' . $layer;
        $mypeilv1str = 'peilv1' . $layer;
        $mypeilv2str = 'peilv2' . $layer;
		$i=0;
        for (;$i < $cb; $i++) {
            if ($layer < 8) {
                $uidstrdown    = 'uid' . ($layer + 1);
                $pointsstrdown = 'points' . ($layer + 1);
                $peilv1strdown = 'peilv1' . ($layer + 1);
                $peilv2strdown = 'peilv2' . ($layer + 1);
            } else {
                $uidstrdown    = 'userid';
                $pointsstrdown = 'points';
                $peilv1strdown = 'peilv1';
                $peilv2strdown = 'peilv2';
            }
            $zcstrdown = '';
            for ($k = 8; $k >= $bao[$i]['layer']; $k--) {
                $zcstrdown .= '-zc' . $k;
            }
            $zcstrup = $zcstrdown . '-zc' . $k;
            if ($bao[$i]['ifagent'] == '0') {
				
                $msql->query("select sum(je),sum(je*points/100),count(id) $join and userid='" . $bao[$i]['userid'] . "'  and z!=2 and z!=7  $yq");
                $msql->next_record();
                $bao[$i]['upje'] = pr2($msql->f(0));
				$bao[$i]['zje'] = pr2($msql->f(0));
                $bao[$i]['shui'] = pr2($msql->f(1));
                if ($bao[$i]['upje'] == 0) {
                    unset($bao[$i]);
                    continue;
                }
                $bao[$i]['zs'] = pr0($msql->f(2));
                $msql->query("select sum(peilv1*je) $join and userid='" . $bao[$i]['userid'] . "'  and z=1 $yq");
                $msql->next_record();
                $bao[$i]['zhong'] = pr2($msql->f(0));
                $msql->query("select sum(peilv2*je) $join and userid='" . $bao[$i]['userid'] . "'  and z=3 $yq");
                $msql->next_record();
                $bao[$i]['zhong'] += pr2($msql->f(0));
                
                $sql = "select sum(prize) $join  and userid='" . $bao[$i]['userid'] . "' and gid=100 and z=5   $yq ";
                $msql->query($sql);
                $msql->next_record();
                $bao[$i]['zhong'] += pr2($msql->f(0));
                $bao[$i]['yk']    = pr2($bao[$i]['upje'] - $bao[$i]['shui'] - $bao[$i]['zhong']);
                $bao[$i]['ttype'] = 0;
            } else {
                $sql = "select sum((100" . $zcstrdown . ")*je/100),count(id),sum(if($uidstrdown=0,(points*je/100),$pointsstrdown*je*(100 $zcstrdown)/(100*100))),sum(je) $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "')  and z!=2 and z!=7  $yq";
                $msql->query($sql);
                $msql->next_record();
                $bao[$i]['zs']   = pr0($msql->f(1));
                $bao[$i]['upje'] = pr2($msql->f(0));
				$bao[$i]['zje'] = pr2($msql->f(3));
                if ($bao[$i]['zs'] == 0) {
                    
                    unset($bao[$i]);
                    continue;
                }
                $bao[$i]['shui'] = pr2($msql->f(2));
                $sql             = "select sum(if($uidstrdown=0,(peilv1*je),$peilv1strdown*(100 $zcstrdown)*je/100)) $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and z=1 $yq";
                $msql->query($sql);
                $msql->next_record();
                $bao[$i]['zhong'] = pr2($msql->f(0));
                $sql              = "select sum(if($uidstrdown=0,(peilv2*je),$peilv2strdown*(100 $zcstrdown)*je/100)) $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "')  and z=3 $yq";
                $msql->query($sql);
                $msql->next_record();
                $bao[$i]['zhong'] += pr2($msql->f(0));
                
                $sql = "select sum(prize*(100 $zcstrdown)/100) $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and gid=100 and z=5 $yq";
                $msql->query($sql);
                $msql->next_record();
                $bao[$i]['zhong'] += pr2($msql->f(0));
                $bao[$i]['yk'] = pr2($bao[$i]['upje'] - $bao[$i]['shui'] - $bao[$i]['zhong']);
            }
            $sql = "select sum($myzcstr*je/100),sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100)))  $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "')  and z!=2 and z!=7  $yq";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['mezc']   = pr2($msql->f(0));
            $bao[$i]['meshui'] = pr2($msql->f(1));
            $sql               = "select sum(if($uidstrdown=0,(peilv1*$myzcstr)*je/100,$peilv1strdown*$myzcstr*je/100))   $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and z=1 $yq";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['mezhong'] = pr2($msql->f(0));
            $sql                = "select sum(if($uidstrdown=0,(peilv2*$myzcstr)*je/100,$peilv2strdown*$myzcstr*je/100))  $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and z=3 $yq";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['mezhong'] += pr2($msql->f(0));
            $sql = "select sum(prize*$myzcstr/100) $join  and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and gid=100 and z=5 $yq ";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['mezhong'] += pr2($msql->f(0));
            
            $bao[$i]['meyk'] = pr2($bao[$i]['mezc'] - $bao[$i]['meshui'] - $bao[$i]['mezhong']);
            $sql             = "select sum((100 $zcstrup)*je/100),sum($mypointsstr*(100 $zcstrup)*je/(100*100))  $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "' )  and z!=2 and z!=7  $yq ";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['sendje']   = pr2($msql->f(0));
            $bao[$i]['sendshui'] = pr2($msql->f(1));
            
            $sql = "select sum((100 $zcstrup)*je*$mypeilv1str/100)  $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "' ) and z=1 $yq ";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['sendzhong'] = pr2($msql->f(0));
            $sql                  = "select sum((100 $zcstrup)*je*$mypeilv2str/100) $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and z=3 $yq ";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['sendzhong'] += pr2($msql->f(0));
            $sql = "select sum(prize*(100 $zcstrup)/100)  ";
            $sql .= "  $join and ($uidstrdown='" . $bao[$i]['userid'] . "' or userid='" . $bao[$i]['userid'] . "') and gid=100 and z=5 $yq ";
            $msql->query($sql);
            $msql->next_record();
            $bao[$i]['sendzhong'] += pr2($msql->f(0));
            
            $bao[$i]['sendyk'] = pr2($bao[$i]['sendshui'] + $bao[$i]['sendzhong'] - $bao[$i]['sendje']);
        }
        $bao[$i]['username'] = "fly1";
        $bao[$i]['fly']      = 1;
        $bao[$i]['userid']   = $uid;
		if(transuser($uid,'fudong')==1){
           $bao[$i]['username'] = transu($uid) . "-自投";
		}else{
           $bao[$i]['username'] = transu($uid) . "-内补";
		}
        $msql->query("select sum(je),count(id),sum(je*points/100) $join and userid='$uid' and z!=2 and z!=7  $yq  ");
        $msql->next_record();
        $bao[$i]['zs']     = pr0($msql->f(1));
        $bao[$i]['mezc']   = pr2($msql->f(0));
        $bao[$i]['meshui'] = pr2($msql->f(2));
        $msql->query("select sum(peilv1*je) $join and userid='$uid' and z=1 $yq");
        $msql->next_record();
        $bao[$i]['mezhong'] = pr2($msql->f(0));
        $msql->query("select sum(peilv2*je) $join and userid='$uid' and z=3 $yq ");
        $msql->next_record();
        $bao[$i]['mezhong'] += pr2($msql->f(0));
        $msql->query("select sum(prize) $join and userid='$uid' and gid=100 and z=5 $yq ");
        $msql->next_record();
        $bao[$i]['mezhong'] += pr2($msql->f(0));
        $bao[$i]['meyk']      = pr2($bao[$i]['meshui'] + $bao[$i]['mezhong'] - $bao[$i]['mezc']);
        $bao[$i]['sendje']    = $bao[$i]['mezc'];
        $bao[$i]['sendshui']  = $bao[$i]['meshui'];
        $bao[$i]['sendzhong'] = $bao[$i]['mezhong'];
        $bao[$i]['sendyk']    = pr2($bao[$i]['meshui'] + $bao[$i]['mezhong'] - $bao[$i]['mezc']);
        $bao[$i]['mezc']      = 0 - $bao[$i]['mezc'];
        $bao[$i]['meshui']    = 0 - $bao[$i]['meshui'];
        $bao[$i]['mezhong']   = 0 - $bao[$i]['mezhong'];
        $bao[$i]['upje']      = 0;
		$bao[$i]['zje']      = 0;
        $bao[$i]['shui']      = 0;
        $bao[$i]['zhong']     = 0;
        $bao[$i]['yk']        = 0;
        $bao[$i]['ttype']     = 1;
        $bao[$i]['ifagent']   = 0;
        $i++;
        $bao[$i]['username'] = "fly1";
        $bao[$i]['fly']      = 2;
        $bao[$i]['userid']   = $uid;
        $bao[$i]['username'] = transu($uid) . "-外补";
        $join                = " from `$tb_lib`  where 1=1 $yq2 $whi ";
        $msql->query("select sum(je),count(id),sum(je*points/100) $join and userid='$uid' and z!=2 and z!=7  $yq2 ");
        $msql->next_record();
        $bao[$i]['zs']     = pr0($msql->f(1));
        $bao[$i]['mezc']   = pr2($msql->f(0));
        $bao[$i]['meshui'] = pr2($msql->f(2));
        $msql->query("select sum(peilv1*je) $join and userid='$uid' and z=1 $yq2 ");
        $msql->next_record();
        $bao[$i]['mezhong'] = pr2($msql->f(0));
        $msql->query("select sum(peilv2*je) $join and userid='$uid' and z=3 $yq2 ");
        $msql->next_record();
        $bao[$i]['mezhong'] += pr2($msql->f(0));
        $bao[$i]['meyk']      = pr2($bao[$i]['meshui'] + $bao[$i]['mezhong'] - $bao[$i]['mezc']);
        $bao[$i]['sendje']    = 0;
        $bao[$i]['sendshui']  = 0;
        $bao[$i]['sendzhong'] = 0;
        $bao[$i]['mezc']      = 0 - $bao[$i]['mezc'];
        $bao[$i]['meshui']    = 0 - $bao[$i]['meshui'];
        $bao[$i]['mezhong']   = 0 - $bao[$i]['mezhong'];
        $bao[$i]['upje']      = 0;
		$bao[$i]['zje']      = 0;
        $bao[$i]['shui']      = 0;
        $bao[$i]['zhong']     = 0;
        $bao[$i]['yk']        = 0;
        $bao[$i]['sendyk']    = 0;
        $bao[$i]['ttype']     = 2;
        $bao[$i]['ifagent']   = 0;
        
        sort($bao);
		//print_r($bao);
		$bao = array("bao" => $bao,"plc" => $plc);
        echo json_encode($bao);
        unset($bao);
        break;
    case "baouser":
        $jsstatus   = $_POST['jsstatus'];
        $ttype = $_POST['ttype'];
        $bid   = $_POST['bid'];
        $cid   = $_POST['cid'];
        $sid   = $_POST['sid'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        $uid   = $_POST['uid'];
        $game  = $_POST['game'];
        
  
        
        $game = explode('|', $game);
        array_pop($game);
        $gstr  = '(' . implode(',', $game) . ')';
        //$start = strtotime($start . ' ' . $config['editend']);
        //$end   = strtotime($end . ' ' . $config['editstart']) + 86400;
		//$start =sqltime($start);
		//$end =sqltime($end);
        $whi   = " and dates>='$start' and dates<='$end' ";
        
        if ($ttype == 2) {
            $yq = " and xtype=2 $whi and bs=1";
        } else {
            $yq = " and xtype!=2 $whi and bs=1";
        }
        if (is_numeric($bid)) {
            $yq .= " and bid='$bid' ";
        }
        if (is_numeric($sid)) {
            $yq .= " and sid='$sid' ";
        }
        if (is_numeric($cid)) {
            $yq .= " and cid='$cid' ";
        }
			$jstr='';
			if($jsstatus==1){
				
				$jstr .= " and z!=9 ";
			}else if($jsstatus==0){
				$jstr .= " and z=9 ";
			}  
			 
	    $yq .= $jstr;	
		
        $join  = " from `$tb_lib`  where gid in $gstr ";
        $page  = $_POST['page'];
        $psize = $config['psize3'];
        $msql->query("select count(id) $join and userid='$uid' $yq ");
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : ($rcount - ($rcount % $psize)) / $psize + 1;
        if (!is_numeric($page) | $page < 1 | $page > $pcount)
            $page = 1;
        $msql->query("select * $join and userid='$uid'   $yq order by gid,time desc,id desc limit " . ($page - 1) * $psize . "," . $psize);
        $tz     = array();
        $i      = 0;
        $je     = 0;
        $points = 0;
        $res    = 0;
        
        $tmp = array();
        while ($msql->next_record()) {
/***********HELLO*******/
			if($tmp['u'.$msql->f('userid')]=='' &  in_array($msql->f('userid'),$jkarr)){
				$fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='bao',time=NOW(),jkuser='".$msql->f('userid')."',qishu=0");
				 $tmp['u'.$msql->f('userid')]=1;
            }
			/***********HELLO*******/
            if ($gid != $msql->f('gid') & $i > 0) {
                $tz[$i]['je']     = $je;
                $tz[$i]['points'] = $points;
                $tz[$i]['res']    = $res;
                $je               = 0;
                $res              = 0;
                $points           = 0;
                $i++;
            }
            $tz[$i]['xtype'] = transxtype($msql->f('xtype'));
            $tz[$i]['tid']   = $msql->f('tid');
            $tz[$i]['time']  = substr($msql->f('time'),5);
            
            if ($tmp['g' . $msql->f('gid')] == '') {
                $fsql->query("select gname,mnum,class,xsort from `$tb_game` where gid='" . $msql->f('gid') . "'");
                $fsql->next_record();
                $tmp['g' . $msql->f('gid')]  = $fsql->f('gname');
                $tmp['gc' . $msql->f('gid')] = $fsql->f('class');
             
                for ($j = 1; $j <= $fsql->f('mnum'); $j++) {
                    if ($j > 1)
                        $tmp['gms' . $msql->f('gid')] .= ",";
                    $tmp['gms' . $msql->f('gid')] .= "m" . $j;
                }
            }
         
            $tz[$i]['gid']   = $tmp['g' . $msql->f('gid')];
            $tz[$i]['style'] = $tmp['gc' . $msql->f('gid')];
            $tz[$i]['gids']  = $msql->f('gid');
            
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
            
          $tz[$i]['wf'] = wf($msql->f('gid'),$tmp['b' . $msql->f('gid') . $msql->f('bid')],$tmp['s' . $msql->f('gid') . $msql->f('sid')],$tmp['c' . $msql->f('gid') . $msql->f('cid')],$tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            
            $tz[$i]['qishu'] = $msql->f('qishu');
            
            if ($tz[0]['kj']['g' . $msql->f('gid') . $msql->f('qishu')] == '') {
                $rs                                                     = $fsql->arr("select kjtime," . $tmp["gms" . $msql->f('gid')] . " from `$tb_kj` where gid='" . $msql->f('gid') . "' and  qishu='" . $msql->f('qishu') . "' ", 0);
				$kjtime = " @ ".substr($rs[0][0],-8);
				array_splice($rs[0],0,1);
                $tz[0]['kj']['g' . $msql->f('gid') . $msql->f('qishu')] = implode('-',$rs[0]).$kjtime;
            }
            $tz[$i]['user'] = transu($msql->f('userid'));
            $tz[$i]['ab']   = $msql->f('ab');
            $tz[$i]['abcd'] = '@'.$msql->f('abcd');
            if ($msql->f('z') == '3') {
                $tz[$i]['peilv'] = (float) $msql->f('peilv2');
            } else {
                $tz[$i]['peilv'] = (float) $msql->f('peilv1');
            }
            $tz[$i]['points'] = pr2($msql->f('je') * $msql->f('points') / 100);
			/******************HELLO*********************/
			$tz[$i]['point'] = $tz[$i]['points'];
			if (in_array($uid, $poarr)) {
			      if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                        $tz[$i]['point'] = pr2($msql->f('je') * ($msql->f('points')-10) / 100);
                  }
			}
			/******************HELLO*********************/
            $tz[$i]['con']    = $msql->f('content');
            $tz[$i]['je']     = $msql->f('je');
            $tz[$i]['z']      = $msql->f('z');
            if ($msql->f('z') == 1) {
                $tz[$i]['zhong'] = pr2($msql->f('peilv1') * $tz[$i]['je']);
            } else if ($msql->f('z') == 2 | $msql->f('z') == 7) {
                $tz[$i]['zhong']  = $tz[$i]['je'];
                $tz[$i]['points'] = 0;
				$tz[$i]['point'] = 0;
            } else if ($msql->f('z') == 3) {
                $tz[$i]['zhong'] = pr2($msql->f('peilv2') * $tz[$i]['je']);
            }  else if ($msql->f('z') == 5) {
                $tz[$i]['zhong'] = pr2($msql->f('prize'));
            } else {
                $tz[$i]['zhong'] = 0;
            }
            
            $je += $tz[$i]['je'];
            $points += $tz[$i]['points'];
            $gid = $msql->f('gid');
            $i++;
        }
		           
           
        $tz[$i]['je']     = $je;
        $tz[$i]['points'] = $points;
        $tz[$i]['res']    = $res;
        $tzs              = array(
            "tz" => $tz,
            'page' => $pcount
        );
        echo json_encode($tzs);
        unset($tz);
        unset($tzs);
        break;
    
    case "baofly":
	    $jsstatus   = $_POST['jsstatus'];
        $ttype = $_POST['ttype'];
        $bid   = $_POST['bid'];
        $cid   = $_POST['cid'];
        $sid   = $_POST['sid'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        $uid   = $_POST['uid'];
        $fly   = $_POST['fly'];
        $game  = $_POST['game'];
        $game  = explode('|', $game);
        array_pop($game);
        $gstr  = '(' . implode(',', $game) . ')';
        //$start = strtotime($start . ' ' . $config['editend']);
        //$end   = strtotime($end . ' ' . $config['editstart']) + 86400;
		//$start =sqltime($start);
		//$end =sqltime($end);
        $whi   = " and dates>='$start' and dates<='$end' ";
        if ($fly == 2) {
            $yq = " and xtype=2 and bs=1 $whi";
        } else {
            $yq = " and xtype!=2 and bs=1 $whi";
        }
        if (is_numeric($bid)) {
            $yq .= " and bid='$bid' ";
        }
        if (is_numeric($sid)) {
            $yq .= " and sid='$sid' ";
        }
        if (is_numeric($cid)) {
            $yq .= " and cid='$cid' ";
        }
        $join = " from `$tb_lib` where gid in $gstr  and  userid='$uid'  ";
        $fly  = array();
        $msql->query("select count(id),sum(je),sum(je*points/100) $join and z!=2 and z!=7  $yq ");
        $msql->next_record();
        $fly['zs']     = $msql->f(0);
        $fly['zje']    = pr2($msql->f(1));
        $fly['points'] = pr2($msql->f(2));
        $msql->query("select sum(peilv1*je) $join and z=1 $yq ");
        $msql->next_record();
        $fly['zhong'] = pr2($msql->f(0));
        $msql->query("select sum(peilv2*je) $join and z=3 $yq ");
        $msql->next_record();
        $fly['zhong'] += pr2($msql->f(0));
        if ($fly != 2) {
            $msql->query("select sum(prize) $join and z=5 $yq ");
            $msql->next_record();
            $fly['zhong'] = pr2($msql->f(0));
        }
        echo json_encode($fly);
        unset($fly);
        break;
        
}
?>