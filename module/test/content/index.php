<?php

//载入全局配置和函数包
require '../../../app.php';

//载入模块配置和函数包
Module :: loader( 'test' );
Module :: loader( 'member' );
Module :: loader( 'analytic' );

//当前 APPID
$appid = Module :: get_appid();

//缓存所有Hock
Module :: hooks_push();

/////////////////////////////////////////////
	
//获取参数
$mode = getgpc("mode");
$action = getgpc('action');
$do = getgpc('do');
$fid = getnum('id',0);

//加载缓存
if( !Cached :: loader($appid,"form.".$fid) ){
	exit('Cache Error!');
}

//回收站中
if( $_CACHE[$appid]['form']['state'] == -1 ){
	exit('Forbidden');	
}

//初始人数
$_CACHE[$appid]['form']['stat'] = $_CACHE[$appid]['form']['stat'] + intval($_CACHE[$appid]['form']['config']['BASE_NUM']);

//数据统计
if( $action == "stat" ){
	Member :: form_stat( $_CACHE[$appid]['form']["stat"], getgpc('format'), getgpc('callback') );
}

/////////////////////////////////////////////

//连接数据库
System :: connect();
		
////////// 载入通行证 ////////////

if( Module :: exists('passport') ){
	
	//载入函数包
	Module :: loader( 'passport' );
	
	//重定义模板
	if( $_G['setting'][$appid]['templet'] ) $_G['setting']['passport']['theme'] = $_G['setting'][$appid]['templet'];
	$_G['setting']['passport']['hidenaver'] = $_CACHE[$appid]['form']['config']['HIDE_NAVER'];
	$_G['setting']['passport']['hideright'] = $_CACHE[$appid]['form']['config']['HIDE_RIGHT'];
	
	//主动初始用户信息
	Passport :: detect( FALSE, FALSE );
}

////////// 验证模板 ////////////
	
$skin = $_CACHE[$appid]['form']['skin'];

if ( !file_exists( 'theme/'.$skin ) ) {
	$_CACHE[$appid]['form']['skin'] = 'default';
}

if ( !file_exists( 'theme/'.$skin.'/index.htm' ) ) {
	$skin = 'default';
}

/////////////////////////////////////////////
	
//加载 Smarty 类
require_once VI_ROOT.'source/smarty/Smarty.class.php';

$smarty = new Smarty;

//模板路径
$smarty->template_dir = 'theme/'.$skin.'/';

//默认模板
$smarty->template_res = VI_ROOT.'module/test/content/theme/default/';

//编译路径
$smarty->compile_dir = create_dir( VI_ROOT.'cache/compile/'.$appid );

//编译编号（防止冲突）
$smarty->compile_id = $skin;

$smarty->compile_check = true;

$smarty->debugging = false;

/////////// 全局变量 //////////

//系统变量
$smarty->assign("_G",$_G);


//当前页面绝对地址
$smarty->assign("SELF",GetAbsUrl());

//前台目录绝对地址
$smarty->assign("Index", Module :: get_index($appid) );

//随机颜色函数
$smarty->register_function('color',"rand_color");

//公共导航
$smarty->assign("Naver",Module :: hooks_slice('passport/naver'));

/////////// 表单变量 //////////

//处理表单配置
$config = $_CACHE[$appid]['form']["config"];

//随机取定量的选项组
if( $config["FORM_RAND_LIMIT"] ){
	$_CACHE[$appid]['group'] = array_limit( $_CACHE[$appid]['group'], $config["FORM_RAND_LIMIT"] );
}

//重组选项组配置
foreach( $_CACHE[$appid]['group'] as $gid => $group ){
	$_CACHE[$appid]['group'][$gid]["description"] = stripslashes( ubb_basic($group["description"]) );
}

//处理描述内容
$_CACHE[$appid]['form']["description"] = stripslashes( ubb_basic($_CACHE[$appid]['form']['description']) );

/////////// 随机排序 //////////
	
//随机排序
if( $config["FORM_RAND_GROUPS"] == "Y" ){	
	$_CACHE[$appid]['group'] = rand_array($_CACHE[$appid]['group']);
}

if( $config["FORM_RAND_OPTION"] == "Y" ){	
	//$_CACHE[$appid]['option'] = rand_array($_CACHE[$appid]['option']);
	foreach( $_CACHE[$appid]['option'] as $gid => &$option ){
		$option = rand_array( $_CACHE[$appid]['option'][$gid] );
	}
}
	
/////////////////////////////////

//表单模式
$smarty->assign('mode', dhtmlspecialchars($mode) );

//表单个性化
$_CACHE[$appid]['form']['background'] = Member :: form_bgimage( $config );
$_CACHE[$appid]['form']['bgsound'] = ( !$action ? Member :: form_bgsound( $config ) : '' );
$_CACHE[$appid]['form']['wrapstyle'] = Member :: form_wpstyle( $config );

//表单结构
$smarty->assign("form", $_CACHE[$appid] );

//表单名称
$smarty->assign('Member',$_G['member']['id'] && Module :: exists('passport') ? Passport :: member( $_G['member']['id'] ) : array() );

/////////// 加载资源 //////////

//处理动作
switch( $action ){

	//查看表单
	case "":
		//统计系统
		Analytic :: ping( $appid, $fid, 'views' );
	break;

	//提交表单
	case "post":
		require 'include.post.php';
	break;

	//用户结果
	case "state":
		require 'include.state.php';
	break;

}

////////////////////////////

//用户登录验证
if( Module :: exists('passport') && $config['USER_MODE'] == "REG" && !in_array( $action , array('result','status') ) ){

	$fields = array();
	
	//记录需要更新的字段
	foreach ($_G['module']['member']["fieds"] as $fied => $name){				
		if( isset( $config["REG_".strtoupper($fied)] ) ){
			$fields[] = $fied;
		}					
	}
	
	//更新特定字段
	if( count($fields) ){
	
		//显示登录窗口，并需要更新资料
		$LOGIN = Passport :: detect( TRUE , $fields );
		
	}else{
		
		//显示登录窗口
		$LOGIN = Passport :: detect( TRUE, FALSE );
		
	}
				
}

//分组类型
$smarty->assign("_LOGIN", isset( $LOGIN ) ? $LOGIN : '' );

//全局缓存
$smarty->assign("_CACHE", $_CACHE );

$smarty->display('index.htm');

////////////////////////////

//关闭数据库
System :: connect();
