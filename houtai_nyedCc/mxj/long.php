<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/userfunc.php');
include('../func/csfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {

    case "long":
        //error_reporting(E_ALL);
        include('../func/long.php');
        $gid   = $_POST['gid'];
        $bid   = $_POST['bid'];
        $sid   = $_POST['sid'];
        $cid   = $_POST['cid'];
       
        $page  = $_POST['page'];
		$psize =50;
      
             if ($page%1!=0)
            $page = 1;
		if($page<1) $page=1;
		
        $kj = long($gid, $bid, $sid, $cid, $psize, $page);
        echo json_encode($kj);
        unset($kj);
        break;
    case "longsm":
        $gid   = $_POST['gid'];
        $psize = $_POST['psize'];
		$psize =30;
        $page  = $_POST['page'];
        if ($page%1!=0)
            $page = 1;
		if($page<1) $page=1;
        include("../func/longsm.php");
        $kj = longsm($gid, $psize, $page);
        echo json_encode($kj);
        unset($kj);
        break;
		    default:
         if ($_REQUEST['gid'] != '') {
            $gid = $_REQUEST['gid'];
        }
        if ($gid== 100) {
			$rs = $msql->arr("select ma from `$tb_config`",0);
			$ma = json_decode($rs[0][0],true);
			foreach($ma as $k =>$v){
			   foreach($v as $k1=>$v1){
				   $ma[$k][$k1] = explode(',',$v1);
				}
			}
$msql->query("select * from `$tb_kj` where gid='$gid' and m1!='' order by qishu desc ");
$i=0;
          while ($msql->next_record()){
      
             $kj[$i]["date"]=date("m-d",$msql->f("kjtime"));
             $kj[$i]["week"]=rweek(date("w",$msql->f("kjtime")));
             $kj[$i]["qishu"]=$msql->f("qishu");
             $kj[$i]["m1"]=$msql->f("m1");
             $kj[$i]["m2"]=$msql->f("m2");
             $kj[$i]["m3"]=$msql->f("m3");
             $kj[$i]["m4"]=$msql->f("m4");
             $kj[$i]["m5"]=$msql->f("m5");
             $kj[$i]["m6"]=$msql->f("m6");
             $kj[$i]["m7"]=$msql->f("m7");
			 $kj[$i]["zf"]= $msql->f("m1")+$msql->f("m2")+$msql->f("m3")+$msql->f("m4")+$msql->f("m5")+$msql->f("m6")+$msql->f("m7");
			if($kj[$i]["zf"]%2==0){
				$kj[$i]["zfds"] ="總雙";
			}else{
				$kj[$i]["zfds"] ="總單";
			}
			if($kj[$i]["zf"]>=175){
				$kj[$i]["zfdx"] ="總大";
			}else{
				$kj[$i]["zfdx"] ="總小";
			}
			foreach( $ma['生肖'] as $k=>$v){
			        if(in_array($msql->f('m7'),$v)){
				       $kj[$i]["sx"] = $k;
					}
			}
			if($msql->f('m7')==49){
				$kj[$i]["ds"] = '和';
				$kj[$i]["dx"] = '和';
				$kj[$i]["hds"] = '和';
				$kj[$i]["wdx"] = '和';
				$kj[$i]["jy"] = '和';
			}else{	 
				 foreach($ma['單雙'] as $k=>$v){
			        if(in_array($msql->f('m7'),$v)){
				       $kj[$i]["ds"] = $k;
					}
				 }
				 foreach($ma['大小'] as $k=>$v){
			        if(in_array($msql->f('m7'),$v)){
				       $kj[$i]["dx"] = $k;
					}
				 }
				 foreach($ma['合單雙'] as $k=>$v){
			        if(in_array($msql->f('m7'),$v)){
				       $kj[$i]["hds"] = $k;
					}
				 }
				 foreach($ma['尾大小'] as $k=>$v){
			        if(in_array($msql->f('m7'),$v)){
				       $kj[$i]["wdx"] = $k;
					}
				 }
				 foreach($ma['家野'] as $k=>$v){
			        if(in_array($msql->f('m7'),$v)){
				       $kj[$i]["jy"] = $k;
					}
				 }	
			}
				 foreach($ma['波色'] as $k=>$v){
			        if(in_array($msql->f('m7'),$v)){
				       $kj[$i]["bs"] = $k;
					}
				 }
				 foreach($ma['五行'] as $k=>$v){
			       if(in_array($msql->f('m7'),$v)){
				       $kj[$i]["wh"] = $k;
					}
				 }
	 


			 $i++;
          }
		  $tpl->assign("kj",$kj);
        } else {
           $gamecs = getgamecs($userid);
			$gamecs = getgamename($gamecs);
			
        
            $tpl->assign('game', $gamecs);
			$msql->query("select kjurl,gname from `$tb_game` where gid='$gid'");
			$msql->next_record();
			$tpl->assign('gurl', $msql->f('kjurl'));
           $tpl->assign('gname', $msql->f('gname'));
            $tpl->assign('b', getb());
            
        } $tpl->assign('gid', $gid);$tpl->display("long.html");
        break;

}
?>