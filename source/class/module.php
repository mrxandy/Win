<?php

class Module{
	
	//类初始化
	public static function init(){
		
		//加载模块清单
		self :: get_list();
		
	}
	
	/*
			是否存在此模块或文件
			$appid		模块ID
			$filename	文件名
			$return         true | false
	*/
	public static function exists( $appid, $filename = NULL ){
		return is_dir( VI_ROOT . 'module/'. $appid ) && ( isset( $filename ) ? file_exists( VI_ROOT . 'module/'. $appid.'/'.$filename ) : TRUE );
	}
	
	 /*
			加载模块配置及函数（类）
			$appid	模块ID
			$return         true | false
	*/
	public static function loader( $appid, $filter = '*' ){
		global $_G;
		global $_CACHE;
		
		$appid = strtolower( $appid );
		$appdd = array( 'config', 'setting', 'navigate', 'function' );
		
		if( is_array( $filter ) ){
			
			$appdd = $filter;
			
		}elseif( $filter != '*' ){
			
			$appdd = array( $filter );
		}

		//如果存在此模块
		if( self :: exists( $appid ) ){
			
			foreach( $appdd as $file ){
				if( file_exists( VI_ROOT . 'module/'. $appid .'/'.$file.'.php' ) ){
					require_once VI_ROOT . 'module/'. $appid .'/'.$file.'.php';
				}
			}
			
			//模块标识为已载入
			if( $filter == '*' || $filter == 'function' ){
				$_CACHE['system']['module'][$appid]['loaded'] = TRUE;
			}
			
			//初始化模块
			//method_exists( $appid, 'init' ) && call_user_func_array( array( $appid, 'init' ), array() );
			
			return TRUE;
		}else{
			return FALSE;
		}

	}
	
	//初始化计划任务
	public static function init_cron(){
		global $_CACHE;
		
		//遍历模块并执行计划任务
		foreach( $_CACHE['system']['module'] as $mod ) {			
			if( $mod['loaded'] == TRUE ){
				class_exists( $mod['appid'] ) && method_exists( $mod['appid'], 'cron' ) && call_user_func_array( array( $mod['appid'], 'cron' ), array() );
			}
		}
	
	}
	
	//////////////////////////////////
	
	/*
		获取当前模块ID（只能使用于模块目录下）
	*/
	public static function get_appid( $base = NULL ){
		global $_G;
		
		//系统根目录
		$root = str_replace( '\\', '/', VI_ROOT );
		
		//当前相对目录
		$base = str_replace( '\\', '/', ( $base ? $base : getcwd() ) );
		$base = str_replace( $root, '', $base );
		
		//模块起始目录
		if( strpos( $base, 'module/' ) === 0 ){
			
			if( substr_count( $base, '/' ) > 1 ){
			
				//获得 module/ 之后最近一个斜线的索引
				$last = strpos( $base, '/', 7 );
				return substr( $base, 7, $last - 7 );
				
			}else if( substr_count( $base, '.' ) == 0 ){
				
				return substr( $base, 7 );
			}
			
		}
		
	}
	
	/*
			获取某模块前台访问地址
			$appid	模块ID
	*/
	public static function get_index( $appid ){
		global $_G;

		//如果存在此模块,加载配置
		if( self :: exists( $appid, 'setting.php' ) && self :: loader( $appid, 'setting' ) ){
			return $_G['setting'][$appid]['domain'] ? $_G['setting'][$appid]['domain'] : VI_HOST.'module/'.$appid.'/content/';                
		}
	
	}
	
	/*
			生成获取某模块前台访问地址
			$appid	模块ID
	*/
	public static function get_naver( $appid ){
		global $_G;
		global $_CACHE;

		//首页地址
		$link = self :: get_index( $appid );
		
		//如果需要关闭
		if( $link && $_G['setting'][$appid]['hooks'] == 'off' ) return;
		
		//模块名称		
		return '<a href="'.$link.'">'.( $_G['setting'][$appid]['name'] ? $_G['setting'][$appid]['name']: $_CACHE['system']['module'][$appid]['name'] ).'</a>';
	
	}
	
