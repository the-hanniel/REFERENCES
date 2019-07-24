<?php
ob_start();
session_start();
/*Include the database configuration file*/
require_once("../../admin/includes/config.php");
/*Include the default function file*/
require_once("../../admin/includes/functions.php");
require_once"../../LIB/config.php";
$db = new DBConnector("school_report_db");

/*This function will check the session*/
checkSession();
if(isset($_GET['msg']) && ($_GET['msg'] == 'success'))
{
	$successMsg		=	"User Successfully Saved!";
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
	#var_dump($_POST);
	$db->updatecells(array("Name"=>$_POST['name'],"Email"=>$_POST['email'],"Dept"=>$_POST['dept'],"UserType"=>$_POST['userType']),"tbl_users",array("ID"=>mysql_real_escape_string(trim($_POST['id']))));

	$successMsg = "SUCCESFULLY UPDATED";
}
if(isset($_POST['submit']))
{
	#echo '<pre>';print_r($_POST);die;
	$dept = mysql_real_escape_string(trim($_POST['dept']));
	$email = mysql_real_escape_string(trim($_POST['email']));
	$hodName = mysql_real_escape_string(trim($_POST['name']));
	$uName = mysql_real_escape_string(trim($_POST['uname']));
	$upass = mysql_real_escape_string(trim($_POST['pass']));
	$utype = mysql_real_escape_string(trim($_POST['userType']));
	if($utype == 2)
		$utype = mysql_real_escape_string(trim($_POST['staffType']));
	if($dept ==	'' || $hodName ==	'' || $email ==	'' || $uName == '' || $upass ==	'') {
		$errorMsg	=	'Error! Required Fields Cannot Be Left Blank!';
	} elseif(sha1($_POST['password']) !== sha1($_POST['pass'])){
		$errorMsg = "Error! Password does not match!";
	} else {
		$selectQuery			=	"SELECT * FROM `tbl_users` WHERE `UserName`='".$uName."' ";
		#echo $selectQuery;return;
		$existFlag				=	recordAlreadyExist($selectQuery);
		#var_dump($existFlag);
		#return;
		if($existFlag)
		{
			$errorMsg			=	"Error! User Name Already Exists!";
		}
		else
		{
			$deptInsertQuery	= 	"INSERT INTO `tbl_users` (`ID`, `Name`, `UserName`,`Email`,`Password`,`UserType`,`Dept`) VALUES (NULL, '{$hodName}', '{$uName}','{$email}','".sha1($upass)."','".$utype."','{$dept}')";
			/*Call General Function to Insert the record*/
			$insertFlag			= 	insertOrUpdateRecord($deptInsertQuery , $_SERVER['PHP_SELF']);
			if(!$insertFlag)
			{
				$errorMsg		=	"Error!Unable to save User!";
			}
		}
	}
}
$dept='';$name;$email;$userName;$password; $button='submit';$usertype='3'; $btnvalue='Add User';
if(@$_GET['sql'] === 'update.sql' && !empty($_GET['id'])){
	$data = $db->selectFields("tbl_users",array("ID","Name","Email","Dept","UserName","UserType"),array("ID"=>$_GET['id']),null,"",true,'=');
	$btnvalue="Save Updates";$usertype = $data[0]['UserType'];$password='*******';$button = 'update';$dept = $data[0]['Dept'];$name = $data[0]['Name'];$email = $data[0]['Email'];$userName = $data[0]['UserName'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>SYSTEM USERS REGISTRATION</title>
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

			<h2 style="font-size:23px;">USERS REGISTRATION</h2>
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
			<form name=mainform method = 'POST' action = "<?php echo $_SERVER['PHP_SELF'];?>">
			<?php if(@$_GET['sql'] == 'update.sql') echo "<input type=hidden name=id value=".$_GET['id'].">"; ?>
			<table width ="550" border = '0' cellspacing = '0' cellpadding = '0'>
			<tr  style="font-size:16px;">
				<td width = '120' height = '30'>
					<strong>User Type</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<script>
					function staffFunction(){
						document.getElementById('format').innerHTML = "<style>#utype{width:98px;}</style>";
						string = '<select id=utype class=typeproforms name=staffType>';
						string += '<option value=4>Discipline</option>'
						string += '</select>';
						document.getElementById('staff').innerHTML = string;
						document.mainform.dept.innerHTML = "<option value=1>School Administration</option>";
					}
					function nonStaffFunction(){
						document.getElementById('format').innerHTML = "";
						document.getElementById('staff').innerHTML = "";
						window.location='<?php echo $_SERVER['PHP_SELF']; ?>';
					}
				</script>
				<td height = '30'>
					<label id=format></label>
					<select name=userType id=utype class=typeproforms>
						<option onclick='staffFunction();' value=2 <?php if($usertype==2) echo "selected" ?>>Staff
						<option onclick='nonStaffFunction();' value=3 <?php if($usertype==3) echo "selected" ?>>Teacher
					</select>
					<span id=staff></span>
				</td>
			</tr>
			<tr  style="font-size:16px;">
				<td width = '120' height = '30'>
					<strong>Department</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<?php
					$dpt = $dept;
					$dept = $db->selectFields($tbl='tbl_dept',$field=array("ID","DepartmentName","Acronym"),$condition=null,$limit=null,$order="ORDER BY DepartmentName ASC",$indexed=true,$sign='=');
					?>
					<select name=dept class = 'typeproforms'>
						<?php
						for($i=0;$i<count($dept);$i++) echo "<option value='".$dept[$i]['ID']."' ".(($dept[$i]['ID'] == $dpt)?"selected":"").">".$dept[$i]['DepartmentName'].($dept[$i]['Acronym'] != 'Administration'?"(".$dept[$i]['Acronym'].")":"");
						?>
					</select>
				</td>
			</tr><!--End of department code row-->
			<tr  style="font-size:16px;">
				<td width = '120' height = '30'>
					<strong>Full Name  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield1">
						<input type = 'text' name = 'name' id = 'name'
						class = 'typeproforms'  value = "<?php if(isset($name))echo $name;?>"/>
					</span>
				</td>
			</tr>
			<tr  style="font-size:16px;">
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
			<tr  style="font-size:16px;">
				<td width = '120' height = '30'>
					<strong>User Name  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield2">
						<input type = 'text' name = 'uname' id = 'uname' <?php if(@$_GET['sql'] == 'update.sql') echo "readonly"; ?>
						class = 'typeproforms'  value = "<?php if(isset($userName))echo $userName;?>"/>
					</span>
				</td>
			</tr>
			<tr  style="font-size:16px;">
				<td width = '120' height = '30'>
					<strong>Password  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield3">
						<input type = 'password' <?php if(@$_GET['sql'] == 'update.sql') echo "readonly"; ?> value='<?php if(isset($password))echo $password;?>' maxlength='8' name = 'password' id = 'uname'
						class = 'typeproforms'  value = ""/>
					</span>
				</td>
			</tr>
			<tr  style="font-size:16px;">
				<td width = '120' height = '30'>
					<strong>Confirm  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield4">
						<input type = 'password' <?php if(@$_GET['sql'] == 'update.sql') echo "readonly"; ?> value='<?php if(isset($password))echo $password;?>' maxlength='8' name = 'pass' id = 'uname'
						class = 'typeproforms'  value = ""/>
					</span>
				</td>
			</tr><!--End of department name row-->
			<tr>
				<td>&nbsp;</td>
				<td height = '30'>
					<input type = 'submit' name = '<?php echo $button; ?>' class = 'button' value = '<?php echo $btnvalue; ?>' />
				</td>
			</tr>
		  	</table>
			</form><!-- End of form-->
			<style>
				table{border-color:#eee;}
				th{ background-color:#555; border-color:#eee; color:#eee; }
				tr#n0{ background-color:#ddd; }
				tr#n0:hover{ background-color:#fef; }
				tr#n1{ background-color:#eee; }
				tr#n1:hover{ background-color:#fef; }
				td{ border-color:#eee; }
			</style>
			<table width=100% border=1>
				<tr id=tableheader>
					<th>#</th><th>Name</th><th>Email</th><th>UserName</th><th>Department</th><th>User Type</th><th colspan=3>Action</th>
				</tr>
				<?php
				$data = $db->selectInMoreTable($lbl=array("tbl"=>array("tbl_users","tbl_dept"),"fld"=>array("tbl_users"=>array("ID","Name","Email","UserName","UserType"),"tbl_dept"=>array("DepartmentName","Acronym")),"condition"=>array("tbl_users`.`Dept"=>"tbl_dept`.`ID")),$multirows=true,$indexed=true, $order="ORDER BY `tbl_dept`.`Acronym` ASC, `tbl_users`.`Name` ASC");
				//$data = $db->selectFields("tbl_users",array("ID","Name","Email","UserName","Dept","UserType"),array("UserType"=>2),null,"",true,'=');
				$out = "";
				for($i=0;$i<count($data);$i++){
					$out .= "<tr id=n".($i%2).">";
					$out .= "<td>";
					$out .= $i + 1;
					$out .= "</td>";
					$out .= "<td>";
					$out .= $data[$i]['Name'];
					$out .= "</td>";
					$out .= "<td align=left>";
					$out .= $data[$i]['Email'];
					$out .= "</td>";
					$out .= "<td align=left>";
					$out .= $data[$i]['UserName'];
					$out .= "</td>";
					$out .= "<td align=left>";
					$out .= $data[$i]['Acronym'];
					$out .= "</td>";
					$out .= "<td align=left>";
					switch ($data[$i]['UserType']) {
						case 1:
							$out .= "Study";
							break;
						case 2:
							$out .= "Don't Know";
							break;
						case 3:
							$out .= "Teacher";
							break;
						case 4:
							$out .= "Discipline";
							break;
					}
					$out .= "</td>";
					/*$out .= "<td align=center>";
					$out .= users($data[$i]['UserType']);
					$out .= "</td>";* /
					$out .= "<td>";
					$out .= "<a href='./?sql=reset.sql&id=".$data[$i]['ID']."' onclick='return confirmFunction(\"Reset Password for ".$data[$i]['Name']."?\")'>Reset</a>";
					$out .= "</td>";*/
					$out .= "<td>";
					$out .= "<a href='./?sql=update.sql&id=".$data[$i]['ID']."'>Update</a>";
					$out .= "</td>";
					$out .= "<td>";
					$out .= "<a href='./?sql=delete.sql&id=".$data[$i]['ID']."' onclick='return deleteFunction(\" ".$data[$i]['Name']."?\")'>Delete</a>";
					$out .= "</td>";
					$out .= "</tr>";
				}
				echo $out;
				?>
			</table><br />
		</div><!-- End of main_body div(main white div)-->
		<?php
			/*This function will return the logo div string to the sidebody*/
			//echo plotLogoDiv("../../admin/images/lgo.png");
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
