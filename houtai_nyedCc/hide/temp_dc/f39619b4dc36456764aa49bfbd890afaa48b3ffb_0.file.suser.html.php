<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:29:38
  from '/www/wwwroot/lhc/houtai/templates/default/hide/suser.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f482dbe072_21311324',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f39619b4dc36456764aa49bfbd890afaa48b3ffb' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/suser.html',
      1 => 1681978388,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:headers.html' => 1,
  ),
),false)) {
function content_6639f482dbe072_21311324 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:headers.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 language="javascript" src="/js/jquery-ui.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="/js/jquery.loadmask.js?v=004"><?php echo '</script'; ?>
>
<link href="/default/css/jquery.loadmask.css" rel="stylesheet" />
<style type="text/css">
.query_panel input.s1{padding:2px;}
.tinfo{display:none;position:absolute}
input.tytxt{width:60px !important;}
#usernameMsg {
    margin-left: 5px;
}
em.success {
    background: url(../css/images/checked.gif) no-repeat 0px 0px;
    margin-left: 3px;
    padding-left: 16px;
    color: blue;
}

em {
    font-style: italic;
}

.error {
    color: #f97c00;
}

em.error {
    background: url(../css/images/unchecked.gif) no-repeat 0px 0px;
    margin-left: 3px;
    padding-left: 16px;
    color: red;
}
.hide {
    display: none;
}
.param_panel .changed {
    background-color: #b5d8f5 !important;
}
</style>
</head>
<body style="overflow:hidden">
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='suser';<?php echo '</script'; ?>
>
	<div class="jt-container">
		<div class="top_info">
		<span class="title"><label>公司</label> -&gt; <label>直属代理</label></span>
			<div class="center">
				<div class="query_panel">
					<span class="input_panel">
					<label>模式：<select id="" name="resetType" class="fudong">
        <option value="all" selected="selected">全部</option>
        <option value="0">信用</option>
        <option value="1">现金</option>
</select>
</label>
					<label>状态：<select id="" name="status" class="status">
        <option value="all"  selected="selected">全部</option>
        <option value="1">启用</option>
        <option value="2">冻结</option>
        <option value="0">停用</option>
</select>
</label> <label>搜索：</label> 账号或名称：<input name="name" class="input" id="usernames">
					</span> <input type="button" value="查找" class="query searchbtn btn4">
     <input type="hidden" name="fid"  id="fid" value="<?php echo $_smarty_tpl->tpl_vars['fid']->value;?>
" username='<?php echo $_smarty_tpl->tpl_vars['username']->value;?>
' />
     <input type="hidden" name='layer' id='layer'  toplayer = '<?php echo $_smarty_tpl->tpl_vars['flayer']->value;?>
' value='<?php $_smarty_tpl->_assignInScope('flayers', ((string)($_smarty_tpl->tpl_vars['flayer']->value+1)));
echo $_smarty_tpl->tpl_vars['flayers']->value;?>
' />
     <input type="hidden" id='saveuserid' />
     <input type="hidden" id='topid' value="<?php echo $_smarty_tpl->tpl_vars['fid']->value;?>
" />
     <input id='optionselect' value='选择项' type="button" class="btn3" />	 
     选定:
     <input id='openselect' value='启用'  type="button" class="btn4" />
     <input id='pauseselect' value='冻结'  type="button" class="btn4" />
     <input id='closeselect' value='停用' type="button" class="btn4" />
     <input  id='delselect' value='删除'  type="button" class="btn4" />
     <!--input  id='jiaozheng' value='额度较正'  type="button" class="btn2" />
     <!--input  id='resetmoney' value='低频信用恢复'  type="button" class="btn4" /-->
     <!--input  id='resetkmoney' value='信用恢复'  type="button" class="btn2" /-->
				</div>
				 <a class="addtop add" href="javascript:void(0);" type='ag'>新增一级代理</a>  <a class="add" style='display:none;' href="javascript:void(0);" type='us'>新增会员</a> 
   
			</div>

			<div class="right">
				<a class="back" href='javascript:void(0)'>返回</a>
			</div>
		</div>
		<div class="contents">
			<ul class="left_panel" style="display: none;" >
				<li class="title">[<label>公司</label>]下线</li>

			</ul>
			<div class="user_list s_main">
				<table class="list user_tb table_ball">
					<thead>
						<tr>
                            <th class="option" style="display: none;"><input type="checkbox" value="all" class="selectall" /></th>
							<th class="online">在线</th>
							<th class="parent">上级账号</th>
							<th class="type">用户类型</th>
							<th class="username">账号</th> 
                            <?php if ($_smarty_tpl->tpl_vars['config']->value['fasttype'] == 1) {?>
							<th class="account">额度</th> 
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['config']->value['slowtype'] == 1) {?>
							<th class="account" style="display: none;">低频彩额度</th> 
                            <?php }?>
							<th class="share">占成</th>  
							<!--<th class="branch ag">下线</th>-->
							<th class="branch ag">代理</th>
							<th class="branch ag">会员</th>
                            <th class="ag">新增</th>
                            <th class="us">盘口</th>
							<th class="created">新增日期</th>
							<th class="status">状态</th>
							<th class="op">功能</th>
						</tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>
                
