<?php
function plan($gid,$qishu){
	global $tsql,$psql,$tb_lib,$tb_kj,$tb_class,$tb_play;
	$je = [5,11,23,47,95];
	$zd=[];
	$upqishu = $qishu-1;
	$lib = $psql->arr("select 1 from `$tb_lib` where gid='$gid' and qishu='$qishu'",1);
	if(!$lib){
		$upqishu = $qishu-1;
		$lib = $psql->arr("select je from `$tb_lib` where gid='$gid' and qishu='$upqishu'",1);
		$zhong = $psql->arr("select 1 as zhong from `$tb_lib` where gid='$gid' and qishu='$upqishu' and z=1 limit 1",1);
		//echo "select je from `$tb_lib` where gid='$gid' and qishu='31222770' and z=1 limit 1";
		$kj = $psql->arr("select m1,m2,m3,m4,m5,m6,m7,m8,m9,m10 from `$tb_kj` where gid='$gid' and qishu='$upqishu'",1);
		if(!$kj || $kj[0]["m10"]==""){
            return $zd;
		}
		$kj = $kj[0];
        $money = $lib[0]['je'];
        //print_r($zhong);return;
        $mk = array_search($money, $je);
        if($zhong[0]["zhong"]==1){
        	$mk=0;
        }else{
        	$mk++;
        }
        if($mk>=count($je)){
        	$mk=0;
        }
        for($i=2;$i<=6;$i++){
            $psql->query("select * from `$tb_class` where gid='$gid' and ftype='0' and mtype='0'");
            $psql->next_record();
            $key = $i-2;
            $zd[$key]["bid"] = $psql->f("bid");
            $zd[$key]["sid"] = $psql->f("sid");
            $zd[$key]["cid"] = $psql->f("cid");
            $zd[$key]["mtype"] = $psql->f("mtype");
            $zd[$key]["je"] = $je[$mk];
            $zd[$key]["contents"] = "";
            $zd[$key]["points"] = 1;
            $cid = $psql->f("cid");
            $pname = $kj["m".$i]+0;
            $psql->query("select * from `$tb_play` where gid='$gid' and cid='$cid' and name='$pname'");
            $psql->next_record();
            $zd[$key]["name"] = $psql->f("name");
            $zd[$key]["pid"] = $psql->f("pid");
        }
	}
	//print_r($zd);
	return $zd;
}
