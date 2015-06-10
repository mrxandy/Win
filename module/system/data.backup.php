<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("数据备份 - VeryIDE");
?>

	<?php

    //延长程序运行时间
    set_time_limit(0);
    
    //载入类库
    require_once VI_ROOT."source/class/database.php";
    
	//备份基准目录
    $base = Database :: direct('backup');
    
    $action = getgpc('action');
    
    if($action){

		//连接数据库
		System :: connect();
    
        switch($action){
        
            case "execute":
    
                //检查权限
                $func = 'system-backup-exe';
                System :: check_func( $func, FALSE );
				
				$update = getgpc("update");
                $sqlfile = $base.$update;
                
                //锁文件
                $lock = str_replace(".sql",".lock",$sqlfile);
                
                if(file_exists($lock)){
                    
                    echo '<div id="state" class="failure">抱歉！备份已经使用。恢复时间：<span class="text-key">'.$update.'</span></div>';
                    
                }else{
                    
                    if(file_exists($sqlfile)){
                    
                        $stat = Database :: import( $sqlfile );
                        
                        if( $stat ){                        
                        
                            //写入锁
                            create_file( $lock ,date("Y-m-d H:i:s"));
                        
                            echo '<div id="state">恭喜！成功恢复数据：'.$update.'，共有 '.$stat.' 条记录。</div>';
                            
                            //搜索模块
                            Module :: search();
                
                            //写入日志
                            System :: insert_event($func,time(),time(),"恢复备份：".$update);
                        
                        }else{
                           
                            echo '<div id="state" class="failure">抱歉！恢复备份失败。以下是本错误信息详细报告：</div>';
							
                        }
                        
                    }
                    
                }
                
                //echo $update;
            break;
            
            case "create":
    
                //检查权限
                $func = 'system-backup-add';
                System :: check_func( $func, FALSE );
                $name = getgpc('name');
                
                if (!$name ){
                    
                    System :: redirect($jump,"备份名称不能为空！");
                    
                }elseif ( !preg_match('/^[^\/\\\?!\*]+$/',$name) ){
                    
                    System :: redirect($jump,"备份名称中有非法字符！");
                    
                }else{
					
					$stat = Database :: export( $name, getgpc('prefix'), getgpc('option') );
                                            
                    if( $stat ){
            
                        //写入日志
                        System :: insert_event($func,time(),time(),"数据备份：". $name.".sql" );				
                        
                        echo '<div id="state">恭喜！成功备份数据库，共有 '.$stat.' 条记录。</div>';
                                
                    }else{
                        
                        echo '<div id="state" class="failure">抱歉！备份数据库失败。</div>';
                    }
                    
                }
            
            break;
        }
	
		//关闭数据库
		System :: connect();

    }
    
    ?>
	
	<div class="item">数据备份清单</div>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
      <tr class="thead">
        <td>ID</td>
        <td>备份文件</td>
        <td>文件名称</td>
        <td>文件大小</td>
        <td>备份时间</td>
        <td width="150">恢复</td>
        <td width="100">下载</td>
      </tr>
    <?php
	
	$list = loop_file( $base, array(), array('sql') );
	
	$x = 1;
    
	foreach( $list as $file ){
		
		//锁文件
		$lock = str_replace(".sql",".lock",$file);
		$val = file_exists($base.$lock);
		
		$name = Database :: getname( $file );
		
		echo ("<tr class='". zebra( $i, array( "line" , "band" ) ) ."'>");
		echo ('<td>'.( $x++ ).'</td>');
		echo ('<td class="text-yes">'. ( $name ? $name : $file ) .'</td>');
		echo ('<td class="text-key">'. ( System :: check_func( 'system-backup-dow' ) ? 'data/backup/'.$file : '没有权限' ) .'</td>');
		
		echo ("<td>".sizecount(filesize($base.$file))."</td>");
		echo ("<td>".date("Y-m-d H:i:s",filemtime($base.$file))."</td>");
		
		echo '<td class="text-key">';			
		if( !$val && System :: check_func( 'system-backup-exe' ) ){
			echo '<button type="button" class="button" data-href="?action=execute&update='.$file.'">执行</button>';	
		}else{
			echo $val ? date("Y-m-d H:i:s",filemtime($base.$lock)) : '没有权限';
		}
		echo '</td>';
		
		echo '<td class="text-gray">';
		if( System :: check_func( 'system-backup-dow' ) ){
			echo '<button type="button" class="button" data-href="'.VI_BASE.'data/backup/'.$file.'" data-target="_blank">下载</button>';	
		}else{
			echo '没有权限';	
		}
		echo '</td>';
	
		echo ("</tr>");

	}
	
	if( count( $list ) == 0 ){
		echo '<tr>     <td colspan="7" class="notice">备份清单暂时没有记录</td>      </tr>';
	}
    
    ?>
	</table>

	<div class="item">创建数据备份</div>

    <form action="?" method="post" name="backup">

	<?php
    if( System :: check_func( 'system-backup-add' ) ){
    ?>
    <table cellpadding="0" cellspacing="0" class="form">
    
        <tr><td colspan="2" class="section">注意: 将只对数据库进行备份，并以将 SQL 语句以文件形式存储。</td></tr>
        
        <tr>
            <th>备份名称：</th>
            <td>
                <input name="name" type="text" class="text" id="name" size="35" data-valid-name="备份名称" data-valid-empty="yes" />
                <a href="javascript:void(0);" onclick="Mo('input[name=name]').value(this.innerHTML);void(0);"><?php echo date("Y-m-d");?></a>
            </td>
        </tr>
        
        <tr>
            <th>备份范围：</th>
            <td>
                <label class="text-yes"><input type="checkbox" class="checkbox" name="prefix[]" value="<?php echo VI_DBMANPRE;?>" checked="checked" data-valid-name="表的范围" data-valid-minsize="1" /> 系统表</label>
                <label class="text-yes"><input type="checkbox" class="checkbox" name="prefix[]" value="<?php echo VI_DBMODPRE;?>" checked="checked" data-valid-name="表的范围" data-valid-minsize="1" /> 模块表</label>
                <label class="text-key"><input type="checkbox" class="checkbox" name="prefix[]" value="*" data-valid-name="表的范围" data-valid-minsize="1" /> 其他表</label>
            </td>
        </tr>
        
        <tr>
            <th>其他选项：</th>
            <td>
                <label class="text-yes"><input type="checkbox" class="checkbox" name="option[]" value="structure" checked="checked" data-valid-name="备份选项" data-valid-minsize="1" /> 含结构</label>
                <label class="text-yes"><input type="checkbox" class="checkbox" name="option[]" value="data" checked="checked" data-valid-name="备份选项" data-valid-minsize="1" /> 含数据</label>
            </td>
        </tr>
        
        <tr>
            <td></td>
            <td>
                <input name="action" type="hidden" id="action" value="create" />
                <button type="submit" name="Submit" class="submit">创建此备份</button>
            </td>
        </tr>
        
    </table>
    
    </form>
        
    <?php
    }else{
    
    	echo System :: check_func( 'system-backup-add',true);
    
    }
    ?>
    
    <script type="text/javascript">

        //绑定表单事件
        Mo("button[type=button]").bind( 'click', function( index, e ){
														  
			if( Mo( this ).attr('data-target') == '_blank' ){
				
				window.open( Mo( this ).attr('data-href') );
				
			}else{
    
				if( confirm('此操作不可撤消，确定要这么做吗？') ){
					Serv.Message('正在执行操作，请稍后…','info', 300);
					location.href = Mo( this ).attr('data-href');
				}
				
			}
    
        });
    
    </script>
    
<?php html_close();?>