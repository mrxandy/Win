<?php

/*

		系统模块列表

*/

//载入全局配置和函数包
require_once dirname(__FILE__).'/../../app.php';

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_G['product']['charset'];?>" />
<title>添加插件 - Powered By VeryIDE</title>

<?php

echo loader_style(array(VI_BASE."static/style/general.css",VI_BASE."static/style/dialog.css"),$_G['product']['charset'],$_G['product']['version']);

echo loader_script(array(VI_BASE."static/js/mo.js",VI_BASE."static/js/serv.dialog.js"),'utf-8',$_G['product']['version']);

?>

<script>

Mo.reader(function(){

	//高亮选中主题
	Mo("#module button").bind( 'click', function( e ){
			
		switch( this.className ){
				
			//新添加
			case "":
	
				this.innerHTML = '取消';
				this.className = 'added';
				
				//保存设置
				parent.Serv.Addons.added( Mo(this).attr("appid") );
					
			break;                        
			
			 //删除插件
			case "added":
	
				this.innerHTML = '添加';
				this.className = '';                                 
				
				//保存设置
				parent.Serv.Addons.remove( Mo(this).attr("appid") );
	
			break;
		
		}

	});
	
	//主框架载入事件
	Serv.Manager.Loaded();

});

</script>

</head>

<body>

    <div id="wrapper">

        <?php
    
        //未登录
        if( !$_G['manager']['id'] ){    
            exit('<div id="state" class="failure">未登录，请先登录！</div>');
        }
    
        ?>
        
        <ul id="naver">
            <li class="menu"><a href="http://www.veryide.com/market.php" target="_blank">购买模块</a></li>
            <li><a href="module.php" data-hash="true">快捷方式</a></li>
            <li><a href="widget.php" data-hash="true">小工具</a></li>
            <li><a href="quick.php" data-hash="true">排序</a></li>
        </ul>
    
        <ul id="module">
        
        <?php
		if( count( $_CACHE['system']['module'] ) == 0 ){
		?>
        
        	<p><a onclick="parent.Serv.Module.open('system','模块管理','calculator','module.control.php?action=search');parent.Serv.store.module.Remove();">使用模块搜索工具，扫描新模块</a></p>
            
        <?php
		}elseif( count( $_CACHE['system']['module'] ) == 1 ){
			
			echo '<p><a href="http://www.veryide.com/market.php" target="_blank">去官网购买更多模块 &raquo;</a></p>';
			
		}
		?>
    
        <?php
        
        //连接数据库
        System :: connect();
        
        //插件列表
        
        $sql="SELECT appid from `sys:quick` WHERE aid = '".$_G['manager']['id']."'";
        
        $res = System :: $db -> getAll( $sql );
        
        foreach( $res as $key => $val ){                
                $addon[] = $val["appid"];
        }        
        
        ///////////////////////////////////////////////////
        
        $i = 1;
        
        //当前用户组
		$group = $_CACHE['system']['group'][ $_G['manager']['gid'] ];
    
        //遍历模块
        foreach( $_CACHE['system']['module'] as $appid => $app ){
    
            //只显示启用的普通模块
            if ( $app["state"] === FALSE || $app['model'] != "module" ) continue;
            
            //仅显示可见模块
			if( $group['module'] && !in_array( $appid, $group['module'] ) ) continue;
            
            echo '<li'.( $i % 2 == 0 ? ' class="zebra"' : '' ).'>';
            
            echo '
                <div class="icon">
                    <img src="'.VI_BASE.'module/'.$appid.'/icon.png" />
                </div>';
                
            echo '<div class="name">
                    <strong>'.$app["name"].'</strong>
                    <br />
                    '.$app["describe"].'
                </div>';
                
            echo '<div class="site">
                     '.( $app["version"] ? '<span>'.number_format($app["version"],1).'</span>' : '' ).'
                    <br />
                    <a href="'.$_G['project']['home'].'guide.php?appid='.$appid.'" target="_blank">使用手册</a>
                </div>';
                
            echo '<div class="plus">';
            
				if( is_array( $addon ) && in_array( $appid , $addon ) ){                        
					echo '<button appid="'.$appid.'" type="button" class="added">取消</button>';                        
				}else{                        
					echo '<button appid="'.$appid.'"type="button">添加</button>';                        
				}
                    
            echo '</div>';
                
            echo '</li>';
            
            $i++;
                
        }
    
        //关闭数据库
        System :: connect();
    
        ?>
    
        </ul>
        
    </div>
    
</body>
</html>