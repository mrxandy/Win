<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	系统请求过滤函数库
*
*	$Id: library.php,v2.0 14:01 2009-03-31 Lay $
*/

class Tool{
	
	/**
     * 检验是否含有非法字符
     *
     * @return bool
     */
    public static function isSafety($str) {
        $utf = (bool) preg_match('//u', $str);
		if( $utf ){
			$match = preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/',$str);	
		}else{
			$match = preg_match('/^(?!_|\s\')[A-Za-z0-9_'.chr(0xa1).'-'.chr(0xff).'\s\']+$/',$str);	
		}
		return ($match != 0);
    }
	
}

/**
 * Mcrypt 加密/解密
 * @param type $data 要加密和解密的数据
 * @param type $key 密钥
 * @param type $mode  encode 默认为加密/decode 为解密
 * @return type 
 * @fixbug http://www.laruence.com/2012/09/24/2810.html
 */
function authcrypt($data, $key , $mode = 'encode') {
	if ($mode == 'decode') {
		$data = base64_decode($data);
	}
	if (function_exists('mcrypt_create_iv')) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		srand((double)microtime()*1000000);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	}
	if (isset($iv) && $mode == 'encode') {
		$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv);
	} elseif (isset($iv) && $mode == 'decode') {
		$passcrypt = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv);
	}
	if ($mode == 'encode') {
		$passcrypt = base64_encode($passcrypt);
	}
	return $passcrypt;
}

/*	 
	检查当前是否在规则内
	$array	IP规则
*/

//if (checkIP(array("127.0.0.1-127.0.0.255"))){
//if (checkIP(array("127.0.0.*"))){
//if (checkIP(array("127.0.0.1"))){
//	echo '成功匹配';
//};
function checkIP($array,$ip=""){

	$ip = ip2long($ip?$ip:$_SERVER['REMOTE_ADDR']);
	$n = count($array);
	
	$find = false;
	
	for($i=0;$i<$n;$i++){
	
		$x = trim($array[$i]);
		$x = str_replace(' ','',$x);
	
		//IP段	127.0.0.1-127.0.0.255
		if( strpos($x,'-') ){
		
			list($start,$end) = explode('-',$x);
			
			if($ip >= ip2long($start) && $ip <= ip2long($end)){
				$find = true;
			}
			
		//全IP段		127.0.0.*
		}elseif( strpos($x,'*') ){
		
			$start =  str_replace("*",0,$x);
			$end =  str_replace("*",255,$x);
			
			if( $ip >= ip2long($start) && $ip <= ip2long($end) ){
				$find = true;
			}
			
		//指定IP		127.0.0.1
		}else{			
			if($ip == ip2long($x)){
				$find = true;
			}			
		}
		
	}
	
	return $find;
}

function dstrpos($string, &$arr, $returnvalue = false) {
	if(empty($string)) return false;
	foreach((array)$arr as $v) {
		if(strpos($string, $v) !== false) {
			$return = $returnvalue ? $v : true;
			return $return;
		}
	}
	return false;
}

/*
	是否为手机访问
	$usemob		强制为手机访问
*/
function check_mobile( $usemob = '' ){
	global $_G;
	
	$mobile = array();
	static $mobilebrowser_list =array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
				'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
				'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
				'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
				'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
				'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
				'benq', 'haier', '^lct', '320x320', '240x320', '176x220');
	$pad_list = array('pad', 'gt-p1000');

	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

	if(dstrpos($useragent, $pad_list)) {
		return false;
	}
	if(($v = dstrpos($useragent, $mobilebrowser_list, true))) {
		$_G['mobile'] = $v;
		return true;
	}
	$brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
	if(dstrpos($useragent, $brower)) return false;

	$_G['mobile'] = 'unknown';
	if( $usemob === 'yes') {
		return true;
	} else {
		return false;
	}
}

