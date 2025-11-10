<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:30:59
  from '/www/wwwroot/lhc/houtai/templates/default/hide/suseredituser.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f4d37baff2_42857738',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ddeb05e33940cb910d8e1b86247b6d9886d2a536' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/suseredituser.html',
      1 => 1681976960,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6639f4d37baff2_42857738 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="top_info">
    <span class="title"><?php echo $_smarty_tpl->tpl_vars['layername']->value;?>
 <span><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
（<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
）</span> -&gt; 更改</span>
    <span class="right"><a class="back close">返回</a></span>
</div>
<ul class="tab">
    <li class="tab_title02">
        <a href="javascript:void(0);" class="on">基本资料</a>
        <a href="javascript:void(0);" class="tuiset">退水设定</a>
    </li>
</ul>
<div class="s_main" id="saveForm"> 
<table class="data_table info_table user_panel edittb list table_ball">
<thead style="display: none;"><tr><th colspan="2" class="actionname" style="text-align:center" layer='<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
'>修改会员</th></tr></thead>
<thead><tr><th colspan="2">账户资料</th></tr></thead>
<tbody>

       <TR>
    <th>所属<?php echo $_smarty_tpl->tpl_vars['layernamefu']->value;?>
</th>
    <TD><label><?php echo $_smarty_tpl->tpl_vars['fname']->value;?>
</label></td>
     </TR>
    <tr>
    <th>会员帐号</th>
    <TD><label id='username' uid='<?php echo $_smarty_tpl->tpl_vars['userid']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
</label></td> </tr>
	
   <TR class='hides'>
    <th>帐户类型：</th>
    <TD><select name="ifagent" id=ifagent>

      <option value="0">会员</option>

     </select>&nbsp;&nbsp;(运营商/会员)</td>
    </TR>

   <TR style="display: none;">
    <th>限制赢利金额：</th>
    <TD><input type="text" name='yingdeny' id='yingdeny'  class="input" value='<?php echo $_smarty_tpl->tpl_vars['yingdeny']->value;?>
'  />&nbsp;&nbsp;设0为不限制</td>
   </tr> 
    
    <TR>
    <th>登入密码</th>
    <TD><input type="text" name=password id=password class="input" /><input type="hidden" name=userpass id=userpass class="input" /></td>
  </TR>

<tr>
  <th>
    错误登录次数
  </th>
  <td>
    <div>
      <span class="errortimesstatus"><?php echo $_smarty_tpl->tpl_vars['errortimes']->value;?>
</span>&nbsp;&nbsp;<a href='javascript:void(0);' class="czpass button bnt_2">重置</a>
    </div>
  </td>
</tr>

   <TR>
    <th>会员名称</th>
    <TD><input type="text" name='name' id='name'  class="input" value='<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
'  /></td>
   </tr>  
     
	    <TR>
    <th>开放盘类</th>
    <TD> <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['fidpan']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
     <label><input  class="pantype" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" <?php if (in_array($_smarty_tpl->tpl_vars['i']->value,$_smarty_tpl->tpl_vars['pans']->value)) {?>checked<?php }?>   />
     <?php echo $_smarty_tpl->tpl_vars['i']->value;?>
盘</label>
     
     <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> <select name="defaultpan" id='defaultpan' class="hide">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['pans']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
     <option value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['defaultpan']->value == $_smarty_tpl->tpl_vars['i']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
</option>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </select> </td>
  
   </tr>
   
  <tr >
 <th>额度模式</th>
    <td > <label><input type="radio" name="fudong" value="0" class="fudong" <?php if ($_smarty_tpl->tpl_vars['fudong']->value == 0) {?>checked="checked"<?php }?> disabled />信用模式</label>
    <label><input type="radio" name="fudong" class="fudong" value="1" <?php if ($_smarty_tpl->tpl_vars['fudong']->value == 1) {?>checked="checked"<?php }?> disabled />现金模式</label></td>
</tr>



   <TR  class="modetr"   style="display: none;">
    <th>信用额度[低频]：</th>
    <TD ><input type="text" name="maxmoney" id='maxmoney' class="input hide" value="<?php echo $_smarty_tpl->tpl_vars['maxmoney']->value;?>
