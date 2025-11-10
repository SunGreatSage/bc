<?php
function addslashes_array($a)
{
    return is_array($a) ? array_map('addslashes_array', $a) : addslashes($a);
}
function acreplace($a)
{
    $b = array('delete', 'insert', 'select', 'update', 'from','chr', 'drop', 'exists', 'alter', 'create', 'truncate', 'or','%',' ','#','`','dual','sleep','DECLARE','char','where','SET','DELAY');
    $c = array('');
    $a = is_array($a) ? array_map('acreplace', $a) : str_ireplace($b, $c, $a);
    return $a;
}
if (!get_magic_quotes_gpc()) {
    if ($_POST) {
        $_POST = addslashes_array($_POST);
    }
    if ($_GET) {
        $_GET = addslashes_array($_GET);
    }
}
if ($_POST) {
    $_POST = acreplace($_POST);
}
if ($_GET) {
    $_GET = acreplace($_GET);
}