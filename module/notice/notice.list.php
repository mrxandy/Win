<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("通知列表 - VeryIDE");
?>


<?php

	//载入模块配置并生成菜单
	$appid = Module :: get_appid();
	
	echo Module :: get_context( $appid );

	//////////////////////////////////



	$q = getgpc('q');
	$s = getgpc("s");	
	
    //批处理列表
    $list = getgpc('list');
    $list = is_array($list) ? implode(",", $list) : $list;

    //上一页
    $jump = $_POST["jump"];

	$state = getnum("state",1);
	$order = getgpc("order");
	$action = getgpc('action');
	
	//连接数据库
	System :: connect();

	if ($action){
	
		switch($action){
			//删除
			case "delete":
			
				//检查权限
				System :: check_func( 'notice-list-del', false );
				
				//直接删除不经过回收站
				if( System :: check_func( 'system-recycle' ) ){
					
					System :: $db -> execute("DELETE FROM module_notice WHERE id in(".$list.")");			
					
					//删除缓存
					$array = explode(",",$list);
					foreach( $array as $id ){
						Cached :: delete( $appid, 'form.'.$id.'.php' );
					}
					
					//清空统计
					Module :: loader( 'analytic' );
					Analytic :: clear( $appid, $list );				
					
					//记录日志										
					System :: insert_event("system-recycle",time(),time(),"删除数据：$appid (".$list.")");
					
				}else{
				
					$sql="UPDATE module_notice SET `state`=-1,`modify`=".time().",mender='".$_G['manager']['account']."' WHERE appid='".$appid."' and id in(".$list.")";
					System :: $db -> execute($sql);
					
					//生成缓存
					Cached :: form( $appid, $list );
					
				}
				
				System :: redirect($appid.".list.php","表单数据删除成功!");
				
			break;
			
			//状态
			case "create":
			
				//生成缓存
				Cached :: form( $appid, $list );
				
				System :: redirect($jump,"表单缓存更新成功!");
				
			break;
			
			case "state":
			
				//检查权限
				System :: check_func( 'notice-list-mod', false );	
				
				$state = getnum('state',0);
			
				$sql="UPDATE module_notice SET state=".$state.",`modify`=".time().",mender='".$_G['manager']['account']."' WHERE appid='".$appid."' and id in(".$list.")";
				System :: $db -> execute($sql);

				//生成缓存
				Cached :: form( $appid, $list );
				
				System :: redirect($jump,"表单状态更改成功!");

			break;
		}
	}
    
    ?>

    <div id="search">
    
    <form name="form1" method="get" action="?">
        <span class="action">
            <button type="button" class="cancel" onclick="location.href='?s=account&q=<?php echo urlencode($_G['manager']['account']);?>';">我的通知</button>
            <button type="button" class="button" onclick="location.href='<?php echo $appid;?>.edit.php';">新增通知</button>
            <button type="button" class="button" onclick="if(confirm('确定现在要更新缓存吗？')){location.href='?action=create';}">更新缓存</button>
        </span>
      
        <select name="s" id="s">
            <option value="name" checked> 标题</option>
            <option value="id"> ID</option>
            <option value="account"> 作者</option>
			<option value="mender"> 修改人</option>
            <option value="description"> 内容</option>
            <option value="tags"> 标签</option>
            <option value="category"> 分类</option>
        </select>
        <input name="q" type="text" class="text" id="q" value="<?php echo $q;?>">
        
        <select id="order" name="order">
            <option value="">所有状态</option>
            <option value="normal">正常状态</option>
            <option value="expire">过期表单</option>
        </select>
        
        <button class="go" type="submit"></button>
        
    </form>
    </div>
    
    <!--用于动作处理_开始-->
    <form name="post-form" id="post-form" method="post">
        <input name="action" id="action" type="hidden" value="" />        
        <input name="state" id="state" type="hidden" value="" />
        
        <input name="jump" type="hidden" value="<?php echo $_G['runtime']['absolute'];?>" />
    
    <?php		
    $sql="SELECT * FROM module_notice WHERE appid='".$appid."' ";

    if( $q != '' && isset( $s ) ){    
        if( strpos($s,"id") !== false ){
            $sql.=" and  `".$s."` = '".$q."'";
        }else{
            $sql.=" and `".$s."` like '%".$q."%'";
        }        
    }
	
	//如果没有权限，则限制为本人
	if( System :: check_func( 'notice-other') == false ) $sql.=" and aid = ".$_G['manager']['id'];
	
	//显示回收站
	if( System :: check_func( 'system-recycle' ) == false ) $sql.=" and state > -1 ";
    
    //排序
    $extra = getgpc('extra');
    if( $extra ){		
    	$sql.=" ORDER BY ". $extra['field'] ." ". $extra['order'] ."";		
    }else{
    	$sql.=" ORDER BY id DESC";
    }
    
    //查询数据库_总记录数
    $row_count = System :: $db -> getCount( $sql );   

    //分页参数
    $page=getpage("page");
    $page_start=$_G['setting']['global']['pagesize']*($page-1);
    $sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];	
    
    //分页链接
    $url="?q=".$q."&s=".$s. format_get( 'extra', $extra ) ."&page=";    
    
    $result = System :: $db -> getAll( $sql );
	
    ?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
      
        <tr class="thead">
            <td width="10"><input type="checkbox" class="checkbox"></td>
            <td>分类</td>
            <td>标题</td>
            <td>发布者</td>
            <td>修改者</td>
            <td>图示</td>
            <td data-sort-field="expire">起止时间</td>
            <td data-sort-field="dateline">创建时间</td>
            <td data-sort-field="state">状态</td>
            <td width="80">操作</td>
        </tr>
      
    <?php
    
    foreach( $result as $row ){
        
        //配置
        $config = fix_json( $row['config'] );
        
        ?>
          <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" data-edit="<?php echo $appid;?>.edit.php?action=edit&fid=<?php echo $row['id'];?>&jump={self}">
            <td title="<?php echo $row['id'];?>">
                <input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
            </td>
            <td><a href="?s=category&q=<?php echo rawurlencode($row['category']);?>"><?php echo $row['category'];?></a></td>
            <td title='<?php echo $row['title'];?>'>
                <a href="content/?id=<?php echo $row['id'];?>" target="_blank"><?php echo format_substr($row['title'],30,"...");?></a>
                <?php
                if($row['expire']<time()){
                    echo '<sup class="text-no">[过期]</sup>';
                }
                if($row['state']==-1){
                    echo '<sup class="text-yes">[回收站]</sup>';
                }
                ?>
            </td>
            <td>
                <a href='?s=account&q=<?php echo urlencode($row['account']);?>'><?php echo $row['account'];?></a>
            </td>
            <td>
                <a href='?s=mender&q=<?php echo urlencode($row['mender']);?>'><?php echo $row['mender'];?></a>
            </td>
            <td>
                <img src="<?php echo $row['thumb'];?>" alt="<?php echo $row['title'];?>" width="100" height="50">
            </td>
            <td>
                <?php
                if($row['start']>time()){
                    echo '<span class="text-no">'.date("y/m/d",$row['start']).'</span>';
                }else{
                    echo '<span class="text-yes">'.date("y/m/d",$row['start']).'</span>';
                }
                
                ?>
                -
                <?php
                
                if($row['expire']<time()){
                    echo '<span class="text-no">'.date("y/m/d",$row['expire']).'</span>';
                }else{
                    echo '<span class="text-yes">'.date("y/m/d",$row['expire']).'</span>';
                }
                ?>
            </td>
            <td title="<?php echo date("Y-m-d H:i:s",$row['dateline']);?>"><?php echo date("y-m-d",$row['dateline']);?></td>
            <td><?php echo $_G['project']['state'][$row['state']];?></td>
            <td>
                <button type="button" class="editor" data-url="<?php echo $appid;?>.edit.php?action=edit&fid=<?php echo $row['id'];?>">修改</button>
                    <button type="button" class="normal" data-url="<?php echo $appid;?>.list.php?action=delete&list=<?php echo $row['id'];?>">删除</button>
            </td>
            </tr>
        <?php
        }
        
		if( count( $result ) == 0 ){
			echo '<tr><td colspan="15" class="notice">没有检索到相关投票，<a href="'.$appid.'.edit.php">创建一个？</a></td></tr>';
		}
        ?>
        
            <tr class="tfoot">
                <td colspan="15">
                    
	                <div class="y">
	                    <?php
	                    echo loader_image("icon/color.png");
	                    ?>
	                    自定义
	                    
	                    <?php
	                    echo loader_image("icon/user.png");
	                    ?>
	                    注册用户
	                    
	                    <?php
	                    echo loader_image("icon/shield.png");
	                    ?>
	                    IP 验证
	                </div>
                
                    <?php echo $_G['module']['vote']['tool'];?>
                    
                </td>
            </tr>
        </table>
    <?php
  
	//关闭数据库
	System :: connect();
    
    ?>
    </form>
    <!--用于动作处理_结束-->
    
    <div id="saving">
		<?php
            echo multipage($page,$row_count,$_G['setting']['global']['pagesize'],$url,"page");
        ?>
    </div>

<?php html_close();?>