
-- ----------------------------
-- 统计
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{TableModPre}go_list` (
  `id` int(11) NOT NULL auto_increment,
  `aid` int(11) default NULL,
  `account` varchar(30) NOT NULL,
  `category` varchar(30) default NULL,
  `name` varchar(100) NOT NULL,
  `method` varchar(10) NOT NULL,
  `link` varchar(255) default NULL,
  `dateline` int(11) NOT NULL,
  `modify` int(11) NOT NULL,
  `mender` varchar(30) default NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `category` (`category`),
  KEY `name` (`name`),
  KEY `method` (`method`),
  KEY `link` (`link`)
) ENGINE=MyISAM;


-- ----------------------------
-- 分析
-- ----------------------------

CREATE TABLE IF NOT EXISTS `{TableModPre}go_click` (
  `app` varchar(30) NOT NULL,
  `ver` varchar(30) NOT NULL,
  `index` int(11) NOT NULL,
  `cate` varchar(10) DEFAULT NULL,
  `date` varchar(10) DEFAULT NULL,
  `click` int(11) NOT NULL,
  KEY `app` (`app`),
  KEY `ver` (`ver`),
  KEY `index` (`index`),
  KEY `cate` (`cate`),
  KEY `date` (`date`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `{TableModPre}go_stat` (
  `hash` varchar(16) NOT NULL,
  `app` varchar(30) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `link` varchar(255) NOT NULL,
  `click` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `final` int(11) NOT NULL,
  PRIMARY KEY (`hash`),
  KEY `app` (`app`),
  KEY `hash` (`hash`),
  KEY `click` (`click`),
  KEY `start` (`start`),
  KEY `final` (`final`)
) ENGINE=MyISAM;


-- ----------------------------
-- QQ号码
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{TableModPre}go_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qq` varchar(15) DEFAULT NULL,
  `nickname` varchar(30) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `headface` varchar(100) NOT NULL,
  `origin` varchar(30) DEFAULT NULL,
  `keyword` varchar(50) NOT NULL,
  `visits` smallint(11) NOT NULL,
  `clicks` smallint(11) NOT NULL,
  `dateline` int(11) NOT NULL,
  `lifetime` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `qq`(qq),
  INDEX `uid`(uid),
  INDEX `username`(username),
  INDEX `origin`(origin),
  INDEX `keyword`(keyword),
  INDEX `visits`(visits),
  INDEX `dateline`(dateline),
  INDEX `state`(state)
) ENGINE=MyISAM;