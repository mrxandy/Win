<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	$Id: page.header.php,v3 19:47 2008-2-18 Lay $
*/
	
/*

	后台子框架公共加载文件

*/

///////////////////////////////////

//载入全局配置和函数包
require_once dirname(__FILE__).'/../../app.php';

//关闭压缩，因为会过滤有效的空格
ob_end_clean();

//开启缓冲
ob_start();

////////////////////框架公共函数块_开始///////////////////////

//页面DTD
function html_dtd(){
	global $_G;

	echo '<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset='.$_G['product']['charset'].'" />
<meta name="robots" content="noindex, nofollow" />'.chr(13);
}

//页面Meta
function html_meta(){
	global $_G;
	echo '<meta name="veryide" version="'.$_G['product']['version'].'" build="'.$_G['product']['build'].'" charset="'.$_G['product']['charset'].'" />'.chr(13);
	echo '<meta name="product" name="'.$_G['product']['appname'].'" version="'.$_G['product']['version'].'" />'.chr(13);
}

/*
	页面开始执行
	$title		页面标题
*/
function html_start($title){
	global $_G;
	
	//页面开始执行
	$_G['project']['time_start']=microtime();
	
	echo '<title>'.$title.'</title>';
	
	//'mo.calendar',
	$script = array( 'mo','mo.ajax','mo.drag','mo.interface','mo.ubb','mo.form','mo.ui','mo.calendar' );
	
	for ( $i = 0 ; $i < count($script) ; $i++ ) {
		echo chr(13).'<script type="text/javascript" charset="utf-8" src="'.VI_BASE.'static/js/'.$script[$i].'.js?ver='.$_G['product']['version'].'"></script>';
	}

	echo chr(13).'<script type="text/javascript">';
	echo System :: reader_config();
	echo '</script>';
	
	echo chr(13).'
	<script type="text/javascript" charset="utf-8" src="'.VI_BASE.'static/js/serv.dialog.js?ver='.$_G['product']['version'].'"></script>
	<script type="text/javascript" charset="utf-8" src="'.VI_BASE.'static/js/serv.upload.js?ver='.$_G['product']['version'].'"></script>
	
	</head>
	
	<body>
	<div id="wrapper">
	';
	
}

//页面结束执行
function html_close(){
	global $_G;
	
	//页面结束执行
	$_G['project']['time_end']=microtime();
	
	//页面执行耗时
	echo '<!--Processed in '. ($_G['project']['time_end']-$_G['project']['time_start']) .' second(s) , '.$_G['project']['queries'].' queries-->';
	
	echo '
	</div>
	<script type="text/javascript">Serv.Manager.Loaded(); Serv.Message(\''.$_G['project']['message'].'\',\''.$_G['project']['class'].'\');</script>
	</body>
	</html>';

}

////////////////////框架公共函数块_结束///////////////////////

//登录状态
System :: check_login();

//HTML DTD
html_dtd();

//HTML CSS
echo loader_style(array(VI_BASE."static/style/general.css",VI_BASE."static/style/dialog.css",VI_BASE."static/style/ubb.css",VI_BASE."static/style/calendar.css"),'utf-8',$_G['product']['version']);

/****************************/	

//页面消息
if ( isset($_GET["VMSG"]) ){
	if( count($_GET["VMSG"]) > 1 ){
		$_G['project']['message']=$_GET["VMSG"][count($_GET["VMSG"])];
	}else{
		$_G['project']['message']=$_GET["VMSG"];
	}
}

//跳转地址
$_G['project']['jump'] = getgpc("jump");

//错误跟踪
set_error_handler( array('System','show_error') );	

/****************************/

//页面权限
System :: check_page();
	
?>