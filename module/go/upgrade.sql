#[version=1.5]
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
#[/version]