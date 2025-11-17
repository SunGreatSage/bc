<?php

include_once 'jsfunc.php';
function calc($fenlei, $gid, $cs, $qishu, $mnum, $ztype, $mtype,$qz=false)
{
    global $fsql, $tsql, $psql, $tb_bclass, $tb_sclass, $tb_class, $tb_play, $tb_lib, $tb_user, $tb_kj, $tb_z, $tb_config,$tb_shui;
    $whi = " gid='{$gid}' and qishu='{$qishu}' ";
    $tsql->query("select * from `{$tb_kj}` where {$whi}");
    $tsql->next_record();
    if ($tsql->f('m1') == '') {
        return "未开奖";
    }
    
    if ($tsql->f('js') == 1 && !$qz) {
        return "该期数已经结算过";
    }
    $tb_lib = "x_lib";
    if(time()-strtotime($tsql->f('kjtime'))>86400){
        $tb_lib = $tb_lib."_".str_replace('-', '', $tsql->f('dates'));
        $psql->query("SHOW TABLES LIKE  '%{$tb_lib}%'");
        $psql->next_record();
        if (strpos($psql->f(0), 'lib') == false) {
            $tb_lib = "x_lib";
            //return ;
        }
    }


    $psql->query("update `{$tb_lib}` set kk=1,z=9,prize=0 where {$whi} and z!=7");
    $kj = [];
    $kj[0] = [];
    $kj[0]['m'] = [];
    for ($i = 1; $i <= $mnum; $i++) {
        $kj[0]['m'][] = $tsql->f("m" . $i);
    }
    $tmp = [];

    $marr = [];
    if ($fenlei == 100) {
        $rs = $tsql->arr("select ma,maxpc from `{$tb_config}`", 0);
        $marrs = json_decode($rs[0][0], true);
        foreach ($marrs as $v) {
            foreach ($v as $k1 => $v1) {
                $marr[$k1] = explode(',', $v1);
            }
        }
        $marr['pc'] = $rs[0][1];
    }
    $tsql->query("delete from `$tb_z` where gid='$gid' and qishu='$qishu'");
    $sql = "select bid,sid,cid,pid,content,bz,ab,userid,bz from `{$tb_lib}` where gid='{$gid}' and qishu='{$qishu}' group by cid,pid,content";
    $lib = $tsql->arr($sql, 1);
    $cl = count($lib);
    for ($i = 0; $i < 1; $i++) {
        $zhong = 0;
        $ft = 0;
        if ($cs['ft'] == 1) {
            $ft = getft($kj[$i]['m'],$cs);
        }
        $sx = [];
        $ws = [];
        if ($fenlei == 100) {
            foreach ($kj[$i]['m'] as $ks => $vs) {
                $sx[] = sx_100($vs, $marr);
                $ws[] = $vs % 10;
            }
        }
        for ($j = 0; $j < $cl; $j++) {
            $flag = 0;
            if ($tmpcid != $lib[$j]['cid']) {
                if (!isset($tmp['c' . $lib[$j]['cid']])) {
                    $tsql->query("select name,mtype from `{$tb_class}` where gid='{$gid}' and cid='{$lib[$j]['cid']}'");
                    $tsql->next_record();
                    $tmp['c' . $lib[$j]['cid']]['name'] = $tsql->f('name');
                    $tmp['c' . $lib[$j]['cid']]['mtype'] = $tsql->f('mtype');
                    $tmp['c' . $lib[$j]['cid']]['cm'] = $mtype[$tsql->f('mtype')];
                }
                if (!isset($tmp['s' . $lib[$j]['sid']])) {
                    $tmp['s' . $lib[$j]['sid']] = transs8('name', $lib[$j]['sid'], $gid);
                }
                if (!isset($tmp['b' . $lib[$j]['bid']])) {
                    $tmp['b' . $lib[$j]['bid']] = transb8('name', $lib[$j]['bid'], $gid);
                }
            }
            if (!isset($tmp['p' . $lib[$j]['pid']])) {
                $tsql->query("select name,ztype,znum1,znum2 from `{$tb_play}` where gid='{$gid}' and pid='{$lib[$j]['pid']}'");
                $tsql->next_record();
                $tmp['p' . $lib[$j]['pid']]['name'] = $tsql->f("name");
                $tmp['p' . $lib[$j]['pid']]['ztype'] = $ztype[$tsql->f("ztype")];
                $tmp['p' . $lib[$j]['pid']]['znum1'] = $tsql->f('znum1');
                $tmp['p' . $lib[$j]['pid']]['znum2'] = $tsql->f('znum2');
            }
            if ($fenlei == 100 && $bid == 23378733) {
                // 过关
                $bz = json_decode($lib[$j]['bz'], true);
                $zflag = 0;
                $xflag = 0;
                foreach ($bz as $v) {
                    $rmtype = $tsql->arr("select mtype from `{$tb_class}` where gid='{$gid}' and sid='" . $v['sid'] . "' and cid='" . $v['cid'] . "'", 1);
                    $rpname = $psql->arr("select name,ztype from `{$tb_play}` where gid='{$gid}' and sid='" . $v['sid'] . "' and cid='" . $v['cid'] . "' and pid='" . $v['pid'] . "'", 1);
                    if (in_array($kj[$i]['m'][$rmtype[0]['mtype'] - 1], $marr[$rpname[0]['name']])) {
                        $zflag++;
                    }
                    if ($kj[$i]['m'][$rmtype[0]['mtype'] - 1] == 25 && ($rpname[0]['name'] == '合尾大' || $rpname[0]['name'] == '合尾大')) {
                        $xflag = 2;
                    }
                    if ($kj[$i]['m'][$rmtype[0]['mtype'] - 1] == 49 && $rpname[0]['name'] != '合尾大' && $rpname[0]['name'] != '合尾大' && $rpname[0]['ztype'] == 1) {
                        $xflag = 2;
                    }
                }
                if ($xflag == 2) {
                    $flag = [2];
                } else {
                    $zflag == count($bz) && $zflag > 0 && ($flag = [1]);
                }
            } else {
                $flag = calcjs($fenlei, $gid, $kj[$i]['m'], $tmp['b' . $lib[$j]['bid']], $tmp['s' . $lib[$j]['sid']], $tmp['c' . $lib[$j]['cid']], $tmp['p' . $lib[$j]['pid']], $lib[$j]['content'], $ft, $marr, $sx, $ws);
            }
            if ($flag[0] == 5) {
                $zflag = $flag[1];
                $tsql->query("update `{$tb_lib}` set kk=1,z='5',prize=((peilv1-1)*{$zflag}*je+je) where {$whi} and pid='{$lib[$j]['pid']}' and z!=7");
            } else {
                if ($fenlei == 100 && $lib[$j]['bid'] == 23378733 && $flag[0] == 1) {
                    $tsql->query("select sum(je*(peilv1-1+points/100)) from `{$tb_lib}` where {$whi} and pid='{$lib[$j]['pid']}' and content='{$lib[$j]['content']}' and z!=7 ");
                    while ($tsql->next_record()) {
                        if ($tsql->f(0) > $marr['pc']) {
                            $tsql->query("update `{$tb_lib}` set kk=1,z='5',prize='{$marr['pc']}' where {$whi} and pid='{$lib[$j]['pid']}' and content='{$lib[$j]['content']}' and z!=7 ");
                        } else {
                            $tsql->query("update `{$tb_lib}` set kk=1,z='1',prize=je*peilv1 where {$whi} and pid='{$lib[$j]['pid']}' and content='{$lib[$j]['content']}' and z!=7 ");
                        }
                    }
                } else {
                    if ($lib[$j]['content'] != "") {
                        if(($tmp['p' . $lib[$j]['pid']]['name']=='三中二' || $tmp['p' . $lib[$j]['pid']]['name']=='二中特') && ($flag[0]==3 || $flag[0]==1)){
                             $tlm = $tsql->arr("select * from `$tb_lib` where {$whi} and pid='{$lib[$j]['pid']}' and content='{$lib[$j]['content']}' and z!=7",1);
                             foreach($tlm as $ka => $va){
                                 $sql = '';
                                 $pei = json_decode($va['bz'],true);
                                 if($flag[0]==1){
                                     foreach ($pei as $kb => $vb) {
                                        if($kb==0){
                                            $sql .= "peilv1='{$pei[0][0]}',";
                                        }else{
                                            $sql .= "peilv1{$kb}='".$pei[$kb][0]."',";
                                        }
                                     }
                                     $sql .= "prize=je*{$pei[0][0]},";
                                 }else{
                                     foreach ($pei as $kb => $vb) {
                                        if($kb==0){
                                            $sql .= "peilv1='{$pei[0][1]}',";
                                        }else{
                                            $sql .= "peilv1{$kb}='".$pei[$kb][1]."',";
                                        }
                                     }
                                     $sql .= "prize=je*{$pei[0][1]},";
                                 }
                                 $tsql->query("update `{$tb_lib}` set $sql"."z=1,kk=1 where id='{$va['id']}'");
                             }
                            
                        }else{
                            $tsql->query("update `{$tb_lib}` set kk=1,z='{$flag[0]}',prize=CASE WHEN '{$flag[0]}'='1' THEN je*peilv1 WHEN '{$flag[0]}'='3' THEN je*peilv2 WHEN '{$flag[0]}'='2' THEN je ELSE 0 END where {$whi} and pid='{$lib[$j]['pid']}' and content='{$lib[$j]['content']}' and z!=7 ");
                        }                        
                    } else {
                        $tsql->query("update `{$tb_lib}` set kk=1,z='{$flag[0]}',prize=CASE WHEN '{$flag[0]}'='1' THEN je*peilv1 WHEN '{$flag[0]}'='3' THEN je*peilv2 WHEN '{$flag[0]}'='2' THEN je ELSE 0 END where {$whi} and pid='{$lib[$j]['pid']}' and z!=7 ");
                        if($flag[0]==1){
                            $tsql->query("insert into `{$tb_z}` set gid='{$gid}',pid='{$lib[$j]['pid']}',qishu='{$qishu}'");
                        }                        
                    }
                }
            }
            $tmpcid = $lib[$j]['cid'];
        }
        $tsql->query("update `{$tb_kj}` set js=1 where {$whi}");
        // jiaozhengedu() 调用位置已移到 prize 计算完成后（第187行）
    }
    $us = $tsql->arr("select * from `$tb_shui` where isok=1 and shui>0",1);
    foreach($us as $k => $v){
        $val = $v["shui"];
        if($v['stype']==1){
            $tsql->query("update `$tb_lib` set peilv1=peilv1-$val,peilv11=peilv11-$val,peilv12=if(peilv12-$val<0,0,peilv12-$val),peilv13=if(peilv13-$val<0,0,peilv13-$val),peilv14=if(peilv14-$val<0,0,peilv14-$val),peilv15=if(peilv15-$val<0,0,peilv15-$val),peilv16=if(peilv16-$val<0,0,peilv16-$val),peilv17=if(peilv17-$val<0,0,peilv17-$val),peilv18=if(peilv18-$val<0,0,peilv18-$val),prize=je*(peilv1-$val),kk=1 where {$whi} and userid='{$v['userid']}' and z=1 and zc0>0");
        }else{
            $zuix = $v['zuix'];
            $zuid = $v['zuid'];
            $tsql->query("update `$tb_lib` set prize=floor(peilv1*$val*je),kk=1 where {$whi} and userid='{$v['userid']}' and z=1 and zc0>0");
            $tsql->query("update `$tb_lib` set prize=if(prize>$zuid,$zuid,prize),kk=1 where {$whi} and userid='{$v['userid']}' and z=1 and zc0>0");
            $tsql->query("update `$tb_lib` set prize=if(prize<$zuix,0,prize),kk=1 where {$whi} and userid='{$v['userid']}' and z=1 and zc0>0");
        }

    }

    // 在 prize 字段计算完成后调用派奖函数，确保获取到正确的实际派奖金额
    jiaozhengedu();

    return 1;
}
function calcmoni($fenlei, $gid, $cs, $qishu, $mnum, $ztype, $mtype)
{
    global $fsql, $tsql, $psql, $tb_bclass, $tb_sclass, $tb_class, $tb_play, $tb_lib, $tb_user, $tb_config,$tb_game;
    $uid = 0;
    if ($cs['zhiding'] != 0) {
        //$arr = $tsql->arr("select userid from `{$tb_user}` where username='" . $cs['zduser'] . "'", 1);
        //$uid = $arr[0]['userid'];
    }    

    if ($cs['zcmode'] == 1) {
        $sql = "select bid,sid,cid,pid,content,bz,ab,userid,sum(je*zc0/100) as jes,sum(je*(zc0/100)*peilv1) as z1,sum(je*(zc0/100)*peilv2) as z2,sum(je*(zc0/100)*points/100) as shui,bz,dates from `{$tb_lib}` where gid='{$gid}' and qishu='{$qishu}' group by cid,pid,content";
    } else {
        $sql = "select bid,sid,cid,pid,content,bz,ab,userid,sum(je) as jes,sum(je*peilv1) as z1,sum(je*peilv2) as z2,sum(je*points/100) as shui,bz,dates from `{$tb_lib}` where gid='{$gid}' and qishu='{$qishu}' group by cid,pid,content";
    }
    $lib = $tsql->arr($sql, 1);
    $cl = count($lib);
    $zje = 0;
    foreach ($lib as $v) {
        $zje += $v['jes'];
    }
    if ($zje < $cs['kongje'] || $cs['xtmode'] == 0 || $cl == 0) {
        return suiji($fenlei, $gid, $qishu);
    }     
    if($cs["ylup"]>0) {
        $dates = $lib[0]['dates'];
        $tsql->query("select sum(je*zc0/100),sum(if(z=1,peilv11,0)*je*zc0/100),sum(je*zc0*points1/100*100) from `$tb_lib` where gid='$gid' and dates='$dates' and z in(0,1)");
        $tsql->next_record();
        $zje = $tsql->f(0);
        $points = $tsql->f(2);
        $zhong = $tsql->f(1);
        $yk = $zje - $points - $zhong;
        if($yk>$cs["ylup"]){
             return suiji($fenlei, $gid, $qishu);
        }   
    }
    $kj = [];
    $tmp = [];
    $ftm = [];
    if ($cs['ft'] == 1) {
        $ftm = explode(',', $cs['ftnum']);
    }
    $marr = [];
    $y1 = [];
    $y2 = [];
    $sy1 = [];
    $sy2 = [];
    if ($fenlei == 100) {
        $rs = $tsql->arr("select ma,maxpc from `{$tb_config}`", 0);
        $marrs = json_decode($rs[0][0], true);
        foreach ($marrs as $v) {
            foreach ($v as $k1 => $v1) {
                $marr[$k1] = explode(',', $v1);
            }
        }
        $marr['pc'] = $rs[0][1];
    }
    $jiang = [];
    $usy = [];
    for ($i = 0; $i < $cs['suiji']; $i++) {
        $kj[$i]['m'] = suiji($fenlei, $gid, $qishu);
        //echo json_encode($kj[$i]['m']),"<bR>";
        $jiang[$i] = 0;
        $usy[$i] = 0;
        $ft = 0;
        if ($cs['ft'] == 1) {
            $ft = getft($kj[$i]['m'],$cs);
        }
        $sx = [];
        $ws = [];
        if ($fenlei == 100) {
            foreach ($kj[$i]['m'] as $ks => $vs) {
                $sx[] = sx_100($vs, $marr);
                $ws[] = $vs % 10;
            }
        }
        for ($j = 0; $j < $cl; $j++) {
            if ($fenlei == 100 && ($lib[$j]['bid'] == '26000004' || $lib[$j]['bid'] == '23378733')) {
                continue;
            }
            if ($tmpcid != $lib[$j]['cid']) {
                if (!isset($tmp['c' . $lib[$j]['cid']])) {
                    $tsql->query("select name,mtype from `{$tb_class}` where gid='{$gid}' and cid='{$lib[$j]['cid']}'");
                    $tsql->next_record();
                    $tmp['c' . $lib[$j]['cid']]['name'] = $tsql->f('name');
                    $tmp['c' . $lib[$j]['cid']]['mtype'] = $tsql->f('mtype');
                    $tmp['c' . $lib[$j]['cid']]['cm'] = $mtype[$tsql->f('mtype')];
                }
                if (!isset($tmp['s' . $lib[$j]['sid']])) {
                    $tmp['s' . $lib[$j]['sid']] = transs8('name', $lib[$j]['sid'], $gid);
                }
                if (!isset($tmp['b' . $lib[$j]['bid']])) {
                    $tmp['b' . $lib[$j]['bid']] = transb8('name', $lib[$j]['bid'], $gid);
                }
            }
            if (!isset($tmp['p' . $lib[$j]['pid']])) {
                $tsql->query("select name,ztype,znum1,znum2 from `{$tb_play}` where gid='{$gid}' and pid='{$lib[$j]['pid']}'");
                $tsql->next_record();
                $tmp['p' . $lib[$j]['pid']]['name'] = $tsql->f("name");
                $tmp['p' . $lib[$j]['pid']]['ztype'] = $ztype[$tsql->f("ztype")];
                $tmp['p' . $lib[$j]['pid']]['znum1'] = $tsql->f('znum1');
                $tmp['p' . $lib[$j]['pid']]['znum2'] = $tsql->f('znum2');
            }
            $flag = calcjs($fenlei, $gid, $kj[$i]['m'], $tmp['b' . $lib[$j]['bid']], $tmp['s' . $lib[$j]['sid']], $tmp['c' . $lib[$j]['cid']], $tmp['p' . $lib[$j]['pid']], $tmp['p' . $lib[$j]['content']], $ft, $marr, $sx, $ws);
            //echo $flag[0],",";
            switch ($flag[0]) {
                case '1':
                    $jiang[$i] += $lib[$j]['jes'] - $lib[$j]['z1'] - $lib[$j]['shui'];
                    break;
                case '3':
                    $jiang[$i] += $lib[$j]['jes'] - $lib[$j]['z2'] - $lib[$j]['shui'];
                    break;
                case '2':
                    $jiang[$i] += 0;
                    break;
                case '0':
                    $jiang[$i] += $lib[$j]['jes'] - $lib[$j]['shui'];
                    break;
            }
            if ($cs['zhiding'] != 0 && is_numeric($uid) && $uid > 10000000 && 1==2) {
                if ($lib[$j]['content'] != "") {
                    $psql->query("select sum(je) as jes,sum(je*peilv1) as z1,sum(je*peilv2) as z2,sum(je*points/100) as shui,bz from `{$tb_lib}` where {$whi} and userid='{$uid}' and pid='{$lib[$j]['pid']}' and content='{$lib[$j]['content']}'");
                } else {
                    $psql->query("select sum(je) as jes,sum(je*peilv1) as z1,sum(je*peilv2) as z2,sum(je*points/100) as shui,bz from `{$tb_lib}` where {$whi} and userid='{$uid}' and pid='{$lib[$j]['pid']}'");
                }
                $psql->next_record();
                switch ($flag[0]) {
                    case '1':
                        $usy[$i] += $psql->f('z1') + $psql->f('shui') - $psql->f('jes');
                        break;
                    case '3':
                        $usy[$i] += $psql->f('z2') + $psql->f('shui') - $psql->f('jes');
                        break;
                    case '2':
                        $usy[$i] += 0;
                        break;
                    case '0':
                        $usy[$i] += $psql->f('shui') - $psql->f('jes');
                        break;
                }
            }
            $tmpcid = $lib[$j]['cid'];
        }
        $jiang[$i] > 0 ? $y1[] = $jiang[$i] : ($y2[] = $jiang[$i]);
        $usy[$i] > 0 ? $sy1[] = $usy[$i] : ($sy2[] = $usy[$i]);
    }
    /*
    for ($i = 0; $i < $cs['suiji']; $i++) {
        $kj[$i]['jj'] = $jiang[$i];
        $kj[$i]['mm'] = implode(',', $kj[$i]['m']);
    }
    */
    sort($y1);
    sort($y2);
    $v = 0;
    switch ($cs['xtmode']) {
        case '3':
            $v = $y1[rand(0, count($y1) - 1)];
            break;
        case '2':
            $v = $y1[count($y1) - 1];
            break;
        case '1':
            $v = $y1[0];
            break;
        case '-1':
            $v = $y2[count($y2) - 1];
            break;
        case '-2':
            $v = $y2[0];
            break;
        case '-3':
            $v = $y2[rand(0, count($y2) - 1)];
            break;
        case '5':
            $totalqs = floor($cs["shenglv"]/10);
            $zhongqs = $cs["shenglv"]%10;
            $buzhongqs = $totalqs - $zhongqs;
            if($cs["yingqs"]+$cs["shuqs"]==$totalqs){
                $cs["yingqs"] = 0;
                $cs["shuqs"] = 0;
            }
            
            $v = $jiang[rand(0,$cs['suiji']-1)];
            $v<0 ? $cs["shuqs"]++ : $cs["yingqs"]++;
            if($cs["yingqs"]>$buzhongqs){
                $cs["yingqs"]--;
                $v = $y2[rand(0, count($y2) - 1)];
                $cs["shuqs"]++;
            }

            if($cs["shuqs"]>$zhongqs){
                $cs["shuqs"]--;
                $v = $y1[rand(0, count($y1) - 1)];
                $cs["yingqs"]++;
            }
            //$cs["v"] = $cs["v"].",".$v;
            $cs = json_encode($cs);
            $psql->query("update `$tb_game` set cs='$cs' where gid='$gid'");
        break;    
    }
    $key = array_search($v, $jiang);
    return $kj[$key]['m'];
}
function calcjs($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft, $marr, $sx, $ws)
{
    switch ($fenlei) {
        case '101':
            return moni_101($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft);
            break;
        case '107':
            return moni_107($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft);
            break;
        case '151':
            return moni_151($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft);
            break;
        case '161':
            return moni_161($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft);
            break;
        case '163':
            return moni_163($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft);
            break;
        case '121':
            return moni_121($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft);
            break;
        case '103':
            return moni_103($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft);
            break;
        case '100':
            return moni_100($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft, $marr, $sx, $ws);
            break;
    }
}
function moni_100($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft, $marr, $sx, $ws)
{
    $v = 0;
    $jj = 0;
    if($c['mtype']==0){
        $c['mtype'] = 6;
    }else if($c['mtype']<=6){
        $c['mtype'] -=1 ;
    }
    switch ($p['ztype']) {
        case '番摊':
            switch ($c['name']) {
                case "双面":
                    if ($p['name'] == "单" && $ft % 2 == 1) {
                        $v = 1;
                    } else {
                        if ($p['name'] == "双" && $ft % 2 == 0) {
                            $v = 1;
                        } else {
                            if ($p['name'] == "大" && $ft > 2) {
                                $v = 1;
                            } else {
                                if ($p['name'] == "小" && $ft < 3) {
                                    $v = 1;
                                }
                            }
                        }
                    }
                    break;
                case "番":
                    $ft . "番" == $p['name'] ? $v = 1 : ($v = 0);
                    break;
                case "念":
                    $ps = explode('念', $p["name"]);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if ($ps[1] == $ft) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                case "角":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case "正":
                    $ps = str_replace('正', '', $p['name']);
                    if ($ps > 2) {
                        $psdui = $ps - 2;
                    } else {
                        $psdui = $ps + 2;
                    }
                    if ($ps == $ft) {
                        $v = 1;
                    } else {
                        if ($psdui == $ft) {
                            $v = 0;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
                case "中":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case '加':
                    $ps = explode('加', $p['name']);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if (strpos($ps[1], $ft . "") !== false) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                default:
                    if ($p['znum1'] == $ft) {
                        $v = 0;
                    } else {
                        if (strpos($p['name'], $ft . "") !== false) {
                            $v = 1;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
            }
            break;
        case '码':
        case '碼':
            ($b == '正码' || $b == '正碼') ? $arr = [$kj[0],$kj[1],$kj[2],$kj[3],$kj[4],$kj[5]] : ($arr = [$kj[$c['mtype']]]);
            in_array($p['name'], $arr) && ($v = 1);
            break;
        case '单双':
        case '單雙':
            if ($c['name'] == '总单双' || $c['name'] == '總單雙') {
                $ma = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4] + $kj[5] + $kj[6];
                strpos($p['name'], danshuang_100($ma)) !== false && ($v = 1);
            } else {
                $ma = $kj[$c['mtype']];
                if ($ma == 49) {
                    $v = 2;
                } else {
                    strpos($p['name'], danshuang_100($ma)) !== false && ($v = 1);
                }
            }
            break;
        case '大小':
            if ($c['name'] == '总大小' || $c['name'] == '總大小') {
                $ma = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4] + $kj[5] + $kj[6];
                (($p['name'] == '总大' || $p['name'] == '總大') && $ma > 174) && ($v = 1);
                (($p['name'] == '总小' || $p['name'] == '總小') && $ma < 175) && ($v = 1);
            } else {
                $ma = $kj[$c['mtype']];
                if ($ma == 49) {
                    $v = 2;
                } else {
                    $p['name'] == '大' && $ma >= 25 && ($v = 1);
                    $p['name'] == '小' && $ma <= 24 && ($v = 1);
                }
            }
            break;
        case '合单双':
        case '合單雙':
            $ma = heshu($kj[$c['mtype']]);
            if ($kj[$c['mtype']] == 49) {
                $v = 2;
            } else {
                (strpos($p['name'], danshuang_100($ma)) !== false) && ($v = 1);
            }
            break;
        case "波色":
            in_array($kj[$c['mtype']], $marr[$p['name']]) && ($v = 1);
        break;    
        case '尾大小':
            $ma = $kj[$c['mtype']];
            if ($c['name'] == "合尾大小") {
                if ($ma == 25) {
                    $v = 2;
                } else {
                    $hs = heshu($ma);
                    (strpos($p['name'], daxiaow($hs%10)) !== false) && ($v = 1);
                }
            } else {
                if ($ma == 49) {
                    $v = 2;
                } else {
                    (strpos($p['name'], daxiaow($ma%10)) !== false) && ($v = 1);
                }
            }
            break;
        case '合大小':
            $ma = heshu($kj[$c['mtype']]);
            if ($kj[$c['mtype']] == 49) {
                $v = 2;
            } else {
                ($p['name'] == heshudaxiao_100($ma)) && ($v = 1);
            }
            break;
        case "家野":
            $ma = $kj[$c['mtype']];
            if ($ma == 49) {
                $v = 2;
            } else {
                in_array($ma, $marr[$p['name']]) && ($v = 1);
            }
            break;
        case "半波":
            if ($kj[$c['mtype']] == 49) {
                $v = 2;
            } else {
                in_array($kj[$c['mtype']], $marr[$p['name']]) && ($v = 1);
            }
            break;
        case "五行":
            in_array($kj[$c['mtype']], $marr[$p['name']]) && ($v = 1);
            break;
        case '生肖':
            if ($b == '一肖') {
                $arr = $kj;
            } else {
                if ($b == '正肖') {
                    $arr = [$kj[0], $kj[1], $kj[2], $kj[3], $kj[4], $kj[5]];
                } else {
                    $arr = [$kj[6]];
                }
            }
            $zflag = 0;
            foreach ($arr as $vv) {
                if (in_array($vv, $marr[$p['name']])) {
                    $zflag += 1;
                }
            }
            if ($zflag >= 1) {
                /*if ($b == "正肖") {
                    $v = 5;
                    $jj = $zflag;
                } else {*/
                    $v = 1;
                //}
            }
            break;
        case '尾数':
        case '尾數':
            $b == "特头尾" ? $arr = [$kj[6]] : ($arr = $kj);
            $zflag = 0;
            foreach ($arr as $vv) {
                if (in_array($vv, $marr[$p['name']])) {
                    $zflag = 1;
                    break;
                }
            }
            $zflag == 1 && ($v = 1);
            break;
        case "其他":
            if ($b == "总肖七色波") {
                $zx = array_count_values($sx);
                $czx = count($zx);
                switch ($c['name']) {
                    case '总肖':
                        $p['znum1'] == $czx && ($v = 1);
                        break;
                    case '总肖单双':
                        strpos($p['name'], danshuang($czx)) !== false && ($v = 1);
                        break;
                    default:
                        $hob = 0;
                        $lao = 0;
                        $lvb = 0;
                        for ($i = 0; $i < 6; $i++) {
                            in_array($kj[$i], $marr["紅"]) && $hob++;
                            in_array($kj[$i], $marr["藍"]) && $lab++;
                            in_array($kj[$i], $marr["綠"]) && $lvb++;
                        }
                        in_array($kj[6], $marr["紅"]) && ($hob += 1.5);
                        in_array($kj[6], $marr["藍"]) && ($lab += 1.5);
                        in_array($kj[6], $marr["綠"]) && ($lvb += 1.5);
                        $p['name'] == "和局" && ($hob == 3 && $lab == 3 && $lvb == 1.5 || $hob == 3 && $lvb == 3 && $lab == 1.5 || $lvb == 3 && $lab == 3 && $hob == 1.5) && ($v = 1);
                        $p['name'] != "和局" && ($hob == 3 && $lab == 3 && $lvb == 1.5 || $hob == 3 && $lvb == 3 && $lab == 1.5 || $lvb == 3 && $lab == 3 && $hob == 1.5) && ($v = 2);
                        $p['name'] == "红波" && $hob == max($hob, $lab, $lvb) && ($v = 1);
                        $p['name'] == "蓝波" && $lab == max($hob, $lab, $lvb) && ($v = 1);
                        $p['name'] == "绿波" && $lvb == max($hob, $lab, $lvb) && ($v = 1);
                        break;
                }
            } else {
                switch ($c['name']) {
                    case '特头数':
                        $p['name'] == floor($kj[$c['mtype']] / 10) . "头" && ($v = 1);
                        break;
                    case '特尾数':
                        $p['name'] == $kj[$c['mtype']] % 10 . "尾" && ($v = 1);
                        break;
                }
            }
            break;
        case '多肖':
            if ($b == '特肖连' | $b == '合肖') {
                if ($kj[6] == 49 && $p['znum1'] == 6) {
                    $v = 2;
                    break;
                }
                $cons = explode('-', $con);
                $cons = array_unique($cons);
                $cc = count($cons);
                $zflag = 0;
                foreach ($cons as $vv) {
                    if (in_array($kj[6], $marr[$vv])) {
                        $zflag = 1;
                        break;
                    }
                }
                if ($p['znum2'] < 0) {
                    $zflag == 0 && ($v = 1);
                } else {
                    $zflag == 1 && ($v = 1);
                }
            } else {
                $cons = explode('-', $con);
                $cons = array_unique($cons);
                $cc = count($cons);
                $zflag = 0;
                foreach ($cons as $vv) {
                    if (in_array($vv, $sx)) {
                        $zflag++;
                    }
                }
                if ($p['znum2'] >= 0) {
                    $zflag == $p['znum1'] && ($v = 1);
                } else {
                    $zflag == 0 && ($v = 1);
                }
            }
            break;
        case '多尾':
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            $zflag = 0;
            foreach ($cons as $vv) {
                if (in_array(str_replace('尾', '', $vv), $ws)) {
                    $zflag++;
                }
            }
            if ($p['znum2'] >= 0) {
                $zflag == $p['znum1'] && ($v = 1);
            } else {
                $zflag == 0 && ($v = 1);
            }
            break;
        case '多不中':
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            $zflag = 1;
            foreach ($cons as $vv) {
                if (in_array($vv, $kj)) {
                    $zflag = 0;
                    break;
                }
            }
            $zflag == 1 && ($v = 1);
            break;
        case '多码':
        case '多碼':
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if ($cc != $p['znum1'] && $cc != $p['znum2']) {
                break;
            }
            $arr = [$kj[0], $kj[1], $kj[2], $kj[3], $kj[4], $kj[5]];
            switch ($p['name']) {
                case '特串':
                    if((in_array($cons[0], $arr) && $cons[1] == $kj[6]) || (in_array($cons[1], $arr) && $cons[0] == $kj[6])){
                       ($v = 1);
                    }
                    break;
                case '二中特':
                    in_array($cons[0], $arr) && in_array($cons[1], $arr) && $cons[0] != $cons[1] && ($v = 1);
                    ($cons[0] == $kj[6] || $cons[1] == $kj[6]) && $con[0] != $con[1] && ($v = 3);
                    break;
                default:
                    $zflag = 0;
                    foreach ($cons as $vv) {
                        if (in_array($vv, $arr)) {
                            $zflag++;
                        }
                    }
                    if ($p['name'] == '三中二') {
                        if ($zflag == 2) {
                            $v = 1;
                        }
                        if ($zflag == 3) {
                            $v = 3;
                        }
                    } else {
                        $zflag == $p['znum1'] && ($v = 1);
                    }
                    break;
            }
            break;
    }
    return [$v, $jj];
}
function moni_121($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft)
{
    $v = 0;
    switch ($p['ztype']) {
        case '番摊':
            switch ($c['name']) {
                case "双面":
                    if ($p['name'] == "单" && $ft % 2 == 1) {
                        $v = 1;
                    } else {
                        if ($p['name'] == "双" && $ft % 2 == 0) {
                            $v = 1;
                        } else {
                            if ($p['name'] == "大" && $ft > 2) {
                                $v = 1;
                            } else {
                                if ($p['name'] == "小" && $ft < 3) {
                                    $v = 1;
                                }
                            }
                        }
                    }
                    break;
                case "番":
                    $ft . "番" == $p['name'] ? $v = 1 : ($v = 0);
                    break;
                case "念":
                    $ps = explode('念', $p["name"]);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if ($ps[1] == $ft) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                case "角":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case "正":
                    $ps = str_replace('正', '', $p['name']);
                    if ($ps > 2) {
                        $psdui = $ps - 2;
                    } else {
                        $psdui = $ps + 2;
                    }
                    if ($ps == $ft) {
                        $v = 1;
                    } else {
                        if ($psdui == $ft) {
                            $v = 0;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
                case "中":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case '加':
                    $ps = explode('加', $p['name']);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if (strpos($ps[1], $ft . "") !== false) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                default:
                    if ($p['znum1'] == $ft) {
                        $v = 0;
                    } else {
                        if (strpos($p['name'], $ft . "") !== false) {
                            $v = 1;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
            }
            break;
        case '码':
            $b == '正码' ? $ma = $kj : ($ma = [$kj[$c['mtype']]]);
            in_array($p['name'], $ma) ? $v = 1 : ($v = 0);
            break;
        case '单双':
            $c['name'] == '总和单双' ? $ma = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4] : ($ma = $kj[$c['mtype']]);
            strpos($p['name'], danshuang($ma)) !== false ? $v = 1 : ($v = 0);
            break;
        case '大小':
            $v = 0;
            if ($c['name'] == '总和大小') {
                $ma = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4];
                $p['name'] == '总和大' && $ma > 30 && ($v = 1);
                $p['name'] == '总和小' && $ma < 30 && ($v = 1);
                $ma == 30 && ($v = 2);
            } else {
                $ma = $kj[$c['mtype']];
                $p['name'] == '小' && $ma <= 5 && ($v = 1);
                $p['name'] == '大' && $ma >= 6 && ($v = 1);
                $ma == 11 && ($v = 2);
            }
            break;
        case '尾大小':
            $ma = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4];
            strpos($p['name'], daxiaow($ma % 10)) !== false && ($v = 1);
            break;
        case '龙虎':
            $ma = longhuhe($kj[0], $kj[4]);
            $ma == $p ? $v = 1 : $v == 0;
            break;
        case '连码':
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($con);
            if ($cc != $p['znum1']) {
                break;
            }
            switch ($p['name']) {
                case '任选二中二':
                    in_array($cons[0], $kj) && in_array($cons[1], $kj) && ($v = 1);
                    break;
                case '任选三中三':
                    in_array($cons[0], $kj) && in_array($cons[1], $kj) && in_array($cons[2], $kj) && ($v = 1);
                    break;
                case '任选四中四':
                    in_array($cons[0], $kj) && in_array($cons[1], $kj) && in_array($cons[2], $kj) && in_array($cons[3], $kj) && ($v = 1);
                    break;
                case '任选五中五':
                case '任选六中五':
                case '任选七中五':
                case '任选八中五':
                    in_array($kj[0], $cons) & in_array($kj[1], $cons) & in_array($kj[2], $cons) & in_array($kj[3], $cons) & in_array($kj[4], $cons) && ($v = 1);
                    break;
                case '选前二组选':
                    $arr = [$kj[0], $kj[1]];
                    in_array($cons[0], $arr) && in_array($cons[1], $arr) && ($v = 1);
                    break;
                case '选前二直选':
                    $cons[0] == $kj[0] && $cons[1] == $kj[1] && ($v = 1);
                    break;
                case '选前三组选':
                    $arr = [$kj[0], $kj[1], $kj[2]];
                    in_array($cons[0], $arr) && in_array($cons[1], $arr) && in_array($cons[2], $arr) && ($v = 1);
                    break;
                case '选前三直选':
                    $cons[0] == $kj[0] && $cons[1] == $kj[1] && $cons[2] == $kj[2] && ($v = 1);
                    break;
            }
            break;
    }
    return [$v];
}
function moni_161($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft)
{
    $v = 0;
    switch ($p['ztype']) {
        case '番摊':
            switch ($c['name']) {
                case "双面":
                    if ($p['name'] == "单" && $ft % 2 == 1) {
                        $v = 1;
                    } else {
                        if ($p['name'] == "双" && $ft % 2 == 0) {
                            $v = 1;
                        } else {
                            if ($p['name'] == "大" && $ft > 2) {
                                $v = 1;
                            } else {
                                if ($p['name'] == "小" && $ft < 3) {
                                    $v = 1;
                                }
                            }
                        }
                    }
                    break;
                case "番":
                    $ft . "番" == $p['name'] ? $v = 1 : ($v = 0);
                    break;
                case "念":
                    $ps = explode('念', $p["name"]);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if ($ps[1] == $ft) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                case "角":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case "正":
                    $ps = str_replace('正', '', $p['name']);
                    if ($ps > 2) {
                        $psdui = $ps - 2;
                    } else {
                        $psdui = $ps + 2;
                    }
                    if ($ps == $ft) {
                        $v = 1;
                    } else {
                        if ($psdui == $ft) {
                            $v = 0;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
                case "中":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case '加':
                    $ps = explode('加', $p['name']);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if (strpos($ps[1], $ft . "") !== false) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                default:
                    if ($p['znum1'] == $ft) {
                        $v = 0;
                    } else {
                        if (strpos($p['name'], $ft . "") !== false) {
                            $v = 1;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
            }
            break;
        case '正码':
            in_array($p['name'], $kj) && ($v = 1);
            break;
        case '总和':
            $ma = 0;
            $qma = 0;
            $dma = 0;
            for ($i = 0; $i < 20; $i++) {
                $kj[$i] <= 40 && $xma++;
                $kj[$i] % 2 == 1 && $dma++;
                $ma += $kj[$i];
            }
            switch ($c['name']) {
                case "总和单双":
                    strpos($p['name'], danshuang($ma)) !== false && ($v = 1);
                    break;
                case "总和大小":
                    $p['name'] == '总和大' && $ma > 810 && ($v = 1);
                    $p['name'] == '总和小' && $ma < 810 && ($v = 1);
                    $ma == 810 && ($v = 2);
                    break;
                case "总和810":
                    $ma == 810 && ($v = 1);
                    break;
                case "总和过关":
                    if ($ma == 810) {
                        $v = 2;
                    } else {
                        $tmp = danshuang($ma);
                        $p['name'] == '总大单' && $tmp == "单" && $ma > 810 && ($v = 1);
                        $p['name'] == '总大双' && $tmp == "双" && $ma > 810 && ($v = 1);
                        $p['name'] == '总小单' && $tmp == "单" && $ma < 810 && ($v = 1);
                        $p['name'] == '总小双' && $tmp == "双" && $ma < 810 && ($v = 1);
                    }
                    break;
                case "单双和":
                    $p['name'] == "单(多)" && $dma > 10 && ($v = 1);
                    $p['name'] == "双(多)" && $dma < 10 && ($v = 1);
                    $p['name'] == "单双(和)" && $dma == 10 && ($v = 1);
                    break;
                case "前后和":
                    $p['name'] == "前(多)" && $dma > 10 && ($v = 1);
                    $p['name'] == "后(多)" && $dma < 10 && ($v = 1);
                    $p['name'] == "前后(和)" && $dma == 10 && ($v = 1);
                    break;
            }
            break;
        case '五行':
            $ma = 0;
            for ($i = 0; $i < 20; $i++) {
                $ma += $kj[$i];
            }
            wuhang_161($ma) == $p['name'] && ($v = 1);
            break;
    }
    return [$v];
}
function moni_151($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft)
{
    $v = 0;
    switch ($p['ztype']) {
        case '码':
            in_array($p['name'], $kj) && ($v = 1);
            break;
        case '骰':
            if ($p["name"] == "全骰") {
                baozhi($kj[0], $kj[1], $kj[2]) == 1 && ($V = 1);
            } else {
                baozhi($kj[0], $kj[1], $kj[2]) == 1 & $kj[0] == $p['name'] % 10 && ($v = 1);
            }
            break;
        case '点':
            $ma = $kj[0] + $kj[1] + $kj[2];
            if ($c['name'] == '三军大小') {
                $p['name'] == '三军大' && $ma >= 11 && ($v = 1);
                $p['name'] == '三军小' && $ma < 11 && ($v = 1);
                baozhi($kj[0], $kj[1], $kj[2]) == 1 && ($v = 0);
            } else {
                str_replace('点', '', $p['name']) == $ma && ($v = 1);
            }
            break;
        case "牌":
            if ($c['name'] == '长牌') {
                $two = $p['name'] % 10;
                $one = ($p['name'] - $two) / 10;
                in_array($one, $kj) && in_array($two, $kj) && ($v = 1);
            } else {
                $two = $p['name'] % 10;
                $cs = array_count_values($kj);
                $cs[$two] >= 2 && ($v = 1);
            }
            break;
    }
    return [$v];
}
function moni_103($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft)
{
    $v = 0;
    switch ($p['ztype']) {
        case '番摊':
            switch ($c['name']) {
                case "双面":
                    if ($p['name'] == "单" && $ft % 2 == 1) {
                        $v = 1;
                    } else {
                        if ($p['name'] == "双" && $ft % 2 == 0) {
                            $v = 1;
                        } else {
                            if ($p['name'] == "大" && $ft > 2) {
                                $v = 1;
                            } else {
                                if ($p['name'] == "小" && $ft < 3) {
                                    $v = 1;
                                }
                            }
                        }
                    }
                    break;
                case "番":
                    $ft . "番" == $p['name'] ? $v = 1 : ($v = 0);
                    break;
                case "念":
                    $ps = explode('念', $p["name"]);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if ($ps[1] == $ft) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                case "角":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case "正":
                    $ps = str_replace('正', '', $p['name']);
                    if ($ps > 2) {
                        $psdui = $ps - 2;
                    } else {
                        $psdui = $ps + 2;
                    }
                    if ($ps == $ft) {
                        $v = 1;
                    } else {
                        if ($psdui == $ft) {
                            $v = 0;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
                case "中":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case '加':
                    $ps = explode('加', $p['name']);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if (strpos($ps[1], $ft . "") !== false) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                default:
                    if ($p['znum1'] == $ft) {
                        $v = 0;
                    } else {
                        if (strpos($p['name'], $ft . "") !== false) {
                            $v = 1;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
            }
            break;
        case '码':
            $b == '正码' ? $ma = $kj : ($ma = [$kj[$c['mtype']]]);
            in_array($p['name'], $ma) ? $v = 1 : ($v = 0);
            break;
        case '单双':
            $c['name'] == '总和单双' ? $ma = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4] + $kj[5] + $kj[6] + $kj[7] : ($ma = $kj[$c['mtype']]);
            strpos($p['name'], danshuang($ma)) !== false ? $v = 1 : ($v = 0);
            break;
        case '合单双':
            $ma = heshu($kj[$c['mtype']]);
            strpos($p['name'], danshuang($ma)) !== false ? $v = 1 : ($v = 0);
            break;
        case '大小':
            $v = 0;
            if ($c['name'] == '总和大小') {
                echo $ma = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4] + $kj[5] + $kj[6] + $kj[7];
                $p['name'] == '总和大' && $ma > 84 && ($v = 1);
                $p['name'] == '总和小' && $ma < 84 && ($v = 1);
                $ma == 84 && ($v = 2);
            } else {
                $ma = $kj[$c['mtype']];
                $p['name'] == '小' && $ma <= 10 && ($v = 1);
                $p['name'] == '大' && $ma >= 11 && ($v = 1);
            }
            break;
        case '尾大小':
            if ($c['name'] == '总尾大小') {
                $ma = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4] + $kj[5] + $kj[6] + $kj[7];
                strpos($p['name'], daxiaow($ma % 10)) !== false && ($v = 1);
            } else {
                $ma = $kj[$c['mtype']];
                strpos($p['name'], daxiaow($ma % 10)) !== false && ($v = 1);
            }
            break;
        case '龙虎':
            $ma = longhuhe($kj[$c['mtype']], $kj[7 - $c['mtype']]);
            $ma == $p['name'] ? $v = 1 : $v = 0;
            break;
        case '方位':
            fangwei($kj[$c['mtype']]) == $p['name'] && ($v = 1);
            break;
        case '中发白':
            zhongfabai($kj[$c['mtype']]) == $p['name'] && ($v = 1);
            break;
        case '连码':
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if ($cc != $p['znum1']) {
                break;
            }
            switch ($p['name']) {
                case '选二任选':
                    in_array($cons[0], $kj) && in_array($cons[1], $kj) && ($v = 1);
                    break;
                case '选三任选':
                    in_array($cons[0], $kj) && in_array($cons[1], $kj) && in_array($cons[2], $kj) && ($v = 1);
                    break;
                case '选四任选':
                    in_array($cons[0], $kj) && in_array($cons[1], $kj) && in_array($cons[2], $kj) && in_array($cons[3], $kj) && ($v = 1);
                    break;
                case '选五任选':
                    in_array($cons[0], $kj) && in_array($cons[1], $kj) && in_array($cons[2], $kj) && in_array($cons[3], $kj) && in_array($cons[4], $kj) && ($v = 1);
                    break;
                case '选二连组':
                    if (in_array($cons[0], $kj)) {
                        $keylm = -1;
                        foreach ($kj as $klm => $vlm) {
                            $cons[0] == $vlm && ($keylm = $klm);
                        }
                        if ($keylm > 0) {
                            ($kj[$keylm - 1] == $cons[1] || $kj[$keylm + 1] == $cons[1]) && ($v = 1);
                        } else {
                            $kj[$keylm + 1] == $cons[1] && ($v = 1);
                        }
                    }
                    break;
                case '选二连直':
                    if (in_array($cons[0], $kj)) {
                        $keylm = -1;
                        foreach ($kj as $klm => $vlm) {
                            $cons[0] == $vlm && ($keylm = $klm);
                        }
                        $kj[$keylm + 1] == $con[1] && ($v = 1);
                    }
                    break;
                case '选三前组':
                    $arrlm = [$kj[0], $kj[1], $kj[2]];
                    in_array($cons[0], $arrlm) && in_array($cons[1], $arrlm) && in_array($cons[2], $arrlm) && ($v = 1);
                    break;
                case '选三前直':
                    $con[0] == $kj[0] && $con[1] == $kj[1] && $con[2] == $kj[2] && ($v = 1);
                    break;
                case '选前二组选':
                    $arr = [$kj[0], $kj[1]];
                    in_array($cons[0], $arr) && in_array($cons[1], $arr) && ($v = 1);
                    break;
                case '选前二直选':
                    $cons[0] == $kj[0] && $cons[1] == $kj[1] && ($v = 1);
                    break;
                case '选前三组选':
                    $arr = [$kj[0], $kj[1], $kj[2]];
                    in_array($cons[0], $arr) && in_array($cons[1], $arr) && in_array($cons[2], $arr) && ($v = 1);
                    break;
                case '选前三直选':
                    $cons[0] == $kj[0] && $cons[1] == $kj[1] && $cons[2] == $kj[2] && ($v = 1);
                    in_array($kj[0], $cons) & in_array($kj[1], $cons) & in_array($kj[2], $cons) & in_array($kj[3], $cons) & in_array($kj[4], $cons) && ($v = 1);
                    break;
            }
            break;
    }
    return [$v];
}
function moni_107($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft)
{
    $v = 0;
    switch ($p['ztype']) {
        case '番摊':
            switch ($c['name']) {
                case "双面":
                    if ($p['name'] == "单" && $ft % 2 == 1) {
                        $v = 1;
                    } else {
                        if ($p['name'] == "双" && $ft % 2 == 0) {
                            $v = 1;
                        } else {
                            if ($p['name'] == "大" && $ft > 2) {
                                $v = 1;
                            } else {
                                if ($p['name'] == "小" && $ft < 3) {
                                    $v = 1;
                                }
                            }
                        }
                    }
                    break;
                case "番":
                    $ft . "番" == $p['name'] ? $v = 1 : ($v = 0);
                    break;
                case "念":
                    $ps = explode('念', $p["name"]);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if ($ps[1] == $ft) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                case "角":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case "正":
                    $ps = str_replace('正', '', $p['name']);
                    if ($ps > 2) {
                        $psdui = $ps - 2;
                    } else {
                        $psdui = $ps + 2;
                    }
                    if ($ps == $ft) {
                        $v = 1;
                    } else {
                        if ($psdui == $ft) {
                            $v = 0;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
                case "中":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case '加':
                    $ps = explode('加', $p['name']);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if (strpos($ps[1], $ft . "") !== false) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                default:
                    if ($p['znum1'] == $ft) {
                        $v = 0;
                    } else {
                        if (strpos($p['name'], $ft . "") !== false) {
                            $v = 1;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
            }
            break;
        case '码':
            $b == '冠亚军组合' ? $ma = $kj[0] + $kj[1] : ($ma = $kj[$c['mtype']]);
            $ma == $p['name'] ? $v = 1 : ($v = 0);
            break;
        case '单双':
            $b == '冠亚军组合' ? $ma = $kj[0] + $kj[1] : ($ma = $kj[$c['mtype']]);
            strpos($p['name'], danshuang($ma)) !== false ? $v = 1 : ($v = 0);
            break;
        case '大小':
            $v = 0;
            if ($b == '冠亚军组合') {
                $zf = $kj[0] + $kj[1];
                if ($p['name'] == '冠亚大' && $zf > 11) {
                    $v = 1;
                } else {
                    if ($p['name'] == '冠亚小' && $zf <= 11) {
                        $v = 1;
                    }
                }
            } else {
                $ma = $kj[$c['mtype']];
                if ($p['name'] == '大' & $ma >= 6) {
                    $v = 1;
                } else {
                    if ($p['name'] == '小' & $ma <= 5) {
                        $v = 1;
                    }
                }
            }
            break;
        case '龙虎':
            $ma = longhuhe($kj[$c['mtype']], $kj[9 - $c['mtype']]);
            $ma == $p['name'] ? $v = 1 : $v == 0;
            break;
    }
    return [$v];
}
function moni_101($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft)
{
    $v = 0;
    switch ($b) {
        case '番摊':
            switch ($c['name']) {
                case "双面":
                    if ($p['name'] == "单" && $ft % 2 == 1) {
                        $v = 1;
                    } else {
                        if ($p['name'] == "双" && $ft % 2 == 0) {
                            $v = 1;
                        } else {
                            if ($p['name'] == "大" && $ft > 2) {
                                $v = 1;
                            } else {
                                if ($p['name'] == "小" && $ft < 3) {
                                    $v = 1;
                                }
                            }
                        }
                    }
                    break;
                case "番":
                    $ft . "番" == $p['name'] ? $v = 1 : ($v = 0);
                    break;
                case "念":
                    $ps = explode('念', $p["name"]);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if ($ps[1] == $ft) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                case "角":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case "正":
                    $ps = str_replace('正', '', $p['name']);
                    if ($ps > 2) {
                        $psdui = $ps - 2;
                    } else {
                        $psdui = $ps + 2;
                    }
                    if ($ps == $ft) {
                        $v = 1;
                    } else {
                        if ($psdui == $ft) {
                            $v = 0;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
                case "中":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case '加':
                    $ps = explode('加', $p['name']);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if (strpos($ps[1], $ft . "") !== false) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                default:
                    if ($p['znum1'] == $ft) {
                        $v = 0;
                    } else {
                        if (strpos($p['name'], $ft . "") !== false) {
                            $v = 1;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
            }
            break;
        case '1~5':
            $ma = $kj[$c['mtype']];
            switch ($p['ztype']) {
                case "码":
                    $ma == $p['name'] ? $v = 1 : ($v = 0);
                    break;
                case "单双":
                    strpos($p['name'], danshuang($ma)) !== false ? $v = 1 : ($v = 0);
                    break;
                case "大小":
                    echo $ma;
                    if (($p['name'] == "大" && $ma >= 5) || ($p['name'] == "小" && $ma < 5)) {
                        $v = 1;
                    }
                    break;
            }
            break;
        case '1字组合':
            $arr = [];
            switch ($c['cm']) {
                case "全部":
                    $arr = $kj;
                    break;
                case '前三':
                    $arr = [$kj[0], $kj[1], $kj[2]];
                    break;
                case '中三':
                    $arr = [$kj[1], $kj[2], $kj[3]];
                    break;
                case '后三':
                    $arr = [$kj[2], $kj[3], $kj[4]];
                    break;
            }
            if (in_array($p['name'], $arr)) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '2字组合':
            $arr = [];
            if (strpos($p['name'], '前三') !== false) {
                $arr = [$kj[0], $kj[1], $kj[2]];
            } else {
                if (strpos($p['name'], '中三') !== false) {
                    $arr = [$kj[1], $kj[2], $kj[3]];
                } else {
                    if (strpos($p['name'], '后三') !== false) {
                        $arr = [$kj[2], $kj[3], $kj[4]];
                    }
                }
            }
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if (in_array($cons[0], $arr) && in_array($cons[1], $arr) && $cc == 2) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '2字定位':
            $pnames = str_replace("定位","",$p['name']);
            switch ($pnames) {
                case '万千':
                    $arr = [$kj[0], $kj[1]];
                    break;
                case '万百':
                    $arr = [$kj[0], $kj[2]];
                    break;
                case '万十':
                    $arr = [$kj[0], $kj[3]];
                    break;
                case '万个':
                    $arr = [$kj[0], $kj[4]];
                    break;
                case '千百':
                    $arr = [$kj[1], $kj[2]];
                    break;
                case '千十':
                    $arr = [$kj[1], $kj[3]];
                    break;
                case '千个':
                    $arr = [$kj[0], $kj[4]];
                    break;
                case '百十':
                    $arr = [$kj[2], $kj[3]];
                    break;
                case '百个':
                    $arr = [$kj[2], $kj[4]];
                    break;
                case '十个':
                    $arr = [$kj[3], $kj[4]];
                    break;
            }
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if ($cons[0] == $arr[0] && $cons[1] == $arr[1] && $cc == 2) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '2字和数':
            switch ($c['cm']) {
                case '万千':
                    $arr = $kj[0] + $kj[1];
                    break;
                case '万百':
                    $arr = $kj[0] + $kj[2];
                    break;
                case '万十':
                    $arr = $kj[0] + $kj[3];
                    break;
                case '万个':
                    $arr = $kj[0] + $kj[4];
                    break;
                case '千百':
                    $arr = $kj[1] + $kj[2];
                    break;
                case '千十':
                    $arr = $kj[1] + $kj[3];
                    break;
                case '千个':
                    $arr = $kj[1] + $kj[4];
                    break;
                case '百十':
                    $arr = $kj[2] + $kj[3];
                    break;
                case '百个':
                    $arr = $kj[2] + $kj[4];
                    break;
                case '十个':
                    $arr = $kj[3] + $kj[4];
                    break;
            }
            if (strpos('[单双]', $p['name'])) {
                $p['name'] == danshuang($arr) ? $v = 1 : ($v = 0);
            } else {
                $tmp = daxiaow($arr % 10);
                strpos($p['name'], $tmp) !== false ? $v = 1 : ($v = 0);
            }
            break;
        case '3字组合':
            if (strpos($p['name'], '前三') !== false) {
                $arr = [$kj[0], $kj[1], $kj[2]];
            } else {
                if (strpos($p['name'], '中三') !== false) {
                    $arr = [$kj[1], $kj[2], $kj[3]];
                } else {
                    if (strpos($p['name'], '后三') !== false) {
                        $arr = [$kj[2], $kj[3], $kj[4]];
                    }
                }
            }
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if (in_array($cons[0], $arr) && in_array($cons[1], $arr) && in_array($cons[2], $arr) && $cc == 3) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '3字定位':
            if (strpos($p['name'], '前三') !== false) {
                $arr = [$kj[0], $kj[1], $kj[2]];
            } else {
                if (strpos($p['name'], '中三') !== false) {
                    $arr = [$kj[1], $kj[2], $kj[3]];
                } else {
                    if (strpos($p['name'], '后三') !== false) {
                        $arr = [$kj[2], $kj[3], $kj[4]];
                    }
                }
            }
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if ($arr[0] == $cons[0] & $arr[1] == $cons[1] & $arr[2] == $cons[2] && $cc == 3) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case "3字和数":
            switch ($c['cm']) {
                case '前三':
                    $arr = $kj[0] + $kj[1] + $kj[2];
                    break;
                case '中三':
                    $arr = $kj[1] + $kj[2] + $kj[3];
                    break;
                case '后三':
                    $arr = $kj[2] + $kj[3] + $kj[4];
                    break;
            }
            if (strpos('[和单和双]', $p['name']) !== false) {
                $tmp = danshuang($arr);
                if (strpos($p['name'], $tmp)) {
                    $v = 1;
                } else {
                    $v = 0;
                }
            } else {
                if (strpos('[和大和小]', $p['name']) !== false) {
                    if ($arr >= 14 && $p['name'] == '和大' || $arr <= 13 & $p['name'] == '和小') {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                } else {
                    if (strpos('[和尾大和尾小]', $p['name']) !== false) {
                        $tmp = daxiaow($arr % 10);
                        if (strpos($p['name'], $tmp)) {
                            $v = 1;
                        } else {
                            $v = 0;
                        }
                    }
                }
            }
            break;
        case '总和龙虎':
            $ma = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4];
            switch ($p['name']) {
                case '总和单':
                case '总和双':
                    strpos($p['name'], danshuang($ma)) !== false ? $v = 1 : ($v = 0);
                    break;
                case '总和大':
                    $ma >= 23 ? $v = 1 : ($v = 0);
                    break;
                case '总和小':
                    $ma <= 22 ? $v = 1 : ($v = 0);
                    break;
                case '总和尾大':
                case '总和尾小':
                    strpos($p['name'], daxiaow($ma % 10)) !== false ? $v = 1 : ($v = 0);
                    break;
                case "龙":
                case "虎":
                case "和":
                    $tmp = longhuhe($kj[0], $kj[4]);
                    $tmp == $p['name'] && ($v = 1);
                    $tmp == '和' && $p['name'] != '和' && ($v = 2);
                    break;
            }
            break;
        case '组选3':
            if (strpos($p['name'], '前三') !== false) {
                $arr = [$kj[0], $kj[1], $kj[2]];
            } else {
                if (strpos($p['name'], '中三') !== false) {
                    $arr = [$kj[1], $kj[2], $kj[3]];
                } else {
                    if (strpos($p['name'], '后三') !== false) {
                        $arr = [$kj[2], $kj[3], $kj[4]];
                    }
                }
            }
            if (duizhi($arr[0], $arr[1], $arr[2]) != 1) {
                $v = 0;
                break;
            }
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if (in_array($arr[0], $cons) && in_array($arr[1], $cons) && in_array($arr[2], $cons)) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '组选6':
            if (strpos($p['name'], '前三') !== false) {
                $arr = [$kj[0], $kj[1], $kj[2]];
            } else {
                if (strpos($p['name'], '中三') !== false) {
                    $arr = [$kj[1], $kj[2], $kj[3]];
                } else {
                    if (strpos($p['name'], '后三') !== false) {
                        $arr = [$kj[2], $kj[3], $kj[4]];
                    }
                }
            }
            if (duizhi($arr[0], $arr[1], $arr[2]) == 1 | baozhi($arr[0], $arr[1], $arr[2]) == 1) {
                $v = 0;
                break;
            }
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if (in_array($arr[0], $cons) && in_array($arr[1], $cons) && in_array($arr[2], $cons)) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '跨度':
            switch ($c['cm']) {
                case '前三':
                    $k1 = abs($kj[0] - $kj[1]);
                    $k2 = abs($kj[0] - $kj[2]);
                    $k3 = abs($kj[1] - $kj[2]);
                    $k = max($k1, $k2, $k3);
                    break;
                case '中三':
                    $k1 = abs($kj[1] - $kj[2]);
                    $k2 = abs($kj[1] - $kj[3]);
                    $k3 = abs($kj[2] - $kj[3]);
                    $k = max($k1, $k2, $k3);
                    break;
                case '后三':
                    $k1 = abs($kj[2] - $kj[3]);
                    $k2 = abs($kj[2] - $kj[4]);
                    $k3 = abs($kj[3] - $kj[4]);
                    $k = max($k1, $k2, $k3);
                    break;
            }
            $k == $p['name'] ? $v = 1 : ($v = 0);
            break;
        case '前中后三':
            switch ($c['cm']) {
                case '前三':
                    $k1 = $kj[0];
                    $k2 = $kj[1];
                    $k3 = $kj[2];
                    break;
                case '中三':
                    $k1 = $kj[1];
                    $k2 = $kj[2];
                    $k3 = $kj[3];
                    break;
                case '后三':
                    $k1 = $kj[2];
                    $k2 = $kj[3];
                    $k3 = $kj[4];
                    break;
            }
            switch ($p['name']) {
                case '豹子':
                    $vv = baozhi($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
                case '顺子':
                    $vv = shunzhi($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
                case '对子':
                    $vv = duizhi($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
                case '半顺':
                    $vv = banshun($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
                case '杂六':
                    $vv = zaliu($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
            }
            break;
    }
    return [$v];
}
function moni_163($fenlei, $gid, $kj, $b, $s, $c, $p, $con, $ft)
{
    $v = 0;
    switch ($b) {
        case '番摊':
            switch ($c['name']) {
                case "双面":
                    if ($p['name'] == "单" && $ft % 2 == 1) {
                        $v = 1;
                    } else {
                        if ($p['name'] == "双" && $ft % 2 == 0) {
                            $v = 1;
                        } else {
                            if ($p['name'] == "大" && $ft > 2) {
                                $v = 1;
                            } else {
                                if ($p['name'] == "小" && $ft < 3) {
                                    $v = 1;
                                }
                            }
                        }
                    }
                    break;
                case "番":
                    $ft . "番" == $p['name'] ? $v = 1 : ($v = 0);
                    break;
                case "念":
                    $ps = explode('念', $p["name"]);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if ($ps[1] == $ft) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                case "角":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case "正":
                    $ps = str_replace('正', '', $p['name']);
                    if ($ps > 2) {
                        $psdui = $ps - 2;
                    } else {
                        $psdui = $ps + 2;
                    }
                    if ($ps == $ft) {
                        $v = 1;
                    } else {
                        if ($psdui == $ft) {
                            $v = 0;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
                case "中":
                    if (strpos($p['name'], $ft . "") !== false) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                    break;
                case '加':
                    $ps = explode('加', $p['name']);
                    if ($ps[0] == $ft) {
                        $v = 1;
                    } else {
                        if (strpos($ps[1], $ft . "") !== false) {
                            $v = 2;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
                default:
                    if ($p['znum1'] == $ft) {
                        $v = 0;
                    } else {
                        if (strpos($p['name'], $ft . "") !== false) {
                            $v = 1;
                        } else {
                            $v = 2;
                        }
                    }
                    break;
            }
            break;
        case '1~3':
            $ma = $kj[$c['mtype']];
            switch ($p['ztype']) {
                case "码":
                    $ma == $p['name'] ? $v = 1 : ($v = 0);
                    break;
                case "单双":
                    strpos($p['name'], danshuang($ma)) !== false ? $v = 1 : ($v = 0);
                    break;
                case "大小":
                    if ($p['name'] == "大" && $ma > 5) {
                        $v = 1;
                    } else {
                        if ($p['name'] == "小" && $ma < 5) {
                            $v = 1;
                        } else {
                            $v = 0;
                        }
                    }
                    break;
            }
            break;
        case '1字组合':
            $arr = $kj;
            if (in_array($p['name'], $arr)) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '2字组合':
            $arr = [];
            $arr = [$kj[0], $kj[1], $kj[2]];
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if (in_array($cons[0], $arr) && in_array($cons[1], $arr) && $cc == 2) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '2字定位':
            $pnames = str_replace("定位","",$p['name']);
            switch ($pnames) {
                case '百十':
                    $arr = [$kj[2], $kj[3]];
                    break;
                case '百个':
                    $arr = [$kj[2], $kj[4]];
                    break;
                case '十个':
                    $arr = [$kj[3], $kj[4]];
                    break;
            }
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if ($cons[0] == $arr[0] && $cons[1] == $arr[1] && $cc == 2) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '2字和数':
            switch ($c['cm']) {
                case '百十':
                    $arr = $kj[2] + $kj[3];
                    break;
                case '百个':
                    $arr = $kj[2] + $kj[4];
                    break;
                case '十个':
                    $arr = $kj[3] + $kj[4];
                    break;
            }
            if (strpos('[单双]', $p['name'])) {
                $p['name'] == danshuang($arr) ? $v = 1 : ($v = 0);
            } else {
                $tmp = daxiaow($arr % 10);
                strpos($p['name'], $tmp) !== false ? $v = 1 : ($v = 0);
            }
            break;
        case '3字组合':
            $arr = $kj;
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if (in_array($cons[0], $arr) && in_array($cons[1], $arr) && in_array($cons[2], $arr) && $cc == 3) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '3字定位':
            $arr = $kj;
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if ($arr[0] == $cons[0] & $arr[1] == $cons[1] & $arr[2] == $cons[2] && $cc == 3) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '总和龙虎':
            $ma = $kj[0] + $kj[1] + $kj[2];
            switch ($p['name']) {
                case '总和单':
                case '总和双':
                    strpos($p['name'], danshuang($ma)) !== false ? $v = 1 : ($v = 0);
                    $ma == 14 && $p['name'] == "总和双" && ($v = 2);
                    $ma == 13 && $p['name'] == "总和单" && ($v = 2);
                    break;
                case '总和大':
                    $ma > 14 ? $v = 1 : ($v = 0);
                    $ma == 14 && ($v = 2);
                    break;
                case '总和小':
                    $ma < 13 ? $v = 1 : ($v = 0);
                    $ma == 13 && ($v = 2);
                    break;
                case '总和尾大':
                case '总和尾小':
                    strpos($p['name'], daxiaow($ma % 10)) !== false ? $v = 1 : ($v = 0);
                    break;
                case "龙":
                case "虎":
                case "和":
                    $tmp = longhuhe($kj[0], $kj[2]);
                    $tmp == $p['name'] ? $v = 1 : ($v = 0);
                    $tmp == '和' && $p['name'] != '和' && ($v = 2);
                    break;
                case "极大":
                    $ma >= 22 && ($v = 1);
                    break;
                case "极小":
                    $ma <= 5 && ($v = 1);
                    break;
                case '总大单':
                    $tmp = danshuang($ma);
                    ($tmp == "单" && $ma > 14) && ($v = 1);
                    break;
                case '总大双':
                    $tmp = danshuang($ma);
                    ($tmp == "双" && $ma > 14) && ($v = 1);
                    ($tmp == "双" && $ma == 14) && ($v = 2);
                    break;
                case '总小单':
                    $tmp = danshuang($ma);
                    ($tmp == "单" && $ma < 13) && ($v = 1);
                    ($tmp == "单" && $ma == 13) && ($v = 2);
                    break;
                case '总小双':
                    $tmp = danshuang($ma);
                    ($tmp == "双" && $ma < 13) && ($v = 1);
                    break;
                default:
                    $ma == $p['name'] && ($v = 1);
                    break;
            }
            break;
        case '组选3':
            $arr = $kj;
            if (duizhi($arr[0], $arr[1], $arr[2]) != 1) {
                $v = 0;
                break;
            }
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if (in_array($arr[0], $cons) && in_array($arr[1], $cons) && in_array($arr[2], $cons)) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '组选6':
            $arr = $kj;
            if (duizhi($arr[0], $arr[1], $arr[2]) == 1 | baozhi($arr[0], $arr[1], $arr[2]) == 1) {
                $v = 0;
                break;
            }
            $cons = explode('-', $con);
            $cons = array_unique($cons);
            $cc = count($cons);
            if (in_array($arr[0], $cons) && in_array($arr[1], $cons) && in_array($arr[2], $cons)) {
                $v = 1;
            } else {
                $v = 0;
            }
            break;
        case '跨度':
            $k1 = abs($kj[0] - $kj[1]);
            $k2 = abs($kj[0] - $kj[2]);
            $k3 = abs($kj[1] - $kj[2]);
            $k = max($k1, $k2, $k3);
            $k == $p['name'] ? $v = 1 : ($v = 0);
            break;
        case '前三':
            $k1 = $kj[0];
            $k2 = $kj[1];
            $k3 = $kj[2];
            switch ($p['name']) {
                case '豹子':
                    $vv = baozhi($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
                case '顺子':
                    $vv = shunzhi($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
                case '对子':
                    $vv = duizhi($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
                case '半顺':
                    $vv = banshun($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
                case '杂六':
                    $vv = zaliu($k1, $k2, $k3);
                    $vv == 1 ? $v = 1 : ($v = 0);
                    break;
            }
            break;
    }
    return [$v];
}
function suiji($fenlei, $gid, $qishu)
{
    switch ($fenlei) {
        case '101':
            return suijikj($gid, $qishu, 5);
            break;
        case '107':
            return suijikj($gid, $qishu, 10);
            break;
        case '151':
            return suijikj($gid, $qishu, 3);
            break;
        case '161':
            return suijikj($gid, $qishu, 20);
            break;
        case '163':
            return suijikj($gid, $qishu, 4);
            break;
        case '121':
            return suijikj($gid, $qishu, 6);
            break;
        case '103':
            return suijikj($gid, $qishu, 8);
            break;
        case '100':
            return suijikj($gid, $qishu, 7);
            break;
    }
}
function suijikj($gid, $qishu, $mnum)
{
    $m = array();
    switch ($mnum) {
        case 4:
            $arr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
            $m[0] = $arr[rand(0, 9)];
            $m[1] = $arr[rand(0, 9)];
            $m[2] = $arr[rand(0, 9)];
            break;
        case 3:
            $arr = [1, 2, 3, 4, 5, 6];
            $m[0] = $arr[rand(0, 5)];
            $m[1] = $arr[rand(0, 5)];
            $m[2] = $arr[rand(0, 5)];
            break;
        case 5:
            $arr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
            $m[0] = $arr[rand(0, 9)];
            $m[1] = $arr[rand(0, 9)];
            $m[2] = $arr[rand(0, 9)];
            $m[3] = $arr[rand(0, 9)];
            $m[4] = $arr[rand(0, 9)];
            break;
        case 8:
            $arr = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20"];
            $m[0] = $arr[rand(0, 19)];
            for ($i = 1; $i < 8; $i++) {
                $m[$i] = randm($m, $arr, $mnum, 20);
            }
            break;
        case 6:
            $arr = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"];
            $m[0] = $arr[rand(0, 10)];
            for ($i = 1; $i < 5; $i++) {
                $m[$i] = randm($m, $arr, $mnum, 11);
            }
            break;
        case 20:
            for ($i = 1; $i <= 80; $i++) {
                if ($i < 10) {
                    $arr[$i - 1] = '0' . $i;
                } else {
                    $arr[$i - 1] = $i;
                }
            }
            $m[0] = $arr[rand(0, 79)];
            for ($i = 1; $i < 20; $i++) {
                $m[$i] = randm($m, $arr, $mnum, 80);
            }
            break;
        case 7:
            for ($i = 1; $i <= 49; $i++) {
                if ($i < 10) {
                    $arr[$i - 1] = '0' . $i;
                } else {
                    $arr[$i - 1] = $i;
                }
            }
            $m[0] = $arr[rand(0, 48)];
            for ($i = 1; $i < 7; $i++) {
                $m[$i] = randm($m, $arr, $mnum, 49);
            }
            break;
        case 10:
            $arr = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10"];
            $m[0] = $arr[rand(0, 9)];
            for ($i = 1; $i < 10; $i++) {
                $m[$i] = randm($m, $arr, $mnum, 10);
            }
            break;
    }
    return $m;
}