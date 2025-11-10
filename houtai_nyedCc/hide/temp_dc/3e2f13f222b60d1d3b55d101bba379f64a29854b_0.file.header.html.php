<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:29:44
  from '/www/wwwroot/lhc/houtai/templates/default/hide/header.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f4880ecfe3_20932881',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3e2f13f222b60d1d3b55d101bba379f64a29854b' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/header.html',
      1 => 1679103718,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6639f4880ecfe3_20932881 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  <?php if ($_smarty_tpl->tpl_vars['rkey']->value == 0) {?>oncontextmenu="return false"<?php }?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1" />
<meta name="viewport" content="user-scalable=yes, target-densityDpi=device-dpi" />
<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
<link rel="stylesheet" href="/js/jquery/new/jqueryuib.min.css" />
<link rel="stylesheet" href="/default/css/bettingm.min.css" />
<link rel="stylesheet" href="/default/css/Skins/blue/skinbd.min.css" />
<?php echo '<script'; ?>
 language="javascript" src="/js/jquery-1.8.3.min.js"><?php echo '</script'; ?>
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
function changeh(h){
   /* var obj = parent.document.getElementById("frame"); //取得父页面IFrame对象  
     obj.style.height = h+"px"; //调整父页面中IFrame的高度为此页面的高度*/
}

<?php echo '</script'; ?>
>
<style>
body{overflow-y:hidden;}
</style><?php }
}
