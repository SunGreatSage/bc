<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');

if($_SESSION['guest']==1){
  echo outjs("请退出试用帐号，并注册！如有任何问题，请联系客服人员！");
   exit;
}

switch($_REQUEST['xtype']){
	case "ucenter":
	   $msql->query("select * from `$tb_user` where userid='$userid'");
	   $msql->next_record();
	   $tpl->assign("username",$msql->f('username'));
	   $tpl->assign("xing",substr($msql->f('tname'),0,3));
	   $tpl->assign("tname",substr($msql->f('tname'),0,3).'**');
	   $tpl->assign("sex",$msql->f('sex'));
	   $tpl->assign("birthday",$msql->f('birthday'));
	   $tpl->assign("tel",$msql->f('tel'));
	   $tpl->assign("qq",$msql->f('qq'));
	   $tpl->assign("shengshi",$msql->f('shengshi'));
       $tpl->assign("street",$msql->f('street'));
	   $tpl->assign("shr",$msql->f('shr'));
		$msql->query("select kfurl from `$tb_config`");
		$msql->next_record();
		$tpl->assign('kfurl', $msql->f('kfurl'));
	   $tpl->display("mem_ucenter.html");
	break;
	case "setuinfo":
	  $tel     = strip_tags($_POST['tel']);
	  $qq     = strip_tags($_POST['qq']);
	  $sex     = strip_tags($_POST['sex']);
	  $birthday     = strip_tags($_POST['birthday']);
	  $shengshi    = strip_tags($_POST['shengshi']);
	  $street     = strip_tags($_POST['street']);
	  $shr     = strip_tags($_POST['shr']);
	  $sql = "update `$tb_user` set tel='$tel',qq='$qq',sex='$sex',birthday='$birthday',shengshi='$shengshi',street='$street',shr='$shr' where userid='$userid'";
	  $msql->query($sql);
	  echo 1;
	break;
	case "notices":
	   $msql->query("select * from `$tb_user` where userid='$userid'");
	   $msql->next_record();
	   $tpl->assign("xing",substr($msql->f('tname'),0,3));
	   $tpl->assign("tname",substr($msql->f('tname'),0,3).'**');
	   $tpage=r1p($_REQUEST['tpage']);
	   $psize = 6;
	   $msql->query("select count(id) from `$tb_notices` where userid='$userid'");
	   $msql->next_record();
	   $rcount = $msql->f(0);
	   $pcount = $rcount%$psize==0 ? $rcount/$psize : (($rcount - $rcount%$psize)/$psize)+1;
	   $msql->query("select * from `$tb_notices` where userid='$userid' order by time desc limit ".(($tpage-1)*$psize).",".$psize);
	   $news = array();
	   $i=0;
	   while($msql->next_record()){
		   $news[$i]['title'] = $msql->f('title');
		   $news[$i]['content'] = $msql->f('content');
		   $news[$i]['time'] = $msql->f('time');
		   $news[$i]['du'] = $msql->f('du');
		   $news[$i]['id'] = $msql->f('id');
		   $i++;		   
	   }
	   $msql->query("select count(id) from `$tb_notices` where userid='$userid' and du=0");
	   $msql->next_record();
	   $arr['news'] = $msql->f(0);
	   
	   for($i=1;$i<=$pcount;$i++){
	      $parr[] = $i;
	   }
	   
	   $arr['news'] = $news;
	   $arr['rcount'] = $rcount;
	   $arr['pcount'] = $pcount;
	   $arr['tpage'] = $tpage;
	   $arr['parr'] = $parr;
	   echo json_encode($arr);
	break;
	case "chdu":
	   $id     = strip_tags($_POST['id']);
	   $msql->query("update `$tb_notices` set du=1 where userid='$userid' and id='$id'");
	   echo 1;
	break;
	case "zxcz":
	   
	   $msql->query("select bankonline,bankatm,weixin,alipay from `$tb_config`");
	   $msql->next_record();
	   $tpl->assign("bankonline",$msql->f('bankonline'));
	   $tpl->assign("bankatm",$msql->f('bankatm'));
	   $tpl->assign("weixin",$msql->f('weixin'));
	   $tpl->assign("alipay",$msql->f('alipay'));
	   $bank = getbank();
	   $tpl->assign("bank",$bank);
	   
	   $msql->query("select * from `$tb_banknum` where userid='99999999' and ifok=1");
	   $banks= array();
	   $i=0;
	   while($msql->next_record()){
		   $fsql->query("select * from `$tb_bank` where bankid='".$msql->f('bankid')."'");
		   $fsql->next_record();
		   $banks[$i]['bankid'] = $fsql->f("bankid");
		   $banks[$i]['bankname'] = $fsql->f("bankname");
		   $banks[$i]['en'] = $fsql->f("en");
		   $banks[$i]['id'] = $msql->f("id");
		   $banks[$i]['name'] = $msql->f("name");
		   $banks[$i]['num'] = $msql->f("num");
		   $banks[$i]['kaihuhang'] = $msql->f("kaihuhang");
		   $banks[$i]['pass'] = rand(11119,99999);
		   
	       $i++;
	   }
	   $tpl->assign("banks",$banks);
	   
	   $fsql->query("select tname,kmoney,lastlogintime,username from `$tb_user` where userid='$userid'");
	   $fsql->next_record();
	   $tpl->assign("username",$fsql->f('username'));
	   $tpl->assign("xing",substr($fsql->f('tname'),0,3));
	   $tpl->assign("tname",substr($fsql->f('tname'),0,3).'**');
	   $tpl->assign("money",$fsql->f('kmoney'));
	   $tpl->assign("lastlogintime",$fsql->f("lastlogintime"));
	   
	   $tpl->display("mem_zxcz.html");
	break;
	case "zxczatm":
	   $id = trim($_POST['cardid']);
	   $msql->query("select * from `$tb_banknum` where id='$id' and userid='99999999'");
	   $msql->next_record();
	   if($msql->f('id')=='') exit;
		   $fsql->query("select * from `$tb_bank` where bankid='".$msql->f('bankid')."'");
		   $fsql->next_record();
		   $tpl->assign("bankid",$fsql->f("bankid"));
		   $tpl->assign("bankname",$fsql->f("bankname"));
		   $tpl->assign("en",$fsql->f("en"));
		   $tpl->assign("num",$msql->f("num"));
		   $tpl->assign("name",$msql->f("name"));
		   $tpl->assign("kaihuhang",$msql->f("kaihuhang"));
		   $tpl->assign("id",$msql->f("id"));
		   $_SESSION['bankid']  = $msql->f('id');
		   $pass = rand(11119,99999);
		   $_SESSION['pass'] = $pass;		   
		   $tpl->assign("pass",$pass);

	   $tpl->display("mem_zxczatm.html");
	break;
	case "zxtk":
	   $bank = getbank();
	   $tpl->assign("bank",$bank);
	   $msql->query("select * from `$tb_banknum` where userid='$userid' and ifok=1");
	   $msql->next_record();
	   $havebank=0;
	   if($msql->f("id")!='') $havebank=1;
	   $tpl->assign("havebank",$havebank);
	   $fsql->query("select * from `$tb_bank` where bankid='".$msql->f("bankid")."'");
	   $fsql->next_record();
	   $tpl->assign("bankname",$fsql->f("bankname"));
	   $tpl->assign("en",$fsql->f("en"));
	   $tpl->assign("cardid",$msql->f('id'));
	   $tpl->assign("lastcard4",substr($msql->f('num'),-4));
	   $fsql->query("select tname,kmoney,lastlogintime,username from `$tb_user` where userid='$userid'");
	   $fsql->next_record();
	   $tpl->assign("username",$fsql->f('username'));
	   $tpl->assign("xing",substr($fsql->f('tname'),0,3));
	   $tpl->assign("tname",substr($fsql->f('tname'),0,3).'**');
	   $tpl->assign("money",$fsql->f('kmoney'));
	   $tpl->assign("lastlogintime",$fsql->f("lastlogintime"));
	   $tpl->display("mem_zxtk.html");
	break;
	case "tk":
	   $money = trim($_POST['money']);
	   $bankpass  = trim($_POST['bankpass']);
	   $bank = trim($_POST['bank']);
	   if(!is_numeric($money) | $money=='' | $money%1!=0){
	       echo 4;
		   exit;
	   }
	   if($money<100){
	      echo 3;
		  exit;
	   }
	   $msql->query("select kmoney,kmaxmoney from `$tb_user` where userid='$userid'");
	   $msql->next_record();	   
	   if($msql->f('kmoney')<$money){
	       echo 2;
		   exit;
	   }
	   $kmoney = $msql->f('kmoney');
	   
	   $msql->query("select 1 from `$tb_lib` where userid='$userid' and z=9");
	   $msql->next_record();
	   if($msql->f(0)==1){
	       echo 6;
		   exit;
	   }
	   
	   $msql->query("select * from `$tb_banknum` where id='$bank' and userid='$userid'");
	   $msql->next_record();
	   if($msql->f('bankpass')!=$bankpass  | $msql->f('bankpass')==''){
	       echo 5;
		   exit;
	   }
	   $fsql->query("select * from `$tb_bank` where bankid='".$msql->f("bankid")."'");
	   $fsql->next_record();
	   $bank = $fsql->f('bankname');
	   $uname = $msql->f("name");
	   $unum = $msql->f("num");
       $fs="bankatm";
	   $sxfei=0;
	   $sql = "insert into `$tb_money` set userid='$userid',mtype=1,money='$money',fs='bankatm',bank='$bank',uname='$uname',unum='$unum',status=0,tjid='$userid',tjtime=NOW()";
	   if($fsql->query($sql)){	        
			$je = $kmoney-$money;
			userchange("提取现金额度".abs($money)."!原额度".$kmoney.",现额度".$je."",$userid);	
			$fsql->query("update `$tb_user` set kmaxmoney='$je',kmoney='$je',ftime=NOW();");
	   }
	   echo 1;	   
	break;
	case "jyjl":
        $upage    = $_REQUEST['upage'];
        $status   = $_REQUEST['status'];
        $mtype   = $_REQUEST['mtype'];
		$sdate = trim($_REQUEST['sdate']);
		$edate = trim($_REQUEST['edate']);

        if($mtype!='' & ($mtype==0 | $mtype==1)  & $mtype!='undefined'){
		    $whi .= " and mtype='$mtype' ";
		}		
        if($status!='' & ($status==0 | $status==1 | $status==2 )  & $status!='undefined'){
		    $whi .= " and status='$status' ";
		}

		if($sdate!='' & $edate!='' & $sdate!='undefined'  & $edate!='undefined'){
		   $sdate=rdates($sdate)." 00:00:00";
		   $edate=rdates($edate)." 23:59:59";
		   $whi .= " and tjtime>='$sdate' and tjtime<='$edate' ";
		}
        $msql->query("select count(id) from `$tb_money` where userid='$userid' " . $whi);
        $msql->next_record();
        $rcount = $msql->f(0);
        $psize  = $config['psize1'] = 5;
        $upage  = r1p($upage);
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize + 1);
        if ($upage > $pcount)
            $upage = 1;
        if ($upage < 1)
            $upage = 1;
        $tpl->assign("rcount", $rcount);
        $tpl->assign("pcount", $pcount);
        $tpl->assign("upage", $upage);
		
        $msql->query("select * from `$tb_money` where userid='$userid' " . $whi . " order by tjtime desc limit " . ($upage - 1) * $psize . "," . $psize);
        $marr = array();
        $i    = 0;
        while ($msql->next_record()) {
			$marr[$i]['status'] = moneystatus($msql->f("status"));
			$marr[$i]['mtype'] = moneymtype($msql->f("mtype"));
			$marr[$i]['money'] = $msql->f("money");
			$marr[$i]['sxfei'] = $msql->f("sxfei");
			$marr[$i]['tjtime'] = $msql->f("tjtime");
			$marr[$i]['bz'] = $msql->f("bz");
			$marr[$i]['ms'] = $msql->f("ms");
			$marr[$i]['fs'] = moneyfs($msql->f("fs"));
			$marr[$i]['bank'] = $msql->f("bank");
			$marr[$i]['sname'] = substr($msql->f('sname'),0,3);
			$marr[$i]['snum'] = substr($msql->f('snum'),-4);
			$marr[$i]['uname'] = substr($msql->f('uname'),0,3);
			$marr[$i]['unum'] = substr($msql->f('unum'),-4);
			$marr[$i]['cuntime'] = $msql->f("cuntime");
			$marr[$i]['pass'] = $msql->f("pass");
			$i++;
		}
 	    $tpl->assign("marr",$marr);
	   for($i=1;$i<=$pcount;$i++){
	      $parr[] = $i;
	   }
	   $tpl->assign("parr",$parr);
	   $fsql->query("select tname,kmoney,lastlogintime,username from `$tb_user` where userid='$userid'");
	   $fsql->next_record();
	   $tpl->assign("username",$fsql->f('username'));
	   $tpl->assign("xing",substr($fsql->f('tname'),0,3));
	   $tpl->assign("tname",substr($fsql->f('tname'),0,3).'**');
	   $tpl->assign("money",$fsql->f('kmoney'));
	   $tpl->assign("lastlogintime",$fsql->f("lastlogintime"));
	   $tpl->display("mem_jyjl.html");
	break;
	case "orderatm":
		$je = $_POST["amount"];
		$uname = $_POST["userName"];
		$unum = $_POST["cardId"];
		$cuntime = $_POST["depostitTime"]; 
		$pass = $_SESSION['pass'] ;
		$fsql->query("select * from `$tb_banknum` where id='".$_SESSION["bankid"]."' and userid='99999999'");
		$fsql->next_record();
		if($fsql->f("id")=='') exit;
		$bank = transbank($fsql->f("bankid"));
		$sname = $fsql->f("name");
		$snum = $fsql->f("num");
		$tjid = $userid;
		
		$sql = "insert into `$tb_money` set userid='$userid',mtype=0,money='$je',sxfei=0,fs='bankatm',bank='$bank',sname='$sname',snum='$snum',uname='$uname',unum='$unum',cuntime='$cuntime',bz='',status=0,tjid='$userid',tjtime=NOW(),pass='$pass',ms='$ms'";
		$msql->query($sql);
		echo 1;			
	break;
	case "addcard":
	   $num = trim($_POST['num']);
	   $bank = trim($_POST['bank']);
	   $kaihuhang = trim($_POST['kaihuhang']);
	   $bankpass =  trim($_POST['bankpass']);
	   $name = trim($_POST['names']);
	   $msql->query("delete from `$tb_banknum` where userid='$userid'");
	   $sql = "insert into `$tb_banknum` set userid='$userid',bankid='$bank',name='$name',num='$num',kaihuhang='$kaihuhang',bankpass='$bankpass',ifok=1";
	   if($msql->query($sql)){
	       echo 1;
	   }
	break;
	case "itembao":
	    $sdate = week();
		$upstart = $sdate[7];
		$upend = $sdate[8];
		$start = $sdate[5];
		$end = $sdate[6];
        $start = strtotime($sdate[5].' '.$config['editend']);
        $ends  = strtotime($sdate[5].' '.$config['editstart']);
        $upstart = strtotime($sdate[7].' '.$config['editend']);
        $upend  = strtotime($sdate[7].' '.$config['editstart']);
		$layer = transuser($userid,'layer');
		if($layer<8){
		   $mystr = "points".$layer;
		   $downstr = "points".($layer-1);
		   $peilvstr = "peilv1".($layer-1);
		}else{
		   $mystr = "points".$layer;
		   $downstr = "points";
		   $peilvstr = 'peilv1';
		}
		$join  = " from `$tb_lib` where uid".$layer."='$userid' and z!=9 and bs=1  ";
		$upbao = array();
		$t['uzs']=0;
		$t['uzje']=0;
		$t['upoints']=0;
		$t['urs=0']=0;
		for($i=1;$i<=7;$i++){
            $dd     = sqldate($upstart + ($i - 1) * 86400);
			$j                = $i - 1;
            $joins = " $join and dates='$dd' ";
            $upbao[$j]['dates'] = $dd;
			$upbao[$j]['date'] = substr($dd,5);
            $upbao[$j]['week']  = rweek(date("w",strtotime($dd)));	
			$msql->query("select count(id) $joins  and z!=2 and z!=7");	
			$msql->next_record();	
			$upbao[$j]['zs']    = $msql->f(0);
            $msql->query("select count(id),sum(je),sum((if($peilvstr=0,$mystr-$downstr,$mystr-points)/100)*je/100) $joins  and z!=2 and z!=7");
            $msql->next_record();
            $upbao[$j]['zje']    = pr2($msql->f(1));
            $upbao[$j]['points'] = pr2($msql->f(2));
			$upbao[$j]['rs'] = $upbao[$j]['points'];
			
		$t['uzs']+= $upbao[$j]['zs'];
		$t['uzje']+= $upbao[$j]['zje'];
		$t['upoints']+= $upbao[$j]['points'];
		$t['urs']+=$upbao[$j]['rs'];
		}
		$t['uzje'] = pr2($t['uzje']);
		$t['upoints'] = pr2($t['upoints']);
		$t['urs'] = pr2($t['urs']);

		$join  = " from `$tb_lib` where uid".$layer."='$userid' and z!=9 and bs=1  ";
		$bao = array();
		$t['zs']=0;
		$t['zje']=0;
		$t['points']=0;
		$t['rs=0']=0;
		for($i=1;$i<=7;$i++){
            $dd     = sqldate($start + ($i - 1) * 86400);
			$j                = $i - 1;
            $joins = " $join and dates='$dd' ";
            $bao[$j]['dates'] = $dd;
			$bao[$j]['date'] = substr($dd,5);
            $bao[$j]['week']  = rweek(date("w",strtotime($dd)));	
			$msql->query("select count(id) $joins  and z!=2 and z!=7");	
			$msql->next_record();	
			$bao[$j]['zs']    = $msql->f(0);
            $msql->query("select count(id),sum(je),sum((if($peilvstr=0,$mystr-$downstr,$mystr-points)/100)*je/100) $joins  and z!=2 and z!=7");
            $msql->next_record();
            $bao[$j]['zje']    = pr2($msql->f(1));
            $bao[$j]['points'] = pr2($msql->f(2));
			$bao[$j]['rs'] = $bao[$j]['points'];
		$t['zs']+= $bao[$j]['zs'];
		$t['zje']+= $bao[$j]['zje'];
		$t['points']+= $bao[$j]['points'];
		$t['rs']+=$bao[$j]['rs'];
		}

		$t['zje'] = pr2($t['zje']);
		$t['points'] = pr2($t['points']);
		$t['rs'] = (float)(pr2($t['rs']+0));
        $tpl->assign("t",$t);
		$tpl->assign("bao",$bao);
		 $tpl->assign("upbao",$upbao);

		$tpl->display("baoweekitem.html");
	break;
	case "baodayitem":
      $date = rdates($_REQUEST['date']);
		
        $tpage = r1p($_REQUEST['tpage']);
        $psize = $config['psize2'];
       
        $sqls = " and z!=9 and bs=1 and dates='$date' "; 
        $layer = transuser($userid,'layer');
 		$rs= $msql->arr("select count(id) from `$tb_lib` where uid".$layer."='$userid' $sqls",0);
        $rcount = $rs[0][0]; 
		
        $sql = "select qishu,je,peilv1,peilv2,points,content,gid,bid,sid,cid,pid,time,abcd,tid,z from `$tb_lib` where  uid".$layer."='$userid' $sqls order by time desc,id desc limit ";
        $sql .= ($tpage - 1) * $psize . ',' . $psize;
		//echo $sql;
        $msql->query($sql);
        $lib = array();
        $i   = 0;
		$tmp = array();
		$zje=0;
		$rs=0;
        while ($msql->next_record()) {
            $lib[$i]['tid'] = $msql->f('tid');
            $lib[$i]['qishu']  = $msql->f('qishu');
            $lib[$i]['je']     = (float) $msql->f('je');
            $lib[$i]['peilv1'] = (float) ($msql->f('peilv1'));
            $lib[$i]['peilv2'] = (float) ($msql->f('peilv2'));
            $lib[$i]['points']  = (float) $msql->f('points');
            $lib[$i]['content'] = $msql->f('content');
			if($tmp['u'.$msql->f('uid')]==''){
				$tmp['u'.$msql->f('uid')] = transuser($msql->f('userid'),'username');
			}
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
			$lib[$i]['uid'] = $tmp['u'.$msql->f('userid')];
			$lib[$i]['gid'] = $tmp['g'.$msql->f('gid')];
			$lib[$i]['wf'] = wf($msql->f('gid'),$tmp['b' . $msql->f('gid') . $msql->f('bid')],$tmp['s' . $msql->f('gid') . $msql->f('sid')],$tmp['c' . $msql->f('gid') . $msql->f('cid')],$tmp['p' . $msql->f('gid') . $msql->f('pid')]);
			$lib[$i]['abcd'] = $msql->f('abcd');
            $lib[$i]['time'] = $msql->f('time');
			$lib[$i]['rs'] = (float)$msql->f('peilv1')*$msql->f('je');
			if($msql->f('z')==1){
			    $lib[$i]['rs'] = (float)($msql->f('peilv1')*$msql->f('je')-$msql->f('je')*(1-$msql->f('points')/100));
			}else if($msql->f('z')==3){
			    $lib[$i]['rs'] = (float)($msql->f('peilv2')*$msql->f('je')-$msql->f('je')*(1-$msql->f('points')/100));
			}else if($msql->f('z')==2){
			    $lib[$i]['rs'] = 0;
			}else{
			    $lib[$i]['rs'] = (float)(0-$msql->f('je')*(1-$msql->f('points')/100));
			}
			$rs += $lib[$i]['rs'];
			$lib[$i]['rs'] = pr2($lib[$i]['rs']);
			$zje += $lib[$i]['je'];
            $i++;
        }
  
		$tpl->assign("lib",$lib);
		$tpl->assign("rs",pr2($rs));
        $tpl->assign("zje",$zje);
		
		$tpl->assign("rcount",$rcount);
		$tpl->assign("psize",$psize);
		$tpl->assign("tpage",$tpage);
		
		$tpl->assign("date",$date);
		$tpl->assign("week",rweek(date('w',strtotime($date))));
       
		 $tpl->display("baodayitem.html"); 
	break;
	case "myitem":
	   $msql->query("select * from `$tb_user` where userid='$userid'");
	   $msql->next_record();
	   $tpl->assign("username",$msql->f("username"));
	   $tpl->assign("xing",substr($msql->f('tname'),0,3));
	   $tpl->assign("tname",substr($msql->f('tname'),0,3).'**');

	   
	   $tpage=r1p($_REQUEST['tpage']);
	   $psize = 10;
	   $msql->query("select count(id) from `$tb_user` where fid='$userid'");
	   $msql->next_record();
	   $rcount = $msql->f(0);
	   $pcount = $rcount%$psize==0 ? $rcount/$psize : (($rcount - $rcount%$psize)/$psize)+1;
	   $msql->query("select * from `$tb_user` where fid='$userid' order by regtime desc limit ".(($tpage-1)*$psize).",".$psize);
	   $user = array();
	   $i=0;
	   while($msql->next_record()){
            $user[$i]['userid']        = $msql->f('userid');
            $user[$i]['username']      = $msql->f('username');
            $user[$i]['online']        = $msql->f('online');
            $user[$i]['tname']         = $msql->f('tname');
            $user[$i]['qq']            = $msql->f('qq');
            $user[$i]['tel']           = $msql->f('tel');
            $user[$i]['sex']           = $msql->f('sex');
            $user[$i]['birthday']      = $msql->f('birthday');
            $user[$i]['regtime']       = substr($msql->f('regtime'), 0, 10);
            $user[$i]['lastlogintime'] = substr($msql->f('lastlogintime'), 0, 10);
            $user[$i]['status']        = $msql->f('status');
            $user[$i]['statusz']       = transstatus($msql->f('status'));
            $user[$i]['kmaxmoney']     = $msql->f('kmaxmoney');
            $user[$i]['kmoney']        = $msql->f('kmoney');
			$user[$i]['liushui']        = $msql->f('liushui');
		   $i++;		   
	   }


	   $tpl->assign("user",$user);
	   $tpl->assign("rcount",$rcount);
	   $tpl->assign("pcount",$pcount);
	   $tpl->assign("tpage",$tpage);
	   $tpl->assign("psize",$psize);

	    
	   $tpl->display("mem_myitem.html"); 
	break;	
   case "upstatus":
      $ustr   = $_POST['ustr'];
      $status = $_POST['status'];
      $u      = explode('|', $ustr);
      $time   = time();
      for ($i = 0; $i < count($u); $i++) {
         if ($u[$i] == '') {
            continue;
         }
         $uid = $u[$i];
         if (transuser(transuser($uid, 'fid'), 'status') == 0 & ($status == 1 | $status == 2)) {
            exit;
         }
		 if(!checkfid($uid)){
		    continue;
		 }
         $sql = "update `$tb_user` set status='$status' where userid='$uid'";
         $msql->query($sql);
         if (($status == 1 | $status == 2) & transuser($uid, 'ifagent') == 1) {
            $msql->query("update `$tb_user` set status='$status' where fid='$uid' and ifson=1");
         }
         if ($status == 0 | $status == 2) {
            $ugroup = getusergroup($uid);
            $fsql->query("update `$tb_user` set status='$status' where instr('$ugroup',userid)");
         }
         $fsql->query("delete from `$tb_online`  where instr('$ugroup',userid)");
         userchange("修改状态", $u[$i]);
      }
      echo 1;
      break;
	case "eshui":
	   $uid = $_POST['uid'];
	   $shui = $_POST['shui'];

	   if(!checkfid($uid)) exit;
	   if(!is_numeric($shui)) exit;
	
	   $msql->query("select defaultpan from `$tb_user` where userid='$uid'");
	   $msql->next_record();
	   $dpan  = strtolower($msql->f('defaultpan'));
	   $msql->query("select * from `$tb_points` where userid='$userid'");
	   $tmp=$shui;
	   while($msql->next_record()){
		   if($msql->f('a')-$shui<0) $a = 0;
		   else $a = $msql->f('a')-$shui;
		   if($msql->f('b')-$shui<0) $b = 0;
		   else $b = $msql->f('b')-$shui;
		   if($msql->f('c')-$shui<0) $c = 0;
		   else $c = $msql->f('c')-$shui;
		   if($msql->f('d')-$shui<0) $d = 0;
		   else $d = $msql->f('d')-$shui;
		   $class = $msql->f('class');
		   $ab = $msql->f('ab');
		   $gid = $msql->f('gid');
		   $fsql->query("update `$tb_points` set a='$a',b='$b',c='$c',d='$d' where gid='$gid' and userid='$uid' and class='$class' and ab='$ab'");
	   }
       $msql->query("update `$tb_user` set liushui='$tmp' where userid='$uid'");
	   echo $tmp;
	break;
	case "cpass":
	   $uid = $_POST['uid'];
	   $pass = $_POST['pass'];
	   if(!checkfid($uid)) exit;
	   $pass = md5(md5($pass).$config['upass']);
	   $msql->query("update `$tb_user` set userpass='$pass' where userid='$uid'");
	   echo 1;
	break;
}