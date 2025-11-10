<?php

include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
include('../func/jsfunc.php');

$type = $_REQUEST['type'];
switch ($_REQUEST['t']) {
    case 'Mobile/Info':
        $type = "userinfo";
        break;
    case 'Mobile/ChangePassword':
        $type = "changepass";
        break;
    case 'Mobile/UnCheckBetting':
        $type = "wjs";
        break;
    case 'Mobile/CheckBetting':
        $type = "baoday";
        break;
    case 'Mobile/HistoryBetList':
        $type = "baoweek";
        break;
    case 'Mobile/ResultHistory':
        $type = "kj";
        break;
    case 'Mobile/GameRule':
        $type = "rule";
        break;
	case 'Mobile/Notification':
        $type = "nft";
        break;
}
        $sdate = week();
        $tpl->assign('sdate', $sdate);
        $tpl->assign('moneytype', $config['moneytype']);
        $msql->query("select kfurl from `$tb_config`");
        $msql->next_record();
        $tpl->assign('kfurl', $msql->f('kfurl'));

        $tpl->assign('gname', $config['gname']);
        $gamecs = getgamecs($userid);
        $gamecs = getgamename($gamecs);
        $tpl->assign('gamecs', $gamecs);
        $tpl->assign('gid', $gid);
        $tpl->assign('b', $b);
        $tpl->assign('webname', $config['webname']);
        $tpl->assign('gname', $config['gname']);
        $tpl->assign('class', $config['class']);
        $tpl->assign('kjurl', $config['kjurl']);
        $tpl->assign('fenlei', $config['fenlei']);
        $tpl->assign('fast', $config['fast']);
        $msql->query("select * from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $tpl->assign('username', $msql->f('username').'('.$msql->f('defaultpan').'盘)');
		$tpl->assign('accountLimit', $msql->f('kmoney'));

        if(!in_array($type, ['wjs','baoday','baoweek','changepass','kj','rule','userinfo','nft'])) $type='kj';
        $tpl->assign("type",$type);
        if(date("His")<str_replace(':','',$config['editstart'])){
            $dates = date("Y-m-d",time()-86400);
        }else{
            $dates = date("Y-m-d");
        }
        $tpl->assign("dates",$dates);
        $tpl->assign("app",$_SESSION['app']);//var_dump($type);die;
        $tpl->display('other.html');
