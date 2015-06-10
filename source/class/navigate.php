<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

/*
	PHP解析XML模板
	获取和设置XML节点
	用于修改和读取站点配置文件
	注意 xml 最好使用 utf-8 编码，这里会自动转编码
	
	*$c = new Navigate('templet.xslt','config.xml');
	* $c->transform();
	* $c->save();
*/

class Navigate{
	
	//配置文件
	private $store;
	
	//模板文件
	private $layout;
	
	//数组名称
	private $appid;
	
	//是否有错
	private $error = FALSE;

	//构造函数
	function __construct( $store, $layout, $appid ){
		
		$this -> store = $store;
		$this -> layout = $layout;
		$this -> appid = $appid;
		
		//检查文件
		if( !file_exists($this -> layout) || !file_exists($this -> store) ){
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
			$_G['navigate'][$this -> appid] = array();
		}
	}

	//转换成HTML表单
	function transform(){		
		global $_G;
		
		if($this -> error) return 'File not found!';
		
		//加载缓存
		$this -> loader();
		
		//临时数组
		$data = $_G['navigate'][$this -> appid];
		
		//追加元素
		$data = $this -> push( $data );
		
		//数组索引
		$indx = 0;
		
		foreach ( $data as $item ){
		
			if( $item['key'] ){
				$ele = $this -> find( $item['key'] );				
				//忽略不存在内置元素
				if( empty( $ele ) ) continue;
				$key = $item['key'];
			}else{
				$ele = array();
				$key = $indx;
			}
		
			$res .= '<tr class="'. zebra( $i, array( "line" , "band" ) ) .'">';
			$res .= '<td><strong>'.$item['key'].'</strong></td>';			
			$res .= '<td><input name="sort['.$key.']" size="1" class="text" value="'.$item['sort'].'" /></td>';			
			$res .= '<td><input name="name['.$key.']" size="10" class="text" value="'.$item['name'].'" /></td>';			
			$res .= '<td><input name="link['.$key.']" size="10" class="text" type="'.( $item['key'] ? 'hidden' : 'text' ).'" value="'.( $item['key'] ? $ele['link'] : $item['link'] ).'" />'.( $item['key'] ? $ele['link'] : '' ).'</td>';
			$res .= '<td><select name="target['.$key.']"><option></option><option value="_blank" '.( $item['target'] == '_blank' ? 'selected' : '' ).'>_blank</option><option value="_self" '.( $item['target'] == '_self' ? 'selected' : '' ).'>_self</option></select></td>';
			$res .= '<td><input name="show['.$key.']" type="checkbox" class="checkbox" value="true" '.( $item['show'] ? 'checked="true"' : '' ).' /></td>';
			
			$res .= '<td><input name="title['.$key.']" style="width:85%" type="text" class="text" value="'.( $item['title'] ? $item['title'] :  $ele['title'] ).'" /></td>';
			$res .= '<td><input name="keywords['.$key.']" style="width:85%" type="text" class="text" value="'.( $item['keywords'] ? $item['keywords'] :  $ele['keywords'] ).'" /></td>';
			$res .= '<td><input name="description['.$key.']" style="width:85%" type="text" class="text" value="'.( $item['description'] ? $item['description'] :  $ele['description'] ).'" /></td>';
			
			$res .= '<td>'.( $item['key'] ? '内置' : '自定义' ).'<input name="key['.$key.']" type="hidden" class="text" value="'.$item['key'].'" /></td>';
			$res .= '<td>'.( $item['key'] ? '' : '<a href="javascript:;" onclick="delrow(this);">删除</a>' ).'</td>';
			$res .= '</tr>';
			
			$indx++;
		}
		
		//输出表单
		return $res;
	
	}
	
	/*
		追加新导航
		$data	原始数据
	*/
	function push( $data ){
		global $_G;
		
		$xml = new DOMDocument;
		$xml->load($this -> layout);		
		$doc = $xml->getElementsByTagName('item');
		
		//遍历XML
		for ($i=0;$i<$doc->length;$i++){			
			$ele = $doc->item($i);
			
			//没有找此元素
			if( $this -> haskey( $data, $ele -> getAttribute("key") ) == false ){
				$res = array();
				foreach( $ele -> attributes as $att => $val ){					
					$res[$att] = iconv( "UTF-8",$_G['product']['charset'], $ele -> getAttribute( $att ) );
				}
				//break;
				array_push( $data, $res );
			}
		}
		
		return $data;
	}
	
	function haskey( $data, $key ){
		
		//遍历数组
		foreach ( $data as $item ){		
			if( $item['key'] == $key ){
				return true;
			}		
		}
		
		return false;
	}
	
	/*
		查找 XML 是否存在某元素
		$key	索引名称
	*/
	function find( $key ){
		global $_G;
		
		$xml = new DOMDocument;
		$xml->load($this -> layout);		
		$doc = $xml->getElementsByTagName('item');
		
		$res = array();
		
		for ($i=0;$i<$doc->length;$i++){			
			$ele = $doc->item($i);				
			if( $ele -> getAttribute("key") == $key ){			
				foreach( $ele -> attributes as $att => $val ){					
					$res[$att] = iconv( "UTF-8",$_G['product']['charset'], $ele -> getAttribute( $att ) );
				}
				break;
			}
		}
		
		return $res;
		
	}
	
	//缓存写入权限测试
	function writable(){
		return is_writable( $this -> store );
	}
	
	/*
		保存配置
		method		参数来源
	*/
	function save( $method ){		
		global $_G;
		
		if($this -> error) return 'File not found!';
		
		$query = ( $method=='POST'? $_POST : $_GET ) ;
		
		$data = array();
		
		//清除转义字符
		foreach( $query as $key => $val ){				
			foreach( $val as $item => $set ){				
				$data[$item][$key] = $set;
			}
		}
		
		//从小到大排序
		function array_asc( $a, $b ){
			if( $a["sort"] <= $b["sort"] ){
				return 0;
			}else{
				return $a["sort"] < $b["sort"] ? -1 : 1;
			}
		}
		
		
		//数据排序
		uasort( $data, "array_asc" );
		
		$str = '<?php'.chr(13);
		$str .= ' /*'.date("Y-m-t H:i:s").'*/ '.chr(13);		
		$str .= '$_G[\'navigate\'][\''.$this -> appid.'\'] = ';		
		$str .= var_export( $data , TRUE );		
		$str .= ';?>';
		
		//写入缓存
		return create_file($this -> store,$str);
		
	}

}
