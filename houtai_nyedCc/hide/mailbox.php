<?php
include '../data/comm.inc.php';
include '../data/myadminvar.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/adminfunc.php';
include '../include.php';
include './checklogin.php';
switch ($_REQUEST['xtype']) {
        case "show":
	    $game = getgamecs($userid);
        $game = getgamename($game);
		$tpl->assign("game", $game);
		$msql->query("select * from `$tb_mailbox`");
		$msql->next_record();
		$mailbox['smtpserver']    = $msql->f("smtpserver");
        $mailbox['smtpusername']    = $msql->f("smtpusername");
        $mailbox['smtppassword']       = $msql->f("smtppassword");
        $mailbox['smtppost']        = $msql->f("smtppost");
        $mailbox['smtpencryption']      = $msql->f("smtpencryption");
        $mailbox['sender']     = $msql->f("sender");
		$mailbox['sendername']     = $msql->f("sendername");
        $mailbox['recipient']   = $msql->f("recipient");
		$mailbox['recipientname']   = $msql->f("recipientname");
		$mailbox['unsettlement']   = $msql->f("unsettlement");
		$mailbox['settled']   = $msql->f("settled");
		$mailbox['utime']   = $msql->f("utime");
		$mailbox['stime']   = $msql->f("stime");
		$mailbox['smtptitle']   = $msql->f("smtptitle");
		$mailbox['smtpcontent']   = $msql->f("smtpcontent");
		$tpl->assign("mailbox", $mailbox);
		$tpl->assign("config", $config);
        $tpl->display("mailbox.html");
        break;
		case "smtpupdate":
		$mailbox = isnum($_POST['mailbox']);
		$unsettlement = isnum($_POST['unsettlement']);
		$settled = isnum($_POST['settled']);
		$gid = $_POST['gid'];
		$smtpserver = $_POST['smtpserver'];
        $smtpusername = $_POST['smtpusername'];
        $smtppassword = $_POST['smtppassword'];
        $smtppost = $_POST['smtppost'];
        $smtpencryption = $_POST['smtpencryption'];
        $sender = $_POST['sender'];
        $recipient = $_POST['recipient'];
		$sendername = $_POST['sendername'];
        $recipientname = $_POST['recipientname'];
		$utime = $_POST['utime'];
		$stime = $_POST['stime'];
		$smtptitle = $_POST['smtptitle'];
		$smtpcontent = $_POST['smtpcontent'];
		$selectedGids = explode(',', $_POST['gid']);
		$sql = "UPDATE $tb_mailbox SET smtpserver='$smtpserver', smtpusername='$smtpusername', smtppassword='$smtppassword', smtppost='$smtppost', smtpencryption='$smtpencryption', sender='$sender', recipient='$recipient', sendername='$sendername', recipientname='$recipientname', unsettlement='$unsettlement', settled='$settled', utime='$utime', stime='$stime', smtptitle='$smtptitle', smtpcontent='$smtpcontent'";
        $msql->query($sql);
		$sql = "UPDATE $tb_config SET mailbox='$mailbox'";
        $msql->query($sql);
		// 将所有游戏的 gamemail 设置为 0
$sql = "UPDATE $tb_game SET gamemail=0";
$msql->query($sql);

// 将选中的游戏的 gamemail 设置为 1
foreach ($selectedGids as $gidValue) {
  $sql = "UPDATE $tb_game SET gamemail=1 WHERE gid='$gidValue'";
  $msql->query($sql);
}
		echo 1;
		break;
}