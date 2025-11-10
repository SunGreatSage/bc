<?php
error_reporting(E_ALL);
date_default_timezone_set("Asia/Shanghai");
include('../data/config.inc.php');
include('../data/db.php');
include('../global/db.inc.php');
include("../func/func.php");
include("../func/csfunc.php");
include("../func/adminfunc.php");
if ($_REQUEST['type'] != 'kj') {
    exit;
}
$gid= $_REQUEST['gid'];
$garr = [108,109,110,165,171,172,173,175];

$msql->query("select editstart,editend from `$tb_config`");
$msql->next_record();
if(date("His")<str_replace(':','',$msql->f("editstart"))){
    $date = date("Y-m-d",time()-86400);
}else{
    $date = date("Y-m-d");
}
if($_REQUEST['date']){
    $date = $_REQUEST['date'];
}
$num=200;
if($_REQUEST['num']){
    $num = $_REQUEST['num'];
}
$time = sqltime(time());

//if(!in_array($gid,$garr)) exit;
//$mnum = transgame($gid,'mnum');
$msql->query("select gname,sgname,mnum,cs,fast,xsort from `$tb_game` where gid='$gid'");
$msql->next_record();
$xsort = $msql->f("xsort");
$mnum=$msql->f("mnum");
$gname=$msql->f('gname');
$sgname = $msql->f('sgname');
$cs= json_decode($msql->f('cs'),true);
if($msql->f('fast')==1){
    $msql->query("select * from `$tb_kj` where gid='$gid' and dates='$date' and kjtime<='$time' and m".$mnum."!='' order by gid,qishu desc limit $num");
}else{
    $msql->query("select * from `$tb_kj` where gid='$gid' and kjtime<='$time' and m".$mnum."!='' order by gid,qishu desc limit $num");
}

$arr=[];
$i=0;
while ($msql->next_record()) {
    $arr[$i]['gid'] = $msql->f('gid');
    $arr[$i]['qishu'] = $msql->f('qishu');
    $arr[$i]['kjtime'] = $msql->f('kjtime');
    $arr[$i]['date'] = $msql->f('dates');
    $kj_num = [];
    for($j=1;$j<=$mnum;$j++){
        $kj_num[] = $msql->f('m'.$j);
    }
    $arr[$i]['kj_num'] = $kj_num;
    $i++;
}
$arr[0]['name'] = $gname;
$arr[0]['sname'] = $sgname;
$arr[0]['cs'] = $cs;
$arr[0]['mnum'] = $mnum;
$arr[0]['xsort'] = $xsort;
echo json_encode($arr);