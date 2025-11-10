<?php
/* Smarty version 3.1.48, created on 2024-05-07 20:04:58
  from '/www/wwwroot/lhc/houtai/templates/default/hide/suseradduser.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a18eaefa0a2_16808495',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '802c5131e41908022ca6cdedb3391f872eafb74f' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/suseradduser.html',
      1 => 1679387122,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_663a18eaefa0a2_16808495 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="top_info">
        <span class="title">会员 -&gt; 新增</span>
        <span class="right"><a class="back close"  href="javascript:;">返回</a></span>
    </div>
<div class="s_main" id="saveForm">
<table class="data_table info_table user_panel addtb list table_ball">
<thead><tr><th colspan="2">账户资料</th></tr></thead>
<tbody>
      <TR>
    <th><?php echo $_smarty_tpl->tpl_vars['layernamefu']->value;?>
账号</th>
    <TD><label><?php echo $_smarty_tpl->tpl_vars['fname']->value;?>
</label><input type="hidden" value="adduser" name='xtype' id='xtype' /><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" name='action' id='action' /></td>
     </TR>
   <tr>
    <th>会员帐号</th>
    <TD>
     <input type="text" name='username' id='username' class="input" style="width:100px;" maxlength="<?php echo $_smarty_tpl->tpl_vars['namelength']->value;?>
" /><span id="usernameMsg"></span>&nbsp;&nbsp;<select name='status'  class='hides' layer='<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
'   id='status'>
      <option   value="1" selectef>可用</option>
      <option value="0"  >禁用</option>
      <option value="2"  >暂停</option>
     </select></td>
   </tr>
      <TR class='hides'>
    <th>帐户类型</th>
    <TD><select name="ifagent" id=ifagent>
   <option value="0">会员</option>
     </select>&nbsp;&nbsp;(运营商/会员)</td>
     </TR>


      <TR style="display: none;">
    <th>限制赢利金额</th>
    <TD><input type="text" name='yingdeny' id='yingdeny'  class="input" value='<?php echo $_smarty_tpl->tpl_vars['yingdeny']->value;?>
'  />&nbsp;&nbsp;设0为不限制</td>
   </tr> 
   
   <TR>
    <th>登入密码</th>
    <TD><input type="text" name=password id=password class="input" /><span id="passdMsg"></span><input type="hidden" name=userpass id=userpass class="input" /></td>
    </TR>
   <TR>
    <th>会员名称</th>
    <TD><input type="text" name='name' id='name'  class="input"  /></td>

   </tr>
      <TR>
    <th>可用盘类</th>
    <TD> <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['pan']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
      <label><input  class="pantype" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"   />
     <?php echo $_smarty_tpl->tpl_vars['i']->value;?>
盘 </label>
     
     <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> <select name="defaultpan" id='defaultpan' class="hide"></select></td>
 
   </tr>
<tr><th>额度模式</th><td>
    <?php if ($_smarty_tpl->tpl_vars['fudong']->value == 0) {?>    
    <label><input type="radio" name="fudong" value="0" class="fudong" checked="checked">信用模式</label>
    <label><input type="radio" name="fudong" class="fudong" value="1">现金模式</label>
    <?php } else { ?>
    <label><input type="radio" name="fudong" value="0" class="fudong" disabled="disabled">信用模式</label>
    <label><input type="radio" name="fudong" class="fudong" value="1" disabled="disabled"  checked="checked">现金模式</label>
    <?php }?>
</td></tr>

    <TR  class="modetr"  style="display: none;">
    <th>信用额度[低频]：</th>
    <TD ><input type="text" name="maxmoney" id='maxmoney' class="input" value="" />&nbsp;&nbsp;<span id="dx" class="dx"></span>
     &nbsp;&nbsp;[上级余额
     <label><?php echo $_smarty_tpl->tpl_vars['maxmoney']->value;?>
</label>
     ]</td>
   </tr>
   <TR  class="kmodetr"  <?php if ($_smarty_tpl->tpl_vars['config']->value['fasttype'] != 1) {?>style='display:none;'<?php }?>>
    <th>额度</th>
    <TD ><input type="text" name="kmaxmoney" id='kmaxmoney' class="input" value="" />&nbsp;&nbsp;<span id="dxk" class="dx"></span>
     &nbsp;(上级余额
     <label><?php echo $_smarty_tpl->tpl_vars['kmaxmoney']->value;?>
</label>
     )</td>
   </tr>

   <TR  class="fmodetr" <?php if ($_smarty_tpl->tpl_vars['fudong']->value == 0) {?>style='display:none;'<?php }?>>
    <th>额度</th>
    <TD ><input type="text" name="fmaxmoney" id='fmaxmoney' class="input" value="" />&nbsp;&nbsp;<span id="dxf" class="dx"></span>
     &nbsp;(上级余额
     <label><?php echo $_smarty_tpl->tpl_vars['kmaxmoney']->value;?>
</label>
     )</td>
   </tr>
   <TR>
    <th>赚取退水</th>
    <TD> 
     <select name="liushui" id="liushui">
        
                                  <option value="0">水全退到底</option>
                                  <option value="0.1">赚取0.1%</option>
                                  <option value="0.3">赚取0.3%</option>
                                  <option value="0.5">赚取0.5%</option>
                                  <option value="1">赚取1.0%</option>
                                  <option value="1.5">赚取1.5%</option>
                                  <option value="2">赚取2.0%</option>
                                  <option value="2.5">赚取2.5%</option>
                                  <option value="100">赚取所有</option>
                              
               </select>
</td>
 
   </tr>




  <TR style="display:none;">
    <th>默认彩种：</th>
    <TD><select name="mgid" id=mgid>
<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['gamecs']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
if ($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'] == 1) {?>
<option value="<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" <?php if ($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'] == $_smarty_tpl->tpl_vars['mgid']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</option>
<?php }
}
}
?>
     </select></td>
    </TR>

     
     <tr class='hides'>
    <th>&nbsp;</th>
   
    <TD><input type="button" class="btn1 btnf add"  value="新增<?php echo $_smarty_tpl->tpl_vars['layername']->value;?>
"  style="margin-right:20px;" /><input type="button" class="btn1 btnf close"  value="关闭窗口" /></td>
   </tr>
   <TR  class='hides'>
    <Td colspan="2">
   <label>如果锁定占成，不管走补功能开放与否，该帐户将不能走补！</label>---会员略过!<BR />
    <label>本级收补占成=收补占成-直属下级收补占成，如果是直属下级走补，本级收补占成=收补占成-0</label>---会员略过!<BR />
    <label>如果直属下级的【上级占成】设为0，本级将没有收补占成</label>---会员略过!
    </Td>
    </TR>
    </tbody>
  </table>
  

<?php if ($_smarty_tpl->tpl_vars['config']->value['zcmode'] == 1) {?> 
  <table class="addtb2 table_ball marT10">
<thead>
                    <tr>
                        <th colspan="8" class="shareMode0">
                            本级占成
                        </th>
                    </tr>
                </thead>
				<tbody>
                    <tr class="shead">
                        <th class="shareMode0 shareMode1" style="display: table-cell;">类型</th>
                        <th class="shareMode0 shareMode1" style="display: table-cell;">彩种开关	</th>
						<th class="shareMode0 shareMode1" style="display: table-cell;">占成</th>
                        <th class="shareMode0">可分配占成</th>
                    </tr>
                </tbody>
<tbody>
   <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['gamecs']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
   <tr <?php if ($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'] == 0) {?>style="display:none"<?php }?>>
    <th gid='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
'><?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</th>
    <td><select class="ifok"  val='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'];?>
'>
    <?php if ($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'] == 0) {?>
      <option value="0">关闭</option>
     <?php } else { ?>
      <option value="0">关闭</option>
      <option value="1">开启</option>
     <?php }?>
     </select></td>
     
    <td>
        <input type="text" class="upzc share" maxzc='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='0' />%    
    </td>
	<td>
         <label style="color:red">（上级占成范围在 0%与<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
% ）之间</label>    
    </td>
    <td  class="hides">
        <input type="text" class="zcmin share"  maxzc='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='0' />%
    </td> 
    <td  class="hides">
        <input type="text" class="zc share"  maxzc='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' />(最大<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
%)
    </td> 
     <td  class="hides">
<select class="flytype"  val='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'];?>
'>
     <?php if ($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 0) {?>
         <option value="0">关闭</option>
     <?php } elseif ($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 1) {?>
        <option value="0">关闭</option>
        <option value="1">内补</option>
     <?php } elseif ($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 2) {?>
         <option value="0">关闭</option>
         <option value="2">外补</option>
     <?php } else { ?>
         <option value="0">关闭</option>
         <option value="1">内补</option>
         <option value="2">外补</option>
         <option value="3">内外补</option>
     <?php }?>
     </select>
     </td>
    <td  class="hides">
        <input type="text" class="flyzc share" maxzc='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
'  value='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
' />(0% 至 <?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
%)
    </td>
   </tr>
   <?php
}
}
?>
  
</tbody>
</table>
<?php } else { ?>
 <table class="data_table info_table share_panel input_panel addtb2 list">
 <thead>
  <th colspan=2>占成设置</th>

   <tr class="shead">
    <th rowspan="2">类型</th>    
    <th rowspan="2"><?php echo $_smarty_tpl->tpl_vars['layernamefu']->value;?>
实际占成</th>
    <th colspan="2"  class="hides"><?php echo $_smarty_tpl->tpl_vars['layername']->value;?>
占成</th>    
    <th rowspan="2"  class="hides">补货功能</th>
    <th rowspan="2"  class="hides">下线补货占成</th>
</tr>
<tr class="shead">
   <th class="hides">最低</th>
   <th class="hides">最高</th>
</tr>
</thead>
<tbody>
   <?php
$__section_i_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['gamecs']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_2_total = $__section_i_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_2_total !== 0) {
for ($__section_i_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_2_iteration <= $__section_i_2_total; $__section_i_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
    <tr <?php if (($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['typeid'] == 1 && $_smarty_tpl->tpl_vars['config']->value['fasttype'] == 0)) {?>style='display:none'<?php }?> <?php if (($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['typeid'] == 0 && $_smarty_tpl->tpl_vars['config']->value['slowtype'] == 0)) {?>style='display:none'<?php }?>>
    <th  typeid='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['typeid'];?>
'><?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['typename'];?>
</th>
    
    <td>
        <input type="text" class="upzc share" maxzc='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='0' />(0% 至 <?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
%)    
    </td>
    <td class="hides">
        <input type="text" class="zcmin share"  maxzc='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='0' />%
    </td> 
    <td class="hides">
        <input type="text" class="zc share"  maxzc='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' />(最大<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
%)
    </td> 
     <td class="hides">
<select class="flytype"  val='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'];?>
'>
     <?php if ($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 0) {?>
         <option value="0">关闭</option>
     <?php } elseif ($_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 1) {?>
        <option value="0">关闭</option>
        <option value="1">开放</option>
     <?php }?>

     </select>
     </td>
    <td class="hides">
        <input type="text" class="flyzc share" maxzc='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
'  value='<?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
' />(0% 至 <?php echo $_smarty_tpl->tpl_vars['gamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
%)
    </td>
   </tr>
   <?php
}
}
?>
  
</tbody>
  </table>
<?php }?>
  <div class="data_footer control"><input type="button" value="确定" class="button add btn1"> <input type="button" value="取消"  class="close button btn1"></div>
   </div><?php }
}
