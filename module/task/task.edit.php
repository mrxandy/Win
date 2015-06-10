<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("编辑任务 - VeryIDE");
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
		
		//项目模块
		$project = getgpc('project');
		
		$modular = getgpc('modular');
		
		$assign = getnum('assign',0);

		$type = getgpc('type');
		
		$demand = getgpc('demand');

		$name = getgpc('name');
		
		$description = getgpc('description');
		
		$priority = getgpc('priority');
		
		$initially = getgpc('initially');
				
		//开始时间
		$start = gettime('start');
		
		//结束时间
		$expire = gettime('expire');
		
		$state = getnum('state',0);
		
		$copy = getnum('copy',0);
		
		switch($action){
			
			case "add":
			
				//检查权限
				System :: check_func( 'workflow-stuff-add', false );
				
				$sql="INSERT INTO `mod:task`(account,uid,project,modular,assign,state,type,demand,name,description,priority,initially,start,expire,copy,dateline,ip) VALUES ('".$_G['manager']['account']."','".$_G['manager']['id']."','".$project."','".$modular."','".$assign."','".$state."','".$type."','".$demand."','".$name."','".$description."','".$priority."','".$initially."','".$start."','".$expire."','".$copy."',".time().",'".GetIP()."')";

				System :: $db -> execute( $sql );
				
				System :: redirect("task.list.php","物品添加成功!");
				
			break;
			
			case "edit":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );
			
				$sql="SELECT * FROM `mod:task` WHERE id=".$id;
				
				$row = System :: $db -> getOne( $sql );
				
			break;
			
			case "update":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );


                $sql="UPDATE `mod:task` SET `project`='".$project."',modular='".$modular."',assign='".$assign."',state='".$state."',type='".$type."',demand='".$demand."',name='".$name."',description='".$description."',priority='".$priority."',initially='".$initially."',start='".$start."',expire='".$expire."',copy='".$copy."' WHERE id=".$id;


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
	
		
	$sql="SELECT * FROM `sys:admin` WHERE 1=1";
	
	$user = System :: $db -> getAll( $sql );

	
	?>

	<div id="box">
		<form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-mode="edit" data-valid="true">
        
        <table cellpadding="0" cellspacing="0" class="form">
            
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>

			<tr>
				<th>所属项目：</th>
				<td>
                    <input name="project" type="text" class="text" id="project" value="<?php echo $row['project'];?>" size="40" data-valid-name="表单主题" data-valid-empty="yes" />
				</td>                
			</tr>
			
			<tr>
				<th>所属模块：</th>
				<td>
                    <input name="modular" type="text" class="text" id="modular" value="<?php echo $row['modular'];?>" size="40" data-valid-name="表单主题" data-valid-empty="yes" />
				</td>                
			</tr>     
            
			<tr>
				<th>指派给：</th>
				<td>
                    <select name="assign" id="assign" style=" width:200px;">
                    <?php
                    
						foreach( $user as $value ){
							
								echo '<option value="'.$value['id'].'" >'.$value['account'].'</option>';
	   
						}
										
					?>
                    </select>
				</td>                
			</tr>    

			<tr>
				<th>任务类型：</th>
				<td>
                    <select name="type" id="type" style=" width:200px;">
                        <option value='design'>设计</option>
                        <option value='devel'>开发</option>
                        <option value='test'>测试</option>
                        <option value='study'>研究</option>
                        <option value='discuss'>讨论</option>
                        <option value='ui'>界面</option>
                        <option value='affair'>事务</option>
                        <option value='misc'>其他</option>
                    </select>
				</td>                
			</tr>


            <tr>
                <th>优先级：</th>
                <td>
                    <?php

                    foreach($_G['module']['task']['level'] as $key=>$val){
                        echo '<label><input type="radio" class="radio" name="priority" value="'.$key.'" /> '.$val.'</label>';
                    }

                    ?>
                </td>
            </tr>

            
			<tr>
				<th>相关需求	：</th>
				<td>
                    <input name="demand" type="text" class="text" id="demand" value="<?php echo $row['demand'];?>" size="40" data-valid-name="表单主题" data-valid-empty="yes" />
				</td>                
			</tr>        
            
			<tr>
				<th>任务名称	：</th>
				<td>
                    <input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="40" data-valid-name="表单主题" data-valid-empty="yes" />
				</td>                
			</tr>   
            
            
			<tr>
				<th>任务描述：</th>
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
				<th>最初预计	：</th>
				<td>
                    <input name="initially" type="text" class="text" id="initially" value="<?php echo $row['initially'];?>" size="40" data-valid-name="表单主题" data-valid-empty="yes" />
				</td>                
			</tr>  
            
            
            <tr>
                <th>起止时间：</th>
                <td>
                <input name="start[date]" type="text" class="text date" value="<?php echo date("Y-m-d",($row['start']?$row['start']:time()));?>" size="12" readonly="true" title="年-月-日">
                
                <input name="start[time]" type="text" title="小时:分钟" class="text time" readonly="true" value="<?php echo $row['start'] ? date("H:i",$row['start']) : "00:00";?>" />
                
                -
                <input name="expire[date]" type="text" class="text date" value="<?php echo date("Y-m-d",($row['expire'] ? $row['expire'] : time() + intval($_G['setting']['global']['overtime'])));?>" size="12" readonly="true" title="年-月-日">
                
                <input name="expire[time]" type="text" title="小时:分钟" class="text time" readonly="true" value="<?php echo $row['expire'] ? date("H:i",$row['expire']) : "23:59";?>" />
                </td>
            </tr>
            
            
			<tr>
				<th>抄送给	：</th>
				<td>
                    <select name="copy" id="copy" style=" width:200px;">
                    <?php
                    
						foreach( $user as $value ){
							
								echo '<option value="'.$value['id'].'" >'.$value['account'].'</option>';
	   
						}
										
					?>
                    </select>
				</td>                
			</tr>              

            
			<tr>
				<td></td>
				<td>
					<?php
						if ($action=="edit"){
							echo '<input name="action" type="hidden" id="action" value="update" />
							<input name="id" type="hidden" value="'.$id.'" />';
							echo '<button type="submit" name="Submit" class="submit">修改任务</button>';
						}else{
							echo '<input name="action" type="hidden" id="action" value="add" />';
							echo '<button type="submit" name="Submit" class="submit">新增任务</button>';
						}
					?>
				</td>				
			</tr>

        </table>
        
		</form>
	
	</div>
		


<?php html_close();?>