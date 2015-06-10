<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

/*
	获取两个输入框组成的时间值
	$name		输入框键名	
	$now		默认时间（当前）	
*/
function gettime( $name, $now = NULL ){	
	$date = getgpc( $name );
	if( !$date || !isDateTime( $date['date'].' '.$date['time'] ) ){
		return $now ? $now : time();				
	}else{				
		return strtotime( $date['date'].' '.$date['time'] );
	}
}

function getcookie($key) {
	global $_G;
	return isset($_G['cookie'][$key]) ? $_G['cookie'][$key] : '';
}

/*
	获取某分类下的子分类ID
	$array		分类数组
	$pid		父分类ID
	$key		父分类标识
*/
function getsubid( $array, $pid, $implode = FALSE, $key = 'parent' ){
	$subcat = array();
	foreach( $array as $cat ){
		if( $cat[$key] == $pid ){
			$subcat[] = $cat['id'];
		}
	}
	return $implode ? implode(',',$subcat) : $subcat;
}

function dsetcookie($var, $value = '', $life = 0, $prefix = 1, $httponly = false) {
	global $_G;

	$config = $_G['cookie'];

	$_G['cookie'][$var] = $value;
	$var = ($prefix ? $_G['setting']['global']['prefix'] : '').$var;
	$_COOKIE[$var] = $value;

	if($value == '' || $life < 0) {
		$value = '';
		$life = -1;
	}

	$life = $life > 0 ? time() + $life : ($life < 0 ? time() - 31536000 : 0);
	$path = $httponly && PHP_VERSION < '5.2.0' ? VI_BASE.'; HttpOnly' : VI_BASE;

	$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
	if(PHP_VERSION < '5.2.0') {
		setcookie($var, $value, $life, $path, $_G['setting']['global']['domain'], $secure);
	} else {
		setcookie($var, $value, $life, $path, $_G['setting']['global']['domain'], $secure, $httponly);
	}
}

/* 获取来路地址 */
function dreferer(){
	global $_G;

	$_G['referer'] = !empty($_GET['referer']) ? $_GET['referer'] : $_SERVER['HTTP_REFERER'];

	return strip_tags($_G['referer']);
}


/*
	根据当前文件得到缩略图地址
	$file	源文件
	$size	尺寸
*/
function fix_thumb( $file , $size = NULL ){

	if( is_array( $size ) ){
		$size = $size[0] ."-". $size[1];
	}else{
		$size = is_string( $size ) ? $size : 'thumb';
	}
	
	return $file ? substr_replace($file,"-$size.",strrpos($file,"."),1) : '';
	
}

/*
	处理多域名下附件路径问题
	$text	内容块
	$root	是否返回本地地址
*/
function fix_attach( $text, $root = FALSE ){
	global $_G;
	
	return str_replace( VI_BASE.'attach/' , ( $root ? VI_ROOT : VI_HOST ).'attach/' , $text );
	
}

/*
	过滤XML标准规定的无效字节
	$xml	XML内容块
*/
function fix_xml( $xml ){
	return preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$xml);
}

/*
	编码或解码JSON，并修正JSON不支持中文的问题	
	$data	JSON字符串或数组
	$strip	是否清除转义字符串
	return	返回修正后的数组
*/
function fix_json( $data, $strip = FALSE ){
	global $_G;
	
	$temp = '';
	
	//如果参数是JSON字符串，就转换为数组
	if( is_string( $data ) ){
		
		//删除空白字符
		$data = preg_replace("/([\r\n\t\f])/"," ",$data);
		
		$temp = array();
		
		//兼容GBK中文
		if( $_G['product']['charset'] == "gbk" ){
		
			$array = json_decode( iconv('GBK', 'UTF-8', $data), true );
			
			if( is_array($array) ){
				foreach ($array as $key => $val){
					$temp[ iconv('UTF-8',$_G['product']['charset'], $key) ] = iconv('UTF-8', $_G['product']['charset'], $val);
				}
			}
			
			unset( $array );
			
		}else{
			
			//json 解码
			$temp = json_decode($data,true);	
		}
		
		
	//否则将数组转换为JSON字符
	}elseif( is_array( $data ) ){
		
		$temp = array();
				
		//兼容GBK中文，PHP 5.2 以下使用第三方 JSON 库（已对编码处理）
		//if( $_G['product']['charset'] == "gbk" && PHP_VERSION >= '5.2' ){				
		if( $_G['product']['charset'] == "gbk" ){
				
			foreach ($data as $key => $val){
			
				$val = ( $strip ? stripslashes( $val ) : $val );
			
				$temp[ iconv($_G['product']['charset'],'UTF-8', $key) ] = iconv( $_G['product']['charset'],'UTF-8', $val);

			}
			
		}else{
			$temp = $data;			
		}
		
		//json 编码
		$temp = json_encode($temp);
		
	}
	
	return $temp;
	
}

/*
	清除字符串或数组中的空白
*/
function fix_white( $param ){
	
	$param = is_array($param) ? array_map('fix_white', $param) : preg_replace("/([\r\n\t\f])/","",$param);
	
	/*
	//如果参数数组
	if(  is_array( $data ) ){
		
		$temp = array();		
				
		foreach ($data as $key => $val){
			$temp[$key] = fix_white( $val );
		}
		
	//否则是字符串
	}else{
		
		$temp = preg_replace("/([\r\n\t\f])/","",$data);
		
	}
	*/
	
	return $param;
	
}

