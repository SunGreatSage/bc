<?php
include('../data/comm.inc.php');include('../data/myadminvar.php');
include('../func/func.php');
include('../func/agentfunc.php');
include('../include.php');
include('./checklogin.php');
if ($_POST['sf'] != '') {
    echo file_get_contents("../js/" . $config['skins'] . '/js' . $config['adi'] . "/" . $_POST['sf'] . $config['adi'] . ".js");
    exit;
}
?>