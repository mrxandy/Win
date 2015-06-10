<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	内核更新及模块管理类
*/


/*
	错误状态信息
	-10001		不能下载远程附件
	-10002		不能创建临时目录
	-10003		附件可能被篡改
	-10004		不能正常解压附件
	-10005		不能移动文件到新目录
	-10006		不能删除临时文件
	-10007		文件缺少读写权限
*/
class Cloud{

	//临时文件存储目录
	public static $filehash;

	//最后一个操作文件
	public static $lastfile;
	
	//构造函数
	public static function init() { 
		self :: $filehash = VI_ROOT . 'data/filehash/'; 
	} 

	//临时目录地址
	public static function get_temp_file( $file ){
		
		return self :: $filehash . filename( $file, FALSE ) . '/';
		
	}

	//下载文件包地址
	public static function get_pack_file( $file ){
		
		return self :: $filehash . filename( $file );
		
	}
	
	/////////////////////////////

	/*
		转换编码
		$tmpdir	临时目录
	*/
	public static function trans_file( $tmpdir ){
		global $_G;
		
		//处理非 GBK 编码
		if( $_G['product']['charset'] != 'gbk' ){
			
			//获取文件清单
			$list = rglob( $tmpdir.'{*.php,*.htm,*.sql}', GLOB_BRACE );
			
			/////////////////////
				
			//批量转换编码
			foreach( $list as $file ){
				
				$text = iconv( 'GBK', $_G['product']['charset'].'//IGNORE', sreadfile( $file ) );
				
				create_file( $file, $text );    		
				
			}
			
			/////////////////////
			
			//标识新编码
			$file = $tmpdir.'config/version.php';
			
			if( file_exists( $file ) ){
				
				$text = sreadfile( $file );
				$text = preg_replace( "/\['product'\]\['charset'\]	= '.*?';/i", "['product']['charset']	= '". $_G['product']['charset'] ."';", $text );
				create_file( $file, $text );
				
			}
			
		}
		
	}

	/*
		移除文件
		$tmpdir	临时目录
		$list		需要删除的文件列表
	*/
	public static function clean_file( $tmpdir, $list ){
		
		//过滤空白数组
		$list = array_filter( $list );
		
		//批量转换编码
		foreach( $list as $file ){
		
			$object = $tmpdir . $file;
			
			//忽略不存在的文件
			if( file_exists( $object ) === FALSE ) continue;
		
			//删除目录
			if( is_dir( $object ) ) delete_dir( $object );
				
			//删除文件出错
			if( is_file( $object ) && unlink( $object ) === FALSE ){
			
				//记录在案
				self :: $lastfile = $tmpdir;
			
				return -10006;
				
			}
    		
		}		
		
	}

	/*
		存储文件
		$file	远程文件
		$md5	文件哈希
	*/
	public static function store_file( $file, $md5 ){
		
		//清除缓存
		clearstatcache();
		
		//创建临时目录
		$tmpdir = create_dir( self :: get_temp_file( $file ), TRUE, 0777 );
		
		//var_dump( $tmpdir );
		//var_dump( file_exists( $tmpdir ) );
		//var_dump( is_dir( $tmpdir ) );
		
		//发生错误
		if( $tmpdir === FALSE ){
		
			//记录在案
			self :: $lastfile = str_replace( VI_ROOT, '', self :: $filehash );
			
			return -10002;
			
		}
		
		//////////////////////
		
		//清除缓存
		clearstatcache();
		
		//本地文件名
		$locale = self :: get_pack_file( $file );
		
		if( self :: valid_file( $locale, $md5 ) ){
			
			//直接使用已有包
			$package = $locale;
			
		}else{
			
			//下载文件包
			$package = getfile( $file, $locale );
			
		}
		
		//发生错误
		if( $package === FALSE ){
		
			//记录在案
			self :: $lastfile = filename($file);
			
			return -10001;
			
		}
		
		//////////////////////
		
		//实例zip类
		$zip = new PclZip( $package );
	
		//解压到临时目录
		$stat = $zip->extract( PCLZIP_OPT_PATH, $tmpdir );
		
		//返回文件数量
		return count($stat);
		
	}

	/*
		验证文件权限
		$file	文件名
		$md5	MD5 值
	*/
	public static function valid_perm( $dire, $ignore = array() ){
	
		//获取文件清单
		$list = rglob( $dire.'*', GLOB_BRACE, $ignore );
		
		//无读写权限清单
		$stat = array();
		
		//将目标加入清单
		array_unshift( $list, $dire );
		
		/////////////////////
			
		//批量测试权限
		foreach( $list as $file ){
			
			if( is_writeable( $file ) === FALSE ){
				array_push( $stat, str_replace( VI_ROOT, './', $file) );
			}   		
			
		}
		
		return $stat;
		
	}

	/*
		验证文件哈希
		$file	文件名
		$md5	MD5 值
	*/
	public static function valid_file( $file, $hash ){
		
		return file_exists( $file ) && md5_file( $file ) == $hash;
		
	}

