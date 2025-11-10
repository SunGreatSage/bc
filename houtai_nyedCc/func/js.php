<?php
include_once('jsfunc.php');
function kjjs($qishu, $gid, $fenlei)
{
    global $tsql, $tb_game;
    $tsql->query("select fenlei from `$tb_game` where gid='$gid'");
    $tsql->next_record();
    if ($fenlei==163 ) {
        return call_user_func("kjjs_101", $qishu, $gid, $tsql->f('fenlei'));
    } else {
        return call_user_func("kjjs_" . $tsql->f('fenlei'), $qishu, $gid, $tsql->f('fenlei'));
    }
}
function kjjs_100($qishu, $gid, $fenlei)
{
    global $tb_lib, $tb_user, $tb_kj, $tb_z, $tb_bclass, $tb_sclass, $tb_class, $tb_play, $tb_config, $tb_game;
    global $tsql, $psql, $fsql, $msql;
    $whi = " gid='$gid'  and qishu='$qishu' ";
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('m1') == '')
        return "未开奖";
    $fsql->query("select js from  `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('js') == 1)
        return '该期数已结算过了！';
    //$fsql->query("update `$tb_kj` set baostatus=0 where $whi");     
    $rs    = $fsql->arr("select ma,maxpc from `$tb_config`", 0);
    $marrs = json_decode($rs[0][0], true);
    foreach ($marrs as $v) {
        foreach ($v as $k1 => $v1) {
            $marr[$k1] = explode(',', $v1);
        }
    }
    $maxpc = $rs[0][1];
    $fsql->query("delete from `$tb_z` where $whi");
    $fsql->query("select * from `$tb_game` where gid='$gid'");
    $fsql->next_record();
    $ftype = json_decode($fsql->f('ftype'), true);
    $mtype = json_decode($fsql->f('mtype'), true);
    $ztype = json_decode($fsql->f('ztype'), true);
    $cs = json_decode($fsql->f('cs'),true);
    $mnum  = $fsql->f('mnum');
    $cf    = count($ftype);
    $cm    = count($mtype);
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    $zf = 0;
    $hob = 0;
    $lab=0;
    $lvb=0;
    for ($i = 0; $i < $mnum; $i++) {
        $kj[$i] = $fsql->f('m' . ($i + 1));
        $sx[$i] = sx_100($fsql->f('m' . ($i + 1)), $marr);
        $ws[$i] = $fsql->f('m' . ($i + 1)) % 10;
        $ws[$i] .= "尾";
        $zf += $kj[$i];
        if($i<$mnum-1){
            if(in_array($kj[$i], $marr['紅'])) $hob+=1;
            if(in_array($kj[$i], $marr['藍'])) $lab+=1;
            if(in_array($kj[$i], $marr['綠'])) $lvb+=1;
        }else{
            if(in_array($kj[$i], $marr['紅'])) $hob+=1.5;
            if(in_array($kj[$i], $marr['藍'])) $lab+=1.5;
            if(in_array($kj[$i], $marr['綠'])) $lvb+=1.5;
        }
    }
    //print_r($sx);
    $zheng = array(
        $fsql->f('m1'),
        $fsql->f('m2'),
        $fsql->f('m3'),
        $fsql->f('m4'),
        $fsql->f('m5'),
        $fsql->f('m6')
    );
  
    if($cs['ft']==1){
        $ftm = explode(',', $cs['ftnum']);
        $ft=0;
        foreach($ftm as $k => $v){
           $ft += $kj[$v-1];
        }
        $ft = $ft%4==0 ? 4 : $ft%4;
    }
    //print_r($marrs);
    //print_r($marr);return;
    unset($marrs);
    //z=5 maxpc
    //z=7 无效注单
    $fsql->query("update `$tb_lib` set kk=1,z=9,prize=0 where $whi  and z!=7");
    $fsql->query("select * from `$tb_class` where gid='$gid' and bid in(select bid from `$tb_bclass` where gid='$gid' and ifok=1) order by bid,sid,xsort");
    while ($fsql->next_record()) {
        $bname    = transb8('name', $fsql->f('bid'), $gid);
        $sname    = transs8('name', $fsql->f('sid'), $gid);
        $cname    = $fsql->f('name');
        $tmpmtype = $fsql->f('mtype');
        if ($tmpmtype == 0)
            $tmpmtype = 6;
        else
            $tmpmtype -= 1;
        $tsql->query("select bid,sid,cid,pid,name,ztype,znum1,znum2 from `$tb_play` where gid='$gid' and  cid='" . $fsql->f('cid') . "'");
        while ($tsql->next_record()) {
            $sql1     = "update `$tb_lib` set kk=1,z='1' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql0     = "update `$tb_lib` set kk=1,z='0' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql2     = "update `$tb_lib` set kk=1,z='2' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sqlz     = "insert into `$tb_z` set gid='$gid',qishu='$qishu',pid='" . $tsql->f('pid') . "'";
            $tmpztype = $ztype[$tsql->f('ztype')];
            $pname    = $tsql->f('name');
            switch ($tmpztype) {
                case "番摊":
                     if($cs['ft']!=1){
                        break;
                     }
                     switch ($cname) {
                         case '双面':
                             if($pname=="单" && $ft%2==1){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="双" && $ft%2==0){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="大" && $ft>2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="小" && $ft<=2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                             break;
                        case "番":
                             if($pname==$ft.'番'){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "念":
                             $ps = explode('念', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($ps[1]==$ft){
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "角":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "正":
                             $ps = str_replace('正', '' , $pname);
                             if($ps>2){
                                 $psdui = $ps-2;
                             }else{
                                 $psdui = $ps+2;
                             }
                             if($ps==$ft){
                                 $psql->query($sql1);
                                 $psql->query($sqlz);
                             }else if($psdui==$ft){                                
                                 $psql->query($sql0);
                             }else{
                                 $psql->query($sql2);
                             }
                        break;
                        case "中":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case '加':
                             $ps = explode('加', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if(strpos($ps[1],$ft."")!==false){     
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                            break;
                        default:
                             if($tsql->f('znum1')==$ft){
                                $psql->query($sql0);
                             }else if(strpos($pname,$ft."")!==false){                                
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql2);
                             }
                            break;
                     }
                break;
                case '码':
                case '碼':
                    if ($bname == '正码' | $bname == '正碼') {
                        $arr = $zheng;
                    } else {
                        $arr = array(
                            $kj[$tmpmtype]
                        );
                    }
                    if (in_array($pname, $arr)) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '单双':
                case '單雙':
                    if ($cname == '总单双' | $cname == '總單雙') {
                        $ma = $zf;
                        if (strpos("[$pname]", danshuang_100($ma))) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else {
                        $ma = $kj[$tmpmtype];
                        if ($ma == 49) {
                            $psql->query($sql2);
                        } else if (strpos("[$pname]", danshuang_100($ma))) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }
                    break;
                case '大小':
                    $zflag = 0;
                    if ($cname == '总大小' | $cname == '總大小') {
                        if (($pname == '总大' | $pname == '總大') & $zf >= 175) {
                            $zflag = 1;
                        } else if (($pname == '总小' | $pname == '總小') & $zf < 175) {
                            $zflag = 1;
                        }
                    } else {
                        $ma = $kj[$tmpmtype];
                        if ($ma == 49) {
                            $zflag = 2;
                        } else if ($pname == '大' & $ma >= 25) {
                            $zflag = 1;
                        } else if ($pname == '小' & $ma <= 24) {
                            $zflag = 1;
                        }
                    }
                    if ($zflag == 2) {
                        $psql->query($sql2);
                    } else if ($zflag) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '合单双':
                case '合單雙':
                    $heshu = heshu($kj[$tmpmtype]);
                    if ($kj[$tmpmtype] == 49) {
                        $psql->query($sql2);
                    } else if (strpos($pname, danshuang_100($heshu))) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '尾大小':
                    $zflag = 0;
                    $ma    = $kj[$tmpmtype];
                    if($cname=="合尾大小"){
                        $hs = heshu($ma);
                        if ($ma == 25) {
                            $psql->query($sql2);
                        } else if (strpos($pname, daxiaow($hs % 10))) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }else{
                        if ($ma == 49) {
                            $psql->query($sql2);
                        } else if (strpos($pname, daxiaow($ma % 10))) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }
                    break;
                case '合大小':
                    $zflag = 0;
                    $ma    = $kj[$tmpmtype];
                    $hs = heshu($ma);
                    if ($ma == 49) {
                        $psql->query($sql2);
                    } else if ($pname==heshudaxiao_100($hs)) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    //file_put_contents('1.txt', "\r\n".$ma.'a'.$pname.','.heshudaxiao_100($hs),FILE_APPEND);
                    break;

                case '家野':
                    if ($kj[$tmpmtype] == 49) {
                        $psql->query($sql2);
                    } else if (in_array($kj[$tmpmtype], $marr[$pname])) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '波色':
                    if (in_array($kj[$tmpmtype], $marr[$pname])) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '半波':
                    if ($kj[$tmpmtype] == 49) {
                        $psql->query($sql2);
                    } else if (in_array($kj[$tmpmtype], $marr[$pname])) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '五行':
                    if (in_array($kj[$tmpmtype], $marr[$pname])) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '生肖':
                    if ($bname == '一肖') {
                        $arr = $kj;
                    }else if ($bname == '正肖') {
                        $arr = $zheng;
                    } else {
                        $arr = array(
                            $kj[6]
                        );
                    }
                    $zflag = 0;
                    foreach ($arr as $v) {
                        if (in_array($v, $marr[$pname])) {
                            $zflag += 1;
                        }
                    }
                    if ($zflag >= 1) {
                        if($bname=="正肖"){
                            $psql->query("update `$tb_lib` set kk=1,z='5',prize=((peilv1-1)*$zflag*je+je) where $whi and pid='" . $tsql->f('pid') . "'  and z!=7");
                        
                        }else{
                            $psql->query($sql1);
                        }                        
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '尾数':
                case '尾數':
                    if($bname=="特头尾"){
                        $arr   = [$kj[6]];
                    }else{
                        $arr   = $kj;
                    }
                    
                    $zflag = 0;
                    foreach ($arr as $v) {
                        if (in_array($v, $marr[$pname])) {
                            $zflag = 1;
                            break;
                        }
                    }
                    if ($zflag == 1) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case "其他":
                    if($bname=="总肖七色波"){
                        $zx = array_count_values($sx);
                        $czx = count($zx);
                        if($cname=="总肖"){
                            if($tsql->f('znum1')==$czx){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            }else{
                                $psql->query($sql0);
                            }   
                        }else if($cname=="总肖单双"){
                            if(strpos($pname,danshuang($czx))!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            }else{
                                $psql->query($sql0);
                            }     
                        }else{
                                 
                            if($pname=="和局" && (($hob==3 && $lab==3 && $lvb==1.5) || ($hob==3 && $lvb==3 && $lab==1.5) || ($lvb==3 && $lab==3 && $hob==1.5)) ){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            }else if(($hob==3 && $lab==3 && $lvb==1.5) || ($hob==3 && $lvb==3 && $lab==1.5) || ($lvb==3 && $lab==3 && $hob==1.5)){
                                $psql->query($sql2);
                            }else if($pname=="红波" && $hob==max($hob,$lab,$lvb)){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            }else if($pname=="蓝波" && $lab==max($hob,$lab,$lvb)){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            }else if($pname=="绿波" && $lvb==max($hob,$lab,$lvb)){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            }else{
                                $psql->query($sql0);
                            } 
                        }
                    }else if($cname=="特头数"){
                        if($pname==floor($kj[$tmpmtype]/10)."头"){
                             $psql->query($sql1);
                             $psql->query($sqlz);
                        }else{
                             $psql->query($sql0);
                        }
                    }else if($cname=="特尾数"){
                        if($pname==($kj[$tmpmtype]%10)."尾"){
                             $psql->query($sql1);
                             $psql->query($sqlz);
                        }else{
                             $psql->query($sql0);
                        }
                    }
                break;    
                default:
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "' and z!=7", 1);
                    $cr = count($rs);
                    switch ($tmpztype) {
                        case '多肖':
                            if ($bname == '特肖连' | $bname == '合肖') {
                                if($kj[6]==49 && $tsql->f('znum1')==6){
                                    $psql->query("update `$tb_lib` set kk=1,z='2' where $whi and  pid='" . $tsql->f('pid') . "' and z!=7");
                                    break;
                                }
                                for ($k = 0; $k < $cr; $k++) {
                                    $zflag = 0;
                                    $con   = explode('-', $rs[$k]['content']);
                                    $con = array_unique($con);
                                    foreach ($con as $v) {
                                        if (in_array($kj[6], $marr[$v])) {
                                            $zflag = 1;
                                            break;
                                        }
                                    }
                                    if($tsql->f('znum2')<0){
                                        if ($zflag == 0) {
                                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                                        } else {
                                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                        }
                                    }else{
                                        if ($zflag == 1) {
                                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                                        } else {
                                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                        }   
                                    }
                                }
                            } else {
                                for ($k = 0; $k < $cr; $k++) {
                                    $zflag = 0;
                                    $con   = explode('-', $rs[$k]['content']);
                                    $con = array_unique($con);
                                    foreach ($con as $v) {
                                        if (in_array($v, $sx)) {
                                            $zflag++;
                                        }
                                    }
                                    if ($tsql->f('znum2') >= 0) {
                                        if ($zflag == $tsql->f('znum1')) {
                                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                                        } else {
                                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                        }
                                    } else {
                                        if ($zflag == 0) {
                                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                                        } else {
                                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                        }
                                    }
                                }
                            }
                            break;
                        case '多尾':
                            if ($bname == '特尾连' | $bname == '特尾連') {
                                for ($k = 0; $k < $cr; $k++) {
                                    $zflag = 0;
                                    $con   = explode('-', $rs[$k]['content']);
                                    $con = array_unique($con);
                                    foreach ($con as $v) {
                                        if ($v == $ws[6]) {
                                            $zflag = 1;
                                            break;
                                        }
                                    }
                                    if ($zflag == 1) {
                                        $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                                    } else {
                                        $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                    }
                                }
                            }  else {
                                for ($k = 0; $k < $cr; $k++) {
                                    $zflag = 0;
                                    $con   = explode('-', $rs[$k]['content']);
                                    $con = array_unique($con);
                                    foreach ($con as $v) {
                                        if (in_array($v, $ws)) {
                                            $zflag++;
                                        }
                                    }
                                    if ($tsql->f('znum2') >= 0) {
                                        if ($zflag == $tsql->f('znum1')) {
                                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                                        } else {
                                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                        }
                                    } else {
                                        if ($zflag == 0) {
                                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                                        } else {
                                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                        }
                                    }
                                }
                            }
                            break;
                        case '多不中':
                            for ($k = 0; $k < $cr; $k++) {
                                $zflag = 1;
                                $con   = explode('-', $rs[$k]['content']);
                                $con   = array_unique($con);
                                $cc    = count($con);
                                if ($cc != $tsql->f('znum1')) {
                                    $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                    continue;
                                }
                                foreach ($con as $v) {
                                    if (in_array($v, $kj)) {
                                        $zflag = 0;
                                        break;
                                    }
                                }
                                if ($zflag == 1) {
                                    $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                                } else {
                                    $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                }
                            }
                            break;
                        case '过关':
                        case '過關':
                            for ($k = 0; $k < $cr; $k++) {
                                $bz    = json_decode($rs[$k]['bz'], true);
                                $zflag = 0;
                                $xflag = 0;
                                foreach ($bz as $v) {
                                    $rmtype = $psql->arr("select mtype from `$tb_class` where gid='$gid' and sid='" . $v['sid'] . "' and cid='" . $v['cid'] . "'", 1);
                                    $rpname = $psql->arr("select name,ztype from `$tb_play` where gid='$gid' and sid='" . $v['sid'] . "' and cid='" . $v['cid'] . "' and pid='" . $v['pid'] . "'", 1);
                                    if (in_array($kj[$rmtype[0]['mtype'] - 1], $marr[$rpname[0]['name']])) {
                                        $zflag++;
                                    }
                                    if ($kj[$rmtype[0]['mtype'] -1 ] == 25 && ($rpname[0]['name']=='合尾大' || $rpname[0]['name']=='合尾大')) {
                                        $xflag = 2;
                                    }
                                    if ($kj[$rmtype[0]['mtype'] -1 ] == 49 && $rpname[0]['name']!='合尾大' && $rpname[0]['name']!='合尾大' && $rpname[0]['ztype']==1) {
                                        $xflag = 2;
                                    }
                                }
                                if ($xflag == 2) {
                                    $psql->query("update `$tb_lib` set kk=1,z='2' where id='" . $rs[$k]['id'] . "'");
                                } else if ($zflag == count($bz) & $zflag > 0) {
                                    $psql->query("select peilv1*je where  id='" . $rs[$k]['id'] . "'");
                                    $psql->next_record();
                                    if ($psql->f(0) < $maxpc | $rs[$k]['xtype'] == 2) {
                                        $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                                    } else {
                                        $psql->query("update `$tb_lib` set kk=1,z='5',prize='$maxpc' where id='" . $rs[$k]['id'] . "'");
                                    }
                                } else {
                                    $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                }
                            }
                            break;
                        case '多码':
                        case '多碼':
                            for ($k = 0; $k < $cr; $k++) {
                                $zflag = 0;
                                $con   = explode('-', $rs[$k]['content']);
                                $con   = array_unique($con);
                                $cc    = count($con);
                                if ($cc != $tsql->f('znum1') & $cc != $tsql->f('znum2')) {
                                    $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                                    continue;
                                }
                                if ($pname == '特串') {
                                    if ((in_array($con[0], $zheng) & $con[1] == $kj[6]) | (in_array($con[1], $zheng) & $con[0] == $kj[6])) {
                                        $zflag = 1;
                                    }
                                } else if ($pname == '二中特') {
                                    if (in_array($con[0], $zheng) & in_array($con[1], $zheng) & $con[0] != $con[1]) {
                                        $zflag = 1;
                                    } else if ((($con[0] == $kj[6] & in_array($con[1], $zheng))|($con[1] == $kj[6] & in_array($con[0], $zheng))) & $con[0] != $con[1]) {
                                        $zflag = 3;
                                    }
                                } else {
                                    $xt = 0;
                                    foreach ($con as $v) {
                                        if (in_array($v, $zheng)) {
                                            $xt++;
                                        }
                                    }
                                    if ($pname == '三中二') {
                                        if ($xt == 2) {
                                            $zflag = 1;
                                        } else if ($xt == 3) {
                                            $zflag = 3;
                                        }
                                    } else if ($xt == $tsql->f('znum1')) {
                                        $zflag = 1;
                                    }
                                }
                                $psql->query("update `$tb_lib` set kk=1,z='$zflag' where id='" . $rs[$k]['id'] . "'");
                                
                            }
                            break;
                    }
                    break;
                    
            }
        }
    }
    
    $fsql->query("update `$tb_kj` set js=1 where $whi");
    return "结算成功888";
}
function kjjs_103($qishu, $gid, $fenlei)
{
    global $tb_lib, $tb_user, $tb_kj, $tb_z, $tb_bclass, $tb_sclass, $tb_class, $tb_play, $tb_game;
    global $tsql, $psql, $fsql, $msql;
    $whi = " gid='$gid'  and qishu='$qishu' ";
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('m1') == '')
        return "未开奖";
    $fsql->query("select js from  `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('js') == 1)
        return '该期数已结算过了！';
    $fsql->query("delete from `$tb_z` where $whi");
    $fsql->query("select * from `$tb_game` where gid='$gid'");
    $fsql->next_record();
    $ftype = json_decode($fsql->f('ftype'), true);
    $mtype = json_decode($fsql->f('mtype'), true);
    $ztype = json_decode($fsql->f('ztype'), true);
    $cs = json_decode($fsql->f('cs'), true);
    $mnum  = $fsql->f('mnum');
    $cf    = count($ftype);
    $cm    = count($mtype);
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    $zf = 0;
    for ($i = 0; $i < $mnum; $i++) {
        $kj[$i] = $fsql->f('m' . ($i + 1));
        $zf += $kj[$i];
    }
    if($cs['ft']==1){
        $ftm = explode(',', $cs['ftnum']);
        $ft=0;
        foreach($ftm as $k => $v){
           $ft += $kj[$v-1];
        }
        $ft = $ft%4==0 ? 4 : $ft%4;
    }
    $fsql->query("update `$tb_lib` set kk=1,z=9 where $whi  and z!=7");
    $fsql->query("select * from `$tb_class` where gid='$gid' and bid in(select bid from `$tb_bclass` where gid='$gid' and ifok=1) order by bid,sid,xsort");
    while ($fsql->next_record()) {
        $bname    = transb8('name', $fsql->f('bid'), $gid);
        $sname    = transs8('name', $fsql->f('sid'), $gid);
        $cname    = $fsql->f('name');
        $tmpmtype = $fsql->f('mtype');
        $tsql->query("select bid,sid,cid,pid,name,ztype,znum1,znum2 from `$tb_play` where gid='$gid' and  cid='" . $fsql->f('cid') . "'");
        while ($tsql->next_record()) {
            $sql1     = "update `$tb_lib` set kk=1,z='1' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql0     = "update `$tb_lib` set kk=1,z='0' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql2     = "update `$tb_lib` set kk=1,z='2' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sqlz     = "insert into `$tb_z` set gid='$gid',qishu='$qishu',pid='" . $tsql->f('pid') . "'";
            $tmpztype = $ztype[$tsql->f('ztype')];
            $pname    = $tsql->f('name');
            switch ($tmpztype) {
                case "番摊":
                     if($cs['ft']!=1){
                        break;
                     }
                     switch ($cname) {
                         case '双面':
                             if($pname=="单" && $ft%2==1){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="双" && $ft%2==0){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="大" && $ft>2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="小" && $ft<=2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                             break;
                        case "番":
                             if($pname==$ft.'番'){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "念":
                             $ps = explode('念', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($ps[1]==$ft){
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "角":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "正":
                             $ps = str_replace('正', '' , $pname);
                             if($ps>2){
                                 $psdui = $ps-2;
                             }else{
                                 $psdui = $ps+2;
                             }
                             if($ps==$ft){
                                 $psql->query($sql1);
                                 $psql->query($sqlz);
                             }else if($psdui==$ft){                                
                                 $psql->query($sql0);
                             }else{
                                 $psql->query($sql2);
                             }
                        break;
                        case "中":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case '加':
                             $ps = explode('加', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if(strpos($ps[1],$ft."")!==false){     
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                            break;
                        default:
                             if($tsql->f('znum1')==$ft){
                                $psql->query($sql0);
                             }else if(strpos($pname,$ft."")!==false){                                
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql2);
                             }
                            break;
                     }
                break;
                case '码':
                    if ($bname == '正码') {
                        $arr = $kj;
                    } else {
                        $arr = array(
                            $kj[$tmpmtype]
                        );
                    }
                    if (in_array($pname, $arr)) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '单双':
                    if ($cname == '总和单双') {
                        $ma = $zf;
                    } else {
                        $ma = $kj[$tmpmtype];
                    }
                    if (strpos("[$pname]", danshuang($ma))) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '大小':
                    $zflag = 0;
                    if ($cname == '总和大小') {
                        if ($pname == '总和大' & $zf > 84) {
                            $zflag = 1;
                        } else if ($pname == '总和小' & $zf < 84) {
                            $zflag = 1;
                        } else if ($zf == 84) {
                            $zflag = 2;
                        }
                    } else {
                        $ma = $kj[$tmpmtype];
                        if ($pname == '大' & $ma >= 11) {
                            $zflag = 1;
                        } else if ($pname == '小' & $ma <= 10) {
                            $zflag = 1;
                        }
                    }
                    if ($zflag == 2) {
                        $psql->query($sql2);
                    } else if ($zflag) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '合单双':
                    $heshu = heshu($kj[$tmpmtype]);
                    if (strpos($pname, danshuang($heshu))) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '尾大小':
                    $zflag = 0;
                    if ($cname == '总尾大小') {
                        $ma = $zf;
                    } else {
                        $ma = $kj[$tmpmtype];
                    }
                    if (strpos($pname, daxiaow($ma % 10))) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '龙虎':
                    $ma = longhuhe($kj[0], $kj[$mnum - 1]);
                    if ($ma == $pname) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '四季':
                    if (siji($kj[$tmpmtype]) == $pname) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '五行':
                    if (wuhang($kj[$tmpmtype]) == $pname) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '方位':
                    if (fangwei($kj[$tmpmtype]) == $pname) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '中发白':
                    if (zhongfabai($kj[$tmpmtype]) == $pname) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '连码':
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "'  and z!=7", 1);
                    $cr = count($rs);
                    for ($k = 0; $k < $cr; $k++) {
                        $zflag = 0;
                        $con   = explode('-', $rs[$k]['content']);
                        $con   = array_unique($con);
                        $cc    = count($con);
                        if ($cc != $tsql->f('znum1')) {
                            $psql->query($sql0);
                            continue;
                        }
                        switch ($pname) {
                            case '选二任选':
                                if (in_array($con[0], $kj) & in_array($con[1], $kj)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '选二连组':
                                if (in_array($con[0], $kj)) {
                                    $keylm = -1;
                                    foreach ($kj as $klm => $vlm) {
                                        if ($con[0] == $vlm) {
                                            $keylm = $klm;
                                        }
                                    }
                                    if ($keylm > 0) {
                                        if ($kj[$keylm - 1] == $con[1] | $kj[$keylm + 1] == $con[1]) {
                                            $psql->query($sql1);
                                        } else {
                                            $psql->query($sql0);
                                        }
                                    } else {
                                        if ($kj[$keylm + 1] == $con[1]) {
                                            $psql->query($sql1);
                                        } else {
                                            $psql->query($sql0);
                                        }
                                    }
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '选二连直':
                                if (in_array($con[0], $kj)) {
                                    $keylm = -1;
                                    foreach ($kj as $klm => $vlm) {
                                        if ($con[0] == $vlm) {
                                            $keylm = $klm;
                                        }
                                    }
                                    if ($kj[$keylm + 1] == $con[1]) {
                                        $psql->query($sql1);
                                    } else {
                                        $psql->query($sql0);
                                    }
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '选三任选':
                                if (in_array($con[0], $kj) & in_array($con[1], $kj) & in_array($con[2], $kj)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '选三前组':
                                $arrlm = array(
                                    $kj[0],
                                    $kj[1],
                                    $kj[2]
                                );
                                if (in_array($con[0], $arrlm) & in_array($con[1], $arrlm) & in_array($con[2], $arrlm)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '选三前直':
                                if ($con[0] == $kj[0] & $con[1] == $kj[1] & $con[2] == $kj[2]) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '选四任选':
                                if (in_array($con[0], $kj) & in_array($con[1], $kj) & in_array($con[2], $kj) & in_array($con[3], $kj)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '选五任选':
                                if (in_array($con[0], $kj) & in_array($con[1], $kj) & in_array($con[2], $kj) & in_array($con[3], $kj) & in_array($con[4], $kj)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                        }
                    }
                    break;
            }
        }
    }
    
    $fsql->query("update `$tb_kj` set js=1 where $whi");
    return "结算成功888";
}
function kjjs_121($qishu, $gid, $fenlei)
{
    global $tb_lib, $tb_user, $tb_kj, $tb_z, $tb_bclass, $tb_sclass, $tb_class, $tb_play, $tb_game;
    global $tsql, $psql, $fsql, $msql;
    $whi = " gid='$gid'  and qishu='$qishu' ";
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('m1') == '')
        return "未开奖";
    $fsql->query("select js from  `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('js') == 1)
        return '该期数已结算过了！';
    $fsql->query("delete from `$tb_z` where $whi");
    $fsql->query("select * from `$tb_game` where gid='$gid'");
    $fsql->next_record();
    $ftype = json_decode($fsql->f('ftype'), true);
    $mtype = json_decode($fsql->f('mtype'), true);
    $ztype = json_decode($fsql->f('ztype'), true);
    $cs = json_decode($fsql->f('cs'), true);
    $mnum  = $fsql->f('mnum');
    $cf    = count($ftype);
    $cm    = count($mtype);
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    $zf = 0;
    for ($i = 0; $i < $mnum; $i++) {
        $kj[$i] = $fsql->f('m' . ($i + 1));
        $zf += $kj[$i];
    }
    if($cs['ft']==1){
        $ftm = explode(',', $cs['ftnum']);
        $ft=0;
        foreach($ftm as $k => $v){
           $ft += $kj[$v-1];
        }
        $ft = $ft%4==0 ? 4 : $ft%4;
    }
    $fsql->query("update `$tb_lib` set kk=1,z=9 where $whi  and z!=7");
    $fsql->query("select * from `$tb_class` where gid='$gid' and bid in(select bid from `$tb_bclass` where gid='$gid' and ifok=1) order by bid,sid,xsort");
    while ($fsql->next_record()) {
        $bname    = transb8('name', $fsql->f('bid'), $gid);
        $sname    = transs8('name', $fsql->f('sid'), $gid);
        $cname    = $fsql->f('name');
        $tmpmtype = $fsql->f('mtype');
        $tsql->query("select bid,sid,cid,pid,name,ztype,znum1,znum2 from `$tb_play` where gid='$gid' and  cid='" . $fsql->f('cid') . "'");
        while ($tsql->next_record()) {
            $sql1     = "update `$tb_lib` set kk=1,z='1' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql0     = "update `$tb_lib` set kk=1,z='0' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql2     = "update `$tb_lib` set kk=1,z='2' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sqlz     = "insert into `$tb_z` set gid='$gid',qishu='$qishu',pid='" . $tsql->f('pid') . "'";
            $tmpztype = $ztype[$tsql->f('ztype')];
            $pname    = $tsql->f('name');
            switch ($tmpztype) {
                case "番摊":
                     if($cs['ft']!=1){
                        break;
                     }
                     switch ($cname) {
                         case '双面':
                             if($pname=="单" && $ft%2==1){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="双" && $ft%2==0){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="大" && $ft>2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="小" && $ft<=2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                             break;
                        case "番":
                             if($pname==$ft.'番'){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "念":
                             $ps = explode('念', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($ps[1]==$ft){
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "角":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "正":
                             $ps = str_replace('正', '' , $pname);
                             if($ps>2){
                                 $psdui = $ps-2;
                             }else{
                                 $psdui = $ps+2;
                             }
                             if($ps==$ft){
                                 $psql->query($sql1);
                                 $psql->query($sqlz);
                             }else if($psdui==$ft){                                
                                 $psql->query($sql0);
                             }else{
                                 $psql->query($sql2);
                             }
                        break;
                        case "中":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case '加':
                             $ps = explode('加', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if(strpos($ps[1],$ft."")!==false){     
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                            break;
                        default:
                             if($tsql->f('znum1')==$ft){
                                $psql->query($sql0);
                             }else if(strpos($pname,$ft."")!==false){                                
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql2);
                             }
                            break;
                     }
                break;
                case '码':
                    if ($bname == '正码') {
                        $arr = $kj;
                    } else {
                        $arr = array(
                            $kj[$tmpmtype]
                        );
                    }
                    if (in_array($pname, $arr)) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '单双':
                    if ($cname == '总和单双') {
                        $ma = $zf;
                    } else {
                        $ma = $kj[$tmpmtype];
                    }
                    if (strpos("[$pname]", danshuang($ma))) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '大小':
                    $zflag = 0;
                    if ($cname == '总和大小') {
                        if ($pname == '总大' & $zf > 30) {
                            $zflag = 1;
                        } else if ($pname == '总小' & $zf < 30) {
                            $zflag = 1;
                        } else if ($zf == 30) {
                            $zflag = 2;
                        }
                    } else {
                        $ma = $kj[$tmpmtype];
                        if ($pname == '小' & $ma <= 5) {
                            $zflag = 1;
                        } else if ($pname == '大' & $ma <= 10 & $ma >= 6) {
                            $zflag = 1;
                        } else if ($zf == 11) {
                            $zflag = 2;
                        }
                    }
                    if ($zflag == 2) {
                        $psql->query($sql2);
                    } else if ($zflag) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '尾大小':
                    $zflag = 0;
                    if ($cname = '总尾大小') {
                        $ma = $zf;
                    } else {
                        $ma = $kj[$tmpmtype];
                    }
                    if (strpos($pname, daxiaow($ma % 10))) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '龙虎':
                    $ma = longhuhe($kj[0], $kj[$mnum - 1]);
                    if ($ma == $pname) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '连码':
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "'  and z!=7", 1);
                    $cr = count($rs);
                    for ($k = 0; $k < $cr; $k++) {
                        $zflag = 0;
                        $con   = explode('-', $rs[$k]['content']);
                        $con   = array_unique($con);
                        $cc    = count($con);
                        if ($cc != $tsql->f('znum1')) {
                            $psql->query($sql0);
                            continue;
                        }
                        switch ($pname) {
                            case '任选二中二':
                                if (in_array($con[0], $kj) & in_array($con[1], $kj)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '选前二组选':
                                $arrlm = array(
                                    $kj[0],
                                    $kj[1]
                                );
                                if (in_array($con[0], $arrlm) & in_array($con[1], $arrlm)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                                break;
                            case '选前二直选':
                                if ($con[0] == $kj[0] & $con[1] == $kj[1]) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '任选三中三':
                                if (in_array($con[0], $kj) & in_array($con[1], $kj) & in_array($con[2], $kj)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                                break;
                            case '选前三组选':
                                $arrlm = array(
                                    $kj[0],
                                    $kj[1],
                                    $kj[2]
                                );
                                if (in_array($con[0], $arrlm) & in_array($con[1], $arrlm) & in_array($con[2], $arrlm)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                                break;
                            case '选前三直选':
                                if ($con[0] == $kj[0] & $con[1] == $kj[1] & $con[2] == $kj[2]) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                            case '任选四中四':
                                if (in_array($con[0], $kj) & in_array($con[1], $kj) & in_array($con[2], $kj) & in_array($con[3], $kj)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                                break;
                            case '任选五中五':
                            case '任选六中五':
                            case '任选七中五':
                            case '任选八中五':
                                if (in_array($kj[0], $con) & in_array($kj[1], $con) & in_array($kj[2], $con) & in_array($kj[3], $con) & in_array($kj[4], $con)) {
                                    $psql->query($sql1);
                                } else {
                                    $psql->query($sql0);
                                }
                                break;
                        }
                    }
                    break;
            }
        }
    }
    
    $fsql->query("update `$tb_kj` set js=1 where $whi");
    return "结算成功888";
}
function kjjs_107($qishu, $gid, $fenlei)
{
    global $config;
    global $tb_config,$tb_lib, $tb_user, $tb_kj, $tb_z, $tb_bclass, $tb_sclass, $tb_class, $tb_play, $tb_game;
    global $tsql, $psql, $fsql, $msql;
    $whi = " gid='$gid'  and qishu='$qishu' ";
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('m1') == '')
        return "未开奖";
    $fsql->query("select js from  `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('js') == 1)
        //return '该期数已结算过了！';
    $fsql->query("delete from `$tb_z` where $whi");
    $fsql->query("select * from `$tb_game` where gid='$gid'");
    $fsql->next_record();
    $ftype = json_decode($fsql->f('ftype'), true);
    $mtype = json_decode($fsql->f('mtype'), true);
    $ztype = json_decode($fsql->f('ztype'), true);
    $cs = json_decode($fsql->f('cs'), true);
    $mnum  = $fsql->f('mnum');
    $cf    = count($ftype);
    $cm    = count($mtype);
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    $zf = 0;
    for ($i = 0; $i < $mnum; $i++) {
        $kj[$i] = $fsql->f('m' . ($i + 1));
        $zf += $kj[$i];
    }
    if($cs['ft']==1){
        $ftm = explode(',', $cs['ftnum']);
        $ft=0;
        foreach($ftm as $k => $v){
           $ft += $kj[$v-1];
        }
        $ft = $ft%4==0 ? 4 : $ft%4;
    }
    $fsql->query("update `$tb_lib` set kk=1,z=9 where $whi  and z!=7");
    $fsql->query("select * from `$tb_class` where gid='$gid' and bid in(select bid from `$tb_bclass` where gid='$gid' and ifok=1) order by bid,sid,xsort");
    while ($fsql->next_record()) {
        $bname    = transb8('name', $fsql->f('bid'), $gid);
        $sname    = transs8('name', $fsql->f('sid'), $gid);
        $cname    = $fsql->f('name');
        $tmpmtype = $fsql->f('mtype');
        $tsql->query("select bid,sid,cid,pid,name,ztype,znum1,znum2 from `$tb_play` where gid='$gid' and  cid='" . $fsql->f('cid') . "'");
        while ($tsql->next_record()) {
            $sql1     = "update `$tb_lib` set kk=1,z='1' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql0     = "update `$tb_lib` set kk=1,z='0' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql2     = "update `$tb_lib` set kk=1,z='2' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sqlz     = "insert into `$tb_z` set gid='$gid',qishu='$qishu',pid='" . $tsql->f('pid') . "'";
            $tmpztype = $ztype[$tsql->f('ztype')];
            $pname    = $tsql->f('name');
            switch ($tmpztype) {
                case "番摊":
                     if($cs['ft']!=1){
                        break;
                     }
                     switch ($cname) {
                         case '双面':
                             if($pname=="单" && $ft%2==1){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="双" && $ft%2==0){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="大" && $ft>2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="小" && $ft<=2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                             break;
                        case "番":
                             if($pname==$ft.'番'){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "念":
                             $ps = explode('念', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($ps[1]==$ft){
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "角":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "正":
                             $ps = str_replace('正', '' , $pname);
                             if($ps>2){
                                 $psdui = $ps-2;
                             }else{
                                 $psdui = $ps+2;
                             }
                             if($ps==$ft){
                                 $psql->query($sql1);
                                 $psql->query($sqlz);
                             }else if($psdui==$ft){                                
                                 $psql->query($sql0);
                             }else{
                                 $psql->query($sql2);
                             }
                        break;
                        case "中":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;

                        case '加':
                             $ps = explode('加', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if(strpos($ps[1],$ft."")!==false){     
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                            break;
                        default:
                             if($tsql->f('znum1')==$ft){
                                $psql->query($sql0);
                             }else if(strpos($pname,$ft."")!==false){                                
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql2);
                             }
                            break;
                     }
                break;
                case '码':
                    if ($bname == '冠亚军组合') {
                        $arr = $kj[0] + $kj[1];
                    } else {
                        $arr = $kj[$tmpmtype];
                    }
                    if ($arr == $pname) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '单双':
                    if ($bname == '冠亚军组合') {
                        $ma = $kj[0] + $kj[1];
                    } else {
                        $ma = $kj[$tmpmtype];
                    }
                    if (strpos("[$pname]", danshuang($ma))) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '质合':
                    $ma = $kj[$tmpmtype];
                    if (strpos("[$pname]", zhihe($ma))) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '大小':
                    $zflag = 0;
                    if ($bname == '冠亚军组合') {
                        $zf = $kj[0] + $kj[1];
                        if ($pname == '冠亚大' & $zf > 11) {
                            $zflag = 1;
                        } else if ($pname == '冠亚小' & $zf <= 11) {
                            $zflag = 1;
                        }
                    } else {
                        $ma = $kj[$tmpmtype];
                        if ($pname == '大' & $ma >= 6) {
                            $zflag = 1;
                        } else if ($pname == '小' & $ma <= 5) {
                            $zflag = 1;
                        }
                    }
                    if ($zflag) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '龙虎':
                    if ($bname == '冠军') {
                        $ma = longhuhe($kj[0], $kj[9]);
                    } else if ($bname == '亚军') {
                        $ma = longhuhe($kj[1], $kj[8]);
                    } else if ($bname == '第3名') {
                        $ma = longhuhe($kj[2], $kj[7]);
                    } else if ($bname == '第4名') {
                        $ma = longhuhe($kj[3], $kj[6]);
                    } else if ($bname == '第5名') {
                        $ma = longhuhe($kj[4], $kj[5]);
                    }
                    if ($ma == $pname) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '任选牛牛':
                    $psql->query("select pk10num,pk10ts from `$tb_config`");
                    $psql->next_record();
                    $pk10num = $psql->f('pk10num');
                    $pk10ts = $psql->f('pk10ts');
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "'  and z!=7", 1);
                    $cr = count($rs);
                    $pk10z = getpk10nium($kj,str_replace(',','-',$pk10num));
                    $pk10z = niuniu($pk10z);
                    $tmp=[];
                    for ($k = 0; $k < $cr; $k++) {
                        $tmp = getpk10nium($kj,$rs[$k]['content']);
                        $tmp = niuniu($tmp);
                        $flag = bjniuniu($pk10z,$tmp,$pk10ts);
                        $sqltmp = "update `$tb_lib` set kk=1,z='".$flag."' where id='" . $rs[$k]['id'] . "'";
                        $psql->query($sqltmp);
                    }   
                break;
            }
        }
    }
    
    $fsql->query("update `$tb_kj` set js=1 where $whi");
    return "结算成功888";
}
function kjjs_101($qishu, $gid, $fenlei)
{
    global $tb_lib, $tb_user, $tb_kj, $tb_z, $tb_bclass, $tb_sclass, $tb_class, $tb_play, $tb_game;
    global $tsql, $psql, $fsql, $msql;
    $whi = " gid='$gid'  and qishu='$qishu' ";
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('m1') == '')
        return "未开奖";
    $fsql->query("select js from  `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('js') == 1)
        return '该期数已结算过了！';
    $fsql->query("delete from `$tb_z` where $whi");
    $fsql->query("select * from `$tb_game` where gid='$gid'");
    $fsql->next_record();
    $ftype = json_decode($fsql->f('ftype'), true);
    $mtype = json_decode($fsql->f('mtype'), true);
    $ztype = json_decode($fsql->f('ztype'), true);
    $cs = json_decode($fsql->f('cs'), true);
    $mnum  = $fsql->f('mnum');
    $cf    = count($ftype);
    $cm    = count($mtype);
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    $zf = 0;
    for ($i = 0; $i < $mnum; $i++) {
        $kj[$i] = $fsql->f('m' . ($i + 1));
        $zf += $kj[$i];
    }
    if($cs['ft']==1){
        $ftm = explode(',', $cs['ftnum']);
        $ft=0;
        foreach($ftm as $k => $v){
           $ft += $kj[$v-1];
        }
        $ft = $ft%4==0 ? 4 : $ft%4;
    }
    $fsql->query("update `$tb_lib` set kk=1,z=9 where $whi  and z!=7");
    $fsql->query("select * from `$tb_class` where gid='$gid' and bid in(select bid from `$tb_bclass` where gid='$gid' and ifok=1) order by bid,sid,xsort");
    while ($fsql->next_record()) {
        $bname = transb8('name', $fsql->f('bid'), $gid);
        $sname = transs8('name', $fsql->f('sid'), $gid);
        $cname = $fsql->f('name');
        $tsql->query("select bid,sid,cid,pid,name,ztype,znum1,znum2 from `$tb_play` where gid='$gid' and  cid='" . $fsql->f('cid') . "'");
        while ($tsql->next_record()) {
            $sql1 = "update `$tb_lib` set kk=1,z='1' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql0 = "update `$tb_lib` set kk=1,z='0' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql2 = "update `$tb_lib` set kk=1,z='2' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sqlz = "insert into `$tb_z` set gid='$gid',qishu='$qishu',pid='" . $tsql->f('pid') . "'";
            $pname = $tsql->f('name');
            switch ($bname) {
                case "番摊":
                     if($cs['ft']!=1){
                        break;
                     }
                     switch ($cname) {
                         case '双面':
                             if($pname=="单" && $ft%2==1){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="双" && $ft%2==0){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="大" && $ft>2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="小" && $ft<=2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                             break;
                        case "番":
                             if($pname==$ft.'番'){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "念":
                             $ps = explode('念', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($ps[1]==$ft){
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "角":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "正":
                             $ps = str_replace('正', '' , $pname);
                             if($ps>2){
                                 $psdui = $ps-2;
                             }else{
                                 $psdui = $ps+2;
                             }
                             if($ps==$ft){
                                 $psql->query($sql1);
                                 $psql->query($sqlz);
                             }else if($psdui==$ft){                                
                                 $psql->query($sql0);
                             }else{
                                 $psql->query($sql2);
                             }
                        break;
                        case "中":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case '加':
                             $ps = explode('加', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if(strpos($ps[1],$ft."")!==false){     
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                            break;
                        default:
                             if($tsql->f('znum1')==$ft){
                                $psql->query($sql0);
                             }else if(strpos($pname,$ft."")!==false){                                
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql2);
                             }
                            break;
                     }
                break;
                case '1~5':
                case '1~3':
                    $hm = '';
                    $hm = $kj[$fsql->f('mtype')];
                    if (is_numeric($tsql->f('name'))) {
                        if ($hm == $tsql->f('name')) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[单双]', $tsql->f('name'))) {
                        $tmp = danshuang($hm);
                        if ($tmp == $tsql->f('name')) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[大小]', $tsql->f('name'))) {
                        $tmp = daxiao($hm);
                        if ($tmp == $tsql->f('name')) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[质合]', $tsql->f('name'))) {
                        $tmp = zhihe($hm);
                        if ($tmp == $tsql->f('name')) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }
                    break;
                case '1字组合':
                    $arr = array();
                    switch ($mtype[$fsql->f('mtype')]) {
                        case '全部':
                            $arr = $kj;
                            break;
                        case '前三':
                            $arr = array(
                                $kj[0],
                                $kj[1],
                                $kj[2]
                            );
                            break;
                        case '中三':
                            $arr = array(
                                $kj[2],
                                $kj[3],
                                $kj[4]
                            );
                            break;
                        case '后三':
                            $arr = array(
                                $kj[1],
                                $kj[2],
                                $kj[3]
                            );
                            break;
                    }
                    
                    if (in_array($tsql->f('name'), $arr)) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '2字组合':
                    if (strpos('[' . $pname . ']', '后三')) {
                        $arr = array(
                            $kj[2],
                            $kj[3],
                            $kj[4]
                        );
                    } else if (strpos('[' . $pname . ']', '中三')) {
                        $arr = array(
                            $kj[1],
                            $kj[2],
                            $kj[3]
                        );
                    } else {
                        $arr = array(
                            $kj[0],
                            $kj[1],
                            $kj[2]
                        );
                    }
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "'  and z!=7", 1);
                    $cr = count($rs);
                    for ($k = 0; $k < $cr; $k++) {
                        $zflag = 0;
                        $con   = explode('-', $rs[$k]['content']);
                        $con   = array_unique($con);
                        $cc    = count($con);
                        if ($cc != $tsql->f('znum1')) {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                            continue;
                        }
                        if (in_array($con[0], $arr) & in_array($con[1], $arr)) {
                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                        } else {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                        }
                    }
                    break;
                case '2字定位':
                    if ($fenlei==163 ) {
                        switch ($pname) {
                            case '百十':
                                $arr = array(
                                    $kj[0],
                                    $kj[1]
                                );
                                break;
                            case '百个':
                                $arr = array(
                                    $kj[0],
                                    $kj[2]
                                );
                                break;
                            case '十个':
                                $arr = array(
                                    $kj[1],
                                    $kj[2]
                                );
                                break;
                        }
                    } else {
                        switch ($pname) {
                            case '万千':
                                $arr = array(
                                    $kj[0],
                                    $kj[1]
                                );
                                break;
                            case '万百':
                                $arr = array(
                                    $kj[0],
                                    $kj[2]
                                );
                                break;
                            case '万十':
                                $arr = array(
                                    $kj[0],
                                    $kj[3]
                                );
                                break;
                            case '万个':
                                $arr = array(
                                    $kj[0],
                                    $kj[4]
                                );
                                break;
                            case '千百':
                                $arr = array(
                                    $kj[1],
                                    $kj[2]
                                );
                                break;
                            case '千十':
                                $arr = array(
                                    $kj[1],
                                    $kj[3]
                                );
                                break;
                            case '千个':
                                $arr = array(
                                    $kj[1],
                                    $kj[4]
                                );
                                break;
                            case '百十':
                                $arr = array(
                                    $kj[2],
                                    $kj[3]
                                );
                                break;
                            case '百个':
                                $arr = array(
                                    $kj[2],
                                    $kj[4]
                                );
                                break;
                            case '十个':
                                $arr = array(
                                    $kj[3],
                                    $kj[4]
                                );
                                break;
                        }
                    }
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "'  and z!=7", 1);
                    $cr = count($rs);
                    for ($k = 0; $k < $cr; $k++) {
                        $zflag = 0;
                        $con   = explode('-', $rs[$k]['content']);
                        $con   = array_unique($con);
                        $cc    = count($con);
                        if ($cc != $tsql->f('znum1')) {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                            continue;
                        }
                        if ($arr[0] == $con[0] & $arr[1] == $con[1]) {
                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                        } else {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                        }
                    }
                    break;
                
                case '2字和数':
                    $hm = '';
                    if ($fenlei==163 ) {
                        switch ($mtype[$fsql->f('mtype')]) {
                            case '百十':
                                $hm = $kj[0] + $kj[1];
                                break;
                            case '百个':
                                $hm = $kj[0] + $kj[2];
                                break;
                            case '十个':
                                $hm = $kj[1] + $kj[2];
                                break;
                        }
                    } else {
                        switch ($mtype[$fsql->f('mtype')]) {
                            case '万千':
                                $hm = $kj[0] + $kj[1];
                                break;
                            case '万百':
                                $hm = $kj[0] + $kj[2];
                                break;
                            case '万十':
                                $hm = $kj[0] + $kj[3];
                                break;
                            case '万个':
                                $hm = $kj[0] + $kj[4];
                                break;
                            case '千百':
                                $hm = $kj[1] + $kj[2];
                                break;
                            case '千十':
                                $hm = $kj[1] + $kj[3];
                                break;
                            case '千个':
                                $hm = $kj[1] + $kj[4];
                                break;
                            case '百十':
                                $hm = $kj[2] + $kj[3];
                                break;
                            case '百个':
                                $hm = $kj[2] + $kj[4];
                                break;
                            case '十个':
                                $hm = $kj[3] + $kj[4];
                                break;
                        }
                    }
                    if (strpos('[单双]', $tsql->f('name'))) {
                        $tmp = danshuang($hm);
                        if ($tmp == $tsql->f('name')) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[尾大尾小]', $tsql->f('name'))) {
                        $tmp = daxiaow($hm % 10);
                        if ($tmp == $tsql->f('name')) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }
                    break;
                case '3字组合':
                    if (strpos('[' . $pname . ']', '后三')) {
                        $arr = array(
                            $kj[2],
                            $kj[3],
                            $kj[4]
                        );
                    } else if (strpos('[' . $pname . ']', '中三')) {
                        $arr = array(
                            $kj[1],
                            $kj[2],
                            $kj[3]
                        );
                    } else {
                        $arr = array(
                            $kj[0],
                            $kj[1],
                            $kj[2]
                        );
                    }
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "'  and z!=7", 1);
                    $cr = count($rs);
                    for ($k = 0; $k < $cr; $k++) {
                        $zflag = 0;
                        $con   = explode('-', $rs[$k]['content']);
                        $con   = array_unique($con);
                        $cc    = count($con);
                        if ($cc != $tsql->f('znum1')) {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                            continue;
                        }
                        if (in_array($arr[0], $con) & in_array($arr[1], $con) & in_array($arr[2], $con)) {
                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                        } else {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                        }
                    }
                    break;
                case '3字定位':
                    if (strpos('[' . $pname . ']', '后三')) {
                        $arr = array(
                            $kj[2],
                            $kj[3],
                            $kj[4]
                        );
                    } else if (strpos('[' . $pname . ']', '中三')) {
                        $arr = array(
                            $kj[1],
                            $kj[2],
                            $kj[3]
                        );
                    } else {
                        $arr = array(
                            $kj[0],
                            $kj[1],
                            $kj[2]
                        );
                    }
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "'  and z!=7", 1);
                    $cr = count($rs);
                    for ($k = 0; $k < $cr; $k++) {
                        $zflag = 0;
                        $con   = explode('-', $rs[$k]['content']);
                        $con   = array_unique($con);
                        $cc    = count($con);
                        if ($cc != $tsql->f('znum1')) {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                            continue;
                        }
                        if ($arr[0] == $con[0] & $arr[1] == $con[1] & $arr[2] == $con[2]) {
                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                        } else {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                        }
                    }
                    break;
                case '3字和数':
                    $hm = '';
                    if ($mtype[$fsql->f('mtype')] == '前三') {
                        $hm = $kj[0] + $kj[1] + $kj[2];
                    } else if ($mtype[$fsql->f('mtype')] == '中三') {
                        $hm = $kj[1] + $kj[2] + $kj[3];
                    } else if ($mtype[$fsql->f('mtype')] == '后三') {
                        $hm = $kj[2] + $kj[3] + $kj[4];
                    }
                    $pname = $tsql->f('name');
                    $wei   = $hm % 10;
                    $tmp   = '';
                    if (strpos('[和单和双]', $pname)) {
                        $tmp = danshuang($hm);
                        if (strpos($pname, $tmp)) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[和大和小]', $pname)) {
                        if (($hm >= 14 & $pname == '和大') | ($hm <= 13 & $pname == '和小')) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[和尾大和尾小]', $pname)) {
                        $tmp = daxiao($wei);
                        if (strpos($pname, $tmp)) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[和尾质和尾合]', $pname)) {
                        $tmp = zhihe($wei);
                        if (strpos($pname, $tmp)) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if ($cname == '尾数') {
                        if ($wei == $pname) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if ($cname == '和数') {
                        $ps = explode('~', $pname);
                        $cp = count($ps);
                        if ($cp == 1) {
                            if ($pname == $hm) {
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            } else {
                                $psql->query($sql0);
                            }
                        } else if ($cp == 2) {
                            if ($hm >= $ps[0] & $hm <= $ps[1]) {
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            } else {
                                $psql->query($sql0);
                            }
                        }
                    }
                    break;
                case '总和龙虎':
                    if ($fenlei==163 ) {
                        $hm = $kj[0] + $kj[1] + $kj[2];
                    } else {
                        $hm = $kj[0] + $kj[1] + $kj[2] + $kj[3] + $kj[4];
                    }
                    $wei   = $hm % 10;
                    $pname = $tsql->f('name');
                    $tmp   = '';
                    if (strpos('[总和单总和双]', $pname)) {
                        $tmp = danshuang($hm);
                        if ($fenlei==163 & (($hm == 14 & $pname == '总和双') | ($hm == 13 & $pname == '总和单'))) {
                            $psql->query($sql2);
                        } else if (strpos($pname, $tmp)!==false) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[总和大总和小]', $pname)) {
                        if ($fenlei==163 ) {
                            if (($hm == 14 & $pname == '总和大') | ($hm == 13 & $pname == '总和小')) {
                                $psql->query($sql2);
                            } else if (($hm > 14 & $pname == '总和大') | ($hm < 13 & $pname == '总和小')) {
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            } else {
                                $psql->query($sql0);
                            }
                        } else {
                            if (($hm >= 23 & $pname == '总和大') || ($hm <= 22 & $pname == '总和小')) {
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            } else {
                                $psql->query($sql0);
                            }
                        }
                    } else if (strpos('[总和尾大总和尾小]', $pname)) {
                        $tmp = daxiao($wei);
                        if (strpos($pname, $tmp)) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[总尾质总尾合]', $pname)) {
                        $tmp = zhihe($wei);
                        if (strpos($pname, $tmp)) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos('[总大单总大双总小单总小双]', $pname)) {
                        $ds = danshuang($hm);
                        if ($hm >= 14)
                            $tmp = '总大' . $ds;
                        else
                            $tmp = '总小' . $ds;
                        if (($hm == 14 & $pname == '总大双') | ($hm == 13 & $pname == '总小单')) {
                           $psql->query($sql2);
                        }else if ($pname == $tmp) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }else if ($cname == '极值大小') {
                        if ($hm >= 22)
                            $tmp = '极大';
                        else if ($hm <=5 )
                            $tmp = '极小';
                        if ($tmp == $pname) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if ($cname == '总和尾数') {
                        if ($wei == $pname) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if (strpos($cname,'总和')!== false | $cname == '总和数') {
                        $ps = explode('~', $pname);
                        $cp = count($ps);
                        if ($cp == 1) {
                            if ($pname == $hm) {
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            } else {
                                $psql->query($sql0);
                            }
                        } else if ($cp == 2) {
                            if ($hm >= $ps[0] & $hm <= $ps[1]) {
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            } else {
                                $psql->query($sql0);
                            }
                        }
                    } else if ($cname == '龙虎和') {
                        if ($fenlei==163 ) {
                            $tmp = longhuhe($kj[0], $kj[2]);
                        } else {
                            $tmp = longhuhe($kj[0], $kj[4]);
                        }
                        if ($tmp == $pname) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else if ($tmp == '和' & $pname != '和') {
                            $psql->query($sql2);
                        } else {
                            $psql->query($sql0);
                        }
                    }
                    break;
                case '组选3':
                    if (strpos('[' . $pname . ']', '后三')) {
                        $arr = array(
                            $kj[2],
                            $kj[3],
                            $kj[4]
                        );
                    } else if (strpos('[' . $pname . ']', '中三')) {
                        $arr = array(
                            $kj[1],
                            $kj[2],
                            $kj[3]
                        );
                    } else {
                        $arr = array(
                            $kj[0],
                            $kj[1],
                            $kj[2]
                        );
                    }
                    if (duizhi($arr[0], $arr[1], $arr[2]) != 1) {
                        $psql->query("update `$tb_lib` set kk=1,z='0' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7");
                        continue;
                    }
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "'  and z!=7", 1);
                    $cr = count($rs);
                    for ($k = 0; $k < $cr; $k++) {
                        $zflag = 0;
                        $con   = explode('-', $rs[$k]['content']);
                        $con   = array_unique($con);
                        $cc    = count($con);
                        if ($cc != $tsql->f('znum1')) {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                            continue;
                        }
                        if (in_array($arr[0], $con) & in_array($arr[1], $con) & in_array($arr[2], $con)) {
                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                        } else {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                        }
                    }
                    break;
                case '组选6':
                    if (strpos('[' . $pname . ']', '后三')) {
                        $arr = array(
                            $kj[2],
                            $kj[3],
                            $kj[4]
                        );
                    } else if (strpos('[' . $pname . ']', '中三')) {
                        $arr = array(
                            $kj[1],
                            $kj[2],
                            $kj[3]
                        );
                    } else {
                        $arr = array(
                            $kj[0],
                            $kj[1],
                            $kj[2]
                        );
                    }
                    if (duizhi($arr[0], $arr[1], $arr[2]) == 1 | baozhi($arr[0], $arr[1], $arr[2]) == 1) {
                        $psql->query("update `$tb_lib` set kk=1,z='0' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7");
                        continue;
                    }
                    $rs = $psql->arr("select * from `$tb_lib` where $whi and  pid='" . $tsql->f('pid') . "'  and z!=7", 1);
                    $cr = count($rs);
                    for ($k = 0; $k < $cr; $k++) {
                        $zflag = 0;
                        $con   = explode('-', $rs[$k]['content']);
                        $con   = array_unique($con);
                        $cc    = count($con);
                        if ($cc != $tsql->f('znum1')) {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                            continue;
                        }
                        if (in_array($arr[0], $con) & in_array($arr[1], $con) & in_array($arr[2], $con)) {
                            $psql->query("update `$tb_lib` set kk=1,z='1' where id='" . $rs[$k]['id'] . "'");
                        } else {
                            $psql->query("update `$tb_lib` set kk=1,z='0' where id='" . $rs[$k]['id'] . "'");
                        }
                    }
                    break;
                case '牛牛梭哈':
                    if ($sname == '牛牛') {
                        $nn    = niuniu($kj);
                        $zflag = 0;
                        if ($nn[0]) {
                            switch ($nn[2]) {
                                case 0:
                                    if ($pname == '牛牛' | $pname == '牛大' | $pname == '牛双' | $pname == '牛合') {
                                        $zflag = 1;
                                    }
                                    break;
                                case 1:
                                    if ($pname == '牛1' | $pname == '牛小' | $pname == '牛单' | $pname == '牛质') {
                                        $zflag = 1;
                                    }
                                    break;
                                case 2:
                                    if ($pname == '牛2' | $pname == '牛小' | $pname == '牛双' | $pname == '牛质') {
                                        $zflag = 1;
                                    }
                                    break;
                                case 3:
                                    if ($pname == '牛3' | $pname == '牛小' | $pname == '牛单' | $pname == '牛质') {
                                        $zflag = 1;
                                    }
                                    break;
                                case 4:
                                    if ($pname == '牛4' | $pname == '牛小' | $pname == '牛双' | $pname == '牛合') {
                                        $zflag = 1;
                                    }
                                    break;
                                case 5:
                                    if ($pname == '牛5' | $pname == '牛小' | $pname == '牛单' | $pname == '牛质') {
                                        $zflag = 1;
                                    }
                                    break;
                                case 6:
                                    if ($pname == '牛6' | $pname == '牛大' | $pname == '牛双' | $pname == '牛合') {
                                        $zflag = 1;
                                    }
                                    break;
                                case 7:
                                    if ($pname == '牛7' | $pname == '牛大' | $pname == '牛单' | $pname == '牛质') {
                                        $zflag = 1;
                                    }
                                    break;
                                case 8:
                                    if ($pname == '牛8' | $pname == '牛大' | $pname == '牛双' | $pname == '牛合') {
                                        $zflag = 1;
                                    }
                                    break;
                                case 9:
                                    if ($pname == '牛9' | $pname == '牛大' | $pname == '牛单' | $pname == '牛合') {
                                        $zflag = 1;
                                    }
                                    break;
                            }
                        }else{
                           if ($pname == '无牛') {
                                $zflag = 1;
                            }
                        }
                        
                        if ($zflag == 1) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else {
                        $suoha = suoha($kj);
                        if ($suoha == $pname) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }
                    break;
                case '跨度':
                    $hm = 0;
                    switch ($mtype[$fsql->f('mtype')]) {
                        case '前三':
                        case '全部':
                            $k1 = abs($kj[0] - $kj[1]);
                            $k2 = abs($kj[0] - $kj[2]);
                            $k3 = abs($kj[1] - $kj[2]);
                            $k  = max($k1, $k2, $k3);
                            break;
                        case '中三':
                            $k1 = abs($kj[1] - $kj[2]);
                            $k2 = abs($kj[1] - $kj[3]);
                            $k3 = abs($kj[2] - $kj[3]);
                            $k  = max($k1, $k2, $k3);
                            break;
                        case '后三':
                            $k1 = abs($kj[2] - $kj[3]);
                            $k2 = abs($kj[2] - $kj[4]);
                            $k3 = abs($kj[3] - $kj[4]);
                            $k  = max($k1, $k2, $k3);
                            break;
                    }
                    if ($k == $tsql->f('name')) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '前中后三':
                    $hm = 0;
                    switch ($mtype[$fsql->f('mtype')]) {
                        case '前三':
                        case '全部':
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
                    $pname = $tsql->f('name');
                    switch ($cname) {
                        case '准对':
                            $num = 0;
                            if ($k1 == $pname)
                                $num++;
                            if ($k2 == $pname)
                                $num++;
                            if ($k3 == $pname)
                                $num++;
                            if ($num == 2) {
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            } else {
                                $psql->query($sql0);
                            }
                        case '不出':
                            if ($pname != $k1 & $pname != $k2 & $pname != $k3) {
                                $psql->query($sql1);
                                $psql->query($sqlz);
                            } else {
                                $psql->query($sql0);
                            }
                        default:
                            switch ($pname) {
                                case '豹子':
                                    $v = baozhi($k1, $k2, $k3);
                                    if ($v == 1) {
                                        $psql->query($sql1);
                                        $psql->query($sqlz);
                                    } else {
                                        $psql->query($sql0);
                                    }
                                    break;
                                case '顺子':
                                    $v = shunzhi($k1, $k2, $k3);
                                    if ($v == 1) {
                                        $psql->query($sql1);
                                        $psql->query($sqlz);
                                    } else {
                                        $psql->query($sql0);
                                    }
                                    break;
                                case '对子':
                                    $v = duizhi($k1, $k2, $k3);
                                    if ($v == 1) {
                                        $psql->query($sql1);
                                        $psql->query($sqlz);
                                    } else {
                                        $psql->query($sql0);
                                    }
                                    break;
                                case '半顺':
                                    $v = banshun($k1, $k2, $k3);
                                    if ($v == 1) {
                                        $psql->query($sql1);
                                        $psql->query($sqlz);
                                    } else {
                                        $psql->query($sql0);
                                    }
                                    break;
                                case '杂六':
                                    if (zaliu($k1, $k2, $k3) == 1) {
                                        $psql->query($sql1);
                                        $psql->query($sqlz);
                                    } else {
                                        $psql->query($sql0);
                                    }
                                    break;
                            }
                            break;
                    }
                    break;
            }
        }
    }
    
    $fsql->query("update `$tb_kj` set js=1 where $whi");
    return "结算成功888";
}
function kjjs_161($qishu, $gid, $fenlei)
{
    global $tb_lib, $tb_user, $tb_kj, $tb_z, $tb_bclass, $tb_sclass, $tb_class, $tb_play, $tb_game;
    global $tsql, $psql, $fsql, $msql;
    $whi = " gid='$gid'  and qishu='$qishu' ";
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('m1') == '')
        return "未开奖";
    $fsql->query("select js from  `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('js') == 1)
        return '该期数已结算过了！';
    $fsql->query("delete from `$tb_z` where $whi");
    $fsql->query("select * from `$tb_game` where gid='$gid'");
    $fsql->next_record();
    $ftype = json_decode($fsql->f('ftype'), true);
    $mtype = json_decode($fsql->f('mtype'), true);
    $ztype = json_decode($fsql->f('ztype'), true);
    $cs = json_decode($fsql->f('cs'), true);
    $mnum  = $fsql->f('mnum');
    $cf    = count($ftype);
    $cm    = count($mtype);
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    $zf = 0;
    $zd = 0;
    $zq = 0;
    for ($i = 0; $i < $mnum; $i++) {
        $kj[$i] = $fsql->f('m' . ($i + 1));
        if (danshuang($fsql->f('m' . ($i + 1))) == '单') {
            $zd++;
        }
        if ($fsql->f('m' . ($i + 1)) <= 40) {
            $zq++;
        }
        $zf += $kj[$i];
    }
    if($cs['ft']==1){
        $ftm = explode(',', $cs['ftnum']);
        $ft=0;
        foreach($ftm as $k => $v){
           $ft += $kj[$v-1];
        }
        $ft = $ft%4==0 ? 4 : $ft%4;
    }
    $fsql->query("update `$tb_lib` set kk=1,z=9 where $whi  and z!=7");
    $fsql->query("select * from `$tb_class` where gid='$gid' and bid in(select bid from `$tb_bclass` where gid='$gid' and ifok=1) order by bid,sid,xsort");
    while ($fsql->next_record()) {
        $bname    = transb8('name', $fsql->f('bid'), $gid);
        $sname    = transs8('name', $fsql->f('sid'), $gid);
        $cname    = $fsql->f('name');
        $tmpmtype = $fsql->f('mtype');
        $tsql->query("select bid,sid,cid,pid,name,ztype,znum1,znum2 from `$tb_play` where gid='$gid' and  cid='" . $fsql->f('cid') . "'");
        while ($tsql->next_record()) {
            $sql1     = "update `$tb_lib` set kk=1,z='1' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql0     = "update `$tb_lib` set kk=1,z='0' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql2     = "update `$tb_lib` set kk=1,z='2' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sqlz     = "insert into `$tb_z` set gid='$gid',qishu='$qishu',pid='" . $tsql->f('pid') . "'";
            $tmpztype = $ztype[$tsql->f('ztype')];
            $pname    = $tsql->f('name');
            switch ($tmpztype) {
                case "番摊":
                     if($cs['ft']!=1){
                        break;
                     }
                     switch ($cname) {
                         case '双面':
                             if($pname=="单" && $ft%2==1){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="双" && $ft%2==0){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="大" && $ft>2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($pname=="小" && $ft<=2){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                             break;
                        case "番":
                             if($pname==$ft.'番'){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "念":
                             $ps = explode('念', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if($ps[1]==$ft){
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "角":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case "正":
                             $ps = str_replace('正', '' , $pname);
                             if($ps>2){
                                 $psdui = $ps-2;
                             }else{
                                 $psdui = $ps+2;
                             }
                             if($ps==$ft){
                                 $psql->query($sql1);
                                 $psql->query($sqlz);
                             }else if($psdui==$ft){                                
                                 $psql->query($sql0);
                             }else{
                                 $psql->query($sql2);
                             }
                        break;
                        case "中":
                             if(strpos($pname,$ft."")!==false){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql0);
                             }
                        break;
                        case '加':
                             $ps = explode('加', $pname);
                             if($ps[0]==$ft){
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else if(strpos($ps[1],$ft."")!==false){     
                                $psql->query($sql2);
                             }else{
                                $psql->query($sql0);
                             }
                            break;
                        default:
                             if($tsql->f('znum1')==$ft){
                                $psql->query($sql0);
                             }else if(strpos($pname,$ft."")!==false){                                
                                $psql->query($sql1);
                                $psql->query($sqlz);
                             }else{
                                $psql->query($sql2);
                             }
                            break;
                     }
                break;
                case '正码':
                    $arr = $kj;
                    if (in_array($pname, $arr)) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '总和':
                    if ($zf < 810) {
                        $dx = "小";
                    } else if ($zf > 810) {
                        $dx = "大";
                    } else {
                        $dx = "和";
                    }
                    if ($zf == 810) {
                        $ds = "和";
                    } else {
                        $ds = danshuang($zf);
                    }
                    $zflag = 0;
                    switch ($cname) {
                        case "总和单双":
                            if (strpos("[$pname]", $ds)) {
                                $zflag = 1;
                            }
                            break;
                        case "总和大小":
                            if ($zf == 810) {
                                $zflag = 2;
                            } else if (strpos("[$pname]", $dx)) {
                                $zflag = 1;
                            }
                            break;
                        case "总和810":
                            if ($zf == 810) {
                                $zflag = 1;
                            }
                            break;
                        case "总和过关":
                            if ($zf == 810) {
                                $zflag = 2;
                            } else if (strpos("[$pname]", $dx . $ds)) {
                                $zflag = 1;
                            }
                            break;
                        case "前后和":
                            switch ($pname) {
                                case "前(多)":
                                    if ($zq >= 11) {
                                        $zflag = 1;
                                    }
                                    break;
                                case "后(多)":
                                    if ($zq <= 9) {
                                        $zflag = 1;
                                    }
                                    break;
                                case "前后(和)":
                                    if ($zq == 10) {
                                        $zflag = 1;
                                    }
                                    break;
                            }
                            break;
                        case "单双和":
                            switch ($pname) {
                                case "单(多)":
                                    if ($zd >= 11) {
                                        $zflag = 1;
                                    }
                                    break;
                                case "双(多)":
                                    if ($zd <= 9) {
                                        $zflag = 1;
                                    }
                                    break;
                                case "单双(和)":
                                    if ($zd == 10) {
                                        $zflag = 1;
                                    }
                                    break;
                            }
                            break;
                    }
                    if ($zflag == 1) {
                        $psql->query($sqlz);
                    }
                    $psql->query("update `$tb_lib` set kk=1,z='$zflag' where $whi and pid='" . $tsql->f('pid') . "' ");
                    break;
                case '五行':
                    $wh = wuhang_161($zf);
                    if ($pname == $wh) {
                        $psql->query($sqlz);
                        $psql->query($sql1);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
            }
        }
    }
    
    $fsql->query("update `$tb_kj` set js=1 where $whi");
    return "结算成功888";
}
function kjjs_151($qishu, $gid, $fenlei)
{
    global $tb_lib, $tb_user, $tb_kj, $tb_z, $tb_bclass, $tb_sclass, $tb_class, $tb_play, $tb_game;
    global $tsql, $psql, $fsql, $msql;
    $whi = " gid='$gid'  and qishu='$qishu' ";
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('m1') == '')
        return "未开奖";
    $fsql->query("select js from  `$tb_kj` where $whi");
    $fsql->next_record();
    if ($fsql->f('js') == 1)
        return '该期数已结算过了！';
    $fsql->query("delete from `$tb_z` where $whi");
    $fsql->query("select * from `$tb_game` where gid='$gid'");
    $fsql->next_record();
    $ftype = json_decode($fsql->f('ftype'), true);
    $mtype = json_decode($fsql->f('mtype'), true);
    $ztype = json_decode($fsql->f('ztype'), true);
    $mnum  = $fsql->f('mnum');
    $cf    = count($ftype);
    $cm    = count($mtype);
    $fsql->query("select * from `$tb_kj` where $whi");
    $fsql->next_record();
    $zf = 0;
    for ($i = 0; $i < $mnum; $i++) {
        $kj[$i] = $fsql->f('m' . ($i + 1));
        $zf += $kj[$i];
    }

    $fsql->query("update `$tb_lib` set kk=1,z=9 where $whi  and z!=7");
    $fsql->query("select * from `$tb_class` where gid='$gid' and bid in(select bid from `$tb_bclass` where gid='$gid' and ifok=1) order by bid,sid,xsort");
    while ($fsql->next_record()) {
        $bname    = transb8('name', $fsql->f('bid'), $gid);
        $sname    = transs8('name', $fsql->f('sid'), $gid);
        $cname    = $fsql->f('name');
        $tmpmtype = $fsql->f('mtype');
        $tsql->query("select bid,sid,cid,pid,name,ztype,znum1,znum2 from `$tb_play` where gid='$gid' and  cid='" . $fsql->f('cid') . "'");
        while ($tsql->next_record()) {
            $sql1     = "update `$tb_lib` set kk=1,z='1' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql0     = "update `$tb_lib` set kk=1,z='0' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sql2     = "update `$tb_lib` set kk=1,z='2' where $whi and pid='" . $tsql->f('pid') . "'  and z!=7";
            $sqlz     = "insert into `$tb_z` set gid='$gid',qishu='$qishu',pid='" . $tsql->f('pid') . "'";
            $tmpztype = $ztype[$tsql->f('ztype')];
            $pname    = $tsql->f('name');
            switch ($tmpztype) {
                case '码':
                    if (in_array($pname, $kj)) {
                        $psql->query($sql1);
                        $psql->query($sqlz);
                    } else {
                        $psql->query($sql0);
                    }
                    break;
                case '骰':
                    if ($pname == '全骰') {
                        if (baozhi($kj[0], $kj[1], $kj[2]) == 1) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else {
                        if (baozhi($kj[0], $kj[1], $kj[2]) == 1 & $kj[0] == $pname % 10) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }
                    break;
                case '点':
                    if ($cname == '三军大小') {
                        if (baozhi($kj[0], $kj[1], $kj[2]) == 1) {
                            $psql->query($sql0);
                        } else if ($pname == '三军大' & $zf >= 11) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else if ($pname == '三军小' & $zf <= 10) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else {
                        if ($zf == str_replace('点', '', $pname)) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }
                    break;
                case '牌':
                    $zflag = 0;
                    if ($cname == '长牌') {
                        $two = $pname % 10;
                        $one = ($pname - $two) / 10;
                        if (in_array($one, $kj) & in_array($two, $kj)) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    } else if ($cname == '短牌') {
                        $two = $pname % 10;
                        $cs  = array_count_values($kj);
                        if ($cs[$two] >= 2) {
                            $psql->query($sql1);
                            $psql->query($sqlz);
                        } else {
                            $psql->query($sql0);
                        }
                    }
                    break;
            }
        }
    }
    
    $fsql->query("update `$tb_kj` set js=1 where $whi");
    return "结算成功888";
}