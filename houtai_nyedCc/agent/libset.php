<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "warn":
        $game = getgamecs($userid);
        $game = getgamename($game);
        if (in_array($_REQUEST['gid'], $garr)) {
            $gid = $_REQUEST['gid'];
        }
        $msql->query("select * from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $ftype = json_decode($msql->f('ftype'), true);
        
        $gname = $msql->f('gname');
		$flname = $msql->f('flname');
		$warn  = array();
        foreach ($ftype as $key => $val) {
            $warn[$key]['name'] = $val['name'];
            $fsql->query("select je,ks from `$tb_warn` where userid='$userid' and gid='$gid' and class='$key'");
            $fsql->next_record();
            $warn[$key]['ftype'] = $key;
            $warn[$key]['je']    = pr0($fsql->f('je'));
            $warn[$key]['ks']    = pr0($fsql->f('ks'));
            $warn[$key]['gname'] = $gname;
        }
	    $tpl->assign("config", $config);
        $tpl->assign('fly', $fly);
		$tpl->assign('nums', floor(count($warn)/2));
        $tpl->assign("gid", $gid);
		$tpl->assign("flname", $flname);
		$tpl->assign("game", $game);
        $tpl->assign("warn", $warn);
        $tpl->display("warn.html");
        break;
    case "setwarn":
        $arr = str_replace('\\', '', $_POST['str']);
        $gid = $_POST['gid'];
        $arr = json_decode($arr, true);
        $ca  = count($arr);
        $msql->query("delete from `$tb_warn` where userid='$userid' and gid='$gid'");
        for ($i = 0; $i < $ca; $i++) {
            $sql = "insert into `$tb_warn` set je='" . $arr[$i]['je'] . "',ks='" . $arr[$i]['ks'] . "',gid='$gid',userid='$userid',class='" . $arr[$i]['ftype'] . "'";
            $msql->query($sql);
        }
        userchange("更改警示金额",$userid);
        echo 1;
        break;
    case 'auto':
        $game = getgamecs($userid);
        $game = getgamename($game);
        if (in_array($_REQUEST['gid'], $garr)) {
            $gid = $_REQUEST['gid'];
        }
        $msql->query("select * from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $ftype = json_decode($msql->f('ftype'), true);
        $auto  = array();
        $gname = $msql->f('gname');
		$flname = $msql->f('flname');
        foreach ($ftype as $key => $val) {
            $msql->query("select * from `$tb_auto` where class='$key' and userid='$userid' and gid='$gid' ");
            $msql->next_record();
            $auto[$key]['ifok']       = $msql->f('ifok');
			$auto[$key]['yj']       = $msql->f('yj');
			$auto[$key]['qsnum']       = $msql->f('qsnum');
			$auto[$key]['qspeilv']       = (float)($msql->f('qspeilv'));
            $auto[$key]['startje']    = pr0($msql->f('startje'));
            $auto[$key]['startpeilv'] = pr4($msql->f('startpeilv'));
            $auto[$key]['stopje']     = pr0($msql->f('stopje'));
            $auto[$key]['addje']      = pr0($msql->f('addje'));
            $auto[$key]['attpeilv']   = pr4($msql->f('attpeilv'));
            $auto[$key]['lowpeilv']   = pr4($msql->f('lowpeilv'));
            $auto[$key]['ifzc']       = $msql->f('ifzc');
            $auto[$key]['name']       = $val['name'];
            $auto[$key]['gname']      = $gname;
            $auto[$key]['ftype']      = $key;
        }
		$tpl->assign("config", $config);
        $tpl->assign('auto', $auto);
		$tpl->assign("flname", $flname);
        $tpl->assign("gid", $gid);
        $tpl->assign("game", $game);
        $tpl->display('auto.html');
        break;
    case 'setauto':
        $arr = str_replace('\\', '', $_POST['str']);
        $gid = $_POST['gid'];
        $arr = json_decode($arr, true);
        $ca  = count($arr);
        $msql->query("delete from `$tb_auto` where userid='$userid' and gid='$gid'");
        for ($i = 0; $i < $ca; $i++) {
            $ifok       = $arr[$i]['ifok'];
			$yj       = $arr[$i]['yj'];
			$qsnum       = $arr[$i]['qsnum'];
			$qspeilv      = $arr[$i]['qspeilv'];
			
            $ifzc       = $arr[$i]['ifzc'];
            $startje    = $arr[$i]['startje'];
            $startpeilv = $arr[$i]['startpeilv'];
            $stopje     = $arr[$i]['stopje'];
            $addje      = $arr[$i]['addje'];
            $attpeilv   = $arr[$i]['attpeilv'];
            $lowpeilv   = $arr[$i]['lowpeilv'];
            $ftype      = $arr[$i]['ftype'];
            $sql        = "insert into  `$tb_auto` set ifok='$ifok',yj='$yj',qsnum='$qsnum',qspeilv='$qspeilv',startje='$startje',stopje='$stopje',addje='$addje',attpeilv='$attpeilv'";
            $sql .= ",lowpeilv='$lowpeilv',startpeilv='$startpeilv',ifzc='$ifzc',class='$ftype',userid='$userid',gid='$gid'";
            $msql->query($sql);
        }
        userchange("设置自动降倍",$userid);
        echo 1;
        break;
    case "flyrecord":
        $uid = $userid;

        if (strlen($uid) != 8 | !is_numeric($uid))
            $uid = $userid;
        include("../global/Iplocation_Class.php");  
   
        $page = r1p($_REQUEST['page']);
        $msql->query("select count(id) from `$tb_user_edit` where userid='$uid' and action='自动补货'");
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $psize = $config['psize3'];
        $pcount = $rcount%$psize==0 ? $rcount/$psize : floor($rcount/$psize)+1;
        $tpl->assign('page',$page);
        $tpl->assign('rcount',$rcount);
        $tpl->assign('pcount',$pcount);
        $tpl->assign('fudong',transuser($uid,'fudong'));
        $msql->query("select * from `$tb_user_edit` where userid='$uid' and action='自动补货' order by moditime desc limit ".($page-1)*$psize.",".$psize);
        $log=array();
        $i=0;
        
        while($msql->next_record()){
            
            $log[$i]['moditime'] = $msql->f('moditime');
            
                $fsql->query("select username from `$tb_user` where userid='" . $msql->f('modisonuser') . "'");
                $fsql->next_record();
                $log[$i]['modisonuser'] = $fsql->f('username');
            
            $log[$i]['id'] = $i+1;
			if ($config['iprecord']==1){ 
            $log[$i]['ip'] = $msql->f('modiip');
            $log[$i]['addr']        = mb_convert_encoding($ips->getaddress($msql->f('modiip')),'utf-8','GBK');
			} else {
			$log[$i]['ip'] = "***";
            $log[$i]['addr'] = "***";
			}
            $log[$i]['newvalue'] = $msql->f('newvalue');
            $log[$i]['oldvalue'] = $msql->f('oldvalue');
            $log[$i]['title'] = $msql->f('title');
            $log[$i]["modiuser"] = transuser($userid2,"username");
            $i++;
        }
        
        $tpl->assign('log',$log);
        $tpl->assign('uid',$uid);
        $tpl->assign('username',transu($uid));
        $tpl->display("flyrecord.html");
    break;    
    case "show":
        $msql->query("select layer,username from `$tb_user` where userid='$userid'");
        $msql->next_record();
       
        $tpl->assign("username",$config["layer"][$msql->f("layer")-1]."（<span id='username'>".$msql->f("username")."</span>）");
        $game = getgamecs($userid);
        $game= getgamename($game);
        if (is_numeric($_REQUEST['gid']) & strlen($_REQUEST['gid']) == 3) {
            $gid = $_REQUEST['gid'];
        }
        $msql->query("select * from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $ftype = json_decode($msql->f('ftype'), true);
        $pan   = json_decode($msql->f('pan'), true);
        $fly   = array();
        $gname = $msql->f('gname');
		$flname = $msql->f('flname');
		$fenlei = $msql->f('fenlei');
        foreach ($ftype as $key => $val) {
           if ($pan[$key]['ab'] == 1) {
                $msql->query("select * from `$tb_fly` where class='$key' and userid='$userid' and gid='$gid' and ab='A' ");
                $msql->next_record();
                $fly[$key]['A']     = pr0($msql->f('je'));
                $fly[$key]['ifok']  = $msql->f('ifok');
                $fly[$key]['name']  = $val['name'];
                $fly[$key]['gname'] = $gname;
                $fly[$key]['ftype'] = $key;
                $fly[$key]['ab']    = 1;
                $msql->query("select * from `$tb_fly` where class='$key' and userid='$userid' and gid='$gid' and ab='B' ");
                $msql->next_record();
                $fly[$key]['B'] = pr0($msql->f('je'));
            } else {
                $msql->query("select * from `$tb_fly` where class='$key' and userid='$userid' and gid='$gid' and ab='A' ");
                $msql->next_record();
                $fly[$key]['A']     = pr0($msql->f('je'));
                $fly[$key]['ifok']  = $msql->f('ifok');
                $fly[$key]['name']  = $val['name'];
                $fly[$key]['gname'] = $gname;
                $fly[$key]['ftype'] = $key;
                $fly[$key]['ab']    = 0;
            }
       }
     
	    $tpl->assign("config", $config);
        $tpl->assign('fly', $fly);
		$tpl->assign('nums', floor(count($fly)/2));
        $tpl->assign("gid", $gid);
		$tpl->assign("flname", $flname);
		$tpl->assign("fenlei", $fenlei);
        $tpl->assign("game", $game);
		$msql->query("select ifexe,autofly,defaultpan,pan from `$tb_user` where userid='$userid'");
		$msql->next_record();
        $tpl->assign("ifexe", $msql->f('ifexe'));
        $tpl->assign('autofly', $msql->f('autofly'));
		$tpl->assign("defaultpan", $msql->f('defaultpan'));
		$tpl->assign('pan', json_decode($msql->f('pan'), true));
        $tpl->display('autofly.html');
        break;
    case "setautofly":
        $arr     = str_replace('\\', '', $_POST['str']);
        $gid     = $_POST['gid'];

        $autofly = $_POST['autofly'];
		$defaultpan = $_POST['defaultpan'];
        $arr     = json_decode($arr, true);
        $ca      = count($arr);
        $old = $msql->arr("select * from `$tb_fly` where userid='$userid' and gid='$gid' and ab='A' order by class",1);
        //print_r($old);
        $msql->query("delete from `$tb_fly` where userid='$userid' and gid='$gid'");
        $msql->query("select ftype,gname,pan from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $ftypearr = json_decode($msql->f('ftype'), true);
        $pan = json_decode($msql->f('pan'), true);
        $gname = $msql->f("gname");
        //print_r($arr);
        for ($i = 0; $i < $ca; $i++) {
            $v1=[];
            $v2=[];
            $ifok  = $arr[$i]['ifok'];
            $aje   = $arr[$i]['aje'];
            $bje   = $arr[$i]['bje'];
            $ftype = $arr[$i]['ftype'];
            $sql   = "insert into  `$tb_fly` set ifok='$ifok',je='$aje',ab='A',class='$ftype',userid='$userid',gid='$gid'";
            $msql->query($sql);
            //echo $ftypearr[$ftype]['bc'],'aa';
            //echo $pan[$ftypearr[$ftype]['bc']]["abcd"];
            if ($pan[$ftypearr[$ftype]['bc']]["abcd"] == 1) {
                $sql = "insert into  `$tb_fly` set ifok='$ifok',je='$bje',ab='B',class='$ftype',userid='$userid',gid='$gid'";
                $msql->query($sql);
            }
            $v2["je"] = $arr[$i]['aje'];
            $v2["gname"] = $gname;
            $v2["ftypename"] = $ftypearr[$ftype]['name'];
            $v2["ifok"] = $arr[$i]['ifok'];
            $v1["je"] = $old[$ftype]["je"];
            $v1["ifok"] = $old[$ftype]["ifok"];
            userflylog($v1,$v2);
        }
        $msql->query("update `$tb_user` set defaultpan='$defaultpan' where userid='$userid'");
        userchange("自动补货设置",$userid);
        echo 1;
        break;
		
	case "yiwotongbuwarn":
         $gid = $_POST['gid'];	
		 $msql->query("select gid from `$tb_game` where fenlei=(select fenlei from `$tb_game` where gid='$gid') and gid!='$gid'");
		 while($msql->next_record()){
			 $ngid = $msql->f('gid');
			 $fsql->query("delete from `$tb_warn` where gid='$ngid' and userid='$userid'");
			 $fsql->query("insert into `$tb_warn` select NULL,$ngid,$userid,class,je,ks from `$tb_warn` where userid='$userid' and gid='$gid'");
			 
		 }
        echo 1;
	break;
	case "yiwotongbuauto":
         $gid = $_POST['gid'];	
		 $msql->query("select gid from `$tb_game` where fenlei=(select fenlei from `$tb_game` where gid='$gid') and gid!='$gid'");
		 while($msql->next_record()){
			 $ngid = $msql->f('gid');
			 $fsql->query("delete from `$tb_auto` where gid='$ngid' and userid='$userid'");
			 $fsql->query("insert into `$tb_auto` select NULL,$ngid,class,$userid,ifok,startje,startpeilv,addje,attpeilv,lowpeilv,stopje,ifzc,yj,qsnum,qspeilv from `$tb_auto` where userid='$userid' and gid='$gid'");
			 
		 }
        echo 1;
	break;
	case "yiwotongbuautofly":
         $gid = $_POST['gid'];	
		 $msql->query("select gid from `$tb_game` where fenlei=(select fenlei from `$tb_game` where gid='$gid') and gid!='$gid'");
		 while($msql->next_record()){
			 $ngid = $msql->f('gid');
			 $fsql->query("delete from `$tb_fly` where gid='$ngid' and userid='$userid'");
			 $fsql->query("insert into `$tb_fly` select NULL,$ngid,$userid,class,ab,je,ifok,maxje from `$tb_fly` where userid='$userid' and gid='$gid'");
			 
		 }
        echo 1;
	break;
}
?>