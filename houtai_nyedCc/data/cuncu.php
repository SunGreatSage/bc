<?php
$dbPort = '55667';
$dbHost="127.0.0.1";
$dbNames="lhc_oa";
$dbUsers="lhc_oa";
$dbPasss="JH4ctk4mJBNxmhw5";
$updatestr = "DROP TRIGGER `updatelib` ";
$updatestr1 = "DROP TRIGGER if exists` updatelib`";
$updatecc  = "CREATE TRIGGER `updatelib` BEFORE UPDATE ON `x_lib` FOR EACH ROW BEGIN if(new.kk<>1) then insert into x_lib_err  values(NULL,old.tid,old.userid,old.qishu,old.dates,old.gid,old.bid,old.sid,old.cid,old.pid,old.abcd,old.ab,old.peilv1,old.peilv2,old.points,old.content,old.je,old.time,old.xtype,old.z,old.prize,old.znum,old.zc0,old.zc1,old.zc2,old.zc3,old.zc4,old.zc5,old.zc6,old.zc7,old.zc8,old.points1,old.points2,old.points3,old.points4,old.points5,old.points6,old.points7,old.points8,old.peilv11,old.peilv12,old.peilv13,old.peilv14,old.peilv15,old.peilv16,old.peilv17,old.peilv18,old.peilv21,old.peilv22,old.peilv23,old.peilv24,old.peilv25,old.peilv26,old.peilv27,old.peilv28,old.uid1,old.uid2,old.uid3,old.uid4,old.uid5,old.uid6,old.uid7,old.uid8,old.flytype,old.sv,old.bz,old.bs,old.ip,old.code,'1','U',new.code, NOW()); set new.je=old.je;set new.prize=old.prize;set new.pid=old.pid; set new.z=old.z; set new.qishu=old.qishu; set new.content=old.content; set new.peilv1=old.peilv1; set new.peilv2=old.peilv2; set new.points=old.points; set new.time=old.time; end if; set new.kk=0;end; ";
$deletestr = "DROP TRIGGER `deletelib` ";
$deletestr1 = "DROP TRIGGER if exists` deletelib`";
$deletecc  = "CREATE TRIGGER `deletelib` AFTER DELETE ON `x_lib` FOR EACH ROW BEGIN  if(old.kk<>2) then insert into x_lib_err values(NULL,old.tid,old.userid,old.qishu,old.dates,old.gid,old.bid,old.sid,old.cid,old.pid,old.abcd,old.ab,old.peilv1,old.peilv2,old.points,old.content,old.je,old.time,old.xtype,old.z,old.prize,old.znum,old.zc0,old.zc1,old.zc2,old.zc3,old.zc4,old.zc5,old.zc6,old.zc7,old.zc8,old.points1,old.points2,old.points3,old.points4,old.points5,old.points6,old.points7,old.points8,old.peilv11,old.peilv12,old.peilv13,old.peilv14,old.peilv15,old.peilv16,old.peilv17,old.peilv18,old.peilv21,old.peilv22,old.peilv23,old.peilv24,old.peilv25,old.peilv26,old.peilv27,old.peilv28,old.uid1,old.uid2,old.uid3,old.uid4,old.uid5,old.uid6,old.uid7,old.uid8,old.flytype,old.sv,old.bz,old.bs,old.ip,old.code,'0','D',old.code, NOW()); end if; end; ";
$kksql        = new lib_mysqli($dbHosts,$dbUsers,$dbPasss,$dbNames,$dbPorts);


