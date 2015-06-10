<?php
/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	浏览器、客户端函数包
*
*	$Id: client.php,v3 19:47 2007-07-27 Lay $
*/

/*
	下载报表数据
	$name		名称
	$content	内容
*/
function report( $name, $content ){	
	global $_G;
	
	//输出缓冲区内容并关闭缓冲
	//ob_end_flush();
	
	//清空缓冲区并关闭输出缓冲
	//ob_end_clean();
	
	//开启缓冲
	//ob_start();
	
	//输出CSV
	header("Cache-Control: public");
	header("Content-type:application/msexcel; charset=".$_G['product']['charset']);
	
	//过滤不允许的字符
	$name = preg_replace('/(\/|\\\|\<|\>|\*|\?|:|"|\\|)+/','',$name);
	
	//PHP 5.3
	if( preg_match("/msie/i", $_SERVER["HTTP_USER_AGENT"] ) ){
		header("Content-Disposition:attachment; filename=\"".$name.".csv\"");
	}else{
		header("Content-Disposition:attachment; filename=\"".addslashes($name).".csv\"");
	}
	
	header("Accept-Ranges: bytes");
	
	//部分环境中需要直接输出内容，并将文件后缀改为 .xls
	echo(chr(255).chr(254));
	echo(iconv($_G['product']['charset'],"UTF-16LE".'//IGNORE',$content));
	
}

//获取访问者浏览器
function browse(){
	$Agent = $_SERVER["HTTP_USER_AGENT"];
	
	$appname="";
	$appver="";
	
	if( eregi("MSIE" , $Agent) ){
	
		$appname = "IE";
		$reg = "/^.+MSIE (\d+\.\d+).+$/";
	
	}else{
	
		if (eregi("Chrome" , $Agent)){
			$appname = "Chrome";
			
			$reg = "/^.+Chrome\/(.*) Safari(.*)/";
			
		}else if (eregi("Safari" , $Agent)){
		
			//iPhone
			if (stripos("Mobile" , $Agent)){
				$appname = "Safari";
				$reg = "/^.+Version\/([\d\.]+?) Mobile.+$/";
				
			//Nokia
			}else if(stripos("Series60" , $Agent)){
				$appname = "Safari";
				$reg = "/^.+Safari\/([\d\.].*)+$/";
				
			//PC
			}else{
				$appname = "Safari";
				$reg = "/^.+Version\/([\d\.]+?) Safari.+$/";
			}
		}else if (eregi ("Opera" , $Agent)){
			$appname = "Opera";
			$reg = "/^.{0,}Opera\/(.+?) \(.+$/";
			
		}else if (eregi ("Firefox" , $Agent)){
			$appname = "Firefox";
			$reg = "/^.+Firefox\/([\d\.]+).{0,}$/";
			
		}else{
			$appname = "Unknown";
			$reg = "";
		}
		
	}
	
	if($reg){
		$appnver = preg_replace ($reg, "$1",$Agent);
	}else{
		$appnver = 0;
	}
	
	$browser=array($appname,$appnver);
	return $browser;
}

//获取访问者操作系统
function platform() {
	$Agent = $_SERVER["HTTP_USER_AGENT"];
	$os="";
	
	if (eregi('win',$Agent) && strpos($Agent, '95')) {
	$os="Windows 95";
	}
	elseif (eregi('win 9x',$Agent) && strpos($Agent, '4.90')) {
	$os="Windows ME";
	}
	elseif (eregi('win',$Agent) && ereg('98',$Agent)) {
	$os="Windows 98";
	}
	elseif (eregi('win',$Agent) && eregi('nt 5.0',$Agent)) {
	$os="Windows 2000";
	}
	elseif (eregi('win',$Agent) && eregi('nt 5.1',$Agent)) {
	$os="Windows XP";
	}
	elseif (eregi('win',$Agent) && eregi('nt 5.2',$Agent)) {
	$os="Windows Server 2003";
	}
	elseif (eregi('win',$Agent) && eregi('nt 6.0',$Agent)) {
	$os="Windows Vista";
	}
	elseif (eregi('win',$Agent) && eregi('nt',$Agent)) {
	$os="Windows NT";
	}
	elseif (eregi('win',$Agent) && ereg('32',$Agent)) {
	$os="Windows 32";
	}
	elseif (eregi('linux',$Agent)) {
	$os="Linux";
	}
	elseif (eregi('unix',$Agent)) {
	$os="Unix";
	}
	elseif (eregi('sun',$Agent) && eregi('os',$Agent)) {
	$os="SunOS";
	}
	elseif (eregi('ibm',$Agent) && eregi('os',$Agent)) {
	$os="IBM OS/2";
	}
	elseif (eregi('Mac',$Agent) && eregi('PC',$Agent)) {
	$os="Macintosh";
	}
	elseif (eregi('Mac',$Agent) && eregi('Intel',$Agent)) {
	$os="MacIntel";
	}
	elseif (eregi('PowerPC',$Agent)) {
	$os="PowerPC";
	}
	elseif (eregi('AIX',$Agent)) {
	$os="AIX";
	}
	elseif (eregi('HPUX',$Agent)) {
	$os="HPUX";
	}
	elseif (eregi('NetBSD',$Agent)) {
	$os="NetBSD";
	}
	elseif (eregi('BSD',$Agent)) {
	$os="BSD";
	}
	elseif (ereg('OSF1',$Agent)) {
	$os="OSF1";
	}
	elseif (ereg('IRIX',$Agent)) {
	$os="IRIX";
	}
	elseif (eregi('FreeBSD',$Agent)) {
	$os="FreeBSD";
	}
	if ($os=='') $os = "Unknown";
	return $os;
}

