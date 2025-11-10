<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "bank":
        $bank = $msql->arr("select * from `$tb_bank` order by id ", 1);
        $tpl->assign("bank", $bank);
        $tpl->display("money_bank.html");
        break;
    case "setbank":
        $str = str_replace('\\', '', $_POST['str']);
        $arr = json_decode($str, true);
        foreach ($arr as $v) {
            if ($v["bankid"] == '') {
                $bankid   = setupid3($tb_bank, 'bankid');
                $bankname = trim($v["bankname"]);
                $en       = trim($v["en"]);
                $msql->query("insert into `$tb_bank` set bankid='$bankid',bankname='$bankname',en='$en'");
            } else {
                $bankid   = $v["bankid"];
                $bankname = trim($v["bankname"]);
                $en       = trim($v["en"]);
                $msql->query("update `$tb_bank` set bankname='$bankname',en='$en' where bankid='$bankid'");
            }
        }
        echo 1;
        break;
    case "dbank":
        $bankid = $_POST["bankid"];
        $msql->query("select 1 from `$tb_banknum` where bankid='$bankid'");
        $msql->next_record();
        if ($msql->f(0) == 1) {
            echo 2;
        } else {
            $msql->query("delete from `$tb_bank` where bankid='$bankid'");
            echo 1;
        }
        break;
    case "chongzhifs":
        $msql->query("select bankonline,bankatm,weixin,alipay from `$tb_config` ");
        $msql->next_record();
        $tpl->assign("bankonline", $msql->f("bankonline"));
        $tpl->assign("bankatm", $msql->f("bankatm"));
        $tpl->assign("weixin", $msql->f("weixin"));
        $tpl->assign("alipay", $msql->f("alipay"));
        $tpl->display("money_chongzhifs.html");
        break;
    case "setchongzhifs":
        $bankonline = isnum($_POST['bankonline']);
        $bankatm    = isnum($_POST['bankatm']);
        $weixin     = isnum($_POST['weixin']);
        $alipay     = isnum($_POST['alipay']);
        $msql->query("update `$tb_config` set bankonline='$bankonline',bankatm='$bankatm',weixin='$weixin',alipay='$alipay'");
        echo 1;
        break;
    case "banknum":
        $uid = $_REQUEST['uid'];
        if ($uid == '')
            $uid = $userid;
        $bank = $msql->arr("select * from `$tb_banknum` where userid='$uid'", 1);
        foreach ($bank as $k => $v) {
            $bank[$k]["username"] = transu($v["userid"]);
            $bank[$k]["bankname"] = transbank($v["bankid"]);
        }
        $tpl->assign("bank", $bank);
        $tpl->assign("banks", getbank());
        $tpl->assign("moneyuser", getmoneyuser());
        $tpl->assign("uid", $uid);
        $tpl->display("money_banknum.html");
        break;
    case "setbanknum":
        $str = str_replace('\\', '', $_POST['str']);
        $arr = json_decode($str, true);
        foreach ($arr as $v) {
            if ($v["id"] == 'new') {
                $bankid    = trim($v["bankid"]);
                $uid       = trim($v["uid"]);
                $num       = trim($v["num"]);
                $name      = trim($v["name"]);
                $kaihuhang = trim($v["num"]);
                $bankpass  = trim($v["bankpass"]);
                $ifok      = trim($v["ifok"]);
                $msql->query("insert into `$tb_banknum` set bankid='$bankid',name='$name',num='$num',kaihuhang='$kaihuhang',bankpass='$bankpass',ifok='$ifok',userid='$uid'");
            } else {
                $id        = trim($v["id"]);
                $uid       = trim($v["uid"]);
                $num       = trim($v["num"]);
                $name      = trim($v["name"]);
                $kaihuhang = trim($v["kaihuhang"]);
                $bankpass  = trim($v["bankpass"]);
                $ifok      = trim($v["ifok"]);
                $msql->query("update `$tb_banknum` set name='$name',num='$num',kaihuhang='$kaihuhang',bankpass='$bankpass',ifok='$ifok' where id='$id' and userid='$uid'");
            }
        }
        echo 1;
        break;
    case "dbanknum":
        $id  = $_POST["id"];
        $uid = $_POST["uid"];
        $msql->query("delete from `$tb_banknum` where id='$id' and userid='$uid'");
        echo 1;
        break;
    case "moneyuser":
        $upage    = $_REQUEST['upage'];
        $status   = $_REQUEST['status'];
        $online   = $_REQUEST['online'];
        $username = trim($_REQUEST['username']);
        $fid      = $_REQUEST['fid'];
        if (checkid($fid)) {
            $whi = " and fid='$fid' ";
        }
        if ($username != '') {
            $whi .= "  and  (username like '%$username%' or name like '%$username%' or tname like '%$username%' or userid='$username')  ";
        }
        if ($status != 'all' & $status != '') {
            $whi .= " and status='$status' ";
        }
        if ($online == 1) {
            $whi .= " and online=1 ";
        }
        $msql->query("select count(id) from `$tb_user` where fudong=1 and wid in(select wid from `$tb_web` where moneytype=1) " . $whi);
        $msql->next_record();
        $rcount = $msql->f(0);
        $psize  = $config['psize1'];
        $upage  = r1p($upage);
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize + 1);
        if ($upage > $pcount)
            $upage = 1;
        if ($upage < 1)
            $upage = 1;
        $tpl->assign("rcount", $rcount);
        $tpl->assign("pcount", $pcount);
        $tpl->assign("upage", $upage);
        $msql->query("select * from `$tb_user` where fudong=1 and wid in(select wid from `$tb_web` where moneytype=1) " . $whi . " order by lastlogintime desc limit " . ($upage - 1) * $psize . "," . $psize);
        $user = array();
        $i    = 0;
        while ($msql->next_record()) {
            $user[$i]['userid']        = $msql->f('userid');
            $user[$i]['username']      = $msql->f('username');
            $user[$i]['online']        = $msql->f('online');
            $user[$i]['tname']         = $msql->f('tname');
            $user[$i]['qq']            = $msql->f('qq');
            $user[$i]['tel']           = $msql->f('tel');
            $user[$i]['sex']           = $msql->f('sex');
            $user[$i]['birthday']      = $msql->f('birthday');
            $user[$i]['regtime']       = substr($msql->f('regtime'), 0, 10);
            $user[$i]['lastlogintime'] = substr($msql->f('lastlogintime'), 0, 10);
            $user[$i]['status']        = $msql->f('status');
            $user[$i]['statusz']       = transstatus($msql->f('status'));
            $user[$i]['kmaxmoney']     = $msql->f('kmaxmoney');
            $user[$i]['kmoney']        = $msql->f('kmoney');
            $user[$i]['wid']           = $msql->f('wid');
            $fsql->query("select layer,webname,maxlayer from `$tb_web` where wid='" . $msql->f('wid') . "'");
            $fsql->next_record();
            $user[$i]['web'] = $fsql->f('webname');
            if ($msql->f('layer') == 1) {
                $user[$i]['upuser'] = "admin";
            } else {
                $fsql->query("select username,tname from `$tb_user` where userid='" . $msql->f('fid') . "'");
                $fsql->next_record();
                $user[$i]['upuser'] = $fsql->f('username');
                $user[$i]['upname'] = $fsql->f('tname');
            }
            $user[$i]['fid']  = $msql->f('fid');
            $user[$i]['fids'] = array();
            for ($j = 0; $j < $msql->f('layer') - 1; $j++) {
                $user[$i]['fids'][] = transu($msql->f('fid' . ($j + 1)));
            }
            $fsql->query("select count(id) from `$tb_user` where fid='" . $msql->f('userid') . "' ");
            $fsql->next_record();
            $user[$i]['downnum'] = r0($fsql->f(0));
            $i++;
        }
        $tpl->assign("user", $user);
        $tpl->assign("online", $online);
        $tpl->display("money_moneyuser.html");
        break;
    case "userxx":
        $uid = $_POST['uid'];
        if (!checkid($uid))
            exit;
        $msql->query("select * from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $user             = array();
        $user["shengshi"] = $msql->f("shengshi");
        $user["street"]   = $msql->f("street");
        $user["shr"]      = $msql->f("shr");
        $user["bz"]       = $msql->f("bz");
        $fsql->query("select * from `$tb_banknum` where userid='$uid'");
        $i            = 0;
        $user["bank"] = array();
        while ($fsql->next_record()) {
            $user["bank"][$i]["bank"]      = transbank($fsql->f('bankid'));
            $user["bank"][$i]["name"]      = $fsql->f("name");
            $user["bank"][$i]["kaihuhang"] = $fsql->f("kaihuhang");
            $user["bank"][$i]["num"]       = $fsql->f("num");
            $user["bank"][$i]["ifok"]      = $fsql->f("ifok");
            $i++;
        }
        echo json_encode($user);
        break;
    case "chongzhi":
        $sdate = rdates($_REQUEST['sdate']);
        $edate = rdates($_REQUEST['edate']);
        $tpl->assign("sdate", $sdate);
        $tpl->assign("edate", $edate);
        $upage    = $_REQUEST['upage'];
        $status   = $_REQUEST['status'];
        $username = strip_tags(($_REQUEST['username']));
        if ($status != '' & ($status == 0 | $status == 1 | $status == 2)) {
            $whi .= " and status='$status' ";
        }
        if ($username != '') {
            $whi .= " and userid in (select userid from `$tb_user` where username like '%$username%' or name like '%$username%' or tname like '%$username%' or userid='$username') ";
        }
        if ($sdate != '' & $edate != '') {
            $sdate = $sdate . " 00:00:00";
            $edate = $edate . " 23:59:59";
            $whi .= " and tjtime>='$sdate' and tjtime<='$edate' ";
        }
        $msql->query("select count(id) from `$tb_money` where mtype=0  " . $whi);
        $msql->next_record();
        $rcount = $msql->f(0);
        $psize  = $config['psize1'];
        $upage  = r1p($upage);
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize + 1);
        if ($upage > $pcount)
            $upage = 1;
        if ($upage < 1)
            $upage = 1;
        $tpl->assign("rcount", $rcount);
        $tpl->assign("pcount", $pcount);
        $tpl->assign("upage", $upage);
        $msql->query("select * from `$tb_money` where mtype=0 " . $whi . " order by tjtime desc limit " . ($upage - 1) * $psize . "," . $psize);
        $marr = array();
        $i    = 0;
        while ($msql->next_record()) {
            $marr[$i]['ustatus'] = $msql->f("status");
            $marr[$i]['status']  = moneystatus($msql->f("status"));
            $marr[$i]['mtype']   = moneymtype($msql->f("mtype"));
            $marr[$i]['money']   = $msql->f("money");
            $marr[$i]['sxfei']   = $msql->f("sxfei");
            $marr[$i]['tjtime']  = $msql->f("tjtime");
            $marr[$i]['bz']      = $msql->f("bz");
            $marr[$i]['ms']      = $msql->f("ms");
            $marr[$i]['fs']      = moneyfs($msql->f("fs"));
            $marr[$i]['bank']    = $msql->f("bank");
            $marr[$i]['sname']   = $msql->f('sname');
            $marr[$i]['snum']    = $msql->f('snum');
            $marr[$i]['uname']   = $msql->f('uname');
            $marr[$i]['unum']    = $msql->f('unum');
            $marr[$i]['cuntime'] = $msql->f("cuntime");
            $marr[$i]['pass']    = $msql->f("pass");
            $marr[$i]['id']      = $msql->f("id");
            $marr[$i]['userid']  = $msql->f("userid");
            $fsql->query("select username,tname from `$tb_user` where userid='" . $msql->f("userid") . "'");
            $fsql->next_record();
            $marr[$i]['username'] = $fsql->f("username");
            $marr[$i]['tname']    = $fsql->f("tname");
            $marr[$i]['tjid']     = $msql->f("tjid");
            $fsql->query("select username,tname from `$tb_user` where userid='" . $msql->f("tjid") . "'");
            $fsql->next_record();
            $marr[$i]['tjname'] = $fsql->f("username");
            if (substr($msql->f("cltime"), 0, 1) == 0) {
                $marr[$i]['cltime'] = '';
            } else {
                $marr[$i]['cltime'] = $msql->f("cltime");
            }
            $marr[$i]['clid'] = $msql->f("clid");
            $fsql->query("select adminname from `$tb_admins` where adminid='" . $msql->f("clid") . "'");
            $fsql->next_record();
            $marr[$i]['clname'] = $fsql->f("adminname");
            $i++;
        }
        $tpl->assign("marr", $marr);
        for ($i = 1; $i <= $pcount; $i++) {
            $parr[] = $i;
        }
        $tpl->assign("parr", $parr);
        $tpl->assign("status", $status);
        $tpl->assign("hide", $_SESSION['hide']);
        $tpl->display("money_chongzhi.html");
        break;
    case "upcz":
        $id = $_POST['ids'];
        if (!is_numeric($id))
            exit;
        $sxfei = $_POST['sxfei'];
        $ms    = $_POST['ms'];
        $bz    = $_POST['bz'];
        $sql   = "update `$tb_money` set sxfei='$sxfei',ms='$ms',bz='$bz' where id='$id'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "delcz":
        if ($_SESSION['hide'] != 1)
            exit;
        $id = explode('|', $_POST["idstr"]);
        foreach ($id as $v) {
            if (is_numeric($v) & $v != '') {
                $msql->query("delete from `$tb_money` where id='$v' and mtype=0");
            }
        }
        echo 1;
        break;
    case "upczstatus":
        $id = $_POST['ids'];
        if (!is_numeric($id))
            exit;
        $status = $_POST["status"];
        if ($status != 1 & $status != 2)
            exit;
        $msql->query("select * from `$tb_money` where id='$id'");
        $msql->next_record();
        if ($msql->f('id') == '')
            exit;
        if ($status == 1 & ($msql->f('status') == 2 | $msql->f('status') == 0)) {
            $money = (float) ($msql->f('money') - $msql->f('sxfei'));
            $fsql->query("select kmoney,kmaxmoney from `$tb_user` where userid='" . $msql->f('userid') . "'");
            $fsql->next_record();
            $je = $fsql->f('kmoney') + $money;
            userchange("存入现金额度" . abs($money) . "!原额度" . $msql->f('kmoney') . ",现额度" . $je . "", $msql->f('userid'));
            $fsql->query("update `$tb_user` set kmaxmoney=kmaxmoney+$money',kmoney=kmoney+$money where userid='" . $msql->f('userid') . "'");
            $fsql->query("update `$tb_money` set cltime=NOW(),clid='$adminid',status=1 where id='$id'");
            echo 1;
        } else if ($status == 2 & $msql->f('status') == 1) {
            $money = (float) ($msql->f('money') - $msql->f('sxfei'));
            $fsql->query("select kmoney,kmaxmoney from `$tb_user` where userid='" . $msql->f('userid') . "'");
            $fsql->next_record();
            if ($fsql->f('kmoney') < $money) {
                echo 20;
                exit;
            }
            $je = $fsql->f('kmoney') - $money;
            userchange("提取现金额度" . abs($money) . "!原额度" . $msql->f('kmoney') . ",现额度" . $je . "", $msql->f('userid'));
            $fsql->query("update `$tb_user` set kmaxmoney=kmaxmoney+$money',kmoney=kmoney+$money where userid='" . $msql->f('userid') . "'");
            $fsql->query("update `$tb_money` set cltime=NOW(),clid='$adminid',status=2 where id='$id'");
            echo 1;
        } else {
            $fsql->query("update `$tb_money` set cltime=NOW(),clid='$adminid',status='$status' where id='$id'");
            echo 1;
        }
        break;
    case "tikuan":
        $sdate = rdates($_REQUEST['sdate']);
        $edate = rdates($_REQUEST['edate']);
        $tpl->assign("sdate", $sdate);
        $tpl->assign("edate", $edate);
        $upage    = $_REQUEST['upage'];
        $status   = $_REQUEST['status'];
        $username = strip_tags(($_REQUEST['username']));
        if ($status != '' & ($status == 0 | $status == 1 | $status == 2)) {
            $whi .= " and status='$status' ";
        }
        if ($username != '') {
            $whi .= " and userid in (select userid from `$tb_user` where username like '%$username%' or name like '%$username%' or tname like '%$username%' or userid='$username') ";
        }
        if ($sdate != '' & $edate != '') {
            $sdate = $sdate . " 00:00:00";
            $edate = $edate . " 23:59:59";
            $whi .= " and tjtime>='$sdate' and tjtime<='$edate' ";
        }
        $msql->query("select count(id) from `$tb_money` where mtype=1 " . $whi);
        $msql->next_record();
        $rcount = $msql->f(0);
        $psize  = $config['psize1'];
        $upage  = r1p($upage);
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize + 1);
        if ($upage > $pcount)
            $upage = 1;
        if ($upage < 1)
            $upage = 1;
        $tpl->assign("rcount", $rcount);
        $tpl->assign("pcount", $pcount);
        $tpl->assign("upage", $upage);
        $msql->query("select * from `$tb_money` where mtype=1 " . $whi . " order by tjtime desc limit " . ($upage - 1) * $psize . "," . $psize);
        $marr = array();
        $i    = 0;
        while ($msql->next_record()) {
            $marr[$i]['ustatus'] = $msql->f("status");
            $marr[$i]['status']  = moneystatus($msql->f("status"));
            $marr[$i]['mtype']   = moneymtype($msql->f("mtype"));
            $marr[$i]['money']   = $msql->f("money");
            $marr[$i]['sxfei']   = $msql->f("sxfei");
            $marr[$i]['tjtime']  = $msql->f("tjtime");
            $marr[$i]['bz']      = $msql->f("bz");
            $marr[$i]['ms']      = $msql->f("ms");
            $marr[$i]['fs']      = moneyfs($msql->f("fs"));
            $marr[$i]['bank']    = $msql->f("bank");
            $marr[$i]['sname']   = $msql->f('sname');
            $marr[$i]['snum']    = $msql->f('snum');
            $marr[$i]['uname']   = $msql->f('uname');
            $marr[$i]['unum']    = $msql->f('unum');
            $marr[$i]['cuntime'] = $msql->f("cuntime");
            $marr[$i]['pass']    = $msql->f("pass");
            $marr[$i]['id']      = $msql->f("id");
            $marr[$i]['userid']  = $msql->f("userid");
            $fsql->query("select username,tname from `$tb_user` where userid='" . $msql->f("userid") . "'");
            $fsql->next_record();
            $marr[$i]['username'] = $fsql->f("username");
            $marr[$i]['tname']    = $fsql->f("tname");
            $marr[$i]['tjid']     = $msql->f("tjid");
            $fsql->query("select username,tname from `$tb_user` where userid='" . $msql->f("tjid") . "'");
            $fsql->next_record();
            $marr[$i]['tjname'] = $fsql->f("username");
            if (substr($msql->f("cltime"), 0, 1) == 0) {
                $marr[$i]['cltime'] = '';
            } else {
                $marr[$i]['cltime'] = $msql->f("cltime");
            }
            $marr[$i]['clid'] = $msql->f("clid");
            $fsql->query("select adminname from `$tb_admins` where adminid='" . $msql->f("clid") . "'");
            $fsql->next_record();
            $marr[$i]['clname'] = $fsql->f("adminname");
            $i++;
        }
        $tpl->assign("marr", $marr);
        for ($i = 1; $i <= $pcount; $i++) {
            $parr[] = $i;
        }
        $tpl->assign("parr", $parr);
        $tpl->assign("status", $status);
        $tpl->assign("hide", $_SESSION['hide']);
        $tpl->display("money_tikuan.html");
        break;
    case "uptk":
        $id = $_POST['ids'];
        if (!is_numeric($id))
            exit;
        $sxfei   = $_POST['sxfei'];
        $ms      = $_POST['ms'];
        $bz      = $_POST['bz'];
        $cuntime = $_POST['cuntime'];
        $sql     = "update `$tb_money` set sxfei='$sxfei',ms='$ms',bz='$bz',cuntime='$cuntime' where id='$id'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "deltk":
        if ($_SESSION['hide'] != 1)
            exit;
        $id = explode('|', $_POST["idstr"]);
        foreach ($id as $v) {
            if (is_numeric($v) & $v != '') {
                $msql->query("delete from `$tb_money` where id='$v' and mtype=1");
            }
        }
        echo 1;
        break;
    case "uptkstatus":
        $id = $_POST['ids'];
        if (!is_numeric($id))
            exit;
        $status = $_POST["status"];
        if ($status != 1 & $status != 2)
            exit;
        $msql->query("update `$tb_money` set status='$status',cltime=NOW(),clid='$adminid' where id='$id'");
        echo 1;
        break;
    case "notices":
        $username = trim($_REQUEST['username']);
        $upage    = $_REQUEST['upage'];
        $whi      = '';
        if ($username != '') {
            $whi .= " and userid in (select userid from `$tb_user` where username like '%$username%' or name like '%$username%' or tname like '%$username%' or userid='$username') ";
        }
        $sdate = rdates($_REQUEST['sdate']);
        $edate = rdates($_REQUEST['edate']);
        $tpl->assign("sdate", $sdate);
        $tpl->assign("edate", $edate);
        $s   = $sdate . " 00:00:00";
        $e   = $edate . " 23:59:59";
        $whi = " time>='$s' and time<='$e'  " . $whi;
        $msql->query("select count(id) from `$tb_notices` where $whi ");
        $msql->next_record();
        $rcount = $msql->f(0);
        $psize  = $config['psize1'];
        $upage  = r1p($upage);
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize + 1);
        if ($upage > $pcount)
            $upage = 1;
        if ($upage < 1)
            $upage = 1;
        $tpl->assign("rcount", $rcount);
        $tpl->assign("pcount", $pcount);
        $tpl->assign("upage", $upage);
        $msql->query("select * from `$tb_notices` where $whi order by time desc limit " . ($upage - 1) * $psize . "," . $psize);
        $notices = array();
        $i       = 0;
        while ($msql->next_record()) {
            $fsql->query("select username,tname from `$tb_user` where userid='" . $msql->f('userid') . "'");
            $fsql->next_record();
            $notices[$i]['username'] = $fsql->f("username");
            $notices[$i]['tname']    = $fsql->f("tname");
            $notices[$i]['title']    = $msql->f('title');
            $notices[$i]['time']     = $msql->f('time');
            $notices[$i]['du']       = $msql->f('du');
            $notices[$i]['content']  = $msql->f('content');
            $notices[$i]['id']       = $msql->f('id');
            $notices[$i]['userid']   = $msql->f('userid');
            if ($msql->f("sendid") == 99999999) {
                $notices[$i]['senduser'] = "系统";
            } else {
                $notices[$i]['senduser'] = transuser($msql->f('sendid'), 'username');
            }
            $i++;
        }
        $tpl->assign("notices", $notices);
        $tpl->display("money_notices.html");
        break;
    case "delnotices":
        $id = $_POST['id'];
        $id = explode('|', $id);
        foreach ($id as $v) {
            if ($v != '' & is_numeric($v)) {
                $msql->query("delete from `$tb_notices` where id='$v'");
            }
        }
        echo 1;
        break;
    case "sendmess":
        $uid = trim($_POST['uid']);
        if (!checkid($uid))
            exit;
        $title   = trim($_POST['title']);
        $content = $_POST['content'];
        $msql->query("insert into `$tb_notices` set userid='$uid',title='$title',du='$du',sendid='99999999',content='$content',time=NOW()");
        echo 1;
        break;
    case "xgmess":
        $uid = trim($_POST['uid']);
        if (!checkid($uid))
            exit;
        $title   = trim($_POST['title']);
        $content = $_POST['content'];
        $id      = trim($_POST['id']);
        $msql->query("update `$tb_notices` set content='$content',title='$title' where userid='$uid' and id='$id'");
        echo 1;
        break;
    case "tiquallmoney":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $etype = $_POST['etype'];
        $msql->query("select ifagent,layer,fudong,wid,kmoney,kmaxmoney from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $ifagent = $msql->f('ifagent');
        $layer   = $msql->f('layer');
        $fudong  = $msql->f('fudong');
        $kmoney  = $msql->f('kmoney');
        $fsql->query("select moneytype from `$tb_web` where wid='" . $msql->f('wid') . "'");
        $fsql->next_record();
        if ($fsql->f('moneytype') == 0) {
            echo 2;
            exit;
        }
        if ($ifagent == 1) {
            if ($etype == 'slow') {
                $msql->query("update `$tb_user` set maxmoney=0,money=0 where  fid" . $layer . "='$uid'");
                $msql->query("update `$tb_user` set maxmoney=0,money=0 where userid='$uid'");
                $msql->query("select userid from `$tb_user` where  fid" . $layer . "='$uid'");
                while ($msql->next_record()) {
                    userchange("提取全部低频额度!", $msql->f('userid'));
                }
                userchange("提取全部低频额度!", $uid);
            } else if ($etype == 'fast') {
                $msql->query("update `$tb_user` set kmaxmoney=0,kmoney=0 where  fid" . $layer . "='$uid'");
                $msql->query("update `$tb_user` set kmaxmoney=0,kmoney=0 where userid='$uid'");
                $msql->query("select userid from `$tb_user` where  fid" . $layer . "='$uid'");
                while ($msql->next_record()) {
                    userchange("提取全部快开彩额度!", $msql->f('userid'));
                }
                userchange("提取全部快开彩额度!", $uid);
            }
        } else {
            if ($etype == 'slow') {
                $msql->query("update `$tb_user` set maxmoney=0,money=0 where userid='$uid'");
                userchange("提取全部低频彩额度!", $uid);
            } else if ($etype == 'fast') {
                $msql->query("update `$tb_user` set kmaxmoney=0,kmoney=0 where userid='$uid'");
                if ($fudong == 1) {
                    $msql->query("update `$tb_user` set kmaxmoney=0,kmoney=0,ftime=NOW() where userid='$uid'");
                    userchange("提取全部现金额度!", $uid);
                    $sql = "insert into `$tb_money` set userid='$uid',mtype=1,money='$kmoney',sxfei=0,cuntime=NOW(),status=1,tjid='$userid',tjtime=NOW(),clid='$userid',cltime=NOW()";
                    $msql->query($sql);
                } else {
                    $msql->query("update `$tb_user` set kmaxmoney=0,kmoney=0 where userid='$uid'");
                    userchange("提取全部快开彩额度!", $uid);
                }
            }
        }
        echo 1;
        break;
    case "setmoney":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $etype = $_POST['etype'];
        $je    = $_POST['je'];
        $types = $_POST['types'];
        if (!is_numeric($je) | $je % 1 != 0 | $je < 1)
            exit;
        if ($types != 0)
            $je = 0 - $je;
        $msql->query("select ifagent,layer,maxmoney,money,kmaxmoney,kmoney,fudong,fid,wid from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $ifagent = $msql->f('ifagent');
        $layer   = $msql->f('layer');
        $fid     = $msql->f('fid');
        $fsql->query("select moneytype from `$tb_web` where wid='" . $msql->f('wid') . "'");
        $fsql->next_record();
        if ($fsql->f('moneytype') == 0) {
            echo 90;
            exit;
        }
        $kmaxmoney = $msql->f('kmaxmoney') + $je;
        $kmoney    = $msql->f('kmoney') + $je;
        if ($je < 0) {
            if ($msql->f('kmoney') < abs($je)) {
                echo 30;
                exit;
            }
            $sql = "update `$tb_user` set kmaxmoney='$kmoney',kmoney='$kmoney',ftime=NOW() where userid='$uid'";
            $fsql->query("select 1 from `$tb_lib` where userid='$uid' and z=9");
            $fsql->next_record();
            if ($fsql->f(0) == 1) {
                echo 32;
                exit;
            }
            $fsql->query($sql);
            userchange("提取现金额度" . abs($je) . "!原额度" . $msql->f('kmoney') . ",现额度" . $kmoney . "", $uid);
            $sql = "insert into `$tb_money` set userid='$uid',mtype=1,money='$kmoney',sxfei=0,cuntime=NOW(),status=1,tjid='$userid',tjtime=NOW(),clid='$userid',cltime=NOW()";
            $fsql->query($sql);
			echo 31;
			exit;
        } else {
            $sql = "update `$tb_user` set kmaxmoney='$kmoney',kmoney='$kmoney',ftime=NOW() where userid='$uid'";
            $fsql->query("select 1 from `$tb_lib` where userid='$uid' and z=9");
            $fsql->next_record();
            if ($fsql->f(0) == 1) {
                echo 42;
                exit;
            }
            $fsql->query($sql);
            userchange("存入现金额度" . abs($je) . "!原额度" . $msql->f('kmoney') . ",现额度" . $kmoney . "", $uid);
            $sql = "insert into `$tb_money` set userid='$uid',mtype=0,money='$kmoney',sxfei=0,cuntime=NOW(),status=1,tjid='$userid',tjtime=NOW(),clid='$userid',cltime=NOW()";
            $fsql->query($sql);
            echo 41;
            exit;
        }
        break;
	case "getcztx":
	    $time  = sqltime(time()-86400);
	    $msql->query("select 1 from `$tb_money` where status=0 and tjtime>'$time'");
		$msql->next_record();
		if($msql->f(0)==1){
		   echo 1;
		}else {
		   echo 0;
		}
	break;
}
?>