<?php
session_start();
global $totals;
$sfields;// = array(array());
for($term=0;$term<=4;$term++){
	for($t=1;$t<=3;$t++){
		$totals[$term.$t] = 0;
	}
	$totals["03"] = 1;
}
/* first sitting */
$dl = array();
//number of echec to be promoted
$dl['promoted']=0;
//number of echec to pass second sitting
$dl['sitting']=4;
//number of echec to repeat the year
$dl['repeat']=5;
//number of echec to be expeled
$dl['expeled']=8;

$status = array('echec'=>0,'sitting'=>array(),'promoted'=>true,'expeled'=>false,'repeat'=>false);
require_once("../../admin/includes/functions.php");
?><div name=paging id=paging></div>

<?php
require_once"../../LIB/config.php";
$db = new DBConnector($dbname = "school_report_db".$_GET['year']);
#echo $dbname;
$studentid = mysql_real_escape_string(trim($_GET['studentid']));
$studentid = ($studentid != 0 ? $studentid:$db->selectMin($tbl='tbl_student',$fld='StudentID',$rtn=false,$condition=array("DeptID"=>$_GET['dept'],"Class"=>$_GET['class'])));
if(!$db->select1cell("tbl_marks","ID",array("StudentID"=>$studentid),true)){
	echo "<span class = 'error'>No Student Found!!!</span>";
	return;
}
?>
<label id=styles></label>
<label id=styles2></label>
<style>
	#echec{ text-decolation:underline; color:#ff2222; }
