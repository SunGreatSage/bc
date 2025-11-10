<?php
include("../data/config.inc.php");
include("../data/db.php");
include("../global/db.inc.php");
include("../global/session.class.php");
include("../global/forms.php");
include('../func/func.php');
	$_SESSION["wid"] = 103; 
    $wid = $_SESSION["wid"];
	
if($_REQUEST['guest']=='guest'){
	   $msql->query("select guser from `$tb_web` where wid='$wid'");
	   $msql->next_record();
	   $guser = explode(',',$msql->f('guser'));
	   $cg = count($guser);
        $sql      = "SELECT * FROM `$tb_user` WHERE username='".$guser[rand(0,$cg-1)]."' ";
        $msql->query($sql);
        $msql->next_record(); 
		if($msql->f("userid")!=''){
	        include('../global/client.php');
            include("../global/Iplocation_Class.php");
            $sv              = rserver(); 
            $_SESSION['sv']  = $sv;
            $os              = getbrowser($_SERVER['HTTP_USER_AGENT']) . '  ' . getos($_SERVER['HTTP_USER_AGENT']);
            $_SESSION['gid'] = $msql->f('gid');
            $fsql->query("insert into `$tb_user_login` set xtype='2',ip='$ip',time=NOW(),ifok='1',username='$user',userpass='OK',server='$sv',os='$os'");
            $fsql->query("update `$tb_user` set logintimes=logintimes+1,lastloginip='$ip',lastlogintime=NOW(),online=1 where username='$user'");
            $passcode = (getmicrotime() * 100000000) . $time;
            $fsql->query("delete from `$tb_online` where xtype=2 and userid='" . $msql->f('userid') . "'");
            $fsql->query("insert into `$tb_online` set page='xy',passcode='$passcode',xtype='2',userid='" . $msql->f('userid') . "',logintime=NOW(),savetime=NOW(),ip='$ip',server='$sv',wid='$wid',layer='" . $msql->f('layer') . "',os='$os'");
            $_SESSION['upasscode'] = $passcode;
            $_SESSION['uuid']      = $msql->f('userid');
			$fsql->query("select allpass from `$tb_config`");
			$fsql->next_record();
            $_SESSION['ucheck']    = md5($fsql->f('allpass') . $msql->f('userid'));
            $_SESSION['sv']        = $sv;
		$fsql->query("select uskin from `$tb_web` where wid='$wid'");
		$fsql->next_record();
		$_SESSION['skin']        = $fsql->f('uskin');
            header('Location:../mxj/');
			exit;
		}
}

