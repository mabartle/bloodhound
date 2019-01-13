#!/bin/bash
#DATE: 11/03/2018
#THIS IS THE BHMM##
#LOCATION OF CONF FILE#
source /opt/BHMM/CONF/bhmm.conf
#BHCOL IP LIST#
source /opt/BHMM/CONF/bhcolip.list

#Grab the capture files off each IP in the BHCOLIP list#
for bhcolhost in $BHCOLIPS
do
echo "------------------Transfering Files for $bhcolhost--------------"
/usr/bin/scp -i /home/bloodhound/.ssh/id_rsa.bloodhound -P $BHCOLPORT $BHCOLUSER@$bhcolhost:$TRANSPORTOUTBOUND/*.gz $BHMM_INBOUND
#/usr/bin/scp -P $BHCOLPORT $BHCOLUSER@$bhcolhost:$TRANSPORTOUTBOUND/*.gz $BHMM_INBOUND
done

#Now remove the capture files from the remote system.
for bhcolhost in $BHCOLIPS
do
echo "------------------Removing Files for $bhcolhost___--------------"
/usr/bin/ssh -i /home/bloodhound/.ssh/id_rsa.bloodhound -p $BHCOLPORT $BHCOLUSER@$bhcolhost 'mv '$TRANSPORTOUTBOUND'/*.gz '$TRANSPORTOBARCHIVE''
#/usr/bin/ssh -p $BHCOLPORT $BHCOLUSER@$bhcolhost 'mv '$TRANSPORTOUTBOUND'/*.gz '$TRANSPORTOBARCHIVE''
done
