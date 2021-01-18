#!/usr/bin/python

import sys,os,time,base64,time
import pdb #for debugging
from time import sleep
#pdb.set_trace()

##Blacklist is home grown from tazer and other projects  test ###
##SL = Suspect List - This list is home grown.  Using IP addresses seen in investigations as suspect hosts###
##ZT = Zeustrackerlist - https://zeustracker.abuse.ch/blocklist.php?download=domainblocklist###
##DL = Domain List - http://www.malwaredomains.com/files/domains.txt then parsed for 1 column###
##CL = Conficker List - http://lists.malwarepatrol.net/conficker/conficker_plain.txt###
def CheckBlackList(data):
    bl = open('/opt/BB/SCRIPTS/tazer_blacklist.txt','r').read().strip().split('\n')
    wl = open('/opt/BB/SCRIPTS/whitelist.txt','r').read().strip().split('\n')
    zt = open('/opt/BB/SCRIPTS/zeustracklist.txt','r').read().strip().split('\n')
    dl = open('/opt/BB/SCRIPTS/domainlist.txt','r').read().strip().split('\n')
    cl = open('/opt/BB/SCRIPTS/confik.txt','r').read().strip().split('\n')
    sl = open('/opt/BB/SCRIPTS/suspect.txt','r').read().strip().split('\n')
    if data in bl:
        status = "BLACKLIST"
    elif data in wl:
        status = "WHITELIST"
    elif data in zt:
        status = "ZEUSLIST"
    elif data in dl:
        status = "DOMAINLIST"
    elif data in cl:
        status = "CONFICKLIST"
##    elif data in sl:
##        status = "SUSPECTLIST"
    else:
        status = "NONE"
    return status
def cleanblacklist():
    wl = open('/opt/BB/SCRIPTS/whitelist.txt','r').read().strip().split('\n')
    bl = open('/opt/BB/SCRIPTS/blacklist.txt','r').read().strip().split('\n')
    updatedblacklist = []
 
    for url in bl:
        if url not in wl:
            updatedblacklist.append(url)
    return updatedblacklist



PROJECT = "BB"
SCRIPTOWNER = "bloodhound"
SCRIPTGROUP = "bloodhound"
MAIN_PATH = "/opt/"+PROJECT
LOGDIR = MAIN_PATH+"/LOGS"
DOCS = MAIN_PATH+"/DOCUMENTATION"
SCRIPTS = MAIN_PATH+"/SCRIPTS"
TEMPDIR = SCRIPTS+"/TMP"
QUEUE = MAIN_PATH+"/QUEUE/"
QUEUE_PRI = QUEUE+"PRIORITY/"
QUEUE_STD = QUEUE+"STANDARD/"
INBOUND = MAIN_PATH+"/INBOUND/"
OUTBOUND = MAIN_PATH+"/OUTBOUND"
COMPLETE = MAIN_PATH+"/COMPLETE/"
INSTALL = "/opt/"+PROJECT+"/INSTALL"
SENSORNAME = "9999"
####Add variables for DB settings - 07/19/11###
bhdbhost="127.0.0.1"
bhdbuser="bh"
bhdbpw="10bh!"
bhdbname="bloodhound"


try:
    cli = sys.argv[0:]
    fileName = cli[1]
    capID = cli[2]
except:
    print("Usage: bloodhound.py <CAPTUREFILENAME> <CAPID>")
    sys.exit()

capStartProcessDate = time.strftime("%Y/%m/%d")
capStartProcessTime = time.strftime("%H:%M:%S")
capProcID = time.strftime("%m%d%H%M%S")
print "Here is the Capture Processing Date/Time = " +capStartProcessDate
print "Here is the Capture Processing Time = " +capStartProcessTime
print "Here is the CapProcID = " +capProcID
print "Creating Analysis Directory"
ANALYSISDIR = OUTBOUND+"/"+fileName+"_DIR"
os.mkdir(ANALYSISDIR, 755)

print "Cleaning CAP File"
capturefile = OUTBOUND+"/"+fileName
cleancapturefile = ANALYSISDIR+"/"+fileName+".ccap"
editcapcommand = "/usr/sbin/editcap "+capturefile+" "+cleancapturefile
os.system(editcapcommand)


