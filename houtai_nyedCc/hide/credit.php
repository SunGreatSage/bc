<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show";
        $msql->query("select pan from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();

       
        $pan = json_decode( $msql->f('pan'), true);
        $cps = count($pan);
        for ($k = 0; $k < $cps; $k++) {
            if ($k > 0) {
                $str .= ',';
            }
            $str .= strtolower($pan[$k]);
        }
        $tpl->assign('span', $pan);
        $gamecs = getgamecs($userid);
        foreach ($gamecs as $v) {
            $gamearr[] = $v['gid'];
        }
        $gamearr = implode(',', $gamearr);
        $msql->query("select * from `{$tb_game}` where gid in ({$gamearr}) order by xsort");
        $i = 0;
        $game = array();
        while ($msql->next_record()) {
            $game[$i]['gid'] = $msql->f('gid');
            $tgid = $msql->f('gid');
            $game[$i]['gname'] = $msql->f('gname');
            $game[$i]['panstatus'] = $msql->f('panstatus');
            $game[$i]['fast'] = $msql->f('fast');
            $game[$i]['pan'] = json_decode($msql->f('pan'), true);
            $game[$i]['dftype'] = json_decode($msql->f('dftype'), true);
            $cp = count($game[$i]['pan']);
            for ($j = 0; $j < $cp; $j++) {
                $tclass = $game[$i]['pan'][$j]['class'];
                $cs = getjes8($tclass, $userid, $tgid);
                $game[$i]['pan'][$j]['name'] = $game[$i]['dftype'][$tclass];
                $game[$i]['pan'][$j]['cmaxje'] = $cs['cmaxje'];
                $game[$i]['pan'][$j]['maxje'] = $cs['maxje'];
                $game[$i]['pan'][$j]['minje'] = $cs['minje'];
                if ($game[$i]['pan'][$j]['abcd'] == 1) {
                    if ($game[$i]['pan'][$j]['ab'] == 1) {
                        $fsql->query("select {$str} from `{$tb_points}` where userid='{$userid}' and gid='{$tgid}' and class='{$tclass}'  and  ab='A' ");
                        $fsql->next_record();
                        for ($k = 0; $k < $cps; $k++) {
                            $tmp = strtolower($pan[$k]);
                            $game[$i]['pan'][$j]['points' . $tmp . 'a'] = pr2($fsql->f($tmp));
                        }
                        $fsql->query("select {$str} from `{$tb_points}` where userid='{$userid}' and gid='{$tgid}' and class='{$tclass}'  and  ab='B' ");
                        $fsql->next_record();
                        for ($k = 0; $k < $cps; $k++) {
                            $tmp = strtolower($pan[$k]);
                            $game[$i]['pan'][$j]['points' . $tmp . 'b'] = pr2($fsql->f($tmp));
                        }
                    } else {
                        $fsql->query("select {$str} from `{$tb_points}` where userid='{$userid}' and gid='{$tgid}' and class='{$tclass}'  and  ab='0' ");
                        $fsql->next_record();
                        for ($k = 0; $k < $cps; $k++) {
                            $tmp = strtolower($pan[$k]);
                            $game[$i]['pan'][$j]['points' . $tmp . '0'] = pr2($fsql->f($tmp));
                        }
                    }
                } else {
                    $fsql->query("select a from `{$tb_points}` where userid='{$userid}' and gid='{$tgid}' and class='{$tclass}'  and  ab='0' ");
                    $fsql->next_record();
                    $game[$i]['pan'][$j]['pointsa0'] = pr2($fsql->f('a'));
                }
            }
            $i++;
        }
        $tpl->assign('game', $game);
		$tpl->assign('gid', $gid);
		
        $msql->query("select * from `$tb_user` where userid='$userid'");
        $msql->next_record();
        $tpl->assign('ugid', $msql->f('gid'));
		$ugame =getgamecs($msql->f('userid'));
		$ugame = getgamename($ugame);
		$tpl->assign('ugame',$ugame);
		$tpl->assign('name', $msql->f('name'));
        $tpl->assign('username', $msql->f('username'));
        $money = getmaxmoney($userid);
        $money<0 && $money=0;
        $tpl->assign('money', $money);
        $tpl->assign('maxmoney', $msql->f('maxmoney'));
        $tpl->assign('premoney', p2($money / $msql->f('maxmoney')) * 100);
        $kmoney = getkmaxmoney($userid);
        $kmoney<0 && $kmoney=0;
        $tpl->assign('kmoney', $kmoney);
        $tpl->assign('kmaxmoney', $msql->f('kmaxmoney'));
        $tpl->assign('prekmoney', p2($kmoney / $msql->f('kmaxmoney')) * 100);

		$tpl->assign('fudong', $msql->f('fudong'));
        $ren = getmaxren($userid);
        $tpl->assign('ren', $ren);
        $tpl->assign('maxren', $msql->f('maxren'));		
        $tpl->assign('preren', p2($ren / $msql->f('maxren')) * 100);
        $tpl->assign('fly', transfly($msql->f('fly')));
        $tpl->assign('userid', $userid);
        $tpl->assign('pan', json_decode($msql->f('pan'), true));
        $tpl->assign('defaultpan', $msql->f('defaultpan'));
        $layer = $msql->f('layer');
        if ($msql->f('ifagent') == 1) {
            $tpl->assign('usertype', $config['layer'][$layer - 1]);
        } else {
            $tpl->assign('usertype', '会员');
        }
        $tpl->assign('layer', $msql->f('layer'));
        $tpl->assign('ifexe', $msql->f('ifexe'));
        $tpl->assign('pself', $msql->f('pself'));
        $tpl->assign('config', $config);
        $tpl->display("creditnew.html");
        break;

}
?>