	/*
		验证文件
		$tmpdir	临时目录
		$newdir	目标目录
	*/
	public static function moved_file( $tmpdir, $newdir, $pack ){
		
		//return rename( $tmpdir, $newdir );
		
		$list = rglob( $tmpdir.'*', GLOB_BRACE );
		
		//var_dump( $list );
		//exit;
		
		//批量迁移文件
		foreach( $list as $file ){
		
			$newd = str_replace( $tmpdir, $newdir, $file );
			
			//var_dump( $file );
			//var_dump( $newd );
			
			//echo '<hr />';
			
			if( file_exists( $file ) && is_writable( $file ) == FALSE ){
			
				//记录在案
				self :: $lastfile = str_replace( $tmpdir, '', $file );
				
				return -10007;
				
			}
			
			////////////////////////////
			
			if( file_exists( $newd ) && is_writable( $newd ) == FALSE ){
			
				//记录在案
				self :: $lastfile = str_replace( $newdir, '', $newd );
				
				return -10007;
				
			}
			
			////////////////////////////
		
			//创建文件夹
			if( is_dir( $file ) ){
				
				create_dir( $newd, TRUE, 0777 );
				
			}else{
			
				//删除旧文件（winodws 环境需要）
				if( file_exists( $newd ) ){
					unlink( $newd );
				}
				
				//生成新文件
				$test = @rename( $file, $newd );
				
				//记录在案
				self :: $lastfile = str_replace( $tmpdir, '', $file );
				
			}
			
			////////////////////////////		
    		
			//移动文件出错
			if( $test === FALSE ) return -10005;
    		
		}
		
		//删除临时目录
		delete_dir( $tmpdir );
		
		//删除文件包
		unlink( self :: get_pack_file( $pack ) );
		
		return count( $list );
		
	}
	
	/////////////////////////////

	/*
		升级内核
		$file		文件包
		$target		目标目录
		$clean		清理目录
	*/
	public static function upgrade_engine( $file, $md5, $clean = array() ){
		
		//存储更新包
		$size = self :: store_file( $file, $md5 );
		
		//发生错误
		if( $size <= 0 ) return $size;
		
		//////////////////////
		
		//临时目录名
		$tmp = self :: get_temp_file( $file );
		
		//转换编码
		self :: trans_file( $tmp );
		
		//清理文件
		self :: clean_file( $tmp, array( 
			'config/attach.php',
			'config/config.php',
			'config/global.php',
			'config/mail.php',
			'config/licence.php',
			'attach/',
			'cache/',
		 ) );
		
		//清理文件
		return self :: moved_file( $tmp, VI_ROOT, $file );
		
	}

	//升级模块
	public static function upgrade_module( $file, $md5, $appid, $clean = array() ){
		
		//存储更新包
		$size = self :: store_file( $file, $md5 );
		
		//发生错误
		if( $size <= 0 ) return $size;
		
		//////////////////////
		
		//临时目录名
		$tmp = self :: get_temp_file( $file );
		
		//转换编码
		self :: trans_file( $tmp );
		
		//配置文件
		array_push( $clean, 'setting.php', 'navigate.php', 'install.lock', 'upgrade.lock', 'content/.htaccess' );
		
		//清理文件
		self :: clean_file( $tmp, $clean );
		
		//清理文件
		return self :: moved_file( $tmp, VI_ROOT.'module/'.$appid.'/', $file );
		
	}

	//安装模块
	public static function install_module( $file, $md5, $appid, $clean = array() ){
		
		//存储更新包
		$size = self :: store_file( $file, $md5 );
		
		//发生错误
		if( $size <= 0 ) return $size;
		
		//////////////////////
		
		//临时目录名
		$tmp = self :: get_temp_file( $file );
		
		//转换编码
		self :: trans_file( $tmp );
				
		//配置文件
		array_push( $clean, 'install.lock', 'upgrade.lock' );
		
		//清理文件
		self :: clean_file( $tmp, $clean );
		
		//清理文件
		return self :: moved_file( $tmp, VI_ROOT.'module/'.$appid.'/', $file );
		
	}
	
	///////////////////////////////////

	/*
		向云平台发送命令
		$command		命令参数
		$domain		目标域名
		$secret		通信密钥
	*/
	public static function request( $command ){
		global $_G;
		
		//序列化
		$dataset   = rawurlencode( authcrypt( serialize($command), VI_SECRET ) );
	
		//发送请求
		$return    = @file_get_contents( $_G['project']['home'].'api.php?action=cloud&domain='.VI_HOST.'&execute='.$dataset );
		
		//反序列化
		$content = $return ? unserialize( $return ) : array( 'return' => 'respond' );
		
		return $content;
		
	}

	/*
		验证当前版本信息
	*/
	public static function licence( ){
		global $_G;
		
		$text = file_get_contents( $_G['project']['home'].'api.php?action=service&execute=business&version='.$_G['product']['version'].'&charset='.$_G['product']['charset'].'&domain='.urlencode(VI_HOST).'&secret='.VI_SECRET );
		
		$data = unserialize($text);
		
		if( $data ){
			
			$file = VI_ROOT."config/licence.php";
		
			//写入到缓存
			create_file($file,'<?php $_G[\'licence\'] = '.var_export( $data, TRUE ).';?>');
			
			//刷新缓存
			$_G['licence'] = $data;
			
		}
		
		return $data;
		
	}
	
}

//初始化
Cloud :: init();
