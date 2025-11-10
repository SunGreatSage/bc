<?php
	exit;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/Exception.php';
require './PHPMailer/PHPMailer.php';
require './PHPMailer/SMTP.php';

// 将表格内容保存到变量中
ob_start();
// ... 输出表格的代码 ...
$tableContent = ob_get_clean();

$mail = new PHPMailer(true);

try {
    // 服务器设置
    $mail->isSMTP();                                      // 使用 SMTP
    $mail->Host       = 'smtp.office365.com';               // 指定主SMTP服务器
    $mail->SMTPAuth   = true;                             // 启用 SMTP 身份验证
    $mail->Username   = 'ztxinjiapodd@outlook.com';         // SMTP 用户名（您的电子邮件地址）
    $mail->Password   = 'AAss1122.+';            // SMTP 密码
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // 启用 TLS 加密，`PHPMailer::ENCRYPTION_SMTPS` 也可用
    $mail->Port       = 587;                              // TCP 端口连接到

    // 收件人
    $mail->setFrom('ztxinjiapodd@outlook.com', 'Mailer');
    $mail->addAddress('zhongsheng666@proton.me', 'Recipient Name'); // 添加收件人

    // 附件
    $mail->addStringAttachment($tableContent, 'table.xls', 'base64', 'application/vnd.ms-excel');

    // 内容
    $mail->isHTML(true);                                  // 设置电子邮件格式为 HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
