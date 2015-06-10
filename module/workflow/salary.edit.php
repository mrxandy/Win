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
		$days = getgpc('work_days');
		$note = getgpc('work_note');
		$diary = getgpc('work_diary');
		$open_salary = getnum('open_salary',0);
		
		switch($action){
			
			case "edit":
			
				//检查权限
				System :: check_func( 'workflow-salary-mod', false );				
			
				$sql="SELECT work_days, work_note FROM `mod:workflow_month` WHERE year = '".$year."' and month = '".$month."'";
				$mon = System :: $db -> getOne( $sql );
			
				$sql="SELECT * FROM `mod:workflow_staff`";
				$amd = System :: $db -> getAll( $sql, 'aid' );
			
				$sql="SELECT * FROM `mod:workflow_salary` WHERE year = '".$year."' and month = '".$month."'";
				$row = System :: $db -> getAll( $sql, 'aid' );
				
				//var_dump( $row );
				
			break;
			
			case "update":
			
				//检查权限
				System :: check_func( 'workflow-salary-mod', false );
				
				$content_bills = getgpc('content_bills');
				$work_bills = getgpc('work_bills');
				$diary_bills = getgpc('diary_bills');
				$post_bills = getgpc('post_bills');
				$meal_bills = getgpc('meal_bills');
				$phone_bills = getgpc('phone_bills');
				$misc_bills = getgpc('misc_bills');
				$late_bills = getgpc('late_bills');
				$deduct_bills = getgpc('deduct_bills');
				$project_bills = getgpc('project_bills');				
				$debit_bills = getgpc('debit_bills');
				
				$bills_note = getgpc('bills_note');
				
				//批量插入
				foreach( $content_bills as $aid => $day ){
					
					$total = array_sum( array( $content_bills[$aid], $work_bills[$aid], $diary_bills[$aid], $post_bills[$aid], $meal_bills[$aid], $phone_bills[$aid], $misc_bills[$aid], $late_bills[$aid], $deduct_bills[$aid], $project_bills[$aid], $debit_bills[$aid] ) );
					
					$fund_bills = $total / 100 * $_G['setting']['workflow']['fund_ratio'];
					
					$sql="UPDATE `mod:workflow_salary` SET `modify`=".time().",mender='".$_G['manager']['account']."',content_bills='".$content_bills[$aid]."',work_bills='".$work_bills[$aid]."',diary_bills='".$diary_bills[$aid]."',deduct_bills='".$deduct_bills[$aid]."',project_bills='".$project_bills[$aid]."',post_bills='".$post_bills[$aid]."',meal_bills='".$meal_bills[$aid]."',phone_bills='".$phone_bills[$aid]."',misc_bills='".$misc_bills[$aid]."',late_bills='".$late_bills[$aid]."',debit_bills='".$debit_bills[$aid]."',bills_note='".$bills_note[$aid]."',total='".$total."',fund_bills='".$fund_bills."' WHERE year = '".$year."' and month = '".$month."' and aid=".$aid;				
					System :: $db -> execute( $sql );
				
				}
				
				$sql="UPDATE `mod:workflow_month` SET open_salary='".$open_salary."' WHERE year = '".$year."' and month = '".$month."'";				
				System :: $db -> execute( $sql );
				
				System :: redirect('salary.edit.php?action=edit&year='.$year.'&month='.$month.'',"工资修改成功!");
				
			break;
		}
		
	}
	?>

	<?php
	
	if( System :: check_func( 'workflow-salary-mod' ) === FALSE ){
		exit('<div id="state" class="failure">对不起，当前没有权限进行此操作！</div>');
	}
	
	?>
    
    <script>
	function bind_table( id ){
		var tr = Mo( id + ' tr' );
		
		tr.each(function( ){
			
			var tr = this;
			var bx = Mo( 'td[data-count="yes"]', tr );
			var ip = Mo( 'input[data-count="yes"]', tr ).bind('change',function(){
				bx.html( Mo.Array( ip.value() ).sum() );
			});
		
		});
		
	}
	
	Mo.reader( function(){ bind_table( '.frame' ); } );
	</script>

	<div id="box">
		<form method="post" action="?" data-mode="edit" data-valid="true">
        
        <table cellpadding="0" cellspacing="0" class="form">
            
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>

			<tr>
				<th>关联月份：</th>
				<td class="text-no text-bold">
                	<?php echo $year;?>年 <?php echo $month;?>月
				</td>                
			</tr>

			<tr>
				<th>基金比例：</th>
				<td class="text-no text-bold">
                	<?php echo $_G['setting']['workflow']['fund_ratio'];?> %
				</td>                
			</tr>

			<tr>
				<th>上班天数：</th>
				<td class="text-no text-bold">
                	<?php echo $mon['work_days'];?> 天，迟到每次扣款 <?php echo $_G['setting']['workflow']['late_price'];?> 元
				</td>                
			</tr>

			<tr>
				<th>本月备注：</th>
				<td class="text-yes"><?php echo $mon['work_note'];?>
				</td>                
			</tr>

			<tr>
				<th>公开工资：</th>
				<td class="text-yes">
                <label><input type="radio" name="open_salary" value="1" />是，公开本月工资结算</label>
                <label><input type="radio" name="open_salary" value="0" />否</label>
				</td>                
			</tr>
			
			<tr>
				<td colspan="2">
				  
				  <table cellpadding="0" cellspacing="1" border="0" class="frame">
				    <thead>
				      <tr>
				        <td>
				          用户名
			            </td>
				        <td colspan="3">
				          内容统计
			            </td>
				        <td>
				          内容工资
			            </td>
				        <td>
				          出勤工资
			            </td>
				        <td>
				          技能工资
			            </td>
				        <td>
				          提成工资
			            </td>
				        <td>
				          项目工资
			            </td>
				        <td>
				          餐补
			            </td>
				        <td>
				          话补
			            </td>
				        <td>
				          岗位津贴
			            </td>
				        <td>
				          其他补贴
			            </td>
				        <td>
				          迟到扣款
			            </td>
				        <td>其他扣款</td>
				        <td>
				          总计
			            </td>
				        <td>
				          基金
			            </td>
				        <td>
				          实发工资
			            </td>
		            </thead>
				    <?php
					
					//月开始和结束时间
					$beg = mktime(0, 0, 0, $month, 1, $year);
					$end = mktime(23, 59, 59, $month, GetDay( $year, $month), $year);

					foreach( $_CACHE['system']['admin'] as $aid => $adm ){
                        //echo '<label><input type="radio" class="radio" name="level" value="'.$key.'" /> '.$val.'</label>';
						
						$thread = System :: $db -> getValue( "SELECT count(id) FROM `mod:workflow_thread` WHERE aid=".$aid." and first=1 and `state`>0 and dateline Between ".$beg." AND ".$end );			
						$replys = System :: $db -> getValue( "SELECT count(id) FROM `mod:workflow_thread` WHERE aid=".$aid." and first=0 and `state`>0 and dateline Between ".$beg." AND ".$end );
						$digest = System :: $db -> getValue( "SELECT count(id) FROM `mod:workflow_thread` WHERE aid=".$aid." and first=1 and `state`>0 and `digest`>0 and dateline Between ".$beg." AND ".$end );
						
						$zebra = zebra( $i, array( "line" , "band" ) );						
						
						$contribute = System :: $db -> getValue( "SELECT sum(contribute) FROM `mod:workflow_devote` WHERE aid=".$aid." and dateline Between ".$beg." AND ".$end );
								
						//var_dump( $thread >= $_G['setting']['workflow']['thread_radix'] );
						//exit;


					?>
				    <tr>
				      <td rowspan="2" valign="bottom" class="text-yes">
				        <b><?php echo $adm["account"];?></b>
				        </td>
				      <td colspan="3" class="text-no"><?php echo $contribute ? '项目贡献值：'.$contribute : '';?></td>
				      <td>
				        <?php echo $amd[$aid]['content_pay'];?>
				        </td>
				      <td>
				        <?php echo $amd[$aid]['work_pay'];?>
				        </td>
				      <td>
				        <?php echo $amd[$aid]['diary_pay'];?>
				        </td>
				      <td>
				        
				        </td>
				      <td>
				        
				        </td>
				      <td>
				        <?php echo $amd[$aid]['meal_pay'];?>
				        </td>
				      <td>
				        <?php echo $amd[$aid]['phone_pay'];?>
				        </td>
				      <td>
				        <?php echo $amd[$aid]['post_pay'];?>
				        </td>
				      <td>
				        <?php echo $amd[$aid]['misc_pay'];?>
				        </td>
				      <td>
				        </td>
				      <td>&nbsp;</td>
				      <td data-count="yes" class="text-yes text-bold">
				        <?php echo $amd[$aid]['total_pay'];?>
				        </td>
				      <td class="text-yes text-bold">
				        -<?php echo $amd[$aid]['total_pay'] / 100 * $_G['setting']['workflow']['fund_ratio'];?>
				        </td>
				      <td class="text-yes text-bold">
				        <?php echo $amd[$aid]['total_pay'] / 100 * ( 100 - $_G['setting']['workflow']['fund_ratio'] );?>
				        </td>
			        </tr>
                    
				    <tr>
				      <td class="text-no"><?php echo $thread ? '主 '.$thread : '';?></td>
				      <td class="text-no"><?php echo $replys ? '回 '.$replys : '';?></td>
				      <td class="text-no"><?php echo $digest ? '精 '.$digest : '';?></td>
				      <td>
				        <input name="content_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['content_bills'] > 0 ? $row[$adm["id"]]['content_bills'] : Workflow :: content_bills( $thread, $replys, $amd[$aid]['content_pay'], $amd[$aid]['content_multi'] );?>" size="5" />
				        </td>
				      <td>				        
				        <input name="work_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['work_bills'] > 0 ? $row[$adm["id"]]['work_bills'] : Workflow :: general_bills( $mon['work_days'], $row[$adm["id"]]['work_days'], $amd[$aid]['work_pay'] );?>" size="5" /><br />
                        <?php echo $row[$adm["id"]]['work_days'];?> 天
				        </td>
				      <td>				        
				        <input name="diary_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['diary_bills'] > 0 ? $row[$adm["id"]]['diary_bills'] : Workflow :: general_bills( $mon['work_days'], $row[$adm["id"]]['work_diary'], $amd[$aid]['diary_pay'] );?>" size="5" /><br />
                        <?php echo $row[$adm["id"]]['work_diary'];?> 条
				        </td>
				      <td>
				        <input name="deduct_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['deduct_bills'] > 0 ? $row[$adm["id"]]['deduct_bills'] : '';?>" size="5" />
				        </td>
				      <td>
				        <input name="project_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['project_bills'] > 0 ? $row[$adm["id"]]['project_bills'] : '';?>" size="5" />
				        </td>
				      <td>
				        <input name="meal_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['meal_bills'] > 0 ? $row[$adm["id"]]['meal_bills'] : Workflow :: general_bills( $mon['work_days'], $row[$adm["id"]]['work_days'], $amd[$aid]['meal_pay'] );?>" size="5" />
				        </td>
				      <td>
				        <input name="phone_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['phone_bills'] > 0 ? $row[$adm["id"]]['phone_bills'] : Workflow :: general_bills( $mon['work_days'], $row[$adm["id"]]['work_days'], $amd[$aid]['phone_pay'] );?>" size="3" />
				        </td>
				      <td>
				        <input name="post_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['post_bills'] > 0 ? $row[$adm["id"]]['post_bills'] : Workflow :: general_bills( $mon['work_days'], $row[$adm["id"]]['work_days'], $amd[$aid]['post_pay'] );?>" size="3" />
				        </td>
				      <td>
				        <input name="misc_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['misc_bills'] > 0 ? $row[$adm["id"]]['misc_bills'] : Workflow :: general_bills( $mon['work_days'], $row[$adm["id"]]['work_days'], $amd[$aid]['misc_pay'] );?>" size="3" />
				        </td>
				      <td>
				        <input name="late_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['late_bills'] > 0 ? $row[$adm["id"]]['late_bills'] : Workflow :: late_bills( $row[$adm["id"]]['work_late'] );?>" size="3" /><br />
                        <?php echo $row[$adm["id"]]['work_late'];?> 次
				        </td>
				      <td><input name="debit_bills[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['debit_bills'] < 0 ? $row[$adm["id"]]['debit_bills'] : '';?>" size="3" /></td>
				      <td data-count="yes" class="text-no text-bold">
				        <?php echo $row[$adm["id"]]['total'];?>
				        </td>
				      <td class="text-no text-bold">
				        -<?php echo $row[$adm["id"]]['fund_bills'];?>
				        </td>
				      <td class="text-no text-bold">
				        <?php echo $row[$adm["id"]]['total'] - $row[$adm["id"]]['fund_bills'];?>
				        </td>
			        </tr>
                    
				    <tr class="block">
				      <td colspan="18">
				        备注信息：<input name="bills_note[<?php echo $adm["id"];?>]" type="text" class="text text-no text-bold" value="<?php echo $row[$adm["id"]]['bills_note'];?>" size="100" />
				        </td>
			        </tr>
                    
				    <?php
                    }

					?>
				    
			      </table>
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
        Mo("input[name=open_salary]").value("<?php echo isset($row['open_salary']) ? $row['open_salary'] : 0;?>");
        </script>
        
		</form>
	
	</div>
	
    <?php
		
	//关闭数据库
	System :: connect();
	?>


<?php html_close();?>