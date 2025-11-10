<?php
$_SERVER['REMOTE_ADDR']='1.1.1.1';
error_reporting(0);
set_time_limit(0);
date_default_timezone_set("Asia/Shanghai");
include('../data/config.inc.php');
include('../data/db.php');
include('../global/db.inc.php');
include("../func/func.php");
include("../func/csfunc.php");
include("../func/adminfunc.php");
include("../func/js.php");
// if ($_REQUEST['admin'] != 'toor') {
//     exit;
// }

$msql->query("select autoopenpan,autoopenpantime from `$tb_ctrl`");
$msql->next_record();
$time = time();
if ($msql->f('autoopenpan') == 1 & ($time - strtotime($msql->f('autoopenpantime'))) < 10) {
    exit;
}
$timek = date("Y-m-d H:i:s");
$msql->query("update `$tb_ctrl` set autoopenpan=1,autoopenpantime=NOW()");

$msql->query("select editstart,editend,livetime from `{$tb_config}` ");
$msql->next_record();
if (date("His") < str_replace(':', '', $msql->f('editstart'))) {
    $dates = date("Y-m-d", time() - 86400);
} else {
    $dates = date("Y-m-d");
}

/****删除超时用户*****/
//

if (substr($time, -1) % 5 == 0) {
    $livetime = date("Y-m-d H:i:s",time()-60*$msql->f('livetime'));
    $msql->query("select userid from `{$tb_online}`  where  savetime<'$livetime'");
    while ($msql->next_record()) {
        $uid = $msql->f('userid');
        $fsql->query("update `$tb_user` set online=0 where userid='$uid'");
        $fsql->query("delete from `$tb_online` where userid='$uid'");
    }
    $msql->query("update `$tb_user` set online=0 where userid not in(select userid from `$tb_online`)");
}
/****删除超时用户*****/

/****慢开*****/
$msql->query("select gname,autoopenpan,panstatus,otherstatus,otherclosetime,cs,thisbml,thisqishu,gid,fenlei from `$tb_game` where fast=0   and ifopen=1 order by xsort"); //and autoopenpan=1
while ($msql->next_record()) {
    $gid = $msql->f('gid');
	$gname=$msql->f('gname');
    $qishu = $msql->f('thisqishu');
    $time = sqltime(time());
    $fsql->query("select * from `$tb_kj` where qishu='$qishu' and gid='$gid' and closetime>'$time' order by closetime limit 1");
    //echo "select * from `$tb_kj` where qishu='$qishu' and gid='$gid' and closetime>'$time' order by closetime limit 1";exit;
    $fsql->next_record();
    if ($fsql->f('id') == '') {
        $tsql->query("update `$tb_game` set otherstatus=0,panstatus=0 where gid='$gid'");
        $fsql->query("select * from `$tb_kj` where gid='$gid' and closetime>'$time' order by  qishu  limit 1");
        $fsql->next_record();
        $his = date("hi");
        if ($fsql->f('qishu') != '' & ($his < 2130 | $his > 2200)) {
            $fsql->query("update `$tb_game` set thisqishu='" . $fsql->f('qishu') . "',panstatus=0,otherstatus=0 where gid='$gid'");
        }
    } else {
        if ($msql->f('autoopenpan') == 1) {
            $time = time();
            if ($msql->f('panstatus') == 0) {
                if (strtotime($fsql->f('opentime')) <= $time & strtotime($fsql->f('closetime')) > $time) {
                    $tsql->query("update `$tb_game` set panstatus=1 where gid='$gid'");
					//清缓存
					$cacheKeys = $redis->keys("zd:{$gid}*");
                    $redis->del($cacheKeys);
                }
            }
            if ($msql->f('panstatus') == 1) {
                if (strtotime($fsql->f('opentime')) > $time | strtotime($fsql->f('closetime')) < $time) {
                    $tsql->query("update `$tb_game` set panstatus=0 where gid='$gid'");
                }
            }

            if ($msql->f('otherstatus') == 0) {
                if (strtotime($fsql->f('opentime')) <= $time & (strtotime($fsql->f('closetime')) - $msql->f('otherclosetime')) > $time) {
                    $tsql->query("update `$tb_game` set otherstatus=1 where gid='$gid'");
                }
            }
            if ($msql->f('otherstatus') == 1) {
                if (strtotime($fsql->f('opentime')) > $time | (strtotime($fsql->f('closetime')) - $msql->f('otherclosetime')) < $time) {
                    $tsql->query("update `$tb_game` set otherstatus=0 where gid='$gid'");
                }
            }

        }
    }

    if ($msql->f('panstatus') == 1) {
		
        attpeilvs($gid);
    }
	echo "慢开【".$gname."】盘口加载成功<br>";
	
}
/****慢开end*****/


