<?php

function getadminid() {
    global $tsql, $tb_admins;
    if ($_SESSION['hide'] == 1) return 10000;
    $tsql->query("select adminid from `$tb_admins` where adminname='" . $_SESSION['username'] . "'");
    $tsql->next_record();
    return $tsql->f('adminid');
}
function transadmin($id, $cols) {
    if ($cols == 'name' | $cols == 'usertype') return "管理员";
    global $tsql, $tb_admins;
    $tsql->query("select adminname from `$tb_admins` where adminid='$id'");
    $tsql->next_record();
    return $tsql->f('adminname');
}
function getuserid() {
    return 99999999;
    global $tsql, $tb_user, $tb_admins;
    $tsql->query("select userid,fid,ifson from `$tb_user` where username='" . $_SESSION['username'] . "'");
    $tsql->next_record();
    if ($tsql->f('userid') != '' & $tsql->f('ifson') == 0) return $tsql->f('userid');
    if ($tsql->f('userid') != '' & $tsql->f('ifson') == 1) return $tsql->f('fid');
    $tsql->query("select adminid from `$tb_admins` where adminname='" . $_SESSION['username'] . "'");
    $tsql->next_record();
    if ($tsql->f('adminid') != '') return 99999999;
}
function sessiondel() {
    unset($_SESSION['uid']);
    unset($_SESSION['passcode']);
    unset($_SESSION['admin']);
    unset($_SESSION['check']);
    unset($_SESSION['hide']);
    unset($_SESSION['wid']);
    unset($_SESSION['gid']);
    unset($_SESSION['sv']);
    //session_destroy();
    
}
function paddqishu($gid, $pdate = '') {
    if (transgame($gid, 'fast') == 0) exit;
    global $tsql, $psql, $tb_kj_m, $tb_kj, $tb_game, $tb_config;
    $tsql->query("select kjip,editstart,editend from `$tb_config`");
    $tsql->next_record();
    $kjip = $tsql->f('kjip');
    $editstart = str_replace(':', '', $tsql->f('editstart'));
    $editend = str_replace(':', '', $tsql->f('editend'));
    $tsql->query("select cs,thisbml from `$tb_game` where gid='$gid'");
    $tsql->next_record();
    $cs = json_decode($tsql->f('cs') , true);
    $bml = $tsql->f('thisbml');
    $time = time();
    if($pdate==''){
        if(date("His")<$editstart){
            $starttime = strtotime(date("Y-m-d",time()-86400) . ' ' . $cs['starttime']);
        }else{
            $starttime = strtotime(date("Y-m-d") . ' ' . $cs['starttime']);
        } 
    }else{
        $starttime = strtotime($pdate . ' ' . $cs['starttime']);
    }
    $his = date("His");
    if ($gid == 1750) {
        if ($his <= 100) {
            $qishu = $cs['qishunum'] * (strtotime(date('Y-m-d', time() - 86400)) - strtotime($cs['startdate'])) / 3600 / 24 + $cs['startqs'] - $cs['tzqs'];
            $starttime = date('Y-m-d', time() - 86400) . " " . $cs['starttime'];
            $dates = date('Y-m-d', time() - 86400);
        } else {
            $qishu = $cs['qishunum'] * (strtotime(date('Y-m-d')) - strtotime($cs['startdate'])) / 3600 / 24 + $cs['startqs'] - $cs['tzqs'];
            $starttime = date('Y-m-d') . " " . $cs['starttime'];
            $dates = date('Y-m-d');
        }
        $starttime = strtotime($starttime);
        for ($i = 1; $i <= $cs['qsnums']; $i++) {
            $qishu++;
            $opentime = $starttime;
            $kjtime = $opentime + $cs['qsjg'] * 60;
            $closetime = $kjtime - $cs['closetime'];
            $his = date("His", $opentime);
            $psql->query("select 1 from `$tb_kj` where gid='$gid' and qishu='$qishu'");
            $psql->next_record();
            if ($psql->f(0) != 1) {
                $sql = "insert into `$tb_kj` set kjtime='" . sqltime($kjtime) . "',opentime='" . sqltime($opentime) . "',closetime='" . sqltime($closetime) . "',qishu='$qishu',dates='$dates',bml='$bml',gid='$gid',baostatus=1";
                $psql->query($sql);
            }
            $starttime = $kjtime;
        }
    }else if ($gid == 200 || $gid == 108 || $gid == 162 || $gid == 109 || $gid == 173 || $gid == 112 || $gid == 110 || $gid == 170 || $gid == 175 || $gid == 172 || $gid == 131 || $gid == 174 || $gid == 179  || $gid == 119 || $gid == 114  || $gid == 211 || $gid == 212 || $gid == 213 || $gid == 215 || $gid == 221 || $gid == 222 || $gid == 223 || $gid == 225 || $gid == 231 || $gid == 232 || $gid == 233 || $gid == 235 || $gid == 227 || $gid == 158 || $gid==214 || $gid==215 || $gid==224 || $gid==234 || $gid==154 || $gid==253 || $gid==311 || $gid==312 || $gid==313 || $gid==314 || $gid==315 || $gid==316 || $gid==321 || $gid==322 || $gid==323 || $gid==324 || $gid==325 || $gid==326 || $gid==331 || $gid==332 || $gid==333 || $gid==334 || $gid==335 || $gid==336 || $gid==341 || $gid==342 || $gid==343 || $gid==344 || $gid==345 || $gid==346 || $gid==351 || $gid==352 || $gid==353 || $gid==354 || $gid==355 || $gid==356  || $gid==251  || $gid==252  || $gid==254 || $gid==500 || $cs['qstype']==1) {
        

        if($pdate!=""){
            
            $qishu = $cs['qishunum'] * (strtotime($pdate) - strtotime($cs['startdate'])) / 3600 / 24 + $cs['startqs'] - $cs['tzqs'];
            $starttime = $pdate . " " . $cs['starttime'];
            $dates = $pdate;
        
        }else{
            if ($his <= 60000) {
            $qishu = $cs['qishunum'] * (strtotime(date('Y-m-d', time() - 86400)) - strtotime($cs['startdate'])) / 3600 / 24 + $cs['startqs'] - $cs['tzqs'];
            $starttime = date('Y-m-d', time() - 86400) . " " . $cs['starttime'];
            $dates = date('Y-m-d', time() - 86400);
        } else {
            $qishu = $cs['qishunum'] * (strtotime(date('Y-m-d')) - strtotime($cs['startdate'])) / 3600 / 24 + $cs['startqs'] - $cs['tzqs'];
            $starttime = date('Y-m-d') . " " . $cs['starttime'];
            $dates = date('Y-m-d');
        }
        }
        $starttime = strtotime($starttime);
        for ($i = 1; $i <= $cs['qsnums']; $i++) {
            $qishu++;
            $opentime = $starttime;
            $kjtime = $opentime + $cs['qsjg'] * 60;
            $closetime = $kjtime - $cs['closetime'];
            $his = date("His", $opentime);
            $psql->query("select 1 from `$tb_kj` where gid='$gid' and qishu='$qishu'");
            $psql->next_record();
            if ($psql->f(0) != 1) {
                $sql = "insert into `$tb_kj` set kjtime='" . sqltime($kjtime) . "',opentime='" . sqltime($opentime) . "',closetime='" . sqltime($closetime) . "',qishu='$qishu',dates='$dates',bml='$bml',gid='$gid',baostatus=1";
                $psql->query($sql);
            }
            $starttime = $kjtime;
        }
    }else if ($gid == 162 | ($gid == 163 & ($his > 235800 | $his < 60000))) {
        return;
        $url = "http://" . $kjip . "/ssc/kj.php?enter=kj&gid=" . $gid;
        if ($gid == 163) {
            $url = "http://" . $kjip . "/ssc/kj.php?enter=kj&gid=162";
            $cs['qsjg'] = 4;
            $cs['closetime'] = 30;
        }
        $kj = file_get_contents($url);
        $kj = json_decode($kj, true);
        $starttime = $kj['kjtime'] - $kj['kjtime'] % 60;
        $fen = date("i", $starttime);
        if ($fen % 4 != 0) {
            $starttime-= ($fen % 4) * 60;
        }
        $qishu = $kj['qishu'];
        $his = date("His", $starttime);
        if ($his >= $editstart) //& $his < 215600
        return;
        while (1) {
            $qishu++;
            $opentime = $starttime;
            $kjtime = $opentime + $cs['qsjg'] * 60;
            $closetime = $kjtime - $cs['closetime'];
            $his = date("His", $opentime);
            if ($his >= 235700 | $his <= $editstart) {
                $psql->query("select 1 from `$tb_kj` where gid='$gid' and qishu='$qishu'");
                $psql->next_record();
                if ($psql->f(0) != 1) {
                    if (date("His", $closetime) <= $editstart) {
                        $dates = sqldate($closetime - 86400);
                    } else {
                        $dates = sqldate($closetime);
                    }
                    $sql = "insert into `$tb_kj` set kjtime='" . sqltime($kjtime + $cs['tuichi']) . "',opentime='" . sqltime($opentime + $cs['tuichikp']) . "',closetime='" . sqltime($closetime) . "',qishu='$qishu',dates='$dates',bml='$bml',gid='$gid',baostatus=1";
                    $psql->query($sql);
                }
            }
            $starttime = $kjtime;
            if (date("his", $starttime) == $editstart) {
                break;
            }
        }
    } else if ($gid == 107 | $gid == 161 | $gid == 163 | $gid == 153) {
        $qishu = $cs['qishunum'] * (strtotime(date('Y-m-d')) - strtotime($cs['startdate'])) / 3600 / 24 + $cs['startqs'] - $cs['tzqs'];
        $starttime = date('Y-m-d') . " " . $cs['starttime'];
        $dates = date('Y-m-d');
        //$qishu+= $cs['tzqs'];
        $starttime = strtotime($starttime);
        for ($i = 1; $i <= $cs['qsnums']; $i++) {
            $qishu++;
            if($gid==107 && $i==22){
                $starttime = strtotime(date("Y-m-d ".$cs['starttime2']));
            }
            $opentime = $starttime;
            $kjtime = $opentime + $cs['qsjg'] * 60;
            $closetime = $kjtime - $cs['closetime'];
            $his = date("His", $opentime);
            $psql->query("select 1 from `$tb_kj` where gid='$gid' and qishu='$qishu'");
            $psql->next_record();
            if ($psql->f(0) != 1) {
                $sql = "insert into `$tb_kj` set kjtime='" . sqltime($kjtime + $cs['tuichi']) . "',opentime='" . sqltime($opentime + $cs['tuichikp']) . "',closetime='" . sqltime($closetime) . "',qishu='$qishu',dates='" . sqldate($closetime) . "',bml='$bml',gid='$gid',baostatus=1";
                $psql->query($sql);
            }
            $starttime = $kjtime;
        }
    } else if ($gid == 111 | $gid == 115 | $gid == 133) {
        $url = "http://" . $kjip . "/ssc/kj.php?enter=kj&gid=" . $gid;
        $kj = file_get_contents($url);
        $kj = json_decode($kj, true);
        $qishu = $kj['nextqishu'];
        $time = time();
        if (substr($qishu, -3) == 1) {
            if (date("His") > 220000) {
                $kjtime = strtotime(date("Y-m-d", time() + 86400) . ' ' . $cs['starttime']);
            } else {
                $kjtime = strtotime(date("Y-m-d", time()) . ' ' . $cs['starttime']);
            }
            $kjtime+= $cs['qsjg'] * 60;
            $opentime = $kjtime - $cs['qsjg'] * 60;
            $closetime = $kjtime - $cs['closetime'];
        } else {
            $kjtime = $kj['kjtime'] + $cs['qsjg'] * 60;
            $opentime = $kjtime - $cs['qsjg'] * 60;
            $closetime = $kjtime - $cs['closetime'];
        }
        $psql->query("select 1 from `$tb_kj` where gid='$gid' and qishu='$qishu'");
        $psql->next_record();
        if ($psql->f(0) != 1) {
            $sql = "insert into `$tb_kj` set kjtime='" . sqltime($kjtime + $cs['tuichi']) . "',opentime='" . sqltime($opentime + $cs['tuichikp']) . "',closetime='" . sqltime($closetime) . "',qishu='$qishu',dates='" . sqldate($closetime) . "',bml='$bml',gid='$gid',baostatus=1";
            $psql->query($sql);
        }
    } else {
        $kjtime = $starttime;
        for ($i = 1; $i <= $cs['qsnums']; $i++) {
            if ($gid == 101) {
                $k = $i + 9;
            } else if ($gid == 135) {
                $k = $i + 9;
            } else if ($gid == 229) {
                $k = $i + 23;
            } else if ($gid == 177) {
                $k = $i + abs($cs["tzqs"]);
            } else {
                $k = $i;
            }
            if ($gid == 229 & $k > 120) {
                $k-= 120;
            }
            if ($gid == 101 & $k > 59) {
                $k-= 59;
            }
            if ($gid == 135 & $k > 59) {
                $k-= 59;
            }
            if ($gid == 177 & $k > 288) {
                $k-= 288;
            }

            if ($k < 10) {
                $j = '00' . $k;
            } else if ($k < 100) {
                $j = '0' . $k;
            } else {
                $j = $k;
            }
            if ($gid == 229 & ($j <= 23 | $j >= 97)) {
                $cs['qsjg'] = $cs['qsjg2'];
                
            }
            $opentime = $kjtime;
            if ($gid == 101 && $k == 1) {
                $opentime = $opentime + $cs['qsjg2'] * 60;
            }
            if ($gid == 135 && $k == 1) {
                $opentime = $opentime + $cs['qsjg2'] * 60;
            }
            $kjtime = $opentime + $cs['qsjg'] * 60;
            $closetime = $kjtime - $cs['closetime'];
            if ($gid == 101 || $gid == 229) {
                $qishu = date("Ymd", $opentime) . $j;
            } else if ($gid == 135) {
                $qishu = date("Ymd", $kjtime) . $j;
            }else if ($gid == 177) {
                $qishu = date("Ymd", $opentime) . $j;
            } else {
                $qishu = date("Ymd", $starttime) . $j;
            }
            $psql->query("select 1 from `$tb_kj` where gid='$gid' and qishu='$qishu'");
            $psql->next_record();
            if ($psql->f(0) != 1) {
                if (date("His", $closetime) <= $editstart) {
                    $dates = sqldate($closetime - 86400);
                } else {
                    $dates = sqldate($closetime);
                }
                $sql = "insert into `$tb_kj` set kjtime='" . sqltime($kjtime + $cs['tuichi']) . "',opentime='" . sqltime($opentime + $cs['tuichikp']) . "',closetime='" . sqltime($closetime) . "',qishu='$qishu',dates='$dates',bml='$bml',gid='$gid',baostatus=1";
                $psql->query($sql);
            }
        }
    }
}

