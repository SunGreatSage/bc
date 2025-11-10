<?php
if (!defined('copyright') && copyright != 'YINHE'){
   echo "<script language='javascript'>window.location.href='http://baidu.com';</script>";
   exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="target-densitydpi=device-dpi, width=1370, user-scalable=yes" /><title>139搜索</title><link href="guides/jquery.cover.css" rel="stylesheet" type="text/css" /><link href="guides/master.css" rel="stylesheet" type="text/css" /><link href="guides/layout.css" rel="stylesheet" type="text/css" /><link href="guides/additional.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="guides/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="guides/jquery.cover.js"></script>
    <script type="text/javascript" src="guides/jquery.form.min.js"></script>
</head>
<body>
   <form name='form' method="post" target="_self"><input type='hidden' name='act' value='1'> 
    <!-- 登录、注册 start-->
    <div class="win" id="win">
        <div class="win_login">
            <div class="div_login" id="win_login">
                <ul class="win_title">
                    <li><span>用户登录</span><a onclick="win.style.display='none',win_login.style.display='none',win_reg.style.display='none'">关闭</a></li>
                </ul>
                <ul class="user_input">
                    <li class="input_te"><span class="ico_user">图片</span><input type="text" name="txtUserName" id="txtUserName" value="" onfocus="if(value==defaultValue){value='';this.style.color='#000'}" onblur="if(!value){value=defaultValue;this.style.color='#a0a0a0'}" /></li>
                    <li class="input_te"><span class="ico_pass">图片</span><input type="password" name="txtUserPwd" id="txtUserPwd" value="" onfocus="if(value==defaultValue){value='';this.style.color='#000'}" onblur="if(!value){value=defaultValue;this.style.color='#a0a0a0'}" /></li>
                    <li class="reg_bu"><a href="javascript:;" class="bu_login">登录</a></li>
                </ul>
            </div>

            <div class="div_reg" id="win_reg">
                <ul class="win_title">
                    <li><span>用户注册</span><a onclick="win.style.display='none',win_login.style.display='none',win_reg.style.display='none'">关闭</a></li>
                </ul>
                <ul class="reg_title">
                    <li>
                        <div class="reg_titlepic">图片</div>
                        请在以下填写您的注册信息</li>
                </ul>
                <ul class="reg_te">
                    <li><span>用户账号：</span><input name="userName" type="text" id="userName" class="te01" />由3-20个数字字符组成</li>
                    <li><span>登录密码：</span><input name="userPwd" type="password" id="userPwd" class="te01" />6位数以上</li>
                    <li><span>确认密码：</span><input name="userPwd1" type="password" id="userPwd1" class="te01" />密码二次确认</li>
                    <li><span>电子邮箱：</span><input name="userMail" type="text" id="userMail" class="te01" /></li>
                </ul>
                <ul class="bu_line">
                    <li class="reg_bu"><a href="javascript:;" class="bu_reg">确认注册</a></li>
                    <li class="reg_msg"></li>
                </ul>
            </div>
        </div>

        <div class="win_bg"></div>
    </div>
    <!-- 登录、注册 end-->
    <div id="pagepiling">
        <!-- section 1 start -->
        <div class="section" id="section1">
            <div class="page">
                <img src="guides/pagebg.jpg" class="myBackgroundImage">
                <div class="header">
                    <img src="guides/pg0header.jpg" class="myBackgroundImage">
                    <div class="headerpage0">
                        <ul>
                            <li class="logo">
                                <img src="guides/smalllogo.png" /></li>
                            <li class="header_nav">
                                <a href="http://www.icbc.com.cn/icbc/" target="_blank">工商银行</a>
                                <a href="http://www.95599.cn/cn/" target="_blank">农业银行</a>
                                <a href="http://www.ccb.com/cn/home/index.html" target="_blank">建设银行</a>
                                <a href="http://www.95559.com.cn/" target="_blank">交通银行</a>
                                <a href="http://www.boc.cn/" target="_blank">中国银行</a>
                                <a href="http://www.cmbchina.com/" target="_blank">招商银行</a>
                                <a href="http://www.psbc.com/portal/zh_CN/index.html" target="_blank">邮政银行</a>
                                <a href="http://www.cmbc.com.cn/" target="_blank">民生银行</a>
                            </li>
                            <li class="header_login">
                                <a onclick="win.style.display='block',win_login.style.display='block',win_reg.style.display='none'" class="login_enter">登录</a>|
                        <a onclick="win.style.display='block',win_login.style.display='none',win_reg.style.display='block'">注册</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <ul class="bd_search">
                    <li class="bd_logo" style="height: 116px">
                        <img src="guides/mainlogo.png" width="392" height="116" /></li>
                    <li class="bd_input">
                        <input  name="code" id="code" type="text" class="searchTop_txt" /><input name="" type="submit" value="搜索一下" class="searchTop_btn" /></li>
                </ul>
                <div class="footer">
                    <img src="guides/pg0footer.jpg" class="myBackgroundImage" />
                    <div class="footerpage0">
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- section 1 end -->
    </div>
    <script type="text/javascript" src="guides/common.js"></script>
    </form>
<div style="display: none"></div>
</body>
</html>
