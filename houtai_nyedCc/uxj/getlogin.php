<?php
if($_POST['xtype']!='getlogin') exit;
include('../data/comm.inc.php');
include('../data/uservar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/userfunc.php');

$check=0;
if($_SESSION['uuid']!='' && $_SESSION['ucheck']==md5($config['allpass'].$_SESSION['uuid']) ){
	 $check=1;
}

if($check==0 | $config['ifopen']==0){
	 sessiondelu();
     echo 1; 
	 exit;
}

$userid=$_SESSION['uuid'];



$msql->query("select passcode,savetime from `$tb_online` where userid='$userid' and xtype=2");
$msql->next_record();
if($msql->f('passcode')!=$_SESSION['upasscode']){
	 sessiondelu();
     echo 1;
	 exit;
}
	

?>