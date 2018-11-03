<?php
/*COMMENTS
FileName= bh_argus-saddrreport_capanddatespecific.php
QueryDescription= Use this query to see all Dest Ports used for a specific CapID, Date, and Time
ENDCOMMENTS*/
include ("bhvars.php");
function query_db($beg_date,$end_date,$beg_time,$end_time,$capid,$ip){
require_once ("bhweb.php");
require_once ('DB.php');
$connection = DB::connect("mysql://$db_username:$db_password@$db_host/$db_database");
if (DB::isError($connection)){
die ("Could not connect to DB: <br ?>". DB::errorMesage($connection));
}
$query1a ="select DISTINCT(os_type) from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'P0FLOG') AND (saddr = '$ip')";
$result1a = $connection->query($query1a);
if (DB::isError($result1a)){
die ("Could not run the query: <br />". $query1a." ".DB::errorMessage($result1a));
}
echo "<h3><u>Possible OS INFO for $ip</u></h3>";
echo ('<table border="1">');
echo "<tr><th>O/S</th></tr>";
while ($result_row = $result1a->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td></tr>';
}
echo ("</table>");



//The query includes the form submission values that were passed to the function
$query ="select COUNT(DISTINCT(saddr)), COUNT(DISTINCT(daddr)), COUNT(DISTINCT(sport)), COUNT(DISTINCT(dport)), COUNT(DISTINCT(proto)), SUM(bytes), SUM(pkts) from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'ARGUSLOG') AND ((saddr = '$ip') OR (daddr = '$ip'))";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo "<h3><u>ARGUS Stat Summary Results for $ip </u></h3>";
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
echo '<a href="bh_argus-portbreakdown-saddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$ip.'"title="Port BreakDowns for SrcIP." target="_blank">PBD_SRCIP</a><br />';
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
$query2 ="select COUNT(DISTINCT(saddr)), COUNT(DISTINCT(daddr)), COUNT(DISTINCT(proto)), COUNT(DISTINCT(tcp_dport)), COUNT(DISTINCT(udp_dport)), COUNT(DISTINCT(http_user_agent)), COUNT(DISTINCT(http_method)), COUNT(DISTINCT(http_host)), COUNT(DISTINCT(http_request_uri)), COUNT(DISTINCT(alert_type)) from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'URLLOG') AND ((saddr = '$ip') OR (daddr = '$ip'))";
$result2 = $connection->query($query2);
if (DB::isError($result2)){
die ("Could not run the query: <br />". $query2." ".DB::errorMessage($result2));
}
echo "<h3><u>URL Stat Summary Results for $ip</u></h3>";
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
echo '<a href="bh_url-http-host-ip_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&ip='.$ip.'"title="Show the HTTP HOSTS url data for this capture." target="_blank">HTTP_HOST</a><br />';
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

$query2a ="select count(*) as count, http_host from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'URLLOG') AND ((saddr = '$ip') OR (daddr = '$ip')) GROUP BY http_host ORDER BY count DESC";
$result2a = $connection->query($query2a);
if (DB::isError($result2a)){
die ("Could not run the query: <br />". $query2a." ".DB::errorMessage($result2a));
}
echo "<h3><u>HTTP HOST INFO for $ip</u></h3>";
echo ('<table border="1">');
echo "<tr><th>Count</th><th>HTTP_HOST</th><th>LINKS</th><th>GRAPHS</tr>";
while ($result_row = $result2a->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo 'All DNS/URL Reports: ';
echo '<a href="bh_urldns-httphost_dns-request-capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&requestorhost='.$result_row["1"].'"title="Show the DNS and URL requests for this Name for this capture." target="_blank">HTTP_HOST and DNS_REQUEST</a><br />';
echo '<a href="http://www.google.com/search?q='.$result_row["1"].'"title="Google Search for URL." target="_blank">Google Search HTTP_HOST</a></td><td>';
echo '<a href="argus-jpgraph-srcdstip-Last7Day-Bar.php?srcip='.$result_row["1"].'&dstip='.$result_row["2"].'&beg_date='.$beg_date.'"title="Last 7 Days, Bar Graph For SRCDSTIP" target="_blank">7DaySRCDSTBAR</a></td></tr>';
}
echo ("</table>");

$query2b ="select count(*) as count, http_user_agent from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'URLLOG') AND ((saddr = '$ip') OR (daddr = '$ip')) GROUP BY http_user_agent ORDER BY count DESC";
$result2b = $connection->query($query2b);
if (DB::isError($result2b)){
die ("Could not run the query: <br />". $query2b." ".DB::errorMessage($result2b));
}
echo "<h3><u>HTTP User Agent INFO for $ip</u></h3>";
echo ('<table border="1">');
echo "<tr><th>Count</th><th>User Agent</th><th>LINKS</th><th>GRAPHS</tr>";
while ($result_row = $result2b->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo 'All DNS/URL Reports:<br /> ';
echo '<a href="bh_url-commreportuseragent_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&useragent='.base64_encode($result_row["1"]).'"title="Show the Communication Report for this User Agent." target="_blank">CR_UserAgent</a><br />';
echo '<a href="bh_url-commreportuseragent-ip_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&useragent='.base64_encode($result_row["1"]).'&ip='.$ip.'"title="Show the Communication Report for this User Agent and SRCIP." target="_blank">CR_SRCIPUserAgent</a><br />';
echo '<a href="http://www.google.com/search?q='.$result_row["1"].'"title="Google Search for User Agent." target="_blank">Google Search User Agent</a></td><td>';
echo '<a href="argus-jpgraph-srcdstip-Last7Day-Bar.php?srcip='.$result_row["1"].'&dstip='.$result_row["2"].'&beg_date='.$beg_date.'"title="Last 7 Days, Bar Graph For SRCDSTIP" target="_blank">7DaySRCDSTBAR</a></td></tr>';
}
echo ("</table>");


###DNS DATA FOR STAT SUMMARY (EACH TABLE REPRESENTS SPECIFIC LOG TYPES)
//The query includes the form submission values that were passed to the function
$query3 ="select COUNT(DISTINCT(saddr)), COUNT(DISTINCT(daddr)),  COUNT(DISTINCT(dns_request)), COUNT(DISTINCT(dns_response)), COUNT(DISTINCT(alert_type)) from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'DNSLOG') AND ((saddr = '$ip') OR (daddr = '$ip'))";
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

$query3a ="select count(*) as count, dns_request from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'DNSLOG') AND ((saddr = '$ip') OR (daddr = '$ip')) GROUP BY dns_request ORDER BY count DESC";
$result3a = $connection->query($query3a);
if (DB::isError($result3a)){
die ("Could not run the query: <br />". $query3a." ".DB::errorMessage($result3a));
}
echo "<h3><u>DNS REQUEST Summary Results for $ip</u></h3>";
echo ('<table border="1">');
echo "<tr><th>Count</th><th>DNS_REQUEST</th><th>LINKS</th></tr>";
while ($result_row = $result3a->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo '<a href="bh_urldns-httphost_dns-request-capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&requestorhost='.$result_row["1"].'"title="Show the DNS and URL requests for this Name for this capture." target="_blank">HTTP_HOST and DNS_REQUEST</a><br />';
echo '<a href="http://www.google.com/search?q='.$result_row["1"].'"title="Google Search for DNS REQUEST." target="_blank">Google Search DNS_REQUEST</a></td></tr>';
}
echo ("</table>");

//The query includes the form submission values that were passed to the function
$query4 ="select count(*) as count, saddr, daddr, class_type, alert_type from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (log_type = 'SNORTLOG') AND ((saddr = '$ip') OR (daddr = '$ip')) GROUP BY daddr, saddr, class_type, alert_type ORDER BY count DESC";
$result4 = $connection->query($query4);
if (DB::isError($result4)){
die ("Could not run the query: <br />". $query4." ".DB::errorMessage($result4));
}
echo "<h3><u>SNORT Summary Results for $ip</u></h3>";
echo ('<table border="1">');
echo "<tr><th>Count</th><th>Src IP</th><th>Dst IP</th><th>Classification</th><th>Alert Type</th><th>LINKS</th></tr>";
while ($result_row = $result4->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo $result_row[2] .'</td><td>';
echo $result_row[3] .'</td><td>';
echo $result_row[4] .'</td><td>';
echo 'SrcIP Communication Reports: ';
echo '<a href="bh_argus-commreport-saddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["1"].'"title="Show the Communication Report for this SrcIP." target="_blank">CR_SRCIP</a>&nbsp';
echo '<a href="bh_argus-commreport-saddrordaddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["1"].'"title="Show the Communication Report for this IP." target="_blank">CR_ALLFORSRCIP</a><br />';
echo 'DstIP Communication Reports: ';
echo '<a href="bh_argus-commreport-daddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&daddr='.$result_row["2"].'"title="Show the Communication Report for this DstIP." target="_blank">CR_DSTIP</a>&nbsp';
echo '<a href="bh_argus-commreport-saddrordaddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["2"].'"title="Show the Communication Report for this IP." target="_blank">CR_ALLFORDSTIP</a><br />';
echo 'All Information Reports: ';
echo '<a href="bh_argus-saddrfullreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["1"].'"title="Show the Communication Report for this IP." target="_blank">ALLFORSRCIP</a>&nbsp';
echo '<a href="bh_argus-daddrfullreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&daddr='.$result_row["2"].'"title="Show the Communication Report for this IP." target="_blank">ALLFORDSTIP</a><br />';
echo 'Who IS Links: ';
echo '<a href="http://whois.domaintools.com/'.$result_row["1"].'"title="WhoIs For ScrIP" target="_blank">WHOSRCIP</a>&nbsp';
echo '<a href="http://whois.domaintools.com/'.$result_row["2"].'"title="WhoIs For DstIP" target="_blank">WHODSTIP</a><br />';
echo 'All Information Reports:<br /> ';
echo '<a href="bh_ipstatsummary.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&ip='.$result_row["1"].'"title="Show the Stat Summary." target="_blank">SrcIP_Stats</a>&nbsp&nbsp';
echo '<a href="bh_ipstatsummary.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&ip='.$result_row["2"].'"title="Show the Stat Summary." target="_blank">DstIP_Stats</a><br>';
echo 'All DNS/URL Reports: ';
echo '<a href="bh_argusurldns-saddrdaddr_httphost_dns-request-capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&requestorhost='.$result_row["2"].'"title="Show me all the argus, dns, and url data for this DstIP." target="_blank">DstIP</a><br />';
echo 'Snort Reports: ';
echo '<a href="bh_snort-ipdetailreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&addr='.$result_row["1"].'"title="Show me the IP Snort Detail Report for this SrcIP." target="_blank">SnortSrcIPDR</a>&nbsp';
echo '<a href="bh_snort-ipdetailreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&addr='.$result_row["2"].'"title="Show me the IP Snort Detail Report for this DstIP." target="_blank">SnortDstIPDR</a></td></tr>';
}
echo ("</table>");


$connection->disconnect();
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="bh-style.css" />
<LINK REL="shortcut icon" HREF="argus_logo.gif" TYPE="image/x-icon">
<title>Bloodhound - IP Stat Summary</title>
</head>
<body>

<?php
$beg_date = $_GET['beg_date'];
$end_date = $_GET['end_date'];
$beg_time = $_GET['beg_time'];
$end_time = $_GET['end_time'];
$capid = $_GET['capid'];
$ip = $_GET['ip'];
$self = $_SERVER['PHP_SELF'];
if ($beg_date != NULL || $ip != NULL ){
echo "<h3><u>IP Stat Summary - Results </u></h3>";
echo "<b>Query Description:</b> This query will give you a basic understanding of how many machines/ports are on the network and the total bytes and packets used during this capture..<br /><br />";
echo "
<strong>Date/Time Search Criteria:</strong><br />
<li><strong>Start Date/Time =<u>$beg_date , $beg_time</u></strong></li>
<li><strong>End Date/Time =<u>$end_date , $end_time</u></strong></li><br />
<strong>CAPID Search Criteria:</strong><br />
<li><strong>Capid = <u>$capid</u></strong></li><br />
<li><strong>IP = <u>$ip</u></strong></li><br />
" ;
query_db($beg_date,$end_date,$beg_time,$end_time,$capid,$ip);
}
else {
 /*Passing today's date into form*/
echo "<h2>Stat Summary by IP</h2>";
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
<label>IP to Search:
<input type="text" name="ip" maxlength="15" size="15" id="ip" value=""/>
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
