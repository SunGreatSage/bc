<?php
function exews($gid, $fenlei, $arr, $qishu)
{
    global $fsql, $tsql, $psql, $tb_class, $tb_play;
    $tz = [];
    $type = [];
    $lib = [];
    foreach ($arr as $k => $v) {
        $type[] = getwstype($gid, $v['mtype'], $v['name']);
        $code = getwscode($gid, $fenlei, $v['mtype'], $v['name']);
        $tz[$k]['BetNo'] = $code;
        $tz[$k]['Odds'] = 1;
        $tz[$k]['BetMoney'] = $v['je'];
        $lib[$k]['gid'] = $gid;
        $lib[$k]['bid'] = $bid;
        $lib[$k]['cid'] = $sid;
        $lib[$k]['sid'] = $cid;
        $lib[$k]['pid'] = $pid;
        $lib[$k]['BetNo'] = $code;
        $lib[$k]['je'] = $v['je'];
    }
    $tmp = array_column($tz, 'BetNo');
    array_multisort($tmp, SORT_ASC, $tz);
    $send['BetItems'] = $tz;
    $send['LotteryId'] = getwsgame($gid);
    $send['PeriodId'] = $qishu;
    $send['type'] = array_unique($type);
    $send['lib'] = $lib;
    return $send;
}

