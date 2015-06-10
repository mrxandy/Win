<?php

//载入全局配置和函数包
require '../../../app.php';

//载入模块配置和函数包
Module :: loader( 'vote' );
Module :: loader( 'analytic' );

//当前 APPID
$appid = Module :: get_appid();

//缓存所有Hock
Module :: hooks_push();

/////////////////////////////////////////////
	
//获取参数
$action = getgpc('action');
$do = getgpc('do');
$fid = getnum('id',0);

//加载缓存
if( !Cached :: loader($appid,"form.".$fid) || !isset($_CACHE[$appid]['form']) ){
	exit('Cache Error!');
}

//回收站中
if( $_CACHE[$appid]['form']['state'] == -1 ){
	exit('Forbidden');	
}

//数据统计
if( $action == "stat" ){
	Member :: form_stat( $_CACHE[$appid]['form']["stat"], getgpc('format'), getgpc('callback') );
}

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
$smarty->template_res = VI_ROOT.'module/vote/content/theme/default/';

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

//随机颜色函数
$smarty->register_function('option',array('Vote','parse_option'));

//公共导航
$smarty->assign("Naver",Module :: hooks_slice('passport/naver'));

/////////// 表单变量 //////////

//重组选项组配置
foreach( $_CACHE[$appid]['group'] as $gid => $group ){
	
	$_CACHE[$appid]['group'][$gid]["description"] = stripslashes( ubb_basic($group["description"]) );
	
	//计算表格宽度
	if( $_CACHE[$appid]['group'][$gid]["config"]["GROUP_BREAK"] ){
		$_CACHE[$appid]['group'][$gid]["width"] = floor(100 / $_CACHE[$appid]['group'][$gid]["config"]["GROUP_BREAK"]) . "%";
	}
	
}

//处理描述内容
$_CACHE[$appid]['form']["description"] = stripslashes( ubb_basic($_CACHE[$appid]['form']['description']) );

//处理表单配置
$config = $_CACHE[$appid]['form']["config"];

/////////// 随机排序 //////////
	
//随机排序
if( $config["FORM_RAND_GROUPS"] == "Y" ){	
	$_CACHE[$appid]['group'] = rand_array($_CACHE[$appid]['group']);
}

if( $config["FORM_RAND_OPTION"] == "Y" ){	
	$_CACHE[$appid]['option'] = rand_array($_CACHE[$appid]['option']);
}

//统计总数
if( $config["HIDE_COUNT"] != "Y" ){
	$_CACHE[$appid]['form']["stat"] = System :: $db -> getValue( "SELECT `stat` FROM `mod:form_form` WHERE id=".$fid );
}

/////////// 子选项统计值 //////////
	
//统计子项
if( $action != "post" ){

	$opt = System :: $db -> getAll( "SELECT id,gid,stat FROM `mod:form_option` WHERE state>0 and fid=".$fid );
	
	//重组选项组配置
	foreach( $opt as $row ){
	
		$_CACHE[$appid]['option'][ $row['gid'] ][ $row['id'] ]["description"] = stripslashes( ubb_basic( $_CACHE[$appid]['option'][ $row['gid'] ][ $row['id'] ]["description"] ) );
	
		//从二级缓存中读取
		$_CACHE[$appid]['option'][ $row['gid'] ][ $row['id'] ]["stat"] = $row['stat'];
	}
	
}

/////////// 自定义排序方式 //////////
	
//排序字段和方式
$fied = getgpc('fied');
$sort = getgpc('sort');

if( $fied && $sort && isset( $_CACHE[$appid]['option'] ) ){

	$smarty->assign("fied", $fied );
	$smarty->assign("sort", $sort );
	
	foreach( $_CACHE[$appid]['option'] as $gid => $group ){
		multi_sort( $_CACHE[$appid]['option'][$gid], $fied, $sort );
	}
	
}else{
	
	$sort = ( $action == 'result' ? $config["FORM_RESULT_SORT"] : $config["FORM_VIEW_SORT"] );
	if( $sort ){
		foreach( $_CACHE[$appid]['option'] as $gid => $group ){
			multi_sort( $_CACHE[$appid]['option'][$gid], 'stat', $sort );
		}
	}
	
}

