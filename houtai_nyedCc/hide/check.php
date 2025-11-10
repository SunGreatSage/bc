<?php

include '../data/comm.inc.php';
include '../data/myadminvar.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/adminfunc.php';
include '../include.php';
include './checklogin.php';
if ($_SESSION['hides'] != 1) {
    exit;
}
set_time_limit(0);
switch ($_REQUEST['xtype']) {
    case "show":
        //error_reporting(E_ALL);
        $msql->query("select ifhide,xpage,pagename from `{$tb_admins_page}` where adminid=10000 order by sortx");
        $xpage = array();
        $i = 0;
        while ($msql->next_record()) {
            $xpage[$i]['xpage'] = $msql->f('xpage');
            $xpage[$i]['ifhide'] = $msql->f('ifhide');
            $xpage[$i]['pagename'] = $msql->f('pagename');
            $i++;
        }
        $tpl->assign("xpage", $xpage);
        $msql->query("select * from `{$tb_ctrl}`");
        $msql->next_record();
        $tpl->assign("autoopenpan", $msql->f('autoopenpan'));
        $tpl->assign("autoopenpantime", $msql->f('autoopenpantime'));
        $tpl->assign("autofly", $msql->f('autofly'));
        $tpl->assign("autoflytime", $msql->f('autoflytime'));
        $tpl->assign("kjjs", $msql->f('kjjs'));
        $tpl->assign("kjjstime", $msql->f('kjjstime'));
        //$tpl->display("check.html");
        //exit;
        include "../data/cuncu.php";
        $kksql->query("show triggers");
        $tg = array();
        while ($kksql->next_record()) {
            $tg[] = $kksql->f('Trigger');
        }
        if (in_array('updatelib', $tg)) {
            $updatelib = 1;
        }
        if (in_array('deletelib', $tg)) {
            $deletelib = 1;
        }
        if (in_array('updateplay', $tg)) {
            $updateplay = 1;
        }
        $tpl->assign("updatelib", $updatelib);
        $tpl->assign("deletelib", $deletelib);
        $tpl->assign("updateplay", $updateplay);
        $msql->query("select count(id) from `{$tb_error}`");
        $msql->next_record();
        $tpl->assign("liberrnum", $msql->f(0));
        $bigdata = 0;
        $msql->query("SHOW TABLES LIKE  '%total%'");
        $msql->next_record();
        if (strpos($msql->f(0), '_total') !== false) {
            $bigdata = 1;
        }
        $tpl->assign("bigdata", $bigdata);
        if($bigdata==1){
            $kksql->query("SHOW CREATE TABLE `x_lib_total`");
            $kksql->next_record();
            $total = $kksql->f(1);
        }        
        $mrg = array();
        $arr = $msql->arr("SHOW TABLES LIKE '%\\_lib%'", 0);
        $i = 0;
        foreach ($arr as $k => $v) {
            if ($v[0] != 'x_lib_err') {
                $mrg[$i]['tb'] = $v[0];
                $msql->query("select count(id) from `" . $v[0] . "`");
                $msql->next_record();
                $mrg[$i]['num'] = $msql->f(0);
                $mrg[$i]['in'] = 0;
                if (strpos($total, "`".$v[0]."`") !== false && $v[0]!='x_lib_total') {
                    $mrg[$i]['in'] = 1;
                }
                $i++;
            }
        }
        $tpl->assign("mrg", $mrg);
        $tpl->display("check.html");
        break;
    case "qkerr":
        $msql->query("delete from `{$tb_error}`");
        echo 1;
        break;
    case "mrg":
        if ($_POST['pass'] != $config['supass']) {
            echo 2;
            exit;
        }
        include "../data/cuncu.php";
        $ac = $_POST['ac'];

        if ($ac == 'sc') {
            $kksql->query("create table if not exists `x_lib_total` like `x_lib`");
            $kksql->query("alter table `x_lib_total` ENGINE = MRG_MYISAM,INSERT_METHOD=LAST");
            $dates = $msql->arr("select dates from `{$tb_lib}` group by dates", 1);
            $thisday = getthisdate();
            $str = "";
            $kksql->query($deletestr);
            foreach ($dates as $k => $v) {
                if ($v['dates'] != $thisday) {
                    $date = str_replace('-', '', $v['dates']);
                    $tb = "x_lib_" . $date;
                    $kksql->query("create table if not exists `{$tb}` like `x_lib`");
                    $kksql->query("TRUNCATE `{$tb}`");
                    $kksql->query("insert into `{$tb}` select * from `x_lib` where dates='" . $v['dates'] . "'");
                    $kksql->query("delete from `x_lib` where dates='" . $v['dates'] . "'");
                    $str .= "`x_lib_" . $date . "`,";
                }
            }
            $kksql->query($deletecc);
            $str .= "`x_lib`";
           
            $kksql->query("alter table `x_lib_total`  union=({$str})");
        } else {
            if ($ac == 'hf') {
                $kksql->query("drop table `x_lib_total`");
                $arr = $msql->arr("SHOW TABLES LIKE '%\\_lib_20%'", 0);
                foreach ($arr as $k => $v) {
                    $tb = $v[0];
                    $kksql->query("insert into `x_lib` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb}`");
                    $kksql->query("drop table `{$tb}`");
                }
            } else {
                if ($ac == 'resc') {
                    $tb = explode('|', $_POST['tb']);
                    $str = '';
                    foreach ($tb as $key => $val) {
                        if ($val != 'x_lib' && $val != 'x_lib' && $val != 'x_lib_err' && $val != 'x_libu' && $val!="") {
                            $str .= "`" . $val . "`,";
                        }
                    }
                    $str .= "`x_lib`";
                    $kksql->query("alter table `x_lib_total` union=({$str})");
                } else {
                    if ($ac == 'scxz') {
                        $msql->query("SHOW TABLES LIKE '%total%'");
                        $msql->next_record();
                        if (strpos($msql->f(0), '_total') !== false) {
                            $bigdata = 1;
                        }
                        if ($bigdata == 1) {
                            $total = $kksql->arr("SHOW CREATE TABLE `x_lib_total`");
                            $tb = explode('|', $_POST['tb']);
                            foreach ($tb as $key => $val) {
                                if (strpos($total[0][1], $val) == false & $val != 'x_lib' & $val != 'x_libu' & $val != 'x_lib_err' & $val != "") {
                                    $kksql->query("drop table if exists `{$val}` ");
                                }
                            }
                        } else {
                            $tb = explode('|', $_POST['tb']);
                            $str = '';
                            foreach ($tb as $key => $val) {
                                if ($val != 'x_lib' & $val != 'x_libu' & $val != 'x_lib_err' & $val != "") {
                                    $kksql->query("drop table if exists `{$val}` ");
                                }
                            }
                        }
                    }
                }
            }
        }
        echo 1;
        break;
    case "tg":
   
     $msql->query("SHOW TABLES LIKE  '%total%'");
        $msql->next_record();
        if (strpos($msql->f(0), '_total') !== false) {
            $bigdata = 1;
        }
        $ac = $_POST['ac'];
        $name = $_POST['name'];
        include "../data/cuncu.php";
        $updateplaydel = "DROP TRIGGER IF EXISTS `updateplay` ";
        $updateplayadd = "CREATE TRIGGER `updateplay` BEFORE UPDATE ON `x_play` FOR EACH ROW BEGIN  if new.peilv1>new.mp1  then set new.peilv1=new.mp1; end if; end;";
        if ($name == 'ulib') {
            if ($ac == 'add') {
                $kksql->query($updatecc);
                $bigdata==1 && $kksql->query($tupdatecc);
            } else {
                $kksql->query($updatestr);
                $bigdata==1 && $kksql->query($tupdatestr);
            }
        }
        if ($name == 'dlib') {
            if ($ac == 'add') {
                $kksql->query($deletecc);
                $bigdata==1 && $kksql->query($tdeletecc);
            } else {
                $kksql->query($deletestr);
                $bigdata==1 && $kksql->query($tdeletestr);
            }
        }
        if ($name == 'uplay') {
            if ($ac == 'add') {
                $kksql->query($updateplayadd);
            } else {
                $kksql->query($updateplaydel);
            }
        }
        echo 1;
        break;
    case "upgn":
        $msql->query("select ifhide,xpage,pagename from `{$tb_admins_page}` where adminid=10000 order by sortx");
        while ($msql->next_record()) {
            $tmp = $msql->f('xpage');
            if (trim($_POST[$tmp]) == 1) {
                $fsql->query("update `{$tb_admins_page}` set ifhide=0,ifok=1 where adminid=10000 and xpage='" . $tmp . "'");
                $fsql->query("update `{$tb_admins_page}` set ifhide=0,ifok=1 where adminid!=10000 and xpage='" . $tmp . "'");
            } else {
                $fsql->query("update `{$tb_admins_page}` set ifhide=1,ifok=0 where xpage='" . $tmp . "'");
            }
        }
        echo 1;
        break;
    case "setctrl":
        $autoopenpan = $_POST['autoopenpan'];
        $autofly = $_POST['autofly'];
        $kjjs = $_POST['kjjs'];
        $time = time();
        $msql->query("update `{$tb_ctrl}` set autoopenpan='{$autoopenpan}',autoopenpantime=NOW(),autofly='{$autofly}',autoflytime=NOW(),kjjs='{$kjjs}',kjjstime=NOW(),slowautoopenpan='{$slowautoopenpan}',slowautoopenpantime=NOW(),slowautofly='{$slowautofly}',slowautoflytime=NOW(),slowkjjs='{$slowkjjs}',slowkjjstime=NOW()");
        echo 1;
        break;
}