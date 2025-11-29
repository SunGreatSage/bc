<?php
$zhishu  = array(
    1,
    2,
    3,
    5,
    7
);
$heshu   = array(
    0,
    4,
    6,
    8,
    9
);
$dashu   = array(
    5,
    6,
    7,
    8,
    9
);
$xiaoshu = array(
    0,
    1,
    2,
    3,
    4
);
function nndaxiao($v){
   if($v>=1 & $v<=5){
      return '小';
   }else{
	   return '大';
	  }
}
function niuniu($arr)
{
    $t1 = 0;
    $t2 = 0;
    $t3 = 0;
    for ($a = 0; $a <= 2; $a++) {
        for ($b = $a + 1; $b <= 3; $b++) {
            for ($c = $b + 1; $c <= 4; $c++) {
                if (($arr[$a] + $arr[$b] + $arr[$c]) % 10 == 0) {
                    $t1 = 1;
                    for ($j = 0; $j <= 4; $j++) {
                        if ($j != $a && $j != $b && $j != $c) {
                            $t3 += $arr[$j];
                        }
                    }
                    if ($t3 % 10 == 0) {
                        $t2 = 1;
                    }
                }
            }
        }
    }
    $arr = [$t1,$t2,$t3%10,max($arr[0],$arr[1],$arr[2],$arr[3],$arr[4])];
	//print_r($arr);
    return $arr;
}
function suoha($arr){
	$r=0;//散号
	$a=array();
	foreach($arr as $v){
		$a[$v] += 1;
	}
	array_merge($a);
	$ca = count($a);
	switch($ca){
		case 1: 
		   $r=1;//五梅
		break;
		case 2:
		   sort($a);
		   if($a[0]==1 | $a[1]==1){
		       $r=2;//炸弹
		   }else{
		       $r=3;//葫芦
		   }
		break;
		case 3:
		   if($a[0]==3 | $a[1]==3 | $a[2]==3){
		      $r=4;//三条
		   }else{
		      $r=5;//两对
		   }
		break;
		case 4:
		   $r=6;//单对
		break;
		case 5:
		   sort($arr);
		   if($arr[4]-$arr[0]==4){
		        $r=7;//顺子
		   }else{
			   $kao1 = array(1,3,5,7,9);	
			   $kao2 = array(0,2,4,6,8);
			   if($arr==$kao1 | $arr==$kao2){
			      $r=8;//五不靠
			   }		   
		   }
		break;
	}
	$arr = array("散号","五梅","炸弹","葫芦","三条","两对","单对","顺子","五不靠");
	return $arr[$r];
}
function qita($v1,$v2,$v3){
	$v=9;
	if(baozhi($v1,$v2,$v3)==1) $v=0;
	else if(shunzhi($v1,$v2,$v3)==1) $v=1;
	else if(duizhi($v1,$v2,$v3)==1) $v=2;
	else if(banshun($v1,$v2,$v3)==1) $v=3;
	else $v=4;
	$arr = array("豹子","顺子","对子","半顺","杂六");
	return $arr[$v];
}
function danshuang($v)
{
    if ($v % 2 == 1) {
        $v = '单';
    } else {
        $v = '双';
    }
    return $v;
}
function danshuang_100($v)
{
    if ($v % 2 == 1) {
        $v = '單';
    } else {
        $v = '雙';
    }
    return $v;
}
function daxiao($v)
{
    global $dashu;
    if (in_array($v, $dashu)) {
        $v = '大';
    } else {
        $v = '小';
    }
    return $v;
}
function daxiao107($v)
{
    if ($v>5) {
        $v = '大';
    } else {
        $v = '小';
    }
    return $v;
}
function daxiao121($v)
{
    if ($v==11) {
        $v = '和';
    }else if ($v>5) {
        $v = '大';
    } else {
        $v = '小';
    }
    return $v;
}
function daxiao103($v)
{
    if ($v>10) {
        $v = '大';
    } else {
        $v = '小';
    }
    return $v;
}
function daxiaow($v)
{
    if ($v <= 4) {
        return '小';
    } else {
        return '大';
    }
}
function zhihe($v)
{
    global $zhishu;
    if (in_array($v, $zhishu)) {
        $v = '质';
    } else {
        $v = '合';
    }
    return $v;
}
function heshu($tm)
{
    if ($tm == '') {
        return '';
    }
    $heshu = $tm % 10 + ($tm - $tm % 10) / 10;
    return $heshu;
}
function heshudaxiao_100($v){
    if($v==13){
        return "和";
    }else if($v<=6){
        return "合小";
    }else{
        return "合大";  
    }
}
function longhuhe($v0, $v4)
{
    $v0= $v0+0;
    $v4= $v4+0;
    if ($v0 > $v4) {
        $v = '龙';
    } else {
        if ($v0 < $v4) {
            $v = '虎';
        } else {
            $v = '和';
        }
    }
    return $v;
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
function siji($v)
{
    //if(strpos('anull',$v)) return '';
    if (in_array($v, array(
        1,
        2,
        3,
        4,
        5
    ))) {
        $v = '春';
    } else {
        if (in_array($v, array(
            6,
            7,
            8,
            9,
            10
        ))) {
            $v = '夏';
        } else {
            if (in_array($v, array(
                11,
                12,
                13,
                14,
                15
            ))) {
                $v = '秋';
            } else {
                if (in_array($v, array(
                    16,
                    17,
                    18,
                    19,
                    20
                ))) {
                    $v = '冬';
                }
            }
        }
    }
    return $v;
}
function wuhang($v)
{
    //if(strpos('anull',$v)) return '';
    if (in_array($v, array(
        5,
        10,
        15,
        20
    ))) {
        $v = '金';
    } else {
        if (in_array($v, array(
            1,
            6,
            11,
            16
        ))) {
            $v = '木';
        } else {
            if (in_array($v, array(
                2,
                7,
                12,
                17
            ))) {
                $v = '水';
            } else {
                if (in_array($v, array(
                    3,
                    8,
                    13,
                    18
                ))) {
                    $v = '火';
                } else {
                    if (in_array($v, array(
                        4,
                        9,
                        14,
                        19
                    ))) {
                        $v = '土';
                    }
                }
            }
        }
    }
    return $v;
}
function wuhang_161($v)
{
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
function fangwei($v)
{
    //if(strpos('anull',$v)) return '';
    if (in_array($v, array(
        1,
        5,
        9,
        13,
        17
    ))) {
        $v = '东';
    } else {
        if (in_array($v, array(
            2,
            6,
            10,
            14,
            18
        ))) {
            $v = '南';
        } else {
            if (in_array($v, array(
                3,
                7,
                11,
                15,
                19
            ))) {
                $v = '西';
            } else {
                if (in_array($v, array(
                    4,
                    8,
                    12,
                    16,
                    20
                ))) {
                    $v = '北';
                }
            }
        }
    }
    return $v;
}
function zhongfabai($v)
{
    //if(strpos('anull',$v)) return '';
    if (in_array($v, array(
        1,
        2,
        3,
        4,
        5,
        6,
        7
    ))) {
        $v = '中';
    } else {
        if (in_array($v, array(
            8,
            9,
            10,
            11,
            12,
            13,
            14
        ))) {
            $v = '发';
        } else {
            if (in_array($v, array(
                15,
                16,
                17,
                18,
                19,
                20
            ))) {
                $v = '白';
            }
        }
    }
    return $v;
}
/**
 * 根据号码获取生肖（支持年份轮转）
 * @param int $m 号码(1-49)
 * @param array $arr 旧版生肖映射表(兼容参数,不再使用)
 * @param string $dates 日期字符串(可选,用于提取年份)
 * @return string 生肖名称
 */
function sx_100($m, $arr, $dates = null)
{
    // 使用新的生肖算法（支持年份轮转）
    // 优先从 $dates 参数获取年份，否则使用当前年份
    if ($dates) {
        $year = date('Y', strtotime($dates));
    } else {
        $year = date('Y');
    }

    // 调用 malhc.php 中的新算法
    if (function_exists('shengxiao')) {
        return shengxiao($m, $year);
    }

    // 兼容回退：如果新函数不存在，使用旧逻辑
    $sx = array(
        "鼠",
        "牛",
        "虎",
        "兔",
        "龍",
        "蛇",
        "馬",
        "羊",
        "猴",
        "雞",
        "狗",
        "豬"
    );
    foreach ($sx as $v) {
        if (in_array($m, $arr[$v])) {
            return $v;
        }
    }
    return false;
}
function getkj($mnum, $gid, $thisqishu, $page, $psize)
{
    global $tb_kj, $psql;
    $sql = '';
    for ($i = 1; $i <= $mnum; $i++) {
        if ($i > 1) {
            $sql .= ',';
        }
        $sql .= 'm' . $i;
    }
    $time = sqltime(time());
    $psql->query("select {$sql},qishu,kjtime from `{$tb_kj}` where gid='{$gid}'  and  closetime<'$time' order by gid,qishu desc limit " . ($page - 1) * $psize . ",{$psize}");
    //echo "select {$sql},qishu,kjtime from `{$tb_kj}` where gid='{$gid}' and qishu<{$thisqishu} and  closetime<{$time} order by qishu desc limit " . ($page - 1) * $psize . ",{$psize}";
    $kj = array();
    $j  = 0;
    while ($psql->next_record()) {
        for ($i = 1; $i <= $mnum; $i++) {
            $kj[$j]['m' . $i] = $psql->f('m' . $i);
        }
        $kj[$j]['qishu'] = $psql->f('qishu');
        $kj[$j]['time']  = date('H:i', $psql->f('kjtime'));
        $j++;
    }
    return $kj;
}
function getkjs($gid, $mnum, $num, $time = 0,$fenlei)
{
    global $tb_kj, $psql;
    $psql->query("select * from `{$tb_kj}` where gid='{$gid}' and  closetime<'$time' order by gid,qishu desc limit {$num}");
    $i  = 0;
    $kj = array();
    if ( $fenlei==151) {
        while ($psql->next_record()) {
            $kj[$i]['qishu'] = $psql->f('qishu');
            $kj[$i]['qs']    = substr($psql->f('qishu'), -2);
            if ($psql->f('m1') == '') {
                $i++;
                continue;
            }
            $he           = $psql->f('m1') + $psql->f('m2') + $psql->f('m3');
            $kj[$i]['m1'] = $psql->f('m1');
            $kj[$i]['m2'] = $psql->f('m2');
            $kj[$i]['m3'] = $psql->f('m3');
            if ($kj[$i]['m1'] == $kj[$i]['m2'] & $kj[$i]['m1'] == $kj[$i]['m3']) {
                $kj[$i]['dx'] = '通吃';
            } else {
                if ($he <= 10) {
                    $kj[$i]['dx'] = '小';
                } else {
                    $kj[$i]['dx'] = '大';
                }
            }
            $kj[$i]['ds'] = $he;
            $i++;
        }
        return $kj;
    }
    if ($fenlei == 161 ) {
        while ($psql->next_record()) {
            $kj[$i]['qishu'] = $psql->f('qishu');
            $kj[$i]['qs']    = substr($psql->f('qishu'), -2);
            if ($psql->f('m1') == '') {
                $i++;
                continue;
            }
            $zd = 0;
            $zq = 0;
            $he = 0;
            for ($h = 1; $h <= 20; $h++) {
                if (danshuang($psql->f('m' . $h)) == '单') {
                    $zd++;
                }
                if ($psql->f('m' . $h) <= 40) {
                    $zq++;
                }
                $he += $psql->f('m' . $h);
                $kj[$i]['m'][] = $psql->f('m' . $h);
            }
            if ($he == 810) {
                $kj[$i]['zds'] = '和';
                $kj[$i]['zdx'] = '和';
            } else {
                if ($he < 810) {
                    $kj[$i]['zdx'] = '小';
                } else {
                    $kj[$i]['zdx'] = '大';
                }
                if (danshuang($he) == '单') {
                    $kj[$i]['zds'] = '单';
                } else {
                    $kj[$i]['zds'] = '双';
                }
            }
            if ($zd == 10) {
                $kj[$i]['dsh'] = '和';
            } else if ($zd > 10) {
                $kj[$i]['dsh'] = '单';
            } else {
                $kj[$i]['dsh'] = '双';
            }
            if ($zq == 10) {
                $kj[$i]['qhh'] = '和';
            } else if ($zq > 10) {
                $kj[$i]['qhh'] = '前';
            } else {
                $kj[$i]['qhh'] = '后';
            }
            $kj[$i]['zf'] = $he;
            $kj[$i]['wh'] = wuhang_161($he);
            $i++;
        }
        return $kj;
    }
    while ($psql->next_record()) {
        $kj[$i]['qishu'] = $psql->f('qishu');
        $kj[$i]['qs']    = substr($psql->f('qishu'), -2);
        $zfs             = 0;
        for ($j = 1; $j <= $mnum; $j++) {
            if ($psql->f('m' . $j) == '' | !is_numeric($psql->f('m' . $j))) {
                continue;
            }
            $zfs += $psql->f('m' . $j);
            if ($gid == 107) {
                if ($psql->f('m' . $j) == '') {
                    $kj[$i]['m' . $j] = $psql->f('m' . $j);
                } else {
                    $kj[$i]['m' . $j] = (int) $psql->f('m' . $j);
                }
            } else {
                $kj[$i]['m' . $j] = $psql->f('m' . $j);
            }
            if ($fenlei==101 | $fenlei==163 ) {
                $kj[$i]['m' . $j . 'ds'] = danshuang($psql->f('m' . $j));
                $kj[$i]['m' . $j . 'dx'] = daxiao($psql->f('m' . $j));
                $kj[$i]['m' . $j . 'zh'] = zhihe($psql->f('m' . $j));
            } else if ($fenlei==103) {
                $kj[$i]['m' . $j . 'ds'] = danshuang($psql->f('m' . $j));
                if ($psql->f('m' . $j) <= 10) {
                    $kj[$i]['m' . $j . 'dx'] = '小';
                } else {
                    $kj[$i]['m' . $j . 'dx'] = '大';
                }
                $hes                      = heshu($psql->f('m' . $j));
                $kj[$i]['m' . $j . 'hds'] = '合' . danshuang($hes);
                $kj[$i]['m' . $j . 'wdx'] = daxiao($psql->f('m' . $j) % 10);
                $kj[$i]['m' . $j . 'fw']  = fangwei($psql->f('m' . $j));
                $kj[$i]['m' . $j . 'wh']  = wuhang($psql->f('m' . $j));
                $kj[$i]['m' . $j . 'sj']  = siji($psql->f('m' . $j));
                $kj[$i]['m' . $j . 'zfb'] = zhongfabai($psql->f('m' . $j));
            } else if ($fenlei==121) {
                $kj[$i]['m' . $j . 'ds'] = danshuang($psql->f('m' . $j));
                if ($psql->f('m' . $j) <= 5) {
                    $kj[$i]['m' . $j . 'dx'] = '小';
                } else {
                    if ($psql->f('m' . $j) <= 10) {
                        $kj[$i]['m' . $j . 'dx'] = '大';
                    } else {
                        $kj[$i]['m' . $j . 'dx'] = '和';
                    }
                }
            } else if ($fenlei == 107) {
                $kj[$i]['m' . $j . 'ds'] = danshuang($psql->f('m' . $j));
                $kj[$i]['m' . $j . 'zh'] = zhihe($psql->f('m' . $j));
                if ($psql->f('m' . $j) <= 5) {
                    $kj[$i]['m' . $j . 'dx'] = '小';
                } else {
                    $kj[$i]['m' . $j . 'dx'] = '大';
                }
            } else if ($fenlei == 162 ) {
                $kj[$i]['m' . $j . 'zds'] = danshuang($psql->f('m' . $j));
                $kj[$i]['m' . $j . 'zdx'] = zhihe($psql->f('m' . $j));
                if ($psql->f('m' . $j) <= 5) {
                    $kj[$i]['m' . $j . 'dx'] = '小';
                } else {
                    $kj[$i]['m' . $j . 'dx'] = '大';
                }
            }
        }
        if (($fenlei==163) & is_numeric($psql->f('m1'))) {
            $kj[$i]['zf']   = $zfs;
            $kj[$i]['zfds'] = danshuang($zfs);
            if ($zfs <= 13)
                $kj[$i]['zfdx'] = "小";
            else
                $kj[$i]['zfdx'] = "大";
        }
        $i++;
    }
    return $kj;
}
function phpC($a, $m) {  
    $r = array();  
  
    $n = count($a);  
    if ($m <= 0 || $m > $n) {  
        return $r;  
    }  
  
    for ($i=0; $i<$n; $i++) {  
        $t = array($a[$i]);  
        if ($m == 1) {  
            $r[] = $t;  
        } else {  
            $b = array_slice($a, $i+1);  
            $c = phpC($b, $m-1);  
            foreach ($c as $v) {  
                $r[] = array_merge($t, $v);  
            }  
        }  
    }  
  
    return $r;  
}  
function phpC2(array $elements, $chosen)
{
    $result = array();
    for ($i = 0; $i < $chosen; $i++) {
        $vecm[$i] = $i;
    }
    for ($i = 0; $i < $chosen - 1; $i++) {
        $vecb[$i] = $i;
    }
    $vecb[$chosen - 1] = count($elements) - 1;
    $result[]          = $vecm;
    $mark              = $chosen - 1;
    while (true) {
        if ($mark == 0) {
            $vecm[0]++;
            $result[] = $vecm;
            if ($vecm[0] == $vecb[0]) {
                for ($i = 1; $i < $chosen; $i++) {
                    if ($vecm[$i] < $vecb[$i]) {
                        $mark = $i;
                        break;
                    }
                }
                if ($i == $chosen && $vecm[$chosen - 1] == $vecb[$chosen - 1]) {
                    break;
                }
            }
        } else {
            $vecm[$mark]++;
            $mark--;
            for ($i = 0; $i <= $mark; $i++) {
                $vecb[$i] = $vecm[$i] = $i;
            }
            $vecb[$mark] = $vecm[$mark + 1] - 1;
            $result[]    = $vecm;
        }
    }
    return $result;
}
function ds($gid, $v)
{
    if (($gid == 121 | $gid == 123 | $gid == 125) & $v == 11) {
        return "和";
    } else if (($gid == 161 | $gid == 162) & $v == 810) {
        return "和";
    } else if ($v % 2 == 0)
        return "双";
    else
        return "单";
}
function zhdx($gid, $v)
{
    if (in_array($gid, array(
        101,
        111,
        113,
        115
    ))) {
        if ($v <= 22)
            return "小";
        else
            return "大";
    } else if (in_array($gid, array(
            117,
            163
        ))) {
        if ($v <= 13)
            return "小";
        else
            return "大";
    } else if (in_array($gid, array(
            121,
            123,
            125
        ))) {
        if ($v < 30)
            return "小";
        else if ($v > 30)
            return "大";
        else
            return "和";
    } else if (in_array($gid, array(
            103,
            133,
            135
        ))) {
        if ($v < 84)
            return "小";
        else if ($v > 84)
            return "大";
        else
            return "和";
    } else if (in_array($gid, array(
            151,
            152
        ))) {
        if ($v <= 10)
            return "小";
        else
            return "大";
    } else if (in_array($gid, array(
            161,
            162
        ))) {
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
    if (in_array($gid, array(
        101,
        111,
        113,
        115
    ))) {
        if ($v <= 4)
            return "小";
        else
            return "大";
    } else if (in_array($gid, array(
            121,
            123,
            125
        ))) {
        if ($v < 6)
            return "小";
        else if ($v < 10)
            return "大";
        else
            return "和";
    } else if (in_array($gid, array(
            103,
            133,
            135
        ))) {
        if ($v < 11)
            return "小";
        return "大";
    } else if (in_array($gid, array(
            151,
            152
        ))) {
        if ($v <= 3)
            return "小";
        else
            return "大";
    } else if (in_array($gid, array(
            161,
            162
        ))) {
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
        else if ($v < 49)
            return "大";
        else
            return "和";
    }
}
function getbuz($gid,$whi){
    global $psql,$tb_play;
	$carr = implode($carr);
	$sql = "select buzqishu,name from `$tb_play` where gid='$gid' $whi order by xsort";	
	$arr = $psql->arr($sql,1);
	return $arr;
}

function getpk10nium($kj,$arr){
	$a = [];
	$arr = explode('-',$arr);
	foreach($arr as $v){
		$a[] = $kj[$v-1];
	}
	return $a;
}

function bjniuniu($a1,$a2,$pk10ts){
	//echo $a1[0];
	if(!$a1[0] & $a2[0]){
		return 1;
	}
	if($a1[0] & !$a2[0]){
		return 0;
	}
	if($a1[0] & $a2[0]){
		if($a1[2]==0) $a1[2]=10;
		if($a2[2]==0) $a2[2]=10;
		if($a1[2]>$a2[2]){
			return 0;
		}else if($a1[2]==$a2[2]){
			return 2;
		}else if($a1[2]<$a2[2]){
			return 1;
		}
	}
	
	if(!$a1[0] & !$a2[0]){
		if($a2[3]<$pk10ts){
			return 0;
		}
		if($a1[3]>$a2[3]){
			return 0;
		}else if($a1[3]==$a2[3]){
			return 2;
		}else if($a1[3]<$a2[3]){
			return 1;
		}
	}
	return 0;
	
}