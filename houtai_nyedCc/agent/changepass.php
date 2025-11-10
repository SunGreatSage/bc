<?php
include('../data/comm.inc.php');



include('../data/agentvar.php'); 
include('../func/func.php');
include('../func/agentfunc.php');

include('../include.php');
include('./checklogin.php');



switch($_REQUEST['xtype']){
    case "show":
	    $msql->query("select passtime from `$tb_user`  where userid='$userid2'");
		$msql->next_record();
		$first=0;
		if(substr($msql->f('passtime'),0,1)==0) $first=1;
		$tpl->assign('first',$first);
	    $tpl->assign('passtime',$config['passtime']);
	    $tpl->assign("username",transuser($userid2,'username'));
		

		$tpl->assign('aimg',$config['aimg']);

		
	    $tpl->display("changpass.html");
	break;
	case "changepass":
        $pass1 = md5($_POST['pass1'].$config['upass']);
        $pass0 = md5($_POST['pass0'].$config['upass']);
        $msql->query("select id from `$tb_user` where userpass='$pass0' and userid='$userid2'");
		$msql->next_record(); 
		if($msql->f('id')==''){
		    echo 1;
			exit;
		}
		$sql="update `$tb_user` set userpass='$pass1',passtime=NOW() where userid='$userid2'";
		if($msql->query($sql)){
            $modiuser = $userid;
            $moditime = time();
            $modiip   = getip();
            userchange("更改密码",$userid2);
			//sessiondela();
		    echo 2;
		}
   break;
}

?>