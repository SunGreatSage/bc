<?php
class Malist                                                        {
    public static function getzh($flid, $kj, $cs = '') {
        $kjnum = explode(',', $kj['kj_num']);
        $kj['zonghe'] = '';
        $kj['zonghedx'] = '';
        $kj['zongheds'] = '';
        $kj['longhu'] = '';
        $kj['longhu1'] = '';
        $kj['longhu2'] = '';
        $kj['longhu3'] = '';
        $kj['longhu4'] = '';
        $kj['longhu5'] = '';
        $cn = count($kjnum);
        if ($flid == 161 | $flid == 163) $cn-= 1;
        if ($cn != $cs['cnum'] & $flid != 100) {
            return $kj;
        }
        if ($flid == 100) {
            if ($cn < 7) {
                for ($i = $cn; $i < 7; $i++) {
                    $kjnum[$i] = '';
                }
            }
        }
        if ($cn != $cs['cnum']) {
            $kj['zonghe'] = '';
            $kj['zonghedx'] = '';
            $kj['zongheds'] = '';
        } else {
            $kj['zonghe'] = self::zonghe($flid, $kjnum);
            $kj['zonghedx'] = self::zonghedaxiao($flid, $kj['zonghe'], 0);
            $kj['zongheds'] = self::danshuang($flid, $kj['zonghe'], 0);
        }
        switch ($flid) {
            case 101:
                $kj['longhu'] = self::longhuhe($flid, $kjnum[0], $kjnum[4], 0);
                break;

            case 103:
                $kj['longhu1'] = self::longhuhe($flid, $kjnum[0], $kjnum[7], 0);
                $kj['longhu2'] = self::longhuhe($flid, $kjnum[1], $kjnum[6], 0);
                $kj['longhu3'] = self::longhuhe($flid, $kjnum[2], $kjnum[5], 0);
                $kj['longhu4'] = self::longhuhe($flid, $kjnum[3], $kjnum[4], 0);
                break;

            case 107:
                $kj['longhu1'] = self::longhuhe($flid, $kjnum[0], $kjnum[9], 0);
                $kj['longhu2'] = self::longhuhe($flid, $kjnum[1], $kjnum[8], 0);
                $kj['longhu3'] = self::longhuhe($flid, $kjnum[2], $kjnum[7], 0);
                $kj['longhu4'] = self::longhuhe($flid, $kjnum[3], $kjnum[6], 0);
                $kj['longhu5'] = self::longhuhe($flid, $kjnum[4], $kjnum[5], 0);
                break;

            case 121:
                $kj['longhu'] = self::longhuhe($flid, $kjnum[0], $kjnum[4], 0);
                break;

            case 100:
                $ma = json_decode($cs['ma'], true);
                $kj['sx1'] = $kjnum[0] == '' ? '' : self::shengxiao($kjnum[0], $kj['bml'], 0);
                $kj['sx2'] = $kjnum[1] == '' ? '' : self::shengxiao($kjnum[1], $kj['bml'], 0);
                $kj['sx3'] = $kjnum[2] == '' ? '' : self::shengxiao($kjnum[2], $kj['bml'], 0);
                $kj['sx4'] = $kjnum[3] == '' ? '' : self::shengxiao($kjnum[3], $kj['bml'], 0);
                $kj['sx5'] = $kjnum[4] == '' ? '' : self::shengxiao($kjnum[4], $kj['bml'], 0);
                $kj['sx6'] = $kjnum[5] == '' ? '' : self::shengxiao($kjnum[5], $kj['bml'], 0);
                $kj['sx7'] = $kjnum[6] == '' ? '' : self::shengxiao($kjnum[6], $kj['bml'], 0);
                $kj['wh1'] = $kjnum[0] == '' ? '' : self::lhwuhang($kjnum[0], $kj['bml'], 0);
                $kj['wh2'] = $kjnum[1] == '' ? '' : self::lhwuhang($kjnum[1], $kj['bml'], 0);
                $kj['wh3'] = $kjnum[2] == '' ? '' : self::lhwuhang($kjnum[2], $kj['bml'], 0);
                $kj['wh4'] = $kjnum[3] == '' ? '' : self::lhwuhang($kjnum[3], $kj['bml'], 0);
                $kj['wh5'] = $kjnum[4] == '' ? '' : self::lhwuhang($kjnum[4], $kj['bml'], 0);
                $kj['wh6'] = $kjnum[5] == '' ? '' : self::lhwuhang($kjnum[5], $kj['bml'], 0);
                $kj['wh7'] = $kjnum[6] == '' ? '' : self::lhwuhang($kjnum[6], $kj['bml'], 0);
                break;
        }
        return $kj;
    }
    public static function getshuxing($flid, $kj, $cnum) {
        $ck = count($kj);
        if ($ck == 0) return $kj;
        for ($i = 0; $i < $ck; $i++) {
            $kj[$i]['ds'] = [];
            $kj[$i]['dx'] = [];
            $kj[$i]['lh'] = [];
            $num = $kj[$i]['num'];
            $cn = count($num);
            if ($cn != $cnum) continue;
            $dan = 0;
            $da = 0;
            for ($j = 0; $j < $cn; $j++) {
                $kj[$i]['ds'][] = self::danshuang($flid, $num[$j], 2);
                $kj[$i]['dx'][] = self::daxiao($flid, $num[$j], 2);
                if (($flid == 103 | $flid == 107) & $j < $cn / 2) {
                    $kj[$i]['lh'][] = self::longhuhe($flid, $num[$j], $num[$cn - $j - 1], 2);
                }
                if ($flid == 103 | $flid == 100) {
                    $kj[$i]['hds'][] = self::hedanshuang($flid, $num[$j], 2);
                    $kj[$i]['wdx'][] = self::weidaxiao($flid, $num[$j], 2);
                }
                if ($flid == 161) {
                    if ($num[$j] > 40) $da++;
                    if ($num[$j] % 2 == 1) $dan++;
                }
                if ($flid == 151) {
                    $kj[$i]['yxx'][] = self::yuxiaxie($num[$j]);
                }
            }
            if (count($kj[$i]['lh']) == 0) {
                if ($flid == 163) {
                    $kj[$i]['lh'][] = self::longhuhe($flid, $num[0], $num[$cn - 2], 2);
                } else {
                    $kj[$i]['lh'][] = self::longhuhe($flid, $num[0], $num[$cn - 1], 2);
                }
            }
            $kj[$i]['zonghe'][] = self::zonghe($flid, $num, 2);
            $kj[$i]['zonghe'][] = self::zonghedaxiao($flid, $kj[$i]['zonghe'][0], 2);
            $kj[$i]['zonghe'][] = self::danshuang($flid, $kj[$i]['zonghe'][0], 2);
            switch ($flid) {
                case 101:
                    $kj[$i]['zonghe'][] = self::qita($num[0], $num[1], $num[2]);
                    $kj[$i]['zonghe'][] = self::qita($num[1], $num[2], $num[3]);
                    $kj[$i]['zonghe'][] = self::qita($num[2], $num[3], $num[4]);
                    break;

                case 163:
                    $kj[$i]['zonghe'][] = self::qita($num[0], $num[1], $num[2]);
                    break;

                case 161:
                     $kj[$i]['zonghe'][] = self::wuhang($flid, $kj[$i]['zonghe'][0]);
                    if($da<10){
                        $kj[$i]['zonghe'][] = 0;
                    }else if($da>10){
                        $kj[$i]['zonghe'][] = 1;
                    }else{
                        $kj[$i]['zonghe'][] = 2;
                    }
                    if($dan<10){
                        $kj[$i]['zonghe'][] = 0;
                    }else if($dan>10){
                        $kj[$i]['zonghe'][] = 1;
                    }else{
                        $kj[$i]['zonghe'][] = 2;
                    }   
                    $kj[$i]['zonghe'][] = $dan;                 
                    break;

                case 103:
                    $kj[$i]['zonghe'][] = self::weidaxiao($flid, $kj[$i]['zonghe'][0], 2);
                    break;

                case 100:
                    $kj[$i]['sx'][] = $num[0] == '' ? '' : self::shengxiao($num[0], $kj[$i]['bml'], 1);
                    $kj[$i]['sx'][] = $num[1] == '' ? '' : self::shengxiao($num[1], $kj[$i]['bml'], 1);
                    $kj[$i]['sx'][] = $num[2] == '' ? '' : self::shengxiao($num[2], $kj[$i]['bml'], 1);
                    $kj[$i]['sx'][] = $num[3] == '' ? '' : self::shengxiao($num[3], $kj[$i]['bml'], 1);
                    $kj[$i]['sx'][] = $num[4] == '' ? '' : self::shengxiao($num[4], $kj[$i]['bml'], 1);
                    $kj[$i]['sx'][] = $num[5] == '' ? '' : self::shengxiao($num[5], $kj[$i]['bml'], 1);
                    $kj[$i]['sx'][] = $num[6] == '' ? '' : self::shengxiao($num[6], $kj[$i]['bml'], 1);
                    $kj[$i]['wh'][] = $num[0] == '' ? '' : self::lhwuhang($num[0], $kj[$i]['bml'], 1);
                    $kj[$i]['wh'][] = $num[1] == '' ? '' : self::lhwuhang($num[1], $kj[$i]['bml'], 1);
                    $kj[$i]['wh'][] = $num[2] == '' ? '' : self::lhwuhang($num[2], $kj[$i]['bml'], 1);
                    $kj[$i]['wh'][] = $num[3] == '' ? '' : self::lhwuhang($num[3], $kj[$i]['bml'], 1);
                    $kj[$i]['wh'][] = $num[4] == '' ? '' : self::lhwuhang($num[4], $kj[$i]['bml'], 1);
                    $kj[$i]['wh'][] = $num[5] == '' ? '' : self::lhwuhang($num[5], $kj[$i]['bml'], 1);
                    $kj[$i]['wh'][] = $num[6] == '' ? '' : self::lhwuhang($num[6], $kj[$i]['bml'], 1);
                    break;
            }
        }
        return $kj;
    }
    public static function danshuang($flid, $val, $i) {
        if ($flid == 100 & $val == 49) {
            $arr = ['和', '和', 2];
        } else if ($flid == 121 & $val == 11) {
            $arr = ['和', '和', 2];
        } else if ($val % 2 == 0) {
            $arr = ['双', '雙', 1];
        } else {
            $arr = ['单', '單', 0];
        }
        return $arr[$i];
    }
    public static function daxiao($flid, $val, $i) {
        $arr = [];
        switch ($flid) {
            case 163:
            case 101:
                if ($val >= 5) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;

            case 107:
                if ($val >= 6) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;

            case 103:
                if ($val >= 11) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;

            case 121:
                if ($val == 11) {
                    $arr = ['和', '和', 2];
                } else {
                    if ($val >= 6) {
                        $arr = ['大', '大', 1];
                    } else {
                        $arr = ['小', '小', 0];
                    }
                }
                break;

            case 151:
                if ($val >= 4) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;

            case 161:
                if ($val >= 41) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;

            case 100:
                if ($val == 49) {
                    $arr = ['和', '和', 2];
                } else {
                    if ($val >= 25) {
                        $arr = ['大', '大', 1];
                    } else {
                        $arr = ['小', '小', 0];
                    }
                }
                break;
        }
        return $arr[$i];
    }
    public static function longhuhe($flid, $v1, $v2, $i) {
        if ($v1 == $v2) {
            $arr = ['和', '和', 2];
        } else {
            if ($v1 > $v2) {
                $arr = ['龙', '龍', 1];
            } else {
                $arr = ['虎', '虎', 0];
            }
        }
        return $arr[$i];
    }
    public static function weidaxiao($flid, $val, $i) {
        $v = $val % 10;
        if ($flid == 100 & $val == 49) {
            $arr = ['和', '和', 2];
        } else if ($v >= 5) {
            $arr = ['大', '大', 1];
        } else {
            $arr = ['小', '小', 0];
        }
        return $arr[$i];
    }
    public static function hedanshuang($flid, $val, $i) {
        $v = $val % 10 + ($val - $val % 10) / 10;
        if ($flid == 100 & $val == 49) {
            $arr = ['和', '和', 2];
        } else if ($v % 2 == 0) {
            $arr = ['双', '雙', 1];
        } else {
            $arr = ['单', '單', 0];
        }
        return $arr[$i];
    }
    public static function zonghe($flid, $kj) {
        $zh = 0;
        if ($flid == 107) {
            $zh = $kj[0] + $kj[1];
        } else if ($flid == 163) {
            $zh = $kj[0] + $kj[1] + $kj[2];
        } else {
            $ck = count($kj);
            for ($i = 0; $i < $ck; $i++) {
                $zh+= $kj[$i];
            }
        }
        return $zh;
    }
    public static function weishu($v) {
        return $v % 10;
    }
    public static function toushu($v) {
        return floor($v / 10);
    }
    public static function heshu($v) {
        $v = $val % 10 + ($val - $val % 10) / 10;
        return $v;
    }
    public static function zonghedaxiao($flid, $val, $i) {
        $arr = [];
        switch ($flid) {
            case 163:
                if ($val >= 14) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;

            case 101:
                if ($val >= 23) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;

            case 107:
                if ($val >= 12) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;

            case 103:
                if ($val == 84) {
                    $arr = ['和', '和', 2];
                } else {
                    if ($val > 84) {
                        $arr = ['大', '大', 1];
                    } else {
                        $arr = ['小', '小', 0];
                    }
                }
                break;

            case 121:
                if ($val == 30) {
                    $arr = ['和', '和', 2];
                } else {
                    if ($val > 30) {
                        $arr = ['大', '大', 1];
                    } else {
                        $arr = ['小', '小', 0];
                    }
                }
                break;

            case 161:
                if ($val == 810) {
                    $arr = ['和', '和', 2];
                } else {
                    if ($val > 810) {
                        $arr = ['大', '大', 1];
                    } else {
                        $arr = ['小', '小', 0];
                    }
                }
                break;

            case 151:
                if ($val >= 11) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;

            case 100:
                if ($val >= 175) {
                    $arr = ['大', '大', 1];
                } else {
                    $arr = ['小', '小', 0];
                }
                break;
        }
        return $arr[$i];
    }
    public static function niuniudaxiao($v) {
        if ($v >= 1 & $v <= 5) {
            return '小';
        } else {
            return '大';
        }
    }
    public static function niuniu($arr) {
        $t1 = false;
        $t2 = false;
        $t3 = 0;
        for ($a = 0; $a <= 2; $a++) {
            for ($b = $a + 1; $b <= 3; $b++) {
                for ($c = $b + 1; $c <= 4; $c++) {
                    if (($arr[$a] + $arr[$b] + $arr[$c]) % 10 == 0) {
                        $t1 = true;
                        for ($j = 0; $j <= 4; $j++) {
                            if ($j != $a && $j != $b && $j != $c) {
                                $t3+= $arr[$j];
                            }
                        }
                        if ($t3 % 10 == 0) {
                            $t2 = true;
                        }
                    }
                }
            }
        }
        return [$t1, $t2, $t3 % 10];
    }
    public static function suoha($arr) {
        $r = 0;
        //散号
        $a = array();
        foreach ($arr as $v) {
            $a[$v]+= 1;
        }
        array_merge($a);
        $ca = count($a);
        switch ($ca) {
            case 1:
                $r = 1;
                //五梅
                break;

            case 2:
                sort($a);
                if ($a[0] == 1 | $a[1] == 1) {
                    $r = 2;
                    //炸弹
                    
                } else {
                    $r = 3;
                    //葫芦
                    
                }
                break;

            case 3:
                if ($a[0] == 3 | $a[1] == 3 | $a[2] == 3) {
                    $r = 4;
                    //三条
                    
                } else {
                    $r = 5;
                    //两对
                    
                }
                break;

            case 4:
                $r = 6;
                //单对
                break;

            case 5:
                sort($arr);
                if ($arr[4] - $arr[0] == 4) {
                    $r = 7;
                    //顺子
                    
                } else {
                    $kao1 = [1, 3, 5, 7, 9];
                    $kao2 = [0, 2, 4, 6, 8];
                    if ($arr == $kao1 | $arr == $kao2) {
                        $r = 8;
                        //五不靠
                        
                    }
                }
                break;
        }
        $arr = ["散号", "五梅", "炸弹", "葫芦", "三条", "两对", "单对", "顺子", "五不靠"];
        return $arr[$r];
    }
    public static function qita($v1, $v2, $v3) {
        $v = 9;
        if (self::baozhi($v1, $v2, $v3) == 1) {
            $v = 0;
        } else {
            if (self::shunzhi($v1, $v2, $v3) == 1) {
                $v = 1;
            } else {
                if (self::duizhi($v1, $v2, $v3) == 1) {
                    $v = 2;
                } else {
                    if (self::banshun($v1, $v2, $v3) == 1) {
                        $v = 3;
                    } else {
                        $v = 4;
                    }
                }
            }
        }
        $arr = ["豹子", "顺子", "对子", "半顺", "杂六"];
        return $arr[$v];
    }
    public static function duizhi($v1, $v2, $v3) {
        if ($v1 == $v2 | $v1 == $v3 | $v2 == $v3) {
            $v = 1;
        } else {
            $v = 0;
        }
        if ($v == 1) {
            $vv = self::baozhi($v1, $v2, $v3);
            if ($vv == 1) {
                $v = 0;
            }
        }
        return $v;
    }
    public static function baozhi($v1, $v2, $v3) {
        if ($v1 == $v2 & $v1 == $v3 & $v2 == $v3) {
            $v = 1;
        } else {
            $v = 0;
        }
        return $v;
    }
    public static function shunzhi($v1, $v2, $v3) {
        $vh = $v1 + $v2 + $v3;
        $v = 0;
        if ($vh % 3 == 0 & $v1 != $v2 & $v1 != $v3 & $v2 != $v3 & max($v1, $v2, $v3) - min($v1, $v2, $v3) == 2) {
            $v = 1;
        } else {
            if (strpos('[019]', $v1) != false & strpos('[019]', $v2) != false & strpos('[019]', $v3) != false & $v1 != $v2 & $v1 != $v3 & $v2 != $v3) {
                if ($v1 != $v2 & $v1 != $v3 & $v2 != $v3) {
                    $v = 1;
                }
            } else {
                if (strpos('[890]', $v1) != false & strpos('[890]', $v2) != false & strpos('[890]', $v3) != false & $v1 != $v2 & $v1 != $v3 & $v2 != $v3) {
                    if ($v1 != $v2 & $v1 != $v3 & $v2 != $v3) {
                        $v = 1;
                    }
                }
            }
        }
        return $v;
    }
    public static function banshun($v1, $v2, $v3) {
        $vh1 = abs($v1 - $v2);
        $vh2 = abs($v1 - $v3);
        $vh3 = abs($v2 - $v3);
        if (self::baozhi($v1, $v2, $v3) == 1) {
            $z = 0;
        } else {
            if (self::shunzhi($v1, $v2, $v3) == 1) {
                $z = 0;
            } else {
                if (self::duizhi($v1, $v2, $v3) == 1) {
                    $z = 0;
                } else {
                    if ($vh1 == 1 | $vh2 == 1 | $vh3 == 1) {
                        $z = 1;
                    } else {
                        if (strpos('[' . $v1 . $v2 . $v3 . ']', '0') != false & strpos('[' . $v1 . $v2 . $v3 . ']', '9') != false) {
                            $z = 1;
                        } else {
                            $z = 0;
                        }
                    }
                }
            }
        }
        return $z;
    }
    public static function zaliu($v1, $v2, $v3) {
        if (self::baozhi($v1, $v2, $v3) == 1) {
            $z = 0;
        } else {
            if (self::shunzhi($v1, $v2, $v3) == 1) {
                $z = 0;
            } else {
                if (self::duizhi($v1, $v2, $v3) == 1) {
                    $z = 0;
                } else {
                    if (self::banshun($v1, $v2, $v3) == 1) {
                        $z = 0;
                    } else {
                        $z = 1;
                    }
                }
            }
        }
        return $z;
    }
    public static function siji($v) {
        //if(strpos('anull',$v)) return '';
        if (in_array($v, [1, 2, 3, 4, 5])) {
            $v = '春';
        } else {
            if (in_array($v, [6, 7, 8, 9, 10])) {
                $v = '夏';
            } else {
                if (in_array($v, [11, 12, 13, 14, 15])) {
                    $v = '秋';
                } else {
                    if (in_array($v, [16, 17, 18, 19, 20])) {
                        $v = '冬';
                    }
                }
            }
        }
        return $v;
    }
    public static function wuhang($flid, $v) {
        if ($flid == 103) {
            if (in_array($v, [5, 10, 15, 20])) {
                $v = '金';
            } else {
                if (in_array($v, [1, 6, 11, 16])) {
                    $v = '木';
                } else {
                    if (in_array($v, [2, 7, 12, 17])) {
                        $v = '水';
                    } else {
                        if (in_array($v, [3, 8, 13, 18])) {
                            $v = '火';
                        } else {
                            if (in_array($v, [4, 9, 14, 19])) {
                                $v = '土';
                            }
                        }
                    }
                }
            }
        } else {
            if ($flid == 161) {
                if ($v <= 695) {
                    $v = '金';
                } else {
                    if ($v <= 763) {
                        $v = '木';
                    } else {
                        if ($v <= 855) {
                            $v = '水';
                        } else {
                            if ($v <= 923) {
                                $v = '火';
                            } else {
                                $v = '土';
                            }
                        }
                    }
                }
            }
        }
        return $v;
    }
    public static function fangwei($v) {
        if (in_array($v, [1, 5, 9, 13, 17])) {
            $v = '东';
        } else {
            if (in_array($v, [2, 6, 10, 14, 18])) {
                $v = '南';
            } else {
                if (in_array($v, [3, 7, 11, 15, 19])) {
                    $v = '西';
                } else {
                    if (in_array($v, [4, 8, 12, 16, 20])) {
                        $v = '北';
                    }
                }
            }
        }
        return $v;
    }
    public static function zhongfabai($v) {
        if (in_array($v, [1, 2, 3, 4, 5, 6, 7])) {
            $v = '中';
        } else {
            if (in_array($v, [8, 9, 10, 11, 12, 13, 14])) {
                $v = '发';
            } else {
                if (in_array($v, [15, 16, 17, 18, 19, 20])) {
                    $v = '白';
                }
            }
        }
        return $v;
    }
    public static function getlhshuxing($m, $cs, $i) {
        $index = 0;
        $arr = [];
        foreach ($cs as $k1 => $v1) {
            $tmp = explode(',', $v1);
            foreach ($tmp as $v2) {
                if ($v2 == $m) {
                    $arr = [$k1, $index];
                }
            }
            $index++;
        }
        return $arr[$i];
    }
    public static function yuxiaxie($val) {
        $arr = ['鱼', '虾', '葫芦', '金钱', '蟹', '鸡'];
        return $arr[$val - 1];
    }
    public static function rwuhang($wh, $bml) {
        $ny = ['金', '金', '火', '火', '木', '木', '土', '土', '金', '金', '火', '火', '水', '水', '土', '土', '金', '金', '木', '木', '水', '水', '土', '土', '火', '火', '木', '木', '水', '水', '金', '金', '火', '火', '木', '木', '土', '土', '金', '金', '火', '火', '水', '水', '土', '土', '金', '金', '木', '木', '水', '水', '土', '土', '火', '火', '木', '木', '水'];
        $jiazhi = ['甲子', '乙丑', '丙寅', '丁卯', '戊辰', '己巳', '庚午', '辛未', '壬申', '癸酉', '甲戌', '乙亥', '丙子', '丁丑', '戊寅', '己卯', '庚辰', '辛巳', '壬午', '癸未', '甲申', '乙酉', '丙戌', '丁亥', '戊子', '己丑', '庚寅', '辛卯', '壬辰', '癸巳', '甲午', '乙未', '丙申', '丁酉', '戊戌', '己亥', '庚子', '辛丑', '壬寅', '癸卯', '甲辰', '乙巳', '丙午', '丁未', '戊申', '己酉', '庚戌', '辛亥', '壬子', '癸丑', '甲寅', '乙卯', '丙辰', '丁巳', '戊午', '己未', '庚申', '辛酉', '壬戌', '癸亥'];
        $in = 0;
        foreach ($jiazhi as $key => $val) {
            if ($val == $bml) {
                $in = $key;
                break;
            }
        }
        $w = array();
        for ($i = 1; $i <= 49; $i++) {
            $index = 1922 - $i + $in - 1;
            if ($ny[$index % 60] == $wh) {
                $t = $i < 10 ? '0' + $i : $i;
                $w[] = $t;
            }
        }
        return $w;
    }
    public static function rlhwuhang($wh, $bml) {
        $ny = ['金', '金', '火', '火', '木', '木', '土', '土', '金', '金', '火', '火', '水', '水', '土', '土', '金', '金', '木', '木', '水', '水', '土', '土', '火', '火', '木', '木', '水', '水', '金', '金', '火', '火', '木', '木', '土', '土', '金', '金', '火', '火', '水', '水', '土', '土', '金', '金', '木', '木', '水', '水', '土', '土', '火', '火', '木', '木', '水'];
        $jiazhi = ['甲子', '乙丑', '丙寅', '丁卯', '戊辰', '己巳', '庚午', '辛未', '壬申', '癸酉', '甲戌', '乙亥', '丙子', '丁丑', '戊寅', '己卯', '庚辰', '辛巳', '壬午', '癸未', '甲申', '乙酉', '丙戌', '丁亥', '戊子', '己丑', '庚寅', '辛卯', '壬辰', '癸巳', '甲午', '乙未', '丙申', '丁酉', '戊戌', '己亥', '庚子', '辛丑', '壬寅', '癸卯', '甲辰', '乙巳', '丙午', '丁未', '戊申', '己酉', '庚戌', '辛亥', '壬子', '癸丑', '甲寅', '乙卯', '丙辰', '丁巳', '戊午', '己未', '庚申', '辛酉', '壬戌', '癸亥'];
        $in = 0;
        foreach ($jiazhi as $key => $val) {
            if ($val == $bml) {
                $in = $key;
                break;
            }
        }
        $w = array();
        for ($i = 1; $i <= 49; $i++) {
            $index = 1922 - $i + $in - 1;
            if ($ny[$index % 60] == $wh) {
                $t = $i < 10 ? '0' + $i : $i;
                $w[] = $t;
            }
        }
        return $w;
    }
    public static function lhwuhang($ma, $bml, $i) {
        $ny = ['金', '金', '火', '火', '木', '木', '土', '土', '金', '金', '火', '火', '水', '水', '土', '土', '金', '金', '木', '木', '水', '水', '土', '土', '火', '火', '木', '木', '水', '水', '金', '金', '火', '火', '木', '木', '土', '土', '金', '金', '火', '火', '水', '水', '土', '土', '金', '金', '木', '木', '水', '水', '土', '土', '火', '火', '木', '木', '水', '水'];
        $jiazhi = ['甲子', '乙丑', '丙寅', '丁卯', '戊辰', '己巳', '庚午', '辛未', '壬申', '癸酉', '甲戌', '乙亥', '丙子', '丁丑', '戊寅', '己卯', '庚辰', '辛巳', '壬午', '癸未', '甲申', '乙酉', '丙戌', '丁亥', '戊子', '己丑', '庚寅', '辛卯', '壬辰', '癸巳', '甲午', '乙未', '丙申', '丁酉', '戊戌', '己亥', '庚子', '辛丑', '壬寅', '癸卯', '甲辰', '乙巳', '丙午', '丁未', '戊申', '己酉', '庚戌', '辛亥', '壬子', '癸丑', '甲寅', '乙卯', '丙辰', '丁巳', '戊午', '己未', '庚申', '辛酉', '壬戌', '癸亥'];
        $in = 0;
        foreach ($jiazhi as $key => $val) {
            if ($val == $bml) {
                $in = $key;
                break;
            }
        }
        //return (1922 - $ma + $in-1)%60;
        if ($i == 0) {
            return $ny[(1922 - $ma + $in - 1) % 60];
        } else {
            return ((1922 - $ma + $in - 1) % 60) % 5;
        }
    }
    public static function shengxiao($ma, $bml, $i) {
        $jiazhi = ['甲子', '乙丑', '丙寅', '丁卯', '戊辰', '己巳', '庚午', '辛未', '壬申', '癸酉', '甲戌', '乙亥', '丙子', '丁丑', '戊寅', '己卯', '庚辰', '辛巳', '壬午', '癸未', '甲申', '乙酉', '丙戌', '丁亥', '戊子', '己丑', '庚寅', '辛卯', '壬辰', '癸巳', '甲午', '乙未', '丙申', '丁酉', '戊戌', '己亥', '庚子', '辛丑', '壬寅', '癸卯', '甲辰', '乙巳', '丙午', '丁未', '戊申', '己酉', '庚戌', '辛亥', '壬子', '癸丑', '甲寅', '乙卯', '丙辰', '丁巳', '戊午', '己未', '庚申', '辛酉', '壬戌', '癸亥'];
        $index = 0;
        foreach ($jiazhi as $key => $val) {
            if ($val == $bml) {
                $index = $key;
                break;
            }
        }
        $index = $index % 12 + 1;
        $ma = $ma % 12;
        $arr = ['鼠', '牛', '虎', '兔', '龍', '蛇', '馬', '羊', '猴', '雞', '狗', '豬'];
        $in = 0;
        if ($index >= $ma) {
            $in = $index - $ma;
        } else {
            $in = $index - $ma + 12;
        }
        if ($in >= 12) $in-= 12;
        if ($i == 0) {
            return $arr[$in];
        } else {
            return $in;
        }
    }
    public static function rshengxiao($sx, $bml) {
        $jiazhi = ['甲子', '乙丑', '丙寅', '丁卯', '戊辰', '己巳', '庚午', '辛未', '壬申', '癸酉', '甲戌', '乙亥', '丙子', '丁丑', '戊寅', '己卯', '庚辰', '辛巳', '壬午', '癸未', '甲申', '乙酉', '丙戌', '丁亥', '戊子', '己丑', '庚寅', '辛卯', '壬辰', '癸巳', '甲午', '乙未', '丙申', '丁酉', '戊戌', '己亥', '庚子', '辛丑', '壬寅', '癸卯', '甲辰', '乙巳', '丙午', '丁未', '戊申', '己酉', '庚戌', '辛亥', '壬子', '癸丑', '甲寅', '乙卯', '丙辰', '丁巳', '戊午', '己未', '庚申', '辛酉', '壬戌', '癸亥'];
        $index = 0;
        foreach ($jiazhi as $key => $val) {
            if ($val == $bml) {
                $index = $key;
                break;
            }
        }
        $index = $index % 12 + 1;
        switch ($sx) {
            case '鼠':
                $i = 1;
                break;

            case '牛':
                $i = 2;
                break;

            case '虎':
                $i = 3;
                break;

            case '兔':
                $i = 4;
                break;

            case '龍':
                $i = 5;
                break;

            case '蛇':
                $i = 6;
                break;

            case '馬':
                $i = 7;
                break;

            case '羊':
                $i = 8;
                break;

            case '猴':
                $i = 9;
                break;

            case '雞':
                $i = 10;
                break;

            case '狗':
                $i = 11;
                break;

            case '豬':
                $i = 12;
                break;
        }
        if ($index >= $i) {
            $arr[0] = $index - $i + 1;
        } else {
            $arr[0] = $index - $i + 13;
        }
        $arr[1] = $arr[0] + 12;
        $arr[2] = $arr[0] + 24;
        $arr[3] = $arr[0] + 36;
        if ($index == $i) {
            $arr[4] = $arr[0] + 48;
        }
        if ($arr[0] < 10) {
            $arr[0] = '0' + $arr[0];
        }
        return $arr;
    }
    public static function rlhma($v2) {
    }
    public static function getlhma($v) {
        $ma['單'] = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29, 31, 33, 35, 37, 39, 41, 43, 45, 47, 49];
        $ma['雙'] = [2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36, 38, 40, 42, 44, 46, 48];
        $ma['单'] = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29, 31, 33, 35, 37, 39, 41, 43, 45, 47, 49];
        $ma['双'] = [2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36, 38, 40, 42, 44, 46, 48];
        $ma['大'] = [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49];
        $ma['小'] = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24];
        $ma['合單'] = [1, 3, 5, 7, 9, 10, 12, 14, 16, 18, 21, 23, 25, 27, 29, 30, 32, 34, 36, 38, 41, 43, 45, 47, 49];
        $ma['合雙'] = [2, 4, 6, 8, 11, 13, 15, 17, 19, 20, 22, 24, 26, 28, 31, 33, 35, 37, 39, 40, 42, 44, 46, 48];
        $ma['尾大'] = [5, 6, 7, 8, 9, 15, 16, 17, 18, 19, 25, 26, 27, 28, 29, 35, 36, 37, 38, 39, 45, 46, 47, 48, 49];
        $ma['尾小'] = [1, 2, 3, 4, 10, 11, 12, 13, 14, 20, 21, 22, 23, 24, 30, 31, 32, 33, 34, 40, 41, 42, 43, 44];
        $ma['合小'] = [1, 2, 3, 4, 5, 6, 10, 11, 12, 13, 14, 15, 20, 21, 22, 23, 24, 30, 31, 32, 33, 40, 41, 42];
        $ma['合大'] = [7, 8, 9, 16, 17, 18, 19, 25, 26, 27, 28, 29, 34, 35, 36, 37, 38, 39, 43, 44, 45, 46, 47, 48, 49];
        $ma['紅'] = [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46];
        $ma['藍'] = [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48];
        $ma['綠'] = [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49];
        $ma['紅單'] = [1, 7, 13, 19, 23, 29, 35, 45];
        $ma['紅雙'] = [2, 8, 12, 18, 24, 30, 34, 40, 46];
        $ma['紅大'] = [29, 30, 34, 35, 40, 45, 46];
        $ma['紅小'] = [1, 2, 7, 8, 12, 13, 18, 19, 23, 24];
        $ma['藍單'] = [3, 9, 15, 25, 31, 37, 41, 47];
        $ma['藍雙'] = [4, 10, 14, 20, 26, 36, 42, 48];
        $ma['藍大'] = [25, 26, 31, 36, 37, 41, 42, 47, 48];
        $ma['藍小'] = [3, 4, 9, 10, 14, 15, 20];
        $ma['綠單'] = [5, 11, 17, 21, 27, 33, 39, 43, 49];
        $ma['綠雙'] = [6, 16, 22, 28, 32, 38, 44];
        $ma['綠大'] = [27, 28, 32, 33, 38, 39, 43, 44, 49];
        $ma['綠小'] = [5, 6, 11, 16, 17, 21, 22];
        $ma['1头'] = [10, 11, 12, 13, 14, 15, 16, 17, 18, 19];
        $ma['2头'] = [20, 21, 22, 23, 24, 25, 26, 27, 28, 29];
        $ma['3头'] = [30, 31, 32, 33, 34, 35, 36, 37, 38, 39];
        $ma['4头'] = [40, 41, 42, 43, 44, 45, 46, 47, 48, 49];
        $ma['0头'] = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $ma['1尾'] = [1, 11, 21, 31, 41];
        $ma['2尾'] = [2, 12, 22, 32, 42];
        $ma['3尾'] = [3, 13, 23, 33, 43];
        $ma['4尾'] = [4, 14, 24, 34, 44];
        $ma['5尾'] = [5, 15, 25, 35, 45];
        $ma['6尾'] = [6, 16, 26, 36, 46];
        $ma['7尾'] = [7, 17, 27, 37, 47];
        $ma['8尾'] = [8, 18, 28, 38, 48];
        $ma['9尾'] = [9, 19, 29, 39, 49];
        $ma['0尾'] = [10, 20, 30, 40];
        $ma['内围'] = [9, 10, 11, 12, 13, 16, 17, 18, 19, 20, 23, 24, 25, 26, 27, 30, 31, 32, 33, 34, 37, 38, 39, 40, 41];
        $ma['外围'] = [1, 2, 3, 4, 5, 6, 7, 8, 14, 15, 21, 22, 28, 29, 35, 36, 42, 43, 44, 45, 46, 47, 48, 49];
        $ma['家'] = ['牛', '馬', '羊', '雞', '狗', '豬'];
        $ma['野'] = ['鼠', '虎', '兔', '龍', '蛇', '猴'];
        $ma['前'] = ['鼠', '牛', '虎', '兔', '龍', '蛇'];
        $ma['後'] = ['馬', '羊', '猴', '雞', '狗', '豬'];
        $ma['男'] = ['兔', '蛇', '羊', '雞', '豬'];
        $ma['女'] = ['鼠', '牛', '虎', '龍', '馬', '猴', '狗'];
        $ma['天'] = ['兔', '马', '猴', '猪', '牛', '龙'];
        $ma['地'] = ['蛇', '羊', '鸡', '狗', '鼠', '虎'];
        $ma["鼠龍猴"] = ["鼠", "龍", "猴"];
        $ma["牛蛇雞"] = ["牛", "蛇", "雞"];
        $ma["虎馬狗"] = ["虎", "馬", "狗"];
        $ma["兔羊豬"] = ["兔", "羊", "豬"];
        $ma["鼠牛"] = ["鼠", "牛"];
        $ma["虎豬"] = ["虎", "豬"];
        $ma["兔狗"] = ["兔", "狗"];
        $ma["龍雞"] = ["龍", "雞"];
        $ma["蛇猴"] = ["蛇", "猴"];
        $ma["馬羊"] = ["馬", "羊"];
        if ($v != '') {
            return $ma[$v];
        } else {
            return $ma;
        }
    }
    public static function chu($v, $para) {
        return $v % $para;
    }
    public static function duan($v, $para) {
        if ($para == 7) {
            return floor($v / $para) + 1;
        } else if ($para == 3) {
            if ($v <= 16) $duan = 1;
            else if ($v <= 32) $duan = 2;
            else $duan = 3;
            return $duan;
        } else if ($para == 4) {
            if ($v <= 12) $duan = 1;
            else if ($v <= 24) $duan = 2;
            else if ($v <= 36) $duan = 3;
            else $duan = 4;
            return $duan;
        }
    }
}

