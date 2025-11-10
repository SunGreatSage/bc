<?php 
$check = 0;

if ($_SESSION['uid'] != '' & $_SESSION['check'] == md5($config['allpass'] . $_SESSION['uid'])) {
    $check = 1;
}


$hurl = $config['hurl'];
if ($check == 0) {
    sessiondel();
    echo "<script>top.window.location.href='/';</script>";
    exit;
}

if ($_SESSION['uid'] == 9) {
    sessiondel();
    echo "<script>top.window.location.href='/';</script>";
    exit;
}

$adminid = $_SESSION['uid'];


if ($_REQUEST['logout'] == 'yes') {
    $msql->query("delete from  `$tb_online` where userid='$adminid'");
    sessiondel();
    echo "<script>top.window.location.href='/';</script>";
    exit();
}

if ($_SESSION['hides'] != 1 & $config['ifopen'] == 0) {
    sessiondel();
    echo "<script>top.window.location.href='/';</script>";
    exit;
}


$xpage = $_SERVER["SCRIPT_NAME"];
$xpage = substr($xpage, strrpos($xpage, '/') + 1);
$xpage = substr($xpage, 0, strlen($xpage) - 4);

$hspage = array(
    "sys",
	"check",
	"message"
	
);
$hpage = array(
    "admins",
    "game"
	
);
$mpage = array(
    "caopao",
	"sysconfig",
	"xxtz2",
	"nows",
	"history",
	"webconfig",
    "fly"
);
if (in_array($xpage, $hspage) & $_SESSION['hides'] != 1)
    {
        exit;
    }

 
if (in_array($xpage, $mpage) & $_SESSION['admin'] != 1)
    {
        exit;
    }

if ($_SESSION['admin'] != 1) {
	$pages = array("longs","new","myscript","admin","changepass","changepass2","top","now","best_plan");
	$msql->query("select xpage from `$tb_admins_page` where adminid='$adminid' and xpage='$xpage' and ifok=1");
    $msql->next_record();
    if((strpos('['.$xpage,$msql->f('xpage'))<0 | $msql->f('xpage')=='') &  !in_array($xpage,$pages) ) exit;
}


if ($_SESSION['hides'] != 1) {
    $msql->query("select passcode,savetime from `$tb_online` where userid='$adminid'");
    $msql->next_record();
    if ($msql->f('passcode') != $_SESSION['passcode']) {
        sessiondel();
        echo "<script>alert('登录超时!');parent.window.location.href='../';</script>";
        exit;
    }
    
    $time = time();
    if ($time - $msql->f('savetime') > 60) {
		if($xpage!='top' & $xpage!='myscript' )
        $msql->query("update `$tb_online` set savetime=NOW(),page='$xpage' where userid='$adminid'");
    }
}


$userid = 99999999;


$tpl->assign('globalpath', $globalpath);
$tpl->assign('rkey', $config['rkey']);
$tpl->assign('mulu', "/".$config['hdi']."/");