	/*
			生成上下文菜单
			$appid	模块ID
	*/
	public static function get_context( $appid, $menu = 'context' ){
		global $_G;
		
		Module :: loader( $appid );
		
		//////////////////////
		
		$context = $_G['module'][ $appid ][ $menu ];
		$setting = $_G['setting'][ $appid ]['domain'];
		$current = $_G['runtime']['current'];
		$qstring = '?'.$_SERVER['QUERY_STRING'];
		
		//var_dump($qstring);
		
		//////////////////////
		
		$text = '<ul id="naver">';
		
		$find = NULL;
		
		//生成父菜单
		foreach( $context as $title => $config ){
		
			//是否在当前父导航中（ 链接相同、当前文件在子菜单中出现、当前文件在标识中出现 ）
			if( $config['link'] == $current || $config == $current || ( is_array($config['menu']) && in_array( $current, $config['menu']) )  || ( is_array($config['flag']) && in_array( $current, $config['flag']) ) ){
				$find = $title;
			}
		
			$text .= '<li '. ( $find == $title ? ' class="active" ' : '' ) .'><a href="'.( is_array($config) ? $config['link'] : $config ).'" data-hash="true">'.$title.'</a></li>';
			
		}
		
		//判断是否有域名绑定
		if( isset( $setting ) ){
			$text .= '<li class="menu"><a href="'.Module::get_index( $appid ).'" target="_blank" data-hash="true">前台页面</a></li>';
		}
		
		$text .= '</ul>';
		
		//////////////////////
		
		//生成子菜单
		if( $find && is_array( $context[$find]['menu'] ) ){
			
			$text .= '<div id="subnav">';
			
			foreach( $context[$find]['menu'] as $title => $link ){
				$text .= '<a '. ( $current == $link || $qstring == $link || ( strpos($qstring,$link) !== FALSE ) ? 'class="active"' : '' ) .' href="'.$link.'">'.$title.'</a>';
			}
			
			$text .= '</div>';
			
			//修正导航多次匹配，只保留最后一个
			preg_match_all( '/<a class="active" href="(.*?)">(.*?)<\/a>/', $text, $match );
			
			if( count( $match[0] ) > 1 ){
			
				for( $i = 0; $i < count( $match[0] ) -1; $i++ ){
					$text = str_replace( $match[0][$i], str_replace(' class="active"', '', $match[0][$i]), $text);
				}
				
			}
			
		}
		
		return $text;
		
	}
	
	/*
			创建并返回模块对应的编译目录
			$appid	模块ID
	*/
	public static function get_static( $appid ){
		return create_dir( VI_ROOT.'cache/compile/'.$appid );                
	}
	
	/*
			获取模块对应的前台目录地址
			$appid	模块ID
	*/
	public static function get_content( $appid, $dir = '' ){
		return VI_ROOT.'module/'.$appid.'/content/'.$dir;                
	}
	
			
	/*
			删除模块全部静态缓存
			$appid	模块ID
	*/
	public static function clear_static( $appid ){
	
		$base = VI_ROOT."cache/compile/".$appid;

		//先删除目录
		delete_dir( $base );
		
		//创建子目录
		create_dir( $self, true );
			
	}
	
