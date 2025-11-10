<?php
set_time_limit(0);
$_SERVER['REMOTE_ADDR']='1.1.1.1';
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');
include '../data/config.inc.php';
include '../data/db.php';
include '../global/db.inc.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/adminfunc.php';
include '../func/js.php';
include '../func/search.php';
include "../func/self.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

$msql->query("select * from `$tb_mailbox`");
$msql->next_record();
$smtpserver= $msql->f("smtpserver");
$smtpusername= $msql->f("smtpusername");
$smtppassword= $msql->f("smtppassword");
$smtppost= $msql->f("smtppost");
$smtpencryption= $msql->f("smtpencryption");
$sender= $msql->f("sender");
$recipient= $msql->f("recipient");
$unsettlement= $msql->f("unsettlement");
$settled= $msql->f("settled");
$utime= $msql->f("utime");
$stime= $msql->f("stime"); 
$sendername= $msql->f("sendername");
$recipientname= $msql->f("recipientname");
$smtptitle= $msql->f("smtptitle");
$smtpcontent= $msql->f("smtpcontent");

$msql->query("select mailbox from `$tb_config`");
$msql->next_record();
$mailbox = $msql->f("mailbox"); 
if ($smtpencryption == 0) {
    $SMTPSecure = 'TLS';
} elseif ($smtpencryption == 1) {
    $SMTPSecure = 'SSL';
} elseif ($smtpencryption == 3) {
    $SMTPSecure = 'STARTTLS';
} else {
    $SMTPSecure = 'unknown';
}
$lastRunFile = "settled.txt";

/*function parameter($str, $arr)
{
    $str = str_replace('{期数}', $arr[0], $str);
    $str = str_replace('{公司名称}', $arr[1], $str);
    $str = str_replace('{开盘时间}', $arr[2], $str);
    $str = str_replace('{关盘时间}', $arr[3], $str);
    $str = str_replace('{开奖时间}', $arr[4], $str);
    return $str;
}*/

