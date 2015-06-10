<?php

if(!defined('VI_BASE')) {
	exit('Access Denied');
}

///////////////////////////////////
?>

<div id="wrapper">

    <!--header start-->
    <div id="header">
    
		<?php
        //加载用户偏好
        require VI_ROOT.'source/dialog/effect.php';
        ?>
        
        <div id="header-logo">
            <?php echo '<strong><a href="'. $_G['setting']['global']['url'] .'" title="'. $_G['setting']['global']['site'] .'" target="_blank">'. $_G['setting']['global']['site'] .'</a></strong>';?>
        </div>
        
        <!--info start-->
        <div id="header-info">
            <?php
            $avatar = ( $_G['manager']["avatar"] ? $_G['manager']["avatar"] : 'image/face-thumb.jpg' );
            ?>
            <a rel="javascript:void(0);"  id="bind_avatar"><img src="<?php echo $avatar;?>" alt="修改头像" /></a>
        
            <div>
                <?php
                if( $_G['manager']['account'] ){
                    echo '<strong>'.$_G['manager']['account'].'</strong> <span>['.$_CACHE['system']['group'][$_G['manager']['gid']]['name'].']</span>';               
                }else{
					echo '欢迎您，请登录';
                }
                ?>
            </div>
            
                <div>
                <a id="bind_start">开始</a>
                - <a id="bind_system">系统设置</a>
                - <a  id="bind_module">模块管理</a>
                <?php
                if( $_G['manager']['account'] ){
                    echo ' - <a id="bind_account">修改资料</a> - <a id="bind_logout">退出</a> ';               
                }
                ?>
                <?php
                    //AdobeAIR
                    if( strpos($_SERVER["HTTP_USER_AGENT"],"AdobeAIR") !== false ){
                        echo '- <a onclick="javascript:location.reload(true);">刷新</a>';
                    }
                ?>
                </div>
            </div>
            <!--info end-->
        
            <!--tool start-->
            <div id="header-tool">
        
            <div id="header-links">    
                <?php
                foreach( $_G['project']['menu'] as $key => $val ){
                echo '<a href="'.$key.'" target="_blank">'.$val.'</a>';	
                }
                ?>
            </div>
            
            <div id="header-search">
                <form action="#" onsubmit="">
                <input type="text" class="text" maxlength="12" name="q" id="q" placeholder="百度搜索，输入并回车.." />
                </form>
            </div>
            
            </div>
        <!--tool end-->
    </div>
    <!--header end-->
    
    <!--control start-->
    <div id="control" title="显示/隐藏侧边栏"></div>
    <!--control end-->
    
    <!--sidebar start-->
    <div id="sidebar">
        <?php
        
        //菜单控制
        if( $_G['manager']['account'] ){						
            echo '
                <div id="sidebar-search">
                    <ul>
                        <li>'.loader_image("icon/user.png").' <a id="bind_admin">用户</a></li>
                        <li>'.loader_image("icon/folder.png").' <a id="bind_attach">附件</a></li>					
                        <li>'.loader_image("icon/info.png").' <a id="bind_licence">验证</a></li>
                    </ul>
                </div>
                ';
        }
        ?>
    
        <div id="sidebar-menu">
        
        <?php
        //菜单控制
        if( $_G['manager']['account'] == '' ){
        
	        echo '
	        <div id="menu-powered">
	            <p></p>
	            <p align="center"><a href="http://www.veryide.com/buy.php" target="_blank"><img src="static/image/model/'.$_G['licence']["type"].'.gif" alt="'.$_G['version'][$_G['licence']["type"]]["name"].'" /></a></p>
	            <p align="center" class="text-key">VeryIDE '.$_G['version'][$_G['licence']["type"]]["name"].'用户</p>
	            <p align="center">- 登录后菜单可见 -</p>
	            <p align="center">'.$_G['project']['powered'].'</p>
	        </div>
	        ';
                
        }else{
            
                echo '
                <div id="menu-title">
                    <span id="bind_splay" title="展开菜单"></span>
                    <span class="custom" id="bind_custom" title="偏好设置"></span>
                    <strong>VeryIDE</strong>
                </div>';
    
                //菜单缓存
                echo '<div id="menu-link">';
                
                //当前用户组权限设置
                //$permit = $_CACHE['system']['group'][$_G['manager']["gid"]]["config"];
                
                //当前用户组
				$group = $_CACHE['system']['group'][ $_G['manager']['gid'] ];
                
                //模块菜单
                foreach( $_CACHE['system']['module'] as $appid => $app ){
                    
                    //判断模块状态、是否有菜单、对当前用户组是否可见
                    if( $app["state"] && is_array( $app["context"] ) && in_array( $appid, $group['module'] ) ){
                            
                        $icon = '<img src="module/'.$appid.'/icon.png" />';
                    
                        echo '<div data-appid=\''.$appid.'\'><span>'. $icon . $app["name"] .'</span></div>';
                        echo '<ul id="mod_'.$appid.'">';
                        
						foreach( $app["context"] as $name => $config ){
							echo '<li><a href="'.VI_BASE.'module/'.$appid."/".( is_array($config) ? $config['link'] : $config ).'">'.$name.'</a></li>';
						}
						
                        echo '</ul>';
                        
                    }
                
                }
                
                echo '</div>';
    
            }
            ?>
    
        </div>
    </div>
    <!--sidebar end-->
    
    <!--container start-->
    <div id="container">
		<iframe frameborder="0" id="column" allowtransparency="true" name="column" src="source/dialog/blank.html"></iframe>
    </div>
    <!--container end-->

</div>