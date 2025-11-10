<?php
function IDC_JSONa($gid,$qishu,$fly,$pl){
    global $lib;
    $arr["hasToken"] = "true";
    $arr["wagetype"] = 0;
    $arr["playgametype"] = 0;
    $arr["wagerround"] = $fly["abcd"];
    $arr["allowcreditquota"] = 1000;
    $valstring=[];
    $transtring= [];
    foreach($lib as $k => $v){
        $idcclass = idc_class($gid,$v['mtype'],$v['name']);
        $idcnumber = idc_number($gid,$v['mtype'],$v['name']);
        $t = [];
        $t[0] = $idcclass;
        $t[1] = $idcnumber;
        $t[2] = $v['je'];
        $valstring[] = implode(':', $t);

        $t=[];
        $t[0] = $idcclass;
        $t[1] = "";
        $t[2] = $idcnumber;
        $t[3] = "";
        $t[4] = idc_pl($pl,$idcclass,$idcnumber);
        $t[5] = $v['je'];
        $lib[$k]['pl'] = $t[4];
        $transtring[] = implode(',', $t);
    }
    $arr["arrstring"] = implode(';', $valstring).';';
    $arr["transtring"] = implode(';', $transtring).';';
    return $arr;
}
function IDC_JSONb($gid,$lib,$qishu,$fly){
    $arr["stype"] = "checkxiadan";
    $arr["wagerroundno"] = $fly["abcd"];
    $arr["roundno"] = idc_qishu($gid,$qishu);
    $arr["gameno"] = idc_gameno($gid);
    $valstring=[];
    foreach($lib as $k => $v){
        $t = [];
        $t[0] = idc_class($gid,$v['mtype'],$v['name']);
        $t[1] = idc_number($gid,$v['mtype'],$v['name']);
        $t[2] = $v['je'];
        $valstring[] = implode(':', $t);
    }
    $arr["wagers"] = implode(';', $valstring).';';
    return $arr;
}

function IDC_JSONc($gid,$lib,$qishu,$fly,$token){
    $arr["gameno"] = idc_gameno($gid);
    $arr["wagerroundstring"] = $fly["abcd"];
    $arr["roundno"] = idc_qishu($gid,$qishu);
    $arr["lianma_transtrin"] = "";
    $arr["token"] = $token;
    $valstring=[];
    foreach($lib as $k => $v){
        $t = [];
        $t[0] = idc_class($gid,$v['mtype'],$v['name']);
        $t[1] = idc_number($gid,$v['mtype'],$v['name']);
        $t[2] = $v['je'];
        $valstring[] = implode(':', $t);
    }
    $arr["arrstring"] = implode(';', $valstring).';';
    return $arr;

}
function idc_pl($pl,$idc_class,$idc_number){
    $arr = $pl["og".$idc_class];
    //print_r($arr);
    $peilv=1;
    foreach ($arr["Rows"] as $k => $v) {
        if($v["objectid"]==$idc_number){
            $peilv = $v["odds"];
            break;
        }
    }
    return $peilv;
}
function idc_qishu($gid,$qishu){
     switch ($gid) {
         case 103:
             $qishu = substr($qishu,0,8).'-'.substr($qishu,-2);
             break;
         
     }
     return $qishu;
}
function idc_gameno($gid){
    switch ($gid) {
        case 107:
            $gameno = 11;
            break;
        case 101:
            $gameno = 6;
            break;
        case 172:
            $gameno = 22;
            break;
        case 191:
            $gameno = 21;
            break;
        case 171:
            $gameno = "XYFT";
            break;
        case 170:
            $gameno = "LUCKYSB";
            break;
        case 177:
            $gameno = "SGFT";
            break;
        case 108:
            $gameno = 23;
            break;
        case 175:
            $gameno = 40;
        break;
        case 109:
            $gameno = 38;
        break;
        case 103:
            $gameno = 8;
        break;    
        case 135:
            $gameno = 20;
        break;
        case 131:
            $gameno = "AULUCKY8";
         break;   
        case 161:
            $gameno = 13;
         break;  
        case 162:
            $gameno = "KL8JSC";
         break; 
    }
    return $gameno;
}

function idc_classarr($gid,$lib){
    $arr=[];
    foreach($lib as $k => $v){
        $arr[] = idc_class($gid,$v['mtype'],$v['name']);
    }
    $arr= array_unique($arr);
    return implode(';', $arr).";";
}

