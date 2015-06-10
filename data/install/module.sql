/*
	VeryIDE
	Version: 1.7
	Database: module
	Date: 2013/08/20
*/


-- ----------------------------
-- 表单
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}form_form`;
CREATE TABLE `{TableModPre}form_form` (
  `id` int(11) NOT NULL auto_increment,
  `appid` varchar(15) NOT NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `category` varchar(30) default NULL,
  `thumb` varchar(100) default NULL,
  `name` varchar(100) NOT NULL,
  `config` text,
  `description` varchar(1000) default NULL,
  `color` varchar(10) default NULL,
  `tags` varchar(100) default NULL,
  `state` tinyint(4) NOT NULL,
  `skin` varchar(20) default NULL,
  `stat` mediumint(9) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `quote` varchar(100) default NULL,
  `start` int(11) NOT NULL,
  `expire` int(11) NOT NULL,
  `dateline` int(11) NOT NULL,
  `modify` int(11) default NULL,
  `mender` varchar(30) default NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `appid` (`appid`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `category` (`category`),
  KEY `name` (`name`),
  KEY `tags` (`tags`)
) ENGINE=MyISAM;

-- ----------------------------
-- 表单——选项组
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}form_group`;
CREATE TABLE `{TableModPre}form_group` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `name` varchar(100) default NULL,
  `type` varchar(20) default NULL,
  `dateline` int(11) NOT NULL,
  `modify` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `sort` mediumint(9) NOT NULL,
  `config` text,
  `selected` varchar(255) default NULL,
  `description` text,
  `stat` mediumint(9) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fid` (`fid`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `name` (`name`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM;

-- ----------------------------
-- 表单——子选项
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}form_option`;
CREATE TABLE `{TableModPre}form_option` (
  `id` int(11) NOT NULL auto_increment,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `fid` int(11) NOT NULL,
  `gid` int(11) default NULL,
  `name` varchar(200) NOT NULL,
  `dateline` int(11) default NULL,
  `sort` mediumint(9) default NULL,
  `config` text,
  `description` varchar(1000) default NULL,
  `quote` varchar(100) default NULL,
  `image` varchar(100) default NULL,
  `state` tinyint(4) default '1',
  `stat` mediumint(9) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `fid` (`fid`),
  KEY `gid` (`gid`),
  KEY `sort` (`sort`),
  KEY `name` (`name`)
) ENGINE=MyISAM;

-- ----------------------------
-- 表单——用户数据
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}form_data`;
CREATE TABLE `{TableModPre}form_data` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(11) NOT NULL,
  `appid` varchar(15) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(30) default NULL,
  `config` text,
  `state` tinyint(4) NOT NULL,
  `dateline` int(11) NOT NULL,
  `xip` varchar(15) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fid` (`fid`),
  KEY `appid` (`appid`),
  KEY `uid` (`uid`),
  KEY `xip` (`xip`),
  KEY `ip` (`ip`),
  KEY `dateline` (`dateline`),
  KEY `username` (`username`)
) ENGINE=MyISAM;

-- ----------------------------
-- 会员表
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}member`;
CREATE TABLE `{TableModPre}member` (
	`id` int(11) NOT NULL,
	`groupid` int(11) NOT NULL,
	`username` varchar(40) NOT NULL,
	`name` varchar(40) DEFAULT '',
	`gender` tinyint(4) DEFAULT NULL,
	`blood` varchar(5) DEFAULT NULL,
	`birthday` varchar(10) DEFAULT NULL,
	`email` varchar(30) DEFAULT NULL,
	`phone` varchar(15) DEFAULT NULL,
	`qq` varchar(30) DEFAULT NULL,
	`msn` varchar(30) DEFAULT NULL,
	`idcard` varchar(20) DEFAULT NULL,
	`address` varchar(50) DEFAULT NULL,
	`marriage` tinyint(4) DEFAULT NULL,
	`blog` varchar(100) DEFAULT NULL,
	`career` varchar(100) DEFAULT NULL,
	`company` varchar(100) DEFAULT NULL,
	`school` varchar(100) DEFAULT NULL,
	`wechat_openid` varchar(32) DEFAULT NULL,
	`wechat_binded` int(11) NOT NULL,
	`yixin_openid` varchar(32) DEFAULT NULL,
	`yixin_binded` int(11) NOT NULL,
	`sweibo_openid` varchar(32) DEFAULT NULL,
	`sweibo_binded` int(11) NOT NULL,
	`qweibo_openid` varchar(32) DEFAULT NULL,
	`qweibo_binded` int(11) NOT NULL,
	`stat_login` mediumint(9) DEFAULT '0',
	`stat_level` mediumint(9) DEFAULT '0',
	`stat_credit` mediumint(9) DEFAULT '0',
	`config` varchar(255) DEFAULT NULL,
	`verified` tinyint(4) NOT NULL DEFAULT '0',
	`photo` varchar(100) DEFAULT NULL,
	`identity` varchar(100) DEFAULT NULL,
	`dateline` int(11) NOT NULL,
	`modify` int(11) NOT NULL DEFAULT '0',
	`state` tinyint(4) DEFAULT '1',
	`ip` varchar(15) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `groupid` (`groupid`),
	KEY `username` (`username`),
	KEY `name` (`name`),
	KEY `gender` (`gender`),
	KEY `qq` (`qq`),
	KEY `email` (`email`),
	KEY `phone` (`phone`),
	KEY `idcard` (`idcard`),
	KEY `state` (`state`),
	KEY `stat_credit` (`stat_credit`),
	KEY `marriage` (`marriage`),
	KEY `wechat_openid` (`wechat_openid`),
	KEY `yixin_openid` (`yixin_openid`),
	KEY `sweibo_openid` (`sweibo_openid`),
	KEY `qweibo_openid` (`qweibo_openid`),
	KEY `verified` (`verified`)
) ENGINE=MyISAM;

-- ----------------------------
-- 统计分析
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}common_analytic`;
CREATE TABLE `{TableModPre}common_analytic` (
  `id` int(11) NOT NULL auto_increment,
  `appkey` varchar(30) NOT NULL,
  `category` varchar(10) default NULL,
  `date` varchar(10) default NULL,
  `views` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `appkey` (`appkey`),
  KEY `category` (`category`),
  KEY `date` (`date`)
) ENGINE=MyISAM;

