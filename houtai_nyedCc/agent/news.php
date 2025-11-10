<?php
include('../data/comm.inc.php');
include('../data/agentvar.php');
include('../func/func.php');
include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');

 $msql->query("select * from `$tb_news`  where  wid in ('".$_SESSION['wid']."',0) and agent in (1,2) and ifok=1 order by time desc");
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
		$username = transuser($userid2,'username');
		$tpl->assign("username",$username);
		$time =sqltime(time()-30);
		$msql->query("select time,ip as ip,addr from `$tb_user_login` where xtype=1 and time<='$time' and ifok=1  and  username='$username' order by time desc limit 1");
		$msql->next_record();
		
		$tpl->assign("time",$msql->f('time'));
		$tpl->assign("ip",$msql->f('ip'));
		$tpl->assign("addr",$msql->f('addr'));
        $tpl->assign('news', $news);
        $tpl->display("news.html");
