<?php
/*
	应用主框架
*/

//载入依赖库以及配置
require_once dirname(__FILE__)."/app.php";

//载入 日历转换 类
require_once("source/class/lunar.php");

//载入 MySQL 类
require_once("source/class/db.mysql.php");

//读取个人配置
if( $_G['manager']['id'] && VI_DBMANPRE && VI_DBMODPRE ){
	
	//用户自定义
	$config = $_CACHE['system']['admin'][$_G['manager']['id']]["config"];
	
}

//界面风格
$config['ui-model'] = isset($config['ui-model']) ? $config['ui-model'] : $_G['setting']['global']['model'];

//开启页面压缩
ob_start("compress");

//圣诞节（共5天）
$xmas = ( date("m")==12 && date("d")>20 && date("d")<30 );
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_G['product']['charset'];?>" />
<title><?php echo $_G['setting']['global']['site'];?></title>
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<?php
if( $_G['device'] != 'unknown' ){
?>
<meta name="apple-mobile-web-app-title" content="<?php echo $_G['setting']['global']['site'];?>">
<link rel="apple-touch-icon-precomposed" href="static/image/homescreen.png" />
	<?php
	if( $_G['device'] == 'ipad' ){
	?>
	<link rel="stylesheet" type="text/css" href="source/touch/homescreen.css" />
	<script type="text/javascript" charset="utf-8" src="source/touch/homescreen.js?ver=<?php echo $_G['product']['version'];?>""></script>
<?php
	}
}
?>

<?php

//加载基本脚本
$script = array( 'mo','mo.ui','mo.fx','serv' );

for ( $i = 0 ; $i < count($script) ; $i++ ) {
	echo '<script type="text/javascript" charset="utf-8" src="static/js/'.$script[$i].'.js?ver='.$_G['product']['version'].'"></script>';
}

//用户登录后用到的脚本
if( $_G['manager']['id'] ){
	$script = array( 'mo.xml','mo.hash','mo.drag','mo.ajax' );	
	for ( $i = 0 ; $i < count($script) ; $i++ ) {
		echo '<script type="text/javascript" charset="utf-8" src="static/js/'.$script[$i].'.js?ver='.$_G['product']['version'].'"></script>';
	}
}

//通用样式
echo '<link type="text/css" href="static/style/extend.css?ver='.$_G['product']['version'].'" rel="stylesheet" />';

//界面模式
if( $config['ui-model'] == 'classic' ){	
	echo '<link type="text/css" href="static/style/classic.css?ver='.$_G['product']['version'].'" rel="stylesheet" />';
	echo loader_script( array('static/js/serv.classic.js'), 'utf-8', $_G['product']['version'] );
}else{
	echo '<link type="text/css" href="static/style/modern.css?ver='.$_G['product']['version'].'" rel="stylesheet" />';
	echo '<link type="text/css" id="style" href="static/theme/'.( $xmas ? 'xmas' : 'default' ).'/style.css?ver='.$_G['product']['version'].'" rel="stylesheet" />';
}

?>
<script type="text/javascript">
	//当前主题
	Mo.store.model	= "<?php echo $config['ui-model'] ? $config['ui-model'] : 'modern';?>";	
	
	//当前主题
	Mo.store.theme	= "<?php echo $_G['manager']['theme'];?>";
	
	//当前头像
	Mo.store.avatar	= "<?php echo fix_thumb( $_G['manager']['avatar'] );?>";

	//当前UID
	Mo.store.aid	= "<?php echo $_G['manager']['id'];?>";
		
	//当前用户
	Mo.store.account= "<?php echo $_G['manager']['account'];?>";
		
	//当前版本
	Mo.store.appname= "<?php echo $_G['product']['appname'];?>";
		
	//当前版本
	Mo.store.version= "<?php echo $_G['product']['version'];?>";
		
	//授权类型
	Mo.store.licence= "<?php echo $_G['licence']['type'];?>";
	
	//心跳频率（秒）
	Mo.store.heartbeat= "<?php echo $_G['project']['heartbeat'];?>";
</script>
</head>

<body>

	<?php 
		
	//加载测试工具
	require VI_ROOT.'source/dialog/checkin.php';
	
	//加载更新工具
	if( $_G['manager']['id'] && VI_DBMANPRE && VI_DBMODPRE ) require VI_ROOT.'source/dialog/fixbug.php';		
	
	//加载安装界面
	if( !VI_DBMANPRE || !VI_DBMODPRE ){
		require VI_ROOT.'source/dialog/install.php';
		exit;
	}
	
	require( 'source/dialog/'.( $config['ui-model'] == 'classic' ? 'classic' : 'modern' ).'.php' );
		
	?>
    
</body>
</html>