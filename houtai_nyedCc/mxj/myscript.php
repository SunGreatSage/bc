<?php
include('../data/comm.inc.php');include('../data/mobivar.php');
include('../func/func.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
if ($_POST['sf'] != '') {//var_dump("../js/" . $config['skins'] . '/js'.$config['mdi'].'/' . $_POST['sf'] . "user.js");die;
    echo file_get_contents("../js/" . $config['skins'] . '/js'.$config['mdi'].'/' . $_POST['sf'] . "user.js");
    exit;
}
?>