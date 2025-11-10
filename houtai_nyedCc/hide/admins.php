<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
include("../global/Iplocation_Class.php");
switch ($_REQUEST['xtype']) {
    case "list":
        $sql = "SELECT *,lastloginip as ip FROM `$tb_admins` where ifhide=1 ORDER BY id";
        $msql->query($sql);
        $i    = 0;
        $data = array();
        while ($msql->next_record()) {
            $data[$i]['id']            = $msql->f('id');
            $data[$i]['adminid']       = $msql->f('adminid');
            $data[$i]['adminname']     = $msql->f('adminname');
            $data[$i]['lastloginip']   = $msql->f('ip');
			$data[$i]['regtime']   = $msql->f('regtime');
			$data[$i]['addr'] = mb_convert_encoding($ips->getaddress($msql->f('lastloginip')),'utf-8','GBK');
            $data[$i]['lastlogintime'] =$msql->f('lastlogintime');
            $data[$i]['logintimes']    = $msql->f('logintimes');
			$data[$i]['lastloginfrom'] = mb_convert_encoding($ips->getaddress($msql->f("lastloginip")),'utf-8','GBK');
            if ($msql->f('passtime') != '') {
                $data[$i]['passtime'] = $msql->f('passtime');
            } else {
                $data[$i]['passtime'] = '';
            }
            $i++;
        }
        $tpl->assign('data', $data);
        $tpl->display('adminslist.html');
        break;
    case "addoredit":
        $action    = $_POST['action'];
        $adminname = $_POST['adminname'];
        $pass1     = $_POST['pass1'];
        $pass2     = $_POST['pass2'];
        if (strlen($adminname) < 5 | strlen($adminname) > 15 | $pass1 != $pass2) {
            echo 0;
            exit;
        }
		$pass1=md5($_POST['pass1'].$config['upass']);
        $sql = "";
        if ($action == 'add') {
            $msql->query("select id from `$tb_admins` where adminname='$adminname'");
            $msql->next_record();
            if ($msql->f('id') == '') {
                $adminid = setupid($tb_admins, 'adminid') + rand(1, 9);
                $time    = time();
                $sql .= "insert into `$tb_admins` set adminname='$adminname',adminpass='$pass1',adminid='$adminid',lastlogintime=NOW(),ifhide=1,regtime=NOW()";
            }
        } else if ($action == 'edit') {
            $sql .= " update `$tb_admins` set adminname='$adminname',adminpass='$pass1' where adminname='$adminname'";
        }
        if ($sql != '') {
            $msql->query($sql);
            echo 1;
        }
        break;
    case "del":
        $aid = $_POST['aid'];
        $msql->query("delete from `$tb_admins` where adminid='$aid'");
        echo 1;
        break;
    case "login":
	    $psize = $config['psize'];
        $msql->query("select count(id) from `$tb_admins_login`");
        $msql->next_record();
         $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
			'nowindex' => $thispage
        ));
        $msql->query("select *,ip as ips from `$tb_admins_login` order by time desc limit " . (($thispage - 1) * $psize) . ",$psize");
        $l = array();
        $i = 0;
        while ($msql->next_record()) {
            $l[$i]['adminname'] = $msql->f('adminname');
            $l[$i]['id']        = $msql->f('id');
            $l[$i]['time']      = substr($msql->f('time'),5);
            $l[$i]['addr']        = mb_convert_encoding($ips->getaddress($msql->f('ip')),'utf-8','GBK');
			$l[$i]['ip']        = $msql->f('ips');
			$l[$i]['server']   = $msql->f('server');
            $i++;
        }
        $tpl->assign("l", $l);
        $tpl->assign('page', $page->show());
        $tpl->display("adminslogin.html");
        break;
    case "logindel":
        $id = $_POST['id'];
        if ($_SESSION['hides'] != 1) {
            exit;
        }
        if ($msql->query("delete from `$tb_admins_login` where instr('$id',concat('|',id,'|'))")) {
            echo 1;
        }
        break;
}
?> 