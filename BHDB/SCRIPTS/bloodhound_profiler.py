
# A script that includes another script

source /opt/BB/CONF/bb_settings.conf
source /opt/BB/SCRIPTS/bh_mysqlimport.sh
###PROFILE SCRIPT ALLOWS A CAP FILE TO BE INPUTTED AND IT OUTPUTS ALL SRCIPS and THEIR OSs
capid=$2
filename=$1
if [ $# -lt 2 ] ; then
        echo 'You Need to Enter a CAPID for Bloodhound to process, example /opt/BB/SCRIPTS/bloodhound_profiler.sh <CAPTUREFILENAME> <CAPID>'
	exit
else
        echo 'normal ops'
capstartprocessdate=$(date +'%Y/%m/%d')
capstartprocesstime=$(date +'%H:%M:%S')
echo "HERE IS THE Capture Processing Date = $capstartprocessdate"
echo "HERE IS THE Capture Processing Time = $capstartprocesstime"


echo "creating analysis directory"
/bin/mkdir $OUTBOUND/$1_DIR

echo "Cleaning CAP file"
/usr/sbin/editcap $OUTBOUND/$1 $OUTBOUND/$1.ccap


echo "p0fing"
/usr/sbin/p0f -qUNls $OUTBOUND/$1.ccap > $OUTBOUND/$1.p0f.out
sleep 3
echo "Prints - SENSORNAME<DL>DATE<DL>TIME<DL>IP<DL>OS<DL>"
awk -F ":[0-9]* " '{print $1 " "$2}' $OUTBOUND/$1.p0f.out | awk -F " [(]up$" '{print $1}' | awk -F " [(]NAT[!][)]$" '{print $1}' | awk -F " - " '{print '$SENSORNAME'"##~##""'$DATEVAR'""##~##""'$TIMEVAR'""##~##"$1"##~##"$2}' | sort | uniq > $OUTBOUND/$1.p0f.final

echo "Prints - SENSORNAME<DL>DATE<DL>TIME<DL>IP<DL>OS<DL>"
awk -F ":[0-9]* " '{print $1 " "$2}' $OUTBOUND/$1.p0f.out | awk -F " [(]up$" '{print $1}' | awk -F " [(]NAT[!][)]$" '{print $1}' | awk -F " - " '{print "'$DATEVAR'"" ""'$TIMEVAR'""##~##"$1"##~##"$2}' | sort | uniq > $OUTBOUND/$1.p0f.final2

echo "Starting argus execution on" $1.ccap
/usr/local/sbin/argus -e $capid -F $SCRIPTS/argus.conf -r $OUTBOUND/$1.ccap -w $OUTBOUND/$1_DIR/$1.argout

                #/usr/bin/sudo /usr/local/bin/ra -F /usr/local/argus/rarc -r $PROCESS/$malware_shortname.argout - not arp and not man > $PROCESS/$malware_shortname.argru

###TEST FOR EXCEL.RARC FILE######
/usr/local/bin/ra -F $SCRIPTS/excel.rarc -r $OUTBOUND/$1_DIR/$1.argout - not arp and not man > $OUTBOUND/$1_DIR/$1.argru

argusfile=$OUTBOUND/$1_DIR/$1.argru

###DB FUNC TEST##
#func_argusdbinsert $argusfile
logdatatype='ARGUS'
func_argusdbinsert2 $argusfile

/usr/local/bin/racluster -F $SCRIPTS/excel.rarc -m proto -r $OUTBOUND/$1_DIR/$1.argout -s proto spkts dpkts sbytes dbytes > $OUTBOUND/$1_DIR/$1.argpc

/usr/local/bin/racluster -F $SCRIPTS/excel.rarc -m dport proto -r $OUTBOUND/$1_DIR/$1.argout -s dport proto spkts dpkts sbytes dbytes > $OUTBOUND/$1_DIR/$1.argdpc


##XML FORMAT FOR AP###
#echo "Prints - XML FORMAT - SENSORNAME<DL>DATE<DL>TIME<DL>IP<DL>OS<DL>"
#awk -F ":[0-9]* " '{print $1 " "$2}' $OUTBOUND/$1.p0f.out | awk -F " [(]up$" '{print $1}' | awk -F " [(]NAT[!][)]$" '{
#print $1}' | awk -F " - " '{print "<Alert>""<DetectTime>""<timestamp>""'$DATEVAR'"" ""'$TIMEVAR'""</timestamp>""</DetectTime>""<Classification>""<Name>"$2"</Name>""<origin>""p0f""</origin>""</Classification>""<Source>""<Address>"$1"</Address>""</Source>"
#"</Alert>"}' | sort | uniq > $OUTBOUND/$1.p0f.xml

#echo "Running TSHARK for URLs"
#/usr/sbin/tshark -r $1  -R http.request -T fields -e frame.time -e ip.src -e ip.dst -e http.host -e http.request.uri | awk -F "," '{print $1$2}' | awk '{print $1" "$2" "$3"##~##"$4"##~##"$5"##~##"$6"##~##http://"$7$8}' >> $1.url

echo "Running TSHARK for URLS"
## The following was added to pull URLs from the pcap
/usr/sbin/tshark -t ad -e frame.time -e ip.src_host -e ip.dst_host -e ip.proto -e tcp.dstport -e udp.dstport -e http.user_agent -e http.host -e http.request.uri -E separator='#' -T fields -nr $OUTBOUND/$1.ccap http.request | awk -F '#' '{print $1"##~##"$2"##~##"$3"##~##"$4"##~##"$5"##~##"$6"##~##"$7"##~##"$8"##~##"$9"##~##"$8$9}' | sed 's/ ##~## /##~##/g' | sed 's/##~####~##/##~##--blank--##~##/g' | sed 's/\(, 20[0-9][0-9]\)\( \)/\1##~##/g' > $OUTBOUND/$1.urls
echo "Inserting URL Data into DB"
urlfile=$OUTBOUND/$1.urls
logdatatype='URL'
#echo $logdatatype
###DB FUNC TEST##
#func_urldbinsert $urlfile $capid
func_urldbinsert2 $urlfile $capid $logdatatype


#echo "Running URL LIST through unique script"
#awk -F "##~##" '{print $3 ","$4 ","$6 ","$9}' $OUTBOUND/$1.urls | sort -u > $OUTBOUND/$1_DIR/$1.urls.uniq-sip_dip_dport_httphost
#awk -F "##~##" '{print $4 ","$9}' $OUTBOUND/$1.urls | sort -u > $OUTBOUND/$1_DIR/$1.urls.uniq-dip_httphost
#awk -F "##~##" '{print $9}' $OUTBOUND/$1.urls | sort -u > $OUTBOUND/$1_DIR/$1.urls.uniq-httphost


echo "Pulling DNS Info"
/usr/sbin/tshark -o "column.format:Time,%Cus:frame.time,delimiter,%Cus:DS,Client,%d,delimiter,%Cus:DS,DNS Query Name,%Cus:dns.qry.name,delimiter,%Cus:DS,Info,%i" -r $OUTBOUND/$1.ccap dns.flags.rcode != 66666 | sed 's/[ ][ ]\+/##~##/g' | sed 's/^\([A-Za-z][A-Za-z][A-Za-z]\)##~##/\1 /g' | sed 's/\(, 20[0-9][0-9]\)\( \)/\1##~##/g' > $OUTBOUND/$1.dns

echo "Inserting DNS Data into DB"
dnsfile=$OUTBOUND/$1.dns
logdatatype='DNS'
###DB FUNC TEST##
#func_dnsdbinsert $dnsfile $capid
func_dnsdbinsert2 $dnsfile $capid $logdatatype

#echo "Running DNS LIST through unique script"
#This next command takes the DNS File and creates a uniq DNS report - SIP, DNSNAME
#awk -F "##~##" '{print $3 ","$4 }' $OUTBOUND/$1.dns | sort -u > $OUTBOUND/$1_DIR/$1.dns.uniq-sip_DNSNAME

#This next command takes the DNS File and creates a uniq DNS report - SIP, DNSNAME
#awk -F "##~##" '{print $4 }' $OUTBOUND/$1.dns | sort -u > $OUTBOUND/$1_DIR/$1.dns.uniq-DNSNAME

#echo "Running TSHARK for IRC Traffic"
#/usr/sbin/tshark -tad -nr $OUTBOUND/$1.ccap irc.request >> $OUTBOUND/$1.irc.request
#/usr/sbin/tshark -tad -nr $OUTBOUND/$1.ccap irc.response >> $OUTBOUND/$1.irc.response

#echo "Starting argus execution on" $1.ccap 
#/usr/local/sbin/argus -e $capid -F $SCRIPTS/argus.conf -r $OUTBOUND/$1.ccap -w $OUTBOUND/$1_DIR/$1.argout

                #/usr/bin/sudo /usr/local/bin/ra -F /usr/local/argus/rarc -r $PROCESS/$malware_shortname.argout - not arp and not man > $PROCESS/$malware_shortname.argru

###TEST FOR EXCEL.RARC FILE######
#/usr/local/bin/ra -F $SCRIPTS/excel.rarc -r $OUTBOUND/$1_DIR/$1.argout - not arp and not man > $OUTBOUND/$1_DIR/$1.argru

#argusfile=$OUTBOUND/$1_DIR/$1.argru

###DB FUNC TEST##
#func_argusdbinsert $argusfile
#logdatatype='ARGUS'
#func_argusdbinsert2 $argusfile

#/usr/local/bin/racluster -F $SCRIPTS/excel.rarc -m proto -r $OUTBOUND/$1_DIR/$1.argout -s proto spkts dpkts sbytes dbytes > $OUTBOUND/$1_DIR/$1.argpc

#/usr/local/bin/racluster -F $SCRIPTS/excel.rarc -m dport proto -r $OUTBOUND/$1_DIR/$1.argout -s dport proto spkts dpkts sbytes dbytes > $OUTBOUND/$1_DIR/$1.argdpc

###Run Table Data####
#Run Table Data####
capstartdate=$(/bin/sed q $argusfile | awk -F "," '{print $2}')
capstarttime=$(/bin/sed q $argusfile | awk -F "," '{print $3}')
echo "HERE IS THE CAPSTARTDATE = $capstartdate"
echo "HERE IS THE CAPSTARTTIME = $capstarttime"
capenddate=$(/bin/sed '$!d' $argusfile | awk -F "," '{print $2}')
capendtime=$(/bin/sed '$!d' $argusfile | awk -F "," '{print $3}')
echo "HERE IS THE CAPENDDATE = $capenddate"
echo "HERE IS THE CAPENDTIME = $capendtime"



#echo "Ending argus execution on " $1.ccap
#echo "Slice and Dice Time"
#echo "Building Comm Channel File"
#awk -F "," '{print $7,$8,$11}' $OUTBOUND/$1_DIR/$1.argru | sort | uniq -c | sort -nr > $OUTBOUND/$1_DIR/$1.srcdstipdstport
#
#awk -F "," '{print $7,$8}' $OUTBOUND/$1_DIR/$1.argru | sort | uniq -c | sort -nr > $OUTBOUND/$1_DIR/$1.srcdstip

#echo "Building SrcIP File"
#awk -F "," '{print $7}' $OUTBOUND/$1_DIR/$1.argru | sort | uniq -c | sort -nr > $OUTBOUND/$1_DIR/$1.srcip
#
#echo "Building DstIP File"
#awk -F "," '{print $8}' $OUTBOUND/$1_DIR/$1.argru | sort | uniq -c | sort -nr > $OUTBOUND/$1_DIR/$1.dstip
#
#echo "Building DstPort File"
#awk -F "," '{print $11}' $OUTBOUND/$1_DIR/$1.argru | sort | uniq -c | sort -nr > $OUTBOUND/$1_DIR/$1.dstport

#Building the Report
#echo "Main Report" > $OUTBOUND/$1_DIR/$1.report
#echo "DstPorts" >> $OUTBOUND/$1_DIR/$1.report
#cat $OUTBOUND/$1_DIR/$1.dstport >> $OUTBOUND/$1_DIR/$1.report
#echo "SrcIPs" >> $OUTBOUND/$1_DIR/$1.report
#cat $OUTBOUND/$1_DIR/$1.srcip >> $OUTBOUND/$1_DIR/$1.report
#echo "DstIPs" >> $OUTBOUND/$1_DIR/$1.report
#cat $OUTBOUND/$1_DIR/$1.dstip >> $OUTBOUND/$1_DIR/$1.report
#echo "Comm Channels - SrcIP --> DstIP" >> $OUTBOUND/$1_DIR/$1.report
#cat $OUTBOUND/$1_DIR/$1.srcdstip >> $OUTBOUND/$1_DIR/$1.report
#echo "Comm Channels - SrcIP --> DstIP DstPort" >> $OUTBOUND/$1_DIR/$1.report
#cat $OUTBOUND/$1_DIR/$1.srcdstipdstport >> $OUTBOUND/$1_DIR/$1.report

capendprocessdate=$(date +'%Y/%m/%d')
capendprocesstime=$(date +'%H:%M:%S')
echo "HERE IS THE Capture Processing End Date = $capendprocessdate"
echo "HERE IS THE Capture Processing End Time = $capendprocesstime"


###DB FUNC TEST##
func_runtableinsert $capid $capstarttime $capstartdate $capendtime $capenddate $capstartprocesstime $capstartprocessdate $capendprocesstime $capendprocessdate $filename


echo "moving files to investigation dir"
/bin/mv $OUTBOUND/$1.p0f.* $OUTBOUND/$1_DIR/
#/bin/mv $OUTBOUND/$1.irc.* $OUTBOUND/$1_DIR/
/bin/mv $OUTBOUND/$1.urls $OUTBOUND/$1_DIR/
/bin/mv $OUTBOUND/$1.dns $OUTBOUND/$1_DIR/

#cat $OUTBOUND/$1_DIR/$1.p0f.final
#cat $OUTBOUND/$1_DIR/$1.urls
#cat $OUTBOUND/$1_DIR/$1.dns

echo "Moving CAP FILE TO ARCHIVE DIR"
/bin/mv $OUTBOUND/$1 $OUTBOUND/$1_DIR/
/bin/mv $OUTBOUND/$1.ccap $OUTBOUND/$1_DIR/

#echo "Copying URL file to ARGUS DIRECTORY FOR PICKUP"
#/bin/cp $OUTBOUND/$1_DIR/$1.urls /opt/ARGUS/OUTBOUND/$1.urls
#/bin/cp $OUTBOUND/$1_DIR/$1.p0f.final /opt/ARGUS/OUTBOUND/$1.p0f.final
#/bin/cp $OUTBOUND/$1_DIR/$1.dns /opt/ARGUS/OUTBOUND/$1.dns

#echo "Moving Files to ARCHIVE DIR"
#/bin/mv $OUTBOUND/$1_DIR $ARCHIVE
fi
