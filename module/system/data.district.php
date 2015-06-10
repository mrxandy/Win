<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("分类列表 - VeryIDE");
?>


<?php

	$action=getgpc('action');

	//连接数据库
	System :: connect();

	if ($action){
	
		switch($action){
			
			//修改
			case "update":
			
				//检查权限
				System :: check_func( 'system-system-set', false );
				
				$name = getgpc('name');
				$sort = getgpc("sort");
				$state = getgpc("state");
				$delete = getgpc("delete");
				$parent = getgpc("parent");
			
				foreach( $name as $id => $val ){					
					
					if( $delete[$id] == 'true' ){
						$sql = "DELETE FROM `mod:common_category` WHERE id in(".$id.") or parent in(".$id.")";					
					}else{					
						$sql="UPDATE `mod:common_category` SET name='".$val."',sort='".intval( $sort[$id], 0 )."',state='".intval( $state[$id], 0 )."',parent='".intval( $parent[$id], 0 )."',modify=".time()." WHERE id=".$id;
					}
					
					System :: $db -> execute( $sql );
				}
				
				$_G['project']['message']="批量修改成功！";
				
			break;
			
			//状态
			case "create":
				
				$sql="UPDATE `mod:common_category` SET appid = 'system-district' WHERE appid = 'shop-area'";					
				System :: $db -> execute( $sql );
			
				$_G['project']['message']="缓存更新成功！";
				
			break;
			
			case "add":			
			
				//检查权限
				System :: check_func( 'system-system-set', false );	
				
				$name = getgpc('name');
				$sort = getnum("sort",0);
				$parent = getnum("parent",0);
				$parent = $parent ? getnum("category",0) : $parent;

				$sql="INSERT INTO `mod:common_category`(appid,name,parent,sort,state,dateline) VALUES('system-district','".$name."',".$parent.",".$sort.",1,".time().")";
				
				System :: $db -> execute( $sql );	
				
				$_G['project']['message']="新增区域成功！";
				
			break;
			
		}		
		
		//更新分类数组缓存
		Cached :: multi( 'system', "SELECT id,name,parent,state FROM `mod:common_category` WHERE appid = 'system-district' ORDER BY sort ASC",'table.district',array('alias'=>'district'));
		
		//更新分类脚本缓存
		Cached :: script( 'system', "SELECT id,name,parent FROM `mod:common_category` WHERE appid = 'system-district' ORDER BY sort ASC","DISTRICT","mod.district",array( 'unicode'=>array('name') ) );
		
	}
	
	Cached :: loader('system','table.district');
	
