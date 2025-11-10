<?php
function BW_gametype($gid,$dftype)
{  
    if(in_array($dftype, [16,18,19])){
        return 1;
    }else if($dftype==0){
        return 1;
    }else{
        return 19;
    }
    
}
function BW_JSON($gid, $zd, $qishu, $fenlei)
{
    $send = [];
    $times = BW_time();
    foreach ($zd as $k => $v) {
        $con = explode('-', $v["content"]);
        $cc = count($con);
        $pl = [];
        for ($i = 0; $i < $cc; $i++) {
            $pl[$i] = $v["pl"][$con[$i]];
        }
        if ($v["dftype"] == 16) {
            $send[$k]["rate"] = implode(",", $pl);
            $send[$k]["s0"] = implode(",", $con);
            $send[$k]["property"] = BW_propertyid($v["name"]);
            $send[$k]["amount"] = $v["je"];
            $send[$k]["totCount"] = 1;
            $send[$k]["t_no"] = $send[$k]["rate"];
            $send[$k]["t_rate"] = BW_lowpeilv($pl);
            $send[$k]["pid"] = $v["pid"];
            $send[$k]["url"] = BW_xdurl($v["dftype"]);
            $send[$k]["content"] = $v["content"];
            $send[$k]["client_order_id"] = $times;
        } else if ($v["dftype"] == 18 || $v["dftype"] == 19) {
            $send[$k]["rate"] = implode(",", $pl);
            $send[$k]["property"] = BW_propertyid($v["name"]);
            $send[$k]["gameType"] = BW_gametype($gid,$v["dftype"]);
            $send[$k]["chBall"] = implode(",", $con);
            $send[$k]["amount"] = $v["je"];
            $send[$k]["head"] = "";
            $send[$k]["zhs"] = 1;
            $send[$k]["tt"] = 1;
            $send[$k]["title"] = BW_title($v["name"]);
            $send[$k]["client_order_id"] = $times;
            $send[$k]["pid"] = $v["pid"];
            $send[$k]["url"] = BW_xdurl($v["dftype"]);
            $send[$k]["content"] = $v["content"];
        }else{
            $send[$k]["id_0"] = BW_propertyid($v["name"]);
            if($v["dftype"]==0){
                 $send[$k]["rate_0"] = $v["pl"][$v["name"]-1];
            }else{
                 $send[$k]["rate_0"] = $v["pl"][$v["name"]];
            }            
            $send[$k]["amount_0"] = $v["je"];
            $send[$k]["no_0"] = $v["name"];
            $send[$k]["type"] = BW_gametype($gid,$v["dftype"]);
            $send[$k]["team_id"] = $send[$k]["type"];
            $send[$k]["client_order_id"] = $times;
            $send[$k]["tot_num"] = 1;
            $send[$k]["teamCode"] = BW_teamCode($v["dftype"]);
            $send[$k]["gClass"] =   BW_gClass($v["name"]);
            $send[$k]["url"] = BW_xdurl($v["dftype"]);
            $send[$k]["pid"] = $v["pid"];
            $send[$k]["content"] = $v["content"];
        }
        $times++;
    }
    return $send;
}
function BW_teamCode(){
    if($dftype==0){
        return "sp";
    }else{
        return "oth";
    }
}
function BW_gClass($pname){
    if(is_numeric($pname)){
        return 1;
    }else{
        return 8;
    }
}
function BW_lowpeilv($pl)
{
    $p = 9999;
    $tmp = '';
    foreach ($pl as $k => $v) {
        if (strpos($v, '/') !== false) {
            $tmp = explode('/', $v);
            if ($tmp[0] < $p) {
                $p = $tmp[0];
            }
        } else {
            $v < $p && ($p = $v);
        }
    }
    return $p;
}
function BW_xdurl($dftype)
{
    $url = '';
    switch ($dftype) {
        case 18:
            $url = "/Bet/Betsave_twl";
            break;
        case 19:
            $url = "/Bet/Betsave_taill";
            break;
        case 16:
            $url = "/Bet/Betsave_lm_fs";
            break;
        case 11:
        case 12:
        case 0:
            $url = "/Bet/Betsave";
        break;    
    }
    return $url;
}
function BW_gettype($dftype)
{
    $type = '';
    switch ($dftype) {
        case 18:
            $type = 14;
            break;
        case 19:
            $type = 15;
            break;
        case 16:
            $type = 4;
            break;
        case 11:
        case 12:
            $type=16;
        break;
        case 0:
            $type =1;
        break;   
    }
    return $type;
}
function BW_getpl($str)
{
    $str = explode('@@', $str);
    $str = explode(';', $str[1]);
    $tmp = [];
    foreach ($str as $k => $v) {
        $tmp = explode(',', $v);
        if ($tmp[1] != "" && $tmp[2] != "") {
            $pl[$tmp[1]] = $tmp[2];
        }
    }
    return $pl;
}
function BW_getpls($str)
{
    $str = explode('&', $str);
    $str = explode(';', $str[1]);
    $tmp = [];
    foreach ($str as $k => $v) {
        $tmp = explode(',', $v);
        if ($tmp[1] != "" && $tmp[2] != "") {
            $pl[$tmp[1]] = $tmp[2];
        }
    }
    return $pl;
}
function BW_getpls2($str)
{
    $str = explode('&&', $str);
    $str = explode(';', $str[1]);
    $tmp = [];
    foreach ($str as $k => $v) {
        if($k>48) break;
        $tmp = explode(',', $v);
        if ($tmp[1] != "" && $tmp[2] != "") {
            $pl[$tmp[1]-1] = $tmp[2];
        }
    }
    //print_r($pl);
    return $pl;
}
function BW_propertyid($pname)
{
    $val = '';
    switch ($pname) {
        case '二肖連(中)':
            $val = 55;
            break;
        case '三肖連(中)':
            $val = 56;
            break;
        case '四肖連(中)':
            $val = 57;
            break;
        case '五肖連(中)':
            $val = 61;
            break;
        case '二肖連(不中)':
            $val = 58;
            break;
        case '三肖連(不中)':
            $val = 59;
            break;
        case '四肖連(不中)':
            $val = 60;
            break;
        case '五肖連(不中)':
            $val = 62;
            break;
        case '二尾連(中)':
            $val = 64;
            break;
        case '三尾連(中)':
            $val = 65;
            break;
        case '四尾連(中)':
            $val = 66;
            break;
        case '二尾連(不中)':
            $val = 67;
            break;
        case '三尾連(不中)':
            $val = 68;
            break;
        case '四尾連(不中)':
            $val = 69;
            break;
        case "二全中":
            $val = 41;
            break;
        case "二中特":
            $val = 42;
            break;
        case "特串":
            $val = 43;
            break;
        case "三全中":
            $val = 44;
            break;
        case "三中二":
            $val = 45;
            break;
        default:
            $a1 = ["鼠","牛","虎","兔","龍","蛇","馬","羊","猴","雞","狗","豬"];
            $a2 = ["0尾","1尾","2尾","3尾","4尾","5尾","6尾","7尾","8尾","9尾"];
            $a3 = ["01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","20","41","42","43","44","45","46","47","48","49"];
            if(in_array($pname,$a1)){
                $val = 130101+array_search($pname, $a1);
            }else if(in_array($pname,$a2)){
                $val = 140100+array_search($pname, $a2);
            }else if(in_array($pname,$a3)){
                $val = 10201+array_search($pname, $a3);
                $val = "0".$val;
            }
        break;       
    }
    return $val;
}
function BW_title($pname)
{
    $val = '';
    switch ($pname) {
        case '二肖連(中)':
            $val = "二肖連(中獎)";
            break;
        case '三肖連(中)':
            $val = "三肖連(中獎)";
            break;
        case '四肖連(中)':
            $val = "四肖連(中獎)";
            break;
        case '五肖連(中)':
            $val = "五肖連(中獎)";
            break;
        case '二肖連(不中)':
            $val = "二肖連(不中獎)";
            break;
        case '三肖連(不中)':
            $val = "三肖連(不中獎)";
            break;
        case '四肖連(不中)':
            $val = "四肖連(不中獎)";
            break;
        case '五肖連(不中)':
            $val = "五肖連(不中獎)";
            break;
        case '二尾連(中)':
            $val = "二尾連(中獎)";
            break;
        case '三尾連(中)':
            $val = "三尾連(中獎)";
            break;
        case '四尾連(中)':
            $val = "四尾連(中獎)";
            break;
        case '二尾連(不中)':
            $val = "二尾連(不中獎)";
            break;
        case '三尾連(不中)':
            $val = "三尾連(不中獎)";
            break;
        case '四尾連(不中)':
            $val = "四尾連(不中獎)";
            break;
    }
    return $val;
}
function BW_time()
{
    list($msec, $sec) = explode(' ', microtime());
    return (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
}