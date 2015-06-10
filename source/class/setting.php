<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

/*
	*
	PHP解析XML模板
	获取和设置XML节点
	用于修改和读取站点配置文件
	注意 xml 最好使用 utf-8 编码，这里会自动转编码
	*2008-4-3 
	*LIQUAN
	*eg.get config
	
	*$c = new Setting('templet.xslt','config.xml');
	* $c->transform();
	* $c->save();
	* $c->save();
*/

class Setting{
	
	//配置文件
	private $store;
	
	//模板文件
	private $layout;
	
	//数组名称
	private $appid;
	
	//是否有错
	private $error = FALSE;
	
	//默认密码
	private $password = '********';

	//构造函数
	function __construct( $store, $layout, $appid ){
		global $_G;
		
		$this -> store = $store;
		$this -> layout = $layout;
		$this -> appid = $appid;
		
		//检查文件
		if( !file_exists($this -> layout) ){
			$this -> error = TRUE;
		}
	}
	
	//加载缓存
	function loader(){
		global $_G;
		
		//加载缓存
		if( file_exists($this -> store) ){
			include( $this -> store );
		}else{
			$_G['setting'][$this -> appid] = array();
		}
	}
	
	function convert( $data ){
		global $_G;
		
		if( $_G['product']['charset'] != "utf-8" ){		
			foreach( $data as $key => $val ){				
				if( is_array($val) ){
					$data[$key] = $this -> convert( $val );
				}else{
					$data[$key] = iconv($_G['product']['charset'],"UTF-8",$val);
				}
			}
		}
		
		return $data;
		
	}

	//转换成HTML表单
	function transform(){		
		global $_G;
		
		if($this -> error) return 'File not found!';
		
		//加载缓存
		$this -> loader();
		
		//临时数组
		$data = $_G['setting'][$this -> appid];
	
		//转编码，将缓存转为UTF-8
		$data = $this -> convert( $data );
							
		// Load the XML source
		$xml = new DOMDocument;
		$xml->load($this -> layout);		
		
		$doc = $xml->getElementsByTagName('*');
		
		//表单
		$res = '';
		
		foreach( $doc as $ele ){
			
			//$ele = $doc->item($i);
			
			if( $ele -> getAttribute("used") ) continue;
			
			//var_dump( $matches );			
			//var_dump( $key, $data[$key] );			
			
			$des = str_replace( array('{$TIME}','{$BASE}','{$HOST}'), array(time(),VI_BASE,VI_HOST), $ele -> getAttribute("comment") );
			
			switch( $ele -> tagName ){
				
				//分组
				case "item":
				
					$res .= '<tr><td colspan="2" class="section"><span>'.$des.'</span><strong>'.$ele -> getAttribute("label").'</strong></td></tr>';
				
				break;
				
				//选项
				case "key":
				
					if( $ele -> getAttribute("group") ){
					
						$res .= '<tr>';
							$res .= '<th width="'.$ele -> getAttribute("width").'" align="'.$ele -> getAttribute("align").'">'.$ele -> getAttribute("group").'</th>';
							$res .= '<td>';
							
							foreach( $doc as $grp ){
								if( $grp -> getAttribute("group") == $ele -> getAttribute("group") && $grp -> getAttribute("used") == "" ){
									$res .= $this -> fixKey( $data, $grp, TRUE );
									$grp -> setAttribute("used","true");
								}
							}
							
							$res .= '<span class="text-gray">'.$des.'</span>';
							$res .= '</td>';					
						$res .= '</tr>';						
					
					}else{
					
						$res .= '<tr>';
							$res .= '<th width="'.$ele -> getAttribute("width").'" align="'.$ele -> getAttribute("align").'">'.$ele -> getAttribute("label").'</th>';
							$res .= '<td>';
							
							$res .= $this -> fixKey( $data, $ele );
							
							if( $ele -> getAttribute("type") != 'notice' ){
							$res .= '<span class="text-gray">'.$des.'</span>';
							}
							
							$res .= '</td>';					
						$res .= '</tr>';						
					
					}
				
				break;				
				
			}
			
		}
	
		//转编码，将UTF-8转为当前编码
		if( $_G['product']['charset'] != "utf-8" ){
			$res = iconv("UTF-8",$_G['product']['charset'],$res);
		}
		
		//输出表单
		return $res;
	
	}
	
