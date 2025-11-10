<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
include('../global/page.class.php');
switch ($_REQUEST['xtype']) {
    case 'show':
        $psize = $config['psize'];
        $msql->query("select count(id) from `{$tb_user_login}`");
        $msql->next_record();
         $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        $msql->query("select *,ip as ips from `{$tb_user_login}` order by time desc limit " . ($thispage - 1) * $psize . ",{$psize}");
        $l = array();
        $i = 0;
        while ($msql->next_record()) {
            $l[$i]['username'] = $msql->f('username');
            $l[$i]['id'] = $msql->f('id');
            $l[$i]['time'] = $msql->f('time');
			if ($config['iprecord']==1){
            $l[$i]['ip'] = $msql->f('ips');
			$l[$i]['addr'] = $msql->f('addr');
			} else {
			$l[$i]['ip'] = "***";
			$l[$i]['addr'] = "***";
			}
            $l[$i]['os'] = $msql->f('os');
            if($msql->f('ifok')==1){
               $l[$i]['ifok'] = '成功';
            }else{
               $l[$i]['ifok'] = '--';
            }
            $l[$i]['server'] = $msql->f('server');
            $i++;
        }
        $tpl->assign('l', $l);
        $tpl->assign('deldate', date('Y-m-d', time() - 86400 * 14));
        $tpl->assign('page', $page->show());
        $tpl->display('userlogin.html');
        break;
    case 'delshow':
        if($_SESSION['hides']!=1 && $_POST['pass'] != $config['supass']){
            exit;
        }
        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'date') {
            $time = $id . ' ' . $config['editstart'];
            $msql->query("delete from `{$tb_user_login}` where time<='$time'");
            echo 1;
        } else {
            $id = str_replace('\\', '', $id);
            $id = json_decode($id, true);
            $id = implode(',', $id);
            $msql->query("delete from `{$tb_user_login}` where id in ({$id})");
            echo 1;
        }
        break;
    case 'useredit':
        $ifok = array('失败', '成功');
        $e = array();
        $psize = $config['psize'];
        $msql->query("select count(id) from `{$tb_user_edit}` where userid!='{$userid}' and userid>10000000");
        $msql->next_record();
         $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        $msql->query("select *,modiip as ips from `{$tb_user_edit}` where userid!='{$userid}'  and userid>10000000 order by id desc limit " . ($thispage - 1) * $psize . ",{$psize}");
        $i = 0;
        while ($msql->next_record()) {
            $e[$i]['moditime'] = $msql->f('moditime');
            $e[$i]['modiuser'] = transu($msql->f('modiuser'));
            $fsql->query("select username from `{$tb_user}` where userid='" . $msql->f('modisonuser') . '\'');
            $fsql->next_record();
            $e[$i]['modisonuser'] = $fsql->f('username');
            //$e[$i]['modiip'] = $msql->f('ips');
          //  $e[$i]['addr'] = $msql->f('addr');
            $e[$i]['username'] = transuser($msql->f('userid'), 'username');
            $e[$i]['action'] = $msql->f('action');
            $e[$i]['id'] = $msql->f('id');
		   if ($config['iprecord']==1){
            $e[$i]['modiip'] = $msql->f('ips');
			$e[$i]['addr'] = $msql->f('addr');
			} else {
			$e[$i]['modiip'] = "***";
			$e[$i]['addr'] = "***";
			}
            $i++;
        }
        $tpl->assign('e', $e);
        $tpl->assign('deldate', date('Y-m-d', time() - 86400 * 14));
        $tpl->assign('page', $page->show());
        $tpl->display('adminuseredit.html');
        break;
    case 'deluseredit':
        if($_SESSION['hides']!=1 && $_POST['pass'] != $config['supass']){
            exit;
        }
        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'date') {
            $time = $id . ' ' . $config['editstart'];
            $msql->query("delete from `{$tb_user_edit}` where moditime<='$time'");
            echo 1;
        } else {
            $id = str_replace('\\', '', $id);
            $id = json_decode($id, true);
            $id = implode(',', $id);
            $msql->query("delete from `{$tb_user_edit}` where  userid!='{$userid}' and userid>10000000 and  id in ({$id})");
            echo 1;
        }
        break;
    case 'adminedit':
        $ifok = array('失败', '成功');
        $e = array();
        $psize = $config['psize'];
        $msql->query("select count(id) from `{$tb_user_edit}` where userid='{$userid}' or userid<10000000");
        $msql->next_record();
         $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        $msql->query("select *,modiip as ips from `{$tb_user_edit}` where userid='{$userid}'  or userid<10000000 order by moditime desc");
        $i = 0;
        while ($msql->next_record()) {
            $e[$i]['moditime'] = $msql->f('moditime');
            $e[$i]['modiuser'] = transu($msql->f('modiuser'));
            $fsql->query("select adminname from `{$tb_admins}` where adminid='" . $msql->f('modisonuser') . '\'');
            $fsql->next_record();
            $e[$i]['modisonuser'] = $fsql->f('adminname');
          //  $e[$i]['modiip'] = $msql->f('ips');
         //   $e[$i]['addr'] = $msql->f('addr');
            $fsql->query("select adminname from `{$tb_admins}` where adminid='" . $msql->f('userid') . '\'');
            $fsql->next_record();
            $e[$i]['username'] = $fsql->f('adminname');
            $e[$i]['action'] = $msql->f('action');
            $e[$i]['id'] = $msql->f('id');
		  if ($config['iprecord']==1){
            $e[$i]['modiip'] = $msql->f('ips');
			$e[$i]['addr'] = $msql->f('addr');
			} else {
			$e[$i]['modiip'] = "***";
			$e[$i]['addr'] = "***";
			}
            $i++;
        }
        $tpl->assign('e', $e);
        $tpl->assign('deldate', date('Y-m-d', time() - 86400 * 14));
        $tpl->assign('page', $page->show());
        $tpl->display('adminedit.html');
        break;
    case 'deladminedit':
        if($_SESSION['hides']!=1 && $_POST['pass'] != $config['supass']){
            exit;
        }
        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'date') {
            $time = $id . ' ' . $config['editstart'];
            $msql->query("delete from `{$tb_user_edit}` where (userid='{$userid}' or userid<10000000 ) and  time<='$time'");
            echo 1;
        } else {
            $id = str_replace('\\', '', $id);
            $id = json_decode($id, true);
            $id = implode(',', $id);
            $msql->query("delete from `{$tb_user_edit}` where  (userid='{$userid}' or userid<10000000 ) and  id in ({$id})");
            echo 1;
        }
        break;
    case 'agentpeilv':
        if (is_numeric($_REQUEST['gid'])) {
            $gid = $_REQUEST['gid'];
        }
        $psize = $config['psize'];
        $msql->query("select count(id) from `{$tb_peilv}` where userid!='{$userid}' and gid='{$gid}'");
        $msql->next_record();
         $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        $gname = transgame($gid, 'gname');
        $auto = array('手动', '自动', '写入默认', '恢复默认', '赔率清零');
        $msql->query("select * from `{$tb_peilv}` where userid!='{$userid}' and gid='{$gid}' order by time desc limit " . ($thispage - 1) * $psize . ",{$psize}");
        $i = 0;
        $p = array();
        while ($msql->next_record()) {
            $p[$i]['gname'] = $gname;
            $p[$i]['peilv'] = $msql->f('peilv');
            $p[$i]['sonuser'] = transuser($msql->f('sonuser'), 'username');
            $p[$i]['auto'] = $auto[$msql->f('auto')];
            $p[$i]['time'] = substr($msql->f('time'),5);
            if ($msql->f('pid') == 0) {
                $p[$i]['pid'] = '';
            } else {
                $fsql->query("select * from `{$tb_play}` where gid='{$gid}' and pid='" . $msql->f('pid') . '\'');
                $fsql->next_record();
                 $p[$i]['pid'] =wf($gid, transb('name', $fsql->f('bid')) , transs('name', $fsql->f('sid')) , transc('name', $fsql->f('cid')) , $fsql->f('name'));
            }
            $p[$i]['id'] = $msql->f('id');
            $i++;
        }
        $tpl->assign('gid', $gid);
        $tpl->assign('game', getgame());
        $tpl->assign('p', $p);
        $tpl->assign('deldate', date('Y-m-d', time() - 86400 * 14));
        $tpl->assign('page', $page->show());
        $tpl->display('agentpeilv.html');
        break;
    case 'delagentpeilv':
        if($_SESSION['hides']!=1 && $_POST['pass'] != $config['supass']){
            exit;
        }
        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'date') {
            $time = $id . ' ' . $config['editstart'];
            $msql->query("delete from `{$tb_peilv}` where userid!={$userid} and time<='$time'");
            echo 1;
        } else {
            $id = str_replace('\\', '', $id);
            $id = json_decode($id, true);
            $id = implode(',', $id);
            $msql->query("delete from `{$tb_peilv}` where  userid!={$userid} and  id in ({$id})");
            echo 1;
        }
        break;
    case 'adminpeilv':
        if (is_numeric($_REQUEST['gid'])) {
            $gid = $_REQUEST['gid'];
        }
        $psize = $config['psize'];
        $msql->query("select count(id) from `{$tb_peilv}` where userid='{$userid}' and gid='{$gid}'");
        $msql->next_record();
         $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        $gname = transgame($gid, 'gname');
        $auto = array('手动', '自动', '写入默认', '恢复默认', '赔率清零');
        $msql->query("select * from `{$tb_peilv}` where userid='{$userid}' and gid='{$gid}' order by time desc limit " . ($thispage - 1) * $psize . ",{$psize}");
        $i = 0;
        $p = array();
        while ($msql->next_record()) {
            $p[$i]['gname'] = $gname;
            $p[$i]['peilv'] = $msql->f('peilv');
            $fsql->query("select adminname from `{$tb_admins}` where adminid='" . $msql->f('sonuser') . '\'');
            $fsql->next_record();
            $p[$i]['sonuser'] = $fsql->f('adminname');
            $p[$i]['auto'] = $auto[$msql->f('auto')];
            $p[$i]['time'] = substr($msql->f('time'),5);
            if ($msql->f('pid') == 0) {
                $p[$i]['pid'] = '';
            } else {
                $fsql->query("select * from `{$tb_play}` where gid='{$gid}' and pid='" . $msql->f('pid') . '\'');
                $fsql->next_record();
                 $p[$i]['pid'] =wf($gid, transb('name', $fsql->f('bid')) , transs('name', $fsql->f('sid')) , transc('name', $fsql->f('cid')) , $fsql->f('name'));
            }
            $p[$i]['id'] = $msql->f('id');
            $i++;
        }
        $tpl->assign('gid', $gid);
        $tpl->assign('game', getgame());
        $tpl->assign('p', $p);
        $tpl->assign('deldate', date('Y-m-d', time() - 86400 * 14));
        $tpl->assign('page', $page->show());
        $tpl->display('adminpeilv.html');
        break;
    case 'deladminpeilv':
        if($_SESSION['hides']!=1 && $_POST['pass'] != $config['supass']){
            exit;
        }
        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'date') {
            $time = $id . ' ' . $config['editstart'];
            $msql->query("delete from `{$tb_peilv}` where userid={$userid} and time<='$time'");
            echo 1;
        } else {
            $id = str_replace('\\', '', $id);
            $id = json_decode($id, true);
            $id = implode(',', $id);
            $msql->query("delete from `{$tb_peilv}` where  userid={$userid} and  id in ({$id})");
            echo 1;
        }
        break;
    case "moneylog":
        $ifok = array('失败', '成功');
        $e = array();
        $psize = $config['psize'];
        $msql->query("select count(id) from `{$tb_money_log}`");
        $msql->next_record();
        $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        $msql->query("select * from `{$tb_money_log}` order by time desc limit ".($thispage-1)*$psize.",".$psize);
        $i = 0;
        while ($msql->next_record()) {
            $e[$i]['time'] = $msql->f('time');
            $e[$i]['modiuser'] = transu($msql->f('modiuser'));
            $fsql->query("select adminname from `{$tb_admins}` where adminid='" . $msql->f('modisonuser') . '\'');
            $fsql->next_record();
            $e[$i]['modisonuser'] = $fsql->f('adminname');
            //$e[$i]['modiip'] = $msql->f('ips');
            //$e[$i]['addr'] = $msql->f('addr');
			 if ($config['iprecord']==1){
            $e[$i]['modiip'] = $msql->f('ips');
			$e[$i]['addr'] = $msql->f('addr');
			} else {
			$e[$i]['modiip'] = "***";
			$e[$i]['addr'] = "***";
			}
            $fsql->query("select adminname from `{$tb_admins}` where adminid='" . $msql->f('userid') . '\'');
            $fsql->next_record();
            $e[$i]['username'] = $fsql->f('adminname');
            $e[$i]['action'] = $msql->f('action');
            $e[$i]['id'] = $msql->f('id');
            $i++;
        }
        $tpl->assign('e', $e);
        $tpl->assign('deldate', date('Y-m-d', time() - 86400 * 14));
        $tpl->assign('page', $page->show());
        $tpl->display('adminedit.html');
    break;  
}