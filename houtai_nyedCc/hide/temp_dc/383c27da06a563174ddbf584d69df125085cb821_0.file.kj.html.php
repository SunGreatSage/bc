<?php
/* Smarty version 3.1.48, created on 2024-05-07 18:19:45
  from '/www/wwwroot/lhc/houtai/templates/default/hide/kj.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_663a00414e9b39_98868206',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '383c27da06a563174ddbf584d69df125085cb821' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/kj.html',
      1 => 1682708288,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header2.html' => 1,
  ),
),false)) {
function content_663a00414e9b39_98868206 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header2.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link href="/default/css/g_HK6.css" rel="stylesheet" type="text/css" />
<link href="/default/css/control.css" rel="stylesheet" />
<link href="/default/css/Site.css" rel="stylesheet" />
<link href="/default/css/jquery.loadmask.css" rel="stylesheet" />
<?php echo '<script'; ?>
 src="/js/sitetool.js?v=004"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="/js/jquery.loadmask.js?v=004"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"><?php echo '</script'; ?>
>
<style>
.editkj{position:absolute;width:1260px;background:#fff;border:2px solid #000;display:none}
.pikj{position:absolute;width:630px;background:#fff;border:2px solid #000;display:none;z-index:100}
.table_ball tbody input[type="text"] {
    border: #cbd2da 1px solid;
    background: url(/default/css/Skins/blue/images/text_input.gif) #fff repeat-x left top !important;
	       width: 100px;
    height: 20px
;
}
a {
    color: #3e34fc;
}
</style>
</head>
<body  class="skin_blue">

<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='kj';<?php echo '</script'; ?>
>
<div class="jt-container">
	<div class="top_info">
		<span class="title">开奖设定</span><div class="center"><input type="checkbox" class="ze" value="1" />只显示有注单记录&nbsp;&nbsp;共<label style="color: red;" class="rcount chu"></label>期记录 <select class="psize">
 <option value="30">每页30条</option>
 <option value="50" selected>每页50条</option>
 <option value="100">每页100条</option>
 <option value="120">每页120条</option>
 <option value="250">每页250条</option>
 <option value="500">每页500条</option>
 </select><input  type="hidden" value="1" class="page" /></div><span class="right">&nbsp;<a class="back" onclick="javascript:history.back(-1);" href="javascript:void(0);">返回</a></span>
	</div>
	 <div class="s_main" >  
	 <div id="p_para" class="contents param_panel input_panel tab_panel">

                        <table class="layout" templatecode="HK6">
                            <tbody><tr>
                                <td height="30" colspan="2" align="left">
								<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
                                        <?php if ($_smarty_tpl->tpl_vars['game2']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'] == 0) {?><span loto="MCHK6" group="HK6" class="g<?php echo $_smarty_tpl->tpl_vars['game2']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
 group_tap" gid='<?php echo $_smarty_tpl->tpl_vars['game2']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
'><?php echo $_smarty_tpl->tpl_vars['game2']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</span><?php }?>
								 <?php
}
}
?>
                                    
                                </td>
                            </tr>
							</tbody>
							</table>
							 </div>
 <table class="data_table" style="display: none;">
   <thead>
    <tr>
      <th >彩种选择</th><th>当前期</th><th>自动开奖</th><th>自动开关盘</th><th>特码状态</th><th>正码状态</th>
      </tr></thead>
      <tr> <td><?php echo $_smarty_tpl->tpl_vars['game']->value[0]['gname'];?>
<select class="game" style="display: none;">
       <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['game']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
         <option value="<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gid'];?>
" fast='<?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['fast'];?>
'><?php echo $_smarty_tpl->tpl_vars['game']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['gname'];?>
</option>
         <?php
}
}
?>
       </select></td>  <td><label><?php echo $_smarty_tpl->tpl_vars['game']->value[0]['thisqishu'];?>
</label></td> <td><img  src='<?php echo $_smarty_tpl->tpl_vars['globalpath']->value;?>
img/<?php echo $_smarty_tpl->tpl_vars['game']->value[0]['autokj'];?>
.gif'  id="autokj"  class="status"  /></td><td><img src='<?php echo $_smarty_tpl->tpl_vars['globalpath']->value;?>
img/<?php echo $_smarty_tpl->tpl_vars['game']->value[0]['autoopenpan'];?>
.gif' id="autoopenpan" class="status"  /></td><td><img  src='<?php echo $_smarty_tpl->tpl_vars['globalpath']->value;?>
img/<?php echo $_smarty_tpl->tpl_vars['game']->value[0]['panstatus'];?>
.gif'  class="status" id="panstatus" /></td><td><img  src='<?php echo $_smarty_tpl->tpl_vars['globalpath']->value;?>
img/<?php echo $_smarty_tpl->tpl_vars['game']->value[0]['otherstatus'];?>
.gif'  class="status"  id="otherstatus" /></td> </tr>
    </tr>  
 </table>

 <?php if ($_smarty_tpl->tpl_vars['game']->value[0]['fast'] == 0) {?>
   <table style="border-collapse:unset;" class="data_table list  addkj table_ball">
   <thead>
   <!--tr class=""><th colspan="5">开奖设定</th></tr-->
   	        <tr>
      <th>期数</th><th>开盘时间</th><th>关盘时间</th><th>开奖时间</th><th>功能</th></th><td rowspan="2" style="display: none;"> 
      &nbsp;&nbsp;日期范围:<input type="text" class="pdate txt5 date" value="<?php echo $_smarty_tpl->tpl_vars['sdate']->value[10];?>
"  /><BR />
      <input type="button" value="批量增加期数" class="padd"  /></td>
     </tr>
	   </thead>
    <tr><td><input type="text" class='qishu txt2' value="<?php echo $_smarty_tpl->tpl_vars['game']->value[0]['thisqishu']+1;?>
"   /></td>
         <td><input type="text" class="opendate txt5 date" value="<?php echo $_smarty_tpl->tpl_vars['current_date']->value;?>
"  />&nbsp;<input type="text" class="opentime txt6" value="<?php echo $_smarty_tpl->tpl_vars['game']->value[0]['opentimekj'];?>
"  /></td>
          <td><input type="text" class="closedate txt5 date" value="<?php echo $_smarty_tpl->tpl_vars['current_date']->value;?>
" />&nbsp;<input type="text" class="closetime txt6" value="<?php echo $_smarty_tpl->tpl_vars['game']->value[0]['closetimekj'];?>
" /></td>
      <td><input type="text" value="<?php echo $_smarty_tpl->tpl_vars['current_date']->value;?>
"  class="kjdate txt5 date" />&nbsp;<input type="text" value="21:35:00"  class="kjtime txt6" /></td>
	   <td><input type="button" value="添加期数" class="add btn7"   /></td>
     </tr>
  </table>
  <?php }?>
  
  
   
   <table  style="border-collapse:unset;margin: 0 auto;margin-top: 10px;" class="data_table cmd" style="height: 50px;">
     <thead>
	 <tr><th>开奖管理</th></tr>
	   </thead>
   <tr><td>&nbsp;&nbsp;日期范围：<input class='txt5 date' id="start"  value='<?php echo $_smarty_tpl->tpl_vars['sdate']->value[10];?>
' size='11' />
    &nbsp;—&nbsp;
    <input class='txt5 date' id="end" name='end'  value='<?php echo $_smarty_tpl->tpl_vars['sdate']->value[10];?>
' size='11' />
    <input type="button" class="s"  d=1 value="今天" />
    <input type="button" class="s"  d=2 value="昨天" />
    <input type="button" class="s"  d=3 value="本星期" />
    <input type="button" class="s"  d=4 value="上星期" />
    <input type="button" class="s"  d=5 value="本月" />
    <input type="button" class="s"  d=6 value="上月" />
    <input type="button" value='删除日期数据' style="width: 90px;" class="deldate btn2" t=1 />
    <input type="button" value='删除指定日期之前' style='display: none;' class="deldate" t=2 />
    <span> &nbsp;勾选项操作: &nbsp;</span>
    <input type="button" value='关闭报表' class="changebaostatus btn2" action='0' /> &nbsp;<input type="button" value='开放报表' class="changebaostatus btn2"  action='1' />&nbsp;<input type="button" value="删除注单" class="delbao btn2" />&nbsp;<input type="button" value="删除所有" class="delall btn2" />
    <input type="button" value='更新长龙' class="updatelong"  action='1' style='display: none;' />
    <input type="button" style="display: none;" value='期数去重复' class="qsqc"  date='<?php echo $_smarty_tpl->tpl_vars['sdate']->value[10];?>
' />
    </td></tr>
 <!--tr><td><input type="button" value="更新开奖" class="updatekj" /><input type="button" value='批量开奖' class="pikjcmd"  /><input type="button" value='批量结算' class="pijs"  />
 
 </td>
 
  <td style="width:42%"></td>
  
  </tr-->
 </table>
  
  <table style="border-collapse:unset;margin: 0 auto;" class="data_table list kjjr table_ball"></table>
  
  <!--table style="border-collapse:unset;" class="data_table editkj table_ball" >
 
    <tr><th>期数</th><th>开盘时间</th><th>关盘时间</th><th>开奖时间</th><th>号码</th>
    <th rowspan="2"><input type="button" value="修改" class="editkjsend"  /><BR /><input type="button" value="关闭" class="editkjclose"  /></th></tr>
    
    <tr><td><label></label></td><td><input type="text" class="txt31 eopentime" /></td><td><input type="text" class="txt31 eclosetime" /></td><td><input type="text" class="txt31 ekjtime" /></td>
    <td class="kjhm"></td></tr>
 </table-->
 <div id="betDetail" class="hmain01 ui-dialog-content ui-widget-content editkj" style="display: none;width: auto; min-height: 453px; max-height: 633px; height: auto;">
    <table class="table_ball" align="center">
        <thead>
            <tr class="td0" align="center">
                <th nowrap="nowrap">开奖期数</th>
                <th nowrap="nowrap">开盘时间</th>
                <th nowrap="nowrap">关盘时间</th>
                <th nowrap="nowrap">开奖时间</th>
                <th nowrap="nowrap">开奖号码</th>
               <th rowspan="2" style="display: none;" ><input type="button" value="修改" class="editkjsend"  /><BR /><input type="button" value="关闭" class="editkjclose"  />
            </tr>
        </thead>
        <tbody>
		<tr><td><label></label></td><td><input type="text" class="txt31 eopentime" style="width: 120px;" /></td><td><input type="text" class="txt31 eclosetime" style="width: 120px;"  /></td><td><input type="text" class="txt31 ekjtime" style="width: 120px;" /></td>
    <td class="kjhm"></td></tr>
		</tbody>
    </table>
</div>

</div>
<div id="betDetail2" class="hmain01 ui-dialog-content ui-widget-content" style="display: none;width: auto; min-height: 453px; max-height: 633px; height: auto;">
    <table class="table_ball xxtb" align="center">
        <thead>
            <tr class="td0 bt" align="center">
                <th nowrap="nowrap" id="tdDanhaoTime">彩种</th>
                <th nowrap="nowrap">期数</th>
                <th nowrap="nowrap" id="tdDanhaoQishu">下注内容</th>
                <th nowrap="nowrap">下注/占成金额</th>
                <th nowrap="nowrap" class="money">@赔率</th>
				 <th nowrap="nowrap">退水</th>
                <th nowrap="nowrap">账号</th>
                <th nowrap="nowrap">时间</th>
            </tr>
        </thead>
        <!--tbody>
        </tbody-->
    </table>

</div>

<!--table class="data_table list xxtb">
<thead>
 <tr class="bt">
 <th style="width:80px">彩种</th>
  <th>期數</th>  
  <th>類別</th>
  <th><a href="javascript:void(0);" class="je">金額<img src="<?php echo $_smarty_tpl->tpl_vars['globalpath']->value;?>
img/down.gif" s='up' /></th>
  <th>赔率</th>
  <th>退水</th>
  <th>會員</th>
  <th><a href="javascript:void(0);" class="time">時間<img src="<?php echo $_smarty_tpl->tpl_vars['globalpath']->value;?>
img/down.gif" s='down' /></th>
 </tr></thead>
</table-->

</div>
<input type="hidden" class='sort' orderby='time' sorttype='DESC' page='1' xtype='2' tztype='0' con='' />
<iframe id='longfrm' style="display:none;"></iframe>
<div id='test' style="clear:both"></div>
<?php echo '<script'; ?>
 language="javascript">
sdate=new Array();
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sdate']->value, 'i', false, 'key');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
sdate[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
";
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
var fenlei = <?php echo $_smarty_tpl->tpl_vars['config']->value['fenlei'];?>
;
var ngid = <?php echo $_smarty_tpl->tpl_vars['config']->value['fenlei'];?>
;
var gid=<?php echo $_smarty_tpl->tpl_vars['gid']->value;?>
;
<?php echo '</script'; ?>
>
</body>
</html>
<?php }
}
