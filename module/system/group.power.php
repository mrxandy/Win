<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("我的权限 - VeryIDE");
?>


	<?php
	
	//其他用户组
	if( getnum("gid") ){
		
		$gid = getnum("gid");
		
	}else{
		
		//loader
		require('include/naver.admin.php');
		
		//当前用户组
		$gid = getnum("gid") ? getnum("gid",0) : $_G['manager']["gid"];		
	}
	
	//从缓存读取
	$row = $_CACHE['system']['group'][$gid];
	
	//配置
    $config = $row['config'];
	
	?>
    	
	<div id="box">
                        
        <table cellpadding="0" cellspacing="0" class="form">
        
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>
            
            <tr>
                <th>分组名称：</th>
                <td>
               		<label><?php echo $row['name'];?></label>
                </td>
            </tr>
        
            <tr>
                <th>分组标识：</th>
                <td>		
					<?php                    
                    echo '<img src="'.VI_BASE.'static/image/medal/'.$row["medal"].'" />';                    
                    ?>
                </td>
            </tr>
                
            <tr><td colspan="2" class="section"><strong>系统页面</strong></td></tr>
            
            <tr>
                <td></td>
                <td>
                
                    <ul class="func">
                    
                    <?php
                    
                    $i = 0;
                    foreach($_G['project']['page'] as $key => $value){
                        echo '<li><label>'.loader_image( ($config[$key]=='Y'?'valid.gif':'invalid.gif') ).' '.$value.'</label></li>';
                        
                        if( $i>=6 ){
                            echo '<br />';
                            $i=0;
                        }else{							
                            $i++;
                        }
                    }
                    
                    ?>
                    
                    </ul>
                    
                </td>
            </tr>
                
            <tr><td colspan="2" class="section"><strong>系统权限</strong></td></tr>
            
            <tr>
                <td></td>
                <td>
                    
                    <?php
                    $i = 0;
                    foreach( $_CACHE['system']['module']['system']['permit'] as $group => $array ){
                        
                        echo '<ul class="func">';
                        
                        echo '<li><strong>'.$group.'：</strong></li>';
                        
                        foreach($array as $key => $value){
                        
                            echo '<li><label>'.loader_image( ($config[$key]=='Y'?'valid.gif':'invalid.gif') ).' '.$value.'</label></li>';
                        
                        }							
                        echo '</ul>';
                        
                    }
                    
                    ?>
                    
                </td>
            </tr>
            
                
            <tr><td colspan="2" class="section"><strong>模块权限</strong></td></tr>
            
            <tr>
                <td></td>
                <td>
                    
					<?php
                    
                    //读取配置			   
                     
                    foreach( $_CACHE['system']['module'] as $appid => $app ){
                        
                        if( $app['model'] != "module" || is_array($app['permit']) === FALSE ) continue;
								
						echo '<ul class="func">';
                
                		echo '<li><strong>'.$app['name'].'：</strong></li>';
                        
                        
                        foreach($app['permit'] as $item => $name){
                            
                            echo '<li><label>'.loader_image( ($config[$item]=='Y'?'valid.gif':'invalid.gif') ).' '.$name.'</label></li>';									
                        }
                        
                       echo '</ul>';
                               
                    }
                    
                    ?>
                    
                </td>
            </tr>
        
        </table>

	</div>


<?php html_close();?>