<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        if (is_numeric($_REQUEST['gid'])) {
            $gid = $_REQUEST['gid'];
        }
		$msql->query("select patt from `$tb_web` where wid='".$_SESSION['wid']."'");
		$msql->next_record();
		
        $msql->query("select patt".$msql->f('patt')." as patt,dftype,ftype from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $patt[0] = json_decode($msql->f('patt'), true);
        $j=1;
		$dftype = json_decode($msql->f('dftype'), true);		
		$ftype = json_decode($msql->f('ftype'), true);
        foreach ($ftype as $k => $v) {

            $cs[$k]['a'.$j]         = echoinput('a'.$j .'_'. $k, $patt[$j-1][$k]['a'], 'a'.$j);
            $cs[$k]['b'.$j]         = echoinput('b'.$j .'_'. $k, $patt[$j-1][$k]['b'], 'b'.$j);
            $cs[$k]['c'.$j]         = echoinput('c'.$j .'_'.$k, $patt[$j-1][$k]['c'], 'c'.$j);
            $cs[$k]['d'.$j]         = echoinput('d'.$j.'_'. $k, $patt[$j-1][$k]['d'], 'd'.$j);
            $cs[$k]['ab'.$j]        = echoinput('ab'.$j .'_'. $k, $patt[$j-1][$k]['ab'], 'ab'.$j);
		    $cs[$k]['name']  = $v['name'];
			$cs[$k]['bcname']  = $dftype[$v['bc']];
        }
        $tpl->assign("cs", $cs);
		$tpl->assign("flname", transgame($gid,'flname'));
		$tpl->assign("gid", $gid);
		$tpl->assign("gname", transgame($gid,'gname'));
		$game = getgamecs($userid);
		$game =getgamename($game);
		$tpl->assign("game",$game);
		$tpl->assign("config",$config);
        $tpl->display("cssz.html");
        break;
    case "setcssz":
		$msql->query("select patt from `$tb_web` where wid='".$_SESSION['wid']."'");
		$msql->next_record();
		
        $msql->query("select dftype,ftype from `$tb_game` where gid='$gid'");
        $msql->next_record();

        $j=1;
		$dftype = json_decode($msql->f('dftype'), true);		
		$ftype = json_decode($msql->f('ftype'), true);
        foreach ($ftype as $k => $v) {

			$a         = trim($_POST['a1_' . $k]);
            $b         = trim($_POST['b1_' . $k]);
            $c        = trim($_POST['c1_' . $k]);
            $xd        = trim($_POST['d1_' . $k]);
            $ab        = trim($_POST['ab1_' . $k]);
			

            $patt[$k]['a']  = $a;
            $patt[$k]['b']  = $b;
            $patt[$k]['c']  = $c;
            $patt[$k]['d']  = $xd;
            $patt[$k]['ab'] = $ab;
	
			
			
        }
		 $pan = json_encode($patt);
       $msql->query("select patt from `$tb_web` where wid='".$_SESSION['wid']."'");
		$msql->next_record();
		
       $cssz=transuser($userid,'cssz');
        if($cssz==1){
        $msql->query("update `$tb_game` set patt".$msql->f('patt')."='$pan' where gid='$gid'");
		}
        echo 1;
        break;

    case 'times':

        if (is_numeric($_REQUEST['gid'])) {
            $gid = $_REQUEST['gid'];
        }
		$page = r1p($_REQUEST['page']);
		
        $game = getgamecs($userid);
        $game = getgamename($game);
		$cg = count($game);
        $msql->query("select times from `$tb_web` where wid='".$_SESSION['wid']."'");
		$msql->next_record();
		$times = json_decode($msql->f('times'),true);
		$ct = count($times);
		
		for($i=0;$i<$cg;$i++){
		    
			for($j=0;$j<$ct;$j++){
			   if($game[$i]['gid'] == $times[$j]['gid']){
			       $game[$i]['o'] = $times[$j]['o'];
				   $game[$i]['c'] = $times[$j]['c'];
				   $game[$i]['io'] = $times[$j]['io'];
			   }
			}
		}

        $tpl->assign("game", $game);
	

		$msql->query("select count(id) from `$tb_kj` where gid='$gid'");
		$msql->next_record();
		$psize = $config['psize1'];
        $rcount   = pr0($msql->f(0));
        $pcount = $rcount%$psize==0 ? $rcount/$psize : (floor($rcount/$psize)+1);
		

        
		
		$msql->query("select closetime,opentime,qishu,kjtime,js from `$tb_kj` where gid='$gid' order by kjtime desc limit ".(($page-1)*$psize).",$psize");
		$i=0;
		$kj=array();
		$time = time();
		while($msql->next_record()){
			$kj[$i]['qishu'] = $msql->f('qishu');
			$opentime = strtotime($msql->f('opentime'))+$config['times']['o'];
			$closetime = strtotime($msql->f('closetime'))-$config['times']['c'];
			$kj[$i]['opentime'] = date("Y-m-d H:i:s",$opentime);
			$kj[$i]['closetime'] = date("Y-m-d H:i:s",$closetime);
		
			if($msql->f('qishu')==$config['thisqishu']) {
				$kj[$i]['zt'] = "开盘中...";
			}else if( $msql->f('kjtime')>$time) {
				$kj[$i]['zt'] = "";
			}else{
				if($msql->f('js')==1){
				    $kj[$i]['zt'] = "<label>已结算</label>";
				}else{
				   $kj[$i]['zt'] = "未结算";
				}
			    
			}
		
			$i++;
		}
		$tpl->assign('kj', $kj);
		$tpl->assign('gid', $gid);
		$tpl->assign('page', $page);
		$tpl->assign('rcount', $rcount);
		$tpl->assign('pcount', $pcount);
		$tpl->assign("config",$config);
        $tpl->display('times.html');
        break;
    case 'settimes':
		 $cssz=transuser($userid,'cssz');
        if($cssz!=1){
           exit;
		}
          $times = $_POST['times'];
		  $msql->query("update `$tb_web` set times='$times' where wid='".$_SESSION['wid']."'");
        echo 1;
        break;

		
	case "yiwotongbucssz": 
	 $cssz=transuser($userid,'cssz');
        if($cssz!=1){
           exit;
		}
 $msql->query("select patt from `$tb_web` where wid='".$_SESSION['wid']."'");
		$msql->next_record();
		$patti=$msql->f('patt');
     
         $gid = $_POST['gid'];	
		 $msql->query("select patt".$patti." as patt from `$tb_game` where gid='$gid'");
		 $msql->next_record();
		 $patt =$msql->f('patt');
		 $msql->query("select gid from `$tb_game` where fenlei=(select fenlei from `$tb_game` where gid='$gid') and gid!='$gid'");
		 while($msql->next_record()){
			 $ngid = $msql->f('gid');
			 $fsql->query("update `$tb_game` set patt".$patti."='$patt' where gid='$ngid'");
			 
		 }
        echo 1;
	break;

}
?>