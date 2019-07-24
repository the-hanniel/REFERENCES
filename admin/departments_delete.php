<?php
ob_start();
session_start();
/*Include the database configuration file*/
require_once("includes/config.php");
/*Include the default function file*/
require_once("includes/functions.php");
/*This function will check the session*/
checkSession();

if(isset($_GET['id']))
{
	$id 				= 	trim($_GET['id']);
	$deptDependencyArr	=	array("Student" => "SELECT u_uname  FROM `tbl_users` WHERE `stud_dept` LIKE '{$id}'",
								  "Teacher" => "SELECT teacher_first_name  FROM `tbl_teacher` WHERE `teacher_department_id` = {$id}",
								  "Subject"	=> "SELECT subject_name  FROM `tbl_subject` WHERE `subject_department_id` = {$id}"
								  );
	/*This function will check the dependency with the tables before delete the record*/
	$dependencyField		=	checkDependency($deptDependencyArr);
	/*If there is any dependency we have to throw error*/
	if($dependencyField)	
	{
		header("Location:department_search.php?keyword=&depend=$dependencyField");
		exit;
	}	
	else
	{	
		$deleteDept		=	"DELETE FROM tbl_department WHERE department_id={$id}";
		$deleteDeptRes	=	mysql_query($deleteDept)OR die(mysql_error());
		$deleteAffRows  = 	mysql_affected_rows();
		if($deleteAffRows)
		{
			header("Location:department_search.php?keyword=&msg=deleted");
			exit;
		}	
	}	
}
else
{
	header("Location:index.php?keyword=&msg=notdeleted");
	exit;
}	
?>
<?php
	ob_end_flush();
?>