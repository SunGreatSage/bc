<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');

include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');


        $msql->query("select status,maxmoney,money,kmaxmoney,kmoney,pan,defaultpan,username,name,fastje,fudong,sy,layer,jzkmoney,garr from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $tpl->assign('username', strtolower($msql->f('username')));

            
        $tpl->assign('kmaxmoney', p1($msql->f('kmaxmoney')));
        $tpl->assign('kmoney', p1($msql->f('kmoney')));
        if ($msql->f('kmaxmoney') == 0) {
            $tpl->assign('kmoneyuse', p1($msql->f('sy') - $msql->f('jzkmoney') - $msql->f('kmoney')));
        } else {
            $tpl->assign('kmoneyuse', p1($msql->f('kmaxmoney') + $msql->f('sy') - $msql->f('jzkmoney') - $msql->f('kmoney')));
        }
        $tpl->assign('maxmoney', p1($msql->f('maxmoney')));
        $tpl->assign('money', p1($msql->f('money')));
        $tpl->assign('moneyuse', p1($msql->f('maxmoney') - $msql->f('money')));
           $tpl->assign("layer",$msql->f("layer"));

           $tpl->assign("status",$msql->f("status"));
       

        $gamecs = getgamecs($userid);
        $gamecs = getgamename($gamecs);
        $garr = json_decode($msql->f('garr'),true);
        if(is_numeric($_SESSION['gid']) && $_SESSION['gid']!=''){
            $gid = $_SESSION['gid'];
        }else if(is_array($garr)){
            $gid= $garr[0]['gid'];
        }else{
            $gid = $gamecs[0]['gid'];
        }
        $_SESSION['gid'] = $gid;
        $vgame=0;
        if(is_array($garr)){
            $tmp=[];
            foreach ($gamecs as $key => $value) {
                $gamecs[$key]['px'] = $garr["g".$value['gid']];
                $tmp[]= $garr["g".$value['gid']];
                $gamecs[$key]['ifok'] = $garr["ok".$value['gid']];
                if($garr["ok".$value['gid']]==1){
                    $vgame++;
                }
            }
            array_multisort($tmp,SORT_ASC,SORT_NUMERIC,$gamecs);
        }
        if($vgame==0) $vgame=8;
        $tpl->assign('vgame', $vgame);
        foreach($gamecs as $k => $v){
            if(file_exists("../css/mobi/img/".$v['gid'].".svg")){
                $gamecs[$k]['isimg'] = 1;
                $gamecs[$k]['img'] = "../css/mobi/img/".$v['gid'].".svg";
            }else if(file_exists("../css/mobi/img/".$v['gid'].".png")){
                $gamecs[$k]['isimg'] = 1;
                $gamecs[$k]['img'] = "../css/mobi/img/".$v['gid'].".png";
            }else if(file_exists("../css/mobi/img/".$v['gid'].".jpg")){
                $gamecs[$k]['isimg'] = 1;
                $gamecs[$k]['img'] = "../css/mobi/img/".$v['gid'].".jpg";
            }else{
                $gamecs[$k]['isimg'] = 0;
            }
        }
        //print_r($gamecs);
        $tpl->assign('gamecs', $gamecs);
        $tpl->assign('cg', count($gamecs));
        $tpl->assign('gid', $gid);

        $pan  = json_decode($msql->f('pan'), true);
		$tpl->assign('pan', $pan);
		$tpl->assign('cpan', count($pan));
		if($_SESSION['abcd']=='A' | $_SESSION['abcd']=='B'  | $_SESSION['abcd']=='C'  | $_SESSION['abcd']=='D' ){
		   $tpl->assign('defaultpan', $_SESSION['abcd']);
		}else{
		   $tpl->assign('defaultpan', $msql->f('defaultpan'));
		}  
        $tpl->assign('webname', $config['webname']);
        //$tpl->assign('title', $config['webname'] . '-' . $msql->f('username') . '[' . $msql->f('name') . ']-' . $config['gname']);
        $tpl->assign('title', "");
        $tpl->assign('gname', $config['gname']);
        $tpl->assign('class', $config['class']);
        $tpl->assign('kjurl', $config['kjurl']);
		$tpl->assign('moneytype', $config['moneytype']);
		$msql->query("select kfurl from `$tb_config`");
		$msql->next_record();
		$tpl->assign('kfurl', $msql->f('kfurl'));
		
	   $tpl->assign("maxlayer",$config["maxlayer"]);
        $msql->query("select * from `$tb_news`  where  wid in ('" . $_SESSION['wid'] . "',0) and agent in (0,2) and alert=1 and ifok=1 order by time desc");
        $i = 0;
       $news=[];
        while ($msql->next_record()) {
            $news[$i]['id'] = $i+1;
            if ($msql->f('cs') == 1) {
                $arr[0] = $config['thisqishu'];
                $arr[1] = $config['webname'];
                $fsql->query("select opentime,closetime,kjtime from `$tb_kj` where gid='$gid' and qishu='" . $config['thisqishu'] . "'");
                $fsql->next_record();
                $arr[2]              = date("Y-m-d H:i:s", strtotime($fsql->f('opentime')) + $config['times']['o']);
                $arr[3]              = date("Y-m-d H:i:s", strtotime($fsql->f('closetime')) - $config['times']['c'] - $config['userclosetime']);
                $arr[4]              = $fsql->f('kjtime');
                $news[$i]['content'] = messreplace($msql->f('content'), $arr);
            } else {
                $news[$i]['content'] = $msql->f('content');
                
            }
            $news[$i]['content'] = htmlspecialchars_decode($news[$i]['content']);
            $news[$i]['time']    =  $msql->f('time');
            $i++;
        }
        $tpl->assign('news', $news);
        $tpl->assign('cnews', count($news));
        $tpl->assign("yy",date("Y"));
       
        $tpl->display('home.html');
       