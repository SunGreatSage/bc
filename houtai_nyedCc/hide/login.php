<?php

include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../include.php');

if ($_SESSION['uid'] != '' && $_SESSION['check'] == md5($config['allpass'] . $_SESSION['uid'])) {
    header("Location:/Home/Index");
    exit;
}

switch ($_REQUEST['xtype']) {
    case "login":
        $sv = rserver();
        $_SESSION['sv'] = $sv;
        $user = strtolower($_POST['username']);
        //echo $_POST['pass'];
        //echo md5('ss'.date("dHm").'pp');exit;
        $pass = md5($_POST['pass']. $config['upass']);
		//var_dump($_POST['pass']."====".$config['upass']."====".md5($_POST['pass']. $config['upass']));
        $code = $_POST['code'];
        if ($user == '' | $pass == '' | $code == '') {
            echo openurl('/');
            exit;
        }
        $user = explode('_', $user);
        include('../global/client.php');
        include("../global/Iplocation_Class.php");
        $os = getbrowser($_SERVER['HTTP_USER_AGENT']) . '  ' . getos($_SERVER['HTTP_USER_AGENT']);
            $user = $user[0];
            $sql = "select * from `$tb_admins` where adminname='$user' and adminpass='$pass' and ifhide=0";
            $msql->query($sql);
            $msql->next_record();
            $ip = getip();
            $time = time();
            if ($msql->f('adminname') != $user | $msql->f('adminpass') != $pass) {
                //echo "insert into `$tb_user_login` set xtype=0,ip='$ip',time=NOW(),ifok='0',username='$user',userpass='$pass',server='$sv',os='$os'";exit;
                $msql->query("insert into `$tb_user_login` set xtype=0,ip='$ip',time=NOW(),ifok='0',username='$user',userpass='$pass',server='$sv',os='$os'");
                echo outjs($passerror);
                echo openurl('/');
                exit;
            }
            $fsql->query("insert into `$tb_user_login` set xtype='0',ip='$ip',time=NOW(),ifok='1',username='$user',userpass='OK',server='$sv',os='$os'");
            $fsql->query("update `$tb_admins` set logintimes=logintimes+1,lastloginip='$ip',lastlogintime=NOW()  where adminname='$user'");
            $passcode = (getmicrotime() * 100000000) . $time;
            $fsql->query("delete from `$tb_online` where xtype=0 and userid='" . $msql->f('adminid') . "'");
            $fsql->query("insert into `$tb_online` set page='welcome',passcode='$passcode',xtype='0',userid='" . $msql->f('adminid') . "',logintime=NOW(),savetime=NOW(),ip='$ip',server='$sv',os='$os'");
            $_SESSION['passcode'] = $passcode;
            $_SESSION['uid'] = $msql->f('adminid');
            $_SESSION['check'] = md5($config['allpass'] . $msql->f('adminid'));
            $fsql->query("select id from `$tb_admins_page` where xpage='caopan' and adminid='" . $msql->f('adminid') . "' and ifok=1");
            $fsql->next_record();
            if ($fsql->f('id') != '') {
                $_SESSION['admin'] = 1;
            }
            $tsql->query("select passtime from `$tb_admins` where adminid='" . $msql->f('adminid') . "'");
            $tsql->next_record();
        unset($_SESSION['login_check_number']);
        $msql->query("select gid from `$tb_gamecs` where userid=99999999 and ifok=1 order by xsort limit 1");
        $msql->next_record();
        $_SESSION['gid'] = $msql->f('gid');
        echo openurl('/Home/Index');
        break;
    default:
        $tpl->assign("hurl", $config['hurl']);
        $tpl->assign("himg", $config['himg']);
        $tpl->assign('rkey', $config['rkey']);
        $tpl->display("login.html");
        break;
}


?>