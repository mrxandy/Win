<?php
/*
		软件安装脚本
*/

if(!defined('VI_BASE')) {
	exit('Access Denied');
}

///////////////////////////////////

//配置文件
$conf = 'config/config.php';

//安装步骤
$step = getnum("step",1);

?>

<script type="text/javascript">
	
	//暂停运行，显示安装界面
	Serv.END = true;

</script>

<div id="greet">

	<!--安装界面_开始-->
	<dl id="setup">
	    <dt>
	    
	        <span>                	
	            <a href="http://www.veryide.com/guide.php" target="_blank"><img src="image/icon/help.png" /> 帮助</a>
	            <a href="http://www.veryide.com/forum.php" target="_blank"><img src="image/icon/talk.png" /> 论坛</a>
	        </span>
	        
	        安装向导
	    </dt>
	    <dd>
	    
	        <sup><?php echo $step;?>/5</sup>
	        
	        <?php
		
			//安装数组
			$install=array(
				"system" => array( "sql" => VI_ROOT."data/install/system.sql" , "lock" => VI_ROOT."cache/sys.system.lock"),
				"module" => array( "sql" => VI_ROOT."data/install/module.sql" , "lock" => VI_ROOT."cache/sys.module.lock")
			);
		
			//锁文件
			$lock1 = $install["system"]["lock"];
			$lock2 = $install["module"]["lock"];
			
			/////////////////////////////////
			
	        if( $step == 1 ){
			?>
	    
	        <p><strong>土豪，我们做朋友吧！</strong></p>
	        
	        <p>
	            准备安装 <em><?php echo $_G['project']['product'];?></em>
	        </p>
	        
	        <p>
	            需要服务器支持 <span>PHP 5</span> 以上（当前为 <span>PHP <?php echo PHP_VERSION;?></span>）和 <span>MySQL 4.1</span> 以上版本
	        </p>
	        <p>安装前请确保对相关目录以及数据库有操作权限！</p>
	        <p>第一次安装请保证数据库连接各项为空，请检查：<strong><?php echo $conf;?></strong></p>
	        
	        <p> <button type="button" onclick="location.replace('?step=2');">验　证</button> </p>
	        
	        <?php
			}
			
			/////////////////////////////////
			
	        if( $step == 2 ){	
				
				//清除缓存
				clearstatcache();
		
				//读配置测试
				if( is_readable( VI_ROOT.$conf ) == FALSE ){
				?>
				
					<p><strong>读写验证</strong></p>        
					<p>读文件出错，请检查文件是否有读取权限：<strong><?php echo $conf;?></strong></p>        
					<p> <button type="button" onclick="location.reload();">再试试</button> </p>
				
				<?php
				
				//写配置测试
				}elseif( is_writable( VI_ROOT.$conf ) == FALSE ){
								
				?>
					
					<p><strong>读写验证</strong></p>
					<p>写文件出错，请检查文件是否有读取权限：<strong><?php echo $conf;?></strong></p>        
					<p> <button type="button" onclick="location.replace('?step=2');">再试试</button> </p>
					
				<?php                        
				}else{
				?>
					
					<p><strong>通过验证</strong></p>
					<p>文件读写操作验证测试通过，可以继续下一步</p>        
					<p> <button type="button" onclick="location.replace('?step=3');">下一步</button> </p>
	
				<?php                        
		
				}
	        
	        ?>
	        
	        <?php
	        }
	        
	        /////////////////////////////////
	        
	        if( $step == 3 ){
	        
				//未安装成功
				//if( !VI_DBMANPRE || !VI_DBMODPRE ){
	        ?>
	        
	            <form action="?step=4" method="post">
	    
		            <p> <strong>数据库信息：</strong> </p>
		    
		            <p> 服务器：<input type="text" name="mysql_host" value="<?php echo ($mysql_host?$mysql_host:'localhost');?>" /> 端　口：<input type="text" name="mysql_port" value="<?php echo ($mysql_port?$mysql_port:'3306');?>" class="short" /></p>
		            <p> 数据库：<input type="text" name="mysql_db" value="<?php echo ($mysql_db?$mysql_db:'');?>" /> <span>需已存在的数据库</span></p>
		            <p> 用户名：<input type="text" name="mysql_user" value="<?php echo ($mysql_user?$mysql_user:'root');?>" /> </p>
		            <p> 密　码：<input type="text" name="mysql_password" value="<?php echo ($mysql_password?$mysql_password:'');?>" /> </p>
		    
		            <p> <strong>数据表前辍：</strong> </p>
		    
		            <p> 系统表：<input type="text" name="mysql_manpre" value="<?php echo ($mysql_manpre?$mysql_manpre:'system_');?>" /> </p>
		            <p> 模块表：<input type="text" name="mysql_modpre" value="<?php echo ($mysql_modpre?$mysql_modpre:'module_');?>" /> </p>
		    
		            <p> <strong>超级管理员：</strong> </p>
		    
		            <p> 用户名：<input type="text" name="mysql_admin" value="<?php echo ($mysql_admin?$mysql_admin:'admin');?>" /> <span>推荐使用真实姓名</span></p>
		            <p> 密　码：<input type="text" name="mysql_pass" value="<?php echo ($mysql_pass?$mysql_pass:'veryide');?>" /> </p>
		    
		            <p> <button type="submit">下一步</button> </p>
	    
	            </form>
	
	            <?php
	            //}
	            ?>
	        
	        <?php
	        }
	        
	        /////////////////////////////////
	
	        if( $step == 4 ){
	        
				$mysql_host		= trim(getgpc("mysql_host"));
				$mysql_port 	= getnum("mysql_port",0);
				$mysql_db		= trim(getgpc("mysql_db"));
				$mysql_user		= trim(getgpc("mysql_user"));
				$mysql_password	= trim(getgpc("mysql_password"));
	
				$mysql_manpre	= trim(getgpc("mysql_manpre"));
				$mysql_modpre	= trim(getgpc("mysql_modpre"));
	
				$mysql_admin	= trim(getgpc("mysql_admin"));
				$mysql_pass		= trim(getgpc("mysql_pass"));
	
				if(VI_DBMANPRE) $mysql_manpre = VI_DBMANPRE;
				if(VI_DBMODPRE) $mysql_modpre = VI_DBMODPRE;
	
				if( $mysql_host ){
					$mysql = @mysql_connect($mysql_host.':'.$mysql_port, $mysql_user, $mysql_password);
				}
				
				/////////////////////////////////////
	
				if ( !$mysql ) {
					
				?>
	            
		            <p><strong>安装出错</strong></p>
		            <p>与数据库服务器连接失败，请检查连接信息</p>
		            <p> <button type="button" onclick="location.replace('?step=3');">上一步</button> </p>			
					
	            <?php			
				
				}else{
					
				
					//数据库连接测试
					$dbcharset = str_replace('-', '', ( VI_DBCHARSET ? VI_DBCHARSET : $_G['product']['charset'] ) );
					
					//mysql open
					System :: $db = new mysql_db( $mysql_host.':'.$mysql_port, $mysql_user, $mysql_password, $mysql_db );
					System :: $db -> setCharset( $dbcharset );
					System :: $db -> connection();
					System :: $connect_id = System :: $db ->_db_connect_id;				
					
					//安装错误
					if( System :: $db -> _errno ){
						
					?>
	                
	                <p><strong>安装出错</strong></p>
	                <p>不能正常连接数据库 <strong><?php echo $mysql_db;?></strong>，请确认是否存在或拥有相关权限 </p>
	                <p> <button type="button" onclick="location.replace('?step=3');">上一步</button> </p>	
	                
	                <?php
					
					}else{
						
						//遇到错误
						$error = false;
						
						//循环安装_开始
						foreach($install as $name=>$config){
						
							//sql
							$sqlfile = $config["sql"];
							
							//找到安装脚本
							if( file_exists($sqlfile) ){
							
								//锁文件
								$lock = $config["lock"];
								
								if( file_exists($lock) ){
									
									//出错了
									$error = true;
									
									?>
									
									<p><strong>安装出错</strong></p>
									<p>安装服务已锁定，请手动删除安装锁：<strong><?php echo str_replace( VI_ROOT,'', $lock );?></strong></p>
									<p> <button type="button" onclick="location.reload();">再试试</button> </p>	                            
									
									<?php
									
								}else{
								
									//连接数据库
									$sql = file_get_contents($sqlfile);
									
									//前置表前辍
									if( $name == 'system' ){
										$sql = str_replace("{TableAdmin}",$mysql_admin,$sql);
										$sql = str_replace("{TablePass}",md5($mysql_pass),$sql);
									}
									
									$sql = str_replace("{TableSysPre}",$mysql_manpre,$sql);
									$sql = str_replace("{TableModPre}",$mysql_modpre,$sql);
									
									$sql = str_replace("{TableBase}",url_base(),$sql);				
								
									$array = Database :: query( $sql, $dbcharset );
									
									if( !$array["error"] ){
										
										//写入锁
										create_file( $lock ,date("Y-m-d H:i:s"));
									
									}else{
										
										//安装出错
										foreach( $array["fail"] as $arr ){								
											
											//出错了
											$error = true;
											
											?>
	                                        
	                                        <p><?php echo $arr['query'].'<br />'.$arr['error'];?></p>
	                                        
	                                        <?php
											
										}
										
										//安装成功
										foreach( $array["succ"] as $arr ){								
											$result .= '<h4>'.$arr['message'].'</h4><p class="note">'.$arr['query'].'</p>';								
										}
										
									}
								}
							}else{
								
								?>
	                            
	                            <p><strong>安装出错</strong></p>
	                            <p>未找到安装脚本：<strong><?php echo $sqlfile;?></strong></p>
	                            <p> <button type="button" onclick="location.reload();">再试试</button> </p>	                            
	                            
	                            <?php
								
								
							}
							
							if($install[count($install)][0]!=$name){
								$result .= '<hr />';
							}
						}
						//循环安装_结束
						
						////////////////////////////////////
						
						//没有错误
						if( $error == false ){
					
							//保存配置
							if( !VI_DBMANPRE && !VI_DBMODPRE ){
								System :: append_config( VI_ROOT.$conf, $mysql_host , $mysql_port, $mysql_db, $mysql_user, $mysql_password ,$mysql_manpre ,$mysql_modpre );
							}
							
							//缓存系统模块
							Module :: search();
							
							//缓存系统用户
							//Cached :: table( 'system',$mysql_manpre."admin", array( 'jsonde' => array('config','weibo') ) );
					
							//缓存系统用户组
							//Cached :: table( 'system',$mysql_manpre."group", array( 'jsonde' => array('config') ) );
							
							?>
							
							<p><strong>安装成功</strong></p>
							<p>成功安装 VeryIDE，欢迎围观我们的官网：<a href="http://www.veryide.com/" target="_blank">www.veryide.com</a></p>
							
							<p><strong>推荐模块</strong></p>
							<p>现在，你可以直接在官网进行模块购买，并且自助安装与升级</p>
	        
				        	<script type="text/html" id="segment_recommend">
				        		<ol>
						        <% for( var index in recommend ){ %>
								<li>
									<a href="http://www.veryide.com/market.php?action=show&appid=<%=recommend[index].appid%>" target="_blank">
										<img src="<%=recommend[index].icon%>"><%=recommend[index].name%><br /><em><%=recommend[index].price > 0 ? '￥' + recommend[index].price : '免费'%></em>
									</a>
								</li>
								<% } %>
								</ol>
							</script>
					        
					        <script src="<?php echo $_G['project']['home'];?>api.php?action=service&execute=recommend&callback=Serv.Cloud.recommend">/*获取推荐模块*/</script>
					        <script src="<?php echo $_G['project']['home'];?>api.php?action=service&execute=install&appname=<?php echo $_G['product']['appname'];?>&version=<?php echo $_G['product']['version'];?>&domain=<?php echo url_host();?>&licence=<?php echo $_G['licence']['type'];?>&host=<?php echo urlencode(url_fore().url_base());?>">/*发送安装命令*/</script>
					        
							<p> <button type="button" onclick="location.replace('./');">立即登入</button> </p>
	
							<?php
						
						}
					
					}
					
				}
	        
	        ?>
	
	        
	        <?php
	        }
	        ?>
	        
	    </dd>        
	</dl>
	<!--安装界面_开始-->

</div>
