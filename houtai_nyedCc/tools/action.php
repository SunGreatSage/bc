<?php
$_SERVER['REMOTE_ADDR']='1.1.1.1';
error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set("Asia/Shanghai");
include('../data/config.inc.php');
include('../data/db.php');
include('../global/db.inc.php');
include("../func/func.php");
include("../func/csfunc.php");
include("../func/adminfunc.php");
include("../func/js.php");
if ($_REQUEST['admin'] != 'toor') {
	if ($_REQUEST['mypass'] != 'sgwins') {
		exit;
	}else{
		if($_REQUEST['type']!="qsqc"){
			exit;
		}
	}
}

$his = date("His");
if ($_REQUEST['type']=="kj") {
    $gid = $_REQUEST['gid'];
    $date = $_REQUEST['date'];
    $day=date("Y-m-d");
    while($day>=$date){
        paddqishu($gid,$day);
        $day = date("Y-m-d",strtotime($day)-86400);
    }

}


$his = date("His");
if ($_REQUEST['type']=="cz") {
    include_once("../data/cuncu.php");

    $msql->query("SHOW TABLES LIKE  '%total%'");
    $msql->next_record();
    $date = date("Ymd",time()-86400);
    $tb = "x_lib_".$date;
    if($msql->f(0)=='x_lib_total'){
        $kksql->query("create table if not exists `$tb` like `$tb_lib`");
        //$kksql->query("TRUNCATE `$tb`");
        $kksql->query("insert into `$tb` select * from `$tb_lib` where 1");
        $kksql->query("TRUNCATE `$tb_lib`");

        $msql->query("SHOW TABLES LIKE  'x_lib_20%'");
        $str="";
        $delstr = "";
        while($msql->next_record()){
           $fsql->query("select count(id) from `".$msql->f(0)."`");
           $fsql->next_record();
           if($fsql->f(0)>0){
               $str .= "`".$msql->f(0)."`,";
           }else{
               if($delstr!="") $delstr .= ",";
               $delstr .= "`".$msql->f(0)."`";
           }
        }
        $str .= "`x_lib`";
        $kksql->query("alter table `x_lib_total` union=($str)");
        $delstr!="" && $kksql->query("drop table $delstr");
    }

    /*
    $msql->query("SHOW TABLE STATUS LIKE '$tb_lib'");
    $msql->next_record();
    if ($msql->f('Engine') == 'MRG_MYISAM') {
        $ymd = date("Ymd");
        $newtb = "x_lib_" . $ymd;
        $msql->query("select lib from `$tb_config`");
        $msql->next_record();
        $lib = $msql->f('lib');
        if (!strpos($lib, $newtb)) {
            $str = $lib . ",`" . $newtb . "`";
            $kksql->query("create table if not exists `$newtb` like `x_lib`");
            $kksql->query("ALTER TABLE `$newtb`  ENGINE = MYISAM");
            $kksql->query("delete from `$newtb` where 1=1");
            $kksql->query("alter table `x_lib` union=($str)");
            $kksql->query("FLUSH TABLE `x_lib` ");
            $kksql->query("update `$tb_config` set lib='$str'");
        }
    }
    */
}
if ($_REQUEST['type']=="kedu") {
    $msql->query("select * from `$tb_config`");
    $msql->next_record();
    if ($msql->f('autodellogin') == 1) {
        $dtime = time() - $msql->f('autodellogintime') * 3600 * 24;
        $fsql->query("delete from `$tb_user_login` where time<'" . sqltime($dtime) . "'");
    }
    if ($msql->f('autodeledit') == 1) {
        $dtime = time() - $msql->f('autodeledittime') * 3600 * 24;
        $fsql->query("delete from `$tb_user_edit` where moditime<'" . sqltime($dtime) . "'");
        $fsql->query("delete from `$tb_money_log` where time<'" . sqltime($dtime) . "'");
    }
    if ($msql->f('autodelpl') == 1) {
        $dtime = time() - $msql->f('autodelpltime') * 3600 * 24;
        $fsql->query("delete from `$tb_peilv` where time<'" . sqltime($dtime) . "'");
    }
    $time = time();
    $fsql->query("update `$tb_user` set sy=0,yingdeny=0 ");
    if ($msql->f('reseted') == 'week' & date('w') == 1) {
        $fsql->query("insert into `$tb_user_edit` select NULL,userid,'99999999','1001','127.0.0.1','',NOW(),'恢复快开额度','',kmoney,kmaxmoney from `$tb_user` where kmaxmoney!=kmoney and fudong=0");
        $fsql->query("insert into `$tb_money_log` select NULL,userid,'99999999','1001',kmaxmoney-kmoney,kmaxmoney,1,NOW(),'127.0.0.1','恢复快开额度', from `$tb_user` where kmaxmoney!=kmoney and fudong=0");
        $fsql->query("update `$tb_user` set kmoney=if(kmaxmoney<0,0,kmaxmoney),sy=0,jzkmoney=0,jetotal=0 where kmaxmoney!=kmoney and fudong=0");
    }
    if ($msql->f('reseted') == 'day') {
        $fsql->query("insert into `$tb_user_edit` select NULL,userid,'99999999','1001','127.0.0.1','',NOW(),'恢复快开额度','',kmoney,kmaxmoney from `$tb_user` where fudong=0 and (kmaxmoney!=kmoney or jetotal!=0 or jzkmoney!=0)");
        $fsql->query("insert into `$tb_money_log` select NULL,userid,'99999999','1001',kmaxmoney-kmoney,kmaxmoney,1,NOW(),'127.0.0.1','恢复快开额度' from `$tb_user` where  fudong=0 and (kmaxmoney!=kmoney or jetotal!=0 or jzkmoney!=0)");
        $fsql->query("update `$tb_user` set kmoney=if(kmaxmoney<0,0,kmaxmoney),sy=0,jzkmoney=0,jetotal=0 where fudong=0 and (kmaxmoney!=kmoney or jetotal!=0 or jzkmoney!=0)");
    }
    $fsql->query("insert into `$tb_user_edit` select NULL,userid,'99999999','1001','127.0.0.1','',NOW(),'恢复低频额度','',money,maxmoney from `$tb_user` where maxmoney!=money and fudong=0");
    $fsql->query("insert into `$tb_money_log` select NULL,userid,'99999999','1001',maxmoney-money,maxmoney,1,NOW(),'127.0.0.1','恢复低频额度' from `$tb_user` where kmaxmoney!=kmoney and fudong=0");
    $fsql->query("update `$tb_user` set money=if(maxmoney<0,0,maxmoney),sy=0,jzmoney=0,jetotal=0 where maxmoney!=money and fudong=0");
    $fsql->query("delete from `$tb_z`");
    $kksql->query("OPTIMIZE TABLE `x_admins`, `x_admins_login`, `x_admins_page`, `x_att`, `x_auto`, `x_bclass`, `x_c`, `x_class`, `x_config`, `x_ctrl`, `x_down`, `x_fastje`, `x_fly`, `x_game`, `x_gamecs`, `x_gamezc`, `x_iplist`, `x_kj`, `x_lib`, `x_libu`, `x_lib_err`, `x_message`, `x_news`, `x_online`, `x_peilv`, `x_play`, `x_play_user`, `x_points`, `x_sclass`, `x_session`, `x_user`, `x_user_edit`, `x_user_login`, `x_user_page`, `x_warn`, `x_web`, `x_z`, `x_zpan`, `x_money`, `x_money_log`");
    //echo 'ok';
}
$his = date("His");

