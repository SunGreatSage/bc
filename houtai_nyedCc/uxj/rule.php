<?php
include('../data/comm.inc.php');
include('../data/uservar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');

	    if(in_array($_REQUEST['gid'],$garr)){
	       $gid= $_REQUEST['gid'];
		}
        $msql->query("select cs,fenlei,mnum from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $cs = json_decode($msql->f("cs"),true);
        $fenlei = $msql->f("fenlei");
        $mnum = $msql->f("mnum");
        
        if($fenlei==107){
        	$arr = ["冠军","亚军","第3名","第4名","第5名","第6名","第7名","第8名","第9名","第10名"];
        }else{
        	$arr=[];
        	for($i=1;$i<=$mnum;$i++){
        		$arr[] = "第".$i."球";
        	}
        }
         
        $mstr = "";
        if($cs['ft']==1){
                $ft = explode(',', $cs['ftnum']);
              if($cs['ftmode']==1){
                   if(count($ft)==1){
                       $mstr = $arr[$ft[0]-1]."";
                   }else if(count($ft)==2){
                       $mstr = $arr[$ft[0]-1]."为十位，".$arr[$ft[1]-1]."为个位，组成的数";
                   }else if(count($ft)==3){
                       $mstr = $arr[$ft[0]-1]."为百位，".$arr[$ft[1]-1]."为十位，".$arr[$ft[2]-1]."为个位，组成的数";
                   }else if(count($ft)==4){
                       $mstr = $arr[$ft[0]-1]."为千位，".$arr[$ft[1]-1]."为百位，".$arr[$ft[2]-1]."为十位，".$arr[$ft[3]-1]."为个位，组成的数";
                   }
              }else{
                if(count($ft)==1){
                        $mstr = $arr[$ft[0]-1]."";
                }else{
                        for($j=0;$j<count($ft);$j++){
                                $mstr .= $arr[$ft[$j]-1];
                                if($j<count($ft)-1) $mstr .= "、";
                        }
                        $mstr .= "之和";
                }
              }
        }
        $tpl->assign("mstr",$mstr);
        $tpl->assign("ft",$cs['ft']);
	
		 $msql->query("select `ma`,maxpc from `$tb_config`");
		 $msql->next_record();
		 $ma = json_decode($msql->f('ma'),true);
		 
		 $tpl->assign("maxpc",$msql->f('maxpc'));
		 $tpl->assign("sx",$ma['生肖']);
		 $tpl->assign("wh",$ma['五行']);
		 $tpl->assign("bm",bml($config['thisbml']));
		 $tpl->assign("gid",$gid);
		 $gamecs = getgamecs($userid);
		 $game = getgamename($gamecs);
		 $tpl->assign("game",$game);

		 echo $tpl->fetch("rule.html");
                 //echo $tpl->fetch($gid.".html");
                 echo $tpl->fetch("r".$fenlei.".html");
		

?>