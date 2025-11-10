<?php

include '../data/comm.inc.php';
include '../data/uservar.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/userfunc.php';
include '../include.php';
include './checklogin.php';
switch ($_REQUEST['xtype']) {
    case "baouser":
        $ttype = $_POST['ttype'];
        $start = $_POST['date'];
        $end = $_POST['date'];
        $game = $_POST['game'];
        $game = explode('|', $game);
        array_pop($game);
        $gstr = '(' . implode(',', $game) . ')';
        //$start = strtotime($start . ' ' . $config['editend']);
        //$end   = strtotime($end . ' ' . $config['editstart']) + 86400;
        //$start =sqltime($start);
        //$end =sqltime($end);
        $whi = " and z!=9  and bs=1 and dates>='{$start}' and dates<='{$end}'  ";
        $join = " from `{$tb_lib}`  where  userid='{$userid}' and gid in {$gstr} ";
        $page = $_POST['page'];
        $psize = $config["psize2"];
        $msql->query("select count(id) {$join} {$whi} ");
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : ($rcount - $rcount % $psize) / $psize + 1;
        if (!is_numeric($page) | $page < 1 | $page > $pcount) {
            $page = 1;
        }
        if (!is_numeric($psize)) {
            $psize = 100;
        }
        $msql->query("select *  {$join} {$whi}  order by gid,time desc,id desc limit " . ($page - 1) * $psize . "," . $psize);
        $tz = array();
        $i = 0;
        $je = 0;
        $points = 0;
        $res = 0;
        $tmp = array();
        while ($msql->next_record()) {
            if ($gid != $msql->f('gid') & $i > 0) {
                $tz[$i]['je'] = $je;
                $tz[$i]['points'] = $points;
                $tz[$i]['res'] = $res;
                $je = 0;
                $res = 0;
                $points = 0;
                $i++;
            }
            $tz[$i]['xtype'] = transxtype($msql->f('xtype'));
            $tz[$i]['tid'] = $msql->f('tid');
            $tz[$i]['time'] = substr($msql->f('time'), 5);
            if ($tmp['g' . $msql->f('gid')] == '') {
                $fsql->query("select gname,mnum,class from `{$tb_game}` where gid='" . $msql->f('gid') . "'");
                $fsql->next_record();
                $tmp['g' . $msql->f('gid')] = $fsql->f('gname');
                $tmp['gc' . $msql->f('gid')] = $fsql->f('class');
                for ($j = 1; $j <= $fsql->f('mnum'); $j++) {
                    if ($j > 1) {
                        $tmp['gms' . $msql->f('gid')] .= ",";
                    }
                    $tmp['gms' . $msql->f('gid')] .= "m" . $j;
                }
            }
            $tz[$i]['gid'] = $tmp['g' . $msql->f('gid')];
            $tz[$i]['style'] = $tmp['gc' . $msql->f('gid')];
            $tz[$i]['gids'] = $msql->f('gid');
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
            $tz[$i]['qishu'] = $msql->f('qishu');
            if ($tz[0]['kj']['g' . $msql->f('gid') . $msql->f('qishu')] == '') {
                $rs = $fsql->arr("select kjtime," . $tmp["gms" . $msql->f('gid')] . " from `{$tb_kj}` where gid='" . $msql->f('gid') . "' and  qishu='" . $msql->f('qishu') . "' ", 0);
                $kjtime = " @ " . substr($rs[0][0], -8);
                array_splice($rs[0], 0, 1);
                $tz[0]['kj']['g' . $msql->f('gid') . $msql->f('qishu')] = implode('-', $rs[0]) . $kjtime;
            }
            $tz[$i]['ab'] = $msql->f('ab');
            $tz[$i]['abcd'] = '@' . $msql->f('abcd');
            if ($msql->f('z') == '3') {
                $tz[$i]['peilv'] = (double) $msql->f('peilv2');
            } else {
                $tz[$i]['peilv'] = (double) $msql->f('peilv1');
            }
            $tz[$i]['points'] = pr2($msql->f('je') * $msql->f('points') / 100);
            $tz[$i]['con'] = $msql->f('content');
            $tz[$i]['je'] = $msql->f('je');
            $tz[$i]['z'] = $msql->f('z');
            if ($msql->f('z') == 1) {
                $tz[$i]['zhong'] = pr2($msql->f('peilv1') * $tz[$i]['je']);
            } else {
                if ($msql->f('z') == 2 | $msql->f('z') == 7) {
                    $tz[$i]['zhong'] = $tz[$i]['je'];
                    $tz[$i]['points'] = 0;
                } elseif ($msql->f('z') == 3) {
                    $tz[$i]['zhong'] = pr2($msql->f('peilv2') * $tz[$i]['je']);
                } else {
                    if ($msql->f('z') == 5) {
                        $tz[$i]['zhong'] = pr2($msql->f('prize'));
                    } else {
                        $tz[$i]['zhong'] = 0;
                    }
                }
            }
            $je += $tz[$i]['je'];
            $points += $tz[$i]['points'];
            $gid = $msql->f('gid');
            $i++;
        }
        $tz[$i]['je'] = $je;
        $tz[$i]['points'] = $points;
        $tz[$i]['res'] = $res;
        $tzs = array("tz" => $tz, 'pcount' => $pcount);
        echo json_encode($tzs);
        unset($tz);
        unset($tzs);
        break;
   case "show":
        $msql->query("SHOW TABLES LIKE  '%total%'");
        $msql->next_record();
        if ($msql->f(0) == 'x_lib_total') {
            //$tb_lib = "x_lib_total";
            $bigdata = 1;
        }
		$merge = $config['merge'];
		if ($merge==0){
	    $sqlmerge = " and gid='$gid'"; 
		}else{
		 $sqlmerge = ""; 
			}
	//	var_dump($sqlmerge);
        $ddd = getthisdate();
        $sdate = week();
        $upstart = $sdate[7];
        $upend = $sdate[8];
        $start = $sdate[5];
        $end = $sdate[6];
        $start = strtotime($sdate[5] . ' ' . $config['editend']);
        $ends = strtotime($sdate[5] . ' ' . $config['editstart']);
        $upstart = strtotime($sdate[7] . ' ' . $config['editend']);
        $upend = strtotime($sdate[7] . ' ' . $config['editstart']);
        $upbao = array();
        $t['uzs'] = 0;
        $t['uzje'] = number_format(0,1);
        $t['upoints'] = number_format(0,1);
        $t['urs=0'] = number_format(0,1);
        for ($i = 1; $i <= 7; $i++) {
            $dd = sqldate($upstart + ($i - 1) * 86400);
            $j = $i - 1;
            $upbao[$j]['date'] = $dd;
            $upbao[$j]['week'] = rweek(date("w", strtotime($dd)));
            if ($dd > $ddd) {
                $upbao[$j]['zs'] = 0;
                $upbao[$j]['zje'] = number_format(0,1);
                $upbao[$j]['points'] = number_format(0,1);
                $upbao[$j]['zhong'] = number_format(0,1);
                $upbao[$j]['rs'] = number_format(0,1);
                continue;
            }
            if ($bigdata == 1) {
                if ($ddd == $dd) {
                    $joins = "from `{$tb_lib}` where userid='{$userid}' ";
                } else {
                    $msql->query("show tables like '{$tb_lib}" . '_' . str_replace('-', '', $dd) . "'");
                    if (!$msql->next_record()) {
                        $upbao[$j]['zs'] = 0;
                        $upbao[$j]['zje'] = number_format(0,1);
                        $upbao[$j]['points'] = number_format(0,1);
                        $upbao[$j]['zhong'] = number_format(0,1);
                        $upbao[$j]['rs'] = number_format(0,1);
                        continue;
                    }
                    $joins = "from `{$tb_lib}" . '_' . str_replace('-', '', $dd) . "` where userid='{$userid}' ";
                }
            } else {
                $joins = "from `{$tb_lib}` where userid='{$userid}' and dates='{$dd}' ";
            }
            $msql->query("select count(id),sum(je),sum(je*points/100) {$joins} and z not in(2,7,9) and bs=1 $sqlmerge ");
            $msql->next_record();
            $upbao[$j]['zs'] = $msql->f(0);
            $upbao[$j]['zje'] = $msql->f(1);
            $upbao[$j]['points'] = $msql->f(2);
            $msql->query("select sum(peilv1*je),sum(prize) {$joins} and z=1 and bs=1 $sqlmerge");
            $msql->next_record();
            $upbao[$j]['zhong'] = $msql->f(0) - $msql->f(1);
            /*
            $msql->query("select sum(peilv2*je) $joins and z=3");
            $msql->next_record();
            $bao[$j]['zhong'] += pr2($msql->f(0));
            $msql->query("select sum(prize) $joins and z=5");
            $msql->next_record();
            $bao[$j]['zhong'] += pr2($msql->f(0));
            */
            $upbao[$j]['rs'] = $upbao[$j]['zhong'] + $upbao[$j]['points'] - $upbao[$j]['zje'];

            $t['uzs'] += $upbao[$j]['zs'];
            $t['uzje'] += $upbao[$j]['zje'];
            $t['upoints'] += $upbao[$j]['points'];
            $t['urs'] += $upbao[$j]['rs'];
            
            $upbao[$j]['zje'] = number_format($upbao[$j]['zje'],1);
            $upbao[$j]['points'] = number_format($upbao[$j]['points'],1);
            $upbao[$j]['rs'] = number_format($upbao[$j]['rs'],1);
            $upbao[$j]['rs'] = str_replace(',','', $upbao[$j]['rs']);
        }
        $t['uzje'] = number_format($t['uzje'],1);
        $t['upoints'] = number_format($t['upoints'],1);
        $t['urs'] = number_format($t['urs'],1);
        $t['urs'] = str_replace(',','', $t['urs']);
        $bao = array();
        $t['zs'] = 0;
        $t['zje'] = number_format(0,1);
        $t['points'] = number_format(0,1);
        $t['rs'] = number_format(0,1);
        for ($i = 1; $i <= 7; $i++) {
            $dd = sqldate($start + ($i - 1) * 86400);
            $j = $i - 1;
            $bao[$j]['date'] = $dd;
            $bao[$j]['week'] = rweek(date("w", strtotime($dd)));
            if ($dd > $ddd) {
                $bao[$j]['zs'] = 0;
                $bao[$j]['zje'] = number_format(0,1);
                $bao[$j]['points'] = number_format(0,1);
                $bao[$j]['zhong'] = number_format(0,1);
                $bao[$j]['rs'] = number_format(0,1);
                continue;
            }
            if ($bigdata == 1) {
                if ($ddd == $dd) {
                    $joins = "from `{$tb_lib}` where userid='{$userid}' ";
                } else {
                    $msql->query("show tables like '{$tb_lib}" . '_' . str_replace('-', '', $dd) . "'");
                    if (!$msql->next_record()) {
                        $bao[$j]['zs'] = 0;
                        $bao[$j]['zje'] = number_format(0,1);
                        $bao[$j]['points'] = number_format(0,1);
                        $bao[$j]['zhong'] = number_format(0,1);
                        $bao[$j]['rs'] = number_format(0,1);
                        continue;
                    }
                    $joins = "from `{$tb_lib}" . '_' . str_replace('-', '', $dd) . "` where userid='{$userid}' ";
                }
            } else {
                $joins = "from `{$tb_lib}` where userid='{$userid}' and dates='{$dd}' ";
            }
            $msql->query("select count(id),sum(je),sum(je*points/100) {$joins} and z not in(2,7,9) and bs=1 $sqlmerge");
            $msql->next_record();
            $bao[$j]['zs'] = $msql->f(0);
            $bao[$j]['zje'] = $msql->f(1);
            $bao[$j]['points'] = $msql->f(2);
            $msql->query("select sum(peilv1*je),sum(prize) {$joins} and z=1 and bs=1 $sqlmerge");
            $msql->next_record();
            $bao[$j]['zhong'] = $msql->f(0) - $msql->f(1);
            /*
            $msql->query("select sum(peilv2*je) $joins and z=3");
            $msql->next_record();
            $bao[$j]['zhong'] += pr2($msql->f(0));
            $msql->query("select sum(prize) $joins and z=5");
            $msql->next_record();
            $bao[$j]['zhong'] += pr2($msql->f(0));
            */
            $bao[$j]['rs'] = $bao[$j]['zhong'] + $bao[$j]['points'] - $bao[$j]['zje'];

            $t['zs'] += $bao[$j]['zs'];
            $t['zje'] += $bao[$j]['zje'];
            $t['points'] += $bao[$j]['points'];
            $t['rs'] += $bao[$j]['rs'];
            
            $bao[$j]['zje'] = number_format($bao[$j]['zje'],1);
            $bao[$j]['points'] = number_format($bao[$j]['points'],1);
            $bao[$j]['rs'] = number_format($bao[$j]['rs'],1);
            $bao[$j]['rs'] = str_replace(',','', $bao[$j]['rs']);

        }
        $t['zje'] = number_format($t['zje'],1);
        $t['points'] = number_format($t['points'],1);
        $t['rs'] = number_format($t['rs'] ,1);
        $t['rs'] = str_replace(',','', $t['rs']);
        $tpl->assign("t", $t);
        $tpl->assign("bao", $bao);
        $tpl->assign("upbao", $upbao);
        $tpl->display("baoweek.html");
        break;
}