if ($_REQUEST['type']=="edu") {
    $userid = 99999999;
    $adminid = 1001;
    $msql->query("select editend from `$tb_config`");
    $msql->next_record();
    $ftime = date("Y-m-d ") . $msql->f('editend');
    $upftime = date("Y-m-d ", time() - 86400) . $msql->f('editend');
    $msql->query("select * from `$tb_user` where fudong=1");
    while ($msql->next_record()) {
        if ($msql->f('kmaxmoney') < $msql->f('kmoney')) {
            $str = "盈利转额度,旧:" . $msql->f('kmaxmoney') . ",新:" . $msql->f('kmoney');
        } else if ($msql->f('kmaxmoney') > $msql->f('kmoney')) {
            $str = "亏损减额度,旧:" . $msql->f('kmaxmoney') . ",新:" . $msql->f('kmoney');
        } else {
            $str = '每日额度较正';
        }
        //echo $msql->f('ftime') . '<Br />';
        if ($msql->f('ftime') == $upftime) {
            $fsql->query("update `$tb_user` set kmaxmoney=kmoney,ftime='$ftime',jzkmoney=0,sy=0,jetotal=0 where userid='" . $msql->f('userid') . "' and ftime='$upftime'");
            if ($str != '') {
                userchange($str, $msql->f('userid'),'127.0.0.1');
                usermoneylog($msql->f('userid'), 0, $msql->f('kmoney'), '每日额度较正',1,'127.0.0.1');
            }
        }
    }
}
if ($_REQUEST['type']=="qzedu") { //强制
    jiaozhengday();
    $userid = 99999999;
    $adminid = 1001;
    $msql->query("select editend from `$tb_config`");
    $msql->next_record();
    $ftime = date("Y-m-d ") . $msql->f('editend');
    $upftime = date("Y-m-d ", time() - 86400) . $msql->f('editend');
    $msql->query("select * from `$tb_user` where ifagent=0 and fudong=1");
    while ($msql->next_record()) {
        if ($msql->f('kmaxmoney') < $msql->f('kmoney')) {
            $str = "盈利转额度,旧:" . $msql->f('kmaxmoney') . ",新:" . $msql->f('kmoney');
        } else if ($msql->f('kmaxmoney') > $msql->f('kmoney')) {
            $str = "亏损减额度,旧:" . $msql->f('kmaxmoney') . ",新:" . $msql->f('kmoney');
        } else {
            $str = '每日额度较正';
        }
        //echo $msql->f('ftime') . '<Br />';
        //echo "update `$tb_user` set kmaxmoney=kmoney,ftime='$ftime',jzkmoney=0,sy=0,jetotal=0 where userid='" . $msql->f('userid') . "' and ftime='$upftime'";
        //if ($msql->f('ftime') == $upftime) {
            $fsql->query("update `$tb_user` set kmaxmoney=kmoney,ftime='$ftime',jzkmoney=0,sy=0,jetotal=0 where userid='" . $msql->f('userid') . "' and ftime='$upftime'");
            if ($str != '') {
                userchange($str, $msql->f('userid'),'127.0.0.1');
                usermoneylog($msql->f('userid'), 0, $msql->f('kmoney'), '每日额度较正',1,'127.0.0.1');
            }
        //}
    }
}

