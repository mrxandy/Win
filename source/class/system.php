<?php

class System{

	//数据库实例
	public static $db = NULL;
	
	//数据库连接
	public static $connect_id = NULL;
	
	//类初始化
	public static function init(){
		
		//过滤输入
		$_POST	= inject_filter($_POST);
		$_GET	= inject_filter($_GET);
		
		self :: init_input();		
		
		//是否为移动设备
		check_mobile();
		
		//获取移动设备名
		check_device();
		
	}
	
	//初始化参数变量
	public static function init_input(){
		global $_G;
		
		//系统用户信息，过滤来自同一个 SID 的不同客户端
		$_G['manager'] = array();
		
		//兼容 1.5 会话，升级时需要，1.7 移除
		if( isset( $_SESSION['Manager'] ) && !isset( $_SESSION['manager'] ) ){
			$_SESSION['manager'] = $_SESSION['Manager'];
			$_SESSION['manager']['authkey'] = self :: authkey();
			unset($_SESSION['Manager']);
		}
		
		if( isset($_SESSION['manager']) && self :: authkey( $_SESSION['manager']['authkey'] ) ){
			$_G['manager'] = $_SESSION['manager'];
		}
		
		////////////////////
		
		//系统会员信息，过滤来自同一个 SID 的不同客户端
		$_G['member'] = array();
		
		if( isset($_SESSION['member']) && self :: authkey( $_SESSION['member']['authkey'] ) ){
			$_G['member'] = $_SESSION['member'];
		}
		
		////////////////////
		
		//保存 Cookie
		$prelength = strlen( $_G['setting']['global']['prefix'] );
		foreach($_COOKIE as $key => $val) {
			if( substr($key, 0, $prelength) == $_G['setting']['global']['prefix'] ) {
				$_G['cookie'][substr($key, $prelength)] = $val;
				//$_G['cookie'][$key] = $val;
			}
		}
		
		////////////////////
	
		//合并表单请求参数
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
		//	$_GET = array_merge($_GET, $_POST);
		}
		
		//保存请求参数
		$_G['parameter'] = $_GET;
		
