<?php
/*COMMENTS
FileName= bh_argus-uniquelogsport_capanddatespecific.php
QueryDescription= Use this query to see all traffic with an originating "low" source port
ENDCOMMENTS*/
include ("bhvars.php");
function query_db($dport){
require_once ("bhweb.php");
require_once ('DB.php');
$connection = DB::connect("mysql://$db_username:$db_password@$db_host/$db_database");
if (DB::isError($connection)){
die ("Could not connect to DB: <br ?>". DB::errorMesage($connection));
}
//The query includes the form submission values that were passed to the function
$query ="select count(*) as count, capid, sdate, saddr, daddr, dport, log_type from bh_table where ((dport = '$dport') OR (tcp_dport = '$dport') OR (udp_dport = '$dport')) GROUP BY capid, sdate, saddr, daddr, dport, log_type ORDER BY sdate DESC";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo ('<table border="1">');
echo "<tr><th>Count</th><th>CAPID</th><th>Date</th><th>SrcIP</th><th>DstIP</th><th>DstPort</th><th>Log Type</th><th>LINKS</th><th>GRAPHS</tr>";
while ($result_row = $result->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo $result_row[2] .'</td><td>';
echo $result_row[3] .'</td><td>';
echo $result_row[4] .'</td><td>';
echo $result_row[5] .'</td><td>';
echo $result_row[6] .'</td><td>';
echo '<a href="bh_argus-commreport-sport_capanddatespecific.php?beg_date='.$beg_date.'&end_date='.$end_date.'&beg_time='.$beg_time.'&end_time='.$end_time.'&capid='.$capid.'&sport='.$result_row["1"].'"title="Show the Communication Report for this SrcPort." target="_blank">CR_SPort</a>&nbsp</td><td>';
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
<title>Bloodhound - DstPort Report</title>
</head>
<body>

<?php
$dport = $_GET['dport'];
$self = $_SERVER['PHP_SELF'];
if ($dport != NULL ){
echo "<h3><u>Dst Port Query - Results </u></h3>";
echo "<b>Query Description:</b> This query will show all occurances of this DstPort from ANY capture.<br /><br />";
echo "
<strong>Search Criteria:</strong><br />
<li><strong>DstPort = <u>$dport</u></strong></li><br />
" ;
query_db($dport);
}
else {
 /*Passing today's date into form*/
echo "<h2>DstPort Report</h2>";
echo "<b>Query Description:</b> This query will show all occurances of this DstPort from ANY capture.<br /><br />";
echo ('
<form action="'.$self.'"method="get">
<label>DstPort to Search:
<input type="text" name="dport" maxlength="5" size="5" id="dport" value=""/>
</label><br />
<input type="submit" value="Run Query" />
</form>
');
}

?>
</body>
</html>
