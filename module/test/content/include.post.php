<?php
	
	if(!defined('VI_BASE')) {
		ob_clean();
		exit('Access Denied');
	}
	
	/////////////////////////////////
	
	//数据特征值
	$stigma = md5( serialize( $_POST ) );
	
	if( $config['USER_MODE'] == "REG" && !$_G['member']['id'] ){
		
		$smarty->assign("error", "login" );
		
	}elseif( $stigma == $_SESSION['stigma'] ){
		
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
				
					$cookie = 'app_'.$appid.'_'.$fid;
					
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
		
			//处理表单
			$arr = array();
			
			//收集标记
			$res = FALSE;
			
			//收集正确数
			$right = array();
			
			//收集错误数
			$fault = array();
			
			//分数统计
			$score = -1;
			
			foreach( $_CACHE[$appid]['group'] as $gid => $group ){
				
				$item = '';				
				$temp = 'G-'.$gid;
				$gset = $group["config"];
				
				//如果需要计算得分
				if( $score == -1 && $gset["GROUP_SCORE"] ){
					$score = 0;	
				}
				
				////////////////////
				
				$value = '';
				
				//类型换算
				switch($group["type"]){
					
					//忽略分隔线
					case 'compart':
						
						continue 2;
						
					break;
						
					case 'checkbox':
						
						$array = isset($_POST[$temp]) ? $_POST[$temp] : array();
						
						$value = implode(",", $array);
						
					break;
						
					default:
					
						$value = $_POST[$temp];
						
						$array = explode(",", $value);
						
					break;
				}
				
				$arr[$temp] = $value;
				
				//更新统计
				if ( $value ){
					
					$sql="UPDATE `mod:form_option` SET stat=stat+1 WHERE id in(".$value.")";
					System :: $db -> execute( $sql );
					
					$res = TRUE;
				}
				
				////////////////////
				
				//判断对错			
				$try = FALSE;
				
				if( $value && $group["selected"] ){
				
					/*
					switch( $group["type"] ){
					
						case "radio":
						case "select":
							if( $value == $group["selected"] ){
								$try = TRUE;	
							}
						break;
					
						case "checkbox":
							if( count( array_diff( explode(",",$group["selected"]) , $array )) == 0 ){
								$try = TRUE;
							}					
						break;
					}
					*/
					
					if( count( array_diff( $group["selected"] , $array ) ) == 0 ){
						$try = TRUE;
					}
					
				}elseif( $value && !$group["selected"] ){
					$try = TRUE;
				}
				
				////////////////////
				
				if( $try ){
					
					$sql="UPDATE `mod:form_group` SET stat=stat+1 WHERE id = ".$gid;
					System :: $db -> execute( $sql );
					
					//累加正确
					$right[ $gid ] = explode( ',', $value );
					
					//累加得分
					$score += $gset["GROUP_SCORE"];
					
				}else{
					
					//累加错误
					$fault[ $gid ] = explode( ',', $value );
					
				}				
				
			}
			
			$smarty->assign("score", $score );
			
			//未收数据
			if( count( $_CACHE[$appid]['group'] ) && $res === false ){
				
				$smarty->assign("error", "null" );		
			
			}else{
				
				//是否测试成功
				$arr["RESULT"] = ( $fault ? "N" : "Y" );
				
				//本次得分
				$arr["SCORES"] = $score;
				
				//使用匿名扩展模式
				//ACCOUNT	姓名	PHONE	电话号码	IDCARD	身份证	EMAIL	电子邮箱
				$extend = getgpc('extend');				
				if( $config['USER_MODE'] == 'ANY' && is_array( $extend ) ){					
					$arr = array_merge( $extend, $arr );
				}

				//记录用户提交数据	
				$value = format_json( fix_json( $arr, TRUE ) );
				
				$sql="INSERT INTO `mod:form_data`(appid,fid,uid,username,config,state,dateline,ip) VALUES('".$_CACHE[$appid]['form']["appid"]."',".$fid.",'".$_G['member']['id']."','".$_G['member']['username']."','".addslashes($value)."',0 ,".time().",'".GetIP()."')";
				
				System :: $db -> execute($sql);
				
				if( $_G['member']['id'] ){
				
					//更新活跃统计
					$sql="UPDATE `mod:member` SET stat_level = stat_level+1 WHERE id=".$_G['member']['id'];
					
					System :: $db -> execute( $sql );
				
				}
				
				/***********************************/
				
				//按每份试题计算积分
				if( $config["FORM_SETTLE"] == "FORM" ){
				
					//扣积分
					/////////////////////////////////
					
					if( $fault && $config["FORM_DEDUCT"] && $_G['member']['id'] ){
						
						//更新用户积分
						$money = Passport :: money( $_G['member']['id'] , -$config["FORM_DEDUCT"] );
						
						$smarty->assign("money", $money );
						
						
					//送积分
					/////////////////////////////////
					
					}elseif( $config["FORM_MONEY"] && $_G['member']['id'] ){
						
						//更新用户积分
						$money = Passport :: money( $_G['member']['id'] , $config["FORM_MONEY"] );
						
						$smarty->assign("money", $money );
						
					}
					
					/////////////////////////////////
				
				}else{	//按单个问题计算积分
					
					//扣积分
					/////////////////////////////////
					
					if( $fault && $config["FORM_DEDUCT"] && $_G['member']['id'] ){
						
						//更新用户积分
						$money = Passport :: money( $_G['member']['id'] , -$config["FORM_DEDUCT"] * count( $fault ) );
						
						$smarty->assign("money", $money );
					
					}
					
					//送积分
					/////////////////////////////////
					if( $right && $config["FORM_MONEY"] && $_G['member']['id'] ){
						
						//更新用户积分
						$money = Passport :: money( $_G['member']['id'] , $config["FORM_MONEY"] * count( $right ) );
						
						$smarty->assign("money", $money );
						
					}
					
					/////////////////////////////////
					
				}
				
				/***********************************/
				
				
				//发邮件
				/////////////////////////////////
				
				if( $config["FORM_NOTICE"] == "Y" ){
					
					//创建者邮件地址
					$address = $_CACHE['system']['admin'][ $_CACHE[$appid]['form']["aid"] ]["email"];	
					
					if( $address ){
					
						$content = str_replace( array('{TIME}','{USER}','{EVENT}','{URL}','{VERYIDE}'),
									array( date('Y-m-d H:i:s') ,$_CACHE[$appid]['form']["account"], $_CACHE[$appid]['form']["name"], Module::get_index($appid)."?id=".$fid."&ref=email", VI_HOST ) ,$_G['setting']['passport']["event"] );
						
						if( $address ){
						
							System :: sendmail( $_CACHE[$appid]['form']["name"] , $address, $config["FORM_SEND"], $content );
						
						}
					
					}
				
				}

				/////////////////////////////////
				
				//测试结果统计（回答正确）
				$config["RESULT"] = ( is_numeric($config["RESULT"]) ? $config["RESULT"] : 0 );
				$config["RESULT"] = ( $fault ? $config["RESULT"] : $config["RESULT"]+=1 );
				
				//使用反斜线引用字符串
				$config['FORM_SUCCEED'] = addslashes( $config['FORM_SUCCEED'] );
				
				//更新人数统计
				$sql="UPDATE `mod:form_form` SET stat=stat+1,config='". format_json( fix_json($config) ) ."' WHERE id = ".$fid;
				System :: $db -> execute( $sql );
				
				//更新缓存
				Cached :: form( $_CACHE[$appid]['form']['appid'], $fid );	
				
				//反引用一个引用字符串
				$config['FORM_SUCCEED'] = stripslashes( $config['FORM_SUCCEED'] );
				
				//记录验证码
				$_SESSION['stigma'] = $stigma;
				
				//写入Cookies
				setcookie($cookie, 'Y', time() + (60*$config["FORM_SPEED"]) ,"" ,"");

				/////////////////////////////////
				
				//错误次数
				$smarty->assign("fault", $fault );
				
				//正确次数
				$smarty->assign("right", $right );
				
				if( $fault ){
					$smarty->assign("error", $fault );
				}else{
					$smarty->assign("error", "" );
				}
			
			}
			
		}
		
	}
?>