# A script that includes another script

source /opt/BB/CONF/bb_settings.conf
###PROFILE SCRIPT ALLOWS A CAP FILE TO BE INPUTTED AND IT OUTPUTS ALL SRCIPS and THEIR OSs

echo "creating analysis directory"
/bin/mkdir $OUTBOUND/$1_DIR

echo "p0fing"
/usr/sbin/p0f -qUNls $1 > $OUTBOUND/$1.p0f.out
sleep 3
echo "Prints - SENSORNAME<DL>DATE<DL>TIME<DL>IP<DL>OS<DL>"
awk -F ":[0-9]* " '{print $1 " "$2}' $OUTBOUND/$1.p0f.out | awk -F " [(]up$" '{print $1}' | awk -F " [(]NAT[!][)]$" '{print $1}' | awk -F " - " '{print '$SENSORNAME'"##~##""'$DATEVAR'""##~##""'$TIMEVAR'""##~##"$1"##~##"$2}' | sort | uniq > $OUTBOUND/$1.p0f.final_$SENSORNAME

echo "Running TSHARK for URLs"
/usr/sbin/tshark -r $1  -R http.request -T fields -e frame.time -e ip.src -e ip.dst -e http.host -e http.request.uri | awk -F "," '{print $1$2}' | awk '{print $1" "$2" "$3"##~##"$4"##~##"$5"##~##"$6"##~##http://"$7$8}' >> $1.url

echo "Running TSHARK for IRC Traffic"
/usr/sbin/tshark -tad -nr $1 $1.irc.request
/usr/sbin/tshark -tad -nr $1 $1.irc.response


echo "moving files to investigation dir"
/bin/mv $OUTBOUND/$1.p0f.* $OUTBOUND/$1_DIR/
/bin/mv $OUTBOUND/$1.url $OUTBOUND/$1_DIR/
/bin/mv $OUTBOUND/$1.irc.* $OUTBOUND/$1_DIR/

cat $OUTBOUND/$1_DIR/$1.p0f.final_$SENSORNAME

echo "Moving CAP FILE TO ARCHIVE DIR"
/bin/mv $OUTBOUND/$1 $OUTBOUND/$1_DIR/