/*
	清理 URL 请求参数
	$query		参数数组
	$set		设定项
*/
function fix_param( $query, $set = array() ){ 
    $queryParts = explode('&', $query); 
    
    $params = array(); 
    foreach ($queryParts as $param){ 
        $item = explode('=', $param); 
        $params[$item[0]] = $item[1]; 
    } 
	
	//var_dump( $params );
   
   /////////////////////////
   
   
    //return $params;	
	
	$tmp = array();
	foreach( $params as $k => $v ){
		
		//var_dump( $k, $v );
		//echo '<br />';
		
		//忽略掉空参数
		if( isset($set[$k]) === FALSE && $v == '' ) continue;
		
		//忽略掉自定义参数
		if( isset($set[$k]) && $set[$k] === FALSE ) continue;
		
		if( $set[$k] ){
			$tmp[] = $k.'='.$set[$k];
		}else{
			$tmp[] = $k.'='.$v;
		}
		
	}
	
	$params = implode('&',$tmp);
	
	//var_dump( $params );
	
	return $params;
}


/*
	格式化GET中的数组
	$name	参数名称
	$array	数组值
	$ignore	要忽略的值
*/
function format_get($name,$array,$ignore=array()){
	
	//兼容 Smarty
	//if( is_array($name) ) extract($name);
	
	//var_dump( $ignore );
	
	if( !is_array($array) ) return;
	
	$arg = '';
	foreach( $array as $key => $val ){
		if (!in_array($key, $ignore)) {
			$arg .= "&".$name."[$key]=".urlencode($val);
		}
	}
	
	//return  substr($arg,0,strlen($arg)-1);
	//exit( $arg );
	return $arg;
}


/*格式化数据表名称*/
function format_table($table){
	$table = str_replace("{TableSysPre}",VI_DBMANPRE,$table);
	$table = str_replace("{TableModPre}",VI_DBMODPRE,$table);
	return $table;	
}

/*
	将 JSON 中的中文字符反编码
	$string		JSON 字符串
	$strip		是否清除转义字符串
*/
function format_json( $string ){
	global $_G;
	return preg_replace("#\\\u([0-9a-f]{4}+)#ie", "iconv('".VI_UCS."', '".$_G['product']['charset']."//IGNORE', pack('H4', '\\1'))", $string );
}

/*格式化字符集名称*/
function format_charset( $charset, $reset = FALSE ){
	if( $reset ){
		return str_replace('utf8', 'utf-8', $charset);	
	}else{
		return str_replace('-', '', $charset);	
	}
}

/*字符截取,自动区分编码*/
function format_substr($str,$slen,$ext=""){
	global $_G;
	if($_G['product']['charset']=="utf-8"){
		return utf_substr($str,$slen,$ext);
	}else{
		return gbk_substr($str,$slen,$ext);
	}
}

/*
	格式化内容输出
*/
/*
function format_output( $output, $format = 'html', $charset = NULL ){
	global $_G;
	
	if( !$charset ) $charset = $_G['product']['charset'];
	
	switch( $format ){
		
		case 'xml':
			header('Content-Type: text/xml; charset='.$charset);
			$output = '<?xml version="1.0" encoding="'.$charset.'" ?>'.$output;
		break;
		
		case 'html':
			header('Content-type: text/html; charset='.$charset);		
		break;	
		
		case 'text':
			header('Content-Type: text/plain; charset='.$charset);	
		break;	
		
		case 'javascript':
			header('Content-type: text/javascript; charset='.$charset);		
		break;	
		
		case 'attachment':
			header("Cache-Control: public");
			header("Content-type:application/octet-stream; charset=".$charset);		
			header("Accept-Ranges: bytes");
		break;	
	}
	
	return $output;
}
*/

/*
	加载样式
	$src			路径
	$charset	字符集
	$version		版本号
*/
function loader_source( $dir, $filename ){

	is_string( $filename ) && $filename = array( $filename );
	
	foreach ( $filename as $file ) {
		require VI_ROOT . 'source/'.$dir.'/'.$file.'.php';
	}
	
}

/*
	加载样式
	$src			路径
	$charset	字符集
	$version		版本号
*/
function loader_style($src,$charset,$version){
	$style = '';
	if(is_array($src)){
		foreach ($src as $key => $value) {
			$style.= '<link type="text/css" rel="stylesheet" charset="'.$charset.'" href="'.$value.'?ver='.$version.'" />'.chr(13);
		}
	}else{
		return '<link type="text/css" rel="stylesheet" charset="'.$charset.'" href="'.$src.'?ver='.$version.'" />'.chr(13);
	}
	
	return $style;
}

/*
	加载脚本
	$src			路径
	$charset	字符集
	$version		版本号
*/
function loader_script($src,$charset,$version){
	$script = '';
	if(is_array($src)){
		foreach ($src as $key => $value) {
			$script.= '<script type="text/javascript" charset="'.$charset.'" src="'.$value.'?ver='.$version.'"></script>'.chr(13);
		}
	}else{
		return '<script type="text/javascript" charset="'.$charset.'" src="'.$src.'?ver='.$version.'"></script>'.chr(13);
	}
	
	return $script;
}


