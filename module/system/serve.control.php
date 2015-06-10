<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("系统设置 - VeryIDE");
?>

	<style>
		html,body,#wrapper{ overflow:hidden; padding:0; margin:0;}	
	</style>

	<script type="text/javascript">
	
		var Reload = function(){
			location.reload();	
		}
	
		var Resize = function(){
			var body = document.documentElement;
			var w = body.clientWidth;
			var h = body.clientHeight;
			
			if( w < 200 || h <= 200 ) return;
		
			var p = Mo("#panel").style({"height":h+"px"}).style('width');
			
			//console.log( p );
			
			Mo("#vessel").style({"width":(w-150)+"px","height":h+"px"});	
		};
		
		
		var Execute = function( link ){
			
			Mo("#vessel").html('<iframe src="'+ link +'" frameborder="0"></iframe>');
			
			Resize();
			
		};
		
		var Callback = function( mark, stat ){
	
			if( Mo("a[data-mark='"+ mark +"'] em").size() == 0 ){
				Mo("a[data-mark='"+ mark +"']").create( "em" , { "innerHTML" : "0", "className" : "badge" }, true ).hide();
			}
			
			if( stat ){
				Mo("a[data-mark='"+ mark +"'] em").html(stat).show();
			}else{
				Mo("a[data-mark='"+ mark +"'] em").hide();
			}
			
		}
	
		Mo.reader(function(){
						   
			Mo("#panel li a").bind("click",function(){
			
				//置灰全部
				Mo("#panel li a").attr({"className":""});
													
				//高亮当前
				Mo( this ).attr({"className":"active"});
				
				Execute( Mo.get("link", this.href ) );
													
			});
			
			/////////////////////
			
			if( Mo.get("link") ){
				Mo("#panel li a[href='#link="+ Mo.get("link") +"']").event('click');
			}else{
				Mo("#panel li a[rel=first]").event('click');
			}
			
		});
		
		Mo.resize( Resize );
		
		parent.Factor && Mo.reader( parent.Factor.Fetch );
	
	</script>

	<dl id="panel">
    	<dt><img src="<?php echo VI_BASE.'static/image/refresh.gif';?>" onclick="Reload();" />系统设置</dt>
        <dd>
        	<ul>
            	<li><a href="#link=system.setting.php?do=global" rel="first"><img src="<?php echo VI_BASE.'static/image/extra/settings.png';?>" class="icon" />全局设置</a></li><li><a href="#link=system.setting.php?do=attach"><img src="<?php echo VI_BASE.'static/image/extra/installer.png';?>" class="icon" />附件设置</a></li><li><a href="#link=system.setting.php?do=mail"><img src="<?php echo VI_BASE.'static/image/extra/mail.png';?>" class="icon" />邮件设置</a></li><li><a href="#link=data.district.php"><img src="<?php echo VI_BASE.'static/image/extra/safari.png';?>" class="icon" />区域管理</a></li><li><a href="#link=data.censor.php"><img src="<?php echo VI_BASE.'static/image/extra/instinctiv-shuffle.png';?>" class="icon" />词语过滤</a></li><li><a href="#link=data.update.php" data-mark="upgrade"><img src="<?php echo VI_BASE.'static/image/extra/timecapsule.png';?>" class="icon" />更新管理</a></li><li><a href="#link=data.backup.php"><img src="<?php echo VI_BASE.'static/image/extra/services.png';?>" class="icon" />数据备份</a></li><li><a href="#link=system.server.php" data-mark="status"><img src="<?php echo VI_BASE.'static/image/extra/term1.png';?>" class="icon" />系统信息</a></li><li><a href="#link=system.licence.php"><img src="<?php echo VI_BASE.'static/image/extra/superpong.png';?>" class="icon" />版本验证</a></li></ul>
        </dd>
    	<dt><img src="<?php echo VI_BASE.'static/image/refresh.gif';?>" onclick="Reload();" />模块设置</dt>
        <dd>
        	<ul>
            	<?php
				foreach($_CACHE['system']['module'] as $appid=>$val){
					
					//当前模块目录
					$base = VI_ROOT.'module/'.$appid;
					
					//有配置项
					if( file_exists($base.'/setting.xml') || file_exists($base.'/navigate.xml') ) {
						echo '<li>'. ( !file_exists($base.'/setting.php') ? '<img src="'.VI_BASE.'static/image/warning.gif" class="warn" />' : '' ) .'<a href="#link=module.setting.php?appid='.$appid.'" data-mark="'.$appid.'"><img src="'.VI_BASE.'module/'.$appid.'/icon.png" class="icon" />'.$val["name"].'</a></li>';
					}
					
				}
				?>
            </ul>
        </dd>
    </dl>

	<div id="vessel">
    
    
    </div>
	


<?php html_close();?>