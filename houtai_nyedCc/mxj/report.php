<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');

switch($_REQUEST['xtype']){
	case "show":
	       $msql->query("select * from `$tb_message` where userid='$userid' order by time desc");
		   $m=array();
		   $i=0;
		   while($msql->next_record()){
		       $m[$i]['content'] = $msql->f('content');
			   $m[$i]['response'] = $msql->f('response');
			   $m[$i]['id'] = $i+1;
			   $m[$i]['time'] = date("m月d日 H:i",$msql->f('time'));
			   $i++;
		   }
		   $tpl->assign("m",$m);
	    $tpl->display("report.html");
	break;
	case "sendreport":
      $content =strip_tags($_POST['content']);
	  $msql->query("insert into `$tb_message` set content='$content',userid='$userid',time=NOW()");
	  echo 1;	
	break;
}

?>