/*
	加载图片
	$src			路径
	$title			标题
	$class			样式
	$click			点击事件
*/
function loader_image( $src, $title="", $class="", $click=""){
	global $_G;
	return '<img src="'.VI_BASE."static/image/".$src.'" title="'.$title.'" alt="'.$title.'" class="'.$class.'" '.($click?'onclick="'.$click.'" style="cursor:pointer;"':'').' />';
}

///////////////////////////////////////

//getinitial
function getchar($str){

	$asc=ord(substr($str,0,1));

	//非中文
	if ($asc<160){
	
		//数字 
		if ($asc>=48 && $asc<=57){ 
			return chr($asc);		
		// A--Z 
		}elseif ($asc>=65 && $asc<=90){ 
			return chr($asc);
		// a--z 
		}elseif ($asc>=97 && $asc<=122){ 
			return chr($asc-32);
		}else{ 
			return '~'; //其他 
		}
		
		//中文
	}else{
	
		$asc = $asc*1000+ord(substr($str,1,1));
		
		//获取拼音首字母A--Z 
		if ($asc>=176161 && $asc<176197){
			return 'A';
		}elseif ($asc>=176197 && $asc<178193){
			return 'B';
		}elseif ($asc>=178193 && $asc<180238){
			return 'C';
		}elseif ($asc>=180238 && $asc<182234){
			return 'D';
		}elseif ($asc>=182234 && $asc<183162){
			return 'E';
		}elseif ($asc>=183162 && $asc<184193){
			return 'F';
		}elseif ($asc>=184193 && $asc<185254){
			return 'G';
		}elseif ($asc>=185254 && $asc<187247){
			return 'H';
		}elseif ($asc>=187247 && $asc<191166){
			return 'J';
		}elseif ($asc>=191166 && $asc<192172){
			return 'K'; 
		}elseif ($asc>=192172 && $asc<194232){
			return 'L';
		}elseif ($asc>=194232 && $asc<196195){
			return 'M';
		}elseif ($asc>=196195 && $asc<197182){
			return 'N';
		}elseif ($asc>=197182 && $asc<197190){
			return 'O';
		}elseif ($asc>=197190 && $asc<198218){
			return 'P';
		}elseif ($asc>=198218 && $asc<200187){
			return 'Q';
		}elseif ($asc>=200187 && $asc<200246){
			return 'R';
		}elseif ($asc>=200246 && $asc<203250){
			return 'S';
		}elseif ($asc>=203250 && $asc<205218){
			return 'T';
		}elseif ($asc>=205218 && $asc<206244){
			return 'W';
		}elseif ($asc>=206244 && $asc<209185){
			return 'X';
		}elseif ($asc>=209185 && $asc<212209){
			return 'Y';
		}elseif ($asc>=212209){
			return 'Z';
		}else{ 
			return '~';
		} 
	} 
}

/*
	字符转实体
	&#xxxxx;
*/
function entity_encode( $str, $charset ){
	preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/",$str,$r);
	$ar = $r[0];
	foreach($ar as $k=>$v) {
		if(ord($v[0]) < 128) {
			$ar[$k] = htmlentities($v);
		} else {
			//$v = iconv( $charset, "UCS-2", $v );
			$v = iconv( $charset, VI_UCS, $v );
			$ar[$k] = "&#".((ord($v[0]) << 8) + ord($v[1])).';';
		}
	}
	return join("",$ar);
	//return '&#' . base_convert(bin2hex( iconv( $charset, 'UCS-4', $str) ), 16, 10) . ';';
}

function html_unicode_encode( $str, $charset ){
	//return join('\u', str_split(array_pop(unpack('H*0', iconv( $charset, 'ucs-2', $str) )), 4));
	return join('\u', str_split(array_pop(unpack('H*0', iconv( $charset, VI_UCS, $str) )), 4));
}

//将内容进行UNICODE编码
function unicode_encode( $string, $charset ){

    $string = iconv( $charset, VI_UCS, $string );
    
    $len = strlen($string);
    $str = '';
    for ($i = 0; $i < $len - 1; $i = $i + 2){
    
    	$c1 = $string[$i];
		$c2 = $string[$i + 1];
    
        /*
        if (ord($c1) > 0){   //两个字节的文字
            $str .= '\u'.base_convert(ord($c1), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
        } else {
            $str .= $c2;
        }
        */
		
		/////////////////
		
		//第一个字节
		$chr = base_convert(ord( $c1 ), 10, 16);

		//两个字节的文字，处理特殊符号
		if( ord( $c1 ) > 0 || preg_match("/[\x7f-\xff]/", $c2 ) ){
		    $str .= '\u'. ( hexdec($chr) > 0xF ? '' : '0' ) . $chr . str_pad( base_convert(ord( $c2 ), 10, 16), 2, 0, STR_PAD_LEFT);
		}else {
		    $str .= $c2;
		}
        
    }
    return $str;
    
}
 
//将UNICODE编码后的内容进行解码
function unicode_decode( $name, $charset ){  //转换编码，将Unicode编码转换成可以浏览的utf-8编码
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches)){
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++){
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0){
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code).chr($code2);
                $c = iconv( VI_UCS, $charset, $c );
                $name .= $c;
            } else {
                $name .= $str;
            }
        }
    }
    return $name;
} 

function dhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	} else {
		$string = str_replace(array('&', '"', '<', '>', '\''), array('&amp;', '&quot;', '&lt;', '&gt;', '&#39;'), $string);
		if(strpos($string, '&amp;#') !== false) {
			$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
		}
	}
	return $string;
}

//随机颜色
function rand_color(){
	$string="0123456789ABCDEF";
	$rand='';
	for($i=0;$i<6;$i++){
		$rand .= substr($string,mt_rand(0,strlen($string)-1),1);
	} 
	$color="#".$rand;
	return $color;
}

function unescape($str) { 
	$str = rawurldecode($str); 
	preg_match_all("/%u.{4}|&#x.{4};|&#d+;|.+/U",$str,$r); 
	$ar = $r[0]; 
	foreach($ar as $k=>$v) { 
		  if(substr($v,0,2) == "%u") 
				   $ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,-4))); 
		  elseif(substr($v,0,3) == "&#x") 
				   $ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,3,-1))); 
		  elseif(substr($v,0,2) == "&#") { 
				   $ar[$k] = iconv("UCS-2","UTF-8",pack("n",substr($v,2,-1))); 
		  } 
	} 
	return join("",$ar); 
} 


//合并路径
function url_merge($path="") { 
	if ($path==""||!is_string($path)) return ; // ----0x0007---- 路径参数错误 
	
	$old = $path;
	$path = explode("/", $path); // 分割路径 
	$cur_path = array(""); 
	
	for ($i=0,$j=count($path);$i<$j;$i++) {
		
		if ($path[$i]=="..") array_pop($cur_path); 
		elseif ($path[$i]=="."||$path[$i]==str_repeat(".", strlen($path[$i]))) continue; // 忽略无用的相对路径地址 . 和 .... 等 
		else array_push($cur_path, $path[$i]); 
		
	} 
	$path = implode("/", $cur_path);
	
	//修正HTTP路径
	$path = str_replace('/http:/','http://',$path);
	$path = str_replace('/https:/','https://',$path);
	
	unset($cur_path);
	
	return $path.($old[strlen($old)-1]=="/"?"/":""); 
}

//取主机信息（包含域名）
function url_host(){
	//部分环境下有错误，字域名是 * 号
	return isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
}

//取基准目录（/ 以后的目录信息）
function url_base(){
	return str_replace("//","/",str_replace("\\","",dirname($_SERVER["PHP_SELF"])."/"));	
}

//取绝对地址（不包含子目录和最后一个/）
function url_fore(){
	
	//拼接协议
	$host = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://';
	
	//拼接主机
	$host .= url_host();
	
	//拼接端口	
	$host .= $_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '';
	
	return $host;
}

//chr字符转html
function chrToHTML($Str){
	$Str = str_Replace(CHR(10), "<br />",$Str);
	$Str = str_Replace(CHR(32), " ",$Str);
	$Str = str_Replace(CHR(9), "　",$Str);
	$Str = str_Replace(CHR(34),"&quot;",$Str);
	$Str = str_Replace(CHR(39),"&#39;",$Str);
	$Str = str_Replace(CHR(13), "",$Str);
	$Str = str_Replace(CHR(10)&CHR(10), "<p>",$Str);
	
	return $Str;
}


/*
	将内容格式成 js 脚本
	$str		原始内容
*/
function format_script( $str){
	$str = compress(trim($str));
	$str = str_replace('"','\"',$str);
	
	return 'document.write("'.$str.'");';
}

/*
	标签格式化并加上链接
	
	$tag	标签
	$url	链接地址
	$target	打开方式
	$space	间隔符
*/
function format_tag( $tag, $url, $target='', $space=' ' ){

	$list = explode(" ", str_replace(","," ",$tag) );
	$link = '';	
	for($i=0;$i<count($list);$i++){
		$link.='<a href="'.$url.urlencode($list[$i]).'" target="'.$target.'">'.$list[$i].'</a>';
		
		if( $i < count($list) ){
			$link .= $space;
		}
	}
	return $link;
}

/*
	截取过长URL(只能用于文件显示)
	$url	URL
	$len	最大长度
*/
function format_url( $url, $len ){
	$str_len = strlen($url);
	if($str_len>$len){
		$str=substr($url,0,$len/2);
		$str.="...";
		$str.=substr($url,$str_len-$len/2,$len/2);
	}else{
		$str=$url;
	}
	return $str;
}

/*
	格式化日期显示
	$date		时间戳
	$showDate	显示详细时间
*/
function format_date( $date, $showDate = 'Y-m-d H:i:s' ){
    $limit = time() - $date;
    
    if($limit < 60){
        return $limit . '秒钟前';
    }
    
    if($limit >= 60 && $limit < 3600){
        return floor($limit/60) . '分钟前';
    }
    
    if($limit >= 3600 && $limit < 86400){
        return floor($limit/3600) . '小时前';
    }
    
    if($limit >= 86400 and $limit<259200){
        return floor($limit/86400) . '天前';
    }
    
    if($limit >= 259200 and $showDate){
        return date( $showDate, $date );
    }else{
        return '';
    }
}

