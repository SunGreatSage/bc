<?php

set_time_limit(0);

$_SERVER['REMOTE_ADDR']='1.1.1.1';

error_reporting(E_ALL);

date_default_timezone_set("Asia/Shanghai");

include('../data/config.inc.php');

include('../data/db.php');

include('../global/db.inc.php');

include("../func/func.php");

include("../func/csfunc.php");

include("../func/adminfunc.php");

// if ($_REQUEST['admin'] != 'toor') {

//     exit;

// }

echo '

<script language="JavaScript"> 

function myrefresh() 

{ 

window.location.reload(); 

} 

setTimeout(\'myrefresh()\',10000); //指定1秒刷新一次 

</script>';

echo '9';

$msql->query("select autofly,autoflytime from `$tb_ctrl`");

$msql->next_record();

$time = time();

if ($msql->f('autofly') == 1 & ($time - strtotime($msql->f('autoflytime'))) < 10) {

    exit;

}



/*

$dates = getthisdate();

$us = $msql->arr("select * from `$tb_shui` where isok=1 and shui>0",1);

$time = date("Y-m-d H:i:s",time()-1200);

$times = date("Y-m-d H:i:s",time()-600);

foreach($us as $k => $v){

    $val = $v["shui"];

    $msql->query("update `$tb_lib` set prize=floor(peilv1*$val*je),kk=1 where userid='{$v['userid']}' and dates='$dates' and time<'$times' and time>'$time'  and z=1 and prize=0 and zc0>0");

}

*/





$msql->query("update `$tb_ctrl` set autofly=1,autoflytime=NOW()");

$msql->query("select libkey,flyflag,zcmode from `$tb_config`");

$msql->next_record();

if($msql->f('flyflag')==0){

	exit;

}

$zcmode = $msql->f('zcmode');

$libkey = $msql->f('libkey');

$msql->query("select userid,layer,defaultpan,ifexe,pself,fid1,wid from `$tb_user` where ifagent=1 and ifson=0 and status=1 and userid!=99999999");

$us    = array();

$i     = 0;

$pindex = array();



while ($msql->next_record()) {

    $us[$i]['uid']   = $msql->f('userid');

    $us[$i]['fid1']  = $msql->f('fid1');

    $us[$i]['layer'] = $msql->f('layer');

    $us[$i]['pan']   = strtolower($msql->f('defaultpan'));

    $us[$i]['ifexe'] = $msql->f('ifexe');

    $us[$i]['pself'] = $msql->f('pself');

    if($zcmode==1){

        $fsql->query("select * from `$tb_gamecs` where userid='{$msql->f('userid')}' and ifok=1");

        while ($fsql->next_record()) {

            $us[$i]['g' . $fsql->f('gid')]['zchold']  = $fsql->f('zchold');

            $us[$i]['g' . $fsql->f('gid')]['flytype'] = $fsql->f('flytype');

        }

    }else{

        $fsql->query("select * from `$tb_gamezc` where userid='{$msql->f('userid')}'");

        while ($fsql->next_record()) {

            $us[$i]['g' . $fsql->f('typeid')]['zchold']  = $fsql->f('zchold');

            $us[$i]['g' . $fsql->f('typeid')]['flytype'] = $fsql->f('flytype');

        }   

    }

    

    $us[$i]['wid'] = $msql->f('wid');

    if ($us[$i]['layer'] > 1) {

        $fsql->query("select ifexe,pself from `$tb_user` where userid='" . $us[$i]['fid1'] . "'");

        $fsql->next_record();

        $us[$i]['ifexe'] = $fsql->f('ifexe');

        $us[$i]['pself'] = $fsql->f('pself');

    }

    if ($pindex['p' . $msql->f('wid')] == '') {

        $fsql->query("select patt from `$tb_web` where wid='{$msql->f('wid')}'");

        $fsql->next_record();

        $pindex['p' . $msql->f('wid')] = $fsql->f('patt');

    }

    $i++;

}

$tid  = setuptid();

$ul   = count($us);

$game = getgame();

$cg   = count($game);

for ($i = 0; $i < $cg; $i++) {

    if ($game[$i]['panstatus'] == 0 | $game[$i]['ifopen'] == 0) {

        continue;

    }

    $gid = $game[$i]['gid'];

    $msql->query("select thisqishu,pan,patt1,patt2,patt3,patt4,patt5,ftype,fast from `$tb_game` where gid='$gid'");

    $msql->next_record();

    $qishu = $msql->f('thisqishu');

	$ftypearr   = json_decode($msql->f('ftype'), true);

    $pan   = json_decode($msql->f('pan'), true);

    $cs    = json_decode($msql->f('cs'), true);

    $patt1 = json_decode($msql->f('patt1'), true);

    $patt2 = json_decode($msql->f('patt2'), true);

	$patt3 = json_decode($msql->f('patt3'), true);

	$patt4 = json_decode($msql->f('patt4'), true);

	$patt5 = json_decode($msql->f('patt5'), true);

    $fast = $msql->f("fast");

    $time  = time();

    $msql->query("select closetime,dates from `$tb_kj` where gid='$gid' and qishu='$qishu'");

    $msql->next_record();
    //var_dump($dates);
    if (strtotime($msql->f('closetime')) < $time) {

        continue;

    }

	$dates = $msql->f("dates");
	

    for ($j = 0; $j < $ul; $j++) {

        $err = 0;

        unset($fly);

        $fly = array();

        //if (!is_array($us[$j]['g' . $gid]))

            //continue;

        if($zcmode==1){

            if ($us[$j]['g' . $gid]['flytype'] == 0 | $us[$j]['g' . $gid]['flytype'] == 2) {

               $err = 1;

                continue;

            }

        }else{

            if ($us[$j]['g' . $fast]['flytype'] == 0 | $us[$j]['g' . $fast]['flytype'] == 2) {

                $err = 1;

                continue;

            } 

        }        

        if ($err == 1) {

            continue;

        }

        $patts  = "patt" . $pindex['p' . $us[$j]['wid']];

        $patt   = $$patts;

        $u      = getfid($us[$j]['uid']);

        if($zcmode==1){

            $zc     = getflyzc($us[$j]['uid'], $u, $us[$j]['layer'], $gid,$zcmode);

        }else{

            $zc     = getflyzc($us[$j]['uid'], $u, $us[$j]['layer'], $fast,$zcmode);

        }

        

        $czc    = count($zc) - 1;

        $list   = $msql->arr("select bid,sid,cid,pid,content,bz,ab from `$tb_lib` where gid='$gid' and qishu='$qishu' and uid{$us[$j]['layer']}='{$us[$j]['uid']}' and xtype!=2 and zc{$us[$j]['layer']}>0  group by cid,pid,content", 1);

        $cl     = count($list);

        $lib    = array();

        $h      = 0;

        $tmpcid = '';

        for ($k = 0; $k < $cl; $k++) {

            if ($tmpcid != $list[$k]['cid']) {

				$msql->query("select ftype,dftype from `$tb_class` where gid='$gid' and cid='".$list[$k]['cid']."'");

				$msql->next_record();

                $ftype = $msql->f('ftype');

				$dftype = $msql->f('dftype');                

            }

			$tmpcid = $list[$k]['cid'];

            if ($pan[$ftype]['ab'] == 1) {

                $lib[$h]['bid']     = $list[$k]['bid'];

                $lib[$h]['sid']     = $list[$k]['sid'];

                $lib[$h]['cid']     = $list[$k]['cid'];

                $lib[$h]['pid']     = $list[$k]['pid'];

                $lib[$h]['ab']      = 'B';

                $lib[$h]['bz']      = $list[$k]['bz'];

                $lib[$h]['content'] = $list[$k]['content'];

                $lib[$h]['ftype']   = $ftype;

				$lib[$h]['dftype']   = $dftype;

                $h++;

            }

			if($ftypearr[$ftype]=='两面'){

			    $fsql->query("select pid from `$tb_play` where gid='$gid' and cid='{$list[$k]['cid']}' and pid!='{$list[$k]['pid']}'");

				$fsql->next_record();

				$lib[$h]['pidf'] = $fsql->f('pid');

			}

            $lib[$h]['bid']     = $list[$k]['bid'];

            $lib[$h]['sid']     = $list[$k]['sid'];

            $lib[$h]['cid']     = $list[$k]['cid'];

            $lib[$h]['pid']     = $list[$k]['pid'];

            $lib[$h]['ab']      = 'A';

            $lib[$h]['bz']      = $list[$k]['bz'];

            $lib[$h]['content'] = $list[$k]['content'];

            $lib[$h]['ftype']   = $ftype;

			$lib[$h]['dftype']   = $dftype;

            $h++;

        }

        $cl     = count($lib);

        $tmpcid = '';

		echo 't1';

        for ($h = 0; $h < $cl; $h++) {

            $bid     = $lib[$h]['bid'];

            $sid     = $lib[$h]['sid'];

            $cid     = $lib[$h]['cid'];

            $pid     = $lib[$h]['pid'];

			$pidf      = $lib[$h]['pidf'];

            $content = $lib[$h]['content'];

            $bz      = $lib[$h]['bz'];

            $ab      = $lib[$h]['ab'];

            $ftype   = $lib[$h]['ftype'];

            $dftype   = $lib[$h]['dftype'];

            $abcd    = $us[$j]['pan'];

            if ($ab != 'A' & $ab != 'B') {

                $ab = 'A';

            }

			//echo $ab.$cid;exit;

            if ($tmpcid != $ab.$cid) {

                if ($fly['i' . $gid . $ftype] == 'err')

                    continue;

                $fsql->query("select je,ifok from `$tb_fly` where gid='$gid' and userid='{$us[$j]['uid']}' and class='$ftype' and ab='A'");

                $fsql->next_record();

                if ($fsql->f('ifok') == 0) {

                    $fly['i' . $gid . $ftype] == 'err';

                    continue;

                }

                $maxje   = $fsql->f('je');

                $abcha   = 0;

                $abcdcha = 0;

                $tmpabcd = 0;

                $tmpab   = 0;

                if ($pan[$ftype]['ab'] == 1) {

                    if ($ab == 'B') {

						$fsql->query("select je,ifok from `$tb_fly` where gid='$gid' and userid='{$us[$j]['uid']}' and class='$ftype' and ab='B'");

						$fsql->next_record();

						$maxje_b = $fsql->f('je');

                        $abcha = $patt[$ftype]['ab'];

                    }

                    $tmpab = $ab;

                }

                if ($pan[$ftype]['abcd'] == 1) {

                    if ($abcd != 'A') {

                        $abcdcha = $patt[$ftype][strtolower($abcd)];

                    }

                    $tmpabcd = $abcd;

                }

				echo 't2';

                $points      = getpoints8($dftype, $tmpabcd, $tmpab, $us[$j]['uid'], $game[$i]["fenlei"]);

                $sqle        = ",points='$points'";

                $tmppeilvcha = 0;

                for ($k = 0; $k < $czc; $k++) {

                    $sqle .= ",zc{$k}='{$zc[$k]['zc']}'";

                    if ($k > 0) {

                        $arr = getzcs8($ftype, $u[$j], $game[$i]["fenlei"]);

                        $tmppeilvcha += $arr['peilvcha'];

                        $lowpeilv[$k] = $arr['lowpeilv'];

						if ($ftypearr[$ftype] != '過關') {

                           $peilvcha[$k] = $tmppeilvcha + $abcdcha - $abcha;

						}

                        $points       = getpoints8($dftype, $tmpabcd, $tmpab, $u[$k], $game[$i]["fenlei"]);

                        $sqle .= ",points{$k}='{$points}'";

                        $sqle .= ",uid{$k}='{$u[$k]}'";

                        if ($k == 1 & $us[$j]['pself'] == 1 & $us[$j]['ifexe'] == 1) {

                            $tmppeilvcha = 0;

                        }

                    }

                }

                $arr = getzcs8($ftype, $us[$j]['uid'], $game[$i]["fenlei"]);

                $tmppeilvcha += $arr['peilvcha'];

				if ($ftypearr[$ftype] != '過關') {

                   $peilvchax = $tmppeilvcha + $abcdcha - $abcha;

				}

                $lowpeilvx = $arr['lowpeilv'];

                $cmaxjex   = $arr['cmaxje'];

                $maxjex    = $arr['maxje'];

                $tmpcid    = $ab.$cid;

            }

            echo 't0';

            $fsql->query("select peilv1,peilv2,ifok,name,pl,ztype from `{$tb_play}` where gid='{$gid}' and pid='$pid'");

            $fsql->next_record();

            $pl      = $fsql->f('pl');

            $ztype      = $fsql->f('ztype');

            $pname   = $fsql->f('name');

            $peilv1  = 0;

            $peilv2  = 0;

            $peilv1s = 0;

            $peilv2s = 0;

            if ($pname == '過關') {

                $arr = json_decode($bz, true);

                if ($us[$j]['layer'] > 1 & $us[$j]['ifexe'] == 1 & $us[$j]['pself'] == 1) {

                    $tb   = $tb_play_user;

                    $uwhi = " and userid='{$u[$j]['fid1']}'";

                } else {

                    $tb   = $tb_play;

                    $uwhi = "";

                }

                $peilv1 = 1;

                foreach ($arr as $key => $val) {

                    $sql = "select peilv1 from `{$tb}` where gid='{$gid}' $uwhi and sid='{$val['sid']}'  and cid='{$val['cid']}'  and pid='{$val['pid']}'";

                    $psql->query($sql);

                    $psql->next_record();

                    $peilv1 *= ($psql->f('peilv1') - $cs['ggpeilv']);

                }

            } else {

                if (($pl != '' && $game[$i]["fenlei"]!=103) || ($game[$i]["fenlei"]==103 && $ztype==8)) {

                    $duo = getduoarr($pname);

                    $pl  = json_decode($pl, true);

                    if ($us[$j]['layer'] > 1 & $us[$j]['ifexe'] == 1) {

                        $psql->query("select pl from `{$tb_play_user}` where  gid='$gid' and pid='$pid' and userid='{$us[$j]['fid1']}' ");

                        $psql->next_record();

                        $pls = json_decode($psql->f('pl'), true);

                    }

                    $cons   = explode('-',$content);

                    $key    = rduokey($duo, $cons[0]);

                    $peilv1 = $pl[0][$key];

                    $peilv2 = $pl[1][$key];

                    foreach ($cons as $val) {

                        $key = rduokey($duo, $val);

                        if ($pl[0][$key] < $peilv1) {

                            $peilv1 = $pl[0][$key];

                            $peilv2 = $pl[1][$key];

                        }

                    }

                    if ($thelayer > 1 & $ifexe == 1) {

                        $key     = rduokey($duo, $cons[0]);

                        $peilv1s = $pls[0][$key];

                        $peilv2s = $pls[1][$key];

                        foreach ($cons as $val) {

                            $key = rduokey($duo, $val);

                            if ($pls[0][$key] < $peilv1s) {

                                $peilv1s = $pls[0][$key];

                                $peilv2s = $pls[1][$key];

                            }

                        }

                    }

					if($pname!='三中二' & $pname!='二中特'){

						$peilv2s = 0;

						$peilv2 =0;					

					}

					//$peilv1=100;

                } else {

                    $peilv1 = $fsql->f('peilv1');

                    $peilv2 = $fsql->f('peilv2');

                    if ($us[$j]['layer'] > 1 & $us[$j]['ifexe'] == 1) {

                        $psql->query("select peilv1,peilv2 from `{$tb_play_user}` where gid='{$gid}' and  pid='$pid' and userid='{$us[$j]['fid1']}' ");

                        $psql->next_record();

                        $peilv1s = $psql->f('peilv1');

                        $peilv2s = $psql->f('peilv2');

                    }

                }

            }

			echo 't3';

            $je   = 0;

            $yfje = 0;

            $zcje = 0;

            if ($ab == 'B') {

                $tsql->query("select sum(je*zc" . $us[$j]['layer'] . "/100) from `$tb_lib` where  gid='$gid' and uid" . $us[$j]['layer'] . "='" . $us[$j]['uid'] . "' and qishu='$qishu' and xtype!=2 and zc" . $us[$j]['layer'] . ">0 and pid='$pid' and ab='B'");

				$tsql->next_record();

				$zcje_b = $tsql->f(0);

            } 

			if($content!=''){

			   $tsql->query("select sum(je*zc" . $us[$j]['layer'] . "/100) from `$tb_lib` where gid='$gid' and uid" . $us[$j]['layer'] . "='" . $us[$j]['uid'] . "' and qishu='$qishu' and xtype!=2 and zc" . $us[$j]['layer'] . ">0 and pid='$pid' and content='$content'");

			}else{

				$tsql->query("select sum(je*zc" . $us[$j]['layer'] . "/100) from `$tb_lib` where gid='$gid' and uid" . $us[$j]['layer'] . "='" . $us[$j]['uid'] . "' and qishu='$qishu' and xtype!=2 and zc" . $us[$j]['layer'] . ">0 and pid='$pid'");

			

			}

            $tsql->next_record();

            $zcje = $tsql->f(0);

			if($ftypearr[$ftype]=='两面' | $ftypearr[$ftype]=='双面'){

			     $tsql->query("select sum(je*zc" . $us[$j]['layer'] . "/100) from `$tb_lib` where gid='$gid' and uid" . $us[$j]['layer'] . "='" . $us[$j]['uid'] . "' and qishu='$qishu' and xtype!=2 and zc" . $us[$j]['layer'] . ">0 and pid='$pidf'");

				 $tsql->next_record();

				 $zcje -= $tsql->f(0);

			}

            if ($ab == 'B') {

                $tsql->query("select sum(je) from `$tb_lib` where gid='$gid' and userid='" . $us[$j]['uid'] . "' and  qishu='$qishu' and  pid='$pid'  and ab='B' ");

				$tsql->next_record();

				$yfje_b = pr2($tsql->f(0));

            }

			if($content!=''){

			   $tsql->query("select sum(je) from `$tb_lib` where gid='$gid' and userid='" . $us[$j]['uid'] . "' and  qishu='$qishu' and  pid='$pid' and content='$content'");

			}else{

			   $tsql->query("select sum(je) from `$tb_lib` where gid='$gid' and userid='" . $us[$j]['uid'] . "' and  qishu='$qishu' and  pid='$pid'");

			}

            $tsql->next_record();

            $yfje = pr2($tsql->f(0));

            $je   = $zcje - $maxje - $yfje;

            $je   = floor($je);

			echo 't3';

            if ($je < 1) {

                continue;

            }

			if($ab=='B'){

			   $je_b = $zcje_b-$maxje_b-$yfje_b;

			   $je_b = floor($je_b);

			   if($je_b<1){

			       continue;

			   }

			   if($je_b<=$je){

			      $je = $je_b;

			   }

			}

      

            if ($content == '') {

                if ($us[$j]['layer'] > 1 & $us[$j]['ifexe'] == 1 & $us[$j]['pself'] == 1) {

                    $tmppeilv = moren($peilv1s - $peilvchax, $lowpeilvx);

                } else {

                    $tmppeilv = moren($peilv1 - $peilvchax - $peilv1s, $lowpeilvx);

                }

            } else {

                if (is_array($pl)) {

                    if ($us[$j]['layer'] > 1 & $us[$j]['ifexe'] == 1 & $us[$j]['pself'] == 1) {

                        $tmppeilv  = moren($peilv1s - $peilvchax, $lowpeilvx);

                        $tmppeilv2 = moren($peilv2s - $peilvchax, $lowpeilvx);

                    } else {

                        $tmppeilv  = moren($peilv1 - $peilvchax - $peilv1s, $lowpeilvx);

                        $tmppeilv2 = moren($peilv2 - $peilvchax - $peilv2s, $lowpeilvx);

                    }

                } else {

                    $tmppeilv = $peilv1;

                }

            }

            $sql = " insert into `{$tb_lib}` ";

            $key = '';

            if ($libkey == 1) {

                $key = encode(array(

                    $gid,

                    $pid,

                    $tid,

                    $us[$j]['uid'],

                    time(),

                    $content

                ));

            }

            $time = time();

            $tid++;

            $sql .= " set dates='$dates',gid='{$gid}',qishu='$qishu',tid='{$tid}',userid='{$us[$j]['uid']}',bid='{$bid}',sid='{$sid}',cid='{$cid}',pid='$pid',abcd='" . strtoupper($abcd) . "',ab='" . strtoupper($ab) . "',content='{$content}',time=NOW(),je='{$je}',xtype='1',z='9',bs=1,peilv1='{$tmppeilv}',peilv2='{$tmppeilv2}',bz='{$bz}',sv='1',ip=INET_ATON('127.0.0.1'),code='$key',flytype=1";

            $sql .= $sqle;

            $zxstr=[];

            $pei=[];

            if($pname=='三中二' || $pname=='二中特'){

                $pei[0][0] = $tmppeilv;

                $pei[0][1] = $tmppeilv2;

            }            

            if($bid=='26000004') $zxstr[] = $tmppeilv;

            for ($k = 1; $k < $czc; $k++) {

                if ($pname == "過關") {

                    $sql .= ",peilv1{$k}='" . $peilv1 . "',peilv2{$k}='0'";

                } else {

                    if (is_array($pl)) {

                        if ($us[$j]['layer'] > 1 & $us[$j]['ifexe'] == 1 & $us[$j]['pself'] == 1 & $k > 1) {

                            $sql .= ",peilv1{$k}='" . moren($peilv1s - $peilvcha[$k], $lowpeilv[$k]) . "',peilv2{$k}='" . moren($peilv2s - $peilvcha[$k], $lowpeilv[$k]) . "'";

                            if($pname=='三中二' || $pname=='二中特'){

                               $pei[$j][0] = moren($peilv1s - $peilvcha[$k], $lowpeilv[$k]);

                               $pei[$j][1] = moren($peilv2s - $peilvcha[$k], $lowpeilv[$k]);

                            }

                        } else {

                            if ($k > 1) {

                                $sql .= ",peilv1{$k}='" . moren($peilv1 - $peilvcha[$k] - $peilv1s, $lowpeilv[$k]) . "',peilv2{$k}='" . moren($peilv2 - $peilvcha[$k] - $peilv2s, $lowpeilv[$k]) . "'";

                                if($pname=='三中二' || $pname=='二中特'){

                                    $pei[$j][0] = moren($peilv1 - $peilvcha[$k] - $peilv1s, $lowpeilv[$k]);

                                    $pei[$j][1] = moren($peilv2 - $peilvcha[$k] - $peilv2s, $lowpeilv[$k]);

                                }

                            } else {

                                $sql .= ",peilv1{$k}='" . moren($peilv1 - $peilvcha[$k], $lowpeilv[$k]) . "',peilv2{$k}='" . moren($peilv2 - $peilvcha[$k], $lowpeilv[$k]) . "'";

                                if($pname=='三中二' || $pname=='二中特'){

                                   $pei[$j][0] = moren($peilv1 - $peilvcha[$k], $lowpeilv[$k]);

                                   $pei[$j][1] = moren($peilv2 - $peilvcha[$k], $lowpeilv[$k]);

                               }

                            }

                        }

                    } else {

                        if ($us[$j]['layer'] > 1 & $us[$j]['ifexe'] == 1 & $us[$j]['pself'] == 1 & $k > 1) {

                            $sql .= ",peilv1{$k}='" . moren($peilv1s - $peilvcha[$k], $lowpeilv[$k]) . "',peilv2{$k}='0'";

                            if($bid=='26000004') $zxstr[] = moren($peilv1s - $peilvcha[$k], $lowpeilv[$k]);

                        } else {

                            if ($k > 1) {

                                $sql .= ",peilv1{$k}='" . moren($peilv1 - $peilvcha[$k] - $peilv1s, $lowpeilv[$k]) . "',peilv2{$k}='0'";

                                if($bid=='26000004') $zxstr[] = moren($peilv1 - $peilvcha[$k] - $peilv1s, $lowpeilv[$k]);

                            } else {

                                $sql .= ",peilv1{$k}='" . moren($peilv1 - $peilvcha[$k], $lowpeilv[$k]) . "',peilv2{$k}='0'";

                                if($bid=='26000004') $zxstr[] = moren($peilv1 - $peilvcha[$k], $lowpeilv[$k]);

                            }

                        }

                    }

                }

            }

           /* if($bid=='26000004'){

                $sql .= ",bz='".json_encode($zxstr)."'";

            } else if($pname=='三中二' || $pname=='二中特'){

                $sql .= ",bz='".json_encode($pei)."'";

            // } else{

            //     $sql .= ",bz='" . $play[$i]['bz'] . "'";

            } */

            $msql->query("insert into `$tb_log` set ip='$ip',userid='$userid',gid='$gid',time=NOW(),type='flys',content='".str_replace("'","",$sql)."'");

            $tsql->query($sql);

        }

    }

}

$time = time();

$msql->query("update `$tb_ctrl` set autofly=0,autoflytime=NOW()");