bloodhoundoutfile = ANALYSISDIR+"/"+fileName+".bh"
bhoutputfile = open(bloodhoundoutfile,"w")

print "p0f'ing it UP!!!"
#pdb.set_trace()
p0foutfile = ANALYSISDIR+"/"+fileName+".p0f.out"
outputfile = open(p0foutfile,"w")
p0foutfile2 = ANALYSISDIR+"/"+fileName+".p0f.out2"
outputfile2 = open(p0foutfile2,"w")
fileType = "P0FLOG"
p0fcommand = "/usr/sbin/p0f -qUNlts "+cleancapturefile
p0foutput = os.popen(p0fcommand).read().strip().split("\n")
#os.system(p0fcommand)
#Adding for empty files 03/14/11 - mab
outputlenth = len(p0foutput)
#print outputlenth
if outputlenth <= 1:
	print "No p0f Data from this Capture"
else:
    for row in p0foutput:
#	pdb.set_trace()
        row = row.split("-")
        modDate = row[0]
        p0fMonth = row[0].split()[1]
        p0fDay = row[0].split()[2]
        p0fYear = row[0].split()[4]
        modYear = p0fYear.rstrip('>')
        p0fdate = p0fMonth+p0fDay+modYear
        modDate = time.strptime(p0fdate,"%b%d%Y")
        modDate = time.strftime("%m/%d/%Y",modDate)
        p0fTime = row[0].split()[3]
        p0fip = row[0].split()[5]
        modp0fip = p0fip.split(":")[0]
        p0fOS = row[1]
        p0fOS = p0fOS.replace(',',';')
        results = fileType, capID, modDate,p0fTime,modp0fip,p0fOS
        results = ','.join(results)
        results2 = fileType, capID, capProcID, modDate,p0fTime,modp0fip,p0fOS
        results2 = ','.join(results2)
        outputfile2.write(results2+"\n")
        outputfile.write(results+"\n")
        bhoutputfile.write(results+"\n")

outputfile.flush()
outputfile.close()
outputfile2.flush()
outputfile2.close()

####SQL PORTION TO INSERT p0f DATA INTO DB###
####NEED TO CONVERT ALL MYSQL INTO A FUNCTION####
import sys
import MySQLdb
#pdb.set_trace()
try:
        conn = MySQLdb.connect ( host = bhdbhost, user = bhdbuser, passwd = bhdbpw, db = bhdbname)
        print "Connected - Loading p0f DATA"
        cursor = conn.cursor()
        query = "LOAD DATA LOCAL INFILE '"+p0foutfile2+"' INTO TABLE bh_table FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (log_type, capid, procid, @sdate, stime, saddr, os_type) SET sdate = STR_TO_DATE(@sdate, '%m/%d/%Y')"
        cursor.execute( query )
        conn.commit()


except MySQLdb.Error, e:
        print "Cannot Connect to Server"
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)

conn.close()
print "Disconnected"
#sys.exit(0)


print "ARGUS it UP!!!"
argusoutfile = ANALYSISDIR+"/"+fileName+".argout"
#arguscommand = "/usr/local/sbin/argus -e "+capID+" -F "+SCRIPTS+"/argus.conf -r "+cleancapturefile+" -w "+argusoutfile
arguscommand = "/usr/local/sbin/argus -e "+capID+" -F "+SCRIPTS+"/argus.conf -r "+cleancapturefile+" -w "+argusoutfile+" - ip"
os.system(arguscommand)


"""
This portion of the script will generate the ARGUS RU file.  This file is HUMAN READABLE AND IS parsed and inputted into the DB (DB stuff has not been added yet).
"""

print "ARGUS - RA STUFF"
#pdb.set_trace()
argusRUoutfile = ANALYSISDIR+"/"+fileName+".argru"
outputfile = open(argusRUoutfile,"w")
argusRUoutfile2 = ANALYSISDIR+"/"+fileName+".argru2"
outputfile2 = open(argusRUoutfile2,"w")
fileType = "ARGUSLOG"
argusRAcommand = "/usr/local/bin/ra -F "+SCRIPTS+"/excel.rarc -r "+argusoutfile+" - not arp and not man"
argusRUoutput = os.popen(argusRAcommand).read().strip().split("\n")
#os.system(p0fcommand)
#Getting the Capture Start/End Date/Time
capstarttime = argusRUoutput[0].split(",")[2]
capstartdate = argusRUoutput[0].split(",")[3]
capenddate =  argusRUoutput[len(argusRUoutput)-1].split(",")[3]
capendtime = argusRUoutput[len(argusRUoutput)-1].split(",")[2]