//数量限制
$limit = getnum('limit',0);

if( $limit ){
	foreach( $_CACHE[$appid]['option'] as $gid => $group ){
		$_CACHE[$appid]['option'][$gid] = array_slice( $_CACHE[$appid]['option'][$gid], 0, $limit );
	}
}


//////////

//表单名称
$smarty->assign('Member',$_G['member']['id'] && Module :: exists('passport') ? Passport :: member( $_G['member']['id'] ) : array() );

//处理动作
switch( $action ){

	//首页
	case '':
	
		if( !function_exists("mcrypt_module_open") ){
			System :: show_notice( '警告：需要 mcrypt 模块支持，<a href="http://www.veryide.com/guide.php?appid=system&id=4" target="_blank">请访问 VeryIDE 查看更多信息</a>' );
		}
	
		//验证码名称
		$captcha = rand_string(10);
	
		//随机验证
		$verify = $_SESSION["verify"] = rand_string(10);
		
		$hash = array("time"=>time(),"ip"=>GetIP(),"captcha"=>$captcha,"verify"=>$verify);		
		$keys = authcrypt( serialize($hash), VI_SECRET );
	
		//表单验证
		$smarty->assign( "keys", rawurlencode($keys) );
	
		//表单验证
		$smarty->assign("captcha",$captcha);
		
		///////////////////////////////////////
		
		//分页参数
		$gid  = getnum('gid',0);
		$page = getpage("page");
		
		//仅显示当前组
		if( $gid && $config["FORM_VIEW_GROUP"] == 'SOLE' ){
			$_CACHE['vote']['group'] = array( $gid => $_CACHE['vote']['group'][ $gid ] );
		}
		
		//重组选项组配置
		foreach( $_CACHE[$appid]['group'] as $g => $group ){
		
			if( $group['config']['GROUP_SIZE'] ){
		
				//取出部分（仅对当前有效）
				$count = Vote :: slice( $g, ( $gid == $g ? $page : 1 ), intval( $group['config']['GROUP_SIZE'] ) );
				
				//分页链接
				$url="?mode=".$mode."&id=".$fid."&gid=".$g."&fied=".$fied."&sort=".$sort."&page=";
				
				//生成链接
				$_CACHE[$appid]['group'][$g]["multi"] = multipage( $page, $count, $group['config']['GROUP_SIZE'], $url, "pp-page" );
			
			}
			
		}
		
		//统计系统
		Analytic :: ping( $appid, $fid, 'views' );
	
	break;

	//提交表单
	case "post":
		require 'include.post.php';
	break;
	
	//投票报名
	case "join":
		require 'include.join.php';
	break;

	//用户结果
	case "state":
		require 'include.state.php';
	break;
	
	//详细信息
	case "detail":
		include("include.detail.php");
	break;

}

$appid = 'vote';

//表单个性化
$_CACHE[$appid]['form']['background'] = Member :: form_bgimage( $config );
$_CACHE[$appid]['form']['bgsound'] = ( !$action ? Member :: form_bgsound( $config ) : '' );
$_CACHE[$appid]['form']['wrapstyle'] = Member :: form_wpstyle( $config );

//表单结构
$smarty->assign("form", $_CACHE[$appid] );

////////////////////////////

//用户登录验证
if( Module :: exists('passport') && $config['USER_MODE'] == "REG" && !in_array( $action , array('result','state') ) ){

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
$smarty->assign("_LOGIN", $LOGIN );

//全局缓存
$smarty->assign("_CACHE", $_CACHE );

$smarty->display('index.htm');

////////////////////////////

//关闭数据库
System :: connect();
