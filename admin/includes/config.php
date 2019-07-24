<?php

//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

/* Config file for specifying username and password of the database */
define("HOSTNAME", "localhost");
define("USERNAME", "root");
define("PASSWORD", "");
define("DBNAME", "school_report_db");

/* Connect with the  mysql database server */
$link = mysql_connect(HOSTNAME, USERNAME, PASSWORD) OR die(mysql_error());
/* Select the database from the mysql server */
mysql_select_db(DBNAME, $link) OR die(mysql_error());
/* Set the default timezone as Africa/Kigali */

/* Set Year Values -> By default we have first,second years */
$yearArr = array(1, 2);
/* Set Semester Values -> By default we have First,Second */
$semesterArr = array(1, 2);
/* Set Semester Values -> By default we have First,Second */
$typeArr = array("Module/course Content And organisation ratings", "Student contribution and ratings", "Learning environment and teaching", "Overall Experience", "Teachers ratings");
/* Set Sponsor Values -> By default we have First,Second */
$spoArr = array("Government", "Private");
/* Set Gender Values -> By default we have First,Second */
$genderArr = array("Male", "Female");
/* Set Desability Values -> By default we have First,Second */
$desabArr = array("Yes", "No");

date_default_timezone_set('Africa/Kigali');
#echo 'Success';
?>