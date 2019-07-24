<?php
session_start();
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
$dept = $db->selectFields($tbl="tbl_course",$field=array("DeptID","Acronym"),$condition=array("TeacherID"=>$_SESSION['u_id']),$limit=null,$order="ORDER BY DeptID ASC",$indexed=true,$sign='=',$multiplereference=false,$distinct=true);
#var_dump($dept);
#var_dump($_GET);
$_GET['acronym'] = 'GNC';
if($_GET['acronym'] == ''){
	echo "<style> #course{background-color:#ff0000;}</style><span class=error>Click on the Course</span>";
	die;
}
#if(!preg_match('',$_GET['']))
$field1 = $_GET['acronym'].$_GET['term']; # $field2=$_GET['acronym'].$_GET['term']."2";
?>
<label id=styles></label>
<label id=styles2></label>
<form name=marks>
<table border=1 width=80% style='border-top:4px solid #fff;border-right:4px solid #fff;border-bottom:4px solid #fff;'>
	<tr><th colspan=5>Displine Term <?php echo $_GET['term'] ?> | S<?php echo $_GET['class'].$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true) ?></th></tr>
	<tr id=tableheader>
		<th width=20px>#</th><th>Name</th><th width=60px>Total/40</th><th>&nbsp;</th>
	</tr>
	<?php
	//select all student in selected class
	#var_dump($_GET);
	$student = $db->selectFields($tbl="tbl_student",$field=array("StudentID"),$condition=array("DeptID"=>$_GET['dept'],"Class"=>$_GET['class']),$limit=null,$order="",$indexed=true,$sign='=',$multiplereference=false,$distinct=false);
	#var_dump($student); die;
	foreach($student as $ids) $db->InsertIfNotExist($tbl="tbl_marks",$data=array("StudentID"=>$ids['StudentID']),$condition=array("StudentID"=>$ids['StudentID']),$auto_increment=true);
	$data = $db->selectInMoreTable($lbl=array("tbl"=>array("school_report_db`.`tbl_student",$dbname."`.`tbl_student",$dbname."`.`tbl_marks"),"fld"=>array("school_report_db`.`tbl_student"=>array("ID","FirstName","LastName"),$dbname."`.`tbl_student"=>array("StudentID"),$dbname."`.`tbl_marks"=>array($field1)),"condition"=>array("school_report_db`.`tbl_student`.`ID"=>$dbname."`.`tbl_student`.`StudentID",$dbname."`.`tbl_marks`.`StudentID"=>$dbname."`.`tbl_student`.`StudentID",$dbname."`.`tbl_student`.`DeptID"=>$_GET['dept'],$dbname."`.`tbl_student`.`Class"=>$_GET['class'])),$multirows=true,$indexed=true, $order="ORDER BY `FirstName` ASC, `LastName` ASC");
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
			$out .= "<td align=center><input id=s{$i} name=m{$data[$i]['ID']}1 onkeyup='if(document.marks.m{$data[$i]['ID']}1.value > 40){alert(document.marks.m{$data[$i]['ID']}1.value + \" Is greater than maximum\");document.marks.m{$data[$i]['ID']}1.value=40;}' onblur='saveMarks(studentid=\"".$data[$i]['ID']."\",field=\"{$field1}\",document.marks.m{$data[$i]['ID']}1.value,\"output".($i)."\",{$i})' type=text maxlength=6 onkeypress='return isNumberKey(event)' class=typeprofor value='".$data[$i][$field1]."'></td>";
			#$out .= "<td align=center><input id=t{$i} name=m{$data[$i]['ID']}2 onblur='saveMarks(studentid=\"".$data[$i]['ID']."\",field=\"{$field2}\",document.marks.m{$data[$i]['ID']}2.value,\"output".($i)."\",{$i})' type=text onkeypress='return isNumberKey(event)' class=typeprofor value='".$data[$i][$field2]."'></td>";
			$out .= "<td><div id=output".$i.">&nbsp;</div></td>";
			$out .= "</tr>";
		} 
	}
	echo $out;
	#var_dump($_GET);
	?>
</table>
</form>
<fieldset class=upload>
	<legend>Upload data in excel format | get the templete <a href='./dwnlds/?dept=<?php echo $_GET['dept'] ?>&class=<?php echo $_GET['class'] ?>&term=<?php echo $_GET['term'] ?>' target='_blank' >here</a></legend>
	<form action='' method=post enctype='multipart/form-data'>
	<input type=file name=exceldoc class=button><br>
	<input type=submit name=upload value=Upload class=button />
	</form>
</fieldset>
</body>
</html>