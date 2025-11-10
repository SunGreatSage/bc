<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');
include('../data/config.inc.php');
include('../data/db.php');
include('../global/db.inc.php');
include ('../func/func.php');
include ('../func/csfunc.php');
include ('../func/adminfunc.php');
include ('../func/js.php');
include ('../func/search.php');
if ($_REQUEST['admin'] != 'toor') {
    exit;
}
$msql->query("select kjjs,kjjstime from `{$tb_ctrl}`");
$msql->next_record();
$time = time();
if ($msql->f('kjjs') == 1 & $time - strtotime($msql->f('kjjstime')) < 20) {
    die;
}
$msql->query("update `{$tb_ctrl}` set kjjs=1,kjjstime=NOW()");
$msql->query("select kjip,autoresetpl,autobaoma,editstart,editend from `{$tb_config}` ");
$msql->next_record();
//if($msql->f('autobaoma')==0) exit;
$kjip        = $msql->f('kjip');
$autoresetpl = $msql->f('autoresetpl');
$game        = getgame();
$cg          = count($game);
if(date("His")<str_replace(':','',$msql->f('editstart'))){
    $dates = date("Y-m-d",time()-86400); 
}else{
    $dates = date("Y-m-d"); 
}
/***********开奖*********/
for ($k = 0; $k < $cg; $k++) {
    $gid  = $game[$k]['gid'];
    $time = time();
    $mnum = $game[$k]['mnum'];
    if ($game[$k]['autokj'] == 0 | $game[$k]['ifopen'] == 0) {
        continue;
    }
    if ($game[$k]['fast'] == 0) {
        $hi = date('Hi');
        if ($hi <= 2132 | $hi >= 2140) {
            continue;
        } else {
            $fsql->query("select * from `{$tb_kj}` where  gid='{$gid}' and days='$dates' and kjtime<NOW() and m" . $mnum . '=\'\' order by qishu desc limit 1');
            $fsql->next_record();
            $qishu = $fsql->f('qishu');
            if ($qishu == '') {
                continue;
            }
            $url = 'http://' . $kjip . '/ssc/kjn.php?enter=kj&gid=100&qishu=' . $qishu;
            $ma  = file_get_contents($url);
            $ma  = json_decode($ma, true);
            if (!is_numeric($ma[0]['m'][0]) | !is_numeric($ma[0]['m'][$mnum - 1])) {
                continue;
            }
            echo $gid . 'ok.....<BR />';
            $jsqishu = $qishu;
            $sql     = "update `{$tb_kj}` set ";
            for ($i = 1; $i <= $mnum; $i++) {
                if ($i > 1) {
                    $sql .= ',';
                }
                $sql .= 'm' . $i . '=\'' . $ma[0]['m'][$i - 1] . '\'';
            }
            $sql .= ' where qishu=\'' . $qishu . "' and gid='{$gid}'";
            $tsql->query($sql);
			$repl=1;
        }
    } else {
		$timekj = $time;
		if($cs['randbm']==1){
            $timekj -=12;
		}
		$fsql->query("select * from `{$tb_kj}` where  gid='{$gid}'  and UNIX_TIMESTAMP(kjtime)<={$timekj} and {$timekj}-UNIX_TIMESTAMP(kjtime)<600 and m" . $mnum . "='' order by kjtime  limit 1");
        $fsql->next_record();
        $qishu = $fsql->f('qishu');
        if ($qishu == '') {
            continue;
        }
        $qs = formatqs($gid, $qishu);
        if ($gid == 163) {
			$his = date("His");
			if($his<60000){
				$url = 'http://' . $kjip . '/ssc/kjn.php?enter=kj&gid=162&qishu=' . $qs;
			}else{
               $url = 'http://' . $kjip . '/ssc/kjn.php?enter=kj&gid=161&qishu=' . $qs;
			}
            //echo $url;
            $ma  = file_get_contents($url);
            $ma  = json_decode($ma, true);
            if (!is_numeric($ma[0]['m'][0]) | !is_numeric($ma[0]['m'][$mnum - 1])) {
                echo $gid . 'waitting.....<BR />';
                continue;
            }
            echo $gid . 'ok.....<BR />';
            $m = xy28kj($ma[0]['m']) ;
            
            $sql = "update `{$tb_kj}` set m1='{$m[0]}',m2='{$m[1]}',m3='{$m[2]}'";            
            $sql .= ' where qishu=\'' . $qishu . "' and gid='{$gid}'";
            $tsql->query($sql);
        } else {
			$tsql->query("select cs from `$tb_game` where gid='$gid'");
			$tsql->next_record();
			$cs = json_decode($tsql->f('cs'),true);
			if($cs['randbm']==1){
				$ma = kjmyself($qishu,$game[$k]['mnum']);
			}else{
			   $url = 'http://' . $kjip . '/ssc/kjn.php?enter=kj&gid=' . $gid . '&qishu=' . $qs;            
                $ma = file_get_contents($url);
			}
			
            $ma = json_decode($ma, true);
            if (!is_numeric($ma[0]['m'][0]) | !is_numeric($ma[0]['m'][$mnum - 1])) {
                echo $gid . 'waitting.....<BR />';
                continue;
            }
            echo $gid . 'ok.....<BR />';
            $sql = "update `{$tb_kj}` set ";
            for ($i = 1; $i <= $mnum; $i++) {
                if ($i > 1) {
                    $sql .= ',';
                }
                $sql .= 'm' . $i . '=\'' . $ma[0]['m'][$i - 1] . '\'';
            }
            $sql .= ' where qishu=\'' . $qishu . "' and gid='{$gid}'";
            $tsql->query($sql);
        }
		$jsqishu = $qishu;
        if ($gid == 115 & substr($ma[0]['nextqishu'], -2) != 1) {
            $cs        = transgame($gid, 'cs');
            $cs        = json_decode($cs, true);
            $kjtime    = $ma[0]['nextkj'];
            $closetime = $kjtime - $cs['closetime'];
            $opentime  = $closetime - 540;
            $qishu++;
            $tsql->query("update `{$tb_kj}` set closetime='".sqltime($closetime)."',kjtime='".sqltime($kjtime)."' where gid='{$gid}' and qishu='{$qishu}'");
        }
        if ($gid == 111 & substr($ma[0]['nextqishu'], -2) != 1) {
            $cs        = transgame($gid, 'cs');
            $cs        = json_decode($cs, true);
            $kjtime    = $ma[0]['nextkj'];
            $closetime = $kjtime - $cs['closetime'];
            $opentime  = $closetime - 540;
            $qishu++;
            $tsql->query("update `{$tb_kj}` set closetime='".sqltime($closetime)."',kjtime='".sqltime($kjtime)."' where gid='{$gid}' and qishu='{$qishu}'");
        }
        if ($gid == 133 & substr($ma[0]['nextqishu'], -2) != 1) {
            $cs        = transgame($gid, 'cs');
            $cs        = json_decode($cs, true);
            $kjtime    = $ma[0]['nextkj'];
            $closetime = $kjtime - $cs['closetime'];
            $opentime  = $closetime - 540;
            $qishu++;
            $tsql->query("update `{$tb_kj}` set closetime='".sqltime($closetime)."',kjtime='".sqltime($kjtime)."' where gid='{$gid}' and qishu='{$qishu}'");
        }
        searchqishu($gid, 50, 1);
        attpeilv($gid);
    }
	if($game[$k]['fenlei']==163){
        //call_user_func('kjjs_101', $jsqishu, $gid, $game[$k]['fenlei']);
	}else{
        //call_user_func('kjjs_' . $game[$k]['fenlei'], $jsqishu, $gid, $game[$k]['fenlei']);
	}
    //jiaozhengedu();
    if ($autoresetpl == 1 & $gid == 100 & $repl==1 ) {
        //$psql->query("update `{$tb_play}` set peilv1=mp1,peilv2=mp2,pl=mpl,ystart=0,yautocs=0,start=0,autocs=0 where gid='$gid'");
        //$psql->query("update `{$tb_play_user}` set peilv1=mp1,peilv2=mp2,pl=mpl,ystart=0,yautocs=0,start=0,autocs=0 where gid='$gid'");
    }
}
/***********开奖*********/