-- ----------------------------
-- 消费记录表
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}common_consume`;
CREATE TABLE `{TableModPre}common_consume`(
  `id` int(11) NOT NULL auto_increment,
  `appid` varchar(15) NOT NULL,
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `price` int(11) NOT NULL,
  `final` tinyint(4) NOT NULL,
  `bingo` tinyint(4) NOT NULL,
  `dateline` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `appid` (`appid`),
  KEY `fid` (`fid`),
  KEY `state` (`state`),
  KEY `username` (`username`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM;

-- ----------------------------
-- 用户评论表
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}common_comment`;
CREATE TABLE `{TableModPre}common_comment` (
  `id` int(11) NOT NULL auto_increment,
  `appid` varchar(20) NOT NULL,
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `state` int(11) NOT NULL,
  `rate` tinyint(4) default NULL,
  `event` tinyint(4) default NULL,
  `extra` tinyint(4) default NULL,
  `extrb` tinyint(4) default NULL,
  `extrc` tinyint(4) default NULL,
  `extrd` tinyint(4) default NULL,
  `price` smallint(6) default NULL,
  `tags` varchar(100) default NULL,
  `question` text NOT NULL,
  `site_answer` varchar(255) default NULL,
  `site_dateline` int(11) default NULL,
  `host_answer` varchar(255) default NULL,
  `host_dateline` int(11) default NULL,
  `dateline` int(11) NOT NULL,
  `modify` int(11) default NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `appid` (`appid`),
  KEY `fid` (`fid`),
  KEY `uid` (`uid`),
  KEY `tags` (`tags`),
  KEY `rate` (`rate`),
  KEY `event` (`event`),
  KEY `state` (`state`),
  KEY `modify` (`modify`)
) ENGINE=MyISAM;

-- ----------------------------
-- 内容分类表
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}common_category`;
CREATE TABLE `{TableModPre}common_category` (
  `id` int(11) NOT NULL auto_increment,
  `appid` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mark` varchar(30) default NULL,
  `title` varchar(100) default NULL,
  `keywords` varchar(500) default NULL,
  `description` varchar(1000) default NULL,
  `config` text NOT NULL,
  `mode` tinyint(4) NOT NULL,
  `parent` int(11) NOT NULL default '0',
  `dateline` int(11) NOT NULL,
  `modify` int(11) default NULL,
  `bind` varchar(30) default NULL,
  `ping` text,
  `link` varchar(100) default NULL,
  `skin` varchar(20) default NULL,
  `sort` int(11) default NULL,
  `stat` int(11) default '0',
  `state` tinyint(4) NOT NULL default '0',
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`),
  KEY `name` (`name`),
  KEY `state` (`state`),
  KEY `appid` (`appid`)
) ENGINE=MyISAM;

-- ----------------------------
-- 公共报名表
-- ----------------------------
DROP TABLE IF EXISTS `{TableModPre}common_contact`;
CREATE TABLE `{TableModPre}common_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` varchar(30) NOT NULL,
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `idcard` varchar(20) DEFAULT NULL,
  `name` varchar(30) NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `qq` varchar(20) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `dateline` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `appid` (`appid`),
  KEY `fid` (`fid`),
  KEY `uid` (`uid`),
  KEY `username` (`username`),
  KEY `state` (`state`)
) ENGINE=MyISAM;
