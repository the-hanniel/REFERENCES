<?php
session_start();

require_once("../../../admin/includes/functions.php");

checkSession();

require_once"../../../LIB/config.php";
$db = new DBConnector("school_report_db".$_SESSION['year']);
$filename = "studentlist_templete.xlsx";
if(!file_exists($filename)){
	//echo "Create file".$filename;
	
	//select all course attached to the current teacher
	$dept = $db->selectFields($tbl="tbl_dept",$field=array('Acronym'),$condition=array("Acronym"=>array("Administration","GNC")),$limit=null,$order="ORDER BY `Acronym` ASC",$indexed=true,$sign='!=',$multiplereference=true,$distinct=false);
	#var_dump($dept); die;
	
	//require the PHPExcel library to allow writting new excel document
	require_once"../../../LIB/Classes/PHPExcel/IOFactory.php";
	require_once"../../../LIB/Classes/PHPExcel.php";
	
	//instantiate the PHPExcel object
	$objPHPExcel = new PHPExcel;
	
	//instantiate the writer object
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel, "Excel2007");
	
	$index = 0;
	
	//loop in all found courses to make the sheet on created file
	for($i=0;$i<count($dept);$i++){
	
		for($class=4;$class<=6;$class++){
			// Create a first sheet, representing sales data
			$objPHPExcel->setActiveSheetIndex($index++);
			$objPHPExcel->getActiveSheet()->setCellValue('A1', "Student List");
			$objPHPExcel->getActiveSheet()->setCellValue('A2', $dept[$i]['Acronym']);
			$objPHPExcel->getActiveSheet()->setCellValue('B2', $class);
			$objPHPExcel->getActiveSheet()->setCellValue('A3', 'No');
			$objPHPExcel->getActiveSheet()->setCellValue('B3', 'First Name');
			$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Last Name');
			$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Date Of Birth');
			$objPHPExcel->getActiveSheet()->setCellValue('A4', '1');
			$objPHPExcel->getActiveSheet()->setCellValue('B4', 'Karangwa');
			$objPHPExcel->getActiveSheet()->setCellValue('C4', 'Innocent');
			$objPHPExcel->getActiveSheet()->setCellValue('D4', '1993-12-30');

			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('S'.$class.$dept[$i]['Acronym']);

			// Create a new worksheet, after the default sheet
			$objPHPExcel->createSheet();
		}
	}
	
	//write the file with the desired filename
	$objWriter->save($filename);
	
	//open the created file to write some new data
	//$objReader = PHPExcel_IOFactory::createReader("Excel2007");
	//load the created file
	//$objPHPExcel = $objReader->load($filename);


}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>SYSTEM USERS REGISTRATION</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!--Link to Validation JS source File -->
	<script type = 'text/javascript' language='javascript' src = '../../../admin/js/validation.js'></script>
	<!--Link to the template css file-->
	<link rel="stylesheet" type="text/css" href="../../../admin/css/style.css" />
	<!--Link to Favicon -->
	<link rel="icon" href="../../../admin/images/favi_logo.gif"/>
	<!-- Spry Stuff Starts here-->
	<link href="../../../admin/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../../admin/spry/textfieldvalidation/SpryValidationTextField.js"></script>
	<!-- Spry Stuff Ends Here-->
</head>
<body>
<div class="main">
	<div class=header>
	
	</div>
	<?php
		/*This function will return the header string with menu information*/
		//echo plotHeaderMenuInfo(basename(__FILE__),2,$db);
	?>
	<div class="body">
		<div class="main_body">
			<h2>Available Downloads please select one (<?php echo $_SESSION['year'] ?>)</h2>
			<input type=hidden name=error />
			<?php
				/*Display the Messages*/
				if(isset($errorMsg))
				{
					echo "<span class = 'error'>{$errorMsg}</span><br />";	
				}
				elseif(isset($successMsg))
				{
					echo "<span class = 'success'>{$successMsg}</span><br />";	
				}
				
				/////////////scan dir functions are going to be implemented here!!!
				
				//start scan process
				
				// require_the scan directory file
				require_once "../../../LIB/Scan/Class_ScanDir.php";
				
				// set the dir path
				$path = "../dwnlds";
				
				// instantiate the class.
				$Dir = new DirScan () ;
				
				// set filter to return only excel formats in the directory
				$Dir->SetFilterExt(array("xls","xlsx")) ;
				
				// enable filter
				$Dir->SetFilterEnable(true);
				
				// list all file extension disabled
				$Dir->SetFileExtListEnable(false);
				
				// disable scan sub directories
				$Dir->SetScanSubDirs(true);
				
				// enable Files Scanning
				$Dir->SetScanFiles(true);
				
				// enable full details
				$Dir->SetFullDetails(true);
				
				// run the scan
				$Dir->ScanDir($path,false);
				
				// display all the file found during scanning
				if(count($Dir->TabFiles) >0){
					$out = "<table border=1 width=100%><tr><th>Type</th><th>File</th><th>Size</th><th>Created on</th></tr>"; $count=0;
					foreach ($Dir->TabFiles as $f) {
						#if(preg_match("/^".preg_replace("/ /","_",trim($db->select1cell("school_report_db`.`tbl_users","Name",array("ID"=>$_SESSION['u_id']),true)))."/",$f['filename']))
							$out .= "<tr><td>[".$f['extension']."]</td><td><a href='./".$f['filename']."'>".(preg_replace(array("/_/","/.".$f['extension']."/"),array("_",""),$f['basename']))."</a></td><td>".$f['size']." Bytes</td><td align=right>".date('Y-m-d h:i:s',$f['datemodify'])."</td></tr>";
							//echo "<pre>";//.$f["filename"]."<br>";
							//	   print_r($f);
					}
					$out .= "</table>";
					echo $out;
				}
				
				//end scan process
				
			?>
			<br />
			<a href='' onclick='window.close();'>Back</a>
		</div><!-- End of main_body div(main white div)-->	
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
-->	
</script>

</body>

</html>