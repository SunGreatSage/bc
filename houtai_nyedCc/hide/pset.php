<?php
include('../data/comm.inc.php');include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
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
       // print_r($p1);
        $msql->query("select pl,mpl from `$tb_play` where gid='$gid' and pid='$pid'");
        $msql->next_record();
        $pl  = json_decode($msql->f('pl'), true);
        $mpl = json_decode($msql->f('mpl'), true);
        if ($epl == 4) {
            if($config['fenlei']==100){
            foreach ($p1 as $k => $v) {
                 if (is_numeric($v)) {
                    $mpl[0][$k] = $v + 0;
                }
            }
            if (is_array($mpl[1])) {
                foreach ($p2 as $k => $v) {
                     if (is_numeric($v)) {
                        $mpl[1][$k] = $v + 0;
                    }
                }
            }
            if (is_array($mpl[2])) {
                foreach ($p3 as $k => $v) {
                     if (is_numeric($v)) {
                        $mpl[2][$k] = $v + 0;
                    }
                }
            }
            }else{
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
            }

            $mpl = json_encode($mpl);
            $msql->query("update `$tb_play` set mpl='$mpl' where gid='$gid' and pid='$pid'");
        } else {

            if($config['fenlei']==100){
            foreach ($p1 as $k => $v) {
                if (is_numeric($v)) {
                    $pl[0][$k] = $v + 0;
                }
            }
            if (is_array($pl[1])) {
                foreach ($p2 as $k => $v) {
                     if (is_numeric($v)) {
                        $pl[1][$k] = $v + 0;
                    }
                }
            }
            if (is_array($pl[2])) {
                foreach ($p3 as $k => $v) {
                     if (is_numeric($v)) {
                        $pl[2][$k] = $v + 0;
                    }
                }
            }
            }else{
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
        }
            $pl = json_encode($pl);
            //echo $pl;
            //echo "update `$tb_play` set pl='$pl' where gid='$gid' and pid='$pid'";
            $msql->query("update `$tb_play` set pl='$pl' where gid='$gid' and pid='$pid'");
            $time = time();
            $msql->query("insert into `$tb_peilv` set gid='$gid',pid='$pid',peilv='0',time=NOW(),userid='$userid',sonuser='$adminid',auto=0");
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
        if ($epl == 4) {
            foreach ($pl as $key => $v) {
                if (substr($key, 0, 2) == 'p1') {
                    $tmp = str_replace('p1', '', $key);
                    $sql = "update `$tb_play` set mp1='$v' where gid='$gid' and  pid='$tmp'";
                    $msql->query($sql);
                } else {
                    $tmp = str_replace('p2', '', $key);
                    $sql = "update `$tb_play` set mp2='$v' where gid='$gid' and  pid='$tmp'";
                    $msql->query($sql);
                }
            }
        } else {
            foreach ($pl as $key => $v) {
                if (substr($key, 0, 2) == 'p1') {
                    $tmp = str_replace('p1', '', $key);
                    $sql = "select ftype,cid from `$tb_class` where gid='$gid' and cid=(select cid from `$tb_play` ";
                    $sql .= " where  gid='$gid' and pid='$tmp')";
                    $msql->query($sql);
                    $msql->next_record();
                    if ($ab == 'B') {
                        $v -= $config['patt'][$msql->f('ftype')]['ab'];
                    }
                    if ($abcd != 'A') {
                        $v += $config['patt'][$msql->f('ftype')][strtolower($abcd)];
                    }
                    $sql = "update `$tb_play` set peilv1=$v where gid='$gid' and  pid='$tmp'";
                    $msql->query($sql);
                    $time = time();
                    $fsql->query("delete from `$tb_c` where gid='$gid' and  pid='$tmp' and userid='$userid'");
                    $fsql->query("insert into `$tb_c` set gid='$gid',pid='$tmp',time=NOW(),userid='$userid'");
                    $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='$tmp',peilv='$v',time=NOW(),userid='$userid',sonuser='$adminid',auto=0");
                } else {
                    $tmp = str_replace('p2', '', $key);
                    $sql = "update `$tb_play` set peilv2='$v' where pid='$tmp'";
                    $msql->query($sql);
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
                $msql->query("update `$tb_play` set mp1=if(mp1-$att>1,mp1-$att,1)  where gid='$gid' and pid='$pid'");
            } else {
                $msql->query("update `$tb_play` set mp1=mp1+$att  where  gid='$gid' and pid='$pid'");
            }
        } else {
            if ($action == 'down') {
                $msql->query("update `$tb_play` set peilv1=if(peilv1-$att>1,peilv1-$att,1)  where gid='$gid' and pid='$pid'");
            } else {
                $msql->query("update `$tb_play` set peilv1=peilv1+$att  where  gid='$gid' and pid='$pid'");
            }
            $time = time();
            $fsql->query("delete from `$tb_c` where gid='$gid' and pid='$pid' and userid='$userid'");
            $fsql->query("insert into `$tb_c` set gid='$gid',pid='$pid',time=NOW(),userid='$userid'");
            $atts = $att;
            if ($action == 'down') {
                $atts = 0 - $atts;
            }
            $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='$pid',peilv='$atts',time=NOW(),userid='$userid',sonuser='$adminid',auto=0");
        }
        echo $att;
        break;
    case "mr":
        $time   = time();
        $action = $_POST['action'];
        if ($action == 'writem') {
            $msql->query("update `$tb_play` set mp1=peilv1,mp2=peilv2,mpl=pl where gid='$gid'");
            $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='0',peilv=0,time=NOW(),userid='$userid',sonuser='$adminid',auto=2");
        } else if ($action == 'resetm') {
            $msql->query("update `$tb_play` set peilv1=mp1,peilv2=mp2,pl=mpl,start=0,autocs=0,zstart=0,zautocs=0,ystart=0,yautocs=0 where gid='$gid'");
            $fsql->query("insert into `$tb_peilv` set gid='$gid',pid='0',peilv=0,time=NOW(),userid='$userid',sonuser='$adminid',auto=3");
        }
        echo 1;
        break;
    case "changeifok";
        $pid = $_POST['pid'];
        $msql->query("update `$tb_play` set ifok=if(ifok=0,1,0) where gid='$gid' and  pid='$pid'");
        $msql->query("select ifok from `$tb_play` where gid='$gid' and  pid='$pid' ");
        $msql->next_record();
        echo $msql->f('ifok');
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
            $sql = "update `$tb_play` set mp1='$val' where gid='$gid' and cid in(select cid from `$tb_class` where gid='$gid' and ftype='$ftype') and name!='和'";
        } else {
            $sql = "update `$tb_play` set peilv1='$val' where gid='$gid' and cid in(select cid from `$tb_class` where gid='$gid' and ftype='$ftype') and name!='和'";
        }
        $msql->query($sql);
        echo 1;
        break;
    case "yiwotongbu":
        $gid = $_POST['gid'];
        $epl = $_POST['epl'];
        $msql->query("select gid from `$tb_game` where gid!='$gid' and fenlei=(select fenlei from `$tb_game` where gid='$gid')");
        while ($msql->next_record()) {
            $gstr[] = $msql->f('gid');
        }
        $garr = implode(',', $gstr);
        $msql->query("select * from `$tb_play` where gid='$gid'");
        while ($msql->next_record()) {
                $pid = $msql->f('pid');
                $mp1  = $msql->f('mp1');
                $mp2  = $msql->f('mp2');
                $mpl  = $msql->f('mpl');
                $p1  = $msql->f('peilv1');
                $p2  = $msql->f('peilv2');
                $pl  = $msql->f('pl');
                $sql = "update `$tb_play` set mp1='$mp1',mp2='$mp2',mpl='$mpl',peilv1='$p1',peilv2='$p2',pl='$pl' where pid='$pid' and gid in($garr)";
               $fsql->query($sql);
        }
        echo 1;
        break;
}
?>