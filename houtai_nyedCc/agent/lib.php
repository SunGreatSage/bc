<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/agentfunc.php');
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
        $time     = strtotime(date("Y-m-d ") . '08:00:01');
        $msql->query("select qishu from `$tb_kj` where gid='$gid' and baostatus=1  and kjtime>$time order by qishu desc limit 120");
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        $tpl->assign('qishu', $qishu);
        $tpl->assign("s", gets());
        $msql->query("select layer,ifexe,pself,pan from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $tpl->assign('pan', json_decode($msql->f('pan'),true));
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign('ifexe', $msql->f('ifexe'));
        $tpl->assign('pself', $msql->f('pself'));
        $tpl->assign("layername", $config['layer']);
        $tpl->assign("topuser", topuser($userid));
        $tpl->assign("gid", $gid);
        $tpl->assign("class", $config['class']);
        $tpl->assign("maxlayer", $config['maxlayer']);
        $tpl->assign("ma", getma());
        $msql->query("select flytype from `$tb_gamecs` where gid='$gid' and userid='$userid'");
        $msql->next_record();
        $flytype = $msql->f('flytype');
        $tpl->assign("flytype", $flytype);
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
        $layer   = transuser($userid, 'layer');
        $p       = $_POST['p'];
        $stype   = $_POST['stype'];
        $zhenghe = $_POST['zhenghe'];
        $yqa     = " gid='$gid'  and qishu='$qishu'";
        $yq2     = $yqa;
        $yq      = '';
        $yq2b    = '';
        $yq .= " and xtype!=2 ";
        $myzc = 0;
        if ($layer < $config['maxlayer'] - 1) {
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
        $yqa .= " and $uidstr='$userid' ";
        $ifok = 0;
        if ($config['panstatus'] == 1) {
            $ifok = 1;
        }
        if ($layer > 1) {
            $fid1 = transuser($userid, "fid1");
            $fsql->query("select ifexe,pself from `$tb_user` where userid='$fid1'");
            $fsql->next_record();
            $ifexe = $fsql->f('ifexe');
            $pself = $fsql->f('pself');
        } else {
            $fsql->query("select ifexe,pself from `$tb_user` where userid='$userid'");
            $fsql->next_record();
            $ifexe = $fsql->f('ifexe');
            $pself = $fsql->f('pself');
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
        $play = getpsm60($bid, $ab, $abcd, $sid);
        $cp   = count($play);
        for ($i = 0; $i < $cp; $i++) {
            if ($cid != $play[$i]['cid']) {
                $one = transc('one', $play[$i]['cid']);
                if ($one == 1) {
                    $sql = "select sum(je*$zcstr/100),sum((if($peilv1str=0,points,$pointsstr)/100)*je*$zcstr/100) ";
                    $sql .= " from `$tb_lib` where $yqa and  cid='" . $play[$i]['cid'] . "' $yq ";
                    $msql->query($sql);
                    $msql->next_record();
                    $sumje     = pr1($msql->f(0));
                    $sumpoints = pr1($msql->f(1));
                    $sql       = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `$tb_lib`";
                    $sql .= " where $yq2 and userid='$userid'  and cid='" . $play[$i]['cid'] . "' $yq2b";
                    $msql->query($sql);
                    $msql->next_record();
                    $sumflyje     = pr1($msql->f(0));
                    $sumflypoints = pr1($msql->f(1));
                    if ($sumje > 0 & $myzc == 0)
                        $myzc = 1;
                } else {
                    $sumje     = 0;
                    $sumpoints = 0;
                }
            }
            $sql = "select sum(je),sum(je*$zcstr/100),sum(if($peilv1str=0,peilv1,$peilv1str)*je*$zcstr/100),sum((if($peilv1str=0,points,$pointsstr)/100)*je*$zcstr/100),count(id) ";
            $sql .= " from `$tb_lib` where $yqa and  pid='" . $play[$i]['pid'] . "' $yq  ";
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
            $sql .= " where $yq2 and userid='$userid' and pid='" . $play[$i]['pid'] . "' $yq2b";
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
                if ($layer == 1 | $pself == 0) {
                    $peilvcha = getuserpeilvcha($userid, $play[$i]['ftype']);
                } else {
                    $peilvcha = getuserpeilvcha2($userid, $play[$i]['ftype']);
                }
            }
            $play[$i]['mepeilv1'] = 0;
            $play[$i]['mepeilv2'] = 0;
            if ($layer == 1 & $ifexe == 1) {
                $psql->query("select peilv1,peilv2,mp1,mp2 from `$tb_play_user` where  userid='$userid' and gid='$gid' and pid='" . $play[$i]['pid'] . "'");
                $psql->next_record();
                $play[$i]['mepeilv1'] = (float) $psql->f('peilv1');
                $play[$i]['mepeilv2'] = (float) $psql->f('peilv2');
                $play[$i]['mp1']      = (float) $psql->f('mp1');
                $play[$i]['mp2']      = (float) $psql->f('mp2');
            } else {
                if ($ifexe == 1) {
                    if ($pself == 1) {
                        $psql->query("select peilv1,peilv2 from `$tb_play_user` where  userid='$fid1' and gid='$gid' and pid='" . $play[$i]['pid'] . "'");
                        $psql->next_record();
                        $play[$i]['peilv1'] = $psql->f('peilv1');
                        $play[$i]['peilv2'] = $psql->f('peilv2');
                        if ($abcd != 'a') {
                            $play[$i]['peilv1'] -= $play[$i][$abcd];
                        }
                        if ($ab == 'b') {
                            $play[$i]['peilv1'] += $config['pan'][$play[$i]['ftype']]['ab'];
                        }
                    } else {
                        $psql->query("select peilv1,peilv2 from `$tb_play_user` where  userid='$fid1' and gid='$gid' and pid='" . $play[$i]['pid'] . "'");
                        $psql->next_record();
                        $play[$i]['peilv1'] -= $psql->f('peilv1');
                        $play[$i]['peilv2'] -= $psql->f('peilv2');
                    }
                }
            }
            $play[$i]['peilv1'] -= $peilvcha;
            $play[$i]['peilv2'] -= $peilvcha;
            $play[$i]['peilv1'] = (float) $play[$i]['peilv1'];
            $play[$i]['peilv2'] = (float) $play[$i]['peilv2'];
            $play[$i]['wje']    = 0;
            $play[$i]['wks']    = 0;
            $play[$i]['yje']    = $arr['je'];
            $play[$i]['yks']    = $arr['ks'];
            if ($play[$i]['zc'] >= $arr['je']) {
                $play[$i]['wje'] = 1;
            }
            if ($play[$i]['ks'] <= (0 - $arr['ks'])) {
                $play[$i]['wks'] = 1;
            }
            if ($ifok == 0) {
                $play[$i]['ifok'] = 0;
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
                $sid   = $msql->f('sid');
                $plays = array_merge($play2, $play3, $play4);
                $cps   = count($plays);
                for ($i = 0; $i < $cps; $i++) {
                    if ($cid != $play[$i]['cid']) {
                        $one = transc('one', $play[$i]['cid']);
                        if ($one == 1) {
                            $sql = "select sum(je*$zcstr/100),sum((if($peilv1str=0,points,$pointsstr)/100)*je*$zcstr/100) ";
                            $sql .= " from `$tb_lib` where $yqa and  cid='" . $play[$i]['cid'] . "' $yq ";
                            $msql->query($sql);
                            $msql->next_record();
                            $sumje     = pr1($msql->f(0));
                            $sumpoints = pr1($msql->f(1));
                            $sql       = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `$tb_lib`";
                            $sql .= " where $yq2 and userid='$userid'  and cid='" . $play[$i]['cid'] . "' $yq2b";
                            $msql->query($sql);
                            $msql->next_record();
                            $sumflyje     = pr1($msql->f(0));
                            $sumflypoints = pr1($msql->f(1));
                        } else {
                            $sumje     = 0;
                            $sumpoints = 0;
                        }
                    }
                    $sql = "select sum(je),sum(je*$zcstr/100),sum(if($peilv1str=0,peilv1,$peilv1str)*je*$zcstr/100),sum((if($peilv1str=0,points,$pointsstr)/100)*je*$zcstr/100),count(id) ";
                    $sql .= " from `$tb_lib` where $yqa and  pid='" . $play[$i]['pid'] . "' $yq  ";
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
                    $sql .= " where $yq2 and userid='$userid' and pid='" . $play[$i]['pid'] . "' $yq2b";
                    $msql->query($sql);
                    $msql->next_record();
                    $play[$i]['fly'] = pr1($msql->f('0'));
                    $play[$i]['zc'] -= $play[$i]['fly'];
                    if ($one == 1) {
                        $play[$i]['ks'] += pr1($sumflypoints - $sumflyje + $msql->f(2));
                    } else {
                        $play[$i]['ks'] += pr1($msql->f(2) + $msql->f(3) - $play[$i]['fly']);
                    }
                    $cid = $play[$i]['cid'];
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
                $sql = " select je,content,peilv1,points,$peilv1str,$pointsstr,$zcstr from `$tb_lib`  where $yqa  and sid='$sid' and $uidstr='$userid' $yq  ";
                $msql->query($sql);
                $arr = $msql->arr();
                $ca  = count($arr);
                for ($i = 0; $i < $ca; $i++) {
                    $con  = explode('-', $arr[$i]['content']);
                    $ccon = count($con);
                    $c2m  = array();
                    for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_push($config[$con[$j]]);
                    }
                    $cc   = count($c2m);
                    $nums = 49;
                    if ($ccon == 6 & $config['cs']['x49'] == 0) {
                        $nums = 48;
                    }
                    if ($arr[$peilv1str] == 0) {
                        $tmppeilv  = $arr[$peilv1str];
                        $tmppoints = $arr[$pointsstr];
                    } else {
                        $tmppeilv  = $arr[$peilv1str];
                        $tmppoints = $arr[$pointsstr];
                    }
                    $tmpzje    = $arr['je'];
                    $tmpzc     = $arr['je'] * $arr[$zcstr] / 100;
                    $tmppoints = $arr['je'] * $tmppoints;
                    $ks1       = pr1($tmpzc - $tmppoints - $tmppeilv * $tmpzc);
                    $ks2       = pr1($tmpzc - $tmppoints);
                    for ($j = 0; $j < $nums; $j++) {
                        if (in_array($play[$j]['name'], $c2m)) {
                            $play[$j]['zje'] += pr1($tmpzje / $cc);
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
                $ca  = count($arr);
                for ($i = 0; $i < $ca; $i++) {
                    $con  = explode('-', $arr[$i]['content']);
                    $ccon = count($con);
                    $c2m  = array();
                    for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_push($config[$con[$j]]);
                    }
                    $cc   = count($c2m);
                    $nums = 49;
                    if ($ccon == 6 & $config['cs']['x49'] == 0) {
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
        if ($myzc != 0) {
            $xsort = $_POST['xsort'];
            if ($xsort != 'zc' & $xsort != 'zje' & $xsort != 'zs' & $xsort != 'ks' & $xsort != 'name')
                $xsort == 'name';
            if ($myzc == 0 & $xsort == 'ks')
                $xsort = 'name';
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
    case "getlibbb":
        $bid      = $_POST['bid'];
        $cid      = $_POST['cid'];
        $ab       = $_POST['ab'];
        $abcd     = $_POST['abcd'];
        $qishu    = $_POST['qishu'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
        if ($setks == 1) {
            $msql->query("update `$tb_user` set maxks='$maxksval' where userid='$userid'");
        }
        $year  = substr($qishu, 0, 4);
        $qishu = substr($qishu, 4);
        $yq    = " and year='$year' and qishu='$qishu' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        $huama = $_POST['huama'];
        $layer = transuser($userid, 'layer');
        $play  = getplaysa($bid, $ab, $abcd, $cid);
        $cp    = count($play);
        if ($ab == 'A' | $ab == 'B') {
            $aandb .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $aandb .= " and abcd='$abcd' ";
        }
        $myzc = 0;
        if ($layer < 5) {
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
        if ($huama == 1 & $bid != 23378687) {
            for ($i = 0; $i < $cp; $i++) {
                if ($tmpcid != $play[$i]['cid']) {
                    $sql = "select sum(je*$zcstr/100),sum((if($peilv1str=0,points,$pointsstr)/100)*je*$zcstr/100) from `$tb_lib`   ";
                    $sql .= "  where $uidstr='$userid'  $yq $aandb and cid='" . $play[$i]['cid'] . "'";
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
                if (is_numeric($play[$i]['name'])) {
                    $sql = "select sum(je),sum(je*$zcstr/100),count(id),sum(if($peilv1str=0,peilv1,$peilv1str)*je*$zcstr/100) ";
                    $sql .= "from `$tb_lib` where $uidstr='$userid'  $yq $aandb and pid='" . $play[$i]['pid'] . "'";
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
                    $play[$i]['bu']  = 0;
                    $play[$i]['z']   = gz($year . $qishu, $play[$i]['pid']);
                    $tmpcid          = $play[$i]['cid'];
                } else {
                    $sql = "select sum(je),sum(je*$zcstr/100),count(id),sum(if($peilv1str=0,peilv1,$peilv1str)*je*$zcstr/100)";
                    $sql .= "  from `$tb_lib` where $uidstr='$userid'  $yq $aandb and pid='" . $play[$i]['pid'] . "'";
                    $msql->query($sql);
                    $msql->next_record();
                    $tzje = pr1($msql->f('0'));
                    $tzc  = pr1($msql->f('1'));
                    $tzs  = $msql->f('2');
                    $tks  = pr1($czje - $msql->f('3') - $czpoints);
                    $sql  = "select sum(je),count(id),sum(je*peilv1) from `$tb_lib`";
                    $sql .= " where userid='$userid' $yq2 $aandb and pid='" . $play[$i]['pid'] . "'";
                    $msql->query($sql);
                    $msql->next_record();
                    $tks += pr1($msql->f(2) + $flypoints - $flyje);
                    $tffly = pr1($msql->f('0'));
                    $tfbu  = 0;
                    $c2m   = ctoma($play[$i]['name']);
                    $cc    = count($c2m);
                    for ($j = 0; $j < 49; $j++) {
                        if (in_array($play[$j]['name'], $c2m)) {
                            $play[$j]['zje'] += pr1($tzje / $cc);
                            $play[$j]['zc'] += pr1($tzc / $cc);
                            $play[$j]['zs'] += $tzs;
                            $play[$j]['ks'] += $tks;
                            $play[$j]['fly'] += pr1($tffly / $cc);
                            $play[$j]['bu'] += $tfbu;
                        }
                    }
                    unset($play[$i]);
                    $tmpcid = $play[$i]['cid'];
                }
            }
        } else {
            for ($i = 0; $i < $cp; $i++) {
                if ($tmpcid != $play[$i]['cid']) {
                    $sql = "select sum(je*$zcstr/100),sum((if($peilv1str=0,points,$pointsstr)/100)*je*$zcstr/100) ";
                    $sql .= " from `$tb_lib` where $uidstr='$userid'  $yq $aandb and cid='" . $play[$i]['cid'] . "'";
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
                $sql = "select sum(je),sum(je*$zcstr/100),count(id),sum(if($peilv1str=0,peilv1,$peilv1str)*je*$zcstr/100),sum(if($peilv1str=0,points,$pointsstr)*je/100)";
                $sql .= " from `$tb_lib` where $uidstr='$userid'  $yq $aandb and pid='" . $play[$i]['pid'] . "'";
                $msql->query($sql);
                $msql->next_record();
                $play[$i]['zje'] = pr1($msql->f('0'));
                $play[$i]['zc']  = pr1($msql->f('1'));
                $play[$i]['zs']  = $msql->f('2');
                if ($bid == 23378687)
                    $play[$i]['ks'] = pr1($msql->f('1') - $msql->f('3') - $msql->f('4'));
                else
                    $play[$i]['ks'] = pr1($czje - $msql->f('3') - $czpoints);
                $sql = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `$tb_lib`";
                $sql .= " where userid='$userid' $yq2 $aandb and pid='" . $play[$i]['pid'] . "'";
                $msql->query($sql);
                $msql->next_record();
                if ($bid == 23378687)
                    $play[$i]['ks'] += pr1($msql->f(2) + $msql->f(3) - $msql->f(0));
                else
                    $play[$i]['ks'] += pr1($msql->f(2) + $flypoints - $flyje);
                $play[$i]['fly'] = pr1($msql->f('0'));
                $play[$i]['bu']  = 0;
                $play[$i]['z']   = gz($year . $qishu, $play[$i]['pid']);
                $tmpcid          = $play[$i]['cid'];
            }
        }
        if ($bid == 23378685 & $huama == 1) {
            $fsql->query("select * from `$tb_play` where  bclassid='23378722' or  bclassid='23378689' or bclassid='23378726'");
            while ($fsql->next_record()) {
                $sql = "select sum(je),sum(je*$zcstr/100),count(id),sum(if($peilv1str=0,peilv1,$peilv1str)*je*$zcstr/100),sum((if($peilv1str=0,points,$pointsstr)/100)*je*$zcstr/100) from `$tb_lib`";
                $sql .= "  where $uidstr='$userid'  $yq $aandb and pid='" . $fsql->f('playid') . "'";
                $msql->query($sql);
                $msql->next_record();
                $tzje     = pr1($msql->f('0'));
                $tzc      = pr1($msql->f('1'));
                $tzs      = $msql->f('2');
                $czpoints = $msql->f('4');
                $tks      = pr1($tzc - $msql->f('3') - $czpoints);
                $tks2     = pr1($tzc - $czpoints);
                $sql      = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `$tb_lib`";
                $sql .= " where userid='$userid' $yq2 $aandb and pid='" . $fsql->f('playid') . "'";
                $msql->query($sql);
                $msql->next_record();
                $flyje     = $msql->f(0);
                $flypoints = $msql->f(3);
                $tks2 += pr1($flypoints - $flyje);
                $tks += pr1($msql->f(2) + $flypoints - $flyje);
                $tffly = pr1($msql->f('0'));
                $tfbu  = 0;
                $c2m   = getma($fsql->f('name'));
                $cc    = count($c2m);
                for ($j = 0; $j < 49; $j++) {
                    if (in_array($play[$j]['name'], $c2m)) {
                        $play[$j]['zje'] += pr1($tzje / $cc);
                        $play[$j]['zc'] += pr1($tzc / $cc);
                        $play[$j]['zs'] += $tzs;
                        $play[$j]['ks'] += $tks;
                        $play[$j]['fly'] += pr1($tffly / $cc);
                        $play[$j]['bu'] += $tfbu;
                    } else {
                        $play[$j]['ks'] += $tks2;
                    }
                }
            }
            $fsql->query("select * from `$tb_play` where  bclassid='23378707' or  bclassid='23378712'");
            while ($fsql->next_record()) {
                $sql = " select je,content,peilv1,points,$peilv1str,$pointsstr,$zcstr as zc from `$tb_lib` ";
                $sql .= " where $uidstr='$userid' $yq $aandb and pid='" . $fsql->f('playid') . "' and $zcstr!=0 ";
                $msql->query($sql);
                while ($msql->next_record()) {
                    if ($msql->f($peilv1str) == 0) {
                        $tmppeilv  = $msql->f('peilv1');
                        $tmppoints = $msql->f('points');
                    } else {
                        $tmppeilv  = $msql->f($peilv1str);
                        $tmppoints = $msql->f($pointsstr);
                    }
                    $tzje     = $msql->f('je');
                    $tzc      = $msql->f('je') * $msql->f('zc') / 100;
                    $czpoints = $tzc * $tmppoints / 100;
                    $tks      = pr1($tzc - $tzc * $tmppeilv - $czpoints);
                    $tks2     = pr1($tzc - $czpoints);
                    $con      = explode('-', $msql->f('content'));
                    $ccon     = count($con);
                    $c2m      = array();
                    for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_merge_recursive($c2m, getma($con[$j]));
                    }
                    $cc    = count($c2m);
                    $cplay = 49;
                    if ($ccon == 6) {
                        $cplay = 48;
                    }
                    for ($j = 0; $j < $cplay; $j++) {
                        if (in_array($play[$j]['name'], $c2m)) {
                            $play[$j]['zje'] += pr1($tzje / $cc);
                            $play[$j]['zc'] += pr1($tzc / $cc);
                            $play[$j]['ks'] += $tks;
                        } else {
                            $play[$j]['ks'] += $tks2;
                        }
                    }
                }
                $sql = "select peilv1,points,content,je from `$tb_lib` ";
                $sql .= " where userid='$userid' $yq2 $aandb and pid='" . $fsql->f('playid') . "'";
                $msql->query($sql);
                while ($msql->next_record()) {
                    $tffly = $msql->f('je');
                    $tks   = pr2($msql->f('je') * ($msql->f('peilv1') + $msql->f('points') / 100) - $msql->f('je'));
                    $tks2  = 0 - pr2($msql->f('je') - $msql->f('je') * $msql->f('points') / 100);
                    $con   = explode('-', $msql->f('content'));
                    $ccon  = count($con);
                    $c2m   = array();
                    for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_push(getma($con[$j]));
                    }
                    $cc    = count($c2m);
                    $cplay = 49;
                    if ($ccon == 6) {
                        $cplay = 48;
                    }
                    for ($j = 0; $j < $cplay; $j++) {
                        if (in_array($play[$j]['name'], $c2m)) {
                            $play[$j]['fly'] += pr1($tffly / $cc);
                            $play[$j]['ks'] += $tks;
                        } else {
                            $play[$j]['ks'] += $tks2;
                        }
                    }
                }
            }
        }
        $cp = count($play);
        for ($i = 0; $i < $cp; $i++) {
            if (!is_numeric($play[$i]['name']))
                break;
            if ($tmpcid != $play[$i]['cid']) {
                $msql->query("select ftype,classid,abcd,ab from `$tb_class` where classid='" . $play['cid'] . "'");
                if ($msql->f('abcd') == 0)
                    $tmpabcd = 0;
                else
                    $tmpabcd = $abcd;
                if ($msql->f('ab') == 0)
                    $tmpab = 0;
                else
                    $tmpab = $ab;
                if ($msql->f('ftype') <= 4)
                    $ftype = $msql->f('ftype');
                else
                    $ftype = $msql->f('classid');
                $tmppoints = getpoints($ftype, $tmpabcd, $tmpab, $userid);
            }
            if ($play[$i]['ks'] < (0 - $maxksval)) {
                $buk            = abs($play[$i]['ks']) - $maxksval;
                $play[$i]['bu'] = p1($buk / ($play[$i]['peilv1'] + ($tmppoints / 100) - 1));
                if ($play[$i]['bu'] < 0)
                    $play[$i]['bu'] = 0;
            }
            $tmpcid = $play[$i]['cid'];
        }
        if ($_POST['xsort'] == "ks" | $_POST['xsort'] == "zje" | $_POST['xsort'] == "zc" | $_POST['xsort'] == "zs" | $_POST['xsort'] == "name") {
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
    case "getlibb":
        $bid      = $_POST['bid'];
        $ab       = $_POST['ab'];
        $abcd     = $_POST['abcd'];
        $qishu    = $_POST['qishu'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
        if ($setks == 1) {
            $msql->query("update `$tb_user` set maxcm='$maxksval' where userid='$userid'");
        }
        $year  = substr($qishu, 0, 4);
        $qishu = substr($qishu, 4);
        $yq    = " and year='$year' and qishu='$qishu' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        $huama = $_POST['huama'];
        $layer = transuser($userid, 'layer');
        if ($layer < 5) {
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
                $yq .= " and userid='" . $puserid . "' ";
            }
        }
        $zcstr  = "zc" . $layer;
        $uidstr = "uid" . $layer;
        $play   = getplaysa($bid, $ab, $abcd);
        $cp     = count($play);
        if ($ab == 'A' | $ab == 'B') {
            $aandb .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $aandb .= " and abcd='$abcd' ";
        }
        $myzc = 0;
        $cp   = count($play);
        for ($i = 0; $i < $cp; $i++) {
            if ($bid != 23378697 & $bid != 23378699 & $bid != 23378722) {
                if ($tmpcid != $play[$i]['cid']) {
                    $sql = "select sum(je*$zcstr/100),sum((if($peilv1str=0,points,$pointsstr)/100)*je*$zcstr/100)    ";
                    $sql .= " from `$tb_lib`  where $uidstr='$userid'  $yq $aandb and cid='" . $play[$i]['cid'] . "'";
                    $msql->query($sql);
                    $msql->next_record();
                    $czje     = pr1($msql->f(0));
                    $czpoints = pr1($msql->f(1));
                    $sql      = "select sum(je),sum(je*$pointsstr/100) from `$tb_lib` where ";
                    $sql .= " userid='$userid' $yq2 $aandb and cid='" . $play[$i]['cid'] . "'";
                    $msql->query($sql);
                    $msql->next_record();
                    $flyje     = $msql->f(0);
                    $flypoints = $msql->f(1);
                    $myzc += $czje;
                }
            } else {
                $sql = "select sum(je*$zcstr/100),sum((if($peilv1str=0,points,$pointsstr)/100)*je*$zcstr/100)   ";
                $sql .= "from `$tb_lib` where  $uidstr='$userid'   $yq $aandb and pid='" . $play[$i]['pid'] . "'";
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
            $sql = "select sum(je),sum(je*$zcstr/100),count(id),sum(if($peilv1str=0,peilv1,$peilv1str)*je*$zcstr/100)";
            $sql .= " from `$tb_lib` where $uidstr='$userid'   $yq $aandb and pid='" . $play[$i]['pid'] . "'";
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
        $bid     = $_POST['bid'];
        $ab      = $_POST['ab'];
        $abcd    = $_POST['abcd'];
        $qishu   = $_POST['qishu'];
        $puserid = $_POST['userid'];
        $year    = substr($qishu, 0, 4);
        $qishu   = substr($qishu, 4);
        $yq      = " and year='$year' and qishu='$qishu' ";
        $yq2     = $yq;
        $yq .= " and xtype!=2 ";
        $huama = $_POST['huama'];
        $layer = transuser($userid, 'layer');
        if ($layer < 5) {
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
                $yq .= " and userid='" . $puserid . "' ";
            }
        }
        $zcstr  = "zc" . $layer;
        $uidstr = "uid" . $layer;
        $play   = getplaysba($bid, $ab, $abcd);
        $cp     = count($play);
        if ($ab == 'A' | $ab == 'B') {
            $aandb .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $aandb .= " and abcd='$abcd' ";
        }
        $myzc = 0;
        $cp   = count($play);
        for ($i = 0; $i < $cp; $i++) {
            $sql = "select sum(je),sum(je*$zcstr/100),count(id),sum(if($peilv1str=0,peilv1,$peilv1str)*je*$zcstr/100) ";
            $sql .= "  from `$tb_lib` where $uidstr='$userid'   $yq $aandb and pid='" . $play[$i]['pid'] . "'";
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
        $yq      = '';
        $yq2b    = '';
        $yq .= " and xtype!=2  ";
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        $sql .= "select * from `$tb_lib` where $yqa  $yq order by replace(content,'-','')";
        $msql->query($sql);
        $rs = $msql->arr();
        $cr = count($rs);
        for ($i = 0; $i < $cr; $i++) {
        }
        $msql->query("select fid1,layer,ifexe,pself from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $fid1  = $msql->f('fid1');
        $layer = $msql->f('layer');
        $ifexe = $msql->f('ifexe');
        $pself = $msql->f('pself');
        if ($layer > 1) {
            $msql->query("select ifexe,pself from `$tb_user` where userid='$fid1'");
            $msql->next_record();
            $ifexe = $msql->f('ifexe');
            $pself = $msql->f('pself');
        }
        if ($layer == 1) {
            $msql->query("select name,pl from `$tb_play` where gid='$gid' and pid='$pid'");
            $msql->next_record();
            $duo[0] = getduoarr($msql->f('name'));
            $pl     = json_decode($msql->f('pl'), true);
            if ($ifexe == 1) {
                $msql->query("select mpl,pl from `$tb_play_user` where userid='$userid' and gid='$gid' and pid='$pid'");
                $msql->next_record();
                $mepl = json_decode($msql->f('pl'), true);
                $mpl  = json_decode($msql->f('mpl'), true);
            }
            $i  = 0;
            $cd = count($duo[0]);
            for ($i = 0; $i < $cd; $i++) {
                $duo[1][$i] = (float) pr3($pl[0][$i]);
                $duo[2][$i] = (float) pr3($pl[1][$i]);
                $duo[3][$i] = (float) pr3($mepl[0][$i]);
                $duo[4][$i] = (float) pr3($mepl[1][$i]);
                $duo[5][$i] = (float) pr3($mpl[0][$i]);
                $duo[6][$i] = (float) pr3($mpl[1][$i]);
            }
        } else {
            if ($ifexe == 0) {
                $msql->query("select name,pl,cid from `$tb_play` where gid='$gid' and pid='$pid'");
                $msql->next_record();
                $duo[0] = getduoarr($msql->f('name'));
                $pl     = json_decode($msql->f('pl'), true);
                $cid    = $msql->f('cid');
            } else {
                $msql->query("select name,pl,cid from `$tb_play` where gid='$gid' and pid='$pid'");
                $msql->next_record();
                $duo[0] = getduoarr($msql->f('name'));
                $pl     = json_decode($msql->f('pl'), true);
                $cid    = $msql->f('cid');
                $fsql->query("select pl from `$tb_play_user` where userid='$fid1' and gid='$gid' and pid='$pid'");
                $fsql->next_record();
                $pl2 = json_decode($fsql->f('pl'), true);
                if ($pself == 1) {
                    $pl = $pl2;
                } else {
                    $cd = count($duo[0]);
                    for ($i = 0; $i < $cd; $i++) {
                        $pl[0][$i] -= $pl2[0][$i];
                        $pl[1][$i] -= $pl2[1][$i];
                    }
                }
            }
            $ftype = transc('ftype', $cid);
            if ($ifexe == 1 & $pself == 1) {
                $peilvcha = getuserpeilvcha2($userid, $ftype);
            } else {
                $peilvcha = getuserpeilvcha($userid, $ftype);
            }
            $cd = count($duo[0]);
            for ($i = 0; $i < $cd; $i++) {
                $duo[1][$i] = (float) pr3($pl[0][$i] - $peilvcha);
                $duo[2][$i] = (float) pr3($pl[1][$i] - $peilvcha);
            }
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
    case "getlibs":
        $qishu = $_POST['qishu'];
        $year  = substr($qishu, 0, 4);
        $qishu = substr($qishu, 4);
        $yq    = " and year='$year' and qishu='$qishu' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        $pid      = $_POST['pid'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
        if ($setks == 1) {
            $msql->query("update `$tb_user` set maxcm='$maxksval' where userid='$userid'");
        }
        $layer = transuser($userid, 'layer');
        if ($layer < 5) {
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
                $yq .= " and userid='" . $puserid . "' ";
            }
        }
        $zcstr  = "zc" . $layer;
        $uidstr = "uid" . $layer;
        $sql .= "select * from `$tb_lib` where $uidstr='$userid'  and pid='$pid' $yq order by replace(content,'-','')";
        $msql->query($sql);
        $ps = array();
        $i  = 0;
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
    case "getnow":
        $msql->query("select * from `$tb_bclass` where ifok=1");
        $now     = array();
        $i       = 0;
        $layer   = transuser($userid, 'layer');
        $puserid = $_POST['userid'];
        $qishu   = $_POST['qishu'];
        $yq1     = " and concat(year,qishu)='$qishu' ";
        if ($layer < 5) {
            $pointsstr = "points" . ($layer + 1);
            $peilv1str = "peilv1" . ($layer + 1);
            $peilv2str = "peilv2" . ($layer + 1);
            $uidstr    = "uid" . $layer;
            if ($puserid != '') {
                $yq2 = $yq1 . " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='$puserid') ";
            } else {
                $yq2 = $yq1;
            }
        } else {
            $pointsstr = "points";
            $peilv1str = "peilv1";
            $peilv2str = "peilv2";
            $uidstr    = "uid" . $layer;
            if ($puserid != '') {
                $yq2 = $yq1 . " and userid='" . $puserid . "' ";
            } else {
                $yq2 = $yq1;
            }
        }
        $yq2 .= " and  xtype!=2 ";
        $zcstr  = "zc" . $layer;
        $uidstr = "uid" . $layer;
        while ($msql->next_record()) {
            $fsql->query("select sum(je),sum(je*$zcstr/100),count(id) from `$tb_lib` where bid='" . $msql->f('classid') . "' $yq2 and $uidstr='$userid'  ");
            $fsql->next_record(0);
            $now[$i]['zje']   = pr1($fsql->f(0));
            $now[$i]['zjezc'] = pr1($fsql->f(1));
            $now[$i]['zs']    = pr1($fsql->f(2));
            $now[$i]['bid']   = $msql->f('classid');
            $fsql->query("select sum(je) from `$tb_lib` where bid='" . $msql->f('classid') . "' $yq1 and  userid='$userid'");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            $i++;
        }
        echo json_encode($now);
        unset($now);
        break;
    case "getztnow":
        $msql->query("select * from `$tb_class` where fid='23378688' and xshow=1");
        $now     = array();
        $i       = 0;
        $layer   = transuser($userid, 'layer');
        $puserid = $_POST['userid'];
        $qishu   = $_POST['qishu'];
        $yq1     = " and concat(year,qishu)='$qishu' ";
        if ($layer < 5) {
            $pointsstr = "points" . ($layer + 1);
            $peilv1str = "peilv1" . ($layer + 1);
            $peilv2str = "peilv2" . ($layer + 1);
            $uidstr    = "uid" . $layer;
            if ($puserid != '') {
                $yq2 = $yq1 . " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='$puserid') ";
            } else {
                $yq2 = $yq1;
            }
        } else {
            $pointsstr = "points";
            $peilv1str = "peilv1";
            $peilv2str = "peilv2";
            $uidstr    = "uid" . $layer;
            if ($puserid != '') {
                $yq2 = $yq1 . " and userid='" . $puserid . "' ";
            } else {
                $yq2 = $yq1;
            }
        }
        $yq2 .= " and  xtype!=2 ";
        $zcstr  = "zc" . $layer;
        $uidstr = "uid" . $layer;
        while ($msql->next_record()) {
            $fsql->query("select sum(je),sum(je*$zcstr/100),count(id) from `$tb_lib` where bid='23378688' $yq2 and $uidstr='$userid'   and cid in (select classid from  `$tb_class` where mtype='" . $msql->f('mtype') . "' and fid='23378688') ");
            $fsql->next_record(0);
            $now[$i]['zje']   = pr1($fsql->f(0));
            $now[$i]['zjezc'] = pr1($fsql->f(1));
            $now[$i]['zs']    = pr1($fsql->f(2));
            $now[$i]['bid']   = $msql->f('classid');
            $fsql->query("select sum(je) from `$tb_lib` where bid='23378688' $yq1 and  userid='$userid' and cid in (select classid from  `$tb_class` where mtype='" . $msql->f('mtype') . "' and fid='23378688')");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            $i++;
        }
        echo json_encode($now);
        unset($now);
        break;
    case "bucang":
        if ($_SESSION['atype'] != 1) {
            $msql->query("select ifok from `$tb_user_page` where userid='$userid2' and xpage='fly'");
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
            $play           = array();
            $pid            = $_POST['pid'];
            $je             = floor($_POST['je']);
            $con            = $_POST['con'];
            $play[0]['pid'] = $pid;
            $play[0]['je']  = $je;
            $play[0]['con'] = $con;
            if ($play[0]['con'] == null)
                $play[0]['con'] = '';
        }
        $ab   = $_POST['ab'];
        $abcd = strtolower($_POST['abcd']);
        if ($ab != 'A' and $ab != 'B') {
            $ab = 'A';
        }
        if ($abcd == 0)
            $abcd = transuser($userid, 'defaultpan');
        $bid     = $_POST['bid'];
        $cp      = count($play);
        $fly     = $_POST['fly'];
        $flyuser = transuser($userid, 'fly');
        if ($flyuser == 0)
            exit;
        if ($fly != 1 & $fly != 2)
            exit;
        if ($fly == 1 & $flyuser == 2)
            exit;
        if ($fly == 2 & $flyuser == 1)
            exit;
        if ($fly == 2) {
            if ($cp == 1) {
                $points = $_POST['bupoints'];
                $peilv1 = $_POST['bupeilv1'];
                $peilv2 = $_POST['bupeilv2'];
                $tid    = setupid($tb_lib, 'tid');
                $sql    = "insert into `$tb_lib` set tid='$tid',userid='$userid',points='$points',peilv1='$peilv1',peilv2='$peilv2',je='" . $play[0]['je'] . "',content='" . $play[0]['content'] . "',xtype=2";
                $msql->query("select * from `$tb_play` where playid='" . $play[0]['pid'] . "'");
                $msql->next_record();
                $sql .= ",pid='" . $msql->f('playid') . "',cid='" . $msql->f('classid') . "',bid='" . $msql->f('bclassid') . "',time=NOW()";
                $sql .= ",year='$thisyear',qishu='$thisqishu',z='0',abcd='$abcd',ab='$ab'";
                $msql->query($sql);
                echo 1;
                exit;
            }
        }
        $layer = transuser($userid, 'layer');
        if ($layer == 1) {
            $zc[0] = 100;
        } else {
            $topuid = gettopuid($userid, $layer);
            $flyzc  = transuser($topuid, "flyzc");
            $zc[0]  = 100 - $flyzc;
            $zc[1]  = 100 - $zc[0];
            if ($layer > 2) {
                $zc[2] = 0;
            }
            if ($layer > 3) {
                $zc[3] = 0;
            }
            if ($layer > 4) {
                $zc[4] = 0;
            }
        }
        $u        = getfid($userid);
        $czc      = count($zc);
        $peilvcha = array();
        $je       = 0;
        $msql->query("select usermoney,layer,fid1,ifpeilv from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $thelayer = $msql->f('layer');
        $fid1     = $msql->f('fid1');
        $ifpeilv  = $msql->f('ifpeilv');
        if ($thelayer > 1) {
            $ifpeilv = transuser($fid1, 'ifpeilv');
        }
        if ($fid1 == 23382978) {
            $temaclosetime -= 90;
            $otherclosetime -= 90;
        }
        $peilv1s = 0;
        $peilv2s = 0;
        for ($i = 0; $i < $cp; $i++) {
            $msql->query("select sum(je*zc" . $layer . "/100) from `$tb_lib` where pid='" . $play[$i]['pid'] . "' and uid" . $layer . "='$userid' and year='$thisyear' and qishu='$thisqishu' union select sum(je) from `$tb_lib` where userid='$userid'  and year='$thisyear' and qishu='$thisqishu' and  pid='" . $play[$i]['pid'] . "'");
            $msql->next_record();
            $maxfei = $msql->f(0);
            $msql->next_record();
            $yifei = $msql->f(0);
            if ($yifei + $play[$i]['je'] > $maxfei) {
                $play[$i]['err'] = "补货额已超占成金额";
                continue;
            }
            $play[$i]['cg'] = 0;
            $tid            = setupid($tb_lib, 'tid');
            $msql->query("select * from `$tb_play` where playid='" . $play[$i]['pid'] . "'");
            $msql->next_record();
            $bid    = $msql->f('bclassid');
            $cid    = $msql->f('classid');
            $peilv1 = $msql->f('peilv1');
            $peilv2 = $msql->f('peilv2');
            if ($thelayer > 1 & $ifpeilv == 1) {
                $fsql->query("select peilv1,peilv2 from `$tb_play_user` where playid='" . $play[$i]['pid'] . "' and userid='$fid1'");
                $fsql->next_record();
                $peilv1s = $fsql->f('peilv1');
                $peilv2s = $fsql->f('peilv2');
            }
            $z = $msql->f('z');
            if ($z == 9 | $z == 10) {
                $peilv1 -= findlowpeilv($play[$i]['con']);
            }
            if ($play[$i]['pid'] == 25579278) {
                $tsql->query("select peilv1,z from `$tb_play` where instr((select bz from `$tb_lib` where content='" . $play[$i]['con'] . "' limit 1),playid)");
                $cpeilv = 1;
                while ($tsql->next_record()) {
                    if ($tsql->f('z') == 6) {
                        $userpeilvcha = getuserpeilvcha($userid, 3);
                        $cpeilv *= ($tsql->f('peilv1') - $patt[3][$abcd] - $userpeilvcha);
                    } else {
                        $userpeilvcha = getuserpeilvcha($userid, 2);
                        $cpeilv *= ($tsql->f('peilv1') - $patt[2][$abcd] - $userpeilvcha);
                    }
                }
                $peilv1 = round($cpeilv, 3);
                $peilv2 = 0;
            }
            if ($bid == '23378685' & $cid == '23378684')
                $peilv1 -= $flyattpeilv;
            else if ($bid == '23378685')
                $peilv1 -= $flyattpeilvps;
            if ($bid == '23378685' & $tepan == 0) {
                $play[$i]['err'] = "已关盘";
                continue;
            } else if ($bid != '23378685' & $otherpan == 0) {
                $play[$i]['err'] = "已关盘";
                continue;
            }
            if ($msql->f('ifok') == 0) {
                $play[$i]['err'] = "已关盘";
                continue;
            }
            if ($tmpcid != $cid) {
                $msql->query("select ftype,classid,abcd,ab from `$tb_class` where classid='$cid'");
                $msql->next_record();
                $ftype    = $msql->f('ftype');
                $tmpabcha = 0;
                $abcdcha  = 0;
                if ($msql->f('ftype') <= 4) {
                    $ftype   = $msql->f('ftype');
                    $abcdcha = $patt[$ftype][$abcd];
                    if ($ftype == 0 & $ab == 'B') {
                        $tmpabcha = $abcha;
                    }
                    if ($ftype == 1 & $ab == 'B') {
                        $tmpabcha = $ztabcha;
                    }
                } else {
                    $ftype   = $msql->f('classid');
                    $abcdcha = $patt['p' . $ftype][$abcd];
                    if ($ftype == 23378690 & $ab == 'B') {
                        $tmpabcha = $zmabcha;
                    }
                }
                if ($msql->f('abcd') == 0)
                    $tmpabcd = 0;
                else
                    $tmpabcd = $abcd;
                if ($msql->f('ab') == 0)
                    $tmpab = 0;
                else
                    $tmpab = $ab;
                $points      = getpoints($ftype, $tmpabcd, $tmpab, $userid);
                $sqle        = ",points='" . $points . "'";
                $tmppeilvcha = 0;
                for ($j = 0; $j < $czc; $j++) {
                    $sqle .= ",zc" . $j . "='" . $zc[$j] . "'";
                    if ($j > 0) {
                        $arr = getzcs($ftype, $u[$j]);
                        $tmppeilvcha += $arr['peilvcha'];
                        if ($play[$i]['pid'] != 25579278)
                            $peilvcha[$j] = $tmppeilvcha + $abcdcha - $tmpabcha;
                        $lowpeilv[$j] = $arr['lowpeilv'];
                        $points       = getpoints($ftype, $tmpabcd, $tmpab, $u[$j]);
                        $sqle .= ",points" . $j . "='" . $points . "'";
                        $sqle .= ",uid" . $j . "='" . $u[$j] . "'";
                    }
                }
                $arr = getzcs($ftype, $userid);
                $tmppeilvcha += $arr['peilvcha'];
                if ($play[$i]['pid'] != 25579278)
                    $peilvchax = $tmppeilvcha + $abcdcha - $tmpabcha;
                $lowpeilvx = $arr['lowpeilv'];
                $tmpcid    = $cid;
            }
            $play[$i]['peilv1'] = moren($peilv1 - $peilvchax - $peilv1s, $lowpeilvx);
            if ($play[$i]['pid'] == 23379231 | $play[$i]['pid'] == 23379233) {
                $play[$i]['peilv2'] = moren($peilv2 - $peilvchax - $peilv2s, $lowpeilvx);
            } else {
                $play[$i]['peilv2'] = 0;
            }
            $sql = "insert into `$tb_lib` set year='$thisyear',qishu='$thisqishu',tid='$tid',userid='$userid',bid='$bid'";
            $sql .= ",cid='$cid',pid='" . $play[$i]['pid'] . "',abcd='" . strtoupper($abcd) . "',ab='$ab',content='" . $play[$i]['con'] . "'";
            $sql .= ",time=NOW(),je='" . $play[$i]['je'] . "',xtype='1',z='0',peilv1='" . $play[$i]['peilv1'] . "'";
            $sql .= ",peilv2='" . $play[$i]['peilv2'] . "',bz='" . $play[$i]['bz'] . "'";
            $sql .= $sqle;
            for ($j = 1; $j < $czc; $j++) {
                if ($play[$i]['pid'] == 23379231 | $play[$i]['pid'] == 23379233) {
                    if ($j > 1) {
                        $sql .= ",peilv1" . $j . "='" . moren($peilv1 - $peilvcha[$j] - $peilv1s, $lowpeilv[$j]) . "',peilv2" . $j . "='" . moren($peilv2 - $peilvcha[$j] - $peilv2s, $lowpeilv[$j]) . "'";
                    } else {
                        $sql .= ",peilv1" . $j . "='" . moren($peilv1 - $peilvcha[$j], $lowpeilv[$j]) . "',peilv2" . $j . "='" . moren($peilv2 - $peilvcha[$j], $lowpeilv[$j]) . "'";
                    }
                } else {
                    if ($j > 1) {
                        $sql .= ",peilv1" . $j . "='" . moren($peilv1 - $peilvcha[$j] - $peilv1s, $lowpeilv[$j]) . "',peilv2" . $j . "='0'";
                    } else {
                        $sql .= ",peilv1" . $j . "='" . moren($peilv1 - $peilvcha[$j], $lowpeilv[$j]) . "',peilv2" . $j . "='0'";
                    }
                }
            }
            if ($msql->query($sql)) {
                $play[$i]['cg'] = 1;
                $je += $play[$i]['je'];
            } else {
                $play[$i]['cg'] = 0;
            }
        }
        if ($i == 1) {
            echo json_encode($play);
        } else {
            echo 1;
        }
        break;
    case "getatt":
        $abcd = strtolower($_POST['abcd']);
        $ab   = $_POST['ab'];
        $ch   = array();
        if ($tepan == 0) {
            echo json_encode($ch);
            exit;
        }
        $time = time();
        $msql->query("select layer,fid1,ifpeilv from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $fid1     = $msql->f('fid1');
        $thelayer = $msql->f('layer');
        $ifpeilv  = $msql->f('ifpeilv');
        if ($thelayer > 1) {
            $ifpeilv = transuser($fid1, 'ifpeilv');
        }
        $msql->query("select * from `$tb_c` where $time-time<=3 and (userid='99999999' or userid='$fid1')");
        $i = 0;
        while ($msql->next_record()) {
            $fsql->query("select classid,peilv1 from `$tb_play` where playid='" . $msql->f('pid') . "'");
            $fsql->next_record();
            $peilv1 = $fsql->f('peilv1');
            $cid    = $fsql->f('classid');
            $fsql->query("select ftype,classid,fid from `$tb_class` where classid='" . $cid . "'");
            $fsql->next_record();
            if ($fsql->f('ftype') <= 4) {
                $ftype = $fsql->f('ftype');
                $peilv1 -= $patt[$ftype][$abcd];
            } else {
                $ftype = $fsql->f('classid');
                $peilv1 -= $patt['p' . $ftype][$abcd];
            }
            $peilv1 -= getuserpeilvcha($userid, $ftype);
            if ($ftype == 0 & $ab == 'B') {
                $peilv1 += $abcha;
            }
            if ($ftype == 1 & $ab == 'B') {
                $peilv1 += $ztabcha;
            }
            if ($ftype == 23378690 & $ab == 'B') {
                $peilv1 += $zmabcha;
            }
            $ch[$i]['pid']    = $msql->f('pid');
            $ch[$i]['peilv1'] = $peilv1;
            if ($thelayer > 1 & $ifpeilv == 1) {
                $fsql->query("select peilv1 from `$tb_play_user` where userid='$fid1' and playid='" . $msql->f('pid') . "'");
                $fsql->next_record();
                $ch[$i]['peilv1'] -= $fsql->f('peilv1');
            } else if ($ifpeilv == 1 & $thelayer == 1) {
                $fsql->query("select peilv1 from `$tb_play_user` where userid='$fid1' and playid='" . $msql->f('pid') . "'");
                $fsql->next_record();
                $ch[$i]['mepeilv1'] = $ch[$i]['peilv1'] - $fsql->f('peilv1');
            }
            $i++;
        }
        echo json_encode($ch);
        unset($ch);
        break;
}
?>