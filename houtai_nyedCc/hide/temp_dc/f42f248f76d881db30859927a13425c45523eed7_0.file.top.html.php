<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:25:02
  from '/www/wwwroot/lhc/houtai/templates/default/hide/top.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f36ec87a24_29556032',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f42f248f76d881db30859927a13425c45523eed7' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/top.html',
      1 => 1683762572,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6639f36ec87a24_29556032 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="user-scalable=yes, width=1225, target-densityDpi=device-dpi" />
<title><?php echo $_smarty_tpl->tpl_vars['webname']->value;?>
</title>
<link rel="stylesheet" href="/js/jquery/new/jqueryuib.min.css" />
<link rel="stylesheet" href="/default/css/adminbd.min.css" />
<link rel="stylesheet" href="/default/css/Skins/blue/skinbd.min.css" />
<?php echo '<script'; ?>
 language="javascript" src="/js/jquery-1.8.3.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript" src="/js/jquerybundle.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript">
function hideinfo(){ if(event.srcElement.tagName=="A"){
   window.status=event.srcElement.innerText}
}
document.onmouseover=hideinfo; 
document.onmousemove=hideinfo;
var globalpath = "<?php echo $_smarty_tpl->tpl_vars['globalpath']->value;?>
";

<?php echo '</script'; ?>
>

 <style>
        .ios {
            -webkit-overflow-scrolling: touch;
            overflow: auto;
        }

.menus{width:160px;background:url(/css/default/img/menu.png);text-align:center;cursor:pointer;float:left;}
.games {
    display: none;
    position: absolute;
    height: 28px;
    background: #FFF;
    z-index: 99;
    width: 100%;
}
        .clear {
            clear: both;
        }

        .clearfix:after {
            content: " ";
            display: block;
            clear: both;
        }
		.upkj{float:left;font-weight:bold;color:#000;}
		li.dropdown-menu-sub-indicator2 a.cur {
    width: 60px;
    display: inline-block !important;
	border-right: 1px solid #b6bec7;
}
.dropdown-menu-shadow2 li {
    min-width: 70px;
    border: 1px solid #efd8a1 !important;
    border-bottom: 1px solid #ddd !important;
    border-top: none !important;
    padding: 3px !important;
    background: #fff !important;
}
    </style>
	
<!--link href="/css/default/ball.css" rel="stylesheet" type="text/css" /-->
</head>
<body id="topbody" style="overflow-y: hidden;">
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='top';<?php echo '</script'; ?>
>
<ul class="games" id='nav'>
  <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['gamecs']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
     <li > <a gid=<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
  gname=<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
  fenlei=<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fenlei'];?>
 <?php if ($_smarty_tpl->tpl_vars['gid']->value == $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid']) {?>class='xz'<?php }?> href='javascript:void(0)'><?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</a></li>
  <?php
}
}
?>
</ul>
<div class="jt-container">
    <div class="header clear">
    <div class="top_w">
       <div class="logo">
            <img src="/logo.png" style="width:160px;height:60px" />
        </div>
		
        <div class="top_menu">
        <div class="expire_info" style="display: none;">
              <div class="menus"></div><div class="status"><span style="margin-left:5px;"><label class=qishu><?php echo $_smarty_tpl->tpl_vars['qishu']->value;?>
