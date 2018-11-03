#!/bin/bash
#DATE: 11/03/2018
#THIS IS THE BH SERVERS WAY OF PULLING FILES FROM THE BHMM BOX##
#LOCATION OF CONF FILE#
source /opt/BB/CONF/bb_settings.conf
#BHCOL IP LIST#
source /opt/BB/CONF/bhmmip.list

#Grab the capture files off each IP in the BHCOLIP list#
for bhmmhost in $BHMMIPS
do
echo "------------------Transfering Files for $bhmmhost--------------"
/usr/bin/scp $BHMMUSER@$bhmmhost:$BHMM_INBOUND/*.gz $INBOUND
done

#Now remove the capture files from the remote system.
for bhmmhost in $BHMMIPS
do
echo "------------------Moving Files for $bhmmhost to ARCHIVE--------------"
/usr/bin/ssh $BHMMUSER@$bhmmhost 'mv '$BHMM_INBOUND'/*.gz '$BHMM_INBOUND_ARCHIVE''
done