/****快开start*****/
$rs = $msql->arr("select autoopenpan,panstatus,cs,thisbml,thisqishu,gid,upqishu from `$tb_game` where fast=1 and ifopen=1 order by xsort", 1);

foreach ($rs as $k => $v) {
    $gid = $v['gid'];
	$gname=$v['gname'];
    /*
    if ($gid == 162) {
    
    $his = date("His");
    if ($his > 60000 & $his < 235800) {
    if ($v['autoopenpan'] == 1) {
    $fsql->query("update `$tb_game` set autoopenpan=0 where gid=162");
    }
    if ($v['panstatus'] == 1) {
    $fsql->query("update `$tb_game` set panstatus=0,otherstatus=0 where gid=162");
    }
    continue;
    }
    if ($his < 60000  | $his > 235800) {//$his <= 215600 & $his >= 215300
    if ($v['autoopenpan'] == 0) {
    $fsql->query("update `$tb_game` set autoopenpan=1 where gid=162");
    }
    }
    
    }
    */
    echo 9;
    $qishu = $v['thisqishu'];
    $time = sqltime(time());
    $timen = time();
    $his = date("His");
    $fsql->query("select * from `$tb_kj` where gid='$gid' and qishu='$qishu' and closetime>'$time' ");
    $fsql->next_record();

    /*
    if($gid==163 & ($his>235800 | $his<60000)){
    if(date("His",$fsql->f('opentime'))==90000){
    paddqishu($gid);
    $fsql->query("update `$tb_game` set thisqishu=(select qishu from `$tb_kj` where gid='$gid' and closetime>$time order by kjtime limit 1) where gid='$gid'");
    continue;
    }
    }
    */
    //echo "select * from `$tb_kj` where qishu='$qishu' and gid='$gid' and closetime>'$time' ";
    if ($fsql->f('id') == '') {
        if ($gid == 1620 | ($gid == 1630 & ($his > 235800 | $his < 50000))) {
            $cs = json_decode($v['cs'], true);
            $msql->query("select kjip,editstart,editend from `{$tb_config}` ");
            $msql->next_record();
            $kjip = $msql->f('kjip');
            $editstart = str_replace(':', '', $msql->f('editstart'));
            $editend = str_replace(':', '', $msql->f('editend'));
            if ($his >= $editstart & $his <= $editend)
                continue;
            $url = "http://" . $kjip . "/ssc/kj.php?enter=kj&gid=162";
            //echo $url;
            $kj = file_get_contents($url);
            $kj = json_decode($kj, true);
            $qishu = $kj['qishu'] + 1;
            $opentime = $kj['kjtime'] - rand(1, 10);
            $closetime = $opentime + 110;
            $kjtime = $closetime + 30;

            $msql->query("select 1 from `$tb_kj` where gid='$gid' and qishu='$qishu'");
            $msql->next_record();
            echo 2;
            if ($msql->f(0) != 1) {
                $msql->query("insert into `$tb_kj` set opentime='" . sqltime($opentime) . "',closetime='" . sqltime($closetime) . "',kjtime='" . sqltime($kjtime) . "',baostatus='1',gid='$gid',qishu='$qishu'");
                $msql->query("update `$tb_game` set thisqishu='$qishu',panstatus=0,otherstatus=0 where gid='$gid'");
            }
        }
        $tsql->query("update `$tb_game` set otherstatus=0,panstatus=0 where gid='$gid'");
        $fsql->query("select * from `$tb_kj` where gid='$gid' and dates='{$dates}' and closetime>'$time' order by  qishu  limit 1");

        $fsql->next_record();
        if ($fsql->f('id') == '') {
            if ($gid == 111 | $gid == 115 | $gid == 133) {
              
                $endqishu = 84;
                $fsql->query("select * from `$tb_kj` where gid='$gid' and UNIX_TIMESTAMP(closetime)>$timen-300 order by  qishu desc limit 1");
                $fsql->next_record();
                if ($fsql->f('id') == '' | substr($fsql->f('qishu'), -2) == $endqishu) {
                    paddqishu($gid, date("Y-m-d"));
                } else {
                    $cs = json_decode($v['cs'], true);
                    $bml = $v['thisbml'];
                    $opentime = $fsql->f('kjtime');
                    $kjtime = $opentime + $cs['qsjg'] * 60;
                    $closetime = $kjtime - $cs['closetime'];
                    $qishu = $fsql->f('qishu') + 1;
                    /*********jiari*************/
                    $jiari = date("Ymd", $kjtime);
                    if ($jiari >= 20160207 & $jiari <= 20160213 & $jiari != '')
                        continue;
                    /*********jiari*************/
                    $tsql->query("insert into `x_kj` set opentime='" . sqltime($opentime) . "',closetime='" . sqltime($closetime) . "',kjtime='" . sqltime($kjtime) . "',qishu='$qishu',bml='$bml',gid='$gid',baostatus=1");
                }
            } else if ($gid == 107 | $gid == 161 | $gid == 162 | $gid == 163 | $gid == 153  | $gid == 109  | $gid == 171  | $gid == 108  | $gid == 172 | $gid == 175) {
                paddqishu($gid);
            } else if ($gid == 135 | $gid == 101 | $gid == 113) {
                $his = date("His");
                $time = time();
                if ($gid == 135 & $his <= 20500) {
                    $time = $time - 86400;
                }
                if ($gid == 113 & $his <= 20000) {
                    $time = $time - 86400;
                }
                if ($gid == 101 & $his <= 15500) {
                    $time = $time - 86400;
                }
                paddqishu($gid, date("Y-m-d", $time));
            } else {
                $cs = json_decode($v['cs'], true);
                $sl = -3;
                if ($gid == 103 | $gid == 151 | $gid == 152 | $gid == 121 | $gid == 123 | $gid == 125) {
                    $sl = -2;
                }

                $fsql->query("select qishu,kjtime from `$tb_kj` where gid='$gid' order by  qishu desc limit 1");
                $fsql->next_record();
                if ($sl == -2) {
                    if (substr($fsql->f('qishu'), $sl) == $cs['qsnums']) {
                        paddqishu($gid, date("Y-m-d", strtotime($fsql->f("kjtime")) + 86400));
                    } else {
                        paddqishu($gid, date("Y-m-d"));
                    }
                } else {
                    if (date("His") <= 60000) {
                        paddqishu($gid, date("Y-m-d", time() - 86400));
                    } else {
                        paddqishu($gid, date("Y-m-d"));
                    }


                }

                echo $gid, "<br />";
            }
            $tsql->query("select * from `$tb_kj` where gid='$gid' and closetime>'$time' order by  qishu  limit 1");
            $tsql->next_record();
            if ($tsql->f('id') != '') {
                $kjtime=strtotime($tsql->f("opentime"));
                $tsql->query("update `$tb_game` set upqishu=thisqishu,kjtime='$kjtime',thisqishu='" . $tsql->f('qishu') . "',panstatus=0,otherstatus=0 where gid='$gid'");
            }
        } else {
            $kjtime=strtotime($fsql->f("opentime"));
            $tsql->query("update `$tb_game` set upqishu=thisqishu,kjtime='$kjtime',thisqishu='" . $fsql->f('qishu') . "',panstatus=0,otherstatus=0 where gid='$gid'");
        }
    } else {
        if ($v['autoopenpan'] == 1) {
            $time = time();
            if ($v['panstatus'] == 0) {
                if (strtotime($fsql->f('opentime')) <= $time & strtotime($fsql->f('closetime')) > $time) {
                    $tsql->query("update `$tb_game` set panstatus=1,otherstatus=1 where gid='$gid'");
                }
            }
            if ($v['panstatus'] == 1) {
                if (strtotime($fsql->f('opentime')) > $time | strtotime($fsql->f('closetime')) < $time) {
                    $tsql->query("update `$tb_game` set panstatus=0,otherstatus=0,upqishu=thisqishu where gid='$gid'");
                }
            }
        }
    }
    if ($v['panstatus'] == 1) {
        attpeilvs($gid);
    }
	echo "快开【".$gname."】盘口加载成功<br>";
}