</label>期</span>  <span class="panstatus" s='<?php echo $_smarty_tpl->tpl_vars['panstatus']->value;?>
' style="margin-left:5px;"><span><?php if ($_smarty_tpl->tpl_vars['panstatus']->value == 1) {?>距关盘:<?php } else { ?>距开盘:<?php }?></span><label class="time0"><?php echo $_smarty_tpl->tpl_vars['pantime']->value;?>
</label></span><?php if ($_smarty_tpl->tpl_vars['gid']->value == 100) {?><span s='<?php echo $_smarty_tpl->tpl_vars['otherstatus']->value;?>
' class="otherstatus hide" style="margin-left:5px;"><span><?php if ($_smarty_tpl->tpl_vars['otherstatus']->value == 1) {?>距正码关盘:<?php } else { ?>距正码开盘:<?php }?></span><label class="time1"><?php echo $_smarty_tpl->tpl_vars['othertime']->value;?>
</label></span><?php }?>&nbsp;<input type="button" value="关盘" class="s1 qzclose"  />      </div><Div style="float:left;">
<label class='upqishu chu blue' m='<?php echo $_smarty_tpl->tpl_vars['upkj']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['upqishu']->value;?>
</label><span class="hei">期开奖:</span></Div><div class="upkj"></div>
        </div>
                <?php if ($_smarty_tpl->tpl_vars['slib']->value == 1) {?><a href="javascript:void(0);" class="hover lib control" i=0 x="slib">即时注单</a><?php }?>
				<?php if ($_smarty_tpl->tpl_vars['libset']->value == 1) {?><a href="javascript:void(0);" i=1 x="libset">注单预警</a><?php }?>
                <?php if ($_smarty_tpl->tpl_vars['suser']->value == 1) {?><a href="javascript:void(0);" x='suser'  i=2>用户管理</a><?php }?>
				<a href="javascript:void(0);"  i=3 x="credit">个人管理</a>
				<?php if ($_smarty_tpl->tpl_vars['zshui']->value == 1) {?><a href="javascript:void(0);"  i=4 x="zshui">赔率设置</a><?php }?>
				<?php if (($_smarty_tpl->tpl_vars['hide']->value == 1)) {?><a href="javascript:void(0);" target="frame"  i=5  x="sysconfig">系统设置</a><?php }?>
                <?php if ($_smarty_tpl->tpl_vars['baox']->value == 1) {?><a href="javascript:void(0);" target="frame" x="baox" i=6>报表查询</a><?php }?>
                <a href="javascript:void(0);" target="frame"  i=7 x="longs">开奖结果</a>
                 <?php if ($_smarty_tpl->tpl_vars['online']->value == 1) {?><a href="javascript:void(0);" target="frame"  i=8 x="online">在线统计</a><?php }?>								
                <a href="javascript:void(0);" x="logout">退出系统</a>
    </div>
		<div class="info_box vertical-container">
            <div>
                    <p id="onlineTotal">在线会员：<label class="online"><?php echo $_smarty_tpl->tpl_vars['onlinenum']->value;?>
</label></p>
                    <p>公司：<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</p>
            </div>
        </div>


    </div>
    
	 <div class="sub_w" >
		 <div class="submenudiv" style="display: none;min-width: 1536px;" >

            <div class="change_tit">当前选中：</div>
            <div id="lotteryInfo"></div>
            <div id="lotterys" style="display: none;" >
                                 <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['gamecs']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
    <a gid='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
'  fenlei='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fenlei'];?>
'  class='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['class'];?>
 g<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
