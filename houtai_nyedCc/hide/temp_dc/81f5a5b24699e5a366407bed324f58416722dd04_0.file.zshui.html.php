<?php
/* Smarty version 3.1.48, created on 2024-05-08 17:01:29
  from '/www/wwwroot/lhc/houtai/templates/default/hide/zshui.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663b3f699b4fa2_43218824',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '81f5a5b24699e5a366407bed324f58416722dd04' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/zshui.html',
      1 => 1682881554,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663b3f699b4fa2_43218824 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link href="/default/css/control.css" rel="stylesheet" />
<link href="/default/css/Site.css" rel="stylesheet" />
<link href="/default/css/jquery.loadmask.css" rel="stylesheet" />
<?php echo '<script'; ?>
 src="/js/sitetool.js?v=004"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="/js/jquery.loadmask.js?v=004"><?php echo '</script'; ?>
>
</head>
<body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='zshui';<?php echo '</script'; ?>
>
<div class="jt-container">
	<div class="top_info">
            <span class="title" style="color:#000000">默认退水</span>
			<div class="center" ><div id="cmcontrol">注单最低：<input id="quick_a1" style="width:60px">&nbsp;&nbsp;注单限额：<input id="quick_a2" style="width:60px">&nbsp;&nbsp;单期限额：<input id="quick_a3" style="width:60px"> &nbsp;&nbsp;<select id="quick1" style="float: none;" >
                        <option value="0">选择退水项目</option>
                        <option value="a">A盘</option>
                        <option value="b">B盘</option>
                        <option value="c">C盘</option>
                        <option value="d">D盘</option>
                    </select>
					<select style="float: none;" id="quick2">
                        <option value="">--</option>
                        <option value="1">1</option>
                        <option value="0.5">0.5</option>
                        <option value="0.1">0.1</option>
                        <option value="0.05">0.05</option>
                        <option value="0.01">0.01</option>
                        <option value="0">0</option>
                        <option value="-0.01">-0.01</option>
                        <option value="-0.05">-0.05</option>
                        <option value="-0.1">-0.1</option>
                        <option value="-0.5">-0.5</option>
                        <option value="-1">-1</option>
                    </select>
		<input type="button" value="确定" id="quickbtn" class="button btn2">
		 &nbsp; &nbsp; &nbsp; &nbsp;
		  <input type="button" data-ignore="1" value="保存"  class="button btn1 send" />
		  <input type="button" data-ignore="1" value="重置"  class="button btn1" />
		  <input type="button" gid="<?php echo $_smarty_tpl->tpl_vars['gid']->value;?>
" class="button btn7 yiwotongbu" value="以我同步其他彩" >
		 &nbsp; &nbsp;保存时下线是否同步: 
		 <label><input type="radio" name="downline" value="1" checked="checked">不同步 </label>
		 <label><input type="radio" name="downline" value="2">全部同步 </label>
		 <label><input type="radio" name="downline" value="3">等量增加</label>
		</div>
        </div>
		<span class="right">&nbsp;&nbsp;<a class="back" onclick="javascript:history.back(-1);" href="javascript:void(0);">返回</a></span>
		</div>
		
	
	<div class="s_main">	
		<div id="p_para" class="contents param_panel input_panel tab_panel">

                        <table class="layout" templatecode="HK6">
                            <tbody><tr>
							<?php if ($_smarty_tpl->tpl_vars['config']->value['slowtype'] == 1) {?>
                                <td height="30" colspan="2" align="left">
								<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
                                        <?php if ($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'] == 0) {?><span loto="MCHK6" group="HK6" class="g<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
 group_tap" gid='<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
'><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</span><?php }?>
								 <?php
}
}
?>
                                    
                                </td>
								<?php }?>
                            </tr>
							</tbody>
							</table>
							 </div>
		<div id="con_six" class="hover param_panel">
    <table class="layout">
        <tr>
            <td class="panel">
                <table class="list table_ball at_2 ">
<thead>
  <TR>
    <th rowspan="2" style="display: none;"><input type="checkbox"  class="all" /></th>
	<th rowspan="2">种类</th><th rowspan="2" style="display:none;">类别</th>
	<th rowspan="2"   style="display:none;">赔率差最大</th>
	<th rowspan="2"  style="display:none;">保底赔率</th>
	<th rowspan="2">注单最低</th>
	<th rowspan="2">注单限额</th>
	<th rowspan="2">单期限额</th>
	<th colspan="2">A盘退水(%)</th>
	<th colspan="2">B盘退水(%)</th>
	<th colspan="2">C盘退水(%)</th>
	<th colspan="2">D盘退水(%)</th>
     
  </TR>
    <tr>
     <th>特/正A</th><th>特/正B</th><th>特/正A</th><th>特/正B</th><th>特/正A</th><th>特/正B</th><th>特/正A</th><th>特/正B</th>
  </tr>
  </thead>
  <tbody class="tablelist">
  <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['zpan']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
     <TR>
     <td style="display: none;"><input type="checkbox"   /><input type="button" class="btn1 btnf pantb" value="同选中" /></td>
        <th class="para_pjName"><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</th>
        <td  style="display:none;">-</td>
        <td  style="display:none;">-</td>
        <td  style="display:none;">-</td>
		<td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['minje'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['maxje'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['cmaxje'];?>
</td>
        <?php if ($_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['abcd'] == 1) {?>
             <?php if ($_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ab'] == 1) {?>
                <td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsaa'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsab'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsba'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsbb'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsca'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointscb'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsda'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsdb'];?>
</td>
             <?php } else { ?>
                <td colspan="2"><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsa0'];?>
</td>
                <td colspan="2"><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsb0'];?>
</td>
                <td colspan="2"><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsc0'];?>
</td>
                <td colspan="2"><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsd0'];?>
</td>
             <?php }?>
        <?php } else { ?>
                <td colspan="8"><?php echo $_smarty_tpl->tpl_vars['zpan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsa0'];?>
</td>
        <?php }?>
     </TR>
  <?php
}
}
?>
  </tbody>
</table>
</td>

        </tr>
        <tr>
	<td height="30" colspan="2" align="center">
		<div class=" data_footer control">
			<input type="button" value="保存" class="button send bnt_1">
			<input type="button" value="重置" class="button cancel bnt_2">
		</div>
		 </td>
        </tr>
		 </table>
</div>
</div>
</div>
<?php echo '<script'; ?>
 language="javascript">
var gid=<?php echo $_smarty_tpl->tpl_vars['gid']->value;?>
;
<?php echo '</script'; ?>
>
<!--div id='test'></div-->
</body>
</html>
<?php }
}
