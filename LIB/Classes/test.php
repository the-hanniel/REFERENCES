<?php
//  Include PHPExcel_IOFactory
include 'PHPExcel/IOFactory.php';

$inputFileName = './example1.xlsx';

//  Read your Excel workbook
try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName);
} catch(Exception $e) {
    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}
#echo "<pre>";var_dump($objPHPExcel);
//  Get worksheet dimensions
for($id=0;$id < $objPHPExcel->getSheetCount();$id++){

	$sheet = $objPHPExcel->getSheet($id);
	#var_dump($sheet);
	#var_dump($objPHPExcel->__numberOfSheets());
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();

	//  Loop through each row of the worksheet in turn
	for ($row = 1; $row <= $highestRow; $row++){ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
										NULL,
										TRUE,
										FALSE);
		echo "<pre>";var_dump($rowData); echo "<hr>";
		//  Insert row data array into your database of choice here
	}
	echo "<hr><hr>";
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"
        type="text/javascript"></script>
        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"
        type="text/javascript"></script>
        <script type="text/javascript">