<?php
/*COMMENTS
FileName= bh_argus-saddrreport_capanddatespecific.php
QueryDescription= Use this query to see all Dest Ports used for a specific CapID, Date, and Time
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
$query ="select COUNT(DISTINCT(saddr)), COUNT(DISTINCT(daddr)), COUNT(DISTINCT(sport)), COUNT(DISTINCT(dport)), COUNT(DISTINCT(proto)), SUM(bytes), SUM(pkts) from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'ARGUSLOG')";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo "<h3><u>ARGUS Stat Summary Results </u></h3>";
echo ('<table border="1">');
echo "<tr><th># of SrcIPs</th><th># of DstIPs</th><th># of SrcPorts</th><th># of DstPorts</th><th># of Protocols</th><th>Sum of Bytes</th><th>Sum of Packets</th><th>LINKS</th><th>GRAPHS</th></tr>";
while ($result_row = $result->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo $result_row[2] .'</td><td>';
echo $result_row[3] .'</td><td>';
echo $result_row[4] .'</td><td>';
echo $result_row[5] .'</td><td>';
echo $result_row[6] .'</td><td>';
echo 'Port Info:';
echo '<a href="bh_argus-dportreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Destination Port Report for this capture." target="_blank">DPort</a>&nbsp';
echo '<a href="bh_argus-sportreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Source Port Report for this capture." target="_blank">SPort</a>&nbsp';
echo '<a href="bh_argus-daddrdportreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Destination IP w/ Dst Port Report for this capture." target="_blank">DstIP-DPort</a>&nbsp';
echo '<a href="bh_argus-saddrdportreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Source IP --> Dst Port Report for this capture." target="_blank">SrcIP-->DstPort</a>&nbsp<br>';
echo 'IP Info:';
echo '<a href="bh_argus-saddrreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Source IP Report for this capture." target="_blank">SrcIP</a>&nbsp';
echo '<a href="bh_argus-daddrreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Destination IP Report for this capture." target="_blank">DstIP</a>&nbsp';
echo '<a href="bh_argus-saddrdaddrreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Source and Destination IP Communication Report for this capture." target="_blank">SrcIP&DstIP</a>&nbsp<br>';
echo '<a href="bh_argus-saddrdistdaddrreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Source IP and DISTINCT Destination IP Count Communication Report for this capture." target="_blank">SrcIP&DISTDstIP</a>&nbsp';
echo '<a href="bh_argus-daddrdistdaddrreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Dst IP and DISTINCT Src IP Count Communication Report for this capture." target="_blank">DstIP&DISTSrcIP</a>&nbsp<br />';
echo '<a href="bh_argus-distdstipport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Src IP DISTINCT Dst IP and Dst Port Count Communication Report for this capture." target="_blank">SrcIP&DISTDstIPDstPort</a>&nbsp<br>';
echo 'IP and Port Info:';
echo '<a href="bh_argus-saddrdaddrdportreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the Src/Dst IP and Dst Port Communication Report for this capture." target="_blank">SrcIP&DstIPwDstPort</a></td><td>';
echo '<a href="argus-jpgraph-srcdstip-Last7Day-Bar.php?srcip='.$result_row["1"].'&dstip='.$result_row["2"].'&beg_date='.$beg_date.'"title="Last 7 Days, Bar Graph For SRCDSTIP" target="_blank">7DaySRCDSTBAR</a></td></tr>';
}
echo ("</table>");
###URL DATA FOR STAT SUMMARY (EACH TABLE REPRESENTS SPECIFIC LOG TYPES)
//The query includes the form submission values that were passed to the function
$query2 ="select COUNT(DISTINCT(saddr)), COUNT(DISTINCT(daddr)), COUNT(DISTINCT(proto)), COUNT(DISTINCT(tcp_dport)), COUNT(DISTINCT(udp_dport)), COUNT(DISTINCT(http_user_agent)), COUNT(DISTINCT(http_method)), COUNT(DISTINCT(http_host)), COUNT(DISTINCT(http_request_uri)), COUNT(DISTINCT(alert_type)) from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'URLLOG')";
$result2 = $connection->query($query2);
if (DB::isError($result2)){
die ("Could not run the query: <br />". $query2." ".DB::errorMessage($result2));
}
echo "<h3><u>URL Stat Summary Results</u></h3>";
echo ('<table border="1">');
echo "<tr><th># of SrcIPs</th><th># of DstIPs</th><th># of Protocols</th><th># of DstPorts(tcp)</th><th># of DstPorts(udp)</th><th># of User Agents</th><th># of HTTP Methods</th><th># of HTTP Hosts</th><th># of HTTP URIs</th><th># of URL Alerts</th><th>URL LINKS</th><th>GRAPHS</th></tr>";
while ($result_row = $result2->fetchRow()){
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
echo 'HTTP HOST Info:<br />';
echo '<a href="bh_url-http-host_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the HTTP HOSTS url data for this capture." target="_blank">HTTP_HOST</a><br />';
echo 'HTTP HOST with IP Info:<br />';
echo '<a href="bh_url-saddrhttp-host_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the SrcIP --> HTTP HOSTS url data for this capture." target="_blank">SrcIP-->HTTP_HOST</a>&nbsp<br />';
echo '<a href="bh_url-saddrdaddrhttp-host_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the SrcIP --> DstIP - HTTP HOSTS url data for this capture." target="_blank">SrcIP-->DstIP-HTTP_HOST</a>&nbsp<br />';
echo '<a href="bh_url-http_hostandhttp-request-uri_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show All URLS with HTTP HOST and HTTP REQUEST URI url data for this capture." target="_blank">HTTP_HOST/HTTP_URI</a>&nbsp<br>';
echo 'HTTP URI Info:<br />';
echo '<a href="bh_url-http-request-uri_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the HTTP REQUEST URI url data for this capture." target="_blank">HTTP_REQUEST_URI</a>&nbsp<br>';
echo 'HTTP METHOD Info:<br />';
echo '<a href="bh_url-method_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the HTTP METHODs url data for this capture." target="_blank">HTTP METHOD</a>&nbsp<br>';
echo 'HTTP User Agent Info:<br />';
echo '<a href="bh_url-useragent_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show All User Agents url data for this capture." target="_blank">HTTP User Agent</a></td><td>';
echo '<a href="argus-jpgraph-srcdstip-Last7Day-Bar.php?srcip='.$result_row["1"].'&dstip='.$result_row["2"].'&beg_date='.$beg_date.'"title="Last 7 Days, Bar Graph For SRCDSTIP" target="_blank">7DaySRCDSTBAR</a></td></tr>';
}
echo ("</table>");

###DNS DATA FOR STAT SUMMARY (EACH TABLE REPRESENTS SPECIFIC LOG TYPES)
//The query includes the form submission values that were passed to the function
$query3 ="select COUNT(DISTINCT(saddr)), COUNT(DISTINCT(daddr)),  COUNT(DISTINCT(dns_request)), COUNT(DISTINCT(dns_response)), COUNT(DISTINCT(alert_type)) from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'DNSLOG')";
$result3 = $connection->query($query3);
if (DB::isError($result3)){
die ("Could not run the query: <br />". $query3." ".DB::errorMessage($result3));
}
echo "<h3><u>DNS Stat Summary Results</u></h3>";
echo ('<table border="1">');
echo "<tr><th># of SrcIPs</th><th># of DstIPs</th><th># of DNS REQUESTS</th><th># of DNS RESPONSES</th><th># of DNS Alerts</th><th>DNS LINKS</th><th>GRAPHS</th></tr>";
while ($result_row = $result3->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo $result_row[2] .'</td><td>';
echo $result_row[3] .'</td><td>';
echo $result_row[4] .'</td><td>';
echo 'DNS Info:';
echo '<a href="bh_dns-dns-request_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the DNS REQUEST dns data for this capture." target="_blank">DNS_REQUEST</a>&nbsp';
echo '<a href="bh_dns-saddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the DNS SrcIP dns data for this capture." target="_blank">DNS_SRCIP</a>&nbsp';
echo '<a href="bh_dns-daddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the DNS SrcIP dns data for this capture." target="_blank">DNS_DSTIP</a>&nbsp';
echo '<a href="bh_dns-saddr-dnsrequest_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the DNS SrcIP --> DNS REQUEST dns data for this capture." target="_blank">DNS_SRCIP-DNSREQUEST</a>&nbsp<br>';
echo '<a href="bh_dns-saddr-daddr-dnsrequest_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'"title="Show the DNS SrcIP --> DstIP --> DNS REQUEST dns data for this capture." target="_blank">DNS_SRCIP-DstIP-DNSREQUEST</a></td><td>';
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
<title>Bloodhound - Stat Summary</title>
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
echo "<h3><u>Stat Summary - Results </u></h3>";
echo "<b>Query Description:</b> This query will give you a basic understanding of how many machines/ports are on the network and the total bytes and packets used during this capture..<br /><br />";
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
echo "<h2>Stat Summary</h2>";
echo "<b>Query Description:</b> This query will give you a basic understanding of how many machines/ports are on the network and the total bytes and packets used during this capture.<br /><br />";
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
