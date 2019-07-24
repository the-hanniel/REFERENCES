<?php
ob_start();
session_start();
/*Include the database configuration file*/
require_once("../../admin/includes/config.php");
/*Include the default function file*/
require_once("../../admin/includes/functions.php");
/*This function will check the session*/
checkSession();
require_once"../../LIB/config.php";
$db = new DBConnector("school_report_db".$_SESSION['year']);
#mysql_query("UPDATE `tbl_leave` SET `LeaveFrom`=NOW() WHERE `HR`=0");
if(isset($_GET['msg']) && ($_GET['msg'] == 'success'))
{
	$successMsg		=	"HOD Successfully Saved!";	
}
if(@$_GET['sql'] == 'allow.sql' && !empty($_GET['id'])){
	$db->updatecells(array("HR"=>1),"tbl_leave",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$successMsg		=	"Leave Successfully Approved!";
}
if(@$_GET['sql'] == "dismiss.sql" && !empty($_GET['id'])){
	$db->delete1row("leave_details",array("LeaveID"=>mysql_real_escape_string(trim($_GET['id']))));
	$db->delete1row("tbl_leave",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$errorMsg		=	"Leave Successfully Dismissed!";
}
if(isset($_POST['submit']))
{	
	#echo '<pre>';print_r($_POST);die;
	$dept = mysql_real_escape_string(trim($_POST['dept']));
	$hodName = mysql_real_escape_string(trim($_POST['name']));
	$uName = mysql_real_escape_string(trim($_POST['uname']));
	$upass = mysql_real_escape_string(trim($_POST['pass']));
	if($dept ==	'' || $hodName ==	'' || $uName == '' || $upass ==	'') {
		$errorMsg	=	'Error! Required Fields Cannot Be Left Blank!';	
	} elseif(sha1($_POST['password']) !== sha1($_POST['pass'])){
		$errorMsg = "Error! Password does not match!";
	} else {	
		$selectQuery			=	"SELECT * FROM `tbl_users` WHERE `UserName`='".$uName."'";
		$existFlag				=	recordAlreadyExist($selectQuery);
		if($existFlag)
		{
			$errorMsg			=	"Error! User Name Already Exists!";
		}	
		else
		{	
			$deptInsertQuery	= 	"INSERT INTO `tbl_users` (`ID`, `Name`, `UserName`,`Password`,`UserType`,`Dept`) VALUES (NULL, '{$hodName}', '{$uName}','".sha1($upass)."','2','{$dept}')";
			/*Call General Function to Insert the record*/
			$insertFlag			= 	insertOrUpdateRecord($deptInsertQuery , $_SERVER['PHP_SELF']);
			if(!$insertFlag)
			{
				$errorMsg		=	"Error!Unable to save department!";
			}
		}	
	}	
}
if(@$_POST['upload']){
	#var_dump($_FILES);die;
	/* upload the file to set it accessible **/
	move_uploaded_file($_FILES['exceldoc']['tmp_name'],"./tmpfile/".$_FILES['exceldoc']['name']);
	
	/* the following code will read the data in the excel file and insert them into database */
		//  Include PHPExcel_IOFactory
		include '../../LIB/Classes/PHPExcel/IOFactory.php';

		$inputFileName = "./tmpfile/".$_FILES['exceldoc']['name'];

		//  Read your Excel workbook
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		#echo "<pre>";var_dump($objPHPExcel);
		//  Get worksheet dimensions
		
		//check if the sheet full fill all needed data
		//fetch all sheets name
		$courses = $objPHPExcel->getSheetNames();
		
		//get the list of course is the system
		$coursesindb = $db->selectFields($tbl="tbl_course",$field=array('CourseName',"Acronym"),$condition=array("TeacherID"=>$_SESSION['u_id']),$limit=null,$order="",$indexed=true,$sign='=',$multiplereference=false,$distinct=false);
		#var_dump($coursesindb); die;
		
		//loop for indb courses to search wether the found course are available
		$validsheets = array(); $fields;
		foreach($courses as $index=>$sheetname){
			for($i=0;$i<count($coursesindb);$i++){
				if(in_array($sheetname,$coursesindb[$i])){
					$validsheets[$index] = $sheetname;
					$fields[$index] = $coursesindb[$i]['Acronym'];
				}
			}
		}
		#var_dump($fields); die;
		foreach($validsheets as $id=>$name){
			
			$sheet = $objPHPExcel->getSheet($id);
			#var_dump($sheet);
			#var_dump($objPHPExcel->__numberOfSheets());
			$highestRow = $sheet->getHighestRow(); 
			$highestColumn = $sheet->getHighestColumn();
			
			//initialise reference fields
			
			$dept; $class; $samestudentid; $morestudent=false; $morecounter=0; $term;
			
			//echo $fields[$id]."==>".$name;

			//  Loop through each row of the worksheet in turn
			for ($row = 1; $row <= $highestRow; $row++){
				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
												NULL,
												TRUE,
												FALSE);
				//my process here
				//echo "<pre>";var_dump($rowData);#  die;
				
				//complete the fields name;
				if($row == 1){
					$dept = $db->select1cell("tbl_dept","ID",array("Acronym"=>$rowData[0][0]),true);
					$class = $rowData[0][1];
					$fields[$id] .= $rowData[0][2];
					$row = 2;
				}
				
				//echo $fields[$id];
				
				//make data array to be saved
				$dataarray = array("StudentID"=>null,$fields[$id]."1"=>null,$fields[$id]."2"=>null);
				
				//print_r($dataarray);
				
				//search for student id
				$studentid = $db->selectFields($tbl="tbl_student",$field=array('StudentID'),$condition=array("FirstName"=>$rowData[0][1],"LastName"=>$rowData[0][2],"DeptID"=>$dept,"Class"=>$class),$limit=null,$order="",$indexed=true,$sign='=',$multiplereference=false,$distinct=false);
				//print_r($studentid);
				//echo "<br><br>";
				
				//fill the data array by new content
				if(count($studentid) == 1){
					$dataarray['StudentID'] = $studentid[0]["StudentID"];
					$dataarray[$fields[$id]."1"] = $rowData[0][3];
					$dataarray[$fields[$id]."2"] = $rowData[0][4];
				} else{
					//when more than one student with same name exist in the same class
					
					
				}
				//print_r($dataarray);
				
				//insert or update the found data into datastore
				$db->InsertOrUpdate($tbl="tbl_marks",$dataarray,$id_increment=true,$condition=array("StudentID"=>$dataarray['StudentID']),$referencefield=$fields[$id]."1",$replace=true);
				/*
				#var_dump($rowData);die;
				#echo "OK 0";
				//check for compatibility
				if($row == 1 && !preg_match("/^".$rowData[0][0].$rowData[0][1]."/",$fields[$id])) break;
				#echo "<pre>";var_dump($rowData); echo "<hr>"; die;
				#echo "OK";
				//skip the first two rows because they are identification rows
				if($row<2){
					$row = 3;
					continue;
				}
				#echo "OK";
				//search the of the student id that correspond to name in db
				$studentid = $db->selectFields($tbl="tbl_student",$field=array('ID'),$condition=array("FirstName"=>mysql_real_escape_string($rowData[0][1]),"LastName"=>mysql_real_escape_string($rowData[0][2])),$limit=null,$order="",$indexed=true,$sign='=',$multiplereference=false,$distinct=false);
				
				//if found more than one student id
				if(count($studentid) > 1){
					$samestudentid = $studentid;
					$morestudent = true;
				}
				$dataarray;
				if(count($studentid)>0){
					//if still there is more than one student with the same same
					if($morestudent && $studentid[$morecounter]['ID'] == $samestudentid[$morecouter]['ID']){
						$dataarray['StudentID'] = $samestudentid[$morecouter]['ID'];
						unset($samestudentid[$morecouter]);
						$morecounter++;
						if($samestudentid[$morecouter] == null) $morestudent = false;
					} else{
						#var_dump($studentid); echo "<br>";
						$dataarray['StudentID'] = $studentid[0]['ID'];
					}
				}
				//var_dump($dataarray);die;
				//make field to get data * /
				
				$fieldss = array(1=>$fields[$id].$term."1",$fields[$id].$term."2");
				if(count($rowData[0]) == 5){
					//$counter = 1;
					//start sql build
					//foreach($rowData[0] as $key=>$value){
					//	if($key != 0 ) {
							$dataarray[$fieldss[1]] = $rowData[0][3];
							$dataarray[$fieldss[2]] = $rowData[0][4];
					//	}
					//}
					#var_dump($dataarray); die;
					$db->InsertOrUpdate($tbl="tbl_marks",$dataarray,$id_increment=true,$condition=array("StudentID"=>$dataarray['StudentID']),$referencefield=$fieldss[1],$replace=true);
				}*/
				//  Insert row data array into your database of choice here
			}
		//	echo "<hr><hr>";
		}
	/* end of excel processing */
	//delete the uploaded files
	//die;
	unlink("./tmpfile/".$_FILES['exceldoc']['name']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>SRS Teacher Section</title>
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
<div class="main">
	<?php
		/*This function will return the header string with menu information*/
		echo plotHeaderMenuInfo(basename(__FILE__),2,$db);
	?>
	<div class="body">
		<div class="main_body">
			<h2>Student Marks Entry Interface (Academic Year <?php echo $_SESSION['year'] ?>)</h2>
			<?php
				/*Display the Messages*/
				if(isset($errorMsg))
				{
					echo "<span class = 'error'>{$errorMsg}</span><br />";	
				}
				elseif(isset($successMsg))
				{
					echo "<span class = 'success'>{$successMsg}</span><br />";	
				}
				#var_dump($_SESSION['year']);
				
				//select all department that implys the logged teacher
				#var_dump($_SESSION);
				$dept = $db->selectFields($tbl="tbl_course",$field=array("DeptID"),$condition=array("TeacherID"=>$_SESSION['u_id']),$limit=null,$order="ORDER BY DeptID ASC",$indexed=true,$sign='=',$multiplereference=false,$distinct=true);
				#echo"<pre>";var_dump($dept);echo"</pre>";
			?>
			<form name=reference>
				<input type=hidden name=error>
				<table>
					<tr>
						<td>Department</td><td>Class</td><td>Term</td><td>Course</td>
					</tr>
					<tr>
						<td>
							<select name=dept class=typeproform>
								<?php
								for($i=0;$i<count($dept);$i++){
									echo "<option onclick='navigation1(document.reference.dept.value); navigation1(document.reference.dept.value,document.reference.classid.value,2,\"courseform\");studentMarksTeacher(document.reference.dept.value,document.reference.classid.value,document.reference.term.value,document.reference.courseid.value);' value='".$dept[$i]['DeptID']."'>".$db->select1cell("tbl_dept","Acronym",array("ID"=>$dept[$i]['DeptID']),true);
								}
								?>
							</select>
						</td>
						<td>
							<div id='classform'><script>navigation1(document.reference.dept.value);</script></div>
						</td>
						<td>
							<select name=term class=typeproform>
								<?php
								for($i=1;$i<=3;$i++){
									echo "<option onclick='studentMarksTeacher(document.reference.dept.value,document.reference.classid.value,document.reference.term.value,document.reference.courseid.value);'>".$i;
								}
								//sleep(2);
								?>
							</select>
						</td>
						<td id=course>
							<div id=courseform><script>alert("Course Could not be loaded, Retry");navigation1(document.reference.dept.value,document.reference.classid.value,2,'courseform');</script></div>
						</td>
					</tr>
				</table>
			</form>
			<link rel="stylesheet" type="text/css" href="../../admin/css/tb.css" />
			<div id=studentmarks><script>studentMarksTeacher(document.reference.dept.value,document.reference.classid.value,document.reference.term.value,'');</script></div>
			<?php /*
			<table width=100%>
				<tr>
					<th colspan=7>
						All Requested Leave Approved by Head of Departments
					</th>
				</tr>
				<tr id=tableheader>
					<th> # </th> <th> Name </th> <th> Leave Type </th> <th> Start Date </th> <th> End Date </th> <th> HR </th>
				</tr>
				<?php
				$requestString = array("tbl"=>array("tbl_leave","leave_details","tbl_users"),"fld"=>array("tbl_users"=>array("Name"),"tbl_leave"=>array("ID","UserID","LeaveFrom","LeaveTo","HOD","HR"),"leave_details"=>array("Replacement","Details","Address")),"condition"=>array("tbl_leave`.`ID"=>"leave_details`.`LeaveID","tbl_leave`.`UserID"=>"tbl_users`.`ID","tbl_leave`.`HOD"=>1,"tbl_leave`.`HR"=>0));
				#echo "<pre>";var_dump($requestString );return;
				#$data = $db->selectInMoreTable($requestString,true,true, "ORDER BY ID DESC");
				//$data = $db->selectFields("tbl_users",array("ID","Name","Dept","UserType"),array("UserType"=>2),null,"",true,'=');
				$out = "";# echo "<pre>"; var_dump($data);
				if(count($data) == 0) echo "<font color=red>No Request!</font>";
				else{
					for($i=0;$i<count($data);$i++){
						$out .= "<tr>";
						$out .= "<td>";
						$out .= $i + 1;
						$out .= "</td> <td>".$data[$i]['Name']."</td> <td>".$data[$i]['Details']."</td> <td align=center>".$data[$i]['LeaveFrom']."</td><td align=center><a href='./' onclick='return ChangeDays(\"".$data[$i]['ID']."\",\"".$data[$i]['UserID']."\")'><abbr title='Click To Edit Leave End'>".$data[$i]['LeaveTo']."</abbr></a></td><td align=center>";
						$out .= $data[$i]['HR']==1?"<font color=green>Approved</font>":"<abbr title='Approved'><a href='./?sql=allow.sql&id=".$data[$i]['ID']."' onclick='return confirmFunction(\"Do You Want To Approve The Leave For ".$data[$i]['Name']." \")'>Approve</a></abbr> | <abbr title='Dismiss'><a href='./?sql=dismiss.sql&id=".$data[$i]['ID']."' onclick='return confirmFunction(\"Do You Want To Dismiss The Leave For ".$data[$i]['Name']." \")'>Dismiss</a></abbr>";
						$out .= "</td>";
						$out .= "</td></tr>";
					}
				}
				echo $out;
				?>
			</table>
			<?php */ ?>
		</div><!-- End of main_body div(main white div)-->
		<?php
			/*This function will return the logo div string to the sidebody*/
			//echo plotLogoDiv("../../admin/images/logo.png");
			//echo plotSearchDiv('department_search.php');
			/*This function will list the departments*/
			//echo listDepartment();
		?>		
	<div class="clr"></div>
	</div><!-- End of Body div-->
</div><!--End of Main Div-->
<?php
	/*This function will return the footer div information*/
	echo plotFooterDiv();
?>
<script type="text/javascript">
<!--
	/*var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "custom",{isRequired:true,characterMasking:/[a-zA-Z ]/,
						useCharacterMasking:true, validateOn:["change"]}); */
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "custom",{isRequired:true,characterMasking:/[a-zA-Z ]/,
						useCharacterMasking:true, validateOn:["change"]});	
	var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9 ]/,
						useCharacterMasking:true, validateOn:["change"]});	
	var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9!@#$%^&*_?>< ]/,
						useCharacterMasking:true, validateOn:["change"]});	
	var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9!@#$%^&*_?>< ]/,
						useCharacterMasking:true, validateOn:["change"]});					
-->	
</script>
</body>
</html>
<?php
	ob_end_flush();
?>