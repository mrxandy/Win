<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	系统上传函数库
*/

class Attach{
	
	/*
		最近一次上传文件信息
	*/
	public static $detail = NULL;
	
	/*
		最近一次上传错误编号
		0，没有错误发生，文件上传成功。 
		1，上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。 
		2，上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。 
		3，文件只有部分被上传。 
		4，没有文件被上传。 
		5，上传的文件是空的（大小为 0）。 
		6，找不到临时文件夹。PHP 4.3.10 和 PHP 5.0.3 引进。 
		7，文件写入失败。PHP 5.1.0 引进。 
	*/
	public static $error = 0;

	/*
		检查上传文件
		$filename		上传文件名
		$filetype		允许的文件分类，参考 $_G['upload']
	*/
	public static function checkfile( $filename, $filetype = '*' ){
		global $_G;
		
		$extra = fileext( $filename );
		
		if( $filetype == '*' ){
			
			foreach( $_G['upload'] as $item => $array ){
				if( in_array( $extra, $array ) ){
					return array( 'type' => $item, 'extra' => $extra );
				}	
			}
			
		}else{
			
			if( isset( $_G['upload'][$filetype] ) && in_array( $extra, $_G['upload'][$filetype] ) ){
				return array( 'type' => $filetype, 'extra' => $extra );
			}
			
		}
		
		//未知格式
		return FALSE;
	}
	
	public static function savemulti( $param = array() ){
		
		$param['field'] || $param['field'] = 'file';
		$stat = count( $_FILES[$param['field']]["name"] );		
		$data = array();		
		
		for( $i = 0; $i < $stat; $i++ ){
			
			$param['index'] = $i;
		
			$data[] = self :: savefile( $param );
		
		}
		
		return $data;
		
	}
	
	/*
		保存上传文件
		$path		保存目录
		$temp		临时文件
		$name		本地名称
		$ext		扩展名
		$replace	重名处理方式：true 替换；false 重命名
	*/
	public static function savefile( $param = array() ){
		global $_G;
		
		require_once VI_ROOT.'source/class/thumb.php';
		require_once VI_ROOT.'source/class/ftp.php';
		
		if( $_G['setting']['global']['upload'] != 'on' ) return FALSE;
		
		$default = array( 
				   'field' => 'file', 			//文件域
				   'model' => 'normal', 		//处理模式（normal 或 flash）
				   'crop' => NULL, 				//裁切尺寸，数组：array( 100, 100 )
				   'thumb' => NULL, 			//缩略图尺寸，数组：array( 100, 100 )
				   'group' => NULL, 			//组图尺寸
				   'index' => -1, 				//单文件上传模式
				   'absolute' => FALSE, 		//是否返回绝对地址
				   'account' => 'manager', 		//账号体系（manager 或 member）
				   'filetype' => '*', 			//允许的文件类型
				   'remote' => ( $_G['setting']["attach"]["FTP_OPEN"] == 'true' ), 	//是否使用远程存储
				   'watermark' => ( $_G['setting']['attach']['MARK_OPEN'] == 'true' ? VI_ROOT.$_G['setting']['attach']['MARK_FILE'] : NULL ), 			//水印图片
				   'position' => $_G['setting']['attach']['MARK_POSITION'], 		//水印位置
				   'multiple' => $_G['setting']['attach']['MARK_MULTIPLE']	 		//原图是水印图尺寸的几倍时打上水印
				   );
		
		$param = array_merge( $default, $param );
		
		////////////////////////
		
		if( $param['index'] == -1 ){
			
			$temp = $_FILES[ $param['field'] ];
			
		}else{
			
			$temp = array( 
					'name'=> $_FILES[ $param['field'] ]["name"][ $param['index'] ], 
					'tmp_name'=> $_FILES[ $param['field'] ]["tmp_name"][ $param['index'] ],
					'size'=> $_FILES[ $param['field'] ]["size"][ $param['index'] ],
					'error'=> $_FILES[ $param['field'] ]["error"][ $param['index'] ]
					);
		}
		
		//var_dump( $temp );
		
		//上传出错
		if( !isset( $temp ) || $temp['error'] ){
			self :: $error = $temp['error'];
			return FALSE;	
		}
		
		////////////////////////
		
		$data = self :: checkfile( $temp['name'], $param['filetype'] );
		
		//未知格式
		if( $data === FALSE ) return FALSE;
		
		//源图信息
		if( $data['type'] == 'image' ){
		
			list( $param['origin']['width'], $param['origin']['height'] ) = getimagesize( $temp['tmp_name'] );
			$width  = $param['origin']['width'];
			$height = $param['origin']['height'];
			
			//裁切尺寸与原图尺寸一样时不裁切
			if( $param['crop'] && $param['crop'][0] == $width && $param['crop'][1] == $height ){
				$param['crop'] = NULL;
			}
			
		}else{
			$param['origin'] = NULL;
			$width = $height = 0;
		}		
				
		//水印信息
		if( $param['watermark'] && file_exists( $param['watermark'] ) ){
			list( $param['water']['width'], $param['water']['height'] ) = getimagesize( $param['watermark'] );
		}else{
			$param['water'] = NULL;
		}
		
		$file = $param['remote'] ? self :: stored_remote( $temp['tmp_name'], $data, $param ) : self :: stored_locale( $temp['tmp_name'], $data, $param );
		
		////////////////////////
		
		//记录到最近（宽度，高度，本地文件名，上传文件名，扩展名，文件大小）
		self :: $detail = array( 'width'=> $width, 'height'=> $height, 'name'=> $temp['name'], 'file'=> $file, 'extra'=> $data['extra'], 'size'=> $temp['size'] );
		
		self :: $error = 0;
		
		////////////////////////
		
		if( $param['account'] == 'member' ){
			$user = array( 'id' => $_G['member']['id'], 'name' => $_G['member']['username'] );
		}else{
			$user = array( 'id' => $_G['manager']['id'], 'name' => $_G['manager']['account'] );
		}
		
		//写入数据库
		$sql="INSERT INTO `sys:attach`(aid,account,name,input,dateline,type,size,ip,width,height,remote) values('".$user['id']."','".$user['name']."','".$file."','".$param['field']."',".time().",'".$data['extra']."',".$temp['size'].",'".GetIP()."',$width,$height,".intval($param['remote']).")";
		
		System :: $db -> execute( $sql );
		
		////////////////////////
		
		//绝对地址
		if( $param['absolute'] && $param['remote'] == FALSE ){
			return substr_replace( $file, VI_HOST, 0, strlen(VI_BASE) );
		}else{
			return $file;
		}
		
	}
	
