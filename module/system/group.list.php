<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("用户组管理 - VeryIDE");
?>


	<?php
	
	//loader
	require('include/naver.admin.php');
	
	//菜单处理
	$action = getgpc('action');
	$jump = getgpc('jump');
	
	//连接数据库
	System :: connect();
	
	$id = getnum('id',0);
	$state = getnum('state',0);
	
	if($action){
	
		//动作处理
		switch ($action){
			
			case "state":
			
				//检查权限
				$func = 'system-group-mod';
				System :: check_func( $func, FALSE );
					
				//更新数据
				$sql="UPDATE `sys:group` SET `state`='".$state."' WHERE id=".$id;
				
				System :: $db -> execute( $sql );
	
				if( System :: $db -> getAffectedRows() ){
					
					$_G['project']['message']="成功更改用户组状态!";					
					
					//写入日志
					System :: insert_event($func,time(),time(),"更改用户组状态：".$_CACHE['system']['group'][$id]["name"]);					
					
				}else{
					$_G['project']['message']="未找到指定用户组！";
				}
				
			break;
			
			case "delete":
			
				//检查权限
				$func = 'system-group-del';
				System :: check_func( $func, FALSE );
					
				//删除数据
				$sql="DELETE FROM `sys:group` WHERE id=".$id;
				
				System :: $db -> execute( $sql );
	
				if( System :: $db -> getAffectedRows() ){
					
					$_G['project']['message']="成功删除用户组!";
					
					//写入日志
					System :: insert_event($func,time(),time(),"删除用户组：".$_CACHE['system']['group'][$id]["name"]);
					
				}else{
					$_G['project']['message']="未找到指定用户组!";
				}
				
			break;
			
		}	
			
		//缓存系统用户组
		Cached :: table( 'system', 'sys:group', array( 'jsonde' => array('config'), 'serialize' => array('module','widget') ) );
	
	}

	//search
	$s = getgpc("s");
	$q = getgpc('q');
	?>		
	
    <div id="search">
        <form name="form1" method="get" action="?">
            <span class="action">
            	<?php
				if( System :: check_func( 'system-group-add' ) ){
				?>
                	<button type="button" class="button" onclick="location.href='group.edit.php';">添加分组</button>
                <?php
				}
				?>
                <button type="button" class="button" onclick="if(confirm('确定现在要更新缓存吗？')){location.href='?action=create';}">更新缓存</button>
            </span>
            
             <select name="s" id="s" size="1">
               <option value="name">组名称</option>
               <option value="account">用户名</option>
               <option value="id">ID</option>
            </select>
            <input name="q" type="text" class="text" title="请输入关键字" id="q" value="<?php echo $q;?>">
            <button class="go" type="submit"></button>
        </form>
        
        <script type="text/javascript">
         Mo("select[name=s]").value("<?php echo $s;?>");
        </script>
    </div>

    <?php
    
    $sql="SELECT * FROM `sys:group` WHERE 1=1 ";
    if ($s && $q){
        $sql.=" and  $s like '%".$q."%'";
    }
    
    $sql.=" ORDER BY id DESC";
    
    //查询数据库_总记录数
    $row_count = System :: $db -> getCount( $sql );

    //分页参数
    $page=getpage("page");
    $page_start=$_G['setting']['global']['pagesize']*($page-1);
    $sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];    
    
    //分页链接
    $url="?s=".$s."&q=$q&page=";	
    
    ?>
    
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table" id="table">
      <tr class="thead">
        <td width="20"></td>
        <td>组名</td>
        <td>用户数</td>
        <td>部分用户 <var data-type="tip">仅显示最近加入的 3 位用户</var></td>
        <td>可用模块</td>
        <td>可用工具</td>
        <td>创建者</td>
        <td>权限</td>
        <td>创建时间</td>
        <td>修改时间</td>
        <td>状态</td>
        <td width="80">操作</td>
      </tr>
    
    <?php
    
    $result = System :: $db -> getAll( $sql );

	foreach( $result as $row ){
		
		$users = System :: $db -> getAll("SELECT account FROM `sys:admin` WHERE gid=".$row['id']." ORDER BY id DESC",'account');
		$users = array_keys( $users );
		$count = count( $users );
		$output = array_slice($users, 0, 3);  
		
		//快捷方式
		$module = unserialize( $row['module'] ); 
		
		//小工具
		$widget = unserialize( $row['widget'] );
        
    ?>
        <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-edit="group.edit.php?action=edit&id=<?php echo $row['id'];?>&jump={self}">
            <td title="<?php echo $row['id'];?>"><img src="<?php echo VI_BASE;?>static/image/medal/<?php echo "mini_".$row["medal"];?>" /></td>
            <td><?php echo $row['name'];?></td>
            <td><a href="admin.list.php?s=gid&q=<?php echo $row['id'];?>"><?php echo $count;?></a></td>
            <td class="text-yes"><?php echo implode("，",$output); echo $count>3 ? '等……' : '';?></td>
            <td class="text-yes">
	            <?php
	            $x = 0;
	            foreach( $module as $appid ){
		            echo $_CACHE['system']['module'][$appid]['name']. ( count( $module ) -1 > $x ? '，' : '' );
		            $x++;
	            }
	            ?>
            </td>
            <td class="text-yes">
	            <?php
	            $x = 0;
	            foreach( $widget as $appid => $list ){
		            echo $_CACHE['system']['module'][$appid]['name'] . '（ '. implode( ', ',  $list ) .' ）' . ( count( $widget ) -1 > $x ? '，' : '' );
		            $x++;
	            }
	            ?>
            </td>
            <td><a href="admin.edit.php?action=edit&id=<?php echo $row["aid"];?>"><?php echo $row['account'];?></a></td>
            <td><a href="group.power.php?gid=<?php echo $row['id'];?>" target="_dialog" title="用户组配置 - <?php echo $row['name'];?>" data-width="950" data-height="65%" class="control">查看</a></td>
            <td title="<?php echo date("Y-m-t H:i:s",$row['dateline']);?>"><?php echo date("Y-m-t",$row['dateline']);?></td>
            <td title="<?php echo date("Y-m-t H:i:s",$row['modify']);?>"><?php echo date("Y-m-t",$row['modify']);?></td>
            <td><?php echo $_G['project']['state'][$row['state']];?></td>
            <td>
				<button type="button" class="editor" data-url="group.edit.php?action=edit&id=<?php echo $row['id'];?>">修改</button>
				<button type="button" class="normal" data-url="?action=delete&id=<?php echo $row['id'];?>">删除</button>
            </td>
        </tr>
        
    <?php
        
    }
    
    ?>
    </table>
    <?php
  
	//关闭数据库
	System :: connect();
    
    ?>
    
    <div id="saving">
		<?php
            echo multipage($page,$row_count,$_G['setting']['global']['pagesize'],$url,"page");
        ?>
    </div>
    


<?php html_close();?>