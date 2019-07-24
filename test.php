<?php

include 'CodeGenerator.php';

$ps = new CodeGenerator();

$it = 3;
$et = 2;
$ae = 1;
$sem2 = 2;
$year1 = 1;
$year2 = 2;

/* generating codes for AE department */
$codes[] =  $ps->generateCode($ae,$year2,$sem2,85);
$codes[] =  $ps->generateCode($ae,$year1,$sem2,99);

/* generating codes for ET department */
$codes[] =  $ps->generateCode($et,$year2,$sem2,98);
$codes[] =  $ps->generateCode($et,$year1,$sem2,106);

/* generating codes for IT department */
$codes[] =  $ps->generateCode($it,$year2,$sem2,78);
//$codes[] =  $ps->generateCode($it,$year1,$sem2,101);


$i = 1;
mysql_connect("localhost", "root", "");
mysql_select_db('course_evaluation');
while(list ($x, $y) = each ($codes)){
    foreach ($y as $a)
        print "$a<br />";
  //  mysql_query ("INSERT INTO tbl_codes SET code = '$a'");
}
?>