function jiaozhengedu($qz=false) {
    global $tsql, $psql, $tb_user, $tb_lib, $tb_config, $tb_game;

    // 开启事务保护：确保批量派奖的原子性
    $tsql->query("START TRANSACTION");
    $transaction_success = true;

    try {
        $rs = $tsql->query("select editstart,reseted,editend from `$tb_config`");
        $tsql->next_record();
    $sdate = week();
    if ($tsql->f('reseted') == 'week') {
        $start = $sdate[5] . ' ' . $tsql->f('editend');
    } else {
        $his = date("His");
        if ($his <= 60030) {
            $start = date("Y-m-d", time() - 86400) . ' ' . $tsql->f('editend');
        } else {
            $start = $sdate[10] . ' ' . $tsql->f('editend');
        }
    }
    $fstart = $sdate[10] . ' ' . $tsql->f('editend');
    $end = sqltime(time());
    $us = $tsql->arr("select userid,maxmoney,kmaxmoney,money,kmoney,sy,jetotal,jzkmoney from `$tb_user` where ifagent=0 and ifson=0 and fudong=0", 1);
    $cu = count($us);
    //$g0 = " gid in(select gid from `$tb_game` where ifopen=1 and fast=0) ";
    //$g1 = " gid in(select gid from `$tb_game` where ifopen=1 and fast=1) ";
    for ($i = 0; $i < $cu; $i++) {
        $uid = $us[$i]['userid'];

        $wh = " userid='$uid' and time>='$start' and time<='$end' ";
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z!=9", 0);
        $jetotals = pr4($rs[0][0]);
        $rs = $tsql->arr("select sum(je),sum(je*points/100) from `$tb_lib` where $wh and z!=9 and z!=2 and z!=7", 0);
        $yjs = pr4($rs[0][0]);
        $points = pr4($rs[0][1]);

        // 修复：直接使用实际派奖金额 prize，而不是 (理论派奖 - 实际派奖)
        // 原错误公式：$yizhong = sum(je*peilv1) - sum(prize) = 被扣除的金额
        // 正确公式：$yizhong = sum(prize) = 实际派奖金额
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=1 ", 0);
        $yizhong = pr4($rs[0][0]);

        // z=3 (半中) 的派奖：检查是否有 prize 字段，如果没有则使用 je*peilv2
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=3 ", 0);
        if ($rs[0][0] > 0) {
            $yizhong+= pr4($rs[0][0]);  // 使用实际派奖
        } else {
            // 如果 z=3 的订单没有设置 prize，则使用理论派奖
            $rs = $tsql->arr("select sum(je*peilv2) from `$tb_lib` where $wh and z=3 ", 0);
            $yizhong+= pr4($rs[0][0]);
        }
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z=9 ", 0);
        $wjs = pr4($rs[0][0]);
        $mon = $us[$i]['kmaxmoney'] - $yjs - $wjs + $yizhong + $points - $us[$i]['jzkmoney'];
        $sy = $yizhong + $points - $yjs;
        if ($jetotals != $us[$i]['jetotal'] || $qz) {
            // 使用乐观锁更新余额，检查更新结果
            $result = $tsql->query("update `$tb_user` set kmoney='$mon',sy='$sy',jetotal='$jetotals' where userid='$uid' and kmoney=" . $us[$i]['kmoney'] . "");

            // 检查是否更新成功（乐观锁可能失败）
            if (!$result) {
                throw new Exception("用户 $uid 余额更新失败（数据库错误）");
            }

            // 检查是否有行被更新（乐观锁条件不满足时 affected_rows = 0）
            // 如果 kmoney 已被其他线程修改，WHERE 条件不满足，affected_rows = 0
            if ($tsql->affected_rows() == 0) {
                throw new Exception("用户 $uid 余额更新失败（并发冲突，乐观锁失败）");
            }

            usermoneylog($uid, pr4($mon - $us[$i]['kmoney']) , $mon, '结算后较正',1,'127.0.0.1');
        }
    }
    $us = $tsql->arr("select userid,kmaxmoney,kmoney,ftime,wid,jetotal,jzkmoney from `$tb_user` where fudong=1", 1);
    $cu = count($us);
    for ($i = 0; $i < $cu; $i++) {
        $uid = $us[$i]['userid'];
        $ftime = $us[$i]['ftime'];
        $wh = "  userid='$uid' and time>'$ftime' ";
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z!=9", 0);
        $jetotals = pr4($rs[0][0]);
        $rs = $tsql->arr("select sum(je),sum(je*points/100) from `$tb_lib` where $wh and z!=9 and z!=2 and z!=7", 0);
        $yjs = pr4($rs[0][0]);
        $points = pr4($rs[0][1]);

        // 修复：直接使用实际派奖金额 prize，而不是 (理论派奖 - 实际派奖)
        // 原错误公式：$yizhong = sum(je*peilv1) - sum(prize) = 被扣除的金额
        // 正确公式：$yizhong = sum(prize) = 实际派奖金额
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=1 ", 0);
        $yizhong = pr4($rs[0][0]);

        // z=3 (半中) 的派奖：检查是否有 prize 字段，如果没有则使用 je*peilv2
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=3 ", 0);
        if ($rs[0][0] > 0) {
            $yizhong+= pr4($rs[0][0]);  // 使用实际派奖
        } else {
            // 如果 z=3 的订单没有设置 prize，则使用理论派奖
            $rs = $tsql->arr("select sum(je*peilv2) from `$tb_lib` where $wh and z=3 ", 0);
            $yizhong+= pr4($rs[0][0]);
        }
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z=9 ", 0);
        $wjs = pr4($rs[0][0]);
        $mon = $us[$i]['kmaxmoney'] - $yjs - $wjs + $yizhong + $points - $us[$i]['jzkmoney'];
        $sy = $yizhong + $points - $yjs;
        if ($jetotals != $us[$i]['jetotal'] || $qz) {
            // 使用乐观锁更新余额，检查更新结果
            $result = $tsql->query("update `$tb_user` set kmoney='$mon',sy='$sy',jetotal='$jetotals' where userid='$uid' and kmoney=" . $us[$i]['kmoney']);

            // 检查是否更新成功（乐观锁可能失败）
            if (!$result) {
                throw new Exception("用户 $uid 余额更新失败（数据库错误）");
            }

            // 检查是否有行被更新（乐观锁条件不满足时 affected_rows = 0）
            // 如果 kmoney 已被其他线程修改，WHERE 条件不满足，affected_rows = 0
            if ($tsql->affected_rows() == 0) {
                throw new Exception("用户 $uid 余额更新失败（并发冲突，乐观锁失败）");
            }

            usermoneylog($uid, pr4($mon - $us[$i]['kmoney']) , $mon, '结算后较正',1,'127.0.0.1');
        }
    }

    // ✅ 新增：结算完成后更新 ftime 为当前时间
    // 这样可以避免 fudong=1 的用户因为超过 24 小时而被禁止投注
    // 如果您的业务逻辑是结算后应该重置为 fudong=0，可以改为：
    // $tsql->query("UPDATE `$tb_user` SET fudong=0, ftime=NULL WHERE fudong=1");
    $tsql->query("UPDATE `$tb_user` SET ftime=NOW() WHERE fudong=1");

        // 提交事务
        $tsql->query("COMMIT");
        return 1;

    } catch (Exception $e) {
        // 回滚事务
        $tsql->query("ROLLBACK");
        error_log("jiaozhengedu 事务回滚: " . $e->getMessage());
        return 0;
    }
}