	/*
		本地存储
		$temp		本地临时文件
		$data		本地临时文件
		$param		参数配置
	*/
	public static function stored_locale( $temp, $data, $param ){
		global $_G;

		//子文件夹
		$base = VI_ROOT.'attach/'.$data['type'];
		
		//创建目录
		$path = create_dir( $base.'/'.date('Y/md/') );
		
		//添加标识符
		$mark = $param['origin'] ? mt_rand(1000,9999) .'-'. $param['origin']['width'] . '-' . $param['origin']['height'] : mt_rand();
		
		//构造文件名
		$file = $path.date('H-i-s-') . $mark . '.' . $data['extra'];
		
		//移动文件
		move_uploaded_file( $temp, $file );
		
		////////////////////////////////
		
		if( $data['type'] == 'image' ){
			
			//缩略图            
			$t = new ThumbHandler();
			
			//生成缩略
			if( is_array( $param['thumb'] ) && $param['thumb'][0] > 0 ){
				
				//源图片地址
				$t->setSrcImg( $file );
				
				//居中剪切
				$t->cut_type = 3;
				
				//输出文件名
				$t->setDstImg( fix_thumb( $file ) );
				
				//给缩略_打上水印
				/*
				if( $param['water'] && ( $param['thumb'][0] / $param['water']['width'] > $param['multiple'] ) && ( $param['thumb'][1] / $param['water']['height'] > $param['multiple'] ) ){
					
					$t->setMaskImg( $param['watermark'] );
					$t->setMaskPosition( $param['position'] );
				
				}
				*/
				
				//生成图片
				$t->createImg( $param['thumb'][0] , $param['thumb'][1] );
			
			}				
	
			//生成组图
			if( is_array( $param['group'] ) ){
				
				foreach( $param['group'] as $thumb ){
					
					//$thumb = explode("*",$g);
					
					if( is_array($thumb) && $thumb[0] > 0 ){
			
						//源图片地址
						$t->setSrcImg( $file );
			
						//输出文件名
						$t->setDstImg( fix_thumb( $file, $thumb ) );
				
						//给组图_打上水印
						/*
						if( $param['water'] && ( $thumb[0] / $param['water']['width'] > $param['multiple'] ) && ( $thumb[1] / $param['water']['height'] > $param['multiple'] ) ){
							
							$t->setMaskImg( $param['watermark'] );
							$t->setMaskPosition( $param['position'] );
						
						}
						*/
				
						//生成图片
						$t->createImg( $thumb[0] , $thumb[1] );
					
					}
				
				}
	
			}
			
			//裁切原图
			if( is_array( $param['crop'] ) && $param['crop'][0] > 0 ){
	
				//源图片地址
				$t->setSrcImg( $file );
				
				//居中剪切
				$t->cut_type = 3;
				
				//输出文件名
				$t->setDstImg( $file );
	
				//生成图片
				$t->createImg( $param['crop'][0] , $param['crop'][1] );
				
				list( $param['origin']['width'], $param['origin']['height'] ) = $param['crop'];
	
			}
				
			//给原图_打上水印
			if( $param['water'] && ( $param['origin']['width'] / $param['water']['width'] > $param['multiple'] ) && ( $param['origin']['height'] / $param['water']['height'] > $param['multiple'] ) ){
				
				//源图片地址
				$t->setSrcImg( $file );
				
				//输出文件名
				$t->setDstImg( $file );					
				
				$t->setMaskImg( $param['watermark'] );
				$t->setMaskPosition( $param['position'] );
				
				//生成图片
				$t->createImg( 100 );
			
			}
			
		}
		
		//相对地址
		$file = str_replace( VI_ROOT, VI_BASE, $file );
						
		//上传成功
		return $file;
		
	}
	
