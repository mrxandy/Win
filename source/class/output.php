<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	全局数据 API 接口
*/

class Output{
	
	
	/*
		文件输出格式
	*/
	public static $format = 'html';
	
	/*
		授权许可域名
	*/
	public static $domain = NULL;
	
	/*
		文件输出编码
	*/
	public static $charset = NULL;
	
	/*
		解密后的数据
	*/
	public static $authash = NULL;

	/*
		第一步，检查授权
	*/
	public static function authorized( $authkey ){
		
		self :: $authash = $hash = self :: decode_param( $authkey );
		
		if( $hash && self :: domain_check( $hash['domain'] ) ){
			self :: $domain = $hash['domain'];
		}
		
		$referer = $_SERVER['HTTP_REFERER'];
		
		//预览数据或调试时始终有效
		if( !$referer || strpos( $referer, VI_HOST ) !== FALSE ){
			return TRUE;
		}else{
			return $hash['domain'] && strpos( $referer, $hash['domain'] ) !== FALSE;
		}
			
	}

	
	/*
		第二步，输出文件头
	*/
	public static function format( $format = 'html', $charset = NULL ){
		global $_G;
		
		self :: $charset = $charset = $charset ? strtolower( $charset ) : $_G['product']['charset'];
		
		self :: $format = $format;
		
		//关闭缓冲
		//ob_end_clean();
		
		switch( $format ){
			
			case 'xml':
				header('Content-Type: text/xml; charset='.$charset);
			break;
			
			case 'html':
				header('Content-type: text/html; charset='.$charset);
			break;	
			
			case 'json':
				header('Content-type: text/javascript; charset='.$charset);
			break;	
			
			case 'csv':
				header("Cache-Control: public");
				header("Content-type:application/msexcel; charset=".$charset);
				header("Accept-Ranges: bytes");
			break;	
			
			case 'attachment':
				header("Cache-Control: public");
				header("Content-type:application/octet-stream; charset=".$charset);
				header("Accept-Ranges: bytes");
			break;	
			
			default:
			case 'text':
				header('Content-Type: text/plain; charset='.$charset);
			break;	
		}
		
	}

	/*
		第三步，输出内容包
		$dataset	数据集，PHP 数组
		$datatype	格式化方式
		$option		需要特别处理的字段
	*/
	public static function convert( $dataset, $datatype, $option ){
		global $_G;

		//解码
		$option['jsonde'] = $option['jsonde'] ? $option['jsonde'] : array();
		
		//使用逗号分隔
		$option['split'] = $option['split'] ? $option['split'] : array();	
		
		//使用空隔分隔
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
		
		//修正地址
		$option['nl2br'] = $option['nl2br'] ? $option['nl2br'] : array();
		
		//修正地址
		$option['cdata'] = $option['cdata'] ? $option['cdata'] : array();
		
		//字段别名
		$option['alias'] = $option['alias'] ? $option['alias'] : array();
		
		///////////////////////////////
		
		foreach( $dataset as &$row ){
							
			foreach( $row as $key => &$text ){
			
				//JSON解码
				if( in_array( $key, $option['jsonde'] ) ){
					$text = fix_json( $text );
				}
				
				//替换换行
				if( in_array( $key, $option['nl2br'] ) ){
					$text = preg_replace("(\r\n|\r|\n)",'<br />', $text );
				}
				
				//处理图片
				if( in_array( $key, $option['attach'] ) ){
					$text = fix_attach( $text );
				}
				
			}
			
		}
		
		unset( $row, $text );
		
		//修正分页参数
		if( $option['pagination'] > $option['pagecount'] ){
			$option['pagination'] = $option['pagecount'];
		}elseif( !$option['pagination'] || $option['pagination'] < -1 ){
			$option['pagination'] = 1;
		}
		
		///////////////////////////////
		
		switch( $datatype ){
	
			case "xml":
			
				$content.='<?xml version="1.0" encoding="'. self :: $charset .'"?>';
	
				$content.='<data pagesize="'.$option['pagesize'].'" rowscount="'.$option['rowscount'].'" pagecount="'.$option['pagecount'].'" pagination="'.$option['pagination'].'" domain="'.self :: $domain.'" charset="'.self :: $charset.'">';
				//$content.='<option>'.fix_json($option).'</option>';		
				
				foreach( $dataset as $row ){
					
					$n = 1;
					$content .= '<item ';
					foreach ($row as $key => $text) {
						
						if( !in_array( $key, $option['cdata'] ) ){
							//$text = dhtmlspecialchars(str_replace('$','\$',addslashes($text)));
							$content .= ''.$key.'='.'"'.$text.'" ';
						}
						
						$n++;
					}
					$content .= '>';
					
					foreach ($row as $key => $text) {
						
						if( in_array( $key, $option['cdata'] ) ){
							$content .= '<'.$key.'><![CDATA['.$text.']]></'.$key.'>';
						}
						
						$n++;
					}
					
					$content .= '</item>';
				
				}
				
				$content.='</data>';
			
			break;
	
			case "json":
			
				$content = '{"pagesize":"'.$option['pagesize'].'", "rowscount":"'.$option['rowscount'].'", "pagecount":"'.$option['pagecount'].'", "pagination":"'.$option['pagination'].'", "domain":"'.self :: $domain.'", "charset":"'.self :: $charset.'", "data" : [';
				
				$numrows = count($dataset);
				
				$x = 1;
				foreach( $dataset as $row ){
				
					$n = 1;
					$content .= '{';
									
					foreach( $row as $key => $text ){
						$content .= '"'.$key.'" : '.'"'. addslashes($text) .'"'.($n!=count($row)?',':'');
						$n++;
					}
					$content .= '}'.($x!=$numrows?',':'');
					
					$x++;
				}
				
				$content.=']}';
			
			break;
			
			case 'csv':
			
				//取第一条数据
				$newest = array_shift( $dataset );
				
				//获取所有字段名
				$fields = array_keys( $newest );
			
				$content .= '';
				foreach( $fields as $key ){
					$content .=  ( $option['alias'][$key] ? $option['alias'][$key] : $key ) . '	';
				}
				$content .= chr(13);
			
				foreach( $dataset as $row ){
					
					$content .= '';
					foreach( $row as $key => $text ){
						$content .= '="'.$text.'"	';
					}
					$content .= chr(13);
					
					//$report.= $row['account'].'	="'.$row["qq"].'"	="'.$row["phone"].'"	'.$row["email"].'	'.$row["blog"].chr(13);				
				}
			
			break;
	
		}
		
		return $content;
		
	}

