<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case 'show':
        $msql->query("select * from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $tpl->assign('name', $msql->f('name'));
		$tpl->assign('status', transstatus($msql->f('status')));
        $tpl->assign('username', $msql->f('username'));
        $tpl->assign('money', p1($msql->f('money')));
        $tpl->assign('maxmoney', p1($msql->f('maxmoney')));
        $tpl->assign('kmoney', p1($msql->f('kmoney')));
        $tpl->assign('kmaxmoney', p1($msql->f('kmaxmoney')));
        $tpl->assign('fudong', $msql->f('fudong'));
        $tpl->assign('defaultpan', $msql->f('defaultpan'));
        $pan = json_decode( $msql->f('pan'), true);
        $cps = count($pan);
        for ($k = 0; $k < $cps; $k++) {
            if ($k > 0) {
                $str .= ',';
            }
            $str .= strtolower($pan[$k]);
        }
        $tpl->assign('span', $pan);
		$tpl->assign('panstr', implode(",",$pan));
        $gamecs = getgamecs($userid);
        foreach ($gamecs as $v) {
            $gamearr[] = $v['gid'];
        }
        $gamearr = implode(',', $gamearr);
        $msql->query("select minje from `$tb_config` ");
        $msql->next_record();
        $minje = $msql->f('minje');
        $msql->query("select * from `{$tb_game}` where gid in ({$gamearr}) order by xsort");
        $i = 0;
        $game = array();
        while ($msql->next_record()) {
            $game[$i]['gid'] = $msql->f('gid');
             $tgid = $msql->f('gid');
            $game[$i]['gname'] = $msql->f('gname');
            $game[$i]['panstatus'] = $msql->f('panstatus');
            $game[$i]['fast'] = $msql->f('fast');
            $game[$i]['pan'] = json_decode($msql->f('pan'), true);
            $game[$i]['dftype'] = json_decode($msql->f('dftype'), true);
            $cp = count($game[$i]['pan']);
            for ($j = 0; $j < $cp; $j++) {
                $tclass = $game[$i]['pan'][$j]['class'];
                $cs = getjes8($tclass, $userid, $tgid);
                $game[$i]['pan'][$j]['name'] = $game[$i]['dftype'][$tclass];
                $game[$i]['pan'][$j]['cmaxje'] = $cs['cmaxje'];
                $game[$i]['pan'][$j]['maxje'] = $cs['maxje'];
                $game[$i]['pan'][$j]['minje'] = $minje;
                if ($game[$i]['pan'][$j]['abcd'] == 1) {
                    if ($game[$i]['pan'][$j]['ab'] == 1) {
                        $fsql->query("select {$str} from `{$tb_points}` where userid='{$userid}' and gid='{$tgid}' and class='{$tclass}'  and  ab='A' ");
                        $fsql->next_record();
                        for ($k = 0; $k < $cps; $k++) {
                            $tmp = strtolower($pan[$k]);
                            $game[$i]['pan'][$j]['points' . $tmp . 'a'] = pr2($fsql->f($tmp));
                        }
                        $fsql->query("select {$str} from `{$tb_points}` where userid='{$userid}' and gid='{$tgid}' and class='{$tclass}'  and  ab='B' ");
                        $fsql->next_record();
                        for ($k = 0; $k < $cps; $k++) {
                            $tmp = strtolower($pan[$k]);
                            $game[$i]['pan'][$j]['points' . $tmp . 'b'] = pr2($fsql->f($tmp));
                        }
                    } else {
                        $fsql->query("select {$str} from `{$tb_points}` where userid='{$userid}' and gid='{$tgid}' and class='{$tclass}'  and  ab='0' ");
                        $fsql->next_record();
                        for ($k = 0; $k < $cps; $k++) {
                            $tmp = strtolower($pan[$k]);
                            $game[$i]['pan'][$j]['points' . $tmp . '0'] = pr2($fsql->f($tmp));
                        }
                    }
                } else {
                    $fsql->query("select a from `{$tb_points}` where userid='{$userid}' and gid='{$tgid}' and class='{$tclass}'  and  ab='0' ");
                    $fsql->next_record();
                    $game[$i]['pan'][$j]['pointsa0'] = pr2($fsql->f('a'));
                }
            }
            $i++;
        }
        
		$tpl->assign('game', $game);
		$tpl->assign('gid', $gid);
		$tpl->assign('moneytype', $config['moneytype']);
        $tpl->display('userinfo.html');
        break;
    case "setdefaultpan":
        $pan = $_POST['pan'];
        $pans  = ['A','B','C','D'];
        if(!in_array($pan,$pans)){
            exit;
        }
        $_SESSION['abcd']=$pan;
        $msql->query("update `$tb_user` set defaultpan='$pan' where userid='$userid'");
        echo 1;
        break;
    case 'edit':
        $msql->query("select * from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $tpl->assign('name', $msql->f('name'));
        $tpl->assign('username', $msql->f('username'));
        $tpl->assign('status', $msql->f('status'));
        $tpl->assign('money', $msql->f('money'));
        $tpl->assign('maxmoney', $msql->f('maxmoney'));
        $tpl->assign('kmoney', $msql->f('kmoney'));
        $tpl->assign('kmaxmoney', $msql->f('kmaxmoney'));
        $tpl->assign('fmoney', $msql->f('fmoney'));
        $tpl->assign('fmaxmoney', $msql->f('fmaxmoney'));
        $tpl->assign('fastje', $msql->f('fastje'));
        $tpl->assign('plwarn', $msql->f('plwarn'));
		$tpl->assign('bank', $msql->f('bank'));
		$tpl->assign('banknum', $msql->f('banknum'));
		$tpl->assign('bankname', $msql->f('bankname'));
		
		$tpl->assign('moneytype', $config['moneytype']);
        $tpl->assign('userid', $userid);
        $tpl->assign('pan', json_decode($msql->f('pan'), true));
        $tpl->assign('defaultpan', $msql->f('defaultpan'));
        $tpl->assign('morengid', $msql->f('gid'));
        $tpl->assign('usertype', '会员');
        $pan = json_decode($msql->f('pan'), true);
        $cps = count($pan);
        $gamecs = getgamecs($userid);
        $gamecs = getgamename($gamecs);
        $tpl->assign('gamecs', $gamecs);
        $tpl->assign('span', $pan);
		$tpl->assign('gid', $gid);
        $fsql->query("select * from `{$tb_fastje}` where userid='{$userid}' order by je");
        $i = 0;
        $je = array();
        while ($fsql->next_record()) {
            $je[$i] = $fsql->f('je');
            $i++;
        }
        $tpl->assign('je', $je);
        $username = $msql->f('username');
		
		$ct = array();
		$msql->query("select moditime,action from `$tb_user_edit` where userid='$userid' and action!='修改资料' and action!='修改状态'");
		$i=0;
		while($msql->next_record()){
			$ct[$i]['time'] =  $msql->f('moditime');
			$ct[$i]['action'] = $msql->f("action");
			$i++;
		}
		
		
        $l = array();
        $ifok = array('<label class=red>失败</label>', '<label class=green>成功</label>');
        $msql->query("select ip as ip,addr,time,ifok from `{$tb_user_login}` where username='{$username}' and xtype=2 order by time desc limit 20");
        $i = 0;
        while ($msql->next_record()) {
            $l[$i]['ip'] = $msql->f('ip');
			$l[$i]['addr'] = $msql->f('addr');
            $l[$i]['time'] = substr($msql->f('time'),5);
            $l[$i]['ifok'] = $ifok[$msql->f('ifok')];
            $i++;
        }
        $tpl->assign('ct', $ct);
		$tpl->assign('l', $l);
        $tpl->display($mobi . 'edit.html');
        break;
    case 'editsend':
        if ($_POST['pass1'] != '' & $_POST['pass0'] != '') {
            $pass1 = md5($_POST['pass1'] . $config['upass']);
            $pass0 = md5($_POST['pass0'] . $config['upass']);
            $msql->query("select id from `{$tb_user}` where userpass='{$pass0}' and userid='{$userid}'");
            $msql->next_record();
            if ($msql->f('id') == '') {
                echo 1;
                die;
            }
            $sql = "update `{$tb_user}` set userpass='{$pass1}',passtime=NOW() where userid='{$userid}'";
            if ($msql->query($sql)) {
                userchange('更改密码', $userid);
                sessiondelu();
                echo 2;
                die;
            }
        }
        $morengid = $_POST['morengid'];
        $fastje = $_POST['fastje'];
        $plwarn = $_POST['plwarn'];
        $pan = $_POST['pan'];
        $sql = "fastje='{$fastje}',plwarn='{$plwarn}',defaultpan='{$pan}'";
        $msql->query("select 1 from `{$tb_gamecs}` where gid='{$morengid}' and ifok=1");
        $msql->next_record();
        if ($msql->f(0) == 1) {
            $sql .= ",gid='{$morengid}'";
        }
        $msql->query("update `{$tb_user}` set " . $sql . " where userid='{$userid}'");
        $arr = str_replace("\\", '', $_POST['je']);
		
        $arr = json_decode($arr, true);
		
        if (count($arr) > 0){
      
           $msql->query("delete from `$tb_fastje` where userid='$userid'");
             foreach ($arr as $key => $val) {
            $msql->query("insert into `$tb_fastje` set je='$val',xsort='$key',userid='$userid'");
             }
		}
		userchange('更改资料', $userid);
        echo 3;
        break;
    case 'changepassword2':
        $msql->query("select passtime from `{$tb_user}`  where userid='{$userid}'");
        $msql->next_record();
        if ($msql->f('passtime') == 0) {
            $first = 1;
        }
        $tpl->assign('first', $first);
        $tpl->assign('passtime', $passtime);
        $tpl->assign('username', $_SESSION['uusername']);
        $tpl->display($mobi . 'changepassword2.html');
        break;
    case 'getnewsall':

        $msql->query("select * from `{$tb_news}` where  wid in ('".$_SESSION['wid']."',0) and agent in (0,2) and ifok=1 order by time desc");
        $i = 0;
        while ($msql->next_record()) {
            $news[$i]['id'] = $i + 1;
			if($msql->f('cs')==1){
				$arr[0] = $config['thisqishu'];
				$arr[1] = $config['webname'];
				$fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='".$config['thisqishu']."'");
				$fsql->next_record();
                $arr[2]              = date("Y-m-d H:i:s", strtotime($fsql->f('opentime'))+$config['times']['o']);
                $arr[3]              = date("Y-m-d H:i:s", strtotime($fsql->f('closetime'))-$config['times']['c']-$config['userclosetime']);
				$arr[4] = $fsql->f('kjtime');
			    $news[$i]['content'] = messreplace($msql->f('content'),$arr);
			}else{
                $news[$i]['content'] =  $msql->f('content');  
                
			}
			$news[$i]['content'] = htmlspecialchars_decode($news[$i]['content']);
            $news[$i]['time'] = $msql->f('time');
            $i++;
        }
        $tpl->assign('news', $news);
        $tpl->display('news.html');
        break;
    case "getnews":
        $msql->query("select * from `$tb_news`  where  wid in ('" . $_SESSION['wid'] . "',0) and agent in (0,2) and gundong=1 and ifok=1 order by time desc");
        $i = 0;
        while ($msql->next_record()) {
            $news[$i]['id'] = $i;
            if ($msql->f('cs') == 1) {
                $arr[0] = $config['thisqishu'];
                $arr[1] = $config['webname'];
                $fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
                $fsql->next_record();
                $arr[2]              = date("Y-m-d H:i:s", strtotime($fsql->f('opentime')) + $config['times']['o']);
                $arr[3]              = date("Y-m-d H:i:s", strtotime($fsql->f('closetime')) - $config['times']['c'] - $config['userclosetime']);
                $arr[4]              = $fsql->f('kjtime');
                $news[$i]['content'] = messreplace($msql->f('content'), $arr);
            } else {
                $news[$i]['content'] = $msql->f('content');
                
            }
            $news[$i]['content'] = htmlspecialchars_decode($news[$i]['content']);
            $news[$i]['time'] = $msql->f('time');
            $i++;
        }
        
        echo json_encode($news);
        unset($news);
        break;
    case "getusermoney":
        $msql->query("select maxmoney,money,kmaxmoney,kmoney,fudong,sy,jzkmoney from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        
		$arr[0] = p1($msql->f('maxmoney'));
        $arr[1] = p1($msql->f('money'));
        $arr[2] = p1($msql->f('maxmoney') - $msql->f('money'));

        $arr[3] = p1($msql->f('kmaxmoney'));
        $arr[4] = p1($msql->f('kmoney'));
        $fsql->query("select sum(je) from `$tb_lib` where userid='{$userid}' and z=9");
        $fsql->next_record();
        $arr[5] = (float)$fsql->f(0);
        $arr[6] = substr($config['thisqishu'], -8);
		$arr[7] = p1($msql->f('sy'));
		
		echo json_encode($arr);
        break;
    case "fastje":
        $je = json_decode($_POST['je'],ture);
        $msql->query("delete from `$tb_fastje` where userid='$userid'");
        foreach($je as $k => $v){
            $msql->query("insert into `$tb_fastje` set je='$v',xsort='$k',userid='$userid'");
        }
        
    break;
}