for row in argusRUoutput:
        results = fileType, row
        results = ','.join(results)
        results2 = fileType, capProcID, row
        results2 = ','.join(results2)
        outputfile.write(results+"\n")
        outputfile2.write(results2+"\n")
        bhoutputfile.write(results+"\n")

outputfile.flush()
outputfile.close()
outputfile2.flush()
outputfile2.close()

####SQL PORTION TO INSERT ARGUS DATA INTO DB###
####NEED TO CONVERT ALL MYSQL INTO A FUNCTION####
import sys
import MySQLdb
#pdb.set_trace()
try:
        conn = MySQLdb.connect ( host = bhdbhost, user = bhdbuser, passwd = bhdbpw, db = bhdbname)
        #conn = MySQLdb.connect ( host = "192.168.50.92", user = "bh", passwd = "10bh!", db = "bloodhound")
        print "Connected - Loading ARGUS DATA"
        cursor = conn.cursor()
        query = "LOAD DATA LOCAL INFILE '"+argusRUoutfile2+"' INTO TABLE  bh_table FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (log_type, procid, capid, sdate, stime, ldate, ltime, dur, saddr, daddr, proto, @sport, @dport, bytes, sbytes, dbytes, pkts, spkts, dpkts, dir, trans, seq, flags) SET sport = NULLIF(@sport,''), dport = NULLIF(@dport,'')"
        cursor.execute( query )
        conn.commit()


except MySQLdb.Error, e:
        print "Cannot Connect to Server"
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)

conn.close()
print "Disconnected"
#sys.exit(0)


"""
URL DATA PORTION
"""

urloutfile = ANALYSISDIR+"/"+fileName+".urls"
outputfile = open(urloutfile,"w")
outputfile2 = ANALYSISDIR+"/"+fileName+".urls2"
urloutfile2 = open(outputfile2,"w")
fileType = "URLLOG"
urlcommand = "/usr/sbin/tshark -t ad -e frame.time -e ip.src_host -e ip.dst_host -e ip.proto -e tcp.dstport -e udp.dstport -e http.user_agent -e http.request.method -e http.host -e http.request.uri -E separator='#' -T fields -nr "+cleancapturefile+" http.request"
urloutput = os.popen(urlcommand).read().strip().split("\n")
outputlenth = len(urloutput)
#print outputlenth
if outputlenth <= 1:
	print "No URL Data from this Capture"
else:
    for row in urloutput:
#	pdb.set_trace()
        row = row.split("#")
        modDate = row[0]
        urlMonth = row[0].split()[0]
        urlDay = row[0].split()[1]
        urlYear = row[0].split()[2]
        urlDate = urlMonth+urlDay+urlYear
        modDate = time.strptime(urlDate,"%b%d, %Y")
        modDate = time.strftime("%m/%d/%Y",modDate)
        urlTime = row[0].split()[3]
        modTime = urlTime.split(".")[0]
        saddr = row[1]
        daddr = row[2]
        proto = row[3]
        tcp_port = row[4]
        udp_port = row[5]
        url_user_agent2 = row[6]
        url_user_agent2 = url_user_agent2.replace(',',';')
        url_user_agent = base64.b64encode(row[6])
        url_request_method = row[7]
        #pdb.set_trace()
        url_http_host = row[8]
        status = CheckBlackList(url_http_host)
        url_http_host2 = row[8]
        url_http_host2 = url_http_host2.replace(',',';')
        url_http_uri2 = row[9]
        url_http_uri2 = url_http_uri2.replace(',',';')
        url_http_host = base64.b64encode(row[8])
        url_http_uri = base64.b64encode(row[9])
        results2 = fileType, capID, capProcID, modDate,modTime,saddr,daddr,proto,tcp_port, udp_port, url_user_agent2, url_request_method, url_http_host2, url_http_uri2, status
        results2 = ','.join(results2)
        results = fileType, capID, modDate,modTime,saddr,daddr,proto,tcp_port, udp_port, url_user_agent, url_request_method, url_http_host, url_http_uri
        results = ','.join(results)
        urloutfile2.write(results2+"\n")
        outputfile.write(results+"\n")
        bhoutputfile.write(results+"\n")
