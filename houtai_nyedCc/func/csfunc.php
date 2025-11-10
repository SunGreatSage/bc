<?php
function getwarn($class)
{
    global $psql, $tb_warn, $gid, $userid;
    $psql->query("select je,ks from `$tb_warn` where userid='$userid' and gid='$gid' and class='$class'");
    $psql->next_record();
    return array(
        "je" => $psql->f('je'),
        "ks" => $psql->f('ks')
    );
}
function getgame()
{
    global $psql, $tb_game;
    $psql->query("select gid,gname,fast,panstatus,otherstatus,otherclosetime,userclosetime,mnum,fenlei,ifopen,autokj,guanfang from `$tb_game` order by xsort");
    $i = 0;
    while ($psql->next_record()) {
        $game[$i]['gid']            = $psql->f('gid');
        $game[$i]['gname']          = $psql->f('gname');
        $game[$i]['fast']           = $psql->f('fast');
        $game[$i]['mnum']           = $psql->f('mnum');
        $game[$i]['fenlei']           = $psql->f('fenlei');
        $game[$i]['xsort']           = $psql->f('xsort');
        $game[$i]['panstatus']      = $psql->f('panstatus');
        $game[$i]['otherstatus']    = $psql->f('otherstatus');
        $game[$i]['otherclosetime'] = $psql->f('otherclosetime');
        $game[$i]['userclosetime']  = $psql->f('userclosetime');
        $game[$i]['ifopen']           = $psql->f('ifopen');
        $game[$i]['autokj']           = $psql->f('autokj');
        $game[$i]['guanfang']           = $psql->f('guanfang');
        $i++;
    }
    return $game;
}
function mtype($v)
{
    global $config;
    $i = 0;
    foreach ($config['mtype'] as $n) {
        if ($n == $v) {
            break;
        }
        $i++;
    }
    return $i;
}
function ftype($v)
{
    global $config;
    foreach ($config['ftype'] as $key => $n) {
        if ($n == $v) {
            $i = $key;
            break;
        }
    }
    $i = str_replace('p', '', $key);
    return $i;
}
function dftype($v)
{
    global $config;
    foreach ($config['dftype'] as $key => $n) {
        if ($n == $v) {
            $i = $key;
            break;
        }
    }
    $i = str_replace('p', '', $key);
    return $i;
}
function ztype($v)
{
    global $config;
    $i = 0;
    foreach ($config['ztype'] as $n) {
        if ($n == $v) {
            break;
        }
        $i++;
    }
    return $i;
}
function transb($field, $bid)
{
    global $tb_bclass, $psql, $gid;
    $psql->query("select $field from `$tb_bclass` where  gid='$gid' and bid='$bid'");
    $psql->next_record();
    return $psql->f($field);
}
function transs($field, $sid)
{
    global $tb_sclass, $psql, $gid;
    $psql->query("select $field from `$tb_sclass` where gid='$gid' and  sid='$sid'");
    $psql->next_record();
    return $psql->f($field);
}
function transc($field, $cid)
{
    global $tb_class, $psql, $gid;
    $psql->query("select $field from `$tb_class` where gid='$gid' and cid='$cid'");
    $psql->next_record();
    return $psql->f($field);
}
function transp($field, $pid)
{
    global $tb_play, $psql, $gid;
    $psql->query("select $field from `$tb_play` where gid='$gid' and pid='$pid'");
    $psql->next_record();
    return $psql->f($field);
}
function transb8($field, $bid, $gid)
{
    global $tb_bclass, $psql;
    $psql->query("select $field from `$tb_bclass` where  gid='$gid' and bid='$bid'");
    $psql->next_record();
    return $psql->f($field);
}
function transs8($field, $sid, $gid)
{
    global $tb_sclass, $psql;
    $psql->query("select $field from `$tb_sclass` where gid='$gid' and  sid='$sid'");
    $psql->next_record();
    return $psql->f($field);
}
function transc8($field, $cid, $gid)
{
    global $tb_class, $psql;
    $psql->query("select $field from `$tb_class` where gid='$gid' and cid='$cid'");
    $psql->next_record();
    return $psql->f($field);
}
function transp8($field, $pid, $gid)
{
    global $tb_play, $psql;
    $psql->query("select $field from `$tb_play` where gid='$gid' and pid='$pid'");
    $psql->next_record();
    return $psql->f($field);
}
function untransp($bid, $sid, $cid, $name,$gid)
{
    global $tb_play, $psql;
    $psql->query("select pid from `$tb_play` where gid='$gid' and bid='$bid'  and sid='$sid'  and cid='$cid' and name='$name'");
    $psql->next_record();
    return $psql->f('pid');
}
function getb8h($gid)
{
    global $tsql,  $tb_bclass;

    $tsql->query("select * from `$tb_bclass` where gid='$gid' and ifok=1 order by xsort");
    $i = 0;
    $b = array();
    while ($tsql->next_record()) {
        $b[$i]['bid']  = $tsql->f('bid');
        $b[$i]['name'] = $tsql->f('name');
        $b[$i]['i']    = $i;
        $i++;
    }

    return $b;
}
function getbh()
{
    global $tsql, $gid, $tb_bclass;


    $tsql->query("select * from `$tb_bclass` where gid='$gid' and ifok=1 order by xsort");
    $i = 0;
    $b = array();
    while ($tsql->next_record()) {
        $b[$i]['bid']  = $tsql->f('bid');
        $b[$i]['name'] = $tsql->f('name');
        $b[$i]['i']    = $i;
        $i++;
    }
    return $b;
}
function getb8($gid)
{
    global $tsql,  $tb_bclass,$config;
    $fenlei = transgame($gid,'fenlei');
    if($fenlei==107){
      $b[0]['bid'] = 0;
      $b[0]['i'] = 0;
      $b[0]['name'] = '冠、亚军组合';
      $b[1]['bid'] = 1;
      $b[1]['i'] = 1;
      $b[1]['name'] = '三、四、五、六名';
      $b[2]['bid'] = 2;
      $b[2]['i'] = 2;
      $b[2]['name'] = '七、八、九、十名';
      if($config['cs']['ft']==1){ 
      $b[3]['bid'] = 3;
      $b[3]['i'] = 3;
      $b[3]['name'] = '番摊';
      }
     if($config['pk10niu']==1){ 
      $b[4]['bid'] = 4;
      $b[4]['i'] = 4;
      $b[4]['name'] = '任选牛牛';
     }
    }else{
    $tsql->query("select * from `$tb_bclass` where gid='$gid' and ifok=1 order by xsort");
    $i = 0;
    $b = array();
    while ($tsql->next_record()) {
        $b[$i]['bid']  = $tsql->f('bid');
        $b[$i]['name'] = $tsql->f('name');
        $b[$i]['i']    = $i;
        $i++;
    }
    }
    return $b;
}
function getb()
{
    global $tsql, $gid, $tb_bclass;
    if($gid==107){
      $b[0]['bid'] = 0;
      $b[0]['i'] = 0;
      $b[0]['name'] = '冠、亚军组合';
      $b[1]['bid'] = 1;
      $b[1]['i'] = 1;
      $b[1]['name'] = '三、四、五、六名';
      $b[2]['bid'] = 2;
      $b[2]['i'] = 2;
      $b[2]['name'] = '七、八、九、十名';
    }else{

    $tsql->query("select * from `$tb_bclass` where gid='$gid' and ifok=1 order by xsort");
    $i = 0;
    $b = array();
    while ($tsql->next_record()) {
        $b[$i]['bid']  = $tsql->f('bid');
        $b[$i]['name'] = $tsql->f('name');
        $b[$i]['i']    = $i;
        $i++;
    }}
    return $b;
}
function gets($bid = '')
{
    global $tsql, $gid, $tb_sclass, $tb_bclass;
    if ($bid != '') {
        $tsql->query("select * from `$tb_sclass` where gid='$gid' and bid='$bid' and ifok=1 order by xsort");
    } else {
        $tsql->query("select * from `$tb_sclass` where gid='$gid'  and ifok=1 order by xsort");
    }//and bid=(select bid from `$tb_bclass` where gid='$gid' order by xsort limit 1)
    $i = 0;
    $s = array();
    while ($tsql->next_record()) {
        $s[$i]['sid']  = $tsql->f('sid');
        $s[$i]['name'] = $tsql->f('name');
        $s[$i]['i']    = $i;
        $i++;
    }
    return $s;
}
function getc($bid = '', $sid = '')
{
    global $tsql, $gid, $tb_class, $tb_bclass;
    if ($bid != '') {
        $tsql->query("select * from `$tb_class` where gid='$gid' and bid='$bid' and sid='$sid' and ifok=1 order by xsort");
    } else {
        $tsql->query("select * from `$tb_class` where gid='$gid' and bid=(select bid from `$tb_bclass` order by xsort limit 1) and ifok=1 order by xsort");
    }
    $i = 0;
    $c = array();
    while ($tsql->next_record()) {
        $c[$i]['sid']  = $tsql->f('sid');
        $c[$i]['name'] = $tsql->f('name');
        $c[$i]['i']    = $i;
        $i++;
    }
    return $c;
}
function getp107($bid, $ab, $abcd, $cid)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid;
    //$abcd = low($abcd);
    $time = time();
    $sql2="";
    if($bid==0){
       $sql = "select * from `$tb_play` where gid='$gid' and bid=23378805 order by bid,xsort";
       $sql2 = "select * from `$tb_play` where gid='$gid' and name!='质' and name!='合' and bid<23378805 order by bid,xsort";
    }else if($bid==1){
       $sql = "select * from `$tb_play` where gid='$gid' and bid>=23378807 and bid<=23378813 order by bid,sid,xsort";
    }else if($bid==2){
       $sql = "select * from `$tb_play` where gid='$gid' and bid>=23378816 and bid<=23378823 order by bid,sid,xsort";
    }else if($bid==3){
       $sql = "select * from `$tb_play` where gid='$gid' and bid=26000000 order by bid,sid,cid,xsort";
    }
    $tsql->query($sql);
    $i    = 0;
    $p    = array();
    $cid  = 0;
    $sid  = 0;
    $csid = 1;
    $ccid = 1;
    $abcd = strtolower($abcd);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0)
            $csid++;
        if ($cid != $tsql->f('cid') & $cid != 0)
            $ccid++;
        if ($cid != $tsql->f('cid')){   
           $psql->query("select dftype,ftype,name from `$tb_class` where gid='$gid' and cid='".$tsql->f('cid')."'");
           $psql->next_record();
           $ftype           = $psql->f('ftype');
           $dftype           = $psql->f('dftype');
           $cname = $psql->f('name');
        }
        if ($sid != $tsql->f('sid')){
           $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype']  = $ftype;
        $p[$i]['dftype']  = $dftype;
        $p[$i]['bid']    = $tsql->f('bid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['sname']  = $sname;
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['cname']  = $cname;
        $p[$i]['pid']    = $tsql->f('pid');
        $p[$i]['name']   = $tsql->f('name');
        $p[$i]['ifok']   = $tsql->f('ifok');
        $p[$i]['xsort']  = $tsql->f('xsort');
        $p[$i]['znum1']    = $tsql->f('znum1');
        $p[$i]['peilv1'] = (float) ($tsql->f('peilv1') - $config['patt'][$ftype][$abcd]);

        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $p[$i]['peilv2'] = (float) $tsql->f('peilv2');
        $p[$i]['mp1']    = (float) $tsql->f('mp1');
        $p[$i]['mp2']    = (float) $tsql->f('mp2');
        $cid             = $tsql->f('cid');
        $sid             = $tsql->f('sid');
        $bid             = $tsql->f('bid');
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['bid']    = $tsql->f('bid');
        $i++;
    }
    if($sql2!=""){
        $tsql->query($sql2);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0)
            $csid++;
        if ($cid != $tsql->f('cid') & $cid != 0)
            $ccid++;
        if ($cid != $tsql->f('cid')){   
           $psql->query("select dftype,ftype,name from `$tb_class` where gid='$gid' and cid='".$tsql->f('cid')."'");
           $psql->next_record();
           $ftype           = $psql->f('ftype');
           $dftype           = $psql->f('dftype');
           $cname = $psql->f('name');
        }
        if ($sid != $tsql->f('sid')){
           $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype']  = $ftype;
        $p[$i]['dftype']  = $dftype;
        $p[$i]['bid']    = $tsql->f('bid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['sname']  = $sname;
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['cname']  = $cname;
        $p[$i]['pid']    = $tsql->f('pid');
        $p[$i]['name']   = $tsql->f('name');
        $p[$i]['ifok']   = $tsql->f('ifok');
        $p[$i]['xsort']  = $tsql->f('xsort');
        $p[$i]['znum1']    = $tsql->f('znum1');
        $p[$i]['peilv1'] = (float) ($tsql->f('peilv1') - $config['patt'][$ftype][$abcd]);

        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $p[$i]['peilv2'] = (float) $tsql->f('peilv2');
        $p[$i]['mp1']    = (float) $tsql->f('mp1');
        $p[$i]['mp2']    = (float) $tsql->f('mp2');
        $cid             = $tsql->f('cid');
        $sid             = $tsql->f('sid');
        $bid             = $tsql->f('bid');
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['bid']    = $tsql->f('bid');
        $i++;
    }
    }
    $p[0]['csid'] = $csid;
    $p[0]['ccid'] = $ccid;
    return $p;
}
function getpsm($bid, $ab, $abcd, $cid)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid;
    //$abcd = low($abcd);
    $time = time();
    $tsql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' order by bid,sid,xsort");
    $i    = 0;
    $p    = array();
    $cid  = 0;
    $sid  = 0;
    $csid = 1;
    $ccid = 1;
    $abcd = strtolower($abcd);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0)
            $csid++;
        if ($cid != $tsql->f('cid') & $cid != 0)
            $ccid++;
        if ($cid != $tsql->f('cid')){   
           $psql->query("select dftype,ftype,name from `$tb_class` where gid='$gid' and cid='".$tsql->f('cid')."'");
           $psql->next_record();
           $ftype           = $psql->f('ftype');
           $dftype           = $psql->f('dftype');
           $cname = $psql->f('name');
        }
        if ($sid != $tsql->f('sid')){
           $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype']  = $ftype;
        $p[$i]['dftype']  = $dftype;
        $p[$i]['bid']    = $tsql->f('bid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['sname']  = $sname;
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['cname']  = $cname;
        $p[$i]['pid']    = $tsql->f('pid');
        $p[$i]['name']   = $tsql->f('name');
        $p[$i]['ifok']   = $tsql->f('ifok');
        $p[$i]['xsort']  = $tsql->f('xsort');
        $p[$i]['znum1']    = $tsql->f('znum1');
        $p[$i]['peilv1'] = (float) ($tsql->f('peilv1') - $config['patt'][$ftype][$abcd]);

        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $p[$i]['peilv2'] = (float) $tsql->f('peilv2');
        $p[$i]['mp1']    = (float) $tsql->f('mp1');
        $p[$i]['mp2']    = (float) $tsql->f('mp2');
        $cid             = $tsql->f('cid');
        $sid             = $tsql->f('sid');
        $bid             = $tsql->f('bid');
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['bid']    = $tsql->f('bid');
        $i++;
    }
    $p[0]['csid'] = $csid;
    $p[0]['ccid'] = $ccid;
    return $p;
}
function getp_1dw($bid, $ab, $abcd, $cid)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid;
    //$abcd = low($abcd);
    $time = time();
    $tsql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and name+1>0 order by bid,sid,xsort");
    $i    = 0;
    $p    = array();
    $cid  = 0;
    $sid  = 0;
    $csid = 1;
    $ccid = 1;
    $abcd = strtolower($abcd);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0)
            $csid++;
        if ($cid != $tsql->f('cid') & $cid != 0)
            $ccid++;
        if ($cid != $tsql->f('cid')){   
           $psql->query("select dftype,ftype,name from `$tb_class` where gid='$gid' and cid='".$tsql->f('cid')."'");
           $psql->next_record();
           $ftype           = $psql->f('ftype');
           $dftype           = $psql->f('dftype');
           $cname = $psql->f('name');
        }
        if ($sid != $tsql->f('sid')){
           $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype']  = $ftype;
        $p[$i]['dftype']  = $dftype;
        $p[$i]['bid']    = $tsql->f('bid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['sname']  = $sname;
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['cname']  = $cname;
        $p[$i]['pid']    = $tsql->f('pid');
        $p[$i]['name']   = $tsql->f('name');
        $p[$i]['ifok']   = $tsql->f('ifok');
        $p[$i]['xsort']  = $tsql->f('xsort');
        $p[$i]['peilv1'] = (float) ($tsql->f('peilv1') - $config['patt'][$ftype][$abcd]);

        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $p[$i]['peilv2'] = (float) $tsql->f('peilv2');
        $p[$i]['mp1']    = (float) $tsql->f('mp1');
        $p[$i]['mp2']    = (float) $tsql->f('mp2');
        $cid             = $tsql->f('cid');
        $sid             = $tsql->f('sid');
        $bid             = $tsql->f('bid');
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['bid']    = $tsql->f('bid');
        $i++;
    }
    $p[0]['csid'] = $csid;
    $p[0]['ccid'] = $ccid;
    return $p;
}
function getpsmc($bid, $ab, $abcd, $cid, $p)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid;
    //$abcd = low($abcd);
    $time = time();
    if (!is_numeric($p) | $p < 0 | $p > 10)
        $p = 0;
    $tsql->query("select count(id) from `$tb_play` where gid='$gid' and bid='$bid' and cid='$cid' and ifok=1");
    $tsql->next_record();
    $rcount = $tsql->f(0);
    if ($rcount < 200) {
        $tsql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and cid='$cid'  order by bid,sid,cid,xsort");
    } else {
        $tsql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and cid='$cid'  order by bid,sid,cid,xsort limit " . ($p * 100) . ",100");
    }
    $i    = 0;
    $p    = array();
    $cid  = 0;
    $sid  = 0;
    $csid = 0;
    $ccid = 0;
    $abcd = strtolower($abcd);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0)
            $csid++;
        if ($cid != $tsql->f('cid') & $cid != 0)
            $ccid++;
        if ($cid != $tsql->f('cid')){   
           $psql->query("select dftype,ftype,name from `$tb_class` where gid='$gid' and cid='".$tsql->f('cid')."'");
           $psql->next_record();
           $ftype           = $psql->f('ftype');
           $dftype           = $psql->f('dftype');
           $cname = $psql->f('name');
        }
        if ($sid != $tsql->f('sid')){
           $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype']  = $ftype;
        $p[$i]['dftype']  = $dftype;
        $p[$i]['bid']    = $tsql->f('bid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['sname']  = $sname;
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['cname']  = $cname;
        $p[$i]['pid']   = $tsql->f('pid');
        $p[$i]['ifok']  = $tsql->f('ifok');
        $p[$i]['name']  = $tsql->f('name');
        $p[$i]['xsort'] = $tsql->f('xsort');
        if ($abcd == 'a') {
            $p[$i]['peilv1'] = (float) $tsql->f('peilv1');
        } else {
            $p[$i]['peilv1'] = (float) ($tsql->f('peilv1') - $tsql->f($abcd));
        }
        $p[$i]['b'] = $tsql->f('b');
        $p[$i]['c'] = $tsql->f('c');
        $p[$i]['d'] = $tsql->f('d');
        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $p[$i]['peilv2'] = (float) $tsql->f('peilv2');
        $p[$i]['ztype']  = $tsql->f('ztype');
        $p[$i]['mp1']    = (float) $tsql->f('mp1');
        $p[$i]['mp2']    = $tsql->f('mp2');
        $cid             = $tsql->f('cid');
        $sid             = $tsql->f('sid');
        $bid             = $tsql->f('bid');
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['bid']    = $tsql->f('bid');
        $i++;
    }
    $p[0]['csid'] = $csid;
    $p[0]['ccid'] = $ccid;
    return $p;
}
function getpsmd($bid, $ab, $abcd, $cid, $sid)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid;
    //$abcd = low($abcd);
    $time = time();
    if ($sid != '' && $bid!='') {
        $tsql->query("select * from `$tb_play` where gid='$gid' and bid='$bid'  and sid='$sid' and ifok=1 order by bid,xsort");
    } else if ($sid != '') {
        $tsql->query("select * from `$tb_play` where gid='$gid' and sid='$sid' and ifok=1 order by bid,xsort");
    }else {
        $tsql->query("select * from `$tb_play` where gid='$gid' and bid='$bid'  and ifok=1  order by sid,xsort");
    }
    $i    = 0;
    $p    = array();
    $cid  = 0;
    $sid  = 0;
    $csid = 1;
    $ccid = 1;
    $abcd = strtolower($abcd);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0)
            $csid++;
        if ($cid != $tsql->f('cid') & $cid != 0)
            $ccid++;
        if ($cid != $tsql->f('cid')){   
           $psql->query("select dftype,ftype,name from `$tb_class` where gid='$gid' and cid='".$tsql->f('cid')."'");
           $psql->next_record();
           $ftype           = $psql->f('ftype');
           $dftype           = $psql->f('dftype');
           $cname = $psql->f('name');
        }
        if ($sid != $tsql->f('sid')){
           $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype']  = $ftype;
        $p[$i]['dftype']  = $dftype;
        $p[$i]['bid']    = $tsql->f('bid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['sname']  = $sname;
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['cname']  = $cname;
        $p[$i]['pid']    = $tsql->f('pid');
        $p[$i]['ifok']   = $tsql->f('ifok');
        $p[$i]['name']   = $tsql->f('name');
        $p[$i]['xsort']  = $tsql->f('xsort');
        $p[$i]['peilv1'] = (float) ($tsql->f('peilv1') - $config['patt'][$ftype][$abcd]);

        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $p[$i]['peilv2'] = (float) $tsql->f('peilv2');
        $p[$i]['mp1']    = (float) $tsql->f('mp1');
        $p[$i]['mp2']    = $tsql->f('mp2');
        $cid             = $tsql->f('cid');
        $sid             = $tsql->f('sid');
        $bid             = $tsql->f('bid');
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['bid']    = $tsql->f('bid');
        $i++;
    }
    $p[0]['csid'] = $csid;
    $p[0]['ccid'] = $ccid;
    return $p;
}
function getpsmq($bid, $ab, $abcd, $cid)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid;
    //$abcd = low($abcd);
    $time = time();
    $tsql->query("select * from `$tb_play` where gid='$gid' and bid='$bid'  order by bid,sid,cid,xsort");
    $i    = 0;
    $p    = array();
    $cid  = 0;
    $sid  = 0;
    $csid = 1;
    $ccid = 1;
    $abcd = strtolower($abcd);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0)
            $csid++;
        if ($cid != $tsql->f('cid') & $cid != 0)
            $ccid++;
        if ($cid != $tsql->f('cid')){   
           $psql->query("select dftype,ftype,name from `$tb_class` where gid='$gid' and cid='".$tsql->f('cid')."'");
           $psql->next_record();
           $ftype           = $psql->f('ftype');
           $dftype           = $psql->f('dftype');
           $cname = $psql->f('name');
        }
        if ($sid != $tsql->f('sid')){
           $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype']  = $ftype;
        $p[$i]['dftype']  = $dftype;
        $p[$i]['bid']    = $tsql->f('bid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['sname']  = $sname;
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['cname']  = $cname;
        $p[$i]['pid']    = $tsql->f('pid');
        $p[$i]['ifok']   = $tsql->f('ifok');
        $p[$i]['name']   = $tsql->f('name');
        $p[$i]['xsort']  = $tsql->f('xsort');
        $p[$i]['peilv1'] = (float) ($tsql->f('peilv1') - $config['patt'][$ftype][$abcd]);

        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $p[$i]['peilv2'] = (float) $tsql->f('peilv2');
        $p[$i]['mp1']    = (float) $tsql->f('mp1');
        $p[$i]['mp2']    = $tsql->f('mp2');
        $cid             = $tsql->f('cid');
        $sid             = $tsql->f('sid');
        $bid             = $tsql->f('bid');
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['bid']    = $tsql->f('bid');
        $i++;
    }
    $p[0]['csid'] = $csid;
    $p[0]['ccid'] = $ccid;
    return $p;
}
function getpsme($bid, $ab, $abcd, $sid)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid;
    $abcd = low($abcd);
    $time = time();
    if($bid==''){
       $tsql->query("select * from `$tb_play` where gid='$gid' and sid='$sid' order by bid,sid,xsort");
    }else if($sid=='' | $sid=='undefined'){
       $tsql->query("select * from `$tb_play` where gid='$gid' and bid='$bid'  order by bid,sid,xsort");
       
    }else{
       $tsql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by bid,sid,xsort");
    }
    $i    = 0;
    $p    = array();
    $cid  = 0;
    $sid  = 0;
    $csid = 1;
    $ccid = 1;
    $abcd = strtolower($abcd);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0)
            $csid++;
        if ($cid != $tsql->f('cid') & $cid != 0)
            $ccid++;
        if ($cid != $tsql->f('cid')){   
           $psql->query("select dftype,ftype,name from `$tb_class` where gid='$gid' and cid='".$tsql->f('cid')."'");
           $psql->next_record();
           $ftype           = $psql->f('ftype');
           $dftype           = $psql->f('dftype');
           $cname = $psql->f('name');
        }
        if ($sid != $tsql->f('sid')){
           $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype']  = $ftype;
        $p[$i]['dftype']  = $dftype;
        $p[$i]['bid']    = $tsql->f('bid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['sname']  = $sname;
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['cname']  = $cname;
        $p[$i]['pid']    = $tsql->f('pid');
        $p[$i]['ifok']   = $tsql->f('ifok');
        $p[$i]['name']   = $tsql->f('name');
        $p[$i]['xsort']  = $tsql->f('xsort');
        $p[$i]['peilv1'] = (float) ($tsql->f('peilv1') - $config['patt'][$ftype][$abcd]);

        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $p[$i]['peilv2'] = (float) $tsql->f('peilv2');
        $p[$i]['mp1']    = (float) $tsql->f('mp1');
        $p[$i]['mp2']    = $tsql->f('mp2');
        $p[$i]['znum1']    = $tsql->f('znum1');
        //$p[$i]['znum2']    = $tsql->f('znum2');
        $cid             = $tsql->f('cid');
        $sid             = $tsql->f('sid');
        $bid             = $tsql->f('bid');
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['bid']    = $tsql->f('bid');
        $i++;
    }
    $p[0]['csid'] = $csid;
    $p[0]['ccid'] = $ccid;
    return $p;
}
function getpsm60($bid, $ab, $abcd, $sid)
{
    global $tsql, $psql, $tb_play, $tb_bclass, $tb_sclass, $tb_class, $config, $userid, $tb_play_user, $gid, $redis;
    $abcd = low($abcd);
    $time = time();
	/*// 尝试从 Redis 中获取缓存的 $p
    $cacheKey = "getpsm60:{$gid}:{$sid}:{$abcd}:{$ab}:{$bid}";
    $cachedP = $redis->get($cacheKey);
    if ($cachedP !== false) {
        return unserialize($cachedP);
    }*/
	
    $tsql->query("select * from `$tb_play` where gid='$gid'  and sid='$sid' order by xsort");
    $i    = 0;
    $p    = array();
    $cid  = 0;
    $sid  = 0;
    $csid = 1;
    $ccid = 1;
    $abcd = strtolower($abcd);
    while ($tsql->next_record()) {
        if ($sid != $tsql->f('sid') & $sid != 0)
            $csid++;
        if ($cid != $tsql->f('cid') & $cid != 0)
            $ccid++;
        if ($cid != $tsql->f('cid')){   
           $psql->query("select dftype,ftype,name from `$tb_class` where gid='$gid' and cid='".$tsql->f('cid')."'");
           $psql->next_record();
           $ftype           = $psql->f('ftype');
           $dftype           = $psql->f('dftype');
           $cname = $psql->f('name');
        }
        if ($sid != $tsql->f('sid')){
           $sname = transs('name', $tsql->f('sid'));
        }
        $p[$i]['ftype']  = $ftype;
        $p[$i]['dftype']  = $dftype;
        $p[$i]['bid']    = $tsql->f('bid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['sname']  = $sname;
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['cname']  = $cname;
        $p[$i]['pid']    = $tsql->f('pid');
        $p[$i]['ifok']   = $tsql->f('ifok');
        $p[$i]['name']   = $tsql->f('name');
        $p[$i]['xsort']  = $tsql->f('xsort');
        $p[$i]['peilv1'] = (float) ($tsql->f('peilv1') - $config['patt'][$ftype][$abcd]);

        if ($config['pan'][$ftype]['ab'] == 1 & ($ab == 'B' | $ab == 'b')) {
            $p[$i]['peilv1'] += $config['patt'][$ftype]['ab'];
        }
        $p[$i]['peilv2'] = (float) $tsql->f('peilv2');
        $p[$i]['mp1']    = (float) $tsql->f('mp1');
        $p[$i]['mp2']    = $tsql->f('mp2');
        $cid             = $tsql->f('cid');
        $sid             = $tsql->f('sid');
        $bid             = $tsql->f('bid');
        $p[$i]['cid']    = $tsql->f('cid');
        $p[$i]['sid']    = $tsql->f('sid');
        $p[$i]['bid']    = $tsql->f('bid');
        $i++;
    }
    $p[0]['csid'] = $csid;
    $p[0]['ccid'] = $ccid;
	//$redis->set($cacheKey, serialize($p));
    return $p;
}
function ftypes($ftype){
    $arr = array();
    foreach($ftype as $k => $v){
        $arr[$k] = $v['name'];
    }
    return $arr;
}
function getuserpeilvcha($uid, $class)
{
    global $tb_user, $tb_zpan, $psql, $gid;
    $peilv = 0;
    while ($uid != 99999999) {
        $psql->query("select peilvcha from `$tb_zpan` where gid='$gid' and userid='$uid' and class='$class' ");
        $psql->next_record();
        $peilv += $psql->f('peilvcha');
        $psql->query("select fid from `$tb_user` where userid='$uid'");
        $psql->next_record();
        $uid = $psql->f('fid');
        if ($uid == 99999999)
            break;
    }
    return $peilv;
}

function getuserpeilvchas($uid, $class,$fenlei)
{
    global $tb_user, $tb_zpan, $psql, $gid;
    $peilv = 0;
    while ($uid != 99999999) {
        $psql->query("select peilvcha from `$tb_zpan` where gid='$gid' and userid='$uid' and class='$class' ");
        $psql->next_record();
        $peilv += $psql->f('peilvcha');
        $psql->query("select fid from `$tb_user` where userid='$uid'");
        $psql->next_record();
        $uid = $psql->f('fid');
        if ($uid == 99999999)
            break;
    }
    return $peilv;
}
function getuserpeilvcha2($uid, $class)
{
    global $tb_user, $tb_zpan, $psql, $gid;
    $peilv = 0;
    $layer = transuser($uid,'layer');
    while ($layer>1) {
        $psql->query("select peilvcha from `$tb_zpan` where gid='$gid' and userid='$uid' and class='$class' ");
        $psql->next_record();
        $peilv += $psql->f('peilvcha');
        $psql->query("select fid,layer from `$tb_user` where userid='$uid'");
        $psql->next_record();
        $uid = $psql->f('fid');
        $layer = $psql->f('layer');
        if ($layer==2)
            break;
    }
    return $peilv;
}

function getuserpeilvcha2s($uid, $class,$fenlei)
{
    global $tb_user, $tb_zpan, $psql, $gid;
    $peilv = 0;
    $layer = transuser($uid,'layer');
    while ($layer>1) {
        $psql->query("select peilvcha from `$tb_zpan` where gid='$gid' and userid='$uid' and class='$class' ");
        $psql->next_record();
        $peilv += $psql->f('peilvcha');
        $psql->query("select fid,layer from `$tb_user` where userid='$uid'");
        $psql->next_record();
        $uid = $psql->f('fid');
        $layer = $psql->f('layer');
        if ($layer==2)
            break;
    }
    return $peilv;
}
function week()
{
    global $config;
    $start = str_replace(':','',$config['editstart']);
    $zuo = 0;
    if(date("His")<$start) $zuo=1;
    $getWeekDay = date("w");
    if ($getWeekDay == 0) {
        $sdate = array(
            0 => date("Y-m-d", mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))),
            1 => date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y'))),
            2 => date('Y-m-d', mktime(0, 0, 0, date('n'), date('t'), date('Y'))),
            3 => date('Y-m-01', strtotime('last month')),
            4 => date('Y-m-t', strtotime('last month')),
            5 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 1 - 7, date("Y"))),
            6 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 7 - 7, date("Y"))),
            7 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 1 - 7 - 7, date("Y"))),
            8 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 7 - 7 - 7, date("Y"))),
            9 => date("Y-m-d", mktime(0, 0, 0, date('m') - 1, date('d') - 4, date('Y'))),
            10 => date("Y-m-d")
        );

    } else if ($getWeekDay == 1 && $zuo==1) {
        $sdate = array(
            0 => date("Y-m-d", mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))),
            1 => date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y'))),
            2 => date('Y-m-d', mktime(0, 0, 0, date('n'), date('t'), date('Y'))),
            3 => date('Y-m-01', strtotime('last month')),
            4 => date('Y-m-t', strtotime('last month')),
            5 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 1 - 7, date("Y"))),
            6 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 7 - 7, date("Y"))),
            7 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 1 - 7 - 7, date("Y"))),
            8 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 7 - 7 - 7, date("Y"))),
            9 => date("Y-m-d", mktime(0, 0, 0, date('m') - 1, date('d') - 4, date('Y'))),
            10 => date("Y-m-d")
        );

    } else {
        $sdate = array(
            0 => date("Y-m-d", mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))),
            1 => date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y'))),
            2 => date('Y-m-d', mktime(0, 0, 0, date('n'), date('t'), date('Y'))),
            3 => date('Y-m-01', strtotime('last month')),
            4 => date('Y-m-t', strtotime('last month')),
            5 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 1, date("Y"))),
            6 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 7, date("Y"))),
            7 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 1 - 7, date("Y"))),
            8 => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $getWeekDay + 7 - 7, date("Y"))),
            9 => date("Y-m-d", mktime(0, 0, 0, date('m') - 1, date('d') - 4, date('Y'))),
            10 => date("Y-m-d")
        );
    }
       if($zuo==1){
          $sdate[0] = date("Y-m-d", mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')));
          $sdate[10] = date("Y-m-d",time()-86400);
          
        }
    return $sdate;
}
function setuptid()
{
    global $tsql,$userid,$tb_lib;
    $tsql->query("select max(tid) from `$tb_lib` where userid='$userid'");
    $tsql->next_record();
    if ($tsql->f(0) == '') {
        return '20000000';
    }
    return $tsql->f(0)+rand(1,3);
}
function getma(){
   global $psql,$tb_config;
   $psql->query("select ma from `$tb_config`");
   $psql->next_record();
   $ma = json_decode($psql->f('ma'),true);
   return $ma;
}
function getduoarr($name){
        if(strpos($name,'肖')>0){
           $pl = array("鼠","牛","虎","兔","龍","蛇","馬","羊","猴","雞","狗","豬");
        }else if(strpos($name,'尾')>0){
           $pl = array("0尾","1尾","2尾","3尾","4尾","5尾","6尾","7尾","8尾","9尾");
        }else{
           $pl = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49");
        }
        return $pl;
}
function getduoarrss($gid,$name){
    
    if($gid==101 || $gid==163){   
        if((strpos('['.$name.']','组')>0)){
            $pl = array("0","1","2","3","4","5","6","7","8","9");
        }else{  
              $names = str_replace('定位','',$name);
              $nl = strlen($names);     
            if($nl==6){             
                $pl = array(substr($names,0,$nl/2)."-0","1","2","3","4","5","6","7","8","9",substr($names,$nl/2)."-0","1","2","3","4","5","6","7","8","9");
            }else{ 
               if(strpos('['.$name.']','前三')){
                  $pl = array("万-0","1","2","3","4","5","6","7","8","9","千-0","1","2","3","4","5","6","7","8","9","百-0","1","2","3","4","5","6","7","8","9");
               }else if(strpos('['.$name.']','中三')){
                  $pl = array("千-0","1","2","3","4","5","6","7","8","9","百-0","1","2","3","4","5","6","7","8","9","十-0","1","2","3","4","5","6","7","8","9");
               }else{
                  $pl = array("百-0","1","2","3","4","5","6","7","8","9","十-0","1","2","3","4","5","6","7","8","9","个-0","1","2","3","4","5","6","7","8","9");
               }
            }
        }
    }else if($gid==121){
        if($name=='选前二直选'){
            $pl = array("第1球-01","02","03","04","05","06","07","08","09","10","11","第2球-01","02","03","04","05","06","07","08","09","10","11");
        }else if($name=='选前三直选'){
            $pl = array("第1球-01","02","03","04","05","06","07","08","09","10","11","第2球-01","02","03","04","05","06","07","08","09","10","11","第3球-01","02","03","04","05","06","07","08","09","10","11");
        }else{
        $pl = array("01","02","03","04","05","06","07","08","09","10","11");
        }
    }else if($gid==107){
        $pl = array("1","2","3","4","5","6","7","8","9","10");

    }else if($gid==103){
        if($name=='选二连直'){
            $pl = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20");
        }else if($name=='选三前直'){
            $pl = array("第1球-01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","第2球-01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","第3球-01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20");
        }else{
           $pl = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20");
        }
    }
    return $pl;
}
function getduoarrssuser($gid,$name){
    
    if($gid==101 || $gid==163){   
        if((strpos('['.$name.']','组')>0)){
            $pl = array("0","1","2","3","4","5","6","7","8","9");
        }else{  
              $names = str_replace('定位','',$name);
              $nl = strlen($names);     
            if($nl==6){             
                $pl = array("0","1","2","3","4","5","6","7","8","9","0","1","2","3","4","5","6","7","8","9");
            }else{ 
               if(strpos('['.$name.']','前三')){
                  $pl = array("0","1","2","3","4","5","6","7","8","9","0","1","2","3","4","5","6","7","8","9","0","1","2","3","4","5","6","7","8","9");
               }else if(strpos('['.$name.']','中三')){
                  $pl = array("0","1","2","3","4","5","6","7","8","9","0","1","2","3","4","5","6","7","8","9","0","1","2","3","4","5","6","7","8","9");
               }else{
                  $pl = array("0","1","2","3","4","5","6","7","8","9","0","1","2","3","4","5","6","7","8","9","0","1","2","3","4","5","6","7","8","9");
               }
            }
        }
    }else if($gid==121){
        if($name=='选前二直选'){
            $pl = array("01","02","03","04","05","06","07","08","09","10","11","01","02","03","04","05","06","07","08","09","10","11");
        }else if($name=='选前三直选'){
            $pl = array("01","02","03","04","05","06","07","08","09","10","11","01","02","03","04","05","06","07","08","09","10","11","01","02","03","04","05","06","07","08","09","10","11");
        }else{
        $pl = array("01","02","03","04","05","06","07","08","09","10","11");
        }
    }else if($gid==107){
        $pl = array("1","2","3","4","5","6","7","8","9","10");

    }else if($gid==103){
        if($name=='选二连直'){
            $pl = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20");
        }else if($name=='选三前直'){
            $pl = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20");
        }else{
           $pl = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20");
        }
    }
    return $pl;
}
function rduokey($arr,$v1){
   foreach($arr as $key => $v2){
       if($v2==$v1){
           return $key;
       }
   }
}
function rduokeydw($arr,$v1,$kk){
   foreach($arr as $key => $v2){
       if($v2==$v1){
           return $key+$kk*10;
       }
   }
}
function rduokeyklsf($arr,$v1,$kk){
   foreach($arr as $key => $v2){
       if($v2==$v1){
           return $key+$kk*20;
       }
   }
}
function rduokeysyxw($arr,$v1,$kk){
   foreach($arr as $key => $v2){
       if($v2==$v1){
           return $key+$kk*11;
       }
   }
}
function formatqs($gid, $qs)
{
    if ($gid == 113 | $gid == 123)
        $qs = substr($qs, 0, 8) . substr($qs, -2);
    else if ( $gid==152 | $gid==153 | $gid==155)
        $qs = substr($qs, -9);
    else if ($gid == 121 | $gid == 125)
        $qs = substr($qs, 0, 8) . substr($qs, -2);
    return $qs;
}

