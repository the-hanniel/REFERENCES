<?php
session_start();
$button = false;
?>
<div name=paging id=paging></div>
<?php if(@$_GET['move'] == 1) echo "<form name=promote method=post>"; ?>
<table width=100% border=1>
	<tr id=tableheader>
		<th <?php echo $_GET['move'] != 0?"colspan=2":""; ?>>#</th><th>First Name</th><th>Last Name</th><th>Department</th><th>Class</th><th colspan=2>Action</th>
	</tr>
	<?php
	require_once"../../LIB/config.php";
	#var_dump($_GET);
	$year = @$_GET['year'] != 0?$_GET['year']:$_SESSION['year'];
	$db = new DBConnector("school_report_db".$year);
	#echo $year;
	$data = $db->selectInMoreTable($lbl=array("tbl"=>array("school_report_db".$year."`.`tbl_student","tbl_dept","school_report_db`.`tbl_student"),"fld"=>array("school_report_db`.`tbl_student"=>array("ID","FirstName","LastName"),"school_report_db".$year."`.`tbl_student"=>array("Class"),"tbl_dept"=>array("Acronym")),"condition"=>array("school_report_db`.`tbl_student`.`ID"=>"tbl_student`.`StudentID","tbl_dept`.`ID"=>"tbl_student`.`DeptID","tbl_student`.`DeptID"=>$_GET['dept'],"tbl_student`.`Class"=>$_GET['class'])),$multirows=true,$indexed=true, $order="ORDER BY `tbl_dept`.`Acronym` ASC, `tbl_student`.`Class` ASC, `school_report_db`.`tbl_student`.`FirstName` ASC, `school_report_db`.`tbl_student`.`LastName` ASC");
	$out = ""; $dpt = $_GET['dept']; $cl = $_GET['class'];
	if(count($data)<1 || ($_GET['move'] == 1 && $_GET['year'] == '')){
		echo "<tr><td colspan=7 align=center>No Student in S".$_GET['class'].$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true).($_GET['year']==''?" Because Non Academic Year Found!":"")."</td></tr>";
	} else{
		$button = true;
		if(@$_GET['move'] == 0 || $_GET['move'] == null){
			$pages = count($data)/12;
			#echo $pages;
			if($pages >1){
				/* map paging to be displayed in paging div */
				$str = "<select name=paging class=typeproform>";
				for($i=0;$i<$pages;$i++){
					$str .= "<option ".(($_GET['start']/12) == $i?"selected":"")." onclick='studentList(".$_GET['dept'].",".$_GET['class'].",".($i*12).",".((($i+1)*12)).")'>".($i+1);
				}
				$str .= "</select>";
				echo "<div>".$str."</div>";
			}
		} else{
			$_GET['start'] = 0;$_GET['end'] = count($data);
		}
		for($i=$_GET['start'];$i<($_GET['end']>count($data)?count($data):$_GET['end']);$i++){
			$out .= "<tr id=n".($i%2).">";
			if(@$_GET['move'] == 1) $out .= "<td><input type=checkbox checked name='".$data[$i]['ID']."'></td>";
			$out .= "<td>";
			$out .= $i + 1;
			$out .= "</td>";
			$out .= "<td>";
			$out .= $data[$i]['FirstName'];
			$out .= "</td>";
			$out .= "<td>";
			$out .= $data[$i]['LastName'];
			$out .= "</td>";
			$out .= "<td>";
			$out .= $data[$i]['Acronym'];
			$out .= "</td>";
			$out .= "<td>";
			$out .= "S".$data[$i]['Class'].$data[$i]['Acronym'];
			$out .= "</td>";/*
			$out .= "<td>";
			$out .= "<a href='./other.php?sql=reset.sql&id=".$data[$i]['ID']."' onclick='return confirmFunction(\"Reset Password for ".$data[$i]['FirstName']."?\")'>Reset</a>";
			$out .= "</td>";*/
			$out .= "<td>";
			$out .= "<a onclick='return confirmFunction(\"This Will Reset Student Marks to Null\")' href='./student.php?sql=update.sql&id=".$data[$i]['ID']."'>Update</a>";
			$out .= "</td>";
			$out .= "<td>";
			$out .= "<a href='./student.php?sql=delete.sql&id=".$data[$i]['ID']."' onclick='return deleteFunction(\" ".$data[$i]['FirstName']." ".$data[$i]['LastName']."? And will delete his/her marks\")'>Delete</a>";
			$out .= "</td>";
			$out .= "</tr>";
		}
	}
	echo $out;
	?>
</table>
<?php if(@$_GET['move'] == 1){
if($button)echo "<input type=hidden name=original value='".$year."'><input type=submit name=promot value='Done!' class=button>";
echo "</form>";
}
if(@$data[0]['Class'] == null || $data[0]['Class'] != 6){
	?>
	<fieldset class=upload>
		<legend style='text-align:center'>Use This Form To Promote Student</legend>
		<div>
			<form name=spromo>
				<table>
					<tr><td>Year</td><td>Department</td><td>Class</td></tr>
					<tr>
						<td>
							<?php
							$dept="";
							$dept = $db->selectFields($tbl='school_report_db`.`tbl_years',$field=array("Year"),$condition=array("Year"=>$_SESSION['year']),$limit=null,$order="ORDER BY Year DESC",$indexed=true,$sign='<',false);
							#var_dump($dept);
							?>
							<select name=year class = 'typeproform'>
								<?php #studentList(deptid=2,classid=4,start=0,end=12,page='studentlist.php',move=0)
								for($i=0;$i<count($dept);$i++) echo "<option onclick='studentListPromo(document.spromo.year.value,document.spromo.dept.value,document.spromo.classid.value,1)' value='".$dept[$i]['Year']."' ".($dept[$i]['Year'] == $_SESSION['year']?"selected":"").">".$dept[$i]['Year'];
								?>
							</select>
						</td>
						<td>
							<?php
							$dept="";
							$dept = $db->selectFields($tbl='tbl_dept',$field=array("ID","DepartmentName","Acronym"),$condition=array("ID"=>array($db->select1cell("tbl_dept","ID",array("Acronym"=>"Administration"),true),$db->select1cell("tbl_dept","ID",array("Acronym"=>"GNC"),true))),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='!=',true);
							?>
							<select name=dept class = 'typeproform'>
								<?php
								for($i=0;$i<count($dept);$i++) echo "<option onclick='studentListPromo(document.spromo.year.value,document.spromo.dept.value,document.spromo.classid.value,1)' value='".$dept[$i]['ID']."' ".($dept[$i]['ID'] == $dpt?"selected":"").">".($dept[$i]['Acronym'] != 'Administration'?$dept[$i]['Acronym']:"");
								?>
							</select>
						</td>
						<td>
							<select name=classid class=typeproform>
								<?php for($i=4;$i<=6;$i++) echo "<option onclick='studentListPromo(document.spromo.year.value,document.spromo.dept.value,document.spromo.classid.value,1)' ".($i==$cl?"selected":"").">".$i; ?>
							</select>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</fieldset>
	<?php
} else echo "<br />";
?>