' ><?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</a>
     <?php
}
}
?>

            </div>
			<ul class="MCHK6" style="display: block;">
                        </ul>
        </div>
		
			 <?php if ($_smarty_tpl->tpl_vars['libset']->value == 1) {?><div class="submenudiv" style="display: none;" > 
      <div class="change_tit">当前选中:<span>注单预警</span></div>
             <ul>
			    
			 <li><a href="javascript:void(0)" target="frame" u='libset' type='warn' >预警金额设置</a></li>
			 <?php if ($_smarty_tpl->tpl_vars['err']->value == 1) {?> <li><a href="javascript:void(0)" target="frame" u='err' type='show'>注单异常查询</a></li>
			 <li><a href="javascript:void(0)" target="frame" u='data' type='show'>数据单检测</a></li>  <?php }?>
			 
        </ul>
		</div> 
		 <?php }?>	
		        <?php if ($_smarty_tpl->tpl_vars['suser']->value == 1) {?> <div  class="submenudiv" style="display: none;" >
                    <div class="change_tit">当前选中:<span>用户管理</span></div>
                    <ul>
                            <li><a  href="javascript:void(0)" class="usermenu userzsdl">直属代理<span id="directAgentCount">(0)</span></a></li>
                            <li><a  href="javascript:void(0)" class="usermenu userqbdl">所有代理<span id="allAgentCount">(0)</span></a></li>
							 <li><a  href="javascript:void(0)" class="usermenu userzshy">直属会员<span id="directMemberCount">(0)</span></a></li>
                            <li><a  href="javascript:void(0)" class="usermenu userqbhy">全部会员<span id="allMemberCount">(0)</span></a></li>
<ul  class="dropdown-menu"  style="font-size: 0px;">
	<li class="dropdown-menu-sub-indicator" style="font-size: 12px;display: inline-block;cursor: pointer;">
                <a style="display: inline" class="accessible cur" target="frame" >选择用户级别<span class="dropdown-menu-sub-indicator">»</span></a>
                    <ul class="dropdown-menu-shadow" style="visibility: hidden; display: none;">
                    </ul>
    </li>
	 </ul>
	
<li id="userMenuDiv" class="last">
<span>当前位置：</span>
<div class="lv">
    <ul  id="drownMenu" class="dropdown-menu"  style="font-size: 0px;">

                    <li style="font-size: 12px;">
                <a href="javascript:void(0)"  style="display: inline" class="one"><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
 (公司)</a>
            </li>

    </ul>
  
 
        </div>
		
		</li>
                    </ul>
                </div>
				<?php }?>
				
						 <div class="submenudiv" style="display: none;" > 
      <div class="change_tit">当前选中:<span>个人管理</span></div>
             <ul>
			    
			 <li><a href="javascript:void(0)" target="frame" u='credit' type='show' >信用资料</a></li>
			 <?php if ($_smarty_tpl->tpl_vars['online']->value == 1) {?>
            <li><a href="javascript:void(0)" target="frame" u='history' type='show'>日志查询</a></li>
            <?php }?>
			<?php if ($_smarty_tpl->tpl_vars['xxtz2']->value == 1) {?>
			 <li><a href="javascript:void(0)" target="frame" u='caopan' type='show' >管理员账号</a></li>
			 <?php }?>
			 <li><a href="javascript:void(0)" target="frame" u='changepass2' type='show'>变更密码</a></li>
			 
        </ul>
		</div> 
		
		 <?php if ($_smarty_tpl->tpl_vars['zshui']->value == 1) {?><div class="submenudiv" style="display: none;" > 
 <div class="change_tit">当前选中:<span>赔率设置</span></div>
        <ul>
			    
			 <li><a href="javascript:void(0)" target="frame" u='zshui' type='ptype' >默认赔率</a></li>
			 <li><a href="javascript:void(0)" target="frame" u='zshui' type='setattshow' >各盘差分、参数</a></li>
			 <li><a href="javascript:void(0)" target="frame" u='zshui' type='show' >公司退水</a></li>
			 <li><a href="javascript:void(0)" target="frame" u='libset' type='auto' >降赔设置</a></li>
			 
        </ul>
		</div> 
		 <?php }?>		
