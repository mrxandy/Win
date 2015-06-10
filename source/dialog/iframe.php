<?php

//载入全局配置和函数包
require_once dirname(__FILE__).'/../../app.php';

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_G['product']['charset'];?>" />
<title>IFRAME 引用代码 - Powered By VeryIDE</title>

<?php
echo loader_style(array(VI_BASE."static/style/general.css",VI_BASE."static/style/share.css"),'utf-8',$_G['product']['version']);

echo loader_script(array(VI_BASE."static/js/mo.js",VI_BASE."static/js/mo.drag.js",VI_BASE."static/js/mo.interface.js"),'utf-8',$_G['product']['version']);

echo $_CACHE['system']['module']['passport'] ? loader_script(array(VI_BASE."module/passport/js/share.js"),'utf-8',$_G['product']['version']) : '';

?>

<style type="text/css">
	#handle{position:absolute; cursor:se-resize; z-index:1000;}
	#bg{position:absolute;background:#333; filter:alpha(opacity=10); opacity:0.1;z-index:0; top:0;left:0; z-index:999;}
</style>
</head>

<body>
	
	<div id="wrapper" class="auto">
    
    	<div id="header">
        	<h2>获取 IFRAME 引用代码</h2>
        </div>
        
        <div id="main">
        
        	<p><strong>小提示：</strong>拖动右下角箭头图标来控制窗口尺寸，不出现滚动条时效果为最佳</p>
		
        	<p>
                <img id="handle" src="<?php echo VI_BASE;?>static/image/cursor/se-resize.png" />
                <iframe id="iframe" name="frame" frameborder="0" style="width:400px;height:400px; margin:auto; border:2px dashed #ccc;"></iframe>
            </p>
			
            <form onSubmit="return false;" style="clear:both;">
            	<p>
                地址：<input type="text" class="text" id="url" value="" style="width:385px;" readonly="readonly" />
                </p>
                <p>
                尺寸：<input type="text" class="text" id="width" name="width" value="" /> × <input type="text" class="text" id="height" name="height" value="" />
                </p>
                
                <table>
                	<tr>
                    <td>
                        <p>
                            HTML 代码：
                            <label><input type="checkbox" class="checkbox" name="frame_width" value="auto" /> 自动设置宽</label>
                            <label><input type="checkbox" class="checkbox" name="frame_height" value="auto" checked="true" /> 自动设置高</label>
                            <a href="http://www.veryide.com/guide.php?appid=form&id=72" target="_blank">使用说明 &raquo;</a>
                        <br />
                        <textarea cols="45" rows="5" id="code" name="code" style="width:400px; vertical-align:middle;"></textarea>
                        <button type="button" onClick="Mo.Clipboard( Mo('#code').value() , function( text ){ alert('已复制如下信息到剪贴板:\n\n'+text); } );" style=" line-height:70px; vertical-align:middle;">复制代码</button>
                        </p>
                    </td>
                    <td>
                        <p>
                            UBB 代码：
                        <br />
                        <textarea cols="45" rows="5" id="ubb" name="ubb" style="width:400px; vertical-align:middle;"></textarea>
                        <button type="button" onClick="Mo.Clipboard( Mo('#ubb').value() , function( text ){ alert('已复制如下信息到剪贴板:\n\n'+text); } );" style=" line-height:70px; vertical-align:middle;">复制代码</button>
                        </p>
                    </td>                    
                    </tr>
                </table>
                
            </form>

        </div>
       
    	<div id="footer">
			<?php echo $_G['project']['powered'];?>
            <?php echo $_G['product']['appname'];?>
            <?php echo $_G['product']['version'];?>
        </div>
    
    
    </div>
    
<script type="text/javascript">

var Frame = {
	
	Init : function(){
		
		//记录ID
		this.id = Mo.get("id");

		//框架对象
		this.frame = Mo("iframe[name=frame]").attr({'id':'IFRAME-'+this.id,'frame_width':'auto','frame_height':'auto','scrolling':'auto'}).item(0);
		
		//框架变更地址
		var s = location.search.substring(1);
		if (s !=''){
			Mo("#url").value(s);
			this.frame.src= s;
		};
		
		//如果有跨域消息机制
		if( typeof XD != 'undefined' ){
			
			XD.receiveMessage(function( data ){
				
				//设置宽度
				if( Frame.frame.getAttribute('frame_width') == 'auto' && data['width'] ){
					Frame.frame.style.width = data['width'];
				}
				
				//设置高度
				if( Frame.frame.getAttribute('frame_height') == 'auto' && data['height'] ){
					Frame.frame.style.height = data['height'];
				}
				
				//显示到当前
				Frame.frame.scrollIntoView(true);
				
				Frame.onLoad();
			
			});
			
		}else{
			
			//bind
			if ( this.frame.attachEvent ){
				this.frame.attachEvent("onload", function(){
					Frame.onLoad();
				});
			} else {
				this.frame.onload = function(){
					Frame.onLoad();
				};
			};
		
		}
		
		////////////////////////
		
		Mo('input[name=frame_width]').bind('change',function(){
			Frame.getCode( Mo('input[name=width]').value(), Mo('input[name=height]').value() );
		});
		
		Mo('input[name=frame_height]').bind('change',function(){
			Frame.getCode( Mo('input[name=width]').value(), Mo('input[name=height]').value() );
		});
		
	},
	
	//onload
	onLoad : function(){

		var pos = Mo( Frame.frame ).position();

		//set iframe
		var tbr = Mo('#handle').item(0);

		tbr.style.left	= pos.left + pos.width +"px";
		tbr.style.top	= pos.top + pos.height +"px";	

		var dg = new Mo.Drag(tbr);

		dg.onStart = function(x, y) {	

			var doc = Mo.document;

			if( Mo("#bg").size() == 0 ){

				//bg
				var bg=document.createElement("DIV");
					bg.id="bg";
	
					document.body.appendChild(bg);
			}
			
			Mo("#bg").style({"width":"100%","height":Math.max( doc.scrollHeight,doc.offsetHeight, doc.clientHeight )+"px"}).show();
				
		};

		dg.onDrag = function( x, y ) {
			
			var pos = Mo( Frame.frame ).position();

			var w = x - pos.left;
			var h = y - pos.top ;
			
			Mo("#width").value(w +"px");
			Mo("#height").value(h +"px");

			Frame.frame.style.width  = w +"px";
			Frame.frame.style.height = h +"px";

			Frame.getCode(w,h);

		};

		dg.onEnd = function(x, y) {
			Mo("#bg").hide();
		};
		
		Frame.getCode( Frame.frame.offsetWidth, Frame.frame.offsetHeight);

	},
	
	//code
	getCode : function( w, h ){
		
		var width = Mo('input[name=frame_width]').value();
		var height = Mo('input[name=frame_height]').value();
		
		Mo("#width").value( w );
		Mo("#height").value( h );
		
		Mo("#code").value('<iframe id="IFRAME-'+ this.id +'" src="'+ this.frame.src +'" frameborder="0" width="'+w+'" height="'+h+'"'+ ( width ? ' frame_width="'+ width +'"' : '' )+( height ? ' frame_height="'+ height +'"' : '' ) +'></iframe>');
		
		Mo("#ubb").value('[iframe='+w+','+h+']'+ this.frame.src +'[/iframe]');
		
	}
	
};

Frame.Init();

</script>
    
</body>
</html>