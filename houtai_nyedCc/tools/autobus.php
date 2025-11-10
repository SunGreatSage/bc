<?php
$_SERVER['REMOTE_ADDR']='1.1.1.1';
error_reporting(E_ALL);
date_default_timezone_set("Asia/Shanghai");
include '../data/config.inc.php';
include '../data/db.php';
include '../global/db.inc.php';
include "../func/func.php";
include "../func/csfunc.php";
include "../func/adminfunc.php";
// if ($_REQUEST['admin'] != 'toor') {
//     exit;
// }
//error_reporting(E_ALL);
include "./bw_func.php";
include "./bwssc_func.php";
include "./ws_func.php";
include "./sgwin_func.php";
include "./dl_func.php";
include "./zysix_func.php";
include "./idc_func.php";
$fid = $_REQUEST['fid'];
$userid = 99999999;
$msql->query("select editstart,editend from `{$tb_config}`");
$msql->next_record();
$editstart = $msql->f('editstart');
if (date("H:i:s") < $editstart) {
    $dates = date("Y-m-d", time() - 86400);
} else {
    $dates = date("Y-m-d");
}
if (is_numeric($fid)) {
    $flyarr = $msql->arr("select * from `{$tb_flyinfo}` where userid='{$userid}' and id='{$fid}' and isable=1", 1);
} else {
    $flyarr = $msql->arr("select * from `{$tb_flyinfo}` where userid='{$userid}' and isable=1", 1);
}
$stop = false;
$connect= false;
foreach ($flyarr as $key => $fly) {
    $sqlw = [];
    if ($fly["zhidinguser"] != "") {
        $zuser = explode(',', strtoupper($fly["zhidinguser"]));
        $uarr = $msql->arr("select userid from `{$tb_user}` where username in('" . implode("','", $zuser) . "')", 1);
        foreach ($uarr as $k => $v) {
            $sqlw[] = " userid=" . $v["userid"];
        }
    }
    if ($fly["zhidingagent"] != "") {
        $zagent = explode(',', strtoupper($fly["zhidingagent"]));
        $aarr = $msql->arr("select userid,layer from `{$tb_user}` where username in('" . implode("','", $zagent) . "')", 1);
        foreach ($aarr as $k => $v) {
            $sqlw[] = " uid" . $v["layer"] . "=" . $v["userid"];
        }
    }
    if (count($sqlw) > 0) {
        $sqlw = " and (" . implode(' or ', $sqlw) . ") ";
    } else {
        $sqlw = "";
    }
    $flyjiabei = $fly["flyjiabei"];
    $stopying = $fly["stopying"];
    $stopshu = $fly["stopshu"];
    $cookie_jar = '../upload/cookie/' . $fly['userid'] . $fly["id"] . ".txt";
  $time = substr(time(),-2);  
  if($time%2==0){
    switch ($fly['webtype']) {
        case 'DL':
            $url = trim($fly['url1']) . '/user/getUserInfo';
            $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "location" => true];
            $res = CURL($send);
            if (strpos($res['res'], "currentPoint") == false) {
                $ip = Rand_IP();
                $fly["ip"] = $ip;
                $msql->query("update `{$tb_flyinfo}` set ip='{$ip}' where id='{$fly["id"]}'");
                dl_autologin($fly, $cookie_jar);
            }
            $res = json_decode($res["res"], true);
            if ($res['todayResult'] >= $stopying) {
                $msql->query("update `{$tb_flyinfo}` set flyjiabei='100' where id='{$fly['id']}'");
            } else {
                if (abs($res['todayResult']) >= $stopshu) {
                    $msql->query("update `{$tb_flyinfo}` set flyjiabei='100' where id='{$fly['id']}'");
                }
            }
            break;
        case 'ZYSIX':
            $url = trim($fly['url1']) . '/vip/cust_info.php';
            $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "location" => true];
            $res = CURL($send);
            if (strpos($res['res'], "id=\"creditAmt\"") == false) {
                unlink($cookie_jar);
                $ip = Rand_IP();
                $fly["ip"] = $ip;
                $msql->query("update `{$tb_flyinfo}` set ip='{$ip}' where id='{$fly["id"]}'");
                zysix_autologin($fly, $cookie_jar);
            }
            break;
        case "IDC":
             $refer = trim($fly['url3'])."ch/main.aspx";
             $url = trim($fly['url3']) . 'app/ws_member.asmx/MembersInfo_Data';
             $data="";
             $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $data, "head" => false,"refer"=>$refer,"json"=>true];
             $res = CURL($send);
               
             $res = str_replace('\\', '', $res["res"]);
             $res = explode('{"Rows":[', $res);
             $res = explode(']}]', $res[1]);
             $data = json_decode($res[0],true);

             if(is_numeric($data["allowcreditquota"])){
                $connect = true;
             }
             if ($res['usecreditquota2'] >= $stopying && $res['usecreditquota2']!="") {
                $msql->query("update `{$tb_flyinfo}` set flyjiabei='100' where id='{$fly['id']}'");
                $stop= true;
            } else {
                if (abs($res['usecreditquota2']) >= $stopshu && $res['usecreditquota2']!="") {
                    $msql->query("update `{$tb_flyinfo}` set flyjiabei='100' where id='{$fly['id']}'");
                    $stop= true;
                }
            }
        break;    
        case "SGWIN":
            $url = trim($fly['url1']) . '/member/accounts';
            $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false];
            $res = CURL($send);
            $res = json_decode($res["res"], true);
            $res = $res[0];
            if(is_numeric($res["balance"])){
                $connect = true;
            }
            if ($res['result'] >= $stopying && $res['result']!="") {
                $msql->query("update `{$tb_flyinfo}` set flyjiabei='100' where id='{$fly['id']}'");
                $stop= true;
            } else {
                if (abs($res['result']) >= $stopshu && $res['result']!="") {
                    $msql->query("update `{$tb_flyinfo}` set flyjiabei='100' where id='{$fly['id']}'");
                    $stop= true;
                }
            }
            break;
        case "BWSSC":
            $url = trim($fly['url1']) . '/User/Home/getuserinfo?UID=' . $fly["uid"];
            $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false];
            $res = CURL($send);
            $res = json_decode($res["res"], true);
            if(is_numeric($res['info']['money_win'])){
                $connect = true;
            }
            if ($res['info']['money_win'] >= $stopying) {
                $msql->query("update `{$tb_flyinfo}` set flyjiabei='100' where id='{$fly['id']}'");
                $stop= true;
            } else {
                if (abs($res['info']['money_win']) >= $stopshu) {
                    $msql->query("update `{$tb_flyinfo}` set flyjiabei='100' where id='{$fly['id']}'");
                    $stop= true;
                }
            }
            break;
    }
    if(!$connect){
        continue;
    }
  }
    $garr = json_decode($fly["game"], true);
    $g1[0]['gid'] = 172;
    $g1[0]['ifok'] = 0; 
    $g1[1]['gid'] = 108;
    $g1[1]['ifok'] = 0; 
    $g1[2]['gid'] = 170;
    $g1[2]['ifok'] = 0;  
    $g1[3]['gid'] = 171;
    $g1[3]['ifok'] = 0; 
    foreach($garr as $k => $v){
        $v['gid']==172 && $g1[0]['ifok']=$v['ifok'];
        $v['gid']==108 && $g1[1]['ifok']=$v['ifok'];
        $v['gid']==170 && $g1[2]['ifok']=$v['ifok'];
        $v['gid']==171 && $g1[3]['ifok']=$v['ifok'];
    }
    $garr = array_merge($g1,$garr);
    //print_r($garr);
    foreach ($garr as $k => $v) {
        if ($v['ifok'] != 1) {
            continue;
        }
        $gid = $v['gid'];
        $msql->query("select fenlei,panstatus,upqishu,thisqishu from `{$tb_game}` where gid='{$gid}'");
        $msql->next_record();
        $fenlei = $msql->f('fenlei');
        $qishu = $msql->f('thisqishu');
        if($msql->f("panstatus")==0){
            $qishu = $msql->f("upqishu");
        }
        //$msql->query("select qishu from `{$tb_kj}` where gid='{$gid}' and dates='{$dates}' and kjtime>NOW() order by qishu asc limit 1");
        //$msql->next_record();
        //$qishu = $msql->f('qishu');
        if ($fly["searchcode"] == '8086') {
            error_reporting(E_ALL);
            require "./plan.php";
            $lib = plan($gid, $qishu);
            if (count($lib) == 0) {
                continue;
            }
            if($stop){
                continue;
            }
        } else {
            //echo "select id,bid,sid,cid,pid,content,bz,ab,points1 as points,peilv1 from `{$tb_lib}` where gid='{$gid}' and qishu='{$qishu}' $sqlw and zc0>0  group by cid,pid,content";
            $arr = $msql->arr("select id,bid,sid,cid,pid,content,bz,ab,points1 as points,peilv1 from `{$tb_lib}` where gid='{$gid}' and qishu='{$qishu}' {$sqlw} and zc0>0  group by cid,pid,content", 1);
            if (count($arr) == 0) {
                continue;
            }
            $lib = [];
            $i = 0;
            $tmpbid = 0;
            $tmpsid = 0;
            $tmpcid = 0;
            $tmppid = 0;
            //print_r($arr);
            foreach ($arr as $k => $v) {
                $bid = $v['bid'];
                //if($bid!=23378692) continue;
                $sid = $v['sid'];
                $cid = $v['cid'];
                $pid = $v['pid'];
                if ($tmpcid != $v['cid']) {
                    $carr = $fsql->arr("select mtype,ftype,dftype from `{$tb_class}` where gid='{$gid}' and bid='{$bid}' and cid='{$cid}'", 1);
                    $carr = $carr[0];
                    $ftype = $carr["ftype"];
                    $dftype = $carr["dftype"];
                    $mtype = $carr["mtype"];
                    $fsql->query("select maxje,je,ifok from `{$tb_fly}` where gid='{$gid}' and userid='{$userid}' and class='{$ftype}'");
                    $fsql->next_record();
                    $maxje = pr0($fsql->f('maxje'));
                    $limitje = pr0($fsql->f('je'));
                    $ifok = $fsql->f("ifok");
                }
                if ($ifok != 1) {
                    continue;
                }
                if ($tmppid != $pid) {
                    $parr = $fsql->arr("select name from `{$tb_play}` where gid='{$gid}' and bid='{$bid}' and pid='{$pid}'", 1);
                    $pname = $parr[0]['name'];
                }
                $je = 0;
                $msql->query("select sum(je*zc0/100),content from `{$tb_lib}` where gid='{$gid}' and qishu='{$qishu}' and pid='{$v['pid']}' {$sqlw} and content='{$v['content']}'");
                $msql->next_record();
                $zcje = $msql->f(0);
                $msql->query("select sum(je),content  from `{$tb_lib}` where gid='{$gid}' and qishu='{$qishu}' and pid='{$v['pid']}' and userid='{$userid}' and bz='{$fly['id']}' and xtype=2 and content='{$v['content']}'");
                $msql->next_record();
                $yfje = $msql->f(0);
                $yfje = $yfje / ($flyjiabei / 100);
                $je = floor($zcje - $yfje - $limitje);
                //echo $limitje;
                //echo $je,"<Br>";
                if ($je >= 1) {
                    if ($je > $maxje) {
                        $je = $maxje;
                    }
                    $je = floor($je * $flyjiabei / 100);
                    if ($je < 1) {
                        continue;
                    }
                    $lib[$i] = $v;
                    $lib[$i]['je'] = $je;
                    $lib[$i]['mtype'] = $mtype;
                    $lib[$i]['ftype'] = $ftype;
                    $lib[$i]['dftype'] = $dftype;
                    $lib[$i]['name'] = $pname;
                    $i++;
                }
            }
            if (count($lib) == 0) {
                continue;
            }
        }
        //print_r($lib);
        switch ($fly['webtype']) {
            case 'WS':
                $arr = exews($gid, $fenlei, $lib, $qishu);
                $type = $arr['type'];
                $lib = $arr['lib'];
                unset($arr['type']);
                unset($arr['lib']);
                $type = '["' . implode('","', $type) . '"]';
                //print_r($arr);
                $send = [];
                $send['lotteryId'] = getwsgame($gid);
                $url = trim($fly['url1']) . '/' . $fly['cookie'] . '/Bet/GetBetPageInit/?marketTypes=' . $type . '&lotteryId=' . $send['lotteryId'] . '&_=' . time();
                $sendx = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($sendx);
                //$res = curls($cookie_jar, $url, false, [], true, false);
                //echo $res['res'];
                //print_r($arr);
                $res = json_decode($res['res'], true);
                $bet = $res['Data']['OddsData'];
                //print_r($bet);
                foreach ($arr['BetItems'] as $k => $v) {
                    foreach ($bet as $k1 => $v1) {
                        if ($v['BetNo'] == $v1['BetNo']) {
                            $arr['BetItems'][$k]['Odds'] = $v1['Odds'];
                        }
                    }
                }
                $send['betParams'] = json_encode($arr);
                //print_r($send);
                //exit;
                $url = trim($fly['url1']) . '/' . $fly['cookie'] . '/Bet/DoBet/';
                $sendx = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $send, "head" => false];
                $res = CURL($sendx);
                //$res = curls($cookie_jar, $url, true, $send, true, false);
                //echo $res['res'];
                $res = json_decode($res['res'], true);
                //print_r($res);
                $msql->query("delete from `{$tb_libu}` where userid='{$userid}'");
                $tid = setuptid();
                $success = false;
                foreach ($res['Data']['BetItems'] as $k => $v) {
                    if ($v['Status'] == 1) {
                        foreach ($lib as $k1 => $v1) {
                            if ($v['BetNo'] == $v1['BetNo']) {
                                $sql = "insert into `{$tb_libu}` set gid='{$gid}',bid='{$v1['bid']}',sid='{$v1['sid']}',cid='{$v1['cid']}',pid='{$v1['pid']}',userid='{$userid}',je='{$v1['je']}',xtype='2',points='{$v1['points']}',peilv1='{$v['Odds']}',tid='{$tid}',ip=INET_ATON('127.0.0.1'),code='',flytype=2,z='9',bs=1,qishu='{$qishu}',content='{$v1['content']}',dates='{$dates}',time=NOW(),abcd='A',ab='A',sv='1',bz='{$fly['id']}'";
                                $msql->query($sql);
                                $tid++;
                            }
                        }
                        $success = true;
                    }
                }
                if ($success) {
                    $msql->query("insert into `{$tb_lib}` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb_libu}` where userid='{$userid}' order by id");
                    $msql->query("delete from x_libu where userid='{$userid}'");
                }
                break;
            case "IDC":
              /*
             Referer: https://53jndgw.yn523.com/cp10_07_mb/ch/bjsc_twosides.aspx?gameno=11
             stype=checkxiadan&gameno=11&roundno=749759&wagerroundno=C&wagers=612%3A1%3A3%3B612%3A2%3A3%3B623%3A1%3A3%3B623%3A2%3A3%3B

              //https://53jndgw.yn523.com/cp10_07_mb/ashx/orderHandler.ashx
              

              Referer: https://53jndgw.yn523.com/cp10_07_mb/ch/left.aspx
              {wagerround:"C",transtring:"612,,1,,1.937,3;612,,2,,1.937,3;623,,1,,1.937,3;623,,2,,1.937,3;",arrstring:"612:1:3;612:2:3;623:1:3;623:2:3;",wagetype:0,allowcreditquota:1000,hasToken:true,playgametype:0}
              https://53jndgw.yn523.com/cp10_07_mb/ch/left.aspx/GetMemberMtran
              
              {"d":"\u003ctable width=\"100%\" cellpadding=\"0\" cellspacing=\"1\"   class=\u0027DTable\u0027 align=\"center\" id=\"tb_mtranlist\"  border=\"0\" style=\"min-width:228px;margin-top: 0px; word-break: break-all; word-wrap: break-word\"\u003e\u003ctr style=\"height: 33px; text-align: left; background: url(../images/tb_bg.jpg); color: Black; font-size: 14px;\"\u003e\u003ctd colspan=\"2\" nowrap align=\"center\" id=\"td_mtranList_title\"\u003e\u003cspan style=\"font-size: 14px; font-weight: bold;\"\u003e下注结果反馈\u003c/span\u003e\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd width=\"70px\" align=\"center\" style=\"line-height: 17px;\" class=\"t_td_caption_1\"\u003e会员帐号\u003c/td\u003e\u003ctd align=\"left\"\u003eku9978(C)盘\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd width=\"70px\" align=\"center\" style=\"line-height: 17px;\" class=\"t_td_caption_1\"\u003e可用金额\u003c/td\u003e\u003ctd align=\"left\"\u003e\u003cspan name=\"allowprice\"\u003e988\u003c/span\u003e\u003c/td\u003e\u003c/tr\u003e\u003ctr class=\"tr_print\"\u003e\u003ctd colspan=\"2\" style=\"height: 33px; text-align: center; background: url(../images/td_but.jpg);\"\u003e\u003cinput type=\"button\" value=\"打 印\" onmouseover=\"this.className=\u0027button_bg2\u0027\" onmouseout=\"this.className=\u0027button_bg1\u0027\" class=\"button_bg1\" onclick=\"OrderPrint()\"/\u003e\u0026nbsp;\u003cinput type=\"button\" value=\"返 回\" onmouseover=\"this.className=\u0027button_bg2\u0027\" onmouseout=\"this.className=\u0027button_bg1\u0027\" class=\"button_bg1\"   onclick=\"toback()\"/\u003e\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:20px;line-height:20px;\"\u003e\u003ctd colspan=\"2\" align=\"center\" id=\"Current_Round\"style=\"font-size: 14px;height:20px;line-height:20px; font-weight: bold;\"\u003e\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd colspan=\"2\" align=\"center\" id=\"Current_XiaZhu\"\u003e\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd  align=\"center\"\u003e下注笔数\u003c/td\u003e\u003ctd align=\"left\" style=\u0027padding-left:5px\u0027\u003e4 笔\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd  align=\"center\"\u003e合计注额\u003c/td\u003e\u003ctd align=\"left\" style=\u0027padding-left:5px\u0027\u003e￥ 12\u003c/td\u003e\u003c/tr\u003e\u003ctr class=\"tr_print followbet\" style=\"display:none;\"\u003e\u003ctd align=\"center\" colspan=\"2\"\u003e\u003cinput type=\"button\" value=\"发 布\" onmouseover=\"this.className = \u0027button_bg2\u0027\" onmouseout=\"this.className = \u0027button_bg1\u0027\" class=\"button_bg1\" onclick=\"sendFollowBet(\u0027612:1:3;612:2:3;623:1:3;623:2:3;\u0027)\"\u003e\u003c/td\u003e\u003c/tr\u003e\u003c/table\u003e$@click_conform(\u0027C\u0027,\u0027612:1:3;612:2:3;623:1:3;623:2:3;\u0027)$@\u003cdiv style=\"font-size:14px; text-align:center; color:#000; line-height:30px;\"\u003e下注明细如下， 是否确定？\u003c/div\u003e\u003ctable border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"tbline\" style=\"text-align:center;\"\u003e\u003ctr class=\"trH\"\u003e\u003ctd colspan=\"2\"\u003e下注内容\u003c/td\u003e\u003ctd\u003e赔率\u003c/td\u003e\u003ctd\u003e金额\u003c/td\u003e\u003c/tr\u003e\u003ctr class=\"trH\"\u003e\u003ctd\u003e共4笔\u003c/td\u003e\u003ctd\u003e\u0026nbsp;\u003c/td\u003e\u003ctd\u003e\u0026nbsp;\u003c/td\u003e\u003ctd\u003e12\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd style=\"text-align:left;\" class=\"pop_title\"\u003e亚军单双\u003c/td\u003e\u003ctd\u003e单\u003c/td\u003e\u003ctd\u003e1.937\u003c/td\u003e\u003ctd\u003e3\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd style=\"text-align:left;\" class=\"pop_title\"\u003e亚军单双\u003c/td\u003e\u003ctd\u003e双\u003c/td\u003e\u003ctd\u003e1.937\u003c/td\u003e\u003ctd\u003e3\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd style=\"text-align:left;\" class=\"pop_title\"\u003e第三名大小\u003c/td\u003e\u003ctd\u003e大\u003c/td\u003e\u003ctd\u003e1.937\u003c/td\u003e\u003ctd\u003e3\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd style=\"text-align:left;\" class=\"pop_title\"\u003e第三名大小\u003c/td\u003e\u003ctd\u003e小\u003c/td\u003e\u003ctd\u003e1.937\u003c/td\u003e\u003ctd\u003e3\u003c/td\u003e\u003c/tr\u003e\u003c/table\u003e$@12$@988$@亚军单双△单△1.937△3★亚军单双△双△1.937△3★第三名大小△大△1.937△3★第三名大小△小△1.937△3$@EB6DF369C3001B71BF56BBE5A366E963"}


            Referer: https://53jndgw.yn523.com/cp10_07_mb/ch/left.aspx
            https://53jndgw.yn523.com/cp10_07_mb/ch/left.aspx/ToCheckIn
            {memberno:"ku9978",submemberno:"",gameno:11,wagerroundno:"C",valstring:"612:1:1.937;612:2:1.937;623:1:1.937;623:2:1.937;"}
            {"d":"[\"\",\"\",{\"Rows\":[]}]"}

            Referer: https://53jndgw.yn523.com/cp10_07_mb/ch/left.aspx
            https://53jndgw.yn523.com/cp10_07_mb/ch/left.aspx/mtran_XiaDan_New

            {gameno:11,wagerroundstring:"C",arrstring:"612:1:3;612:2:3;623:1:3;623:2:3;",roundno:"749759",lianma_transtrin:"",token:"EB6DF369C3001B71BF56BBE5A366E963"}

            {"d":"$@\u003ctable width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"color:#000;font-family:Verdana,\u0027宋体\u0027,Arial,Sans;height:66px;min-width:228px; word-break: break-all; word-wrap: break-word;border-bottom:1px solid #e9a884;\"\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd align=center style=\"width:26%;height:16px;line-height:16px;\"\u003e注单号：\u003c/td\u003e\u003ctd width=\"74%\" align=left style=\"height:16px;line-height:16px;\"\u003e1867022217#\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;\" colSpan=2 align=center\u003e\u003cspan style=\"color:blue;\"\u003e第三名大小  小\u003c/span\u003e@\u0026nbsp;\u003cspan style=\"color:red;\"\u003e1.937\u003c/span\u003e\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;width:58px;\" align=center\u003e下注额：\u003c/td\u003e\u003ctd align=left style=\"height:16px;line-height:16px;\"\u003e3.00\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;width:58px;\" align=center\u003e可赢额：\u003c/td\u003e\u003ctd align=left style=\"height:16px;line-height:16px;\"\u003e2.81\u003c/td\u003e\u003c/tr\u003e\u003c/table\u003e\u003ctable width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"color:#000;font-family:Verdana,\u0027宋体\u0027,Arial,Sans;height:66px;min-width:228px; word-break: break-all; word-wrap: break-word;border-bottom:1px solid #e9a884;\"\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd align=center style=\"width:26%;height:16px;line-height:16px;\"\u003e注单号：\u003c/td\u003e\u003ctd width=\"74%\" align=left style=\"height:16px;line-height:16px;\"\u003e1867022216#\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;\" colSpan=2 align=center\u003e\u003cspan style=\"color:blue;\"\u003e第三名大小  大\u003c/span\u003e@\u0026nbsp;\u003cspan style=\"color:red;\"\u003e1.937\u003c/span\u003e\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;width:58px;\" align=center\u003e下注额：\u003c/td\u003e\u003ctd align=left style=\"height:16px;line-height:16px;\"\u003e3.00\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;width:58px;\" align=center\u003e可赢额：\u003c/td\u003e\u003ctd align=left style=\"height:16px;line-height:16px;\"\u003e2.81\u003c/td\u003e\u003c/tr\u003e\u003c/table\u003e\u003ctable width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"color:#000;font-family:Verdana,\u0027宋体\u0027,Arial,Sans;height:66px;min-width:228px; word-break: break-all; word-wrap: break-word;border-bottom:1px solid #e9a884;\"\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd align=center style=\"width:26%;height:16px;line-height:16px;\"\u003e注单号：\u003c/td\u003e\u003ctd width=\"74%\" align=left style=\"height:16px;line-height:16px;\"\u003e1867022215#\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;\" colSpan=2 align=center\u003e\u003cspan style=\"color:blue;\"\u003e亚军单双  双\u003c/span\u003e@\u0026nbsp;\u003cspan style=\"color:red;\"\u003e1.937\u003c/span\u003e\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;width:58px;\" align=center\u003e下注额：\u003c/td\u003e\u003ctd align=left style=\"height:16px;line-height:16px;\"\u003e3.00\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;width:58px;\" align=center\u003e可赢额：\u003c/td\u003e\u003ctd align=left style=\"height:16px;line-height:16px;\"\u003e2.81\u003c/td\u003e\u003c/tr\u003e\u003c/table\u003e\u003ctable width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"color:#000;font-family:Verdana,\u0027宋体\u0027,Arial,Sans;height:66px;min-width:228px; word-break: break-all; word-wrap: break-word;\"\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd align=center style=\"width:26%;height:16px;line-height:16px;\"\u003e注单号：\u003c/td\u003e\u003ctd width=\"74%\" align=left style=\"height:16px;line-height:16px;\"\u003e1867022214#\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;\" colSpan=2 align=center\u003e\u003cspan style=\"color:blue;\"\u003e亚军单双  单\u003c/span\u003e@\u0026nbsp;\u003cspan style=\"color:red;\"\u003e1.937\u003c/span\u003e\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;width:58px;\" align=center\u003e下注额：\u003c/td\u003e\u003ctd align=left style=\"height:16px;line-height:16px;\"\u003e3.00\u003c/td\u003e\u003c/tr\u003e\u003ctr style=\"height:16px;line-height:16px;\"\u003e\u003ctd style=\"height:16px;line-height:16px;width:58px;\" align=center\u003e可赢额：\u003c/td\u003e\u003ctd align=left style=\"height:16px;line-height:16px;\"\u003e2.81\u003c/td\u003e\u003c/tr\u003e\u003c/table\u003e"}


             arrstring=621:1:2;621:2:2;611:1:2;611:2:2;631:1:2;631:2:2;
arrstring=622:1:2;622:2:2;632:1:2;632:2:2;



             */
             $idc_gameno = idc_gameno($gid);
             $refer = trim($fly['url3']) . "ch/bjsc_twosides.aspx?gameno=".$idc_gameno;             
             $idc_class = idc_classarr($gid,$lib);
             $url = trim($fly['url3']) . "ashx/orderHandler.ashx?stype=getoddsbytype&gameno=$idc_gameno&wagerroundno={$fly['abcd']}&oddsgroupnos=".$idc_class."&ts=".time();;
             //$data = IDC_JSONb($gid,$lib,$qishu,$fly);
             //echo json_encode($data);
             $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => fasle, "postdata" => [], "head" => false, "refer"=>$refer];
             $res = CURL($send);
             $pl = json_decode($res["res"],true);
             //print_r($pl);
 
             $refer = trim($fly['url3']) . "ch/left.aspx";
             $url = trim($fly['url3']) . "ch/left.aspx/GetMemberMtran";
             $data = IDC_JSONa($gid,$qishu,$fly,$pl);
             //echo json_encode($data);
             $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $data, "head" => false, "json" => true,"refer"=>$refer];
             $res = CURL($send);
             $res = explode('$@', $res["res"]);
             //print_r($res);
             $token = substr($res[6],0,32);

             $refer = trim($fly['url3']) . "ch/left.aspx";
             $url = trim($fly['url3']) . "ch/left.aspx/mtran_XiaDan_New";
             $data = IDC_JSONc($gid,$lib,$qishu,$fly,$token);
             //echo json_encode($data);
             $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $data, "head" => false, "json" => true,"refer"=>$refer];
             $res = CURL($send);
             
             //echo $res["res"];
             //echo json_encode($lib);
             if(strpos($res["res"],"注单号")!==fasle && strpos($res["res"],"可赢额")!==fasle){
                 $msql->query("delete from `{$tb_libu}` where userid='{$userid}'");
                 $tid = setuptid();
                 foreach ($lib as $k => $v1) {
                     $sql = "insert into `{$tb_libu}` set gid='{$gid}',bid='{$v1['bid']}',sid='{$v1['sid']}',cid='{$v1['cid']}',pid='{$v1['pid']}',userid='{$userid}',je='{$v1['je']}',xtype='2',points='{$v1['points']}',peilv1='{$v1['pl']}',tid='{$tid}',ip=INET_ATON('127.0.0.1'),code='',flytype=2,z='9',bs=1,qishu='{$qishu}',content='{$v1['content']}',dates='{$dates}',time=NOW(),abcd='{$fly['abcd']}',ab='A',sv='1',bz='{$fly['id']}'";
                     $msql->query($sql);
                     $tid++;
                 }
                 $msql->query("insert into `{$tb_lib}` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb_libu}` where userid='{$userid}' order by id");
                 $msql->query("delete from x_libu where userid='{$userid}'");
             }
            break;    
            case 'SGWIN':
                $url = trim($fly['url1']) . "/member/bet";
                $data = SGWIN_JSON($gid, $lib, $qishu, $fenlei);
                //print_r($data);
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $data, "head" => false, "json" => true];
                $res = CURL($send);
                $list = [];
                $list["userid"] = $userid;
                $list["sendtext"] = json_encode($data);
                $list["retext"] = $res["res"];
                $list["gid"] = $gid;
                $list["qishu"] = $qishu;
                $list["url"] = $url;
                $list["webtype"] = $fly["webtype"].'_'.$fly['id'];
                $list["time"] = date("Y-m-d H:i:s");
                flylog($list);
                //echo $res["res"];
                //print_r($data);
                //$res = curlsjson($cookie_jar, $url, true, $data, true,false);
                //echo $res['res'];
                $res = json_decode($res['res'], true);
                //print_r($res);
                //print_r($lib);
                //print_r($data['bets']);
                $msql->query("delete from `{$tb_libu}` where userid='{$userid}'");
                $tid = setuptid();
                $success = false;
                foreach ($data['bets'] as $k => $v) {
                    if ($res['ids'][$k] != "") {
                        foreach ($lib as $k1 => $v1) {
                            if ($v['pid'] == $v1['pid'] & $v['content'] == $v1['content']) {
                                $sql = "insert into `{$tb_libu}` set gid='{$gid}',bid='{$v1['bid']}',sid='{$v1['sid']}',cid='{$v1['cid']}',pid='{$v1['pid']}',userid='{$userid}',je='{$v1['je']}',xtype='2',points='{$v1['points']}',peilv1='{$res['odds'][$k]}',tid='{$tid}',ip=INET_ATON('127.0.0.1'),code='',flytype=2,z='9',bs=1,qishu='{$qishu}',content='{$v1['content']}',dates='{$dates}',time=NOW(),abcd='A',ab='A',sv='1',bz='{$fly['id']}'";
                                $msql->query($sql);
                                //echo $sql;
                                $tid++;
                            }
                        }
                        $success = true;
                    }
                }
                if ($success) {
                    $msql->query("insert into `{$tb_lib}` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb_libu}` where userid='{$userid}' order by id");
                    $msql->query("delete from x_libu where userid='{$userid}'");
                }
                break;
            case "DL":
                $game = DL_getgametype($gid);
                $url = trim($fly['url1']) . "/" . $game . "/renewInfo";
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                //$res = curlsjson($cookie_jar, $url, false, "", true,false);
                $res = json_decode($res['res'], true);
                $data = DL_JSON($gid, $lib, $qishu, $fenlei, $res["betRate"]);
                //print_r($data);
                $post = [];
                foreach ($data as $k => $v) {
                    $post["betAry[" . $k . "][detailID]"] = $v["detailID"];
                    $post["betAry[" . $k . "][betMoney]"] = $v["betMoney"];
                    $post["betAry[" . $k . "][betRate]"] = $v["betRate"];
                }
                //print_r($post);
                $url = trim($fly['url1']) . "/" . $game . "/bet";
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $post, "head" => false];
                $res = CURL($send);
                //echo $url;
                //$res = curls($cookie_jar, $url, true, $post, true, false);
                //echo $res['res'];
                $res = json_decode($res['res'], true);
                //print_r($res);
                $msql->query("delete from `{$tb_libu}` where userid='{$userid}'");
                $tid = setuptid();
                $success = false;
                foreach ($data as $k => $v) {
                    if ($res["pass"] == true) {
                        foreach ($lib as $k1 => $v1) {
                            if ($v['pid'] == $v1['pid']) {
                                $sql = "insert into `{$tb_libu}` set gid='{$gid}',bid='{$v1['bid']}',sid='{$v1['sid']}',cid='{$v1['cid']}',pid='{$v1['pid']}',userid='{$userid}',je='{$v1['je']}',xtype='2',points='{$v1['points']}',peilv1='{$v['betRate']}',tid='{$tid}',ip=INET_ATON('127.0.0.1'),code='',flytype=2,z='9',bs=1,qishu='{$qishu}',content='{$v1['content']}',dates='{$dates}',time=NOW(),abcd='A',ab='A',sv='1',bz='{$fly['id']}'";
                                $msql->query($sql);
                                $tid++;
                            }
                        }
                        $success = true;
                    }
                }
                if ($success) {
                    $msql->query("insert into `{$tb_lib}` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb_libu}` where userid='{$userid}' order by id");
                    $msql->query("delete from x_libu where userid='{$userid}'");
                }
                break;
            case "ZYSIX":
                $game = ZYSIX_JSON($gid, $lib, $qishu, $fenlei);
                $his = date("YmdHis");
                $msql->query("delete from x_libu where userid='{$userid}'");
                $tid = setuptid();
                $dftype = 999;
                header("Content-type:text/html;charset=gbk");
                $play = [];
                $pl = [];
                $je = [];
                foreach ($game as $k => $v) {
                    if ($v["content"] == "") {
                        $play[] = ZYSIX_gcode($v["dftype"], $v['name']);
                        $pl[] = 1;
                        $je[] = $v["je"];
                        //if($v["dftype"]!=$game[$k+1]["dftype"]){
                        $garr = [];
                        $garr[] = $v["gtype"];
                        $garr[] = $v["gclass"];
                        $garr[] = ZYSIX_gplay2($v["dftype"]);
                        $garr[] = $his;
                        $garr[] = 0;
                        $garr[] = 0;
                        $garr[] = 0;
                        $garr[] = implode('|', $play);
                        //$url = trim($fly['url1']) . "/vip/online/getInfoRate.php?gClass=".$v["gclass"]."&gPro=".$garr[2]."&datetime=&time=".BW_time();
                        //$send = ["headip"=>$fly["ip"],"cookietype"=>true,"cookie_jar"=>$cookie_jar,"url"=>$url,"posttype"=>false];
                        //$res = CURL($send);
                        //echo $res["res"];
                        //$pl = ZYSIX_pl2($play,$res["res"]);
                        //print_r($pl);
                        $garr[] = implode('|', $pl);
                        $garr[] = implode('|', $je);
                        $url = trim($fly['url1']) . "/vip/gOrderA.php?gStr=" . implode('@', $garr) . "&time=" . BW_time();
                        $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false];
                        $res = CURL($send);
                        if (strpos($res["res"], '@') !== false) {
                            $pl = explode('@', $res["res"]);
                            $garr[8] = $pl[1];
                            $url = trim($fly['url1']) . "/vip/gOrderA.php?gStr=" . implode('@', $garr) . "&time=" . BW_time();
                            $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false];
                            $res = CURL($send);
                        }
                        //echo '123'."\r\n";
                        if (strpos($res["res"], 'ok') !== false && strpos($res["res"], '@') == false) {
                            $peilv2 = 0;
                            $sql = "insert into `{$tb_libu}` set gid='{$gid}',bid='{$v['bid']}',sid='{$v['sid']}',cid='{$v['cid']}',pid='{$v['pid']}',userid='{$userid}',je='{$v['je']}',xtype='2',points='{$v['points']}',peilv1='{$v['peilv1']}',peilv2='{$peilv2}',tid='{$tid}',ip=INET_ATON('127.0.0.1'),code='',flytype=2,z='9',bs=1,qishu='{$qishu}',content='{$v['content']}',dates='{$dates}',time=NOW(),abcd='A',ab='A',sv='1',bz='{$fly['id']}'";
                            $msql->query($sql);
                            $tid++;
                        }
                        $play = [];
                        $pl = [];
                        $je = [];
                        //}
                    }
                }
                $msql->query("insert into `{$tb_lib}` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb_libu}` where userid='{$userid}' order by id");
                $msql->query("delete from x_libu where userid='{$userid}'");
                foreach ($game as $k => $v) {
                    if ($v["content"] != "") {
                        $garr = [];
                        $garr[] = $v["gtype"];
                        $garr[] = $v["gclass"];
                        $garr[] = $v["gplay"];
                        $garr[] = $his;
                        $garr[] = 0;
                        $garr[] = 0;
                        $garr[] = 0;
                        $con = str_replace('-', ',', $v["content"]);
                        $garr[] = ZYSIX_con($con, $v["dftype"]);
                        $garr[] = implode(',', ZYSIX_pl($v["content"]));
                        $garr[] = $v["je"];
                        $url = trim($fly['url1']) . "/vip/gOrderB.php?gStr=" . implode('@', $garr) . "&time=" . BW_time();
                        $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false];
                        $res = CURL($send);
                        $r = $res["res"];
                        echo "\r\n";
                        if (strpos($r, "<span title=") !== false) {
                            preg_match_all("/<span title='(.*)'>/isU", $r, $output);
                            //print_r($output);
                            $garr[8] = $output[1][0];
                            //print_r($garr);
                            $url = trim($fly['url1']) . "/vip/gOrderB.php?gStr=" . implode('@', $garr) . "&time=" . BW_time();
                            $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false];
                            $res = CURL($send);
                        }
                        if (strpos($res["res"], 'ok') !== false && strpos($res["res"], $garr[8]) !== false) {
                            $peilv2 = 0;
                            $sql = "insert into `{$tb_libu}` set gid='{$gid}',bid='{$v['bid']}',sid='{$v['sid']}',cid='{$v['cid']}',pid='{$v['pid']}',userid='{$userid}',je='{$v['je']}',xtype='2',points='{$v['points']}',peilv1='{$v['peilv1']}',peilv2='{$peilv2}',tid='{$tid}',ip=INET_ATON('127.0.0.1'),code='',flytype=2,z='9',bs=1,qishu='{$qishu}',content='{$v['content']}',dates='{$dates}',time=NOW(),abcd='A',ab='A',sv='1',bz='{$fly['id']}'";
                            $msql->query($sql);
                            $tid++;
                        }
                        $his++;
                    }
                }
                $msql->query("insert into `{$tb_lib}` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb_libu}` where userid='{$userid}' order by id");
                $msql->query("delete from x_libu where userid='{$userid}'");
                break;
            case "BW":
                $tmppid = 99999;
                $pl = [];
                //print_r($lib);
                foreach ($lib as $k => $v) {
                    $propertyid = BW_propertyid($v["name"]);
                    $typeid = BW_gettype($v["dftype"]);
                    if ($tmppid !== $propertyid) {
                        if ($v["bid"] == 23378685 && count($pl) != 49) {
                            $url = trim($fly['url1']) . "/online/info?UID=" . $fly["uid"] . "&type=" . $typeid . "&order_type=B&r=" . time();
                            $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $val, "head" => false, "location" => false];
                            $res = CURL($send);
                            //echo $res["res"];
                            $pl = BW_getpls2($res["res"]);
                        } else {
                            if ($v["bid"] != 23378685) {
                                if ($v["content"] != "") {
                                    $url = trim($fly['url1']) . "/online/info2?UID=" . $fly["uid"] . "&type=" . $typeid . "&property=" . $propertyid . "&limit=&datetime=20110208210106&time=" . BW_time() . "&r=" . time();
                                    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $val, "head" => false, "location" => false];
                                    $res = CURL($send);
                                    $pl = BW_getpl($res["res"]);
                                } else {
                                    $url = trim($fly['url1']) . "/online/info?UID=" . $fly["uid"] . "&type=" . $typeid . "&limit=&datetime=20110208210106&time=" . BW_time() . "&r=" . time();
                                    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $val, "head" => false, "location" => false];
                                    $res = CURL($send);
                                    $pl = BW_getpls($res["res"]);
                                }
                            }
                        }
                    }
                    //print_r($pl);
                    $lib[$k]["pl"] = $pl;
                    $tmppid = $propertyid;
                }
                $data = BW_JSON($gid, $lib, $qishu, $fenlei);
                //print_r($data);
                //break;
                $msql->query("delete from `{$tb_libu}` where userid='{$userid}'");
                $tid = setuptid();
                $success = false;
                foreach ($data as $k => $v) {
                    $val = $v;
                    $url = trim($fly['url1']) . $v["url"] . "?UID=" . $fly["uid"];
                    //echo $url;
                    unset($val["pid"]);
                    unset($val["url"]);
                    unset($val["content"]);
                    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $val, "head" => false, "location" => false];
                    $res = CURL($send);
                    //$res = curls($cookie_jar,$url,true,$val,true,false,false);
                    //echo $res["res"];
                    if (strpos($res["res"], "下單完成") !== false || strpos($res["res"], "下注完成") !== false) {
                        //echo "aaaaaaaaaaaa";
                        if (strpos($res["res"], "賠率變化") !== false) {
                            continue;
                        }
                        //echo "bbbbbbb";
                        foreach ($lib as $k1 => $v1) {
                            if ($v['pid'] == $v1['pid'] && $v["content"] == $v1["content"]) {
                                if ($v["content"] == "") {
                                    preg_match_all('/<tr><td>(.*)下注完成!<\\/td><\\/tr>/isU', $res["res"], $output);
                                    $peilv1 = explode(' ', $output[1][0]);
                                    $peilv1 = $peilv1[1];
                                } else {
                                    preg_match_all('/賠率:<span style=\'color:red\'>(.*)<\\/span>/isU', $res["res"], $output);
                                    $peilv1 = $output[1][0];
                                    $peilv1 = explode(',', $peilv1);
                                    $peilv1 = $peilv1[0];
                                }
                                $peilv2 = 0;
                                //echo $peilv1;
                                if (strpos($peilv1, '/') !== false) {
                                    $peilv1 = explode('/', $peilv1);
                                    //print_r($peilv1);
                                    $peilv2 = $peilv1[1];
                                    $peilv1 = $peilv1[0];
                                }
                                $sql = "insert into `{$tb_libu}` set gid='{$gid}',bid='{$v1['bid']}',sid='{$v1['sid']}',cid='{$v1['cid']}',pid='{$v1['pid']}',userid='{$userid}',je='{$v1['je']}',xtype='2',points='{$v1['points']}',peilv1='{$peilv1}',peilv2='{$peilv2}',tid='{$tid}',ip=INET_ATON('127.0.0.1'),code='',flytype=2,z='9',bs=1,qishu='{$qishu}',content='{$v1['content']}',dates='{$dates}',time=NOW(),abcd='A',ab='A',sv='1',bz='{$fly['id']}'";
                                //echo $sql;
                                $msql->query($sql);
                                $tid++;
                                $success = true;
                            }
                        }
                    }
                }
                if ($success) {
                    $msql->query("insert into `{$tb_lib}` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb_libu}` where userid='{$userid}' order by id");
                    $msql->query("delete from x_libu where userid='{$userid}'");
                }
                break;
            case "BWSSC":
                $gt = BWSSC_gt($gid);
                $grpid = [];
                foreach ($lib as $k => $v) {
                    $lib[$k]["code"] = BWSSC_code($gid, $fenlei, $v["mtype"], $v["name"]);
                    $tmp = BWSSC_grpid($gid, $fenlei, $v["mtype"], $v["name"]);
                    if (!in_array($tmp, $grpid)) {
                        $grpid[] = $tmp;
                    }
                }
                //print_r($grpid);
                $pl = [];
                foreach ($grpid as $k => $v) {
                    $url = trim($fly['url1']) . "/User/Bet/getplinfo";
                    $data = ["gt" => $gt, "grpid" => $v, "uid" => $fly["uid"], "prekjqs" => "", "r" => random()];
                    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $data, "head" => false, "location" => false];
                    $res = CURL($send);
                    //echo $res["res"];
                    //$res = curls($cookie_jar,$url,true,$data,true,false,false);
                    $pl = array_merge($pl, BWSSC_getpl($res["res"]));
                }
                //print_r($pl);
                $data = BWSSC_JSON($gid, $lib, $qishu, $fenlei, $pl);
                $uPI_ID = [];
                $uPI_P = [];
                $uPI_M = [];
                foreach ($data as $k => $v) {
                    if ($v["type"] == 1) {
                        $uPI_ID[] = $v["uPI_ID"];
                        $uPI_P[] = $v["uPI_P"];
                        $uPI_M[] = $v["uPI_M"];
                    }
                }
                //print_r($data);
                $tid = setuptid();
                $msql->query("delete from `{$tb_libu}` where userid='{$userid}'");
                if (count($uPI_ID) > 0) {
                    $send["gt"] = $gt;
                    $send["qs"] = BWSSC_qs($gid, $qishu);
                    $send["r"] = random();
                    $send["uid"] = $fly["uid"];
                    $send["uPI_ID"] = implode(',', $uPI_ID);
                    $send["uPI_P"] = implode(',', $uPI_P);
                    $send["uPI_M"] = implode(',', $uPI_M);
                    $url = trim($fly['url1']) . "/User/Bet/Betsave?" . http_build_query($send);
                    $sendx = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $send, "head" => false, "location" => false, "json" => true];
                    $res = CURL($sendx);
                    $list["userid"] = $userid;
                    $list["sendtext"] = json_encode($send);
                    $list["retext"] = $res["res"];
                    $list["gid"] = $gid;
                    $list["qishu"] = $qishu;
                    $list["url"] = $url;
                    $list["webtype"] = $fly["webtype"].'_'.$fly['id'];
                    $list["time"] = date("Y-m-d H:i:s");
                    flylog($list);
                    //echo $res["res"];
                    //$res = curlsjson($cookie_jar,$url,true,$send,true,false,false);
                    $arr = json_decode($res["res"], true);
                    if (strpos($res["res"], "投注成功") !== false) {
                        $str = implode(',', $uPI_ID);
                        $pls = BWSSC_pls($arr["newpldata"]);
                        foreach ($data as $k1 => $v1) {
                            if (strpos($str, $v1["uPI_ID"] . "") !== false) {
                                $peilv1 = $pls["p" . $v1["uPI_ID"]];
                                $peilv2 = 0;
                                $sql = "insert into `{$tb_libu}` set gid='{$gid}',bid='{$v1['bid']}',sid='{$v1['sid']}',cid='{$v1['cid']}',pid='{$v1['pid']}',userid='{$userid}',je='{$v1['je']}',xtype='2',points='{$v1['points']}',peilv1='{$peilv1}',peilv2='{$peilv2}',tid='{$tid}',ip=INET_ATON('127.0.0.1'),code='',flytype=2,z='9',bs=1,qishu='{$qishu}',content='{$v1['content']}',dates='{$dates}',time=NOW(),abcd='A',ab='A',sv='1',bz='{$fly['id']}'";
                                $msql->query($sql);
                                $tid++;
                            }
                        }
                    }
                }
                $msql->query("insert into `{$tb_lib}` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb_libu}` where userid='{$userid}' order by id");
                $msql->query("delete from x_libu where userid='{$userid}'");
                foreach ($data as $k1 => $v1) {
                    if ($v["type"] == 2) {
                        $send["gt"] = $gt;
                        $send["qs"] = BWSSC_qs($gid, $qishu);
                        $send["r"] = random();
                        $send["uid"] = $fly["uid"];
                        $send["itype"] = $v1["itype"];
                        $send["gip"] = $v1["gip"];
                        $send["gim"] = $v1["gim"];
                        $send["idlist"] = $v1["idlist"];
                        $send["tzcount"] = $v1["tzcount"];
                        $send["sname"] = $v1["sname"];
                        $send["ggameid"] = $v1["ggameid"];
                        //print_r($send);
                        $url = trim($fly['url1']) . "/User/Bet/Betsave2?" . http_build_query($send);
                        $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $send, "head" => false, "location" => false, "json" => true];
                        $res = CURL($send);
                        $list["userid"] = $userid;
                        $list["sendtext"] = json_encode($send);
                        $list["retext"] = $res["res"];
                        $list["gid"] = $gid;
                        $list["qishu"] = $qishu;
                        $list["url"] = $url;
                        $list["webtype"] = $fly["webtype"].'_'.$fly['id'];
                        $list["time"] = date("Y-m-d H:i:s");
                        flylog($list);
                        //$res = curlsjson($cookie_jar,$url,true,$send,true,false,false);
                        //echo $res["res"];
                        $arr = json_decode($res["res"], true);
                        if (strpos($res["res"], "投注成功") !== false) {
                            $peilv2 = 0;
                            $peilv1 = $arr["newpldata"][0]["g_odds"];
                            $sql = "insert into `{$tb_libu}` set gid='{$gid}',bid='{$v1['bid']}',sid='{$v1['sid']}',cid='{$v1['cid']}',pid='{$v1['pid']}',userid='{$userid}',je='{$v1['je']}',xtype='2',points='{$v1['points']}',peilv1='{$peilv1}',peilv2='{$peilv2}',tid='{$tid}',ip=INET_ATON('127.0.0.1'),code='',flytype=2,z='9',bs=1,qishu='{$qishu}',content='{$v1['content']}',dates='{$dates}',time=NOW(),abcd='A',ab='A',sv='1',bz='{$fly['id']}'";
                            $msql->query($sql);
                            $tid++;
                        }
                    }
                }
                $msql->query("insert into `{$tb_lib}` select NULL,tid,userid,dates,qishu,gid,bid,sid,cid,pid,abcd,ab,peilv1,peilv2,points,content,je,time,xtype,z,prize,znum,zc0,zc1,zc2,zc3,zc4,zc5,zc6,zc7,zc8,points1,points2,points3,points4,points5,points6,points7,points8,peilv11,peilv12,peilv13,peilv14,peilv15,peilv16,peilv17,peilv18,peilv21,peilv22,peilv23,peilv24,peilv25,peilv26,peilv27,peilv28,uid1,uid2,uid3,uid4,uid5,uid6,uid7,uid8,flytype,sv,bz,bs,ip,code,kk from `{$tb_libu}` where userid='{$userid}' order by id");
                $msql->query("delete from x_libu where userid='{$userid}'");
                break;
        }
    }
}
echo date("H:i:s");
function CURL($arr = ["cookietype" => false, "cookie_jar" => "", "url" => "", "posttype" => false, "postdata" => [], "head" => true, "location" => true, "refer" => "", "headip" => "127.0.0.1", "sslhostflag" => true, "json" => false])
{
    $url = str_replace('://', '---', $arr["url"]);
    $url = str_replace('//', '/', $url);
    $url = str_replace('---', '://', $url);
    //echo $url;
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($SSL) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }
    if ($arr["head"]) {
        curl_setopt($ch, CURLOPT_HEADER, true);
    }
    if ($arr["refer"] != "") {
        curl_setopt($ch, CURLOPT_REFERER, $arr["refer"]);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $arr["location"]);
    if ($arr["cookietype"]) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $arr["cookie_jar"]);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $arr["cookie_jar"]);
    }
    //$headers = ['CLIENT-IP:' . $arr["headip"], 'X-FORWARDED-FOR:' . $arr["headip"]];
    if ($arr["posttype"]) {
        if ($arr["json"]) {
            $postdata = "";
            is_array($arr["postdata"]) && $postdata = json_encode($arr["postdata"]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            $headers[] = 'Content-Type: application/json; charset=utf-8';
            $headers[] = 'Content-Length: ' . strlen($postdata);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arr["postdata"]));
        }
    }
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    //curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $info = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    if (curl_error($ch)) {
        echo curl_error($ch);
    }
    curl_close($ch);
    return ['res' => $result, 'location' => $info];
}
function random($min = 0, $max = 1)
{
    $v = $min + mt_rand() / mt_getrandmax() * ($max - $min);
    return $v;
}
function Rand_IP()
{
    $ip2id = round(rand(600000, 2550000) / 10000);
    $ip3id = round(rand(600000, 2550000) / 10000);
    $ip4id = round(rand(600000, 2550000) / 10000);
    $arr_1 = array("218", "218", "66", "66", "218", "218", "60", "60", "202", "204", "66", "66", "66", "59", "61", "60", "222", "221", "66", "59", "60", "60", "66", "218", "218", "62", "63", "64", "66", "66", "122", "211");
    $randarr = mt_rand(0, count($arr_1) - 1);
    $ip1id = $arr_1[$randarr];
    return $ip1id . "." . $ip2id . "." . $ip3id . "." . $ip4id;
}
function flylog($arr)
{
    global $psql, $tb_flylist;
    $sql = "";
    foreach ($arr as $k => $v) {
        $sql .= $k . "='" . str_replace("'", "", $v) . "',";
    }
    $sql = "insert into `{$tb_flylist}` set " . substr($sql, 0, strlen($sql) - 1);
    $psql->query($sql);
}
echo '
<script language="JavaScript"> 
function myrefresh() 
{ 
window.location.reload(); 
} 
setTimeout(\'myrefresh()\',10000); //指定1秒刷新一次 
</script>';