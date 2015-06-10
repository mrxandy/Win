<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("用户编辑 - VeryIDE");
?>


	<?php
	Cached :: loader("system","table.group");
	
	//loader
	require('include/naver.admin.php');
	
	//菜单处理
	$action = getgpc('action');
	$jump = getgpc('jump');
	
	//下一步
	$next=true;
	
	if(!$jump) $jump="admin.list.php";
	
	$people = getgpc("people");
	
	if($people=="me"){
		$id = $_G['manager']['id'];
	}else{
		$id = getnum('id',0);
	}
	
	if($action){
		$account = trim( getgpc('name') );
		$password = trim( getgpc('password') );
		$confirm = getgpc('confirm');
		$avatar = getgpc('avatar');
		$gid = getnum('gid',0);
		$state = getnum("state",1);
		$gender = getnum('gender',0);
		$email = getgpc('email');
		$phone = getgpc('phone');
		$blog = getgpc('blog');    
		$qq = getgpc('qq');
		$birthday = getgpc('birthday');
		
		$extra = $_POST['extra'];
		$extra = format_json( fix_json( $extra ) );
	}	
	
	//连接数据库
	System :: connect();

	//动作处理
	switch ($action){
		
		case "add":
		
			//检查权限
			$func = 'system-admin-add';
			System :: check_func( $func, FALSE );				
				
			//查询数据
			$sql="SELECT * FROM `sys:admin` WHERE account='".$account."'";
			$row = System :: $db -> getOne( $sql );

			if( $row ){
				
				$_G['project']['message']="已经存在同名用户!";
				
			}else{
				
				$salt = substr(uniqid(rand()), -6);
			
				//插入数据
				$sql="INSERT INTO `sys:admin`(account,password,salt,state,gid,dateline,blog,avatar,email,ip,qq,phone,gender,birthday,extra,config) VALUES('".$account."','".md5( md5($password) . $salt )."','".$salt."','".$state."','".$gid."',".time().",'".$blog."','".$avatar."','".$email."','".GetIP()."','".$qq."','".$phone."',".$gender.",'".$birthday."','".$extra."','{}')";
				
				System :: $db -> execute( $sql );
				
				$id = System :: $db -> getInsertId();
				
				///////////////////////////
				
				//复制快捷方式
				$module = unserialize( $_CACHE['system']['group'][$gid]['module'] );
				
				if( $module && is_array( $module ) ){
				
					foreach( $module as $q ){	
						//插入数据
						$sql = "INSERT INTO `sys:quick`(aid,appid,dateline) VALUES(".$id.",'".$q."',".time().")";	
	
						System :: $db -> execute( $sql );	
					}
					
				}
				
				//复制小工具
				$widget = unserialize( $_CACHE['system']['group'][$gid]['widget'] );

				if( $widget && is_array( $widget ) ){
					foreach( $widget as $appid => $a ){

						foreach( $a as $w ){
	
							//插入数据
							$sql = "INSERT INTO `sys:widget`(aid,appid,widget,x,y,z,fx,fy,dateline) VALUES(".$id.",'".$appid."','".$w."','','',0,0,0,".time().")";	
	
							System :: $db -> execute( $sql );
	
						}
	
					}
				}
				
				//////////////////////////////////////
					
				//写入日志
				System :: insert_event($func,time(),time(),"创建用户：".$account."，用户：".$_CACHE['system']['group'][$gid]["name"]);				
				
				//缓存系统用户
				Cached :: table( 'system', 'sys:admin', array( 'jsonde' => array('config','extra') ) );
				
				System :: redirect("admin.list.php","成功添加新用户!");
				
			}
		
		break;
		
		case "update":
		
			//检查权限			
			if( $id == $_G['manager']['id'] ){
				$func = 'system-account-mod';
				System :: check_func( $func, FALSE );
			}else{
				$func = 'system-admin-mod';
				System :: check_func( $func, FALSE );
			}			
				
			//更新数据
			$sql="UPDATE `sys:admin` SET `state`='".$state."',blog='".$blog."',avatar='".$avatar."',email='".$email."',qq='".$qq."',phone='".$phone."',gender='".$gender."',birthday='".$birthday."',extra='".$extra."',`modify`='".time()."' WHERE id=".$id;			
			
			System :: $db -> execute( $sql );
			
			//修改密码
			if( $password && $password == $confirm ){
				
				//检查权限			
				if( $id == $_G['manager']['id'] ){
					$func = 'system-account-pwd';
					System :: check_func( $func, FALSE );
				}else{
					$func = 'system-admin-pwd';
					System :: check_func( $func, FALSE );
				}
				
				$sql="UPDATE `sys:admin` SET password = md5( concat( '".md5( $password )."', `salt` ) ) WHERE id=".$id;			
				
				System :: $db -> execute( $sql );
					
				//写入日志
				System :: insert_event($func,time(),time(),"修改用户密码：".$_CACHE['system']['admin'][$id]["name"]);				
			}
			
			//更改权限
			if( $gid ){
				
				$func = 'system-account-gid';
				System :: check_func( $func, FALSE );
				
				$sql="UPDATE `sys:admin` SET gid='".$gid."' WHERE id=".$id;			
				
				System :: $db -> execute( $sql );
					
				//写入日志
				System :: insert_event($func,time(),time(),"变更用户组：".$_CACHE['system']['admin'][$id]["name"]);				
			}
			
			$_G['manager']['id'] == $id && System :: admin_update( 'avatar', $avatar );
					
			//写入日志
			System :: insert_event($func,time(),time(),"修改用户资料：".$_CACHE['system']['admin'][$id]["name"]);				
				
			//缓存系统用户
			Cached :: table( 'system', 'sys:admin', array( 'jsonde' => array('config','extra') ) );
				
			System :: redirect(($jump?$jump:"?id=".$id."&action=edit"),"成功修改用户信息!");
		
		break;
		
		case "edit":
		
			$sql="SELECT * FROM `sys:admin` WHERE id=".$id;
			$row = System :: $db -> getOne( $sql );
			$extra = fix_json( $row['extra'] );
		
		break;
		
	}
	
	//关闭数据库
	System :: connect();
	
	/*
	安全问题
	<select name="question" onchange="showcustomquest(this.value)" style="width:124px">
		<option value="0">无安全问题</option>
		<option value="1">我爸爸的出生地</option>
		<option value="2">我妈妈的出生地</option>
		<option value="3">我的小学校名</option>
		<option value="4">我的中学校名</option>
		<option value="5">我最喜欢的运动</option>
		<option value="6">我最喜欢的歌曲</option>
		<option value="7">我最喜欢的电影</option>
		<option value="8" >我最喜欢的颜色</option>
		<option value="-1">自定义问题</option>
	</select>
	*/	

	?>

	<?php
	
	//显示权限状态
	switch($action){
		case "":
			echo System :: check_func( 'system-admin-add',true);
		break;
		
		case "edit":
			if( $id == $_G['manager']['id'] ){
				echo System :: check_func( 'system-account-mod',true);				
			}else{
				echo System :: check_func( 'system-admin-mod',true);				
			}			
		break;
	}
	
	?>

	
    
	<div id="box">
		<form action="?" method="post" data-mode="edit" data-valid="true">
                
        <table cellpadding="0" cellspacing="0" class="form">
            
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>
                
            <tr>
                <th>用户名称：</th>
                <td><input name="name" type="text" class="text" id="name" value="<?php echo $row['account'];?>" size="35" data-valid-name="用户名称" data-valid-empty="yes" <?php echo ($action?'disabled="true"':'');?> /></td>
            </tr>
        
			<?php
            
			if( $action=="add" || $action=="" || ($id == $_G['manager']['id'] && System :: check_func( 'system-account-pwd')) || ($id != $_G['manager']['id'] && System :: check_func( 'system-admin-pwd'  )) ){
			echo '
	
			<tr>
				<th>用户密码：</th>
				<td>
					<input name="password" type="password" value="" size="35" maxlength="15"  data-valid-empty="'. ( !$action ? 'yes' : 'no' ) .'" data-valid-name="用户密码" data-valid-confirm="input[name=confirm]" onkeyup="Mo.Password( this.value, function( value, level, pos ){ Mo(\'#level\').attr( {\'class\' : \'l\'+ pos} ) }); ">
					
					<div id="level"></div>
				</td>
			</tr>
		
			<tr>
				<th>确认密码：</th>
				<td><input name="confirm" type="password" value="" size="35" maxlength="15" /> <span>同上</span></td>
			</tr>
			';
			}
            
            ?>
        
        	<tr>
            	<th>电子邮箱：</th>
                <td><input name="email" type="text" class="text" value="<?php echo $row["email"];?>" size="35" data-valid-name="电子邮箱" data-valid-empty="yes" />
                用来接收邮件通知
                </td>
            </tr>
            
			<?php
            if( System :: check_func( 'system-account-gid' ) ){
            ?>
        	<tr>
            	<th>用户分组：</th>
                <td>
                <?php
				foreach($_CACHE['system']['group'] as $key => $value){
					echo '<label><input type="radio" class="radio" name="gid" value="'.$key.'" '.($key==$row["gid"]?'checked="checked"':'').' data-valid-name="用户级别" data-valid-empty="yes" /><img src="'.VI_BASE.'static/image/medal/mini_'.$value["medal"].'" />'.$value["name"].'</label>';
				}			
				?>
                </td>
            </tr>            
            <?php
            }
            ?>
             
        	<tr>
            	<th>帐号状态：</th>
                <td>		
                    <label>
                    <input type="radio" class="radio" name="state" value="0">
                    禁用</label>
                    <label>
                    <input type="radio" class="radio" name="state" value="1" checked> 
                    正常
                    </label>
                </td>
            </tr>
            
            <tr><td colspan="2" class="section"><strong>扩展信息</strong></td></tr>
        
            <tr>
                <th>用户头像：</th>
                <td>
                <script type="text/javascript">
                new Serv.Upload("avatar","<?php echo $row["avatar"] ? $row["avatar"] : VI_BASE.'static/image/face.jpg';?>",{'format':['<?php echo implode("','",$_G['upload']['image']);?>'],'again':true,'recovery':true,'input':true,'thumb':[50,50], 'crop' : [100,100] });
                </script>
                </td>
            </tr>
       
        	<tr>
            	<th>我的生日：</th>
                <td><input name="birthday" id="birthday" type="text" class="text date" value="<?php echo $row["birthday"];?>" size="35" readonly="true" title="年-月-日">
                </td>
            </tr>
        
        	<tr>
            	<th>我的性别：</th>
                <td>
                <label><input type="radio" class="radio" name="gender" value="0" data-valid-name="用户性别" data-valid-empty="yes" /><img src="<?php echo VI_BASE;?>static/image/icon/female.png" /> 女士</label>
                <label><input type="radio" class="radio" name="gender" value="1" data-valid-name="用户性别" data-valid-empty="yes" /><img src="<?php echo VI_BASE;?>static/image/icon/male.png" /> 男士</label>
                </td>
            </tr>
                
        	<tr>
            	<th>QQ/MSN：</th>
                <td><input name="qq" type="text" class="text" value="<?php echo $row["qq"];?>" size="35"></td>
            </tr>
        
        	<tr>
            	<th>电话号码：</th>
                <td><input name="phone" type="text" class="text" value="<?php echo $row["phone"];?>" size="35"></td>
            </tr>
        
        	<tr>
            	<th>个人主页：</th>
                <td><input name="blog" type="text" class="text" value="<?php echo $row["blog"];?>" size="35"></td>
            </tr>
             
            <tr><td colspan="2" class="section"><strong>关联微博</strong></td></tr>
            
            <tr>
                <th>新浪微博：</th>
                <td>
                    <input type="text" size="35" class="text" name="extra[sina]" value="<?php echo $extra["sina"];?>" />
                    新浪微博地址链接
                </td>
            </tr>
            
            <tr>
                <th>腾讯微博：</th>
                <td>
                    <input type="text" size="35" class="text" name="extra[tenc]" value="<?php echo $extra["tenc"];?>" />
                    腾讯微博地址链接
                </td>
            </tr>        
             
            <tr><td colspan="2" class="section"><strong>关联论坛</strong></td></tr>
            
            <tr>
                <th>UID：</th>
                <td>
                    <input type="text" size="35" class="text" name="extra[uid]" value="<?php echo $extra["uid"];?>" />
                    数字 ID
                </td>
            </tr>
            
            <tr>
                <th>用户名：</th>
                <td>
                    <input type="text" size="35" class="text" name="extra[username]" value="<?php echo $extra["username"];?>" />
                    登录论坛的用户名
                </td>
            </tr>        
        
        	<tr>
            	<td></td>
                <td>
                <?php
				
				if($action=="edit" || $action=="update" ){
					echo '
					<button type="submit" name="Submit" class="submit" '.($disabled?'disabled="true"':'').'>修改用户</button>
					<input type="hidden" name="action" id="action" value="update" />
					<input type="hidden" name="id" id="id" value="'.$id.'" />
					';
				}else{
					echo '
					<button type="submit" name="Submit" class="submit" '.($disabled?'disabled="true"':'').'>添加用户</button>
					<input type="hidden" name="action" id="action" value="add" />
					';
				}
				echo '<input type="hidden" name="jump" id="jump" value="'.$_GET["jump"].'" />';
				
				?>
                </td>
            </tr>
       
        </table>
        
		<script type="text/javascript">
			Mo("input[name=gender]").value("<?php echo $row["gender"];?>");
			Mo("input[name=state]").value("<?php echo isset($row['state']) ? $row['state'] : 1;?>");
		</script>

		</form>
	</div>


<?php html_close();?>