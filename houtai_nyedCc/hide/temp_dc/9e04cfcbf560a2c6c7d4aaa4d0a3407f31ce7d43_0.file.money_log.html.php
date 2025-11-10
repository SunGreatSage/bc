<?php
/* Smarty version 3.1.48, created on 2024-05-08 17:01:57
  from '/www/wwwroot/lhc/houtai/templates/default/hide/money_log.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663b3f85083323_12145381',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9e04cfcbf560a2c6c7d4aaa4d0a3407f31ce7d43' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/money_log.html',
      1 => 1681974296,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:headers.html' => 1,
  ),
),false)) {
function content_663b3f85083323_12145381 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:headers.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
</head>
<body id="topbody">
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';
var js=1;var sss='money_log';
<?php echo '</script'; ?>
>
<div class="jt-container">
<div class="top_info">
<span class="title"><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
 额度变更记录</span>
<span class="right">
				<a class="back" onclick="javascript:history.back(-1);" href="javascript:void(0);">返回</a>
			</span>
</div>
<div class="contents  s_main">
<table class="data_table list table_ball">
<thead><tr><th>时间</th><th>额度类型</th><th>操作金额</th><th>余额</th><th>操作</th><th>IP</th><th>IP归属地</th><th>操作员</th></tr></thead>
<tbody>
<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['log']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
<tr>
    <td class="time"><?php echo $_smarty_tpl->tpl_vars['log']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['time'];?>
</td>
    <td><?php if ($_smarty_tpl->tpl_vars['log']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['type'] == 0) {?>信用额度<?php } elseif ($_smarty_tpl->tpl_vars['fudong']->value == 1) {?>现金额度<?php } else { ?>信用额度<?php }?></td>
    <td><?php echo $_smarty_tpl->tpl_vars['log']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['money'];?>
</td>
    <td><?php echo $_smarty_tpl->tpl_vars['log']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['usermoney'];?>
</td>
    <td><?php echo $_smarty_tpl->tpl_vars['log']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['bz'];?>
</td>
    <td><?php echo $_smarty_tpl->tpl_vars['log']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ip'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['log']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['addr'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['log']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['modiuser'];?>
(<?php echo $_smarty_tpl->tpl_vars['log']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['modisonuser'];?>
)</td></tr>
<?php
}
}
?>
</tbody>
</table>
</div>

</div>
</body>
</html><?php }
}
