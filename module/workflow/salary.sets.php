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

	switch($action){
		
		case "update":
		
			//检查权限
			System :: check_func( 'workflow-salary-mod', false );
			
			$content_multi = getgpc('content_multi');
			$content_pay = getgpc('content_pay');
			$work_pay = getgpc('work_pay');
			$diary_pay = getgpc('diary_pay');
			$post_pay = getgpc('post_pay');
			$meal_pay = getgpc('meal_pay');
			$phone_pay = getgpc('phone_pay');
			$misc_pay = getgpc('misc_pay');
			$bank_card = getgpc('bank_card');
			$entry_time = getgpc('entry_time');
			
			//批量插入
			foreach( $content_pay as $aid => $day ){
				
				$total_pay = array_sum( array( $content_pay[$aid], $work_pay[$aid], $diary_pay[$aid], $post_pay[$aid], $meal_pay[$aid], $phone_pay[$aid], $misc_pay[$aid] ) );
				
				$sql="REPLACE INTO `mod:workflow_staff`(aid,account,`modify`,mender,content_multi,content_pay,work_pay,diary_pay,post_pay,meal_pay,phone_pay,misc_pay,total_pay,bank_card,entry_time) VALUES(".$aid.",'".$_CACHE['system']['admin'][$aid]['account']."',".time().",'".$_G['manager']['account']."','".$content_multi[$aid]."','".$content_pay[$aid]."','".$work_pay[$aid]."','".$diary_pay[$aid]."','".$post_pay[$aid]."','".$meal_pay[$aid]."','".$phone_pay[$aid]."','".$misc_pay[$aid]."','".$total_pay."','".$bank_card[$aid]."','".$entry_time[$aid]."');";
				
				System :: $db -> execute( $sql );
			
			}
			
			System :: redirect('?',"用户信息修改成功!");
			
		break;
		
		case '':
		
			$sql="SELECT * FROM `mod:workflow_staff`";
			$row = System :: $db -> getAll( $sql, 'aid' );
			
		break;
	}
	
	//关闭数据库
	System :: connect();
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
                	本次修改，将在<?php echo $year;?>年 <?php echo $month;?>月生效
				</td>                
			</tr>

			<tr>
				<th>内容基数：</th>
				<td class="text-no text-bold">
                	新帖 <?php echo $_G['setting']['workflow']['thread_radix'];?>个，回复 <?php echo $_G['setting']['workflow']['reply_radix'];?>个
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
						内容倍数
						</td>
						<td>
						内容工资
						</td>
						<td>
						考勤工资
						</td>
						<td>
						技能工资
						</td>
						<td>
						岗位津贴
						</td>
						<td>
						午餐补贴
						</td>
						<td>
						话费补贴
						</td>
						<td>
						其他补贴
						</td>
						<td>
						合计
						</td>
						<td>
						银行账号
						</td>
						<td>
						入职时间
						</td>
					</thead>
                    <?php
					foreach( $_CACHE['system']['admin'] as $adm ){
					?>
					<tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>">
						<td>
							<b class="text-yes"><?php echo $adm["account"];?></b>
						</td>
						<td>
                            <input name="content_multi[<?php echo $adm["id"];?>]" type="text" class="text" data-count="yes" value="<?php echo $row[$adm["id"]]['content_multi'];?>" size="5" data-valid-name="内容工资" data-valid-number="yes" />
						</td>
						<td>
                            <input name="content_pay[<?php echo $adm["id"];?>]" type="text" class="text" data-count="yes" value="<?php echo $row[$adm["id"]]['content_pay'];?>" size="5" data-valid-name="内容工资" data-valid-number="yes" />
						</td>
						<td>
                            <input name="work_pay[<?php echo $adm["id"];?>]" type="text" class="text" data-count="yes" value="<?php echo $row[$adm["id"]]['work_pay'];?>" size="5" data-valid-name="考勤工资" data-valid-number="yes" />
						</td>
						<td>
                            <input name="diary_pay[<?php echo $adm["id"];?>]" type="text" class="text" data-count="yes" value="<?php echo $row[$adm["id"]]['diary_pay'];?>" size="5" data-valid-name="日志工资" data-valid-number="yes" />
						</td>
						<td>
                            <input name="post_pay[<?php echo $adm["id"];?>]" type="text" class="text" data-count="yes" value="<?php echo $row[$adm["id"]]['post_pay'];?>" size="5" data-valid-name="岗位津贴" data-valid-number="yes" />
						</td>
						<td>
                            <input name="meal_pay[<?php echo $adm["id"];?>]" type="text" class="text" data-count="yes" value="<?php echo $row[$adm["id"]]['meal_pay'];?>" size="5" data-valid-name="午餐补贴" data-valid-number="yes" />
						</td>
						<td>
                            <input name="phone_pay[<?php echo $adm["id"];?>]" type="text" class="text" data-count="yes" value="<?php echo $row[$adm["id"]]['phone_pay'];?>" size="5" data-valid-name="话费补贴" data-valid-number="yes" />
						</td>
						<td>
                            <input name="misc_pay[<?php echo $adm["id"];?>]" type="text" class="text" data-count="yes" value="<?php echo $row[$adm["id"]]['misc_pay'];?>" size="5" data-valid-name="其他补贴" data-valid-number="yes" />
						</td>
						<td data-count="yes" class="text-yes text-bold">
                            <?php echo $row[$adm["id"]]['total_pay'];?>
						</td>
						<td>
                            <input name="bank_card[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['bank_card'];?>" size="25" />
						</td>
						<td>
                            <input name="entry_time[<?php echo $adm["id"];?>]" type="text" class="text" value="<?php echo $row[$adm["id"]]['entry_time'];?>" size="10" readonly="true" title="年-月-日" />
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
						echo '
						<input name="action" type="hidden" id="action" value="update" />
						<input name="year" type="hidden" value="'.$year.'" />
						<input name="month" type="hidden" value="'.$month.'" />
						';
						echo '<button type="submit" name="Submit" class="submit">修改此考勤</button>';
					?>
				</td>				
			</tr>
            
        </table>
        
		</form>
	
	</div>
		


<?php html_close();?>