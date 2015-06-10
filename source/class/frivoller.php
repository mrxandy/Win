<?php

/** 
* 百度空间相册图片防盗链破解程序 - PHP版 
* 
* 使用方法： 
* 
* http://yourdomain/frivoller.php?url=http://hiphotos.baidu.com/verdana/pic/item/baidupicture.jpg 
* 
* @author verdana 
* @version 1.0 
* @since July 16, 2006 
*/ 
class Frivoller{
	/** 
	* The HTTP Version (1.0, 1.1) , Baidu use version 1.1 
	* 
	* @var string 
	*/ 
	protected $version; 

	/** 
	* The HTTP response body 
	* 
	* @var string 
	*/ 
	protected $body; 

	/** 
	* The HTTP URL 
	* 
	* @var string 
	*/ 
	protected $link; 

	/** 
	* An array that containing any of the various components of the URL. 
	* 
	* @var array 
	*/ 
	protected $components; 

	/** 
	* The HTTP host 
	* 
	* @var string 
	*/ 
	protected $host; 

	/** 
	* The path of required file. 
	* (e.g. '/verdana/abpic/item/mygirl.png') 
	* 
	* @var string 
	*/ 
	protected $path; 

	/** 
	* The HTTP referer, extra it from original URL 
	* 
	* @var string 
	*/ 
	protected $referer; 

	/** 
	* The HTTP method, 'GET' for default 
	* 
	* @var string 
	*/ 
	protected $method = 'GET'; 

	/** 
	* The HTTP port, 80 for default 
	* 
	* @var int 
	*/ 
	protected $port = 80; 

	/** 
	* Timeout period on a stream 
	* 
	* @var int 
	*/ 
	protected $timeout = 30; 

	/** 
	* The filename of image 
	* 
	* @var string 
	*/ 
	protected $filename; 

	/** 
	* The ContentType of image file. 
	* image/jpeg, image/gif, image/png, image 
	* 
	* @var string 
	*/ 
	protected $contentType; 


	/** 
	* Frivoller constructor 
	* 
	* @param string $link 
	*/ 
	public function __construct( $link, $referer = NULL ){
	 
		// parse the http link 
		$this->parseLink( $link, $referer ); 

		// begin to fetch the image 
		$stream = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout); 
		if (!$stream) die ("ERROR: $errno - $errstrn"); 

		fputs($stream, $this->buildHeaders()); 

		$this->body = ""; 
		while (!feof($stream)) { 
			$this->body .= fgets($stream, 4096); 
		}
		
		//PHPSESSID=9909fc279eb5dce1628ba47e34f7c620

		// extract picture data 
		$this->extractBody($this->body); 

		// send 'ContentType' header for saving this file correctly 
		// 如果不发送CT，则在试图保存图片时，IE7 会发生错误 (800700de) 
		// Flock, Firefox 则没有这个问题，Opera 没有测试 
		header("Content-Type: $this->contentType"); 

		print $this->body;

		// save this picture 
		// file_put_contents('hello.jpg', $this->body); 

		fclose($stream); 
	} 


	/** 
	* Compose HTTP request header 
	* 
	* @return string 
	*/ 
	private function buildHeaders(){
		$request = "$this->method $this->path HTTP/1.1\r\n"; 
		$request .= "Host: $this->host\r\n"; 
		$request .= "Content-Type: image/jpeg\r\n"; 
		$request .= "Accept: */*\r\n"; 
		$request .= "Keep-Alive: 300\r\n"; 
		$request .= "Connection: close\r\n"; 
		$request .= "Referer: $this->referer\r\n"; 
		//$request .= "Cookie: PHPSESSID=111111111111111111111111\r\n";
		$request .= "Cache-Control: max-age=315360000\r\n\r\n"; 

		return $request; 
	} 


	/** 
	* Strip initial header and filesize info 
	*/ 
	private function extractBody(&$body){ 
		//echo '<pre>'.$body.'</pre>';
		
		//echo '<hr />';
		
		// The status of link 
		if(strpos($body, '200 OK') > 0) { 

			// strip header 
			$endpos = strpos($body, "\r\n\r\n"); 
			$body = substr($body, $endpos + 4); 

			// strip filesize at nextline 
			// 下面这句注释掉了,有问题
			//$body = substr($body, strpos($body, "\r\n") + 2); 
		} 
		//echo '<pre>'.$body.'</pre>';
		//exit();
	} 


	/** 
	* Extra the http url 
	* 
	* @param $link 
	*/ 
	private function parseLink( $link, $referer ){ 
		$this->link = $link; 
		$this->components = parse_url($this->link); 
		$this->host = $this->components['host']; 
		$this->path = $this->components['path']; 
		$this->referer = isset($referer) ? $referer : $this->components['scheme'] . '://' . $this->components['host'];
		$this->filename = basename($this->path); 

		// extract the content type 
		$ext = substr(strrchr($this->path, '.'), 1); 
		if ($ext == 'jpg' or $ext == 'jpeg') { 
			$this->contentType = 'image/pjpeg'; 
		} 
		elseif ($ext == 'gif') { 
			$this->contentType = 'image/gif'; 
		} 
		elseif ($ext == 'png') { 
			$this->contentType = 'image/x-png'; 
		} 
		elseif ($ext == 'bmp') { 
			$this->contentType = 'image/bmp'; 
		}else{ 
			$this->contentType = 'application/octet-stream'; 
		} 
	} 
} 

/*
// Get the url, maybe you should check the given url
$url=$_SERVER["argv"][0];
if ($url) { 
	new Frivoller($url); 
}
*/
