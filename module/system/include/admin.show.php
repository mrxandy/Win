<?php
	if( !is_array($_G['project']) ){
		ob_clean();
		exit("Forbidden");	
	}
?>
  
<?php

	echo '<table class="files" border="0" cellspacing="1" cellpadding="0">';
	echo '<tbody>';
	echo '<tr>';

	//开始位置
	$x = 1;
	
	//总记录数
	$y = count($result);
	
	//每行数量
	$z = 5;
		
	foreach( $result as $row ){

		if( $row["avatar"] ){
			$preview = '<img src="'.$row["avatar"].'" />';	
		}else{
			$preview = '<img src="'.VI_BASE.'static/image/none.gif" />';	
		}

		echo '
		<td>
			<div class="card">
				<div class="avatar">'.$preview.'</div>
				<ul>
					<li><strong>'.$row['account'].'</strong></li>
					<li>'.($row["gender"]?loader_image("icon/male.png","男士"):loader_image("icon/female.png","女士")).'
				<a href="mailto:'.$row["email"].'">'.($row["email"]?loader_image("icon/email.png",$row["email"]):"").'</a>
				'.(time()-$row["last_active"]>30?'<a href="?v=show&line=off">'.loader_image("icon/offline.png","离线").'</a>':'<a href="?v=show&line=on">'.loader_image("icon/online.png","在线")).'</a>'.'</li>
					<li>'.loader_image("icon/qq.png").'<a href="tencent://message/?uin='.$row["qq"].'" target="_blank">'.$row["qq"].'</a></li>
					<li>'.loader_image("icon/phone.png").''.$row["phone"].'</li>
				</ul>
			</div>
		</td>
		';
		
		//结尾
		if( $x != $y ){			
			if ( $x % $z == 0 ){
				echo "</tr>".chr(13).chr(13)."<tr>";
			}
		}
		
		$x ++;
		
	}
	
	//补空余位	
	for($i=0;$i< ( ceil( ($x-1) / $z) - ($x-1) / $z)* $z ; $i++ ){
		echo '<td></td>';
	}	
	
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';

?>