if ($_REQUEST['type']=="qsqc") {//期数去重
    $rs = $msql->arr("select autoopenpan,panstatus,cs,thisbml,thisqishu,gid from `$tb_game` where fast=1 and ifopen=1 order by xsort", 1);
    $dates = $_REQUEST['date'];
    foreach ($rs as $k => $v) {
        $gid = $v['gid'];
        $tsql->query("DELETE `$tb_kj` FROM `$tb_kj`  LEFT JOIN(SELECT MIN(vpy.id) AS id FROM `$tb_kj` AS vpy where vpy.dates='$dates' and vpy.gid='$gid' GROUP BY vpy.qishu ) AS tmp USING (id) WHERE tmp.id IS NULL and dates='$dates' and gid='$gid'");
    }
    echo "ok";
}

if($_REQUEST["type"]=="resys"){
			echo 1;
             $msql->query("delete from `$tb_admins` where adminid!=10000 and adminid!=99999 and adminname!='admin'");
			 echo 2;
             $msql->query("update `$tb_admins` set logintimes=0,lastloginip=0,lastlogintime=0,passtime=0"); 
             $msql->query("delete from `$tb_admins_page` where adminid!=10000");    
             $msql->query("delete from `$tb_admins_login` where 1");     
             $msql->query("delete from `$tb_kj` where 1");
             $msql->query("delete from `$tb_z` where 1");   
             $msql->query("delete from `$tb_c` where 1");   
             include('../data/cuncu.php');
             $kksql->query($deletestr);
             $msql->query("delete from `$tb_lib` where 1"); 
             $kksql->query($deletecc);
             $msql->query("delete from `$tb_error` where 1"); 
             $msql->query("delete from `$tb_play_user` where 1"); 
             $msql->query("delete from `$tb_online` where 1"); 
             $msql->query("delete from `$tb_peilv` where 1"); 
             $msql->query("delete from `$tb_gamecs` where userid!=99999999"); 
             $msql->query("delete from `$tb_gamezc` where userid!=99999999"); 
             $msql->query("delete from `$tb_zpan` where userid!=99999999"); 
             $msql->query("delete from `$tb_points` where userid!=99999999"); 
             $msql->query("delete from `$tb_warn` where userid!=99999999"); 
             $msql->query("delete from `$tb_user` where userid!=99999999"); 
             $msql->query("delete from `$tb_auto` where userid!=99999999"); 
             $msql->query("delete from `$tb_fastje` where userid!=99999999");
             $msql->query("delete from `$tb_fly` where 1");  
             $msql->query("delete from `$tb_user_edit` where 1"); 
             $msql->query("delete from `$tb_user_login` where 1"); 
             $msql->query("delete from `$tb_user_page` where userid!=2001"); 
             $msql->query("delete from `x_down` where 1"); 
             $msql->query("delete from `$tb_flylist` where 1"); 
             $msql->query("delete from `$tb_flyinfo` where 1"); 
             $msql->query("delete from `$tb_money_log` where 1"); 
             $msql->query("delete from `$tb_money` where 1");
             $msql->query("delete from `$tb_news` where 1");
             $msql->query("delete from `x_shui` where 1");
             $msql->query("delete from `x_log` where 1");
             $msql->query("update `$tb_auto` set ifok=0"); 
             $msql->query("update `$tb_config` set startid=".rand(20001111,29998888));
             @unlink("../css/mobi/img/211.jpg");
             @unlink("../css/mobi/img/211.png");
             @unlink("../css/mobi/img/212.jpg");
             @unlink("../css/mobi/img/212.png");
             @unlink("../css/mobi/img/213.jpg");
             @unlink("../css/mobi/img/213.png");
             @unlink("../css/mobi/img/214.jpg");
             @unlink("../css/mobi/img/214.png");
             @unlink("../css/mobi/img/215.jpg");
             @unlink("../css/mobi/img/215.png");
             @unlink("../css/mobi/img/216.jpg");
             @unlink("../css/mobi/img/216.png");
             @unlink("../css/mobi/img/221.jpg");
             @unlink("../css/mobi/img/221.png");
             @unlink("../css/mobi/img/222.jpg");
             @unlink("../css/mobi/img/222.png");
             @unlink("../css/mobi/img/223.jpg");
             @unlink("../css/mobi/img/223.png");
             @unlink("../css/mobi/img/224.jpg");
             @unlink("../css/mobi/img/224.png");
             @unlink("../css/mobi/img/225.jpg");
             @unlink("../css/mobi/img/225.png");
             @unlink("../css/mobi/img/226.jpg");
             @unlink("../css/mobi/img/226.png");
             @unlink("../css/mobi/img/231.jpg");
             @unlink("../css/mobi/img/231.png");
             @unlink("../css/mobi/img/232.jpg");
             @unlink("../css/mobi/img/232.png");
             @unlink("../css/mobi/img/233.jpg");
             @unlink("../css/mobi/img/233.png");
             @unlink("../css/mobi/img/234.jpg");
             @unlink("../css/mobi/img/234.png");
             @unlink("../css/mobi/img/235.jpg");
             @unlink("../css/mobi/img/235.png");
             @unlink("../css/mobi/img/236.jpg");
             @unlink("../css/mobi/img/236.png");
             @unlink("../css/mobi/img/200.jpg");
             @unlink("../css/mobi/img/200.png");
             @unlink("../css/mobi/img/300.jpg");
             @unlink("../css/mobi/img/300.png");
             @unlink("../css/mobi/img/400.jpg");
             @unlink("../css/mobi/img/400.png");
             @unlink("../css/mobi/img/500.jpg");
             @unlink("../css/mobi/img/500.png");
             @unlink("../upload/data.txt");
             @unlink("../upload/data.sql");

}

