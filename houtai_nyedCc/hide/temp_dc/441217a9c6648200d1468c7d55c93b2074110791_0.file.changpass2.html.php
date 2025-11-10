<?php
/* Smarty version 3.1.48, created on 2024-05-07 20:07:09
  from '/www/wwwroot/lhc/houtai/templates/default/hide/changpass2.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a196d2c3eb6_63242814',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '441217a9c6648200d1468c7d55c93b2074110791' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/changpass2.html',
      1 => 1679412060,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663a196d2c3eb6_63242814 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 language="javascript" src="/js/md5.js"><?php echo '</script'; ?>
>
</head>
<body>
<?php echo '<script'; ?>
 language="javascript" id=myjs>var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';
var js=1;var sss='changepass2';
<?php echo '</script'; ?>
>
<div class="jt-container">
    <div class="top_info">
        <span class="title">变更密码</span>
    </div>
        <div class="s_main">
            <table class="data_table info_table table_ball">
                <thead><tr><th colspan="2">修改密码</th></tr></thead>
                <tr><th style="width: 40%">原始密码</th><td class="t_left"><input style="text-align: left" id="oldPassword" name="oldpasswd" type="password" required="required" class="input" /></td></tr>
                <tr><th>新设密码</th><td class="t_left"><input style="text-align: left" id="password" name="newpasswd" type="password" required="required" class="input" /></td></tr>
                <tr><th>确认密码</th><td class="t_left"><input style="text-align: left" id="ckPassword" name="confirmpasswd" type="password" required="required" class="input" /></td></tr>

            </table>
            <div class="data_footer control">
                &nbsp;<input class="button btn1" type="submit" value="确定" />&nbsp;<input type="reset" class="button btn2" value="重置" />
            </div>
            <div class="control fter" style="color: red">
                
            </div>
        </div>
</div>
</body>
</html><?php }
}
