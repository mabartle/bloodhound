<?php
/*COMMENTS
FileName= bh_runreport.php
QueryDescription=This page will show all 'capture' runs in the run_tbl.
ENDCOMMENTS*/
include ("bhvars.php");
function query_db($beg_date,$end_date,$capid){
require_once ("bhweb.php");
require_once ('DB.php');
$connection = DB::connect("mysql://$db_username:$db_password@$db_host/$db_database");
if (DB::isError($connection)){
die ("Could not connect to DB: <br ?>". DB::errorMesage($connection));
}
//The query includes the form submission values that were passed to the function
$query ="select capid, capstarttime, capstartdate, capendtime, capenddate, capstartprocesstime, capstartprocessdate, capendprocesstime, capendprocessdate,filename from run_tbl where (capstartdate >= '$beg_date') AND (capstartdate <= '$end_date') AND (capid LIKE '$capid')";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo ('<table border="1">');
echo "<tr><th>CAPID</th><th>Capture Times</th><th>Processing Times</th><th>File Name</th><th>LINKS</th><tr>";
while ($result_row = $result->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo '<b>St. Time:</b>' .$result_row[1] .','.$result_row[2] .'<br>' .'<b>End Time:</b>'.$result_row[3] .','.$result_row[4] .'</td><td>';
echo '<b>St. Time:</b>' .$result_row[5] .','.$result_row[6] .'<br>' .'<b>End Time:</b>'. $result_row[7] .','.$result_row[8] .'</td><td>';
echo $result_row[9] .'</td><td>';
echo 'CAP Info:';
echo '<a href="bh_argus_statsummary.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Stat Summary." target="_blank">Stat_Summary</a><br>';
echo 'Port Info:';
echo '<a href="bh_argus-dportreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Destination Port Report for this capture." target="_blank">DPort</a>&nbsp';
echo '<a href="bh_argus-dportreportPKTS_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Destination Port Report by Packets for this capture." target="_blank">DPortPkts</a>&nbsp';
echo '<a href="bh_argus-dportreportBYTES_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Destination Port Report by Bytes for this capture." target="_blank">DPortBytes</a>&nbsp';
echo '<a href="bh_argus-sportreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Source Port Report for this capture." target="_blank">SPort</a>&nbsp';
echo '<a href="bh_argus-daddrdportreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Destination IP w/ Dst Port Report for this capture." target="_blank">DstIP-DPort</a>&nbsp';
echo '<a href="bh_argus-saddrdportreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Source IP --> Dst Port Report for this capture." target="_blank">SrcIP-->DstPort</a>&nbsp<br>';
echo '<a href="bh_argus-lowsrcanddstportreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show all communications with a low SrcPort and DstPort for this capture." target="_blank">LowSrcPort-->LowDstPort</a>&nbsp<br>';
echo 'IP Info:';
echo '<a href="bh_argus-saddrreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Source IP Report for this capture." target="_blank">SrcIP</a>&nbsp';
echo '<a href="bh_argus-daddrreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Destination IP Report for this capture." target="_blank">DstIP</a>&nbsp';
echo '<a href="bh_argus-daddrreportTOTBYTES_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Destination IP Report by Total Bytes for this capture." target="_blank">DstIPTOTBYTES</a>&nbsp';
echo '<a href="bh_argus-saddrdaddrreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Source and Destination IP Communication Report for this capture." target="_blank">SrcIP&DstIP</a>&nbsp<br>';
echo '<a href="bh_argus-saddrdistdaddrreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Source IP and DISTINCT Destination IP Count Communication Report for this capture." target="_blank">SrcIP&DISTDstIP</a>&nbsp<br>';
echo '<a href="bh_argus-daddrdistdaddrreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Dst IP and DISTINCT Src IP Count Communication Report for this capture." target="_blank">DstIP&DISTSrcIP</a>&nbsp<br>';
echo '<a href="bh_argus-distdstipport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Src IP DISTINCT Dst IP and Dst Port Count Communication Report for this capture." target="_blank">SrcIP&DISTDstIPDstPort</a>&nbsp<br>';
echo 'IP and Port Info:';
echo '<a href="bh_argus-saddrdaddrdportreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Src/Dst IP and Dst Port Communication Report for this capture." target="_blank">SrcIP&DstIPwDstPort</a>&nbsp<br>';
echo 'Protocol Info:';
echo '<a href="bh_argus-icmpreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the ICMP CODES from data for this capture." target="_blank">ICMP CODES</a><br />';
echo '<a href="bh_argus-icmpsaddrreport_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the ICMP CODES with SrcIP from data for this capture." target="_blank">ICMP w/SrcIP</a><br />';
echo 'URL Info:';
echo '<a href="bh_url-http-host_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the HTTP HOSTS url data for this capture." target="_blank">HTTP_HOST</a>&nbsp';
echo '<a href="bh_url-saddrhttp-host_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the SrcIP --> HTTP HOSTS url data for this capture." target="_blank">SrcIP-->HTTP_HOST</a>&nbsp';
echo '<a href="bh_url-saddrdaddrhttp-host_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the SrcIP --> DstIP - HTTP HOSTS url data for this capture." target="_blank">SrcIP-->DstIP-HTTP_HOST</a>&nbsp';
echo '<a href="bh_url-saddrdistdaddr_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the SrcIP --> DstIP - HTTP HOSTS url data for this capture." target="_blank">SrcIP-->Dist-DstIP-HTTP_HOST</a>&nbsp';
echo '<a href="bh_url-http-request-uri_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the HTTP REQUEST URI url data for this capture." target="_blank">HTTP_REQUEST_URI</a>&nbsp<br>';
echo '<a href="bh_url-http_hostandhttp-request-uri_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show All URLS with HTTP HOST and HTTP REQUEST URI url data for this capture." target="_blank">HTTP_HOST/HTTP_URI</a>';
echo '<a href="bh_url-useragent_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show All HTTP User Agents for this capture." target="_blank">HTTP_USER_AGENT</a>';
echo '<a href="bh_url-method_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show All HTTP METHODS for this capture." target="_blank">HTTP_METHOD</a><br>';
echo 'DNS Info:';
echo '<a href="bh_dns-dns-request_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the DNS REQUEST dns data for this capture." target="_blank">DNS_REQUEST</a>&nbsp';
echo '<a href="bh_dns-saddr_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the DNS SrcIP dns data for this capture." target="_blank">DNS_SRCIP</a>&nbsp';
echo '<a href="bh_dns-daddr_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the DNS SrcIP dns data for this capture." target="_blank">DNS_DSTIP</a>&nbsp';
echo '<a href="bh_dns-saddr-dnsrequest_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the DNS SrcIP --> DNS REQUEST dns data for this capture." target="_blank">DNS_SRCIP-DNSREQUEST</a>&nbsp<br>';
echo '<a href="bh_dns-saddr-daddr-dnsrequest_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the DNS SrcIP --> DstIP --> DNS REQUEST dns data for this capture." target="_blank">DNS_SRCIP-DstIP-DNSREQUEST</a>&nbsp<br>';
echo 'Snort Info: ';
echo '<a href="bh_snort-commreport-saddrdaddr_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Snort Communication Report for this capture." target="_blank">SNORT_CR</a>&nbsp<br />';
echo '<a href="bh_snort-srcipcommreport-saddrdaddr_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show the Snort SrcIP Communication Report for this capture." target="_blank">SNORT_SRCIPCR</a>&nbsp<br />';
echo 'Interesting Traffic: ';
echo '<a href="bh_interestingtrafficquery_capanddatespecific.php?beg_date='.$result_row["2"].'&end_date='.$result_row["4"].'&beg_time='.$result_row["1"].'&end_time='.$result_row["3"].'&capid='.$result_row["0"].'"title="Show all flagged events for this capture." target="_blank">Flagged_Events</a>&nbsp<br></td></tr>';
}
echo ("</table>");
$connection->disconnect();
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="bh-style.css" />
<LINK REL="shortcut icon" HREF="argus_logo.gif" TYPE="image/x-icon">
<title>Bloodhound Run Page</title>
</head>
<body>
<?php
$beg_date = $_GET['beg_date'];
$end_date = $_GET['end_date'];
$capid = $_GET['capid'];
$self = $_SERVER['PHP_SELF'];
if ($beg_date != NULL ){
echo "<h3><u>Bloodhound Run Page - Results </u></h3>";
echo "<b>Query Description:</b> This page shows all Bloodhound Processing Runs.<br /><br />";
echo "
<strong>Search Criteria:</strong><br />
<li><strong>Start Date =<u>$beg_date</u></strong></li>
<li><strong>End Date =<u>$end_date</u></strong></li>
<li><strong>Capture ID =<u>$capid</u></strong></li><br />
" ;
query_db($beg_date,$end_date,$capid);
}
else {
 /*Passing today's date into form*/
echo "<h2>Bloodhound Run Page</h2>";
echo "<b>Query Description:</b> This page shows all Bloodhound Processing Runs.<br /><br />";
echo ('
<form action="'.$self.'"method="get">
<label>Start Date:
<input type="text" name="beg_date" maxlength="10" size="10" id="beg_date" value="'.$sdate.'"/>
</label><br />
<label>End Date:
<input type="text" name="end_date" maxlength="10" size="10" id="end_date" value="'.$sdate.'"/>
</label><br />
<label>Capture ID:
<input type="text" name="capid" maxlength="15" size="15" id="capid" value="%"/>
</label><br />
<input type="submit" value="Run Query" />
</form>
');
}

?>
</body>
</html>
