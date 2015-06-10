<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	$Id: page.global.php,v3 19:47 2008-2-18 Lay $
*/
	
/*

	全局配置、函数及缓存载入

*/

///////////////////////////////////

//全局配置
require_once dirname(__FILE__).'/config/config.php';

//载入配置
require_once VI_ROOT.'config/version.php';
require_once VI_ROOT."config/global.php";
require_once VI_ROOT."config/attach.php";
require_once VI_ROOT."config/mail.php";

//载入扩展
require_once VI_ROOT."config/licence.php";

//设置时区
if( PHP_VERSION > '5.1' ) {
	function_exists('date_default_timezone_set') && date_default_timezone_set('Etc/GMT'.($_G['setting']['global']['timezone'] > 0 ? '-' : '+').(abs($_G['setting']['global']['timezone'])));
}

//设置语言
@setlocale(LC_CTYPE, "C");

//载入函数
require_once VI_ROOT.'source/function/compat.php';
require_once VI_ROOT.'source/function/array.php';
require_once VI_ROOT.'source/function/library.php';
require_once VI_ROOT.'source/function/ubb.php';
require_once VI_ROOT.'source/function/filter.php';
require_once VI_ROOT.'source/function/verify.php';
require_once VI_ROOT.'source/function/client.php';

//载入类库
require_once VI_ROOT.'source/class/system.php';
require_once VI_ROOT.'source/class/module.php';
require_once VI_ROOT.'source/class/cached.php';
require_once VI_ROOT.'source/class/attach.php';
require_once VI_ROOT.'source/class/output.php';
require_once VI_ROOT.'source/class/database.php';
require_once VI_ROOT.'source/class/db.mysql.php';

//////////// 模块 /////////////

if( VI_DBMANPRE && VI_DBMODPRE ){

	//载入模块缓存
	$cache = VI_ROOT.'cache/dataset/system/files.module.php';
	
	//若不存在则主动创建缓存
	if( !file_exists($cache) ){
	
		//扫描模块
		Module :: search();
	
	}
	
	//载入缓存
	require($cache);

}

//////////// 用户 /////////////

//VeryIDE 已安装
if( VI_DBMANPRE && VI_DBMODPRE ){

	//载入用户缓存
	$_cache = VI_ROOT.'cache/dataset/system/table.admin.php';

	//若不存在则主动创建缓存
	if( !file_exists( $_cache ) ){

		//连接数据库
		System :: connect();

		//缓存系统用户
		Cached :: table( 'system','sys:admin', array( 'jsonde' => array('config','weibo') ) );

		//关闭数据库
		System :: connect();

	}	

	//载入缓存
	require( $_cache );

}

//////////// 用户组 /////////////

//VeryIDE 已安装
if( VI_DBMANPRE && VI_DBMODPRE ){

	//载入分组缓存
	$_cache = VI_ROOT.'cache/dataset/system/table.group.php';

	//若不存在则主动创建缓存
	if( !file_exists( $_cache ) ){

		//连接数据库
		System :: connect();

		//缓存系统用户组
		Cached :: table( 'system', 'sys:group', array( 'jsonde' => array('config'), 'serialize' => array('module','widget') ) );

		//关闭数据库
		System :: connect();

	}

	//载入缓存
	require( $_cache );
}

//////////// initialize /////////////

System :: init();

Module :: init();

//加载权限到全局变量
$_G['group'] = $_G['manager']["gid"] ? $_CACHE['system']['group'][$_G['manager']["gid"]]["config"] : array();

//////////// 压缩选项 /////////////

//开启页面压缩
if( $_G['setting']['global']['compress'] == 'on' ){
	ob_start('compress');
}
