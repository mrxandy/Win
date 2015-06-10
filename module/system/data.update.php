<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("更新管理 - VeryIDE");
?>
    
	<?php
	
	//更新基准目录
	$base = Database :: direct('update');
	
	$action = getgpc('action');
	$update = getgpc("update");
                
	//连接数据库
	System :: connect();
	
	switch($action){

		case "update":

			//检查权限
			$func = 'system-update-add';
			System :: check_func( $func, FALSE );

			$res = Database :: update( $update );
			
			switch( $res ){
				
				case 'locked':
					echo '<div id="state" class="failure">抱歉！安装已经存在。更新于早前已经安装，并在使用中：<span class="text-key">'.$update.'</span></div>';					
				break;
				
				case 'abort':
					echo '<div id="state" class="failure">抱歉！安装更新失败。以下是本错误信息详细报告：</div>';					
				break;
				
				case 'success':
					echo '<div id="state">恭喜！成功安装更新：'.$update.'</div>';
				break;
				
			}
	
		break;
		
		case "execute":

			//检查权限
			$func = 'system-update-sql';
			System :: check_func( $func, FALSE );
		
			$res = Database :: query( stripslashes( $_POST['sql'] ) );
			
			if( $res['error'] == 0 ){
				
				echo '<div id="state">恭喜！成功执行自定义 SQL 语句。</div>';
			
				//写入日志
				System :: insert_event($func,time(),time(),"执行查询：".$_POST['sql'] );
				
				//更新模块
				Module :: search();
						
			}else{
				
				echo '<div id="state" class="failure">抱歉！执行SQL语句失败。以下是本错误信息详细报告：</div>';
				
				//trigger_error(str_replace('class="error"','class="text-no"',preg_replace('/<h4>(.*?)<\/h4>/','',$array[1])), E_USER_ERROR);
			}
		
		break;
	}
			
	//关闭数据库
	System :: connect();
    
    ?>
    
    <div class="item">数据更新清单</div>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
      <tr class="thead">
        <td>ID</td>
        <td>更新名称</td>
        <td>适用版本</td>
        <td>更新状态</td>
        <td>发布时间</td>
        <td width="40">操作</td>
      </tr>
    <?php
	
		$result = System :: check_upgrade();
        
        $x = 1;
    
        foreach( $result['list'] as $file ){
        
            echo ("<tr class='". zebra( $i, array( "line" , "band" ) ) ."'>");
            echo ('<td>'.( $x++ ).'</td>');
            echo ('<td class="text-yes">'.$file['name'].'</td>');
            echo ('<td>'.$file['version'].'</td>');
            
            if ( $file['lock'] ) {
                echo ("<td>于 <span class='text-key'>".date("Y-m-d H:i:s",$file['mtime'])."</span> 安装此更新</td>");
                echo ("<td>".date("Y-m-d H:i:s",$file['ctime'])."</td>");
            }else{
                echo ("<td><em>未安装此更新</em></td>");
                echo ("<td>".date("Y-m-d H:i:s",$file['ctime'])."</td>");
            }
            
            if( $file['lock'] === FALSE ){
                echo '<td width="70"><button type="button" class="button" onclick="location.href=\'?action=update&update='.$file['name'].'\';">安装</button></td>';
            }else{
                echo '<td>无</td>';
            }
            
            echo ("</tr>");
                
        }
        
        if( $x == 1 ){
			echo '<tr> <td colspan="6" class="notice">更新清单暂时没有记录</td> </tr>';
        }else{
	        echo '<tr> <td colspan="6" class="choice">请按版本顺序安装更新</td> </tr>';
        }
    
    ?>
        
	</table>
    
	<?php
    if( System :: check_func( 'system-update-sql' ) ){
    ?>
    
        <div class="item">执行 SQL 查询</div>
    
        <form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-mode="edit" data-valid="true">
        
	        <p>
		        <textarea name="sql" style="width:700px;" rows="6" id="sql"></textarea>
	        </p>
	        
	        <p class="highlight">
		        注意: 为确保升级成功，请不要修改官方发布的 SQL 语句任何部分。
	        </p>
	        
	        <p>
		        <input name="action" type="hidden" id="action" value="execute" />
	            <button type="submit" name="Submit" class="submit">执行此语句</button>
	        </p>
        
        </form>
    
	<?php
    }
    ?>

<?php html_close();?>