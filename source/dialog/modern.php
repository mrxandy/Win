<?php

if(!defined('VI_BASE')) {
	exit('Access Denied');
}

///////////////////////////////////
?>

<!--欢迎_开始-->
<div id="greet">
</div>
<!--欢迎_结束-->

<!--页面_开始-->
<div id="wrapper" style=" display:none; <?php echo $config["bg-image"] ? 'background:url('.$config["bg-image"].');' : '';?>">

<!--页头_开始-->
<div id="header">

    <!--LOGO_开始-->
    <div id="logo">
	<a href="<?php echo $_G['setting']['global']['url'];?>" title="<?php echo $_G['setting']['global']['site'];?>" target="_blank"><img src="<?php echo $_G['setting']['global']["logo"] ? $_G['setting']['global']["logo"] : 'image/logo.png';?>" /></a>
    </div>
    <!--LOGO__结束-->
    
		<?php
		//登录后显示快捷栏
		if( $_G['manager']['id'] ){
		?>
	
		<!--快捷方式_开始-->
		<ul id="quick">
			<!--li id="bind_menu"><a href="javascript:void(0);"><img src="image/extra/scummvm2.png" /><br />开始</a></li-->
			<li id="bind_market" style="display:none;" reserve="true"><a href="javascript:void(0);"><img src="static/image/extra/netvibes.png" /><br />添加</a></li>
	
			<!--li><a href="javascript:void(0);" onclick="Serv.Apps.open('system','系统管理','safari','start.php');"><img src="image/extra/safari.png" /><br />开始</a></li-->
			<!--li id="bind_admins"><a href="javascript:void(0);"><img src="image/extra/contacts.png" /><br />用户</a></li>
			<li id="bind_system"><a href="javascript:void(0);"><img src="image/extra/settings.png" /><br />系统</a></li>
			<li id="bind_module"><a href="javascript:void(0);"><img src="image/extra/calculator.png" /><br />模块</a></li>
			<li id="bind_stats"><a href="javascript:void(0);"><img src="image/extra/stocks.png" /><br />统计</a></li>				
			
			<li id="bind_addons"><a href="javascript:void(0);"><img src="image/extra/netvibes.png" /><br />应用</a></li-->
			
			<!--li id="bind_tasks"><a href="javascript:void(0);"><img src="image/extra/itunes.png" /><br />任务</a></li-->
			
			<!--li id="bind_widget"><a href="javascript:void(0);"><img src="image/extra/gpsphone.png" /><br />小工具</a></li-->
			<!--li id="bind_addons"><a href="javascript:void(0);"><img src="image/spacer.gif" /></a></li-->				
		</ul>
		<!--快捷方式_开始-->
		
		<div id="addon"></div>
    
		<?php
		}
		?>
       
        
        <div id="sign">
        	<span><a href="javascript:void(0);">打卡</a></span>
        	<span><a href="javascript:void(0);">申报</a></span>
        </div>
	
		<!--头像_开始-->
		<div id="avatar">
						
			<a href="javascript:void(0);" id="bind_avatar"><img src="static/image/face-thumb.jpg" /></a>
			<strong><?php echo $_G['manager']['account'] ? $_G['manager']['account'] : '<a href="http://www.veryide.com/">admin</a>';?></strong>
			<br />
			<div id="person"><?php echo date("n月j日"); ?> <?php echo $_G['project']['weeks'][date("w")]; ?></div>

			<?php
			//加载用户偏好
			require VI_ROOT.'source/dialog/effect.php';
			?>
			
		</div>
		<!--头像_结束-->
		
		<!--菜单_开始-->
		<div id="collect" style=" display:none;">
			<ul>
			</ul>
		</div>
		<!--菜单_结束-->      
		
		<!--链接_开始-->
		<div id="link" style="display:none;">            
			<ul>
				<?php
				//推荐链接
				foreach($_G['project']['menu'] as $link => $name){
					echo '<li><a target="_blank" href="'.$link.'">'.$name.'</a></li>';
				}					
				?>
			</ul>
			<div class="clear"></div>
		</div>
		<!--链接_结束-->

</div>
<!--页头_结束-->

