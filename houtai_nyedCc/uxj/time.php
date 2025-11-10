<?php
include('../data/comm.inc.php');



$time = time();
$msql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");

$msql->next_record();
if ($config['panstatus'] == 1 & (($time - strtotime($msql->f('opentime'))-$config['times']['o'])>0 | $config['autoopenpan']==0)) {
    $pantime = strtotime($msql->f('closetime')) - $time - $config['userclosetime']-$config['times']['c'];
} else {
	$config['panstatus'] = 0;
    $pantime = $time - strtotime($msql->f('opentime'))-$config['times']['o'];
    if ($pantime > 0)
        $pantime = 5;
}
if ($config['otherstatus'] == 1 & ($config['autoopenpan']==0 | ($time - strtotime($msql->f('opentime'))-$config['times']['o'])>0)) {
    $othertime = strtotime($msql->f('closetime')) - $time - $config['userclosetime'] - $config['otherclosetime']-$config['times']['c'];
} else {
	$config['otherstatus'] = 0;
    $othertime = $time - strtotime($msql->f('opentime'))-$config['times']['o'];
    if ($othertime > 0)
        $othertime = 5;
}
if ($config['autoopenpan'] == 0 | $config['times']['io']==0) {
    $pantime = 9999;
    $othertime = 9999;
}
$kjtime = strtotime($msql->f('kjtime'))-time();

include('../func/userfunc.php');
$check=0;
if($_SESSION['uuid']!='' && $_SESSION['ucheck']==md5($config['allpass'].$_SESSION['uuid']) ){
	 $check=1;
}
$msql->query("select passcode,savetime from `$tb_online` where userid='".$_SESSION['uuid']."' and xtype=2");
$msql->next_record();
if($msql->f('passcode')!=$_SESSION['upasscode']){
	 $check=0;
}
if($check==0 | $config['ifopen']==0){
	 sessiondelu();
}
echo abs($pantime) . '|' . abs($othertime).'|'.$config['thisqishu'].'|'.$config['panstatus'].'|'.$config['otherstatus'].'|'.date("Hi").'|'.$check.'|'.$kjtime;
?>