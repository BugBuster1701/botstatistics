-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_botstatistics_counter`
-- 

CREATE TABLE `tl_botstatistics_counter` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `bid` int(10) unsigned NOT NULL default '0',
  `bot_date` date NOT NULL default '1999-01-01',
  `bot_name` varchar(60) NOT NULL default 'Unknown',
  `bot_counter` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `bid_date_name` (`bid`, `bot_date`, `bot_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
 
-- 
-- Table `tl_botstatistics_counter_details`
-- 
 
CREATE TABLE `tl_botstatistics_counter_details` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `bot_page_alias` varchar(255) NOT NULL default 'Unknown',
  `bot_page_alias_counter` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pid_alias` (`pid`, `bot_page_alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
    

-- --------------------------------------------------------

-- 
-- Table `tl_botstatistics_blocker`
-- 

CREATE TABLE `tl_botstatistics_blocker` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `bid` int(10) unsigned NOT NULL default '0',
  `bot_tstamp` timestamp NULL default NULL,
  `bot_ip` varchar(40) NOT NULL default '0.0.0.0',
  PRIMARY KEY  (`id`),
  KEY `bid` (`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `botstatistics_name` varchar(64) NOT NULL default '',
  `botstatistics_details` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;  

