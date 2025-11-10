<?php
/* Smarty version 3.1.48, created on 2024-05-08 17:03:24
  from '/www/wwwroot/lhc/houtai/templates/default/hide/suserpointsnew.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663b3fdc6025a1_22877568',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f8408557c3eb68d8b35ff286940b47e277525aa0' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/suserpointsnew.html',
      1 => 1679387322,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_663b3fdc6025a1_22877568 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="top_info">
    <span class="title"><?php echo $_smarty_tpl->tpl_vars['layername']->value;?>
 <span><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
（<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
）</span> -&gt; 退水</span>
    <span class="right"><a class="back close" types="<?php if ($_smarty_tpl->tpl_vars['ifagent']->value == 1) {?>ag<?php } else { ?>u<?php }?>">返回</a></span>
</div>
<div class="warning_panel" style="display: none;">
    快开彩在【<?php echo $_smarty_tpl->tpl_vars['editstart']->value;?>
--<?php echo $_smarty_tpl->tpl_vars['editend']->value;?>
】修改以下参数才能生效，低频彩开盘期间修改以下参数不会生效！ 当天未投注用户修改参数可以立即生效！
</div>




<ul class="tab">
    <li class="tab_title02">
        <a href="javascript:void(0);" class="infoset">基本资料</a>
        <a href="javascript:void(0);" class="on">退水设定</a>
    </li>
</ul>



<div class="s_main" id="saveForm">
<div id="limitSettingList">
<table class="data_table info_table user_panel pointstb table_ball">
    <thead style="display: none;"><th colspan="2" uid='<?php echo $_smarty_tpl->tpl_vars['uid']->value;?>
' fid='<?php echo $_smarty_tpl->tpl_vars['fid']->value;?>
' class="uid">【<label><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
</label>】退水设定</th></thead>
    <tbody style='display:none;'>
    <td colspan="2">
    <!--统一设置赚取退水:    <select name="liushui" id="liushui">
    
         <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['liushui']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
          <option value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
</option>
         <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?><option value="all">全部</option>
               </select>%&nbsp;&nbsp;-->
    <input type="button" class="btn1 btnf setpointssend" value="提交修改" />
     <input type="button" class="btn1 btnf close" value="关闭窗口" /></td>
   </tr>
   <TR >
    <Td colspan="2" style="text-align:left;padding-left:10px;font-size:13px;color:#0063e3">注意事项:<BR />一、如果修改代理级的退水，<Br />
      1、改动后的退水大于改动前退水，该用户所有下线的退水维持不变。<BR />
      2、改动后的退水[P]小于改动前的退水，所有下线的退水和[P]作比较，大于[P]，下线的退水为[P]，小于[P]，下线退水不变<BR />
      二、如果修改占成，该用户的所有下级占成将归0。<BR />
      三、如果修改代理级的单注最大限额，<Br />
      1、改动后的值大于改动前值，该用户所有下线的（单注最大限额）维持不变。<BR />
      2、改动后的值[M]小于改动前的值，所有下线的（单注最大限额）和[M]作比较，大于[M]，下线的(单注最大限额)为[M]，小于[M]，下线(单注最大限额)不变<br />
      四、如果修改用户的（单注最低限额），该用户的所有下线的（单注最低限额）和该用户相同。 </Td>
   </TR>


   </tbody>
  </table>
  

<div class="game_tab_class">
<li  class="hover">六合彩</li>
<div id="cmcontrol">&nbsp;&nbsp;&nbsp;&nbsp;赚取退水：<select id="selectsetting">
            <option value="0">所有盘口</option>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
盘</option>
				  <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </select>&nbsp;&nbsp;<input> <input type="button" class="slowbtn btn2" value="确定">&nbsp;&nbsp;&nbsp;&nbsp;赚赔设置&nbsp;&nbsp;<select id="selDeductValue"><option value="">--</option><option value="1">1</option><option value="0.5">0.5</option><option value="0.1">0.1</option><option value="0.05">0.05</option><option value="0.01">0.01</option><option value="0">0</option><option value="-0.01">-0.01</option><option value="-0.05">-0.05</option><option value="-0.1">-0.1</option><option value="-0.5">-0.5</option><option value="-1">-1</option></select>&nbsp;&nbsp;<input type="button" class="wtzpbtn btn3" value="微调">&nbsp;&nbsp;统一值：<input style="width:60px">&nbsp;&nbsp;<input type="button" class="zpbtn btn3" value="确定"> &nbsp;&nbsp;<input type="button" value="保存" class="button btn1 setpointssend">&nbsp;<input type="button" value="取消"  class="button btn2 close"></div>
</div>  
   <div class="contents param_panel input_panel tab_panel data_panel">
   
<table class="data_table quick" <?php if ($_smarty_tpl->tpl_vars['config']->value['fasttype'] == 0) {?>style='display:none;'<?php }?>>
<thead><tr>
<th align="center" nowrap="nowrap" style="width: 250px;">项目名称</th>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
    <th><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
盘(%)</th>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
<th>注单限额</th>
<th>单期限额</th>


<th align="center" nowrap="nowrap">修改</th></tr></thead>
<tbody>
<tr class="t_BALL"><th class="color" style="text-align: right">号码类(正码、特码|正码特请手动）</th>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
    <td><input name="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" class="commission"></td>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	<td><input class="amount"></td>
	<td><input class="amount"></td>

	


	<td><input type="button" value="修改"></td>
</tr>
<tr class="t_LM"><th class="color" style="text-align: right">两面类（特码两面、总肖单双等）</th>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
    <td><input name="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" class="commission"></td>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	<td><input class="amount"></td>
	<td><input class="amount"></td>


	<td><input type="button" value="修改"></td>
</tr>
<tr class="t_ITEM"><th class="color" style="text-align: right">多项类（尾数、平特一肖尾数、正码1-6色波）</th>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
    <td><input name="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" class="commission"></td>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	<td><input class="amount"></td>
	<td><input class="amount"></td>


	<td><input type="button" value="修改"></td>
</tr>
<tr class="t_MP"><th class="color" style="text-align: right">连码类（三中二、三全中、二全中等）</th>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
    <td><input name="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" class="commission"></td>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	<td><input class="amount"></td>
	<td><input class="amount"></td>


	<td><input type="button" value="修改"></td>
</tr>
<tr class="t_"><th class="color" style="text-align: right">其它（半波特头尾、合肖总肖、连肖连尾等）</th>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
    <td><input name="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" class="commission"></td>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	<td><input class="amount"></td>
	<td><input class="amount"></td>


	<td><input type="button" value="修改"></td>
</tr>
<tr class="tf_"  style="display: none;"><th class="color">番摊</th>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
    <td><input name="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" class="commission"></td>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
  <td><input class="amount"></td>
  <td><input class="amount"></td>


  <td><input type="button" value="修改"></td>
</tr>
</tbody>
</table>

</div>
<div class="data_footer control">
            <input type="button" value="保存"  class="button btn1 setpointssend">
            <input type="button" value="取消"  class="button btn2 close">
        </div>
		
</div>
</div>
<style type="text/css">
.param_panel .data_table td {
	text-align:center;
}
.param_panel .data_table input {
	text-align:center;
}
.param_panel .layout th.color{height:45px;}
</style><?php }
}