function jiaozhengeduedit($uids) {
    global $tsql, $psql, $tb_user, $tb_lib, $tb_config, $tb_game;

    // 开启事务保护：确保手动结算的原子性，防止执行到一半失败导致数据不一致
    $tsql->query("START TRANSACTION");

    try {
        $rs = $tsql->query("select editstart,reseted,editend from `$tb_config`");
        $tsql->next_record();
    $sdate = week();
    if ($tsql->f('reseted') == 'week') {
        $start = $sdate[5] . ' ' . $tsql->f('editend');
    } else {
        $his = date("His");
        if ($his <= str_replace(':', '', $tsql->f('editstart'))) {
            $start = date("Y-m-d", time() - 86400) . ' ' . $tsql->f('editend');
        } else {
            $start = $sdate[10] . ' ' . $tsql->f('editend');
        }
    }
    $fstart = $sdate[10] . ' ' . $tsql->f('editend');
    $end = sqltime(time());
    $us = $tsql->arr("select userid,maxmoney,kmaxmoney,money,kmoney,sy,jetotal,jzkmoney from `$tb_user` where userid='$uids' and ifagent=0 and ifson=0 and fudong=0", 1);
    $cu = count($us);
    //$g0 = " gid in(select gid from `$tb_game` where ifopen=1 and fast=0) ";
    //$g1 = " gid in(select gid from `$tb_game` where ifopen=1 and fast=1) ";
    for ($i = 0; $i < $cu; $i++) {
        $uid = $us[$i]['userid'];

        $wh = " userid='$uid' and time>='$start' and time<='$end' ";
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z!=9", 0);
        $jetotals = pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(je),sum(je*points/100) from `$tb_lib` where $wh and z!=9 and z!=2 and z!=7", 0);
        $yjs = pr0($rs[0][0]);
        $points = pr0($rs[0][1]);
        // 修复：直接使用实际派奖金额 prize，而不是 sum(je*peilv1)-sum(prize)
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=1 ", 0);
        $yizhong = pr0($rs[0][0]);
        // 修复：z=3 也使用实际派奖金额 prize，与 jiaozhengedu() 保持一致
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=3 ", 0);
        $yizhong+= pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z=9 ", 0);
        $wjs = pr0($rs[0][0]);
        $mon = $us[$i]['kmaxmoney'] - $yjs - $wjs + $yizhong + $points - $us[$i]['jzkmoney'];
        $sy = $yizhong + $points - $yjs;
        //if ($jetotals != $us[$i]['jetotal']) {
            // 使用乐观锁更新余额：WHERE条件包含kmoney=旧值，防止并发时覆盖其他管理员的修改
            $result = $tsql->query("update `$tb_user` set kmoney='$mon',sy='$sy',jetotal='$jetotals' where userid='$uid' and kmoney=" . $us[$i]['kmoney']);

            // 检查更新是否成功（数据库错误检测）
            if (!$result) {
                throw new Exception("用户 $uid 余额更新失败（数据库错误）");
            }

            // 检查是否有行被更新（乐观锁冲突检测）
            // 如果kmoney已被其他管理员修改，WHERE条件不满足，affected_rows=0
            if ($tsql->affected_rows() == 0) {
                throw new Exception("用户 $uid 余额更新失败（并发冲突或用户不存在）");
            }

            //usermoneylog($uid, pr0($mon - $us[$i]['kmoney']) , $mon, '结算后较正','127.0.0.1');
        //}
    }
    $us = $tsql->arr("select userid,kmaxmoney,kmoney,ftime,wid,jetotal,jzkmoney from `$tb_user` where userid='$uids' and fudong=1", 1);
    $cu = count($us);
    for ($i = 0; $i < $cu; $i++) {
        $uid = $us[$i]['userid'];
        $ftime = $us[$i]['ftime'];
        $wh = "  userid='$uid' and time>'$ftime' ";
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z!=9", 0);
        $jetotals = pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(je),sum(je*points/100) from `$tb_lib` where $wh and z!=9 and z!=2 and z!=7", 0);
        $yjs = pr0($rs[0][0]);
        $points = pr0($rs[0][1]);
        // 修复：直接使用实际派奖金额 prize，而不是 sum(je*peilv1)-sum(prize)
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=1 ", 0);
        $yizhong = pr0($rs[0][0]);
        // 修复：z=3 也使用实际派奖金额 prize，与 jiaozhengedu() 保持一致
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=3 ", 0);
        $yizhong+= pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z=9 ", 0);
        $wjs = pr0($rs[0][0]);
        $mon = $us[$i]['kmaxmoney'] - $yjs - $wjs + $yizhong + $points - $us[$i]['jzkmoney'];
        $sy = $yizhong + $points - $yjs;
        //if ($jetotals != $us[$i]['jetotal']) {
            // 使用乐观锁更新余额：WHERE条件包含kmoney=旧值，防止并发时覆盖其他管理员的修改
            $result = $tsql->query("update `$tb_user` set kmoney='$mon',sy='$sy',jetotal='$jetotals' where userid='$uid' and kmoney=" . $us[$i]['kmoney']);

            // 检查更新是否成功（数据库错误检测）
            if (!$result) {
                throw new Exception("用户 $uid 余额更新失败（数据库错误）");
            }

            // 检查是否有行被更新（乐观锁冲突检测）
            // 如果kmoney已被其他管理员修改，WHERE条件不满足，affected_rows=0
            if ($tsql->affected_rows() == 0) {
                throw new Exception("用户 $uid 余额更新失败（并发冲突或用户不存在）");
            }

            //usermoneylog($uid, pr0($mon - $us[$i]['kmoney']) , $mon, '结算后较正','127.0.0.1');
        //}
    }

        // 提交事务：所有手动结算操作成功完成
        $tsql->query("COMMIT");
        return 1;

    } catch (Exception $e) {
        // 回滚事务：手动结算过程中发生错误，撤销所有操作
        $tsql->query("ROLLBACK");
        error_log("jiaozhengeduedit 失败 (userid={$uids}): " . $e->getMessage());
        return 0;
    }
}

