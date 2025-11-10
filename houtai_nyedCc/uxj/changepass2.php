<?php
include('../data/comm.inc.php');
include('../data/uservar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
switch($_REQUEST['xtype']){
    case "show":
	    $msql->query("select passtime,username from `$tb_user`  where userid='$userid'");
		$msql->next_record(); 
	    $tpl->assign('passtime',$config['passtime']);
	    $tpl->assign("username",$msql->f('username'));	
		$tpl->assign('title',$config['webname']);
		$tpl->assign('uimg',$config['uimg']);
	    $tpl->display("changpass2.html");
	break;
	case "changepass":
        $pass1 = md5($_POST['pass1'].$config['upass']);
        $pass0 = md5($_POST['pass0'].$config['upass']);
        $msql->query("select id from `$tb_user` where userpass='$pass0' and userid='$userid'");
	
		$msql->next_record();

		if($msql->f('id')==''){
			
		    echo 1;
			exit;
		}
		$ip="";
		if($ipa['i'.$userid]!=""){
            $ip = $ipa['i'.$userid];
        }
		$sql="update `$tb_user` set userpass='$pass1',passtime=NOW() where userid='$userid'";
		if($msql->query($sql)){
            userchange("更改密码",$userid,$ip);
			sessiondelu();
		    echo 2;
		}
   break;
}

?>