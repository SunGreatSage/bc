<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:29:39
  from '/www/wwwroot/lhc/houtai/templates/default/hide/suserlist.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f4834cb293_33746636',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7324e5df16b8d9306021c1af8614683c437f90cb' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/suserlist.html',
      1 => 1681160098,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6639f4834cb293_33746636 (Smarty_Internal_Template $_smarty_tpl) {
?> <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['user']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
   <TR layer='<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'];?>
' uid="<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['userid'];?>
" fid="<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fid'];?>
" fname="<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fname'];?>
" username='<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['username'];?>
' ifagent='<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifagent'];?>
' wid='<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['wid'];?>
' class="in<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['userid'];?>
" fudong='<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fudong'];?>
' fids='<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['userid'];?>
,<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fids'];?>
' types='<?php if ($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifagent'] == 1) {?>ag<?php } else { ?>us<?php }?>' lyname='<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['lyname'];?>
'>
    
      <td class="option" style="display: none;" ><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['userid'];?>
" /></td>
     <td class="online"><?php if (($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['online'] == 0)) {?><span class='s0'></span><?php } else { ?><span class='s1 zhuxiao' title='注销'></span><?php }?></td>
     <td class="parent"><?php if ($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 1) {?>admin<?php } else { ?><a href='javascript:void(0);' class="upuser"><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['upuser'];?>
</a><?php }?></TD>
  
     
     <td class="type"><?php if ($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fudong'] == 0) {?>信用<?php } else { ?>现金<?php }?><BR /><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layername'];?>
</td>
     <td class="username"><a href='javascript:void(0);' <?php if ($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifagent'] == 1) {?> class='showdown' layertype=0 <?php }?>><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['username'];?>
</a> [<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
]</td>
     <?php if ($_smarty_tpl->tpl_vars['config']->value['fasttype'] == 1 || $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fudong'] == 0) {?>
     <td class="account">
         <?php if ($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifagent'] == 1) {?>
           <a href='javascript:void(0);'  class="kmaxmoney"><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['kmoney'];?>
</a> 
         <?php } else { ?>
           <a href='javascript:void(0);'  class="kmaxmoney"><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['kmoney'];?>
</a> 
         <?php }?>   
     </td>
     <?php }?>
     <?php if ($_smarty_tpl->tpl_vars['config']->value['slowtype'] == 1) {?>
     <td class="account" style="display: none;">
         <?php if ($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifagent'] == 1) {?>
           <a href='javascript:void(0);'  class="maxmoney"><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['money'];?>
</a> 
         <?php } else { ?>
           <a href='javascript:void(0);'  class="maxmoney"><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['money'];?>
</a> 
         <?php }?>  
     </td>
     <?php }?>

     <td class="share"><a href='javascript:void(0);' class="zcmx">明细</a></td>
     <?php if (($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifagent'] == 0)) {?>
        <td class="branch"><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pan'];?>
盘</td>
     <?php } else { ?>
        <!--<td class="branch"><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['downnum'];?>
</td>-->
        <td class="branch"><a href='javascript:void(0);' class='showdown' layertype=0><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['downnumag'];?>
</a></td>
        <td class="branch"><a href='javascript:void(0);' class='showdown' layertype=1><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['downnumu'];?>
</a></td>
        <td nowrap="nowrap">
        
        <?php if (($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer']+1) < $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['maxlayer']) {?><a href='javascript:void(0);' class='bu_ico ico_dl add' types='ag'>代理</a>/<?php }?><a href='javascript:void(0);' class='bu_ico ico_hy add' types='us' >会员</a>
        </td>
     <?php }?>   

     <td class="create"><?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['regtime'];?>
</td>
     <td class="status"><input type="button"  style="color: <?php if (($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['status'] == 1)) {?>green<?php } else { ?>red<?php }?>;" class="s<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['status'];?>
" v="<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['status'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['statusz'];?>
"></td>
  
     <TD class="op">
      <a href='javascript:void(0);'  class="modify edit"  >修改</a>/
      <a href='javascript:void(0);'  class="commission setpoints"  >退水</a>/
	  <a href='javascript:void(0);'  class="resetpoints"  title="恢复退水与上级相同" >恢复限额</a>/
      <a href='javascript:void(0);'  class="login_log info logininfo" >登陆日志</a>/
      <a href='javascript:void(0);'  class="op_log record"  >变更日志</a>/ 
	  <a href='javascript:void(0);'  class="my moneylog" >额度变更</a>/         
      <?php if ($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifagent'] == 1) {?>
        <a href='javascript:void(0);'  class="showson" >子帐号</a>
      <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hides']->value == 1) {?>
       <a href='javascript:void(0);'  class="editmoney"  >改信用</a>/
       <?php if ($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['layer'] == 1) {?>
         <a href='javascript:void(0);'  class="resetpl"  >复赔</a>/
       <?php }?>
       <a href='javascript:void(0);'  class="ss"  >消息</a>/
       <a href='javascript:void(0);'  class="jzftime"  >校正时间</a>/
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['user']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifagent'] == 0) {?>
       <a href='javascript:void(0);'  class="userzd"  >注单</a>/
       <a href='javascript:void(0);'  class="deluserbao"  >清空报表</a>
    <?php }?>
     </TD>
  
   </TR>
   <?php
}
}
?>
   
   <input type="hidden" class='pageinfo' pcount='<?php echo $_smarty_tpl->tpl_vars['pcount']->value;?>
' rcount='<?php echo $_smarty_tpl->tpl_vars['rcount']->value;?>
' upage='<?php echo $_smarty_tpl->tpl_vars['upage']->value;?>
' />
<?php }
}
