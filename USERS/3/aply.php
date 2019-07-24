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
$inleave = false;
$check = mysql_query("SELECT * FROM `tbl_leave` WHERE `UserID`='".$_SESSION['u_id']."' && `LeaveTo`>='".date('Y-m-d',time())."'");
if($check && mysql_num_rows($check)>=1) $inleave = true;
if(isset($_GET['msg']) && ($_GET['msg'] == 'success'))
{
	$successMsg		=	"Data Saved!";	
}
if(isset($_POST['submit']))
{	
	#echo '<pre>';print_r($_POST);die;
	$dept = mysql_real_escape_string(trim($_POST['dept']));
	$name = mysql_real_escape_string(trim($_POST['name']));
	$replacement = mysql_real_escape_string(trim($_POST['replacement']));
	$leavetype = mysql_real_escape_string(trim($_POST['leavetype']));
	$address = mysql_real_escape_string(trim($_POST['address']));
	$data = array("UserID"=>$_SESSION['u_id'],"LeaveFrom"=>"NOW()","LeaveTo"=>"NOW()","HOD"=>1,"HR"=>0,"Finance"=>0);
	if($db->InsertIfNotExist("tbl_leave",$data,array("UserID"=>$_SESSION['u_id'],"LeaveFrom"=>date("Y-m-d",time())),true)) {
		$data1 = array("LeaveID"=>mysql_insert_id(),"Details"=>$leavetype,"Replacement"=>$replacement,"Address"=>$address);
		if($db->InsertIfNotExist("leave_details",$data1,array("LeaveID"=>mysql_insert_id()),false)){
			/* send the email to hr */
			$successMsg = "Data Saved!";
		} else {	
			$errorMsg = "Error!Details Not Saved";
		}
	} else {	
		$errorMsg = "Error!Possibly you Submitted request to day";
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
			<h2>Leave Application Form</h2>
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
				echo checkLeaveAvailability($_SESSION['u_id']);
				if($inleave){
					$requestString = array("tbl"=>array("tbl_leave","leave_details","tbl_users"),"fld"=>array("tbl_users"=>array("Name"),"tbl_leave"=>array("ID","LeaveFrom","LeaveTo","HOD","HR","Finance"),"leave_details"=>array("Replacement","Details","Address")),"condition"=>array("tbl_leave`.`ID"=>"leave_details`.`LeaveID","tbl_leave`.`UserID"=>"tbl_users`.`ID","tbl_users`.`ID"=>$_SESSION['u_id']));
					#echo "<pre>";var_dump($requestString );return;
					$data = $db->selectInMoreTable($requestString,true,true, "ORDER BY `ID` DESC");
					//$data = $db->selectFields("tbl_users",array("ID","Name","Dept","UserType"),array("UserType"=>2),null,"",true,'=');
					$out = "";# echo "<pre>"; var_dump($data);
					if(count($data) == 0) echo "<font color=red>No Request!</font>";
					else{
						for($i=0;$i<count($data);$i++){
							/*$out .= "<tr>";
							$out .= "<td>";
							$out .= $i + 1;
							$out .= "</td> <td>".$data[$i]['Name']."</td> <td>".$data[$i]['Details']."</td> <td align=center>".$data[$i]['LeaveFrom']."</td><td align=center>".$data[$i]['LeaveTo']."</td><td align=center width='50'>";
							$out .= $data[$i]['HOD']==1?"<abbr title='HOD' style='cursor:pointer;'><img src='../../admin/images/s_okay.png'></abbr>":"<abbr title='HOD'><img border=0 src='../../admin/images/s_really2.png'></abbr>";
							$out .= $data[$i]['HR']==1?"<abbr title='HR' style='cursor:pointer;'><img src='../../admin/images/s_okay.png'></abbr>":"<abbr title='HR'><img border=0 src='../../admin/images/s_really2.png'></abbr>";
							$out .= $data[$i]['Finance']==1?"<abbr title='Finance' style='cursor:pointer;'><img src='../../admin/images/s_okay.png'></abbr>":"<abbr title='Finance'><img border=0 src='../../admin/images/s_really2.png'></abbr>";
							*/$condition = array("LeaveTo"=>array('sign'=>"<","value"=>date("Y-m-d",time())),"ID"=>$data[$i]['ID']);
							$approved = $data[$i]['HR']==1?$data[$i]['Finance']==1? $db->select1cell("tbl_leave","LeaveTo",$condition,true)?"<center><img src='../../admin/images/b_events2.png'></center>":"<font color=green>".DetectDays($db->select1cell("tbl_leave","LeaveTo",array("ID"=>$data[$i]['ID']),true))."</font>":"<font color=red>No</font>":"<font color=red>No</font>";
							$out .= "<br /><font color=red>Remaining days for the current leave:<b>".$approved."</font><br />";break;
						}
					}
					echo $out;
				}
			?>
			<br/>	
			<form method = 'POST' action = "<?php echo $_SERVER['PHP_SELF'];?>">
			<table border = '0' cellspacing = '0' cellpadding = '0'>
			<tr>
				<td width = '120' height = '30'>
					<strong>Name</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<input type=text class = 'typeproforms' name=name readonly value='<?php echo $db->select1cell("tbl_users","Name",array("ID"=>$_SESSION['u_id']),true) ?>'>
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Department</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<input type=text class = 'typeproforms' name=dept readonly value='<?php echo $db->select1cell("tbl_users","Dept",array("ID"=>$_SESSION['u_id']),true) ?>'>
				</td>
			</tr><!--End of department code row-->
			<tr>
				<td width = '120' height = '30'>
					<strong>Replacement Staff  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<?php
					$data = $db->selectFields("tbl_users",array("Name","ID","Dept"),null,null,"Dept",true,'=');
					#var_dump($data);
					?>
					<select name=replacement class = 'typeproforms'>
					<?php
					for($i=0;$i<count($data);$i++){
						echo "<option value='".$data[$i]['ID']."'";
						echo $data[$i]['Dept']== $db->select1cell("tbl_users","Dept",array("ID"=>$_SESSION['u_id']),true)?" selected ":" "; 
						echo ">".$data[$i]['Name'];
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Type of leave</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<select name=leavetype class='typeproforms'>
						<option value="Annual" selected> Annual
						<option value="Circumstance"> Circumstance
						<option value="Materinity / Paternity" > Materinity / Paternity
						<option value="Sick"> Sick
						<option value="Authorised"> Authorised
					</select>
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Address On Leave</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield3">
						<input type = 'text' name = 'address' id = 'uname' 
						class = 'typeproforms'  value = "<?php if(isset($deptName))echo $deptName;?>"/>
					</span>	
				</td>
			</tr><!--End of department name row-->
			<tr>
				<td colspan=2 align=center height = '30'>
					<input type = <?php echo $inleave?"'button' onclick='alert(\"You Are Still In Leave\")'":"'submit'" ?> <?php echo $db->select1cell("tbl_leave_counter","RemainingDays",array("UserId"=>$_SESSION['u_id']),true)?"onclick='return true'":"onclick='return false'" ?> name = 'submit' class = 'button' value = 'Submit' />
				</td>
			</tr>
		  	</table>
			</form><!-- End of form-->
			<br/>
			<table width=100%>
				<tr>
					<th colspan=7>
						All Requested Leave
					</th>
				</tr>
				<tr id=tableheader>
					<th>
						#
					</th>
					<th>
						Leave Type
					</th>
					<th>
						Start Date
					</th>
					<th>
						End Date
					</th>
					<th>
						HOD
					</th>
					<th>
						HR
					</th>
					<th>
						Finance
					</th>
				</tr>
				<?php
				$requestString = array("tbl"=>array("tbl_leave","leave_details"),"fld"=>array("tbl_leave"=>array("ID","LeaveFrom","LeaveTo","HOD","HR","Finance"),"leave_details"=>array("Replacement","Details","Address")),"condition"=>array("tbl_leave`.`ID"=>"leave_details`.`LeaveID","tbl_leave`.`UserID"=>$_SESSION['u_id']));
				$data = $db->selectInMoreTable($requestString,true,true, "ORDER BY ID DESC");
				//$data = $db->selectFields("tbl_users",array("ID","Name","Dept","UserType"),array("UserType"=>2),null,"",true,'=');
				$out = "";# echo "<pre>"; var_dump($data);
				for($i=0;$i<count($data);$i++){
					$out .= "<tr>";
					$out .= "<td>";
					$out .= $i + 1;
					$out .= "</td>";
					$out .= "<td>";
					$out .= $data[$i]['Details'];
					$out .= "</td>";
					$out .= "<td align=center>";
					$out .= $data[$i]['LeaveFrom'];
					$out .= "</td>";
					$out .= "<td align=center>";
					$out .= $data[$i]['LeaveTo'];
					$out .= "</td>";
					$out .= "<td align=center>";
					$out .= $data[$i]['HOD']==1?"Approved":"Not Approved";
					$out .= "</td>";
					$out .= "<td align=center>";
					$out .= $data[$i]['HR']==1?"Approved":"Not Approved";
					$out .= "</td>";
					$out .= "<td align=center>";
					$out .= $data[$i]['Finance']==1?"Approved":"Not Approved";
					$out .= "</td>";
					$out .= "</tr>";
				}
				echo $out;
				?>
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
	var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9 ]/,
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