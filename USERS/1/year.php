<?php
ob_start();
session_start();
/*Include the database configuration file*/
require_once("../../admin/includes/config.php");
/*Include the default function file*/
require_once("../../admin/includes/functions.php");
require_once"../../LIB/config.php";
$db = new DBConnector("school_report_db");
//$db2 = new DBConnector("school_report_marks_db");

/*This function will check the session*/
checkSession();
if(isset($_GET['msg']) && ($_GET['msg'] == 'success'))
{
	$successMsg		=	"Course Successfully Saved!";	
}

$update = array("msg"=>"Register","btn"=>array("name"=>"submit","value"=>"Save Data")); $dept="";$t="";
if(@$_GET['sql'] == 'update.sql' && !empty($_GET['id'])){
	$update['msg'] = "Update"; $update['btn'] = array("name"=>'update',"value"=>"Save Updates");
	$data = $db->selectOneRowFromTable("tbl_course",array("ID"=>mysql_real_escape_string(trim($_GET['id']))),true);
	#var_dump($data);
	$dept = $data['DeptID'];
	$courseName = $data['CourseName'];
	$t = $data['TeacherID'];
	$max = $data['Maximum'];
	$class = $data['Acronym'];
	//$upass = "********";
}
if(@$_GET['sql'] == "delete.sql" && !empty($_GET['id'])){
	$f = array();
	$fl = $db->select1cell("tbl_course","Acronym",array("ID"=>$_GET['id']),true);
	for($i=1;$i<=3;$i++) for($t=1;$t<=2;$t++) $f[] = $fl.$i.$t;
	$db->DropColomn("tbl_marks",$f);
	$db->delete1row("tbl_course",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$successMsg		=	"Course Successfully Deleted!";
}
if(@$_GET['sql'] == "reset.sql" && !empty($_GET['id'])){
	$db->updatecells(array("Password"=>sha1("123")),"tbl_users",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$successMsg		=	"Password Successfully Reseted to <b>123</b>!";
}
if(@$_POST['update']){
	if($db->updatecells(array("CourseName"=>mysql_real_escape_string(trim($_POST['name'])),"Maximum"=>$_POST['max'],"TeacherID"=>$_POST['teacher']),"tbl_course",array("ID"=>mysql_real_escape_string(trim($_POST['id']))))){
		$successMsg		=	"Course Successfully Updated!";
		$dept = "";
		$courseName = "";
		$t = "";
		$max = "";
		$class = "";
	
	} else{
		$errorMsg = "Error! Fail To Update!";
	}
}
if(isset($_POST['submit']))
{	
	#echo '<pre>';print_r($_POST);#die;
	$dept = mysql_real_escape_string(trim($_POST['dept']));
	$courseName = mysql_real_escape_string(trim($_POST['name']));
	$class = mysql_real_escape_string(trim($_POST['class']));
	$max = mysql_real_escape_string(trim($_POST['max']));
	$teacher = mysql_real_escape_string(trim($_POST['teacher'])); #die; 
	#var_dump($dept);
	if($_POST['coursetype'])$cndtn = array("Acronym"=>"LIKE('".$db->select1cell("tbl_dept","Acronym",array("ID"=>$dept),true).$class.($_POST['coursetype']?"9":"")."%')");
	else $cndtn = array("Acronym"=>array("LIKE('".$db->select1cell("tbl_dept","Acronym",array("ID"=>$dept),true).$class."%')","Acronym"=>"NOT LIKE('".$db->select1cell("tbl_dept","Acronym",array("ID"=>$dept),true).$class."9%')"));
	#$acronym = $db->select1cell("tbl_dept","Acronym",array("ID"=>$dept),true).$class.($db->selectMax($tbl="tbl_course",$fld="Acronym",$rtn=false,$condition=$cndtn));
	$acronym = $db->select1cell("tbl_dept","Acronym",array("ID"=>$dept),true).$class.($db->selectMax($tbl='tbl_course',$fld='Acronym',$rtn=false, $condition=$cndtn,!$_POST['coursetype']?true:false) != NULL ? suffix($db->selectMax($tbl='tbl_course',$fld='Acronym',$rtn=false, $condition=$cndtn,!$_POST['coursetype']?true:false),$_POST['coursetype']) : ($_POST['coursetype']?"9":"0")."1");//mysql_real_escape_string(trim($_POST['acronym']));
	#die;//$upass = mysql_real_escape_string(trim($_POST['pass']));
	#echo $acronym; die;
	if($courseName ==	'' || $acronym == '' ) {
		$errorMsg	=	'Error! Required Fields Cannot Be Left Blank!';	
	} else {	
		$selectQuery			=	"SELECT * FROM `tbl_course` WHERE `CourseName`='".$courseName."' && Acronym LIKE('".$db->select1cell("tbl_dept","Acronym",array("ID"=>$dept),true).$class."%')";
		#echo $selectQuery;
		$existFlag				=	recordAlreadyExist($selectQuery);
		if($existFlag)
		{
			$errorMsg			=	"Error! course Already Exists !";
		}	
		else
		{	
			$deptInsertQuery	= 	"INSERT INTO `tbl_course` (`ID`, `CourseName`,`Acronym`,`Maximum`,`TeacherID`,`DeptID`) VALUES (NULL, '{$courseName}','{$acronym}','{$max}','{$teacher}','{$dept}')";
			/*Call General Function to Insert the record*/
			$field = array();
			for($i=1;$i<=3;$i++) for($t=1;$t<=2;$t++)$field[] = array("NAME"=>$acronym.$i.$t,"TYPE"=>"FLOAT","LENGTH"=>null,"NOT_NULL"=>true,"DEFAULT"=>0);
			$insertFlag			= 	insertOrUpdateRecord($deptInsertQuery , $_SERVER['PHP_SELF'],array("con"=>$db,"tbl"=>"tbl_marks","data"=>$field));
			if(!$insertFlag)
			{
				$errorMsg		=	"Error!Unable to save course information !";
			}
		}	
	}	
}
if(@$_GET['activate'] == 1){
	#var_dump($db);die;
	$db->updateCells($data=array("Status"=>0),$tbl="tbl_years",$condition=null);
	$db->updateCells($data=array("DeactivationDate"=>"NOW()"),$tbl="tbl_years",$condition=array("ID"=>$db->select1cell("tbl_years","ID",array("Status"=>1),true)));
	if($db->InsertOrUpdate($tbl="tbl_years",$data=array("Year"=>mysql_real_escape_string(trim($_GET['year'])),"Status"=>1,"ActivationDate"=>"NOW()","DeactivationDate"=>"NOW()"),$id_increment=true,$condition=array("Year"=>$_GET['year']),$referencefield="Status",$replace=true)){
		mysql_query("CREATE DATABASE `school_report_db".$_GET['year']."`");
		$_SESSION['year'] = $_GET['year'];
		$newdb = "school_report_db".$_GET['year'];
		$olddb = "school_report_db".$_GET['current'];
		//start moving tables
		$tables = array("tbl_dept","tbl_student","tbl_course","tbl_marks");
		foreach($tables as $table){
			mysql_query("CREATE TABLE IF NOT EXISTS `".$newdb."`.`".$table."` LIKE `".$olddb."`.`".$table."`");
			if($table != 'tbl_student' && $table != 'tbl_marks') mysql_query("INSERT INTO `".$newdb."`.`".$table."`(SELECT * FROM `".$olddb."`.`".$table."`);");
		}
		//end moving tables
		//deleting all teachers files
		$dir = "../3/dwnlds/";
		foreach(glob($dir."*.xlsx") as $file){
			unlink($file);
		}
		#unlink("../3/dwnlds/*.xlsx");
		$successMsg = "Year Successfully Changed !!!";
	} else $errorMsg = "Undefined Error !!!";
}
#$dept="";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Academic Year</title>
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
	<script>
		function refleshPage(year,current){
			if(confirmFunction(msg="This Will Affect the Entier System"))window.location='<?php echo $_SERVER['PHP_SELF']."?activate=1&year=" ?>' + year + "&current="+ current;
		}
	</script>
	<div class="body">
		<div class="main_body">
			<h2 style="font-size:23px;">Academic Year <?php echo $_SESSION['year']."(".date('Y-M-d').")" ?><!-- | <label><input type=checkbox name=newyear value='<?php// echo date('Y') ?>' onclick='newYear()' />Active Year<b> <?php// echo date('Y') ?></b></label>--></h2>
			
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
			<form name=fyear>
			Change Active Year
			<select name=year id=year class=typeproform>
				<?php for($i=date('Y')+1;$i>=2011;$i--) echo "<option onclick='refleshPage(document.fyear.year.value,{$_SESSION['year']});' ".($i == $_SESSION['year']?"selected":"").">".$i; ?>
			</select></form>
			<?php /*
			<br/>	
			<form method = 'POST' name=frm action = "<?php echo $_SERVER['PHP_SELF'];?>">
			<table width ="550" border = '0' cellspacing = '0' cellpadding = '0'>
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
			<tr>
				<td width = '120' height = '30'>
					<strong>Course Type</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<select name=coursetype  class = 'typeproforms'>
						<?php if(@$_GET['sql'] != 'update.sql') { ?>
						<option value=0>Technical Course</option>
						<option value=1>General Course</option>
						<?php } else { echo "<option value=".($class[4] == 9?"1":"0").">".($class[4] == 9?"General Course":"Technical Course");} ?>
					</select>
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Department</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<?php
					$dpt = $dept;
					$dept = $db->selectFields($tbl='tbl_dept',$field=array("ID","DepartmentName","Acronym"),$condition=array("ID"=>array($db->select1cell("tbl_dept","ID",array("Acronym"=>"Administration"),true),$db->select1cell("tbl_dept","ID",array("Acronym"=>"GNC"),true))),$limit=null,$order="",$indexed=true,$sign='!=',true);
					?>
					<select name=dept class = 'typeproforms'>
						<?php
						for($i=0;$i<count($dept);$i++) echo "<option value='".$dept[$i]['ID']."' ".($dept[$i]['ID'] == $dpt?"selected":"").">".$dept[$i]['DepartmentName'].($dept[$i]['Acronym'] != 'Administration'?"(".$dept[$i]['Acronym'].")":"");
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Course Name</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield1">
						<input type = 'text' name = 'name' id = 'name' 
						class = 'typeproforms'  value = "<?php if(isset($courseName))echo $courseName;?>"/>
					</span>	
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Maximum</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield10">
						<input type = 'text' name = 'max' id = 'max' onkeyup='if(document.frm.max.value >= 120){ document.frm.max.value=120;return false;}'
						class = 'typeproforms'  value = "<?php if(isset($max))echo $max; else echo ""?>"/>
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
						if(@$_GET['sql'] == 'update.sql'){
							echo "<option>".$class[3];
						} else{
							?>
							<option <?php if(@$class[3] == 4) echo "selected" ?> >4<option <?php if(@$class[3] == 5) echo "selected" ?> >5<option <?php if(@$class[3] == 6) echo "selected" ?> >6
							<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Teacher</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<?php
					$teacher = $db->selectFields($tbl='tbl_users',$field=array("ID","Name","Dept"),$condition=array("UserType"=>3),$limit=null,$order="ORDER BY Dept ASC, Name ASC",$indexed=true,$sign='=',false);
					?>
					<select name=teacher class = 'typeproforms'>
						<?php
						for($i=0;$i<count($teacher);$i++) echo "<option value='".$teacher[$i]['ID']."' ".($teacher[$i]['ID'] == $t?"selected":"").">".$teacher[$i]['Name']."(".$db->select1cell("tbl_dept","Acronym",array("ID"=>$teacher[$i]['Dept']),true).")";
						?>
					</select>
				</td>
			</tr><?php /*
			<tr>
				<td width = '120' height = '30'>
					<strong>User Name  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield2">
						<input type = 'text' name = 'uname' id = 'uname' 
						class = 'typeproforms' <?php if(@$_GET['sql'] == 'update.sql') echo "readonly"; ?> value = "<?php if(isset($uName))echo $uName;?>"/>
					</span>	
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Password  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield3">
						<input type = 'password' maxlength='8' <?php if(@$_GET['sql'] == 'update.sql') echo "readonly"; ?> name = 'password' id = 'uname' 
						class = 'typeproforms'  value = "<?php if(isset($upass)) echo $upass ?>"/>
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
						<input type = 'password' <?php if(@$_GET['sql'] == 'update.sql'){ echo "readonly"; }?> maxlength='8' name = 'pass' id = 'uname' 
						class = 'typeproforms'  value = "<?php if(isset($upass)) echo $upass ?>"/>
					</span>	
				</td>
			</tr> * / ?><!--End of department name row-->
			<tr>
				<td>&nbsp;</td>
				<td height = '30'>
					<input type = 'submit' name = '<?php echo $update['btn']['name'] ?>' class = 'button' value = '<?php echo $update['btn']['value'] ?>' />
				</td>
			</tr>
		  	</table>
			</form><!-- End of form--> <?php */ ?>
			<br/>
			<link rel='stylesheet' type='text/css' href='../../admin/css/tb.css'>
			<table width=100% border=1>
				<tr id=tableheader>
					<th colspan=2>#</th><th>Year</th><th>Status</th><th>Activation Day</th><th>Deactivation Day</th>
				</tr>
				<?php
				$data = $db->selectFields($tbl='tbl_years',$field=array("ID","Year","Status","ActivationDate","DeactivationDate"),$condition=null,$limit=null,$order="ORDER BY Year ASC",$indexed=true,$sign='=',$multiplereference=false);
				$out = "";
				for($i=0;$i<count($data);$i++){
					$out .= "<label><tr id=n".($i%2)."><td width=2%><input type=checkbox ".($data[$i]['Status']?"checked":"").(!$data[$i]['Status']?" onclick='refleshPage(".$data[$i]['Year'].",{$_SESSION['year']}); return false;'":" onclick='return false'")." name=activate /></td><td>";
					$out .= $i + 1;
					$out .= "</td><td>".$data[$i]['Year'];
					$out .= "</td><td>".($data[$i]['Status']?"Active":"Not Active")."</td>";
					//$out .= "</td><td align=center>".users($data[$i]['UserType']);
					//$out .= "</td><td><!--<a href='./hr.php?sql=reset.sql&id=".$data[$i]['ID']."' onclick='return confirmFunction(\"Reset Password for ".$data[$i]['Name']."?\")'>Reset</a>";
					$out .= "<td>".$data[$i]['ActivationDate']."</td>";
					$out .= "<td>".$data[$i]['DeactivationDate']."</td>";/*
					$out .= "<td>";
					$out .= ($data[$i]['Acronym']!='Administration'?"<a href='./course.php?sql=update.sql&id=".$data[$i]['ID']."'>Update</a>":"");
					$out .= "</td>";
					$out .= "<td>";
					$out .= ($data[$i]['Acronym']!='Administration'?"<a href='./course.php?sql=delete.sql&id=".$data[$i]['ID']."' onclick='return deleteFunction(\" ".$data[$i]['CourseName']."\")'>Delete</a>":"&nbsp;");
					$out .= "</td>";*/
					$out .= "</tr></label>";
				}
				echo $out;
				?>
			</table>
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
<!--
	/*var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "custom",{isRequired:true,characterMasking:/[a-zA-Z ]/,
						useCharacterMasking:true, validateOn:["change"]}); */
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "custom",{isRequired:true,characterMasking:/[a-zA-Z+ ]/,
						useCharacterMasking:true, validateOn:["change"]});	
	var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9 ]/,
						useCharacterMasking:true, validateOn:["change"]});	
	var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9!@#$%^&*_?>< ]/,
						useCharacterMasking:true, validateOn:["change"]});	
	var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9!@#$%^&*_?>< ]/,
						useCharacterMasking:true, validateOn:["change"]});
	var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "custom",{isRequired:true,characterMasking:/[0-9]/,
						useCharacterMasking:true, validateOn:["change"]});
-->	
</script>
</body>
</html>
<?php
	ob_end_flush();
?>
<!-- welcome to our application -->