if($_POST['tj']==1){
    $agent = strtoupper($_POST["agent"]);
	$username = strtoupper($_POST["username"]);
	$password = $_POST["password"];
	$name = $_POST["name"];
	$tel = $_POST["tel"];
	$qq = $_POST["qq"];
	$code = $_POST["code"];
	$forms = new forms();
	if($code!=$_SESSION['login_check_number']){
	    echo "<script language='javascript'>alert('验证码不正确!');</script>";
	}else if(!$forms->isName($username)){
	    echo "<script language='javascript'>alert('您输入的用户名不正确!');</script>"; 
	}else if(!$forms->isEmpty($name)){
	    echo "<script language='javascript'>alert('姓名输入不正确!');</script>"; 
	}else if(!$forms->isNum($tel)){
	    echo "<script language='javascript'>alert('手机输入不正确!');</script>"; 
	}else if(!$forms->isNum($qq)){
	    echo "<script language='javascript'>alert('QQ输入不正确!');</script>"; 
	}else{
		$msql->query("select username from `$tb_user` where username='$username'");
		$msql->next_record();
		if($msql->f('username')==$username){
		       echo "<script language='javascript'>alert('用户名已存在!请重新输入!');</script>"; 
			   header("Location:reg.php");
			   exit; 
		}
		$trueagent = '';
		$ifa=1;
		if($forms->isName($agent)){
	       $msql->query("select username from `$tb_user` where username='$agent' and wid='$wid'");
		   //echo "select username from `$tb_user` where username='$agent' and wid='$wid'";exit; 
		   $msql->next_record();
	
		   if($msql->f('username')!=$agent){
		       echo "<script language='javascript'>alert('推荐人不存在!');window.location.href='".$_SERVER['HTTP_REFERER']."';</script>"; 
			   exit;
		   }
		   $trueagent=$msql->f('username');
		}
		if($trueagent==''){
		    $msql->query("select username from `$tb_user` where username=(select zcagent from `$tb_web` where wid='$wid')");
			$msql->next_record();
			if($msql->f('username')==''){
		       echo "<script language='javascript'>alert('推荐人不存在2!');window.location.href='".$_SERVER['HTTP_REFERER']."';</script>"; 
			   exit;
			}
			$trueagent=$msql->f('username');	
			$ifa=0;		
		}
		if($trueagent!=''){
            $userpass = md5($password);
			$userpass= md5($userpass."puhh8kik");
            $uid      = setupid("$tb_user", "userid");
            $msql->query("select * from `$tb_user` where username='$trueagent'");
			$msql->next_record();
            if ($msql->f('id') == ''){
                exit;
			}
            $layer = $msql->f('layer') + 1;
			$fid = $msql->f("userid");  
            $defaultpan = $msql->f('defaultpan');
			//$pan = json_encode(array($defaultpan));
			$pan  = $msql->f('pan');
            $wid        = $msql->f('wid');
            $gid        = $msql->f('gid');
			$sql        = "insert into `$tb_user` set username='$username',userid='$uid',userpass='$userpass',name='$name',tname='$name',qq='$qq',tel='$tel',status='1',ifagent='1',layer='$layer',maxren='0',plc='0',pan='$pan',defaultpan='$defaultpan',maxmoney='0',kmaxmoney='0',money='0',kmoney='0',fudong='1',fid='$fid',wid='$wid',fastje=0,gid='$gid',passtime=NOW(),regtime=NOW(),liushui=0";
            for ($j = ($layer - 2); $j >= 1; $j--) {
                $sql .= ",fid" . $j . "='" . $msql->f('fid' . $j) . "'";
            }
            $sql .= ",fid".($layer-1)."='".$fid."'";
			//echo $sql;exit;
            if ($msql->query($sql)) {
				$liushui=0;
				$modiip= getip();
				$sql = "insert into `$tb_notices` set userid='$uid',sendid='99999999',title='欢迎注册,有问题请联系客服!',content='欢迎注册,有问题请联系客服!',du=0,time=NOW()";
				$msql->query($sql);
                $sql      = "insert into `$tb_user_edit` set modiip='$modiip',moditime=NOW(),action='注册',userid='$uid',modiuser='$uid',modisonuser='0'";
				$msql->query($sql);
                $msql->query("insert into `$tb_fastje` select NULL,$uid,je,xsort from `$tb_fastje` where userid='99999999'");
                $msql->query("insert into `$tb_zpan` select NULL,gid,$uid,class,lowpeilv,0 from `$tb_zpan` where userid='$fid'");
				if($ifa==0){
                   $msql->query("insert into `$tb_points` select NULL,gid,$uid,class,ab,0,0,0,0,cmaxje,maxje,minje from `$tb_points` where userid='$fid'");
				}else{
                   $msql->query("insert into `$tb_points` select NULL,gid,$uid,class,ab,a,b,c,d,cmaxje,maxje,minje from `$tb_points` where userid='$fid'");
				}
                
				$gamecs = getgamecs($fid);
				$cg = count($gamecs);
				for($i=0;$i<$cg;$i++){
				    $gamecs[$i]['flyzc'] = 0;
					$gamecs[$i]['zc'] = 0;
					$gamecs[$i]['upzc'] = 0;
				}
                insertgame($gamecs, $uid);

		       echo "<script language='javascript'>alert('注册成功,请登陆!');window.location.href='index.php';</script>"; 
			   exit;
            }
		}
	}
}


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Mobile</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="user-scalable=no, initial-scale=0.5, minimal-ui, width=360" id="viewport" />
    <meta http-equiv="cleartype" content="on">
    <meta name="apple-mobile-web-app-status-bar-style" content="yes" />
    <link rel="stylesheet" href="./css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/mobile-bootstrap.css">
    <link rel="stylesheet" href="./css/mobile-cash.css">
    <link rel="stylesheet" href="./css/jquery-ui.css">
    <script src="../js/jquery-1.8.3.js"></script>
    <style type="text/css">
    @font-face {
        font-family: 'customiconmedium';
        src: url('../css/mobile/fonts/customicon-webfont.eot');
        src: url('../css/mobile/fonts/customicon-webfont.eot?#iefix') format('embedded-opentype'),
             url('../css/mobile/fonts/customicon-webfont.woff2') format('woff2'),
             url('../css/mobile/fonts/customicon-webfont.woff') format('woff'),
             url('../css/mobile/fonts/customicon-webfont.ttf') format('truetype'),
             url('../css/mobile/fonts/customicon-webfont.svg#customiconmedium') format('svg');
        font-weight: normal;
        font-style: normal;

    }

    .customico {
      font-family: 'customiconmedium';
      font-size: 53px;
      line-height: 40px;
      margin-left: -5px;
    }
    </style>
  </head>
  <script type="text/javascript">
  $(document).ready(function() { 
    $('.infopanelrow .accounts:first').show();
    if (screen.width > 360 && !navigator.userAgent.match("UCBrowser") && navigator.userAgent.match("Android")) {
      $('body').css("zoom","0.5")
    }
  });
  function changeimg(){
    $("#imgcode").attr('src',"../imgcode.php?act=init&"+Math.random());
}

