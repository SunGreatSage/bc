<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
if (transuser($userid, 'status') != 1)
    exit;
switch ($_REQUEST['xtype']) {
    case "show":
        $tpl->assign("fid", $userid);
        $tpl->assign("username", transuser($userid, 'username'));
        $tpl->assign("flayer", transuser($userid, 'layer'));
        $tpl->assign("layer", $config['layer']);
        $tpl->assign("maxlayer", $config['maxlayer']);
        $tpl->assign("maxrenflag", $config['maxrenflag']);
        $tpl->assign("yingdeny", $config['yingdeny']);
        $tpl->assign("toplayer", transuser($userid, 'layer'));
        $tpl->assign("config", $config);
         transuser($userid,'moneypass')=="" ? $moneypassflag =0 : $moneypassflag =1 ;
        $tpl->assign("moneypassflag",$moneypassflag);
        $tpl->display("suser.html");
        break;
    case "getuser":
        $melayer   = 0;
        $upage     = $_POST['upage'];
        $fudong    = $_POST['fudong'];
        $layer     = $_POST['layer'];
        $layertype = $_POST['layertype'];
        $status    = $_POST['status'];
        $online    = $_POST['online'];
        $fid       = $_POST['fid'];
        $uid       = $userid;
        $username  = trim($_POST['username']);
        $flayer    = transuser($fid, 'layer');
        
        $sql = " ifson=0 ";
        
        if ($layertype == 0) {
            $sql .= " and fid='$fid' and ifagent=1 ";
        } else if ($layertype == 1) {
            $sql .= " and fid='$fid' and ifagent=0 ";
        } else if ($layertype == 2) {
            $flayer = transuser($fid, 'layer');
            if ($flayer == 0) {
                $sql .= " and ifagent=1 and layer='$layer'";
            } else {
                
                $sql .= " and fid" . $flayer . "='$fid' and ifagent=1 and layer='$layer'";
            }
        } else if ($layertype == 3) {
            $flayer = transuser($fid, 'layer');
            
            if ($flayer == 0) {
                $sql .= " and ifagent=1 and userid!=99999999";
            } else {
                $sql .= " and fid" . $flayer . "='$fid' and ifagent=1 ";
            }
        } else if ($layertype == 4) {
            $flayer = transuser($fid, 'layer');
            if ($flayer == 0) {
                $sql .= "  and ifagent=0 and userid!=99999999";
            } else {
                $sql .= " and fid" . $flayer . "='$fid' and ifagent=0 ";
            }
        } else {
            exit;
        }
        
        
        if ($username != '') {
            $whi = "  and  (username like '%$username%' or name like '%$username%' or userid='$username')  ";
        }
        
        if ($status != 'all') {
            $whi .= " and status='$status' ";
        }
        if ($online == 1) {
            $whi .= " and online=1 ";
        }
        if ($fudong != 'all') {
            $whi .= " and fudong='$fudong' ";
        }
        $sql .= $whi;
        
        $msql->query("select count(id) from `$tb_user` where" . $sql);
        $msql->next_record();
        $rcount = $msql->f(0);
        $psize  = $config['psize1'];
        $upage  = r1p($upage);
        $pcount = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize + 1);
        if ($upage > $pcount)
            $upage = 1;
        if ($upage < 1)
            $upage = 1;
        $msql->query("select * from `$tb_user` where " . $sql . " order by lastlogintime desc limit " . ($upage - 1) * $psize . "," . $psize);
        $user = array();
        $i    = 0;
        while ($msql->next_record()) {
            $layer                     = $msql->f('layer');
            $user[$i]['username']      = strtolower($msql->f('username'));
            $user[$i]['userid']        = $msql->f('userid');
            $user[$i]['online']        = $msql->f('online');
            $user[$i]['regtime']       = substr($msql->f('regtime'), 0, 19);
            $user[$i]['lastlogintime'] = substr($msql->f('lastlogintime'), 0, 10);
            ;
            $user[$i]['name']      = $msql->f('name');
            $user[$i]["layer"]     = $msql->f('layer');
            $user[$i]["utype"]     = transutype($msql->f('ifagent'));
            $user[$i]["ifagent"]   = $msql->f('ifagent');
            $user[$i]["pan"]       = implode(',', json_decode($msql->f('pan'), true));
            $user[$i]['maxmoney']  = number_format($msql->f('maxmoney'));
            $user[$i]['kmaxmoney'] = number_format($msql->f('kmaxmoney'));
            if ($msql->f('ifagent') == 0 | $msql->f('fudong') == 1) {
                $user[$i]['money']  = number_format($msql->f('money'));
                $user[$i]['kmoney'] = number_format($msql->f('kmoney'));
            } else {
                $user[$i]['money']  = number_format(getmaxmoney($msql->f('userid')));
                $user[$i]['kmoney'] = number_format(getkmaxmoney($msql->f('userid')));
            }
            if($user[$i]['money']<0) $user[$i]['money']=0;
            if($user[$i]['kmoney']<0) $user[$i]['kmoney']=0;
            $user[$i]['fudong']  = $msql->f('fudong');
            $user[$i]['status']  = $msql->f('status');
            $user[$i]['statusz'] = transstatus($msql->f('status'));
            $user[$i]['fid']     = $msql->f('fid');
            $user[$i]['fname']   = strtolower(transuser($msql->f('fid'), 'username'));
            $user[$i]['fids']    = implode(',', getfids($msql->f('userid'), $melayer));
            $user[$i]['plc']     = $msql->f('plc');
            $fsql->query("select count(id) from `$tb_user` where fid='" . $msql->f('userid') . "' and ifson=0");
            $fsql->next_record();
            $user[$i]['downnum'] = r0($fsql->f(0));
            
            $fsql->query("select count(id) from `$tb_user` where fid='" . $msql->f('userid') . "' and ifson=0 and ifagent=1");
            $fsql->next_record();
            $user[$i]['downnumag'] = r0($fsql->f(0));
            $fsql->query("select count(id) from `$tb_user` where fid='" . $msql->f('userid') . "' and ifson=0 and ifagent=0");
            $fsql->next_record();
            $user[$i]['downnumu'] = r0($fsql->f(0));
            
            
            $user[$i]['wid'] = $msql->f('wid');
            $fsql->query("select layer,webname,maxlayer from `$tb_web` where wid='" . $msql->f('wid') . "'");
            $fsql->next_record();
            $user[$i]['web']       = $fsql->f('webname');
            $layers                = json_decode($fsql->f('layer'), true);
            $user[$i]["layername"] = $layers[$msql->f('layer') - 1];
			$user[$i]["lyname"] =  mb_substr(($layers[$msql->f('layer') - 1]),0,2);
            if ($msql->f('ifagent') == 0) {
                $user[$i]["layername"] = "会员";
                /*
                if (strpos($user[$i]["layername"], '级') > 0) {
                    $user[$i]["layername"] = str_replace('代理', '会员', $user[$i]["layername"]);
                } else {
                    $user[$i]["layername"] = $user[$i]["layername"] . '(会员)';
                }*/
            }
            $user[$i]['layers']   = $layers;
            $user[$i]['maxlayer'] = $fsql->f('maxlayer');
            if ($layer == 1) {
                $user[$i]['upuser'] = "admin";
            } else {
                $user[$i]['upuser'] = strtolower(transuser($msql->f('fid'), 'username'));
            }
            if($msql->f("status")==0){
                $user[$i]['xstatus'] = 3;
            }else{
                $user[$i]['xstatus'] = $msql->f("status");
            }
            $i++;
        }
        $tmp = array_column($user, 'xstatus');
        array_multisort($tmp, SORT_ASC, $user);
        $tpl->assign("user", $user);
        //$tpl->assign("sql", $sql.$_POST['fudong'].$fudong);
        $tpl->assign("melayer", transuser($userid, 'layer'));
        $tpl->assign("flayer", $flayer);
        $tpl->assign('hides', $_SESSION['hides']);
        $tpl->assign('pcount', $pcount);
        $tpl->assign('rcount', $rcount);
        $tpl->assign('upage', $upage);
        $tpl->assign("config", $config);
        $tpl->display("suserlist.html");
        break;
    case "getzc":
        $uid = $_POST['uid'];
        if (!checkid($uid))
            exit;
        
        $msql->query("select layer,wid from `$tb_user` where userid='$uid'");
        $msql->next_record();
        if ($msql->f('layer') == '')
            exit;
        
        $u  = getfid($uid);
        $zc = getzcnewall($uid, $u, $msql->f('layer'),$config['zcmode']);
        if (is_array($u)) {
            $msql->query("select layer from `$tb_web` where wid='" . $msql->f('wid') . "'");
            $msql->next_record();
            $layername = json_decode($msql->f('layer'), true);
        }
        $u[0]  = 99999999;
        $layer = transuser($userid, 'layer');
        foreach ($u as $k => $v) {
            if ($k < $layer)
                $u[$k] = 0;
        }
        foreach ($u as $k => $v) {
            if ($v == 0) {
                $n[$k] = 0;
            } else {
                $n[$k] = transuser($v, 'username');
            }
        }
        
        $arr = array(
            'u' => $u,
            'n' => $n,
            'zc' => $zc,
            'layername' => $layername
        );
        echo json_encode($arr);
        break;
    case "gfid":
        $fid = $_POST['fid'];
        if (!checkfid($fid))
            exit;
        $msql->query("select fid from `$tb_user` where userid='$fid'");
        $msql->next_record();
        $arr = array(
            '1',
            $msql->f('fid'),
            transuser($msql->f('fid'), 'username')
        );
        echo json_encode($arr);
        break;
    case "gettree":
        $fid = $_POST['fid'];
        if (transuser($fid, 'userid') == '')
            exit;
        $layer = transuser($fid, 'layer');
        $msql->query("select count(id) from `$tb_user` where fid='$fid' and ifagent=1 and ifson=0");
        $msql->next_record();
        $tree[0]['name']      = "直属代理";
        $tree[0]['num']       = $msql->f(0);
        $tree[0]['layertype'] = 0;
        
        $msql->query("select count(id) from `$tb_user` where fid='$fid' and ifagent=0");
        $msql->next_record();
        $tree[1]['name']      = "直属会员";
        $tree[1]['num']       = $msql->f(0);
        $tree[1]['layertype'] = 1;
        $j                    = 2;
        for ($i = $layer + 1; $i <= 8; $i++) {
            if ($fid != 99999999) {
                $msql->query("select count(id) from `$tb_user` where fid" . $layer . "='$fid' and layer='" . $i . "' and ifagent=1 and ifson=0");
            } else {
                $msql->query("select count(id) from `$tb_user` where layer='" . $i . "' and ifagent=1 and ifson=0");
            }
            $msql->next_record();
            if ($msql->f(0) > 0) {
                $tree[$j]['layertype'] = 2;
                $tree[$j]['layer']     = $i;
                $tree[$j]['num']       = $msql->f(0);
                $tree[$j]['name']      = $config['layer'][$i - 1];
                $j++;
            }
        }
        $j++;
        if ($fid != 99999999) {
            $msql->query("select count(id) from `$tb_user` where fid" . $layer . "='$fid' and  ifagent=1 and ifson=0 and layer>0");
        } else {
            $msql->query("select count(id) from `$tb_user` where ifagent=1 and ifson=0 and layer>0");
        }
        $msql->next_record();
        $tree[$j]['name']      = "全部代理";
        $tree[$j]['num']       = $msql->f(0);
        $tree[$j]['layertype'] = 3;
        
        $j++;
        
        if ($fid != 99999999) {
            $msql->query("select count(id) from `$tb_user` where  fid" . $layer . "='$fid' and  ifagent=0 and layer>0");
        } else {
            $msql->query("select count(id) from `$tb_user` where  ifagent=0 and layer>0");
        }
        $msql->next_record();
        $tree[$j]['name']      = "全部会员";
        $tree[$j]['num']       = $msql->f(0);
        $tree[$j]['layertype'] = 4;
        
        echo json_encode($tree);
        break;
    
    case "add":
        if (transuser($userid, 'status') != 1) {
            die;
        }
        $fid = $_REQUEST['fid'];
        if (!is_numeric($fid)) {
            $fid = $userid;
        }
        if (transuser($fid, 'ifagent') == '' | transuser($fid, 'ifagent') == 0 | transuser($fid, 'ifson') == 1) {
            exit;
        }
        
        $maxren = getmaxren($fid);
        if ($maxren <= 0 & $config['maxrenflag'] == 1) {
            header("Content-Type:text/html;charset=utf-8");
            echo "该帐户的可用会员数不足!请联系上级！";
            exit;
        }
        $fsql->query("select * from `$tb_user` where userid='$fid'");
        $fsql->next_record();
        $tpl->assign('status', $fsql->f('status'));
        $tpl->assign('fudong', $fsql->f('fudong'));
        $tpl->assign("plc", $fsql->f('plc'));
        
        if ($fsql->f('fudong') == 1) {
            $tpl->assign("maxmoney", 0);
            $tpl->assign("kmaxmoney", $fsql->f('kmoney'));
            
        } else {
            $tpl->assign("maxmoney", getmaxmoney($fid));
            $tpl->assign("kmaxmoney", getkmaxmoney($fid));
        }
        
        $layer = $fsql->f('layer');
        $tpl->assign('fname', strtolower(transuser($fid,"username")));
        if ($layer > 0) {
            $wid = transuser($fid, 'wid');
            $fsql->query("select * from `$tb_web` where wid='$wid'");
            $fsql->next_record();
            $config['layer'] = json_decode($fsql->f('layer'), true);
            $namehead        = json_decode($fsql->f('namehead'), true);
            $tpl->assign("maxlayer", $fsql->f('maxlayer'));
            $tpl->assign('moneytype', $fsql->f('moneytype'));
        } else {
            $namehead = $config['namehead'];
            $tpl->assign("maxlayer", $config['maxlayer']);
        }
        
        
        $tpl->assign('userhead', $namehead[$layer]);
        $tpl->assign('namelength', $config['namelength']);
        
        if($config['zcmode']==1){
        $gamecs = getgamecs($fid);
        $cg     = count($gamecs);
        for ($i = 0; $i < $cg; $i++) {
            $gamecs[$i]['gname'] = transgame($gamecs[$i]['gid'], 'gname');
        }
        }else{
        $gamecs = getgamezc($fid);
        }
        $tpl->assign('gamecs', $gamecs);
        $pan = json_decode(transuser($fid, "pan"), true);
        $tpl->assign('pan', $pan);
        //$fsql->query("select min(a) from `$tb_points` where userid='$fid'");
        //$fsql->next_record();
        $minpoints = 1;
        $liushui   = array();
        for ($i = 0; $i <= $minpoints; $i += 0.1) {
            $liushui[] = $i;
        }
        $tpl->assign('liushui', $liushui);
        $layer     = $layer + 1;
        $layername = translayer($layer);
        $tpl->assign('usertype', $layername);
        $tpl->assign('layer', $layer);
        $tpl->assign('layername', $layername);
        $tpl->assign('layernamefu', translayer($layer - 1));
        $tpl->assign("maxren", $maxren);
        $tpl->assign("yingdenyje", $yingdenyje);
        if ($fid == 99999999) {
            $msql->query("select webname,wid,maxlayer from `$tb_web`");
            $i = 0;
            while ($msql->next_record()) {
                $web[$i]['wid']      = $msql->f('wid');
                $web[$i]['webname']  = $msql->f('webname');
                $web[$i]['maxlayer'] = $msql->f('maxlayer');
                $i++;
            }
        }
        $tpl->assign('web', $web);
        $tpl->assign("fid", $fid);
        $tpl->assign("action", "add");
        $tpl->assign("maxrenflag", $config['maxrenflag']);
        $tpl->assign("yingdeny", 0);
        $tpl->assign("config", $config);
        if ($_POST['types'] == 'ag' | $layer == 1) {
            $tpl->display("suseradd.html");
        } else {
            $tpl->display("suseradduser.html");
        }
        break;
    
    case "edit":
        if (transuser($userid, 'status') != 1) {
            die;
        }
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $fid = transuser($uid, 'fid');
        $msql->query("select * from `$tb_user` where userid='$uid'");
        $msql->next_record();
        if ($msql->f('userid') != $uid) {
            echo 2;
            exit;
        }
        $fid   = $msql->f('fid');
        $layer = $msql->f('layer');
        if ($layer > 1) {
            $fsql->query("select layer,moneytype from `$tb_web` where wid='" . $msql->f('wid') . "'");
            $fsql->next_record();
            $config['layer'] = json_decode($fsql->f('layer'), true);
            $tpl->assign('moneytype', $fsql->f('moneytype'));
        }
        $tpl->assign("errortimes",$msql->f("errortimes"));
         $msql->f("moneypass")=="" ? $moneypassflag =0 : $moneypassflag =1 ;
        $tpl->assign("moneypassflag",$moneypassflag);
        $tpl->assign('fid', $msql->f('fid'));
        $tpl->assign('fname', strtolower(transuser($msql->f('fid'),"username")));
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign('layername', $config['layer'][$msql->f('layer') - 1]);
        $tpl->assign('layernamefu', translayer($msql->f('layer') - 1));
        
        $tpl->assign('username', strtolower($msql->f('username')));
        $tpl->assign('yingdeny', $msql->f('yingdeny'));
        
        $tpl->assign('tel', $msql->f('tel'));
        $tpl->assign('qq', $msql->f('qq'));
        $tpl->assign('email', $msql->f('email'));
        $tpl->assign('bz', $msql->f('bz'));
        $tpl->assign('bank', $msql->f('bank'));
        $tpl->assign('bankname', $msql->f('bankname'));
        $tpl->assign('banknum', $msql->f('banknum'));
        $tpl->assign('moneypass', $msql->f('moneypass'));
        
        
        $tpl->assign('userid', $msql->f('userid'));
        $tpl->assign('name', $msql->f('name'));
        $tpl->assign('maxren', $msql->f('maxren'));
        $tpl->assign('pan', $msql->f('pan'));
        $tpl->assign('pans', json_decode($msql->f('pan'), true));
        $tpl->assign('defaultpan', $msql->f('defaultpan'));
        
        if ($msql->f('ifagent') == 0) {
            $tpl->assign('money', $msql->f('money'));
            $tpl->assign('maxmoney', $msql->f('maxmoney'));
            $tpl->assign('kmoney', $msql->f('kmoney'));
            $tpl->assign('kmaxmoney', $msql->f('kmaxmoney'));
        } else if ($msql->f('fudong') == 1) {
            $tpl->assign('money', 0);
            $tpl->assign('maxmoney', 0);
            $tpl->assign('kmoney', $msql->f('kmoney'));
            $tpl->assign('kmaxmoney', $msql->f('kmaxmoney'));
            
        } else {
            $tpl->assign('money', getmaxmoney($msql->f('userid')));
            $tpl->assign('maxmoney', $msql->f('maxmoney'));
            $tpl->assign('kmoney', getkmaxmoney($msql->f('userid')));
            $tpl->assign('kmaxmoney', $msql->f('kmaxmoney'));
        }
        
        
        $tpl->assign('fudong', $msql->f('fudong'));
        $tpl->assign('ifexe', $msql->f('ifexe'));
        $tpl->assign('pself', $msql->f('pself'));
        $tpl->assign('plc', $msql->f('plc'));
        $tpl->assign('fidplc', transuser($msql->f('fid'), 'plc'));
        $tpl->assign("ifagent", $msql->f('ifagent'));
        $tpl->assign("status", $msql->f('status'));
        $tpl->assign("mgid", $msql->f('gid'));
        $tpl->assign("cssz", $msql->f('cssz'));
        $tpl->assign("action", 'edit');
        $tpl->assign("dis", getdis($msql->f('userid'), $msql->f('ifagent'), $msql->f('layer'), $msql->f('fudong')));
        
        
        $tpl->assign('fidmaxmoney', getmaxmoney($fid) + $msql->f('maxmoney'));
        $tpl->assign('fidkmaxmoney', getkmaxmoney($fid) + $msql->f('kmaxmoney'));
        
        $tpl->assign('fidmaxren', getmaxren($fid) + $msql->f('maxren'));
        
        $fsql->query("select maxlayer from `$tb_web` where wid='" . $msql->f('wid') . "'");
        $fsql->next_record();
        $tpl->assign("maxlayer", $fsql->f('maxlayer'));
        
        if ($fid == 99999999) {
            $tpl->assign("wid", $msql->f('wid'));
            $msql->query("select webname,wid from `$tb_web`");
            $i = 0;
            while ($msql->next_record()) {
                $web[$i]['wid']     = $msql->f('wid');
                $web[$i]['webname'] = $msql->f('webname');
                $i++;
            }
            $tpl->assign('web', $web);
        }
        $tpl->assign('fidpan', json_decode(transuser($fid, "pan"), true));
        
        
     if($config['zcmode']==1){
         $fidgamecs = getgamecs($fid);  
        $cg        = count($fidgamecs);
        for ($i = 0; $i < $cg; $i++) {
            $fidgamecs[$i]['gname']     = transgame($fidgamecs[$i]['gid'], 'gname'); //$fidgamecs[$i]['ifok'].
            $fidgamecs[$i]['panstatus'] = transgame($fidgamecs[$i]['gid'], 'panstatus');
            $fidgamecs[$i]['fast']      = transgame($fidgamecs[$i]['gid'], 'fast');
            $fsql->query("select * from `$tb_gamecs` where userid='" . $uid . "' and gid='" . $fidgamecs[$i]['gid'] . "'");
            $fsql->next_record();
            $fidgamecs[$i]['uifok']    = $fsql->f('ifok');
            $fidgamecs[$i]['uflyzc']   = $fsql->f('flyzc');
            $fidgamecs[$i]['uzc']      = $fsql->f('zc');
            $fidgamecs[$i]['uupzc']    = $fsql->f('upzc');
            $fidgamecs[$i]['uflytype'] = $fsql->f('flytype');
            $fidgamecs[$i]['uzcmin']  = $fsql->f('zcmin');
        }
     }else{
         $fidgamecs = getgamezc($fid);
        $cg        = count($fidgamecs);
        for ($i = 0; $i < $cg; $i++) {
            $fsql->query("select * from `$tb_gamezc` where userid='" . $uid . "' and typeid='" . $fidgamecs[$i]['typeid'] . "'");
            $fsql->next_record();
            $fidgamecs[$i]['uflyzc']   = $fsql->f('flyzc');
            $fidgamecs[$i]['uzc']      = $fsql->f('zc');
            $fidgamecs[$i]['uupzc']    = $fsql->f('upzc');
            $fidgamecs[$i]['uflytype'] = $fsql->f('flytype');
            $fidgamecs[$i]['uzcmin']  = $fsql->f('zcmin');
        }
        }
        
        $tpl->assign("editstart", $config['editstart']);
        $tpl->assign("editend", $config['editend']);
        $tpl->assign('fidgamecs', $fidgamecs);
        $tpl->assign("maxrenflag", $config['maxrenflag']);
        $tpl->assign("config", $config);
        if ($_POST['types'] == 'ag') {
            $tpl->display("suseredit.html");
        } else {
            $tpl->display("suseredituser.html");
        }
        break;
    case "checkuser":
        $username = $_POST['username'];
        $msql->query("select id from `$tb_user` where username='$username'");
        $msql->next_record();
        if ($msql->f('id') == '') {
            echo 1;
        }
        break;
    case "adduser":
        if (transuser($userid, 'status') != 1) {
            die;
        }
        $fid = $_POST['fid'];
        if ($fid == '' | !is_numeric($fid) | !checkfid($fid))
            $fid = $userid;
        $username = strtoupper($_POST['username']);
        $userpass = md5($_POST['userpass'] . $config['upass']);
        
        $name     = $_POST['name'];
        $tname    = $_POST['tname'];
        $tel      = $_POST['tel'];
        $qq       = $_POST['qq'];
        $sex      = $_POST['sex'];
        $birthday = $_POST['birthday'];
        $bz       = $_POST['bz'];
        $shengshi = $_POST['shengshi'];
        $street   = $_POST['street'];
        $shr      = $_POST['shr'];
        
        $yingdeny = $_POST['yingdeny'];
        
        if (!mb_ereg("^[\w\-\.]{2,32}$", $username)) {
            echo json_encode(array(
                3
            ));
            exit;
        }
        
        $maxmoney  = $_POST['maxmoney'];
        $kmaxmoney = $_POST['kmaxmoney'];
        
        
        if ($maxmoney < 0 | !is_numeric($maxmoney))
            $maxmoney = 0;
        if ($kmaxmoney < 0 | !is_numeric($kmaxmoney))
            $kmaxmoney = 0;
        
        
        
        
        $maxren = $_POST['maxren'];
        if ($maxren < 0 | !is_numeric($maxren))
            $maxren = 0;
        $pan        = $_POST['pan'];
        $defaultpan = $_POST['defaultpan'];
        if (transuser($fid, 'layer') == 0) {
            $wid = $_POST['wid'];
        } else {
            $wid = transuser($fid, 'wid');
        }
        $ifexe = $_POST['ifexe'];
        $pself = $_POST['pself'];
        $cssz  = $_POST['cssz'];
        
        $ifagent = $_POST['ifagent'];
        $layer   = $_POST['layer'];
        $status  = $_POST['status'];
        $plc     = $_POST['plc'];
        
        $fudong    = $_POST['fudong'];
        $fidfudong = transuser($fid, 'fudong');
        if ($fidfudong == 1)
            $fudong = 1;
        $liushui = $_POST['liushui'];

        if ($liushui < 0 & $liushui!='all')
            $liushui = 0;
        
        
        
        if (transuser($fid, 'plc') == 0)
            $plc = 0;
        if ($ifagent == 0) {
            $fdc = 0;
            $plc = 0;
        }
        if ($ifexe == 0)
            $pself = 0;
        $maxrens = getmaxren($fid);
        if ($maxren > $maxrens)
            $maxren = 0;
        if ($fidfudong == 1 & $fid != 99999999) {
            $maxmoney   = 0;
            $kmaxmoneys = transuser($fid, 'kmoney');
            if ($kmaxmoney > $kmaxmoneys)
                $kmaxmoney = $kmaxmoneys;
        } else {
            $maxmoneys  = getmaxmoney($fid);
            $kmaxmoneys = getkmaxmoney($fid);
            if ($maxmoney > $maxmoneys)
                $maxmoney = $maxmoneys;
            if ($kmaxmoney > $kmaxmoneys)
                $kmaxmoney = $kmaxmoneys;
        }
        $money  = $maxmoney;
        $kmoney = $kmaxmoney;
        
        
        
        $gamecs = $_POST['gamecs'];
        $gamecs = str_replace('\\', '', $gamecs);
        $gamecs = json_decode($gamecs, true);
        $garr   = array();
        foreach ($gamecs as $v) {
            $garr[] = $v['gid'];
        }
        
        $layer = transuser($fid, "layer") + 1;
        if ($layer == 1) {
            $fudong = 0;
        }
        $uid  = setupid("$tb_user", "userid");
        $time = time();
        if (date("His") < str_replace(':', '', $config['editstart'])) {
            $ftime = date("Y-m-d ".$config['editend'], time() - 86400);
        } else {
            $ftime = date("Y-m-d ".$config['editend']);
        }
        $sql = "insert into `$tb_user` set username='$username',userid='$uid',userpass='$userpass',name='$name',status='$status',ifagent='$ifagent',layer='$layer',maxren='$maxren',ifexe='$ifexe',pself='$pself',plc='$plc',pan='$pan',defaultpan='$defaultpan',maxmoney='$maxmoney',kmaxmoney='$kmaxmoney',money='$money',kmoney='$kmoney',fudong='$fudong',fid='$fid',wid='$wid',fastje=0,cssz='$cssz',regtime=now(),ftime='$ftime'";
        
        $sql .= ",tname='$tname',tel='$tel',qq='$qq',sex='$sex',bz='$bz',birthday='$birthday',shengshi='$shengshi',street='$street',shr='$shr'";
        
        $thefid = $fid;
        for ($j = ($layer - 1); $j >= 1; $j--) {
            $sql .= ",fid" . $j . "='" . $fid . "'";
            $fid = transuser($fid, "fid");
        }
        $msql->query("select id from `$tb_user` where username='$username'");
        $msql->next_record();
        if ($msql->f('id') != '') {
            echo json_encode(array(
                2
            ));
        } else {
            if ($msql->query($sql)) {
                if ($fudong == 1) {
                    userchange("新增,现金额度$kmaxmoney,", $uid);
                    usermoneylog($uid,$kmaxmoney,$kmaxmoney,'新增');
                    if ($fidfudong == 1 & $thefid != 99999999) {
                        $msql->query("update `$tb_user` set kmaxmoney=kmaxmoney-$kmoney,kmoney=kmoney-$kmoney where userid='$thefid'");
                        usermoneylog($thefid,pr0(0-$kmaxmoney),$kmaxmoneys - $kmaxmoney,'新增用户充值-'.$username);
                    }
                } else {
                    userchange("新增,低频彩额度$maxmoney,快开彩额度$kmaxmoney,", $uid);
                    usermoneylog($uid,$kmaxmoney,$kmaxmoney,'新增');
                    usermoneylog($uid,$maxmoney,$maxmoney,'新增',0);
                }
                if ($layer == 1) {
                    $msql->query("insert into `$tb_play_user` select NULL,gid,$uid,bid,sid,cid,pid,0,0,mp1,mp2,pl,mpl,0,0,xsort,0,0,0,0 from `$tb_play`");
                }
                $msql->query("insert into `$tb_warn` select NULL,gid,$uid,class,je,ks from `$tb_warn` where userid='99999999'");
                $msql->query("insert into `$tb_fastje` select NULL,$uid,je,xsort from `$tb_fastje` where userid='99999999'");
                $msql->query("insert into `$tb_zpan` select NULL,gid,$uid,class,lowpeilv,0 from `$tb_zpan` where userid='$thefid'");
                if($liushui=='all'){
                    $msql->query("insert into `$tb_points` select NULL,gid,$uid,class,ab,0,0,0,0,cmaxje,maxje,minje from `$tb_points` where userid='$thefid'");
                }else{
                    $msql->query("insert into `$tb_points` select NULL,gid,$uid,class,ab,if(a-$liushui<0,0,a-$liushui),if(b-$liushui<0,0,b-$liushui),if(c-$liushui<0,0,c-$liushui),if(d-$liushui<0,0,d-$liushui),cmaxje,maxje,minje from `$tb_points` where userid='$thefid'");
                }
                if($config['zcmode']==1){
                   insertgame($gamecs, $uid);
                }else{
                   insertgamezc($gamecs, $uid);
                }
                $msql->query("update `$tb_user` set gid=(select gid from `$tb_gamecs` where userid='$uid' and ifok=1 order by xsort limit 1)");
                echo json_encode(array(
                    1,
                    transuser($thefid, 'username'),$uid
                ));
            }
        }
        break;
    case "edituser":
        if (transuser($userid, 'status') != 1) {
            die;
        }
        $uid = $_POST['userid'];
        if ($uid == '' | !is_numeric($uid) | !checkfid($uid))
            exit;
        $userpass = $_POST['userpass'];
        $msql->query("select * from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $name = $_POST['name'];

        $tname    = $_POST['tname'];
        $tel      = $_POST['tel'];
        $qq       = $_POST['qq'];
        $sex      = $_POST['sex'];
        $birthday = $_POST['birthday'];
        $bz       = $_POST['bz'];
        $shengshi = $_POST['shengshi'];
        $street   = $_POST['street'];
        $shr      = $_POST['shr'];
        $yingdeny = $_POST['yingdeny'];
        
        
        $maxren     = $_POST['maxren'];
        $pan        = $_POST['pan'];
        $defaultpan = $_POST['defaultpan'];
        if ($msql->f('layer') == 1) {
            $wid = $_POST['wid'];
        } else {
            $wid = transuser($msql->f('fid'), 'wid');
        }

        $ifexe   = $_POST['ifexe'];
        $pself   = $_POST['pself'];
        $cssz    = $_POST['cssz'];
        $ifagent = $_POST['ifagent'];
        $layer   = $_POST['layer'];
        $status  = $_POST['status'];
        $plc     = $_POST['plc'];
        $fudong  = $_POST['fudong'];
        $fsql->query("select moneytype from `$tb_web` where wid='" . $msql->f('wid') . "'");
        $fsql->next_record();
        if ($fsql->f('moneytype') == 1)
            $fudong = 1;
        $mgid = $_POST['mgid'];
        if ($ifexe == 0)
            $pself = 0;
        if (transuser($msql->f('fid'), 'plc') == 0)
            $plc = 0;
        if ($ifagent == 0) {
            $plc = 0;
        }
        $gamecs     = $_POST['gamecs'];
        $ifagentold = $msql->f('ifagent');
        if ($ifagentold == 1 & $ifagent == 0) {
            $fsql->query("select count(id) from `$tb_user` where fid='$uid'");
            $fsql->next_record();
            if ($fsql->f(0) > 0) {
                $ifagent = 1;
            }
        }
        if ($fsql->f('layer') == 1 & $ifexe == 0) {
            $fsql->query("update `$tb_play_user` set peilv1=0,peilv2=0 where userid='$uid'");
        }
        if ($ifagent == 0 | $fsql->f('layer') > 1) {
            $ifexe = 0;
            $pself = 0;
        }
        
        $themaxren = getmaxren($msql->f('fid')) + $msql->f('maxren');
        if ($maxren > $themaxren) {
            $maxren = $themaxren;
        }
        $fsql->query("select sum(maxren),count(id) from `$tb_user` where fid='$uid'");
        $fsql->next_record();
        if ($maxren < $fsql->f(0) + $fsql->f(1)) {
            $maxren = $fsql->f(0) + $fsql->f(1) + 1;
        }
        
        $gamecs     = str_replace('\\', '', $gamecs);
        $gamecs2    = json_decode($gamecs, true);
        $gamecs3    = $gamecs2;
        $cg         = count($gamecs3);
        $his        = date("His");
        $layers     = transuser($uid, 'layer');
        $uidstr     = "fid" . $layers;
        $ustr       = "uid" . $layers;
        $time       = time();
        $fastpoints = 1;
        $hi         = date("His");
        $start      = str_replace(':', '', $config['editstart']);
        $end        = str_replace(':', '', $config['editend']);
        if ($hi < $start | $hi > $end) {
            $fastpoints = 0;
            $stime      = strtotime(date("Y-m-d " . $config['editstart']));
            if (date("His") < $start) {
                $stime = strtotime(date("Y-m-d " . $config['editend'], time() - 86400));
            } else {
                $stime = strtotime(date("Y-m-d " . $config['editend']));
            }

            if ($ifagent == 1) {
                $fsql->query("select 1 from `$tb_lib` where $ustr='$uid' and time > $stime limit 1");
            } else {
                $fsql->query("select 1 from `$tb_lib` where userid='$uid' and time > $stime limit 1");
            }
            $fsql->next_record();
            if ($fsql->f(0) != 1) {
                $fastpoints = 1;
            }

        }
    $uarr=array();  

    if($config['zcmode']==1){   
        for ($i = 0; $i < $cg; $i++) {
            $fsql->query("select panstatus,otherstatus,fast from `$tb_game` where gid='" . $gamecs3[$i]['gid'] . "'");
            $fsql->next_record();
            $tsql->query("select * from `$tb_gamecs` where gid='" . $gamecs3[$i]['gid'] . "' and userid='$uid'");
            $tsql->next_record();
            if ($fsql->f('fast') == 0) {
                if ($fsql->f('panstatus') == 1 & $fastpoints == 0) {
                    $gamecs3[$i]['ifok']    = $tsql->f('ifok');
                    $gamecs3[$i]['zc']      = $tsql->f('zc');
                    $gamecs3[$i]['upzc']    = $tsql->f('upzc');
                    $gamecs3[$i]['zchold']  = $tsql->f('zcmin');
                    $gamecs3[$i]['flytype'] = $tsql->f('flytype');
                }
            } else {
                if ($fastpoints == 0) {
                    $gamecs3[$i]['ifok']    = $tsql->f('ifok');
                    $gamecs3[$i]['zc']      = $tsql->f('zc');
                    $gamecs3[$i]['upzc']    = $tsql->f('upzc');
                    $gamecs3[$i]['zcmin']  = $tsql->f('zcmin');
                    $gamecs3[$i]['flytype'] = $tsql->f('flytype');
                }
            }
            if ($config['editzc'] == 0) {
                $gamecs3[$i]['zc']     = $tsql->f('zc');
                $gamecs3[$i]['upzc']   = $tsql->f('upzc');
                $gamecs3[$i]['zcmin'] = $tsql->f('zcmin');
                $gamecs2[$i]['zc']     = $tsql->f('zc');
                $gamecs2[$i]['upzc']   = $tsql->f('upzc');
                $gamecs2[$i]['zcmin'] = $tsql->f('zcmin');
                continue;
            }
          if ($gamecs3[$i]['zc'] != $tsql->f('zc')) {
              if(!in_array($uid,$uarr)) $uarr[] = $uid;
          }
            if ($ifagent == 1) {
                if ($gamecs3[$i]['zc'] < $tsql->f('zc')) {
                    $tsql->query("update `$tb_gamecs` A,`$tb_user` B set A.zc=0,A.upzc=0,A.zcmin=0 where A.userid=B.userid and B.$uidstr='$uid' and A.gid='" . $gamecs3[$i]['gid'] . "'");
                    $tsql->query("select userid from `$tb_user` where $uidstr='$uid' ");
                    while($tsql->next_record()){
                        if(!in_array($uid,$uarr)) $uarr[]= $tsql->f('userid');
                    }
                  
                }
                if ($gamecs3[$i]['ifok'] == 0) {
                    $tsql->query("update `$tb_gamecs` A,`$tb_user` B set A.zc=0,A.upzc=0,A.zcmin=0,A.flytype=0,A.ifok=0 where A.userid=B.userid and B.$uidstr='$uid' and A.gid='" . $gamecs3[$i]['gid'] . "'");
                }
                if ($gamecs3[$i]['flytype'] != $tsql->f('flytype')) {
                    $tsql->query("update `$tb_gamecs` A,`$tb_user` B set A.flytype=0 where A.userid=B.userid and B.$uidstr='$uid' and A.gid='" . $gamecs3[$i]['gid'] . "'");
                }
            }
        }
        updategame($gamecs3, $uid);
    }else{
        for ($i = 0; $i < $cg; $i++) {
            
        $tsql->query("select * from `$tb_gamezc` where  userid='$uid' and typeid='" . $gamecs3[$i]['typeid'] . "'");
                 $tsql->next_record();
                if ($fastpoints == 0 || $config['editzc'] == 0) {
        
                    $gamecs3[$i]['zc']      = $tsql->f('zc');
                    $gamecs3[$i]['upzc']    = $tsql->f('upzc');
                    $gamecs3[$i]['zcmin']  = $tsql->f('zcmin');
                    $gamecs3[$i]['flytype'] = $tsql->f('flytype');
                }
                
          if ($gamecs3[$i]['zc'] != $tsql->f('zc')) {
              if(!in_array($uid,$uarr)) $uarr[] = $uid;
          }
            if ($ifagent == 1) {
                if ($gamecs3[$i]['zc'] < $tsql->f('zc')) {
                    $tsql->query("update `$tb_gamezc` A,`$tb_user` B set A.zc=0,A.upzc=0,A.zcmin=0 where A.userid=B.userid and B.$uidstr='$uid' and A.typeid='" . $gamecs3[$i]['typeid'] . "'");
                    $tsql->query("select userid from `$tb_user` where $uidstr='$uid' ");
                    while($tsql->next_record()){
                        if(!in_array($uid,$uarr)) $uarr[]= $tsql->f('userid');
                    }
                }
                if ($gamecs3[$i]['flytype'] != $tsql->f('flytype')) {
                    $tsql->query("update `$tb_gamezc` A,`$tb_user` B set A.flytype=0 where A.userid=B.userid and B.$uidstr='$uid' and A.typeid='" . $gamecs3[$i]['typeid'] . "'");
                }
            }
        }
        updategamezc($gamecs3, $uid);
    }
    foreach($uarr as $v){
        userchamge("修改占成",$v);
    }
        $oldplc = transuser($uid, 'plc');
        if ($userpass == '') {
            $sql = "update `$tb_user` set name='$name',ifagent='$ifagent',maxren='$maxren',ifexe='$ifexe',pself='$pself',plc='$plc',pan='$pan',defaultpan='$defaultpan',wid='$wid',cssz='$cssz'";
        } else {
            $userpass = md5(md5($_POST['userpass']) . $config['upass']);
            $sql      = "update `$tb_user` set passtime=0,userpass='$userpass',errortimes=0,name='$name',ifagent='$ifagent',maxren='$maxren',ifexe='$ifexe',pself='$pself',plc='$plc',pan='$pan',defaultpan='$defaultpan',wid='$wid',cssz='$cssz'";
        }
        $sql .= ",tname='$tname',tel='$tel',qq='$qq',sex='$sex',bz='$bz',birthday='$birthday',shengshi='$shengshi',street='$street',shr='$shr'";
        $sql .= " where userid='$uid' ";
        $fsql->query($sql);
        userchange("修改资料", $uid);
        if ($plc == 0 & $oldplc == 1) {
            $fsql->query("update `$tb_user` set plc='0' where fid" . ($layers + 1) . "='$uid' or fid='$uid'");
        }
        
        echo 1;
        break;
    case "upstatus":
        $ustr   = $_POST['ustr'];
        $status = $_POST['status'];
        $u      = explode('|', $ustr);
        $time   = time();
        for ($i = 0; $i < count($u); $i++) {
            if ($u[$i] == '') {
                continue;
            }
            $uid = $u[$i];
            if (transuser(transuser($uid, 'fid'), 'status') == 0 & ($status == 1 | $status == 2)) {
                exit;
            }
            if (transuser(transuser($uid, 'fid'), 'status') == 2 & $status == 1) {
                exit;
            }
            if (!checkfid($uid)) {
                continue;
            }
            $sql = "update `$tb_user` set status='$status' where userid='$uid'";
            $msql->query($sql);
            if (($status == 1 | $status == 2) & transuser($uid, 'ifagent') == 1) {
                $msql->query("update `$tb_user` set status='$status' where fid='$uid' and ifson=1");
            }
            if ($status == 0 | $status == 2) {
                $ugroup = getusergroup($uid);
                $fsql->query("update `$tb_user` set status='$status' where instr('$ugroup',userid)");
            }
            $fsql->query("delete from `$tb_online`  where instr('$ugroup',userid)");
            userchange("修改状态", $u[$i]);
        }
        echo 1;
        break;
    case "copyuser":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $fid = transuser($uid, 'fid');
        if (getmaxren($fid) < 1 & $config['maxrenflag'] == 1) {
            echo 3;
            exit;
        }
        $username = strtoupper($_POST['username']);
        $name     = $_POST['name'];
        if (transuser($uid, 'ifson') == 1) {
            echo 2;
            exit;
        }
        $msql->query("select 1 from `$tb_user` where username='$username'");
        $msql->next_record();
        if ($msql->f(0) == 1) {
            echo 2;
            exit;
        } else {
            $userid = setupid($tb_user, 'userid');
            $msql->query("select * from `$tb_user` where userid='$uid'");
            $msql->next_record();
            $fid          = $msql->f('fid');
            $themaxmoney  = getmaxmoney($fid);
            $themaxren    = getmaxren($fid);
            if(transuser($fid,'fudong')==1 & $fid!=99999999){
            
            $thekmaxmoney  = transuser($fid,'kmoney');
            
            }else{
               $thekmaxmoney = getkmaxmoney($fid);
            }
            if ($msql->f('maxmoney') > $themaxmoney) {
                $maxmoney = $themaxmoney;
            } else {
                $maxmoney = $msql->f('maxmoney');
            }
            if ($msql->f('kmaxmoney') > $thekmaxmoney) {
                $kmaxmoney = $thekmaxmoney;
            } else {
                $kmaxmoney = $msql->f('kmaxmoney');
            }
            if ($msql->f('maxren') > $themaxren) {
                $maxren = $themaxren;
            } else {
                $maxren = $msql->f('maxren');
            }
            if (date("His") < str_replace(':', '', $config['editstart'])) {
                $ftime = date("Y-m-d ".$config['editend'], time() - 86400);
            } else {
                $ftime = date("Y-m-d ".$config['editend']);
            }
            $time = time();
            $sql  = "insert into `$tb_user` select NULL,'$userid','$username',userpass,'$name','','','','','','','','','','0','0','0','','0',NOW(),status,ifagent,ifson,layer,if(ifagent=0,0,$maxren),0,ifexe,pself,pan,defaultpan,0,'$maxmoney','$maxmoney','0','0',fudong,'$ftime',fastje,plc,plwarn,fid,fid1,fid2,fid3,fid4,fid5,fid6,fid7,fid8,gid,wid,0,0,0,0,0,0,0,'','',''";
            $sql .= " from `$tb_user` where userid='$uid'";
            $fsql->query($sql);
            $msql->query("insert into `$tb_gamecs` select NULL,$userid,gid,ifok,flytype,flyzc,zc,upzc,zcmin,xsort  from `$tb_gamecs` where userid='$uid'");
            $msql->query("insert into `$tb_gamezc` select NULL,$userid,typeid,typename,flytype,flyzc,zc,upzc,zcmin  from `$tb_gamezc` where userid='$uid'");
            $fsql->query("insert into `$tb_points` select NULL,gid,$userid,class,ab,a,b,c,d,cmaxje,maxje,minje from `$tb_points` where userid='$uid'");
            $fsql->query("insert into `$tb_zpan` select NULL,gid,$userid,class,lowpeilv,peilvcha from `$tb_zpan` where userid='$uid'");
            $msql->query("insert into `$tb_warn` select NULL,gid,$userid,class,je,ks from `$tb_warn` where userid='$uid'");
            $msql->query("insert into `$tb_fastje` select NULL,$userid,je,xsort from `$tb_fastje` where userid='$uid'");
            $msql->query("select fudong from `$tb_user` where userid='$userid'");
            $msql->next_record();

            if ($msql->f('fudong') == 1) {
                userchange("新增(复制),现金额度0,", $userid);
                usermoneylog($userid,0,0,'复制帐号'.transuser($uid,'username'));
            } else {
                userchange("新增(复制),低频彩额度$maxmoney,快开彩额度0,", $userid);
                usermoneylog($userid,0,0,'复制帐号'.transuser($uid,'username'));
                usermoneylog($userid,$maxmoney,$maxmoney,'复制帐号'.transuser($uid,'username'),0);
            }

            if ($msql->f('layer') == 1) {
                $msql->query("insert into `$tb_play_user` select NULL,gid,$userid,bid,sid,cid,pid,0,0,mp1,mp2,pl,mpl,xsort,0,0,0,0,0,0 from `$tb_play`");
            }
            echo 1;
        }
        break;
    case "editpoints":
        $uid = $_POST['uid'];
        if (!checkfid($uid)) {
            exit;
        }
        $msql->query("select * from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $fid = $msql->f('fid');
        $tpl->assign("username", strtolower($msql->f('username')));
        $tpl->assign("name", $msql->f('name'));
        $tpl->assign("layername", translayer($msql->f('layer')));
        $tpl->assign("ifagent", $msql->f('ifagent'));
        $pan    = json_decode($msql->f('pan'), true);
        $gamecs = getgamecs($uid);
        $gamecs = getgamename($gamecs);
        $garr=[];
        foreach ($gamecs as $v) {
            $garr[] = $v['gid'];
        }
        $garr = implode(',', $garr);
        $cps   = count($pan);
        for ($k = 0; $k < $cps; $k++) {
            if ($k > 0)
                $str .= ',';
            $str .= strtolower($pan[$k]);
        }
        $tpl->assign("span", $pan);
        $msql->query("select * from `$tb_game` where gid in($garr) order by xsort ");
        $i    = 0;
        $game = array();
        while ($msql->next_record()) {
            $game[$i]['gid']       = $msql->f('gid');
            $game[$i]['fenlei']    = $msql->f('fenlei');
            $game[$i]['flname']    = $msql->f('flname');
            $game[$i]['gname']     = $msql->f('gname');
            $game[$i]['panstatus'] = $msql->f('panstatus');
            $game[$i]['fast']      = $msql->f('fast');
            $game[$i]['pan']       = json_decode($msql->f('pan'), true);
            $game[$i]['ftype']     = json_decode($msql->f('ftype'), true);
            
            $cp = count($game[$i]['pan']);
            for ($j = 0; $j < $cp; $j++) {
                $tgid   = $game[$i]['gid'];
                $tclass = $game[$i]['pan'][$j]['class'];
                $cs     = getjes8($tclass, $uid, $tgid);
                $cs1    = getjes8($tclass, $fid, $tgid);
                
                $game[$i]['pan'][$j]['cmaxje']['v']  = $cs['cmaxje'];
                $game[$i]['pan'][$j]['cmaxje']['vm'] = $cs1['cmaxje'];
                $game[$i]['pan'][$j]['maxje']['v']   = $cs['maxje'];
                $game[$i]['pan'][$j]['maxje']['vm']  = $cs1['maxje'];
                $game[$i]['pan'][$j]['minje']['v']   = $cs['minje'];
                $game[$i]['pan'][$j]['minje']['vm']  = $cs1['minje'];
                $cc                                  = 0;
                foreach ($game[$i]['ftype'] as $key => $val) {
                    if ($val['bc'] == $tclass) {
                        $cs                                                = getzcs8($val['class'], $uid, $tgid);
                        $game[$i]['pan'][$j]['cs'][$key]['peilvcha']['v']  = $cs['peilvcha'];
                        $game[$i]['pan'][$j]['cs'][$key]['peilvcha']['vm'] = transatt8($tclass, 'maxatt', $gid);
                        $game[$i]['pan'][$j]['cs'][$key]['lowpeilv']['v']  = $cs['lowpeilv'];
                        $game[$i]['pan'][$j]['cs'][$key]['bc']             = $val['bc'];
                        $game[$i]['pan'][$j]['cs'][$key]['name']           = $val['name'];
                        $game[$i]['pan'][$j]['cs'][$key]['class']          = $val['class'];
                        $game[$i]['pan'][$j]['cs'][$key]['id']             = $tgid . $val['class'];
                        $cc++;
                    }
                }
                $game[$i]['pan'][$j]['son'] = $cc;
                $game[$i]['pan'][$j]['id']  = $tgid . $tclass;
                $att                        = transatt8($tclass, 'points', $gid, 1);
                if ($game[$i]['pan'][$j]['abcd'] == 1) {
                    if ($game[$i]['pan'][$j]['ab'] == 1) {
                        
                        $fsql->query("select $str from `$tb_points` where gid='$tgid' and class='$tclass' and userid='$uid'  and  ab='A' ");
                        $fsql->next_record();
                        $tsql->query("select $str from `$tb_points` where gid='$tgid' and class='$tclass' and userid='$fid'  and  ab='A' ");
                        $tsql->next_record();
                        for ($k = 0; $k < $cps; $k++) {
                            $tmp                                               = strtolower($pan[$k]);
                            $game[$i]['pan'][$j]['points' . $tmp . 'a']['v']   = pr2($fsql->f($tmp));
                            $game[$i]['pan'][$j]['points' . $tmp . 'a']['vm']  = pr2($tsql->f($tmp));
                            $game[$i]['pan'][$j]['points' . $tmp . 'a']['att'] = $att;
                        }
                        $fsql->query("select $str from `$tb_points` where gid='$tgid' and class='$tclass' and userid='$uid'  and  ab='B' ");
                        $fsql->next_record();
                        $tsql->query("select $str from `$tb_points` where gid='$tgid' and class='$tclass' and userid='$fid'  and  ab='B' ");
                        $tsql->next_record();
                        for ($k = 0; $k < $cps; $k++) {
                            $tmp                                               = strtolower($pan[$k]);
                            $game[$i]['pan'][$j]['points' . $tmp . 'b']['v']   = pr2($fsql->f($tmp));
                            $game[$i]['pan'][$j]['points' . $tmp . 'b']['vm']  = pr2($tsql->f($tmp));
                            $game[$i]['pan'][$j]['points' . $tmp . 'b']['att'] = $att;
                        }
                    } else {
                        $fsql->query("select $str from `$tb_points` where gid='$tgid' and class='$tclass' and userid='$uid'  and  ab='0' ");
                        $fsql->next_record();
                        $tsql->query("select $str from `$tb_points` where gid='$tgid' and class='$tclass' and userid='$fid'  and  ab='0' ");
                        $tsql->next_record();
                        for ($k = 0; $k < $cps; $k++) {
                            $tmp                                               = strtolower($pan[$k]);
                            $game[$i]['pan'][$j]['points' . $tmp . '0']['v']   = pr2($fsql->f($tmp));
                            $game[$i]['pan'][$j]['points' . $tmp . '0']['vm']  = pr2($tsql->f($tmp));
                            $game[$i]['pan'][$j]['points' . $tmp . '0']['att'] = $att;
                        }
                    }
                } else {
                    $fsql->query("select a from `$tb_points` where gid='$tgid' and class='$tclass' and userid='$uid'  and  ab='0' ");
                    $fsql->next_record();
                    $tsql->query("select a from `$tb_points` where gid='$tgid' and class='$tclass' and userid='$fid'  and  ab='0' ");
                    $tsql->next_record();
                    $tmp                                               = 'a';
                    $game[$i]['pan'][$j]['points' . $tmp . '0']['v']   = pr2($fsql->f($tmp));
                    $game[$i]['pan'][$j]['points' . $tmp . '0']['vm']  = pr2($tsql->f($tmp));
                    $game[$i]['pan'][$j]['points' . $tmp . '0']['att'] = $att;
                }
            }
            $i++;
        }
        
        $minpoints = 3;
        $liushui   = array();
        for ($i = 0; $i <= $minpoints; $i += 0.1) {
            $liushui[] = $i;
        }
        $tpl->assign('liushui', $liushui);
        
        $tpl->assign("uid", $uid);
        $plc = transuser($fid, 'plc');
        $tpl->assign("plc", $plc);
        $tpl->assign("fid", $fid);
        $tpl->assign("game", $game);
        $tpl->assign("gamecs", $gamecs);
        $tpl->assign("fl", $fl);
        $tpl->assign("config", $config);
        $tpl->assign("editstart", $config['editstart']);
        $tpl->assign("editend", $config['editend']);
        //$html = $tpl->fetch("suserpoints.html");
        $html = $tpl->fetch("suserpointsnew.html");
        $arr  = array(
            'html' => $html,
            'g' => $game,
            'pan' => $pan,
            'plc' => $plc
        );
        unset($gamecs);
        unset($game);
        echo json_encode($arr);
        break;
    case "setpoints":
        $uid = $_POST['uid'];
        if (!checkfid($uid)) {
            exit;
        }
        $msql->query("select * from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $fid     = $msql->f('fid');
        $layer   = $msql->f('layer');
        $ifagent = $msql->f('ifagent');
        $ustr    = 'fid' . $layer;
        $ustr2   = 'uid' . $layer;
        
        $pan    = json_decode($msql->f('pan'), true);
        $gamecs = getgamecs($uid);
        $garr=[];
        foreach ($gamecs as $v) {
            $garr[] = $v['gid'];
        }
        $garr = implode(',', $garr);
        
        $time       = time();
        $fastpoints = 1;
        $slowpoints = 1;
        $his        = date("His");
        $start      = str_replace(':', '', $config['editstart']);
        $end        = str_replace(':', '', $config['editend']);
        if ($his < $start | $his > $end) {
            $fastpoints = 0;
            $slowpoints = 0;
            $stime      = strtotime(date("Y-m-d " . $config['editend']));
            if (date("His") < $start) {
                $stime = strtotime(date("Y-m-d " . $config['editend'], time() - 86400));
            }
            //echo "select 1 from `$tb_lib` where userid='$uid' and time>'" . sqltime($stime) . "'";
            //var_dump($ifagent);
            if ($ifagent == 1) {
                $msql->query("select 1 from `$tb_lib` where $ustr2='$uid' and time>'" . sqltime($stime) . "' LIMIT 1");
            } else {
                $msql->query("select 1 from `$tb_lib` where userid='$uid' and time>'" . sqltime($stime) . "' LIMIT 1");
            }
            $msql->next_record();
            if ($msql->f(0) != 1) {
                $fastpoints = 1;
            }
            if ($ifagent == 1) {
                $msql->query("select 1 from `$tb_lib` where gid in (100,200,300) and $ustr2='$uid' and time>'" . sqltime($stime) . "' LIMIT 1");
            } else {
                $msql->query("select 1 from `$tb_lib` where gid in (100,200,300) and userid='$uid' and time>'" . sqltime($stime) . "' LIMIT 1");
            }
            $msql->next_record();
            if ($msql->f(0) != 1) {
                $slowpoints = 1;
            }
        }

        $msql->query("select * from `$tb_game` where gid in($garr) order by xsort");
        $i    = 0;
        $game = array();
        //echo 'f',$fastpoints;
        while ($msql->next_record()) {
            if ($msql->f('fast') == 1 & $fastpoints == 0)
                continue;
            if ($msql->f('fast') == 0 & $slowpoints == 0)
                continue;
            if($slowpoints == 0){
                continue;
            }
            $game[$i]['pan']   = json_decode($msql->f('pan'), true);
            $game[$i]['ftype'] = json_decode($msql->f('ftype'), true);
            $cp                = count($game[$i]['pan']);
            $tgid              = $msql->f('gid');
            foreach ($game[$i]['ftype'] as $key => $val) {
                $lowpeilv = r0p($_POST['lowpeilv' . $tgid . $val['class']]);
                $lowpeilv = 1;
                $peilvcha = r0p($_POST['peilvcha' . $tgid . $val['class']]);
                $sql      = "update `$tb_zpan` set lowpeilv='$lowpeilv',peilvcha='$peilvcha' where userid='$uid' and class='" . $val['class'] . "' and gid='$tgid'";
                $fsql->query($sql);
            }
            for ($j = 0; $j < $cp; $j++) {
                $tgid   = $msql->f('gid');
                $tclass = $game[$i]['pan'][$j]['class'];
                $cmaxje = r0p($_POST['cmaxje' . $tgid . $tclass]);
                $maxje  = r0p($_POST['maxje' . $tgid . $tclass]);

                $sqls = " where X.userid=Y.userid and Y.$ustr='$uid' and X.class='$tclass' and X.gid='$tgid'";
                if ($game[$i]['pan'][$j]['abcd'] == 1) {
                    if ($game[$i]['pan'][$j]['ab'] == 1) {
                        $a = r0p($_POST['pointsaa' . $tgid . $tclass]);
                        $b = r0p($_POST['pointsba' . $tgid . $tclass]);
                        $c = r0p($_POST['pointsca' . $tgid . $tclass]);
                        $d = r0p($_POST['pointsda' . $tgid . $tclass]);
                        $fsql->query("select a,b,c,d,cmaxje,maxje from `$tb_points` where userid='$fid'  and gid='$tgid' and class='$tclass' and ab='A'");
                        $fsql->next_record();
                        $a      = bjs($a, $fsql->f('a'));
                        $b      = bjs($b, $fsql->f('b'));
                        $c      = bjs($c, $fsql->f('c'));
                        $d      = bjs($d, $fsql->f('d'));
                        $cmaxje = bjs($cmaxje, $fsql->f('cmaxje'));
                        $maxje  = bjs($maxje, $fsql->f('maxje'));
                        
                        $sql = "update `$tb_points` set a='$a',b='$b',c='$c',d='$d',cmaxje='$cmaxje',maxje='$maxje' where userid='$uid' and gid='$tgid' and class='$tclass' and ab='A'";
                        $fsql->query($sql);
                        if ($ifagent == 1) {
                            $sql1 = "update `$tb_points` X,`$tb_user` Y set X.a=if($a>X.a,X.a,$a),X.b=if($b>X.b,X.b,$b),X.c=if($c>X.c,X.c,$c),X.d=if($d>X.d,X.d,$d),X.cmaxje=if(X.cmaxje>$cmaxje,$cmaxje,X.cmaxje),X.maxje=if(X.maxje>$maxje,$maxje,X.maxje)";
                            $fsql->query($sql1 . $sqls . "  and X.ab='A'");
                        }
                        $a = r0p($_POST['pointsab' . $tgid . $tclass]);
                        $b = r0p($_POST['pointsbb' . $tgid . $tclass]);
                        $c = r0p($_POST['pointscb' . $tgid . $tclass]);
                        $d = r0p($_POST['pointsdb' . $tgid . $tclass]);
                        
                        $fsql->query("select a,b,c,d from `$tb_points` where userid='$fid'  and gid='$tgid' and class='$tclass' and ab='B'");
                        $fsql->next_record();
                        $a = bjs($a, $fsql->f('a'));
                        $b = bjs($b, $fsql->f('b'));
                        $c = bjs($c, $fsql->f('c'));
                        $d = bjs($d, $fsql->f('d'));
                        
                        $sql = "update `$tb_points` set a='$a',b='$b',c='$c',d='$d',cmaxje='$cmaxje',maxje='$maxje' where userid='$uid' and gid='$tgid' and class='$tclass' and ab='B'";
                        $fsql->query($sql);
                        
                        if ($ifagent == 1) {
                            $sql1 = "update `$tb_points` X,`$tb_user` Y set X.a=if($a>X.a,X.a,$a),X.b=if($b>X.b,X.b,$b),X.c=if($c>X.c,X.c,$c),X.d=if($d>X.d,X.d,$d),X.cmaxje=if(X.cmaxje>$cmaxje,$cmaxje,X.cmaxje),X.maxje=if(X.maxje>$maxje,$maxje,X.maxje)";
                            $fsql->query($sql1 . $sqls . "  and X.ab='B'");
                        }
                        
                    } else {
                        $a = r0p($_POST['pointsa0' . $tgid . $tclass]);
                        $b = r0p($_POST['pointsb0' . $tgid . $tclass]);
                        $c = r0p($_POST['pointsc0' . $tgid . $tclass]);
                        $d = r0p($_POST['pointsd0' . $tgid . $tclass]);
                        $fsql->query("select a,b,c,d,cmaxje,maxje from `$tb_points` where userid='$fid'  and gid='$tgid' and class='$tclass' and ab='0'");
                        $fsql->next_record();
                        $a      = bjs($a, $fsql->f('a'));
                        $b      = bjs($b, $fsql->f('b'));
                        $c      = bjs($c, $fsql->f('c'));
                        $d      = bjs($d, $fsql->f('d'));
                        $cmaxje = bjs($cmaxje, $fsql->f('cmaxje'));
                        $maxje  = bjs($maxje, $fsql->f('maxje'));
                        
                        $sql = "update `$tb_points` set a='$a',b='$b',c='$c',d='$d',cmaxje='$cmaxje',maxje='$maxje' where userid='$uid' and gid='$tgid' and class='$tclass' and ab='0'";
                        $fsql->query($sql);
                        if ($ifagent == 1) {
                            $sql1 = "update `$tb_points` X,`$tb_user` Y set X.a=if($a>X.a,X.a,$a),X.b=if($b>X.b,X.b,$b),X.c=if($c>X.c,X.c,$c),X.d=if($d>X.d,X.d,$d),X.cmaxje=if(X.cmaxje>$cmaxje,$cmaxje,X.cmaxje),X.maxje=if(X.maxje>$maxje,$maxje,X.maxje)";
                            $fsql->query($sql1 . $sqls . "  and X.ab='0'");
                        }
                    }
                } else {
                    $a = r0p($_POST['pointsa0' . $tgid . $tclass]);
                    $fsql->query("select a,cmaxje,maxje from `$tb_points` where userid='$fid'  and gid='$tgid' and class='$tclass' and ab='0'");
                    $fsql->next_record();
                    $a      = bjs($a, $fsql->f('a'));
                    $cmaxje = bjs($cmaxje, $fsql->f('cmaxje'));
                    $maxje  = bjs($maxje, $fsql->f('maxje'));
                    $sql    = "update `$tb_points` set a='$a',cmaxje='$cmaxje',maxje='$maxje' where userid='$uid' and gid='$tgid' and class='$tclass' and ab='0'";
                    $fsql->query($sql);
                    
                    if ($ifagent == 1) {
                        $sql1 = "update `$tb_points` X,`$tb_user` Y set X.a=if($a>X.a,X.a,$a),X.cmaxje=if(X.cmaxje>$cmaxje,$cmaxje,X.cmaxje),X.maxje=if(X.maxje>$maxje,$maxje,X.maxje) ";
                        $fsql->query($sql1 . $sqls . "  and X.ab='0'");
                    }
                }
            }
            $i++;
        }
        userchange("修改退水", $uid);
        echo 1;
        break;
    
    case "resetpoints":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $msql->query("select * from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $fid        = $msql->f('fid');
        $ifagent    = $msql->f('ifagent');
        $ustr       = 'uid' . $msql->f('layer');
        $time       = time();
        $fastpoints = 1;
        $his        = date("His");
        $start      = str_replace(':', '', $config['editstart']);
        $end        = str_replace(':', '', $config['editend']);
        if ($his < $start | $his > $end) {
            $fastpoints = 0;
            $stime      = strtotime(date("Y-m-d " . $config['editend']));
            if (date("his") < $end) {
                $stime = strtotime(date("Y-m-d " . $config['editend'], time() - 86400));
            }
            if ($ifagent == 1) {
                $msql->query("select 1 from `$tb_lib` where $ustr='$uid' and time>'" . sqltime($stime) . "'");
            } else {
                $msql->query("select 1 from `$tb_lib` where userid='$uid' and time>'" . sqltime($stime) . "'");
            }
            $msql->next_record();
            if ($msql->f(0) != 1) {
                $fastpoints = 1;
            }
        }
        if ($fastpoints == 0) {
            $whi = " and gid in (select gid from `$tb_game` where fast=0 and panstatus=0 and otherstatus=0) ";
        } else {
            $whi = " and gid in (select gid from `$tb_game` where (fast=0 and panstatus=0 and otherstatus=0) or fast=1) ";
        }
        $msql->query("select fid from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $fid = $msql->f('fid');
        $msql->query("delete from `$tb_zpan` where userid='$uid' $whi");
        $msql->query("delete from `$tb_points` where userid='$uid' $whi ");
        $msql->query("insert into `$tb_zpan` select NULL,gid,$uid,class,lowpeilv,0 from   `$tb_zpan`  where userid='$fid' $whi ");
        $msql->query("insert into `$tb_points` select NULL,gid,$uid,class,ab,a,b,c,d,cmaxje,maxje,minje    `$tb_points`  where userid='$fid' $whi ");
        userchange("恢复退水", $uid);
        echo 1;
        break;
    case "readmoney":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $etype = $_POST['etype'];
        $msql->query("select maxmoney,money,kmaxmoney,kmoney,fid,ifagent from `$tb_user` where userid='$uid'");
        $msql->next_record();
        if ($etype == 'slow') {
            if ($msql->f('ifagent') == 0) {
                $arr = array(
                    $msql->f('maxmoney'),
                    $msql->f('money'),
                    getmaxmoney($msql->f('fid'))
                );
            } else {
                $arr = array(
                    $msql->f('maxmoney'),
                    getmaxmoney($uid),
                    getmaxmoney($msql->f('fid'))
                );
            }
        } else if ($etype == 'fast') {
            if (transuser($msql->f('fid'), 'fudong') == 1) {
                $arr = array(
                    $msql->f('kmaxmoney'),
                    $msql->f('kmoney'),
                    transuser($msql->f('fid'), 'kmoney')
                );
            } else {
                if ($msql->f('ifagent') == 0 | $msql->f('fudong') == 1) {
                    $arr = array(
                        $msql->f('kmaxmoney'),
                        $msql->f('kmoney'),
                        getkmaxmoney($msql->f('fid'))
                    );
                } else {
                    $arr = array(
                        $msql->f('kmaxmoney'),
                        getkmaxmoney($uid),
                        getkmaxmoney($msql->f('fid'))
                    );
                }
            }
        }
        foreach ($arr as $k => $v) {
            $arr[$k] = number_format($v);
        }
        echo implode('|', $arr);
        break;
    case "tiquallmoney":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $pass = md5($_POST['pass']);
        $msql->query("select moneypass from `$tb_user` where userid='$userid'");
        $msql->next_record();
        if($pass!=$msql->f('moneypass')){
            //echo 101;
            //exit;
        }

        $etype = $_POST['etype'];
        $msql->query("select ifagent,layer,fudong from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $ifagent = $msql->f('ifagent');
        $layer   = $msql->f('layer');
        $fudong  = $msql->f('fudong');
        if ($ifagent == 1) {
            if ($etype == 'slow') {
                $msql->query("select count(id) from `$tb_user` where  fid" . $layer . "='$uid' and maxmoney=money");
                $msql->next_record();
                $fsql->query("select count(id) from `$tb_user` where  fid" . $layer . "='$uid'");
                $fsql->next_record();
                if ($msql->f(0) != $fsql->f(0)) {
                    echo 3;
                    exit;
                }
                $msql->query("update `$tb_user` set maxmoney=0,money=0 where  fid" . $layer . "='$uid' and maxmoney=money");
                $msql->query("update `$tb_user` set maxmoney=0,money=0 where userid='$uid'  and maxmoney=money");
                $msql->query("select userid from `$tb_user` where  fid" . $layer . "='$uid' and sy=0");
                while ($msql->next_record()) {
                    userchange("提取全部低频额度!", $msql->f('userid'));
                }
                userchange("提取全部低频额度!", $uid);
            } else if ($etype == 'fast') {
                $msql->query("select count(id) from `$tb_user` where  fid" . $layer . "='$uid' and kmaxmoney=kmoney");
                $msql->next_record();
                $fsql->query("select count(id) from `$tb_user` where  fid" . $layer . "='$uid'");
                $fsql->next_record();
                if ($msql->f(0) != $fsql->f(0)) {
                    echo 3;
                    exit;
                }
                $msql->query("update `$tb_user` set kmaxmoney=0,kmoney=0 where  fid" . $layer . "='$uid' and kmaxmoney=kmoney");
                $msql->query("update `$tb_user` set kmaxmoney=0,kmoney=0 where userid='$uid'  and kmaxmoney=kmoney");
                $msql->query("select userid from `$tb_user` where  fid" . $layer . "='$uid' ");
                while ($msql->next_record()) {
                    userchange("提取全部快开彩额度!", $msql->f('userid'));
                }
                userchange("提取全部快开彩额度!", $uid);
            }
        } else {
            if ($etype == 'slow') {
                $msql->query("select maxmoney,money from `$tb_user` where userid='$uid'");
                $msql->next_record();
                if ($msql->f(0) == $msql->f(1)) {
                    $msql->query("update `$tb_user` set maxmoney=0,money=0 where userid='$uid'");
                    userchange("提取全部低频彩额度!", $uid);
                } else {
                    echo 3;
                    exit;
                }
            } else if ($etype == 'fast') {
                $msql->query("select kmaxmoney,kmoney from `$tb_user` where userid='$uid'");
                $msql->next_record();
                if ($msql->f(0) == $msql->f(1)) {
                    if ($fudong == 1) {
                        $msql->query("update `$tb_user` set kmaxmoney=0,kmoney=0 where userid='$uid'");
                        userchange("提取全部现金额度!", $uid);
                    } else {
                        $msql->query("update `$tb_user` set kmaxmoney=0,kmoney=0 where userid='$uid'");
                        userchange("提取全部快开彩额度!", $uid);
                    }
                } else {
                    echo 3;
                    exit;
                }
            }
        }
        echo 1;
        break;
    case "cmoneypass":
        if($_SESSION['atype']!=1) exit;
        $v1 = md5($_POST['v1']);
        $msql->query("update `$tb_user` set moneypass='$v1' where userid='$userid'");
        echo 1;
    break;   
    case "czmoneypass":
        if($_SESSION['atype']!=1) exit;
        $pass = md5($_POST['pass']);
        $uid = intval($_POST['uid']);
        $msql->query("update `$tb_user` set moneypass='$pass' where userid='$uid'");
        echo 1;
    break;

    case "czpass":
       $uid = intval($_POST['uid']);
       if (!checkfid($uid))
            exit;
       $msql->query("update `$tb_user` set errortimes=0 where userid='$uid'");
       echo 1;
    break;  
 
    case "cmoneypasse":
        $tpl->display("cmoneypass.html");
    break;

    case "cmoneypasssend":
        if($_SESSION['atype']!=1) exit;
        $pass1 = md5($_POST['pass1']);
        $pass0 = md5($_POST['pass0']);
        $msql->query("select moneypass from `$tb_user` where userid='$userid'");
        $msql->next_record();
        if($pass0!=$msql->f("moneypass")){
            echo 2;
            exit;
        }
        $sql = "update `$tb_user` set moneypass='$pass1' where userid='$userid'";
        if ($msql->query($sql)) {
            userchange("更改转账密码",$userid);
            echo 1;
        }
    break;

    case "setmoney":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $pass = md5($_POST['pass']);
        $msql->query("select moneypass from `$tb_user` where userid='$userid'");
        $msql->next_record();
        if($pass!=$msql->f('moneypass')){
            //echo 101;
            //exit;
        }
        $etype = $_POST['etype'];
        $je    = $_POST['je'];
        $types = $_POST['types'];
        if (!is_numeric($je) | $je % 1 != 0 | $je < 1 | $je == '')
            exit;
        if ($types != 0)
            $je = 0 - $je;
        $msql->query("select ifagent,layer,maxmoney,money,kmaxmoney,kmoney,fudong,fid,username from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $ifagent = $msql->f('ifagent');
        $layer   = $msql->f('layer');
        $fid     = $msql->f('fid');
        $fsql->query("select ifagent,layer,maxmoney,money,kmaxmoney,kmoney,fudong,fid,username from `$tb_user` where userid='$fid'");
        $fsql->next_record();
        if ($fsql->f('fudong') == 1) {
            if ($etype == 'fast') {
                $kmoney    = $msql->f('kmoney') + $je;
                $kmaxmoney = $msql->f('kmaxmoney') + $je;
                if ($je < 0) {
                    if ($msql->f('kmoney') < abs($je)) {
                        echo 30;
                        exit;
                    }
                    if ($kmaxmoney >= 0) {
                        $sql = "update `$tb_user` set kmaxmoney=kmaxmoney+$je,kmoney=kmoney+$je where userid='$uid'";
                    } else {
                        $kmaxmoney = abs($kmaxmoney);
                        $sql       = "update `$tb_user` set kmaxmoney=0,kmoney=kmoney+$je,jzkmoney=jzkmoney+$kmaxmoney where userid='$uid'";
                    }
                    
                    $tsql->query($sql);
                    
                    $sql = "update `$tb_user` set kmaxmoney=kmaxmoney-$je,kmoney=kmoney-$je where userid='$fid'";
                    $tsql->query($sql);
                    
                    usermoneylog($uid, $je, $msql->f('kmoney') + $je, '手动提现');
                    usermoneylog($fid, 0 - $je, $fsql->f('kmoney') - $je, '提取' . $msql->f('username') . '额度');
                    userchange("提现".$je.",余额".($msql->f('kmoney') + $je),$uid);
                    userchange("下线提现".$je,$fid);
                    echo 31;
                    exit;
                } else {
                    if ($fsql->f('kmoney') < abs($je)) {
                        echo 40;
                        exit;
                    }
                    $sql = "update `$tb_user` set kmaxmoney=kmaxmoney+$je,kmoney=kmoney+$je where userid='$uid'";
                    $tsql->query($sql);
                    $sql = "update `$tb_user` set kmaxmoney=kmaxmoney-$je,kmoney=kmoney-$je where userid='$fid'";
                    $tsql->query($sql);
                    usermoneylog($uid, $je, $msql->f('kmoney') + $je, '手动充值');
                    usermoneylog($fid, 0 - $je, $fsql->f('kmoney') - $je, '给下级' . $msql->f('username') . '充值');
                    userchange("充值".$je.",余额".($msql->f('kmoney') + $je),$uid);
                    userchange("下线充值".$je,$fid);
                    echo 41;
                    exit;
                }
            }
        } else {
            if ($ifagent == 1) {
                if ($etype == 'slow') {
                    if ($je < 0) {
                        $smoney = getmaxmoney($uid);
                        if ($smoney < abs($je)) {
                            echo 50;
                            exit;
                        }
                        $sql = "update `$tb_user` set maxmoney=maxmoney+$je,money=money+$je where userid='$uid'";
                        $tsql->query($sql);
                        usermoneylog($uid, $je, $msql->f('maxmoney') + $je, '提取低频彩额度', 0);
                        userchange("提取低频额度".$je.",余额".($msql->f('maxmoney') + $je),$uid);
                        echo 51;
                        exit;
                    } else {
                        $fmoney = getmaxmoney($fid);
                        if ($fmoney < abs($je)) {
                            echo 60;
                            exit;
                        }
                        $sql = "update `$tb_user` set maxmoney=maxmoney+$je,money=money+$je where userid='$uid'";
                        $tsql->query($sql);
                        usermoneylog($uid, $je, $msql->f('maxmoney') + $je, '存入低频彩额度', 0);
                        userchange("存入低频额度".$je.",余额".($msql->f('maxmoney') + $je),$uid);
                        echo 61;
                        exit;
                    }
                } else if ($etype == 'fast') {
                    $kmoney    = $msql->f('kmoney') + $je;
                    $kmaxmoney = $msql->f('kmaxmoney') + $je;
                    if ($je < 0) {
                        if ($msql->f('fudong') == 1) {
                            $smoney = $msql->f('kmoney');
                            if ($smoney < abs($je)) {
                                echo 70;
                                exit;
                            }
                            if ($kmaxmoney >= 0) {
                                $sql = "update `$tb_user` set kmaxmoney=kmaxmoney+$je,kmoney=kmoney+$je where userid='$uid'";
                            } else {
                                $kmaxmoney = abs($kmaxmoney);
                                $sql       = "update `$tb_user` set kmaxmoney=0,kmoney=kmoney+$je,jzkmoney=jzkmoney+$kmaxmoney where userid='$uid'";
                            }
                            $tsql->query($sql);
                            usermoneylog($uid, $je, $msql->f('kmoney') + $je, '提取现金额度');
                            userchange("提取现金额度".$je.",余额".($msql->f('kmoney') + $je),$uid);
                            echo 71;
                            exit;
                        } else {
                            $smoney = getkmaxmoney($uid);
                            if ($smoney < abs($je)) {
                                echo 70;
                                exit;
                            }
                            $sql = "update `$tb_user` set kmaxmoney=kmaxmoney+$je,kmoney=kmoney+$je where userid='$uid'";
                            $tsql->query($sql);
                            usermoneylog($uid, $je, $msql->f('kmoney') + $je, '提取快开彩额度');
                            userchange("提取快开彩额度".$je.",余额".($msql->f('kmoney') + $je),$uid);
                            echo 71;
                            exit;
                        }
                    } else {
                        
                        $fmoney = getkmaxmoney($fid);
                        if ($fmoney < abs($je)) {
                            echo 80;
                            exit;
                        }
                        $sql = "update `$tb_user` set kmaxmoney=kmaxmoney+$je,kmoney=kmoney+$je where userid='$uid'";
                        $tsql->query($sql);
                        if ($msql->f('fudong') == 1) {
                            usermoneylog($uid, $je, $msql->f('kmoney') + $je, '存入现金额度');
                            userchange("存入现金额度".$je.",余额".($msql->f('kmoney') + $je),$uid);
                        } else {
                            usermoneylog($uid, $je, $msql->f('kmoney') + $je, '存入快开彩额度');
                            userchange("存入快开彩额度".$je.",余额".($msql->f('kmoney') + $je),$uid);
                        }
                        echo 81;
                        exit;
                    }
                    
                }
            } else {
                if ($etype == 'slow') {
                    if ($je < 0) {
                        if ($msql->f('money') < abs($je)) {
                            echo 10;
                            exit;
                        }
                        $sql = "update `$tb_user` set maxmoney=maxmoney+$je,money=money+$je where userid='$uid'";
                        $tsql->query($sql);
                        usermoneylog($uid, $je, $msql->f('maxmoney') + $je, '提取低频彩额度', 0);
                        userchange("提取低频彩额度".$je.",余额".($msql->f('maxmoney') + $je),$uid);
                        echo 11;
                        exit;
                    } else {
                        $fmoney = getmaxmoney($fid);
                        if ($fmoney < $je) {
                            echo 20;
                            exit;
                        }
                        $sql = "update `$tb_user` set maxmoney=maxmoney+$je,money=money+$je where userid='$uid'";
                        $tsql->query($sql);
                        usermoneylog($uid, $je, $msql->f('maxmoney') + $je, '存入低频彩额度', 0);
                        userchange("存入低频彩额度".$je.",余额".($msql->f('maxmoney') + $je),$uid);
                        echo 21;
                        exit;
                    }
                } else if ($etype == 'fast') {
                    $kmoney    = $msql->f('kmoney') + $je;
                    $kmaxmoney = $msql->f('kmaxmoney') + $je;
                    if ($je < 0) {
                        if ($msql->f('kmoney') < abs($je)) {
                            echo 30;
                            exit;
                        }
                        
                        if ($kmaxmoney >= 0) {
                            $sql = "update `$tb_user` set kmaxmoney=kmaxmoney+$je,kmoney=kmoney+$je where userid='$uid'";
                        } else {
                            $kmaxmoney = abs($kmaxmoney);
                            $sql       = "update `$tb_user` set kmaxmoney=0,kmoney=kmoney+$je,jzkmoney=jzkmoney+$kmaxmoney where userid='$uid'";
                        }                        
                        $tsql->query($sql);
                        if ($msql->f('fudong') == 1) {
                            usermoneylog($uid, $je, $msql->f('kmoney') + $je, '提取现金额度');
                            userchange("提取现金额度".$je.",余额".($msql->f('kmoney') + $je),$uid);
                        } else {
                            usermoneylog($uid, $je, $msql->f('kmoney') + $je, '提取快开彩额度');
                            userchange("提取快开彩额度".$je.",余额".($msql->f('kmoney') + $je),$uid);
                        }
                        echo 31;
                        exit;
                    } else {
                        $fmoney = getkmaxmoney($fid);
                        if ($fmoney < $je) {
                            echo 40;
                            exit;
                        }
                        $sql = "update `$tb_user` set kmaxmoney=kmaxmoney+$je,kmoney=kmoney+$je where userid='$uid'";
                        $tsql->query($sql);
                        if ($msql->f('fudong') == 1) {
                            usermoneylog($uid, $je, $msql->f('kmoney') + $je, '存入现金额度');
                            userchange("存入现金额度".$je.",余额".($msql->f('kmoney') + $je),$uid);
                        } else {
                            usermoneylog($uid, $je, $msql->f('kmoney') + $je, '存入快开彩额度');
                            userchange("存入快开彩额度".$je.",余额".($msql->f('kmoney') + $je),$uid);
                        }
                        echo 41;
                        exit;
                    }
                }
            }
        }        break;
    case "cpass":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $time  = time();
        $pass1 = md5(md5($_POST['pass1']) . $config['upass']);
        $msql->query("update `$tb_user` set userpass='$pass1',passtime=NOW() where userid='$uid'");
        userchange("更改密码", $uid);
        echo 1;
        break;
    case "createson":
        $uid = $_POST['uid'];
        echo json_encode(topuser($uid));
        break;
    case "editson":
        $uid = $_REQUEST['uid'];
        if (strlen($uid) != 8 | !is_numeric($uid))
            $uid = $userid;
        if (!checkfid($uid) & $uid!=$userid)
            exit;
        $data_arr = array();
        $msql->query("select *,lastloginip as ip from `$tb_user` where userid='$uid'");
        $msql->next_record();
        $data_arr[0]['userid']        = $msql->f("userid");
        $data_arr[0]['username']      = $msql->f("username");
        $data_arr[0]['ifson']         = $msql->f("ifson");
        $data_arr[0]['logintimes']    = $msql->f("logintimes");
        $data_arr[0]['regtime']       = substr($msql->f("regtime"), 5);
        $data_arr[0]['lastlogintime'] = substr($msql->f("lastlogintime"), 5);
		$data_arr[0]['lastloginip']   = $msql->f("ip");
        $data_arr[0]['lastloginfrom'] = transip($msql->f("lastloginip"));
        $data_arr[0]['passtime']      = substr($msql->f("passtime"), 5);;
        $layer = $msql->f('layer');
        $tpl->assign("username", $msql->f("username"));
        $tpl->assign("uid", $msql->f("userid"));
        $page = array();
        if ($layer > 1)
            $whi = " and xpage!='pset' ";
        $whi .= " and xpage!='record' ";
        $fsql->query("select * from `$tb_user_page` where userid=2001 $whi order by xsort");
        $page[0][0] = '权限设置';
        $page[1][0] = $msql->f("username");
        $j          = 1;
        while ($fsql->next_record()) {
            $page[0][$j] = $fsql->f('pagename');
            $page[1][$j] = "<input type='checkbox' checked='checked' disabled />";
            $j++;
        }
        $sql = "SELECT *,lastloginip as ip FROM `$tb_user` where fid='$uid' and ifson=1 order by username";
        $msql->query($sql);
        $i = 1;
        while ($msql->next_record()) {
            $data_arr[$i]['online']        = $msql->f("online");
            $data_arr[$i]['userid']        = $msql->f("userid");
            $data_arr[$i]['username']      = $msql->f("username");
            $data_arr[$i]['ifson']         = $msql->f("ifson");
            $data_arr[$i]['logintimes']    = $msql->f("logintimes");
            $data_arr[$i]['regtime']       = substr($msql->f("regtime"), 5);
            $data_arr[$i]['lastlogintime'] = substr($msql->f("lastlogintime"), 5);
			if ($config['iprecord']==1){ 
            $data_arr[$i]['lastloginip']   = $msql->f("ip");
            $data_arr[$i]['lastloginfrom'] = '';
			} else {
			$data_arr[$i]['lastloginip']   = '***';
            $data_arr[$i]['lastloginfrom'] = '***';
			}
            $data_arr[$i]['passtime']      = substr($msql->f("passtime"), 5);
            ;
            $page[$i + 1][0] = $msql->f('username');
            $fsql->query("select * from `$tb_user_page` where userid='" . $msql->f('userid') . "' $whi order by xsort");
            $j = 2;
            while ($fsql->next_record()) {
                $page[$i + 1][$j] = "<input type='checkbox' value='" . $fsql->f('ifok') . "' page='" . $fsql->f('xpage') . "' uid='" . $msql->f('userid') . "'  ifson='" . $msql->f('ifson') . "'  />";
                $j++;
            }
            $i++;
        }
        $tpl->assign('page', $page);
        $tpl->assign('data', $data_arr);
        $tpl->display("seditson.html");
        break;
    case "editsondel":
        $uid = $_POST['uid'];
        if (!checkfid($uid))
            exit;
        $msql->query("delete from `$tb_user` where userid='$uid'");
        $msql->query("delete from `$tb_user_page` where userid='$uid'");
        $msql->query("delete from `$tb_online` where userid='$uid'");
        userchange("删除帐号", $uid);
        echo 1;
        break;
    case "editsonuppage":
        $uid  = $_POST['uid'];
        $page = $_POST['page'];
        if (!checkfid($uid))
            exit;
        $time = time();
        $ip   = getip();
        $msql->query("update `$tb_user_page` set ifok=if(ifok=0,1,0) where userid='$uid' and xpage='$page'");
        userchange("更新权限", $uid);
        $msql->query("select ifok from `$tb_user_page` where userid='$uid' and xpage='$page'");
        $msql->next_record();
        echo $msql->f('ifok');
        break;
    case "editsonaddoredit":
        $uid      = $_POST['uid'];
        $action   = $_POST['action'];
        $username = strtoupper($_POST['username']);
        $time     = time();
        $ip       = getip();
        $pass1    = md5($_POST['pass1'] . $config['upass']);
        $pass2    = md5($_POST['pass2'] . $config['upass']);
        if (!mb_ereg("^[\w\-\.]{1,32}$", $username) | $pass1 != $pass2) {
            echo 0;
            exit;
        }
        $sql = "";
        if ($action == 'add') {
            $usernamef = transuser($uid, 'username');
            $msql->query("select id from `$tb_user` where username='$username'");
            $msql->next_record();
            if ($msql->f('id') == '') {
                $userid2 = setupid($tb_user, 'userid') + rand(1, 9);
                $time    = time();
                $layer   = transuser($uid, 'layer');
                $wid     = transuser($uid, 'wid');
                $gid     = transuser($uid, 'gid');
                $sql     = "insert into `$tb_user` set username='$username',userpass='$pass1',wid='$wid',gid='$gid',userid='$userid2',fid='$uid',status='1',passtime=0,layer='$layer',ifson='1',ifagent='1',regtime=NOW()";
            } else {
                echo 2;
                exit;
            }
        } else if ($action == 'edit') {
            $sql .= " update `$tb_user` set passtime=0,userpass='$pass1',errortimes=0 where username='$username' and fid='$uid'";
        }
        if ($sql != '') {
            $msql->query($sql);
            if ($action == 'add') {
                $msql->query("select * from `$tb_user_page` where userid='2001' order by xsort ");
                while ($msql->next_record()) {
                    $fsql->query("insert into `$tb_user_page` set xpage='" . $msql->f('xpage') . "',pagename='" . $msql->f('pagename') . "',userid='$userid2',ifok='0',xsort='" . $msql->f('xsort') . "'");
                }
                userchange("新增", $userid2);
            } else {
                $msql->query("select userid from `$tb_user` where username='$username' and fid='$uid'");
                $msql->next_record();
                userchange("修改密码", $msql->f('userid'));
            }
            echo 1;
        }
        break;
    case "showrecord":
        $uid      = $_POST['uid'];
        $username = $_POST['username'];
        $ifok     = array(
            "失败",
            "成功"
        );
        $e        = array();
        $msql->query("select moditime,modiuser,modisonuser,modiip as modiip,addr,action from `$tb_user_edit` where userid='$uid' order by moditime desc limit 20");
        $i = 0;
        while ($msql->next_record()) {
            $e[$i]['moditime'] = $msql->f('moditime');
            if (!checkfid($msql->f('modiuser')) & $msql->f('modiuser') != $userid) {
                $e[$i]['modiuser']    = '系统';
                $e[$i]['modisonuser'] = '';
            } else {
                $e[$i]['modiuser'] = transu($msql->f('modiuser'));
                $fsql->query("select username from `$tb_user` where userid='" . $msql->f('modisonuser') . "'");
                $fsql->next_record();
               /* if ($fsql->f('username')=='nul1'){*/
                $e[$i]['modisonuser'] = $fsql->f('username');
			/*	 } else {
			    $e[$i]['modisonuser'] = '系统';
			   }*/
            }
            //$e[$i]['modiip'] = $msql->f('modiip');
			if ($config['iprecord']==1){	
            $e[$i]['modiip']   = substr($msql->f('modiip'),0,2).".*";
            $e[$i]['addr']   = $msql->f('addr');
			}else{
			$e[$i]['modiip']   = "***";
            $e[$i]['addr']   = "***";
			}
            $e[$i]['action'] = $msql->f('action');
            $i++;
        }
        
        $arr = array(
            'e' => $e
        );
        echo json_encode($arr);
        unset($e);
        break;
    case "showlogininfo":
        $uid      = $_POST['uid'];
        $username = $_POST['username'];
        $ifok     = array(
            "失败",
            "成功"
        );
        $l        = array();
        $msql->query("select ip as ip,addr,time,ifok from `$tb_user_login` where username='$username' and xtype!=0 order by time desc limit 20");
        $i = 0;
        while ($msql->next_record()) {
            if ($config['iprecord']==1){
			$l[$i]['ip']   = substr($msql->f('ip'),0,2).".*";
            $l[$i]['addr'] = $msql->f('addr');
			}else{
			$l[$i]['ip']   = "***";
            $l[$i]['addr'] = "***";
				}
            $l[$i]['time'] = $msql->f('time');
            $l[$i]['ifok'] = $ifok[$msql->f('ifok')];
            $i++;
        }
        $arr = array(
            'l' => $l
        );
        echo json_encode($arr);
        unset($l);
        break;
    case "zhuxiaologin":
        $uid = trim($_POST['uid']);
        if (!checkid($uid))
            exit;
        $msql->query("update `$tb_user` set online=0 where userid='$uid'");
        $msql->query("delete from `$tb_online` where userid='$uid'");
        echo 1;
        break;
    case "moneylog":
        $uid = $_REQUEST['uid'];
        if (strlen($uid) != 8 | !is_numeric($uid))
            $uid = $userid;
        if (!checkfid($uid) & $uid!=$userid)
            exit;
        include("../global/Iplocation_Class.php");  
        $page = r1p($_REQUEST['page']);
        $msql->query("select count(id) from `$tb_money_log` where userid='$uid' ");
        $msql->next_record();
        $rcount = pr0($msql->f(0));
        $psize = $config['psize3'];
        $pcount = $rcount%$psize==0 ? $rcount/$psize : floor($rcount/$psize)+1;
        $tpl->assign('page',$page);
        $tpl->assign('rcount',$rcount);
        $tpl->assign('pcount',$pcount);
        $tpl->assign('fudong',transuser($uid,'fudong'));
        $msql->query("select * from `$tb_money_log` where userid='$uid' order by time desc limit ".($page-1)*$psize.",".$psize);
        $log=array();
        $i=0;
        while($msql->next_record()){
            
            $log[$i]['time'] = $msql->f('time');
            if (!checkfid($msql->f('modiuser')) & $msql->f('modiuser') != $userid) {
                $log[$i]['modiuser']    = '系统';
                $log[$i]['modisonuser'] = '';
            } else {
                $log[$i]['modiuser'] = transu($msql->f('modiuser'));
                $fsql->query("select username from `$tb_user` where userid='" . $msql->f('modisonuser') . "'");
                $fsql->next_record();
                $log[$i]['modisonuser'] = $fsql->f('username');
            }
            $log[$i]['ip'] = $msql->f('ip');
            $log[$i]['addr']        = mb_convert_encoding($ips->getaddress($msql->f('ip')),'utf-8','GBK');
            $log[$i]['bz'] = $msql->f('bz');
            $log[$i]['type'] = $msql->f('type');
            $log[$i]['money'] = number_format($msql->f('money'));
            $log[$i]['usermoney'] = number_format($msql->f('usermoney'));
            $i++;
        }
        $tpl->assign('log',$log);
        $tpl->assign('uid',$uid);
        $tpl->assign('username',transu($uid));
        $tpl->display("money_log.html");
    break;  
}
function exegroup($u, $sql1, $sql2)
{
    global $tsql;
    $cu = count($u);
    for ($i = 0; $i < $cu; $i++) {
        if ($u[$i] != '') {
            $sql = $sql1 . $u[$i] . $sql2;
            $tsql->query($sql);
        }
    }
}
?>