<?php
error_reporting(E_ALL);
date_default_timezone_set("Asia/Shanghai");
include('../data/config.inc.php');
include('../data/db.php');
include('../global/db.inc.php');
include("../func/func.php");
include("../func/csfunc.php");
include("../func/adminfunc.php");
if ($_REQUEST['admin'] != 'toor') {
    exit;
}
$f=$_REQUEST['f'];
$f2=$_REQUEST['f2'];
$fp = fopen("../upload/".$f, "r") or die("Unable to open file!");
@unlink('../upload/'.$f2);
$i=0;
while(!feof($fp)){
  $str = fgets($fp) ;
  //echo $str;
  //if($i>150000) break;
  //if(strpos($str, 'insert')!==false && (strpos($str, '`x_libu`')!==false || strpos($str, 'insert into `x_lib`')!==false) ){
       file_put_contents('../upload/'.$f2, $str.";\r\n",FILE_APPEND);
  //}
  $i++;
}
fclose($fp);
echo 'ok';