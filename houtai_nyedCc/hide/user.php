<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
   case "show":
      $tpl->assign("fid", $userid);
      $tpl->assign("flayer", transuser($userid, 'layer'));
      $msql->query("select wid,layer,namehead from `$tb_web` order by wid");
      $i = 0;
      while ($msql->next_record()) {
         $layer[$i]['wid']      = $msql->f('wid');
         $layer[$i]['layer']    = json_decode($msql->f('layer'), true);
         $namehead              = json_decode($msql->f('namehead'), true);
         $layer[$i]['namehead'] = $namehead[0];
         $i++;
      }
	  $tpl->assign("username", transuser($userid, 'username'));
      $tpl->assign("layer", $layer);
	  $tpl->assign("maxlayer", $config['maxlayer']);
	  $tpl->assign("maxrenflag", $config['maxrenflag']);
	  $tpl->assign("yingdeny", $config['yingdeny']);
      $tpl->assign("toplayer", transuser($userid, 'layer'));
	   $tpl->assign('hides', $_SESSION['hides']);
      $tpl->display("user.html");
      break;
   case "getuser":
      $melayer=0;
      $layer    = $_POST['layer'];
      $fid      = $_POST['fid'];
      $status   = $_POST['status'];
	  $online = $_POST['online'];
      $uid      = $userid;
      $username = trim($_POST['username']);
      $flayer   = transuser($fid, 'layer');
	  if(transuser($fid,'userid')=='') exit;
	  
      $sql = "select * from `$tb_user` where ifson=0 ";
      if ($username != '') {
         $whi = "  and  (username like '%$username%' or name like '%$username%' or userid='$username')  ";
      }else{
		        if ($flayer == 0) {
         $whi = " and layer='$layer'  ";
      } else {
         $whi = " and  fid" . $flayer . "='$fid' and layer='$layer'  ";
      }     
	 }
      if ($status != 'all') {
         $whi .= " and status='$status' ";
      }
      if ($online ==1) {
         $whi .= " and online=1 ";
      } 
      $sql .= $whi;
      $sql .= " order by ifagent,fudong desc limit 300";

      $msql->query($sql);
      $user = array();
      $i    = 0;
      while ($msql->next_record()) {
         $layer                 = $msql->f('layer');
         $user[$i]['username']  = $msql->f('username');
         $user[$i]['userid']    = $msql->f('userid');
		 $user[$i]['online']    = $msql->f('online');
		 $user[$i]['regtime']    = $msql->f('regtime');
		 $user[$i]['lastlogintime']    = $msql->f('lastlogintime');
		 $user[$i]['regtimes']    = substr($msql->f('regtime'),0,10);
		 $user[$i]['lastlogintimes']    = substr($msql->f('lastlogintime'),0,10);
         $user[$i]['name']      = $msql->f('name');
         
         $user[$i]["layer"]     = $msql->f('layer');
         $user[$i]["utype"]     = transutype($msql->f('ifagent'));
         $user[$i]["ifagent"]   = $msql->f('ifagent');
         $user[$i]['maxmoney']  =  number_format($msql->f('maxmoney'));
         $user[$i]['money']     =  number_format($msql->f('money'));
         $user[$i]['kmaxmoney'] =  number_format($msql->f('kmaxmoney'));
         $user[$i]['kmoney']    =  number_format($msql->f('kmoney'));
         $user[$i]['fmaxmoney'] =  number_format($msql->f('fmaxmoney'));
         $user[$i]['fmoney']    =  number_format($msql->f('fmoney'));
         $user[$i]['fudong']    = $msql->f('fudong');
         $user[$i]['status']    = $msql->f('status');
		 $user[$i]['fid']    = $msql->f('fid');
		 $user[$i]['fids']    = implode(',',getfids($msql->f('userid'),$melayer));
         $user[$i]['plc']       = $msql->f('plc');
         $fsql->query("select count(id) from `$tb_user` where fid='" . $msql->f('userid') . "' and ifson=0");
         $fsql->next_record();
         $user[$i]['downnum'] = r0($fsql->f(0));
         
         $u                   = getfid($user[$i]['userid']);
         $user[$i]['zc']      = getzcnewall($user[$i]['userid'], $u,$msql->f('layer'));
         $user[$i]['wid']     = $msql->f('wid');
		 $fsql->query("select layer,webname from `$tb_web` where wid='".$msql->f('wid')."'");
		 $fsql->next_record();
		 $user[$i]['web']     = $fsql->f('webname');
		 $layers = json_decode($fsql->f('layer'),true);
		 $user[$i]["layername"] = $layers[$msql->f('layer')-1];
		 $user[$i]['layers']     = $layers;
		 //print_r( $user[$i]['layers']);
         $i++;
      }
	  //echo 234;
      $tpl->assign("user", $user);
      $tpl->assign("melayer", transuser($userid, 'layer'));
      $tpl->assign("flayer", $flayer);
      $tpl->assign('hides', $_SESSION['hides']);
      $tpl->display("suserlist.html");
      break;

  
   case "gettree":
      $fid = $_POST['fid'];
	  if(transuser($fid,'userid')=='') exit;
      $msql->query("select userid,username,ifagent,layer,name,wid,fid from `$tb_user` where fid='$fid' and ifson=0 order by ifagent,fudong desc");
	  $i=0;
      while($msql->next_record()){
		 $user[$i]['fid']      = $msql->f('fid');
	     $user[$i]['uid']      = $msql->f('userid');
		 $user[$i]['ifagent']      = $msql->f('ifagent');
		 $user[$i]['wid']      = $msql->f('wid');
		 $user[$i]['layer']      = $msql->f('layer');
		 $user[$i]['username'] = $msql->f('username').'('.$msql->f('name').')';
		 $user[$i]['total'] = 0;
		 if($msql->f('ifagent')==1){
		     $fsql->query("select count(id) from `$tb_user` where fid='".$msql->f('userid')."' and ifson=0");
			 $fsql->next_record();
			 $user[$i]['total'] = $fsql->f(0);
		 }
		 $i++;
	  }
	  
      //$msql->query("select count(id) from `$tb_user` where fid".$msql->f('layer')."='$userid' and ifson=0");
	  if(count($user)>0){
	  $msql->query("select count(id) from `$tb_user` where ifson=0");
	  $msql->next_record();
	  $user[0]['toptotal'] = $msql->f(0);
	  }
      echo json_encode($user);
      break;
   case "add":
      $fid = $_REQUEST['fid'];
      if (!is_numeric($fid)) {
         $fid = $userid;
      }
      if (transuser($fid, 'ifagent') == '' | transuser($fid, 'ifagent') == 0 | transuser($fid, 'ifson') == 1) {
         exit;
      }
      $yingdenyje = getmaxyingdenyje($fid);
	  $maxren = getmaxren($fid);
      if ($maxren <= 0 & $config['maxrenflag']==1) {
         header("Content-Type:text/html;charset=utf-8");
         echo "该帐户的可用会员数不足!请联系上级！";
         exit;
      }
	  $tpl->assign('fname', transu($fid));
      $layer = transuser($fid, 'layer');
      if ($layer > 0) {
         $wid = transuser($fid, 'wid');
         $fsql->query("select * from `$tb_web` where wid='$wid'");
         $fsql->next_record();
         $config['layer'] = json_decode($fsql->f('layer'), true);
         $namehead        = json_decode($fsql->f('namehead'), true);
         $tpl->assign("maxlayer", $fsql->f('maxlayer'));
      } else {
         $namehead = $config['namehead'];
         $tpl->assign("maxlayer", $config['maxlayer']);
      }
	  $tpl->assign('moneytype', $config['moneytype']);
      $tpl->assign('userhead', $namehead[$layer]);
      $tpl->assign("maxmoney", getmaxmoney($fid));
      $tpl->assign("kmaxmoney", getkmaxmoney($fid));
      $tpl->assign("fmaxmoney", getfmaxmoney($fid));
      $tpl->assign("fdc", transuser($fid, 'fdc'));
      $tpl->assign("plc", transuser($fid, 'plc'));
      $tpl->assign('namelength', $config['namelength']);
      $tpl->assign('status', transuser($fid, 'status'));
      $gamecs = getgamecs($fid);
      $cg     = count($gamecs);
      for ($i = 0; $i < $cg; $i++) {
         $gamecs[$i]['gname'] = transgame($gamecs[$i]['gid'], 'gname');
      }
      $tpl->assign('gamecs', $gamecs);
	$pan = json_decode(transuser($fid, "pan"), true);
        $tpl->assign('pan', $pan);
				//$fsql->query("select min(a) from `$tb_points` where userid='$fid'");
		//$fsql->next_record();
		$minpoints = 3;
		$liushui = array();
		for($i=0;$i<=$minpoints;$i+=0.1){
		   $liushui[] = $i;
		}
		$tpl->assign('liushui', $liushui);
      $layer     = transuser($fid, 'layer') + 1;
      $layername = translayer($layer);
      $tpl->assign('usertype', $layername);
      $tpl->assign('layer', $layer);
      $tpl->assign('layername', $layername);
	  $tpl->assign('layernamefu', translayer($layer-1)); 
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
	  $tpl->assign("yingdeny", $config['yingdeny']);
      $tpl->display("suseradd.html");
      break;
   case "edit":
      $uid = $_POST['uid'];
      if (!checkfid($uid))
         exit;
      $fid = transuser($uid, 'fid');
      $msql->query("select * from `$tb_user` where userid='$uid'");
      $msql->next_record();
		if($msql->f('userid')!=$uid){
		   echo 2;exit;
		}
      $fid   = $msql->f('fid');
      $layer = $msql->f('layer');	  
	  if($layer>1){
         $fsql->query("select layer from `$tb_web` where wid='".$msql->f('wid')."'");
         $fsql->next_record();
         $config['layer'] = json_decode($fsql->f('layer'), true);
	  }
      $tpl->assign('fid', $msql->f('fid'));
	  $tpl->assign('fname', transu($msql->f('fid')));
      $tpl->assign('layer', $msql->f('layer'));
	  $tpl->assign('layername', $config['layer'][$msql->f('layer')-1]);
	  $tpl->assign('layernamefu', translayer($msql->f('layer')-1));
		 
      $tpl->assign('username', $msql->f('username'));
	  $tpl->assign('tel', $msql->f('tel'));
	  $tpl->assign('qq', $msql->f('qq'));
	  $tpl->assign('email', $msql->f('email'));
	  $tpl->assign('bz', $msql->f('bz'));
	  $tpl->assign('bank', $msql->f('bank'));
	  $tpl->assign('bankname', $msql->f('bankname'));
	  $tpl->assign('banknum', $msql->f('banknum'));
	  $tpl->assign('moneypass', $msql->f('moneypass'));
	  
	  $tpl->assign('moneytype', $config['moneytype']);
	  
      $tpl->assign('userid', $msql->f('userid'));
      $tpl->assign('name', $msql->f('name'));
      $tpl->assign('maxren', $msql->f('maxren'));
      $tpl->assign('pan', $msql->f('pan'));
      $tpl->assign('pans', json_decode($msql->f('pan'), true));
      $tpl->assign('defaultpan', $msql->f('defaultpan'));
      $tpl->assign('money', $msql->f('money'));
      $tpl->assign('maxmoney', $msql->f('maxmoney'));
      $tpl->assign('kmoney', $msql->f('kmoney'));
      $tpl->assign('kmaxmoney', $msql->f('kmaxmoney'));
      $tpl->assign('fmaxmoney', $msql->f('fmaxmoney'));
      $tpl->assign('fmoney', $msql->f('fmoney'));
	  $tpl->assign('yingdenyje', $msql->f('yingdenyje'));
      $tpl->assign('fudong', $msql->f('fudong'));
      $tpl->assign('ifexe', $msql->f('ifexe'));
      $tpl->assign('pself', $msql->f('pself'));
      $tpl->assign('fdc', $msql->f('fdc'));
      $tpl->assign('plc', $msql->f('plc'));
      $tpl->assign('fidfdc', transuser($msql->f('fid'), 'fdc'));
      $tpl->assign('fidplc', transuser($msql->f('fid'), 'plc'));
      $tpl->assign("ifagent", $msql->f('ifagent'));
      $tpl->assign("status", $msql->f('status'));
	  $tpl->assign("mgid", $msql->f('gid'));
	  $tpl->assign("cssz", $msql->f('cssz'));
      $tpl->assign("action", 'edit');
      
 
     
      $tpl->assign('fidmaxmoney', getmaxmoney($fid) + $msql->f('maxmoney'));
      $tpl->assign('fidkmaxmoney', getkmaxmoney($fid) + $msql->f('kmaxmoney'));
      $tpl->assign('fidfmaxmoney', getfmaxmoney($fid) + $msql->f('fmaxmoney'));
	  $tpl->assign('fidyingdenyje', getmaxyingdenyje($fid) + $msql->f('yingdenyje'));
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
	  
	 
      $fidgamecs = getgamecs($fid);
      $cg        = count($fidgamecs);	 
      for ($i = 0; $i < $cg; $i++) {
         $fidgamecs[$i]['gname']     = transgame($fidgamecs[$i]['gid'], 'gname');//$fidgamecs[$i]['ifok'].
         $fidgamecs[$i]['panstatus'] = transgame($fidgamecs[$i]['gid'], 'panstatus');
         $fidgamecs[$i]['fast']      = transgame($fidgamecs[$i]['gid'], 'fast');
		 $fsql->query("select * from `$tb_gamecs` where userid='".$uid."' and gid='".$fidgamecs[$i]['gid']."'");
		 $fsql->next_record();
		 $fidgamecs[$i]['uifok']     = $fsql->f('ifok');
		 $fidgamecs[$i]['uflyzc']       = $fsql->f('flyzc');
         $fidgamecs[$i]['uzc']       = $fsql->f('zc');
         $fidgamecs[$i]['uupzc']     = $fsql->f('upzc');
         $fidgamecs[$i]['uflytype']  = $fsql->f('flytype');
         $fidgamecs[$i]['uzchold']   = $fsql->f('zchold');
      }
	  
	  $tpl->assign("editstart",$config['editstart']);
	  $tpl->assign("editend",$config['editend']);
      $tpl->assign('fidgamecs', $fidgamecs);
	  $tpl->assign("maxrenflag", $config['maxrenflag']);
	  $tpl->assign("yingdeny", $config['yingdeny']);
      $tpl->display("suseredit.html");
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
      $fid = $_POST['fid'];
      if ($fid == '' | !is_numeric($fid) | !checkfid($fid))
         $fid = $userid;
      $username = strtoupper($_POST['username']);
      $userpass = md5($_POST['userpass'] . $config['upass']);
      $name     = $_POST['name'];
	  $tel     = $_POST['tel'];
	  $qq     = $_POST['qq'];
	  $email     = $_POST['email'];
	  $bz     = $_POST['bz'];
	  $moneypass     = $_POST['moneypass'];
	  $bank     = $_POST['bank'];
	  $bankname     = $_POST['bankname'];
	  $banknum     = $_POST['banknum'];
	  
      if (!mb_ereg("^[\w\-\.]{1,32}$", $username)) {
         echo 3;
         exit;
      }
	  
      $maxmoney  = $_POST['maxmoney'];
      $kmaxmoney = $_POST['kmaxmoney'];
      $fmaxmoney = $_POST['fmaxmoney'];
	  $yingdenyje = r0($_POST['yingdenyje']);
	  
      if ($maxmoney < 0)
         $maxmoney = 0;
      if ($kmaxmoney < 0)
         $kmaxmoney = 0;
      if ($fmaxmoney < 0)
         $fmaxmoney = 0;
      if ($yingdenyje < 0)
         $yingdenyje = 0;
      $money      = $maxmoney;
      $kmoney     = $kmaxmoney;
     $fmoney     = $fmaxmoney;

      $maxren     = $_POST['maxren'];
      $pan        = $_POST['pan'];
      $defaultpan = $_POST['defaultpan'];
		if(transuser($fid,'layer')==0){
        $wid        = $_POST['wid'];
		}else{
		   $wid        = transuser($fid,'wid');
		}
      $ifexe      = $_POST['ifexe'];
      $pself      = $_POST['pself'];
	  $cssz      = $_POST['cssz'];
	  $cssz      = $_POST['cssz'];
      $ifagent    = $_POST['ifagent'];
      $layer      = $_POST['layer'];
      $status     = $_POST['status'];
      $fdc        = $_POST['fdc'];
      $plc        = $_POST['plc'];
      $fudong     = $_POST['fudong'];
		$liushui = $_POST['liushui'];
		$mgid      = $_POST['mgid'];
		if($liushui<0) $liushui=0;
		
      if (transuser($fid, 'fdc') == 0)
         $fdc = 0;
	   
      if (transuser($fid, 'plc') == 0)
         $plc = 0;
		 if($ifagent==0) {$fdc=0;$plc=0;}	
      if ($ifexe == 0)
         $pself = 0;
      $maxrens    = getmaxren($fid);
      $maxmoneys  = getmaxmoney($fid);
      $kmaxmoneys = getkmaxmoney($fid);
    $fmaxmoneys = getfmaxmoney($fid);
	
	$yingdenyjes = getmaxyingdenyje($fid);
      if ($maxmoney > $maxmoneys)
         $maxmoney = $maxmoneys;
      if ($kmaxmoney > $kmaxmoneys)
         $kmaxmoney = $kmaxmoneys;
      if ($fmaxmoney > $fmaxmoneys)
         $fmaxmoney = $fmaxmoneys;
      if ($maxren > $maxrens)
         $maxren = $maxrens;
      if ($yingdenyje > $yingdenyjes)
         $yingdenyje = $yingdenyjes;
      $money  = $maxmoney;
      $kmoney = $kmaxmoney;
    $fmoney = $fmaxmoney;


      $gamecs = $_POST['gamecs'];
	  $gamecs = str_replace('\\', '', $gamecs);
        $gamecs = json_decode($gamecs, true);
		$garr = array();
		foreach($gamecs as $v){
		   $garr[] = $v['gid'];
		}
		
      $layer  = transuser($fid, "layer") + 1;
      $uid    = setupid("$tb_user", "userid");
	 $time = time();
      $sql    = "insert into `$tb_user` set username='$username',userid='$uid',userpass='$userpass',name='$name',status='$status',ifagent='$ifagent',layer='$layer',maxren='$maxren',ifexe='$ifexe',pself='$pself',plc='$plc',fdc='$fdc',pan='$pan',defaultpan='$defaultpan',maxmoney='$maxmoney',kmaxmoney='$kmaxmoney',fmaxmoney='$fmaxmoney',money='$money',kmoney='$kmoney',fmoney='$fmoney',fudong='$fudong',fid='$fid',wid='$wid',fastje=0,gid='$mgid',cssz='$cssz',regtime=NOW(),yingdenyje='$yingdenyjes'"; 
		
		$sql .= ",tel='$tel',qq='$qq',email='$email',bz='$bz',bank='$bank',bankname='$bankname',banknum='$banknum',moneypass='$moneypass'";
      $thefid = $fid;
      for ($j = ($layer - 1); $j >= 1; $j--) {
         $sql .= ",fid" . $j . "='" . $fid . "'";
         $fid = transuser($fid, "fid");
      }
      $msql->query("select id from `$tb_user` where username='$username'");
      $msql->next_record();
      if ($msql->f('id') != '') {
         echo 2;
      } else {
         if ($msql->query($sql)) {
            userchange("新增", $uid);
            if ($layer == 1) {
               $msql->query("insert into `$tb_play_user` select NULL,gid,$uid,bid,sid,cid,pid,0,0,mp1,mp2,pl,mpl,0,0,xsort,0,0,0,0 from `$tb_play`");
            }
            $msql->query("insert into `$tb_warn` select NULL,gid,$uid,class,je,ks from `$tb_warn` where userid='99999999'");
            $msql->query("insert into `$tb_fastje` select NULL,$uid,je,xsort from `$tb_fastje` where userid='99999999'");
            $msql->query("insert into `$tb_zpan` select NULL,gid,$uid,class,cmaxje,maxje,minje,lowpeilv,0 from `$tb_zpan` where userid='$thefid'");
            $msql->query("insert into `$tb_points` select NULL,gid,$uid,class,ab,if(a-$liushui<0,0,a-$liushui),if(b-$liushui<0,0,b-$liushui),if(c-$liushui<0,0,c-$liushui),if(d-$liushui<0,0,d-$liushui) from `$tb_points` where userid='$thefid'");
            
            
            insertgame($gamecs, $uid,$fid);
            echo 1;
         }
      }
      break;
   case "edituser":
      $uid = $_POST['userid'];
      if ($uid == '' | !is_numeric($uid) | !checkfid($uid))
         exit;
       //$userpass = md5($_POST['userpass'] . $config['upass']);
	  $userpass = $_POST['userpass'];
      $msql->query("select * from `$tb_user` where userid='$uid'");
      $msql->next_record();
      $name      = $_POST['name'];
	  $tel     = $_POST['tel'];
	  $qq     = $_POST['qq'];
	  $email     = $_POST['email'];
	  $bz     = $_POST['bz'];
	  $bank     = $_POST['bank'];
	  $bankname     = $_POST['bankname'];
	  $banknum     = $_POST['banknum'];
	  $moneypass     = $_POST['moneypass'];
	  
      $maxmoney  = $_POST['maxmoney'];
      $kmaxmoney = $_POST['kmaxmoney'];
      $fmaxmoney = $_POST['fmaxmoney'];
	  $yingdenyje = r0($_POST['yingdenyje']);
      if ($maxmoney < 0)
         $maxmoney = 0;
      if ($kmaxmoney < 0)
         $kmaxmoney = 0;
      if ($fmaxmoney < 0)
         $fmaxmoney = 0;
      if ($yingdenyje < 0)
         $yingdenyje = 0;
      $maxren     = $_POST['maxren'];
      $pan        = $_POST['pan'];
      $defaultpan = $_POST['defaultpan'];
		if($msql->f('layer')==1){
        $wid        = $_POST['wid'];
		}else{
		   $wid        = transuser($msql->f('fid'),'wid');
		}
      $ifexe      = $_POST['ifexe'];
      $pself      = $_POST['pself'];
	  $cssz      = $_POST['cssz'];
      $ifagent    = $_POST['ifagent'];
      $layer      = $_POST['layer'];
      $status     = $_POST['status'];
      $plc        = $_POST['plc'];
      $fdc        = $_POST['fdc'];
      $fudong     = $_POST['fudong'];
	  $mgid      = $_POST['mgid'];
      if ($ifexe == 0)
         $pself = 0;
      if (transuser($msql->f('fid'), 'fdc') == 0)
         $fdc = 0;
      if (transuser($msql->f('fid'), 'plc') == 0)
         $plc = 0;
		 if($ifagent==0) {$fdc=0;$plc=0;}
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
        $theyingdenyje = getmaxyingdenyje($msql->f('fid')) + $msql->f('yingdenyje');
        if ($theyingdenyje < $yingdenyje) {
            $yingdenyje = $theyingdenyje;
        }
      $themaxmoney = getmaxmoney($msql->f('fid')) + $msql->f('maxmoney');
      if ($themaxmoney < $maxmoney) {
         $maxmoney = $themaxmoney;
      }
      $thekmaxmoney = getkmaxmoney($msql->f('fid')) + $msql->f('kmaxmoney');
      if ($thekmaxmoney < $kmaxmoney) {
         $kmaxmoney = $thekmaxmoney;
      }
      $thefmaxmoney = getfmaxmoney($msql->f('fid')) + $msql->f('fmaxmoney');
      if ($thefmaxmoney < $fmaxmoney) {
         $fmaxmoney = $thefmaxmoney;
      }
      $fsql->query("select sum(maxmoney) from `$tb_user` where fid='$uid' and ifson=0");
      $fsql->next_record();
      $downmaxmoney = $fsql->f(0);
      if ($maxmoney < $downmaxmoney) {
         $maxmoney = $downmaxmoney;
      }
      if ($maxmoney < $msql->f('maxmoney') & $msql->f('money') != $msql->f('maxmoney')) {
         $maxmoney = $msql->f('maxmoney');
      }
      $moneycha     = $maxmoney - $msql->f('maxmoney');
      $money        = $msql->f('money') + $moneycha;
      $thekmaxmoney = getkmaxmoney($msql->f('fid')) + $msql->f('kmaxmoney');
      if ($thekmaxmoney < $kmaxmoney) {
         $kmaxmoney = $thekmaxmoney;
      }
      $fsql->query("select sum(kmaxmoney) from `$tb_user` where fid='$uid' and ifson=0");
      $fsql->next_record();
      $downkmaxmoney = $fsql->f(0);
      if ($kmaxmoney < $msql->f('kmaxmoney') & $msql->f('kmoney') != $msql->f('kmaxmoney')) {
         $kmaxmoney = $msql->f('kmaxmoney');
      }
      if ($kmaxmoney < $downkmaxmoney) {
         $kmaxmoney = $downkmaxmoney;
      }
      $kmoneycha    = $kmaxmoney - $msql->f('kmaxmoney');
      $kmoney       = $msql->f('kmoney') + $kmoneycha;
      $thefmaxmoney = getfmaxmoney($msql->f('fid')) + $msql->f('fmaxmoney');
      if ($thefmaxmoney < $fmaxmoney) {
         $fmaxmoney = $thefmaxmoney;
      }
      $fsql->query("select sum(fmaxmoney) from `$tb_user` where fid='$uid' and ifson=0");
      $fsql->next_record();
      $downfmaxmoney = $fsql->f(0);
      if ($fmaxmoney < $msql->f('fmaxmoney') & $msql->f('fmoney') != $msql->f('fmaxmoney')) {
         $fmaxmoney = $msql->f('fmaxmoney');
      }
      if ($fmaxmoney < $downfmaxmoney) {
         $fmaxmoney = $downfmaxmoney;
      }
      $fmoneycha = $fmaxmoney - $msql->f('fmaxmoney');
      $fmoney    = $msql->f('fmoney') + $fmoneycha;
      $themaxren = getmaxren($msql->f('fid')) + $msql->f('maxren');
      if ($maxren > $themaxren) {
         $maxren = $themaxren;
      }


      if ($maxmoney != $msql->f('maxmoney')) {
         userchange("修改一般额度:原" . $msql->f('maxmoney') . "新" . $maxmoney, $msql->f('userid'));
      }
      if ($kmaxmoney != $msql->f('kmaxmoney')) {
         userchange("修改快开额度:原" . $msql->f('kmaxmoney') . "新" . $kmaxmoney, $msql->f('userid'));
      }
      if ($fmaxmoney != $msql->f('fmaxmoney')) {
         userchange("修改现金额度:原" . $msql->f('fmaxmoney') . "新" . $fmaxmoney, $msql->f('userid'));
      }
      $fsql->query("select sum(maxren),count(id) from `$tb_user` where fid='$uid'");
      $fsql->next_record();
      if ($maxren < $fsql->f(0) + $fsql->f(1)) {
         $maxren = $fsql->f(0) + $fsql->f(1) + 1;
      }
      $gamecs  = str_replace('\\', '', $gamecs);
      $gamecs2 = json_decode($gamecs, true);
      $gamecs3 = $gamecs2;
      //$gamecs  = getgamecs($uid);
      $cg      = count($gamecs3);
      $his     = date("His");
        $layers     = transuser($uid, 'layer');
        $uidstr     = "fid" . $layers;
      //$ugroup  = getusergroup($uid);
      //$ugroup  = str_replace($uid, '', $ugroup);
      for ($i = 0; $i < $cg; $i++) {
         if ($ifagent == 1) {
			$fsql->query("select zc,ifok,flytype from `$tb_gamecs` where gid='".$gamecs3[$i]['gid']."' and userid='$uid'");
			//echo "select zc,ifok,flytype from `$tb_gamecs` where gid='".$gamecs3[$i]['gid']."' and userid='$uid'";
			$fsql->next_record(); 
            if ($gamecs3[$i]['zc'] != $fsql->f('zc')) {
               $tsql->query("update `$tb_gamecs` A,`$tb_user` B set A.zc=0,A.upzc=0 where A.userid=B.userid and B.$uidstr='$uid' and A.gid='" . $gamecs3[$i]['gid'] . "'");
			   $tsql->query("update `$tb_gamecs` A,`$tb_user` B set A.zc=0,A.upzc=0 where A.userid=B.userid and B.fid='$uid' and A.gid='" . $gamecs3[$i]['gid'] . "'");
            }
            if ($gamecs3[$i]['ifok'] == 0) {
               $tsql->query("update `$tb_gamecs` A,`$tb_user` B set A.zc=0,A.upzc=0,A.flytype=0,A.zchold=0,A.ifok=0 where A.userid=B.userid and B.$uidstr='$uid' and A.gid='" . $gamecs3[$i]['gid'] . "'");
            }
            if ($gamecs3[$i]['flytype'] != $fsql->f('flytype')) {
               $tsql->query("update `$tb_gamecs` A,`$tb_user` B set A.flytype=0 where A.userid=B.userid and B.$uidstr='$uid' and A.gid='" . $gamecs3[$i]['gid'] . "'");
            }
         }
      }
	  //print_r($gamecs3);
      updategame($gamecs3, $uid);
      /*        if ($ifagent == 1 & $status == 0) {
      $msql->query("update `$tb_user` set status=0 where instr('$ugroup',userid)");
      }*/
	  $oldplc = transuser($uid,'plc');
	  $oldfdc = transuser($uid,'fdc');
      if ($userpass == '') {
         $sql = "update `$tb_user` set name='$name',ifagent='$ifagent',maxren='$maxren',ifexe='$ifexe',pself='$pself',plc='$plc',fdc='$fdc',pan='$pan',defaultpan='$defaultpan',maxmoney='$maxmoney',kmaxmoney='$kmaxmoney',money='$money',kmoney='$kmoney',fudong='$fudong',wid='$wid',cssz='$cssz',gid='$mgid',yingdenyje='$yingdenyje'";
		 if($ifagent==1){
		    $sql .= ",fmaxmoney='$fmaxmoney',fmoney='$fmoney'";
		 }
      } else {
		 $userpass = md5($_POST['userpass'] . $config['upass']);
         $sql = "update `$tb_user` set userpass='$userpass',name='$name',ifagent='$ifagent',maxren='$maxren',ifexe='$ifexe',pself='$pself',plc='$plc',fdc='$fdc',pan='$pan',defaultpan='$defaultpan',maxmoney='$maxmoney',kmaxmoney='$kmaxmoney',money='$money',kmoney='$kmoney',fudong='$fudong',wid='$wid',cssz='$cssz',gid='$mgid',yingdenyje='$yingdenyje'";
		 if($ifagent==1){
		    $sql .= ",fmaxmoney='$fmaxmoney',fmoney='$fmoney'";
		 }
      }
	  $sql .= ",tel='$tel',qq='$qq',email='$email',bz='$bz',bank='$bank',bankname='$bankname',banknum='$banknum',moneypass='$moneypass'";
	  $sql .= " where userid='$uid' ";
	  $msql->query($sql);
      userchange("修改资料", $uid);
      $layer = transuser($uid, 'layer');
      if ($layer == 1) {
         $msql->query("update `$tb_user` set wid='$wid' where fid" . $layer . "='$uid' or fid='$uid'");
      }
      if ($plc == 0 & $oldplc==1) {
         $msql->query("update `$tb_user` set plc='0' where fid" . $layer . "='$uid' or fid='$uid'");
      }
	    if ($fdc == 0 & $oldfdc==1) {
         $msql->query("update `$tb_user` set fdc='0' where fid" . $layer . "='$uid' or fid='$uid'");
      }
      echo 1;
      break;
   case "deluser":
      set_time_limit(0);
	  if($_POST['pass']!=$config['supass'] & $_SESSION['hide']!=1){
		  echo 2;
		  exit;
		 }
	  $ustr = $_POST['ustr'];
      $ustr = explode('|', $ustr);
      for ($i = 0; $i < count($ustr); $i++) {
         if ($ustr[$i] == '')
            continue;
         $ugroup = getusergroup($ustr[$i]);
         $msql->query("select id from `$tb_lib` where instr('$ugroup',userid)");
         $msql->next_record();
         if ($msql->f('id') != '') {
            echo 3;
            exit;
         }
      }
      for ($i = 0; $i < count($ustr); $i++) {
         if ($ustr[$i] == '')
            continue;
         $ugroup = getusergroup($ustr[$i]);
         $msql->query("delete from `$tb_user_page` where userid!=2001 and userid in (select userid from `$tb_user` where instr('$ugroup',fid))");
         $msql->query("delete from `$tb_user` where instr('$ugroup',userid) and userid!=99999999");
         $msql->query("delete from `$tb_user` where instr('$ugroup',fid) and userid!=99999999");
         $msql->query("delete from `$tb_points` where instr('$ugroup',userid) and userid!=99999999");
         $msql->query("delete from `$tb_zpan` where instr('$ugroup',userid) and userid!=99999999");
         $msql->query("delete from `$tb_lib` where instr('$ugroup',userid)");
         $msql->query("delete from `$tb_message` where instr('$ugroup',userid)");
         $msql->query("delete from `$tb_online` where instr('$ugroup',userid)");
         $msql->query("delete from `$tb_play_user` where instr('$ugroup',userid) and userid!=99999999");
         $msql->query("delete from `$tb_fly` where instr('$ugroup',userid) and userid!=99999999");
         $msql->query("delete from `$tb_fastje` where instr('$ugroup',userid) and userid!=99999999");
         $msql->query("delete from `$tb_warn` where instr('$ugroup',userid) and userid!=99999999");
         $msql->query("delete from `$tb_auto` where instr('$ugroup',userid) and userid!=99999999");
         $msql->query("delete from `$tb_gamecs` where instr('$ugroup',userid) and userid!=99999999");
      }
      echo 1;
      break;
   case "updatestatus":
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
   case "updatefudong":
      $uid = $_POST['uid'];
      if (!checkfid($uid))
         exit;
      $msql->query("update `$tb_user` set fudong=if(fudong=1,0,1) where userid='$uid'");
      userchange("修改现金开关", $uid);
      $msql->query("select fudong from `$tb_user` where userid='$uid'");
      $msql->next_record();
      echo $msql->f('fudong');
      break;
   case "copyuser":
      $uid = $_POST['uid'];
      if (!checkfid($uid))
         exit;
      $fid = transuser($uid, 'fid');
      if (getmaxren($fid) < 1 & $config['maxrenflag']==1) {
         echo 3;
         exit;
      }
      $username = strtoupper($_POST['username']);
      $name     = $_POST['name'];
      if (transuser($userid, 'ifson') == 1) {
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
         $thekmaxmoney = getkmaxmoney($fid);
         $thefmaxmoney = getfmaxmoney($fid);
         $themaxren    = getmaxren($fid);
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
         if ($msql->f('fmaxmoney') > $thefmaxmoney) {
            $fmaxmoney = $thefmaxmoney;
         } else {
            $fmaxmoney = $msql->f('fmaxmoney');
         }
         if ($msql->f('maxren') > $themaxren) {
            $maxren = $themaxren;
         } else {
            $maxren = $msql->f('maxren');
         }
         $money  = $maxmoney;
         $kmoney = $kmaxmoney;
		 $time = time();
         $sql    = "insert into `$tb_user` select NULL,'$userid','$username',userpass,'$name','','','','','','','','','0','0','0','0','0',status,ifagent,ifson,layer,if(ifagent=0,0,$maxren),0,ifexe,pself,pan,defaultpan,0,'$maxmoney','$maxmoney','$kmaxmoney','$kmaxmoney','$fmaxmoney','$fmaxmoney',fudong,0,fdc,fastje,plc,plwarn,fid,fid1,fid2,fid3,fid4,fid5,fid6,fid7,fid8,gid,wid,0,0,NOW(),0,0";
         $sql .= " from `$tb_user` where userid='$uid'";
         $fsql->query($sql);
         $msql->query("insert into `$tb_gamecs` select NULL,$userid,gid,ifok,flytype,flyzc,zc,upzc,zchold,xsort  from `$tb_gamecs` where userid='$uid'");
         $fsql->query("insert into `$tb_points` select NULL,gid,$userid,class,ab,a,b,c,d from `$tb_points` where userid='$uid'");
         $fsql->query("insert into `$tb_zpan` select NULL,gid,$userid,class,cmaxje,maxje,minje,lowpeilv,peilvcha from `$tb_zpan` where userid='$uid'");
         $msql->query("insert into `$tb_warn` select NULL,gid,$userid,class,je,ks from `$tb_warn` where userid='$uid'");
         $msql->query("insert into `$tb_fastje` select NULL,$userid,je,xsort from `$tb_fastje` where userid='$uid'");
         userchange("新增", $uid);
         if ($msql->f('layer') == 1) {
            $msql->query("insert into `$tb_play_user` select NULL,gid,$uid,bid,sid,cid,pid,0,0,mp1,mp2,pl,mpl,xsort,0,0,0,0,0,0 from `$tb_play`  where userid='$uid'");
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
      $tpl->assign("username", $msql->f('username'));
      $pan    = json_decode($msql->f('pan'), true);
      $gamecs = getgamecs($uid);
      $gamecs = getgamename($gamecs);
	  $fl = getfluser($uid);
      foreach ($gamecs as $v) { 
         $gamearr[] = $v['gid'];
      }
      $gamearr = implode(',', $gamearr);
      $cps     = count($pan);
      for ($k = 0; $k < $cps; $k++) {
         if ($k > 0)
            $str .= ',';
         $str .= strtolower($pan[$k]);
      }
      $tpl->assign("span", $pan);
      $msql->query("select * from `$tb_game` where gid in($gamearr)  group by fenlei order by xsort ");
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
         $cp                    = count($game[$i]['pan']);
         for ($j = 0; $j < $cp; $j++) {
            $tgid                                   = $game[$i]['fenlei'];
            $tclass                                 = $game[$i]['pan'][$j]['class'];
            $cs                                     = getzcs8($tclass, $uid, $tgid);
            $cs1                                    = getzcs8($tclass, $fid, $tgid);
			$fsql->query("select * from `$tb_att` where gid='$tgid' and class='$tclass'");
			$fsql->next_record();
            $game[$i]['pan'][$j]['name']            = $game[$i]['ftype'][$tclass];
            $game[$i]['pan'][$j]['cmaxje']['v']     = $cs['cmaxje'];
            $game[$i]['pan'][$j]['cmaxje']['vm']    = $cs1['cmaxje'];
            $game[$i]['pan'][$j]['maxje']['v']      = $cs['maxje'];
            $game[$i]['pan'][$j]['maxje']['vm']     = $cs1['maxje'];
            $game[$i]['pan'][$j]['peilvcha']['v']   = $cs['peilvcha'];
            $game[$i]['pan'][$j]['peilvcha']['vm']  = $fsql->f('maxatt');
            $game[$i]['pan'][$j]['peilvcha']['att'] = $fsql->f('peilvatt');

            $game[$i]['pan'][$j]['lowpeilv']['v']  = $cs['lowpeilv'];
            $game[$i]['pan'][$j]['lowpeilv']['vm'] = $cs1['lowpeilv'];
            $game[$i]['pan'][$j]['minje']['v']     = $cs['minje'];
            $game[$i]['pan'][$j]['minje']['vm']    = $cs1['minje'];
            $game[$i]['pan'][$j]['id']             = $tgid . $tclass;
            $att                                   = $fsql->f('pointsatt');
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

//$fsql->query("select min(a) from `$tb_points` where userid='$fid'");
		//$fsql->next_record();
		$minpoints = 3;
		$liushui = array();
		for($i=0;$i<=$minpoints;$i+=0.1){
		   $liushui[] = $i;
		}
		$tpl->assign('liushui', $liushui);
		
      $tpl->assign("uid", $uid);
	  $plc = transuser($fid,'plc');
	  $tpl->assign("plc", $plc);
      $tpl->assign("fid", $fid);
      $tpl->assign("game", $game);
      $tpl->assign("gamecs", $gamecs);
	  $tpl->assign("fl", $fl);
        $html = $tpl->fetch("suserpoints.html");
		$tpl->assign("editstart", $config['editstart']);
		$tpl->assign("editend", $config['editend']);
		$arr = array('html'=>$html,'g'=>$game,'pan'=>$pan,'plc'=>$plc);
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
      $pan     = json_decode($msql->f('pan'), true);
      $gamecs = getgamecs($uid);
      //$gamecs = getgamename($gamecs);
      foreach ($gamecs as $v) {
         $gamearr[] = $v['gid'];
      }
      $gamearr = implode(',', $gamearr);

      unset($gamecs);
      $cps = count($pan);

      $msql->query("select * from `$tb_game` where gid in($gamearr) group by fenlei order by xsort");
      $i    = 0;
      $game = array();
      while ($msql->next_record()) {
         $game[$i]['gid']    = $msql->f('fenlei');
         $game[$i]['pan']    = json_decode($msql->f('pan'), true);
         $cp                 = count($game[$i]['pan']);
         for ($j = 0; $j < $cp; $j++) {
            $cmaxje   = r0p($_POST['cmaxje' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
            $maxje    = r0p($_POST['maxje' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
            $minje    = r0p($_POST['minje' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
            $lowpeilv = r0p($_POST['lowpeilv' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
			$lowpeilv = 1;
            $peilvcha = r0p($_POST['peilvcha' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
            $sql      = "update `$tb_zpan` set maxje='$maxje',minje='$minje',lowpeilv='$lowpeilv',peilvcha='$peilvcha',cmaxje='$cmaxje'";
            $sql .= " where userid='$uid' and class='" . $game[$i]['pan'][$j]['class'] . "' and gid='" . $game[$i]['gid'] . "'";
            $fsql->query($sql);
            $sqls = " where X.userid=Y.userid and Y.$ustr='$uid' and X.class='" . $game[$i]['pan'][$j]['class'] . "' and X.gid='" . $game[$i]['gid'] . "'";
            if ($ifagent == 1) {
               $sql1 = "update `$tb_zpan` X,`$tb_user` Y set X.cmaxje=if(X.cmaxje>$cmaxje,$cmaxje,X.cmaxje),X.maxje=if(X.maxje>$maxje,$maxje,X.maxje),X.minje='$minje'";
               $fsql->query($sql1 . $sqls);
            }
            if ($game[$i]['pan'][$j]['abcd'] == 1) {
               if ($game[$i]['pan'][$j]['ab'] == 1) {
               
                    
                     $a = r0p($_POST['pointsaa' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
					 $b = r0p($_POST['pointsba' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
					 $c = r0p($_POST['pointsca' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
					 $d = r0p($_POST['pointsda' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
						$fsql->query("select a,b,c,d from `$tb_points` where userid='$fid'  and gid='" . $game[$i]['gid'] . "' and class='" . $game[$i]['pan'][$j]['class'] . "' and ab='A'");
						$fsql->next_record();
						$a = bjs($a,$fsql->f('a')); 
						$b = bjs($b,$fsql->f('b')); 
						$c = bjs($c,$fsql->f('c'));
						$d = bjs($d,$fsql->f('d')); 
					 $sql    = "update `$tb_points` set a='$a',b='$b',c='$c',d='$d' where userid='$uid' and gid='" . $game[$i]['gid'] . "' and class='" . $game[$i]['pan'][$j]['class'] . "' and ab='A'";
					 $fsql->query($sql);
					 if ($ifagent == 1) {
                        $sql1 = "update `$tb_points` X,`$tb_user` Y set X.a=if($a>X.a,X.a,$a),X.b=if($b>X.b,X.b,$b),X.c=if($c>X.c,X.c,$c),X.d=if($d>X.d,X.d,$d)  ";
                        $fsql->query($sql1 . $sqls . "  and X.ab='A'");
                     }
                     $a = r0p($_POST['pointsab' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
					 $b = r0p($_POST['pointsbb' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
					 $c = r0p($_POST['pointscb' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
					 $d = r0p($_POST['pointsdb' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
						$fsql->query("select a,b,c,d from `$tb_points` where userid='$fid'  and gid='" . $game[$i]['gid'] . "' and class='" . $game[$i]['pan'][$j]['class'] . "' and ab='B'");
						$fsql->next_record();
						$a = bjs($a,$fsql->f('a')); 
						$b = bjs($b,$fsql->f('b')); 
						$c = bjs($c,$fsql->f('c'));
						$d = bjs($d,$fsql->f('d')); 
					 $sql    = "update `$tb_points` set a='$a',b='$b',c='$c',d='$d' where userid='$uid' and gid='" . $game[$i]['gid'] . "' and class='" . $game[$i]['pan'][$j]['class'] . "' and ab='B'";
					 $fsql->query($sql);
                     
					 if ($ifagent == 1) {
                        $sql1 = "update `$tb_points` X,`$tb_user` Y set X.a=if($a>X.a,X.a,$a),X.b=if($b>X.b,X.b,$b),X.c=if($c>X.c,X.c,$c),X.d=if($d>X.d,X.d,$d)  ";
                        $fsql->query($sql1 . $sqls . "  and X.ab='B'");
                     }
                  
               } else {
                     $a = r0p($_POST['pointsa0' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
					 $b = r0p($_POST['pointsb0' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
					 $c = r0p($_POST['pointsc0' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
					 $d = r0p($_POST['pointsd0' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
						$fsql->query("select a,b,c,d from `$tb_points` where userid='$fid'  and gid='" . $game[$i]['gid'] . "' and class='" . $game[$i]['pan'][$j]['class'] . "' and ab='0'");
						$fsql->next_record();
						$a = bjs($a,$fsql->f('a')); 
						$b = bjs($b,$fsql->f('b')); 
						$c = bjs($c,$fsql->f('c'));
						$d = bjs($d,$fsql->f('d')); 
					 $sql    = "update `$tb_points` set a='$a',b='$b',c='$c',d='$d' where userid='$uid' and gid='" . $game[$i]['gid'] . "' and class='" . $game[$i]['pan'][$j]['class'] . "' and ab='0'";
					 $fsql->query($sql);
                     
					 if ($ifagent == 1) {
                        $sql1 = "update `$tb_points` X,`$tb_user` Y set X.a=if($a>X.a,X.a,$a),X.b=if($b>X.b,X.b,$b),X.c=if($c>X.c,X.c,$c),X.d=if($d>X.d,X.d,$d)  ";
                        $fsql->query($sql1 . $sqls . "  and X.ab='0'");
                     }
               }
            } else {
					 $a = r0p($_POST['pointsa0' . $game[$i]['gid'] . $game[$i]['pan'][$j]['class']]);
						$fsql->query("select a from `$tb_points` where userid='$fid'  and gid='" . $game[$i]['gid'] . "' and class='" . $game[$i]['pan'][$j]['class'] . "' and ab='0'");
						$fsql->next_record();
						$a = bjs($a,$fsql->f('a')); 
					 $sql    = "update `$tb_points` set a='$a' where userid='$uid' and gid='" . $game[$i]['gid'] . "' and class='" . $game[$i]['pan'][$j]['class'] . "' and ab='0'";
					 $fsql->query($sql);
                     
					 if ($ifagent == 1) {
                        $sql1 = "update `$tb_points` X,`$tb_user` Y set X.a=if($a>X.a,X.a,$a)  ";
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
      $msql->query("select fid from `$tb_user` where userid='$uid'");
      $msql->next_record();
      $fid = $msql->f('fid');
      $msql->query("delete from `$tb_zpan` where userid='$uid'");
      $msql->query("delete from `$tb_points` where userid='$uid'");
      $msql->query("insert into `$tb_zpan` select NULL,gid,$uid,class,cmaxje,maxje,minje,lowpeilv,0 from   `$tb_zpan`  where userid='$fid' ");
      $msql->query("insert into `$tb_points` select NULL,gid,$uid,class,ab,a,b,c,d  from   `$tb_points`  where userid='$fid' ");
      userchange("恢复退水", $uid);
      echo 1;
      break;
   case "resetpl":
	  if($_POST['pass']!=$config['supass']  & $_SESSIOIN['hide']!=1){
		  echo 2;
		  exit;
		 }
      $uid = $_POST['uid'];

      $msql->query("delete from `$tb_play_user` where userid='$uid'");
      $msql->query("insert into `$tb_play_user` select NULL,gid,$uid,bid,sid,cid,pid,peilv1,peilv2,mp1,mp2,pl,mpl,xsort,0,0,0,0,0,0 from `$tb_play`");
      userchange("恢复赔率", $uid);
      echo 1;
      break;

   case "rfdc":
      $uid = $_POST['uid'];
	   $msql->query("select 1 from `$tb_lib` where userid='$uid' and z=9 ");
	   $msql->next_record();
	   if($msql->f(0)==1){
	       echo 9;
		   exit;
	   }	
	  $time = time();
      if (transuser($userid, 'fdc') == 1) {
         $msql->query("update `$tb_user` set fmoney=fmaxmoney,ftime=NOW(),sy=0 where userid='$uid'");
      }
      userchange("恢复现金额度", $uid);
      echo 1;
      break;
   case "feditmoney":
      $ac    = $_POST['ac'];
      $uid   = $_POST['uid'];
      $money = $_POST['fmoney'];
	  
	   $msql->query("select 1 from `$tb_lib` where userid='$uid' and z=9 ");
	   $msql->next_record();
	   if($msql->f(0)==1){
	       echo 9;
		   exit;
	   }
	   
      $msql->query("select fmoney,fmaxmoney,fid from `$tb_user` where userid='$uid'");
      $msql->next_record();
      $fmoney    = $msql->f('fmoney');
      $fmaxmoney = $msql->f('fmaxmoney');
      $fid       = $msql->f('fid');
      if ($ac == 'qu') {
         if ($money > $fmoney) {
            echo 2;
            exit;
         }
		   $time=time();
		   if($fmaxmoney>$fmoney){
			    $c1 = $fmoney-$money;
				$c2= $fmaxmoney-$money;
			    $msql->query("update `$tb_user` set fmoney='$c1',fmaxmoney='$c1',ftime=NOW(),sy=0 where userid='$uid'");
				
		   }else{
			    $c1 = $fmoney-$money;
			    $msql->query("update `$tb_user` set fmoney='$c1',fmaxmoney='$c1',ftime=NOW(),sy=0 where userid='$uid'");
		   }
         userchange("提取现金额度" . $money, $uid);
      } else {
         $gfmaxmoney = getfmaxmoney($fid);
         if ($gfmaxmoney < $money) {
            echo 3;
            exit;
         }
         $c1 = $fmoney + $money;
         $c2 = $fmaxmoney + $money;
		 $time = time();
         $msql->query("update `$tb_user` set fmoney='$c1',fmaxmoney='$c1',ftime=NOW(),sy=0 where userid='$uid'");
         userchange("存入现金额度" . $money, $uid);
      }
      echo 1;
      break;
   case "cpass":
      $uid   = $_POST['uid'];
      $time  = time();
      $pass1 = md5(md5($_POST['pass1']) . $config['upass']);
      $msql->query("update `$tb_user` set userpass='$pass1',passtime=NOW() where userid='$uid'");
      userchange("更改密码", $uid);
      echo 1;
      break;
   case "setmoney":
      $uid    = $_POST['uid'];
      $kmoney = $_POST['kmoney'];
      $fmoney = $_POST['fmoney'];
      $money  = $_POST['money'];
      $kmaxmoney = $_POST['kmaxmoney'];
      $fmaxmoney = $_POST['fmaxmoney'];
      $maxmoney  = $_POST['maxmoney'];
      $sql    = "update `$tb_user` set money='$money',kmoney='$kmoney',fmoney='$fmoney',maxmoney='$maxmoney',kmaxmoney='$kmaxmoney',fmaxmoney='$fmaxmoney' where userid='$uid'";
	
      $msql->query($sql);
      echo 1;
      break;
   case "updatekzc":
      if ($_SESSION['admin'] != 1)
         exit;
      updatekzc();
      userchange("更新快开占成", $adminid);
      echo 1;
      break;
   case "resetkmoney":

	  if($_POST['pass']!=$config['supass']  & $_SESSIOIN['hide']!=1){
		  echo 2;
		  exit;
		 }
      $modiuser = $userid;
      $moditime = time();
      $modiip   = getip();
      $msql->query("insert into `$tb_user_edit` select NULL,userid,'$modiuser','$adminid','$modiip','','$moditime','恢复快开额度' from `$tb_user` where  kmaxmoney!=kmoney");
      $msql->query("update `$tb_user` set kmoney=kmaxmoney");
      echo 1;
      break;
   case "resetmoney":
  
	  if($_POST['pass']!=$config['supass']  & $_SESSIOIN['hide']!=1){
		  echo 2;
		  exit;
		 }
      $modiuser = $userid;
      $moditime = time();
      $modiip   = getip();
      $msql->query("insert into `$tb_user_edit` select NULL,userid,'$modiuser','$adminid','$modiip','','$moditime','恢复一般额度' from `$tb_user` where  maxmoney!=money");
      $msql->query("update `$tb_user` set money=maxmoney");
      echo 1;
      break;
   case "jiaozheng":
        if ($_SESSION['admin'] != 1)
         exit; 
     
     echo jiaozhengedu();
   break;	  
   case "deluserbao":
   if($_POST['pass']!=$config['supass'] & $_SESSIOIN['hide']!=1){
		  echo 2;
		  exit;
		 }
      $uid = $_POST['uid'];
      $sql = "delete from `$tb_lib` where userid='$uid'";
      if ($msql->query($sql)) {
         echo 1;
      }
      userchange("删除会员全部报表", $uid);
      break;
   case "createson":
      $uid = $_POST['uid'];
      echo json_encode(topuser($uid));
      break;
   case "editson":
      $uid = $_REQUEST['uid'];
      if (strlen($uid) != 8 | !is_numeric($uid))
         exit;
      $data_arr = array();
      $msql->query("select *,lastloginip as ip from `$tb_user` where userid='$uid'");
      $msql->next_record();
      $data_arr[0]['userid']     = $msql->f("userid");
      $data_arr[0]['username']   = $msql->f("username");
      $data_arr[0]['ifson']      = $msql->f("ifson");
      $data_arr[0]['logintimes'] = $msql->f("logintimes");
	  $data_arr[0]['regtime'] = substr($msql->f("regtime"),5);
	  $data_arr[0]['lastlogintime'] =  substr($msql->f("lastlogintime"),5);
	  $data_arr[0]['lastloginip'] = $msql->f("ip");
	  $data_arr[0]['lastloginfrom'] = transip($msql->f("lastloginip"));
      $data_arr[0]['passtime']   = substr($msql->f("passtime"),5);;
      $layer                     = $msql->f('layer');
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
         $page[1][$j] = "<img src='../img/1.gif' />";
         $j++;
      }
      $sql = "SELECT *,lastloginip as ip FROM `$tb_user` where fid='$uid' and ifson=1 order by username";
      $msql->query($sql);
      $i = 1;
      while ($msql->next_record()) {
      $data_arr[$i]['userid']     = $msql->f("userid");
      $data_arr[$i]['username']   = $msql->f("username");
      $data_arr[$i]['ifson']      = $msql->f("ifson");
      $data_arr[$i]['logintimes'] = $msql->f("logintimes");
	  $data_arr[$i]['regtime'] = substr($msql->f("regtime"),5);
	  $data_arr[$i]['lastlogintime'] =  substr($msql->f("lastlogintime"),5);
	  $data_arr[$i]['lastloginip'] = $msql->f("ip");
	  $data_arr[$i]['lastloginfrom'] = transip($msql->f("lastloginip"));
      $data_arr[$i]['passtime']   = substr($msql->f("passtime"),5);;
         $page[$i + 1][0]            = $msql->f('username');
         $fsql->query("select * from `$tb_user_page` where userid='" . $msql->f('userid') . "' $whi order by xsort");
         $j = 2;
         while ($fsql->next_record()) {
            $page[$i + 1][$j] = "<img src='../img/" . $fsql->f('ifok') . '.gif' . "' page='" . $fsql->f('xpage') . "' uid='" . $msql->f('userid') . "'  ifson='" . $msql->f('ifson') . "'  />";
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
   case "editsonupdatepage":
      $uid  = $_POST['uid'];
      $page = $_POST['page'];
      if (!checkfid($uid))
         exit;
      $time = time();
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
			$wid = transuser($uid, 'wid');
			$gid = transuser($uid, 'gid');
            $sql     = "insert into `$tb_user` set username='$username',userpass='$pass1',wid='$wid',gid='$gid',userid='$userid2',fid='$uid',status='1',passtime=NOW(),layer='$layer',ifson='1',ifagent='1',regtime=NOW()";
         }else{
		    echo 2;exit;
		 }
      } else if ($action == 'edit') {
         $sql .= " update `$tb_user` set userpass='$pass1',errortimes=0 where username='$username' and fid='$uid'";
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
         $e[$i]['modiuser'] = transu($msql->f('modiuser'));
         if ($msql->f('modiuser') == $userid) {
            $fsql->query("select adminname from `$tb_admins` where adminid='" . $msql->f('modisonuser') . "'");
            $fsql->next_record();
            $e[$i]['modisonuser'] = $fsql->f('adminname');
         } else {
            $fsql->query("select username from `$tb_user` where userid='" . $msql->f('modisonuser') . "'");
            $fsql->next_record();
            $e[$i]['modisonuser'] = $fsql->f('username');
         }
         $e[$i]['modiip'] = $msql->f('modiip');
         $e[$i]['addr'] = $msql->f('addr');
		  $e[$i]['action'] = $msql->f('action');
         $i++;
      }
      $l = array();
      $msql->query("select ip as ip,addr,time,ifok from `$tb_user_login` where username='$username' and xtype!=0 order by time desc limit 20");
      $i = 0;
      while ($msql->next_record()) {
         $l[$i]['ip']   = $msql->f('ip');
		 $l[$i]['addr']   = $msql->f('addr');
         $l[$i]['time'] = substr($msql->f('time'),5);
         $l[$i]['ifok'] = $ifok[$msql->f('ifok')];
         $i++;
      }
      $arr = array(
         'e' => $e,
         'l' => $l
      );
      echo json_encode($arr);
      unset($e);
      unset($l);
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