<?php if (($_smarty_tpl->tpl_vars['hide']->value == 1)) {?>		 
						<div class="submenudiv" style="display: none;" > 
      <div class="change_tit">当前选中:<span>系统管理</span></div>
             <ul>
			 <li><a href="javascript:void(0)" target="frame" u='sysconfig' type='show'>系统设置</a></li>
			
            <?php if ($_smarty_tpl->tpl_vars['kj']->value == 1) {?>
			<li><a href="javascript:void(0)" target="frame" u='game' type='show'>开盘设定</a></li> 
            <li> <a href="javascript:void(0);" target="frame" u="kj" type='show'>开奖管理</a></li>
            <?php }?> 
            <?php if ($_smarty_tpl->tpl_vars['zshui']->value == 1) {?>
            <li><a href="javascript:void(0)" target="frame" u='zshui' type='ma'>玩法属性</a></li>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['news']->value == 1) {?>
            <li><a href="javascript:void(0)" target="frame" u='news' type='show'>系统公告</a></li>   
            <?php }?>
			
            <li><a href="javascript:void(0)" target="frame" u='xxtz2' type='show'>注单明细</a></li>
            
			<?php if ($_smarty_tpl->tpl_vars['now']->value == 1) {?>
			<li ><a href='javascript:void(0);' u='betinfo' type="show">注单备份</a></li>
			<li ><a href='javascript:void(0);' u='mailbox' type="show">备份邮箱设置</a></li> <?php }?>
			<li><a href="javascript:void(0)" target="frame" u='webconfig' type='show'>自动维护</a></li>
			 <li><a href="javascript:void(0)" target="frame" u='zshui' type='gameset'>彩种管理设置</a></li>
        </ul>
		</div> 
 <?php }?>	

				
		    <?php if ($_smarty_tpl->tpl_vars['baox']->value == 1) {?> <div  class="submenudiv" style="display: none;">
<div class="change_tit2 baotit">公司报表查询 </div>
<span class="top_back back back1"  uid="0" style="cursor: pointer;" > 返回</span>
<span class="top_back back back2"  style="cursor: pointer;display: none;"> 返回</span>

                </div><?php }?>
				
				
<div  class="submenudiv" style="display: none;">
                      <div class="change_tit2">开奖结果</div>
               <div class="kj_s"></div>
                </div>
				
				
				
				<div  class="submenudiv" style="display: none;">
                      <div class="change_tit2">在线统计</div>
                </div>
		
		

				
		
		

      
    </div> 

   </div> 
      
	   <div id="middle-content" class="middle-content clear" style="height: 624px;">

            <div class="main-content floatL">
                <div class="main-content-in">
                    <div id="content" class="content ios" >
                        <iframe id="frame" name="frame" src="/hide/new.php" width="100%" height="100%" frameborder="0"></iframe>
                    </div>
                </div>
            </div>

        </div>

	<div class="foot_box">
            <marquee direction="left" align="bottom" id="notices" height="30" width="90%" onmouseout="this.start()" onmouseover="this.stop()" scrollamount="2" scrolldelay="1" style="text-align:left"></marquee>
            <a href="javascript:void(0)" class="more_c more"  target="frame">更多&gt;</a>

            <div class="global_notice" title="重新加载">
                <img class="no_data" src="/default/warning/notice_icons.png?cache=" style="display: inline;">
            </div>

            
            <div class="global_notice_media"></div>

        </div>
		
      </div> 

			<?php if ($_smarty_tpl->tpl_vars['cnews']->value > 0) {
$__section_i_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['news2']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_2_total = $__section_i_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_2_total !== 0) {
for ($__section_i_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_2_iteration <= $__section_i_2_total; $__section_i_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?> 	
<div id="dialogNotice_<?php echo $_smarty_tpl->tpl_vars['news2']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['id'];?>
" class="word_wrap ui-dialog-content ui-widget-content" style="display: none; width: auto; min-height: 0px; max-height: none;"><?php echo $_smarty_tpl->tpl_vars['news2']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['content'];?>
</div>
<?php
}
}
}?>
    
<?php echo '<script'; ?>
 language="javascript" id='zhishu'>
var ngid=<?php echo $_smarty_tpl->tpl_vars['gid']->value;?>
;
var fenlei = <?php echo $_smarty_tpl->tpl_vars['fenlei']->value;?>
;
var layer=<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
;
ma = [];
     ma['紅'] = new Array(01,02,07,08,12,13,18,19,23,24,29,30,34,35,40,45,46); 
     ma['藍'] = new Array(03,04,09,10,14,15,20,25,26,31,36,37,41,42,47,48); 
     ma['綠'] = new Array(05,06,11,16,17,21,22,27,28,32,33,38,39,43,44,49); 

<?php echo '</script'; ?>
>
</body>
</html>
<?php }
}
