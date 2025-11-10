<?php
include('../data/comm.inc.php');



include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');

include('../include.php');
include('./checklogin.php');

switch($_REQUEST['xtype']){
   case "show":
        $msql->query("select baoma from `$tb_config`");
		$msql->next_record();
		echo $msql->f('baoma');		
		echo "&nbsp;&nbsp;<a href='checkfly.php?xtype=setfly'>set fly</a>";
   break;
   case "setfly":
        $msql->query("update `$tb_config` set baoma=0");
   break;
   
}


?>