urloutfile2.flush()
urloutfile2.close()
outputfile.flush()
outputfile.close()
#pdb.set_trace()

####SQL PORTION TO INSERT URL DATA INTO DB###
####NEED TO CONVERT ALL MYSQL INTO A FUNCTION####
import sys
import MySQLdb

try:
        conn = MySQLdb.connect ( host = bhdbhost, user = bhdbuser, passwd = bhdbpw, db = bhdbname)
        #conn = MySQLdb.connect ( host = "192.168.50.92", user = "bh", passwd = "10bh!", db = "bloodhound")
        print "Connected - Loading URL DATA"
        cursor = conn.cursor()
        query = "LOAD DATA LOCAL INFILE '"+outputfile2+"' INTO TABLE  bh_table FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (log_type, capid, procid, @sdate, stime, saddr, daddr, proto, @tcp_dport, @udp_dport, http_user_agent, http_method, http_host, http_request_uri,alert_type) SET sdate = STR_TO_DATE(@sdate, '%m/%d/%Y'), tcp_dport = NULLIF(@tcp_dport,''), udp_dport = NULLIF(@udp_dport,'')"
        cursor.execute( query )
        conn.commit()


except MySQLdb.Error, e:
        print "Cannot Connect to Server"
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)

conn.close()
print "Disconnected"
#sys.exit(0)



"""
DNS DATA PORTION
"""

dnsoutfile = ANALYSISDIR+"/"+fileName+".dns"
outputfile = open(dnsoutfile,"w")
outputfile2 = ANALYSISDIR+"/"+fileName+".dns2"
dnsoutfile2 = open(outputfile2,"w")
fileType = "DNSLOG"
dnscommand = "/usr/sbin/tshark -o 'column.format:Time,%Cus:frame.time,delimiter,%Cus:ip.dst_host,delimiter,%Cus:ip.src_host,delimiter,%Cus:DS,DNS Query Name,%Cus:dns.qry.name,delimiter,%Cus:DS,Info,%i' -r "+cleancapturefile+" dns.flags.rcode != 66666"
#/usr/sbin/tshark -o 'column.format:Time,%Cus:frame.time,delimiter,%Cus:DS,Client,%d,delimiter,%Cus:DS,DNS Query Name,%Cus:dns.qry.name,delimiter,%Cus:DS,Info,%i' -r "+cleancapturefile+" dns.flags.rcode != 66666"
dnsoutput = os.popen(dnscommand).read().strip().split("\n")
outputlenth = len(dnsoutput)
#print outputlenth
if outputlenth <= 1:
	print "No DNS Data from this Capture"
else:
    for row in dnsoutput:
            #pdb.set_trace()
            dnsMonth = row.split()[0]
            dnsDay = row.split()[1]
            dnsYear = row.split()[2]
            dnsDate = dnsMonth+dnsDay+dnsYear
            modDate = time.strptime(dnsDate,"%b%d, %Y")
            modDate = time.strftime("%m/%d/%Y",modDate)
            dnsTime = row.split()[3]
            modTime = dnsTime.split(".")[0]
            dnssaddr = row.split()[4]
            dnsdaddr = row.split()[5]
            dnsrequest = row.split()[6]
            status = CheckBlackList(dnsrequest)
            dnsreply = row.split()[7:]
            dnsreply = ' '.join(dnsreply)
            dnsreply = dnsreply.replace(',',';')
            results = fileType, capID, modDate,modTime,dnssaddr,dnsdaddr,dnsrequest,dnsreply
            results = ','.join(results)
            results2 = fileType, capID, capProcID, modDate,modTime,dnssaddr,dnsdaddr,dnsrequest,dnsreply,status
            results2 = ','.join(results2)
            outputfile.write(results+"\n")
            dnsoutfile2.write(results2+"\n")
            bhoutputfile.write(results+"\n")
#        print results

outputfile.flush()
outputfile.close()
dnsoutfile2.flush()
dnsoutfile2.close()

####SQL PORTION TO INSERT DNS DATA INTO DB###
####NEED TO CONVERT ALL MYSQL INTO A FUNCTION####
import sys
import MySQLdb

