<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	系统及模块缓存处理
*/

class Cached{

	public static function direct( $appid ){		
		return VI_ROOT.'cache/dataset/'.$appid.'/';		
	}
	
	public static function delete( $appid, $file ){		
		return unlink( self :: direct( $appid ) . $file );		
	}

	/*
		加载缓存
		$appid	目录名称
		$file		文件名称，不包括扩展名
		$url		不存在缓存时跳转的URL
	*/
	public static function loader( $appid, $file, $url="" ){
		global $_G;
		global $_CACHE;

		$cache = VI_ROOT.'cache/dataset/'.$appid."/".$file.".php";

		if( file_exists($cache) ){
			return require($cache);		
		}elseif( $url ){
			
			if (strrpos($url,"=")>0 || strrpos($url,"%3d")>0){
				$url.="&jump=".GetCurUrl();
			}else{
				$url.="?jump=".GetCurUrl();
			}
			
			header("Location:".VI_BASE."serve.error.php?action=cache&cache=".($id ? $file."/".$id : $file)."&url=".urlencode($url));
		}else{
			return FALSE;
		}
		
	}

	/*
		获取缓存生成时间
		$appid	目录名称
		$file		文件名称，不包括扩展名
		$extra		文件扩展名
	*/
	public static function gettime( $appid, $file, $extra = 'php' ){
		$cache = VI_ROOT.'cache/dataset/'.$appid.'/'.$file.'.'.$extra;		
		return file_exists( $cache ) ? filemtime( $cache ) : 0;		
	}
	
	/*
			缓存查询至JavaScript                
			$appid		模块ID
			$sql		查询语句
			$variable	变量名
			$file		缓存文件名
			$option		选项模板
	*/
	public static function script( $appid, $sql, $variable, $file, $option = array() ){
		global $_G;

		//主键
		$option['index'] = $option['index'] ? $option['index'] : 'id';

		//编码
		$option['charset'] = $option['charset'] ? $option['charset'] : 'utf-8';

		//压缩文本
		$option['compress'] = $option['compress'] ? $option['compress'] : TRUE;

		//unicode 编码
		$option['unicode'] = isset( $option['unicode'] ) ? $option['unicode'] : TRUE;

		//html 实体符
		$option['entity'] = isset( $option['entity'] ) ? $option['entity'] : FALSE;
				
		/////////////////////////
		
		//查询数据                
		$part = System :: $db -> getAll( $sql, $option['index'] );
		
		foreach( $part as &$row ){
			$row = self :: format( $row, $option );
		}
		
		$string = var_export( $part, TRUE );
		
		//转换成 JSON 格式
		$string = str_replace( 
						array(	'=>',	'array (',	'),'	),
						array(	':',	'{',	'},'	),
						$string
					);
		
		//构造变量名
		$string = 'var '.$variable.' = '.$string.';';
		
		//去除多余的逗号
		$string = preg_replace( '/\,(\s*)\}/', '\1}', $string );
		$string = preg_replace( '/\}\,(\s*)\)\;/', '}\1};', $string );
		
		//转换相关关键字
		$string = str_replace( ': NULL', ': null', $string );
		
		//unicode 编码
		if( $option['unicode'] === TRUE ){
			$string = unicode_encode( $string, $_G['product']['charset'] );
		}else{
			$string = stripslashes( $string );
		}
		
		//html 实体符
		if( $option['entity'] === TRUE ){
			$string = entity_encode( $string, $_G['product']['charset'] );
		}
		
		//压缩文本
		if( $option['compress'] === TRUE ){
			$string = compress( $string );
		}
		
		//存储编码
		if( $_G['product']['charset'] != $option['charset'] ){
			$string = iconv( $_G['product']['charset'], $option['charset'], $string );
		}
		
		$string = '/*'.date("Y-m-t H:i:s").'*/'.chr(10).$string;

		//写入缓存
		create_file( self :: direct( $appid ) . str_replace(array('sys.','mod.'),array('script.','script.'),$file).".js", $string );
	}

	/*
			缓存多条数据记录	
			$appid		模块ID
			$sql		        查询语句
			$file		        缓存名称
			$option		配置选项
	*/
	public static function multi( $appid, $sql, $file, $option = array() ){
		global $_G;
		
		//别名
		$table = self :: table_name( $sql, $option['alias'] );

		//主键
		$option['index'] = $option['index'] ? $option['index'] : 'id';
		
		$query = self :: table_query( $sql );
						
		/////////////////////////
		
		$part = System :: $db -> getAll( $query, $option['index'] );
		
		foreach( $part as &$row ){                        
			$row = self :: format( $row, $option );
		}
		
		//写入缓存
		create_file(  self :: direct( $appid ) . $file.'.php', '<?php /*'.date("Y-m-d H:i:s").'*/ $_CACHE[\''.$appid.'\'][\''.$table.'\'] = '.var_export( $part, true ).';' );
		
		return $part;
	}

