<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
     
switch($_REQUEST['xtype']){
    case "show":
	    if(is_numeric($_REQUEST['gid'])) $gid = $_REQUEST['gid'];
	    $psize = $config['psize'];
        $msql->query("select count(id) from `$tb_peilv` where userid='$userid' and gid='$gid'");
        $msql->next_record();
        $rcount   = $msql->f(0);
        $pcount = $rcount%$psize==0 ? $rcount/$psize : (($rcount-$rcount%$psize)/$psize) + 1;
		
        $thispage = $_REQUEST['page'];
		if(!is_numeric($thispage)) $thispage = 1;

		
		$gname = transgame($gid,'gname');
		$auto = array("手动","自动","写入默认","恢复默认","赔率清零");
        $msql->query("select * from `$tb_peilv` where userid='$userid' and gid='$gid' order by time desc limit " . (($thispage - 1) * $psize) . ",$psize");
		$i=0;
		$p=array();
		while($msql->next_record()){
		    $p[$i]['gname'] = $gname;
			$p[$i]['peilv'] = $msql->f('peilv');
						$fsql->query("select adminname from `$tb_admins` where adminid='".$msql->f('sonuser')."'");
			$fsql->next_record();
			$p[$i]['sonuser'] = $fsql->f('adminname');
			$p[$i]['auto'] = $auto[$msql->f('auto')];
			$p[$i]['time']    = substr($msql->f('time'),5);
			if($msql->f('pid')==0){
			    $p[$i]['pid']= '';
			}else{
				$fsql->query("select * from `$tb_play` where gid='$gid' and pid='".$msql->f('pid')."'");
				
				$fsql->next_record();
		     	$p[$i]['pid']= transb('name',$fsql->f('bid')).'-'.transs('name',$fsql->f('sid')).'-'.transc('name',$fsql->f('cid')).':'.$fsql->f('name');
			}
			
			$i++;
		}
        $tpl->assign("gid", $gid);
        $tpl->assign("game", getgame());
		$tpl->assign("p", $p);
		$tpl->assign('page', page($pcount,$thispage));
        $tpl->display("peilv.html");
        break;
}
?>