$js = 0;
foreach ($game as $key => $v) {
    if ($v['ifopen'] == 0)
        continue;
    $rs1 = $psql->arr("select qishu from `$tb_kj` where gid='" . $v['gid'] . "' and js=0 and m" . $v['mnum'] . "!='' order by kjtime desc limit 5", 0);
    //echo "select qishu from `$tb_kj` where gid='".$v['gid']."' and js=0 and m".$v['mnum']."!='' order by kjtime desc limit 5";
    foreach ($rs1 as $v1) {
        echo $v1[0], "kjjs_" . $v['fenlei'], "<BR />";
		if($v['fenlei']==163){
           echo call_user_func("kjjs_101", $v1[0], $v['gid'], $v['fenlei']);
		}else{
           echo call_user_func("kjjs_" . $v['fenlei'], $v1[0], $v['gid'], $v['fenlei']);
		}
        $js = 1;
    }
    if ($autoresetpl == 1 & $v['gid'] == 100 & $js == 1 ) {
        $psql->query("update `$tb_play` set peilv1=mp1,peilv2=mp2,pl=mpl,start=0,autocs=0,ystart=0,yautocs=0 where gid='" . $v['gid'] . "'");
        $psql->query("update `$tb_play_user` set peilv1=mp1,peilv2=mp2,pl=mpl,start=0,autocs=0,ystart=0,yautocs=0 where gid='" . $v['gid'] . "'");
    }
}
if ($js == 1) {
    jiaozhengedu();
}

$time = time();
$msql->query("update `{$tb_ctrl}` set kjjs=0,kjjstime=NOW()");
?>
<script language="JavaScript"> 
function myrefresh() 
{ 
window.location.reload(); 
} 
setTimeout('myrefresh()',3000); //指定1秒刷新一次 
</script> 