<div class="pagination page_info page_control page">

</div>

			</div>
		</div>
	</div>
 
 
<div class="utb"></div>
 

<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-draggable ui-resizable zcmxtb" tabindex="-1" role="dialog" aria-describedby="shares" aria-labelledby="ui-id-1" style="position: absolute; height: auto; width: 900px; display: none;"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle"><span id="ui-id-1" class="ui-dialog-title">pei099# 占成明细</span><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" title="Close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">Close</span></button></div><div id="shares" class="popdiv ui-dialog-content ui-widget-content" style="display: block; width: auto; min-height: 96px; max-height: 346px; height: auto;"><table id="liml" class="data_table table_ball zctb">
</table>
</div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div></div>
 

 
<div id="statusPanel" class="popdiv" style="position:absolute; display: none;"><div class="title">修改帐户状态<i></i></div><div class="statuslist"><label><input name="ustatus" type="radio" value="1">启用</label><label><input name="ustatus" type="radio" value="2">冻结</label><label><input name="ustatus" type="radio" value="0">停用</label></div></div>

<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-draggable ui-resizable xxdiv" tabindex="-1" role="dialog" aria-describedby="shares" aria-labelledby="ui-id-1" style="position: absolute; height: auto; width: 900px; display: none;"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle"><span id="ui-id-1" class="ui-dialog-title">pei099# 占成明细</span><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" title="Close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">Close</span></button></div><div id="shares" class="popdiv ui-dialog-content ui-widget-content" style="display: block; width: auto; min-height: 96px; max-height: 346px; height: auto;"> <table class="data_table table_ball xxtb">
 <thead><tr class="bt">
 <th>彩种</th>
  <th>期数</th>  
  <th>内容</th>
  <th>金额</th>
  <th>赔率</th>
  <th>退水</th>
  <th>会员</th>
  <th>时间</th>
 </tr></thead><tbody></tbody>
</table></div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div></div>
<input type="hidden" class='sort' orderby='time' sorttype='DESC' page='1' xtype='2' js='0' gid='99' con='' />
<table class="recordtb info_table hide">
<tr><td class="e" valign="top"></td><td class="l" valign="top"></td></tr>

</table>
<table class="moneypasstb" style="width: 400px;display: none;">
	<tbody>
		<tr>
			<th>账号</th>
			<td></td>
		</tr>
		<tr>
			<th>转账密码</th>
			<td><input id="transferPassword"  type="password" class="input"></td>
		</tr>
		<tr>
			<th>确认转账密码</th>
			<td><input id="ckTransferPassword" type="password" class="input"></td>
		</tr>
		<tr>
			<th></th>
			<td><input class="moneypasssend" type="button" value="确定"></td>
		</tr>
	</tbody>
</table>


<table class="tinfo info_table copytb">
<tr><th>新账号</th><td><input type="text" class="copyusername input" uid='0' /></td><td rowspan="2"><input type="button" value="复制" class="copysend button" /></td></tr>
<tr><th>名称</th><td><input type="text" class="copyname input" /></td></tr>
</table>
<table class="tinfo cpasstb">
<tr><th>账号</th><td><label class="cpassusername"></label></td><td rowspan="2"><input type="button" value="修改密码" class="cpasssend button" /></td></tr>
<tr><th>新密码</th><td><input type="text" class="cpass1 input" /></td></tr>
<tr><th>确认密码</th><td><input type="text" class="cpass2 input" /></td></tr>
</table>
<table class="tinfo moneytb">
<tr><th>低频彩额度</th><td><input type="text" class="maxmoney input" uid='0' /><input type="text" class="money input" uid='0' /></td><td rowspan="2"><input type="button" value="提交" class="moneysend button" /></td></tr>
<tr><th>快开彩额度</th><td><input type="text" class="kmaxmoney input" /><input type="text" class="kmoney input" /></td></tr>
</table>
<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-draggable ui-resizable edudiv" tabindex="-1" role="dialog" aria-describedby="shares" aria-labelledby="ui-id-1" style="position: absolute; height: auto; width: 900px; display: none;"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle"><span id="ui-id-1" class="ui-dialog-title">pei099# 占成明细</span><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" title="Close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">Close</span></button></div><div id="shares" class="popdiv ui-dialog-content ui-widget-content" style="display: block; width: auto; min-height: 96px; max-height: 346px; height: auto;"> 
<table class="data_table info_table edutb table_ball">
	<tbody>
		<tr>
			<th>账号</th>
			<td class="t_left"></td>
		</tr>
		<tr>
			<th>额度</th>
			<td class="t_left"><span></span>&nbsp;<input type="button" class="s1 tiquall" value="提取全部额度" ></td>
		</tr>
		<tr><th>当前余额</th><td class="t_left">0</td></tr>
		<tr><th>上级可用额度</th><td class="t_left">0</td></tr>
		<tr>
			<th>类型</th>
			<td class="t_left"><label><input type="radio" name="types" value="0">加额(+)</label> <label><input type="radio" name="types" value="1">减额(-)</label></td>
		</tr>
		<tr>
			<th>金额</th>
			<td class="t_left"><input name="balance" class="input"> <span id="popDx" style="color:red" class="dx"></span></td>
		</tr>
		<tr>
			<th></th>
			<td class="t_left"><input id="btnOK" class="s1 setmoney btn4" type="button" value="确定" ></td>
		</tr>
	</tbody>
