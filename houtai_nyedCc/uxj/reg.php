<?php
include("../data/config.inc.php");
include("../data/db.php");
include("../global/db.inc.php");
include("../global/session.class.php");
include("../global/forms.php");
include('../func/func.php');
if($_POST['tj']==1){
	$_SESSION["wid"] = 103; 
    $wid = $_SESSION["wid"];
    $agent = strtoupper($_POST["reg_agent"]);
	$username = strtoupper($_POST["reg_username"]);
	$password = $_POST["reg_password"];
	$name = $_POST["reg_name"];
	$tel = $_POST["reg_tel"];
	$qq = $_POST["reg_qq"];
	$code = $_POST["reg_code"];
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
		       echo "<script language='javascript'>alert('推荐人不存在!');window.location.href='".$_SERVER['HTTP_REFERER']."';</script>"; 
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Welcome</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="./css/master.css" rel="stylesheet" type="text/css">
<link href="./css/validationEngine.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="./js/jquery1.js"></script>
<script type="text/javascript" src="./js/jquery_003.js"></script>
<script type="text/javascript" src="./js/jquery2.js"></script>
<script type="text/javascript" src="./js/reg.js"></script>
<script language="javascript">
function changeimg(){
    $("#imgcode").attr('src',"../imgcode.php?act=init&"+Math.random());
}
</script>
<style>
.inp-txt {
	width: 340px;
	height: 34px;
	font-size: 20px;
	line-height: 34px;
	text-indent: 5px;
	border-top: 1px solid #b8b8b8;
	border-left: 1px solid #b8b8b8;
	border-right: 1px solid #cccccc;
	border-bottom: 1px solid #cccccc;
	margin: 0 15px 0 0;
}

.bu_reg {
	width: 202px;
	height: 43px;
	margin: 0 20px 0 115px;
}
</style>
</head>

<body style="background: white;">
	<div class="header">
		<div class="headerc">
			<ul>
				<li><object width="280" height="73" data="./img/logo.swf" type="application/x-shockwave-flash">
						<param name="wmode" value="transparent" />
						<param name="src" value="./img/logo.swf" />
					</object></li>
			</ul>
		</div>
	</div>
	<div class="reg_nav"></div>
	<div class="reg_info">
		<ul class="reg_title">
			<li>请在以下填写您的注册信息</li>
		</ul>
		<script type="text/javascript">
		jQuery(document).ready(function() {
            var theForm = $("#main");
            theForm.validationEngine();
			jQuery("input:text").each(function(){
			    jQuery(this).attr("name",jQuery(this).attr("id"));
			});
			jQuery("input:password").each(function(){
			    jQuery(this).attr("name",jQuery(this).attr("id"));
			});
        });
		</script>
		<form id="main" method="post" name="main">
           <input type="hidden" name='tj' value="1" />
			<ul class="reg_te">
				<fieldset>
					<table>
						<tbody>
                           <tr>
								<th>推荐人帐号：</th>
								<td><input size="40" id="reg_agent" maxlength="15" value="<?php echo $_GET['agent']; ?>"  
									class="inp-txt" type="text"></td>
								<td>如果没有可以不写</td>
							</tr>
							<tr>
								<th></th>
								<th style="height: 10px;"></th>
							</tr>
							<tr>
								<th>会员账号：</th>
								<td><input size="40" id="reg_username" maxlength="15" value="<?php echo $_POST['reg_username']; ?>"  
									class="validate[required,custom[onlyLetterNumber],minSize[3],maxSize[8]] inp-txt" type="text"></td>
								<td>账号规则：账号必须为3-8为数字和字母组合</td>
							</tr>
							<tr>
								<th></th>
								<th style="height: 10px;"></th>
							</tr>
							<tr>
								<th>登陆密码：</th>
								<td><input id="reg_password" maxlength="15" size="40"
									class="validate[required,minSize[6],maxSize[15]] inp-txt" type="password"></td>
								<td>密码规则：密码长度要有6-15个字符，以及必须含有数字和字母组合</td>
							</tr> 
							<tr>
								<th></th>
								<th style="height: 10px;"></th>
							</tr>
							<tr>
								<th>确认密码：</th>
								<td><input id="reg_password1" maxlength="15" size="40"
									class="validate[required,equals[reg_password]] inp-txt" type="password"></td>
								<td>确定密码</td>
							</tr>
							<tr>
								<th></th>
								<th style="height: 10px;"></th>
							</tr>
							<tr>
								<th>真实姓名：</th>
								<td><input id="reg_name" size="40" maxlength="10" class="validate[required] inp-txt" value="<?php echo $_POST['reg_name']; ?>" ></td>
								<td style="color: #ff0000;">姓名必须与你用于提款的银行户口名字一致，否则无法提款</td>
							</tr>
							<tr>
								<th></th>
								<th style="height: 10px;"></th>
							</tr>
							<tr>
								<th>手机号码：</th>
								<td><input id="reg_tel" size="40" maxlength="13" class="validate[required] inp-txt" value="<?php echo $_POST['reg_tel']; ?>"></td>
								<td style="color: #ff0000;"></td>
							</tr>
							<tr>
								<th></th>
								<th style="height: 10px;"></th>
							</tr>
							<tr>
								<th>QQ：</th>
								<td><input id="reg_qq" size="40" maxlength="30"
									class="validate[required] inp-txt" type="text" value="<?php echo $_POST['reg_qq']; ?>"></td>
								<td style="color: #ff0000;"></td>
							</tr>
							<tr>
								<th></th>
								<th style="height: 10px;"></th>
							</tr>
							<tr>
								<th>验证码：</th>
								<td><input id="reg_code" size="40" maxlength="10" class="validate[required] inp-txt"></td>
								<td><img id="imgcode" src="../imgcode.php?act=init" onclick="changeimg();" alt="none" title="看不清？点击更换一张验证图片" /></td>
							</tr>
							<tr>
								<td colspan="2" height="5"></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</ul>
			<ul class="reg_bu">
				<li class="bu_line" style="overflow: hidden; zoom: 1;"><input type="submit" name="regBtn" value=""
					class="bu_reg" style="margin-left: 238px; float: left;"> <span style="float: left; line-height: 42px;">如果您已有账号，可点击<a
						href="index.php" style="float: none; display: inline; margin: 0;" class="click_to_login">登录</a>进入
				</span></li>
				<li></li>
			</ul>
		</form>
	</div>
</body>
</html>