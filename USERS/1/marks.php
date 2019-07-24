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
if(@$_GET['del'] == true && $_SESSION['utype'] === "1"){
	$db->DropTable("tbl_second_sitting");
	header("Location:./".$_SERVER['PHP_SELF']);
}
if(@$_GET['set'] == sha1(true)){
	$tblname = "tbl_second_sitting";
	#echo $tblname;
	//select all course
	$course = $db->selectFields($tbl="tbl_course",$field=array("Acronym","Maximum","DeptID"),$condition=null,$limit=null,$order="",$indexed=true,$sign='=',$distinct=false);
	#echo "<pre>"; var_dump($course); echo "</pre>"; die;
	/* SEARCH for all second sitting exam */
	$sitting=array();
	for($i=0;$i<count($course);$i++){
		#echo "<pre>";#var_dump($sitting);
		if(!in_array($course[$i]['Acronym'],$sitting)){
			$student = $db->selectFields($tbl="tbl_student",$field=array("ID","DeptID","Class"),$condition=array("DeptID"=>$course[$i]['DeptID'],"Class"=>$course[$i]['Acronym'][3]),$limit=null,$order="",$indexed=true,$sign='=',$distinct=false);
			#var_dump($student); die;
			for($st=0;$st<count($student);$st++){
				#var_dump($student[$st]["ID"]); echo "<br>";
				$fields = array();
				for($term=1;$term<=3;$term++){
					for($c=1;$c<=2;$c++){
						if(preg_match("/".$db->select1cell("tbl_dept","Acronym",array("ID"=>$student[$st]['DeptID']),true).$student[$st]['Class']."/",$course[$i]['Acronym'])) $fields[] = $course[$i]['Acronym'].$term.$c;
					}
				}
				#echo "<pre>";var_dump($fields); die;
				//select marks for 1 student in one course the whole year
				$marks = $db->selectFields($tbl="tbl_marks",$field=$fields,$condition=array("StudentID"=>$student[$st]["ID"]),$limit=null,$order="",$indexed=true,$sign='=',$distinct=false);
				#echo "<pre>";var_dump($marks); echo "<br>";#die;
				if(count($marks) == 1){
					#echo "Test Marks Start";
					if(!in_array($course[$i]['Acronym'],$sitting)){
						#echo "Test echecs and Null marks Starts here";
						if(array_sum($marks[0]) < $course[$i]['Maximum']*3 ) $sitting[] = $course[$i]['Acronym'];  /*|| !in_array(null,$marks[0]*/
					}
				}
			}# die;
			#echo "<pre>";var_dump($fields); die;
			//select all student in the student table
		}
		#if($course[$i]['Acronym'] == 'CEL501') break;
	} #die;
	#echo "<pre>";var_dump($sitting); die;
	//make the fields complete
	$fields = array(array("NAME"=>"ID","TYPE"=>"INT","LENGTH"=>NULL,"NOT_NULL"=>true,"AUTO_INCREMENT"=>true,"PRIMARY_KEY"=>true),
					array("NAME"=>"StudentID","TYPE"=>"INT","LENGTH"=>NULL,"NOT_NULL"=>true,"AUTO_INCREMENT"=>false,"PRIMARY_KEY"=>false)
					);
	for($i=0;$i<count($sitting);$i++){
		$fields[] = array("NAME"=>$sitting[$i],"TYPE"=>"FLOAT","LENGTH"=>NULL,"NOT_NULL"=>false,"AUTO_INCREMENT"=>false,"PRIMARY_KEY"=>false);
	}
	$fields['UNIQUE'] = array("StudentID");
	#echo "<pre>";var_dump($fields); die;
	$db->createTable($tblname,$fields);
	#die;
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
<?php
mysql_connect("localhost","root","") or die("Could not connect");
mysql_select_db("school_report_db") or die("could not find db!");
$output = '';
if(isset($_POST['search'])) {

	$searchq = $_POST['search'];
	$searchq = preg_replace("#[^0-9a-z]#i","",$searchq);

	$query = mysql_query("SELECT * FROM `tbl_student` WHERE `ID` LIKE '%$searchq%' OR `FirstName` LIKE '%$searchq%' OR `LastName` LIKE '%$searchq%'") or die("could not search! ".mysql_error());
	$count = mysql_num_rows($query);
	if($count == 0) {
		$output = 'No results found!';
	}else{
		while ($row = mysql_fetch_array($querry)) {
			$fname = $row['FirstName'];
			$lname = $row['LastName'];
			$id = $row['ID'];

			$output .= '<div>'.$fname.' '.$lname.'</div>';
		}
	}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Student Marks</title>
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

	<!-- Spry Stuff Ends Here-->
</head><!--<frameset cols="200,*" rows="*" id="mainFrameset">
				<frame frameborder="0" id="frame_navigation"
        src="marks.php"
        name="frame_navigation" />
				<noframes>

				</noframes>
			</frameset>-->
<body>
<div class="main">
	<?php
		/*This function will return the header string with menu information*/
		echo plotHeaderMenuInfo(basename(__FILE__),1,$db);

	?>
	<script>
		function refleshPage(year){
			window.location='<?php echo $_SERVER['PHP_SELF']."?year=" ?>' + year;
		}
	</script>
	<div class="body">
		<div class="main_body">
			<h2 style="font-size:23px;">Select the year (Academic year <?php echo $year; ?>)</h2><form name=fyear>
			<select name=year id=year class=typeproform>
				<?php for($i=date('Y')+1;$i>=2013;$i--) echo "<option onclick='refleshPage(document.fyear.year.value);' ".($i == $year?"selected":"").">".$i; ?>

			</select>&nbsp;&nbsp;&nbsp;</form><button onclick="Export()">Print</button> <span>


				<input type="text" id="myInput" placeholder="Serch here..." />
			<h2><?php echo $update['msg'] ?> Choose Class Identification</h2>
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
			<php print("$output"); ?>
			<table border=0 width=100%>
				<tr valign=top>
					<td width=60%>
							<form name=slist><input type=hidden name=error>
								<table><tr><td>Department</td><td>
								<?php
									$dpt = $dept="";
									$dept = $db->selectFields($tbl='tbl_dept',$field=array("ID","DepartmentName","Acronym"),$condition=array("ID"=>array($db->select1cell("tbl_dept","ID",array("Acronym"=>"Administration"),true),$db->select1cell("tbl_dept","ID",array("Acronym"=>"GNC"),true))),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='!=',true);
									?>
									<select name=dept class = 'typeproform'>
										<?php
										for($i=0;$i<count($dept);$i++) echo "<option onclick='studentMarks({$year},document.slist.dept.value,document.slist.classid.value,start=0,end=12,page=\"studentmarks.php\",document.slist.termid.value);classCourse(document.slist.dept.value,document.slist.classid.value);' value='".$dept[$i]['ID']."' ".($dept[$i]['ID'] == $dpt?"selected":"").">".($dept[$i]['Acronym'] != 'Administration'?$dept[$i]['Acronym']:"");
										?>
									</select></td><td>
								Class</td><td>
								<select name=classid class=typeproform>
									<?php for($i=4;$i<=6;$i++) echo "<option onclick='studentMarks({$year},document.slist.dept.value,document.slist.classid.value,start=0,end=12,page=\"studentmarks.php\",document.slist.termid.value); classCourse(document.slist.dept.value,document.slist.classid.value);'>".$i; ?>
								</select></td><td>
								Term</td><td>
								<select name=termid class=typeproform>
									<?php for($i=1;$i<=3;$i++) echo "<option onclick='studentMarks({$year},document.slist.dept.value,document.slist.classid.value,start=0,end=12,page=\"studentmarks.php\",document.slist.termid.value); classCourse(document.slist.dept.value,document.slist.classid.value);'>".$i; ?>
								</select>
								</td><td>
								<!-- the code for activating 2nd sitting for the active year -->
								<?php
									//check if the second sitting is active
									$active = $db->checkTable("tbl_second_sitting"); $datain = false;
									if($active){
										//check if there is any records
										$records = $db->selectAllInTable($tbl="tbl_second_sitting",$indexed=false,$condition=null ,$order="");
										if(count($records)>0) $datain = true;
									}
								?>
								<label <?php if($datain){ ?> onclick='alert("Some data prevent table to be delete");return false;' <?php } elseif($active && !$datain){ ?> onclick='if(confirmFunction("Delete Second Sitting Table")){ window.location="<?php echo $_SERVER['PHP_SELF'] ?>?del=true"}return false;' <?php } else{ ?> onclick='if(confirmFunction("Activate Second Sitting")){window.location="<?php echo $_SERVER['PHP_SELF']."?set=".sha1(true) ?>"} return false;'<?php } ?>><input type=checkbox <?php if($active) echo "checked"; ?> name=second>2<sup>nd</sup> Sitting</label>
								<!-- end of 2nd sitting activation -->
								</td></tr><tr><td align=center colspan=2><div name=paging id=paging></div></td></tr></table>
							</form>
						</fieldset>
					</td>
				</tr>
			</table>
			<br/>
			<link rel=stylesheet type='text/css' href="../../admin/css/tb.css" />
			<div id='studentlist'><script>studentMarks(<?php echo $year ?>,deptid=3,classid=4,start=0,end=12,page='studentmarks.php',document.slist.termid.value,year='<?php echo @$_GET['year'] ?>')</script></div>
			<fieldset class=upload>
				<legend style='text-align:center'>Courses</legend>
				<div id='classcourse'><script>classCourse(deptid=3,classid=4,<?php echo $year ?>)</script></div>
			</fieldset>
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
	echo plotFooterDiv();
?>
<script type="text/javascript">
		function Export() {
				html2canvas(document.getElementById('tblMarks'), {
						onrendered: function (canvas) {
								var data = canvas.toDataURL();
								var docDefinition = {
										content: [{
												image: data,
												width: 500
										}]
								};
								pdfMake.createPdf(docDefinition).download("Student-marks.pdf");
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

$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTableData tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

</script>
</body>
</html>
<?php
	ob_end_flush();
?>
