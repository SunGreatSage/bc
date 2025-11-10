<?php
/* Smarty version 3.1.48, created on 2024-05-07 20:07:11
  from '/www/wwwroot/lhc/houtai/templates/default/hide/userlogin.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a196f976623_41181451',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9181084fcbcde3e68e9636b7c997eede00022ee7' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/userlogin.html',
      1 => 1681986410,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663a196f976623_41181451 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?> 
<?php echo '<script'; ?>
 language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"><?php echo '</script'; ?>
>
<style>
a.red{color:#D50000}
.control a.blue{border: 1px solid #ddd;
    color: #2161b3 !important;
    background: #f9f9f9;
	 padding: 4px 8px;}
	 .control span.chu{border: 1px solid #2161b3;
    color: #fff !important;
    background-color: #2161b3;
    padding: 4px 10px;}
	select{     padding: 3px 5px;}
</style>
</head>
<body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='his';<?php echo '</script'; ?>
>
<div class="jt-container">
	<div class="top_info">
		<span class="title">登录日志</span><div class="center">日期范围：<INPUT type="text" value="<?php echo $_smarty_tpl->tpl_vars['deldate']->value;?>
" class="deldate date" style="width: 80px;" size=8 />&nbsp;之前&nbsp;<input type="button" value="清空" class="button del btn4"  /></div><span class="right">&nbsp;&nbsp;<a class="back" onclick="javascript:history.back(-1);" href="javascript:void(0);">返回</a></span>
	</div>
<div class="s_main">
			<div class="sub_w2" style="margin:0px;">
            

            <div class="page-tabs">
                <div class="tab_con2 clear pages" id="scroll_div2">
                    <ul id="sliderLottery">
						<li>
					<a href="javascript:void(0)" class="show"  page="show">登录日志</a>
<a href="javascript:void(0)" class="useredit" page="useredit">会员操作日志</a>
<a href="javascript:void(0)" class="adminedit" page="adminedit">代理操作日志</a>
<a href="javascript:void(0)" class="agentpeilv" page="agentpeilv">公司控盘日志</a>	
<a href="javascript:void(0)" class="adminpeilv" page="adminpeilv">代理控盘日志</a>
                    </ul>
                </div>
            </div>
        </div>
 <table class="data_table list table_center nrtb" style="margin-top:10px;">
 <thead><TR>
 <th><input type="checkbox" id='clickall' />全选</th><th>序  号</th><th>登陆账号</th><th>登陆时间</th><th>IP</th><th>IP归属</th><th style="display: none;">服务器</th><th>操作系统</th><th>状态</th><th><input type="button" class="btn7 btnf" id='delselect' style="width: 70px;"  value="删除选中" /></th>
 </TR></thead> 
 <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['l']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
<TR><td><input type="checkbox" value='<?php echo $_smarty_tpl->tpl_vars['l']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['id'];?>
' /></td><td><?php echo $_smarty_tpl->tpl_vars['l']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['id'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['l']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['username'];?>
</td><td style="color: olivedrab;" ><?php echo $_smarty_tpl->tpl_vars['l']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['time'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['l']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ip'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['l']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['addr'];?>
</td><td style="display: none;"><?php echo $_smarty_tpl->tpl_vars['l']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['server'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['l']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['os'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['l']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'];?>
</td><td><input type="button" class="delone btn4 btnf" value='删除' /></td></TR>
 <?php
}
}
?>
  </table>
  <div class="control">
		<span><?php echo $_smarty_tpl->tpl_vars['page']->value;?>
</span>
	</div>	
	</div>
</div>
<?php echo '<script'; ?>
 language="javascript">
	var page = 'show';
	<?php echo '</script'; ?>
>
<div id='test'></div>
</body>
</html>
<?php }
}
