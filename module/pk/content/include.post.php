<?php
	
	if(!defined('VI_BASE')) {
		ob_clean();
		exit('Access Denied');
	}
	
	/////////////////////////////////
	
	//防止重复提交
	$verify= $_POST["verify"];
	
	if( $config['USER_MODE'] == "REG" && !$_G['member']['id'] ){
		
		$smarty->assign("error", "login" );
		
	}elseif( !$verify || $verify== $_SESSION["__VERIFY"] ){
		
		$smarty->assign("error", "verify" );	

	}elseif( $config["FORM_MAX"]>0 && $config["FORM_MAX"] <= $_CACHE[$appid]['form']["stat"] ){
		
		$smarty->assign("error", "toplimit" );
		
	}elseif( $_CACHE[$appid]['form']["start"]>time() || !$_CACHE[$appid]['form']['state'] ){
		
		$smarty->assign("error", "state" );
		
	}elseif( $_CACHE[$appid]['form']["expire"]<time() ){
		
		$smarty->assign("error", "expire" );
		
	}else{	
		
		//是否允许提交
		$allowpost = TRUE;
		
		//会话验证		
		if( $config["FORM_SPEED"] ){
			
			switch( $config["VERIFY_MODE"] ){
				
				//Cookies验证
				case "CK":
				
					$cookie = $appid."-".$fid;
					
					if( $_COOKIE[$cookie] ){						
						
						$smarty->assign("error", "cookie" );
						
						$allowpost = FALSE;
					}				
				break;
				
				//IP提交数检查
				case "IP":
					
					$sql="SELECT id FROM `mod:form_data` WHERE ip = '".GetIP()."' and dateline>".(time()-$config["FORM_SPEED"]*60)." and fid=".$fid;
							
					$row = System :: $db -> getOne( $sql );

               		if( $row ){
						
						$smarty->assign("error", "ip" );
						
						$allowpost = FALSE;
					}				
				break;				
			}
		}
		
		//注册用户，不允许用户名重复
		if( $allowpost && $config['USER_MODE'] == "REG" && $config['FORM_NUMBER'] ){
			
			$sql="SELECT count(id) FROM `mod:form_data` WHERE uid = '".$_G['member']['id']."' and fid=".$fid;
					
			if( System :: $db -> getValue($sql) >= $config['FORM_NUMBER'] ){
				
				$smarty->assign("error", "number" );
				
				$allowpost = FALSE;
			}
			
		}
		
		if( $allowpost ){

			$OBJECT = $_POST["OBJECT"];
			$MESSAGE = $_POST["MESSAGE"];		//这里可以做个开关，是否强制要输入内容

			//已接收数据
			if( $OBJECT ){

				//处理表单
				$arr = array();

				//被投对象
				$arr["OBJECT"]  = $OBJECT;

				//消息主体
				$arr["MESSAGE"] = $MESSAGE;				

				//使用匿名扩展模式
				//ACCOUNT	姓名	PHONE	电话号码	IDCARD	身份证	EMAIL	电子邮箱
				$extend = getgpc('extend');				
				if( $config['USER_MODE'] == 'ANY' && is_array( $extend ) ){					
					$arr = array_merge( $extend, $arr );
				}

				//记录用户提交数据	
				$value = format_json( fix_json( $arr, TRUE ) );

				$sql="INSERT INTO `mod:form_data`(appid,fid,uid,username,config,state,dateline,ip) VALUES('".$_CACHE[$appid]['form']["appid"]."',".$fid.",'".$_G['member']['id']."','".$_G['member']['username']."','".addslashes($value)."','".$state."',".time().",'".GetIP()."')";
				System :: $db -> execute( $sql );

				if( $_G['member']['id'] ){				
					//更新活跃统计
					$sql="UPDATE `mod:member` SET stat_level=stat_level+1 WHERE id=".$_G['member']['id'];
					System :: $db -> execute( $sql );		

				}
				
				////////////////

				//测试结果统计				
				$config[$OBJECT."_RESULT"] = intval($config[$OBJECT."_RESULT"]);
				
				$config[$OBJECT."_RESULT"] = ( $err ? $config[$OBJECT."_RESULT"] : $config[$OBJECT."_RESULT"]+1 );
				
				//增加反义符
				//$config['POSITIVE_POINT'] = addslashes($config['POSITIVE_POINT']);
				//$config['NEGATIVE_POINT'] = addslashes($config['NEGATIVE_POINT']);

				//更新人数统计
				$sql="UPDATE `mod:form_form` SET stat=stat+1,config='". addslashes( format_json( fix_json( $config, TRUE ) ) ) ."' WHERE id = ".$fid;
				
				//exit( $sql );
				
				System :: $db -> execute( $sql );

				//更新缓存
				Cached :: rows( $appid, "SELECT * FROM `mod:form_form` WHERE id=".$fid, array( 'alias' =>'form', 'jsonde' => array('config') ) );

				//记录验证码
				$_SESSION["__VERIFY"] = $verify;

				//写入Cookies
				setcookie($cookie, 'Y', time() + (60*$config["FORM_SPEED"]) ,"" ,"");
				
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
				
				$smarty->assign("error", "" );	

			}else{

				$smarty->assign("error", "null" );		

			}

		}

	}
?>