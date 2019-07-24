<?php
session_start();
#var_dump($_GET);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>SYSTEM USERS REGISTRATION</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!--Link to Validation JS source File -->
	<script type = 'text/javascript' language='javascript' src = '../../admin/js/validation.js'></script>
	<!--Link to the template css file-->
	<link rel="stylesheet" type="text/css" href="../../admin/css/style.css" />
	<!--Link to Favicon -->
	<link rel="icon" href="../../admin/images/favi_logo.gif"/>
	<!-- Spry Stuff Starts here-->
	<link href="../../admin/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../admin/spry/textfieldvalidation/SpryValidationTextField.js"></script>
	<!-- Spry Stuff Ends Here-->
</head>
<body>
<div name=paging id=paging></div>

<?php
require_once"../../LIB/config.php";
$db = new DBConnector($dbname = "school_report_db".$_SESSION['year']);
#echo $dbname;
if(!$db->checkTable("tbl_second_sitting")){
	echo "<span class=error>Second Sitting Is Not Activated Yet<br>Please Contant School Head Teacher To Activate It</span>";
	die;
}
$dept = $db->selectFields($tbl="tbl_course",$field=array("DeptID","Acronym"),$condition=array("TeacherID"=>$_SESSION['u_id']),$limit=null,$order="ORDER BY DeptID ASC",$indexed=true,$sign='=',$multiplereference=false,$distinct=true);
#var_dump($dept);
#var_dump($_GET);
if($_GET['acronym'] == ''){
	echo "<style> #course{background-color:#ff0000;}</style><span class=error>Click on the Course</span>";
	die;
}
#if(!preg_match('',$_GET['']))
$field1 = $_GET['acronym'];//.$_GET['term']."1"; $field2=$_GET['acronym'].$_GET['term']."2";

//load students with less than 50% into 2 nd sitting table
$students=array();
$student = $db->selectFields($tbl="tbl_student",$field=array("StudentID","DeptID","Class"),$condition=array("DeptID"=>$_GET['dept'],"Class"=>$_GET['class']),$limit=null,$order="",$indexed=true,$sign='=',$distinct=false);
#echo "<pre>";var_dump($student); die;
for($st=0;$st<count($student);$st++){
	#var_dump($student[$st]["ID"]); echo "<br>";
	$fields = array();
	for($term=1;$term<=3;$term++){
		for($c=1;$c<=2;$c++){
			if(preg_match("/".$db->select1cell("tbl_dept","Acronym",array("ID"=>$student[$st]['DeptID']),true).$student[$st]['Class']."/",$_GET['acronym'])) $fields[] = $_GET['acronym'].$term.$c;
		}
	}
	#echo "<pre>";var_dump($fields); die;
	//select marks for 1 student in one course the holl year
	$marks = $db->selectFields($tbl="tbl_marks",$field=$fields,$condition=array("StudentID"=>$student[$st]["StudentID"]),$limit=null,$order="",$indexed=true,$sign='=',$distinct=false);
	#echo "<pre>";var_dump($marks); echo "<br><br>";#die;
	if(count($marks) == 1){
		#echo "Test Marks Start";
		if(!in_array($student[$st]['StudentID'],$students)){
			#echo "Test echecs and Null marks Starts here";
			if(array_sum($marks[0]) < ($db->select1cell("tbl_course","Maximum",array("Acronym"=>$_GET['acronym']),true)*3) ) $students[] = $student[$st]['StudentID'];  /*|| !in_array(null,$marks[0]*/
		}
	}
}
#echo "<pre>";var_dump($students); echo "<br><br>";die;
//insert all found student in the table

