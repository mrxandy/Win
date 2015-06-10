<?php
	if( !is_array($_G['project']) ){
		ob_clean();
		exit("Forbidden");	
	}
?>
  
<?php

	echo '
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr class="thead">
		<td width="10"><input type="checkbox" class="checkbox"></td>
		<td>文件名</td>
		<td>用户</td>
		<td>来源</td>
		<td>大小</td>
		<td>位置</td>
		<td>尺寸</td>
		<td>上传时间</td>
		<td width="40">操作</td>
	</tr>
	';

	foreach( $result as $row ){
		
		/*
		if( $row["remote"] ){
			$link = url_merge(str_replace(FTP_ROOT,FTP_SITE,$row['name']));	
			$name = str_replace(FTP_ROOT,'',$row['name']);
		}else{
			$link = (VI_BASE.$row['name']);
			$name = $row['name'];
		}
		*/
		
		if( $row["remote"] ){
			$link = url_merge(str_replace(FTP_ROOT,FTP_SITE,$row['name']));	
			$name = str_replace(FTP_ROOT,'',$row['name']);
		}else{
			$name = $link = $row['name'];
		}

		echo ("<tr class='".zebra( $i, array( "line" , "band" ) )."'>");
		
		echo "<td><input name='id' type='checkbox' class='checkbox' id='id' value='".$row['id']."'></td>";
		
		echo '<td title="'.$row['name'].'" id="file-'.$row['id'].'"><var data-type="type" data-link="?s=type&q='.$row["type"].'">'.$row["type"].'</var><a id="url-'.$row['id'].'" href="'.$link.'" target="_blank">'.format_url($row['name'],40).'</a></td>';
		echo ("<td><a href='?s=account&q=".urlencode($row['account'])."'>".$row['account']."</a></td>");
		echo ("<td>".$row["input"]."</td>");
		echo ("<td>".sizecount($row["size"])."</td>");
		echo ("<td><a href='?r=".$row["remote"]."'>".$_G['project']['attach'][$row["remote"]]."</a></td>");
		echo ("<td>".($row["width"]?$row["width"]."*".$row["height"]:"")."</td>");
		echo ("<td>".date("Y-m-d H:i:s",$row['dateline'])."</td>");
		echo '<td><button type="button" class="normal" data-url="?action=delete&list='.$row["id"].'">删除</button></td>';
		
	}
	
	echo '
		<tr class="tfoot">
		<td colspan="9"><a href="javascript:$(\'list\').value = setAll(\'#table tbody tr input\',true); void(0);">全选</a> / <a href="javascript:$(\'list\').value = setAll(\'#table tbody tr input\',false); void(0);">全不选</a> / <a href="javascript:$(\'list\').value = setAll(\'#table tbody tr input\',\'anti\'); void(0);">反选</a> - <a href="javascript:if(confirm(\'确定要删除所选吗?\')){post-form(\'post-form\',\'delete\');}void(0);">删除所选</a></td>
		</tr>
	</table>
	';

?>