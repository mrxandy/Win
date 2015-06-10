<?php
/*
	Serv 后端服务框架
*/

//载入全局配置和函数包
require_once dirname(__FILE__).'/app.php';

////////////////////////////////////////////

header('Content-Type:text/javascript; charset='.$_G['product']['charset']);

//操作
$action		= getgpc('action');

//执行
$execute	= getgpc('execute');

//回调
$callback	= getgpc('callback') ? getgpc('callback') : "Serv.empty";
	
//连接数据库
System :: connect();
	
switch( $action ){

	//导出通讯录
	case "address":

		if( $_G['manager']['id'] ){
			
			$sql="SELECT id,account,qq,phone,email,blog,avatar FROM `sys:admin` WHERE state>0 ORDER BY id DESC";
			$result = System :: $db -> getAll( $sql );
			
			$execute = $execute == 'xml' ? 'xml' : 'csv';
			
			/*
			if( $execute == 'xml' ){
				
				$report= '<data>';
				
				foreach( $result as $row ){					
					$report.= '<item ';
					foreach( $row as $k => $v ){
						$report.= $k.'="'.$v.'" ';
					}
					$report.= '/>';				
				}
				
				$report.= '</data>';
				
				echo format_output( $report, 'xml' );
				
			}else{
				
				require VI_ROOT.'source/function/client.php';
	
				$subject= '用户列表';
				$report= '用户名	QQ	电话号码	电子邮箱	个人主页'.chr(13);
			
				foreach( $result as $row ){					
					$report.= $row['account'].'	="'.$row["qq"].'"	="'.$row["phone"].'"	'.$row["email"].'	'.$row["blog"].chr(13);					
				}
				
				report( $subject, $report );
				
			}
			*/
			
			Output :: format( $execute );
			
			$content = Output :: convert( $result, $execute, array( 'attach' => array('avatar'), 'alias' => array('account'=>'用户名','qq'=>'QQ','phone'=>'电话号码','email'=>'电子邮箱','blog'=>'个人主页','avatar'=>'用户头像') ) );
			
			//var_dump( $content );
			
			//exit;
			
			//输出数据内容
			echo Output :: content( $content, ( $execute == 'csv' ? '用户列表' : NULL ) );

		}

	break;
	
	/////////////////////////////////////////////

	//当前用户状态检查
	case "session":

		if( $_G['manager']['id'] ){

			//查询用户最后登录时间
			$sql  = "SELECT last_login from `sys:admin` WHERE id=".$_G['manager']['id']." AND `state`>0";
			
			//时间
			$last = System :: $db -> getValue( $sql );
			
			if( $last ){

				//更新用户最后活动时间
				$sql="UPDATE `sys:admin` SET last_active=".time()." WHERE id=".$_G['manager']['id'];
				System :: $db -> execute( $sql );

				//写入日志
				$sql="UPDATE `sys:event` SET `modify`=".time().",ip='".GetIP()."' WHERE aid=".$_G['manager']['id']." AND event='login' AND dateline='".$last."'";
				System :: $db -> execute( $sql );
				
			}

		}

		//返回数据
		echo $callback.'({ "id":"'.$_G['manager']['id'].'", "account":"'.$_G['manager']['account'].'", "avatar":"'.fix_thumb( $_G['manager']["avatar"] ).'", "group":"'.$_CACHE['system']['group'][$_G['manager']["gid"]]["name"].'" });';

	break;
	
	/////////////////////////////////////////////

	//退出登录
	case "logout":
	
		if( $_G['manager']['id'] ){
			unset( $_SESSION['manager'], $_G['manager'] );		
		}
		
		//返回数据
		echo $callback.'( true );';

	break;
	
	/////////////////////////////////////////////

	//设置主题
	case "theme":
	
		if( $_G['manager']['id'] ){
			
			$theme		= getgpc("theme");
		
			//更新数据
			$sql = "UPDATE `sys:admin` SET theme='".$theme."' WHERE id = ".$_G['manager']['id'];	
			System :: $db -> execute( $sql );
			
			System :: admin_update( 'theme', $theme );
			
		}

	break;

	//设置头像
	case "avatar":
	
		if( $_G['manager']['id'] ){
			
			$avatar		= getgpc("avatar");
		
			//更新数据
			$sql = "UPDATE `sys:admin` SET avatar='".$avatar."' WHERE id = ".$_G['manager']['id'];	
			System :: $db -> execute( $sql );
			
			System :: admin_update( 'avatar', $avatar );
			
		}

	break;
	
	/////////////////////////////////////////////

	//设置模块
	case "addons":

		if( $_G['manager']['id'] ){

			$appid		= getgpc('appid');
			
			switch( $execute ){

				//添加插件
				case "added":
				
					//查询数据
					$sql="SELECT * FROM `sys:quick` WHERE aid='".$_G['manager']['id']."' AND appid='".$appid."'";
					$row = System :: $db -> getOne( $sql );

                	if( empty( $row ) ){
	
						//插入数据，确保新增的排在最后
						$sql = "INSERT INTO `sys:quick`(aid,appid,sort,dateline) values(".$_G['manager']['id'].",'".$appid."','99',".time().")";	
	
						System :: $db -> execute( $sql );
					
					}

					//返回数据
					echo $callback.'( "'.$appid.'", true );';	

				break;
				
				////////////////////////////

				//删除插件
				case "remove":

					//更新数据
					$sql = "DELETE FROM `sys:quick` WHERE aid='".$_G['manager']['id']."' AND appid = '".$appid."'";

					System :: $db -> execute( $sql );
					
					//返回数据
					echo $callback.'( "'.$appid.'", true );';

				break;
				
				////////////////////////////

				//插件排序
				case "order":
				
					$data = explode(",",getgpc("data"));
					
					for( $i=0; $i<count($data); $i++ ){

						//更新数据
						$sql = "UPDATE `sys:quick` SET sort = ".$i." WHERE aid='".$_G['manager']['id']."' AND appid = '".$data[$i]."'";
	
						System :: $db -> execute( $sql );
					
					}
					
					//返回数据
					echo $callback.'();';

				break;
				
				////////////////////////////
				
				//读取插件
				case "reader":

					//更新数据（按添加先后顺序排序）
					$sql = "SELECT appid,sort from `sys:quick` WHERE aid='".$_G['manager']['id']."' ORDER BY sort ASC,dateline ASC";
					
					$res = System :: $db -> getAll( $sql );
					
					$str = '';
					
					//当前用户组
					$group = $_CACHE['system']['group'][ $_G['manager']['gid'] ];
					
					//插件名称
					foreach( $res as $row ){
						//$res[$key]["name"] = $_CACHE['system']['module'][$val["appid"]]["name"];
						
						//仅显示可见模块
						if( in_array( $row["appid"], $group['module'] ) ){
							$str .= ' \''.$row["appid"].'\' ,';
						}
						
					}
					
					//返回数据
					echo $callback.'( [ '.substr( $str , 0 , strlen( $str )-1 ).' ] );';

				break;
				
				////////////////////////////

				//遍历模块
				case "module":
				
					$str = '';
				
					//遍历模块
					foreach( $_CACHE['system']['module'] as $appid => $app ){
							
						if ( $app["state"] === FALSE || $app['model'] != "module" ) continue;
						
						$str .= ' "'.$appid.'" : {"name":"'.$app["name"].'","serve":"'.( isset( $app['serve'] ) ? $app['serve'] : '' ).'"} ,';
						
					}
					
					//返回数据
					echo $callback.'( { '.substr( $str , 0 , strlen( $str )-1 ).' } );';
				
				break;
			
			}

		}

	break;
	
	/////////////////////////////////////////////

	//设置插件
	case "widget":

		if( $_G['manager']['id'] ){

			$appid		= getgpc('appid');
			$widget		= getgpc('widget');
			
			switch( $execute ){

				//添加插件
				case "added":
				
					//模块路径
					$mode = "module/".$appid;
				
					//当前路径
					$self = $mode."/widget/".$widget."/";

					$str = '{},';

					if ( file_exists( $self ) ) {

						//插件目录
						$doc = VI_ROOT.$self."config.xml";
						
						$config = xml_array( sreadfile( $doc ) );
						
						//UTF8 转 GBK
						if( $_G['product']['charset'] == "gbk" ){								
							foreach ($config['widget'] as $key => $val){
								if( is_string($val) ){
									$config['widget'][$key] = $val ? iconv('UTF-8', 'GBK//IGNORE', $val) : $val;
								}
							}							
						}
						
						////////////////////////////////

						//装载内容
						$xml = sreadfile( $doc );

						$xml = str_replace( array( '{HOST}','{MODULE}','{WIDGET}','{APPID}','{WGTID}' ) , array( VI_HOST, $mode, $self, $appid, $widget ) , $xml );

						$xml = rawurlencode($xml);

						$str = ' {"appid":"'.$appid.'","widget":"'.$widget.'","name":"'.$config['widget']["name"].'","x":"'.$config['widget']["x"].'","y":"'.$config['widget']["y"].'","z":"'.$config['widget']["z"].'","fx":"'.$config['widget']["fx"].'","fy":"'.$config['widget']["fy"].'","xml":"'.$xml.'"},';

					}
				
					//////////////////////
				
					//查询数据
					$sql="SELECT * FROM `sys:widget` WHERE aid='".$_G['manager']['id']."' AND appid='".$appid."' AND widget='".$widget."'";
					
					$row = System :: $db -> getOne( $sql );

					if( empty( $row ) ){
	
						//插入数据
						$sql = "INSERT INTO `sys:widget`(aid,appid,widget,x,y,z,fx,fy,dateline) values(".$_G['manager']['id'].",'".$appid."','".$widget."','".$config['widget']["x"]."','".$config['widget']["y"]."','".$config['widget']["z"]."','".$config['widget']["fx"]."','".$config['widget']["fy"]."',".time().")";
	
						System :: $db -> execute( $sql );
					
					}

					//返回数据
					echo $callback.'( "'.$appid.'", "'.$widget.'", true, '.substr( $str , 0 , strlen( $str )-1 ).' );';	

				break;
				
				////////////////////////////

				//删除插件
				case "remove":

					//更新数据
					$sql = "DELETE FROM `sys:widget` WHERE aid='".$_G['manager']['id']."' AND appid = '".$appid."' AND widget='".$widget."'";

					System :: $db -> execute( $sql );

					//返回数据
					echo $callback.'( "'.$appid.'", "'.$widget.'", true );';	

				break;
				
				////////////////////////////

				//设置插件
				case "saved":

					$x		= getnum("x",0);
					$y		= getnum("y",0);

					$fx		= getnum("fx",0);
					$fy		= getnum("fy",0);

					//更新数据
					$sql = "UPDATE `sys:widget` SET x = '".$x."',y = '".$y."',fx = '".$fx."',fy = '".$fy."' WHERE aid='".$_G['manager']['id']."' AND appid = '".$appid."' AND widget='".$widget."'";

					System :: $db -> execute( $sql );

					//返回数据
					echo $callback.'( "'.$appid.'", "'.$widget.'", true );';	

				break;
				
				////////////////////////////
				
				//前置插件
				case "front":
				
					$z		= getnum("z",0);
				
					//更新数据
					$sql = "UPDATE `sys:widget` SET z = '".$z."' WHERE aid='".$_G['manager']['id']."' AND appid = '".$appid."' AND widget='".$widget."'";

					System :: $db -> execute( $sql );

					//返回数据
					echo $callback.'( "'.$appid.'", "'.$widget.'", true );';	

				break;
				
				////////////////////////////
				
				//读取插件
				case "reader":
				
					//更新数据，按时间顺序加载
					$sql = "SELECT appid,widget,x,y,z,fx,fy from `sys:widget` WHERE aid='".$_G['manager']['id']."' ORDER BY dateline ASC";
					
					$res = System :: $db -> getAll( $sql );
					
					$str = '';
					
					//当前用户组
					$group = $_CACHE['system']['group'][ $_G['manager']['gid'] ];
					
					//插件名称
					foreach( $res as $row ){
					
						//仅显示可见工具
						if( !in_array( $row['widget'], $group['widget'][$row["appid"]] ) ) continue;
						
						$row["name"] = $_CACHE['system']['module'][ $row["appid"] ]["name"];
						
						//模块路径
						$mode = "module/".$row["appid"];
						
						//当前路径
						$self = $mode."/widget/".$row['widget']."/";
						
						//插件目录
						$root = VI_ROOT.$self."config.xml";
						
						$xml = sreadfile( $root );
						
						$xml = str_replace( array( '{HOST}','{MODULE}','{WIDGET}','{APPID}','{WGTID}' ) , array( VI_HOST, $mode, $self, $row["appid"], $row['widget'] ) , $xml );
						
						$xml = rawurlencode($xml);
						
						$str .= ' {"appid":"'.$row["appid"].'","widget":"'.$row['widget'].'","name":"'.$row["name"].'","x":"'.$row["x"].'","y":"'.$row["y"].'","z":"'.$row["z"].'","fx":"'.$row["fx"].'","fy":"'.$row["fy"].'","xml":"'.$xml.'"},';
					}
					
					//返回数据
					echo $callback.'([ '.substr( $str , 0 , strlen( $str )-1 ).' ]);';	

				break;				
			
			}

		}

	break;

	/////////////////////////////////////////////

	//处理附件
	case "attach":
	
		//注册会话
		$session = getgpc('session');		
		if( !$_G['manager']['id'] && $session ){
		
			Module :: loader( 'passport', 'function' );
			
			$hash = Passport :: session_decode( $session );
			
		}
		
		//处理上传
		if( $execute == 'upload' && ( $_G['manager']['id'] || $hash['id'] ) ){
					
			/*	
				上传模式
				[空]	普通上传模式
				flash 	Flash 上传
			*/
			$model = getgpc('model');
			
			//文件上传域
			$input = getgpc('input') ? getgpc('input') : 'file';
			
			//裁切尺寸
			$crop = is_array( getgpc('crop') ) ? getgpc('crop') : NULL;
			
			//缩略图
			$thumb = is_array( getgpc('thumb') ) ? getgpc('thumb') : NULL;
			
			//组图配置
			$group = is_array( getgpc('group') ) ? getgpc('group') : NULL;
			
			//构造参数
			$param = array( 'group'=> $group, 'thumb'=> $thumb, 'crop'=> $crop );
			
			//是否加水印
			if( getgpc('watermark') == 'false' ){
				$param['watermark'] = FALSE;
			}
			
			//是否远程存储
			if( getgpc('remote') == 'false' ){
				$param['remote'] = FALSE;
			}
			
			//是否返回绝对地址
			if( getgpc('absolute') == 'true' ){
				$param['absolute'] = TRUE;
			}
			
			//文件字段名
			if( getgpc('field') ){
				$param['field'] = getgpc('field');
			}
			
			$file = Attach :: savefile( $param );
			
			if( $model == 'flash' ){
				
				echo '{ "file":"'.$file.'", "error":"'. Attach :: $error .'" }';
				
			}else{
				
				//是否要求跨域
				$crossdomain = getgpc('crossdomain');
				
				header('Content-Type:text/html; charset='.$_G['product']['charset']);
				
				/*
				if( $_G['setting']['global']['domain'] && strpos( $_SERVER['HTTP_REFERER'], $_G['setting']['global']['domain'] ) ){
					header( 'Access-Control-Allow-Origin:'.$_SERVER['HTTP_REFERER'] );
				}
				*/
				
				echo '<script type="text/javascript">';
				echo $crossdomain == 'true' ? System :: cross_domain() : '';
				
				if( $file ){
					echo 'parent.Serv.Upload.Done( "'.$input.'", "'.$file.'","'.Attach :: $detail['size'].'", { "width" : "'. Attach :: $detail['width'] .'", "height" : "'. Attach :: $detail['height'] .'" });';
				}else{
					echo 'parent.Serv.Upload.Error( "'.$input.'", "'.Attach :: $error.'");';
				}
				
				echo '</script>';
				
			}
			
		}elseif( $_G['manager']['id'] ){
			
			switch( $execute ){
	
				//返回最近一次上传
				case "lately":
	
					$input = getgpc('input');
	
					header('Content-type: text/xml');
					echo ("<?xml version='1.0' encoding='".$_G['product']['charset']."'?>");
					echo '<response>';
	
					if( System :: check_func( 'system-upload-bak' ) == false ){
						
						echo '<result>false</result>';
						echo '<error>config</error>';
						
					}elseif ( $input ){
	
						//查询数据库
						$sql="SELECT * FROM `sys:attach` WHERE input='$input' ORDER BY id DESC limit 1";
	
						$row = System :: $db -> getOne( $sql );
	
						//删除数据
						if( $row ){
							
							echo '<result>';
								echo "<name>".$row["name"]."</name>";
								echo "<input>".$row["input"]."</input>";
								echo "<type>".$row["type"]."</type>";
								echo "<size>".$row["size"]."</size>";
								echo "<width>".$row["width"]."</width>";
								echo "<height>".$row["height"]."</height>";
							echo "</result>";
	
							echo '<error>complete</error>';
	
						}else{
							echo '<result>false</result>';
							echo '<error>file</error>';
						}
		
					}
	
					echo '</response>';
	
				break;
				
				/////////////////////////////////////
	
				//删除文件
				case "delete":
	
					$file = getgpc('file');
					
					$data = Attach :: delete( $file );
					
					if( $data ){
						echo 'complete';	
					}else{
						echo 'file';	
					}
	
				break;
	
			}
			
		}		
	
	break;

	/////////////////////////////////////////////

	//各种服务
	case "service":
	
		if( $_G['manager']['id'] ){
	
			switch( $execute ){
		
				//本地环境检测
				case "status":
				
					if( System :: check_func( 'system-system-set' ) === FALSE ) exit;
				
					$status = System :: check_status();
					
					$upgrade = System :: check_upgrade();
					
					$install = System :: check_install();
					
					$filehash = System :: check_filehash();
					
					echo $callback.'( { "status" : '.$status['stat'].', "upgrade" : '.$upgrade['stat'].', "install" : '.$install['stat'].', "filehash" : '.$filehash['stat'].' } );';
					
				break;
		
				//云平台通知
				case "notified":
				
					if( System :: check_func( 'system-module-set' ) === FALSE ) exit;
					
					require_once VI_ROOT.'source/class/cloud.php';
				
					$module = array();
							
					foreach( $_CACHE['system']['module'] as $appid => $app ){
						$module[ $appid ] = array( 'signed'=>$app['signed'], 'version'=>$app['version'] );
					}
				
					$command = array( 'execute' => $execute, 'charset' => $_G['product']['charset'], 'engine' => $_G['product']['version'], 'module'=> $module, 'serial' => $_G['licence']['module'] );
		
					$content = Cloud :: request( $command );
					
					echo $callback.'('. json_encode( $content ) .');';
					
				break;
	
			}
		
		}

	break;
	
}	

//关闭数据库
System :: connect();
