<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include("../func/csfunc.php");
include('../func/adminfunc.php');
include('../include.php');
include './checklogin.php';
include('../data/myadminvar.php');
include("../func/malhc.php");
$input = $_POST;
$otherclosetime = $config['otherclosetime'];
function ds($expect)
{

    $returnSet = [];
    foreach ($expect as $k=>$v){
        if ($v % 2 == 0)
            $returnSet[] = 1;
        else {
            $returnSet[] =  0;
        }
    }
    return $returnSet;
}

function dx($gid, $expect)
{    
    $array = [];

    foreach ($expect as $v) {
      
        if ($gid==101) {
            if ($v <= 4)
                $array[] = 0;
            else
                $array[] = 1;
        } else if ($gid==121) {
            if ($v < 6)
                $array[] = 0;
            else if ($v < 10)
                $array[] = 1;
        } else if ($gid==103) {
            if ($v < 11)
                $array[] = 0;
            else
                $array[] = 1;
        } else if ($gid==151) {
            if ($v <= 3)
                $array[] = 0;
            else
                $array[] = 1;
        } else if ($gid==161) {
            if ($v < 41)
                $array[] = 0;
            else
                $array[] = 1;
        } else if ($gid == 107) {
            if ($v <= 5)
                $array[] = 0;
            else
                $array[] = 1;
        } else if ($gid == 100) {
            if ($v < 25)
                $array[] = 0;
            else if ($v <= 49)
                $array[] = 1;
        }
    }
    return $array;
}


function wdx($expect)
{
    $array = [];
    foreach ($expect as $v) {
        $v = $v % 10;
        if ($v <= 4)
            $array[] = 1;
        else
            $array[] = 0;
    }
    return $array;
}

function zonghe($fenlei,$expect)
{
    $a = 0;//总和
    $c = 0;//大小 0小1大 2和
    $b = 0;//单双 0单1双 2和
	$d0 = 0;//大小
	$d1 = 0;//单双
	$d2 = 0;//中
	$bs = 0;//中
    $q_res='';
    $z_res='';
    $h_res='';
    
    //$d = 1;
    //澳洲幸运5，极速时时彩，幸运时时彩
    if($fenlei == 101){
        foreach ($expect as $v) {
            $a += $v;
        }
        if ($a % 2 == 0) {
            $b = 1;
        }
        if($a>=23){
            $c = 1;
        }
        //前三位特殊玩法规则
        $q_expect = [$expect[0],$expect[1],$expect[2]];
        $q_res = tesuwanfa($q_expect);
        //中三位特殊玩法规则
        $z_expect= [$expect[1],$expect[2],$expect[3]];
        $z_res = tesuwanfa($z_expect);
        //后三位特殊玩法规则
        $h_expect = [$expect[2],$expect[3],$expect[4]];
        $h_res = tesuwanfa($h_expect);
    }
    //极速赛车，极速飞艇，sg飞艇，澳洲幸运10
    if($fenlei == 107){
        $a = $expect[0]+$expect[1];
        if ($a % 2 == 0) {
            $b = 1;
        }
        if($a> 11){
            $c = 1;
        }
    }
    //澳洲幸运20
    if($fenlei == 161){
        $ss = $ds = 0; //ss双数个数，$ds单数个数
        $qs = $hs = 0;//$qs前多个数，$hs后多个数
        foreach ($expect as $v) {
            $a += $v;
            if($v % 2 == 0){
                $ss++;
            }else{
                $ds++;
            }
            if($v>=1 && $v<=40){
                $qs++;
            }else if($v>=41 && $v<=80){
                $hs++;
            }
        }
        if ($a % 2 == 0) {
            $b = 1;
        }
        if($a> 810){
            $c = 1;
        }
        if($a>=210 && $a<=695){
            $q_res = '金';
        }
        if($a>=696 && $a<=763){
            $q_res = '木';
        }
        if($a>=764 && $a<=855){
            $q_res = '水';
        }
        if($a>=856 && $a<=923){
            $q_res = '火';
        }
        if($a>=924 && $a<=1410){
            $q_res = '土';
        }
        if($ss>$ds){
            $z_res = 1;
        }else if($ss<$ds){
            $z_res = 0;
        }else{
            $z_res = 2;
        }
        if($qs>$hs){
            $h_res = 0;
        }else if($qs<$hs){
            $h_res = 1;
        }else{
            $h_res = 2;
        }
    }
    //澳洲幸运8
    if($fenlei == 103){
        foreach ($expect as $v) {
            $a += $v;
        }
        
    }
	 if($fenlei == 100){
        foreach ($expect as $v) {
            $a += $v;
        }
		$ZTM = $expect[6];
		if ($ZTM % 2 == 0) {
            $b = 1;
			$d0 = 1;
		}	
       if($ZTM>=24){
            $c = 1;
			$d1 = 1;
        }
		if($ZTM>=24){
            $d2 = 1;
        }
		
		if ($ZTM>=20 && $ZTM<=40 ) {
			$d2 = 3;
			}
		if ($ZTM== 49) {
			$d0 = 2;
			$d1 = 2;
			$d2 = 2;
			}
	    $bs=sjbose($ZTM);
}		
    return [$a,$c,$b,$d0,$d1,$d2,$bs,$q_res,$z_res,$h_res];//[$a,$b,$c,$d,1,1,1];//
}
//特殊玩法规则
function tesuwanfa($vars){
    $res = '';
    if($vars[0] == $vars[1] && $vars[0] == $vars[2] && $vars[1] == $vars[2]){
        $res ='豹子';
    }else{
        $dz ='';
        if($vars[0] == $vars[1]){
            $dz++;
        }
        if($vars[0] == $vars[2]){
            $dz++;
        }
        if($vars[1] == $vars[2]){
            $dz++;
        }
        if($dz ==1){
            $res ='对子';
        }else {
            $bb=0;
            if(abs($vars[0]-$vars[1]) ==1){
                $bb++;
            }
            if (abs($vars[0]-$vars[2]) ==1){
                $bb++;
            }
            if(abs($vars[1]-$vars[2]) ==1){
                $bb++;
            }
            sort($vars);
            if($vars[0] == 0 && $vars[2] == 9){
                $bb++;
            }
            if($vars[0] == 0 && $vars[1] == 1 && $vars[2] == 9){//019特殊处理
                $bb++;
            }
            if($bb ==0){
                $res ='杂六';
            }if($bb==1){
                $res ='半顺';
            }if($bb==2){
                $res ='顺子';
            }
        }
    }
    return $res;
}

