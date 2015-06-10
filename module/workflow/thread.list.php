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
	
	$sel = getgpc("sel");

	$q = getgpc('q');
	$s = getgpc("s");

    $state = getnum('state',-1);
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
				System :: check_func( 'workflow-thread-del', false );		
			
				System :: $db -> execute("DELETE FROM `mod:workflow_thread` WHERE id in(".$list.")");
				
				$stat = System :: $db -> getAffectedRows();
				
				$_G['project']['message']="成功删除 ".$stat." 条数据！";
				
				$_G['project']['jump'] = '';
				
			break;
			
			case "digest":
			
				//检查权限
				System :: check_func( 'workflow-thread-mod', false );
				
				$state = getnum('state',0);
			
				$sql="UPDATE `mod:workflow_thread` SET digest=".$state.",`modify`=".time().",mender='".$_G['manager']['account']."' WHERE first=1 and state>0 and id in(".$list.")";
				
				System :: $db -> execute($sql);
				
				$stat = System :: $db -> getAffectedRows();
				
				System :: redirect($jump,"成功更新 ".$stat." 个精品帖！");

			break;
			
			case "credit":
			
				//检查权限
				System :: check_func( 'workflow-thread-mod', false );
				
				$state = getnum('state',0);
			
				$sql="UPDATE `mod:workflow_thread` SET credit=".$state.",`modify`=".time().",mender='".$_G['manager']['account']."' WHERE first=1 and state>0 and id in(".$list.")";
				
				System :: $db -> execute($sql);
				
				$stat = System :: $db -> getAffectedRows();
				
				System :: redirect($jump,"成功更新 ".$stat." 个奖励帖！");

			break;
		}
	}
	
	$digest = getnum("digest",-1);
	$credit = getnum("credit",-1);
    $first = getnum('first',-1);
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
        <select name="first" onchange="Mo(this.form).submit();" ignore="true">
            <optgroup label="帖子类型" />
            <option value="">帖子类型</option>
            <option value="1">主题</option>
            <option value="0">回复</option>
        </select>
        </label>
        
        <label>
        <select name="state" onchange="Mo(this.form).submit();" ignore="true">
            <optgroup label="帖子状态" />
            <option value="">帖子状态</option>
            <?php            
            foreach( $_G['module']['workflow']['state'] as $key => $val ){
                echo '<option value="'.$key.'">'.$val.'</option>';
            }            
            ?>
        </select>
        </label>
        
        <label>
        <select name="digest" onchange="Mo(this.form).submit();" ignore="true">
            <optgroup label="精品状态" />
            <option value="">精品状态</option>
            <?php            
            foreach( $_G['module']['workflow']['digest'] as $key => $val ){
                echo '<option value="'.$key.'">'.$val.'</option>';
            }            
            ?>
        </select>
        </label>
        
        <!--label>
        <select name="credit" onchange="Mo(this.form).submit();" ignore="true">
            <optgroup label="奖励状态" />
            <option value="">奖励状态</option>
            <?php            
            foreach( $_G['module']['workflow']['credit'] as $key => $val ){
                echo '<option value="'.$key.'">'.$val.'</option>';
            }            
            ?>
        </select>
        </label-->
       </span>

        <select name="s" id="s">
            <option value="subject" checked>帖子标题</option>
            <option value="author">帖子作者</option>
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
		Mo("select[name=first]").value("<?php echo $first;?>");
		Mo("select[name=digest]").value("<?php echo $digest;?>");
		//Mo("select[name=credit]").value("<?php echo $credit;?>");
        Mo("select[name=state]").value("<?php echo $state;?>");
        Mo("select[name=account]").value("<?php echo $account;?>");
    </script>
    
    <!--用于动作处理_开始-->
    <form name="post-form" id="post-form" method="post">
        <input name="action" id="action" type="hidden" value="" />        
        <input name="state" id="state" type="hidden" value="" />        
        <input name="jump" type="hidden" value="<?php echo $_G['runtime']['absolute'];?>" />
    
    <?php
    
    $sql="SELECT * FROM `mod:workflow_thread` WHERE 1=1";
    
    if( $q != '' && isset( $s ) ){
        
        if( strpos($s,"id") !== false ){
            $sql.=" and  ".$s." = '".$q."'";
        }else{
            $sql.=" and `".$s."` like '%".$q."%'";
        }     
        
    }

    if ( $first > -1 ){
        $sql.=" and  first ='".$first."'";
    }

    if ( $digest>-1 ){
        $sql.=" and  digest ='".$digest."'";
    }

    if ( $credit>-1 ){
        $sql.=" and  credit ='".$credit."'";
    }
    
    if ( $state>-1 ){
        $sql.=" and  state ='".$state."'";
    }
    
    if ( $account ){
        $sql.=" and  aid ='".$account."'";
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
    $url="?s=".$s."&q=$q&first=$first&digest=$digest&credit=$credit&account=$account&state=$state&start=$start&end=$end&page=";
    
    ?>
    
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
    
        <tr class="thead">
        	<td width="10"><input type="checkbox" class="checkbox"></td>
            <td>TID</td>
            <td>PID</td>
            <td>标题</td>
            <td>作者</td>
            <td>发帖时间</td>
            <td>入库者</td>
            <td>入库时间</td>
            <td>状态</td>
            <td>是否精品</td>
            <!--td>是否奖励</td-->
            <td width="40">操作</td>
        </tr>			
        
        <?php
        
        foreach( $result as $row ){
            
		?>

              <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" edit_='thread.edit.php?action=edit&id=<?php echo $row['id'];?>&jump={self}'>
              	<td title="<?php echo $row['id'];?>">
                    <input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
                </td>
                <td><?php echo $row["tid"];?></td>
                <td><?php echo $row["pid"];?></td>
                <td>
                <a href="<?php echo Workflow :: thread_url( $row );?>" target="_blank"><?php echo $row['subject'];?></a>
                <?php
                if($row['first']==0){
                    echo '<sup class="text-yes">[回帖]</sup>';
                }
                ?>
                </td>
                <td><a href="?s=author&q=<?php echo urlencode($row["author"]);?>"><?php echo $row['author'];?></a></td>
                <td title="<?php echo date("Y-m-d H:i:s",$row['posttime']);?>"><?php echo date("Y-m-d",$row['posttime']);?></td>
                <td><a href="?account=<?php echo $row["aid"];?>"><?php echo $row["account"];?></a></td>
                <td title="<?php echo date("Y-m-d H:i:s",$row['dateline']);?>"><?php echo date("Y-m-d",$row['dateline']);?></td>
                <td><?php echo $_G['module']['workflow']['state'][$row['state']];?></td>
                <td><a href="?s=digest&q=<?php echo urlencode($row["digest"]);?>"><?php echo $_G['module']['workflow']["digest"][$row["digest"]];?></a></td>
                <!--td><a href="?s=credit&q=<?php echo urlencode($row["credit"]);?>"><?php echo $_G['module']['workflow']["credit"][$row["credit"]];?></a></td-->
		<td>
		<button type="button" class="normal" data-url="?action=delete&list=<?php echo $row['id'];?>">删除</button>
		</td>
              </tr>
            
            <?php
              
        }
        
        if( count( $result ) == 0 ){
			echo '<tr><td colspan="11" class="notice">没有检索到相关帖子</td></tr>';
		}
        
        ?>
        <tr class="tfoot">
        <td colspan="11" class="first">
            <?php echo $_G['module']['workflow']['tool'];?>
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