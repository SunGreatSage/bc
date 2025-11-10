<?php
include('../data/comm.inc.php');include('../data/agentvar.php');
include('../func/func.php');
include('../func/agentfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
	include("../global/Iplocation_Class.php");
        $posi = array(
            "slib" => "即时注单",
            "baox" => "报表",
            "lib" => "未结明细",
            "bao" => "报表",
            "suser" => "帐户管理",
            "xxtz" => "投注明细",
            "now" => "注单明细",
            "make" => "投注区",
            "long" => "开奖记录",
            "credit" => "个人信用",
            "libset" => "注单设置",
            "record" => "操作记录",
            "changepass2" => "密码更改",
            "error" => "异常注单"
        );
        $time = time();

        $xtype    = $_REQUEST['type'];
		if($xtype == '' | $xtype == 1) $xtype=2;
		if($xtype==0) $xtype=1;
        if (!in_array($xtype, array(
            1,
            2
        ))) {
            $xtype = 2;
        }
		
        $layer = transuser($userid, 'layer');
		if($layer!=1){
		   //exit;
		}
        $whi   = " and userid in (select userid from `$tb_user` where fid='$userid' or fid" . $layer . "='$userid')";
        $rs    = $msql->arr("select count(id) from `$tb_online` where xtype=1 $whi", 0);
        $num[] = $rs[0][0];
        $rs    = $msql->arr("select count(id) from `$tb_online` where xtype=2 $whi", 0);
        $num[] = $rs[0][0];
        $tpl->assign("num", $num);
        $msql->query("select count(id) from `$tb_online` where  xtype='$xtype' $whi ");
        $msql->next_record();
        $rcount   = $msql->f(0);
        $psize    = $config['psize'];
        $pcount   = $rcount % $psize == 0 ? $rcount / $psize : (($rcount - $rcount % $psize) / $psize) + 1;
        $thispage = $_REQUEST['page'];

        if (!is_numeric($thispage) | $thispage == '') {
            $thispage = 1;
        }
        
        $msql->query("select *,ip as ips from `$tb_online` where xtype='$xtype'  $whi order by xtype,savetime desc limit " . (($thispage - 1) * $psize) . ",$psize");
        
        
        $i     = 0;
        $ipstr = '';
        $on    = array();
        
        while ($msql->next_record()) {
            $on[$i]['id'] = $msql->f('id');  
			$fsql->query("select username,name,ifagent from `$tb_user` where userid='" . $msql->f('userid') . "'");
            $fsql->next_record();

            $on[$i]['username'] = $fsql->f('username');
            $on[$i]['name']     = $fsql->f('name'); 
			if($fsql->f('ifagent')==0){
				$on[$i]['usertype'] = "会员";
			}else{
                $on[$i]['usertype'] = $config['layer'][$msql->f('layer') - 1];
			}
            $on[$i]['posi'] = $posi[$msql->f('page')];            
            $on[$i]['logintime'] = substr($msql->f('logintime'),-8);
            $on[$i]['savetime']  = substr($msql->f('savetime'),-8);
            $on[$i]['addr']      = mb_convert_encoding($ips->getaddress($msql->f('ip')),'utf-8','GBK');
            $on[$i]['server']    = $msql->f('server');            
            $on[$i]['uid'] = $msql->f('userid');
			$on[$i]['ip'] = '*';
            $on[$i]['i']   = $i + 1;
            $i++;
        }
        
        $tpl->assign('type', $xtype);
        $tpl->assign('on', $on);
        $tpl->assign('page', page($pcount, $thispage));
        $tpl->display("online.html");
        break;
    case "dellogin":
        if ($_SESSION['atype'] == 1) {
            $layer = transuser($userid, 'layer');
			$id    = explode('|',$_POST['id']);
			$fstr = " and fid".transuser($userid,'layer')."='$userid' ";
			foreach($id as $k => $v){
				if($v=='' | !is_numeric($v)) continue;
				$msql->query("select * from `$tb_online` where id='$v'");
				$msql->next_record();
			    $uid = $msql->f('userid');
			    if($fsql->query("update `$tb_user` set online=0 where userid='$uid' $fstr")){
				   $fsql->query("delete from `$tb_online` where id='$v'");
				}
			}
        }
		echo 1;
        break;
}
