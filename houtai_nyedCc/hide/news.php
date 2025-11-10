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
	$rs = $msql->arr("select wid,webname from `$tb_web` order by wid",1);
	
        $msql->query("select * from `$tb_news` order by time desc");
        $i = 0;
		
		
        while ($msql->next_record()) {
            $news[$i]['id']      = $msql->f('id');
			$news[$i]['ifok']      = $msql->f('ifok');
			$news[$i]['cs']      = $msql->f('cs');
			$news[$i]['wid']      = $msql->f('wid');
			$news[$i]['agent']      = $msql->f('agent');
			$news[$i]['gundong']      = $msql->f('gundong');
			$news[$i]['alert']      = $msql->f('alert');
            $news[$i]['content'] = $msql->f('content');
            $news[$i]['time']    = $msql->f('time');
            $i++;
        }
		
		$tpl->assign('web', $rs);
        $tpl->assign('news', $news);
        $tpl->display("news.html");
        break;
    case "addnews":
        $content = htmlspecialchars ($_POST['content']);
        $time    = time();
		$wid= $_POST['wid'];
        $sql     = "insert into `$tb_news` set content='$content',time=NOW(),wid='$wid'";
        if ($msql->query($sql)) {
            echo openurl("news.php?xtype=show");
        }
        break;
    case "newsdel":
        $id = $_POST['id'];
        if ($msql->query("delete from `$tb_news` where instr('$id',concat('|',id,'|'))")) {
            echo 1;
        }
        break;
		case "newsedit":
		    $id = trim($_POST['id']);
			$ifok= $_POST['ifok'];
			$cs= $_POST['cs'];
			$wid= $_POST['wid'];
            $agent= $_POST['agent'];   
			$gundong= $_POST['gundong'];
			$alert= $_POST['alert'];  
            $time= $_POST['time'];      
			$content = htmlspecialchars ($_POST['con']);
		$sql = "update `$tb_news` set ifok='$ifok',agent='$agent',gundong='$gundong',alert='$alert',cs='$cs',wid='$wid',content='$content',time='$time' where id='$id'";

		if ($msql->query($sql)) {
		  echo 1;
		}
		break;
}
?>