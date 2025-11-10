<?php
include('../data/comm.inc.php');
include('../data/uservar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    
    case "getatt":
        $gid  = $_POST['gid'];
        $abcd = strtolower($_POST['abcd']);
        $ab   = $_POST['ab'];
        $ch   = array();
        if ($config['panstatus'] == 0) {
            echo json_encode($ch);
            exit;
        }
        $time = time();
        $msql->query("select layer,fid1,ifexe,pself from `$tb_user` where userid='$userid'");
        $msql->next_record();
        
        $thelayer = $msql->f('layer');
        $fid1     = $msql->f('fid1');
        $ifexe    = $msql->f('ifexe');
        $pself    = $msql->f('pself');
        if ($thelayer > 1) {
            $msql->query("select ifexe,pself from `$tb_user` where userid='$fid1'");
            $msql->next_record();
            $ifexe = $msql->f('ifexe');
            $pself = $msql->f('pself');
        }
        $msql->query("select * from `$tb_c` where gid='$gid' and $time-UNIX_TIMESTAMP(time)<=3 and (userid='99999999' or userid='$fid1')");
        $i = 0;
        while ($msql->next_record()) {
            $pid     = $msql->f('pid');
            $peilv1s = 0;
            if ($thelayer > 1 & $ifexe == 1) {
                $tsql->query("select peilv1 from `$tb_play_user` where userid='$fid1' and gid='$gid' and pid='$pid'");
                $tsql->next_record();
                $peilv1s = $tsql->f('peilv1');
            }
            
            $fsql->query("select cid,peilv1 from `$tb_play` where gid='$gid' and pid='$pid'");
            $fsql->next_record();
            $peilv1 = $fsql->f('peilv1');
            $cid    = $fsql->f('cid');
            
            $fsql->query("select ftype from `$tb_class` where gid='$gid' and   cid='$cid'");
            $fsql->next_record();
            $ftype = $fsql->f('ftype');
            $abcha = 0;
            if ($config['pan'][$ftype]['ab'] == 1 & $ab == 'B') {
                $abcha = $config['patt'][$ftype]['ab'];
            }
            $abcdcha = 0;
            if ($config['pan'][$ftype]['abcd'] == 1 & $abcd != 'A') {
                $abcdcha = $config['patt'][$ftype][strtolower($abcd)];
            }
            $ch[$i]['pid'] = $pid;
            if ($thelayer > 1 & $ifexe == 1 & $pself == 1) {
                $peilvcha         = getuserpeilvcha2($userid, $ftype);
                $ch[$i]['peilv1'] = $peilv1s + $abcha - $abcdcha - $peilvcha;
            } else if ($thelayer > 1 & $ifexe == 1) {
                $peilvcha         = getuserpeilvcha($userid, $ftype);
                $ch[$i]['peilv1'] = $peilv1 + $abcha - $abcdcha - $peilv1s - $peilvcha;
            } else {
                $peilvcha         = getuserpeilvcha($userid, $ftype);
                $ch[$i]['peilv1'] = $peilv1 + $abcha - $abcdcha - $peilvcha;
            }
            $i++;
        }
        echo json_encode($ch);
        unset($ch);
        break;
    
    case "make":
        if (transuser($userid, 'status') != 1) {
            die;
        }
        $ab   = strtoupper($_POST['ab']);
        $abcd = strtoupper($_POST['abcd']);
        $bid  = $_POST['bid'];
        //echo $_POST['pstr'];
        $play = str_replace('\\u', 'uuu', $_POST['pstr']);
        $play = str_replace('\\', '', $play);
        $play = str_replace('uuu', '\\u', $play);
        $play = json_decode($play, true);
        $ip = getip();
        $msql->query("insert into `$tb_log` set ip='$ip',userid='$userid',gid='$gid',time=NOW(),type='user',content='".json_encode($play,JSON_UNESCAPED_UNICODE)."'");
        //var_dump($play);exit;
        $cp   = count($play);
        if ($_SESSION['exe'] == 1 & (time() - $_SESSION['exetime']) < 30) {
            foreach ($play as $key => $val) {
                $play[$key]['err'] = "系统忙,请重试!";
            }
            echo json_encode($play);
            exit;
        }
        if ((time() - $_SESSION['exetime']) < $config['tzjg']) {
            foreach ($play as $key => $val) {
                $play[$key]['err'] = "系统忙!";
            }
            echo json_encode($play);
            exit;
        }
        $_SESSION['exe']     = 1;
        $_SESSION['exetime'] = time();
        
        $je = 0;
        $msql->query("select kmoney,money,fudong,layer,fid1,ifexe,pself,status,pan,plwarn,sy,yingdeny,ftime from `$tb_user` where userid='$userid'");
        $msql->next_record();
        if($msql->f('fudong')==1 & time()-strtotime($msql->f('ftime'))>86400){
            foreach ($play as $key => $val) {
                $play[$key]['err'] = "系统清算中,请稍后投注!";
            }
            echo json_encode($play);
            unset($_SESSION['exe']);
            exit;
        }
        if ($msql->f('status') != 1) {
            foreach ($play as $key => $val) {
                $play[$key]['err'] = "您已被暂停投注!";
            }
            echo json_encode($play);
            unset($_SESSION['exe']);
            exit;
        }
        if ($msql->f("yingdeny") ==1) {
            foreach ($play as $key => $val) {
                $play[$key]['err'] = "赢利超限,请明日再投注!";
            }
            echo json_encode($play);
            unset($_SESSION['exe']);
            exit;
        }
        
        if ($ab !== 'A' & $ab !== 'B')
            $ab = 'A';
        $uabcd = json_decode($msql->f('pan'), true);
        if (!in_array($abcd, $uabcd))
            $abcd = $uabcd[0];
        $fudong = $msql->f('fudong');
        /*
        if ($fudong == 1 | $config['fast'] == 1) {
            $moneys = $msql->f('kmoney');
        } else {
            $moneys = $msql->f('money');
        }*/
        $moneys = $msql->f('kmoney');
        $plwarn   = $msql->f('plwarn');
        $thelayer = $msql->f('layer');
        $fid1     = $msql->f('fid1');
        $ifexe    = $msql->f('ifexe');
        $pself    = $msql->f('pself');
        if ($thelayer > 1) {
            $msql->query("select ifexe,pself from `$tb_user` where userid='$fid1'");
            $msql->next_record();
            $ifexe = $msql->f('ifexe');
            $pself = $msql->f('pself');
        }
        $je = 0;
        for ($i = 0; $i < $cp; $i++) {
            $je += $play[$i]['je'];
        }
        
        if ($je > $moneys) {
            foreach ($play as $key => $val) {
                $play[$key]['err'] = "余额不足!";
            }
            echo json_encode($play);
            unset($_SESSION['exe']);
            exit;
        }
        $u        = getfid($userid);
        $zc       = getzcnew($userid, $u, $thelayer, $gid, $config['zcmode']);
        $czc      = count($zc) - 1;
        $peilvcha = array();
        $jex      = 0;
        $fsql->query("delete from `$tb_libu` where  userid='$userid'");
        $msql->query("select closetime,opentime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
        $msql->next_record();
        $config['closetime'] = strtotime($msql->f('closetime'));
        if ((time() - strtotime($msql->f('opentime')) - $config['times']['o']) < 0 & $config['autoopenpan'] == 1) {
            $config['panstatus']   = 0;
            $config['otherstatus'] = 0;
        }
        $tid    = setuptid();
        $ip     = getip();
        if($ipa['i'.$userid]!=""){
            $ip = $ipa['i'.$userid];
        }
        $ytparr = array();
        
        
        if (date("His") <= str_replace(':', '', $config['editstart'])) {
            $dates = sqldate(time() - 86400);
        } else {
            $dates = sqldate(time());
        }
        
        for ($i = 0; $i < $cp; $i++) {
            if ($play[$i]['je'] <= 0 | $play[$i]['je'] % 1 != 0)
                continue;
            
            $tid++;
            if (in_array($play[$i]['pid'], $ytparr) & !is_array($play[$i]['con'])) {
                //$play[$i]['err']  = "重复投注!";
                //$play[$i]['cg']   = 0;
                //$play[$i]['goon'] = 0;
                //continue;
            }
            if ($play[$i]['sc'] == 1) {
                $play[$i]['err']  = "注单取消!";
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            $ytparr[] = $play[$i]['pid'];
            $msql->query("select bid,sid,cid,peilv1,peilv2,ifok,name,pl,yautocs,ystart,ztype from `$tb_play` where gid='$gid' and pid='" . $play[$i]['pid'] . "'");
            $msql->next_record();
            $bid     = $msql->f('bid');
            $sid     = $msql->f('sid');
            $cid     = $msql->f('cid');
            $pname   = $msql->f('name');
            $ifok    = $msql->f('ifok');
            $yautocs = $msql->f('yautocs');
            $ystart  = $msql->f('ystart');
            $pl      = $msql->f('pl');
            $peilv1  = 0;
            $peilv2  = 0;
            $peilv1s = 0;
            $peilv2s = 0;
            if ($pname == '過關' | $pname == '过关') {
                $play[$i]['bz'] = json_encode($play[$i]['arr']);
                $arr            = $play[$i]['arr'];
                if ($thelayer > 1 & $ifexe == 1 & $pself == 1) {
                    $tb   = $tb_play_user;
                    $uwhi = " and userid='$fid1' ";
                } else {
                    $tb   = $tb_play;
                    $uwhi = "";
                }
                $peilv1 = 1;
                foreach ($arr as $key => $val) {
                    
                    $sql = "select peilv1,ystart,yautocs from `$tb` where gid='$gid' $uwhi and sid='" . $val['sid'] . "' and cid='" . $val['cid'] . "'   and pid='" . $val['pid'] . "'";
                    
                    $fsql->query($sql);
                    $fsql->next_record();
                    $peilv1 *= ($fsql->f('peilv1') - $config['cs']['ggpeilv']);
                    $yautocs = $msql->f('yautocs');
                    $ystart  = $msql->f('ystart');
                }
            } else if (($config["fenlei"]!=103 && $msql->f('pl') != '') || ($config["fenlei"]==103 && $msql->f("ztype")==8)) {
                if ($config["fenlei"] == 100) {
                    $duo = getduoarr($pname);
                } else {
                    $duo = getduoarrssuser($config["fenlei"], $pname);
                }
                $pl = json_decode($pl, true);
                if ($thelayer > 1 & $ifexe == 1) {
                    $fsql->query("select pl,ystart,yautocs from `$tb_play_user` where  userid='$fid1' and gid='$gid' and pid='" . $play[$i]['pid'] . "' ");
                    $fsql->next_record();
                    $pls     = json_decode($fsql->f('pl'), true);
                    $yautocs = $msql->f('yautocs');
                    $ystart  = $msql->f('ystart');
                }
                
                if (strpos($pname, '字组合')) {
                    $pcl = count($play[$i]['con']);
                    
                    if ($pcl == 2) {
                        $pkey = 0;
                        if ($play[$i]['con'][0] != $play[$i]['con'][1]) {
                            $pkey = 1;
                        }
                    } else if ($pcl == 3) {
                        if ($play[$i]['con'][0] == $play[$i]['con'][1] & $play[$i]['con'][0] == $play[$i]['con'][2]) {
                            $pkey = 0;
                        } else if ($play[$i]['con'][0] != $play[$i]['con'][1] & $play[$i]['con'][0] != $play[$i]['con'][2] & $play[$i]['con'][1] != $play[$i]['con'][2]) {
                            $pkey = 2;
                        } else {
                            $pkey = 1;
                        }
                    } else {
                        exit;
                    }
                    $key    = rduokey($duo, $play[$i]['con'][0]);
                    $peilv1 = $pl[$pkey][$key];
                    foreach ($play[$i]['con'] as $val) {
                        $key = rduokey($duo, $val);
                        if ($pl[$pkey][$key] < $peilv1) {
                            $peilv1 = $pl[$pkey][$key];
                        }
                    }
                    if ($thelayer > 1 & $ifexe == 1) {
                        $key     = rduokey($duo, $play[$i]['con'][0]);
                        $peilv1s = $pls[$pkey][$key];
                        foreach ($play[$i]['con'] as $val) {
                            $key = rduokey($duo, $val);
                            if ($pls[$pkey][$key] < $peilv1s) {
                                $peilv1s = $pls[$pkey][$key];
                            }
                        }
                    }
                } else if (strpos($pname, '字定位') | $pname == '选前三直选' | $pname == '选三前直' | $pname == '选前二直选') {
                    if ($pname == '选前二直选' | $pname == '选前三直选') {
                        $keyfunc = "rduokeysyxw";
                    } else if ($pname == '选三前直') {
                        $keyfunc = "rduokeyklsf";
                    } else {
                        $keyfunc = "rduokeydw";
                    }
                    $key    = call_user_func($keyfunc, $duo, $play[$i]['con'][0], 0);
                    $peilv1 = $pl[0][$key];
                    foreach ($play[$i]['con'] as $keyc => $val) {
                        $key = call_user_func($keyfunc, $duo, $val, $keyc);
                        if ($pl[0][$key] < $peilv1) {
                            $peilv1 = $pl[0][$key];
                        }
                    }
                    if ($thelayer > 1 & $ifexe == 1) {
                        $key     = call_user_func($keyfunc, $duo, $play[$i]['con'][0], 0);
                        $peilv1s = $pls[0][$key];
                        foreach ($play[$i]['con'] as $keyc => $val) {
                            $key = call_user_func($keyfunc, $duo, $val, $keyc);
                            if ($pls[0][$key] < $peilv1s) {
                                $peilv1s = $pls[0][$key];
                            }
                        }
                    }
                } else {
                    $key    = rduokey($duo, $play[$i]['con'][0]);
                    $peilv1 = $pl[0][$key];
                    $peilv2 = $pl[1][$key];
                    foreach ($play[$i]['con'] as $val) {
                        $key = rduokey($duo, $val);
                        if ($pl[0][$key] < $peilv1) {
                            $peilv1 = $pl[0][$key];
                            $peilv2 = $pl[1][$key];
                        }
                    }
                    if ($thelayer > 1 & $ifexe == 1) {
                        $key     = rduokey($duo, $play[$i]['con'][0]);
                        $peilv1s = $pls[0][$key];
                        $peilv2s = $pls[1][$key];
                        foreach ($play[$i]['con'] as $val) {
                            $key = rduokey($duo, $val);
                            if ($pls[0][$key] < $peilv1s) {
                                $peilv1s = $pls[0][$key];
                                $peilv2s = $pls[1][$key];
                            }
                        }
                    }
                }
                $play[$i]['con'] = implode('-', $play[$i]['con']);
            } else {
                $peilv1 = $msql->f('peilv1');
                $peilv2 = $msql->f('peilv2');
                if ($thelayer > 1 & $ifexe == 1) {
                    $fsql->query("select peilv1,peilv2,ystart,yautocs from `$tb_play_user` where userid='$fid1' and gid='$gid' and  pid='" . $play[$i]['pid'] . "' ");
                    $fsql->next_record();
                    $peilv1s = $fsql->f('peilv1');
                    $peilv2s = $fsql->f('peilv2');
                    $yautocs = $msql->f('yautocs');
                    $ystart  = $msql->f('ystart');
                }
            }
            
            if ($config['panstatus'] == 0 | (($bid != 23378685 | !is_numeric($pname)) & $config['otherstatus'] == 0)) {
                $play[$i]['err']  = "已关盘";
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            $time = time();
            if ($bid == 23378685 & is_numeric($pname)) {
                if ($time > ($config['closetime'] - $config['userclosetime'] - $config['times']['c'])) {
                    $play[$i]['err']  = "已关盘I2";
                    $play[$i]['cg']   = 0;
                    $play[$i]['goon'] = 0;
                    continue;
                }
            } else {
                if ($time > ($config['closetime'] - $config['otherclosetime'] - $config['userclosetime'] - $config['times']['c'])) {
                    $play[$i]['err']  = "已关盘2";
                    $play[$i]['cg']   = 0;
                    $play[$i]['goon'] = 0;
                    continue;
                }
            }
            
            if ($ifok != 1) {
                $play[$i]['err']  = "已关盘3";
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            if ($tmpcid != $cid) {
                $fsql->query("select ftype,dftype from `$tb_class` where gid='$gid' and cid='$cid'");
                $fsql->next_record();
                $ftype  = $fsql->f('ftype');
                $dftype = $fsql->f('dftype');
                if ($ifexe == 1 & $pself == 1) {
                    $autopl = $fsql->arr("select * from `$tb_auto` where userid='$fid1' and gid='$gid' and class='$ftype'", 1);
                } else {
                    $autopl = $fsql->arr("select * from `$tb_auto` where userid='99999999' and gid='$gid' and class='$ftype'", 1);
                }
                $autopl  = $autopl[0];
                $abcha   = 0;
                $abcdcha = 0;
                $tmpabcd = 0;
                $tmpab   = 0;
                if ($config['pan'][$dftype]['ab'] == 1) {
                    if ($ab == 'B') {
                        $abcha = $config['patt'][$ftype]['ab'];
                    }
                    $tmpab = $ab;
                }
                if ($config['pan'][$dftype]['abcd'] == 1) {
                    if ($abcd != 'A') {
                        $abcdcha = $config['patt'][$ftype][strtolower($abcd)];
                    }
                    $tmpabcd = $abcd;
                }
                $points = getpoints8($dftype, $tmpabcd, $tmpab, $userid, $gid);
                /*******************HELLO**************/
                if ($ftype == 0 & $ab == 'B' & in_array($userid, $poarr)) {
                    $points += 10;
                }
                /******************HELLO***************/
                $sqle        = ",points='" . $points . "'";
                $tmppeilvcha = 0;
                for ($j = 0; $j < $czc; $j++) {
                    $sqle .= ",zc" . $j . "='" . $zc[$j]['zc'] . "'";
                    if ($j > 0) {
                        $arr = getzcs8($ftype, $u[$j], $gid);
                        $tmppeilvcha += $arr['peilvcha'];
                        $lowpeilv[$j] = $arr['lowpeilv'];
                        $peilvcha[$j] = $tmppeilvcha + $abcdcha - $abcha;
                        $points       = getpoints8($dftype, $tmpabcd, $tmpab, $u[$j], $gid);
                        /*******************HELLO**************/
                        if ($ftype == 0 & $ab == 'B' & in_array($userid, $poarr)) {
                            $points += 10;
                        }
                        /******************HELLO***************/
                        $sqle .= ",points" . $j . "='" . $points . "'";
                        $sqle .= ",uid" . $j . "='" . $u[$j] . "'";
                        if ($j == 1 & $ifexe == 1 & $pself == 1) {
                            $tmppeilvcha = 0;
                        }
                    }
                }
                $arr = getzcs8($ftype, $userid, $gid);
                $tmppeilvcha += $arr['peilvcha'];
                $peilvchax = $tmppeilvcha + $abcdcha - $abcha;
                $lowpeilvx = $arr['lowpeilv'];
                $arr       = getjes8($dftype, $userid, $gid);
                $cmaxjex = $arr['cmaxje'];
                $maxjex  = $arr['maxje'];
                $tmpcid = $cid;
                
            }
            if ($play[$i]['je'] > $maxjex) {
                $play[$i]['err']  = "超单注限额！";
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            $fsql->query("select sum(je) from `$tb_lib` where gid='$gid' and userid='$userid' and qishu='{$config['thisqishu']}' and pid='{$play[$i]['pid']}' ");
            $fsql->next_record();
            if ($fsql->f(0) + $play[$i]['je'] > $cmaxjex) {
                $play[$i]['err']  = "超单场限额！";
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
                continue;
            }
            
            $attpeilv = 0;
            if ($autopl['yj'] == 1) {
                $constr = '';
                if ($play[$i]['con'] != '') {
                    $constr = " and content='" . $play[$i]['con'] . "' ";
                }
                if ($ifexe == 1 & $pself == 1) {
                    if ($autopl['ifzc'] == 1) {
                        $fsql->query("select sum(je*zc1/100) from `$tb_lib` where gid='$gid' and qishu='" . $config['thisqishu'] . "'  and pid='" . $play[$i]['pid'] . "' and uid1='$fid1' $constr");
                    } else {
                        $fsql->query("select sum(je) from `$tb_lib` where gid='$gid' and qishu='" . $config['thisqishu'] . "'  and pid='" . $play[$i]['pid'] . "' and uid1='$fid1' $constr");
                    }
                } else {
                    if ($autopl['ifzc'] == 1) {
                        $fsql->query("select sum(je*zc0/100) from `$tb_lib` where gid='$gid' and qishu='" . $config['thisqishu'] . "'  and pid='" . $play[$i]['pid'] . "' $constr");
                    } else {
                        $fsql->query("select sum(je) from `$tb_lib` where gid='$gid' and qishu='" . $config['thisqishu'] . "'  and pid='" . $play[$i]['pid'] . "' $constr");
                    }
                }
                $fsql->next_record();
                $ytje = $fsql->f(0) + $play[$i]['je'];
                if ($ytje >= $autopl['startje']) {
                    $startje = $autopl['startje'];
                    $addje   = $autopl['addje'];
                    $stopje  = $autopl['stopje'];
                    $attcs   = ((($ytje - $startje) - (($ytje - $startje) % $addje)) / $addje);
                    
                    if ($play[$i]['con'] != '' | $ystart == 0) {
                        $attcs    = floor($attcs);
                        $attpeilv = $autopl['startpeilv'] + $autopl['attpeilv'] * $attcs;
                    } else {
                        $ucs      = floor($attcs - $yautocs);
                        $attpeilv = $autopl['attpeilv'] * $ucs;
                    }
                    if (($peilv1 - $attpeilv) < $autopl['lowpeilv']) {
                        $peilv1 = $autopl['lowpeilv'];
                    } else {
                        $peilv1 -= $attpeilv;
                    } 
                }
            }
            $play[$i]['cgs'] = 0;
            $tmppeilv        = 0;
            $tmppeilv2       = 0;


            if ($play[$i]['con'] == '') {
                if ($thelayer > 1 & $ifexe == 1 & $pself == 1) {
                    $tmppeilv = moren($peilv1s - $peilvchax, $lowpeilvx);
                } else if ($thelayer > 1 & $ifexe == 1) {
                    $tmppeilv = moren($peilv1 - $peilvchax - $peilv1s, $lowpeilvx);
                } else {
                    $tmppeilv = moren($peilv1 - $peilvchax, $lowpeilvx);
                }
                if (p3($tmppeilv) < p3($play[$i]['peilv1'])) {
                    $play[$i]['cgs'] = 1; //赔率改变
                }
            } else if (is_array($pl)) {
                if ($thelayer > 1 & $ifexe == 1 & $pself == 1) {
                    $tmppeilv  = moren($peilv1s - $peilvchax, $lowpeilvx);
                    $tmppeilv2 = moren($peilv2s - $peilvchax, $lowpeilvx);
                } else if ($thelayer > 1 & $ifexe == 1) {
                    $tmppeilv  = moren($peilv1 - $peilvchax - $peilv1s, $lowpeilvx);
                    $tmppeilv2 = moren($peilv2 - $peilvchax - $peilv2s, $lowpeilvx);
                } else {
                    $tmppeilv  = moren($peilv1 - $peilvchax, $lowpeilvx);
                    $tmppeilv2 = moren($peilv2 - $peilvchax, $lowpeilvx);
                }
            } else {
                $tmppeilv = $peilv1;
            }
            $play[$i]['peilv1'] = $tmppeilv;
            if ($cp > 3) {
                $sql = " insert into `$tb_libu` ";
            } else {
                $sql = " insert into `$tb_lib` ";
            }
            $key = '';
            if ($config['libkey'] == 1) {
                $key = encode(array(
                    $gid,
                    $play[$i]['pid'],
                    $tid,
                    $userid,
                    time(),
                    $play[$i]['con']
                ));
            }
            $sql .= " set dates='$dates',gid='$gid',qishu='" . $config['thisqishu'] . "',tid='$tid',userid='$userid',bid='$bid',sid='$sid',cid='$cid',pid='" . $play[$i]['pid'] . "',abcd='$abcd',ab='$ab',content='" . $play[$i]['con'] . "',time=NOW(),je='" . $play[$i]['je'] . "',xtype='0',z='9',bs=1,peilv1='" . $tmppeilv . "',peilv2='" . $tmppeilv2 . "',sv='" . $_SESSION['sv'] . "',ip='$ip',code='$key'";
            $sql .= $sqle;
            $zxstr=[];
            $pei=[];
            if($pname=='三中二' || $pname=='二中特'){
                $pei[0][0] = $tmppeilv;
                $pei[0][1] = $tmppeilv2;
            } 

            if($bid=='26000004') $zxstr[] = $tmppeilv;
            for ($j = 1; $j < $czc; $j++) {
                if ($pname == '過關' | $pname == '过关') {
                    $sql .= ",peilv1" . $j . "='" . $peilv1 . "',peilv2" . $j . "='0'";
                } else if (is_array($pl)) {
                    if ($ifexe == 1 & $pself == 1 & $j > 1) {
                        $sql .= ",peilv1" . $j . "='" . moren($peilv1s - $peilvcha[$j], $lowpeilv[$j]) . "',peilv2" . $j . "='" . moren($peilv2s - $peilvcha[$j], $lowpeilv[$j]) . "'";
                        if($pname=='三中二' || $pname=='二中特'){
                            $pei[$j][0] = moren($peilv1s - $peilvcha[$j], $lowpeilv[$j]);
                            $pei[$j][1] = moren($peilv2s - $peilvcha[$j], $lowpeilv[$j]);
                        }
                    } else {
                        if ($ifexe == 1 & $j > 1) {
                            $sql .= ",peilv1" . $j . "='" . moren($peilv1 - $peilvcha[$j] - $peilv1s, $lowpeilv[$j]) . "',peilv2" . $j . "='" . moren($peilv2 - $peilvcha[$j] - $peilv2s, $lowpeilv[$j]) . "'";
                            if($pname=='三中二' || $pname=='二中特'){
                                $pei[$j][0] = moren($peilv1 - $peilvcha[$j] - $peilv1s, $lowpeilv[$j]);
                                $pei[$j][1] = moren($peilv2 - $peilvcha[$j] - $peilv2s, $lowpeilv[$j]);
                            }
                        } else {
                            $sql .= ",peilv1" . $j . "='" . moren($peilv1 - $peilvcha[$j], $lowpeilv[$j]) . "',peilv2" . $j . "='" . moren($peilv2 - $peilvcha[$j], $lowpeilv[$j]) . "'";
                            if($pname=='三中二' || $pname=='二中特'){
                                $pei[$j][0] = moren($peilv1 - $peilvcha[$j], $lowpeilv[$j]);
                                $pei[$j][1] = moren($peilv2 - $peilvcha[$j], $lowpeilv[$j]);
                            }
                        }
                    }
                } else {
                    if ($ifexe == 1 & $pself == 1 & $j > 1) {
                        $sql .= ",peilv1" . $j . "='" . moren($peilv1s - $peilvcha[$j] , $lowpeilv[$j]) . "',peilv2" . $j . "='0'";
                        if($bid=='26000004') $zxstr[] = moren($peilv1s - $peilvcha[$j] , $lowpeilv[$j]);
                    } else {
                        if ($ifexe == 1 & $j > 1) {
                            $sql .= ",peilv1" . $j . "='" . moren($peilv1 - $peilvcha[$j] - $peilv1s, $lowpeilv[$j]) . "',peilv2" . $j . "='0'";
                            if($bid=='26000004') $zxstr[] = moren($peilv1 - $peilvcha[$j] - $peilv1s, $lowpeilv[$j]);
                        } else {
                            $sql .= ",peilv1" . $j . "='" . moren($peilv1 - $peilvcha[$j] , $lowpeilv[$j]) . "',peilv2" . $j . "='0'";
                            if($bid=='26000004') $zxstr[] = moren($peilv1 - $peilvcha[$j] , $lowpeilv[$j]);
                        }
                    }
                }
            }
            if($bid=='26000004'){
                $sql .= ",bz='".json_encode($zxstr)."'";
            } else if($pname=='三中二' || $pname=='二中特'){
                $sql .= ",bz='".json_encode($pei)."'";
            } else{
                $sql .= ",bz='" . $play[$i]['bz'] . "'";
            } 
            
            if ($msql->query($sql)) {
                $play[$i]['cg'] = 1;
                $jex += $play[$i]['je'];
                $play[$i]['goon'] = 0;
            } else {
                $play[$i]['cg']   = 0;
                $play[$i]['goon'] = 0;
            }
			$play[$i]['time'] = date("H:i:s");
        }
        if ($cp > 3) {
            $msql->query("insert into `$tb_lib` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `$tb_libu` where userid='$userid' order by id");
            $msql->query("delete from x_libu where userid='$userid'");
        }
        //if ($fudong == 1 | $config['fast'] == 1) {
            $msql->query("update `$tb_user` set kmoney=kmoney-$jex where userid='$userid'");
            usermoneylog($userid, 0 - $jex, $moneys - $jex, '投注',1,$ip);
        /*
        } else {
            $msql->query("update `$tb_user` set money=money-$jex where userid='$userid'");
            usermoneylog($userid, 0 - $jex, $moneys - $jex, '投注', 0,$ip);
        }*/
        echo json_encode($play);
        unset($play);
        unset($_SESSION['exe']);
        break;
    case "getlast15":
        if ($config['fast'] == 0) {
            $msql->query("select qishu,je,peilv1,peilv2,points,content,bid,sid,cid,pid,time,tid,gid from `$tb_lib` where  " . $config['thisqishu'] . "=qishu and userid='$userid' and gid='$gid' order by time desc,id desc limit 10");
        } else {
            //$msql->query("select qishu,je,peilv1,peilv2,points,content,bid,sid,cid,pid,time,tid from `$tb_lib` where  " . $config['thisqishu'] . "-qishu<=2 and userid='$userid' and gid='$gid' order by time desc,id desc limit 10");
            $msql->query("select qishu,je,peilv1,peilv2,points,content,bid,sid,cid,pid,time,tid,gid from `$tb_lib` where  " . $config['thisqishu'] . "=qishu and userid='$userid' and gid='$gid' order by time desc,id desc limit 10");
        }
        $i   = 0;
        $lib = array();
        $tmp = array();
        while ($msql->next_record()) {
            $lib[$i]['tid']    = $msql->f('tid');
            $lib[$i]['time']   = substr($msql->f('time'), -8);
            $lib[$i]['peilv1'] = (float) $msql->f('peilv1');
            $lib[$i]['peilv2'] = (float) $msql->f('peilv2');
            $lib[$i]['je']     = (float) $msql->f('je');
            $lib[$i]['qishu']  = substr($msql->f('qishu'), -3);     
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

            
            $lib[$i]['name'] = wfuser($config['fenlei'],$tmp['b' . $msql->f('gid') . $msql->f('bid')],$tmp['s' . $msql->f('gid') . $msql->f('sid')],$tmp['c' . $msql->f('gid') . $msql->f('cid')],$tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            if ($msql->f('content') != '') {
                $lib[$i]['name'] .= ":" . $msql->f('content');
            }
            $i++;
        }
        echo json_encode($lib);
        unset($lib);
        break;
    
    case "getopen":
        $arr    = array();
        $arr[0] = $config['panstatus'];
        $arr[1] = $config['otherstatus'];
        $time   = time();
        $msql->query("select closetime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
        $msql->next_record();
        if ($config['panstatus'] == 1) {
            $pantime = $msql->f('closetime') - $time - $config['userclosetime'];
            if ($pantime <= 0)
                $arr[0] = 0;
        }
        if ($config['otherstatus'] == 1) {
            $othertime = $msql->f('closetime') - $time - $config['userclosetime'] - $config['otherclosetime'];
            if ($othertime <= 0)
                $arr[1] = 0;
        }
        echo json_encode($arr);
        unset($arr);
        break;
    case "getb":
        $gid = $_POST['gid'];
        $msql->query("select * from `$tb_bclass` where gid='$gid'  and ifok=1 order by xsort");
        $b = array();
        $i = 0;
        while ($msql->next_record()) {
            $b[$i]['bid']   = $msql->f('bid');
            $b[$i]['name']  = $msql->f('name');
            $b[$i]['ifok']  = $msql->f('ifok');
            $b[$i]['xsort'] = $msql->f('xsort');
            $i++;
        }
        echo json_encode($b);
        break;
    case "gets":
        if ($_POST['gid'] != '') {
            $gid = $_POST['gid'];
        }
        $bid = $_POST['bid'];
        $msql->query("select * from `$tb_sclass` where gid='$gid' and bid='$bid'  order by bid,xsort");
        $s = array();
        $i = 0;
        while ($msql->next_record()) {
            $s[$i]['sid']   = $msql->f('sid');
            $s[$i]['name']  = $msql->f('name');
            $s[$i]['ifok']  = $msql->f('ifok');
            $s[$i]['xsort'] = $msql->f('xsort');
            $i++;
        }
        echo json_encode($s);
        break;
    case "getc":
        if ($_POST['gid'] != '') {
            $gid = $_POST['gid'];
        }
        $bid = $_POST['bid'];
        $sid = $_POST['sid'];
        $msql->query("select * from `$tb_class`  where gid='$gid' and bid='$bid' and sid='$sid' order by bid,sid,xsort ");
        $i = 0;
        $c = array();
        while ($msql->next_record()) {
            $c[$i]['cid']  = $msql->f('cid');
            $c[$i]['name'] = $msql->f('name');
            $i++;
        }
        echo json_encode($c);
        unset($c);
        break;
    case "setgame":
        $gid = $_REQUEST['gid'];
        if (in_array($gid, $garr)) {
            $_SESSION['gid'] = $gid;
            echo 1;
        }
        break;
}
function low1($v)
{
    if ($v < 1)
        return 1;
}
?>