	/*
			缓存单条数据记录                
			$appid		模块ID
			$sql		        查询语句
			$option		配置选项
	*/
	public static function rows( $appid, $sql, $option = array() ){
		global $_G;          
						
		//别名
		$table = self :: table_name( $sql, $option['alias'] );
		
		$query = self :: table_query( $sql );
		
		$part = System :: $db -> getAll( $query );
	   
		foreach( $part as &$row ){
			
			$row = self :: format( $row, $option );
			
			//写入缓存
			create_file( self :: direct( $appid ) . $table.'.'.$row['id'].'.php', '<?php /*'.date("Y-m-d H:i:s").'*/ $_CACHE[\''.$appid.'\'][\''.$table.'\'] = '.var_export( $row, true ).';' );
			
		}
		
		return $part;
	}

	/*
			缓存整张数据表
			$appid		模块ID
			$mysql		数据库连接
			$table		表名
			$option		选项模板
			$sort		排序方式
			$index		索引键
	*/
	public static function table( $appid, $table, $option = array() ){
		global $_G;
		
		//字段
		$option['field'] = $option['field'] ? $option['field'] : '*';
		
		//排序
		$option['order'] = $option['order'] ? $option['order'] : '';
		
		//主键
		$option['index'] = $option['index'] ? $option['index'] : 'id';
				
		/////////////////////////
		
		$sql="SELECT ".$option['field']." FROM `$table`";
		
		if( $option['group'] ){
			$sql.=" GROUP BY ".$option['group'];
		}
		
		if( $option['order'] ){
			$sql.=" ORDER BY ".$option['order'];
		}else{
			$sql.=" ORDER BY ".$option['index'];
		}
		
		//别名
		$table = self :: table_name( $sql, $option['alias'] );
		
		$query = self :: table_query( $sql );
		
		$part = System :: $db -> getAll( $query, $option['index'] );
		
		foreach( $part as &$row ){
			$row = self :: format( $row, $option );
		}
		
		//写入缓存
		create_file(  self :: direct( $appid ) . 'table.'.$table.'.php', '<?php /*'.date("Y-m-d H:i:s").'*/ $_CACHE[\''.$appid.'\'][\''.$table.'\'] = '.var_export( $part, true ).';' );
		
		return $part;
		
	}
	
	/*
			格式化数据集
			$row           数据行
			$option         配置选项
	*/
	public static function format( $row, $option ){
		global $_G;

		//JSON解码
		$option['jsonde'] = $option['jsonde'] ? $option['jsonde'] : array();
		
		//逗号分隔
		$option['split'] = $option['split'] ? $option['split'] : array();	
		
		//空隔分隔
		$option['space'] = $option['space'] ? $option['space'] : array();
		
		//反序列化
		$option['serialize'] = $option['serialize'] ? $option['serialize'] : array();
		
		//HTML实体
		$option['entity'] = $option['entity'] ? $option['entity'] : array();
		
		//移除数字零
		$option['zeroed'] = $option['zeroed'] ? $option['zeroed'] : array();
		
		//Unicode
		$option['unicode'] = $option['unicode'] ? $option['unicode'] : array();
		
		//修正地址
		$option['attach'] = $option['attach'] ? $option['attach'] : array();
		
		//转换换行
		$option['nl2br'] = $option['nl2br'] ? $option['nl2br'] : array();
		
		///////////////////////////////
		
		foreach ($row as $key => &$val ) {
			
			if( $val == '' && ( in_array( $key, $option['jsonde'] ) || in_array( $key, $option['split'] ) || in_array( $key, $option['space'] ) || in_array( $key, $option['serialize'] ) ) ){
				
				$val = array();
				
			}else{
				
				if( in_array( $key, $option['jsonde'] ) ){		//JSON解码
			
					$val = fix_json( $val );
						
				}elseif( in_array( $key, $option['split'] ) ){		//换行和双竖线分隔符
						
					$val = preg_split("/(\n|,|(\|\|))/", $val );
						
				}elseif( in_array( $key, $option['space'] ) ){		//空格分隔符
						
					$val = explode( ' ', $val );
						
				}elseif( in_array( $key, $option['serialize'] ) ){	//反序列化
				
					$val = unserialize( $val );
				
				}elseif( in_array( $key, $option['entity'] ) ){		//HTML实体
				
					$val = entity_encode( $val, $_G['product']['charset'] ); 
						                                       
				}elseif( in_array( $key, $option['zeroed'] ) ){		//去除小数后多除的零
				
					$val = $val + 0; 
						                                       
				}elseif( in_array( $key, $option['unicode'] ) ){	//Unicode
				     
					$val = unicode_encode( $val, $_G['product']['charset'] );
					
				}elseif( in_array( $key, $option['attach'] ) ){	//修正地址
				     
					$val = fix_attach( $val );
					
				}elseif( in_array( $key, $option['nl2br'] ) ){	//转换换行
					$val = preg_replace("(\r\n|\r|\n)",'<br />', $val );
				}
				
			}

		}
		
		return $row;

	}
	
	/////////////////////////////////