	/*
		第四步，输出内容包
	*/
	public static function content( $output, $callback = NULL ){
		global $_G;
	
		//关闭缓冲
		ob_end_clean();
	
		//导出
		if( self :: $format == 'csv' ){
		
			//过滤不允许的字符
			$name = $callback ? preg_replace( '/(\/|\\\|\<|\>|\*|\?|:|"|\\|)+/', '', $callback ) : 'output';
			
			//PHP 5.3
			if( preg_match("/msie/i", $_SERVER["HTTP_USER_AGENT"] ) ){
				header("Content-Disposition:attachment; filename=\"".$name.".csv\"");
			}else{
				header("Content-Disposition:attachment; filename=\"".addslashes($name).".csv\"");
			}
			
			$output = iconv( $_G['product']['charset'], 'UTF-16LE//IGNORE', $output );
			
			return chr(255).chr(254).$output;
			
		}
		
		//转编码
		if( self :: $charset != $_G['product']['charset'] && in_array( self :: $charset, array("gbk","utf-8") ) ){
			$output = iconv( $_G['product']['charset'], self :: $charset, $output );
		}
		
		return $callback ? $callback.'( '.$output.' );' : $output;
	}

	//////////////// 会话传递相关 //////////////////

	/*
		获取用户绑定社交账号
	*/
	public static function encode_param( $domain ){
		return rawurlencode( authcrypt( serialize( $domain ), VI_SECRET ) );
	}

	/*
		获取用户绑定社交账号
	*/
	public static function decode_param( $domain ){
		return unserialize( authcrypt( rawurldecode( $domain ), VI_SECRET, 'decode' ) );
	}
		
	//检验域名是否有效
	public static function domain_check( $domain ){
		return preg_match("/^[A-Za-z:\d\.\/\-]+$/", $domain );
	}
		
	//生成接口调用参数
	public static function create_param( $apiurl, $param, $ignore = array(), $authkey = array() ){
		$links = '';
		
		if( $authkey ){
		
			$tmp = array();
			
			foreach( $authkey as $k ){
				$tmp[ $k ] = $param[ $k ];
				unset( $param[ $k ] );
			}
		
			$param['authkey'] = self :: encode_param( $tmp );
			
		}
	
		foreach( $param as $k => $v ){
			if( $v != '' && !in_array( $k, $ignore ) ) $links .= ( $links ? '&' : '' ) . $k . '=' . urlencode( is_array( $v ) ? implode(',', $v) : $v );
			
		}
		
		return $apiurl . '?' . $links;
	}

}