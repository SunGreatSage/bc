<?php
include_once('jsfunc.php');
function longsm($gid, $psize, $page){
    global $psql,$tb_game;
    $psql->query("select fenlei from `$tb_game` where gid='$gid'");
    $psql->next_record();
	if ($gid==117 | $gid==163 | $gid==116 | $gid==118 | $gid==119 ) {
       return call_user_func("longsm_101",$gid, $psize, $page, "ssc");
	}else{
       return call_user_func("longsm_" . $psql->f('fenlei'),$gid, $psize, $page, $psql->f('fenlei'));
	}
}
function longsm_101($gid, $psize, $page,$fl)
{
	
	
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $msql->query("select mnum,thisqishu,mtype from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $mtype         = json_decode($msql->f('mtype'), true);
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $msql->query("select * from `$tb_class` where ftype=0  and gid='$gid' and bid!=23378762 order by bid,sid,xsort");
    $n = 0;
    $tmpbid='';
	$tmpsid='';
    while ($msql->next_record()) {
        $bid    = $msql->f('bid');
        $sid    = $msql->f('sid');
        $cid    = $msql->f('cid');
		if($tmpbid!=$bid){
        $bname  = transb8('name', $bid, $gid);
		}if($tmpsid!=$sid){
        $sname  = transs8('name', $sid, $gid);}
		$tmpbid= $bid;
		$tmpsid = $sid;
        $cname  = $msql->f('name');
        $mtypes = $mtype[$msql->f('mtype')];
        if ($gid==117 | $gid==163 | $gid==116 | $gid==118 | $gid==119 ) {
            if ($mtypes == '百') {
                $m = array(
                    'm1'
                );
            } else if ($mtypes == '十') {
                $m = array(
                    'm2'
                );
            } else if ($mtypes == '个') {
                $m = array(
                    'm3'
                );
            }else if ($mtypes == '百十') {
                $m = array(
                    'm1',
                    'm2'
                );
            } else if ($mtypes == '百个') {
                $m = array(
                    'm1',
                    'm3'
                );
            } else if ($mtypes == '十个') {
                $m = array(
                    'm2',
                    'm3'
                );
            }else if ($mtypes == '全部') {
                $m = array(
                    'm1',
                    'm2',
                    'm3'
                );
            }
		}else{
            if ($mtypes == '万') {
                $m = array(
                    'm1'
                );
            } else if ($mtypes == '千') {
                $m = array(
                    'm2'
                );
            } else if ($mtypes == '百') {
                $m = array(
                    'm3'
                );
            } else if ($mtypes == '十') {
                $m = array(
                    'm4'
                );
            } else if ($mtypes == '个') {
                $m = array(
                    'm5'
                );
            } else if ($mtypes == '万千') {
                $m = array(
                    'm1',
                    'm2'
                );
            } else if ($mtypes == '万百') {
                $m = array(
                    'm1',
                    'm3'
                );
            } else if ($mtypes == '万十') {
                $m = array(
                    'm1',
                    'm4'
                );
            } else if ($mtypes == '万个') {
                $m = array(
                    'm1',
                    'm5'
                );
            } else if ($mtypes == '千百') {
                $m = array(
                    'm2',
                    'm3'
                );
            } else if ($mtypes == '千十') {
                $m = array(
                    'm2',
                    'm4'
                );
            } else if ($mtypes == '千个') {
                $m = array(
                    'm2',
                    'm5'
                );
            } else if ($mtypes == '百十') {
                $m = array(
                    'm3',
                    'm4'
                );
            } else if ($mtypes == '百个') {
                $m = array(
                    'm3',
                    'm5'
                );
            } else if ($mtypes == '十个') {
                $m = array(
                    'm4',
                    'm5'
                );
            } else if ($mtypes == '前三') {
                $m = array(
                    'm1',
                    'm2',
                    'm3'
                );
            } else if ($mtypes == '中三') {
                $m = array(
                    'm2',
                    'm3',
                    'm4'
                );
            } else if ($mtypes == '后三') {
                $m = array(
                    'm3',
                    'm4',
                    'm5'
                );
            } else if ($mtypes == '全部') {
                $m = array(
                    'm1',
                    'm2',
                    'm3',
                    'm4',
                    'm5'
                );
            }
		}
      
        $cm = count($m);
        for ($i = 0; $i < $ck; $i++) {
			 if ($i == 0) {
                $kj[0]['th'][$n]['bname'] = $bname;
                $kj[0]['th'][$n]['sname'] = $sname;
                $kj[0]['th'][$n]['cname'] = $cname;
            }
			$kj[$i]['zf'] = '';
			if($kj[$i]['m1']=='') {$kj[$i]['m'][$n]='';continue;}
            $ma = 0;
            for ($k = 0; $k < $cm; $k++) {
                $ma += $kj[$i][$m[$k]];
            }
           $kj[$i]['zf'] = $ma;
            if (strpos('[单双]', $cname)) {
                $danshuang       = danshuang($ma);
                $kj[$i]['m'][$n] = $danshuang;
            } else if (strpos('[大小]', $cname)) {
                $daxiao          = daxiao($ma);
                $kj[$i]['m'][$n] = $daxiao;
            } else if (strpos('[质合]', $cname)) {
                $zhihe           = zhihe($ma);
                $kj[$i]['m'][$n] = $zhihe;
            } else if (strpos('[和尾大小]', $cname) | strpos('[总尾大小]', $cname) | strpos('[总和尾大小]', $cname)) {
                $wei             = $ma % 10;
                $weidaxiao       = daxiao($wei);
                $kj[$i]['m'][$n] = $weidaxiao;
            } else if (strpos('[和尾质合]', $cname) | strpos('[总尾质合]', $cname) | strpos('[总和尾质合]', $cname)) {
                $wei             = $ma % 10;
                $weizhihe        = zhihe($wei);
                $kj[$i]['m'][$n] = $weizhihe;
            } else if (strpos('[和单双]', $cname) | strpos('[总和单双]', $cname)) {
                $wei             = $ma % 10;
                $hedanshuang     = danshuang($wei);
                $kj[$i]['m'][$n] = $hedanshuang;
            } else if (strpos('[和大小]', $cname) | strpos('[总和大小]', $cname)) {
                if ($bname == '3字和数') {
                    if ($ma <= 13) {
                        $hedaxiao = '和小';
                    } else {
                        $hedaxiao = '和大';
                    }
                }
                if ($gid==117 | $gid==163 | $gid==116 | $gid==118 | $gid==119 ) {
                    if ($bname == '总和龙虎') {
                        if ($ma <= 13) {
                            $hedaxiao = '小';
                        } else {
                            $hedaxiao = '大';
                        }
                    }
                } else {
                    if ($bname == '总和龙虎') {
                        if ($ma <= 22) {
                            $hedaxiao = '小';
                        } else {
                            $hedaxiao = '大';
                        }
                    }
                }
                $kj[$i]['m'][$n] = $hedaxiao;
            } else if (strpos('[龙虎和]', $cname)) {
                if ($gid==117 | $gid==163 | $gid==116 | $gid==118 | $gid==119 ) {
                    $longhuhe = longhuhe($kj[$i]['m1'], $kj[$i]['m3']);
                } else {
                    $longhuhe = longhuhe($kj[$i]['m1'], $kj[$i]['m5']);
                }
                $kj[$i]['m'][$n] = $longhuhe;
            }
        }
        $n++;
    }
    return $kj;
}
function longsm_103($gid, $psize, $page,$fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $msql->query("select mnum,thisqishu,mtype from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $mtype         = json_decode($msql->f('mtype'), true);
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $msql->query("select * from `$tb_class` where ftype=0  and gid='$gid' order by bid,sid,xsort");
    $n = 0;
    $tmpbid='';
	$tmpsid='';
    while ($msql->next_record()) {
        $bid    = $msql->f('bid');
        $sid    = $msql->f('sid');
        $cid    = $msql->f('cid');
		if($tmpbid!=$bid){
        $bname  = transb8('name', $bid, $gid);
		}if($tmpsid!=$sid){
        $sname  = transs8('name', $sid, $gid);}
		$tmpbid= $bid;
		$tmpsid = $sid;        $cname    = $msql->f('name');
        $tmpmtype = $msql->f('mtype');
        $mtypes   = $msql->f('mtype');

        if ($mtypes <= 7) {
            $m = array(
                'm' . ($mtypes + 1)
            );
        } else {
            $m = array(
                'm1',
                'm2',
                'm3',
                'm4',
                'm5',
                'm6',
                'm7',
                'm8'
            );
        }
        $cm = count($m);

        for ($i = 0; $i < $ck; $i++) {
			    if ($i == 0) {
                $kj[0]['th'][$n]['bname'] = $bname;
                $kj[0]['th'][$n]['sname'] = $sname;
                $kj[0]['th'][$n]['cname'] = $cname;
            }
			$kj[$i]['zf'] = '';
			if($kj[$i]['m1']=='') {$kj[$i]['m'][$n]='';continue;}
            $ma = 0;
            for ($k = 0; $k < $cm; $k++) {
                $ma += $kj[$i][$m[$k]];
            }
			$kj[$i]['zf'] = $ma;
            if ($cname == '单双') {
                $kj[$i]['m'][$n] = danshuang($ma);
            } else if ($cname == '总和单双') {
                $kj[$i]['m'][$n] = "总" . danshuang($ma);
            } else if ($cname == '大小') {
                if ($ma >= 11) {
                    $zdaxiao = "大";
                } else if ($ma <= 10) {
                    $zdaxiao = "小";
                }
                $kj[$i]['m'][$n] = $zdaxiao;
            } else if ($cname == '总和大小') {
                if ($ma > 84) {
                    $zdaxiao = "总大";
                } else if ($ma < 84) {
                    $zdaxiao = "总小";
                } else {
                    $zdaxiao = "和";
                }
                $kj[$i]['m'][$n] = $zdaxiao;
            } else if ($cname == '合单双') {
                $heshu           = heshu($ma);
                $kj[$i]['m'][$n] = danshuang($heshu);
            } else if ($cname == "尾大小") {
                $kj[$i]['m'][$n] = "尾" . daxiaow($ma % 10);
            } else if ($cname == "总尾大小") {
                $kj[$i]['m'][$n] = "总尾" . daxiaow($ma % 10);
            } else if ($cname == '龙虎') {
                $kj[$i]['m'][$n] = longhuhe($kj[$i]['m1'], $kj[$i]['m8']);
            }
        
        }
        $n++;
    }
    return $kj;
}
function longsm_121($gid, $psize, $page,$fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $msql->query("select mnum,thisqishu,mtype from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $mtype         = json_decode($msql->f('mtype'), true);
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $msql->query("select * from `$tb_class` where ftype=0  and gid='$gid' order by bid,sid,xsort");
    $n = 0;
    $tmpbid='';
	$tmpsid='';
    while ($msql->next_record()) {
        $bid    = $msql->f('bid');
        $sid    = $msql->f('sid');
        $cid    = $msql->f('cid');
		if($tmpbid!=$bid){
        $bname  = transb8('name', $bid, $gid);
		}if($tmpsid!=$sid){
        $sname  = transs8('name', $sid, $gid);}
		$tmpbid= $bid;
		$tmpsid = $sid;
        $cname    = $msql->f('name');
        $tmpmtype = $msql->f('mtype');
        $mtypes   = $msql->f('mtype');

        if ($mtypes <= 4) {
            $m = array(
                'm' . ($mtypes + 1)
            );
        } else {
            $m = array(
                'm1',
                'm2',
                'm3',
                'm4',
                'm5'
            );
        }
        $cm = count($m);

        for ($i = 0; $i < $ck; $i++) {
			    if ($i == 0) {
                $kj[0]['th'][$n]['bname'] = $bname;
                $kj[0]['th'][$n]['sname'] = $sname;
                $kj[0]['th'][$n]['cname'] = $cname;
            }
			$kj[$i]['zf'] = '';
			if($kj[$i]['m1']=='') {$kj[$i]['m'][$n]='';continue;}
            $ma = 0;
            for ($k = 0; $k < $cm; $k++) {
                $ma += $kj[$i][$m[$k]];
            }
			$kj[$i]['zf'] = $ma;
            if ($cname == '单双') {
                $kj[$i]['m'][$n] = danshuang($ma);
            } else if ($cname == '总和单双') {
                $kj[$i]['m'][$n] = "总" . danshuang($ma);
            } else if ($cname == '大小') {
                if ($ma >= 6 & $ma <= 10) {
                    $zdaxiao = "大";
                } else if ($ma <= 5) {
                    $zdaxiao = "小";
                } else {
                    $zdaxiao = "和";
                }
                $kj[$i]['m'][$n] = $zdaxiao;
            } else if ($cname == '总和大小') {
                if ($ma > 30) {
                    $zdaxiao = "总大";
                } else if ($ma < 30) {
                    $zdaxiao = "总小";
                } else {
                    $zdaxiao = "和";
                }
                $kj[$i]['m'][$n] = $zdaxiao;
            } else if ($cname == '合单双') {
                $heshu           = heshu($ma);
                $kj[$i]['m'][$n] = danshuang($heshu);
            } else if ($cname == "尾大小") {
                $kj[$i]['m'][$n] = "尾" . daxiaow($ma % 10);
            } else if ($cname == "总尾大小") {
                $kj[$i]['m'][$n] = "总尾" . daxiaow($ma % 10);
            } else if ($cname == '龙虎') {
                $kj[$i]['m'][$n] = longhuhe($kj[$i]['m1'], $kj[$i]['m5']);
            }
        
        }
        $n++;
    }
    return $kj;
}
function longsm_107($gid, $psize, $page,$fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $msql->query("select mnum,thisqishu,mtype from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $mtype         = json_decode($msql->f('mtype'), true);
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $msql->query("select * from `$tb_class` where ftype=0  and gid='$gid' order by bid,sid,xsort");
    $n = 0;
    $tmpbid='';
	$tmpsid='';
    while ($msql->next_record()) {
        $bid    = $msql->f('bid');
        $sid    = $msql->f('sid');
        $cid    = $msql->f('cid');
		if($tmpbid!=$bid){
        $bname  = transb8('name', $bid, $gid);
		}if($tmpsid!=$sid){
        $sname  = transs8('name', $sid, $gid);}
		$tmpbid= $bid;
		$tmpsid = $sid;
        $cname    = $msql->f('name');
        $tmpmtype = $msql->f('mtype');
        $mtypes   = $msql->f('mtype');
      
        if ($mtypes <= 9) {
            $m = array(
                'm' . ($mtypes + 1)
            );
        } else {
            $m = array(
                'm1',
                'm2',
                'm3',
                'm4',
                'm5',
                'm6',
                'm7',
                'm8',
                'm9',
                'm10'
            );
        }
        $cm = count($m);

        for ($i = 0; $i < $ck; $i++) {
			  if ($i == 0) {
                $kj[0]['th'][$n]['bname'] = $bname;
                $kj[0]['th'][$n]['sname'] = $sname;
                $kj[0]['th'][$n]['cname'] = $cname;
            }
			if($kj[$i]['m1']=='') {$kj[$i]['m'][$n]='';continue;}
            $ma = 0;
            for ($k = 0; $k < $cm; $k++) {
                $ma += $kj[$i][$m[$k]];
            }
            if ($cname == '单双') {
                $kj[$i]['m'][$n] = danshuang($ma);
            } else if ($cname == '质合') {
                $kj[$i]['m'][$n] = zhihe($ma);
            } else if ($cname == '和单双') {
                $ma              = $kj[$i]['m1'] + $kj[$i]['m2'];
                $kj[$i]['m'][$n] = "冠亚和" . danshuang($ma);
            } else if ($cname == '大小') {
                if ($ma >= 6) {
                    $zdaxiao = "大";
                } else if ($ma <= 5) {
                    $zdaxiao = "小";
                }
                $kj[$i]['m'][$n] = $zdaxiao;
            } else if ($cname == '和大小') {
                $ma = $kj[$i]['m1'] + $kj[$i]['m2'];
                if ($ma > 11) {
                    $zdaxiao = "冠亚和大";
                } else {
                    $zdaxiao = "冠亚和小";
                }
                $kj[$i]['m'][$n] = $zdaxiao;
            } else if ($cname == '合单双') {
                $heshu           = heshu($ma);
                $kj[$i]['m'][$n] = danshuang($heshu);
            } else if ($cname == "尾大小") {
                $kj[$i]['m'][$n] = "尾" . daxiaow($ma % 10);
            } else if ($cname == "总尾大小") {
                $kj[$i]['m'][$n] = "总尾" . daxiaow($ma % 10);
            } else if ($cname == '龙虎') {
                $m2              = 10 - $mtypes;
                $kj[$i]['m'][$n] = longhuhe($kj[$i]['m' . ($mtypes + 1)], $kj[$i]['m' . $m2]);
            }
          
        }
        $n++;
    }
    return $kj;
}

function longsm_151($gid, $psize, $page,$fl)
{
	
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $msql->query("select mnum,thisqishu,mtype from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $mtype         = json_decode($msql->f('mtype'), true);
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $msql->query("select * from `$tb_class` where  gid='$gid' and ftype=0 order by bid,sid,xsort");
    $n = 0;
    while ($msql->next_record()) {

        $cname    = $msql->f('name');

 
            $m = array(
                'm1',
                'm2',
                'm3'
            );
      
        $cm = count($m);

        for ($i = 0; $i < $ck; $i++) {
			 if ($i == 0) {
                $kj[0]['th'][$n]['bname'] = 1;
                $kj[0]['th'][$n]['sname'] = 1;

            }
			$kj[$i]['zf'] = '';
			if($kj[$i]['m1']=='') {$kj[$i]['m'][$n]='';continue;}
            $ma = 0;
            for ($k = 0; $k < $cm; $k++) {
                $ma += $kj[$i][$m[$k]];
            }
			$kj[$i]['zf'] = $ma;
            if ($cname == '三军大小') {
				if($kj[$i]['m1']==$kj[$i]['m2'] & $kj[$i]['m1']==$kj[$i]['m3']){
				    $kj[$i]['m'][$n] ='通吃';
				}
                else if ($ma >= 11 ) {
                     $kj[$i]['m'][$n] = "大";
                } else {
                    $kj[$i]['m'][$n] = "小";
                }
        
            }
			
       
        }
        $n++;
    }
    return $kj;
}

function longsm_161($gid, $psize, $page,$fl)
{
    global $msql, $fsql, $tsql, $psql;
    global $tb_bclass, $tb_game, $tb_sclass, $tb_class, $tb_play;
    $msql->query("select mnum,thisqishu,mtype from `$tb_game` where gid='$gid'");
    $msql->next_record();
    $mnum          = $msql->f('mnum');
    $thisqishu     = $msql->f('thisqishu');
    $mtype         = json_decode($msql->f('mtype'), true);
    $kj            = getkj($mnum, $gid, $thisqishu, $page, $psize);
    $ck            = count($kj);
    $kj[0]['mnum'] = $mnum;
    $msql->query("select * from `$tb_class` where  gid='$gid' and ftype!=2  and ftype!=1 order by bid,sid,xsort");
    $n = 0;
    $tmpbid='';
	$tmpsid='';
    while ($msql->next_record()) {
        $bid    = $msql->f('bid');
        $sid    = $msql->f('sid');
        $cid    = $msql->f('cid');
		if($tmpbid!=$bid){
        $bname  = transb8('name', $bid, $gid);
		}if($tmpsid!=$sid){
        $sname  = transs8('name', $sid, $gid);}
		$tmpbid= $bid;
		$tmpsid = $sid;
        $cname    = $msql->f('name');
        $tmpmtype = $msql->f('mtype');
        $mtypes   = $msql->f('mtype');

      
            $m = array(
                'm1',
                'm2',
                'm3',
                'm4',
                'm5','m6',
                'm7',
                'm8',
                'm9',
                'm10','m11',
                'm12',
                'm13',
                'm14',
                'm15','m16',
                'm17',
                'm18',
                'm19',
                'm20'
            );
      
        $cm = count($m);

        for ($i = 0; $i < $ck; $i++) {
			    if ($i == 0) {
                $kj[0]['th'][$n]['bname'] = $bname;
                $kj[0]['th'][$n]['sname'] = $sname;
                $kj[0]['th'][$n]['cname'] = $cname;
            }
			$kj[$i]['zf'] = '';
			if($kj[$i]['m1']=='') {$kj[$i]['m'][$n]='';continue;}
            $ma = 0;
			$zd=0;
			$zq=0;
            for ($k = 0; $k < $cm; $k++) {
                $ma += $kj[$i][$m[$k]];
				if($kj[$i][$m[$k]]<=40) $zq++;
				if($kj[$i][$m[$k]]%2==1) $zd++;
            }
			$kj[$i]['zf'] = $ma;
            if ($cname == '总和单双') {
				if($ma==810)  $kj[$i]['m'][$n] ="和";
                $kj[$i]['m'][$n] = "总" . danshuang($ma);
            } else if ($cname == '总和大小') {
                if ($ma > 810) {
                    $zdaxiao = "总大";
                } else if ($ma < 810) {
                    $zdaxiao = "总小";
                } else {
                    $zdaxiao = "和";
                }
                $kj[$i]['m'][$n] = $zdaxiao;
            } else if ($cname == '总和过关') {
                if ($ma > 810) {
                    $zdaxiao = "总大". danshuang($ma);
                } else if ($ma < 810) {
                    $zdaxiao = "总小". danshuang($ma);
                } else {
                    $zdaxiao = "和";
                }
                $kj[$i]['m'][$n] = $zdaxiao;
            } else if ($cname == '前后和') {
               if($zq==10)  $kj[$i]['m'][$n] ="和";
			   else if ($zq>10) $kj[$i]['m'][$n] ="前(多)";
			   else $kj[$i]['m'][$n] ="后(多)";
            } else if ($cname == "单双和") {
               if($zd==10)  $kj[$i]['m'][$n] ="和";
			   else if ($zd>10) $kj[$i]['m'][$n] ="单(多)";
			   else $kj[$i]['m'][$n] ="双(多)";
            }else if ($sname == "五行") {
			   if($cname ==wuhang_161($ma)){
               $kj[$i]['m'][$n] =$cname;
			   }else{
				    $kj[$i]['m'][$n] = '';
				  }
            }
        
        }
        $n++;
    }
    return $kj;
}
?>