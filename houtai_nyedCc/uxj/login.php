<?php
include('../data/comm.inc.php');
include('../data/uservar.php');
include('../func/func.php');
include('../include.php');
if ($_SESSION['uuid'] != '' && $_SESSION['ucheck'] == md5($config['allpass'] . $_SESSION['uuid'])) {
    //header("Location:/Member/index");
    //exit;
}
switch ($_REQUEST['xtype']) {
    case "login":
        include('../global/client.php');
        include("../global/Iplocation_Class.php");
        //print_r($_POST);
        $sv = rserver();
        $_SESSION['sv'] = $sv;
        //echo $_POST['pass'] ;
        $os = getbrowser($_SERVER['HTTP_USER_AGENT']) . '  ' . getos($_SERVER['HTTP_USER_AGENT']);
        $user = strtoupper($_POST['username']);
        $pass = md5($_POST['pass']. $config['upass']);//var_dump($pass);die;
        $code = $_POST['code'];
       /* if ($code != $_SESSION['login_check_number']) {
            echo outjs("验证码不正确!");
            echo openurl('/Member/Login');
            exit;
        }*/
        if (!preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){1,10}$/", $user) | !preg_match("/^[a-z\d_]{16,64}$/", $pass)) {
            echo outjs("用户名或密码不正确!");
            echo openurl('/Member/Login');
            exit;
        }
                
        $msql->query("select errortimes from `$tb_user` where username='$user'");
        $msql->next_record();
        if ($msql->f(0) >= 5) {
            echo outjs("您的密码错误次数超过5次,请联系上级修改密码!");
            echo openurl('/Member/Login');
            exit;
        }
        $sql = "SELECT * FROM `$tb_user` WHERE username='$user' and userpass='$pass' and ifagent='0' and ifson='0'";
        $msql->query($sql);
        $msql->next_record();
        $ip = getip();
        $time = time();
        if ($msql->f('username') != $user | $msql->f('userpass') != $pass) {
            $msql->query("insert into `$tb_user_login` set server='$sv',xtype=2,ip='$ip',time=NOW(),ifok='0',username='$user',userpass='{$_POST['password']}',os='$os'");
            $msql->query("update `$tb_user` set errortimes=errortimes+1 where username='$user'");
            echo outjs("用户名或密码不正确!");
            echo openurl('/Member/Login');
            exit;
        }
        unset($_SESSION['login_check_number']);
        if ($msql->f('status') == 0) {
            echo outjs($userdeny);
            echo openurl('/Member/Login');
            exit;
        }
        $wid = $msql->f('wid');
        $err = true;
        if ($wid != $_SESSION['wid']) {
            $err = false;
        }
        if (!$err) {
            //echo outjs("用户名不正确!");
            //echo openurl('/Member/Login');
            //exit;
        }
        if($ipa['i'.$msql->f('userid')]!=""){
            $ip = $ipa['i'.$msql->f('userid')];
        }
        $_SESSION['gid'] = $msql->f('gid');
        $fsql->query("insert into `$tb_user_login` set xtype='2',ip='$ip',time=NOW(),ifok='1',username='$user',userpass='OK',server='$sv',os='$os'");
        $fsql->query("update `$tb_user` set errortimes=0,logintimes=logintimes+1,lastloginip='$ip',lastlogintime=NOW(),online=1 where username='$user'");
        $passcode = (getmicrotime() * 100000000) . $time;
        $fsql->query("delete from `$tb_online` where xtype=2 and userid='" . $msql->f('userid') . "'");
        $fsql->query("insert into `$tb_online` set page='xy',passcode='$passcode',xtype='2',userid='" . $msql->f('userid') . "',logintime=NOW(),savetime=NOW(),ip='$ip',server='$sv',wid='$wid',layer='" . $msql->f('layer') . "',os='$os'");
        $_SESSION['upasscode'] = $passcode;
        $_SESSION['uuid'] = $msql->f('userid');
        $_SESSION['ucheck'] = md5($config['allpass'] . $msql->f('userid'));
        $_SESSION['sv'] = $sv;
        $_SESSION['ip'] = $ip;
        $fsql->query("select uskin from `$tb_web` where wid='$wid'");
        $fsql->next_record();
        $_SESSION['skin'] = $fsql->f('uskin');
        if ((($time - strtotime($msql->f('passtime'))) / (60 * 60 * 24)) >= $config['passtime'] & $config['passtime'] != 0) {
            echo openurl('/Member/ChangePassword?xtype=show&url=login&type=1');
            exit;
        }
        echo openurl('/Member/Agreement');
        break;
    default:
        $tpl->assign("uurl", $config['uurl']);
        $tpl->assign("bgimg", $config['uimg']);
        $tpl->assign('rkey', $config['rkey']);
        $tpl->assign('moneytype', $config['moneytype']);
        $tpl->display("login.html");
        break;
}
?>