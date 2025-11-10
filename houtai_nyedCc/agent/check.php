<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');

$tpl->assign('panstatus',$config['panstatus']);
$tpl->assign('otherstatus',$config['otherstatus']);

$tpl->display($mobi."check.html");
?>