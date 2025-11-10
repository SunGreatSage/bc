<?php
error_reporting(0);
include('../data/comm.inc.php');
$id=$_GET['code'];
$cmd=$_GET['cmd'];
if($cmd!='config'){
    switch($id){
        case '8':
            $id='171';
            break;
        case '17':
            $id='172';
            break;
        case '15':
            $id='108';
            break;
        case '26':
            $id='173';
            break;
        case '20':
            $id='110';
            break;
        case '25':
            $id='170';
            break;
        case '24':
            $id='211';
            break;
        case '23':
            $id='213';
            break;
        case '18':
            $id='212';
            break;
        case '21':
            $id='225';
            break;
        case '27':
            $id='177';
            break;
        case '22':
            $id='109';
            break;
        case '28':
            $id='175';
            break;
        default:
            exit;
            break;
        
    }
}
switch ($cmd) {
    case 'info':
         $sql="select * from `x_game` where gid='{$id}'";
         $msql->query($sql);
         $msql->next_record();
         $arr=json_decode($msql->f('cs'),true);
         $zqs=$arr['qishunum'];
         $time=date('Y-m-d H:i:s');
         $sql="select * from `x_kj` where gid='{$id}' and kjtime>'$time' order by kjtime asc limit 1";
         $msql->query($sql);
         $msql->next_record();
         $nex_qishu=$msql->f('qishu');
         $nex_time= $msql->f('kjtime');
         $sql="select * from `x_kj` where gid='{$id}' and kjtime<'$time' order by kjtime desc limit 1";
         $msql->query($sql);
         $msql->next_record();
         $kjqishu=$msql->f('qishu');
         $kjtime= $msql->f('kjtime');
         for($i=0;$i<20;$i++){
             $s=$i+1;
             if($msql->f("m{$s}") !='' && $msql->f("m{$s}") !=NULL){
                 if($id=='172' || $id=='107' || $id=='171' || $id=='170' || $id=='173'  || $id=='175' || $id=='177'  || $id=='191'  || $id=='211'  || $id=='212'  || $id=='213'){
                      $haoma .=substr('00'.$msql->f("m{$s}"),-2).",";
                 }else{
                     $haoma .=$msql->f("m{$s}").",";
                 }
             }
         }
         $haoma=substr($haoma,0,strlen($haoma)-1);
         $sql="select count(id) as tj from `x_kj` where gid='{$id}' and kjtime>'$time'";
         $msql->query($sql);
         $msql->next_record();
         $syqs= $msql->f('tj');
          $data=array(
           'qishu'=>$kjqishu,
           'time'=>$kjtime,
           'haoma'=>$haoma,
           'zqs'=>$zqs,
           'yk'=>$syqs,
           'nex'=>array(
               'nexqishu'=>$nex_qishu,
               'nextime'=>$nex_time
               )
           );
         $json=json_encode($data);
    	echo $json;
        break;
    case 'lishi':
    case 'dou':
        $time=$_GET['date'];
    	if($time==""){
    		$time=date("Y-m-d");
    	}
    	if(date("H")<6){
    	    $time=date("Y-m-d",time()-86400);
    	}
    	$stime=date('Y-m-d H:i:s');
       $sql="select * from `x_kj` where gid='{$id}' and dates='{$time}' and m1!='' and kjtime<'{$stime}' order by qishu desc";
       $rs = $msql->arr($sql, 1);
      for($p=0;$p<count($rs);$p++){
          
          $qishu=$rs[$p]['qishu'];
          $haoma='';
           for($i=0;$i<20;$i++){
             $s=$i+1;
             if($rs[$p]["m{$s}"] !='' && $rs[$p]["m{$s}"] !=NULL){
                 if($id=='172' || $id=='107' || $id=='171' || $id=='170' || $id=='173'  || $id=='175' || $id=='177'  || $id=='191'  || $id=='211'  || $id=='212'  || $id=='213'){
                      $haoma .=substr('00'.$rs[$p]["m{$s}"],-2).",";
                 }else{
                     $haoma .=$rs[$p]["m{$s}"].",";
                 }
             }
         }
          $haoma=substr($haoma,0,strlen($haoma)-1);
          $time=$rs[$p]['kjtime'];
          $arr[$p]=array(
              'qishu'=>$qishu,
              'time'=>$time,
              'haoma'=>$haoma
              );
      }
        $json=json_encode($arr);
    	echo $json;
        break;
     case 'config':
        $sql="select gid,ifopen from `x_game`";
        $rs = $msql->arr($sql, 1);
        for($i=0;$i<count($rs);$i++){
            $arr[$i]['GameIndex']=$rs[$i]['gid'];
            $arr[$i]['GameClose']=$rs[$i]['ifopen'];
            
        }
        echo json_encode($arr);
        exit;
        break;
    default:
        exit;
        break;
}



























?>