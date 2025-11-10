<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
if($_SESSION['admin']!=1) exit;
switch ($_REQUEST['xtype']) {
    case "this":
	   $v = "<ul class='menu' >";
	   $v .= " <li><a href='javascript:void(0);' X='caopan'>操盘员</a></li>";
	   $v .= " <li><a href='best_plan.php?xtype=show'>最佳控盘计划</a></li>";
	   $v .= "<li><a href='zshui.php?xtype=ma'>号码属性</a></li>";
	   $v .= "<li><a href='javascript:void(0);' X='zshui'>默认退水</a></li>";
	   $v .= "<li><a href='javascript:void(0);' X='setatt'>赔率参数</a></li>";
       $v .= "<li><a href='javascript:void(0);' X='history'>记录管理</a></li>";  
       $v .= "<li><a href='javascript:void(0);' X='sysconfig'>参数配置</a></li>";         
       $v .= "<li><a href='javascript:void(0);' X='xxtz2'>注单删改</a></li>";	
	   if($_SESSION['hides']==1 | $_SESSION['hide']==1){
	   $v .= "<li><a href='javascript:void(0);' X='webconfig'>网站配置</a></li>";
	   $v .= "<li><a href='javascript:void(0);' X='admins'>管理员</a></li>";	   
	   $v .= "<li><a href='javascript:void(0);' X='game'>游戏配置</a></li>";
	   $v .= "<li><a href='javascript:void(0);' X='xxtz2'>注单删改</a></li>";	
	   $v .= "<li><a href='zshui.php?xtype=gameset'>彩种开放</a></li>";    
	   }
	   if($_SESSION['hides']==1){
	   $v .= "<li><a href='javascript:void(0);' x='check'>检测</a></li>";
	   $v .= "<li><a href='javascript:void(0);' X='message'>会员反馈</a></li>";
	   $v .= "<li><a href='class.php?xtype=classpan'>玩法归类</a></li>";
	   $v .= "<li><a href='class.php?xtype=bigclass'>玩法大分类</a></li>";
	   $v .= "<li><a href='class.php?xtype=sclass'>玩法小分类</a></li>";
	   $v .= "<li><a href='class.php?xtype=class'>玩法分类</a></li>";
	   $v .= "<li><a href='play.php?xtype=show'>玩法列表</a></li>";
	   $v .= "<li><a href='javascript:void(0);' X='nows'>注单删改2</a></li>";
	   $v .= "<li><a href='play.php?xtype=downlist'>下载记录</a></li>";
	   
	
	   }
	   $v .= "</ul>"; 
	   $tpl->assign('v',$v);
	   $tpl->display("left.html");
	break;

}

?>