	function fixKey( $data, $ele, $grp = FALSE ){
		
		$key = $ele -> getAttribute("name");
			
		//是否数组
		preg_match( '/(\S*)\[(\S*)\]/', $key, $matche );
		
		//二维数组
		if( $matche ){
			$val = $data[$matche[1]][$matche[2]];
		}else{
			$val = $data[$key];
		}
		
		$val = dhtmlspecialchars( $val );
		
		$des = str_replace( array('{$TIME}','{$BASE}','{$HOST}'), array(time(),VI_BASE,VI_HOST), $ele -> getAttribute("comment") );
		
		//////////////////////////////////////////////
		
		$res = $grp ? $ele -> getAttribute("label") : '';		
	
		//类型
		switch( $ele -> getAttribute("type") ){
			
			case "hidden":
				$res .= '<input name="'.$key.'" type="hidden" value="'.$val.'" />';
				$res .= $key;
			break;
			
			case "password":
				$res .= '<input name="'.$key.'" type="password" class="text" size="'.$ele -> getAttribute("size").'" value="'.($val? $this -> password :'').'" />';
			break;
			
			case "text":								
				if( $ele -> getAttribute("readonly") ){
					$res .= '<input name="'.$key.'" size="'.$ele -> getAttribute("size").'" class="text" value="'.$val.'" '. $this ->  fixAttr( $ele ) .' readonly="true" />';	
				}else{
					$res .= '<input name="'.$key.'" size="'.$ele -> getAttribute("size").'" placeholder="'.$ele -> getAttribute("placeholder").'" class="text" value="'.$val.'" '. $this ->  fixAttr( $ele ) .' />';
				}								
			break;
			
			case "textarea":
				$res .= '<textarea name="'.$key.'" rows="'.$ele -> getAttribute("rows").'" placeholder="'.$ele -> getAttribute("placeholder").'" cols="'.$ele -> getAttribute("cols").'" '. $this ->  fixAttr( $ele ) .'>'.$val.'</textarea>';							
			break;
			
			case "select":
				$res .= '<select name="'.$key.'" style="width:'.$ele -> getAttribute("size").'px;">';
				
				//遍历子选项
				$sub = $ele->getElementsByTagName('option');
				
				for ($s=0; $s<$sub->length; $s++){														
					$res .= '<option value="'.dhtmlspecialchars($sub ->item($s) -> getAttribute("value")).'" '. ( $val == $sub ->item($s) -> getAttribute("value") ? 'selected="selected"' : '') .'>'.$sub ->item($s) -> nodeValue.'</option>';								
				}
				
				//////////////////////////
				
				//遍历文件夹
				if( $ele -> getAttribute("folder") ){
					
					//遍历皮肤目录
					$list = loop_dir( VI_ROOT.$ele -> getAttribute("folder") );
					
					foreach( $list as $file ){
						$res .= '<option value="'.$file.'" '. ( $val == $file ? 'selected="selected"' : '') .'>'.$file.'</option>';							
					}
					
				}
											
				$res .= '</select>';							
			break;
			
			case "radio":								
				
				//子选项
				$sub = $ele->getElementsByTagName('option');
				
				for ($s=0; $s<$sub->length; $s++){														
					$res .= '<label><input name="'.$key.'" value="'.dhtmlspecialchars($sub ->item($s) -> getAttribute("value")).'" class="radio" type="radio" '. ( $val == $sub ->item($s) -> getAttribute("value") ? 'checked="checked"' : '') .' />'.$sub ->item($s) -> nodeValue.'</option></label>';
				}															
											
			break;
			
			case "checkbox":
				
				//子选项
				$sub = $ele->getElementsByTagName('option');
				
				for ($s=0; $s<$sub->length; $s++){														
					$res .= '<label><input name="'.$key.'[]" value="'.dhtmlspecialchars($sub ->item($s) -> getAttribute("value")).'" class="checkbox" type="checkbox" '. ( in_array($sub ->item($s) -> getAttribute("value"),$val) ? 'checked="checked"' : '') .' />'.$sub ->item($s) -> nodeValue.'</option></label>';								
				}															
											
			break;
			
			case "notice":
			
				$res .= '<div class="highlight">'.$des.'</div>';
				
				$des = '';
				
			break;
			
			default:
				
				$res .= 'ERROR';								
											
			break;
			
		}
		
		return $res.' ';
		
	}
	
