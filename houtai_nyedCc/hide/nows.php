<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case 'show':
        $qishu = array();
        $qishu[0] = $config['thisqishu'];
        $tpl->assign('qishu', $qishu);
        if ($gid == 100) {
            $tpl->assign('b', getb());
            $msql->query("select qishu from `{$tb_kj}` where gid='{$gid}' and m1!=''   order by qishu desc");
            //and kjtime>$time
            $i = 1;
            while ($msql->next_record()) {
                $qishu[$i] = $msql->f('qishu');
                $i++;
            }
        } else {
            $game = getgamecs($userid);
            $game = getgamename($game);
            $tpl->assign('game', $game);
        }
        $tpl->assign('topid', $userid);
        $tpl->assign('layer', transuser($userid, 'layer'));
        $tpl->assign('username', transuser($userid, 'username'));
        $tpl->assign('layername', getweb());
        $tpl->assign('topuser', topuser($userid));
        $sdate = week();
        $tpl->assign('sdate', week());
        $tpl->assign('gid', $gid);
        $tpl->display('nows.html');
        break;
    case 'getqishu':
        $gid = $_POST['gid'];
        $rs = $msql->arr("select qishu from `{$tb_lib}` where gid='{$gid}' group by qishu desc");
        echo json_encode($rs);
        break;
    case 'getnow':
	 $xinfo = $_POST['xinfo'];
        $gid = $_POST['gid'];
        $bid = $_POST['bid'];
        $cid = $_POST['cid'];
        $sid = $_POST['sid'];
        $puserid = $_POST['puserid'];
        $page = $_POST['page'];
        if (!is_numeric($page) | $page == '') {
            $page = 1;
        }
        $psize = $config['psize3'];
        $fs = $_POST['fs'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        $qishu = $_POST['qishu'];
        $z = $_POST['z'];
        $xtype = $_POST['xtypes'];
        $sorttype = $_POST['sorttype'];
        $orderby = $_POST['orderby'];
        $username = $_POST['username'];

        if($username!=''){
            $msql->query("select userid from `$tb_user` where ifagent=0 and username='$username'");
            $msql->next_record();
            $uid = $msql->f('userid');
            if($uid=='') exit;
        }
		if ($gid != 999) {
            $wh = " gid='{$gid}' ";
        } else {
            $rs = $msql->arr("select gid from `{$tb_game}` where fast=1");
            $garr = array();
            foreach ($rs as $v) {
                $garr[] = $v[0];
            }
            $wh = ' gid in (' . implode($garr, ',') . ') ';
        }           
		if ($z == 0) {
            $wh .= ' and z=9 ';
        } else {
            if ($z == 1) {
                $wh .= ' and z!=9 ';
            }
        }
        if($uid!='' & is_numeric($uid)){
            $wh .= " and userid='{$uid}' ";
        }else if ($puserid != '' & is_numeric($puserid) & $puserid != $userid) {
            $layer = transuser($puserid, 'layer');
            if ($layer == 9) {
                $wh .= " and userid='{$puserid}' ";
            } else {
                $wh .= " and (userid='{$puserid}' or uid{$layer}='{$puserid}') ";
            }
        }
		if ($fs == 1) {
            $wh .= "  and qishu='{$qishu}'    and bs=1 ";
        } else {
            $wh .= " and  dates>='$start' and dates<='$end' and bs=1 ";
        }  
		if (in_array($xtype,array(0, 1, 2))) {
            $wh .= " and xtype='{$xtype}' ";
        } 
        if ($bid != '' & is_numeric($bid)) {
            $wh .= " and bid='{$bid}' ";
        }
        if ($sid != '' & is_numeric($sid)) {
            $wh .= " and sid='{$sid}' ";
        }
        if ($cid != '' & is_numeric($cid)) {
            $wh .= " and cid='{$cid}' ";
        }
  


        
        if ($sorttype != 'ASC' & $sorttype != 'DESC') {
            $sorttype = 'DESC';
        }
        $join = " from `{$tb_lib}` where {$wh} ";
        $sql = " select count(id)  {$join} ";
        $msql->query($sql);
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : ($rcount - $rcount % $psize) / $psize + 1;
        $pstr = page($pcount, $page);
        $sql = " select * {$join} ";
        if ($orderby == 'time') {
            $sql .= " order by time {$sorttype} ";
        } else {
            $sql .= " order by zc0*je {$sorttype} ";
        }
        $sql .= ' limit ' . ($page - 1) * $psize . ',' . $psize;
	
        $msql->query($sql);
        $tz = array();
        $i = 0;
        $tmp = array();
        while ($msql->next_record()) {
            $tz[$i]['xtype']  = transxtype($msql->f('xtype'));
            $tz[$i]['id']    = $msql->f('id');
			$tz[$i]['tid']    = $msql->f('tid');
            $tz[$i]['userid'] = $msql->f('userid');
            $tz[$i]['qishu']  = $msql->f('year') . $msql->f('qishu');
            $tz[$i]['je']     = (float) $msql->f('je');
            if ($uid == $msql->f('userid'))
                $tz[$i]['me'] = 1;
            $tz[$i]['zcje']   = pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            $tz[$i]['peilv2'] = (float) $msql->f('peilv2');
            $tz[$i]['points'] = (float) $msql->f('points');
            $tz[$i]['con']    = $msql->f('content');
			 if ($tmp['g' . $msql->f('gid')] == '') {
                $tmp['g' . $msql->f('gid')] = transgame($msql->f('gid'), 'sgname');
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
            if ($tmp['fl' . $msql->f('gid')]==''){
                $tmp['fl' . $msql->f('gid')] = transgame($msql->f('gid'),'fenlei');
            }
			 $tz[$i]['gid'] = $tmp['g' . $msql->f('gid')];

            $tz[$i]['bid']    = $tmp['b' . $msql->f('gid') . $msql->f('bid')];
            $tz[$i]['sid']    =  $tmp['s' . $msql->f('gid') . $msql->f('sid')];
            $tz[$i]['cid']    =  $tmp['c' . $msql->f('gid') . $msql->f('cid')];
            $tz[$i]['pid']    = $tmp['p' . $msql->f('gid') . $msql->f('pid')];
            $tz[$i]['gids']    = $msql->f('gid');
            $tz[$i]['bids']    = $msql->f('bid');
            $tz[$i]['sids']    =  $msql->f('sid');
            $tz[$i]['cids']    =  $msql->f('cid');
            $tz[$i]['pids']    =$msql->f('pid');
            $tz[$i]['fl']    = $tmp['fl' . $msql->f('gid')];
            $tz[$i]['time']   = $msql->f('time');
            $tz[$i]['xtime']  = substr($msql->f('time'),-8);
            $tz[$i]['user']   = transu($msql->f('userid'));
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
			$tz[$i]['z'] = $msql->f('z');	
            $i++;
           
        }
        $e = array('tz' => $tz, 'page' => $pstr, 'sql' => $sql, 'layer' => $layer);
        echo json_encode($e);
        unset($e);
        unset($tmp);
        break;
    case "deltz":
	    if($_SESSION['hide']!=1) exit;
		$tid = $_POST['tid'];
        $sql = "delete from `$tb_lib` where instr('$tid',concat(id,tid,userid))";
        $msql->query($sql);
		echo 1;
        break;
    case "edittz":
        $str = str_replace('\\', '', $_POST['str']);
        $arr = json_decode($str, true);
        $ca  = count($arr);

        for ($i = 0; $i < $ca; $i++) {
            $pid = '';
            $msql->query("select * from `$tb_lib` where concat(id,tid,userid)='" . $arr[$i]['tid'] . "'");
            $msql->next_record();
            $fl = transgame($msql->f('gid'),'fenlei');
            $gid = $msql->f('gid');
            if(($fl==107 & $msql->f('bid')!=23378805) | ($fl==101 & $msql->f('bid')==23378755)){
                $sid = $arr[$i]['sid'];
                $fsql->query("select * from `$tb_sclass` where gid='$gid' and name='$sid' ");
                $fsql->next_record();
                $bid = $fsql->f('bid');
                $sid = $fsql->f('sid');
                $fsql->query("select * from `$tb_play` where gid='$gid' and sid='$sid' and name='".$arr[$i]['pid']."'");
                $fsql->next_record();
                $cid = $fsql->f('cid');
                $pid = $fsql->f('pid');
                $peilv1  = $arr[$i]['peilv1'];
                $points  = $arr[$i]['points'];
                $content = $arr[$i]['con'];
                $je      = $arr[$i]['je'];
                $z      = $arr[$i]['z'];
                $time    = date("Y-m-d ",strtotime($msql->f('time'))) . $arr[$i]['time'];
                $sql     = '';
                $sql     = "update `$tb_lib` set bid='$bid',sid='$sid',cid='$cid',pid='$pid',peilv1='$peilv1',points='$points',content='$content',je='$je',time='$time',z='$z'";
            }else {
                $pid = untransp($msql->f('bid'), $msql->f('sid'), $msql->f('cid'), $arr[$i]['pid']);
                if ($pid == '')
                    continue;
                $peilv1  = $arr[$i]['peilv1'];
                $points  = $arr[$i]['points'];
                $content = $arr[$i]['con'];
                $je      = $arr[$i]['je'];
                $z      = $arr[$i]['z'];
                $time    = date("Y-m-d ",strtotime($msql->f('time')))  . $arr[$i]['time'];
                $sql     = '';
                $sql     = "update `$tb_lib` set pid='$pid',peilv1='$peilv1',points='$points',content='$content',je='$je',time='$time',z='$z'";
            }
            for ($j = 1; $j < 9; $j++) {
                $sql .= ",peilv1" . $j . "='" . $arr[$i]['peilv1' . $j] . "'";
                $sql .= ",points" . $j . "='" . $arr[$i]['points' . $j] . "'";
            }
            //echo $sql;
            $sql1 = $sql . ",kk=0 where id='" . $msql->f('id') . "'";
            $sql2 = $sql . ",kk=1 where id='" . $msql->f('id') . "'";
            $fsql->query($sql1);
            $fsql->query($sql2);
        }

        echo 1;
        break;
}