<?php
/*
 * Created on 2008-12-29
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */ 
function texiao($tm,$bml) {
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

if ($tm<13) {$sxy=$tm;}
else        {$sxy=$tm%12;}

switch ($shengxiao)
{
      case 1:
             switch ($sxy) {
                   case 1:
                     $Texiao="鼠";
                     break;
                   case 2:
                     $Texiao="豬";
                     break;
                   case 3:
                     $Texiao="狗";
                     break;
                   case 4:
                     $Texiao="雞";
                     break;
                   case 5:
                     $Texiao="猴";
                     break;
                   case 6:
                     $Texiao="羊";
                     break;
                   case 7:
                     $Texiao="馬";
                     break;
                   case 8:
                     $Texiao="蛇";
                     break;
                   case 9:
                     $Texiao="龍";
                     break;
                   case 10:
                     $Texiao="兔";
                     break;
                   case 11:
                     $Texiao="虎";
                     break;
                   default:
                     $Texiao="牛";
                     break;
                          }
      break;
      case 2:
             switch ($sxy) {
                   case 1:
                     $Texiao="牛";
                     break;
                   case 2:
                     $Texiao="鼠";
                     break;
                   case 3:
                     $Texiao="豬";
                     break;
                   case 4:
                     $Texiao="狗";
                     break;
                   case 5:
                     $Texiao="雞";
                     break;
                   case 6:
                     $Texiao="猴";
                     break;
                   case 7:
                     $Texiao="羊";
                     break;
                   case 8:
                     $Texiao="馬";
                     break;
                   case 9:
                     $Texiao="蛇";
                     break;
                   case 10:
                     $Texiao="龍";
                     break;
                   case 11:
                     $Texiao="兔";
                     break;
                   default:
                     $Texiao="虎";
                     break;
                          }
      break;
      case 3:
             switch ($sxy) {
                   case 1:
                     $Texiao="虎";
                     break;
                   case 2:
                     $Texiao="牛";
                     break;
                   case 3:
                     $Texiao="鼠";
                     break;
                   case 4:
                     $Texiao="豬";
                     break;
                   case 5:
                     $Texiao="狗";
                     break;
                   case 6:
                     $Texiao="雞";
                     break;
                   case 7:
                     $Texiao="猴";
                     break;
                   case 8:
                     $Texiao="羊";
                     break;
                   case 9:
                     $Texiao="馬";
                     break;
                   case 10:
                     $Texiao="蛇";
                     break;
                   case 11:
                     $Texiao="龍";
                     break;
                   default:
                     $Texiao="兔";
                     break;
                          }
      break;
      case 4:
             switch ($sxy) {
                   case 1:
                     $Texiao="兔";
                     break;
                   case 2:
                     $Texiao="虎";
                     break;
                   case 3:
                     $Texiao="牛";
                     break;
                   case 4:
                     $Texiao="鼠";
                     break;
                   case 5:
                     $Texiao="豬";
                     break;
                   case 6:
                     $Texiao="狗";
                     break;
                   case 7:
                     $Texiao="雞";
                     break;
                   case 8:
                     $Texiao="猴";
                     break;
                   case 9:
                     $Texiao="羊";
                     break;
                   case 10:
                     $Texiao="馬";
                     break;
                   case 11:
                     $Texiao="蛇";
                     break;
                   default:
                     $Texiao="龍";
                     break;
                          }
      break;
      case 5:
             switch ($sxy) {
                   case 1:
                     $Texiao="龍";
                     break;
                   case 2:
                     $Texiao="兔";
                     break;
                   case 3:
                     $Texiao="虎";
                     break;
                   case 4:
                     $Texiao="牛";
                     break;
                   case 5:
                     $Texiao="鼠";
                     break;
                   case 6:
                     $Texiao="豬";
                     break;
                   case 7:
                     $Texiao="狗";
                     break;
                   case 8:
                     $Texiao="雞";
                     break;
                   case 9:
                     $Texiao="猴";
                     break;
                   case 10:
                     $Texiao="羊";
                     break;
                   case 11:
                     $Texiao="馬";
                     break;
                   default:
                     $Texiao="蛇";
                     break;
                          }
      break;
      case 6:
             switch ($sxy) {
                   case 1:
                     $Texiao="蛇";
                     break;
                   case 2:
                     $Texiao="龍";
                     break;
                   case 3:
                     $Texiao="兔";
                     break;
                   case 4:
                     $Texiao="虎";
                     break;
                   case 5:
                     $Texiao="牛";
                     break;
                   case 6:
                     $Texiao="鼠";
                     break;
                   case 7:
                     $Texiao="豬";
                     break;
                   case 8:
                     $Texiao="狗";
                     break;
                   case 9:
                     $Texiao="雞";
                     break;
                   case 10:
                     $Texiao="猴";
                     break;
                   case 11:
                     $Texiao="羊";
                     break;
                   default:
                     $Texiao="馬";
                     break;
                          }
      break;
      case 7:
             switch ($sxy) {
                   case 1:
                     $Texiao="馬";
                     break;
                   case 2:
                     $Texiao="蛇";
                     break;
                   case 3:
                     $Texiao="龍";
                     break;
                   case 4:
                     $Texiao="兔";
                     break;
                   case 5:
                     $Texiao="虎";
                     break;
                   case 6:
                     $Texiao="牛";
                     break;
                   case 7:
                     $Texiao="鼠";
                     break;
                   case 8:
                     $Texiao="豬";
                     break;
                   case 9:
                     $Texiao="狗";
                     break;
                   case 10:
                     $Texiao="雞";
                     break;
                   case 11:
                     $Texiao="猴";
                     break;
                   default:
                     $Texiao="羊";
                     break;
                          }
      break;
      case 8:
             switch ($sxy) {
                   case 1:
                     $Texiao="羊";
                     break;
                   case 2:
                     $Texiao="馬";
                     break;
                   case 3:
                     $Texiao="蛇";
                     break;
                   case 4:
                     $Texiao="龍";
                     break;
                   case 5:
                     $Texiao="兔";
                     break;
                   case 6:
                     $Texiao="虎";
                     break;
                   case 7:
                     $Texiao="牛";
                     break;
                   case 8:
                     $Texiao="鼠";
                     break;
                   case 9:
                     $Texiao="豬";
                     break;
                   case 10:
                     $Texiao="狗";
                     break;
                   case 11:
                     $Texiao="雞";
                     break;
                   default:
                     $Texiao="猴";
                     break;
                          }
      break;
      case 9:
             switch ($sxy) {
                   case 1:
                     $Texiao="猴";
                     break;
                   case 2:
                     $Texiao="羊";
                     break;
                   case 3:
                     $Texiao="馬";
                     break;
                   case 4:
                     $Texiao="蛇";
                     break;
                   case 5:
                     $Texiao="龍";
                     break;
                   case 6:
                     $Texiao="兔";
                     break;
                   case 7:
                     $Texiao="虎";
                     break;
                   case 8:
                     $Texiao="牛";
                     break;
                   case 9:
                     $Texiao="鼠";
                     break;
                   case 10:
                     $Texiao="豬";
                     break;
                   case 11:
                     $Texiao="狗";
                     break;
                   default:
                     $Texiao="雞";
                     break;
                          }
      break;
      case 10:
             switch ($sxy) {
                   case 1:
                     $Texiao="雞";
                     break;
                   case 2:
                     $Texiao="猴";
                     break;
                   case 3:
                     $Texiao="羊";
                     break;
                   case 4:
                     $Texiao="馬";
                     break;
                   case 5:
                     $Texiao="蛇";
                     break;
                   case 6:
                     $Texiao="龍";
                     break;
                   case 7:
                     $Texiao="兔";
                     break;
                   case 8:
                     $Texiao="虎";
                     break;
                   case 9:
                     $Texiao="牛";
                     break;
                   case 10:
                     $Texiao="鼠";
                     break;
                   case 11:
                     $Texiao="豬";
                     break;
                   default:
                     $Texiao="狗";
                     break;
                          }
      break;
      case 11:
             switch ($sxy) {
                   case 1:
                     $Texiao="狗";
                     break;
                   case 2:
                     $Texiao="雞";
                     break;
                   case 3:
                     $Texiao="猴";
                     break;
                   case 4:
                     $Texiao="羊";
                     break;
                   case 5:
                     $Texiao="馬";
                     break;
                   case 6:
                     $Texiao="蛇";
                     break;
                   case 7:
                     $Texiao="龍";
                     break;
                   case 8:
                     $Texiao="兔";
                     break;
                   case 9:
                     $Texiao="虎";
                     break;
                   case 10:
                     $Texiao="牛";
                     break;
                   case 11:
                     $Texiao="鼠";
                     break;
                   default:
                     $Texiao="豬";
                     break;
                          }
      break;
      default:
             switch ($sxy) {
                   case 1:
                     $Texiao="豬";
                     break;
                   case 2:
                     $Texiao="狗";
                     break;
                   case 3:
                     $Texiao="雞";
                     break;
                   case 4:
                     $Texiao="猴";
                     break;
                   case 5:
                     $Texiao="羊";
                     break;
                   case 6:
                     $Texiao="馬";
                     break;
                   case 7:
                     $Texiao="蛇";
                     break;
                   case 8:
                     $Texiao="龍";
                     break;
                   case 9:
                     $Texiao="兔";
                     break;
                   case 10:
                     $Texiao="虎";
                     break;
                   case 11:
                     $Texiao="牛";
                     break;
                   default:
                     $Texiao="鼠";
                     break;
                          }
      break;
}
if ($tm==0) {return "";}
else        {return $Texiao;}
}

