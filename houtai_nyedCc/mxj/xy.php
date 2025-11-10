<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
		

$tpl->assign('webname',$config['webname']);

$tpl->display($mobi."xy.html");

?>