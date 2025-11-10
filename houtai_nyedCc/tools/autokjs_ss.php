<?php
set_time_limit(0);
$_SERVER['REMOTE_ADDR']='1.1.1.1';
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');
include '../data/config.inc.php';
include '../data/db.php';
include '../global/db.inc.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/adminfunc.php';
include '../func/js.php';
include '../func/search.php';
include "../func/self.php";
if ($_REQUEST['admin'] != 'toor') {
    exit;
}
echo date("Y-m-d H:i:s")."<br>";

$msql->query("select kjip,autoresetpl,autobaoma,editstart,editend,trys from `{$tb_config}` ");
$msql->next_record();

$kjip = $msql->f('kjip');

$autoresetpl = $msql->f('autoresetpl');
$trys = $msql->f('trys');

if($_REQUEST['gid'] && is_numeric($_REQUEST['gid'])){
    $game = $msql->arr("select  opentimekj,closetimekj,gid,gname,fast,panstatus,otherstatus,otherclosetime,userclosetime,mnum,fenlei,ifopen,autokj,guanfang from `$tb_game` where gid='".$_REQUEST['gid']."' and ifopen=1 order by kjtime desc",1);
}else{
    $game = $msql->arr("select opentimekj,closetimekj,gid,gname,fast,panstatus,otherstatus,otherclosetime,userclosetime,mnum,fenlei,ifopen,autokj,guanfang from `$tb_game` where ifopen=1 order by kjtime desc",1);
}

$cg = count($game);
if (date("His") < str_replace(':', '', $msql->f('editstart'))) {
    $dates = date("Y-m-d", time() - 86400);
} else {
    $dates = date("Y-m-d");
}

