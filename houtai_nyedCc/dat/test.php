<?php
include("../data/config.inc.php");
include("../global/db.inc.php");
include("../global/Iplocation_Class.php");
$ips = new IpLocation("../dat/QQWry.Dat");

$rs   = $msql->arr("select `ip`,id from `x_user_login` where addr=''", 1);
foreach ($rs as $key => $val) {
	echo $val['ip'];
    echo $addr = $ips->getaddress($val['ip']);
    $id   = $val['id'];
    $msql->query("update `x_user_login` set addr='$addr' where id='$id'");
}

echo $ips->getaddress("211.97.131.245");
?>