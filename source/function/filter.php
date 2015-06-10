<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	字符串、请求参数过滤函数包
*
*	$Id: filter.php,v2.0 14:01 2009-03-31 Lay $
*/


//反过滤特殊字符
function UnFilterHtml($Str){
	$Str = str_Replace( "<br/>",CHR(10) ,$Str);
	$Str = str_Replace( "<br />", chr(10),$Str);
	$Str = str_Replace( "", CHR(13),$Str);
	$Str = str_Replace( "&#39;",CHR(39),$Str);
	$Str = str_Replace( "&quot;",CHR(34),$Str);
	$Str = str_Replace( "&nbsp;", CHR(9),$Str);
	$Str = str_Replace( "&nbsp;", CHR(32),$Str);
	$Str = str_Replace( "&lt;", "<",$Str);	
	$Str = str_Replace( "&gt;", ">",$Str);    						
	$Str = str_Replace( "&amp;","&",$Str);
	$Str = str_Replace("&acute;","'",$Str);
	$Str = str_Replace("&#44",",",$Str);
	return $Str;
}
	
//过滤特殊字符
function FilterHtml($str){
    $str = str_Replace("'", "&acute;",$str);
    //'Str = Replace(Str,",","&#44")
    return $str;
}

//过滤UBB标签
function clear_ubb($content){
	
	//过滤图片
	$content = preg_replace("/\[img\](.+?)\[\/img\]/is",'',$content);
	
	return preg_replace("/\[[^\[\]]{1,}\]/m","",$content);
	
	//全部过滤，包括内容
	//return preg_replace("/\\[[^\\[\\]]+\\][\s\S]*?\\[\/[^\\[\\]]+\\]/i", '',$content);	
} 

function DeleteHtml($str){
	$str = trim($str);
	$str = strip_tags($str,"");
	$str = ereg_replace("\t","",$str);
	$str = ereg_replace("\r\n","",$str);
	$str = ereg_replace("\r","",$str);
	$str = ereg_replace("\n","",$str);
	$str = ereg_replace(" "," ",$str);
	return trim($str);
}

/*
	压缩HTML，删除无用空白
	
	注意：以下形式的注释暂时不能处理
	<script><!--
	--></script>	
*/
function compress($str){
	$str = trim($str);
	
	//清除JS单行注释 // 这种风格的
	$str = preg_replace("/^[\s]*\/\/.*/m","",$str);
	$str = preg_replace("/([\;\(\)\{\}][ \t]*)\/\/.*/","\\1",$str);
	
	//去除所有位置空白字符
	$str = preg_replace("/([\r\n\t\f])/","",$str);
	$str = preg_replace("/[\s]{2,}/"," ",$str);
	
	//清除JS多行注释 /* 这种风格的 */
	$str = preg_replace("/\/\*([\S\s]*?)\*\//","",$str);
	
	//去除两个标签之外的空白字符
	$str = preg_replace("~>\s+\r~", ">", preg_replace("~>\s+\n~", ">", $str));
	$str = preg_replace("~>\s+<~", "> <", $str);
	
	//去除HTML注释
	$str = preg_replace("/\<!--([\S\s]*?)--\>/", "", $str);
	
	return trim($str);
}


/*
	过滤全局输入
*/
function inject_filter( $param ){
	
	if (!get_magic_quotes_gpc()) {
		
		$param = is_array($param) ? array_map('inject_filter', $param) : addslashes($param);
		
		/*
		if (is_array($content)) {
			foreach ($content as $key=>$value) {
				
				//处理数组
				if( is_array($value) ){
					$content[$key] = inject_filter($value);
				}else{
					$content[$key] = addslashes($value);
				}			
				
			}
		} else {
			addslashes($content);
		}
		*/
		
	} 
	return $param;
}

/*
	检查SQL参数中是否存在危险参数
	$param		从外部传入的参数	
					存在危险参数时返回 FALSE，否则返回 TRUE
*/
function inject_check( $param ){

	// 进行过滤
	$check = eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $param);
	
	//有风险
	if ($check){
		return false;
	}else{
		return $param;
	}
}


?>