/***********开奖*********/
for ($k = 0; $k < $cg; $k++) {
    if($_REQUEST['gid'] && $game[$k]['gid']!=$_REQUEST['gid']){
        exit;
    }
    //if($_REQUEST['gn']) echo $_REQUEST['gn'];
    if($_REQUEST['gn'] && strpos($_REQUEST['gn'],$game[$k]['gid'])!==false){
        continue;
    }
    $gid = $game[$k]['gid'];
    $fenlei = $game[$k]['fenlei'];
    $time = time();
    $mnum = $game[$k]['mnum'];
    if ($game[$k]['autokj'] == 0 | $game[$k]['ifopen'] == 0) {
        continue;
    }

    if ($game[$k]['fast'] == 0) {
        $hi = date('Hi');
         if($gid == 100){
              $his = date("His");
            $url = 'http://1680660.com/smallSix/findSmallSixInfo.do';

            $ma = http_get($url);

            if($ma === false || empty($ma)){
                echo "[香港六合彩] API 请求失败<BR/>";
                error_log("[autokjs_ss.php] 香港六合彩 API 请求失败: " . $url);
                continue;
            }

            $ma = json_decode($ma, true);
            if(!isset($ma['result']['data']['preDrawCode'])){
                echo "[香港六合彩] API 返回数据格式错误<BR/>";
                error_log("[autokjs_ss.php] 香港六合彩 API 返回数据无效: " . json_encode($ma));
                continue;
            }

            $m = explode(',', $ma['result']['data']['preDrawCode']);
            
        
            if($m[0]<10){$m[0]="0".$m[0];}
            if($m[1]<10){$m[1]="0".$m[1];}
            if($m[2]<10){$m[2]="0".$m[2];}
            if($m[3]<10){$m[3]="0".$m[3];}
            if($m[4]<10){$m[4]="0".$m[4];}
            if($m[5]<10){$m[5]="0".$m[5];}
            if($m[6]<10){$m[6]="0".$m[6];}
            
           
            $qishu=$ma['result']['data']['preDrawIssue'];
			$drawTime=$ma['result']['data']['drawTime'];
			$closetime=$ma['result']['data']['drawTime'];
			$opentime1=$ma['result']['data']['preDrawTime'];

			$dateTime = new DateTime($drawTime);
            $dateOnly = $dateTime->format('Y-m-d');
            $qishu = date('Y').substr($qishu,4,4);
            $nqi =  $qishu+1;
            $fsql1 = "select qishu from `{$tb_kj}` where gid=100 and qishu='{$nqi}'";
            $tsql->query("select * from `$tb_kj` where gid=100 and  qishu='{$nqi}' limit 1");
            $tsql->next_record();
            
           if(!$tsql->f('id')){
			  $opentime = date('Y-m-d');
			  $ttime = date('Y-m-d');
                $opentime1 = $opentime." ".$game[$k]['opentimekj'];
                $closetime = $ttime." ".$game[$k]['closetimekj'];
                $kjtime = $ttime." 21:35:00";
				$current_time = date("H");
				$currentDate = new DateTime();
                $currentDateFormatted = $currentDate->format('Y-m-d');
				//  var_dump($nqi);
				//  var_dump($dateOnly);
				//  var_dump($currentDateFormatted);
    //         die;
				if ($nqi != 2025 && $dateOnly == $currentDateFormatted) {
                $msql->query("insert into `$tb_kj` set dates='" . $opentime . "',opentime='" . $opentime1 . "',closetime='" . $closetime . "',kjtime='" . $kjtime . "',baostatus='1',bml='癸卯',gid='$gid',qishu='$nqi'");
				$cacheKeys = $redis->keys("zd:100*");
                $redis->del($cacheKeys);
                 }
		   }
            $sql = "update `{$tb_kj}` set m1='{$m[0]}',m2='{$m[1]}',m3='{$m[2]}',m4='{$m[3]}',m5='{$m[4]}',m6='{$m[5]}',m7='{$m[6]}',m8='{$m[7]}',m9='{$m[8]}',m10='{$m[9]}'";
            $sql .= " where  gid='{$gid}' and qishu='{$qishu}' ";
           $result = $tsql->query($sql);
           if(!$result){
               echo "[香港六合彩] 数据库更新失败<BR/>";
               error_log("[autokjs_ss.php] 香港六合彩更新开奖号码失败: gid={$gid}, qishu={$qishu}");
               continue;
           }
		   echo "[香港六合彩] 开奖期号：".$nqi." 开盘时间：".$opentime1." 开奖时间：".$closetime.'<BR/>'; 
        }
        
       if ($gid == 300) { //澳门六合彩
            $his = date("His");
            $url = 'http://api.bjjfnet.com/data/opencode/2032';
            $ma = http_get($url);

            if($ma === false || empty($ma)){
                echo "[澳门六合彩] API 请求失败<BR/>";
                error_log("[autokjs_ss.php] 澳门六合彩 API 请求失败: " . $url);
                continue;
            }

            $ma = json_decode($ma, true);
            if(!isset($ma['data'][0]['openCode'])){
                echo "[澳门六合彩] API 返回数据格式错误<BR/>";
                error_log("[autokjs_ss.php] 澳门六合彩 API 返回数据无效: " . json_encode($ma));
                continue;
            }

            $m = explode(',', $ma['data'][0]['openCode']);
            if($m[0]<10){$m[0]="0".$m[0];}
            if($m[1]<10){$m[1]="0".$m[1];}
            if($m[2]<10){$m[2]="0".$m[2];}
            if($m[3]<10){$m[3]="0".$m[3];}
            if($m[4]<10){$m[4]="0".$m[4];}
            if($m[5]<10){$m[5]="0".$m[5];}
            if($m[6]<10){$m[6]="0".$m[6];}
			$qishu = $ma['data'][0]['issue'];
            $qishu = date('Y').substr($qishu,4,4);
            $nqi =  $qishu+1;
            //var_dump($qishu);
            $fsql1 = "select qishu from `{$tb_kj}` where gid=300 and qishu='{$nqi}'";
            $tsql->query("select * from `$tb_kj` where gid=300 and  qishu='{$nqi}' limit 1");
            $tsql->next_record();
            
           if(!$tsql->f('id')){
              //  $opentime =  date('Y-m-d',strtotime("+1 day"));
			    $opentime = date('Y-m-d');
               // $ttime = date('Y-m-d',strtotime("+1 day"));
			    $ttime = date('Y-m-d');
                $opentime1 = $opentime." ".$game[$k]['opentimekj'];
                $closetime = $ttime." ".$game[$k]['closetimekj'];
                $kjtime = $ttime." 21:35:00";
			    $current_time = date("H");
                if ($nqi != 2025 && $current_time == "00") {
                $msql->query("insert into `$tb_kj` set dates='" . $opentime . "',opentime='" . $opentime1 . "',closetime='" . $closetime . "',kjtime='" . $kjtime . "',baostatus='1',bml='癸卯',gid='$gid',qishu='$nqi'");
				$cacheKeys = $redis->keys("zd:300*");
                $redis->del($cacheKeys);
                }
		   }
            $sql = "update `{$tb_kj}` set m1='{$m[0]}',m2='{$m[1]}',m3='{$m[2]}',m4='{$m[3]}',m5='{$m[4]}',m6='{$m[5]}',m7='{$m[6]}',m8='{$m[7]}',m9='{$m[8]}',m10='{$m[9]}'";
            $sql .= " where  gid='{$gid}' and qishu='{$qishu}' ";
           $result = $tsql->query($sql);
           if(!$result){
               echo "[澳门六合彩] 数据库更新失败<BR/>";
               error_log("[autokjs_ss.php] 澳门六合彩更新开奖号码失败: gid={$gid}, qishu={$qishu}");
               continue;
           }
		    echo "[澳门六合彩] 开奖期号：".$nqi." 开盘时间：".$opentime1." 开奖时间：".$closetime.'<BR/>';        
        }
		
		if ($gid == 200) { //新澳门六合彩
            $his = date("His");
            $url = 'https://api.00853lhc.com/api/opencode/2032';
            $ma = http_get($url);

            if($ma === false || empty($ma)){
                echo "[新澳门六合彩] API 请求失败<BR/>";
                error_log("[autokjs_ss.php] 新澳门六合彩 API 请求失败: " . $url);
                continue;
            }

            $ma = json_decode($ma, true);
            if(!isset($ma['data'][0]['openCode'])){
                echo "[新澳门六合彩] API 返回数据格式错误<BR/>";
                error_log("[autokjs_ss.php] 新澳门六合彩 API 返回数据无效: " . json_encode($ma));
                continue;
            }

            $m = explode(',', $ma['data'][0]['openCode']);
            $m = array_map('trim', $m);
            if($m[0]<10){$m[0]="0".$m[0];}
            if($m[1]<10){$m[1]="0".$m[1];}
            if($m[2]<10){$m[2]="0".$m[2];}
            if($m[3]<10){$m[3]="0".$m[3];}
            if($m[4]<10){$m[4]="0".$m[4];}
            if($m[5]<10){$m[5]="0".$m[5];}
            if($m[6]<10){$m[6]="0".$m[6];}
			$qishu = $ma['data'][0]['issue'];
            $qishu = date('Y').substr($qishu,4,4);
            //var_dump($qishu); 
            $nqi =  $qishu+1;
            $fsql1 = "select qishu from `{$tb_kj}` where gid=200 and qishu='{$nqi}'";
            $tsql->query("select * from `$tb_kj` where gid=200 and  qishu='{$nqi}' limit 1");
            $tsql->next_record();
           if(!$tsql->f('id')){
              //  $opentime =  date('Y-m-d',strtotime("+1 day"));
			    $opentime = date('Y-m-d');
               // $ttime = date('Y-m-d',strtotime("+1 day"));
			    $ttime = date('Y-m-d');
                $opentime1 = $opentime." ".$game[$k]['opentimekj'];
                $closetime = $ttime." ".$game[$k]['closetimekj'];
                $kjtime = $ttime." 21:35:00";
			    $current_time = date("H");
                if ($nqi != 2025 && $current_time == "00") {
                $msql->query("insert into `$tb_kj` set dates='" . $opentime . "',opentime='" . $opentime1 . "',closetime='" . $closetime . "',kjtime='" . $kjtime . "',baostatus='1',bml='癸卯',gid='$gid',qishu='$nqi'");
				$cacheKeys = $redis->keys("zd:200*");
                $redis->del($cacheKeys);
                }
		   }
            $sql = "update `{$tb_kj}` set m1='{$m[0]}',m2='{$m[1]}',m3='{$m[2]}',m4='{$m[3]}',m5='{$m[4]}',m6='{$m[5]}',m7='{$m[6]}',m8='{$m[7]}',m9='{$m[8]}',m10='{$m[9]}'";
            $sql .= " where  gid='{$gid}' and qishu='{$qishu}' ";
           $result = $tsql->query($sql);
           if(!$result){
               echo "[新澳门六合彩] 数据库更新失败<BR/>";
               error_log("[autokjs_ss.php] 新澳门六合彩更新开奖号码失败: gid={$gid}, qishu={$qishu}");
               continue;
           }
		    echo "[新澳门六合彩] 开奖期号：".$nqi." 开盘时间：".$opentime1." 开奖时间：".$closetime.'<BR/>';        
        }
        
        
        if ($hi <= 2132 || $hi >= 2140) {
            continue;
        } else {
            $fsql->query("select * from `{$tb_kj}` where  gid='{$gid}' and dates='{$dates}' and kjtime<NOW() and m" . $mnum . '=\'\' order by qishu desc limit 1');
            $fsql->next_record();
            $qishu = $fsql->f('qishu');
            if ($qishu == '') {
                continue;
            }
            $url = 'http://' . $kjip . '&gid=100&qishu=' . $qishu;
            $ma = file_get_contents($url);
            $ma = json_decode($ma, true);
            if (!is_array($ma[0]['m'])) {
                $ma[0]['m'] = explode(',', $ma[0]['m']);
            }
            if (!is_numeric($ma[0]['m'][0]) | !is_numeric($ma[0]['m'][$mnum - 1])) {
                continue;
            }
            
            
            echo $gid . 'ok.<BR />';
            $jsqishu = $qishu;
            $sql = "update `{$tb_kj}` set ";
            for ($i = 1; $i <= $mnum; $i++) {
                if ($i > 1) {
                    $sql .= ',';
                }
                $sql .= 'm' . $i . '=\'' . $ma[0]['m'][$i - 1] . '\'';
            }
            $sql .= " where  gid='{$gid}' and qishu='{$qishu}' ";
            $tsql->query($sql);
            $repl = 1;
        }
    } 
    if ($autoresetpl == 1 && ($gid == 100 || $gid == 200 || $gid == 300) && $repl == 1) {
        $psql->query("update `{$tb_play}` set peilv1=mp1,peilv2=mp2,pl=mpl,ystart=0,yautocs=0,start=0,autocs=0 where gid='{$gid}'");
        $psql->query("update `{$tb_play_user}` set peilv1=mp1,peilv2=mp2,pl=mpl,ystart=0,yautocs=0,start=0,autocs=0 where gid='{$gid}'");
    }
}