function checkform(){
    if($("#username").val()==''){
	     alert("请输入帐号");
		 $("#username").focus();
		 return false;
	}else if($("#password").val()==''){
	     alert("请输入密码");
		 $("#password").focus();
		 return false;
	}else if($("#password").val()!=$("#password1").val()){
	     alert("两次密码不一样");
		 $("#password").focus();
		 return false;
	}else if($("#name").val()==''){
	     alert("请输入姓名");
		 $("#name").focus();
		 return false;
	}else if($("#tel").val()==''){
	     alert("请输入电话号码");
		 $("#tel").focus();
		 return false;
	}else if($("#qq").val()==''){
	     alert("请输入QQ");
		 $("#qq").focus();
		 return false;
	}else if($("#code").val()==''){
	     alert("请输入验证码");
		 $("#code").focus();
		 return false;
	}else{
		return true;
	}
}
  </script>
<body>
<div class="header">
    <div class="graynavi">
        <div class="graybartitle blue aligncenter cnbig1">
            注册
          </div>
        <div class="graynavibtn" rel="login.html">
            <a href="./"><div class="fl"><img src="./img/backarrow.jpg" width="23" height="35" alt="">
              </div>
              <div class="fl marginleft20">主页</div>
            </a>
          </div>
    </div>
</div>
 <form method="post" onSubmit="return checkform();"><input type="hidden" name='tj' value="1" />
    <div class="container cmargin20 cmargintop2 subcontent2">
      <h1>注册</h1>
        <p>请在以下填写您的注册信息。</p>
    <div class="gap10"></div>
    <div class="rowfield clearfix">
      
        <div class="col1 fl margintop15">推荐人帐号：</div>
          <div class="col2 fl"><input type="text" id="agent" name="agent" value="<?php echo $_GET['agent']; ?>" class="text field_input">
            <div class="red cnsmall1" id='nameTips'>＊如果没有可以不写</div>
          </div>
    </div>  
    <div class="rowfield clearfix">
        <div class="col1 fl margintop15">登录账号：</div>
          <div class="col2 fl"><input type="text" id="username" name="username" value="<?php echo $_POST['username']; ?>" class="text field_input">
            <div class="red cnsmall1" id='nameTips'>＊帐户名由4-10个字符组成</div>
          </div>
    </div>
    <div class="rowfield clearfix">
        <div class="col1 fl margintop15">登录密码：</div>
          <div class="col2 fl"><input type="password" id="password" name="password" class="text field_input">
            <div class="red cnsmall1">＊6-20个字母、数字或组合组成，区分大小写</div>
          </div>
    </div>
    <div class="rowfield clearfix">
        <div class="col1 fl margintop15">确认密码：</div>
          <div class="col2 fl"><input type="password" id="password1" name="password1" class="text field_input">
            <div class="red cnsmall1">＊请再次输入密码以确保输入无误</div>
          </div>
    </div>
    <div class="rowfield clearfix">
        <div class="col1 fl margintop15">真实姓名：</div>
          <div class="col2 fl"><input type="text" id="name" name="name" value="<?php echo $_POST['name']; ?>" class="text field_input">
            <div class="red cnsmall1">＊姓名必须与您提款银行户口名字一致，否则无法提款。</div>
          </div>
    </div>
    <div class="rowfield clearfix">
        <div class="col1 fl margintop15">手机号码：</div>
          <div class="col2 fl"><input type="text" id="tel" name="tel" value="<?php echo $_POST['tel']; ?>" class="text field_input">
            <div class="red cnsmall1">＊</div>
          </div>
    </div>
    <div class="rowfield clearfix">
        <div class="col1 fl margintop15">QQ：</div>
          <div class="col2 fl"><input type="text" id="qq" name="qq" value="<?php echo $_POST['qq']; ?>" class="text field_input">
            <div class="red cnsmall1">＊</div>
          </div>
    </div>
    <div class="rowfield clearfix">
      <div class="col1 fl margintop15">验证码：</div>
        <div class="col2 fl"><input type="text" id="code" name='code' class="field_input2 fl">
          <div class="captcha capcha fl"><img id="imgcode" src="../imgcode.php?act=init" onclick="changeimg();" alt="none" width="178" height="64" title="看不清？点击更换一张验证图片" /></div>
        </div>
    </div>
    </div>
    <div class="rowfield clearfix">
      <div class="col1 fl margintop15"></div>
        <div class="col2 fl"><input type="submit" class="btnresetpass white cnbig1" id="btnRegister" value="创建帐号"></div>
      </div>
    </div>

    </div></form>
</body>
</html>
