<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("选票选项 - VeryIDE");
?>


	<?php

	$appid = Module :: get_appid();

	//module
	require 'config.php';
	require 'function.php';
	//require 'naver.php';
	
	//连接数据库
	System :: connect();
	
	$do = getgpc('do');

	$action = getgpc('action');
	$jump = getgpc('jump');
	
	$fid = getnum('fid',0);
	$gid = getnum('gid',0);
	
	//批处理列表
	$list = getgpc('list');
	$list = is_array($list) ? implode(",", $list) : $list;

	//上一页
	$jump = $_POST["jump"];
				
	//有无修改票数权限
	$permit = System :: check_func( 'notice-data-mod');
	
	if ($action){

		$name = getgpc('name');
		$sort = getgpc('sort');
		$state = getnum('state',0);
		$oid = getnum('id',0);
		$stat = getgpc("stat");
		$description = getgpc('description');
		
		$image = getgpc("image");
		$quote = getgpc('quote');

		switch($action){
			
			case "add":
			
				//检查权限
				System :: check_func( 'notice-list-add', false );
		
				//批量插入
				foreach( $name as $key => $val ){
						
					$image = Attach :: savefile( array( 'field'=> 'image', 'index'=> $key ) );
				
					$sql="INSERT INTO `mod:form_option`(name,sort,fid,gid,dateline,description,stat,state,quote,image) VALUES('".$val."',".intval($sort[$key],0).",".$fid.",".$gid.",".time().",'".$description[$key]."',".intval($stat,0).",".$state.",'".$quote[$key]."','".$image."')";
					
					System :: $db -> execute( $sql );
					
					$id = System :: $db -> getInsertId();
				}
				
				//生成缓存
				Cached :: form( $appid, $fid );
				
				System :: redirect("?do=list&fid=".$fid."&gid=".$gid,"信息提交成功!");
				
			break;
				
			case "edit":
			
				$sql="SELECT * FROM `mod:form_option` WHERE id=".$oid;
				$row = System :: $db -> getOne( $sql );
			
			break;
				
			case "update":
			
				//检查权限
				System :: check_func( 'notice-list-mod', false );
		
				$sql="UPDATE `mod:form_option` SET name='".$name."',sort=".intval($sort,0).",stat=".( !empty( $stat ) ? intval($stat,0) : 'stat' ).",description='".$description."',state=".$state.",quote='".$quote."',image='".$image."' WHERE id=".$oid;
				System :: $db -> execute( $sql );
								
				//生成缓存
				Cached :: form( $appid, $fid );
				
				System :: redirect("?do=list&fid=".$fid."&gid=".$gid,"信息修改成功!");
				
			break;
				
			case "delete":
			
				//检查权限
				System :: check_func( 'notice-list-del', false );
		
				System :: $db -> execute( "DELETE FROM `mod:form_option` WHERE id in(".$list.")" );
				
				System :: redirect("?do=list&fid=".$fid."&gid=".$gid,"信息删除成功!");
				
			break;
			
			//重新计算票数
			case "stat":
			
				//检查权限
				System :: check_func( 'notice-list-mod', false );
		
				$sql="SELECT id FROM `mod:form_option` WHERE gid=".$gid;
				
				$result = System :: $db -> getAll( $sql );

        		foreach( $result as $row ){
					
					//投票值
					$stat = notice :: stat( $fid, $gid, $row['id'] );
					
					$sql="UPDATE `mod:form_option` SET stat=".$stat." WHERE id = ".$row['id'];
					
					System :: $db -> execute( $sql );
					
				}
								
				//生成缓存
				Cached :: form( $appid, $fid );
				
				System :: redirect("?do=list&fid=".$fid."&gid=".$gid,"选项统计已重新计算!");
				
			break;
			
			case "state":
			
				//检查权限
				System :: check_func( 'notice-list-mod', false );
		
				$sql="UPDATE `mod:form_option` SET state=".$state." WHERE id in(".$list.")";
				System :: $db -> execute( $sql );
								
				//生成缓存
				Cached :: form( $appid, $fid );
				
				System :: redirect("?do=list&fid=".$fid."&gid=".$gid,"选项状态更改成功!");
				
			break;
			
			//批量修改
			case "mass":
			
				//检查权限
				System :: check_func( 'notice-list-mod', false );
				
				foreach( $name as $id => $val ){
					$sql="UPDATE `mod:form_option` SET name='".$val."',sort=".intval($sort[$id],0).",stat=".( is_numeric( $stat[$id] ) ? intval($stat[$id],0) : 'stat' )." WHERE id=".$id;
					System :: $db -> execute( $sql );					
				}
				
				System :: redirect( $jump,"信息批量修改成功!");
				
			break;
							
		}		
		
  	}	
	
	//加载缓存
	Cached :: loader( $appid, 'form.'.$fid );
	?>

	<?php
	
	echo '
    <div class="item">
        <strong>'.$_CACHE[$appid]['form']["name"].' &gt;&gt; '.$_CACHE[$appid]['group'][$gid]["name"].'</strong> 	
    </div>
    
    <ul id="naver">
        <li'.($do=='new'?' class="active"':'').'><a href="?do=new&fid='.$fid.'&gid='.$gid.'">'.($action=="edit"?'编辑':'新增').'子选项</a></li>
        <li'.($do=='list'?' class="active"':'').'><a href="?do=list&fid='.$fid.'&gid='.$gid.'">管理子选项</a></li>
    </ul>
	';
	?>
    
    <div id="box">
    
    <?php 
	if($do=="new"){
		
		$config = $_CACHE[$appid]['group'][$gid]["config"];
		
		//展示模板
		$template = $config["GROUP_STYLE"] ? $_G['module']['notice']["style"][$config["GROUP_STYLE"]] : $config["GROUP_TEMPLATE"];
		
	?>
		<?php    
        echo loader_script(array(VI_BASE."static/js/mo.ubb.js"),'utf-8',$_G['product']['version']);
		echo loader_script(array(VI_BASE.'source/editor/kindeditor.js',VI_BASE.'source/editor/lang/zh_CN.js'),'utf-8',$_G['product']['version']);
        ?>

    	<form action="?fid=<?php echo $fid;?>&gid=<?php echo $gid;?>" method="post" data-valid="true" enctype="multipart/form-data">
        
        <table cellpadding="0" cellspacing="0" class="form">
        
        	<tr>
            	<th>目标类型：</th>
                <td>
                <?php
				echo $_G['module']['notice']["group"][$_CACHE[$appid]['group'][$gid]["type"]];
				?>
                </td>
            </tr>
            
            <?php
			if( $config["GROUP_STAT"] && !$action ){
			?>
        
            <tr>
                <th>起始票数：</th>
                <td>
                <?php echo $config["GROUP_STAT"];?>
                <input name="stat" type="hidden" id="stat" value="<?php echo $config["GROUP_STAT"];?>" />
                </td>
            </tr>
            
            <?php
			}
			?>
            
            <?php
			//批量添加
			if( $action == "" ){			
			?>
        
        	<tr>
            	<th>批量创建：</th>
                <td>
				
                	<div id="tag-box">
                    
                    <table id="tag-<?php echo $i;?>" cellpadding="0" cellspacing="1" border="0" class="frame">
                        <tr>
                            <td>名称</td>
                            <td>
	                            <input name="name[]" type="text" class="text" value="<?php echo $key;?>" size="35" data-valid-name="标签名称" data-valid-empty="yes" />
                                <input name="sort[]" type="text" size="10" data-valid-name="标签排序" data-valid-number="no" title="显示排序" class="text digi" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '标签排序' : (function(){ var a = []; for(var i=-9;i<=60;i++){a.push(i);} return a; })() }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true" value="<?php echo $row["sort"] ? $row["sort"] : "0";?>" />
                            </td>
                            <td align="right">
	                            <a onclick="javascript:Planer.move(this,'up',4);void(0);">上移</a>
								<a onclick="javascript:Planer.move(this,'down',4);void(0);">下移</a>
								<a onclick="javascript:Planer.remove(this,4);void(0);">删除</a>
                            </td>
                        </tr>
                        <?php
						if( stripos($template,"{LINK}") !== false ){
						?>
                        <tr>
                            <td>链接</td>
                            <td colspan="2">
	                            <input name="quote[]" type="text" class="text" value="<?php echo $val;?>" size="51" />
                            </td>
                        </tr>
                        <?php
						}
						?>
														
						<?php
						if( stripos($template,"{IMAGE}") !== false ){
						?>
                        <tr>
                            <td>图片</td>
                            <td colspan="2">
	                            <input name="image[]" type="file" class="text" value="<?php echo $val;?>" size="40" />
                            </td>
                        </tr>
                        <?php
						}
						?>
						
						<?php
						if( stripos($template,"{DESC}") !== false ){
						?>
                        <tr>
                            <td>描述</td>
                            <td colspan="2">
	                            <textarea name="description[]" cols="70" rows="4"><?php echo $row["description"];?></textarea>
                            </td>
                        </tr>
                        <?php
						}
						?>
                    </table>
                    
                    </div>
                
                    <script type="text/javascript">
                    var Planer = new Mo.Planer(Mo.$("tag-box"));
                    var Remove = function( table ){
	                    Mo( "input", table ).each(function( ){ this.name = this.name.replace(/\[\d*\]/,'['+ Mo.Date().time() +']'); });
                    }
                    </script>
                    
                    <a onclick="javascript:Planer.copy( Remove );void(0);">添加</a>
                
                </td>
            </tr>
            
            <?php
			}else{			
			?>
        
        	<tr>
            	<th>标签名称：</th>
                <td>
                	<input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="50" data-valid-name="标签名称" data-valid-empty="yes" />
               		<input name="sort" type="text" size="35" data-valid-name="标签排序" data-valid-number="no" title="显示排序" class="text digi" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '标签排序' : (function(){ var a = []; for(var i=-9;i<=60;i++){a.push(i);} return a; })() }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true" value="<?php echo $row["sort"] ? $row["sort"] : "0";?>" />
                </td>
            </tr>
            
            <?php
			//if( stripos($template,"{DESC}") !== false ){
			?>
        
        	<tr>
            	<th>标签描述：</th>
                <td>
				<textarea name="description" cols="50" rows="10" id="description" style="width:670px;height:200px;"><?php echo $row['description'];?></textarea>
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
            
            <?php
			//}
			?>
            
            <?php
			if( stripos($template,"{IMAGE}") !== false ){
			?>
        
            <tr text='填写此表单的关键字词,将用于显示与此相关的表单'>
                <th>上传图片：</th>
                <td>
                <script type="text/javascript">
                new Serv.Upload("image","<?php echo $row['image'];?>",{'format':['<?php echo implode("','",$_G['upload']['image']);?>'],'again':true,'recovery':true,'input':true});
                </script>
                </td>
            </tr>
            
            <?php
			}
			?>
            
            <?php
			if( stripos($template,"{LINK}") !== false ){
			?>
        
            <tr text='填写此表单的关键字词,将用于显示与此相关的表单'>
                <th>链接地址：</th>
                <td>
                <input name="quote" type="text" class="text" id="quote" value="<?php echo $row["quote"];?>" size="60" />
                </td>
            </tr>
            
            <?php
			}
			?>
        
            
            <?php
			}			
			?>
        
        	<?php
			if( $_G['licence']['type'] != 'free' && $permit ){
				
				$stat = $_CACHE[$appid]['form']["stat"] <= 10000 ? notice :: stat( $fid, $gid, $row['id'] ) : '-';
				
			?>
        	<tr>
            	<th>当前票数：</th>
                <td>
                    <input name="stat" type="text" class="text" value=""> 
                    当前 <?php echo $row['stat'];?> 票（真实票数：<?php echo $stat;?>），如果不修改，请保持为空
                </td>
                
            </tr>
            
            <?php
			}			
			?>
        
        	<tr>
            	<th>项目状态：</th>
                <td>
                    <label>
                    <input name="state" type="radio" class="radio" value="1" checked> 
                    完全开放
                    </label>
                    <label>
                    <input name="state" type="radio" class="radio" value="0">
                    禁用项目
                    </label>
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
                       echo '<button type="submit" name="Submit" class="submit">修改项目</button>';
                    }else{
						echo '
						<input name="action" type="hidden" id="action" value="add" />
						';
                        echo '<button type="submit" name="Submit" class="submit">新增项目</button>';
                    }
                    ?>
                </td>
            </tr>
            
          </table>
          
          <?php
		  if( isset($row['config']) ){
			require(VI_ROOT."module/member/config.php");
		  ?>
          
          <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
          
          	<tr><td colspan="2" class="section"><strong>报名信息</strong></td></tr>
                    
			<?php
			
			$config = fix_json( $row['config'] );
            
            //用户数据
            $i = 0;
            foreach ( $config as $fied => $value){
				
				$fied = strtolower( $fied );
                
                //转换数据					
                switch($fied){
                
                    case 'gender':						
                        $value = $_G['project']['gender'][$value];
                    break;
                
                    case 'marriage':						
                        $value = $_G['project']['marriage'][$value];
                    break;
                
                    case 'modify':
                    case 'dateline':
                        $value = $value ? date("Y/m/d H:i:s",$value) : '-';
                    break;
                
                }
                
                echo '<tr class="'.($i%2?'title':'line').'"> <td width="100" align="right">'.$_G['module']['member']['fieds'][$fied].'</td> <td class="text-yes"> '.$value.' </td></tr>';
                
                $i++;
            }
            
            
            ?>
        </table>
          
          <?php
		  }
		  ?>
        
        <script type="text/javascript">
        	Mo("input[name=state]").value("<?php echo isset($row['state']) ? $row['state'] : 1;?>");
        </script>
        
	 </form>
	
    <?php
	}elseif($do=="list"){
	?>
    
    <form name="post-form" id="post-form" method="post" action="?fid=<?php echo $fid;?>&gid=<?php echo $gid;?>">
		<input name="action" id="action" type="hidden" value="mass" />
		<input name="state" id="state" type="hidden" value="" />
		
		<input name="jump" type="hidden" value="" />
		<script>Mo('#post-form input[name=jump]').value( location.href );</script>

		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table" id="table">
	
			<tr class="thead">
            <td width="10"><input type="checkbox" class="checkbox"></td>
            <td>ID</td>
            <td width="65">排序</td>
			<td>标签名称</td>
			<td>当前票数<?php echo $_G['licence']['type'] != 'free' && $permit ? '，留空不修改' : '';?></td>
			<td>有效值票数</td>
			<td>链接地址</td>
			<td>添加时间</td>
            <td>状态</td>
			<td width="80">操作</td>
			</tr>
			<?php
				$sql="SELECT * FROM `mod:form_option` WHERE gid=".$gid." ORDER BY state desc,sort ASC";
				
				$result = System :: $db -> getAll( $sql );

				foreach( $result as $row ){
					
					//投票值
					/*
					$val = '"G-'.$gid.'":"'.$row['id'].'"';
					$val = '"G-'.$gid.'":"'.$row['id'].',%"';
					$val = '"G-'.$gid.'":"%,'.$row['id'].'"';
					$val = '"G-'.$gid.'":"%,'.$row['id'].',%"';
					
					//计算准确值
					$sql = "SELECT count(id) FROM `mod:form_data` WHERE config like '%".$val."%' and fid = ".$fid;
					
					echo $sql;
					
					$stat = System :: $db -> getValue( $sql );
					*/
					
					$stat = $_CACHE[$appid]['form']["stat"] <= 10000 ? notice :: stat( $fid, $gid, $row['id'] ) : '-';
					
					?>
					
					<tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" data-edit="<?php echo $appid;?>.option.php?do=new&action=edit&fid=<?php echo $fid;?>&gid=<?php echo $gid;?>&id=<?php echo $row['id'];?>&jump={self}">
                        <td title="<?php echo $row['id'];?>">
                            <input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
                        </td>
						<td><?php echo $row['id'];?></td>
						<td><input type="text" class="text" name="sort[<?php echo $row['id'];?>]" size="4" value="<?php echo $row["sort"];?>" /></td>
						<td><input type="text" class="text text-yes" name="name[<?php echo $row['id'];?>]" size="22" value="<?php echo $row['name'];?>" /></td>
						<td class="text-yes"><?php if( $_G['licence']['type'] != 'free' && $permit ) { ?><input type="text" class="text text-yes" name="stat[<?php echo $row['id'];?>]" size="5" value="" /> <?php }?><?php echo $row["stat"];?></td>
						<td class="<?php echo ( $stat != $row["stat"] ? 'text-key' : 'text-yes' );?>"><?php echo $stat;?></td>
						<td><var data-type="file"><?php echo $row['image'];?></var><a target="_blank" href="<?php echo $row["quote"];?>"><?php echo $row["quote"];?></a></td>
						<td><?php echo date('y-m-d',$row['dateline']);?></td>
                        <td>
                            <?php echo $_G['project']['state'][$row['state']];?>           
                        </td>
						<td>
							<button type="button" class="editor" data-url="?do=new&action=edit&fid=<?php echo $row["fid"];?>&gid=<?php echo $row["gid"];?>&id=<?php echo $row['id'];?>">修改</button>
                            <button type="button" class="normal" data-url="?do=list&action=delete&fid=<?php echo $row["fid"];?>&gid=<?php echo $row["gid"];?>&list=<?php echo $row['id'];?>">删除</button>
						</td>
					</tr>
                    
                    <?php
                    
				}
		
			if( count( $result ) == 0 ){
				echo '<tr><td colspan="10" class="notice">没有检索到相关子选项，<a href="?fid='.$fid.'&gid='.$gid.'&do=new">创建一个？</a></td></tr>';
			}
	  
			?>
		
			<tr class="tfoot">
				<td colspan="10" class="first">
                    <span class="y">
                    	<button type="submit" class="button">保存修改</button>
                    	<button type="button" class="cancel" onclick="if(confirm('确定要重新计算票数吗?')){ location.href='?do=list&fid=<?php echo $fid;?>&gid=<?php echo $gid;?>&action=stat'; }">重新计算</button>
                    </span>
					<?php echo $_G['module']['notice']['tool'];?>
				</td>
			</tr>
        </table>
    
    </form>
	
    <?php 
	
	}
	
	//关闭数据库
	System :: connect();
	?>
	
    </div>
    	


<?php html_close();?>