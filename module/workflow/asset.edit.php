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
	
		$id = getgpc("id");
		$state = getnum('state',0);
		$name = getgpc('name');
		$category = getgpc('category');
		$quantity = getnum('quantity',0);
		$total = getnum('total',0);
		$summary = getgpc('summary');
		$description = getgpc('description');
		$quote = getgpc('quote');
		$image = getgpc("image");
		
		$uid = getnum('uid',0);
		$username = $uid ? $_CACHE['system']['admin'][$uid]['account'] : '';
		
		switch($action){
			
			case "add":
			
				//检查权限
				System :: check_func( 'workflow-stuff-add', false );
			
				$sql="INSERT INTO `mod:workflow_asset`(name,aid,account,quote,category,quantity,total,summary,image,description,uid,username,dateline,ip) VALUES('".$name."',".$_G['manager']['id'].",'".$_G['manager']['account']."','".$quote."','".$category."',".$quantity.",".$total.",'".$summary."','".$image."','".$description."','".$uid."','".$username."',".time().",'".GetIP()."')";
				
				System :: $db -> execute( $sql );
				
				System :: redirect("stuff.edit.php","资产添加成功!");
				
			break;
			
			case "edit":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );
			
				$sql="SELECT * FROM `mod:workflow_asset` WHERE id=".$id;
				
				$row = System :: $db -> getOne( $sql );
				
			break;
			
			case "update":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );				
			
				$sql="UPDATE `mod:workflow_asset` SET `modify`=".time().",mender='".$_G['manager']['account']."',quote='".$quote."',category='".$category."',quantity='".$quantity."',total='".$total."',summary='".$summary."',image='".$image."',description='".$description."',uid='".$uid."',username='".$username."',state='".$state."' WHERE id=".$id;
				
				System :: $db -> execute( $sql );
				
				System :: redirect($jump,"资产修改成功!");
				
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
				<th>物品名称：</th>
				<td>
				
					<input type="text" name="category" id="category" class="text digi" data-valid-name="商家分类" data-valid-empty="yes" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '商家分类' : ['<?php echo implode("','",explode(" ",trim($_G['setting']['global']['category'])));?>'] }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true" value="<?php echo $row["category"];?>" onfocus="this.blur();" />
				
                    <div style=" position:absolute; right:20px;"><img id="picture" src="<?php echo $row["image"];?>" style="max-height:400px;" /></div>
                    <input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="40" data-valid-name="资产名称" placeholder="资产名称" data-valid-empty="yes" />
                    
                    <input name="quantity" type="text" class="text digi" value="<?php echo $row['quantity'];?>" size="4" data-valid-name="资产数量" placeholder="资产数量" data-valid-number="yes" />
                    
                    <input name="total" type="text" class="cash" value="<?php echo $row['total'];?>" size="10" data-valid-name="市场估价" placeholder="市场估价" data-valid-number="yes" />
                    
				</td>                
			</tr>
			
			<tr>
				<th>物品图片：</th>
				<td>
				<script type="text/javascript">
                new Serv.Upload("image","<?php echo $row["image"];?>",{'format':['<?php echo implode("','",$_G['upload']['image']);?>'],'again':true,'recovery':true,'input':true, 'callback' : function( o ){ Mo("#picture").attr({"src":o["value"]}) } });
                </script>
				</td>                
			</tr>
			
			<tr>
				<th>物品摘要：</th>
				<td>                	
					<textarea name="summary" cols="90" rows="4"><?php echo $row['summary'];?></textarea>
				</td>                
			</tr>
			            
            <tr>
				<th>资产状态：</th>
				<td>
                    <?php
					
						foreach($_G['module']['workflow']['asset'] as $key=>$val){
							echo '<label><input type="radio" class="radio" name="state" value="'.$key.'" /> '.$val.'</label>';
						}

					?>
					
					<select name="uid" data-extra="done">
	                	<option value=''></option>
	                    <?php
						foreach( $_CACHE['system']['admin'] as $aid => $acc ){
							echo '<option value="'.$aid.'">'.$acc["account"].'</option>';	
						}
						?>
	                </select>
					
				</td>
			</tr>
					
			<tr>
				<th>详细说明：</th>
				<td>
					<textarea name="description" cols="50" rows="10" id="description" style="width:670px;height:300px;"><?php echo $row['description'];?></textarea>
                    <script type="text/javascript">
                    var editor;
                    KindEditor.ready(function(K) {
                        editor = K.create('#description', {
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
                <th>引用地址：</th>
                <td>
                    <input name="quote" type="text" class="text link" id="quote" value="<?php echo $row["quote"];?>" size="60" />
                </td>
            </tr>
            
			<tr>
				<th></th>
				<td>
					<?php
						if ($action=="edit"){
							echo '<input name="action" type="hidden" id="action" value="update" />
							<input name="id" type="hidden" value="'.$id.'" />';
							echo '<button type="submit" name="Submit" class="submit">修改此资产</button>';
						}else{
							echo '<input name="action" type="hidden" id="action" value="add" />';
							echo '<button type="submit" name="Submit" class="submit">新增此资产</button>';
						}
					?>
				</td>				
			</tr>

        </table>
        
		</form>
	
	</div>
		
	<script type="text/javascript">
		Mo("select[name=uid]").value("<?php echo $row['uid'];?>");
		
		Mo("input[name=state]").bind('change',function(){
			var val = Mo( this ).value();
			if( val == 1 ){
				Mo("*[data-extra='done']").enabled();	
			}else{
				Mo("*[data-extra='done']").disabled();	
			}													   
		}).value("<?php echo isset($row['state']) ? $row['state'] : 1;?>").event('change');
    </script>

<?php html_close();?>