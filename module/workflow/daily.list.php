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
				System :: check_func( 'workflow-salary-chk', false );		
			
				System :: $db -> execute("DELETE FROM `mod:workflow_salary` WHERE aid in(".$list.")");
				
				$stat = System :: $db -> getAffectedRows();
				
				$_G['project']['message']="成功删除 ".$stat." 条数据！";
				
				$_G['project']['jump'] = '';
				
			break;
		}
	}
	
	$year = getnum('year',0);
	$month = getnum('month',0);
	
	?>

	
    
    <div id="search">
    
    <form name="find-form" id="find-form" method="get" action="?">
      <span class="action">
        <select name="year" onchange="Mo(this.form).submit();" ignore="true">
        	<optgroup label="年份" />
            <option value="0">全部年份</option>
		<?php
        for( $y = date('Y'); $y > date('Y') - 2; $y -- ){
            echo '<option value="'.$y.'">'.$y.'年</option>';
        }
        ?>
        </select>
        <select name="month" onchange="Mo(this.form).submit();" ignore="true">
        	<optgroup label="月份" />
            <option value="0">全部月份</option>
        <?php
        for( $m = 1; $m <= 12; $m ++ ){
            echo '<option value="'.$m.'">'.$m.'月</option>';
        }
        ?>
        </select>
        
        <select name="owner" id="owner" onchange="Mo(this.form).submit();" ignore="true">
            <optgroup label="所有者" />
            <option value="0">全部人员</option>
            <?php
                foreach( $_CACHE['system']['admin'] as $i => $a ){
                echo '<option value="'.$i.'">'.$a['account'].'</option>';
            }
            ?>
        </select>
        
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
        
        <label>
        <select name="credit" onchange="Mo(this.form).submit();" ignore="true">
            <optgroup label="奖励状态" />
            <option value="">奖励状态</option>
            <?php            
            foreach( $_G['module']['workflow']['credit'] as $key => $val ){
                echo '<option value="'.$key.'">'.$val.'</option>';
            }            
            ?>
        </select>
        </label>
       </span>

        <select name="s" id="s">
            <option value="phone" checked>电话号码</option>
            <option value="appid">模块ID</option>            
            <option value="content">短信内容</option>
            <option value="platform">发送方式</option>
        </select>
        <input name="q" type="text" class="text" title="请输入关键字" id="q" size="15" value="<?php echo $q;?>">
        
        <button class="go" type="submit"></button>
    </form>
    
    </div>
    <script type="text/javascript">
		Mo("select[name=s]").value("<?php echo $s;?>");		
		Mo("select[name=year]").value("<?php echo $year;?>");
        Mo("select[name=month]").value("<?php echo $month;?>");
    </script>
    
    <!--用于动作处理_开始-->
    <form name="post-form" id="post-form" method="post">
        <input name="action" id="action" type="hidden" value="" />        
        <input name="state" id="state" type="hidden" value="" />        
        <input name="jump" type="hidden" value="<?php echo $_G['runtime']['absolute'];?>" />
    
    <?php
    
    $sql="SELECT * FROM `mod:workflow_salary` WHERE 1=1";
    
    if( $q != '' && isset( $s ) ){
        
        if( strpos($s,"id") !== false ){
            $sql.=" and  ".$s." = '".$q."'";
        }else{
            $sql.=" and `".$s."` like '%".$q."%'";
        }     
        
    }

    if ( $year ){
        $sql.=" and  year ='".$year."'";
    }

    if ( $month ){
        $sql.=" and  month ='".$month."'";
    }

    if ( $credit>-1 ){
        $sql.=" and  credit ='".$credit."'";
    }
    
    $sql.=" ORDER BY year DESC, month DESC";
    
    //查询数据库_总记录数
    $row_count = System :: $db -> getCount( $sql, "*" );   

    //分页参数
    $page=getpage("page");
    $page_start=$_G['setting']['global']['pagesize']*($page-1);
    $sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];	
    
    $result = System :: $db -> getAll( $sql );
    
    //分页链接
    $url="?sel=".$sel."&s=".$s."&q=$q&year=$year&month=$month&page=";
    
    ?>
    
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
    
        <tr class="thead">
        	<td width="10"><input type="checkbox" class="checkbox"></td>
            <td>日期</td>
            <td>姓名</td>
            <td>考勤天数</td>
            <td>日志天数</td>
            <td>迟到次数</td>
            <td>备注信息</td>
            <td>记录时间</td>
            <td width="80">操作</td>
        </tr>			
        
        <?php
        
        foreach( $result as $row ){
            
		?>

              <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" data-edit='daily.edit.php?action=edit&year=<?php echo $row['year'];?>&month=<?php echo $row['month'];?>&jump={self}'>
              	<td title="<?php echo $row['id'];?>">
                    <input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
                </td>
                 <td><?php echo $row['year'];?> / <?php echo $row['month'];?></td>
                <td><a href="?s=account&q=<?php echo urlencode($row["account"]);?>"><?php echo $row['account'];?></a></td>
                <td class="text-yes"><?php echo $row['work_days'];?></td>
                <td class="text-no"><?php echo $row['work_diary'];?></td>
                <td class="text-no"><?php echo $row['work_late'] ? $row['work_late'] : '';?></td>
                <td><?php echo $row['work_note'];?></td>
                <td><?php echo date("Y-m-d",$row['dateline']);?></td>
				<td>
					<button type="button" class="editor" data-url="daily.edit.php?action=edit&year=<?php echo $row['year'];?>&month=<?php echo $row['month'];?>">修改</button>
						<button type="button" class="normal" data-url="?action=delete&year=<?php echo $row['year'];?>&month=<?php echo $row['month'];?>&list=<?php echo $row['aid'];?>">删除</button>
				</td>
              </tr>
            
            <?php
              
        }
        
        if( count( $result ) == 0 ){
			echo '<tr><td colspan="9" class="notice">没有检索到相关考勤</td></tr>';
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