/////////////////////////

//返回IP所在地
function convertip($ip) {
	global $_G;
	$return = '';

	if(preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {
		$iparray = explode('.', $ip);

		if($iparray[0] == 10 || $iparray[0] == 127 || ($iparray[0] == 192 && $iparray[1] == 168) || ($iparray[0] == 172 && ($iparray[1] >= 16 && $iparray[1] <= 31))) {
			$return = '- LAN';
		} elseif($iparray[0] > 255 || $iparray[1] > 255 || $iparray[2] > 255 || $iparray[3] > 255) {
			$return = '- Invalid IP Address';
		} else {
			
			$tinyipfile = dirname(__FILE__).'/../ipdata/tiny.dat';
			$fullipfile = dirname(__FILE__).'/../ipdata/full.dat';
			
			if(@file_exists($fullipfile)) {
				$return = convertip_full($ip, $fullipfile);
			} elseif(@file_exists($tinyipfile)) {
				$return = convertip_tiny($ip, $tinyipfile);
			}
		}
	}

	return $_G['product']['charset'] == 'gbk' ? $return : iconv( 'gbk',$_G['product']['charset'], $return );

}

function convertip_tiny($ip, $ipdatafile) {

	static $fp = NULL, $offset = array(), $index = NULL;

	$ipdot = explode('.', $ip);
	$ip    = pack('N', ip2long($ip));

	$ipdot[0] = (int)$ipdot[0];
	$ipdot[1] = (int)$ipdot[1];

	if($fp === NULL && $fp = @fopen($ipdatafile, 'rb')) {
		$offset = unpack('Nlen', fread($fp, 4));
		$index  = fread($fp, $offset['len'] - 4);
	} elseif($fp == FALSE) {
		return  '- Invalid IP data file';
	}

	$length = $offset['len'] - 1028;
	$start  = unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);

	for ($start = $start['len'] * 8 + 1024; $start < $length; $start += 8) {

		if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip) {
			$index_offset = unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
			$index_length = unpack('Clen', $index{$start + 7});
			break;
		}
	}

	fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
	if($index_length['len']) {
		return '- '.fread($fp, $index_length['len']);
	} else {
		return '- Unknown';
	}

}