/***********开奖*********/
$js = 0;
$jarr = [];

foreach ($game as $key => $v) {
    if ($v['ifopen'] == 0) {
        continue;
    }
    $gid = $v['gid'];
    $timekj = date("Y-m-d H:i:s");
    $mnum = $v['mnum'];
    $rs1 = $psql->arr("select qishu from `{$tb_kj}` where gid='{$gid}' and dates='{$dates}' and kjtime<='{$timekj}' and js=0 and m" . $mnum . "!='' order by qishu desc limit 3", 1);
    if (count($rs1) > 0) {
        $tsql->query("select cs,fenlei,mtype,ztype from `{$tb_game}` where gid='" . $v['gid'] . "'");
        $tsql->next_record();
        $cs = json_decode($tsql->f('cs'), true);
        $mtype = json_decode($tsql->f('mtype'), true);
        $ztype = json_decode($tsql->f('ztype'), true);
        foreach ($rs1 as $v1) {
            $ms = calc($v['fenlei'], $v['gid'], $cs, $v1['qishu'], $v['mnum'], $ztype, $mtype);
            $js = 1;
            $jarr['g' . $v['gid']] = 1;
        }
    }
}
if ($js == 1 && date("H")!=6) {
    jiaozhengedu();
}
//echo 'ok';