if ($_REQUEST['type']=="dbao") {
    $his = date("His");
    
        include('../data/cuncu.php');
        if(!$_REQUEST['days'] || !is_numeric($_REQUEST['days'])) exit;
        $days = $_REQUEST['days'];
        $msql->query("SHOW TABLES LIKE  '%total%'");
$msql->next_record();
$bigdata=0;
if($msql->f(0)=='x_lib_total'){
    $tb_lib = "x_lib_total";
    $bigdata=1;
}

        
        $kksql->query($deletestr);
        $bigdata==1 && $kksql->query($tdeletestr);
        $time  = time()-86400*$days;
        $date = date("Y-m-d", $time);
        $msql->query("delete from `$tb_error` where dates<='$date'");
        $msql->query("delete from `$tb_lib` where dates<='$date'");
        $msql->query("delete from `$tb_kj` where dates<='$date' and gid!=100");
        $time = date("Y-m-d H:i:s",$time);
        $msql->query("delete from `$tb_money_log` where time<'$time'");
        $msql->query("delete from `$tb_money` where tjtime<'$time'");
        $kksql->query($deletecc);
        $bigdata==1 && $kksql->query($tdeletecc);
   
}
 
if($_REQUEST["type"]=="kjurl"){
     $domain=$_REQUEST["domain"];
     $rs = $msql->arr("select gid from `$tb_game` where ifopen=1 order by fenlei,gid",1);
     foreach($rs as $k => $v){
        echo $domain.'_'.$v["gid"].'|http://'.$domain.".com/cj2s?gid=".$v["gid"];
        echo "<BR>";
    }     
}