</table></div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div></div>


<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-draggable ui-resizable zztb" tabindex="-1" role="dialog" aria-describedby="transferPasswordDialogue" aria-labelledby="ui-id-2" style="position: absolute; height: 142.28px; width: 550.28px; top: 230px; left: 400px; display: block; z-index: 101; right: auto; bottom: auto;display: none;"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle"><span id="ui-id-2" class="ui-dialog-title">请输入转账密码</span><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close closezz" role="button" title="Close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">Close</span></button></div><div id="transferPasswordDialogue" class="popdiv ui-dialog-content ui-widget-content" style="display: block; width: auto; min-height: 95px; max-height: none; height: auto;"><table class="list data_table info_table"><tbody><tr><th>转账密码</th><td><input id="inputTransferPassword" type="password" class="input"></td></tr></tbody></table><div style="position:absolute;right:170px;bottom:20px;"><input id="transferOk" type="button" value="确定"></div></div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div></div>

<table class="tinfo fedittb">
<tr><th></th><td><input type="text" class="feditmoney input" uid='0' /></td></tr><tr><td colspan=2 ><input type="button" value="提交" class="feditsend button" /><input type="button" value="关闭" class="feditclose button" /></td></tr>

</table>
<?php if ($_smarty_tpl->tpl_vars['hides']->value == 1) {?>


<table class="list nss">
<tr><td><input type="text" size="20" class="mess" /><input type="button" class="close input" value="关闭" /><input type="button" class="sendmess input" value="发送" /></td></tr>
</table>
<?php echo '<script'; ?>
 language="javascript" >
$(function(){
	$(".nss .close").click(function(){
	   $(".nss").hide();
	});
	$(".nss .sendmess").click(function(){
		var news = $(".nss .mess").val();
		var uid  = $(".nss").attr('uid');
					$.ajax({
					type: 'POST',
					url: 'message.php',
					cache: false,
					data: 'xtype=nss&uid=' + uid+"&news="+news,
					success: function(m) {
					
						if (Number(m) == 1) {
							alert('ok')
						}
					}
				})
	});
});

<?php echo '</script'; ?>
>
<STYLE type="text/css">
.nss{width:300px;position:absolute;background:#fff;display:none}
</STYLE>
<?php }?>
<div id='test'></div>
<input type='text'  id='test2' class="hide" />
<input type="text" id='tests' class="hide"   /> 
<input type="text" id='testss' class="hide"   /> 
<?php echo '<script'; ?>
 language="javascript">
layernames= new Array();
layername = new Array();
<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['layer']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
] = new Array();
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
]['wid'] = <?php echo $_smarty_tpl->tpl_vars['layer']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['wid'];?>
;
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
]['layer'] = new Array();
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['layer']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'], 'j', false, 'key');
$_smarty_tpl->tpl_vars['j']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['j']->value) {
$_smarty_tpl->tpl_vars['j']->do_else = false;
?>
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
]['layer'][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
] = '<?php echo $_smarty_tpl->tpl_vars['j']->value;?>
';
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
]['namehead'] = '<?php echo $_smarty_tpl->tpl_vars['layer']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['namehead'];?>
';
<?php
}
}
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['layer']->value[0]['layer'], 'j', false, 'key');
$_smarty_tpl->tpl_vars['j']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['j']->value) {
$_smarty_tpl->tpl_vars['j']->do_else = false;
?>
layername[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
] = '<?php echo $_smarty_tpl->tpl_vars['j']->value;?>
';
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
var maxlayer = <?php echo $_smarty_tpl->tpl_vars['maxlayer']->value;?>
;
var maxrenflag = <?php echo $_smarty_tpl->tpl_vars['maxrenflag']->value;?>
;
var ustatus = 'all';
var wids=0;
var treeflag=false;
var fidarr=[];
var fidindex = -1;
var slowtype = <?php echo $_smarty_tpl->tpl_vars['config']->value['slowtype'];?>
;
var fasttype = <?php echo $_smarty_tpl->tpl_vars['config']->value['fasttype'];?>
;
var zcmode = <?php echo $_smarty_tpl->tpl_vars['config']->value['zcmode'];?>
;
var moneypassflag = <?php echo $_smarty_tpl->tpl_vars['moneypassflag']->value;?>
;
var transferok=false;
<?php echo '</script'; ?>
>
</body></html><?php }
}
