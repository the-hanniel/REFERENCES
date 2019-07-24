<?php
ob_start();
session_start();
/*Include the database configuration file*/
require_once("../../admin/includes/config.php");
/*Include the default function file*/
require_once("../../admin/includes/functions.php");
require_once"../../LIB/config.php";
$db = new DBConnector("school_report_db".$_SESSION['year']);

$year = $_SESSION['year'];

/*This function will check the session*/
checkSession();
if(isset($_GET['msg']))
{
	$successMsg		=	$_GET['msg'];	
}
if(isset($_GET['error']))
{
	$errorMsg		=	$_GET['error'];	
}

$update = array("msg"=>"Register","btn"=>array("name"=>"submit","value"=>"Add User"));
if(@$_GET['sql'] == 'update.sql' && !empty($_GET['id'])){
	$update['msg'] = "Update"; $update['btn'] = array("name"=>'update',"value"=>"Save Updates");
	$db->delete1row("school_report_db".$_SESSION['year']."`.`tbl_marks",array("StudentID"=>mysql_real_escape_string(trim($_GET['id']))));
	#$data = $db->selectOneRowFromTable("school_report_db".$_SESSION['year']."`.`tbl_student",array("ID"=>mysql_real_escape_string(trim($_GET['id']))),true);
	$data = $db->selectInMoreTable($lbl=array("tbl"=>array("school_report_db".$year."`.`tbl_student","tbl_dept","school_report_db`.`tbl_student"),"fld"=>array("school_report_db`.`tbl_student"=>array("FirstName","LastName","DOB"),"school_report_db".$year."`.`tbl_student"=>array("ID","Class","DeptID"),"tbl_dept"=>array("Acronym")),"condition"=>array("school_report_db`.`tbl_student`.`ID"=>"school_report_db".$year."`.`tbl_student`.`StudentID","school_report_db".$year."`.`tbl_student`.`StudentID"=>$_GET['id'],"school_report_db".$year."`.`tbl_student`.`DeptID"=>"school_report_db".$year."`.`tbl_dept`.`ID")),$multirows=true,$indexed=true, $order="ORDER BY `tbl_dept`.`Acronym` ASC, `tbl_student`.`Class` ASC, `school_report_db`.`tbl_student`.`FirstName` ASC, `school_report_db`.`tbl_student`.`LastName` ASC");
	#echo "<pre>";var_dump($data);echo "</pre>";
	$dept = $data[0]['DeptID'];
	$fname = $data[0]['FirstName'];
	$lname = $data[0]['LastName'];
	$class = $data[0]['Class'];
	$dob = $data[0]['DOB'];
}
if(@$_GET['sql'] == "delete.sql" && !empty($_GET['id'])){
	$db->delete1row("school_report_db".$_SESSION['year']."`.`tbl_marks",array("StudentID"=>mysql_real_escape_string(trim($_GET['id']))));
	$db->delete1row("school_report_db".$_SESSION['year']."`.`tbl_student",array("StudentID"=>mysql_real_escape_string(trim($_GET['id']))));
	$db->delete1row("school_report_db`.`tbl_student",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$successMsg		=	"Student Successfully Deleted!";
}
if(@$_GET['sql'] == "reset.sql" && !empty($_GET['id'])){
	$db->updatecells(array("Password"=>sha1("123")),"school_report_db`.`tbl_users",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$successMsg		=	"Password Successfully Reseted to <b>123</b>!";
}
if(@$_POST['update']){
	#var_dump($_POST); die;
	if($db->updatecells(array("DeptID"=>mysql_real_escape_string(trim($_POST['dept'])),"Class"=>$_POST['class']),"school_report_db".$_SESSION['year']."`.`tbl_student",array("StudentID"=>mysql_real_escape_string(trim($_POST['id']))))){
		if($db->updatecells(array("FirstName"=>mysql_real_escape_string(trim($_POST['fname'])),"LastName"=>mysql_real_escape_string(trim($_POST['lname'])),"DOB"=>$_POST['dob']),"school_report_db`.`tbl_student",array("ID"=>mysql_real_escape_string(trim($_POST['id']))))){
			$successMsg		=	"Student Successfully Update!";
		}
	} else{
		$errorMsg = "Error! Fail To Update!";
	}
}
if(@$_POST['promot']){
	#echo "<pre>";#var_dump($_POST); die;
	$all=true;#die;
	$count = 0;
	foreach($_POST as $id=>$status){
		if(is_int($id)){
			//select the student identification
			$student = $db->selectFields($tbl="school_report_db".$_POST['original']."`.`tbl_student",$field=array("*"),$condition=array("ID"=>$id),$limit=null,$order="",$indexed=true,$sign='=',$multiplereference=false);
			#var_dump($student);die;
			//insert those data into the new table
			if($student[0]['Class'] != 6){
				$insert = array("StudentID"=>$student[0]['StudentID'],"DeptID"=>$student[0]['DeptID'],"Class"=> ++$student[0]['Class']);
				#die;
				if($db->InsertData($tbl="school_report_db".$_SESSION['year']."`.`tbl_student",$data=$insert,$id_increment=true)){
					$count++;
				} else{
					$all = false;
				}
			}
		}
	}
	if($all) header("Location:".$_SERVER['PHP_SELF']."?msg={$count}+Selected+Student+".($count>1?"Are":"Is")."+In+S".($student[0]['Class']).$db->select1cell("tbl_dept","Acronym",array("ID"=>$student[0]['DeptID']),true));
	else header("Location:".$_SERVER['PHP_SELF']."?error=Some Student Can't Be Promoted!!!");
	exit;
}
if(isset($_POST['submit']))
{	
	#echo '<pre>';print_r($_POST);die;
	$dept = mysql_real_escape_string(trim($_POST['dept']));
	$lname = mysql_real_escape_string(trim($_POST['lname']));
	$fname = mysql_real_escape_string(trim($_POST['fname']));
	$class = mysql_real_escape_string(trim($_POST['class']));
	$dob = mysql_real_escape_string(trim($_POST['dob']));
	if($dept ==	'' || $fname ==	'' || $lname == '' || $class ==	'') {
		$errorMsg	=	'Error! Required Fields Cannot Be Left Blank!';	
	} else {	
		/*$selectQuery			=	"SELECT * FROM `school_report_db`.`tbl_users` WHERE `UserName`='".$uName."'";
		$existFlag				=	recordAlreadyExist($selectQuery);
		if($existFlag)
		{
			$errorMsg			=	"Error! User Name Already Exists!";
		}	
		else
		{	*/
			$deptInsertQuery	= 	"INSERT INTO `school_report_db`.`tbl_student` (`ID`, `FirstName`,`LastName`,`DOB`) VALUES (NULL, '{$fname}','{$lname}','{$dob}')";
			/*Call General Function to Insert the record*/
			$insertFlag			= 	insertOrUpdateRecord($deptInsertQuery , $_SERVER['PHP_SELF'],null,'',false);
			//select the id of inserted record
			$id = $db->select1cell("school_report_db`.`tbl_student","ID",array("FirstName"=>$fname,"LastName"=>$lname,"DOB"=>$dob,),true);
			#echo $id; die;
			$data = array("StudentID"=>$id,"DeptID"=>$dept,"Class"=>$class,"StudentPhoto"=>"");
			if($db->InsertIfNotExist($tbl="tbl_student",$data,$condition=array("StudentID"=>$id),$auto_increment=true)){
				header("Location:".$_SERVER['PHP_SELF']."?msg=Successfully Saved!");
				exit;
			}
			#die;
			if(!$insertFlag)
			{
				$errorMsg		=	"Error!Unable to save Student!";
			}
		//}	
	}	
}	
if(@$_POST['upload'] && $_FILES['exceldoc']['error'] == 0 && preg_match('/.xl/',$_FILES['exceldoc']['name'])){
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
		for($id=0;$id < $objPHPExcel->getSheetCount();$id++){

			$sheet = $objPHPExcel->getSheet($id);
			#var_dump($sheet);
			#var_dump($objPHPExcel->__numberOfSheets());
			$highestRow = $sheet->getHighestRow(); 
			$highestColumn = $sheet->getHighestColumn();

			//  Loop through each row of the worksheet in turn
			$dept=0; $class=4;
			for ($row = 2; $row <= $highestRow; $row++){ 
				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
												NULL,
												TRUE,
												FALSE);
				#if($rowData[0][0]);
				#echo "<pre>".$row." ";var_dump($rowData); echo "<hr>";
				
				//get the class and departement of the sheet
				if($row == 2){
					$dept = $db->select1cell("tbl_dept","ID",array("Acronym"=>$rowData[0][0]),true);
					$class = $rowData[0][1];
					$row = 3;
					continue;
				}
				$fields = array(1=>"FirstName","LastName","DOB");
				if($rowData[0][1] == null || $rowData[0][2] == null) continue;
				if(count($rowData[0]) == 4 && !in_array($rowData[0][1],$fields)){
					$dataarray = array();
					$counter = 1;
					//start sql build
					foreach($rowData[0] as $key=>$value){
						if($key != 0 ) {
							$dataarray[$fields[$counter++]] = $value;
						}
					}
					#var_dump($dataarray); echo "<br><br>";
					$db->InsertData($tbl='school_report_db`.`tbl_student',$dataarray,$id_increment=true);
					//prepare data for other tables
					$studentid = mysql_insert_id();
					$insertarray = array("StudentID"=>$studentid,"DeptID"=>$dept,"Class"=>$class,"StudentPhoto"=>"");
					$db->InsertIfNotExist($tbl="tbl_student",$data=$insertarray,$condition=array("StudentID"=>$studentid),$auto_increment=true);
				}
				//  Insert row data array into your database of choice here
			}
			#echo "<hr>";
		}
	/* end of excel processing */
	//delete the uploaded files
	unlink("./tmpfile/".$_FILES['exceldoc']['name']);
	header("Location:".$_SERVER['PHP_SELF']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Student List</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!--Link to Validation JS source File -->
	<script type = 'text/javascript' language='javascript' src = '../../admin/js/validation.js'></script>
	<!--Link to the template css file-->
	<link rel="stylesheet" type="text/css" href="../../admin/css/style.css" />
	<!--Link to Favicon -->
	<link rel="icon" href="../../admin/images/lgo.jpg"/>
	<!-- Spry Stuff Starts here-->
	<link href="../../admin/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../admin/spry/textfieldvalidation/SpryValidationTextField.js"></script>
	<!-- Spry Stuff Ends Here-->
</head>
<body>
<div class="main">
	<?php
		/*This function will return the header string with menu information*/
		echo plotHeaderMenuInfo(basename(__FILE__),1,$db);
	?>
	<div class="body">
		<div class="main_body">
			<?php
			if(@$_GET['card'] == sha1('1')){
				?>
				<h2 style="font-size:23px;">Students Cards (Academic Year <?php echo $_SESSION['year'] ?>)</h2>
				
				<!-- form to help selecting classes -->
				<form name=slist>
					<table><tr><td>Department</td><td> 
					<?php
						$dpt = $dept="";
						$dept = $db->selectFields($tbl='tbl_dept',$field=array("ID","DepartmentName","Acronym"),$condition=array("ID"=>array($db->select1cell("tbl_dept","ID",array("Acronym"=>"Administration"),true),$db->select1cell("tbl_dept","ID",array("Acronym"=>"GNC"),true))),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='!=',true);
						?>
						<select name=dept class = 'typeproform'>
							<?php
							for($i=0;$i<count($dept);$i++) echo "<option onclick='studentList(document.slist.dept.value,document.slist.classid.value,0,12,\"cardstudentlist.php\",0,\"".$_SESSION['year']."\")' value='".$dept[$i]['ID']."' ".($dept[$i]['ID'] == $dpt?"selected":"").">".($dept[$i]['Acronym'] != 'Administration'?$dept[$i]['Acronym']:"");
							?>
						</select></td></tr><tr><td>
					Class</td><td> 
					<select name=classid class=typeproform>
						<?php for($i=4;$i<=6;$i++) echo "<option onclick='studentList(document.slist.dept.value,document.slist.classid.value,0,12,\"cardstudentlist.php\",0,\"".$_SESSION['year']."\")'>".$i; ?>
					</select></td></tr>
					<!--<tr><td align=center colspan=2><br><br><br><input style='cursor:pointer;' type=button onclick='window.location="<?php //echo $_SERVER['PHP_SELF'] ?>?card=<?php //echo sha1('1') ?>"' class=button value='Complete Student Card! ' /></td></tr>
					-->
					</table>
				</form>
				<!-- end of the form -->
				
				<!-- div to display student list -->
				<table border=1 width=550>
					<tr valign=top>
						<td>
							<link rel=stylesheet type='text/css' href="../../admin/css/tb.css" />
							<div id='studentlist'><script>studentList(deptid=3,classid=4,start=0,end=12,page='cardstudentlist.php',move=0,year='<?php echo $_SESSION['year'] ?>')</script></div>
						</td>
						<td width=350px>
							<?php
							require_once"./ajaximage/index.php";
							?>
						</td>
					</tr>
				</table>
				<!-- end of student list display -->
				<br />
				<?php
			} else{
				?>
				<h2><?php echo $update['msg'] ?> Students (Academic Year <?php echo $_SESSION['year'] ?>)</h2>
				
				<?php
					/*Display the Messages*/
					if(isset($errorMsg))
					{
						echo "<span class = 'error'>{$errorMsg}</span>";	
					}
					elseif(isset($successMsg))
					{
						echo "<span class = 'success'>{$successMsg}</span>";	
					}
				?>
				<br/>	
				<table border=0 width=100%>
					<tr valign=top>
						<td width=60%>
							<form method = 'POST' action = "<?php echo $_SERVER['PHP_SELF'];?>">
								<table width ="350" border = '0' cellspacing = '0' cellpadding = '0'>
								<!--<tr>
									<td width = '120' height = '30'>
										<strong>User Type</strong>
										<span class = 'mandatory'>*</span>
									</td>
									<td height = '30'>
										<label><input type=radio name=userType value=1>Contract</label>
										<label><input type=radio name=userType value=2 checked>Permenant</label>
									</td>
								</tr>-->
								<?php
								if(@$_GET['sql'] == 'update.sql') echo "<input type=hidden name=id value='".$_GET['id']."' />";
								?>
								<tr   style="font-size:16px;">
									<td width = '120' height = '30'>
										<strong>Department</strong>
										<span class = 'mandatory'>*</span>
									</td>
									<td height = '30'>
										<?php
										$dpt = @$dept;
										#echo $dpt;
										$dept = $db->selectFields($tbl='tbl_dept',$field=array("ID","DepartmentName","Acronym"),$condition=array("ID"=>array($db->select1cell("tbl_dept","ID",array("Acronym"=>"Administration"),true),$db->select1cell("tbl_dept","ID",array("Acronym"=>"GNC"),true))),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='!=',true);
										?>
										<select name=dept class = 'typeproforms'>
											<?php
											for($i=0;$i<count($dept);$i++) echo "<option value='".$dept[$i]['ID']."' ".($dept[$i]['ID'] == $dpt?"selected":"").">".$dept[$i]['DepartmentName'].($dept[$i]['Acronym'] != 'Administration'?"(".$dept[$i]['Acronym'].")":"");
											?>
										</select>
									</td>
								</tr><!--End of department code row-->
								<tr   style="font-size:16px;">
									<td width = '120' height = '30'>
										<strong>Family Name  </strong>
										<span class = 'mandatory'>*</span>
									</td>
									<td height = '30'>
										<span id="sprytextfield1">
											<input type = 'text' name = 'fname' id = 'name' 
											class = 'typeproforms'  value = "<?php if(isset($fname))echo $fname;?>"/>
										</span>	
									</td>
								</tr>
								<tr  style="font-size:16px;">
									<td width = '120' height = '30'>
										<strong>Other Name  </strong>
										<span class = 'mandatory'>*</span>
									</td>
									<td height = '30'>
										<span id="sprytextfield2">
											<input type = 'text' name = 'lname' id = 'name' 
											class = 'typeproforms'  value = "<?php if(isset($lname))echo $lname;?>"/>
										</span>	
									</td>
								</tr>
								<tr  style="font-size:16px;">
									<td width = '120' height = '30'>
										<strong>Date Of Birth  </strong>
										<span class = 'mandatory'>*</span>
									</td>
									<td height = '30'>
										<span id="sprytextfield13">
											<?php include_once"../../admin/includes/calender.php" ?>
										</span>	
									</td>
								</tr>
								<tr   style="font-size:16px;">
									<td width = '120' height = '30'>
										<strong>Class</strong>
										<span class = 'mandatory'>*</span>
									</td>
									<td height = '30'>
										<select name=class class = 'typeproforms'>
											<?php
											/*if(@$_GET['sql'] == 'update.sql'){
												echo "<option>".$class;
											} else{*/
												?>
												<option <?php if(@$class == 4) echo "selected" ?> >4<option <?php if(@$class == 5) echo "selected" ?> >5<option <?php if(@$class == 6) echo "selected" ?> >6
												<?php
											//}
											?>
										</select>
									</td>
								</tr><!--
								<tr>
									<td width = '120' height = '30'>
										<strong>Password  </strong>
										<span class = 'mandatory'>*</span>
									</td>
									<td height = '30'>
										<span id="sprytextfield3">
											<input type = 'password' maxlength='8' <?php // if(@$_GET['sql'] == 'update.sql') echo "readonly"; ?> name = 'password' id = 'uname' 
											class = 'typeproforms'  value = "<?php // if(isset($upass)) echo $upass ?>"/>
										</span>	
									</td>
								</tr>
								<tr>
									<td width = '120' height = '30'>
										<strong>Confirm  </strong>
										<span class = 'mandatory'>*</span>
									</td>
									<td height = '30'>
										<span id="sprytextfield4">
											<input type = 'password' <?php // if(@$_GET['sql'] == 'update.sql'){ echo "readonly"; }?> maxlength='8' name = 'pass' id = 'uname' 
											class = 'typeproforms'  value = "<?php // if(isset($upass)) echo $upass ?>"/>
										</span>	
									</td>
								</tr><!--End of department name row-->
								<tr>
									<td>&nbsp;</td>
									<td height = '30'>
										<input type = 'submit' name = '<?php echo $update['btn']['name'] ?>' class = 'button' value = '<?php echo $update['btn']['value'] ?>' />
									</td>
								</tr>
								</table>
							</form><!-- End of form-->
						</td>
						<td>
							<fieldset class=upload>
								<legend style='text-align:center'>Get Student List Templete <a href='./dwnlds/' target='_blank'>here</a></legend>
								<form method=post enctype='multipart/form-data'>
									<input class=button type=file name=exceldoc />
									<input type=submit name=upload value='Import' class=button />
								</form>
								Upload A list of student from EXCEL format
								<BR/>
								<form name=slist style='border-top:3px solid #eee;' >
									<table><tr><td>Department</td><td> 
									<?php
										$dpt = $dept="";
										$dept = $db->selectFields($tbl='tbl_dept',$field=array("ID","DepartmentName","Acronym"),$condition=array("ID"=>array($db->select1cell("tbl_dept","ID",array("Acronym"=>"Administration"),true),$db->select1cell("tbl_dept","ID",array("Acronym"=>"GNC"),true))),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='!=',true);
										?>
										<select name=dept class = 'typeproform'>
											<?php
											for($i=0;$i<count($dept);$i++) echo "<option onclick='studentList(document.slist.dept.value,document.slist.classid.value)' value='".$dept[$i]['ID']."' ".($dept[$i]['ID'] == $dpt?"selected":"").">".($dept[$i]['Acronym'] != 'Administration'?$dept[$i]['Acronym']:"");
											?>
										</select></td></tr><tr><td>
									Class</td><td> 
									<select name=classid class=typeproform>
										<?php for($i=4;$i<=6;$i++) echo "<option onclick='studentList(document.slist.dept.value,document.slist.classid.value)'>".$i; ?>
									</select></td></tr><tr><td align=center colspan=2><br><input style='cursor:pointer;' type=button onclick='window.location="<?php echo $_SERVER['PHP_SELF'] ?>?card=<?php echo sha1('1') ?>"' class=button value='Complete Student Card! ' /></td></tr></table>
								</form>
							</fieldset>
						</td>
					</tr>
				</table>
				<br/>
				<link rel=stylesheet type='text/css' href="../../admin/css/tb.css" />
				<div id='studentlist'><script>studentList(deptid=3,classid=4)</script></div>
				<?php
			}
			?>
		</div><!-- End of main_body div(main white div)-->
		<?php
			/*This function will return the logo div string to the sidebody*/
			//echo plotLogoDiv("../../admin/images/lgo.jpg");
			//echo plotSearchDiv('department_search.php');
			/*This function will list the departments*/
			//echo listDepartment();
		?>		
	<div class="clr"></div>
	</div><!-- End of Body div-->
</div><!--End of Main Div-->
<?php
	/*This function will return the footer div information*/
	echo plotFooterDiv("../../admin/images/lgo.png");
?>
<script type="text/javascript">
<!--
	/*var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "custom",{isRequired:true,characterMasking:/[a-zA-Z ]/,
						useCharacterMasking:true, validateOn:["change"]}); */
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "custom",{isRequired:true,characterMasking:/[a-zA-Z ]/,
						useCharacterMasking:true, validateOn:["change"]});	
	var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "custom",{isRequired:true,characterMasking:/[a-zA-Z ]/,
						useCharacterMasking:true, validateOn:["change"]});	
	var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9!@#$%^&*_?>< ]/,
						useCharacterMasking:true, validateOn:["change"]});	
	var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9!@#$%^&*_?>< ]/,
						useCharacterMasking:true, validateOn:["change"]});
	var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9@.]/,
						useCharacterMasking:true, validateOn:["change"]});		
-->	
</script>
</body>
</html>
<?php
	ob_end_flush();
?>