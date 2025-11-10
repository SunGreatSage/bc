<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:16:47
  from '/www/wwwroot/lhc/houtai/templates/default/hide/login.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f17f0b0aa9_31083705',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ab43678492d7a18e1364eb3a614823e07f34238b' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/login.html',
      1 => 1696097200,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6639f17f0b0aa9_31083705 (Smarty_Internal_Template $_smarty_tpl) {
?><html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!--避免IE使用兼容模式-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="renderer" content="webkit" />
    <title>管理员登陆</title>
    <link href="/default/css/aglogin.css" rel="stylesheet" type="text/css" />
    <?php echo '<script'; ?>
 language="javascript" src="/js/jquery-1.8.3.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 language="javascript" src="/js/md5.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript">
	$(function(){
	$("input:password").click(function(){
	     $(this).attr("placeholder","");
	});
	$("#password").blur(function(){
		 if($(this).val()==''){
	       $(this).attr("placeholder",$(this).attr("title"));
		 }else{
	       $("#pass").val(men_md5_password($("#password").val()));	
		 }
	});
	$("input:text").click(function(){
	     $(this).attr("placeholder","");
	});
	$("input:text").blur(function(){
		if($(this).val()==''){
	      $(this).attr("placeholder",$(this).attr("title"));
		}
	});

	$(".right").click(function () {
                $(".mfa").slideToggle("slow");
            });
});
$(document).ready(function () {
            $('.code img').click(function() {
               $(this).attr('src',"../imgcode.php?act=init&"+Math.random());
            });
            Login();
        });

function Login() {
            $(".submit_btn").on("click", function () {
                var Account = $("input[name=username]");
                if (!$.trim(Account.val())) {
                    alert("请输入登陆名!");
                    Account.focus();
                    return false;
                }
                var Password = $("input[name=password]");
                if (!$.trim(Password.val())) {
                    alert("请输入登陆密码!");
                    Password.focus();
                    return false;
                }
                var Code = $("input[name=code]");
                if (!$.trim(Code.val())) {
                    alert("请输入正确的验证码!");
                    Code.focus();
                    return false;
                }
                var _this = $(this);
            });
        }

function stop(){
   return false;
}

function hideinfo(){ if(event.srcElement.tagName=="A"){
   window.status=event.srcElement.innerText}
}
document.onmouseover=hideinfo; 
document.onmousemove=hideinfo;

<?php echo '</script'; ?>
>

</head>

<body class="login_bg" id="headerview">
    <form autocomplete="off" id='form' name="form" method="post">
	<input type="hidden" name="xtype" value="login" />
	<input type="hidden" name='pass' id='pass' value="" />
	<input type="hidden" name='type' id='type' value="1" />
        <div class="login_wrap header">
            <div class="login_box">
                <div class="login_tips">
                    <span class="left"></span>
					<span class="right"></span>
                </div>
                <ul>
                    <li>
                        <i>帐&emsp;号：</i>
                        <input  type="text" name="username" id="username" autocomplete="off" placeholder="请输入您的账号" title="请输入您的账号">
                    </li>
                    <li>
                        <i>密&emsp;码：</i>
                        <input name="password" type="password" name="password"  id="password" autocomplete="off" placeholder="请输入您的密码" title="您的密码">
                    </li>
                    <li>
                        <i>验证码：</i>
                        <dt class="code">
                            <img src="../imgcode.php?act=init" id="imgcode" alt="none" title="看不清？点击更换一张验证图片">
                        </dt>
                        <ol>
                            <input name="code" type="text" id='code' class="ico_key" placeholder="请输入验证码" title="验证码" />
                        </ol>
                    </li>
					 <li class="mfa">
                        <i>二次验证码：</i>
                        <input name="mfacode" type="text" class="ico_key" placeholder="请输入二次验证码" title="二次验证码">
                    </li>

                </ul>
                <input class="submit_btn" type="submit" value="登 录">
                <div class="login_tool">
                    <a class="ico_login"></a>
                        <a class="ico_aplus" href="javascript:void(0);"></a>
                </div>
            </div>

        </div>
    </form>


</body>
</html><?php }
}
