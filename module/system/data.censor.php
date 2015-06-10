<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("词语过滤 - VeryIDE");
?>


<?php

	require(VI_ROOT."source/class/censor.php");

	$action=getgpc('action');

	//连接数据库
	System :: connect();

	if ($action){
	
		switch($action){
			
			//修改
			case "update":
			
				//检查权限
				System :: check_func( 'system-system-set', false );
				
				$find = getgpc('find');
				$replace = getgpc("replace");
				$content = getgpc("replacecontent");
				$type = getgpc("wordtype");
				$delete = getgpc("delete");
			
				foreach( $find as $id => $val ){					
					
					if( $delete[$id] == 'true' ){
						$sql = "DELETE FROM `sys:word` WHERE id in(".$id.")";					
					}else{					
						$sql="UPDATE `sys:word` SET find='".$val."',replacement='".( $replace[$id] == '{REPLACE}' ? $content[$id] : $replace[$id] )."',type='".intval( $type[$id], 0 )."' WHERE id=".$id;
					}					
					
					System :: $db -> execute( $sql );
				}
				
				$_G['project']['message']="批量修改成功！";
				
			break;
			
			//状态
			case "create":
				
				Censor :: instance() -> build();
			
				$_G['project']['message']="缓存更新成功！";
				
			break;
			
			case "add":			
			
				//检查权限
				System :: check_func( 'system-system-set', false );	
				
				$find = getgpc('find');
				$replace = getgpc('replace');
				$content = getgpc('replacecontent');
				$type = getnum("wordtype",0);

				$sql="INSERT INTO `sys:word`(find,replacement,type,aid,account,dateline,ip) VALUES('".$find."','".( $replace[0] == '{REPLACE}' ? $content[0] : $replace[0] )."',".$type.",'".$_G['manager']['id']."','".$_G['manager']['account']."',".time().",'". GetIP() ."')";
				
				System :: $db -> execute( $sql );	
				
				$_G['project']['message']="新增词语成功！";
				
			break;
			
		}
		
	}
	
?>

	<?php	
	echo System :: check_func( 'system-system-set',true);	
	?>

    <div id="search">
    <form name="find-form" id="find-form" method="post" data-mode="edit" data-valid="true">
      <span class="action">
        <input type="button" value="更新缓存" onclick="if(confirm('确定现在更新分类缓存吗？')){location.href='?action=create';}" class='button'>
      </span>
        
        词语：<input name="find" type="text" class="text" value="" size="20" data-valid-name="不良词语" data-valid-empty="yes" />
        动作：		
		<select name="replace[0]" class="replace">                    
			<?php                    
			foreach($_G['censor']['replace'] as $key => $val){
				echo '<option value="'.$key.'"> '.$val.'</option>';
			}                    
			?>
		</select>
		
		<input class="text" type="text" size="20" name="replacecontent[0]" value="" />
		
		<select name="wordtype[<?php echo $row['id'];?>]">
			<?php            
			foreach($_G['censor']['wordtype'] as $k => $v ){
				echo '<option value="'.$k.'">'.$v.'</option>';
			}
			?>            
		</select>
        
		<button type="submit" name="Submit" class="button">新增词语</button>
        <input name="action" type="hidden" id="action" value="add" />
    </form>
    </div>

    <form action="?" method="post" data-valid="true">
    
    <?php	
	
	$q=getgpc('q');
	$s=getgpc("s");
	
    $sql="SELECT * FROM `sys:word` WHERE 1=1";
	
	if( $q != '' && isset( $s ) ){
		
		if( $q == '{REPLACE}' ){
			$sql.=" and  `".$s."` NOT IN ( '{MOD}', '{BANNED}' )";
		}else{
			$sql.=" and  `".$s."` = '".$q."'";	
		}		 
    	
    }
	
	$sql.=" ORDER BY id ASC";
	
	//echo $sql;
	
	//查询数据库_总记录数
    $row_count = System :: $db -> getCount( $sql );	

    //分页参数
    $page=getpage("page");
    $page_start=$_G['setting']['global']['pagesize']*($page-1);
    $sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];
	
    //分页链接
    $url="?q=".urlencode($q)."&s=".$s."&page=";	
	
    $result = System :: $db -> getAll( $sql );
    ?>
    <table id="table" width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
          <tr class="thead">
            <td width="50">ID</td>
            <td>不良词语</td>
            <td data-sort-field="replacement" data-sort-screen='<?php echo fix_json( $_G['censor']['replace'] );?>' data-sort-query="?s=$field&q=$value">过滤动作</td>
            <td data-sort-field="type" data-sort-screen='<?php echo fix_json( $_G['censor']['wordtype'] );?>' data-sort-query="?s=$field&q=$value">词语分类</td>
            <td align="center">删除</td>
            <td>操作者</td>
          </tr>
        <?php
        foreach( $result as $row ){
            
            ?>
              <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>">
                <td><?php echo $row['id'];?></td>
                <td title="<?php echo $row['name'];?>">
                <input name="find[<?php echo $row['id'];?>]" type="text" class="text text-yes text-bold" value="<?php echo $row['find'];?>" size="30" data-valid-name="分类标题" data-valid-empty="yes" />
                </td>                
                <td>
                <select name="replace[<?php echo $row['id'];?>]" class="replace">                    
					<?php                    
					foreach($_G['censor']['replace'] as $key => $val){
						echo '<option value="'.$key.'" '.( ( $key == $row['replacement'] || !in_array( $row['replacement'], array( '{BANNED}', '{MOD}' ) ) ) ? 'selected="selected"' : '' ).'> '.$val.'</option>';
					}                    
					?>
                </select>
				<input class="text" type="text" size="20" name="replacecontent[<?php echo $row['id'];?>]" value="<?php echo $row['replacement'];?>" />
                </td>
                <td>
				<select name="wordtype[<?php echo $row['id'];?>]">
					<?php            
					foreach($_G['censor']['wordtype'] as $k => $v ){
						echo '<option value="'.$k.'" '.( $k == $row['type'] ? 'selected="selected"' : '' ).'>'.$v.'</option>';
					}
					?>            
                </select>
				</td>
                <td align="center">                    
                    <input name="delete[<?php echo $row['id'];?>]" type="checkbox" value="true" />
                </td>
                <td>                    
                    <?php echo $row['account'];?>
                </td>
              </tr>              
        <?php
        }
		if( count( $result ) == 0 ){
			echo '<tr><td colspan="6" class="notice">没有检索到相关分类</td></tr>';
		}
        ?>
        </table>

	<?php

	//关闭数据库
	System :: connect();
		
	if( count( $result ) ){
	?>
	
    <div id="saving">
        <div class="y">
			<input name="action" id="action" type="hidden" value="update" />
			<input type="submit" class="submit" value="保存更改" />
		</div>
		
		<?php
            echo multipage($page,$row_count,$_G['setting']['global']['pagesize'],$url,"page");
        ?>
    </div>
    
    <?php
	}
    ?>
    
	</form>
	
	<script>
	
	Mo(".replace").bind('change',function(){
		var id = this.name.replace(/replace\[|\]/ig,'');
		//console.log( id );
		if( this.options[this.options.selectedIndex].value == '{REPLACE}' ){
			Mo('input[name=\'replacecontent['+ id +']\']').show();
		}else{
			Mo('input[name=\'replacecontent['+ id +']\']').hide();
		}
		
	}).event('change');
	
	</script>

<?php html_close();?>