	/*
		扫描模块，缓存所有Hock
		$modid 	模块ID
		$state	设置状态
				true 启用 false 禁用
	*/
	public static function hooks_push( $debug = FALSE ){
		global $_G;
		global $_CACHE;
		
		$_G['hooks'] = array();
		
		//遍历模块
		foreach( $_CACHE['system']['module'] as $app ){
			
			//只处理普通模块
			if( $app["state"] && $app["model"] == 'module' ){
							
				//载入模块，并配置hock选项
				if( self :: loader( $app["appid"] ) && is_array( $_G['module'][$app["appid"]] ) && is_array( $_G['module'][$app["appid"]]['hooks'] ) ){
					
					foreach( $_G['module'][$app["appid"]]['hooks'] as $key => $fun ){
					
						if( !is_array( $_G['hooks'][ $key ] ) ) $_G['hooks'][ $key ] = array();
						
						array_push( $_G['hooks'][$key], $fun );
						
					}
				}
				
			}
		
		}
		
		if( $debug ){
			echo '<pre>';
			var_dump($_G['hooks']);
			echo '</pre>';
		}
		
		return $_G['hooks'];
		
	}
	
	/*
		输出当前 Hock
		$key	Hock索引
	*/
	public static function hooks_slice($key){
		global $_G;
		global $_CACHE;
		
		$res = '';
		
		//忽略空数组
		if( isset( $_G['hooks'][$key] ) === FALSE ) return $res;
		
		foreach( $_G['hooks'][$key] as $i => $func ){
			$res .= call_user_func_array( $func['func'], $func['parm'] );
		}
		
		return $res;
		
	}
	
	
	////////////////////////////////////////
	
	/*
			删除模块全部动态缓存
			$appid	模块ID
	*/
	public static function clear_cache( $appid ){
	
		$base = VI_ROOT."cache/dataset/".$appid;

		//先删除目录
		delete_dir( $base );
		
		//创建子目录
		create_dir( $self, TRUE );
			
	}
	
	/*
			加载模块缓存信息并返回缓存文件地址
	*/
	public static function get_cache(){
			
	}
	
	/*
			加载模块缓存信息并返回缓存文件地址
	*/
	public static function get_list(){
		global $_CACHE;
		
		$file = VI_ROOT.'cache/dataset/system/files.module.php';
		file_exists( $file ) && require $file;
		return $file;
	}
	
	/*
			扫描关缓存模块信息
	*/
	public function search(){
		global $_G;
		global $_CACHE;
		
		//模块列表缓存文件
		$file = self :: get_list();
		
		$list = loop_dir( VI_ROOT.'module/' );
		
		$module = array();
		
		$cached = $_CACHE['system']['module'];
		
		foreach( $list as $appid ){
		
			//创建缓存目录
			create_dir( VI_ROOT.'cache/dataset/'.$appid );
			create_dir( VI_ROOT.'cache/compile/'.$appid );
											
			//配置文件
			$config = 'module/'.$appid.'/config.php';				
			
			//加载配置
			file_exists(VI_ROOT.$config) && require_once(VI_ROOT.$config);
			
			$app = $_G['module'][$appid];
			
			//有配置文件
			if( is_array( $app ) ){
			
				$_CACHE['system']['module'][ $appid ] = $module[ $appid ] = array( 
					'name'=> $app['name'],
					'appid'=> $appid,
					'model'=> $app['model'],
					'signed'=> $app['signed'],
					'version'=> $app['version'],
					'serve'=> $app['serve'],
					'hooks'=> $app['hooks'],
					'index'=> self :: get_index( $appid ),
					'domain'=> $_G['setting'][$appid]['domain'],
					'statis'=> $app['statis'],
					'permit'=> $app['permit'],
					'author'=> $app['author'],
					'support'=> $app['support'],
					'context'=> $app['context'],
					'writable'=> $app['writable'],
					'describe'=> $app['describe'],
					'external'=> $app['external'],
					'state'=> ( isset( $cached[$appid]['state'] ) ? $cached[$appid]['state'] : TRUE )
				);
				
				//新模块标识
				if( !is_array( $_CACHE['system']['module'][$appid]) ){
					$module[ $appid ]['dateline'] = time();
				}
				
				//刷新伪静态
				self :: rewrite( $appid );
			
			//没有配置文件
			}else{
				$_CACHE['system']['module'][ $appid ] = $module[ $appid ] = array( 'name'=> $appid, 'appid'=> $appid, 'state'=> TRUE );
			}
			
		}
		
		$text ='<?php'.chr(13);
		$text .=' /*'.date("Y-m-t H:i:s").'*/ '.chr(13);
		$text .= '$_CACHE[\'system\'][\'module\']='.var_export( $module, true );
		$text .= ';';
		
		//实时缓存
		//$_CACHE['system']['module'] = $module;
		
		//写入缓存
		return create_file( $file, $text );
	}
	
	
	 /*
			扫描关缓存模块信息
			$appid	        模块ID
			$state          是否启用
	*/
	public static function set_state( $appid, $state = TRUE ){
		global $_G;
		global $_CACHE;

		//模块列表缓存文件
		$file = self :: get_list();
		
		//设置状态
		$_CACHE['system']['module'][ $appid ]['state'] = $state;
		
		$text ='<?php'.chr(13);
		$text .=' /*'.date("Y-m-t H:i:s").'*/ '.chr(13);
		$text .= '$_CACHE[\'system\'][\'module\']='.var_export( $_CACHE['system']['module'], true );
		$text .= ';';
		
		//写入日志
		if( $state ){
			System :: insert_event($func,time(),time(),"启用模块成功：".$appid);
		}else{
			System :: insert_event($func,time(),time(),"禁用模块成功：".$appid);
		}
		
		//写入缓存
		return create_file( $file, $text );
	
	}
	