<!--主体_开始-->
<div id="container">

		<!--工具_开始-->
		<div id="widget">
		</div>
		<!--工具_结束-->
		
		<!--窗口_开始-->
		<div id="window">
		</div>
		<!--窗口_结束-->

		<!--应用_开始-->
		<div id="module">
			
			<dl>
				<!--dt> <strong>公告通知</strong> </dt>
				<dd>
				
					<ol id="notice">
						<li><a href="javascript:void(0);" onclick="Serv.Apps.open('phone','Phone');"> JavaScript内核系列第0版整理稿下载
<span>2011/3/4</span></a></li>
						<li><a href="javascript:void(0);" onclick="Serv.Apps.open('ipod','iPod',[300,500]);"> JavaScript内核系列第0版整理稿下载 <span>2011/3/4</span></a></li>
						<li><a href="#"> JavaScript内核系列第0版整理稿下载 <span>2011/3/4</span></a></li>
						<li><a href="#"> JavaScript内核系列第0版整理稿下载 <span>2011/3/4</span></a></li>
						<li><a href="#"> JavaScript内核系列第0版整理稿下载 <span>2011/3/4</span></a></li>
					</ol>

					<!--div id="notice_desc">没有记录</div-->
				<!--/dd-->		
				
				<!--dt> <strong>最近使用</strong> </dt>
				<dd>
					<ul id="lately">
						<li><a href="javascript:void(0);" onclick="Serv.Apps.open('phone','Phone');"><img src="image/extra/Phone.png" /><br /><span>Phone</span></a></li>
						<li><a href="javascript:void(0);" onclick="Serv.Apps.open('ipod','iPod',[300,500]);"><img src="image/extra/iPod.png" /><br /><span>iPod</span></a></li>
						<li><a href="#"><img src="image/extra/Mail.png" /><br /><span>Mail</span></a></li>
						<li><a href="#"><img src="image/extra/Maps.png" /><br /><span>Maps</span></a></li>
						<li><a href="#"><img src="image/extra/Safari.png" /><br /><span>Safari</span></a></li>
					</ul>

					<!--div id="lately_desc">没有记录</div-->
				<!--/dd-->					
				
				<!--dt> <ol id="addons_page"></ol> <strong>我的应用</strong> </dt>
				<dd>
					<ul id="addons">
						<?php
						/*							
						foreach($_CACHE['system']['module'] as $appid=>$val){
						
							//忽略后台服务模块
							if( $val["config"] ){
							
								echo '<li><a href="javascript:void(0);" onclick="Serv.Apps.open(\''.$appid.'\',\''.$val["name"].'\',\'\');"><img src="module/'.$appid.'/icon.png" /><br /><span>'.$val["name"].'</span></a></li>';
						
							}
							
						}							
						*/							
						?>
					</ul>
					
					<div id="addons_desc">没有记录</div>
					
				</dd-->
			</dl>				
 
		</div>
		<!--应用_结束-->

</div>
<!--主体_结束-->

<!--页尾_开始-->
<div id="footer">

	<!--任务栏_开始-->
	<ul id="task"></ul>
	<!--任务栏_结束-->

	<!--设置栏_开始-->
	<ul id="place">
		<li id="loading"><img src="static/image/loading.gif" /></li>
		<li id="bind_home"><a href="javascript:void(0);"></a></li>
		<li id="bind_help"><a href="javascript:void(0);"></a></li>
		<li id="bind_desktop"><a href="javascript:void(0);"></a></li>
		<li id="bind_theme"><a href="javascript:void(0);"></a></li>
		<li id="bind_refresh"><a href="javascript:void(0);"></a></li>
		<li id="bind_custom"><a href="javascript:void(0);"></a></li>
		<li id="bind_logout"><a href="javascript:void(0);"></a></li>
	</ul>
	<!--设置栏_结束-->

	<!--驱动方_开始-->
	<div id="powered">
		<?php echo $_G['project']['powered'];?>
	</div>
	<!--驱动方_结束-->
	
</div>
<div id="footbg"></div>
<!--页尾_结束-->    

</div>
<!--页面_结束-->