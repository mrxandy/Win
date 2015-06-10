<?php
	
	if(!defined('VI_BASE')) {
		ob_clean();
		exit('Access Denied');
	}
	
	/////////////////////////////////
	
	$submit = getgpc('submit');
	$smarty->assign("submit", $submit );
	
	if( $submit ){
	
		// || !$_G['member']['modify']
		if( !$error && $config['USER_MODE'] == "REG" && !$_G['member']['id'] ){		
			$error = "login";		
		}
		
		if( !$error && $config["JOIN_SHOW"] != 'Y' ){		
			$error = "state";		
		}
		
		if( !$error && $_CACHE[$appid]['form']["start"]>time() || !$_CACHE[$appid]['form']['state'] ){		
			$error = "state";		
		}
		
		if( !$error && $_CACHE[$appid]['form']["expire"]<time() ){		
			$error = "expire";		
		}
		
		if( !$error && ( !$_SESSION['captcha'] || strtolower($_POST['captcha']) != $_SESSION['captcha'] ) ){		
			$error = "captcha";		
		}
		
		//没有错误
		if( $error == '' ){
		
			$name = getgpc('name');
			$quote = getgpc('quote');
			$description = getgpc('description');
			$state = ( $config["JOIN_STATE"] == 'N' ) ? -1 : 1;
			
			//var_dump( $_FILES['image'] );
			//exit;
		
			//遍历选项组
			foreach( $_CACHE['vote']['group'] as $gid => $group ){
			
				//记录用户提交数据
				//ACCOUNT	姓名	PHONE	电话号码	IDCARD	身份证	EMAIL	电子邮箱
				$data = format_json( fix_json( $_POST["config"] ) );
				
				//上传文件
				$file = Attach :: savefile( array( 'field'=> 'image', 'index'=> $gid, 'account'=> 'member' ) );
				
				$sql="INSERT INTO `mod:form_option`(fid,gid,name,image,quote,config,state,stat,dateline) VALUES('".$fid."',".$gid.",'".$name[$gid]."','".$file."','".$quote[$gid]."','".$data."', '".$state."', 0 ,".time().")";
				
				System :: $db -> execute( $sql );
				
			}		
			
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
			
			//清空验证码
			$_SESSION['captcha'] = $_SESSION["verify"] = '';
			
			$error = "";
			
			//更新缓存
			if( $config["JOIN_STATE"] == 'Y' ){
				Cached :: form( $_CACHE[$appid]['form']['appid'], $fid );	
			}
			
		}
		
	}else{
		
		if( $config["JOIN_SHOW"] != 'Y' ){		
			$error = "state";		
		}
		
	}
	
	$smarty->assign("error", $error );		
	
?>