	/*
		远程存储
		$temp		本地临时文件
		$data		本地临时文件
		$param		参数配置
	*/
	public static function stored_remote( $temp, $data, $param ){
		global $_G;

		//连接 FTP
		$ftp = new ClsFTP( $_G['setting']['attach']['FTP_USER'], $_G['setting']['attach']['FTP_PASS'], $_G['setting']['attach']['FTP_HOST'], $_G['setting']['attach']['FTP_PORT'] );
		
		//FTP 模式
		$ftp->pasv( $_G['setting']['attach']['FTP_PASV'] == 'true' );
		
		//创建子文件夹
		$dir = $_G['setting']['attach']['FTP_ROOT'].'/'.$data['type'];
		
		//创建根目录
		$ftp->mkdir($dir);
		
		//创建年文件夹
		$ftp->mkdir($dir."/".date("Y/"));
		
		//创建月日文件夹
		$ftp->mkdir($dir."/".date("Y/md/"));
		
		//添加标识符
		$mark = $param['origin'] ? mt_rand(1000,9999) .'-'. $param['origin']['width'] . '-' . $param['origin']['height'] : mt_rand();
		
		//新文件名称
		$name = date("Y/md/")."/".date("H-i-s-"). $mark .".".$data['extra'];
		
		/*****************/
		
		if( $data['type'] == 'image' ){

			//缩略图            
			$t = new ThumbHandler();
				
			//生成缩略
			if( is_array( $param['thumb'] ) && $param['thumb'][0] > 0 ){
				
				$local = fix_thumb( $temp );
				
				//源图片地址
				$t->setSrcImg( $temp );
				
				//输出文件名
				$t->setDstImg( $local );
				
				//给缩略_打上水印
				if( $param['water'] && ( $param['thumb'][0] / $param['water']['width'] > $param['multiple'] ) && ( $param['thumb'][1] / $param['water']['height'] > $param['multiple'] ) ){
					
					$t->setMaskImg( $param['watermark'] );
					$t->setMaskPosition( $param['position'] );
				
				}
				
				//生成图片
				$t->createImg( $param['thumb'][0] , $param['thumb'][1] );
				
				//上传文件
				$post = $ftp->put( $dir.'/'.fix_thumb( $name ) , $local );
				
				//删除临时文件
				unlink( $local );
			
			}
			
			/*
			var_dump( $temp );
			var_dump( fix_thumb( $temp ) );
			var_dump( $dir.'/'.fix_thumb( $name ) );
			exit;
			exit;
			*/
		
			//生成组图
			if( is_array( $param['group'] ) ){
				
				foreach( $param['group'] as $g ){
					
					$thumb = explode("*",$g);
					
					if( is_array($thumb) && $thumb[0] > 0 ){
						
						$local = fix_thumb( $temp, $thumb );
			
						//源图片地址
						$t->setSrcImg( $temp );
			
						//输出文件名
						$t->setDstImg( $local );
				
						//给组图_打上水印
						if( $param['water'] && ( $thumb[0] / $param['water']['width'] > $param['multiple'] ) && ( $thumb[1] / $param['water']['height'] > $param['multiple'] ) ){
							
							$t->setMaskImg( $param['watermark'] );
							$t->setMaskPosition( $param['position'] );
						
						}
				
						//生成图片
						$t->createImg( $thumb[0] , $thumb[1] );						
				
						//上传文件
						$post = $ftp->put( $dir.'/'.fix_thumb( $name, $thumb ) , $local );
						
						//删除临时文件
						unlink( $local );			
					
					}
				
				}
	
			}			
			
			//裁切原图
			if( is_array( $param['crop'] ) && $param['crop'][0] > 0 ){
	
				//源图片地址
				$t->setSrcImg( $temp );
	
				//输出文件名
				$t->setDstImg( $temp );
	
				//生成图片
				$t->createImg( $param['crop'][0] , $param['crop'][1] );
				
				list( $param['origin']['width'], $param['origin']['height'] ) = $param['crop'];
	
			}
				
			//给原图_打上水印
			if( $param['water'] && ( $param['origin']['width'] / $param['water']['width'] > $param['multiple'] ) && ( $param['origin']['height'] / $param['water']['height'] > $param['multiple'] ) ){
				
				//源图片地址
				$t->setSrcImg( $temp );
				
				//输出文件名
				$t->setDstImg( $temp );					
				
				$t->setMaskImg( $param['watermark'] );
				$t->setMaskPosition( $param['position'] );
				
				//生成图片
				$t->createImg( 100 );
			
			}
		
		}
		
		/*****************/
		
		//上传文件
		$post = $ftp->put( $dir."/".$name, $temp );
		
		//绝对地址
		$file = url_merge($_G['setting']["attach"]["FTP_SITE"].'/'.$data['type'].'/'.$name);
		
		//关闭 FTP
		$ftp->close();
		
		return $file;
		
	}
	
