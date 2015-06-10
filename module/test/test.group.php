<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("试题选项组 - VeryIDE");
?>


	<?php

	$appid = Module :: get_appid();

	//module
	//require 'function.php';
	require 'config.php';
	//require 'naver.php';

	//连接数据库
	System :: connect();

	$do = getgpc('do');
	$jump = getgpc('jump');
	$action = getgpc('action');
	$fid = getnum('fid',0);
	$gid = getnum('gid',0);	
	
	//批处理列表
	$list = getgpc('list');
	$list = is_array($list) ? implode(",", $list) : $list;
	
	if ($action){

		$name = getgpc('name');
		$sort = getnum("sort",0);
		$state = getnum('state',0);
		$type = getgpc("type");
		$description = getgpc('description');
		
		//配置
		$config = $_POST['config'];
		$config = format_json( fix_json( $config, TRUE ) );
	
		switch($action){
				
			case "add":
			
				//检查权限
				System :: check_func( 'test-list-add', false );		
			
				if (!$fid || !$name){
					$_G['project']['message'] ="表单必选项不能为空！";
					
				}else{
				
					$sql="INSERT INTO `mod:form_group`(name,aid,account,state,fid,sort,type,dateline,description,config,stat) 
					values('".$name."',".$_G['manager']['id'].",'".$_G['manager']['account']."',".$state.",".$fid.",".$sort.",'".$type."',".time().",'".$description."','".addslashes($config)."',0)";
				
					System :: $db -> execute( $sql );
					
					$id = System :: $db -> getInsertId();
					
					//生成缓存
					Cached :: form( $appid, $fid );
					
					System :: redirect("?do=list&fid=".$fid."&mark=".$id,"信息提交成功!");
				}
				
			break;
				
			case "update":
			
				//检查权限
				System :: check_func( 'test-list-mod', false );		
			
				$sql="UPDATE `mod:form_group` SET name='".$name."',state=".$state.",type='".$type."',sort=".$sort.",description='".$description."',config='".addslashes($config)."' WHERE id=".$gid;
				System :: $db -> execute($sql);
				
				//生成缓存
				Cached :: form( $appid, $fid );
				
				System :: redirect("?do=list&gid=".$gid."&fid=".$fid,"选项组修改成功!");
				
			break;
				
			case "edit":
						
				$sql="SELECT *  FROM `mod:form_group` WHERE id=".$gid;
				$row = System :: $db -> getOne( $sql );

               	if( $row ){					
					//配置
					$config = fix_json( $row['config'] );
				}
				
			break;
				
			case "delete":
			
				//检查权限
				System :: check_func( 'test-list-del', false );		
			
				System :: $db -> execute("DELETE FROM `mod:form_group` WHERE id=".$gid);
				System :: $db -> execute("DELETE FROM `mod:form_option` WHERE gid=".$gid);
				
				//生成缓存
				Cached :: form( $appid, $fid );
				
				System :: redirect("?do=list&fid=".$fid,"选项组删除成功!");
			
			break;
			
			case "state":
			
				//检查权限
				System :: check_func( 'test-list-mod', false );		
			
				$sql="UPDATE `mod:form_group` SET state=".$state." WHERE id in(".$list.")";
				System :: $db -> execute($sql);
				
				//生成缓存
				Cached :: form( $appid, $fid );

				System :: redirect($jump,"选项组修改成功!");
				
			break;
		}
		
	}
	  
	//加载缓存
	Cached :: loader( $appid, 'form.'.$fid );
	?>


	<?php
	
	echo '
    <div class="item">
        <strong>'.$_CACHE[$appid]['form']["name"].'</strong> 	
    </div>
    
    <ul id="naver">
        <li'.($do=='new'?' class="active"':'').'><a href="?do=new&fid='.$fid.'">'.($action=="edit"?'编辑':'新增').'选项组</a></li>
        <li'.($do=='list'?' class="active"':'').'><a href="?do=list&fid='.$fid.'">管理选项组</a></li>
    </ul>
	';
	?>
    
    <?php 
	if($do=="new"){
	?>
    
	<?php    
    echo loader_script(array(VI_BASE."static/js/mo.ubb.js"),'utf-8',$_G['product']['version']);
	echo loader_script(array(VI_BASE.'source/editor/kindeditor.js',VI_BASE.'source/editor/lang/zh_CN.js'),'utf-8',$_G['product']['version']);
    ?>
    <script type="text/javascript">
		function doChanged(s){
			var b = ['radio','checkbox'];
			if( Mo.Array( b ).indexOf( s ) > -1 ){                
				Mo( '#break *' ).enabled();                
			}else{                
				Mo( '#break *' ).disabled();
			}
			if( s == 'compart' ){
				Mo( '#score *' ).disabled();
			}else{
				Mo( '#score *' ).enabled();
			}
		}
		
	</script>
    
    <div id="box">

	<form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-valid="true">
		<input name="fid" type="hidden" id="fid" value="<?php echo $fid;?>" />
        
        <table cellpadding="0" cellspacing="0" class="form">
        
        	<tr>
            	<th>项目标题：</th>
                <td><input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="60" data-valid-name="项目标题" data-valid-empty="yes" /></td>
                
            </tr>
        
        	<tr>
            	<th>项目类型：</th>
                <td>
                	<?php
					foreach( $_G['module']['test']["group"] as $key => $val ){
						echo '<label> <input type="radio" class="radio" name="type" value="'.$key.'" checked onclick="doChanged(this.value)">'.$val.'</label>';
					}
					?>
                    <!--label> <input type="radio" class="radio" name="type" value="radio" checked onclick='doChanged(this.value)'>	单选</label>
                    <label> <input type="radio" class="radio" name="type" value="checkbox" onclick='doChanged(this.value)'>	复选</label>
                    <label> <input type="radio" class="radio" name="type" value="select" onclick='doChanged(this.value)'>	下拉选框</label-->
                </td>
                
            </tr>
            
        	<tr>
            	<th>项目属性：</th>
                <td>
                    
                    <table cellpadding="0" cellspacing="1" border="0" class="gird">
                        <tr>
                            <td id="break">
                            	换行设置<br />
                                <select name="config[GROUP_BREAK]">
                                    <option value=0>无换行</option> 
                                    <option value=1>每隔1项换行</option> 
                                    <option value=2>每隔2项换行</option> 
                                    <option value=3>每隔3项换行</option> 
                                    <option value=4>每隔4项换行</option> 
                                    <option value=5>每隔5项换行</option> 
                                    <option value=6>每隔6项换行</option> 
                                    <option value=7>每隔7项换行</option> 
                                    <option value=8>每隔8项换行</option> 
                                    <option value=9>每隔9项换行</option> 
                                </select>
                            </td>
                            <td id="score">
                            本题分数<br />
                            <input name="config[GROUP_SCORE]" type="text" class="text" value="<?php echo $config["GROUP_SCORE"];?>" size="20" data-valid-name="本题分数" data-valid-number="no">
                            <var data-type="tip">【注意】请在用户答题前设置好分数，以后的修改不会影响用户的分数统计</var>
                            </td>
                        </tr>
                    </table>
	     	
                </td>
                
            </tr>
        
        	<tr>
            	<th>扩展属性：</th>
                <td>
                    
                    <table cellpadding="0" cellspacing="1" border="0" class="gird">
                        <tr>
                            <td>
                            空值验证<br />
                            <label>
                                <input type="checkbox" class="checkbox" name="config[GROUP_MUST]" value="Y" />
                                必选项，不能为空
                            </label>
                            </td>
                            <td>
                            显示排序<br />
                            <input name="sort" type="text" class="text" value="<?php echo $row["sort"];?>" size="20" data-valid-name="项目排序" data-valid-number="no">
                            <var data-type="tip">将以从小到大的顺序进行显示</var>
                            </td>
                        </tr>
                    </table>
                    
                </td>
                
            </tr>
            
        	<tr>
            	<th>简要介绍：</th>
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
                        <input name="gid" type="hidden" id="gid" value="'.$row['id'].'" />
                        ';
                        echo '<button type="submit" name="submit" class="submit">修改此项目</button>';
                    }else{
                    
                        echo '
                        <input name="action" type="hidden" id="action" value="add" />
                        ';
                        if (!$fid){
                            echo '<button type="button" name="submit" class="submit">请先选择相应的调查表</button>';
                        }else{
                            echo '<button type="submit" name="submit" class="submit">新增此项目</button>';
                        }
                    }
                    ?>
                </td>
                
            </tr>
        
        </table>        
		<script type='text/javascript'>
            Mo("select[name='config[GROUP_BREAK]']").value("<?php echo $config["GROUP_BREAK"];?>");            
           
            Mo("input[name='config[GROUP_MUST]']").value("<?php echo isset($config) ? $config["GROUP_MUST"] : 'Y';?>"); 
            Mo("input[name='config[GROUP_VERIFY]']").value("<?php echo $config["GROUP_VERIFY"];?>");
			
            Mo("input[name=state]").value("<?php echo isset($row['state']) ? $row['state'] : 1;?>");
			Mo("input[name=type]").value("<?php echo $row["type"]?$row["type"]:'radio';?>");
            
            doChanged("<?php echo $row["type"]?$row["type"]:'radio';?>");
            
        </script>
        
	</form>
    
    </div>
	
    <?php
	}elseif($do=="list"){
	?>
    
	<!--用于动作处理_开始-->
	<form name="post-form" id="post-form" method="post">
		<input name="action" id="action" type="hidden" value="" />        
		<input name="state" id="state" type="hidden" value="" />

		<input name="jump" type="hidden" value="<?php echo $_G['runtime']['absolute'];?>" />
	
      <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table" id="table">
        <tr class="thead">
			<td width="10"><input type="checkbox" class="checkbox"></td>
            <td>ID</td>
            <td>排序</td>
            <td>项目名称</td>
            <td>子选项 <var data-type="tip"><strong>子选项</strong><br />本项目组供用户选择的选项</var></td>
            <td>类型</td>
            <td>有效性</td>
            <td>分数</td>
            <td>正确答案</td>
            <td>添加时间</td>
            <td width="70">状态</td>
            <td width="80">操作</td>
        </tr>
        
        <?php
        $sql="SELECT * FROM `mod:form_group` WHERE fid=".$fid." ORDER BY sort ASC";
        
        $result = System :: $db -> getAll( $sql );
		
		$score = 0;

        foreach( $result as $row ){
            
            //配置
        	$config = fix_json( $row['config'] );
			
			//统计页面数量
			$count = System :: $db -> getValue( "SELECT count(id) FROM `mod:form_option` WHERE gid=".$row['id'] );
			
			//正确案例
			$right = $row["selected"] ? System :: $db -> getAll( "SELECT name FROM `mod:form_option` WHERE id in(".$row["selected"].")" ) : array();
			
			//试题总分
			$score = $score + $config["GROUP_SCORE"];
            
            ?>
            <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" data-edit="<?php echo $appid;?>.group.php?do=new&action=edit&fid=<?php echo $fid;?>&gid=<?php echo $row['id'];?>&jump={self}">
				<td title="<?php echo $row['id'];?>">
					<input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
				</td>
                <td><?php echo $row['id'];?></td>
                <td><?php echo $row["sort"];?></td>
                <td><strong class="text-yes"><?php echo $row['name'];?></strong></td>
                <td>                        
                <?php 
                if ( in_array($row["type"],array("radio","checkbox","select")) ){
                ?>
                
                <a id="mark-<?php echo $row['id'];?>" href="<?php echo $appid;?>.option.php?do=list&fid=<?php echo $row["fid"];?>&gid=<?php echo $row['id'];?>" target="_dialog" title="表单子选项" data-width="80%" data-height="60%" class="control">选项组 <?php echo $count;?></a>
                
                <?php
                }
                ?>
                </td>
                <td>
                    <?php echo $_G['module']['test']["group"][$row["type"]];?>
                    <?php
                    if( $config["GROUP_INPUT"]=='Y' ){
                        echo " + 输入";
                    }
                    ?>
                </td>
                <td><?php echo ($config["GROUP_MUST"]=='Y'?'必选项':'可以空');?></td>
                <td><strong class="text-key"><?php echo $config["GROUP_SCORE"];?></strong></td>
                <td>
				<?php
				$x = 1;
                foreach( $right as $val ){
					 echo '<sup class="text-key">('.$x.')</sup> <strong class="text-yes">'.$val["name"].'</strong> ';
					 $x++;
				}
				?>
                </td>
                <td><?php echo date('y-m-d',$row['dateline']);?></td>
                <td><?php echo $_G['project']['state'][$row['state']];?></td>
                <td>
                <button type="button" class="editor" data-url="?do=new&action=edit&fid=<?php echo $row["fid"];?>&gid=<?php echo $row['id'];?>">修改</button>
                    <button type="button" class="normal" data-url="?do=list&action=delete&fid=<?php echo $row["fid"];?>&gid=<?php echo $row['id'];?>">删除</button>
                </td>
            </tr>
            <?php
        
        }
		
		if( count( $result ) == 0 ){
			echo '<tr><td colspan="10" class="notice">没有检索到相关选项组，<a href="?fid='.$fid.'&do=new">创建一个？</a></td></tr>';
		}
	  
		?>
		<tr class="tfoot">
            <td colspan="10" class="first">
                <?php echo $_G['module']['test']['tool'];?>
            </td>
            <td>
                试题总分
            </td>
            <td>
            	<strong class="text-key"> <?php echo $score;?> </strong>
            </td>
		</tr>
	</table>
    
    <?php 
	}

	//关闭数据库
	System :: connect();
	
	?>
    </form>
	<!--用于动作处理_结束-->
    


<?php html_close();?>