$his = date("His");
if ($his ==80000) {
    $dates = date("Y-m-d");
    foreach ($rs as $k => $v) {
        $gid = $v['gid'];
        $tsql->query("DELETE `$tb_kj` FROM `$tb_kj`  LEFT JOIN(SELECT MIN(vpy.id) AS id FROM `$tb_kj` AS vpy where vpy.dates='$dates' and vpy.gid='$gid' GROUP BY vpy.qishu ) AS tmp USING (id) WHERE tmp.id IS NULL and dates='$dates' and gid='$gid'");
    }
}



/****快开end*****/



/***********搜索地址*********/
include("../global/Iplocation_Class.php");
$time = sqltime(time() - 3600);
$rs = $msql->arr("select `modiip`,id from `$tb_user_edit` where moditime>'$time' and addr=''", 1);
foreach ($rs as $key => $val) {
    $addr = mb_convert_encoding($ips->getaddress($val['modiip']), 'utf-8', 'GBK');
    $id = $val['id'];
    $msql->query("update `$tb_user_edit` set addr='$addr' where id='$id'");
}
$time = sqltime(time() - 3600);
$rs = $msql->arr("select `ip`,id from `$tb_user_login` where time>'$time' and addr=''", 1);
foreach ($rs as $key => $val) {
    $addr = mb_convert_encoding($ips->getaddress($val['ip']), 'utf-8', 'GBK');
    $id = $val['id'];
    $msql->query("update `$tb_user_login` set addr='$addr' where id='$id'");
}
/***********搜索地址*********/

