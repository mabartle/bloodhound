<?php
/*COMMENTS
FileName= bh_argus-distdstipport_capanddatespecific.php 
QueryDescription= Use this query to see the Communication Report - Count, saddr, daddr, proto, sport, dport for a specific CapID, Date, and Time
ENDCOMMENTS*/
include ("bhvars.php");
function query_db($beg_date,$end_date,$beg_time,$end_time,$capid){
require_once ("bhweb.php");
require_once ('DB.php');
$connection = DB::connect("mysql://$db_username:$db_password@$db_host/$db_database");
if (DB::isError($connection)){
die ("Could not connect to DB: <br ?>". DB::errorMesage($connection));
}
//The query includes the form submission values that were passed to the function
$query ="select saddr, COUNT(DISTINCT(daddr)) as DCDSTIP, COUNT(DISTINCT(dport)) as DCDSTPORT from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (stime >= '$beg_time')) AND ((sdate <= '$end_date') AND (stime <= '$end_time')) GROUP BY saddr ORDER BY DCDSTIP DESC, DCDSTPORT DESC";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo ('<table border="1">');
echo "<tr><th>SrcIP</th><th>DC_DSTIP</th><th>DC_DSTPORT</th><th>LINKS</th><th>GRAPHS</tr>";
while ($result_row = $result->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo $result_row[2] .'</td><td>';
echo '<a href="bh_argus-commreport-saddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["0"].'"title="Show the Communication Report for this SrcIP." target="_blank">CR_SRCIP</a>&nbsp';
echo '<a href="bh_argus-commreport-saddrordaddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["0"].'"title="Show the Communication Report for this IP." target="_blank">CR_ALLFORSRCIP</a>&nbsp';
echo '<a href="bh_blanketquery.php?anything='.$result_row["0"].'"title="Blanket Query" target="_blank">BC_FORIP</a></td><td>';
echo '<a href="argus-jpgraph-srcdstip-Last7Day-Bar.php?srcip='.$result_row["1"].'&dstip='.$result_row["2"].'&beg_date='.$beg_date.'"title="Last 7 Days, Bar Graph For SRCDSTIP" target="_blank">7DaySRCDSTBAR</a></td></tr>';
}
echo ("</table>");
$connection->disconnect();
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="bh-style.css" />
<LINK REL="shortcut icon" HREF="argus_logo.gif" TYPE="image/x-icon">
<title>Bloodhound - SrcIP --> DC_DSTIP, DC_DSTPORT Report</title>
</head>
<body>

<?php
$beg_date = $_GET['beg_date'];
$end_date = $_GET['end_date'];
$beg_time = $_GET['beg_time'];
$end_time = $_GET['end_time'];
$capid = $_GET['capid'];
$self = $_SERVER['PHP_SELF'];
if ($beg_date != NULL ){
echo "<h3><u>SrcIP --> DC_DSTIP, DC_DSTPORT Report Query - Results </u></h3>";
echo "<b>Query Description:</b> This query will show a breakdown by Src IP and 'how' many DstIP's and DstPort it is communicating 'on/with'.<br /><br />";
echo "
<strong>Date/Time Search Criteria:</strong><br />
<li><strong>Start Date/Time =<u>$beg_date , $beg_time</u></strong></li>
<li><strong>End Date/Time =<u>$end_date , $end_time</u></strong></li><br />
<strong>CAPID Search Criteria:</strong><br />
<li><strong>Capid = <u>$capid</u></strong></li><br />
" ;
query_db($beg_date,$end_date,$beg_time,$end_time,$capid);
}
else {
 /*Passing today's date into form*/
echo "<h2>SrcIP --> DC_DSTIP, DC_DSTPORT Report</h2>";
echo "<b>Query Description:</b>  This query will show a breakdown by Src IP and 'how' many DstIP's and DstPort it is communicating 'on/with'.<<br /><br />";
echo ('
<form action="'.$self.'"method="get">
<label>Start Date to Search:
<input type="text" name="beg_date" maxlength="10" size="10" id="beg_date" value="'.$sdate.'"/>
</label><br />
<label>End Date to Search:
<input type="text" name="end_date" maxlength="10" size="10" id="end_date" value="'.$sdate.'"/>
</label><br />
<label>Start Time to Search:
<input type="text" name="beg_time" maxlength="10" size="10" id="beg_time" value="'.$stime.'"/>
</label><br />
<label>End Time to Search:
<input type="text" name="end_time" maxlength="10" size="10" id="end_time" value="'.$stime.'"/>
</label><br />
<label>CAPID to Search:
<input type="text" name="capid" maxlength="15" size="15" id="capid" value="%"/>
</label><br />
<input type="submit" value="Run Query" />
</form>
');
}

?>
</body>
</html>
