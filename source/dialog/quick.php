<?php

/*

		用户收藏模块管理

*/

//载入全局配置和函数包
require_once dirname(__FILE__).'/../../app.php';

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_G['product']['charset'];?>" />
<title>模块管理 - Powered By VeryIDE</title>

<?php

echo loader_style(array(VI_BASE."static/style/general.css",VI_BASE."static/style/dialog.css"),$_G['product']['charset'],$_G['product']['version']);

echo loader_script(array(VI_BASE."static/js/mo.js",VI_BASE."static/js/mo.drag.js",VI_BASE."static/js/serv.dialog.js"),'utf-8',$_G['product']['version']);

?>

<script>

Mo.reader(function(){
				   
	if(window.Node){
		Node.prototype.swapNode=function(node){
			var nextSibling=this.nextSibling;
			var parentNode=this.parentNode;
			node.parentNode.replaceChild(this,node);
			parentNode.insertBefore(node,nextSibling);
		}
	}

				   
	var func = function( ele, e ){

		//使其可以拖动
		var dg = new Mo.Drag( ele );
		
		//开始拖动
		dg.onStart = function( x, y, e ) {
			
			//鼠标位置
			var ms = Mo.Event( e ).mouse();
			
			//设置样式
			Mo( ele ).style({ "position":"absolute", "left":ms.x+"px", "top":ms.y+"px" });
			
			//在当前位置创建占位符
			Mo( ele ).insert( "li", { "id" : "engross",  "className" : "temp" } );
	
		
		};
		
		//开始拖动
		dg.onDrag = function( x, y, e ) {
			
			var self = this;
			
			find( e, self );
			
		};
		
		//开始拖动
		dg.onEnd = function( x, y, e ) {
			
			var obj = find( e, self );
			
			if( obj ){
				
				//删除占位符
				Mo("#engross").remove();
			
				//在当前位置创建占位符
				Mo( obj ).insert( "li", { "id" : "engross",  "className" : "temp" } );
				
				//交换节点
				ele.swapNode( obj );
				
			}
						
			//删除占位符
			Mo("#engross").remove();
			
			//设置样式
			Mo( ele ).style({ "position":"" });	
			
			//去样式
			Mo("#addons li").attr({ "className":"" });	
			
			//保存排序
			parent.Serv.Addons.saved( loop() );
			
		};
		
		//
		var find = function( e, self ){
			
			var res = null;
			
			//鼠标位置
			var ms = Mo.Event( e ).mouse();
			
			//比较位置
			Mo("#addons li").each(function(){
				
				//不能是自己或占位符
				if( this == self || this.id == "engross" ) return;
				
				var pos = Mo( this ).position();
				
				//在区域内
				if( ms.x > pos.left && ms.x < pos.left + pos.width && ms.y > pos.top && ms.y < pos.top + pos.height ){
					this.className = "light";
					res = this;
				}else{
					this.className = "";
				}
				
			});
			
			return res;
		
		};
		
		//
		var loop = function(){
			
			var sort = [];
			
			Mo("#addons li").each( function(){
	
				var appid   = this.getAttribute("appid");
				var appname = this.getAttribute("appname");
		
				sort.push( appid );
		
			});
			
			return sort;
		};
		
	};

	//高亮选中主题
	Mo("#addons li").bind( 'mousedown', function( e ){
												 
		func( this , e );
			
	}).bind( 'mousemove', function( e ){
		
		func( this , e );
			
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
            <li><a href="module.php" data-hash="true">快捷方式</a></li>
            <li><a href="widget.php" data-hash="true">小工具</a></li>
            <li><a href="quick.php" data-hash="true">排序</a></li>
            <li><a href="quick.v2.php" data-hash="true">排序（方案2）</a></li>
		</ul>
    
        <ul id="addons">
    
        <?php
        
        //连接数据库
        System :: connect();
        
        //插件列表
        
        $sql="SELECT appid from `sys:quick` WHERE aid = '".$_G['manager']['id']."' ORDER BY sort asc, dateline asc";
        
        $res = System :: $db -> getAll( $sql );
        
        foreach( $res as $row ){
    
            //存在缓存
            if ( isset( $_CACHE['system']['module'][$row['appid']] ) ) {
                
				echo '<li appid="'.$row['appid'].'" appname="'.$_CACHE['system']['module'][$row['appid']]["name"].'">';

				echo '<a><img src="'.VI_BASE.'module/'.$row['appid'].'/icon.png" /><span>'.$_CACHE['system']['module'][$row['appid']]["name"].'</span></a>';

				echo '</li>';
    
            }
        }
    
        //关闭数据库
        System :: connect();
    
        ?>
    
        </ul>
        
    </div>
    
</body>
</html>