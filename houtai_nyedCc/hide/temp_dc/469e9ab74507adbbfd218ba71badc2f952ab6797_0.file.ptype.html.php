<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:33:30
  from '/www/wwwroot/lhc/houtai/templates/default/hide/ptype.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f56a423892_07905407',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '469e9ab74507adbbfd218ba71badc2f952ab6797' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/ptype.html',
      1 => 1682881528,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_6639f56a423892_07905407 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link href="/default/css/control.css" rel="stylesheet" />
<link href="/default/css/Site.css" rel="stylesheet" />
<link href="/default/css/jquery.loadmask.css" rel="stylesheet" />
<?php echo '<script'; ?>
 src="/js/sitetool.js?v=004"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="/js/jquery.loadmask.js?v=004"><?php echo '</script'; ?>
>
</head>
<body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='ptype';<?php echo '</script'; ?>
>
<div class="jt-container">
	<div class="top_info">
		<span class="title" style="color:#000000">默认赔率</span>
		<div class="center"  style="margin-left: 100px;" ><div id="cmcontrol">
		<input type="button" data-ignore="1" value="保存"  class="button btn1 send" />
		<input type="button" data-ignore="1" value="重置"  class="button btn1" />
		<input type="button" gid="<?php echo $_smarty_tpl->tpl_vars['gid']->value;?>
" class="button btn7 yiwotongbu" value="以我同步其他彩" >
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span style="color: #000000;">如需设置其他盘口赔率,请移步赔率各盘差分、参数</span>
		</div>
        </div>
		
		<span class="right">&nbsp;&nbsp;<a class="back" onclick="javascript:history.back(-1);" href="javascript:void(0);">返回</a></span>
	</div>
	
	<div class="s_main">	
		<div id="p_para" class="contents param_panel input_panel tab_panel">

                        <table class="layout" templatecode="HK6">
                            <tbody><tr>
							<?php if ($_smarty_tpl->tpl_vars['config']->value['slowtype'] == 1) {?>
                                <td height="30" colspan="2" align="left">
								<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
                                        <?php if ($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'] == 0) {?><span loto="MCHK6" group="HK6" class="g<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
 group_tap" gid='<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
'><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</span><?php }?>
								 <?php
}
}
?>
                                    
                                </td>
								<?php }?>
                            </tr>
							</tbody>
							</table>
							 </div>
			
	<div id="con_six" class="hover param_panel">
    <table class="layout">
        <tr>
            <td class="panel">
                <table class="list table_ball">
  <thead>
<tr>
  <th style="display: none;">序 号</th><th>种类</th><th><font class="font_r">默认赔率(A盘)</font></th>
</tr></thead>
<tbody class="tablelist">
<?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['ptype']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = min(($__section_i_1_loop - 0), $__section_i_1_loop);
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
 <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null) <= $_smarty_tpl->tpl_vars['nums']->value) {?>
   <TR>
   <th style="display: none;"><?php echo $_smarty_tpl->tpl_vars['ptype']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['id'];?>
</th><th class="para_pjName"><?php echo $_smarty_tpl->tpl_vars['ptype']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['c'];?>
<input style="display: none;" type="text" class="txt c" value="<?php echo $_smarty_tpl->tpl_vars['ptype']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['c'];?>
" /></th><td><input type="text" class="txt p" value="<?php echo $_smarty_tpl->tpl_vars['ptype']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['p'];?>
" /></td></tr>
   <?php }?>
   <?php
}
}
?>
 </tbody>
</table>


	  </td>
	  <td class="panel">
                <table class="list table_ball">
  <thead>
<tr>
  <th style="display: none;">序 号</th><th>种类</th><th><font class="font_r">默认赔率(A盘)</font></th>
</tr></thead>
<tbody class="tablelist">
<?php
$__section_i_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['ptype']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_2_total = min(($__section_i_2_loop - 0), $__section_i_2_loop);
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_2_total !== 0) {
for ($__section_i_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_2_iteration <= $__section_i_2_total; $__section_i_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
 <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null) > $_smarty_tpl->tpl_vars['nums']->value) {?>
   <TR>
   <th style="display: none;"><?php echo $_smarty_tpl->tpl_vars['ptype']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['id'];?>
</th><th class="para_pjName"><?php echo $_smarty_tpl->tpl_vars['ptype']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['c'];?>
<input style="display: none;" type="text" class="txt c" value="<?php echo $_smarty_tpl->tpl_vars['ptype']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['c'];?>
" /></th><td><input type="text" class="txt p" value="<?php echo $_smarty_tpl->tpl_vars['ptype']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['p'];?>
" /></td></tr>
   <?php }?>
   <?php
}
}
?>
 </tbody>
</table>


	  </td>

        </tr>
        <tr>
 

	 <td height="30" colspan="2" align="center">
		<div class=" data_footer control">
			<input type="button" value="保存" class="button send bnt_1">
			<input type="button" value="重置" class="button cancel bnt_2">
		</div>
		  </td>
        </tr>
		</table>
		</div>
	</div>
		</div>
</div>
<?php echo '<script'; ?>
 language="javascript">
var gid=<?php echo $_smarty_tpl->tpl_vars['gid']->value;?>
;
<?php echo '</script'; ?>
>
<div id='test'></div>
</body>
</html>
<?php }
}
