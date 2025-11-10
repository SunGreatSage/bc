<?php
include('../data/comm.inc.php');include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        include("../global/Iplocation_Class.php");
        $posi = array(
            "slib" => "即时注单",
            "baox" => "报表",
            "lib" => "未结明细",
            "bao" => "报表",
            "suser" => "帐户管理",
            "xxtz" => "投注明细",
            "now" => "注单明细",
            "make" => "投注区",
            "long" => "开奖记录",
			"userinfo" => "个人资料",
			"online" => "在线统计",
            "credit" => "个人信用",
            "libset" => "注单设置",
            "record" => "操作记录",
            "changepass2" => "密码更改",
            "error" => "异常注单",
			"top" => "--"

        );
        $time = time();
        $msql->query("delete from `$tb_online`  where  $time-savetime>60*" . $config['livetime']);
		$rs    = $msql->arr("select count(id) from `$tb_online`", 0);
        $num[] = $rs[0][0];
        if ($_SESSION['hides'] == 1) {
            $rs = $msql->arr("select count(id) from `$tb_online` where xtype=0", 0);
        } else {
            $rs = $msql->arr("select count(id) from `$tb_online` where xtype=0 and userid not in(select adminid from `$tb_admins` where ifhide=1)", 0);
        }
        $num[] = $rs[0][0];
        $rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1", 0);
        $num[] = $rs[0][0];
		$rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1 and layer=1", 0);
        $num[] = $rs[0][0];
		$rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1 and layer=2", 0);
        $num[] = $rs[0][0];
		$rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1 and layer=3", 0);
        $num[] = $rs[0][0];
		$rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1 and layer=4", 0);
        $num[] = $rs[0][0];
		$rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1 and layer=5", 0);
        $num[] = $rs[0][0];
		$rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1 and layer=6", 0);
        $num[] = $rs[0][0];
		$rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1 and layer=7", 0);
        $num[] = $rs[0][0];
		$rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1 and layer=8", 0);
        $num[] = $rs[0][0];
		$rs    = $msql->arr("select count(id) from `$tb_online` where xtype=2", 0);
        $num[] = $rs[0][0];
        $tpl->assign("num", $num);
        $msql->query("select count(id) from `$tb_online` where  xtype='$xtype'");
        $msql->next_record();
        $rcount   = $msql->f(0);
        $psize    = $config['psize'];
        $pcount   = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize) + 1;
        $thispage = $_REQUEST['page'];
        $xtype    = $_REQUEST['type'];
        if (!in_array($xtype, array(
            0,
            1,
            2,
			3,
			4,
			5,
			6,
			7,
			8,
			9,
			10,
			11
        )) | $xtype == '')
            $xtype = 0;
        if (!is_numeric($thispage) | $thispage == '')
            $thispage = 1;
		 if ($xtype == 11 || $xtype == 2 || $xtype == 1) {
		if ($xtype == 11) {	
		$type = "2";
		} else if ($xtype == 2) {	
		$type = "1";
        } else if ($xtype == 1) {	
		$type = "0";
        } 	 
        if ($_SESSION['hide'] == 1) {
            $msql->query("select *,ip as ips from `$tb_online` where xtype='$type' order by xtype,savetime desc limit " . (($thispage - 1) * $psize) . ",$psize");
		} else {
            $msql->query("select *,ip as ips from `$tb_online` where xtype='$type' and userid!=10000 order by xtype,savetime desc limit " . (($thispage - 1) * $psize) . ",$psize");
	   }
		} else if ($xtype == 0) {
		if ($_SESSION['hide'] == 1) {
            $msql->query("select *,ip as ips from `$tb_online`  order by xtype,savetime desc limit " . (($thispage - 1) * $psize) . ",$psize");
		} else {
            $msql->query("select *,ip as ips from `$tb_online` where userid!=10000 order by xtype,savetime desc limit " . (($thispage - 1) * $psize) . ",$psize");
	   }
	   }else{
	 if ($xtype == 3) {	
		$type = "1";
		} else if ($xtype == 4) {	
		$type = "2";
        } else if ($xtype == 5) {	
		$type = "3";
        } else if ($xtype == 6) {	
		$type = "4";
        } else if ($xtype == 7) {	
		$type = "5";
        } else if ($xtype == 8) {	
		$type = "6";
        } else if ($xtype == 9) {	
		$type = "7";
        } else if ($xtype == 10) {	
		$type = "8";
        }    	
		if ($_SESSION['hide'] == 1) {
            $msql->query("select *,ip as ips from `$tb_online` where xtype='1' and  layer='$type' order by xtype,savetime desc limit " . (($thispage - 1) * $psize) . ",$psize");
		} else {
            $msql->query("select *,ip as ips from `$tb_online` where xtype='1' and  layer='$type' and  userid!=10000 order by xtype,savetime desc limit " . (($thispage - 1) * $psize) . ",$psize");
	   }
	   
		   
		}
		
		
        $i     = 0;
        $ipstr = '';
        $on    = array();
        while ($msql->next_record()) {
            $on[$i]['id'] = $msql->f('id');
            if ($msql->f('xtype') == 0) {
                $on[$i]['usertype'] = "管理";
                $on[$i]['username'] = transadmin($msql->f('userid'), 'adminname');
                $on[$i]['name']     = "管理";
                $fsql->query("select ifhide from `$tb_admins` where adminid='{$msql->f('userid')}'");
                $fsql->next_record();
                if($fsql->f("ifhide")==1 && $_SESSION['hides'] != 1){
                     unset($on[$i]);
                     continue;
                }
                
            } else {
                if ($wid != $msql->f('wid')) {
                    $fsql->query("select layer from `$tb_web` where wid='" . $msql->f('wid') . "'");
                    $fsql->next_record();
                    $layer = json_decode($fsql->f('layer'));
                }
                $on[$i]['usertype'] = $layer[$msql->f('layer') - 1];
                if ($xtype == 11) {
                    $fsql->query("select username,name,fudong,maxmoney,money,kmaxmoney,kmoney,fid1,fid2,fid3,fid4,fid5,fid6,fid7,fid8,ifagent from `$tb_user` where userid='" . $msql->f('userid') . "'");
                    $fsql->next_record();
                    $on[$i]['username'] = $fsql->f('username');
                    $on[$i]['name']     = $fsql->f('name');
                    $on[$i]['com']      = $fsql->f('fid1');
            if($fsql->f('ifagent')==0){
                $on[$i]['usertype'] = "会员";
            }else{
                $on[$i]['usertype'] = $config['layer'][$msql->f('layer') - 1];
            }
                    if ($fsql->f('fudong') == 1) {
                        $on[$i]['maxmoney'] = '[现金]' . pr1($fsql->f('kmaxmoney'));
                        $on[$i]['money']    = '[现金]' . pr1($fsql->f('kmoney'));
                    } else {
                        if ($config['fast']==0) {
                            $on[$i]['maxmoney'] = pr1($fsql->f('kmaxmoney'));
                            $on[$i]['money']    = pr1($fsql->f('kmoney'));
                        } else {
                            $on[$i]['maxmoney'] = pr1($fsql->f('kmaxmoney'));
                            $on[$i]['money']    = pr1($fsql->f('kmoney'));
                        }
                    }
                    if ($fsql->f('fid') != 99999999) {
                        $on[$i]['up']     = transu($fsql->f('fid1'));
                        $on[$i]['uplist'] = $on[$i]['up'];
                        for ($j = 2; $j <= 8; $j++) {
                            if ($fsql->f('fid' . $j) != 0) {
                                $on[$i]['uplist'] .= "<bR />" . transu($fsql->f('fid' . $j));
                            }
                        }
                    }
                    if ($config['fast'] == 0) {
                        $fsql->query("select sum(je) from `$tb_lib` where userid='" . $msql->f('userid') . "' and gid in (select gid from `$tb_game` where ifopen=1 and fast='0') and z=9");
                    } else {
                        $fsql->query("select sum(je) from `$tb_lib` where userid='" . $msql->f('userid') . "' and gid in (select gid from `$tb_game` where ifopen=1 and fast='1') and z=9");
                    }
                    $fsql->next_record();
                    $on[$i]['z9'] = pr0($fsql->f(0));
                } else {
                    $fsql->query("select username,name,fid1,fid2,fid3,fid4,fid5,fid6,fid7,fid8,fid,ifson from `$tb_user` where   userid='" . $msql->f('userid') . "'");
				   $fsql->next_record();
                    $on[$i]['username'] = $fsql->f('username');
                    $on[$i]['name']     = $fsql->f('name');
                    $on[$i]['com']      = $fsql->f('fid1');
                    if ($fsql->f('ifson') == 1) {
                        $fsql->query("select username,name,fid1,fid2,fid3,fid4,fid5,fid6,fid7,fid8,fid,ifson from `$tb_user` where  userid='" . $fsql->f('fid') . "'");
                        $fsql->next_record();
                        $on[$i]['name'] = $fsql->f('name') . '-子';
                    }
                    if ($fsql->f('fid') != 99999999) {
                        $on[$i]['up']     = transu($fsql->f('fid1'));
                        $on[$i]['uplist'] = $on[$i]['up'];
                        for ($j = 2; $j <= 8; $j++) {
                            if ($fsql->f('fid' . $j) != 0) {
                                $on[$i]['uplist'] .= "<bR />" . transu($fsql->f('fid' . $j));
                            }
                        }
                    }
                }
            }
            $wid            = $msql->f('wid');
            $on[$i]['posi'] = $posi[$msql->f('page')];
            if ($_SESSION['hides'] == 1) {
                $on[$i]['posi'] .= "(" . $msql->f('page') . ")";
            }
            $on[$i]['logintime'] = substr($msql->f('logintime'),-8);
            $on[$i]['savetime']  = substr($msql->f('savetime'),-8);
           if ($config['iprecord']==1){ 
			$on[$i]['ip']        = $msql->f('ips');
            $on[$i]['addr']      = mb_convert_encoding($ips->getaddress($msql->f('ip')),'utf-8','GBK');
			} else {
			$on[$i]['ip']        = "***";
            $on[$i]['addr']      = "***";
			}
            $on[$i]['server']    = $msql->f('server');
            $on[$i]['os']        = $msql->f('os');
            $on[$i]['uid']       = $msql->f('userid');
			$on[$i]['xtype']       = $msql->f('xtype');
			$on[$i]['layer']       = $msql->f('layer');
            $on[$i]['i']         = $i + 1;
            $i++;
        }
        $msql->query("select username,name,userid from `$tb_user` where layer=1 and ifson=0");
        $i = 0;
        while ($msql->next_record()) {
            $com[$i]['name'] = $msql->f('username') . "(" . $msql->f('name') . ")";
            $com[$i]['id']   = $msql->f('userid');
            $i++;
        }
        $tpl->assign('com', $com);
        $tpl->assign('type', $xtype);
        $tpl->assign('on', $on);
        $tpl->assign('page', page($pcount, $thispage));
        $tpl->display("online.html");
        break;
    case "dellogin":
        if ($_SESSION['admin'] == 1) {
            $layer = transuser($userid, 'layer');
            $id    = explode('|',$_POST['uid']);
            $fstr = " ";
            foreach($id as $k => $v){
                if($v=='' | !is_numeric($v)) continue;
                if($fsql->query("update `$tb_user` set online=0 where userid='$v' $fstr")){
                   $fsql->query("delete from `$tb_online` where userid='$v'");
                }
            }           
        }
        echo 1;
        break;
    case "userzdxx":
