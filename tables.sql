#
# Table structure for table `groups`
#

CREATE TABLE `groups` (
  `groupname` varchar(32) NOT NULL default '',
  `gid` smallint(6) unsigned NOT NULL auto_increment,
  `members` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`gid`),
  UNIQUE KEY `groupname` (`groupname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='ProFTPd group table';

#
# Table structure for table `users`
#

CREATE TABLE `users` (
  `id` smallint(2) unsigned NOT NULL auto_increment,
  `userid` varchar(32) NOT NULL default '',
  `uid` smallint(6) unsigned default NULL,
  `gid` smallint(6) unsigned default NULL,
  `passwd` varchar(265) NOT NULL default '',
  `homedir` varchar(255) NOT NULL default '',
  `comment` varchar(255) NOT NULL default '',
  `disabled` smallint(2) unsigned NOT NULL default '0',
  `shell` varchar(32) NOT NULL default '/sbin/nologin',
  `email` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `title` varchar(5) NOT NULL default '',
  `company` varchar(255) NOT NULL default '',
  `bytes_in_used` bigint(20) unsigned NOT NULL default '0',
  `bytes_out_used` bigint(20) unsigned NOT NULL default '0',
  `files_in_used` bigint(20) unsigned NOT NULL default '0',
  `files_out_used` bigint(20) unsigned NOT NULL default '0',
  `login_count` int(11) unsigned NOT NULL default '0',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='ProFTPd user table';

#
# Table structure for table `quotalimits`
#

CREATE TABLE `quotalimits` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(30) DEFAULT NULL,
  `quota_type` enum('user','group','class','all') NOT NULL,
  `per_session` enum('false','true') NOT NULL,
  `limit_type` enum('soft','hard') NOT NULL,
  `bytes_in_avail` float NOT NULL,
  `bytes_out_avail` float NOT NULL,
  `bytes_xfer_avail` float NOT NULL,
  `files_in_avail` int(10) UNSIGNED NOT NULL,
  `files_out_avail` int(10) UNSIGNED NOT NULL,
  `files_xfer_avail` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

#
# Table structure for table `quotatallies`
#

CREATE TABLE `quotatallies` (
  `name` varchar(30) NOT NULL,
  `quota_type` enum('user','group','class','all') NOT NULL,
  `bytes_in_used` float NOT NULL,
  `bytes_out_used` float NOT NULL,
  `bytes_xfer_used` float NOT NULL,
  `files_in_used` int(10) UNSIGNED NOT NULL,
  `files_out_used` int(10) UNSIGNED NOT NULL,
  `files_xfer_used` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
