<?php
include '../data/comm.inc.php';
include '../data/uservar.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/userfunc.php';
include '../include.php';
include './checklogin.php';
include '../func/jsfunc.php';
switch ($_REQUEST['xtype']) {
    case 'show':
        $sdate = week();
        $fenlei = transgame($gid, "fenlei");
        $tpl->assign('sdate', $sdate);
        $msql->query("select username,name,fudong,status from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $tpl->assign('fudong', $msql->f('fudong'));
        $tpl->assign('status', $msql->f('status'));
        $tpl->assign('username', $msql->f('username') . '[' . $msql->f('name') . ']');
        $fsql->query("select * from `{$tb_fastje}` where userid='{$userid}' order by je");
        $i = 0;
        $je = array();
        while ($fsql->next_record()) {
            $je[$i] = $fsql->f('je');
            $i++;
        }
        $tpl->assign('je', $je);
        $tpl->assign('thisqishu', $config['thisqishu']);
        $tpl->assign('gid', $gid);
        $b = getbh();
        if ($fenlei == 161) {
            $c = $b[0];
            $b = array($b[0]);
        }
        //print_r($b);
        $tpl->assign('b', $b);
        $tpl->assign('webname', $config['webname']);
        $tpl->assign('title', $config['webname'] . '-' . $msql->f('username') . '[' . $msql->f('name') . ']-' . $config['gname']);
        $tpl->assign('title', "");
        $tpl->assign('gname', $config['gname']);
        $tpl->assign('fast', $config['fast']);
        $tpl->assign('kjurl', $config['kjurl']);
        $tpl->assign('fastinput', $config['fastinput']);
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
        $kjtime = strtotime($msql->f('kjtime'))-time();
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
        $tpl->assign('fenlei', $fenlei);
        if($_GET['menu']!=''){
           $tpl->assign('menu', $_REQUEST['menu']);
        }else{
          $tpl->assign('menu','0');
        }
        if ($fenlei == 100) {
            $tpl->assign('ma', getma());
        }
        $tpl->display('makes.html');
        break;
    case "skin":
        $_SESSION['skin'] = trim($_POST['skin']);
        break;
    case 'duolib':
        $abcd = $_POST['abcd'];
        $ab = $_POST['ab'];
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
                        $pl[2][$i] -= $pl2[2][$i];
                    }
                }
            }
            $ftype = transc('ftype', $cid);
            if ($ifexe == 1 & $pself == 1) {
                $peilvcha = getuserpeilvcha2s($userid, $ftype, $config['fenlei']);
            } else {
                $peilvcha = getuserpeilvchas($userid, $ftype, $config['fenlei']);
            }
        }
        $cd = count($duo[0]);
        for ($i = 0; $i < $cd; $i++) {
            $duo[1][$i] = (double) (pr3($pl[0][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
            if ($pname == '三中二' | $pname == '二中特' | strpos($pname, '字组合')) {
                $duo[2][$i] = (double) (pr3($pl[1][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
            }
            if (strpos($pname, '2字组合')) {
                $duo[3][$i] = (double) (pr3($pl[2][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
            }
        }
        echo json_encode($duo);
        break;
    case 'duolibss':
        $abcd = $_POST['abcd'];
        $ab = $_POST['ab'];
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
            $duo[0] = getduoarrssuser($config["fenlei"], $msql->f('name'));
            $pl = json_decode($msql->f('pl'), true);
            $pname = $msql->f('name');
            $ftype = transc('ftype', $msql->f('cid'));
        } else {
            if ($ifexe == 0) {
                $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                $msql->next_record();
                $duo[0] = getduoarrssuser($config["fenlei"], $msql->f('name'));
                $pl = json_decode($msql->f('pl'), true);
                $cid = $msql->f('cid');
                $pname = $msql->f('name');
            } else {
                $msql->query("select name,pl,cid from `{$tb_play}` where gid='{$gid}' and pid='{$pid}'");
                $msql->next_record();
                $duo[0] = getduoarrssuser($config["fenlei"], $msql->f('name'));
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
                        $pl[2][$i] -= $pl2[2][$i];
                    }
                }
            }
            $ftype = transc('ftype', $cid);
            if ($ifexe == 1 & $pself == 1) {
                $peilvcha = getuserpeilvcha2s($userid, $ftype, $config['fenlei']);
            } else {
                $peilvcha = getuserpeilvcha($userid, $ftype, $config['fenlei']);
            }
        }
        $cd = count($duo[0]);
        for ($i = 0; $i < $cd; $i++) {
            $duo[1][$i] = (double) (pr3($pl[0][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
            if ($pname == '三中二' | $pname == '二中特' | strpos($pname, '字组合')) {
                $duo[2][$i] = (double) (pr3($pl[1][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
            }
            if (strpos($pname, '2字组合')) {
                $duo[3][$i] = (double) (pr3($pl[2][$i]) - $peilvcha-$config['patt'][$ftype][strtolower($abcd)]);
            }
        }
        echo json_encode($duo);
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
        $bid = $_POST['bid'];
        $sid = $_POST['sid'];
        $cid = $_POST['cid'];
        $pid = $_POST['pid'];
        $ab = strtoupper($_POST['ab']);
        $abcd = strtoupper($_POST['abcd']);
        $qishu = $_POST['qishu'];
        $p = $_POST['p'];
        $stype = $_POST['stype'];
        $smtype = $_POST['smtype'];
        if ($ab !== 'A' & $ab !== 'B') {
            $ab = 'A';
        }
        $uabcd = json_decode(transuser($userid, 'pan'), true);
        if (!in_array($abcd, $uabcd)) {
            $abcd = $uabcd[0];
        }
        $_SESSION['abcd'] = $abcd;
        $ab = strtolower($ab);
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
                $play = getpsme($bid, $ab, $abcd, $sid);
                break;
            case "b":
                $play = getpsmd($bid, $ab, $abcd, $cid, $sid);
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
        $cp = count($play);
        $ftype = '';
        $peilcha = 0;
        $minje = 0;
        $maxje = 0;
        $msql->query("select opentime,closetime from `{$tb_kj}` where gid='{$gid}' and qishu='" . $config['thisqishu'] . '\'');
        $msql->next_record();
        $opentime = strtotime($msql->f('opentime'));
        $closetime = strtotime($msql->f('closetime'));
        $time = time();
        $ifok = 0;
        if ($config['panstatus'] == 1 & ($time - strtotime($msql->f('opentime')) - $config['times']['o'] > 0 | $config['autoopenpan'] == 0)) {
            if ($closetime - $config['userclosetime'] - $time - $config['times']['c'] > 0) {
                $ifok = 1;
            }
        }
        $fid1 = transuser($userid, 'fid1');
        $fsql->query("select ifexe,pself from `{$tb_user}` where userid='{$fid1}'");
        $fsql->next_record();
        $ifexe = $fsql->f('ifexe');
        $pself = $fsql->f('pself');
        for ($i = 0; $i < $cp; $i++) {
            if ($play[$i]['ftype'] != $ftype) {
                $cs = getjes($play[$i]['dftype'], $userid);
                $minje = $cs['minje'];
                $maxje = $cs['maxje'];
                if ($ifexe == 1 & $pself == 1) {
                    $peilvcha = getuserpeilvcha2s($userid, $play[$i]['ftype'], $config['fenlei']);
                } else {
                    $peilvcha = getuserpeilvchas($userid, $play[$i]['ftype'], $config['fenlei']);
                }
            }
            if ($ifexe == 1) {
                if ($pself == 1) {
                    $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where  userid='{$fid1}' and gid='{$gid}' and pid='" . $play[$i]['pid'] . '\'');
                    $psql->next_record();
                    if ($stype == 'gg') {
                        $play[$i]['peilv1'] = $psql->f('peilv1') - $config['cs']['ggpeilv'];
                        $play[$i]['peilv2'] = $psql->f('peilv2') - $config['cs']['ggpeilv'];
                    } else {
                        $play[$i]['peilv1'] = $psql->f('peilv1');
                        $play[$i]['peilv2'] = $psql->f('peilv2');
                    }
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
            $play[$i]['minje'] = $minje;
            $play[$i]['maxje'] = $maxje;
            if ($ifok == 0) {
                $play[$i]['ifok'] = 0;
            }
            if ($gid == 100 & ($play[$i]['bid'] != 23378685 | !is_numeric($play[$i]['name']))) {
                if ($closetime - $config['userclosetime'] - $time - $config['otherclosetime'] - $config['times']['c'] < 0) {
                    $play[$i]['ifok'] = 0;
                }
            }
            $ftype = $play[$i]['ftype'];
        }
        if ($stype == 'gg') {
            $msql->query("select pid,cid,ifok from `{$tb_play}` where gid='{$gid}' and name='過關' ");
            $msql->next_record();
            $play[0]['ifok'] = $msql->f('ifok');
            if ($closetime - $config['userclosetime'] - $time - $config['otherclosetime'] - $config['times']['c'] < 0 | $ifok == 0) {
                $play[0]['ifok'] = 0;
            }
            $play[0]['pids'] = $msql->f('pid');
            $cid = $msql->f('cid');
            $msql->query("select ftype from `{$tb_class}` where gid='{$gid}' and cid='{$cid}'");
            $msql->next_record();
            $cs = getzcs($msql->f('ftype'), $userid);
            $play[0]['minje'] = $cs['minje'];
            $play[0]['maxje'] = $cs['maxje'];
        }
        echo json_encode($play);
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
        $tu = $_POST['tu'];
        $news = $_POST['news'];
        $m1 = $_POST['m1'];
        $time = sqltime(time());
        $msql->query("select * from `{$tb_kj}` where gid='{$gid}' and m1!='' and closetime<'{$time}' order by gid,qishu desc limit 1");
        $msql->next_record();
        if ($m1 == $msql->f('m1') && $qs == $msql->f('qishu') && $news != '1') {
            echo json_encode(array("A", "B",$news));
            exit;
        }
        $mm = 1;
        if ($config["panstatus"]!=1) {
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
        //error_reporting(E_ALL);
        //if ($fenlei != 100) {
            $tpl->assign('fenlei', $fenlei);
            $tpl->assign('tu', $tu);
            if ($fenlei != 151 & 1 == 2) {
                $kj = getkjs($gid, $config['mnum'], 30, sqltime(time()));
                $tpl->assign('kj', $kj);
                $co[0] = 'blue';
                $co[1] = 'orange';
                $co[2] = 'lv';
                $co[3] = 'red';
                $tpl->assign('co', $co);
                $longl = $tpl->fetch('longl.html');
            }
            $ftlu=[];
            if ($config['cs']['ft'] == '1' && $tu == 2) {
                $tpl->assign("ftnum",$config['cs']['ftnum']);
                
                if(date("H:i:s")<$config['editend']){
                    $timestr = " and dates='" . date("Y-m-d", time()-86400) . "' ";
                } else {
                    $timestr = " and dates='" . date("Y-m-d") . "' ";
                }
                $sql = '';
                for ($i = 1; $i <= $config['mnum']; $i++) {
                    $sql .= ",m" . $i;
                }
                //echo "select qishu$sql from `$tb_kj` where gid='$gid' and m1!='' $timestr $kjtime order by qishu desc";
                $kj = $psql->arr("select * from `{$tb_kj}` where gid='{$gid}' {$timestr} and m1!=''  order by qishu desc limit 99", 1);
                $ck = count($kj);
                $cn = count($num);
                for($i=0;$i<99;$i++){
                    $ftlu[$i] = 0;
                }
                //$k1 = floor($ck/10);
                 //1
                for ($i = 0; $i < $ck; $i++) {
                    $kjarr = [$kj[$i]['m1'],$kj[$i]['m2'],$kj[$i]['m3'],$kj[$i]['m4'],$kj[$i]['m5'],$kj[$i]['m6'],$kj[$i]['m7'],$kj[$i]['m8'],$kj[$i]['m9'],$kj[$i]['m10']] ;
                    $zh = getftzh($kjarr,$config['cs']);
                    $kj[$i]['z'] = $zh;
                    $zh = $zh % 4 == 0 ? 4 : $zh % 4;
                    $kj[$i]['mft'] = $zh;
                    $kj[$i]['ftds'] = danshuang($zh);
                    $kj[$i]['ftdx'] = $zh <= 2 ? '小' : '大';   
                    $j = $ck-$i-1;
                    $k1 = floor($j/10);//2 
                    $k2 = $j%10;
                    //$ftlu[$k2*10+$k1] = substr($kj[$i]['qishu'],-3); //21      
                    $ftlu[$k2*10+$k1] = $zh;          
                }
                
                $tpl->assign('kj', $kj);
                $longr = $tpl->fetch('longr.html');
            } else {
                if ($fenlei == 151) {
                    $kj = getkjs($gid, $config['mnum'], $config['cs']['qsnums'], sqltime(time()), $fenlei);
                    $tpl->assign('kj', $kj);
                    $longr = $tpl->fetch('longr.html');
                } else {
                    $z = getzlong();
                    $buz = getbuzlong();
                    $tpl->assign('z', $z);
                    $tpl->assign('buz', $buz);
                    $longr = $tpl->fetch('longr.html');
                }
            }
            if ($tu != 0) {
                //echo $gid, $config['mnum'],$fenlei,$tu;
                $tu = tu($gid, $config['mnum'], $fenlei, $tu);
                //$tu = '';
            }
        //}
        $longl = json_encode($num);
        
        $news="";
        $time = time();
        $msql->query("select content from `$tb_message` where userid='$userid' and $time-UNIX_TIMESTAMP(time)<600 order by time desc limit 1");
            if ($msql->next_record()) {
                $news = $msql->f('content');
            }
        echo json_encode(array($longl, $longr, $tu, $mm, $ma, $mqishu, $config['gname'],$news,$config['cs']['ft'],$ftlu,$sx));
        unset($longl);
        unset($longr);
        break;
    case "tu":
        $tu = $_POST['tu'];
        $tu = tu($gid, $config['mnum'], $config['fenlei'], $tu);
        echo json_encode($tu);
        unset($tu);
        break;
}
function getsmgg($bid, $ab, $abcd, $sid, $stype)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid;
    $abcd = low($abcd);
    $time = time();
    $tsql->query("select * from `{$tb_play}` where gid='{$gid}' and bid=23378688 and name<1 order by bid,sid,xsort");
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
            $p[$i]['peilv1'] = (double)($tsql->f('peilv1')-$config['patt'][$ftype][strtolower($abcd)]);
            $p[$i]['peilv2'] = (double)($tsql->f('peilv2')-$config['patt'][$ftype][strtolower($abcd)]);
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
function getsm($bid, $ab, $abcd, $sid, $smtype, $fenlei)
{
    global $tb_play, $msql, $psql, $tb_class, $gid, $config;
    $fenlei = $config['fenlei'];
    if ($fenlei == 101) {
        $sql = "select * from `{$tb_play}` where gid='{$gid}' and (( bid=23378755 and  name in('单','双','大','小')) or name in('总和单','总和双','总和大','总和小','龙','虎','和') or  bid=23378767) order by bid,sid,cid,xsort ";
    } else {
        if ($fenlei == 103 | $fenlei == 121) {
            $sql = "select * from `{$tb_play}` where gid='{$gid}' and (name in('单','双','大','小','合数单','合数双','尾大','尾小','总和单','总和双','总和大','总和小','总和尾大','总和尾小','龙','虎'))  order by bid,sid,xsort";
        } else {
            if ($fenlei == 151) {
                $sql = "select * from `{$tb_play}` where gid='{$gid}'  order by bid,sid,cid,xsort";
            } else {
                if ($fenlei == 161) {
                    $sql = "select * from `{$tb_play}` where gid='{$gid}' and cid<> 23379261 and bid<> 26000000  order by id";
                } else {
                    if ($fenlei == 107) {
                        $sql = "select * from `{$tb_play}` where gid='{$gid}' and name in('单','双','大','小','龙','虎','冠亚单','冠亚双','冠亚大','冠亚小') order by bid,sid,xsort";
                    } else {
                        if ($fenlei == 163) {
                            $sql = "select * from `{$tb_play}` where gid='{$gid}' and ( name in('单','双','大','小') or bid='23378858') and bid!=23378857 order by bid,sid,xsort";
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
        $fsql->query("select * from `{$tb_play}` where gid='{$gid}' and zqishu>=2 and name in('单','双','大','小','龙','虎','冠亚单','冠亚双','冠亚大','冠亚小','总和单','总和双','总和大','总和小','合数单','合数双','尾大','尾小') order by zqishu desc,bid,sid,cid,xsort");
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
        $timestr = " and kjtime<='" . date("Y-m-d") . " " . $config['editstart'] . "' and kjtime>='" . date("Y-m-d", time()-86400) . " " . $config['editstart'] . "'";
    }  else {
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
        for ($i = 0; $i < $ck; $i++) {
            $zh = 0;
            $kjarr = [$kj[$i]['m1'],$kj[$i]['m2'],$kj[$i]['m3'],$kj[$i]['m4'],$kj[$i]['m5'],$kj[$i]['m6'],$kj[$i]['m7'],$kj[$i]['m8'],$kj[$i]['m9'],$kj[$i]['m10']] ;
            $zh = getftzh($kjarr,$config['cs']);
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
                                        if($j<4){
                                            $tu[$bname[$j]]['龙虎'][$i] = longhuhe($kj[$i]['m' . ($j + 1)],$kj[$i]['m' . (8-$j)]);
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