		//当前绝对地址
		$_G['runtime'] = array( 'absolute' => GetAbsUrl(), 'current' => GetCurFile() );
		
	}
	
	/*
		生成或校验随机安全字符串
		$authkey	需要校验的字符串
	*/
	public static function authkey( $authkey = NULL ){
		if( isset( $authkey ) ){
			return $authkey === md5( VI_START . $_SERVER['HTTP_USER_AGENT'] );
		}else{
			return md5( VI_START . $_SERVER['HTTP_USER_AGENT'] );
		}
	}

	/*
			打开或关闭 MySQL
			$close	为空或 false 为打开数据库，true 关闭数据库
	*/
	public static function connect( $close = FALSE ){
		global $_G;
		
		//如果数据库已打开，则关闭
		if( self :: $db ){
	
			self :: $db -> close();
			self :: $db = NULL;
				
		}else{
	
			//连接数据库
			self :: $db = new mysql_db( VI_DBHOST, VI_DBUSER, VI_DBPASS, VI_DBNAME );
			
			//调试模式
			if( $_G['setting']['global']['debug'] == "on" ) self :: $db->setDebug();
			
			self :: $db -> setCharset( VI_DBCHARSET ? VI_DBCHARSET : $_G['product']['charset'] );
			self :: $db -> connection( TRUE );
			self :: $connect_id = self :: $db ->_db_connect_id;			
			
			//计划任务
			Module :: init_cron();
			
			return self :: $db ->_errno;
				
		}                
	}

	//系统设置
	public static function reader_config(){
		global $_G;
		
		return '
Mo.store.site		="'.$_G['setting']['global']['site'].'";
Mo.store.link		="'.$_G['setting']['global']['url'].'";
Mo.store.base		="'.VI_BASE.'";
Mo.store.build		="'.$_G['product']['build'].'";
Mo.store.appname		="'.$_G['product']['appname'].'";
Mo.store.version		="'.$_G['product']['version'].'";
Mo.store.licence		="'.$_G['licence']['type'].'";
Mo.store.host		="'.VI_HOST.'";
Mo.store.prefix		="'.$_G['setting']['global']['prefix'].'";
		';
	}
	
	/*
			页面重定向
			$url		目标地址
			$msg		页面消息
	*/
	public static function redirect( $url, $msg ){
		$msg = urlencode($msg);
		if($msg){
			if (strrpos($url,"=")>0 || strrpos($url,"%3d")>0){
				$url.="&VMSG=".$msg;
			}else{
				$url.="?VMSG=".$msg;
			}
		}
		header("Location: ".$url);
		exit;
	}
	
	//////////////////////////////////
	
	/* 加载跨域脚本 */
	public static function cross_domain(){
		global $_G;
		
		//跨域支持（子域名必需）
		return $_G['setting']['global']['domain'] ? 'document.domain = "'. $_G['setting']['global']['domain'] .'";' : '';
	}

	//////////////////////////////////

	/*
			用户登录验证
			$username               用户名
			$password               密码（md5 过的）
	*/
	public static function admin_login( $username, $password ){
		global $_G;
			
		if( !$password || !$username ){
			
			//登录信息不能为空!                        
			return -1;
		
		}elseif( $_G['setting']['global']['captcha']['show'] == 'on' && ( !$_SESSION['captcha'] || strtolower($_POST['captcha']) != strtolower($_SESSION['captcha']) ) ){
			
			//验证码输入错误!                        
			return -2;
		
		}elseif( strlen($password) != 32 || !$_SESSION['RNDCODE'] || $_SESSION['RNDCODE'] != $_POST["rndcode"] ){
								
			//写入安全日志
			self :: insert_event( "login",time(),time(),"尝试登录系统时使用密码不符合规则：".$password);
			
			//密码或随机验证输入错误!
			return -3;
				
		}elseif( $_G['setting']['global']["ipzone"] && !checkIP( explode("\n",$_G['setting']['global']["ipzone"]) ,GetIP()) ){
								
			//写入安全日志
			self :: insert_event( "login",time(),time(),"尝试登录系统时使用的IP不在允许的范围内：".GetIP());
			
			//当前IP不在授权的范围!
			return -4;
				
		}else{
				
			//登录脚本
			$sql = "SELECT id,gid,account,avatar,email,phone,theme,last_login,last_active,stat_login from `sys:admin` WHERE ( account='".$username."' and password = md5( concat( '".$password."', `salt` ) ) and `state`>0 ) LIMIT 0, 1";
			
			//查询数据库(Manager)
			$row = self :: $db -> getOne($sql);
			
			if( is_array( $row ) == FALSE ){
				
				//用户名或密码错误!                                
				return -5;
			
			}else{
					
				//同一用户只能登录一次
				if ( $_G['setting']['global']["sso"]=="on" && $row["last_active"] && time() - $row["last_active"] <= $_G['setting']['global']["interval"] ){
					
					//该用户已经在登录状态!                                        
					return -6;
						
				}else{					
			
					//设置Session
					self :: admin_session( $row );
					
					//time
					$time = time();
					
					//更新用户最后活动信息
					$sql = "UPDATE `sys:admin` SET stat_login=stat_login+1,last_ip='".GetIP()."',last_login='".$time."',last_active=".$time." WHERE id = ".$_G['manager']['id'];
					
					self :: $db -> execute($sql);
					
					//清除最近的登录日志
					self :: delete_event( $_G['manager']['id'], "login" );
					
					//写入日志
					self :: insert_event( "login" ,$time, $time );
					
					return $_G['manager']['id'];
				
				}
			
			}
		}
			
	}
	
	/*
			生成用户会话信息
			$row            数据记录（单条）
	*/
	public static function admin_session( $row ){                
		global $_G;

		//复制用户信息
		$_SESSION['manager'] = $row;
		
		//随机安全验证
		$_SESSION['manager']['authkey'] = self :: authkey();
		
		//赋值全局变量
		$_G['manager'] = $_SESSION['manager'];
		
	}
	
	/*
		更新用户会话信息
		$key       索引键名
		$val       值
	*/
	public static function admin_update( $key, $val ){                
		global $_G;
		
		$_G['manager'][$key] = $_SESSION['manager'][$key] = $val;					
	}
	
	/*
			注册新管理员
			$row            数据记录（单条）
	*/
	public static function admin_insert( $row ){                
		global $_G;

		//用户信息
		$_SESSION['manager']=array();
		
		foreach ($row as $key => $val) {
			$_SESSION['manager'][$key]=$val;
		}	
		
		$_G['manager'] = $_SESSION['manager'];					
	}
	
	/*
			注册新管理员
			$row            数据记录（单条）
	*/
	public static function admin_delete( $row ){                
		global $_G;

		//用户信息
		$_SESSION['manager']=array();
		
		foreach ($row as $key => $val) {
			$_SESSION['manager'][$key]=$val;
		}	
		
		$_G['manager'] = $_SESSION['manager'];
					
	}
	
	/*
			处理用户登出
	*/
	public static function admin_logout(){
		global $_G;
		
		//记录注销事件
		// && self :: $db
		if( $_G['manager']['id'] ){
								
			//写入日志
			self :: insert_event("exit",time(),time());					
		}

		unset( $_SESSION['manager'], $_G['manager'] );                
	}
	
	////////////////////////////////
	
	/*
			在线用户统计
			$gid            用户组
	*/
	public static function admin_online( $gid ){
		global $_G;
		
		$sql="SELECT * FROM `sys:admin` WHERE ( ".time()." - last_active <= ".$_G['project']['heartbeat']." )";                
		$gid && $sql.=" and gid = ".$gid;                
		$sql.=" ORDER BY id desc,state desc";
		
		return self :: $db -> getAll( $sql );
	}
	
	/*
			更改用户头像
			$uid             用户ID
			$avatar        头像地址
	*/
	public static function admin_avatar( $uid, $avatar = NULL ){
		
		if( isset( $avatar ) ){
			return self :: $db -> execute( "UPDATE `sys:admin` SET avatar='".$avatar."' WHERE id = ".$uid );
		}else{
			return self :: $db -> getValue( "SELECT avatar `sys:admin` WHERE id = ".$uid );
		}
			
	}
	
	/*
			更改用户头像
			$uid             用户ID
			$theme        头像地址
	*/
	public static function admin_theme( $uid, $theme ){
	
		//更新数据
		$sql = "UPDATE `sys:admin` SET theme='".$theme."' WHERE id = ".$uid;                
		self :: $db -> execute($sql);
			
	}
	
	/*
		根据权限名称索引符合的用户组
		$func	权限名称
		$join	是否转换成字符串组合
	*/
	public static function get_func_gid( $func, $join = false ){
		global $_CACHE;
		
		$list = array();
		foreach( $_CACHE['system']['group'] as $gid => $row ){
			if( array_key_exists( $func, $row['config'] ) ){
				array_push( $list, $gid );
			}
		}
		
		return $join ? implode( ',', $list ) : $list;
			
	}
	
	/*
		根据权限名称索引符合的用户组
		$gid	用户组ID
		$join	是否转换成字符串组合
	*/
	public static function get_user_gid( $gid, $join = FALSE  ){
		global $_CACHE;
		
		$list = array();
		foreach( $_CACHE['system']['admin'] as $aid => $row ){
			if( in_array( $row['gid'], $gid ) ){
				array_push( $list, $aid );
			}
		}
		
		return $join ? implode( ',', $list ) : $list;
			
	}
	
	////////////////////////////////
	
	/*
	登录状态检查
	$jump	是否登录后转到本页，true 或 false
	*/
	public static function check_login( $jump = TRUE ){
		global $_G;
		
		if(!$_G['manager']['account'] && strpos(strtolower($_SERVER['PHP_SELF']),"admin.login.php")==0){
			header("Location:".VI_BASE."module/system/admin.login.php".($jump?'?jump='.GetCurUrl():''));
		}
	}

	/*
			页面权限
	*/
	public static function check_page(){
		global $_G;
		
		//当前模块	
		$appid = str_replace( VI_BASE.'module/', '', dirname($_SERVER["REQUEST_URI"]) );
		
		if( $appid == "system" && array_key_exists(GetCurFile(),$_G['project']['page']) && !array_key_exists(GetCurFile(),$_G['group']) && !array_key_exists("*",$_G['group']) ){
			header("Location:".VI_BASE."module/system/serve.error.php?action=page&page=".GetCurFile());
			exit();
		}
	}

	/*
		功能权限
		$func		权限名称
		$debug	显示调试信息
	*/
	public static function check_func( $func, $debug = NULL ){
		global $_G;
		
		//调试模式
		if( isset( $debug ) ){			
			if( !array_key_exists( $func, $_G['group'] ) && !array_key_exists("*",$_G['group']) ){	
			
				//显示错误
				if( $debug ){
					return "<div id='state' class='failure'>当前用户组没有 <strong>".$func."</strong> 权限，您可能无法完成本页某些操作</div>";
				}else{
					ob_end_clean();
					header("Location:".VI_BASE."module/system/serve.error.php?action=func&func=".$func);
					exit;	
				}
			}
		}else{
			//返回状态
			return array_key_exists( $func, $_G['group'] );
		}
		
	}
	
	/*
			检查类是否存在
			$class	类名称
	*/
	public static function check_class($class){	
		if( !class_exists($class) ){
			header("Location:".VI_BASE."module/system/serve.error.php?action=class&class=".$class);
			exit;
		}
	}

	/*
			检查变量是否为空
	*/
	public static function check_empty(){
		$num = func_num_args();
		$arg = func_get_args();
		for ($i = 0; $i < $num; $i++) {
			if( empty($arg[$i]) ){
				header("Location:".VI_BASE."module/system/serve.error.php?action=empty&empty=".$i);
				exit;
			}
		}
	}

	/*
			检查目录是否可写
	*/
	public static function check_writable(){
		$num = func_num_args();
		$arg = func_get_args();
		for ($i = 0; $i < $num; $i++) {
			if( is_writable($arg[$i]) === FALSE ){
				return "<div id='state' class='failure'>警告：目录 <strong>".str_replace( VI_ROOT, VI_BASE, $arg[$i] )."</strong> 没有写权限！</div>";
			}
		}
	}
	
	//////////////////////////////////

	/*
		写入一条系统日志
		$event	事件标识
		$start	发生时间
		$last	最后更新时间
		$desc	描述内容
	*/
	public static function insert_event( $event, $start, $last, $desc = "" ){
			
		$sql="INSERT INTO `sys:event`(account,aid,event,dateline,modify,ip,description) values('".$_SESSION['manager']['account']."','".$_SESSION['manager']['id']."','".$event."','".$start."','".$last."','".GetIP()."','".$desc."')";
		
		self :: $db -> execute($sql);
			
	}
	
	/*
		删除过期系统日志
		$uid	用户ID
		$event	事件标识
	*/
	public static function delete_event( $uid, $event ){                
		global $_G;

		if( !$uid ) return;

		//删除日志
		$sql = "SELECT * FROM `sys:event` WHERE event='$event' and aid=".$uid;
		$rows = self :: $db -> query($sql);
		
		if( $rows > $_G['setting']['global']["logsize"]-1 ){
		
			//删除日志_不支持子查询
			$sql = "DELETE FROM `sys:event` WHERE event='$event' and dateline<".(time()-604800)." and aid=".$uid;
	
			self :: $db -> execute($sql);
		}
			
	}
	
	
	//////////////////////////////////
	
	/*
		记录新审核
		$perm		审核权限
		$appid		模块ID
		$func		操作名称
		$summary	内容摘要
		$execute	执行语句
		$original	原始摘要
	*/
	public static function examine_insert( $perm, $appid, $func, $summary, $execute, $original = array() ){
		global $_G;
		global $_CACHE;
		
		//如果有执行权限
		if( array_key_exists( $perm, $_G['group'] ) ){
			
			self :: examine_execute( $appid, $execute, $summary, $original );
			
		}else{
			
			//记录新审核
			$sql="INSERT INTO `sys:examine`(account,aid,appid,func,summary,original,execute,state,dateline,ip) values('".$_G['manager']['account']."','".$_G['manager']['id']."','".$appid."','".$func."','".format_json( fix_json( $summary ) )."','".format_json( fix_json( $original ) )."','".addslashes( $execute )."',-1,'".time()."','".GetIP()."')";
			
			self :: $db -> execute($sql);
			
			//查找拥有审核权限的用户组
			$gid = self :: get_func_gid( $perm );
			
			//查找拥有审核权限的用户组
			$uid = self :: get_user_gid( $gid );
			
			//批量发送审核提醒邮件
			foreach( $_CACHE['system']['admin'] as $aid => $row ){
				if( in_array( $aid, $uid ) && $row['email'] ){
					self :: sendmail( '新审核请求……', $row['email'], '', $row['account'].'<br />$content' );
				}
			}
			
		}

	}
	
	/*
		删除过期系统日志
		$id			审核ID
		$state		状态值
		$remark		备注信息
	*/
	public static function examine_update( $id, $state, $remark = '' ){                
		global $_G;
		global $_CACHE;
		
		//查询记录
		$sql = "SELECT * FROM `sys:examine` WHERE state = -1 and id=".$id;
		$row = self :: $db -> getOne($sql);
		
		if( $row ){
		
			$row['summary'] = fix_json( $row['summary'] );			
			$row['original'] = fix_json( $row['original'] );
			
			/*
	
			//执行查询
			self :: $db -> execute( $row['execute'] );
			
			//如果是新插入数据
			if( preg_match("/INSERT INTO/i", $row['execute'] ) ){
			
				//读取插入ID
				$newid = self :: $db -> getInsertId();
				
				//记录新ID
				$row['summary']['id'] = $newid;
			}
			
			*/
			
			//更新数据
			$sql = "UPDATE `sys:examine` SET remark='".$remark."',state='".$state."',auditor='".$_SESSION['manager']['account']."' WHERE id = ".$id;                
			self :: $db -> execute($sql);
			
			self :: examine_execute( $row['appid'], $row['execute'], $row['summary'], $row['original'] );
			
			/*
						
			//加载模块
			Module :: loader( $row['appid'] );
			
			//调用模块接口
			call_user_func_array( array( $row['appid'], 'examine'), array( $row['summary'], $row['original'] ) );
			
			*/
			
			//审核处理完邮件提醒
			$email = $_CACHE['system']['admin'][$row['aid']]['email'];
			
			if( $email ){
				self :: sendmail( '审核处理完毕……', $email, '', '$content' );
			}
			
		}
			
	}
	
	/*
		删除过期系统日志
		$id			审核ID
		$state		状态值
		$remark		备注信息
	*/
	public static function examine_execute( $appid, $execute, $summary, $original ){                
		global $_G;

		//执行查询
		self :: $db -> execute( $execute );
		
		//如果是新插入数据
		if( preg_match("/INSERT INTO/i", $execute ) ){
		
			//读取插入ID
			$newid = self :: $db -> getInsertId();
			
			//记录新ID
			$summary['id'] = $newid;
		}
					
		//加载模块
		Module :: loader( $appid );
		
		//调用模块接口
		call_user_func_array( array( $appid, 'examine'), array( $summary, $original ) );
			
	}
	
	//////////////////////////////////

	//保存配置
	public static function append_config( $file, $mysql_host, $mysql_port, $mysql_db, $mysql_user, $mysql_password, $mysql_manpre ,$mysql_modpre ){

		$success = false;
		
		if( !$file ) return false;
		
		if($content = file_get_contents($file)) {
			$content = trim($content);
			
			$content = self :: insert_config($content, "/define\('VI_BASE',\s*'.*?'\);/i", "define('VI_BASE', '".url_base()."');");

			$content = self :: insert_config($content, "/define\('VI_HOST',\s*'.*?'\);/i", "define('VI_HOST', '". url_fore() . url_base() ."');");		
			
			$content = self :: insert_config($content, "/define\('VI_START',\s*'.*?'\);/i", "define('VI_START', '".time()."');");
			$content = self :: insert_config($content, "/define\('VI_SECRET',\s*'.*?'\);/i", "define('VI_SECRET', '".rand_string( 16 )."');");
			
			$content = self :: insert_config($content, "/define\('VI_DBHOST',\s*'.*?'\);/i", "define('VI_DBHOST', '".( $mysql_port ? $mysql_host.':'.$mysql_port : $mysql_host )."');");
			$content = self :: insert_config($content, "/define\('VI_DBNAME',\s*'.*?'\);/i", "define('VI_DBNAME', '$mysql_db');");
			$content = self :: insert_config($content, "/define\('VI_DBUSER',\s*'.*?'\);/i", "define('VI_DBUSER', '$mysql_user');");
			$content = self :: insert_config($content, "/define\('VI_DBPASS',\s*'.*?'\);/i", "define('VI_DBPASS', '$mysql_password');");
			
			$content = self :: insert_config($content, "/define\('VI_DBMANPRE',\s*'.*?'\);/i", "define('VI_DBMANPRE', '$mysql_manpre');");
			$content = self :: insert_config($content, "/define\('VI_DBMODPRE',\s*'.*?'\);/i", "define('VI_DBMODPRE', '$mysql_modpre');");

			if(@file_put_contents($file, $content)) {
				$success = true;
			}
		}

		return $success;
	}

	/*
		写入配置
		$s			原始内容
		$find		查找内容
		$replace	替换内容
	*/
	public static function insert_config($s, $find, $replace) {
		if( preg_match($find, $s) ){
			$s = preg_replace($find, $replace, $s);
		} else {
			$s .= "\r\n".$replace;
		}
		return $s;
	}

	//////////////////////////
       
	/*
		验证邮件配置
	*/
	public static function validmail(){
		global $_G;
		
		if( !$_G['setting']['mail']['MAIL_USER'] || !$_G['setting']['mail']['MAIL_PASS'] ){
			return FALSE;
		}else{
			return TRUE;
		}
	
	}
       
	/*
		普通邮件发送函数
		$subject		主题
		$address	收件人地址
		$list			抄送人地址
		$content		邮件主体
	*/
	public static function sendmail( $subject, $address, $list, $content ){
		global $_G;
		
		//验证邮件配置
		if( !self :: validmail() ) return FALSE;
	
		//class
		//require_once VI_ROOT.'config/mail.php';
		require_once VI_ROOT.'source/class/class.phpmailer.php';
		
		//必需项不能为空
		if( !$subject || !$address || !$content ) return FALSE;
		
		//读取邮件模板
		if( !isset( $_G['mail_template'] ) ){
			$_G['mail_template'] = sreadfile( VI_ROOT.'source/dialog/mail.htm' );
		}
		
		$template = $_G['mail_template'];
		
		error_reporting(E_STRICT);
		
		$mail             = new PHPMailer();
		$mail->CharSet	  = $_G['product']['charset'];
		
		$content             = array_var_convert( $template, array( '{TEMPLATE_HEAD}' => $_G['setting']['mail']['MAIL_TEMPLATE_HEAD'], '{TEMPLATE_FOOT}' => $_G['setting']['mail']['MAIL_TEMPLATE_FOOT'], '{TEMPLATE_TIME}' => date('Y年m月d日'), '{TEMPLATE_HOST}' => VI_HOST, '{TEMPLATE_BODY}' => stripslashes($content) ) );
		
		$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->Host       = "smtp.qq.com"; // SMTP server
		//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												   // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = $_G['setting']["mail"]["MAIL_SMTP_HOST"]; // sets the SMTP server
		$mail->Port       = $_G['setting']["mail"]["MAIL_SMTP_PORT"];                    // SET the SMTP port for the GMAIL server
		$mail->Username   = $_G['setting']["mail"]["MAIL_USER"]; // SMTP account username
		$mail->Password   = $_G['setting']["mail"]["MAIL_PASS"];        // SMTP account password
		
		$mail->SetFrom($_G['setting']["mail"]["MAIL_SMTP_FROM"], $_G['setting']["mail"]["MAIL_SMTP_NAME"]);
		
		//$mail->AddReplyTo("name@yourdomain.com","First Last");
		
		$mail->Subject    = $subject;
		
		//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		
		$mail->MsgHTML($content);
		
		//$address = "verywork@gmail.com";
		$mail->AddAddress($address);
		
		if( $list ){
			$array = explode(",",$list);
			foreach( $array as $address ){
				$mail->AddBCC(trim($address));
			}
		}
		
		//$mail->AddAttachment("image/phpmailer.gif");      // attachment
		//$mail->AddAttachment("image/phpmailer_mini.gif"); // attachment
		
		if(!$mail->Send()) {
			return false;
		} else {
			return true;
		}
	}
	
	public static function show_error($errno, $errstr, $errfile, $errline){

		//为了安全起见，不暴露出真实物理路径，下面两行过滤实际路径
		$errfile = str_replace(getcwd(),"",$errfile);
		$errstr = str_replace(getcwd(),"",$errstr);
		
		switch ($errno) {
			case E_USER_ERROR:		
				echo "<div id='error'><b>USER ERROR：</b> <br /><br />\n <em>$errstr</em> \n<br /><br />";
				echo "  Fatal error on line $errline in file $errfile";
				echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
				echo "Aborting...<br />\n</div>";
				exit(1);
			break;
		
			case E_USER_WARNING:
				echo "<div id='error'><b>USER WARNING：</b> <br /><br /> <em>$errstr</em> <br />\n</div>";
				exit(1);
			break;
		
			case E_USER_NOTICE:
				echo "<div id='error'><b>USER NOTICE</b> <br /><br /> <em>$errstr</em> <br />\n</div>";
				exit(1);
			break;
		
			default:
				//echo "Unknown error type: [$errno] $errstr<br />\n";
			break;
		}
		
		/* Don't execute PHP internal error handler */
		return true;
	}
	
	public static function show_notice( $text ){
		echo '<div style="margin:50px; text-align:center; border:#ff8f8f solid 2px; padding:40px; font-size:1.5em; background:#fff3c9; border-radius: 5px;">'. $text .'</div>';
		exit;
	}
	
	/*
		执行系统升级脚本
		$version		系统版本
	*/
	public static function upgrade( $version ){
		global $_G;
		global $_CACHE;
	
		//升级脚本
		$exec = 'bee.'. $version .'.sql';
		
		//执行升级
		$call = Database :: update( $exec );
		
		//////////////
		
		//修正补丁
		$exec = VI_ROOT.'data/fixbug/fix.'. $version .'.php';
		
		//锁文件
		$lock = str_replace( '.php', '.lock', $exec );
		
		//确保脚本只执行一次
		if( file_exists( $exec ) && !file_exists( $lock ) ){
		
			//载入脚本
			require $exec;
			
			//写入锁
			create_file( $lock, date("Y-m-d H:i:s") );
			
		}
		
		//////////////
		
		return $call;
	
	}
	
	/////////////////////////////
	
	//检查系统环境
	public static function check_status(){
		global $_CACHE;
	
		/*
		$funcs = array( 'iconv', 'mcrypt_module_open', 'imageline', 'curl_init', 'gzinflate' );
		
		$class = array( 'DOMDocument' );
		*/
		
		$result = array(
			'content' => array(
				/*
				'function' => array(),
				'class' => array(),
				*/
				'config' => array(),
				'module' => array()
			),
			'stat' => 0
		);
		
		$stat = 0;
		
		//////////////////
		
		/*
		//检测类
		foreach( $class as $item ){
			if( !class_exists( $item ) ){
				array_push( $result['content']['class'], $item );
				$stat++;
			}
		}
		
		//检测函数
		foreach( $funcs as $item ){
			if( !function_exists( $item ) ){
				array_push( $result['content']['function'], $item );
				$stat++;
			}
		}
		*/
		
		//检测模块
		foreach( $_CACHE['system']['module'] as $item => $conf ){
		
			$setfile = 'module/'.$item.'/setting.php';
			$navfile = 'module/'.$item.'/navigate.php';
			$htafile = 'module/'.$item.'/content/.htaccess';
			
			$result['content']['module'][$item] = array();
			
			if( file_exists( VI_ROOT.$setfile ) && !is_writable( VI_ROOT.$setfile ) ){
				array_push( $result['content']['module'][$item], $setfile );
				$stat++;
			}
			
			if( file_exists( VI_ROOT.$navfile ) && !is_writable( VI_ROOT.$navfile ) ){
				array_push( $result['content']['module'][$item], $navfile );
				$stat++;
			}
			
			if( file_exists( VI_ROOT.$htafile ) && !is_writable( VI_ROOT.$htafile ) ){
				array_push( $result['content']['module'][$item], $htafile );
				$stat++;
			}
			
			//处理子目录
			if( !$conf['writable'] ) continue;
			
			foreach( $conf['writable'] as $file ){
				$newfile = 'module/'.$item.'/'.$file;
				if( !file_exists( VI_ROOT.$newfile ) || !is_writable( VI_ROOT.$newfile ) ){
					array_push( $result['content']['module'][$item], $newfile );
					$stat++;
				}
			}
			
		}
		
		//检测配置
		$list = loop_file( VI_ROOT.'config/', array(), array('php') );
		
		foreach( $list as $file ){
			
			$setfile = 'config/'.$file;
			
			if( !is_writable( VI_ROOT.$setfile ) ){
				array_push( $result['content']['config'], $setfile );
				$stat++;
			}
			
		}
		
		//////////////////
		
		/*
		
		foreach( $result as $key => $val ){
			$stat += count( array_values( $val ) );
		}
		*/
		
		$result['stat'] = $stat;
		
		//////////////////
		
		return $result;
		
	}
	
	//检查可安装
	public static function check_install(){
		global $_CACHE;
	
		$result = array(
			'list' => array(),
			'stat' => 0
		);
		
		$stat = 0;
		
		//////////////////
		
		//检测模块
		foreach( $_CACHE['system']['module'] as $item => $conf ){
		
			$insfile = 'module/'.$item.'/install.sql';
			$lckfile = 'module/'.$item.'/install.lock';
			
			if( file_exists( VI_ROOT.$insfile ) && !file_exists( VI_ROOT.$lckfile ) ){
				array_push( $result['list'], $item );
				$stat++;
			}
			
		}
		
		//////////////////
		
		$result['stat'] = $stat;
		
		//////////////////
		
		return $result;
		
	}
	
	//检查系统更新
	public static function check_upgrade(){
		global $_CACHE;
		
		$result = array(
			'list' => array(),
			'stat' => 0
		);
		
		//////////////////
		
		$base = VI_ROOT.'data/update/';
		
		$list = loop_file( $base, array(), array('sql') );

		//读取配置
		foreach( $list as $file ){
		
			//锁文件
			$lock = $base . str_replace( ".sql", ".lock", $file );
            
            $vers = fileparm( $base . $file, 'version' );
            
            //有锁文件
            if( file_exists( $lock ) ){
	            array_push( $result['list'], array( 'name'=>$file, 'version'=>$vers, 'size'=>filesize( $base . $file ),'ctime'=>filemtime( $base . $file ),'lock'=>TRUE, 'mtime'=>filemtime( $lock ) ) );
            }else{
	            array_push( $result['list'], array( 'name'=>$file, 'version'=>$vers, 'size'=>filesize( $base . $file ),'ctime'=>filemtime( $base . $file ),'lock'=>FALSE ) );
	            $result['stat'] += 1;
            }
		
		}
		
		//////////////////
		
		return $result;
		
	}
	
	//检查系统环境
	public static function check_filehash(){
		global $_G, $_CACHE;
		
		//更新基准目录
		$file = VI_ROOT  .'data/filehash/'. $_G['product']['charset'] .'.data';
		
		$text = sreadfile( $file );
        $list = array_filter( explode("\n", $text) );
        
        $result = array( 'stat' => 0, 'lost' => 0, 'newly' => 0, 'file' => array() );
        
        //当前系统模块
        $module = array_keys( $_CACHE['system']['module'] );
        
        //当前文件清单
        $entrys = array();
        
        //没有找到特征
        if( count( $list ) == 0 ) $result['stat'] = -1;
        
        //////////////////////////
    
        foreach( $list as $item ){
        	
        	list( $file, $hash, $size ) = explode("\t", $item);
        	
        	$test = array( 'name' => $file, 'hash' => $hash );
        	
        	//获取模块标识
        	$appid = Module :: get_appid( $file );
        	
        	//模块不存在
        	if( $appid && in_array($appid, $module) === FALSE ) continue;
        	
        	array_push( $entrys, $file );
        	
        	//文件不存在
        	if( file_exists( VI_ROOT.$file ) === FALSE ){
        	
	        	$test['lost'] = TRUE;
	        	
	        	$result['lost'] += 1;
	        	
        	}else{
	        	
	        	if( md5_file( VI_ROOT.$file ) === $hash ) continue;
	        	
	        	$test['size'] = filesize(VI_ROOT.$file);
	        	$test['change'] = filesize(VI_ROOT.$file) - $size;
	        	$test['mtime'] = filemtime(VI_ROOT.$file);
	        	
	        	$result['stat'] += 1;
	        	
        	}
        	
        	array_push( $result['file'], $test );
                
        }
        
        //////////////////////////
        
        //获取文件清单
		$list = rglob( VI_ROOT.'{*.php,*.js,*.htm,*.xml,*.sql,*.css}', GLOB_BRACE, 
    				array(
	    				VI_ROOT.'_doc',
	    				VI_ROOT.'_dir',
	    				VI_ROOT.'_src',
	    				VI_ROOT.'_tmp',
	    				VI_ROOT.'cache',
	    				VI_ROOT.'attach',
	    				//	VI_ROOT.'static',
	    				VI_ROOT.'module/special/content/data',
	    				VI_ROOT.'module/special/content/html',
	    				VI_ROOT.'module/special/content/article'
    				)
	    		 );
	    
	    //修正 Win 各种路径
	    $base = str_replace( '\\', '/', VI_ROOT );
	    
	    //获取不在清单中文件
	    foreach( $list as $file ){
	    	$name = str_replace( array('\\',$base), array('/',''), $file );
	    	if( in_array( $name, $entrys) ) continue;
    		array_push( $result['file'], array( 'name'=> $name, 'size'=>filesize( $file ), 'mtime'=>filemtime( $file ), 'newly' => TRUE ) );
    		$result['stat'] += 1;
    		$result['newly'] += 1;
		}
        
        //////////////////////////
        
        return $result;
	
	}
        
}

?>