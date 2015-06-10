<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("编辑日志 - VeryIDE");
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
	
		$id = getgpc("id");
		$content = getgpc("content");

		
		switch($action){
			
			case "add":
			
				//检查权限
				System :: check_func( 'workflow-stuff-add', false );
			
				$sql="INSERT INTO `mod:journal`(uid,account,content,dateline,ip) VALUES(".$_G['manager']['id'].",'".$_G['manager']['account']."','".$content."',".time().",'".GetIP()."')";
				
				var_dump( $sql );
				
				System :: $db -> execute( $sql );
				
				System :: redirect("stuff.edit.php","物品添加成功!");
				
			break;
			
			case "edit":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );
			
				$sql="SELECT * FROM `mod:journal` WHERE id=".$id;
				
				$row = System :: $db -> getOne( $sql );
				
			break;
			
			case "update":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );				
			
				$sql="UPDATE `mod:journal` SET `modify`=".time().",mender='".$_G['manager']['account']."',quote='".$quote."',mid='".$mid."',quantity='".$quantity."',total='".$total."',summary='".$summary."',image='".$image."',description='".$description."' WHERE id=".$id;
				
				System :: $db -> execute( $sql );
				
				System :: redirect($jump,"物品修改成功!");
				
			break;
		}
		
	}
	
	?>

	<?php
	
	//显示权限状态
	switch($action){
		case "":
		case "edit":
			echo System :: check_func( 'workflow-stuff-mod',true);
		break;
	}
	
	echo loader_script(array(VI_BASE.'source/editor/kindeditor.js',VI_BASE.'source/editor/lang/zh_CN.js'),'utf-8',$_G['product']['version']);
	?>

	<div id="box">
		<form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-mode="edit" data-valid="true">
        
        <table cellpadding="0" cellspacing="0" class="form">
            
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>

				
			<tr>
				<th>日志内容：</th>
				<td>
					<textarea name="content" cols="50" rows="10" id="content" style="width:670px;height:300px;"><?php echo $row['content'];?></textarea>
                    <script type="text/javascript">
                    var editor;
                    KindEditor.ready(function(K) {
                        editor = K.create('#content', {
                            resizeType : 1,
                            newlineTag : 'p',
                            allowFileManager : false,
                            filterMode : false,
                            loadStyleMode : true,
                            urlType : 'domain',
                            uploadJson : '<?php echo VI_BASE;?>source/editor/upload.php'
                        });
                    });
                    </script>
				</td>                
			</tr>
			
			<tr>
				<td></td>
				<td>
					<?php
						if ($action=="edit"){
							echo '<input name="action" type="hidden" id="action" value="update" />
							<input name="id" type="hidden" value="'.$id.'" />';
							echo '<button type="submit" name="Submit" class="submit">修改日志</button>';
						}else{
							echo '<input name="action" type="hidden" id="action" value="add" />';
							echo '<button type="submit" name="Submit" class="submit">新增日志</button>';
						}
					?>
				</td>				
			</tr>


        </table>
        
		</form>
	
	</div>
		


<?php html_close();?>