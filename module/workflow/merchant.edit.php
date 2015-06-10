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
		$name = getgpc('name');
		$level = getnum('level',0);
		$contribute = getnum('contribute',0);
		$phone = getgpc('phone');
		$address = getgpc('address');
		$summary = getgpc('summary');
		$description = getgpc('description');
		$quote = getgpc('quote');
		$category = getgpc('category');
		
		$contact = getgpc('contact');
		$contact = serialize( $contact );
		
		$product = getgpc('product');
		$product = implode( ',', $product );
		$detailed = serialize( getgpc('detailed') );
		
		switch($action){
			
			case "add":
			
				//检查权限
				System :: check_func( 'workflow-merchant-add', false );
			
				$sql="INSERT INTO `mod:workflow_merchant`(name,aid,account,quote,level,phone,address,summary,description,dateline,ip,category,contact,product,detailed) VALUES('".$name."',".$_G['manager']['id'].",'".$_G['manager']['account']."','".$quote."',".$level.",'".$phone."','".$address."','".$summary."','".$description."',".time().",'".GetIP()."','".$category."','".$contact."','".$product."','".$detailed."')";
				
				System :: $db -> execute( $sql );
				
				System :: redirect("merchant.edit.php","商家添加成功!");
				
			break;
			
			case "edit":
			
				//检查权限
				System :: check_func( 'workflow-merchant-mod', false );
			
				$sql="SELECT * FROM `mod:workflow_merchant` WHERE id=".$id;
				
				$row = System :: $db -> getOne( $sql );
				
				$row['product'] = explode( ',', $row['product'] );
				$row['contact'] = unserialize( $row['contact'] );
				$row['detailed'] = unserialize( $row['detailed'] );
				
			break;
			
			case "update":
			
				//检查权限
				System :: check_func( 'workflow-merchant-mod', false );				
			
				$sql="UPDATE `mod:workflow_merchant` SET `modify`=".time().",mender='".$_G['manager']['account']."',quote='".$quote."',level='".$level."',phone='".$phone."',address='".$address."',summary='".$summary."',description='".$description."',category='".$category."',contact='".$contact."',product='".$product."',detailed='".$detailed."' WHERE id=".$id;
				
				System :: $db -> execute( $sql );
				
				System :: redirect($jump,"商家修改成功!");
				
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
			echo System :: check_func( 'workflow-merchant-mod',true);
		break;
	}
	
	echo loader_script(array(VI_BASE.'source/editor/kindeditor.js',VI_BASE.'source/editor/lang/zh_CN.js'),'utf-8',$_G['product']['version']);
	?>
    
    <script type="text/javascript">	
    function doProduct(){
        var o = Mo("input[name='product[]']").value();		
		Mo("*[data-block]").hide();
		for( var i=0; i<o.length; i++ ){
			Mo("*[data-block="+ o[i] +"]").show();
		}
    }
    </script>

	<div id="box">
		<form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-mode="edit" data-valid="true">
        
        <table cellpadding="0" cellspacing="0" class="form">
            
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>

			<tr>
				<th>商家名称：</th>
				<td>
					<input type="text" name="category" id="category" class="text digi" data-valid-name="商家分类" data-valid-empty="yes" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '商家分类' : ['<?php echo implode("','",explode(" ",trim($_G['setting']['global']['category'])));?>'] }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true" value="<?php echo $row["category"];?>" onfocus="this.blur();" />
                    <input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="49" data-valid-name="表单主题" data-valid-empty="yes" />
                    <select name="level">
                    <?php
					foreach($_G['module']['workflow']['merchant'] as $key=>$val){
                        echo '<option value="'.$key.'"> '.$val.'</option>';
                    }
					?>
                    </select>
				</td>                
			</tr>
			
			<tr>
				<th>电话地址：</th>
				<td>
                <input name="phone" type="text" class="text" value="<?php echo $row['phone'];?>" size="20" />
                <input name="address" type="text" class="text" value="<?php echo $row['address'];?>" size="54" />
				</td>                
			</tr>
			
            <tr>
                <th>链接地址：</th>
                <td>
                    <input name="quote" type="text" class="text link" id="quote" value="<?php echo $row["quote"];?>" size="80" />
                </td>
            </tr>
			
			<!--tr>
				<th>商家摘要：</th>
				<td>
					<textarea name="summary" cols="80" rows="4"><?php echo $row['summary'];?></textarea>
				</td>                
			</tr-->
					
			<tr>
				<th>详细介绍：</th>
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
            	<th>各联系人：</th>
                <td>
                    
                    <table id="tag-box" cellpadding="0" cellspacing="1" border="0" class="frame">
                    	<thead>
                            <tr>
                            <td>联系人</td>
                            <td>电话</td>
                            <td>QQ</td>
                            <td>职位</td>
                            <td>操作</td>
                        </thead>
						<?php
						//var_dump($row['contact']);		
						
						if( !$row['contact'] ) $row['contact'] = array( 'name' => array('') );
						
                        foreach( $row['contact']['name'] as $i => $name ){
							//var_dump( $conf );
							//echo '<br />';
                        ?>
                        <tr>
                            <td>
                            <input type="text" size="10" name="contact[name][]" value="<?php echo $row['contact']['name'][$i];?>" data-valid-name="联系人" data-valid-empty="yes" />
                            </td>
                            <td>
                            <input type="text" size="10" name="contact[phone][]" value="<?php echo $row['contact']['phone'][$i];?>" data-valid-name="电话" data-valid-number="no" />
                            </td>
                            <td>
                            <input type="text" size="10" name="contact[qq][]" value="<?php echo $row['contact']['qq'][$i];?>" data-valid-name="QQ" data-valid-number="no" />
                            </td>
                            <td>
                            <input type="text" size="10" name="contact[post][]" value="<?php echo $row['contact']['post'][$i];?>" />
                            </td>
                            <td>
                            <a onclick="javascript:Planer.move(this,'up',2);void(0);">上移</a>
							<a onclick="javascript:Planer.move(this,'down',2);void(0);">下移</a>
                            <a onclick="javascript:Planer.remove(this,2);void(0);">删除</a>
                            </td>
                        </tr>
                        <?php
						}
						?>
                    </table>
                    
                    <script type="text/javascript">
                    var Planer = new Mo.Planer(Mo.$("tag-box"));
                    </script>
                    
                    <a onclick="javascript:Planer.copy();void(0);">添加</a>
                    
              </td>
                
            </tr>
 			
            <tr>
                <th>购买产品：</th>
                <td>
                    <?php
					
						foreach($_G['module']['workflow']['product'] as $key=>$val){
							echo '<label><input type="checkbox" class="checkbox" name="product[]" value="'.$key.'" '.( in_array( $key, $row['product'] ) ? 'checked="checked"' : '' ).' /> '.$val.'</label>';
						}

					?>
                </td>
            </tr>
           
            <tbody data-block="qrcode" style="display:none;">
            
            <tr><td colspan="2" class="section"><strong>二维码</strong></td></tr>
            
            <tr>
                <th>QQ账号：</th>
                <td>
                    <table cellpadding="0" cellspacing="1" border="0" class="gird">
                    	<thead>
                        	<td>QQ账号</td>
                        	<td>QQ密码</td>
                        </thead>
                        <tr>
                            <td><input name="detailed[qrcode][qq][account]" type="text" class="text" value="<?php echo $row['detailed']['qrcode']['qq']['account'];?>" size="20" /></td>
                            <td><input name="detailed[qrcode][qq][password]" type="text" class="text" value="<?php echo $row['detailed']['qrcode']['qq']['password'];?>" size="20" /></td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
                <th>微信账号：</th>
                <td>
                    <table cellpadding="0" cellspacing="1" border="0" class="frame">
                    	<thead>
                        	<tr>
                        	<td>微信账号</td>
                        	<td>微信密码</td>
                        	<td>验证手机</td>
                        	<td>验证身份证</td>
                        </thead>
                        <tr>
                            <td><input name="detailed[qrcode][wechat][account]" type="text" class="text" value="<?php echo $row['detailed']['qrcode']['wechat']['account'];?>" size="15" /></td>
                            <td><input name="detailed[qrcode][wechat][password]" type="text" class="text" value="<?php echo $row['detailed']['qrcode']['wechat']['password'];?>" size="15" /></td>
                            <td><input name="detailed[qrcode][wechat][phone]" type="text" class="text" value="<?php echo $row['detailed']['qrcode']['wechat']['phone'];?>" size="15" /></td>
                            <td>
                            <input name="detailed[qrcode][wechat][idcard]" id="idcard" type="hidden" class="text" value="<?php echo $row['detailed']['qrcode']['wechat']['idcard'];?>" size="15" />
                            <script type="text/javascript">
							new Serv.Upload("thumb","<?php echo $row['detailed']['qrcode']['wechat']['idcard'];?>",{'format':['<?php echo implode("','",$_G['upload']['image']);?>'],'callback':function( data ){ Mo("#idcard").value( data.value ) }});
							</script>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
                <th>论坛账号：</th>
                <td>
                    <table cellpadding="0" cellspacing="1" border="0" class="gird">
                    	<thead>
                        	<td>论坛账号</td>
                        	<td>论坛密码</td>
                        </thead>
                        <tr>
                            <td>
                            <input name="detailed[qrcode][forum][account]" type="text" class="text" value="<?php echo $row['detailed']['qrcode']['forum']['account'];?>" size="20" />
                            </td>
                            <td>
                            <input name="detailed[qrcode][forum][password]" type="text" class="text" value="<?php echo $row['detailed']['qrcode']['forum']['password'];?>" size="20" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            </tbody>
            
			<tr>
				<td></td>
				<td>
					<?php
						if ($action=="edit"){
							echo '<input name="action" type="hidden" id="action" value="update" />
							<input name="id" type="hidden" value="'.$id.'" />';
							echo '<button type="submit" name="Submit" class="submit">修改此商户</button>';
						}else{
							echo '<input name="action" type="hidden" id="action" value="add" />';
							echo '<button type="submit" name="Submit" class="submit">新增此商户</button>';
						}
					?>
				</td>
			</tr>

        </table>
		  
		<script type="text/javascript">
        Mo("input[name=level]").value("<?php echo isset($row['level']) ? $row['level'] : 1;?>");
		Mo("input[name='product[]']").bind('change',doProduct).event('change');
        </script>
        
		</form>
	
	</div>
		


<?php html_close();?>