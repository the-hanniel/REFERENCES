<?php
session_start();
require_once"../../LIB/config.php";
$db = new DBConnector($dbname = "school_report_db".$_SESSION['year']);
#var_dump($_GET);
$mark = null;
if($_GET['mark'] !== "") $mark = $_GET['mark'];
if ($_GET['mark'] > 40) $mark = null;
#var_dump($mark);
echo "<div id='sth'></div>";
#echo $_GET['field'];
if($db->updateCells($data=array($_GET['field']=>$mark),$tbl="tbl_marks",$condition=array("StudentID"=>$_GET['student']))){
	#echo "<span class='success'>Done</span>";
	if($_GET['field'][7] == 1) echo "<style>#s".$_GET['row']."{ border:1px solid green}</style>";
	if($_GET['field'][7] == 2) echo "<style>#t".$_GET['row']."{ border:1px solid green}</style>";
}
?>
