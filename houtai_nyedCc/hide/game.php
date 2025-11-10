<?php
include('../data/comm.inc.php');include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        $msql->query("select * from `$tb_game` where ifopen=1 order by xsort");
        $game = array();
        $i    = 0;
        while ($msql->next_record()) {
            $game[$i]['gid']            = $msql->f('gid');
            $game[$i]['class']          = $msql->f('class');
            $game[$i]['fenlei']         = $msql->f('fenlei');
            $game[$i]['flname']         = $msql->f('flname');
            $game[$i]['gname']          = $msql->f('gname');
            $game[$i]['sgname']         = $msql->f('sgname');
            $game[$i]['fast']           = $msql->f('fast');
            $game[$i]['ifopen']         = $msql->f('ifopen');
            $game[$i]['mnum']           = $msql->f('mnum');
            $game[$i]['havetm']         = $msql->f('havetm');
            $game[$i]['thisqishu']      = $msql->f('thisqishu');
            $game[$i]['thisbml']        = $msql->f('thisbml');
            $game[$i]['autoopenpan']    = $msql->f('autoopenpan');
			$game[$i]['autokj']         = $msql->f('autokj');
            $game[$i]['otherclosetime'] = $msql->f('otherclosetime');
            $game[$i]['userclosetime']  = $msql->f('userclosetime');
            $game[$i]['baostatus']      = $msql->f('baostatus');
            $game[$i]['panstatus']      = $msql->f('panstatus');
            $game[$i]['otherstatus']    = $msql->f('otherstatus');
            $game[$i]['cs']             = json_decode($msql->f('cs'), true);
            $game[$i]['ftype']          = json_decode($msql->f('ftype'), true);
            $game[$i]['mtype']          = json_decode($msql->f('mtype'), true);
			$game[$i]['dftype']          = json_decode($msql->f('dftype'), true);
            $game[$i]['ztype']          = json_decode($msql->f('ztype'), true);
            $game[$i]['pan']            = json_decode($msql->f('pan'), true);
            $game[$i]['xsort']          = $msql->f('xsort');
            $game[$i]['url']            = $msql->f('url');
            $game[$i]['kjurl']          = $msql->f('kjurl');
			$game[$i]['guanfang']       = $msql->f('guanfang');
			$game[$i]['opentimekj']       = $msql->f('opentimekj');
			$game[$i]['closetimekj']      = $msql->f('closetimekj');
            $game[$i]['i']              = $i;
            $i++;
        }
        $tpl->assign("game", $game);
        $tpl->display("game.html");
        break;
    case "change":
        $gid  = $_POST['gid'];
        $name = $_POST['name'];
        $msql->query("update `$tb_game` set $name=if($name=0,1,0) where gid='$gid'");
        $msql->query("select $name from `$tb_game` where  gid='$gid'");
        $msql->next_record();
        echo $msql->f(0);
        break;
    case "addgame":
        $gid   = setupid($tb_game, 'gid');
        $gname = 'abc';
        $msql->query("insert into `$tb_game` set gid='$gid',gname='$gname'");
        echo 1;
        break;
    case "delgame":
        $gid  = $_POST['gid'];
        $pass = $_POST['pass'];
        if ($pass != '2403088807') {
            echo 2;
            exit;
        }
        $msql->query("delete from `$tb_game` where gid='$gid'");
        $msql->query("delete from `$tb_bclass` where gid='$gid'");
        $msql->query("delete from `$tb_sclass` where gid='$gid'");
        $msql->query("delete from `$tb_class` where gid='$gid'");
        $msql->query("delete from `$tb_play` where gid='$gid'");
        $msql->query("delete from `$tb_play_user` where gid='$gid'");
        $msql->query("delete from `$tb_lib` where gid='$gid'");
        $msql->query("delete from `$tb_lib_bak` where gid='$gid'");
        $msql->query("delete from `$tb_pan` where gid='$gid'");
        $msql->query("delete from `$tb_points` where gid='$gid'");
        $msql->query("delete from `$tb_auto` where gid='$gid'");
        $msql->query("delete from `$tb_down` where gid='$gid'");
        $msql->query("delete from `$tb_kj` where gid='$gid'");
        $msql->query("delete from `$tb_z` where gid='$gid'");
        $msql->query("delete from `$tb_c` where gid='$gid'");
        echo 1;
        break;
    case "editgame":
        $sql = '';
        foreach ($_POST as $key => $val) {
            if ($key != 'action' & $key != 'gid' & $key != 'patt1' & $key != 'patt2' & $key != 'pan' & $key != 'ftype' & $key != 'cs' & $key != 'ztype' & $key != 'mtype' & $key != 'xtype') {
                $sql .= ',' . $key . "='" . $val . "'";
            }
            if ($key == 'pan' | $key == 'ftype' | $key == 'cs' | $key == 'ztype' | $key == 'mtype'  | $key == 'dftype') {
                $tmp = str_replace('\\', '', $val);
                $sql .= ',' . $key . "='" . $tmp . "'";
            }
        }
        $gid = $_POST['gid'];
        $sql = substr($sql, 1);
        $sql = "update `$tb_game` set $sql where gid='$gid'";
        $msql->query($sql);
        echo 1;
        break;
}
?>