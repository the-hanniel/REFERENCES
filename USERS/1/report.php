<?php
ob_start();
session_start();
#$_SESSION['year'] = 2013;
/*Include the database configuration file*/
require_once("../../admin/includes/config.php");
/*Include the default function file*/
require_once("../../admin/includes/functions.php");
require_once"../../LIB/config.php";
$year = (@$_GET['year']?addslashes(htmlspecialchars(trim($_GET['year']))):$_SESSION['year']);
$db = new DBConnector($dbname="school_report_db".$year);

/*This function will check the session*/
checkSession();
if(isset($_GET['msg']) && ($_GET['msg'] == 'success'))
{
	$successMsg		=	"User Successfully Saved!";
}

$update = array("msg"=>"","btn"=>array("name"=>"submit","value"=>"Add User"));
if(@$_GET['sql'] == 'update.sql' && !empty($_GET['id'])){
	$update['msg'] = "Update"; $update['btn'] = array("name"=>'update',"value"=>"Save Updates");
	$data = $db->selectOneRowFromTable("tbl_users",array("ID"=>mysql_real_escape_string(trim($_GET['id']))),true);
	#var_dump($data);
	$dept = $data['Dept'];
	$hodName = $data['Name'];
	$email = $data['Email'];
	$uName = $data['UserName'];
	$upass = "********";
}
if(@$_GET['sql'] == "delete.sql" && !empty($_GET['id'])){
	$db->delete1row("tbl_users",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$successMsg		=	"User Successfully Deleted!";
}
if(@$_GET['sql'] == "reset.sql" && !empty($_GET['id'])){
	$db->updatecells(array("Password"=>sha1("123")),"tbl_users",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$successMsg		=	"Password Successfully Reseted to <b>123</b>!";
}
if(@$_POST['update']){
	if($db->updatecells(array("Dept"=>mysql_real_escape_string(trim($_POST['dept'])),"Name"=>$hodName = mysql_real_escape_string(trim($_POST['name'])),"Email"=>mysql_real_escape_string(trim($_POST['email']))),"tbl_users",array("UserName"=>mysql_real_escape_string(trim($_POST['uname']))))){
		$successMsg		=	"Profile Successfully Update!";
	} else{
		$errorMsg = "Error! Fail To Update!";
	}
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
			$deptInsertQuery	= 	"INSERT INTO `tbl_users` (`ID`, `Name`,`Email`,`UserName`,`Password`,`UserType`,`Dept`,`ContractType`) VALUES (NULL, '{$hodName}','".$_POST['email']."' ,'{$uName}','".sha1($upass)."','5','{$dept}','".$_POST['userType']."')";
			/*Call General Function to Insert the record*/
			$insertFlag			= 	insertOrUpdateRecord($deptInsertQuery , $_SERVER['PHP_SELF']);
			if(!$insertFlag)
			{
				$errorMsg		=	"Error!Unable to save user!";
			}
		}
	}
}
if(@$_POST['upload']){
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
			for ($row = 1; $row <= $highestRow; $row++){
				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
												NULL,
												TRUE,
												FALSE);
				$fields = array(1=>"FirstName","LastName","DeptID","Class");
				if(count($rowData[0]) == 5 && !in_array($rowData[0][1],$fields)){
					$dataarray = array();
					$counter = 1;
					//start sql build
					foreach($rowData[0] as $key=>$value){
						if($key != 0 ) {
							$dataarray[$fields[$counter++]] = $value;
						}
					}
					$db->InsertData($tbl='tbl_student',$dataarray,$id_increment=true);
				}
				#echo "<pre>";var_dump($rowData); echo "<hr>";
				//  Insert row data array into your database of choice here
			}
			#echo "<hr><hr>";
		}
	/* end of excel processing */
	//delete the uploaded files
	unlink("./tmpfile/".$_FILES['exceldoc']['name']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Student Report</title>
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.22/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

	<script>
		function checkIt() {

			var values = $('#myInput').val().toLowerCase();
			$("#stdListes tr").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(values) > -1)
			});

		}
	</script>

	<!-- Spry Stuff Ends Here-->
