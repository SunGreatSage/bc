<?php
error_reporting(0);
date_default_timezone_set('Asia/Shanghai');
include('../data/config.inc.php');
include('../data/db.php');
include('../global/db.inc.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../global/session.class.php');
$config['upass'] = "puhh8kik";
if ($_REQUEST['api'] == 'login') {
    $conn     = base64_decode($_REQUEST['conn']);
    $conn     = strtolower(str_replace('\'', '"', $conn));
    $conn     = json_decode($conn, true);
    $username = $conn['user'];
    $userpass = md5($conn['pass']. $config['upass']);
	$arr = array();
    if (!preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){3,10}$/", $username)) {
        $arr['status'] ='err1';
		echo json_encode($arr);
        exit;
    }
    $msql->query("select adminname from `$tb_admins` where adminname='$username' and adminpass='$userpass' ");
    $msql->next_record();
    if ($msql->f("adminname") != $username) {
        $arr['status'] ='err2';
		echo json_encode($arr);
        exit;
    }
    $psql->query("select gid,gname,fast,panstatus,otherstatus,otherclosetime,userclosetime,mnum,fenlei,ifopen,autokj,thisqishu from `$tb_game` where ifopen=1 order by xsort ");
    $i = 0;
    while ($psql->next_record()) {
        $game[$i]['gid']            = $psql->f('gid');
        $game[$i]['gname']          = $psql->f('gname');
        $game[$i]['fast']           = $psql->f('fast');
        $game[$i]['mnum']           = $psql->f('mnum');
		$game[$i]['fenlei']           = $psql->f('fenlei');
		$game[$i]['xsort']           = $psql->f('xsort');
        $game[$i]['panstatus']      = $psql->f('panstatus');
        $game[$i]['otherstatus']    = $psql->f('otherstatus');
        $game[$i]['otherclosetime'] = $psql->f('otherclosetime');
        $game[$i]['userclosetime']  = $psql->f('userclosetime');
		$game[$i]['ifopen']           = $psql->f('ifopen');
		$game[$i]['autokj']           = $psql->f('autokj');
		$game[$i]['thisqishu']           = $psql->f('thisqishu');
        $i++;
    }
        $arr['status'] ='ok';
		$arr['game'] = $game;
		$_SESSION['user'] = $username;
		echo json_encode($arr);
		

} else if ($_REQUEST['api'] == 'qishu') {
	if(!$_SESSION['user']){
        $arr[0]['status'] ='err3';
		echo json_encode($arr);
        exit;
	}
	$garr = json_decode($_REQUEST['gstr'],true);
	$game = array();
	foreach($garr as $key => $val){
	   $fsql->query("select thisqishu from `$tb_game` where gid='".$val[0]."' ");
	   $fsql->next_record();
	   $msql->query("select * from `$tb_lib` where gid='".$val[0]."' and qishu='".$val[2]."' and qishu<'".$fsql->f('thisqishu')."' order by time desc,tid desc");
	   $i=0;
	   $arr=array();
	   $tmp=array();
	   $zs=0;
	   $zje=0;
	   while($msql->next_record()){
			if($tmp['g'.$msql->f('gid')]==''){
				$tmp['g'.$msql->f('gid')] = transgame($msql->f('gid'),'gname');
			}		
			if($tmp['b'.$msql->f('gid').$msql->f('bid')]==''){
				$tmp['b'.$msql->f('gid').$msql->f('bid')] = transb8('name', $msql->f('bid'),$msql->f('gid'));
			}
			if($tmp['s'.$msql->f('gid').$msql->f('sid')]==''){
				$tmp['s'.$msql->f('gid').$msql->f('sid')] = transs8('name', $msql->f('sid'),$msql->f('gid'));
			}
			if($tmp['c'.$msql->f('gid').$msql->f('cid')]==''){
				$tmp['c'.$msql->f('gid').$msql->f('cid')] = transc8('name', $msql->f('cid'),$msql->f('gid'));
			}
			if($tmp['p'.$msql->f('gid').$msql->f('pid')]==''){
				$tmp['p'.$msql->f('gid').$msql->f('pid')] = transp8('name', $msql->f('pid'),$msql->f('gid'));
			}
			$arr[$i]['gid'] = $tmp['g'.$msql->f('gid')];
			$arr[$i]['wf'] = wf($msql->f('gid'),$tmp['b' . $msql->f('gid') . $msql->f('bid')],$tmp['s' . $msql->f('gid') . $msql->f('sid')],$tmp['c' . $msql->f('gid') . $msql->f('cid')],$tmp['p' . $msql->f('gid') . $msql->f('pid')]);
			$arr[$i]['abcd'] = $msql->f("abcd");
			$arr[$i]['je'] = $msql->f("je");
			$arr[$i]['time'] = $msql->f("time");
			$arr[$i]['qishu'] = $msql->f("qishu");
			$arr[$i]['peilv1'] = $msql->f("peilv1");
			$arr[$i]['points'] = $msql->f("points");
			$arr[$i]['tid'] = $msql->f("tid");
			if($tmp['u'.$msql->f('userid')]==''){
			   $tmp['u'.$msql->f('userid')] = transuser($msql->f('userid'),"username");
			}
			 $arr[$i]['user'] = $tmp['u'.$msql->f('userid')];
			 $zje += $msql->f('je');
			 $zs++;
			 $i++;
	   }
	   $arr[0]['zs'] = $zs;
	   $arr[0]['zje'] = $zje;
	   $game[$key]["nr"] = $arr;	  
	   $game[$key]["zs"] = $zs;
	   $game[$key]["thisqishu"] = $fsql->f('thisqishu'); 
	   $game[$key]["gname"] = $val[1];
	   $game[$key]["gid"] = $val[0];
	   $msql->query("select editstart,editend from `$tb_config`");
	   $msql->next_record();
	   if(str_replace(":","",$msql->f('editstart'))>date("His")){
	      $game[$key]["date"] = date("Ymd",time()-86400);
	   }else{
	      $game[$key]["date"] = date("Ymd",time());
	   }
	}
	$game[0]["status"] ='ok';
	$game[0]["cc"] =count($garr);
	echo json_encode($game);
} else if ($_REQUEST['api'] == 'jiaozheng') {
	if(!$_SESSION['user']){
        $arr['status'] ='err4';
		echo json_encode($arr);
        exit;
	}
	$d = $_REQUEST['d'];
	$gname = $_REQUEST['gname'];
	$msql->query("select gid,gname from `$tb_game` where `gname`='$gname'");
	$msql->next_record();
	if($msql->f("gname")!=$gname){
        $arr['status'] ='err5';
		echo json_encode($arr);
        exit;
	}
	$gid = $msql->f('gid');
	
if (!preg_match ("/\d{4}-1[0-2]|0?[1-9]-0?[1-9]|[12][0-9]|3[01]/", $d)){
   $d = date("Y-m-d");
}
	   $msql->query("select editstart,editend from `$tb_config`");
	   $msql->next_record();
	   
    $start = $d." ".$msql->f("editend");
	$ends = date("Y-m-d",strtotime($start)+86400)." ".$msql->f("editstart");
	//echo "select * from `$tb_lib` where gid='$gid' and time>='$start' and time<='$ends'";
	$msql->query("select * from `$tb_lib` where gid='$gid' and time>='$start' and time<='$ends'");
	$tmp=array();
	while($msql->next_record()){
			if($tmp['g'.$msql->f('gid')]==''){
				$tmp['g'.$msql->f('gid')] = transgame($msql->f('gid'),'gname');
			}		
			if($tmp['b'.$msql->f('gid').$msql->f('bid')]==''){
				$tmp['b'.$msql->f('gid').$msql->f('bid')] = transb8('name', $msql->f('bid'),$msql->f('gid'));
			}
			if($tmp['s'.$msql->f('gid').$msql->f('sid')]==''){
				$tmp['s'.$msql->f('gid').$msql->f('sid')] = transs8('name', $msql->f('sid'),$msql->f('gid'));
			}
			if($tmp['c'.$msql->f('gid').$msql->f('cid')]==''){
				$tmp['c'.$msql->f('gid').$msql->f('cid')] = transc8('name', $msql->f('cid'),$msql->f('gid'));
			}
			if($tmp['p'.$msql->f('gid').$msql->f('pid')]==''){
				$tmp['p'.$msql->f('gid').$msql->f('pid')] = transp8('name', $msql->f('pid'),$msql->f('gid'));
			}
			if($tmp['u'.$msql->f('userid')]==''){
			   $tmp['u'.$msql->f('userid')] = transuser($msql->f('userid'),"username");
			}

			
			$arr[$tmp['u'.$msql->f('userid')].$msql->f("tid")]['gid'] =$tmp['g'.$msql->f('gid')];
			$arr[$tmp['u'.$msql->f('userid')].$msql->f("tid")]['qishu'] = $msql->f("je");
			$arr[$tmp['u'.$msql->f('userid')].$msql->f("tid")]['je'] = $msql->f("je");
			$arr[$tmp['u'.$msql->f('userid')].$msql->f("tid")]['tid'] = $msql->f("tid");
			$arr[$tmp['u'.$msql->f('userid')].$msql->f("tid")]['user'] = $msql->f("tid");
			$arr[$tmp['u'.$msql->f('userid')].$msql->f("tid")]['time'] = $msql->f("time");
			$arr[$tmp['u'.$msql->f('userid')].$msql->f("tid")]['wf'] =  wf($msql->f('gid'),$tmp['b' . $msql->f('gid') . $msql->f('bid')],$tmp['s' . $msql->f('gid') . $msql->f('sid')],$tmp['c' . $msql->f('gid') . $msql->f('cid')],$tmp['p' . $msql->f('gid') . $msql->f('pid')]);
			
	
	}
	$arr["status"] ='ok';
	echo json_encode($arr);
}


