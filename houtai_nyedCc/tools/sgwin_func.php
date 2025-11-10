<?php

function SGWIN_JSON($gid, $zd,$qishu,$fenlei)
{
    $arr = [];
    $arr['lottery'] = SGWIN_getgametype($gid);
    $arr['ignore'] = true;
    $arr['drawNumber'] = $qishu;
    $send = [];
    foreach ($zd as $k => $v) {
        $send[$k]['amount'] = $v['je'];
        $send[$k]['odds'] = 1;
        $send[$k]['title'] = "";
        $send[$k]['game'] = SGWIN_getctype($gid, $v['mtype'], $v['name'],$fenlei);
        $send[$k]['contents'] = SGWIN_getptype($gid, $v['mtype'], $v['name'],$fenlei);
        $send[$k]['content'] = $v['content'];
        $send[$k]['pid'] = $v['pid'];
    }
    $arr['bets'] = $send;
    return $arr;
}
function SGWIN_getgametype($gid)
{
    $type = "";
    switch ($gid) {
        case 107:
            $type = "BJPK10";
            break;
        case 101:
            $type = "CQHLSX";
            break;
        case 172:
            $type = "PK10JSC";
            break;
        case 171:
            $type = "XYFT";
            break;
        case 170:
            $type = "LUCKYSB";
            break;
        case 177:
            $type = "SGFT";
            break;
        case 108:
            $type = "SSCJSC";
            break;
        case 175:
            $type = "AULUCKY10";
        break;
        case 109:
            $type = "AULUCKY5";
        break;
        case 103:
            $type ="GDKLSF";
        break;    
        case 135:
            $type = "CQXYNC";
        break;
        case 131:
            $type = "AULUCKY8";
         break;   
        case 161:
            $type = "BJKL8";
         break;  
        case 162:
            $type = "KL8JSC";
         break; 
    }
    return $type;
}
function SGWIN_getctype($gid, $ming, $pname,$fenlei)
{
    $ctype = '';
    if ($fenlei==107 && $ming == 10) {
        switch ($pname) {
            case '冠亚单':
            case '冠亚双':
                $ctype = 'GDS';
                break;
            case '冠亚大':
            case '冠亚小':
                $ctype = 'GDX';
                break;
            default:
                $ctype = 'GYH';
                break;
        }
    } else {
        if ($fenlei==101 && $ming == 18) {
            switch ($pname) {
                case '总和单':
                case '总和双':
                    $ctype = 'ZDS';
                    break;
                case '总和大':
                case '总和小':
                    $ctype = 'ZDX';
                    break;
                case '龙':
                case '虎':
                case '和':
                    $ctype = 'LH';
                    break;
            }
        }else if ($fenlei==103 && $ming == 8) {
            switch ($pname) {
                case '总和单':
                case '总和双':
                    $ctype = 'ZDS';
                    break;
                case '总和大':
                case '总和小':
                    $ctype = 'ZDX';
                    break;
                case '总和尾大':
                case '总和尾小':
                    $ctype = 'ZWDX';
                    break;
                default:
                    $ctype = "ZM";
                break;    
            }
        } else {
            if ($fenlei==101 &&  in_array($ming, [15, 16, 17])) {
                $ctype = 'TS' . ($ming - 14);
            } else {
                switch ($pname) {
                    case '单':
                    case '双':
                        $ctype = 'DS' . ($ming+1);
                        break;
                    case '大':
                    case '小':
                        $ctype = 'DX' . ($ming+1);
                        break;
                    case '合单':
                    case '合双':
                    case '合数单':
                    case '合数双':
                        $ctype = 'HDS' . ($ming+1);
                        break;
                    case '尾大':
                    case '尾小':
                        $ctype = 'WDX' . ($ming+1);
                        break;
                    case '龙':
                    case '虎':
                        $ctype = 'LH' . ($ming+1);
                        break;
                    case '东':
                    case '南':
                    case '西':
                    case '北':
                        $ctype = 'FW' . ($ming+1);
                        break;
                    case '中':
                    case '发':
                    case '白':
                        $ctype = 'ZFB' . ($ming+1);
                        break;
                    default:
                        $ctype = 'B' . ($ming+1);
                        break;
                }
            }
        }
    }
    return $ctype;
}
function SGWIN_getptype($gid, $ming, $pname,$fenlei)
{
    $ptype = '';
    if ($fenlei==107 && $ming == 10) {
        switch ($pname) {
            case '冠亚单':
                $ptype = 'D';
                break;
            case '冠亚双':
                $ptype = 'S';
                break;
            case '冠亚大':
                $ptype = 'D';
                break;
            case '冠亚小':
                $ptype = 'X';
                break;
            default:
                $ptype = $pname;
                break;
        }
    } else {
        if ($fenlei==101 && $ming == 18) {
            switch ($pname) {
                case '总和单':
                    $ptype = 'D';
                    break;
                case '总和双':
                    $ptype = 'S';
                    break;
                case '总和大':
                    $ptype = 'D';
                    break;
                case '总和小':
                    $ptype = 'X';
                    break;
                case '龙':
                    $ptype = 'L';
                    break;
                case '虎':
                    $ptype = 'H';
                    break;
                case '和':
                    $ptype = 'T';
                    break;
            }
        } else if ($fenlei==103 && $ming == 8) {
            switch ($pname) {
                case '总和单':
                    $ptype = 'D';
                    break;
                case '总和双':
                    $ptype = 'S';
                    break;
                case '总和大':
                    $ptype = 'D';
                    break;
                case '总和小':
                    $ptype = 'X';
                    break;
                case '总和尾大':
                    $ptype = 'D';
                    break;
                case '总和尾小':
                    $ptype = 'X';
                    break;
                default:
                    $ptype = $pname;
                break;    
            }
        } else {
            if ($fenlei==101 && in_array($ming, [15, 16, 17])) {
                switch ($pname) {
                    case '豹子':
                        $ptype = '0';
                        break;
                    case '顺子':
                        $ptype = '1';
                        break;
                    case '对子':
                        $ptype = '2';
                        break;
                    case '半顺':
                        $ptype = '3';
                        break;
                    case '杂六':
                        $ptype = '4';
                        break;
                }
            } else {
                switch ($pname) {
                    case '单':
                        $ptype = 'D';
                        break;
                    case '双':
                        $ptype = 'S';
                        break;
                    case '大':
                        $ptype = 'D';
                        break;
                    case '小':
                        $ptype = 'X';
                        break;
                    case '合单':
                    case '合数单':
                        $ptype = 'D';
                        break;
                    case '合双':
                    case '合数双':
                        $ptype = 'S';
                        break;
                    case '尾大':
                        $ptype = 'D';
                        break;
                    case '尾小':
                        $ptype = 'X';
                    case '龙':
                        $ptype = 'L';
                        break;
                    case '虎':
                        $ptype = 'H';
                        break;
                    case '东':
                        $ptype = '0';
                        break;
                    case '南':
                        $ptype = '1';
                        break;
                    case '西':
                        $ptype = '2';
                        break;
                    case '北':
                        $ptype = '3';
                        break;
                    case '中':
                        $ptype = '0';
                        break;
                    case '发':
                        $ptype = '1';
                        break;
                    case '白':
                        $ptype = '2';
                        break;
                    default:
                        $ptype = $pname;
                        break;
                }
            }
        }
    }
    return $ptype;
}