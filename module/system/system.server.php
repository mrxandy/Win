<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("服务器信息 - VeryIDE");
?>
    
	<?php
    
	//phpinof()
    if( $_GET['action']=="phpinfo" ){
	
		ob_clean();	
		phpinfo();
		exit();
		
    }
        
    //服务器变量_开始
    if (get_cfg_var("register_globals")=="1"){
        $server_env="ON";
    }else{
        $server_env="OFF";
    }

    if( function_exists("zend_version") ){
        $zendversion="<span class='yes'>".zend_version()."</span>";
    }else{
        $zendversion="<span class='no'>不支持</span>";
    }
    
    
    //连接数据库
	System :: connect();
    ?>
    
    <div class="item">系统基本信息</div>
    
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
        <tr class="thead">
          <td width="90">参数</td>
          <td>信息</td>
          <td width="90">参数</td>
          <td>信息</td>
        </tr>
        <tr class="line">
        	<td>VeryIDE 版本</td>
			<td class="text-yes"><?php echo $_G['product']['version'].' - '.strtoupper($_G['product']['charset']).' - '.$_G['product']['build']." <span class='text-key'>（".$_G['version'][$_G['licence']['type']]["name"].'）</span>';?></td>
			<td>安装时间</td>
			<td class="text-yes"><?php echo date("Y-m-d",VI_START);?></td>
        </tr>
        <tr class="band">
        	<td>空间占用</td>
			<td class="text-yes"><?php echo sizecount(foldersize( VI_ROOT ));?></td>
			<td>基准目录</td>
			<td class="text-yes"><?php echo VI_BASE;?></td>
        </tr>
        <tr class="line">
        	<td>绝对地址</td>
			<td class="text-yes"><?php echo VI_HOST;?></td>
			<td>本地目录</td>
			<td class="text-yes"><?php echo VI_ROOT;?></td>
        </tr>
        <tr class="band">
        	<td>UCS 编码</td>
			<td class="text-yes"><?php echo VI_UCS;?></td>
			<td>数据库编码</td>
			<td class="text-yes"><?php echo VI_DBCHARSET;?></td>
        </tr>
    </table>
    
     <div class="item"><a class="y" href="?action=phpinfo" target="_blank">详细信息 <?php echo loader_image("link.gif","详细信息");?></a>当前环境信息</div>
    
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
        <tr class="thead">
          <td width="90">参数</td>
          <td>信息</td>
          <td width="90">参数</td>
          <td>信息</td>
          <td width="90">参数</td>
          <td>信息</td>
        </tr>
        
        <tr class="line">
	        <td>MySQL 版本</td>
	        <td class="text-yes"><?php echo mysql_get_server_info();?></td>
	        <td>Zend 版本</td>
	        <td class="text-yes"><?php echo $zendversion;?></td>
	        <td>PHP 版本</td>
	        <td class="text-yes"><?php echo phpversion();?></td>
        </tr>
        
        <tr class="band">
	        <td>语言环境</td>
	        <td class="text-yes"><?php echo getenv('HTTP_ACCEPT_LANGUAGE');?></td>
	        <td>脚本超时时间</td>
	        <td class="text-yes"><?php echo ini_get("max_execution_time");?> 秒</td>
	        <td>文件上传</td>
	        <td class="text-<?php echo ini_get('file_uploads') ? 'yes' : 'no';?>">
		        <?php
		        if( ini_get('file_uploads') ){
		            $max_size = ini_get('upload_max_filesize');
		            $server_upload_status = "允许/最大 $max_size";
		
		        } else {
		            $server_upload_status = "不允许上传附件";
		        }
		        echo $server_upload_status;
		        ?>
	        </td>
        </tr>
        
        <tr class="line">
        	<td>客户端IP地址</td>
	        <td class="text-yes"><?php echo GetIP();?></td>
	        <td>服务器IP地址</td>
	        <td class="text-yes"><?php echo $_SERVER["SERVER_ADDR"].":".$_SERVER["SERVER_PORT"];?></td>
	        <td>服务器时间</td>
	        <td class="text-yes"><?php echo date("Y-m-d H:i:s");?></td>
        </tr>
        
        <tr class="band">
			<td>运行环境</td>
			<td class="text-yes"><?php echo $_SERVER["SERVER_SOFTWARE"];?></td>
			<td>安全模式</td>
			<td class="text-yes"><?php echo get_cfg_var("safemode") ? '是' : '否';?></td>
			<td>允许远程URL</td>
			<td class="text-<?php echo ini_get("allow_url_fopen") ? 'yes' : 'no';?>"><?php echo ini_get('allow_url_fopen') ? '开启' : '禁用';?></td>
        </tr>
        
        <tr class="line">
          <td>被禁用函数</td>
          <td colspan="5" class="text-<?php echo ini_get("disable_functions") ? 'yes' : 'no';?>"><?php echo ini_get("disable_functions") ? ini_get("disable_functions") : '无';?></td>
        </tr>
        
        <tr class="band">
          <td>操作系统</td>
          <td colspan="5" class="text-yes"><?php echo php_uname();?></td>
        </tr>
        
        
    </table>
    
    <?php
    
    $result = System :: check_status();
    
    $matrix = array(
    	/*
    	'function' => array( 'name'=>'PHP扩展函数', 'desc' => '没有安装', 'stat' => 0, 'warn' => '请安装以上 PHP 扩展', 'list' => array( 
    		'mcrypt_module_open' => 'mcrypt', 'imageline' => 'GD', 'curl_init' => 'CURL', 'gzinflate' => 'Zlib'
    		)
    	 ),
		'class' => array( 'name'=>'PHP扩展类库', 'desc' => '没有安装', 'stat' => 0, 'warn' => '请安装以上 PHP 扩展', 'list' => array( 
    		'DOMDocument' => 'XML'
    		)
    	 ),
    	 */
		'config' => array( 'name'=>'系统配置文件读写权限', 'desc' => '不能读写', 'stat' => 0, 'warn' => '请将以上文件设置为可读写' ),
		'module' => array( 'name'=>'模块配置文件读写权限', 'desc' => '不能读写', 'stat' => 0, 'warn' => '请将以上文件设置为可读写' )
    );
    
    $number = 1;
    
    foreach( $result['content'] as $item => $content ){
	    
	    echo '<div class="item">'. $matrix[$item]['name'] .'</div>';
	    
	    echo '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">';
	    
	    echo '<tr class="thead"> <td width="20">#</td> <td>@</td> <td width="60">'. $matrix[$item]['desc'] .'</td> </tr>';
		    
	    foreach( $content as $cate => $object ){
	    
	    	//模块等二级数组
	    	if( is_array( $object ) ){
	    	
	    		//echo '<tr> <td colspan="3" class="title">'. $cate .'</td> </tr>';
		    	
		    	foreach( $object as $sub ){
			    	
			    	echo '<tr class="'.zebra( $i, array( "line" , "band" ) ).'">';
					
					echo '<td>'.$number.'</td>';
					
					echo '<td>'.$sub.'</td>';
					
					echo '<td align="center"><img src="'.VI_BASE.'static/image/invalid.gif" title="不支持" alt="不支持" class=""  /></td>';
					
					echo '</tr>';
					
					$number ++;
					$matrix[$item]['stat'] ++;
					
		    	}
		    	
		    	
	    	}else{
	    	
	    		if( isset( $matrix[$item]['list'] ) && array_key_exists($object, $matrix[$item]['list']) ){
		    		$alias = $matrix[$item]['list'][$object];
	    		}else{
		    		$alias = $object;
	    		}
		    	
		    	echo '<tr class="'.zebra( $i, array( "line" , "band" ) ).'">';
			
				echo '<td>'.$number.'</td>';
				
				echo '<td>'.$alias.'</td>';
				
				echo '<td align="center"><img src="'.VI_BASE.'static/image/invalid.gif" title="不支持" alt="不支持" class=""  /></td>';
				
				echo '</tr>';
				
				$number ++;
				$matrix[$item]['stat'] ++;
		    	
	    	}
		    
	    }
	    
	    if( $matrix[$item]['stat'] == 0 ){
	    
		    echo '<tr> <td colspan="3" class="notice">本类均已设置正常</td> </tr>';
		    
	    }else{
	    
		    echo '<tr> <td colspan="3" class="choice">'. $matrix[$item]['warn'] .'</td> </tr>';
		    
	    }
	    
	    echo '</table>';
	    
    }
    
    ?>

<?php html_close();?>