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

//文件头
header('Content-Type:text/javascript; charset='.$_G['product']['charset'].'');

//操作
$action		= getgpc('action');

//回调
$callback	= getgpc('callback') ? getgpc('callback') : "LinkClick.empty";

$app = getgpc('app');
$app = iconv( 'utf-8', $_G['product']['charset'], $app );

$ver = getgpc('ver');
$ver = iconv( 'utf-8', $_G['product']['charset'], $ver );

switch( $action ){
	
	//链出统计
	case "click":
		
		$index = getnum('index',-1);
		$link = getgpc('link');
		$title = getgpc('title');
		$title = iconv( 'utf-8', $_G['product']['charset'], $title );
	
		if ( $app && $ver && $index > -1 ){
		
			//连接数据库
			System :: connect();
			
			//ping
			Go :: link_click( $app, $ver, $index, $title );
			
			//ping
			Go :: link_stat( $app, $link, $title );
			
			//关闭数据库
			System :: connect();
			
			//调试输出
			if( $app != '主站' ){
				create_file( date("Y-m-d") . '.txt', var_export($_SERVER, TRUE) );
			}
			
		}
		
		//返回数据
		echo $callback.'({ "click":"true" });';
		
	break;
	
	//输出统计
	case "count":
	
		//连接数据库
		System :: connect();
		
		//今日点击
		$today = Go :: link_count( array( 'app' => $app, 'ver' => $ver, 'cate' => 'D', 'date' => date("Y-m-d") ) );
		
		//昨日点击
		$yesterday = Go :: link_count( array( 'app' => $app, 'ver' => $ver, 'cate' => 'D', 'date' => date("Y-m-d", time()-86400 ) ) );
		
		//关闭数据库
		System :: connect();
		
		//返回数据
		echo $callback.'({ "today":'.$today.', "yesterday":'.$yesterday.' });';
		
	break;
	
	//索引统计
	case "index":
	
		$index = getnum('index',-1);
	
		//连接数据库
		System :: connect();
		
		//今日点击
		$today = Go :: link_count( array( 'app' => $app, 'ver' => $ver, 'index' => $index, 'cate' => 'D', 'date' => date("Y-m-d") ) );
		
		//昨日点击
		$yesterday = Go :: link_count( array( 'app' => $app, 'ver' => $ver, 'index' => $index, 'cate' => 'D', 'date' => date("Y-m-d", time()-86400 ) ) );
		
		//前日点击
		$beforeday = Go :: link_count( array( 'app' => $app, 'ver' => $ver, 'index' => $index, 'cate' => 'D', 'date' => date("Y-m-d", time()-86400-86400 ) ) );
		
		//关闭数据库
		System :: connect();
		
		//返回数据
		echo $callback.'({ "today" : '.$today.' , "yesterday" : '.$yesterday.', "beforeday" : '.$beforeday.' });';
		
	break;
	
	//索引统计
	case "statis":
	
		$cate = getgpc('cate');
	
		//连接数据库
		System :: connect();
		
		$data = $weekend = array();
		
		switch( $cate ){
		
			//最近七天
			case 'week';
				
				for( $i = -7; $i < 0; $i++ ){
					
					$time = strtotime("+$i day");
					
					$date = date("Y-m-d", $time );
					
					//判断周末
					if( date("w", $time ) == 0 | date("w", $time ) == 6 ) array_push( $weekend, date("j", $time ) );
					
					$stat = Go :: link_count( array( 'app' => $app, 'cate' => 'D', 'date' => $date ) );
					
					$data[ date("j", $time ) ] = $stat;
					
				}
			
			break;
		
			//本月统计
			case 'moon';
			
				for( $i = 1; $i <= date("d"); $i++ ){
					
					$date = date("Y-m-".( $i > 9 ? $i : '0'.$i ));
					
					$time = strtotime( $date );
					
					//判断周末
					if( date("w", $time ) == 0 | date("w", $time ) == 6 ) array_push( $weekend, $i );
					
					$stat = Go :: link_count( array( 'app' => $app, 'cate' => 'D', 'date' => $date ) );
					
					$data[ $i ] = $stat;
					
				}
			
			break;
		
			//本年统计
			case 'year';
				
				for( $i = 1; $i < date("m"); $i++ ){
					
					$date = date("Y-".( $i > 9 ? $i : '0'.$i ));
					
					$stat = Go :: link_count( array( 'app' => $app, 'cate' => 'M', 'date' => $date ) );
					
					$data[ $i ] = $stat;
					
				}
			
			break;
		
			//时段分析
			case 'hour';
			
				$weekend = array( 8, 9, 10, 11, 14, 15, 16, 17, 20, 21, 22 );
				
				for( $i = 0; $i < 24; $i++ ){
					
					$stat = Go :: link_count( array( 'app' => $app, 'cate' => 'H', 'date' => ( $i > 9 ? $i : '0'.$i ) ) );
					
					$data[ $i ] = $stat;
					
				}
			
			break;
			
		}
		
		//关闭数据库
		System :: connect();
		
		//测试数据
		/*
		foreach( $data as $k => $v ){
			$data[ $k ] = rand( 1, 1000 );	
		}
		*/
		
		//返回数据
		if( count($data) ){
			echo $callback.'( { "item": '. json_encode( $data ) .', "min" : '. min( $data ) .', "max" : '. max( $data ) .', "weekend" : '. json_encode( $weekend ) .' } );';
		}else{
			echo $callback.'( { "state": "error" } );';
		}
		
	break;
	
	//计划任务
	case "cron":
	
		$index = getnum('index');
		
		/////////////////////////
		
		//周末下午5点执行
		//if( date("w") == 0 && date("G") == 17 ){
		if( date("G") == 17 ){
			
			//关掉浏览器，PHP脚本也可以继续执行.
			ignore_user_abort();
			
			//让程序无限制的执行下去
			set_time_limit(0);
			
			//连接数据库
			System :: connect();
			
			$res = Go :: link_cron();
			
			//关闭数据库
			System :: connect();
			
			//返回数据
			echo $callback.'({ "send" : "'. $res .'" });';
			
		}else{
					
			//返回数据
			echo $callback.'({ "send" : "wait" });';
	
		}
		
	break;
	
	///////////////////
	
	//获取QQ
	/*
	case "qqwrite":
	
		$host = getgpc('qqhost');
		$uin = getgpc('qquin');
	
		$origin = getgpc('origin');
		$keyword = getgpc('keyword');
		$charset = getgpc('charset');
		
		if( $host && $uin ){
			
			$link = $host . $uin;
			
			$text = file_get_contents( $link );
			
			//转编码
			if( $_G['product']['charset'] != 'utf-8' ){
				$text = iconv( 'utf-8', $_G['product']['charset'], $text );
			}
			
			//提取最近访客
			preg_match( '/<li><a href="\/profiles\/(.+?)"><img src="(.+?)" alt="(.+?)" \/>(.+?)<\/a><\/li>/', $text, $matche );
			
			//找到最近一个
			if( $matche ){
				
				//连接数据库
				System :: connect();
				
				if( $keyword && $charset != $_G['product']['charset'] ){
					$keyword = iconv( 'utf-8', $_G['product']['charset'], $keyword );
				}
				
				$res = Go :: uin_write( $matche[1], $matche[3], $matche[2], $origin, $keyword );
				
				//关闭数据库
				System :: connect();
				
			}
			
			echo $callback.'({ "return" : "saved", "number" : "'. $matche[1] .'", "lifetime" : "'. time() .'" });';
			
			//var_dump( $matche );
			
		}
		
	break;
	
	//获取QQ
	case "qqupdate":
	
		$number = getgpc('number');
		
		if( $number ){
				
			//连接数据库
			System :: connect();
			
			$res = Go :: uin_update( $number );
			
			//关闭数据库
			System :: connect();
			
			echo $callback.'({ "return" : "saved", "number" : "'. $number .'", "lifetime" : "'. time() .'" });';
			
			//var_dump( $matche );
			
		}
		
	break;
	*/
	
}

?>