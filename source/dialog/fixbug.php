<?php
/*
		程序修正脚本
*/

if(!defined('VI_BASE')) {
	exit('Access Denied');
}

///////////////////////////////////

//安装步骤
$step = getnum("step",1);

//////////////////////////

$base = VI_ROOT."data/fixbug/";

$list = loop_file( $base, array(), array("php") );

$fixfile = '';

foreach( $list as $file ){	
	if( !file_exists( str_replace(".php",".lock",$base.$file) ) ){		
		$fixfile = $base.$file;		
		break;		
	}	
}

if( $fixfile ){
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
	            <a href="http://www.veryide.com/guide.php" target="_blank"><img src="static/image/icon/help.png" /> 帮助</a>
	            <a href="http://www.veryide.com/forum.php" target="_blank"><img src="static/image/icon/talk.png" /> 论坛</a>
	        </span>        
	        更新向导
	    </dt>
	    <dd>    
		<?php
		include( $fixfile );
		?>        
	    </dd>        
	</dl>
	<!--安装界面_开始-->

</div>
<?php

	exit;
}
?>