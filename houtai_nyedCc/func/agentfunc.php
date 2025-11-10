<?php
function getuserid()
{
    global $tsql, $tb_user, $tb_admins;
    $tsql->query("select userid,fid,ifson from `$tb_user` where username='" . $_SESSION['ausername'] . "'");
    $tsql->next_record();
    if ($tsql->f('userid') != '' & $tsql->f('ifson') == 0)
        return $tsql->f('userid');
    if ($tsql->f('userid') != '' & $tsql->f('ifson') == 1)
        return $tsql->f('fid');
    $tsql->query("select adminid from `$tb_admins` where adminname='" . $_SESSION['username'] . "'");
    $tsql->next_record();
    if ($tsql->f('adminid') != '')
        return 99999999;
}
function getuserid2()
{
    global $tsql, $tb_user;
    $tsql->query("select userid from `$tb_user` where username='" . $_SESSION['ausername'] . "'");
    $tsql->next_record();
    return $tsql->f('userid');
}
?>