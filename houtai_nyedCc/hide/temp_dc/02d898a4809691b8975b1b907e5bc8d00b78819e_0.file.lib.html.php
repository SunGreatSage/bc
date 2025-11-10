<?php
/* Smarty version 3.1.48, created on 2024-05-07 17:29:44
  from '/www/wwwroot/lhc/houtai/templates/default/hide/lib.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6639f4880d3621_14931500',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '02d898a4809691b8975b1b907e5bc8d00b78819e' => 
    array (
      0 => '/www/wwwroot/lhc/houtai/templates/default/hide/lib.html',
      1 => 1693589146,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
  ),
),false)) {
function content_6639f4880d3621_14931500 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:header.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link href="/default/css/g_HK6.css" rel="stylesheet" />
  <?php echo '<script'; ?>
 type="text/javascript" src="/default/js/function.js"> <?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 language="javascript" src="/js/jquery-ui.min.js"><?php echo '</script'; ?>
>
  <link href="/default/css/jquery.loadmask.css" rel="stylesheet" />
  <?php echo '<script'; ?>
 src="/js/jquery.loadmask.js?v=004"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript">
function json_encode_js(aaa) {
	function je(str) {
		var a = [],
			i = 0;
		var pcs = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		for (; i < str.length; i++) {
			if (pcs.indexOf(str[i]) == -1) a[i] = "\\u" + ("0000" + str.charCodeAt(i).toString(16)).slice(-4);
			else a[i] = str[i]
		}
		return a.join("")
	}
	var i, s, a, aa = [];
	if (typeof(aaa) != "object") {
		alert("ERROR json");
		return
	}
	for (i in aaa) {
		s = aaa[i];
		a = '"' + je(i) + '":';
		if (typeof(s) == 'object') {
			a += json_encode_js(s)
		} else {
			if (typeof(s) == 'string') a += '"' + je(s) + '"';
			else if (typeof(s) == 'number') a += s
		}
		aa[aa.length] = a
	}
	return "{" + aa.join(",") + "}"
}
function getResult(num, n) {
	return Math.round(num * Math.pow(10, n)) / Math.pow(10, n)
}
function getresult(num, n) {
	return num.toString().replace(new RegExp("^(\\-?\\d*\\.?\\d{0," + n + "})(\\d*)$"), "$1") + 0
}
function strlen(sString) {
	var sStr, iCount, i, strTemp;
	iCount = 0;
	sStr = sString.split("");
	for (i = 0; i < sStr.length; i++) {
		strTemp = escape(sStr[i]);
		if (strTemp.indexOf("%u", 0) == -1) {
			iCount = iCount + 1
		} else {
			iCount = iCount + 2
		}
	}
	return iCount
}
function rhtml(str) {
	return str.match(/<a\b[^>]*>[\s\S]*?<\/a>/ig)
}
function getm(val) {
	if (ngid == 101 | ngid == 111 | ngid == 113 | ngid == 115 | ngid == 108 | ngid == 109 | ngid == 110 | ngid == 112 ) ngid = 101;
	if (ngid == 121 | ngid == 123 | ngid == 125 | ngid == 127 | ngid == 129 ) ngid = 105;
	if(ngid==133) ngid=103;
	var sl = sma['g' + ngid].length;
	for (i = 0; i < sl; i++) {
		if (val == sma['g' + ngid][i]['name']) {
			return sma['g' + ngid][i]['ma']
		}
	}
}
<?php echo '</script'; ?>
>
<style>

body{font-size:13px;overflow:auto}
.hide{display:none}
.show{display:block}

.pset td{x-height:30px;}
.pset .btns:hover{}
.pset input.click{background:#C66}

.w1002{width:994px;}
.w500{width:500px;}

td.ext{height:100px;}
th.bo{background:#FC6}

.butb{width:150px;position:absolute;background:#fff;display:none;}
.butbwai{width:150px;position:absolute;background:#fff;display:none;}



.onepeilvtb{width:120px;position:absolute;background:#fff;display:none;border:2px solid #000;z-index:3}

a.red{color:#D50000}
img{border:none}

tr.z1{background:#69F}
tr.z3{background:#EFE9ED}

.pset td{x-height:30px;}
.pset .btns:hover{background:#FC6}
.pset input.click{background:#C66}

.libs {margin-top:5px;display:none}

.tinfo td.warn{background: rgb(235, 244, 255) !IMPORTANT;}
.tinfo th.warn{background: rgb(235, 244, 255) !IMPORTANT;}

.duopl{width:700px;position:absolute;1}
.duopl .m{width:50px;}
.duopl label.pl{color: rgb(255, 0, 0);}
.duopl th{background:##F9FAF1 !important}

input.red{color:#D50000}
input.blue{color:blue}
input.green{color:green}
td.tiao{text-align:center;width:55px !important;}
.jt-container .bar_tool {
    color: #5b3600;
    margin-top: 1px;
    padding: 0 0;
    min-width: 1499px;
    overflow-wrap: break-word;
}
.bar_tool .right {
    float: right;
    margin-right: -60px;
}
.pset, input {
    margin: 1px 2px;
}
 label {
    cursor: pointer;
}
.lockmode
{
       background: rgb(235, 244, 255) !IMPORTANT;
}
.current {
    list-style: none;
    display: inline-block;
    text-decoration: none;
    margin: 0 2px 0 0;
    padding: 2px 6px;
	    border: 1px solid #2161b3;
   color: #fff !important;
    background-color: #2161b3;
}
.page {
   list-style: none;
    display: inline-block;
    border: 1px solid #ccc;
    text-decoration: none;
    margin: 0 2px 0 0;
    padding: 2px 6px;
}
<?php if ($_smarty_tpl->tpl_vars['panstatus']->value != 1) {?>
td.duoname{
       background: #f6f6f6 !IMPORTANT;
}
th.duoname{
       background: #f6f6f6 !IMPORTANT;
}
td.s{
       background: #f6f6f6 !IMPORTANT;
}
td.t{
       background: #f6f6f6 !IMPORTANT;
}
td.winlost{
       background: #f6f6f6 !IMPORTANT;
}
td.bu{
       background: #f6f6f6 !IMPORTANT;
}
.libs td{
       background: #f6f6f6 !IMPORTANT;
}
.libs th{
       background: #f6f6f6 !IMPORTANT;
}
<?php }?>
th.m{
       background: #f6f6f6 !IMPORTANT;
}
td.c{
       background: #f6f6f6 !IMPORTANT;
	   font-weight: bold;
}
.libspages span
{padding: 1px 5px;
    border: 1px solid #ddd;
    color: #2161b3 !important;
    background: #f9f9f9;
	font-weight: normal;
	    cursor: pointer;
}
.libspages span.red
{
    border: 1px solid #2161b3 !important;
    color: #fff !important;
    background: #2161b3 !important;
}
.small
{
width: 50px;
}
</style>
</head>
<body>
<?php echo '<script'; ?>
 id=myjs language="javascript">var mulu='<?php echo $_smarty_tpl->tpl_vars['mulu']->value;?>
';var js=1;var sss='lib';<?php echo '</script'; ?>
>
<div class="jt-container s_main">
<div class="bar_tool">
            <span id="drawNumber" class="title">
			<?php echo $_smarty_tpl->tpl_vars['upqishu']->value;?>
期
            </span>
			<select style="margin: 0 2px;margin-top: 5px;;display: none;"  id=qishu><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['qishu']->value, 'i');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?><option value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
期</option><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select>
            <div class="period">
			<span style="display: none;" class="time0" ><?php echo $_smarty_tpl->tpl_vars['pantime']->value;?>
</span>
			<span style="display: none;" class="panstatus" s='<?php echo $_smarty_tpl->tpl_vars['panstatus']->value;?>
'></span>
                    <span id="gameName"></span>
                <label class="cdDraw" id="cdDraw" style="color: #333;font-weight: normal;" ><label>距封盘：</label><span class="bresult cdDraw">00:00</span></label>

            </div>

            <div class="right">
				<div id="drawLotterInfo">
                    <label class="draw_number"><label id="spanQishu" m='<?php echo $_smarty_tpl->tpl_vars['upkj']->value;?>
' sx='<?php echo $_smarty_tpl->tpl_vars['upsx']->value;?>
' ><?php echo $_smarty_tpl->tpl_vars['upqishu']->value;?>
</label><span>期开奖:&nbsp;</span></label>
                    <label class="table_ball balls" style="border-top: 0; border-left: 0; border-right: 0; background-color: transparent;">
 
 
  <span id="lot_Result" class="draw_number  openResult ball_his ball_HK6"></span>
 
</label>
                </div>
				<label style="float: left;"><input type="checkbox" style="float: left;margin-top: 10px;" value="1" id=zhenghe />总项盈亏</label>&nbsp;
				<label style="float: left;"><input type="checkbox" style="float: left;margin-top: 10px;" value="1" id=lump />显示总额(虚注)</label>&nbsp;
                <select style="max-width:85px;" id='userid' layer='<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
'>
                    <option value="">请选择下线</option>
     
     
<?php
$__section_i_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['topuser']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_0_total = $__section_i_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_0_total !== 0) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= $__section_i_0_total; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>

     
     <option value="<?php echo $_smarty_tpl->tpl_vars['topuser']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['userid'];?>
"><?php echo $_smarty_tpl->tpl_vars['topuser']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['username'];?>
</option>
     
     
<?php
}
}
?>
                </select>&nbsp;
				<select id='fly' class="hide" ><option value="2">外补</option></select>&nbsp;
                <select id="ab">
                        <option value="0">全部</option>
                        <option value="A">特码A</option>
                        <option value="B">特码B</option>
                    </select>&nbsp;
				<select id="abcd">
     <option value="0">全部</option>
                                <option value="A">A盘</option>
                                <option value="B">B盘</option>
                                <option value="C">C盘</option>
                                <option value="D">D盘</option>
                                </select>&nbsp; 
                <select id="xsort">
                     <option value="ks" selected>按盈亏排序</option>
	 <option value="name">按号码排序</option>
     <option value="zje">按总投排序</option>
     <option value="zc">按实占排序</option>
     <option value="zs">按注数排序</option>
                </select>&nbsp;
                <input name="button" type="button" class="query btn4" id="btnRefresh" value="刷新">
                &nbsp;
				<input name="button" type="button"  style="float: left;" class="query btn4" id="zanting" value="暂停">
				 &nbsp;
				<select id="refreshInteval">
     <option value="10">10秒</option>
     <option value="15">15秒</option>
     <option value="20">20秒</option>
     <option value="30">30秒</option>
     <option value="45">45秒</option>
     <option value="60" selected>60秒</option>
    </select>
  <label id="cdRefresh" class="time" ></label>
            </div>
        </div>
 <div class="bar_tool">
 <span class="title">
    <input type="button" class="btn3 btnf hide" value="赔率設置" id='pset' />
	&nbsp;&nbsp;
	<font class="font_bold">控盘功能</font>
	<input type="button" class="plmode btn3 btnf plmodeclick" value="当前赔率" v='2' />
    <input type="button" class="plmode btn3 btnf"  style="width: 140px;" value="查看默认赔率|停押设置" v='4' />    
    <input type="button" class="btn3 btnf moren" style="width: 120px;" value="设置当前赔率为初始"  ac='writemoren' />
    <input type="button" class="btn3 btnf moren" style="width: 80px;" value="恢复默认赔率"  ac='resetmoren' />
    <input type="button" class="btn7 btnf" style="border: none;" id="quickodds" value="展开快捷赔率▼"  />
	  </span>
	</div>
	
<table class='tinfo wd100 pset' style="border: 1px solid #b8c3d0;background: #ebf5ff;display: none;" >
 <TR>
   <td style="text-align:left;"><input type="button" value="單" class="btns btnf"/><input type="button" value="雙" class="btns btnf"/><input type="button" value="大" class="btns btnf"/><input type="button" value="小" class="btns btnf"/><input type="button" value="合單" class="btns btnf"/><input type="button" value="合雙" class="btns btnf"/><input type="button" value="尾大" class="btns btnf"/><input type="button" value="尾小" class="btns btnf"/><input type="button" value="家畜" class="btns btnf"/><input type="button" value="野獸" class="btns btnf"/><input type="button" value="前" class="btns btnf"/><input type="button" value="後" class="btns btnf"/><input type="button" value="紅" class="btns btnf red"/><input type="button" value="藍" class="btns btnf blue"/><input type="button" value="綠" class="btns btnf green"/><input type="button" value="紅單" class="btns btnf red"/><input type="button" value="紅雙" class="btns btnf red"/><input type="button" value="紅大" class="btns btnf red"/><input type="button" value="紅小" class="btns btnf red"/><input type="button" value="藍單" class="btns btnf blue"/><input type="button" value="藍雙" class="btns btnf blue"/><input type="button" value="藍大" class="btns btnf blue"/><input type="button" value="藍小" class="btns btnf blue"/><input type="button" value="綠單" class="btns btnf green"/><input type="button" value="綠雙" class="btns btnf green"/><input type="button" value="綠大" class="btns btnf green"/><input type="button" value="綠小" class="btns btnf green"/><input type="button" value="0尾" class="btns btnf"/><input type="button" value="1尾" class="btns btnf"/><input type="button" value="2尾" class="btns btnf"/><input type="button" value="3尾" class="btns btnf"/><input type="button" value="4尾" class="btns btnf"/><input type="button" value="5尾" class="btns btnf"/><input type="button" value="6尾" class="btns btnf"/><input type="button" value="7尾" class="btns btnf"/><input type="button" value="8尾" class="btns btnf"/><input type="button" value="9尾" class="btns btnf"/><br/><input type="button" value="鼠" class="btns btnf"/><input type="button" value="牛" class="btns btnf"/><input type="button" value="虎" class="btns btnf"/><input type="button" value="兔" class="btns btnf"/><input type="button" value="龍" class="btns btnf"/><input type="button" value="蛇" class="btns btnf"/><input type="button" value="馬" class="btns btnf"/><input type="button" value="羊" class="btns btnf"/><input type="button" value="猴" class="btns btnf"/><input type="button" value="雞" class="btns btnf"/><input type="button" value="狗" class="btns btnf"/><input type="button" value="豬" class="btns btnf"/><input type="button" value="0头" class="btns btnf"/><input type="button" value="1头" class="btns btnf"/><input type="button" value="2头" class="btns btnf"/><input type="button" value="3头" class="btns btnf"/><input type="button" value="4头" class="btns btnf"/><input type="button" value="前3" class="btns btnf"/><input type="button" value="前5" class="btns btnf"/><input type="button" value="前10" class="btns btnf botton"/><input type="button" value="全部" class="btns btnf"/><input type="button" value="反选" class="red btns btnf" />
&nbsp;操作：&nbsp;
<select id='psettype'>
	<option value="0">減</option>
	<option value="1">加</option>
</select>
&nbsp;调赔值：&nbsp;
<select id='psetattvalue'>
	<option value="0.01">0.01</option>
	<option value="0.02">0.02</option>
	<option value="0.05">0.05</option>
	<option value="0.1" selected>0.1</option>
	<option value="0.2">0.2</option>
	<option value="0.5">0.5</option>
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="5">5</option>
</select>
<input type="button" class="btn4 btnf" value="调整" id='psetatt'/>&nbsp;统一值：&nbsp;<input type="text" style="width:35px;" value="42.5" id='psetvalue'/><input type="button" class="btn4 btnf" value="送出" id='psetsend'/><input type="button" class="btn4 btnf" value="保存" id='psetpost'/><input type="button" class="btn3 btnf" value="重置" id='psetcancel'/><input type="checkbox" value="1" class='tmode' /><span>取同</span><input type="checkbox" value="2" class='tmode' /><span>取总</span>



   </td>

 </TR>
</table>
<ul class="menu6" style="display: none;">
    <?php $_smarty_tpl->_assignInScope('firstDropdown', true);?>
    <?php
$__section_i_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['s']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_1_total = $__section_i_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_1_total !== 0) {
for ($__section_i_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_1_iteration <= $__section_i_1_total; $__section_i_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
        <?php if ($_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'] == '正1特' || $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'] == '正2特' || $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'] == '正3特' || $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'] == '正4特' || $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'] == '正5特' || $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'] == '正6特') {?>
            <?php if ($_smarty_tpl->tpl_vars['firstDropdown']->value) {?>
			<ul class="dropdown-menu" style="font-size: 0px;">
                <li class="dropdown-menu-sub-indicator2" style="font-size: 12px;display: inline-block;cursor: pointer;">
                    <a style="display: inline" class="cur" target="frame">正特码<span class="dropdown-menu-sub-indicator">»</span></a>
                    <ul class="dropdown-menu dropdown-menu-shadow2" style="visibility: hidden; display: none;">
                        <?php $_smarty_tpl->_assignInScope('firstDropdown', false);?>
            <?php }?>
                        <li><a href="javascript:void(0);" sid='<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['sid'];?>
' sname='<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
' class="n"><?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</a></li>
        <?php } else { ?>
            <?php if (!$_smarty_tpl->tpl_vars['firstDropdown']->value) {?>
                    </ul>
                </li>
				</ul>
                <?php $_smarty_tpl->_assignInScope('firstDropdown', true);?>
            <?php }?>
            <li><a href="javascript:void(0);" sid='<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['sid'];?>
' sname='<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
' class="n"><?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</a></li>
        <?php }?>
    <?php
}
}
?>
    <?php if (!$_smarty_tpl->tpl_vars['firstDropdown']->value) {?>
                </ul>
            </li>
    <?php }?>
</ul>




<table class="wd100 tinfo now" style="display: none;" >
  <Tr> 
<?php
$__section_i_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['s']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_2_total = $__section_i_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_2_total !== 0) {
for ($__section_i_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_2_iteration <= $__section_i_2_total; $__section_i_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
<th class="n<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['sid'];?>
"><?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
</th><td sid='<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['sid'];?>
' sname='<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
' class="n nx<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['sid'];?>
"></td>
  <?php if (($_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['i'] == 7 || $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['i'] == 15)) {?></Tr><tr><?php }
}
}
?>
<TD colspan="4" style="text-align:left;width:400px;">占成/總投/走飛:<label class="zc">0</label>/<label class="zong">0</label>/<label  class="fly">0</label></TD>
</Tr>
</table>

<div class="sider-left floatR" id="menuleft">
    <div id="leftMenu_lhc" class="now">
        
       
       <?php
$__section_i_3_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['s']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_i_3_total = $__section_i_3_loop;
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if ($__section_i_3_total !== 0) {
for ($__section_i_3_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_3_iteration <= $__section_i_3_total; $__section_i_3_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
      <h3 data-collapse-summary="" aria-expanded="ture"><a href="javascript:void(0);" >
            <ul>
                <li class="n<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['sid'];?>
">
                    <a href="javascript:void(0);"  sid='<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['sid'];?>
' sname='<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
'  class="n" ><?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['name'];?>
：<font  class="font_g n nx<?php echo $_smarty_tpl->tpl_vars['s']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)]['sid'];?>
"  >0</font></a>
                </li>
            </ul>
        </a></h3>
        <?php
}
}
?>

    </div>

</div>
<div class="toggle floatR"><div class="sider-toggle"><span title="收缩">切换&nbsp;</span></div></div>


  
<div class="main" id="main">
<div id="intimeSheet">
<div id="lhcIntimesheet">
<div id="mainType" class="table_small">
<table class="wd100 tinfo lib w1330 ">

</table>
<table class="data_table table_ball list tinfo libs">
<tr><th colspan="3" class="libspages">单页显示列表数：<span>50</span>&nbsp;&nbsp;<span class="red">100</span>&nbsp;&nbsp;<span>200</span>&nbsp;&nbsp;<span>300</span></th><th colspan="4">选择页数：<SELECT class="pages"></SELECT>&nbsp;页</th></tr>
<thead>
<tr><th>序 号</th><th>投注内容</th><th class='gpl hide'>赔率</th><th>笔数</th><th>总额</th><th>占成</th><th>输赢</th><th>补货</th></tr></thead>
</table>
</div>
</div>
</div>
<div id="qickBuhuo" class="data_footer control floatD" style="margin-top: 5px; position: static; bottom: 0px;display: none;">

    <div style="width: 800px; margin: 0 auto;" class="marT10">
        <span class="step"></span><span>手补：</span>
        <input id="txtBuhuoAmount" class="inp1" name="BuhuoAmount" style="width: 100px;" >
        &nbsp;&nbsp;
     <input name="button" type="button" class="btn_box" id="ztmks" value=" 设置 " style="cursor: pointer;">
        &nbsp;&nbsp;
        <input href="javascript:void(0)" class="query button" id='pfly' type="button" value=" 批量补货 " style="cursor: pointer;">&nbsp;
    </div>

</div>
</div>
</div>


<input type="hidden" class='sort' orderby='time' sorttype='DESC' page='1' xtype='2' con='' />


<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-draggable ui-resizable zczd" tabindex="-1" role="dialog" aria-describedby="betDetail" aria-labelledby="ui-id-2" style="position: absolute; height: auto; width: 780px; top: 62px; left: 368.5px; display: block; z-index: 101;display: none;"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle"><span id="ui-id-2" class="ui-dialog-title">投注列表</span><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close zdclose" role="button" title="Close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">Close</span></button></div><div id="betDetail" class="hmain01 ui-dialog-content ui-widget-content" style="width: auto; min-height: 453px; max-height: 633px; height: auto;">
    <table class="table_ball xxtb" align="center">
        <tbody id="betDetailList123"></tbody>
    </table>
   
</div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div></div>

<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-draggable ui-resizable budd" tabindex="-1" role="dialog" aria-describedby="betDetail" aria-labelledby="ui-id-2" style="position: absolute; height: auto; width: 780px; top: 62px; left: 368.5px; display: block; z-index: 101;display: none;"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle"><span id="ui-id-2" class="ui-dialog-title">补货列表</span><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close buclose" role="button" title="Close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">Close</span></button></div><div id="betDetail" class="hmain01 ui-dialog-content ui-widget-content" style="width: auto; min-height: 453px; max-height: 633px; height: auto;">
    <table class="table_ball flytb" align="center">
        <tbody id="betDetailList123"></tbody>
    </table>
   
</div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div></div>






<div class="ui-widget-overlay ui-front" style="z-index: 100;display: none;"></div>
 <div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-dialog-buttons ui-draggable ui-resizable buui" tabindex="-1" role="dialog" aria-describedby="buhuoDetail" aria-labelledby="ui-id-1" style="position: absolute; height: auto; width: 600px; top: 0px; left: 458.5px; display: none;">
   <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle">
    <span id="ui-id-1" class="ui-dialog-title">补货投注列表</span>
    <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close buclose" role="button" title="Close" style="height: 28px;"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">Close</span></button>
   </div>
   <div id="buhuoDetail" class="hmain01 ui-dialog-content ui-widget-content" style="width: auto; min-height: 0px; max-height: none; height: 416px;"> 
    <table class="tinfo sendtb table_ball" align="center" style="margin-top:5px">

    </table> 
   </div>
   <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
    <div class="ui-dialog-buttonset">
     <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only qr" role="button" style="height: 28px;"><span class="ui-button-text">确定</span></button>
     <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only cancel" role="button" style="height: 28px;"><span class="ui-button-text">取消</span></button>
    </div>
   </div>
  </div>
  
<iframe id="downfastfrm" style="display:none;" ></iframe>
<?php echo '<script'; ?>
 language="javascript" >
layername= new Array();
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['layername']->value, 'i', false, 'key');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
layername[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
";
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
var layer =<?php echo $_smarty_tpl->tpl_vars['layer']->value;?>
 ;
var style = '<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
';

var ngid= <?php echo $_smarty_tpl->tpl_vars['gid']->value;?>
;
var ma=new Array();
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['ma']->value, 'i', false, 'key');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
 <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['i']->value, 'ii', false, 'k');
$_smarty_tpl->tpl_vars['ii']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['ii']->value) {
$_smarty_tpl->tpl_vars['ii']->do_else = false;
?>
    ma['<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'] = new Array(<?php echo $_smarty_tpl->tpl_vars['ii']->value;?>
); 
 <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>	
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
echo '</script'; ?>
>
<div id='test'></div>
</body>
</html><?php }
}
