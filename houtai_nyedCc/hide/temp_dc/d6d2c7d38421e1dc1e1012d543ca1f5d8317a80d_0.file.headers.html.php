<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:25:10
  from '/www/wwwroot/lhc/houtai/templates/default/hide/headers.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f376632199_95193567',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd6d2c7d38421e1dc1e1012d543ca1f5d8317a80d' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/headers.html',
      1 => 1681149392,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6639f376632199_95193567 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"   <?php if ($_smarty_tpl->tpl_vars['rkey']->value == 0) {?>oncontextmenu="return false"<?php }?>><head>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <!--避免IE使用兼容模式-->
    <meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1" />
    <meta name="viewport" content="user-scalable=yes, target-densityDpi=device-dpi" />
<title>Welcome</title>
<link rel="stylesheet" href="/js/jquery/new/jqueryuib.min.css" />
<link rel="stylesheet" href="/default/css/layoutbl.min.css" />
<link rel="stylesheet" href="/default/css/Skins/blue/skinbd.min.css" />
<?php echo '<script'; ?>
 language="javascript" src="/js/jquery-1.8.3.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript" src="/js/public.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript" src="/js/md5.js"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 language="javascript">
function hideinfo(){ if(event.srcElement.tagName=="A"){
   window.status=event.srcElement.innerText}
}
document.onmouseover=hideinfo; 
document.onmousemove=hideinfo;
var globalpath = "<?php echo $_smarty_tpl->tpl_vars['globalpath']->value;?>
";
function changeh(){
    /*var obj = parent.document.getElementById("frame"); //取得父页面IFrame对象  
    var h= document.body.clientHeight+500;
    obj.style.height = h+"px"; //调整父页面中IFrame的高度为此页面的高度
    //obj.scrollTo(0, 0);*/
}

<?php echo '</script'; ?>
><?php }
}
