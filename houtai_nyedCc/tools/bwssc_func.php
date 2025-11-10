<?php
function BWSSC_gametype($gid)
{
    return 1;
}
function BWSSC_JSON($gid, $zd, $qishu, $fenlei,$pl)
{
    foreach ($zd as $k => $v) {
        if($v["content"]==""){
            $zd[$k]["uPI_ID"] = $v["code"];
            $zd[$k]["uPI_P"] = $pl["p".$v["code"]]=="" ? 1 : $pl["p".$v["code"]];
            $zd[$k]["uPI_M"] = $v["je"];
            $zd[$k]["type"] = 1;
        }else{
            $zd[$k]["ggameid"] = $v["code"];
            $zd[$k]["gip"] = $pl["p".$v["code"]]=="" ? 1 : $pl["p".$v["code"]];
            $zd[$k]["gim"] = $v["je"];
            $zd[$k]["idlist"] = str_replace('-', ',', $v["content"]);
            $zd[$k]["tzcount"] = 1;
            $zd[$k]["itype"] = 2;
            $zd[$k]["sname"] = BWSSC_sname($v["name"]);
            $zd[$k]["type"] = 2;
        } 
    }
    return $zd;
}
function BWSSC_pls($arr){
    $pl=[];
    foreach($arr as $k => $v){
        $pl["p".$v["g_game_id"]] = $v["g_odds"]; 
    }
    return $pl;
}
function BWSSC_qs($gid,$qishu){
    if($gid==103){
        return substr($qishu,0,8).substr($qishu,-2);
    }
    return $qishu;
}
function BWSSC_sname($pname){
    $v="";
    switch ($pname) {
        case '选二任选':
            $v= "任選二";
            break;
        case '选二连组':
            $v= "選二連組";
            break;
        case '选三任选':
            $v= "任選三";
            break;
        case '选三前组':
            $v= "選三前組";
            break;
        case '选四任选':
            $v= "任選四";
            break;
        case '选五任选':
            $v= "任選五";
            break;
    }
    return $v;
}
function BWSSC_getpl($str){
    $arr = json_decode($str,true);
    $arr = $arr["data"];
    $pl =[];
    foreach($arr as $k => $v){
        $pl["p".$v["gid"]] = $v["ov"];
    }
    return $pl;
}
function BWSSC_lowpeilv($pl)
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
function BWSSC_xdurl($con)
{
    $url = '';
    if($con==""){
        $url = "User/Bet/Betsave";
    }else{
        $url = "User/Bet/Betsave2";
    }
    return $url;
}
function BWSSC_grpid($gid,$fenlei,$ming,$pname){
    $id=0;
    switch ($fenlei) {
        case 107:
            if($ming<=9){
                if(is_numeric($pname)){
                    $id=5;
                }else{
                    $id=1;
                }
            }else{
                $id=2;
            }
            break;
        case 101:
            if($ming<=4){
                $id=7;
            }else{
                $id=2;
            }
            break;
        case 103:
            if($ming<=7){
                $id = $ming+2;
            }else if(in_array($pname,["选二任选","选二连组","选三任选","选三前组","选四任选","选五任选"])){
                $id=11;
            }else{
                $id=1;
            }
            break;
    }
    return $id;
}
function  BWSSC_gt($gid){
    $gt="";
    switch ($gid){
        case 107:
            $gt="BJ";
            break;
        case 101:
            $gt="CQ";
            break;
        case 103:
            $gt="GD";
            break;
        case 135:
            $gt="NC";
            break;
        case 171:
        case 191:
            $gt = "FT";
        break;    
    }
    return $gt;
}

