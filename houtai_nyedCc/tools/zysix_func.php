<?php
function zysix_autologin($fly,$cookie_jar){
	$url = $fly['url1'];
    $send = ["headip"=>$fly["ip"],"cookietype"=>true,"cookie_jar"=>$cookie_jar,"url"=>$url,"posttype"=>false,"location"=>true];
    $res = CURL($send);
    if(strpos($res["res"],'action="vip/login.php"')!==false){
         $url = $fly['url1'] . '/vip/login.php';
         $post = ['account' => $fly['username'], 'passwd' => $fly['passwd'], 'loginBtn'=>""];
         $send = ["headip"=>$fly["ip"],"cookietype"=>true,"cookie_jar"=>$cookie_jar,"url"=>$url,"posttype"=>true,"postdata"=>$post,"location"=>true];
         $res = CURL($send);
    }else{
         $url = $fly['url1'];
         $post = ['user' => $fly['username'], 'pass' => $fly['passwd'], 'x' => "49","y"=>"17"];
         $send = ["headip"=>$fly["ip"],"cookietype"=>true,"cookie_jar"=>$cookie_jar,"url"=>$url,"posttype"=>true,"postdata"=>$post,"location"=>true];
         $res = CURL($send);
    }
    return true;

    
}

function ZYSIX_pl($con){
	$con = explode('-', $con);
	$tmp=[];
	foreach($con as $k =>$v){
        $tmp[] = 1;
	}
	return $tmp;
}
function ZYSIX_pl2($play,$str){
	$str = explode('@@', $str);
	$str = explode(';', $str[0]);
    print_r($str);
	$pl = [];
	foreach($str as $k => $v){
		$tmp = explode(',', $v);
        $pl["p".$tmp[0]] = $tmp[2];
	}
    $pls=[];
	foreach($play as $k =>$v){
        $pls[] = $pl["p".$v] == ""  ? 1 : $pl["p".$v];
	}
	return $pls;
}

function ZYSIX_pl3($con,$str,$dftype){
    $str = explode('@@', $str);
    $str = explode(';', $str[0]);
    $pl = [];
    foreach($str as $k => $v){
        $tmp = explode(',', $v);
        $pl[] = $tmp[2];
    }
    //print_r($pl);
    $pls=[];
    $con = explode(',', $con);
    foreach($con as $k =>$v){
        $pls[] = $pl[$v-1] == ""  ? 1 : $pl[$v-1];
    }
    return implode(',',$pls);
}

function ZYSIX_con($con,$dftype){
	if($dftype==16 || $dftype==17){
		return $con;
	}
	$con = explode(',', $con);
    switch ($dftype) {
        case 18:
            $arr = ["鼠","牛","虎","兔","龍","蛇","馬","羊","猴","雞","狗","豬"];
            
            foreach($con as $k => $v){
               $con[$k] = array_search($v, $arr)+1;
            }
            break;
        case 19:
            $arr = ["1尾","2尾","3尾","4尾","5尾","6尾","7尾","8尾","9尾","0尾"];
            foreach($con as $k => $v){
               $con[$k] = array_search($v, $arr)+1;
            }
            break;
    }
    sort($con);
    return implode(',',$con);
}

function ZYSIX_JSON($gid, $lib,$qishu,$fenlei){
	foreach ($lib as $k => $v) {
		$lib[$k]["gtype"] = ZYSIX_gtype($v["dftype"]);
		$lib[$k]["gclass"] = ZYSIX_gclass($v["dftype"],$v["name"]);
		$lib[$k]["gplay"] = ZYSIX_gplay($v["dftype"],$v["name"]);
	}
	return $lib;
}

function ZYSIX_gtype($dftype){
    $type = '';
    switch ($dftype) {
        case 18:
            $type = "twLm";
            break;
        case 19:
            $type = "tailLm";
            break;
        case 16:
            $type = "lm";
            break;
        case 17:
            $type= "not";
        break;
        case 11:
           $type = "twOth";
        break;
        case 12:
           $type="numOth";
        break;    
    }
    return $type;
}

function ZYSIX_gclass($dftype,$pname){
    $v = '';
    switch ($dftype) {
        case 18:
            $v=21;
            break;
        case 19:
           $v=22;
            break;
        case 16:
            switch ($pname) {
            	case '二全中':
            	case '二中特':
            	case '特串';
            		 $v =  4;
            		break;
            	case '三全中':
            	case '三中二':
            	    $v=5; 
            	break;	
            }
            break;
        case 17:
            switch ($pname) {
            	case '五不中':
            	    $v=20;
            	break;    
            	case '六不中':
            	    $v=24;
            	break; 
            	case '七不中':
            	    $v=23;
            	break; 
            	case '八不中':
            	    $v=25;
            	break; 
            	case '九不中':
            	    $v=26;
            	break; 
            	case '十不中':
            	    $v=27;
            	break; 
            }
            break;
        case 11:
            $v=19;
        break;
        case 12:
            $v=16;
        break;  
    }
    return $v;
}
function ZYSIX_gplay2($dftype){
    $v = '';
    switch ($dftype) {
        case 11:
            $v=1;
        break;
        case 12:
            $v=1;
        break;  
    }
    return $v;
}
function ZYSIX_gcode($dftype,$pname){
    $id = '';
    switch ($dftype) {
        case 11:
            $arr = ["鼠","牛","虎","兔","龍","蛇","馬","羊","猴","雞","狗","豬"];
            $id = 22645+array_search($pname, $arr);
        break;
        case 12:
            $arr = ["0尾","1尾","2尾","3尾","4尾","5尾","6尾","7尾","8尾","9尾"];
            $id = 22623+array_search($pname, $arr);
        break;  
    }
    return $id;
}
function ZYSIX_gplay($dftype,$pname){
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
            $val = 100;
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
            $val = 101;
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
            	case '五不中':
            	    $val=1;
            	break;    
            	case '六不中':
            	   $val=1;
            	break; 
            	case '七不中':
            	    $val=1;
            	break; 
            	case '八不中':
            	    $val=1;
            	break; 
            	case '九不中':
            	    $val=1;
            	break; 
            	case '十不中':
            	    $val=1;
            	break; 

    }
    return $val;
}