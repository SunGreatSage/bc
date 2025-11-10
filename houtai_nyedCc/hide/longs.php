<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../func/csfunc.php');
include('../include.php');
include('./checklogin.php');
	    if(in_array($_REQUEST['gid'],$garr)){
           $gid= $_REQUEST['gid'];
        }
        $msql->query("select gname,fenlei,fast,cs from `$tb_game` where gid='$gid'");
        $msql->next_record();
    $cs = json_decode($msql->f('cs'),true);
    $fenlei = $msql->f("fenlei");   
    $gname = $msql->f("gname");
    $fast=$msql->f('fast');    
    $tpl->assign("fast",$fast);
    $nc=0;
    if(strpos($gname,'农场')!==false){
        $nc=1;
    }
$tpl->assign("fenlei",$fenlei);
$tpl->assign("nc",$nc);
$game = getgamecs($userid);
$game = getgamename($game);
$tpl->assign("game",$game);
$tpl->assign("gid", $gid);
$tpl->assign("ftnum",$cs['ftnum']);
$tpl->assign("ft",$cs['ft']);
$cg    = count($gname);

$cg    = count($gname);

$date = $_REQUEST['date'];
if (!preg_match ("/\d{4}-1[0-2]|0?[1-9]-0?[1-9]|[12][0-9]|3[01]/", $date)){
    if(date("His")<str_replace(':','',$config['editstart'])){
       $date = date("Y-m-d",time()-86400);
    }else{
       $date = date("Y-m-d");
    }
}
$tpl->assign("thisday", $date);

//$start = strtotime($date." ".$config['editend']);
//$start = sqltime($start);
//$end = strtotime($date." ".$config['editstart'])+86400;
//if($end>time()){
//   $end=time();
//}
//$end = sqltime($end);

$end = sqltime(time());
if ($fenlei == 100) {
 include("../func/malhc.php");
}

if($fast==0 || $cs['qsnums']<20){
    $sql     = "select * from `$tb_kj`  where gid='{$gid}'  and m1!=''  order by  gid,qishu desc ";
}else{
$sql     = "select * from `$tb_kj`  where gid='{$gid}' and dates='$date' and kjtime<='$end' order by  gid,qishu desc ";
}


$rs = $msql->arr($sql, 1);
$cr = count($rs);
$mnums = transgame($gid,'mnum');

$tpl->assign("mnums",$mnums);