function tmsx($tm,$bml){
	      $sxy=Texiao($tm,$bml);
	             switch ($sxy) {
                   case "鼠":
                      $tmsx="1";
                     break;
                   case "牛":
                      $tmsx="2";
                     break;
                   case "虎":
                      $tmsx="3";
                     break;
                   case "兔":
                      $tmsx="4";
                     break;
                   case "龍":
                      $tmsx="5";
                     break;
                   case "蛇":
                      $tmsx="6";
                     break;
                   case "馬":
                      $tmsx="7";
                     break;
                   case "羊":
                      $tmsx="8";
                     break;
                   case "猴":
                      $tmsx="9";
                     break;
                   case "雞":
                      $tmsx="10";
                     break;
                   case "狗":
                      $tmsx="11";
                     break;
                   default:
                      $tmsx="12";
                     break;
                          }
             return $tmsx;
}


function wuhang($tm,$bml){
    $nayinstr = "甲子金|乙醜金|丙寅火|丁卯火|戊辰木|己巳木|庚午土|辛未土|壬申金|癸酉金|甲戌火|乙亥火|丙子水|丁醜水|戊寅土|己卯土|庚辰金|辛巳金|壬午木|癸未木|甲申水|乙酉水|丙戌土|丁亥土|戊子火|己醜火|庚寅木|辛卯木|壬辰水|癸巳水|甲午金|乙未金|丙申火|丁酉火|戊戌木|己亥木|庚子土|辛醜土|壬寅金|癸卯金|甲辰火|乙巳火|丙午水|丁未水|戊申土|己酉土|庚戌金|辛亥金|壬子木|癸醜木|甲寅水|乙卯水|丙辰土|丁巳土|戊午火|己未火|庚申木|辛酉木|壬戌水|癸亥水|";	
    $jiazhi = array("甲子","乙醜","丙寅","丁卯","戊辰","己巳","庚午","辛未","壬申","癸酉","甲戌","乙亥","丙子","丁醜","戊寅","己卯","庚辰","辛巳","壬午","癸未","甲申","乙酉","丙戌","丁亥","戊子","己醜","庚寅","辛卯","壬辰","癸巳","甲午","乙未" ,"丙申","丁酉","戊戌","己亥","庚子","辛醜","壬寅","癸卯","甲辰","乙巳","丙午","丁未","戊申","己酉","庚戌","辛亥","壬子","癸醜","甲寅","乙卯","丙辰","丁巳","戊午","己未","庚申","辛酉","壬戌","癸亥");
    $cj = count($jiazhi);
	for($i=0;$i<$cj;$i++){
	     if($bml==$jiazhi[$i]){
		    break;
		 }
	}
	$tyear = 1984 +$i;
	$nylist = nylist($jiazhi,$nayinstr);
	$index = $tyear - 1922 - $tm - 1;
	return $nylist[$index%60];

}

