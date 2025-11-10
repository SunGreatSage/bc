<?php include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
switch($_REQUEST['xtype']) {
	case "show":          $zpan=getzpan();
	$cz=count($zpan);
	for ($i=0;$i<$cz;$i++) {
		$zpan[$i]['points']  = echoinput('points'.$zpan[$i]['class'],transatt($zpan[$i]['class'],'points'));
		$zpan[$i]['pointsatt']  = echoinput('pointsatt'.$zpan[$i]['class'],transatt($zpan[$i]['class'],'pointsatt'));
		$zpan[$i]['peilvatt']  = echoinput('peilvatt'.$zpan[$i]['class'],transatt($zpan[$i]['class'],'peilvatt'));
		$zpan[$i]['b']  = echoinput('b'.$zpan[$i]['class'],transatt($zpan[$i]['class'],'b'));
		$zpan[$i]['c']  = echoinput('c'.$zpan[$i]['class'],transatt($zpan[$i]['class'],'c'));
		$zpan[$i]['d']  = echoinput('d'.$zpan[$i]['class'],transatt($zpan[$i]['class'],'d'));
	}
	$tpl->assign("zpan",$zpan);
	$tpl->display("setatt.html");
	break;
	case "setatt":          $zpan=getzpan();
	$cz=count($zpan);
	for ($i=0;$i<$cz;$i++) {
		$points=$_POST['points'.$zpan[$i]['class']];
		$pointsatt=$_POST['pointsatt'.$zpan[$i]['class']];
		$peilvatt=$_POST['peilvatt'.$zpan[$i]['class']];
		$b=$_POST['b'.$zpan[$i]['class']];
		$c=$_POST['c'.$zpan[$i]['class']];
		$xd=$_POST['d'.$zpan[$i]['class']];
		$msql->query("select maxatt from `$tb_att` where class='".$zpan[$i]['class']."'");
		$msql->next_record();
		$maxatt = $msql->f('maxatt');
		$msql->query("delete from `$tb_att` where class='".$zpan[$i]['class']."'");
		$sql=" insert into  `$tb_att` set  points='$points',pointsatt='$pointsatt',peilvatt='$peilvatt',b='$b',c='$c',d='$xd',class='".$zpan[$i]['class']."',maxatt='$maxatt'";
		$msql->query($sql);
		if($zpan[$i]['class']>4) {
			$patt['p'.$zpan[$i]['class']]['a']  = 0;
			$patt['p'.$zpan[$i]['class']]['b']  = $b;
			$patt['p'.$zpan[$i]['class']]['c']  = $c;
			$patt['p'.$zpan[$i]['class']]['d']  = $xd;
			$ftype['p'.$zpan[$i]['class']] = $zpan[$i]['name'];
		} else {
			$patt[$zpan[$i]['class']]['a']  = 0;
			$patt[$zpan[$i]['class']]['b']  = $b;
			$patt[$zpan[$i]['class']]['c']  = $c;
			$patt[$zpan[$i]['class']]['d']  = $xd;
			$ftype[$i] = $zpan[$i]['name'];
		}
	}
	$sql = " ftype='".json_encode($ftype)."',patt='".json_encode($patt)."'";
	$sql =  str_replace('u','\u',$sql);
	$sql = "update `$tb_config` set " .$sql;
	$msql->query($sql);
	echo 1;
	break;
}
?>