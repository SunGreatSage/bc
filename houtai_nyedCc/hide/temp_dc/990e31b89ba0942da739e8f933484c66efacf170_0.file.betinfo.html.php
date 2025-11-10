<?php
/* Smarty version 3.1.48, created on 2024-05-07 18:54:56
  from '/www/wwwroot/lhc/houtai/templates/default/hide/betinfo.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a0880dbe744_48580609',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '990e31b89ba0942da739e8f933484c66efacf170' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/betinfo.html',
      1 => 1681876194,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663a0880dbe744_48580609 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="L_HK6">
  <?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='betinfo';<?php echo '</script'; ?>
>  



<div class="jt-container s_main">
    <div class="header clear">
    </div> 
    <div id="middle-content" class="middle-content clear" style="height: 2048px;"> 
        <div class="main-content">
            <div class="main-content-in">
                <div id="content" class="content ios" style="height: 2048px;">
                    <table class="data_table info_table panel">
                        <thead><tr><th colspan="2">注单备份</th></tr></thead>
                        <tbody>
                            <tr>
                                <th class="ft_sd te-rt" width="20%">下注账号：</th>
                                <td>
                                    <input type="text" name="agentId" id="agentId" />  说明：选填，若不填则导出所有注单。
                                </td>
                            </tr>
                            <tr>
                                <th class="ft_sd te-rt">彩种：</th>
                                <td>
                                    <select id="lotteryType">
                                        <option value="">请选择彩种</option>
										<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
    <?php if ($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'] == 0) {?><option value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
"><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</option><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</a><?php }?>
    <?php
}
}
?>
                                           
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th class="ft_sd te-rt">导出期数：</th>
                                <td>
                                   <span id=qishu>选择彩种后获取期数</span>
                                </td>
                            </tr>
							 <tr>
                                <th class="ft_sd te-rt">导出状态：</th>
                                <td>
                                   <span id=status>选择彩种后获取状态</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="data_footer control">
                        <input type="button" id="ExportBetInfo" class="button bnt_1" value="导出注单" />
                    </div>
					
                </div>
            </div>
        </div>

    </div> 
</div>
   <iframe id="downfastfrm" style="display:none;" ></iframe> 

</body>
</html><?php }
}
