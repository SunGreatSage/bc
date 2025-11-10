<?php
include_once('jsfunc.php');
function long($gid, $bid, $sid, $cid, $psize, $page)
{
    global $psql, $tb_game;
    $psql->query("select fenlei from `$tb_game` where gid='$gid'");
    $psql->next_record();
    if ($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
        return call_user_func("long_101", $gid, $bid, $sid, $cid, $psize, $page, "ssc");
    } else {
        return call_user_func("long_" . $psql->f('fenlei'), $gid, $bid, $sid, $cid, $psize, $page, $psql->f('fenlei'));
    }
}
function long_151($gid, $bid, $sid, $cid, $psize, $page, $fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $bname = transb8('name', $bid, $gid);
    $sname = transs8('name', $sid, $gid);
    $msql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='$cid'");
    $msql->next_record();
    $cname = $msql->f('name');
    $kj    = array();
    $msql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    if ($bname == '三军') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $m            = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
            $arr          = array(
                $kj[$i]['m1'],
                $kj[$i]['m2'],
                $kj[$i]['m3']
            );
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = '';
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($kj[$i]['m1'] == '')
                    continue;
                if (strpos('[三军大三军小]', $p[$j]['name'])) {
                    if (($m >= 11 & $p[$j]['name'] == '三军大') | ($m <= 10 & $p[$j]['name'] == '三军小')) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (in_array($p[$j]['name'], $arr)) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    } else if ($bname == '围全骰') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $m            = baozhi($kj[$i]['m1'], $kj[$i]['m2'], $kj[$i]['m3']);
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = '';
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($kj[$i]['m1'] == '')
                    continue;
                if ($m == 1 & $p[$j]['name'] == '全骰') {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
                if ($m == 1 & $p[$j]['name'] % 10 == $kj[$i]['m1']) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    } else if ($bname == '点数') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $m            = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = $m;
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if (str_replace('点', '', $p[$j]['name']) == $m) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    } else if ($bname == '长牌') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $arr          = array(
                $kj[$i]['m1'],
                $kj[$i]['m2'],
                $kj[$i]['m3']
            );
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = '';
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                $m1              = $p[$j]['name'] % 10;
                $m2              = ($p[$j]['name'] - $p[$j]['name'] % 10) / 10;
                if (in_array($m1, $arr) & in_array($m2, $arr)) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    } else if ($bname == '短牌') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $arr          = array(
                $kj[$i]['m1'],
                $kj[$i]['m2'],
                $kj[$i]['m3']
            );
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = '';
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                $m1              = $p[$j]['name'] % 10;
                $cs              = array_count_values($arr);
                if (in_array($m1, $arr) & $cs[$m1] >= 2) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    }
    return $kj;
}
function long_161($gid, $bid, $sid, $cid, $psize, $page, $fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $bname = transb8('name', $bid, $gid);
    $sname = transs8('name', $sid, $gid);
    $msql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='$cid'");
    $msql->next_record();
    $cname = $msql->f('name');
    $kj    = array();
    $msql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    if ($bname == '正码') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
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
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                $kj[$i]['ms']    = $sname;
                $kj[$i]['mi']    = '';
                if (in_array($p[$j]['name'], $arr)) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    } else if ($bname == '总和过关') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $m   = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'] + $kj[$i]['m6'] + $kj[$i]['m7'] + $kj[$i]['m8'] + $kj[$i]['m9'] + $kj[$i]['m10'] + $kj[$i]['m11'] + $kj[$i]['m12'] + $kj[$i]['m13'] + $kj[$i]['m14'] + $kj[$i]['m15'] + $kj[$i]['m16'] + $kj[$i]['m17'] + $kj[$i]['m18'] + $kj[$i]['m19'] + $kj[$i]['m20'];
            $tmp = danshuang($m);
            if ($m < 810) {
                $tmp = '小' . $tmp;
            } else if ($m > 810) {
                $tmp = '大' . $tmp;
            }
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = $m;
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($m == 0)
                    continue;
                if ($m == 810) {
                    $kj[$i]['m'][$j] = '和';
                } else if ($p[$j]['name'] == '总' . $tmp) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    } else if ($bname == '总和') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $m            = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'] + $kj[$i]['m6'] + $kj[$i]['m7'] + $kj[$i]['m8'] + $kj[$i]['m9'] + $kj[$i]['m10'] + $kj[$i]['m11'] + $kj[$i]['m12'] + $kj[$i]['m13'] + $kj[$i]['m14'] + $kj[$i]['m15'] + $kj[$i]['m16'] + $kj[$i]['m17'] + $kj[$i]['m18'] + $kj[$i]['m19'] + $kj[$i]['m20'];
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = $m;
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($m == 0)
                    continue;
                if (strpos('[总和单总和双]', $p[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ("总和" . $tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[总和大总和小]', $p[$j]['name'])) {
                    if (($m > 810 & $p[$j]['name'] == '总和大') | ($m < 810 & $p[$j]['name'] == '总和小')) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if ($p[$j]['name'] == '总和810') {
                    if (810 == $m) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                }
            }
        }
    } else if ($bname == '五行') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $m            = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'] + $kj[$i]['m6'] + $kj[$i]['m7'] + $kj[$i]['m8'] + $kj[$i]['m9'] + $kj[$i]['m10'] + $kj[$i]['m11'] + $kj[$i]['m12'] + $kj[$i]['m13'] + $kj[$i]['m14'] + $kj[$i]['m15'] + $kj[$i]['m16'] + $kj[$i]['m17'] + $kj[$i]['m18'] + $kj[$i]['m19'] + $kj[$i]['m20'];
            $mk           = wuhang_161($m);
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = $m;
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($m == 0)
                    continue;
                if ($p[$j]['name'] == $mk) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    } else if ($bname == '单双和') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $zd = 0;
            for ($h = 1; $h <= 20; $h++) {
                if ($kj[$i]['m' . $h] % 2 == 1)
                    $zd++;
            }
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = '';
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($zd == 0)
                    continue;
                if ($zd == 10 & $p[$j]['name'] == '单双(和)') {
                    $kj[$i]['m'][$j] = '和';
                } else if ($p[$j]['name'] == '单(多)' & $zd > 10) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                } else if ($p[$j]['name'] == '后(多)' & $zd < 10) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    } else if ($bname == '前后和') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $zq = 0;
            for ($h = 1; $h <= 20; $h++) {
                if ($kj[$i]['m' . $h] <= 40)
                    $zq++;
            }
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = '';
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($zq == 0)
                    continue;
                if ($zq == 10 & $p[$j]['name'] == '前后(和)') {
                    $kj[$i]['m'][$j] = '和';
                } else if ($p[$j]['name'] == '前(多)' & $zq > 10) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                } else if ($p[$j]['name'] == '后(多)' & $zq < 10) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    }
    return $kj;
}
function long_103($gid, $bid, $sid, $cid, $psize, $page, $fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $bname = transb8('name', $bid, $gid);
    $sname = transs8('name', $sid, $gid);
    $msql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='$cid'");
    $msql->next_record();
    $cname = $msql->f('name');
    $kj    = array();
    $msql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    if (strpos($bname, '球') | $bname == '特码号') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                $m               = $kj[$i]['m' . $p[$j]['mtype']];
                $kj[$i]['ms']    = $sname;
                $kj[$i]['mi']    = $m;
                if ($m == '')
                    continue;
                if (is_numeric($p[$j]['name'])) {
                    if ($m == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[单双]', $p[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[大小]', $p[$j]['name'])) {
                    if (($m >= 11 & $p[$j]['name'] == '大') | ($m <= 10 & $p[$j]['name'] == '小')) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[合单合双]', $p[$j]['name'])) {
                    $tmp = danshuang(heshu($m));
                    if ("合" . $tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[尾大尾小]', $p[$j]['name'])) {
                    $tmp = daxiaow($m % 10);
                    if ("尾" . $tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[春夏秋冬]', $p[$j]['name'])) {
                    $tmp = siji($m);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[金木水火土]', $p[$j]['name'])) {
                    $tmp = wuhang($m);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[东南西北]', $p[$j]['name'])) {
                    $tmp = fangwei($m);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[中发白]', $p[$j]['name'])) {
                    $tmp = zhongfabai($m);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                }
            }
        }
    } else if ($bname == '总和龙虎') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $m            = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'] + $kj[$i]['m6'] + $kj[$i]['m7'] + $kj[$i]['m8'];
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = $m;
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($kj[$i]['m1'] == '')
                    continue;
                if (strpos('[总和单总和双]', $p[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ("总" . $tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[总和大总和小]', $p[$j]['name'])) {
                    if (($m > 84 & $p[$j]['name'] == '总和大') | ($m < 84 & $p[$j]['name'] == '总和小')) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    } else if ($m == 84) {
                        $kj[$i]['m'][$j] = '和';
                    }
                } else if (strpos('[总和尾大总和尾小]', $p[$j]['name'])) {
                    $tmp = daxiao($m % 10);
                    if ("总尾" . $tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[龙虎]', $p[$j]['name'])) {
                    $tmp = longhuhe($kj[$i]['m1'], $kj[$i]['m8']);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                }
            }
        }
    } else if ($bname == '正码') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $arr          = array(
                $kj[$i]['m1'],
                $kj[$i]['m2'],
                $kj[$i]['m3'],
                $kj[$i]['m4'],
                $kj[$i]['m5'],
                $kj[$i]['m6'],
                $kj[$i]['m7'],
                $kj[$i]['m8']
            );
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = '';
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if (in_array($p[$j]['name'], $arr)) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    }
    return $kj;
}
function long_121($gid, $bid, $sid, $cid, $psize, $page, $fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $bname = transb8('name', $bid, $gid);
    $sname = transs8('name', $sid, $gid);
    $msql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='$cid'");
    $msql->next_record();
    $cname = $msql->f('name');
    $kj    = array();
    $msql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    if (strpos($bname, '球')) {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $kj[$i]['ms'] = $sname;
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                $m               = $kj[$i]['m' . $p[$j]['mtype']];
                $kj[$i]['mi']    = $m;
                if ($m == '')
                    continue;
                if (is_numeric($p[$j]['name'])) {
                    if ($m == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[单双]', $p[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[大小]', $p[$j]['name'])) {
                    if (($m <= 10 & $m >= 6 & $p[$j]['name'] == '大') | ($m <= 5 & $p[$j]['name'] == '小')) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    } else if ($m == 11) {
                        $kj[$i]['m'][$j] = '和';
                    }
                }
            }
        }
    } else if ($bname == '总和龙虎') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $m            = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = $m;
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if ($kj[$i]['m1'] == '')
                    continue;
                if (strpos('[总单总双]', $p[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ("总" . $tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[总大总小]', $p[$j]['name'])) {
                    if (($m > 30 & $p[$j]['name'] == '总大') | ($m < 30 & $p[$j]['name'] == '总小')) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    } else if ($m == 30) {
                        $kj[$i]['m'][$j] = '和';
                    }
                } else if (strpos('[总尾大总尾小]', $p[$j]['name'])) {
                    $tmp = daxiao($m % 10);
                    if ("总尾" . $tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[总尾质总尾合]', $p[$j]['name'])) {
                    $tmp = zhihe($m % 10);
                    if ("总尾" . $tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[龙虎]', $p[$j]['name'])) {
                    $tmp = longhuhe($kj[$i]['m1'], $kj[$i]['m5']);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                }
            }
        }
    } else if ($bname == '正码') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $arr          = array(
                $kj[$i]['m1'],
                $kj[$i]['m2'],
                $kj[$i]['m3'],
                $kj[$i]['m4'],
                $kj[$i]['m5']
            );
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = '[' . implode(',', $arr) . ']';
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if (in_array($p[$j]['name'], $arr)) {
                    $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    }
    return $kj;
}
function long_107($gid, $bid, $sid, $cid, $psize, $page, $fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $bname = transb8('name', $bid, $gid);
    $sname = transs8('name', $sid, $gid);
    $msql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='$cid'");
    $msql->next_record();
    $cname = $msql->f('name');
    $kj    = array();
    $msql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    if (strpos($bname, '名') | strpos($bname, '军')) {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                $m               = $kj[$i]['m' . $p[$j]['mtype']];
                $kj[$i]['ms']    = $sname;
                $kj[$i]['mi']    = $m;
                if ($m == '')
                    continue;
                if (is_numeric($p[$j]['name'])) {
                    if ($m == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[单双]', $p[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[质合]', $p[$j]['name'])) {
                    $tmp = zhihe($m);
                    if ($tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[大小]', $p[$j]['name'])) {
                    if (($m >= 6 & $p[$j]['name'] == '大') | ($m <= 5 & $p[$j]['name'] == '小')) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[龙虎]', $p[$j]['name'])) {
                    $m2     = 11 - $p[$j]['mtype'];
                    $longhu = longhuhe($kj[$i]['m' . ($p[$j]['mtype'])], $kj[$i]['m' . $m2]);
                    if ($longhu == $p[$j]['name'])
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                }
            }
        }
    } else if ($bname == '冠亚军组合') {
        $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
        $p = array();
        $i = 0;
        while ($msql->next_record()) {
            $p[$i]['name']     = $msql->f('name');
            $p[$i]['zqishu']   = $msql->f('zqishu');
            $p[$i]['buzqishu'] = $msql->f('buzqishu');
            $fsql->query("select name,mtype from `$tb_class` where gid='$gid' and cid='" . $msql->f('cid') . "'");
            $fsql->next_record();
            $p[$i]['cname'] = $fsql->f('name');
            $p[$i]['mtype'] = $fsql->f('mtype') + 1;
            $i++;
        }
        $cp = count($p);
        for ($i = 0; $i < $ck; $i++) {
            $m            = $kj[$i]['m1'] + $kj[$i]['m2'];
            $kj[$i]['ms'] = $sname;
            $kj[$i]['mi'] = $m;
            for ($j = 0; $j < $cp; $j++) {
                if ($i == 0) {
                    $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                    $kj[$i]['th'][$j]['bname'] = $bname;
                    $kj[$i]['th'][$j]['sname'] = $sname;
                    $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                    $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                    $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                }
                $kj[$i]['m'][$j] = '-';
                if (is_numeric($p[$j]['name'])) {
                    if ($m == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[冠亚单冠亚双]', $p[$j]['name'])) {
                    $tmp = danshuang($m);
                    if ("和" . $tmp == $p[$j]['name']) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                } else if (strpos('[冠亚大冠亚小]', $p[$j]['name'])) {
                    if (($m > 11 & $p[$j]['name'] == '冠亚大') | ($m <= 11 & $p[$j]['name'] == '冠亚小')) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    }
                }
            }
        }
    }
    return $kj;
}
function long_101($gid, $bid, $sid, $cid, $psize, $page, $fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $bname = transb8('name', $bid, $gid);
    $sname = transs8('name', $sid, $gid);
    $cname = transc8('name', $cid, $gid);
    $kj    = array();
    $msql->query("select mnum,thisqishu from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    switch ($bname) {
        case '1~5':
            $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by cid,xsort");
            $p = array();
            $i = 0;
            while ($msql->next_record()) {
                $p[$i]['name']     = $msql->f('name');
                $p[$i]['zqishu']   = $msql->f('zqishu');
                $p[$i]['buzqishu'] = $msql->f('buzqishu');
                $p[$i]['cname']    = transc8('name', $msql->f('cid'), $gid);
                $i++;
            }
            $cp = count($p);
            if ($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                if ($sname == '第一球') {
                    $m = 1;
                } else if ($sname == '第二球') {
                    $m = 2;
                } else if ($sname == '第三球') {
                    $m = 3;
                }
            } else {
                if ($sname == '第一球') {
                    $m = 1;
                } else if ($sname == '第二球') {
                    $m = 2;
                } else if ($sname == '第三球') {
                    $m = 3;
                } else if ($sname == '第四球') {
                    $m = 4;
                } else if ($sname == '第五球') {
                    $m = 5;
                }
            }
            for ($i = 0; $i < $ck; $i++) {
                for ($j = 0; $j < $cp; $j++) {
                    if ($i == 0) {
                        $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                        $kj[$i]['th'][$j]['bname'] = $bname;
                        $kj[$i]['th'][$j]['sname'] = $sname;
                        $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                        $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                        $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                    }
                    $kj[$i]['ms']    = $sname;
                    $kj[$i]['mi']    = $kj[$i]['m' . $m];
                    $kj[$i]['m'][$j] = '';
                    if ($kj[$i]['mi'] == '')
                        continue;
                    if (is_numeric($p[$j]['name'])) {
                        if ($kj[$i]['m' . $m] != $p[$j]['name']) {
                            $kj[$i]['m'][$j] = '';
                        } else {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if (strpos('[单双]', $p[$j]['name'])) {
                        $tmp = danshuang($kj[$i]['m' . $m]);
                        if ($tmp != $p[$j]['name']) {
                            $kj[$i]['m'][$j] = '';
                        } else {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if (strpos('[大小]', $p[$j]['name'])) {
                        $tmp = daxiao($kj[$i]['m' . $m]);
                        if ($tmp != $p[$j]['name']) {
                            $kj[$i]['m'][$j] = '';
                        } else {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if (strpos('[质合]', $p[$j]['name'])) {
                        $tmp = zhihe($kj[$i]['m' . $m]);
                        if ($tmp != $p[$j]['name']) {
                            $kj[$i]['m'][$j] = '';
                        } else {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    }
                }
            }
            break;
        case '1字组合':
            $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' and cid='$cid' order by xsort");
            $p = array();
            $i = 0;
            while ($msql->next_record()) {
                $p[$i]['name']     = $msql->f('name');
                $p[$i]['zqishu']   = $msql->f('zqishu');
                $p[$i]['buzqishu'] = $msql->f('buzqishu');
                $i++;
            }
            $cp = count($p);
            for ($i = 0; $i < $ck; $i++) {
                $arr = array();
                if ($cname == '全五1字组合') {
                    $arr = array(
                        $kj[$i]['m1'],
                        $kj[$i]['m2'],
                        $kj[$i]['m3'],
                        $kj[$i]['m4'],
                        $kj[$i]['m5']
                    );
                } else if ($cname == '前三1字组合' | $cname == '1字组合') {
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
                $kj[$i]['ms'] = $cname;
                $kj[$i]['mi'] = '';
                for ($j = 0; $j < $cp; $j++) {
                    if ($i == 0) {
                        $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                        $kj[$i]['th'][$j]['bname'] = $bname;
                        $kj[$i]['th'][$j]['sname'] = $sname;
                        $kj[$i]['th'][$j]['cname'] = $cname;
                        $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                        $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                    }
                    if (in_array($p[$j]['name'], $arr)) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    } else {
                        $kj[$i]['m'][$j] = '';
                    }
                }
            }
            break;
        case '2字和数':
            $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' order by sid,cid ,xsort");
            $p = array();
            $i = 0;
            while ($msql->next_record()) {
                $p[$i]['name']     = $msql->f('name');
                $p[$i]['sname']    = transs8('name', $msql->f('sid'), $gid);
                $p[$i]['cname']    = transs8('name', $msql->f('cid'), $gid);
                $p[$i]['zqishu']   = $msql->f('zqishu');
                $p[$i]['buzqishu'] = $msql->f('buzqishu');
                $i++;
            }
            $cp = count($p);
            for ($i = 0; $i < $ck; $i++) {
                $kj[$i]['ms'] = $bname;
                if ($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                    $kj[$i]['mi'] = $kj[$i]['m1'] . ',' . $kj[$i]['m2'] . ',' . $kj[$i]['m3'];
                } else {
                    $kj[$i]['mi'] = $kj[$i]['m1'] . ',' . $kj[$i]['m2'] . ',' . $kj[$i]['m3'] . ',' . $kj[$i]['m4'] . ',' . $kj[$i]['m5'];
                }
                for ($j = 0; $j < $cp; $j++) {
                    if ($i == 0) {
                        $kj[$i]['th'][$j]['bname'] = $bname;
                        $kj[$i]['th'][$j]['sname'] = $p[$j]['sname'];
                        $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                        $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                        $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                        $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                    }
                    $kj[$i]['m'][$j] = '-';
                    if ($kj[$i]['m1'] == '')
                        continue;
                    if ($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                        if ($p[$j]['sname'] == '百十和数') {
                            $he = $kj[$i]['m1'] + $kj[$i]['m2'];
                        } else if ($p[$j]['sname'] == '百个和数') {
                            $he = $kj[$i]['m1'] + $kj[$i]['m3'];
                        } else if ($p[$j]['sname'] == '十个和数') {
                            $he = $kj[$i]['m2'] + $kj[$i]['m3'];
                        }
                    } else {
                        if ($p[$j]['sname'] == '万千和数') {
                            $he = $kj[$i]['m1'] + $kj[$i]['m2'];
                        } else if ($p[$j]['sname'] == '万百和数') {
                            $he = $kj[$i]['m1'] + $kj[$i]['m3'];
                        } else if ($p[$j]['sname'] == '万和数十') {
                            $he = $kj[$i]['m1'] + $kj[$i]['m4'];
                        } else if ($p[$j]['sname'] == '万和数个') {
                            $he = $kj[$i]['m1'] + $kj[$i]['m5'];
                        } else if ($p[$j]['sname'] == '千百和数') {
                            $he = $kj[$i]['m2'] + $kj[$i]['m3'];
                        } else if ($p[$j]['sname'] == '千十和数') {
                            $he = $kj[$i]['m2'] + $kj[$i]['m4'];
                        } else if ($p[$j]['sname'] == '千个和数') {
                            $he = $kj[$i]['m2'] + $kj[$i]['m5'];
                        } else if ($p[$j]['sname'] == '百十和数') {
                            $he = $kj[$i]['m3'] + $kj[$i]['m4'];
                        } else if ($p[$j]['sname'] == '百个和数') {
                            $he = $kj[$i]['m3'] + $kj[$i]['m5'];
                        } else if ($p[$j]['sname'] == '十个和数') {
                            $he = $kj[$i]['m4'] + $kj[$i]['m5'];
                        }
                    }
                    $he = p0($he);
                    if ($p[$j]['name'] == danshuang($he)) {
                        $kj[$i]['m'][$j] = $p[$j]['name'];
                    } else if ($p[$j]['name'] == "和尾" . daxiaow($he % 10)) {
                        $kj[$i]['m'][$j] = str_replace('和', '', $p[$j]['name']);
                    }
                }
            }
            break;
        case '3字和数':
            if (strpos("[$cname]", '和')) {
            } else {
            }
            $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by xsort");
            $p = array();
            $i = 0;
            while ($msql->next_record()) {
                $p[$i]['name']     = $msql->f('name');
                $p[$i]['sname']    = transs8('name', $msql->f('sid'), $gid);
                $p[$i]['cname']    = transc8('name', $msql->f('cid'), $gid);
                $p[$i]['zqishu']   = $msql->f('zqishu');
                $p[$i]['buzqishu'] = $msql->f('buzqishu');
                $i++;
            }
            $cp = count($p);
            for ($i = 0; $i < $ck; $i++) {
                if ($sname == '前三和数') {
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
                } else if ($sname == '中三和数') {
                    $he = $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'];
                } else if ($sname == '后三和数') {
                    $he = $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                }
                $kj[$i]['ms'] = $sname;
                $kj[$i]['mi'] = $he;
                for ($j = 0; $j < $cp; $j++) {
                    if ($i == 0) {
                        $kj[$i]['th'][$j]['bname'] = $bname;
                        $kj[$i]['th'][$j]['sname'] = $p[$j]['sname'];
                        $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                        $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                        $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                        $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                    }
                    $wei             = $he % 10;
                    $kj[$i]['m'][$j] = '-';
                    if ($kj[$i]['m1'] == '')
                        continue;
                    if (strpos('[和单和双]', $p[$j]['name'])) {
                        $tmp = danshuang($he);
                        if (strpos($p[$j]['name'], $tmp)) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if (strpos('[和大和小]', $p[$j]['name'])) {
                        if (($he >= 14 & $p[$j]['name'] == '和大') | ($he <= 13 & $p[$j]['name'] == '和小')) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if (strpos('[和尾大和尾小]', $p[$j]['name'])) {
                        $tmp = daxiao($wei);
                        if (strpos($p[$j]['name'], $tmp)) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if (strpos('[和尾质和尾合]', $p[$j]['name'])) {
                        $tmp = zhihe($wei);
                        if (strpos($p[$j]['name'], $tmp)) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if ($p[$j]['cname'] == '和数') {
                        $ps  = explode('~', $p[$j]['name']);
                        $cps = count($ps);
                        if ($cps == 1) {
                            if ($p[$j]['name'] == $he) {
                                $kj[$i]['m'][$j] = $p[$j]['name'];
                            }
                        } else {
                            if ($he >= $ps[0] & $he <= $ps[1]) {
                                $kj[$i]['m'][$j] = $p[$j]['name'];
                            }
                        }
                    } else if ($p[$j]['cname'] == '尾数') {
                        if ($wei == $p[$j]['name']) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    }
                }
            }
            break;
        case '总和龙虎':
            $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' and sid='$sid' order by xsort");
            $p = array();
            $i = 0;
            while ($msql->next_record()) {
                if ($msql->f('name') == '总大单' | $msql->f('name') == '总大双' | $msql->f('name') == '总小单' | $msql->f('name') == '总小双')
                    continue;
                $p[$i]['name']     = $msql->f('name');
                $p[$i]['sname']    = transs8('name', $msql->f('sid'), $gid);
                $p[$i]['cname']    = transc8('name', $msql->f('cid'), $gid);
                $p[$i]['zqishu']   = $msql->f('zqishu');
                $p[$i]['buzqishu'] = $msql->f('buzqishu');
                $i++;
            }
            $cp = count($p);
            for ($i = 0; $i < $ck; $i++) {
                if ($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'];
                } else {
                    $he = $kj[$i]['m1'] + $kj[$i]['m2'] + $kj[$i]['m3'] + $kj[$i]['m4'] + $kj[$i]['m5'];
                }
                $wei          = $he % 10;
                $kj[$i]['ms'] = $sname;
                $kj[$i]['mi'] = $he;
                for ($j = 0; $j < $cp; $j++) {
                    if ($i == 0) {
                        $kj[$i]['th'][$j]['bname'] = $bname;
                        $kj[$i]['th'][$j]['sname'] = $p[$j]['sname'];
                        $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                        $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                        $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                        $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                    }
                    $kj[$i]['m'][$j] = '-';
                    if ($kj[$i]['m1'] == '')
                        continue;
                    if (strpos('[总和单总和双]', $p[$j]['name'])) {
                        $tmp = danshuang($he);
                        if (strpos($p[$j]['name'], $tmp)) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if (strpos('[总和大总和小]', $p[$j]['name'])) {
                        if ($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                            if (($he >= 14 & $p[$j]['name'] == '总和大') | ($he <= 13 & $p[$j]['name'] == '总和小')) {
                                $kj[$i]['m'][$j] = $p[$j]['name'];
                            }
                        } else {
                            if (($he >= 23 & $p[$j]['name'] == '总和大') | ($he <= 22 & $p[$j]['name'] == '总和小')) {
                                $kj[$i]['m'][$j] = $p[$j]['name'];
                            }
                        }
                    } else if (strpos('[总和尾大总和尾小]', $p[$j]['name'])) {
                        $tmp = daxiao($wei);
                        if (strpos($p[$j]['name'], $tmp)) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if (strpos('[总尾质总尾合]', $p[$j]['name'])) {
                        $tmp = zhihe($wei);
                        if (strpos($p[$j]['name'], $tmp)) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if (strpos('[总大单总大双总小单总小双]', $p[$j]['name'])) {
                        $ds = danshuang($he);
                        if ($he >= 14)
                            $tmp = '大' . $ds;
                        else
                            $tmp = '小' . $ds;
                        if (strpos($p[$j]['name'], $tmp)) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if ($p[$j]['cname'] == '和数' | strpos($p[$j]['cname'],'和-')) {
                        $ps  = explode('~', $p[$j]['name']);
                        $cps = count($ps);
                        if ($cps == 1) {
                            if ($p[$j]['name'] == $he) {
                                $kj[$i]['m'][$j] = $p[$j]['name'];
                            }
                        } else {
                            if ($he >= $ps[0] & $he <= $ps[1]) {
                                $kj[$i]['m'][$j] = $p[$j]['name'];
                            }
                        }
                    } else if ($p[$j]['cname'] == '总和尾数') {
                        if ($wei == $p[$j]['name']) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if ($p[$j]['cname'] == '龙虎和') {
                        if ($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                            $tmp = longhuhe($kj[$i]['m1'], $kj[$i]['m3']);
                        } else {
                            $tmp = longhuhe($kj[$i]['m1'], $kj[$i]['m5']);
                        }
                        if ($tmp == $p[$j]['name']) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    }
                }
            }
            break;
        case '牛牛梭哈':
            $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid'  order by xsort");
            $p = array();
            $i = 0;
            while ($msql->next_record()) {
                $p[$i]['name']     = $msql->f('name');
                $p[$i]['sname']    = transs8('name', $msql->f('sid'), $gid);
                $p[$i]['cname']    = transc8('name', $msql->f('cid'), $gid);
                $p[$i]['zqishu']   = $msql->f('zqishu');
                $p[$i]['buzqishu'] = $msql->f('buzqishu');
                $i++;
            }
            $cp = count($p);
            for ($i = 0; $i < $ck; $i++) {
                $arr          = array(
                    $kj[$i]['m1'],
                    $kj[$i]['m2'],
                    $kj[$i]['m3'],
                    $kj[$i]['m4'],
                    $kj[$i]['m5']
                );
                $kj[$i]['ms'] = $kj[$i]['m1'] . ',' . $kj[$i]['m2'] . ',' . $kj[$i]['m3'] . ',' . $kj[$i]['m4'] . ',' . $kj[$i]['m5'];
                $kj[$i]['mi'] = '';
                $nn           = niuniu($arr);
                $sh           = suoha($arr);
                for ($j = 0; $j < $cp; $j++) {
                    if ($i == 0) {
                        $kj[$i]['th'][$j]['bname'] = $bname;
                        $kj[$i]['th'][$j]['sname'] = $p[$j]['sname'];
                        $kj[$i]['th'][$j]['cname'] = $p[$j]['cname'];
                        $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                        $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                        $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                    }
                    $kj[$i]['m'][$j] = '-';
                    if ($kj[$i]['m1'] == '')
                        continue;
                    if ($p[$j]['sname'] == '牛牛') {
                        if (!$nn[0] & $p[$j]['name'] == '无牛') {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        } else if ($nn[0] & $nn[1] & $p[$j]['name'] == '牛牛') {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        } else if (strpos('[牛单牛双]', $p[$j]['name']) & $nn[0]) {
                            if ($p[$j]['name'] == "牛" . danshuang($nn[2])) {
                                $kj[$i]['m'][$j] = $p[$j]['name'];
                            }
                        } else if (strpos('[牛大牛小]', $p[$j]['name']) & $nn[0]) {
                            if ($p[$j]['name'] == "牛" . nndaxiao($nn[2])) {
                                $kj[$i]['m'][$j] = $p[$j]['name'];
                            }
                        } else if (strpos('[牛质牛合]', $p[$j]['name']) & $nn[0]) {
                            if ($p[$j]['name'] == "牛" . zhihe($nn[2])) {
                                $kj[$i]['m'][$j] = $p[$j]['name'];
                            }
                        } else if ($p[$j]['name'] == '牛' . $nn[2]) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else {
                        if ($sh == $p[$j]['name']) {
                            $kj[$i]['m'][$j] = $sh;
                        }
                    }
                }
            }
            break;
        case '跨度':
            $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid' order by xsort");
            $p   = array();
            $i   = 0;
            $sid = 0;
            while ($msql->next_record()) {
                $p[$i]['name']     = $msql->f('name');
                $p[$i]['zqishu']   = $msql->f('zqishu');
                $p[$i]['buzqishu'] = $msql->f('buzqishu');
                if ($sid != $msql->f('sid')) {
                    $sname = transs8('name', $msql->f('sid'), $gid);
                }
                $sid            = $msql->f('sid');
                $p[$i]['sname'] = $sname;
                $i++;
            }
            $cp = count($p);
            for ($i = 0; $i < $ck; $i++) {
                $k1a = abs($kj[$i]['m1'] - $kj[$i]['m2']);
                $k2a = abs($kj[$i]['m1'] - $kj[$i]['m3']);
                $k3a = abs($kj[$i]['m2'] - $kj[$i]['m3']);
                if (!($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119)) {
                    $k1b = abs($kj[$i]['m2'] - $kj[$i]['m3']);
                    $k2b = abs($kj[$i]['m2'] - $kj[$i]['m4']);
                    $k3b = abs($kj[$i]['m3'] - $kj[$i]['m4']);
                    $k1c = abs($kj[$i]['m3'] - $kj[$i]['m4']);
                    $k2c = abs($kj[$i]['m3'] - $kj[$i]['m5']);
                    $k3c = abs($kj[$i]['m4'] - $kj[$i]['m5']);
                }
                $kj[$i]['ms'] = '跨度';
                if ($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                    $kj[$i]['mi'] = $kj[$i]['m1'] . ',' . $kj[$i]['m2'] . ',' . $kj[$i]['m3'];
                } else {
                    $kj[$i]['mi'] = $kj[$i]['m1'] . ',' . $kj[$i]['m2'] . ',' . $kj[$i]['m3'] . ',' . $kj[$i]['m4'] . ',' . $kj[$i]['m5'];
                }
                $ka = max($k1a, $k2a, $k3a);
                $kb = max($k1b, $k2b, $k3b);
                $kc = max($k1c, $k2c, $k3c);
                for ($j = 0; $j < $cp; $j++) {
                    if ($i == 0) {
                        $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                        $kj[$i]['th'][$j]['bname'] = $bname;
                        $kj[$i]['th'][$j]['sname'] = $sname;
                        $kj[$i]['th'][$j]['cname'] = $cname;
                        $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                        $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                    }
                    $kj[$i]['m'][$j] = '';
                    if ($kj[$i]['m1'] == '')
                        continue;
                    if ($p[$j]['sname'] == '前三' | $gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                        if ($p[$j]['name'] == $ka) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if ($p[$j]['sname'] == '中三') {
                        if ($p[$j]['name'] == $kb) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if ($p[$j]['sname'] == '后三') {
                        if ($p[$j]['name'] == $kc) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    }
                }
            }
            break;
        case '前中后三':
            $msql->query("select * from `$tb_play` where gid='$gid' and bid='$bid'  order by xsort");
            $p   = array();
            $i   = 0;
            $sid = 0;
            while ($msql->next_record()) {
                $p[$i]['name']     = $msql->f('name');
                $p[$i]['zqishu']   = $msql->f('zqishu');
                $p[$i]['buzqishu'] = $msql->f('buzqishu');
                if ($sid != $msql->f('sid')) {
                    $sname = transs8('name', $msql->f('sid'), $gid);
                }
                $sid            = $msql->f('sid');
                $p[$i]['sname'] = $sname;
                $i++;
            }
            $cp = count($p);
            for ($i = 0; $i < $ck; $i++) {
                $arr = array();
                $k1  = '';
                $k2  = '';
                $k3  = '';
                $ka  = qita($kj[$i]['m1'], $kj[$i]['m2'], $kj[$i]['m3']);
                if (!($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119)) {
                    $kb = qita($kj[$i]['m2'], $kj[$i]['m3'], $kj[$i]['m4']);
                    $kc = qita($kj[$i]['m3'], $kj[$i]['m4'], $kj[$i]['m5']);
                }
                $kj[$i]['ms'] = '前中后三';
                if ($gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                    $kj[$i]['mi'] = $kj[$i]['m1'] . ',' . $kj[$i]['m2'] . ',' . $kj[$i]['m3'];
                } else {
                    $kj[$i]['mi'] = $kj[$i]['m1'] . ',' . $kj[$i]['m2'] . ',' . $kj[$i]['m3'] . ',' . $kj[$i]['m4'] . ',' . $kj[$i]['m5'];
                }
				
                for ($j = 0; $j < $cp; $j++) {
                    if ($i == 0) {
                        $kj[$i]['th'][$j]['pname'] = $p[$j]['name'];
                        $kj[$i]['th'][$j]['bname'] = $bname;
                        $kj[$i]['th'][$j]['sname'] = $sname;
                        $kj[$i]['th'][$j]['cname'] = $cname;
                        $kj[0]['zqishu'][$j]       = $p[$j]['zqishu'];
                        $kj[0]['buzqishu'][$j]     = $p[$j]['buzqishu'];
                    }
                    $kj[$i]['m'][$j] = '';
                    if ($kj[$i]['m1'] == '')
                        continue;
                    if ($p[$j]['sname'] == '前三' | $gid == 117 | $gid == 163 | $gid == 116 | $gid == 118 | $gid == 119) {
                        if ($p[$j]['name'] == $ka) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if ($p[$j]['sname'] == '中三') {
                        if ($p[$j]['name'] == $kb) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    } else if ($p[$j]['sname'] == '后三') {
                        if ($p[$j]['name'] == $kc) {
                            $kj[$i]['m'][$j] = $p[$j]['name'];
                        }
                    }
                }
            }
            break;
    }
    unset($p);
    return $kj;
}
?>