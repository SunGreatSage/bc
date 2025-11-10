<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
if ($_SESSION['gid'] != 100) {
    header("Location:slib.php?xtype=show");
    exit;
}
switch ($_REQUEST['xtype']) {
    case "show":
        $qishu    = array();
        $qishu[0] = $config['thisqishu'];
        $msql->query("select qishu from `$tb_lib` where gid='$gid' and qishu<>'" . $config['thisqishu'] . "'   group by qishu desc");
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        $tpl->assign('qishu', $qishu);
        //$tpl->assign("b", getb());
        $tpl->assign("s", gets());
        $msql->query("select layer from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign("layername", $config['layer']);
        $tpl->assign("topuser", topuser($userid));
        $tpl->assign("gid", $gid);
        $tpl->assign("class", $config['class']);
		$tpl->assign("ma", getma());
        $tpl->display("lib.html");
        break;
    case "getlib":
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
        $yqa     = "  gid='$gid'  and qishu='$qishu'";
        $yq2     = $yqa;
		$yq='';
		$yq2b='';
        $yq .= " and xtype!=2  ";
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        if ($ab == 'A' | $ab == 'B') {
            $yq .= " and ab='$ab' ";
			$yq2b .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $yq .= " and abcd='$abcd' ";
			$yq2b .= " and abcd='$abcd' ";
        }
        if ($abcd != 'A' & $abcd != 'B' & $abcd != 'C' & $abcd != 'D')
            $abcd = 'A';
        if ($ab != 'A' & $ab != 'B')
            $ab = 'A';
        if ($stype == 's') {
            $play = getpsm60($bid, $ab, $abcd, $sid);
        } else if ($stype == 'd') {
            $play = getpsmd($bid, $ab, $abcd, $cid);
        } else if ($stype == 'c') {
            $play = getpsmc($bid, $ab, $abcd, $cid, $p);
        } else if ($stype == 'q') {
            $play = getpsmq($bid, $ab, $abcd, $cid);
        }
        $cp = count($play);
		$myzc=0;
        for ($i = 0; $i < $cp; $i++) {
            if ($cid != $play[$i]['cid']) {
                $one = transc('one', $play[$i]['cid']);
                if ($one == 1) {
                    $sql = "select sum(je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100) ";
                    $sql .= " from `$tb_lib` where  $yqa and cid='" . $play[$i]['cid'] . "' $yq ";
                    $msql->query($sql);
                    $msql->next_record();
                    $sumje     = pr1($msql->f(0));
                    $sumpoints = pr1($msql->f(1));
                    $sql       = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `$tb_lib`";
                    $sql .= " where $yq2 and  userid='$userid'  and cid='" . $play[$i]['cid'] . "' $yq2b";
                    $msql->query($sql);
                    $msql->next_record();
                    $sumflyje     = pr1($msql->f(0));
                    $sumflypoints = pr1($msql->f(1));
					if($sumje>0 & $myzc==0) $myzc=1;
                } else {
                    $sumje     = 0;
                    $sumpoints = 0;
                }
            }
            $sql = "select sum(je),sum(je*zc0/100),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100),count(id) ";
            $sql .= " from `$tb_lib` where $yqa and pid='" . $play[$i]['pid'] . "' $yq ";
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
            $sql = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `$tb_lib`";
            $sql .= " where $yq2 and userid='$userid'  and pid='" . $play[$i]['pid'] . "' $yq2b";
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
            }
            $play[$i]['wje'] = 0;
            $play[$i]['wks'] = 0;
			$play[$i]['yje'] = $arr['je'];
			$play[$i]['yks'] = $arr['ks'];
            if ($play[$i]['zc'] >= $arr['je']) {
                $play[$i]['wje'] = 1;
            }
            if ($play[$i]['ks'] <= (0 - $arr['ks'])) {
                $play[$i]['wks'] = 1;
            }
            $play[$i]['z'] = getzhong($qishu, $play[$i]['pid']);
            $cid           = $play[$i]['cid'];
            $ftype         = $play[$i]['ftype'];
        }
        $zhitem = array(
            "特碼",
            "正1特",
            "正2特",
            "正3特",
            "正4特",
            "正5特",
            "正6特"
        );
        $sname  = transs('name', $sid);
        if ($zhenghe == 1 & in_array($sname, $zhitem)) {
            $ma      = getma();
            $posearr = array(
                "紅",
                "藍",
                "綠"
            );
            for ($i = 49; $i < $cp; $i++) {
                $arr = $ma[$play[$i]['name']];
                $ca  = count($arr);
                if ($config['cs']['x49'] == 1 | in_array($play[$i]['name'], $posearr)) {
                    $nums = 49;
                } else {
                    $nums = 48;
                }
                for ($j = 0; $j < $nums; $j++) {
                    if (in_array($play[$j]['name'], $arr)) {
                        $play[$j]['zje'] += pr1($play[$i]['zje'] / $ca);
                        $play[$j]['zc'] += pr1($play[$i]['zc'] / $ca);
                        $play[$j]['fly'] += pr1($play[$i]['fly'] / $ca);
                        $play[$j]['ks'] += $play[$i]['ks'];
                    } else {
                        $play[$j]['ks'] += $play[$i]['ks'];
                    }
                }
            }
            if ($sname == '特碼') {
                $zharr = array(
                    "半波",
                    "五行",
                    "特肖",
                    "合肖"
                );
                $msql->query("select sid from `$tb_sclass` where gid='$gid' and name in ('半波','五行','特肖','合肖')");
                $msql->next_record();
                $play2 = getpsm60($bid, $ab, $abcd, $msql->f('sid'));
                $msql->next_record();
                $play3 = getpsm60($bid, $ab, $abcd, $msql->f('sid'));
                $msql->next_record();
                $play4 = getpsm60($bid, $ab, $abcd, $msql->f('sid'));
				$msql->next_record();
				$sid = $msql->f('sid');
                $plays = array_merge($play2, $play3, $play4);
                $cps   = count($plays);
                for ($i = 0; $i < $cps; $i++) {
                    if ($cid != $plays[$i]['cid']) {
                        $one = transc('one', $plays[$i]['cid']);
                        if ($one == 1) {
                            $sql = "select sum(je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100) ";
                            $sql .= " from `$tb_lib` where  $yqa and cid='" . $plays[$i]['cid'] . "' $yq ";
                            $msql->query($sql);
                            $msql->next_record();
                            $sumje     = pr1($msql->f(0));
                            $sumpoints = pr1($msql->f(1));
                            $sql       = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `$tb_lib`";
                            $sql .= " where  $yq2 and userid='$userid'and cid='" . $plays[$i]['cid'] . "' $yq2b";
                            $msql->query($sql);
                            $msql->next_record();
                            $sumflyje     = pr1($msql->f(0));
                            $sumflypoints = pr1($msql->f(1));
                        } else {
                            $sumje     = 0;
                            $sumpoints = 0;
                        }
                    }
                    $sql = "select sum(je),sum(je*zc0/100),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100),count(id) ";
                    $sql .= " from `$tb_lib` where  $yqa and pid='" . $plays[$i]['pid'] . "' $yq ";
                    $msql->query($sql);
                    $msql->next_record();
                    $plays[$i]['zje'] = pr1($msql->f(0));
                    $plays[$i]['zc']  = pr1($msql->f(1));
                    $plays[$i]['zs']  = pr1($msql->f(4));
                    if ($one == 1) {
                        $plays[$i]['ks'] = pr1($sumje - $sumpoints - $msql->f(2));
                    } else {
                        $plays[$i]['ks'] = pr1($msql->f(1) - $msql->f(2) - -$msql->f(3));
                    }
                    $sql = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `$tb_lib`";
                    $sql .= " where $yq2 and userid='$userid' and pid='" . $plays[$i]['pid'] . "' $yq2b";
                    $msql->query($sql);
                    $msql->next_record();
                    $plays[$i]['fly'] = pr1($msql->f('0'));
                    $plays[$i]['zc'] -= $plays[$i]['fly'];
                    if ($one == 1) {
                        $plays[$i]['ks'] += pr1($sumflypoints - $sumflyje + $msql->f(2));
                    } else {
                        $plays[$i]['ks'] += pr1($msql->f(2) + $msql->f(3) - $plays[$i]['fly']);
                    }
                    $cid   = $plays[$i]['cid'];
                }
                for ($i = 0; $i < $cps; $i++) {
                    $arr = $ma[$play[$i]['name']];
                    $ca  = count($arr);
                    if ($play[$i]['sname'] == "半波") {
                        $nums = 48;
                    } else {
                        $nums = 49;
                    }
                    for ($j = 0; $j < $nums; $j++) {
                        if (in_array($play[$j]['name'], $arr)) {
                            $play[$j]['zje'] += pr1($play[$i]['zje'] / $ca);
                            $play[$j]['zc'] += pr1($play[$i]['zc'] / $ca);
                            $play[$j]['fly'] += pr1($play[$i]['fly'] / $ca);
                            $play[$j]['ks'] += $play[$i]['ks'];
                        } else {
                            $play[$j]['ks'] += $play[$i]['ks'];
                        }
                    }
                }
				//半波五行特肖结束
				
				$sql = " select je,content,peilv1,points,peilv11,points1,zc0 from `$tb_lib`  where $yqa  and sid='$sid' $yq  ";
				$msql->query($sql);
				$arr = $msql->arr();
				$ca = count($arr);
				for($i=0;$i<$ca;$i++){
				    $con   = explode('-', $arr[$i]['content']);
                    $ccon  = count($con);
					$c2m   = array();
                    for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_push($config[$con[$j]]);
                    }
					$cc    = count($c2m);
					$nums = 49;
					if ($ccon == 6 & $config['cs']['x49']==0) {
                        $nums = 48;
                    }
					
                    if ($arr['peilv11'] == 0) {
                        $tmppeilv  = $arr['peilv1'];
                        $tmppoints = $arr['points'];
                    } else {
                        $tmppeilv  = $arr['peilv11'];
                        $tmppoints = $arr['points1'];
                    }
					$tmpzje = $arr['je'];
					$tmpzc = $arr['je']*$arr['zc0']/100;
					$tmppoints = $arr['je']*$tmppoints;
					$ks1 = pr1($tmpzc-$tmppoints-$tmppeilv*$tmpzc);
					$ks2 = pr1($tmpzc - $tmppoints);
                    for ($j = 0; $j < $nums; $j++) {
                        if (in_array($play[$j]['name'], $c2m)) {
                            $play[$j]['zje'] += pr1($tmpzje/ $cc);
                            $play[$j]['zc'] += pr1($tmpzc / $cc);
                            $play[$j]['ks'] += $ks1;
                        } else {
                            $play[$j]['ks'] += $ks2;
                        }
                    }
				} 
				$sql = "select peilv1,points,content,je from `$tb_lib` where $yq2  and sid='$sid' and userid='$userid'  $yq2b";
$msql->query($sql);
				$arr = $msql->arr();
				$ca = count($arr);
				for($i=0;$i<$ca;$i++){
				    $con   = explode('-', $arr[$i]['content']);
                    $ccon  = count($con);
					$c2m   = array();
                    for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_push($config[$con[$j]]);
                    }
					$cc    = count($c2m);
					$nums = 49;
					if ($ccon == 6 & $config['cs']['x49']==0) {
                        $nums = 48;
                    }
					
                    $flyje = $arr['je'];
                    $ks1   = pr1($arr['je'] * ($arr['peilv1'] + $arr['points'] / 100) - $arr['je']);
                    $ks2   = pr1($arr['je'] - $arr['je'] * $arr['points'] / 100);
					
                    for ($j = 0; $j < $nums; $j++) {
                        if (in_array($play[$j]['name'], $c2m)) {
                            $play[$j]['fly'] += pr1($play[$i]['fly'] / $cc);
                            $play[$j]['ks'] += $ks1;
                        } else {
                            $play[$j]['ks'] += $ks2;
                        }
                    }
				} 
            }
        }
        if($myzc!=0){
            $xsort = $_POST['xsort'];
			if($xsort!='zc' & $xsort!='zje' & $xsort!='zs'& $xsort!='ks' & $xsort!='name') $xsort=='name';
			if($myzc==0 & $xsort=='ks' ) $xsort='name';
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
            if ($xsort == "zc" | $xsort == "zs" | $xsort == "zje") {
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
       
        echo json_encode($play);
        break;
    case "getlibb":
        $bid      = $_POST['bid'];
        $ab       = $_POST['ab'];
        $abcd     = $_POST['abcd'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
        if ($setks == 1) {
            $msql->query("update `$tb_user` set maxcm='$maxksval' where userid='$userid'");
        }
        $qishu = $_POST['qishu'];
        $year  = substr($qishu, 0, 4);
        $qishu = substr($qishu, 4);
        $yq    = " and year='$year' and qishu='$qishu' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        $huama = $_POST['huama'];
        $layer = 0;
        $play  = getplaysmy($bid, $ab, $abcd);
        $cp    = count($play);
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "') ";
        }
        if ($ab == 'A' | $ab == 'B') {
            $aandb .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $aandb .= " and abcd='$abcd' ";
        }
        $myzc = 0;
        $play = getplaysbmy($bid, $ab, $abcd);
        $cp   = count($play);
        for ($i = 0; $i < $cp; $i++) {
            if ($bid != 23378697 & $bid != 23378699 & $bid != 23378722) {
                if ($tmpcid != $play[$i]['cid']) {
                    $sql = "select sum(je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100) from `$tb_lib` where   ";
                    $sql .= " userid!='$userid' $yq $aandb and cid='" . $play[$i]['cid'] . "'";
                    $msql->query($sql);
                    $msql->next_record();
                    $czje     = pr1($msql->f(0));
                    $czpoints = pr1($msql->f(1));
                    $sql      = "select sum(je),sum(je*points/100) from `$tb_lib` where ";
                    $sql .= " userid='$userid' $yq2 $aandb and cid='" . $play[$i]['cid'] . "'";
                    $msql->query($sql);
                    $msql->next_record();
                    $flyje     = $msql->f(0);
                    $flypoints = $msql->f(1);
                    $myzc += $czje;
                }
            } else {
                $sql = "select sum(je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100) from `$tb_lib` where   ";
                $sql .= " userid!='$userid' $yq $aandb and pid='" . $play[$i]['pid'] . "'";
                $msql->query($sql);
                $msql->next_record();
                $czje     = pr1($msql->f(0));
                $czpoints = pr1($msql->f(1));
                $sql      = "select sum(je),sum(je*points/100) from `$tb_lib` where ";
                $sql .= " userid='$userid' $yq2 $aandb and pid='" . $play[$i]['pid'] . "'";
                $msql->query($sql);
                $msql->next_record();
                $flyje     = $msql->f(0);
                $flypoints = $msql->f(1);
                $myzc += $czje;
            }
            $sql = "select sum(je),sum(je*zc0/100),count(id),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100) from `$tb_lib`";
            $sql .= " where userid!='$userid' $yq $aandb and pid='" . $play[$i]['pid'] . "'";
            $msql->query($sql);
            $msql->next_record();
            $play[$i]['zje'] = pr1($msql->f('0'));
            $play[$i]['zc']  = pr1($msql->f('1'));
            $play[$i]['zs']  = $msql->f('2');
            $play[$i]['ks']  = pr1($czje - $msql->f('3') - $czpoints);
            $sql             = "select sum(je),count(id),sum(je*peilv1) from `$tb_lib`";
            $sql .= " where userid='$userid' $yq2 $aandb and pid='" . $play[$i]['pid'] . "'";
            $msql->query($sql);
            $msql->next_record();
            $play[$i]['ks'] += pr1($msql->f(2) + $flypoints - $flyje);
            $play[$i]['fly'] = pr1($msql->f('0'));
            if ($play[$i]['zc'] - $play[$i]['fly'] > $maxksval) {
                $play[$i]['bu'] = p0($play[$i]['zc'] - $play[$i]['fly'] - $maxksval);
            } else {
                $play[$i]['bu'] = 0;
            }
            $play[$i]['z'] = gz($year . $qishu, $play[$i]['pid']);
            $tmpcid        = $play[$i]['cid'];
        }
        if ($_POST['xsort'] == "ks" | $_POST['xsort'] == "zje" | $_POST['xsort'] == "zc" | $_POST['xsort'] == "zs") {
            $xsort = $_POST['xsort'];
            if ($myzc == 0 & $xsort == 'ks')
                $xsort = 'zje';
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
            if ($xsort == "zc" | $xsort == "zs" | $xsort == "zje") {
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
        echo json_encode($play);
        unset($play);
        break;
    case "getlibc":
        $bid      = $_POST['bid'];
        $ab       = $_POST['ab'];
        $abcd     = $_POST['abcd'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
        if ($setks == 1) {
            $msql->query("update `$tb_user` set maxcm='$maxksval' where userid='$userid'");
        }
        $qishu = $_POST['qishu'];
        $year  = substr($qishu, 0, 4);
        $qishu = substr($qishu, 4);
        $yq    = " and year='$year' and qishu='$qishu' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        $huama = $_POST['huama'];
        $layer = 0;
        $play  = getplaysmy($bid, $ab, $abcd);
        $cp    = count($play);
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        if ($ab == 'A' | $ab == 'B') {
            $aandb .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $aandb .= " and abcd='$abcd' ";
        }
        $myzc = 0;
        $play = getplaysbmy($bid, $ab, $abcd);
        $cp   = count($play);
        for ($i = 0; $i < $cp; $i++) {
            $sql = "select sum(je),sum(je*zc0/100),count(id),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100) from `$tb_lib`";
            $sql .= " where userid!='$userid' $yq $aandb and pid='" . $play[$i]['pid'] . "'";
            $msql->query($sql);
            $msql->next_record();
            $play[$i]['zje'] = pr1($msql->f('0'));
            $play[$i]['zc']  = pr1($msql->f('1'));
            $play[$i]['zs']  = $msql->f('2');
            $sql             = "select sum(je),count(id),sum(je*peilv1) from `$tb_lib`";
            $sql .= " where userid='$userid' $yq2 $aandb and pid='" . $play[$i]['pid'] . "'";
            $msql->query($sql);
            $msql->next_record();
            $play[$i]['fly'] = pr1($msql->f('0'));
            $play[$i]['bu']  = 0;
            $tmpcid          = $play[$i]['cid'];
        }
        echo json_encode($play);
        unset($play);
        break;
	case "duoxx":
        $bid     = $_POST['bid'];
        $sid     = $_POST['sid'];
        $cid     = $_POST['cid'];
		$pid     = $_POST['pid'];
        $ab      = $_POST['ab'];
        $abcd    = $_POST['abcd'];
        $qishu   = $_POST['qishu'];
        $puserid = $_POST['userid'];
        $zhenghe = $_POST['zhenghe'];
        $setks   = $_POST['setks'];
        $layer   = 0;
        $p       = $_POST['p'];
        $stype   = $_POST['stype'];
        $yqa     = "  gid='$gid'  and qishu='$qishu' and pid='$pid' ";
        $yq2     = $yqa;
		$yq='';
		$yq2b='';
        $yq .= " and xtype!=2  ";
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
		$sql .= "select * from `$tb_lib` where $yqa  $yq order by replace(content,'-','')";
		$msql->query($sql);
		$rs = $msql->arr();
		$cr = count($rs);
		for($i=0;$i<$cr;$i++){
		
		}
		
		$msql->query("select name,pl from `$tb_play` where gid='$gid' and pid='$pid'");
		//echo "select name,pl from `$tb_play` where gid='$gid' and pid='$pid'";
		$msql->next_record();
		$duo[0] = getduoarr($msql->f('name'));
		$pl = json_decode($msql->f('pl'),true);
	    $i=0;
		//print_r($duo);
		$cd = count($duo[0]);
		for($i=0;$i<$cd;$i++){
		   $duo[1][$i] = (float) pr3($pl[0][$i]);
		   $duo[2][$i] = (float) pr3($pl[1][$i]);
		}
        $arr = array('rs'=>$rs,'pl'=>$duo);
		echo json_encode($arr);
		unset($arr);
		unset($duo);
		unset($rs);
    break;
    case "getlibs":
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
        if ($setks == 1) {
            $msql->query("update `$tb_user` set maxcm='$maxksval' where userid='$userid'");
        }
        $qishu = $_POST['qishu'];
        $year  = substr($qishu, 0, 4);
        $qishu = substr($qishu, 4);
        $yq    = " and year='$year' and qishu='$qishu' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        $layer = 0;
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        $pid = $_POST['pid'];
        $sql .= "select * from `$tb_lib` where pid='$pid' $yq  and userid!='$userid' order by replace(content,'-','')";
        $msql->query($sql);
        $ps        = array();
        $i         = 0;
        $zcstr     = 'zc0';
        $pointsstr = "points1";
        $peilv1str = "peilv11";
        $peilv2str = "peilv21";
        while ($msql->next_record()) {
            if ($name == '') {
                $bname = transbclass('name', $msql->f('bid'));
                $cname = transclass('name', $msql->f('cid'));
                $pname = transplay('name', $msql->f('pid'));
                if ($bname == "正特") {
                    $bname = $mtype[transclass('mtype', $msql->f('cid'))];
                }
                if ($bname == $cname) {
                    $name = $bname . '-' . $pname;
                } else {
                    $name = $bname . '-' . $cname . '-' . $pname;
                }
            }
            if ($content != $msql->f('content')) {
                $ps[$i]['qishu']  = $msql->f('year') . $msql->f('qishu');
                $ps[$i]['con']    = $msql->f('content');
                $ps[$i]['peilv1'] = $msql->f('peilv1');
                $ps[$i]['peilv2'] = $msql->f('peilv2');
                $ps[$i]['zje']    = $msql->f('je');
                $ps[$i]['zc']     = pr1($msql->f('je') * $msql->f($zcstr) / 100);
                $ps[$i]['zs']     = 1;
                $ps[$i]['bu']     = 0;
                $ps[$i]['z']      = $msql->f('z');
                if ($msql->f($pointsstr) == 0) {
                    $ps[$i]['ks1'] = $ps[$i]['zc'] - $ps[$i]['zc'] * $msql->f('peilv1') - $ps[$i]['zc'] * $msql->f('points') / 100;
                    $ps[$i]['ks2'] = $ps[$i]['zc'] - $ps[$i]['zc'] * $msql->f('peilv2') - $ps[$i]['zc'] * $msql->f('points') / 100;
                } else {
                    $ps[$i]['ks1'] = $ps[$i]['zc'] - $ps[$i]['zc'] * $msql->f($peilv1str) - ($ps[$i]['zc'] * $msql->f($pointsstr) / 100);
                    $ps[$i]['ks2'] = $ps[$i]['zc'] - $ps[$i]['zc'] * $msql->f($peilv2str) - ($ps[$i]['zc'] * $msql->f($pointsstr) / 100);
                }
                $fsql->query("select sum(je),sum(je*peilv1),sum(je*peilv2),sum(je*points/100) from `$tb_lib` where userid='$userid'  and pid='$pid' and content='" . $msql->f('content') . "' $yq2");
                $fsql->next_record();
                $ps[$i]['fly'] = pr1($fsql->f(0));
                $ps[$i]['ks1'] += pr1($fsql->f(1) + $fsql->f(3) - $fsql->f(0));
                $ps[$i]['ks2'] += pr1($fsql->f(2) + $fsql->f(3) - $fsql->f(0));
                $ps[$i]['name'] = $name;
                if ($i > 0) {
                    if ($ps[$i - 1]['zc'] - $ps[$i - 1]['fly'] > $maxksval) {
                        $ps[$i - 1]['bu'] = $ps[$i - 1]['zc'] - $ps[$i - 1]['fly'] - $maxksval;
                    } else {
                        $ps[$i - 1]['bu'] = 0;
                    }
                }
                $i++;
            } else {
                $ps[$i - 1]['zje'] += $msql->f('je');
                $tmpzc = pr1($msql->f('je') * $msql->f($zcstr) / 100);
                $ps[$i - 1]['zc'] += $tmpzc;
                $ps[$i - 1]['zs'] += 1;
                if ($msql->f($pointsstr) == 0) {
                    $ps[$i - 1]['ks1'] += $tmpzc - $tmpzc * $msql->f('peilv1') - $tmpzc * $msql->f('points') / 100;
                    $ps[$i - 1]['ks2'] = $tmpzc - $tmpzc * $msql->f('peilv2') - $tmpzc * $msql->f('points') / 100;
                } else {
                    $ps[$i - 1]['ks1'] += $tmpzc - $tmpzc * $msql->f($peilv1str) - $tmpzc * $msql->f($pointsstr) / 100;
                    $ps[$i - 1]['ks2'] = $tmpzc - $tmpzc * $msql->f($peilv2str) - $tmpzc * $msql->f($pointsstr) / 100;
                }
                $ps[$i - 1]['peilv1'] = ($msql->f('peilv1') + $ps[$i - 1]['peilv1']) / 2;
                $ps[$i - 1]['peilv2'] = ($msql->f('peilv2') + $ps[$i - 1]['peilv2']) / 2;
            }
            $content = $msql->f('content');
        }
        if ($i > 0) {
            if ($ps[$i - 1]['zc'] - $ps[$i - 1]['fly'] > $maxksval) {
                $ps[$i - 1]['bu'] = $ps[$i - 1]['zc'] - $ps[$i - 1]['fly'] - $maxksval;
            } else {
                $ps[$i - 1]['bu'] = 0;
            }
        }
        foreach ($ps as $tmp) {
            $mm[] = $tmp['zc'];
        }
        if (is_array($mm)) {
            array_multisort($mm, SORT_DESC, SORT_NUMERIC, $ps);
            unset($mm);
        }
        $rcount = count($ps);
        $page   = $_POST['page'];
        $psize  = $_POST['psize'];
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : ($rcount - ($rcount % $psize)) / $psize + 1;
        if (!is_numeric($page) | $page < 1 | $page > $pcount)
            $page = 1;
        if (!is_numeric($psize))
            $psize = 50;
        $new = array_slice($ps, ($page - 1) * $psize, $psize);
        $out = array(
            'tz' => $new,
            'page' => $pcount
        );
        unset($ps);
        unset($new);
        echo json_encode($out);
        unset($out);
        break;
    case "getclass":
        $fid = $_POST['fid'];
        $c   = array();
        if ($fid == 23378685 | $fid == '') {
            $c[0]['name'] = "特A+B";
            $c[0]['cid']  = "0";
            $c[1]['name'] = "特A";
            $c[1]['cid']  = "A";
            $c[2]['name'] = "特B";
            $c[2]['cid']  = "B";
        } else {
            $msql->query("select * from `$tb_class` where fid='$fid' and xshow=1");
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
    case "setpeilvall":
        $pl   = $_POST['pl'];
        $pl   = json_decode(str_replace('\\', "", $pl));
        //print_r($pl);
        $ab   = $_POST['ab'];
        $abcd = low($_POST['abcd']);
        foreach ($pl as $key => $v) {
            if (substr($key, 0, 2) == 'p1') {
                $tmp = str_replace('p1', '', $key);
                $sql = "select bclassid,classid from `$tb_play` where playid='$tmp' ";
                $sql .= " union select ftype,classid from `$tb_class` where classid=(select classid from `$tb_play` ";
                $sql .= " where  playid='$tmp')";
                $msql->query($sql);
                $msql->next_record();
                if ($msql->f(0) == '23378685' & $ab == 'B') {
                    $v -= $abcha;
                }
                $msql->next_record();
                if ($msql->f(0) <= 4 & ($abcd == 'b' | $abcd == 'c' | $abcd == 'd')) {
                    $v += $patt[$msql->f(0)][$abcd];
                }
                if ($tmp == 23378730) { //单
                    include("../func/pose.php");
                    $shuma = rTexiao("鼠", $thisbml);
                    $fsql->query("select peilv1 from `$tb_play` where playid='$tmp'");
                    $fsql->next_record();
                    $yp = $fsql->f('peilv1');
                    if ($yp - $v > 0) {
                        if ($shuma[0] % 2 == 0) {
                            $fsql->query("update `$tb_play` set peilv1=peilv1-" . ($yp - $v * 2) . " where instr('牛兔蛇羊鸡猪',name) and bclassid='23378689'");
                        } else {
                            $fsql->query("update `$tb_play` set peilv1=peilv1-" . ($yp - $v * 2) . " where instr('鼠虎龙马猴狗',name) and bclassid='23378689'");
                        }
                        $fsql->query("update `$tb_play` set peilv1=peilv1-" . ($yp - $v * 2) . " where  bclassid='23378707'");
                        $fsql->query("update `$tb_play` set peilv1=peilv1-" . ($yp - $v * 2) . " where  bclassid='23378712'");
                    }
                }
                if ($tmp == 23378731) { //双
                    include("../func/pose.php");
                    $shuma = rTexiao("鼠", $thisbml);
                    $fsql->query("select peilv1 from `$tb_play` where playid='$tmp'");
                    $fsql->next_record();
                    $yp = $fsql->f('peilv1');
                    if ($yp - $v > 0) {
                        if ($shuma[0] % 2 == 0) {
                            $fsql->query("update `$tb_play` set peilv1=peilv1-" . ($yp - $v * 2) . " where instr('鼠虎龙马猴狗',name) and bclassid='23378689'");
                        } else {
                            $fsql->query("update `$tb_play` set peilv1=peilv1-" . ($yp - $v * 2) . " where instr('牛兔蛇羊鸡猪',name) and bclassid='23378689'");
                        }
                        $fsql->query("update `$tb_play` set peilv1=peilv1-" . ($yp - $v * 2) . " where  bclassid='23378707'");
                        $fsql->query("update `$tb_play` set peilv1=peilv1-" . ($yp - $v * 2) . " where  bclassid='23378712'");
                    }
                }
                $sql = "update `$tb_play` set peilv1='$v' where playid='$tmp'";
                //echo $sql,"<BR>";
                $msql->query($sql);
                $time = time();
                $fsql->query("delete from `$tb_c` where pid='$tmp' and userid='$userid'");
                $fsql->query("insert into `$tb_c` set pid='$tmp',time=NOW(),userid='$userid'");
            } else {
                $tmp = str_replace('p2', '', $key);
                $sql = "update `$tb_play` set peilv2='$v' where playid='$tmp'";
                //echo $sql;
                $msql->query($sql);
            }
        }
        echo 1;
        break;
    case "setatt":
        $pid    = $_POST['pid'];
        $val    = $_POST['val'];
        $action = $_POST['action'];
        if ($action == 0) {
            $sql = "update `$tb_play` set peilv1=if(peilv1-$val>1,peilv1-$val,1) where instr('$pid',playid)";
        } else {
            $sql = "update `$tb_play` set peilv1=if(peilv1+$val>49,49,peilv1+$val) where instr('$pid',playid)";
        }
        if ($msql->query($sql)) {
            $time = time();
            $pid  = explode('|', $pid);
            $cp   = count($pid);
            for ($i = 0; $i < $cp; $i++) {
                if (strlen($pid[$i]) == 8) {
                    $fsql->query("delete from `$tb_c` where pid='" . $pid[$i] . "' and userid='$userid'");
                    $fsql->query("insert into `$tb_c` set pid='" . $pid[$i] . "',time=NOW(),userid='$userid'");
                }
            }
            echo 1;
        }
        break;
    case "setatttwo":
        $action = $_POST['action'];
        $pid    = $_POST['pid'];
        $sql    = "select ftype,classid from `$tb_class` where classid=(select classid from `$tb_play` where  playid='$pid')";
        $msql->query($sql);
        $msql->next_record();
        if ($msql->f('ftype') <= 4) {
            $att = transatt($msql->f('ftype'), 'peilvatt');
        } else {
            $att = transatt($msql->f('classid'), 'peilvatt');
        }
        if ($action == 'down') {
            $msql->query("update `$tb_play` set peilv1=if(peilv1-$att>1,peilv1-$att,1)  where playid='$pid'");
        } else {
            $msql->query("update `$tb_play` set peilv1=if(peilv1+$att>49,49,peilv1+$att)  where playid='$pid'");
        }
        $time = time();
        $fsql->query("delete from `$tb_c` where pid='$pid' and userid='$userid'");
        $fsql->query("insert into `$tb_c` set pid='$pid',time=NOW(),userid='$userid'");
        $val = $att * 2;
        if ($pid == 23378730) { //单
            include("../func/pose.php");
            $shuma = rTexiao("鼠", $thisbml);
            if ($shuma[0] % 2 == 0) {
                if ($action == 'down') {
                    $fsql->query("update `$tb_play` set peilv1=if(peilv1-$val>1,peilv1-$val,1) where instr('牛兔蛇羊鸡猪',name) and bclassid='23378689'");
                } else {
                    //$fsql->query("update `$tb_play` set peilv1=if(peilv1+$val>12,12,peilv1+$val) where instr('牛兔蛇羊鸡猪',name) and bclassid='23378689'");
                }
            } else {
                if ($action == 'down') {
                    $fsql->query("update `$tb_play` set peilv1=if(peilv1-$val>1,peilv1-$val,1) where instr('鼠虎龙马猴狗',name) and bclassid='23378689'");
                } else {
                    //$fsql->query("update `$tb_play` set peilv1=if(peilv1+$val>12,12,peilv1+$val) where instr('鼠虎龙马猴狗',name) and bclassid='23378689'");
                }
            }
            if ($action == 'down') {
                $fsql->query("update `$tb_play` set peilv1=if(peilv1-$val>1,peilv1-$val,1) where  bclassid='23378707'");
                $fsql->query("update `$tb_play` set peilv1=if(peilv1-$val>1,peilv1-$val,1) where  bclassid='23378712'");
            } else {
                //$fsql->query("update `$tb_play` set peilv1=if(peilv1+$val>12,12,peilv1+$val) where  bclassid='23378707'");
            }
        }
        if ($pid == 23378731) { //双
            include("../func/pose.php");
            $shuma = rTexiao("鼠", $thisbml);
            if ($shuma[0] % 2 == 0) {
                if ($action == 'down') {
                    $fsql->query("update `$tb_play` set peilv1=if(peilv1-$val>1,peilv1-$val,1) where instr('鼠虎龙马猴狗',name) and bclassid='23378689'");
                } else {
                    //$fsql->query("update `$tb_play` set peilv1=if(peilv1+$val>12,12,peilv1+$val) where instr('鼠虎龙马猴狗',name) and bclassid='23378689'");
                }
            } else {
                if ($action == 'down') {
                    $fsql->query("update `$tb_play` set peilv1=if(peilv1-$val>1,peilv1-$val,1) where instr('牛兔蛇羊鸡猪',name) and bclassid='23378689'");
                } else {
                    //$fsql->query("update `$tb_play` set peilv1=if(peilv1+$val>12,12,peilv1+$val) where instr('牛兔蛇羊鸡猪',name) and bclassid='23378689'");
                }
            }
            if ($action == 'down') {
                $fsql->query("update `$tb_play` set peilv1=if(peilv1-$val>1,peilv1-$val,1) where  bclassid='23378707'");
                $fsql->query("update `$tb_play` set peilv1=if(peilv1-$val>1,peilv1-$val,1) where  bclassid='23378712'");
            } else {
                //$fsql->query("update `$tb_play` set peilv1=if(peilv1+$val>12,12,peilv1+$val) where  bclassid='23378707'");
            }
        }
        echo $att;
        break;
    case "getnow":
        $qishu   = $_POST['qishu'];
        $yq1     = " and concat(year,qishu)='$qishu' ";
        $puserid = $_POST['userid'];
        if ($puserid != '') {
            $yq2 = $yq1 . " and ( uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "') ";
        } else {
            $yq2 = $yq1;
        }
        $yq2 .= " and  xtype!=2 ";
        $msql->query("select * from `$tb_bclass` where ifok=1");
        $now = array();
        $i   = 0;
        while ($msql->next_record()) {
            $fsql->query("select sum(je),sum(je*zc0/100),count(id) from `$tb_lib` where bid='" . $msql->f('classid') . "' $yq2 and userid!='$userid' ");
            $fsql->next_record(0);
            $now[$i]['zje']   = pr1($fsql->f(0));
            $now[$i]['zjezc'] = pr1($fsql->f(1));
            $now[$i]['zs']    = pr1($fsql->f(2));
            $now[$i]['bid']   = $msql->f('classid');
            $fsql->query("select sum(je) from `$tb_lib` where bid='" . $msql->f('classid') . "' $yq1  and userid='$userid'");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            $i++;
        }
        echo json_encode($now);
        unset($now);
        break;
    case "getztnow":
        $qishu   = $_POST['qishu'];
        $yq1     = " and concat(year,qishu)='$qishu' ";
        $sid     = $_POST['sid'];
        $puserid = $_POST['userid'];
        if ($puserid != '') {
            $yq2 = $yq1 . " and ( uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "') ";
        } else {
            $yq2 = $yq1;
        }
        $yq2 .= " and  xtype!=2 ";
        $msql->query("select * from `$tb_class` where fid='23378688' and xshow=1");
        $now = array();
        $i   = 0;
        while ($msql->next_record()) {
            $fsql->query("select sum(je),sum(je*zc0/100),count(id) from `$tb_lib` where 1=1 $yq2 and userid!='$userid' and cid in (select classid from  `$tb_class` where mtype='" . $msql->f('mtype') . "' and fid='23378688')");
            $fsql->next_record(0);
            $now[$i]['zje']   = pr1($fsql->f(0));
            $now[$i]['zjezc'] = pr1($fsql->f(1));
            $now[$i]['zs']    = pr1($fsql->f(2));
            $now[$i]['bid']   = $msql->f('classid');
            $fsql->query("select sum(je) from `$tb_lib` where bid='23378688' $yq1  and userid='$userid' and cid in (select classid from  `$tb_class` where mtype='" . $msql->f('mtype') . "' and fid='23378688')");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            $i++;
        }
        echo json_encode($now);
        unset($now);
        break;
    case "bucang":
        if ($_SESSION['type'] != 1) {
            $msql->query("select ifok from `$tb_admins_page` where adminid='$adminid' and xpage='fly'");
            $msql->next_record();
            if ($msql->f('ifok') == 0)
                break;
        }
        if ($_POST['bstr'] != '') {
            $play = str_replace("\\", "", $_POST['bstr']);
            $play = get_object_vars(json_decode($play));
            $i    = 0;
            foreach ($play as $key => $v) {
                $playc[$i]['pid'] = $key;
                $vs               = explode('_', $v);
                $playc[$i]['je']  = floor($vs[0]);
                if ($vs[1] == null | $vs[1] == 'null' | $vs[1] == '' | $vs[1] == 'undefined') {
                    $playc[$i]['con'] = '';
                } else {
                    $playc[$i]['con'] = $vs[1];
                }
                $i++;
            }
            unset($play);
            $play = $playc;
            unset($playc);
        } else {
            $play              = array();
            $peilv1            = $_POST['peilv1'];
            $peilv2            = $_POST['peilv2'];
            $points            = $_POST['points'];
            $pid               = $_POST['pid'];
            $je                = $_POST['je'];
            $con               = $_POST['con'];
            $play[0]['pid']    = $pid;
            $play[0]['je']     = floor($je);
            $play[0]['con']    = $con;
            $play[0]['peilv1'] = $peilv1;
            $play[0]['peilv2'] = $peilv2;
            $play[0]['points'] = $points;
            if ($play[0]['con'] == null)
                $play[0]['con'] = '';
        }
        $ab   = $_POST['ab'];
        $abcd = $_POST['abcd'];
        if ($ab == 0)
            $ab = 'A';
        if ($abcd == 0)
            $abcd = 'A';
        $bid   = $_POST['bid'];
        $cp    = count($play);
        $layer = 0;
        if ($cp > 1)
            exit;
        for ($i = 0; $i < $cp; $i++) {
            $tid = setupid($tb_lib, 'tid');
            $msql->query("select * from `$tb_play` where playid='" . $play[$i]['pid'] . "'");
            $msql->next_record();
            $bid = $msql->f('bclassid');
            $cid = $msql->f('classid');
            if ($peilv1 == '' | !is_numeric($peilv1))
                $peilv1 = $msql->f('peilv1');
            if ($peilv2 == '' | !is_numeric($peilv2))
                $peilv2 = $msql->f('peilv2');
            $msql->query("select ftype,classid,abcd,ab from `$tb_class` where classid='$cid'");
            $msql->next_record();
            $ftype = $msql->f('ftype');
            if ($msql->f('ftype') <= 4) {
                $ftype   = $msql->f('ftype');
                $abcdcha = $patt[$ftype][strtolower($abcd)];
            } else {
                $ftype   = $msql->f('classid');
                $abcdcha = 0;
            }
            if ($msql->f('abcd') == 0)
                $tmpabcd = 0;
            else
                $tmpabcd = $abcd;
            if ($msql->f('ab') == 0)
                $tmpab = 0;
            else
                $tmpab = $ab;
            if ($points == '' | !is_numeric($points)) {
                $points = getpoints($ftype, $tmpabcd, $tmpab, $userid);
            }
            if ($play[$i]['pid'] != 23379231 & $play[$i]['pid'] != 23379233)
                $peilv2 = 0;
            $sql = "insert into `$tb_lib` set year='$thisyear',qishu='$thisqishu',tid='$tid',userid='$userid',bid='$bid'";
            $sql .= ",cid='$cid',pid='" . $play[$i]['pid'] . "',abcd='$abcd',ab='$ab',content='" . $play[$i]['con'] . "'";
            $sql .= ",time=NOW(),je='" . $play[$i]['je'] . "',xtype='2',z='0',peilv1='" . $peilv1 . "'";
            $sql .= ",peilv2='" . $peilv2 . "',points='$points'";
            if ($msql->query($sql)) {
                $play[$i]['cg'] = 1;
            } else {
                $play[$i]['cg'] = 0;
            }
        }
        unset($play);
        echo 1;
        break;
}
?>