<?php
/*This file contains the variables for most of the queries.*/
$sdate = date("Y/m/d");
$stime = date("H:i:s",mktime(date("H"),date("i")-10,date("s")));
$edate = date("Y/m/d");
$etime = date ("H:i:s");

//Today's Date
$todaysdate = date("D, F d, Y");


//Finding the last 7 days in the date.
$last7 = mktime(0,0,0,date("m"),date("d")-7,date("Y"));
$disreal7 = date("M d, Y (l)", $last7);


$real7 = date("Y-m-d", $last7);
$real7t = date("Y/m/d", $last7);


//Finding the last 30 days in the date
$last30 = mktime(0,0,0,date("m"),date("d")-30,date("Y"));
$real30 = date("Y/m/d", $last30);

//Date Function for SQL, if you want "regular" date use 
// CURDATE() if you want UTC use UTC_DATE()
$dateset = "UTC_DATE()";
$datetype = "UTC";
?>