	/*
			返回模块当前状态
			$appid	        模块ID
	*/
	public static function get_state( $appid ){
		global $_CACHE;

		//载入模块缓存
		$file = self :: get_list();
		
		//返回状态
		return $_CACHE['system']['module'][ $appid ]['state'];
				   
	}
	
	//////////////////////////////////
	
	/*
			安装某一模块
			$appid	        模块ID
	*/
	public static function install( $appid ){
		global $_G;
			
		//缓存目录
		$dir = VI_ROOT.'cache/dataset/system/';
		
		//创建子文件夹
		$folder = create_dir($dir);
		
		//当前模块目录
		$base = VI_ROOT.'module/'.$appid;

		//脚本文件
		$exec = $base.'/install.sql';

		//锁文件
		$lock = $base.'/install.lock';
		
		//目录不可读写
		if( is_writable($base) === FALSE ){
			
			return 'permission';
		
		//存在锁文件
		}elseif( file_exists($lock) ){
				
			return 'locked';
		
		//无效安装脚本
		}elseif( !file_exists($exec) ){
				
			return 'script';
				
		}else{
		
			//创建缓存目录
			create_dir( VI_ROOT.'cache/'.$appid );
			create_dir( VI_ROOT.'cache/compile/'.$appid );
	
			$array = Database :: query( sreadfile( $exec ) );
			
			if(!$array[0]){
			
				//写入锁文件
				create_file( $lock ,date("Y-m-d H:i:s"));
				
				//删除卸载锁
				@unlink( $base.'/uninstall.lock' );
				
				//启用本模块
				self :: set_state( $appid, TRUE );
		
				//完成脚本安装
				return 'success';
					
			}else{
				
				//脚本安装出错
				return 'abort';
					
			}
				
		}
			
	}
	
