<?php
include '../data/comm.inc.php';
include '../data/mobivar.php';
include '../func/func.php';
include '../func/csfunc.php';
include '../func/userfunc.php';
include '../include.php';
include './checklogin.php';
switch ($_REQUEST['xtype']) {
    case "show":
        $date = $_REQUEST['date'];
        if (!preg_match("/\\d{4}-1[0-2]|0?[1-9]-0?[1-9]|[12][0-9]|3[01]/", $date)) {
            if (date("His") < str_replace(':', '', $config['editstart'])) {
                $date = date("Y-m-d", time() - 86400);
            } else {
                $date = date("Y-m-d");
            }
        }
$msql->query("SHOW TABLES LIKE  '%total%'");
$msql->next_record();
if($msql->f(0)=='x_lib_total'){
    $dates= getthisdate();
        if($date!=$dates){
            $tb_lib = "x_lib_".str_replace('-', '', $date); 
        }
}
  
        
        $page = r1p($_REQUEST['page']);
        $psize = $config['psize2'];
        $sqls = " and dates='$date' and z not in (2,9) and bs=1  ";
        $sqls2 = " and dates='$date' and z not in (2,7,9) and bs=1  ";   

        $total=[];

        $msql->query("select sum(je),sum(je*points/100),count(id) from `$tb_lib` where userid='$userid' $sqls2");
        $msql->next_record();
        $total['je'] = $msql->f(0);
        $total['points'] = pr1($msql->f(1));
        
        $msql->query("select count(id) from `$tb_lib` where userid='$userid' $sqls");
        $msql->next_record();
        $total['zs'] = pr0($msql->f(0));
        $rcount = $total['zs'] ;


        $fsql->query("select sum(je*peilv1),sum(prize) from `$tb_lib` where userid='$userid' $sqls and z=1");
        $fsql->next_record();
        $total['zhong'] = pr1($fsql->f(0))-pr1($fsql->f(1));
        $fsql->query("select sum(je*peilv2) from `$tb_lib` where userid='$userid' $sqls and z=3");
        $fsql->next_record();
        $total['zhong'] += pr1($fsql->f(0));

        $total['jg'] = pr1($total['zhong']+$total['points']-$total['je']);

        $pcount = $rcount%$psize==0 ? $rcount/$psize : ($rcount-$rcount%$psize)/$psize+1;
        if($page>$pcount) $page = $pcount;
        $sql = "select prize,qishu,je,peilv1,peilv2,points,content,gid,bid,sid,cid,pid,time,abcd,tid,z,prize from `{$tb_lib}` where userid='{$userid}' {$sqls} order by time desc,id desc limit ";
        $sql .= ($page - 1) * $psize . ',' . $psize;
        $msql->query($sql);
        $lib = array();
        $i = 0;
        $tmp = array();
        $zje = 0;
        $rs = 0;
        while ($msql->next_record()) {
            $lib[$i]['tid'] = $msql->f('tid');
            $lib[$i]['qishu'] = $msql->f('qishu');
            $lib[$i]['je'] = (float) $msql->f('je');
            $lib[$i]['peilv1'] = (float) $msql->f('peilv1');
            $lib[$i]['peilv2'] = (float) $msql->f('peilv2');
            $lib[$i]['points'] = (float) $msql->f('points');
            $lib[$i]['z'] = (float) $msql->f('z');
            //$lib[$i]['content'] = $msql->f('content');
            if ($tmp['g' . $msql->f('gid')] == '') {
            	$fsql->query("select gname,fenlei from `$tb_game` where gid='".$msql->f('gid')."'");
            	$fsql->next_record();
                $tmp['g' . $msql->f('gid')] = $fsql->f('gname');
                $tmp['f' . $msql->f('gid')] = $fsql->f('fenlei');
            }
            if ($tmp['b' . $msql->f('gid') . $msql->f('bid')] == '') {
                $tmp['b' . $msql->f('gid') . $msql->f('bid')] = transb8('name', $msql->f('bid'), $msql->f('gid'));
            }
            if ($tmp['s' . $msql->f('gid') . $msql->f('sid')] == '') {
                $tmp['s' . $msql->f('gid') . $msql->f('sid')] = transs8('name', $msql->f('sid'), $msql->f('gid'));
            }
            if ($tmp['c' . $msql->f('gid') . $msql->f('cid')] == '') {
                $tmp['c' . $msql->f('gid') . $msql->f('cid')] = transc8('name', $msql->f('cid'), $msql->f('gid'));
            }
            if ($tmp['p' . $msql->f('gid') . $msql->f('pid')] == '') {
                $tmp['p' . $msql->f('gid') . $msql->f('pid')] = transp8('name', $msql->f('pid'), $msql->f('gid'));
            }
            $lib[$i]['gid'] = $tmp['g' . $msql->f('gid')];
            $lib[$i]['wf'] = wfuser($tmp['f' . $msql->f('gid')], $tmp['b' . $msql->f('gid') . $msql->f('bid')], $tmp['s' . $msql->f('gid') . $msql->f('sid')], $tmp['c' . $msql->f('gid') . $msql->f('cid')], $tmp['p' . $msql->f('gid') . $msql->f('pid')]);
            if($msql->f('content')!=""){
                $lib[$i]['wf'] .= $msql->f('content');
            }
            
            $lib[$i]['abcd'] = $msql->f('abcd');
            $lib[$i]['time'] = substr($msql->f('time'), -8);
            $lib[$i]['date'] = substr($msql->f('time'), 5, 5);
            $lib[$i]['rs'] = (float) $msql->f('peilv1') * $msql->f('je');
            if ($msql->f('z') == 1) {
                $lib[$i]['rs'] = (float) ($msql->f('peilv1') * $msql->f('je') - $msql->f('je') * (1 - $msql->f('points') / 100));
            } else {
                if ($msql->f('z') == 3) {
                    $lib[$i]['rs'] = (float) ($msql->f('peilv2') * $msql->f('je') - $msql->f('je') * (1 - $msql->f('points') / 100));
                } else {
                    if ($msql->f('z') == 2) {
                        $lib[$i]['rs'] = 0;
                    } else {
                        if ($msql->f('z') == 5) {
                            $lib[$i]['rs'] = (float) ($msql->f('prize') - $msql->f('je') + $msql->f('je') * $msql->f('points') / 100);
                        } else {
                            $lib[$i]['rs'] = (float) (0 - $msql->f('je') * (1 - $msql->f('points') / 100));
                        }
                    }
                }
            }
            if($msql->f('z')!=7){
                $rs += $lib[$i]['rs'];
            $lib[$i]['rs'] = pr2($lib[$i]['rs']);
            $zje += $lib[$i]['je'];
                
            }
            
            $i++;
        }
        $lib[0]['total'] = $total;
        $lib[0]['zje'] = $zje;
        $lib[0]['rcount'] = $rcount;
        $lib[0]['zrs'] = pr2($rs);
        $lib[0]['psize'] = $psize;
        $lib[0]['page'] = $page;
        $lib[0]['pcount'] = $pcount;
        $lib[0]['dates'] = $date;
        //$lib[0]['sql'] = $sql;
        $lib[0]['week'] = rweek(date('w', strtotime($date)));
        echo json_encode($lib);
        unset($lib);
        break;
}