?>

	<?php	
	echo System :: check_func( 'system-system-set',true);	
	?>

    <div id="search">
    <form name="find-form" id="find-form" method="post" data-mode="edit" data-valid="true">
      <span class="action">
        <input type="button" value="更新缓存" onclick="if(confirm('确定现在更新分类缓存吗？')){location.href='?action=create';}" class='button'>
      </span>
        
        区域：<input name="name" type="text" class="text" value="" size="20" data-valid-name="分类名称" data-valid-empty="yes" />
        排序：<input name="sort" type="text" class="text digi" value="" data-valid-name="分类排序" data-valid-number="no" />
      <var data-type="tip">从小到大依次排序</var>
        
        <label>
        <input type="radio" class="radio" name="parent" id="parent" value="0" onclick="Mo('#category').disabled();" checked>
        大分类
        </label>
        
        <input name="parent" id="parent" type="radio" class="radio" onclick="Mo('#category').enabled();" value="1">
        <select name="category" id="category" disabled="disabled">            
        <?php            
        foreach($_CACHE['system']['district'] as $key){
			if($key["parent"]=="0"){
				echo '<option value="'.$key["id"].'">'.$key["name"].'</option>';
			}
        }            
        ?>            
        </select>
        
		<button type="submit" name="Submit" class="button">新增分类</button>
        <input name="action" type="hidden" id="action" value="add" />
    </form>
    </div>

    <form action="?" method="post" data-valid="true">
    
    <?php		
    $sql="SELECT * FROM `mod:common_category` WHERE parent=0 and appid = 'system-district' ORDER BY sort ASC,id DESC";
    $result = System :: $db -> getAll( $sql );		
    ?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
          <tr class="thead">
            <td>ID</td>
            <td width="55">排序 <var data-type="tip">从小到大依次排序</var></td>
            <td>分类名称</td>
            <td>状态</td>
            <td>变更分类</td>
            <td>删除</td>
            <td>创建时间</td>
          </tr>
        <?php
        foreach( $result as $row ){
            
            ?>
              <tr class="entry">
                <td><?php echo $row['id'];?></td>
                <td>
                <input name="sort[<?php echo $row['id'];?>]" type="text" class="text" size="4" value="<?php echo $row["sort"];?>" data-valid-name="分类排序" data-valid-number="yes" />
                </td>
                <td title="<?php echo $row['name'];?>">
                <input name="name[<?php echo $row['id'];?>]" type="text" class="text text-yes text-bold" value="<?php echo $row['name'];?>" data-valid-name="分类标题" data-valid-empty="yes" />
                </td>
                
                <td>
                <select name="state[<?php echo $row['id'];?>]">                    
                <?php                    
                foreach($_G['project']['state'] as $key => $val){
                    if( $key < 0 || $key > 1 ) continue;
                    echo '<option value="'.$key.'" '.( $key == $row['state'] ? 'selected="selected"' : '' ).'> '.$val.'</option>';
                }                    
                ?>
                </select>
                </td>
                <td>&nbsp;</td>
                <td align="center">                    
                    <input name="delete[<?php echo $row['id'];?>]" type="checkbox" value="true" />
                </td>
                <td title="修改于 <?php echo date("Y-m-d H:i:s",$row['modify']);?>"><?php echo date("Y-m-d",$row['dateline']);?></td>
              </tr>
              
              <?php
            //子菜单
            $sql="SELECT * FROM `mod:common_category` WHERE parent=".$row['id']." ORDER BY sort ASC,id DESC";
            $result2 = System :: $db -> getAll( $sql );
            
            foreach( $result2 as $row ){
                
                ?>
                <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>">
                <td><?php echo $row["id"];?></td>
                <td><input type="text" class="text" name="sort[<?php echo $row["id"];?>]" size="4" value="<?php echo $row["sort"];?>" data-valid-name="分类排序" data-valid-number="yes" /></td>
                <td><?php echo loader_image("tree.gif");?> <input type="text" class="text" name="name[<?php echo $row["id"];?>]" size="22" value="<?php echo $row["name"];?>" data-valid-name="分类名称" data-valid-empty="yes" /></td>
                
                <td>
                <select name="state[<?php echo $row['id'];?>]">                    
                <?php                    
                foreach($_G['project']['state'] as $key => $val){
                    if( $key < 0 || $key > 1 ) continue;
                    echo '<option value="'.$key.'" '.( $key == $row['state'] ? 'selected="selected"' : '' ).'> '.$val.'</option>';
                }                    
                ?>                    
                </select>
                </td>
                <td>
                <select name="parent[<?php echo $row['id'];?>]">
                <?php            
                foreach($_CACHE['system']['district'] as $key){
                    if( $key["parent"] == "0" ){
                        echo '<option value="'.$key["id"].'" '.( $key["id"] == $row['parent'] ? 'selected="selected"' : '' ).'>'.$key["name"].'</option>';
                    }
                }
                ?>            
                </select>
                </td>
                <td align="center">
                <input name="delete[<?php echo $row['id'];?>]" type="checkbox" value="true" />
                </td>
                <td title="修改于 <?php echo date("Y-m-d H:i:s",$row['modify']);?>"><?php echo date("Y-m-d",$row["dateline"]);?></td>
            </tr>
              <?php
            }
        }
		if( count( $result ) == 0 ){
			echo '<tr><td colspan="7" class="notice">没有检索到相关分类</td></tr>';
		}
        ?>
        </table>

	<?php

	//关闭数据库
	System :: connect();
		
	if( count( $result ) ){
	?>
	
    <div id="saving">
        <input name="action" id="action" type="hidden" value="update" />
        <input type="submit" class="submit" value="保存更改" />
    </div>
    
    <?php
	}
    ?>
    
	</form>

<?php html_close();?>