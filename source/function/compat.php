<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	PHP 5 以下版本兼容
*
*	$Id: compat.php,v1.0 12:25 2010-4-18 Lay $
*/

# Define parse_ini_string if it doesn't exist.
# Does accept lines starting with ; as comments
# Does not accept comments after values
if( !function_exists('parse_ini_string') ){
    /*
    function parse_ini_string( $string ) {
        $array = Array();

        $lines = explode("\n", $string );
        
        foreach( $lines as $line ) {
            $statement = preg_match(
"/^(?!;)(?P<key>[\w+\.\-]+?)\s*=\s*(?P<value>.+?)\s*$/", $line, $match );

            if( $statement ) {
                $key    = $match[ 'key' ];
                $value    = $match[ 'value' ];
                
                # Remove quote
                if( preg_match( "/^\".*\"$/", $value ) || preg_match( "/^'.*'$/", $value ) ) {
                    $value = mb_substr( $value, 1, mb_strlen( $value ) - 2 );
                }
                
                $array[ $key ] = $value;
            }
        }
        return $array;
    }
    */
    function parse_ini_string( $str, $ProcessSections = false, $scanner_mode = NULL ){
        $lines  = explode("\n", $str);
        $return = Array();
        $inSect = false;
        foreach($lines as $line){
            $line = trim($line);
            if(!$line || $line[0] == "#" || $line[0] == ";")
                continue;
            if($line[0] == "[" && $endIdx = strpos($line, "]")){
                $inSect = substr($line, 1, $endIdx-1);
                continue;
            }
            if(!strpos($line, '=')) // (We don't use "=== false" because value 0 is not valid as well)
                continue;
            
            $tmp = explode("=", $line, 2);
            if($ProcessSections && $inSect)
                $return[$inSect][trim($tmp[0])] = ltrim($tmp[1]);
            else
                $return[trim($tmp[0])] = ltrim($tmp[1]);
        }
        return $return;
    }
}

//stripos
if(!function_exists('stripos')) {
	function stripos($haystack, $needle, $offset = 0) {
		return strpos(strtolower($haystack), strtolower($needle), $offset);
	}
}

//json_encode
if (!function_exists('json_encode')) {
	
	require_once dirname(__FILE__).'/../class/json.php';
	
	function json_encode($arg)
	{
		global $services_json;
		if (!isset($services_json)) {
			$services_json = new Services_JSON();
		}
		return $services_json->encode($arg);
	}
}

//json_decode
if (!function_exists('json_decode')) {
	
	require_once dirname(__FILE__).'/../class/json.php';
	
	function json_decode($content, $assoc=false) {
		if ($assoc) {
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		}
		else {
			$json = new Services_JSON;
		}
		return $json->decode($content);
	}

}

if (!function_exists('property_exists')) {
    /**
     +----------------------------------------------------------
     * 判断对象的属性是否存在 PHP5.1.0以上已经定义
     +----------------------------------------------------------
     * @param object $class 对象实例
     * @param string $property 属性名称
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function property_exists($class, $property) {
        if (is_object($class))
         $class = get_class($class);
        return array_key_exists($property, get_class_vars($class));
    }
}

//读取数据
if(!function_exists('file_put_contents')) {
	function file_put_contents($filename, $s) {
		$fp = @fopen($filename, 'w');
		@fwrite($fp, $s);
		@fclose($fp);
		return TRUE;
	}
}

?>