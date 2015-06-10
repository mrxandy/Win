<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("辩论编辑 - VeryIDE");
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

		$name = getgpc('name');
		$state = getnum('state',0);
		$tags = getgpc('tags');
		$skin = getgpc("skin");
		$thumb = getgpc("thumb");
		$description = getgpc('description');
		$quote = getgpc('quote');
		$category = getgpc('category');
		
		//配置
		$config = clear_array( $_POST['config'] );
		
		//删除空白字符
		$config['POSITIVE_POINT'] = preg_replace("/([\r\n\t\f])/"," ",$config['POSITIVE_POINT']);
		$config['NEGATIVE_POINT'] = preg_replace("/([\r\n\t\f])/"," ",$config['NEGATIVE_POINT']);
		
		//编辑前加载数据
		if( $action == "update" ){
			
			//加载缓存
			if( !Cached :: loader($appid,"form.".$fid) ){
				exit('Cache Error!');
			}else{
				$fconfig = $_CACHE[$appid]['form']["config"];	
			}
			
			//正方
			$config["POSITIVE_RESULT"] = $fconfig["POSITIVE_RESULT"];
			
			//反方
			$config["NEGATIVE_RESULT"] = $fconfig["NEGATIVE_RESULT"];
		}
		
		$config = format_json( fix_json( $config ) );
		
		//var_dump($config);
		//exit;	
		
		//开始时间
		$start = gettime('start');
		
		//结束时间
		$expire = gettime('expire');
		
		switch($action){
			case "add":
			
				//检查权限
				System :: check_func( 'pk-list-add', false );		
			
				$sql="INSERT INTO `mod:form_form`(name,aid,account,state,description,tags,dateline,start,expire,skin,quote,thumb,appid,config,ip,stat,category) VALUES('".$name."',".$_G['manager']['id'].",'".$_G['manager']['account']."',".$state.",'".$description."','".$tags."',".time().",".$start.",".$expire.",'".$skin."','".$quote."','".$thumb."','".$appid."','".addslashes($config)."','".GetIP()."',0,'".$category."')";
				
				System :: $db -> execute( $sql );
				
				$fid = System :: $db -> getInsertId();
				
				//生成缓存
				Cached :: rows( $appid, "SELECT * FROM `mod:form_form` WHERE id=".$fid, array( 'alias' =>'form', 'jsonde' => array('config') ) );
				
				System :: redirect($appid.".list.php?mark=".$id,"信息提交成功!");
				
			break;
				
			case "update":
			
				//检查权限
				System :: check_func( 'pk-list-mod', false );		
			
				$sql="UPDATE `mod:form_form` SET name='".$name."',state=".$state.",description='".$description."',tags='".$tags."',skin='".$skin."',quote='".$quote."',thumb='".$thumb."',config='".addslashes($config)."',start=".$start.",expire=".$expire.",category='".$category."',`modify`=".time().",mender='".$_G['manager']['account']."' WHERE id=".$fid;
				System :: $db -> execute($sql);
				
				//生成缓存
				Cached :: rows( $appid, "SELECT * FROM `mod:form_form` WHERE id=".$fid, array( 'alias' =>'form', 'jsonde' => array('config') ) );
				
				System :: redirect($jump,"信息修改成功!");
			
			break;
			
			case "edit";
			
				$sql="SELECT * FROM `mod:form_form` WHERE id=".$fid;
				$row = System :: $db -> getOne( $sql );

				if( $row ){	
					$config = fix_json($row['config']);	
					$config = array_map('dhtmlspecialchars', $config);
					$row = array_map('dhtmlspecialchars', $row);					
				}
			
			break;
		}
		
	}
	  
    //关闭数据库
    System :: connect();
    
    ?>
    
 	<?php    
      
	echo loader_script(array(VI_BASE.'source/editor/kindeditor.js',VI_BASE.'source/editor/lang/zh_CN.js'),'utf-8',$_G['product']['version']);
    ?>

	<?php
	
	//显示权限状态
	switch($action){
		case "":
			echo System :: check_func( 'pk-list-add',true);
		break;
		
		case "edit":
			echo System :: check_func( 'pk-list-mod',true);
		break;
	}
	
	?>

	<div id="box">
	    <form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-mode="edit" data-valid="true">
                        
        <table cellpadding="0" cellspacing="0" class="form">
    
            <tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>
    
            <tr>
                <th>表单主题：</th>
                <td>
                    <input type="text" name="category" id="category" class="text digi" data-valid-name="内容分类" data-valid-empty="yes" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '内容分类' : ['<?php echo implode("','",explode(" ",trim($_G['setting']['global']['category'])));?>'] }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true" value="<?php echo $row["category"];?>" onfocus="this.blur();" />
                    
                    <input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="65" data-valid-name="表单主题" data-valid-empty="yes" />
                    <select name="skin" id="skin" style=" width:120px;">
                    <?php
                    //遍历皮肤目录
                    $root = "content/theme/";
                    
                    $dirs = loop_dir( $root );
        
                    foreach( $dirs as $file ){
                        
                        //参数获取
						$parm = fileparm( $root.$file."/style.css" );
                        
                        //忽略没有名称的，为删除皮肤提供方法
                        if( $parm['name'] ){	
							echo '<option value="'.$file.'" config="'.$parm['config'].'">'.$parm['name'].'</option>';
                        }
                        
                    }
                    ?>
                    </select>
                
                    <script type="text/javascript">
                    
                    Mo("#skin").bind( 'change', function( ele, index, event ){
                        
                        Mo("#preview").show().html( '<img src="content/theme/'+ Mo(this).value() +'/preview.jpg" onerror="this.src=\'<?php echo VI_BASE;?>static/image/none.gif\'" />' );							
                    
                    });						
                    
                    </script>
					
					<var data-type="tip"><strong>添加新风格</strong> <br />将风格文件夹存放至 <span class=text-key>module/<?php echo $appid;?>/content/theme/</span> 下，系统会自动识别。<br />风格文件夹中的 <span class=text-key>preview.jpg</span> 将作为此风格的预览图使用。</var>
                
                </td>
                
            </tr>
        
            <tr>
                <th>简要介绍：</th>
                <td>
                	<div id="preview" style=" position:absolute; margin-left:680px; "></div>
                    
                	<textarea name="description" cols="50" rows="10" id="description" style="width:670px;height:250px;"><?php echo $row['description'];?></textarea>
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
        
            <!--tr text='表单个性化功能'>
                <td>特殊标记</strong></td>
                <td>
                    <a href="javascript:;"  onclick="Mo('#description').value('$CLOCK',true);" >倒计时</a>
                    <a href="javascript:;"  onclick="Mo('#description').value('[header=宽度,高度,样式(CSS)]Flash地址[/header]',true);" >插入Flash</a>
                    <var data-type="tip"><strong>统计人数</strong> 将会在页面上实时显示参于本活动的人数<br /><strong>点 击 数</strong> 将会在页面上动态显示本活动被点击次数<br /><strong>倒 计 时</strong> 将会在页面上动态显示本活动倒数记时器<br /><strong>用户列表</strong> 在页面上显示参与本活动用户的页面链接<br /><strong>隐藏统计</strong> 根据需要可以将统计按钮隐藏起来<br /><strong>隐藏标题</strong> 根据需要可以将表单标题进行隐藏<br /><strong>隐藏编号</strong> 根据需要可以将表单选项前的自动编号进行隐藏</var>
                    
                </td>                    
            </tr-->
        
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
           
            <tr><td colspan="2" class="section"><strong>正方与反方</strong></td></tr>
            
            <tr>
                <td></td>
                <td>
                
                    <table cellpadding="0" cellspacing="1" border="0" class="gird">
                        <thead>
                            <td>
                            正方
                            </td>
                        </thead>
                        <tr>
                            <td>
                            <input name="config[POSITIVE]" type="text" size="20" class="text" value="<?php echo $config["POSITIVE"];?>" placeholder="正方名称" />
                            <input name="config[POSITIVE_RESULT]" type="hidden" value="<?php echo intval($config["POSITIVE_RESULT"])+0;?>" />
                            票数：<?php echo $config["POSITIVE_RESULT"] ? $config["POSITIVE_RESULT"] : 0;?>
                            </td>
                        </tr>
                        <tr>
                            <td>
							<textarea name="config[POSITIVE_POINT]" cols="20" rows="5" style="100px;" id="POSITIVE_POINT"><?php echo $config["POSITIVE_POINT"];?></textarea>
							<script type="text/javascript">
		                    var editor;
		                    KindEditor.ready(function(K) {
		                        editor = K.create('#POSITIVE_POINT', {
		                            resizeType : 1,
		                            newlineTag : 'p',
		                            allowFileManager : false,
		                            filterMode : false,
		                            loadStyleMode : true,
		                            urlType : 'domain',
		                            width : '300px',
		                            items : [
								        'source', '|', 'cut', 'copy', 'paste', '|', 'justifyleft', 'justifycenter', 'justifyright',
								        'justifyfull', '|', 'fontsize', 'forecolor', 'hilitecolor', 'bold',
								        'italic', 'underline', 'strikethrough', 'removeformat', '|', 'image', 'emoticons', 'link', 'unlink'
									]
		                        });
		                    });
		                    </script>
                            </td>
                        </tr>
                        <thead>
                            <td>
                            反方
                            </td>
                        </thead>
                        <tr>
                            <td>
                            <input name="config[NEGATIVE]" type="text" size="20" class="text" value="<?php echo $config["NEGATIVE"];?>" placeholder="反方名称" />
                            <input name="config[NEGATIVE_RESULT]" type="hidden" value="<?php echo intval($config["NEGATIVE_RESULT"])+0;?>" />
                            票数：<?php echo $config["NEGATIVE_RESULT"] ? $config["NEGATIVE_RESULT"] : 0;?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            观点<br />
							<textarea name="config[NEGATIVE_POINT]" cols="50" rows="5" id="NEGATIVE_POINT"><?php echo $config["NEGATIVE_POINT"];?></textarea>
							<script type="text/javascript">
		                    var editor;
		                    KindEditor.ready(function(K) {
		                        editor = K.create('#NEGATIVE_POINT', {
		                            resizeType : 1,
		                            newlineTag : 'p',
		                            allowFileManager : false,
		                            filterMode : false,
		                            loadStyleMode : true,
		                            urlType : 'domain',
		                            width : '300px',
		                            items : [
								        'source', '|', 'cut', 'copy', 'paste', '|', 'justifyleft', 'justifycenter', 'justifyright',
								        'justifyfull', '|', 'fontsize', 'forecolor', 'hilitecolor', 'bold',
								        'italic', 'underline', 'strikethrough', 'removeformat', '|', 'image', 'emoticons', 'link', 'unlink'
									]
		                        });
		                    });
		                    </script>
                            </td>
                        </tr>
                    </table>
                    
                </td>
            </tr>
                    
            <tr>
                <th>用户评论：</th>
                <td>
                    <label><input type="radio" class="radio" name="config[HIDE_MESSAGE]" value="N" /> 开启</label>
                    <label><input type="radio" class="radio" name="config[HIDE_MESSAGE]" value="Y" /> 关闭</label>
                </td>
                
            </tr>
            
            <?php
            
            //载入表单高级选项
            Module :: exists( 'passport' ) && require(VI_ROOT."module/passport/include.form.php");				
            
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
                                echo '<button type="submit" name="Submit" class="submit">修改此辩论</button>';
                            }else{
                                echo '<input name="action" type="hidden" id="action" value="add" />';
                                echo '<button type="submit" name="Submit" class="submit">新增此辩论</button>';
                            }
                        ?>
                    </td>				
                </tr>
            </tbody>
        
        </table>
     
        <script type='text/javascript'>
            Mo("#category").value( "<?php echo $row["category"];?>" );
            Mo("input[name=state]").value("<?php echo isset($row['state']) ? $row['state'] : 1;?>"); Mo("#skin").value( "<?php echo $row["skin"];?>" );
            
            Mo("#skin").event('change');
        
            Mo("input[name='config[HIDE_MESSAGE]']").value("<?php echo $action ? $config['HIDE_MESSAGE'] : "N";?>");
        
        </script>
	 </form>
	</div>
	


<?php html_close();?>