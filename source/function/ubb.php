<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	UBB 执行函数包
*
*	$Id: veryide.ubb.php,v1.0 14:01 2008-9-23 Lay $
*/

/*
	UBB代码解析
	$msg	内容块
*/
function ubb_basic($msg){
		
	//$msg=preg_replace("/\[code\](.+?)\[\/code\]/is", $stx->stx_string(stripslashes('$0')) ,$msg);
	
	$msg=preg_replace("/\[code=(.+?)\](.+?)\[\/code\]/is", '<textarea name="code" rows="10" cols="50" class="\\1">\\2</textarea>' ,$msg);

	$msg=preg_replace("/\[url\](http:\/\/.+?)\[\/url\]/is",'<a href="\\1"  target="_blank">\\1</a>',$msg);
	$msg=preg_replace("/\[url\](.+?)\[\/url\]/is",'<a href=\"\\1\"  target="_blank">\\1</a>',$msg);
	$msg=preg_replace("/\[url=(http:\/\/.+?)\](.+?)\[\/url\]/is",'<a href="\\1"  target="_blank">\\2</a>',$msg);
	$msg=preg_replace("/\[url=(.+?)\](.+?)\[\/url\]/is",'<a href="\\1"  target="_blank">\\2</a>',$msg);
	
	$msg=preg_replace("/\[email\](.+?)\[\/email\]/is",'<a href="mailto:\\1">\\1</a>',$msg);
	
	$msg=preg_replace("/\[img\](.+?)\[\/img\]/is",'<img src="\\1" />',$msg);
	$msg=preg_replace("/\[br\]/is",'<br />',$msg);
	
	//颜色
	$msg=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is",'<font color="\\1">\\2</font>',$msg);
	
	//字号
	$msg=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is",'<font size="\\1">\\2</font>',$msg);
	
	//字体
	$msg=preg_replace("/\[face=(.+?)\](.+?)\[\/face\]/is",'<font face="\\1">\\2</font>',$msg);
	$msg=preg_replace("/\[font=(.+?)\](.+?)\[\/font\]/is",'<font face="\\1">\\2</font>',$msg);

	$msg=preg_replace("/\[b\](.+?)\[\/b\]/is","<strong>\\1</strong>",$msg);
	$msg=preg_replace("/\[i\](.+?)\[\/i\]/is","<em>\\1</em>",$msg);
	$msg=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$msg);
	
	$msg=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$msg);
	$msg=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$msg);
	$msg=preg_replace("/\[quote\](.+?)\[\/quote\]/is","<blockquote>\\1</blockquote>",$msg);
	$msg=preg_replace("/\[code\](.+?)\[\/code\]/is","<code>\\1</code>",$msg);
	
	$msg=preg_replace("/\[align=(\w{4,6})\](.+?)\[\/align\]/is",'<div align="\\1">\\2</div>',$msg);
	
	$msg=preg_replace("/\[(mp3|flash)=(\d*?|),(\d*?|)\]([^<>]*?)\[\/(mp3|flash)\]/is",'<embed width="\\2" height="\\3" src="\\4"></embed>',$msg);
	$msg=preg_replace("/\[embed=(\d*?|),(\d*?|),(.+?)\]([^<>]*?)\[\/embed\]/is",'<embed width="\\1" height="\\2" src="\\4" autostart="\\3"></embed>',$msg);
	
	return $msg;
}

/*
	转换UBB表情
	$msg	内容块
	$image	图片目录
	$ext	扩展名
*/
function ubb_smile($msg,$image,$ext){
	return preg_replace("/\[smile\](.+?)\[\/smile\]/is",'<img  border="0"  src="'.$image.'\\1'.$ext.'">',$msg);
}

/*
	清除UBB代码
	$content	内容块
*/
function ubb_clear( $content ){
	return preg_replace("/\[[^\[\]]{1,}\]/","",$content);;
}


?>