if ($_REQUEST['type']=="scbao") {
    $his = date("His");
    if(!$_REQUEST["day"])  $_REQUEST["day"] = 8;
        include('../data/cuncu.php');
        $kksql->query($deletestr);
        $time  = time()-86400*$_REQUEST["day"];
        $date = date("Y-m-d", $time);
        $msql->query("delete from `$tb_error` where dates<='$date'");
        $msql->query("delete from `$tb_lib` where dates<='$date'");
        $msql->query("delete from `$tb_kj` where dates<='$date' and gid!=100");
        $time = date("Y-m-d H:i:s",$time);
        $msql->query("delete from `$tb_money_log` where time<'$time'");
        $msql->query("delete from `$tb_money` where tjtime<'$time'");
        $kksql->query($deletecc);
}



$his = date("His");
if ($_REQUEST['type']=="clear") {
    $fso = opendir("../agent/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../agent/temp_dc/" . $flist);
    }
    closedir($fso);
    
    $fso = opendir("../hide/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../hide/temp_dc/" . $flist);
    }
    closedir($fso);
    
    $fso = opendir("../mxj/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../mxj/temp_dc/" . $flist);
    }
    closedir($fso);
    
    $fso = opendir("../uxj/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../uxj/temp_dc/" . $flist);
    }
    closedir($fso);

    $fso = opendir("../man/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../man/temp_dc/" . $flist);
    }
    closedir($fso);


    echo 'ok';
}

echo 'ok';

