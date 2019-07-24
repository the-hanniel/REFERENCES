<?php
session_start();

#echo "YES";
require_once"../../LIB/config.php";
$db = new DBConnector("school_report_db".$_SESSION['year']);
#echo $_GET['year'];
$data = $db->selectFields($tbl='tbl_course',$field=array("CourseName","Acronym"),$condition=array("Acronym"=>"LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."%')"),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='=',$multiplereference=false);
#var_dump($data);
$str = "";
if(count($data)>0){
	$str .= "<table border=0>"; $tr = false; $str .= "<tr>";
	for($i=1;$i<=count($data);$i++){
		if($i%3==0) $tr = true;
		else $tr = false;
			$str .= "<td class='".$data[$i-1]['Acronym']."' style='border-left:1px solid #aaa; cursor:pointer;'><div onmouseover='document.getElementById(\"styles\").innerHTML=\"<style>.".$data[$i-1]['Acronym']."{ background-color:#ddffdd; color:#000; }</style>\"' onmouseout='document.getElementById(\"styles\").innerHTML=\"\"'>";
				$str .= "<b>".$data[$i-1]['Acronym']."</b>:";
			$str .= "</div></td>";
			$str .= "<td class='".$data[$i-1]['Acronym']."' style='cursor:pointer;'><div onmouseover='document.getElementById(\"styles\").innerHTML=\"<style>.".$data[$i-1]['Acronym']."{ background-color:#ddffdd; color:#000; }</style>\"' onmouseout='document.getElementById(\"styles\").innerHTML=\"\"'>";
				$str .= $data[$i-1]['CourseName'];# var_dump($tr);
			$str .= "</div></td>";
		if($tr) $str .= "</tr><tr>";
	}
	if(!$tr) $str .= "</tr>";
	$str .= "</table>";
}
echo $str;
?>
<!--
<table>
	<tr>
	<tr>
<table> -->