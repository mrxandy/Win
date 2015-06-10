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
				System :: check_func( 'workflow-salary-del', false );		
			
				System :: $db -> execute("DELETE FROM `mod:workflow_salary` WHERE aid in(".$list.")");
				
				$stat = System :: $db -> getAffectedRows();
				
				$_G['project']['message']="成功删除 ".$stat." 条数据！";
				
				$_G['project']['jump'] = '';
				
			break;
		}
	}
	
	$year = getnum('year',0);
	$month = getnum('month',0);
	$account = getnum('account',0);
	
	//没有权限时，仅可以看自己的
	if( System :: check_func( 'workflow-salary-mod' ) === FALSE ){
		$account = $_G['manager']['id'];
	}
	
	?>

	
    
    <div id="search">
    
    <form name="find-form" id="find-form" method="get" action="?">
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
        
        <select name="account" onchange="Mo(this.form).submit();" ignore="true">
            <optgroup label="所有者" />
            <option value="0">全部人员</option>
            <?php
                foreach( $_CACHE['system']['admin'] as $i => $a ){
                echo '<option value="'.$i.'">'.$a['account'].'</option>';
            }
            ?>
        </select>
    </form>
    
    </div>
    <script type="text/javascript">
		Mo("select[name=s]").value("<?php echo $s;?>");		
		Mo("select[name=year]").value("<?php echo $year;?>");
        Mo("select[name=month]").value("<?php echo $month;?>");
        Mo("select[name=account]").value("<?php echo $account;?>");
    </script>
    
    <!--用于动作处理_开始-->
    <form name="post-form" id="post-form" method="post">
        <input name="action" id="action" type="hidden" value="" />        
        <input name="state" id="state" type="hidden" value="" />        
        <input name="jump" type="hidden" value="<?php echo $_G['runtime']['absolute'];?>" />
    
    <?php
    
	$sql="SELECT CONCAT( year, '_', month ) as date , open_salary FROM `mod:workflow_month`";
	$mon = System :: $db -> getAll( $sql, 'date' );
	
	//var_dump( $month );
	
	////////////////////////
	
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

    if ( $account ){
        $sql.=" and  aid ='".$account."'";
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
            <td>内容工资</td>
            <td>出勤工资</td>
            <td>提成工资</td>
            <td>项目工资</td>
            <td>技能工资</td>
            <td>午餐补贴</td>
            <td>话费补贴</td>
            <td>岗位津贴</td>
            <td>其他补贴</td>
            <td>迟到扣款</td>
            <td>其他扣款</td>
            <td>总计</td>
            <td>员工基金</td>
            <td>实发工资</td>
            <td width="80">操作</td>
        </tr>			
        
        <?php
        
        foreach( $result as $row ){
			
			if( $mon[ $row['year'].'_'.$row['month'] ]['ignore'] ) continue;
			
			if( $mon[ $row['year'].'_'.$row['month'] ]['open_salary'] == '1' || System :: check_func( 'workflow-salary-mod') ){
				
				$bid = $row['year'].'_'.$row['month'].'_'.$row['aid'];
            
		?>

              <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" onclick="Mo('#block_<?php echo $bid;?>').toggle();" data-mark="<?php echo $row['id'];?>" data-edit='salary.edit.php?action=edit&year=<?php echo $row['year'];?>&month=<?php echo $row['month'];?>&jump={self}'>
              	<td title="<?php echo $row['id'];?>">
                    <input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
                </td>
                 <td><?php echo $row['year'];?> / <?php echo $row['month'];?></td>
                <td><?php echo $row['account'];?></td>
                <td><?php echo $row['content_bills'];?></td>
                <td><?php echo $row['work_bills'];?></td>
                <td><?php echo $row['deduct_bills'];?></td>
                <td><?php echo $row['project_bills'];?></td>
                <td><?php echo $row['diary_bills'];?></td>
                <td><?php echo $row['meal_bills'];?></td>
                <td><?php echo $row['phone_bills'];?></td>
                <td><?php echo $row['post_bills'];?></td>
                <td><?php echo $row['misc_bills'];?></td>
                <td><?php echo $row['late_bills'];?></td>
                <td class="text-no" title="<?php echo $row['bills_note'];?>"><?php echo $row['debit_bills'];?></td>
                <td class="text-yes"><?php echo $row['total'];?></td>
                <td class="text-yes">-<?php echo $row['fund_bills'];?></td>
                <td class="text-no text-bold"><?php echo $row['total'] - $row['fund_bills'];?></td>
                <td>
					<button type="button" class="editor" data-url="salary.edit.php?action=edit&year=<?php echo $row['year'];?>&month=<?php echo $row['month'];?>">修改</button>
						<button type="button" class="normal" data-url="?action=delete&year=<?php echo $row['year'];?>&month=<?php echo $row['month'];?>&list=<?php echo $row['aid'];?>">删除</button>
				</td>
              </tr>
              
              <tr id="block_<?php echo $bid;?>" style="display:none;">
              	<td colspan="18" class="block">          
                <strong>考勤备注：</strong><?php echo nl2br($row['work_note']);?>
                <br />
                <strong>工资备注：</strong><?php echo nl2br($row['bills_note']);?>
                </td>
              </tr>
            
            <?php
			
			}else{
				
				$mon[ $row['year'].'_'.$row['month'] ]['ignore'] = TRUE;
			
				echo '<tr><td colspan="18" class="notice">本月（'. $row['year'].'年/'.$row['month'] .'月）工资结算还没完成，请耐心等待（<a href="salary.edit.php?action=edit&year='.$row['year'].'&month='.$row['month'].'">管理入口</a>）</td></tr>';
			
			}
              
        }
        
        ?>
    </table>
            
	<?php
	
	//查询数据库_总记录数
    $count = System :: $db -> getOne( str_replace( '*', 'sum(total) as x, sum(fund_bills) as y, sum(total) - sum(fund_bills) as z', $sql ) ); 

	//关闭数据库
	System :: connect();
			
	?>
    </form>
    <!--用于动作处理_结束-->
    
    <div id="saving">
    	<span class="y">
        总计：<span class="text-key"><?php echo $count['x'];?></span>
        员工基金：<span class="text-key"><?php echo $count['y'];?></span>
        实发工资：<span class="text-key"><?php echo $count['z'];?></span>
        </span>
		<?php
            echo multipage($page,$row_count,$_G['setting']['global']['pagesize'],$url,"page");
        ?>
    </div>

<?php html_close();?>