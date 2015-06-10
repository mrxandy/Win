<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("模块管理 - VeryIDE");
?>


	<?php
    
	//连接数据库
	System :: connect();
	
	$action = getgpc('action');
	$module = getgpc("module");
	
	if($action){
	
		switch($action){
			
			//安装模块
			case "install":
		
				//检查权限
				$func = 'system-module-add';
				System :: check_func( $func, FALSE );
				
				$res = Module :: install( $module );
				
				switch( $res ){					
					case 'permission':
						echo '<div id="state" class="failure">抱歉！当前目录没有写权限（module/'.$module.'）</div>';
					break;
					
					case 'locked':
						echo '<div id="state" class="failure">抱歉！当前模块已安装（'.$module.'）</div>';
					break;
					
					case 'script':
						echo '<div id="state" class="failure">抱歉！未找到安装脚本（'.$module.'）</div>';
					break;
					
					case 'abort':
						echo '<div id="state" class="failure">抱歉！安装模块（'.$module.'）失败，以下是本错误信息详细报告：</div>';
					break;
					
					case 'success':
					
						//写入日志
						System :: insert_event( $func, time(),time(), "安装模块成功：".$module );
						
						echo '<div id="state">恭喜！成功安装模块（'.$module.'）</div>';
						
					break;					
				}
				
			break;
			
			//卸载模块
			case "uninstall":
		
				//检查权限
				$func = 'system-module-del';
				System :: check_func( $func, FALSE );
				
				$res = Module :: uninstall( $module );
				
				switch( $res ){					
					case 'locked':
						echo '<div id="state" class="failure">抱歉！当前模块已卸载（'.$module.'）</div>';
					break;
					
					case 'script':
						echo '<div id="state" class="failure">抱歉！未找到卸载脚本（'.$module.'）</div>';
					break;
					
					case 'abort':
						echo '<div id="state" class="failure">抱歉！卸载模块（'.$module.'）失败，以下是本错误信息详细报告：</div>';
					break;
					
					case 'success':
					
						//写入日志
						System :: insert_event( $func,time(),time(),"卸载模块成功：".$module );
					
						echo '<div id="state">恭喜！成功卸载模块（'.$module.'）</div>';
						
					break;					
				}
				
			break;			
			
			//禁用模块
			case "disabled":
		
				//检查权限
				$func = 'system-module-dis';
				System :: check_func( $func, FALSE );
				
				Module :: set_state( $module, FALSE );				
				
			break;
			
			//启用模块
			case "enabled":
		
				//检查权限
				$func = 'system-module-ena';
				System :: check_func( $func, FALSE );
				
				Module :: set_state( $module, TRUE );
				
			break;
			
			//搜索模块
			case "search":				
				Module :: search();
			break;
			
		}
	
		//关闭数据库
		System :: connect();
		
		//加载模块缓存
		Module :: get_list();
		
		if( $action != "install" && $action != "uninstall" ){		
			echo '<div id="state">正在使用模块管理工具，您刚才的操作已经成功执行</div>';
			echo '<script type="text/javascript">parent.Serv && parent.Serv.Addons.module();</script>';
		}

	}

    ?>

    <?php
    
    $i = 1;
	$d = 0;
	$html = '';

    //遍历模块
    foreach( $_CACHE['system']['module'] as $appid => $app ){
		
		//当前模块目录
		$base = VI_ROOT . 'module/'. $appid .'/';
		
		$html .= '<li'.( $i % 2 == 0 ? ' class="zebra"' : '' ).'>';
		
		$html .= '
			<div class="icon">
				<img src="'.VI_BASE.'module/'.$appid.'/icon.png" />
			</div>';
			
		$html .= '<div class="name">
				<strong>'.$app["name"].'</strong> <br />
				
				'.$app["describe"].'
			</div>';
                    
		$html .= '<div class="site">
				'.( $app["version"] ? '<span>'.number_format($app["version"],1).'</span>' : '' ).'
				<br />
				<a href="'.$app['support'].'" target="_blank">'.$app["author"].'</a>
			</div>';
			
		$html .= '<div class="plus">';
		
			if( file_exists($base.'/install.sql') && !file_exists($base.'/install.lock') ){
				
				$html .= '<button type="button" onclick="location.href=\'?action=install&module='.$app["appid"].'\';">安装</button>';
				
			}elseif( file_exists($base.'/uninstall.sql') && !file_exists($base.'/uninstall.lock') ){
				
				$html .= '<button type="button" class="added" onclick="if(confirm(\'确定要卸载此模块吗？\n\n同时删除相关数据库\')){location.href=\'?action=uninstall&module='.$app["appid"].'\';}">卸载</button>';
				
			}elseif( file_exists($base.'/install.lock') ){
				
				$html .= '<button type="button" class="added">已安装</button>';
				
			}
	
		$html .= '</div>';
			
		$html .= '<div class="plus">';
		
			if( $app['model'] == "module" ){
			
				//已禁用
				if( $app["state"] === FALSE ){
					$html .= '<button type="button" onclick="location.href=\'?action=enabled&module='.$app["appid"].'#appid-'.$app["appid"].'\';">启用</button>';
					$d++;
				}else{
					$html .= '<button type="button" class="added" onclick="location.href=\'?action=disabled&module='.$app["appid"].'#appid-'.$app["appid"].'\';">禁用</button>';
				}
			
			}
				
		$html .= '</div>';
			
		$html .= '</li>';
		
		$i++;
		
    }

    ?>
	
	<div id="saving">
    	<span class="action">
        	<button type="button" class="button" onclick="location.href='?action=search'">搜索模块</button>
        	<a class="shared" href="http://www.veryide.com/market.php" target="_blank">购买模块</a>
        </span>
		共找到模块 <?php echo count($_CACHE['system']['module']);?> 个
        <?php
        if( $d ){
			echo '，'.$d.' 个已禁用';
		}
		?> 
        
	</div>
    
    <ul id="module">
		<?php
		if( count( $_CACHE['system']['module'] ) == 0 ){
			echo '<p> 未发现模块，请使用搜索工具 </p>';
		}else{
			echo $html;
		}
		if( count( $_CACHE['system']['module'] ) == 1 ){
			echo '<p><a href="http://www.veryide.com/market.php" target="_blank">去官网购买更多模块 &raquo;</a></p>';
		}
		?>
		
    </ul>



<?php html_close();?>