for ($i = 0; $i < $cr; $i++) {
    $list[$i]['qishu'] = $rs[$i]['qishu'];

     $w = date("w",strtotime($rs[$i]['kjtime']));
	 
     $list[$i]['time'] = substr($rs[$i]['kjtime'],0,10) . " (" . rweek($w) . ") " .substr($rs[$i]['kjtime'],-8,5);
  
     if ($fenlei == 107) {
        $list[$i]['m'][0] = $rs[$i]['m1'] + $rs[$i]['m2'];
     }
	 $dan=0;
	 $qian=0;
    for ($j = 1; $j <= $mnums; $j++) {
        
        if ($fenlei == 107 ) {
			$list[$i]['ma'][] = $rs[$i]['m' . $j]+0;
        }else if ($fenlei==100) {
            $list[$i]['m'][0] += $rs[$i]['m' . $j];
			//$list[$i]['ma'][] = $rs[$i]['m' . $j];
            $list[$i]['ma'][] = $rs[$i]['m' . $j].'_'.shengxiao($rs[$i]['m'.$j],$rs[$i]['bml']);
        }else if ($fenlei==161) {
            $list[$i]['m'][0] += $rs[$i]['m' . $j];
            $list[$i]['ma'][] = strlen($rs[$i]['m' . $j])==1 ? '0'.$rs[$i]['m' . $j] : $rs[$i]['m' . $j];
        }else{
            $list[$i]['m'][0] += $rs[$i]['m' . $j];
			$list[$i]['ma'][] = $rs[$i]['m' . $j]; 
		}
		if($fenlei==161){
		    if($rs[$i]['m' . $j]%2==1) $dan++;
			if($rs[$i]['m' . $j]<=40) $qian++;
		}
        
    }
    if($rs[$i]['m'.$mnums]=='') $list[$i]['m'][0]='';
    if ($fenlei==151 & $rs[$i]['m1'] == $rs[$i]['m3'] & $rs[$i]['m1'] == $rs[$i]['m2'] & $rs[$i]['m3']!='') {
        $list[$i]['m'][] = '通吃';
    } else {
        $list[$i]['m'][] = zhdx($fenlei, $list[$i]['m'][0]);
    }
	if($fenlei!=151){
	   $list[$i]['m'][] = ds($fenlei,$list[$i]['m'][0]);
	}
	if($fenlei==100){
	    $list[$i]['m'][] = shengxiao($rs[$i]['m'.$mnums],$rs[$i]['bml']);
		$list[$i]['m'][] = wuhang($rs[$i]['m'.$mnums],$rs[$i]['bml']);
		$list[$i]['m'][] = ds($fenlei,$rs[$i]['m'.$mnums]);
		$list[$i]['m'][] = dx($fenlei,$rs[$i]['m'.$mnums]);
		$list[$i]['m'][] = "合".ds($fenlei,hs($rs[$i]['m'.$mnums]));
		$list[$i]['m'][] = "合".hdx2($fenlei,hs($rs[$i]['m'.$mnums]));
		$list[$i]['m'][] = wdx($rs[$i]['m'.$mnums]%10);
		$list[$i]['m'][] = rbose($rs[$i]['m'.$mnums]);
	}else if($fenlei==101){
		$list[$i]['m'][] = longhu($rs[$i]['m1'],$rs[$i]['m5']);
		$list[$i]['m'][] = qita($rs[$i]['m1'],$rs[$i]['m2'],$rs[$i]['m3']);
		$list[$i]['m'][] = qita($rs[$i]['m2'],$rs[$i]['m3'],$rs[$i]['m4']);
		$list[$i]['m'][] = qita($rs[$i]['m3'],$rs[$i]['m4'],$rs[$i]['m5']);
	
	}else if($fenlei==163){
	    $list[$i]['m'][] = qita($rs[$i]['m1'],$rs[$i]['m2'],$rs[$i]['m3']);
	}else if($fenlei==103){
		$list[$i]['m'][] = wdx($list[$i]['m'][0]%10);
                    $list[$i]['m'][] = longhu($rs[$i]['m1'], $rs[$i]['m8']);
                    $list[$i]['m'][] = longhu($rs[$i]['m2'], $rs[$i]['m7']);
                    $list[$i]['m'][] = longhu($rs[$i]['m3'], $rs[$i]['m6']);
                    $list[$i]['m'][] = longhu($rs[$i]['m4'], $rs[$i]['m5']);	
	}else if($fenlei==107){
	    $list[$i]['m'][] = longhu($rs[$i]['m1'],$rs[$i]['m10']);
		$list[$i]['m'][] = longhu($rs[$i]['m2'],$rs[$i]['m9']);
		$list[$i]['m'][] = longhu($rs[$i]['m3'],$rs[$i]['m8']);
		$list[$i]['m'][] = longhu($rs[$i]['m4'],$rs[$i]['m7']);
		$list[$i]['m'][] = longhu($rs[$i]['m5'],$rs[$i]['m6']);
	}else if($fenlei==161){
		$list[$i]['m'][] =  wuhang_161($list[$i]['m'][0]);;
		if($dan==0){
		    $list[$i]['m'][] = "";
		}else if($dan==10){
		    $list[$i]['m'][] = "单双(和)";
		}else if($dan<10){
		    $list[$i]['m'][] = "双(多)";
		}else if($dan>10){
		    $list[$i]['m'][] = "单(多)";
		}
		if($qian==0){
		    $list[$i]['m'][] = "";
		}else if($qian==10){
		    $list[$i]['m'][] = "前后(和)";
		}else if($qian>10){
            $list[$i]['m'][] = "前(多)";
        }else if($qian<10){
            $list[$i]['m'][] = "后(多)";
        }
    
	
	}else if($fenlei==121){
		$list[$i]['m'][] = wdx($list[$i]['m'][0]%10);
	    $list[$i]['m'][] = longhu($rs[$i]['m1'],$rs[$i]['m5']);
	}
 /*   if($fenlei!=151 && $config['cs']['ft']==1){
        $zh = 0;
        $kjarr = [$rs[$i]['m1'],$rs[$i]['m2'],$rs[$i]['m3'],$rs[$i]['m4'],$rs[$i]['m5'],$rs[$i]['m6'],$rs[$i]['m7'],$rs[$i]['m8'],$rs[$i]['m9'],$rs[$i]['m10']] ;
        $zh = getftzh($kjarr,$config['cs']);
        $list[$i]['m'][] = $zh;
        $list[$i]['m'][] = $zh % 4 == 0 ? 4 : $zh % 4;
    }*/
}

