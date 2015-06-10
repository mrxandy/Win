<?php 

/** 
*  类名: mobile 
*  描述: 手机信息类 
*  其他: 偶然 编写 
*/ 

class Mobile{ 

   /** 
   *   函数名称:   getPhoneNumber 
   *   函数功能:   取手机号 
   *   输入参数:   none 
   *   函数返回值: 成功返回号码，失败返回false 
   *   其它说明:   说明 
   */ 
   public static function getPhoneNumber(){
   
       if (isset($_SERVER['HTTP_X_NETWORK_INFO'])){ 

           $str1 = $_SERVER['HTTP_X_NETWORK_INFO']; 
           $getstr1 = preg_replace('/(.*,)(13[d]{ 9 })(,.*)/i','2',$str1); 

           Return $getstr1; 

        }elseif (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])){ 

           $getstr2 = $_SERVER['HTTP_X_UP_CALLING_LINE_ID']; 

           Return $getstr2; 

        }elseif (isset($_SERVER['HTTP_X_UP_SUBNO'])){ 

           $str3 = $_SERVER['HTTP_X_UP_SUBNO']; 
           $getstr3 = preg_replace('/(.*)(13[d]{ 9 })(.*)/i','2',$str3); 

           Return $getstr3; 

        }elseif (isset($_SERVER['DEVICEID'])){ 

           Return $_SERVER['DEVICEID']; 

        }else{ 
           Return false; 
        } 
    } 


   /** 
   *   函数名称:   getHttpHeader 
   *   函数功能:   取头信息 
   *   输入参数:   none 
   *   函数返回值: 成功返回号码，失败返回false 
   *   其它说明:   说明 
   */ 
  public static  function getHttpHeader(){ 
       $str = ''; 

       foreach ($_SERVER as $key=>$val){ 
           $gstr = str_replace("&","&",$val); 
           $str.= "$key -> ".$gstr." "; 
        } 
       Return $str; 

    } 


   /** 
   *   函数名称:   getUA 
   *   函数功能:   取UA 
   *   输入参数:   none 
   *   函数返回值: 成功返回号码，失败返回false 
   *   其它说明:   说明 
   */ 
   public static function getUA(){ 

       if (isset($_SERVER['HTTP_USER_AGENT'])){ 
           Return $_SERVER['HTTP_USER_AGENT']; 
        }else{ 
           Return false; 
        } 

    } 

   /** 
   *   函数名称:   getPhoneType 
   *   函数功能:   取得手机类型 
   *   输入参数:   none 
   *   函数返回值: 成功返回string，失败返回false 
   *   其它说明:   说明 
   */ 
   public static function getPhoneType(){ 

		$ua = $this->getUA(); 
		if($ua!=false){ 
		   $str = explode(' ',$ua); 
		   Return $str[0]; 
		}else{ 
		   Return false; 
		} 

    } 

   /** 
   *   函数名称:   isOpera 
   *   函数功能:   判断是否是opera 
   *   输入参数:   none 
   *   函数返回值: 成功返回string，失败返回false 
   *   其它说明:   说明 
   */ 
  public static  function isOpera(){ 
   
		$uainfo = $this->getUA(); 
		if (preg_match('/.*Opera.*/i',$uainfo)){ 
		   Return true; 
		}else{ 
		   Return false; 
		} 
    } 

   /** 
   *   函数名称:   isM3gate 
   *   函数功能:   判断是否是m3gate 
   *   输入参数:   none 
   *   函数返回值: 成功返回string，失败返回false 
   *   其它说明:   说明 
   */ 
   public static function isM3gate(){ 

		$uainfo = $this->getUA(); 
		if (preg_match('/M3Gate/i',$uainfo)){ 
		
		   Return true; 
		
		}else{ 
		   Return false; 
		} 
    } 

   /** 
   *   函数名称:   getHttpAccept 
   *   函数功能:   取得HA 
   *   输入参数:   none 
   *   函数返回值: 成功返回string，失败返回false 
   *   其它说明:   说明 
   */ 
   public static function getHttpAccept(){
   
		if (isset($_SERVER['HTTP_ACCEPT'])){ 
		   Return $_SERVER['HTTP_ACCEPT']; 
		}else{ 
		   Return false; 
		} 

    } 

   /** 
   *   函数名称:   getIP 
   *   函数功能:   取得手机IP 
   *   输入参数:   none 
   *   函数返回值: 成功返回string 
   *   其它说明:   说明 
   */ 
   public static function getIP(){ 
		
		$ip=getenv('REMOTE_ADDR'); 
		$ip_ = getenv('HTTP_X_FORWARDED_FOR'); 
		if (($ip_ != "") && ($ip_ != "unknown")){ 
		   $ip=$ip_; 
		} 
		
		return $ip; 

    } 
	
	/*
	*   函数名称:   getSeat 
	*   函数功能:   取得手机号码归属地
	*   输入参数:   number 
	*   函数返回值: 成功返回string 
	*   其它说明:   说明 
	*/
	public static function getSeat( $number ){
		global $_G;
		
		if( $number ){
			
			$url = 'http://life.tenpay.com/cgi-bin/mobile/MobileQueryAttribution.cgi?chgmobile='.$number;
			$ch = curl_init();
			
			curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type: application/x-www-form-urlencoded','Content-length: 0'));
			curl_setopt($ch,CURLOPT_URL,$url);
			//curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			
			$data = curl_exec($ch);
			curl_close($ch);
			
			$data = simplexml_load_string($data);
			
			return iconv( 'utf-8', $_G['product']['charset'], $data->province . $data->city );
		}
		
	}
} 
