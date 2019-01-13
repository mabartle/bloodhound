<?php
/*COMMENTS
FileName= bh_blanketquery.php
QueryDescription= Use this query to see any entries for a certain value (shows Srcip, DstiP, all SrcIP --> DstIP - HTTP_HOSTS url data for a specific CapID, Date, and Time
ENDCOMMENTS*/
include ("bhvars.php");
function query_db($anything){
require_once ("bhweb.php");
require_once ('DB.php');
$connection = DB::connect("mysql://$db_username:$db_password@$db_host/$db_database");
if (DB::isError($connection)){
die ("Could not connect to DB: <br ?>". DB::errorMesage($connection));
}
//The query includes the form submission values that were passed to the function
$query ="select count(*) as count, capid, saddr, daddr, log_type, http_host, dns_request,dns_response from bh_table where (saddr LIKE '%$anything%') OR (daddr LIKE '%$anything%') OR (http_host LIKE '%$anything%') or (dns_request LIKE '%$anything%') OR (dns_response LIKE '%$anything%') GROUP BY capid, saddr, daddr, log_type, http_host, dns_request, dns_response ORDER BY count DESC";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo ('<table border="1">');
echo "<tr><th>Count</th><th>CapID</th><th>SrcIP</th><th>DstIP</th><th>Log Type</th><th>HTTP_HOST</th><th>DNS REQUEST</th><th>DNS RESPONSE</th><th>LINKS</th><th>GRAPHS</tr>";
while ($result_row = $result->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo $result_row[2] .'</td><td>';
echo $result_row[3] .'</td><td>';
echo $result_row[4] .'</td><td>';
echo $result_row[5] .'</td><td>';
echo $result_row[6] .'</td><td>';
echo $result_row[7] .'</td><td>';
echo '<a href="bh_urldns-httphost_dns-request-capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&requestorhost='.$result_row["3"].'"title="Show the DNS and URL requests for this Name for this capture." target="_blank">HTTP_HOST and DNS_REQUEST</a>&nbsp';
echo '<a href="bh_argusurldns-saddrdaddr_httphost_dns-request-capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&requestorhost='.$result_row["2"].'"title="Show me all the argus, dns, and url data for this DstIP." target="_blank">DstIP</a></td><td>';
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
<title>Bloodhound - Blanket Query Report</title>
</head>
<body>

<?php
$anything = $_GET['anything'];
$self = $_SERVER['PHP_SELF'];
if ($anything != NULL ){
echo "<h3><u>Blanket Query - Results </u></h3>";
echo "<b>Query Description:</b> This query will show all data that relates to the search value..<br /><br />";
echo "
<strong>Anything Search Criteria:</strong><br />
<li><strong>What do you want to look for?? = <u>$anything</u></strong></li><br />
" ;
query_db($anything);
}
else {
 /*Passing today's date into form*/
echo "<h2>Blanket Query Report</h2>";
echo "<b>Query Description:</b> This query will show all data that relates to the search value.<br /><br />";
echo ('
<form action="'.$self.'"method="get">
<label>Anything to Search:
<input type="text" name="anything" maxlength="60" size="60" id="anything" value=""/>
</label><br />
<input type="submit" value="Run Query" />
</form>
');
}

?>
</body>
</html>
