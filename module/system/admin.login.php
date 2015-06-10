<?php
/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/
require '../../source/dialog/loader.php';
html_start("用户登录 VeryIDE");

/////////////////////////////////

$jump = getgpc('jump');
$action = getgpc('action');

//Cookie名称
$cookie = 'admin';

switch ($action){
	
	//登录
	case "login":
				
		//连接数据库
		System :: connect();
		
		$username = getgpc("username");
		$password = getgpc('password');
		
		$result = System :: admin_login( $username, $password );
		
		/*
		if( $Global["captcha"] == "on" && ( !$_COOKIE["VCODE"] || $_POST["captcha"] != $_COOKIE["VCODE"] ) ){
	
			$Project["message"]	="验证码输入错误!";
			$Project["jump"]	="";
			$Project["class"]	="info";			

		}
		*/
		
		//清空验证码
		$_SESSION['captcha'] = '';
		
		switch( $result ){
			
			case -1:			
				$_G['project']['message']	="登录信息不能为空!";
				$_G['project']['jump']	="";
				$_G['project']['class']	="info";			
			break;
						
			case -2:			
				$_G['project']['message']	="验证码输入错误!";
				$_G['project']['jump']	="";
				$_G['project']['class']	="info";			
			break;
						
			case -3:			
				$_G['project']['message']	="密码或随机验证输入错误!";
				$_G['project']['jump']	="";
				$_G['project']['class']	="info";			
			break;
						
			case -4:			
				$_G['project']['message']	="当前IP不在授权的范围!";
				$_G['project']['jump']	="";
				$_G['project']['class']	="wrong";
			break;
						
			case -5:			
				$_G['project']['message']	="用户名或密码错误!";
				$_G['project']['jump']	="";
				$_G['project']['class']	="wrong";			
			break;
						
			case -6:			
				$_G['project']['message']	="该用户已经在登录状态!";
				$_G['project']['jump']	="";
				$_G['project']['class']	="wrong";			
			break;
			
			default:
			
				//写入Cookie
				if( $_G['setting']['global']["remember"] == "on" ){
					setcookie($cookie,urlencode($_G['manager']['account']),time()+60*60*24*30,VI_BASE);
				}else{
					setcookie($cookie,'',0,VI_BASE);
				}
				
				//重载页面
				echo '<script type="text/javascript">';
				if( $jump ){
					echo 'parent.location.replace("'.VI_BASE.'api.php?action=launch&command=launch&content='. $jump .'");';
				}else{
					echo 'parent.location.reload();';
				}
				echo '</script>';
				exit;
				
			break;			
			
		}

		//关闭数据库
		System :: connect();
		
	break;

	//未登录
	default:
	
		$username = urldecode($_COOKIE[$cookie]);
		
	break;
}

$_SESSION['RNDCODE'] = rand_string(16);			

echo loader_script(array(VI_BASE."static/js/mo.hash.js"),'utf-8',$_G['product']['version']);

?>
<script type="text/javascript">

Mo.reader(function(){
	
	//绑定表单事件
	Mo("#wrapper form").bind( 'submit', function( index, e ){
	
		Mo.Event( e ).stop();

		var result = Mo.ValidForm( this, function(x){
			Mo.Message("wrong",'<div class="s"></div><div class="c">'+x+'</div><div class="e"></div>', 3, { "unique" : "message", "center" : true });
		});
		
		//将密码加密
		if( result ){
			Mo('#password').value( Mo.md5( Mo('#password').value() ) );
			Mo( "input[type=submit]", this ).disabled();
			Mo.Message("info",'<div class="s"></div><div class="c">正在验证，请稍后…</div><div class="e"></div>', 10, { "unique" : "message", "center" : true });
			this.submit();
		}

	});

});

////////////////////////////////

function showCode(){
	
	var user = Mo("#username").value();
	var pass = Mo("#password").value();
	
	if( "<?php echo $_G['setting']['global']['captcha']['show'];?>" == 'off' ) return false;
	
	if( user && pass ){
	
		Mo("#captcha_box").show();
		
		if( !Mo("#Getcaptcha").attr("src") ) Mo("#Getcaptcha").attr( {"src":"<?php echo VI_BASE;?>api.php?action=captcha&rand="+Math.random()} );
	
	}
}

function rndCode(o){
	o.src=o.src+'&rand='+Math.random();
	Mo('#captcha').value('').focus();
}

Mo.reader(function(){	
	if( Mo("#username").value() ){
		Mo("#password").focus();
	}else{
		Mo("#username").focus();
	}
	
	Mo('#password').bind( 'keypress' , detectCapsLock );
});

</script>

    
	<?php                
	//错误信息
	if ( $_G['project']['message'] ){				
		echo '<div id="notices">'.$_G['project']['message'].'</div>';				
		$_G['project']['message'] = '';
	}else{
		echo '<p>&nbsp;</p>';	
	}
	?>
    
    <div id="licence">
        <p><a href="http://www.veryide.com/" target="_blank"><img src="<?php echo VI_BASE;?>static/image/model/<?php echo $_G['licence']['type'];?>.gif" alt="<?php echo $_G['version'][$_G['licence']['type']]['name'];?>" /></a></p>
        <p class="text-key">VeryIDE <?php echo $_G['version'][$_G['licence']['type']]['name'];?></p>
        <p><?php echo $_G['product']['powered'];?></p>
    </div>
    
    <form action="?" method="post" id="logincp">
		<div class="entry">
		
			<p><input name="username" id="username" type="text" size="20" tabindex="1" maxlength="20" class="text login" value="<?php echo $username;?>" onchange="showCode();" placeholder="用户账号" data-valid-name="用户账号" data-valid-empty="yes" /></p>
			
			<p>
			<input name="password" id="password" type="password" size="22" tabindex="2" maxlength="32" class="text login" value="<?php echo "";?>" onchange="showCode();" placeholder="用户密码" onFocus="showCode();" onkeyup="showCode();" onkeydown="showCode();" data-valid-name="用户密码" data-valid-empty="yes" />
			</p>
			
			<?php			
			if( $_G['setting']['global']['captcha']['show'] == "on" ){			
			?>
		
			<p id="captcha_box" style="display:none;">
				<input name="captcha" type="text" id="captcha" class="text login captcha" size="50" maxlength="4" tabindex="3" autocomplete="off" placeholder="验证字符" data-valid-name="验证字符" data-valid-empty="yes" />
				<br />
				<img id="Getcaptcha" onclick="rndCode(this);" style="cursor:pointer;" alt="点击切换验证码" />
			</p>
			
			<?php
			}else{
				echo '<p><img src="'.VI_BASE.'static/image/icon/lock.png" align="absmiddle" />  已使用安全方式登录</p>';
			}
			?>
			
		</div>
		
		<div class="enter">
			<p>
			<input type="hidden" name="action" id="action" value="login" />
			<input type="hidden" name="jump" id="jump" value="<?php echo rawurlencode($jump);?>" />
			<input type="hidden" name="rndcode" value="<?php echo $_SESSION['RNDCODE'];?>" />
			<button type="submit" name="Submit">登录</button>
			</p>			
		</div>

    </form>

	</div>
	
<?php html_close();?>