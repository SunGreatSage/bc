<?php
/*
 * Created on 208,12-29
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */ 
function rTexiao($sx,$bml) {
	$sx=str_replace('特肖:','',$sx);
 	if (strpos($bml,"子")) {$shengxiao=1;}
    if (strpos($bml,"醜")) {$shengxiao=2;}
    if (strpos($bml,"寅")) {$shengxiao=3;}
    if (strpos($bml,"卯")) {$shengxiao=4;}
    if (strpos($bml,"辰")) {$shengxiao=5;}
    if (strpos($bml,"巳")) {$shengxiao=6;}
    if (strpos($bml,"午")) {$shengxiao=7;}
    if (strpos($bml,"未")) {$shengxiao=8;}
    if (strpos($bml,"申")) {$shengxiao=9;}
    if (strpos($bml,"酉")) {$shengxiao=10;}
    if (strpos($bml,"戌")) {$shengxiao=11;}
    if (strpos($bml,"亥")) {$shengxiao=12;}


switch ($shengxiao)
{
      case 1:
             switch ($sx) {
                   case "鼠":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "豬":
                     $ma=array(2,14,26,38);
                     break;
                   case "狗":
                     $ma=array(3,15,27,39);
                     break;
                   case "雞":
                     $ma=array(4,16,28,40);
                     break;
                   case "猴":
                     $ma=array(5,17,29,41);
                     break;
                   case "羊":
                     $ma=array(6,18,30,42);
                     break;
                   case "馬":
                     $ma=array(7,19,31,43);
                     break;
                   case "蛇":
                     $ma=array(8,20,32,44);
                     break;
                   case "龍":
                     $ma=array(9,21,33,45);
                     break;
                   case "兔":
                     $ma=array(10,22,34,46);
                     break;
                   case "虎":
                     $ma=array(11,23,35,47);
                     break;
                   case "牛":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 2:
              switch ($sx) {
                   case "牛":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "鼠":
                     $ma=array(2,14,26,38);
                     break;
                   case "豬":
                     $ma=array(3,15,27,39);
                     break;
                   case "狗":
                     $ma=array(4,16,28,40);
                     break;
                   case "雞":
                     $ma=array(5,17,29,41);
                     break;
                   case "猴":
                     $ma=array(6,18,30,42);
                     break;
                   case "羊":
                     $ma=array(7,19,31,43);
                     break;
                   case "馬":
                     $ma=array(8,20,32,44);
                     break;
                   case "蛇":
                     $ma=array(9,21,33,45);
                     break;
                   case "龍":
                     $ma=array(10,22,34,46);
                     break;
                   case "兔":
                     $ma=array(11,23,35,47);
                     break;
                   case "虎":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 3:
              switch ($sx) {
                   case "虎":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "牛":
                     $ma=array(2,14,26,38);
                     break;
                   case "鼠":
                     $ma=array(3,15,27,39);
                     break;
                   case "豬":
                     $ma=array(4,16,28,40);
                     break;
                   case "狗":
                     $ma=array(5,17,29,41);
                     break;
                   case "雞":
                     $ma=array(6,18,30,42);
                     break;
                   case "猴":
                     $ma=array(7,19,31,43);
                     break;
                   case "羊":
                     $ma=array(8,20,32,44);
                     break;
                   case "馬":
                     $ma=array(9,21,33,45);
                     break;
                   case "蛇":
                     $ma=array(10,22,34,46);
                     break;
                   case "龍":
                     $ma=array(11,23,35,47);
                     break;
                   case "兔":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 4:
              switch ($sx) {
                   case "兔":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "虎":
                     $ma=array(2,14,26,38);
                     break;
                   case "牛":
                     $ma=array(3,15,27,39);
                     break;
                   case "鼠":
                     $ma=array(4,16,28,40);
                     break;
                   case "豬":
                     $ma=array(5,17,29,41);
                     break;
                   case "狗":
                     $ma=array(6,18,30,42);
                     break;
                   case "雞":
                     $ma=array(7,19,31,43);
                     break;
                   case "猴":
                     $ma=array(8,20,32,44);
                     break;
                   case "羊":
                     $ma=array(9,21,33,45);
                     break;
                   case "馬":
                     $ma=array(10,22,34,46);
                     break;
                   case "蛇":
                     $ma=array(11,23,35,47);
                     break;
                   case "龍":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 5:
             switch ($sx) {
                   case "龍":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "兔":
                     $ma=array(2,14,26,38);
                     break;
                   case "虎":
                     $ma=array(3,15,27,39);
                     break;
                   case "牛":
                     $ma=array(4,16,28,40);
                     break;
                   case "鼠":
                     $ma=array(5,17,29,41);
                     break;
                   case "豬":
                     $ma=array(6,18,30,42);
                     break;
                   case "狗":
                     $ma=array(7,19,31,43);
                     break;
                   case "雞":
                     $ma=array(8,20,32,44);
                     break;
                   case "猴":
                     $ma=array(9,21,33,45);
                     break;
                   case "羊":
                     $ma=array(10,22,34,46);
                     break;
                   case "馬":
                     $ma=array(11,23,35,47);
                     break;
                   case "蛇":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 6:
             switch ($sx) {
                   case "蛇":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "龍":
                     $ma=array(2,14,26,38);
                     break;
                   case "兔":
                     $ma=array(3,15,27,39);
                     break;
                   case "虎":
                     $ma=array(4,16,28,40);
                     break;
                   case "牛":
                     $ma=array(5,17,29,41);
                     break;
                   case "鼠":
                     $ma=array(6,18,30,42);
                     break;
                   case "豬":
                     $ma=array(7,19,31,43);
                     break;
                   case "狗":
                     $ma=array(8,20,32,44);
                     break;
                   case "雞":
                     $ma=array(9,21,33,45);
                     break;
                   case "猴":
                     $ma=array(10,22,34,46);
                     break;
                   case "羊":
                     $ma=array(11,23,35,47);
                     break;
                   case "馬":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 7:
            switch ($sx) {
                   case "馬":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "蛇":
                     $ma=array(2,14,26,38);
                     break;
                   case "龍":
                     $ma=array(3,15,27,39);
                     break;
                   case "兔":
                     $ma=array(4,16,28,40);
                     break;
                   case "虎":
                     $ma=array(5,17,29,41);
                     break;
                   case "牛":
                     $ma=array(6,18,30,42);
                     break;
                   case "鼠":
                     $ma=array(7,19,31,43);
                     break;
                   case "豬":
                     $ma=array(8,20,32,44);
                     break;
                   case "狗":
                     $ma=array(9,21,33,45);
                     break;
                   case "雞":
                     $ma=array(10,22,34,46);
                     break;
                   case "猴":
                     $ma=array(11,23,35,47);
                     break;
                   case "羊":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 8:
            switch ($sx) {
                   case "羊":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "馬":
                     $ma=array(2,14,26,38);
                     break;
                   case "蛇":
                     $ma=array(3,15,27,39);
                     break;
                   case "龍":
                     $ma=array(4,16,28,40);
                     break;
                   case "兔":
                     $ma=array(5,17,29,41);
                     break;
                   case "虎":
                     $ma=array(6,18,30,42);
                     break;
                   case "牛":
                     $ma=array(7,19,31,43);
                     break;
                   case "鼠":
                     $ma=array(8,20,32,44);
                     break;
                   case "豬":
                     $ma=array(9,21,33,45);
                     break;
                   case "狗":
                     $ma=array(10,22,34,46);
                     break;
                   case "雞":
                     $ma=array(11,23,35,47);
                     break;
                   case "猴":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 9:
            switch ($sx) {
                   case "猴":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "羊":
                     $ma=array(2,14,26,38);
                     break;
                   case "馬":
                     $ma=array(3,15,27,39);
                     break;
                   case "蛇":
                     $ma=array(4,16,28,40);
                     break;
                   case "龍":
                     $ma=array(5,17,29,41);
                     break;
                   case "兔":
                     $ma=array(6,18,30,42);
                     break;
                   case "虎":
                     $ma=array(7,19,31,43);
                     break;
                   case "牛":
                     $ma=array(8,20,32,44);
                     break;
                   case "鼠":
                     $ma=array(9,21,33,45);
                     break;
                   case "豬":
                     $ma=array(10,22,34,46);
                     break;
                   case "狗":
                     $ma=array(11,23,35,47);
                     break;
                   case "雞":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 10:
            switch ($sx) {
                   case "雞":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "猴":
                     $ma=array(2,14,26,38);
                     break;
                   case "羊":
                     $ma=array(3,15,27,39);
                     break;
                   case "馬":
                     $ma=array(4,16,28,40);
                     break;
                   case "蛇":
                     $ma=array(5,17,29,41);
                     break;
                   case "龍":
                     $ma=array(6,18,30,42);
                     break;
                   case "兔":
                     $ma=array(7,19,31,43);
                     break;
                   case "虎":
                     $ma=array(8,20,32,44);
                     break;
                   case "牛":
                     $ma=array(9,21,33,45);
                     break;
                   case "鼠":
                     $ma=array(10,22,34,46);
                     break;
                   case "豬":
                     $ma=array(11,23,35,47);
                     break;
                   case "狗":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 11:
            switch ($sx) {
                   case "狗":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "雞":
                     $ma=array(2,14,26,38);
                     break;
                   case "猴":
                     $ma=array(3,15,27,39);
                     break;
                   case "羊":
                     $ma=array(4,16,28,40);
                     break;
                   case "馬":
                     $ma=array(5,17,29,41);
                     break;
                   case "蛇":
                     $ma=array(6,18,30,42);
                     break;
                   case "龍":
                     $ma=array(7,19,31,43);
                     break;
                   case "兔":
                     $ma=array(8,20,32,44);
                     break;
                   case "虎":
                     $ma=array(9,21,33,45);
                     break;
                   case "牛":
                     $ma=array(10,22,34,46);
                     break;
                   case "鼠":
                     $ma=array(11,23,35,47);
                     break;
                   case "豬":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
      case 12:
            switch ($sx) {
                   case "豬":
                     $ma=array(1,13,25,37,49);
                     break;
                   case "狗":
                     $ma=array(2,14,26,38);
                     break;
                   case "雞":
                     $ma=array(3,15,27,39);
                     break;
                   case "猴":
                     $ma=array(4,16,28,40);
                     break;
                   case "羊":
                     $ma=array(5,17,29,41);
                     break;
                   case "馬":
                     $ma=array(6,18,30,42);
                     break;
                   case "蛇":
                     $ma=array(7,19,31,43);
                     break;
                   case "龍":
                     $ma=array(8,20,32,44);
                     break;
                   case "兔":
                     $ma=array(9,21,33,45);
                     break;
                   case "虎":
                     $ma=array(10,22,34,46);
                     break;
                   case "牛":
                     $ma=array(11,23,35,47);
                     break;
                   case "鼠":
                     $ma=array(12,24,36,48);
                     break;
                  }
      break;
}
   return $ma;
}
 

function rwuhang($wh,$bml){
   $nayinstr = "甲子金|乙醜金|丙寅火|丁卯火|戊辰木|己巳木|庚午土|辛未土|壬申金|癸酉金|甲戌火|乙亥火|丙子水|丁醜水|戊寅土|己卯土|庚辰金|辛巳金|壬午木|癸未木|甲申水|乙酉水|丙戌土|丁亥土|戊子火|己醜火|庚寅木|辛卯木|壬辰水|癸巳水|甲午金|乙未金|丙申火|丁酉火|戊戌木|己亥木|庚子土|辛醜土|壬寅金|癸卯金|甲辰火|乙巳火|丙午水|丁未水|戊申土|己酉土|庚戌金|辛亥金|壬子木|癸醜木|甲寅水|乙卯水|丙辰土|丁巳土|戊午火|己未火|庚申木|辛酉木|壬戌水|癸亥水|";	
   $jiazhi = array("甲子","乙醜","丙寅","丁卯","戊辰","己巳","庚午","辛未","壬申","癸酉","甲戌","乙亥","丙子","丁醜","戊寅","己卯","庚辰","辛巳","壬午","癸未","甲申","乙酉","丙戌","丁亥","戊子","己醜","庚寅","辛卯","壬辰","癸巳","甲午","乙未","丙申","丁酉","戊戌","己亥","庚子","辛醜","壬寅","癸卯","甲辰","乙巳","丙午","丁未","戊申","己酉","庚戌","辛亥","壬子","癸醜","甲寅","乙卯","丙辰","丁巳","戊午","己未","庚申","辛酉","壬戌","癸亥");
   if($bml=="庚寅") $tyear = 2010;
   else if($bml=="辛卯") $tyear = 2011;
   else if($bml=="壬辰") $tyear = 2012;
   else if($bml=="癸巳") $tyear = 2013;
   else if($bml=="甲午") $tyear = 2014;
   else if($bml=="乙未") $tyear = 2015;
   else if($bml=="丙申") $tyear = 2016;
   $nylist = nylist($jiazhi,$nayinstr);
   //$w1 = array(); $w2 = array(); $w3 = array(); $w4 = array(); $w5 = array();
   $w=array();
   for($i=1;$i<=49;$i++){
	   $index = $tyear - 1922 - $i - 1;
	   $itemname = $nylist[$index%60];
	   if($itemname==$wh){
	       array_push($w,$i);
	   }
   }
   return $w;
}


function rdanshuang($v)
{
   $v= "|".$v;	
   if(strpos($v,"單")){
       $ds=array(1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47);
   }else if(strpos($v,"雙")){
       $ds=array(2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48);
   } 
   return $ds;
}

function rtetou($v){
	$v=str_replace('特头:','',$v);
    $v=str_replace('头','',$v);
    $str=array();;
	if($v==0) $i=1;
	else $i=0;
	for(;$i<=9;$i++){
	   $str[] = $v.$i;
	}
	return $str;
}

function rtewei($v){
	$v=str_replace('特尾:','',$v);
    $v=str_replace('尾','',$v);
    $str=array();;
	if($v==0) $i=1;
	else $i=0;
	for(;$i<=4;$i++){
	   $str[] = $i.$v;
	}
	return $str;
}


function rdaxiaowei($v)
{
   $v= "|".$v;
   if(strpos($v,"小")){
       $ds=array(1,2,3,4,10,11,12,13,14,20,21,22,23,24,30,31,32,33,34,40,41,42,43,44);
   }else if(strpos($v,"大")){
       $ds=array(5,6,7,8,9,15,16,17,18,19,25,26,27,28,29,35,36,37,38,39,45,46,47,48);
   } 
   return $ds;
}


function rdaxiao($v)
{
   $v= "|".$v;
   if(strpos($v,"小")){
       $ds=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24);
   }else if(strpos($v,"大")){
       $ds=array(25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48);
   } 
   return $ds;
}

function rall($v){
    $all=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49);
	return $all;
}

function rhedaxiao($v)
{
   if($v=="合大"){
       $ds=array(7,16,25,34,43,8,17,26,35,44,9,18,27,36,45,19,28,37,46,29,38,47,39,48);
   }else if($v=="合小"){
       $ds=array(1,10,2,11,20,3,12,21,30,4,13,22,31,40,5,14,23,32,41,6,15,24,33,42);
   } /*
   if($v=="合大"){
       $ds=array(5,6,7,8,9,14,15,16,17,18,23,24,25,26,27,32,33,34,35,36,41,42,43,44,45);
   }else if($v=="合小"){
       $ds=array(1,2,3,4,10,11,12,13,19,20,21,22,28,29,30,31,37,38,39,40,46,47,48);
   }*/
   return $ds;
}

function rhedanshuang($v)
{
   if($v=="合雙"){
       $ds=array(2,4,6,8,11,13,15,17,19,20,22,24,26,28,31,33,35,37,39,40,42,44,46,48);
   }else if($v=="合單"){
       $ds=array(1,3,5,7,9,10,12,14,16,18,21,23,25,27,29,30,32,34,36,38,41,43,45,47);
   } 
   return $ds;
}

function rbose($v){
   $hong=array(1,2,7,8,12,13,18,19,23,24,29,30,34,35,40,45,46);
   $lan=array(3,4,9,10,14,15,20,25,26,31,36,37,41,42,47,48);
   $lv=array(5,6,11,16,17,21,22,27,28,32,33,38,39,43,44,49);
   $v= '|'.$v;
   if(strpos($v,"紅")){
       return $hong;
   }else if(strpos($v,"藍")){
       return $lan;
   }else if(strpos($v,"綠")){
       return $lv;
   }
}

function rbanbo($v){
   $arr1=rbose($v);
   $arr2=rdanshuang($v);
   $arr3=rdaxiao($v);
   //print_r($arr2);
   //exit;
   $a=array();
   if(is_array($arr1) && is_array($arr2)){
	   foreach($arr1 as $arr){
	      if(in_array($arr,$arr2)){
		     $a[]=$arr;
		  }
	   }       
   }else if(is_array($arr1) && is_array($arr3)){
	   foreach($arr1 as $arr){
	      if(in_array($arr,$arr3)){
		     $a[]=$arr;
		  }
	   }
   }
   if($v=='綠單') $a[] = 49;
   if($v=='綠大') $a[] = 49;
   return $a;
}

function rqianhoujiaye($v,$bml){
	$qian= array("鼠","牛","虎","兔","龍","蛇");
	$hou = array("馬","羊","猴","雞","狗","豬");
	$jia = array("牛","馬","羊","雞","狗","豬");
	$ye = array("鼠","猴","虎","兔","龍","蛇");
	$v = "|" . $v;
	if(strpos($v,"前")){
	    $arr = $qian;
	}else if(strpos($v,"後")){
	    $arr = $hou;
	}if(strpos($v,"家")){
	    $arr = $jia;
	}if(strpos($v,"野")){
	    $arr = $ye;
	}
	$a=array();
	foreach( $arr  as $v){
	   $b=rTexiao($v,$bml);
	   foreach($b as $c){
	      $a[]=$c;
	   }
	}
	return $a;
}

function ma(){
    $ma[0]['name'] = '單';
    $ma[0]['ma'] = implode(',',getma("單"));
    $ma[1]['name'] = '雙';
    $ma[1]['ma'] = implode(',',getma("雙"));
    $ma[2]['name'] = '大';
    $ma[2]['ma'] = implode(',',getma("大"));
    $ma[43]['name'] = '小';
    $ma[43]['ma'] = implode(',',getma("小"));
    $ma[3]['name'] = '合單';
    $ma[3]['ma'] = implode(',',getma("合單"));
    $ma[4]['name'] = '合雙';
    $ma[4]['ma'] = implode(',',getma("合雙"));
    $ma[5]['name'] = '大尾';
    $ma[5]['ma'] = implode(',',getma("大尾"));
    $ma[6]['name'] = '小尾';
    $ma[6]['ma'] = implode(',',getma("小尾"));
    $ma[7]['name'] = '紅';
    $ma[7]['ma'] = implode(',',getma("紅"));
    $ma[8]['name'] = '藍';
    $ma[8]['ma'] = implode(',',getma("藍"));
    $ma[9]['name'] = '綠';
    $ma[9]['ma'] = implode(',',getma("綠"));
    $ma[10]['name'] = '金';
    $ma[10]['ma'] = implode(',',getma("金"));
    $ma[11]['name'] = '木';
    $ma[11]['ma'] = implode(',',getma("木"));
    $ma[12]['name'] = '水';
    $ma[12]['ma'] = implode(',',getma("水"));
    $ma[13]['name'] = '火';
    $ma[13]['ma'] = implode(',',getma("火"));
    $ma[14]['name'] = '土';
    $ma[14]['ma'] = implode(',',getma("土"));
    $ma[15]['name'] = '鼠';
    $ma[15]['ma'] = implode(',',getma("鼠"));
    $ma[16]['name'] = '牛';
    $ma[16]['ma'] = implode(',',getma("牛"));
    $ma[17]['name'] = '虎';
    $ma[17]['ma'] = implode(',',getma("虎"));
    $ma[18]['name'] = '兔';
    $ma[18]['ma'] = implode(',',getma("兔"));
    $ma[19]['name'] = '龍';
    $ma[19]['ma'] = implode(',',getma("龍"));
    $ma[20]['name'] = '蛇';
    $ma[20]['ma'] = implode(',',getma("蛇"));
    $ma[21]['name'] = '馬';
    $ma[21]['ma'] = implode(',',getma("馬"));
    $ma[22]['name'] = '羊';
    $ma[22]['ma'] = implode(',',getma("羊"));
    $ma[23]['name'] = '猴';
    $ma[23]['ma'] = implode(',',getma("猴"));
    $ma[24]['name'] = '雞';
    $ma[24]['ma'] = implode(',',getma("雞"));
    $ma[25]['name'] = '狗';
    $ma[25]['ma'] = implode(',',getma("狗"));
    $ma[26]['name'] = '豬';
    $ma[26]['ma'] = implode(',',getma("豬"));
    $ma[27]['name'] = '1头';
    $ma[27]['ma'] = implode(',',getma("1头"));
    $ma[28]['name'] = '2头';
    $ma[28]['ma'] = implode(',',getma("2头"));
    $ma[29]['name'] = '3头';
    $ma[29]['ma'] = implode(',',getma("3头"));
    $ma[30]['name'] = '4头';
    $ma[30]['ma'] = implode(',',getma("4头"));
    $ma[31]['name'] = '0头';
    $ma[31]['ma'] = implode(',',getma("0头"));
    $ma[32]['name'] = '1尾';
    $ma[32]['ma'] = implode(',',getma("1尾"));
    $ma[33]['name'] = '2尾';
    $ma[33]['ma'] = implode(',',getma("2尾"));
    $ma[34]['name'] = '3尾';
    $ma[34]['ma'] = implode(',',getma("3尾"));
    $ma[35]['name'] = '4尾';
    $ma[35]['ma'] = implode(',',getma("4尾"));
    $ma[36]['name'] = '5尾';
    $ma[36]['ma'] = implode(',',getma("5尾"));
    $ma[37]['name'] = '6尾';
    $ma[37]['ma'] = implode(',',getma("6尾"));
    $ma[38]['name'] = '7尾';
    $ma[38]['ma'] = implode(',',getma("7尾"));
    $ma[39]['name'] = '8尾';
    $ma[39]['ma'] = implode(',',getma("8尾"));
    $ma[40]['name'] = '9尾';
    $ma[40]['ma'] = implode(',',getma("9尾"));
    $ma[41]['name'] = '0尾';
    $ma[41]['ma'] = implode(',',getma("0尾"));
    $ma[42]['name'] = '全部';
    $ma[42]['ma'] = implode(',',getma("全部"));
	$ma[44]['name'] = "家畜";
	$ma[44]['ma'] = implode(',',getma("家"));
	$ma[45]['name'] = "野兽";
	$ma[45]['ma'] = implode(',',getma("野"));
	$ma[46]['name'] = "前";
	$ma[46]['ma'] = implode(',',getma("前"));
	$ma[47]['name'] = "後";
	$ma[47]['ma'] = implode(',',getma("後"));
	$ma[48]['name'] = "紅單";
	$ma[48]['ma'] = implode(',',getma("紅單"));
	$ma[49]['name'] = "紅雙";
	$ma[49]['ma'] = implode(',',getma("紅雙"));
	$ma[50]['name'] = "紅大";
	$ma[50]['ma'] = implode(',',getma("紅大"));
	$ma[51]['name'] = "紅小";
	$ma[51]['ma'] = implode(',',getma("紅小"));
	$ma[52]['name'] = "藍單";
	$ma[52]['ma'] = implode(',',getma("藍單"));
	$ma[53]['name'] = "藍雙";
	$ma[53]['ma'] = implode(',',getma("藍雙"));
	$ma[54]['name'] = "藍大";
	$ma[54]['ma'] = implode(',',getma("藍大"));
	$ma[55]['name'] = "藍小";
	$ma[55]['ma'] = implode(',',getma("藍小"));
	$ma[56]['name'] = "綠單";
	$ma[56]['ma'] = implode(',',getma("綠單"));
	$ma[57]['name'] = "綠雙";
	$ma[57]['ma'] = implode(',',getma("綠雙"));
	$ma[58]['name'] = "綠大";
	$ma[58]['ma'] = implode(',',getma("綠大"));
	$ma[59]['name'] = "綠小";
	$ma[59]['ma'] = implode(',',getma("綠小"));
	$ma[60]['name'] = "合大";
	$ma[60]['ma'] = implode(',',getma("合大"));
	$ma[61]['name'] = "合小";
	$ma[61]['ma'] = implode(',',getma("合小"));
	
	$ma[0]['i'] = 0;
	$ma[1]['i'] = 1;
	$ma[2]['i'] = 2;
	$ma[3]['i'] = 3;
	$ma[4]['i'] = 4;
	$ma[5]['i'] = 5;
	$ma[6]['i'] = 6;
	$ma[7]['i'] = 7;
	$ma[8]['i'] = 8;
	$ma[9]['i'] = 9;
	$ma[10]['i'] = 10;
	$ma[11]['i'] = 11;
	$ma[12]['i'] = 12;
	$ma[13]['i'] = 13;
	$ma[14]['i'] = 14;
	$ma[15]['i'] = 15;
	$ma[16]['i'] = 16;
	$ma[17]['i'] = 17;
	$ma[18]['i'] = 18;
	$ma[19]['i'] = 19;
	$ma[20]['i'] = 20;
	$ma[21]['i'] = 21;
	$ma[22]['i'] = 22;
	$ma[23]['i'] = 23;
	$ma[24]['i'] = 24;
	$ma[25]['i'] = 25;
	$ma[26]['i'] = 26;
	$ma[27]['i'] = 27;
	$ma[28]['i'] = 28;
	$ma[29]['i'] = 29;
	$ma[30]['i'] = 30;
	$ma[31]['i'] = 31;
	$ma[32]['i'] = 32;
	$ma[33]['i'] = 33;
	$ma[34]['i'] = 34;
	$ma[35]['i'] = 35;
	$ma[36]['i'] = 36;
	$ma[37]['i'] = 37;
	$ma[38]['i'] = 38;
	$ma[39]['i'] = 39;
	$ma[40]['i'] = 40;
	$ma[41]['i'] = 41;
	$ma[42]['i'] = 42;
	$ma[43]['i'] = 43;
	$ma[44]['i'] = 44;
	$ma[45]['i'] = 45;
	$ma[46]['i'] = 46;
	$ma[47]['i'] = 47;
	$ma[48]['i'] = 48;
	$ma[49]['i'] = 49;
	$ma[50]['i'] = 50;
	$ma[51]['i'] = 51;
	$ma[52]['i'] = 52;
	$ma[53]['i'] = 53;
	$ma[54]['i'] = 54;
	$ma[55]['i'] = 55;
	$ma[56]['i'] = 56;
	$ma[57]['i'] = 57;
	$ma[58]['i'] = 58;
	$ma[59]['i'] = 59;
	$ma[60]['i'] = 60;
	$ma[61]['i'] = 61;
	return $ma;
}

function getma($ma){
	global $thisbml;
	$bml=$thisbml;
	
	if(strpos("|單雙",$ma)){
					    $m=rdanshuang($ma);
					}else if(strpos("|大小",$ma)){
				        $m=rdaxiao($ma);
				    }else if(strpos("|合大合小",$ma)){
				        $m=rhedaxiao($ma);
				    }else if(strpos("|合單合雙",$ma)){
				        $m=rhedanshuang($ma);
				    }else if(strpos("|大尾小尾",$ma)){
				        $m=rdaxiaowei($ma);
				    }else if(strpos("|紅藍綠",$ma)){
				        $m=rbose($ma);
				    }else if(strpos("|紅單紅雙藍單藍雙綠單綠雙紅大紅小綠大綠小藍大藍小",$ma)){
				        $m=rbanbo($ma);
				    }else if(strpos("|鼠牛虎兔龍蛇馬羊猴雞狗豬",$ma)){
				        $m=rTexiao($ma,$bml);
				    }else if(strpos("|前後家野",$ma)){
				        $m=rqianhoujiaye($ma,$bml);
				    }else if(strpos("|金木水火土",$ma)){
				        $m=rwuhang($ma,$bml);
				    }else if(strpos("|0头1头2头3头4头",$ma)){
				        $m=rtetou($ma,$bml);
				    }else if(strpos("|0尾1尾2尾3尾4尾5尾6尾7尾8尾9尾",$ma)){
				        $m=rtewei($ma,$bml);
				    }else if(strpos("|全部",$ma)){
					    $m=rall($ma);
					}else if (strpos($ma,"||")){
				        $m=explode('|',$ma);						
						$ma=array();
						foreach($m as $v){
						   if($v!='') $ma[]=$v; 
						}
						
						return $ma;
	}

	return $m;		
		
}

