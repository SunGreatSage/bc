<?php
//echo phpinfo();die;
include("./data/config.inc.php");
include("./data/db.php");
include("./global/db.inc.php");
include("./global/session.class.php");
$po = $_SERVER['SERVER_PORT'];

//var_dump($_SESSION);die;
if(substr($po,-2)=='01'){
    header("Location:/Member/Login");
    exit;
}
$url  = $_SERVER['HTTP_HOST'];
$msql->query("select logincode,loginfs from `$tb_config`");
$msql->next_record();
$loginfs = $msql->f('loginfs');
if ($msql->f('logincode') == 1) {
    $mobi = $_REQUEST['mobi'];
    $type = base64_decode(substr($_REQUEST['type'],5));
	$type = substr($type,0,1);
    if ($_SESSION['login'] == 1 & $type != '') {
        if ($loginfs == 'dk') {
            $msql->query("select mdi,udi,adi,hdi,mpo,upo,apo,hpo,wid from `$tb_web` where upo='$po' or apo='$po' or hpo='$po'  limit 1");
        } else {
            $msql->query("select mdi,udi,adi,hdi,mpo,upo,apo,hpo,wid from `$tb_web` where uurl='$url' or aurl='$url' or hurl='$url'  limit 1");
        }
        $msql->next_record();
        $_SESSION['wid'] = $msql->f('wid');
        if ($mobi == 1) {
            $_SESSION['mobi'] = 1;
            $floder           = $msql->f('mdi');
            header("Location:$floder/?com=" . rand(1000, 9999) . "");
            exit;
        } else if ($type == 'u') {
            unset($_SESSION['mobi']);
            $floder = $msql->f('udi');
        } else if ($type == 'a') {
            unset($_SESSION['mobi']);
            $floder = $msql->f('adi');
        } else {
            echo "<script language='javascript'>window.location.href='http://baidu.com/s?wd=abc';</script>";
            exit;
        }
        //echo $floder;exit;
        echo "
<html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head><frameset rows='0,*' frameborder='NO' border='0' framespacing='0'><frame src='about:blank' name='topFrame' scrolling='NO' noresize ><frame src='$floder/?com=" . rand(1000, 9999) . "' name='indexFrame'></frameset><noframes><body></body></noframes></html>";
        exit;
    }
    if ($_POST['code']) {
        $code = $_POST['code'];
        if ($loginfs == 'dk') {
            $msql->query("select mdi,udi,adi,hdi,mpo,upo,apo,hpo,wid,webname,ucode,acode,hcode from `$tb_web` where upo='$po' or apo='$po' or hpo='$po'  limit 1");
        } else {
            $msql->query("select mdi,udi,adi,hdi,murl,uurl,aurl,hurl,wid,webname,ucode,acode,hcode from `$tb_web` where uurl='$url' or aurl='$url' or hurl='$url'  limit 1");
        }
        $msql->next_record();
        if ($loginfs == 'dk') {
            if ($po == $msql->f('hpo') & $code == $msql->f('hcode')) {
                $_SESSION['login'] = 1;
                $_SESSION['wid']   = 100;
                $floder            = $msql->f('hdi');
                echo "
<html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head><frameset rows='0,*' frameborder='NO' border='0' framespacing='0'><frame src='about:blank' name='topFrame' scrolling='NO' noresize ><frame src='$floder/?com=" . rand(1000, 9999) . "' name='indexFrame'></frameset><noframes><body></body></noframes></html>";
                exit;
                
            } else if (($po == $msql->f('apo') & $code == $msql->f('acode')) | ($po == $msql->f('upo') & $code == $msql->f('ucode'))) {
                
                $_SESSION['login'] = 1;
                if (ismobi()) {
                    include("navmobi.php");
                } else {
                    include("navcomputer.php");
                }
                
            } else {
            echo "<script language='javascript'>window.location.href='http://baidu.com/?wd=" . $code . "';</script>";
            exit;
            
        }
        } else if ($loginfs == 'url') {
            if ($url == $msql->f('hurl') & $code == $msql->f('hcode')) {
                $_SESSION['login'] = 1;
                $_SESSION['wid']   = 100;
                $floder            = $msql->f('hdi');
                echo "
<html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head><frameset rows='0,*' frameborder='NO' border='0' framespacing='0'><frame src='about:blank' name='topFrame' scrolling='NO' noresize ><frame src='$floder/?com=" . rand(1000, 9999) . "' name='indexFrame'></frameset><noframes><body></body></noframes></html>";
                exit;
                
            } else if (($url == $msql->f('aurl') & $code == $msql->f('acode')) | ($url == $msql->f('uurl') & $code == $msql->f('ucode')) ) {
                
                $_SESSION['login'] = 1;
                if (ismobi()) {
                    include("navmobi.php");
                } else {
                    include("navcomputer.php");
                }
                
            } else {
            echo "<script language='javascript'>window.location.href='http://baidu.com/?wd=" . $code . "';</script>";
            exit;
            
        }
        } else {
            echo "<script language='javascript'>window.location.href='http://baidu.com/s?wd=" . $code . "';</script>";
            exit;
            
        }
        
    } else {
        include("./nav.php");
    }
} else {
	//echo $loginfs;die;
    if ($loginfs == 'dk') {
		//echo "=="."select mdi,udi,adi,hdi,murl,uurl,aurl,hurl,wid from `$tb_web` where uurl='$url' or aurl='$url' or hurl='$url'  limit 1";die;
        $msql->query("select mdi,udi,adi,hdi,mpo,upo,apo,hpo,wid from `$tb_web` where upo='$po' or apo='$po' or hpo='$po'  limit 1");
    } else {
        $msql->query("select mdi,udi,adi,hdi,murl,uurl,aurl,hurl,wid from `$tb_web` where uurl like '$url%' or aurl='$url' or hurl='$url'  limit 1");
    }
    $msql->next_record();
    if ($loginfs == 'dk') {
        if ($po == $msql->f('upo')) {
            $_SESSION['wid'] = $msql->f('wid');
            
            if (ismobi()) {
                
                $floder           = $msql->f('mdi');;
                $_SESSION['mobi'] = 1;
                header("Location:/Mobile/Login");
                exit;
            } else {
                $floder = $msql->f('udi');
            }
        } else if ($po == $msql->f('apo')) {
            $_SESSION['wid'] = $msql->f('wid');
            $floder          = $msql->f('adi');
        } else if ($po == $msql->f('hpo')) {
            $_SESSION['wid'] = 100;
            $floder          = $msql->f('hdi');
        } else {
            echo "<script>window.location.href='http://google.com';</script>";
            exit;
        }
    } else {
       	if (strstr($msql->f('uurl'), $url)){
            $_SESSION['wid'] = $msql->f('wid');
            
            if (ismobi()) {
                
                $floder           = $msql->f('mdi');;
                $_SESSION['mobi'] = 1;
				$msql->query("select loginfenli from `$tb_config`");
                $msql->next_record();
				$loginfenli = $msql->f('loginfenli');
				if ($loginfenli == '1') {
                header("Location:/Mobile/Login");
				} else {
				header("Location:/Member/Login");
					}
				
				
                exit;
            } else {
                $floder = $msql->f('udi');
            }
        } else if ($url == $msql->f('aurl')) {
            $_SESSION['wid'] = $msql->f('wid');
            $floder          = $msql->f('adi');
        } else if ($url == $msql->f('hurl')) {
            $_SESSION['wid'] = 100;
            $floder          = $msql->f('hdi');
        } else {
            echo "<script>window.location.href='http://google.com';</script>";
            exit;
        }
    }//var_dump($floder);die;
    if ($_REQUEST['wai'] == 'code') {
        header("Location:login.php");
    } else if ($_REQUEST['wai'] == 'guest') {
        header("Location:guest.php");
    } else {
        header("Location:Member/Login");
        /*echo "
<html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head><frameset rows='0,*' frameborder='NO' border='0' framespacing='0'><frame src='about:blank' name='topFrame' scrolling='NO' noresize ><frame src='$floder/?com=" . rand(1000, 9999) . "' name='indexFrame'></frameset><noframes><body></body></noframes></html>";*/
    }
    exit;
    
}
function ismobi()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是www.hnzwz.com移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
    /*
    $agent = $_SERVER['HTTP_USER_AGENT'];  
    if(strpos($agent,"NetFront") || strpos($agent,"iPhone") || strpos($agent,"MIDP-2.0") || strpos($agent,"Opera Mini") || strpos($agent,"UCWEB") || strpos($agent,"Android") || strpos($agent,"Windows CE") || strpos($agent,"SymbianOS")){
    return true;
    }
    return false;*/
}