	//删除文件
	public static function delete( $file, $param = array() ){
		global $_G;
		
		//检查权限
		if( System :: check_func( 'system-upload-del' ) == false ) return FALSE;
		
		//查询数据库
		$sql = "SELECT id, type, remote from `sys:attach` WHERE ( name='$file' ) LIMIT 0, 1";
		$row = System :: $db -> getOne( $sql );
		
		if( $row ){
			 
			$res = $row['remote'] ? self :: delete_remote( $file, $param ) : self :: delete_locale( $file, $param );
	
			//删除数据
			System :: $db -> execute( "DELETE FROM `sys:attach` WHERE id=".$row['id'] );
			
			return $res;
			
		}else{
			return FALSE;
		}
	
	}
	
	/*
		本地删除
	*/
	public static function delete_locale( $file, $param ){		
		global $_G;
		
		//真实路径
		$file = VI_ROOT.$file;
		
		if( in_array( fileext( $file ), $_G['upload']['image'] ) ){
			
			if( $param['thumb'] ){
				
				//删除图片缩略图
				$thumb = fix_thumb( $file );
				
				unlink($thumb);
			}
			
			if( is_array( $param['group'] ) ){
				
				foreach( $param['group'] as $g ){
					
					$thumb = explode("*",$g);
					
					if( is_array($thumb) ){
						
						//删除图片缩略图
						$thumb = fix_thumb( $file, $thumb );
						
						unlink($thumb);
						
					}
				}
				
			}
			
		}
		
		//删除文件
		$res = unlink($file);
		
		return $res;		
	}
	
