<?php
/* Smarty version 3.1.48, created on 2024-05-07 19:57:17
  from '/www/wwwroot/lhc/houtai/templates/default/hide/caopan.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a171de24e31_79524884',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd3101a70243d3f9c3e95cfefa05fdf9ebb6666d9' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/caopan.html',
      1 => 1682091050,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663a171de24e31_79524884 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 language="javascript" src="/js/md5.js"><?php echo '</script'; ?>
>
</head>
<body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='caopan';<?php echo '</script'; ?>
>
<div class="main">
	<div class="top_info">
		<span class="title">管理员管理</span><div class="right">
			<a class="back" onclick="javascript:history.back(-1);" href="javascript:void(0);">返回</a>
			</div>
			 <?php if ($_smarty_tpl->tpl_vars['data']->value[0]['adminid'] == 10000) {?>
			<div class="center" style="float: right;margin-right: 100px;">
<a class="ico_add add" id="add<?php echo $_smarty_tpl->tpl_vars['data']->value[0]['userid'];?>
" href="javascript:void(0);" >新增管理员</a>
</div>
 <?php }?>

	</div>
	<div class="contents s_main">
   <table class="list  user_list table_ball">
   <thead>
   <TR>
   
<th>管理员</th><th>新增日期</th><th style="display: none;">登陆次数</th><th>最后登录IP</th><th>最后登录时间</th><th style="display: none;">最后修改密码时间</th><th style="width: 25%;">功能</th>

   </TR>   </thead>  
   <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['data']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
   <tr><td ><?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminname'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['regtime'];?>
</td><td style="display: none;"><?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['logintimes'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['lastloginip'];?>
[<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['lastloginfrom'];?>
]</td><td><?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['lastlogintime'];?>
</td><td style="display: none;"><?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['passtime'];?>
</td>
   <td style="width:180px;">
   <?php if ($_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminid'] != 10000) {?>
   <a  class='del'  id=del"<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminid'];?>
" aid="<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminid'];?>
" />删除</a>/
   <?php }?>
    <a class='edit' id=edit"<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminid'];?>
" aid="<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminid'];?>
" />修改密码</a>/
   <a  class='record'  aid='<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminid'];?>
' username='<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminname'];?>
'  id=showrecord"<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminid'];?>
"  />记录</a>/
     <a class='logininfo'  aid='<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminid'];?>
' username='<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminname'];?>
'   id="showlogininfo<?php echo $_smarty_tpl->tpl_vars['data']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['adminid'];?>
" />日志</a>
   </td>
   </tr>
   <?php
}
}
?>
   </table>
    <table class="data_list list pages table_ball" style="margin-top:20px;">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['page']->value, 'i', false, 'k');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
     <?php if ($_smarty_tpl->tpl_vars['k']->value == 0) {?><thead><?php }?>
      <tr>
          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['i']->value, 'j', false, 'h');
$_smarty_tpl->tpl_vars['j']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['h']->value => $_smarty_tpl->tpl_vars['j']->value) {
$_smarty_tpl->tpl_vars['j']->do_else = false;
?>
            <?php if (($_smarty_tpl->tpl_vars['k']->value == 0 || $_smarty_tpl->tpl_vars['h']->value == 0)) {?> <Th><?php echo $_smarty_tpl->tpl_vars['j']->value;?>
</Th><?php } else { ?>
            <TD><?php echo $_smarty_tpl->tpl_vars['j']->value;?>
</TD>
            <?php }?>
          <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
      </tr>
      <?php if ($_smarty_tpl->tpl_vars['k']->value == 0) {?></thead><?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
   </table>
   </div>

  <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['aid']->value;?>
" name='aid' id='aid' />
</div>


<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-draggable ui-resizable addtb" tabindex="-1" role="dialog" aria-describedby="shares" aria-labelledby="ui-id-1" style="position: absolute; height: 152.28px; width: 360px; top: 257px; left: 564.5px; display:none; right: auto; bottom: auto;"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle"><span id="ui-id-1" class="ui-dialog-title">2988316036#明细</span><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" title="Close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">Close</span></button></div><div id="shares" class="popdiv ui-dialog-content ui-widget-content" style="display: block; width: auto; min-height: 96px; max-height: 346px; height: auto;"><table class="data_table">
<tbody>
<TR><Th>管理员</Th><TD><label id='user0'><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
</label><input type="text" id='user1' maxlength="10" class="txt1" style="width:80px;" /><input type="hidden" id='action' value="add" /></TD></TR>
      <TR><Th>密码</Th><TD><input type="password" id='pass1x' class="intext" /></TD></TR>
      <TR><Th>重复密码</Th><TD><input type="password" id='pass2x' class="intext" /></TD></TR>
      <TR><th colspan="2" class="tcenter"><input type="button" id='addbtn' class="btn1 btnf" value="新增子帐号" />&nbsp;<input type="button" id='closebtn' class="btn1 btnf" value="关闭" /></th></TR>
</tbody>
</table></div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div></div>

<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-draggable ui-resizable recordtb" tabindex="-1" role="dialog" aria-describedby="shares" aria-labelledby="ui-id-1" style="position: absolute; height:400px; width: 360px; top: 257px; left: 564.5px; display:none; right: auto; bottom: auto;"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle"><span id="ui-id-1" class="ui-dialog-title">2988316036# 占成明细</span><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" title="Close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">Close</span></button></div><div id="shares" class="popdiv ui-dialog-content ui-widget-content" style="display: block; width: auto; min-height: 96px; max-height: 346px; height: auto;">
<table class="data_table list ltb  table_ball">
</table></div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div></div>
<div id='test'></div>
</body>
</html>
<?php }
}
