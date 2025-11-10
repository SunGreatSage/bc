<?php
/**********************/
/*                    */
/*  Version : 0.1     */
/*  Author  : WebNone */
/*  Company : Aicom   */
/*                    */
/**********************/

function ebmail( $to, $from, $subject, $message )
{
    global $CONF;
    $owner_m_smtp = $CONF['owner_m_smtp'];
    $owner_m_user = $CONF['owner_m_user'];
    $owner_m_pass = $CONF['owner_m_pass'];
    $owner_m_check = $CONF['owner_m_check'];
    $owner_m_mail = $CONF['owner_m_mail'];
    $ownersys = $CONF['ownersys'];
    //$message = $owner_alert.$message;
	$headers .= "MIME-Version: 1.0 \n"; 
    $headers .= "Content-type: text/html; charset=GBK \n"; 
    $headers .= "from:".iconv('utf-8','gbk','')." <sss@cc.cn>\r\nCc:$mail_cc\r\nBcc:$mail_bcc";
   /* if ( $ownersys == "1" )
    {*/
       if (mail( $to, $subject, $message,$headers)) return "ss";
  /*  }
    else if ( $ownersys == "2" )
    {
       return send22( $to, $from, $subject, $message );
    }	*/
}

function send22( $to, $from, $subject, $message )
{
    global $CONF;
    $owner_m_smtp = $CONF['owner_m_smtp'];
    $owner_m_user = $CONF['owner_m_user'];
    $owner_m_pass = $CONF['owner_m_pass'];
    $owner_m_check = $CONF['owner_m_check'];
    $owner_m_mail = $CONF['owner_m_mail'];
    $ownersys = $CONF['ownersys'];
    $smtp = $owner_m_smtp;
    $check = $owner_m_check;
    if ( $check )
    {
        $username = $owner_m_user;
        $password = $owner_m_pass;
    }
    $s_from = $owner_m_mail;
    $fp = fsockopen( $smtp, 25, $errno, $errstr, 20 );
    if ( !$fp )
    {
        return "联接服务器失败".( 52 );
    }
    set_socket_blocking( $fp, true );
    $lastmessage = fgets( $fp, 512 );
    if ( substr( $lastmessage, 0, 3 ) != 220 )
    {
        return "错误信息:".$lastmessage.( 56 );
    }
    $yourname = "YOURNAME";
    if ( $check == "1" )
    {
        $lastact = "EHLO ".$yourname."\r\n";
    }
    else
    {
        $lastact = "HELO ".$yourname."\r\n";
    }
    fputs( $fp, $lastact );
    $lastmessage == fgets( $fp, 512 );
    if ( substr( $lastmessage, 0, 3 ) != 220 )
    {
        return "错误信息{$lastmessage}".( 65 );
    }
    while ( true )
    {
        $lastmessage = fgets( $fp, 512 );
        if ( substr( $lastmessage, 3, 1 ) != "-" || empty( $lastmessage ) )
        {
            break;
        }
    }
    if ( $check == "1" )
    {
        $lastact = "AUTH LOGIN"."\r\n";
        fputs( $fp, $lastact );
        $lastmessage = fgets( $fp, 512 );
        if ( substr( $lastmessage, 0, 3 ) != 334 )
        {
            return "错误信息{$lastmessage}".( 79 );
        }
        $lastact = base64_encode( $username )."\r\n";
        fputs( $fp, $lastact );
        $lastmessage = fgets( $fp, 512 );
        if ( substr( $lastmessage, 0, 3 ) != 334 )
        {
            return "错误信息{$lastmessage}".( 84 );
        }
        $lastact = base64_encode( $password )."\r\n";
        fputs( $fp, $lastact );
        $lastmessage = fgets( $fp, 512 );
        if ( substr( $lastmessage, 0, 3 ) != "235" )
        {
            return "错误信息{$lastmessage}".( 89 );
        }
    }
    $lastact = "MAIL FROM: <".$s_from.">\r\n";
    fputs( $fp, $lastact );
    $lastmessage = fgets( $fp, 512 );
    if ( substr( $lastmessage, 0, 3 ) != 250 )
    {
        return "错误信息{$lastmessage}".( 96 );
    }
    $lastact = "RCPT TO: <".$to.">\r\n";
    fputs( $fp, $lastact );
    $lastmessage = fgets( $fp, 512 );
    if ( substr( $lastmessage, 0, 3 ) != 250 )
    {
        return "错误信息{$lastmessage}".( 102 );
    }
    $lastact = "DATA\r\n";
    fputs( $fp, $lastact );
    $lastmessage = fgets( $fp, 512 );
    if ( substr( $lastmessage, 0, 3 ) != 354 )
    {
        return "错误信息{$lastmessage}".( 108 );
    }
    $head = "Subject: {$subject}\r\n";
    $message = $head."\r\n".$message;
    $head = "From: {$from}\r\n";
    $message = $head.$message;
    $head = "To: {$to}\r\n";
    $message = 'Content-Type:text/html;charset=gb2312'.$head.$message;
    $message .= "\r\n.\r\n";
    fputs( $fp, $message );
    $lastact = "QUIT\r\n";
    fputs( $fp, $lastace );
    fclose( $fp );
    return "ss";
}

?>