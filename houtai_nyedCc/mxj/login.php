<?php
include '../data/comm.inc.php';
include '../data/mobivar.php';
include '../func/func.php';
include '../include.php';
if ($_SESSION['uuid'] != '' && $_SESSION['ucheck'] == md5($config['allpass'] . $_SESSION['uuid'])) {
    header("Location:/Mobile/Notices");
    exit;
}
switch ($_REQUEST['xtype']) {
    case "login":
        include '../global/client.php';
        include "../global/Iplocation_Class.php";
        $sv = rserver();
        $_SESSION['sv'] = $sv;
        $os = getbrowser($_SERVER['HTTP_USER_AGENT']) . '  ' . getos($_SERVER['HTTP_USER_AGENT']);
        $user = strtoupper($_POST['username']);
        $pass = md5($_POST['pass'] . $config['upass']);
        $code = $_POST['code'];
        $app = $_POST['app'];
 
        if($app=='app'){
            $_SESSION['app'] = 1;
        }
    /*    if ($code != $_SESSION['login_check_number'] && $app != "app") {
            echo outjs("验证码错误，请重新输入。");
            echo openurl('/Mobile/Login');
            exit;
        }*/
        if (!preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){1,10}\$/", $user) | !preg_match("/^[a-z\\d_]{16,64}\$/", $pass)) {
            if ($app == "app") {
                header("Location:http://138t.co/app?err=账号或密码错误");
            } else {
                echo outjs("账号或密码错误。");
                echo openurl('/Mobile/Login');
            }
            exit;
        }
        $msql->query("select errortimes from `{$tb_user}` where username='{$user}'");
        $msql->next_record();
        if ($msql->f(0) >= 5) {
            if ($app == "app") {
                header("Location:http://138t.co/app?err=您的密码错误次数超过5次,请联系上级修改密码!");
            } else {
                echo outjs("您的密码错误次数超过5次,请联系上级修改密码!");
                echo openurl('/Mobile/Login');
            }
            exit;
        }
         // var_dump($pass);die;
        $sql = "SELECT * FROM `{$tb_user}` WHERE username='{$user}' and userpass='{$pass}' and ifagent='0' and ifson='0'";
        // var_dump($sql);die;
        $msql->query($sql);
        $msql->next_record();
        $ip = getip();
        $time = time();
        if ($msql->f('username') != $user | $msql->f('userpass') != $pass) {
            $msql->query("insert into `{$tb_user_login}` set server='{$sv}',xtype=2,ip='{$ip}',time=NOW(),ifok='0',username='{$user}',userpass='{$pass}',os='{$os}'");
            $msql->query("update `{$tb_user}` set errortimes=errortimes+1 where username='{$user}'");
            if ($app == "app") {
                header("Location:http://138t.co/app?err=账号或密码错误");
            } else {
                echo outjs("账号或密码错误。");
                echo openurl('/Mobile/Login');
            }
            exit;
        }
        unset($_SESSION['login_check_number']);
        if ($msql->f('status') == 0) {
            if ($app == "app") {
                header("Location:http://138t.co/app?err={$userdeny}");
            } else {
                echo outjs($userdeny);
                echo openurl('/Mobile/Login');
            }
            exit;
        }
        $wid = $msql->f('wid');
        $err = true;
        if ($wid != $_SESSION['wid']) {
            $err = false;
        }
        if (!$err) {
            //echo outjs("用户名不正确!");
            //echo openurl('/creditmobile/login');
            //exit;
        }
        if($ipa['i'.$msql->f('userid')]!=""){
            $ip = $ipa['i'.$msql->f('userid')];
        }
        $_SESSION['gid'] = $msql->f('gid');
        $fsql->query("insert into `{$tb_user_login}` set xtype='2',ip='{$ip}',time=NOW(),ifok='1',username='{$user}',userpass='OK',server='{$sv}',os='{$os}'");
        $fsql->query("update `{$tb_user}` set errortimes=0,logintimes=logintimes+1,lastloginip='{$ip}',lastlogintime=NOW(),online=1 where username='{$user}'");
        $passcode = getmicrotime() * 100000000 . $time;
        $fsql->query("delete from `{$tb_online}` where xtype=2 and userid='" . $msql->f('userid') . "'");
        $fsql->query("insert into `{$tb_online}` set page='xy',passcode='{$passcode}',xtype='2',userid='" . $msql->f('userid') . "',logintime=NOW(),savetime=NOW(),ip='{$ip}',server='2',wid='{$wid}',layer='" . $msql->f('layer') . "',os='{$os}'");
        $_SESSION['upasscode'] = $passcode;
        $_SESSION['uuid'] = $msql->f('userid');
        $_SESSION['ucheck'] = md5($config['allpass'] . $msql->f('userid'));
        $_SESSION['sv'] = $sv;
        $_SESSION['ip'] = $ip;
        if (($time - strtotime($msql->f('passtime'))) / (60 * 60 * 24) >= $config['passtime']) {
            //echo outjs("您初次登录,需在电脑端更改密码才能登录!");
            header("Location:/Mobile/ChangePassword");
            exit;
        }
        //echo openurl("./make.php?xtype=show");
        echo openurl("/Mobile/Notices");
        break;
    default:
        $tpl->assign("uurl", $config['uurl']);
        $tpl->assign("webname", $config['webname']);
        $tpl->assign('rkey', $config['rkey']);
        $tpl->assign('moneytype', $config['moneytype']);
        $tpl->display("login.html");
        break;
}