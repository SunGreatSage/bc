<?php
include_once('jsfunc.php');
function searchqishu($gid, $psize, $page)
{
    global $psql, $tb_game, $tb_play;
    $psql->query("update `$tb_play` set zqishu=0,buzqishu=0 where gid='$gid'");
    $psql->query("select fenlei from `$tb_game` where gid='$gid'");
    $psql->next_record();
    if ($psql->f('fenlei')==163) {
        return call_user_func("searchqishu_101", $gid, $psize, $page, $psql->f('fenlei'));
    } else {
        return call_user_func("searchqishu_" . $psql->f('fenlei'), $gid, $psize, $page, $psql->f('fenlei'));
    }
}
function searchqishu_101($gid, $psize, $page,$fenlei)
{
    global $tsql, $fsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = array();
    $tsql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $tsql->next_record();
    $mnum          = $tsql->f('mnum');
    $thisqishu     = $tsql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs            = $tsql->arr("select * from `$tb_play` where gid='$gid' order by xsort", 1);
    $cr            = count($rs);
	//echo $gid;
    for ($i = 0; $i < $ck; $i++) {
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1)
                continue;
            if ($tmpbid != $rs[$j]['bid'])
                $bname = transb8('name', $rs[$j]['bid'], $gid);
            if ($tmpsid != $rs[$j]['sid'])
                $sname = transs8('name', $rs[$j]['sid'], $gid);
            if ($tmpcid != $rs[$j]['cid']) {
                $tsql->query("select mtype,name from `$tb_class` where gid='$gid' and cid='" . $rs[$j]['cid'] . "'");
                $tsql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
            }
            if ($bname == '1~5') {
                $m = $kj[$i]['m' . ($mtype + 1)];
                if (is_numeric($rs[$j]['name'])) {
                    if ($m != $rs[$j]['name']) {
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
                } else if (strpos('[单双]', $rs[$j]['name'])) {
                    $tmp = danshuang($m);
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
                } else if (strpos('[大小]', $rs[$j]['name'])) {
                    $tmp = daxiao($m);
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
                } else if (strpos('[质合]', $rs[$j]['name'])) {
                    $tmp = zhihe($m);
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
            } else if ($bname == '1字组合') {
                if ($cname == '全五1字组合') {
                    $arr = array(
                        $kj[$i]['m1'],
                        $kj[$i]['m2'],
                        $kj[$i]['m3'],
                        $kj[$i]['m4'],
                        $kj[$i]['m5']
                    );
                } else if ($cname == '前三1字组合' | $fenlei==163 ) {
                    $arr = array(
                        $kj[$i]['m1'],
                        $kj[$i]['m2'],
                        $kj[$i]['m3']
                    );
                } else if ($cname == '中三1字组合') {
                    $arr = array(
                        $kj[$i]['m2'],
                        $kj[$i]['m3'],
                        $kj[$i]['m4']
                    );
                } else if ($cname == '后三1字组合') {
                    $arr = array(
                        $kj[$i]['m3'],
                        $kj[$i]['m4'],
                        $kj[$i]['m5']
                    );
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
            } else if ($bname == '2字和数') {
                $he = 0;
                if ($fenlei==163 ) {
                    if ($sname == '百十和数') {
                        $he = $kj[$i]['m1'] + $kj[$i]['m2'];
                    } else if ($sname == '百个和数') {
                        $he = $kj[$i]['m1'] + $kj[$i]['m3'];
                    } else if ($sname == '十个和数') {
                        $he = $kj[$i]['m2'] + $kj[$i]['m3'];
                    }
                } else {
                    if ($sname == '万千和数') {
                        $he = $kj[$i]['m1'] + $kj[$i]['m2'];
                    } else if ($sname == '万百和数') {
                        $he = $kj[$i]['m1'] + $kj[$i]['m3'];
                    } else if ($sname == '万十和数') {
                        $he = $kj[$i]['m1'] + $kj[$i]['m4'];
                    } else if ($sname == '万个和数') {
                        $he = $kj[$i]['m1'] + $kj[$i]['m5'];
                    } else if ($sname == '千百和数') {
                        $he = $kj[$i]['m2'] + $kj[$i]['m3'];
                    } else if ($sname == '千十和数') {
                        $he = $kj[$i]['m2'] + $kj[$i]['m4'];
                    } else if ($sname == '千个和数') {
                        $he = $kj[$i]['m2'] + $kj[$i]['m5'];
                    } else if ($sname == '百十和数') {
                        $he = $kj[$i]['m3'] + $kj[$i]['m4'];
                    } else if ($sname == '百个和数') {
                        $he = $kj[$i]['m3'] + $kj[$i]['m5'];
                    } else if ($sname == '十个和数') {
                        $he = $kj[$i]['m4'] + $kj[$i]['m5'];
                    }
                }
                if ($cname == '单双') {
                    if ($rs[$j]['name'] != danshuang($he)) {
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
                    if ($rs[$j]['name'] != "和尾" . daxiaow($he % 10)) {
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
            } else if ($bname == '3字和数') {
                if ($sname == '前三和数') {
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
                } else if ($sname == '中三和数') {
                    $he = $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'];
                } else if ($sname == '后三和数') {
                    $he = $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                }
                $wei = $he % 10;
                if (strpos('[和单和双]', $rs[$j]['name'])) {
                    $tmp = danshuang($he);
                    if (!strpos($rs[$j]['name'], $tmp)) {
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
                } else if (strpos('[和大和小]', $rs[$j]['name'])) {
                    if (!(($he >= 14 & $rs[$j]['name'] == '和大') | ($he <= 13 & $rs[$j]['name'] == '和小'))) {
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
                } else if (strpos('[和尾大和尾小]', $rs[$j]['name'])) {
                    $tmp = daxiao($wei);
                    if (!strpos($rs[$j]['name'], $tmp)) {
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
                } else if (strpos('[和尾质和尾合]', $rs[$j]['name'])) {
                    $tmp = zhihe($wei);
                    if (!strpos($rs[$j]['name'], $tmp)) {
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
                } else if ($rs[$j]['cname'] == '尾数') {
                    if ($wei != $rs[$j]['name']) {
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
            } else if ($bname == '牛牛梭哈') {
                $arr = array(
                    $kj[$i]['m1'],
                    $kj[$i]['m2'],
                    $kj[$i]['m3'],
                    $kj[$i]['m4'],
                    $kj[$i]['m5']
                );
                if ($sname == '牛牛') {
                    $nn = niuniu($arr);
                    if ($rs[$j]['name'] == '无牛') {
                        if ($nn[0]) {
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
                    } else if ($rs[$j]['name'] == '牛牛') {
                        if (!$nn[0] | $nn[1]) {
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
                    } else if ($rs[$j]['name'] == '牛单' | $rs[$j]['name'] == '牛双') {
                        if (!$nn[0] | '牛' . danshuang($nn[2]) != $rs[$j]['name']) {
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
                    } else if ($rs[$j]['name'] == '牛大' | $rs[$j]['name'] == '牛小') {
                        if (!$nn[0] | '牛' . nndaxiao($nn[2]) != $rs[$j]['name']) {
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
                    } else if ($rs[$j]['name'] == '牛质' | $rs[$j]['name'] == '牛合') {
                        if (!$nn[0] | '牛' . zhihe($nn[2]) != $rs[$j]['name']) {
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
                        if (!$nn[0] | '牛' . $nn[2] != $rs[$j]['name']) {
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
                    
                } else {
                    $sh = suoha($arr);
                    if ($sh != $rs[$j]['name']) {
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
                
            } else if ($bname == '总和龙虎') {
                if ($fenlei==163 ) {
                    $he    = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
                    $hemid = 13;
                } else {
                    $he    = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                    $hemid = 22;
                }
                $wei = $he % 10;
				//echo $rs[$j]['name'];
                if (strpos('[总和单总和双]', $rs[$j]['name'])) {
                    $tmp = danshuang($he);
                    if (!strpos($rs[$j]['name'], $tmp)) {
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
                } else if (strpos('[总和大总和小]', $rs[$j]['name'])) {
                    
                    if (!(($he >= $hemid + 1 & $rs[$j]['name'] == '总和大') | ($he <= $hemid & $rs[$j]['name'] == '总和小'))) {
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
                } else if (strpos('[总和尾大总和尾小]', $rs[$j]['name'])) {
                    $tmp = daxiao($wei);
                    if (!strpos($rs[$j]['name'], $tmp)) {
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
                } else if (strpos('[总尾质总尾合]', $rs[$j]['name'])) {
                    $tmp = zhihe($wei);
                    if (!strpos($rs[$j]['name'], $tmp)) {
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
                } else if (strpos('[总大单总大双总小单总小双]', $rs[$j]['name'])) {
                    $ds = danshuang($he);
                    if ($he >= 14)
                        $tmp = '大' . $ds;
                    else
                        $tmp = '小' . $ds;
                    if (!strpos($rs[$j]['name'], $tmp)) {
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
                } else if ($cname == '总和尾数') {
                    if ($wei != $rs[$j]['name']) {
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
                } else if ($cname == '总和数' || strpos($cname,'和-') ) {
				
                    if ($fenlei==163 ) {
                        $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
                    } else {
                        $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                    }
                    $ps   = explode('~', $rs[$j]['name']);
                    $cp   = count($ps);
                    $flag = false;
                    if ($cp == 1) {
                        if ($he != $rs[$j]['name']) {
                            $flag = true;
                        }
                    } else {
                        if ($he < $ps[0] | $he > $ps[1]) {
                            $flag = true;
                        }
                    }
                    if ($flag) {
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
                } else if ($cname == '龙虎和') {
                    
                    if ($fenlei==163 ) {
                        $tmp = longhuhe($kj[$i]['m1'], $kj[$i]['m3']);
                    } else {
                        $tmp = longhuhe($kj[$i]['m1'], $kj[$i]['m5']);
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
                }
            } else if ($bname == '跨度') {
                if (strpos("[$sname]", '前三') | $fenlei==163 ) {
                    $k1 = abs($kj[$i]['m1'] - $kj[$i]['m2']);
                    $k2 = abs($kj[$i]['m1'] - $kj[$i]['m3']);
                    $k3 = abs($kj[$i]['m2'] - $kj[$i]['m3']);
                } else if (strpos("[$sname]", '中三')) {
                    $k1 = abs($kj[$i]['m2'] - $kj[$i]['m3']);
                    $k2 = abs($kj[$i]['m2'] - $kj[$i]['m4']);
                    $k3 = abs($kj[$i]['m3'] - $kj[$i]['m4']);
                } else if (strpos("[$sname]", '后三')) {
                    $k1 = abs($kj[$i]['m3'] - $kj[$i]['m4']);
                    $k2 = abs($kj[$i]['m3'] - $kj[$i]['m5']);
                    $k3 = abs($kj[$i]['m4'] - $kj[$i]['m5']);
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
            } else if ($bname == '其他') {
                if (strpos("[$sname]", '前三') | $fenlei==163 ) {
                    $k1 = $kj[$i]['m1'];
                    $k2 = $kj[$i]['m2'];
                    $k3 = $kj[$i]['m3'];
                } else if (strpos("[$sname]", '中三')) {
                    $k1 = $kj[$i]['m2'];
                    $k2 = $kj[$i]['m3'];
                    $k3 = $kj[$i]['m4'];
                } else if (strpos("[$sname]", '后三')) {
                    $k1 = $kj[$i]['m3'];
                    $k2 = $kj[$i]['m4'];
                    $k3 = $kj[$i]['m5'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($cname == '准对') {
                    $num = 0;
                    if ($k1 == $rs[$j]['name'])
                        $num++;
                    if ($k2 == $rs[$j]['name'])
                        $num++;
                    if ($k3 == $rs[$j]['name'])
                        $num++;
                    if ($num != 2) {
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
                } else if ($cname == '不出') {
                    if (!($k1 != $rs[$j]['name'] & $k2 != $rs[$j]['name'] & $k3 != $rs[$j]['name'])) {
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
                } else if ($rs[$j]['name'] == '豹子') {
                    $v = baozhi($k1, $k2, $k3);
                    if ($v != 1) {
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
                } else if ($rs[$j]['name'] == '顺子') {
                    $v = shunzhi($k1, $k2, $k3);
                    if ($v != 1) {
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
                } else if ($rs[$j]['name'] == '对子') {
                    $v = duizhi($k1, $k2, $k3);
                    if ($v != 1) {
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
                } else if ($rs[$j]['name'] == '半顺') {
                    $v = banshun($k1, $k2, $k3);
                    if ($v != 1) {
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
                } else if ($rs[$j]['name'] == '杂六') {
                    if (zaliu($k1, $k2, $k3) != 1) {
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
            }
            $tmpbid = $rs[$j]['bid'];
            $tmpsid = $rs[$j]['sid'];
			$tmpcid = $rs[$j]['cid'];
        }
    }
    unset($p);
    unset($kj);
}
function searchqishu_103($gid, $psize, $page,$fenlei)
{
    global $tsql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = array();
    $tsql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $tsql->next_record();
    $mnum          = $tsql->f('mnum');
    $thisqishu     = $tsql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs            = $tsql->arr("select * from `$tb_play` where gid='$gid' order by bid,sid,cid,xsort", 1);
    $cr            = count($rs);
    for ($i = 0; $i < $ck; $i++) {
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1)
                continue;
            if ($tmpbid != $rs[$j]['bid']) {
                $bname = transb8('name', $rs[$j]['bid'], $gid);
            }
            if ($tmpcid != $rs[$j]['cid']) {
                $tsql->query("select mtype,name from `$tb_class` where gid='$gid' and cid='" . $rs[$j]['cid'] . "'");
                $tsql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
            }
            $m = $kj[$i]['m' . ($mtype + 1)];
            if (strpos($bname, '球') | $bname == '特码号') {
                if (is_numeric($rs[$j]['name'])) {
                    if ($m != $rs[$j]['name']) {
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
                } else if (strpos('[单双]', $rs[$j]['name'])) {
                    $tmp = danshuang($m);
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
                } else if (strpos('[大小]', $rs[$j]['name'])) {
                    if (!(($m >= 11 & $rs[$j]['name'] == '大') | ($m <= 10 & $rs[$j]['name'] == '小'))) {
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
                } else if (strpos('[合单合双]', $rs[$j]['name'])) {
                    $tmp = danshuang(heshu($m));
                    if ("合" . $tmp != $rs[$j]['name']) {
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
                } else if (strpos('[尾大尾小]', $rs[$j]['name'])) {
                    $tmp = daxiao($m % 10);
                    if ("尾" . $tmp != $rs[$j]['name']) {
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
                } else if (strpos('[春夏秋冬]', $rs[$j]['name'])) {
                    $tmp = siji($m);
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
                } else if (strpos('[金木水火土]', $rs[$j]['name'])) {
                    $tmp = wuhang($m);
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
                } else if (strpos('[东南西北]', $rs[$j]['name'])) {
                    $tmp = fangwei($m);
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
                } else if (strpos('[中发白]', $rs[$j]['name'])) {
                    $tmp = zhongfabai($m);
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
            } else if ($bname == '总和龙虎') {
                $m = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'] + $kj[$i]['m6'] + $kj[$i]['m7'] + $kj[$i]['m8'];
                if (strpos('[总单总双]', $rs[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ("总" . $tmp != $rs[$j]['name']) {
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
                } else if (strpos('[总大总小]', $rs[$j]['name'])) {
                    if (!(($m > 84 & $rs[$j]['name'] == '总大') | ($m < 84 & $rs[$j]['name'] == '总小'))) {
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
                } else if (strpos('[总尾大总尾小]', $rs[$j]['name'])) {
                    $tmp = daxiao($m % 10);
                    if ("总尾" . $tmp != $rs[$j]['name']) {
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
                } else if (strpos('[龙虎]', $rs[$j]['name'])) {
                    $tmp = longhuhe($kj[$i]['m1'], $kj[$i]['m8']);
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
            } else if ($bname == '正码') {
                $arr = array(
                    $kj[$i]['m1'],
                    $kj[$i]['m2'],
                    $kj[$i]['m3'],
                    $kj[$i]['m4'],
                    $kj[$i]['m5'],
                    $kj[$i]['m6'],
                    $kj[$i]['m7'],
                    $kj[$i]['m8']
                );
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
            }
            $tmpbid = $rs[$j]['bid'];
            $tmpcid = $rs[$j]['cid'];
        }
    }
    unset($p);
    unset($kj);
}
function searchqishu_121($gid, $psize, $page,$fenlei)
{
    global $tsql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = array();
    $tsql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $tsql->next_record();
    $mnum          = $tsql->f('mnum');
    $thisqishu     = $tsql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs            = $tsql->arr("select * from `$tb_play` where gid='$gid' order by bid,sid,cid,xsort", 1);
    $cr            = count($rs);
    for ($i = 0; $i < $ck; $i++) {
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1)
                continue;
            if ($tmpbid != $rs[$j]['bid'])
                $bname = transb8('name', $rs[$j]['bid'], $gid);
            if ($tmpcid != $rs[$j]['cid']) {
                $tsql->query("select mtype,name from `$tb_class` where gid='$gid' and cid='" . $rs[$j]['cid'] . "'");
                $tsql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
            }
            $m = $kj[$i]['m' . ($mtype + 1)];
            if (strpos($bname, '球')) {
                if (is_numeric($rs[$j]['name'])) {
                    if ($m != $rs[$j]['name']) {
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
                } else if (strpos('[单双]', $rs[$j]['name'])) {
                    $tmp = danshuang($m);
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
                } else if (strpos('[大小]', $rs[$j]['name'])) {
                    if (!(($m <= 10 & $m >= 6 & $rs[$j]['name'] == '大') | ($m <= 5 & $rs[$j]['name'] == '小'))) {
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
            } else if ($bname == '总和龙虎') {
                $m = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                if (strpos('[总单总双]', $rs[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ("总" . $tmp != $rs[$j]['name']) {
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
                } else if (strpos('[总大总小]', $rs[$j]['name'])) {
                    if (!(($m > 30 & $rs[$j]['name'] == '总大') | ($m < 30 & $rs[$j]['name'] == '总小'))) {
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
                } else if (strpos('[总尾大总尾小]', $rs[$j]['name'])) {
                    $tmp = daxiao($m % 10);
                    if ("总尾" . $tmp != $rs[$j]['name']) {
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
                } else if (strpos('[龙虎]', $rs[$j]['name'])) {
                    $tmp = longhuhe($kj[$i]['m1'], $kj[$i]['m5']);
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
            } else if ($bname == '正码') {
                $arr = array(
                    $kj[$i]['m1'],
                    $kj[$i]['m2'],
                    $kj[$i]['m3'],
                    $kj[$i]['m4'],
                    $kj[$i]['m5']
                );
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
            }
            $tmpbid = $rs[$j]['bid'];
            $tmpcid = $rs[$j]['cid'];
        }
    }
}
function searchqishu_107($gid, $psize, $page,$fenlei)
{
    global $tsql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = array();
    $tsql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $tsql->next_record();
    $mnum          = $tsql->f('mnum');
    $thisqishu     = $tsql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs            = $tsql->arr("select * from `$tb_play` where gid='$gid' order by bid,sid,cid,xsort", 1);
    $cr            = count($rs);
    for ($i = 0; $i < $ck; $i++) {
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1)
                continue;
            if ($tmpbid != $rs[$j]['bid'])
                $bname = transb8('name', $rs[$j]['bid'], $gid);
            if ($tmpcid != $rs[$j]['cid']) {
                $tsql->query("select mtype,name from `$tb_class` where gid='$gid' and cid='" . $rs[$j]['cid'] . "'");
                $tsql->next_record();
                $cname = $tsql->f('name');
                $mtype = $tsql->f('mtype');
            }
            $m = $kj[$i]['m' . ($mtype + 1)];
            if (strpos($bname, '名') || $bname=="冠军" || $bname=="亚军") {
                if (is_numeric($rs[$j]['name'])) {
                    if ($m != $rs[$j]['name']) {
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
                } else if (strpos('[单双]', $rs[$j]['name'])) {
                    $tmp = danshuang($m);
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
                } else if (strpos('[大小]', $rs[$j]['name'])) {
                    if (!(($m >= 6 & $rs[$j]['name'] == '大') | ($m <= 5 & $rs[$j]['name'] == '小'))) {
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
                } else if (strpos('[质合]', $rs[$j]['name'])) {
                    $tmp = zhihe($m);
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
                } else if (strpos('[龙虎]', $rs[$j]['name'])) {
                    $m2     = 10 - $mtype;
                    $longhu = longhuhe($m, $kj[$i]['m' . $m2]);
                    if ($longhu != $rs[$j]['name']) {
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
            } else if ($bname == '冠亚军组合') {
                $m = $kj[$i]['m1'] + $kj[$i]['m2'];
                if (is_numeric($rs[$j]['name'])) {
                    if ($m != $rs[$j]['name']) {
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
                } else if (strpos('[冠亚单冠亚双]', $rs[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ("冠亚" . $tmp != $rs[$j]['name']) {
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
                } else if (strpos('[冠亚大冠亚小]', $rs[$j]['name'])) {
                    if (!(($m > 11 & $rs[$j]['name'] == '冠亚大') | ($m <= 11 & $rs[$j]['name'] == '冠亚小'))) {
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
            }
            $tmpbid = $rs[$j]['bid'];
            $tmpcid = $rs[$j]['cid'];
        }
    }
}
function searchqishu_161($gid, $psize, $page,$fenlei)
{
    global $tsql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $bname = transb8('name', $bid, $gid);
    $kj    = array();
    $tsql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $tsql->next_record();
    $mnum          = $tsql->f('mnum');
    $thisqishu     = $tsql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs            = $tsql->arr("select * from `$tb_play` where gid='$gid' order by bid,sid,cid,xsort", 1);
    $cr            = count($rs);
    for ($i = 0; $i < $ck; $i++) {
        $m  = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'] + $kj[$i]['m6'] + $kj[$i]['m7'] + $kj[$i]['m8'] + $kj[$i]['m9'] + $kj[$i]['m10'] + $kj[$i]['m11'] + $kj[$i]['m12'] + $kj[$i]['m13'] + $kj[$i]['m14'] + $kj[$i]['m15'] + $kj[$i]['m16'] + $kj[$i]['m17'] + $kj[$i]['m18'] + $kj[$i]['m19'] + $kj[$i]['m20'];
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
            if ($kj[0]['u'][$j] == 1)
                continue;
            if ($tmpbid != $rs[$j]['bid'])
                $bname = transb8('name', $rs[$j]['bid'], $gid);
				switch($bname){
            case '正码':
                $arr = array(
                    $kj[$i]['m1'],
                    $kj[$i]['m2'],
                    $kj[$i]['m3'],
                    $kj[$i]['m4'],
                    $kj[$i]['m5'],
                    $kj[$i]['m6'],
                    $kj[$i]['m7'],
                    $kj[$i]['m8'],
                    $kj[$i]['m9'],
                    $kj[$i]['m10'],
                    $kj[$i]['m11'],
                    $kj[$i]['m12'],
                    $kj[$i]['m13'],
                    $kj[$i]['m14'],
                    $kj[$i]['m15'],
                    $kj[$i]['m16'],
                    $kj[$i]['m17'],
                    $kj[$i]['m18'],
                    $kj[$i]['m19'],
                    $kj[$i]['m20']
                );
                if (in_array($rs[$j]['name'], $arr)) {
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
            case  '总和':
              if (strpos('[总和单总和双]', $rs[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ("总和" . $tmp != $rs[$j]['name']) {
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
                } else if (strpos('[总和大总和小]', $rs[$j]['name'])) {
                    if (!(($m > 810 & $rs[$j]['name'] == '总和大') | ($m < 810 & $rs[$j]['name'] == '总和小'))) {
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
                } else if (strpos('[总和810]', $rs[$j]['name'])) {
                    if ($tmp != 810) {
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
                case  '前后和':
                    if (strpos('[前(多)后(多)]', $rs[$j]['name'])) {
                        if (!(("前(多)" == $rs[$j]['name'] & $zq > 10) | ("后(多)" == $rs[$j]['name'] & $zq < 10) | ("前后(和)" == $rs[$j]['name'] & $zq == 10))) {
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
                    } else if (strpos('[前后(和)]', $rs[$j]['name'])) {
                        if (!($kj[$i]['m10'] <= 40 & $kj[$i]['m11'] >= 41)) {
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
                case  '单双和':
                    if (strpos('[单(多)双(多)单双(和)]', $rs[$j]['name'])) {
                        if (!(("单(多)" == $rs[$j]['name'] & $zd > 10) | ("双(多)" == $rs[$j]['name'] & $zd < 10) | ("单双(和)" == $rs[$j]['name'] & $zd == 10))) {
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
                case  '总和过关':
                    if (strpos('[总大单总小单总大双总小双]', $rs[$j]['name'])) {
                        $tmp = danshuang($m);
                        if ($m < 810)
                            $tmp = "总小" . $tmp;
                        else if ($tmp > 810)
                            $tmp = "总大" . $tmp;
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
                case  '五行':
                    if (strpos('[金木水火土]', $rs[$j]['name'])) {
                        $tmp = wuhang_161($m);
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
                case '正码':
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
				
            }
            $tmpbid = $rs[$j]['bid'];
        }
    }
}
function searchqishu_151($gid, $psize, $page,$fenlei)
{
    global $tsql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $kj = array();
    $tsql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $tsql->next_record();
    $mnum          = $tsql->f('mnum');
    $thisqishu     = $tsql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $rs            = $tsql->arr("select * from `$tb_play` where gid='$gid' order by bid,sid,cid,xsort", 1);
    $cr            = count($rs);
    for ($i = 0; $i < $ck; $i++) {
        $ma = array(
            $kj[$i]['m1'],
            $kj[$i]['m2'],
            $kj[$i]['m3']
        );
        $m  = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
        for ($j = 0; $j < $cr; $j++) {
            if ($kj[0]['u'][$j] == 1) {
                continue;
            }
            if ($tmpbid != $rs[$j]['bid'])
                $bname = transb8('name', $rs[$j]['bid'], $gid);
            if ($bname == '三军') {
                if (strpos('[三军大三军小]', $rs[$j]['name'])) {
                    if (!(($m >= 11 & $rs[$j]['name'] == '三军大') | ($m <= 10 & $rs[$j]['name'] == '三军小'))) {
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
                } else if (is_numeric($rs[$j]['name'])) {
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
                }
            } else if ($bname == '围骰') {
                if (!(baozhi($kj[0], $kj[1], $kj[2]) == 1 & $ma[0] == $rs[$j]['name'] % 10)) {
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
            } else if ($bname == '全骰') {
                if (baozhi($kj[0], $kj[1], $kj[2]) != 1) {
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
            } else if ($bname == '点数') {
                if ($rs[$j]['name'] != $m) {
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
            } else if ($bname == '长牌') {
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
            } else if ($bname == '短牌') {
                $one = $rs[$j]['name'] % 10;
                $cs  = array_count_values($ma);
                if ($cs[$one] < 2) {
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
            $tmpbid = $rs[$j]['bid'];
        }
    }
}
function updateqishu($gid, $pid, $z, $v)
{
    global $psql, $tb_play;
    if ($z == 1) {
        $psql->query("update `$tb_play` set zqishu='$v' where gid='$gid' and pid='" . $pid . "'");
    } else {
        $psql->query("update `$tb_play` set buzqishu='$v' where gid='$gid' and pid='" . $pid . "'");
    }
}
?>