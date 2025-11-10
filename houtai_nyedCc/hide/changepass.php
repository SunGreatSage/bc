<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        $msql->query("select passtime from `$tb_admins`  where adminid='$adminid'");
        $msql->next_record();
        if ($msql->f('passtime') == 0)
            $first = 1;
        $tpl->assign('first', $first);
        $tpl->assign('passtime', $config['passtime']);
        $tpl->assign("username", $_SESSION['username']);
        $tpl->assign('mimg', $config['himg']);
        $tpl->display("changpass.html");
        break;
    case "changepass":
        //echo 2;
        //exit;
        $pass1 = md5($_POST['pass1'].$config['upass']);
        $pass0 = md5($_POST['pass0'].$config['upass']);
        $msql->query("select id from `$tb_admins` where adminpass='$pass0' and adminid='$adminid'");
        $msql->next_record();
        if ($msql->f('id') == '') {
            echo 1;
            exit;
        }
        $sql = "update `$tb_admins` set adminpass='$pass1',passtime=NOW() where adminid='$adminid'";
        if ($msql->query($sql)) {
            userchange("更改密码",$adminid); 
            //sessiondel();
            echo 2;
        }
        break;
}
?>