function wuhangx($tm,$bml){
    $z=wuhang($tm,$bml);
	if($z=='金'){
	    $z=1;
	}else if($z=='木'){
	    $z=2;
	}else if($z=='水'){
	    $z=3;
	}else if($z=='火'){
	    $z=4;
	}else if($z=='土'){
	    $z=5;
	}
	return $z;
}
function danshuang($tm)
{
if ($tm==49) 	{return "和";}
if (($tm%2)==1) {$danshuang="<font color=red>單</font>";}
else           {$danshuang="<font color=blue>雙</font>";}
return $danshuang;
}

function zfdanshuangx($tm)
{
if (($tm%2)==1) {$danshuang="總單";}
else           {$danshuang="總雙";}
return $danshuang;
}

function danshuangx($tm)
{
if ($tm==49) 	{$danshuang="和";}
else if (($tm%2)==1) {$danshuang="單";}
else           {$danshuang="雙";}
return $danshuang;
}

function danshuang_1($tm)
{
if (($tm%2)==1) {$danshuang="1";}
else           {$danshuang="2";}
return $danshuang;
}

function daxiao($tm)
{
if ($tm==49) 	{return "和";}
if ($tm<25) {$daxiao="<font color=red>小</font>";}
else        {$daxiao="<font color=blue>大</font>";}
return $daxiao;
}

