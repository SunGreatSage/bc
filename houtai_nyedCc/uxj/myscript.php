<?php
include('../data/comm.inc.php');include('../data/uservar.php');
include('../func/func.php');
include('../func/userfunc.php');
include('../include.php');
include('./checklogin.php');
if ($_POST['sf'] != '') {
    echo file_get_contents("../js/" . $config['skins'] . '/js' . $config['udi'] . "/" . $_POST['sf'] . "user.js");
    exit;
}
?>