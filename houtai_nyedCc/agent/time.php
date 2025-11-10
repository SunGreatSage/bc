<?php

include('../data/comm.inc.php');
$time  = time();
$check = 0;
if ($_SESSION['auid2'] != '' && $_SESSION['acheck'] == md5($config['allpass'] . $_SESSION['auid2'])) {
    $check = 1;
}
if ($check == 0 | $config['ifopen'] == 0 | $config['ifopens'] == 0) {
    echo 'err';
    exit;
}
$msql->query("select opentime,closetime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
$msql->next_record();
if ($config['panstatus'] == 1 & (($time - strtotime($msql->f('opentime')) - $config['times']['o']) > 0 | $config['autoopenpan'] == 0)) {
    $pantime = strtotime($msql->f('closetime')) - $time - $config['times']['c'];
} else {
    $config['panstatus'] = 0;
    $pantime             = $time - strtotime($msql->f('opentime')) - $config['times']['o'];
    if ($pantime > 0)
        $pantime = 3;
}
if ($config['otherstatus'] == 1 & ($config['autoopenpan'] == 0 | ($time - strtotime($msql->f('opentime')) - $config['times']['o']) > 0)) {
    $othertime = strtotime($msql->f('closetime')) - $time - $config['otherclosetime'] - $config['times']['c'];
} else {
    $config['otherstatus'] = 0;
    $othertime             = $time - strtotime($msql->f('opentime')) - $config['times']['o'];
    if ($othertime > 0)
        $othertime = 3;
}

$fsql->query("select layer,cssz,status from `$tb_user` where userid='" . $_SESSION['auid'] . "'");
$fsql->next_record();
if ($fsql->f('layer') == 1) {
	
    if ($config['uppanstatus'] == 1 & (($time - strtotime($msql->f('opentime'))) > 0 | $config['autoopenpan'] == 0)) {
        $config['uppanstatus'] = 1;
        $uppantime             = strtotime($msql->f('closetime')) - $time;
		
    } else {
        $config['uppanstatus'] = 0;
        $uppantime             = $time - strtotime($msql->f('opentime'));
        if ($uppantime > 0)
            $uppantime = 3;
    }
    if ($config['upotherstatus']  == 1 & ($config['autoopenpan'] == 0 | ($time - strtotime($msql->f('opentime'))) > 0)) {
        $config['upotherstatus'] = 1;
        $upothertime             = strtotime($msql->f('closetime')) - $time - $config['otherclosetime'];
    } else {
        $config['upotherstatus'] = 0;
        $upothertime             = $time - strtotime($msql->f('opentime'));
        if ($upothertime > 0)
            $upothertime = 3;
    }

}
if ($config['autoopenpan'] == 0) {
    $pantime   = 9999;
    $othertime = 9999;
    $uppantime   = 9999;
    $upothertime = 9999;
}
if($config['times']['io']==0){
    $pantime   = 9999;
    $othertime = 9999;
}
echo abs($pantime) . '|' . abs($othertime) . '|' . $config['thisqishu'] . '|' . $config['panstatus'] . '|' . $config['otherstatus'] . '|' . abs($uppantime) . '|' . abs($upothertime) . '|' . $config['uppanstatus'] . '|' . $config['upotherstatus'].'|'.$fsql->f('status');