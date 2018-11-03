<?php
/*COMMENTS
FileName= argus-CommunicationQuery-ByDateandHour-IP-Table.php
QueryDescription=Use this query to find the Top 20 communicators.
08-16-2006:  Changed the query form input to display starttime= 00:00:01 and endtime= 23:59:59, because I was running query with defaults and didn't see any output.
08-17-2006:  Added the ipauditweb.php file which contains the db login info. and other things.  Variables, etc.
USE THIS TO FIND BIG BYTES!!!!!
09-08-2006:  Added a list for a Sort option in the Form.  Now we can sort on ip1bytes, ip2bytes, ip1pkts, ip2pkts, or count.
09-11-2006:  Added a list option for Number of Bytes.
12-19-2006:  Copied the argus-CommunicationQuery-ByDateandHour-Table.php file and made this one with IP Search.  Add ip_search Var.
12-05-2007:  Verified page to make sure it was using correct logins (argusweb.php), etc.
ENDCOMMENTS*/
include ("argusvars.php");
function query_db($beg_date,$shour,$ehour,$sort,$bytes,$ipsearch,$port,$probe_nm){
require_once ("argusweb.php");
require_once ('DB.php');
$connection = DB::connect("mysql://$db_username:$db_password@$db_host/$db_database");
if (DB::isError($connection)){
die ("Could not connect to DB: <br ?>". DB::errorMesage($connection));
}
//The query includes the form submission values that were passed to the function
$query ="select count(*) as count, saddr, daddr, SUM(sbytes) as 'Total_SrcBytes', SUM(dbytes) as 'Total_DestBytes', SUM(spkts) as 'Total_SrcPkts', SUM(dpkts) as 'Total_DestPkts' from argus, probe_tbl where (sdate = '$beg_date') AND (stime >= '$shour:00:00') AND (stime <= '$ehour:59:59') AND ((sbytes > '$bytes') OR (dbytes > '$bytes')) AND ((saddr LIKE '$ipsearch') OR (daddr LIKE '$ipsearch'))AND ((sport LIKE '$port') OR (dport LIKE '$port')) AND (argus.srcid = probe_tbl.probename_g) AND (probe_tbl.probe_label LIKE '$probe_nm')   GROUP BY saddr, daddr ORDER BY $sort DESC";
$result = $connection->query($query);
if (DB::isError($result)){
die ("Could not run the query: <br />". $query." ".DB::errorMessage($result));
}
echo ('<table border="1">');
echo "<tr><th>Count</th><th>SrcIP</th><th>DestIP</th><th>Sum Src_Bytes</th><th>Sum Dest_Bytes</th><th>Sum Src_Pkts</th><th>Sum Dest_Pkts</th><th>LINKS</th><th>GRAPHS</tr>";
while ($result_row = $result->fetchRow()){
echo "<tr><td>";
echo $result_row[0] .'</td><td>';
echo $result_row[1] .'</td><td>';
echo $result_row[2] .'</td><td>';
echo $result_row[3] .'</td><td>';
echo $result_row[4] .'</td><td>';
echo $result_row[5] .'</td><td>';
echo $result_row[6] .'</td><td>';
echo '<a href="argus-ar-scip.php?sip='.$result_row["1"].'&beg_date='.$beg_date.'&shour='.$shour.'&ehour='.$ehour.'"title="All Records for SrcIP" target="_blank">SIPAR</a>&nbsp';
echo '<a href="argus-ar-scipdstip.php?sip='.$result_row["1"].'&dip='.$result_row["2"].'&beg_date='.$beg_date.'&shour='.$shour.'&ehour='.$ehour.'"title="All Records for SrcIP and DstIP Combo" target="_blank">S&DIPAR</a>&nbsp';
echo '<a href="argus-cr-sipdip.php?sip='.$result_row["1"].'&beg_date='.$beg_date.'&shour='.$shour.'&ehour='.$ehour.'"title="Communication Report for SrcIP" target="_blank">CR</a>&nbsp';
echo '<a href="argus-cr-sipdipdport.php?sip='.$result_row["1"].'&beg_date='.$beg_date.'&shour='.$shour.'&ehour='.$ehour.'"title="Communication Report for SrcIP with Dst Port" target="_blank">CR-DP</a>&nbsp<br>';
echo '<a href="argus-cr-sipdistdip.php?sip='.$result_row["1"].'&beg_date='.$beg_date.'&shour='.$shour.'&ehour='.$ehour.'"title="Distinct Communication Report for SrcIP" target="_blank">CR-Dist</a>&nbsp';
echo '<a href="argus-pbr-sip.php?sip='.$result_row["1"].'&beg_date='.$beg_date.'&shour='.$shour.'&ehour='.$ehour.'"title="Port Breakdown SrcIP" target="_blank">PBD</a>&nbsp';
echo '<a href="http://isc.sans.org/ipinfo.html?ip='.$result_row["1"].'"title="ISC Source IP Report" target="_blank">S-ISC</a>&nbsp';
echo '<a href="http://isc.sans.org/ipinfo.html?ip='.$result_row["2"].'"title="ISC Destination IP Report" target="_blank">D-ISC</a>&nbsp';
echo '<a href="/HONEYWEB/honey-ar-sip.php?sip='.$result_row["1"].'&beg_date='.$beg_date.'"title="Show ALL records for this SrcIP from HONEYPOT LOGS" target="_blank">HSIPAR</a></td><td>';
echo '<a href="argus-jpgraph-srcip-Last1Day-Bar.php?srcip='.$result_row["1"].'&beg_date='.$beg_date.'"title="Hourly Bar Graph For SRCIP" target="_blank">HRSRCBAR</a>&nbsp';
echo '<a href="argus-jpgraph-srcip-Last7Day-Bar.php?srcip='.$result_row["1"].'&beg_date='.$beg_date.'"title="Last 7 Days, Bar Graph For SRCIP" target="_blank">7DaySRCBAR</a>&nbsp<br>';
echo '<a href="argus-jpgraph-dstip-Last1Day-Bar.php?dstip='.$result_row["2"].'&beg_date='.$beg_date.'"title="Hourly Bar Graph For DSTIP" target="_blank">HRDSTBAR</a>&nbsp';
echo '<a href="argus-jpgraph-dstip-Last7Day-Bar.php?dstip='.$result_row["2"].'&beg_date='.$beg_date.'"title="Last 7 Days, Bar Graph For DSTIP" target="_blank">7DayDSTBAR</a>&nbsp<br>';
echo '<a href="argus-jpgraph-srcdstip-Last1Day-Bar.php?srcip='.$result_row["1"].'&dstip='.$result_row["2"].'&beg_date='.$beg_date.'"title="Last 1 Day, Bar Graph For SRCDSTIP" target="_blank">1DaySRCDSTBAR</a>&nbsp';
echo '<a href="argus-jpgraph-srcdstip-Last7Day-Bar.php?srcip='.$result_row["1"].'&dstip='.$result_row["2"].'&beg_date='.$beg_date.'"title="Last 7 Days, Bar Graph For SRCDSTIP" target="_blank">7DaySRCDSTBAR</a></td></tr>';
}
echo ("</table>");
$connection->disconnect();
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="argus-style.css" />
<LINK REL="shortcut icon" HREF="argus_logo.gif" TYPE="image/x-icon">
<title>Communication Report</title>
</head>
<body>

<?php
$beg_date = $_POST['beg_date'];
$shour = $_POST['shour'];
$ehour = $_POST['ehour'];
$sort = $_POST['sort'];
$bytes = $_POST['bytes'];
$ipsearch = $_POST['ipsearch'];
$port = $_POST['port'];
$probe_nm = $_POST['probe_nm'];
$self = $_SERVER['PHP_SELF'];
if ($beg_date != NULL ){
echo "<h3><u>Communication Query - Results </u></h3>";
echo "<b>Query Description:</b> This query will show all communications for the given IP, Port, and Bytes during the Date and Time range specified.  Communication Queries generate a 'summed' report of communications  for a detailed report use the Argus Search pages.<br /><br />";
echo "
<strong>Date/Time Search Criteria:</strong><br />
<li><strong>Start Date/Time =<u>$beg_date , $shour:00:00</u></strong></li>
<li><strong>End Date/Time =<u>$beg_date , $ehour:59:59</u></strong></li><br />
<strong>IP Search Criteria:</strong><br />
<li><strong>IP = <u>$ipsearch</u></strong></li><br />
<strong>Port Search Criteria:</strong><br />
<li><strong>Port = <u>$port</u></strong></li><br />
<strong>Byte Total Search Criteria:</strong><br />
<li><strong>Total Bytes >= <u>$bytes</u></strong></li><br />
<strong>Probe Name Search Criteria:</strong><br />
<li><strong>For Probe = <u>$probe_nm</u></strong></li><br />
" ;
query_db($beg_date,$shour,$ehour,$sort,$bytes,$ipsearch,$port,$probe_nm);
}
else {
 /*Passing today's date into form*/
echo "<h2>Communication Report</h2>";
echo "<b>Query Description:</b> This query will show all communications for the given IP, Port, and Bytes during the Date and Time range specified.  Communication Queries generate a 'summed' report of communications  for a detailed report use the Argus Search pages.<br /><br />";
echo ('
<form action="'.$self.'"method="post">
<label>Date to Search:
<input type="text" name="beg_date" maxlength="10" size="10" id="beg_date" value="'.$sdate.'"/>
</label><br />
<label>Hour to Begin Search:
<SELECT  name="shour" id="shour">
<option value="00" selected="selected">00:00</option>
<option value="01">1:00</option>
<option value="02">2:00</option>
<option value="03">3:00</option>
<option value="04">4:00</option>
<option value="05">5:00</option>
<option value="06">6:00</option>
<option value="07">7:00</option>
<option value="08">8:00</option>
<option value="09">9:00</option>
<option value="10">10:00</option>
<option value="11">11:00</option>
<option value="12">12:00</option>
<option value="13">13:00</option>
<option value="14">14:00</option>
<option value="15">15:00</option>
<option value="16">16:00</option>
<option value="17">17:00</option>
<option value="18">18:00</option>
<option value="19">19:00</option>
<option value="20">20:00</option>
<option value="21">21:00</option>
<option value="22">22:00</option>
<option value="23">23:00</option>
</SELECT><br />
<label>Hour to End Search:
<SELECT  name="ehour" id="ehour">
<option value="00">00:59</option>
<option value="01">1:59</option>
<option value="02">2:59</option>
<option value="03">3:59</option>
<option value="04">4:59</option>
<option value="05">5:59</option>
<option value="06">6:59</option>
<option value="07">7:59</option>
<option value="08">8:59</option>
<option value="09">9:59</option>
<option value="10">10:59</option>
<option value="11">11:59</option>
<option value="12">12:59</option>
<option value="13">13:59</option>
<option value="14">14:59</option>
<option value="15">15:59</option>
<option value="16">16:59</option>
<option value="17">17:59</option>
<option value="18">18:59</option>
<option value="19">19:59</option>
<option value="20">20:59</option>
<option value="21">21:59</option>
<option value="22">22:59</option>
<option value="23" selected="selected">23:59</option>
</SELECT><br />
<label>Sort By:
<SELECT name="sort" id="sort">
<option value="count" selected="selected">Count</option>
<option value="Total_SrcBytes">Total Source Bytes</option>
<option value="Total_DestBytes">Total Destination Bytes</option>
<option value="Total_SrcPkts">Total Source Pkts</option>
<option value="Total_DestPkts">Total Destination Pkts</option>
</SELECT><br />
<label>Total Number of Bytes:
<SELECT name="bytes" id="bytes">
<option value="0" selected="selected">0 Bytes</option>
<option value="10000">10,000 Bytes</option>
<option value="100000">100,000 Bytes</option>
<option value="1000000">1,000,000 Bytes</option>
<option value="10000000">10,000,000 Bytes</option>
<option value="100000000">100,000,000 Bytes</option>
<option value="1000000000">1,000,000,000 Bytes</option>
</SELECT><br />
<label>IP to Search:
<input type="text" name="ipsearch" maxlength="15" size="15" id="ipsearch" value="%"/>
</label><br />
<label>Port to Search:
<input type="text" name="port" maxlength="5" size="5" id="port" value="%"/>
</label><br />
<label>Probe Name:
<input type="text" name="probe_nm" maxlength="15" size="15" id="probe_nm" value="%"/>
</label><br />
<input type="submit" value="Run Query" />
</form>
');
}

?>
</body>
</html>






