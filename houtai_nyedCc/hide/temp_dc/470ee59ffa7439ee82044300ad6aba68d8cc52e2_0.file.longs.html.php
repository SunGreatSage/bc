<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:25:10
  from '/www/wwwroot/lhc/houtai/templates/default/hide/longs.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f376621805_46833940',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '470ee59ffa7439ee82044300ad6aba68d8cc52e2' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/longs.html',
      1 => 1681170356,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:headers.html' => 1,
  ),
),false)) {
function content_6639f376621805_46833940 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:headers.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 type="text/javascript" src="../js/jquery-ui.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="../js/jquery.ui.datepicker-zh-CN.js"><?php echo '</script'; ?>
>
<link href="/default/css/g_HK6.css" rel="stylesheet" />

<style>
    #pageDiv {
        font-size: 13px;
    }

    .list tbody .hover td, .list tbody .hover:nth-child(even) {
        background: rgb(255,255,198);
    }
</style>
</head>
<body class="L_HK6">
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='longs';<?php echo '</script'; ?>
>
<div class="jt-container">
<div class="kj_s" style="display: none;">
<ul>
<li>
<label>彩种：</label><select id="game" name="game">
<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
<option value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'] == $_smarty_tpl->tpl_vars['gid']->value)) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</option>
<?php
}
}
?></select></li></ul></div>
<div id="drawInfo" class="contents s_main kj_box kj_hk6">
<table class="list data_table table_ball">
<thead>
           <tr><th rowspan="2" nowrap="nowrap">期数</th>
			<th rowspan="2" nowrap="nowrap">日期</th>
			<th colspan="14" nowrap="nowrap">彩球号码</th>
			<th rowspan="2" nowrap="nowrap">总分</th>
			<th>总</th>
			<th>总</th>
			<th>特</th>
			<th>特</th>
			<th>特</th>
			<th>特</th>
			<th nowrap="nowrap">特合</th>
			<th nowrap="nowrap">特合</th>
			<th nowrap>特合尾</th>
			<th rowspan="2">波段</th>
			</tr>
	  <tr><th colspan="2">一</th>
			<th colspan="2">二</th>
			<th colspan="2">三</th>
			<th colspan="2">四</th>
			<th colspan="2">五</th>
			<th colspan="2">六</th>
			<th colspan="2">特</th>
			<th nowrap="nowrap">单双</th>
			<th nowrap="nowrap">大小</th>
			<th nowrap>生肖</th>
			<th nowrap="nowrap">五行</th>
			<th nowrap="nowrap">单双</th>
			<th nowrap="nowrap">大小</th>
			<th nowrap="nowrap">单双</th>
			<th nowrap="nowrap">大小</th>
			<th nowrap="nowrap">大小</th></tr>   </thead>
<tbody>
<?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['list']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
<tr>
<td class="period"><?php echo $_smarty_tpl->tpl_vars['list']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['qishu'];?>
</td>
<td class="drawTime"><?php echo $_smarty_tpl->tpl_vars['list']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['time'];?>
</td>
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ma'], 'k');
$_smarty_tpl->tpl_vars['k']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value) {
$_smarty_tpl->tpl_vars['k']->do_else = false;
?>
<td class="name"><?php if ($_smarty_tpl->tpl_vars['k']->value != '') {?><span class="b<?php echo substr($_smarty_tpl->tpl_vars['k']->value,0,2);?>
"><?php echo substr($_smarty_tpl->tpl_vars['k']->value,0,2);?>
</span></td>
<td class="name"><label class="sxs"><?php echo substr($_smarty_tpl->tpl_vars['k']->value,3);?>
</label><?php }?></td>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['m'], 'n');
$_smarty_tpl->tpl_vars['n']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['n']->value) {
$_smarty_tpl->tpl_vars['n']->do_else = false;
?>
   <td class="name <?php if (($_smarty_tpl->tpl_vars['n']->value == '绿')) {?>green01<?php } elseif (($_smarty_tpl->tpl_vars['n']->value == '红')) {?>red01<?php } elseif (($_smarty_tpl->tpl_vars['n']->value == '蓝')) {?>blue01<?php } elseif (($_smarty_tpl->tpl_vars['n']->value == "大" || $_smarty_tpl->tpl_vars['n']->value == "合单" || $_smarty_tpl->tpl_vars['n']->value == "尾大" || $_smarty_tpl->tpl_vars['n']->value == "单" || $_smarty_tpl->tpl_vars['n']->value == "合大")) {?>dx_d<?php }?>"><?php echo $_smarty_tpl->tpl_vars['n']->value;?>
</td>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</tr>
<?php
}
}
?>
</tbody></table>
</div>
</div>
</body>
</html><?php }
}
