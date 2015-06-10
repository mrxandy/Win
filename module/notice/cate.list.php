<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("通知分类列表 - VeryIDE");
?>


<?php

	//载入模块配置并生成菜单
	$appid = Module :: get_appid();
	
	echo Module :: get_context( $appid );

	//载入 GEOHASH
	require VI_ROOT.'source/class/geohash.php';

	//////////////////////////

	$q=getgpc('q');
	$s=getgpc("s");
	
	$list=getgpc('list');
	$list = is_array($list) ? implode(",", $list) : $list;
	
	$state=getnum("state",1);
	$order=getgpc("order");
	
	$action=getgpc('action');

	//连接数据库
	System :: connect();

	if ($action){
	
		switch($action){
			//删除
			case "delete":
			
				//检查权限
				System :: check_func( 'notice-cate-del', false );		

				$id = getnum('id',0);
				
				$id && System :: $db -> execute("DELETE FROM `mod:common_category` WHERE id in(".$id.") or parent in(".$id.")");
				
				$_G['project']['message']="分类删除成功！";
			
			break;
			
			//修改
			case "update":
			
				//检查权限
				System :: check_func( 'notice-cate-mod', false );
			
				$name = getgpc('name');
				$sort = getgpc("sort");
				$mark = getgpc("mark");
			
				foreach( $name as $id => $val ){			
					$sql="UPDATE `mod:common_category` SET name='".$val."',sort='".intval( $sort[$id], 0 )."',mark='".$mark[$id]."' WHERE id=".$id;					
					System :: $db -> execute( $sql );
				}
				
			break;
			
			//状态
			case "create":
			
				//统计分类
				$sql="SELECT id,parent,ping,config FROM `mod:common_category` WHERE appid = 'notice-cate' ";
				
				$result = System :: $db -> getAll( $sql );

       			foreach( $result as $row ){
       			
       				//将原来的点评设置（ping）全并至 config 字段，只执行一次
       				if( $row['ping'] ){
       				
       					$conf = array();
	       				$conf['review'] = unserialize( $row['ping'] );
	       				$conf['extend'] = unserialize( $row['config'] );
       				
	       				$sql="UPDATE `mod:common_category` SET config = '". serialize( $conf ) ."', ping = '' WHERE id=".$row['id'];
	       				//var_dump( $sql );
	       				//echo '<br />';
	       									
						System :: $db -> execute( $sql );
       				}
       			
       				///////////////////
					
					$sql="UPDATE `mod:common_category` SET stat = ( SELECT count(id) FROM `mod:notice_list` WHERE state > 0 and `". ( $row['parent'] == 0 ? 'parent' : 'type' ) ."` = ".$row['id']." and master < 2 and ( ( vip>0 and biz_start < ".time()." and biz_expire > ".time()." ) or vip = 0 ) ) WHERE id=".$row['id'];					
					System :: $db -> execute( $sql );
					
				}
			
				$_G['project']['message']="缓存更新成功！";
				
			break;
			
			case "state":
			
				//检查权限
				System :: check_func( 'notice-cate-mod', false );		
				
				$sql="UPDATE `mod:common_category` SET state=".$state." WHERE id in(".$list.")";
				
				System :: $db -> execute( $sql );
				
				$_G['project']['message']="分类修改成功！";
				
			break;
			
		}		
		
		//更新分类数组缓存
		Cached :: multi($appid,"SELECT id,name,mark,title,parent,skin,mode,stat,config,ping,state,bind,link FROM `mod:common_category` WHERE appid = 'notice-cate' ORDER BY sort ASC","table.category", array( 'alias'=>'category', 'serialize' => array('config','ping') ) );
		
		//更新分类脚本缓存
		Cached :: script($appid,"SELECT id,name,parent,config FROM `mod:common_category` WHERE appid = 'notice-cate' ORDER BY sort ASC","CATEGORY","mod.category",array( 'serialize'=>array('config'), 'charset' => 'utf-8', 'unicode' => TRUE ));
		
		//转到
		if( $jump ){
			System :: redirect($jump,"成功能更新缓存!");
		}
	}
	
