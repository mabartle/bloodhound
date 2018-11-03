<?php
/*COMMENTS
FileName= bh_blacklistquery_capanddatespecific.php
QueryDescription= Use this query to see all BLACKLIST "TYPE" events
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
$query ="select capid, stime, sdate, saddr, daddr, proto, http_host, http_request_uri, dns_request, dns_response, log_type, alert_type from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND ((alert_type = 'BLACKLIST') OR (alert_type = 'ZEUSLIST') OR (alert_type = 'DOMAINLIST') OR (alert_type = 'CONFICKLIST') OR (alert_type = 'SUSPECTLIST'))";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo ('<table border="1">');
echo "<tr><th>CapID</th><th>Start_Time</th><th>Start_Date</th><th>SrcIP</th><th>Dst IP</th><th>Proto</th><th>HTTP_HOST</th><th>HTTP_REQUEST_URI</th><th>DNS_REQUEST</th><th>DNS_RESPONSE</th><th>Log_Type</th><th>Alert_Type</th><th>LINKS</th><th>GRAPHS</tr>";
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
echo $result_row[8] .'</td><td>';
echo $result_row[9] .'</td><td>';
echo $result_row[10] .'</td><td>';
echo $result_row[11] .'</td><td>';
echo 'SrcIP Communication Reports: ';
echo '<a href="bh_argus-commreport-saddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["3"].'"title="Show the Communication Report for this SrcIP." target="_blank">CR_SRCIP</a>&nbsp';
echo '<a href="bh_argus-commreport-saddrordaddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["3"].'"title="Show the Communication Report for this IP." target="_blank">CR_ALLFORSRCIP</a><br />';
echo 'DstIP Communication Reports: ';
echo '<a href="bh_argus-commreport-daddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&daddr='.$result_row["4"].'"title="Show the Communication Report for this DstIP." target="_blank">CR_DSTIP</a>&nbsp';
echo '<a href="bh_argus-commreport-saddrordaddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["4"].'"title="Show the Communication Report for this IP." target="_blank">CR_ALLFORDSTIP</a><br />';
echo 'All Information Reports:<br /> ';
echo '<a href="bh_ipstatsummary.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&ip='.$result_row["3"].'"title="Show the Stat Summary." target="_blank">SrcIP_Stats</a>&nbsp&nbsp';
echo '<a href="bh_ipstatsummary.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&ip='.$result_row["4"].'"title="Show the Stat Summary." target="_blank">DstIP_Stats</a><br>';
echo '<a href="bh_argus-saddrfullreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["3"].'"title="Show the Communication Report for this IP." target="_blank">ALLFORSRCIP</a>&nbsp';
echo '<a href="bh_argus-daddrfullreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&daddr='.$result_row["4"].'"title="Show the Communication Report for this IP." target="_blank">ALLFORDSTIP</a><br />';
echo 'Who IS Links: ';
echo '<a href="http://whois.domaintools.com/'.$result_row["3"].'"title="WhoIs For ScrIP" target="_blank">WHOSRCIP</a>';
echo '<a href="http://whois.domaintools.com/'.$result_row["4"].'"title="WhoIs For DstIP" target="_blank">WHODSTIP</a>';
echo 'All DNS/URL Reports: ';
echo '<a href="bh_argusurldns-saddrdaddr_httphost_dns-request-capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&requestorhost='.$result_row["6"].'"title="Show me all the argus, dns, and url data for this HTTP HOST." target="_blank">HTTPHOST</a>&nbsp';
echo '<a href="http://www.google.com/search?q='.$result_row["6"].'"title="Google Search for URL." target="_blank">Google - HTTP_HOST</a><br />';
echo '<a href="bh_argusurldns-saddrdaddr_httphost_dns-request-capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&requestorhost='.$result_row["8"].'"title="Show me all the argus, dns, and url data for this HTTP HOST." target="_blank">DNSNAME</a>&nbsp';
echo '<a href="http://www.google.com/search?q='.$result_row["8"].'"title="Google Search for URL." target="_blank">Google - DNS NAME</a><br />';
echo 'Snort Reports: ';
echo '<a href="bh_snort-ipdetailreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&addr='.$result_row["3"].'"title="Show me the IP Snort Detail Report for this Source IP." target="_blank">SnortSrcIPDR</a>&nbsp';
echo '<a href="bh_snort-ipdetailreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&addr='.$result_row["4"].'"title="Show me the IP Snort Detail Report for this Destination IP." target="_blank">SnortDstIPDR</a></td></tr>';
}
echo ("</table>");
$connection->disconnect();
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="bh-style.css" />
<LINK REL="shortcut icon" HREF="argus_logo.gif" TYPE="image/x-icon">
<title>Bloodhound - Interesting Traffic Report</title>
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
echo "<h3><u>Interesting Traffic Query - Results </u></h3>";
echo "<b>Query Description:</b> This query will all Interesting Events which were flagged by Bloodhound.<br /><br />";
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
echo "<h2>Interesting Traffic Report</h2>";
echo "<b>Query Description:</b> This query will all Interesting Events which were flagged by Bloodhound.<br /><br />";
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
