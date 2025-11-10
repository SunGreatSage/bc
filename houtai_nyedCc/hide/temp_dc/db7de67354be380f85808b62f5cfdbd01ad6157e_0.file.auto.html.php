<?php
/* Smarty version 3.1.48, created on 2024-05-07 19:52:43
  from '/www/wwwroot/lhc/houtai/templates/default/hide/auto.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a160b4caf89_98641078',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'db7de67354be380f85808b62f5cfdbd01ad6157e' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/auto.html',
      1 => 1682881504,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663a160b4caf89_98641078 (Smarty_Internal_Template $_smarty_tpl) {
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
var js=1;var sss='auto';
<?php echo '</script'; ?>
>


<div class="jt-container">
<div class="top_info">
            <span class="title" style="color:#000000">降赔设置</span>
        </div>
		<div class="s_main">
             <div class="sub_w2" style="margin:0px;">
             <div class="right_btns">
                <span class="left_arrow" id="toleft2"><a class="bx-prev disabled"></a></span><span class="right_arrow" id="toright2"><a class="bx-next disabled" ></a></span>
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
                                赔率设置
								<span class="right" style="float: right; padding-right: 10px"><input type="checkbox" id="chkAll" ><label for="chkAll">全选</label></span>
                            </td>
							
                        </tr>

                    </tbody>
                    <tbody id="quickSetting"  style=" text-align:center">
						<tr style="border: none">
						 <td height="27" align="center" style="border: none; padding:1px 6px;" nowrap="nowrap">
                        </select>累计方法：<select id="quickjsff" style="float: none;" >
				        <option value="0">选择</option>
                        <option value="1">总额</option>
                        <option value="2">占成</option>
						 </select>
                    </td>
					</tr>
						<tr style="border: none">
                    <td height="27" align="center" style="border: none; padding:1px 6px;" nowrap="nowrap">
                        <select id="selectsetting">
                            <option value="startje">自动赔率累计金额</option>
                            <option value="startpeilv">累计金额降赔值</option>
                            <option value="addje">自动赔率金额变动</option>
                                <option value="attpeilv">金额变动降赔值</option>
                            <option value="lowpeilv">最低赔率</option>
                        </select>
                    </td>
                </tr>
						<tr style="border: none">
                    <td height="27" align="center" style="border: none; padding:1px 6px;" nowrap="nowrap"><input id="txtSetValue"></td>
                </tr>
				<tr style="border: none">
                            <td height="27" align="center" style="border: none; padding:1px 6px;" nowrap="nowrap">
                                <input type="button"  value="确定" id="quickbtn"  class="button btn1" />
								 &nbsp;
								<input type="button"  value="保存"  class="button btn4 send" />
								
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
                <table class="list table_ball pantb ">
 <thead>
  <TR>
   <th  align="center" nowrap="nowrap"  style="display: none;"><input type="checkbox"  class="all" />全选</th>
   <th align="center" nowrap="nowrap" >项目名称</th>
   <th align="center" nowrap="nowrap"><font class="font_r">累计方法</font></th>
   <th align="center" nowrap="nowrap"><font class="font_r">累计金额-降赔值</font></th>
   <th align="center" nowrap="nowrap"><font class="font_r">金额变动-降赔值</font></th>
   <th align="center" nowrap="nowrap" style="display: none;">开始下调期数-下调赔率[限快开]</th>
   <th align="center" nowrap="nowrap" style="display:none">停止下注金额</th>
   <th align="center" nowrap="nowrap"><font class="font_g">最低赔率</font></th>
   <th align="center" nowrap="nowrap">预降</th>
   <th align="center" nowrap="nowrap">自动开关</th>
  </TR>
  </thead>
   <tbody class="tablelist">
  <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['auto']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
  <tr f='<?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ftype'];?>
' class="nr">
  <td style="display: none;"><input type="checkbox"  /><input type="button" class="s1 pantb" value="同步选中" /></td>
   <th  align="center" nowrap="nowrap" class="para_pjName" ><?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</th>
   <td><select class='ifzc'>
     <option <?php if ($_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifzc'] == 0) {?>selected<?php }?> value="0">总额</option>
     <option <?php if ($_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifzc'] == 1) {?>selected<?php }?>  value="1">占成</option>
    </select></td>
   <td><input type="text" class="flag startje"  style="ime-mode: disabled; width: 60px" value="<?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['startje'];?>
" />
    -
    <input type="text" class="flag startpeilv"  style="ime-mode: disabled; width: 60px" value="<?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['startpeilv'];?>
" /></td>
   <td><input type="text" class="flag addje"  style="ime-mode: disabled; width: 60px" value="<?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['addje'];?>
" />
    -
    <input type="text" class="flag attpeilv" style="ime-mode: disabled; width: 60px"  value="<?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['attpeilv'];?>
" /></td>
   <td style="display: none;"><input type="text" class="flag qsnum"   value="<?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['qsnum'];?>
" />
    -
    <input type="text" class="flag qspeilv"   value="<?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['qspeilv'];?>
" /></td>
   <td  style="display:none"><input type="text" class="flag stopje"  value="<?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['stopje'];?>
" /></td>
   <td><input type="text" class="flag lowpeilv"  style="ime-mode: disabled; width: 60px" value="<?php echo $_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['lowpeilv'];?>
" /></td>
   <td><input type="checkbox" class="yj" <?php if ($_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['yj'] == 1) {?>checked<?php }?> /></td>
   <td><input type="checkbox" class="ifok" <?php if ($_smarty_tpl->tpl_vars['auto']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'] == 1) {?>checked<?php }?> /></td>
  </tr>
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
			<input type="button" value="修改" class="button send btn1">
			 &nbsp;
			<input type="button" value="取消" class="button cancel btn1">
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
</html><?php }
}
