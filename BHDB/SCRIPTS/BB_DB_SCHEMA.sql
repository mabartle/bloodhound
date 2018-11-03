/*
##BH Release 1_0 DB Installation
##Change Notes:
##04-24-2010 - Initial Creation of the BH DB SCHEMA.
##05-03-2010 - Added http_method field for additional URL Data
##07-12-2011 - Adding PARTITIONING SECTION.  also, verified the datatypes against the production db to make sure we didn't miss anything.
*/
/*This is the Database Script for BH DB (OPENSOURCEVersion).
It will do the following:
        - Create the 'BloodHound' DATABASE.
        - Create the 'bh_table' TABLE.
        - Create the 'run_table' TABLE.
 */
/*This creates the Bloodhound DATABASE.*/
CREATE DATABASE IF NOT EXISTS bloodhound;

/*Moves into the tazer database*/
USE bloodhound;
/*This is the malware table.  This table contains ALL fields used in the 'storing' of
 the malware analysis.*/

 CREATE TABLE IF NOT EXISTS run_tbl (
  `capid` int(15) NOT NULL DEFAULT '0',
  `procid` int(15) NOT NULL DEFAULT '0',
  `capstarttime` time NOT NULL DEFAULT '00:00:00',
  `capstartdate` date NOT NULL DEFAULT '2006-06-08',
  `capendtime` time NOT NULL DEFAULT '00:00:00',
  `capenddate` date NOT NULL DEFAULT '2006-06-08',
  `capstartprocesstime` time NOT NULL DEFAULT '00:00:00',
  `capstartprocessdate` date NOT NULL DEFAULT '2006-06-08',
  `capendprocesstime` time NOT NULL DEFAULT '00:00:00',
  `capendprocessdate` date NOT NULL DEFAULT '2006-06-08',
  `capruntime` int(25) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(300) NOT NULL default '',
  PRIMARY KEY (capid,procid)
);

##ADDING PARTITIONING FOR BLOODHOUND V2 - Upgraded MYSQL DB to 5.1 - 4/12/11###
CREATE TABLE IF NOT EXISTS bh_table (
  capid int(11) NOT NULL DEFAULT '0',
  procid int(11) NOT NULL DEFAULT '0',
  eventid int(10) NOT NULL DEFAULT '0',
  stime time NOT NULL default '00:00:00',
  sdate date NOT NULL default '2006-06-08',
  ltime time NOT NULL default '00:00:00',
  ldate date NOT NULL default '2006-06-08',
  dur dec(30,6) unsigned NOT NULL default '0',
  saddr CHAR(25) NOT NULL default '-',
  daddr CHAR(25) NOT NULL default '-',
  proto varchar(15) NOT NULL default '-',
  sport smallint(5) unsigned default '0',
  dport smallint(5) unsigned default '0',
  bytes int(10) unsigned NOT NULL default '0',
  sbytes int(10) unsigned NOT NULL default '0',
  dbytes int(10) unsigned NOT NULL default '0',
  pkts int(10) unsigned NOT NULL default '0',
  spkts int(10) unsigned NOT NULL default '0',
  dpkts int(10) unsigned NOT NULL default '0',
  dir CHAR(15) NOT NULL default '-',
  trans int(10) unsigned NOT NULL default '0',
  seq int(10) unsigned NOT NULL default '0',
  flags CHAR(15) NOT NULL default '-',
  tcp_dport smallint(5) unsigned default '0',
  udp_dport smallint(5) unsigned default '0',
  http_user_agent varchar(300) NOT NULL default '-',
  http_host varchar(300) NOT NULL default '-',
  http_method varchar(300) NOT NULL default '-',
  http_request_uri varchar(3000) NOT NULL default '-',
  dns_request varchar(300) NOT NULL default '-',
  dns_response varchar(500) NOT NULL default '-',
  os_type varchar(300) NOT NULL default '-',
  log_type varchar(300) NOT NULL default '-',
  alert_type varchar(300) NOT NULL default '-',
  class_type varchar(300) NOT NULL default '-',
  sev smallint(5) unsigned default '0')engine=myisam
partition by range (to_days(sdate))
 (PARTITION p0 VALUES LESS THAN (to_days('2018-11-01')),
 PARTITION p1 VALUES LESS THAN (to_days('2018-11-02')),
 PARTITION p2 VALUES LESS THAN (to_days('2018-11-03')),
 PARTITION p3 VALUES LESS THAN (to_days('2018-11-04')),
 PARTITION p4 VALUES LESS THAN (to_days('2018-11-05')),
 PARTITION p5 VALUES LESS THAN (to_days('2018-11-06')),
 PARTITION p6 VALUES LESS THAN (to_days('2018-11-07')),
 PARTITION p7 VALUES LESS THAN (to_days('2018-11-08')),
 PARTITION p8 VALUES LESS THAN (to_days('2018-11-09')),
 PARTITION p9 VALUES LESS THAN (to_days('2018-11-10')),
 PARTITION p10 VALUES LESS THAN (to_days('2018-11-11')),
 PARTITION p11 VALUES LESS THAN (to_days('2018-11-12')),
 PARTITION p12 VALUES LESS THAN (to_days('2018-11-13')),
 PARTITION p13 VALUES LESS THAN (to_days('2018-11-14')),
 PARTITION p14 VALUES LESS THAN (to_days('2018-11-15')),
 PARTITION p15 VALUES LESS THAN (to_days('2018-11-16')),
 PARTITION p16 VALUES LESS THAN (to_days('2018-11-17')),
 PARTITION p17 VALUES LESS THAN (to_days('2018-11-18')),
 PARTITION p18 VALUES LESS THAN (to_days('2018-11-19')),
 PARTITION p19 VALUES LESS THAN (to_days('2018-11-20')),
 PARTITION p20 VALUES LESS THAN (to_days('2018-11-21')),
 PARTITION p21 VALUES LESS THAN (to_days('2018-11-22')),
 PARTITION p22 VALUES LESS THAN (to_days('2018-11-23')),
 PARTITION p23 VALUES LESS THAN (to_days('2018-11-24')));

/*GRANT PERMISSIONS FOR USERS*/
GRANT SELECT, UPDATE, INSERT ON bloodhound.* TO 'bh'@'localhost' IDENTIFIED BY '10bh!';
/*GRANT SELECT on tazer.* to 'tazer'@'192.168.50.135' IDENTIFIED BY '08tazer!';*/