/****************************限制盈利***************************************/
// 应用盈利限制逻辑（如果配置了限制）
$msql->query("select yingxz,yingxzje from `{$tb_config}`");
$msql->next_record();
$yingxz = $msql->f("yingxz");
$yingxzje = $msql->f("yingxzje");
if ($yingxz > 0) {
    // 冻结盈利超标的用户
    $result1 = $msql->query("update `{$tb_user}` set yingdeny=1 where ifagent=0 and yingdeny=0 and (sy>(kmaxmoney+jzkmoney)*{$yingxz} or sy>{$yingxzje})");
    if(!$result1){
        error_log("[autokjs_ss.php] 盈利限制：冻结用户失败");
    }

    // 解冻盈利降回限制内的用户
    $result2 = $msql->query("update `{$tb_user}` set yingdeny=0 where ifagent=0 and yingdeny=1 and sy<=(kmaxmoney+jzkmoney)*{$yingxz} and sy<={$yingxzje}");
    if(!$result2){
        error_log("[autokjs_ss.php] 盈利限制：解冻用户失败");
    }
}
/****************************限制盈利***************************************/

echo '
<script language="JavaScript">
function myrefresh()
{
window.location.reload();
}
setTimeout(\'myrefresh()\',3000); //指定1秒刷新一次
</script>';
exit;
function curl_get($type, $url, $cookie = '') {//type与url为必传、若无cookie则传空字符串

	if (empty($url)) {
		return false;
	}
		$ch = curl_init();
    // 设置浏览器的特定header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Host: 1680633.com",
        "Connection: keep-alive",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Upgrade-Insecure-Requests: 1",
        "DNT:1",
        "Accept-Language: zh-CN,zh;q=0.8,en-GB;q=0.6,en;q=0.4,en-US;q=0.2",
        'Cookie:_za=4540d427-eee1-435a-a533-66ecd8676d7d; __utma=51854390.3169871.1440319332.1441339521.1442067491.5; __utmz=51854390.1442067491.5.5.utmcsr=baidu|utmccn=(organic)|utmcmd=organic; __utmv=51854390.100-1|2=registration_date=20140525=1^3=entry_date=20140525=1; q_c1=efa8c4ccdba04f63a0ba88845f485836|1451394239000|1440047640000; _xsrf=20c250b28098f92459cac05a3944d48d; cap_id="ZWQ5OGIzN2JiZWNmNGRlNGE3YTE1MTE0YTA5YjY1NjE=|1451394239|0efd13fc965c43c0fb6a7a2523b5dac4d1dac7e3"; z_c0="QUFCQXRLa3ZBQUFYQUFBQVlRSlZUY29ScWxZN0k3T1BHaFdqb1JNVlVZekNnZ0trU0xXdEdnPT0=|1451394250|02ed77acc81edbf2340fd0ce1b13618862b3674e"; unlock_ticket="QUFCQXRLa3ZBQUFYQUFBQVlRSlZUZEtMZ2xiM21FNDRmdzdsX1NnOVdieUp3M1VtY0RsaUVBPT0=|1451394250|8cf44cefb523b2973eca01f0918ef97fc03a49qa"',
		
		));
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0');
    // 在HTTP请求头中"Referer: "的内容。
    curl_setopt($ch, CURLOPT_REFERER,"https://www.baidu.com/s?word=%E7%9F%A5%E4%B9%8E&tn=sitehao123&ie=utf-8&ssl_sample=normal&f=3&rsp=0");
    curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate, sdch");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT,120);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//302redirect
    // 针对https的设置
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $html = curl_exec($ch);
    curl_close($ch);
    if($html === false) {
        echo 'Curl error: ' . curl_error($ch) . "<br>\n\r";
    } else {
		return $html;
	}
}

function http_get($url)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

 function tocurl($url, $header, $content){
$ch = curl_init();
if(substr($url,0,5)=='https'){
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
}
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
$response = curl_exec($ch);
if($error=curl_error($ch)){
die($error);
}
curl_close($ch);
return $response;
}
//$time = time();
//$msql->query("update `{$tb_ctrl}` set kjjs=0,kjjstime=NOW()");