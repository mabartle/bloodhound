#!/usr/bin/python

###GATHERING HTTP_HOST INFORMATION FROM THE TAZER DB####
import sys
import MySQLdb
import pdb

tazer_blacklist = "/opt/BB/SCRIPTS/tazer_blacklist.txt"
outputfiletazerbl = open(tazer_blacklist,"w")
def cleanblacklist(urldnsip):
    #pdb.set_trace()
    wl = open('/opt/BB/SCRIPTS/whitelist.txt','r').read().strip().split('\n')
    for url in urldnsip:
        if url not in wl:
            outputfiletazerbl.write(url+'\n')

tazer_httphost = "/opt/BB/SCRIPTS/tazer_httphost.txt"
outputfiletazerhh = open(tazer_httphost,"w")
try:
        conn = MySQLdb.connect (db = "tazer",
                                host = "127.0.0.1",
                                user = "bh",
                                passwd = "10bh!")
        print "Connected - IT WORKED"
        cursor = conn.cursor()
	cursor.execute ("select http_host from http_host_tbl")
	rows = cursor.fetchall ()
	for row in rows:
		if row == None:
			pass
     		#print row[0]
		outputfiletazerhh.write(row[0]+"\n")
	cursor.close ()
except MySQLdb.Error, e:
        print "Cannot Connect to Server"
	print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)
conn.close()
outputfiletazerhh.flush()
outputfiletazerhh.close()

print "Disconnected"
#sys.exit(0)

###GATHERING IP INFORMATION FROM THE TAZER DB####
import sys
import MySQLdb
tazer_ip = "/opt/BB/SCRIPTS/tazer_ip.txt"
outputfiletazerip = open(tazer_ip,"w")
#pdb.set_trace()
try:
        conn = MySQLdb.connect (db = "tazer",
                                host = "127.0.0.1",
                                user = "bh",
                                passwd = "10bh!")
        print "Connected - IT WORKED"
        cursor = conn.cursor()
        cursor.execute ("select ip from ip_tbl")
        rows = cursor.fetchall ()
        for row in rows:
                if row == None:
                        pass
                #print row[0]
                outputfiletazerip.write(row[0]+"\n")
        cursor.close ()
except MySQLdb.Error, e:
        print "Cannot Connect to Server"
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)
conn.close()
outputfiletazerip.flush()
outputfiletazerip.close()
print "Disconnected"

###GATHERING ARGUS DST IP INFORMATION FROM THE TAZER DB####
import sys
import MySQLdb
tazer_argip = "/opt/BB/SCRIPTS/tazer_argusip.txt"
outputfiletazerargip = open(tazer_argip,"w")
#pdb.set_trace()
try:
        conn = MySQLdb.connect (db = "tazer",
                                host = "127.0.0.1",
                                user = "bh",
                                passwd = "10bh!")
        print "Connected - IT WORKED"
        cursor = conn.cursor()
        cursor.execute ("select DISTINCT(argus_daddr) as count from mal_argus_tbl WHERE (argus_daddr != '239.255.255.250')")
        rows = cursor.fetchall ()
        for row in rows:
                if row == None:
                        pass
                #print row[0]
                outputfiletazerargip.write(row[0]+"\n")
        cursor.close ()
except MySQLdb.Error, e:
        print "Cannot Connect to Server"
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)
conn.close()
outputfiletazerargip.flush()
outputfiletazerargip.close()
print "Disconnected"



###GATHERING IP INFORMATION FROM THE TAZER DB####
import sys
import MySQLdb
tazer_dns = "/opt/BB/SCRIPTS/tazer_dns.txt"
outputfiletazerdns = open(tazer_dns,"w")
#pdb.set_trace()
try:
        conn = MySQLdb.connect (db = "tazer",
                                host = "127.0.0.1",
                                user = "bh",
                                passwd = "10bh!")
        print "Connected - IT WORKED"
        cursor = conn.cursor()
        cursor.execute ("select DISTINCT(dns) from dnsip_tbl")
        rows = cursor.fetchall ()
        for row in rows:
                if row == None:
                        pass
                #print row[0]
		outputfiletazerdns.write(row[0]+"\n")
		cleanblacklist(row)
        cursor.close ()
except MySQLdb.Error, e:
        print "Cannot Connect to Server"
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)
conn.close()
outputfiletazerdns.flush()
outputfiletazerdns.close()
print "Disconnected"
#outputfiletazerbl.flush()
#outputfiletazerbl.close()