$tpl->assign("list", $list);
$tpl->display('longs.html');

function wuhang_161($v)
{
	if($v=='') return '';
    if ($v <= 695) {
        $v = '金';
    } else if ($v <= 763) {
        $v = '木';
    } else if ($v <= 855) {
        $v = '水';
    } else if ($v <= 923) {
        $v = '火';
    } else {
        $v = '土';
    }
    return $v;
}
function qita($v1,$v2,$v3){
	if($v3=='') return '';
	$v=9;
	if(baozhi($v1,$v2,$v3)==1) $v=0;
	else if(shunzhi($v1,$v2,$v3)==1) $v=1;
	else if(duizhi($v1,$v2,$v3)==1) $v=2;
	else if(banshun($v1,$v2,$v3)==1) $v=3;
	else $v=4;
	$arr = array("豹子","顺子","对子","半顺","杂六");
	return $arr[$v];
}
function duizhi($v1, $v2, $v3)
{
    if ($v1 == $v2 | $v1 == $v3 | $v2 == $v3) {
        $v = 1;
    } else {
        $v = 0;
    }
    if ($v == 1) {
        $vv = baozhi($v1, $v2, $v3);
        if ($vv == 1) {
            $v = 0;
        }
    }
    return $v;
}
function baozhi($v1, $v2, $v3)
{
    if ($v1 == $v2 & $v1 == $v3 & $v2 == $v3) {
        $v = 1;
    } else {
        $v = 0;
    }
    return $v;
}
function shunzhi($v1, $v2, $v3)
{
    $vh = $v1 + $v2 + $v3;
    $v  = 0;
    if ($vh % 3 == 0 & $v1 != $v2 & $v1 != $v3 & $v2 != $v3 & max($v1, $v2, $v3) - min($v1, $v2, $v3) == 2) {
        $v = 1;
    } else {
        if (strpos('[019]', $v1) != false & strpos('[019]', $v2) != false & strpos('[019]', $v3) != false & $v1 != $v2 & $v1 != $v3 & $v2 != $v3) {
            if ($v1 != $v2 & $v1 != $v3 & $v2 != v3) {
                $v = 1;
            }
        } else {
            if (strpos('[890]', $v1) != false & strpos('[890]', $v2) != false & strpos('[890]', $v3) != false & $v1 != $v2 & $v1 != $v3 & $v2 != $v3) {
                if ($v1 != $v2 & $v1 != $v3 & $v2 != v3) {
                    $v = 1;
                }
            }
        }
    }
    return $v;
}
function banshun($v1, $v2, $v3)
{
    $vh1 = abs($v1 - $v2);
    $vh2 = abs($v1 - $v3);
    $vh3 = abs($v2 - $v3);
    if (baozhi($v1, $v2, $v3) == 1) {
        $z = 0;
    } else {
        if (shunzhi($v1, $v2, $v3) == 1) {
            $z = 0;
        } else {
            if (duizhi($v1, $v2, $v3) == 1) {
                $z = 0;
            } else {
                if ($vh1 == 1 | $vh2 == 1 | $vh3 == 1) {
                    $z = 1;
                } else {
                    if (strpos('[' . $v1 . $v2 . $v3 . ']', '0') != false & strpos('[' . $v1 . $v2 . $v3 . ']', '9') != false) {
                        $z = 1;
                    } else {
                        $z = 0;
                    }
                }
            }
        }
    }
    return $z;
}
function zaliu($v1, $v2, $v3)
{
    if (baozhi($v1, $v2, $v3) == 1) {
        $z = 0;
    } else {
        if (shunzhi($v1, $v2, $v3) == 1) {
            $z = 0;
        } else {
            if (duizhi($v1, $v2, $v3) == 1) {
                $z = 0;
            } else {
                if (banshun($v1, $v2, $v3) == 1) {
                    $z = 0;
                } else {
                    $z = 1;
                }
            }
        }
    }
    return $z;
}
function ds($gid,$v)
{
	if($v=='') return '';
    if (($gid == 121 | $gid == 123 | $gid == 125) & $v == 11) {
        return "和";
    } else if (($gid == 161 | $gid == 162) & $v == 810) {
        return "和";
    } else if ($v % 2 == 0)
        return "双";
    else {
        return "单";
    }
}
function zhdx($gid, $v)
{
	if($v=='') return '';
    if ($gid==101) {
        if ($v <= 22)
            return "小";
        else
            return "大";
    }else if ($gid==163) {
        if ($v <= 13)
            return "小";
        else
            return "大";
    } else if ($gid==121) {
        if ($v < 30)
            return "小";
        else if ($v > 30)
            return "大";
        else
            return "和";
    } else if ($gid==103) {
        if ($v < 84)
            return "小";
        else if ($v > 84)
            return "大";
        else
            return "和";
    } else if ($gid==151) {
        if ($v <= 10)
            return "小";
        else
            return "大";
    } else if ($gid==161) {
        if ($v < 810)
            return "小";
        else if ($v > 810)
            return "大";
        else
            return "和";
    } else if ($gid == 107) {
        if ($v <= 11)
            return "小";
        else
            return "大";
    } else if ($gid == 100) {
        if ($v <= 174)
            return "小";
        else
            return "大";
    }
}

