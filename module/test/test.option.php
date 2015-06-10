<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("试题选项 - VeryIDE");
?>


	<?php

	$appid = Module :: get_appid();

	//module
	require 'config.php';
	
	//连接数据库
	System :: connect();
	
	$do = getgpc('do');

	$action = getgpc('action');
	$jump = getgpc('jump');
	
	$fid = getnum('fid',0);
	$gid = getnum('gid',0);
	
	//批处理列表
	$list = getgpc('list');
	$list = is_array($list) ? implode(",", $list) : $list;
	
	if ($action){

		$name = getgpc('name');
		$sort = getgpc("sort");
		$oid = getnum('id',0);
		$description = getgpc('description');

		switch($action){
			
			case "add":
			
				//检查权限
				System :: check_func( 'test-list-add', false );
			
				//批量插入
				foreach( $name as $key => $val){
					$sql="INSERT INTO `mod:form_option`(name,sort,fid,gid,dateline,stat,state) VALUES('".FilterHtml($val)."',".intval($sort[$key],0).",".$fid.",".$gid.",".time().",0,1)";
					System :: $db -> execute( $sql );
				}
				
				System :: redirect("?do=list&fid=".$fid."&gid=".$gid,"信息提交成功!");
				
			break;
				
			case "edit":
			
				$sql="SELECT * FROM `mod:form_option` WHERE id=".$oid;
				$row = System :: $db -> getOne( $sql );
			
			break;
				
			case "update":
			
				//检查权限
				System :: check_func( 'test-list-mod', false );
			
				$sql="UPDATE `mod:form_option` SET name='".$name."',sort=".$sort.",state=1 WHERE id=".$oid;
				
				System :: $db -> execute( $sql );
				
			break;
				
			case "delete":
			
				//检查权限
				System :: check_func( 'test-list-del', false );
			
				$sql="DELETE FROM `mod:form_option` WHERE id in(".$list.")";
				
				System :: $db -> execute( $sql );
				
			break;
			
			case "state":

				$sql="UPDATE `mod:form_option` SET state=".$state." WHERE id in(".$list.")";
				System :: $db -> execute($sql);				
				
				System :: redirect( $jump ,"选项状态更改成功!");
				
			break;
				
			case "mass":
			
				//检查权限
				System :: check_func( 'test-list-mod', false );
			
				//更新正确值
				$selected = $_POST["selected"];
				
				$sql="UPDATE `mod:form_group` SET selected='".implode(",",$selected)."' WHERE id=".$gid;				
				System :: $db -> execute( $sql );
				
				foreach( $name as $id => $val ){
					$sql="UPDATE `mod:form_option` SET name='".$val."',sort=".intval($sort[$id],0)." WHERE id=".$id;
					System :: $db -> execute( $sql );					
				}
				
			break;
							
		}        
		
		if( $action != "edit" ){
			
			//生成缓存
			Cached :: form( $appid, $fid );
			
			System :: redirect("?do=list&fid=".$fid."&gid=".$gid,"子选项更新成功!");
		}
  	}	
	
	//加载缓存
	Cached :: loader( $appid, 'form.'.$fid );
	?>


	<?php
	
	echo '
    <div class="item">
        <strong>'.$_CACHE[$appid]['form']["name"].' &gt;&gt; '.$_CACHE[$appid]['group'][$gid]["name"].'</strong> 	
    </div>
    
    <ul id="naver">
        <li'.($do=='new'?' class="active"':'').'><a href="?do=new&fid='.$fid.'&gid='.$gid.'">'.($action=="edit"?'编辑':'新增').'子选项</a></li>
        <li'.($do=='list'?' class="active"':'').'><a href="?do=list&fid='.$fid.'&gid='.$gid.'">管理子选项</a></li>
    </ul>
	';
	?>
        
    <div id="box">
    
    <?php 
	if($do=="new"){
	?>
    
    	<form action="?fid=<?php echo $fid;?>&gid=<?php echo $gid;?>" method="post" data-valid="true">

        <table cellpadding="0" cellspacing="0" class="form">
        
        	<tr>
            	<th>目标类型：</th>
                <td>
                <?php
				echo $_G['module']['test']["group"][$_CACHE[$appid]['group'][$gid]["type"]];
				?>
                </td>
            </tr>
            
            <?php
			//编辑状态
			if( $action == "" ){			
			?>
        
        	<tr>
            	<th>批量创建：</th>
                <td>

                	<div id="tag-box">
                    
                    <table id="tag-<?php echo $i;?>" cellpadding="0" cellspacing="1" border="0" class="gird">
                        <tr>
                            <td>
                            	<span class="action"><a onclick="javascript:Planer.remove(this);void(0);">删除</a></span>
                           		名称：
                                <br />
                                <input name="name[]" type="text" class="text" value="<?php echo $key;?>" size="20" data-valid-name="标签名称" data-valid-empty="yes" />
                                <br />
                                排序：<br />
                                <input name="sort[]" type="text" class="text digi" value="<?php echo $val;?>" size="20" data-valid-name="标签排序" data-valid-number="no" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '标签排序' : (function(){ var a = []; for(var i=-9;i<=60;i++){a.push(i);} return a; })() }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true" />
                            </td>
                        </tr>
                    </table>
                    
                    </div>
                
                    <script type="text/javascript">
                    var Planer = new Mo.Planer(Mo.$("tag-box"));
                    </script>
                    
                    <a onclick="javascript:Planer.copy();void(0);">添加</a>
                
                </td>
            </tr>
            
            <?php
			}else{			
			?>
        
        	<tr>
            	<th>标签名称：</th>
                <td><input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="35" data-valid-name="标签名称" data-valid-empty="yes" />
                </td>
            </tr>
        
        	<tr>
            	<th>标签排序：</th>
                <td><input name="sort" type="text" class="text digi" value="<?php echo $row["sort"];?>" size="35" data-valid-name="标签排序" data-valid-number="no" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '标签排序' : (function(){ var a = []; for(var i=-9;i<=60;i++){a.push(i);} return a; })() }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true">
                </td>
            </tr>
            
            <?php
			}			
			?>
        
        	<tr>
            	<td></td>
                <td>
					<?php
                    if ($action=="edit"){
						 echo '
						<input name="action" type="hidden" id="action" value="update" />
						<input name="id" type="hidden" id="id" value="'.$row['id'].'" />
						';
                       echo '<button type="submit" name="Submit" class="submit">修改项目</button>';
                    }else{
						echo '
						<input name="action" type="hidden" id="action" value="add" />
						';
                        echo '<button type="submit" name="Submit" class="submit">新增项目</button>';
                    }
                    ?>
                </td>
            </tr>
        
        </table>
        
	 </form>
	
    <?php
	}elseif($do=="list"){
	?>
    
    <form name="post-form" id="post-form" method="post" action="?fid=<?php echo $fid;?>&gid=<?php echo $gid;?>">
    
    	<input name="action" id="action" type="hidden" value="mass" />
		<input name="state" id="state" type="hidden" value="" />
		
		<input name="jump" type="hidden" value="" />
		<script>Mo('#post-form input[name=jump]').value( location.href );</script>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table" id="table">
        <tr class="thead">
        <td width="10"><input type="checkbox" class="checkbox"></td>
        <td width="50">正确<var data-type="tip">注意：如果没有选择任何子选项，表示任意选项都为正确答案</var></td>
        <td>ID</td>
        <td width="65">排序</td>
        <td>标签名称</td>
        <td>统计</td>
        <td>添加时间</td>
        <td width="80">操作</td>
        </tr>
        <?php
            $sql="SELECT * FROM `mod:form_option` WHERE gid=".$gid." ORDER BY state desc,sort ASC";
            
            $result = System :: $db -> getAll( $sql );
			
            foreach( $result as $row ){
                
                if( $_CACHE[$appid]['group'][$gid]["type"] == 'checkbox' ){
	                $input_type = 'checkbox';
                }else{
	                $input_type = 'radio';
                }
                
                ?>
                
                <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" data-edit="<?php echo $appid;?>.option.php?do=new&action=edit&fid=<?php echo $fid;?>&gid=<?php echo $gid;?>&id=<?php echo $row['id'];?>&jump={self}">
                	<td title="<?php echo $row['id'];?>">
						<input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
					</td>
                    <td>
                        <input name="selected[]" type="<?php echo $input_type;?>" class="<?php echo $input_type;?>" value="<?php echo $row['id'];?>" <?php echo ( in_array( $row['id'], $_CACHE[$appid]['group'][$gid]["selected"] ) ? 'checked' : '' );?> />
                    </td>
                    <td><?php echo $row['id'];?></td>
                    <td><input type="text" class="text" name="sort[<?php echo $row['id'];?>]" size="4" value="<?php echo $row["sort"];?>" /></td>
                    <td><input type="text" class="text text-yes" name="name[<?php echo $row['id'];?>]" size="22" value="<?php echo $row['name'];?>" /></td>
                    <td><?php echo $row["stat"];?></td>
                    <td><?php echo date('y-m-d',$row['dateline']);?></td>
                    <td>
                        <button type="button" class="editor" data-url="?do=new&action=edit&fid=<?php echo $fid;?>&gid=<?php echo $gid;?>&id=<?php echo $row['id'];?>">修改</button>
                            <button type="button" class="normal" data-url="?do=list&action=delete&fid=<?php echo $fid;?>&gid=<?php echo $row["gid"];?>&list=<?php echo $row['id'];?>">删除</button>
                    </td>
                </tr>

                <?php
                
            }
		
			if( count( $result ) == 0 ){
				echo '<tr><td colspan="8" class="notice">没有检索到相关子选项，<a href="?fid='.$fid.'&gid='.$gid.'&do=new">创建一个？</a></td></tr>';
			}
	  
        ?>
        
        <tr class="tfoot">
            <td colspan="8" class="first">
                <span class="y">
                    <button type="submit" class="button">保存修改</button>
                </span>
                <?php echo $_G['module']['test']['tool'];?>
            </td>
        </tr>
        
    </table>
    
    </form>
	
    <?php 
	}
	
        //关闭数据库
        System :: connect();
	?>
    
    </div>
    


<?php html_close();?>