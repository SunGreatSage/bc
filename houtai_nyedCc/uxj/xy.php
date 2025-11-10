<?php
//file_put_contents("app.txt","99"."\r\n",FILE_APPEND);
include('../data/comm.inc.php');
include('../data/uservar.php');
include('../func/func.php');
 //file_put_contents("app.txt","9"."\r\n",FILE_APPEND);
include('../func/userfunc.php');
include('../include.php');
 //file_put_contents("app.txt","10"."\r\n",FILE_APPEND);
include('./checklogin.php');
  //file_put_contents("app.txt","11"."\r\n",FILE_APPEND);
$mess = $msql->arr("select content,cs from `$tb_news`  where wid in ('".$_SESSION['wid']."',0) and agent in (0,2) and alert=1 and ifok=1 order by time desc",1);
    
foreach($mess as $key => $val){
   if($val['cs']==1){
      	$arr[0] = $config['thisqishu'];
				$arr[1] = $config['webname'];
				$fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='".$config['thisqishu']."'");
				$fsql->next_record();
			    $arr[2] = $fsql->f('opentime');
				$arr[3] = $fsql->f('closetime');
				$arr[4] = $fsql->f('kjtime');
			    $mess[$key]['content'] = messreplace($mess[$key]['content'],$arr);
   }
}
$tpl->assign("status",transuser($userid,'status'));
$tpl->assign("mess",$mess);
$tpl->assign('title',$config['webname']);
$tpl->assign('xy',$xy);
$tpl->display("xy.html");

?>