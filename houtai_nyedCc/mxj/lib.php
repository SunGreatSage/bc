<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');

switch ($_REQUEST['xtype']) {
	case "show":
        
        $page = r1p($_REQUEST['page']);
        $psize = $config['psize2'];
       
        $sqls = " and z=9 and bs=1 "; 

		$rs= $msql->arr("select count(id) from `$tb_lib` where userid='$userid' $sqls",0);
        $rcount = $rs[0][0];       
		
        $sql = "select qishu,je,peilv1,peilv2,points,content,gid,bid,sid,cid,pid,time,abcd,tid from `$tb_lib` where userid='$userid' $sqls order by time desc,id desc limit ";
        $sql .= ($tpage - 1) * $psize . ',' . $psize;
		//echo $sql;
        $msql->query($sql);
        $lib = array();
        $i   = 0;
		$tmp = array();
		$zje=0;
        while ($msql->next_record()) {
            $lib[$i]['tid'] = $msql->f('tid');
            $lib[$i]['qishu']  = $msql->f('qishu');
            $lib[$i]['je']     = (float) $msql->f('je');
            $lib[$i]['peilv1'] = (float) ($msql->f('peilv1'));
            $lib[$i]['peilv2'] = (float) ($msql->f('peilv2'));
            $lib[$i]['points']  = (float) $msql->f('points');
            $lib[$i]['content'] = $msql->f('content');
			if($tmp['g'.$msql->f('gid')]==''){
                $fsql->query("select gname,fenlei from `$tb_game` where gid='".$msql->f('gid')."'");
                $fsql->next_record();
                $tmp['g' . $msql->f('gid')] = $fsql->f('gname');
                $tmp['f' . $msql->f('gid')] = $fsql->f('fenlei');
			}		
			if($tmp['b'.$msql->f('gid').$msql->f('bid')]==''){
				$tmp['b'.$msql->f('gid').$msql->f('bid')] = transb8('name', $msql->f('bid'),$msql->f('gid'));
			}
			if($tmp['s'.$msql->f('gid').$msql->f('sid')]==''){
				$tmp['s'.$msql->f('gid').$msql->f('sid')] = transs8('name', $msql->f('sid'),$msql->f('gid'));
			}
			if($tmp['c'.$msql->f('gid').$msql->f('cid')]==''){
				$tmp['c'.$msql->f('gid').$msql->f('cid')] = transc8('name', $msql->f('cid'),$msql->f('gid'));
			}
			if($tmp['p'.$msql->f('gid').$msql->f('pid')]==''){
				$tmp['p'.$msql->f('gid').$msql->f('pid')] = transp8('name', $msql->f('pid'),$msql->f('gid'));
			}
			$lib[$i]['gid'] = $tmp['g'.$msql->f('gid')];
			$lib[$i]['wf'] = wfuser($tmp['f' . $msql->f('gid')],$tmp['b' . $msql->f('gid') . $msql->f('bid')],$tmp['s' . $msql->f('gid') . $msql->f('sid')],$tmp['c' . $msql->f('gid') . $msql->f('cid')],$tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            if($msql->f('content')!=""){
                $lib[$i]['wf'] .= $msql->f('content');
            }
			$lib[$i]['abcd'] = $msql->f('abcd');
            $lib[$i]['time'] = substr($msql->f('time'),-8);
			$lib[$i]['date'] = substr($msql->f('time'),0,10);
			$lib[$i]['ky'] = (float)(($msql->f('peilv1')-1)*$msql->f('je'));
			$zje += $lib[$i]['je'];
            $i++;
        }
  
		$tpl->assign("lib",$lib);
        $tpl->assign("zje",$zje);
		
		$tpl->assign("rcount",$rcount);
		$tpl->assign("psize",$psize);
		$tpl->assign("tpage",$tpage);
       
		// $tpl->display("lib.html"); 
		 unset($lib);
        unset($out);
        break;
    case "wjs":
        $page = r1p($_REQUEST['page']); 		
        $psize = $config['psize2'];
		
		$total=[];
        $msql->query("select sum(je),count(id),peilv1 from `$tb_lib` where userid='$userid'  and z in (7,9) and bs=1 ");
        $msql->next_record();
        $total['je'] = $msql->f(0);
        $total['zs'] = pr0($msql->f(1));
		$total['ky'] = (float)(($msql->f('peilv1')-1)*$msql->f(0));
        $rcount = $total['zs'] ;
		
		$pcount = $rcount%$psize==0 ? $rcount/$psize : ($rcount-$rcount%$psize)/$psize+1;
        if($page>$pcount) $page = $pcount;
        $sql = "select qishu,je,peilv1,peilv2,points,content,gid,bid,sid,cid,pid,time,abcd,tid,z from `$tb_lib` where userid='$userid' and z in(7,9) and bs=1 order by time desc,id desc limit ";
		$sql .= ($page - 1) * $psize . ',' . $psize;	
        $msql->query($sql);
        $lib = array();
        $tmp = array();
        $zje=0;
        $i=0;
        while ($msql->next_record()) {
            $lib[$i]['tid'] = $msql->f('tid');
            $lib[$i]['qishu']  = $msql->f('qishu');
            $lib[$i]['je']     = (float) $msql->f('je');
            $lib[$i]['peilv1'] = (float) ($msql->f('peilv1'));
            $lib[$i]['peilv2'] = (float) ($msql->f('peilv2'));
            $lib[$i]['points']  = (float) $msql->f('points');
            $lib[$i]['z']  = (float) $msql->f('z');
            $lib[$i]['content'] = $msql->f('content');
            if ($tmp['g' . $msql->f('gid')] == '') {
                $fsql->query("select gname,fenlei from `$tb_game` where gid='".$msql->f('gid')."'");
                $fsql->next_record();
                $tmp['g' . $msql->f('gid')] = $fsql->f('gname');
                $tmp['f' . $msql->f('gid')] = $fsql->f('fenlei');
            }
            if ($tmp['b' . $msql->f('gid') . $msql->f('bid')] == '') {
                $tmp['b' . $msql->f('gid') . $msql->f('bid')] = transb8('name', $msql->f('bid'), $msql->f('gid'));
            }
            if ($tmp['s' . $msql->f('gid') . $msql->f('sid')] == '') {
                $tmp['s' . $msql->f('gid') . $msql->f('sid')] = transs8('name', $msql->f('sid'), $msql->f('gid'));
            }
            if ($tmp['c' . $msql->f('gid') . $msql->f('cid')] == '') {
                $tmp['c' . $msql->f('gid') . $msql->f('cid')] = transc8('name', $msql->f('cid'), $msql->f('gid'));
            }
            if ($tmp['p' . $msql->f('gid') . $msql->f('pid')] == '') {
                $tmp['p' . $msql->f('gid') . $msql->f('pid')] = transp8('name', $msql->f('pid'), $msql->f('gid'));
            }
            $lib[$i]['gid'] = $tmp['g' . $msql->f('gid')];
            $lib[$i]['wf'] = wfuser($tmp['f' . $msql->f('gid')], $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            if($msql->f('content')!=""){
                $lib[$i]['wf'] .= $msql->f('content');
            }
			//新加
			$lib[0]['page'] = $page;
            $lib[0]['pcount'] = $pcount;
			$lib[0]['total'] = $total;
			//
            $lib[$i]['abcd'] = $msql->f('abcd');
            $lib[$i]['time'] = substr($msql->f('time'),-8);
            $lib[$i]['date'] = substr($msql->f('time'),5,5);
            $lib[$i]['ky'] = (float)(($msql->f('peilv1')-1)*$msql->f('je'));
            $zje += $lib[$i]['je'];
            $i++;
        }
        echo json_encode($lib);
        break;

    case "download":
        $qishu = explode('~', $_REQUEST['qishu']);
        $q1    = trim($qishu[0]);
        $q2    = trim($qishu[1]);
        $yq    = " and qishu>=$q2 and qishu<=$q1  and gid='$gid' ";
        header("Content-type: text/html; charset=utf-8");
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: filename=$qishu.xls");
        if ($config['panstatus'] == 1 | $config['otherstatus'] == 1) {
            echo "为保证大家的投|注网速，开盘期间不提供下载";
            exit;
        }
        echo "期数", "\t";
        echo "类型", "\t";
        echo "大类", "\t";
        echo "小类", "\t";
        echo "玩法", "\t";
        echo "大盘", "\t";
        echo "小盘", "\t";
        echo "内容", "\t";
        echo "金额", "\t";
        echo "赔率1", "\t";
        echo "赔率2", "\t";
        echo "退水", "\t";
        echo "会员", "\t";
        echo "时间", "\t\n";
        $sql = " select * from `$tb_lib` where 1=1 $yq and userid='$userid'  order by time desc ";
        $msql->query($sql);
        while ($msql->next_record()) {
            echo $msql->f('year') . $msql->f('qishu'), "\t";
            echo transxtype($msql->f('xtype')), "\t";
            $bid = transbclass('name', $msql->f('bid'));
            $cid = transclass('name', $msql->f('cid'));
            if ($msql->f('bid') == 23378688) {
                $bid = $mtype[transclass('mtype', $msql->f('cid'))];
            }
            $pid = transplay('name', $msql->f('pid'));
            echo $bid, "\t";
            echo $cid, "\t";
            echo $pid, "\t";
            if (is_numeric($msql->f('abcd'))) {
                echo '', "\t";
            } else {
                echo $msql->f('abcd'), "\t";
            }
            if (is_numeric($msql->f('ab'))) {
                echo '', "\t";
            } else {
                echo $msql->f('ab'), "\t";
            }
            echo $msql->f('content'), "\t";
            echo $msql->f('je'), "\t";
            echo $msql->f('peilv1'), "\t";
            echo $msql->f('peilv2'), "\t";
            echo $msql->f('points'), "\t";
            echo transu($msql->f('userid'), 'username'), "\t";
            echo substr($msql->f('time'),-8), "\t";
            echo "\n";
        }
        $msql->query("insert into x_down set userid='$userid',time=NOW(),downtype='xls',qishu='$thisqishu'");
        break;
    case "downloadloto":
	exit;
        if ($config['panstatus'] == 1) {
            outjs("为保证会员的速度，开盘期间不提供下载");
            exit;
        }
		$qishu = $_REQUEST['qishu'];
        $msql->query("select ljs,fid1 from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $fid1     = $msql->f('fid1');
        $ljs      = $msql->f('ljs');
        if ($thelayer > 1) {
            $msql->query("select ljs from `$tb_user` where userid='$fid1'");
            $msql->next_record();
            $ljs = $msql->f('ljs');
        }
        header("Content-type: text/html; charset=utf-8");
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: filename=$qishu.xls");
       
        echo "期数", "\t";
        echo "内容", "\t";
        //echo "金额", "\t";
        echo "会员", "\t";
        echo "时间", "\t\n";
		if($ljs==1){
        $sql = " select * from `$tb_lib` where gid='$gid' and qishu='$qishu' where uid1='$fid1' order by time desc ,id desc ";
		}else{
        $sql = " select * from `$tb_lib` where gid='$gid' and qishu='$qishu' order by time desc ,id desc ";
		}

        $msql->query($sql);
        while ($msql->next_record()) {
            echo $msql->f('qishu'), "\t";
            echo $msql->f('content'), "\t";
            //echo $msql->f('je'), "\t";
            echo transuser($msql->f('userid'), 'username'), "\t";
            echo substr($msql->f('time'),-8), "\t";
            echo "\n";
        }
        $msql->query("insert into x_down set gid='$gid',userid='$userid',time=NOW(),downtype='xls',qishu='$qishu'");
        break;

}

?>