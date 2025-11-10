<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        $bid = $_REQUEST['bid'];
        $sid = $_REQUEST['sid'];
        $cid = $_REQUEST['cid'];
        $config['ztype'] = json_decode(transgame($gid,"ztype"),true);
        $config['ptype'] = json_decode(transgame($gid,"ptype"),true);
        //print_r($config['ptype']);
        if ($bid != '' & $sid != '' & $cid != '') {
            $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' and cid='$cid' order by bid,sid,cid,xsort limit 200");
        } else if ($bid != '' & $sid != '') {
            $msql->query("select * from `$tb_play` where gid='$gid' and  bid='$bid' and sid='$sid'  order by bid,sid,cid,xsort  limit 200");
        } else if ($bid != '') {
            $msql->query("select * from `$tb_play` where gid='$gid' and  bid='$bid'   order by bid,sid,cid,xsort  limit 200");
        } else {
            $msql->query("select * from `$tb_play` where gid='$gid' order by bid,sid,cid,xsort limit 200");
        }
        $i = 0;
        $p = array();
        while ($msql->next_record()) {
            $p[$i]['bid']   = $msql->f('bid');
            $p[$i]['bname'] = transb('name', $msql->f('bid'));
            
            $p[$i]['sid']   = $msql->f('sid');
            $p[$i]['sname'] = transs('name', $msql->f('sid'));
            
            $p[$i]['cid']   = $msql->f('cid');
            $p[$i]['cname'] = transc('name', $msql->f('cid'));
            
            $p[$i]['pid'] = $msql->f('pid');
            
            $p[$i]['name']   = $msql->f('name');
            $p[$i]['ifok']   = $msql->f('ifok');
            $p[$i]['xsort']  = $msql->f('xsort');
            $p[$i]['peilv1'] = $msql->f('peilv1');
            $p[$i]['peilv2'] = $msql->f('peilv2');
            $p[$i]['ztype']  = $config['ztype'][$msql->f('ztype')];
            $p[$i]['znum1']  = $msql->f('znum1');
            $p[$i]['znum2']  = $msql->f('znum2');
            $p[$i]['ptype']  = $msql->f('ptype');
            $i++;
        }
        $tpl->assign('p', $p);
        
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
        
        if ($bid != '' and $sid != '') {
            $msql->query("select * from `$tb_class` where gid='$gid' and bid='$bid'  and sid='$sid' order by xsort");
            $c = array();
            $i = 0;
            while ($msql->next_record()) {
                $c[$i]['cid']  = $msql->f('cid');
                $c[$i]['name'] = $msql->f('name');
                $i++;
            }
            $tpl->assign("c", $c);
        }
        
        
        $tpl->assign("ztype", $config['ztype']);
        $tpl->assign("ptype", $config['ptype']);
        
        $tpl->assign('bid', $bid);
        $tpl->assign('sid', $sid);
        $tpl->assign('cid', $cid);
        $tpl->assign('zh', $zh);
        $tpl->display("play.html");
        break;
    
    case "addplay":
        $bid    = $_POST['bid'];
        $sid    = $_POST['sid'];
        $cid    = $_POST['cid'];
        $name   = $_POST['name'];
        $ztype  = ztype(trim($_POST['ztype']));
        $peilv1 = $_POST['peilv1'];
        $peilv2 = $_POST['peilv2'];
        $znum1  = $_POST['znum1'];
        $znum2  = $_POST['znum2'];
        $pid    = setupid($tb_play, 'pid');
        $sql    = "insert into `$tb_play` set gid='$gid',bid='$bid',sid='$sid',cid='$cid',name='$name',ztype='$ztype',znum1='$znum1',znum2='$znum2'";
        $sql .= ",pid='$pid',ifok=1,xsort=0";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "editplay":  
        $arr = str_replace('\\', '', $_POST['str']);
        $arr = json_decode($arr, true);
        $ca  = count($arr);
		$config['ztype'] = json_decode(transgame($gid,"ztype"),true);
        for ($i = 0; $i < $ca; $i++) {
            $bid    = trim($arr[$i]['bid']);
            $sid    = trim($arr[$i]['sid']);
            $cid    = trim($arr[$i]['cid']);
            $name   = $arr[$i]['name'];
            $ztype  = ztype(trim($arr[$i]['ztype']));
            $peilv1 = $arr[$i]['peilv1'];
            $peilv2 = $arr[$i]['peilv2'];
            $znum1  = $arr[$i]['znum1'];
            $znum2  = $arr[$i]['znum2'];
            $pid    = $arr[$i]['pid'];
            $ifok   = $arr[$i]['ifok'];
            $xsort  = $arr[$i]['xs'];
            $ptype = $arr[$i]['ptype'];
            
            $sql = "update `$tb_play` set bid='$bid',sid='$sid',cid='$cid',name='$name',ztype='$ztype',znum1='$znum1',znum2='$znum2',peilv1='$peilv1',peilv2='$peilv2'";
            $sql .= ",ifok='$ifok',xsort='$xsort',ptype='$ptype' where pid='$pid' and gid='$gid'";
		
            $msql->query($sql);
            
        }
        echo 1;
        break;
    case "delplay":
        $idstr = $_POST['idstr'];
        $sql   = "delete from `$tb_play` where instr('$idstr',pid) and gid='$gid'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
	 case "downlist":
        $thispage=r1($_REQUEST['PB_page']);
        $user= trim($_REQUEST['user']);
        $whi = "xls";
        if(preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){1,10}$/", $user)){
            $whi = " where jkuser=(select userid from `$tb_user` where username='$user')";
        }
	    $msql->query("select count(id) from `x_down` $whi");
		$msql->next_record();
		$rcount=$msql->f(0);
		$page=new page(array('total'=>$rcount,'perpage'=>$config['psize']));
		
	    $msql->query("select * from `x_down` $whi  order by id desc limit ".(($thispage-1)*$config['psize']).",".$config['psize']);
		$l=array();
		$i=0;
        $tmp = [];
		while($msql->next_record()){
		    if($tmp['u'.$msql->f("userid")]==""){
              if(strlen($msql->f('userid'))<8){
                 $tmp['u'.$msql->f("userid")] = transadmin($msql->f('userid'));
              }else{
                 $tmp['u'.$msql->f("userid")] = transu($msql->f('userid'));
              }
            }
            if($tmp['x'.$msql->f('jkuser')]==""){
                $tmp['x'.$msql->f('jkuser')] = transu($msql->f('jkuser'));
            }
             $l[$i]['username'] = $tmp['u'.$msql->f("userid")];
			 $l[$i]['jkuser'] = $tmp['x'.$msql->f('jkuser')];

			 $l[$i]['id'] = $msql->f('id');
			 $l[$i]['gname'] = transgame($msql->f('gid'),'gname');
			 $l[$i]['qishu'] = $msql->f('qishu');
			 $l[$i]['time'] =$msql->f('time');
			 $l[$i]['downtype'] = $msql->f('downtype');
			 $i++;
		}
		$tpl->assign("l",$l);
		$tpl->assign('page',$page->show());
	    
	    $tpl->display("down.html");
	 break;
    case "downlistdel":
        $id = $_POST['id'];
        if ($id == 'all') {
            $msql->query("delete from `x_down`");
            exit;
        }
        if ($msql->query("delete from `x_down` where instr('$id',concat('|',id,'|'))")) {
            echo 1;
        }
        break;
        
}
?>