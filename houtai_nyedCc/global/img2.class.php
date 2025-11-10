<?php

//需GD库支持
include("data/config.inc.php");
include("data/db.php");
include("data/session.php");
//session_register("login_check_number"); 

//先成生背景，再把生成的验证码放上去

//先定义图片的长、宽

$img_height=60; 
$img_width=23;

if($_GET["act"]== "init")
{ 
    for($Tmpa=0;$Tmpa<4;$Tmpa++)
 { 
        $nmsg.=dechex(rand(0,9)); // 生成随机数，并转成十六进制

    }


  $_SESSION['login_check_number'] = $nmsg;


    $aimg = imagecreate($img_height,$img_width); //生成图片 

    imagecolorallocate($aimg, 255,255,255); //图片底色，ImageColorAllocate第1次定义颜色PHP就认为是底色了 

    $black = imagecolorallocate($aimg, 0,0,0); //定义需要的黑色 

    imagerectangle($aimg,0,0,$img_height-1,$img_width-1,$black);//先成一黑色的矩形把图片包围 


    //下面该生成雪花背景了，其实就是在图片上生成一些符号 

    for ($i=1; $i<=120; $i++)//先用100个做测试 

 { 
        imagestring($aimg,1,mt_rand(1,$img_height),mt_rand(1,$img_width),"%",imageColorAllocate($aimg,mt_rand(150,255),mt_rand(150,255),mt_rand(150,255))); 
        //就是生成＊号而已。为了使它们看起来"杂乱无章、5颜6色"，就得在1个1个生成它们的时候，让它们的位置、颜色，甚至大小都用随机数，rand()或mt_rand都可以完成。 

    } 

    //上面生成了背景，现在就该把已经生成的随机数放上来了。道理和上面差不多，随机数1个1个地放，同时让他们的位置、大小、颜色都用成随机数~~ 

    //为了区别于背景，这里的颜色不超过200，上面的不小于200 

    for ($i=0;$i<strlen($_SESSION[login_check_number]);$i++)
 { 
        imagestring($aimg, 6,$i*$img_height/4+3,3, $_SESSION[login_check_number][$i],imagecolorallocate($aimg,mt_rand(0,0),mt_rand(0,0),mt_rand(0,0))); 
    } 
    header("Content-type: image/jpeg"); //告诉浏览器，下面的数据是图片，而不要按文字显示 

    imagejpeg($aimg); //生成png格式

    imagedestroy($aimg); 
} 


?>