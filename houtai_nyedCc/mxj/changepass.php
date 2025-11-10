<?php

include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
switch($_REQUEST['xtype']){

	case "changepass":
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
			sessiondelu();
		    echo 2;
		}
   break;
   default:
   	    $msql->query("select passtime from `$tb_user`  where userid='$userid'");
		$msql->next_record(); 
		if ((($time - strtotime($msql->f('passtime'))) / (60 * 60 * 24)) >= $config['passtime']) {
			$first=1;
		}
		$first=1;//liml
		$tpl->assign('first',$first);
	    $tpl->assign('passtime',$config['passtime']);
	    $tpl->assign("username",transuser($userid,'username'));
		$tpl->assign('webname',$config['webname']);

		$tpl->assign('uimg',$config['uimg']);		
		
	    $tpl->display("changepass.html");
	break;
}

?>