<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("系统设置 - VeryIDE");
?>

	<?php
		
	require_once(VI_ROOT."source/class/setting.php");
	
	//模块ID
	$appid = getgpc('appid');
	
	//配置地址
	$self = VI_ROOT.'module/'.$appid.'/config.php';
	
	//加载导航
	include('include/naver.setting.php');
	
	if( $appid && file_exists( $self ) && require( $self ) ){
	
		//模块配置
		$app = $_G['module'][$appid];
		
		//权限名称
		$func = 'system-'. $app['model'] .'-set';
	
		//检查权限
		System :: check_func( $func, FALSE );
		
		$_GalSet = new Setting( VI_ROOT.'module/'.$appid.'/setting.php' , VI_ROOT.'module/'.$appid.'/setting.xml' , $appid );
		
		if( $_GET["action"] == "update" && !empty($_POST) ){
			
			if( $_GalSet->save('POST') ){
				
				//连接数据库
				System :: connect();
					
				//写入日志
				System :: insert_event( $func, time(), time() );
				
				//关闭数据库
				System :: connect();
				
				//更新模块缓存
				Module :: search();
				
				echo '<div id="state">成功修改模块配置，新配置将立即生效</div>';
				
			}else{
				
				echo "<div id='state' class='failure'>保存模块配置失败！请检查 ./module/".$appid."/setting.php 是否有读写权限</div>";
				
			}
			
		}elseif( $_GalSet -> writable() == FALSE ){
		
			echo "<div id='state' class='failure'>请检查 ./module/".$appid."/setting.php 是否有读写权限</div>";
			
		}elseif( file_exists( VI_ROOT.'module/'.$appid.'/content/.htaccess' ) && $_GalSet -> writable( VI_ROOT.'module/'.$appid.'/content/.htaccess' ) == FALSE ){
		
			echo "<div id='state' class='failure'>请检查 ./module/".$appid."/content/.htaccess 是否有读写权限</div>";
			
		}
		
		$form = $_GalSet->transform();
		
		?>
	
		<form method="post" action="?appid=<?php echo $appid;?>&action=update" autocomplete="off" data-mode="edit" data-valid="true">

		<table cellpadding="0" cellspacing="0" class="form">

		<?php

		echo $form;		

		?>
		<tr>
			<td></td>
			<td>
				<button type="submit" name="" class="submit">保存设置</button>
			</td>				
		</tr>

		</table>

		</form>
		
		<?php
		
	}else{
	
		echo "<div id='state' class='failure'>模块不存在或已删除！请检查 module/".$appid."/ 目录是否有效</div>";
	
	}
	
    ?>



<?php html_close();?>