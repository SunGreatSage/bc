<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
switch($_REQUEST['xtype']){
    case "nft":
	   $msql->query("select * from `$tb_news`  where  wid in ('".$_SESSION['wid']."',0) and agent in (0,2) and ifok=1 order by time desc");
        $i = 0;
        while ($msql->next_record()) {
            $news[$i]['id']      = $i;
			if($msql->f('cs')==1){
				$arr[0] = $config['thisqishu'];
				$arr[1] = $config['webname'];
				$fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='".$config['thisqishu']."'");
				$fsql->next_record();
			    $arr[2] = date("Y-m-d H:i:s", strtotime($fsql->f('opentime')));
				$arr[3] = date("Y-m-d H:i:s", strtotime($fsql->f('closetime')));
				$arr[4] = $fsql->f('kjtime');
			    $news[$i]['content'] = messreplace($msql->f('content'),$arr);
			}else{
                $news[$i]['content'] =  $msql->f('content');  
                
			}
			$news[$i]['content'] = htmlspecialchars_decode($news[$i]['content']);
            $news[$i]['time']    = $msql->f('time');
            $i++;
        }
		$tpl->assign("time",$msql->f('time'));
        $tpl->assign('news', $news);
        $tpl->display("nft.html");
	break;
}

?>