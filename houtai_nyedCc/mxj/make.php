<?php

include '../data/comm.inc.php';
include '../data/mobivar.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/userfunc.php';
include '../include.php';
include './checklogin.php';
include '../func/jsfunc.php';

// ini_set("display_errors", "On"); 
// error_reporting(E_ALL | E_STRICT);


switch ($_REQUEST['xtype']) {
    case 'show':
        $sdate = week();
        $tpl->assign('sdate', $sdate);
        $tpl->assign('moneytype', $config['moneytype']);
        $msql->query("select kfurl from `{$tb_config}`");
        $msql->next_record();
        $tpl->assign('kfurl', $msql->f('kfurl'));
        $msql->query("select maxmoney,money,kmaxmoney,kmoney,pan,defaultpan,username,name,fastje,fudong,sy,jzkmoney from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
         
        $tpl->assign('kmaxmoney', p1($msql->f('kmaxmoney')));
        $tpl->assign('kmoney', p1($msql->f('kmoney')));
        if ($msql->f('kmaxmoney') == 0) {
            $tpl->assign('kmoneyuse', p1($msql->f('sy') - $msql->f('jzkmoney') - $msql->f('kmoney')));
        } else {
            $tpl->assign('kmoneyuse', p1($msql->f('kmaxmoney') + $msql->f('sy') - $msql->f('jzkmoney') - $msql->f('kmoney')));
        }
        $tpl->assign('maxmoney', p1($msql->f('maxmoney')));
        $tpl->assign('money', p1($msql->f('money')));
        $tpl->assign('moneyuse', p1($msql->f('maxmoney') - $msql->f('money')));
        $tpl->assign('synow', p1($msql->f('sy')));
        $tpl->assign('fudong', $msql->f('fudong'));
        $pan = json_decode($msql->f('pan'), true);
        $cpan = count($pan);
        $tpl->assign('pan', $pan);
        $tpl->assign('cpan', $cpan);
        if ($_SESSION['abcd'] == 'A' | $_SESSION['abcd'] == 'B' | $_SESSION['abcd'] == 'C' | $_SESSION['abcd'] == 'D') {
            $tpl->assign('defaultpan', $_SESSION['abcd']);
        } else {
            $tpl->assign('defaultpan', $msql->f('defaultpan'));
        }
        $tpl->assign('thisqishu', substr($config['thisqishu'], -8));
        $tpl->assign('gname', $config['gname']);
        $gamecs = getgamecs($userid);
        $gamecs = getgamename($gamecs);
        $tpl->assign('gamecs', $gamecs);
        $tpl->assign('gid', $gid);

        //var_dump($config['fenlei']);die;
        if ($config['fenlei'] == 100) {
            $b = gets();
        } else {
            $b = getbh();
        }
        if ($config['fenlei'] == 161) {
            $c = $b[0];
            $b = array($b[0]);
        }
        $tpl->assign('b', $b);
        $tpl->assign('webname', $config['webname']);
        $tpl->assign('title', $config['webname'] . '-' . $msql->f('username') . '[' . $msql->f('name') . ']-' . $config['gname']);
        $tpl->assign('gname', $config['gname']);
        $tpl->assign('class', $config['class']);
        $tpl->assign('kjurl', $config['kjurl']);
        $tpl->assign('fenlei', $config['fenlei']);
        $tpl->assign('fast', $config['fast']);
		$tpl->assign('username', $msql->f('username').'('.$msql->f('defaultpan').'盘)');
        $msql->query("select opentime,closetime,kjtime from `{$tb_kj}` where qishu='" . $config['thisqishu'] . "' and gid='{$gid}'");
        $msql->next_record();
        $time = time();
        if ($config['panstatus'] == 1 & ($time - strtotime($msql->f('opentime')) - $config['times']['o'] > 0 | $config['autoopenpan'] == 0)) {
            $pantime = strtotime($msql->f('closetime')) - $time - $config['userclosetime'] - $config['times']['c'];
        } else {
            $config['panstatus'] = 0;
            $pantime = $time - strtotime($msql->f('opentime')) - $config['times']['o'];
            if ($pantime > 0) {
                $pantime = 0;
            }
        }
        if ($config['otherstatus'] == 1 & ($config['autoopenpan'] == 0 | $time - strtotime($msql->f('opentime')) - $config['times']['o'] > 0)) {
            $othertime = strtotime($msql->f('closetime')) - $time - $config['userclosetime'] - $config['otherclosetime'] - $config['times']['c'];
        } else {
            $config['otherstatus'] = 0;
            $othertime = $time - strtotime($msql->f('opentime')) - $config['times']['o'];
            if ($othertime > 0) {
                $othertime = 0;
            }
        }
        if ($config['autoopenpan'] == 0 | $config['times']['io'] == 0) {
            $pantime = 9999;
            $othertime = 9999;
        }
        $kjtime = strtotime($msql->f('kjtime')) - time();
        $tpl->assign('panstatus', $config['panstatus']);
        $tpl->assign('otherstatus', $config['otherstatus']);
        $tpl->assign('kjtime', $kjtime);
        $tpl->assign('pantime', $pantime);
        $tpl->assign('othertime', $othertime);
        $tpl->assign('wid', $_SESSION['wid']);
        $tpl->assign('pk10num', $config['pk10num']);
        $tpl->assign('pk10ts', $config['pk10ts']);
        $tpl->assign('pk10niu', $config['pk10niu']);
        $tpl->assign('ft', $config['cs']['ft']);
        $fsje = $msql->arr("select je from  `{$tb_fastje}` where userid='{$userid}' order by xsort ", 1);
        if (count($fsje) != 5) {
            $fsje = [['je' => 5], ['je' => 10], ['je' => 20], ['je' => 50], ['je' => 100]];
        }
        //print_r($fsje);
        $tpl->assign('fastje', $fsje);
        if ($config["fenlei"] == 100) {
            $tpl->assign('ma', getma());
        }
        $tpl->assign("app", $_SESSION['app']);
        $tpl->display('makev.html');
        break;
    case 'duolib':
        $pid = $_POST['pid'];
        $msql->query("select fid1,layer,ifexe,pself from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $fid1 = $msql->f('fid1');
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
            $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
            $msql->next_record();
            $duo[0] = getduoarr($msql->f('name'));
            $pl = json_decode($msql->f('pl'), true);
            $pname = $msql->f('name');
            $ftype = transc('ftype', $msql->f('cid'));
        } else {
            if ($ifexe == 0) {
                $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                $msql->next_record();
                $duo[0] = getduoarr($msql->f('name'));
                $pl = json_decode($msql->f('pl'), true);
                $cid = $msql->f('cid');
                $pname = $msql->f('name');
            } else {
                $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                $msql->next_record();
                $duo[0] = getduoarr($msql->f('name'));
                $pl = json_decode($msql->f('pl'), true);
                $cid = $msql->f('cid');
                $pname = $msql->f('name');
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
        }
        $cd = count($duo[0]);
        for ($i = 0; $i < $cd; $i++) {
            $duo[1][$i] = (double) (pr3($pl[0][$i]) - $peilvcha - $config['patt'][$ftype][strtolower($abcd)]);
            if ($pname == '三中二' | $pname == '二中特' | strpos($pname, '字组合')) {
                $duo[2][$i] = (double) (pr3($pl[1][$i]) - $peilvcha - $config['patt'][$ftype][strtolower($abcd)]);
            }
            if (strpos($pname, '2字组合')) {
                $duo[3][$i] = (double) (pr3($pl[2][$i]) - $peilvcha - $config['patt'][$ftype][strtolower($abcd)]);
            }
        }
        echo json_encode($duo);
        break;
    case "getcl":
       //error_reporting(E_ALL);
        $qs = intval($_POST['qs']);
        $type = $_REQUEST['type'];
        if ($type != 'cl' && $type != 'yl') {
            $type = 'cl';
        }
        //echo 'aaaaaaaaaa';
        $qarr = [6, 8, 10, 12];
        if(!in_array($qs, $qarr) && $type == 'cl'){
            $qs = 6;
        }
        $gamecs = getgamecs($userid);
        $msql->query("select fid1,defaultpan from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $abcd = strtolower($msql->f("defaultpan"));
        $fid1 = $msql->f("fid1");
        $fsql->query("select ifexe,pself,wid from `{$tb_user}` where userid='{$fid1}'");
        $fsql->next_record();
        $ifexe = $fsql->f('ifexe');
        $pself = $fsql->f('pself');
        $msql->query("select patt from `{$tb_web}` where wid='" . $fsql->f("wid") . "'");
        $msql->next_record();
        $pattnum = $msql->f("patt");
        $ps = [];
        $tmp = [];
        //error_reporting(E_ALL);
        foreach ($gamecs as $k => $v) {
            $msql->query("select fenlei,gname,thisqishu,userclosetime,autoopenpan,panstatus from `{$tb_game}` where gid='{$v['gid']}'");
            $msql->next_record();
            $v['fenlei'] = $msql->f("fenlei");
            if ($v['fenlei'] != 101 && $v['fenlei'] != 107) {
                continue;
            }
            $v['thisqishu'] = $msql->f("thisqishu");
            $v['autoopenpan'] = $msql->f('autoopenpan');
            $v['panstatus'] = $msql->f('panstatus');
            $v['userclosetime'] = $msql->f('userclosetime');
            $v['gname'] = $msql->f('gname');
            $msql->query("select opentime,closetime from `{$tb_kj}` where gid='{$v['gid']}' and qishu='{$v['thisqishu']}'");
            $msql->next_record();
            $opentime = strtotime($msql->f('opentime'));
            $closetime = strtotime($msql->f('closetime'));
            $time = time();
            $ifok = 0;
            if ($v['panstatus'] == 1 & ($time - strtotime($msql->f('opentime')) > 0 | $v['autoopenpan'] == 0)) {
                if ($closetime - $v['userclosetime'] - $time > 0) {
                    $ifok = 1;
                }
            }
            if ($ifok == 0) {
                continue;
            }
            $closetime = $closetime - $v['userclosetime'] - $time;
            $msql->query("select patt" . $pattnum . " as patt from `{$tb_game}` where gid='{$v['gid']}'");
            $msql->next_record();
            $tmp['g'.$v['gid']]['patt'] = json_decode($msql->f("patt"), true);
            $tmp['g'.$v['gid']]['gname'] = $v['gname'];
            $tmp['g'.$v['gid']]['qishu'] = $v['thisqishu'];
            $tmp['g'.$v['gid']]['closetime'] = $closetime;
            if ($type == 'cl') {
                if ($v['fenlei'] == 107){
                    $p = $msql->arr("select * from `{$tb_play}` where gid='{$v['gid']}' and ifok=1 and zqishu>='{$qs}' and  name in('单','双','大','小','龙','虎','冠亚单','冠亚双','冠亚大','冠亚小','总和单','总和双','总和大','总和小','合数单','合数双','尾大','尾小') order by gid,sid,cid,xsort", 1);
                
                }else{
                    $p = $msql->arr("select * from `{$tb_play}` where gid='{$v['gid']}' and (bid=23378755 or bid=23378763) and ifok=1 and zqishu>'{$qs}' and  name in('单','双','大','小','龙','虎','冠亚单','冠亚双','冠亚大','冠亚小','总和单','总和双','总和大','总和小','合数单','合数双','尾大','尾小') order by gid,sid,cid,xsort", 1);
                }

            } else {
                
                if ($v['fenlei'] == 107) {
                    $p = $msql->arr("select * from `{$tb_play}` where gid='{$v['gid']}' and ifok=1 and buzqishu>20 and ztype=0 and bid!=23378805 order by buzqishu desc", 1);
                } else {
                    $p = $msql->arr("select * from `{$tb_play}` where gid='{$v['gid']}' and bid=23378755 and ifok=1 and buzqishu>20 and ztype=0 order by buzqishu desc", 1);
                }
           
                $carr = [];
                $j = 0;
                $p2 = [];
                foreach ($p as $k1 => $v1) {
                    if (!in_array($v1['cid'], $carr)) {
                        $p2[$j] = $v1;
                        $p2[$j]['fenlei'] = $v['fenlei'];
                        $j++;
                        $carr[] = $v1['cid'];
                    }
                }
                $p = $p2;
                unset($p2);
            }
            if(count($ps)==0){
                $ps = $p;
            }else if(count($p)>0){
                $ps = array_merge($ps,$p);
            }
        }
        if ($type == 'yl') {
            $t = array_column($ps, 'buzqishu');
            array_multisort($t, SORT_DESC, $ps);
            $p2 = [];
            $j = 0;
            foreach ($ps as $k1 => $v1) {
                if($k1>15) continue;
                $p2[$j] = $v1;
                $m = $v1['fenlei']==107 ? randp([$v1['name']],1) : randp([$v1['name']],0);
                $m = implode(',', $m);
                $pp = $msql->arr("select * from `{$tb_play}` where gid='{$v1['gid']}' and sid='{$v1['sid']}' and cid='{$v1['cid']}' and name+0 in($m) ", 1);
               
                $j++;
                $p2[$j] = $pp[0];
                $j++;
                $p2[$j] = $pp[1];
                $j++;
            }
            $ps = $p2;
            unset($p2);
        }else{
            $t = array_column($ps, 'zqishu');
            array_multisort($t, SORT_DESC, $ps);
            $p2 = [];
            $j = 0;
            foreach ($ps as $k1 => $v1) {
                $p2[$j] = $v1;
                $pp = $msql->arr("select * from `{$tb_play}` where gid='{$v1['gid']}' and sid='{$v1['sid']}' and cid='{$v1['cid']}' and pid!='{$v1['pid']}' and name!='和'", 1);               
                $j++;
                $p2[$j] = $pp[0];
                $j++;
            }
            $ps = $p2;
            unset($p2);
        }
       
        $play = [];
        $i = 0;        
        foreach ($ps as $k1 => $v1) {
            $gid = $v1['gid'];
            $g = $tmp['g'.$v1['gid']];
            $play[$i]['gname'] = $g['gname'];
            $play[$i]['gid'] = $gid;
            $play[$i]['qishu'] = $g['qishu'];
            $play[$i]['closetime'] = $g['closetime'];
            $play[$i]['name'] = $v1['name'];
            $play[$i]['zqishu'] = $v1['zqishu'];
            $play[$i]['buzqishu'] = $v1['buzqishu'];            
            $patt = $g['patt'];
            if ($tmp['c' . $v1['gid'] . $v1['cid']] == "") {
                $psql->query("select ftype,name,dftype from `{$tb_class}` where gid='{$v1['gid']}' and cid='{$v1['cid']}'");
                $psql->next_record();
                $tmp['c' . $v1['gid'] . $v1['cid']]['ftype'] = $psql->f('ftype');
                $tmp['c' . $v1['gid'] . $v1['cid']]['cname'] = $psql->f('name');
                $tmp['c' . $v1['gid'] . $v1['cid']]['dftype'] = $psql->f('dftype');
                $tmp['c' . $v1['gid'] . $v1['cid']]['cs'] = getjes($psql->f('ftype'), $userid);
                if ($ifexe == 1 & $pself == 1) {
                    $tmp['c' . $v1['gid'] . $v1['cid']]['peilvcha'] = getuserpeilvcha2($userid, $psql->f('ftype'));
                } else {
                    $tmp['c' . $v1['gid'] . $v1['cid']]['peilvcha'] = getuserpeilvcha($userid, $psql->f('ftype'));
                }
            }
            if ($tmp['s' . $v1['gid'] . $v1['sid']] == "") {
                $tmp['s' . $v1['gid'] . $v1['sid']] = transs8('name', $v1['sid'], $v1['gid']);
            }
            if ($tmp['b' . $v1['gid'] . $v1['bid']] == "") {
                $tmp['b' . $v1['gid'] . $v1['bid']] = transb8('name', $v1['bid'], $v1['gid']);
            }
            $play[$i]['name'] = $v1['name'];
            $play[$i]['bname'] = $tmp['b' . $v1['gid'] . $v1['bid']];
            $play[$i]['sname'] = $tmp['s' . $v1['gid'] . $v1['sid']];
            $play[$i]['cname'] = $tmp['c' . $v1['gid'] . $v1['cid']]['cname'];
            $play[$i]['pid'] = $v1['pid'];
            $play[$i]['ifok'] = $v1['ifok'];
            $play[$i]['dftype'] = $tmp['c' . $v1['gid'] . $v1['cid']]['dftype'];
            $play[$i]['ftype'] = $tmp['c' . $v1['gid'] . $v1['cid']]['ftype'];
            $play[$i]['bid'] = $v1['bid'];
            $play[$i]['sid'] = $v1['sid'];
            $play[$i]['cid'] = $v1['cid'];
            $play[$i]['minje'] = $tmp['c' . $v1['gid'] . $v1['cid']]['cs']['minje'];
            $play[$i]['maxje'] = $tmp['c' . $v1['gid'] . $v1['cid']]['cs']['maxje'];
            if ($abcd == 'a') {
                $play[$i]['peilv1'] = $v1['peilv1'];
            } else {
                $play[$i]['peilv1'] = $v1['peilv1'] - $patt[$tmp['c' . $v1['gid'] . $v1['cid']]['ftype']][$abcd];
            }
            $maxje = $tmp['c' . $v1['gid'] . $v1['cid']]['cs']['maxje'];
            $minje = $tmp['c' . $v1['gid'] . $v1['cid']]['cs']['minje'];
            if ($ifexe == 1) {
                if ($pself == 1) {
                    $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where  userid='{$fid1}' and gid='{$v1['gid']}' and pid='" . $play[$i]['pid'] . '\'');
                    $psql->next_record();
                    $play[$i]['peilv1'] = $psql->f('peilv1');
                    $play[$i]['peilv2'] = $psql->f('peilv2');
                    if ($abcd != 'a') {
                        $play[$i]['peilv1'] -= $patt[$play[$i]['ftype']][$abcd];
                    }
                } else {
                    $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where  userid='{$fid1}' and gid='{$v1['gid']}' and pid='" . $play[$i]['pid'] . '\'');
                    $psql->next_record();
                    $play[$i]['peilv1'] -= $psql->f('peilv1');
                    $play[$i]['peilv2'] -= $psql->f('peilv2');
                }
            }
            $play[$i]['peilv1'] -= $tmp['c' . $v1['gid'] . $v1['cid']]['peilvcha'];
            $play[$i]['peilv1'] = (double) $play[$i]['peilv1'];
            $i++;
        }
        //print_r($play);
        echo json_encode($play,JSON_UNESCAPED_UNICODE);
        break;
   case 'lib':
        if (transuser($userid, 'status') != 1) {
            die;
        }
        $msql->query("select ifok from `{$tb_gamecs}` where userid='{$userid}' and gid='{$gid}'");
        $msql->next_record();
        if ($msql->f('ifok') == 0) {
            die;
        }
        $bid    = $_POST['bid'];
        $sid    = $_POST['sid'];
        $cid    = $_POST['cid'];
        $pid    = $_POST['pid'];
        $ab     = strtoupper($_POST['ab']);
        $abcd   = strtoupper($_POST['abcd']);
        $qishu  = $_POST['qishu'];
        $p      = $_POST['p'];
        $stype  = $_POST['stype'];
        $smtype = $_POST['smtype'];
        if ($ab !== 'A' & $ab !== 'B') {
            $ab = 'A';
        }
        $uabcd = json_decode(transuser($userid, 'pan'), true);
        if (!in_array($abcd, $uabcd)) {
            $abcd = $uabcd[0];
        }
        $_SESSION['abcd'] = $abcd;
        $ab   = strtolower($ab);
        $abcd = strtolower($abcd);
        
        switch ($stype) {
            case "a":
                $play = getpsm($bid, $ab, $abcd, $cid);
                break;
            case "1dw":
                $play = getpsm($bid, $ab, $abcd, $cid);
                break;
            case "gg":
            case "1-6":
                $play = getsmgg($bid, $ab, $abcd, $sid, $stype);
                break;
            case "sm":
                $play = getsm($bid, $ab, $abcd, $sid, $smtype);
                break;
            case "d":
            case "d2":
            if($config["fenlei"]==100){
            $sid  = $bid;
            $bid='';
            $play = getpsme($bid, $ab, $abcd, $sid);
            }else{
            $play = getpsme($bid, $ab, $abcd, $sid);
            }
                break;
            case "b":
                if($config["fenlei"]==100){
                    $sid = $bid;
                    $bid='';
                }
                $play = getpsmd($bid, $ab, $abcd, $cid, $sid);
                // var_dump($play);die;
                break;
            case "15":
            case "610":
            case "110":
            case "105":
            case "108":
                $play = getpaiming($bid, $ab, $abcd, $stype);
                break;
            case "c":
                $play = getpsmc($bid, $ab, $abcd, $cid, $p);
                break;
        }
        
        $cp      = count($play);
        $ftype   = '';
        $peilcha = 0;
        $minje   = 0;
        $maxje   = 0;
        $msql->query("select opentime,closetime from `{$tb_kj}` where gid='{$gid}' and qishu='" . $config['thisqishu'] . '\'');
        $msql->next_record();
        $opentime  = strtotime($msql->f('opentime'));
        $closetime = strtotime($msql->f('closetime'));
        $time      = time();
        $ifok      = 0;
        if ($config['panstatus'] == 1 & (($time - strtotime($msql->f('opentime')) - $config['times']['o']) > 0 | $config['autoopenpan'] == 0)) {
            if ($closetime - $config['userclosetime'] - $time - $config['times']['c'] > 0) {
                $ifok = 1;
            }
        }
        $fid1 = transuser($userid, 'fid1');
        $fsql->query("select ifexe,pself from `{$tb_user}` where userid='{$fid1}'");
        $fsql->next_record();
        $ifexe = $fsql->f('ifexe');
        $pself = $fsql->f('pself');
        $tu    = array();
        $cn    = array(
            "前后和",
            "单双和",
            "四季",
            "五行",
            "中发白",
            "方位"
        );
        if ($pid == 999){
            $pid = $play[0]['pid'];
        }                
        for ($i = 0; $i < $cp; $i++) {
            if($stype=='d2' && $pid==$play[$i]['pid']){
                $play[0]['index'] = $i;
            }
            if ($play[$i]['ftype'] != $ftype) {
                $cs    = getjes($play[$i]['dftype'], $userid);
                $minje = $cs['minje'];
                $maxje = $cs['maxje'];
                if ($ifexe == 1 & $pself == 1) {
                    $peilvcha = getuserpeilvcha2($userid, $play[$i]['ftype']);
                } else {
                    $peilvcha = getuserpeilvcha($userid, $play[$i]['ftype']);
                }
            }
            if ($gid != 100 && $gid != 200) {
                if (($play[$i]['ftype'] == 0 | in_array($play[$i]['cname'], $cn)) & !in_array($play[$i]['cid'], $tu)) {
                    $tu[] = $play[$i]['cid'];
                }
            }
            if ($ifexe == 1) {
                if ($pself == 1) {
                    $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where  userid='{$fid1}' and gid='{$gid}' and pid='" . $play[$i]['pid'] . '\'');
                    $psql->next_record();
                    $play[$i]['peilv1'] = $psql->f('peilv1');
                    $play[$i]['peilv2'] = $psql->f('peilv2');
                    if ($abcd != 'a' & $stype != 'gg') {
                        $play[$i]['peilv1'] -= $config['patt'][$play[$i]['ftype']][$abcd];
                    }
                    if ($ab == 'b') {
                        $play[$i]['peilv1'] += $config['patt'][$play[$i]['ftype']]['ab'];
                    }
                } else {
                    $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where  userid='{$fid1}' and gid='{$gid}' and pid='" . $play[$i]['pid'] . '\'');
                    $psql->next_record();
                    $play[$i]['peilv1'] -= $psql->f('peilv1');
                    $play[$i]['peilv2'] -= $psql->f('peilv2');
                }
            }
            if ($stype != 'gg') {
                $play[$i]['peilv1'] -= $peilvcha;
                $play[$i]['peilv2'] -= $peilvcha;
            }
            $play[$i]['peilv1'] = (double) $play[$i]['peilv1'];
            $play[$i]['peilv2'] = (double) $play[$i]['peilv2'];
            $play[$i]['minje']  = $minje;
            $play[$i]['maxje']  = $maxje;
            if ($ifok == 0) {
                $play[$i]['ifok'] = 0;
            }
            if ($config["fenlei"] == 100 & ($play[$i]['bid'] != 23378685 | !is_numeric($play[$i]['name']))) {
                if (($closetime - $config['userclosetime'] - $time - $config['otherclosetime'] - $config['times']['c']) < 0) {
                    $play[$i]['ifok'] = 0;
                }
            }
            $ftype = $play[$i]['ftype'];
        }
        
        if ($stype == 'd2') {   
           if(!isset($play[0]['index'])){
                $play[0]['index'] = 0;
           }        
            if ($pid == 999)
                $pid = $play[0]['pid'];
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
                if($config['fenlei'] = 100){
                      $duo[0] = getduoarr($msql->f('name'));
                }else{
                     $duo[0] = getduoarrssuser($config["fenlei"],$msql->f('name'));
                }
                $pl     = json_decode($msql->f('pl'), true);
                $pname  = $msql->f('name');
            } else {
                if ($ifexe == 0) {
                    $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                    $msql->next_record();
                if($config['fenlei'] = 100){
                      $duo[0] = getduoarr($msql->f('name'));
                }else{
                     $duo[0] = getduoarrssuser($config["fenlei"],$msql->f('name'));
                }
                    $pl     = json_decode($msql->f('pl'), true);
                    $cid    = $msql->f('cid');
                    $pname  = $msql->f('name');
                } else {
                    $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                    $msql->next_record();
                                    if($config['fenlei'] = 100){
                      $duo[0] = getduoarr($msql->f('name'));
                }else{
                     $duo[0] = getduoarrssuser($config["fenlei"],$msql->f('name'));
                }
                    $pl     = json_decode($msql->f('pl'), true);
                    $cid    = $msql->f('cid');
                    $pname  = $msql->f('name');
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
            }
            $cd = count($duo[0]);
            for ($i = 0; $i < $cd; $i++) {
                $duo[1][$i] = (double) (pr3($pl[0][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
                $duo[2][$i] = (double) (pr3($pl[1][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
                $duo[3][$i] = (double) (pr3($pl[2][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
                
                if ($pname == '三中二' | $pname == '二中特') {
                    $duo[2][$i] = (double) (pr3($pl[1][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
                }
            }
            $play[0]['duo']    = $duo;
            $play[0]['pidduo'] = $pid;
        }

        if ($stype == 'gg') {
            $msql->query("select pid,cid,ifok from `{$tb_play}` where gid='{$gid}' and name='過關' ");
            $msql->next_record();
            $play[0]['ifok'] = $msql->f('ifok');
            if (($closetime - $config['userclosetime'] - $time - $config['otherclosetime'] - $config['times']['c']) < 0 | $ifok == 0) {
                $play[0]['ifok'] = 0;
            }
            $play[0]['pid'] = $msql->f('pid');
            $cid            = $msql->f('cid');
            $msql->query("select ftype from `{$tb_class}` where gid='{$gid}' and cid='{$cid}'");
            $msql->next_record();
            $cs               = getzcs($msql->f('ftype'), $userid);
            $play[0]['minje'] = $cs['minje'];
            $play[0]['maxje'] = $cs['maxje'];
        }
        $play[0]['t']  = implode(',', $tu);
        //$tu            = tu($gid, $tu, $config['mnum']);
        //$play[0]['tu'] = $tu;
        echo json_encode($play);
        unset($tu);
        unset($play);
        break;
    case 'getc':
        $bid = $_POST['bid'];
        $msql->query("select * from `{$tb_class}`  where gid='{$gid}' and bid='{$bid}'  order by bid,sid,xsort ");
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
    case 'gets':
        $bid = $_POST['bid'];
        $msql->query("select * from `{$tb_sclass}`  where gid='{$gid}' and bid='{$bid}'  order by bid,xsort ");
        $i = 0;
        $s = array();
        while ($msql->next_record()) {
            $s[$i]['sid'] = $msql->f('sid');
            $s[$i]['name'] = $msql->f('name');
            $i++;
        }
        echo json_encode($s);
        unset($c);
        break;
    case 'getpan':
        $arr = array('panstatus' => $config['panstatus'], 'otherstatus' => $config['otherstatus']);
        echo json_encode($arr);
        unset($arr);
        break;
    case 'upl':
        $qishu = $config['thisqishu'];
        $qs = $_POST['qs'];
        $tu = 1;
        $news = $_POST['news'];
        $m1 = $_POST['m1'];
        $time = sqltime(time());
        $msql->query("select * from `{$tb_kj}` where gid='{$gid}' and m1!='' and closetime<'{$time}' order by gid,qishu desc limit 1");
        $msql->next_record();
        if ($m1 == $msql->f('m1') && $qs == $msql->f('qishu')) {
            echo json_encode(array("A", "B", $news));
            exit;
        }
        $mm = 1;
        if ($msql->f('m1') == '') {
            $mm = 0;
        }
        $ma = array();
        $sx=[];
        for ($i = 1; $i <= $config['mnum']; $i++) {
            $ma[] = $msql->f('m' . $i);
            $config['fenlei']==100 && $sx[] = shengxiaos($msql->f('m'.$i),$msql->f("bml"));
        }
        $mqishu = $msql->f('qishu');
        $fenlei = $config['fenlei'];
        $tu = tu($gid, $config['mnum'], $fenlei, $tu);
        $longl = "";
        $longr = "";
        $zlong = getzlong();
        $bzlong = getbuzlong();
        $news = "";
        $time = time();
        $msql->query("select content from `{$tb_message}` where userid='{$userid}' and {$time}-UNIX_TIMESTAMP(time)<600 order by time desc limit 1");
        if ($msql->next_record()) {
            $news = $msql->f('content');
        }
        $ftlu=[];
        if($config['cs']['ft']==1){
            if(date("H:i:s")<$config['editend']){
                $timestr = " and dates='" . date("Y-m-d", time()-86400) . "' ";
            } else {
                $timestr = " and dates='" . date("Y-m-d") . "' ";
            }
            $kj = $psql->arr("select * from `{$tb_kj}` where gid='{$gid}' {$timestr} and m1!=''  order by qishu desc limit 99", 1);
            $ck = count($kj);
            for($i=0;$i<99;$i++){
                $ftlu[$i] = 0;
            }
            
            for ($i = 0; $i < $ck; $i++) {
                $kjarr = [$kj[$i]['m1'],$kj[$i]['m2'],$kj[$i]['m3'],$kj[$i]['m4'],$kj[$i]['m5'],$kj[$i]['m6'],$kj[$i]['m7'],$kj[$i]['m8'],$kj[$i]['m9'],$kj[$i]['m10']] ;
                $zh = getftzh($kjarr,$config['cs']);
                $kj[$i]['z'] = $zh;
                $zh = $zh % 4 == 0 ? 4 : $zh % 4;  
                $j = $ck-$i-1;
                $k1 = floor($j/10);//2 
                $k2 = $j%10; 
                $ftlu[$k2*10+$k1] = $zh;          
            }
        }
        echo json_encode(array($longl, $longr, $tu, $mm, $ma, $mqishu, $config['gname'], $qs, $zlong, $bzlong, $news,$config['cs']['ft'],$ftlu,$sx));
        unset($longl);
        unset($longr);
        break;
}
function getsmgg($bid, $ab, $abcd, $sid)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid;
    $abcd = low($abcd);
    $time = time();
    $tsql->query("select * from `{$tb_play}` where gid='{$gid}' and bid=(select bid from `{$tb_bclass}` where gid='{$gid}' and instr(name,'正') and instr(name,'特') ) and name<1 order by bid,sid,xsort");
    $i = 0;
    $p = array();
    $cid = 0;
    $sid = 0;
    $csid = 1;
    $ccid = 1;
    $abcd = strtolower($abcd);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0) {
            $csid++;
        }
        if ($cid != $tsql->f('cid') & $cid != 0) {
            $ccid++;
        }
        if ($cid != $tsql->f('cid')) {
            $psql->query("select ftype,name,dftype from `{$tb_class}` where gid='{$gid}' and cid='" . $tsql->f('cid') . '\'');
            $psql->next_record();
            $ftype = $psql->f('ftype');
            $cname = $psql->f('name');
            $dftype = $psql->f('dftype');
        }
        if ($sid != $tsql->f('sid')) {
            $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype'] = $ftype;
        $p[$i]['dftype'] = $dftype;
        $p[$i]['bid'] = $tsql->f('bid');
        $p[$i]['sid'] = $tsql->f('sid');
        $p[$i]['sname'] = $sname;
        $p[$i]['cid'] = $tsql->f('cid');
        $p[$i]['cname'] = $cname;
        $p[$i]['pid'] = $tsql->f('pid');
        $p[$i]['ifok'] = $tsql->f('ifok');
        $p[$i]['name'] = $tsql->f('name');
        $p[$i]['xsort'] = $tsql->f('xsort');
        if ($stype == 'gg') {
            $p[$i]['peilv1'] = (double) ($tsql->f('peilv1') - $config['cs']['ggpeilv']);
            $p[$i]['peilv2'] = (double) ($tsql->f('peilv2') - $config['cs']['ggpeilv']);
        } else {
            $p[$i]['peilv1'] = (double) ($tsql->f('peilv1') - $config['patt'][$ftype][strtolower($abcd)]);
            $p[$i]['peilv2'] = (double) ($tsql->f('peilv2') - $config['patt'][$ftype][strtolower($abcd)]);
        }
        $p[$i]['mp1'] = (double) $tsql->f('mp1');
        $p[$i]['mp2'] = $tsql->f('mp2');
        $cid = $tsql->f('cid');
        $sid = $tsql->f('sid');
        $bid = $tsql->f('bid');
        $p[$i]['cid'] = $tsql->f('cid');
        $p[$i]['sid'] = $tsql->f('sid');
        $p[$i]['bid'] = $tsql->f('bid');
        $i++;
    }
    $p[0]['csid'] = $csid;
    $p[0]['ccid'] = $ccid;
    return $p;
}
function getpaiming($bid, $ab, $abcd, $stype)
{
    global $tb_play, $msql, $psql, $tb_class, $tb_bclass, $gid, $config;
    if ($stype == 15) {
        $sql = "select * from `{$tb_play}` where gid='{$gid}' and ztype=0 and bid in (23378800,23378803,23378807,23378809,23378812)";
    } else {
        if ($stype == 110) {
            $sql = "select * from `{$tb_play}` where gid='{$gid}' and ztype=0 and bid in (23378800,23378803,23378807,23378809,23378812,23378813,23378816,23378819,23378821,23378823)";
        } else {
            if ($stype == 610) {
                $sql = "select * from `{$tb_play}` where gid='{$gid}' and ztype=0 and bid in (23378813,23378816,23378819,23378821,23378823)";
            } else {
                if ($stype == 105) {
                    $sql = "select * from `{$tb_play}` where gid='{$gid}' and ztype=0 and bid!=23378798 ";
                } else {
                    if ($stype == 108) {
                        $sql = "select * from `{$tb_play}` where gid='{$gid}' and ztype=0 and bid!=23378785 ";
                    }
                }
            }
        }
    }
    $sql .= " order by bid,sid,xsort";
    $msql->query($sql);
    $i = 0;
    $p = array();
    $abcd = strtolower($abcd);
    while ($msql->next_record()) {
        $p[$i]['name'] = $msql->f('name');
        if ($cid != $msql->f('cid')) {
            $psql->query("select ftype,name,dftype from `{$tb_class}` where gid='{$gid}' and cid='" . $msql->f('cid') . '\'');
            $psql->next_record();
            $ftype = $psql->f('ftype');
            $cname = $psql->f('name');
            $dftype = $psql->f('dftype');
        }
        if ($sid != $msql->f('sid')) {
            $sname = transs('name', $msql->f('sid'));
        }
        if ($bid != $msql->f('bid')) {
            $bname = transb('name', $msql->f('bid'));
        }
        $p[$i]['bname'] = $bname;
        $p[$i]['sname'] = $sname;
        $p[$i]['cname'] = $cname;
        $p[$i]['ftype'] = $ftype;
        $p[$i]['pid'] = $msql->f('pid');
        $p[$i]['ifok'] = $msql->f('ifok');
        $p[$i]['dftype'] = $dftype;
        $cid = $msql->f('cid');
        $sid = $msql->f('sid');
        $bid = $msql->f('bid');
        $p[$i]['cid'] = $msql->f('cid');
        $p[$i]['sid'] = $msql->f('sid');
        $p[$i]['bid'] = $msql->f('bid');
        if ($abcd == 'a') {
            $p[$i]['peilv1'] = (double) $msql->f('peilv1');
        } else {
            $p[$i]['peilv1'] = (double) ($msql->f('peilv1') - $config['patt'][$ftype][$abcd]);
        }
        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $i++;
    }
    return $p;
}
function getsm($bid, $ab, $abcd, $sid, $smtype)
{
    global $tb_play, $msql, $psql, $tb_class, $gid, $config;
    $fenlei = $config['fenlei'];
    if ($fenlei == 101) {
        $sql = "select * from `{$tb_play}` where gid='{$gid}' and (( bid=23378755 and  name in('单','双','大','小')) or name in('总和单','总和双','总和大','总和小','龙','虎','和') or  bid=23378767) and bid<>26000000 order by bid,sid,cid,xsort ";
    } else {
        if ($fenlei == 103 | $fenlei == 121) {
            $sql = "select * from `{$tb_play}` where gid='{$gid}' and (name in('单','双','大','小','质','合','合单','合双','尾大','尾小','总和单','总和双','总和大','总和小','总和尾大','总和尾小','龙','虎','和')) and bid<>26000000 order by bid,sid,cid,xsort";
        } else {
            if ($fenlei == 151) {
                $sql = "select * from `{$tb_play}` where gid='{$gid}' and bid<>26000000 order by bid,sid,cid,xsort";
            } else {
                if ($fenlei == 161) {
                    $sql = "select * from `{$tb_play}` where gid='{$gid}' and cid<> 23379261 and bid<>26000000 order by xsort";
                } else {
                    if ($fenlei == 107) {
                        $sql = "select * from `{$tb_play}` where gid='{$gid}' and name in('单','双','大','小','质','合','龙','虎','冠亚单','冠亚双','冠亚大','冠亚小') and bid<>26000000 order by bid,sid,xsort";
                    } else {
                        if ($fenlei == 163) {
                            $sql = "select * from `{$tb_play}` where gid='{$gid}' and ( name in('单','双','大','小') or bid='23378858') and bid<>23378857 and bid<>26000000 order by bid,sid,xsort";
                        }
                    }
                }
            }
        }
    }
    $msql->query($sql);
    $i = 0;
    $p = array();
    $abcd = strtolower($abcd);
    while ($msql->next_record()) {
        $p[$i]['name'] = $msql->f('name');
        if ($cid != $msql->f('cid')) {
            $psql->query("select ftype,name,dftype from `{$tb_class}` where gid='{$gid}' and cid='" . $msql->f('cid') . '\'');
            $psql->next_record();
            $ftype = $psql->f('ftype');
            $cname = $psql->f('name');
            $dftype = $psql->f('dftype');
        }
        if ($sid != $msql->f('sid')) {
            $sname = transs('name', $msql->f('sid'));
        }
        if ($bid != $msql->f('bid')) {
            $bname = transb('name', $msql->f('bid'));
        }
        $p[$i]['bname'] = $bname;
        $p[$i]['sname'] = $sname;
        $p[$i]['cname'] = $cname;
        $p[$i]['ftype'] = $ftype;
        $p[$i]['pid'] = $msql->f('pid');
        $p[$i]['ifok'] = $msql->f('ifok');
        $p[$i]['dftype'] = $dftype;
        $cid = $msql->f('cid');
        $sid = $msql->f('sid');
        $bid = $msql->f('bid');
        $p[$i]['cid'] = $msql->f('cid');
        $p[$i]['sid'] = $msql->f('sid');
        $p[$i]['bid'] = $msql->f('bid');
        if ($abcd == 'a') {
            $p[$i]['peilv1'] = (double) $msql->f('peilv1');
        } else {
            $p[$i]['peilv1'] = (double) ($msql->f('peilv1') - $config['patt'][$ftype][$abcd]);
        }
        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $i++;
    }
    return $p;
}
function getbuzlong()
{
    global $gid, $fsql, $tb_play, $tb_class, $config;
    $buz = array();
    if ($gid == 161 | $gid == 162) {
        $fsql->query("select * from `{$tb_play}` where gid='{$gid}' and buzqishu>=2 and cid in (select cid from `{$tb_class}` where gid='{$gid}' and ftype not in (1,2))  order by buzqishu desc,bid,sid,cid,xsort");
    } else {
        $fsql->query("select * from `{$tb_play}` where gid='{$gid}' and buzqishu>=2 and cid in (select cid from `{$tb_class}` where gid='{$gid}' and ftype=0)   and name not in('质','合','总尾质','总尾合','和尾质','和尾合') order by buzqishu desc,bid,sid,cid,xsort");
    }
    $i = 0;
    $tmp = array();
    while ($fsql->next_record()) {
        $pname = $fsql->f('name');
        if ($tmp['b' . $fsql->f('bid')] == '') {
            $tmp['b' . $fsql->f('bid')] = transb('name', $fsql->f('bid'));
        }
        if ($tmp['s' . $fsql->f('sid')] == '') {
            $tmp['s' . $fsql->f('sid')] = transs('name', $fsql->f('sid'));
        }
        if ($tmp['c' . $fsql->f('cid')] == '') {
            $tmp['c' . $fsql->f('cid')] = transc('name', $fsql->f('cid'));
        }
        $buz[$i]['name'] = wf2($config["fenlei"], $tmp['b' . $fsql->f('bid')], $tmp['s' . $fsql->f('sid')], $tmp['c' . $fsql->f('cid')]);
        $buz[$i]['pname'] = $pname;
        $buz[$i]['bname'] = $tmp['b' . $fsql->f('bid')];
        $buz[$i]['qishu'] = $fsql->f('buzqishu');
        $i++;
    }
    return $buz;
}
function getzlong($fenlei=0)
{
    global $gid, $fsql, $tb_play, $tb_class, $config;
    $z = array();
    if ($gid == 161 | $gid == 162) {
        $fsql->query("select * from `{$tb_play}` where gid='{$gid}' and zqishu>=2 and cid in (select cid from `{$tb_class}` where gid='{$gid}' and ftype not in(1,2))  order by zqishu desc,bid,sid,cid,xsort");
    } else {
        $fsql->query("select * from `{$tb_play}` where gid='{$gid}'  and zqishu>=2  and name in('单','双','大','小','龙','虎','冠亚单','冠亚双','冠亚大','冠亚小','总和单','总和双','总和大','总和小','合数单','合数双','尾大','尾小') order by zqishu desc,bid,sid,cid,xsort");
    }
    $i = 0;
    $tmp = array();
    while ($fsql->next_record()) {
        $pname = $fsql->f('name');
        if ($tmp['b' . $fsql->f('bid')] == '') {
            $tmp['b' . $fsql->f('bid')] = transb('name', $fsql->f('bid'));
        }
        if ($tmp['s' . $fsql->f('sid')] == '') {
            $tmp['s' . $fsql->f('sid')] = transs('name', $fsql->f('sid'));
        }
        if ($tmp['c' . $fsql->f('cid')] == '') {
            $tmp['c' . $fsql->f('cid')] = transc('name', $fsql->f('cid'));
        }
        $z[$i]['name'] = wf2($config["fenlei"], $tmp['b' . $fsql->f('bid')], $tmp['s' . $fsql->f('sid')], $tmp['c' . $fsql->f('cid')]);
        $z[$i]['pname'] = $pname;
        $z[$i]['bname'] = $tmp['b' . $fsql->f('bid')];
        $z[$i]['qishu'] = $fsql->f('zqishu');
        $i++;
    }
    return $z;
}
function tu($gid, $mnum, $fenlei, $tt)
{
    global $psql, $tb_kj, $tb_play, $tb_class, $tb_bclass, $tb_sclass, $config;
    $his = date("H:i:s");
    if ($his <= $config['editend']) {
        $timestr = " and kjtime<='" . date("Y-m-d") . " " . $config['editstart'] . "' and kjtime>='" . date("Y-m-d", time() - 86400) . " " . $config['editstart'] . "'";
    } else {
        $timestr = " and kjtime>='" . date("Y-m-d") . " " . $config['editstart'] . "'";
    }
    $sql = '';
    for ($i = 1; $i <= $mnum; $i++) {
        $sql .= ",m" . $i;
    }
    //echo "select qishu$sql from `$tb_kj` where gid='$gid' and m1!='' $timestr $kjtime order by qishu desc";
    $kj = $psql->arr("select qishu{$sql} from `{$tb_kj}` where gid='{$gid}' and m1!='' {$timestr}  order by gid,qishu desc", 1);
    $ck = count($kj);
    $tu = array();
    if ($config['cs']['ft'] == 1 && $tt == 2) {
        $num = explode(',', $config['cs']['ftnum']);
        if (count($num) == 0) {
            $num = ['1'];
        }
        $cn = count($num);
        for ($i = 0; $i < $ck; $i++) {
            $zh = 0;
            for ($j = 0; $j < $cn; $j++) {
                $zh += $kj[$i]['m' . $num[$j]];
            }
            $tu['番'][$i] = $zh % 4 == 0 ? 4 : $zh % 4;
            $tu['单双'][$i] = danshuang($tu['番'][$i]);
            if ($tu['番'][$i] <= 2) {
                $tu['大小'][$i] = '小';
            } else {
                $tu['大小'][$i] = '大';
            }
        }
    } else {
        if ($fenlei == 161) {
            for ($i = 0; $i < $ck; $i++) {
                $tmpbid = 0;
                $zq = 0;
                $zd = 0;
                $he = 0;
                for ($h = 1; $h <= $mnum; $h++) {
                    if ($kj[$i]['m' . $h] <= 40) {
                        $zq++;
                    }
                    if ($kj[$i]['m' . $h] <= 40) {
                        $zd++;
                    }
                    $he += $kj[$i]['m' . $h];
                }
                if ($zq == 10) {
                    $tu["前后和"][$i] = "和";
                } else {
                    if ($zq > 10) {
                        $tu["前后和"][$i] = "前";
                    } else {
                        $tu["前后和"][$i] = "后";
                    }
                }
                if ($zd == 10) {
                    $tu["单双和"][$i] = "和";
                } else {
                    if ($zd > 10) {
                        $tu["单双和"][$i] = "单";
                    } else {
                        $tu["单双和"][$i] = "双";
                    }
                }
                $tu["五行"][$i] = wuhang_161($he);
                $tu["总和单双"][$i] = danshuang($he);
                if ($he == 810) {
                    $tu["总和大小"][$i] = "和";
                } else {
                    if ($he > 810) {
                        $tu["总和大小"][$i] = "大";
                    } else {
                        $tu["总和大小"][$i] = "小";
                    }
                }
            }
        } else {
            if ($fenlei == 107) {
                $bname = array("冠军", "亚军", "第三名", "第四名", "第五名", "第六名", "第七名", "第八名", "第九名", "第十名");
                $ma = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
                for ($j = 0; $j < 10; $j++) {
                    foreach ($ma as $k => $v) {
                        $tu['chu'][$bname[$j]][$k] = 0;
                    }
                }
                $tu["b"] = $bname;
                for ($i = 0; $i < $ck; $i++) {
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'];
                    $tu["冠亚和单双"][$i] = danshuang($he);
                    if ($he <= 11) {
                        $tu["冠亚和大小"][$i] = "小";
                    } else {
                        $tu["冠亚和大小"][$i] = "大";
                    }
                    $tu["冠亚和"][$i] = $he;
                    for ($j = 0; $j < 10; $j++) {
                        $tu[$bname[$j]][$bname[$j]][$i] = $kj[$i]['m' . ($j + 1)];
                        $tu[$bname[$j]]['单双'][$i] = danshuang($kj[$i]['m' . ($j + 1)]);
                        $tu[$bname[$j]]['大小'][$i] = daxiao107($kj[$i]['m' . ($j + 1)]);
                        if (j < 5) {
                            $tu[$bname[$j]]['龙虎'][$i] = longhuhe($kj[$i]['m' . ($j + 1)], $kj[$i]['m' . (10 - $j)]);
                        }
                        foreach ($ma as $k => $v) {
                            if ($kj[$i]['m' . ($j + 1)] == $v) {
                                $tu['chu'][$bname[$j]][$k]++;
                            }
                        }
                    }
                }
                $tu["冠亚和不出"] = getbuz($gid, " and bid=23378805 and ztype=0");
                for ($j = 0; $j < 10; $j++) {
                    $tu['bc'][$bname[$j]] = getbuz($gid, " and bid=(select bid from `{$tb_bclass}` where gid='{$gid}' and name='" . $bname[$j] . "') and ztype=0");
                }
            } else {
                if ($tt > 9) {
                    $name = transb8("name", $tt, $fenlei);
                    $mtype = json_decode(transgame($fenlei, 'mtype'), true);
                    if ($name == "2字和数") {
                        if ($fenlei == 101) {
                            $bname = array("万千", "万百", "万十", "万个", "千百", "千十", "千个", "百十", "百个", "十个");
                            $h = 10;
                        } else {
                            $bname = array("百十", "百个", "十个");
                            $h = 3;
                        }
                    } else {
                        $bname = array("前三", "中三", "后三");
                        $h = 3;
                    }
                    $tu["b"] = $bname;
                    for ($i = 0; $i < $ck; $i++) {
                        for ($j = 0; $j < $h; $j++) {
                            if ($fenlei == 163) {
                                switch ($bname[$j]) {
                                    case "百十":
                                        $he = $kj[$i]['m1'] + $kj[$i]['m2'];
                                        break;
                                    case "百个":
                                        $he = $kj[$i]['m1'] + $kj[$i]['m3'];
                                        break;
                                    case "十个":
                                        $he = $kj[$i]['m2'] + $kj[$i]['m3'];
                                        break;
                                }
                            } else {
                                switch ($bname[$j]) {
                                    case "万千":
                                        $he = $kj[$i]['m1'] + $kj[$i]['m2'];
                                        break;
                                    case "万百":
                                        $he = $kj[$i]['m1'] + $kj[$i]['m3'];
                                        break;
                                    case "万十":
                                        $he = $kj[$i]['m1'] + $kj[$i]['m4'];
                                        break;
                                    case "万个":
                                        $he = $kj[$i]['m1'] + $kj[$i]['m5'];
                                        break;
                                    case "千百":
                                        $he = $kj[$i]['m2'] + $kj[$i]['m3'];
                                        break;
                                    case "千十":
                                        $he = $kj[$i]['m2'] + $kj[$i]['m4'];
                                        break;
                                    case "千个":
                                        $he = $kj[$i]['m2'] + $kj[$i]['m5'];
                                        break;
                                    case "百十":
                                        $he = $kj[$i]['m3'] + $kj[$i]['m4'];
                                        break;
                                    case "百个":
                                        $he = $kj[$i]['m3'] + $kj[$i]['m5'];
                                        break;
                                    case "十个":
                                        $he = $kj[$i]['m4'] + $kj[$i]['m5'];
                                        break;
                                    case "前三":
                                        $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
                                        break;
                                    case "中三":
                                        $he = $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'];
                                        break;
                                    case "后三":
                                        $he = $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                                        break;
                                }
                            }
                            $tu[$bname[$j]]['和单双'][$i] = danshuang($he);
                            if ($name == '3字和数') {
                                if ($he <= 13) {
                                    $tu[$bname[$j]]['和大小'][$i] = "小";
                                } else {
                                    $tu[$bname[$j]]['和大小'][$i] = "大";
                                }
                            }
                            $tu[$bname[$j]]['和尾大小'][$i] = daxiao($he % 10);
                        }
                    }
                } else {
                    if ($fenlei == 101) {
                        $bname = array("第一球", "第二球", "第三球", "第四球", "第五球");
                        $ma = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
                        for ($j = 0; $j < 5; $j++) {
                            foreach ($ma as $k => $v) {
                                $tu['chu'][$bname[$j]][$k] = 0;
                            }
                        }
                        $tu["b"] = $bname;
                        for ($i = 0; $i < $ck; $i++) {
                            $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                            $tu["总和单双"][$i] = danshuang($he);
                            if ($he <= 22) {
                                $tu["总和大小"][$i] = "小";
                            } else {
                                $tu["总和大小"][$i] = "大";
                            }
                            $tu["总尾大小"][$i] = daxiao($he % 10);
                            $tu["龙虎和"][$i] = longhuhe($kj[$i]['m1'], $kj[$i]['m5']);
                            for ($j = 0; $j < 5; $j++) {
                                $tu[$bname[$j]][$bname[$j]][$i] = $kj[$i]['m' . ($j + 1)];
                                $tu[$bname[$j]]['单双'][$i] = danshuang($kj[$i]['m' . ($j + 1)]);
                                $tu[$bname[$j]]['大小'][$i] = daxiao($kj[$i]['m' . ($j + 1)]);
                                foreach ($ma as $k => $v) {
                                    if ($kj[$i]['m' . ($j + 1)] == $v) {
                                        $tu['chu'][$bname[$j]][$k]++;
                                    }
                                }
                            }
                        }
                        for ($j = 0; $j < 5; $j++) {
                            $tu['bc'][$bname[$j]] = getbuz($gid, " and sid=(select sid from `{$tb_sclass}` where gid='{$gid}' and name='" . $bname[$j] . "') and ztype=0");
                        }
                    } else {
                        if ($fenlei == 163) {
                            $bname = array("第一球", "第二球", "第三球");
                            $ma = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
                            for ($j = 0; $j < 3; $j++) {
                                foreach ($ma as $k => $v) {
                                    $tu['chu'][$bname[$j]][$k] = 0;
                                }
                            }
                            $tu["b"] = $bname;
                            for ($i = 0; $i < $ck; $i++) {
                                $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
                                $tu["总和单双"][$i] = danshuang($he);
                                if ($he <= 13) {
                                    $tu["总和大小"][$i] = "小";
                                } else {
                                    $tu["总和大小"][$i] = "大";
                                }
                                $tu["总和尾大小"][$i] = daxiao($he % 10);
                                $tu["龙虎和"][$i] = longhuhe($kj[$i]['m1'], $kj[$i]['m3']);
                                for ($j = 0; $j < 3; $j++) {
                                    $tu[$bname[$j]][$bname[$j]][$i] = $kj[$i]['m' . ($j + 1)];
                                    $tu[$bname[$j]]['单双'][$i] = danshuang($kj[$i]['m' . ($j + 1)]);
                                    $tu[$bname[$j]]['大小'][$i] = daxiao($kj[$i]['m' . ($j + 1)]);
                                    foreach ($ma as $k => $v) {
                                        if ($kj[$i]['m' . ($j + 1)] == $v) {
                                            $tu['chu'][$bname[$j]][$k]++;
                                        }
                                    }
                                }
                            }
                            $tu["总和不出"] = getbuz($gid, " and bid=23378858 and ztype=0");
                            for ($j = 0; $j < 3; $j++) {
                                $tu['bc'][$bname[$j]] = getbuz($gid, " and sid=(select sid from `{$tb_sclass}` where gid='{$gid}' and name='" . $bname[$j] . "') and ztype=0");
                            }
                        } else {
                            if ($fenlei == 103) {
                                $bname = array("第一球", "第二球", "第三球", "第四球", "第五球", "第六球", "第七球", "第八球");
                                $ma = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
                                for ($j = 0; $j < 8; $j++) {
                                    foreach ($ma as $k => $v) {
                                        $tu['chu'][$bname[$j]][$k] = 0;
                                    }
                                }
                                $tu["b"] = $bname;
                                for ($i = 0; $i < $ck; $i++) {
                                    $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'] + $kj[$i]['m6'] + $kj[$i]['m7'] + $kj[$i]['m8'];
                                    $tu["总和单双"][$i] = danshuang($he);
                                    if ($he < 84) {
                                        $tu["总和大小"][$i] = "小";
                                    } else {
                                        if ($he == 84) {
                                            $tu["总和大小"][$i] = "和";
                                        } else {
                                            $tu["总和大小"][$i] = "大";
                                        }
                                    }
                                    $tu["总尾大小"][$i] = daxiao($he % 10);
                                    for ($j = 0; $j < 8; $j++) {
                                        $tu[$bname[$j]][$bname[$j]][$i] = $kj[$i]['m' . ($j + 1)];
                                        $tu[$bname[$j]]['大小'][$i] = daxiao103($kj[$i]['m' . ($j + 1)]);
                                        $tu[$bname[$j]]['单双'][$i] = danshuang($kj[$i]['m' . ($j + 1)]);
                                        if ($j < 4) {
                                            $tu[$bname[$j]]['龙虎'][$i] = longhuhe($kj[$i]['m' . ($j + 1)], $kj[$i]['m' . (8 - $j)]);
                                        }
                                        $tu[$bname[$j]]['尾数大小'][$i] = daxiao($kj[$i]['m' . ($j + 1)] % 10);
                                        $tu[$bname[$j]]['合数单双'][$i] = danshuang(heshu($kj[$i]['m' . ($j + 1)]));
                                        //$tu[$bname[$j]]['方位'][$i] = fangwei($kj[$i]['m' . ($j + 1)]);
                                        //$tu[$bname[$j]]['中发白'][$i] = zhongfabai($kj[$i]['m' . ($j + 1)]);
                                        foreach ($ma as $k => $v) {
                                            if ($kj[$i]['m' . ($j + 1)] == $v) {
                                                $tu['chu'][$bname[$j]][$k]++;
                                            }
                                        }
                                    }
                                }
                                for ($j = 0; $j < 8; $j++) {
                                    $tu['bc'][$bname[$j]] = getbuz($gid, " and bid=(select bid from `{$tb_bclass}` where gid='{$gid}' and name='" . $bname[$j] . "') and ztype=0");
                                }
                            } else {
                                if ($fenlei == 121) {
                                    $bname = array("第1球", "第2球", "第3球", "第4球", "第5球");
                                    $ma = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
                                    for ($j = 0; $j < 5; $j++) {
                                        foreach ($ma as $k => $v) {
                                            $tu['chu'][$bname[$j]][$k] = 0;
                                        }
                                    }
                                    $tu["b"] = $bname;
                                    for ($i = 0; $i < $ck; $i++) {
                                        $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                                        $tu["总和单双"][$i] = danshuang($he);
                                        if ($he < 30) {
                                            $tu["总和大小"][$i] = "小";
                                        } else {
                                            if ($he == 30) {
                                                $tu["总和大小"][$i] = "和";
                                            } else {
                                                $tu["总和大小"][$i] = "大";
                                            }
                                        }
                                        $tu["总和尾大小"][$i] = daxiao($he % 10);
                                        $tu["龙虎"][$i] = longhuhe($kj[$i]['m1'], $kj[$i]['m8']);
                                        for ($j = 0; $j < 5; $j++) {
                                            $tu[$bname[$j]][$bname[$j]][$i] = $kj[$i]['m' . ($j + 1)];
                                            $tu[$bname[$j]]['单双'][$i] = danshuang($kj[$i]['m' . ($j + 1)]);
                                            $tu[$bname[$j]]['大小'][$i] = daxiao103($kj[$i]['m' . ($j + 1)]);
                                            foreach ($ma as $k => $v) {
                                                if ($kj[$i]['m' . ($j + 1)] == $v) {
                                                    $tu['chu'][$bname[$j]][$k]++;
                                                }
                                            }
                                        }
                                    }
                                    for ($j = 0; $j < 5; $j++) {
                                        $tu['bc'][$bname[$j]] = getbuz($gid, " and bid=(select bid from `{$tb_bclass}` where gid='{$gid}' and name='" . $bname[$j] . "') and ztype=0");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $tu;
}

function randp($v,$type){
    $arr=[];
    while(1){
        $t = $type==0 ? rand(0,9) : rand(1,10);
        if(!in_array($t, $v)){
            $arr[] = $t;
            $v[] = $t;
        }
        if(count($arr)==2){
            break;
        }
    }
    return $arr;
}

function shengxiaos($ma, $bml)
{
    $jiazhi = array('甲子', '乙丑', '丙寅', '丁卯', '戊辰', '己巳', '庚午', '辛未', '壬申', '癸酉', '甲戌', '乙亥', '丙子', '丁丑', '戊寅', '己卯', '庚辰', '辛巳', '壬午', '癸未', '甲申', '乙酉', '丙戌', '丁亥', '戊子', '己丑', '庚寅', '辛卯', '壬辰', '癸巳', '甲午', '乙未', '丙申', '丁酉', '戊戌', '己亥', '庚子', '辛丑', '壬寅', '癸卯', '甲辰', '乙巳', '丙午', '丁未', '戊申', '己酉', '庚戌', '辛亥', '壬子', '癸丑', '甲寅', '乙卯', '丙辰', '丁巳', '戊午', '己未', '庚申', '辛酉', '壬戌', '癸亥');
    $index = 0;
    foreach ($jiazhi as $key => $val) {
        if ($val == $bml) {
            $index = $key;
            break;
        }
    }
    $index = $index % 12 + 2;
    $ma = $ma % 12;
    $arr = array('鼠', '牛', '虎', '兔', '龍', '蛇', '馬', '羊', '猴', '雞', '狗', '豬');
    $in= 0 ;
    if ($index >= $ma) {
      $in = $index - $ma;
    } else {
       $in =  $index - $ma + 12;
    }
    if($in>=12) $in -=12;
    return $arr[$in];
}