/*
	日期时间相加
	$interval	类型
	$number		数量
	$date		时间	
*/
function DateAdd ($interval, $number, $date) { 
	$date_time_array = getdate($date); 
	$hours = $date_time_array["hours"]; 
	$minutes = $date_time_array["minutes"]; 
	$seconds = $date_time_array["seconds"]; 
	$month = $date_time_array["mon"]; 
	$day = $date_time_array["mday"]; 
	$year = $date_time_array["year"]; 
	
	switch ($interval) {
		//年
		case "yyyy": $year =$number; break;
		
		//季度
		case "q": $month =($number*3); break;
		
		//月份
		case "m": $month =$number; break;
		
		case "y": 
		case "d": 
		case "w": $day =$number; break;
		
		//周
		case "ww": $day =($number*7); break;
		
		//小时
		case "h": $hours =$number; break;
		
		//分
		case "n": $minutes =$number; break;
		
		//秒
		case "s": $seconds =$number; break; 
	}
	
	$timestamp = mktime($hours ,$minutes, $seconds,$month ,$day, $year); 
	return $timestamp;
} 


//日期比较函数 
function FormatDateDiff($d1,$d2=""){
	
	if(!is_numeric($d1)) $d1=strtotime($d1); 
	if(!is_numeric($d2)) $d2=strtotime($d2);
	
	$s=$d2-$d1;

	if( floor($s / 31104000)>0 ){
	
		$str=floor($s / 31536000)." 年";
	}else{
		
		if( floor($s / 2592000)>0 ){
			$str=floor($s / 2592000)." 月";
		}else{
			if( floor($s / 86400)>0 ){
				$str=floor($s / 86400)." 天";
			}else{
				if( floor($s / 3600)>0 ){
					$str=floor($s / 3600)." 小时";
				}else{
					if( floor($s / 60)>0 ){
						$str=floor($s / 60)." 分钟";
					}else{
						$str=$s ." 秒";
					}
				}
			}
		}
	}
	 
	return $str; 
}

/*
	比较日期时间
*/
function DateDiff( $interval, $date1, $date2 ){
	$timedifference=$date2-$date1;
	switch($interval){
		case   "w":$retval=ceil($timedifference/604800);break;
		case   "d":$retval=ceil($timedifference/86400);break;
		case   "h":$retval=ceil($timedifference/3600);break;
		case   "n":$retval=ceil($timedifference/60);break;
		case   "s":$retval=$timedifference;break;
	}
	return $retval;
}

/*
	获取请求
	$key		索引
*/
function getgpc($k, $type='GP') {
	$type = strtoupper($type);
	switch($type) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		default:
			if(isset($_GET[$k])) {
				$var = &$_GET;
			} else {
				$var = &$_POST;
			}
		break;
	}

	return isset($var[$k]) ? $var[$k] : NULL;
}


/*
	获取数值
	$key		索引
	$default	默认值
*/
function getnum( $key, $default = 0 ){
	$num = getgpc( $key );
	$num = is_numeric($num) ? intval($num) : $default;
	return $num;
}



/*
	获取页码
	$key		索引
*/
function getpage( $key ){
	$num = getnum( $key, 1 );
	$num = $num < 1 ? 1 : $num;
	return $num;
}


/* 
Utf-8、gb2312都支持的汉字截取函数 
GetSubStr(字符串, 截取长度, 开始长度, 编码); 
编码默认为 utf-8 
开始长度默认为 0 
*/ 
function GetSubStr($string, $sublen, $start = 0, $code = 'UTF-8'){ 
    if( strtolower($code) == 'utf-8') 
    { 
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/"; 
        preg_match_all($pa, $string, $t_string); 
 
        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."..."; 
        return join('', array_slice($t_string[0], $start, $sublen)); 
    } 
    else 
    { 
        $start = $start*2; 
        $sublen = $sublen*2; 
        $strlen = strlen($string); 
        $tmpstr = ''; 
 
        for($i=0; $i< $strlen; $i++) 
        { 
            if($i>=$start && $i< ($start+$sublen)) 
            { 
                if(ord(substr($string, $i, 1))>129) 
                { 
                    $tmpstr.= substr($string, $i, 2); 
                } 
                else 
                { 
                    $tmpstr.= substr($string, $i, 1); 
                } 
            } 
            if(ord(substr($string, $i, 1))>129) $i++; 
        } 
        if(strlen($tmpstr)< $strlen ) $tmpstr.= "..."; 
        return $tmpstr; 
    } 
} 

/*
	GBK 中文截取，单字节截取模式
	
	$str	源字符串
	$slen	目标长度
	$flag	省略符
*/
function gbk_substr($str,$slen,$flag=""){
	$restr = "";
	$c = "";
	$str_len = strlen($str);
	$startdd = 0;
	if($str_len < $startdd+1) return "";
	if($str_len < $startdd + $slen || $slen==0) $slen = $str_len - $startdd;
	$enddd = $startdd + $slen - 1;
	for($i=0;$i<$str_len;$i++)
	{
		if($startdd==0) $restr .= $c;
		else if($i > $startdd) $restr .= $c;
		
		if(ord($str[$i])>0x80){
			if($str_len>$i+1) $c = $str[$i].$str[$i+1];
			$i++;
		}
		else{	$c = $str[$i]; }

		if($i >= $enddd){
			if(strlen($restr)+strlen($c)>$slen) break;
			else{ $restr .= $c; break; }
		}
	}
	if($restr != $str && $flag) $restr.=$flag;
	return $restr;
}
	
