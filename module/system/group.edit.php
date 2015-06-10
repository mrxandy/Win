<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("用户组编辑 - VeryIDE");
?>


	<?php
	//loader
	require('include/naver.admin.php');
	
	//菜单处理
	$action = getgpc('action');
	$jump = getgpc('jump');
	
	//下一步
	$next=true;
	
	$gid = getnum('id',0);
	$name = getgpc('name');
	$state = getnum('state',0);
	$medal = getgpc("medal");
	$description = getgpc('description');
	$parentid = getgpc('parentid');
	
	if( $action ){

		//权限配置
		$config = $_POST['config'];
		$config = format_json( fix_json( $config ) );
		
		//快捷方式
		$module = $_POST['module'];
		$module = serialize( $module );
		
		//小工具
		$widget = $_POST['widget'];
		$widget = serialize( $widget );
		
	}
		
	//连接数据库
	System :: connect();
	
	//动作处理
	switch ($action){
		case "add":
		
			//检查权限
			$func = 'system-group-add';
			System :: check_func( $func, FALSE );
			$sql="INSERT INTO `sys:group`(name,aid,account,state,description,dateline,config,module,widget,medal,parentid) VALUES('".$name."',".$_G['manager']['id'].",'".$_G['manager']['account']."',".$state.",'".$description."',".time().",'".$config."','".$module."','".$widget."','".$medal."','".$parentid."')";
			System :: $db -> execute( $sql );
			
			$id = System :: $db -> getInsertId();
					
			//写入日志
			System :: insert_event($func,time(),time(),"新增用户组：".$name);				
			
			//缓存系统用户组
			Cached :: table( 'system', 'sys:group', array( 'jsonde' => array('config'), 'serialize' => array('module','widget') ) );
			
			System :: redirect("group.list.php","<b>消息:</b> 成功添加用户组!");
		
		break;
		
		case "update":
		
			//检查权限
			$func = 'system-group-mod';
			System :: check_func( $func, FALSE );				
		
			//更新数据
			$sql="UPDATE `sys:group` SET name='".$name."',`state`='".$state."',description='".$description."',`modify`='".time()."',config='".$config."',module='".$module."',widget='".$widget."',medal='".$medal."',parentid='".$parentid."' WHERE id=".$gid;
			
			System :: $db -> execute( $sql );
			
			//缓存系统用户组
			Cached :: table( 'system', 'sys:group', array( 'jsonde' => array('config'), 'serialize' => array('module','widget') ) );
					
			//写入日志
			System :: insert_event($func,time(),time(),"修改用户组：".$name);				
			
			//重载权限
			$_SESSION["GroupLife"] = 0;
			
			//$_G['project']['message']="<b>消息:</b> 成功修改用户!";
			System :: redirect("group.list.php","成功修改用户组!");
		
		break;
		
		case "edit";

			$sql="SELECT * FROM `sys:group` WHERE id=".$gid;
			$row = System :: $db -> getOne( $sql );

			if( $row ){

				//权限配置
				$config = fix_json( $row['config'] );
				
				//快捷方式
				$module = unserialize( $row['module'] );
				
				//小工具
				$widget = unserialize( $row['widget'] );
				
			}
			
			//$CFG = $_CACHE['system']['group'][]
			//var_dump($config);
		
		break;
		
	}
	
	/////////////////////////
	
	//关闭数据库
	System :: connect();
	
	?>
    
    <script type="text/javascript">
	
	function doSelect(val){
		Mo('#medal').value(val+".png");
		
		Mo('#group li').attr( { "className" : "" } );
		
		Mo('#'+val).attr( {"className" : 'active'} );
	}
	
	</script>
	
	<div id="box">
		<form action="?" method="post" data-mode="edit" data-valid="true">
                        
            <table cellpadding="0" cellspacing="0" class="form">
            
                <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>
                
				<tr>
					<th>所属分组</th>
					<td>
						<select name="parentid">
							<option value="0">请选择</option>
						<?php
							System :: connect();
							$sql = mysql_query("select * from system_group WHERE parentid=0");
							while($t=mysql_fetch_array($sql)){
						?>
							<option value="<?php echo $t['id'];?>">
								<?php echo $t[name];?>
							</option>
							<?php
								$category = mysql_query("select * from system_group WHERE parentid='".$t['id']."'");
								while($k=mysql_fetch_array($category)){
							?>
								<option value="<?php echo $k['id'];?>">
									 └<?php echo $k['name'];?>
								</option>
							<?php
								}
							?>
						<?php
							}
							System :: connect();
						?>
						</select>
					</td>
				</tr>

                <tr>
                    <th>分组名称：</th>
                    <td>
                    <input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="60" data-valid-name="用户名称" data-valid-empty="yes" />
                    </td>
                </tr>
                
                <tr>
                    <th>分组标识：</th>
                    <td>                    
                    <ul id='group'>
					<?php
					
					//读取图标		    
					$base = VI_ROOT. 'static/image/medal/';
					
					$list = loop_file( $base, array(), array('png') );
					
					foreach( $list as $file ){
					    if( stripos($file,"mini")===false){				
							echo '<li id="'.str_replace(".png","",$file).'" onclick="doSelect(\''.str_replace(".png","",$file).'\');" class="active"><img src="'.VI_BASE.'static/image/medal/'.$file.'" /></li>';
					
							$icon = $row["medal"] ? $row["medal"] : $file;
					    }
					}
                    
                    ?>
                    </ul>
                    </td>
                </tr>
            
                <tr>
                    <th>后台登录：</th>
                    <td>		
                       <label><input type="radio" class="radio" name="state" value="1" checked> 启用 </label>
                       <label><input type="radio" class="radio" name="state" value="0"> 禁用</label>
                    </td>
                </tr>
                    
                <tr><td colspan="2" class="section"><strong>系统页面</strong></td></tr>
                
                <tr>
                    <td></td>
                    <td id="block-pages">
                    
                    	<ul class="func">
                        
                        <?php
                        
                        foreach($_G['project']['page'] as $key => $value){
                            echo '<li><label><input type="checkbox" class="checkbox" '.($config[$key]=='Y'?'checked="checked"':'').' name="config['.$key.']" value="Y" /> '.$value.'</label></li>';
                        }
                        
                        ?>
                        
                        </ul>
                        
                    </td>
                </tr>
                
                <tr>
                    <td></td>
                    <td>
                        <a href="javascript:Mo('#block-pages input').checked(true);void(0);">全选</a> / <a href="javascript:Mo('#block-pages input').checked(false);void(0);">不选</a> / <a href="javascript:Mo('#block-pages input').checked();void(0);">反选</a>
                    </td>
                </tr>
                    
                <tr><td colspan="2" class="section"><strong>系统权限</strong></td></tr>
                
                <tr>
                    <td></td>
                    <td id="block-sys-permit">
                        
                        <?php
                        $i = 0;
                        foreach( $_CACHE['system']['module']['system']['permit'] as $group => $array){
							
							echo '<ul class="func">';
							
							echo '<li><strong>'.$group.'：</strong></li>';
							
							foreach($array as $key => $value){
							
                            	echo '<li><label><input type="checkbox" class="checkbox" '.($config[$key]=='Y'?'checked="checked"':'').' name="config['.$key.']" value="Y" /> '.$value.'</label></li>';
							
							}
							
							echo '</ul>';
							
                        }
                        
                        ?>
                        
                    </td>
                </tr>
                
                <tr>
                    <td></td>
                    <td>
                        <a href="javascript:Mo('#block-sys-permit input').checked(true);void(0);">全选</a> / <a href="javascript:Mo('#block-sys-permit input').checked(false);void(0);">不选</a> / <a href="javascript:Mo('#block-sys-permit input').checked();void(0);">反选</a>
                    </td>
                </tr>
				
				<tr><td colspan="2" class="section"><strong>模块权限</strong></td></tr>

				<tr>
					<td></td>
					<td id="block-mod-permit">
                
					<?php
	
					//读取配置
					foreach( $_CACHE['system']['module'] as $appid => $app ){
					
						if( $app['model'] != "module" || is_array($app['permit']) === FALSE ) continue;
							
						echo '<ul class="func">';

						echo '<li><strong>'.$app['name'].'：</strong></li>';

						foreach($app['permit'] as $item => $name){

							echo '<li><label><input type="checkbox" class="checkbox" '.($config[$item]=='Y'?'checked="checked"':'').' name="config['.$item.']" value="Y" /> '.$name.'</label></li>';

						}

						echo '</ul>';
						
					}
	
					?>
	
					</td>
				</tr>

				<tr>
					<td></td>
					<td>
						<a href="javascript:Mo('#block-mod-permit input').checked(true);void(0);">全选</a> / <a href="javascript:Mo('#block-mod-permit input').checked(false);void(0);">不选</a> / <a href="javascript:Mo('#block-mod-permit input').checked();void(0);">反选</a>
					</td>
				</tr>
				
				<tr><td colspan="2" class="section"><strong>可用模块</strong></td></tr>

				<tr>
					<td></td>
					<td id="block-module">

					<?php

					//读取配置
					
					echo '<ul class="album">';
					
					$i = 0;

					foreach( $_CACHE['system']['module'] as $appid => $app ){
					
						if( $app['model'] != "module" ) continue;

						echo '<li><img src="'.VI_BASE.'module/'.$appid.'/icon.png" /><br /> '.$app['name'].'<br /><input type="checkbox" class="checkbox" '.( in_array( $appid, $module ) ?'checked="checked"':'').' name="module[]" value="'.$appid.'" /></li>';
						
						if( $i == 8 ){									
							echo '</ul><ul class="album">';
							
							$i = 0;
						}else{
							$i++;
						}
							
					}
					
					echo '</ul>';

					?>

					</td>
				</tr>

				<tr>
					<td></td>
					<td>
						<a href="javascript:Mo('#block-module input').checked(true);void(0);">全选</a> / <a href="javascript:Mo('#block-module input').checked(false);void(0);">不选</a> / <a href="javascript:Mo('#block-module input').checked();void(0);">反选</a>
					</td>
				</tr>
				
				<tr><td colspan="2" class="section"><strong>可用工具</strong></td></tr>

				<tr>
					<td></td>
					<td id="block-widget">

					<?php

					//读取配置
					foreach( $_CACHE['system']['module'] as $appid => $val ){

						//插件目录
						$root = VI_ROOT."module/".$appid."/widget/";

						if( file_exists($root) ){

							/////////////////////
							
							$app = $_CACHE['system']['module'][$appid];

							echo '<strong class="item">'.$app["name"].'</strong>';	

							echo '<ul class="album">';

							//遍历目录
							$list = loop_dir( $root );
				
							foreach( $list as $file ){
							
								$doc = $root.$file."/config.xml";
										
								//如果配置文件存在
								if( file_exists($doc) ){
									
									$config = xml_array( sreadfile($doc) );
									
									//UTF8 转 GBK
									if( $_G['product']['charset'] == "gbk" ){								
										foreach ($config['widget'] as $key => $val){
											$config['widget'][$key] = $val ? iconv('UTF-8', 'GBK//IGNORE', $val) : $val;
										}							
									}

									//
									echo '<li rel="'.$file.'">';
	
									echo '<img src="'.VI_BASE.'module/'.$appid.'/widget/'.$file.'/preview.png" /> <br /> '.$config['widget']["name"].' <br /> ';
									
									echo '<input type="checkbox" class="checkbox" '.(in_array( $file, $widget[$appid] ) ?'checked="checked"':'').' name="widget['.$appid.'][]" value="'.$file.'" />';
	
									echo '</li>';
	
									$i++;
								
								}
							
							}

							echo '</ul>';
						
						}
							
					}

					?>

					</td>
				</tr>

				<tr>
					<td></td>
					<td>
						<a href="javascript:Mo('#block-widget input').checked(true);void(0);">全选</a> / <a href="javascript:Mo('#block-widget input').checked(false);void(0);">不选</a> / <a href="javascript:Mo('#block-widget input').checked();void(0);">反选</a>
					</td>
				</tr>
        
                <tr>
                    <td></td>
                    <td>
                    <?php
                    
                    if($action=="edit" || $action=="update" ){
                        echo '
                        <button type="submit" name="Submit" class="submit">修改分组</button>
                        <input type="hidden" name="action" id="action" value="update" />
                        <input type="hidden" name="id" id="id" value="'.$row['id'].'" />
                        ';
                    }else{
                        echo '
                        <button type="submit" name="Submit" class="submit">添加分组</button>
                        <input type="hidden" name="action" id="action" value="add" />
                        ';
                    }
                    echo '<input type="hidden" name="jump" id="jump" value="'.$_GET["jump"].'" />';
                    
                    ?>
                    <input type="hidden" name="medal" id="medal" value="" />
                    </td>
                </tr>
            
            </table>
            
            <script type="text/javascript">
                Mo("input[name=state]").value("<?php echo isset($row['state']) ? $row['state'] : 1;?>");
				doSelect('<?php echo str_replace(".png","",$icon);?>');
            </script>

		</form>
	</div>


<?php html_close();?>