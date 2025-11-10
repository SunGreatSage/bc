<?php

file_put_contents("app.txt",file_get_contents("php://input")."====".$_REQUEST['xtype']."==".$_SESSION['login_check_number']."===\r\n",FILE_APPEND);
include('../data/comm.inc.php');
include('../data/uservar.php');
include('../func/func.php');
include('../include.php');
if ($_SESSION['uuid'] != '' && $_SESSION['ucheck'] == md5($config['allpass'] . $_SESSION['uuid'])) {
    header("Location:/uxj/admin.php");
    //exit;
}
$agent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($agent, "comFront") || strpos($agent, "iPhone") || strpos($agent, "MIDP-2.0") || strpos($agent, "Opera Mini") || strpos($agent, "UCWEB") || strpos($agent, "Android") || strpos($agent, "Windows CE") || strpos($agent, "SymbianOS"))
	header("Location:./mxj/login.php");
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
        if ($code != $_SESSION['login_check_number']) {
            echo outjs("验证码错误，请重新输入。");
            echo openurl('/uxj/login.php');
            exit;
        }
        if (!preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){1,10}$/", $user) | !preg_match("/^[a-z\d_]{16,64}$/", $pass)) {
            echo outjs("账号或密码错误。");
            echo openurl('/uxj/login.php');
            exit;
        }
                
        $msql->query("select errortimes from `$tb_user` where username='$user'");
        $msql->next_record();
        if ($msql->f(0) >= 5) {
            echo outjs("您的密码错误次数超过5次,请联系上级修改密码!");
            echo openurl('/uxj/login.php');
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
            echo outjs("账号或密码错误。");
            echo openurl('/uxj/login.php');
            exit;
        }
        unset($_SESSION['login_check_number']);
        if ($msql->f('status') == 0) {
            echo outjs($userdeny);
            echo openurl('/uxj/login.php');
            exit;
        }
        $wid = $msql->f('wid');
        $err = true;
        if ($wid != $_SESSION['wid']) {
            $err = false;
        }
        if (!$err) {
            //echo outjs("用户名不正确!");
            //echo openurl('/uxj/login.php');
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
            echo openurl('/uxj/changepass.php?xtype=show&url=login&type=1');
            exit;
        }
        break;
    default:
        $post_j = file_get_contents("php://input");
         parse_str($post_j,$post);
        file_put_contents("app.txt",var_export($post,true)."\r\n",FILE_APPEND);
        if($post['type']=="1")
        {
        file_put_contents("app.txt","12313123"."\r\n",FILE_APPEND);
        include('../global/client.php');
        include("../global/Iplocation_Class.php");
        file_put_contents("app.txt","45445454"."\r\n",FILE_APPEND);
        //print_r($_POST);
        $sv = rserver();
        $_SESSION['sv'] = $sv;
        //echo $_POST['pass'] ;
        $os = getbrowser($_SERVER['HTTP_USER_AGENT']) . '  ' . getos($_SERVER['HTTP_USER_AGENT']);
        $user = strtoupper($post['account']);
        $pass = md5($post['password']. $config['upass']);//var_dump($pass);die;
        $code = $post['code'];
        file_put_contents("app.txt",$code."===="."1"."===".$_SESSION['login_check_number']."\r\n",FILE_APPEND);
        if ($code != $_SESSION['login_check_number']) {
            echo outjs("验证码错误，请重新输入。");
            echo openurl('/uxj/login.php');
            exit;
        }
        file_put_contents("app.txt","2"."\r\n",FILE_APPEND);
        if (!preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){1,10}$/", $user) | !preg_match("/^[a-z\d_]{16,64}$/", $pass)) {
            echo outjs("账号或密码错误。");
            echo openurl('/uxj/login.php');
            exit;
        }
        file_put_contents("app.txt","3"."\r\n",FILE_APPEND);        
       /* $msql->query("select errortimes from `$tb_user` where username='$user'");
        $msql->next_record();
        if ($msql->f(0) >= 5) {
            echo outjs("您的密码错误次数超过5次,请联系上级修改密码!");
            echo openurl('/uxj/login.php');
            exit;
        }*/
        $sql = "SELECT * FROM `$tb_user` WHERE username='$user' and userpass='$pass' and ifagent='0' and ifson='0'";
        $msql->query($sql);
        $msql->next_record();
        $ip = getip();
        file_put_contents("app.txt","4"."\r\n",FILE_APPEND);
        $time = time();
        file_put_contents("app.txt",$msql->f('username')."===". $user."====".$msql->f('userpass')."====".$pass."\r\n",FILE_APPEND);
        if ($msql->f('username') != $user || $msql->f('userpass') != $pass) {
            $msql->query("insert into `$tb_user_login` set server='$sv',xtype=2,ip='$ip',time=NOW(),ifok='0',username='$user',userpass='{$_POST['password']}',os='$os'");
            $msql->query("update `$tb_user` set errortimes=errortimes+1 where username='$user'");
            //echo outjs("账号或密码错误。");
            //echo openurl('/uxj/login.php');
            //exit;
        }
        unset($_SESSION['login_check_number']);
        file_put_contents("app.txt","5"."\r\n",FILE_APPEND);
        /*if ($msql->f('status') == 0) {
            echo outjs($userdeny);
            echo openurl('/uxj/login.php');
            exit;
        }*/
        $wid = $msql->f('wid');
        $err = true;
        file_put_contents("app.txt","6"."\r\n",FILE_APPEND);
        if ($wid != $_SESSION['wid']) {
            $err = false;
        }
        if (!$err) {
            //echo outjs("用户名不正确!");
            //echo openurl('/uxj/login.php');
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
        file_put_contents("app.txt","7"."\r\n",FILE_APPEND);
        $_SESSION['skin'] = $fsql->f('uskin');
        if ((($time - strtotime($msql->f('passtime'))) / (60 * 60 * 24)) >= $config['passtime'] & $config['passtime'] != 0) {
             file_put_contents("app.txt","9"."\r\n",FILE_APPEND);
           // echo openurl('/uxj/changepass.php?xtype=show&url=login&type=1');
           // exit;
        }
         file_put_contents("app.txt","8"."\r\n",FILE_APPEND);
file_put_contents("app.txt","99"."\r\n",FILE_APPEND);
include('../data/uservar.php');
file_put_contents("app.txt","99"."\r\n",FILE_APPEND);
 file_put_contents("app.txt","9"."\r\n",FILE_APPEND);
include('../func/userfunc.php');
 file_put_contents("app.txt","10"."\r\n",FILE_APPEND);
include('./checklogin.php');
  file_put_contents("app.txt","11"."\r\n",FILE_APPEND);
$mess = $msql->arr("select content,cs from `$tb_news`  where wid in ('".$_SESSION['wid']."',0) and agent in (0,2) and alert=1 and ifok=1 order by time desc",1);
 file_put_contents("app.txt",var_export($mess,true)."\r\n",FILE_APPEND);
foreach($mess as $key => $val){
   if($val['cs']==1){
      	$arr[0] = $config['thisqishu'];
				$arr[1] = $config['webname'];
				$fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='".$config['thisqishu']."'");
				$fsql->next_record();
			    $arr[2] = $fsql->f('opentime');
				$arr[3] = $fsql->f('closetime');
				$arr[4] = $fsql->f('kjtime');
			    $mess[$key]['content'] = messreplace($mess[$key]['content'],$arr);
   }
}

file_put_contents("app.txt",transuser($userid,'status')."|||||||\r\n",FILE_APPEND);
$tpl->assign("status",transuser($userid,'status'));
$tpl->assign("mess",$mess);
$tpl->assign('title',$config['webname']);
$tpl->assign('xy',$xy);
$tpl->display("xy.html");
        die;
        }
        $tpl->assign("uurl", $config['uurl']);
        $tpl->assign("bgimg", $config['uimg']);
        $tpl->assign('rkey', $config['rkey']);
        $tpl->assign('moneytype', $config['moneytype']);
        $tpl->display("login.html");
        break;
}
?>