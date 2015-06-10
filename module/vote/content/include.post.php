<?php
	
	if(!defined('VI_BASE')) {
		ob_clean();
		exit('Access Denied');
	}
	
	/////////////////////////////////
	
	//载入客户端函数包
	

	//加密验证
	$keys = rawurldecode( $_POST["keys"] );
	
	//错误信息
	$error = '';
	
	//获取代理IP
	if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'],  'unknown' ) ) {  
		$xff = $_SERVER['HTTP_X_FORWARDED_FOR']; 
		preg_match( '/^((?:\d{1,3}\.){3}\d{1,3})/', $xff, $match );
		$xip = $match ? $match[0] : '';
	}
	
	//获取当前 IP
	$ip = GetIP();
	
	//没有安装加密模块
	if( !function_exists("mcrypt_module_open") || !$keys ){	
	
		$error = "mcrypt";
		
	}else{
		
		$hash = unserialize( authcrypt( $keys, VI_SECRET, 'decode' ) );
		
		/*
		var_dump( $hash );
		var_dump( $_SESSION );
		var_dump( $ip );
		*/
		
		//验证码出错
		if( !is_array($hash) || $hash['ip'] != $ip || $hash['verify'] != $_SESSION["verify"] || $hash['verify'] == $_SESSION["before"] ){		
			$error = "verify";
		}
		
	}
	
	// || !$_G['member']['modify']
	if( !$error && $config['USER_MODE'] == "REG" && !$_G['member']['id'] ){		
		$error = "login";		
	}
	
	if( !$error && $config["VERIFY_CAPTCHA"] && ( !$_SESSION['captcha'] || strtolower($_POST[$hash['captcha']]) != $_SESSION['captcha'] ) ){		
		$error = "captcha";		
	}
	
	if( !$error && $config["FORM_MAX"]>0 && $config["FORM_MAX"] <= $_CACHE[$appid]['form']["stat"] ){		
		$error = "toplimit";		
	}
	
	if( !$error && $_CACHE[$appid]['form']["start"]>time() || !$_CACHE[$appid]['form']['state'] ){		
		$error = "state";		
	}
	
	if( !$error && $_CACHE[$appid]['form']["expire"]<time() ){		
		$error = "expire";		
	}
	
	if( !$error && trim( $config["USER_ZONE"] ) && !preg_match( '/('. str_replace( ' ', '|', trim( $config["USER_ZONE"] ) ) .')/i', convertip( $ip ), $matches) ){
	//if( !$error && $config["USER_ZONE"] && strpos( convertip( $ip ), $config["USER_ZONE"] ) === false ){		
		$smarty->assign("zone", convertip( $ip ) );
		$error = "zone";		
	}
	
	//没有错误
	if( $error == '' ){	
		
		//是否允许提交
		$allowpost = TRUE;
		
		////////////////////////////
		
		//今天开始时间
		$today = strtotime('today');
		
		//今天结束时间
		$tomorrow = strtotime('tomorrow');
			
		///////////////////////////
		
		//Cookies验证
		$cookie = 'app_'.$appid.'_'.$fid;
					
		if( $config["FORM_FREQ"] && $_COOKIE[$cookie] ){

			$error = "cookie";
			$allowpost = FALSE;
			
		}
		
		//IP 验证次数验证	
		if( $allowpost && $config["FORM_SPEED"] ){
					
			$sql="SELECT count(id) FROM `mod:form_data` WHERE ip = '".$ip."' and fid=".$fid;
			
			switch( $config["VERIFY_MODE"] ){
			
				//按天计算
				case 'DAY':
					$sql .= " and dateline > ".$today." and dateline < ".$tomorrow;
				break;
			
				//按小时计算
				case 'HOUR':
					$sql .= " and dateline between ". ( time() - 3600 ) ." and ".time();
				break;
				
			}
			
			if( System :: $db -> getValue($sql) >= $config['FORM_SPEED'] ){
				
				$error = "ip";
				$allowpost = FALSE;
				
			}

		}

		//IP 提交间隔验证
		if( $allowpost && $config["FORM_SPACE"] ){

			$sql="SELECT count(id) FROM `mod:form_data` where ip = '".$ip."' and dateline>".(time()-$config["FORM_SPACE"]*60)." and fid=".$fid;
			
			if( System :: $db -> getValue($sql) ){
				
				$error = "ip";
				$allowpost = FALSE;
				
			}

		}

		//注册用户单独处理
		if( $allowpost && $config['USER_MODE'] == "REG" ){

			//参加次数限制
			if( $config['FORM_NUMBER'] ){
				
				$sql="SELECT count(id) FROM `mod:form_data` WHERE uid = '".$_G['member']['id']."' and fid=".$fid;
				
				switch( $config["FORM_DATEDIFF"] ){
			
					//按天计算
					case 'DAY':
						$sql .= " and  dateline > ".$today." and dateline < ".$tomorrow;
					break;
				
					//按小时计算
					case 'HOUR':
						$sql .= " and dateline between ". ( time() - 3600 ) ." and ".time();
					break;
					
				}
						
				if( System :: $db -> getValue($sql) >= $config['FORM_NUMBER'] ){
					
					$error = "number";					
					$allowpost = FALSE;
				}
				
			}
			
			//注册用户，特定积分限制
			if( $allowpost && $config['FORM_SCORE'] ){
				
				$score = Passport :: money( $_G['member']['id'] );
						
				if( $score < $config['FORM_SCORE'] ){
					
					$error = "score";
					$smarty->assign("score", $score );
					
					$allowpost = FALSE;
				}
				
			}
			
			//注册用户，注册时间限制
			if( $allowpost && $config['FORM_REGDATE'] ){
						
				if( !$_G['member']['regdate'] || $_G['member']['regdate'] > $config['FORM_REGDATE'] ){
					
					$error = "regdate";
					
					$allowpost = FALSE;
				}
				
			}

		}
		
		if( $allowpost ){
		
			//处理表单
			$arr = array();
			
			//收集标记
			$res = FALSE;			
			
			foreach( $_CACHE[$appid]['group'] as $gid => $group ){
				
				$item = '';				
				$temp = 'G-'.$gid;
				$gset = $group["config"];
				
				//忽略空值
				if( empty( $_POST[$temp] ) ){
					continue;
				}else{
					$value = $_POST[$temp];	
				}
				
				//////////////////////

				$new = array();
				foreach( $value as $v ){
					//将没有找到的（和禁用的）子选项排除
					if( $_CACHE['vote']['option'][ $gid ][ $v ] && $_CACHE['vote']['option'][ $gid ][ $v ]['state'] ){
						array_push( $new , $v );
					}
				}
				
				//数组转成字符串
				$value = implode(",", $new);
				
				//////////////////////
				
				$arr[$temp] = $value;
				
				//更新统计
				if ( $value && in_array($group["type"],array("radio","checkbox","button")) ){
					
					$sql="UPDATE `mod:form_option` SET stat=stat+1 WHERE state>0 and id in(".$value.")";
					System :: $db -> execute( $sql );
					
					$res = TRUE;
				}
				
			}
			
			//未收数据
			if( count( $_CACHE[$appid]['group'] ) && $res === false ){	
				
				$error = "null";
			
			}else{
				
				//使用匿名扩展模式
				//ACCOUNT	姓名	PHONE	电话号码	IDCARD	身份证	EMAIL	电子邮箱
				$extend = getgpc('extend');				
				if( $config['USER_MODE'] == 'ANY' && is_array( $extend ) ){					
					$arr = array_merge( $extend, $arr );
				}

				//记录用户提交数据	
				$value = format_json( fix_json( $arr, TRUE ) );
				
				$sql="INSERT INTO `mod:form_data`(appid,fid,uid,username,config,state,dateline,xip,ip) VALUES('".$_CACHE[$appid]['form']["appid"]."',".$fid.",'".$_G['member']['id']."','".$_G['member']['username']."','".addslashes($value)."',0 ,".time().",'".$xip."','".$ip."')";
				
				System :: $db -> execute( $sql );
				
				if( $_G['member']['id'] ){
				
					//更新活跃统计
					$sql="UPDATE `mod:member` SET stat_level=stat_level+1 WHERE id=".$_G['member']['id'];
					System :: $db -> execute( $sql );		
				
				}
				
				//更新人数统计
				$sql="UPDATE `mod:form_form` SET stat=stat+1 WHERE id = ".$fid;
				System :: $db -> execute( $sql );
				
				//记录验证码
				$_SESSION["before"] = $hash['verify'];
				
				//清空验证码
				$_SESSION['captcha'] = $_SESSION["verify"] = '';
				
				//写入Cookies
				setcookie( $cookie, 'Y', time() + ( 60 * intval($config["FORM_FREQ"] ) ) );
				
				//送积分
				/////////////////////////////////
				
				if( $config["FORM_MONEY"] && $_G['member']['id'] ){
					
					//更新用户积分
					$money = Passport :: money( $_G['member']['id'] , $config["FORM_MONEY"] );
					
					$smarty->assign("money", $money );
					
				}
				
				/////////////////////////////////				
				
				//发邮件
				/////////////////////////////////
				
				if( $config["FORM_NOTICE"] == "Y" ){
					
					//创建者邮件地址
					$address = $_CACHE['system']['admin'][ $_CACHE[$appid]['form']["aid"] ]["email"];	
					
					if( $address ){
					
						$content = str_replace( array('{TIME}','{USER}','{EVENT}','{URL}','{VERYIDE}'),
									array( date('Y-m-d H:i:s') ,$_CACHE[$appid]['form']["account"], $_CACHE[$appid]['form']["name"], Module::get_index($appid)."?id=".$fid."&ref=email", VI_HOST ) ,$_G['setting']['passport']["event"] );
						
						if( $address ){
						
							System :: sendmail( $_CACHE[$appid]['form']["name"] ,$address,$config["FORM_SEND"], $content );
						
						}
					
					}
				
				}
				
				/////////////////////////////////
				
				$error = "";
			
			}
	  
		}
		
	}
	
	$smarty->assign("error", $error );
	
	//////////////// 写日志
	/*
	function writelog( $var ){
		$f = './log.txt';	
		if (!file_exists($f)) touch($f);
		$of = file_get_contents($f);
		$str = '$_VAR '.var_export($var, 1);
		file_put_contents($f, "\r\n".$str."\r\n------------------------------------------------" . $of);
	}
	*/
	
?>