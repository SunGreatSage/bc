<?php
include('../data/comm.inc.php');
include('../data/uservar.php');

include('../func/userfunc.php');

include('../include.php');
//include('./checklogin.php');

$tpl->assign('panstatus',$config['panstatus']);
$tpl->assign('otherstatus',$config['otherstatus']);

$tpl->display("check.html"); 


?>