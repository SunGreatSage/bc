<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/adminfunc.php');
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

		userchange("更改自动降倍",$userid);
        echo 1;
        break;

   case "show":
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
                $fly[$key]['maxje']  = $msql->f('maxje');
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
                $fly[$key]['maxje']  = $msql->f('maxje');
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
        $msql->query("delete from `$tb_fly` where userid='$userid' and gid='$gid'");
        $msql->query("select pan from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $pan = json_decode($msql->f('pan'), true);
        for ($i = 0; $i < $ca; $i++) {
            $ifok  = $arr[$i]['ifok'];
            $aje   = $arr[$i]['aje'];
            $bje   = $arr[$i]['bje'];
            $ftype = $arr[$i]['ftype'];
            $maxje = $arr[$i]['maxje'];
            $sql   = "insert into  `$tb_fly` set ifok='$ifok',je='$aje',ab='A',class='$ftype',userid='$userid',gid='$gid',maxje='$maxje'";
            $msql->query($sql);
            if ($pan[$ftype]['ab'] == 1) {
                $sql = "insert into  `$tb_fly` set ifok='$ifok',je='$bje',ab='B',class='$ftype',userid='$userid',gid='$gid',maxje='$maxje'";
                $msql->query($sql);
            }
            
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
			 $fsql->query("insert into `$tb_warn` select NULL,$ngid,$userid,class,je,ks from `$tb_warn` where gid='$gid' and userid='$userid'");
			 
		 }
        echo 1;
	break;
	case "yiwotongbuauto":
         $gid = $_POST['gid'];	
		 $msql->query("select gid from `$tb_game` where fenlei=(select fenlei from `$tb_game` where gid='$gid') and gid!='$gid'");
		 while($msql->next_record()){
			 $ngid = $msql->f('gid');
			 $fsql->query("delete from `$tb_auto` where gid='$ngid' and userid='$userid'");
			 $fsql->query("insert into `$tb_auto` select NULL,$ngid,class,$userid,ifok,startje,startpeilv,addje,attpeilv,lowpeilv,stopje,ifzc,yj,qsnum,qspeilv from `$tb_auto` where gid='$gid' and userid='$userid'");
			 
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