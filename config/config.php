<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	$Id: config.php,v2.1 11:45 2010-5-10 Lay $
*/

//VeryIDE 相对地址
define('VI_BASE', '/');

//VeryIDE 绝对地址
define('VI_HOST', 'http://oa.cc/');

//VeryIDE 本地地址
define('VI_ROOT', str_replace( '\\', '/', substr( __FILE__, 0, -17 ) ));

//VeryIDE 通信密钥，请勿修改
define('VI_SECRET', 'E8MIVbCd8b6S3h1k');

//VeryIDE 安装时间，请勿修改
define('VI_START', '1430191451');

//VeryIDE数据库
define('VI_DBHOST', '121.40.238.56:3306');
define('VI_DBNAME', 'oa');
define('VI_DBUSER', 'root');
define('VI_DBPASS', '45351c3902');

//VeryIDE表前缀，重新安装时请先清空
define('VI_DBMANPRE', 'system_'); 	//默认 system_
define('VI_DBMODPRE', 'module_'); 	//默认 module_

//MySQL 字符集, 可选 'gbk', 'utf8', 'latin1'
define('VI_DBCHARSET', 'utf8');

//UCS编码方式
//部分 Linux 系统下可能会遇到表单提交后中文乱码的问题，请试试改成 UCS-2BE，否则默认为 UCS-2
define('VI_UCS', 'UCS-2BE');