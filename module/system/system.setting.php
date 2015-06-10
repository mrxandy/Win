<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("系统设置 - VeryIDE");
?>
    
	<?php
	
	require_once VI_ROOT.'source/class/setting.php';

	//检查权限
	$func = 'system-system-set';
	System :: check_func( $func, FALSE );
	
	$do = getgpc('do');
	
	if( !$do ){
		ob_end_clean();
		header("location:?do=global");
		exit;
	}
	
	$active = array('global','attach','mail');
	
	if( in_array( $do , $active ) ){
		
		$_GalSet = new Setting(VI_ROOT.'config/'.$do.'.php' , VI_ROOT.'config/'.$do.'.xml' , $do);
		
		if( $_GET["action"]=="update" && !empty($_POST) ){
		
			//var_dump($_POST);
			//exit;
		
			if( $_GalSet->save('POST') ){		
				
				//连接数据库
				System :: connect();
					
				//写入日志
				System :: insert_event( $func, time(), time() );
				
				//关闭数据库
				System :: connect();
				
				//更新模块缓存
				Module :: search();
				
				echo '<div id="state">成功修改系统配置：'.$do.'</div>';
				
			}else{
				
				echo "<div id='state' class='failure'>保存系统配置失败！请检查 ./config/".$do.".php 是否有读写权限</div>";
				
			}
			
		}elseif( $_GalSet -> writable() == FALSE ){
			echo "<div id='state' class='failure'>请检查 ./config/".$do.".php 是否有读写权限</div>";	
		}
		
		$form = $_GalSet->transform();
		
	}
	
    ?>
	
	<form method="post" action="?do=<?php echo $do;?>&action=update" autocomplete="off" data-mode="edit" data-valid="true">
    
    <table cellpadding="0" cellspacing="0" class="form">
    
	<?php

	echo $form;
	
    ?>
    <tr>
        <td></td>
        <td>
            <button type="submit" name="" class="submit">保存配置</button>
        </td>				
    </tr>
        
	</table>
	  
	</form>

<?php html_close();?>