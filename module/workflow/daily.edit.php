<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("订单编辑 - VeryIDE");
?>


<?php

	//载入模块配置并生成菜单
	$appid = Module :: get_appid();
	
	echo Module :: get_context( $appid );


	//////////////////////////////////


	
	$jump = getgpc('jump');
	$action = getgpc('action');

	//连接数据库
	System :: connect();

	if($action){
	
		$year = getnum('year',0);
		$month = getnum('month',0);
		$late = getgpc('work_late');
		$days = getgpc('work_days');
		$note = getgpc('work_note');
		$diary = getgpc('work_diary');
		
		switch($action){
			
			case "add":
				
				//检查权限
				System :: check_func( 'workflow-salary-chk', false );
				
				$sql="INSERT INTO `mod:workflow_month`(year,month,aid,account,work_days,work_note,dateline,ip) VALUES('".$year."','".$month."',".$_G['manager']['id'].",'".$_G['manager']['account']."','".$days['month']."','".$note['month']."',".time().",'".GetIP()."')";
				System :: $db -> execute( $sql );
				
				//批量插入
				foreach( $days['staff'] as $aid => $day ){
					
					//考勤或备注必选一项
					if( $day || $note['staff'][$aid] ){
					
						$sql="INSERT INTO `mod:workflow_salary`(year,month,aid,account,work_days,work_note,work_late,work_diary,dateline,ip) VALUES('".$year."','".$month."',".$aid.",'".$_CACHE['system']['admin'][$aid]['account']."','".$day."','".$note['staff'][$aid]."','".$late['staff'][$aid]."','".$diary['staff'][$aid]."',".time().",'".GetIP()."')";
						
						System :: $db -> execute( $sql );
					
					}
				
				}
				
				System :: redirect("daily.list.php?year=".$year."&month=".$month,"考勤提交成功!");
				
			break;
			
			case "edit":
			
				//检查权限
				System :: check_func( 'workflow-salary-chk', false );				
			
				$sql="SELECT work_days, work_note FROM `mod:workflow_month` WHERE year = '".$year."' and month = '".$month."'";
				$mon = System :: $db -> getOne( $sql );
			
				$sql="SELECT aid, work_days, work_diary, work_late, work_note FROM `mod:workflow_salary` WHERE year = '".$year."' and month = '".$month."'";
				$row = System :: $db -> getAll( $sql, 'aid' );
				
				//var_dump( $row );
				
			break;
			
			case "update":
			
				//检查权限
				System :: check_func( 'workflow-salary-chk', false );
				
				$sql="UPDATE `mod:workflow_month` SET `modify`=".time().",mender='".$_G['manager']['account']."',work_days='".$days['month']."',work_note='".$note['month']."' WHERE year = '".$year."' and month = '".$month."'";				
				System :: $db -> execute( $sql );
				
				//批量插入
				foreach( $days['staff'] as $aid => $day ){
					
					$sql="SELECT * FROM `mod:workflow_salary` WHERE year = '".$year."' and month = '".$month."' and aid=".$aid;
					$exist = System :: $db -> getOne( $sql );
					
					if( $exist ){
						
						$sql="UPDATE `mod:workflow_salary` SET `modify`=".time().",mender='".$_G['manager']['account']."',work_days='".$day."',work_note='".$note['staff'][$aid]."',work_late='".$late['staff'][$aid]."',work_diary='".$diary['staff'][$aid]."' WHERE year = '".$year."' and month = '".$month."' and aid=".$aid;
						System :: $db -> execute( $sql );
						
					}else{
						
						//考勤或备注必选一项
						if( $day || $note['staff'][$aid] ){		
						
							$sql="INSERT INTO `mod:workflow_salary`(year,month,aid,account,work_days,work_note,work_late,work_diary,dateline,ip) VALUES('".$year."','".$month."',".$aid.",'".$_CACHE['system']['admin'][$aid]['account']."','".$day."','".$note['staff'][$aid]."','".$late['staff'][$aid]."','".$diary['staff'][$aid]."',".time().",'".GetIP()."')";
							System :: $db -> execute( $sql );
							
						}
						
					}					
				
				}
				
				System :: redirect($jump,"考勤修改成功!");
				
			break;
		}
		
	}
	
	//关闭数据库
	System :: connect();
	?>

	<?php
	
	//显示权限状态
	switch($action){
		case "":
		case "edit":
			echo System :: check_func( 'workflow-salary-chk',true);
		break;
	}
	
	?>

	<div id="box">
		<form method="post" action="?" data-mode="edit" data-valid="true">
        
        <table cellpadding="0" cellspacing="0" class="form">
            
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>

			<tr>
				<th>关联月份：</th>
				<td class="text-no text-bold">
                	<?php
					if( $action == "edit" ){
					?>
                    	<?php echo $year;?>年 <?php echo $month;?>月
                	<?php
					}else{
					?>
                        <select name="year">
                        <?php
                        for( $y = date('Y'); $y > date('Y') - 2; $y -- ){
                            echo '<option value="'.$y.'">'.$y.'年</option>';
                        }
                        ?>
                        </select>
                        <select name="month">
                        <?php
                        for( $m = 1; $m <= 12; $m ++ ){
                            echo '<option value="'.$m.'">'.$m.'月</option>';
                        }
                        ?>
                        </select>
                	<?php
					}
					?>
				</td>                
			</tr>

			<tr>
				<th>上班天数：</th>
				<td>
                	<input name="work_days[month]" type="text" class="text" value="<?php echo $mon['work_days'];?>" size="20" data-valid-name="本月应上班天数" data-valid-number="yes" />
                    本月应上班天数
				</td>                
			</tr>

			<tr>
				<th>本月备注：</th>
				<td><textarea name="work_note[month]" rows="4" cols="87" class="text"><?php echo $mon['work_note'];?></textarea>
				</td>                
			</tr>
			
			<tr>
				<th>用户信息：</th>
				<td>
					
                    <table cellpadding="0" cellspacing="1" border="0" class="frame">
					<thead>
						<td>
						 用户名
						</td>
						<td>
						考勤天数
						</td>
						<td>
						日志数量
						</td>
						<td>
						迟到次数
						</td>
						<td>
						备注信息
						</td>
					</thead>
                    <?php
					
					//当前年份
					$YEAR = date("Y");
					
					//当前年份
					$MONT = date("n");
					
					//月开始和结束时间
					$beg = mktime(0, 0, 0, $MONT, 1, $YEAR);
					$end = mktime(23, 59, 59, $MONT, GetDay( $YEAR, $MONT), $YEAR);

					foreach( $_CACHE['system']['admin'] as $adm ){
						
						//过滤被禁用的用户，并且在本月以前禁用
						if( $adm['state'] == 0 && $adm['modify'] < $beg ) continue;
						
						//var_dump( $adm['modify'] + 0 , $beg, ( $adm['modify'] + 0 ) < $beg );
						//echo '<br />';
					?>
					<tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>">
						<td>
							<b class="text-yes"><?php echo $adm["account"];?></b>
						</td>
						<td>
                            <input name="work_days[staff][<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['work_days'];?>" size="4" data-valid-name="考勤天数" data-valid-number="no" />
						</td>
						<td>
                            <input name="work_diary[staff][<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['work_diary'];?>" size="4" data-valid-name="日志数量" data-valid-number="no" />
						</td>
						<td>
                            <input name="work_late[staff][<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['work_late'];?>" size="4" data-valid-name="迟到次数" data-valid-number="no" />
						</td>
						<td>
                            <input name="work_note[staff][<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['work_note'];?>" size="45" />
						</td>
					</tr>
                    <?php
                    }

					?>
                    
					
				 </table>
                 
                <div class="highlight">
                    注意：考勤天数和备注信息都为空时，将不会纳入本月工资结算</div>
                    
				</td>                
			</tr>
            
			<tr>
				<td></td>
				<td>
					<?php
						if ($action=="edit"){
							echo '
							<input name="action" type="hidden" id="action" value="update" />
							<input name="year" type="hidden" value="'.$year.'" />
							<input name="month" type="hidden" value="'.$month.'" />
							<input name="jump" type="hidden" id="jump" value="'.$jump.'">
							';
							echo '<button type="submit" name="Submit" class="submit">修改此考勤</button>';
						}else{
							echo '<input name="action" type="hidden" id="action" value="add" />';
							echo '<button type="submit" name="Submit" class="submit">新增此考勤</button>';
						}
					?>
				</td>				
			</tr>
            
        </table>
		  
		<script type="text/javascript">
        Mo("input[name=state]").value("<?php echo isset($row['state']) ? $row['state'] : 1;?>");
        Mo("select[name=year]").value("<?php echo isset($row['year']) ? $row['year'] : date('Y');?>");
        Mo("select[name=month]").value("<?php echo isset($row['month']) ? $row['month'] : date('n');?>");
        </script>
        
		</form>
	
	</div>
		


<?php html_close();?>