<?php

/*

		用户主题设置

*/

//载入全局配置和函数包
require_once dirname(__FILE__).'/../../app.php';

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_G['product']['charset'];?>" />
<title>更换主题 - Powered By VeryIDE</title>

<?php

echo loader_style(array(VI_BASE."static/style/general.css",VI_BASE."static/style/dialog.css"),'utf-8',$_G['product']['version']);

echo loader_script(array(VI_BASE."static/js/mo.js",VI_BASE."static/js/mo.ui.js",VI_BASE."static/js/serv.dialog.js"),'utf-8',$_G['product']['version']);

?>

<script>

Mo.reader(function(){

	//高亮当前主题
	Mo("#theme li").each( function(){

		if( Mo(this).attr("rel") == Mo.get('theme') ){
			this.className = 'active';			
		}

	});
	
	//高亮选中主题
	Mo("#theme li").bind( 'click', function( index, e ){
		
		//设置主题
		parent.Serv.System.theme( Mo(this).attr("rel") );
		
		//取消高亮所有主题
		Mo("#theme li").each( function(){
			this.className = '';	
		});		
		
		//高亮当前主题
		var obj = Mo.Event( e ).element();
	
		if( obj == this || obj.parentNode == this ){
			this.className = 'active';
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
	
		<ul id="theme">
	
			<?php
			//遍历皮肤目录
			$root = VI_ROOT.'static/theme/';
			
			$dirs = loop_dir( $root );
			
			foreach( $dirs as $file ){
				
				//皮肤名称
				$name = fileparm($root.$file.'/style.css','name');
				
				//忽略没有名称的，为删除皮肤提供方法
				if( $name ){	
					echo '<li rel="'.$file.'"><img src="'.VI_BASE.'static/theme/'.$file.'/preview.png" />'.$name.' </li>';						
				}
				
			}
	
			?>	
	
		</ul>
        
        <div class="tabs"></div>
        <script type="text/javascript">
		
			Mo.TabXXX = function( tab, box, e, number ){
				this.Event = e;
				this.Tab = tab;
				this.Box = box;
				this.Number= number ? number : 1;
			
				//选项卡改动
				this.Change = function( tab ){
					
					//数组长度
					var length = Mo( this.Box ).size();
					
					//全部隐藏
					Mo( this.Box ).hide();
					Mo( this.Tab + ' a' ).attr({'class':''});
					
					Mo( this.Tab + ' a' ).item( tab ).className = 'active';
					
					////////////////////////
					
					//起始位置
					var offset = tab * this.Number;
					
					//终止位置
					var number = ( tab + 1 ) * this.Number;
					
					for( ; offset < number && offset < length; offset++){
						Mo( this.Box ).item( offset ).style.display = '';
					}
					
				}
			
				//播放选项卡
				this.Play = function( t ){
					var self = this;
					
					//数组长度
					var length = Mo( this.Box ).size();
					
					//分页数量
					var pgsize = Math.ceil(length / this.Number);
					
					for( var i = 0; i < pgsize; i++ ){
						
						(function(){
						
							var tab = i;
							Mo( self.Tab ).create( "a" , {}, true ).bind( self.Event, function(){
								self.Change( tab );
							} );
								  
						})();
						
					};
					
					if(t <= length - 1 ){
						this.Change(t);
					}else{
						this.Change(0);
					}
				}
				
				this.Find = function( rel ){
					var self = this;
					
					//数组长度
					var length = Mo( this.Box ).size();
					
					var offset = 0;
					
					Mo( this.Box ).each(function( ){												 
						if( this.getAttribute("rel") == rel ){
							offset = Math.floor( this.index / self.Number );
						}						
					});
					
					this.Play( offset );
					
				}
				
			}

			///////////////////
    
			var TO = new Mo.TabXXX( '.tabs', '#theme li',"click",6);
			//TO.Play(0);
			TO.Find( Mo.get('theme') );
			
		</script>
	
	</div>
	
</body>
</html>