</head>
<body>
<div class="main" style='border:0px solid green;'>
	<?php
		/*This function will return the header string with menu information*/
		echo plotHeaderMenuInfo(basename(__FILE__),1,$db);

	?>
	<script>
		function refleshPage(year){
			window.location='<?php echo $_SERVER['PHP_SELF']."?year=" ?>' + year;
		}
	</script>
	<style>
		.typeproform{
			width:80px;
		}
	</style>
	<div class="body">
		<div class="main_body" style='width:650px; border:0px solid #f00;'>
			<h2 style="font-size:23px;">Complete Class Selection <button onclick="Export()">Print</button></h2>
			<table>
				<tr>
					<td>
						<form name=fyear>
							<?php
							$years = $db->selectFields($tbl='school_report_db`.`tbl_years',$field=array('Year'),$condition=null,$limit=null,$order="ORDER BY `Year` ASC",$indexed=true,$sign='=',$multiplereference=false,$distinct=false);
							?>
							<select name=year id=year class=typeproform>
								<?php for($i=0;$i<count($years);$i++) echo "<option onclick='refleshPage(document.fyear.year.value);' ".($years[$i]['Year'] == $year?"selected":"").">".$years[$i]['Year']; ?>
							</select>
						</form>
						<?php /*
						<h2><?php echo $update['msg'] ?> Students Marks</h2>
						<?php
							/*Display the Messages* /
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
									<td width=60%><!--
										<form method = 'POST' action = "<?php// echo $_SERVER['PHP_SELF'];?>">
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
											</tr>- ->
											<?php
											//if(@$_GET['sql'] == 'update.sql') echo "<input type=hidden name=id value='".$_GET['id']."' />";
											?>
											<tr>
												<td width = '120' height = '30'>
													<strong>Department</strong>
													<span class = 'mandatory'>*</span>
												</td>
												<td height = '30'>
													<?php
													//$dpt = $dept="";
													//$dept = $db->selectFields($tbl='tbl_dept',$field=array("ID","DepartmentName","Acronym"),$condition=array("ID"=>array($db->select1cell("tbl_dept","ID",array("Acronym"=>"Administration"),true),$db->select1cell("tbl_dept","ID",array("Acronym"=>"GNC"),true))),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='!=',true);
													?>
													<select name=dept class = 'typeproforms'>
														<?php
														//for($i=0;$i<count($dept);$i++) echo "<option value='".$dept[$i]['ID']."' ".($dept[$i]['ID'] == $dpt?"selected":"").">".$dept[$i]['DepartmentName'].($dept[$i]['Acronym'] != 'Administration'?"(".$dept[$i]['Acronym'].")":"");
														?>
													</select>
												</td>
											</tr><!--End of department code row- ->
											<tr>
												<td width = '120' height = '30'>
													<strong>Family Name  </strong>
													<span class = 'mandatory'>*</span>
												</td>
												<td height = '30'>
													<span id="sprytextfield1">
														<input type = 'text' name = 'name' id = 'name'
														class = 'typeproforms'  value = "<?php //if(isset($hodName))echo $hodName;?>"/>
													</span>
												</td>
											</tr>
											<tr>
												<td width = '120' height = '30'>
													<strong>Other Name  </strong>
													<span class = 'mandatory'>*</span>
												</td>
												<td height = '30'>
													<span id="sprytextfield2">
														<input type = 'text' name = 'name' id = 'name'
														class = 'typeproforms'  value = "<?php //if(isset($hodName))echo $hodName;?>"/>
													</span>
												</td>
											</tr>
											<tr>
												<td width = '120' height = '30'>
													<strong>Class</strong>
													<span class = 'mandatory'>*</span>
												</td>
												<td height = '30'>
													<select name=class class = 'typeproforms'>
														<?php
														//if(@$_GET['sql'] == 'update.sql'){
														//	echo "<option>".$class[3];
														//} else{
															?>
															<option <?php// if(@$class[3] == 4) echo "selected" ?> >4<option <?php// if(@$class[3] == 5) echo "selected" ?> >5<option <?php// if(@$class[3] == 6) echo "selected" ?> >6
															<?php
													//	}
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
											</tr><!--End of department name row- ->
											<tr>
												<td>&nbsp;</td>
												<td height = '30'>
													<input type = 'submit' name = '<?php// echo $update['btn']['name'] ?>' class = 'button' value = '<?php echo $update['btn']['value'] ?>' />
												</td>
											</tr>
											</table>
										</form><!-- End of form- ->
									</td>
									<td><!--
										<fieldset class=upload>
											<legend style='text-align:center'>Upload EXCEL Format Only</legend>
											<form method=post enctype='multipart/form-data'>
												<input class=button type=file name=exceldoc />
												<input type=submit name=upload value='import' class=button />
											</form>-->
									</td><?php */ ?>
					</td>
		<form name=slist><input type=hidden name=error>
			<!--<table><tr>-->
					<td>
						Department
					</td>
					<td>
						<?php
						$dpt = $dept="";
						$dept = $db->selectFields($tbl='tbl_dept',$field=array("ID","DepartmentName","Acronym"),$condition=array("ID"=>array($db->select1cell("tbl_dept","ID",array("Acronym"=>"Administration"),true),$db->select1cell("tbl_dept","ID",array("Acronym"=>"GNC"),true))),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='!=',true);
						?>
						<select name=dept class = 'typeproform'>
							<?php
							for($i=0;$i<count($dept);$i++) echo "<option onclick='studentReport({$year},document.slist.dept.value,document.slist.classid.value,start=0,end=12,page=\"studentreport.php\",document.slist.termid.value,0);reportStudentList({$year},document.slist.dept.value,document.slist.classid.value,start=0,end=12,page=\"reportstudentlist.php\");' value='".$dept[$i]['ID']."' ".($dept[$i]['ID'] == $dpt?"selected":"").">".($dept[$i]['Acronym'] != 'Administration'?$dept[$i]['Acronym']:"");
							?>
						</select>
					</td>
					<td>
						Class
					</td>
					<td>
						<select name=classid class=typeproform>
							<?php for($i=4;$i<=6;$i++) echo "<option onclick='studentReport({$year},document.slist.dept.value,document.slist.classid.value,start=0,end=12,page=\"studentreport.php\",document.slist.termid.value,0); reportStudentList({$year},document.slist.dept.value,document.slist.classid.value,start=0,end=12,page=\"reportstudentlist.php\");'>".$i; ?>
						</select>
					</td>
					<td>
						Term
					</td>
					<td>
						<select name=termid class=typeproform>
							<?php for($i=1;$i<=3;$i++) echo "<option onclick='studentReport({$year},document.slist.dept.value,document.slist.classid.value,start=0,end=12,page=\"studentreport.php\",document.slist.termid.value,0);'>".$i; ?>
						</select>
					</td>
				</tr>
			</table>
		</form><hr style='border-top:1px solid #eee;' />
	<?php /* </fieldset><?php */ ?>
			<link rel=stylesheet type='text/css' href="../../admin/css/tb.css" />
			<div id='studentlist'><script>studentReport(<?php echo $year ?>,deptid=3,classid=4,start=0,end=12,page='studentreport.php',document.slist.termid.value,0)</script></div>
			<!--
			<fieldset class=upload>
				<legend style='text-align:center'>Courses</legend>
				<div id='classcourse'><script>classCourse(deptid=3,classid=4,<?php// echo $year ?>)</script></div>
			</fieldset>-->
		</div><!-- End of main_body div(main white div)-->
		<div id='reportstudentlist' class="logo shadowEffect" style='width:200px;'><script>reportStudentList(<?php echo $year ?>,deptid=3,classid=4,start=0,end=12,page='reportstudentlist.php')</script></div>
		<?php
			/*This function will return the logo div string to the sidebody*/
			//echo plotLogoDiv("../../admin/images/lgo.jpg");
			#echo plotSearchDiv('department_search.php');
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
		function Export() {
				html2canvas(document.getElementById('tblReport'), {
						onrendered: function (canvas) {
								var data = canvas.toDataURL();
								var docDefinition = {
										content: [{
												image: data,
												width: 500
										}]
								};
								pdfMake.createPdf(docDefinition).download("Student-report.pdf");
						}
				});
		}
</script>

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
