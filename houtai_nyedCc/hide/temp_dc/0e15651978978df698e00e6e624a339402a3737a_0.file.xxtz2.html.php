<?php
/* Smarty version 3.1.48, created on 2024-03-31 07:01:54
  from '/www/wwwroot/lhc_2/templates/default/hide/xxtz2.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_660899e2ad3a73_59441635',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0e15651978978df698e00e6e624a339402a3737a' => 
    array (
      0 => '/www/wwwroot/lhc_2/templates/default/hide/xxtz2.html',
      1 => 1683763120,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_660899e2ad3a73_59441635 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php if ($_smarty_tpl->tpl_vars['rkey']->value == 0) {?>oncontextmenu="return false"<?php }?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="/js/jquery/new/jqueryuib.min.css" />
<link rel="stylesheet" href="/default/css/layoutbl.min.css" />
<link rel="stylesheet" href="/default/css/Skins/blue/skinbd.min.css" />
<?php echo '<script'; ?>
 language="javascript" src="/js/jquery-1.8.3.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript">
function changeh(){
   /* var obj = parent.document.getElementById("frame"); //取得父页面IFrame对象  
	var h= document.body.clientHeight+500;
    obj.style.height = h+"px"; //调整父页面中IFrame的高度为此页面的高度*/
}
<?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"><?php echo '</script'; ?>
>
    <style>
a {
    color: #3e34fc;
}
.contents {
    overflow-x: auto;
}
.wh2500 {
    width: 2500px;
}
.txts {
    width: 40px;
    text-align: center;
}
.con {
    width: 80px;

    </style>
</head><body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='xxtz2<?php if (($_smarty_tpl->tpl_vars['changeorder']->value == 1 && $_smarty_tpl->tpl_vars['xxtz2']->value == 1)) {?>kk<?php }?>';<?php echo '</script'; ?>
>
<div class="jt-container">
	<div class="top_info">
		<span class="title">注单明细查询</span>
		<span class="right">显示各级代理明细：<input type="button"   style="width: 60px;" class="button btn7  btnf" id="zkgjmx" value="展开▼"><?php if (($_smarty_tpl->tpl_vars['changeorder']->value == 1 && $_smarty_tpl->tpl_vars['xxtz2']->value == 1)) {?>&nbsp;<input type="button" value="批量修改" class="button btn2 editall2"><?php }?>&nbsp;<input type="button" value="批量注销" class="button btn2 editall">&nbsp;<input type="button" value="批量删除" class="button btn2 delall"></span>
	</div>
	<div class="s_main">
 <table class="data_table s_head info_table ">
 <thead><tr><th colspan="2">注单明细</th></tr></thead>
  <tr style="display: none;">
   <th style="width:150px;" >查询方式</th>
   <td class="r"  colspan="2"><input type="radio" value="0" name="fs" checked />
    按日期
    <input type="radio" value="1" name="fs"/>
    按期数&nbsp;&nbsp;</td>
  </tr>
  <TR  style="display: none;">
   <th>日期范围</th>
   <Td style="text-align:left;padding-left:10px;" colspan="2"><select name="select" class=qishu>
    
     
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['qishu']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
     
    <option value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
期</option>
    
     <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
      
    
   </select></Td>
  </TR>
  <tr>
   <th  width="20%" class="ft_sd te-rt">日期选择</th>
   <td class="r"  colspan="2"><input class='textb' id="start"  value='<?php echo $_smarty_tpl->tpl_vars['sdate']->value[10];?>
' size='11' />
    &nbsp;—&nbsp;
    <input class='textb' id="end" name='end'  value='<?php echo $_smarty_tpl->tpl_vars['sdate']->value[10];?>
' size='11' />
    <input type="button" class="s btnf"  d=1 value="今天" />
    <input type="button" class="s btnf"  d=2 value="昨天" />
    <input type="button" class="s btnf"  d=3 value="本星期" />
    <input type="button" class="s btnf"  d=4 value="上星期" />
    <input type="button" class="s btnf"  d=5 value="本月" />
    <input type="button" class="s btnf"  d=6 value="上月" /></td>
  </tr>
  <tr>
   <th  width="20%" class="ft_sd te-rt">用户名</th>
   <td class="r"  colspan="2"><input id="username"  value='' />
  </tr>
  <tr  style="display: none;">
   <th>分类选择</th>
   <td class="r" colspan="2"><select name="select" class='bid'>
    <option value="">全部</option>
    
     
      <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['b']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
        
     
    <option value="<?php echo $_smarty_tpl->tpl_vars['b']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['bid'];?>
"><?php echo $_smarty_tpl->tpl_vars['b']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</option>
    
     
      <?php
}
}
?>
      
    
   </select>
    <select name="select" class='sid'>
    </select>
    <select name="select" class='cid'>
    </select></td>
  </tr>
  <tr>
   <th></th>
   <td  colspan="2" class="r">
    <input class="btn4 btnf query" type="button" value="查询"  style="margin:1px;" />
    <input class="btn4 btnf winprint" type="button" value="打印"  style="margin:1px;" />
    <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['topid']->value;?>
