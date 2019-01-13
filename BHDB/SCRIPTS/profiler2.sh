# A script that includes another script

source /opt/BB/CONF/bb_settings.conf
###PROFILE SCRIPT ALLOWS A CAP FILE TO BE INPUTTED AND IT OUTPUTS ALL SRCIPS and THEIR OSs

echo "creating analysis directory"
/bin/mkdir $OUTBOUND/$1_DIR

echo "p0fing"
/usr/sbin/p0f -qUNls $OUTBOUND/$1 > $OUTBOUND/$1.p0f.out
sleep 3
echo "Prints - SENSORNAME<DL>DATE<DL>TIME<DL>IP<DL>OS<DL>"
awk -F ":[0-9]* " '{print $1 " "$2}' $OUTBOUND/$1.p0f.out | awk -F " [(]up$" '{print $1}' | awk -F " [(]NAT[!][)]$" '{print $1}' | awk -F " - " '{print '$SENSORNAME'"##~##""'$DATEVAR'""##~##""'$TIMEVAR'""##~##"$1"##~##"$2}' | sort | uniq > $OUTBOUND/$1.p0f.final

echo "Prints - SENSORNAME<DL>DATE<DL>TIME<DL>IP<DL>OS<DL>"
awk -F ":[0-9]* " '{print $1 " "$2}' $OUTBOUND/$1.p0f.out | awk -F " [(]up$" '{print $1}' | awk -F " [(]NAT[!][)]$" '{print $1}' | awk -F " - " '{print "'$DATEVAR'"" ""'$TIMEVAR'""##~##"$1"##~##"$2}' | sort | uniq > $OUTBOUND/$1.p0f.final2


##XML FORMAT FOR AP###
echo "Prints - XML FORMAT - SENSORNAME<DL>DATE<DL>TIME<DL>IP<DL>OS<DL>"
awk -F ":[0-9]* " '{print $1 " "$2}' $OUTBOUND/$1.p0f.out | awk -F " [(]up$" '{print $1}' | awk -F " [(]NAT[!][)]$" '{
print $1}' | awk -F " - " '{print "<Alert>""<DetectTime>""<timestamp>""'$DATEVAR'"" ""'$TIMEVAR'""</timestamp>""</DetectTime>""<Classification>""<Name>"$2"</Name>""<origin>""p0f""</origin>""</Classification>""<Source>""<Address>"$1"</Address>""</Source>"
"</Alert>"}' | sort | uniq > $OUTBOUND/$1.p0f.xml

#echo "Running TSHARK for URLs"
#/usr/sbin/tshark -r $1  -R http.request -T fields -e frame.time -e ip.src -e ip.dst -e http.host -e http.request.uri | awk -F "," '{print $1$2}' | awk '{print $1" "$2" "$3"##~##"$4"##~##"$5"##~##"$6"##~##http://"$7$8}' >> $1.url

echo "Running TSHARK for URLS"
## The following was added to pull URLs from the pcap
/usr/sbin/tshark -t ad -e frame.time -e ip.src_host -e ip.dst_host -e ip.proto -e tcp.dstport -e udp.dstport -e http.user_agent -e http.host -e http.request.uri -E separator='#' -T fields -nr $OUTBOUND/$1 http.request | awk -F '#' '{print $1"##~##"$2"##~##"$3"##~##"$4"##~##"$5"##~##"$6"##~##"$7"##~##"$8"##~##"$9"##~##"$8$9}' | sed 's/ ##~## /##~##/g' | sed 's/##~####~##/##~##--blank--##~##/g' | sed 's/\(, 20[0-9][0-9]\)\( \)/\1##~##/g' > $OUTBOUND/$1.urls




echo "Pulling DNS Info"
/usr/sbin/tshark -o "column.format:Time,%Cus:frame.time,delimiter,%Cus:DS,Client,%d,delimiter,%Cus:DS,DNS Query Name,%Cus:dns.qry.name,delimiter,%Cus:DS,Info,%i" -r $OUTBOUND/$1 dns.flags.rcode != 66666 | sed 's/[ ][ ]\+/##~##/g' | sed 's/^\([A-Za-z][A-Za-z][A-Za-z]\)##~##/\1 /g' | sed 's/\(, 20[0-9][0-9]\)\( \)/\1##~##/g' > $OUTBOUND/$1.dns


echo "Running TSHARK for IRC Traffic"
/usr/sbin/tshark -tad -nr $OUTBOUND/$1 irc.request >> $OUTBOUND/$1.irc.request
/usr/sbin/tshark -tad -nr $OUTBOUND/$1 irc.response >> $OUTBOUND/$1.irc.response


echo "moving files to investigation dir"
/bin/mv $OUTBOUND/$1.p0f.* $OUTBOUND/$1_DIR/
/bin/mv $OUTBOUND/$1.irc.* $OUTBOUND/$1_DIR/
/bin/mv $OUTBOUND/$1.urls $OUTBOUND/$1_DIR/
/bin/mv $OUTBOUND/$1.dns $OUTBOUND/$1_DIR/

cat $OUTBOUND/$1_DIR/$1.p0f.final
cat $OUTBOUND/$1_DIR/$1.urls
cat $OUTBOUND/$1_DIR/$1.dns

echo "Moving CAP FILE TO ARCHIVE DIR"
/bin/mv $OUTBOUND/$1 $OUTBOUND/$1_DIR/

echo "Copying URL file to ARGUS DIRECTORY FOR PICKUP"
/bin/cp $OUTBOUND/$1_DIR/$1.urls /opt/ARGUS/OUTBOUND/$1.urls
/bin/cp $OUTBOUND/$1_DIR/$1.p0f.final /opt/ARGUS/OUTBOUND/$1.p0f.final
/bin/cp $OUTBOUND/$1_DIR/$1.dns /opt/ARGUS/OUTBOUND/$1.dns

echo "Moving Files to ARCHIVE DIR"
/bin/mv $OUTBOUND/$1_DIR $ARCHIVE