function kjmyselfs($gid,$cs,$qishu,$mnum,$fenlei,$dates){
    global $tb_lib,$tb_user,$tb_play,$tb_class,$psql;
    $psql->query("select sum(je) from `$tb_lib` where gid='$gid' and qishu='$qishu'");
    $psql->next_record();
    $zje = $psql->f(0);
    if($zje<=$cs['kongje']){
        $m = randbm($gid,$qishu,$mnum); 
    }else{
        if($fenlei==107){
            pk10myself($gid,$cs,$qishu,$mnum,$zje,$dates);
        }else if($fenlei==100){
            sscmyself($gid,$cs,$qishu,$mnum,$zje,$dates);
        }
    }
    $kj = array();
    $kj[0] = array();
    $kj[0]['kjtime'] = time();
    $kj[0]['qishu'] = $qishu;
    $kj[0]['m'] = $m;
    return json_encode($kj);
}
function klsfmyself($gid, $cs, $qishu, $mnum, $dates) {
    return randbm($gid, $qishu, 8);
}
function kl8myself($gid, $cs, $qishu, $mnum, $dates) {
    return randbm($gid, $qishu, 20);
}
function k3myself($gid, $cs, $qishu, $mnum, $dates) {
    return randbm($gid, $qishu, 3);
}
function pcddmyself($gid, $cs, $qishu, $mnum, $dates) {
    return randbm($gid, $qishu, 4);
}
function syxwmyself($gid, $cs, $qishu, $mnum, $dates) {
    return randbm($gid, $qishu, 6);
}
function lhcmyself($gid, $cs, $qishu, $mnum, $dates) {
    return randbm($gid, $qishu, 7);
}