	/*
		执行模块升级脚本
		$appid	        模块ID
		$
	*/
	public static function upgrade( $appid ){
		global $_CACHE;
	
		//当前模块目录
		$base = VI_ROOT.'module/'.$appid;

		//脚本文件
		$exec = $base.'/upgrade.sql';

		//锁文件
		//$lock = $base.'/upgrade.lock';
		
		//目录不可读写
		if( is_writable($base) === FALSE ){
			
			return 'permission';
		
		//无效升级脚本
		}elseif( !file_exists($exec) ){
				
			return 'script';
				
		}else{
		
			//读取锁内容
			//$lockfile = sreadfile( $lock );
			
			//获取全部参数
			preg_match_all( "/#\[version=(.*?)\](.+?)#\[\/version\]/ism", sreadfile( $exec ), $match );
			
			//遍历模块
			foreach( $match[1] as $index => $version ){
				
				//比较模块版本号，仅处理大于当前版本号的语句
				if( $version && $version > $_CACHE['system']['module'][$appid]['version'] ){
				
					//$hash = md5( $match[2][$index] );
					
					//检查是否已经执行过
					//if( strpos( $lockfile, $hash ) === FALSE ){
						
						//执行查询语句
						Database :: query( $match[2][$index] );
												
						//追加到锁文件
						//create_file( $lock, $hash.'	'.$version.'	'.date('Y/m/d').chr(10), 'a' );
						
					//}
					
				}
				
			}
			
			//完成脚本更新
			return 'success';
			
		}
	
	}
	
	/*
		卸载某一模块
		$appid	        模块ID
	*/
	public static function uninstall( $appid ){
		global $_G;
	
		//当前模块目录
		$base = VI_ROOT.'module/'.$appid;

		//脚本文件
		$exec = $base.'/uninstall.sql';

		//锁文件
		$lock = $base.'/uninstall.lock';
		
		if( file_exists($lock) ){
				
			//echo '<div id="state" class="failure">抱歉！当前模块已卸载（'.$module.'）</div>';
			return 'locked';
				
		}elseif( !file_exists($exec) ){
				
			//echo '<div id="state" class="failure">抱歉！未找到卸载脚本（'.$module.'）</div>';
			return 'script';
				
		}else{
		
			$sql = file_get_contents($exec);			
			$array = Database :: query( $sql );
			
			if(!$array[0]){
			
				//写入锁
				create_file( $lock ,date("Y-m-d H:i:s"));
				
				//删除安装锁
				@unlink( $base.'/install.lock' );
				
				//禁用本模块
				self :: set_state( $appid, FALSE );
				
				//echo '<div id="state">恭喜！成功卸载模块（'.$module.'）</div>';
				
				return 'success';
					
			}else{
					
				//echo '<div id="state" class="failure">抱歉！卸载模块失败（'.$module.'），以下是本错误信息详细报告：</div>';
				
				return 'abort';
				
				//trigger_error(str_replace('class="error"','class="text-no"',preg_replace('/<h4>(.*?)<\/h4>/','',$array[1])), E_USER_ERROR);
			}
				
		}
	
	}
	
	/*
		生成伪静态
		$appid	        模块ID
	*/
	public static function rewrite( $appid ){
		global $_G;
		global $_CACHE;
	
		//当前目录
		$base = VI_ROOT.'module/'.$appid;
		
		//XML 地址
		$data = $base . '/rewrite.xml';
			
		//存储目录
		$save = $base . '/content/.htaccess';
		
		//找到规则文件
		if( file_exists( $data ) ){
			
			//转成数组
			$docm = xml_array( sreadfile( $data ) );
			
			//验证是否有效
			if( is_array( $docm ) ){
			
				//获取规则模板
				$text = $docm['config'][ $_G['setting']['global']['rewrite']['platform'] ];
				
				//修正基准目录
				if( $_CACHE['system']['module'][$appid]['domain'] ){
					$text = str_replace( '{BASE}', '/', $text );
				}else{
					$text = str_replace( '{BASE}', VI_BASE.'module/'.$appid.'/content/', $text );
				}
				
				//转编码（Linux 主机必需为 ANSI 格式）
				$text = iconv( 'UTF-8', 'GBK', trim( $text ) );
				
				return create_file( $save, $text );
				
			}
			
		}
		
		/*
		// Load the XML source
		$xml = new DOMDocument;
		$xml->load( $data );		
		$doc = $xml->getElementsByTagName('key');		
		for ($i=0;$i<$doc->length;$i++){			
			$ele = $doc->item($i);			
			if( $ele -> getAttribute("name") == $mode ){
				$text = trim( $ele -> nodeValue );
				
				//如果有绑定域名
				if( $domain ){
					$text = str_replace( '{BASE}', '/', $text );
				}else{					
					$text = str_replace( '{BASE}', VI_BASE.'module/'.$this -> appid.'/content/', $text );
				}
				
				//转编码（Linux 主机必需为 ANSI 格式）
				$text = iconv('UTF-8','GBK',$text);
				
				return create_file( $save , $text );
				break;
			}
			
		}
		*/
	
	}
	
