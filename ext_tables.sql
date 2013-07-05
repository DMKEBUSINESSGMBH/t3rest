
#
# Table structure for table 'tx_t3sportstats_players'
# Statistic data of player per match
#
CREATE TABLE tx_t3rest_providers (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	restkey varchar(55) DEFAULT '' NOT NULL,
	classname varchar(255) DEFAULT '' NOT NULL,
	config text,

	PRIMARY KEY (uid),
	KEY parent (restkey,hidden,deleted)
);

CREATE TABLE tx_t3rest_accesslog (
	uid int(11) NOT NULL auto_increment,
	tstamp datetime DEFAULT '0000-00-00 00:00:00',
	ip varchar(40) DEFAULT '' NOT NULL,
	os varchar(10) DEFAULT '' NOT NULL,
	system varchar(40) DEFAULT '' NOT NULL,
	sysver varchar(40) DEFAULT '' NOT NULL,
	version varchar(40) DEFAULT '' NOT NULL,
	app varchar(40) DEFAULT '' NOT NULL,
	runtime float(7,4) DEFAULT '0.0000' NOT NULL,
	host varchar(255) DEFAULT '' NOT NULL,
	method varchar(10) DEFAULT '' NOT NULL,
	uri mediumtext,
	status smallint(6) DEFAULT '0' NOT NULL,
	useragent varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY idx (version,app),
	KEY idx_dateos (tstamp,os)
);

#
# Table for mkforms session cache
#
CREATE TABLE tx_t3rest_cache (
	id int(11) NOT NULL auto_increment,
	identifier varchar(128) DEFAULT '' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	content longblob NOT NULL,
	lifetime int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (id),
	KEY cache_id (identifier)
);

#
# Unused dummy table for TYPO3 caching framework
#
CREATE TABLE tx_t3rest_tags (
	id int(11) NOT NULL auto_increment,
	identifier varchar(128) DEFAULT '' NOT NULL,
	tag varchar(128) DEFAULT '' NOT NULL,

	PRIMARY KEY (id),
	KEY cache_id (identifier),
	KEY cache_tag (tag)
);
