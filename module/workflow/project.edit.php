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
		$level = getnum('level',0);
		$contribute = getnum('contribute',0);
		$summary = getgpc('summary');
		$description = getgpc('description');
		$quote = getgpc('quote');
		$category = getgpc('category');
		$member = getgpc('member');
		$devote = getgpc('devote');
		
		$start_time = strtotime( getgpc('start_time') );
		$stop_time = strtotime( getgpc('stop_time') );
		$done_time = strtotime( getgpc('done_time') );
		$report = getgpc('report');
		
		switch($action){
			
			case "add":
			
				$sql="INSERT INTO `mod:workflow_project`(name,aid,account,quote,state,level,summary,description,dateline,ip,category,member) VALUES('".$name."',".$_G['manager']['id'].",'".$_G['manager']['account']."','".$quote."',".$state.",".$level.",'".$summary."','".$description."',".time().",'".GetIP()."','".$category."','".$member."')";
				
				System :: $db -> execute( $sql );
				
				$id = System :: $db -> getInsertId();
				
			break;
			
			case "edit":
			
				//检查权限
				System :: check_func( 'workflow-project-mod', false );
			
				$sql="SELECT * FROM `mod:workflow_project` WHERE id=".$id;
				
				$row = System :: $db -> getOne( $sql );
				
			break;
			
			case "update":
			
				//检查权限
				System :: check_func( 'workflow-project-mod', false );				
			
				$sql="UPDATE `mod:workflow_project` SET `modify`=".time().",mender='".$_G['manager']['account']."',start_time='".$start_time."',stop_time='".$stop_time."',name='".$name."',quote='".$quote."',level='".$level."',summary='".$summary."',description='".$description."',member='".$member."',category='".$category."' WHERE id=".$id;
				
				System :: $db -> execute( $sql );
				
			break;
		}
		
		//有审核权限
		if( $action != "edit" && System :: check_func( 'workflow-project-exa' ) ){
			
			$contribute = getall_by_key( $devote, 'contribute' );
			
			$contribute = array_sum( $contribute );
			
			$sql="UPDATE `mod:workflow_project` SET `modify`=".time().",mender='".$_G['manager']['account']."',`state`='".$state."',`contribute`='".$contribute."',`done_time`='".( $state == 2 ? $done_time : 0 )."',report='".$report."' WHERE id=".$id;
				
			System :: $db -> execute( $sql );
			
			//exit( $sql );
			
			/////////////////////////////////////
			
			foreach( $devote as $aid => $item ){
				
				$sql="REPLACE INTO `mod:workflow_devote`(pid,aid,account,master,contribute,summary,dateline) VALUES('".$id."',".$aid.",'".$item['account']."','".$item['master']."','".$item['contribute']."','".$item['summary']."',".$done_time.")";
				
				System :: $db -> execute( $sql );
				
			}		
			
			/////////////////////////////////////
			
			//新选成员
			$newly = array_keys( $devote );
			
			/*
			
			//已选成员
			$sql = "SELECT aid FROM `mod:workflow_devote` WHERE pid=".$id;
			$member = System :: $db -> getAll( $sql, 'aid' );
			$exist = array_keys( $member );
			
			//比较差集
			$diff = array_diff( $exist, $newly );
			var_dump(  );
			
			*/
			
			$sql="DELETE FROM `mod:workflow_devote` WHERE pid=".$id." AND aid NOT IN(". implode( ',', $newly ) .")";				
			System :: $db -> execute( $sql );			
			
			System :: redirect( $jump ? $jump : "project.list.php","项目处理成功!");
			
		}		
		
	}
	
	?>

	<?php
	
	//显示权限状态
	switch($action){
		case "":
		case "edit":
			echo System :: check_func( 'workflow-project-mod',true);
		break;
	}
	
	
	echo loader_script(array(VI_BASE.'source/editor/kindeditor.js',VI_BASE.'source/editor/lang/zh_CN.js'),'utf-8',$_G['product']['version']);
	?>
    
    <style>
	.idchoice{ margin:0 15px; overflow:hidden; font-size:12px;}
	.idchoice li{ width:25%; float:left; line-height:25px; margin-top:10px;}
	.idchoice li img{ display:block; width:50px; height:50px;}
	</style>
    
    <script type="text/javascript">	
	
	Serv.IdCard = {
		
		//回调函数
		Call : null,
		
		//用户清单
		User : null,
	
		/*
		获取系统用户组
		*/
		Panel : function( fn ){
			
			Serv.IdCard.Call = fn;
			
			/////////////////////
			
			//从缓存读取
			if( Serv.IdCard.User ){
				
				Serv.IdCard.Display( Serv.IdCard.User );
				
			}else{
			
				var url = Mo.store.base + 'serv.php';		
				
				var ajax = new Mo.Ajax( url );
				ajax.method = "GET";
				ajax.setVar({'action':'address','&execute':'xml'});
				
				ajax.onError = function (){
					alert(ajax.response)
				}
		
				ajax.onCompletion = function (){
					var data = Serv.IdCard.User =  ajax.responseXML;				
					Serv.IdCard.Display( data );
				}
				
				ajax.send("");
			
			}
			
		},
		
		Display : function( data ){			
			
			var item = data.getElementsByTagName('item');
			var html = '';
			
			html += '<ul class="idchoice">';
			for( var i=0; i<item.length; i++ ){
				
				var avatar = item[i].getAttribute('avatar') ? item[i].getAttribute('avatar') : Mo.store.base + 'image/face-thumb.jpg';
				
				html += '<li><img src="'+ Serv.fix_thumb( avatar ) +'" title=" '+ item[i].getAttribute('account') +'" /><label><input type="radio" name="user" value="{&quot;uid&quot;:'+ item[i].getAttribute('id') +',&quot;account&quot;:&quot;'+ item[i].getAttribute('account') +'&quot;}" />'+ item[i].getAttribute('account') +'</label></li>';
			}
			html += '</ul>';
			
			html += '<p><button type="button" class="button" onclick="Serv.IdCard.Choice(this.parentNode.parentNode);">确定选择</button></p>';
			
			Mo.store._idcard = new Mo.Dialog( "mo-dialog", "人员选择", html, 390, 0, { "unique":"dialog_idcard", "remove" : true, "draged" : true, "create" : true } );
			
		},
		
		Choice : function( wrap ){
			
			var data = Mo( "input[name=user]", wrap ).value();
			//var data = Mo( "input[value="+ uid +"]", wrap ).attr('account');
			
			if( data ){				
				var user = Mo.String( data ).eval();
				Serv.IdCard.Call( user );			
				//alert( user );
			}
			
			Mo.store._idcard.Remove();
			
		},
		
		Change : function( line ){
			
			Serv.IdCard.Panel( function( user ){ 
				//console.log( data );
				
				Mo( "input", line ).each(function( ){ this.name = this.name.replace(/devote\[\d+\]/,'devote['+ user.uid +']'); });
				
				Mo( "*[data-account=true]", line ).value( user.account );
				
			} );
			
		}
		
	}
	
	//Serv.IdCard.Panel( function( data ){ console.log( data ); } );
    </script>

	<div id="box">
		<form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-mode="edit" data-valid="true">
        
        <table cellpadding="0" cellspacing="0" class="form">
            
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>

			<tr>
				<th>项目名称：</th>
				<td>
					<input type="text" name="category" id="category" class="text digi" data-valid-name="项目分类" data-valid-empty="yes" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '项目分类' : ['<?php echo implode("','",explode(" ",trim($_G['setting']['workflow']['category'])));?>'] }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true" value="<?php echo $row["category"];?>" onfocus="this.blur();" />
                    <input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="65" data-valid-name="表单主题" data-valid-empty="yes" />
				</td>
			</tr>
			
			<tr>
				<th>项目级别：</th>
				<td>
                    <?php

					foreach($_G['module']['workflow']['level'] as $key=>$val){
                        echo '<label><input type="radio" class="radio" name="level" value="'.$key.'" /> '.$val.'</label>';
                    }

					?>
				</td>                
			</tr>
			
			<tr>
				<th>项目摘要：</th>
				<td>
					<textarea name="summary" cols="80" rows="4" style="width:94%;" data-valid-name="项目摘要" data-valid-empty="yes"><?php echo $row['summary'];?></textarea>
				</td>                
			</tr>
					
			<tr>
				<th>项目介绍：</th>
				<td>
					<textarea name="description" cols="50" rows="10" id="description" style="width:95%;height:300px;"><?php echo $row['description'];?></textarea>
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
            
			<!--tr>
				<th>项目成员：</th>
				<td>
                    <input name="member" type="text" class="text" value="<?php echo $row['member'];?>" size="60" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '内置标签' : ['<?php echo implode( "','", getall_by_key( $_CACHE['system']['admin'], 'account' ) );?>'] }, function( value ){ var val = this.value; var txt = ' '+ value; var pos = val.indexOf( txt ); this.value = pos > -1 ? val.replace( txt, '' ) : val + txt; }, 1 , 23 );" />
				</td>                
			</tr-->
        
        	<tr>
            	<th>起止时间：</th>
                <td>
                    <input name="start_time" type="text" class="text date" value="<?php echo date("Y-m-d",($row['start_time']?$row['start_time']:time()));?>" size="12" readonly="true" title="年-月-日">
                    开始，预计
                    <input name="stop_time" type="text" class="text date" value="<?php echo date("Y-m-d",($row['stop_time'] ? $row['stop_time'] : time()));?>" size="12" readonly="true" title="年-月-日">
                    结束
                </td>
                
            </tr>        
            
            
            <?php
			if( System :: check_func( 'workflow-project-exa' ) ){
			?>			
            
			<tr>
				<th>项目状态：</th>
				<td>
                    <?php
					
						foreach($_G['module']['workflow']['project'] as $key=>$val){
							echo '<label><input type="radio" class="radio" name="state" value="'.$key.'" /> '.$val.'</label>';
						}

					?>
				</td>
			</tr>
            
            <?php
			}
			?>
            
            <tr><td colspan="2" class="section"><strong>项目贡献</strong></td></tr>
        
        	<tr>
            	<th>项目成员：</th>
                <td>
                    
                    <table cellpadding="0" cellspacing="1" border="0" class="frame">
                    	<thead>
                            <tr>
                              <td>选择</td>
                            <td>人员</td>
                            <td>是否负责人</td>
                            <td>任务安排</td>
                            <td data-extra="done">贡献值</td>
                            <td>删除</td>
                        </thead>
                        <tbody id="tag-box">
                        <?php
						
						if( $action ){						
							$sql = "SELECT * FROM `mod:workflow_devote` WHERE pid=".$id." ORDER BY master DESC";							
							$devote = System :: $db -> getAll( $sql, 'aid' );						
						}
						
						//var_dump( $devote );
						
						if( !$devote ) $devote = array( 'name' => array('') );
						//var_dump($row['contact']);
                        foreach( $devote as $item ){
							//var_dump( $conf );
							//echo '<br />';
                        ?>
                        <tr>
                          <td><button type="button" onclick="Serv.IdCard.Change(this.parentNode.parentNode);">+</button></td>
                            <td>
                            <input data-account="true" type="text" size="10" name="devote[<?php echo $item["aid"];?>][account]" value="<?php echo $item["account"];?>" readonly="readonly" />
                            </td>
                            <td>
                            <label><input type="checkbox" size="10" name="devote[<?php echo $item["aid"];?>][master]" value="1" <?php echo $item["master"] ? 'checked="checked"' : '';?> /> 是</label>
                            </td>
                            <td>
                            <input type="text" size="55" name="devote[<?php echo $item["aid"];?>][summary]" value="<?php echo $item["summary"];?>" />
                            </td>
                            <td data-extra="done">
                            <input type="text" size="5" name="devote[<?php echo $item["aid"];?>][contribute]" value="<?php echo $item["contribute"];?>" />
                            </td>
                            <td><button type="button" onclick="Planer.remove(this,2);">-</button></td>
                        </tr>
                        <?php
						}
						?>
                        </tbody>
                    </table>
                    
					<script type="text/javascript">
                    var Planer = new Mo.Planer(Mo.$("tag-box"));
                    </script>
                    
                    <a onclick="javascript:Planer.copy();void(0);">添加</a>                    
                    
              </td>
                
            </tr>
            
            <tbody data-extra="done">
            
            <tr><td colspan="2" class="section"><strong>项目结案</strong></td></tr>
        
        	<tr>
            	<th>完成时间：</th>
                <td>
                    <input name="done_time" type="text" class="text date" value="<?php echo date("Y-m-d",($row['done_time']?$row['done_time']:time()));?>" size="12" readonly="true" title="年-月-日">
                    实际完成时间
                </td>
                
            </tr>        
        
        	<tr>
            	<th>结案报告：</th>
                <td>
                    <textarea name="report" cols="50" rows="10" id="report" style="width:95%;height:300px;"><?php echo $row['report'];?></textarea>
                    <script type="text/javascript">
                    var editor;
                    KindEditor.ready(function(K) {
                        editor = K.create('#report', {
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
            
            </tbody>
            
			<tr>
				<td></td>
				<td>
					<?php
						if ($action=="edit"){
							echo '<input name="action" type="hidden" id="action" value="update" />
							<input name="id" type="hidden" value="'.$id.'" />';
							echo '<button type="submit" name="Submit" class="submit">修改此项目</button>';
						}else{
							echo '<input name="action" type="hidden" id="action" value="add" />';
							echo '<button type="submit" name="Submit" class="submit">新增此项目</button>';
						}
					?>
				</td>
			</tr>
            
        </table>
		  
		<script type="text/javascript">
        Mo("input[name=state]").bind('change',function(){
			var val = Mo( this ).value();
			if( val == 2 ){
				Mo("*[data-extra='done']").show();	
			}else{
				Mo("*[data-extra='done']").hide();	
			}													   
		}).value("<?php echo isset($row['state']) ? $row['state'] : 1;?>").event('change');
		
        Mo("input[name=level]").value("<?php echo isset($row['level']) ? $row['level'] : 1;?>");
        </script>
        
		</form>
	
	</div>
		


<?php html_close();?>