#
# Table structure for table 'tx_t3rest_providers'
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
    classname varchar(255) DEFAULT '' NOT NULL,
    fe_group varchar(100) DEFAULT '0' NOT NULL,
    config text,

    PRIMARY KEY (uid)
);

#
# Table for t3rest session cache
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
