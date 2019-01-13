<?php
/*COMMENTS
FileName= bh_argus-commreportPKTS-dport_capanddatespecific.php
QueryDescription= Use this query to see the Communication Report with BYTE info - Count, saddr, daddr, proto, sport, dport for a specific CapID, Date, and Time
ENDCOMMENTS*/
include ("bhvars.php");
function query_db($beg_date,$end_date,$beg_time,$end_time,$capid, $dport){
require_once ("bhweb.php");
require_once ('DB.php');
$connection = DB::connect("mysql://$db_username:$db_password@$db_host/$db_database");
if (DB::isError($connection)){
die ("Could not connect to DB: <br ?>". DB::errorMesage($connection));
}
//The query includes the form submission values that were passed to the function
$query ="select count(*) as count, saddr, daddr, proto, dport, sum(bytes) as sumbytes, sum(pkts) as sumpkts from bh_table where (capid LIKE '$capid') AND ((sdate >= '$beg_date') AND (sdate <= '$end_date')) AND ((stime >= '$beg_time') AND (stime <= '$end_time')) AND (dport = '$dport') AND (log_type = 'ARGUSLOG') GROUP BY saddr, daddr, proto, dport ORDER BY sumpkts DESC";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo ('<table border="1">');
echo "<tr><th>Count</th><th>Src IP</th><th>Dst IP</th><th>Proto</th><th>Dst Port</th><th>Bytes</th><th>Packets</th><th>LINKS</th><th>GRAPHS</tr>";
while ($result_row = $result->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo $result_row[2] .'</td><td>';
echo $result_row[3] .'</td><td>';
echo $result_row[4] .'</td><td>';
echo $result_row[5] .'</td><td>';
echo $result_row[6] .'</td><td>';
echo 'SrcIP Comm Reports: ';
echo '<a href="bh_argus-commreport-saddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["1"].'"title="Show the Communication Report for this SrcIP." target="_blank">CR_SRCIP</a>&nbsp';
echo '<a href="bh_argus-commreport-saddrordaddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["1"].'"title="Show the Communication Report for this IP." target="_blank">CR_ALLFORSRCIP</a><br />';
echo 'DstIP Comm Reports: ';
echo '<a href="bh_argus-commreport-daddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&daddr='.$result_row["2"].'"title="Show the Communication Report for this DstIP." target="_blank">CR_DSTIP</a>&nbsp';
echo '<a href="bh_argus-commreport-saddrordaddr_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["2"].'"title="Show the Communication Report for this IP." target="_blank">CR_ALLFORDSTIP</a><br />';
echo 'All Info Reports:<br /> ';
echo '<a href="bh_ipstatsummary.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&ip='.$result_row["1"].'"title="Show the Stat Summary." target="_blank">SrcIP_Stats</a>&nbsp&nbsp';
echo '<a href="bh_ipstatsummary.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&ip='.$result_row["2"].'"title="Show the Stat Summary." target="_blank">DstIP_Stats</a><br>';
echo '<a href="bh_argus-saddrfullreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&saddr='.$result_row["1"].'"title="Show the Communication Report for this IP." target="_blank">ALLFORSRCIP</a>&nbsp';
echo '<a href="bh_argus-daddrfullreport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&daddr='.$result_row["2"].'"title="Show the Communication Report for this IP." target="_blank">ALLFORDSTIP</a><br />';
echo 'Who IS Links: ';
echo '<a href="http://whois.domaintools.com/'.$result_row["1"].'"title="WhoIs For ScrIP" target="_blank">WHOSRCIP</a>';
echo '<a href="http://whois.domaintools.com/'.$result_row["2"].'"title="WhoIs For DstIP" target="_blank">WHODSTIP</a><br />';
echo 'All DNS/URL Reports: ';
echo '<a href="bh_argusurldns-saddrdaddr_httphost_dns-request-capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&requestorhost='.$result_row["1"].'"title="Show me all the argus, dns, and url data for this SrcIP." target="_blank">SrcIP</a>&nbsp';
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
<title>Bloodhound - Dst Port Communication Report by Packets</title>
</head>
<body>

<?php
$beg_date = $_GET['beg_date'];
$end_date = $_GET['end_date'];
$beg_time = $_GET['beg_time'];
$end_time = $_GET['end_time'];
$capid = $_GET['capid'];
$dport = $_GET['dport'];
$self = $_SERVER['PHP_SELF'];
if ($beg_date != NULL ){
echo "<h3><u>Destination Port Communication Report (by Packets) Query - Results </u></h3>";
echo "<b>Query Description:</b> This query will show the Communication Report for the Dst Port(s) by Packets used during this capture..<br /><br />";
echo "
<strong>Date/Time Search Criteria:</strong><br />
<li><strong>Start Date/Time =<u>$beg_date , $beg_time</u></strong></li>
<li><strong>End Date/Time =<u>$end_date , $end_time</u></strong></li><br />
<strong>CAPID Search Criteria:</strong><br />
<li><strong>Capid = <u>$capid</u></strong></li><br />
<strong>Dst Port Search Criteria:</strong><br />
<li><strong>Destination Port = <u>$dport</u></strong></li><br />
" ;
query_db($beg_date,$end_date,$beg_time,$end_time,$capid,$dport);
}
else {
 /*Passing today's date into form*/
echo "<h2>Destination Port Communication Report (by Packets)</h2>";
echo "<b>Query Description:</b> This query will show the Communication Report for the Dst Port(s) by Packets  used during this capture..<br /><br />";
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
<label>Destination Port to Search:
<input type="text" name="dport" maxlength="6" size="6" id="dport" value="%"/>
</label><br />
<input type="submit" value="Run Query" />
</form>
');
}

?>
</body>
</html>