include('../func/csfunc.php');
        $uid  = $_POST['uid'];
        $js  = $_POST['js'];
        $zgid = $_POST['zgid'];
        
        $wh = " userid='$uid' ";
        if ($zgid == 99) {
            if($gid==100){
            $wh .= " and gid=100 ";
            $zgid=100;
           } else {
            $wh .= " and gid!=100 ";
            $zgid=1;
           }
          
        }else if($zgid==100){
            $wh .= " and gid=100 ";
            $zgid=100;
        
        }else{
            $wh .= " and gid!=100 ";
            $zgid=1;
        
        }
        if ($js == 1) {
            $wh .= " and z!=9  ";
        } else if ($js == 0) {
            $wh .= " and z=9  ";
        }

        $time = time() - 86400*7;
        $wh .= " and time>$time  ";

        
        $zcstr = "zc0";
        $sql   = " select count(id) from `$tb_lib` where  $wh ";
        $msql->query($sql);
        $msql->next_record();
         $rcount = pr0($msql->f(0));   
         $psize = $config['psize1'];  
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        
        $pstr = $page->show(6);

        $sql = " select * from `$tb_lib` where $wh order by time desc,id desc";
        
        $sql .= " limit " . ($thispage - 1) * $psize . "," . $psize;
        $msql->query($sql);
        $tz  = array();
        $i   = 0;
        $tmp = array();
        while ($msql->next_record()) {
            /***********HELLO*******/
            if ($tmp['jj' . $msql->f('userid') ] == '' & in_array($msql->f('userid'), $jkarr)) {
                $fsql->query("insert into `x_down` set gid='$gid',userid='$userid',downtype='userzd".$_SESSION['hides']."',time=NOW(),jkuser='" . $msql->f('userid') . "',qishu=0");
                $tmp['jj' . $msql->f('userid')] = 1;
            }
            /***********HELLO*******/
            $tz[$i]['qishu']  = $msql->f('qishu');
            $tz[$i]['je']     = (float) $msql->f('je');
            $tz[$i]['zcje']   = (float) pr2($msql->f('je') * $msql->f($zcstr) / 100);
            $tz[$i]['peilv1'] = (float) $msql->f('peilv1');
            
            $tz[$i]['points'] = (float) $msql->f('points');

            /*********************HELLO***************/
            if(in_array($uid,$poarr)){
                if ($msql->f('ab') == 'B' & $msql->f('points') >= 10) {
                    $tz[$i]['points'] -=10; 
                }
            } 
            /*********************HELLO***************/

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
            $tz[$i]['con']   = $msql->f('content');
            $tz[$i]['wf']    = wf($tmp['f' . $msql->f('gid')], $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            $tz[$i]['time']  = $msql->f('time');
            $tz[$i]['z']     = $msql->f('z');
            $tz[$i]['gname'] = $tmp['g' . $msql->f('gid')];
            $tz[$i]['xtime'] = substr($msql->f('time'),-8);
            $tz[$i]['user']  = transu($msql->f('userid'));
            
            $i++;
        }
        $e = array(
            "tz" => $tz,
            "page" => $pstr,
            "js" => $js,
            "zgid" => $zgid,
            "sql" => $sql
        );
        echo json_encode($e);
        unset($e);
        unset($tmp);
        break;

}
?>