<?php
include('../data/comm.inc.php');
include('../data/mobivar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
include('../func/jsfunc.php');

$msql->query("select maxmoney,money,kmaxmoney,kmoney,pan,defaultpan,username,name,fastje,fudong from `{$tb_user}` where userid='{$userid}'");
$msql->next_record();
$tpl->assign('username', $msql->f('username') . '[' . $msql->f('name') . ']');
if ($msql->f('fudong') == 1) {
    $tpl->assign('maxmoney', $msql->f('kmaxmoney'));
    $tpl->assign('money', $msql->f('kmoney'));
    $tpl->assign('moneyuse', $msql->f('kmaxmoney') - $msql->f('kmoney'));
    $tpl->assign('synow', 0 - ($msql->f('kmaxmoney') - $msql->f('kmoney')));
} else {
   
        $tpl->assign('maxmoney', $msql->f('maxmoney'));
        $tpl->assign('money', $msql->f('money'));
        $tpl->assign('moneyuse', $msql->f('maxmoney') - $msql->f('money'));
        $tpl->assign('synow', 0 - ($msql->f('maxmoney') - $msql->f('money')));
    
        $tpl->assign('kmaxmoney', $msql->f('kmaxmoney'));
        $tpl->assign('kmoney', $msql->f('kmoney'));
        $tpl->assign('kmoneyuse', $msql->f('kmaxmoney') - $msql->f('kmoney'));
        $tpl->assign('ksynow', 0 - ($msql->f('kmaxmoney') - $msql->f('kmoney')));
    
}
$tpl->assign('fudong', $msql->f('fudong'));
$pan  = json_decode($msql->f('pan'), true);
$cpan = count($pan);
if ($cpan == 1) {
    $pan = $pan[0];
}
$tpl->assign('pan', $pan);
$tpl->assign('cpan', $cpan);
$tpl->assign('defaultpan', $msql->f('defaultpan'));
$gamecs = getgamecs($userid);
$gamecs = getgamename($gamecs);
$tpl->assign('gamecs', $gamecs);
$tpl->assign('webname', $config['webname']);
$tpl->assign('title', $config['webname'] . '-' . $msql->f('username') . '[' . $msql->f('name') . ']-' . $config['gname']);
$tpl->display('nav.html');
      