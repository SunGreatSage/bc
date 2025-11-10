<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        $msql->query("select * from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $tpl->assign('name', $msql->f('name'));
        $tpl->assign('username', $msql->f('username'));
        $tpl->assign('fly', $msql->f('fly'));
        $tpl->assign('status', $msql->f('status'));
        $tpl->assign('usermoney', $msql->f('usermoney'));
        $tpl->assign('maxmoney', $msql->f('maxmoney'));
        $tpl->assign('maxdown', $msql->f('maxdown'));
        $tpl->assign('zc', $msql->f('zc'));
        $tpl->assign('upzc', $msql->f('upzc'));
        $tpl->assign('userid', $msql->f('userid'));
        $tpl->assign("upname", transu($msql->f('fid')));
        $tpl->assign('pantype', $msql->f('pantype'));
        $tpl->assign('defaultpan', $msql->f('defaultpan'));
		$tpl->assign('maxrenflag',$config['maxrenflag']);
        $layer   = $msql->f('layer');
        $ifpeilv = $msql->f('ifpeilv');
        if ($msql->f('ifagent') == 1) {
            $tmp = 'level' . $layer;
            $tpl->assign('usertype', $$tmp);
        } else {
            $tpl->assign('usertype', '会员');
        }
        $tsql->query("select sum(maxdown),count(id) from 	`$tb_user` where fid='" . $msql->f('userid') . "'");
        $tsql->next_record();
        $tpl->assign("nowdown", $tsql->f(0) + $tsql->f(1));
        $field_arr[0]['name'] = "子帐号ID";
        $field_arr[1]['name'] = "子帐号名称";
        $field_arr[2]['name'] = "最后登陆IP";
        $field_arr[3]['name'] = "最后登陆时间";
        $field_arr[4]['name'] = "登陆次数";
        $field_arr[5]['name'] = "上次改密码时间";
        $field_arr[6]['name'] = "操作";
        $sql                  = "SELECT * FROM `$tb_user` where fid='$userid' and ifson=1";
        $msql->query($sql);
        $i        = 0;
        $data_arr = array();
        $page     = array();
        if ($layer == 1 & $ifpeilv == 1) {
            $fsql->query("select * from `$tb_user_page` where userid=2001 group by xpage");
        } else {
            $fsql->query("select * from `$tb_user_page` where userid=2001 and xpage!='pset' group by xpage");
        }
        $page[0][0] = '子帐号权限设置';
        $j          = 1;
        while ($fsql->next_record()) {
            $page[0][$j] = $fsql->f('pagename');
            $j++;
        }
        while ($msql->next_record()) {
            $data_arr[$i]['userid']        = $msql->f("userid");
            $data_arr[$i]['username']      = $msql->f("username");
            $data_arr[$i]['lastloginip']   = $msql->f("lastloginip");
            $data_arr[$i]['lastlogintime'] = date("m-d H点", $msql->f("lastlogintime"));
            $data_arr[$i]['logintimes']    = $msql->f("logintimes");
            $data_arr[$i]['passtime']      = date("m-d H点", $msql->f("passtime"));
            $page[$i + 1][0]               = $msql->f('username');
            if ($layer == 1 & $ifpeilv == 1) {
                $fsql->query("select * from `$tb_user_page` where userid='" . $msql->f('userid') . "' group by xpage");
            } else {
                $fsql->query("select * from `$tb_user_page` where userid='" . $msql->f('userid') . "' and xpage!='pset' group by xpage");
            }
            $j = 1;
            while ($fsql->next_record()) {
                $page[$i + 1][$j] = "<img src='../img/" . $fsql->f('ifok') . '.gif' . "' page='" . $fsql->f('xpage') . "' uid='" . $msql->f('userid') . "' />";
                $j++;
            }
            $i++;
        }
        $str = "";
        foreach ($data_arr as $d1) {
            $str .= "<tr>";
            foreach ($d1 as $d2) {
                $str .= "<td >" . $d2 . "</td>";
            }
            $str .= "<td style='width:220px;'>";
            $str .= "<input type='button' class='del btn1 btnf' value='$thedel' id=del" . $d1['userid'] . " uid=" . $d1['userid'] . " /><input class='edit btn1 btnf' id=edit" . $d1['userid'] . " value='$theedit' uid=" . $d1['userid'] . " type='button' />";
            $str .= "</td></tr>";
        }
        $tpl->assign('data', $str);
        $tpl->assign('field_arr', $field_arr);
        $pstr = "";
        foreach ($page as $k1 => $d1) {
            $pstr .= "<tr>";
            if ($k1 == 0) {
                foreach ($d1 as $k2 => $d2) {
                    $pstr .= "<th>" . $d2 . "</th>";
                }
            } else {
                foreach ($d1 as $k2 => $d2) {
                    if ($k2 == 0) {
                        $pstr .= "<th>" . $d2 . "</th>";
                    } else {
                        $pstr .= "<td>" . $d2 . "</td>";
                    }
                }
            }
            $pstr .= "</tr>";
        }
        $tpl->assign('pstr', $pstr);
        $tpl->display('userinfo.html');
        break;
    case "addoredit":
        $action   = $_POST['action'];
        $username = $_POST['username'];
        $pass1    = $_POST['pass1'];
        $pass2    = $_POST['pass2'];
        if (strlen($username) < 4 | strlen($username) > 10 | $pass1 != $pass2) {
            echo 0;
            exit;
        }
        $sql = "";
        if ($action == 'add') {
            $usernamef = transuser($userid, 'username');
            if (substr($username, 0, strlen($username) - 1) != $usernamef)
                exit;
            $msql->query("select id from `$tb_user` where username='$username'");
            $msql->next_record();
            if ($msql->f('id') == '') {
                $userid2 = setupid($tb_user, 'userid') + rand(1, 9);
                $time    = time();
                $layer   = transuser($userid, 'layer');
                $sql .= "insert into `$tb_user` set username='$username',userpass='$pass1',userid='$userid2',fid='$userid',status='1',passtime=NOW(),lastlogintime=NOW(),layer='$layer',ifson='1',ifagent='1'";
            }
        } else if ($action == 'edit') {
            $sql .= " update `$tb_user` set userpass='$pass1' where username='$username' and fid='$userid'";
        }
        if ($sql != '') {
            $msql->query($sql);
            if ($action == 'add') {
                $msql->query("select * from `$tb_user_page` where userid='2001' group by xpage ");
                while ($msql->next_record()) {
                    $fsql->query("insert into `$tb_user_page` set xpage='" . $msql->f('xpage') . "',pagename='" . $msql->f('pagename') . "',userid='$userid2',ifok='0'");
                }
            }
            echo 1;
        }
        break;
    case "del":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $msql->query("delete from `$tb_user` where userid='$uid'");
        $msql->query("delete from `$tb_user_page` where userid='$uid'");
        echo 1;
        break;
    case "updatepage":
        $uid  = $_POST['uid'];
        $page = $_POST['page'];
        if (!checkfid($uid))
            exit;
        $msql->query("update `$tb_user_page` set ifok=if(ifok=0,1,0) where userid='$uid' and xpage='$page'");
        $msql->query("select ifok from `$tb_user_page` where userid='$uid' and xpage='$page'");
        $msql->next_record();
        echo $msql->f('ifok');
        break;
    case "changeautofly":
        $msql->query("update `$tb_user` set autofly=if(autofly=0,1,0) where userid='$userid'");
        $msql->query("select autofly from `$tb_user` where userid='$userid'");
        $msql->next_record();
        echo $msql->f('autofly');
        break;
    case "changepass":
        $userpass = $_POST['password'];
        if ($userpass == '')
            $sql = "update `$tb_user` set maxcun='$maxcun' where userid='$userid'";
        else
            $sql = "update `$tb_user` set userpass='$userpass',passtime=NOW() where userid='$userid'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
}
?>