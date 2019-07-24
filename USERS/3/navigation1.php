<?php
session_start();
require_once"../../LIB/config.php";
$db = new DBConnector("school_report_db".$_SESSION['year']);
#var_dump($_GET);
if($_GET['type'] == 1){
	$dept = $db->selectFields($tbl="tbl_course",$field=array("Acronym"),$condition=array("TeacherID"=>$_SESSION['u_id'],"Deptid"=>$_GET['dept']),$limit=null,$order="ORDER BY DeptID ASC",$indexed=true,$sign='=',$multiplereference=false,$distinct=true);
	?>
	<select name=classid class=typeproform>
		<?php
		$last ="";
		for($i=0;$i<count($dept);$i++){
			if($last == $dept[$i]['Acronym'][3]) continue;
			$last = $dept[$i]['Acronym'][3];
			echo "<option onclick='navigation1(document.reference.dept.value,document.reference.classid.value,2,\"courseform\"); studentMarksTeacher(document.reference.dept.value,document.reference.classid.value,document.reference.term.value,document.reference.courseid.value);' value='".$dept[$i]['Acronym'][3]."'>".$dept[$i]['Acronym'][3];
		}
		?>
	</select>
	<?php
}
#echo $_GET['type'];
if($_GET['type'] == 2){
	$dept = $db->selectFields($tbl="tbl_course",$field=array("CourseName","Acronym"),$condition=array("TeacherID"=>$_SESSION['u_id'],"Deptid"=>$_GET['dept'],"Acronym"=>"LIKE('".$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true).$_GET['class']."%')"),$limit=null,$order="ORDER BY DeptID ASC",$indexed=true,$sign='=',$multiplereference=false,$distinct=true);
	?>
	<select name=courseid class=typeproforms>
		<?php
		$last ="";
		for($i=0;$i<count($dept);$i++){
			if($last == $dept[$i]['CourseName']) continue;
			$last = $dept[$i]['CourseName'];
			echo "<option value='".$dept[$i]['Acronym']."' onclick='studentMarksTeacher(document.reference.dept.value,document.reference.classid.value,document.reference.term.value,document.reference.courseid.value);'>".$dept[$i]['CourseName'];
		}
		?>
	</select>
	<?php
}
?>