<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');

include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');

switch ($_REQUEST['xtype']) {
    case "show":
        
        $msql->query("select * from `$tb_web` order by wid");
        $web = array();
        $i   = 0;
        while ($msql->next_record()) {
            $web[$i]['wid']  = $msql->f('wid');
			$web[$i]['webname']  = $msql->f('webname');
            $web[$i]['namehead'] = json_decode($msql->f('namehead'), true);
			$web[$i]['namehead'] = implode('-',$web[$i]['namehead']);
			$web[$i]['namehead'] = trim($web[$i]['namehead']);
			$web[$i]['layer'] = json_decode($msql->f('layer'), true);
			$web[$i]['layer'] = implode('-',$web[$i]['layer']);
			
			$web[$i]['maxlayer']     = $msql->f('maxlayer');
            $web[$i]['patt']     = $msql->f('patt');
            $web[$i]['moneytype']    = $msql->f('moneytype');
			$web[$i]['slowtype']    = $msql->f('slowtype');
			$web[$i]['fasttype']    = $msql->f('fasttype');
			
			$web[$i]['zcagent']    = $msql->f('zcagent');
			$web[$i]['guser']    = $msql->f('guser');
			$web[$i]['uskin']    = $msql->f('uskin');
			$web[$i]['skins']    = $msql->f('skins');
			
			$web[$i]['mpo']    = $msql->f('mpo');
            $web[$i]['upo']    = $msql->f('upo');
            $web[$i]['apo']    = $msql->f('apo');
            $web[$i]['hpo']    = $msql->f('hpo');
			
			$web[$i]['mdi']     = $msql->f('mdi');
            $web[$i]['udi']     = $msql->f('udi');
            $web[$i]['adi']     = $msql->f('adi');
            $web[$i]['hdi']     = $msql->f('hdi');
			
			$web[$i]['murl']     = $msql->f('murl');
            $web[$i]['uurl']     = $msql->f('uurl');
            $web[$i]['aurl']     = $msql->f('aurl');
            $web[$i]['hurl']     = $msql->f('hurl');
			
			$web[$i]['mimg']     = $msql->f('mimg');
            $web[$i]['uimg']     = $msql->f('uimg');
            $web[$i]['aimg']     = $msql->f('aimg');
            $web[$i]['himg']     = $msql->f('himg');
			
			$web[$i]['mcode']    = $msql->f('mcode');
            $web[$i]['ucode']    = $msql->f('ucode');
            $web[$i]['acode']    = $msql->f('acode');
            $web[$i]['hcode']    = $msql->f('hcode');
			
            $web[$i]['webclose']    = $msql->f('webclose');
            $web[$i]['fastinput']    = $msql->f('fastinput');

			$web[$i]['times']    = $msql->f('times');
            $i++;
        }
		$msql->query("select * from `$tb_config`");
		$msql->next_record();
		$config['s1'] = $msql->f('s1');
		$config['s2'] = $msql->f('s2');
		$config['s3'] = $msql->f('s3');
		$config['s4'] = $msql->f('s4');
		$config['s5'] = $msql->f('s5');
		$config['s6'] = $msql->f('s6');
		$config['kfurl'] = $msql->f('kfurl');
		$config['autodellogin'] = $msql->f('autodellogin');
		$config['autodellogintime'] = $msql->f('autodellogintime');
		$config['autodeledit'] = $msql->f('autodeledit');
		$config['autodeledittime'] = $msql->f('autodeledittime');
		$config['autodelpl'] = $msql->f('autodelpl');
		$config['autodelpltime'] = $msql->f('autodelpltime');
		$config['logincode'] = $msql->f('logincode');
		$config['loginfs'] = $msql->f('loginfs');
        $tpl->assign("config", $config);
	    $tpl->assign("game",getgame());
        $tpl->assign("web", $web);
        $tpl->display("webconfig.html");
        break;
    case "setsys":
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
		$kjip = $_POST['kjip'];
		$startid = $_POST['startid'];
		$libkey = isnum($_POST['libkey']);
		$maxrenflag = isnum($_POST['maxrenflag']);
        $allpass   = $_POST['allpass'];
		$rkey = isnum($_POST['rkey']);
		
		$psize = isnum($_POST['psize']);
		$psize1 = isnum($_POST['psize1']);
		$psize2 = isnum($_POST['psize2']);
		$psize3 = isnum($_POST['psize3']);
		$psize5 = isnum($_POST['psize5']);
		$logincode = isnum($_POST['logincode']);
        $loginfs   = $_POST['loginfs'];
   
		$s1 = $_POST['s1'];
		$s2 = $_POST['s2'];
		$s3 = $_POST['s3'];
		$s4 = $_POST['s4'];
		$s5 = $_POST['s5'];
		$s6 = $_POST['s6'];
		
		$kfurl = $_POST['kfurl'];

		$autodellogin = isnum($_POST['autodellogin']);
		$autodellogintime = isnum($_POST['autodellogintime']);
		$autodeledit = isnum($_POST['autodeledit']);
		$autodeledittime = isnum($_POST['autodeledittime']);
		$autodelpl = isnum($_POST['autodelpl']);
		$autodelpltime = isnum($_POST['autodelpltime']);

       
        $trys = isnum($_POST['trys']);

        $sql      = "update `$tb_config` set autodellogin='$autodellogin',autodellogintime='$autodellogintime',autodeledit='$autodeledit',autodeledittime='$autodeledittime',autodelpl='$autodelpl',autodelpltime='$autodelpltime',psize='$psize',psize1='$psize1',psize2='$psize2',psize3='$psize3',psize5='$psize5'";

        $msql->query($sql);
        echo 1;
        
        break;
	case "editweb":
            $wid  = $_POST['wid'];
            $namehead = $_POST['namehead']; 
			$layer = $_POST['layer'];
			//echo $layer;
			$action = $_POST['action'];
			$sql = '';
			foreach($_POST as $key => $val){
				if($key!='action' & $key != 'wid' & $key != 'namehead'  & $key != 'layer' & $key != 'xtype'){
				    $sql .= ','.$key ."='" .trim($val)."'";
				}
			}
			$namehead = explode('-',$namehead);
			if (count($namehead)==10) unset($namehead[9]);
			foreach($namehead as $k => $v){
			     $namehead[$k] = trim($v);
			}
            $namehead = json_encode($namehead);
			
			$layer = explode('-',$layer);
			if (count($layer)==10) unset($layer[9]);
			foreach($layer as $k => $v){
			     $layer[$k] = trim($v);
			}
            $layer = json_encode($layer);
			$layer= str_replace('\\','\\\\',$layer);
			
			$sql = "namehead='".trim($namehead)."',layer='".trim($layer)."'".$sql;

			if($action=='add'){
			    $wid = setupid($tb_web,'wid');
			    $sql = "insert into `$tb_web` set wid='$wid',".$sql;
			}else{
			    $sql = "update `$tb_web` set $sql where wid='$wid'";
			}
			$msql->query($sql);
			echo 1;
            
							 
	break;
	case "delweb":
	      $wid = $_POST['wid'];
		  $czpass = $_POST['czpass'];
		  if($czpass!=$config['supass']){
			   echo 2;
			   exit; 
		  }
		  $msql->query("delete from `$tb_web` where wid='$wid'");
		  echo 1;
	break;
        
}
?>