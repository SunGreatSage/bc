<?php
//header("P3P: CP=CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR");
define('copyright', 'YINHE');
function addslashes_array($a)
{
    return is_array($a) ? array_map('addslashes_array', $a) : addslashes($a);
}
function acreplace($a)
{
    $b = array('delete', 'insert', 'select', 'update', 'from','drop', 'exists', 'alter', 'create', 'truncate', 'or','`','dual','sleep','DECLARE','char','where','DELAY','undefined','ord','load_file','hex','substring','outfile','exec','chr','--','script','*','#','<','>',';','&','%');
    $c = 0;
    $str = is_array($a) ? array_map('acreplace', $a) : str_ireplace($b, $c, $a);
    return $str;
}
if (!get_magic_quotes_gpc()) {
    if ($_POST) {
        $_POST = addslashes_array($_POST);
    }
    if ($_GET) {
        $_GET = addslashes_array($_GET);
    }
}
if($_COOKIE){
	$_COOKIE=acreplace($_COOKIE);
}
if ($_POST) {
    $_POST = acreplace($_POST);
}
if ($_GET) {
    $_GET = acreplace($_GET);
}

/*discuz防注入代码 开始*/
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
function daddslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = daddslashes($val);
		}
	} else {
		$string = addslashes(str_replace("'","",stripslashes($string)));
	}
	return $string;
}

function do_query_safe($sql,$diyunsafe=array()) {
	$querysafe['dfunction']	= array('load_file','hex','substring','if','ord','char');
	$querysafe['daction']	= array('@','intooutfile','intodumpfile','unionselect','(select', 'unionall', 'uniondistinct');
	//$querysafe['dnote']	= array('/*','*/','#','--','"');
	$querysafe['dlikehex']	= 1;
	
	$sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
	$mark = $clean = '';
	
	$len = strlen($sql);
	$mark = $clean = '';
	for ($i = 0; $i < $len; $i++) {
		$str = $sql[$i];
		switch ($str) {
			case '`':
				if(!$mark) {
					$mark = '`';
					$clean .= $str;
				} elseif ($mark == '`') {
					$mark = '';
				}
				break;
			case '\'':
				if (!$mark) {
					$mark = '\'';
					$clean .= $str;
				} elseif ($mark == '\'') {
					$mark = '';
				}
				break;
			case '/':
				if (empty($mark) && $sql[$i + 1] == '*') {
					$mark = '/*';
					$clean .= $mark;
					$i++;
				} elseif ($mark == '/*' && $sql[$i - 1] == '*') {
					$mark = '';
					$clean .= '*';
				}
				break;
			case '#':
				if (empty($mark)) {
					$mark = $str;
					$clean .= $str;
				}
				break;
			case "\n":
				if ($mark == '#' || $mark == '--') {
					$mark = '';
				}
				break;
			case '-':
				if (empty($mark) && substr($sql, $i, 3) == '-- ') {
					$mark = '-- ';
					$clean .= $mark;
				}
				break;

			default:
				break;
		}
		$clean .= $mark ? '' : $str;
	}

	/*
	if(strpos($clean, '@') !== false) {
		return '-3';
	}
	*/

	$clean = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($clean));
	
	if (is_array($querysafe['dfunction'])) {
		foreach ($querysafe['dfunction'] as $fun) {
			if (strpos($clean, $fun . '(') !== false)
				return '-1';
		}
	}

	if (is_array($querysafe['daction'])) {
		foreach ($querysafe['daction'] as $action) {
			if (strpos($clean, $action) !== false)
				return '-3';
		}
	}

	if ($querysafe['dlikehex'] && strpos($clean, 'like0x')) {
		return '-2';
	}

	if (is_array($querysafe['dnote'])) {
		foreach ($querysafe['dnote'] as $note) {
			if (strpos($clean, $note) !== false)
				return '-4';
		}
	}

	/* 自定义部分 */
	if (is_array($diyunsafe)) {
		foreach ($diyunsafe as $diy) {
			if (strpos($clean, $diy) !== false) return '-5';
		}
	}

	return 1;
}
/*discuz防注入代码 结束*/

