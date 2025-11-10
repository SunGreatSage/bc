<?php  

function getuserid(){ 	global $tsql,$tb_user; 	$tsql->query("select userid,fid,ifson from `$tb_user` where username='".$_SESSION['uusername']."'"); 	$tsql->next_record(); 	if($tsql->f('userid')!='' & $tsql->f('ifson')==0 &  $tsql->f('ifagent')==0) return $tsql->f('userid');   } 

function getlibje($gid, $qs) {
    global $tb_lib, $psql;
    $rs = $psql->arr("select count(id),sum(je),sum(je*zc0/100) from `$tb_lib` where gid='$gid' and qishu='$qs' and xtype!=2", 0);
    $r2 = $psql->arr("select count(id),sum(je) from `$tb_lib` where gid='$gid' and qishu='$qs' and userid=99999999", 0);
    return array(
        pr0($rs[0][0]) ,
        pr0($rs[0][1]) ,
        pr0($rs[0][2]),
        pr0($r2[0][0]) ,
        pr0($r2[0][1]) 
    );
}



  ?>