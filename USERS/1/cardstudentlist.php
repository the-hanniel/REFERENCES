
<?php
require_once"../../LIB/config.php";
$db = new DBConnector("school_report_db".$_GET['year']);
$year = $_GET['year'];
 echo "Class <b>S".$_GET['class'].$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true)."</b>";
?>
<div name=paging id=paging></div>
<table width=100% border=1>
	<tr id=tableheader>
		<th>#</th><th>Name</th>
	</tr>
	<?php
	//$data = $db->selectInMoreTable($lbl=array("tbl"=>array("tbl_student","tbl_dept","tbl_marks"),"fld"=>array("tbl_student"=>array("ID","FirstName","LastName","Class"),"tbl_dept"=>array("Acronym"),"tbl_marks"=>array()),"condition"=>array("tbl_marks`.`StudentID"=>"tbl_student`.`ID","tbl_dept`.`ID"=>"tbl_student`.`DeptID","tbl_student`.`DeptID"=>$_GET['dept'],"tbl_student`.`Class"=>$_GET['class'])),$multirows=true,$indexed=true, $order="ORDER BY `tbl_dept`.`Acronym` ASC, `tbl_student`.`Class` ASC, `tbl_student`.`FirstName` ASC, `tbl_student`.`LastName` ASC");
	$data = $db->selectInMoreTable($lbl=array("tbl"=>array("school_report_db".$year."`.`tbl_student","tbl_dept","school_report_db`.`tbl_student"),"fld"=>array("school_report_db`.`tbl_student"=>array("ID","FirstName","LastName"),"school_report_db".$year."`.`tbl_student"=>array("Class","StudentPhoto"),"tbl_dept"=>array("Acronym")),"condition"=>array("school_report_db`.`tbl_student`.`ID"=>"tbl_student`.`StudentID","tbl_dept`.`ID"=>"tbl_student`.`DeptID","tbl_student`.`DeptID"=>$_GET['dept'],"tbl_student`.`Class"=>$_GET['class'])),$multirows=true,$indexed=true, $order="ORDER BY `tbl_dept`.`Acronym` ASC, `tbl_student`.`Class` ASC, `school_report_db`.`tbl_student`.`FirstName` ASC, `school_report_db`.`tbl_student`.`LastName` ASC");
	$out = "";
	if(count($data)<1){
		echo "<tr><td colspan=7 align=center>No Student in S".$_GET['class'].$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true)."</td></tr>";
	} else{
		$pages = count($data)/16;
		#echo $pages;
		if($pages >1){
			/* map paging to be displayed in paging div */
			$str = "<select name=paging class=typeproform>";
			$paged = "cardstudentlist.php";
			for($i=0;$i<=$pages;$i++){
				//studentList(deptid=3,classid=4,start=0,end=12,page='cardstudentlist.php',move=0,year='<?php echo $_SESSION['year'] ? >')
				$str .= "<option ".(($_GET['start']/16) == $i?"selected":"")." onclick='studentList(".$_GET['dept'].",".$_GET['class'].",".($i*16).",".((($i+1)*16)).",\"{$paged}\",0,{$_GET['year']})'>".($i+1);
			}
			$str .= "</select>";
			echo "<div>".$str."</div>";
		}
		#var_dump($data);
		
	for($i=$_GET['start'];$i<($_GET['end']>count($data)?count($data):$_GET['end']);$i++){
		$out .= "<tr id=n".($i%2).">";
		$out .= "<td>";
		$out .= $i + 1;
		$out .= "</td>"; 
		$a = "	document.getElementById(\"name\").innerHTML = \"".$data[$i]['FirstName']." ".$data[$i]['LastName']."\";
				document.getElementById(\"class\").innerHTML = \"S".$data[$i]['Class'].$data[$i]['Acronym']."\";
				document.getElementById(\"photo\").innerHTML = \"<img src=./ajaximage/uploads/".$data[$i]['StudentPhoto']." alt=photo id=imgs />\";
				document.getElementById(\"acyear\").innerHTML = \"".$_GET['year']."\";
				document.getElementById(\"upload\").style.display = \"none\";
				document.getElementById(\"expdate\").innerHTML = \"Dec ".$_GET['year']."\"
		";
		if($data[$i]['StudentPhoto'] == '' || $data[$i]["StudentPhoto"] == null){
			$a .= ";document.getElementById(\"upload\").innerHTML=\"<input class=typeproform type=file name=photoimg id=photoimg />\"";
			$a .= ";document.getElementById(\"upload\").style.display=\"block\"";
			$a .= ";document.imageform.id.value=\"".$data[$i]['ID']."\"";
		}
		//onclick='document.getElementById(\"studentcard1\").style.display=\"block\";document.getElementById(\"studentcard2\").style.display=\"none\";document.imageform.id.value=\"".$data[$i]['ID']."\";document.imageform.studentname.value=\"".$data[$i]['FirstName']." ".$data[$i]['LastName']."\";'
		$out .= "<td> <div onclick='document.getElementById(\"studentcard1\").style.display=\"none\";document.getElementById(\"studentcard2\").style.display=\"block\"; {$a};' >";
		$out .= $data[$i]['FirstName'];
		$out .= " ".$data[$i]['LastName'];
		$out .= "</div></td>";
		$out .= "</tr>";
	} }
	echo $out;
	?>
</table>