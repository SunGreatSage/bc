<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');

include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');

switch ($_REQUEST['xtype']) {
    case "show":
	    include("../global/Iplocation_Class.php");
	    if ($_SESSION['uid'] != 10000) 
        { 
            $noSupers = 'and adminid != "10000" ';
        }
        $sql = "SELECT *,lastloginip as ip FROM `$tb_admins` where ifhide=0 $noSupers ORDER BY id";
        $msql->query($sql);
        $i    = 0;
        $data = array();
        $page = array();
        $fsql->query("select * from `$tb_admins_page` where ifhide=0  group by xpage order by sortx ");
        $page[0][0] = '权限设置';
        $j          = 1;
        while ($fsql->next_record()) {
            $page[0][$j] = $fsql->f('pagename');
            $j++;
        }
        
        while ($msql->next_record()) {
            $data[$i]['id']            = $msql->f('id');
            $data[$i]['adminid']       = $msql->f('adminid');
            $data[$i]['adminname']     = $msql->f('adminname');
			$data[$i]['regtime']   =  $msql->f('regtime');
			if ($config['iprecord']==1){
            $data[$i]['lastloginip']   = $msql->f('ip');
			} else {
			$data[$i]['lastloginip']   = "***";
			}
            $data[$i]['lastlogintime'] = $msql->f('lastlogintime');
            $data[$i]['logintimes']    = $msql->f('logintimes');
			if ($config['iprecord']==1){
			$data[$i]['lastloginfrom'] = mb_convert_encoding($ips->getaddress($msql->f('ip')),'utf-8','GBK');
			} else {
			$data[$i]['lastloginfrom'] = "***";
				}
            if ($msql->f('passtime') != '') {
                $data[$i]['passtime'] = $msql->f('passtime');
            } else {
                $data[$i]['passtime'] = '';
            }
            $page[$i + 1][0] = $msql->f('adminname');
            $fsql->query("select * from `$tb_admins_page` where adminid='" . $msql->f('adminid') . "' and ifhide=0  order by sortx ");
            $j = 1;
            while ($fsql->next_record()) {
                //$page[$i + 1][$j] = "<img src='../img/" . $fsql->f('ifok') . '.gif' . "' page='" . $fsql->f('xpage') . "' aid='" . $msql->f('adminid') . "' />";
				$page[$i + 1][$j] = "<input type='checkbox' value='" . $fsql->f('ifok') . "' page='" . $fsql->f('xpage') . "' aid='" . $msql->f('adminid') . "'    />";
                $j++;
            }
            $i++;
        }
        $tpl->assign('page', $page);
        $tpl->assign('data', $data);
        $tpl->display('caopan.html');
        break;
    
    case "addoredit":
        $action    = $_POST['action'];
        $aid       = $_POST['aid'];
        $adminname = $_POST['username'];
        $pass1     = $_POST['pass1'];
        $pass2     = $_POST['pass2'];
        if ($pass1 != $pass2) {
            echo 0;
            exit;
        }
        $sql = "";
        
        $modiuser  = $userid;
        $modiuser2 = $adminid;
        $moditime  = time();
        $modiip    = getip();
        
        $pass1 = md5($_POST['pass1'] . $config['upass']);
        if ($action == 'add') {
            $msql->query("select id from `$tb_admins` where adminname='$adminname'");
            $msql->next_record();
            if ($msql->f('id') == '') {
                $adminid = setupid($tb_admins, 'adminid') + rand(1, 9);
                $time    = time();
                $sql .= "insert into `$tb_admins` set adminname='$adminname',adminpass='$pass1',adminid='$adminid',ifhide=0,regtime=NOW()";
                
            }
        } else if ($action == 'edit') {
            //echo 1;
            //exit;
            $sql .= " update `$tb_admins` set adminpass='$pass1' where adminid='$aid'";
            
        }
        if ($sql != '') {
            $msql->query($sql);
            if ($action == 'add') {
                $msql->query("select * from `$tb_admins_page` where 1=1 group by xpage");
                while ($msql->next_record()) {
                    $fsql->query("insert into `$tb_admins_page` set xpage='" . $msql->f('xpage') . "',pagename='" . $msql->f('pagename') . "',adminid='$adminid',ifok='0',sortx='" . $msql->f('sortx') . "',ifhide='".$msql->f('ifhide')."'");
                }
				userchange("新增",$adminid);
            }else{
			    userchange("修改密码",$aid);
			}
            echo 1;
        }
        
        break;
    case "sc":
        $aid = $_POST['aid'];
        $msql->query("delete from `$tb_admins` where adminid='$aid'");
        userchange("删除",$aid);        
        $msql->query("delete from `$tb_admins_page` where adminid='$aid'");
        echo 1;
        break;
    
    case "uppage":
        $aid = $_POST['aid'];
        if ($aid == 10000)
            exit;
        $page = $_POST['page'];
        $msql->query("update `$tb_admins_page` set ifok=if(ifok=0,1,0) where adminid='$aid' and xpage='$page'");
        
        userchange("更改权限",$aid); 
        
        $msql->query("select ifok from `$tb_admins_page` where adminid='$aid' and xpage='$page'");
        $msql->next_record();
        echo trim($msql->f('ifok'));
        break;
        case "record":
            $aid = $_POST['aid'];
			
			
			$ifok= array("失败","成功");
			$e= array();
			
			$msql->query("select moditime,modiuser,modisonuser,modiip as modiip,addr,action from `$tb_user_edit` where userid='$aid' order by moditime desc limit 20");
			$i=0;
			while($msql->next_record()){
				$e[$i]['moditime'] = $msql->f('moditime');
				$e[$i]['modiuser'] = transu($msql->f('modiuser'));
				if($msql->f('modiuser')==$userid){
				    $fsql->query("select adminname from `$tb_admins` where adminid='".$msql->f('modisonuser')."'");
					$fsql->next_record();
					$e[$i]['modisonuser'] = $fsql->f('adminname');
				}else{
				    $fsql->query("select username from `$tb_user` where userid='".$msql->f('modisonuser')."'");
					$fsql->next_record();
					$e[$i]['modisonuser'] = $fsql->f('username');
				}
				if ($config['iprecord']==1){
				$e[$i]['modiip'] = $msql->f('modiip');
                $e[$i]['addr'] = $msql->f('addr');
				} else {
				$e[$i]['modiip'] = "***";
                $e[$i]['addr'] = "***";
				}
				$e[$i]['action']= $msql->f('action');
				$i++;
			}
		$e = array("e"=>$e);
		echo json_encode($e);
	break;	
	case "logininfo":
			$l = array();
		$ifok= array("失败","成功");
             $username = $_POST['username'];
			$msql->query("select ip as ip,addr,time,ifok from `$tb_user_login` where username='$username' and xtype=0 order by time desc limit 20");
			$i=0;
			while($msql->next_record()){
		if ($config['iprecord']==1){ 
         $l[$i]['ip']   = $msql->f('ip');
		 $l[$i]['addr']   = $msql->f('addr');
		 } else {
		 $l[$i]['ip']   = "***";
		 $l[$i]['addr']   = "***";
		 }
         $l[$i]['time'] = substr($msql->f('time'),5);
         $l[$i]['ifok'] = $ifok[$msql->f('ifok')];
				 $i++;
			}
$l = array("l"=>$l);
			echo json_encode($l);
        break;
		
}
?>
