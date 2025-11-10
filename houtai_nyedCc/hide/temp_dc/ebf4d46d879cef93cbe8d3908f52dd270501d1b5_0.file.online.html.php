<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:33:25
  from '/www/wwwroot/lhc/houtai/templates/default/hide/online.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f56508e0e9_90680201',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ebf4d46d879cef93cbe8d3908f52dd270501d1b5' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/online.html',
      1 => 1681169936,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_6639f56508e0e9_90680201 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
</head>
<style>
	.xxtb{display:none;position: absolute;width:1000px;background: #fff}
	.click{background-color: rgb(255, 255, 162) !important;}
	.page{border: 1px solid #ddd;
    color: #2161b3 !important;
    background: #f9f9f9;
	 padding: 4px 8px;}
		.current{border: 1px solid #2161b3;
    color: #fff !important;
    background-color: #2161b3;
    padding: 4px 8px;}
</style>
<body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='online';<?php echo '</script'; ?>
>
<div class="jt-container">
<div class="s_main">  
<TABLE class="list table_ball">
 <thead>
 <tr><th>所有</th><th>总公司</th><th>总代</th><th>一级</th><th>二级</th><th>三级</th><th>四级</th><th>五级</th><th>六级</th><th>七级</th><th>八级	</th><th>会员</th></tr></thead>
<tr><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['num']->value, 'i', false, 'k');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?><td class="type<?php if ($_smarty_tpl->tpl_vars['type']->value == $_smarty_tpl->tpl_vars['k']->value) {?> click<?php }?>" type='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' style="cursor: pointer;" ><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
</td><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></tr>
</TABLE><BR />

   <table class="list table_ball on_tb ">
   <thead>
   <TR><th style="display: none;"><input type="checkbox" id='clickall' />全选</th><th style="display: none;">ID</th><th>级别</th><th>登陆名</th><th>名称</th><th>客户端</th>
   <?php if ($_smarty_tpl->tpl_vars['type']->value == 11) {?>
     <th>信用额度/余额</th>
     <th>未结算</th>
   <?php }?>
   <th>IP</th><th>地址</th><th>登陆时间</th><th>最后活动时间</th><th>操作<input   type="button" value="踢出选中" id='delall' style="width: 70px;display: none;"  /></th></TR></thead>
    <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['on']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
    <TR class="com" id="<?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['com'];?>
">
       <TD style="display: none;"><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uid'];?>
" /><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['i'];?>
</TD>
	   <TD style="display: none;"><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['i'];?>
</TD>
	   <?php if ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['xtype'] == 0) {?>
       <td>总公司</td>
	   <?php } elseif ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['xtype'] == 1) {?>
	    <?php if ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 1) {?>
		<td>一级</td>
		<?php } elseif ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 2) {?>
		<td>二级</td>
		<?php } elseif ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 3) {?>
		<td>三级</td>
		<?php } elseif ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 4) {?>
		<td>四级</td>
		<?php } elseif ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 5) {?>
		<td>五级</td>
		<?php } elseif ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 6) {?>
		<td>六级</td>
		<?php } elseif ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 7) {?>
		<td>七级</td>
		<?php } elseif ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 8) {?>
		<td>八级</td>
		<?php }?>
	    <?php } elseif ($_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['xtype'] == 2) {?>
		<td>会员</td>
       <?php }?>
        <Td class='tl'><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['username'];?>
</Td>
		<Td class='tl'><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</Td>
		<td class='os'><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['os'];?>
</td>
         <?php if ($_smarty_tpl->tpl_vars['type']->value == 11) {?>
     <td class="tl" style="color: red;" ><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['maxmoney'];?>
/<?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['money'];?>
</td>
     <td class='tl' uid='<?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uid'];?>
'><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['z9'];?>
</td>
   <?php }?>  
        
		<Td><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ip'];?>
</Td>
		<Td class="ip" ip=''><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['addr'];?>
</Td>
        <Td ><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['logintime'];?>
</Td>
        	<Td><?php echo $_smarty_tpl->tpl_vars['on']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['savetime'];?>
</Td>
        <Td><input type="button" value="踢出" /></Td>
     </TR>
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
<table class="data_table data_list list xxtb">
 <tr class="bt">
 <th>彩种</th>
  <th>期數</th>  
  <th>類別</th>
  <th>金額</th>
  <th>赔率</th>
  <th>退水</th>
  <th>會員</th>
  <th>時間</th>
 </tr>
</table>
<input type="hidden" class='sort' orderby='time' sorttype='DESC' page='1' xtype='2' js='0' gid='99' con='' />
<div id='test'></div>
</body>
</html>
<?php }
}
