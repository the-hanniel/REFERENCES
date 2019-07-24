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
$db = new DBConnector($dbname = "school_report_db".$_SESSION['year']);
if(isset($_GET['msg']) && ($_GET['msg'] == 'success'))
{
	$successMsg		=	"HOD Successfully Saved!";	
}
if(@$_GET['sql'] == 'allow.sql' && !empty($_GET['id'])){
	$db->updatecells(array("HOD"=>1),"tbl_leave",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>School Manager</title>
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
		echo plotHeaderMenuInfo(basename(__FILE__),3,$db);
	?>
	<div class="body">
		<div class="main_body">
			<h2>Here Fill The List Of Student With Money Problem</h2>
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
			
			<table width=100% border=0>
				<tr valign=top>
					<td width=50>
						<?php
						$class = $db->selectFields($tbl="tbl_student",$field=array("Class","DeptID"),$condition=null,$limit=null,$order="ORDER BY DeptID ASC, Class ASC",$indexed=true,$sign='=',$multiplereference=false,$distinct=true);
						#var_dump($class);
						$dept;$classid;
						for($i=0;$i<count($class);$i++){
							if($i==0){
								$dept = $class[$i]['DeptID'];
								$classid = $class[$i]['Class'];
							}
							echo "<a href='./?dept={$class[$i]['DeptID']}&class={$class[$i]['Class']}&active={$i}' ".($i==0 && !@$_GET['active']?"style='color:#ff0000;'":($i==@$_GET['active']?"style='color:#ff0000;'":"")).">S".$class[$i]['Class']." ".$db->select1cell("tbl_dept","Acronym",array("ID"=>$class[$i]['DeptID']),true)."</a><br />";
						}
						if(@$_GET['dept'] && $_GET['class']){
							$dept = $_GET['dept'];
							$classid = $_GET['class'];
						}
						?>
					</td>
					<td>
						<style>
							table{border-color:#eee;}
							th{ background-color:#555; border-color:#eee; color:#eee; }
							tr#n0{ background-color:#ddd; }
							tr#n0:hover{ background-color:#fef; cursor:pointer; }
							tr#n1{ background-color:#eee;}
							tr#n1:hover{ background-color:#fef; cursor:pointer; }
							td{ border-color:#eee; }
						</style>
						
        <style type="text/css">
            .ds_box {
                background-color: #FFF;
                border: 1px solid #000;
                position: absolute;
                z-index: 32767;
            }

            .ds_tbl {
                background-color: #FFF;
            }

            .ds_head {
                background-color: #006633;
                color: #FFF;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 13px;
                font-weight: bold;
                text-align: center;
                letter-spacing: 2px;
            }

            .ds_subhead {
                background-color: #CCC;
                color: #000;
                font-size: 12px;
                font-weight: bold;
                text-align: center;
                font-family: Arial, Helvetica, sans-serif;
                width: 32px;
            }

            .ds_cell {
                background-color: #EEE;
                color: #000;
                font-size: 13px;
                text-align: center;
                font-family: Arial, Helvetica, sans-serif;
                padding: 5px;
                cursor: pointer;
				border: 1px solid #fff;
            }
			
			
            .ds_celll {
                background-color: #0f0;
                color: #000;
                font-size: 13px;
                text-align: center;
                font-family: Arial, Helvetica, sans-serif;
                padding: 5px;
                cursor: pointer;
				border: 1px solid #fff;
            }
            .ds_cell:hover {
                background-color: #F3F3F3;
            } /* This hover code won't work for IE */
        </style>

    <table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
           style="display: none;">
        <tr>
            <td id="ds_calclass"></td>
        </tr>
    </table>
						<script type='text/javaScript' src='./script.js'></script>
						<table border=1 width=60%>
							<tr>
								<th colspan=3>Student In S<?php echo $classid." ".$db->select1cell("tbl_dept","Acronym",array("ID"=>$dept),true);  ?>
							</tr>
							<?php
							$student = $db->selectInMoreTable($lbl=array("tbl"=>array($dbname."`.`tbl_student","school_report_db`.`tbl_student"),"fld"=>array("school_report_db`.`tbl_student"=>array("ID","FirstName","LastName"),$dbname."`.`tbl_student"=>array("StudentPhoto")),"condition"=>array("school_report_db`.`tbl_student`.`ID"=>"tbl_student`.`StudentID",$dbname."`.`tbl_student`.`DeptID"=>$dept,$dbname."`.`tbl_student`.`Class"=>$classid)),$multirows=true,$indexed=true, $order="ORDER BY `school_report_db`.`tbl_student`.`FirstName` ASC, `school_report_db`.`tbl_student`.`LastName` ASC");
							#var_dump($student);
							if($student && count($student)>0){
								$str = "";
								for($i=0;$i<count($student);$i++){
									$str .= "<tr id='n".($i%2)."' onmouseover=\"DisplayPhoto(this,'{$student[$i]['StudentPhoto']}')\">";
									$str .= "<td><input type=checkbox name='{$student[$i]['ID']}' /></td>";
									$str .= "<td>{$student[$i]['FirstName']}</td>";
									$str .= "<td> {$student[$i]['LastName']}</td>";
									$str .= "</tr>";
								}
								echo $str;
							} else{
								echo "<tr><td><span class=error>No Student Found</span></td></tr>";
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		</div><!-- End of main_body div(main white div)-->
		<?php
			/*This function will return the logo div string to the sidebody*/
			echo plotLogoDiv("../../admin/images/logo.png");
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