function getwsgame($gid)
{
    switch ($gid) {
        case 101:
            $LotteryId = 1;
            break;
        case 107:
            $LotteryId = 2;
            break;
        case 103:
            $LotteryId = 4;
            break;
        case 135:
            $LotteryId = 3;
            break;
        case 171:
            $LotteryId = 8;
            break;

    }
    return $LotteryId;
}
function getwscode($gid, $fenlei, $mtype, $name) {
    $gids = $gid;
    if ($gids == 103) {
        $gid = 135;
    }
    if ($gids == 171) {
        $gid = 107;
    }
    $code = 0;
    switch ($gid) {
        case 135:
            if ($mtype < 8) {
                $start = 300000 + ($mtype + 1) * 1000;
                if (is_numeric($name)) {
                    $code = 100 + $name;
                } else if ($name == '大') {
                    $code = 201;
                } else if ($name == '小') {
                    $code = 202;
                } else if ($name == '单') {
                    $code = 301;
                } else if ($name == '双') {
                    $code = 302;
                } else if ($name == '合单') {
                    $code = 701;
                } else if ($name == '合双') {
                    $code = 702;
                } else if ($name == '尾大') {
                    $code = 601;
                } else if ($name == '尾小') {
                    $code = 602;
                } else if ($name == '东') {
                    $code = 801;
                } else if ($name == '南') {
                    $code = 802;
                } else if ($name == '西') {
                    $code = 803;
                } else if ($name == '北') {
                    $code = 804;
                } else if ($name == '中') {
                    $code = 901;
                } else if ($name == '发') {
                    $code = 902;
                } else if ($name == '白') {
                    $code = 903;
                }
            } else if ($mtype == 8) {
                $start = 320100;
                $code = $name + 0;
            } else {
                $start = 300000;
                if ($name == '总大') {
                    $code = 201;
                } else if ($name == '总小') {
                    $code = 202;
                } else if ($name == '总单') {
                    $code = 301;
                } else if ($name == '总双') {
                    $code = 302;
                } else if ($name == '总尾大') {
                    $code = 601;
                } else if ($name == '总尾小') {
                    $code = 602;
                }
            }
            $code = $start + $code;
            break;
        case 107:
            if ($mtype < 10) {
                $start = 200000 + ($mtype + 1) * 1000;
                if (is_numeric($name)) {
                    $code = 100 + $name;
                } else if ($name == '大') {
                    $code = 201;
                } else if ($name == '小') {
                    $code = 202;
                } else if ($name == '单') {
                    $code = 301;
                } else if ($name == '双') {
                    $code = 302;
                } else if ($name == '龙') {
                    $code = 401;
                } else if ($name == '虎') {
                    $code = 402;
                }
            } else {
                $start = 200000;
                if (is_numeric($name)) {
                    $code = 100 + $name;
                } else if ($name == '和大') {
                    $code = 201;
                } else if ($name == '和小') {
                    $code = 202;
                } else if ($name == '和单') {
                    $code = 301;
                } else if ($name == '和双') {
                    $code = 302;
                }
            }
            $code = $start + $code;
            break;
        case 101:
            if ($mtype < 5) {
                $start = 100000 + ($mtype + 1) * 1000;
                if (is_numeric($name)) {
                    $code = 100 + $name;
                } else if ($name == '大') {
                    $code = 201;
                } else if ($name == '小') {
                    $code = 202;
                } else if ($name == '单') {
                    $code = 301;
                } else if ($name == '双') {
                    $code = 302;
                }
            } else if ($mtype == 15) {
                $start = 112500;
                if ($name == '豹子') {
                    $code = 1;
                } else if ($name == '顺子') {
                    $code = 2;
                } else if ($name == '对子') {
                    $code = 3;
                } else if ($name == '半顺') {
                    $code = 4;
                } else if ($name == '杂六') {
                    $code = 5;
                }
            } else if ($mtype == 16) {
                $start = 113500;
                if ($name == '豹子') {
                    $code = 1;
                } else if ($name == '顺子') {
                    $code = 2;
                } else if ($name == '对子') {
                    $code = 3;
                } else if ($name == '半顺') {
                    $code = 4;
                } else if ($name == '杂六') {
                    $code = 5;
                }
            } else if ($mtype == 17) {
                $start = 114500;
                if ($name == '豹子') {
                    $code = 1;
                } else if ($name == '顺子') {
                    $code = 2;
                } else if ($name == '对子') {
                    $code = 3;
                } else if ($name == '半顺') {
                    $code = 4;
                } else if ($name == '杂六') {
                    $code = 5;
                }
            } else {
                $start = 0;
                if ($name == '总大') {
                    $code = 100201;
                } else if ($name == '总小') {
                    $code = 100202;
                } else if ($name == '总单') {
                    $code = 100301;
                } else if ($name == '总双') {
                    $code = 100302;
                } else if ($name == '龙') {
                    $code = 101401;
                } else if ($name == '虎') {
                    $code = 101402;
                } else if ($name == '和') {
                    $code = 101403;
                }
            }
            $code = $start + $code;
            break;
    }
    if ($gids == 103) {
        $code+= 100000;
    }
    if ($gids == 171) {
        $code+= 600000;
    }
    return $code;
}
function getwstype($gid, $mtype, $name)
{
    $gids = $gid;
    if ($gids == 103) {
        $gid = 135;
    }
    if ($gids == 171) {
        $gid = 107;
    }
    switch ($gid) {
        case 101:
            if (is_numeric($name)) {
                $code = 1000 + ($mtype + 1) * 10 + 1;
            } else {
                if ($mtype == 18) {
                    switch ($name) {
                        case '总大':
                        case '总小':
                            $code = 1002;
                            break;
                        case '总单':
                        case '总双':
                            $code = 1003;
                            break;
                        default:
                            $code = 1014;
                            break;
                    }
                } else {
                    if ($mtype == 15) {
                        $code = 1125;
                    } else {
                        if ($mtype == 16) {
                            $code = 1135;
                        } else {
                            if ($mtype == 17) {
                                $code = 1145;
                            } else {
                                switch ($name) {
                                    case '大':
                                    case '小':
                                        $code = 1000 + ($mtype + 1) * 10 + 2;
                                        break;
                                    case '单':
                                    case '双':
                                        $code = 1000 + ($mtype + 1) * 10 + 3;
                                        break;
                                    case '龙':
                                    case '虎':
                                    case '和':
                                        $code = 1000 + ($mtype + 1) * 10 + 4;
                                        break;
                                }
                            }
                        }
                    }
                }
            }
            break;
        case 107:
            if (is_numeric($name)) {
                if ($mtype < 10) {
                    $code = 2000 + ($mtype + 1) * 10 + 1;
                } else {
                    $code = 2001;
                }
            } else {
                switch ($name) {
                    case '大':
                    case '小':
                        $code = 2000 + ($mtype + 1) * 10 + 2;
                        break;
                    case '单':
                    case '双':
                        $code = 2000 + ($mtype + 1) * 10 + 3;
                        break;
                    case '龙':
                    case '虎':
                        $code = 2000 + ($mtype + 1) * 10 + 4;
                        break;
                    case '和大':
                    case '和小':
                        $code = 2002;
                        break;
                    case '和单':
                    case '和双':
                        $code = 2003;
                        break;
                }
            }
            break;
        case 135:
            if (is_numeric($name)) {
                if ($mtype == 8) {
                    $code = 3201;
                } else {
                    $code = 3000 + ($mtype + 1) * 10 + 1;
                }
            } else {
                if ($mtype == 8) {
                    switch ($name) {
                        case '总大':
                        case '总小':
                            $code = 3002;
                            break;
                        case '总单':
                        case '总双':
                            $code = 3003;
                            break;
                        case '总尾大':
                        case '总尾小':
                            $code = 3006;
                            break;
                    }
                } else {
                    if ($mtype == 9) {
                        $code = 3014;
                    } else {
                        switch ($name) {
                            case '大':
                            case '小':
                                $code = 3000 + ($mtype + 1) * 10 + 2;
                                break;
                            case '单':
                            case '双':
                                $code = 3000 + ($mtype + 1) * 10 + 3;
                                break;
                            case '合单':
                            case '合双':
                                $code = 3000 + ($mtype + 1) * 10 + 7;
                                break;
                            case '尾大':
                            case '尾小':
                                $code = 3000 + ($mtype + 1) * 10 + 6;
                                break;
                            case '东':
                            case '南':
                            case '西':
                            case '北':
                                $code = 3000 + ($mtype + 1) * 10 + 8;
                                break;
                            case '中':
                            case '发':
                            case '白':
                                $code = 3000 + ($mtype + 1) * 10 + 9;
                                break;
                        }
                    }
                }
            }
            break;
    }
    if ($gids == 103) {
        $code += 1000;
    }
    if ($gids == 171) {
        $code += 6000;
    }
    return $code;
}