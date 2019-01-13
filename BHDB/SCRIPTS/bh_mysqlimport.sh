#! /bin/bash
. /opt/BB/CONF/bb_settings.conf

####ARGUS INSERT FUNCTION###
####################################################################################################
func_argusdbinsert() {
mysql --host=$bh_db_ipaddr --user=$bh_db_username --password=$bh_pword --database=bloodhound -e "LOAD DATA LOCAL INFILE '$argusfile'
    INTO TABLE argus
    FIELDS TERMINATED BY ','
    LINES TERMINATED BY '\n'
    (capid, sdate, stime, ldate, ltime, dur, saddr, daddr, proto, sport, dport, bytes, sbytes, dbytes, pkts, spkts, dpkts, dir, trans, seq, flags);"
}

####ARGUS INSERT FUNCTION###
####################################################################################################
func_argusdbinsert2() {
mysql --host=$bh_db_ipaddr --user=$bh_db_username --password=$bh_pword --database=bloodhound -e "LOAD DATA LOCAL INFILE '$argusfile'
    INTO TABLE bh_table
    FIELDS TERMINATED BY ','
    LINES TERMINATED BY '\n'
    (capid, sdate, stime, ldate, ltime, dur, saddr, daddr, proto, sport, dport, bytes, sbytes, dbytes, pkts, spkts, dpkts, dir, trans, seq, flags)
	SET log_type = '$logdatatype';
;"
}

####URL INSERT FUNCTION###
####################################################################################################
func_urldbinsert() {
mysql --host=$bh_db_ipaddr --user=$bh_db_username --password=$bh_pword --database=bloodhound -e "LOAD DATA LOCAL INFILE '$urlfile'
    INTO TABLE url
    FIELDS TERMINATED BY '##~##'
    LINES TERMINATED BY '\n'
    (@sdate, stime, saddr, daddr, proto, tcp_dport, udp_dport, http_user_agent, http_host, http_request_uri)
    SET sdate = STR_TO_DATE(@sdate,'%M %d,%Y'),
        capid = $capid;
;"
}

####URL INSERT FUNCTION###
####################################################################################################
func_urldbinsert2() {
mysql --host=$bh_db_ipaddr --user=$bh_db_username --password=$bh_pword --database=bloodhound -e "LOAD DATA LOCAL INFILE '$urlfile'
    INTO TABLE bh_table
    FIELDS TERMINATED BY '##~##'
    LINES TERMINATED BY '\n'
    (@sdate, stime, saddr, daddr, proto, tcp_dport, udp_dport, http_user_agent, http_host, http_request_uri)
    SET sdate = STR_TO_DATE(@sdate,'%M %d,%Y'),
        capid = $capid,
	log_type = '$logdatatype';
;"
}



####DNS INSERT FUNCTION###
####################################################################################################
func_dnsdbinsert() {
mysql --host=$bh_db_ipaddr --user=$bh_db_username --password=$bh_pword --database=bloodhound -e "LOAD DATA LOCAL INFILE '$dnsfile'
    INTO TABLE dns
    FIELDS TERMINATED BY '##~##'
    LINES TERMINATED BY '\n'
    (@sdate, stime, saddr, dns_request, dns_response)
    SET sdate = STR_TO_DATE(@sdate,'%M %d,%Y'),
        capid = $capid;
;"
}

####DNS INSERT FUNCTION###
####################################################################################################
func_dnsdbinsert2() {
mysql --host=$bh_db_ipaddr --user=$bh_db_username --password=$bh_pword --database=bloodhound -e "LOAD DATA LOCAL INFILE '$dnsfile'
    INTO TABLE bh_table
    FIELDS TERMINATED BY '##~##'
    LINES TERMINATED BY '\n'
    (@sdate, stime, saddr, dns_request, dns_response)
    SET sdate = STR_TO_DATE(@sdate,'%M %d,%Y'),
        capid = $capid,
	log_type = '$logdatatype';
;"
}


####DNS INSERT FUNCTION###
####################################################################################################
func_runtableinsert() {
#capid=$1
echo 'Inserting Run Table '
        mysql -h $bh_db_ipaddr -u $bh_db_username -p$bh_pword -D bloodhound -e "INSERT into run_tbl (capid, capstarttime, capstartdate, capendtime, capenddate, capstartprocesstime, capstartprocessdate, capendprocesstime, capendprocessdate, capruntime, filename) values ('$capid', '$capstarttime', '$capstartdate', '$capendtime', '$capenddate', '$capstartprocesstime', '$capstartprocessdate', '$capendprocesstime', '$capstartprocessdate',0,'$filename')"
}

