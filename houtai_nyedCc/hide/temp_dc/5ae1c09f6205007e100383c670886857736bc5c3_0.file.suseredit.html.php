<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:32:12
  from '/www/wwwroot/lhc/houtai/templates/default/hide/suseredit.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f51cb73593_23721085',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5ae1c09f6205007e100383c670886857736bc5c3' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/suseredit.html',
      1 => 1681976948,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6639f51cb73593_23721085 (Smarty_Internal_Template $_smarty_tpl) {
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
'></th></tr></thead>
<thead><tr><th colspan="2">账户资料</th></tr></thead>
<tbody>

       <TR>
    <th>所属<?php echo $_smarty_tpl->tpl_vars['layernamefu']->value;?>
</th>
    <TD><label><?php echo $_smarty_tpl->tpl_vars['fname']->value;?>
</label></td>
     </TR>
   <tr>
    <th><?php echo $_smarty_tpl->tpl_vars['layername']->value;?>
账号</th>
    <TD><label id='username' uid='<?php echo $_smarty_tpl->tpl_vars['userid']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
</label></td>

   </tr>
   <tr>
    <th>账号状态</th>
    <TD><label id='username' uid='<?php echo $_smarty_tpl->tpl_vars['userid']->value;?>
'></label><span class="statusControl"><label><input type="radio" name="status" value="1" <?php if ($_smarty_tpl->tpl_vars['status']->value == 1) {?>checked="checked"<?php }?>>启用</label>
    <label><input type="radio" name="status" value="2"  <?php if ($_smarty_tpl->tpl_vars['status']->value == 2) {?>checked="checked"<?php }?>>冻结</label>
    <label><input type="radio" name="status" value="0"  <?php if ($_smarty_tpl->tpl_vars['status']->value == 0) {?>checked="checked"<?php }?>>停用</label></span>
     </select><input type="hidden" value="edituser" name='xtype' id='xtype' /><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" name='action' id='action' /></td>

   </tr>
   <TR class='hides'>
    <th>帐户类型</th>
    <TD><select name="ifagent" id=ifagent>
    <?php if ($_smarty_tpl->tpl_vars['layer']->value == $_smarty_tpl->tpl_vars['maxlayer']->value) {?>
      <option value="0" <?php if ($_smarty_tpl->tpl_vars['ifagent']->value == 0) {?>selected<?php }?>>会员</option>
     <?php } else { ?>
      <option value="1" <?php if ($_smarty_tpl->tpl_vars['ifagent']->value == 1) {?>selected<?php }?>>运营商</option>
      <?php if ($_smarty_tpl->tpl_vars['layer']->value > 1) {?><option value="0" <?php if ($_smarty_tpl->tpl_vars['ifagent']->value == 0) {?>selected<?php }?>>直属会员</option><?php }?>
     <?php }?> 
     </select>&nbsp;&nbsp;(运营商/会员)</td>
    </TR>
    
 

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
    <th><?php echo $_smarty_tpl->tpl_vars['layername']->value;?>
名称</th>
    <TD><input type="text" name='name' id='name'  class="input" value='<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
'  /></td>
   </tr>
  <tr >
 <th>额度模式</th>
    <td > <label><input type="radio" name="fudong" value="0" class="fudong" <?php if ($_smarty_tpl->tpl_vars['fudong']->value == 0) {?>checked="checked"<?php }?> disabled />信用模式</label>
    <label><input type="radio" name="fudong" class="fudong" value="1" <?php if ($_smarty_tpl->tpl_vars['fudong']->value == 1) {?>checked="checked"<?php }?> disabled />现金模式</label></td>
</tr>


   <TR  class="modetr"  style="display: none;">
    <th>信用额度[低频]：</th>
    <TD ><input type="text" name="maxmoney" id='maxmoney' class="input hide" value="<?php echo $_smarty_tpl->tpl_vars['maxmoney']->value;?>
" /><span><?php echo $_smarty_tpl->tpl_vars['money']->value;?>
</span>&nbsp;&nbsp;<span id="dx" class="dx"></span><span class="hide">
     &nbsp;&nbsp;[限额
     <label><?php echo $_smarty_tpl->tpl_vars['fidmaxmoney']->value;?>
</label>
     ]&nbsp;&nbsp;可用余额：
   <label id='money'><?php echo $_smarty_tpl->tpl_vars['money']->value;?>
</label></span>&nbsp;<a href='javascript:void(0);'>修改</a></td>
   </tr>
   <TR  class="kmodetr"  <?php if ($_smarty_tpl->tpl_vars['config']->value['fasttype'] != 1) {?>style='display:none;'<?php }?>>
    <th>额度</th>
    <TD ><input type="text" name="kmaxmoney" id='kmaxmoney' class="input hide" value="<?php echo $_smarty_tpl->tpl_vars['kmaxmoney']->value;?>
" /><span><?php echo $_smarty_tpl->tpl_vars['kmoney']->value;?>
</span>&nbsp;&nbsp;<span id="dxk" class="dx"></span><span class="hide">
     &nbsp;&nbsp;
     [限额
     <label><?php echo $_smarty_tpl->tpl_vars['fidkmaxmoney']->value;?>
</label>
     ]&nbsp;&nbsp;可用余额：
   <label id='kmoney'><?php echo $_smarty_tpl->tpl_vars['kmoney']->value;?>
</label></span>&nbsp;<a href='javascript:void(0);' class="button bnt_2">修改</a></td>
   </tr>   

      <TR  class="fmodetr" <?php if ($_smarty_tpl->tpl_vars['fudong']->value == 0) {?>style='display:none;'<?php }?>>
    <th>额度</th>
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
    <th>最多帐户数</th>
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
   <TR>
    <th>开放盘口</th>
    <TD> <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['fidpan']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
     <label><input  class="pantype" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" <?php if (in_array($_smarty_tpl->tpl_vars['i']->value,$_smarty_tpl->tpl_vars['pans']->value)) {?>checked<?php }?>  />
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
   <?php if ($_smarty_tpl->tpl_vars['layer']->value == 1) {?>
   <TR style="display: none;">
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
  <TR>    <th>控盘功能</th>
    <TD><select name='ifexe'  <?php if ($_smarty_tpl->tpl_vars['config']->value['panel'] == 0) {?>disabled <?php }?> id='ifexe'>
      <option value="0" <?php if ($_smarty_tpl->tpl_vars['ifexe']->value == 0) {?>selected<?php }?>>关闭</option>
      <option value="1" <?php if ($_smarty_tpl->tpl_vars['ifexe']->value == 1) {?>selected<?php }?>>开启</option>
     </select></td>
     
     </TR>
     <tr style="display: none;">
    <th>赔率调节方式</th>
    <TD><select name='pself' id='pself' style="width:210px;">
      <option value="0"   <?php if ($_smarty_tpl->tpl_vars['pself']->value == 0) {?>selected<?php }?>>使用上级赔率(上级基础上加减)</option>
      <option value="1"   <?php if ($_smarty_tpl->tpl_vars['pself']->value == 1) {?>selected<?php }?>>使用自设赔率</option>
     </select></td>

   </tr>
  <TR style="display: none;">    <th>参数设置</th>
    <TD><select name='cssz' id='cssz'>
      <option value="0" <?php if ($_smarty_tpl->tpl_vars['cssz']->value == 0) {?>selected<?php }?>>关</option>
      <option value="1" <?php if ($_smarty_tpl->tpl_vars['cssz']->value == 1) {?>selected<?php }?>>开</option>
     </select>&nbsp;&nbsp;&nbsp;设置各盘赔率差</td>
     
     </TR>

   <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['fidplc']->value == 1) {?>
   <TR>
    <th>赚赔设置</th>
       <TD>
<select name='plc' id='plc'>
      <option value="0"   <?php if ($_smarty_tpl->tpl_vars['plc']->value == 0) {?>selected<?php }?> >关闭</option>
      <option value="1"  <?php if ($_smarty_tpl->tpl_vars['plc']->value == 1) {?>selected<?php }?> >开启</option>
     </select>&nbsp;&nbsp;是否赚赔下级,关闭后下级也不能赚赔
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
   <TR  class='hides'>
    <Td colspan="2" style="font-size:13px;color:#0063e3">备注：快开类型在【<?php echo $_smarty_tpl->tpl_vars['editstart']->value;?>
--<?php echo $_smarty_tpl->tpl_vars['editend']->value;?>
】修改才能生效，低频类型在开盘期间修改以下参数不会生效！管理员修改参数立即生效<br />
        <label>如果锁定占成，不管补货功能开放与否，该帐户将不能补货！</label>---会员略过!<BR />
    <label>本级吃补占成=吃补占成-直属下级吃补占成，如果是直属下级补货，本级吃补占成=吃补占成-0</label>---会员略过!<BR />
    <label>如果直属下级的【上级占成】设为0，本级将没有吃补占成</label>---会员略过!</Td></TR>

    </tbody>
  </table>
   
 <table class="addtb2   marT10 table_ball">
 <thead>
 <th colspan="7" class="shareMode0">占成设置 - 默认模式</th>
			</thead>
<tbody>
 <tr class="shead">
            <th rowspan="2" width="10%" class="shareMode0 shareMode1">类型</th>
			<th rowspan="2" width="10%" class="shareMode0 shareMode1">彩种开关</th>
            <th rowspan="2" width="20%" class="shareMode0 shareMode1">本级<?php echo $_smarty_tpl->tpl_vars['layernamefu']->value;?>
占成</th>
            <th colspan="2" class="shareMode0">下级<?php echo $_smarty_tpl->tpl_vars['layername']->value;?>
占成</th>
            <th rowspan="2" width="15%" class="shareMode0 shareMode1">允许补货</th>
			<th rowspan="2" width="20%" class="shareMode0 shareMode1">补货占成</th>
        </tr>
        <tr class="shead">

             <th width="15%" class="shareMode0" style="display: ;">最低占成 (%)</th>

            <th width="15%" class="shareMode0">最高成数 (%)</th>
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
        <input type="text" class="upzc share"  style="width: 100px" maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uupzc'];?>
' />&nbsp;(0% 至 <?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
%)    
    </td>
    <td>
        <input type="text" class="zcmin share"  style="width: 100px" maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uzcmin'];?>
' />&nbsp;%
    </td> 
    <td>
        <input type="text" class="zc share"  style="width: 100px" maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
' value='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uzc'];?>
' />&nbsp;(最大<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['zc'];?>
%)
    </td> 
     <td>
<select class="flytype"  val='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['uflytype'];?>
'>
     <?php if ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 0) {?>
         <option value="0">禁止</option>
     <?php } elseif ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 1) {?>
        <option value="0">禁止</option>
        <option value="1">开放</option>
     <?php } elseif ($_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flytype'] == 2) {?>
         <option value="0">禁止</option>
         <option value="2">开放</option>
     <?php } else { ?>
         <option value="0">禁止</option>
         <option value="1">开放</option>
     <?php }?>
     </select>
     </td>
    <td>
        <input type="text"  style="width: 100px" class="flyzc share" maxzc='<?php echo $_smarty_tpl->tpl_vars['fidgamecs']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flyzc'];?>
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

  <div class="data_footer control"><input type="button" value="确定" class="button edit btn1" /> <input type="button" value="取消"  class="close button btn1"></div>
    </div><?php }
}
