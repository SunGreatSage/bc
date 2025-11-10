<?php
include '../data/comm.inc.php';
include '../data/myadminvar.php';
include '../func/func.php';
include "../func/csfunc.php";
include '../func/adminfunc.php';
include '../include.php';
include './checklogin.php';
switch ($_REQUEST['xtype']) {
    case "loglist":
        include('../global/page.class.php');
        $psize = 200;
        $msql->query("select count(id) from `{$tb_log}`");
        $msql->next_record();
        $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
            'nowindex' => $thispage
        ));
        $l = $msql->arr("select * from `{$tb_log}` order by id desc limit " . ($thispage - 1) * $psize . ",{$psize}",1);
        $tmp=[];
        foreach($l as $k => $v){
            if($tmp["g".$v["gid"]]==""){
                $tmp["g".$v["gid"]] = transgame($v["gid"],"gname");
            }
            if($tmp["u".$v["userid"]]==""){
                $tmp["u".$v["userid"]] = transuser($v["userid"],"username");
            }
            $l[$k]["gname"] = $tmp["g".$v["gid"]];
            $l[$k]["user"] = $tmp["u".$v["userid"]];
        }

        $tpl->assign('l', $l);
        $tpl->assign('deldate', date('Y-m-d', time() - 86400 * 14));
        $tpl->assign('page', $page->show());
        $tpl->display("loglist.html");
    break;
    case 'dlist':
        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'date') {
            $time = $id . ' ' . $config['editstart'];
            $msql->query("delete from `{$tb_log}` where time<='$time'");
            echo 1;
        } else {
            $id = str_replace('\\', '', $id);
            $id = json_decode($id, true);
            $id = implode(',', $id);
            $msql->query("delete from `{$tb_log}` where id in ({$id})");
            echo 1;
        }
        break;
 }