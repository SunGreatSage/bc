<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:29:55
  from '/www/wwwroot/lhc/houtai/templates/default/hide/webconfig.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f49375e853_07713769',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd3665f4ccf7c50f30ab16c73e344cac80f68fb3d' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/webconfig.html',
      1 => 1681187932,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_6639f49375e853_07713769 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link href="/default/css/jquery.loadmask.css" rel="stylesheet" />
<?php echo '<script'; ?>
 src="/js/sitetool.js?v=004"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="/js/jquery.loadmask.js?v=004"><?php echo '</script'; ?>
>
</head><body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='webconfig';<?php echo '</script'; ?>
>
<div class="jt-container">
 <input type="hidden" name="xtype" value="setsys" />
   <div class="s_main">
 <table style="border-collapse:unset;"  class="data_table info_table user_panel table_ball table_ball infotb" >
 <thead><tr class=""><th colspan="2">自动维护</th></tr></thead>
     <tr><th>自动删除登录日志(天)</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['autodellogintime'];?>
' class="autodellogintime txt1" /><input type="checkbox" <?php if ($_smarty_tpl->tpl_vars['config']->value['autodellogin'] == 1) {?>checked<?php }?> class="autodellogin" /></td></tr>
       <tr><th>自动删除修改日志(天)</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['autodeledittime'];?>
' class="autodeledittime txt1" /><input type="checkbox" <?php if ($_smarty_tpl->tpl_vars['config']->value['autodeledit'] == 1) {?>checked<?php }?> class="autodeledit" />&nbsp;包括修改记录、资金记录</td></tr>
     <tr><th>自动删除赔率日志(天)</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['autodelpltime'];?>
' class="autodelpltime txt1" /><input type="checkbox" <?php if ($_smarty_tpl->tpl_vars['config']->value['autodelpl'] == 1) {?>checked<?php }?> class="autodelpl" /></td></tr>
    <tr><th>每页记录数1</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['psize'];?>
' class="psize txt1"  />&nbsp;代理报表页数,会员下注单页数</td></tr>
    <tr><th>每页记录数2</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['psize1'];?>
' class="psize1 txt1"  />&nbsp;用户列表页数,在线统计页数</td></tr>
    <tr><th>每页记录数3</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['psize2'];?>
' class="psize2 txt1"  />&nbsp;开奖记录页数</td></tr>
  <tr><th>每页记录数4</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['psize3'];?>
' class="psize3 txt1"  />&nbsp;补货变更记录页数,资金记录页数,公司注单查询页数</td></tr>
    <tr><th>每页记录数5</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['psize5'];?>
' class="psize5 txt1"  /></td></tr>
 </table>
 <div class="data_footer control">
          <input type="button" value="保存" class="button editall bnt_1" />
		</div>
 </div>
<div id='test'></div>
</body>
</html><?php }
}
