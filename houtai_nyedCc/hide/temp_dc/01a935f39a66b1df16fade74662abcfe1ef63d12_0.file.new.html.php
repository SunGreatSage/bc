<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:25:03
  from '/www/wwwroot/lhc/houtai/templates/default/hide/new.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f36f43b552_48479382',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '01a935f39a66b1df16fade74662abcfe1ef63d12' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/new.html',
      1 => 1679194906,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_6639f36f43b552_48479382 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
</head><body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';
var js=1;var sss='new';
<?php echo '</script'; ?>
>
<div class='main'>
<div class="top_info">
<span class="title">最新公告</span>
</div>
<div class="contents s_main">
 <!--table class="data_table data_list list"> 
<thead><tr><th>公司</th><th>对象</th><th>时间</th><th>公告详情</th></tr></thead>
<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['news']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
<TR><td><?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['web'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['agent'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['time'];?>
</td><TD style="text-align:left;padding-left:10px;"><?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['content'];?>
</TD></TR>
<?php
}
}
?>
</table-->
<table class="data_list list table_ball table_layout">
<thead><tr><th style="width: 50px;">可见</th><th style="width:140px;">时间</th><th>公告详情</th></tr></thead>
<tbody>
 <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['news']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
<tr><td><?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['agent'];?>
</td><td class="online" nowrap="nowrap"><?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['time'];?>
</td><td class="type word_wrap"><?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['content'];?>
</td></tr>
<?php
}
}
?>
</tbody>
</table>
	</div>
</div>
<div id='test'></div>
</body>
</html><?php }
}
