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
        $qishu    = array();
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
            $game = getgame();
            $tpl->assign('game', $game);
        }
        $tpl->assign('topid', $userid);
        $tpl->assign('layer', transuser($userid, 'layer'));
        $tpl->assign('username', transuser($userid, 'username'));
        $tpl->assign('layername', $config['layer']);
        $tpl->assign('maxlayer', $config['maxlayer']);
        $tpl->assign('topuser', topuser($userid));
        
        $tpl->assign('sdate', week());
        $tpl->assign('gid', $gid);
        $tpl->display('now.html');
        break;
    case 'getqishu':
        $gid = $_POST['gid'];
        $rs  = $msql->arr("select qishu from `{$tb_lib}` where gid='{$gid}' group by qishu desc");
        echo json_encode($rs);
        break;
    case 'getnow':
        $xinfo   = $_POST['xinfo'];
        $gid     = $_POST['gid'];
        $bid     = $_POST['bid'];
        $cid     = $_POST['cid'];
        $sid     = $_POST['sid'];
        $puserid = $_POST['puserid'];
        $page    = $_POST['page'];
        if (!is_numeric($page) | $page == '') {
            $page = 1;
        }
        $psize = $config['psize2'];
        $fs       = $_POST['fs'];
        $start = rdates($_POST['start']);
        $end   = rdates($_POST['end']);
        $qishu    = $_POST['qishu'];
        $z        = $_POST['z'];
        $xtype    = $_POST['xtypes'];
        $sorttype = $_POST['sorttype'];
        $orderby  = $_POST['orderby'];
        
        $layer  = transuser($userid, 'layer');
        $zcstr  = 'zc' . $layer;
        $uidstr = 'uid' . $layer;
        $wh     = " ($uidstr='$userid' or userid='$userid') ";
        if ($gid != 999) {
            $wh .= " and gid='{$gid}' ";
        } else {
            $rs   = $msql->arr("select gid from `{$tb_game}` where fast=1");
            $garr = array();
            foreach ($rs as $v) {
                $garr[] = $v[0];
            }
            $wh .= ' and gid in (' . implode($garr, ',') . ') ';
        }
        if ($z == 0) {
            $wh .= ' and z=9 ';
        }else if ($z == 7) {
                $wh .= ' and z=7 ';
            } else if ($z == 1){
             
                $wh .= ' and z!=9 and z!=7 ';
            }
        if ($layer < $config['maxlayer']) {
            if (is_numeric($puserid) & $puserid != '') {
                $wh .= '  and (uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\') ';
            }
        } else {
            
            if (is_numeric($puserid) & $puserid != '') {
                $wh .= '  and userid=\'' . $puserid . '\'';
            }
        }
        
        
        
        if ($fs == 1) {
            $wh .= "  and qishu='{$qishu}'    and bs=1 ";
        } else {

            $wh .= " and dates>='$start' and dates<='$end'   and bs=1 ";
        }
        if (in_array($xtype, array(
            0,
            1,
            2
        ))) {
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
        $sql  = " select count(id)  {$join} ";
        $msql->query($sql);
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : ($rcount - $rcount % $psize) / $psize + 1;
        $pstr   = page($pcount, $page);
        $sql    = " select * {$join} ";
        if ($orderby == 'time') {
            $sql .= " order by time {$sorttype},id {$sorttype} ";
        } else {
            $sql .= " order by $zcstr*je {$sorttype},id {$sorttype} ";
        }
        $sql .= ' limit ' . ($page - 1) * $psize . ',' . $psize;
        
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='now".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu=0");
                $tmp['jj' . $msql->f('userid')] = 1;
            }
            /***********HELLO*******/
            $tz[$i]['xtype'] = transxtype($msql->f('xtype'));
            $tz[$i]['tid']   = $msql->f('tid');
			$tz[$i]['zt']   = transzt($msql->f('z'));
            $tz[$i]['qishu'] = $msql->f('year') . $msql->f('qishu');
            $tz[$i]['je']    = (double) $msql->f('je');
            if ($uid == $msql->f('userid')) {
                $tz[$i]['me'] = 1;
            }
            $tz[$i]['zcje']   = pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (double) $msql->f('peilv1');
            if ($msql->f('peilv2') > 1) {
                $tz[$i]['peilv1'] .= '/' . (double) $msql->f('peilv2');
            }
            $tz[$i]['points'] = (double) $msql->f('points');
            
            /*********************HELLO***************/
            if (in_array($msql->f('userid'), $poarr)) {
                if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $tz[$i]['points'] -= 10;
                }
            }
            /*********************HELLO***************/
            
            $tz[$i]['con'] = $msql->f('content');
            if ($tmp['g' . $msql->f('gid')] == '') {
                $fsql->query("select gname,fenlei from `$tb_game` where gid='".$msql->f('gid')."'");
                $fsql->next_record();
                $tmp['g' . $msql->f('gid')] = $fsql->f('gname');
                $tmp['f' . $msql->f('gid')] = $fsql->f('fenlei');
            }
            if($tmp['u'.$msql->f('userid')]==''){
                $tmp['u'.$msql->f('userid')] = transu2($msql->f('userid'));
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
            $tz[$i]['gid']   = $tmp['g' . $msql->f('gid')];
            $tz[$i]['wf']    = wf($tmp['f' . $msql->f('gid')], $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            $tz[$i]['time']  = $msql->f('time');
            $tz[$i]['xtime'] = substr($msql->f('time'),5);
            $tz[$i]['user']  = $tmp['u'.$msql->f('userid')];
            if ($xinfo == 1) {
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
                        /*********************HELLO***************/
                        if (in_array($msql->f('userid'), $poarr)) {
                            if ($msql->f('ab') == 'B' & $msql->f('points' . $j) >= 10) {
                                $tz[$i]['points' . $j] -= 10;
                            }
                        }
                        /*********************HELLO***************/
                        if ($msql->f('peilv2' . $j) > 1) {
                            $tz[$i]['peilv1' . $j] .= '/' . (double) $msql->f('peilv2' . $j);
                        }
                    }
                }
            }
            if (strpos('|A|B|C|D', $msql->f('abcd'))) {
                $tz[$i]['abcd'] = $msql->f('abcd');
            } else {
                $tz[$i]['abcd'] = '';
            }
            $tz[$i]['z'] = $msql->f('z');
            if (strpos('|A|B|', $msql->f('ab'))) {
                $tz[$i]['ab'] = $msql->f('ab');
            } else {
                $tz[$i]['ab'] = '';
            }
            $i++;
        }
        $e = array(
            'tz' => $tz,
            'page' => $pstr,
            'sql' => $sql,
            'layer' => $layer
        );
        echo json_encode($e);
        unset($e);
        unset($tmp);
        break;
    case 'getxx':
        $abcd     = $_POST['abcd'];
        $ab       = $_POST['ab'];
        $pid      = $_POST['pid'];
        $sid      = $_POST['sid'];
        $con      = $_POST['con'];
        $puserid  = $_POST['puserid'];
        $orderby  = $_POST['orderby'];
        $sorttype = $_POST['sorttype'];
        $xtype    = $_POST['xtypes'];
        $qishu    = $_POST['qishu'];
        $page     = r1p($_POST['page']);
		$flys   = $_POST['flys'];
        
        $yq = " gid='{$gid}' and qishu='{$qishu}' and pid='{$pid}' ";
        if ($xtype == 0 | $xtype == 1) {
            $xstr = " and xtype='{$xtype}' ";
        }else if($flys!=1){
		    $xstr = " and xtype!=2 ";
		}
        if ($ab == 'A' | $ab == 'B') {
            $aandb .= " and ab='{$ab}' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $aandb .= " and abcd='{$abcd}' ";
        }
        if ($con != '') {
            $aandb .= " and content='$con' ";
        }
        $layer = transuser($userid, 'layer');
        if ($layer < $config['maxlayer']) {
            $pointsstr = 'points' . ($layer + 1);
            $peilv1str = 'peilv1' . ($layer + 1);
            $peilv2str = 'peilv2' . ($layer + 1);
            $uidstr    = 'uid' . $layer;
            if ($puserid != '' & $flys!=1) {
                $aandb .= " and (uid". ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
            }
        } else {
            $pointsstr = 'points';
            $peilv1str = 'peilv1';
            $peilv2str = 'peilv2';
            $uidstr    = 'uid' . $layer;
            if ($puserid != '' & $flys!=1) {
                $aandb .= " and userid='" . $puserid . "'";
            }
        }
        $myzcstr = 'zc' . $layer;
		if($flys!=1){
           $ustr = " and {$uidstr}='{$userid}' ";
		}else{
           $ustr = " and userid='{$userid}' ";
		}
     //   $zcstr = "zc0";
	  $zcstr  = 'zc' . $layer;
	   $sql = " select count(id) from `{$tb_lib}` where {$yq} {$ustr} {$xstr} {$aandb} ";
        $msql->query($sql);
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $psize  = 20;
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : ($rcount - $rcount % $psize) / $psize + 1;
        $sql    = " select * from `{$tb_lib}` where {$yq} {$ustr} {$xstr} {$aandb} ";
        if ($orderby == 'time') {
            $sql .= " order by time {$sorttype} ";
        } else {
            $sql .= " order by {$zcstr}*je {$sorttype} ";
        }
        $sql .= ' limit ' . ($page - 1) * $psize . ',' . $psize;
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
		
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='getxx".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu='" . $msql->f('qishu') . "'");
                $tmp['jj' . $msql->f('userid')] = 1;
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
            if($tmp['g'.$msql->f('gid')]==''){
                $fsql->query("select gname,fenlei from `$tb_game` where gid='".$msql->f('gid')."'");
                $fsql->next_record();
                $tmp['g' . $msql->f('gid')] = $fsql->f('gname');
                $tmp['f' . $msql->f('gid')] = $fsql->f('fenlei');
            }
            if($tmp['u'.$msql->f('userid')]==''){
                $tmp['u'.$msql->f('userid')] = transu2($msql->f('userid'));
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
            
            $tz[$i]['wf']    = wf($tmp['f' . $msql->f('gid')], $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            $tz[$i]['time']  = $msql->f('time');
            $tz[$i]['xtime'] =$msql->f('time');
            $tz[$i]['user']  = $tmp['u'.$msql->f('userid')];
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
            'tz' => $tz,
            'page' => $page,
			'pcount' => $pcount,
			'rcount' => $rcount,
            'xtype' => $xtype,
            'sql' => $sql
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
            if($tmp['g'.$msql->f('gid')]==''){
                $fsql->query("select gname,fenlei from `$tb_game` where gid='".$msql->f('gid')."'");
                $fsql->next_record();
                $tmp['g' . $msql->f('gid')] = $fsql->f('gname');
                $tmp['f' . $msql->f('gid')] = $fsql->f('fenlei');
            }
            if($tmp['u'.$msql->f('userid')]==''){
                $tmp['u'.$msql->f('userid')] = transu2($msql->f('userid'));
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
            
            $tz[$i]['wf'] = wf($tmp['f' . $msql->f('gid')], $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            
            $tz[$i]['time']    = $msql->f('time');
            $tz[$i]['xtime']   =substr($msql->f('time'),-8);
            $tz[$i]['bz']      = $msql->f('bz');
            $tz[$i]['flytype'] = transflytype($msql->f('flytype'));
            $tz[$i]['user']    = $tmp['u'.$msql->f('userid')];
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
    
    case "downfast":
        $layer = transuser($userid, 'layer');
		$qishu=$_GET['qishu'];
        $time  = "zd" . date("mdHis");
        if($gid==100){
           $msql->query("select gid,gname,thisqishu,fenlei from `{$tb_game}` where gid=100 order by gid");
        }else{
           $msql->query("select gid,gname,thisqishu,fenlei from `{$tb_game}` where gid!=100 order by gid");
        }
        header("Content-type: text/html; charset=utf-8");
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: filename=$time.xls");
        $td1 = "<td width='120'>";
        $td2 = "</td>";
        echo "<table border=1><tr>";
        echo $td1, "序号", $td2;
        echo $td1, "彩种", $td2;
        echo $td1, "期数", $td2;
        echo $td1, "会员", $td2;
        echo $td1, "时间", $td2;
        echo $td1, "盘类", $td2;
        echo $td1, "类别", $td2;
        echo $td1, "内容", $td2;
        echo $td1, "金额", $td2;
        echo $td1, "赔率", $td2;
        echo $td1, "退水", $td2;
        echo "</tr>";
        $i   = 1;
        $tmp = array();
        while ($msql->next_record()) {
            $gid   = $msql->f('gid');            
			if($gid==100){
				$fsql->query("select * from `$tb_lib` where gid='$gid' and qishu='$qishu' and (uid" . $layer . "='$userid' or userid='$userid') order by time desc"); 
			}else{
			   $qishu = $msql->f('thisqishu');
               $fsql->query("select * from `$tb_lib` where gid='$gid' and z=9 and (uid" . $layer . "='$userid' or userid='$userid') order by time desc"); 
			}
            while ($fsql->next_record()) {
                echo "<tr>";
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
            /*********************HELLO***************/
            if (in_array($fsql->f('userid'), $poarr)) {
                if ($fsql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $upoints -= 10;
                }
            }
            /*********************HELLO***************/
                echo $td1, $upoints, $td2;
                echo "</tr>";
                $i++;
            }
        }
        echo "</table>";
        /***********HELLO*******/
        $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='xls".$_SESSION['hides']."',time=NOW(),jkuser=0,qishu=0");
        /***********HELLO*******/
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
?>