$tb_admins       = "x_admins";
$tb_admins_login = "x_admins_login";
$tb_admins_page  = "x_admins_page";
$tb_message      = "x_message";
$tb_news         = "x_news";
$tb_user         = "x_user";
$tb_online       = "x_online";
$tb_user_login   = "x_user_login";
$tb_user_page    = "x_user_page";
$tb_user_edit    = "x_user_edit";
$tb_user         = "x_user";
$tb_faq          = "x_faq";
$tb_bclass       = "x_bclass";
$tb_class        = "x_class";
$tb_play         = "x_play";
$tb_play_user    = "x_play_user";
$tb_zhong        = "x_zhong";
$tb_order        = "x_order";
$tb_kj           = "x_kj";
$tb_kjinfo           = "x_kjinfo";
$tb_kj_m           = "x_kj_m";
$tb_lib          = "x_lib";
$tb_logs          = "x_logs";
$tb_libu      = "x_libu";
$tb_error       = "x_lib_err";
$tb_z            = "x_z";
$tb_c            = "x_c";
$tb_zpan         = "x_zpan";
$tb_points       = "x_points";
$tb_points_bak       = "x_points_bak";
$tb_points_og       = "x_points_og";
$tb_att          = "x_att";
$tb_auto         = "x_auto";
$tb_config       = "x_config";
$tb_session      = "x_session";
$tb_fly          = "x_fly";
$tb_flyinfo          = "x_flyinfo";
$tb_flylist          = "x_flylist";
$tb_sclass       = "x_sclass";
$tb_web       = "x_web";
$tb_game       = "x_game";
$tb_data       = "x_data";
$tb_fastje       = "x_fastje";
$tb_warn = "x_warn";
$tb_peilv       = "x_peilv";
$tb_ctrl       = "x_ctrl";
$tb_gamecs      = "x_gamecs";
$tb_ip      = "ip";
$tb_bank      = "x_bank";
$tb_banknum      = "x_banknum";
$tb_money     = "x_money";
$tb_notices      = "x_notices";
$tb_money_log      = "x_money_log";
$tb_gamezc      = "x_gamezc";
$tb_shui      = "x_shui";
$tb_log = "x_log";
$tb_mailbox       = "x_mailbox";

$tupdatestr = "DROP TRIGGER if exists `tupdatelib` ";
$tupdatecc  = "CREATE TRIGGER `tupdatelib` BEFORE UPDATE ON `x_lib_total` FOR EACH ROW BEGIN if(new.kk<>1) then insert into x_lib_err  values(NULL,old.tid,old.userid,old.qishu,old.dates,old.gid,old.bid,old.sid,old.cid,old.pid,old.abcd,old.ab,old.peilv1,old.peilv2,old.points,old.content,old.je,old.time,old.xtype,old.z,old.prize,old.znum,old.zc0,old.zc1,old.zc2,old.zc3,old.zc4,old.zc5,old.zc6,old.zc7,old.zc8,old.points1,old.points2,old.points3,old.points4,old.points5,old.points6,old.points7,old.points8,old.peilv11,old.peilv12,old.peilv13,old.peilv14,old.peilv15,old.peilv16,old.peilv17,old.peilv18,old.peilv21,old.peilv22,old.peilv23,old.peilv24,old.peilv25,old.peilv26,old.peilv27,old.peilv28,old.uid1,old.uid2,old.uid3,old.uid4,old.uid5,old.uid6,old.uid7,old.uid8,old.flytype,old.sv,old.bz,old.bs,old.ip,old.code,'1','U',new.code, NOW()); set new.je=old.je;set new.prize=old.prize;set new.pid=old.pid; set new.z=old.z; set new.qishu=old.qishu; set new.content=old.content; set new.peilv1=old.peilv1; set new.peilv2=old.peilv2; set new.points=old.points; set new.time=old.time; end if; set new.kk=0;end; ";
$tdeletestr = "DROP TRIGGER if exists `tdeletelib` ";
$tdeletecc  = "CREATE TRIGGER `tdeletelib` AFTER DELETE ON `x_lib_total` FOR EACH ROW BEGIN  if(old.kk<>2) then insert into x_lib_err values(NULL,old.tid,old.userid,old.qishu,old.dates,old.gid,old.bid,old.sid,old.cid,old.pid,old.abcd,old.ab,old.peilv1,old.peilv2,old.points,old.content,old.je,old.time,old.xtype,old.z,old.prize,old.znum,old.zc0,old.zc1,old.zc2,old.zc3,old.zc4,old.zc5,old.zc6,old.zc7,old.zc8,old.points1,old.points2,old.points3,old.points4,old.points5,old.points6,old.points7,old.points8,old.peilv11,old.peilv12,old.peilv13,old.peilv14,old.peilv15,old.peilv16,old.peilv17,old.peilv18,old.peilv21,old.peilv22,old.peilv23,old.peilv24,old.peilv25,old.peilv26,old.peilv27,old.peilv28,old.uid1,old.uid2,old.uid3,old.uid4,old.uid5,old.uid6,old.uid7,old.uid8,old.flytype,old.sv,old.bz,old.bs,old.ip,old.code,'0','D',old.code, NOW()); end if; end; ";