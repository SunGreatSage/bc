<?php
include('../data/comm.inc.php');include('../data/agentvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        if (is_numeric($_REQUEST['gid'])) {
            $gid = $_REQUEST['gid'];
        }
        $psize = $config['psize'];
		$page = r1p($_REQUEST['page']);
        $msql->query("select count(id) from `{$tb_peilv}` where userid='{$userid}' and gid='{$gid}'");
        $msql->next_record();
        $rcount   = pr0($msql->f(0));
        $pcount = $rcount%$psize==0 ? $rcount/$psize : (floor($rcount/$psize)+1);
        $gname    = transgame($gid, 'gname');
        $auto     = array(
            '手动',
            '自动',
            '写入默认',
            '恢复默认',
            '赔率清零'
        );
        $msql->query("select * from `{$tb_peilv}` where userid='{$userid}' and gid='{$gid}' order by time desc limit " . ($page - 1) * $psize . ",{$psize}");
        $i = 0;
        $p = array();
        while ($msql->next_record()) {
            $p[$i]['gname']   = $gname;
            $p[$i]['peilv']   = $msql->f('peilv');
            $p[$i]['sonuser'] = transuser($msql->f('sonuser'), 'username');
            $p[$i]['auto']    = $auto[$msql->f('auto')];
            $p[$i]['time']    = substr($msql->f('time'),5);
            if ($msql->f('pid') == 0) {
                $p[$i]['pid'] = '';
            } else {
                $fsql->query("select * from `{$tb_play}` where gid='{$gid}' and pid='" . $msql->f('pid') . '\'');
                $fsql->next_record();
                $p[$i]['pid'] = wf($gid, transb('name', $fsql->f('bid')), transs('name', $fsql->f('sid')), transc('name', $fsql->f('cid')), $fsql->f('name'));
            }
            $p[$i]['id'] = $msql->f('id');
            $i++;
        }
		$game = getgamecs($userid);
		$game = getgamename($game);
		$msql->query("select autodelpltime from `$tb_config`");
		$msql->next_record();
		$tpl->assign("autodelpltime",$msql->f('autodelpltime'));
        $tpl->assign('gid', $gid);
        $tpl->assign('game', $game);
		$tpl->assign('config', $config);
        $tpl->assign('p', $p);
		$tpl->assign('page', $page);
		$tpl->assign('rcount', $rcount);
		$tpl->assign('pcount', $pcount);
        $tpl->display('peilvr.html');
        break;
    case "setpeilvallduo":
        $p1  = $_POST['p1'];
        $p1  = str_replace('\\', '', $p1);
        $p2  = $_POST['p2'];
        $p2  = str_replace('\\', '', $p2);
        $p3  = $_POST['p3'];
        $p3  = str_replace('\\', '', $p3);
        $pid = $_POST['pid'];
        $epl = $_POST['epl'];
        $p1  = json_decode($p1, true);
        $p2  = json_decode($p2, true);
		$p3  = json_decode($p3, true);
        $msql->query("select pl,mpl from `$tb_play_user` where userid='$userid' and gid='$gid' and pid='$pid'");
        $msql->next_record();
        $pl  = json_decode($msql->f('pl'), true);
        $mpl = json_decode($msql->f('mpl'), true);
        if ($epl == 4) {
            foreach ($p1 as $v) {
                if (is_numeric($v['p'])) {
                    $mpl[0][$v['i']] = $v['p'] + 0;
                }
            }
            if (is_array($mpl[1])) {
                foreach ($p2 as $v) {
                    if (is_numeric($v['p'])) {
                        $mpl[1][$v['i']] = $v['p'] + 0;
                    }
                }
            }
            if (is_array($mpl[2])) {
                foreach ($p3 as $v) {
                    if (is_numeric($v['p'])) {
                        $mpl[2][$v['i']] = $v['p'] + 0;
                    }
                }
            }
            $mpl = json_encode($mpl);
            $msql->query("update `$tb_play_user` set mpl='$mpl' where userid='$userid' and gid='$gid' and pid='$pid'");
        } else {
            foreach ($p1 as $v) {
                if (is_numeric($v['p'])) {
                    $pl[0][$v['i']] = $v['p'] + 0;
                }
            }
            if (is_array($pl[1])) {
                foreach ($p2 as $v) {
                    if (is_numeric($v['p'])) {
                        $pl[1][$v['i']] = $v['p'] + 0;
                    }
                }
            }
            if (is_array($pl[2])) {
                foreach ($p3 as $v) {
                    if (is_numeric($v['p'])) {
                        $pl[2][$v['i']] = $v['p'] + 0;
                    }
                }
            }
            $pl = json_encode($pl);
            $msql->query("update `$tb_play_user` set pl='$pl' where userid='$userid' and gid='$gid' and pid='$pid'");
			$time = time();
			$msql->query("insert into `$tb_peilv` set gid='$gid',pid='$pid',peilv='0',time=NOW(),userid='$userid',sonuser='$userid2',auto=0");
        }
        echo 1;
        break;
    case "setpeilvall":
        $pl   = $_POST['pl'];
        $abcd = $_POST['abcd'];
        $ab   = $_POST['ab'];
        $epl  = $_POST['epl'];
        $pl   = json_decode(str_replace('\\', "", $pl), true);
        if ($ab != 'A' & $ab != 'B')
            $ab = 'A';
        if ($abcd != 'A' & $abcd != 'B' & $abcd != 'C' & $abcd != 'D')
            $abcd = 'A';
        $msql->query("select ifexe,pself from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $ifexe = $msql->f('ifexe');
        $pself = $msql->f('pself');
        if ($epl == 4) {
            foreach ($pl as $key => $v) {
                if (substr($key, 0, 2) == 'p1') {
                    $tmp = str_replace('p1', '', $key);
                    $sql = "update `$tb_play_user` set mp1='$v' where userid='$userid' and gid='$gid' and  pid='$tmp'";
                    $msql->query($sql);
                } else {
                    $tmp = str_replace('p2', '', $key);
                    $sql = "update `$tb_play_user` set mp2='$v' where userid='$userid' and gid='$gid' and  pid='$tmp'";
                    $msql->query($sql);
                }
            }
        } else {
            if ($pelf == 1) {
                foreach ($pl as $key => $v) {
                    if (substr($key, 0, 2) == 'p1') {
                        $tmp = str_replace('p1', '', $key);
                        $sql = "select ftype,cid from `$tb_class` where gid='$gid' and cid=(select cid from `$tb_play`  where  gid='$gid' and pid='$tmp')";
                        $msql->query($sql);
                        $msql->next_record();
                        if ($ab == 'B') {
                            $v -= $config['patt'][$msql->f('ftype')]['ab'];
                        }
                        if ($abcd != 'A') {
                            $v += $config['patt'][$msql->f('ftype')][strtolower($abcd)];
                        }
                        $sql = "update `$tb_play_user` set peilv1=$v where userid='$userid' and gid='$gid' and pid='$tmp'";
                        $msql->query($sql);
                        $time = time();
                        $fsql->query("delete from `$tb_c` where gid='$gid' and  pid='$tmp' and userid='$userid'");
                        $fsql->query("insert into `$tb_c` set gid='$gid',pid='$tmp',time=NOW(),userid='$userid'");
                        $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='$tmp',peilv='$v',time=NOW(),userid='$userid',sonuser='$userid2',auto=0");
                    } else {
                        $tmp = str_replace('p2', '', $key);
                        $sql = "update `$tb_play_user` set peilv2='$v' where userid='$userid' and gid='$gid' and pid='$tmp'";
                        $msql->query($sql);
                    }
                }
            } else {
                foreach ($pl as $key => $v) {
                    if (substr($key, 0, 2) == 'p1') {
                        $tmp = str_replace('p1', '', $key);
                        $sql = "update `$tb_play_user` set peilv1='$v' where userid='$userid' and gid='$gid' and  pid='$tmp'";
                        $msql->query($sql);
                        $time = time();
                        $fsql->query("delete from `$tb_c` where gid='$gid' and  pid='$tmp' and userid='$userid'");
                        $fsql->query("insert into `$tb_c` set gid='$gid',pid='$tmp',time=NOW(),userid='$userid'");
                        $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='$tmp',peilv='$v',time=NOW(),userid='$userid',sonuser='$userid2',auto=0");
                    } else {
                        $tmp = str_replace('p2', '', $key);
                        $sql = "update `$tb_play_user` set peilv2='$v' where userid='$userid' and gid='$gid' and pid='$tmp'";
                        $msql->query($sql);
                    }
                }
            }
        }
        echo 1;
        break;
    case "setatttwo":
        $action = $_POST['action'];
        $pid    = $_POST['pid'];
        $epl    = $_POST['epl'];
        $sql    = "select ftype from `$tb_class` where gid='$gid' and cid=(select cid from `$tb_play` where  gid='$gid' and pid='$pid')";
        $msql->query($sql);
        $msql->next_record();
        $att = transatt($msql->f('ftype'), 'peilvatt1',0);
        if ($epl == 4) {
            if ($action == 'down') {
                $msql->query("update `$tb_play_user` set mp1=mp1-$att  where userid='$userid' and gid='$gid' and pid='$pid' ");
            } else {
                $msql->query("update `$tb_play_user` set mp1=mp1+$att  where userid='$userid' and gid='$gid' and pid='$pid' ");
            }
        } else {
            $pself = transuser($userid, 'pself');
            if ($pself == 1) {
                if ($action == 'down') {
                    $msql->query("update `$tb_play_user` set peilv1=peilv1-$att  where userid='$userid' and gid='$gid' and pid='$pid' ");
                } else {
                    $msql->query("update `$tb_play_user` set peilv1=peilv1+$att  where userid='$userid' and gid='$gid' and pid='$pid' ");
                }
            } else {
                if ($action == 'down') {
                    $msql->query("update `$tb_play_user` set peilv1=peilv1+$att  where userid='$userid' and gid='$gid' and pid='$pid' ");
                } else {
                    $msql->query("update `$tb_play_user` set peilv1=peilv1-$att  where userid='$userid' and gid='$gid' and pid='$pid' ");
                }
            }
            $time = time();
            $fsql->query("delete from `$tb_c` where gid='$gid' and pid='$pid' and userid='$userid'");
            $fsql->query("insert into `$tb_c` set gid='$gid',pid='$pid',time=NOW(),userid='$userid'");
            $atts = $att;
            if ($action == 'down') {
                $atts = 0 - $atts;
            }
            $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='$pid',peilv='$atts',time=NOW(),userid='$userid',sonuser='$userid2',auto=0");
        }
        echo $att;
        break;
    case "mr":
        $action = $_POST['action'];
        $time   = time();
        if ($action == 'writem') {
            $msql->query("update `$tb_play_user` set mp1=peilv1,mp2=peilv2,mpl=pl where gid='$gid' and  userid='$userid'");
            $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='0',peilv=0,time=NOW(),userid='$userid',sonuser='$userid2',auto=2");
        } else if ($action == 'resetm') {
            $msql->query("update `$tb_play_user` set peilv1=mp1,peilv2=mp2,pl=mpl,autocs=0,start=0,zautocs=0,zstart=0 where gid='$gid' and  userid='$userid'");
            $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='0',peilv=0,time=NOW(),userid='$userid',sonuser='$userid2',auto=3");
        } else if ($action == 'resetz') {
            $msql->query("select ifexe,pself from `$tb_user` where userid='$userid'");
            $msql->next_record();
            $ifexe = $msql->f('ifexe');
            $pself = $msql->f('pself');
            if ($ifexe == 1 & $pself == 1) {
                $msql->query("update `$tb_play_user` X,`$tb_play` Y set X.peilv1=Y.peilv1,X.peilv2=Y.peilv2,X.pl=Y.pl where X.gid='$gid' and X.userid='$userid' and X.gid=Y.gid and X.pid=Y.pid");
                $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='0',peilv=0,time=NOW(),userid='$userid',sonuser='$userid2',auto=4");
            } else {
                $msql->query("update `$tb_play_user` set peilv1=0,peilv2=0,pl='',mp1=0,mp2=0,mpl='' where gid='$gid' and userid='$userid'");
                $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='0',peilv=0,time=NOW(),userid='$userid',sonuser='$userid2',auto=4");
            }
        }
        echo 1;
        break;
    case "getatt":
        $abcd = strtolower($_POST['abcd']);
        $ab   = $_POST['ab'];
        $ch   = array();
        if ($config['panstatus'] == 0) {
            echo json_encode($ch);
            exit;
        }
        $time = time();
        $msql->query("select kmoney,layer,fid1,ifexe,pself from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $kmoney   = $msql->f('kmoney');
        $thelayer = $msql->f('layer');
        $fid1     = $msql->f('fid1');
        $ifexe    = $msql->f('ifexe');
        $pself    = $msql->f('pself');
        if ($thelayer > 1) {
            $msql->query("select ifexe,pself from `$tb_user` where userid='$fid1'");
            $msql->next_record();
            $ifexe = $msql->f('ifexe');
            $pself = $msql->f('pself');
        }
        $msql->query("select * from `$tb_c` where gid='$gid' and $time-UNIX_TIMESTAMP(time)<=3 and (userid='99999999' or userid='$fid1')");
        $i = 0;
        while ($msql->next_record()) {
            $fsql->query("select cid,peilv1,b,c,d from `$tb_play` where gid='$gid' and pid='" . $msql->f('pid') . "'");
            $fsql->next_record();
            $peilv1  = $fsql->f('peilv1');
            $cid     = $fsql->f('cid');
            $peilv1s = 0;
            if ($thelayer > 1 & $ifexe == 1) {
                $tsql->query("select peilv1 from `$tb_play_user` where userid='$fid1' and gid='$gid' and pid='$pid'");
                $tsql->next_record();
                $peilv1s = $tsql->f('peilv1');
            }
            $tsql->query("select ftype,cid from `$tb_class` where gid='$gid' and   cid='" . $cid . "'");
            $tsql->next_record();
            $ftype = $tsql->f('ftype');
            $abcha = 0;
            if ($config['pan'][$ftype]['ab'] == 1 & $ab == 'B') {
                $abcha = $config['patt'][$ftype]['ab'];
            }
            $abcdcha = 0;
            if ($config['pan'][$ftype]['abcd'] == 1 & $abcd != 'A') {
                $abcdcha = $fsql->f(strtolower($abcd));
            }
            $ch[$i]['pid'] = $msql->f('pid');
            if ($thelayer > 1 & $ifexe == 1 & $pself == 1) {
                $peilvcha         = getuserpeilvcha2($userid, $ftype);
                $ch[$i]['peilv1'] = $peilv1s + $abcha - $abcdcha - $peilvcha;
            } else {
                $peilvcha         = getuserpeilvcha($userid, $ftype);
                $ch[$i]['peilv1'] = $peilv1 + $abcha - $abcdcha - peilv1s - $peilvcha;
            }
            $i++;
        }
        echo json_encode($ch);
        unset($ch);
        break;
    case "pism":
        $val = $_POST['val'];
        $epl = $_POST['epl'];
        foreach ($config['ftype'] as $key => $v) {
            if ($v == '两面') {
                $ftype = $key;
            }
        }
        if ($epl == 'mp') {
            $sql = "update `$tb_play_user` set mp1='$val' where userid='$userid' and gid='$gid' and cid in(select cid from `$tb_class` where gid='$gid' and ftype='$ftype') ";
        } else {
            $sql = "update `$tb_play_user` set peilv1='$val' where userid='$userid' and  gid='$gid' and cid in(select cid from `$tb_class` where gid='$gid' and ftype='$ftype')";
        }
        $msql->query($sql);
        echo 1;
        break;
    case "yiwotongbu":
        $gid = $_POST['gid'];
        $epl = $_POST['epl'];
        $msql->query("select gid from `$tb_game` where gid!='$gid' and fenlei=(select fenlei from `$tb_game` where gid='$gid')");
        while ($msql->next_record()) {
            $garr[] = $msql->f('gid');
        }
        $garr = implode(',', $garr);
        $msql->query("select * from `$tb_play_user` where userid='$userid' and gid='$gid'");
        while ($msql->next_record()) {
            $pid = $msql->f('pid');
            
                $mp1  = $msql->f('mp1');
                $mp2  = $msql->f('mp2');
                $mpl  = $msql->f('mpl');
                $p1  = $msql->f('peilv1');
                $p2  = $msql->f('peilv2');
                $pl  = $msql->f('pl');
                $sql = "update `$tb_play_user` set mp1='$mp1',mp2='$mp2',mpl='$mpl',peilv1='$p1',peilv2='$p2',pl='$pl' where userid='$userid' and pid='$pid' and gid in($garr)";
           $fsql->query($sql);
		   
        }
        echo 1;
        break;
}
?>