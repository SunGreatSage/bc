<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../include.php');
include('./checklogin.php');
if ($_POST['sf'] != '') {
	//if($_POST['sf']=='suser'){var_dump("../js/" . $config['skins'] . '/js' . $config['hdi'] . "/" . $_POST['sf'] . "myadmin.js");}
    echo file_get_contents("../js/" . $config['skins'] . '/js' . $config['hdi'] . "/" . $_POST['sf'] . "myadmin.js");
}
?>