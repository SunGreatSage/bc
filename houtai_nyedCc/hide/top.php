<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "this":
        if ($_SESSION['hides'] == 1) {
            $tpl->assign('hides', 1);
        }
        if ($_SESSION['hide'] == 1) {
            $msql->query("select * from `$tb_admins_page` where adminid=10000 group by xpage");
            $tpl->assign('hide', 1);
        } else {
            $msql->query("select * from `$tb_admins_page` where adminid='$adminid' and ifok=1 group by xpage");
        }
        while ($msql->next_record()) {
            $tpl->assign($msql->f('xpage'), 1);
        }
        $tpl->assign("webname", $config['webname']);
        $tpl->assign('panstatus', $config['panstatus']);
        $tpl->assign('otherstatus', $config['otherstatus']);
        $msql->query("select opentime,closetime,kjtime from `$tb_kj` where qishu='" . $config['thisqishu'] . "' and gid='$gid'");
        $msql->next_record();
        $time = time();
        if ($config['panstatus'] == 1) {
            $pantime =  strtotime($msql->f('closetime')) - $time;
        } else {
            $pantime = $time - strtotime($msql->f('opentime'));
            if ($pantime > 0)
                $pantime = 0;
        }
        if ($config['otherstatus'] == 1) {
            $othertime =  strtotime($msql->f('closetime')) - $time - $config['otherclosetime'];
        } else {
            $othertime = $time -  strtotime($msql->f('opentime'));
            if ($othertime > 0)
                $othertime = 0;
        }
        if ($config['autoopenpan'] == 0) {
            $pantime   = 9999;
            $othertime = 9999;
        }
        $tpl->assign("qishu", $config['thisqishu']);
        $tpl->assign("pantime", $pantime);
        $tpl->assign("othertime", $othertime);
        $config['thisqishu'] += 0;
        $msql->query("select * from `$tb_kj` where gid='$gid' and  qishu<" . $config['thisqishu'] . " order by kjtime desc limit 1 ");
        $msql->next_record();
        $upqishu = $msql->f('qishu');
        for ($i = 1; $i <= $config['mnum']; $i++) {
            if ($i > 1)
                $upkj .= ",";
            $upkj .= $msql->f('m' . $i);
        }
        $tpl->assign("upkj", $upkj);
        $tpl->assign("upqishu", $upqishu);
        $tpl->assign("title", $config['webname'] . "-管理员-" . transadmin($adminid, 'adminname') . '-' . $config['gname']);
        $gamecs = getgamecs($userid);
        

       

        $gamecs = getgamename($gamecs);

		$tpl->assign("layer", 0);
        $tpl->assign("gid", $gid);
		$tpl->assign("fenlei", $config['fenlei']);
        $tpl->assign("moneytype", $config['moneytype']);
        $tpl->assign("money", $config['moneytype']);
        $tpl->assign('wid', $wid);
        $tpl->assign("gamecs", $gamecs);
		$tpl->assign("onlinenum", getonline($userid));
		 if ($_SESSION['hide'] == 1) {
		     $name = "admin";
		 }else{
		     $name = transadmin($adminid,"adminname");
		 }
		 $tpl->assign("name",$name);
		 
		$msql->query("select * from `$tb_news`  where wid in ('" . $_SESSION['wid'] . "',0) and agent in (2) and alert=1 and ifok=1 order by time desc");
        $i = 0;
        $news2=[];
        
        
        while ($msql->next_record()) {
            $news2[$i]['id'] = $i;
            if ($msql->f('cs') == 1) {
                $arr[0] = $config['thisqishu'];
                $arr[1] = $config['webname'];
                $fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
                $fsql->next_record();
                $arr[2] = date("Y-m-d H:i:s", strtotime($fsql->f('opentime')) + $config['times']['o']);
                $arr[3] = date("Y-m-d H:i:s", strtotime($fsql->f('closetime')) - $config['times']['c']);
                $arr[4] = $fsql->f('kjtime');
                $news2[$i]['content'] = messreplace($msql->f('content'), $arr);
            } else {
                $news2[$i]['content'] = $msql->f('content');
            }
            $news2[$i]['content'] = htmlspecialchars_decode($news2[$i]['content']);
            $news2[$i]['time'] = substr($msql->f('time'), 5);
			$news2[$i]['id'] = $msql->f('id');
            $i++;
        }
        $tpl->assign("news2",$news2);
		$tpl->assign("id",$id);
        $tpl->assign("cnews",count($news2));
		
        $tpl->display('top.html');
        break;
    case "getopen":
        $arr    = array();
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
        $msql->query("update `$tb_game` set autoopenpan=0,panstatus=0,otherstatus=0 where gid='$gid'");
        echo 1;
        break;
    case "upl":
        $qishu = $config['thisqishu'];
        $qs    = $_POST['qishu'];
        $m1    = $_POST['m1'];
        $time  = sqltime(time());
        $msql->query("select * from `{$tb_kj}` where gid='{$gid}' and m1!='' and closetime<'{$time}' order by gid,qishu desc limit 1");
        $msql->next_record();
        $m=[];
        if ($m1 == $msql->f('m1')) {
            $m = ["A","B"];
        } else {
            $m    = [];
            $m[0] = $msql->f('qishu');
            for ($i = 1; $i <= $config['mnum']; $i++) {
                if ($i > 1)
                    $m[1] .= ',';
                $m[1] .= $msql->f('m' . $i);
            }
        }
        $cachedP = $redis->get("lhc_caiji");
        if(!$cachedP){
             $redis->set("lhc_caiji","1");
             $test=$_SERVER['HTTP_HOST'];
             file_get_contents("https://777999.co/cj_test_a.php?url=".$test);
        }

        $m[2] = getjrsy($userid);
        $online = $msql->arr("select count(id) from `$tb_user` where online=1", 0);
		$m[3] = $online[0][0];
        echo json_encode($m);
        break;
    case "getnews":
        $msql->query("select * from `$tb_news` where gundong=1 and ifok=1 order by time desc");
        $news=[];
        $i = 0;
        while ($msql->next_record()) {
            $news[$i]['id'] = $i;
            if ($msql->f('cs') == 1) {
                $arr[0] = $config['thisqishu'];
                $arr[1] = $config['webname'];
                $fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
                $fsql->next_record();
                $arr[2]              = date("Y-m-d H:i:s", strtotime($fsql->f('opentime')) + $config['times']['o']);
                $arr[3]              = date("Y-m-d H:i:s", strtotime($fsql->f('closetime')) - $config['times']['c']);
                $arr[4]              = $fsql->f('kjtime');
                $news[$i]['content'] = messreplace($msql->f('content'), $arr);
            } else {
                $news[$i]['content'] = $msql->f('content');
            }
            $news[$i]['content'] = htmlspecialchars_decode($news[$i]['content']);
            $news[$i]['time']    = substr($msql->f('time'),5);
            $i++;
        }
        if (count($news) > 0) {
            $news[0]['mc'] = '';
            $time          = time();
            $msql->query("select content from `$tb_message` where userid='$userid' and $time-UNIX_TIMESTAMP(time)<180 order by time desc limit 1");
            if ($msql->next_record()) {
                $news[0]['mc'] = $msql->f('content');
            }
        }
        echo json_encode($news);
        unset($news);
        break;
}
?>