" /><span><?php echo $_smarty_tpl->tpl_vars['money']->value;?>
</span>&nbsp;&nbsp;<span id="dx" class="dx"></span><span class="hide">
     &nbsp;&nbsp;[限额
     <label><?php echo $_smarty_tpl->tpl_vars['fidmaxmoney']->value;?>
</label>
     ]</span>&nbsp;&nbsp;【可用余额：
   <label id='money'><?php echo $_smarty_tpl->tpl_vars['money']->value;?>
</label>】&nbsp;&nbsp;<a href='javascript:void(0);'>修改</a></td>
   </tr>
   <TR  class="kmodetr"  <?php if (($_smarty_tpl->tpl_vars['fudong']->value == 1 || $_smarty_tpl->tpl_vars['config']->value['fasttype'] == 0)) {?>style='display:none;'<?php }?>>
    <th>额度：</th>
    <TD ><input type="text" name="kmaxmoney" id='kmaxmoney' class="input hide" value="<?php echo $_smarty_tpl->tpl_vars['kmaxmoney']->value;?>
" /><span><?php echo $_smarty_tpl->tpl_vars['kmoney']->value;?>
</span>&nbsp;&nbsp;<span id="dxk" class="dx"></span><span class="hide">
     &nbsp;&nbsp;
     [限额
     <label><?php echo $_smarty_tpl->tpl_vars['fidkmaxmoney']->value;?>
</label>
     ]</span>&nbsp;&nbsp;<a href='javascript:void(0);' class="button bnt_2">修改</a>&nbsp;&nbsp;(可用余额：<label id='kmoney'><?php echo $_smarty_tpl->tpl_vars['kmoney']->value;?>
</label>)</td>
   </tr>   
   
      <TR  class="fmodetr" <?php if ($_smarty_tpl->tpl_vars['fudong']->value == 0) {?>style='display:none;'<?php }?>>
    <th>额度：</th>
    <TD ><input type="text" name="fmaxmoney" id='fmaxmoney' class="input hide" value="<?php echo $_smarty_tpl->tpl_vars['kmaxmoney']->value;?>
" /><span><?php echo $_smarty_tpl->tpl_vars['kmoney']->value;?>
</span>&nbsp;&nbsp;<span id="dxf" class="dx"></span><span class="hide">
     &nbsp;&nbsp;
     [限额
     <label><?php echo $_smarty_tpl->tpl_vars['fidkmaxmoney']->value;?>
</label>
     ]</span>&nbsp;&nbsp;<a href='javascript:void(0);' class="button bnt_2">修改</a>&nbsp;&nbsp;(余额：<label id='kmoney'><?php echo $_smarty_tpl->tpl_vars['kmoney']->value;?>
</label>)</td>
   </tr>   

  

   <?php if ($_smarty_tpl->tpl_vars['layer']->value < $_smarty_tpl->tpl_vars['maxlayer']->value) {?>
     <?php if ($_smarty_tpl->tpl_vars['maxrenflag']->value == 1) {?>
   <TR>
    <th>最多帐户数：</th>
    <TD><input type="text" class="input" name='maxren'  id='maxren' value="<?php echo $_smarty_tpl->tpl_vars['maxren']->value;?>
" />
     [限额
     <label><?php echo $_smarty_tpl->tpl_vars['fidmaxren']->value;?>
</label>
     ] </td>
    
   </tr>
   <?php }?>
  <?php }?>

<tr style="display: none;">
  <th>
    转账密码
  </th>
  <td>
    <div>
      <?php if (($_smarty_tpl->tpl_vars['moneypassflag']->value == 1)) {?>已绑定<?php } else { ?>未绑定<?php }?> <?php if (($_smarty_tpl->tpl_vars['moneypassflag']->value == 1)) {?><input type="button" class="czmoneypass" value="重置"><?php }?>
    </div>
  </td>
