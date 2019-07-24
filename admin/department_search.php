<?php
ob_start();
session_start();
/*Include the database configuration file*/
require_once("includes/config.php");
/*Include the default function file*/
require_once("includes/functions.php");
/*This function will check the session*/
checkSession();
if(isset($_GET['msg']) && ($_GET['msg'] == 'deleted'))
{
	$successMsg		=	"Department Successfully Deleted!";	
}
if(isset($_GET['msg']) && ($_GET['msg'] == 'notdeleted'))
{
	$errorMsg		=	"Error! Unable to Delete Department!";	
}
/*To display the dependency message - Delete*/
if(isset($_GET['depend']))
{
	$dependMsg		=	trim($_GET['depend']);
	$errorMsg		=	"Error! Dependency Exists in {$dependMsg}";	
	var_dump($errorMsg);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Student Search</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!--Link to the template css file-->
	<link rel="stylesheet" type="text/css" href="CSS/style.css" />
	<!--Link to Favicon -->
	<link rel="icon" href="images/favi_logo.gif"/>
</head>
<body>
<div class="main">
	<?php
  		//To Plot Menus in this Page
  		echo plotHeaderMenuInfo("department_new.php");
  	?>
  <div class="body">
    <div class="main_body">
		<h2>Department - Search</h2>
    	<?php
				/*Display the Messages*/
				if(isset($errorMsg))
				{
					echo "<p><span class = 'error'>{$errorMsg}</span></p>";	
				}
				elseif(isset($successMsg))
				{
					echo "<p><span class = 'success'>{$successMsg}</span></p>";	
				}
		?>	
		<table id='listEntries' width="550" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" style="border-collapse:collapse;">
        <?php
          $keyword			=	mysql_real_escape_string(trim($_GET['keyword']));
	   	  $searchQry		=	"SELECT *  FROM `tbl_department` WHERE `department_code` LIKE '{$keyword}%' OR `department_name` LIKE '{$keyword}%' ORDER BY department_id DESC";
		  $searchRes 		= 	mysql_query($searchQry);
		  $searchNumRows	=	mysql_num_rows($searchRes);
          if (!$searchNumRows)
		  {
	           echo '<tr>
                <td height="30" colspan="3" align="center"><strong style="color:red;">Search Not Found</strong></td>
              </tr>';
		  }
		  else
		  { 
		  ?>
		      <tr>
                <th  height="30" align="center"><strong>Department Code</strong></td>
                <th  height="30" align="center"><strong>Department Name</strong></td>
                <th  height="30" align="center"><strong>Actions</strong></td>
              </tr>

			<?php
			 	while ($searchArr = mysql_fetch_assoc($searchRes))
				{
					//echo "<pre>";print_r($searchArr);die;	
			?>
					<tr>
						<td height="30">&nbsp;
							<?php 
								echo $searchArr["department_code"];
							?>
						</td>
						<td height="30">&nbsp;
							<?php 
								echo $searchArr["department_name"];
							?>
						</td>
						<td align="center">
							<a href="departments_edit.php?id=<?php echo $searchArr["department_id"] ?>&keyword=<?php echo $_GET["keyword"]?>">Edit</a> | 
							<a onclick="javascript: return confirm('Sure! Do you want to Delete?');" href="departments_delete.php?id=<?php echo $searchArr["department_id"]?>&keyword=
								<?php $_GET["keyword"]?>">Delete
							</a>
						</td>
					</tr>
          <?php
		  		}
			}
		  ?>
        </table>
      <p>&nbsp;</p>

    </div>
		<?php
			/*This function will return the logo div string to the sidebody*/
			echo plotLogoDiv();
			echo plotSearchDiv('department_search.php');
		?><!-- End of Search Div-->
    </div>	
		<div class="clr"></div>
		<br/><br/>
	</div><!-- End of Body div-->
</div><!--End of Main Div-->	
<?php
	/*This function will return the footer div information*/
	echo plotFooterDiv();
?>
</body>
</html>
<?php
	ob_end_flush();
?>