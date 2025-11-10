<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "setpeilvall":
        $pl   = $_POST['pl'];
        $pl   = json_decode(str_replace('\\', "", $pl));
        //print_r($pl);
        foreach ($pl as $key => $v) {
            if (substr($key, 0, 2) == 'p1') {
                $tmp = str_replace('p1', '', $key);
                $sql = "update `$tb_play` set mp1=$v where gid='$gid' and  pid='$tmp'";
                //echo $sql,"<BR>";
                $msql->query($sql);

            } else {
                $tmp = str_replace('p2', '', $key);
                $sql = "update `$tb_play` set mp2='$v' where pid='$tmp'";
                //echo $sql;
                $msql->query($sql);
            }
        }
        echo 1;
        break;
    case "setatt":
        $pid    = $_POST['pid'];
        $val    = $_POST['val'];
        $action = $_POST['action'];
        if ($action == 0) {
            $sql = "update `$tb_play` set mp1=if(mp1-$val>1,mp1-$val,1) where gid='$gid' and instr('$pid',pid)";
        } else {
            $sql = "update `$tb_play` set mp1=mp1+$val  where  gid='$gid'  and instr('$pid',pid)";
        }
        if ($msql->query($sql)) {
            echo 1;
        }
        
        break;
    case "setatttwo":
        $action = $_POST['action'];
        $pid    = $_POST['pid'];
        $sql    = "select ftype from `$tb_class` where gid='$gid' and cid=(select cid from `$tb_play` where  gid='$gid' and pid='$pid')";
        $msql->query($sql);
        $msql->next_record();
        $att = transatt($msql->f('ftype'), 'peilvatt');
        if ($action == 'down') {
            $msql->query("update `$tb_play` set mp1=if(mp1-$att>1,mp1-$att,1)  where gid='$gid' and pid='$pid'");
        } else {
            $msql->query("update `$tb_play` set mp1=mp1+$att  where  gid='$gid' and pid='$pid'");
        }
        echo $att;
        break;
	case "changeifok";
	   $pid = $_POST['pid'];
	   $msql->query("update `$tb_play` set ifok=if(ifok=0,1,0) where gid='$gid' and  pid='$pid'");
	   $msql->query("select ifok from `$tb_play` where gid='$gid' and  pid='$pid' ");
	   $msql->next_record();
	   echo $msql->f('ifok');
	   
	break;
	
	case "huifumoren":
	    $time = time();
	    $msql->query("update `$tb_play` set peilv1=mp1,peilv2=mp2,start=0,autocs=0,zqishu=0,buzqishu=0    where gid='$gid'");
		$fsql->query("insert into `$tb_peilv` set gid='$gid',pid='0',peilv=0,time=NOW(),userid='$userid',sonuser='$adminid',auto=3");
		echo 1;
	break;

}
?>