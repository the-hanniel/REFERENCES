
<?php
require_once"../../LIB/config.php";
$db = new DBConnector("school_report_db".$_GET['year']);
$year = $_GET['year'];
 echo "Class <b>S".$_GET['class'].$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true)."</b>";
?>
<div name=paging id=paging></div>
<input type="text" onkeyup="checkIt()" id="myInput" placeholder="Serch here..." />
<table width=100% border=1>
  <thead id="jhk">
  	<tr id='tableheader'>
  		<th>#</th><th>Name</th>
  	</tr>
  </thead>

	<?php
	//$data = $db->selectInMoreTable($lbl=array("tbl"=>array("tbl_student","tbl_dept","tbl_marks"),"fld"=>array("tbl_student"=>array("ID","FirstName","LastName","Class"),"tbl_dept"=>array("Acronym"),"tbl_marks"=>array()),"condition"=>array("tbl_marks`.`StudentID"=>"tbl_student`.`ID","tbl_dept`.`ID"=>"tbl_student`.`DeptID","tbl_student`.`DeptID"=>$_GET['dept'],"tbl_student`.`Class"=>$_GET['class'])),$multirows=true,$indexed=true, $order="ORDER BY `tbl_dept`.`Acronym` ASC, `tbl_student`.`Class` ASC, `tbl_student`.`FirstName` ASC, `tbl_student`.`LastName` ASC");
	$data = $db->selectInMoreTable($lbl=array("tbl"=>array("school_report_db".$year."`.`tbl_student","tbl_dept","school_report_db`.`tbl_student"),"fld"=>array("school_report_db`.`tbl_student"=>array("ID","FirstName","LastName"),"school_report_db".$year."`.`tbl_student"=>array("Class"),"tbl_dept"=>array("Acronym")),"condition"=>array("school_report_db`.`tbl_student`.`ID"=>"tbl_student`.`StudentID","tbl_dept`.`ID"=>"tbl_student`.`DeptID","tbl_student`.`DeptID"=>$_GET['dept'],"tbl_student`.`Class"=>$_GET['class'])),$multirows=true,$indexed=true, $order="ORDER BY `tbl_dept`.`Acronym` ASC, `tbl_student`.`Class` ASC, `school_report_db`.`tbl_student`.`FirstName` ASC, `school_report_db`.`tbl_student`.`LastName` ASC");
	$out = "";
	if(count($data)<1){
		echo "<tr><th colspan='2' align='center'>No Student in S".$_GET['class'].$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true)."</th></tr>";
	} else{
		$pages = count($data)/16;
		#echo $pages;
		if($pages >1){
			/* map paging to be displayed in paging div */
			$str = "<select name='paging' class='typeproform'>";
			for($i=0;$i<=$pages;$i++){
				$str .= "<option ".(($_GET['start']/16) == $i?"selected":"")." onclick='reportStudentList({$_GET['year']},".$_GET['dept'].",".$_GET['class'].",".($i*16).",".((($i+1)*16)).")'>".($i+1);
			}
			$str .= "</select>";
			echo "<div>".$str."</div>";
		} ?>
    <tbody id='stdListes'>
    <?php
		#var_dump($data);

	for($i=$_GET['start'];$i<($_GET['end']>count($data)?count($data):$_GET['end']);$i++){
		$out .= "<tr id=n".($i%2).">";
		$out .= "<td>";
		$out .= $i + 1;
		$out .= "</td>";
		$out .= "<td> <div onclick='studentReport({$year},document.slist.dept.value,document.slist.classid.value,start=0,end=12,page=\"studentreport.php\",document.slist.termid.value,".$data[$i]['ID'].",".($i+1).");' >";
		$out .= $data[$i]['FirstName'];
		$out .= " ".$data[$i]['LastName'];
		$out .= "</div></td>";/*
		$out .= "<td>";
		$out .= $data[$i]['Acronym'];
		$out .= "</td>";
		$out .= "<td>";
		$out .= "S".$data[$i]['Class'].$data[$i]['Acronym'];
		$out .= "</td>";/*
		$out .= "<td>";
		$out .= "<a href='./other.php?sql=reset.sql&id=".$data[$i]['ID']."' onclick='return confirmFunction(\"Reset Password for ".$data[$i]['FirstName']."?\")'>Reset</a>";
		$out .= "</td>";* /
		$out .= "<td>";
		$out .= "<a href='./student.php?sql=update.sql&id=".$data[$i]['ID']."'>Update</a>";
		$out .= "</td>";
		$out .= "<td>";
		$out .= "<a href='./student.php?sql=delete.sql&id=".$data[$i]['ID']."' onclick='return deleteFunction(\" ".$data[$i]['FirstName']."?\")'>Delete</a>";
		$out .= "</td>";*/
		$out .= "</tr>";
	}
 }
	echo $out;
	?>
  </tbody>
</table>
