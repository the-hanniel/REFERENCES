<?php
/* connect to database */
require_once"../../LIB/config.php";
$db = new DBConnector("tct_leave");
$id = $_GET['id'];
$days = $_GET['days'];
/* get the start date from db */
$start = $db->select1cell("tbl_leave","LeaveFrom",array("ID"=>$id),true);
$day = $start[strlen($start)-2].$start[strlen($start)-1];
$start[strlen($start)-1] = "";$start[strlen($start)-2]="";
$start = trim($start);
$day += $days;# echo $days;
if($day <= 30) $start .= $day;
else{
	/* get the month */
	$month = $start[strlen($start)-3].$start[strlen($start)-2];
	$start[strlen($start)-1] = "";$start[strlen($start)-2]="";$start[strlen($start)-3]="";
	$start = trim($start);
	$month += 1;
	if($month <= 12){
		if($month<10) $month = "0".$month;
		$start .= $month."-".((int)$day - 30);
	} else{
		/* get the year */
		$year = $start[0].$start[1].$start[2].$start[3];
		$year += 1; $moth = (int)$month-12;
		if($moth<10) $moth = "0".$moth;
		$dy = (int)$day - 30;
		if($dy<10) $dy = "0".$dy;
		$start = $year."-".$moth."-".$dy;
	}
}
#echo $start;
/* Update the database */
if($db->updateCells(array("LeaveTo"=>$start),"tbl_leave",array("ID"=>$id))){
	mysql_query("UPDATE tbl_leave_counter SET `RemainingDays` = RemainingDays - ".$_GET['days']." WHERE `UserID`='".$_GET['uid']."'");
	echo "Done!
OK";
} else{
	echo "Not Saved!
Error!";
}
?>