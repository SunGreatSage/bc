<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case 'uinfo':
        $msql->query("select * from `{$tb_user}` where userid='{$userid}'");
        $msql->next_record();
        $tpl->assign('name', $msql->f('name'));
        $tpl->assign('username', $msql->f('username'));
        $tpl->assign('money', $msql->f('money'));
        $tpl->assign('maxmoney', $msql->f('maxmoney'));
        $tpl->assign('kmoney', $msql->f('kmoney'));
        $tpl->assign('kmaxmoney', $msql->f('kmaxmoney'));

       
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
            $tgid = $game[$i]['gid'];
            $game[$i]['gname'] = $msql->f('gname');
            $game[$i]['panstatus'] = $msql->f('panstatus');
            $game[$i]['fast'] = $msql->f('fast');
            $game[$i]['pan'] = json_decode($msql->f('pan'), true);
            $game[$i]['ftype'] = json_decode($msql->f('ftype'), true);
            $cp = count($game[$i]['pan']);
            for ($j = 0; $j < $cp; $j++) {
                $tclass = $game[$i]['pan'][$j]['class'];
                $cs = getzcs8($tclass, $userid, $game[$i]['gid']);
                $game[$i]['pan'][$j]['name'] = $game[$i]['ftype'][$tclass]['name'];
                $game[$i]['pan'][$j]['cmaxje'] = (float)$cs['cmaxje']/10000;
                $game[$i]['pan'][$j]['maxje'] = (float)$cs['maxje']/10000;
                $game[$i]['pan'][$j]['minje'] = $cs['minje'];
                $game[$i]['pan'][$j]['peilvcha'] = $cs['peilvcha'];
                $game[$i]['pan'][$j]['lowpeilv'] = $cs['lowpeilv'];
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
        $tpl->display($mobi . 'uinfo.html');
        break;
 
}