	//////////////////////////////////
	
	//地图API加载
	public static function map_load(){
		global $_G;
		
		$base = VI_HOST.'source/location/';
		
		if( $_G['setting']['global']['location']['provide'] == 'google' ){
		
			//加载 Google 地图
			echo '<script src="http://maps.google.com/maps?file=api&v=2&key='.$_G['setting']['global']['location']['key'].'&sensor=false&oe='.$_G['product']['charset'].'&ie='.$_G['product']['charset'].'" type="text/javascript"></script>';
			
			//加载 Google 地图
			echo loader_script(array($base."map.google.js"),$_G['product']['charset'],$_G['product']['version']);
		
		}else{	
			
			//加载 Baidu tangram 框架，避免出现 baidu 未定义的情况（刷新页面时产生）
			echo '<script src="http://img.baidu.com/js/tangram-base-core-1.3.7.js" charset="utf-8" type="text/javascript"></script>';
			
			//加载 Baidu 地图
			echo '<script src="http://api.map.baidu.com/api?v=1.2" charset="utf-8" type="text/javascript"></script>';
		
			//加载 Baidu 地图
			echo loader_script(array($base."map.baidu.js",$base."map.baidu.g2b.js"),'utf-8',$_G['product']['version']);
		
		}
		
	}
	
	/*
		获取查询结果
		$params		参数传入
		&$smarty	
	*/
	public static function sql_exec( $params, &$smarty ){
		global $_G;
		
		if( !empty($params['sql']) && System :: $db ){
		
			// 创建目录
			$base = create_dir( VI_ROOT.'cache/dataset/sql/' );
	
			// 缓存地址
			$file = $base .md5( $params['sql'] ). ".php";
		
			//////////////////////
		
			//检查缓存是否有效
			if( $params['cache'] && file_exists($file) && time() - filemtime( $file ) <= ( $params['cache'] * 60 ) ){
			
				$result = unserialize( file_get_contents($file) );
				
			}else{
			
				switch( $params['type'] ){
				
					case 'value':
						$result = System :: $db -> getValue( $params['sql'] );
					break;
					
					case 'first':
						$result = System :: $db -> getOne( $params['sql'] );
					break;
					
					default:
					case 'multi':
						$result = System :: $db -> getAll( $params['sql'] );
					break;
					
				}
				
				//////////////////////
				
				if( $params['jsonde'] ) $params['jsonde'] = explode(',', $params['jsonde']);
				
				if( $params['serialize'] ) $params['serialize'] = explode(',', $params['serialize']);
				
				if( $result !== FALSE ){
				
					switch( $params['type'] ){
						
						case 'first':
							$result = Cached :: format( $result, $params );
						break;
						
						default:
						case 'multi':
							foreach( $result as &$row ){
								$row = Cached :: format( $row, $params );
							}
						break;
						
					}
				
				}
				
			}
		
			//////////////////////
			
			if( $params['cache'] ){
				create_file( $file, serialize( $result ) );
			}
		
			//////////////////////
			
			$smarty->assign( $params['assign'], $result );
		
		}
	}
	
	/*
		获取表单链接
		$params		参数传入
	*/
	public static function url_form( $params ){
		global $_G;
		
		if( $params['quote'] ){
			return $params['quote'];
		}else{
			return VI_HOST . 'module/'. $params['appid'] .'/content/?id=' . $params['id'];
		}
		
	}
	
}