function lh($fenlei,$expect) {
    $expect2 = array_reverse($expect);
    $array = [];
    foreach ($expect as $k=>$v) {
        if ($expect[$k] > $expect2[$k]) {
            $array[] = 0;//龙
        } else if ($expect[$k] < $expect2[$k]){
            $array[] = 1;//虎
        }else{
            $array[] = 2;//和
        }
    }
    if($fenlei == 101){
        return array_slice($array,4);
    }
    if($fenlei == 107){
        $expect2 = array_reverse($expect);
        $array = [];
        foreach ($expect as $k=>$v) {
            if ($expect[$k] > $expect2[$k]) {
                $array[] = 1;//龙
            } else if ($expect[$k] < $expect2[$k]){
                $array[] = 0;//虎
            }else{
                $array[] = 2;//和
            }
            if($k>=4)break;
        }
        return $array;
    }
    if($fenlei == 103){
        $expect2 = array_reverse($expect);
        $array = [];
        foreach ($expect as $k=>$v) {
            if ($expect[$k] > $expect2[$k]) {
                $array[] = 1;//龙
            } else if ($expect[$k] < $expect2[$k]){
                $array[] = 0;//虎
            }else{
                $array[] = 2;//和
            }
            if($k>=3)break;
        }
        return $array;
    }
    
}

function hs($expect)
{
    $array = [];
    foreach ($expect as $v) {
     $ge = $v % 10;
        $array[] = ($v - $ge) / 10 + $ge;
    }
    return $array;
}

