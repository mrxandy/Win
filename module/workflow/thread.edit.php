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
	$parent = getgpc("parent");
	$action = getgpc('action');

	//连接数据库
	System :: connect();

	if($action){
	
		$id = getgpc("id");
		$state = getnum('state',0);
		
		switch($action){
			
			case "edit":
			
				//检查权限
				System :: check_func( 'sms-mod', false );				
			
				$sql="SELECT * FROM `mod:workflow_thread` WHERE id=".$id;
				$row = System :: $db -> getOne( $sql );
				
			break;
			
			case "update":
			
				//检查权限
				System :: check_func( 'sms-mod', false );				
			
				$sql="UPDATE `mod:workflow_thread` SET `modify`=".time().",`state`='".$state."',account='".$_G['manager']['account']."',aid='".$_G['manager']['id']."' WHERE id=".$id;
				
				System :: $db -> execute( $sql );
				
				System :: redirect($jump,"订单修改成功!");
				
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
			echo System :: check_func( 'sms-mod',true);
		break;
	}
	
	?>

	<div id="box">
		<form method="post" action="?" data-mode="edit" data-valid="true">
        
        <table cellpadding="0" cellspacing="0" class="form">
            
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>

			<tr>
				<th>手机号码：</th>
				<td>
					<b class="text-yes"><?php echo $row["phone"];?></b>
				</td>                
			</tr>
			
			<tr>
				<th>短信内容：</th>
				<td>
					<b class="text-key"><?php echo $row["content"];?></b>
				</td>                
			</tr>
			
			<tr>
				<th>发送时间：</th>
				<td>
					<?php echo date("Y-m-d H:i:s",$row['dateline']);?>
				</td>                
			</tr>
					
			<tr>
				<th>发送方式：</th>
				<td>
					<?php echo $_G['module']['sms']["platform"][$row["platform"]];?>
				</td>                
			</tr>
			
			<tr>
				<th>发送状态：</th>
				<td>
					<?php echo $_G['module']['sms']['state'][$row['state']];?>
				</td>                
			</tr>
            
            		<tr><td colspan="2" class="section"><strong>用户信息</strong></td></tr>
			
			<tr>
				<td></td>
				<td>

				<table cellpadding="0" cellspacing="1" border="0" class="gird">
					<thead>
						<td>
						 UID
						</td>
						<td>
						用户名
						</td>
					</thead>
					<tr>
						<td>
							<b class="text-yes"><?php echo $row["uid"];?></b>
						</td>
						<td>
							<b class="text-key"><?php echo $row["username"];?></b>
						</td>
					</tr>
					
				 </table>

				</td>
			</tr>
			
			<tr><td colspan="2" class="section"><strong>模块关联</strong></td></tr>
			
			<tr>
				<td></td>
				<td>

				<table cellpadding="0" cellspacing="1" border="0" class="frame" width="514">
					<thead>
						<td>
						 APPID
						</td>
						<td>
						FID
						</td>
						<td>
						SID
						</td>
					</thead>
					<tr>
						<td>
							<b class="text-yes"><?php echo $row["appid"];?></b>
						</td>
						<td>
							<b class="text-key"><?php echo $row["fid"];?></b>
						</td>
						<td>
							<b class="text-key"><?php echo $row["sid"];?></b>
						</td>
					</tr>
					
				 </table>

				</td>
			</tr>
            
			<tr>
				<td></td>
				<td>
					<?php
					
						echo '
						<button type="submit" name="Submit5" class="submit">更新订单</button>
						<input name="action" type="hidden" id="action" value="update" />
						<input name="id" type="hidden" id="id" value="'.$row['id'].'">
						<input name="jump" type="hidden" id="jump" value="'.$jump.'">';
					
					echo '<input name="jump" type="hidden" id="jump" value="'.$jump.'" />';
					?>
				</td>				
			</tr>
            
        </table>
		  
		<script type="text/javascript">
        Mo("input[name=state]").value("<?php echo isset($row['state']) ? $row['state'] : 1;?>"); </script>
        
		</form>
	
	</div>
		


<?php html_close();?>