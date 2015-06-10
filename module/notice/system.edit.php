<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("制度编辑 - VeryIDE");
?>


	<?php

	//载入模块配置并生成菜单
	$appid = Module :: get_appid();
	
	echo Module :: get_context( $appid );

	//////////////////////////////////




    
	//连接数据库
	System :: connect();
	
	$jump = getgpc('jump');
	$action = getgpc('action');
	$fid = getnum('fid',0);
	$gid = getnum('gid',0);

	
	if ($action){

		$title = getgpc('title');
		$state = getnum('state',0);
		$content = getgpc('content');
        $listorder = getgpc('listorder');
		$thumb = getgpc("thumb");
		$category = getgpc('category');
		
		//注册时间
		if( $_POST['config']['FORM_REGDATE'] ){
			$_POST['config']['FORM_REGDATE'] = strtotime( $_POST['config']['FORM_REGDATE']." 0:0:0" );
		}
		
		//配置
		$config = clear_array( $_POST['config'] );
		$config = format_json( fix_json( $config ) );
		
		//开始时间
		$start = gettime('start');
		
		//结束时间
		$expire = gettime('expire');
		
		switch($action){
			
			case "add":
			
				//检查权限
				System :: check_func( 'notice-list-add', false );		
			
				$sql="INSERT INTO module_notice(title,aid,account,state,content,dateline,start,expire,thumb,appid,config,ip,category,listorder) VALUES('".$title."',".$_G['manager']['id'].",'".$_G['manager']['account']."',".$state.",'".$content."',".time().",".$start.",".$expire.",'".$thumb."','".$appid."','".addslashes($config)."','".GetIP()."','".$category."','".$listorder."')";

				System :: $db -> execute( $sql );
				
				$fid = System :: $db -> getInsertId();
				
				//生成缓存
				Cached :: form( $appid, $fid );
				
				System :: redirect($appid.".list.php?mark=".$fid,"信息提交成功!");
				
			break;
				
			case "update":
			
				//检查权限
				System :: check_func( 'notice-list-mod', false );		
			
				$sql="UPDATE module_notice SET title='".$title."',state=".$state.",content='".$content."',thumb='".$thumb."',config='".addslashes($config)."',start=".$start.",expire=".$expire.",category='".$category."',`modify`=".time()." WHERE id=".$fid;
				System :: $db -> execute($sql);
				//生成缓存
				Cached :: form( $appid, $fid );
								
				System :: redirect($jump,"信息修改成功!");
			
			break;
			
			case "edit";
			
				$sql="SELECT * FROM module_notice WHERE id=".$fid;
				$row = System :: $db -> getOne( $sql );

				if( $row ){				
					$config = fix_json($row['config']);
					$config = array_map('dhtmlspecialchars', $config);
					$row = array_map('dhtmlspecialchars', $row);
				}
			
			break;
		}
		
	}

    
    ?>
    
	<?php    
      
	echo loader_script(array(VI_BASE.'source/editor/kindeditor.js',VI_BASE.'source/editor/lang/zh_CN.js'),'utf-8',$_G['product']['version']);
    ?>

	<?php
	
	//显示权限状态
	switch($action){
		case "":
			echo System :: check_func( 'notice-list-add',true);
		break;
		
		case "edit":
			echo System :: check_func( 'notice-list-mod',true);
		break;
	}
	
	?>

	<div id="box">
	    <form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-mode="edit" data-valid="true">
	                            
        <table cellpadding="0" cellspacing="0" class="form">
    
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>
    
            <tr>
                <th>通知标题：</th>
                <td>
                    <input name="title" type="text" class="text" id="title" value="<?php echo $row['title'];?>" size="65" data-valid-name="通知标题" placeholder="请输入通知标题" data-valid-empty="yes" />
                    <select name="category" id="category">
                    <?php
                        $sql = mysql_query("SELECT * FROM module_common_category WHERE parent=0 AND appid='notice' ORDER BY sort ASC");
                        while($t=mysql_fetch_array($sql)){
                        ?>
                        <option value="<?php echo $t['id'];?>" <?php if($t['id'] == $row['category'])echo "selected=selected" ;?>>
                            <?php echo $t['name'];?>
                        </option>
                    <?php
                        }
                    ?>
                    </select>
                </td>
            </tr>
        
            <tr>
                <th>简要介绍：</th>
                <td>
                	<textarea name="content" cols="50" rows="10" id="content" style="width:670px;height:250px;"><?php echo $row['content'];?></textarea>
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
                <th>表单状态：</th>
                <td>
                    <label>
                    <input type="radio" class="radio" name="state" value="1" checked> 
                    开放表单
                    </label>
                    <label>
                    <input type="radio" class="radio" name="state" value="0">
                    关闭表单
                    </label>
                </td>
                
            </tr>
            
            <tr>
                <th>预览图片：</th>
                <td>
                <script type="text/javascript">
                new Serv.Upload("thumb","<?php echo $row["thumb"];?>",{'format':['<?php echo implode("','",$_G['upload']['image']);?>'],'again':true,'recovery':true,'input':true});
                </script>
                </td>
            </tr>

            <tr>
                <th>排序值：</th>
                <td>
                    <input type="number" name="listorder" id="listorder" value="<?php echo $row['listorder'];?>">
                    <label>排序值越大越在前面</label>
                </td>
            </tr>
            
            <?php
            
            //载入表单高级选项
            Module :: exists( 'passport' ) && require("include.form.php");				
            
            ?>              
          
            <tbody>
                <tr>
                    <td></td>
                    <td>
                        <?php
                            if ($action=="edit"){
                                echo '
                                <input name="action" type="hidden" id="action" value="update" />
                                <input name="fid" type="hidden" id="fid" value="'.$fid.'" />
                                ';
                                echo '<button type="submit" name="Submit" class="submit">修改此通知</button>';
                            }else{
                                echo '<input name="action" type="hidden" id="action" value="add" />';
                                echo '<button type="submit" name="Submit" class="submit">新增此通知</button>';
                            }
                        ?>
                    </td>				
                </tr>
            </tbody>
        
        </table>
	 </form>
	</div>
	


<?php html_close();
//关闭数据库
    System :: connect();
?>