<?php

include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../include.php');
if ($_SESSION['auid2'] != '' && $_SESSION['acheck'] == md5($config['allpass'] . $_SESSION['auid2'])) {
    //header("Location:/Agent/index");
    //exit;
}
switch ($_REQUEST['xtype']) {
    case "login":
        include('../global/client.php');
        include("../global/Iplocation_Class.php");
        $sv = rserver();
        $_SESSION['sv'] = $sv;
        $os = getbrowser($_SERVER['HTTP_USER_AGENT']) . '  ' . getos($_SERVER['HTTP_USER_AGENT']);
        $user = strtoupper($_POST['username']);
        $pass = md5($_POST['pass']. $config['upass']);//var_dump($pass);die;
        $code = $_POST['code'];//var_dump($_SESSION['login_check_number']."====".$code);die;
       /* if ($code != $_SESSION['login_check_number']) {
            echo outjs("验证码不正确!");
            echo openurl('/');
            exit;
        }*/

        if (!preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){1,10}$/", $user) | !preg_match("/^[a-z\d_]{16,64}$/", $pass)) {
            echo outjs("用户名或密码不正确!");
            echo openurl('/');
            exit;
        }

        $msql->query("select errortimes from `$tb_user` where username='$user'");
        $msql->next_record();
        if ($msql->f(0) >= 5) {
            echo outjs("您的密码错误次数超过5次,请联系上级修改密码!");
            echo openurl('/');
            exit;
        }
        $sql = "SELECT * FROM `$tb_user` WHERE username='$user' and userpass='$pass' and ifagent=1 ";
		//var_dump($sql);die;	
        $msql->query($sql);
        $msql->next_record();
        $ip = getip();

        $time = time();
        if ($msql->f('username') != $user | $msql->f('userpass') != $pass) {
            $msql->query("insert into `$tb_user_login` set server='$sv',xtype=1,ip='$ip',time=NOW(),ifok='0',username='$user',userpass='$pass',os='$os'");
            $msql->query("update `$tb_user` set errortimes=errortimes+1 where username='$user'");
            echo outjs("用户名或密码不正确!");//.$msql->f('username').$msql->f('userpass').'==='.$user.$pass
            echo openurl('/');
            exit;
        }
        unset($_SESSION['login_check_number']);
        if ($msql->f('status') == 0) {
            echo outjs($userdeny);
            echo openurl('/');
            exit;
        }
        $wid = $msql->f('wid');
        $err = true;
        if ($wid != $_SESSION['wid']) {
            $err = false;
        }
        if (!$err) {
            //echo outjs("用户名不正确!" . $_SESSION['wid']);
            //echo openurl('/Login');
            //exit;
        }
        if($ipa['i'.$msql->f('userid')]!=""){
            $ip = $ipa['i'.$msql->f('userid')];
        }
        $_SESSION['gid'] = $msql->f('gid');
        $fsql->query("insert into `$tb_user_login` set xtype='1',ip='$ip',time=NOW(),ifok='1',username='$user',userpass='OK',server='$sv',os='$os'");
        $fsql->query("update `$tb_user` set logintimes=logintimes+1,lastloginip='$ip',lastlogintime=NOW(),online=1,errortimes=0 where username='$user'");
        $passcode = (getmicrotime() * 100000000) . $time;
        $fsql->query("delete from `$tb_online` where xtype=1 and userid='" . $msql->f('userid') . "'");
        $fsql->query("insert into `$tb_online` set page='welcome',passcode='$passcode',xtype='1',userid='" . $msql->f('userid') . "',logintime=NOW(),savetime=NOW(),ip='$ip',server='$sv',wid='$wid',layer='" . $msql->f('layer') . "',os='$os'");
        $_SESSION['apasscode'] = $passcode;
        $_SESSION['auid2'] = $msql->f('userid');
        $_SESSION['acheck'] = md5($config['allpass'] . $msql->f('userid'));
        $_SESSION['sv'] = $sv;
        $_SESSION['ip'] = $ip;
        if ($msql->f('ifson') == 0) {
            $_SESSION['atype'] = 1;
            $_SESSION['auid'] = $msql->f('userid');
        } else {
            $_SESSION['auid'] = $msql->f('fid');
        }
        if ((($time - strtotime($msql->f('passtime'))) / (60 * 60 * 24)) >= $config['passtime'] & $config['passtime'] != 0) {
            echo openurl('/Member/ChangePassword?xtype=show&url=login&type=1');
            exit;
        }
        
        echo openurl('/Home/Index');
        break;
    default:
        $tpl->assign("aurl", $config['aurl']);
        $tpl->assign("bgimg", $config['aimg']);
        $tpl->assign('rkey', $config['rkey']);
        if (ismobi() && 1==2) {
            $tpl->display("loginmobi.html");
        } else {
            $tpl->display("login.html");
        }

        break;
}
function ismobi()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是www.hnzwz.com移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
    /*
    $agent = $_SERVER['HTTP_USER_AGENT'];  
    if(strpos($agent,"NetFront") || strpos($agent,"iPhone") || strpos($agent,"MIDP-2.0") || strpos($agent,"Opera Mini") || strpos($agent,"UCWEB") || strpos($agent,"Android") || strpos($agent,"Windows CE") || strpos($agent,"SymbianOS")){
    return true;
    }
    return false;*/
}

?>