function BWSSC_code($gid, $fenlei, $ming, $pname) {
    $id=0;
    if ($gid == 107) {
        $startid = 0;
        if ($ming <= 9) {            
            $arr = ["大", "小", "单", "双", "龙", "虎"];
            if (in_array($pname, $arr)) {
                $karr = ["100","100","200","200","500","500"];
                $key =  array_search($pname, $arr);
                $k = $karr[$key]+($ming+1);  
                $id = "3".$k.'0'.($key%2+1);               
            } else {
                $id = 380000+($ming+1)*100+$pname;
            }
        } else {
            $arr = ["冠亚大", "冠亚小","冠亚单", "冠亚双"];
            if (in_array($pname, $arr)) {
                $idarr = [310001,310002,320001,320002];
                $id = $idarr[array_search($pname, $arr)];
            } else {
                $id = 390000+$pname;
            }
        }
    }
    if ($gid == 171 || $gid == 191) {
        $startid = 0;
        if ($ming <= 9) {            
            $arr = ["大", "小", "单", "双", "龙", "虎"];
            if (in_array($pname, $arr)) {
                $karr = ["100","100","200","200","500","500"];
                $key =  array_search($pname, $arr);
                $k = $karr[$key]+($ming+1);  
                $id = "4".$k.'0'.($key%2+1);               
            } else {
                $id = 480000+($ming+1)*100+$pname;
            }
        } else {
            $arr = ["冠亚大", "冠亚小","冠亚单", "冠亚双"];
            if (in_array($pname, $arr)) {
                $idarr = [410001,410002,420001,420002];
                $id = $idarr[array_search($pname, $arr)];
            } else {
                $id = 490000+$pname;
            }
        }
    }
    if ($gid == 103) {
        $startid = 0;
        if ($ming <= 7) {            
            $arr = ["大", "小", "单", "双","尾大","尾小","合数单","合数双","龙", "虎"];
            $fw = ["东", "南","西","北"];
            $zfb = ["中","发","白"];
            if (in_array($pname, $arr)) {
                $karr = ["100","100","200","200","300","300","400","400","500","500"];
                $key =  array_search($pname, $arr);
                $k = $karr[$key]+($ming+1);  
                $id = "1".$k.'0'.($key%2+1);               
            } else if (in_array($pname, $fw)) {
                $karr = ["700","700","700","700"];
                $key =  array_search($pname, $fw);
                $k = $karr[$key]+($ming+1);  
                $id = "1".$k.'0'.($key+1);               
            }else if (in_array($pname, $zfb)) {
                $karr = ["600","600","600"];
                $key =  array_search($pname, $zfb);
                $k = $karr[$key]+($ming+1);  
                $id = "1".$k.'0'.($key+1);               
            } else {
                $id = 180000+($ming+1)*100+$pname;
            }
        }else if($ming==8){
            $arr = ["总和大", "总和小","总和单", "总和双","总和尾大", "总和尾小"];
            if (in_array($pname, $arr)) {
                $idarr = [110001,110002,120001,120002,130001,130002];
                $id = $idarr[array_search($pname, $arr)];
            }else{
               $arr = ["选二任选","选二连组","选三任选","选三前组","选四任选","选五任选"];
               $idarr = [190100,190300,190400,190600,190700,190800];
               $id = $idarr[array_search($pname, $arr)];
           }
        }
    }
    if ($gid == 135) {
        $startid = 0;
        if ($ming <= 7) {            
            $arr = ["大", "小", "单", "双","尾大","尾小","合数单","合数双","龙", "虎"];
            $fw = ["东", "南","西","北"];
            $zfb = ["中","发","白"];
            if (in_array($pname, $arr)) {
                $karr = ["100","100","200","200","300","300","400","400","500","500"];
                $key =  array_search($pname, $arr);
                $k = $karr[$key]+($ming+1);  
                $id = "6".$k.'0'.($key%2+1);               
            } else if (in_array($pname, $fw)) {
                $karr = ["700","700","700","700"];
                $key =  array_search($pname, $fw);
                $k = $karr[$key]+($ming+1);  
                $id = "6".$k.'0'.($key+1);               
            }else if (in_array($pname, $zfb)) {
                $karr = ["600","600","600"];
                $key =  array_search($pname, $zfb);
                $k = $karr[$key]+($ming+1);  
                $id = "6".$k.'0'.($key+1);               
            } else {
                $id = 680000+($ming+1)*100+$pname;
            }
        }else if($ming==8){
            $arr = ["总和大", "总和小","总和单", "总和双","总和尾大", "总和尾小"];
            if (in_array($pname, $arr)) {
                $idarr = [610001,610002,620001,620002,630001,630002];
                $id = $idarr[array_search($pname, $arr)];
            }else{
                $arr = ["选二任选","选二连组","选三任选","选三前组","选四任选","选五任选"];
                $idarr = [690100,690300,690400,690600,690700,690800];
                $id = $idarr[array_search($pname, $arr)];
            }
        }
    }

    if ($gid == 101) {
        $startid = 0;
        if ($ming <= 4) {            
            $arr = ["大", "小", "单", "双"];
            if (in_array($pname, $arr)) {
                $karr = ["100","100","200","200"];
                $key =  array_search($pname, $arr);
                $k = $karr[$key]+($ming+1);  
                $id = "2".$k.'0'.($key%2+1);               
            } else {
                $id = 280000+($ming+1)*100+$pname;
            }
        }else if($ming==18){
            $arr = ["总和大", "总和小","总和单", "总和双","龙","虎","和"];
            $idarr = [210001,210002,220001,220002,250001,250002,250003];
            $id = $idarr[array_search($pname, $arr)];
        } else if(in_array($ming, [15,16,17])){
            $arr = ["豹子", "顺子", "对子", "半顺","杂六"];
            $id = "290".($key+1).'0'.($ming-14);      
        }
    }
    return $id;
}
function BWSSC_time()
{
    list($msec, $sec) = explode(' ', microtime());
    return (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
}