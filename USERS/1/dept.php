<?php
ob_start();
session_start();
/*Include the database configuration file*/
require_once("../../admin/includes/config.php");
/*Include the default function file*/
require_once("../../admin/includes/functions.php");
require_once"../../LIB/config.php";
$db = new DBConnector("school_report_db".$_SESSION['year']);

/*This function will check the session*/
checkSession();
if(isset($_GET['msg']) && ($_GET['msg'] == 'success'))
{
	$successMsg		=	"User Successfully Saved!";	
}

$update = array("msg"=>"Register","btn"=>array("name"=>"submit","value"=>"Save Data"));
if(@$_GET['sql'] == 'update.sql' && !empty($_GET['id'])){
	$update['msg'] = "Update"; $update['btn'] = array("name"=>'update',"value"=>"Save Updates");
	$data = $db->selectOneRowFromTable("tbl_dept",array("ID"=>mysql_real_escape_string(trim($_GET['id']))),true);
	#var_dump($data);
	//$dept = $data['Dept'];
	$hodName = $data['DepartmentName'];
	$acronym = $data['Acronym'];
	//$upass = "********";
}
if(@$_GET['sql'] == "delete.sql" && !empty($_GET['id'])){
	$db->delete1row("tbl_dept",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$successMsg		=	"Department Successfully Deleted!";
}
if(@$_GET['sql'] == "reset.sql" && !empty($_GET['id'])){
	$db->updatecells(array("Password"=>sha1("123")),"tbl_users",array("ID"=>mysql_real_escape_string(trim($_GET['id']))));
	$successMsg		=	"Password Successfully Reseted to <b>123</b>!";
}
if(@$_POST['update']){
	if($db->updatecells(array("DepartmentName"=>mysql_real_escape_string(trim($_POST['name'])),"Acronym"=>mysql_real_escape_string(trim($_POST['acronym']))),"tbl_dept",array("ID"=>mysql_real_escape_string(trim($_POST['id']))))){
		$successMsg		=	"Department Successfully Update!";
		$hodName = "";
		$acronym = "";
	} else{
		$errorMsg = "Error! Fail To Update!";
	}
}
if(isset($_POST['submit']))
{	
	#echo '<pre>';print_r($_POST);die;
	//$dept = mysql_real_escape_string(trim($_POST['dept']));
	$hodName = mysql_real_escape_string(trim($_POST['name']));
	$acronym = mysql_real_escape_string(trim($_POST['acronym']));
	//$upass = mysql_real_escape_string(trim($_POST['pass']));
	if($hodName ==	'' || $acronym == '' ) {
		$errorMsg	=	'Error! Required Fields Cannot Be Left Blank!';	
	} else {	
		$selectQuery			=	"SELECT * FROM `tbl_dept` WHERE `Acronym`='".$acronym."'";
		$existFlag				=	recordAlreadyExist($selectQuery);
		if($existFlag)
		{
			$errorMsg			=	"Error! Department Already Exists !";
		}	
		else
		{	
			$deptInsertQuery	= 	"INSERT INTO `tbl_dept` (`ID`, `DepartmentName`,`Acronym`) VALUES (NULL, '{$hodName}','{$acronym}')";
			/*Call General Function to Insert the record*/
			$insertFlag			= 	insertOrUpdateRecord($deptInsertQuery , $_SERVER['PHP_SELF']);
			if(!$insertFlag)
			{
				$errorMsg		=	"Error!Unable to save department information !";
			}
		}	
	}	
}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Department Registration</title>
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
			<h2 style="font-size:23px;">Department Management Page</h2>
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
			<tr  style="font-size:16px;">
				<td width = '150' height = '30'>
					<strong>Department Name  </strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield1">
						<input type = 'text' name = 'name' id = 'name' 
						class = 'typeproforms'  value = "<?php if(isset($hodName))echo $hodName;?>"/>
					</span>	
				</td>
			</tr>
			<tr style="font-size:16px;">
				<td width = '150' height = '30'>
					<strong>Acronym</strong>
					<span class = 'mandatory'>*</span>
				</td>
				<td height = '30'>
					<span id="sprytextfield10">
						<input type = 'text' name = 'acronym' id = 'acronym' 
						class = 'typeproforms'  value = "<?php if(isset($acronym))echo $acronym;?>"/>
					</span>	
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
			</tr> */ ?><!--End of department name row-->
			<tr>
				<td>&nbsp;</td>
				<td height = '30'>
					<input type = 'submit' name = '<?php echo $update['btn']['name'] ?>' class = 'button' value = '<?php echo $update['btn']['value'] ?>' />
				</td>
			</tr>
		  	</table>
			</form><!-- End of form-->
			<br/>
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
					<th>
						#
					</th>
					<th>
						Department Name
					</th>
					<th>
						Acrnym
					</th>
					<th colspan=3 width=10%>
						Action
					</th>
				</tr>
				<?php
				$data = $db->selectFields("tbl_dept",array("ID","DepartmentName","Acronym"),null,null,"ORDER BY DepartmentName ASC",true,'=');
				$out = "";
				for($i=0;$i<count($data);$i++){
					$out .= "<tr id=n".($i%2)."><td>";
					$out .= $i + 1;
					$out .= "</td><td>".$data[$i]['DepartmentName'];
					$out .= "</td><td>".$data[$i]['Acronym'];
					//$out .= "</td><td align=center>".users($data[$i]['UserType']);
					//$out .= "</td><td><!--<a href='./hr.php?sql=reset.sql&id=".$data[$i]['ID']."' onclick='return confirmFunction(\"Reset Password for ".$data[$i]['Name']."?\")'>Reset</a>";
					$out .= "</td>";
					$out .= "<td>";
					$out .= ($data[$i]['Acronym']!='Administration' && $data[$i]['Acronym']!='GNC'?"<a href='./dept.php?sql=update.sql&id=".$data[$i]['ID']."'>Update</a>":"");
					$out .= "</td>";
					$out .= "<td>";
					$out .= ($data[$i]['Acronym']!='Administration' && $data[$i]['Acronym']!='GNC'?"<a href='./dept.php?sql=delete.sql&id=".$data[$i]['ID']."' onclick='return deleteFunction(\" ".$data[$i]['DepartmentName']." Department\")'>Delete</a>":"&nbsp;");
					$out .= "</td>";
					$out .= "</tr>";
				}
				echo $out;
				?>
			</table> <br />
		</div><!-- End of main_body div(main white div)-->
		<!--<?php
			/*This function will return the logo div string to the sidebody*/
			//echo plotLogoDiv("../../admin/images/lgo.png");
			//echo plotSearchDiv('department_search.php');
			/*This function will list the departments*/
			//echo listDepartment();
		?>-->		
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
	var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "custom",{isRequired:true,characterMasking:/[A-Z]/,
						useCharacterMasking:true, validateOn:["change"]});
-->	
</script>
</body>
</html>
<?php
	ob_end_flush();
?>