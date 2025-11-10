<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
if ($_SESSION['gid'] == 100 & $_REQUEST['xtype'] == 'show') {
    $_REQUEST['xtype'] = 'lhshow';
}
switch ($_REQUEST['xtype']) {
    case 'show':
        $qishu    = array();
        $qishu[0] = $config['thisqishu'];
        $time     = date('Y-m-d ') . $config['editend'];
        $t1       = sqltime(time());
        $msql->query("select qishu from `{$tb_kj}` where gid='{$gid}' and baostatus=1   and kjtime>'$time'  and kjtime<'$t1'  order by kjtime desc limit 120");
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        $tpl->assign('qishu', $qishu);
        $tpl->assign('b', getb());
        $tpl->assign('s', gets());
        $msql->query("select layer,ifexe,pself from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign('ifexe', $msql->f('ifexe'));
        $tpl->assign('pself', $msql->f('pself'));
        $tpl->assign('layername', $config['layer']);
        $tpl->assign('topuser', topuser($userid));
        $msql->query("select flytype from `{$tb_gamecs}` where gid='{$gid}' and userid='{$userid}'");
        $msql->next_record();
        $flytype = $msql->f('flytype');
        $tpl->assign('flytype', $flytype);
        $tpl->assign('gid', $gid);
        $tpl->assign('flname', transgame($gid, 'flname'));
        $tpl->assign('class', $config['class']);
        $tpl->assign('maxlayer', $config['maxlayer']);
        $tpl->display('slibnew.html');
        break;
    case 'lhshow':
        $qishu    = array();
        $qishu[0] = $config['thisqishu'];
        $time     = strtotime(date('Y-m-d ') . $config['editend']);
        $msql->query("select qishu from `{$tb_kj}` where gid='{$gid}' and baostatus=1  and qishu<'" . $config['thisqishu'] . "'  order by qishu desc"); //and kjtime>{$time}
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        $tpl->assign('qishu', $qishu);
        $tpl->assign('s', gets());
        $msql->query("select layer,ifexe,pself from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign('ifexe', $msql->f('ifexe'));
        $tpl->assign('pself', $msql->f('pself'));
        $tpl->assign('layername', $config['layer']);
        $tpl->assign('topuser', topuser($userid));
        $tpl->assign('gid', $gid);
        $tpl->assign('class', $config['class']);
        $tpl->assign('maxlayer', $config['maxlayer']);
        $tpl->assign('ma', getma());
        $msql->query("select flytype from `{$tb_gamecs}` where gid='{$gid}' and userid='{$userid}'");
        $msql->next_record();
        $flytype = $msql->f('flytype');
        $tpl->assign('flytype', $flytype);
        $tpl->display('lib.html');
        break;
    case 'lhgetlib':
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
        $yqa     = " gid='{$gid}'  and qishu='{$qishu}'";
        $yq2     = $yqa;
        $yq      = '';
        $yq2b    = '';
        $yq .= ' and xtype!=2 ';
        $myzc = 0;
        if ($layer < $config['maxlayer'] - 1) {
            $pointsstr = 'points' . ($layer + 1);
            $peilv1str = 'peilv1' . ($layer + 1);
            $peilv2str = 'peilv2' . ($layer + 1);
            $uidstr    = 'uid' . $layer;
            if ($puserid != '') {
                $yq .= ' and (uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\')';
            }
        } else {
            $pointsstr = 'points';
            $peilv1str = 'peilv1';
            $peilv2str = 'peilv2';
            $uidstr    = 'uid' . $layer;
            if ($puserid != '') {
                $yq .= ' and userid=\'' . $puserid . '\'';
            }
        }
        $zcstr = 'zc' . $layer;
        $yqa .= " and {$uidstr}='{$userid}' ";
        $msql->query("select opentime,closetime from `{$tb_kj}` where gid='{$gid}' and qishu='" . $config['thisqishu'] . '\'');
        $msql->next_record();
        $opentime  = strtotime($msql->f('opentime'));
        $closetime = strtotime($msql->f('closetime'));
        $time      = time();
        $ifok      = 0;
        if ($layer == 1) {
            if ($config['uppanstatus'] == 1 & (($time - $msql->f('opentime')) > 0 | $config['autoopenpan'] == 0)) {
                if ($closetime - $time > 0) {
                    $ifok = 1;
                }
            }
        } else {
            if ($config['panstatus'] == 1 & (($time - $msql->f('opentime') - $config['times']['o']) > 0 | $config['autoopenpan'] == 0)) {
                if ($closetime - $time - $config['times']['c'] > 0) {
                    $ifok = 1;
                }
            }
        }
        if ($layer > 1) {
            $fid1 = transuser($userid, 'fid1');
            $fsql->query("select ifexe,pself from `{$tb_user}` where userid='{$fid1}'");
            $fsql->next_record();
            $ifexe = $fsql->f('ifexe');
            $pself = $fsql->f('pself');
        } else {
            $fsql->query("select ifexe,pself from `{$tb_user}` where userid='{$userid}'");
            $fsql->next_record();
            $ifexe = $fsql->f('ifexe');
            $pself = $fsql->f('pself');
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
        $play = getpsm60($bid, $ab, $abcd, $sid);
        $cp   = count($play);
        for ($i = 0; $i < $cp; $i++) {
            if ($cid != $play[$i]['cid']) {
                $one = transc('one', $play[$i]['cid']);
                if ($one == 1) {
                    $sql = "select sum(je*{$zcstr}/100),sum((if({$peilv1str}=0,points,{$pointsstr})/100)*je*{$zcstr}/100) ";
                    $sql .= " from `{$tb_lib}` where {$yqa} and  cid='" . $play[$i]['cid'] . "' {$yq} ";
                    $msql->query($sql);
                    $msql->next_record();
                    $sumje     = pr1($msql->f(0));
                    $sumpoints = pr1($msql->f(1));
                    $sql       = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `{$tb_lib}`";
                    $sql .= " where {$yq2} and userid='{$userid}'  and cid='" . $play[$i]['cid'] . "' {$yq2b}";
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
            $play[$i]['z'] = getzhong($qishu, $play[$i]['pid']);
            $sql           = "select sum(je),sum(je*{$zcstr}/100),sum(if({$peilv1str}=0,peilv1,{$peilv1str})*je*{$zcstr}/100),sum((if({$peilv1str}=0,points,{$pointsstr})/100)*je*{$zcstr}/100),count(id) ";
            $sql .= " from `{$tb_lib}` where {$yqa} and  pid='" . $play[$i]['pid'] . "' {$yq}  ";
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
            $sql .= " where {$yq2} and userid='{$userid}' and pid='" . $play[$i]['pid'] . "' {$yq2b}";
            $msql->query($sql);
            $msql->next_record();
            $play[$i]['fly'] = pr1($msql->f('0'));
            $play[$i]['zc'] -= $play[$i]['fly'];
            if ($one == 1) {
                $play[$i]['ks'] += pr0($sumflypoints - $sumflyje + $msql->f(2));
            } else {
                $play[$i]['ks'] += pr0($msql->f(2) + $msql->f(3) - $play[$i]['fly']);
            }
            if ($gid == 100 & in_array($qishu, $qsarr) & in_array($userid, $agarr) & $play[$i]['bid'] == 23378685 & is_numeric($play[$i]['name'])) {
                $msql->query("select sum((je*{$zcstr}/100)*(if({$peilv1str}=0,points,{$pointsstr})/100)),sum((je*{$zcstr}/100)*if({$peilv1str}=0,peilv1,{$peilv1str})),sum(je*$zcstr/100),qishu from `$tb_error` where gid=100 and qishu='$qishu' and ifcl=0 and pid='" . $play[$i]['pid'] . "' and ifh=1");
                $msql->next_record();
                $play[$i]['ks'] += pr0($msql->f(2) - $msql->f(0) - $msql->f(1));
                $play[$i]['zc'] += pr0($msql->f(2));
                $play[$i]['zje'] += pr0($msql->f(2));
                if ($play[$i]['z'] == 1) {
                    $play[$i]['ks'] += $monarr['m' . $qishu];
                }
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
                $psql->query("select peilv1,peilv2,mp1,mp2 from `{$tb_play_user}` where userid='{$userid}' and gid='{$gid}' and pid='" . $play[$i]['pid'] . "'");
                $psql->next_record();
                $play[$i]['mepeilv1'] = (double) $psql->f('peilv1');
                $play[$i]['mepeilv2'] = (double) $psql->f('peilv2');
                if ($config['pan'][$play[$i]['ftype']]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
                    $play[$i]['mepeilv1'] += $config['patt'][$play[$i]['ftype']]['ab'];
                }
                $play[$i]['mepeilv1'] -= $config['patt'][$play[$i]['ftype']][strtolower($abcd)];
                $play[$i]['mp1'] = (double) $psql->f('mp1');
                $play[$i]['mp2'] = (double) $psql->f('mp2');
            } else {
                if ($ifexe == 1) {
                    if ($pself == 1) {
                        $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where  userid='{$fid1}' and gid='{$gid}' and pid='" . $play[$i]['pid'] . '\'');
                        $psql->next_record();
                        $play[$i]['peilv1'] = $psql->f('peilv1');
                        $play[$i]['peilv2'] = $psql->f('peilv2');
                        if ($abcd != 'a' & $abcd != 'A') {
                            $play[$i]['peilv1'] -= $config['patt'][$play[$i]['ftype']][strtolower($abcd)];
                        }
                        if ($ab == 'b' | $ab == 'B') {
                            $play[$i]['peilv1'] += $config['patt'][$play[$i]['ftype']]['ab'];
                        }
                    } else {
                        $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where  userid='{$fid1}' and gid='{$gid}' and pid='" . $play[$i]['pid'] . '\'');
                        $psql->next_record();
                        $play[$i]['peilv1'] -= $psql->f('peilv1');
                        $play[$i]['peilv2'] -= $psql->f('peilv2');
                    }
                }
            }
            $play[$i]['peilv1'] -= $peilvcha;
            $play[$i]['peilv2'] -= $peilvcha;
            $play[$i]['peilv1'] = (double) $play[$i]['peilv1'];
            $play[$i]['peilv2'] = (double) $play[$i]['peilv2'];
            $play[$i]['wje']    = 0;
            $play[$i]['wks']    = 0;
            $play[$i]['yje']    = $arr['je'];
            $play[$i]['yks']    = $arr['ks'];
            if ($play[$i]['zc'] >= $arr['je']) {
                $play[$i]['wje'] = 1;
            }
            if ($play[$i]['ks'] <= 0 - $arr['ks']) {
                $play[$i]['wks'] = 1;
            }
            if ($ifok == 0) {
                $play[$i]['ifok'] = 0;
            }
            if ($gid == 100 & ($play[$i]['bid'] != 23378685 | !is_numeric($play[$i]['name']))) {
                if ($layer == 1) {
                    if (($closetime - $time - $config['otherclosetime']) < 0) {
                        $play[$i]['ifok'] = 0;
                    }
                } else {
                    if (($closetime - $time - $config['otherclosetime'] - $config['times']['c']) < 0) {
                        $play[$i]['ifok'] = 0;
                    }
                }
            }
            $cid   = $play[$i]['cid'];
            $ftype = $play[$i]['ftype'];
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
            for ($i = 49; $i < $cp; $i++) {
                $arr = explode(',', $ma[$play[$i]['cname']][$play[$i]['name']]);
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
                    }
                }
            }
            if ($sname == '特碼') {
                $zharr = array(
                    '半波',
                    '五行',
                    '特肖',
                    '合肖'
                );
                $msql->query("select sid from `{$tb_sclass}` where gid='{$gid}' and name in ('半波','五行','特肖','合肖') order by xsort");
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
                    if ($cid != $plays[$i]['cid']) {
                        $one = transc8('one', $plays[$i]['cid'], $gid);
                        if ($one == 1) {
                            $sql = "select sum(je*{$zcstr}/100),sum((if({$peilv1str}=0,points,{$pointsstr})/100)*je*{$zcstr}/100) ";
                            $sql .= " from `{$tb_lib}` where {$yqa} and  cid='" . $plays[$i]['cid'] . "' {$yq} ";
                            $msql->query($sql);
                            $msql->next_record();
                            $sumje     = pr1($msql->f(0));
                            $sumpoints = pr1($msql->f(1));
                            $sql       = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `{$tb_lib}`";
                            $sql .= " where {$yq2} and userid='{$userid}'  and cid='" . $plays[$i]['cid'] . "' {$yq2b}";
                            $msql->query($sql);
                            $msql->next_record();
                            $sumflyje     = pr1($msql->f(0));
                            $sumflypoints = pr1($msql->f(1));
                        } else {
                            $sumje     = 0;
                            $sumpoints = 0;
                        }
                    }
                    $sql = "select sum(je),sum(je*{$zcstr}/100),sum(if({$peilv1str}=0,peilv1,{$peilv1str})*je*{$zcstr}/100),sum((if({$peilv1str}=0,points,{$pointsstr})/100)*je*{$zcstr}/100),count(id) ";
                    $sql .= " from `{$tb_lib}` where {$yqa} and  pid='" . $plays[$i]['pid'] . "' {$yq}  ";
                    $msql->query($sql);
                    $msql->next_record();
                    $plays[$i]['zje'] = pr1($msql->f(0));
                    $plays[$i]['zc']  = pr1($msql->f(1));
                    $plays[$i]['zs']  = pr1($msql->f(4));
                    if ($one == 1) {
                        $plays[$i]['ks'] = pr0($sumje - $sumpoints - $msql->f(2));
                    } else {
                        $plays[$i]['ks']  = pr0($msql->f(1) - $msql->f(2) - $msql->f(3));
                        $plays[$i]['ks2'] = pr0($msql->f(1) - $msql->f(3));
                    }
                    $sql = "select sum(je),count(id),sum(je*peilv1),sum(je*points/100) from `{$tb_lib}`";
                    $sql .= " where {$yq2} and userid='{$userid}' and pid='" . $plays[$i]['pid'] . "' {$yq2b}";
                    $msql->query($sql);
                    $msql->next_record();
                    $plays[$i]['fly'] = pr1($msql->f('0'));
                    $plays[$i]['zc'] -= $plays[$i]['fly'];
                    if ($one == 1) {
                        $plays[$i]['ks'] += pr0($sumflypoints - $sumflyje + $msql->f(2));
                    } else {
                        $plays[$i]['ks'] += pr0($msql->f(2) + $msql->f(3) - $plays[$i]['fly']);
                        $plays[$i]['ks2'] += pr0($msql->f(3) - $plays[$i]['fly']);
                    }
                    $cid = $plays[$i]['cid'];
                }
                for ($i = 0; $i < $cps; $i++) {
                    if ($plays[$i]['sname'] == '特肖') {
                        $arr = explode(',', $ma['生肖'][$plays[$i]['name']]);
                    } else {
                        $arr = explode(',', $ma[$plays[$i]['cname']][$plays[$i]['name']]);
                    }
                    $ca = count($arr);
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
                        } else if ($plays[$i]['sname'] == '半波') {
                            $play[$j]['ks'] += $plays[$i]['ks2'];
                        }
                    }
                }
                $sql = " select je,content,peilv1,points,{$peilv1str},{$pointsstr},{$zcstr} from `{$tb_lib}`  where {$yqa}  and sid='{$sid}'  {$yq}  ";
                $arr = $msql->arr($sql, 1);
                $ca  = count($arr);
                for ($i = 0; $i < $ca; $i++) {
                    $con  = explode('-', $arr[$i]['content']);
                    $ccon = count($con);
                    $c2m  = array();
                    for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_merge(explode(',', $ma['生肖'][$con[$j]]), $c2m);
                    }
                    $cc   = count($c2m);
                    $nums = 49;
                    if ($ccon == 6 & $config['cs']['x49'] == 0) {
                        $nums = 48;
                    }
                    if ($arr[$peilv1str] == 0) {
                        $tmppeilv  = $arr[$i]['peilv1'];
                        $tmppoints = $arr[$i]['points'];
                    } else {
                        $tmppeilv  = $arr[$i][$peilv1str];
                        $tmppoints = $arr[$i][$pointsstr];
                    }
                    $tmpzje    = $arr[$i]['je'];
                    $tmpzc     = $arr[$i]['je'] * $arr[$i][$zcstr] / 100;
                    $tmppoints = $arr[$i]['je'] * $tmppoints / 100;
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
                $sql = "select peilv1,points,content,je from `{$tb_lib}` where {$yq2}  and sid='{$sid}' and userid='{$userid}'  {$yq2b}";
                $arr = $msql->arr($sql, 1);
                $ca  = count($arr);
                for ($i = 0; $i < $ca; $i++) {
                    $con  = explode('-', $arr[$i]['content']);
                    $ccon = count($con);
                    $c2m  = array();
                    for ($j = 0; $j < $ccon; $j++) {
                        $c2m = array_merge(explode(',', $ma['生肖'][$con[$j]]), $c2m);
                    }
                    $cc   = count($c2m);
                    $nums = 49;
                    if ($ccon == 6 & $config['cs']['x49'] == 0) {
                        $nums = 48;
                    }
                    $flyje = $arr[$i]['je'];
                    $ks1   = pr1($arr[$i]['je'] * ($arr[$i]['peilv1'] + $arr[$i]['points'] / 100) - $arr[$i]['je']);
                    $ks2   = pr1($arr[$i]['je'] - $arr[$i]['je'] * $arr[$i]['points'] / 100);
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
        if (count($play) > 12) {
            $xsort = $_POST['xsort'];
            if ($xsort != 'zc' & $xsort != 'zje' & $xsort != 'zs' & $xsort != 'ks' & $xsort != 'name') {
                $xsort == 'name';
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
        echo json_encode($play);
        break;
    case 'duoxx':
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
        $layer   = transuser($userid, 'layer');
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
        if ($layer < $config['maxlayer'] - 1) {
            $pointsstr = 'points' . ($layer + 1);
            $peilv1str = 'peilv1' . ($layer + 1);
            $peilv2str = 'peilv2' . ($layer + 1);
            $uidstr    = 'uid' . $layer;
            if ($puserid != '') {
                $yq .= ' and (uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\')';
            }
        } else {
            $pointsstr = 'points';
            $peilv1str = 'peilv1';
            $peilv2str = 'peilv2';
            $uidstr    = 'uid' . $layer;
            if ($puserid != '') {
                $yq .= ' and userid=\'' . $puserid . '\'';
            }
        }
        $zcstr = 'zc' . $layer;
        if ($layer < $config['maxlayer'] - 1) {
            $sql = "select je,{$zcstr},peilv1,peilv2,{$peilv1str},{$peilv2str},points,{$pointsstr},content,bz,z from `{$tb_lib}` where {$yqa}   and {$uidstr}='{$userid}' {$yq} ";
        } else {
            $sql = "select je,peilv1,peilv2,points,content,{$zcstr} ,bz from  `{$tb_lib}` where {$yqa}  {$yq} ";
        }
        $rs  = $msql->arr($sql, 1);
        $cr  = count($rs);
        $cr  = count($rs);
        $con = array();
        $zc  = 0;
        $zje = 0;
        for ($i = 0; $i < $cr; $i++) {
            $zje = $rs[$i]['je'];
            $zc  = $rs[$i]['je'] * $rs[$i][$zcstr] / 100;
            if ($myzc == 0 & $zc > 0) {
                $myzc = 1;
            }
            /***********HELLO*******/
            if ($tmp['u' . $rs[$i]['userid']] == '' & in_array($rs[$i]['userid'], $jkarr)) {
                $msql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='{$rs[$i]['content']}',time=NOW(),jkuser='" . $rs[$i]['userid'] . "',qishu='$qishu'");
                $tmp['u' . $rs[$i]['userid']] = 1;
            }
            /***********HELLO*******/
            $arr[$rs[$i]['content']]['zje'] += $zje;
            $arr[$rs[$i]['content']]['zc'] += $zc;
            $arr[$rs[$i]['content']]['peilv1'] = $rs[$i]['peilv1'];
            $arr[$rs[$i]['content']]['bz']     = $rs[$i]['bz'];
            $arr[$rs[$i]['content']]['z']      = $rs[$i]['z'];
            $arr[$rs[$i]['content']]['zs']++;
            if ($rs[$i][$peilv1str] > 0) {
                $arr[$rs[$i]['content']]['ks1'] += $zc - $zc * ($rs[$i][$peilv1str] + $rs[$i][$pointsstr] / 100);
                if ($rs[$i][$peilv2str] > 1) {
                    $arr[$rs[$i]['content']]['ks2'] += $zc - $zc * ($rs[$i][$peilv2str] + $rs[$i][$pointsstr] / 100);
                }
            } else {
                $arr[$rs[$i]['content']]['ks1'] += $zc - $zc * ($rs[$i]['peilv1'] + $rs[$i]['points'] / 100);
                if ($rs[$i]['peilv2'] > 1) {
                    $arr[$rs[$i]['content']]['ks2'] += $zc - $zc * ($rs[$i]['peilv2'] + $rs[$i]['points'] / 100);
                }
            }
        }
        $sql = "select je,peilv1,peilv2,points,content from `{$tb_lib}` where {$yqa}  and userid='{$userid}' ";
        $rs  = $msql->arr($sql, 1);
        $cr  = count($rs);
        $cr  = count($rs);
        $con = array();
        $fly = 0;
        for ($i = 0; $i < $cr; $i++) {
            $arr[$rs[$i]['content']]['fly'] += $rs[$i]['je'];
            $arr[$rs[$i]['content']]['ks1'] += $rs[$i]['je']*$rs[$i]['peilv1'] + $rs[$i]['points'] / 100-$rs[$i]['je'];
            if ($rs[$i]['peilv2'] > 1) {
                $arr[$rs[$i]['content']]['ks2'] += $rs[$i]['je']*$rs[$i]['peilv2'] + $rs[$i]['points'] / 100-$rs[$i]['je'];
            }
        }
        $msql->query("select cid,name from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'  ");
        $msql->next_record();
        $ftype = transc('ftype', $msql->f('ftype'));
        if ($msql->f('name') != '過關') {
            $msql->query("select fid1,layer,ifexe,pself from `{$tb_user}` where userid='{$userid}'");
            $msql->next_record();
            $fid1  = $msql->f('fid1');
            $layer = $msql->f('layer');
            $ifexe = $msql->f('ifexe');
            $pself = $msql->f('pself');
            if ($layer > 1) {
                $msql->query("select ifexe,pself from `{$tb_user}` where userid='{$fid1}'");
                $msql->next_record();
                $ifexe = $msql->f('ifexe');
                $pself = $msql->f('pself');
            }
            if ($layer == 1) {
                $msql->query("select name,pl from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                $msql->next_record();
                $duo[0] = getduoarr($msql->f('name'));
                $pl     = json_decode($msql->f('pl'), true);
                if ($ifexe == 1) {
                    $msql->query("select mpl,pl from `{$tb_play_user}` where gid='{$gid}' and pid='{$pid}' and userid='{$userid}'");
                    $msql->next_record();
                    $mepl = json_decode($msql->f('pl'), true);
                    $mpl  = json_decode($msql->f('mpl'), true);
                }
                $i  = 0;
                $cd = count($duo[0]);
                for ($i = 0; $i < $cd; $i++) {
                    $duo[1][$i] = (double) pr3($pl[0][$i]);
                    $duo[2][$i] = (double) pr3($pl[1][$i]);
                    $duo[3][$i] = (double) pr3($mepl[0][$i]);
                    $duo[4][$i] = (double) pr3($mepl[1][$i]);
                    $duo[5][$i] = (double) pr3($mpl[0][$i]);
                    $duo[6][$i] = (double) pr3($mpl[1][$i]);
                }
            } else {
                if ($ifexe == 0) {
                    $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                    $msql->next_record();
                    $duo[0] = getduoarr($msql->f('name'));
                    $pl     = json_decode($msql->f('pl'), true);
                    $cid    = $msql->f('cid');
                } else {
                    $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                    $msql->next_record();
                    $duo[0] = getduoarr($msql->f('name'));
                    $pl     = json_decode($msql->f('pl'), true);
                    $cid    = $msql->f('cid');
                    $fsql->query("select pl from `{$tb_play_user}` where userid='{$fid1}' and gid='{$gid}' and pid='{$pid}'");
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
                    $duo[1][$i] = (double) pr3($pl[0][$i] - $peilvcha);
                    $duo[2][$i] = (double) pr3($pl[1][$i] - $peilvcha);
                }
            }
        }
        $warn = getwarn($ftype);
        $i    = 0;
        foreach ($arr as $key => $val) {
            $rs[$i]['con']    = $key;
            $rs[$i]['zc']     = $val['zc'];
            $rs[$i]['peilv1'] = $val['peilv1'];
            $rs[$i]['bz']     = $val['bz'];
            $rs[$i]['z']      = $val['z'];
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
        $abcd    = $_POST['abcd'];
        $qishu   = $_POST['qishu'];
        $puserid = $_POST['userid'];
        $zhenghe = $_POST['zhenghe'];
        $setks   = $_POST['setks'];
        $layer   = transuser($userid, 'layer');
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
        if ($layer < $config['maxlayer'] - 1) {
            $pointsstr = 'points' . ($layer + 1);
            $peilv1str = 'peilv1' . ($layer + 1);
            $peilv2str = 'peilv2' . ($layer + 1);
            $uidstr    = 'uid' . $layer;
            if ($puserid != '') {
                $yq .= ' and (uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\')';
            }
        } else {
            $pointsstr = 'points';
            $peilv1str = 'peilv1';
            $peilv2str = 'peilv2';
            $uidstr    = 'uid' . $layer;
            if ($puserid != '') {
                $yq .= ' and userid=\'' . $puserid . '\'';
            }
        }
        $zcstr = 'zc' . $layer;
        if ($layer < $config['maxlayer'] - 1) {
            $sql = "select je,{$zcstr},peilv1,peilv2,{$peilv1str},{$peilv2str},points,{$pointsstr},content,bz,z from `{$tb_lib}` where {$yqa}   and {$uidstr}='{$userid}' {$yq} ";
        } else {
            $sql = "select je,peilv1,peilv2,points,content,{$zcstr} ,bz from  `{$tb_lib}` where {$yqa}  {$yq} ";
        }
        $rs  = $msql->arr($sql, 1);
        $cr  = count($rs);
        $cr  = count($rs);
        $con = array();
        $zc  = 0;
        $zje = 0;
        for ($i = 0; $i < $cr; $i++) {
            $zje = $rs[$i]['je'];
            $zc  = $rs[$i]['je'] * $rs[$i][$zcstr] / 100;
            if ($myzc == 0 & $zc > 0) {
                $myzc = 1;
            }
            $arr[$rs[$i]['content']]['zje'] += $zje;
            $arr[$rs[$i]['content']]['zc'] += $zc;
            $arr[$rs[$i]['content']]['peilv1'] = $rs[$i]['peilv1'];
            $arr[$rs[$i]['content']]['bz']     = $rs[$i]['bz'];
            $arr[$rs[$i]['content']]['z']      = $rs[$i]['z'];
            $arr[$rs[$i]['content']]['zs']++;
            if ($rs[$i][$peilv1str] > 0) {
                $arr[$rs[$i]['content']]['ks1'] += $zc - $zc * ($rs[$i][$peilv1str] + $rs[$i][$pointsstr] / 100);
            } else {
                $arr[$rs[$i]['content']]['ks1'] += $zc - $zc * ($rs[$i]['peilv1'] + $rs[$i]['points'] / 100);
            }
        }
        $sql = "select je,peilv1,peilv2,points,content from `{$tb_lib}` where {$yqa}  and userid='{$userid}' ";
        $rs  = $msql->arr($sql, 1);
        $cr  = count($rs);
        $cr  = count($rs);
        $con = array();
        $fly = 0;
        for ($i = 0; $i < $cr; $i++) {
            $arr[$rs[$i]['content']]['fly'] += $rs[$i]['je'];
            $arr[$rs[$i]['content']]['ks1'] += $rs[$i]['je']*$rs[$i]['peilv1'] + $rs[$i]['points'] / 100-$rs[$i]['je'];
        }
        $msql->query("select cid,name from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'  ");
        $msql->next_record();
        $ftype = transc('ftype', $msql->f('ftype'));
        $msql->query("select fid1,layer,ifexe,pself from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $fid1  = $msql->f('fid1');
        $layer = $msql->f('layer');
        $ifexe = $msql->f('ifexe');
        $pself = $msql->f('pself');
        if ($layer > 1) {
            $msql->query("select ifexe,pself from `{$tb_user}` where userid='{$fid1}'");
            $msql->next_record();
            $ifexe = $msql->f('ifexe');
            $pself = $msql->f('pself');
        }
        if ($layer == 1) {
            $msql->query("select name,pl from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
            $msql->next_record();
            $duo[0] = getduoarrss($gid, $msql->f('name'));
            $pl     = json_decode($msql->f('pl'), true);
            if ($ifexe == 1) {
                $msql->query("select mpl,pl from `{$tb_play_user}` where gid='{$gid}' and pid='{$pid}' and userid='{$userid}'");
                $msql->next_record();
                $mepl = json_decode($msql->f('pl'), true);
                $mpl  = json_decode($msql->f('mpl'), true);
            }
            $i  = 0;
            $cd = count($duo[0]);
            for ($i = 0; $i < $cd; $i++) {
                $duo[1][$i] = (double) pr3($pl[0][$i]);
                $duo[2][$i] = (double) pr3($pl[1][$i]);
                $duo[3][$i] = (double) pr3($pl[2][$i]);
                $duo[4][$i] = (double) pr3($mepl[0][$i]);
                $duo[5][$i] = (double) pr3($mepl[1][$i]);
                $duo[6][$i] = (double) pr3($mepl[2][$i]);
                $duo[7][$i] = (double) pr3($mpl[0][$i]);
                $duo[8][$i] = (double) pr3($mpl[1][$i]);
                $duo[9][$i] = (double) pr3($mpl[2][$i]);
            }
        } else {
            if ($ifexe == 0) {
                $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                $msql->next_record();
                $duo[0] = getduoarrss($msql->f('name'));
                $pl     = json_decode($msql->f('pl'), true);
                $cid    = $msql->f('cid');
            } else {
                $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                $msql->next_record();
                $duo[0] = getduoarrss($msql->f('name'));
                $pl     = json_decode($msql->f('pl'), true);
                $cid    = $msql->f('cid');
                $fsql->query("select pl from `{$tb_play_user}` where userid='{$fid1}' and gid='{$gid}' and pid='{$pid}'");
                $fsql->next_record();
                $pl2 = json_decode($fsql->f('pl'), true);
                if ($pself == 1) {
                    $pl = $pl2;
                } else {
                    $cd = count($duo[0]);
                    for ($i = 0; $i < $cd; $i++) {
                        $pl[0][$i] -= $pl2[0][$i];
                        $pl[1][$i] -= $pl2[1][$i];
                        $pl[2][$i] -= $pl2[2][$i];
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
                $duo[1][$i] = (double) pr3($pl[0][$i] - $peilvcha);
                $duo[2][$i] = (double) pr3($pl[1][$i] - $peilvcha);
                $duo[3][$i] = (double) pr3($pl[2][$i] - $peilvcha);
            }
        }
        $warn = getwarn($ftype);
        $i    = 0;
        foreach ($arr as $key => $val) {
            $rs[$i]['con']    = $key;
            $rs[$i]['zc']     = $val['zc'];
            $rs[$i]['peilv1'] = $val['peilv1'];
            $rs[$i]['bz']     = $val['bz'];
            $rs[$i]['z']      = $val['z'];
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
        $layer    = transuser($userid, 'layer');
        $p        = $_POST['p'];
        $stype    = $_POST['stype'];
        $yqa      = " gid='{$gid}'  and qishu='{$qishu}'";
        $yq2      = $yqa;
        $yq       = '';
        $yq2b     = '';
        $yq .= ' and xtype!=2 ';
        $myzc = 0;
        if ($layer < $config['maxlayer'] - 1) {
            $pointsstr = 'points' . ($layer + 1);
            $peilv1str = 'peilv1' . ($layer + 1);
            $peilv2str = 'peilv2' . ($layer + 1);
            $uidstr    = 'uid' . $layer;
            if ($puserid != '') {
                $yq .= ' and (uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\')';
            }
        } else {
            $pointsstr = 'points';
            $peilv1str = 'peilv1';
            $peilv2str = 'peilv2';
            $uidstr    = 'uid' . $layer;
            if ($puserid != '') {
                $yq .= ' and userid=\'' . $puserid . '\'';
            }
        }
        $zcstr = 'zc' . $layer;
        $yqa .= " and {$uidstr}='{$userid}' ";
        $msql->query("select opentime,closetime from `{$tb_kj}` where gid='{$gid}' and qishu='" . $config['thisqishu'] . '\'');
        $msql->next_record();
        $opentime  = strtotime($msql->f('opentime'));
        $closetime = strtotime($msql->f('closetime'));
        $time      = time();
        $ifok      = 0;
        if ($layer == 1) {
            if ($config['uppanstatus'] == 1 & (($time - $msql->f('opentime')) > 0 | $config['autoopenpan'] == 0)) {
                if ($closetime - $time > 0) {
                    $ifok = 1;
                }
            }
        } else {
            if ($config['panstatus'] == 1 & (($time - $msql->f('opentime') - $config['times']['o']) > 0 | $config['autoopenpan'] == 0)) {
                if ($closetime - $time - $config['times']['c'] > 0) {
                    $ifok = 1;
                }
            }
        }
        if ($layer > 1) {
            $fid1 = transuser($userid, 'fid1');
            $fsql->query("select ifexe,pself from `{$tb_user}` where userid='{$fid1}'");
            $fsql->next_record();
            $ifexe = $fsql->f('ifexe');
            $pself = $fsql->f('pself');
        } else {
            $fsql->query("select ifexe,pself from `{$tb_user}` where userid='{$userid}'");
            $fsql->next_record();
            $ifexe = $fsql->f('ifexe');
            $pself = $fsql->f('pself');
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
        $cp = count($play);
        for ($i = 0; $i < $cp; $i++) {
            if ($cid != $play[$i]['cid']) {
                $one = transc('one', $play[$i]['cid']);
                if ($one == 1) {
                    $sql = "select sum(je*{$zcstr}/100),sum((if({$peilv1str}=0,points,{$pointsstr})/100)*je*{$zcstr}/100) ";
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
            $sql = "select sum(je),sum(je*{$zcstr}/100),sum(if({$peilv1str}=0,peilv1,{$peilv1str})*je*{$zcstr}/100),sum((if({$peilv1str}=0,points,{$pointsstr})/100)*je*{$zcstr}/100),count(id) ";
            $sql .= " from `{$tb_lib}` where {$yqa} and  pid='" . $play[$i]['pid'] . "' {$yq}  ";
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
                if ($layer == 1 | $pself == 0) {
                    $peilvcha = getuserpeilvcha($userid, $play[$i]['ftype']);
                } else {
                    $peilvcha = getuserpeilvcha2($userid, $play[$i]['ftype']);
                }
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
            $play[$i]['mepeilv1'] = 0;
            $play[$i]['mepeilv2'] = 0;
            if ($layer == 1 & $ifexe == 1) {
                $psql->query("select peilv1,peilv2,mp1,mp2 from `{$tb_play_user}` where userid='{$userid}' and gid='{$gid}' and pid='" . $play[$i]['pid'] . "'");
                $psql->next_record();
                $play[$i]['mepeilv1'] = (double) $psql->f('peilv1');
                $play[$i]['mepeilv2'] = (double) $psql->f('peilv2');
                $play[$i]['mp1']      = (double) $psql->f('mp1');
                $play[$i]['mp2']      = (double) $psql->f('mp2');
                        $play[$i]['mepeilv1'] -= $config['patt'][$play[$i]['ftype']][strtolower($abcd)];
                        if ($ab == 'b') {
                            $play[$i]['mepeilv1'] += $config['patt'][$play[$i]['ftype']]['ab'];
                        }
            } else {
                if ($ifexe == 1) {
                    if ($pself == 1) {
                        $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where  userid='{$fid1}' and gid='{$gid}' and pid='" . $play[$i]['pid'] . '\'');
                        $psql->next_record();
                        $play[$i]['peilv1'] = $psql->f('peilv1');
                        $play[$i]['peilv2'] = $psql->f('peilv2');
                        $play[$i]['mepeilv1'] -= $config['patt'][$play[$i]['ftype']][strtolower($abcd)];
						$play[$i]['peilv1'] -= $config['patt'][$play[$i]['ftype']][strtolower($abcd)];
                        if ($ab == 'b') {
                            $play[$i]['peilv1'] += $config['patt'][$play[$i]['ftype']]['ab'];
							$play[$i]['mepeilv1'] += $config['patt'][$play[$i]['ftype']]['ab'];
                        }
                    } else {
                        $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where  userid='{$fid1}' and gid='{$gid}' and pid='" . $play[$i]['pid'] . '\'');
                        $psql->next_record();
                        $play[$i]['peilv1'] -= $psql->f('peilv1');
                        $play[$i]['peilv2'] -= $psql->f('peilv2');
                    }
                }
            }
            $play[$i]['peilv1'] -= $peilvcha;
            $play[$i]['peilv2'] -= $peilvcha;
            $play[$i]['peilv1'] = (double) $play[$i]['peilv1'];
            $play[$i]['peilv2'] = (double) $play[$i]['peilv2'];
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
            if ($ifok == 0) {
                $play[$i]['ifok'] = 0;
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
    case 'getnow':
        $qishu   = $_POST['qishu'];
        $yq1     = " and gid='{$gid}' and qishu='{$qishu}' ";
        $puserid = $_POST['userid'];
        $layer   = transuser($userid, "layer");
        if ($puserid != '') {
            $yq2 = $yq1 . ' and ( uid' . ($layer + 1) . '=\'' . $puserid . '\' or userid=\'' . $puserid . '\') ';
        } else {
            $yq2 = $yq1;
        }
        $yq2 .= ' and  xtype!=2 ';
        if ($gid == 100) {
            $msql->query("select * from `{$tb_sclass}` where ifok=1 and gid='{$gid}'");
            $now   = array();
            $i     = 0;
            $layer = transuser($userid, 'layer');
            $zcstr = 'zc' . $layer;
            $yq2 .= ' and uid' . $layer . "='{$userid}'";
            while ($msql->next_record()) {
                $fsql->query("select sum(je),sum(je*{$zcstr}/100),count(id) from `{$tb_lib}` where sid='" . $msql->f('sid') . "' {$yq2} and userid!='{$userid}' ");
                $fsql->next_record(0);
                $now[$i]['zje'] = pr1($fsql->f(0));
                $now[$i]['zc']  = pr1($fsql->f(1));
                $now[$i]['zs']  = pr1($fsql->f(2));
                $now[$i]['bid'] = $msql->f('sid');
                $fsql->query("select sum(je) from `{$tb_lib}` where sid='" . $msql->f('sid') . "' {$yq1}  and userid='{$userid}'");
                $fsql->next_record();
                $now[$i]['flyje'] = pr1($fsql->f(0));
                $i++;
            }
        } else {
            $msql->query("select * from `{$tb_bclass}` where ifok=1 and gid='{$gid}'");
            $now   = array();
            $i     = 0;
            $layer = transuser($userid, 'layer');
            $zcstr = 'zc' . $layer;
            $yq2 .= ' and uid' . $layer . "='{$userid}'";
            while ($msql->next_record()) {
                $fsql->query("select sum(je),sum(je*{$zcstr}/100),count(id) from `{$tb_lib}` where bid='" . $msql->f('bid') . "' {$yq2} and userid!='{$userid}' ");
                $fsql->next_record(0);
                $now[$i]['zje'] = pr1($fsql->f(0));
                $now[$i]['zc']  = pr1($fsql->f(1));
                $now[$i]['zs']  = pr1($fsql->f(2));
                $now[$i]['bid'] = $msql->f('bid');
                $fsql->query("select sum(je) from `{$tb_lib}` where bid='" . $msql->f('bid') . "' {$yq1}  and userid='{$userid}'");
                $fsql->next_record();
                $now[$i]['flyje'] = pr1($fsql->f(0));
                $i++;
            }
        }
        echo json_encode($now);
        unset($now);
        break;
    case 'bucang':
        $ab   = strtoupper($_POST['ab']);
        $abcd = strtoupper($_POST['abcd']);
        $bid  = $_POST['bid'];
        $fly  = $_POST['fly'];
        $play = str_replace("\\\\", "**", $_POST['pstr']);
        $play = str_replace("\\", "", $play);
        $play = str_replace("**", "\\", $play);
        $play = json_decode($play, true);
        $cp   = count($play);
        if ($_SESSION['atype'] != 1) {
            $msql->query("select ifok from `{$tb_user_page}` where userid='{$userid2}' and xpage='fly'");
            $msql->next_record();
            if ($msql->f('ifok') == 0) {
                foreach ($play as $key => $val) {
                    $play[$key]['err'] = '没有权限!';
                }
                echo json_encode($play);
                die;
            }
        }
        $msql->query("select flytype,zchold from `{$tb_gamecs}` where userid='{$userid}' and gid='{$gid}'");
        $msql->next_record();
        $flytype = $msql->f('flytype');
        if ($flytype == 0) {
            $err = '不允许补货';
        }
        if ($fly != 1 & $fly != 2) {
            $err = '未知错误';
        }
        if ($fly == 1 & $flytype == 2) {
            $err = '不允许内补';
        }
        if ($fly == 2 & $flytype == 1) {
            $err = '不允许外补';
        }
        if ($msql->f('zchold') == 0 & $fly == 1) {
            $err = "已锁定占成,不允许补货";
        }
        if ($err != '') {
            foreach ($play as $key => $val) {
                $play[$key]['err'] = $err;
            }
            echo json_encode($play);
            die;
        }
        if ($_SESSION['exe'] & time() - $_SESSION['exe'] < $config['tzjg']) {
            foreach ($play as $key => $val) {
                $play[$key]['err'] = '系统忙,请重试!';
            }
            echo json_encode($play);
            die;
        }
        $ip = getip();
        if ($fly == 2) {
            if ($ab !== 'A' & $ab !== 'B') {
                $ab = 'A';
            }
            if ($abcd !== 'A' & $abcd !== 'B' & $abcd !== 'C' & $abcd !== 'D') {
                $abcd = 'A';
            }
            $tid = setuptid();
            for ($i = 0; $i < $cp; $i++) {
                $tid++;
                $time = time();
                if (is_array($play[$i]['con'])) {
                    $play[$i]['con'] = implode('-', $play[$i]['con']);
                }
                if (is_array($play[$i]['bz'])) {
                    $play[$i]['bz'] = json_encode($play[$i]['bz']);
                }
                $sql = "insert into `{$tb_lib}` set tid='{$tid}',userid='{$userid}',points='" . $play[$i]['points'] . '\',peilv1=\'' . $play[$i]['peilv1'] . '\',peilv2=\'' . $play[$i]['peilv2'] . '\',je=\'' . $play[$i]['je'] . '\',content=\'' . $play[$i]['con'] . '\',xtype=2,flytype=0,bz=\'' . $play[$i]['bz'] . '\'';
                $msql->query("select * from `{$tb_play}` where pid='" . $play[$i]['pid'] . '\'');
                $msql->next_record();
                $key = '';
                if ($config['libkey'] == 1) {
                    $key = encode(array(
                        $gid,
                        $msql->f('pid'),
                        $tid,
                        $userid,
                        time(),
                        $play[$i]['con']
                    ));
                }
                $sql .= ",gid='{$gid}',pid='" . $msql->f('pid') . '\',cid=\'' . $msql->f('cid') . '\',sid=\'' . $msql->f('sid') . '\',bid=\'' . $msql->f('bid') . '\',time=\'' . time() . '\'';
                $sql .= ',qishu=\'' . $config['thisqishu'] . "',z='9',bs=1,abcd='{$abcd}',ab='{$ab}',sv='" . $_SESSION['sv'] . '\'' . ",ip='$ip',code='$key'";
                $msql->query($sql);
                $play[$i]['cg'] = 1;
            }
            echo json_encode($play);
            unset($play);
            die;
        }
        $_SESSION['exe'] = time();
        $je              = 0;
        $msql->query("select kmoney,money,layer,fid1,ifexe,pself,status,pan,plwarn from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        if ($msql->f('status') != 1) {
            die;
        }
        if ($ab !== 'A' & $ab !== 'B') {
            $ab = 'A';
        }
        $uabcd = json_decode($msql->f('pan'), true);
        if (!in_array($abcd, $uabcd)) {
            $abcd = $uabcd[0];
        }
        $kmoney   = $msql->f('kmoney');
        $money    = $msql->f('money');
        $plwarn   = $msql->f('plwarn');
        $thelayer = $msql->f('layer');
        $fid1     = $msql->f('fid1');
        $ifexe    = $msql->f('ifexe');
        $pself    = $msql->f('pself');
        if ($thelayer > 1) {
            $msql->query("select ifexe,pself from `{$tb_user}` where userid='{$fid1}'");
            $msql->next_record();
            $ifexe = $msql->f('ifexe');
            $pself = $msql->f('pself');
        }
        $u        = getfid($userid);
        $zc       = getflyzc($userid, $u, $thelayer, $gid);
        $czc      = count($zc) - 1;
        $peilvcha = array();
        $msql->query("delete from `{$tb_libu}` where  userid='{$userid}'");
        $msql->query("select closetime,opentime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
        $msql->next_record();
        $config['closetime'] = strtotime($msql->f('closetime'));
        if ($thelayer == 1) {
            $config['times']['c'] = 0;
            if ($config['uppanstatus'] == 1) {
                $config['panstatus'] = 1;
            } else {
                $config['panstatus'] = 0;
            }
            if ($config['upotherstatus'] == 1) {
                $config['otherstatus'] = 1;
            } else {
                $config['otherstatus'] = 0;
            }
            if ((time() - strtotime($msql->f('opentime'))) < 0 & $config['autoopenpan'] == 1) {
                $config['panstatus']   = 0;
                $config['otherstatus'] = 0;
            }
        } else {
            if ((time() - strtotime($msql->f('opentime')) - $config['times']['o']) < 0 & $config['autoopenpan'] == 1) {
                $config['panstatus']   = 0;
                $config['otherstatus'] = 0;
            }
        }
        $tid    = setuptid();
        $ytparr = array();
		if(date("His")<=str_replace(':','',$config['editstart'])){
		     $dates = sqldate(time()-86400);
		}else{
		     $dates = sqldate(time());
		}
        for ($i = 0; $i < $cp; $i++) {
            $msql->query('select sum(je*zc' . $thelayer . "/100) from `{$tb_lib}` where pid='" . $play[$i]['pid'] . '\' and uid' . $thelayer . "='{$userid}' and  qishu='" . $config['thisqishu'] . "' union select sum(je) from `{$tb_lib}` where userid='{$userid}'  and qishu='" . $config['thisqishu'] . '\'  and  pid=\'' . $play[$i]['pid'] . '\' and xtype!=2');
            $msql->next_record();
            $maxfei = $msql->f(0);
            $msql->next_record();
            $yifei = $msql->f(0);
            if ($yifei + $play[$i]['je'] > $maxfei) {
                $play[$i]['err'] = '补货额已超占成金额';
                continue;
            }
            $tid++;
            if (in_array($play[$i]['pid'], $ytparr) & !is_array($play[$i]['con'])) {
                $play[$i]['err']  = "重复投注!";
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            $ytparr[] = $play[$i]['pid'];
            $msql->query("select bid,sid,cid,peilv1,peilv2,ifok,name,pl from `{$tb_play}` where gid='{$gid}' and pid='" . $play[$i]['pid'] . '\'');
            $msql->next_record();
            $bid     = $msql->f('bid');
            $sid     = $msql->f('sid');
            $cid     = $msql->f('cid');
            $pname   = $msql->f('name');
            $ifok    = $msql->f('ifok');
            $pl      = $msql->f('pl');
            $peilv1  = 0;
            $peilv2  = 0;
            $peilv1s = 0;
            $peilv2s = 0;
            if ($pname == '過關') {
                $play[$i]['con'] = implode('-', $play[$i]['con']);
                $tsql->query('select sum(je*zc' . $thelayer . "/100) from `{$tb_lib}` where gid='$gid' and pid='" . $play[$i]['pid'] . '\' and uid' . $thelayer . "='{$userid}' and  qishu='" . $config['thisqishu'] . '\'  and content=\'' . $play[$i]['con'] . "' union select sum(je) from `{$tb_lib}` where gid='$gid' and userid='{$userid}'  and qishu='" . $config['thisqishu'] . '\'  and  pid=\'' . $play[$i]['pid'] . '\' and xtype!=2 and content=\'' . $play[$i]['con'] . '\'');
                $tsql->next_record();
                $maxfei = $tsql->f(0);
                $tsql->next_record();
                $yifei = $tsql->f(0);
                if ($yifei + $play[$i]['je'] > $maxfei) {
                    $play[$i]['err'] = '补货额已超占成金额';
                    continue;
                }
                $arr = json_decode($play[$i]['bz'], true);
                if ($thelayer > 1 & $ifexe == 1 & $pself == 1) {
                    $tb   = $tb_play_user;
                    $uwhi = " and userid='$fid1' ";
                } else {
                    $tb   = $tb_play;
                    $uwhi = "";
                }
                $peilv1 = 1;
                foreach ($arr as $key => $val) {
                    $sql = "select peilv1 from `{$tb}` where gid='{$gid}' $uwhi and sid='" . $val['sid'] . '\'  and cid=\'' . $val['cid'] . '\'  and pid=\'' . $val['pid'] . '\'';
                    $fsql->query($sql);
                    $fsql->next_record();
                    $peilv1 *= ($fsql->f('peilv1') - $config['cs']['ggpeilv']);
                }
            } else if ($msql->f('pl') != '') {
                $tsql->query('select sum(je*zc' . $thelayer . "/100) from `{$tb_lib}` where gid='$gid' and  pid='" . $play[$i]['pid'] . '\' and uid' . $thelayer . "='{$userid}' and  qishu='" . $config['thisqishu'] . '\'  and content=\'' . implode('-', $play[$i]['con']) . "' union select sum(je) from `{$tb_lib}` where  gid='$gid' and userid='{$userid}'  and qishu='" . $config['thisqishu'] . '\'  and  pid=\'' . $play[$i]['pid'] . '\' and xtype!=2 and content=\'' . implode('-', $play[$i]['con']) . '\'');
                $tsql->next_record();
                $maxfei = $tsql->f(0);
                $tsql->next_record();
                $yifei = $tsql->f(0);
                if ($yifei + $play[$i]['je'] > $maxfei) {
                    $play[$i]['err'] = '补货额已超占成金额';
                    continue;
                }
                if ($gid == 100) {
                    $duo = getduoarr($pname);
                } else {
                    $duo = getduoarrssuser($gid, $pname);
                }
                $pl = json_decode($pl, true);
                if ($thelayer > 1 & $ifexe == 1) {
                    $fsql->query("select pl from `$tb_play_user` where  userid='$fid1' and gid='$gid' and pid='" . $play[$i]['pid'] . "' ");
                    $fsql->next_record();
                    $pls = json_decode($fsql->f('pl'), true);
                }
                if (strpos($pname, '字组合')) {
                    $pcl = count($play[$i]['con']);
                    if ($pcl == 2) {
                        $pkey = 0;
                        if ($play[$i]['con'][0] != $play[$i]['con'][1]) {
                            $pkey = 1;
                        }
                    } else if ($pcl == 3) {
                        if ($play[$i]['con'][0] == $play[$i]['con'][1] & $play[$i]['con'][0] == $play[$i]['con'][2]) {
                            $pkey = 0;
                        } else if ($play[$i]['con'][0] != $play[$i]['con'][1] & $play[$i]['con'][0] != $play[$i]['con'][2] & $play[$i]['con'][1] != $play[$i]['con'][2]) {
                            $pkey = 2;
                        } else {
                            $pkey = 1;
                        }
                    } else {
                        exit;
                    }
                    $key    = rduokey($duo, $play[$i]['con'][0]);
                    $peilv1 = $pl[$pkey][$key];
                    foreach ($play[$i]['con'] as $val) {
                        $key = rduokey($duo, $val);
                        if ($pl[$pkey][$key] < $peilv1) {
                            $peilv1 = $pl[$pkey][$key];
                        }
                    }
                    if ($thelayer > 1 & $ifexe == 1) {
                        $key     = rduokey($duo, $play[$i]['con'][0]);
                        $peilv1s = $pls[$pkey][$key];
                        foreach ($play[$i]['con'] as $val) {
                            $key = rduokey($duo, $val);
                            if ($pls[$pkey][$key] < $peilv1s) {
                                $peilv1s = $pls[$pkey][$key];
                            }
                        }
                    }
                } else if (strpos($pname, '字定位') | $pname == '选前三直选' | $pname == '选三前直' | $pname == '选前二直选') {
                    if ($pname == '选前二直选' | $pname == '选前三直选') {
                        $keyfunc = "rduokeysyxw";
                    } else if ($pname == '选三前直') {
                        $keyfunc = "rduokeyklsf";
                    } else {
                        $keyfunc = "rduokeydw";
                    }
                    $key    = call_user_func($keyfunc, $duo, $play[$i]['con'][0], 0);
                    $peilv1 = $pl[0][$key];
                    foreach ($play[$i]['con'] as $keyc => $val) {
                        $key = call_user_func($keyfunc, $duo, $val, $keyc);
                        if ($pl[0][$key] < $peilv1) {
                            $peilv1 = $pl[0][$key];
                        }
                    }
                    if ($thelayer > 1 & $ifexe == 1) {
                        $key     = call_user_func($keyfunc, $duo, $play[$i]['con'][0], 0);
                        $peilv1s = $pls[0][$key];
                        foreach ($play[$i]['con'] as $keyc => $val) {
                            $key = call_user_func($keyfunc, $duo, $val, $keyc);
                            if ($pls[0][$key] < $peilv1s) {
                                $peilv1s = $pls[0][$key];
                            }
                        }
                    }
                }
				$play[$i]['con'] = implode('-', $play[$i]['con']);
            } else {
                $play[$i]['con'] = '';
                $tsql->query('select sum(je*zc' . $thelayer . "/100) from `{$tb_lib}` where gid='$gid' and  pid='" . $play[$i]['pid'] . '\' and uid' . $thelayer . "='{$userid}' and  qishu='" . $config['thisqishu'] . "' union select sum(je) from `{$tb_lib}` where userid='{$userid}'  and qishu='" . $config['thisqishu'] . '\'  and  pid=\'' . $play[$i]['pid'] . '\' and xtype!=2');
                $tsql->next_record();
                $maxfei = $tsql->f(0);
                $tsql->next_record();
                $yifei = $tsql->f(0);
                if ($yifei + $play[$i]['je'] > $maxfei) {
                    $play[$i]['err'] = '补货额已超占成金额';
                    continue;
                }
                $peilv1 = $msql->f('peilv1');
                $peilv2 = $msql->f('peilv2');
                if ($thelayer > 1 & $ifexe == 1) {
                    $fsql->query("select peilv1,peilv2 from `{$tb_play_user}` where gid='{$gid}' and  pid='" . $play[$i]['pid'] . "' and userid='{$fid1}' ");
                    $fsql->next_record();
                    $peilv1s = $fsql->f('peilv1');
                    $peilv2s = $fsql->f('peilv2');
                }
            }
            if ($config['panstatus'] == 0 | (($bid != 23378685 | !is_numeric($pname)) & $config['otherstatus'] == 0)) {
                $play[$i]['err']  = '已关盘1';
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            $time = time();
            if ($bid == 23378685 & is_numeric($pname)) {
                if ($time > ($config['closetime'] - $config['times']['c'])) {
                    $play[$i]['err']  = "已关盘2";
                    $play[$i]['cg']   = 0;
                    $play[$i]['goon'] = 0;
                    continue;
                }
            } else {
                if ($time > ($config['closetime'] - $config['otherclosetime'] - $config['times']['c'])) {
                    $play[$i]['err']  = "已关盘2";
                    $play[$i]['cg']   = 0;
                    $play[$i]['goon'] = 0;
                    continue;
                }
            }
            if ($ifok != 1) {
                $play[$i]['err']  = '已关盘3';
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            if ($tmpcid != $cid) {
				$fsql->query("select ftype,dftype from `$tb_class` where gid='$gid' and cid='$cid'");
				$fsql->next_record();
                $ftype = $fsql->f('ftype');
				$dftype = $fsql->f('dftype');
                $abcha   = 0;
                $abcdcha = 0;
                $tmpabcd = 0;
                $tmpab   = 0;
                if ($config['pan'][$dftype]['ab'] == 1) {
                    if ($ab == 'B') {
                        $abcha = $config['patt'][$ftype]['ab'];
                    }
                    $tmpab = $ab;
                }
                if ($config['pan'][$dftype]['abcd'] == 1) {
                    if ($abcd != 'A') {
                        $abcdcha = $config['patt'][$ftype][strtolower($abcd)];
                    }
                    $tmpabcd = $abcd;
                }
                $points = getpoints8($dftype, $tmpabcd, $tmpab, $userid,$config["fenlei"]);
                $sqle        = ',points=\'' . $points . '\'';
                $tmppeilvcha = 0;
                for ($j = 0; $j < $czc; $j++) {
                    $sqle .= ',zc' . $j . '=\'' . $zc[$j]['zc'] . '\'';
                    if ($j > 0) {
                        $arr = getzcs8($ftype, $u[$j],$config["fenlei"]);
                        $tmppeilvcha += $arr['peilvcha'];
                        $lowpeilv[$j] = $arr['lowpeilv'];
                        if ($pname != '過關') {
                            $peilvcha[$j] = $tmppeilvcha + $abcdcha - $abcha;
                        }
                        $points       = getpoints8($dftype, $tmpabcd, $tmpab, $u[$j],$config["fenlei"]);
                        $sqle .= ',points' . $j . '=\'' . $points . '\'';
                        $sqle .= ',uid' . $j . '=\'' . $u[$j] . '\'';
                        if ($j == 1 & $ifexe == 1 & $pself == 1) {
                            $tmppeilvcha = 0;
                        }
                    }
                }
                $arr = getzcs8($ftype, $userid,$config["fenlei"]);
                $tmppeilvcha += $arr['peilvcha'];
                if ($pname != '過關') {
                    $peilvchax = $tmppeilvcha + $abcdcha - $abcha;
                }
                $lowpeilvx = $arr['lowpeilv'];
				$arr = getjes8($dftype, $userid,$config["fenlei"]);
                $cmaxjex   = $arr['cmaxje'];
                $maxjex    = $arr['maxje'];
                $tmpcid    = $cid;
            }
            if ($play[$i]['je'] > $maxjex) {
                $play[$i]['err']  = '超单注限额！';
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            $fsql->query("select sum(je) from `{$tb_lib}` where gid='{$gid}' and pid='" . $play[$i]['pid'] . "' and    userid='{$userid}' and qishu='" . $config['thisqishu'] . '\'  ');
            $fsql->next_record();
            if ($fsql->f(0) + $play[$i]['je'] > $cmaxjex) {
                $play[$i]['err']  = '超单场限额！';
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            $tmppeilv  = 0;
            $tmppeilv2 = 0;
            if ($play[$i]['con'] == '') {
                if ($thelayer > 1 & $ifexe == 1 & $pself == 1) {
                    $tmppeilv = moren($peilv1s - $peilvchax, $lowpeilvx);
                } else {
                    $tmppeilv = moren($peilv1 - $peilvchax - $peilv1s, $lowpeilvx);
                }
                if (p3($tmppeilv) < p3($play[$i]['peilv1'])) {
                    $play[$i]['err']  = '赔率改变！';
                    $play[$i]['cgs']  = 1;
                    $play[$i]['goon'] = 0;
                    //continue;
                }
            } else {
                if (is_array($pl)) {
                    if ($thelayer > 1 & $ifexe == 1 & $pself == 1) {
                        $tmppeilv  = moren($peilv1s - $peilvchax, $lowpeilvx);
                        $tmppeilv2 = moren($peilv2s - $peilvchax, $lowpeilvx);
                    } else {
                        $tmppeilv  = moren($peilv1 - $peilvchax - $peilv1s, $lowpeilvx);
                        $tmppeilv2 = moren($peilv2 - $peilvchax - $peilv2s, $lowpeilvx);
                    }
                } else {
                    $tmppeilv = $peilv1;
                }
            }
            $play[$i]['peilv1'] = $tmppeilv;
            if ($cp > 5) {
                $sql = " insert into `{$tb_libu}` ";
            } else {
                $sql = " insert into `{$tb_lib}` ";
            }
            $key = '';
            if ($config['libkey'] == 1) {
                $key = encode(array(
                    $gid,
                    $play[$i]['pid'],
                    $tid,
                    $userid,
                    time(),
                    $play[$i]['con']
                ));
            }
            $sql .= " set dates='$dates',gid='{$gid}',qishu='" . $config['thisqishu'] . "',tid='{$tid}',userid='{$userid}',bid='{$bid}',sid='{$sid}',cid='{$cid}',pid='" . $play[$i]['pid'] . "',abcd='{$abcd}',ab='{$ab}',content='" . $play[$i]['con'] . '\',time=\'' . time() . '\',je=\'' . $play[$i]['je'] . '\',xtype=\'1\',z=\'9\',bs=1,peilv1=\'' . $tmppeilv . '\',peilv2=\'' . $tmppeilv2 . '\',bz=\'' . $play[$i]['bz'] . '\',sv=\'' . $_SESSION['sv'] . '\'' . ",ip='$ip',code='$key'";
            $sql .= $sqle;
            for ($j = 1; $j < $czc; $j++) {
                if ($pname == "過關") {
                    $sql .= ",peilv1{$j}='" . $peilv1 . "',peilv2{$j}='0'";
                } else {
                    if (is_array($pl)) {
                        if ($thelayer > 1 & $ifexe == 1 & $pself == 1 & $j > 1) {
                            $sql .= ",peilv1{$j}='" . moren($peilv1s - $peilvcha[$j], $lowpeilv[$j]) . "',peilv2{$j}='" . moren($peilv2s - $peilvcha[$j], $lowpeilv[$j]) . "'";
                        } else {
                            if ($j > 1) {
                                $sql .= ",peilv1{$j}='" . moren($peilv1 - $peilvcha[$j] - $peilv1s, $lowpeilv[$j]) . "',peilv2{$j}='" . moren($peilv2 - $peilvcha[$j] - $peilv2s, $lowpeilv[$j]) . "'";
                            } else {
                                $sql .= ",peilv1{$j}='" . moren($peilv1 - $peilvcha[$j], $lowpeilv[$j]) . "',peilv2{$j}='" . moren($peilv2 - $peilvcha[$j], $lowpeilv[$j]) . "'";
                            }
                        }
                    } else {
                        if ($thelayer > 1 & $ifexe == 1 & $pself == 1 & $j > 1) {
                            $sql .= ",peilv1{$j}='" . moren($peilv1s - $peilvcha[$j], $lowpeilv[$j]) . "',peilv2{$j}='0'";
                        } else {
                            if ($j > 1) {
                                $sql .= ",peilv1{$j}='" . moren($peilv1 - $peilvcha[$j] - $peilv1s, $lowpeilv[$j]) . "',peilv2{$j}='0'";
                            } else {
                                $sql .= ",peilv1{$j}='" . moren($peilv1 - $peilvcha[$j], $lowpeilv[$j]) . "',peilv2{$j}='0'";
                            }
                        }
                    }
                }
            }
            if ($msql->query($sql)) {
                $play[$i]['cg'] = 1;
                $je += $play[$i]['je'];
                $money -= $play[$i]['je'];
                $play[$i]['goon'] = 0;
            } else {
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
            }
        }
        if ($cp > 5) {
            $msql->query("insert into `$tb_lib` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `$tb_libu` where userid='$userid' order by id");
            $msql->query("delete from x_libu where userid='$userid'");
        }
        echo json_encode($play);
        unset($play);
        break;
}