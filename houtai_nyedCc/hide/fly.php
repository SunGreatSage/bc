<?php
include '../data/comm.inc.php';
include '../data/myadminvar.php';
include '../func/func.php';
include "../func/csfunc.php";
include '../func/adminfunc.php';
include '../include.php';
include './checklogin.php';
switch ($_REQUEST['xtype']) {
    case "shui":
        $agent = strtoupper($_REQUEST['agent']);
        $whi = "";
        if(preg_match("/^[a-zA-Z0-9]{1}([a-zA-Z0-9]|[._]){1,10}$/", $agent)){
            $msql->query("select layer,userid,username from `$tb_user` where username='$agent'");
            $msql->next_record();
            if($msql->f("username")==$agent){
                $whi = " and fid".$msql->f("layer")."='".$msql->f("userid")."'";
            }
        }
        $us = $msql->arr("select userid,username,name,fid1,fid2,fid3,fid4,fid5,fid6,fid7,fid8,layer from `$tb_user` where ifagent=0 $whi ",1);
        foreach($us as $k => $v){
            $us[$k]['fids'] = "";
            for($i=1;$i<$v["layer"];$i++){
                $msql->query("select username,name from `$tb_user` where userid='".$v["fid".$i]."'");
                $msql->next_record();
                $us[$k]['fids'] .= $msql->f("username").'&nbsp;('.$msql->f("name").')'."<Br>";
            }
            $msql->query("select shui,isok,userid,qishu,stype,zuix,zuid from `$tb_shui` where userid='{$v['userid']}'");
            $msql->next_record();
            $us[$k]["shui"] = (float)$msql->f("shui");
            $us[$k]["zuix"] = $msql->f("zuix");
            $us[$k]["zuid"] = $msql->f("zuid");
            $us[$k]["qishu"] = $msql->f("qishu");
            $us[$k]["stype"] = $msql->f("stype");
            $us[$k]["isok"] = pr0($msql->f("isok"));
        }
        $tpl->assign("us",$us);
        $tpl->display("shui.html");                                                                                                                                                                             
    break;
    case "setshui":
        $data= json_decode(str_replace('\\', '', $_POST["data"]),true);
        $sql = "";
        foreach($data as $k => $v){
            $msql->query("delete from `$tb_shui` where userid='{$v['userid']}'");
            $k>0 && $sql .= ",";
            $sql .= "('{$v['userid']}','{$v['username']}','{$v['shui']}','{$v['isok']}','{$v['stype']}','{$v['qishu']}','{$v['zuix']}','{$v['zuid']}')";
        }
        
        $msql->query("insert into `$tb_shui`(userid,username,shui,isok,stype,qishu,zuix,zuid) values $sql");
        echo 1;

    break;
    case "flylist":
        include('../global/page.class.php');
        $psize = 200;
        $msql->query("select count(id) from `{$tb_flylist}`");
        $msql->next_record();
        $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        $l = $msql->arr("select * from `{$tb_flylist}` order by id desc limit " . ($thispage - 1) * $psize . ",{$psize}",1);
        $tmp=[];
        foreach($l as $k => $v){
            if($tmp["g".$v["gid"]]==""){
                $tmp["g".$v["gid"]] = transgame($v["gid"],"gname");
            }
            $l[$k]["gname"] = $tmp["g".$v["gid"]];
        }

        $tpl->assign('l', $l);
        $tpl->assign('deldate', date('Y-m-d', time() - 86400 * 14));
        $tpl->assign('page', $page->show());
        $tpl->display("flylist.html");
    break;
    case 'dflylist':
        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'date') {
            $time = $id . ' ' . $config['editstart'];
            $msql->query("delete from `{$tb_flylist}` where time<='$time'");
            echo 1;
        } else {
            $id = str_replace('\\', '', $id);
            $id = json_decode($id, true);
            $id = implode(',', $id);
            $msql->query("delete from `{$tb_flylist}` where id in ({$id})");
            echo 1;
        }
        break;
    case "show":
        $game = getgamecs($userid);
        $game = getgamename($game);
        $fly = $msql->arr("select * from `{$tb_flyinfo}` where userid='{$userid}'", 1);
        foreach ($fly as $k => $v) {
            $fly[$k]["game"] = json_decode($v["game"], true);
            //print_r($fly[$k]["game"]);
            $tmp = $game;
            foreach ($tmp as $k1 => $v1) {
                $tmp[$k1]['ifoks'] = 0;
                foreach ($fly[$k]["game"] as $k2 => $v2) {
                    //echo $v1["gid"],'aa',$v2["gid"],"b",$v2['ifok'],"<BR>";
                    if ($v1['gid'] == $v2['gid'] && $v2['ifok'] == 1) {
                        $tmp[$k1]['ifoks'] = 1;
                    }
                }
            }
            //print_r($tmp);
            $fly[$k]["game"] = $tmp;
        }
        //print_r($fly);
        $tpl->assign("fly", $fly);
        $tpl->assign('game', $game);
        $tpl->assign('config', $config);
        $tpl->display("fly.html");
        break;
    case "add":
        $msql->query("insert into `{$tb_flyinfo}` set userid='{$userid}'");
        echo 1;
        break;
    case "sc":
        $id = $_POST['id'];
        if (!is_numeric($id)) {
            exit;
        }
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
        $msql->query("delete from `{$tb_flyinfo}` where userid='{$userid}' and id='{$id}'");
        echo 1;
        break;
    case "edit":
        $data = str_replace('\\', '', $_POST['data']);
        $data = json_decode($data, true);
        $sql = "update `{$tb_flyinfo}` set ";
        foreach ($data as $k => $v) {
            if ($k == 'game') {
                $sql .= $k . "='" . json_encode($v) . "',";
            } else {
                $sql .= $k . "='" . $v . "',";
            }
        }
        $sql = substr($sql, 0, strlen($sql) - 1);
        $msql->query($sql . " where id='" . $data['id'] . "'");
        echo $sql . " where id='" . $data['id'] . "'";
        echo 1;
        break;
    case "getstatus":
        $fid = $_REQUEST['fid'];
        $fly = $msql->arr("select * from `{$tb_flyinfo}` where userid='{$userid}' and id='{$fid}' and isable=1 limit 1", 1);
        if (!$fly) {
            exit;
        }
        $fly = $fly[0];
        //print_r($fly);
        $cookie_jar = '../upload/cookie/' . $fly['userid'] . $fid . ".txt";
        $arr["webtype"] = $fly["webtype"];
        switch ($fly['webtype']) {
            case 'WS':
                $url = trim($fly['url1']) . '/' . $fly['cookie'] . '/Member/GetMemberInfo?lotteryId=4&_=' . time();
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => [], "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, true, [], true, false);
                //echo $res["res"];
                $res = json_decode($res['res'], true);
                $data = $res['Data'];
                $arr['balance'] = $data['Balance'];
                $arr['loginuser'] = $data['Account'] . "(" . $data['HandicapPackageName'] . "盘)";
                $arr['name'] = $fly['name'];
                $arr['sy'] = $data['Balance'] + $data['BetMoney'] - $data['Credit'];
                $arr['wjs'] = $data['BetMoney'];
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

                $arr['balance'] = $data["allowcreditquota"];
                $arr['loginuser'] = $data['memberno'];
                $arr['name'] = $fly['name'];
                $arr['wjs'] = $data["usecreditquota"];
                $arr["sy"] = $arr['balance'] - $data["creditquota"];
                if($data["opena"]=='true'){
                    $abcd='A';
                }else if($data["openb"]=='true'){
                    $abcd='B';
                }else if($data["openc"]=='true'){
                    $abcd='C';
                }else{
                    $abcd='D';
                }
                $arr["abcd"] = $abcd;
                $msql->query("update `$tb_flyinfo` set abcd='$abcd' where id='{$fly['id']}'");
                
            break;    
            case 'SGWIN':
                $url = trim($fly['url1']) . '/member/accounts';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, "", true, false);
                $res = json_decode($res["res"], true);
                $res = $res[0];
                $arr['balance'] = $res['balance'];
                $arr['wjs'] = $res['betting'];
                $arr['sy'] = $res['result'];
                $arr['loginuser'] = $fly['username'];
                $arr['name'] = $fly['name'];
                break;
            case "DL":
                $url = trim($fly['url1']) . '/user/getUserInfo';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, "", true, false);
                $res = json_decode($res["res"], true);
                $arr['balance'] = $res['currentPoint'];
                $arr['wjs'] = $res['creditPoint'];
                $arr['sy'] = $res['todayResult'];
                $arr['loginuser'] = $res['account'];
                $arr['name'] = $res['name'];
                break;
            case "BW":
                $url = trim($fly['url1']) . '/Home/cust_info?UID=' . $fly["uid"];
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, "", true, false);
                preg_match_all('/<td width="50%" align="center" bgcolor="#FFFFFF">(.*)<\\/td>/isU', $res["res"], $output);
                $arr["money"] = str_replace("&nbsp;", "", $output[1][1]);
                $arr["balance"] = str_replace("&nbsp;", "", $output[1][3]);
                $arr["wjs"] = str_replace("&nbsp;", "", $output[1][2]);
                $arr['loginuser'] = $output[1][0];
                $arr['name'] = $fly['name'];
                break;
            case "ZYSIX":
                http:
                //dh1.a558k.com/vip/cust_info.php
                $url = trim($fly['url1']) . '/vip/cust_info.php';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                preg_match_all('/<span id="userNo">(.*)<\\/span>/isU', $res["res"], $output);
                $arr["loginuser"] = $output[1][0];
                preg_match_all('/<span id="creditOver">(.*)<\\/span>/isU', $res["res"], $output);
                $arr["balance"] = $output[1][0];
                preg_match_all('/<span id="bettingAmt">(.*)<\\/span>/isU', $res["res"], $output);
                $arr["wjs"] = $output[1][0];
                $arr['name'] = $fly['name'];
                //print_r($arr);
                break;
            case "BWSSC":
                $r = $_REQUEST['r'];
                $refer = trim($fly['url1']) . 'User/Home/leftinfo?UID=' . $fly["uid"];
                $url = trim($fly['url1']) . '/User/Home/getuserinfo';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => ["UID" => $fly["uid"]], "head" => false];
                $res = CURL($send);
                //echo $res["res"];
                //$res = curls($cookie_jar, $url, true, ["UID"=>$fly["uid"]], true, false);
                $a= $res["res"];
                $res = json_decode($res["res"], true);
                $arr['balance'] = str_replace(',','',$res['info']['money_ky']);
                $arr['wjs'] = $res['info']['totalmoney'];
                $arr['sy'] = $res['info']['money_win'];
                $arr['loginuser'] = $fly['username'];
                $arr['name'] = $fly['name'];
                $arr['aa'] = $a;
                break;
        }
        echo json_encode($arr);
        break;
    case "cxfly":
        $fly = $msql->arr("select * from `{$tb_flyinfo}` where userid='{$userid}' and isable=1 limit 1", 1);
        if (count($fly) == 0) {
            exit;
        }
        foreach ($fly as $k => $v) {
            $cookie_jar = '../upload/cookie/' . $v['userid'] . $v["id"] . ".txt";
            switch ($fly['webtype']) {
                case 'WS':
                    $url = $v['url1'] . '/' . $v['cookie'] . '/Member/GetMemberInfo?lotteryId=4&_=' . time();
                    $send = ["headip" => $v["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => [], "head" => false];
                    $res = CURL($send);
                    //$res = curls($cookie_jar, $url, true, [], true, false);
                    $res = json_decode($res['res'], true);
                    $data = $res['Data'];
                    if ($data == '') {
                        echo 1;
                        exit;
                    }
                    if ($data['Balance'] < $v['txje']) {
                        echo 2;
                        exit;
                    }
                    break;
                case "IDC":
                    $refer = trim($fly['url3'])."ch/main.aspx";
                    $url = trim($fly['url3']) . '/app/ws_member.asmx/MembersInfo_Data';
                    $data="";
                    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $data, "head" => false,"refer"=>$refer,"json"=>true];
                    $res = CURL($send);
                    $res = str_replace('\\', '', $res["res"]);
                    $res = explode('{"Rows":[', $res);
                    $res = explode(']}]', $res[1]);
                    $data = json_decode($res[0],true);
                    if ($data['allowcreditquota'] == "") {
                        echo 1;
                        exit;
                    }
                    if ($data['allowcreditquota']  < $v['txje']) {
                        echo 2;
                        exit;
                    }
                break; 
                case 'SGWIN':
                    $url = $v['url1'] . '/member/accounts';
                    $send = ["headip" => $v["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                    $res = CURL($send);
                    //$res = curls($cookie_jar, $url, false, "", true, false);
                    $res = json_decode($res["res"], true);
                    $res = $res[0];
                    if ($res['balance'] == "") {
                        echo 1;
                        exit;
                    }
                    if ($res['balance'] < $v['txje']) {
                        echo 2;
                        exit;
                    }
                    break;
                case "DL":
                    $url = $v['url1'] . '/user/getUserInfo';
                    $send = ["headip" => $v["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                    $res = CURL($send);
                    //$res = curls($cookie_jar, $url, false, "", true, false);
                    $res = json_decode($res["res"], true);
                    if ($res['currentPoint'] == "") {
                        echo 1;
                        exit;
                    }
                    if ($res['currentPoint'] < $v['txje']) {
                        echo 2;
                        exit;
                    }
                    break;
                case "ZYSIX":
                    $url = $v['url1'] . '/vip/cust_info.php';
                    $send = ["headip" => $v["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                    $res = CURL($send);
                    preg_match_all('/<span id="userNo">(.*)<\\/span>/isU', $res["res"], $output);
                    $arr["loginuser"] = $output[1][0];
                    preg_match_all('/<span id="creditOver">(.*)<\\/span>/isU', $res["res"], $output);
                    $arr["balance"] = $output[1][0];
                    preg_match_all('/<span id="bettingAmt">(.*)<\\/span>/isU', $res["res"], $output);
                    $arr["wjs"] = $output[1][0];
                    if ($arr["balance"] == "") {
                        echo 1;
                        exit;
                    }
                    if ($arr["balance"] < $v['txje']) {
                        echo 2;
                        exit;
                    }
                    break;
                case "BW":
                    $url = $v['url1'] . '/Home/cust_info?UID=' . $v["uid"];
                    $send = ["headip" => $v["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                    $res = CURL($send);
                    //$res = curls($cookie_jar, $url, false, "", true, false);
                    preg_match_all('/<td width="50%" align="center" bgcolor="#FFFFFF">(.*)<\\/td>/isU', $res["res"], $output);
                    $arr["money"] = str_replace("&nbsp;", "", $output[1][1]);
                    $arr["balance"] = str_replace("&nbsp;", "", $output[1][3]);
                    $arr["wjs"] = str_replace("&nbsp;", "", $output[1][2]);
                    $arr['loginuser'] = $output[1][0];
                    $arr['name'] = $v['name'];
                    if ($arr["balance"] == "") {
                        echo 1;
                        exit;
                    }
                    if ($arr["balance"] < $v['txje']) {
                        echo 2;
                        exit;
                    }
                    break;
                case "BWSSC":
                    $url = $v['url1'] . '/User/Home/getuserinfo';
                    $send = ["headip" => $v["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => ["UID" => $v["uid"]], "head" => false];
                    $res = CURL($send);
                    //$res = curls($cookie_jar, $url, true, ["UID"=>$v["uid"]], true, false);
                    $res = json_decode($res["res"], true);
                    $arr['balance'] = str_replace(',','',$res['info']['money_ky']);
                    $arr['wjs'] = $res['info']['totalmoney'];
                    $arr['sy'] = $res['info']['money_win'];
                    $arr['loginuser'] = $v['username'];
                    $arr['name'] = $v['uname'];
                    if ($arr["balance"] == "") {
                        echo 1;
                        exit;
                    }
                    if ($arr["balance"] < $v['txje']) {
                        echo 2;
                        exit;
                    }
                    break;
            }
        }
        break;
    case "logout":
        $fid = $_REQUEST['fid'];
        $fly = $msql->arr("select * from `{$tb_flyinfo}` where userid='{$userid}' and id='{$fid}' and isable=1 limit 1", 1);
        if (!$fly) {
            exit;
        }
        $fly = $fly[0];
        $cookie_jar = '../upload/cookie/' . $fly['userid'] . $fid . ".txt";
        switch ($fly['webtype']) {
            case 'WS':
                $url = trim($fly['url1']) . '/' . $fly['cookie'] . '/Member/Logout?status=1';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, [], true, false);
                unlink($cookie_jar);
                echo 1;
                break;
            case "IDC":                
                $refer = trim($fly['url3'])."ch/main.aspx";
                $url = trim($fly['url3'])."logout.aspx?mno=".$fly["username"]."&submno=&sid=xpwtciynjq1ittfuvyglgt4g_53";
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                unlink($cookie_jar);
                $res = CURL($send);
                echo 1;
            break;    
            case 'SGWIN':
                unlink($cookie_jar);
                $url = trim($fly['url1']) . '/member/logout';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, "", true);
                echo 1;
                break;
            case 'DL':
                $url = trim($fly['url1']) . '/user/logout';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                $res = CURL($send);
                unlink($cookie_jar);
                //$res = curls($cookie_jar, $url, false, "", true);
                echo 1;
                break;
            case "ZYSIX":
                $url = trim($fly['url1']) . '/vip/logout.php';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                $res = CURL($send);
                unlink($cookie_jar);
                echo 1;
                break;
            case "BW":
                $url = trim($fly['url1']) . '/Home/logout?uid=' . $fly["uid"];
                $msql->query("update `{$tb_flyinfo}` set uid='' where id='" . $fly["id"] . "'");
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                $res = CURL($send);
                unlink($cookie_jar);
                //$res = curls($cookie_jar, $url, false, "", true);
                echo 1;
                break;
            case "BWSSC":
                $refer = trim($fly['url1']) . "/User/Home/header?UID=" . $fly["uid"];
                $url = trim($fly['url1']) . "/User/Login/Logout?UID=" . $fly["uid"];
                $msql->query("update `{$tb_flyinfo}` set uid='' where id='" . $fly["id"] . "'");
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "refer" => $refer];
                $res = CURL($send);
                unlink($cookie_jar);
                //$res = curls($cookie_jar, $url, false, "", true);
                echo 1;
                break;
        }
        break;
    case "getcode":
        $fid = $_REQUEST['fid'];
        //echo "select * from `{$tb_flyinfo}` where userid='{$userid}' and id='{$fid}' and isable=1 limit 1";
        $fly = $msql->arr("select * from `{$tb_flyinfo}` where userid='{$userid}' and id='{$fid}' and isable=1 limit 1", 1);
        if (!$fly) {
            exit;
        }
        $fly = $fly[0];
        //print_r($fly);
        $cookie_jar = '../upload/cookie/' . $fly['userid'] . $fid . ".txt";
        $code = '../upload/' . $fly['userid'] . $fid . rand(111, 999) . ".png";
        unlink($cookie_jar);
        //error_reporting(E_ALL);
        delcode('../upload/');
        //echo $fly['webtype'];
        $ip = Rand_IP();
        $msql->query("update `{$tb_flyinfo}` set ip='{$ip}' where id='{$fly["id"]}'");
        switch ($fly['webtype']) {
            case 'WS':
                $url = trim($fly['url1']) . '/Member/Login?_=' . time() . '&token=' . $fly['searchcode'];
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, [], true, true);
                $location = explode(trim($fly['url1']), $res['location']);
                $location = explode('/Member/Login', $location[1]);
                $msql->query("update `{$tb_flyinfo}` set cookie='" . $location[0] . "' where id=" . $fly['id']);
                $url = trim($fly['url1']) . '/' . $location[0] . '/Member/GK';
                $send = ["headip" => $ip, "cookietype" => false, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, [], false, false);
                $data['user'] = $fly['username'];
                $data['passwd'] = $fly['passwd'];
                $data['gk'] = json_decode($res['res'], true);
                //$data['gks'] = $res['res'];
                $arr['status'] = 1;
                $arr['webtype'] = $fly['webtype'];
                $arr['data'] = $data;
                break;
            case 'IDC':
                $url = trim($fly['url1']) . '/search.aspx';
               
                $data=["wd"=>$fly['searchcode']];
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $data];
                $res = CURL($send);
                //echo $res["res"];
                preg_match_all('/src="(.*)"/isU', $res["res"], $output);
                $url = $output[1][0];
                $url = explode('?', $url);
                
                $urls = $url[0] . '/indexmb.aspx?'.$url[1];
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $urls, "posttype" => false, "postdata" => [] ];
                $res = CURL($send);
                preg_match_all('/value="(.*)"/isU', $res["res"], $output);
              
                $data=[];
                $data["__VIEWSTATE"] = $output[1][0];
                $data["__VIEWSTATEGENERATOR"] = $output[1][1];
                $data["__RequestVerificationToken"] = $output[1][2];
                
                preg_match_all('/action="(.*)"/isU', $res["res"], $output);
                $data["action"] = substr($output[1][0],2);
                $data["action"] = str_replace('&amp;', '&', $data['action']);

                $cookie = json_encode($data);

                $msql->query("update `$tb_flyinfo` set url3='{$url[0]}',url4='{$url[1]}',cookie='$cookie' where id='{$fly[id]}'");


                $url = $url[0]."/checknum.aspx?ts=".time();
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                //list($header, $body) = explode("\r\n\r\n", $res["res"]);
                $fp = fopen($code, "w");
                fwrite($fp, $res["res"]);
                fclose($fp);
                $arr = ["status" => 1, "data" => substr($code, 2), 'webtype' => $fly['webtype']];
                //https://08x1dgw.yn523.com/cp11_07_mb/checknum.aspx?ts=1599228172341
            break;    
            case 'SGWIN':
                $url = trim($fly['url1']) . '/login';
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, "", true);
                $url = trim($fly['url1']) . '/code?_=' . time();
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, "", true,false);
                //list($header, $body) = explode("\r\n\r\n", $res["res"]);
                $fp = fopen($code, "w");
                fwrite($fp, $res["res"]);
                fclose($fp);
                $arr = ["status" => 1, "data" => substr($code, 2), 'webtype' => $fly['webtype']];
                break;
            case "BW":
                $url = trim($fly['url1']) . '/Login/Index?t=' . time();
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, "", true);
                $url = trim($fly['url1']) . '/Login/VerifyCode?t=' . time();
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, "", true,false);
                $fp = fopen($code, "w");
                fwrite($fp, $res["res"]);
                fclose($fp);
                $loginhtml = file_get_contents("./js/BW_login.html");
                $loginjs = "./js/BW_login.min.js";
                $arr = ["status" => 1, "data" => substr($code, 2), 'webtype' => $fly['webtype'], "uid" => $uid, "loginjs" => $loginjs, "loginhtml" => $loginhtml, "user" => $fly["username"], "pass" => $fly["passwd"]];
                break;
            case "BWSSC":
                $url = trim($fly['url1']) . '/User/login/index';
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false, "sslhostflag" => 2];
                $res = CURL($send);
                //$res = curls_bwssc($cookie_jar, $url, false, "", 1,0,1);
                preg_match_all('/Image\\/VerifyCodeN\\?r=(.*)" alt=/isU', $res["res"], $output);
                $url = trim($fly['url1']) . '/Image/VerifyCodeN?r=' . $output[1][1];
                $refer = trim($fly['url1']) . '/User/login/index';
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [], "head" => false, "refer" => $refer, "sslhostflag" => 2];
                $res = CURL($send);
                //$res = curls_bwsscimg($cookie_jar, $url, false, "", 1,0,1,$refer);
                $fp = fopen($code, "w");
                fwrite($fp, $res["res"]);
                fclose($fp);
                $loginhtml = file_get_contents("./js/BW_login.html");
                $loginjs = "./js/BW_login.min.js";
                $arr = ["status" => 1, "data" => substr($code, 2), 'webtype' => $fly['webtype'], "uid" => $uid, "loginjs" => $loginjs, "loginhtml" => $loginhtml, "user" => $fly["username"], "pass" => $fly["passwd"]];
                break;
            case "DL":
                $url = trim($fly['url1']) . '/?cID=' . $fly["searchcode"];
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, false, "", true);
                $arr = ["status" => 1, 'webtype' => $fly['webtype']];
                break;
            case "ZYSIX":
                $url = trim($fly['url1']);
                $send = ["headip" => $ip, "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                $res = CURL($send);
                $arr = ["status" => 1, 'webtype' => $fly['webtype']];
                break;
        }
        echo json_encode($arr);
        break;
    case "login":
        $fid = $_REQUEST['fid'];
        $fly = $msql->arr("select * from `{$tb_flyinfo}` where userid='{$userid}' and id='{$fid}' and isable=1 limit 1", 1);
        if (!$fly) {
            exit;
        }
        $fly = $fly[0];
        $cookie_jar = '../upload/cookie/' . $fly['userid'] . $fid . ".txt";
        switch ($fly['webtype']) {
            case 'WS':
                $data['pk'] = $_POST['pk'];
                $data['info'] = $_POST['info'];
                $data['Token'] = $fly['searchcode'];
                $data['Captcha'] = 1111;
                $url = trim($fly['url1']) . '/' . $fly['cookie'] . '/Member/DoLogin';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $data, "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, true, $data, true, false);
                $res = json_decode($res['res'], true);
                if ($res['Status'] == 1) {
                    $url = trim($fly['url1']) . '/' . $fly['cookie'] . '/Member/AcceptAgreement';
                    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => [], "head" => false];
                    $res = CURL($send);
                    //$res = curls($cookie_jar, $url, true, [], true, false);
                    $arr['status'] = 1;
                    $arr['webtype'] = $fly['webtype'];
                    $arr['data'] = $res['res'];
                } else {
                    $arr['status'] = 0;
                    $arr['webtype'] = $fly['webtype'];
                    $arr['data'] = $res['res'];
                }
                break;
            case "IDC":
                $cookie = json_decode($fly["cookie"],true);
                $url = trim($fly['url3']) . '/'.$cookie["action"];
                unset($cookie["action"]);
                $code = $_REQUEST['imgcode'];
                $post = ['txt_U_name' => $fly['username'], 'txt_U_Password' => $fly['passwd'], 'txt_validate' => $code];
                $post = array_merge($post,$cookie);
                
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $post, "location" => true];
                $res = CURL($send);
                $result = $res["res"];
               
                /*
                $url = trim($fly['url3']) . 'ch/agreement.aspx/LocationUrl';
                $data = ["stype"=>1];
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $data,"json"=>true];
                $res = CURL($send);
 
                $url = trim($fly['url3']) . 'ch/main.aspx';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => [],"location"=>true];
                $res = CURL($send);

                $refer = trim($fly['url3'])."ch/main.aspx";
                $url = trim($fly['url3']) . 'app/ws_member.asmx/MembersInfo_Data';
 
                $data="";
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => "","refer"=>$refer,"json"=>true];
                $res = CURL($send);
                 */
                if(strpos($res["res"],'ch/agreement.aspx')!==false){
                    $arr = ["status" => 200, "data" => "登陆成功"];
                    echo json_encode($arr);
                    exit;
                }else{
                    $arr = sgwingetcode($fly, $cookie_jar);
                    $arr['err'] = '账号或密码错误';
                    $arr["message"] = $result;
                    echo json_encode($arr);
                    exit;
                }
                
                //$url = trim($fly['url3']) . '/ch/agreement.aspx';
                //$send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "postdata" => []];
                //$res = CURL($send);

                //echo $res["res"];
            break;    
            case 'SGWIN':
                $url = trim($fly['url1']) . '/login';
                $code = $_REQUEST['imgcode'];
                $post = ['type' => '1', 'account' => $fly['username'], 'password' => $fly['passwd'], 'code' => $code];
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $post, "location" => true];
                $res = CURL($send);
                $result = $res["res"];
                if (strpos($result, '验证码错误') !== false) {
                    $arr = sgwingetcode($fly, $cookie_jar);
                    //print_r($arr);
                    $arr['err'] = '验证码错误';
                    $arr["message"] = $result;
                    echo json_encode($arr);
                    exit;
                } else {
                    if (strpos($result, '账号或密码错误') !== false) {
                        $arr = sgwingetcode($fly, $cookie_jar);
                        $arr['err'] = '账号或密码错误';
                        $arr["message"] = $result;
                        echo json_encode($arr);
                        exit;
                    } else {
                        if (strpos($result, '用户协议') !== false) {
                            //$OLID = getSubstr($result, "_OLID_=", "\r");
                            //$msql->query("update `$tb_flyinfo` set cookie='$OLID' where userid='$userid' and isable=1");
                            $arr = ["status" => 200, "data" => "登陆成功"];
                            echo json_encode($arr);
                            exit;
                        } else {
                            $arr = sgwingetcode($fly, $cookie_jar);
                            $arr['err'] = '账号或密码错误';
                            $arr["message"] = $result;
                            $arr["url"] = $url;
                            //echo 768;
                            echo json_encode($arr);
                            exit;
                        }
                    }
                }
                break;
            case 'BW':
                $url = trim($fly['url1']) . '/Login/CheckLogin';
                $code = $_POST['imgcode'];
                $post = ['code' => $code];
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $post, "head" => false];
                $res = CURL($send);
                //$res = curls($cookie_jar, $url, true, $post, true,false);
                //echo $res["res"];
                $res = json_decode($res["res"], true);
                if ($res["message"] == "验证码错误" || $res["message"] == "驗證碼錯誤") {
                    $arr = bwgetcode($fly, $cookie_jar);
                    $arr['err'] = '验证码错误';
                    $arr["message"] = $result;
                    echo json_encode($arr);
                    exit;
                } else {
                    if ($res["message"] == "密碼錯誤") {
                        $arr = bwgetcode($fly, $cookie_jar);
                        $arr['err'] = '账号或密码错误';
                        $arr["message"] = $result;
                        echo json_encode($arr);
                        exit;
                    } else {
                        if ($res["success"] == true || $res["success"] == "True") {
                            $uid = explode('=', $res["url"]);
                            $msql->query("update `{$tb_flyinfo}` set uid='" . $uid[1] . "' where id='" . $fly["id"] . "'");
                            $arr = ["status" => 200, "data" => "登陆成功"];
                            echo json_encode($arr);
                            exit;
                        } else {
                            $arr = bwgetcode($fly, $cookie_jar);
                            $arr['err'] = '账号或密码错误';
                            $arr["message"] = $result;
                            echo json_encode($arr);
                            exit;
                        }
                    }
                }
                break;
            case 'BWSSC':
                $url = trim($fly['url1']) . '/User/login/CheckLogin';
                $code = $_POST['imgcode'];
                $post = ['code' => $code];
                $refer = trim($fly['url1']) . '/User/login/index';
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $post, "head" => false, "refer" => $refer, "sslhostflag" => 2];
                $result = CURL($send);
                //$result = curls_bwsscimg($cookie_jar, $url, true, $post, true,false,1,$refer);
                $res = json_decode($result["res"], true);
                //print_r($res);
                if ($res["message"] == "验证码错误" || $res["message"] == "驗證碼錯誤") {
                    $arr = bwsscgetcode($fly, $cookie_jar);
                    $arr['err'] = '验证码错误';
                    $arr["message"] = $result["res"];
                    echo json_encode($arr);
                    exit;
                } else {
                    if ($res["message"] == "密碼錯誤") {
                        $arr = bwsscgetcode($fly, $cookie_jar);
                        $arr['err'] = '账号或密码错误';
                        $arr["message"] = $result["res"];
                        echo json_encode($arr);
                        exit;
                    } else {
                        if ($res["success"] == true || $res["success"] == "True") {
                            $uid = explode('=', $res["url"]);
                            $msql->query("update `{$tb_flyinfo}` set uid='" . $uid[1] . "' where id='" . $fly["id"] . "'");
                            $arr = ["status" => 200, "data" => "登陆成功"];
                            echo json_encode($arr);
                            exit;
                        } else {
                            $arr = bwsscgetcode($fly, $cookie_jar);
                            $arr['err'] = '账号或密码错误';
                            $arr["error"] = $send;
                            $arr["message"] = $result["res"];
                            echo json_encode($arr);
                            exit;
                        }
                    }
                }
                break;
            case "DL":
                $url = trim($fly['url1']) . '/login/check';
                $post = ['account' => $fly['username'], 'password' => $fly['passwd'], 'cID' => $fly['searchcode'], "userLang" => "cn"];
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $post, "location" => true];
                $res = CURL($send);
                //echo json_encode($post);
                //$res = curls($cookie_jar, $url, true, $post, true);
                //echo $res["res"];
                $result = $res["res"];
                if (strpos($result, '帐号密码错误') !== false) {
                    $arr['err'] = "帐号密码错误";
                    $arr["message"] = $result;
                    $arr['status'] = 1;
                    $arr["data"] = "";
                    echo json_encode($arr);
                    exit;
                } else {
                    if (strpos($result, '信用额度') !== false || strpos($res["location"], 'index') !== false) {
                        $arr = ["status" => 200, "data" => "登陆成功"];
                        echo json_encode($arr);
                        exit;
                    } else {
                        $arr['err'] = '账号或密码错误';
                        $arr["message"] = $result;
                        $arr["url"] = $url;
                        $arr['status'] = 1;
                        $arr["data"] = "";
                        echo json_encode($arr);
                        exit;
                    }
                }
                break;
            case "ZYSIX":
                $url = trim($fly['url1']);
                $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "location" => true];
                $res = CURL($send);
                if (strpos($res["res"], 'action="vip/login.php"') !== false) {
                    $url = trim($fly['url1']) . '/vip/login.php';
                    $post = ['account' => $fly['username'], 'passwd' => $fly['passwd'], 'loginBtn' => ""];
                    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $post, "location" => true];
                    $res = CURL($send);
                } else {
                    $url = trim($fly['url1']);
                    $post = ['user' => $fly['username'], 'pass' => $fly['passwd'], 'x' => "49", "y" => "17"];
                    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => true, "postdata" => $post, "location" => true];
                    $res = CURL($send);
                }
                $result = $res["res"];
                if (strpos($result, "self.location='../index.php'") !== false) {
                    $arr['err'] = "帐号密码错误";
                    $arr["message"] = "";
                    $arr['status'] = 1;
                    $arr["data"] = "";
                    echo json_encode($arr);
                    exit;
                } else {
                    if (strpos($result, '<div id="rulesarea">') !== false) {
                        $arr = ["status" => 200, "data" => "登陆成功"];
                        echo json_encode($arr);
                        exit;
                    } else {
                        $arr['err'] = '账号或密码错误';
                        //$arr["message"] = $result;
                        $arr["url"] = $url;
                        $arr['status'] = 1;
                        $arr["data"] = "";
                        echo json_encode($arr);
                        exit;
                    }
                }
                break;
        }
        echo json_encode($arr);
        break;
}
function random($min = 0, $max = 1)
{
    $v = $min + mt_rand() / mt_getrandmax() * ($max - $min);
    return $v;
}
function delcode($path)
{
    $list = scandir($path);
    foreach ($list as $k => $v) {
        strpos($v, "png") !== false && @unlink($path . '/' . $v);
    }
}
function CURL($arr = ["cookietype" => false, "cookie_jar" => "", "url" => "", "posttype" => false, "postdata" => [], "head" => true, "location" => true, "refer" => "", "headip" => "127.0.0.1", "sslhostflag" => true, "json" => false])
{
    //print($arr);
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
        //echo $arr["refer"];
        curl_setopt($ch, CURLOPT_REFERER, $arr["refer"]);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36');

    //curl_setopt($ch, CURL_XML, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($arr["location"]) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $arr["location"]);
    }
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
    //echo json_encode($headers);
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    //curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $info = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    if (curl_error($ch)) {
        echo curl_error($ch);
    }
    curl_close($ch);
    return ['res' => $result, 'location' => $info, "arr" => $arr];
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
function bwsscgetcode($fly, $cookie_jar)
{
    $code = '../upload/' . $fly['userid'] . $fly["id"] . rand(111, 999) . ".png";
    unlink($code);
    $url = trim($fly['url1']) . '/User/login/index';
    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "head" => false, "sslhostflag" => 2];
    $res = CURL($send);
    //$res = curls_bwssc($cookie_jar, $url, false, "", 1,0,1);
    preg_match_all('/Image\\/VerifyCodeN\\?r=(.*)" alt=/isU', $res["res"], $output);
    $url = trim($fly['url1']) . '/Image/VerifyCodeN?r=' . $output[1][1];
    $refer = trim($fly['url1']) . '/User/login/index';
    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "head" => false, "sslhostflag" => 2, "refer" => $refer];
    $res = CURL($send);
    //$res = curls_bwsscimg($cookie_jar, $url, false, "", 1,0,1,$refer);
    $fp = fopen($code, "w");
    fwrite($fp, $res["res"]);
    fclose($fp);
    $loginhtml = file_get_contents("./js/BW_login.html");
    $loginjs = "./js/BW_login.min.js";
    $arr = ["status" => 1, "data" => substr($code, 2), 'webtype' => $fly['webtype'], "uid" => $uid, "loginjs" => $loginjs, "loginhtml" => $loginhtml, "user" => $fly["username"], "pass" => $fly["passwd"]];
    return $arr;
}
function bwgetcode($fly, $cookie_jar)
{
    $code = '../upload/' . $fly['userid'] . $fly["id"] . rand(111, 999) . ".png";
    unlink($code);
    $url = trim($fly['url1']) . '/Login/Index?t=' . time();
    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false];
    $res = CURL($send);
    //$res = curls($cookie_jar, $url, false, "", true);
    $url = trim($fly['url1']) . '/Login/VerifyCode?t=' . time();
    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "head" => false];
    $res = CURL($send);
    //$res = curls($cookie_jar, $url, false, "", true,false);
    $fp = fopen($code, "w");
    fwrite($fp, $res["res"]);
    fclose($fp);
    $loginhtml = file_get_contents("./js/BW_login.html");
    $loginjs = "./js/BW_login.min.js";
    $arr = ["status" => 1, "data" => substr($code, 2), 'webtype' => $fly['webtype'], "uid" => $uid, "loginjs" => $loginjs, "loginhtml" => $loginhtml, "user" => $fly["username"], "pass" => $fly["passwd"]];
    return $arr;
}
function sgwingetcode($fly, $cookie_jar)
{
    $code = '../upload/' . $fly['userid'] . $fly["id"] . rand(111, 999) . ".png";
    unlink($code);
    $url = trim($fly['url1']) . '/code?_=' . time();
    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "head" => false];
    $res = CURL($send);
    //$res = curls($cookie_jar, $url, false, "", true,false);
    $fp = fopen($code, "w");
    fwrite($fp, $res["res"]);
    fclose($fp);
    $arr = ["status" => 1, "data" => substr($code, 2), 'webtype' => $fly['webtype']];
    return $arr;
}

function idcgetcode($fly, $cookie_jar)
{
    $code = '../upload/' . $fly['userid'] . $fly["id"] . rand(111, 999) . ".png";
    unlink($code);
    $url = trim($fly['url1']) . "/checknum.aspx?ts=".time();
    $send = ["headip" => $fly["ip"], "cookietype" => true, "cookie_jar" => $cookie_jar, "url" => $url, "posttype" => false, "head" => false];
    $res = CURL($send);
    //$res = curls($cookie_jar, $url, false, "", true,false);
    $fp = fopen($code, "w");
    fwrite($fp, $res["res"]);
    fclose($fp);
    $arr = ["status" => 1, "data" => substr($code, 2), 'webtype' => $fly['webtype']];
    return $arr;
}

function getSubstr($str, $leftStr, $rightStr)
{
    $left = strpos($str, $leftStr);
    $right = strpos($str, $rightStr, $left);
    if ($left < 0 or $right < $left) {
        return '';
    }
    return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}