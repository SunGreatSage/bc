<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:25:08
  from '/www/wwwroot/lhc/houtai/templates/default/hide/sysconfig.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f3747cd343_92704285',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b9da35442a08345f06ef0c276ca4a317074ea315' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/sysconfig.html',
      1 => 1681973576,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_6639f3747cd343_92704285 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link href="/default/css/jquery.loadmask.css" rel="stylesheet" />
<?php echo '<script'; ?>
 src="/js/sitetool.js?v=004"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="/js/jquery.loadmask.js?v=004"><?php echo '</script'; ?>
>
</head><body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='sysconfig';<?php echo '</script'; ?>
>
<div class="jt-container">
 <input type="hidden" name="xtype" value="setsys" />
  <div class="s_main">  
 <table  class="data_table info_table user_panel table_ball infotb">
 <thead><tr><th colspan="4">参数设置</th></tr></thead>
 <tbody>
  <tr>
                <th>系统状态</th>
				<td><select style="width: 100px;"  class="ifopen">
   <OPTION value="0" <?php if ($_smarty_tpl->tpl_vars['config']->value['ifopen'] == 0) {?>selected<?php }?>>关闭</OPTION>
   <OPTION value="1" <?php if ($_smarty_tpl->tpl_vars['config']->value['ifopen'] == 1) {?>selected<?php }?>>开启</OPTION>  
   </select></td>
				 <th>登入识别手机版</th>
               <td>
				 <label><input type="radio" name="loginfenli" class="loginfenli"  <?php if ($_smarty_tpl->tpl_vars['config']->value['loginfenli'] == 1) {?>checked<?php }?> value="1">识别</label>
			     <label><input type="radio" name="loginfenli" class="loginfenli"  <?php if ($_smarty_tpl->tpl_vars['config']->value['loginfenli'] == 0) {?>checked<?php }?>  value="0">不识别</label>&nbsp;(会员自行切换手机版)</td>
            </tr>
			
			
  <tr>
                <th>平台名称</th>
                <td><input type="text" style="width: 100px;" class='webname txt1'  value='<?php echo $_smarty_tpl->tpl_vars['config']->value['webname'];?>
' ></td>
				 <th>最低注单金额</th>
                <td><input type="text" style="width: 100px;" class='minje txt1' value='<?php echo $_smarty_tpl->tpl_vars['config']->value['minje'];?>
' ></td>
            </tr>
 <tr>
                  <th>用户密码有效期(天)</th>
                <td><input type="text" style="width: 100px;" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['passtime'];?>
' class="passtime txt1" /></td>
				 <th>登陆停留(分钟)</th>
                <td><input type="text" style="width: 100px;" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['livetime'];?>
' class="livetime txt1" /></td>
            </tr>
	<tr>
             <th>投注间隔(秒)</th>
                <td><input type="text" style="width: 100px;" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['tzjg'];?>
' class="tzjg txt1" /></td>
				  <th>注单最高派彩</th>
                <td><input type="text" style="width: 100px;" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['maxpc'];?>
