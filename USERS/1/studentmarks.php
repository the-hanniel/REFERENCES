<?php
session_start();
?>
<div name=paging id=paging></div>

<?php
require_once"../../LIB/config.php";
$year = $_GET['year'] != null?$_GET['year']:$_SESSION['year'];
$db = new DBConnector($dbname = "school_report_db".$_GET['year']);
#echo $dbname;
?>
<label id=styles></label>
<label id=styles2></label>
<table  id="tblMarks" border=1 style='border-top:4px solid #fff;border-right:4px solid #fff;border-bottom:4px solid #fff;'>
	<thead>
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
					$fld[] = "GNC".$_GET['term'];
					$subrow .= "<th>Total</th>";
					echo "<th>Discipline</th>";
				}
				$maintotal==0?$maintotal=1:$maintotal = $maintotal;
			?>
		</tr>
		<tr>
		<?php
		echo $subrow;
		?>
		</tr>
	</thead>
	<tbody id="myTableData">
	<?php

	$data = $db->selectInMoreTable($lbl=array("tbl"=>array("school_report_db`.`tbl_student",$dbname."`.`tbl_student",$dbname."`.`tbl_marks"),"fld"=>array("school_report_db`.`tbl_student"=>array("ID","FirstName","LastName"),$dbname."`.`tbl_student"=>array("studentID"),$dbname."`.`tbl_marks"=>$fld),"condition"=>array("school_report_db`.`tbl_student`.`ID"=>$dbname."`.`tbl_student`.`StudentID",$dbname."`.`tbl_marks`.`StudentID"=>$dbname."`.`tbl_student`.`StudentID",$dbname."`.`tbl_student`.`DeptID"=>$_GET['dept'],$dbname."`.`tbl_student`.`Class"=>$_GET['class'])),$multirows=true,$indexed=true, $order="ORDER BY `tbl_marks`.`StudentID` ASC");
	$out = "";
	if(count($data)<1){
		echo "<tr><td colspan=".$counter2." bgcolor=#fff align=center>No Student in S".$_GET['class'].$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true)."</td></tr>";
	} else{
		$pages = count($data)/12;
		#echo $pages;
		if($pages >1){
			/* map paging to be displayed in paging div */
			$str = "<select name=paging class=typeproform>";
			for($i=0;$i<$pages;$i++){
				$str .= "<option ".(($_GET['start']/12) == $i?"selected":"")." onclick='studentMarks({$year},".$_GET['dept'].",".$_GET['class'].",".($i*12).",".((($i+1)*12)).",\"studentmarks.php\",{$_GET['term']})'>".($i+1);
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
			$out .= "<td align=right bgcolor='".(($data[$i][$f] > ( preg_match('/^GNC/',$f)?$db->select1cell("tbl_course","Maximum",array("Acronym"=>"GNC"),true):$db->select1cell("tbl_course","Maximum",array("Acronym"=>preg_replace("/".substr($f,6)."/","",$f)),true) )) || !is_numeric($data[$i][$f])?"#f00":"")."' class='".preg_replace("/".substr($f,6)."/","",$f)."'>".$data[$i][$f]."</td>";
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
		$out .= "</td>";*/
		$out .= "</tr>";
	} }
	echo $out;
	?>
	</tbody>
</table>