/*
	截取字符串
	
	$str	源字符串
	$slen	目标长度
	$flag	省略符
*/
function utf_substr($str,$len,$flag=""){
	if(!$str) return "";

	$src=$str;
	
	for($i=0;$i<$len;$i++){
		$temp_str=substr($str,0,1);
		if(ord($temp_str) > 127){
			$i++;
			if($i<$len)
			{
			$new_str[]=substr($str,0,3);
			$str=substr($str,3);
			}
		}else{
			$new_str[]=substr($str,0,1);
			$str=substr($str,1);
		}
	}
	
	$new_str=join($new_str);
	
	if( $new_str!==$src && $flag){
		$new_str.=$flag;
	}
	return $new_str;
}

//隐藏IP后两段
function hide_ip($ip){
	$iparray = explode('.', $ip);
	return $iparray[0].".".$iparray[1].".*."."*";
}

//隐藏电话号码后4位
function hide_phone($ip){
	if(strlen($ip)<6) return $ip;
	$str = substr($ip,0,strlen($ip)-4);
	$str = str_pad($str, strlen($ip) , "*");
	return $str;
}

//隐藏名字最后一个字
function hide_name($ip){
	if(strlen($ip)<2) return $ip;
	$str = format_substr($ip,2,"*");
	return $str;
}

//隐藏电子邮件
function hide_email($ip){

	if(strlen($ip)<6) return $ip;

	$at = strpos($ip,"@");
	if($at){
		$str = substr($ip,0,$at-4)."***";
		$str .= substr($ip,$at);
		
		return $str;
	}else{
		return $ip;
	}
}

//获取IP
function GetIP(){
	$realip = '';	//设置默认值
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
		$realip = $_SERVER['HTTP_CLIENT_IP'];
	}
	preg_match( '/^((?:\d{1,3}\.){3}\d{1,3})/', $realip, $match );
	return $match ? $match[0] : $_SERVER['REMOTE_ADDR'];
}

//获得当前的脚本网址
function GetCurUrl(){
	if(!empty($_SERVER["REQUEST_URI"])){
		$scriptName = $_SERVER["REQUEST_URI"];
		$nowurl = $scriptName;
	}else{
		$scriptName = $_SERVER["PHP_SELF"];
		if(empty($_SERVER["QUERY_STRING"])) $nowurl = $scriptName;
		else $nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
	}
	return $nowurl;
}

//获得当前的绝对网址
function GetAbsUrl(){
	return (isset($_SERVER['HTTP_SCHEME']) && $_SERVER['HTTP_SCHEME'] =='https'?'https':'http').'://'.$_SERVER['SERVER_NAME'].($_SERVER["SERVER_PORT"]!='80'?':'.$_SERVER["SERVER_PORT"]:'').$_SERVER["REQUEST_URI"];	
}

//获得当前的文件名
function GetCurFile(){
	$name = explode("/",$_SERVER["PHP_SELF"]);		
	return $name[count($name)-1];
}

function Text2Html($txt){
	$txt = str_replace("  ","　",$txt);
	$txt = str_replace("<","&lt;",$txt);
	$txt = str_replace(">","&gt;",$txt);
	$txt = preg_replace("/[\r\n]{1,}/isU","<br/>\r\n",$txt);
	return $txt;
}

//清除HTML标记
function ClearHtml($str){
	$str = str_replace('<','&lt;',$str);
	$str = str_replace('>','&gt;',$str);
	return $str;
}

 //取得文件夹大小
function foldersize( $d ) {
	$dir=dir($d);
	$size=0;
	if( $dir ){
		while (false !== $e = $dir->read()) {
			if ($e[0] == '.') {continue;}
			$c_dir=$d.'/'.$e; if(is_dir($c_dir)) $size=$size+foldersize($c_dir); else $size=$size+filesize($c_dir); 
		}
		$dir->close();
	}
	return $size;
}

//取得字节单位
function size_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g':
             $val *= 1024;
        case 'm':
             $val *= 1024;
        case 'k':
             $val *= 1024;
    }
    return $val;
}

/*
	得到文件扩展名
	$file	文件名
*/
function fileext( $filename ) {
	return strtolower(addslashes(trim(substr(strrchr($filename, '.'), 1, 10))));
}

