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
        $msql->query("select * from `$tb_config`");
        $msql->next_record();
        $config['passtime']    = $msql->f("passtime");
        $config['livetime']    = $msql->f("livetime");
        $config['maxpc']       = $msql->f("maxpc");
        $config['tzjg']        = $msql->f("tzjg");
        $config['supass']      = $msql->f("supass");
        $config['reseted']     = $msql->f("reseted");
        $config['editstart']   = $msql->f("editstart");
        $config['editend']     = $msql->f("editend");
        $config['comattpeilv'] = $msql->f("comattpeilv");
        $config['flyflag']    = $msql->f("flyflag");
        $config['autobaoma']   = $msql->f("autobaoma");
        $config['ifopen']      = $msql->f("ifopen");
        $config['editzc']      = $msql->f("editzc");
        $config['deluser']     = $msql->f("deluser");
        $config['autoresetpl'] = $msql->f("autoresetpl");
		$config['autold'] = $msql->f("autold");
		$config['plresetfs'] = $msql->f("plresetfs");
		$config['loginfenli'] = $msql->f("loginfenli");
		$config['minje'] = $msql->f('minje');
        $config['moneytype'] = $msql->f('moneytype');
        $config['yingxz'] = $msql->f('yingxz');
        $config['yingxzje'] = $msql->f('yingxzje');
		$config['merge'] = $msql->f('merge');
		$config['baobf'] = $msql->f('baobf');
		$config['agents'] = $msql->f('agents');
		$config['members'] = $msql->f('members');
		$config['panel'] = $msql->f('panel');
		$config['iprecord'] = $msql->f('iprecord');
		$msql->query("select plc from `$tb_user` where userid='$userid'");
		$msql->next_record();
		$config['plc'] = $msql->f('plc');
		$msql->query("select webname from `$tb_web` where wid='100'");
		$msql->next_record();
		$config['webname'] = $msql->f('webname');
		
        $tpl->assign("config", $config);
        transuser($userid,'moneypass')=="" ? $moneypassflag =0 : $moneypassflag =1 ;
        $tpl->assign("moneypassflag",$moneypassflag);
        $tpl->display("sysconfig.html");
        break;
    case "setsys":
        // 优选源码源码网 yxymk.net yxymk.com yxymk.com
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
        $passtime    = isnum($_POST["passtime"]);
        $livetime    = isnum($_POST["livetime"]);
        $maxpc       = isnum($_POST["maxpc"]);
        $tzjg        = isnum($_POST["tzjg"]);
        $supass      = $_POST["supass"];
        $reseted     = $_POST["reseted"];
        $editstart   = $_POST["editstart"];
        $editend     = $_POST["editend"];
		$merge     = $_POST["merge"];
		$baobf     = $_POST["baobf"];
        $agents       = isnum($_POST["agents"]);
		$members       = isnum($_POST["members"]);
        $comattpeilv = isnum($_POST["comattpeilv"]);
        $flyflag     = isnum($_POST["flyflag"]);
        $autobaoma   = isnum($_POST["autobaoma"]);
        $ifopen      = isnum($_POST["ifopen"]);
        $editzc      = isnum($_POST["editzc"]);
        $deluser     = isnum($_POST["deluser"]);
        $autoresetpl = isnum($_POST["autoresetpl"]);
		$autold = isnum($_POST["autold"]);
		$moneytype = isnum($_POST["moneytype"]);		
		$plresetfs     = $_POST["plresetfs"];
		$loginfenli = isnum($_POST["loginfenli"]);
		$zcmode = isnum($_POST["zcmode"]);
		$plc = isnum($_POST["plc"]);
		$pk10num =$_POST["pk10num"];
		$pk10ts =$_POST["pk10ts"];
		$minje     = $_POST["minje"];
		$webname     = $_POST["webname"];
		$pk10niu = isnum($_POST["pk10niu"]);
        $yingxz     = $_POST["yingxz"];
        $yingxzje     = $_POST["yingxzje"];
		$panel = isnum($_POST["panel"]);
		$iprecord = isnum($_POST["iprecord"]);
        if(empty($minje) | !is_numeric($minje) | $minje%1!=0 | $minje<1){
		    $minje=2;
		}
    //    $sql         = "passtime='$passtime',livetime='$livetime',maxpc='$maxpc',tzjg='$tzjg',reseted='$reseted',editstart='$editstart',editend='$editend',comattpeilv='$comattpeilv',flyflag='$flyflag',autobaoma='$autobaoma',ifopen='$ifopen',editzc='$editzc',deluser='$deluser',autoresetpl='$autoresetpl',moneytype='$moneytype',autold='$autold',plresetfs='$plresetfs',loginfenli='$loginfenli',zcmode='$zcmode',minje='$minje',pk10num='$pk10num',pk10ts='$pk10ts',pk10niu='$pk10niu',yingxz='$yingxz',yingxzje='$yingxzje'";
	
	    $sql         = "passtime='$passtime',livetime='$livetime',maxpc='$maxpc',tzjg='$tzjg',reseted='$reseted',editstart='$editstart',editend='$editend',merge='$merge',baobf='$baobf',comattpeilv='$comattpeilv',flyflag='$flyflag',autobaoma='$autobaoma',ifopen='$ifopen',editzc='$editzc',deluser='$deluser',autoresetpl='$autoresetpl',moneytype='$moneytype',plresetfs='$plresetfs',loginfenli='$loginfenli',minje='$minje',agents='$agents',members='$members',panel='$panel',iprecord='$iprecord'";
	
	
		if($supass!=''){
		    $sql .= ",supass= '$supass'";
		}
        $sql         = "update `$tb_config` set $sql";
        $msql->query($sql);
		$msql->query("update `$tb_points` set minje='$minje'");
		if($plc==0 || $plc==1){
		   $msql->query("select plc from `$tb_user` where userid='$userid'");
		   $msql->next_record();
		   $oldplc = $msql->f('plc');
		   $msql->query("update `$tb_user` set plc='$plc' where userid='$userid'");
		   if($plc==0 & $oldplc==1){
			    $msql->query("update `$tb_user` set plc=0");
				$msql->query("update `$tb_zpan` set peilvcha=0");
		   }
		}
		$msql->query("update `$tb_web` set webname='$webname' where wid='100'");
		
        echo 1;
        break;
}
?>