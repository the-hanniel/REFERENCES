<?php
ob_start();
session_start();
/*Include the database configuration file*/
require_once("../../admin/includes/config.php");
/*Include the default function file*/
require_once("../../admin/includes/functions.php");
require_once"../../LIB/config.php";
$db = new DBConnector("tct_leave");

/*This function will check the session*/
checkSession();
if(isset($_GET['msg']) && ($_GET['msg'] == 'success'))
{
	$successMsg		=	"User Successfully Saved!";	
}

$update = array("msg"=>"Register","btn"=>array("name"=>"submit","value"=>"Add User"));
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Other users</title>
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
		echo plotHeaderMenuInfo(basename(__FILE__),1,$db);
	?>
	<div class="body">
		<div class="main_body">
			<h2><?php echo $update['msg'] ?> other users like Teachers and others</h2>
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
			<form method = 'POST' action = "<?php echo $_SERVER['PHP_SELF'];?>">
			<table width ="550" border = '0' cellspacing = '0' cellpadding = '0'>
			<tr>
				<td width = '120' height = '30'>
					<strong>User Type</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<label><input type=radio name=userType value=1>Contract</label>
					<label><input type=radio name=userType value=2 checked>Permenant</label>
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Department</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<select name=dept class = 'typeproforms'>
						<option <?php if(@$dept == 'AE') echo "selected" ?>>AE
						<option <?php if(@$dept == 'ET') echo "selected" ?>>ET
						<option <?php if(@$dept == 'IT') echo "selected" ?>>IT
						<option <?php if(@$dept == 'Other') echo "selected" ?>>Other
					</select>
				</td>
			</tr><!--End of department code row-->
			<tr>
				<td width = '120' height = '30'>
					<strong>Name  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield1">
						<input type = 'text' name = 'name' id = 'name' 
						class = 'typeproforms'  value = "<?php if(isset($hodName))echo $hodName;?>"/>
					</span>	
				</td>
			</tr>
			<tr>
				<td width = '120' height = '30'>
					<strong>Email  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield10">
						<input type = 'email' name = 'email' id = 'email' 
						class = 'typeproforms'  value = "<?php if(isset($email))echo $email;?>"/>
					</span>	
				</td>
			</tr>
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
			</tr><!--End of department name row-->
			<tr>
				<td>&nbsp;</td>
				<td height = '30'>
					<input type = 'submit' name = '<?php echo $update['btn']['name'] ?>' class = 'button' value = '<?php echo $update['btn']['value'] ?>' />
				</td>
			</tr>
		  	</table>
			</form><!-- End of form-->
			<br/>
			<table width=100%>
				<tr id=tableheader>
					<th>
						#
					</th>
					<th>
						Name
					</th>
					<th>
						Email
					</th>
					<th>
						Department
					</th>
					<th>
						User Type
					</th>
					<th colspan=3>
						Action
					</th>
				</tr>
				<?php
				$data = $db->selectFields("tbl_users",array("ID","Name","Email","Dept","UserType"),array("UserType"=>5),null,"",true,'=');
				$out = "";
				for($i=0;$i<count($data);$i++){
					$out .= "<tr>";
					$out .= "<td>";
					$out .= $i + 1;
					$out .= "</td>";
					$out .= "<td>";
					$out .= $data[$i]['Name'];
					$out .= "</td>";
					$out .= "<td>";
					$out .= $data[$i]['Email'];
					$out .= "</td>";
					$out .= "<td align=center>";
					$out .= $data[$i]['Dept'];
					$out .= "</td>";
					$out .= "<td align=center>";
					$out .= users($data[$i]['UserType']);
					$out .= "</td>";
					$out .= "<td>";
					$out .= "<a href='./other.php?sql=reset.sql&id=".$data[$i]['ID']."' onclick='return confirmFunction(\"Reset Password for ".$data[$i]['Name']."?\")'>Reset</a>";
					$out .= "</td>";
					$out .= "<td>";
					$out .= "<a href='./other.php?sql=update.sql&id=".$data[$i]['ID']."'>Update</a>";
					$out .= "</td>";
					$out .= "<td>";
					$out .= "<a href='./other.php?sql=delete.sql&id=".$data[$i]['ID']."' onclick='return deleteFunction(\" ".$data[$i]['Name']."?\")'>Delete</a>";
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