<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');

switch($_REQUEST['xtype']){
     case "show":
	     echo "<iframe id='kjnow' scrolling='no' src='http://490kj.com/k.php' frameborder='0' style='float:none;margin:0px auto;width:100%;height:50px;background:#fff' width='100%' height='100%'></iframe>";
	 break;
}
?>