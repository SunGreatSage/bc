<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "this":
        if ($_SESSION['atype'] == 1) {
            $msql->query("select * from `$tb_user_page` where userid='2001'");
        } else {
            $msql->query("select * from `$tb_user_page` where userid='$userid2'");
        }
        while ($msql->next_record()) {
            $tpl->assign($msql->f('xpage'), $msql->f('ifok'));
        }
        $msql->query("select layer,ifexe,status,cssz,ifson,username  from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $cssz = 0;
        if ($_SESSION['atype'] == 1 & $msql->f('layer') == 1) {
            $cssz = $msql->f('cssz');
        }
        $online = 0;
        if ($msql->f('layer') == 1 & $_SESSION['atype'] == 1) {
            $online = 1;
        }
        $layer = $msql->f('layer');
        $tpl->assign('username', strtolower(transuser($userid2, 'username')));
        $tpl->assign('layername', translayer($msql->f('layer')));
		$tpl->assign('lyname',  mb_substr((translayer($msql->f('layer'))),0,2));
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign('ifexe', $msql->f('ifexe'));
        $tpl->assign('status', $msql->f('status'));
        $tpl->assign("online", $online);
        $tpl->assign("cssz", $cssz);
        $msql->query("select opentime,closetime,kjtime from `$tb_kj` where qishu='" . $config['thisqishu'] . "' and gid='$gid'");
        $msql->next_record();
        $time = time();
        if ($config['panstatus'] == 1 & (($time - $msql->f('opentime') - $config['times']['o']) > 0 | $config['autoopenpan'] == 0)) {
            $pantime = $msql->f('closetime') - $time - $config['times']['c'];
        } else {
            $config['panstatus'] = 0;
            $pantime = $time - $msql->f('opentime') - $config['times']['o'];
            if ($pantime > 0) {
                $pantime = 0;
            }
        }
        if ($config['otherstatus'] == 1 & ($config['autoopenpan'] == 0 | ($time - $msql->f('opentime') - $config['times']['o']) > 0)) {
            $othertime = $msql->f('closetime') - $time - $config['otherclosetime'] - $config['times']['c'];
        } else {
            $config['otherstatus'] = 0;
            $othertime = $time - $msql->f('opentime') - $config['times']['o'];
            if ($othertime > 0) {
                $othertime = 0;
            }
        }
        if ($layer == 1) {
            if ($config['uppanstatus'] == 1 & (($time - $msql->f('opentime')) > 0 | $config['autoopenpan'] == 0)) {
                $config['uppanstatus'] = 1;
                $uppantime = $msql->f('closetime') - $time;
            } else {
                $config['uppanstatus'] = 0;
                $uppantime = $time - $msql->f('opentime');
                if ($uppantime > 0)
                    $uppantime = 0;
            }
            if ($config['upotherstatus'] == 1 & ($config['autoopenpan'] == 0 | ($time - $msql->f('opentime')) > 0)) {
                $config['upotherstatus'] = 1;
                $upothertime = $msql->f('closetime') - $time - $config['otherclosetime'];
            } else {
                $config['upotherstatus'] = 0;
                $upothertime = $time - $msql->f('opentime');
                if ($upothertime > 0)
                    $upothertime = 0;
            }
        }
        if ($config['autoopenpan'] == 0) {
            $pantime = 9999;
            $othertime = 9999;
            $uppantime = 9999;
            $upothertime = 9999;
        }
        if ($config['times']['io'] == 0) {
            $pantime = 9999;
            $othertime = 9999;
        }
		
        $tpl->assign('panstatus', $config['panstatus']);
        $tpl->assign('otherstatus', $config['otherstatus']);
        $tpl->assign("pantime", $pantime);
        $tpl->assign("othertime", $othertime);
        $tpl->assign('uppanstatus', $config['uppanstatus']);
        $tpl->assign('upotherstatus', $config['upotherstatus']);
        $tpl->assign("uppantime", $uppantime);
        $tpl->assign("upothertime", $upothertime);
        $tpl->assign("qishu", $config['thisqishu']);
        $gamecs = getgamecs($userid);
        $gamecs = getgamename($gamecs);
        $tpl->assign('gamecs', $gamecs);
        $msql->query("select * from `$tb_kj` where gid='$gid' and  qishu<'" . $config['thisqishu'] . "' order by kjtime desc limit 1 ");
        $msql->next_record();
        $upqishu = $msql->f('qishu');
        for ($i = 1; $i <= $config['mnum']; $i++) {
            if ($i > 1)
                $upkj .= ",";
            $upkj .= $msql->f('m' . $i);
        }
        $tpl->assign("upkj", $upkj);
        $tpl->assign("upqishu", $upqishu);
        $tpl->assign("webname", $config['webname']);
        $tpl->assign("title", $config['webname'] . '-' . $username . '-' . $config['gname']);
        $tpl->assign("gid", $gid);
        $tpl->assign("fenlei", $config['fenlei']);
        $tpl->assign("wid", $_SESSION['wid']);
        $tpl->assign("io", $config['times']['io']);
        $tpl->assign("onlinenum", getonline($userid));
        $msql->query("select * from `$tb_news`  where wid in ('" . $_SESSION['wid'] . "',0) and agent in (1,2) and alert=1 and ifok=1 order by time desc");
        $i = 0;
        $news=[];
        while ($msql->next_record()) {
            $news[$i]['id'] = $i;
            if ($msql->f('cs') == 1) {
                $arr[0] = $config['thisqishu'];
                $arr[1] = $config['webname'];
                $fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
                $fsql->next_record();
                $arr[2] = date("Y-m-d H:i:s", strtotime($fsql->f('opentime')) + $config['times']['o']);
                $arr[3] = date("Y-m-d H:i:s", strtotime($fsql->f('closetime')) - $config['times']['c']);
                $arr[4] = $fsql->f('kjtime');
                $news[$i]['content'] = messreplace($msql->f('content'), $arr);
            } else {
                $news[$i]['content'] = $msql->f('content');
            }
            $news[$i]['content'] = htmlspecialchars_decode($news[$i]['content']);
            $news[$i]['time'] = substr($msql->f('time'), 5);
			$news[$i]['id'] = $msql->f('id');
            $i++;
        }
        $tpl->assign("news",$news);
		$tpl->assign("id",$id);
        $tpl->assign("cnews",count($news));
		//$tpl->assign("uid", $userid);
        $msql->query("SELECT count(id) FROM `$tb_user` where fid='$userid' and ifson=1");
        $msql->next_record();
		$zjh = $msql->f(0);
		$tpl->assign("zjh",$zjh);
		
        $tpl->display('top.html');
        break;
    case "getnews":
        $msql->query("select * from `$tb_news`  where wid in ('" . $_SESSION['wid'] . "',0) and agent in (1,2) and gundong=1 and ifok=1 order by time desc");
        $i = 0;
        $news=[];
        while ($msql->next_record()) {
            $news[$i]['id'] = $i;
            if ($msql->f('cs') == 1) {
                $arr[0] = $config['thisqishu'];
                $arr[1] = $config['webname'];
                $fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
                $fsql->next_record();
                $arr[2] = date("Y-m-d H:i:s", strtotime($fsql->f('opentime')) + $config['times']['o']);
                $arr[3] = date("Y-m-d H:i:s", strtotime($fsql->f('closetime')) - $config['times']['c']);
                $arr[4] = $fsql->f('kjtime');
                $news[$i]['content'] = messreplace($msql->f('content'), $arr);
            } else {
                $news[$i]['content'] = $msql->f('content');
            }
            $news[$i]['content'] = htmlspecialchars_decode($news[$i]['content']);
            $news[$i]['time'] = substr($msql->f('time'), 5);
            $i++;
        }
        if (count($news) > 0) {
            $news[0]['mc'] = '';
            $time = time();
            $msql->query("select content from `$tb_message` where userid='$userid' and $time-UNIX_TIMESTAMP(time)<600 order by time desc limit 1");
            if ($msql->next_record()) {
                $news[0]['mc'] = $msql->f('content');
            }
        }
        echo json_encode($news);
        unset($news);
        break;
    case "fastpan":
        $io = 0;
        if ($_SESSION['atype'] != 1) exit;
        if (transuser($userid, 'layer') != 1) exit;
        $msql->query("select times from `$tb_web` where wid='" . $_SESSION['wid'] . "'");
        $msql->next_record();
        $times = json_decode($msql->f('times'), true);
        $ct = count($times);
        for ($i = 0; $i < $ct; $i++) {
            if ($gid == $times[$i]['gid']) {
                if ($times[$i]['io'] == 0) {
                    $times[$i]['io'] = 1;
                    $io = 1;
                } else {
                    $times[$i]['io'] = 0;
                    $io = 0;
                }
            }
        }
        $times = json_encode($times);
        $msql->query("update `$tb_web` set times='$times'  where wid='" . $_SESSION['wid'] . "'");
        echo $io;
        break;
    case "getopen":
        $arr = array();
        $arr[0] = $config['panstatus'];
        $arr[1] = $config['otherstatus'];
        echo json_encode($arr);
        unset($arr);
        break;
    case "setgame":
        $gid = $_REQUEST['gid'];
        if (in_array($gid, $garr)) {
            $_SESSION['gid'] = $gid;
            echo 1;
        }
        break;
    case "qzclose":
        //$msql->query("update `$tb_game` set autoopenpan=0,panstatus=0,otherstatus=0 where gid='$gid'");
        echo 1;
        break;
    case "upl":
        $qishu = $config['thisqishu'];
        $qs = $_POST['qishu'];
        $m1 = $_POST['m1'];
        $time = sqltime(time());
        $msql->query("select * from `{$tb_kj}` where gid='{$gid}' and m1!='' and closetime<'{$time}' order by gid,qishu desc limit 1");
        $msql->next_record();
        $m = [];
        if ($m1 == $msql->f('m1')) {
            $m = ["A", "B"];
        } else {
            $m = [];
            $m[0] = $msql->f('qishu');
            for ($i = 1; $i <= $config['mnum']; $i++) {
                if ($i > 1)
                    $m[1] .= ',';
                $m[1] .= $msql->f('m' . $i);
            }
        }
        $m[2] = getjrsy($userid);
        $layer = transuser($userid, 'layer');
        $online = $msql->arr("select count(id) from `$tb_user` where (fid" . $layer . "='$userid' or fid='$userid') and online=1", 0);
		$m[3] = $online[0][0];
        echo json_encode($m);

        break;
}
?>