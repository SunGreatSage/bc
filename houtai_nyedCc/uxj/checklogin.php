<?php 
if($_SESSION['ip']!=getip()){
    sessiondelu();
    echo "<script>alert('登陆超时!');top.window.location.href='/';</script>"; 
    exit;
}

$check=0;
if($_SESSION['uuid']!='' && $_SESSION['ucheck']==md5($config['allpass'].$_SESSION['uuid']) ){
	 $check=1;
}


$uurl = $config['uurl'];
if($check==0 | $config['ifopen']==0 | $config['ifopens']==0){
	 sessiondelu();
     echo "<script>top.window.location.href='/';</script>"; 
	 exit;
}

$userid=$_SESSION['uuid'];

if($_REQUEST['logout']=='yes'){
	 $msql->query("delete from `$tb_online` where userid='$userid'");
	 $msql->query("update `$tb_user` set online=0 where userid='$userid2'");
     sessiondelu();

        echo "<script>top.window.location.href='/';</script>"; 
	 
	 exit();
}





$msql->query("select passcode,savetime from `$tb_online` where userid='$userid' and xtype=2");
$msql->next_record();
if($msql->f('passcode')!=$_SESSION['upasscode']){
	 sessiondelu();
     echo "<script>alert('登录超时!');top.window.location.href='/';</script>"; 
	 exit;
}
$time = time();
    $xpage=$_SERVER["SCRIPT_NAME"];
    $xpage=substr($xpage,strrpos($xpage,'/')+1);
    $xpage=substr($xpage,0,strlen($xpage)-4);
if($time-$msql->f('savetime')>90 & $_POST['xtype']!='getopen' & $_POST['xtype']!='getnews' & $_POST['xtype']!='getatt' & $xpage!='myscript'){
    $msql->query("update `$tb_online` set savetime=NOW(),page='$xpage' where userid='$userid'");
	
}

$tpl->assign('globalpath',$globalpath);
$tpl->assign('rkey', $config['rkey']);
$tpl->assign('skin', $_SESSION['skin']);
$tpl->assign('mulu', "/".$config['udi']."/");
