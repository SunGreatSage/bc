<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        $qishu    = array();
        $qishu[0] = $thisyear . $thisqishu;
        $msql->query("select year,qishu from `$tb_kj` order by year desc,qishu desc");
        $i = 1;
        while ($msql->next_record()) {
            $qishu[$i] = $msql->f('year') . $msql->f('qishu');
            $i++;
        }
        $tpl->assign('qishu', $qishu);
        $layer = transuser($userid, 'layer');
        $tmp   = 'level' . ($layer + 1);
        $tpl->assign('usertype', $$tmp);
        $tpl->assign('layer', $layer);
        $tpl->assign('uid', $userid);
        $tpl->assign("b", getbclass());
        $tpl->assign("topuser", topuser($userid));
        $tpl->assign('username', $_SESSION['ausername']);
        $tpl->assign('thisdate', date("Y-m-d"));
        $tpl->display("now.html");
        break;
    case "getnow":
        $psize = $_POST['psize'];
        if (!is_numeric($psize))
            $psize = 100;
        $qishu = $_POST['qishu'];
        $page  = $_POST['page'];
        if (!is_numeric($page))
            $page = 1;
        $yq      = " and gid='$gid' and qishu='$qishu' ";
        $puserid = $_POST['puserid'];
        $layer   = transuser($userid, 'layer');
        if ($puserid != '') {
            $uidstr = 'uid' . transuser($puserid, 'layer');
            $yq .= " and $uidstr='" . $puserid . "'";
        }
        $sql = " select userid,uid" . ($layer + 1) . " from `$tb_lib` where 1=1 $yq  group by userid ";
        $msql->query($sql);
        $lib = array();
        $i   = 0;
        while ($msql->next_record()) {
            $lib[$i]['topuser'] = transu($msql->f('uid' . ($layer + 1)));
            $lib[$i]['user']    = transu($msql->f('userid'));
            $lib[$i]['uid']     = $msql->f('userid');
            $fsql->query("select count(id),sum(je) from `$tb_lib` where 1=1 $yq and userid='" . $msql->f('userid') . "' ");
            $fsql->next_record();
            $lib[$i]['zs'] = $fsql->f(0);
            $lib[$i]['je'] = pr0($fsql->f(1));
            $fsql->query("select time from `$tb_lib` where 1=1 $yq and userid='" . $msql->f('userid') . "' order by time desc");
            $fsql->next_record();
            $lib[$i]['time']  = date("m-d H:i:s", $fsql->f('time'));
            $lib[$i]['times'] = $fsql->f('time');
            $i++;
        }
        foreach ($lib as $libs) {
            $mm[] = $libs['times'];
        }
        unset($libs);
        array_multisort($mm, SORT_DESC, SORT_NUMERIC, $lib);
        $rc   = count($lib);
        $pc   = $rc % $psize == 0 ? $rc / $psize : (($rc - $rc % $psize) / $psize) + 1;
        $lib  = array_slice($lib, ($page - 1) * $psize, $psize);
        $pstr = page($pc, $page);
        $e    = array(
            "tz" => $lib,
            "page" => $pstr,
            'sql' => $sql,
            "layer" => $layer
        );
        echo json_encode($e);
        unset($e);
        unset($lib);
        break;
    case "getnowxx":
        $uid   = $_POST['uid'];
        $psize = $_POST['psizes'];
        $page  = $_POST['pages'];
        $qishu = $_POST['qishu'];
        $msql->query("select count(id) from `$tb_lib` where userid='$uid' and  gid='$gid' and qishu='$qishu'");
        //echo "select count(id) from `$tb_lib` where userid='$uid' and  gid='$gid' and qishu='$qishu'";
        $msql->next_record();
        $rc = $msql->f(0);
        $pc = $rc % $psize == 0 ? $rc / $psize : (($rc - $rc % $psize) / $psize) + 1;
        $msql->query("select content,time from `$tb_lib` where userid='$uid' and  gid='$gid' and qishu='$qishu' order by time desc limit " . (($page - 1) * $psize) . ",$psize");
        $i   = 0;
        $lib = array();
        while ($msql->next_record()) {
            $lib[$i]['time'] = substr($msql->f('time'),5);
            $lib[$i]['con']  = $msql->f('content');
            $i++;
        }
        echo json_encode(array(
            'p' => $pc,
            'con' => $lib
        ));
        break;
    case "download":
        if ($config['panstatus'] == 1) {
            outjs("为保证会员的速度，开盘期间不提供下载");
            exit;
        } 
		$qishu = $_REQUEST['qishu'];
        $msql->query("select ljs,fid1 from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $fid1     = $msql->f('fid1');
        $ljs      = $msql->f('ljs');
        if ($thelayer > 1) {
            $msql->query("select ljs from `$tb_user` where userid='$fid1'");
            $msql->next_record();
            $ljs = $msql->f('ljs');
        }
        header("Content-type: text/html; charset=utf-8");
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: filename=$qishu.xls");
       
        echo "期数", "\t";
        echo "内容", "\t";
        //echo "金额", "\t";
        echo "会员", "\t";
        echo "时间", "\t\n";
		if($ljs==1){
        $sql = " select * from `$tb_lib` where gid='$gid' and qishu='$qishu' where uid1='$fid1' order by time desc ,id desc ";
		}else{
        $sql = " select * from `$tb_lib` where gid='$gid' and qishu='$qishu' order by time desc ,id desc ";
		}
        $msql->query($sql);
        while ($msql->next_record()) {
            echo $msql->f('qishu'), "\t";
            echo $msql->f('content'), "\t";
            //echo $msql->f('je'), "\t";
            echo transuser($msql->f('userid'), 'username'), "\t";
            echo date("m-d H:i:s", $msql->f('time')), "\t";
            echo "\n";
        }
        $msql->query("insert into x_down set gid='$gid',userid='$userid',time=NOW(),downtype='xls',qishu='$qishu'");
        break;
}
?>