<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
switch($_REQUEST['xtype']){

	case "cpasssend":
        $pass1 = md5($_POST['pass1'].$config['upass']);
        $pass0 = md5($_POST['pass0'].$config['upass']);
        $msql->query("select id from `$tb_user` where userpass='$pass0' and userid='$userid'");
		$msql->next_record();
		if($msql->f('id')==''){
		    echo 1;
			exit;
		}
		$sql="update `$tb_user` set userpass='$pass1',passtime=NOW() where userid='$userid'";
		if($msql->query($sql)){
            userchange("更改密码",$userid);
		    echo 2;
		}
   break;
   case "cpass":
		$tpl->assign('webname',$config['webname']);
		
	    $tpl->display($mobi."cpass.html");
	break;
}

?>