	//缓存写入权限测试
	function writable( $file ){
		return is_writable( $file ? $file : $this -> store );
	}
	
	function fixAttr( $ele ){
		$ext = '';		
		foreach( $ele -> attributes as $att ){
			if( strpos( $att -> name,  'data-' ) !== FALSE ){
				$ext .= $att -> name.'="'.$att -> value.'" ';
			}
		}
		if( $ext && $ele -> getAttribute("data-valid-name") == NULL ){
			$ext .= 'data-valid-name="'. $ele -> getAttribute("label") .'" ';
		}
		return $ext;
	}
	

	//读取XML配置
	function get(){		
		global $_G;
		
		if($this -> error) return 'File not found!';
							
		//读取插件
		$xml = simplexml_load_file($this -> layout);
		$dom = simplexml_import_dom($xml);
		
		$array = array();
		
		//遍历节点
		foreach ($dom->xpath('//key') as $key) {
			$name = $key['name'];
			$type = $key["type"];
			$format = $key["format"];
			
			switch($type){
				case "input":
				case "radio":
				case "hidden":
				case "select":
					$value = $key['value'];					
				break;
				
				case "checkbox":				
					$value = "";
					//遍历子元素
					foreach ($key->xpath('option') as $input) {
						if( $input["checked"] == "true" ){
							$value .= $input["value"].",";
						}						
					}
					$value = substr($value,0,-1);
				break;
				
				case "textarea":
					$value = $key;
				break;
			}
			
			//转编码
			if( $_G['product']['charset'] && $_G['product']['charset'] != "utf-8" ){
				$value = iconv("UTF-8",$_G['product']['charset'],$value);
			}
			
			$array[(string) $key['name']] = (string) $value;
			
		}
		
		return $array;		
	}
	
	/*
		保存配置
	
		make	重写
	*/
	function save( $method , $make = TRUE ){		
		global $_G;
		
		if($this -> error) return 'File not found!';
		
		$this -> loader();
		
		//用户提交数据
		$query = ( $method=='POST'? $_POST : $_GET ) ;
		
		/////////////////
		
		//原始存储数组
		$origi = $_G['setting'][$this -> appid];
		
		//还原密码并清除反斜杠
		foreach( $query as $key => $val ){
			if( is_array( $val ) ){
				foreach( $val as $mak => $sub ){
					if( $sub == $this -> password ){
						$query[$key][$mak] = $origi[$key][$mak];
					}else{
						$query[$key][$mak] = stripcslashes( $sub );
					}
				}
			}else{
				if( $val == $this -> password ){
					$query[$key] = $origi[$key];
				}else{
					$query[$key] = stripcslashes( $val );
				}
			}
			
		}
		
		/////////////////
		
		//生成伪静态规则
		//if( isset( $query['platform'] ) ) $this -> rewrite( $query['platform'], $query['domain'] );
		
		//生成Flash规则，全局配置
		if( isset( $query['timezone'] ) && isset( $query['domain'] ) ) $this -> domain( $query['domain'] );
		
		$str = '<?php'.chr(13);
		$str .= ' /*'.date("Y-m-t H:i:s").'*/ '.chr(13);
		$str .= '$_G[\'setting\'][\''.$this -> appid.'\'] = ';
		$str .= var_export( $query , TRUE );
		$str .= ';?>';
		
		/////////////////
		
		//更新缓存
		$_G['setting'][$this -> appid] = $query;
		
		//写入缓存
		return create_file( $this -> store, $str );
		
	}
	
	/*
		生成伪静态规则
		
	*/
	/*
	function rewrite( $mode, $domain ){
		global $_G;
		
		//当前目录
		$base = dirname( $this -> layout );
		
		//XML 地址
		$data = $base . '/rewrite.xml';
		
		//存储目录
		$save = $base . '/content/.htaccess';
		
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
	
	}
	*/
	
	function domain( $domain ){
	
		$find = "/<allow-access-from domain=\".*?\" \/>/i";
		$replace = '<allow-access-from domain="*'. ( $domain ? '.'.$domain : '' ) .'" />';
		$file = VI_ROOT . 'crossdomain.xml';
		$text = sreadfile( $file );
	
		if( preg_match( $find, $text ) ){
		
			$text = preg_replace($find, $replace, $text );
			
			create_file( $file , $text );
			
		}
		
	}

}
?>