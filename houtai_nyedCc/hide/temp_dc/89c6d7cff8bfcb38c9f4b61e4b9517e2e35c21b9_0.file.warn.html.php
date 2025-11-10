<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:29:42
  from '/www/wwwroot/lhc/houtai/templates/default/hide/warn.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f486ceb3f1_80665617',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '89c6d7cff8bfcb38c9f4b61e4b9517e2e35c21b9' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/warn.html',
      1 => 1682881550,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_6639f486ceb3f1_80665617 (Smarty_Internal_Template $_smarty_tpl) {
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
<?php echo '<script'; ?>
 type="text/javascript" src="/default/js/function.js"> <?php echo '</script'; ?>
>
</head>
<body id="topbody">
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';
var js=1;var sss='warn';
<?php echo '</script'; ?>
>

<div class="jt-container">
	<div class="top_info">
		<span class="title" style="color:#000000">注单预警金额设置</span>
	</div>
		<div class="s_main">
	<div class="sub_w2" style="margin:0px;">
              <div class="right_btns">
                <span class="left_arrow" id="toleft2"><a class="bx-prev disabled" ></a></span><span class="right_arrow" id="toright2"><a class="bx-next disabled" ></a></span>
            </div>

            <div class="page-tabs">
                <div class="tab_con2 clear " id="scroll_div2">
                    <ul id="sliderLottery">
					<?php if ($_smarty_tpl->tpl_vars['config']->value['slowtype'] == 1) {?>
<li>
<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
    <?php if ($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'] == 0) {?><a href="javascript:void(0)" class="g<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" gid='<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
'><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</a><?php }?>
 <?php
}
}
?>
</li>
<?php }?>
                    </ul>
                </div>
            </div>
        </div>
			 <div class="sider-left floatR" id="menuleft">

            <div class="main_set" id="quickSetting" style="display:">
                <table style="border: none" class="t_list" width="100%">
                    <tbody>
                        <tr style="border: none">
                            <td height="27" align="center" style="border: none; text-align: justify" nowrap="nowrap" id="tdMaxWinlostCode">
                                设置：
                            </td>
                        </tr>

                    </tbody>
                    <tbody id="quickSetting"  style=" text-align:center">
						<tr style="border: none">
                    <td height="27" align="center" style="border: none; padding:1px 6px;" nowrap="nowrap">
                        <select id="selectsetting">
                            <option value="je">实占金额预警</option>
                            <option value="ks">止损金额</option>
                        </select>
                    </td>
                </tr>
						<tr style="border: none">
                    <td height="27" align="center" style="border: none; padding:1px 6px;" nowrap="nowrap"><input id="txtSetValue"></td>
                </tr>
                        <tr style="border: none">
                            <td height="27" align="center" style="border: none; padding:1px 6px;" nowrap="nowrap">
                                <input type="button" value="确定" id="quickbtn"  class="button btn1" />
								 &nbsp;
								<input type="button" value="保存"  class="button btn4 send" />
								
                            </td>
                        </tr>
						<tr style="border: none">
                            <td height="27" align="center" style="border: none; padding:1px 6px;" nowrap="nowrap">
                                <input type="button" gid="<?php echo $_smarty_tpl->tpl_vars['gid']->value;?>
" class="button btn7 yiwotongbu" value="以我同步其他彩" >
								
                            </td>
                        </tr>
                    </tbody>
                </table>


            </div>
        </div>
			 <div class="toggle floatR"><div class="sider-toggle"><span title="收缩" style="top:50px">切换&nbsp;</span></div></div>
 	<div id="con_six" class="hover" style="margin-top:10px;">
    <table class="layout">
        <tr>
				<td class="panel">
					<table class="list table_ball">
					<thead>
					<tr>
						<th style="display: none;">
							<input type="checkbox" class="all">全选
						</th>
						<th>
							种类
						</th>
						<th>
						<font class="font_r">实占金额预警(变色)</font>
						</th>
						<th>
							<font class="font_r">止损金额(计算手补)</font>
						</th>
					</tr>
					</thead>
					<tbody class="tablelist">
                    
   <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['warn']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = min(($__section_i_1_loop - 0), $__section_i_1_loop);
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
     <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null) <= $_smarty_tpl->tpl_vars['nums']->value) {?>
   <TR class='nr' f='<?php echo $_smarty_tpl->tpl_vars['warn']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ftype'];?>
'>
    <td style="display: none;"><input type="checkbox"  class="pantbc"  /><input type="button" class="s1 pantb" value="同步选中" /></td>
    <th class="para_pjName"><?php echo $_smarty_tpl->tpl_vars['warn']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</th>
    <td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['warn']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['je'];?>
'  class="flag je" /></td>
    <td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['warn']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ks'];?>
'  class="flag ks" /></td>
   </TR>
   <?php }?>
   <?php
}
}
?>
   					</tbody>
					</table>
				</td>
				<td class="panel">
					<table class="list table_ball">
					<thead>
					<tr>
						<th style="display: none;">
							<input type="checkbox" class="all">全选
						</th>
						<th>
							种类
						</th>
						<th>
						<font class="font_r">实占金额预警(变色)</font>
						</th>
						<th>
						<font class="font_r">止损金额(计算手补)</font>
						</th>
					</tr>
					</thead>
					<tbody class="tablelist">
   <?php
$__section_i_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['warn']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_2_total = min(($__section_i_2_loop - 0), $__section_i_2_loop);
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_2_total !== 0) {
for ($__section_i_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_2_iteration <= $__section_i_2_total; $__section_i_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
     <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null) > $_smarty_tpl->tpl_vars['nums']->value) {?>
   <TR class='nr' f='<?php echo $_smarty_tpl->tpl_vars['warn']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ftype'];?>
'>
    <td style="display: none;"><input type="checkbox"  class="pantbc"  /><input type="button" class="s1 pantb" value="同步选中" /></td>
    <th class="para_pjName"><?php echo $_smarty_tpl->tpl_vars['warn']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</th>
    <td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['warn']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['je'];?>
'  class="flag je" /></td>
    <td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['warn']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ks'];?>
'  class="flag ks" /></td>
   </TR>
   <?php }?>
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
			<input type="button" value="保存" class="button send btn1">
			<input type="button" value="重置" class="button cancel btn1">
		</div>


	 </td>
        </tr>
		 </table>

</div>
</div>
</div>

<div id='test'></div>
<?php echo '<script'; ?>
 language="javascript">
var gid=<?php echo $_smarty_tpl->tpl_vars['gid']->value;?>
;
<?php echo '</script'; ?>
>
</body>
</html><?php }
}
