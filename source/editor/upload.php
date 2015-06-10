<?php
/**
 * KindEditor PHP
 * 
 * 本PHP程序是演示程序，建议不要直接在实际项目中使用。
 * 如果您确定直接使用本程序，使用之前请仔细确认相关安全设置。
 * 
 */

//载入全局配置和函数包
require dirname(__FILE__)."/../../app.php";

//////////////////////////////

//注册会话
$session = getgpc('session');

//用户体系
$sid = '';

//系统管理员
if( $_G['manager']['id'] ){
	$sid = 'manager';
}

//普通用户
if( !$_G['manager']['id'] && $session ){

	Module :: loader( 'passport', 'function' );
	
	$hash = Passport :: session_decode( $session );
	
	if( $hash['id'] ){
		$sid = 'member';
	}
}

//会话域
if ( !$sid ){
	
	alert( 1, '您当前不在登录状态（'. $sid .'）');

}elseif( empty($_FILES) === FALSE ) {
	
	//连接数据库
	System :: connect();
	
	$res = Attach :: savefile( array( 'field'=> 'imgFile', 'filetype'=> 'image', 'account'=> $sid, 'absolute'=> TRUE ) );
	
	//关闭数据库
	System :: connect();
	
	if( $res ){
		alert( 0, NULL, $res );	
	}else{
		alert( 1, '文件上传出现错误（'. $sid .'）', $res );	
	}
	
}

/*
	输出消息
	$err	0 无错 1 有错
	$msg	消息内容
	$url	附件地址
*/
function alert( $err, $msg, $url = NULL ) {
	global $_G;
	
	header('Content-type: text/html; charset=UTF-8');
	
	if( $msg && $_G['product']['charset'] != 'utf-8' ) $msg = iconv($_G['product']['charset'],'UTF-8', $msg);
	
	//跨域支持（子域名必需）
	echo $_GET['crossdomain'] == 'true' ? '<script>'. System :: cross_domain() .'</script>' : '';
	
	echo json_encode( array('error' => $err, 'message' => $msg, 'url' => $url) );
	exit;
}
?>