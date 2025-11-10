<?php

function dl_autologin($fly,$cookie_jar){
    $url = $fly['url1'] . '/login/check';
    $post = ['account' => $fly['username'], 'password' => $fly['passwd'], 'cID' => $fly['searchcode'],"userLang"=>"cn"];
    $send = ["headip"=>$fly["ip"],"cookietype"=>true,"cookie_jar"=>$cookie_jar,"url"=>$url,"posttype"=>true,"postdata"=>$post,"location"=>false];
    $res = CURL($send);
    return true;
}

function  DL_getgametype($gid)
{
    $type = "";
    switch ($gid) {
        case 107:
            $type = "pk10";
            break;
        case 101:
            $type = "ssc";
            break;
        case 171:
            $type = "xyft";
            break;
        case 113:
            $type = "xjssc";
            break;
        case 103:
            $type ="gdklsf";
        break;    
        case 135:
            $type = "cqklsf";
         break;   
    }
    return $type;
}

function DL_JSON($gid, $zd,$qishu,$fenlei,$pl){
    $send = [];
    $pids = 0;
    foreach ($zd as $k => $v) {
        $pids= DL_ID($gid, $v['mtype'], $v['name'],$fenlei);
        if($pids==0) continue;
        $send[$k]['betMoney'] = $v['je'];
        $send[$k]['detailID'] = $pids;
        $send[$k]['betRate'] = $pl[$pids];
        $send[$k]['title'] = "";
        $send[$k]['content'] = $v['content'];
        $send[$k]['pid'] = $v['pid'];
    }
    return $send;
}
function DL_ID($gid, $ming, $pname, $fenlei)
{
    $id=0;
    if ($gid == 107) {
        $startid = 0;
        if ($ming <= 9) {            
            $arr = ["大", "小", "单", "双", "龙", "虎"];
            if (in_array($pname, $arr)) {
                if ($ming <= 4) {
                    $id = $startid + 100 + $ming * 6 + array_search($pname, $arr) + 1;
                } else {
                    $id = $startid + 100 + 5 * 6 + ($ming-5)*4 + array_search($pname, $arr) + 1;
                }
            } else {
                $id = $startid + $ming * 10 + $pname;
            }
        } else {
            $arr = ["冠亚单", "冠亚双", "冠亚大", "冠亚小"];
            if (in_array($pname, $arr)) {
                $id = $startid + 100 + 50 + array_search($pname, $arr) + 1;
            } else {
                $id = $startid + 100 + 50 + 4 + ($pname - 2);
            }
        }
    }
    if ($gid == 171) {
        $startid = 171;
        if ($ming <= 9) {            
            $arr = ["大", "小", "单", "双", "龙", "虎"];
            if (in_array($pname, $arr)) {
                if ($ming <= 4) {
                    $id = $startid + 100 + $ming * 6 + array_search($pname, $arr) + 1;
                } else {
                    $id = $startid + 100 + 5 * 6 + ($ming-5)*4 + array_search($pname, $arr) + 1;
                }
            } else {
                $id = $startid + $ming * 10 + $pname;
            }
        } else {
            $arr = ["冠亚单", "冠亚双", "冠亚大", "冠亚小"];
            if (in_array($pname, $arr)) {
                $id = $startid + 100 + 50 + array_search($pname, $arr) + 1;
            } else {
                $id = $startid + 100 + 50 + 4 + ($pname - 2);
            }
        }
    }
    if($gid==113){
        $startid = 434;
        if ($ming <= 4) {            
            $arr = ["大", "小", "单", "双"];
            if (in_array($pname, $arr)) {
                $id = $startid + 50 + $ming*4 + array_search($pname, $arr) + 1;
            } else {
                $id = $startid + $ming * 10 + $pname + 1;
            }
        }else if($ming==18){
            $arr = ["总和大", "总和小","总和单", "总和双"];
            if (in_array($pname, $arr)) {
                $id = $startid + 50 + 20 + array_search($pname, $arr) + 1;
            }else if($pname=="龙"){
                $id = 1891;
            }else if($pname=="虎"){
                $id = 1892;
            }else if($pname=="和"){
                $id = 1893;
            }
        } else if(in_array($ming, [15,16,17])){
            $arr = ["豹子", "顺子", "对子", "半顺","杂六"];
            $id = 511 + ($ming-15)*5 + array_search($pname, $arr) + 1;
        }
    }
    if($gid==101){
        $startid = 342;
        if ($ming <= 4) {            
            $arr = ["大", "小", "单", "双"];
            if (in_array($pname, $arr)) {
                $id = $startid + 50  + $ming*4 + array_search($pname, $arr) + 1;
            } else {
                $id = $startid + $ming * 10 + $pname + 1;
            }
        }else if($ming==18){
            $arr = ["总和大", "总和小","总和单", "总和双"];
            if (in_array($pname, $arr)) {
                $id = $startid + 50 + 20 + array_search($pname, $arr) + 1;
            }else if($pname=="龙"){
                $id = 1848;
            }else if($pname=="虎"){
                $id = 1849;
            }else if($pname=="和"){
                $id = 1850;
            }
        } else if(in_array($ming, [15,16,17])){
            $arr = ["豹子", "顺子", "对子", "半顺","杂六"];
            $id = 419 + ($ming-15)*5 + array_search($pname, $arr) + 1;
        }
    }
    if ($gid == 135) {
        $startid = 1143;
        if ($ming <= 7) {            
            $arr = ["大", "小", "单", "双", "东", "南","西","北","中","发","白"];
            if (in_array($pname, $arr)) {
                $id = $startid + 160 + $ming * 11 + array_search($pname, $arr) + 1;
            }else if($pname=="龙" && $ming==0){
                $id = 1404;
            }else if($pname=="虎" && $ming==0){
                $id = 1405;
            }else if(is_numeric($pname)){
                $id = $startid + $ming * 20 + $pname;
            }
        }else if($ming==8){
            $arr = [ "总和大", "总和小","总和单", "总和双","总和尾大","总和尾小"];
            $id = 1397 + array_search($pname, $arr) + 1;
        }
    }
    if ($gid == 103) {
        $startid = 881;
        if ($ming <= 7) {            
            $arr = ["大", "小", "单", "双", "东", "南","西","北","中","发","白"];
            if (in_array($pname, $arr)) {
                $id = $startid + 160 + $ming * 11 + array_search($pname, $arr) + 1;
            }else if($pname=="龙" && $ming==0){
                $id = 1142;
            }else if($pname=="虎" && $ming==0){
                $id = 1143;
            }else if(is_numeric($pname)){
                $id = $startid + $ming * 20 + $pname;
            }
        }else if($ming==8){
            $arr = [ "总和大", "总和小","总和单", "总和双","总和尾大","总和尾小"];
            $id = 1135 + array_search($pname, $arr) + 1;
        }
    }
    return $id;
}