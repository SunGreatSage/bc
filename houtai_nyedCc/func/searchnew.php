<?php
include_once 'jsfunc.php';
function searchqishu($gid, $psize, $page)
{
    global $psql, $tb_game, $tb_play;
    $psql->query("update `{$tb_play}` set zqishu=0,buzqishu=0 where gid='{$gid}'");
    $psql->query("select fenlei,mnum,thisqishu,cs from `{$tb_game}` where gid='{$gid}'");
    $psql->next_record();
    return call_user_func("searchqishu_" . $psql->f('fenlei'), $gid, $psize, $page, $psql->f('fenlei'), $psql->f("mnum"), $psql->f("thisqishu"), json_decode($msql->f("cs"), true));
}
function searchqishu_100($gid, $psize, $page, $fenlei, $mnum, $thisqishu, $cs)
{

}
function searchqishu_101($gid, $psize, $page, $fenlei, $mnum, $thisqishu, $cs)
{
    global $tsql, $fsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs = $psql->arr("select * from `{$tb_play}` where gid='{$gid}' order by xsort", 1);
    $cr = count($rs);
    $tmp = [];
    $ftm = [];
    if ($cs['ft'] == 1) {
        $ftm = explode(',', $cs['ftnum']);
    }
    for ($i = 0; $i < $ck; $i++) {
        $ft = 0;
        if ($cs['ft'] == 1) {
            foreach ($ftm as $k => $v) {
                $ft += $kj[$i]['m'][$v - 1];
            }
            $ft = $ft % 4 == 0 ? 4 : $ft % 4;
        }
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1) {
                continue;
            }
            $tmp['b' . $rs[$j]['bid']] == "" && ($tmp['b' . $rs[$j]['bid']] = transb8('name', $rs[$j]['bid'], $gid));
            $tmp['s' . $rs[$j]['sid']] == "" && ($tmp['s' . $rs[$j]['sid']] = transs8('name', $rs[$j]['sid'], $gid));
            if ($tmp['c' . $rs[$j]['cid']] == "") {
                $psql->query("select mtype,name from `{$tb_class}` where gid='{$gid}' and cid='" . $rs[$j]['cid'] . "'");
                $psql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
                $tmp['c' . $rs[$j]['cid']] = $name;
                $tmp['mtype' . $rs[$j]['cid']] = $mtype;
            }
            switch ($tmp['b' . $rs[$j]['bid']]) {
                case "番摊":
                    if ($cs['ft'] != 1) {
                        continue;
                    }
                    switch ($tmp["c" . $rs[$k]['cid']]) {
                        case '番':
                            $tmps = $ft;
                            break;
                        case '双面':
                            switch ($rs[$j]['name']) {
                                case '单':
                                case '双':
                                    $tmps = danshuang($ft);
                                    break;
                                case '大':
                                case '小':
                                    $tmps = $ft > 2 ? "大" : "小";
                                    break;
                            }
                            break;
                        default:
                            continue;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case '1~5':
                    $m = $kj[$i]['m' . ($tmp['mtype' . $rs[$j]['cid']] + 1)];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case '单双':
                            $tmps = danshuang($m);
                            break;
                        case '大小':
                            $tmps = daxiao($m);
                            break;
                        default:
                            $tmps = $m;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "1字组合":
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case '全五1字组合':
                            $arr = [$kj[$i]['m1'], $kj[$i]['m2'], $kj[$i]['m3'], $kj[$i]['m4'], $kj[$i]['m5']];
                            break;
                        case '前三1字组合':
                            $arr = [$kj[$i]['m1'], $kj[$i]['m2'], $kj[$i]['m3']];
                            break;
                        case '中三1字组合':
                            $arr = [$kj[$i]['m2'], $kj[$i]['m3'], $kj[$i]['m4']];
                            break;
                        case '后三1字组合':
                            $arr = [$kj[$i]['m3'], $kj[$i]['m4'], $kj[$i]['m5']];
                            break;
                    }
                    if (!in_array($rs[$j]['name'], $arr)) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "2字和数":
                    $he = 0;
                    switch ($tmp['s' . $rs[$j]['sid']]) {
                        case "万千和数":
                            $he = $kj[$i]['m1'] + $kj[$i]['m2'];
                            break;
                        case "万百和数":
                            $he = $kj[$i]['m1'] + $kj[$i]['m3'];
                            break;
                        case "万十和数":
                            $he = $kj[$i]['m1'] + $kj[$i]['m4'];
                            break;
                        case "万个和数":
                            $he = $kj[$i]['m1'] + $kj[$i]['m5'];
                            break;
                        case "千百和数":
                            $he = $kj[$i]['m2'] + $kj[$i]['m3'];
                            break;
                        case "千十和数":
                            $he = $kj[$i]['m2'] + $kj[$i]['m4'];
                            break;
                        case "千个和数":
                            $he = $kj[$i]['m2'] + $kj[$i]['m5'];
                            break;
                        case "百十和数":
                            $he = $kj[$i]['m3'] + $kj[$i]['m4'];
                            break;
                        case "百个和数":
                            $he = $kj[$i]['m3'] + $kj[$i]['m5'];
                            break;
                        case "十个和数":
                            $he = $kj[$i]['m4'] + $kj[$i]['m5'];
                            break;
                    }
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case "单双":
                            $tmps = danshuang($he);
                            break;
                        case "和尾大小":
                            $tmps = "和尾" . daxiaow($he % 10);
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case '3字和数':
                    $he = 0;
                    switch ($tmp['s' . $rs[$j]['sid']]) {
                        case "前三和数":
                            $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
                            break;
                        case "中三和数":
                            $he = $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'];
                            break;
                        case "后三和数":
                            $he = $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                            break;
                    }
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case "和大小":
                            $tmps = $he >= 14 ? "和大" : "和小";
                            break;
                        case "和单双":
                            $tmps = "和" . danshuang($he);
                            break;
                        case "和尾大小":
                            $tmps = "和尾" . daxiaow($he % 10);
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "总和龙虎":
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case "总和大小":
                            $tmps = $he > 22 ? "总和大" : "总和小";
                            break;
                        case "总和单双":
                            $tmps = "总和" . danshuang($he);
                            break;
                        case "总尾大小":
                            $tmps = "总和尾" . daxiaow($he % 10);
                            break;
                        case "龙虎和":
                            $tmps = longhuhe($kj[$i]['m1'], $kj[$i]['m5']);
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "跨度":
                    switch ($tmp["s" . $rs[$j]['sid']]) {
                        case '前三':
                            $k1 = abs($kj[$i]['m1'] - $kj[$i]['m2']);
                            $k2 = abs($kj[$i]['m1'] - $kj[$i]['m3']);
                            $k3 = abs($kj[$i]['m2'] - $kj[$i]['m3']);
                            break;
                        case '中三':
                            $k1 = abs($kj[$i]['m2'] - $kj[$i]['m3']);
                            $k2 = abs($kj[$i]['m2'] - $kj[$i]['m4']);
                            $k3 = abs($kj[$i]['m3'] - $kj[$i]['m4']);
                            break;
                        case '后三':
                            $k1 = abs($kj[$i]['m3'] - $kj[$i]['m4']);
                            $k2 = abs($kj[$i]['m3'] - $kj[$i]['m5']);
                            $k3 = abs($kj[$i]['m4'] - $kj[$i]['m5']);
                            break;
                    }
                    $k = max($k1, $k2, $k3);
                    if ($rs[$j]['name'] != $k) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "前中后三":
                    switch ($tmp["s" . $rs[$j]['sid']]) {
                        case '前三':
                            $k1 = $kj[$i]['m1'];
                            $k2 = $kj[$i]['m2'];
                            $k3 = $kj[$i]['m3'];
                            break;
                        case '中三':
                            $k1 = $kj[$i]['m2'];
                            $k2 = $kj[$i]['m3'];
                            $k3 = $kj[$i]['m4'];
                            break;
                        case '后三':
                            $k1 = $kj[$i]['m3'];
                            $k2 = $kj[$i]['m4'];
                            $k3 = $kj[$i]['m5'];
                            break;
                    }
                    $tmps = qita($k1, $k2, $k3);
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
            }
        }
    }
}
function searchqishu_163($gid, $psize, $page, $fenlei, $mnum, $thisqishu, $cs)
{
    global $tsql, $fsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs = $psql->arr("select * from `{$tb_play}` where gid='{$gid}' order by xsort", 1);
    $cr = count($rs);
    $tmp = [];
    $ftm = [];
    if ($cs['ft'] == 1) {
        $ftm = explode(',', $cs['ftnum']);
    }
    for ($i = 0; $i < $ck; $i++) {
        $ft = 0;
        if ($cs['ft'] == 1) {
            foreach ($ftm as $k => $v) {
                $ft += $kj[$i]['m'][$v - 1];
            }
            $ft = $ft % 4 == 0 ? 4 : $ft % 4;
        }
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1) {
                continue;
            }
            $tmp['b' . $rs[$j]['bid']] == "" && ($tmp['b' . $rs[$j]['bid']] = transb8('name', $rs[$j]['bid'], $gid));
            $tmp['s' . $rs[$j]['sid']] == "" && ($tmp['s' . $rs[$j]['sid']] = transs8('name', $rs[$j]['sid'], $gid));
            if ($tmp['c' . $rs[$j]['cid']] == "") {
                $psql->query("select mtype,name from `{$tb_class}` where gid='{$gid}' and cid='" . $rs[$j]['cid'] . "'");
                $psql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
                $tmp['c' . $rs[$j]['cid']] = $name;
                $tmp['mtype' . $rs[$j]['cid']] = $mtype;
            }
            switch ($tmp['b' . $rs[$j]['bid']]) {
                case "番摊":
                    if ($cs['ft'] != 1) {
                        continue;
                    }
                    switch ($tmp["c" . $rs[$k]['cid']]) {
                        case '番':
                            $tmps = $ft;
                            break;
                        case '双面':
                            switch ($rs[$j]['name']) {
                                case '单':
                                case '双':
                                    $tmps = danshuang($ft);
                                    break;
                                case '大':
                                case '小':
                                    $tmps = $ft > 2 ? "大" : "小";
                                    break;
                            }
                            break;
                        default:
                            continue;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case '1~3':
                    $m = $kj[$i]['m' . ($tmp['mtype' . $rs[$j]['cid']] + 1)];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case '单双':
                            $tmps = danshuang($m);
                            break;
                        case '大小':
                            $tmps = daxiao($m);
                            break;
                        default:
                            $tmps = $m;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "1字组合":
                    $arr = [$kj[$i]['m1'], $kj[$i]['m2'], $kj[$i]['m3']];
                    if (!in_array($rs[$j]['name'], $arr)) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "2字和数":
                    $he = 0;
                    switch ($tmp['s' . $rs[$j]['sid']]) {
                        case "百十和数":
                            $he = $kj[$i]['m3'] + $kj[$i]['m4'];
                            break;
                        case "百个和数":
                            $he = $kj[$i]['m3'] + $kj[$i]['m5'];
                            break;
                        case "十个和数":
                            $he = $kj[$i]['m4'] + $kj[$i]['m5'];
                            break;
                    }
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case "单双":
                            $tmps = danshuang($he);
                            break;
                        case "和尾大小":
                            $tmps = "和尾" . daxiaow($he % 10);
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case '总和龙虎':
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case "极值大小":
                            $tmps = $he >= 22 ? "极大" : "极小";
                            break;
                        case "总和大小":
                            if ($rs[$j]["name"] == "总和大") {
                                $tmps = $he > 14 ? "总和大" : "总和小";
                                $he == 14 && ($tmps = "和");
                            } else {
                                $tmps = $he < 13 ? "总和小" : "总和大";
                                $he == 13 && ($tmps = "和");
                            }
                            break;
                        case "总和单双":
                            $tmps = "总和" . danshuang($he);
                            break;
                        case "总和尾大小":
                            $tmps = "总和尾" . daxiaow($he % 10);
                            break;
                        case "总和过关":
                            $tmps = danshuang($ma);
                            $tmps == "单" && $ma > 13 && ($tmps = "总大单");
                            $tmps == "单" && $ma == 13 && ($tmps = "和");
                            $tmps == "单" && $ma < 13 && ($tmps = "总小单");
                            $tmps == "双" && $ma > 14 && ($tmps = "总大双");
                            $tmps == "双" && $ma == 14 && ($tmps = "和");
                            $tmps == "双" && $ma < 14 && ($tmps = "总小双");
                            break;
                        case "龙虎和":
                            $tmps = longhuhe($kj[$i]['m1'], $kj[$i]['m3']);
                            break;
                        default:
                            $tmps = $he;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "跨度":
                    $k1 = abs($kj[$i]['m1'] - $kj[$i]['m2']);
                    $k2 = abs($kj[$i]['m1'] - $kj[$i]['m3']);
                    $k3 = abs($kj[$i]['m2'] - $kj[$i]['m3']);
                    $k = max($k1, $k2, $k3);
                    if ($rs[$j]['name'] != $k) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "前三":
                    $k1 = $kj[$i]['m1'];
                    $k2 = $kj[$i]['m2'];
                    $k3 = $kj[$i]['m3'];
                    $tmps = qita($k1, $k2, $k3);
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
            }
        }
    }
}
function searchqishu_107($gid, $psize, $page, $fenlei, $mnum, $thisqishu, $cs)
{
    global $tsql, $fsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs = $psql->arr("select * from `{$tb_play}` where gid='{$gid}' order by xsort", 1);
    $cr = count($rs);
    $tmp = [];
    $ftm = [];
    if ($cs['ft'] == 1) {
        $ftm = explode(',', $cs['ftnum']);
    }
    for ($i = 0; $i < $ck; $i++) {
        $ft = 0;
        if ($cs['ft'] == 1) {
            foreach ($ftm as $k => $v) {
                $ft += $kj[$i]['m'][$v - 1];
            }
            $ft = $ft % 4 == 0 ? 4 : $ft % 4;
        }
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1) {
                continue;
            }
            $tmp['b' . $rs[$j]['bid']] == "" && ($tmp['b' . $rs[$j]['bid']] = transb8('name', $rs[$j]['bid'], $gid));
            $tmp['s' . $rs[$j]['sid']] == "" && ($tmp['s' . $rs[$j]['sid']] = transs8('name', $rs[$j]['sid'], $gid));
            if ($tmp['c' . $rs[$j]['cid']] == "") {
                $psql->query("select mtype,name from `{$tb_class}` where gid='{$gid}' and cid='" . $rs[$j]['cid'] . "'");
                $psql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
                $tmp['c' . $rs[$j]['cid']] = $name;
                $tmp['mtype' . $rs[$j]['cid']] = $mtype;
            }
            switch ($tmp['b' . $rs[$j]['bid']]) {
                case "番摊":
                    if ($cs['ft'] != 1) {
                        continue;
                    }
                    switch ($tmp["c" . $rs[$k]['cid']]) {
                        case '番':
                            $tmps = $ft;
                            break;
                        case '双面':
                            switch ($rs[$j]['name']) {
                                case '单':
                                case '双':
                                    $tmps = danshuang($ft);
                                    break;
                                case '大':
                                case '小':
                                    $tmps = $ft > 2 ? "大" : "小";
                                    break;
                            }
                            break;
                        default:
                            continue;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case '冠亚军组合':
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case "和大小":
                            $tmps = $he > 11 ? "冠亚大" : "冠亚小";
                            break;
                        case "和单双":
                            $tmps = "冠亚" . danshuang($he);
                            break;
                        default:
                            $tmps = $he;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                default:
                    $m = $kj[$i]['m' . ($tmp['mtype' . $rs[$j]['cid']] + 1)];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case '单双':
                            $tmps = danshuang($m);
                            break;
                        case '大小':
                            $tmps = daxiao($m);
                            break;
                        case '龙虎':
                            $tmps = longhuhe($m, $kj[$i]['m' . (10 - $tmp['mtype' . $rs[$j]['cid']])]);
                            break;
                        default:
                            $tmps = $m;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
            }
        }
    }
}
function searchqishu_103($gid, $psize, $page, $fenlei, $mnum, $thisqishu, $cs)
{
    global $tsql, $fsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs = $psql->arr("select * from `{$tb_play}` where gid='{$gid}' order by xsort", 1);
    $cr = count($rs);
    $tmp = [];
    $ftm = [];
    if ($cs['ft'] == 1) {
        $ftm = explode(',', $cs['ftnum']);
    }
    for ($i = 0; $i < $ck; $i++) {
        $ft = 0;
        if ($cs['ft'] == 1) {
            foreach ($ftm as $k => $v) {
                $ft += $kj[$i]['m'][$v - 1];
            }
            $ft = $ft % 4 == 0 ? 4 : $ft % 4;
        }
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1) {
                continue;
            }
            $tmp['b' . $rs[$j]['bid']] == "" && ($tmp['b' . $rs[$j]['bid']] = transb8('name', $rs[$j]['bid'], $gid));
            $tmp['s' . $rs[$j]['sid']] == "" && ($tmp['s' . $rs[$j]['sid']] = transs8('name', $rs[$j]['sid'], $gid));
            if ($tmp['c' . $rs[$j]['cid']] == "") {
                $psql->query("select mtype,name from `{$tb_class}` where gid='{$gid}' and cid='" . $rs[$j]['cid'] . "'");
                $psql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
                $tmp['c' . $rs[$j]['cid']] = $name;
                $tmp['mtype' . $rs[$j]['cid']] = $mtype;
            }
            switch ($tmp['b' . $rs[$j]['bid']]) {
                case "番摊":
                    if ($cs['ft'] != 1) {
                        continue;
                    }
                    switch ($tmp["c" . $rs[$k]['cid']]) {
                        case '番':
                            $tmps = $ft;
                            break;
                        case '双面':
                            switch ($rs[$j]['name']) {
                                case '单':
                                case '双':
                                    $tmps = danshuang($ft);
                                    break;
                                case '大':
                                case '小':
                                    $tmps = $ft > 2 ? "大" : "小";
                                    break;
                            }
                            break;
                        default:
                            continue;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case '总和龙虎':
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'] + $kj[$i]['m6'] + $kj[$i]['m7'] + $kj[$i]['m8'];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case "总和大小":
                            if ($he > 84) {
                                $tmps = "总和大";
                            } else {
                                if ($he < 84) {
                                    $tmps = "总和小";
                                } else {
                                    $tmps = "和";
                                }
                            }
                            break;
                        case "总和单双":
                            $tmps = "总和" . danshuang($he);
                            break;
                        case "总尾大小":
                            $tmps = "总和尾" . daxiaow($he % 10);
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "正码":
                    $arr = [$kj[$i]['m1'], $kj[$i]['m2'], $kj[$i]['m3'], $kj[$i]['m4'], $kj[$i]['m5'], $kj[$i]['m6'], $kj[$i]['m7'], $kj[$i]['m8']];
                    if (!in_array($rs[$j]['name'], $arr)) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "连码":
                    continue;
                    break;
                default:
                    $m = $kj[$i]['m' . ($tmp['mtype' . $rs[$j]['cid']] + 1)];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case '单双':
                            $tmps = danshuang($m);
                            break;
                        case '大小':
                            $tmps = daxiao($m);
                            break;
                        case '龙虎':
                            $tmps = longhuhe($m, $kj[$i]['m' . (8 - $tmp['mtype' . $rs[$j]['cid']])]);
                            break;
                        case "尾大小":
                            $tmps = "尾" . daxiaow($m % 10);
                            break;
                        case "合单双":
                            $tmps = "合数" . danshuang(heshu($m));
                            break;
                        case "方位":
                            $tmps = fangwei($m);
                            break;
                        case "中发白":
                            $tmps = zhongfabai($m);
                            break;
                        default:
                            $tmps = $m;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
            }
        }
    }
}
function searchqishu_121($gid, $psize, $page, $fenlei, $mnum, $thisqishu, $cs)
{
    global $tsql, $fsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs = $psql->arr("select * from `{$tb_play}` where gid='{$gid}' order by xsort", 1);
    $cr = count($rs);
    $tmp = [];
    $ftm = [];
    if ($cs['ft'] == 1) {
        $ftm = explode(',', $cs['ftnum']);
    }
    for ($i = 0; $i < $ck; $i++) {
        $ft = 0;
        if ($cs['ft'] == 1) {
            foreach ($ftm as $k => $v) {
                $ft += $kj[$i]['m'][$v - 1];
            }
            $ft = $ft % 4 == 0 ? 4 : $ft % 4;
        }
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1) {
                continue;
            }
            $tmp['b' . $rs[$j]['bid']] == "" && ($tmp['b' . $rs[$j]['bid']] = transb8('name', $rs[$j]['bid'], $gid));
            $tmp['s' . $rs[$j]['sid']] == "" && ($tmp['s' . $rs[$j]['sid']] = transs8('name', $rs[$j]['sid'], $gid));
            if ($tmp['c' . $rs[$j]['cid']] == "") {
                $psql->query("select mtype,name from `{$tb_class}` where gid='{$gid}' and cid='" . $rs[$j]['cid'] . "'");
                $psql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
                $tmp['c' . $rs[$j]['cid']] = $name;
                $tmp['mtype' . $rs[$j]['cid']] = $mtype;
            }
            switch ($tmp['b' . $rs[$j]['bid']]) {
                case "番摊":
                    if ($cs['ft'] != 1) {
                        continue;
                    }
                    switch ($tmp["c" . $rs[$k]['cid']]) {
                        case '番':
                            $tmps = $ft;
                            break;
                        case '双面':
                            switch ($rs[$j]['name']) {
                                case '单':
                                case '双':
                                    $tmps = danshuang($ft);
                                    break;
                                case '大':
                                case '小':
                                    $tmps = $ft > 2 ? "大" : "小";
                                    break;
                            }
                            break;
                        default:
                            continue;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case '总和龙虎':
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case "总和大小":
                            if ($he > 84) {
                                $tmps = "总和大";
                            } else {
                                if ($he < 84) {
                                    $tmps = "总和小";
                                } else {
                                    $tmps = "和";
                                }
                            }
                            break;
                        case "总和单双":
                            $tmps = "总和" . danshuang($he);
                            break;
                        case "总尾大小":
                            $tmps = "总和尾" . daxiaow($he % 10);
                            break;
                        case "龙虎":
                            $tmps = longhuhe($kj[$i]['m1'], $kj[$i]['m5']);
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "正码":
                    $arr = [$kj[$i]['m1'], $kj[$i]['m2'], $kj[$i]['m3'], $kj[$i]['m4'], $kj[$i]['m5']];
                    if (!in_array($rs[$j]['name'], $arr)) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "连码":
                    continue;
                    break;
                default:
                    $m = $kj[$i]['m' . ($tmp['mtype' . $rs[$j]['cid']] + 1)];
                    switch ($tmp['c' . $rs[$j]['cid']]) {
                        case '单双':
                            $tmps = danshuang($m);
                            break;
                        case '大小':
                            $tmps = daxiao($m);
                            break;
                        default:
                            $tmps = $m;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
            }
        }
    }
}
function searchqishu_161($gid, $psize, $page, $fenlei, $mnum, $thisqishu, $cs)
{
    global $tsql, $fsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs = $psql->arr("select * from `{$tb_play}` where gid='{$gid}' order by xsort", 1);
    $cr = count($rs);
    $tmp = [];
    $ftm = [];
    if ($cs['ft'] == 1) {
        $ftm = explode(',', $cs['ftnum']);
    }
    for ($i = 0; $i < $ck; $i++) {
        $ft = 0;
        if ($cs['ft'] == 1) {
            foreach ($ftm as $k => $v) {
                $ft += $kj[$i]['m'][$v - 1];
            }
            $ft = $ft % 4 == 0 ? 4 : $ft % 4;
        }
        $m = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'] + $kj[$i]['m6'] + $kj[$i]['m7'] + $kj[$i]['m8'] + $kj[$i]['m9'] + $kj[$i]['m10'] + $kj[$i]['m11'] + $kj[$i]['m12'] + $kj[$i]['m13'] + $kj[$i]['m14'] + $kj[$i]['m15'] + $kj[$i]['m16'] + $kj[$i]['m17'] + $kj[$i]['m18'] + $kj[$i]['m19'] + $kj[$i]['m20'];
        $zd = 0;
        $zq = 0;
        for ($h = 1; $h <= 20; $h++) {
            if (danshuang($kj[$i]['m' . $h]) == '单') {
                $zd++;
            }
            if ($kj[$i]['m' . $h] <= 40) {
                $zq++;
            }
        }
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1) {
                continue;
            }
            $tmp['b' . $rs[$j]['bid']] == "" && ($tmp['b' . $rs[$j]['bid']] = transb8('name', $rs[$j]['bid'], $gid));
            $tmp['s' . $rs[$j]['sid']] == "" && ($tmp['s' . $rs[$j]['sid']] = transs8('name', $rs[$j]['sid'], $gid));
            if ($tmp['c' . $rs[$j]['cid']] == "") {
                $psql->query("select mtype,name from `{$tb_class}` where gid='{$gid}' and cid='" . $rs[$j]['cid'] . "'");
                $psql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
                $tmp['c' . $rs[$j]['cid']] = $name;
                $tmp['mtype' . $rs[$j]['cid']] = $mtype;
            }
            switch ($tmp['b' . $rs[$j]['bid']]) {
                case "番摊":
                    if ($cs['ft'] != 1) {
                        continue;
                    }
                    switch ($tmp["c" . $rs[$k]['cid']]) {
                        case '番':
                            $tmps = $ft;
                            break;
                        case '双面':
                            switch ($rs[$j]['name']) {
                                case '单':
                                case '双':
                                    $tmps = danshuang($ft);
                                    break;
                                case '大':
                                case '小':
                                    $tmps = $ft > 2 ? "大" : "小";
                                    break;
                            }
                            break;
                        default:
                            continue;
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "正码":
                    if (!in_array($rs[$j]['name'], $kj[$i])) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "总和":
                    $tmps = "";
                    switch ($tmp["c" . $rs[$j]["cid"]]) {
                        case '总和单双':
                            $tmps = "总和" . danshuang($m);
                            break;
                        case '总和大小':
                            $tmps = $m > 810 ? "总和大" : "总和小";
                            $m == 810 && ($tmps = "和");
                            break;
                        case '总和810':
                            $m == 810 && ($tmps = "总和810");
                            break;
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "总和过关":
                    $tmps = danshuang($m);
                    if ($m < 810) {
                        $tmps = "总小" . $tmp;
                    } else {
                        if ($tmp > 810) {
                            $tmps = "总大" . $tmp;
                        }
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "前后和":
                    if ($zq > 10) {
                        $tmps = "前(多)";
                    } else {
                        if ($zq < 10) {
                            $tmps = "后(多)";
                        } else {
                            if ($zq == 10) {
                                $tmps = "前后(和)";
                            }
                        }
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "单双和":
                    if ($zd > 10) {
                        $tmps = "单(多)";
                    } else {
                        if ($zd < 10) {
                            $tmps = "双(多)";
                        } else {
                            if ($zd == 10) {
                                $tmps = "单双(和)";
                            }
                        }
                    }
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "五行":
                    $tmps = wuhang_161($m);
                    if ($tmp != $rs[$j]['name']) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
            }
        }
    }
}
function searchqishu_151($gid, $psize, $page, $fenlei, $mnum, $thisqishu, $cs)
{
    global $tsql, $fsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs = $psql->arr("select * from `{$tb_play}` where gid='{$gid}' order by xsort", 1);
    $cr = count($rs);
    $tmp = [];
    for ($i = 0; $i < $ck; $i++) {
        $ma = [$kj[$i]['m1'], $kj[$i]['m2'], $kj[$i]['m3']];
        $m = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1) {
                continue;
            }
            $tmp['b' . $rs[$j]['bid']] == "" && ($tmp['b' . $rs[$j]['bid']] = transb8('name', $rs[$j]['bid'], $gid));
            $tmp['s' . $rs[$j]['sid']] == "" && ($tmp['s' . $rs[$j]['sid']] = transs8('name', $rs[$j]['sid'], $gid));
            if ($tmp['c' . $rs[$j]['cid']] == "") {
                $psql->query("select mtype,name from `{$tb_class}` where gid='{$gid}' and cid='" . $rs[$j]['cid'] . "'");
                $psql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
                $tmp['c' . $rs[$j]['cid']] = $name;
                $tmp['mtype' . $rs[$j]['cid']] = $mtype;
            }
            switch ($tmp['b' . $rs[$j]['bid']]) {
                case "三军":
                    if ($tmp["c" . $rs[$j]['cid']] == "三军") {
                        if (!in_array($rs[$j]['name'], $ma)) {
                            $kj[$i]['m'][$j] = 2;
                            if ($i > 0) {
                                if ($kj[$i - 1]['m'][$j] == 2) {
                                    $kj[0]['z'][$j] += 1;
                                    if ($kj[0]['z'][$j] >= $ck) {
                                        updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                    }
                                } else {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                    $kj[0]['u'][$j] = 1;
                                }
                            } else {
                                $kj[0]['z'][$j] += 1;
                            }
                        } else {
                            $kj[$i]['m'][$j] = 1;
                            if ($i > 0) {
                                if ($kj[$i - 1]['m'][$j] == 1) {
                                    $kj[0]['z'][$j] += 1;
                                    if ($kj[0]['z'][$j] >= $ck) {
                                        updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                    }
                                } else {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                    $kj[0]['u'][$j] = 1;
                                }
                            } else {
                                $kj[0]['z'][$j] += 1;
                            }
                        }
                    } else {
                        $tmp = $m < 11 ? "三军大" : "三军小";
                        if ($tmp != $rs[$j]['name']) {
                            $kj[$i]['m'][$j] = 2;
                            if ($i > 0) {
                                if ($kj[$i - 1]['m'][$j] == 2) {
                                    $kj[0]['z'][$j] += 1;
                                    if ($kj[0]['z'][$j] >= $ck) {
                                        updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                    }
                                } else {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                    $kj[0]['u'][$j] = 1;
                                }
                            } else {
                                $kj[0]['z'][$j] += 1;
                            }
                        } else {
                            $kj[$i]['m'][$j] = 1;
                            if ($i > 0) {
                                if ($kj[$i - 1]['m'][$j] == 1) {
                                    $kj[0]['z'][$j] += 1;
                                    if ($kj[0]['z'][$j] >= $ck) {
                                        updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                    }
                                } else {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                    $kj[0]['u'][$j] = 1;
                                }
                            } else {
                                $kj[0]['z'][$j] += 1;
                            }
                        }
                    }
                    break;
                case "围全骰":
                    if ($tmp["c" . $rs[$j]["cid"]] == "围骰") {
                        if (!(baozhi($kj[$i][0], $kj[$i][1], $kj[$i][2]) == 1 & $ma[0] == $rs[$j]['name'] % 10)) {
                            $kj[$i]['m'][$j] = 2;
                            if ($i > 0) {
                                if ($kj[$i - 1]['m'][$j] == 2) {
                                    $kj[0]['z'][$j] += 1;
                                    if ($kj[0]['z'][$j] >= $ck) {
                                        updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                    }
                                } else {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                    $kj[0]['u'][$j] = 1;
                                }
                            } else {
                                $kj[0]['z'][$j] += 1;
                            }
                        } else {
                            $kj[$i]['m'][$j] = 1;
                            if ($i > 0) {
                                if ($kj[$i - 1]['m'][$j] == 1) {
                                    $kj[0]['z'][$j] += 1;
                                    if ($kj[0]['z'][$j] >= $ck) {
                                        updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                    }
                                } else {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                    $kj[0]['u'][$j] = 1;
                                }
                            } else {
                                $kj[0]['z'][$j] += 1;
                            }
                        }
                    } else {
                        if (baozhi($kj[$i][0], $kj[$i][1], $kj[$i][2]) != 1) {
                            $kj[$i]['m'][$j] = 2;
                            if ($i > 0) {
                                if ($kj[$i - 1]['m'][$j] == 2) {
                                    $kj[0]['z'][$j] += 1;
                                    if ($kj[0]['z'][$j] >= $ck) {
                                        updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                    }
                                } else {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                    $kj[0]['u'][$j] = 1;
                                }
                            } else {
                                $kj[0]['z'][$j] += 1;
                            }
                        } else {
                            $kj[$i]['m'][$j] = 1;
                            if ($i > 0) {
                                if ($kj[$i - 1]['m'][$j] == 1) {
                                    $kj[0]['z'][$j] += 1;
                                    if ($kj[0]['z'][$j] >= $ck) {
                                        updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                    }
                                } else {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                    $kj[0]['u'][$j] = 1;
                                }
                            } else {
                                $kj[0]['z'][$j] += 1;
                            }
                        }
                    }
                    break;
                case "点数":
                    if ($rs[$j]['name'] != $m."点") {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "长牌":
                    $one = $rs[$j]['name'] % 10;
                    $two = ($rs[$j]['name'] - $one) / 10;
                    if (!(in_array($one, $ma) & in_array($two, $ma))) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
                case "短牌":
                    $one = $rs[$j]['name'] % 10;
                    $csk = array_count_values($ma);
                    if ($csk[$one] < 2) {
                        $kj[$i]['m'][$j] = 2;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 2) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    } else {
                        $kj[$i]['m'][$j] = 1;
                        if ($i > 0) {
                            if ($kj[$i - 1]['m'][$j] == 1) {
                                $kj[0]['z'][$j] += 1;
                                if ($kj[0]['z'][$j] >= $ck) {
                                    updateqishu($gid, $rs[$j]['pid'], 1, $kj[0]['z'][$j]);
                                }
                            } else {
                                updateqishu($gid, $rs[$j]['pid'], 0, $kj[0]['z'][$j]);
                                $kj[0]['u'][$j] = 1;
                            }
                        } else {
                            $kj[0]['z'][$j] += 1;
                        }
                    }
                    break;
            }
        }
    }
}