<?php
include('../data/comm.inc.php');

include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/adminfunc.php');

include('../include.php');
include('./checklogin.php');

switch ($_REQUEST['xtype']) {
    case "show":
        $qishu    = array();
        $qishu[0] = $config['thisqishu'];
        $msql->query("select qishu from `$tb_kj` where gid='$gid' and m1!='' order by qishu desc");
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('qishu');
            $i++;
        }
        $tpl->assign('qishu', $qishu);
        $tpl->assign("b", getb());
        $tpl->assign("s", gets());
        $msql->query("select layer from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign("layername", $config['layer']);
        $tpl->assign("topuser", topuser($userid));
        $tpl->display("moren.html");
        break;
	case "getlib":
        $bid      = $_POST['bid'];
		$sid      = $_POST['sid'];
        $cid      = $_POST['cid'];
        $ab       = $_POST['ab'];
        $abcd     = $_POST['abcd'];
        $qishu    = $_POST['qishu'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
		$layer = 0;
		


        $yq    = " and qishu='$qishu' and gid='$gid' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        if ($ab == 'A' | $ab == 'B') {
            $yq .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $yq .= " and abcd='$abcd' ";
        }
		
        
        $play  = getpsm($bid, $ab, $abcd, $cid);
		$cp = count($play);
		for($i=0;$i<$cp;$i++){
			 $sql = "select sum(je),sum(je*zc0/100),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100),count(id) ";
			 $sql .= " from `$tb_lib` where  pid='".$play[$i]['pid']."' $yq ";
		     $msql->query($sql);
			 $msql->next_record();
			 
			 $play[$i]['zje'] = pr1($msql->f(0));
			 $play[$i]['zc'] = pr1($msql->f(1));
			 $play[$i]['zs'] = pr1($msql->f(4));
			 $play[$i]['ks'] = pr1($play[$i]['zc'] - $msql->f(2) - - $msql->f(3));
			 
			 if($ftype!=$play[$i]['ftype']){
			      $arr = getwarn($play[$i]['ftype']);
			 }
			 $play[$i]['wje'] = 0;
			 $play[$i]['wks'] = 0;
			 if($play[$i]['zc']>=$arr['je']){
			      $play[$i]['wje'] = 1;
			 }
			 if(abs($play[$i]['ks'])>=$arr['ks']){
			      $play[$i]['wks'] = 1;
			 }	 
			 $ftype = $play[$i]['ftype'];
		}
		echo json_encode($play);

	break;
	
    case "getc":
        $bid   = $_POST['bid'];
       
		$msql->query("select * from `$tb_class`  where gid='$gid' and bid='$bid'  order by bid,sid,xsort ");
		$i=0;
		$c=array();
		while($msql->next_record()){
		     $c[$i]['cid'] = $msql->f('cid');
			 $c[$i]['name'] = $msql->f('name');
			 $i++;
		}
		echo json_encode($c);
		unset($c);
	break;
	case "getlibd":
        $bid      = $_POST['bid'];
		$sid      = $_POST['sid'];
        $cid      = $_POST['cid'];
        $ab       = $_POST['ab'];
        $abcd     = $_POST['abcd'];
        $qishu    = $_POST['qishu'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
		$layer = 0;
		


        $yq    = " and qishu='$qishu' and gid='$gid' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        if ($ab == 'A' | $ab == 'B') {
            $yq .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $yq .= " and abcd='$abcd' ";
        }
		
        
        $play  = getpsmd($bid, $ab, $abcd, $cid);
		$cp = count($play);
		for($i=0;$i<$cp;$i++){
			 $sql = "select sum(je),sum(je*zc0/100),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100),count(id) ";
			 $sql .= " from `$tb_lib` where  pid='".$play[$i]['pid']."' $yq ";
		     $msql->query($sql);
			 $msql->next_record();
			 
			 $play[$i]['zje'] = pr1($msql->f(0));
			 $play[$i]['zc'] = pr1($msql->f(1));
			 $play[$i]['zs'] = pr1($msql->f(4));
			 $play[$i]['ks'] = pr1($play[$i]['zc'] - $msql->f(2) - - $msql->f(3));
			 if($ftype!=$play[$i]['ftype']){
			      $arr = getwarn($play[$i]['ftype']);
			 }
			 $play[$i]['wje'] = 0;
			 $play[$i]['wks'] = 0;
			 if($play[$i]['zc']>=$arr['je']){
			      $play[$i]['wje'] = 1;
			 }
			 if(abs($play[$i]['ks'])>=$arr['ks']){
			      $play[$i]['wks'] = 1;
			 }	 
			 $ftype = $play[$i]['ftype'];
		}
		echo json_encode($play);

	break;
	case "getlibe":
        $bid      = $_POST['bid'];
		$sid      = $_POST['sid'];
        $cid      = $_POST['cid'];
        $ab       = $_POST['ab'];
        $abcd     = $_POST['abcd'];
        $qishu    = $_POST['qishu'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $setks    = $_POST['setks'];
		$layer = 0;
		


        $yq    = " and qishu='$qishu' and gid='$gid' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        if ($ab == 'A' | $ab == 'B') {
            $yq .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $yq .= " and abcd='$abcd' ";
        }
		
        

        $play  = getpsmd($bid, $ab, $abcd, $cid);
		$cp = count($play);
		for($i=0;$i<$cp;$i++){
			 $sql = "select sum(je),sum(je*zc0/100),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100),count(id) ";
			 $sql .= " from `$tb_lib` where  pid='".$play[$i]['pid']."' $yq ";
		     $msql->query($sql);
			 $msql->next_record();
			 
			 $play[$i]['zje'] = pr1($msql->f(0));
			 $play[$i]['zc'] = pr1($msql->f(1));
			 $play[$i]['zs'] = pr1($msql->f(4));
			 $play[$i]['ks'] = pr1($play[$i]['zc'] - $msql->f(2) - - $msql->f(3));
			 if($ftype!=$play[$i]['ftype']){
			      $arr = getwarn($play[$i]['ftype']);
			 }
			 $play[$i]['wje'] = 0;
			 $play[$i]['wks'] = 0;
			 if($play[$i]['zc']>=$arr['je']){
			      $play[$i]['wje'] = 1;
			 }
			 if(abs($play[$i]['ks'])>=$arr['ks']){
			      $play[$i]['wks'] = 1;
			 }	 
			 $ftype = $play[$i]['ftype'];
		}
		echo json_encode($play);

	break;
	
	case "getlibc":
        $bid      = $_POST['bid'];
		$sid      = $_POST['sid'];
        $cid      = $_POST['cid'];
        $ab       = $_POST['ab'];
        $abcd     = $_POST['abcd'];
        $qishu    = $_POST['qishu'];
        $puserid  = $_POST['userid'];
        $maxksval = $_POST['maxksval'];
        $p    = $_POST['p'];
		$layer = 0;
		


        $yq    = " and qishu='$qishu' and gid='$gid' ";
        $yq2   = $yq;
        $yq .= " and xtype!=2 ";
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        if ($ab == 'A' | $ab == 'B') {
            $yq .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $yq .= " and abcd='$abcd' ";
        }
		
        

        $play  = getpsmc($bid, $ab, $abcd, $cid,$p);
		$cp = count($play);
		for($i=0;$i<$cp;$i++){
			 $sql = "select sum(je),sum(je*zc0/100),sum(if(peilv11=0,peilv1,peilv11)*je*zc0/100),sum((if(peilv11=0,points,points1)/100)*je*zc0/100),count(id) ";
			 $sql .= " from `$tb_lib` where  pid='".$play[$i]['pid']."' $yq ";
		     $msql->query($sql);
			 $msql->next_record();
			 
			 $play[$i]['zje'] = pr1($msql->f(0));
			 $play[$i]['zc'] = pr1($msql->f(1));
			 $play[$i]['zs'] = pr1($msql->f(4));
			 $play[$i]['ks'] = pr1($play[$i]['zc'] - $msql->f(2) - - $msql->f(3));
			 if($ftype!=$play[$i]['ftype']){
			      $arr = getwarn($play[$i]['ftype']);
			 }
			 $play[$i]['wje'] = 0;
			 $play[$i]['wks'] = 0;
			 if($play[$i]['zc']>=$arr['je']){
			      $play[$i]['wje'] = 1;
			 }
			 if(abs($play[$i]['ks'])>=$arr['ks']){
			      $play[$i]['wks'] = 1;
			 }	 
			 $ftype = $play[$i]['ftype'];
		}
		echo json_encode($play);
		/*
        $cp    = count($play);
        if ($puserid != '') {
            $yq .= " and (uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "')";
        }
        if ($ab == 'A' | $ab == 'B') {
            $aandb .= " and ab='$ab' ";
        }
        if ($abcd == 'A' | $abcd == 'B' | $abcd == 'C' | $abcd == 'D') {
            $aandb .= " and abcd='$abcd' ";
        }*/
	break;
    case "getnow":
        $qishu   = $_POST['qishu'];
        $yq1     = " and gid='$gid' and qishu='$qishu' ";
        $puserid = $_POST['userid'];
        if ($puserid != '') {
            $yq2 = $yq1 . " and ( uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "') ";
        } else {
            $yq2 = $yq1;
        }
        $yq2 .= " and  xtype!=2 ";
        $msql->query("select * from `$tb_bclass` where ifok=1 and gid='$gid'");
        $now = array();
        $i   = 0;
        while ($msql->next_record()) {
            $fsql->query("select sum(je),sum(je*zc0/100),count(id) from `$tb_lib` where bid='" . $msql->f('bid') . "' $yq2 and userid!='$userid' ");
            $fsql->next_record(0);
            $now[$i]['zje']   = pr1($fsql->f(0));
            $now[$i]['zc'] = pr1($fsql->f(1));
            $now[$i]['zs']    = pr1($fsql->f(2));
            $now[$i]['bid']   = $msql->f('bid');
            $fsql->query("select sum(je) from `$tb_lib` where bid='" . $msql->f('bid') . "' $yq1  and userid='$userid'");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            $i++;
        }
        echo json_encode($now);
        unset($now);
        break;
    case "getnowson":

		
        $qishu   = $_POST['qishu'];
		$bid = $_POST['bid'];
        $yq1     = " and gid='$gid' and qishu='$qishu' ";
        
        $puserid = $_POST['userid'];
        if ($puserid != '') {
            $yq2 = $yq1 . " and ( uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "') ";
        } else {
            $yq2 = $yq1;
        }
        $yq2 .= " and  xtype!=2 ";
        $msql->query("select * from `$tb_class` where bid='$bid' and gid='$gid'");
        $now = array();
        $i   = 0;
        while ($msql->next_record()) {
            $fsql->query("select sum(je),sum(je*zc0/100),count(id) from `$tb_lib` where 1=1 $yq2 and userid!='$userid' and cid='".$msql->f('cid')."' and bid='$bid' ");
            $fsql->next_record();
            $now[$i]['zje']   = pr1($fsql->f(0));
            $now[$i]['zc'] = pr1($fsql->f(1));
            $now[$i]['zs']    = pr1($fsql->f(2));
            $now[$i]['cid']   = $msql->f('cid');
            $fsql->query("select sum(je) from `$tb_lib` where 1-1 $yq1  and userid='$userid'  and cid='".$msql->f('cid')."' and bid='$bid'");
            $fsql->next_record();
            $now[$i]['flyje'] = pr1($fsql->f(0));
            $i++;
        }
        echo json_encode($now);
        unset($now);
        break;
    case "getnowsan":
	   // error_reporting(E_ALL);
		
        $qishu   = $_POST['qishu'];
		$bid = $_POST['bid'];
		$cid = $_POST['cid'];
        $yq1     = " and gid='$gid' and qishu='$qishu' ";
        
        $puserid = $_POST['userid'];
        if ($puserid != '') {
            $yq2 = $yq1 . " and ( uid" . ($layer + 1) . "='" . $puserid . "' or userid='" . $puserid . "') ";
        } else {
            $yq2 = $yq1;
        }
        $yq2 .= " and  xtype!=2 ";
		$now = array();
		for($i=0;$i<=9;$i++){
		     $now[$i]['t'] = $i;
			 $sql = "select sum(je),sum(je*zc0/100),count(id) from `$tb_lib` where 1=1 $yq2 and userid!='$userid' and cid='$cid' and bid='$bid' ";
			 $sql .= " and pid in (select pid from `$tb_play` where gid='$gid' and cid='$cid' and bid='$bid' and substr(name,1,1)=".$i.")";
			 //echo $sql;
			 $fsql->query($sql);
             $fsql->next_record();            
			 $now[$i]['zje']   = pr1($fsql->f(0));
             $now[$i]['zc'] = pr1($fsql->f(1));
             $now[$i]['zs']    = pr1($fsql->f(2));  
			 $sql = "select sum(je) from `$tb_lib` where 1-1 $yq1  and userid='$userid'  and cid='$cid' and bid='$bid' ";
			 $sql .= " and pid in (select pid from `$tb_play` where gid='$gid' and cid='$cid' and bid='$bid' and substr(name,1,1)=".$i.")";
			 $fsql->query($sql);
             $fsql->next_record();
             $now[$i]['flyje'] = pr1($fsql->f(0));
			 
		}

        echo json_encode($now);
        unset($now);
        break; 
        
}

?>