/*
*	获取移动设备系统名称
*/
function check_device(){
	global $_G;
	
	$device = '';

	if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
		$device = "ipad";
	} else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
		$device = "iphone";
	} else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
		$device = "blackberry";
	} else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
		$device = "android";
	} else if( stristr($_SERVER['HTTP_USER_AGENT'],'windows phone') ) {
		$device = "windows phone";
	}

	if( $device ) {
		$_G['device'] = $device;
		return $device; 
	} return false; {
		$_G['device'] = 'unknown';
		return false;
	}
}

//检验email
function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

//检验网址
function isHttp($str){
	return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $str);
}

//检验qq
function isQQ($str){
	return preg_match("/^[1-9]\d{4,8}$/", $str);
}

//检验邮编
function isZip($str){
	return preg_match("/^[1-9]\d{5}$/", $str);
}

//检验身份证
function isIDCard($str){
	return preg_match("/^\d{15}(\d{2}[A-Za-z0-9])?$/", $str);
}

//检验是否是中文
function isChinese($str){
	return ereg("^[".chr(0xa1)."-".chr(0xff)."]+$",$str);
}

//检验是否是英文
function isEnglish($str){
	return preg_match("/^[A-Za-z]+$/", $str);
}

//检验是否是手机
function isMobile($str){
	return preg_match("/^((\(\d{3}\))|(\d{3}\-))?1\d{10}$/", $str);
}

//检验是否电话
function isPhone($str){
	return preg_match("/^((\(\d{3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}$/",$str);
}

//检验是否含有非法字符
function isSafe($str){
	return (preg_match("/^(([A-Z]*|[a-z]*|\d*|[-_\~!@#\$%\^&\*\.\(\)\[\]\{\}<>\?\/\\/\'\"]*)|.{0,5})$|\s/", $str) != 0);
}

//检验是否含为合法用户名
function isName($str){
	$pos = preg_match("/[\/\\:*\"'?<>|;,%\^]+/", $str);

	if($pos){
		return false;
	}else{
		return true;
	}
}

//检验是否为合法密码
function isPassword($str){
	return preg_match("/^(\w){6,20}$/", $str);
}

//检验日期是否合法
function isDateTime($time){
	return preg_match("/^[0-9]{4}(\-|\/)[0-9]{1,2}(\\1)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/",$time);
}

//检验是否为IP地址
function isIP($str){
	return preg_match("/^[0-9.]{1,20}$/", $str);
}

/*
function is_utf8($str) {
    $c=0; $b=0;
    $bits=0;
    $len=strlen($str);
    for($i=0; $i<$len; $i++){
        $c=ord($str[$i]);
        if($c > 128){
            if(($c >= 254)) return false;
            elseif($c >= 252) $bits=6;
            elseif($c >= 248) $bits=5;
            elseif($c >= 240) $bits=4;
            elseif($c >= 224) $bits=3;
            elseif($c >= 192) $bits=2;
            else return false;
            if(($i+$bits) > $len) return false;
            while($bits > 1){
                $i++;
                $b=ord($str[$i]);
                if($b < 128 || $b > 191) return false;
                $bits--;
            }
        }
    }
    return true;
}
*/

// Returns true if $string is valid UTF-8 and false otherwise.
function is_utf8($string) {

// From http://w3.org/International/questions/qa-forms-utf-8.html
return preg_match('%^(?:
[\x09\x0A\x0D\x20-\x7E] # ASCII
| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
)*$%xs', $string);

} // function is_utf8

function is_gb2312($str){
	for($i=0; $i<strlen($str); $i++) {
			$v = ord( $str[$i] );
			if( $v > 127) {
					if( ($v >= 228) && ($v <= 233) )
					{
							if( ($i+2) >= (strlen($str) - 1)) return true;  // not enough characters
							$v1 = ord( $str[$i+1] );
							$v2 = ord( $str[$i+2] );
							if( ($v1 >= 128) && ($v1 <=191) && ($v2 >=128) && ($v2 <= 191) ) // utf编码
									return false;
							else
									return true;
					}
			}
	}
	return true;
}

?>