try:
        #conn = MySQLdb.connect ( host = "192.168.50.92", user = "bh", passwd = "10bh!", db = "bloodhound")
        conn = MySQLdb.connect ( host = bhdbhost, user = bhdbuser, passwd = bhdbpw, db = bhdbname)
        print "Connected - Loading DNS Data"
        cursor = conn.cursor()
        query = "LOAD DATA LOCAL INFILE '"+outputfile2+"' INTO TABLE  bh_table FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (log_type, capid, procid, @sdate, stime, saddr, daddr, dns_request, dns_response, alert_type) SET sdate = STR_TO_DATE(@sdate, '%m/%d/%Y')"
        cursor.execute( query )
        conn.commit()


except MySQLdb.Error, e:
        print "Cannot Connect to Server"
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)

conn.close()
print "Disconnected"
#sys.exit(0)

"""
DNS DATA PORTION
"""

snortoutfile = ANALYSISDIR+"/"+fileName+".snort"
outputfile = open(snortoutfile,"w")
outputfile2 = ANALYSISDIR+"/"+fileName+".snort2"
snortoutfile2 = open(outputfile2,"w")
fileType = "SNORTLOG"
snortcommand = "/usr/sbin/snort -A console -c /etc/snort/snort.conf -yNq -r "+cleancapturefile
snortoutput = os.popen(snortcommand).read().strip().split("\n")
outputlenth = len(snortoutput)
#print outputlenth
if outputlenth <= 1:
	print "No SNORT Data from this Capture"
else:
    for row in snortoutput:
            #print "SNORT DATA"
            #pdb.set_trace()
            snortDate = row.split("-")[0]
            modDate = time.strptime(snortDate,"%m/%d/%y")
            modDate = time.strftime("%m/%d/%Y",modDate)
            snortTime = row.split("-")[1].split(".")[0]
            snortSID = row.split()[2].split(":")[1]
            snortAlertName = row.split("]")[2].split("[")[0].strip()
            snortClassName = row.split("]")[3].split(":")[1].strip()
            snortPriority = row.split("]")[4].split(":")[1].strip()
            snortProtocol = row.split("{")[1].split("}")[0].strip()
            if snortProtocol == "UDP" or snortProtocol == "TCP":
                #print "UDP OR TCP"
                snortSrcIP = row.rsplit(":",2)[0].rsplit(" ",1)[1]
                snortSrcPort = row.rsplit(":",2)[1].split()[0]
                snortDstIP = row.rsplit(":",1)[0].rsplit(" ",1)[1]
                snortDstPort = row.rsplit(":",1)[1].split()[0]
            elif snortProtocol == "ICMP":
                #print "ICMP"
                snortSrcIP = row.rsplit(" ",2)[0].rsplit(" ",1)[1]
                snortDstIP = row.split()[-1]
                snortSrcPort = "n/a"
                snortDstPort = "n/a"
            else:
                #print "OTHER"
                snortSrcIP = "n/a"
                snortDstIP = "n/a"
                snortSrcPort = "n/a"
                snortDstPort = "n/a"
            results = fileType, capID, modDate,snortTime,snortSrcIP,snortSrcPort,snortDstIP,snortDstPort,snortProtocol,snortSID,snortAlertName,snortClassName,snortPriority
            #results = ','.join(results)
            #02-23-11 - Change on delimter for better integration with Vigilance.
            results = '##~##'.join(results)
            results2 = fileType, capID, capProcID, modDate,snortTime,snortSrcIP,snortSrcPort,snortDstIP,snortDstPort,snortProtocol,snortSID,snortAlertName,snortClassName,snortPriority
            #results2 = ','.join(results2)
            #02-23-11 - Change on delimiter for better integration with Vigilance.
            results2 = '##~##'.join(results2)
            outputfile.write(results+"\n")
            snortoutfile2.write(results2+"\n")
            bhoutputfile.write(results+"\n")
#        print results

outputfile.flush()
outputfile.close()
snortoutfile2.flush()
snortoutfile2.close()

#TEST
####SQL PORTION TO INSERT SNORT DATA INTO DB###
####NEED TO CONVERT ALL MYSQL INTO A FUNCTION####
import sys
import MySQLdb

