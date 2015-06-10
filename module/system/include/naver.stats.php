<?php
	if( !is_array($_G['project']) ){
		ob_clean();
		exit("Forbidden");	
	}
?>
  
	<!--div id="nav"><strong>欢迎使用</strong></div-->
	<ul id="naver">
        <li><a href="data.attach.php" data-hash="true">文件管理</a></li>
		<li><a href="data.stats.php" data-hash="true">统计信息</a></li>
		<li><a href="data.tables.php" data-hash="true">数据结构</a></li>
	</ul>
	
	