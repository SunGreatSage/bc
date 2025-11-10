<?php
if ($_POST['xtype'] != 'getopen')
    exit;
include('../data/comm.inc.php');
$time  = time();
$check = 0;
if ($_SESSION['uid'] != '' & $_SESSION['check'] == md5($config['allpass'] . $_SESSION['uid'])) {
    $check = 1;
}
if ($check == 0) {
    echo 'err';
    exit;
}
$msql->query("select opentime,closetime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
$msql->next_record();

if ($config['panstatus'] == 1) {
    $pantime = strtotime($msql->f('closetime')) - $time;
} else {
    $pantime = $time - strtotime($msql->f('opentime'));
    if ($pantime > 0)
        $pantime = 0;
}
if ($config['otherstatus'] == 1) {
    $othertime = strtotime($msql->f('closetime')) - $time - $config['otherclosetime'];
} else {
    $othertime = $time - strtotime($msql->f('opentime'));
    if ($othertime > 0)
        $othertime = 0;
}
if ($config['autoopenpan'] == 0) {
    $pantime   = 9999;
    $othertime = 9999;
}
echo abs($pantime) . '|' . abs($othertime) . '|' . $config['thisqishu'] . '|' . $config['panstatus'] . '|' . $config['otherstatus'];