	/*
		缓存页面查询
		$sql		查询语句
		$time		缓存有效期，分钟
		$temp		缓存模板
		$debug		调试模式
	*/
	public static function query( $sql, $time, $temp='', $debug=false ){
		global $_G;

		// 创建目录
		$base = create_dir( VI_ROOT.'cache/dataset/sql/' );

		// 缓存地址
		$file = $base .md5( $sql.$temp ). ".php";
		
		//检查缓存是否有效
		if( !file_exists($file) || time() - filemtime( $file ) >= ( $time*60 ) ){
			
			$cache = System :: $db -> getAll( $sql );
			
			//保存缓存
			if( !$debug ){
				create_file( $file, serialize( $cache ) );
			}
			
		}else{
		
			$cache = unserialize( file_get_contents($file) );
			
		}
		
		//////////////////////
		
		//编译模板
		if( $temp && $cache ){
			
			$i = 1;
			$content = '';
			
			foreach( $cache as $row ){
			
				$tmp = $temp;			
				foreach ($row as $key => $value) {
				   $tmp=str_replace("{table:". $key ."}",$value,$tmp);
				}
				
				//index
				$tmp=str_replace("{table:#}",$i,$tmp);			
				$content .= $tmp;
				
				$i++;
			}
			
			//函数处理
			return self :: exec_eval( $content );
				
		}
		
		return $cache;
	}

	/*
			对字符进行函数格式化                
			$str	模板	
					DEMO	{fun:urlencode("{#t_name}"):end}
	*/
	public static function exec_eval($str){
	
		preg_match_all("{fun:([\S\s]+?):end}", $str, $matches,PREG_SET_ORDER);	
		
		for( $i=0; $i<count($matches); $i++ ){
				
			$tmp_str = '{'.$matches[$i][0].'}';
			$tmp_val = eval('return '.$matches[$i][1].';');			
			$str = str_replace($tmp_str,$tmp_val,$str);
				
		}
		return $str;
		
	}
	
	/*
		将查询中的变量转换
		$sql    查询语句
	*/
	public static function table_query( $sql ){
		
		$sql = str_replace('mod:',VI_DBMODPRE,$sql);
		$sql = str_replace('sys:',VI_DBMANPRE,$sql);
		
		return $sql;
			
	}
	
	/*
		提取查询中的数据表名
		$sql    查询语句
		$alias	别名
	*/
	public static function table_name( $sql, $alias = NULL ){
	
		//直接返回别名
		if( isset( $alias ) ) return $alias;
		
		preg_match ( '/SELECT(.*?)FROM `(.*?)`/i', $sql, $match );
		
		if( empty( $match ) ){
			return str_replace( 'table.', '', $sql );                        
		}else{                        
			return str_replace( array('sys:','mod:','table.'), array('','',''), $match[2] );
		}
	}
	
	/////////////////////////////////
	
	/*
			生成表单结构缓存
			$list			列表
			$appid		模块ID
	*/
	public static function form( $appid, $list ){
		global $_G;

		$sql="SELECT * FROM `mod:form_form` WHERE appid='".$appid."' ";
		
		//读取表单信息
		$list && $sql .=" and id in(".$list.")";
				
		//创建子文件夹
		$folder = create_dir( self :: direct( $appid ) );
		
		$result = System :: $db -> getAll( $sql );

		foreach( $result as $form ){
				
			$file = $folder."/form.".$form['id'].".php";
			
			$part = array( 'form' => array(), 'group' => array(), 'option' => array() );
			
			//表单主体
			foreach ( $form as $key => $val ) {
			
				if( $key == 'config' ){
					$form[ $key ] = fix_json( $val );
				}
				
			}
			
			$part['form'] = $form;
			
			////////////////
			
			//选项组
			$sql="SELECT * FROM `mod:form_group` WHERE fid=".$form['id']." and `state`>0 order BY sort ASC,id ASC";
			
			$res = System :: $db -> getAll( $sql, 'id' );

			foreach( $res as $gid => $group ){
				
				foreach ( $group as $key => $val ) {
				
					if( $key == 'config' ){
						$group[ $key ] = fix_json( $val );
					}
				
					if( $key == 'selected' ){
						$group[ $key ] = explode( ',', $val );
					}
					
				}
				
				$part['group'][ $gid ] = $group;
				
			}
			
			////////////////
			
			//子选项
			$sql="SELECT * FROM `mod:form_option` WHERE fid=".$form['id']." and `state`>0 order BY sort ASC,id ASC";
			
			$res = System :: $db -> getAll( $sql, 'id' );

			foreach( $res as $oid => $option ){
				
				foreach ( $option as $key => $val ) {
				
					if( $key == 'config' ){
						$option[ $key ] = fix_json( $val );
					}
						
				}
				
				$part['option'][ $option['gid'] ][ $oid ] = $option;
				
			}
			
			////////////////
			
			//写入缓存
			create_file( $file, '<?php /*'.date("Y-m-d H:i:s").'*/ $_CACHE[\''.$appid.'\'] = '.var_export( $part, true ).';' );
		}
	}
        
}
