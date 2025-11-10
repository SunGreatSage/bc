<?php
$SESS_DBH  = "";
function sess_open($save_path, $session_name)
{
    global $dbHost, $dbName, $dbUser, $dbPass, $SESS_DBH;
    if (!$SESS_DBH = mysql_connect($dbHost, $dbUser, $dbPass)) {
        echo "<li>MySql Error:" . mysql_error() . "<li>";
        die();
    }
    if (!mysql_select_db($dbName, $SESS_DBH)) {
        echo "<li>MySql Error:" . mysql_error() . "<li>";
        die();
    }
    return true;
}

function sess_close()
{
    return true;
}

function sess_read($key)
{
    global $SESS_DBH, $SESS_LIFE;
    $qry = "select value from `x_session` where sesskey = '$key' and expiry > " . time();
    $qid = mysql_query($qry, $SESS_DBH);
    if (list($value) = mysql_fetch_row($qid)) {
        return $value;
    }
    return false;
}

function sess_write($key, $val)
{
    global $SESS_DBH, $SESS_LIFE;
    $expiry = time() + $SESS_LIFE;
    $value  = $val;
    $qry    = "insert into `x_session` values('$key',$expiry,'$value')";
    $qid    = mysql_query($qry, $SESS_DBH);
    if (!$qid) {
        $qry = "update `x_session` set expiry=$expiry, value='$value' where sesskey='$key' and expiry >" . time();
        $qid = mysql_query($qry, $SESS_DBH);
    }
    return $qid;
}

function sess_destroy($key)
{
    global $SESS_DBH;
    $qry = "delete from `x_session` where sesskey = '$key'";
    $qid = mysql_query($qry, $SESS_DBH);
    return $qid;
}

function sess_gc($maxlifetime)
{
    global $SESS_DBH;
    $qry = "delete from `x_session` where expiry < " . time();
    $qid = mysql_query($qry, $SESS_DBH);
    return mysql_affected_rows($SESS_DBH);
}
session_module_name();
session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
session_start();
?>