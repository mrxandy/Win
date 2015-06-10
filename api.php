<?php

/*

		系统模块列表

*/

//载入全局配置和函数包
require_once dirname(__FILE__).'/app.php';

////////////////////////////////////////////

//操作
$action		= getgpc('action');

//执行
$execute	= getgpc('execute');

//回调
$callback	= getgpc('callback') ? getgpc('callback') : "Serv.empty";

//////////////////////

require_once VI_ROOT.'source/class/pclzip.php';
require_once VI_ROOT.'source/class/cloud.php';
	
switch ($action){

	//接收客户端命令
	case "command":
		
		//密钥
		$keygen		= getgpc('keygen');
		
		/*
		$f = './android_log.txt';
		if (!file_exists($f)) touch($f);
		$of = file_get_contents($f);
		$str = var_export($_SERVER, 1);
		file_put_contents($f, $str . "\r\n" . $of);
		*/
	
		//测试信息
		if( $execute == 'checkin' ){
			
			//密钥不正确
			if( $keygen != VI_SECRET ){				
				
				exit('FALSE');
				
			}else{
					
				//连接数据库
				System :: connect();
	
				//关闭数据库
				System :: connect();
				
				//正常连接
				exit('TRUE');
				
			}
			
		}else{
		
			exit( $execute );
		
		}

	break;
	
	//////////////////////////////
	
	//应用市场命令
	case "market":
		
		// && $_G['manager']['id']
		
		//是否开启安全通信
		$connect = isset( $_G['setting']['global']['connect'] ) ? $_G['setting']['global']['connect'] : 'off';
		
		//没有安装 mcrypt
		if( function_exists('mcrypt_create_iv') === FALSE ){
		
			exit( serialize( array( 'return' => 'mcrypt', 'connect' => $connect ) ) );
			
		}
			
		//反序列化
		$command = unserialize(authcrypt( $execute, VI_SECRET, 'decode' ));
		
		//测试命令
		if( is_array($command) === FALSE ){
			
			exit( serialize( array( 'return' => 'secret', 'connect' => $connect ) ) );
			
		}
			
		//延长程序运行时间
		if( !ini_get('safe_mode') ){
			set_time_limit(0);
		}
		
		/////////////////////////
		
		//连接数据库
		System :: connect();
	
		switch( $command['execute'] ){

			//测试命令
			case 'testing':
				
				exit( serialize( array( 'string' => $command['string'], 'connect' => $connect ) ) );
			
			break;
								
			////////////////////////////
			
			//授权生成
			case 'licence':
			
				//通信已关闭
				if( $connect == 'off' ){
					exit( serialize( array( 'return' => 'connect', 'connect' => $connect ) ) );
				}
			
				$data = Cloud :: licence();
				
				exit( serialize( array( 'licence'=> $data ) ) );
			
			break;
			
			////////////////////////////
		
			//引擎处理
			case 'engine':
			
				switch( $command['method'] ){
					
					//引擎升级
					case 'upgrade':
					
						//通信已关闭
						if( $connect == 'off' ){
							exit( serialize( array( 'return' => 'connect', 'connect' => $connect ) ) );
						}
						
						//测试读写权限
						$status = Cloud :: valid_perm( VI_ROOT, array( 'attach/', 'cache/', 'static/' ) );
						if( count( $status ) ){
							exit( serialize( array( 'return' => 'permission', 'catalog' => $status ) ) );
						}
						
						//升级引擎
						$status = Cloud :: upgrade_engine( $command['package'], $command['hash'] );
						
						//升级成功
						if( $status > 0 ){
							
							//执行升级脚本
							System :: upgrade( $command['version'] );
							
							//更新模块缓存
							Module :: search();
							
							exit( serialize( array( 'return' => 'success', 'status' => 'success' ) ) );
							
						}else{
							
							exit( serialize( array( 'return' => 'package', 'status' => $status, 'lastfile' => Cloud :: $lastfile ) ) );
							
						}
					
					break;
					
					////////////////////////////
					
					//模块搜索
					case 'licence':
					
						exit( serialize( $_G['licence'] ) );
					
					break;
					
					////////////////////////////
					
					//模块安装
					case 'version':
					
						exit( serialize( array( 'product' => $_G['product'], 'connect' => $connect ) ) );
					
					break;
					
				}
			
			break;
			
			////////////////////////////
			
			//模块处理
			case 'module':
			
				$module = VI_ROOT . 'module/'. $command['appid'].'/';
			
				switch( $command['method'] ){
					
					//模块安装
					case 'install':

						//通信已关闭
						if( $connect == 'off' ){
							exit( serialize( array( 'return' => 'connect', 'connect' => $connect ) ) );
						}

						//缺少必要参数
						if( !$command['appid'] || !$command['package'] ){
							exit( serialize( array( 'return' => 'argument', 'appid' => $command['appid'], 'package' => $command['package'] ) ) );
						}
						
						//模块已存在
						if( Module :: exists( $command['appid'] ) ){
							exit( serialize( array( 'return' => 'exist', 'appid' => $command['appid'] ) ) );
						}
						
						//创建模块目录
						$dir = create_dir( $module, TRUE, 0777 );
						if( $dir === FALSE ){
							exit( serialize( array( 'return' => 'permission', 'catalog' => array( './module/' ) ) ) );
						}
						
						//测试读写权限
						$status = Cloud :: valid_perm( $module );
						if( count( $status ) ){
							exit( serialize( array( 'return' => 'permission', 'catalog' => $status ) ) );
						}
								
						//安装模块
						$status = Cloud :: install_module( $command['package'], $command['hash'], $command['appid'], $command['option']['install']['ignore'] );
						
						//安装成功
						if( $status > 0 ){
							
							//执行安装脚本
							Module :: install( $command['appid'] );
							
							//更新模块缓存
							Module :: search();
							
							exit( serialize( array( 'return' => 'success', 'appid' => $command['appid'], 'status' => 'success' ) ) );
							
						}else{
						
							//删除目录
							delete_dir( $module );
							
							exit( serialize( array( 'return' => 'package', 'appid' => $command['appid'], 'status' => $status, 'lastfile' => Cloud :: $lastfile ) ) );
							
						}
					
					break;
					
					////////////////////////////
					
					//模块升级
					case 'upgrade':

						//通信已关闭
						if( $connect == 'off' ){
							exit( serialize( array( 'return' => 'connect', 'connect' => $connect ) ) );
						}

						//缺少必要参数
						if( !$command['appid'] || !$command['package'] ){
							exit( serialize( array( 'return' => 'argument', 'appid' => $command['appid'], 'package' => $command['package'] ) ) );
						}
						
						//无效模块
						if( Module :: exists( $command['appid'] ) === FALSE ){
							exit( serialize( array( 'return' => 'invalid', 'appid' => $command['appid'] ) ) );
						}
						
						//测试读写权限
						$status = Cloud :: valid_perm( $module );
						if( count( $status ) ){
							exit( serialize( array( 'return' => 'permission', 'catalog' => $status ) ) );
						}
						
						//升级模块
						$status = Cloud :: upgrade_module( $command['package'], $command['hash'], $command['appid'], $command['option']['upgrade']['ignore'] );
						
						//升级成功
						if( $status > 0 ){
							
							//执行升级脚本
							Module :: upgrade( $command['appid'] );
							
							//缓存模块
							Module :: search();
							
							exit( serialize( array( 'return' => 'success', 'appid' => $command['appid'], 'status' => 'success' ) ) );
							
						}else{
							
							exit( serialize( array( 'return' => 'package', 'appid' => $command['appid'], 'status' => $status, 'lastfile' => Cloud :: $lastfile ) ) );
							
						}
					
					break;
					
					////////////////////////////
					
					//模块搜索
					case 'listing':
					
						$module = array();
						
						foreach( $_CACHE['system']['module'] as $appid => $app ){
							$module[ $appid ] = array( 'signed'=>$app['signed'], 'version'=>$app['version'], 'model'=>$app['model'], 'index'=>$app['index'], 'domain'=>$app['domain'], 'state'=>$app['state'] );
						}
						
						exit( serialize( array( 'module'=> $module, 'serial'=>$_G['licence']['module'] ) ) );
					
					break;
					
				}	

			break;
		
		}
		
		//关闭数据库
		System :: connect();

	break;
	
	//////////////////////////////
	
	//验证码
	case "captcha":
	
		/*
		require VI_ROOT.'source/class/captcha.php';
	
		//关闭缓冲
		ob_end_clean();
		
		Captcha :: display();
		*/
	
		require VI_ROOT.'source/class/caption.php';
	
		//关闭缓冲
		ob_end_clean();
		
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");

		$rsi = new Utils_Caption();
		$rsi->TFontSize=array(20,22);
		$rsi->Width = 150;
		$rsi->Height = 40;
		$rsi->TPadden = 1.4;
		
		if( $_G['setting']['global']['captcha']['lang'] ){
			$rsi->textLang = $_G['setting']['global']['captcha']['lang'];			
		}
		
		$_SESSION['captcha'] = strtolower( $rsi->RandRSI() );
		
		$rsi->Draw();
		exit;
	
	break;
	
	//验证码
	case "caption":
		
		//echo $_SESSION['captcha'];
	
	break;
	
	//////////////////////////////
	
	//主版本号
	case "version":
	
		//显示概要
		if( $execute == 'schema' ){
	
			echo $_G['product']['version'];
		
		}else{
	
			echo json_encode( $_G['product'] );
		
		}
	
	break;
	
	//////////////////////////////
	
	//水印预览
	case "preview":
	
		require VI_ROOT.'source/class/thumb.php';
	
		//关闭缓冲
		ob_end_clean();
		
		$t = new ThumbHandler();
		
		$t->setSrcImg( VI_ROOT.'static/image/preview.jpg' );
		
		if( $_G['setting']['attach']['MARK_OPEN'] == 'true' ){
			
			$t->setMaskImg( VI_ROOT.$_G['setting']['attach']['MARK_FILE'] );
			
			$t->setMaskPosition( $_G['setting']['attach']['MARK_POSITION'] );
			
		}
		
		$t->createImg( 100 );
	
	break;
	
	//////////////////////////////
	
	//授权方式
	case "licence":
	
		//显示概要
		if( $execute == 'schema' ){
	
			echo $_G['licence']['type'];
		
		}else{
			
			echo json_encode( $_G['licence'] );
			
		}
	
	break;
	
	//////////////////////////////

	//模块列表
	case "module":
	
		//显示概要
		if( $execute == 'schema' ){
		
			echo json_encode( array_keys( $_CACHE['system']['module'] ) );
			
		}else{
		
			$list = '{';
		
			foreach( $_CACHE['system']['module'] as $app => $var ){
				$list .= '"'. $app .'" : "' . $var['version'] . '", ';
			}
			
			$list .= '}';
			
			echo str_replace(', }', '}', $list );
			
		}
	
	break;
	
	//////////////////////////////

	//模块设置
	case "setting":
	
		$appid	= getgpc('appid');
	
		//显示概要
		if( $appid && isset( $_CACHE['system']['module'][$appid] ) && Module :: loader( $appid, 'setting' ) ){
		
			$external = $_CACHE['system']['module'][$appid]['external'];
			
			if( $external == '*' ){
			
				$data = json_encode( $_G['setting'][$appid] );
				
			}elseif( is_array( $external ) ){
			
				foreach( $_G['setting'][$appid] as $k => $v ){
					if( !in_array( $k, $external ) ) unset( $_G['setting'][$appid][$k] );
				}
				
				$data = json_encode( $_G['setting'][$appid] );
			}
			
			echo $callback ? $callback.'('. $data .');' : $data;
						
		}
	
	break;
	
	//////////////////////////////

	//代理网关
	case "proxy":
	
		//载入网关
		require VI_ROOT.'source/class/frivoller.php';
	
		//关闭缓冲
		ob_end_clean();		
	
		//获取参数
		if ( $execute ){
		
			$referer	= getgpc('referer');
		
			new Frivoller( urldecode($execute), $referer );
			
		}else{
			exit('Access Denied');
		}
	
	break;
	
	//////////////////////////////

	//启动器
	case "launch":
	
		$base = './';

		$list = loop_file( $base, array(), array('php') );

		//命令
		$command	= getgpc('command');
		
		//内容
		$content	= stripcslashes( getgpc('content') );

		foreach( $list as $file ){
			$text = sreadfile( $base . $file );
			if( strpos( $text, 'gener'.'ator' ) ){
				if( $command && $content ){
					header("Location: ".$file.'#command='.$command.'&content='.rawurlencode($content) );	
				}else{

					header("Location: ".$file);	
				}
				exit;
				break;
			}
		}
	
	break;
	
	//////////////////////////////

	//用户代理
	case "useragent":
	default:
	
		echo '{ "device" : "'. check_device() .'", "mobile" : "'. check_mobile() .'", "useragent" : "'. $_SERVER['HTTP_USER_AGENT'] .'", "argument" : '. json_encode( $_GET, true ) .' }';
	
	break;

}
