--
-- 琛ㄧ殑缁撴瀯 `{TableModPre}workflow_expend`
--

CREATE TABLE IF NOT EXISTS `{TableModPre}workflow_expend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `quote` varchar(100) DEFAULT NULL,
  `modify` int(11) DEFAULT NULL,
  `mender` varchar(30) DEFAULT NULL,
  `dateline` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- 琛ㄧ殑缁撴瀯 `{TableModPre}workflow_merchant`
--

CREATE TABLE IF NOT EXISTS `{TableModPre}workflow_merchant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `category` varchar(30) NOT NULL,
  `aid` int(11) DEFAULT NULL,
  `account` varchar(30) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `description` text,
  `quote` varchar(100) DEFAULT NULL,
  `modify` int(11) DEFAULT NULL,
  `mender` varchar(30) DEFAULT NULL,
  `dateline` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `level` (`level`),
  KEY `category` (`category`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `summary` (`summary`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- 琛ㄧ殑缁撴瀯 `{TableModPre}workflow_month`
--

CREATE TABLE IF NOT EXISTS `{TableModPre}workflow_month` (
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(255) NOT NULL,
  `work_days` tinyint(4) DEFAULT NULL,
  `work_note` varchar(255) DEFAULT NULL,
  `modify` int(11) DEFAULT NULL,
  `mender` varchar(30) DEFAULT NULL,
  `dateline` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  UNIQUE KEY `year` (`year`,`month`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- 琛ㄧ殑缁撴瀯 `{TableModPre}workflow_project`
--

CREATE TABLE IF NOT EXISTS `{TableModPre}workflow_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `contribute` tinyint(4) DEFAULT NULL,
  `summary` tinytext,
  `description` text,
  `attachment` text,
  `stoptime` int(11) DEFAULT NULL,
  `member` text,
  `quote` varchar(100) DEFAULT NULL,
  `modify` int(11) DEFAULT NULL,
  `mender` varchar(30) DEFAULT NULL,
  `dateline` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `ip` char(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `level` (`level`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `contribute` (`contribute`),
  KEY `dateline` (`dateline`),
  KEY `state` (`state`)
) ENGINE=MyISAM;

-- ----------------------------
-- Table structure for module_workflow_devote
-- ----------------------------

CREATE TABLE IF NOT EXISTS `{TableModPre}workflow_devote` (
  `pid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `master` tinyint(4) NOT NULL,
  `contribute` int(11) NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `dateline` int(11) NOT NULL,
  UNIQUE KEY `pid` (`pid`,`aid`),
  KEY `master` (`master`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM;


-- --------------------------------------------------------

--
-- 琛ㄧ殑缁撴瀯 `{TableModPre}workflow_salary`
--

CREATE TABLE IF NOT EXISTS `{TableModPre}workflow_salary` (
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `work_late` tinyint(4) NOT NULL,
  `work_days` decimal(3,1) NOT NULL,
  `work_note` varchar(255) NOT NULL,
  `work_diary` tinyint(4) NOT NULL,
  `content_bills` decimal(10,1) NOT NULL,
  `deduct_bills` decimal(10,1) NOT NULL,
  `project_bills` decimal(10,1) NOT NULL,
  `diary_bills` decimal(10,1) NOT NULL,
  `meal_bills` decimal(10,1) NOT NULL,
  `phone_bills` decimal(10,1) NOT NULL,
  `misc_bills` decimal(10,1) NOT NULL,
  `work_bills` decimal(10,1) NOT NULL,
  `post_bills` decimal(10,1) NOT NULL,
  `late_bills` int(11) NOT NULL,
  `fund_bills` decimal(10,1) NOT NULL,
  `debit_bills` decimal(10,0) NOT NULL,
  `total` int(11) NOT NULL,
  `bills_note` varchar(255) DEFAULT NULL,
  `modify` int(11) DEFAULT NULL,
  `mender` varchar(30) DEFAULT NULL,
  `dateline` int(11) NOT NULL,
  `ip` char(15) NOT NULL,
  KEY `year` (`year`),
  KEY `month` (`month`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `total` (`total`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- 琛ㄧ殑缁撴瀯 `{TableModPre}workflow_staff`
--

CREATE TABLE IF NOT EXISTS `{TableModPre}workflow_staff` (
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `content_multi` tinyint(4) NOT NULL,
  `content_pay` int(11) NOT NULL,
  `work_pay` int(11) NOT NULL,
  `diary_pay` int(11) NOT NULL,
  `post_pay` int(11) NOT NULL,
  `meal_pay` int(11) NOT NULL,
  `phone_pay` int(11) NOT NULL,
  `misc_pay` int(11) NOT NULL,
  `total_pay` int(11) NOT NULL,
  `bank_card` varchar(25) DEFAULT NULL,
  `entry_time` varchar(10) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `modify` int(11) NOT NULL,
  `mender` varchar(30) DEFAULT NULL,
  `dateline` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`aid`),
  KEY `aid` (`aid`),
  KEY `account` (`account`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- 琛ㄧ殑缁撴瀯 `{TableModPre}workflow_stuff`
--

CREATE TABLE IF NOT EXISTS `{TableModPre}workflow_stuff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `quantity` int(11) NOT NULL,
  `surplus` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `description` text,
  `quote` varchar(100) DEFAULT NULL,
  `modify` int(11) DEFAULT NULL,
  `mender` varchar(30) DEFAULT NULL,
  `dateline` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `name` (`name`),
  KEY `surplus` (`surplus`),
  KEY `summary` (`summary`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- 琛ㄧ殑缁撴瀯 `{TableModPre}workflow_thread`
--

CREATE TABLE IF NOT EXISTS `{TableModPre}workflow_thread` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `first` tinyint(4) NOT NULL,
  `aid` int(11) NOT NULL,
  `account` varchar(30) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `authorid` int(11) NOT NULL,
  `author` varchar(30) NOT NULL,
  `posttime` int(11) NOT NULL,
  `digest` tinyint(4) NOT NULL,
  `credit` tinyint(4) NOT NULL,
  `modify` int(11) DEFAULT NULL,
  `mender` varchar(30) DEFAULT NULL,
  `dateline` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `ip` char(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `pid` (`pid`),
  KEY `aid` (`aid`),
  KEY `account` (`account`),
  KEY `digest` (`digest`),
  KEY `credit` (`credit`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM;

