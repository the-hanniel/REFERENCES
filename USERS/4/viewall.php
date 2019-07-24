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
$db = new DBConnector("tct_leave");
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
	<title>Head of departments</title>
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
			<h2>View All Approved Leave </h2>
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
			
			<table width=100% border=1 cellspacing=0>
				<tr>
					<th colspan=7>
						All Approved Leave
					</th>
				</tr>
				<tr id=tableheader>
					<th> # </th> <th> Name </th> <th> Leave Type </th> <th> Start Date </th> <th> End Date </th> <th> Approve </th> <th> Status </th>
				</tr>
				<?php
				$requestString = array("tbl"=>array("tbl_leave","leave_details","tbl_users"),"fld"=>array("tbl_users"=>array("Name"),"tbl_leave"=>array("ID","LeaveFrom","LeaveTo","HOD","HR","Finance"),"leave_details"=>array("Replacement","Details","Address")),"condition"=>array("tbl_leave`.`ID"=>"leave_details`.`LeaveID","tbl_leave`.`UserID"=>"tbl_users`.`ID"));
				#echo "<pre>";var_dump($requestString );return;
				$data = $db->selectInMoreTable($requestString,true,true, "ORDER BY ID DESC");
				//$data = $db->selectFields("tbl_users",array("ID","Name","Dept","UserType"),array("UserType"=>2),null,"",true,'=');
				$out = "";# echo "<pre>"; var_dump($data);
				if(count($data) == 0) echo "<font color=red>Non Approved Leave!</font>";
				else{
					for($i=0;$i<count($data);$i++){
						$out .= "<tr>";
						$out .= "<td>";
						$out .= $i + 1;
						$out .= "</td> <td>".$data[$i]['Name']."</td> <td>".$data[$i]['Details']."</td> <td align=center>".$data[$i]['LeaveFrom']."</td><td align=center>".$data[$i]['LeaveTo']."</td><td align=center width='50'>";
						$out .= $data[$i]['HOD']==1?"<abbr title='HOD'><img src='../../admin/images/s_okay.png'></abbr>":"<abbr title='HOD'><img border=0 src='../../admin/images/s_really2.png'></abbr>";
						$out .= $data[$i]['HR']==1?"<abbr title='HR'><img src='../../admin/images/s_okay.png'></abbr>":"<abbr title='HR'><img border=0 src='../../admin/images/s_really2.png'></abbr>";
						$out .= $data[$i]['Finance']==1?"<abbr title='Finance'><img src='../../admin/images/s_okay.png'></abbr>":"<abbr title='Finance'><img border=0 src='../../admin/images/s_really2.png'></abbr>";
						//$approved = $data[$i]['HR']==1?$data[$i]['Finance']==1?"<font color=green>OK</font>":"<font color=red>No</font>":"<font color=red>No</font>";
						$condition = array("LeaveTo"=>array('sign'=>"<","value"=>date("Y-m-d",time())),"ID"=>$data[$i]['ID']);
						$approved = $data[$i]['HR']==1?$data[$i]['Finance']==1? $db->select1cell("tbl_leave","LeaveTo",$condition,true)?"<center><img src='../../admin/images/b_events2.png'></center>":"<font color=green>OK</font>":"<font color=red>No</font>":"<font color=red>No</font>";
						$out .= "</td><td align=center>".$approved."</td></tr>";
					}
				}
				echo $out;
				
				?>
			</table><br />
		</div><!-- End of main_body div(main white div)-->
		<?php
			/*This function will return the logo div string to the sidebody*/
			echo plotLogoDiv("../../admin/images/logo.png");
	//		echo plotSearchDiv('department_search.php');
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