<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("链接编辑 - VeryIDE");
?>


	<?php	

	//载入模块配置并生成菜单
	$appid = Module :: get_appid();
	
	echo Module :: get_context( $appid );

	//////////////////////////////////

	$jump = getgpc('jump');
	$action = getgpc('action');
	$id = getnum('id',0);
	
	if ($action){
		
		//连接数据库
		System :: connect();

		$name = getgpc('name');
		$link = getgpc('link');
		$method = getgpc('method');
		$category = getgpc('category');
		
		switch($action){
			case "add":
				
				//检查权限
				System :: check_func( 'go-add', false );
				
				//检查数据
				System :: check_empty($name,$method);
				
				$sql="INSERT INTO `mod:go_list`(name,aid,account,category,link,dateline,modify,method) VALUES('".$name."',".$_G['manager']['id'].",'".$_G['manager']['account']."','".$category."','".$link."','".time()."',".time().",'".$method."')";
				
				System :: $db -> execute( $sql );
				
				$id = System :: $db -> getInsertId();
				
				//缓存广告缓存
				Cached :: rows( $appid,"SELECT * FROM `mod:go_list` WHERE id=".$id, array( 'alias' => 'go' ) );
				
				System :: redirect("go.list.php?mark=".$id,"链接提交成功!");

			break;
				
			case "update":
				
				//检查权限
				System :: check_func( 'go-mod', false );
				
				$sql="UPDATE `mod:go_list` SET name='".$name."',category='".$category."',link='".$link."',`modify`=".time().",mender='".$_G['manager']['account']."' WHERE id=".$id;

				System :: $db -> execute($sql);
				
				//缓存广告缓存
				Cached :: rows( $appid,"SELECT * FROM `mod:go_list` WHERE id=".$id, array( 'alias' => 'go' ) );		
				
				System :: redirect("go.list.php","链接修改成功!");
			
			break;
			
			case "edit";
				
				$sql="SELECT * FROM `mod:go_list` WHERE id=".$id;
				$row = System :: $db -> getOne( $sql );
			
			break;
		}
		  
		//关闭数据库
        System :: connect();
        
	  }
	  
	?>


	<?php
	
	//显示权限状态
	switch($action){
		case "":
			echo System :: check_func( 'go-add',true);
		break;
		
		case "edit":
			echo System :: check_func( 'go-mod',true);
		break;
	}
	
	?>

	<div id="box">
	    <form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-mode="edit" data-valid="true">        
        	
        <table cellpadding="0" cellspacing="0" class="form">
        
        	<tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>
        
        	<tr>
            	<th>链接名称：</th>
                <td>
                	<input type="text" name="category" id="category" class="text digi" data-valid-name="内容分类" data-valid-empty="yes" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '内容分类' : ['<?php echo implode("','",explode(" ",trim($_G['setting']['global']['category'])));?>'] }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true" value="<?php echo $row["category"];?>" onfocus="this.blur();" />
                
                <input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="35" data-valid-name="链接主题" data-valid-empty="yes" />
                
                </td>
            </tr>
        
        	<tr>
            	<th>数据类型：</th>
                <td>
                <label><input type="radio" class="radio" name="method" value="click" checked="checked" onclick='doChanged(this.value)' <?php echo ($action?' disabled="disabled" ':'');?> /> 点击量 </label>
                <label><input type="radio" class="radio" name="method" value="view" onclick='doChanged(this.value)' <?php echo ($action?' disabled="disabled" ':'');?> /> 浏览量 </label>
                </td>
            </tr>
        
        	<tr id="addv" text='填写此链接引用页地址,将在列表中显示,以后可以更方便的找到引用页'>
            	<th>链接地址：</th>
                <td>
                    <input name="link" type="text" class="text" id="link" value="<?php echo $row['link'];?>" size="70" /> 
                    <?php echo loader_image("icon/globe.png","打开引用地址","","var s=Mo('#link').value();if(s){window.open(s);}");?>
                	<p>
                    	支持以下连接方式：
                        <a href="javascript:void(0);" onclick="getScript('http')" title="URL连接地址">HTTP</a>
                        |
                        <a href="javascript:void(0);" onclick="getScript('mail')" title="电子邮件链接">Mail</a>
                        |
                        <a href="javascript:void(0);" onclick="getScript('qq')" title="QQ 交谈窗口">QQ</a>
                        |
                        <a href="javascript:void(0);" onclick="getScript('msn')" title="MSN 交谈窗口">MSN</a>
                        |
                        <a href="javascript:void(0);" onclick="getScript('ww')" title="阿里旺旺聊天窗口">旺旺</a>
                    </p>
                </td>
            </tr>
        
        	<tr>
            	<td></td>
                <td>
					<?php
                        if ($action=="edit"){
                            echo '
                            <input name="action" type="hidden" id="action" value="update" />
                            <input name="id" type="hidden" id="id" value="'.$row['id'].'" />
                            ';
                            echo '<button type="submit" name="Submit" class="submit">修改此统计</button>';
                        }else{
                            echo '<input name="action" type="hidden" id="action" value="add" />';
                            echo '<button type="submit" name="Submit" class="submit">新增此统计</button>';
                        }
                    ?>
                </td>
            </tr>
        
        </table>
        
		<script type="text/javascript">
		Mo("input[name=method]").value("<?php echo $row['method']?$row['method']:"click";?>");
		Mo("#category").value( "<?php echo $row["category"];?>" );
		
		function doChanged(s){
			var b = ['click'];
			if( Mo.Array( b ).indexOf( s ) > -1 ){
				Mo('#addv').show();
			}else{
				Mo('#addv').hide();
			}
		}
		
		function getScript( pox ){
			var old = Mo('#link').value();
			var tmp = old ? old.replace( /^(http|mail|qq|msn|ww):\/\// , pox+'://' ) : pox+'://';
			Mo('#link').value( tmp ).focus();
		}
		
		doChanged("<?php echo $row['method']?$row['method']:"click";?>");
        </script>
        
    	</form>
        
    </div>
	


<?php html_close();?>