function idc_class($gid,$ming,$pname){
    $idcclass=0;
    switch ($gid) {
        case 107:
        case 172:
        case 175:
        case 171:
        case 191:
            switch ($pname) {
                case "大":
                case "小":
                    $idcclass = 621+$ming;
                break;
                case "单":
                case "双":
                    $idcclass = 611+$ming;
                break;
                case "龙":
                case "虎":
                    $idcclass = 631+$ming;
                break;
                case "冠亚大":
                case "冠亚小":
                    $idcclass = 637;
                break;
                case "冠亚单":
                case "冠亚双":
                    $idcclass = 636;
                break;
                default:
                    if($ming<=9){
                        $idcclass = 601+$ming;
                    }else{
                        $idcclass = 638;
                    }
                break;
            }
        break;

        case 101:
        case 108:
        case 109:
            switch ($pname) {
                case "大":
                case "小":
                    $idcclass = 210+$ming;
                break;
                case "单":
                case "双":
                    $idcclass = 215+$ming;
                break;
                case "龙":
                case "虎":
                case "和":
                    $idcclass = 347;
                break;
                case "总和大":
                case "总和小":
                    $idcclass = 346;
                break;
                case "总和单":
                case "总和双":
                    $idcclass = 345;
                break;
                default:
                    $idcclass = 205+$ming;
                break;
            }
        break;

        case 103:
        case 135:
            switch ($pname) {
                case "大":
                case "小":
                    $idcclass = 417+$ming;
                break;
                case "单":
                case "双":
                    $idcclass = 409+$ming;
                break;
                case "尾大":
                case "尾小":
                    $idcclass = 433+$ming;
                break;
                case "合数单":
                case "合数双":
                    $idcclass = 425+$ming;
                break;
                case "龙":
                case "虎":
                    $idcclass = 460;
                break;
                case "总和大":
                case "总和小":
                    $idcclass = 458;
                break;
                case "总和单":
                case "总和双":
                    $idcclass = 457;
                break;
                case "总和尾大":
                case "总和尾小":
                    $idcclass = 459;
                break;
                case "东":
                case "南":
                case "西":
                case "北":
                    $idcclass = 441+$ming;
                break;  
                case "中":
                case "发":
                case "白":
                    $idcclass = 449+$ming;
                break;    
                default:
                    $idcclass = 401+$ming;
                break;
            }
        break;

    }
    return $idcclass;
}

function idc_number($gid,$ming,$pname){
    $arr=[];
    switch ($gid) {
        case 107:
        case 172:
        case 175:
        case 171:
        case 191:
            switch ($pname) {
                case "大":
                case "小":
                    $arr = ["大","小"];
                break;
                case "单":
                case "双":
                    $arr = ["单","双"];
                break;
                case "龙":
                case "虎":
                    $arr = ["龙","虎"];
                break;
                case "冠亚大":
                case "冠亚小":
                    $arr = ["冠亚大","冠亚小"];
                break;
                case "冠亚单":
                case "冠亚双":
                    $arr = ["冠亚单","冠亚双"];
                break;
                default:
                    if($ming<=9){
                        $arr = [1,2,3,4,5,6,7,8,9,10];
                    }else{
                        $arr = [3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19];
                    }
                break;
            }
        break;

        case 101:
        case 108:
        case 109:
            switch ($pname) {
                case "大":
                case "小":
                    $arr = ["大","小"];
                break;
                case "单":
                case "双":
                    $arr = ["单","双"];
                break;
                case "龙":
                case "虎":
                case "和":
                    $arr = ["龙","虎","和"];
                break;
                case "总和大":
                case "总和小":
                    $arr = ["总和大","总和小"];
                break;
                case "总和单":
                case "总和双":
                    $arr = ["总和单","总和双"];
                break;
                default:
                    $arr = [0,1,2,3,4,5,6,7,8,9];
                break;
            }
        break;

        case 103:
        case 135:
            switch ($pname) {
                case "大":
                case "小":
                    $arr = ["大","小"];
                break;
                case "单":
                case "双":
                    $arr = ["单","双"];
                break;
                case "尾大":
                case "尾小":
                    $arr = ["尾大","尾小"];
                break;
                case "合数单":
                case "合数双":
                    $arr = ["合数单","合数双"];
                break;
                case "龙":
                case "虎":
                    $arr = ["龙","虎"];
                break;
                case "总和大":
                case "总和小":
                    $arr = ["总和大","总和小"];
                break;
                case "总和单":
                case "总和双":
                    $arr = ["总和单","总和双"];
                break;
                case "总和尾大":
                case "总和尾小":
                    $arr = ["总和尾大","总和尾小"];
                break;
                case "东":
                case "南":
                case "西":
                case "北":
                    $arr = ["东","南","西","北"];
                break;  
                case "中":
                case "发":
                case "白":
                    $arr = ["中","发","白"];
                break;    
                default:
                    $arr = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];
                break;
            }
        break;

    }
    $number =  array_search(trim($pname), $arr);
    return $number+1;
}
