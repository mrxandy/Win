<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	$Id: index.php,v2 10:10 2009-7-28 Lay $
*/

/**********/


//载入全局配置和函数包
require '../../../app.php';

Module :: loader( 'go' );
Module :: loader( 'analytic' );

//当前 APPID
$appid = Module :: get_appid();

//统计 ID
$id = getnum('id',0);

//加载缓存
if( !Cached :: loader($appid,"$appid.".$id) ){
	exit('Cache Error!');	
}

switch($_CACHE['go']['go']["method"]){
	
	//链出统计
	case "click":
	
		if ($_CACHE['go']['go']["link"] && $id){
		
			//连接数据库
			System :: connect();
			
			//ping
			Analytic :: ping( $appid, $id, 'clicks' );
			
			//关闭数据库
			System :: connect();
			
			//转向目标链接
			Go :: click( $_CACHE['go']['go']["link"] );
			
		}
		
	break;
	
	//展示统计
	case "view":
	
		if ($id){
			
			//关闭压缩
			ob_end_clean();
			
			//清空缓冲
			ob_clean();
			
			//连接数据库
			System :: connect();
			
			//ping
			Analytic :: ping( $appid, $id, 'views' );
			
			//count
			$conut = Analytic :: count( $appid, $id, 'views' );
			
			//关闭数据库
			System :: connect();
	
			$width		= 10 + strlen($conut) * 6;
			$height		= 20;
			
			header("Content-type: image/png");
			
			$im=@imagecreate($width,$height) or die("Cannot Initialize new GD image stream");
			imagecolortransparent($im,imagecolorallocate($im,255,255,255));
			imagestring($im,2,5,3,$conut,imagecolorallocate($im,0,0,0));
			imagepng($im);
			
		}
		
	break;
}

?>