function convertip_full($ip, $ipdatafile) {

	if(!$fd = @fopen($ipdatafile, 'rb')) {
	//if(!$fd = @fopen($ipdatafile)) {
		return '- Invalid IP data file (full)';
	}

	$ip = explode('.', $ip);
	$ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

	if(!($DataBegin = fread($fd, 4)) || !($DataEnd = fread($fd, 4)) ) return;
	@$ipbegin = implode('', unpack('L', $DataBegin));
	if($ipbegin < 0) $ipbegin += pow(2, 32);
	@$ipend = implode('', unpack('L', $DataEnd));
	if($ipend < 0) $ipend += pow(2, 32);
	$ipAllNum = ($ipend - $ipbegin) / 7 + 1;

	$BeginNum = $ip2num = $ip1num = 0;
	$ipAddr1 = $ipAddr2 = '';
	$EndNum = $ipAllNum;

	while($ip1num > $ipNum || $ip2num < $ipNum) {
		$Middle= intval(($EndNum + $BeginNum) / 2);

		fseek($fd, $ipbegin + 7 * $Middle);
		$ipData1 = fread($fd, 4);
		if(strlen($ipData1) < 4) {
			fclose($fd);
			return '- System Error';
		}
		$ip1num = implode('', unpack('L', $ipData1));
		if($ip1num < 0) $ip1num += pow(2, 32);

		if($ip1num > $ipNum) {
			$EndNum = $Middle;
			continue;
		}

		$DataSeek = fread($fd, 3);
		if(strlen($DataSeek) < 3) {
			fclose($fd);
			return '- System Error';
		}
		$DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
		fseek($fd, $DataSeek);
		$ipData2 = fread($fd, 4);
		if(strlen($ipData2) < 4) {
			fclose($fd);
			return '- System Error';
		}
		$ip2num = implode('', unpack('L', $ipData2));
		if($ip2num < 0) $ip2num += pow(2, 32);

		if($ip2num < $ipNum) {
			if($Middle == $BeginNum) {
				fclose($fd);
				return '- Unknown';
			}
			$BeginNum = $Middle;
		}
	}

	$ipFlag = fread($fd, 1);
	if($ipFlag == chr(1)) {
		$ipSeek = fread($fd, 3);
		if(strlen($ipSeek) < 3) {
			fclose($fd);
			return '- System Error';
		}
		$ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
		fseek($fd, $ipSeek);
		$ipFlag = fread($fd, 1);
	}

	if($ipFlag == chr(2)) {
		$AddrSeek = fread($fd, 3);
		if(strlen($AddrSeek) < 3) {
			fclose($fd);
			return '- System Error';
		}
		$ipFlag = fread($fd, 1);
		if($ipFlag == chr(2)) {
			$AddrSeek2 = fread($fd, 3);
			if(strlen($AddrSeek2) < 3) {
				fclose($fd);
				return '- System Error';
			}
			$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
			fseek($fd, $AddrSeek2);
		} else {
			fseek($fd, -1, SEEK_CUR);
		}

		while(($char = fread($fd, 1)) != chr(0))
		$ipAddr2 .= $char;

		$AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
		fseek($fd, $AddrSeek);

		while(($char = fread($fd, 1)) != chr(0))
		$ipAddr1 .= $char;
	} else {
		fseek($fd, -1, SEEK_CUR);
		while(($char = fread($fd, 1)) != chr(0))
		$ipAddr1 .= $char;

		$ipFlag = fread($fd, 1);
		if($ipFlag == chr(2)) {
			$AddrSeek2 = fread($fd, 3);
			if(strlen($AddrSeek2) < 3) {
				fclose($fd);
				return '- System Error';
			}
			$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
			fseek($fd, $AddrSeek2);
		} else {
			fseek($fd, -1, SEEK_CUR);
		}
		while(($char = fread($fd, 1)) != chr(0))
		$ipAddr2 .= $char;
	}
	fclose($fd);

	if(preg_match('/http/i', $ipAddr2)) {
		$ipAddr2 = '';
	}
	$ipaddr = "$ipAddr1 $ipAddr2";
	$ipaddr = preg_replace('/CZ88\.NET/is', '', $ipaddr);
	$ipaddr = preg_replace('/^\s*/is', '', $ipaddr);
	$ipaddr = preg_replace('/\s*$/is', '', $ipaddr);
	if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
		$ipaddr = '- Unknown';
	}	

	return '- '.$ipaddr;

}

/*
	获取远程文件
	$url		文件地址
	$file		保存文件名
	$timeout	超时时间
*/
function getfile( $url, $file="", $timeout=60 ) {
	
	//clearstatcache();

	//提取文件名
	$filename = pathinfo( $url, PATHINFO_BASENAME );

	if( $file && is_dir( $file ) ){
		
		//构造存储名称
		$file = $file . $filename;
		
	}else{
		
		//提取文件名
	    $file = empty($file) ? $filename : $file;
	    
	    //提取目录名
	    $dir = pathinfo( $file, PATHINFO_DIRNAME );
	    
	    //目录不存在时创建
	    !is_dir($dir) && @mkdir($dir,0755,true);
	    $url = str_replace(" ","%20",$url);
		
	}

	//////////////////////////////

    if(function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $temp = curl_exec($ch);
        if(@file_put_contents($file, $temp) && !curl_error($ch)) {
            return $file;
        } else {
            return false;
        }
    } else {
    
    	//PHP 5.3 兼容
		if( PHP_VERSION >= '5.3' ){
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
			$opts = array(
			  "http"=>array(
			  "method"=>"GET",
			  "header"=>$userAgent,
			  "timeout"=>$timeout)
			);
			$context = stream_context_create($opts);
			$res = @copy($url, $file, $context);
		}else{
			$res = @copy($url, $file);
		}
		
        if( $res ) {
            return $file;
        } else {
            return FALSE;
        }
    }
}

?>