//build the query
foreach($students as $id){
	$db->InsertIfNotExist($tbl="tbl_second_sitting",$data=array("StudentID"=>$id),$condition=array("StudentID"=>$id),$auto_increment=true);
}
?>
<label id=styles></label>
<label id=styles2></label>
<form name=marks>
<table border=1 width=80% style='border-top:4px solid #fff;border-right:4px solid #fff;border-bottom:4px solid #fff;'>
	<tr><th colspan=4>Second Sitting Marks: <?php echo $db->select1cell("tbl_course","CourseName",array("Acronym"=>$_GET['acronym']),true); ?></th></tr>
	<tr id=tableheader>
		<th width=20px>#</th><th>Name</th><th>%</th><th></th>
	</tr>
	<?php
	//select all student in selected class
	#var_dump($_GET);
	#$student = $db->selectFields($tbl="tbl_student",$field=array("ID"),$condition=array("DeptID"=>$_GET['dept'],"Class"=>$_GET['class']),$limit=null,$order="ORDER BY `FirstName` ASC, `LastName` ASC",$indexed=true,$sign='=',$multiplereference=false,$distinct=false);
	#var_dump($student);
	#foreach($student as $ids) $db->InsertIfNotExist($tbl="tbl_marks",$data=array("StudentID"=>$ids['ID']),$condition=array("StudentID"=>$ids['ID']),$auto_increment=true);
	$data = $db->selectInMoreTable($lbl=array("tbl"=>array("school_report_db`.`tbl_student",$dbname."`.`tbl_student",$dbname."`.`tbl_second_sitting"),"fld"=>array("school_report_db`.`tbl_student"=>array("ID","FirstName","LastName"),$dbname."`.`tbl_student"=>array("StudentID"),$dbname."`.`tbl_second_sitting"=>array($field1)),"condition"=>array("school_report_db`.`tbl_student`.`ID"=>$dbname."`.`tbl_student`.`StudentID", $dbname."`.`tbl_second_sitting`.`StudentID"=>$dbname."`.`tbl_student`.`StudentID", $dbname."`.`tbl_student`.`DeptID"=>$_GET['dept'],$dbname."`.`tbl_student`.`Class"=>$_GET['class'])),$multirows=true,$indexed=true, $order="ORDER BY `tbl_second_sitting`.`StudentID` ASC");
	$out = "";
	if(count($data)<1){
		echo "<tr><td colspan=4 bgcolor=#fff align=center>No Student in ".$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true)." Department</td></tr>";
	} else{
		$_GET['start'] = 0; $_GET['end']=count($data);
		for($i=$_GET['start'];$i<($_GET['end']>count($data)?count($data):$_GET['end']);$i++){
			$out .= "<tr id=n".($i%2).">";
			$out .= "<td>";
			$out .= $i + 1;
			$out .= "</td>";
			$out .= "<td>";
			$out .= $data[$i]['FirstName']." ".$data[$i]['LastName'];
			$out .= "</td>";
			$out .= "<td align=center><input id=s{$i} name=m{$data[$i]['ID']}1 onblur='saveMarksSitting(studentid=\"".$data[$i]['ID']."\",field=\"{$field1}\",document.marks.m{$data[$i]['ID']}1.value,\"output".($i)."\",{$i})' type=text maxlength=6 onkeypress='return isNumberKey(event)' class=typeprofor value='".$data[$i][$field1]."'></td>";
			//$out .= "<td align=center><input id=t{$i} name=m{$data[$i]['ID']}2 onblur='saveMarks(studentid=\"".$data[$i]['ID']."\",field=\"{$field2}\",document.marks.m{$data[$i]['ID']}2.value,\"output".($i)."\",{$i})' type=text onkeypress='return isNumberKey(event)' class=typeprofor value='".$data[$i][$field2]."'></td>";
			$out .= "<td><div id=output".$i.">&nbsp;</div></td>";
			$out .= "</tr>";
		} 
	}
	echo $out;
	?>
</table>
</form>
<fieldset class=upload>
	<legend>Upload data in excel format</legend>
	<form action='' method=post enctype='multipart/form-data'>
	<input type=file name=exceldoc class=button><br>
	<input type=submit name=upload value=Upload class=button />
	</form>
</fieldset>
</body>
</html>