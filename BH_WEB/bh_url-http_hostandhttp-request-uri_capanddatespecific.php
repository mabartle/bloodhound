<?php
/*COMMENTS
FileName= bh_url-http_hostandhttp-request-uri_capanddatespecific.php
QueryDescription= Use this query to see all unique http_host, http-request-uri url data for a specific CapID, Date, and Time
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
$query ="select count(*) as count, http_host, http_request_uri from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'URLLOG') GROUP BY saddr, http_host, http_request_uri ORDER BY count DESC";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo ('<table border="1">');
echo "<tr><th>Count</th><th>HTTP_HOST</th><th>HTTP_REQUEST_URI</th><th>LINKS</th><th>GRAPHS</tr>";
while ($result_row = $result->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo $result_row[2] .'</td><td>';
echo '<a href="/HONEYWEB/honey-ar-sip.php?sip='.$result_row["1"].'&beg_date='.$beg_date.'"title="Show ALL records for this SrcIP from HONEYPOT LOGS" target="_blank">HSIPAR</a></td><td>';
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
<title>Bloodhound - URL Report</title>
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
echo "<h3><u>SrcIP --> URL - Results </u></h3>";
echo "<b>Query Description:</b> This query will show a count of all URLS (HTTP_HOST and HTTP_URI from the URL data used during this capture..<br /><br />";
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
echo "<h2>URL Report</h2>";
echo "<b>Query Description:</b> This query will show a count of all URLS (HTTP_HOST and HTTP_URI from the URL data used during this capture.<br /><br />";
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