function zfdaxiao($tm)
{
if ($tm<175) {$daxiao="<font color=red>小</font>";}
else        {$daxiao="<font color=blue>大</font>";}
return $daxiao;
}

function daxiaox($tm)
{
if ($tm==49) 	{$daxiao="和";}
else if ($tm<25) {$daxiao="小";}
else        {$daxiao="大";}
return $daxiao;
}

function zfdaxiaox($tm)
{
if ($tm<175) {$daxiao="總小";}
else        {$daxiao="總大";}
return $daxiao;
}

function daxiao_1($tm)
{
if ($tm<25) {$daxiao="2";}
else        {$daxiao="1";}
return $daxiao;
}

function tetou($tm)
{
  if ($tm<=10) {$tetou=0;}
  else
  {
   $tetou=($tm-$tm%10)/10;
  }
   return $tetou;
}

function menshu($tm)
{
  if ($tm<10) {$menshu="1";}
  else
  {
   $menshu=($tm-$tm%10)/10+1;
  }

   switch ($menshu){
   	case 1:
   	break;
   	$tsx="一门";
   	case 2:
   	break;
   	$tsx="二门";
   	case 3:
   	break;
   	$tsx="三门";
   	case 4:
   	break;
   	$tsx="四门";
   	default:
   	break;
   	$tsx="五门";

   }
   return $tsx;
}

function menshu_1($tm)
{
  if ($tm<10) {$menshu="1";}
  else
  {
   $menshu=($tm-$tm%10)/10+1;
  }

  return $menshu;
}

function weishudaxiao($tm)
{
   $weishu=$tm%10;
   if($tm==49) {return "和";}
 if ($weishu<5){return "<font color=red>小</font>";}
 else          {return "<font color=blue>大</font>";}
}


function weishudaxiao_1($tm)
{
   $weishu=$tm%10;
 if ($weishu<5){return "2";}
 else          {return "1";}
}

function weishu($tm)
{
  $wsx=$tm%10;
  return $wsx;
}
function weishu_1($tm)
{
  $wsx=$tm%10;
  if ($wsx==0) $wsx=10;
  return $wsx;
}

function weishudaxiaox($tm)
{
 $weishu=$tm%10;
 if($tm==49) return "和";
 if ($weishu<5){return "小尾";}
 else          {return "大尾";}
}

function weishux($tm){
   $weishu=$tm%10;
   return $weishu."尾";
}


function heshu($tm)
{
$heshu=$tm%10+($tm-$tm%10)/10;
return $heshu;
} 

function heshudaxiao($tm)
{
$heshu=$tm%10+($tm-$tm%10)/10;
   if ($tm==49) 	{return "和";}
   if ($heshu<7){
   	return "<font color=red>小</font>";
   }
   else{
   	return "<font color=blue>大</font>";
   }
}

function heshudaxiaox($tm)
{
$heshu=$tm%10+($tm-$tm%10)/10;
  if ($tm==49) 	{return "和";}
 else if ($heshu>=7){
   	return "合大";
  }else{
   	return "合小";
  }
}



function heshudaxiao_1($tm)
{
$heshu=$tm%10+($tm-$tm%10)/10;
   if ($heshu>=5 & $heshu<=9){
   	return "1";
   }
   else{
   	return "2";
   }
}

