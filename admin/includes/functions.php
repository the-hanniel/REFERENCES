<?php
/* Include the database configuration file */
require_once("config.php");

/* This function will return the header string with menu information */
function suffix($str,$type){
	#if($str[3] == )
	$suffix = $str[4].$str[5];
	$n = (int)$suffix;
	++$n;
	if($n<10) $n ="0".$n;
	if(is_int($n)) $n = (string)$n;
	#var_dump((string)$n);
	$t = $n[0];
	#var_dump($n);
	if($type) $n[0] = '9';
	else $n[0] = $t;
	return $n;
}
function loadCourses1(&$totals,&$status,$dl=null,$type=0,$db=null,$studentid=1,&$sfields=null){
	#$totals['01']="30";
	//Search for Caurses of The given Class
	if(!$type)$cndtn = array("Acronym"=>"LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."9%')");
	else $cndtn = array("Acronym"=>array("LIKE('".$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true).$_GET['class']."%')","Acronym"=>"NOT LIKE('".$db->select1cell("tbl_dept","Acronym",array("ID"=>$_GET['dept']),true).$_GET['class']."9%')"));
	//array("Acronym"=>($type?"NOT ":"")."LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."9%')")
	$data = $db->selectFields($tbl='tbl_course',$field=array("CourseName","Acronym","Maximum"),$condition=array("Acronym"=>($type?"LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."%') && `Acronym` NOT ":"")."LIKE('".$db->select1cell($tbl="tbl_dept",$field="Acronym",$condition=array("ID"=>$_GET['dept']),true).$_GET['class']."9%')"),$limit=null,$order="ORDER BY Acronym ASC",$indexed=true,$sign='=',$multiplereference=false);
	if(count($data)>0){
		$totals['03'] == 1 ?$totals['03']=0:"";
		for($i=0;$i<count($data);$i++){
			#echo "<tr>";
			#echo "<td>".$data[$i]['CourseName']."</td>";
			$max = 1; $tot = 0;
			for($term=0;$term<=4;$term++){
				if($term == 0){
					//Load the maximum set of found course
					for($c=1;$c<3;$c++){
						@$totals[$term.$c] += $data[$i]['Maximum'];
						#echo "<td align=right>".$data[$i]['Maximum']."</td>";
					}
					@$totals[$term.'3'] += $data[$i]['Maximum'] + $data[$i]['Maximum'];
					#echo "<td align=right>".($data[$i]['Maximum'] + $data[$i]['Maximum'])."</td>";
					$max = (($data[$i]['Maximum'] + $data[$i]['Maximum'])*3);
				} elseif($term == 4){
					$percent = round(($tot*100)/$max,1);
					if(!$status['expeled'] && $percent<50){
						++$status['echec'];
						if($status['echec']<=$dl['sitting']){
							//2nd sitting section;
							$status['sitting'][] = $data[$i]['CourseName'];
							$status['promoted'] = false;
						} elseif($status['echec'] < $dl['expeled'] && $status['echec'] >= $dl['repeat']){
							//repeat section
							$status['repeat'] = true;
							$status['sitting'] = null;
							$status['promoted'] = false;
						} else{
							//expeled during 1 first sitting
							$status['expeled'] = true;
							$status['repeat'] = false;
							$status['promoted'] = false;
							$status['sitting'] = null;
						}
					}
					@$totals[$term.'1'] += $tot;
					@$totals[$term.'2'] += $max;
					@$totals[$term.'3'] += $percent;
					#echo "<td align=right id=".($tot<($max/2)?"echec":"").">".$tot."</td>";
					#echo "<td align=right>".$max."</td>";
					#echo "<td align=right id=".($percent<50?"echec":"").">".$percent."</td>";
				} else{
					$stot = 0;
					for($h=1;$h<=2;$h++){
						$sfields[$term][] = $data[$i]['Acronym'].$term.$h;
						$mark = $db->select1cell("tbl_marks",$data[$i]['Acronym'].$term.$h,array("StudentID"=>$studentid),true);
						$stot += $mark; # $max += $data[$i]['Maximum'];
						@$totals[$term.$h] += $mark;
						#echo "<td align=right id=".($mark<($data[$i]['Maximum']/2)?"echec":"").">".$mark."</td>";
					}
					#echo "<td align=right id=".($stot<($data[$i]['Maximum'])?"echec":"").">".$stot."</td>";
					$tot += $stot;
					@$totals[$term."3"] += $stot;
				}
			}
			//sitting section
			#echo "<td></td>";
			#echo "</tr>";
		}
	}
}
function LoadStudentPlace($db=null,$fields=null,$studentid=0,$term=1,$dept=3,$class=4,$error=0){
	#var_dump($error);
	if($error != 0) return 0;
	$place = 0; $annualplace=0;
	#echo $term."<br>";var_dump($db);
	//select all valid ids for a given class
	$ids = $db->selectFields($tbl='tbl_student',$field=array("StudentID"),$condition=array("DeptID"=>$dept,"Class"=>$class));
	if(count($ids)<1) return "!ERROR";
	#var_dump($ids); echo "<br><br>";
	//make the query that will be used to select marks
	$query = "";
	for($i=1;$i<count($ids);$i++){
		if($i != 0) $query .= " || ";
		$query .= "`StudentID`=\"".$ids[$i]['StudentID'];
		if($i != count($ids)-1) $query .= "\"";
	}
	#echo $query."<br>";
	//execute the marks selection query
	$fields[$term][] = 'StudentID';
	$allmarks = $db->selectFields($tbl='tbl_marks',$field=$fields[$term],$condition=array("studentID"=>$ids[0]['StudentID']."\" ".$query));
	#echo "<pre>";var_dump($allmarks);echo "</pre>";
	#$totals = array();
	#$id;
	for($i=0;$i<count($allmarks);$i++){
		#$id[] = $allmarks[$i]['StudentID'];
		if(!in_array(null,$allmarks[$i])) $totals[$allmarks[$i]['StudentID']] = (array_sum($allmarks[$i]) - (int)$allmarks[$i]['StudentID']);
	}
	arsort($totals);
	#echo "<pre>";var_dump($totals);var_dump($studentid); echo "</pre>";
	$p=1;
	foreach($totals as $key=>$value){
		if($key == $studentid) $place = $p;
		++$p;
	}
	if($term == 4){
		$annual; $totals; $place = 0;
		for($t=1;$t<=3;$t++){
			$fields[$t][] = 'StudentID';
			$allmarks = $db->selectFields($tbl='tbl_marks',$field=$fields[$t],$condition=@array("studentID"=>$ids[0]['ID']."\" ".$query));
			#echo "<pre>";var_dump($allmarks);echo "</pre>"; break;
			//separate marks depeding on studentid
			for($i=0;$i<count($allmarks);$i++){
				$annual[$allmarks[$i]['StudentID']][] = $allmarks[$i];
				#$id[] = $allmarks[$i]['StudentID'];
				#if(!in_array(null,$allmarks[$i])) $totals[$allmarks[$i]['StudentID']] = (array_sum($allmarks[$i]) - (int)$allmarks[$i]['StudentID']);
			}
			#echo "<pre>";var_dump($annual);echo "</pre>";
		}
		#echo "<pre>";var_dump($annual);echo "</pre>";
		//after data maping try to make sum
		foreach($annual as $id=>$mrk){
			#echo "<pre>".$id."=>";var_dump($mrk);echo "</pre>";
			#$totals[$id] += (array_sum($mrk));// - (int)$id);
			$unclasified = false;
			for($i=0;$i<count($mrk);$i++){
				if(!in_array(null,$mrk[$i]) && !$unclasified){
					$totals[$id] += (array_sum($mrk[$i]) - (int)$id);
				} else{
					unset($totals[$id]);
					$unclasified = true;
				}
			}
		}
		#echo "<pre>";var_dump($totals);echo "</pre>";
		arsort($totals);
		#echo "<pre>";var_dump($totals); echo "</pre>";
		$p=1;
		foreach($totals as $key=>$value){
			if($key == $studentid) $place = $p;
			++$p;
		}
	}
	return $place;
}
function plotHeaderMenuInfo($activemenuName,$user=1,$db=null) {
    $menuNamesArr = array( "Users" => "index.php", "Department" => "dept.php", "Students" => "student.php", "Courses"=>"course.php", "Marks" => "marks.php", "Report" => "report.php","Academic Year"=>"year.php");
    if($user == 2) $menuNamesArr = array( "Student Marks" => "index.php", "2<sup>nd</sup> Sitting" => "sitting.php", "Student Report" => "viewall.php");
    if($user == 4) $menuNamesArr = array( "Discpline Marks" => "index.php", "Student Report" => "viewall.php");
    if($user == 3) $menuNamesArr = array( "Student" => "index.php","Comment"=>"inde.php");
    if($user == 5) $menuNamesArr = array( "Aply For Leave" => "index.php");
	echo "<div class='header'>
			<div class='header_text'>
			  <p align='right'>Welcome 
				<strong>". $db->select1cell("school_report_db`.`tbl_users","Name",array("ID"=>$_SESSION['u_id']),true) ."</strong><br/>
				<a href='change_password.php'>Change Password</a> | <a href='../../logout.php'>Logout</a>
			  </p>
			</div>";
    echo '<div class="menu">';
    echo '<ul>';
    foreach ($menuNamesArr as $menuName => $menuUrl) {
        if ($menuUrl == $activemenuName) {
            echo "<li><a href = '{$menuUrl}' class='active'>{$menuName}</a></li>";
        } else {
            echo "<li><a href = '{$menuUrl}'>{$menuName}</a></li>";
        }
    }
    echo '</ul>';
    echo '</div><!--End of menu div-->';
    echo '</div><!--End of header div-->';
}

function plotHeaderStudentInfo($activemenuName) {
    $menuNamesArr = array(
        "Feedback" => "feedback.php",
        "MyInfo" => "#",
    );
    echo "<div class='header'>
			<div class='header_text'>
			  <p align='right'>Welcome
				<strong>{$_SESSION['u_utype']}</strong><br/>
				<a href='change_password.php'>Change Password</a> | <a href='logout.php'>Logout</a>
			  </p>
			</div>";
    echo '<div class="menu">';
    echo '<ul>';
    foreach ($menuNamesArr as $menuName => $menuUrl) {
        if ($menuUrl == $activemenuName) {
            echo "<li><a href = '{$menuUrl}' class='active'>{$menuName}</a></li>";
        } else {
            echo "<li><a href = '{$menuUrl}'>{$menuName}</a></li>";
        }
    }
    echo "</ul></div>";
}

/* This function will return the logo div string to the sidebody */

function plotLogoDiv($imgPath = "./admin/images/lgo.png") {
    $logoImageDivStr = <<<ABC
	<div class="logo shadowEffect">
		<img src='./admin/images/lgo.png'  /> 
	</div><!--end of Logo div-->
ABC;
    return $logoImageDivStr;
}

/* This function will check the record already there inthe table(beore inserting new rec) */

function recordAlreadyExist($selectQuery) {
	#echo $selectQuery;
    $selectResource = mysql_query($selectQuery)or die(mysql_error());
    $selNumRows = mysql_num_rows($selectResource);
	#var_dump($selNumRows);
    if ($selNumRows == 1) {
        return true;
    } else {
        return false;
    }
}

/* General Function to Insert Record into the table */

function insertOrUpdateRecord($query, $redirectFileName,$db=null, $id = '',$h=true) {
	#echo $query;
    $resResource = mysql_query($query);
    $affectedRows = mysql_affected_rows();
    if ($affectedRows !== -1) {
		#var_dump($db['con']);die;
		if($db != null)$db['con']->AddColomn($table=$db['tbl'],$data=$db['data']);
		if($h){
			if ($id == '') {
				header("Location:{$redirectFileName}?msg=success");
				exit;
			} else {
				header("Location:{$redirectFileName}?id={$id}&msg=success");
				exit;
			}
		}
    } else {
        return false;
    }
}

function DetectDays($endday){
	$c = date("Y",time());
	$endday = preg_replace("#/#","-",$endday);
	$pattern = "/^[".$c[0].",".($c[0]+1)."]{1}[0-9]{3}-[0-9]{2}-[0-9]{2}$/";
	#echo $endday ."===".$pattern."<br>";
	if(preg_match($pattern,$endday)){
		$year = $endday[0].$endday[1].$endday[2].$endday[3];
		$month = $endday[5].$endday[6];
		$days = $endday[8].$endday[9];
		$remain_days = 0;
		if($days - date('d',time())>0){ //check for days
			$remain_days += $days - date('d',time()); //add found days
			if($month - date('m',time()) >0){ //check the month
				$remain_days += ($month - date('m',time()))*30; //add found days
				if($year - date("Y",time())>0){
					$remain_days += ($year - date('Y',time()))*365; //add found days
				}
			} elseif($year - date("Y",time())>0){
				$remain_days += (($year - date('Y',time()))*365)+(((12-date('m',time()))+date('m',time()))*30); //add found days
			}
		} elseif($month - date('m',time()) >0){ //check the month
				$remain_days += (($month - date('m',time()))*30)-(date('d',time())-$days); //add found days
				if($year - date("Y",time())>0){
					$remain_days += ($year - date('Y',time()))*365; //add found days
				}
		} elseif($year - date("Y",time())>0){
			$remain_days += ($year - date('Y',time()))*365; //add found days
			//to be edited
		}
		#var_dump($remain_days);
		return $remain_days;
	}
	return false;
}

/* This function is used to list the department names */

function listDepartment() {
    $listDeptQuery = "SELECT * FROM `tbl_department` ORDER BY department_id DESC LIMIT 0,3";
    $listDeptRes = mysql_query($listDeptQuery) OR die(mysql_error());
    $listDeptNumRows = mysql_num_rows($listDeptRes);
    $listDeptStr = "<div class='side_body shadowEffect'>
							 <h2>Latest Entries...</h2>";
    if ($listDeptNumRows) {
        while ($listDeptArr = mysql_fetch_assoc($listDeptRes)) {
            $listDeptStr .= <<<ABC
				<div class='title'>
					<a href = "departments_edit.php?id={$listDeptArr['department_id']}">
					{$listDeptArr['department_name']}
					</a>
				</div>
				<div class='clr' style='border-bottom:1px solid #CCCCCC;margin-bottom:5px;'>
				</div>
				<br/>
ABC;
        }
    } else {
        $listDeptStr .= "<p><span class= 'error'>No Department(s)</span></p>";
    }
    $listDeptStr .= '</div><!--End Of side body Div-->';
    return $listDeptStr;
}
function users($cat){
	$users = array(1=>'Admin','HOD','HR','Finance','Other');
	if($cat>0 && $cat<=5)return $users[$cat];
	else return "Unknown";
}

/* This function is used to list the Teachers */


function firstOnThePage() {

    return $_SESSION['hassavedsomething'] == "yes" ? false : true;
}

function plotFooterDiv() {
    $footerDivStr = <<<ABC
	<br/><!--This will give space b/w main div and footer if the latest entry block is empty-->
	<div class="footer">
	&reg; All right reserved Ecole Technique Saint Kizito Save<br />
		Copyright &copy MUTSINZI 2019. <br />

		<!--<a href="#">Home</a> | <a href="#">Contact</a>-->
	</div><!--End of Footer div-->
	<div class="clr"></div>
ABC;
    return $footerDivStr;
}

/* This function will plot the department dropdown */

function plotDepartmentDropdown($deptSelVal = '', $ajaxEnabled='no', $subjectAjaxEnabled='no') {
    $deptDropdownStr = '';
    $deptQry = "SELECT * FROM `tbl_department` ORDER BY `department_name`";
    $deptRes = mysql_query($deptQry) or die(mysql_error());
    $deptNumRows = mysql_num_rows($deptRes);
    if ($deptNumRows) {
        if ($subjectAjaxEnabled == 'yes') {
            $deptDropdownStr = "<select name='department' id='department' class='typeproforms'
								onchange='javascript:plotSubjectByDept(\"\")'>";
        } elseif ($ajaxEnabled == 'yes') {
            $deptDropdownStr = "<select name='department' id='department' class='typeproforms'
								onchange='javascript:plotTeacherByDept(this.value)'>";
        } else {
            $deptDropdownStr = "<select name='department' id='department' class='typeproforms'>";
        }
        $deptDropdownStr .= "<option value=''>--select--</option>";
        while ($deptArr = mysql_fetch_assoc($deptRes)) {

            if ($deptArr['department_id'] == $deptSelVal) {
                $deptDropdownStr .= "<option value={$deptArr['department_id']} selected='selected'>{$deptArr['department_name']}</option>";
            } else {
                $deptDropdownStr .= "<option value={$deptArr['department_id']}>{$deptArr['department_name']}</option>";
            }
        }
        $deptDropdownStr .='</select>';
    } else {
        $deptDropdownStr = "<select name='department' id='department' class='typeproforms'>
								<option value = ''>--select--</option>
								</select>";
    }
    return $deptDropdownStr;
}
function checkLeaveAvailability($user_ID){
	/* check the user in leaveCounter table */
	start_process:
	$sql = "SELECT * FROM tbl_leave_counter WHERE UserID='".$user_ID."'";
	$query = mysql_query($sql);
	if($query && mysql_num_rows($query) == 1){
		/* get remaining days */
		#echo "data found";
		$data = mysql_fetch_assoc($query);
		$rtnStr =  $data['RemainingDays']." Days Remain
			<a href='' onClick=\"return DetailsData('".$data['DoneLeaves']."')\">Click To View Leave Passed</a>";
		return $rtnStr;
	} else{
		/* try to insert new records for the user */
		mysql_query("INSERT INTO `tbl_leave_counter` SET `ID`=NULL,`UserID`='".$user_ID."',`RemainingDays`='30', `DoneLeaves`='', `UpdateDate`=NOW()");
		goto start_process;
	}
}

function checkSession() {
    if (!isset($_SESSION['u_id'])) {
        header('Location:../../index.php?msg=sesexpired');
        exit;
    } elseif (!(isset($_SESSION['uname']) && isset($_SESSION['utype']))) {
        header('Location:../../index.php?msg=invalid');
        exit;
    }
}

/* This function will check the dependency with the tables before delete the record */


function plotLogoWithAddress() {
    $logoStr = <<<ABC
				<div style='margin:0 auto;width:500px;height:150px;'>
				<table border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td>
							<img src="../admin/images/logo.png"  style="float:none"  />
						</td>
						<td>
						<table>
							<tr><td>Ecole Technique Saint KIZITO save,</td></tr>
							<tr><td>P.O. BOX:6638,Rulindo,</td></tr>
							<tr><td>Tel:(250)7845015114 /5/6,</td></tr>
							<tr><td>Northern Province,</td></tr>
							<tr><td>Website:www.ets.ac.rw</td></tr>
							</tr>
						</table>
					</tr>
				</table>
					<div style = 'color:#fff;font-weight:bold;font-size:1.3em;border:1px;background-color:#096bad;width:600px;'>
						<center>WELCOME TO Saint KIZITO COURSE EVALUATION</center>
					</div>
					<br>
					<div style = 'font-weight:bold;'>
						Thank you for taking your time to complete this evaluation form. Your ratings and comments will be very helpful to instructors, lecturers and the college in general
					</div>
				</div>
ABC;
    return $logoStr;
}
function plotSearchDiv($searchActionFile) {
    $searchVal = (isset($_GET['keyword'])) ? $_GET['keyword'] : 'Search';
    if (isset($_GET['keyword']) && $_GET['keyword'] == '') {
        $searchVal = 'Search';
        ;
    }
    $searchDiv = <<<ABC
				<div class="search" id='searchEffect'>
					<form action="{$searchActionFile}" method="get" name="search" onsubmit="if (document.search.keyword.value == 'Search'){ document.search.keyword.value = '';}"">
					<input name="keyword" type="text"  class="keywords" value="$searchVal" 
						onfocus="if (document.search.keyword.value == 'Search'){ document.search.keyword.value = '';}"
						onblur="if (document.search.keyword.value == ''){ document.search.keyword.value = 'Search';}" />
					<input name="Search" type="image" src="../../admin/images/search.gif" value="Search"  />
					</form>
				</div><!-- End of Search Div-->
ABC;
    return $searchDiv;
}


function plotLogoWithAddress1() {
    $logoStr = <<<ABC
				<div style='margin:0 auto;width:500px;height:150px;'>
				<table border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td>
							<img src="../admin/images/logo.png"  style="float:none"  />
						</td>
						<td>
						<table>
							<tr><td>Ecole technique saint KIZITO save,</td></tr>
							<tr><td>P.O. BOX:6638,Rulindo,</td></tr>
							<tr><td>Tel:(250)7845015114 /5/6,</td></tr>
							<tr><td>Southern Province,</td></tr>
							<tr><td>Website:www.ets.ac.rw</td></tr>
							</tr>
						</table>
					</tr>
				</table>
					
				</div>
ABC;
    return $logoStr;
}


function plotTextArea($name = 'comments', $rows='5', $cols='8') {
    return "<textarea class='typeproforms' name = {$name} id = 'qualities' rows = '{$rows}' cols='{$cols}'></textarea>";
}

function setJsonStrForChart($mapArr) {
    if (is_array($mapArr)) {
        $jsonStrForMap = '[';
        foreach ($mapArr as $mapFiled => $mapValue) {
            $jsonStrForMap .= "['{$mapFiled}',{$mapValue}],";
        }
        /* To Strip Last Comma From String */
        $jsonStrForMap = substr($jsonStrForMap, 0, -1);
        $jsonStrForMap .= ']';
        return $jsonStrForMap;
    }
}

function plotChart($jsonTitle, $jsonContent, $whichChart='BarChart', $divId) {
    $chartStr = <<<ABC
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">	
			 // Load the Visualization API and the piechart package.
			google.load('visualization', '1.0', {'packages':['corechart']});
			// Set a callback to run when the Google Visualization API is loaded.
			google.setOnLoadCallback(drawChart);
			
			 // Callback that creates and populates a data table, 
			// instantiates the pie chart, passes in the data and
			// draws it.
		  function drawChart() 
		  {
			  // Create the data table.
			  var data = new google.visualization.DataTable();
			  data.addColumn('string', 'Topping');
			  data.addColumn('number', 'Feedback');
			  data.addRows($jsonContent);

			  // Set chart options
			  var options = $jsonTitle;

			  // Instantiate and draw our chart, passing in some options.
			  var chart = new google.visualization.{$whichChart}(document.getElementById('chart_div{$divId}'));
			  chart.draw(data, options);
			}
		</script>
ABC;
    echo $chartStr;
}
/* Charts Functions Ends Here */
?>