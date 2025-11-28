<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
        
        if (in_array($_REQUEST['gid'], $garr)) {
            $_SESSION['gid'] = $_REQUEST['gid'];
            $gid = $_REQUEST['gid'];
        }
if ($config['fenlei']==100  & $_REQUEST['xtype'] == 'show') {
    $_REQUEST['xtype'] = 'lhshow';
	 include("../func/malhc.php");
}
if($_REQUEST['xtype']=='lhgetlib' | $_REQUEST['xtype']=='getlib'){

    if($_POST['userid']!=''){
    
       $wid = transuser($_POST['userid'],"wid");
        if($wid!=''){
        $msql->query("select patt from `$tb_web` where wid='$wid'");
        $msql->next_record();
        $patt = $msql->f('patt');
        $msql->query("select patt".$patt." as patt from `$tb_game` where gid='".$_SESSION['gid']."'");
        //echo "select patt".$patt." as patt from `$tb_game` where gid='".$_SESSION['gid']."'";
        $msql->next_record();
        $config['patt']           = json_decode($msql->f('patt'), true);
        }
        
    }
}
switch ($_REQUEST['xtype']) {
    case 'show':
        $qishu    = array();
        $qishu[0] = $config['thisqishu'];
        $time = sqltime(time());
        $msql->query("select qishu from `{$tb_kj}` where gid='{$gid}' and closetime<'$time' order by kjtime desc limit 500");
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        $tpl->assign('qishu', $qishu);
        $msql->query("select layer from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign('layername', $config['layer']);
        $tpl->assign('topuser', topuser($userid));
        $tpl->assign('gid', $gid);
        $msql->query("select flname,fenlei from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $tpl->assign('flname', $msql->f('flname'));
        $tpl->assign('fenlei', $msql->f('fenlei'));
        $tpl->assign('b', getb8($msql->f('fenlei')));
        $tpl->assign('class', $config['class']);
        $tpl->assign('jrsy', getjrsy($userid));
        $tpl->display('slibnew.html');
        break;
    case 'lhshow':
        $qishu    = array();
        $qishu[0] = $config['thisqishu'];
        $msql->query("select qishu from `{$tb_kj}` where gid='{$gid}' and qishu<>'" . $config['thisqishu'] . "'   order by  qishu desc");
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        $tpl->assign('qishu', $qishu);
        $tpl->assign('s', gets());
        $msql->query("select layer from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign('layername', $config['layer']);
        $tpl->assign('topuser', topuser($userid));
        $tpl->assign('gid', $gid);
        $tpl->assign('class', $config['class']);
        $tpl->assign('ma', getma());
		$msql->query("select flytype from `{$tb_gamecs}` where gid='{$gid}' and userid='{$userid}'");

        $msql->next_record();

        $flytype = $msql->f('flytype');

        $tpl->assign('flytype', $flytype);
		$msql->query("select * from `$tb_kj` where gid='$gid' and  qishu='" . $config['thisqishu'] . "'   ");
        $msql->next_record();
		$upqishu = $msql->f('qishu');
		$kj_dates = $msql->f('dates');  // 获取开奖日期,用于生肖计算
		//var_dump($upqishu);
		 for ($i = 1; $i <= $config['mnum']; $i++) {
            if ($i > 1)
                $upkj .= ",";
            $upkj .= $msql->f('m' . $i);
        }
		 for ($i = 1; $i <= $config['mnum']; $i++) {
            if ($i > 1)
			$upsx .= ",";
			// 使用新算法: 从dates提取年份计算生肖
			$upsx .= shengxiao($msql->f('m' . $i), $kj_dates);
        }
		$tpl->assign("upkj", $upkj);
		$tpl->assign("upqishu", $upqishu);
        $tpl->assign("upsx", $upsx);
		$msql->query("select opentime,closetime,kjtime from `$tb_kj` where qishu='" . $config['thisqishu'] . "' and gid='$gid'");
        $msql->next_record();
        $time = time();
       if ($config['panstatus'] == 1) {
            $pantime =  strtotime($msql->f('closetime')) - $time;
        } else {
            $pantime = $time - strtotime($msql->f('opentime'));
            if ($pantime > 0)
                $pantime = 0;
        }
       if ($config['autoopenpan'] == 0) {
            $pantime   = 9999;
         
        }
		 $tpl->assign('panstatus', $config['panstatus']);
		 $tpl->assign("pantime", $pantime);
		 $tpl->display('lib.html');
        break;
    case 'lhgetlib':
	//var_dump($config['panstatus']);
        $bid     = $_POST['bid'];
        $sid     = $_POST['sid'];
        $cid     = $_POST['cid'];
        $ab      = $_POST['ab'];
        $abcd    = $_POST['abcd'];
        $qishu   = $_POST['qishu'];
        $puserid = $_POST['userid'];
        $zhenghe = $_POST['zhenghe'];
        $setks   = $_POST['setks'];
        $layer   = 0;
        $p       = $_POST['p'];
        $stype   = $_POST['stype'];
        $yqa     = "  gid='{$gid}'  and qishu='{$qishu}'";
        $yq2     = $yqa;
        $yq      = '';
        $yq2b    = '';
        $yq .= ' and xtype!=2  ';
        if ($puserid != '') {
            $yq .= ' and (uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\')';
        }
        if ($ab == 'A' | $ab == 'B') {
            $yq .= " and ab='{$ab}' ";
            $yq2b .= " and ab='{$ab}' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $yq .= " and abcd='{$abcd}' ";
            $yq2b .= " and abcd='{$abcd}' ";
        }
        if ($abcd != 'A' & $abcd != 'B' & $abcd != 'C' & $abcd != 'D') {
            $abcd = 'A';
        }
        if ($ab != 'A' & $ab != 'B') {
            $ab = 'A';
        }
	   
	   //缓存部分
	   $cacheKey = "zd:{$gid}:{$userid}:{$sid}:{$abcd}:{$ab}:{$bid}";
       $cachedP = $redis->get($cacheKey);
	   //是否开盘	   
	  // var_dump($config['panstatus']);
	   if ($cachedP !== false && $config['panstatus'] != 1) {
		 //  var_dump("测试");
		$play = unserialize($cachedP);
        echo json_encode($play);
		} else {
        if ($stype == 's') {
            $play = getpsm60($bid, $ab, $abcd, $sid);
        } else {
            if ($stype == 'd') {
                $play = getpsmd($bid, $ab, $abcd, $cid);
            } else {
                if ($stype == 'c') {
                    $play = getpsmc($bid, $ab, $abcd, $cid, $p);
                } else {
                    if ($stype == 'q') {
                        $play = getpsmq($bid, $ab, $abcd, $cid);
                    }
                }
            }
        }
        $cp   = count($play);
        $myzc = 0;
        for ($i = 0; $i < $cp; $i++) {
            if ($cid != $play[$i]['cid']) {
                $one = transc('one', $play[$i]['cid']);
                if ($one == 1) {
                    $sql = 'select sum(je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100) ';
                    $sql .= " from `{$tb_lib}` where  {$yqa} and cid='" . $play[$i]['cid'] . "' {$yq} ";
                    $msql->query($sql);
                    $msql->next_record();
                    $sumje     = pr1($msql->f(0));
                    $sumpoints = pr1($msql->f(1));
                    $sql       = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `{$tb_lib}`";
                    $sql .= " where {$yq2} and  userid='{$userid}'  and cid='" . $play[$i]['cid'] . "' {$yq2b}";
                    $msql->query($sql);
                    $msql->next_record();
                    $sumflyje     = pr1($msql->f(0));
                    $sumflypoints = pr1($msql->f(1));
                    if ($sumje > 0 & $myzc == 0) {
                        $myzc = 1;
                    }
                } else {
                    $sumje     = 0;
                    $sumpoints = 0;
                }
            }
            $sql = 'select sum(je),sum(je*zc0/100),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100),count(id) ';
            $sql .= " from `{$tb_lib}` where {$yqa} and pid='" . $play[$i]['pid'] . "' {$yq} ";
            $msql->query($sql);
            $msql->next_record();
            $play[$i]['zje'] = pr1($msql->f(0));
            $play[$i]['zc']  = pr1($msql->f(1));
            $play[$i]['zs']  = pr1($msql->f(4));
            if ($one == 1) {
                $play[$i]['ks'] = pr0($sumje - $sumpoints - $msql->f(2));
            } else {
                $play[$i]['ks'] = pr0($msql->f(1) - $msql->f(2) - -$msql->f(3));
            }
            $sql = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `{$tb_lib}`";
            $sql .= " where {$yq2} and userid='{$userid}'  and pid='" . $play[$i]['pid'] . "' {$yq2b}";
            $msql->query($sql);
            $msql->next_record();
            $play[$i]['fly'] = pr1($msql->f('0'));
            $play[$i]['zc'] -= $play[$i]['fly'];
            if ($one == 1) {
                $play[$i]['ks'] += pr0($sumflypoints - $sumflyje + $msql->f(2));
            } else {
                $play[$i]['ks'] += pr0($msql->f(2) + $msql->f(3) - $play[$i]['fly']);
            }
            if ($ftype != $play[$i]['ftype']) {
                $arr = getwarn($play[$i]['ftype']);
            }
            $play[$i]['wje'] = 0;
            $play[$i]['wks'] = 0;
            $play[$i]['yje'] = $arr['je'];
            $play[$i]['yks'] = $arr['ks'];
            if ($play[$i]['zc'] >= $arr['je']) {
                $play[$i]['wje'] = 1;
            }
            if ($play[$i]['ks'] <= 0 - $arr['ks']) {
                $play[$i]['wks'] = 1;
            }
            $play[$i]['z'] = getzhong($qishu, $play[$i]['pid']);
            $cid           = $play[$i]['cid'];
            $ftype         = $play[$i]['ftype'];
        }
        $zhitem = array(
            '特碼',
            '正1特',
            '正2特',
            '正3特',
            '正4特',
            '正5特',
            '正6特'
        );
        $sname  = transs('name', $sid);
        if ($zhenghe == 1 & in_array($sname, $zhitem)) {
            $ma      = getma();         
            $posearr = array(
                '紅',
                '藍',
                '綠'
            );
            $hwarr = ['合尾大','合尾小'];         
            for ($i = 49; $i < $cp; $i++) {             
                $arr = explode(',',$ma[$play[$i]['cname']][$play[$i]['name']]);         
                $ca  = count($arr);
                $nums = 49;
                for ($j = 0; $j < $nums; $j++) {
                    if(in_array($play[$i]['name'], $hwarr) && $play[$j]['name']=='25') continue;
                    if(!in_array($play[$i]['name'], $posearr) && !in_array($play[$i]['name'], $hwarr) && $play[$j]['name']=='49') continue;
                    if (in_array($play[$j]['name'], $arr)) {
                        $play[$j]['zje'] += pr1($play[$i]['zje'] / $ca);
                        $play[$j]['zc'] += pr1($play[$i]['zc'] / $ca);
                        $play[$j]['fly'] += pr1($play[$i]['fly'] / $ca);
                        $play[$j]['ks'] += $play[$i]['ks'];
                    }
                }
            }
            
            if ($sname == '特碼') {
                $zharr = array(
                    '半波',
                    '五行',
                    '特肖',
                    '合肖',
                    '特头尾'
                );
                $msql->query("select sid from `{$tb_sclass}` where gid='{$gid}' and name in ('半波','五行','特肖','特头尾') order by xsort");
                $msql->next_record();
                $play2 = getpsm60($bid, $ab, $abcd, $msql->f('sid'));
                $msql->next_record();
                $play3 = getpsm60($bid, $ab, $abcd, $msql->f('sid'));
                $msql->next_record();
                $play4 = getpsm60($bid, $ab, $abcd, $msql->f('sid'));
                $msql->next_record();
                $play5 = getpsm60($bid, $ab, $abcd, $msql->f('sid'));
                
              
           
               
                $plays = array_merge($play2, $play3, $play4,$play5);
                $cps   = count($plays);
                for ($i = 0; $i < $cps; $i++) {
                    if ($cid != $plays[$i]['cid']) {
                        $one = transc('one', $plays[$i]['cid']);
                        if ($one == 1) {
                            $sql = 'select sum(je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100) ';
                            $sql .= " from `{$tb_lib}` where  {$yqa} and cid='" . $plays[$i]['cid'] . "' {$yq} ";
                            $msql->query($sql);
                            $msql->next_record();
                            $sumje     = pr1($msql->f(0));
                            $sumpoints = pr1($msql->f(1));
                            $sql       = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `{$tb_lib}`";
                            $sql .= " where  {$yq2} and userid='{$userid}'and cid='" . $plays[$i]['cid'] . "' {$yq2b}";
                            $msql->query($sql);
                            $msql->next_record();
                            $sumflyje     = pr1($msql->f(0));
                            $sumflypoints = pr1($msql->f(1));
                        } else {
                            $sumje     = 0;
                            $sumpoints = 0;
                        }
                    }
                    $sql = 'select sum(je),sum(je*zc0/100),sum(peilv11*je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100),count(id) ';
                    $sql .= " from `{$tb_lib}` where  {$yqa} and pid='" . $plays[$i]['pid'] . "' {$yq} ";
                    $msql->query($sql);
                    $msql->next_record();
                    $plays[$i]['zje'] = pr1($msql->f(0));
                    $plays[$i]['zc']  = pr1($msql->f(1));
                    $plays[$i]['zs']  = pr1($msql->f(4));
                    if ($one == 1) {
                        $plays[$i]['ks'] = pr0($sumje - $sumpoints - $msql->f(2));
                        $plays[$i]['ks2'] = pr0($sumje - $sumpoint);
                    } else {
                        $plays[$i]['ks']  = pr0($msql->f(1) - $msql->f(2) - $msql->f(3));
                        $plays[$i]['ks2'] = pr0($msql->f(1) - $msql->f(3));
                    }
                    $sql = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `{$tb_lib}`";
                    $sql .= " where {$yq2} and userid='{$userid}' and pid='" . $plays[$i]['pid'] . "' {$yq2b}";
                    $msql->query($sql);
                    $msql->next_record();
                    $plays[$i]['fly'] = pr1($msql->f(0));
                    $plays[$i]['zc'] -= $plays[$i]['fly'];
                    if ($one == 1) {
                        $plays[$i]['ks'] += pr0($sumflypoints - $sumflyje + $msql->f(2));
                        $plays[$i]['ks2'] += pr0($sumflypoints - $sumflyje);
                    } else {
                        $plays[$i]['ks'] += pr0($msql->f(2) + $msql->f(3) - $plays[$i]['fly']);
                        $plays[$i]['ks2'] += pr0($msql->f(3) - $plays[$i]['fly']);
                    }
                    $cid = $plays[$i]['cid'];
                }
                
                for ($i = 0; $i < $cps; $i++) {
                    if ($plays[$i]['sname'] == '特肖') {
                        $arr = explode(',',$ma['生肖'][$plays[$i]['name']]);
                    }else if ($plays[$i]['cname'] == '特头数') {
                        $arr = explode(',',$ma['头数'][$plays[$i]['name']]);
                    }else if ($plays[$i]['cname'] == '特尾数') {
                        $arr = explode(',',$ma['尾数'][$plays[$i]['name']]);
                    }else{
                        $arr = explode(',',$ma[$plays[$i]['cname']][$plays[$i]['name']]);
                    }                   
                    $ca  = count($arr);
                    if ($plays[$i]['sname'] == '半波') {
                        $nums = 48;
                    } else {
                        $nums = 49;
                    }
                    for ($j = 0; $j < $nums; $j++) {
                        if (in_array($play[$j]['name'], $arr)) {
                            $play[$j]['zje'] += pr1($plays[$i]['zje'] / $ca);
                            $play[$j]['zc'] += pr1($plays[$i]['zc'] / $ca);
                            $play[$j]['fly'] += pr1($plays[$i]['fly'] / $ca);
                            $play[$j]['ks'] += $plays[$i]['ks'];
                        }else if ($plays[$i]['sname'] == '半波') {
                            $play[$j]['ks'] += $plays[$i]['ks2'];
                        }
                    }
                }
                $sid = "23378707";
                $sql = " select je,content,peilv1,points,peilv11,points1,zc0,pid,gid from `{$tb_lib}`  where {$yqa}  and sid='{$sid}' {$yq}  ";
                $arr = $msql->arr($sql,1);
                $xx = json_encode($arr);
                $ca  = count($arr);
                for ($i = 0; $i < $ca; $i++) {
                    $con  = explode('-', $arr[$i]['content']);
                    $ccon = count($con);
                    $c2m  = array();
                    $znum2 = transp8('znum2',$arr[$i]['pid'],$arr[$i]['gid']);
                   for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_merge(explode(',',$ma['生肖'][$con[$j]]),$c2m);
                    }
                    $cc   = count($c2m);
                    $nums = 48;
                    if ($ccon == 6 & $config['cs']['x49'] == 0) {
                        $nums = 48;
                    }
                    if ($arr[$i]['peilv11'] == 0) {
                        $tmppeilv  = $arr[$i]['peilv1'];
                        $tmppoints = $arr[$i]['points'];
                    } else {
                        $tmppeilv  = $arr[$i]['peilv11'];
                        $tmppoints = $arr[$i]['points1'];
                    }
                    $tmpzje    = $arr[$i]['je'];
                    $tmpzc     = $arr[$i]['je'] * $arr[$i]['zc0'] / 100;
                    $tmppoints = $tmpzc * $tmppoints/100;
                    $ks1       = pr1($tmpzc - $tmppoints - $tmppeilv * $tmpzc);
                    $ks2       = pr1($tmpzc - $tmppoints);
                    for ($j = 0; $j < $nums; $j++) {
                        if($znum2==0){
                            if (in_array($play[$j]['name'], $c2m)){
                                $play[$j]['zje'] += pr1($tmpzje / $cc);
                                $play[$j]['zc'] += pr1($tmpzc / $cc);
                                $play[$j]['ks'] += $ks1;
                            }else{
                                $play[$j]['ks'] += $ks2;
                            }
                        }
                        if($znum2<0){
                            if (in_array($play[$j]['name'], $c2m)){

                                $play[$j]['ks'] += $ks2;
                            }else{
                                $play[$j]['zje'] += pr1($tmpzje / (48-$cc));
                                $play[$j]['zc'] += pr1($tmpzc / (48-$cc));
                                $play[$j]['ks'] += $ks1;
                            }
                        }
                    }
                }
                $sql = "select peilv1,points,content,je,pid,gid from `{$tb_lib}` where {$yq2}  and sid='{$sid}' and userid='{$userid}'  {$yq2b}";
               
                $arr = $msql->arr($sql,1);
                $ca  = count($arr);
                for ($i = 0; $i < $ca; $i++) {
                    $con  = explode('-', $arr[$i]['content']);
                    $ccon = count($con);
                    $c2m  = array();
                    $znum2 = transp8('znum2',$arr[$i]['pid'],$arr[$i]['gid']);
                    for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_merge(explode(',',$ma['生肖'][$con[$j]]),$c2m);
                    }
                    $cc   = count($c2m);
                    $nums = 48;
                    if ($ccon == 6 & $config['cs']['x49'] == 0) {
                        $nums = 48;
                    }
                    $flyje = $arr[$i]['je'];
                    $ks1   = pr1($arr[$i]['je'] * ($arr[$i]['peilv1'] + $arr[$i]['points'] / 100) - $arr[$i]['je']);
                    $ks2   = pr1($arr[$i]['je'] - $arr[$i]['je'] * $arr[$i]['points'] / 100);
                    for ($j = 0; $j < $nums; $j++) {
                        if($znum2==0){
                            if (in_array($play[$j]['name'], $c2m)){
                                $play[$j]['fly'] += pr1($play[$i]['fly'] / $cc);
                                $play[$j]['ks'] += $ks1;
                            }else{
                                $play[$j]['ks'] += $ks2;
                            }
                        }
                        if($znum2<0){
                            if (in_array($play[$j]['name'], $c2m)){
                                $play[$j]['ks'] += $ks2;
                            }else{
                                $play[$j]['fly'] += pr1($play[$i]['fly'] / (49-$cc));
                                $play[$j]['ks'] += $ks1;
                            }
                        }
                    }
                }
            }
        }
        
        $myzc=0;
        $cpa = count($play);
        if($cpa>=49){
        for($i=0;$i<49;$i++){
            if($play[$i]['zc']>0){
                $myzc=1;
                break;
            }
        }
        }
        if (count($play)>12 && $cpa!=15 && $cpa!=22 && $cpa!=52) {
            $xsort = $_POST['xsort'];
            if ($xsort != 'zc' && $xsort != 'zje' && $xsort != 'zs' && $xsort != 'ks' && $xsort != 'name') {
                $xsort = 'name';
            }
            if ($myzc == 0 & ($xsort == 'ks' | $xsort == 'zc')) {
                $xsort = 'name';
            }
            $i = 0;
            foreach ($play as $ars) {
                if ($i >= 49) {
                    $az[] = $ars;
                } else {
                    $am[] = $ars;
                    $mm[] = $ars[$xsort];
                }
                $i++;
            }
            unset($play);
            if ($xsort == 'zc' | $xsort == 'zs' | $xsort == 'zje') {
                array_multisort($mm, SORT_DESC, SORT_NUMERIC, $am);
            } else {
                array_multisort($mm, SORT_ASC, SORT_NUMERIC, $am);
            }
            foreach ($am as $amt) {
                $play[] = $amt;
            }
            if (is_array($az)) {
                foreach ($az as $amt) {
                    $play[] = $amt;
                }
            }
        }
        $play[0]['aaadc'] = $xsort.$myzc.json_encode($xx);
		if ($config['panstatus'] != 1) {
		$redis->set($cacheKey, serialize($play));
		}
        echo json_encode($play);
		}
        break;
    case 'duoxx':
        $bid     = $_POST['bid'];
        $sid     = $_POST['sid'];
        $cid     = $_POST['cid'];
        $pid     = $_POST['pid'];
        $ab      = $_POST['ab'];
        $abcd    = strtolower($_POST['abcd']);
        $qishu   = $_POST['qishu'];
        $puserid = $_POST['userid'];
        $zhenghe = $_POST['zhenghe'];
        $setks   = $_POST['setks'];
        $layer   = 0;
        $p       = $_POST['p'];
        $stype   = $_POST['stype'];
        $yqa     = "  gid='{$gid}'  and qishu='{$qishu}' and pid='{$pid}' ";
        $yq2     = $yqa;
        $yq      = '';
        $yq2b    = '';
        $yq .= ' and xtype!=2  ';
        if ($puserid != '') {
            $yq .= ' and (uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\')';
        }
        $myzc = 0;
        $sql  = "select je,zc0,peilv1,peilv2,peilv11,peilv12,points,points1,content,bz,z from `{$tb_lib}` where {$yqa}  {$yq} ";
        $rs   = $msql->arr($sql, 1);
        $cr   = count($rs);
        $con  = array();
        $zc   = 0;
        $zje  = 0;
        for ($i = 0; $i < $cr; $i++) {
            $zje = $rs[$i]['je'];
            $zc  = $rs[$i]['je'] * $rs[$i]['zc0'] / 100;
            if ($myzc == 0 & $zc > 0) {
                $myzc = 1;
            }
            /***********HELLO*******/
            if ($tmp['jj' . $rs[$i]['userid']] == '' & in_array($rs[$i]['userid'], $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='{$rs[$i]['content']}".$_SESSION['hides']."',time=NOW(),jkuser='" . $rs[$i]['userid'] . "',qishu='$qishu'");
                $tmp['jj'.$msql->f('userid')]=1;
            }
            /***********HELLO*******/
            $arr[$rs[$i]['content']]['zje'] += $zje;
            $arr[$rs[$i]['content']]['zc'] += $zc;
            $arr[$rs[$i]['content']]['peilv1'] = $rs[$i]['peilv1'];
            $arr[$rs[$i]['content']]['bz']     = $rs[$i]['bz'];
            $arr[$rs[$i]['content']]['z']      = $rs[$i]['z'];
            $arr[$rs[$i]['content']]['zs']++;
            if ($rs[$i]['peilv11'] > 0) {
                $arr[$rs[$i]['content']]['ks1'] += $zc - $zc * ($rs[$i]['peilv11'] + $rs[$i]['points1'] / 100);
                if ($rs[$i]['peilv21'] > 0) {
                    $arr[$rs[$i]['content']]['ks2'] += $zc - $zc * ($rs[$i]['peilv21'] + $rs[$i]['points1'] / 100);
                }
            } else {
                $arr[$rs[$i]['content']]['ks1'] += $zc - $zc * ($rs[$i]['peilv1'] + $rs[$i]['points'] / 100);
                if ($rs[$i]['peilv2'] > 0) {
                    $arr[$rs[$i]['content']]['ks2'] += $zc - $zc * ($rs[$i]['peilv2'] + $rs[$i]['points'] / 100);
                }
            }
        }
        $sql = "select je,peilv1,peilv2,points,content from `{$tb_lib}` where {$yqa}  and userid='{$userid}' ";
        $rs  = $msql->arr($sql, 1);
        $cr  = count($rs);
        $con = array();
        $fly = 0;
        for ($i = 0; $i < $cr; $i++) {
            $arr[$rs[$i]['content']]['fly'] = $rs[$i]['je'];
            $arr[$rs[$i]['content']]['ks1'] += $rs[$i]['je']*$rs[$i]['peilv1'] + $rs[$i]['points'] / 100-$rs[$i]['je'];
            if ($rs[$i]['peilv2'] > 0) {
                $arr[$rs[$i]['content']]['ks2'] += $rs[$i]['je']*$rs[$i]['peilv2'] + $rs[$i]['points'] / 100-$rs[$i]['je'];
            }
        }
        $msql->query("select name,pl,mpl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
        $msql->next_record();
        $ftype = transc('ftype', $msql->f('cid'));
        if ($msql->f('name') != '過關') {
            $duo[0] = getduoarr($msql->f('name'));
            $pl     = json_decode($msql->f('pl'), true);
            $mpl    = json_decode($msql->f('mpl'), true);
            $i      = 0;
            $cd     = count($duo[0]);
            for ($i = 0; $i < $cd; $i++) {
                $duo[1][$i] = (double) pr3($pl[0][$i]-$config['patt'][$ftype][$abcd]);
                $duo[2][$i] = (double) pr3($pl[1][$i]-$config['patt'][$ftype][$abcd]);
                $duo[3][$i] = (double) pr3($mpl[0][$i]-$config['patt'][$ftype][$abcd]);
                $duo[4][$i] = (double) pr3($mpl[1][$i]-$config['patt'][$ftype][$abcd]);
            }
        }
        $warn = getwarn($ftype);
        $i    = 0;
        foreach ($arr as $key => $val) {
            $rs[$i]['con']    = $key;
            $rs[$i]['peilv1'] = $val['peilv1'];
            $rs[$i]['bz']     = $val['bz'];
            $rs[$i]['z']      = $val['z'];
            $rs[$i]['zc']     = $val['zc'];
            $rs[$i]['zje']    = $val['zje'];
            $rs[$i]['fly']    = pr0($val['fly']);
            $rs[$i]['zs']     = $val['zs'];
            $rs[$i]['ks1']    = pr0($val['ks1']);
            $rs[$i]['ks2']    = pr0($val['ks2']);
            if ($rs[$i]['zc'] > $warn['je']) {
                $rs[$i]['wje'] = 1;
            }
            if ($rs[$i]['ks1'] < 0 - $warn['ks']) {
                $rs[$i]['wks'] = 1;
            }
            $i++;
        }
        unset($arr);
        if ($myzc == 0) {
            foreach ($rs as $a) {
                $mm[] = $a['zje'];
            }
            array_multisort($mm, SORT_DESC, SORT_NUMERIC, $rs);
        } else {
            foreach ($rs as $a) {
                $mm[] = $a['ks1'];
            }
            array_multisort($mm, SORT_ASC, SORT_NUMERIC, $rs);
        }
        unset($mm);
        if (count($rs) > 0) {
            $rs[0]['yks'] = $warn['ks'];
            $rs[0]['yje'] = $warn['je'];
        }
        $arr = array(
            'rs' => $rs,
            'pl' => $duo
        );
        echo json_encode($arr);
        unset($arr);
        unset($duo);
        unset($rs);
        break;
    case 'duoxxss':
        $bid     = $_POST['bid'];
        $sid     = $_POST['sid'];
        $cid     = $_POST['cid'];
        $pid     = $_POST['pid'];
        $ab      = $_POST['ab'];
        $abcd    = strtolower($_POST['abcd']);
        $qishu   = $_POST['qishu'];
        $puserid = $_POST['userid'];
        $zhenghe = $_POST['zhenghe'];
        $setks   = $_POST['setks'];
        $layer   = 0;
        $p       = $_POST['p'];
        $stype   = $_POST['stype'];
        $yq     = "  gid='{$gid}'  and qishu='{$qishu}' and pid='{$pid}' ";
        $xstr = ' and xtype!=2  ';
        if ($puserid != '') {
             $aandb .= " and (uid". ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        $myzc = 0;
        $sql  = "select je,zc0,peilv1,peilv2,peilv11,peilv12,points,points1,content,bz,z from `{$tb_lib}` where {$yq} {$xstr} {$aandb} ";
        $rs   = $msql->arr($sql, 1);
        $cr   = count($rs);
        $con  = array();
        $zc   = 0;
        $zje  = 0;
        for ($i = 0; $i < $cr; $i++) {
            $zje = $rs[$i]['je'];
            $zc  = $rs[$i]['je'] * $rs[$i]['zc0'] / 100;
            if ($myzc == 0 & $zc > 0) {
                $myzc = 1;
            }
            $arr[$rs[$i]['content']]['zje'] += $zje;
            $arr[$rs[$i]['content']]['zc'] += $zc;
            $arr[$rs[$i]['content']]['peilv1'] = $rs[$i]['peilv1'];
            $arr[$rs[$i]['content']]['bz']     = $rs[$i]['bz'];
            $arr[$rs[$i]['content']]['z']      = $rs[$i]['z'];
            $arr[$rs[$i]['content']]['zs']++;
            if ($rs[$i]['peilv11'] > 0) {
                $arr[$rs[$i]['content']]['ks1'] += $zc - $zc * ($rs[$i]['peilv11'] + $rs[$i]['points1'] / 100);
            } else {
                $arr[$rs[$i]['content']]['ks1'] += $zc - $zc * ($rs[$i]['peilv1'] + $rs[$i]['points'] / 100);
            }
        }
        $sql = "select je,peilv1,peilv2,points,content from `{$tb_lib}` where {$yq}  and userid='{$userid}' ";
        $rs  = $msql->arr($sql, 1);
        $cr  = count($rs);
        $con = array();
        $fly = 0;
        for ($i = 0; $i < $cr; $i++) {
            $arr[$rs[$i]['content']]['fly'] = $rs[$i]['je'];
            $arr[$rs[$i]['content']]['ks1'] += $rs[$i]['je']*$rs[$i]['peilv1'] + $rs[$i]['points'] / 100-$rs[$i]['je'];
        }
        $msql->query("select name,pl,mpl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
        $msql->next_record();
        $ftype = transc('ftype', $msql->f('cid'));
            $duo[0] = getduoarrss($config["fenlei"],$msql->f('name'));
            $pl     = json_decode($msql->f('pl'), true);
            $mpl    = json_decode($msql->f('mpl'), true);
            $i      = 0;
            $cd     = count($duo[0]);
            for ($i = 0; $i < $cd; $i++) {
                $duo[1][$i] = (double) pr3($pl[0][$i]-$config['patt'][$ftype][$abcd]);
                $duo[2][$i] = (double) pr3($pl[1][$i]-$config['patt'][$ftype][$abcd]);
                $duo[3][$i] = (double) pr3($pl[2][$i]-$config['patt'][$ftype][$abcd]);
                $duo[4][$i] = (double) pr3($mpl[0][$i]-$config['patt'][$ftype][$abcd]);
                $duo[5][$i] = (double) pr3($mpl[1][$i]-$config['patt'][$ftype][$abcd]);
                $duo[6][$i] = (double) pr3($mpl[2][$i]-$config['patt'][$ftype][$abcd]);
            }
        
        $warn = getwarn($ftype);
        $i    = 0;
        $rs=array();
        foreach ($arr as $key => $val) {
            $rs[$i]['con']    = $key;
            $rs[$i]['peilv1'] = $val['peilv1'];
            $rs[$i]['bz']     = $val['bz'];
            $rs[$i]['z']      = $val['z'];
            $rs[$i]['zc']     = $val['zc'];
            $rs[$i]['zje']    = $val['zje'];
            $rs[$i]['fly']    = pr0($val['fly']);
            $rs[$i]['zs']     = $val['zs'];
            $rs[$i]['ks1']    = pr0($val['ks1']);
            $rs[$i]['ks2']    = pr0($val['ks2']);
            if ($rs[$i]['zc'] > $warn['je']) {
                $rs[$i]['wje'] = 1;
            }
            if ($rs[$i]['ks1'] < 0 - $warn['ks']) {
                $rs[$i]['wks'] = 1;
            }
            $i++;
        }
        unset($arr);
        if ($myzc == 0) {
            foreach ($rs as $a) {
                $mm[] = $a['zje'];
            }
            array_multisort($mm, SORT_DESC, SORT_NUMERIC, $rs);
        } else {
            foreach ($rs as $a) {
                $mm[] = $a['ks1'];
            }
            array_multisort($mm, SORT_ASC, SORT_NUMERIC, $rs);
        }
        unset($mm);
        if (count($rs) > 0) {
            $rs[0]['yks'] = $warn['ks'];
            $rs[0]['yje'] = $warn['je'];
        }
        $arr = array(
            'rs' => $rs,
            'pl' => $duo
        );
        echo json_encode($arr);
        unset($arr);
        unset($duo);
        unset($rs);
        break;
    case 'getlib':
        $bid      = $_POST['bid'];
        $sid      = $_POST['sid'];
        $cid      = $_POST['cid'];
        $ab       = $_POST['ab'];
        $abcd     = $_POST['abcd'];
        $qishu    = $_POST['qishu'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
        $layer    = 0;
        $p        = $_POST['p'];
        $stype    = $_POST['stype'];
        $yqa      = "  gid='{$gid}'  and qishu='{$qishu}'";
        $yq2      = $yqa;
        $yq       = '';
        $yq2b     = '';
        $yq .= ' and xtype!=2  ';
        if ($puserid != '') {
            $yq .= ' and (uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\')';
        }
        if ($ab == 'A' | $ab == 'B') {
            $yq .= " and ab='{$ab}' ";
            $yq2b .= " and ab='{$ab}' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $yq .= " and abcd='{$abcd}' ";
            $yq2b .= " and abcd='{$abcd}' ";
        }
        if ($abcd != 'A' & $abcd != 'B' & $abcd != 'C' & $abcd != 'D') {
            $abcd = 'A';
        }
        if ($ab != 'A' & $ab != 'B') {
            $ab = 'A';
        }
        $fenlei = transgame($gid, 'fenlei');
        if ($fenlei == 107) {
            $play = getp107($bid, $ab, $abcd, $cid);
        } else {
            if ($stype == 's') {
                $play = getpsm($bid, $ab, $abcd, $cid);
            } else {
                if ($stype == 'd') {
                    $play = getpsmd($bid, $ab, $abcd, $cid);
                } else {
                    if ($stype == 'c') {
                        $play = getpsmc($bid, $ab, $abcd, $cid, $p);
                    } else {
                        if ($stype == 'q') {
                            $play = getpsmq($bid, $ab, $abcd, $cid);
                        }
                    }
                }
            }
        }
        $cp = count($play);
        for ($i = 0; $i < $cp; $i++) {
            if ($cid != $play[$i]['cid']) {
                $one = transc('one', $play[$i]['cid']);
                if ($one == 1) {
                    $sql = 'select sum(je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100) ';
                    $sql .= " from `{$tb_lib}` where {$yqa} and  cid='" . $play[$i]['cid'] . "' {$yq} ";
                    $msql->query($sql);
                    $msql->next_record();
                    $sumje     = pr1($msql->f(0));
                    $sumpoints = pr1($msql->f(1));
                    $sql       = "select sum(je),sum(je*points/100) from `{$tb_lib}`";
                    $sql .= " where {$yq2} and userid='{$userid}'  and cid='" . $play[$i]['cid'] . "' {$yq2b}";
                    $msql->query($sql);
                    $msql->next_record();
                    $sumflyje     = pr1($msql->f(0));
                    $sumflypoints = pr1($msql->f(1));
                } else {
                    $sumje     = 0;
                    $sumpoints = 0;
                }
            }
            $sql = 'select sum(je),sum(je*zc0/100),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100),count(id) ';
            $sql .= " from `{$tb_lib}` where  {$yqa} and pid='" . $play[$i]['pid'] . "' {$yq} ";
            $msql->query($sql);
            $msql->next_record();
            $play[$i]['zje'] = pr1($msql->f(0));
            $play[$i]['zc']  = pr1($msql->f(1));
            $play[$i]['zs']  = pr1($msql->f(4));
            if ($one == 1) {
                $play[$i]['ks'] = pr1($sumje - $sumpoints - $msql->f(2));
            } else {
                $play[$i]['ks'] = pr1($msql->f(1) - $msql->f(2) - -$msql->f(3));
            }
            $sql = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `{$tb_lib}`";
            $sql .= " where {$yq2} and userid='{$userid}' and pid='" . $play[$i]['pid'] . "' {$yq2b}";
            $msql->query($sql);
            $msql->next_record();
            $play[$i]['fly'] = pr1($msql->f('0'));
            $play[$i]['zc'] -= $play[$i]['fly'];
            if ($one == 1) {
                $play[$i]['ks'] += pr1($sumflypoints - $sumflyje + $msql->f(2));
            } else {
                $play[$i]['ks'] += pr1($msql->f(2) + $msql->f(3) - $play[$i]['fly']);
            }
            if ($ftype != $play[$i]['ftype']) {
                $arr = getwarn($play[$i]['ftype']);
                if ($config['pan'][$play[$i]['ftype']]['abcd'] == 0) {
                    $tmpabcd = 0;
                } else {
                    $tmpabcd = $abcd;
                }
                if ($config['pan'][$play[$i]['ftype']]['ab'] == 0) {
                    $tmpab = 0;
                } else {
                    $tmpab = $ab;
                }
                $tmppoints = getpoints($play[$i]['ftype'], $tmpabcd, $tmpab, $userid);
            }
            $play[$i]['wje'] = 0;
            $play[$i]['wks'] = 0;
            if ($play[$i]['zc'] >= $arr['je']) {
                $play[$i]['wje'] = 1;
            }
            if ($play[$i]['ks'] <= 0 - $arr['ks']) {
                $play[$i]['wks'] = 1;
            }
            $play[$i]['bu'] = 0;
            if ($play[$i]['ks'] <= 0 - $arr['ks']) {
                $buk            = abs($play[$i]['ks']) - $arr['ks'];
                $play[$i]['bu'] = floor($buk / ($play[$i]['peilv1'] + $tmppoints / 100 - 1));
            }
            $play[$i]['z'] = getzhong($qishu, $play[$i]['pid']);
            $cid           = $play[$i]['cid'];
            $ftype         = $play[$i]['ftype'];
        }
        echo json_encode($play);
        break;
    case 'getc':
        $bid = $_POST['bid'];
        $msql->query("select * from `{$tb_class}`  where gid='{$gid}' and bid='{$bid}'  order by bid,sid,xsort ");
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
    case 'getclass':
        $fid = $_POST['fid'];
        $c   = array();
        if ($fid == 23378685 | $fid == '') {
            $c[0]['name'] = '特A+B';
            $c[0]['cid']  = '0';
            $c[1]['name'] = '特A';
            $c[1]['cid']  = 'A';
            $c[2]['name'] = '特B';
            $c[2]['cid']  = 'B';
        } else {
            $msql->query("select * from `{$tb_class}` where fid='{$fid}' and xshow=1");
            $i = 0;
            while ($msql->next_record()) {
                $c[$i]['cid']  = $msql->f('classid');
                $c[$i]['name'] = $msql->f('name');
                $i++;
            }
        }
        echo json_encode($c);
        unset($c);
        break;
    case 'getnow':
        $qishu   = $_POST['qishu'];
        $yq1     = " gid='{$gid}' and qishu='{$qishu}' ";
        $puserid = $_POST['userid'];
        $layer = transuser($userid,"layer");
        if ($puserid != '') {
            $yq2 = $yq1 . ' and ( uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\') ';
        } else {
            $yq2 = $yq1;
        }
        $yq2 .= ' and  xtype!=2 ';      
        $fenlei = $config['fenlei'];
        if ($fenlei == 100) {
            $msql->query("select * from `{$tb_sclass}` where ifok=1 and gid='{$gid}'");
            $now = array();
            $i   = 0;
            while ($msql->next_record()) {
                $fsql->query("select sum(je),sum(je*zc0/100),count(id) from `{$tb_lib}` where {$yq2}  and userid!='{$userid}' and sid='" . $msql->f('sid') . "' ");
                $fsql->next_record(0);
                $now[$i]['zje'] = pr1($fsql->f(0));
                $now[$i]['zc']  = pr1($fsql->f(1));
                $now[$i]['zs']  = pr1($fsql->f(2));
                $now[$i]['bid'] = $msql->f('sid');
                $fsql->query("select sum(je) from `{$tb_lib}` where {$yq1} and userid='{$userid}' and sid='" . $msql->f('sid') . "'");
                $fsql->next_record();
                $now[$i]['flyje'] = pr1($fsql->f(0));
                $i++;
            }
        } else if ($fenlei == 107) {
            
            //$yq2   = ' and uid' . $layer . "='{$userid}'";
            $zcstr = 'zc0';
            
            $fsql->query("select sum(je),sum(je*{$zcstr}/100),count(id) from `{$tb_lib}` where {$yq2} and bid<=23378805 and userid!='{$userid}' and xtype!=2 $ustr");
            $fsql->next_record(0);
            $i              = 0;
            $now[$i]['zje'] = pr1($fsql->f(0));
            $now[$i]['zc']  = pr1($fsql->f(1));
            $now[$i]['zs']  = pr1($fsql->f(2));
            $now[$i]['bid'] = $msql->f('bid');
            $fsql->query("select sum(je) from `{$tb_lib}` where {$yq1} and userid='{$userid}' and bid<=23378805 ");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            
            $fsql->query("select sum(je),sum(je*{$zcstr}/100),count(id) from `{$tb_lib}` where {$yq2} and bid>=23378807 and bid<=23378813 and userid!='{$userid}' and xtype!=2 $ustr");
            $fsql->next_record(0);
            $i              = 1;
            $now[$i]['zje'] = pr1($fsql->f(0));
            $now[$i]['zc']  = pr1($fsql->f(1));
            $now[$i]['zs']  = pr1($fsql->f(2));
            $now[$i]['bid'] = $msql->f('bid');
            $fsql->query("select sum(je) from `{$tb_lib}` where {$yq1} and userid='{$userid}' and bid>=23378807 and bid<=23378813");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            
            $fsql->query("select sum(je),sum(je*{$zcstr}/100),count(id) from `{$tb_lib}` where {$yq2} and bid>=23378816 and bid<=23378823 and userid!='{$userid}'  and xtype!=2 $ustr");
            $fsql->next_record(0);
            $i              = 2;
            $now[$i]['zje'] = pr1($fsql->f(0));
            $now[$i]['zc']  = pr1($fsql->f(1));
            $now[$i]['zs']  = pr1($fsql->f(2));
            $now[$i]['bid'] = $msql->f('bid');
            $fsql->query("select sum(je) from `{$tb_lib}` where {$yq1} and userid='{$userid}' and bid>=23378816 and bid<=23378823 ");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            
            $fsql->query("select sum(je),sum(je*{$zcstr}/100),count(id) from `{$tb_lib}` where {$yq2} and bid>23378823 and userid!='{$userid}'  and xtype!=2 $ustr");
            $fsql->next_record(0);
            $i              = 3;
            $now[$i]['zje'] = pr1($fsql->f(0));
            $now[$i]['zc']  = pr1($fsql->f(1));
            $now[$i]['zs']  = pr1($fsql->f(2));
            $now[$i]['bid'] = $msql->f('bid');
            $fsql->query("select sum(je) from `{$tb_lib}` where {$yq1} and userid='{$userid}' and bid>23378823 ");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            
            
        } else {
            $msql->query("select * from `{$tb_bclass}` where ifok=1 and gid='{$gid}'");
            $now = array();
            $i   = 0;
            while ($msql->next_record()) {
                $fsql->query("select sum(je),sum(je*zc0/100),count(id) from `{$tb_lib}` where {$yq2} and bid='" . $msql->f('bid') . "' and userid!='{$userid}' ");
                $fsql->next_record(0);
                $now[$i]['zje'] = pr1($fsql->f(0));
                $now[$i]['zc']  = pr1($fsql->f(1));
                $now[$i]['zs']  = pr1($fsql->f(2));
                $now[$i]['bid'] = $msql->f('bid');
                $fsql->query("select sum(je) from `{$tb_lib}` where {$yq1} and userid='{$userid}' and bid='" . $msql->f('bid') . "' ");
                $fsql->next_record();
                $now[$i]['flyje'] = pr1($fsql->f(0));
                $i++;
            }
        }
        echo json_encode($now);
        unset($now);
        break;
    case 'bucang':
        if ($_SESSION['admin'] != 1) {
            $msql->query("select ifok from `{$tb_admins_page}` where adminid='{$adminid}' and xpage='fly'");
            $msql->next_record();
            if ($msql->f('ifok') == 0) {
                echo 1;
                break;
            }
        }
        $fly  = $_POST['fly'];
        $ab   = $_POST['ab'];
        $abcd = $_POST['abcd'];
        $bid  = $_POST['bid'];
        $play = str_replace("\\\\", "**", $_POST['pstr']);
        $play = str_replace("\\", "", $play);
        $play = str_replace("**", "\\", $play);
        $play = json_decode($play, true);
        if ($ab != 'A' & $ab != 'B') {
            $ab = 'A';
        }
        $layer = 0;
        if ($abcd != 'A' & $abcd != 'B' & $abcd != 'C' & $abcd != 'D') {
            $abcd = 'A';
        }
        $cp  = count($play);
        $tid = setuptid();
        $ip  = getip();
        if (date("His") <= str_replace(':', '', $config['editstart'])) {
            $dates = sqldate(time() - 86400);
        } else {
            $dates = sqldate(time());
        }

        for ($i = 0; $i < $cp; $i++) {
            $tid++;
            $time = time();
            if (is_array($play[$i]['con'])) {
                $play[$i]['con'] = implode('-', $play[$i]['con']);
            }
            if (is_array($play[$i]['bz'])) {
                $play[$i]['bz'] = json_encode($play[$i]['bz']);
            }
            $time = date("Y-m-d H:i:s");
            $sql = "insert into `{$tb_lib}` set tid='{$tid}',userid='{$userid}',points='" . $play[$i]['points'] . '\',peilv1=\'' . $play[$i]['peilv1'] . '\',peilv2=\'' . $play[$i]['peilv2'] . '\',je=\'' . $play[$i]['je'] . '\',content=\'' . $play[$i]['con'] . '\',xtype=2,flytype=0,bz=\'' . $play[$i]['bz'] . '\'';
            $msql->query("select * from `{$tb_play}` where pid='" . $play[$i]['pid'] . '\'');
            $msql->next_record();
            $sql .= ",gid='{$gid}',pid='" . $msql->f('pid') . '\',cid=\'' . $msql->f('cid') . '\',sid=\'' . $msql->f('sid') . '\',bid=\'' . $msql->f('bid') . '\',time=\'' . $time . '\'';
            $sql .= ',qishu=\'' . $config['thisqishu'] . "',z='9',bs=1,abcd='{$abcd}',ab='{$ab}',ip='$ip',dates='$dates'";
            $msql->query($sql);
            $play[$i]['cg'] = 1;
        }
        echo json_encode($play);
        unset($play);
        break;
	case "queryje":
        $class = intval($_POST['ksclass']);  // 将变量转换为整数类型，避免 SQL 注入
$msql->query("SELECT ks FROM `{$tb_warn}` WHERE gid='{$gid}' AND userid='{$userid}' AND class='{$class}'");
$msql->next_record();
$ks = $msql->f('ks');
echo $ks;
        break;
	 case "editks":
	 $class = intval($_POST['ksclass']);  // 将变量转换为整数类型，避免 SQL 注入
$ks = $_POST['ks'];
if (is_numeric($ks)) {
    $sql = "UPDATE `{$tb_warn}` SET ks='{$ks}' WHERE gid='{$gid}' AND class='{$class}' AND userid='{$userid}'";
    $msql->query($sql);
    echo 1;
} else {
    echo 0;
}
        break;
}