' class="maxpc txt1" /></td>
            </tr>
			<tr>
             <th>操作密码更改</th>
               <td><input type="password" type='text' style="width: 100px;" class="txt1 supass" placeholder="请输入密码"  /></td>
				     <th>系统版本</th><td><select  disabled style="width: 100px;" class="moneytype">
   <OPTION value="0" <?php if ($_smarty_tpl->tpl_vars['config']->value['moneytype'] == '0') {?>selected<?php }?>>信用版</OPTION>
   <OPTION value="1" <?php if ($_smarty_tpl->tpl_vars['config']->value['moneytype'] == '1') {?>selected<?php }?>>现金版</OPTION>
   </select>&nbsp;(请联系系统人员)</td>
            </tr>
			
		<tr>
               <th>降倍赔率恢复</th>
				<td>
               <select  style="width: 100px;" class="plresetfs">
   <OPTION value="now" <?php if ($_smarty_tpl->tpl_vars['config']->value['plresetfs'] == 'now') {?>selected<?php }?>>开出即恢复</OPTION>
   <OPTION value="next" <?php if ($_smarty_tpl->tpl_vars['config']->value['plresetfs'] == 'next') {?>selected<?php }?>>停留一期恢复</OPTION>
   </select>&nbsp;(自动降倍后的赔率)</td>
   <th>信用额度恢复</th>
				<td>
               <select  style="width: 100px;" class="reseted">
   <OPTION value="week" <?php if ($_smarty_tpl->tpl_vars['config']->value['reseted'] == 'week') {?>selected<?php }?>>每周</OPTION>
   <OPTION value="day" <?php if ($_smarty_tpl->tpl_vars['config']->value['reseted'] == 'day') {?>selected<?php }?>>每天</OPTION>
   </select></td>
            </tr>
		<tr>
                <th>自动恢复赔率</th>
				<td>
			 <label><input type="radio" name="autoresetpl" class="autoresetpl"  <?php if ($_smarty_tpl->tpl_vars['config']->value['autoresetpl'] == 1) {?>checked<?php }?> value="1">开启</label>
			 <label><input type="radio" name="autoresetpl" class="autoresetpl"  <?php if ($_smarty_tpl->tpl_vars['config']->value['autoresetpl'] == 0) {?>checked<?php }?>  value="0">关闭</label>
			 &nbsp;(控盘后的赔率恢复)
			 </td>
                <th>代理自动降倍</th>
				<td>
				 <label><input type="radio" name="comattpeilv" class="comattpeilv"  <?php if ($_smarty_tpl->tpl_vars['config']->value['comattpeilv'] == 1) {?>checked<?php }?> value="1">启用</label>
			     <label><input type="radio" name="comattpeilv" class="comattpeilv"  <?php if ($_smarty_tpl->tpl_vars['config']->value['comattpeilv'] == 0) {?>checked<?php }?>  value="0">不启用</label>
				 &nbsp;(仅支持一级代理启用自动降倍)
				 </td>
            </tr>
			<tr>
                <th>代理自动补货</th>
				<td>
				 <label><input type="radio" name="flyflag" class="flyflag"  <?php if ($_smarty_tpl->tpl_vars['config']->value['flyflag'] == 1) {?>checked<?php }?> value="1">启用</label>
			     <label><input type="radio" name="flyflag" class="flyflag"  <?php if ($_smarty_tpl->tpl_vars['config']->value['flyflag'] == 0) {?>checked<?php }?>  value="0">不启用</label></td>
                <th>代理修改占成</th>
				<td>
				<label><input type="radio" name="editzc" class="editzc"  <?php if ($_smarty_tpl->tpl_vars['config']->value['editzc'] == 1) {?>checked<?php }?> value="1">允许</label>
			     <label><input type="radio" name="editzc" class="editzc"  <?php if ($_smarty_tpl->tpl_vars['config']->value['editzc'] == 0) {?>checked<?php }?>  value="0">不允许</label>
				</td>
            </tr>
			<tr>
                <th>一级控盘功能</th>
				<td>
				 <label><input type="radio" name="panel" class="panel"  <?php if ($_smarty_tpl->tpl_vars['config']->value['panel'] == 1) {?>checked<?php }?> value="1">启用</label>
			     <label><input type="radio" name="panel" class="panel"  <?php if ($_smarty_tpl->tpl_vars['config']->value['panel'] == 0) {?>checked<?php }?>  value="0">不启用</label></td>
                <th>IP记录</th>
				<td>
				<label><input type="radio" name="iprecord" class="iprecord"  <?php if ($_smarty_tpl->tpl_vars['config']->value['iprecord'] == 1) {?>checked<?php }?> value="1">记录</label>
			     <label><input type="radio" name="iprecord" class="iprecord"  <?php if ($_smarty_tpl->tpl_vars['config']->value['iprecord'] == 0) {?>checked<?php }?>  value="0">不记录</label>
				 &nbsp;(包括登陆,操作IP)
				</td>
            </tr>
			<tr>
                <th>代理删除用户</th>
				<td>
				<label><input type="radio" name="deluser" class="deluser"  <?php if ($_smarty_tpl->tpl_vars['config']->value['deluser'] == 1) {?>checked<?php }?> value="1">允许</label>
			     <label><input type="radio" name="deluser" class="deluser"  <?php if ($_smarty_tpl->tpl_vars['config']->value['deluser'] == 0) {?>checked<?php }?>  value="0">不允许</label>
				</td>
				
                <th>启用赚赔功能</th>
				<td class="t_left"><select style="width: 100px;" class="plc">
   <OPTION value="0" <?php if ($_smarty_tpl->tpl_vars['config']->value['plc'] == 0) {?>selected<?php }?>>关闭</OPTION>
   <OPTION value="1" <?php if ($_smarty_tpl->tpl_vars['config']->value['plc'] == 1) {?>selected<?php }?>>开启</OPTION>  
   </select></td>
            </tr>
			<tr>
			 <th>会员报表彩种合并</th>
			 <td>
			 <label><input type="radio" name="merge" class="merge"  <?php if ($_smarty_tpl->tpl_vars['config']->value['merge'] == 1) {?>checked<?php }?> value="1">开启</label>
			 <label><input type="radio" name="merge" class="merge"  <?php if ($_smarty_tpl->tpl_vars['config']->value['merge'] == 0) {?>checked<?php }?>  value="0">关闭</label>
			  &nbsp;(会员查账彩种是否合并)
			 </td>
			 <th>代理报表下载</th>
			 <td>
			 <label><input type="radio" name="baobf" class="baobf"  <?php if ($_smarty_tpl->tpl_vars['config']->value['baobf'] == 1) {?>checked<?php }?> value="1">开启</label>
			 <label><input type="radio" name="baobf" class="baobf"  <?php if ($_smarty_tpl->tpl_vars['config']->value['baobf'] == 0) {?>checked<?php }?>  value="0">关闭</label>
			 </td>
			 </tr>
			 
			 <tr  class='tram' >
			 <th>代理报表查询限制</th>
			 <td><input type="text" style="width: 60px;" class='agents txt1' value='<?php echo $_smarty_tpl->tpl_vars['config']->value['agents'];?>
' >&nbsp;日<span>&nbsp;(可查范围：<span class="agents"></span>—<span class="time"></span>)</span></td>
			 <th>会员报表查询限制</th>
			 <td><input type="text" style="width: 60px;" class='members txt1'  disabled value='<?php echo $_smarty_tpl->tpl_vars['config']->value['members'];?>
' >&nbsp;日<span>&nbsp;(默认两周报表不可更改)</span></td>
			 </tr>
			 
			 
			 
			 
			 
			<tr style="display: none;">
	  <th>退水修改开始时间</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['editstart'];?>
' class="editstart txt1" /></td>
    <th>退水修改结束时间</th><td><input type="text" value='<?php echo $_smarty_tpl->tpl_vars['config']->value['editend'];?>
' class="editend txt1" /></td>
	 </td>

    </tr>
</tbody>
 </table>
      <div class="data_footer control">
          <input type="button" value="保存" class="button editall bnt_1" />
		</div>
</div>
<div id='test'></div>
<?php echo '<script'; ?>
 language="javascript">
var agents =<?php echo $_smarty_tpl->tpl_vars['config']->value['agents'];?>
;
var members =<?php echo $_smarty_tpl->tpl_vars['config']->value['members'];?>
;
<?php echo '</script'; ?>
>
</body>
</html><?php }
}