function sscmyself($gid, $cs, $qishu, $mnum, $dates,&$trys) {
    global $tb_lib, $tb_user, $tb_play, $tb_class, $psql,$tb_kjinfo;
    $psql->query("select sum(je*zc0/100) as je from `$tb_lib` where gid='$gid' and qishu='$qishu' ");
    $psql->next_record();
    $benqije = $psql->f('je');
    if ($benqije < $cs['kongje']) {
        return randbm($gid, $qishu, 5);
    }
    $psql->query("select sum(je*zc0/100),sum(peilv11*je*zc0/100),sum(je*zc0*points1/100*100) from `$tb_lib` where gid='$gid' and dates='$dates' and qishu!='$qishu' and z in(0,1)");
    $psql->next_record();
    $zje = $psql->f(0);
    $points = $psql->f(2);
    $zhong = $psql->f(1);
    $yk = $zje - $points - $zhong;
    if ($yk > $cs['ylup']) {
        $fanjiang = $benqije + $yk - $cs['ylup'] + 10;
    } else {
        $fanjiang = $benqije * $cs['fanjianglv'] / 100;
    }
    //echo $fanjiang;
    $rs = $psql->arr("select * from `$tb_play` where gid='$gid' and ztype=0 and bid=23378755", 1);
    $play = [];
    $mtype = [];
    $parr = [];
    $sarr = [];
    foreach ($rs as $k => $v) {
        $pid = $v['pid'];
        $parr[] = $pid;
        $play['p' . $pid]['rand'] = rand(1, 9999);
        $play['p' . $pid]['je'] = 0;
        $play['p' . $pid]['je'] = 0;
        $play['p' . $pid]['shui'] = 0;
        $play['p' . $pid]['pid'] = $v['pid'];
        $play['p' . $pid]['cid'] = $v['cid'];
        $play['p' . $pid]['sid'] = $v['sid'];
        $play['p' . $pid]['name'] = $v['name'];
        $sarr['s' . $v['sid']]['sid'][] = $pid;
        $sarr['s' . $v['sid']]['z'] = 0;
        if ($mtype['c' . $v['cid']] == '') {
            $psql->query("select mtype,cid from `$tb_class` where gid='$gid' and cid='{$v['cid']}'");
            $psql->next_record();
            $mtype['c' . $v['cid']] = $psql->f('mtype');
        }
        $play['p' . $pid]['mtype'] = $mtype['c' . $v['cid']];
    }
    //print_r($mtype);
    $rs = $psql->arr("select sum(je*zc0/100) as je,pid,sid,cid,bid,peilv11,sum(peilv11*je*zc0/100) as zhong,sum(je*points1*zc0/100*100) as shui from `$tb_lib` where gid='$gid' and qishu='$qishu' and bid=23378755 group by pid", 1);
    $mtype = [];
    foreach ($rs as $k => $v) {
        $psql->query("select name,pid,sid,cid,bid from `$tb_play` where gid='$gid' and pid='" . $v['pid'] . "'");
        $psql->next_record();
        $pname = $psql->f('name');
        $pid = $v['pid'];
        $cid = $v['cid'];
        if ($mtype['c' . $v['cid']] == '') {
            $psql->query("select mtype,cid from `$tb_class` where gid='$gid' and cid='{$v['cid']}'");
            $psql->next_record();
            $mtype['c' . $v['cid']] = $psql->f('mtype');
        }
        if ($mtype['c' . $v['cid']] >= 5) continue;
        $smarr = ['单', '双', '大', '小'];
        $sarr['s' . $v['sid']]['z']+= $v['zhong'];
        if (is_numeric($pname)) {
            $play['p' . $pid]['je']+= $v['je'];
            $play['p' . $pid]['zhong']+= $v['zhong'];
            $play['p' . $pid]['shui']+= $v['shui'];
        } else if (in_array($pname, $smarr)) {
            switch ($pname) {
                case "大":
                    $arr = [5, 6, 7, 8, 9];
                break;
                case "小":
                    $arr = [0, 1, 2, 3, 4];
                break;
                case "单":
                    $arr = [1, 3, 5, 7, 9];
                break;
                case "双":
                    $arr = [0, 2, 4, 6, 8];
                break;
            }
            $pr = $psql->arr("select * from `$tb_play` where gid='$gid' and sid='{$v['sid']}' and ztype=0 and name in (" . implode(',', $arr) . ")", 1);
            foreach ($pr as $k1 => $v1) {
                $play['p' . $v1['pid']]['je']+= $v['je'] / 5;
                $play['p' . $v1['pid']]['zhong']+= $v['zhong'];
                $play['p' . $v1['pid']]['shui']+= $v['shui'] / 5;
            }
        }
    }

    $playz = [];
    foreach ($play as $k => $v) {
        if ($v['zhong'] > 0) {
            $playz[$k] = $v;
            $sarrz['s' . $v['sid']][] = $v['pid'];
        }
    }
    shuffle($sarrz);
    $nosid = [];
    $je = 0;
    $m = [];
    if ($cs['kzmode'] == 0) {
        foreach ($sarrz as $k => $v) {
            $a = 0;
            while (1) {
                $key = 'p' . $v[rand(0, count($v) - 1) ];
                if (($je + $playz[$key]['zhong']) <= $fanjiang) {
                    $je+= $playz[$key]['zhong'];
                    $m[$play[$key]['mtype']] = $playz[$key]['name'];
                    $nosid[] = $playz[$key]['sid'];
                }
                $a++;
                if ($a > 200 || $m[$play[$key]['mtype']] != '') {
                    break;
                }
            }
            if ($je >= $fanjiang) {
                break;
            }
        }
    } else {
        foreach ($sarrz as $k => $v) {
            $a = 0;
            while (1) {
                $key = 'p' . $v[rand(0, count($v) - 1) ];
                if (($je + $playz[$key]['zhong']) <= $fanjiang && $je + $playz[$key]['zhong'] < $benqije) {
                    $je+= $playz[$key]['zhong'];
                    $m[$play[$key]['mtype']] = $playz[$key]['name'];
                    $nosid[] = $playz[$key]['sid'];
                }
                $a++;
                if ($a > 200 || $m[$play[$key]['mtype']] != '') {
                    break;
                }
            }
            if ($je >= $fanjiang) {
                break;
            }
        }
    }
    $pidarr = [];
    $sidarr = [];
    $pnamearr = [];
    //shuffle($sarr);
    $tmp = [];
    foreach ($sarr as $k => $v) {
        $tmp[] = $v['z'];
    }
    array_multisort($tmp, SORT_DESC, SORT_NUMERIC, $sarr);
    $zje = 0;
    foreach ($sarr as $kk => $vv) {
        if (in_array(str_replace('s', '', $kk), $nosid)) continue;
        $nopid = [];
        $tmp = $vv['sid'];
        $zje+= $vv['z'];
        while (1) {
            $ct = count($tmp) - 1;
            $tkey = rand(0, $ct);
            $key = 'p' . $tmp[$tkey];
            if ($je + abs($play[$key]['zhong']) > $fanjiang) {
                $ks = abs($play[$key]['zhong']);
                $pid = $play[$key]['pid'];
                $rands = rand(0, 1);
                foreach ($play as $k => $v) {
                    if ($v['sid'] == $play[$key]['sid'] && abs($v['zhong']) == $ks && $rands == 1) {
                        $pid = $v['pid'];
                        $ks = abs($v['zhong']);
                    }
                    if ($v['sid'] == $play[$key]['sid'] && abs($v['zhong']) < $ks) {
                        $pid = $v['pid'];
                        $ks = abs($v['zhong']);
                    }
                }
                $key = 'p' . $pid;
                $je+= abs($play[$key]['zhong']);
                $m[$play[$key]['mtype']] = $play[$key]['name'];
                $pidarr[$play[$key]['mtype']] = $play[$key]['pid'];
                $sidarr[$play[$key]['mtype']] = $play[$key]['sid'];
                $pnamearr[$play[$key]['mtype']] = $play[$key]['name'];
                $i++;
                break;
            } else {
                $je+= abs($play[$key]['zhong']);
                $m[$play[$key]['mtype']] = $play[$key]['name'];
                $pidarr[$play[$key]['mtype']] = $play[$key]['pid'];
                $sidarr[$play[$key]['mtype']] = $play[$key]['sid'];
                $pnamearr[$play[$key]['mtype']] = $play[$key]['name'];
                $i++;
                break;
            }
        }
    }
    $ms = [];
    for ($i = 0;$i < 5;$i++) {
        $ms[$i] = $m[$i];
    }
    $hes = $m[0] + $m[1] + $m[2] + $m[3] + $m[4];
    $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378763' and name='龙'");
    $psql->next_record();
    $pid = $psql->f('pid');
    $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
    $psql->next_record();
    $long = $psql->f('zhong');
    $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378763' and name='虎'");
    $psql->next_record();
    $pid = $psql->f('pid');
    $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
    $psql->next_record();
    $hu = $psql->f('zhong');
    $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378763' and name='和'");
    $psql->next_record();
    $pid = $psql->f('pid');
    $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
    $psql->next_record();
    $he = $psql->f('zhong');
    $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378763' and name='总大'");
    $psql->next_record();
    $pid = $psql->f('pid');
    $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
    $psql->next_record();
    $zd = $psql->f('zhong');
    $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378763' and name='总小'");
    $psql->next_record();
    $pid = $psql->f('pid');
    $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
    $psql->next_record();
    $zx = $psql->f('zhong');
    $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378763' and name='总单'");
    $psql->next_record();
    $pid = $psql->f('pid');
    $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
    $psql->next_record();
    $zdd = $psql->f('zhong');
    $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378763' and name='总双'");
    $psql->next_record();
    $pid = $psql->f('pid');
    $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
    $psql->next_record();
    $zs = $psql->f('zhong');
    $zharr = ['long'=>$long,'hu'=>$hu,'he'=>$he,'zd'=>$zd,'zx'=>$zx,'zdd'=>$zdd,'zs'=>$zs];

    /*
    if ($hes <= 22 && $zd > $zx && $je < $fanjiang && $je + $zd - $zx <= $fanjiang) {
        for ($i = 1;$i < 5;$i++) {
            $ms[$i] = rand(6, 9);
        }
    }
    if ($hes >= 23 && $zx > $zd && $je < $fanjiang && $je + $zx - $zd <= $fanjiang) {
        for ($i = 1;$i < 5;$i++) {
            $ms[$i] = rand(0, 3);
        }
    }
    */

    $mm = [];
    $mm[0]['m'] = $ms;
    $mm[0]['qishu'] = $qishu;
    $mm[0]['kjtime'] = time();
    $arr = checkssc($play, $mm, $fanjiang, $cs, $benqije,$zharr);
    $psql->query("insert into `$tb_kjinfo` set ma='{$arr['m']}',gid='$gid',qishu='$qishu',je='{$arr['je']}'");
    if (!$arr['flag'] && $trys > 0) {
        $trys--;
        if($trys==0){
            if($cs['kzmode']==1){
                $psql->query("select ma from `$tb_kjinfo` where gid='$gid' and qishu='$qishu' order by je limit 1");
                $psql->next_record();
            }else{
                $psql->query("select ma from `$tb_kjinfo` where gid='$gid' and qishu='$qishu' and je<='$fanjiang' order by je desc limit 1");  
                $psql->next_record();
                if($psql->f('ma')==""){
                    $trys=20;
                    return sscmyself($gid, $cs, $qishu, $mnum, $dates,$trys);
                }
            } 
            $mm[0]['m'] = json_decode($psql->f('ma'),true);
        }else{
            return sscmyself($gid, $cs, $qishu, $mnum, $dates,$trys);
        }

    }
    return json_encode($mm);
}
function checkssc($play, $mm, $fanjiang, $cs, $benqije,$zharr) {
    $je = 0;
    foreach ($play as $k => $v) {
        foreach ($mm[0]['m'] as $k1 => $v1) {
            if ($v['mtype'] == $k1 && $v['name'] == $v1) {
                $je+= $v['zhong'];
            }
        }
    }
    if($mm[0]['m'][0]>$mm[0]['m'][4]){
        $je += $zharr['long'];
    }else if($mm[0]['m'][0]<$mm[0]['m'][4]){
        $je += $zharr['hu'];
    }else{
        $je += $zharr['he'];
    }
    $he += $mm[0]['m'][0]+$mm[0]['m'][1]+$mm[0]['m'][2]+$mm[0]['m'][3]+$mm[0]['m'][4];
    if($he<=22){
        $je += $zharr['zx'];
    }else{
        $je += $zharr['zd'];
    }
    if($he%2==0){
        $je += $zharr['zs'];
    }else{
        $je += $zharr['zdd'];
    }
    $arr['je'] = $je;
    $arr['m'] = json_encode($mm[0]['m']);
    if ($cs['kzmode'] == 1) {
        if ($je > $fanjiang || $je > $benqije) {
            $arr['flag'] = false;
        } else {
            $arr['flag'] = true;
        }
    } else {
        if ($je > $fanjiang || $je < $benqije) {
            $arr['flag'] = false;
        } else {
            $arr['flag'] = true;
        }
    }
    return $arr;
}
function pk10myself($gid, $cs, $qishu, $mnum, $dates,&$trys) {
    global $tb_lib, $tb_user, $tb_play, $tb_class, $psql,$tb_kjinfo;
    $psql->query("select sum(je*zc0/100) as je from `$tb_lib` where gid='$gid' and qishu='$qishu' ");
    $psql->next_record();
    $benqije = $psql->f('je');
    if ($benqije < $cs['kongje']) {
        return randbm($gid, $qishu, 10);
    }
    $psql->query("select sum(je*zc0/100),sum(peilv11*je*zc0/100),sum(je*zc0*points1/100*100) from `$tb_lib` where gid='$gid' and dates='$dates' and qishu!='$qishu' and z in(0,1)");
    $psql->next_record();
    $zje = $psql->f(0);
    $points = $psql->f(2);
    $zhong = $psql->f(1);
    $yk = $zje - $points - $zhong;
    if ($yk > $cs['ylup']) {
        $fanjiang = $benqije + $yk - $cs['ylup'];
    } else {
        $fanjiang = $benqije * $cs['fanjianglv'] / 100;
    }
    $rs = $psql->arr("select * from `$tb_play` where gid='$gid' and ztype=0 and bid!=23378805", 1);
    $play = [];
    $mtype = [];
    $parr = [];
    $sarr = [];
    foreach ($rs as $k => $v) {
        $pid = $v['pid'];
        $parr[] = $pid;
        $play['p' . $pid]['rand'] = rand(1, 9999);
        $play['p' . $pid]['je'] = 0;
        $play['p' . $pid]['shui'] = 0;
        $play['p' . $pid]['pid'] = $v['pid'];
        $play['p' . $pid]['cid'] = $v['cid'];
        $play['p' . $pid]['sid'] = $v['sid'];
        $play['p' . $pid]['name'] = $v['name'];
        $sarr['s' . $v['sid']]['sid'][] = $pid;
        $sarr['s' . $v['sid']]['z'] = 0;
        if ($mtype['c' . $v['cid']] == '') {
            $psql->query("select mtype,cid from `$tb_class` where gid='$gid' and cid='{$v['cid']}'");
            $psql->next_record();
            $mtype['c' . $v['cid']] = $psql->f('mtype');
        }
        $play['p' . $pid]['mtype'] = $mtype['c' . $v['cid']];
        //$psql->query("select sum(je) as je from `$tb_lib` where gid='$gid' and qishu='$qishu' and sid='".$v['sid']."'");
        
    }
    $rs = $psql->arr("select sum(je*zc0/100) as je,pid,sid,cid,bid,peilv11,sum((peilv11)*je*zc0/100) as zhong,sum(je*points1*zc0/100*100) as shui from `$tb_lib` where gid='$gid' and qishu='$qishu'  and bid!=23378805 group by pid", 1);
    $mtype = [];
    foreach ($rs as $k => $v) {
        $psql->query("select name,pid,cid,bid from `$tb_play` where gid='$gid' and pid='" . $v['pid'] . "'");
        $psql->next_record();
        $pname = $psql->f('name');
        $pid = $v['pid'];
        $cid = $v['cid'];
        if ($mtype['c' . $v['cid']] == '') {
            $psql->query("select mtype,cid from `$tb_class` where gid='$gid' and cid='{$v['cid']}'");
            $psql->next_record();
            $mtype['c' . $v['cid']] = $psql->f('mtype');
        }
        if ($mtype['c' . $v['cid']] >= 10) continue;
        $smarr = ['单', '双', '大', '小'];
        $sarr['s' . $v['sid']]['z']+= $v['zhong'];
        if (is_numeric($pname)) {
            $play['p' . $pid]['je']+= $v['je'];
            $play['p' . $pid]['zhong']+= $v['zhong'];
            $play['p' . $pid]['shui']+= $v['shui'];
        } else {
            if ($pname == '虎' || $pname == '龙') continue;
            switch ($pname) {
                case "大":
                    $arr = ["06", "07", "08", "09", "10"];
                break;
                case "小":
                    $arr = ["01", "02", "03", "04", "05"];
                break;
                case "单":
                    $arr = ["01", "03", "05", "07", "09"];
                break;
                case "双":
                    $arr = ["02", "04", "06", "08", "10"];
                break;
                default:
                    continue;
                break;
            }
            $pr = $psql->arr("select * from `$tb_play` where gid='$gid' and sid='{$v['sid']}' and ztype=0 and name in (" . implode(',', $arr) . ")", 1);
            foreach ($pr as $k1 => $v1) {
                $play['p' . $v1['pid']]['je']+= $v['je'] / 5;
                $play['p' . $v1['pid']]['zhong']+= $v['zhong'];
                $play['p' . $v1['pid']]['shui']+= $v['shui'] / 5;
            }
        }
    }
    $playz = [];
    $playzno = [];
    foreach ($play as $k => $v) {
        if ($v['zhong'] > 0) {
            $playz[$k] = $v;
            $sarrz['s' . $v['sid']][] = $v['pid'];
        } else {
            $playzno[$k] = $v;
            $sarrzno['s' . $v['sid']][] = $v['pid'];
        }
    }
    shuffle($sarrz);
    $nosid = [];
    $je = 0;
    $m = [];
    if ($cs['kzmode'] == 0) {
        foreach ($sarrz as $k => $v) {
            $a = 0;
            while (1) {
                $key = 'p' . $v[rand(0, count($v) - 1) ];
                if (($je + $playz[$key]['zhong']) <= $fanjiang) {
                    $je+= $playz[$key]['zhong'];
                    $m[$play[$key]['mtype']] = $playz[$key]['name'];
                    $nosid[] = $playz[$key]['sid'];
                }
                $a++;
                if ($a > 200 || $m[$play[$key]['mtype']] != '') {
                    break;
                }
            }
            if ($je >= $fanjiang) {
                break;
            }
        }
    } else {
        foreach ($sarrz as $k => $v) {
            $a = 0;
            while (1) {
                $key = 'p' . $v[rand(0, count($v) - 1) ];
                if (($je + $playz[$key]['zhong']) <= $fanjiang && $je + $playz[$key]['zhong'] < $benqije) {
                    $je+= $playz[$key]['zhong'];
                    $m[$play[$key]['mtype']] = $playz[$key]['name'];
                    $nosid[] = $playz[$key]['sid'];
                }
                $a++;
                if ($a > 200 || $m[$play[$key]['mtype']] != '') {
                    break;
                }
            }
            if ($je >= $fanjiang) {
                break;
            }
        }
    }
    $pidarr = [];
    $sidarr = [];
    $pnamearr = [];
    $tmp = [];
    foreach ($sarr as $k => $v) {
        $tmp[] = $v['z'];
    }
    array_multisort($tmp, SORT_DESC, SORT_NUMERIC, $sarr);
    foreach ($sarr as $kk => $vv) {
        if (in_array(str_replace('s', '', $kk), $nosid)) continue;
        $nopid = [];
        $tmp = $vv['sid'];
        while (1) {
            $ct = count($tmp) - 1;
            $tkey = rand(0, $ct);
            $key = 'p' . $tmp[$tkey];
            if ($je + abs($play[$key]['zhong']) > $fanjiang && !in_array($play[$key]['pid'], $nopid)) {
                $ks = abs($play[$key]['zhong']);
                $pid = $play[$key]['pid'];
                $rands = rand(0, 1);
                foreach ($play as $k => $v) {
                    if ($v['sid'] == $play[$key]['sid'] && !in_array($v['pid'], $nopid) && abs($v['zhong']) == $ks && $rands == 1) {
                        $pid = $v['pid'];
                        $ks = abs($v['zhong']);
                    }
                    if ($v['sid'] == $play[$key]['sid'] && !in_array($v['pid'], $nopid) && abs($v['zhong']) < $ks) {
                        $pid = $v['pid'];
                        $ks = abs($v['zhong']);
                    }
                }
                $key = 'p' . $pid;
                if (!in_array($play[$key]['name'], $m)) {
                    $je+= abs($play[$key]['zhong']);
                    $m[$play[$key]['mtype']] = $play[$key]['name'];
                    $pidarr[$play[$key]['mtype']] = $play[$key]['pid'];
                    $sidarr[$play[$key]['mtype']] = $play[$key]['sid'];
                    $pnamearr[$play[$key]['mtype']] = $play[$key]['name'];
                    break;
                } else {
                    if (!in_array($play[$key]['pid'], $nopid)) {
                        $nopid[] = $play[$key]['pid'];
                    }
                    foreach ($tmp as $ks => $vs) {
                        if ($vs == $play[$key]['pid']) {
                            unset($tmp[$ks]);
                        }
                    }
                    $tmp = array_values($tmp);
                }
            } else {
                if (!in_array($play[$key]['name'], $m)) {
                    $je+= abs($play[$key]['zhong']);
                    $m[$play[$key]['mtype']] = $play[$key]['name'];
                    $pidarr[$play[$key]['mtype']] = $play[$key]['pid'];
                    $sidarr[$play[$key]['mtype']] = $play[$key]['sid'];
                    $pnamearr[$play[$key]['mtype']] = $play[$key]['name'];
                    break;
                } else {
                    if (!in_array($play[$key]['pid'], $nopid)) {
                        $nopid[] = $play[$key]['pid'];
                    }
                    unset($tmp[$tkey]);
                    $tmp = array_values($tmp);
                }
            }
        }
    }
    $ms = [];
    for ($i = 0;$i < 10;$i++) {
        $ms[$i] = $m[$i];
    }
    $zharr=[];
    for ($i = 0;$i < 5;$i++) {
        $psql->query("select * from `$tb_play` where gid='$gid' and sid='" . $sidarr[$i] . "' and name='龙'");
        $psql->next_record();
        $pid = $psql->f('pid');
        $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
        $psql->next_record();
        $long = $psql->f('zhong');
        $psql->query("select * from `$tb_play` where gid='$gid' and sid='" . $sidarr[$i] . "' and name='虎'");
        $psql->next_record();
        $pid = $psql->f('pid');
        $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
        $psql->next_record();
        $hu = $psql->f('zhong');
        $zharr[$i]['long'] = $long;
        $zharr[$i]['hu'] = $hu;
        /*
        foreach ($play as $k => $v) {
            if ($v['sid'] == $sidarr[$i] && $v['name'] == $pnamearr[9 - $i]) {
                $zheng = $v['zhong'];
            }
            if ($v['sid'] == $sidarr[9 - $i] && $v['name'] == $pnamearr[$i]) {
                $fan = $v['zhong'];
            }
        }
        if (($ms[$i] > $ms[9 - $i] && $hu < $long) || ($ms[$i] < $ms[9 - $i] && $hu > $long)) {
            $add = abs($long - $hu);
            $old = abs($play['p' . $pidarr[$i]]['zhong'] + $play['p' . $pidarr[9 - $i]]['zhong']);
            $new = abs($zheng + $fan);
            if ($add > $old && $add > $new && $je > $fanjiang) {
                $ms[$i] = $play['p' . $pidarr[9 - $i]]['name'];
                $ms[9 - $i] = $play['p' . $pidarr[$i]]['name'];
            }
        }*/
    }

    $he = $ms[0]+$ms[1];
    $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378805' and name='$he'");
    $psql->next_record();
    $pid = $psql->f('pid');
    $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
    $psql->next_record();
    $zharr[5]['num'] = $psql->f('zhong');
    $zharr[5]['sm'] = 0;
    if($he<=11){
        $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378805' and name='和小'");
        $psql->next_record();
        $pid = $psql->f('pid');
        $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
        $psql->next_record();
        $zharr[5]['sm'] += $psql->f("zhong");
    }else{
        $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378805' and name='和大'");
        $psql->next_record();
        $pid = $psql->f('pid');
        $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
        $psql->next_record();
        $zharr[5]['sm'] += $psql->f("zhong");
    }
    if($he%2==0){
        $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378805' and name='和双'");
        $psql->next_record();
        $pid = $psql->f('pid');
        $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
        $psql->next_record();
        $zharr[5]['sm'] += $psql->f("zhong");
    }else{
        $psql->query("select * from `$tb_play` where gid='$gid' and bid='23378805' and name='和单'");
        $psql->next_record();
        $pid = $psql->f('pid');
        $psql->query("select sum(peilv1*je*zc0/100) as zhong from `$tb_lib` where gid='$gid' and qishu='$qishu' and pid='$pid'");
        $psql->next_record();
        $zharr[5]['sm'] += $psql->f("zhong");
    }
    $ms = array_unique($ms);
    if (count($ms) != 10) {
        $mm = randbm($gid, $qishu, 10);
        $mm = json_decode($mm, true);
    } else {
        $mm = [];
        $mm[0]['m'] = $ms;
        $mm[0]['qishu'] = $qishu;
        $mm[0]['kjtime'] = time();
    }

    $arr = checkpk10($play, $mm, $fanjiang, $cs, $benqije,$zharr);
    $arr['bq'] = $benqije;
    $arr['fan'] = $fanjiang;
    $arr['kzmode'] = $cs['kzmode'];
    $arr['trys'] = $trys;
    //echo json_encode($zharr);
    //echo json_encode($arr);

    $psql->query("insert into `$tb_kjinfo` set ma='{$arr['m']}',gid='$gid',qishu='$qishu',je='{$arr['je']}'");
    if (!$arr['flag'] && $trys > 0) {
        $trys--;
        if($trys==0){
            if($cs['kzmode']==1){
                $psql->query("select ma from `$tb_kjinfo` where gid='$gid' and qishu='$qishu' order by je limit 1");
                $psql->next_record();
            }else{
                $psql->query("select ma from `$tb_kjinfo` where gid='$gid' and qishu='$qishu' and je<='$fanjiang' order by je desc limit 1");  
                $psql->next_record();
                if($psql->f('ma')==""){
                    $trys=20;
                    return pk10myself($gid, $cs, $qishu, $mnum, $dates,$trys);
                }
            } 
            $mm[0]['m'] = json_decode($psql->f('ma'),true);
        }else{
            return pk10myself($gid, $cs, $qishu, $mnum, $dates,$trys);
        }
    }
    return json_encode($mm);
}
function checkpk10($play, $mm, $fanjiang, $cs, $benqije,$zharr) {
    $je = 0;
    foreach ($play as $k => $v) {
        foreach ($mm[0]['m'] as $k1 => $v1) {
            if ($v['mtype'] == $k1 && $v['name'] == $v1) {
                $je+= $v['zhong'];
            }
        }
    }
    if($mm[0]['m'][0]>$mm[0]['m'][9]){
        $je += $zharr[0]['long'];
    }else{
        $je += $zharr[0]['hu'];
    }
    if($mm[0]['m'][1]>$mm[0]['m'][8]){
        $je += $zharr[1]['long'];
    }else{
        $je += $zharr[1]['hu'];
    }
    if($mm[0]['m'][2]>$mm[0]['m'][7]){
        $je += $zharr[2]['long'];
    }else{
        $je += $zharr[2]['hu'];
    }
    if($mm[0]['m'][3]>$mm[0]['m'][6]){
        $je += $zharr[3]['long'];
    }else{
        $je += $zharr[3]['hu'];
    }
    if($mm[0]['m'][4]>$mm[0]['m'][5]){
        $je += $zharr[4]['long'];
    }else{
        $je += $zharr[4]['hu'];
    }
    $je += $zharr[5]['num'] + $zharr[5]['sm'];
    $arr['je'] = $je;
    $arr['m'] = json_encode($mm[0]['m']);
    if ($cs['kzmode'] == 1) {
        if ($je > $fanjiang || $je > $benqije) {
            $arr['flag'] = false;
        } else {
            $arr['flag'] = true;
        }
    } else {
        if ($je > $fanjiang || $je < $benqije) {
            $arr['flag'] = false;
        } else {
            $arr['flag'] = true;
        }
    }
    return $arr;
}