</tr>
<tr>
	<th>账号状态</th>
    <TD>&nbsp;<span class="statusControl"><label><input type="radio" name="status" value="1" <?php if ($_smarty_tpl->tpl_vars['status']->value == 1) {?>checked="checked"<?php }?>>启用</label>
    <label><input type="radio" name="status" value="2"  <?php if ($_smarty_tpl->tpl_vars['status']->value == 2) {?>checked="checked"<?php }?>>冻结</label>
    <label><input type="radio" name="status" value="0"  <?php if ($_smarty_tpl->tpl_vars['status']->value == 0) {?>checked="checked"<?php }?>>停用</label></span>
    
    <input type="hidden" value="edituser" name='xtype' id='xtype' /><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" name='action' id='action' /></td>

   </tr>
   <?php if ($_smarty_tpl->tpl_vars['layer']->value == 1) {?>
   <TR>
    <th>网站名称</th>
    <TD><select name='wid' id='wid'>
      
      
      
      
      <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['web']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
        
      
      
      
      <option value="<?php echo $_smarty_tpl->tpl_vars['web']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['wid'];?>
" <?php if ($_smarty_tpl->tpl_vars['web']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['wid'] == $_smarty_tpl->tpl_vars['wid']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['web']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['webname'];?>
</option>
      
      
      
      
      <?php
}
}
?>
     
     
     
     
     </select></td>

   </tr>
  <TR>    <th>赔率调节</th>
    <TD><select name='ifexe' id='ifexe'>
      <option value="0" <?php if ($_smarty_tpl->tpl_vars['ifexe']->value == 0) {?>selected<?php }?>>关</option>
      <option value="1" <?php if ($_smarty_tpl->tpl_vars['ifexe']->value == 1) {?>selected<?php }?>>开</option>
     </select></td>
     
     </TR>
     <tr>
    <th>赔率调节方式</th>
    <TD><select name='pself' id='pself' style="width:210px;">
      <option value="0"   <?php if ($_smarty_tpl->tpl_vars['pself']->value == 0) {?>selected<?php }?>>使用上级赔率(上级基础上加减)</option>
      <option value="1"   <?php if ($_smarty_tpl->tpl_vars['pself']->value == 1) {?>selected<?php }?>>使用自设赔率</option>
     </select></td>

   </tr>
  <TR>    <th>参数设置</th>
    <TD><select name='cssz' id='cssz'>
      <option value="0" <?php if ($_smarty_tpl->tpl_vars['cssz']->value == 0) {?>selected<?php }?>>关</option>
      <option value="1" <?php if ($_smarty_tpl->tpl_vars['cssz']->value == 1) {?>selected<?php }?>>开</option>
     </select>&nbsp;&nbsp;&nbsp;设置各盘赔率差</td>
     
     </TR>

   <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['fidplc']->value == 1) {?>
   <TR style="display: none;">
    <th>赚赔设置</th>
       <TD>
<select name='plc' id='plc'>
      <option value="0"   <?php if ($_smarty_tpl->tpl_vars['plc']->value == 0) {?>selected<?php }?> >关</option>
      <option value="1"  <?php if ($_smarty_tpl->tpl_vars['plc']->value == 1) {?>selected<?php }?> >开</option>
     </select>&nbsp;&nbsp;&nbsp;设置此帐户能否赚下级赔率差，如果关闭,该帐户所有下级也不能赚赔率差。
    </td>

   </tr>
   <?php }?>
   

   

 <TR style="display:none;">
    <th>默认彩种：</th>
    <TD><select name="mgid" id=mgid>
<?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['fidgamecs']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
if ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'] == 1) {?>
<option value="<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" <?php if ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'] == $_smarty_tpl->tpl_vars['mgid']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</option>
<?php }
}
}
?>
     </select></td>
    </TR>
    
    <tr  class='hides'><Td></Td>
    <TD><input type="button" class="btn1 btnf"  id='edit' value="修改<?php echo $_smarty_tpl->tpl_vars['layername']->value;?>
" style="margin-right:20px;" /><input type="button" class="btn1 btnf close"  value="关闭窗口" /></td>
   </tr>
   <TR class='hides'>
    <Td colspan="2" style="font-size:13px;color:#0063e3">备注：快开类型在【<?php echo $_smarty_tpl->tpl_vars['editstart']->value;?>
