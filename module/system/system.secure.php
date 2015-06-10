<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("安全中心 - VeryIDE");
?>

	<?php
    
    $action = getgpc('action');
    
    $log = VI_ROOT."cache/system/files.secure.php";
    
    $action = getgpc('action');

    switch($action){
    
        case "clean":
        
        	//连接数据库
			System :: connect();
        
            $file = getgpc('file');
			
			//检查权限
			$func = 'system-cache';
			System :: check_func( $func, FALSE );
			
			///////////////////
			
			if( $file == 'static' ){
					
				$list = rglob( VI_ROOT.'cache/compile/{*.htm.php}', GLOB_BRACE );
				
				foreach( $list as $item ){
					unlink( $item );
				}
				
				$_G['project']['message']="成功清除模板缓存：".$file;
				
			}else{
				
				if( in_array( $file, array("rss","sql") ) ){
				
					$cache = VI_ROOT.'cache/'.$file."/";
					
					//开始时间
					$start = time();
					
					//删除并重建目录
					if( delete_dir($cache) && create_dir($cache) ){
					
						System :: insert_event($func,$start,time(),'清除系统查询缓存'.$ext);
	
						$_G['project']['message']="成功清除系统缓存：".$file;
						
					}else{
					
						System :: insert_event($func,$start,time(),'清除系统查询缓存<span class="text-no">失败</span>'.$ext);
	
						$_G['project']['message']="清除统缓存失败：".$file;
						
					}
				
				}
				
			}
			
			
			///////////////////
			
			
			
			//关闭数据库
			System :: connect();
            
        break;
        
        case '':
        	
        	if( file_exists($log) ){
        	
        		$time = filemtime($log);
	        	
	        	//大于一周时提醒
	        	if( time() - $time > 604800 ){
		        	echo '<div id="state" class="failure"><span class="text-no">注意：上一次扫描在'. format_date( $time ) .'</span></div>';
	        	}else{
		        	echo '<div id="state">上一次扫描是：<span class="text-yes">'.date("Y-m-d H:i:s",$time).'</span></div>';
	        	}
	        	
        	}else{
	        	
	        	echo '<div id="state" class="failure"><span class="text-no">注意：从未进行安全扫描</span></div>';
	        	
        	}
        
        break;
        
    }
    
    ?>

    <?php
    
		// 以下地址不视为外链

		$hostname = $_SERVER['HTTP_HOST'];
		$hostname = substr($hostname, in_array(substr($hostname, 0, 4), array('www.', 'bbs.')) ? 3 : 0);
		$hostip = preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $hostname);

		$safeurls = array
			(
			0 => '.alipay.com',
			1 => '.taobao.com',
			2 => '.discuz.net',
			3 => '.comsenz.com',
			4 => '.7dps.com',
			5 => '.manyou.com',
			6 => '.qihoo.com',
			7 => '.qq.com',
			8 => '.live.com',
			9 => '.veryide.com',
			// 如果您需要添加其它的安全外链, 请附加在这里
			);
        
        //检查链接
        function scanurl($url) {

            global $hostip, $hostname, $safeurls;
            $hostnamelen = strlen($hostname);

            $url = substr($url, substr($url, 0, 7) == 'http://' ? 7 : 0);
            $url = strpos($url, '/') === false ? $url : substr($url, 0, strpos($url, '/'));

            if($hostip && $url == $hostname) {
                return true;
            } elseif (!$hostip && substr($url, -$hostnamelen) == $hostname) {
                return true;
            }

            foreach($safeurls as $safeurl) {
                if(substr($url, -strlen($safeurl)) == $safeurl || $url == substr($safeurl, 1)) {
                    return true;
                }
            }

            return false;

        }
        
        //扫描文件
        function get_all_files( $path ){
            $list = array();
            foreach( glob( $path . '/*') as $item ){
                if( is_dir( $item ) ){
                 $list = array_merge( $list , get_all_files( $item ) );
                }
                else{
                 $list[] = $item;
                }
            }
            return $list;
        }
        
        function get_all_errors($file,$msg,$txt){
        
            $result = '<tr class="line">';
                $result.= '<td><img src="'.VI_BASE.'static/image/icon/warning.png" /> '. str_replace(VI_ROOT, '', $file) .'</td>';
                $result.= '<td>'.$txt.'</td>';
                $result.= '<td>'.$msg.'</td>';
                $result.= '<td>'.date("Y-m-d H:i:s",filemtime($file)).'</td>';
            $result.= '</tr>';
            
            return $result;
        
        }
        
        //扫描附件
        function scanfiles(){
        
            $list = get_all_files(VI_ROOT.'attach');
            
            $result = '';
            
            foreach($list as $file) {

                $ext = fileext($file, -4);
                
                if(in_array($ext, array('php', 'php3', 'php4', 'asp', 'aspx', 'asa', 'cdx', 'js'))) {
                
                    $result .= get_all_errors($file,'附件文件夹发现可执行的 PHP/ASP 文件, 请检查其安全性!','');
                    
                }
            
            }

            return $result;
        }
        
        // 扫描引擎函数
        function scanwebshell($path) {
        
            $list = get_all_files($path);
            
            $result = '';
            
            foreach($list as $file) {

                $filename = basename($file);
                $filesize = filesize($file);
    
                $suggestion1 = '该文件发现 Webshell, 请立即检查!';
                $suggestion2 = '该文件有非法代码, 请立即检查!';
    
                if(substr($filename, 0, 10) != 'usergroup_' && substr($filename, 0, 6) != 'style_') {
                    $result .= '';
                }
    
                if(!$fp = @fopen($file, 'r')) {
                    //return '读取该文件失败, 无法进行检查.';
                    $result .= get_all_errors($file,'读取该文件失败, 无法进行检查.',$src);
                }
    
                if(!$filedata = fread($fp, $filesize)) {
                    //return '';
                    $result .= '';
                }
    
                if(preg_match('/\$_(post|get)([ \t\n\r]*)\[/i', $filedata,$matches)) {
                    //return $suggestion1;
                    $result .= get_all_errors($file,$suggestion1,$matches[0]);
                } elseif (strpos($filedata, 'define(\'\',\'\');') !== false) {
                    return $suggestion2;
                    $result .= get_all_errors($file,$suggestion2,$src);
                } else {
                    $result .= '';
                }
            
            }
            
            return $result;

        }
        
        //扫描缓存
        function scantemplate($path) {
        
            if(!$html = new DOMDocument()) {
                return '服务器不支持 DOM，无法检查模板文件。';
            }
        
            $list = get_all_files($path);
            
            $result = '';
            
            //print_r($list);
            
            foreach($list as $file) {
            
                //echo $file.'<br />';

                $suggestion1 = '该文件有通过脚本调用外链地址的代码, 请自行检查!';
                $suggestion2 = '该文件有通过框架调用外链地址的代码, 请自行检查!';

                $html = new DOMDocument();
                @$html->loadHTMLFile($file);

                $scripts = $html->getElementsByTagName('script');
                foreach($scripts as $script) {
                    if(($src = $script->getAttribute('src')) && substr($src, 0, 7) == 'http://') {
                        if(!scanurl($src)) {
                            $result .= get_all_errors($file,$suggestion1,$src);
                        }
                    }
                    if($data = $script->nodeValue) {
                        preg_match_all('/(http:\/\/)([^\/\'"]+)/i', $data, $urls);
                        if($urls && is_array($urls)) {
                            foreach($urls[0] as $url) {
                                if(!scanurl($url)) {
                                    //return $suggestion1;
                                    $result .= get_all_errors($file,$suggestion1,$src);
                                }
                            }
                        }
                    }
                }

                $iframes = $html->getElementsByTagName('iframe');
                foreach($iframes as $iframe) {
                    if(($src = $iframe->getAttribute('src')) && substr($src, 0, 7) == 'http://') {
                        if(!scanurl($src)) {
                            //return $suggestion2;
                            $result .= get_all_errors($file,$suggestion2,$src);
                        }
                    }
                }

                unset($html);
                //return $file;
            
            }
            
            return $result;

        }
        
    ?>
    
    <?php
    if( $action == 'check' ){
    ?>

        <div class="item">危险代码扫描结果</div>
    
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
            <tr class="thead">
                <td>位置</td>
                <td>包含可疑内容</td>
                <td>行为描述</td>
                <td>最后修改时间</td>
            </tr>

            <?php

            if($_POST["attachments"]){

                echo '<tr class="band"><td colspan="1">上传附件</td><td colspan="3">位置：'.'attach/'.'</td></tr>';

                $result = scanfiles( VI_ROOT.'attach' );

                if($result){
                    echo $result;
                }else{
                    echo '<tr class="choice"><td colspan="4"><img src="'.VI_BASE.'static/image/icon/tick.png" /> 未发现异常</td></tr>';
                }

            }

            if($_POST["webshell"]){

                echo '
                <tr class="band"><td colspan="1">系统缓存中扫描 Webshell</td><td colspan="3">位置：'.'cache/'.'</td></tr>';

                $result = scanwebshell( VI_ROOT.'cache' );

                if($result){
                    echo $result;
                }else{
                    echo '<tr class="choice"><td colspan="4"><img src="'.VI_BASE.'static/image/icon/tick.png" /> 未发现异常</td></tr>';
                }

                echo '
                <tr class="band"><td colspan="1">模板缓存中扫描 Webshell</td><td colspan="3">位置：static/</td></tr>';

                $result = scanwebshell( VI_ROOT.'static' );

                if($result){
                    echo $result;
                }else{
                    echo '<tr class="choice"><td colspan="4"><img src="'.VI_BASE.'static/image/icon/tick.png" /> 未发现异常</td></tr>';
                }

            }

            if($_POST["templates"]){

                echo '<tr class="band"><td colspan="1">模块缓存中扫描不安全外链</td><td colspan="3">位置：'.'cache/'.'</td></tr>';

                $result = '';

                foreach( $_CACHE['system']['module'] as $appid ){
                    $result .= scantemplate( VI_ROOT.'cache/'.$appid );
                }

                if($result){
                    echo $result;
                }else{
                    echo '<tr class="choice"><td colspan="4"><img src="'.VI_BASE.'static/image/icon/tick.png" /> 未发现异常</td></tr>';
                }

            }

            //echo '<pre>';

            create_file( $log ,date("Y-m-d H:i:s"));
            
            ?>
       </table>
       
    <?php
    }else{
    ?>
    
    	<div class="item">危险代码扫描</div>

        <form id="bind_secure" action="?" method="post">        
        
        <table cellpadding="0" cellspacing="0" class="form">

            <tr>
                <td>
                
                <p><input type="checkbox" class="checkbox" name="webshell" id="webshell" checked /> <label for="webshell">扫描系统缓存文件中的 Webshell</label></p>
                <p><input type="checkbox" class="checkbox" name="templates" id="templates" <?php echo (!new DOMDocument() ? 'disabled' : 'checked');?> /> <label for="templates">扫描页面缓存/模板文件中的不安全外链 (仅 PHP5 或更新版本)</label></p>
                <p><input type="checkbox" class="checkbox" name="attachments" id="attachments" checked /> <label for="attachments">扫描上传附件目录中存在的 PHP/ASP 文件</label></p>
                
                <p>
                	<input name="action" type="hidden" id="action" value="check" />
                	<button type="submit" class="submit">开始扫描</button>
                </p>
                
                </td>
            </tr>
            
         </table>
        
        </form>
        
    <?php
    }
    ?>

	<?php
    if( System :: check_func( 'system-cache' ) ){
    ?>
        
        <div class="item">系统清理工具</div>
		
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
		<tr class="thead">
			<td width="120">工具</td>
			<td>目录</td>
			<td>大小</td>
			<td>作用</td>
			<td width="70"></td>
		</tr>
		
		<tr class="line">
			<td>清除查询缓存</td>
			<td>cache/sql/</td>
			<td class="text-key"><?php echo sizecount(foldersize( VI_ROOT.'cache/sql/' ));?></td>
			<td>删除因查询语句改动而失效的多余缓存，此过程会清空所有查询缓存</td>						
			<td><input name="" type="button" class="button" value="立即使用" onclick="location.href='?action=clean&file=sql';" /></td>
		</tr>
		
		<tr class="band">
			<td>清除RSS缓存</td>
			<td>cache/rss/</td>
			<td class="text-key"><?php echo sizecount(foldersize( VI_ROOT.'cache/rss/' ));?></td>
			<td>删除RSS内容读取缓存，RSS缓存可能在部分功能模块中有涉及和使用</td>						
			<td><input name="" type="button" class="button" value="立即使用" onclick="location.href='?action=clean&file=rss';" /></td>
		</tr>
		
		<tr class="line">
			<td>清除模板缓存</td>
			<td>static/*/*.htm.php</td>
			<td class="text-key"><?php echo sizecount(foldersize( VI_ROOT.'cache/compile/' ));?></td>
			<td>删除各前台页面由 Smarty 产生的缓存，当页面没有正常刷新时使用</td>						
			<td><input name="" type="button" class="button" value="立即使用" onclick="location.href='?action=clean&file=static';" /></td>
		</tr>
		
		</table>
        
	<?php
    }
    
    ?>
    
    <div class="item">文件对比器</div>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
      <tr class="thead">
        <td>ID</td>
        <td>文件名</td>
        <td>特征值</td>
        <td>文件大小</td>
        <td>文件变动</td>
        <td>修改时间</td>
      </tr>
    <?php
    
    if( !$action ){
    
		$result = System :: check_filehash();
		
		$x = 1;
	
	    foreach( $result['file'] as $hash => $file ){
	    
	    	if( $file['lost'] ) continue;
	    
	        echo '<tr class="'. zebra( $i, array( "line" , "band" ) ) .'">';
	        echo '<td>'.( $x++ ).'</td>';
	        
	        if( $file['newly'] ){
	        	echo '<td class="text-yes">'.$file['name'].'</td>';
	        	echo '<td class="text-key">新增文件</td>';
	            echo '<td>'.sizecount(sprintf("%.0f", $file['size'] ) ).'</td>';
		    echo '<td></td>';
				echo '<td class="text-key">'.date("Y-m-d H:i:s",$file['mtime']).'</td>';
	        }else{
	        	echo '<td class="text-yes">'.$file['name'].'</td>';
	        	echo '<td class="text-gray">'.$file['hash'].'</td>';
	            echo '<td>'.sizecount(sprintf("%.0f", $file['size'] ) ).'</td>';
		    echo '<td>'.sizecount(sprintf("%.0f", $file['change'] ) ).'</td>';
				echo '<td class="text-key">'.date("Y-m-d H:i:s",$file['mtime']).'</td>';
	        }
	        
	        echo "</tr>";
	            
	    }
	    
	    if( $result['stat'] == 0 ){
	        echo '<tr> <td colspan="6" class="notice">没有文件变动</td> </tr>';
	    }else if( $result['stat'] == -1 ){
	        echo '<tr> <td colspan="6" class="notice">没有找到特征文件，请联系 <a href="'.$_G['project']['home'].'" target="_blank">VeryIDE</a></td> </tr>';
	    }else{
	        echo '<tr> <td colspan="6" class="choice">请确认以上文件是正常变动，否则可能会有安全风险</td> </tr>';
	    }
    
    }else{
	    
	    echo '<tr> <td colspan="6" class="notice">正在进行其他任务……</td> </tr>';
	    
    }
    
    ?>
        
	</table>

<?php html_close();?>