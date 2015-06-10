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
		$mid = getnum('mid',0);
		$quantity = getnum('quantity',0);
		$total = getnum('total',0);
		$summary = getgpc('summary');
		$description = getgpc('description');
		$quote = getgpc('quote');
		$image = getgpc("image");
		
		switch($action){
			
			case "add":
			
				//检查权限
				System :: check_func( 'workflow-stuff-add', false );
			
				$sql="INSERT INTO `mod:workflow_stuff`(name,aid,account,quote,mid,quantity,surplus,total,summary,image,description,dateline,ip) VALUES('".$name."',".$_G['manager']['id'].",'".$_G['manager']['account']."','".$quote."',".$mid.",".$quantity.",".$quantity.",".$total.",'".$summary."','".$image."','".$description."',".time().",'".GetIP()."')";
				
				System :: $db -> execute( $sql );
				
				System :: redirect("stuff.edit.php","物品添加成功!");
				
			break;
			
			case "edit":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );
			
				$sql="SELECT * FROM `mod:workflow_stuff` WHERE id=".$id;
				
				$row = System :: $db -> getOne( $sql );
				
			break;
			
			case "update":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );				
			
				$sql="UPDATE `mod:workflow_stuff` SET `modify`=".time().",mender='".$_G['manager']['account']."',quote='".$quote."',mid='".$mid."',quantity='".$quantity."',total='".$total."',summary='".$summary."',image='".$image."',description='".$description."' WHERE id=".$id;
				
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
				<th>物品名称：</th>
				<td>
                    <div style=" position:absolute; right:20px;"><img id="picture" src="<?php echo $row["image"];?>" style="max-height:400px;" /></div>
                    <input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="40" data-valid-name="表单主题" data-valid-empty="yes" />
                    
                    数量：
                    <input name="quantity" type="text" class="text" value="<?php echo $row['quantity'];?>" size="5" data-valid-name="物品数量" data-valid-number="yes" />
                    
                    估价：
                    <input name="total" type="text" class="cash" value="<?php echo $row['total'];?>" size="10" data-valid-name="市场估价" data-valid-number="yes" />
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
					<textarea name="summary" cols="80" rows="4"><?php echo $row['summary'];?></textarea>
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
				<td></td>
				<td>
					<?php
						if ($action=="edit"){
							echo '<input name="action" type="hidden" id="action" value="update" />
							<input name="id" type="hidden" value="'.$id.'" />';
							echo '<button type="submit" name="Submit" class="submit">修改此物品</button>';
						}else{
							echo '<input name="action" type="hidden" id="action" value="add" />';
							echo '<button type="submit" name="Submit" class="submit">新增此物品</button>';
						}
					?>
				</td>				
			</tr>
            
            <?php
			if( $action == 'edit' ){
			?>
            
            <tr><td colspan="2" class="section"><strong>领用记录</strong></td></tr>
            <tr>
            
            	<td></td>
            	<td>
            
            	<table class="frame"> 
                
                	<thead>
                        <td>#</td>
                        <td>领用人员</td>
                        <td>领用数量</td>
                        <td>备注信息</td>
                        <td>领用时间</td>
                    </thead>
                          
					<?php
                    $sql="SELECT * FROM `mod:workflow_expend` WHERE sid=".$id." ORDER BY id ASC";
                    
                    $result = System :: $db -> getAll( $sql );
					
					$i = 1;
            
                    foreach( $result as $item ){
                        
                        ?>
                        <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>">
                            <td><?php echo $i;?></td>
                            <td><strong class="text-yes"><?php echo $item['account'];?></strong></td>
                            <td><?php echo $item["quantity"];?></td>
                            <td><?php echo $item["summary"];?></td>
                            <td><?php echo date("Y-m-d H:i:s",$item['dateline']);?></td>
                        </tr>
                        <?php
                    
                    }
                    
                    if( count( $result ) == 0 ){
                        echo '<tr><td colspan="5" class="notice">没有检索到相关记录，<a href="?do=new&sid='.$sid.'">创建一个？</a></td></tr>';
                    }
                  
                    ?>
            	</table>
                
                <p>剩余数量：<strong class="text-no"><?php echo $row["surplus"];?></strong></p>
                
            </tr>
            
            <?php
			}
			?>

        </table>
        
		</form>
	
	</div>
		


<?php html_close();?>