try:
        conn = MySQLdb.connect ( host = bhdbhost, user = bhdbuser, passwd = bhdbpw, db = bhdbname)
        #conn = MySQLdb.connect ( host = "192.168.50.92", user = "bh", passwd = "10bh!", db = "bloodhound")
        print "Connected - Loading Snort Data"
        cursor = conn.cursor()
        #query = "LOAD DATA INFILE '"+outputfile2+"' INTO TABLE  bh_table FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (log_type, capid, procid, @sdate, stime, saddr, @sport, daddr, @dport, proto, eventid, alert_type, class_type, sev) SET sdate = STR_TO_DATE(@sdate, '%m/%d/%Y'), sport = NULLIF(@sport,'n/a'), dport = NULLIF(@dport,'n/a')"
        #Change for Delimiter problem.
        query = "LOAD DATA LOCAL INFILE '"+outputfile2+"' INTO TABLE  bh_table FIELDS TERMINATED BY '##~##' LINES TERMINATED BY '\n' (log_type, capid, procid, @sdate, stime, saddr, @sport, daddr, @dport, proto, eventid, alert_type, class_type, sev) SET sdate = STR_TO_DATE(@sdate, '%m/%d/%Y'), sport = NULLIF(@sport,'n/a'), dport = NULLIF(@dport,'n/a')"
        cursor.execute( query )
        conn.commit()


except MySQLdb.Error, e:
        print "Cannot Connect to Server"
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)

conn.close()
print "Disconnected"
#sys.exit(0)




bhoutputfile.flush()
bhoutputfile.close()

capEndProcessDate = time.strftime("%Y/%m/%d")
capEndProcessTime = time.strftime("%H:%M:%S")
print "Here is the Capture End Processing Date = " +capEndProcessDate
print "Here is the Capture End Processing Time = " +capEndProcessTime

#pdb.set_trace()
####SQL PORTION TO INSERT Run Time DATA INTO DB###
####NEED TO CONVERT ALL MYSQL INTO A FUNCTION####
import sys
import MySQLdb
#pdb.set_trace()
try:
        conn = MySQLdb.connect ( host = bhdbhost, user = bhdbuser, passwd = bhdbpw, db = bhdbname)
        #conn = MySQLdb.connect ( host = "192.168.50.92", user = "bh", passwd = "10bh!", db = "bloodhound")
#        pdb.set_trace()
        print "Connected - Entering the RUN TIME VALUES"
        cursor = conn.cursor()
        #query = "INSERT INTO run_tbl (capid, capstarttime, capstartdate, capendtime, capenddate, capstartprocesstime, capstartprocessdate, capendprocesstime, capendprocessdate, filename) VALUES('"+capID"','"+capstarttime"','"+capstartdate"','"+capendtime"','"+capenddate"','"+capStartProcessTime"','"+capStartProcessDate"','"+capEndProcessTime"','"+capEndProcessDate"','"+fileName"')"
        query = "INSERT INTO run_tbl (capid, procid, capstarttime, capstartdate, capendtime, capenddate, capstartprocessdate, capstartprocesstime, capendprocessdate, capendprocesstime, filename) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')"%(capID, capProcID, capstarttime, capstartdate, capendtime, capenddate, capStartProcessDate, capStartProcessTime,capEndProcessDate, capEndProcessTime, fileName )
        cursor.execute( query )        
        conn.commit()
#pdb.set_trace()

except MySQLdb.Error, e:
        print "Cannot Connect to Server"
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)

conn.close()
print "Disconnected"
#sys.exit(0)

####MOVING PCAP FILE TO DIRECTCRY###
import shutil
print "Moving PCAP to ANALYSISDIR"
#shutil.move(fileName, ANALYSISDIR)
shutil.move(capturefile, ANALYSISDIR)

###NFSCOPY SECTION will COPY the .BH FILE
###TO THE NFSDIR IF THE NFSMOVE VARIABLE IS SET TO YES###
if NFSMOVE == "yes":
	print "MOVING BH FILE TO NFSMOUNT"
        import shutil
        shutil.copy(bloodhoundoutfile, NFSDIR)
else:
        print "NO MOVE NECESSARY FOR NFSMOUNT"

"""
Add Settings to main script.

source /opt/BB/CONF/bb_settings.conf
source /opt/BB/SCRIPTS/bh_mysqlimport.sh
"""