if ($mailbox == 1 && $settled == 1) {
$targetTimestamp = strtotime($stime);
$currentTimestamp = strtotime(date('H:i:s'));
$currentDay = date('Y-m-d');

if (file_exists($lastRunFile)) {
    $lastRun = file_get_contents($lastRunFile);
} else {
    $lastRun = "";
}
if ($currentTimestamp >= $targetTimestamp && $currentDay !== $lastRun) {
	
    // 在此处插入要运行的代码
    // ...
	$gids = [100,200,300]; // 添加 gid=100 的支持
	foreach ($gids as $gid) {
	    
		$layer = 0;
		$msql->query("select thisqishu, gamemail, gname from `{$tb_game}` where gid='{$gid}' ");
		$msql->next_record();
		$qishu =$msql->f('thisqishu');
		$gamemail = $msql->f('gamemail');
		$gname = $msql->f('gname'); // 获取 gname 字段内容
        $time  = "zd" . date("mdHis");
		$msql->query("select js from `{$tb_kj}` where gid='{$gid}' and qishu='{$qishu}' ");
		$msql->next_record();
	    $status =$msql->f('js');
		if ($gamemail == 1 && $status == 1) {
		$msql->query("select gid,gname,thisqishu,fenlei from `{$tb_game}` where gid='{$gid}' order by gid");
		    ob_start();
        $td1 = '<td width=\'120\'>';
        $td2 = '</td>';
        echo '<table border=1><tr>';
        echo $td1, '序号', $td2;
        echo $td1, '彩别', $td2;
        echo $td1, '期数', $td2;
        echo $td1, '会员', $td2;
        echo $td1, '下单时间', $td2;
        echo $td1, '盘类', $td2;
        echo $td1, '类别', $td2;
        echo $td1, '内容', $td2;
        echo $td1, '金额', $td2;
        echo $td1, '赔率', $td2;
        echo $td1, '退水', $td2;
		echo $td1, '中奖', $td2;
		echo $td1, '结果', $td2;
        echo '</tr>';
        $i = 1;
        while ($msql->next_record()) {
            $gid   = $msql->f('gid');            
			 $fsql->query("select * from `{$tb_lib}` where gid='{$gid}' and qishu='{$qishu}' order by time desc");
            while ($fsql->next_record()) {
                echo '<tr>';
                echo $td1, $i, $td2;
                echo $td1, $msql->f('gname'), $td2;
                echo $td1, $qishu, $td2;
                echo $td1, transu($fsql->f('userid')), $td2;
                echo $td1;
                echo substr($fsql->f('time'),-8);
                echo $td2;
                echo $td1, $fsql->f('abcd'), $td2;
                if ($tmp['b' . $fsql->f('gid') . $fsql->f('bid')] == '') {
                    $tmp['b' . $fsql->f('gid') . $fsql->f('bid')] = transb8('name', $fsql->f('bid'), $fsql->f('gid'));
                }
                if ($tmp['s' . $fsql->f('gid') . $fsql->f('sid')] == '') {
                    $tmp['s' . $fsql->f('gid') . $fsql->f('sid')] = transs8('name', $fsql->f('sid'), $fsql->f('gid'));
                }
                if ($tmp['c' . $fsql->f('gid') . $fsql->f('cid')] == '') {
                    $tmp['c' . $fsql->f('gid') . $fsql->f('cid')] = transc8('name', $fsql->f('cid'), $fsql->f('gid'));
                }
                if ($tmp['p' . $fsql->f('gid') . $fsql->f('pid')] == '') {
                    $tmp['p' . $fsql->f('gid') . $fsql->f('pid')] = transp8('name', $fsql->f('pid'), $fsql->f('gid'));
                }
                
                $wf = wfuser($msql->f("fenlei"), $tmp['b' . $fsql->f('gid') . $fsql->f('bid')], $tmp['s' . $fsql->f('gid') . $fsql->f('sid')], $tmp['c' . $fsql->f('gid') . $fsql->f('cid')], $tmp['p' . $fsql->f('gid') . $fsql->f('pid')]);
                
                echo $td1, $wf, $td2;
                echo $td1, $fsql->f('content'), $td2;
                echo $td1, $fsql->f('je'), $td2;
                echo $td1, $fsql->f('peilv1'), $td2;
				$upoints = $fsql->f('points');
            if (in_array($fsql->f('userid'), $poarr)) {
                if ($fsql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $upoints -= 10;
                }
            }
                echo $td1, $upoints, $td2;
				$uz = $fsql->f('z');
                $result = ($uz == 1) ? '中' : '不中';
                echo $td1, $result, $td2;
				if ($uz == 1) {
				$ulement = (float)($fsql->f('peilv1')*$fsql->f('je') - $fsql->f('je')*(1-$fsql->f('points')/100));
				} else {
				$ulement = (float)(0-$fsql->f('je')*(1-$fsql->f('points')/100));
				}
				echo $td1, $ulement, $td2;
                echo '</tr>';
                $i++;
            }
        }
        echo '</table>';
		var_dump("测试");
		    $tableContent = ob_get_clean();
            $mail = new PHPMailer(true);

try {
    // 电子邮件设置
    $mail->SMTPDebug = 0; // 调试输出级别 (0 = 关闭调试输出, 1 = 客户端消息, 2 = 客户端和服务器消息)
    $mail->isSMTP(); // 使用 SMTP
    $mail->Host = $smtpserver; // SMTP 服务器地址
    $mail->SMTPAuth = true; // 使用 SMTP 验证
    $mail->Username = $smtpusername; // 发件人邮箱
    $mail->Password = $smtppassword; // 发件人邮箱密码
    $mail->SMTPSecure = $SMTPSecure; // 加密方式
    $mail->Port = $smtppost; // TCP 端口 
    // 收件人和发件人
	
	$sendername = str_replace('{彩种名称}', $gname, $sendername);
    $sendername = str_replace('{期数}', $qishu, $sendername);
     $mail->setFrom($sender, $sendername);
	$recipientname = str_replace('{彩种名称}', $gname, $recipientname);
    $recipientname = str_replace('{期数}', $qishu, $recipientname);
    $mail->addAddress($recipient, $recipientname);

    // 附件
    $directory = "bak"; // 将文件保存在这个目录中
    $filename = "{$directory}/{$time}.xls";
    file_put_contents($filename, $tableContent); // 将表格内容保存为文件
     $mail->addAttachment($filename); // 添加附件

    // 邮件内容
	$mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
	$smtptitle = str_replace('{彩种名称}', $gname, $smtptitle);
    $smtptitle = str_replace('{期数}', $qishu, $smtptitle);
    $mail->Subject = $smtptitle; // 
	$smtpcontent = str_replace('{彩种名称}', $gname, $smtpcontent);
    $smtpcontent = str_replace('{期数}', $qishu, $smtpcontent);
	$mail->Body    = $smtpcontent;
    //$mail->Body    = '请查看附件中的 Excel 表格为:'. $qishu . '期未结算注单';

    // 发送邮件
    $mail->send();
    echo '邮件已发送。';
 
    // 删除临时文件
    unlink($filename);
} catch (Exception $e) {
    echo "邮件发送失败。错误信息：{$mail->ErrorInfo}";
}
}
}
  // 将当前日期保存为上次运行的日期
     file_put_contents($lastRunFile, $currentDay);
} else {
    echo "失效";
}


}