--<?php echo $_smarty_tpl->tpl_vars['editend']->value;?>
】修改才能生效，低频类型在开盘期间修改以下参数不会生效！管理员修改参数立即生效<br />
        <label>如果锁定占成，不管补货功能开放与否，该帐户将不能补货！</label>---会员略过!<BR />
    <label>本级吃补占成=吃补占成-直属下级吃补占成，如果是直属下级补货，本级吃补占成=吃补占成-0</label>---会员略过!<BR />
    <label>如果直属下级的【上级占成】设为0，本级将没有吃补占成</label>---会员略过!</Td></TR>

    </tbody>
  </table>

  
<?php if ($_smarty_tpl->tpl_vars['config']->value['zcmode'] == 1) {?> 
 <table class="addtb2   marT10 table_ball">
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
$__section_i_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['fidgamecs']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_2_total = $__section_i_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_2_total !== 0) {
for ($__section_i_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_2_iteration <= $__section_i_2_total; $__section_i_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
   <tr <?php if ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'] == 0) {?>style="display:none"<?php }?>>
    <th gid='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
'><?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</th>
    <td><select class="ifok"  val='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uifok'];?>
'>
    <?php if ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifok'] == 0) {?>
      <option value="0">关闭</option>
     <?php } else { ?>
      <option value="0">关闭</option>
      <option value="1">开启</option>
     <?php }?>
     </select></td>
     
    <td>
        <input type="text" class="upzc share" maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uupzc'];?>
' />% 
    </td>
	<td>
         <label style="color:red">（上级占成范围在 0%与<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
% ）之间</label>    
    </td>
    <td  class="hides">
        <input type="text" class="zcmin share"  maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uzcmin'];?>
'  />%
    </td> 
    <td  class="hides">
        <input type="text" class="zc share"  maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uzc'];?>
' />(最大<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
%)
    </td> 
     <td  class="hides">
<select class="flytype"  val='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uflytype'];?>
'>
     <?php if ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 0) {?>
         <option value="0">关闭</option>
     <?php } elseif ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 1) {?>
        <option value="0">关闭</option>
        <option value="1">内补</option>
     <?php } elseif ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 2) {?>
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
        <input type="text" class="flyzc share" maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
'  value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uflyzc'];?>
' />(0% 至 <?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
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
$__section_i_3_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['fidgamecs']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_3_total = $__section_i_3_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_3_total !== 0) {
for ($__section_i_3_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_3_iteration <= $__section_i_3_total; $__section_i_3_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
    <tr <?php if (($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['typeid'] == 1 && $_smarty_tpl->tpl_vars['config']->value['fasttype'] == 0)) {?>style='display:none'<?php }?> <?php if (($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['typeid'] == 0 && $_smarty_tpl->tpl_vars['config']->value['slowtype'] == 0)) {?>style='display:none'<?php }?>>
    <th  typeid='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['typeid'];?>
'><?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['typename'];?>
</th>
    
    <td>
        <input type="text" class="upzc share" maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uupzc'];?>
' />(0% 至 <?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
%)    
    </td>
    <td class="hides">
        <input type="text" class="zcmin share"  maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uzcmin'];?>
'  />%
    </td> 
    <td class="hides">
        <input type="text" class="zc share"  maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uzc'];?>
' />(最大<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
%)
    </td> 
     <td class="hides">
<select class="flytype"  val='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uflytype'];?>
'>
     <?php if ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 0) {?>
         <option value="0">关闭</option>
     <?php } elseif ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 1) {?>
        <option value="0">关闭</option>
        <option value="1">开放</option>
     <?php }?>

     </select>
     </td>
    <td class="hides">
        <input type="text" class="flyzc share" maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
'  value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uflyzc'];?>
' />(0% 至 <?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
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
  
  <div class="data_footer control"><input type="button" value="确定" class="button edit btn1" /> <input type="button" value="取消"  class="close button btn1"></div>
 </div><?php }
}
