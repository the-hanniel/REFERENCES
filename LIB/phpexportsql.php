<?php
/***
*@ class to export mysql database to .sql file
*
*
*
**/
class ExportSqlDB{
	private $con;
	
	private $exportType[];
	
	function __construct($connection = null){
		/* those database are by default for your choice edit them!!! */
		$this->con = $connection?$connection:mysql_connect("127.0.0.1","root","");
	}
	
	function setExportType($export=null){
		if(!$export) return null;
		$this->exportType[] = mysql_real_escape_string(trim($export));
	}
	
	private function ExportDB($db){
	
	}
}
?>