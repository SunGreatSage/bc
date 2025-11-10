<?php 
if($_SESSION['ip']!=getip()){
    sessiondela();
    echo "<script>alert('登陆超时!');top.window.location.href='/';</script>"; 
    exit;
}

$check=0;

if($_SESSION['auid2']!='' && $_SESSION['acheck']==md5($config['allpass'].$_SESSION['auid2']) ){
	 $check=1;
}

$aurl = $config['aurl'];
if($check==0 | $config['ifopen']==0 | $config['ifopens']==0){
	 sessiondela();
     echo "<script>top.window.location.href='/';</script>"; 
	 exit;
}

$userid2=$_SESSION['auid2'];
$userid=$_SESSION['auid'];

if($_REQUEST['logout']=='yes'){
	//var_dump($userid2);die;
	 $msql->query("delete from `$tb_online` where userid='$userid2'");
	 $msql->query("update `$tb_user` set online=0 where userid='$userid2'");
     sessiondela();
     echo "<script>top.window.location.href='/';</script>"; 
	 exit();
}

$msql->query("select passcode,savetime,page from `$tb_online` where userid='$userid2' and xtype=1");
$msql->next_record();
if($msql->f('passcode')!=$_SESSION['apasscode']){
	 sessiondela();
     echo "<script>alert('登录超时!');top.window.location.href='/';</script>"; 
	 exit;
}
$oldpage=$msql->f('page');

$time = time();

    $xpage=$_SERVER["SCRIPT_NAME"];
    $xpage=substr($xpage,strrpos($xpage,'/')+1);
    $xpage=substr($xpage,0,strlen($xpage)-4);

if($_SESSION['atype']!=1){
	
    $pages = array("longs","news","myscript","admin","changepass","changepass2","top","rule","online","money");
    $msql->query("select xpage from `$tb_user_page` where userid='$userid2' and xpage='$xpage' and ifok=1");
    $msql->next_record();
     if((strpos('['.$xpage,$msql->f('xpage'))<0 | $msql->f('xpage')=='') &  !in_array($xpage,$pages) ) exit;
}

if( $_POST['xtype']!='getopen' & $_POST['xtype']!='getnews' & $_POST['xtype']!='getnow'  & $_POST['xtype']!='admin'  & $xpage!='myscript'){
	if($xpage!=$oldpage | $time-$msql->f('savetime')>90){
        $msql->query("update `$tb_online` set savetime=NOW(),page='$xpage' where userid='$userid2'");
	}
}


$tpl->assign('globalpath',$globalpath);
$tpl->assign('rkey', $config['rkey']);
$tpl->assign('mulu', "/".$config['adi']."/");
