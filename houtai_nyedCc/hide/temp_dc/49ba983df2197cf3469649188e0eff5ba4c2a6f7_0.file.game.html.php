<?php
/* Smarty version 3.1.48, created on 2024-05-07 18:19:53
  from '/www/wwwroot/lhc/houtai/templates/default/hide/game.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a004901a094_56359207',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '49ba983df2197cf3469649188e0eff5ba4c2a6f7' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/game.html',
      1 => 1682183638,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663a004901a094_56359207 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 src="/js/sitetool.js?v=004"><?php echo '</script'; ?>
>
<style>
.h {
	display:none
}
.edittb {
	position:absolute;
	width:950px;
	display:none;
	background:#fff;
}

</style>
</head><body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='game';<?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript">
var g=new Array();
<?php echo '</script'; ?>
>
<div class="jt-container">
<div class="top_info">
    <span class="title">开盘设定</span><span class="right">&nbsp;&nbsp;<a class="back" onclick="javascript:history.back(-1);" href="javascript:void(0);">返回</a></span>
</div>
  <div class="s_main">  
 <table class="table_ball list  ztb">
 <thead>
  <tr>
   <th style="display: none;">ID</th>
   <th>彩种</th>
   <th style="display: none;">排序</th>
   <th>当前期数</th>
   <th>默认开盘时间</th>
   <th>默认关盘时间</th>
   <th>自动开奖</th>
   <th>自动开关盘</th>
   <th>盘口状态</th>
    <th>正码状态</th>
   <th>报表开放</th>
   <th style="display: none;">彩种开放</th>
   <th style="display: none;">年份</th>
   <th class="h">特码</th>
   <th>正码提前关盘(秒)</th>
   <th>会员提前关盘(秒)</th>
   <th>玩法</th>
  </tr>
  </thead>
  <?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
  <tr>
   <td style="display: none;" class="gid"><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
</td>
   <td class="gname" ><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</td>
   <td class="xsort" style="display: none;"><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['xsort'];?>
</td>
   <td class="thisqishu"><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['thisqishu'];?>
<input type="text" name="thisqishu" style="width: 70px;display: none;" value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['thisqishu'];?>
"><input type="button" class="tsset"  style="display: none;" value="修改"></td>
   <td class="opentimekj"><input type="text" name="opentimekj" style="width: 70px;" value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['opentimekj'];?>
">&nbsp;<input type="button" class="ojset button btn3" value="设置"></td>
   <td class="closetimekj"><input type="text" name="closetimekj" style="width: 70px;" value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['closetimekj'];?>
">&nbsp;<input type="button" class="cjset button btn3" value="设置"></td>
   <td class="autokj" state="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['autokj'];?>
"><label><input type="radio" name="autokj<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" class="autokj" style="color:red" value="0"  <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['autokj'] == 0)) {?>checked="checked"<?php }?>>手动</label>&nbsp;<label><input type="radio" name="autokj<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
"  class="autokj" <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['autokj'] == 1)) {?>checked="checked"<?php }?>value="1" >自动</label></td>
   <td class="autoopenpan" state="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['autoopenpan'];?>
"><label><input type="radio" name="autoopenpan<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" class="autoopenpan" style="color:red" value="0"  <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['autoopenpan'] == 0)) {?>checked="checked"<?php }?>>手动</label>&nbsp;<label><input type="radio" name="autoopenpan<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
"  class="autoopenpan" <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['autoopenpan'] == 1)) {?>checked="checked"<?php }?>value="1" >自动</label></td>
   <td class="panstatus" state="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['panstatus'];?>
"><label><input type="radio" name="panstatus<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" class="panstatus" style="color:red" value="0"  <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['panstatus'] == 0)) {?>checked="checked"<?php }?>>关盘</label>&nbsp;<label><input type="radio" name="panstatus<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
"  class="panstatus" <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['panstatus'] == 1)) {?>checked="checked"<?php }?> value="1" >开盘</label></td>
   <td class="otherstatus" state="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['otherstatus'];?>
" ><label><input type="radio" name="otherstatus<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" class="otherstatus" style="color:red" value="0"  <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['otherstatus'] == 0)) {?>checked="checked"<?php }?>>关盘</label>&nbsp;<label><input type="radio" name="otherstatus<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
"  class="otherstatus" <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['otherstatus'] == 1)) {?>checked="checked"<?php }?> value="1" >开盘</label></td>
   <td class="baostatus" state="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['baostatus'];?>
"><label><input type="radio" name="baostatus<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" class="baostatus" style="color:red" value="0"  <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['baostatus'] == 0)) {?>checked="checked"<?php }?>>开放</label>&nbsp;<label><input type="radio" name="baostatus<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
"  class="baostatus" <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['baostatus'] == 1)) {?>checked="checked"<?php }?> value="1" >关闭</label></td>
   <td class="ifopen" state="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifopen'];?>
" style="display: none;"><label><input type="radio" name="ifopen<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" class="ifopen" style="color:red" value="0"  <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifopen'] == 0)) {?>checked="checked"<?php }?>>关闭</label>&nbsp;<label><input type="radio" name="ifopen<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
"  class="ifopen" <?php if (($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['ifopen'] == 1)) {?>checked="checked"<?php }?> value="1" >开放</label></td>
   <td class="fast"  state="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'];?>
" style="display: none;"><img src='<?php echo $_smarty_tpl->tpl_vars['globalpath']->value;?>
img/<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'];?>
.gif'  class="fast" /></td>
   <td class="thisbml" style="display: none;"  ><input type="text" name="thisbml" style="width: 50px;" value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['thisbml'];?>
">&nbsp;<input type="button" class="tlset button btn3" value="修改"></td>
    <td class="otherclosetime"><input type="text" name="otherclosetime" style="width: 30px;" value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['otherclosetime'];?>
">&nbsp;<input type="button" class="oeset button btn3" value="设置"></td>
   <td class="userclosetime"><input type="text" name="userclosetime" style="width: 30px;" value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['userclosetime'];?>
">&nbsp;<input type="button" class="ueset button btn3" value="设置"></td>
  <td class="flname"><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['flname'];?>
</td>
  </tr>
  <?php
}
}
?>
  </tr>
  
 </table>
</div>
</div>
<div id='test' style="display: none;" ></div>
</body>
</html><?php }
}