function jiaozhengedusss($qz=false) {
    global $tsql, $psql, $tb_user, $tb_lib, $tb_config, $tb_game;
    $thisday = getthisdate();
    $us = $tsql->arr("select userid,maxmoney,kmaxmoney,money,kmoney,sy,jetotal,jzkmoney from `$tb_user` where ifagent=0 and ifson=0 and fudong=0", 1);
    $cu = count($us);
    $g0 = " gid in(select gid from `$tb_game` where ifopen=1 and fast=0) ";
    $g1 = " gid in(select gid from `$tb_game` where ifopen=1 and fast=1) ";
    for ($i = 0; $i < $cu; $i++) {
        $uid = $us[$i]['userid'];
        $wh = " userid='$uid' and $g0 ";
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z=9 ", 0);
        $wjs = pr0($rs[0][0]);
        $mon = $us[$i]['maxmoney'] - $wjs;
        if ($us[$i]['money'] != $mon) {
            $tsql->query("update `$tb_user` set money='$mon',sy=0 where userid='$uid'");
        }
        $wh = " userid='$uid' and dates='$thisday' and $g1";
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z!=9", 0);
        $jetotals = pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(je),sum(je*points/100) from `$tb_lib` where $wh and z!=9 and z!=2 and z!=7", 0);
        $yjs = pr0($rs[0][0]);
        $points = pr0($rs[0][1]);
        // 修复：直接使用实际派奖金额 prize，而不是 sum(je*peilv1)-sum(prize)
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=1 ", 0);
        $yizhong = pr0($rs[0][0]);
        // 修复：z=3 也使用实际派奖金额 prize，与 jiaozhengedu() 保持一致
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=3 ", 0);
        $yizhong+= pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z=9 ", 0);
        $wjs = pr0($rs[0][0]);
        $mon = $us[$i]['kmaxmoney'] - $yjs - $wjs + $yizhong + $points - $us[$i]['jzkmoney'];
        $sy = $yizhong + $points - $yjs;
        if ($jetotals != $us[$i]['jetotal'] || $qz) {
            $tsql->query("update `$tb_user` set kmoney='$mon',sy='$sy',jetotal='$jetotals' where userid='$uid' and kmoney='" . $us[$i]['kmoney'] . "'");
            if(!$qz){
                usermoneylog($uid, pr0($mon - $us[$i]['kmoney']) , $mon, '结算后较正',1,'127.0.0.1');
            }
            
        }
    }
    $us = $tsql->arr("select userid,kmaxmoney,kmoney,ftime,wid,jetotal,jzkmoney from `$tb_user` where fudong=1", 1);
    $cu = count($us);
    for ($i = 0; $i < $cu; $i++) {
        $uid = $us[$i]['userid'];
        $ftime = $us[$i]['ftime'];
        $wh = "  userid='$uid' and dates='$thisday' ";
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z!=9", 0);
        $jetotals = pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(je),sum(je*points/100) from `$tb_lib` where $wh and z!=9 and z!=2 and z!=7", 0);
        $yjs = pr0($rs[0][0]);
        $points = pr0($rs[0][1]);
        // 修复：直接使用实际派奖金额 prize，而不是 sum(je*peilv1)-sum(prize)
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=1 ", 0);
        $yizhong = pr0($rs[0][0]);
        // 修复：z=3 也使用实际派奖金额 prize，与 jiaozhengedu() 保持一致
        $rs = $tsql->arr("select sum(prize) from `$tb_lib` where $wh and z=3 ", 0);
        $yizhong+= pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(je) from `$tb_lib` where $wh and z=9 ", 0);
        $wjs = pr0($rs[0][0]);
        $mon = $us[$i]['kmaxmoney'] - $yjs - $wjs + $yizhong + $points - $us[$i]['jzkmoney'];
        $sy = $yizhong + $points - $yjs;
        if ($jetotals != $us[$i]['jetotal'] || $qz) {
            $tsql->query("update `$tb_user` set kmoney='$mon',sy='$sy',jetotal='$jetotals' where userid='$uid' and kmoney='" . $us[$i]['kmoney'] . "' ");
            if(!$qz){
                usermoneylog($uid, pr0($mon - $us[$i]['kmoney']) , $mon, '结算后较正',1,'127.0.0.1');
            }
        }
    }
    return 1;
}

