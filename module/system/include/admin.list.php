<?php
	if( !is_array($_G['project']) ){
		ob_clean();
		exit("Forbidden");	
	}
?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table" id="table">
		<tr class="thead">
			<td></td>
			<td>用户名</td>
			<td>用户组</td>
			<td>QQ</td>
			<td>电话</td>
			<td>邮箱</td>
			<td>关联论坛</td>
			<td>最后登录</td>
			<td>状态</td>
			<td width="80">操作</td>
		</tr>
	
		<?php
		
		foreach( $result as $row ){
			
			$row['extra'] = json_decode( $row['extra'], TRUE );
			
			?>
			<tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-edit="admin.edit.php?action=edit&id=<?php echo $row['id'];?>&jump={self}">
			
				<td width="30">
				<?php 
				echo (time()-$row["last_active"]>30?'<a href="?line=off">'.loader_image("icon/offline.png","离线").'</a>':'<a href="?line=on">'.loader_image("icon/online.png","在线")).'</a>';
				?>
				
				<div id="card-<?php echo $row['id'];?>" style="display:none;">
				
					<table border="0" cellpadding="0" cellspacing="0">
					  <tr>
						<td rowspan="2" width="60"><img src="<?php echo fix_thumb( $row["avatar"] );?>" class="avatar" /></td>
						<td><strong><?php echo $row['account'];?></strong></td>
					  </tr>
					  <tr>
						<td>
							<?php echo ($row["gender"]?loader_image("icon/male.png","男士"):loader_image("icon/female.png","女士"));?>
							<?php
							$bdmonth = date("m",strtotime($row["birthday"]));
							$bdday = date("d",strtotime($row["birthday"]));
							
							echo ($bdmonth == date("m") && (date("d")-$bdday<3 || date("d")-$bdday>3)?loader_image("icon/cake.png","生日快到了"):'');
							?>
						</td>
					  </tr>
					</table>
					
					<p>
						用户 I D：<?php echo $row['id'];?><br />
						登录次数：<a href="admin.event.php?s=account&q=<?php echo urlencode($row['account']);?>&event=login"><?php echo $row["stat_login"];?></a><br />
						注册时间：<?php echo date("Y-m-t",$row['dateline']);?>
					</p>
				
				</div>
				
				</td>
				<td id="tips-<?php echo $row['id'];?>" onmouseover='Mo.Tips(this,event,"card",Mo("#card-<?php echo $row['id'];?>").html(),"mouseover",60,0,{"unique":"card"});'>
					<a href="admin.edit.php?action=edit&id=<?php echo $row['id'];?>"><?php echo $row['account'];?></a>
				</td>
				<td>
					<img src="<?php echo VI_BASE;?>static/image/medal/<?php echo "mini_".$_CACHE['system']['group'][$row["gid"]]["medal"];?>" />
					<a href="?gid=<?php echo $row["gid"];?>"><?php echo $_CACHE['system']['group'][$row["gid"]]["name"];?></a>
				</td>
				<td><?php echo $row["qq"];?></td>
				<td><?php echo $row["phone"];?></td>
				<td><?php echo ($row["email"]?loader_image("icon/email.png").' <a href="mailto:'.$row["email"].'">'.$row["email"].'</a>':'');?></td>
				<td>
				<?php if( $row["extra"]["uid"] ){ ?>
				UID：<?php echo $row["extra"]["uid"];?> 用户名：<?php echo $row["extra"]["username"];?>
				<?php
				}
				?>
				</td>
				<td title="<?php echo date("Y-m-d H:i:s",$row["last_login"]);?>"><?php echo ($row["last_login"]?date('y-m-d',$row["last_login"]):'');?></td>
				<td><?php echo $_G['project']['state'][$row['state']];?></td>
				<td>
				<button type="button" class="editor" data-url="admin.edit.php?action=edit&id=<?php echo $row['id'];?>">修改</button>
				<button type="button" class="normal" data-url="?action=delete&id=<?php echo $row['id'];?>">删除</button>
				</td>
			</tr>
			<?php
			
		}
		
		?>
		<tr class="tfoot">
			<td colspan="10">
				
				<?php
				echo loader_image("icon/offline.png");
				?>
				当前离线
				
				<?php
				echo loader_image("icon/online.png");
				?>
				当前在线
			</td>
		</tr>
	
	</table>