	/*
		远程删除
	*/
	public static function delete_remote( $file, $param ){
		global $_G;
		
		require VI_ROOT.'source/class/ftp.php';
		
		//转换成相对路径
		$file = str_replace( $_G['setting']['attach']['FTP_SITE'], $_G['setting']['attach']['FTP_ROOT'], $file );
		
		//连接 FTP
		$ftp = new ClsFTP($_G['setting']["attach"]["FTP_USER"], $_G['setting']["attach"]["FTP_PASS"], $_G['setting']["attach"]["FTP_HOST"], $_G['setting']["attach"]["FTP_PORT"]);
		
		//FTP 模式
		$ftp->pasv( $_G['setting']['attach']['FTP_PASV'] == 'true' );
		
		if( in_array( fileext( $file ), $_G['upload']['image'] ) ){
			
			if( $param['thumb'] ){
				
				//删除图片缩略图
				$thumb = fix_thumb( $file );
				
				$ftp->delete( $thumb );
			}
			
			if( is_array( $param['group'] ) ){
				
				foreach( $param['group'] as $g ){
					
					$thumb = explode("*",$g);
					
					if( is_array($thumb) ){
						
						//删除图片缩略图
						$thumb = fix_thumb( $file, $thumb );
						
						$ftp->delete( $thumb );
						
					}
				}
				
			}
			
		}
		
		//删除文件
		$res = $ftp->delete( $file );
		
		//关闭 FTP
		$ftp->close();
		
		return $res;		
	}
	
