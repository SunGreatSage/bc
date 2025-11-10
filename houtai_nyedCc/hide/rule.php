<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case 'show':
		 if(in_array($_REQUEST['gid'],$garr)){
	       $gid= $_REQUEST['gid'];
		}
		$msql->query("select cs,fenlei,mnum from `$tb_game` where gid='$gid'");
        $msql->next_record();
		$fenlei = $msql->f("fenlei");
	     $zong = $tpl->fetch("rule_zong.html");
		 $msql->query("select `ma`,maxpc from `$tb_config`");
		 $msql->next_record();
		$ma = json_decode($msql->f('ma'),true);
		 $tpl->assign("maxpc",$msql->f('maxpc'));
		 $tpl->assign("sx",$ma['ÉúÐ¤']);
		 $tpl->assign("wh",$ma['ÎåÐÐ']);
	//	 $tpl->assign("bm",bml($config['thisbml']));
		  $tpl->assign("gid",$gid);
		 $g = $tpl->fetch("rule_".$fenlei.".html");
		 $tpl->assign('zong',$zong);
		 $tpl->assign('g',$g);
		  $gamecs = getgamecs($userid);
		 $game = getgamename($gamecs);
		 $tpl->assign("game",$game);
		 $tpl->display("rule.html");
        break;
}