" id='saveuserid' LAYER="<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
"  />
    <input type="hidden" id=page value="1" />
    <input type="hidden" id='topid' value="<?php echo $_smarty_tpl->tpl_vars['topid']->value;?>
" LAYER="<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
" username='<?php echo $_smarty_tpl->tpl_vars['username']->value;?>
' /></td>
  </tr>
  <tr  style="display: none;">
   <TD></TD>
   <td style="text-align:left;padding-left:10px;"> 当前用户：
    <label class="nowuser" uid='<?php echo $_smarty_tpl->tpl_vars['topid']->value;?>
' LAYER="<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
</label></td>
   <td style="text-align:left;padding-left:10px;"><font color="blue">[返回上线]</font>：<a href='javascript:void(0)'>
    <label class="upuser" uid='<?php echo $_smarty_tpl->tpl_vars['topid']->value;?>
' LAYER="<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
"></label>
   </a></td>
  </TR>
 </table>
 <table class="data_table data_list list user" style="margin-top:10px;">
 </table>
 <div class="contents">
 <div id="p_para" class="contents param_panel input_panel tab_panel">
 <table style="border-collapse:unset" class="data_table list table_center nowtb" style='margin-top:10px;background:#fff;width:2500px;'>
  <thead class="nowtb2">
  <tr class="bt">
  <th><input type="checkbox" class="clickall" /></th>
  <th><strong>功能</strong></th>
  <th><strong>彩种</strong></th>
   <th><strong>期数</strong></th>
   <th><strong>下注编号</strong></th>
   <th><strong>类型</strong></th>
    <th><strong>下注类别</strong></strong></th>
   <th><strong>盘口</strong></th>
   <!--th>小盘</th-->
   <th><strong>状态</strong></th>
   <th><strong>投注内容</strong></th>
   <th><strong>下注金额</strong></th>
   <th><strong>赔率</strong></th>
   <th><strong>退水(%)</strong></th>
   <th><strong>会员账号</strong></th>
   <th><strong>下注时间</strong></th>
  </tr>
 
 </table>
 </div>
	</div>
</div>
</div>
<div id='test' style="margin-top:0px;"></div>
<?php echo '<script'; ?>
 language="javascript">
layernames= new Array();
layername = new Array();
<?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['layername']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
] = new Array();
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
]['wid'] = <?php echo $_smarty_tpl->tpl_vars['layername']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['wid'];?>
;
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
]['layer'] = new Array();
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['layername']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'], 'j', false, 'key');
$_smarty_tpl->tpl_vars['j']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['j']->value) {
$_smarty_tpl->tpl_vars['j']->do_else = false;
?>
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
]['layer'][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
] = '<?php echo $_smarty_tpl->tpl_vars['j']->value;?>
';
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
layernames[<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null);?>
]['namehead'] = '<?php echo $_smarty_tpl->tpl_vars['layername']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['namehead'];?>
';
<?php
}
}
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['layername']->value[0]['layer'], 'j', false, 'key');
$_smarty_tpl->tpl_vars['j']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['j']->value) {
$_smarty_tpl->tpl_vars['j']->do_else = false;
?>
layername[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
] = '<?php echo $_smarty_tpl->tpl_vars['j']->value;?>
';
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
var maxlayer= layername.length;
var layer =<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
 ;
sdate=new Array();
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sdate']->value, 'i', false, 'key');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
sdate[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
";
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
echo '</script'; ?>
>
</body>
</html><?php }
}