function kjmyself_lhpkm($v,$type){
    if($v==1 | $v==10){
        return 'error';
    }
    $vv = 'error' ;
    if($type==0){
        $vv = rand(1,$v-1);
    }else if($type==1){
        $vv  = rand($v+1,10);
    }
    return $vv;
    
}

function randbm($gid, $qishu, $mnum){
    $m = array();
    switch ($mnum) {
        case 4:
            $arr = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
            $m[0] = $arr[rand(0, 9)];
            $m[1] = $arr[rand(0, 9)];
            $m[2] = $arr[rand(0, 9)];
            break;
        case 3:
            $arr = array(1, 2, 3, 4, 5, 6);
            $m[0] = $arr[rand(0, 5)];
            $m[1] = $arr[rand(0, 5)];
            $m[2] = $arr[rand(0, 5)];
            break;
        case 5:
            $arr = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
            $m[0] = $arr[rand(0, 9)];
            $m[1] = $arr[rand(0, 9)];
            $m[2] = $arr[rand(0, 9)];
            $m[3] = $arr[rand(0, 9)];
            $m[4] = $arr[rand(0, 9)];
            break;
        case 8:
            $arr = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20");
            $m[0] = $arr[rand(0, 19)];
            for ($i = 1; $i < 8; $i++) {
                $m[$i] = randm($m, $arr, $mnum, 20);
            }
            break;
        case 6:
            $arr = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11");
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
            $arr = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10");
            $m[0] = $arr[rand(0, 9)];
            for ($i = 1; $i < 10; $i++) {
                $m[$i] = randm($m, $arr, $mnum, 10);
            }
            break;
    }
    $mm = [];
    $mm[0]['m'] = $m;
    $mm[0]['qishu'] = $qishu;
    $mm[0]['kjtime'] = time();
    return json_encode($mm);
}
function randm($m,$arr,$mnum,$maxs){
    $a = $arr[rand(0,$maxs-1)];
    if(in_array($a,$m)) return randm($m,$arr,$mnum,$maxs);
    else return $a;
}


function xy28kj($arr){
    $m=array();
    $ca = count($arr);
    $tmp=0;
            for ($i = 0; $i < $ca; $i++) {
                $tmp += $arr[$i];
                if ($i == 5) {
                    $m[]  = $tmp % 10;
                    $tmp = 0;
                }
                if ($i == 11) {
                    $m[]  = $tmp % 10;
                    $tmp = 0;
                }
                if ($i == 17) {
                    $m[]  = $tmp % 10;
                    $tmp = 0;
                }
                
            }
            return $m;
}

function bml($v){
    if(strpos($v,'子')) return "鼠";
    if(strpos($v,'丑')) return "牛";
    if(strpos($v,'寅')) return "虎";
    if(strpos($v,'卯')) return "兔";
    if(strpos($v,'辰')) return "龍";
    if(strpos($v,'巳')) return "蛇";
    if(strpos($v,'午')) return "馬";
    if(strpos($v,'未')) return "羊";
    if(strpos($v,'申')) return "猴";
    if(strpos($v,'酉')) return "雞";
    if(strpos($v,'戌')) return "狗";
    if(strpos($v,'亥')) return "豬";

}