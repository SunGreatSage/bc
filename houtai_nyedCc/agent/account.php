<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        $msql->query("select * from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $tpl->assign('gid', $msql->f('gid'));
		$game =getgamecs($msql->f('userid'));
		$game = getgamename($game);
		$tpl->assign('game',$game);
		$tpl->assign('name', $msql->f('name'));
        $tpl->assign('username', $msql->f('username'));
        $money = getmaxmoney($userid);
        $tpl->assign('money', $money);
        $tpl->assign('maxmoney', $msql->f('maxmoney'));
        $tpl->assign('premoney', p2($money / $msql->f('maxmoney')) * 100);
        $kmoney = getkmaxmoney($userid);
        $tpl->assign('kmoney', $kmoney);
        $tpl->assign('kmaxmoney', $msql->f('kmaxmoney'));
        $tpl->assign('prekmoney', p2($kmoney / $msql->f('kmaxmoney')) * 100);
		$tpl->assign('fudong', $msql->f('fudong'));
        $ren = getmaxren($userid);
        $tpl->assign('ren', $ren);
        $tpl->assign('maxren', $msql->f('maxren'));		
        $tpl->assign('preren', p2($ren / $msql->f('maxren')) * 100);
        $tpl->assign('fly', transfly($msql->f('fly')));
        $tpl->assign('userid', $userid);
        $tpl->assign('pan', json_decode($msql->f('pan'), true));
        $tpl->assign('defaultpan', $msql->f('defaultpan'));
        $layer = $msql->f('layer');
        if ($msql->f('ifagent') == 1) {
            $tpl->assign('usertype', $config['layer'][$layer - 1]);
        } else {
            $tpl->assign('usertype', '会员');
        }
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign('ifexe', $msql->f('ifexe'));
        $tpl->assign('pself', $msql->f('pself'));

        $tpl->display('userinfo.html');
        break;
    case "addoredit":
        $action   = $_POST['action'];
        $username = $_POST['username'];
        $pass1    = md5($_POST['pass1'] . $config['upass']);
        $pass2    = md5($_POST['pass2'] . $config['upass']);
        if (!mb_ereg("^[\w\-\.]{1,32}$", $username) | $pass1 != $pass2) {
            echo 0;
            exit;
        }
        $sql       = "";
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
                $sql .= "insert into `$tb_user` set username='$username',userpass='$pass1',userid='$userid2',fid='$userid',status='1',passtime=NOW(),layer='$layer',ifson='1',ifagent='1'";
               
            }
        } else if ($action == 'edit') {
            $sql .= " update `$tb_user` set userpass='$pass1' where username='$username' and fid='$userid'";
           
        }
        if ($sql != '') {
            $msql->query($sql);
            if ($action == 'add') {
                $msql->query("select * from `$tb_user_page` where userid='2001' order by xsort ");
                while ($msql->next_record()) {
                    $fsql->query("insert into `$tb_user_page` set xpage='" . $msql->f('xpage') . "',pagename='" . $msql->f('pagename') . "',userid='$userid2',ifok='0',xsort='".$msql->f('xsort')."'");
                }
				userchange("新增",$userid2);
            }else{
			$msql->query("select userid from `$tb_user` where username='$username' and fid='$uid'");
			$msql->next_record();
			 userchange("修改密码",$msql->f('userid'));
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
        userchange("更新权限",$uid);
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
    case "changegid":
	    $gid = $_POST['gid'];
        $msql->query("update `$tb_user` set gid='$gid' where userid='$userid'");
		userchange("更改默认彩种",$userid);
        echo 1;
        break;
    case "pself":
        $msql->query("update `$tb_user` set pself=if(pself=0,1,0) where userid='$userid'");
        userchange("更改赔率模式",$uid);
        echo 1;
        break;
    case "changepan":
        $pan = $_POST['pan'];
        if ($pan == 'A' | $pan == 'B' | $pan == 'C' | $pan == 'D') {
            $msql->query("update `$tb_user` set defaultpan='$pan' where userid='$userid'");
            userchange("更改默认盘口",$userid);
            echo 1;
        }
        break;
}
?>