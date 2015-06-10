/*
	VeryIDE
	Version: 1.6
	Database: system
	Date: 2013/08/20
*/

-- ----------------------------
-- 即时消息
-- ----------------------------
DROP TABLE IF EXISTS `{TableSysPre}message`;
CREATE TABLE `{TableSysPre}message` (
  `id` int(11) NOT NULL auto_increment,
  `fromid` int(11) NOT NULL,
  `toid` int(11) default NULL,
  `message` tinytext,
  `ip` varchar(15) NOT NULL,
  `client` varchar(10) default NULL,
  `read` tinyint(4) default NULL,
  `dateline` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fromid` (`fromid`),
  KEY `toid` (`toid`),
  KEY `read` (`read`)
) ENGINE=MyISAM;

-- ----------------------------
-- 内部公告
-- ----------------------------
DROP TABLE IF EXISTS `{TableSysPre}notice`;
CREATE TABLE `{TableSysPre}notice` (
  `id` int(11) NOT NULL auto_increment,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `gid` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text,
  `dateline` int(11) NOT NULL,
  `modify` int(11) default NULL,
  `ip` varchar(15) NOT NULL,
  `state` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `gid` (`gid`),
  KEY `title` (`title`)
) ENGINE=MyISAM;

-- ----------------------------
-- 应用收藏
-- ----------------------------
DROP TABLE IF EXISTS `{TableSysPre}quick`;
CREATE TABLE `{TableSysPre}quick` (
  `aid` int(11) NOT NULL,
  `appid` varchar(30) NOT NULL,
  `sort` tinyint(4) NOT NULL,
  `dateline` int(11) NOT NULL,
  KEY `aid` (`aid`),
  KEY `appid` (`appid`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM;

-- ----------------------------
-- 插件收藏
-- ----------------------------
DROP TABLE IF EXISTS `{TableSysPre}widget`;
CREATE TABLE `{TableSysPre}widget` (
  `aid` int(11) NOT NULL,
  `appid` varchar(30) NOT NULL,
  `widget` varchar(30) NOT NULL,
  `x` varchar(10) NOT NULL,
  `y` varchar(10) NOT NULL,
  `z` smallint(4) NOT NULL,
  `fx` smallint(6) NOT NULL,
  `fy` smallint(6) NOT NULL,
  `dateline` int(11) NOT NULL,
  KEY `aid` (`aid`),
  KEY `appid` (`appid`)
) ENGINE=MyISAM;

-- ----------------------------
-- 附件表
-- ----------------------------
DROP TABLE IF EXISTS `{TableSysPre}attach`;
CREATE TABLE `{TableSysPre}attach` (
  `id` int(11) NOT NULL auto_increment,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `name` varchar(100) default NULL,
  `type` varchar(10) default NULL,
  `size` int(11) default NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `input` varchar(20) default NULL,
  `remote` tinyint(4) NOT NULL,
  `dateline` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `name` (`name`),
  KEY `type` (`type`),
  KEY `width` (`width`),
  KEY `height` (`height`),
  KEY `input` (`input`),
  KEY `remote` (`remote`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM;

-- ----------------------------
-- 用户组
-- ----------------------------
DROP TABLE IF EXISTS `{TableSysPre}group`;
CREATE TABLE `{TableSysPre}group` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `medal` varchar(30) default NULL,
  `config` text,
  `description` varchar(1000) default NULL,
  `module` text NOT NULL,
  `widget` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `dateline` int(11) NOT NULL,
  `modify` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `aid` (`aid`),
  KEY `account` (`account`)
) ENGINE=MyISAM;

-- ----------------------------
-- 系统日志
-- ----------------------------
DROP TABLE IF EXISTS `{TableSysPre}event`;
CREATE TABLE `{TableSysPre}event` (
  `id` int(11) NOT NULL auto_increment,
  `aid` int(11) NOT NULL default '0',
  `account` varchar(30) default NULL,
  `event` varchar(50) NOT NULL,
  `description` varchar(255) default NULL,
  `dateline` int(11) NOT NULL default '0',
  `modify` int(11) NOT NULL default '0',
  `quote` varchar(50) default NULL,
  `rank` tinyint(4) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `event` (`event`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM;

-- ----------------------------
-- 管理员
-- ----------------------------
DROP TABLE IF EXISTS `{TableSysPre}admin`;
CREATE TABLE `{TableSysPre}admin` (
  `id` int(11) NOT NULL auto_increment,
  `gid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` char(6) NOT NULL default '',
  `gender` tinyint(4) NOT NULL,
  `avatar` varchar(100) default NULL,
  `email` varchar(20) default NULL,
  `birthday` varchar(10) default NULL,
  `question` tinyint(4) NOT NULL,
  `answer` varchar(50) NOT NULL,
  `qq` varchar(20) default NULL,
  `blog` varchar(100) default NULL,
  `phone` varchar(20) default NULL,
  `theme` varchar(20) default NULL,
  `extra` varchar(255) NOT NULL default '{}',
  `stat_login` mediumint(9) NOT NULL default '0',
  `stat_modify` mediumint(9) NOT NULL default '0',
  `last_login` int(11) NOT NULL,
  `last_active` int(11) NOT NULL default '0',
  `last_ip` varchar(15) default NULL,
  `dateline` int(11) NOT NULL,
  `modify` int(11) NOT NULL,
  `config` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `gid` (`gid`),
  KEY `account` (`account`),
  KEY `password` (`password`)
) ENGINE=MyISAM;

-- ----------------------------
-- 词语过滤
-- ----------------------------
DROP TABLE IF EXISTS `{TableSysPre}word`;
CREATE TABLE `{TableSysPre}word` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL,
  `account` varchar(15) NOT NULL DEFAULT '',
  `find` varchar(255) NOT NULL DEFAULT '',
  `replacement` varchar(255) NOT NULL DEFAULT '',
  `extra` varchar(255) NOT NULL DEFAULT '',
  `type` smallint(6) NOT NULL DEFAULT '1',
  `dateline` int(11) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `find` (`find`),
  KEY `type` (`type`)
) ENGINE=MyISAM;


-- ----------------------------
-- 初始分组
-- ----------------------------
INSERT INTO `{TableSysPre}group`(aid,account,name,medal,config,dateline,state,ip) VALUES ('1','{TableAdmin}','超级管理员', 'bronze_1.png', '{"*":"*"}',{TableTime}, '1', '{TableIP}');
INSERT INTO `{TableSysPre}group`(aid,account,name,medal,config,dateline,state,ip) VALUES ('1','{TableAdmin}','管理员', 'bronze_2.png', '{"*":"*"}',{TableTime}, '1', '{TableIP}');
INSERT INTO `{TableSysPre}group`(aid,account,name,medal,config,dateline,state,ip) VALUES ('1','{TableAdmin}','广告组', 'silver_1.png', '{"*":"*"}',{TableTime}, '1', '{TableIP}');
INSERT INTO `{TableSysPre}group`(aid,account,name,medal,config,dateline,state,ip) VALUES ('1','{TableAdmin}','编辑组', 'silver_1.png', '{"*":"*"}',{TableTime}, '1', '{TableIP}');

-- ----------------------------
-- 初始用户 admin:veryide
-- ----------------------------
INSERT INTO `{TableSysPre}admin`(gid,account,password,salt,avatar,blog,stat_login,last_ip,theme,dateline,modify,state,ip) VALUES ('1', '{TableAdmin}', md5( concat('{TablePass}','{TableSalt}') ), '{TableSalt}', '{TableBase}static/image/face.jpg', 'http://www.veryide.com/', '1', '{TableIP}', 'flower', {TableTime},{TableTime}, '1', '{TableIP}');


-- ----------------------------
-- 初始用户设置
-- ----------------------------
INSERT INTO `{TableSysPre}widget` VALUES ('1', 'system', 'control', '100', '200', '0', '0', '0', {TableTime});

-- ----------------------------
-- 初始词语过滤
-- ----------------------------
INSERT INTO `{TableSysPre}word` VALUES ('1', '0', 'System', '64式销售QQ', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('2', '0', 'System', '92式销售QQ', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('3', '0', 'System', 'C4炸药', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('4', '0', 'System', 'CFX气枪销售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('5', '0', 'System', 'QU365.COM', '恶意网站', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('6', '0', 'System', 'rdtx', '恶意网站', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('7', '0', 'System', 'register.asp?netmsky', 'xxx', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('8', '0', 'System', 'sdo.com', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('9', '0', 'System', 'sf一条龙', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('10', '0', 'System', 'shop34664999', '**', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('12', '0', 'System', 'sosocg.com', '（非法信息）', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('13', '0', 'System', 'sxyy.net.ru', '非法信息', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('14', '0', 'System', 'TNT炸弹', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('18', '0', 'System', 'ud999.com', '非法信息', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('19', '0', 'System', 'url-Go', '非法信息', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('20', '0', 'System', 'vipse8.cn', '病毒网站', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('25', '0', 'System', 'zj78', '诈骗网站', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('26', '0', 'System', 'zjone.cn', '恶意网站', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('27', '0', 'System', 'zjons', '色情网站', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('28', '0', 'System', 'zjwzxg.com', '**', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('29', '0', 'System', '“代开', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('30', '0', 'System', '“转让”“发票”', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('32', '0', 'System', '阿兵哥言语录', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('33', '0', 'System', '阿樱的悄悄话', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('34', '0', 'System', '爱上师娘的床', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('35', '0', 'System', '爱神之传奇', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('36', '0', 'System', '白小姐 特码', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('37', '0', 'System', '邦达调查事务所', '厕所', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('38', '0', 'System', '北京餐饮定额发票', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('39', '0', 'System', '北京代开增值税发票', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('40', '0', 'System', '北京代理发票', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('41', '0', 'System', '北京股民资料', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('42', '0', 'System', '北逃', '**', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('43', '0', 'System', '边锋', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('44', '0', 'System', '变性晓薇', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('45', '0', 'System', '冰毒 QQ', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('46', '0', 'System', '冰毒 货到付款', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('47', '0', 'System', '沧澜曲', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('48', '0', 'System', '长期出售股民', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('49', '0', 'System', '尘诱2', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('50', '0', 'System', '陈光诚', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('51', '0', 'System', '宠霸美男', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('52', '0', 'System', '出 售 假 币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('53', '0', 'System', '出售 \"瓦斯手狗\"', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('54', '0', 'System', '出售 king粉', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('55', '0', 'System', '出售 K粉', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('56', '0', 'System', '出售 冰毒', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('57', '0', 'System', '出售 甲基苯丙胺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('58', '0', 'System', '出售 假币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('59', '0', 'System', '出售 麻古', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('60', '0', 'System', '出售 脱氧麻黄素', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('61', '0', 'System', '出售 盐酸羟亚胺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('62', '0', 'System', '出售 硬币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('63', '0', 'System', '出售 致幻剂', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('64', '0', 'System', '出售4.5MM狗粮', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('65', '0', 'System', '出售54式', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('66', '0', 'System', '出售56式军刺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('67', '0', 'System', '出售64式', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('68', '0', 'System', '出售77式', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('69', '0', 'System', '出售92式', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('70', '0', 'System', '出售AK军刺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('71', '0', 'System', '出售BB弹', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('72', '0', 'System', '出售BB枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('73', '0', 'System', '出售CP99', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('74', '0', 'System', '出售GLOCK', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('75', '0', 'System', '出售M1911', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('76', '0', 'System', '出售MT-虎牙', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('77', '0', 'System', '出售PPK', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('78', '0', 'System', '出售匕首', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('79', '0', 'System', '出售勃朗宁军刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('80', '0', 'System', '出售步枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('81', '0', 'System', '出售刺刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('82', '0', 'System', '出售弹簧刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('83', '0', 'System', '出售电狗', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('84', '0', 'System', '出售短信群发猫', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('85', '0', 'System', '出售短信群发器', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('86', '0', 'System', '出售娥眉气枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('87', '0', 'System', '出售防身武器', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('88', '0', 'System', '出售仿真军刺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('89', '0', 'System', '出售仿真枪模', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('90', '0', 'System', '出售仿真手狗', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('91', '0', 'System', '出售高仿假钱', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('92', '0', 'System', '出售高仿假人民币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('93', '0', 'System', '出售高仿人民币假人民币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('94', '0', 'System', '出售高仿真假币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('95', '0', 'System', '出售高仿真人民币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('96', '0', 'System', '出售高假仿人民币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('97', '0', 'System', '出售戈博军刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('98', '0', 'System', '出售各式热兵器', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('99', '0', 'System', '出售工字气枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('100', '0', 'System', '出售弓弩', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('101', '0', 'System', '出售股民电话', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('102', '0', 'System', '出售股民联系方式', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('103', '0', 'System', '出售股民名单', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('104', '0', 'System', '出售股民信息', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('105', '0', 'System', '出售股民资料', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('106', '0', 'System', '出售股民资源', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('107', '0', 'System', '出售广州三箭气枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('108', '0', 'System', '出售蝴蝶刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('109', '0', 'System', '出售蝴蝶跳刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('110', '0', 'System', '出售虎头双管', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('111', '0', 'System', '出售虎牙军刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('112', '0', 'System', '出售户外砍刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('113', '0', 'System', '出售火药动力钢珠枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('114', '0', 'System', '出售假 人民币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('115', '0', 'System', '出售假币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('116', '0', 'System', '出售假币 货到付款', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('117', '0', 'System', '出售假币QQ', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('118', '0', 'System', '出售假币当面交易', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('119', '0', 'System', '出售假币电话', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('120', '0', 'System', '出售假币货到付款', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('121', '0', 'System', '出售假币货到付款或当面交易', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('122', '0', 'System', '出售假币联系电话', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('123', '0', 'System', '出售假币联系方式电话', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('124', '0', 'System', '出售假钞QQ', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('125', '0', 'System', '出售假钞银行卡', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('126', '0', 'System', '出售假钱', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('127', '0', 'System', '出售假人民币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('128', '0', 'System', '出售健卫小口径步枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('129', '0', 'System', '出售金钟气枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('130', '0', 'System', '出售精仿军刃', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('131', '0', 'System', '出售精仿三棱军刺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('132', '0', 'System', '出售精品军刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('133', '0', 'System', '出售军刺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('134', '0', 'System', '出售军刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('135', '0', 'System', '出售军品', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('136', '0', 'System', '出售军品刺刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('137', '0', 'System', '出售军用手狗', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('138', '0', 'System', '出售军用五连发手枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('139', '0', 'System', '出售开刃军刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('140', '0', 'System', '出售开山刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('141', '0', 'System', '出售雷明登猎狗', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('142', '0', 'System', '出售猎木仓', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('143', '0', 'System', '出售猛虎军刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('144', '0', 'System', '出售蒙汗药', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('145', '0', 'System', '出售迷昏药假币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('146', '0', 'System', '出售气步枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('147', '0', 'System', '出售气木仓', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('148', '0', 'System', '出售气手枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('149', '0', 'System', '出售铅弹气枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('150', '0', 'System', '出售枪具', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('151', '0', 'System', '出售窃听器', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('152', '0', 'System', '出售曲马多', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('153', '0', 'System', '出售热武器', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('154', '0', 'System', '出售三棱刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('155', '0', 'System', '出售三棱军刺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('156', '0', 'System', '出售三唑仑', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('157', '0', 'System', '出售圣甲虫跳刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('158', '0', 'System', '出售手拉鸡', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('159', '0', 'System', '出售手木仓', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('160', '0', 'System', '出售兽用麻醉枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('161', '0', 'System', '出售甩棍', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('162', '0', 'System', '出售跳刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('163', '0', 'System', '出售微型手枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('164', '0', 'System', '出售伪钞QQ', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('165', '0', 'System', '出售温切斯特', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('166', '0', 'System', '出售新股民信息', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('167', '0', 'System', '出售盐酸曲马多', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('168', '0', 'System', '出售野战求生刀具', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('169', '0', 'System', '出售一元假硬币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('170', '0', 'System', '出售一元硬币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('171', '0', 'System', '出售硬币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('172', '0', 'System', '出售战刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('173', '0', 'System', '出售纸币货到付款', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('174', '0', 'System', '出售指纹膜', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('175', '0', 'System', '出售指纹膜制作方法', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('176', '0', 'System', '出售指纹套 代打卡', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('177', '0', 'System', '出售自制手枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('178', '0', 'System', '出售左轮手枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('179', '0', 'System', '创世之子猎艳之旅', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('180', '0', 'System', '打倒共产党', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('181', '0', 'System', '大黑鹰弓弩专卖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('182', '0', 'System', '代开 住宿 发 票', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('183', '0', 'System', '代开餐饮发票', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('184', '0', 'System', '代开发票', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('185', '0', 'System', '代开增值税发票', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('186', '0', 'System', '代開各类票据', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('187', '0', 'System', '低价销售[假币][假钞]', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('188', '0', 'System', '短信群发', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('189', '0', 'System', '对准桃源洞口', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('190', '0', 'System', '发 票代开', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('191', '0', 'System', '发票保真', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('192', '0', 'System', '发票代 开', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('193', '0', 'System', '发票代开', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('194', '0', 'System', '法轮功', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('195', '0', 'System', '反共', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('196', '0', 'System', '反屏蔽无线耳机', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('197', '0', 'System', '封疆大吏', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('198', '0', 'System', '高压气枪专卖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('199', '0', 'System', '跟踪定位器', '有病毒', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('200', '0', 'System', '弓弩批发', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('201', '0', 'System', '弓弩直销', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('202', '0', 'System', '弓弩专卖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('203', '0', 'System', '供应盐酸羟亚胺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('204', '0', 'System', '共匪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('205', '0', 'System', '购蒙汗药', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('206', '0', 'System', '官战', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('207', '0', 'System', '杭州证书办理公司', '**', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('208', '0', 'System', '核弹制作方法', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('209', '0', 'System', '贺国强', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('210', '0', 'System', '黑车交易', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('211', '0', 'System', '黑道狂神', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('212', '0', 'System', '黑火药配方', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('213', '0', 'System', '黄学敏', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('214', '0', 'System', '贾庆林', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('215', '0', 'System', '假币出售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('216', '0', 'System', '假币出售QQ', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('217', '0', 'System', '假币出售电话', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('218', '0', 'System', '假币货到付款联系电话', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('219', '0', 'System', '假币银行卡', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('220', '0', 'System', '假钞出售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('221', '0', 'System', '假钞人民币假人民币出售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('222', '0', 'System', '尖 嘴', 'XXX', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('223', '0', 'System', '尖嘴', 'XXX', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('224', '0', 'System', '简易原子弹教程', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('225', '0', 'System', '健尔马', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('226', '0', 'System', '进口弓弩专卖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('227', '0', 'System', '进口汽狗销售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('228', '0', 'System', '静坐', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('229', '0', 'System', '居安宝', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('230', '0', 'System', '绝色家族', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('231', '0', 'System', '军用匕首出售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('232', '0', 'System', '军用弓弩专卖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('233', '0', 'System', '军用手枪出售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('234', '0', 'System', '考试答案', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('235', '0', 'System', '考试试题', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('236', '0', 'System', '考试用耳机', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('237', '0', 'System', '考试用窃听器', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('238', '0', 'System', '考试专用 窃听器', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('239', '0', 'System', '刻章办证发票', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('240', '0', 'System', '兰博军刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('241', '0', 'System', '老虎机', '非法信息', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('242', '0', 'System', '李长春', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('243', '0', 'System', '李克强', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('244', '0', 'System', '刘晓波', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('245', '0', 'System', '摩洛客成人网址', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('246', '0', 'System', '魔刀丽影', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('247', '0', 'System', '茉 莉', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('248', '0', 'System', '茉 莉 花', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('249', '0', 'System', '茉莉', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('250', '0', 'System', '男女激情淫乱', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('251', '0', 'System', '男人本“色”之欲望校园', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('254', '0', 'System', '起爆药制作', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('255', '0', 'System', '气枪铅弹销售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('256', '0', 'System', '钱云会', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('257', '0', 'System', '窃听器出售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('258', '0', 'System', '秦青的幸福生活', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('259', '0', 'System', '秦氏弓弩专卖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('260', '0', 'System', '求购三唑仑片', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('261', '0', 'System', '全国出售高仿真假', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('262', '0', 'System', '燃烧弹制作', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('263', '0', 'System', '燃烧瓶制作', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('264', '0', 'System', '三利达弓弩专卖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('265', '0', 'System', '三唑仑', '（非法信息）', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('266', '0', 'System', '三唑仑迷魂药', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('267', '0', 'System', '三唑仑片价格', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('268', '0', 'System', '色老汉', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('269', '0', 'System', '色情六月天', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('270', '0', 'System', '色站导航', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('271', '0', 'System', '涩爱', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('272', '0', 'System', '森林之狼弓弩专卖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('273', '0', 'System', '删帖公司', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('274', '0', 'System', '商翼', '千客', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('275', '0', 'System', '上海股民资料', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('276', '0', 'System', '上海民生证券投资', '恶意网站', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('277', '0', 'System', '深圳代开 开发发票', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('278', '0', 'System', '十景缎', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('279', '0', 'System', '十年孤剑沧海盟', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('280', '0', 'System', '视频裸聊', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('281', '0', 'System', '手机短信群发器', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('282', '0', 'System', '售高仿真/人民币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('283', '0', 'System', '售隐形耳机', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('284', '0', 'System', '售针孔摄像头', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('285', '0', 'System', '甩棍 杀破狼', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('286', '0', 'System', '睡着的武神', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('287', '0', 'System', '塑胶炸药制作', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('288', '0', 'System', '太阳城', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('289', '0', 'System', '唐僧和粉尘女子的性爱生活', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('290', '0', 'System', '逃北', '**', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('291', '0', 'System', '套弄花心', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('292', '0', 'System', '特码 六合彩', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('293', '0', 'System', '跳刀 弹簧刀', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('294', '0', 'System', '偷听器', '（非法信息）', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('295', '0', 'System', '偷聼器', '病毒', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('296', '0', 'System', '秃鹰汽枪货到付款', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('297', '0', 'System', '推广员', '**', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('298', '0', 'System', '网游之亵渎', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('299', '0', 'System', '伪共', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('300', '0', 'System', '尾行', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('302', '0', 'System', '温州鹿城娱乐', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('303', '0', 'System', '温州鹊桥交友', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('304', '0', 'System', '乌坎', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('305', '0', 'System', '吴邦国', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('306', '0', 'System', '习惯性风流', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('307', '0', 'System', '习近平', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('308', '0', 'System', '香港开奖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('309', '0', 'System', '香港六合/彩公司 特码', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('310', '0', 'System', '销售54式64式', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('311', '0', 'System', '销售工字牌汽枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('312', '0', 'System', '销售弓弩', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('313', '0', 'System', '销售股民资料', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('314', '0', 'System', '销售虎头猎枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('315', '0', 'System', '销售假币', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('316', '0', 'System', '销售狙击枪', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('317', '0', 'System', '销售盐酸羟亚胺', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('318', '0', 'System', '修真之我是神偷', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('319', '0', 'System', '盐酸羟亚胺 货到付款', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('320', '0', 'System', '叶哲', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('321', '0', 'System', '一手股民资料', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('322', '0', 'System', '异世之风流大法师', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('323', '0', 'System', '淫网', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('324', '0', 'System', '隐形作弊耳机', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('325', '0', 'System', '英语四六级', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('326', '0', 'System', '游行', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('327', '0', 'System', '原子弹DIY教程', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('328', '0', 'System', '原子弹配方', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('329', '0', 'System', '原子弹制作手册', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('330', '0', 'System', '曾道人 六合彩公司', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('331', '0', 'System', '曾道人 特码', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('332', '0', 'System', '炸药装置制作', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('333', '0', 'System', '赵氏弓弩专卖', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('334', '0', 'System', '赵速平', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('335', '0', 'System', '照日天劫', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('336', '0', 'System', '指纹代打卡', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('337', '0', 'System', '指纹膜 代打卡', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('338', '0', 'System', '指纹膜出售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('339', '0', 'System', '指纹膜制作方法', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('340', '0', 'System', '指纹套 代打卡', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('341', '0', 'System', '指纹套出售', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('342', '0', 'System', '指纹套制作方法', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('343', '0', 'System', '制作硝化甘油', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('344', '0', 'System', '专卖枪械模型', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('345', '0', 'System', '专卖手狗', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('346', '0', 'System', '自制迷魂药', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('347', '0', 'System', '自制炸药方法', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('348', '0', 'System', '左手上天堂右手下地狱', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('349', '0', 'System', '作弊器', '{MOD}', '', '1', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('350', '0', 'System', 'M1911', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('351', '0', 'System', 'M24', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('352', '0', 'System', 'M4配件', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('353', '0', 'System', 'M700', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('354', '0', 'System', 'M9/M92', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('355', '0', 'System', 'marui', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('356', '0', 'System', 'MP-654K', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('357', '0', 'System', 'MP5', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('358', '0', 'System', 'PPK', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('359', '0', 'System', 'R17', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('360', '0', 'System', 'R27', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('361', '0', 'System', 'R28', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('362', '0', 'System', 'R45', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('363', '0', 'System', 'SVD', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('364', '0', 'System', 'USP', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('365', '0', 'System', 'YIKA9式', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('366', '0', 'System', '波箱', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('367', '0', 'System', '长狗', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('368', '0', 'System', '电狗', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('369', '0', 'System', '电鸡', '{MOD}', '', '7', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('370', '0', 'System', '短狗', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('371', '0', 'System', '仿长枪', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('372', '0', 'System', '狗粮', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('373', '0', 'System', '金弓', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('374', '0', 'System', '金身', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('375', '0', 'System', '气狗', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('376', '0', 'System', '沙漠之鹰', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('377', '0', 'System', '手狗', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('378', '0', 'System', '手拉鸡', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('379', '0', 'System', '雄鹰', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('380', '0', 'System', '81年蒙古族学生运动', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('386', '0', 'System', '“7&#8226;5”惨剧与东突厥斯坦', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('387', '0', 'System', '“东伊运”向“世维会”发出“警告”视频声明', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('388', '0', 'System', '“圣战”太比力克宣讲4', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('389', '0', 'System', '安拉', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('390', '0', 'System', '草原英雄哈达', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('391', '0', 'System', '对东突穆斯林同胞们的告诫', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('392', '0', 'System', '感谢安拉', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('393', '0', 'System', '岗仁波切开发', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('394', '0', 'System', '高玉莲', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('395', '0', 'System', '高玉莲女士', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('396', '0', 'System', '给土耳其穆斯林的劝诫', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('397', '0', 'System', '黑暗资料', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('398', '0', 'System', '胡琴呼', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('399', '0', 'System', '吉哈德', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('400', '0', 'System', '进藏游客', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('401', '0', 'System', '进藏游客拍照', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('402', '0', 'System', '就中国政府公布第三批恐怖嫌疑人名单谣言声明', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('403', '0', 'System', '狙击手训练教程', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('404', '0', 'System', '抗议中国政府民族暴虐政策的示威游行', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('405', '0', 'System', '恐怖分子手册', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('406', '0', 'System', '拉嘎运动', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('407', '0', 'System', '拉萨公安局', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('408', '0', 'System', '拉萨贡嘎机场', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('409', '0', 'System', '拉萨火车站', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('410', '0', 'System', '拉萨严控', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('411', '0', 'System', '喇嘛证', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('412', '0', 'System', '蒙藏文化中心', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('413', '0', 'System', '蒙古人权保卫同盟', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('414', '0', 'System', '蒙古异见人士', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('415', '0', 'System', '蒙古自由联盟党', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('416', '0', 'System', '民族英雄哈达', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('417', '0', 'System', '内蒙古独立', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('418', '0', 'System', '内蒙古人民党', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('419', '0', 'System', '内蒙古人民革命党', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('420', '0', 'System', '内蒙古人权保护同盟', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('421', '0', 'System', '内蒙古人权保卫同盟', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('422', '0', 'System', '南蒙古', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('423', '0', 'System', '南蒙古独立', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('424', '0', 'System', '南蒙古过渡政府', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('425', '0', 'System', '南蒙古民主', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('426', '0', 'System', '南蒙古民主力量', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('427', '0', 'System', '南蒙古人权', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('428', '0', 'System', '南蒙古人权信息中心', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('429', '0', 'System', '南蒙古人权信息中信', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('430', '0', 'System', '南蒙古自由', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('431', '0', 'System', '南蒙古自由和人权', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('432', '0', 'System', '南蒙古自由民主运动', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('433', '0', 'System', '皮山', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('434', '0', 'System', '七五期间', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('435', '0', 'System', '情爱的祖国东突', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('436', '0', 'System', '热爱天堂的人', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('437', '0', 'System', '热爱天堂的人6', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('438', '0', 'System', '瑞典蒙古委员会', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('439', '0', 'System', '三证俱全', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('440', '0', 'System', '杀人手册', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('441', '0', 'System', '圣战安拉', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('442', '0', 'System', '圣战体会', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('443', '0', 'System', '圣战勇士们', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('444', '0', 'System', '什么是群众伊吉拉特与吉哈德', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('445', '0', 'System', '双语教育', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('446', '0', 'System', '损坏建筑', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('447', '0', 'System', '突厥斯坦的好男儿们——让我们投身到救世之路吧！', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('448', '0', 'System', '团体和单独‘圣战’的定义', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('449', '0', 'System', '为了真主永远坚持', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('450', '0', 'System', '维吾尔人失踪', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('451', '0', 'System', '我们辉煌的呼罗珊', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('452', '0', 'System', '五证俱全', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('453', '0', 'System', '席海明', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('454', '0', 'System', '信徒之旅公告', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('455', '0', 'System', '信徒之旅之二', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('456', '0', 'System', '信徒之旅之三', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('457', '0', 'System', '信徒之旅之四', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('458', '0', 'System', '信徒之旅之五', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('459', '0', 'System', '羊湖开发游艇', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('460', '0', 'System', '伊吉拉特', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('461', '0', 'System', '伊吉拉特感想', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('462', '0', 'System', '伊扎布特', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('463', '0', 'System', '异见人士哈达', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('464', '0', 'System', '支持南蒙古委员会', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('465', '0', 'System', '致高加索圣战者的一封信', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('466', '0', 'System', '致圣战战士', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('467', '0', 'System', '周边藏区', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('468', '0', 'System', '自由蒙古', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('469', '0', 'System', '自由南蒙古', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('470', '0', 'System', '宗教和生命', '{MOD}', '', '14', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('471', '0', 'System', '0577home.net', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('472', '0', 'System', '18057782299', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('473', '0', 'System', '18606666577', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('474', '0', 'System', '57996616', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('475', '0', 'System', 'dt818.com', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('476', '0', 'System', 'http://www.duanwenxue.com/jingdian/ganwu/', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('477', '0', 'System', 'http://www.jj59.com/', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('478', '0', 'System', 'http://www.tg263.com/', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('479', '0', 'System', 'sns', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('480', '0', 'System', 'SNS军团淘寳店铺刷單技巧', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('481', '0', 'System', 'SNS军团淘寳刷單兼職赚钱吗', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('482', '0', 'System', 'SNS淘宝刷单平台', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('483', '0', 'System', 'souqian.com', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('484', '0', 'System', '艾婓隄岢', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('485', '0', 'System', '艾悱袛苛', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('486', '0', 'System', '白鹿网', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('487', '0', 'System', '帮帮网', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('488', '0', 'System', '包围 日 使馆', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('489', '0', 'System', '包哲东', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('490', '0', 'System', '保钓', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('491', '0', 'System', '伯俫师忒', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('492', '0', 'System', '伯崃蒒特', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('493', '0', 'System', '伯莱氏鋱', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('494', '0', 'System', '伯莱特', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('495', '0', 'System', '泊崃士特', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('496', '0', 'System', '博彩网', '**', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('497', '0', 'System', '陈{1}{2}{3}拆{4}{5}{6}拆', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('498', '0', 'System', '陈{1}{2}{3}拆{4}{5}拆', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('499', '0', 'System', '陈{1}{2}{3}拆{4}拆', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('500', '0', 'System', '陈{1}{2}{3}德{4}{5}{6}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('501', '0', 'System', '陈{1}{2}{3}德{4}{5}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('502', '0', 'System', '陈{1}{2}拆{3}{4}拆', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('503', '0', 'System', '陈{1}{2}德{3}{4}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('504', '0', 'System', '陈{1}{2}德{3}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('505', '0', 'System', '陈{1}拆{2}拆', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('506', '0', 'System', '陈{1}德{2}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('507', '0', 'System', '陈拆拆', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('508', '0', 'System', '陈浩', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('509', '0', 'System', '陈金彪', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('510', '0', 'System', '陈荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('511', '0', 'System', '陈笑华', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('512', '0', 'System', '陈一新', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('513', '0', 'System', '陈作荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('514', '0', 'System', '仇杨均', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('515', '0', 'System', '代办违章', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('516', '0', 'System', '代考', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('517', '0', 'System', '戴嘉宝', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('518', '0', 'System', '登山协会', 'XXXX', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('519', '0', 'System', '第一团队酒店招聘网', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('520', '0', 'System', '点缀网', '**', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('521', '0', 'System', '电子地磅遥控器', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('522', '0', 'System', '钓鱼岛 游 行', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('523', '0', 'System', '反独裁', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('524', '0', 'System', '反日', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('525', '0', 'System', '反日 警察给打了', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('526', '0', 'System', '非转农坛友', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('527', '0', 'System', '服务小姐', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('528', '0', 'System', '葛慧君', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('529', '0', 'System', '葛益平', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('530', '0', 'System', '海门', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('531', '0', 'System', '黑丝带', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('532', '0', 'System', '胡搞', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('533', '0', 'System', '胡剑谨', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('534', '0', 'System', '胡锦涛', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('535', '0', 'System', '胡瑞怀', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('536', '0', 'System', '黄建省', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('537', '0', 'System', '纪念64', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('538', '0', 'System', '季洪海', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('539', '0', 'System', '加 驶 证 收 分', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('540', '0', 'System', '加驶证', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('541', '0', 'System', '驾 驶 证', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('542', '0', 'System', '驾驶分', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('543', '0', 'System', '驾照 分', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('544', '0', 'System', '驾照分', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('545', '0', 'System', '谏言书', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('546', '0', 'System', '接头举牌', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('547', '0', 'System', '解码器', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('548', '0', 'System', '康师傅下架', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('549', '0', 'System', '攷試荅案', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('550', '0', 'System', '攷試答案', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('551', '0', 'System', '考試荅案', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('552', '0', 'System', '考試答案', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('553', '0', 'System', '孔海龙', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('554', '0', 'System', '喇嘛尼姑纷纷自焚', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('555', '0', 'System', '李旺阳', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('556', '0', 'System', '刘云山', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('557', '0', 'System', '六四签名', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('558', '0', 'System', '马小跳团队', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('559', '0', 'System', '美致客品', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('560', '0', 'System', '美中宜和', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('561', '0', 'System', '孟建新', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('562', '0', 'System', '哪里有小姐', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('563', '0', 'System', '怒蛙网络', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('564', '0', 'System', '潘桥高桐路105号', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('565', '0', 'System', '泮河', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('566', '0', 'System', '彭佳学', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('567', '0', 'System', '綦江', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('568', '0', 'System', '缺{1}{2}{3}{4}德{5}{6}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('569', '0', 'System', '缺{1}{2}{3}德{4}{5}{6}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('570', '0', 'System', '缺{1}{2}{3}德{4}{5}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('571', '0', 'System', '缺{1}{2}德{3}{4}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('572', '0', 'System', '缺{1}{2}德{3}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('573', '0', 'System', '缺{1}德{2}荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('574', '0', 'System', '缺德荣', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('575', '0', 'System', '任玉明', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('576', '0', 'System', '日系车被砸', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('577', '0', 'System', '融资在线网', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('578', '0', 'System', '三个统一', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('579', '0', 'System', '三个至上', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('580', '0', 'System', '三月讲话', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('581', '0', 'System', '上海万凤宫', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('582', '0', 'System', '社保违规', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('585', '0', 'System', '什邡', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('586', '0', 'System', '什么叫敌对势力', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('587', '0', 'System', '收 购', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('588', '0', 'System', '收分', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('589', '0', 'System', '刷单', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('590', '0', 'System', '撕扯日本国旗', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('591', '0', 'System', '搜钱网', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('592', '0', 'System', '搜埥天堂里', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('593', '0', 'System', '搜情', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('594', '0', 'System', '搜情幏园', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('595', '0', 'System', '搜情榢园', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('596', '0', 'System', '搜情乐园', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('597', '0', 'System', '搜晴糘园', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('598', '0', 'System', '搜碃天堂的会员', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('599', '0', 'System', '艘情傢圜', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('600', '0', 'System', '通商宝', '垃圾', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('602', '0', 'System', '王祖焕', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('603', '0', 'System', '网店加盟代理', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('604', '0', 'System', '网站介绍', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('605', '0', 'System', '網資', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('606', '0', 'System', '温家宝', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('607', '0', 'System', '温吞水', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('608', '0', 'System', '温州 加 驶 证 收 分', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('609', '0', 'System', '西安游街', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('610', '0', 'System', '西方错误观点有哪些', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('611', '0', 'System', '想学网球', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('612', '0', 'System', '向使馆建筑投掷杂物', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('613', '0', 'System', '小姐服务', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('614', '0', 'System', '徐顺聪', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('615', '0', 'System', '搖情網', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('616', '0', 'System', '摇情网', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('617', '0', 'System', '一件代发', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('618', '0', 'System', '壹伍伍伍柒伍柒陆陆玖零', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('619', '0', 'System', '英語四級', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('620', '0', 'System', '拥护西方政治制度', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('621', '0', 'System', '余梅生', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('622', '0', 'System', '於其一', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('623', '0', 'System', '月末冲量存款', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('624', '0', 'System', '曾恩伟', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('625', '0', 'System', '章方璋', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('626', '0', 'System', '找小姐', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('627', '0', 'System', '浙江股票配资', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('628', '0', 'System', '郑朝阳', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('629', '0', 'System', '政变', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('630', '0', 'System', '周而复始', '{MOD}', '', '0', '0', '');
INSERT INTO `{TableSysPre}word` VALUES ('631', '0', 'System', '驾驶证分数', '{BANNED}', '', '0', '0', '');