function jiaozhengedubb() {
    global $tsql, $psql, $tb_user, $tb_lib, $tb_config, $tb_game;
    $rs = $tsql->query("select editstart,reseted,editend from `$tb_config`");
    $tsql->next_record();
    $sdate = week();
    if ($tsql->f('reseted') == 'week') {
        $start = $sdate[5] . ' ' . $tsql->f('editend');
    } else {
        $his = date("His");
        if ($his <= str_replace(':', '', $tsql->f('editstart'))) {
            $start = date("Y-m-d", time() - 86400) . ' ' . $tsql->f('editend');
        } else {
            $start = $sdate[10] . ' ' . $tsql->f('editend');
        }
    }
    $fstart = $sdate[10] . ' ' . $tsql->f('editend');
    $end = sqltime(time());
    $us = $tsql->arr("select userid,maxmoney,kmaxmoney,money,kmoney,sy,jetotal,jzkmoney from `$tb_user` where ifagent=0 and ifson=0 and fudong=0", 1);
    $cu = count($us);
    for ($i = 0; $i < $cu; $i++) {
        $uid = $us[$i]['userid'];
        $wh = " from `$tb_lib` A join `$tb_game` B on A.gid=B.gid where A.userid='$uid' and B.ifopen=1 and B.fast=0 and A.time>='$start' and A.time<='$end' ";
        $rs = $tsql->arr("select sum(A.je) $wh and A.z=9 ", 0);
        $wjs = pr0($rs[0][0]);
        $mon = $us[$i]['maxmoney'] - $wjs;
        if ($us[$i]['money'] != $mon) {
            $tsql->query("update `$tb_user` set money='$mon',sy=0 where userid='$uid'");
        }
        $wh = " from `$tb_lib` A join `$tb_game` B on A.gid=B.gid where A.userid='$uid' and B.ifopen=1 and B.fast=1 and A.time>='$start' and A.time<='$end' ";
        $rs = $tsql->arr("select sum(A.je),sum(A.je*A.points/100) $wh and A.z!=9 and A.z!=2 and A.z!=7", 0);
        $yjs = pr0($rs[0][0]);
        $points = pr0($rs[0][1]);
        $rs = $tsql->arr("select sum(A.je*A.peilv1) $wh and A.z=1 ", 0);
        $yizhong = pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(A.je*A.peilv2) $wh and A.z=3 ", 0);
        $yizhong+= pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(A.je) $wh and A.z=9 ", 0);
        $wjs = pr0($rs[0][0]);
        $mon = $us[$i]['kmaxmoney'] - $yjs - $wjs + $yizhong + $points - $us[$i]['jzkmoney'];
        $sy = $yizhong + $points - $yjs;
        if ($yjs != $us[$i]['jetotal']) {
            $tsql->query("update `$tb_user` set kmoney='$mon',sy='$sy',jetotal='$yjs' where userid='$uid' and kmoney='" . $us[$i]['kmoney'] . "'");
            usermoneylog($uid, pr0($mon - $us[$i]['kmoney']) , $mon, '结算后较正',1,'127.0.0.1');
        }
    }
    $us = $tsql->arr("select userid,kmaxmoney,kmoney,ftime,wid,jetotal,jzkmoney from `$tb_user` where fudong=1", 1);
    $cu = count($us);
    for ($i = 0; $i < $cu; $i++) {
        $uid = $us[$i]['userid'];
        $ftime = $us[$i]['ftime'];
        $wh = " from `$tb_lib` A join `$tb_game` B on A.gid=B.gid where A.userid='$uid' and B.ifopen=1 and B.fast=1 and A.time>'$ftime' ";
        $rs = $tsql->arr("select sum(A.je),sum(A.je*A.points/100) $wh and A.z!=9 and A.z!=2 and A.z!=7", 0);
        $yjs = pr0($rs[0][0]);
        $points = pr0($rs[0][1]);
        $rs = $tsql->arr("select sum(A.je*A.peilv1) $wh and A.z=1 ", 0);
        $yizhong = pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(A.je*A.peilv2) $wh and A.z=3 ", 0);
        $yizhong+= pr0($rs[0][0]);
        $rs = $tsql->arr("select sum(A.je) $wh and A.z=9 ", 0);
        $wjs = pr0($rs[0][0]);
        $mon = $us[$i]['kmaxmoney'] - $yjs - $wjs + $yizhong + $points - $us[$i]['jzkmoney'];
        $sy = $yizhong + $points - $yjs;
        if ($yjs != $us[$i]['jetotal']) {
            $tsql->query("update `$tb_user` set kmoney='$mon',jetotal='$yjs',sy='$sy' where userid='$uid' and kmoney='" . $us[$i]['kmoney'] . "'");
            usermoneylog($uid, pr0($mon - $us[$i]['kmoney']) , $mon, '结算后较正',1,'127.0.0.1');
        }
    }
    return 1;
}
function jiaozhengday($dates) {
    global $tsql, $psql, $tb_user, $tb_lib, $tb_config, $tb_game, $tb_money;
    $psql->query("select editend from `$tb_config`");
    $psql->next_record();
    if($dates==""){
        $day = date("Y-m-d", time() - 86400);
        $ftime = date("Y-m-d") . ' ' . $psql->f('editend');
        $upftime = date("Y-m-d", time() - 86400) . ' ' . $psql->f('editend');
    }else{
        $day = $dates;
        $ftime = $day. ' ' . $psql->f('editend');
         $upftime = date("Y-m-d", strtotime($day) - 86400) . ' ' . $psql->f('editend');
    }
  
    $us = $tsql->arr("select userid,kmaxmoney,ftime,kmoney,layer,fid1,jetotal,jzkmoney from `$tb_user` where ifagent=1 and ifson=0 and fudong=1", 1);
    $cu = count($us);
    for ($i = 0; $i < $cu; $i++) {
        if ($us[$i]['userid'] == 99999999) continue;
        $uid = $us[$i]['userid'];
        $layer = $us[$i]['layer'];
        if ($layer < 8) {
            $mystr = "points" . $layer;
            $downstr = "points" . ($layer + 1);
            $peilvstr = "peilv1" . ($layer + 1);
            $wh = " uid" . $layer . "='$uid' and dates='$day' ";
        } else {
            $mystr = "points" . $layer;
            $downstr = "points";
            $peilvstr = 'peilv1';
            $wh = " uid" . $layer . "='$uid' and dates='$day' ";
        }
        $tsql->query("select sum((if($peilvstr=0,$mystr-points,$mystr-$downstr)/100)*je) from `$tb_lib` where $wh and z!=9 and z!=2 and z!=7");
        $tsql->next_record();
        $points = round($tsql->f(0) , 0);
        $kmoney = $us[$i]["kmoney"] + $points;
        if ($us[$i]['ftime'] == $upftime) {
            $tsql->query("update `$tb_user` set kmaxmoney='$kmoney',kmoney='$kmoney',ftime='$ftime',jzkmoney=0,sy=0,jetotal=0 where userid='$uid' and ftime='$upftime'");
            usermoneylog($uid, $points, $kmoney, $day . "赚取退水",1,'127.0.0.1');
            userchange($day . "赚取退水".$points, $uid,'127.0.0.1');
        }
    }
    return 1;
}
function getlibje($gid, $qs) {
    global $tb_lib, $psql;
    $rs = $psql->arr("select count(id),sum(je),sum(je*zc0/100) from `$tb_lib` where gid='$gid' and qishu='$qs' and xtype!=2", 0);
    $r2 = $psql->arr("select count(id),sum(je) from `$tb_lib` where gid='$gid' and qishu='$qs' and userid=99999999", 0);
    return array(
        pr0($rs[0][0]) ,
        pr0($rs[0][1]) ,
        pr0($rs[0][2]),
        pr0($r2[0][0]) ,
        pr0($r2[0][1]) 
    );
}
function attpeilvs($gid) {
    global $tb_config, $psql, $tb_game, $tb_auto, $tb_play, $tb_play_user, $tb_class, $tb_peilv, $tb_user, $tb_lib, $tb_c;
    $psql->query("select thisqishu,panstatus,otherstatus from `$tb_game` where gid='$gid'");
    $psql->next_record();
    if ($psql->f('panstatus') != 1 & $psql->f('otherstatus') != 1) return;
    $qishu = $psql->f('thisqishu');
    $rs = $psql->arr("select comattpeilv from `$tb_config`", 0);
    $comattpeilv = $rs[0][0];
    $psql->query("select * from `$tb_game` where gid='$gid'");
    $psql->next_record();
    $ftypearr = json_decode($psql->f('ftype') , true);
    if ($comattpeilv == 1) {
        $sql = "select * from `$tb_auto` where gid = '$gid' and ifok=1  and ( userid='99999999' or userid in (select userid from `$tb_user` where layer=1 and ifexe=1 and pself=1))  order by class";
    } else {
        $sql = "select * from `$tb_auto` where gid = '$gid' and ifok=1  and userid='99999999'  order by class";
    }
    $rs = $psql->arr($sql, 1);
    $cid = array();
    foreach ($rs as $k => $v) {
        if ($v['ifok'] != 1 || $v['startje']==0) continue;
        $startje = $v['startje'];
        $startpeilv = $v['startpeilv'];
        $addje = $v['addje'];
        $attpeilv = $v['attpeilv'];
        $lowpeilv = $v['lowpeilv'];
        $stopje = $v['stopje'];
        $ifzc = $v['ifzc'];
        $class = $v['class'];
        $userid = $v['userid'];
        $time = time();
        if (!is_array($cidarr[$class])) {
            $rs1 = $psql->arr("select cid from `$tb_class` where gid='$gid' and ftype='$class'", 0);
            foreach ($rs1 as $k1 => $v1) {
                $cidarr[$class][] = $v1[0];
            }
        }
        $tmp = implode(',', $cidarr[$class]);
        if ($userid == 99999999) {
            if ($ifzc == 1) {
                $pa = $psql->arr("select sum(je*zc0/100) as je,pid,content from `$tb_lib` where gid='$gid' and qishu='$qishu' and xtype!=2 and cid in ($tmp) and content='' group by pid", 1);
            } else {
                $pa = $psql->arr("select sum(je) as je,pid,content from `$tb_lib` where gid='$gid' and qishu='$qishu' and xtype!=2 and cid in ($tmp) and content='' group by pid", 1);
            }
        } else {
            if ($ifzc == 1) {
                $pa = $psql->arr("select sum(je*zc1/100) as je,pid,cid,sid,pid,content from `$tb_lib` where gid='$gid' and qishu='$qishu' and uid1='$userid' and xtype!=2 and cid in ($tmp) and content='' group by pid", 1);
            } else {
                $pa = $psql->arr("select sum(je) as je,pid,cid,sid,bid,content from `$tb_lib` where gid='$gid' and qishu='$qishu' and uid1='$userid' and xtype!=2 and cid in ($tmp) and content='' group by pid", 1);
            }
        }
        foreach ($pa as $ka => $va) {
            $je = $va['je'];
            if ($je < $startje) {
                continue;
            }
            if ($je >= $stopje) {
                if ($userid == 99999999) {
                    //$psql->query("update `$tb_play` set ifok='0' where gid='$gid' and pid='$pid'");
                }
            }
            $pid = $va['pid'];
            $cid = $va['cid'];
            $sid = $va['sid'];
            $bid = $va['bid'];
            $con = $va['content'];
            if ($userid == 99999999) {
                $psql->query("select yautocs,ystart from `$tb_play` where gid='$gid' and pid='$pid'");
                $psql->next_record();
                $ystart = $psql->f('ystart');
                $yautocs = $psql->f('yautocs');
                if ($ystart == 0) {
                    $time = time();
                    $psql->query("delete from `$tb_c` where userid='$userid' and gid='$gid' and pid='$pid' ");
                    $psql->query("insert into `$tb_c` set gid='$gid',pid='$pid',time=NOW(),userid='$userid'");
                    $psql->query("update `$tb_play` set ystart=1,yautocs=1,peilv1=peilv1-$startpeilv where gid='$gid' and pid='$pid'");
                    $startpeilvs = 0 - $startpeilv;
                    $psql->query("insert into `$tb_peilv` set gid='$gid',pid='$pid',peilv='$startpeilvs',time=NOW(),userid='$userid',sonuser='11111111',auto=1");
                } else {
                    $attcs = ((($je - $startje) - (($je - $startje) % $addje)) / $addje) + 1;
                    $ucs = floor($attcs - $yautocs);
                    $time = time();
                    if ($ucs > 0) {
                        $psql->query("delete from `$tb_c` where userid='$userid' and gid='$gid' and pid='$pid' ");
                        $psql->query("insert into `$tb_c` set gid='$gid',pid='$pid',time=NOW(),userid='$userid'");
                        $psql->query("update `$tb_play` set yautocs='$attcs',peilv1=if(peilv1-$ucs*$attpeilv>$lowpeilv,peilv1-$ucs*$attpeilv,$lowpeilv) where gid='$gid' and pid='$pid'");
                        $tmp = 0 - $ucs * $attpeilv;
                        $psql->query("insert into `$tb_peilv` set gid='$gid',pid='$pid',peilv='$tmp',time=NOW(),userid='$userid',sonuser='11111111',auto=1");
                    }
                }
            } else {
                $psql->query("select yautocs,ystart from `$tb_play_user` where userid='$userid' and gid='$gid' and pid='$pid'");
                $psql->next_record();
                $ystart = $psql->f('ystart');
                $yautocs = $psql->f('yautocs');
                if ($ystart == 0) {
                    $time = time();
                    $psql->query("delete from `$tb_c` where userid='$userid' and gid='$gid' and pid='$pid' ");
                    $psql->query("insert into `$tb_c` set gid='$gid',pid='$pid',time=NOW(),userid='$userid'");
                    $psql->query("update `$tb_play_user` set ystart=1,yautocs=1,peilv1=peilv1-$startpeilv where userid='$userid' and gid='$gid' and pid='$pid'");
                    $startpeilvs = 0 - $startpeilv;
                    $psql->query("insert into `$tb_peilv` set gid='$gid',pid='$pid',peilv='$startpeilv',time=NOW(),userid='$userid',sonuser='11111111',auto=1");
                } else {
                    $attcs = ((($je - $startje) - (($je - $startje) % $addje)) / $addje) + 1;
                    $ucs = floor($attcs - $yautocs);
                    $time = time();
                    if ($ucs > 0) {
                        $psql->query("delete from `$tb_c` where userid='$userid' and gid='$gid' and pid='$pid' ");
                        $psql->query("insert into `$tb_c` set gid='$gid',pid='$pid',time=NOW(),userid='$userid'");
                        $psql->query("update `$tb_play_user` set yautocs='$attcs',peilv1=if(peilv1-$ucs*$attpeilv>$lowpeilv,peilv1-$ucs*$attpeilv,$lowpeilv) where userid='$userid' and gid='$gid' and pid='$pid'");
                        $tmp = 0 - $ucs * $attpeilv;
                        $psql->query("insert into `$tb_peilv` set gid='$gid',pid='$pid',peilv='$tmp',time=NOW(),userid='$userid',sonuser='11111111',auto=1");
                    }
                }
            }
        }
    }
}
function attpeilv($gid) {
    if ($gid == 100) return false;
    global $tb_config, $psql, $tb_auto, $tb_play, $tb_play_user, $tb_class, $tb_peilv, $tb_user, $tb_game;
    $fenlei = transgame($gid, 'fenlei');
    $farr['g101']['单'] = "1,3,5,7,9";
    $farr['g101']['双'] = "0,2,4,6,8";
    $farr['g101']['大'] = "5,6,7,8,9";
    $farr['g101']['小'] = "0,1,2,3,4";
    $farr['g107']['单'] = "1,3,5,7,9";
    $farr['g107']['双'] = "2,4,6,8,10";
    $farr['g107']['大'] = "6,7,8,9,10";
    $farr['g107']['小'] = "1,2,3,4,5";
    $psql->query("select comattpeilv,autold,plresetfs from `$tb_config`");
    $psql->next_record();
    $comattpeilv = $psql->f(0);
    $autold = $psql->f(1);
    $plresetfs = $psql->f(2);
    $psql->query("update `$tb_play` set yautocs=0,ystart=0 where gid='$gid'");
    $psql->query("update `$tb_play_user` set yautocs=0,ystart=0 where gid='$gid'");
    $psql->query("update `$tb_play` set peilv1=mp1 where gid='$gid'");// and starat=0
    $psql->query("update `$tb_play_user` set peilv1=mp1 where gid='$gid'"); //and start=0
    if ($comattpeilv == 1) {
        $sql = "select * from `$tb_auto` where gid = '$gid' and ifok=1  and ( userid='99999999' or userid in (select userid from `$tb_user` where layer=1 and ifexe=1 and pself=1))  order by class";
    } else {
        $sql = "select * from `$tb_auto` where gid = '$gid' and ifok=1  and userid='99999999'  order by class";
    }
    $rs = $psql->arr($sql, 1);
    $cid = array();
    foreach ($rs as $k => $v) {
        if ($v['ifok'] != 1) continue;
        if($v['startpeilv']==0 || $v['attpeilv']==0 || $v['qsnum']==0 || $v['startje']==0 || $v['addje']==0) continue;
        $attpeilv = $v['qspeilv'];
        $attpeilvs = 0 - $attpeilv;
        $class = $v['class'];
        $qishu = $v['qsnum'];
        $userid = $v['userid'];
        $lowpeilv = $v['lowpeilv'];
        $time = time();
        if (!is_array($cidarr[$class])) {
            $rs1 = $psql->arr("select cid from `$tb_class` where gid='$gid' and ftype='$class'", 0);
            foreach ($rs1 as $k1 => $v1) {
                $cidarr[$class][] = $v1[0];
            }
        }
        $tmp = implode(',', $cidarr[$class]);
        if ($userid == 99999999) {
            $whi = " gid='$gid' and cid in ($tmp) ";
            if ($plresetfs == 'now') {
                $pp = $psql->arr("select * from `$tb_play` where $whi and buzqishu<$qishu and autocs>0 and start=1 and name in('大','小','单','双') and gid in(select gid from `$tb_game` where fenlei=107 or fenlei=101) ");
                foreach ($pp as $k2 => $v2) {
                    if ($v2['sid'] != '') {
                        $sql1 = "insert into `$tb_peilv` select NULL,'$gid',pid,mp1,NOW(),'99999999','11111111',1 from `$tb_play` where gid='$gid' and sid='" . $v2['sid'] . "' and name in (" . $farr['g' . $fl][$pp['name']] . ")";
                        $psql->query($sql1);
                        $sql2 = "update `$tb_play` set peilv1=mp1 where  $whi  and buzqishu<$qishu and autocs>0 and start=1 ";
                        $psql->query($sql2);
                    }
                }
                $sql1 = "insert into `$tb_peilv` select NULL,'$gid',pid,mp1,NOW(),'99999999','11111111',1 from `$tb_play` where $whi and buzqishu<$qishu and autocs>0 and start=1";
                $psql->query($sql1);
                $sql2 = "update `$tb_play` set peilv1=mp1,autocs=0,start=0 where  $whi  and buzqishu<$qishu and autocs>0 and start=1 ";
                $psql->query($sql2);
            } else {
                $sql1 = "insert into `$tb_peilv` select NULL,'$gid',pid,mp1,NOW(),'99999999','11111111',1 from `$tb_play` where $whi and buzqishu<$qishu and autocs>0 and start=2";
                $psql->query($sql1);
                $sql2 = "update `$tb_play` set peilv1=mp1,autocs=0,start=0 where  $whi  and buzqishu<$qishu and autocs>0 and start=2 ";
                $psql->query($sql2);
                $sql = "update `$tb_play` set start=2,peilv1=mp1-autocs*$attpeilv where  $whi  and buzqishu<$qishu and autocs>0 and start=1";
                $psql->query($sql);
            }
            $sql3 = "insert into `$tb_peilv` select NULL,'$gid',pid,$attpeilvs,NOW(),'99999999','11111111',1 from `$tb_play` where  $whi  and buzqishu>=$qishu";
            $psql->query($sql3);
            $sql4 = "update `$tb_play` set peilv1=if((mp1-(buzqishu-$qishu+1)*$attpeilv)<$lowpeilv,$lowpeilv,mp1-(buzqishu-$qishu+1)*$attpeilv),autocs=autocs+1,start=1 where  $whi  and buzqishu>=$qishu";
            $psql->query($sql4);
            if ($autold == 1) {
                if ($plrestfs == 'now') {
                    $sql1 = "insert into `$tb_peilv` select NULL,'$gid',pid,mp1,NOW(),'99999999','11111111',1 from `$tb_play` where  $whi  and zqishu<$qishu and zautocs>0 and zstart=1";
                    $psql->query($sql1);
                    $sql2 = "update `$tb_play` set peilv1=mp1,zautocs=0,zstart=0 where  $whi  and zqishu<$qishu and zautocs>0 and zstart=1 ";
                    $psql->query($sql2);
                } else {
                    $sql1 = "insert into `$tb_peilv` select NULL,'$gid',pid,mp1,NOW(),'99999999','11111111',1 from `$tb_play` where  $whi  and zqishu<$qishu and zautocs>0 and zstart=2";
                    $psql->query($sql1);
                    $sql2 = "update `$tb_play` set peilv1=mp1,zautocs=0,zstart=0 where  $whi  and zqishu<$qishu and zautocs>0 and zstart=2 ";
                    $psql->query($sql2);
                    $sql = "update `$tb_play` set start=2,peilv1=mp1-zautocs*$attpeilv where  $whi  and zqishu<$qishu and zautocs>0 and start=1";
                    $psql->query($sql);
                }
                $sql3 = "insert into `$tb_peilv` select NULL,'$gid',pid,$attpeilvs,NOW(),'99999999','11111111',1 from `$tb_play` where  $whi  and zqishu>=$qishu";
                $psql->query($sql3);
                $sql4 = "update `$tb_play` set peilv1=if((mp1-(zqishu-$qishu+1)*$attpeilv)<$lowpeilv,$lowpeilv,mp1-(zqishu-$qishu+1)*$attpeilv),zautocs=zautocs+1,zstart=1 where  $whi  and zqishu>=$qishu";
                $psql->query($sql4);
            }
        } else {
            $whi = " A.userid='$userid' and B.gid='$gid' and  B.cid in ($tmp) ";
            if ($plresetfs == 'now') {
                $sql1 = "insert into `$tb_peilv` select NULL,'$gid',A.pid,A.mp1,NOW(),A.userid,'11111111',1 from `$tb_play_user` as A left join `$tb_play` as B  on A.gib=B.gid and A.pid=B.pid  where $whi and B.buzqishu<$qishu and A.autocs>0 and A.start=1";
                $psql->query($sql1);
                $sql2 = "update `$tb_play_user` as A,`$tb_play` as B set A.peilv1=A.mp1,A.autocs=0,A.start=0  where $whi and B.buzqishu<$qishu and A.autocs>0 and A.start=1 and A.pid=B.pid ";
                $psql->query($sql2);
            } else {
                $sql1 = "insert into `$tb_peilv` select NULL,'$gid',A.pid,A.mp1,NOW(),A.userid,'11111111',1 from `$tb_play_user` as A left join `$tb_play` as B on A.gib=B.gid and A.pid=B.pid  where $whi and B.buzqishu<$qishu and A.autocs>0 and A.start=2";
                $psql->query($sql1);
                $sql2 = "update `$tb_play_user` as A,`$tb_play` as B set A.peilv1=A.mp1,A.autocs=0,A.start=0 where $whi and B.buzqishu<$qishu and A.autocs>0 and A.start=2 and A.pid=B.pid ";
                $psql->query($sql2);
                $sql = "update `$tb_play_user` as A,`$tb_play` as B set A.start=2,A.peilv1=A.mp1-A.autocs*$attpeilv  where  $whi  and B.buzqishu<$qishu and A.autocs>0 and A.start=1 and A.pid=B.pid";
                $psql->query($sql);
            }
            $sql3 = "insert into `$tb_peilv` select NULL,'$gid',A.pid,$attpeilvs,NOW(),A.userid,'11111111',1 from `$tb_play_user` as A left join `$tb_play` as B on A.gib=B.gid and A.pid=B.pid  where  $whi  and B.buzqishu>=$qishu ";
            $psql->query($sql3);
            $sql4 = "update `$tb_play_user` as A,`$tb_play` as B set A.peilv1=if((A.mp1-(B.buzqishu-$qishu+1)*$attpeilv)<$lowpeilv,$lowpeilv,A.mp1-(B.buzqishu-$qishu+1)*$attpeilv),A.autocs=A.autocs+1,A.start=1 where  $whi  and B.buzqishu>=$qishu and A.pid=B.pid ";
            $psql->query($sql4);
            if ($autold == 1) {
                if ($plrestfs == 'now') {
                    $sql1 = "insert into `$tb_peilv` select NULL,'$gid',A.pid,A.mp1,NOW(),A.userid,'11111111',1 from `$tb_play_user` as A left join `$tb_play` as B on A.gib=B.gid and A.pid=B.pid  where  $whi  and B.zqishu<$qishu and A.zautocs>0 and A.zstart=1";
                    $psql->query($sql1);
                    $sql2 = "update `$tb_play_user` as A,`$tb_play` as B set A.peilv1=A.mp1,A.zautocs=0,A.zstart=0  where $whi and B.zqishu<$qishu and A.zautocs>0 and A.zstart=1 and A.pid=B.pid ";
                    $psql->query($sql2);
                } else {
                    $sql1 = "insert into `$tb_peilv` select NULL,'$gid',A.pid,A.mp1,NOW(),A.userid,'11111111',1 from `$tb_play_user` as A left join `$tb_play` as B on A.gib=B.gid and A.pid=B.pid  where  $whi  and B.zqishu<$qishu and A.zautocs>0 and A.zstart=2";
                    $psql->query($sql1);
                    $sql2 = "update `$tb_play_user` as A,`$tb_play` as B set A.peilv1=A.mp1,A.zautocs=0,A.zstart=0  where $whi and B.zqishu<$qishu and A.zautocs>0 and A.zstart=2 and A.pid=B.pid ";
                    $psql->query($sql2);
                    $sql = "update `$tb_play_user` as A,`$tb_play` as B set A.start=2,A.peilv1=A.mp1-A.zautocs*$attpeilv  where $whi and B.zqishu<$qishu and A.zautocs>0 and A.start=1 and A.pid=B.pid";
                    $psql->query($sql);
                }
                $sql3 = "insert into `$tb_peilv` select NULL,'$gid',A.pid,$attpeilvs,NOW(),A.userid,'11111111',1 from `$tb_play_user` as A left join `$tb_play` as B on A.gib=B.gid and A.pid=B.pid  where  $whi  and B.zqishu>=$qishu ";
                $psql->query($sql3);
                $sql4 = "update `$tb_play_user` as A,`$tb_play` as B set A.peilv1=if((A.mp1-(B.zqishu-$qishu+1)*$attpeilv)<$lowpeilv,$lowpeilv,A.mp1-(B.zqishu-$qishu+1)*$attpeilv),A.zautocs=A.zautocs+1,A.zstart=1 where  $whi  and B.zqishu>=$qishu and A.pid=B.pid ";
                $psql->query($sql4);
            }
        }
    }
    $sql = "update `$tb_play` set peilv1=mp1 where peilv1>mp1 and gid='$gid' ";
    $psql->query($sql);
    $sql = "select userid from `$tb_user` where layer=1 and ifexe=1 and pself=1 and userid!=99999999";
    $rs = $psql->arr($sql, 1);
    foreach ($rs as $k => $v) {
        $sql = "update `$tb_play_user` set peilv1=mp1 where peilv1>mp1 and gid='$gid' and userid='" . $v['userid'] . "'";
        $psql->query($sql);
    }
}

