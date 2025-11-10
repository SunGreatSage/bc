<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:29:54
  from '/www/wwwroot/lhc/houtai/templates/default/hide/mailbox.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f492b7a776_49626574',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '38291b6386bb343f0c5dac1df43eee563a2ff7be' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/mailbox.html',
      1 => 1681832256,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_6639f492b7a776_49626574 (Smarty_Internal_Template $_smarty_tpl) {
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
';var js=1;var sss='mailbox';<?php echo '</script'; ?>
>
<style>
.table_ball tbody input {
    text-align: left;
}
</style>
<div class="jt-container">
 <div class="top_info">
        <span class="title">备份邮箱设置</span>
    </div>
  <div class="s_main">  
 <table class="data_table info_table  table_ball">
 <thead><tr><th colspan="2">自动备份邮箱设置</th></tr></thead>
 <tbody>
                    <tr>
                        <th style="width: 40%">自动备份注单到邮箱</th>
                        <td class="t_left">
                            <label><input type="radio" name="mailbox"  <?php if ($_smarty_tpl->tpl_vars['config']->value['mailbox'] == 1) {?>checked<?php }?> value="1">启用</label>
					   <label><input type="radio" name="mailbox"  <?php if ($_smarty_tpl->tpl_vars['config']->value['mailbox'] == 0) {?>checked<?php }?> value="0">不启用</label>
                        </td>
                    </tr>
                    <tr>
                        <th>SMTP服务器</th>
                        <td class="t_left">
                              <input type="text"  style="width: 200px;" id="smtpserver" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['smtpserver'];?>
" >
                        </td>
                    </tr>
					 <tr>
                        <th>SMTP用户名</th>
                        <td class="t_left">
                              <input type="text" style="width: 200px;" id="smtpusername" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['smtpusername'];?>
" >
                        </td>
                    </tr>
					<tr>
                        <th>SMTP密码</th>
                        <td class="t_left">
                              <input type="password" style="width: 200px;" id="smtppassword" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['smtppassword'];?>
" >
                        </td>
                    </tr>
					 <tr>
                        <th>SMTP加密方式</th>
                       <td class="t_left">
					   <label><input type="radio" name="smtpencryption"  <?php if ($_smarty_tpl->tpl_vars['mailbox']->value['smtpencryption'] == 0) {?>checked<?php }?> value="0">TLS</label>
					   <label><input type="radio" name="smtpencryption"  <?php if ($_smarty_tpl->tpl_vars['mailbox']->value['smtpencryption'] == 1) {?>checked<?php }?> value="1">SSL</label>
					   <label><input type="radio" name="smtpencryption"  <?php if ($_smarty_tpl->tpl_vars['mailbox']->value['smtpencryption'] == 2) {?>checked<?php }?> value="2">STARTTLS</label>
                        </td>
                    </tr>
					<tr>
                        <th>SMTP端口</th>
                       <td class="t_left">
                              <input type="text" style="width: 100px;" id="smtppost" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['smtppost'];?>
" >
                        </td>
                    </tr>
					 <tr>
                        <th>发件人</th>
                       <td class="t_left">
                              <input type="text" style="width: 200px;" id="sender" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['sender'];?>
" >
							   名字
							    <input type="text" style="width: 90px;" id="sendername" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['sendername'];?>
" >
                        </td>
                    </tr>
					<tr>
                        <th>收件人</th>
                       <td class="t_left">
                              <input type="text" style="width: 200px;" id="recipient" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['recipient'];?>
" >
							  名字
							   <input type="text" style="width: 90px;" id="recipientname" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['recipientname'];?>
" >
                        </td>
                    </tr>
					<tr>
                        <th>发送标题</th>
                       <td class="t_left">
                              <input type="text" style="width: 300px;" id="smtptitle" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['smtptitle'];?>
" >
                        </td>
                    </tr>
					<tr>
                        <th>发送内容</th>
                       <td class="t_left">
                              <input type="text" style="width: 300px;" id="smtpcontent" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['smtpcontent'];?>
" >
                        </td>
                    </tr>
					<tr>
                        <th>启用彩种</th>
                       <td class="t_left">
					   <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
                             <?php if ($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'] == 0) {?> <label><input type="checkbox"  name="gamemail"  <?php if ($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gamemail'] == 1) {?>checked<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" ><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</label><?php }?>
							   <?php
}
}
?>
                        </td>
                    </tr>
					<tr>
                        <th>未结算备份</th>
						<td class="t_left">
                            <label><input type="radio" name="unsettlement"  <?php if ($_smarty_tpl->tpl_vars['mailbox']->value['unsettlement'] == 1) {?>checked<?php }?> value="1">启用</label>
					   <label><input type="radio" name="unsettlement"  <?php if ($_smarty_tpl->tpl_vars['mailbox']->value['unsettlement'] == 0) {?>checked<?php }?> value="0">不启用</label>
                        开始备份时间：<input type="text" style="width: 100px;" id="utime" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['utime'];?>
" >&nbsp;<span style="color:red;">请设置为开奖时间前且不在高峰期 一般为21:30:00</span>
						</td>
                    </tr>
					<tr>
                        <th>已结算备份</th>
						<td class="t_left">
                            <label><input type="radio" name="settled"  <?php if ($_smarty_tpl->tpl_vars['mailbox']->value['settled'] == 1) {?>checked<?php }?> value="1">启用</label>
					   <label><input type="radio" name="settled"  <?php if ($_smarty_tpl->tpl_vars['mailbox']->value['settled'] == 0) {?>checked<?php }?> value="0">不启用</label>
                        开始备份时间：<input type="text" style="width: 100px;" id="stime" value="<?php echo $_smarty_tpl->tpl_vars['mailbox']->value['stime'];?>
" >&nbsp;<span style="color:red;">请设置为结算时间后且不在高峰期 一般为23:30:00</span>
						</td>
                    </tr>
                </tbody>
				<tfoot><tr><td colspan="2" style="color:red;">SMTP信息不懂配置请联系系统人员,收件人禁止使用国内邮箱 可以使用参数 {期数} {彩种名称}来在名字 标题 内容获取对应的期数或彩种名称</td></tr></tfoot>
 </table>
      <div class="data_footer control">
          <input type="button" value="保存" id="smtpbtn" class="button bnt_1" />
		</div>
</div>
</body>
</html><?php }
}
