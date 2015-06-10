<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("版本验证 - VeryIDE");
?>

	<?php
	
	//安装地址
	//$install = VI_HOST;
	
	require_once VI_ROOT.'source/class/cloud.php';
	
	$action = getgpc('action');
	
	switch($action){
		
		case "activation":
			
			//验证授权
			$data = Cloud :: licence();
			
			if( !$data ){
				
				echo '<div id="state" class="failure">抱歉！与 VeryIDE 服务器数据传输出错，请稍后再试。</div>';
				
			}else{
				
				switch( $data['amount'] ){
					
					case 0:				
						echo '<div id="state" class="failure">您当前使用的是非商业授权的免费版。</div>';
					break;
					
					default:
						echo '<div id="state">恭喜！版本验证成功，当前使用的是 <strong class="text-key">'.$_G['version'][$data['type']]['name'].'</strong></div>';
					break;
				
				}
				
			}
			
		break;
		
		
	}
	
	?>

    <form action="?" method="get">
   		<input type="hidden" name="action" value="activation" />
    
        <table cellpadding="0" cellspacing="0" class="form">

                <tr><td class="section"><strong>版本验证</strong></td></tr>

                <tr>
                	<td class="block">
                        <p class="text-key">
                            将收集以下信息发送至 VeryIDE ：
                        </p>
                        <p>
                            当前域名：<span><?php echo $_G['project']["domain"];?></span>
                        </p>
                        <p>
                            安装地址：<span><?php echo VI_HOST;?></span>
                        </p>
                        <p>
                            安装时间：<span><?php echo date("Y-m-d",VI_START);?></span>
                        </p>
                    </td>
                </tr>
                
                <tr>
                	<td>
                	
                   		<p>
                   			<button type="submit" name="Submit" class="submit">立即验证</button>
                   			<?php if( $_G['licence']['username'] ){?>
                   			已绑定 VeryIDE 云平台用户：<strong class="text-yes"><?php echo $_G['licence']['username'];?></strong>
                   			<?php }?>
				        </p>
                   		
                    </td>                
                </tr>
                
        </table>
    
    </form>
    
    <table cellpadding="0" cellspacing="0" class="form">

        <tr><td colspan="<?php echo count($_G['version']);?>" class="section"><strong>版本对比</strong></td></tr>

        <tr>
			<?php
            
            foreach( $_G['version'] as $key => $val ){                    
                echo '<td align="center">';
				
					if( $_G['licence']['type'] == $key ){
						echo '<span style="float:right" class="text-yes"><img src="'.VI_BASE.'static/image/icon/tick.png" /> 当前</span>';
					}
				
                	echo '<a href="http://www.veryide.com/market.php" target="_blank"><img src="'.VI_BASE.'static/image/model/'.$key.'.gif" /></a><br />';
					echo  $val['desc'];
					
                echo '</td>';
            
            }
            
            ?>                    
        </tr>

        <tr><td colspan="<?php echo count($_G['version']);?>" class="section"> <a class="y" href="http://www.veryide.com/market.php" target="_blank">购买模块 &raquo;</a> <strong>推荐模块</strong></td></tr>

        <tr>
			<td colspan="<?php echo count($_G['version']);?>">
			
				<script type="text/html" id="segment_recommend">
	        		<ol class="module">
			        <% for( var index in recommend ){ %>
					<li>
						<a href="http://www.veryide.com/market.php?action=show&appid=<%=recommend[index].appid%>" target="_blank">
							<img src="<%=recommend[index].icon%>"><%=recommend[index].name%><br /><em><%=recommend[index].price > 0 ? '￥' + recommend[index].price : '免费'%></em>
						</a>
					</li>
					<% } %>
					</ol>
				</script>
				
				<div id="package_recommend">
					<p class="notice">正在载入推荐模块……</p>
				</div>
				
				
				<script>
				var callback = function( data ){
					
					Mo('#package_recommend').attr( { 'outerHTML' : Mo.Template( "segment_recommend", { 'recommend' : data } ) } );
					
				}
				</script>
		        
		        <script src="<?php echo $_G['project']['home'];?>api.php?action=service&execute=recommend&module=<?php echo implode( ',', array_keys( $_CACHE['system']['module'] ) );?>&callback=callback">/*获取推荐模块*/</script>
		        
	        </td>
			            
        </tr>
    </table>
    
    <script type="text/javascript">

        //绑定表单事件
        Mo("form").bind( 'submit', function(){
														 
			Serv.Message('正在发送请求，请稍后…','info', 300);
    
        });
    
    </script>


<?php html_close();?>