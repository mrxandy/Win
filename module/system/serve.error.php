<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("错误信息 - VeryIDE");
?>



    <div id="error">
    
		<?php
		
		$action = $_GET["action"];
        
        switch($action){
        
            case "page":
			
				$page = $_GET["page"];
			
                echo '
                    <p><strong>禁止浏览本页</strong></p>
                    <p>当前用户组 <span class="text-key">（'.$_CACHE['system']['group'][$_G['manager']["gid"]]["name"].'）</span> 被设置为禁止浏览本页面 <span class="text-key">（'.$page.'）</span></p>					
					<p><a href="group.power.php" target="_dialog" title="用户组权限" data-width="80%" data-height="65%">查看当前用户组权限</a></p>
                ';
            
            break;
        
            case "func":
			
				$func = $_GET['func'];
			
                echo '
                    <p><strong>禁止使用功能</strong></p>
                    <p>当前用户组 <span class="text-key">（'.$_CACHE['system']['group'][$_G['manager']["gid"]]["name"].'）</span> 被设置为禁止使用本功能 <span class="text-key">（'.$func.'）</span></p>					
					<p><a href="group.power.php" target="_dialog" title="用户组权限" data-width="80%" data-height="65%">查看当前用户组权限</a></p>
                ';
            
            break;
        
            case "cache":
			
				$cache = $_GET["cache"];
				$url = $_GET["url"];
			
                echo '
                    <p><strong>载入缓存失败</strong></p>
                    <p>系统载入缓存 <span class="text-key">（cache/'.$cache.'）</span> 失败，可能该文件还未生成，点这里更新缓存：<a href="'.$url.'">'.$url.'</a></p>					
					<p>更多信息请访问 VeryIDE '.$_G['project']['faq'].'</p>
                ';
            
            break;

            case "class":

                $class = $_GET["class"];

                echo '
                    <p><strong>无效系统模块</strong></p>
                    <p>当前服务器不支持使用 <span class="text-key">（'.$class.'）</span> 扩展，请联系系统管理员</p>					
                    <p>更多信息请访问 VeryIDE '.$_G['project']['faq'].'</p>
                ';

            break;
            
            case "empty":

                $empty = $_GET["empty"];

                echo '
                    <p><strong>参数不能为空</strong></p>
                    <p>当前页面执行操作时不接受空的参数 <span class="text-key">（索引：'.$empty.'）</span> 或数据，请仔细检查</p>					
                    <p>更多信息请访问 VeryIDE '.$_G['project']['faq'].'</p>
                ';

            break;
        
        }
        ?>
    	<p><button type="button" class="submit" onclick="history.back();">返回上一页</button></p>
    
    </div>
		


<?php html_close();?>