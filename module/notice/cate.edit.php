<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("通知分类编辑 - VeryIDE");
?>


	<?php
	
	//载入模块配置并生成菜单
	$appid = Module :: get_appid();
	
	echo Module :: get_context( $appid );
	
	//////////////////////////
	
	//连接数据库
	System :: connect();
	
	$jump=getgpc("jump");
	$action=getgpc('action');
	$id=getnum('id',0);
	
	if ($action){

		$name=getgpc('name');
		$mark=getgpc("mark");
		$title=getgpc("title");
		$state=getgpc('state');
		
		$parent = getnum("parent",0);
		$parent = $parent ? getnum("category",0) : $parent;
		
		$state = getnum('state',0);
		$mode= getnum("mode",0);
		$sort = getnum("sort",0);
		
		//标签配置
		//$array = array();
		
		//只对小分类有效
		if( $parent ){
			
			$bind=getgpc("bind");
			$skin=getgpc("skin");		
			$link=getgpc('link');
			
			$field = $_POST['field'];		
			
			//组合配置
			/*
			foreach( $field['extend'] as $k => $v  ){
				if( !$v['key'] || !$v['tag'] ) unset( $field['extend'][$k] );
			}
			*/
			
			foreach( $field['photo']['config'] as $k => $v  ){
				
				if( !$v['name'] ){
					unset( $field['photo']['config'][$k] );
					continue;	
				}
				
				//分解可选值
				$field['photo']['config'][$k]['option'] = parse_ini_string($v['option']);
				
			}
			
			foreach( $field['price']['config'] as $k => $v  ){
				
				if( !$v['name'] ){
					unset( $field['price']['config'][$k] );
					continue;	
				}
				
				//分解可选值
				$field['price']['config'][$k]['option'] = parse_ini_string($v['option']);
				
			}
			
			$config = serialize( $field );
			
		}
		/****************/
	
		switch($action){
			
			case "add":			
			
				//检查权限
				System :: check_func( 'notice-cate-add', false );		

				$sql="INSERT INTO `mod:common_category`(appid,name,mark,title,parent,sort,mode,state,dateline,skin,bind,link,config,ip) VALUES('".$appid."','".$name."','".$mark."','".$title."',".$parent.",".$sort.",".$mode.",".$state.",".time().",'".$skin."','".$bind."','".$link."','".$config."','".GetIP()."')";
				
				System :: $db -> execute( $sql );				
				
			break;
				
			case "update":
			
				//检查权限
				System :: check_func( 'notice-cate-mod', false );		

				$sql="UPDATE `mod:common_category` SET name='".$name."',mark='".$mark."',title='".$title."',state=".$state.",sort=".$sort.",mode=".$mode.",parent=".$parent.",skin='".$skin."',bind='".$bind."',ping='',link='".$link."',config='".$config."',modify=".time()." WHERE id=".$id;
				
				System :: $db -> execute($sql);
			
			break;
			
			case "edit";

				$sql="SELECT * FROM `mod:common_category` WHERE id=".$id;
				$row = System :: $db -> getOne( $sql );
					
				//读取配置
				$config = unserialize( $row['config'] );

				//兼容老版本
            	if( $row["ping"] ){
                	$config = array( 'review' => unserialize($row["ping"]), 'extend' => $config );
            	}
				
				if( !$config['photo'] ) $config['photo'] = array( 'config' => array() );
				
				if( !$config['price'] ) $config['price'] = array( 'config' => array() );
				
				/*
				if( !is_array($config) ){
					$config = array( 'review' => array(), 'extend' => array() );	
				}
				
				//读取配置
				$ping = unserialize($row["ping"]);
				*/
			
			break;
			
			case "status":
			
				//检查权限
				System :: check_func( 'notice-cate-mod', false );		
				
				$sql="UPDATE `mod:common_category` SET state=".$state." WHERE id=".$id;
				
				System :: $db -> execute($sql);				
				
			break;
		}

		//转向
		if( $action!="edit" && $action && !$_G['project']['message'] ){
			
			//更新分类数组缓存
			Cached :: multi( $appid,"SELECT id,name,mark,title,parent,skin,mode,stat,config,ping,state,bind,link FROM `mod:common_category` WHERE appid = 'notice-cate' ORDER BY sort ASC","table.category", array( 'alias'=>'category', 'serialize' => array('config','ping') ) );
		
			//更新分类脚本缓存
			Cached :: script($appid,"SELECT id,name,parent,config FROM `mod:common_category` WHERE appid = 'notice-cate' ORDER BY sort ASC","CATEGORY","mod.category",array( 'serialize'=>array('config'), 'charset' => 'utf-8', 'unicode' => TRUE ));
			
			if($jump){
				System :: redirect($jump,"信息修改成功!");
				exit();
			}else{
				System :: redirect("cate.list.php","信息提交成功!");	
			}
		}

	}else{
		$config = array( 'review'=>array(), 'extend'=>array(), 'photo'=>array(), 'price'=>array() );
	}
	  
	//关闭数据库
	System :: connect();
	
	?>

    
    <script type="text/javascript">	

    function _adForm(){
        var o=Mo("input[name=parent]").value();
        if(o==1){
            Mo("#advanced").show();
        }else{
            Mo("#advanced").hide();
        }
    }

    function doSelect(val){
        Mo('#skin').value(val);

        Mo('#group li').attr( { "className" : "" } );

        Mo('#'+val).attr( {"className" : 'active'} );
    }

    </script>

	<?php
	
	//显示权限状态
	switch($action){
		case "":
			echo System :: check_func( 'notice-cate-add',true);
		break;
		
		case "edit":
			echo System :: check_func( 'notice-cate-mod',true);
		break;
	}
	
	?>

	<div id="box">
	    <form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-mode="edit" data-valid="true">
                
        <table cellpadding="0" cellspacing="0" class="form">
		<tbody>
        
        	<tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>
        
        	<tr>
            	<th>分类名称：</th>
                <td>
                	<input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="35" data-valid-name="分类名称" data-valid-empty="yes" />
                    
                    <input name="sort" type="text" id="sort" value="<?php echo $row["sort"];?>" size="35" data-valid-name="分类排序" data-valid-number="no" class="text digi" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '显示排序' : (function(){ var a = []; for(var i=0;i<=34;i++){a.push(i);} return a; })() }, function( value ){ this.value = value; }, 1 , 10 );" readonly="true"  />
				 <var data-type="tip">从小到大依次排序</var>
                </td>
            </tr>
        
        	<tr>
            	<th>页面标题：</th>
                <td>
                	<input name="title" type="text" class="text" id="title" value="<?php echo $row["title"];?>" size="35" />
                    <var data-type="tip">显示于浏览器标题栏，有助于SEO</var>
                </td>
            </tr>
        
        	<tr>
            	<th>分类标识：</th>
                <td>
                	<input name="mark" type="text" class="text" value="<?php echo $row["mark"];?>" size="35" onclick="Mo.Soler( this, event, this.value, 'mo-cater', { '内置标签' : ['food','play','health','train','marry','baby','build','fitment','helpful','travel'] }, function( value ){ this.value = value; }, 1 , 23 );" />
                    <var data-type="tip">该分类英文标识</var>
                </td>
            </tr>
         
        	<tr>
            	<th>分类状态：</th>
                <td>
                        
					<?php
                    foreach($_G['project']['state'] as $key => $val){
                    	// || $key > 1
						if( $key < 0 ) continue;
                        echo '<label><input type="radio" class="radio" name="state" value="'.$key.'" /> '.$val.'</label>';
                    }
                    
                    ?>
                    
                    <br />
                    
                    <p class="text-gray">选推荐，将会在首页 “热门分类” 中显示</p>
                        
                </td>
            </tr>
      
            <tr>
                <th>所属分类：</th>
                <td>
                    <label>
                    <input type="radio" class="radio" name="parent" id="parent" value="0" onclick="_adForm();Mo('#category').disabled();" checked>
                    顶级分类
                    </label>
                
                    <label>
                        <input name="parent" id="parent" type="radio" class="radio" onclick="_adForm();Mo('#category').enabled();" value="1">
                    </label>
                    <select name="category" id="category">
                    
                    <?php
                    
                    Cached :: loader($appid,'table.category');
                    
                    foreach($_CACHE['notice']['category'] as $key){
                        if($key["parent"]=="0"){
                            echo '<option value="'.$key["id"].'">'.$key["name"].'</option>';
                        }
                    }
                    
                    ?>
                    
                    </select>
                </td>
            </tr>
           
            <tbody id="advanced" style="display:none;">
         
        	<tr><td colspan="2" class="section"><strong>显示设置</strong></td></tr>
        
        	<tr>
            	<th>链接地址：</th>
                <td>
                	<input name="link" type="text" class="text" id="link" value="<?php echo $row['link'];?>" size="35" />
                    {SID} 商家ID
                </td>
            </tr>
         
        	<tr>
            	<th>展示模式：</th>
                <td>
                    <label>
                    <input type="radio" class="radio" name="mode" value="0" checked="checked" data-valid-name="展示模式" data-valid-empty="yes" />
                    完整模式
                    </label>
                    
                    <label>
                    <input type="radio" class="radio" name="mode" value="1"  data-valid-name="展示模式" data-valid-empty="yes" />
                    简洁模式
                    </label>
                </td>
            </tr>
            
        	<tr>
            	<th>商家风格：</th>
                <td>
                    <ul id='group'>
                    <?php                    
                    
					//遍历皮肤目录
					$root = "content/skins/";
					
					$list = loop_dir( $root );
					
					foreach( $list as $file ){
						echo '<li onclick="doSelect(\''.$file.'\');" id="'.$file.'" ><img src="'.$root.$file.'/preview.gif" /></li>';
					}
                    
                    ?>
                    </ul>
                    <input type="hidden" name="skin" id="skin" value="<?php echo $row["skin"]?$row["skin"]:'gray';?>" data-valid-name="商家风格" data-valid-empty="yes" />
                </td>
            </tr>
       
        	<tr><td colspan="2" class="section"><strong>高级设置</strong></td></tr>
         
        	<!--tr>
            	<th>绑定行业：</th>
                <td>
                    <select name="bind" id="bind">
                    	<option value="">无</option>
                    <?php
					
                    //遍历行业扩展目录
                    $root = VI_ROOT."module/boss/extend/";
                    
                    $list = loop_dir( $root );
					
					foreach( $list as $file ){
                    
						//config							
						$_config = $root.$file."/config.php";                    
						
						if( is_dir($root.$file) && file_exists($_config) ){								
							
							//载入配置文件
							include($_config);
							
							//私有模块
							if( $Plugins[$file]["mode"]=="private" ){								
								echo '<option value="'.$file.'">'.$Plugins[$file]["name"].'</option>';									
							}
					
						}
						
                    }
					
                    ?>
                    </select>
                </td>
            </tr-->
            
        	<tr>
            	<th>分类标签：</th>
                <td>
                    
                	<div id="tag-box">
					<?php
					$i=0;
					
                    foreach($_G['module']['notice']["extra"] as $key){
                    ?>
                    
                    <table id="tag-<?php echo $key;?>" cellpadding="0" cellspacing="1" border="0" class="gird">
                        <tr>
                            <td>
                           		<span class="action"><?php echo $key;?></span>分类：
                                <br />
                                <input name="field[extend][<?php echo $key;?>][key]" type="text" class="text" value="<?php echo $config['extend'][$key]["key"];?>" size="20" />
                                <br />
                                标签：<br />
                                <textarea name="field[extend][<?php echo $key;?>][tag]" cols="78" rows="2" style="font-family:Verdana;"><?php echo $config['extend'][$key]["tag"];?></textarea>
                            </td>
                        </tr>
                    </table>                    
                    
					<?php
						$i++;
                    }
                    ?>
                    </div>
                    
                    <div class="highlight">多个标签请用英文空隔隔开。</div>
                    
                </td>
            </tr>
            
            <tr><td colspan="2" class="section"><strong>商家相册</strong></td></tr>

            <tr>
                <th>启用相册：</th>
                <td>
                    <label><input name="field[photo][open]" type="checkbox" class="checkbox" value="true" /> 商家可以自主管理相册（需配合扩展的模块使用）</label>
                    
                </td>
            </tr>
            
        	<tr>
            	<th>相册分类：</th>
                <td>
                
                	<script type="text/javascript">
					var Planer = new Mo.Planer( Mo.$("photo_box") );
					</script>
                    
                    <table id="photo_box" cellpadding="0" cellspacing="1" border="0" class="frame">
                    <thead>
                        <tr>
                        <td width="60">
                        
                        </td>
                        <td width="55">
                        字段
                        </td>
                        <td width="100">
                        显示名
                        </td>
                        <td width="120">
                        可选值
                        </td>
                    </thead>
                    
                    <tr class="<?php echo zebra( $x, array( "line" , "band" ) );?>" fixed="true">
                        <td>
                        </td>
                        <td valign="top">
                        sort_0
                        </td>
                        <td valign="top">
                        建站程序
                        </td>
                        <td>
                        1=DiscuzX<br />
                        2=PHPWind<br />
                        3=VeryIDE
                        </td>
                    </tr>
                    
                    <?php
                    
                    for( $i = 1; $i <= 3; $i++ ){
						if( array_key_exists( 's'.$i, $config['photo']['config'] ) === FALSE && array_key_exists( $i, $config['photo']['config'] ) === FALSE ){
							$config['photo']['config']['s'.$i] = array( 'index' => $i );
						}
					}
					
                    foreach( $config['photo']['config'] as $conf ){
                    ?>
                        <tr>
                        	<td>
		                        <a onclick="javascript:Planer.move(this,'up',2);void(0);">上移</a>
		                        <a onclick="javascript:Planer.move(this,'down',2);void(0);">下移</a>
	                        </td>
	                        <td>
		                        sort_<?php echo $conf['index'];?>
		                        <input name="field[photo][config][s<?php echo $conf['index'];?>][index]" type="hidden" class="text" value="<?php echo $conf['index'];?>" />
	                        </td>
	                        <td>
	                        	<input name="field[photo][config][s<?php echo $conf['index'];?>][name]" type="text" class="text" value="<?php echo $conf['name'];?>" size="10" style="vertical-align: top;" />
	                        </td>
                            <td>
                           		<textarea name="field[photo][config][s<?php echo $conf['index'];?>][option]" cols="42" rows="4"><?php $s = ''; foreach( $conf['option'] as $k => $v ){ $s .= $k .'='.$v.chr(10);} echo trim($s)?></textarea>
                            </td>
                        </tr>
                    <?php
					}
					?>
					
					</table>
                    
                </td>
            </tr>
            
            <tr><td colspan="2" class="section"><strong>价格套餐</strong></td></tr>

            <tr>
                <th>启用套餐：</th>
                <td>
                    <label><input name="field[price][open]" type="checkbox" class="checkbox" value="true" /> 商家可以自主管理套餐（需配合扩展的模块使用）</label>
                    
                </td>
            </tr>
            
        	<tr>
            	<th>价格分类：</th>
                <td>
                
                	<script type="text/javascript">
					var Planer = new Mo.Planer( Mo.$("price_box") );
					</script>
                    
                    <table id="price_box" cellpadding="0" cellspacing="1" border="0" class="frame">
                    <thead>
                        <tr>
                        <td width="60">
                        
                        </td>
                        <td width="55">
                        字段
                        </td>
                        <td width="100">
                        显示名
                        </td>
                        <td width="120">
                        可选值
                        </td>
                    </thead>
                    
                    <tr class="<?php echo zebra( $x, array( "line" , "band" ) );?>" fixed="true">
                        <td>
                        </td>
                        <td valign="top">
                        sort_0
                        </td>
                        <td valign="top">
                        建站程序
                        </td>
                        <td>
                        1=DiscuzX<br />
                        2=PHPWind<br />
                        3=VeryIDE
                        </td>
                    </tr>
                    
                    <?php
                    
                    for( $i = 1; $i <= 3; $i++ ){
						if( array_key_exists( 's'.$i, $config['price']['config'] ) === FALSE && array_key_exists( $i, $config['price']['config'] ) === FALSE ){
							$config['price']['config']['s'.$i] = array( 'index' => $i );
						}
					}
					
                    foreach( $config['price']['config'] as $conf ){
                    ?>
                        <tr>
                        	<td>
		                        <a onclick="javascript:Planer.move(this,'up',2);void(0);">上移</a>
		                        <a onclick="javascript:Planer.move(this,'down',2);void(0);">下移</a>
	                        </td>
	                        <td>
		                        sort_<?php echo $conf['index'];?>
		                        <input name="field[price][config][s<?php echo $conf['index'];?>][index]" type="hidden" class="text" value="<?php echo $conf['index'];?>" />
	                        </td>
	                        <td>
	                        	<input name="field[price][config][s<?php echo $conf['index'];?>][name]" type="text" class="text" value="<?php echo $conf['name'];?>" size="10" style="vertical-align: top;" />
	                        </td>
                            <td>
                           		<textarea name="field[price][config][s<?php echo $conf['index'];?>][option]" cols="42" rows="4"><?php $s = ''; foreach( $conf['option'] as $k => $v ){ $s .= $k .'='.$v.chr(10);} echo trim($s)?></textarea>
                            </td>
                        </tr>
                    <?php
					}
					?>
					
					</table>
                    
                </td>
            </tr>
       
        	<tr><td colspan="2" class="section"><strong>点评控制</strong></td></tr>

            <tr>
                <th>开启点评：</th>
                <td>
                    <label><input name="field[review][open]" type="checkbox" class="checkbox" value="true" /> 启用总体评分和点评内容框</label>
                    
                </td>
            </tr>

            <tr>
                <th>人均消费：</th>
                <td>
                    <input name="field[review][price]" type="text" class="text" value="<?php echo $config['review']["price"];?>" />
                    `price` 消费输入框
                </td>
            </tr>
            
            <!--tr>
                <th>活动点评：</th>
                <td>
                    <input name="ping[event]" type="checkbox" class="checkbox" value="true" />
                    `event` 活动勾选框
                </td>
            </tr-->
  
            <tr>
                <th>特色推荐：</th>
                <td>
                    <input name="field[review][tags]" type="text" class="text" value="<?php echo $config['review']["tags"];?>" />
                    `tags` 文字输入框
                </td>
            </tr>
            
            <tr>
            	<th>扩展打分：</th>
                <td>
                
                <table cellpadding="0" cellspacing="1" border="0" class="gird">
                    <thead>
                        <td>
                         `extra`
                        </td>
                        <td>
                        `extrb`
                        </td>
                    </thead>
                    <tr>
                        <td>
                        <input name="field[review][extra]" type="text" class="text" value="<?php echo $config['review']["extra"];?>" />
                        如：环境
                        </td>
                        <td>
                        <input name="field[review][extrb]" type="text" class="text" value="<?php echo $config['review']["extrb"];?>" />
                        如：服务
                        </td>
                    </tr>
                    <thead>
                        <td>
                         `extrc`
                        </td>
                        <td>
                        `extrd`
                        </td>
                    </thead>
                    <tr>
                        <td>
                        <input name="field[review][extrc]" type="text" class="text" value="<?php echo $config['review']["extrc"];?>" />
                        如：口味
                        </td>
                        <td>
                        <input name="field[review][extrd]" type="text" class="text" value="<?php echo $config['review']["extrd"];?>" />
                        如：速度
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
                        echo '
                        <button type="submit" name="Submit" class="submit">修改此分类</button>
                        <input name="id" type="hidden" id="id" value="'.$row['id'].'" />
                        <input name="action" type="hidden" id="action" value="update" />
                        ';
                    }else{
                        echo '
                        <button type="submit" name="Submit" class="submit">新增此分类</button>
                        <input name="action" type="hidden" id="action" value="add" />
                        ';
                    }
                    
                    echo '<input name="jump" type="hidden" id="jump" value="'.$jump.'" />';
                    ?>
                </td>
                
            </tr>
        </tbody>
        
        </table>
            
	 </form>
	</div>
    
	<script type='text/javascript'>
	
	Mo("input[name='field[photo][open]']").value("<?php echo $config['photo']["open"];?>");
	Mo("input[name='field[price][open]']").value("<?php echo $config['price']["open"];?>");
	Mo("input[name='field[review][open]']").value("<?php echo $config['review']["open"];?>");
    //Mo("input[name='ping[price]']").value("<?php echo $config['review']["price"];?>");
    //Mo("input[name='ping[event]']").value("<?php echo $ping["event"];?>");
    //Mo("input[name='ping[question]']").value("<?php echo $config['review']["question"];?>");
	
    Mo("input[name=mode]").value("<?php echo isset($row["mode"]) ? $row["mode"] : 0;?>");
    Mo("input[name=state]").value("<?php echo isset($row['state']) ? $row['state'] : 1;?>");
    Mo("input[name=parent]").value("<?php echo $row["parent"] ? 1 : 0;?>");
	
	if( '<?php echo $row["parent"];?>' ){
        Mo('#category').enabled();
    }else{
        Mo('#category').disabled();
    }

    Mo("select[name=category]").value("<?php echo $row["parent"];?>");
    Mo("select[name=bind]").value("<?php echo $row["bind"];?>");

    _adForm();

    doSelect('<?php echo $row["skin"]?$row["skin"]:'gray';?>');
    </script>
	


<?php html_close();?>