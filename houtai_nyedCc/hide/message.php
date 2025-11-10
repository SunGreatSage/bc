<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
	    $psize = $config['psize'];
        $msql->query("select count(id) from `$tb_message`");
        $msql->next_record();
         $rcount = $msql->f(0);       
        $thispage = r1($_REQUEST['PB_page']);
        $page     = new page(array(
            'total' => $rcount,
            'perpage' => $psize,
			'nowindex' => $thispage
        ));
        $msql->query("select * from `$tb_message` order by time desc limit " . (($thispage - 1) * $psize) . ",$psize");
        $m = array();
        $i = 0;
        while ($msql->next_record()) {
            $m[$i]['content']  = $msql->f('content');
            $m[$i]['response'] = $msql->f('response');
            $m[$i]['id']       = $msql->f('id');
            $m[$i]['time']     = $msql->f('time');
            $m[$i]['user']     = transu($msql->f('userid'));
            $i++;
        }
        $tpl->assign("m", $m);
        $tpl->assign('page', $page->show());
        $tpl->display("message.html");
        break;
    case "mdel":
        $id  = $_POST['id'];
        $sql = "delete from `$tb_message` where instr('$id',concat('|',id,'|'))";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
    case "response":
        $id      = $_POST['id'];
        $message = $_POST['message'];
        $sql     = "update `$tb_message` set response='$message' where id='$id'";
        if ($msql->query($sql)) {
            echo 1;
        }
        break;
   case "nss":
	  $uid = $_REQUEST['uid'];
	  $news = $_REQUEST['news'];
	  $time = time();
	  $sql     = "INSERT into `$tb_message` set content='$news',userid='$uid',time=NOW()";
        if ($msql->query($sql)) {
            echo 1;
        }
   break;	
}
?>