$his = date("His");
if ($his > 61200 & $his < 61500) {
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
    }*/

}
$his = date("His");
//$his = 61612;
if ($his > 61600 & $his < 61800) {
    $msql->query("select * from `$tb_config`");
    $msql->next_record();

    $time = time();
    $fsql->query("update `$tb_user` set sy=0,yingdeny=0 ");
    if ($msql->f('reseted') == 'week' & date('w') == 1) {
        $fsql->query("insert into `$tb_user_edit` select NULL,userid,'99999999','1001','127.0.0.1','',NOW(),'恢复快开额度','',kmoney,kmaxmoney from `$tb_user` where kmaxmoney!=kmoney and fudong=0");
        $fsql->query("insert into `$tb_money_log` select NULL,userid,'99999999','1001',kmaxmoney-kmoney,kmaxmoney,1,NOW(),'127.0.0.1','恢复快开额度' from `$tb_user` where kmaxmoney!=kmoney and fudong=0");
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

    $kksql->query("OPTIMIZE TABLE `x_admins`, `x_admins_login`, `x_admins_page`, `x_att`, `x_auto`, `x_bclass`, `x_c`, `x_class`, `x_config`, `x_ctrl`, `x_down`, `x_fastje`, `x_fly`, `x_game`, `x_gamecs`, `x_gamezc`, `x_iplist`, `x_kj`, `x_lib`, `x_libu`, `x_lib_err`, `x_message`, `x_news`, `x_online`, `x_peilv`, `x_play`, `x_play_user`, `x_points`, `x_sclass`, `x_session`, `x_user`, `x_user_edit`, `x_user_login`, `x_user_page`, `x_warn`, `x_web`, `x_z`, `x_zpan`, `x_money`, `x_money_log`");


}
$his = date("His");

if ($his > 61000 & $his < 61100) {
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
        if ($msql->f('ftime') == $upftime) {
            $fsql->query("update `$tb_user` set kmaxmoney=kmoney,ftime='$ftime',jzkmoney=0,sy=0,jetotal=0 where userid='" . $msql->f('userid') . "' and ftime='$upftime'");
            if ($str != '') {
                userchange($str, $msql->f('userid'),'127.0.0.1');
                usermoneylog($msql->f('userid'), 0, $msql->f('kmoney'), '每日额度较正',1,'127.0.0.1');
            }
        }
    }

}
$his = date("His");
if ($his > 60930 & $his < 60950) {
    jiaozhengday();
}
$his = date("His");
if (($his > 70000 & $his < 70100) | ($his > 160000 & $his < 160100)) {
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


}


