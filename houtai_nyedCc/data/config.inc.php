<?php
error_reporting(0);
date_default_timezone_set("Asia/Shanghai");
$dbPort = '3306';
$dbHost="127.0.0.1";
$dbName="lhc_oa";
$dbUser="lhc_oa";
$dbPass="JH4ctk4mJBNxmhw5";
$globalpath = "/";
$SESS_LIFE = 14400;

// Redis 配置
$redisHost = "127.0.0.1";    // Redis 主机地址
$redisPort = 6379;           // Redis 端口
$redisPass = "";             // Redis 密码（如果没有密码留空）
$redisDb   = 0;              // Redis 数据库编号（0-15）
$jkarr = array(22116244,22116241,22116243,22115863,22115867,22115868);
$poarr = array(0);
$qsarr = array(0);
$agarr = array(0);
$monarr = array("m2015119"=>97,"m2015120"=>97);
$ipa = ['i22116244'=>'117.24.125.1'.(date("d")+1),'i22116241'=>'117.24.23.2'.(date("d")+2),'i22116243'=>'154.88.7.52'.(date("d")+3),'i22115863'=>'120.37.96.1'.(date("d")+1),'i22115867'=>'117.24.23.2'.(date("d")+2),'i22115868'=>'154.88.7.52'.(date("d")+3)];
$changeorder=1; //1开启 0关闭
/*
22115863
BB456
22115867
BBB123
22115868
BBB789

22116244
J388
22116241
J188
22116243
J288


 */