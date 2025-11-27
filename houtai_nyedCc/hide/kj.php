<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include("../func/csfunc.php");
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
$msql->query("SHOW TABLES LIKE  '%total%'");
$msql->next_record();
$bigdata=0;
if($msql->f(0)=='x_lib_total'){
    $tb_lib = "x_lib_total";
    $bigdata=1;
}
switch ($_REQUEST['xtype']) {
    case "show":
        if ($_REQUEST['gid'] != '')
            $gid = $_REQUEST['gid'];
        $msql->query("select * from `$tb_game` where gid='$gid' ");
        $msql->next_record();
        $game                   = array();
        $game[0]['gid']         = $msql->f('gid');
        $game[0]['gname']       = $msql->f('gname');
        $game[0]['fast']        = $msql->f('fast');
        $game[0]['baostatus']   = $msql->f('baostatus');
        $game[0]['autokj']      = $msql->f('autokj');
        $game[0]['autoopenpan'] = $msql->f('autoopenpan');
        $game[0]['panstatus']   = $msql->f('panstatus');
        $game[0]['otherstatus'] = $msql->f('otherstatus');
        $game[0]['thisqishu']   = $msql->f('thisqishu');
		$game[0]['opentimekj']   = $msql->f('opentimekj');
		$game[0]['closetimekj']   = $msql->f('closetimekj');
		$game[0]['fast']   = $msql->f('fast');
		$game[0]['guanfang']   = $msql->f('guanfang');        
        $game[0]['thisbml'] = $msql->f('thisbml');
		$tpl->assign('cs', json_decode($msql->f('cs'),true));
        $msql->query("select * from `$tb_game` where gid!='$gid' and ifopen=1 order by xsort");
        $i = 1;
        while ($msql->next_record()) {
            $game[$i]['gid']   = $msql->f('gid');
            $game[$i]['gname'] = $msql->f('gname');
            $game[$i]['fast']  = $msql->f('fast');
            $i++;
        }
        $sdate = week();
		$current_date = date("Y-m-d");
        $tpl->assign("sdate", $sdate);
		$tpl->assign("current_date", $current_date);
        $tpl->assign('game', $game);
	    $tpl->assign("gid", $gid);
		$tpl->assign('config', $config);
		$game2 = getgamecs($userid);
        $game2 = getgamename($game2);
		$tpl->assign('game2', $game2);
        $tpl->display("kj.html");
        break;
	case "editguanfang":
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
		$gid   = $_POST['gid'];
		$msql->query("select cs from `$tb_game` where gid='$gid'");
		$msql->next_record();
		$cs= json_decode($msql->f('cs'),true);		
		$cs['kongje'] = $_POST['kongje'];
        $cs['zcmode'] = $_POST['zcmode'];
        $cs['cjmode'] = $_POST['cjmode'];
        $cs['xtmode'] = $_POST['xtmode'];
        $cs['zhiding'] = $_POST['zhiding'];
        $cs['zduser'] = $_POST['zduser'];
        $cs['suiji'] = $_POST['suiji'];
        $cs['ylup'] = $_POST['ylup'];
        $cs['shenglv'] = $_POST['shenglv'];
        if(!is_numeric($cs["kongje"]) || $cs["kongje"]%1!=0 || $cs["kongje"]<0){
            $cs["kongje"] = 0;
        }
        if(!is_numeric($cs["ylup"]) || $cs["ylup"]%1!=0 || $cs["ylup"]<0){
            $cs["ylup"] = 0;
        }
        if(!in_array($cs["shenglv"],[21,31,32,41,42,43,51,52,53,54,61,71,72,73,81,83,91,92])){
            $cs['shenglv'] = 31;
        }
        if(!is_numeric($cs["suiji"]) || $cs["suiji"]%1!=0 || $cs["suiji"]<50){
            $cs["suiji"] = 100;
        }
        if($cs["suiji"]>5000){
            $cs["suiji"] = 5000;
        }
        if($cs["xtmode"]==5){
            $cs["yingqs"] = 0;
            $cs["shuqs"] = 0;
        }
        if (!mb_ereg("^[\w\-\.]{2,32}$", $cs['zduser'])) {
            $cs['zduser'] = "";
            $cs['zhiding'] = 0;
        }else{
            $msql->query("select userid,username from `$tb_user` where username='".$cs['zduser']."'");
            $msql->next_record();
            if($msql->f("username")!=$cs['zduser']){
                $cs['zduser'] = "";
                $cs['zhiding'] = 0;
            }
        }        
		$cs = json_encode($cs);
		$msql->query("update `$tb_game` set cs='$cs' where gid='$gid'");

		echo 1;
		break;
    case "editkpcs":
        if ($_SESSION['admin'] != 1)
            exit;
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
        $gid   = $_POST['gid'];
        $msql->query("select cs from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $cs= json_decode($msql->f('cs'),true);      
        if($_SESSION['hides']==1){
            $cs['starttime'] = $_POST['starttime'];
            $cs['starttime2'] = $_POST['starttime2'];
            $cs['qsjg'] = $_POST['qsjg'];        
            $cs['qsnums'] = $_POST['qsnums'];
            $cs['qishunum'] = $_POST['qishunum'];
            $cs['startdate'] = $_POST['startdate'];
            $cs['startqs'] = $_POST['startqs'];
            $cs['tzqs'] = $_POST['tzqs'];
        }      
        $cs['closetime'] = $_POST['closetime'];
        $cs['tuichi'] = $_POST['tuichi'];
        $cs['tuichikp'] = $_POST['tuichikp'];
        $cs = json_encode($cs);
        $msql->query("update `$tb_game` set cs='$cs' where gid='$gid'");
        $msql->query("delete from `$tb_kj` where gid='$gid' and opentime>NOW()");
        echo 1;
        break;
    case "padd":
        $gid   = $_POST['gid'];
        $pdate = $_POST['pdate'];
        if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $pdate))
            exit;
        paddqishu($gid, $pdate);
        echo 1;
        break;
    case "add":
        $gid = $_POST['gid'];
        if ($gid == 111 | $gid == 115 | $gid == 133) {
            echo 1;
            exit;
        }
        $opentime  = $_POST['opentime'];
        $closetime = $_POST['closetime'];
        $kjtime    = $_POST['kjtime'];
        $qishu     = $_POST['qishu'];
        $msql->query("select thisbml from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $bml       = $msql->f('thisbml');
        $editstart = str_replace(':', '', $config['editstart']);
        if (str_replace(':', '', substr($kjtime, -8)) < $editstart) {
            $dates = sqldate(strtotime($kjtime));
        } else {
            $dates = substr($kjtime, 0, 10);
        }
        $sql = "insert into `$tb_kj` set kjtime='$kjtime',opentime='$opentime',closetime='$closetime',qishu='$qishu',dates='$dates',bml='$bml',gid='$gid',baostatus=1";
       // var_dump("insert into `$tb_kj` set kjtime='$kjtime',opentime='$opentime',closetime='$closetime',qishu='$qishu',dates='$dates',bml='$bml',gid='$gid',baostatus=1");
		$msql->query("select 1 from `$tb_kj` where gid='$gid' and qishu='$qishu'");
        $msql->next_record();
        if ($msql->f(0) != 1) {
            $msql->query($sql);
        }
        echo 1;
        break;
    case "getkj":
	//var_dump($config['mnum']);
        $gid      = $_POST['gid'];
        $jsstatus = $_POST['jsstatus'];
        $psize    = $_POST['psize'];
        $page     = $_POST['page'];
        $start    = $_POST['start'];
        $end      = $_POST['end'];
        $ze       = $_POST['ze'];
        //$start    = strtotime($start . ' ' . $config['editend']);
        //$end      = strtotime($end . ' ' . $config['editstart']) + 86400;
        
        //$start    = sqltime($start);
        //$end      = sqltime($end);
        $fast = transgame($gid, 'fast');
        $time = sqltime(time()+900);
        if ($fast == 1) {
            if (!is_numeric($page))
                $page = 1;
            if ($jsstatus == 0) {
                if($start==$end) $whi = " and dates='$start' and js=0 ";
                else $whi     = " and js=0 and dates>='$start' and dates<='$end'";
                $orderby = " order by gid,dates,kjtime ";
            } else if ($jsstatus == 1) {
                if($start==$end) $whi = " and dates='$start' and kjtime<'$time' ";
                else $whi     = "  and dates>='$start' and dates<='$end' and kjtime<'$time' ";
                $orderby = " order by gid,dates desc,kjtime desc ";
            } else {
                if($start==$end) $whi = " and dates='$start' ";
                else $whi     = " and dates>='$start' and dates<='$end'  ";
                $orderby = " order by gid,dates,kjtime ";
            }
        } else {
            $orderby = " order by gid,qishu  desc ";
        }
		
        //echo "select count(id) from `$tb_kj` where gid='$gid' $whi ";
        //$sqla="select count(id) from `$tb_kj` where gid='$gid' $whi ";
        $msql->query("select count(id) from `$tb_kj` where gid='$gid' $whi ");
        $msql->next_record();
        $rcount = $msql->f(0);
        $msql->query("select * from `$tb_kj` where gid='$gid' $whi $orderby limit " . (($page - 1) * $psize) . ",$psize");
        $i              = 0;
        $kj             = array();
        $otherclosetime = $config['otherclosetime'];
        $tmp            = array();
        while ($msql->next_record()) {
            if ($tmp['g' . $msql->f('gid')] == '') {
                $tmp['g' . $msql->f('gid')] = transgame($msql->f('gid'), 'sgname');
            }
            if ($ze == 1) {
                $fsql->query("select 1 from `$tb_lib` where gid='" . $msql->f('gid') . "' and qishu='" . $msql->f('qishu') . "'  limit 1");
                $fsql->next_record();
                if ($fsql->f(0) != 1) {
                    continue;
                }
            }
            $kj[$i]['gname']          = $tmp['g' . $msql->f('gid')];
            $kj[$i]['gid']            = $msql->f('gid');
            $kj[$i]['bml']            = $msql->f('bml');
            $kj[$i]['oy']             = substr($msql->f('opentime'), 0, 4);
            $kj[$i]['cy']             = substr($msql->f('closetime'), 0, 4);
            $kj[$i]['ky']             = substr($msql->f('kjtime'), 0, 4);
            $kj[$i]['opentime']       = date("m-d H:i:s", strtotime($msql->f('opentime')));
            $kj[$i]['closetime']      = date("m-d H:i:s", strtotime($msql->f('closetime')));
            $kj[$i]['kjtime']         = date("m-d H:i:s", strtotime($msql->f('kjtime')));
            $kj[$i]['otherclosetime'] = date("m-d H:i:s", strtotime($msql->f('closetime')) - $otherclosetime);
            $kj[$i]['baostatus']      = $msql->f('baostatus');
            $kj[$i]['js']             = $msql->f('js');
            $kj[$i]['qishu']          = $msql->f('qishu');
            $kj[$i]['lib']            = getlibje($msql->f('gid'), $msql->f('qishu'));
            for ($j = 1; $j <= $config['mnum']; $j++) {
                $kj[$i]['m' . $j] = $msql->f('m' . $j);
            }
            $i++;
        }
	//	var_dump($config['mnum']);
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : (1 + ($rcount - $rcount % $psize) / $psize);
		
        echo json_encode(array(
            "kj" => $kj,
            'pcount' => $pcount,
            'rcount' => $rcount,
            'mnum' => $config['mnum']
        ));
        break;
    case "upkj":
        //error_reporting(E_ALL);
        include_once("../func/csfunc.php");
        include("../func/self.php");
        $gid   = $_REQUEST['gid'];
        $qishu = $_REQUEST['qishu'];
        $qishu = explode('|', $qishu);
        $cq    = count($qishu);

        $mnum = transgame($gid, 'mnum');
        $guanfang = transgame($gid, 'guanfang');
        $msql->query("select cs,mtype,ztype,fenlei from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $cs   = json_decode($msql->f('cs'), true);
        $mtype = json_decode($msql->f('mtype'), true);
        $ztype = json_decode($msql->f('ztype'), true);
        $fenlei = $msql->f("fenlei");

        for ($i = 0; $i < $cq; $i++) {
            if ($qishu[$i] != '') {
                if ($guanfang == 1 && $cs['cjmode']==1) {
                    $msql->query("select m".$mnum." as mm,kjtime from `$tb_kj` where qishu='" . $qishu[$i] . "' and gid='$gid'");
                    $msql->next_record();
                    //if($msql->f('mm')!='') continue;
                    if(strtotime($msql->f('kjtime'))>=time()) continue;
                    $ma = calcmoni($fenlei,$gid,$cs,$qishu[$i],$mnum,$ztype,$mtype);
                    if ($ma[0] == ''){
                        continue;
                    }                   
                    $sql = "update `$tb_kj` set ";
                    for ($j = 1; $j <= $mnum; $j++) {
                        if ($j > 1)
                            $sql .= ",";
                        $sql .= "m" . $j . "='" . $ma[$j - 1] . "'";
                    }
                    $sql .= " where qishu='" . $qishu[$i] . "' and gid='$gid'";
                   
                    $msql->query($sql);
                    $ma = calc($fenlei,$gid,$cs,$qishu[$i],$mnum,$ztype,$mtype);
                    continue;
                   
                }
                $qs = formatqs($gid, $qishu[$i]);
                if ($gid == 163) {
                    $ma = file_get_contents("http://" . $config['kjip'] . "&gid=161&qishu=" . $qs);
                    
                    $ma = json_decode($ma, true);
                    if (!is_array($ma[0]['m'])) {
                        $ma[0]['m'] = explode(',', $ma[0]['m']);
                    }
                    if ($ma[0]['m'][0] == '')
                        continue;
                    $m   = xy28kj($ma[0]['m']);
                    $sql = "update `$tb_kj` set ";
                    for ($j = 1; $j <= 3; $j++) {
                        $sql .= "m" . $j . "='" . $m[$j - 1] . "',";
                    }
                    $sql .= "kjtime='" . $ma[0]['kjtime'] . "' where qishu='" . $qishu[$i] . "' and gid='$gid'";
                } else {         
                    $ma = file_get_contents("http://" . $config['kjip'] . "&gid=$gid&qishu=" . $qs);
        
                    if(strpos($ma,"error")!== false){
                         continue;
                    }

                    //echo "http://" . $config['kjip'] . "&gid=$gid&qishu=" . $qs;
                    //echo $ma;exit;
                    $ma = json_decode($ma, true);
                    if (!is_array($ma[0]['m'])) {
                        $ma[0]['m'] = explode(',', $ma[0]['m']);
                    }
                    if ($ma[0]['m'][0] == '')
                        continue;
                    $sql = "update `$tb_kj` set ";
                    for ($j = 1; $j <= $mnum; $j++) {
						if($j>1) $sql .= ",";
                        $sql .= "m" . $j . "='" . $ma[0]["m"][$j - 1] . "'";
                    }
                    if($gid==162 || $gid==109 || $gid==175 || $gid==131){
                        $sql .= " where qishu='" . $qishu[$i] . "' and gid='$gid'";
                    }else{
                        $sql .= ",kjtime='" . $ma[0]['kjtime'] . "' where qishu='" . $qishu[$i] . "' and gid='$gid'";                    
                    }
                }
                $msql->query($sql);
            }
        }
        echo 1;
        break;
    case "setthisqishu":
        $gid   = $_POST['gid'];
        $qishu = $_POST['qishu'];
        $msql->query("update `$tb_game` set thisqishu='$qishu' where gid='$gid'");
		$cacheKeys = $redis->keys("zd:{$gid}*");
        $redis->del($cacheKeys);
        echo 1;
        break;
    case "delall":
        if ($_SESSION['admin'] != 1)
            exit;
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
        $gid   = $_POST['gid'];
        $qishu = $_POST['qishu'];
        include('../data/cuncu.php');
		//var_dump($deletestr1);
        //$kksql->query($deletestr1);
        $msql->query("delete from `$tb_kj` where instr('$qishu',qishu) and gid='$gid'");
        $msql->query("delete from `$tb_lib` where instr('$qishu',qishu) and gid='$gid'");
        //$msql->query("delete from `$tb_z` where instr('$qishu',qishu) and gid='$gid'");
       // $kksql->query($deletestr1);
        echo 1;
        break;
      case "delbao":
        if ($_SESSION['admin'] != 1)
            exit;
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
        $gid   = $_POST['gid'];
        $qishu = $_POST['qishu'];
        include('../data/cuncu.php');
        //$kksql->query($deletestr);
       // $bigdata==1 && $kksql->query($tdeletestr);
        $msql->query("delete from `$tb_lib` where instr('$qishu',qishu) and gid='$gid'");
        //$msql->query("delete from `$tb_z` where instr('$qishu',qishu) and gid='$gid'");
       // $kksql->query($deletecc);
       // $bigdata==1 && $kksql->query($tdeletecc);
        echo 1;
        break;
    case "deldate":
        if ($_SESSION['admin'] != 1)
            exit;
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
        
        $gid     = $_POST['gid'];
        $start   = $_POST['start'];
        $end     = $_POST['end'];
        $t       = $_POST['t'];
        $allgame = $_POST['allgame'];
        //$start   = strtotime($start . ' ' . $config['editend']);
        //$end     = strtotime($end . ' ' . $config['editstart']) + 86400;
        //$start   = sqltime($start);
        //$end     = sqltime($end);
        if ($allgame == 0) {
            $gamestr           = "and  `$tb_kj`.gid='$gid' ";
            $gamearr           = array();
            $gamearr[0]        = array();
            $gamearr[0]['gid'] = $gid;
        } else {
            $gamearr = getgame();
        }
        $cg = count($gamearr);
        include('../data/cuncu.php');
        //$kksql->query($deletestr);
      //  $bigdata==1 && $kksql->query($tdeletestr);
        for ($i = 0; $i < $cg; $i++) {
            if ($gamearr[$i]['gid'] == 100 & $cg > 1)
                continue;
            $gamestr = " gid='" . $gamearr[$i]['gid'] . "' ";
            if ($t == 1) {
                $whi = " $gamestr and dates>='$start' and dates<='$end' ";
            } else if ($t == 2) {
                $whi = " $gamestr and dates<='$end' ";
            }
            if ($t == 1 | $t == 2) {
                $msql->query("delete from `$tb_lib` where $whi");
                $msql->query("delete from `$tb_kj`  where $whi ");
				//var_dump("delete from `$tb_kj` where $whi");
            }
        }
		var_dump("delete from `$tb_lib` where $whi");
      //  $kksql->query($deletecc);
      //  $bigdata==1 && $kksql->query($tdeletecc);
        echo 1;
        break;
    
    case "changebaos":
        $gid   = $_POST['gid'];
        $qishu = $_POST['qishu'];
        $msql->query("update `$tb_kj` set baostatus=if(baostatus=1,0,1) where gid='$gid' and qishu='$qishu'");
        $msql->query("select baostatus from `$tb_kj` where  gid='$gid' and qishu='$qishu'");
        $msql->next_record();
        $bs = $msql->f('baostatus');
        $msql->query("update `$tb_lib` set bs='$bs' where gid='$gid' and qishu='$qishu'");
        echo $bs;
        break;
    case "changebaostatus":
        $gid       = $_POST['gid'];
        $start     = $_POST['start'];
        $end       = $_POST['end'];
        $baostatus = $_POST['baostatus'];
        $start     = strtotime($start . ' ' . $config['editend']);
        $end       = strtotime($end . ' ' . $config['editstart']) + 86400;
        $msql->query("update `$tb_kj` set baostatus='$baostatus' where gid='$gid' and kjtime>=$start and kjtime<=$end");
        $msql->query("update `$tb_lib` set bs='$baostatus' where gid='$gid' and time>=$start and time<=$end");
        echo 1;
        break;
     case "ckj" :
        $gid       = $_POST['gid'];
        $qishu = intval($_POST['qishu']);
        $msql->query("update `$tb_kj` set m1='',m2='',m3='',m4='',m5='',m6='',m6='',m7='',m8='',m9='',m10='',m11='',m12='',m13='',m14='',m15='',m16='',m16='',m17='',m18='',m19='',m20='',js=0 where gid='$gid' and qishu='$qishu'");
        echo 1;
    break;
    case "changejs":
        $gid   = $_POST['gid'];
        $qishu = $_POST['qishu'];
        $msql->query("select js from `$tb_kj` where gid='$gid' and qishu='$qishu'");
        $msql->next_record();
        $js = $msql->f('js');
        if ($js == 0) {
            echo $js;
            exit;
        }
        include('../data/cuncu.php');
        $kksql->query($updatestr);
        $msql->query("update `$tb_lib` set z=9 where gid='$gid' and qishu='$qishu'");
        $kksql->query($updatecc);
        
        $msql->query("update `$tb_kj` set js=0 where gid='$gid' and qishu='$qishu'");
        $msql->query("delete from `$tb_z` where gid='$gid' and qishu='$qishu'");
        jiaozhengedu();
        echo 0;
        break;
    case "updatestatus":
        $type = $_POST['type'];
        $gid  = $_POST['gid'];
        $msql->query("update `$tb_game` set $type=if($type=1,0,1) where gid='$gid'");
        echo 1;
        break;
    
    case "editkj":
        $gid       = $_POST['gid'];
        $qishu     = $_POST['qishu'];
        $kjtime    = $_POST['kjtime'];
        $closetime = $_POST['closetime'];
        $opentime  = $_POST['opentime'];
        $m         = str_replace('\\', '', $_POST['em']);
        $m         = json_decode($m, true);
        $m1 = array_filter($m); 
        $m2 = array_unique($m1);
        if($config['mnum']==10 && count($m1)!=count($m2)){
            $m=[];
        }
        $mstr      = '';
        switch ($config['mnum']) {
            case 3:
                if($config['fenlei']==163){
                    $arr   = [0,1,2,3,4,5,6,7,8,9];
                }else{
                    $arr   = [1,2,3,4,5,6];
                }
                break;
            case 5:
                   $arr   = [0,1,2,3,4,5,6,7,8,9];
                break;
            case 8:
                   $arr   = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20'];
                break;
            case 10:
                   $arr   = ['01','02','03','04','05','06','07','08','09','10'];
            break;   
            case 20:

        for($i=1;$i<=80;$i++){
            if($i<10){
                $arr[$i-1] = '0'.$i;
            }else{
                $arr[$i-1] = $i;
            }
        }


            break;
            case 7:

        for($i=1;$i<=49;$i++){
            if($i<10){
                $arr[$i-1] = '0'.$i;
            }else{
                $arr[$i-1] = $i;
            }
        }

            break; 
        }
        foreach($m as $k => $v){
            if($config['fenlei']==103 || $config['fenlei']==107 || $config['fenlei']==100) {
                if(strlen($v)==1 && $v!=""){
                    $m[$k] = '0'.$v;
                }
            }
        }
        //print_r($m);
        foreach($m as $k => $v){
            if(!in_array($v, $arr) && $v!="" ){
                $m=[];
                break;
            }
            if(!is_numeric($v) && $v!=""){
                $m=[];
                break;
            }
        }
        // print_r($m);
        for ($i = 0; $i < $config['mnum']; $i++) {
            if ($m[$i] == '') {
                if($config['fenlei']==151){
                    $m[$i] = $arr[rand(0, 5)];
                }else if ($config['mnum'] == 3) {
                    $m[$i] = $arr[rand(0, 9)];
                } else if ($config['mnum'] == 5) {
                    $m[$i] = $arr[rand(0, 9)];
                } else if ($config['mnum'] == 8) {
                    $m[$i] = randm($m, $arr, $config['mnum'],20);
                } else if ($config['mnum'] == 10) {
                    $m[$i] = randm($m, $arr, $config['mnum'],10);
                }else if ($config['mnum'] == 7) {
                   /* $m[$i] = randm($m, $arr, $config['mnum'],49);*/
                }else if ($config['mnum'] == 20) {
                    $m[$i] = randm($m, $arr, $config['mnum'],80);
                }
            }
            
            $mstr .= ",m" . ($i + 1) . "='" . $m[$i] . "'";
        }

        $year = date("Y");
        //if (strtotime($kjtime) < time()) {
            $sql = "update `$tb_kj` set kjtime='$kjtime',closetime='$closetime',opentime='$opentime'" . $mstr;
        //} else {
            //$sql = "update `$tb_kj` set kjtime='$kjtime',closetime='$closetime',opentime='$opentime'";
        //}
        $sql .= " where qishu='$qishu' and gid='$gid' ";
        if ($msql->query($sql)) {
            include("../func/search.php");
            searchqishu($gid, 60, 1);

            // ✅ 新增：手动开奖后自动触发结算
            // 检查开奖号码是否填写完整（第一个号码不为空）
            if ($m[0] != '') {
                // 检查该期是否已经结算过
                $psql->query("select js from `$tb_kj` where gid='$gid' and qishu='$qishu'");
                $psql->next_record();

                // 如果未结算（js != 1），则自动触发结算
                if ($psql->f('js') != 1) {
                    set_time_limit(0);  // 结算可能耗时较长，取消时间限制
                    include_once("../func/self.php");

                    // 获取游戏配置（用于结算计算）
                    $fsql->query("select fenlei,cs,mtype,ztype,mnum from `$tb_game` where gid='$gid'");
                    $fsql->next_record();
                    $fenlei_js = $fsql->f("fenlei");
                    $mnum_js = $fsql->f('mnum');
                    $cs_js = json_decode($fsql->f("cs"),true);
                    $ztype_js = json_decode($fsql->f("ztype"),true);
                    $mtype_js = json_decode($fsql->f("mtype"),true);

                    // 调用结算函数（自动计算中奖、派奖、更新用户余额）
                    calc($fenlei_js,$gid,$cs_js,$qishu,$mnum_js,$ztype_js,$mtype_js,true);
                }
            }

            echo 1;
        }
        break;
    case "kjjs":
        set_time_limit(0);
        //error_reporting(E_ALL);
        include("../func/self.php");
        $gid   = $_REQUEST['gid'];
        $qishu = $_REQUEST['qishu'];
        $msql->query("select fenlei,cs,mtype,ztype,mnum from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $fenlei = $msql->f("fenlei");
        $mnum = $msql->f('mnum');
        $cs = json_decode($msql->f("cs"),true);
        $ztype = json_decode($msql->f("ztype"),true);
        $mtype = json_decode($msql->f("mtype"),true);
        $val =  calc($fenlei,$gid,$cs,$qishu,$mnum,$ztype,$mtype,true);
        echo $val;
        break;
    case "xtkj";
        $rs  = $msql->arr("select bid,sid,cid,pid,name from `$tb_play` where gid='107' and bid!=26000000 and bid!=23378879 order by bid,xsort",1);
        //echo json_encode($rs);
        $arr  =["aa"=>["cc"=>11]];
        //echo json_encode($arr); 
        //exit;
        set_time_limit(0);
        include("../func/self.php");
        $gid   = $_REQUEST['gid'];
        $qishu = $_REQUEST['qishu'];
        $msql->query("select fenlei,cs,mtype,ztype,mnum from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $fenlei = $msql->f("fenlei");
        $mnum = $msql->f('mnum');
        $cs = json_decode($msql->f("cs"),true);
        $ztype = json_decode($msql->f("ztype"),true);
        $mtype = json_decode($msql->f("mtype"),true);
        $val =  calcmoni($fenlei,$gid,$cs,$qishu,$mnum,$ztype,$mtype);
        print_r($val);
        $val =  calc($fenlei,$gid,$cs,$qishu,$mnum,$ztype,$mtype);
        echo json_encode($val);
    case "searchqishu":
        //error_reporting(E_ALL);
        include("../func/search.php");
        
        echo $gid = $_REQUEST['gid'];
        
        searchqishu($gid, 60, 1);
        break;
    case "exkj":
        $gid  = $_REQUEST['gid'];
        $data = $_REQUEST['data'];
        $data = explode("\n", $data);
        $mnum = transgame($gid, 'mnum');
        foreach ($data as $val) {
            $v     = explode(',', $val);
            $qishu = $v[0];
            if (!is_numeric($qishu))
                break;
            $sql = '';
            for ($i = 1; $i <= $mnum; $i++) {
                if ($i > 1)
                    $sql .= ",";
                $sql .= "m" . $i . "='" . $v[$i] . "'";
            }
            $msql->query("update `$tb_kj` set $sql where qishu='$qishu' and gid='$gid'");
            //echo "update `$tb_kj` set $sql where qishu='$qishu' and gid='$gid'","<BR />";
        }
        echo 1;
        break;
    case "attpeilvs":
        attpeilvs(100);
        break;
    case "attpeilv":
        attpeilv($_REQUEST['gid']);
        break;
    case "kjxx":
        include('../global/page.class.php');
        $tztype   = $_POST['tztype'];
        $qishu    = $_POST['qishu'];
        $orderby  = $_POST['orderby'];
        $sorttype = $_POST['sorttype'];
        $wh       = " gid='$gid'  and qishu='$qishu' ";
        
        if ($tztype != 2) {
            $wh .= " and xtype='$tztype' ";
        }
        
        
        $zcstr = "zc0";
        $sql   = " select count(id) from `$tb_lib` where  $wh ";
        $msql->query($sql);
        $msql->next_record();
        $rcount   = pr0($msql->f(0));
        $psize    = $config['psize1'];
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        
        $pstr = $page->show(6);
        
        $sql = " select * from `$tb_lib` where $wh ";
        if ($orderby == 'time') {
            $sql .= " order by time $sorttype,id $sorttype";
        } else {
            $sql .= " order by $zcstr*je $sorttype,id $sorttype";
        }
        $sql .= " limit " . ($thispage - 1) * $psize . "," . $psize;
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='kjxx".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu=0");
                $tmp['jj' . $msql->f('userid')] = 1;
            }
            /***********HELLO*******/
            $tz[$i]['qishu']  = $msql->f('qishu');
            $tz[$i]['je']     = (float) $msql->f('je');
            $tz[$i]['zcje']   = (float) pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            
            $tz[$i]['points'] = (float) $msql->f('points');
            
            /*********************HELLO***************/
            if (in_array($msql->f('userid'), $poarr)) {
                if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $tz[$i]['points'] -= 10;
                }
            }
            /*********************HELLO***************/
            
            if ($tmp['g' . $msql->f('gid')] == '') {
                $fsql->query("select gname,fenlei from `$tb_game` where gid='".$msql->f('gid')."'");
                $fsql->next_record();
                $tmp['g' . $msql->f('gid')] = $fsql->f('gname');
                $tmp['f' . $msql->f('gid')] = $fsql->f('fenlei');
            }
            if ($tmp['b' . $msql->f('gid') . $msql->f('bid')] == '') {
                $tmp['b' . $msql->f('gid') . $msql->f('bid')] = transb8('name', $msql->f('bid'), $msql->f('gid'));
            }
            if ($tmp['s' . $msql->f('gid') . $msql->f('sid')] == '') {
                $tmp['s' . $msql->f('gid') . $msql->f('sid')] = transs8('name', $msql->f('sid'), $msql->f('gid'));
            }
            if ($tmp['c' . $msql->f('gid') . $msql->f('cid')] == '') {
                $tmp['c' . $msql->f('gid') . $msql->f('cid')] = transc8('name', $msql->f('cid'), $msql->f('gid'));
            }
            if ($tmp['p' . $msql->f('gid') . $msql->f('pid')] == '') {
                $tmp['p' . $msql->f('gid') . $msql->f('pid')] = transp8('name', $msql->f('pid'), $msql->f('gid'));
            }
            $tz[$i]['con']   = $msql->f('content');
            $tz[$i]['wf']    = wf($tmp['f' . $msql->f('gid')], $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            $tz[$i]['time']  = $msql->f('time');
            $tz[$i]['z']     = $msql->f('z');
            $tz[$i]['gname'] = $tmp['g' . $msql->f('gid')];
            $tz[$i]['xtime'] = substr($msql->f('time'), -8);
            $tz[$i]['user']  = transu($msql->f('userid'));
            
            $i++;
        }
        $e = array(
            "tz" => $tz,
            "page" => $pstr,
            "tztype" => $tztype,
            "sql" => $sql
        );
        echo json_encode($e);
        unset($e);
        
        unset($tmp);
        break;
}
function rm($v)
{
    if (!is_numeric($v))
        return 49;
    if ($v < 10 & strlen($v) == 1)
        $v = '0' . $v;
    return $v;
}
function closepan()
{
    global $tsql, $tb_config;
    $tsql->query("update `$tb_config` set tepan=0,otherpan=0,autoopenpan=0");
    echo 1;
}
?>