function shengxiaos($ma, $bml)
{
    $jiazhi = array('甲子', '乙丑', '丙寅', '丁卯', '戊辰', '己巳', '庚午', '辛未', '壬申', '癸酉', '甲戌', '乙亥', '丙子', '丁丑', '戊寅', '己卯', '庚辰', '辛巳', '壬午', '癸未', '甲申', '乙酉', '丙戌', '丁亥', '戊子', '己丑', '庚寅', '辛卯', '壬辰', '癸巳', '甲午', '乙未', '丙申', '丁酉', '戊戌', '己亥', '庚子', '辛丑', '壬寅', '癸卯', '甲辰', '乙巳', '丙午', '丁未', '戊申', '己酉', '庚戌', '辛亥', '壬子', '癸丑', '甲寅', '乙卯', '丙辰', '丁巳', '戊午', '己未', '庚申', '辛酉', '壬戌', '癸亥');
    $index = 0;
    foreach ($jiazhi as $key => $val) {
        if ($val == $bml) {
            $index = $key;
            break;
        }
    }
    $index = $index % 12 +2;
    $ma = $ma % 12;
    $arr = array('鼠', '牛', '虎', '兔', '龍', '蛇', '馬', '羊', '猴', '雞', '狗', '豬');
    $in= 0 ;
    if ($index >= $ma) {
      $in = $index - $ma;
    } else {
       $in =  $index - $ma + 12;
    }
    if($in>=12) $in -=12;
    return $arr[$in];
}
  //分页
  $page = r1p($_REQUEST['page']);
  $psize = $config['psizekj'];
  
    $msql->query("select cs,mtype,ztype,fenlei from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $fenlei = $msql->f("fenlei");
        
    $where = '1 = 1';
    $where .= " AND gid = {$input['gid']}";
    if ($fenlei != 100 && $fenlei !=300) {
        $where .= " AND dates like '%{$input['dates']}%'";
    }
    $where .= ' AND js = 1';
    
	
    $total=[];
    $msql->query("select count(id)  from `x_kj` where $where ");
    $msql->next_record();
	$total['zs'] = $msql->f(0);
	$rcount = $total['zs'];
	
     $pcount = $rcount%$psize==0 ? $rcount/$psize : ($rcount-$rcount%$psize)/$psize+1;
     if($page>$pcount) $page = $pcount;
	$array = [];
	$pagekj = ($page - 1) * $psize . ',' . $psize;
	//var_dump($rcount);
    $res = $msql->query("select * from `x_kj` where $where   order by qishu desc limit $pagekj");
    while ($msql->next_record()) {
        $expect = [];
        $sx=[];
        for ($j = 1; $j <= $config['mnum']; $j++) {
            $expect[] = $msql->f('m' . $j);
            $fenlei ==100 && $sx[] = shengxiaos( $msql->f('m' . $j) ,$msql->f("bml"));
        }
    //   var_dump(dx($msql->f('gid'),$expect));die;
        $array[] = [
            'gname' => 'g'.$msql->f('gid'),
            'gid' => $msql->f('gid'),
            'bml' => $msql->f('bml'),
            'oy' => substr($msql->f('opentime'), 0, 4),
            'cy' => substr($msql->f('closetime'), 0, 4),
            'ky' => substr($msql->f('kjtime'), 0, 4),
            'opentime' => date("m-d H:i:s", strtotime($msql->f('opentime'))),
            'closetime' => date("m-d H:i:s", strtotime($msql->f('closetime'))),
            'kjtime' => date("m-d", strtotime($msql->f('kjtime'))),
            'otherclosetime' => date("m-d H:i:s", strtotime($msql->f('closetime')) - $otherclosetime),
            'baostatus' => $msql->f('baostatus'),
            'js' => $msql->f('js'),
            'qishu' => $msql->f('qishu'),
            'num' => $expect,
            'zonghe' => zonghe($fenlei,$expect),
            'ds' => ds($expect),
            'dx' => dx($fenlei,$expect),
            'wdx' => wdx($expect),
            'lh' => lh($fenlei,$expect),
            'hs' => hs($expect),
            'kj_num'=>  join($expect,','),
            'sx' => $sx
            ];
            
    }
        echo json_encode(array(
            "kj" => $array,
            'fenlei' => $fenlei,
            'rcount' => 1,
            'mnum' => $config['mnum'],
			'psize' => $psize,
			'page' => $page,
			'pcount' => $pcount
        ));
        