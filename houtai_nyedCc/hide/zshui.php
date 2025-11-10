 <?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/csfunc.php');
include('../func/adminfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
	    if(in_array($_REQUEST['gid'],$garr)){
	       $gid= $_REQUEST['gid'];
		}
        $game = getgamecs($userid);
        $game = getgamename($game);
        $zpan = json_decode(transgame($gid,'pan'),true);
		$dftype = json_decode(transgame($gid,'dftype'),true);
		$fenlei = transgame($gid,'fenlei');
		//echo $gid;
		foreach($dftype as $k => $v){
		    $zpan[$k]['class'] = $k;
			$zpan[$k]['name'] = $v;
			$cs                   = getjes8($k, $userid,$gid);
            $zpan[$k]['cmaxje']   = echousercs('cmaxje' . $k, $cs['cmaxje'], $cs['cmaxje']);
            $zpan[$k]['maxje']    = echousercs('maxje' . $k, $cs['maxje'], $cs['maxje']);
            $zpan[$k]['minje']    = echousercs('minje' . $k, $cs['minje'], $cs['minje']);   
			$zpan[$k]['cs'] = array();
			foreach($ftype as $key => $val){
			    if($val['bc']==$zpan[$k]['class']){
				    $zpan[$k]['cs'][$key]['class'] = $val['class'];
					$zpan[$k]['cs'][$key]['name'] = $val['name'];
					$zpan[$k]['cs'][$key]['bc'] = $val['bc'];
					$cs                   = getzcs8($val['class'], $userid,$gid);
					$zpan[$k]['cs'][$key]['lowpeilv'] = echousercs('lowpeilv' . $val['class'], $cs['lowpeilv'], $cs['lowpeilv']);
					$zpan[$k]['cs'][$key]['peilvcha'] = peilvchaselecttop8('peilvcha' . $val['class'], $val['class'],$gid);
				}
			}  
			       
            if ($zpan[$k]['abcd'] == 1) {
                if ($zpan[$k]['ab'] == 1) {
                    $zpan[$k]['pointsaa'] = pointsselecttop8('pointsaa', $userid, $k, 'A', 'A', $userid,$gid);
                    $zpan[$k]['pointsab'] = pointsselecttop8('pointsab', $userid, $k, 'A', 'B', $userid,$gid);
                    $zpan[$k]['pointsba'] = pointsselecttop8('pointsba', $userid, $k, 'B', 'A', $userid,$gid);
                    $zpan[$k]['pointsbb'] = pointsselecttop8('pointsbb', $userid, $k, 'B', 'B', $userid,$gid);
                    $zpan[$k]['pointsca'] = pointsselecttop8('pointsca', $userid, $k, 'C', 'A', $userid,$gid);
                    $zpan[$k]['pointscb'] = pointsselecttop8('pointscb', $userid, $k, 'C', 'B', $userid,$gid);
                    $zpan[$k]['pointsda'] = pointsselecttop8('pointsda', $userid, $k, 'D', 'A', $userid,$gid);
                    $zpan[$k]['pointsdb'] = pointsselecttop8('pointsdb', $userid, $k, 'D', 'B', $userid,$gid);
                } else {
					
                    $zpan[$k]['pointsa0'] = pointsselecttop8('pointsa0', $userid, $k, 'A', '0', $userid,$gid);
                    $zpan[$k]['pointsb0'] = pointsselecttop8('pointsb0', $userid, $k, 'B', '0', $userid,$gid);
                    $zpan[$k]['pointsc0'] = pointsselecttop8('pointsc0', $userid, $k, 'C', '0', $userid,$gid);
                    $zpan[$k]['pointsd0'] = pointsselecttop8('pointsd0', $userid, $k, 'D', '0', $userid,$gid);
                }
            } else {
                $zpan[$k]['pointsa0'] = pointsselecttop8('pointsa0', $userid, $k, 'A', '0', $userid,$gid);
            }
			
			
		}

        $tpl->assign("zpan", $zpan);
		$tpl->assign("game", $game);
		$tpl->assign("flname", transgame($gid,'flname'));
		$tpl->assign("gid", $gid);
		$tpl->assign("config", $config);
        $tpl->display("zshui.html");
        break;
    case "setpoints":
	    $gid =$_POST['gid'];
		$downline =$_POST['downline'];
		//var_dump($downline);
        $zpan = json_decode(transgame($gid,'pan'),true);
		$dftype = json_decode(transgame($gid,'dftype'),true);
		$ftype = json_decode(transgame($gid,'ftype'),true);
		$fenlei = transgame($gid,'fenlei');
		foreach($ftype as $k => $v){
			$msql->query("delete from `$tb_zpan` where gid='$gid' and userid=99999999 and class='".$v['class']."'");
		    $sql = "insert into `$tb_zpan` set gid='$gid',class='".$v['class']."',userid=99999999,lowpeilv=0,peilvcha=0";
			$msql->query($sql);			
		}
		//先同步到备份表
		$sql = "UPDATE $tb_points_og AS og
        INNER JOIN $tb_points AS p ON og.class = p.class AND og.ab = p.ab
        SET og.a = p.a, og.b = p.b, og.c = p.c, og.d = p.d, og.cmaxje = p.cmaxje, og.maxje = p.maxje
        WHERE p.userid = $userid AND p.gid = $gid AND og.userid = $userid AND og.gid = $gid";
		//var_dump($sql);
		$msql->query($sql);
        foreach($dftype as $k => $v){  
		    $ifnew = 1;          
            $fsql->query("select 0 from `$tb_points` where gid='$gid' and userid='$userid' and class='" . $k . "'");
            if ($fsql->next_record()) {
                $ifnew = $fsql->f(0);
            }
            $cmaxje   = pr0($_POST['cmaxje' . $k]);
            $maxje    = pr0($_POST['maxje' . $k]);
            $minje    = pr0($_POST['minje' . $k]);

            if ($zpan[$k]['abcd'] == 1) {
                if ($zpan[$k]['ab'] == 1) {
                    $a = $_POST['pointsaa_' . $k];
					$b = $_POST['pointsba_' . $k];
					$c = $_POST['pointsca_' . $k];
					$d = $_POST['pointsda_' . $k];
					
                    $msql->query(" delete from `$tb_points` where gid='$gid' and userid='$userid' and class='" . $k . "' and ab='A'");
                    $sql = "insert into `$tb_points` set gid='$gid',a='$a',b='$b',c='$c',d='$d',ab='A',userid='$userid',class='" . $k . "',cmaxje='$cmaxje',maxje='$maxje',minje='$minje'";
                    $msql->query($sql);
					
                    $a = $_POST['pointsab_' . $k];
					$b = $_POST['pointsbb_' . $k];
					$c = $_POST['pointscb_' . $k];
					$d = $_POST['pointsdb_' . $k];
					
                    $msql->query(" delete from `$tb_points` where gid='$gid' and userid='$userid' and class='" . $k . "' and ab='B'");
                    $sql = "insert into `$tb_points` set gid='$gid',a='$a',b='$b',c='$c',d='$d',ab='B',userid='$userid',class='" . $k . "',cmaxje='$cmaxje',maxje='$maxje',minje='$minje'";
                    $msql->query($sql);

                } else {
                    $a = $_POST['pointsa0_' . $k];
					$b = $_POST['pointsb0_' . $k];
					$c = $_POST['pointsc0_' . $k];
					$d = $_POST['pointsd0_' . $k];
					
                    $msql->query(" delete from `$tb_points` where gid='$gid' and userid='$userid' and class='" . $k . "' and ab='0'");
                    $sql = "insert into `$tb_points` set gid='$gid',a='$a',b='$b',c='$c',d='$d',ab='0',userid='$userid',class='" . $k . "',cmaxje='$cmaxje',maxje='$maxje',minje='$minje'";
                    $msql->query($sql);
                }
            } else {
                $a = $_POST['pointsa0_' . $k];
                $msql->query(" delete from `$tb_points` where gid='$gid' and userid='$userid' and class='" . $k . "' and ab='0'");
                $sql = "insert into `$tb_points` set gid='$gid',a='$a',ab='0',userid='$userid',class='" . $k . "',cmaxje='$cmaxje',maxje='$maxje',minje='$minje'";
                $msql->query($sql);
            }
            if ($ifnew == 1 & $zpan[$k]['ab'] != 1) {
                $fsql->f("delete from  `$tb_points_bak` ");
                $fsql->query("insert into  `$tb_points_bak` select * from `$tb_points` where gid='$gid' and userid='$userid' and class='" . $k . "'");
                $fsql->query("select * from  `$tb_points` where  userid!=$userid group by userid");
                while ($fsql->next_record()) {
                    $tsql->query("delete from `$tb_points` where gid='$gid' and userid='" . $fsql->f('userid') . "' and class='" . $k . "'");
                    $tsql->query("insert into `$tb_points` select NULL,gid," . $fsql->f('userid') . ",'" . $k . "',ab,a,b,c,d,cmaxje,maxje,minje from `$tb_points_bak`");
                }
            }
        }
      if ($downline == 2) {
$sql = "UPDATE $tb_points AS a
        INNER JOIN $tb_points AS b ON a.class = b.class AND a.ab = b.ab AND a.gid = b.gid
        SET a.a = b.a, a.b = b.b, a.c = b.c, a.d = b.d, a.cmaxje = b.cmaxje, a.maxje = b.maxje
        WHERE a.userid <> $userid AND b.userid = $userid AND a.gid = $gid AND b.gid = $gid";
 $msql->query($sql);
		   }
	  if ($downline == 3) {
$user_points_query = "SELECT class, ab, SUM(a) AS a, SUM(b) AS b, SUM(c) AS c, SUM(d) AS d, SUM(cmaxje) AS cmaxje, SUM(maxje) AS maxje FROM $tb_points WHERE userid = $userid AND gid = $gid GROUP BY class, ab";
$user_points = $msql->query($user_points_query)->fetch_all(MYSQLI_ASSOC);
$og_points_query = "SELECT class, ab, SUM(a) AS a, SUM(b) AS b, SUM(c) AS c, SUM(d) AS d, SUM(cmaxje) AS cmaxje, SUM(maxje) AS maxje FROM $tb_points_og WHERE userid = $userid AND gid = $gid GROUP BY class, ab";
$og_points = $msql->query($og_points_query)->fetch_all(MYSQLI_ASSOC);
foreach ($user_points as $key => $user_point) {
    $class = $user_point['class'];
    $ab = $user_point['ab'];
    $add_a = $user_point['a'] - $og_points[$key]['a'];
    $add_b = $user_point['b'] - $og_points[$key]['b'];
    $add_c = $user_point['c'] - $og_points[$key]['c'];
    $add_d = $user_point['d'] - $og_points[$key]['d'];
    $add_cmaxje = $user_point['cmaxje'] - $og_points[$key]['cmaxje'];
    $add_maxje = $user_point['maxje'] - $og_points[$key]['maxje'];
    $sql = "UPDATE $tb_points SET a = a + $add_a, b = b + $add_b, c = c + $add_c, d = d + $add_d, cmaxje = cmaxje + $add_cmaxje, maxje = maxje + $add_maxje WHERE userid <> $userid AND gid = $gid AND class = '$class' AND ab = '$ab'";
    $msql->query($sql);
}
		   }
	   
        echo 1;
        break;
	case "yiwotongbuzshui":
         $gid = $_POST['gid'];	
		 $msql->query("select gid from `$tb_game` where fenlei=(select fenlei from `$tb_game` where gid='$gid') and gid!='$gid'");
		 while($msql->next_record()){
			 $ngid = $msql->f('gid');
			 $fsql->query("delete from `$tb_points` where gid='$ngid' and userid='$userid'");
			 $fsql->query("insert into `$tb_points` select NULL,$ngid,$userid,class,ab,a,b,c,d,cmaxje,maxje,minje from `$tb_points` where gid='$gid' and userid='$userid'");	
		 }
        echo 1;
	break;
    case "setattshow":
	    if(in_array($_REQUEST['gid'],$garr)){
	       $gid= $_REQUEST['gid'];
		}
        $game = getgamecs($userid);
        $game = getgamename($game);
        $msql->query("select patt1,patt2,patt3,patt4,patt5,ftype,dftype from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $patt[] = json_decode($msql->f('patt1'), true);
        $patt[] = json_decode($msql->f('patt2'), true);
		$patt[] = json_decode($msql->f('patt3'), true);
		$patt[] = json_decode($msql->f('patt4'), true);
		$patt[] = json_decode($msql->f('patt5'), true);
			
		$dftype = json_decode($msql->f('dftype'), true);		
		$ftype = json_decode($msql->f('ftype'), true);
		
        foreach ($ftype as $k => $v) {
            $cs[$k]['pointsatt']  = echoinput('pointsatt' . $k, transatt8($k, 'pointsatt',$gid));
			$cs[$k]['points']  = echoinput('points' . $k, transatt8($k, 'points',$gid));
            $cs[$k]['peilvatt']  = echoinput('peilvatt' . $k, transatt8($k, 'peilvatt',$gid));
			$cs[$k]['peilvatt1']  = echoinput('peilvatt_1' . $k, transatt8($k, 'peilvatt1',$gid));
            $cs[$k]['maxatt']    = echoinput('maxatt' . $k, transatt8($k, 'maxatt',$gid));
            $cs[$k]['flypeilv']  = echoinput('flypeilv' . $k, transatt8($k, 'flypeilv',$gid));
			$cs[$k]['flyifok']  = echoinput('flyifok' . $k, transatt8($k, 'flyifok',$gid));			
			$cs[$k]['name']  = $v['name'];
			$cs[$k]['bcname']  = $dftype[$v['bc']];
			$cs[$k]['class']  = $v['class'];
			
			for($j=1;$j<=5;$j++){
             $cs[$k]['a'.$j]         = echoinput('a'.$j .'_'. $k, $patt[$j-1][$k]['a'], 'a'.$j);
            $cs[$k]['b'.$j]         = echoinput('b'.$j .'_'. $k, $patt[$j-1][$k]['b'], 'b'.$j);
            $cs[$k]['c'.$j]         = echoinput('c'.$j .'_'.$k, $patt[$j-1][$k]['c'], 'c'.$j);
            $cs[$k]['d'.$j]         = echoinput('d'.$j.'_'. $k, $patt[$j-1][$k]['d'], 'd'.$j);
            $cs[$k]['ab'.$j]        = echoinput('ab'.$j .'_'. $k, $patt[$j-1][$k]['ab'], 'ab'.$j);
			}
			
        }
        $tpl->assign("game", $game);
		$tpl->assign("dpan", $dpan);
		$tpl->assign("cs", $cs);
		$tpl->assign("flname", transgame($gid,'flname'));
		$tpl->assign("gid", $gid);
		$tpl->assign("config", $config);
        $tpl->display("setatt.html");
        break;
    case "setatt":
	    $gid = $_POST['gid'];

        $msql->query("select patt1,patt2,patt3,patt4,patt5,ftype,dftype from `$tb_game` where gid='$gid'");		
        $msql->next_record();
        $dftype = json_decode($msql->f('dftype'), true);
		$ftype = json_decode($msql->f('ftype'), true);

		
		$cf = count($ftype);
        for ($i = 0; $i < $cf; $i++) {
            $pointsatt  = $_POST['pointsatt' . $ftype[$i]['class']];
			$points  = $_POST['points' . $ftype[$i]['class']];
            $peilvatt  = $_POST['peilvatt' . $ftype[$i]['class']];
			$peilvatt1  = $_POST['peilvatt_1' . $ftype[$i]['class']];
            $maxatt    = $_POST['maxatt' . $ftype[$i]['class']];
			$flypeilv    = $_POST['flypeilv' . $ftype[$i]['class']];
			$flyifok    = $_POST['flyifok' . $ftype[$i]['class']];
			
			$a1         = $_POST['a1' .'_'. $ftype[$i]['class']];
            $b1         = $_POST['b1' .'_'. $ftype[$i]['class']];
            $c1        = $_POST['c1' .'_'. $ftype[$i]['class']];
            $xd1        = $_POST['d1' .'_'. $ftype[$i]['class']];
            $ab1        = $_POST['ab1' .'_'. $ftype[$i]['class']];
			
			$a2         = $_POST['a2' .'_'. $ftype[$i]['class']];
            $b2         = $_POST['b2' .'_'. $ftype[$i]['class']];
            $c2         = $_POST['c2' .'_'. $ftype[$i]['class']];
            $xd2        = $_POST['d2' .'_'. $ftype[$i]['class']];
            $ab2        = $_POST['ab2' .'_'. $ftype[$i]['class']];
			
			$a3         = $_POST['a3' .'_'. $ftype[$i]['class']];
            $b3         = $_POST['b3' .'_'. $ftype[$i]['class']];
            $c3         = $_POST['c3' .'_'. $ftype[$i]['class']];
            $xd3        = $_POST['d3' .'_'. $ftype[$i]['class']];
            $ab3        = $_POST['ab3' .'_'. $ftype[$i]['class']];
			
			$a4         = $_POST['a4' .'_'. $ftype[$i]['class']];
            $b4         = $_POST['b4' .'_'. $ftype[$i]['class']];
            $c4         = $_POST['c4' .'_'. $ftype[$i]['class']];
            $xd4        = $_POST['d4' .'_'. $ftype[$i]['class']];
            $ab4        = $_POST['ab4' .'_'. $ftype[$i]['class']];
			
			$a5         = $_POST['a5' .'_'. $ftype[$i]['class']];
            $b5        = $_POST['b5' .'_'. $ftype[$i]['class']];
            $c5         = $_POST['c5' .'_'. $ftype[$i]['class']];
            $xd5        = $_POST['d5' .'_'. $ftype[$i]['class']];
            $ab5        = $_POST['ab5' .'_'. $ftype[$i]['class']];
			
			
            $msql->query("delete from `$tb_att` where class='" . $ftype[$i]['class'] . "' and gid='$gid'");
            $sql = " insert into  `$tb_att` set  points='$points',pointsatt='$pointsatt',peilvatt='$peilvatt',peilvatt1='$peilvatt1',class='" . $ftype[$i]['class'] . "',bc='" . $ftype[$i]['bc'] . "',maxatt='$maxatt',flypeilv='$flypeilv',flyifok='$flyifok',gid='$gid'";
            $msql->query($sql);
            $patt1[$i]['a']  = $a1;
            $patt1[$i]['b']  = $b1;
            $patt1[$i]['c']  = $c1;
            $patt1[$i]['d']  = $xd1;
            $patt1[$i]['ab'] = $ab1;
			$patt1[$i]['peilvatt'] = $peilvatt;
			$patt1[$i]['peilvatt1'] = $peilvatt1;
			$patt1[$i]['maxatt'] = $maxatt;
			$patt1[$i]['flypeilv'] = $flypeilv;
			$patt1[$i]['flyifok'] = $flyifok;
			
            $patt2[$i]['a']  = $a2;
            $patt2[$i]['b']  = $b2;
            $patt2[$i]['c']  = $c2;
            $patt2[$i]['d']  = $xd2;
            $patt2[$i]['ab'] = $ab2;
			$patt2[$i]['peilvatt'] = $peilvatt;
			$patt2[$i]['peilvatt1'] = $peilvatt1;
			$patt2[$i]['maxatt'] = $maxatt;
			$patt2[$i]['flypeilv'] = $flypeilv;
			$patt2[$i]['flyifok'] = $flyifok;
			
            $patt3[$i]['a']  = $a3;
            $patt3[$i]['b']  = $b3;
            $patt3[$i]['c']  = $c3;
            $patt3[$i]['d']  = $xd3;
            $patt3[$i]['ab'] = $ab3;
			$patt3[$i]['peilvatt'] = $peilvatt;
			$patt3[$i]['peilvatt1'] = $peilvatt1;
			$patt3[$i]['maxatt'] = $maxatt;
			$patt3[$i]['flypeilv'] = $flypeilv;
			$patt3[$i]['flyifok'] = $flyifok;
			
            $patt4[$i]['a']  = $a4;
            $patt4[$i]['b']  = $b4;
            $patt4[$i]['c']  = $c4;
            $patt4[$i]['d']  = $xd4;
            $patt4[$i]['ab'] = $ab4;
			$patt4[$i]['peilvatt'] = $peilvatt;
			$patt4[$i]['peilvatt1'] = $peilvatt1;
			$patt4[$i]['maxatt'] = $maxatt;
			$patt4[$i]['flypeilv'] = $flypeilv;
			$patt4[$i]['flyifok'] = $flyifok;
			
            $patt5[$i]['a']  = $a5;
            $patt5[$i]['b']  = $b5;
            $patt5[$i]['c']  = $c5;
            $patt5[$i]['d']  = $xd5;
            $patt5[$i]['ab'] = $ab5;
			
			$patt5[$i]['peilvatt'] = $peilvatt;
			$patt5[$i]['peilvatt1'] = $peilvatt1;
			$patt5[$i]['maxatt'] = $maxatt;
			$patt5[$i]['flypeilv'] = $flypeilv;
			$patt5[$i]['flyifok'] = $flyifok;
			
        }
        $pan1 = json_encode($patt1);
		$pan2 = json_encode($patt2);
		$pan3 = json_encode($patt3);
		$pan4 = json_encode($patt3);
		$pan5 = json_encode($patt3);
        $msql->query("update `$tb_game` set patt1='$pan1',patt2='$pan2',patt3='$pan3',patt4='$pan4',patt5='$pan5' where gid='$gid'");
        echo 1;
        break;
    case "yiwotongbuatt":
         $gid = $_POST['gid'];
		 $msql->query("select patt1,patt2,patt3,patt4,patt5,mtype,ftype,ztype,pan,dftype from `$tb_game` where gid='$gid'");
		 $msql->next_record();
		 $patt1= $msql->f('patt1');
		 $patt2= $msql->f('patt2');
		 $patt3= $msql->f('patt3');
		 $patt4= $msql->f('patt4');
		 $patt5= $msql->f('patt5');
		 $pan= $msql->f('pan');
		 $ztype= $msql->f('ztype');
		 $mtype= $msql->f('mtype');
		 $ftype= $msql->f('ftype');
		 $dftype= $msql->f('dftype');
		 $dpan = $msql->f('dpan');
	
		 $msql->query("select gid from `$tb_game` where fenlei=(select fenlei from `$tb_game` where gid='$gid') and gid!='$gid'");
		 while($msql->next_record()){
			 $ngid = $msql->f('gid');
			 $fsql->query("delete from `$tb_att` where gid='$ngid'");
			 $fsql->query("insert into `$tb_att` select NULL,$ngid,bc,class,points,pointsatt,peilvatt,maxatt,peilvatt1,flypeilv,flyifok from `$tb_att` where gid='$gid'");
			 $fsql->query("update `$tb_game` set patt1='$patt1',patt2='$patt2',patt3='$patt3',patt4='$patt4',patt5='$patt5',mtype='$mtype',ztype='$ztype',ftype='$ftype',pan='$pan',dftype='$dftype' where gid='$ngid'");
		 }
        echo 1;
        break;
		case "ma":
	    $msql->query("select ma from `$tb_config`");
		$msql->next_record();
		$ma = json_decode($msql->f('ma'),true);
		$m=array();
		foreach($ma as $key =>$val){
		   foreach($val as  $k =>$v){
			   $m[$k] = $v;
		   }
		}
         
        $tpl->assign("ma", $m);
		$tpl->assign('nums', floor(count($m)/2));
        $tpl->display("editma.html");
        break;
    case "editma":
        $arr= str_replace('\\','',$_POST['str']);
	    $arr = json_decode($arr,true);
		$parr = array("五行"=>array("金","木","水","火","土"),"内外围"=>array("内围","外围"),"單雙"=>array("單","雙"),"大小"=>array("大","小"),"合單雙"=>array("合單","合雙"),"尾大小"=>array("尾大","尾小"),"合大小"=>array("合大","合小"),"合尾大小"=>array("合尾大","合尾小"),"波色"=>array("紅","藍","綠"),"半波"=>array("紅單","紅雙","藍單","藍雙","綠單","綠雙","紅大","紅小","藍大","藍小","綠大","綠小"),"生肖"=>array("鼠","牛","虎","兔","龍","蛇","馬","羊","猴","雞","狗","豬"),"家野"=>array("家畜","野獸"),"前後"=>array("前","後"),"头数"=>array("0头","1头","2头","3头","4头"),"尾数"=>array("0尾","1尾","2尾","3尾","4尾","5尾","6尾","7尾","8尾","9尾"));
		$new = array();
		foreach($parr as $k => $v){
		  foreach($v as $v1){	
		  
		   $tmp = explode(',',$arr[$v1]);		   
		   if(checkma($tmp)){
		       $new[$k][$v1] = $arr[$v1];
		   }
		  }
		}
		
		$str = '{';
		$i=0;
		foreach($new as $key => $val){
		   if($i>0) $str .= ',';
		   $str .= '"'.$key.'":{';
		   $j=0;
		   foreach($val as $k => $v){
			   if($j>0) $str .= ',';
		      $str .= '"'.$k.'":'.'"'.$v.'"';
			  $j++;
		   }
		   $str .= "}";
		   $i++;
		}
		$str .= '}';
		
        $msql->query("update `$tb_config` set ma='$str'");
        echo 1;
        break;
		case "gameset":
		    $game = $msql->arr("select gid,gname,fast,panstatus,otherstatus,otherclosetime,userclosetime,mnum,fenlei,ifopen,autokj,xsort,thisbml,kjurl,flname from `$tb_game` order by xsort ",1);
			$cg = count($game);
			for($i=0;$i<$cg;$i++){
			     $msql->query("select ifok from `$tb_gamecs` where userid='99999999' and gid='".$game[$i]['gid']."'");
				  $msql->next_record();
				 $game[$i]['ifok'] = $msql->f('ifok');
			}
			//print_r($game);
			$tpl->assign("game",$game);
			$tpl->display("gameset.html");
		break;
		case "setgame":
		   $game = str_replace('\\','' ,$_POST['str']);
		   $game = json_decode($game,true);
		   $cg = count($game);
		   include("../data/cuncu.php");
		   $msql->query("delete from `$tb_gamecs` where userid='99999999'");		   
		   for($i=0;$i<$cg;$i++){
		      $sql = "insert into `$tb_gamecs` set userid='99999999',gid='".$game[$i]['gid']."',ifok='".$game[$i]['ifok']."',flytype=3,zc=100,upzc=0,zcmin=0,xsort='".$game[$i]['px']."',flyzc=100";
			  $msql->query($sql);
			  $msql->query("update `$tb_game` set xsort='".$game[$i]['px']."',ifopen='".$game[$i]['ifopen']."' where gid='".$game[$i]['gid']."'");
			  if($game[$i]['ifok']==0){
			      $msql->query("update `$tb_gamecs` set ifok=0 where gid='".$game[$i]['gid']."'");
			  }  
		   }
			  $kksql->query("create table `tmp` select * from `$tb_gamecs` where userid='99999999'"); 
			  $kksql->query("update `$tb_gamecs` a,tmp b set a.xsort=b.xsort,a.ifok=b.ifok where a.gid=b.gid");
			  $kksql->query("drop table `tmp`");
		   $msql->query("delete from `$tb_gamezc` where userid='99999999'");
		   $sql = "insert into `$tb_gamezc` set userid='99999999',typeid='0',flytype=1,zc=100,upzc=0,zcmin=0,flyzc=100,typename='低频彩'";
		   $msql->query($sql);
		   $sql = "insert into `$tb_gamezc` set userid='99999999',typeid='1',flytype=1,zc=100,upzc=0,zcmin=0,flyzc=100,typename='快开彩'";
		   $msql->query($sql);
		   echo 1;
		break;
		case "resys":
		     if($_REQUEST['enter']!='su') exit;
		     $msql->query("delete from `$tb_admins` where adminid!=10000 and adminid!=99999");
             $msql->query("update `$tb_admins` set logintimes=0,lastloginip=0,lastlogintime=0,passtime=0");	
			 $msql->query("delete from `$tb_admins_page` where adminid!=10000");	
			 $msql->query("delete from `$tb_admins_login` where 1");	 
			 $msql->query("delete from `$tb_kj` where 1");
			 $msql->query("delete from `$tb_z` where 1");	
			 $msql->query("delete from `$tb_c` where 1");	
			 include('../data/cuncu.php');
			 $kksql->query($deletestr);
			 $msql->query("delete from `$tb_lib` where 1"); 
			 $kksql->query($deletecc);
			 
			 $msql->query("delete from `$tb_error` where 1"); 
			 $msql->query("delete from `$tb_play_user` where 1"); 
			 $msql->query("delete from `$tb_online` where 1"); 
			 $msql->query("delete from `$tb_peilv` where 1"); 
			 $msql->query("delete from `$tb_gamecs` where userid!=99999999"); 
			 $msql->query("delete from `$tb_gamezc` where userid!=99999999"); 
			 $msql->query("delete from `$tb_zpan` where userid!=99999999"); 
			 $msql->query("delete from `$tb_points` where userid!=99999999"); 
			 $msql->query("delete from `$tb_warn` where userid!=99999999"); 
			 $msql->query("delete from `$tb_user` where userid!=99999999"); 
			 $msql->query("delete from `$tb_auto` where userid!=99999999"); 
			 $msql->query("delete from `$tb_fastje` where userid!=99999999");
			 $msql->query("delete from `$tb_fly` where 1");  
			 $msql->query("delete from `$tb_user_edit` where 1"); 
			 $msql->query("delete from `$tb_user_login` where 1"); 
			 $msql->query("delete from `$tb_user_page` where userid!=2001"); 
			 $msql->query("delete from `x_down` where 1"); 
			 $msql->query("delete from `$tb_flylist` where 1"); 
			 $msql->query("delete from `$tb_flyinfo` where 1"); 
			 echo outjs("ok");			 
		break;
	case "ptype":
	    if(in_array($_REQUEST['gid'],$garr)){
	       $gid= $_REQUEST['gid'];
		}
        $game = getgamecs($userid);
        $game = getgamename($game);
		$ptype = json_decode(transgame($gid,'ptype'),true);

		
        $tpl->assign("ptype", $ptype);
		$tpl->assign('nums', floor(count($ptype)/2));
		$tpl->assign("game", $game);
		$tpl->assign("flname", transgame($gid,'flname'));
		$tpl->assign("gid", $gid);
		$tpl->assign("config", $config);
	    $tpl->display("ptype.html");
	break;	
	case "setptype":
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
	    $gid =$_POST["gid"];
	    $data=str_replace('\\', '', $_POST['data']);
	    $msql->query("update `$tb_game` set ptype='{$data}' where gid='$gid'");
        $data = json_decode($data,true);
        $parr = ['选二任选','选二连组','选二连直','选三任选','选三前组','选三前直','选四任选','选五任选','任选二中二','选前二组选','选前二直选','任选三中三','选前三组选','选前三直选','任选四中四','任选五中五','任选六中五','任选七中五','任选八中五'];
        $lhlmarr=['四全中','三全中','二全中','三中二','二中特','特串'];
        $fenlei = transgame($gid,'fenlei');
        $bnsx='';
        if($fenlei==100){
            $msql->query("select ma from `$tb_config`");
            $msql->next_record();
            $ma = json_decode($msql->f("ma"),true);
            foreach ($ma['生肖'] as $k => $v) {
            	if(strpos($v,'49')!==false){
            		$bnsx= $k;
            		break;
            	}
            }
        }
	    foreach($data as $k => $v){
	    	if(in_array($v["c"], $parr)){
	    		$arr = getduoarrssuser($fenlei,$v["c"]);
	    		$pl = [];
	    		$pl[0]=[];
	    		foreach($arr as $k1 => $v1){
	    			$pl[0][$k1] = $v["p"];
	    		}
                $pl = json_encode($pl);
                $msql->query("update `$tb_play` set pl='$pl',mpl='$pl' where gid='$gid' and ptype='{$v["id"]}'");
	    	}else if($fenlei==100 && (strpos($v['c'],'一肖')!==false || strpos($v['c'],'正肖')!==false  || strpos($v['c'],'特肖')!==false) ){
	    		$pp = explode('/',$v['p']);

                $msql->query("update `$tb_play` set peilv1='{$pp[0]}',mp1='{$pp[0]}' where gid='$gid' and ptype='{$v["id"]}'");
                $msql->query("update `$tb_play` set peilv1='{$pp[1]}',mp1='{$pp[1]}' where gid='$gid' and ptype='{$v["id"]}' and name='$bnsx'");
	    	}else if($fenlei==100 && (strpos($v['c'],'合肖')!==false || strpos($v['c'],'肖連')!==false) ){
	    		$arr = getduoarr($v['c']);
	    		$pp = explode('/',$v['p']);
	    		$pl = [];
	    		$pl[0]=[];
	    		foreach($arr as $k1 => $v1){
	    			$pl[0][$k1] = $bnsx==$v1 ? $pp[1] : $pp[0];	    			
	    		}
                $pl = json_encode($pl);
                $msql->query("update `$tb_play` set pl='$pl',mpl='$pl' where gid='$gid' and ptype='{$v["id"]}'");
	    	}else if($fenlei==100 && strpos($v['c'],'尾連')!==false){
	    		$arr = getduoarr($v['c']);
	    		$pp = explode('/',$v['p']);
	    		$pl = [];
	    		$pl[0]=[];
	    		foreach($arr as $k1 => $v1){
	    			$pl[0][$k1] = '0尾'==$v1 ? $pp[1] : $pp[0];	    			
	    		}
                $pl = json_encode($pl);
                $msql->query("update `$tb_play` set pl='$pl',mpl='$pl' where gid='$gid' and ptype='{$v["id"]}'");
	    	}else if($fenlei==100 && (strpos($v['c'],'不中')!==false || in_array($v['c'], $lhlmarr) ) ){
	    		$arr = getduoarr('连码');
	    		$pp = explode('/',$v['p']);
	    		$pl = [];
	    		$pl[0]=[];
	    		foreach($arr as $k1 => $v1){
	    			$pl[0][$k1] = $pp[0];	    			
	    		}
	    		if($v['c']=='二中特' || $v['c']=='三中二'){
                    $pl[1]=[];
	    	    	foreach($arr as $k1 => $v1){
	    			   $pl[1][$k1] = $pp[1];	    			
	    		    }
	    		}
                $pl = json_encode($pl);
                $msql->query("update `$tb_play` set pl='$pl',mpl='$pl' where gid='$gid' and ptype='{$v["id"]}'");
	    	}else{
	    		$peilv1 = $v["p"];
                $msql->query("update `$tb_play` set peilv1='$peilv1',mp1='$peilv1' where gid='$gid' and ptype='{$v["id"]}'");
	    	}
	    }
	    echo 1;
	break;
	case "yiwotongbuptype":
        if ($_POST['pass'] != $config['supass'] && $_SESSION['hides'] != 1) {
            echo 2;
            exit;
        }
        $gid =$_POST["gid"];
        $msql->query("select ptype,gid,fenlei from `$tb_game` where gid='$gid'");
        $msql->next_record();
        $fenlei = $msql->f("fenlei");
        $ptype = $msql->f("ptype");
        $msql->query("update `$tb_game` set ptype='{$ptype}' where fenlei='$fenlei'");
        $play = $msql->arr("select * from `$tb_play` where gid='$gid'",1);
        foreach($play as $k => $v){
        	$whi = " and gid in(select gid from `$tb_game` where fenlei='$fenlei') ";
        	$sql = "update `$tb_play` set peilv1='{$v['peilv1']}',mp1='{$v['mp1']}',pl='{$v['pl']}',mpl='{$v['mpl']}' where pid='{$v['pid']}' $whi";
        	$msql->query($sql);
        }
        echo 1;
	break;

}


function checkma($arr){
   $ca = count($arr);
   $v = true;
   for($i=0;$i<$ca;$i++){
       if(!is_numeric($arr[$i]) | $arr[$i]<1 | $arr[$i]>49 | $arr[$i]%1!=0){
	      $v=false;
		  break;
  	   }
   }
   return $v;
}
?>