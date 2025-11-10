<?php
/* Smarty version 3.1.48, created on 2024-05-07 19:52:42
  from '/www/wwwroot/lhc/houtai/templates/default/hide/setatt.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a160a73f261_08380458',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3c91f0cf6f5c5816c37db3c0cea9d9413f667397' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/setatt.html',
      1 => 1682881540,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663a160a73f261_08380458 (Smarty_Internal_Template $_smarty_tpl) {
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
<style>
.hide{display:none;}
</style>
</head>
<body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='setatt';<?php echo '</script'; ?>
>
<div class="jt-container">
	<div class="top_info">
		<span class="title" style="color:#000000">各盘差分、参数</span>
		
		<div class="center" ><div id="cmcontrol">退水最大值：<input id="quick_a1" style="width:60px">&nbsp;&nbsp;赔率加减值：<input id="quick_a2" style="width:60px">&nbsp;&nbsp;赚赔最大值：<input id="quick_a3" style="width:60px">&nbsp;&nbsp;<select id="quick1" style="float: none;" >
                        <option value="0">选择盘差项目</option>
                        <option value="b">B盘差分</option>
                        <option value="c">C盘差分</option>
                        <option value="d">D盘差分</option>
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
                <table class="list table_ball setatt2 patt ">
<thead>
<TR style="display: none;"> <Th colspan="10"></Th><th colspan="4">模式1<input type="button" class="btn1 btnf modetb" v='1' value="同步其他模式" /></th>  <th colspan="4" class="hide">模式2<input type="button" class="btn1 btnf modetb" v='2' value="同步其他模式" /></th><th colspan="4" class="hide">模式3<input type="button" class="btn1 btnf modetb" v='3' value="同步其他模式" /></th><th colspan="4"  class="hide">模式4<input type="button" class="btn1 btnf modetb" v='4' value="同步其他模式" /></th><th colspan="4"  class="hide">模式5<input type="button" class="btn1 btnf modetb" v='5' value="同步其他模式" /></th></TR>
  <TR>
   <th  style="display: none;"><input type="checkbox"  class="all" /></th>
     <th  class='hide' >ID</th><th  style="display: none;">大类</th><th rowspan="2">种类</th><th rowspan="2">退水最大值</th><th rowspan="2" style="display: none;" >退水调节差</th><th rowspan="2" style="display: none;">赔率调节差</th><th rowspan="2">赔率加减值<span style="color: red;">(控盘)<span class="title"></th><th rowspan="2" style="color: olivedrab;" >赚赔最大值</th><th rowspan="2" style="display: none;">补货赔率调整</th><th rowspan="2" style="display: none;">补货赔率调整开关</th>
     <th class="hide">A盘</th><th>B盘差分</th><th>C盘差分</th><th>D盘差分</th><th>AB盘差</th>
     <th class="hide" >A盘差</th><th class="hide">B盘差</th><th class="hide">C盘差</th><th class="hide">D盘差</th><th class="hide">AB差</th>
     <th class="hide">A盘差</th><th class="hide">B盘差</th><th class="hide">C盘差</th><th class="hide">D盘差</th><th class="hide">AB差</th>
     <th class="hide">A盘差</th><th class="hide">B盘差</th><th class="hide">C盘差</th><th class="hide">D盘差</th><th class="hide">AB差</th>
     <th class="hide">A盘差</th><th class="hide">B盘差</th><th class="hide">C盘差</th><th class="hide">D盘差</th><th class="hide">AB差</th>
     

	</TR>
	<tr>
                                                    <th>降(正减负加)</th>

                                                    <th>降(正减负加)</th>

                                                    <th>降(正减负加)</th>

                                                    <th>特/正</th>

                                                </tr>
	</thead>
<tbody class="tablelist">
  <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['cs']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
     <TR>
     <td style="display: none;"><input type="checkbox" /><input type="button" class="btn1 btnf patttb" value="同选中" /></td>
     <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['class'];?>
</td>
        <td class="bcs"  style="display: none;" ><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['bcname'];?>
</td>
        <th class="para_pjName" ><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</th>
         <td style="display: none;"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['points'];?>
</td>
        <td style="display: none;"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pointsatt'];?>
</td>
        <td class='peilvatt'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['peilvatt'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['peilvatt1'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['maxatt'];?>
</td>
        <td style="display: none;"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flypeilv'];?>
</td>
        <td style="display: none;"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyifok'];?>
[0/1]</td>

        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['a1'];?>
</td>
        <td class='pc'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['b1'];?>
</td>
        <td class='pc'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['c1'];?>
</td>
        <td class='pc'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['d1'];?>
</td>
		<?php if (($_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'] == '特碼' || $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'] == '正特')) {?>
        <td class='pc'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ab1'];?>
</td>
		<?php } else { ?>
		<td></td>
		<?php }?>
        
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['a2'];?>
</td>
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['b2'];?>
</td>
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['c2'];?>
</td>
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['d2'];?>
</td>
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ab2'];?>
</td>
        
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['a3'];?>
</td>
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['b3'];?>
</td>
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['c3'];?>
</td>
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['d3'];?>
</td>
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ab3'];?>
</td>
        
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['a4'];?>
</td>
        <td class="hide"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['b4'];?>
</td>
        <td class="hide"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['c4'];?>
</td>
        <td class="hide"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['d4'];?>
</td>
        <td class="hide"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ab4'];?>
</td> 
        
        <td class='hide'><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['a5'];?>
</td>
        <td class="hide"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['b5'];?>
</td>
        <td class="hide"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['c5'];?>
</td>
        <td class="hide"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['d5'];?>
</td>
        <td class="hide"><?php echo $_smarty_tpl->tpl_vars['cs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ab5'];?>
</td>
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
<div id='test'></div>
</body>
</html>
<?php }
}
