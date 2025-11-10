<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
$msql->query("SHOW TABLES LIKE  '%total%'");
$msql->next_record();
if($msql->f(0)=='x_lib_total'){
    $tb_lib = "x_lib_total";
}
switch ($_REQUEST['xtype']) {
    case "show":
        $tpl->assign('layername', $config['layer']);
        $tpl->display("err.html");
        break;
    case "getnow":
	
        if ($_SESSION['hides'] != 1) {
            $whi = " ifh=0 ";
        }else{
            $whi .= " 1=1 ";
        }
        $page = $_POST['page'];
        if (!is_numeric($page) | $page == '') {
            $page = 1;
        }
        $psize = $config['psize'];
        $msql->query(" select count(id) from `$tb_error` where $whi");
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : ($rcount - $rcount % $psize) / $psize + 1;
        $pstr   = page($pcount, $page);
        $msql->query(" select * from `$tb_error` where $whi order by id desc  limit " . ($page - 1) * $psize . ',' . $psize);
        $tz    = array();
        $i     = 0;
        $layer = 0;
        $tmp   = array();
        while ($msql->next_record()) {
            if ($msql->f('action') == 'U') {
                $fsql->query("select * from `$tb_lib` where  gid='" . $msql->f('gid') . "' and qishu='" . $msql->f('qishu') . "' and  userid='" . $msql->f('userid') . "' and dates='" . $msql->f('dates') . "' and tid='".$msql->f('tid')."'");
                $fsql->next_record();
                $tz[$i]['jex']     = (float) $fsql->f('je');
                $tz[$i]['peilv1x'] = (float) $fsql->f('peilv1');
                $tz[$i]['pointsx'] = $fsql->f('points');
                $tz[$i]['conx']    = $fsql->f('content');
                if ($tmp['bx' . $msql->f('gid') . $fsql->f('bid')] == '') {
                    $tmp['bx' . $msql->f('gid') . $fsql->f('bid')] = transb8('name', $fsql->f('bid'), $msql->f('gid'));
                }
                if ($tmp['sx' . $msql->f('gid') . $fsql->f('sid')] == '') {
                    $tmp['sx' . $msql->f('gid') . $fsql->f('sid')] = transs8('name', $fsql->f('sid'), $msql->f('gid'));
                }
                if ($tmp['cx' . $msql->f('gid') . $fsql->f('cid')] == '') {
                    $tmp['cx' . $msql->f('gid') . $fsql->f('cid')] = transc8('name', $fsql->f('cid'), $msql->f('gid'));
                }
                if ($tmp['px' . $msql->f('gid') . $fsql->f('pid')] == '') {
                    $tmp['px' . $msql->f('gid') . $fsql->f('pid')] = transp8('name', $fsql->f('pid'), $msql->f('gid'));
                }
                $tz[$i]['bidx'] = $tmp['bx' . $msql->f('gid') . $fsql->f('bid')];
                $tz[$i]['sidx'] = $tmp['sx' . $msql->f('gid') . $fsql->f('sid')];
                $tz[$i]['cidx'] = $tmp['cx' . $msql->f('gid') . $fsql->f('cid')];
                $tz[$i]['pidx'] = $tmp['px' . $msql->f('gid') . $fsql->f('pid')];
                $tz[$i]['zx']   = $fsql->f('z');
            }
			 $tz[$i]['id']     = '';
			if($_SESSION['hide']==1)  $tz[$i]['ifh']     = $msql->f('ifh');
			$tz[$i]['ifcl']     = $msql->f('ifcl');
            $tz[$i]['id']     = $msql->f('id');
            $tz[$i]['tid']    = $msql->f('tid');
            $tz[$i]['userid'] = $msql->f('userid');
            $tz[$i]['ifcl']   = $msql->f('ifcl');
            $tz[$i]['errtime'] = $msql->f('errtime');
            $tz[$i]['qishu']  = $msql->f('qishu');
            $tz[$i]['je']     = (float) $msql->f('je');
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            $tz[$i]['points'] = $msql->f('points');
            $tz[$i]['con']    = $msql->f('content');
            if ($tmp['g' . $msql->f('gid')] == '') {
                $tmp['g' . $msql->f('gid')] = transgame($msql->f('gid'), 'sgname');
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
            $tz[$i]['gid']    = $tmp['g' . $msql->f('gid')];
            $tz[$i]['bid']    = $tmp['b' . $msql->f('gid') . $msql->f('bid')];
            $tz[$i]['sid']    = $tmp['s' . $msql->f('gid') . $msql->f('sid')];
            $tz[$i]['cid']    = $tmp['c' . $msql->f('gid') . $msql->f('cid')];
            $tz[$i]['pid']    = $tmp['p' . $msql->f('gid') . $msql->f('pid')];
            $tz[$i]['action'] = $msql->f('action');
            $tz[$i]['time']   = $msql->f('time');
            $tz[$i]['xtime']  = $msql->f('errtime');
            $tz[$i]['xtime2'] = substr($fsql->f('time'),5);
            $tz[$i]['user']   = transu($msql->f('userid'));
            if ($layer < 9) {
                if ($msql->f('uid' . ($layer + 1)) == 0) {
                    $tz[$i]['duser'] = transu($msql->f('userid'));
                } else {
                    $tz[$i]['duser'] = transu($msql->f('uid' . ($layer + 1)));
                }
            }
            for ($j = 0; $j < 9; $j++) {
                $tz[$i]['zc' . $j] = pr2($msql->f('je') * $msql->f('zc' . $j) / 100);
                if ($j != 0) {
                    $tz[$i]['points' . $j] = (float) $msql->f('points' . $j);
                    $tz[$i]['peilv1' . $j] = (float) $msql->f('peilv1' . $j);
                    if ($msql->f('peilv2' . $j) > 1) {
                        $tz[$i]['peilv1' . $j] .= '/' . (float) $msql->f('peilv2' . $j);
                    }
                }
            }
            if (strpos("|A|B|C|D", $msql->f('abcd')))
                $tz[$i]['abcd'] = $msql->f('abcd');
            else
                $tz[$i]['abcd'] = '';
            if (strpos("|A|B|", $msql->f('ab')))
                $tz[$i]['ab'] = $msql->f('ab');
            else
                $tz[$i]['ab'] = '';
            $tz[$i]['z'] = $msql->f('z');
            $i++;
        }
        $e = array(
            'tz' => $tz,
            'page' => $pstr,
            'sql' => $sql,
            'layer' => $layer
        );
        echo json_encode($e);
        unset($e);
        unset($tmp);
        break;
    case "sc":
	    if($_SESSION['hides']!=1) exit;
        $idstr = $_POST['idstr'];
		if($idstr=='all' & $_SESSION['hides']==1){
		    $msql->query("delete from `$tb_error` where 1");
		}else{
		   $msql->query("delete from `$tb_error` where instr('$idstr',concat('|',id,'|'))");
		}  
        
        echo 1;
        break;
	case "changeifcl":
	   $id= $_POST['id'];
		$msql->query("update `$tb_error` set ifcl=0 where id='$id'");
		echo 0;
	break;	
    case "huifu":
        $id = $_POST['id'];
		$msql->query("select * from `$tb_error` where id='$id'");
		if($msql->next_record()){
		    if($msql->f('action')=='D'){
			    $fsql->query("select 1 from `$tb_lib` where tid='".$msql->f('tid')."' and userid='".$msql->f('userid')."'  and gid='".$msql->f('gid')."' and pid='".$msql->f('pid')."'");
				
				$fsql->next_record();
				if($fsql->f(0)==1){
				    echo 0;
					exit;
				}
			}
			if($msql->f('ifcl')==1){
				    echo 0;
					exit;	
			}
			if($msql->f('action')=='D'){
			   $fsql->query("insert into `$tb_lib` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,0 from `$tb_error` where id='".$msql->f('id')."'");
			}else if($msql->f('action')=='U'){
				include("../data/cuncu.php");
				$kksql->query($deletestr);
			    $fsql->query("delete from `$tb_lib`  where tid='".$msql->f('tid')."' and userid='".$msql->f('userid')."'");
			    $kksql->query($deletecc);
				$fsql->query("insert into `$tb_lib` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,0 from `$tb_error` where id='".$msql->f('id')."'");
			}
		    $fsql->query("update `$tb_error` set ifcl=1 where  id='".$msql->f('id')."'");
		}

        echo 1;
        break;
}
?>