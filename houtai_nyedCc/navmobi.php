<?php
if (!defined('copyright') && copyright != 'YINHE'){
   echo "<script language='javascript'>window.location.href='http://baidu.com';</script>";
   exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
         <meta content="width=device-width; initial-scale=1.0; user-scalable=0; maximum-scale=1; minimum-scale=1.0" name="viewport" />
        <meta content="yes" name="apple-mobile-web-app-capable" />
        <meta content="black" name="apple-mobile-web-app-status-bar-style" />
        <meta name="format-detection" content="email=no; telephone=no" />
        <title>页面导航</title>        

        <style>
        body,ul,ol,dl,dd,dt,li,p,div,form,h1,h2,h3,h4,img,a img {padding:0;margin:0;border:0;}
        body,html{height: 100%; position: relative; color: #000;}
        input {font-size:16px; font-family:helvetica, san-serif; outline:none;}
        .f_L{float:left}
        /* herder */
       .header-model{background:url(../imgn/mobi/h-bg.png);color:#fff;padding:6px;box-shadow: 0 2px 5px #333;font-size:1.3em;}        
        .tc{text-align:center;}
        .login-win{margin:0 3%;min-width:300px}
        .login-win dl{width:100%;}
        .login-win h2{color:#666666;margin:20px 0;font-size:26px;}
        .login-win .left{width:48.5%;margin: 0 1.5% 0 0; background-color:#f2f2f2;}
        .login-win .right{width:48.5%;margin: 0 0 0 1.5%; background-color:#f2f2f2;}
        .login-win .title{color:#5697cd;text-align: center;font-size:23px;margin:15px 0;}
        .login-win .con{height:250px; overflow:auto;}
        .login-win .con li{text-align:center;line-height:46px;font-size:26px;margin:20px;background-color: #438cd0;list-style: none;}
        .login-win .con li a{display:block;width:100%;line-height:44px;height:44px;color: #fff;text-decoration: none;}
        .login-win .con .special-css {text-align:center;line-height:46px;font-size:26px;margin:20px;background-color: #22ac38;list-style: none;}
        </style>
    </head>
    <body>
        <div class="header-model tc rel">
            登录
        </div>
        <div class="login-win">
        	<dl>
        		<dt><h2><?php echo $msql->f('webname'); ?> - 请先择线路以登录系统</h2></dt>
        		<dd class="f_L left">
        			<div class="con">
        				<ul>
				<li><a href="?line=1&type=<?php echo 'H3678'.base64_encode('uuuuu88888')?>&mobi=1">线路1</a></li>
				<li><a href="?line=2&type=<?php echo 'C2378'.base64_encode('uuuuu88888')?>&mobi=1">线路2</a></li>
				<li><a href="?line=3&type=<?php echo 'D9008'.base64_encode('uuuuu88888')?>&mobi=1">线路3</a></li>
				<li><a href="?line=4&type=<?php echo 'V1888'.base64_encode('uuuuu88888')?>&mobi=1">线路4</a></li>
        				</ul>
        			</div>
        			<h3 class="title">会员手机版线路</h3>
        		</dd>
        		<dd class="f_L right">
        			<div class="con">
        				<ul >
				<li><a href="?line=1&type=<?php echo 'H3678'.base64_encode('uuuuu88888')?>">会员1</a></li>
				<li><a href="?line=2&type=<?php echo 'C2378'.base64_encode('uuuuu88888')?>">会员2</a></li>
				<li><a href="?line=1&type=<?php echo 'H3678'.base64_encode('aaaaa88888')?>">代理1</a></li>
				<li><a href="?line=2&type=<?php echo 'C2378'.base64_encode('aaaaa88888')?>">代理2</a></li>
        				</ul>
        			</div>
        			<h3  class="title">桌面版线路</h3>
        		</dd>
        	</dl>
        </div>
             

    </body>
</html>

