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

		if( in_array($row["type"],$_G['upload']['image']) ){
			$preview = '<img src="'.$row['name'].'" />';	
		}else{
			$preview = '<img src="'.VI_BASE.'static/image/format/file_'.$row["type"].'.png" />';	
		}

		echo '
		<td>
			<div class="box">
				<a class="y" href="'.$row['name'].'" target="_blank">'.loader_image("link.gif","新窗口打开").'</a>
				<span class="preview">
					<a href="'.$row['name'].'" target="_blank">'.$preview.'</a>
				</span>
				<img src="'.VI_BASE.'static/image/format/'.$row["type"].'.gif" class="format" />
				<a href="?v=album&s=account&q='.urlencode($row['account']).'">'.$row['account'].'</a> '.date("Y-m-d",$row['dateline']).'				
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
	
	if( $y == 0 ){
		echo '<td colspan="7" class="notice"><p>没有检索到相关附件</p></td>';
	}
	
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';

?>