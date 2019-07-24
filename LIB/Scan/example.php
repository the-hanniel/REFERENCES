<?php
/*	Example of use of the Scandir class    */



// include the class ScanDir
include_once "Class_ScanDir.php";


// the path must be an absolute path without the end slash
// ei : $Path="/var/www/test";
$Path = $_SERVER['DOCUMENT_ROOT']."/Pati";


// instantiate the class.
$Dir = new DirScan () ;


// if needed, set the filter of extension and activate it
// 1 / first define the filter : it will be an array of all extension you want,
// for example here : .php, .jpg, .gif
// in the array string, you only need to put the extension name without dot '.'
$Dir->SetFilterExt(array("php","jpg","gif")) ;
// 2 / just activate it or not. here, the filter is not activate by default.
// you just need to change 'false' by 'true' to enable the filter mode.
$Dir->SetFilterEnable(1);

// enable the listing off all the extension of files found during scanning
// by specifying true, the scan will keep in an array, all the unique extension
// found during process. this array can be different of the filter array if
// enable, because all the filter extension will not be present in the path found.
$Dir->SetFileExtListEnable(true);

// enable sub directories scan
// if "true", the scan process all the subdirectory
// if "false" the scan on scan files in the specified path
$Dir->SetScanSubDirs(true);


// enable Files Scanning
// if "true", the scan process the files
// if "false" the scan do not check files,
$Dir->SetScanFiles(true);

// enable full details
// if "false", the only information are filename and size
// if "true", the information are filename, size, dates, perms, type, basename.
$Dir->SetFullDetails(true);

// run the Directory scanning
// each new scan will flush the TabFiles properties, to have only the result of the scan


// run the scan
$Dir->ScanDir($Path,false);

// display some result
echo "<br>Total byte : " .$Dir->FileSize;
echo "<br>Nb Files  : " .$Dir->FileCount;
echo "<br>Nb Dirs  : " .$Dir->DirCount;
echo "<br>List of extension : <br>";

// to see the contains of the list of the extensions files found
print_r($Dir->FileExtList);
echo "<br>";
// display all the file found during scanning
foreach ($Dir->TabFiles as $f) {
		echo $f["filename"].chr(0xA)."<br>";
               // print_r($f);
}

?>