if(date("w")==3){
    $his = date("His");
    if ($his > 60300 & $his < 60900) {
        include('../data/cuncu.php');
        $msql->query("select autodellogintime from `$tb_config`");
        $msql->next_record();
        $days = $msql->f('autodellogintime');
        $msql->query("SHOW TABLES LIKE  '%total%'");
$msql->next_record();
$bigdata=0;
if($msql->f(0)=='x_lib_total'){
    $tb_lib = "x_lib_total";
    $bigdata=1;
}
        
        @$kksql->query($deletestr);
        $bigdata==1 && @$kksql->query($tdeletestr);
        $time  = time()-86400*$days;
        $date = date("Y-m-d", $time);
        $msql->query("delete from `$tb_error` where dates<='$date'");
        $msql->query("delete from `$tb_lib` where dates<='$date'");
        $msql->query("delete from `$tb_kj` where dates<='$date' and gid!=100");
        $time = date("Y-m-d H:i:s",$time);
        $msql->query("delete from `$tb_money_log` where time<'$time'");
        $msql->query("delete from `$tb_peilv` where time<'$time'");
        $msql->query("delete from `$tb_flylist` where time<'$time'");
        $msql->query("delete from `$tb_money` where tjtime<'$time'");
        @$kksql->query($deletecc);
        $bigdata==1 && @$kksql->query($tdeletecc);
        $time  = time()-86400*7;
        $msql->query("delete from `$tb_log` where time<'$time'");

    }
}
if(date("w")==3){
    $his = date("His");
    if ($his > 60700 & $his < 60800) {
        $time  = time()-86400*25;
        $date = date("Y-m-d", $time);
        $msql->query("select * from `$tb_user`");
        while($msql->next_record()){
            if($msql->f('userid')==99999999) continue;
            $regtime  = $date." ".substr($msql->f("regtime"),-8);
            $fsql->query("update `$tb_user` set regtime='$regtime' where userid='".$msql->f('userid')."' and regtime<'$regtime'");
        }
        $msql->query("select moneytype from `$tb_config`");
        $msql->next_record();
        if($msql->f('moneytype')==0){
            //$msql->query("update `$tb_user` set status=0 where ifagent=0 and lastlogintime<regtime");
        }     
        $msql->query("update `$tb_admins` set logintimes=0");
    }
}

if ($his > 60000 & $his < 60030) {
    $msql->query("update `$tb_lib` set z=7,kk=1 where z=9");
    jiaozhengedu();
}


$time = time();
$msql->query("update `$tb_ctrl` set autoopenpan=0,autoopenpantime=NOW()");
echo '
<script language="JavaScript"> 
function myrefresh() 
{ 
window.location.reload(); 
} 
setTimeout(\'myrefresh()\',10000); //指定1秒刷新一次 
</script>';