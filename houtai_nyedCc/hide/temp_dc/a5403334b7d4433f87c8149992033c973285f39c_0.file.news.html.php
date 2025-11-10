<?php
/* Smarty version 3.1.48, created on 2024-05-07 18:42:56
  from '/www/wwwroot/lhc/houtai/templates/default/hide/news.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a05b034e837_88876542',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a5403334b7d4433f87c8149992033c973285f39c' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/news.html',
      1 => 1681171494,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663a05b034e837_88876542 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
</head>
<body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='news';<?php echo '</script'; ?>
>
<div class="jt-container">
	<div class="top_info">
		<span class="title">系统公告发布</span>
		<span class="right">&nbsp;&nbsp;<a class="back" onclick="javascript:history.back(-1);" href="javascript:void(0);">返回</a></span>
	</div>
	<div class="user_list s_main">
	<form method="POST" style="margin:0">
		<input type="hidden" value="addnews" name="xtype">
			<table style="border-collapse:unset;" class="data_table list info_table">
			<thead>
	 <tr><th colspan="2">公告发布</th></tr>
	   </thead>
				<tbody>
            <tr>
            <td class="txt-right txt-paddin-right w30" style="background: #f6f6f6;" >公告内容</td>
            <td class="txt-left txt-paddin-left">
                <div>
                  <textarea cols="80" rows="5" id=content name="content"></textarea> 
                </div>
                <div style="margin-top: 10px;" ><select name=wid style="display: none;">
        <OPTION value="0">所有公司</OPTION>
        <?php
$__section_k_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['web']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_k_0_total = $__section_k_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_k'] = new Smarty_Variable(array());
if ($__section_k_0_total !== 0) {
for ($__section_k_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_k']->value['index'] = 0; $__section_k_0_iteration <= $__section_k_0_total; $__section_k_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_k']->value['index']++){
?>
        <OPTION value="<?php echo $_smarty_tpl->tpl_vars['web']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_k']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_k']->value['index'] : null)]['wid'];?>
"><?php echo $_smarty_tpl->tpl_vars['web']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_k']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_k']->value['index'] : null)]['webname'];?>
</OPTION>
        <?php
}
}
?>
        </select> <input type="submit"  value="发布公告" class="bnt_1 button"></div></td>
            </tr>
            </tbody>
			</table>
			
		<table style="border-collapse:unset;margin-top: 10px;" class="data_table list table_center news_tb">
			
		<thead>
		<tr>
			<th style="width: 70px;">
				<input type="checkbox" id="clickall">全选
			</th>
			<th style="width: 70px;">
				序  号
			</th>
			<th style="width: 250px;">
				新增日期
			</th>
            <th style="display: none;">
				公司
			</th>
           <th style="width: 70px;display: none;">
				参数
			</th>
			<th style="width: 70px;">
				启用
			</th>
			<th style="width: 100px;">
				可见
			</th>
			<th style="width: 70px;">
				滚动
			</th>
			<th style="width: 70px;">
				弹出
			</th>
			<th class="conwidth">
				内容
			</th>
			
			<th style="width: 150px;">
				<input type="button" class="btn7 btnf dels" id="delall"  style="width: 70px;" value="删除选中">
			</th>
		</tr>
        </thead>
		<tbody>
         <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['news']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
		<tr>
			<td>
				<input class='chk' type="checkbox" value='<?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['id'];?>
' />
			</td>
			<td>
				<?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['id'];?>

			</td>
			<td>
				<input type="text" class="time" value='<?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['time'];?>
' />
			</td>
            <td style="display: none;">
               <select class=wid>
                <OPTION <?php if ($_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['wid'] == 0) {?>selected<?php }?> value="0">所有公司</OPTION>
        <?php
$__section_k_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['web']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_k_2_total = $__section_k_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_k'] = new Smarty_Variable(array());
if ($__section_k_2_total !== 0) {
for ($__section_k_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_k']->value['index'] = 0; $__section_k_2_iteration <= $__section_k_2_total; $__section_k_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_k']->value['index']++){
?>
        <OPTION <?php if ($_smarty_tpl->tpl_vars['web']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_k']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_k']->value['index'] : null)]['wid'] == $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['wid']) {?>selected<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['web']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_k']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_k']->value['index'] : null)]['wid'];?>
"><?php echo $_smarty_tpl->tpl_vars['web']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_k']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_k']->value['index'] : null)]['webname'];?>
</OPTION>
        <?php
}
}
?>
        </select>
            </td>
           <td style="display: none;">
				<input type="checkbox" class="cs" value='1' <?php if ($_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['cs'] == 1) {?>checked<?php }?> />
			</td> 
			<td>
				<input type="checkbox" class="ifok" value='1' <?php if ($_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'] == 1) {?>checked<?php }?> />
			</td>
			<td>
			  <SELECT class="agent">
                <OPTION value="2" <?php if ($_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['agent'] == 2) {?>selected<?php }?>>所有</OPTION>
                <OPTION value="1" <?php if ($_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['agent'] == 1) {?>selected<?php }?>>代理</OPTION>
                <OPTION value="0" <?php if ($_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['agent'] == 0) {?>selected<?php }?>>会员</OPTION>
              </SELECT>
			
            </td>
			<td>
				<input type="checkbox" class="gundong" value='1' <?php if ($_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gundong'] == 1) {?>checked<?php }?> />
			</td>
			<td>
				<input type="checkbox" class="alert" value='1' <?php if ($_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['alert'] == 1) {?>checked<?php }?> />
			</td>
			<td class="conwidth">
				<textarea   cols="50" class="con" rows="3"><?php echo $_smarty_tpl->tpl_vars['news']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['content'];?>
</textarea>
			</td>
			
			<td>
			<input type="button" class="btn1 btnf edit" value="修改">
			</td>
		</tr>
<?php
}
}
?>
</tbody>
		</table>
	</form>
</div>
</div>
<div id="test">
</body>
</html>
<?php }
}