function dx($gid, $v)
{
	if($v=='') return '';
    if ($gid==101) {
        if ($v <= 4)
            return "小";
        else
            return "大";
    } else if ($gid==121) {
        if ($v < 6)
            return "小";
        else if ($v < 10)
            return "大";
        else
            return "和";
    } else if ($gid==103) {
        if ($v < 11)
            return "小";
        return "大";
    } else if ($gid==151) {
        if ($v <= 3)
            return "小";
        else
            return "大";
    } else if ($gid==161) {
        if ($v < 41)
            return "小";
        else
            return "大";
    } else if ($gid == 107) {
        if ($v <= 5)
            return "小";
        else
            return "大";
    } else if ($gid == 100) {
        if ($v < 25)
            return "小";
        else if ($v <= 49)
            return "大";
        //else return "和";
    }
}
function hdx2($gid, $v)
{
        if ($v <= 6)
            return "小";
        else if ($v > 6)
            return "大";
    }
	
function wdx($v)
{
	if($v=='') return '尾小';
    $v = $v % 10;
    if ($v <= 4)
        return "尾小";
    else
        return "尾大";
}
function zh($v)
{
	if($v=='') return '';
    $zhi = array(
        1,
        2,
        3,
        5,
        7
    );
    if (in_array($v, $zhi)) {
        return "质";
    } else {
        return "合";
    }
}
function hs($v)
{
	if($v=='') return '';
    $ge = $v % 10;
    $hs = ($v - $ge) / 10 + $ge;
    return $hs;
}
function longhu($v1, $v2)
{
	if($v2=='') return '';
    if ($v1 == $v2) {
        return "和";
    } else if ($v1 < $v2) {
        return "虎";
    } else {
        return "龙";
    }
}