	/*
		给图片打上水印
	*/
	public static function watermark(){
	
		//生成缩略
		if( $data['type'] == 'image' && $thumb ){
		
			//缩略图            
			$t = new ThumbHandler();
			
			//源图片地址
			$t->setSrcImg($file);
			
			//输出文件名
			$t->setDstImg( str_replace(".".$data['extra'],"-thumb.".$data['extra'],$file) );
			
			//给缩略_打上水印
			if( $_G['setting']["attach"]["MARK_OPEN"] == "true" && $param['water']['width'] && $param['water']['height'] && ( $thumb[0] / $param['water']['width'] > $param['multiple'] ) && ( $thumb[1] / $param['water']['height'] > $param['multiple'] ) ){
				
				$t->setMaskImg( $param['watermark'] );
				$t->setMaskPosition( $param['position'] );
			
			}
			
			//生成图片
			$t->createImg( $thumb[0] , $thumb[1] );
		
		}				

		//生成组图
		if( $data['type'] == 'image' && is_array( $group ) ){
			
			foreach( $group as $g ){
				
				$thumb = explode("*",$g);
				
				if( is_array($thumb) ){

					//缩略图            
					$t = new ThumbHandler();
		
					//源图片地址
					$t->setSrcImg($file);
		
					//输出文件名
					$t->setDstImg( str_replace( ".".$data['extra'], "-".$thumb[0] ."-". $thumb[1].".".$data['extra'] , $file ) );
			
					//给组图_打上水印
					if( $_G['setting']["attach"]["MARK_OPEN"] == "true" && $param['water']['width'] && $param['water']['height'] && ( $thumb[0] / $param['water']['width'] > $param['multiple'] ) && ( $thumb[1] / $param['water']['height'] > $param['multiple'] ) ){
						
						$t->setMaskImg( $param['watermark'] );
						$t->setMaskPosition( $param['position'] );
					
					}
			
					//生成图片
					$t->createImg( $thumb[0] , $thumb[1] );
				
				}
			
			}

		}				
			
		//给原图_打上水印
		if( $_G['setting']["attach"]["MARK_OPEN"] == "true" && $param['water']['width'] && $param['water']['height'] && ( $width / $param['water']['width'] > $param['multiple'] ) && ( $height / $param['water']['height'] > $param['multiple'] ) ){
			
			//缩略图            
			$t = new ThumbHandler();
			
			//源图片地址
			$t->setSrcImg($file);
			
			//输出文件名
			$t->setDstImg($file);					
			
			$t->setMaskImg( $param['watermark'] );
			$t->setMaskPosition( $param['position'] );
			
			//生成图片
			$t->createImg( $width , $height );
		
		}		
		
	}
	
	
	/*
		上传文件到服务器
		$field		文件域名称
		$index		索引名称，用于批量上传，-1 为单文件上传
		$type		限制类型，数组
		
		$config		配置
			size	大小限制
			crop	裁切尺寸
			thumb	数组，缩略图尺寸
			group	二维数组，缩略图尺寸，可以生成多张图片
		
		返回值
		1		没有上传文件或出错
		2		不被允许文件的类型
		3		文件类型不在上传配置中
		4		文件太大
		5		移动文件出错
	*/
	public static function file_upload( $field ,$index = -1 , $type , $config ){
		global $_G;
	
		//本地文件名称
		$local = $index > -1 && is_int($index) ? $_FILES[$field]["name"][$index] : $_FILES[$field]["name"] ;
		
		//错误信息
		$error = $index > -1 && is_int($index) ? $_FILES[$field]["error"][$index] : $_FILES[$field]["error"] ;
			
		//没有上传文件或出错
		if( !$local || $error ){	
			return 1;	
		}
	
		//文件大小
		$size = $index > -1 && is_int($index) ? $_FILES[$field]["size"][$index] : $_FILES[$field]["size"];
	
		//临时名称
		$temp = $index > -1 && is_int($index) ? $_FILES[$field]["tmp_name"][$index] : $_FILES[$field]["tmp_name"];
	
		//当前文件类型
		$mime = fileext($local);
	
		//不被允许的类型
		if( !in_array($mime,$type) ){
			return 2;
		}
	
		//搜索所在分类
		foreach( $_G['upload'] as $item => $array ){		
			//定位到分类
			if( in_array($mime,$array) ){
	
				//文件分类
				$cate = $item;
	
				break;
			}	
		}
	
		//文件类型不在上传配置中
		if( !$cate ) return 3;
		
		//根文件夹
		$folder = VI_ROOT.'attach/'.$cate;
		
		//创建子文件夹
		if( !file_exists($folder) ) create_dir($folder);
	
		//限制文件大小
		if( is_int($config["size"]) && $size > $config["size"] ) return 4;
		
		/*******保存文件_开始*******/	
		
		//创建目录	
		$path = create_dir( $folder."/".date("Y/md/") );
	
		//生成文件名
		$name = $path.date("H-i-s-").mt_rand().".".$mime;
	
		//移动文件失败
		if( !copy( realpath($temp) ,$name ) ) return 5;
		
		/*******保存文件_结束*******/	
	
		//图片则计算尺寸
		$width = $height = 0;
		if( $cate == "image" ){
	
			//返回图片尺寸
			list($width, $height) = getimagesize($name);
		
			//包含类
			require_once VI_ROOT.'source/class/thumb.php';
			
			//生成缩略图
			if( is_array( $config["thumb"] ) ){
	
				//缩略图            
				$t = new ThumbHandler();
	
				//源图片地址
				$t->setSrcImg($name);
	
				//输出文件名
				$t->setDstImg( str_replace( ".".$mime, "-thumb.".$mime , $name ) );
	
				//生成图片
				$t->createImg( $config["thumb"][0] , $config["thumb"][1] );
	
			}
			
			//生成组图
			if( is_array( $config["group"] ) ){
				
				foreach( $config["group"] as $g ){
					
					if( is_array($g) ){
	
						//缩略图            
						$t = new ThumbHandler();
			
						//源图片地址
						$t->setSrcImg($name);
			
						//输出文件名
						$t->setDstImg( str_replace( ".".$mime, "-".$g[0] ."-". $g[1].".".$mime , $name ) );
			
						//生成图片
						$t->createImg( $g[0] , $g[1] );
					
					}
				
				}
	
			}
			
			//生成裁切图
			if( is_array( $config["crop"] ) ){
	
				//缩略图            
				$t = new ThumbHandler();
	
				//源图片地址
				$t->setSrcImg($name);
	
				//输出文件名
				$t->setDstImg($name);
	
				//生成图片
				$t->createImg( $config["crop"][0] , $config["crop"][1] );
	
			}
	
		}
		
		//exit(  );
		
		//合并路径
		$file = url_merge( str_replace( VI_ROOT , "" , VI_BASE.$name ) );
		
		//echo $file;
		
		//exit($file);
		
		//写入数据库
		$sql = "INSERT INTO `sys:attach`(name,input,dateline,type,size,ip,width,height,remote) values('".$file."','".$field."',".time().",'".$mime."',$size,'".GetIP()."',$width,$height,0)";
		System :: $db -> execute( $sql );
		
		//返回文件名
		return $file;
	
	}

}
