#!/bin/sh
#DATE: 11/03/2018
#THIS IS THE BHMM##
#LOCATION OF CONF FILE#
source /opt/BB/CONF/bb_settings.conf
#BHCOL IP LIST#
#source /opt/BHMM/CONF/bhcolip.list
cd $INBOUND
ext="*.gz"

if stat -t *.gz >/dev/null 2>&1 
then
	#Grab the capture files off each IP in the BHCOLIP list#
	for file in *.gz
	do
	ASSETID=$(echo $file | cut -d'_' -f1)
	echo "Here is the FILENAME: ${file} and ASSETID: ${ASSETID}"
	#echo $ASSETID
	#MOVING CAPTURE FILE TO OUTBOUND DIRECTORY FOR PROCESSING
	/bin/mv $INBOUND/$file $OUTBOUND

	#RUN BLOODHOUND.PY AGAINST CAPTURE#
	/opt/BB/SCRIPTS/bloodhound.py $file $ASSETID
	done
else
	echo "No Files, going back to sleep"
	exit
fi
