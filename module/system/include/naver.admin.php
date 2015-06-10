<?php
	if( !is_array($_G['project']) ){
		ob_clean();
		exit("Forbidden");	
	}
?>
  
	<!--div id="nav"><strong>用户中心</strong></div-->
	<ul id="naver">
		<!--li><a href="avatar.php" data-hash="true">头像设置</a></li>
		<li><a href="custom.php" data-hash="true">偏好设置</a></li-->
		<li><a href="admin.event.php" data-hash="true">日志查询</a></li>
		<li><a href="group.power.php" data-hash="true">我的权限</a></li>
		<li><a href="group.list.php" rel="group.edit.php" data-hash="true">用户组</a></li>
		<li><a href="admin.list.php" rel="admin.edit.php" data-hash="true">用户管理</a></li>
	</ul>
	
	