?>
        <div id="search">
		
	    <form name="find-form" id="find-form">

          <span class="action">
            <button type="button" class="button" onclick="if(confirm('确定现在更新分类缓存吗？')){location.href='?action=create';}">更新缓存</button>
           </span>

			<select name="s" id="s">
                <option value="name" checked> 名称</option>
                <option value="title"> 标题</option>
                <option value="id"> ID</option>
                <option value="mark"> 标识</option>
            </select>
		    <input name="q" type="text" class="text" title="请输入关键字" id="q" size="15" value="<?php echo $q;?>">
            
            <button class="go" type="submit"></button>
        </form>
        </div>
 
		<form action="?" name="post-form" id="post-form" method="post" data-valid="true">
	        <input name="action" id="action" type="hidden" value="update" />
	        <input name="state" id="state" type="hidden" value="" />
		
		<?php		
		//echo $mysql;
		
		$sql="SELECT * FROM `mod:common_category` WHERE parent=0 and appid = '$appid'";
		if ($q){
			$sql.=" and `".$s."` like '%".$q."%'";
		}
		

		if( $q != '' && isset( $s ) ){
			if( strpos($s,"id") !== false ){
				$sql.=" and  `".$s."` = '".$q."'";
			}else{
				$sql.=" and `".$s."` like '%".$q."%'";
			}        
		}
		
		//排序
		$sql.=" ORDER BY sort ASC,id DESC";
		
		//查询数据库_总记录数
		$row_count = System :: $db -> getCount( $sql );
		
		//分页链接
		$url="?q=".$q."&s=".$s."&page=";
		
		
		?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
			  <tr class="thead">
			  	<td width="10"><input type="checkbox" class="checkbox"></td>
				<td width="55">排序 <var data-type="tip">从小到大依次排序</var></td>
				<td>分类名称</td>
				<td>有效商家</td>
				<td>页面标题</td>				
				<td>展示模式</td>
				<td>标识</td>
				<td>皮肤</td>
				<td>绑定行业</td>
				<td>点评</td>
				<td>相册</td>
				<td>套餐</td>
				<td>链接</td>
				<td>状态</td>
				<td width="80">操作</td>
			  </tr>
			<?php
				
			$result = System :: $db -> getAll( $sql );
			
			foreach( $result as $row ){
				
				?>
				  <tr class="entry" data-mark="<?php echo $row['id'];?>" data-edit="cate.edit.php?action=edit&id=<?php echo $row['id'];?>&jump={self}">
				  	<td title="<?php echo $row['id'];?>">
		                <input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
		            </td>
					<td>
					<input name="sort[<?php echo $row['id'];?>]" type="text" class="text" size="2" value="<?php echo $row["sort"];?>" data-valid-name="分类排序" data-valid-number="yes" />
					</td>
                    <td title="<?php echo $row['name'];?>">
					<input name="name[<?php echo $row['id'];?>]" type="text" class="text" value="<?php echo $row['name'];?>" data-valid-name="分类标题" data-valid-empty="yes" />
                    </td>
                    <td><?php echo $row["stat"];?></td>
					<td><?php echo format_substr($row["title"],30);?></td>
					<td></td>					
					<td><input name="mark[<?php echo $row['id'];?>]" type="text" class="text" size="5" value="<?php echo $row['mark'];?>" /></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
                    <td><?php echo $_G['project']['state'][$row['state']];?></td>
			        <td align="center">
                		<button type="button" class="editor" data-url="cate.edit.php?action=edit&id=<?php echo $row['id'];?>">修改</button>
						<button type="button" class="normal" data-url="cate.list.php?action=delete&id=<?php echo $row['id'];?>">删除</button>
            		</td>
				  </tr>
                  
                  <?php
				//子菜单
				$sql="SELECT * FROM `mod:common_category` WHERE parent=".$row['id']." ORDER BY sort ASC,id DESC";
				$subres = System :: $db -> getAll( $sql );
				
				foreach( $subres as $sub ){
				
					$sub['conifg'] = unserialize( $sub['config'] );
					
			?>
                    <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $sub['id'];?>" data-edit="cate.edit.php?action=edit&id=<?php echo $sub["id"];?>&jump={self}">
					  	<td title="<?php echo $sub['id'];?>">
			                <input name="list[]" type="checkbox" class="checkbox" value="<?php echo $sub['id'];?>">
			            </td>
                        <td><input type="text" class="text" name="sort[<?php echo $sub["id"];?>]" size="2" value="<?php echo $sub["sort"];?>" data-valid-name="分类排序" data-valid-number="yes" /></td>
                        <td><?php echo loader_image("tree.gif");?> <input type="text" class="text" name="name[<?php echo $sub["id"];?>]" size="14" value="<?php echo $sub["name"];?>" data-valid-name="分类名称" data-valid-empty="yes" /></td>
                        <td><a href="notice.list.php?type=<?php echo $sub["id"];?>"><?php echo $sub["stat"];?></a></td>
                        <td><?php echo format_substr($sub["title"],30);?></td>                            
                        <td><?php echo $_G['module']['notice']["mode"][$sub["mode"]];?></td>
                        <td></td>
                        <td><?php echo $sub["skin"] ? '<img src="content/skins/'.$sub["skin"].'/preview.gif" style="width:16px;" /> ' : '';?></td>
                        <td><strong class="text-yes"><?php echo $sub["bind"];?></strong></td>
                        <td><strong class="text-yes"><?php echo $sub['conifg']['review']['open'] ? '开启' : '';?></strong></td>
                        <td><strong class="text-yes"><?php echo $sub['conifg']['photo']['open'] ? '开启' : '';?></strong></td>
                        <td><strong class="text-yes"><?php echo $sub['conifg']['price']['open'] ? '开启' : '';?></strong></td>
                        <td><?php echo format_url($sub["link"],20,'...');?></td>
                        <td><?php echo $_G['project']['state'][$sub['state']];?></td>
                        <td align="center">                                            
							<button type="button" class="editor" data-url="cate.edit.php?action=edit&id=<?php echo $sub["id"];?>">修改</button>
                            <button type="button" class="normal" data-url="cate.list.php?action=delete&id=<?php echo $sub["id"];?>">删除</button>
                        </td>
					</tr>
                  <?php
				}

			//}
			  
			  
			  ?>
                  
                  
			<?php
			}
			
			if( count( $result ) == 0 ){
				echo '<tr><td colspan="16" class="notice">没有检索到相关分类，<a href="cate.edit.php">创建一个？</a></td></tr>';
			}else{
				echo '<tr class="tfoot"> <td colspan="16"><div class="y"><button class="button" type="submit">保存更改</button></div>'.$_G['module']['notice']['cate'].' </td> </tr>';
			}
			
			?>
                </table>
				
	<?php

        //关闭数据库
        System :: connect();
	?>
				
	</form>

<?php html_close();?>