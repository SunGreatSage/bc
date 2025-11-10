<?php
/* set_time_limit(0);
include("../data/config.inc.php");
include("../data/db.php");
include("../global/db.inc.php");
error_reporting(E_ALL);
for($i=10;$i<=20;$i++){
	
	if($i<2) $n='00'.$i;
	else if ($i<100) $n='0'.$i;
	else $n = $i;
    $fp  = fopen('f://388//388//388_'.$n.'.sql', 'r+');  
	if($fp){
		$timer='';
	    while (!feof($fp)) {
		    $fstr = fgets($fp,4096);
			if (strpos('0'.$fstr,"insert into `x_lib")){
				echo $timer;
				 $time = str_replace('SET TIMESTAMP=','',$timer);
				 $time = str_replace('/*!*/;','',$time);
				 $time = date("Y-m-d H:i:s",$time);
				 $sql = str_replace("NOW()","'$time'",$fstr);
				 //$msql->query($sql);
			}
			$timer = $fstr;
		}
	}
} */
?>