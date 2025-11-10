<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/agentfunc.php');
include('../func/csfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":

        $ifok   = array(
            "失败",
            "成功"
        );
        $e      = array();
        $msql->query("select * from `$tb_user_edit` where userid='$userid' or userid in (select userid from `$tb_user` where fid='$userid' and ifson=1) order by moditime desc limit 50");
        $i     = 0;
        $layer = transuser($userid, 'layer');
        while ($msql->next_record()) {
            $e[$i]['moditime'] = substr($msql->f('moditime'),5);
            
            if (transuser($msql->f('modiuser'), 'layer') < $layer) {
                unset($e[$i]);
                continue;
                $e[$i]['modiuser']    = "上级";
                $e[$i]['modisonuser'] = "";
            } else {
                $e[$i]['modiuser'] = transu($msql->f('modiuser'));
                $fsql->query("select username from `$tb_user` where userid='" . $msql->f('modisonuser') . "'");
                $fsql->next_record();
                $e[$i]['modisonuser'] = $fsql->f('username');
            }
            $e[$i]['username'] = transuser($msql->f('userid'), 'username');
            $e[$i]['modiip']   = $msql->f('modiip');
            $e[$i]['action']   = $msql->f('action');
            $i++;
        }
        $l = array();
        
        $msql->query("select * from `$tb_user_login` where username in (select username from `$tb_user` where userid='$userid' or (fid='$userid' and ifson=1) )and xtype!=0 order by time desc limit 50");
        $i = 0;
        while ($msql->next_record()) {
            $l[$i]['ip']   = $msql->f('ip');
            $l[$i]['time'] = substr($msql->f('time'),5);
            $l[$i]['ifok'] = $ifok[$msql->f('ifok')];
            $i++;
        }
        
        $tpl->assign("e", $e);
        $tpl->assign("l", $l);
        
        
        $tpl->assign("ifexe", transuser($userid, 'ifexe'));        
        $tpl->display('record.html');
        break;
    case "news":
        $msql->query("select * from `$tb_news` order by time desc limit 20");
        $i = 0;
        while ($msql->next_record()) {
            $news[$i]['id']      = $i + 1;
            $news[$i]['content'] = $msql->f('content');
            $news[$i]['time']    = substr($msql->f('time'),5);
            $i++;
        }
        $tpl->assign("news", $news);
        $tpl->assign("ifexe", transuser($userid, 'ifexe'));
        $tpl->display("news.html");
        break;
    
    case "peilv":
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
			$p[$i]['sonuser'] = transuser($msql->f('sonuser'),'username');
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