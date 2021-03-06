<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("广告订单 - VeryIDE");
?>


<?php

	//载入模块配置并生成菜单
	$appid = Module :: get_appid();
	
	echo Module :: get_context( $appid );


	//////////////////////////////////



	//连接数据库
	System :: connect();
	
	//***********************
	
	$q = getgpc('q');
	$s = getgpc("s");

    $state = getnum('state',-2);
	$action = getgpc('action');
	
	//批处理列表
	$list = getgpc('list');
	$list = is_array($list) ? implode(",", $list) : $list;
    
    //上一页
    $jump = $_POST["jump"];
	
	//日期筛选
	$start = getgpc('start');
	$end = getgpc('end');

	if ($action){
	
		switch($action){
        
			//删除
			case "delete":
			
				//检查权限
				System :: check_func( 'workflow-stuff-del', false );		
			
				System :: $db -> execute("DELETE FROM `mod:workflow_asset` WHERE id in(".$list.")");
				
				$stat = System :: $db -> getAffectedRows();
				
				$_G['project']['message']="成功删除 ".$stat." 条数据！";
				
				$_G['project']['jump'] = '';
				
			break;
			
		}
	}
	
	$surplus = getnum("surplus",-1);
	$account = getnum('account',0);
	
	?>

	
    
    <div id="search">
    
    <form name="find-form" id="find-form" method="get" action="?">
      <span class="action">
      	<select name="account" onchange="Mo(this.form).submit();" ignore="true">
            <optgroup label="所有者" />
            <option value="">全部人员</option>
            <?php
			foreach( $_CACHE['system']['admin'] as $i => $a ){
                echo '<option value="'.$i.'">'.$a['account'].'</option>';
            }
            ?>
        </select>
      
        <label>
        <select name="state" onchange="Mo(this.form).submit();" ignore="true">
            <optgroup label="资产状态" />
            <option value="-2">全部资产</option>
            <?php
            foreach($_G['module']['workflow']['asset'] as $key=>$val){
				echo '<option value="'.$key.'">'.$val.'</option>';
			}
            ?>
        </select>
        </label>
       </span>

        <select name="s" id="s">
            <option value="name" checked>资产名称</option>
            <option value="summary">资产摘要</option>
        </select>
        <input name="q" type="text" class="text" title="请输入关键字" id="q" size="15" value="<?php echo $q;?>">
        
        <!--时间节点-->
        <input name="start" type="text" class="date" id="start" value="<?php echo $start;?>" size="10" readonly="true" title="年月日" onchange="Mo(this.form).submit();">
        -
        <input name="end" type="text" class="date" id="end" value="<?php echo $end;?>" size="10" readonly="true" title="年月日" onchange="Mo(this.form).submit();">
        <!--时间节点-->
        
        <button class="go" type="submit"></button>
    </form>
    
    </div>
    <script type="text/javascript">
		Mo("select[name=s]").value("<?php echo $s;?>");		
		Mo("select[name=state]").value("<?php echo $state;?>");
		Mo("select[name=account]").value("<?php echo $account;?>");
    </script>
    
    <!--用于动作处理_开始-->
    <form name="post-form" id="post-form" method="post">
        <input name="action" id="action" type="hidden" value="" />        
        <input name="state" id="state" type="hidden" value="" />        
        <input name="jump" type="hidden" value="<?php echo $_G['runtime']['absolute'];?>" />
    
    <?php
    
    $sql="SELECT * FROM `mod:workflow_asset` WHERE 1=1";
    
    if( $q != '' && isset( $s ) ){
        
        if( strpos($s,"id") !== false ){
            $sql.=" and  ".$s." = '".$q."'";
        }else{
            $sql.=" and `".$s."` like '%".$q."%'";
        }     
        
    }

    if ( $state > -2 ){
        $sql.=" and state ='".$state."'";
    }

    if ( $account ){
        $sql.=" and uid ='".$account."'";
    }
	
	if ($start && $end){
        $sql.=" and  dateline >=".strtotime($start)." and dateline <= ".strtotime($end.' 23:59:59');
    }
    
    $sql.=" ORDER BY id DESC";
    
    //查询数据库_总记录数
    $row_count = System :: $db -> getCount( $sql );   

    //分页参数
    $page=getpage("page");
    $page_start=$_G['setting']['global']['pagesize']*($page-1);
    $sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];	
    
    $result = System :: $db -> getAll( $sql );
    
    //分页链接
    $url="?s=".$s."&q=$q&surplus=$surplus&account=$account&start=$start&end=$end&page=";
    
    ?>
    
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
    
        <tr class="thead">
        	<td width="10"><input type="checkbox" class="checkbox"></td>
        	<td>#</td>
            <td>物品名称</td>
            <td>物品说明</td>
            <td>物品数量</td>
            <td>物品估价</td>
            <td>资产状态</td>
            <td>使用人</td>
            <td>创建人</td>
            <td>创建时间</td>
            <td width="80">操作</td>
        </tr>			
        
        <?php
        
        foreach( $result as $row ){
            
		?>

              <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" data-edit='asset.edit.php?action=edit&id=<?php echo $row['id'];?>&jump={self}' onmouseover='Mo.Tips( this, event, "tips","<img src=\"<?php echo $row["image"];?>\" style=\"max-height:300px\" />","mouseover",-20,30,{ "unique" : "signbar" });'>
              	<td title="<?php echo $row['id'];?>">
                    <input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
                </td>
                <td><?php echo $row['id'];?></td>
                <td>
                <?php
				if( $row["quote"] ){
                    echo '<a href="'.$row["quote"].'" target="_blank">'.$row['name'].'</a> ';
                }else{
					echo $row['name'];
				}
				?>
                </td>
                <td><?php echo $row["summary"];?></td>
                <td class="text-bold text-yes"><?php echo $row['quantity'];?></td>
                <td class="text-no text-bold"><?php echo $row['total'];?></td>
                <td><a href="?state=<?php echo $row["state"];?>"><?php echo $_G['module']['workflow']['asset'][$row['state']];?></a></td>
                <td><a href="?uid=<?php echo $row["uid"];?>" class="text-no text-bold"><?php echo $row['username'];?></a></td>
                
                <td><a href="?account=<?php echo $row["aid"];?>"><?php echo $row['account'];?></a></td>
                <td><?php echo date("Y-m-d",$row['dateline']);?></td>
				<td>
					<button type="button" class="editor" data-url="asset.edit.php?action=edit&id=<?php echo $row['id'];?>">修改</button>
						<button type="button" class="normal" data-url="?action=delete&list=<?php echo $row['id'];?>">删除</button>
				</td>
              </tr>
            
            <?php
              
        }
        
        if( count( $result ) == 0 ){
			echo '<tr><td colspan="11" class="notice">没有检索到相关资产，<a href="asset.edit.php">创建一个？</a></td></tr>';
		}
        
        ?>
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