</style>
<table bgcolor=#fff width=100% border=1 style='/*border-top:4px solid #fff;border-right:4px solid #fff;border-bottom:4px solid #fff;*/' id="tblReport">
	<tr valign=top>
		<td colspan=6>SOUTHERN PROVINCE <br>GISAGARA DISTRICT<br>ECOLE TECHNIQUE SAINT KIZITO<br>P.O.BOX: 174 BUTARE<br>Tel: 532 107<br>Mobile: 07 88 74 01 27
		</td>
		<td colspan=4 valign=center align=center><center><img height=100 width=100 src='./ajaximage/uploads/<?php echo $db->select1cell("tbl_student","StudentPhoto",array("StudentID"=>$studentid),true) ?>' /></center></td>
		<td colspan=7 valign=top>Name:<?php echo $db->select1cell("school_report_db`.`tbl_student","FirstName",array("ID"=>($studentid)),true)." ".$db->select1cell("school_report_db`.`tbl_student","LastName",array("ID"=>($studentid)),true); ?><br>No:<?php echo $_GET['studentnumber']; ?><br>Class: Senior <?php echo $_GET['class'] ?><br>Option:<?php echo $db->select1cell($tbl="tbl_dept",$field="DepartmentName",$condition=array("ID"=>$_GET['dept']),true) ?><br>Academic Year:<?php echo $_SESSION['year']; ?><br></td>
	</tr>
	<tr>
		<td colspan=17 align=center>SCHOOL REPORT</td>
	</tr>
	<tr>
		<td align=left rowspan=2>Courses</td>
		<?php
		$terms = array("Maximum","First Term","Second Term","Third Term","Year","2<sup>nd</sup> Sitt"); $i=0;
		foreach($terms as $term){
			echo "<td align=center colspan='".($i==5?1:3)."'>".$term."</td>"; $i++;
		}
		?>
	</tr>
	<tr>
		<?php
		$header = array("CAT","EX","TOT");
		for($i=0;$i<=3;$i++)foreach($header as $h) echo "<td colspan=1 align=center>".$h."</td>";
		?>
		<td colspan=1 align=center>TOT</td><td colspan=1 align=center>Max</td><td align=center>%</td><td align=center>%</td>
	</tr>
	<tr>
		<td>Displine</td>
		<?php
		$max = 0; $tot = 0; $thirdterm = false;
		for($i=0;$i<5;$i++){
			if($i == 0){
				//Load the maximum set for displine caurse
				echo "<td align=right colspan=3>40</td>";
			} elseif($i == 4){
				//Make the sum of Displine marks and %
				$percent=null;
				if($thirdterm){
					$percent = round(($tot*100)/$max,2);
					if($percent<50){
						$status['expeled'] = true;
						$status['promoted'] = false;
					}
				} else{
					$tot = null;
				}
				echo "<td align=right id=".($tot<($max/2)?"echec":"").">".$tot."</td><td align=right>".$max."</td><td align=right>".$percent."</td>";
			} else{
				//Load the The student Displine Marks
				$discipline = $db->select1cell("tbl_marks","GNC".$i,array("StudentID"=>$studentid),true);
				if($i == 3 && $discipline != null) $thirdterm = true;
				echo "<td align=right colspan=3>{$discipline}</td>";
				$tot += $discipline; $max += 40;
			}
			if($i == 4) echo "<td>&nbsp;</td>";
		}
		?>
	</tr>
	<tr>
		<td align=center colspan=17>GENERAL COURSES</td></label>
	</tr>
	<?php
	$error=array();
	function loadCourses(&$totals,&$status,$dl=null,$type=0,$db=null,$studentid=1,&$sfields=null,&$error=0){
		#$totals['01']="30";
		//Search for Caurses of The given Class
		if(!$type)$cndtn = array("Acronym"=>"LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."9%')");
		else $cndtn = array("Acronym"=>array("LIKE('".$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true).$_GET['class']."%')","Acronym"=>"NOT LIKE('".$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true).$_GET['class']."9%')"));
		//array("Acronym"=>($type?"NOT ":"")."LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."9%')")
		$data = $db->selectFields($tbl='tbl_course',$field=array("CourseName","Acronym","Maximum"),$condition=array("Acronym"=>($type?"LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."%') && `Acronym` NOT ":"")."LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."9%')"),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='=',$multiplereference=false);
		if(count($data)>0){
			echo ""; $animate="<style>";
			$totals['03'] == 1 ?$totals['03']=0:"";
			for($i=0;$i<count($data);$i++){
				echo "<tr>";
				echo "<td>".$data[$i]['CourseName']."</td>";
				$max = 1; $tot = 0; $termss = array(1=>true,true,true);
				for($term=0;$term<=4;$term++){
					if($term == 0){
						//Load the maximum set of found course
						for($c=1;$c<3;$c++){
							@$totals[$term.$c] += $data[$i]['Maximum'];
							echo "<td id=mn".$i.$type.$c." align=right>".$data[$i]['Maximum']."</td>";
						}
						@$totals[$term.'3'] += $data[$i]['Maximum'] + $data[$i]['Maximum'];
						echo "<td align=right>".($data[$i]['Maximum'] + $data[$i]['Maximum'])."</td>";
						$max = (($data[$i]['Maximum'] + $data[$i]['Maximum'])*3);
					} elseif($term == 4){
						@$totals[$term.'2'] += $max;
						if($termss[3] ){
							@$totals[$term.'1'] += $tot;
							//@$totals[$term.'2'] += $max;
							@$totals[$term.'3'] += $percent;
							$percent = round(($tot*100)/$max,1);
							if(!$status['expeled'] && $percent<50){
								++$status['echec'];
								if($status['echec']<=$dl['sitting']){
									//2nd sitting section;
									$status['sitting'][] = $data[$i]['CourseName'];
									$status['promoted'] = false;
								} elseif($status['echec'] < $dl['expeled'] && $status['echec'] >= $dl['repeat']){
									//repeat section
									$status['repeat'] = true;
									$status['sitting'] = array();
									$status['promoted'] = false;
								} else{
									//expeled during 1 first sitting
									$status['expeled'] = true;
									$status['repeat'] = false;
									$status['promoted'] = false;
									$status['sitting'] = array();
								}
							}
							echo "<td align=right id=".($tot<($max/2)?"echec":"").">".$tot."</td>";
							echo "<td align=right>".$max."</td>";
							echo "<td align=right id=".($percent<50?"echec":"").">".$percent."</td>";
						} else{
							echo "<td align=right></td><td align=right>".$max."</td><td align=right></td>";
						}
					} else{
						$stot = 0; $t=array(1=>true,true);
						for($h=1;$h<=2;$h++){
							$sfields[$term][] = $data[$i]['Acronym'].$term.$h;
							$mark = $db->select1cell("tbl_marks",$data[$i]['Acronym'].$term.$h,array("StudentID"=>$studentid),true);
							if($mark == null || $mark > $data[$i]['Maximum']){
								$error[] = $term;
								$t[$h] = false;
								echo "<td>".($mark)."</td>";
							} else{
								$stot += $mark; # $max += $data[$i]['Maximum'];
								@$totals[$term.$h] += $mark;
								echo "<td align=right id=".($mark<($data[$i]['Maximum']/2) && $mark != null ?"echec":"").">".$mark."</td>";
							}
						}
						#var_dump($stot);
						echo "<td align=right id=".($stot<($data[$i]['Maximum'])?"echec":"").">".($t[1] || $t[2]?$stot:null)."</td>";
						$tot += $stot;
						@$totals[$term."3"] += $stot;
						if(!$t[1] && !$t[2]) $termss[$term] = false;
					}
				}
				//sitting section
				#echo "<td>";
				#var_dump($status['sitting']); echo "<br>";

				if(in_array($data[$i]['CourseName'],$status['sitting'])){
					$n = null;
					if($db->checkTable("tbl_second_sitting"))$n = $db->select1cell("tbl_second_sitting",$data[$i]['Acronym'],array("StudentID"=>$studentid),true);
					echo "<td id=".($n<50?"echec":"").">".$n."</td>";
					if(@$status['sitting']['status'] == null){
						$status['sitting']['status'] = array('echec'=>0,'promoted'=>true,'expeled'=>false,'repeat'=>false);
					}
					if(!$status['sitting']['status']['expeled'] && $n<50){
						#echo "OK";
						++$status['sitting']['status']['echec'];
						if($status['sitting']['status']['echec'] < $dl['expeled'] && $status['sitting']['status']['echec'] >= $dl['repeat']){
							//repeat section
							$status['sitting']['status']['repeat'] = true;
							$status['sitting']['status']['promoted'] = false;
						} elseif($status['sitting']['status']['echec'] >= $dl['expeled']){
							//expeled during 2nd sitting
							$status['sitting']['status']['expeled'] = true;
							$status['sitting']['status']['repeat'] = false;
							$status['sitting']['status']['promoted'] = false;
						} elseif($status['sitting']['status']['echec']>0 ){
							$status['sitting']['status']['expeled'] = false;
							$status['sitting']['status']['repeat'] = true;
							$status['sitting']['status']['promoted'] = false;
						}
					}
				} else echo "<td></td>";
				#echo @$status['sitting']['status']['echec'];
				#echo "</td>";
				/*
				for($term=0;$term<4;$term++){
					if($term == 0){
						//Load the maximum set of found course
						for($c=1;$c<3;$c++){
							echo "<td align=right>".$data[$i]['Maximum']."</td>";
						}
						echo "<td align=right>".($data[$i]['Maximum'] + $data[$i]['Maximum'])."</td>";
					} elseif($term == 3){
						//Make the sum of marks and % of found course
						//echo "<td>".$tot."</td><td>".$max."</td><td>".round(($tot*100)/$max,2)."</td>";
					} else{
						//Load the student Marks from tbl_marks table;
						for($ter=1;$ter<=3;$ter++){
							$tot = 0;
							for($p=1;$p<=2;$p++){
								//select marks in table that correspond to the student and course;
								$mark = $db->select1cell("tbl_marks",$data[$i]['Acronym'].$ter.$p,array("StudentID"=>$studentid),true);
								$tot += $mark; $max += $data[$i]['Maximum'];
								echo "<td align=right>".$mark."</td>";
							}
							echo "<td align=right></td>";
						}
						#$tot += 32; $max += 40;
					}
					if($term == 4) echo "<td>&nbsp;</td>";
				} */
				echo "</tr>";
			}
		}
	}
	loadCourses($totals,$status,$dl,$type=0,$db,$studentid,$sfields,$error);
	?>
	<tr>
		<td align=center colspan=17>TECHNICAL COURSES</td>
	</tr>
	<?php
	loadCourses($totals,$status,$dl,$type=1,$db,$studentid,$sfields,$error);
	/*
	?>
	<tr id=tableheader>
		<th rowspan=2>#</th><th rowspan=2>Name</th>
		<?php
			$fld = array(); $fld2 = array(); $maintotal = 0; $subrow=""; $counter2=2;
			$field = $db->selectFields($tbl='tbl_course',$field=array("Acronym","Maximum"),$condition=array("Acronym"=>"LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."%')"),$limit=null,$order="",$indexed=true,$sign='=',$multiplereference=false);
			if(count($field >0)){
				for($i=0;$i<count($field);$i++){
					$maintotal += $db->select1cell("tbl_course","Maximum",array("Acronym"=>$field[$i]['Acronym']),true);
					$fld2[] = $field[$i]['Acronym'];
					$fld[] = $field[$i]['Acronym'].$_GET['term']."1"; $counter2 += 2;
					$fld[] = $field[$i]['Acronym'].$_GET['term']."2";
					$subrow .= "<th class='".$field[$i]['Acronym']."'>TJ /".$field[$i]['Maximum']."</th><th class='".$field[$i]['Acronym']."'>EX /".$field[$i]['Maximum']."</th>";
					echo "<th class='".$field[$i]['Acronym']."' colspan=2>".$field[$i]['Acronym']."</th>";
				}
			}
			$maintotal==0?$maintotal=1:$maintotal = $maintotal;
		?>
	</tr>
	<tr>
		<?php
		echo $subrow;
		?>
	</tr>
	<?php

	$data = $db->selectInMoreTable($lbl=array("tbl"=>array("tbl_student","tbl_marks"),"fld"=>array("tbl_student"=>array("ID","FirstName","LastName"),"tbl_marks"=>$fld),"condition"=>array("tbl_marks`.`StudentID"=>"tbl_student`.`ID","tbl_student`.`DeptID"=>$_GET['dept'],"tbl_student`.`Class"=>$_GET['class'])),$multirows=true,$indexed=true, $order="ORDER BY `tbl_marks`.`StudentID` ASC");
	$out = "";
	if(count($data)<1){
		echo "<tr><td colspan=".$counter2." bgcolor=#fff align=center>No Student in S".$_GET['class'].$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true)."</td></tr>";
	} else{
		$pages = count($data)/12;
		#echo $pages;
		if($pages >1){
			/* map paging to be displayed in paging div * /
			$str = "<select name=paging class=typeproform>";
			for($i=0;$i<=$pages;$i++){
				$str .= "<option ".(($_GET['start']/12) == $i?"selected":"")." onclick='studentMarks(".$_GET['dept'].",".$_GET['class'].",".($i*12).",".((($i+1)*12)).")'>".($i+1);
			}
			$str .= "</select>";
			echo "<div>".$str."</div>";
		}
	for($i=$_GET['start'];$i<($_GET['end']>count($data)?count($data):$_GET['end']);$i++){
		$out .= "<tr id=n".($i%2).">";
		$out .= "<td>";
		$out .= $i + 1;
		$out .= "</td>";
		$out .= "<td>";
		$out .= $data[$i]['FirstName']." ".$data[$i]['LastName'];
		$out .= "</td>";
		$total = 0;
		foreach($fld as $f){
			$total += $data[$i][$f];
			$out .= "<td align=right bgcolor='".(($data[$i][$f] > $db->select1cell("tbl_course","Maximum",array("Acronym"=>preg_replace("/".substr($f,6)."/","",$f)),true)) || !is_numeric($data[$i][$f])?"#f00":"")."' class='".preg_replace("/".substr($f,6)."/","",$f)."'>".$data[$i][$f]."</td>";
		}
		/*
		$out .= "<td align=right>".$total."</td>";
		$out .= "<td align=right>".round(($total*100)/$maintotal,2)."</td>";
		/*
		$out .= "<td>";
		$out .= $data[$i]['Acronym'];
		$out .= "</td>";
		$out .= "<td>";
		$out .= "S".$data[$i]['Class'].$data[$i]['Acronym'];
		$out .= "</td>";/*
		$out .= "<td>";
		$out .= "<a href='./other.php?sql=reset.sql&id=".$data[$i]['ID']."' onclick='return confirmFunction(\"Reset Password for ".$data[$i]['FirstName']."?\")'>Reset</a>";
		$out .= "</td>";* /
		$out .= "<td>";
		$out .= "<a href='./student.php?sql=update.sql&id=".$data[$i]['ID']."'>Update</a>";
		$out .= "</td>";
		$out .= "<td>";
		$out .= "<a href='./student.php?sql=delete.sql&id=".$data[$i]['ID']."' onclick='return deleteFunction(\" ".$data[$i]['FirstName']."?\")'>Delete</a>";
		$out .= "</td>";* /
		$out .= "</tr>";
	} }
	echo $out; */
	//$totals['01'] = 32;
	?>
	<tr>
		<td>
			Total
		</td>
		<?php
		#echo "<pre>";var_dump($totals); echo "</pre>";
		for($term=0;$term<=4;$term++){
			for($t=1;$t<=3;$t++){ //$totals[$term.$t]
				if($totals[$term.$t] == 0) $totals[$term.$t] = null;
				if($term == 0)echo "<td>".($totals[$term.$t]==1?0:$totals[$term.$t])."</td>";
				elseif($term == 4){
					if($t == 3){
						echo "<td align=right id='".($t != 2 && $t != 3 && $totals[$term.$t]<($totals[$term.'2']/2) ?"echec":"")."'>".($totals['42'] == 0 || $totals['41'] == 0 ?null:round(($totals['41']*100)/$totals['42'],2))."</td>";
					} else
						echo "<td align=right id='".($t != 2 && $t != 3 && $totals[$term.$t]<($totals[$term.'2']/2) ?"echec":"")."'>".$totals[$term.$t]."</td>";

				} else echo "<td align=right id='".($t != 3 && $totals[$term.$t]<$totals['01']/2?"echec":($t == 3 && $totals[$term.$t]<$totals['01']?"echec":""))."'>".$totals[$term.$t]."</td>";
			}
		}
		?>
	</tr>
	<tr>
		<td colspan=4>Percentage</td>
		<?php
		$place = array(1=>true,true,true,true);
		$parcenta = 0; $terms = array(1=>true,true,true);
		for($term=1;$term<=3;$term++){
			$per = round(($totals[$term."3"]*100)/$totals["03"],2);
			$parcenta += $per;
			if($per == 0){
				$per=null;
				$terms[$term] = false;
				$place[$term] = false;
			} else {
				$per=$per;
			}
			echo "<td colspan='3' align=right>".$per."%</td>";
		}
		if($terms[3] ){

			echo "<td colspan='3' align=right>".round(($parcenta*100)/300,2)."%</td>";
		} else{
			$place[$term] = false;
			echo "<td colspan='3' align=right>%</td>";
		}
		?>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=4>Place</td>
		<?php
		#var_dump($_GET['class']);
		for($term=1;$term<=4;$term++){
			if($place[$term]){
				#echo "..";
				echo "<td colspan=3 align=right> ".(LoadStudentPlace($db,$sfields,$studentid,$term,$_GET['dept'],$_GET['class'],(in_array($term,$error)?$term:0)) != 0?LoadStudentPlace($db,$sfields,$studentid,$term,$_GET['dept'],$_GET['class'],(in_array($term,$error)?$term:0)):"")." of ".$db->selectCount("tbl_student","ID",array("DeptID"=>$_GET['dept'],"Class"=>$_GET['class']))."</td>";
			} else{
				echo "<td colspan=3 align=right>...of ".$db->selectCount("tbl_student","ID",array("DeptID"=>$_GET['dept'],"Class"=>$_GET['class']))."</td>";
			}
		}
		?>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=4>Sign. of Parents</td>
		<?php
		for($term=1;$term<=4;$term++){
			echo "<td colspan=3></td>";
		}
		?>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=4>Sign. of Class's Advisor</td>
		<?php
		for($term=1;$term<=4;$term++){
			echo "<td colspan=3>&nbsp;</td>";
		}
		?>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=10 valign=top><u>Decision of the deliberation</u><br>
			<style>
				#box{border:1px solid #000; width:25px;}
			</style>
			<?php
			#var_dump($status);
			?>
			<table width=100% border=0>
				<tr>
					<td><b>1. First Sitting</b></td>
				</tr>
				<tr>
					<td>Pomoted</td><td id=box><?php echo $status['promoted']==true?"<img src='../../admin/images/ok.png' />":"&nbsp;" ?></td>
				</tr>
				<tr>
					<td>Proposed to repeat the year</td><td id=box><?php echo $status['repeat']==true?"<img src='../../admin/images/ok.png' />":"&nbsp;" ?></td>
				</tr>
				<tr>
					<td>Expeled</td><td id=box><?php echo $status['expeled']==true?"<img src='../../admin/images/ok.png' />":"&nbsp;" ?></td>
				</tr>
				<?php
				if($status['sitting'] != null){
					?>
					<tr>
						<td colspan=2>
							<u>Proposed to do the Second Sitting Exams of </u><br>
							<?php
							for($i=0;$i<count($status['sitting'])-($status['sitting']['status'] != null?1:0) ;$i++){
								echo ($i+1).". ".$status['sitting'][$i]."<br />";
							}
							?>
							<br />
						</td>
					</tr>
					<?php
				}
				if(@$status['sitting']['status'] != null){
					#var_dump($status['sitting']['status']);
				?>
				<tr>
					<td><b>2. Second Sitting</b></td>
				</tr>
				<tr>
					<td>Pomoted</td><td id=box><?php echo $status['sitting']['status']['promoted']==true?"<img src='../../admin/images/ok.png' />":"&nbsp;" ?></td>
				</tr>
				<tr>
					<td>Proposed to repeat the year</td><td id=box><?php echo $status['sitting']['status']['repeat']==true?"<img src='../../admin/images/ok.png' />":"&nbsp;" ?></td>
				</tr>
				<tr>
					<td>Expeled</td><td id=box><?php echo $status['sitting']['status']['expeled']==true?"<img src='../../admin/images/ok.png' />":"&nbsp;" ?></td>
				</tr>
				<?php
				}
				?>
			</table>
		</td>
		<td colspan=7>Done at Save,on ...../...../<?php echo $_SESSION['year'] ?><br><br><br><br><br>The Headmaster </td>
	</tr>
</table>
<?php
#var_dump($sfields);
?>
