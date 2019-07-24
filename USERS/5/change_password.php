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
	$successMsg		=	"Password Successfully Changed!";	
}
if(isset($_POST['submit']))
{	
	#var_dump($_POST);
	$currPass		=	mysql_real_escape_string(trim($_POST['current_password']));
	$newPass		=	mysql_real_escape_string(trim($_POST['new_password']));
	$retypePass		=	mysql_real_escape_string(trim($_POST['retype_password']));
	$currMD5Pass 	= 	sha1($currPass);
	
	if($currPass == '' || $newPass == '' || $retypePass == '')
	{
		$errorMsg	=	"Error! Required Fileds cannot be left blank!";
	
	}
	else
	{
		$userSelQry		=	"SELECT *  FROM `tbl_users` WHERE `ID` = {$_SESSION['u_id']}  AND `Password`='{$currMD5Pass}'";
		#echo $userSelQry;
		$existFlag		=	recordAlreadyExist($userSelQry);#return;
		if(!$existFlag)
		{
			$errorMsg	=	"Error! Wrong Current Password!";
		}
		else
		{
			if($newPass !== $retypePass)
			{
				$errorMsg		=	"Error!New Password and Retype Password must be same!";
			}
			elseif(strlen($newPass) < 5)
			{
				$errorMsg		=	"Error!New Password must at least 5 characters!";
			}
			else
			{
				$newMD5Pass 	=  sha1($newPass);
				$passUpdateQry 	=  "UPDATE `tbl_users` SET `Password` = '{$newMD5Pass}' WHERE `tbl_users`.`ID` = {$_SESSION['u_id']}";
				$insertFlag		=  insertOrUpdateRecord($passUpdateQry , $_SERVER['PHP_SELF']);
				if(!$insertFlag)
				{
					$errorMsg		=	"Error!Unable to change the Password!";
				}
			}
		}
	}
}	
#var_dump($_SESSION);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Change Password</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!--Link to the template css file-->
	<link rel="stylesheet" type="text/css" href="../../admin/css/style.css" />
	<!--Link to Favicon -->
	<link rel="icon" href="../../admin/images/favi_logo.gif"/>
	<!--Link to Validation JS source File -->
	<script type = 'text/javascript' language='javascript' src = '../admin/js/validation.js'></script>
	<!-- Spry Stuff Starts here-->
	<link href="../../admin/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../admin/spry/textfieldvalidation/SpryValidationTextField.js"></script>
	<!-- Spry Stuff Ends Here-->
</head>
<body>
<div class="main">
	<?php
		/*This function will return the header string with menu information*/
		echo plotHeaderMenuInfo(basename(__FILE__),5,$db);
	?>
	<div class="body">
		<div class="main_body">
			<h2>Change Password</h2>
			<?php
				#var_dump($_SESSION);
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
				<td width = '130' height = '30'>
					<strong>Current Password</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield1">
						<input type = 'password' name = 'current_password' id = 'current_password' 
						value = "<?php if(isset($currPass))echo $currPass;?>" class = 'typeproforms' />
					</span>	
				</td>
			</tr>	
			<tr>
				<td width = '130' height = '30'>
					<strong>New Password</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield2">
						<input type = 'password' name = 'new_password' id = 'new_password' 
						value = "<?php if(isset($newPass))echo $newPass;?>" class = 'typeproforms' />
					</span>	
				</td>
			</tr>
			<tr>
			<tr>
				<td width = '130' height = '30'>
					<strong>Retype Password</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield3">
						<input type = 'password' name = 'retype_password' id = 'retype_password' 
						value = "<?php if(isset($retypePass))echo $retypePass;?>" class = 'typeproforms' />
					</span>	
				</td>
			</tr>
				<td>&nbsp;</td>
				<td height = '30'>
					<input type = 'submit' name = 'submit' class = 'button' value = 'Change Password' />
				</td>
			</tr>
		  	</table>
			</form><!-- End of form-->
			<br/>
		</div><!-- End of main_body div(main white div)-->
		<?php
			/*This function will return the logo div string to the sidebody*/
			echo plotLogoDiv("../../admin/images/logo.png");
		?>
	</div><!-- End of Body div-->
	<div class='clr'></div>
</div><!--End of Main Div-->	
<?php
	/*This function will return the footer div information*/
	echo plotFooterDiv();
?>
<script type="text/javascript">
<!--
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "custom",{isRequired:true,characterMasking:/[a-zA-Z1-9. ]/,
						useCharacterMasking:true, validateOn:["change"]});
	var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "custom",{isRequired:true,characterMasking:/[a-zA-Z1-9. ]/,
						useCharacterMasking:true, validateOn:["change"]});
	var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "custom",{isRequired:true,characterMasking:/[a-zA-Z1-9. ]/,
						useCharacterMasking:true, validateOn:["change"]});						
-->	
</script>
</body>
</html>
<?php
	ob_end_flush();
?>