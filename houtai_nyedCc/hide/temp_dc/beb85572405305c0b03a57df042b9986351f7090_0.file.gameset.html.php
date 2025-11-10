<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:29:56
  from '/www/wwwroot/lhc/houtai/templates/default/hide/gameset.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f49418c9d8_77279915',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'beb85572405305c0b03a57df042b9986351f7090' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/gameset.html',
      1 => 1682706912,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_6639f49418c9d8_77279915 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 src="/js/sitetool.js?v=004"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='gameset';<?php echo '</script'; ?>
>
</head>
<body>
<div class="jt-container">
	<div class="top_info">
		<span class="title">彩种管理设置<span class="right"></span>
	</div>
	<div class="s_main">
 <table class="s_tb table_ball list">
 
<thead>
 <TR>
 <th >彩种</th><th  style="display: none;" >gid</th><th>排序</th><th>总开关(包括后台)</th><th>基本开关(仅下线)</th><th>年份(农历前一位)</th><th>开奖直播链接</th><th>玩法名称</th>
 </TR></thead>	 
 <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
   <TR>
       <td><label><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</label></td>
       <td class='gid' style="display: none;"><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
</td>
	   <td><INPUT type='text'  style="width: 60px;"   class="px"   value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['xsort'];?>
"  /></td>
       <td class='v'><INPUT type='checkbox'  class="ifopen"   value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" <?php if ($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifopen'] == 1) {?>checked<?php }?> /></td>
       <td class='v'><INPUT type='checkbox'  class="ifok"   value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" <?php if ($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'] == 1) {?>checked<?php }?> /></td>
	   <td class="thisbml"><INPUT type='text'   style="width: 60px;" value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['thisbml'];?>
"  />&nbsp;<input type="button" class="tlset button btn3" value="设置"></td>
	   <td class="kjurl"><INPUT type='text'     value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['kjurl'];?>
"  />&nbsp;<input type="button" class="kjset button btn3" value="设置"></td>
	   <td class="flname"><INPUT type='text'     style="width: 60px;"  value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flname'];?>
"  />&nbsp;<input type="button" class="wfset button btn3" value="设置"></td>
   </TR>
 <?php
}
}
?>
  </table>
  
	<div class=" data_footer control">
			<input type="button" value="保存" class="send button bnt_1">
		</div>
</div>
</div>

<div id='test'></div>

</body>
</html><?php }
}
