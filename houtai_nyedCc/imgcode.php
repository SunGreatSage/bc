<?php
include('./data/config.inc.php');
include("./global/session.class.php");
include('./global/img.class.5.php');
$n = new imgdata;
$list = scandir("./code");
$cl = count($list);
while(1){
	$code = $list[rand(2,$cl-1)];
    $_SESSION['login_check_number']  = substr($code,0,4);
    $n->getdir("./code/".$code);
    $n->img2data();
    if($n->imgform=="image/jpeg" && is_numeric($_SESSION['login_check_number'])){
    	$n->data2img();
    	break;
    }    
}
