<?php
/*
		目录读写测试
*/

if(!defined('VI_BASE')) {
	exit('Access Denied');
}

///////////////////////////////////

//清除缓存
clearstatcache();

//错误信息
$notice = array( 'class' => array(), 'function' => array(), 'directory' => array() );

//检测类
foreach( $_G['project']['checkin']['class'] as $func => $item ){
	if( !class_exists( $func ) ){
		array_push( $notice['class'], $item );
		$statis++;
	}
}

//检测函数
foreach( $_G['project']['checkin']['function'] as $func => $item ){
	if( !function_exists( $func ) ){
		array_push( $notice['function'], $item );
		$statis++;
	}
}
	
//目录测试_开始
foreach( $_G['project']['checkin']['directory'] as $file ){
	if( !file_exists( VI_ROOT.$file ) || !is_writable( VI_ROOT.$file ) ){
		array_push( $notice['directory'], $file );
		$statis++;
	}
}

?>

<?php

//有错误消息
if( $statis > 0 ){

?>

<script type="text/javascript">
	
	//暂停运行，显示测试界面
	Serv.END = true;

</script>

<div id="greet">

	<!--调试界面_开始-->
	<dl id="setup">
	    <dt>
	        <span>                	
	            <a href="http://www.veryide.com/guide.php" target="_blank"><img src="image/icon/help.png" /> 帮助</a>
	            <a href="http://www.veryide.com/forum.php" target="_blank"><img src="image/icon/talk.png" /> 论坛</a>
	        </span>
	        
	        调试工具
	    </dt>
	    <dd>
	        
	        <?php
	        if( $notice['class'] ){
	        ?>
		        <p><strong>PHP 扩展组件缺失</strong></p>
		        
		        <ul>
		        <?php
				foreach( $notice['class'] as $item ){
					echo '<li>'.$item.'<em>如果不存在，请手动创建</em></li>';		
				}
				?>
				</ul>
				<p>请启用或安装以上 PHP 扩展，<a href="http://www.veryide.com/guide.php?appid=system&id=4" target="_blank">参考手册 &raquo;</a></p>
			<?php
			}
			?>
	        
	        <?php
	        if( $notice['function'] ){
	        ?>
		        <p><strong>PHP 扩展组件缺失</strong></p>
		        
		        <ul>
		        <?php
				foreach( $notice['function'] as $item ){
					echo '<li>'.$item.'</li>';		
				}
				?>
				</ul>
				<p>请启用或安装以上 PHP 扩展，<a href="http://www.veryide.com/guide.php?appid=system&id=4" target="_blank">参考手册 &raquo;</a></p>
			<?php
			}
			?>
	        
	        <?php
	        if( $notice['directory'] ){
	        ?>
		        <p><strong>文件读写权限缺失</strong></p>
		        
		        <ul>
		        <?php
				foreach( $notice['directory'] as $item ){
					echo '<li>'.$item.'</li>';		
				}
				?>
				</ul>
				<p>请创建或更改以上文件权限，<a href="http://www.veryide.com/guide.php?appid=system&id=3" target="_blank">参考手册 &raquo;</a></p>
			<?php
			}
			?>
	
	        <p> <button type="button" onclick="location.reload();">再试试</button> </p>
	        
	    </dd>        
	</dl>
	<!--调试界面_开始-->

</div>

<?php

	exit;
		
}else{
	
	//加载扩展模板
	if( $_G['manager']['id'] && System :: check_func( 'system-module-set' ) && GetIP() != '127.0.0.1' ){
		require VI_ROOT.'source/dialog/extend.php';
	}
	
}

?>