<?php
include('../data/comm.inc.php');include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "bigclass":
        $msql->query("select * from `$tb_bclass` where gid='$gid' order by xsort ");
        $b = array();
        $i = 0;
        while ($msql->next_record()) {
            $b[$i]['bid']   = $msql->f('bid');
            $b[$i]['name']  = $msql->f('name');
            $b[$i]['ifok']  = $msql->f('ifok');
            $b[$i]['xsort'] = $msql->f('xsort');
            $i++;
        }
        $tpl->assign("b", $b);
		$msql->query("select fenlei,flname from `$tb_game` where gid='$gid'");
		$msql->next_record();
		$tpl->assign("fenlei", $msql->f('fenlei'));
		$tpl->assign("flname", $msql->f('flname'));
        $tpl->display("classlistb.html");
        break;
    case "addb":
        $name = $_POST['name'];
        if ($name == '')
            exit;
        $bid = setupid($tb_bclass, 'bid');
        $sql = "insert into `$tb_bclass` set gid='$gid',bid='$bid',name='$name',ifok='1',xsort=0";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "editb":
        $name  = $_POST['name'];
        $ifok  = $_POST['ifok'];
        $xsort = $_POST['xst'];
        $bid   = $_POST['bid'];
        $sql   = "update `$tb_bclass` set name='$name',ifok='$ifok',xsort='$xsort' where bid='$bid' and gid='$gid'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "delb":
        $idstr = $_POST['idstr'];
        $sql   = "delete from `$tb_bclass` where instr('$idstr',bid) and gid='$gid'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "sclass":
        $bid = $_REQUEST['bid'];
        if ($bid == '') {
            $msql->query("select * from `$tb_sclass` where gid='$gid' order by xsort limit 100 ");
        } else {
            $msql->query("select * from `$tb_sclass` where gid='$gid' and bid='$bid' order by xsort ");
        }
        $s = array();
        $i = 0;
        while ($msql->next_record()) {
            $s[$i]['bid']   = $msql->f('bid');
            $s[$i]['bname'] = transb('name', $msql->f('bid'));
            $s[$i]['sid']   = $msql->f('sid');
            $s[$i]['name']  = $msql->f('name');
            $s[$i]['ifok']  = $msql->f('ifok');
            $s[$i]['xsort'] = $msql->f('xsort');
            $i++;
        }
        $tpl->assign("s", $s);
        
        $msql->query("select * from `$tb_bclass` where gid='$gid' order by xsort");
        $b = array();
        $i = 0;
        while ($msql->next_record()) {
            $b[$i]['bid']  = $msql->f('bid');
            $b[$i]['name'] = $msql->f('name');
            $i++;
        }
        $tpl->assign("b", $b);
        
        $tpl->assign("bid", $bid);
        
        $tpl->display("classlists.html");
        break;
        break;
    case "adds":
        $name = $_POST['name'];
        $bid  = $_POST['bid'];
        $sid  = setupid($tb_sclass, 'sid');
        $sql  = "insert into `$tb_sclass` set gid='$gid',bid='$bid',sid='$sid',name='$name',ifok=1,xsort=0";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "gets":
        $bid = $_POST['bid'];
        $msql->query("select * from `$tb_sclass` where gid='$gid' and bid='$bid'");
        $s = array();
        $i = 0;
        while ($msql->next_record()) {
            $s[$i]['sid']   = $msql->f('sid');
            $s[$i]['name']  = $msql->f('name');
            $s[$i]['ifok']  = $msql->f('ifok');
            $s[$i]['xsort'] = $msql->f('xsort');
            $i++;
        }
        echo json_encode($s);
        break;
    case "edits":
        $name  = $_POST['name'];
        $ifok  = $_POST['ifok'];
        $xsort = $_POST['xst'];
        $sid   = trim($_POST['sid']);
		$bid   = trim($_POST['bid']);
        $sql   = "update `$tb_sclass` set name='$name',ifok='$ifok',xsort='$xsort',bid='$bid' where sid='$sid' and gid='$gid'";
        if ($msql->query($sql)) {
            echo 1;
        }
        
        break;
    case "dels":
        $idstr = $_POST['idstr'];
        $sql   = "delete from `$tb_sclass` where instr('$idstr',sid) and gid='$gid'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "class":
        $bid = $_GET['bid'];
        $sid = $_GET['sid'];
        $whi = " where gid='$gid' ";
        if ($bid != '')
            $whi .= " and bid='$bid' ";
        if ($sid != '')
            $whi .= " and sid='$sid' ";
        $msql->query("select mtype,ftype,dftype from `$tb_game` where gid='$gid'");
		$msql->next_record();
		$config['ftype'] = json_decode($msql->f('ftype'),true);
		$config['dftype'] = json_decode($msql->f('dftype'),true);
		$config['mtype'] = json_decode($msql->f('mtype'),true);
		$config['ftype'] = ftypes($config['ftype']);
        $msql->query("select * from `$tb_class`  $whi order by xsort ");
        $c = array();
        $i = 0;
        while ($msql->next_record()) {
            $c[$i]['bid']   = $msql->f('bid');
            $c[$i]['bname'] = transb('name', $msql->f('bid'));
            $c[$i]['sid']   = $msql->f('sid');
            $c[$i]['sname'] = transs('name', $msql->f('sid'));
            
            $c[$i]['cid']  = $msql->f('cid');
            $c[$i]['name'] = $msql->f('name');
            $c[$i]['ifok'] = $msql->f('ifok');
            /*if ($msql->f('ftype') > 6) {
                $c[$i]['ftype'] = $config['mtype']['p' . $msql->f('ftype')];
            } else {
                $c[$i]['ftype'] = $config['mtype'][$msql->f('ftype')];
            }*/
			$c[$i]['ftype'] = $config['ftype'][$msql->f('ftype')];
			$c[$i]['dftype'] = $config['dftype'][$msql->f('dftype')];
            $c[$i]['mtype'] = $config['mtype'][$msql->f('mtype')];
            $c[$i]['xsort'] = $msql->f('xsort');
            $c[$i]['fid']   = $msql->f('fid');
            $c[$i]['xshow'] = $msql->f('xshow');
			$c[$i]['one'] = $msql->f('one');
            $i++;
			
        }
        $tpl->assign("c", $c);
        

        $msql->query("select * from `$tb_bclass` where gid='$gid' order by xsort");
        $b = array();
        $i = 0;
        while ($msql->next_record()) {
            $b[$i]['bid']  = $msql->f('bid');
            $b[$i]['name'] = $msql->f('name');
            
            $i++;
        }
        $tpl->assign("b", $b);
        
        if ($bid != '') {
            $msql->query("select * from `$tb_sclass` where gid='$gid' and bid='$bid' order by xsort");
            $s = array();
            $i = 0;
            while ($msql->next_record()) {
                $s[$i]['sid']  = $msql->f('sid');
                $s[$i]['name'] = $msql->f('name');
                $i++;
            }
            $tpl->assign("s", $s);
        }
        
        
        
        $tpl->assign('mtype', $config['mtype']);
        $tpl->assign('ftype', $config['ftype']);
		$tpl->assign('dftype', $config['dftype']);
		
        $tpl->assign("bid", $bid);
        $tpl->assign("sid", $sid);
        $tpl->display("classlist.html");
        break;
    case "addc":
        $name  = $_POST['name'];
        $bid   = $_POST['bid'];
        $sid   = $_POST['sid'];
        $mtype = mtype(trim($_POST['mtype']));
        $ftype = ftype(trim($_POST['ftype']));
        $cid   = setupid($tb_class, 'cid');
        $sql   = "insert into `$tb_class` set gid='$gid',bid='$bid',sid='$sid',name='$name',mtype='$mtype',ftype='$ftype',ifok=1,xsort=0,cid='$cid'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "editc":
        $arr  = $_POST['arr'];		
		$arr = str_replace('\\','',$arr);
        $arr  = json_decode($arr,true);
        $arrc = count($arr);
		
		$msql->query("select mtype,ftype,dftype from `$tb_game` where gid='$gid'");
		$config['ftype'] = json_decode($msql->f('ftype'),true);
		$config['dftype'] = json_decode($msql->f('dftype'),true);
		$config['mtype'] = json_decode($msql->f('mtype'),true);
		$config['ftype'] = ftypes($config['ftype']);
		
        for ($i = 0; $i < $arrc; $i++) {
            $name    = $arr[$i]['name'];
            $ifok    = $arr[$i]['ifok'];
            $xsort   = $arr[$i]['xst'];
            $abcd    = $arr[$i]['abcd'];
            $ab      = $arr[$i]['ab'];
            $xshow   = $arr[$i]['xshow'];
			$one   = $arr[$i]['one'];
            $cid = $arr[$i]['cid'];
            $bid     = trim($arr[$i]['bid']);
            $sid     = trim($arr[$i]['sid']);
            $mtype   = mtype(trim($arr[$i]['mtype']));
			$dftype   = dftype(trim($arr[$i]['dftype']));
            $ftype   = ftype(trim($arr[$i]['ftype']));
            $sql     = "update `$tb_class` set name='$name',one='$one',ifok='$ifok',xsort='$xsort',bid='$bid',sid='$sid',mtype='$mtype',dftype='$dftype',ftype='$ftype',xshow='$xshow' where cid='$cid' and gid='$gid' ";
			$msql->query($sql);
        }
        
        echo 1;
        break;
    case "delc":
        $idstr = $_POST['idstr'];
        $sql   = "delete from `$tb_class` where instr('$idstr',cid) and gid='$gid'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "getc":
        $bid   = $_POST['bid'];
        $sid   = $_POST['sid'];
		$msql->query("select * from `$tb_class`  where gid='$gid' and bid='$bid' and sid='$sid' order by bid,sid,xsort ");
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
    case "classpan":
	    if(in_array($_REQUEST['gid'],$garr)){
	       $gid= $_REQUEST['gid'];
		}
        $game = getgamecs($userid);
        $game = getgamename($game);
        $msql->query("select pan from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $pan = json_decode($msql->f('pan'), true);
		
		$tpl->assign("pan",$pan);
		$tpl->assign("game",$game);
		$tpl->assign("gid",$gid);
        $tpl->display("classpan.html");
        break;
    case "editpan":
        $arr= str_replace('\\','',$_POST['str']);
	    $gid= $_REQUEST['gid'];
        $msql->query("update `$tb_game` set pan='$arr' where gid='$gid'");
        echo 1;
        break;
    case "yiwotongbu":
         $fenlei = transgame($gid,'fenlei'); 
         $game  = $msql->arr("select * from `$tb_gamecs` where userid='99999999' and gid in (select gid from `$tb_game` where fenlei='$fenlei')",1);   
         $arr  = $msql->arr("select * from `$tb_game` where gid='$fenlei'",1);
         $dftype = $arr[0]['dftype'];
         $ftype = $arr[0]['ftype'];
         $mtype = $arr[0]['mtype'];
         $ztype = $arr[0]['ztype'];
         $patt1  = $arr[0]['patt1'];
         $patt2  = $arr[0]['patt2'];
         $patt3  = $arr[0]['patt3'];
         $patt4  = $arr[0]['patt4'];
         $patt5  = $arr[0]['patt5'];
         $pan  = $arr[0]['pan'];
         $ptype = $arr[0]['ptype'];
         $msql->query("update `$tb_game` set ftype='$ftype',dftype='$dftype',ztype='$ztype',mtype='$mtype',patt1='$patt1',patt2='$patt2',patt3='$patt3',patt4='$patt4',patt5='$patt5',pan='$pan',ptype='$ptype' where fenlei='$fenlei' and gid!='$fenlei' ");
         $msql->query("select gid from `$tb_game` where fenlei='$fenlei' and gid!='$fenlei'");
         //error_reporting(E_ALL);
         while($msql->next_record()){
             $gid=$msql->f('gid');
         $fsql->query("delete from `$tb_bclass` where gid='$gid'");
         $fsql->query("delete from `$tb_sclass` where gid='$gid'");
         $fsql->query("delete from `$tb_class` where gid='$gid'");
         $fsql->query("delete from `$tb_play` where gid='$gid'");
         $fsql->query("delete from `$tb_att` where gid='$gid'");
         $fsql->query("delete from `$tb_gamecs` where gid='$gid' and userid=99999999");
         $fsql->query("delete from `$tb_points` where gid='$gid' and userid=99999999");
         $fsql->query("delete from `$tb_zpan` where gid='$gid' and userid=99999999");
         $fsql->query("insert into `$tb_bclass` select NULL,$gid,bid,name,ifok,xsort from `$tb_bclass` where gid='$fenlei'");
         $fsql->query("insert into `$tb_sclass` select NULL,$gid,bid,sid,name,ifok,xsort from `$tb_sclass` where gid='$fenlei'");
         $fsql->query("insert into `$tb_class` select NULL,$gid,bid ,sid,cid,name,xsort,ifok,mtype,ftype,xshow,one,dftype from `$tb_class` where gid='$fenlei'");
         $fsql->query("insert into `$tb_play` select NULL,$gid,bid,sid,cid,pid,name,ifok,peilv1,peilv2,mp1,mp2,ztype,znum1,znum2,xsort,start,autocs,zstart,zautocs,zqishu,buzqishu,pl,mpl,ystart,yautocs,ptype from `$tb_play` where gid='$fenlei'");
         $fsql->query("insert into `$tb_att` select NULL,$gid,bc,class,points,pointsatt,peilvatt,maxatt,peilvatt1,flypeilv,flyifok from `$tb_att` where gid='$fenlei'");
         $fsql->query("insert into `$tb_gamecs` select NULL,userid,$gid,ifok,flytype,flyzc,zc,upzc,zcmin,xsort  from `$tb_gamecs` where gid='$fenlei' and userid=99999999");
         $fsql->query("insert into `$tb_points` select NULL,$gid,userid,class,ab,a,b,c,d,cmaxje,maxje,minje  from `$tb_points` where gid='$fenlei' and userid=99999999");
         $fsql->query("insert into `$tb_zpan` select NULL,$gid,userid,class,lowpeilv,peilvcha from `$tb_zpan` where gid='$fenlei' and userid=99999999");
         
         }   
         
         foreach($game as $k => $v){
            if($v['ifok']!=1){
                $msql->query("update `$tb_gamecs` set ifok=0 where gid='".$v['gid']."'");
            }
         }
         
         
        echo 1;
        break;
}


?>