function heshudanshuang($tm)
{
$hedanshuang=heshu($tm);
if ($tm==49) 	{return "和";}
if (($hedanshuang%2)==1) {
	return "<font color=red>合單</font>";
}
else{
	return "<font color=blue>合雙</font>";
}
}

function heshudanshuangx($tm)
{
$hedanshuang=heshu($tm);
if ($tm==49) 	{return "和";}
else if (($hedanshuang%2)==1) {
	return "合單";
}
else{
	return "合雙";
}
}

function jiayex($tm,$bml){
  if ($tm==49) 	{return "和";}	
  $jiashou=Texiao($tm,$bml);
  if (strpos("|牛豬馬羊雞狗",$jiashou)) {return "家禽";}
  else {return "野獸";}
}

function heshudanshuang_1($tm)
{
$hedanshuang=heshu($tm);
if (($hedanshuang%2)==1) {
	return "1";
}
else{
	return "2";
}
}

function week($year,$date)
{
$datex=explode("-",$date);
$month=$datex[0];
$day=$datex[1];
/*try{
$wday=jddayofweek(cal_to_jd(CAL_GREGORIAN,$month,$day,$year),0);
}catch(Exception $e){*/
$wday=date("w", mktime(0, 0, 0, $month,$day,$year));
//}
if ($wday==0) {$wday="周日";}
if ($wday==1) {$wday="周一";}
if ($wday==2) {$wday="周二";}
if ($wday==3) {$wday="周三";}
if ($wday==4) {$wday="周四";}
if ($wday==5) {$wday="周五";}
if ($wday==6) {$wday="周六";}
return $wday;
}


function color($tm)
{
	 $z=bose($tm);
     if ($z==1){return "red";}
	 if ($z==2){return "blue";}
	 if ($z==3){return "green";}
	 if($z=='') return "#000";

}

function colorx($tm)
{
	 $z=bose($tm);
     if ($z==1){return "紅";}
	 if ($z==2){return "藍";}
	 if ($z==3){return "綠";}

}

function bose($tm)
{
$w1="01,02,07,08,12,13,18,19,23,24,29,30,34,35,40,45,46";
$w2="03,04,09,10,14,15,20,25,26,31,36,37,41,42,47,48";
$w3="05,06,11,16,17,21,22,27,28,32,33,38,39,43,44,49";
    for ($j=1;$j<4;$j++)
   {
        $x="w".$j;
	$y=explode(",",$$x);
	foreach($y as $y1)
	{
	   if ($y1==$tm) {$z=$j; break;}
	}
	if ($z != "") {break;}
}
 return $z;
}

function jiashou($tm,$bml) {
 $jiashou=Texiao($tm,$bml);
 if($tm==49) {return "和";}
 if (strspn($jiashou,"牛豬馬羊雞狗")==3) {return "<font color=red>家畜</font>";}
                               else {return "<font color=blue>野兽</font>";}

}



function qianhou($tm,$bml) {
 $qianhou=Texiao($tm,$bml);
 if (strspn($qianhou,"鼠牛虎兔龍蛇")==3) {return "<font color=red>前</font>";}
                               else {return "<font color=blue>后</font>";}

}



function banbox($v){
   $arr1=bosenew($v);
   $arr2=danshuangnew($v);
   $arr3=daxiaonew($v);

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
   return $a;
}

function  bosenew($v){
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
function danshuangnew($v)
{
   $v= "|".$v;	
   if(strpos($v,"單")){
       $ds=array(1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49);
   }else if(strpos($v,"雙")){
       $ds=array(2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48);
   } 
   return $ds;
}
function daxiaonew($v)
{
   $v= "|".$v;
   if(strpos($v,"小")){
       $ds=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24);
   }else if(strpos($v,"大")){
       $ds=array(25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49);
   } 
   return $ds;
}



function banbo($tm){
	if ($tm%2==0){
		switch (bose($tm)){
			case 1:
			 return 2;
			 break;
			case 2:
			 return 4;
			 break;
			default:
			 return 6;
			 break;
		}
	}else{
		switch (bose($tm)){
			case 1:
			 return 1;
			 break;
			case 2:
			 return 3;
			 break;
			default:
			 return 5;
			 break;
		}
	}

}



function tm($tm){
	return $tm;
}

?>