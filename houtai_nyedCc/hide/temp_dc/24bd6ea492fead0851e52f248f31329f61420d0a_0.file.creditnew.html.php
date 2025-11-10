<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:33:31
  from '/www/wwwroot/lhc/houtai/templates/default/hide/creditnew.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f56bad4800_09703608',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '24bd6ea492fead0851e52f248f31329f61420d0a' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/creditnew.html',
      1 => 1681982822,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_6639f56bad4800_09703608 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link href="/default/css/info.css?v=1" rel="stylesheet" type="text/css" />
<style type="text/css">
        .host, .link { width: 200px; }
        .code { width: 70px; }
        .marR10 { margin-right: 10px; }
    </style>
</head>
<body  class="L_HK6" id="topbody">
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';
var js=1;var sss='creditnew';
<?php echo '</script'; ?>
>
<div class="jt-container">
    <div class="top_info">
        <span class="title">用户资料</span>
    </div>
    <div class="s_main">
        <table class="list table_ball user_info">
            <thead><tr><th colspan="2">基本信息</th></tr></thead>
            <tbody>
                <tr><th>账号</th><td class="t_left"><?php echo strtolower($_smarty_tpl->tpl_vars['username']->value);?>
</td></tr>
                <tr><th>额度类型</th><td class="t_left">信用额度</td></tr>
                        <tr>
                            <th>额度</th>
                            <td class="t_left"><font color="red">(公司无限额度)</font></td>
                        </tr>



            </tbody>
        </table>

        <div class="info_body">


            <div class="sub_w2" style="margin-bottom:10px;">
                <div class="right_btns">
                    <span class="left_arrow" id="toleft2"><a class="bx-prev disabled" ></a></span><span class="right_arrow" id="toright2"><a class="bx-next disabled" ></a></span>
                </div>

                <div class="page-tabs">
                    <div class="tab_con2 clear" id="scroll_div2">
                        <ul id="sliderLottery">
                                <li>
				<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
if ($_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'] == 0) {?><a href="javascript:void(0)" class="g<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" gid='<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
'><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</a><?php }
}
}
?>
                                </li>

                        </ul>
                    </div>
                </div>
            </div>

                <div id="con_six_1" class="hover" style="">
                    <div id="tabs-MCHK6">
                        <table class="list table data_table table_ball">
                            <thead>
                                <tr>
                                    <th style="width:120px" nowrap="nowrap">玩法</th>
									 <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
    <th  class="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
 p"><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
盘退水</th>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                                    <th>单注最低</th>
                                    <th>单注金额</th>
                                    <th>单期金额</th>
                                </tr>
                            </thead>
                            <tbody>
							
							<tbody>
   <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?> 
   <?php $_smarty_tpl->_assignInScope('pan', $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['pan']);?>
   <?php
$__section_j_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['pan']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_j_2_total = $__section_j_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_j'] = new Smarty_Variable(array());
if ($__section_j_2_total !== 0) {
for ($__section_j_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] = 0; $__section_j_2_iteration <= $__section_j_2_total; $__section_j_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']++){
?>
   <tr gid='<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
' class="gametr g<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
">
    <th class="<?php echo $_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)]['class'];?>
" gid='<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
'><?php echo $_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)]['name'];?>
</th>

   <?php if ($_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)]['abcd'] == 1) {?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['span']->value, 'k');
$_smarty_tpl->tpl_vars['k']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value) {
$_smarty_tpl->tpl_vars['k']->do_else = false;
?>   
           <?php $_smarty_tpl->_assignInScope('m', strtolower($_smarty_tpl->tpl_vars['k']->value));?>
           <?php if ($_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)]['ab'] == 1) {?>
               <?php $_smarty_tpl->_assignInScope('tmp1', "points".((string)$_smarty_tpl->tpl_vars['m']->value)."a");?>
               <?php $_smarty_tpl->_assignInScope('tmp2', "points".((string)$_smarty_tpl->tpl_vars['m']->value)."b");?>
               <td><?php echo $_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)][$_smarty_tpl->tpl_vars['tmp1']->value];?>
/<?php echo $_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)][$_smarty_tpl->tpl_vars['tmp2']->value];?>
%</td>
           <?php } else { ?>
                <?php $_smarty_tpl->_assignInScope('tmp', "points".((string)$_smarty_tpl->tpl_vars['m']->value)."0");?>
                <td><?php echo $_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)][$_smarty_tpl->tpl_vars['tmp']->value];?>
%</td>
            <?php }?> 
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php } else { ?>
         <?php $_smarty_tpl->_assignInScope('tmp', count($_smarty_tpl->tpl_vars['span']->value));?>
         <?php $_smarty_tpl->_assignInScope('tmp2', $_smarty_tpl->tpl_vars['tmp']->value*2);?>
         <td colspan="<?php echo $_smarty_tpl->tpl_vars['tmp2']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)]['pointsa0'];?>
%</td>
    <?php }?>
    <td><?php echo $_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)]['minje'];?>
</td> 
    <td><?php echo $_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)]['maxje'];?>
</td>
    <td><?php echo $_smarty_tpl->tpl_vars['pan']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_j']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_j']->value['index'] : null)]['cmaxje'];?>
</td>
    
    </tr>
   <?php
}
}
?>
   <?php
}
}
?>

</tbody>
                                      
										
										
                                        
                            </tbody>
                        </table>
                    </div>
                </div>

        </div>

    </div>
</div>
</body>
</html><?php }
}
