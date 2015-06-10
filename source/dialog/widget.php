<?php

/*

		系统数据统计

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
	Mo("#widget button").bind( 'click', function( index , e ){
                
		switch( this.className ){
				
			//新添加
			case "":

				this.innerHTML = '取消';
				this.className = 'added';
				
				//保存设置
				parent.Serv.Widget.added( Mo(this).attr("appid") , Mo(this).attr("widget") );
					
			break;                        
			
			 //删除插件
			case "added":

				this.innerHTML = '添加';
				this.className = '';
				
				//保存设置
				parent.Serv.Widget.remove( Mo(this).attr("appid") , Mo(this).attr("widget") );

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
        	<li class="menu"><a href="http://www.veryide.com/projects/bee/widget.htm" target="_blank">开发手册</a></li>
            <li><a href="module.php" data-hash="true">快捷方式</a></li>
            <li><a href="widget.php" data-hash="true">小工具</a></li>
            <li><a href="quick.php" data-hash="true">排序</a></li>
		</ul>
    
        <dl id="widget">
    
        <?php
        
        //连接数据库
        System :: connect();
		
		///////////////////////////////////////////////////
        
        //插件列表
        
        $sql="SELECT appid,widget from `sys:widget` WHERE aid = '".$_G['manager']['id']."'";
        
        $res = System :: $db -> getAll( $sql );
        
        foreach( $res as $key => $val ){                
                $widgets[$val["appid"]][] = $val['widget'];
        }
		
        ///////////////////////////////////////////////////
        
        $i = 1;
        
        //当前用户组
		$group = $_CACHE['system']['group'][ $_G['manager']['gid'] ];
    
        //遍历模块
        foreach( $_CACHE['system']['module'] as $appid => $config ){
			
			//插件目录
			$root = VI_ROOT."module/".$appid."/widget/";
			
            //加载配置
            if ( file_exists( $root ) && $group['widget'] && is_array( $group['widget'][$appid] ) ) {
            
                $app = $_CACHE['system']['module'][$appid];
				
				echo '<dt>'.$app["name"].'</dt>';
				
				echo '<dd>';
				
				echo '<ul>';
				
				//遍历目录
				$list = loop_dir( $root );
				
				foreach( $list as $file ){
					
					//
					$widget = $file;
					
					$doc = $root.$widget."/config.xml";
					
					//如果配置文件存在
					if( file_exists($doc) && in_array( $widget, $group['widget'][$appid] ) ){
						
						$config = xml_array( sreadfile($doc) );
						
						//var_dump($config);
						
						//UTF8 转 GBK
						if( $_G['product']['charset'] == "gbk" ){								
							foreach ($config['widget'] as $key => $val){
								if( is_string($val) ){
									$config['widget'][$key] = $val ? iconv('UTF-8', 'GBK//IGNORE', $val) : $val;
								}
							}							
						}
						
						////////////////////////////////
							
						//
						echo '<li rel="'.$file.'">';
						
						echo '<img src="'.VI_BASE.'module/'.$appid.'/widget/'.$file.'/preview.png" /> <br /> '.$config['widget']["name"].' <br /> ';
						
						if( is_array( $widgets[$appid] ) && in_array( $widget , $widgets[$appid] ) ){                        
								echo '<button appid="'.$appid.'" widget="'.$widget.'" class="added">取消</button>';                        
						}else{                        
								echo '<button appid="'.$appid.'" widget="'.$widget.'">添加</button>';                        
						}
						
						echo '</li>';
	
					}
					
				}
				
				echo '</ul>';
                    
                echo '</dd>';
                
                $i++;
    
            }
        }
        
        if( $i == 1 ){
	        echo '<p> 未发现可用小工具，请联系管理员 </p>';
        }
    
        //关闭数据库
        System :: connect();
    
        ?>
    
        </dl>
        
    </div>
    
</body>
</html>