/*
	得到文件内置参数
	$file	文件名
	$parm	参数名称，为空时将获取全部参数
*/
function fileparm( $file, $parm = NULL ) {
	global $_G;
	
	$text = sreadfile( $file );
	
	//处理单个参数
	if( $text && isset( $parm ) ){
		preg_match( "/\[$parm\]([\s\S]*)\[\/$parm\]/i", $text, $match );
		return iconv( 'UTF-8' , $_G['product']['charset'], empty($match[1]) ? '' : $match[1] );
	}
	
	//一次性获取全部参数
	if( $text && $parm === NULL ){
		
		//获取全部参数
		preg_match_all( "/\[(.*?)\]([\s\S]*)\[\/\\1\]/", $text, $match );
		
		if( $match ){
		
			//合并成二维数组
			$array = array_combine( $match[1], $match[2] );
			
			//将值转成当前编码
			$func = create_function('&$val, $key', '
			    global $_G;
			    $val = iconv( \'UTF-8\' , $_G[\'product\'][\'charset\'], $val );
			');
			
			/*
			$func = function( &$val, $key ) {
				global $_G;
			    $val = iconv( 'UTF-8' , $_G['product']['charset'], $val );
			};
			*/
			
			array_walk( $array, $func );
			
			return $array;
		}
	}
}

/*
	获得文件名
	$file		文件名
	$ext		是否包含扩展名，默认为 TRUE
*/
function filename( $file, $ext = TRUE ){

	$file = isset($file) ? str_replace( '\\', '/', $file ) : $_SERVER["PHP_SELF"];
	$list = explode( "/", $file );
	$name = $list[count($list)-1];
	
	return $ext ? $name : preg_replace('/\.[a-zA-Z]+$/', '', $name);
}

/*
	function get_name($file_name) {
    return preg_replace('/\.[a-zA-Z]+$/', '', $file_name);
}
	
*/

/*
	测试字符串是否在另一字符串中出现
	$string		当前字符串
	$find		要搜索的字符串
*/
function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}

/*
	调试信息
*/
function debuginfo() {
	global $_G;
	if(getglobal('setting/debug')) {
		$db = & DB::object();
		$_G['debuginfo'] = array('time' => number_format((dmicrotime() - $_G['starttime']), 6), 'queries' => $db->querynum, 'memory' => ucwords($_G['memory']));
		return TRUE;
	} else {
		return FALSE;
	}
}

function sizecount($size) {
	if($size >= 1073741824) {
		$size = round($size / 1073741824 * 100) / 100 . ' GB';
	} elseif($size >= 1048576) {
		$size = round($size / 1048576 * 100) / 100 . ' MB';
	} elseif($size >= 1024) {
		$size = round($size / 1024 * 100) / 100 . ' KB';
	} else {
		$size = $size . ' Bytes';
	}
	return $size;
}

function site() {
	return $_SERVER['HTTP_HOST'];
}


/*
	根据年月返回当月最大天数
	$e_y	年
	$e_m	月
*/
function GetDay($e_y,$e_m){
	$e_d = 31;
	if (($e_m==4 || $e_m==6 || $e_m==9 || $e_m==11) && $e_d>30){
		$e_d=30;
	}
	
	$e_mod=$e_y % 4;
	if ($e_mod>0 && $e_d>28 && $e_m==2){
		$e_d=28;
	}
	return $e_d;	
}

/*
	完整页码
	$page		当前页码
	$row_count	总记录数
	$page_size	页数
	$url		URL
	$style		样式名称
	$detail		是否显示明细
*/
function multipage( $page, $row_count, $page_size, $url, $style, $detail = true ){
	
	//总页数
	$count = ceil($row_count/$page_size);
	
	$page_link="<div class='".$style."'>";

	//第一页
	if($page<1){
		$page=1;
	}elseif($page>$count ){
		$page=$count ;
	}
	
	//链接组
	$group = floor(($page-1)/$page_size);

	//开始和结束
	$start = $group * $page_size+1;
	if($row_count==0){
		$start=0;
	}
	
	$end = ($group +1) * $page_size;
	if($end>$count ){
		$end=$count ;
	}
	
	if( $page!=1 && $start>$page_size ){
		$page_link.='<a href="'.parselink( $url, 1 ).'" title="第1页">1</a>';
		if($count >$start-1 && $start>1){
			$page_link.='<a href="'.parselink( $url, ($start-1) ).'" title="上一组,第'.$i.'页">'.($start-1).'</a>';
		}
		$page_link.='...';
	}
	
	for( $i=$start; $i<=$end; $i++){
		if($i==$page){
			$page_link.='<strong>'.$i.'</strong>';
		}else{
			$page_link.='<a href="'.parselink( $url, $i ).'" title="第'.$i.'页">'.$i.'</a>';
		}		
	}
	
	if( $page!=$count ){
		$page_link.='...';
		if($count >=$end+1){
			$page_link.='<a href="'.parselink( $url,($end+1)).'" title="下一组,第'.$i.'页">'.($end+1).'</a>';
		}
		$page_link.='<a href="'.parselink( $url,$count) .'" title="最末页">'.$count .'</a>';
	}
	
	if( $detail ) $page_link.="<span>页次:$page/".$count ." ".$page_size."条/页,共".$row_count."条 </span>";
	
	$page_link.="</div>";
	
	return $page_link;
}

/*
	处理页码链接
	$url	链接
	$page	分页号码
*/
function parselink( $url, $page ){
	
	//如果页面标签有出现
	if( strpos( $url, '{*page*}' ) !== FALSE ){
		return str_replace( '{*page*}', $page, $url );
	}else{
		return $url . $page;	
	}
	
}

/*
	简单页码
	$pagecount	页面数量
	$curpage	当前页码
	$mpurl		URL
	$style		样式名称
*/
function simplepage( $pagecount, $curpage, $mpurl, $style, $lang = array( 'prev'=>'上一页', 'next'=>'下一页' )) {
	$return = '';
	$next = $pagecount > $curpage ? '<a class="prev" href="'. parselink( $mpurl, $curpage + 1 ) .'">'.$lang['next'].'</a>' : '';
	$prev = $curpage > 1 ? '<a class="next" href="'.  parselink( $mpurl, $curpage - 1 )  .'">'.$lang['prev'].'</a>' : '';
	if($next || $prev) {
		$return = '<div class="'. $style .'">'.$prev.$next.'</div>';
	}
	return $return;
}

// Does not support flag GLOB_BRACE
/*
	遍历文件目录
	$pattern	路径及表达式
	$flags		附加选项
	$ignore		需要忽略的文件
*/
function rglob( $pattern, $flags = 0, $ignore = array() ) {

	/*
    $files = glob($pattern, $flags);
    $based = dirname( $pattern ).'/';
    
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
    	//if( substr( basename( $dir ), 0, 1 ) == '_' ) continue;
    	if( $ignore && in_array( $dir, $ignore ) ) continue;
        $files = array_merge($files, rglob( $dir.'/'.basename($pattern), $flags, $ignore ));
    }
    */
    
    //获取子文件
    $files = glob($pattern, $flags);
    
    //修正部分环境返回 FALSE 的问题
	if( is_array( $files ) === FALSE ) $files = array();
	
	//获取子目录
	$subdir = glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT);
	
	//////////////////////////
	
	//将反斜线修正为斜线
	$func = create_function( '&$val, $key', '
		$val = str_replace( \'\\\\\', \'/\', $val );
	');
	
	array_walk( $subdir, $func );
	
	array_walk( $ignore, $func );
	
	//////////////////////////
	
	if( is_array( $subdir ) ){
	    foreach( $subdir as $dir ) {
	    	//if( substr( basename( $dir ), 0, 1 ) == '_' ) continue;
	    	if( $ignore && in_array( $dir, $ignore ) ) continue;
	        $files = array_merge($files, rglob( $dir.'/'.basename($pattern), $flags, $ignore ) );
	    }
	}
    
    return $files;
}

/*
	遍历子目录
	$root	根目录
	$filter	排除在外的目录名
*/
function loop_dir( $root, $filter = array() ){
	
	$result = array();

	if ($handle = opendir($root)) {
		while (false !== ($file = readdir($handle))) {
			if( ($file  !=   ".") && ($file   !=   "..") && is_dir($root.'/'.$file) ){	
				//不在过滤列表中时追加到数组
				if( !$filter || !in_array( $file, $filter ) ) array_push( $result,$file );
			}
		}
		closedir($handle);
	}
	
	return $result;
}

/*
	遍历子文件
	$root	根目录
	$filter	排除在外的文件名
	$exta	限定文件类型
*/
function loop_file( $root, $filter = array(), $exta = array() ){
	
	$result = array();

	if ($handle = opendir($root)) {
		while (false !== ($file = readdir($handle))) {
			if  (($file  !=   ".") && ($file   !=   "..") && !is_dir($root.$file)){
				//不在过滤列表中时追加到数组
				if( !in_array( $file, $filter ) && ( count($exta) ? in_array( fileext($file), $exta ) : true ) ){
					array_push($result,$file);
				}
			}
		}
		closedir($handle);
	}
	
	return $result;
}

//获取文件内容
function sreadfile($filename) {
	$content = '';
	if(function_exists('file_get_contents')) {
		@$content = file_get_contents($filename);
	} else {
		if(@$fp = fopen($filename, 'r')) {
			@$content = fread($fp, filesize($filename));
			@fclose($fp);
		}
	}
	return $content;
}

/*
	读写文件
	$file	文件名称
	$text	写入内容
	$mode	读写方式，默认为写入方式
*/
function create_file( $file, $text, $mode = 'w' ){

    if( !@$handle=fopen( $file, $mode ) ) {
        return false;
    }
	if( !fwrite($handle, $text) ){
		fclose($handle);
		return false;
	}else{
		fclose($handle);
		return true;
	}

}

/*
	创建目录
	$file		目录地址
	$index	是否屏蔽索引
	$mode	用户权限
*/
function create_dir( $file, $index = FALSE , $mode = 0755 ){
	
	//var_dump( $file );
	//var_dump( file_exists($file) );
	
	clearstatcache();
	
	//如果不存在
	if ( !file_exists($file) ){
		
		//创建目录
		//create_dir( dirname($file), $index );
		
		//更改权限
		//mkdir( $file, $mode );		
		//chmod($file,$mode);
		
		//更改权限
		if( !@mkdir( $file, $mode, TRUE ) ) return FALSE;
		
		//屏蔽索引
		if( !$index ) create_file( $file.'/index.htm' ,'<meta http-equiv="refresh" content="0;url=http://www.veryide.com/" />');
		
	}		
	
	return $file;
}

//删除文件夹
function delete_dir( $dir ){
	if(substr($dir,-1) != '/') $dir .= '/';
	if(is_dir($dir)) {
		if ($dp = opendir($dir)) {
			while (($file=readdir($dp)) !== false) {
				if (is_dir($dir.$file) && $file!='.' && $file!='..') {
					delete_dir($dir.$file);
				}else {
				if (!is_dir($dir.$file)) {
					if(!@unlink($dir.$file)){
						return false;
					}
				}
				}
			}
			closedir($dp);
			
			if(!@rmdir($dir)){
				return false;
			}
		}
	}
	return true;
}

//获取指定长度随机字符
function rand_string($length){
	$hash = "";
	$chars = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789abcdefghijklmnpqrstuvwxyz";
	$max = strlen($chars) - 1;
	mt_srand((double)microtime() * 1000000);
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}

//输出斑马线
function zebra( $var, $val ){
	global $$var;
	if( $$var % 2 == 0 ){
		$$var++;
		return $val[0];
	}else{
		$$var++;
		return $val[1];	
	}
}
