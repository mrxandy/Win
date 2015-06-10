<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("选票选项组 - VeryIDE");
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
	
	//上一页
	$jump = $_POST["jump"];
	
	if ($action){

		$name = getgpc('name');
		$jump = getgpc('jump');
		$sort = getnum("sort",0);
		$state = getnum('state',0);
		$type = getgpc("type");
		$description = getgpc('description');
		
		$config = getgpc('config');				
		$config = format_json( fix_json( $config, TRUE ) );
		
		switch($action){
				
			case "add":
			
				//检查权限
				System :: check_func( 'vote-list-add', false );		
			
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
				System :: check_func( 'vote-list-mod', false );		
			
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
					$config = fix_json($row['config']);
				}
				
			break;
				
			case "delete":
			
				System :: $db -> execute("DELETE FROM `mod:form_group` WHERE id in(".$list.")");
				System :: $db -> execute("DELETE FROM `mod:form_option` WHERE gid in(".$list.")");
				
				//生成缓存
				Cached :: form( $appid, $fid );
				
				System :: redirect("?do=list&fid=".$fid,"选项组删除成功!");
			
			break;
			
			case "state":
			
				//检查权限
				System :: check_func( 'vote-list-mod', false );		
			
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
    
    <div id="box">
    
    <?php 
	if($do=="new"){
	?>
    
	<?php    
	echo loader_script(array(VI_BASE.'source/editor/kindeditor.js',VI_BASE.'source/editor/lang/zh_CN.js'),'utf-8',$_G['product']['version']);
    ?>
	<script type="text/javascript">
        
        function doChanged(s){
        
            var a = ['radio','checkbox'];
            for(var i=0; i<a.length; i++){
                Mo('#extra_'+a[i]).hide();	
            }
            Mo('#extra_'+s).show();
            
        }	
		
		/******************/
		
		var styles = ["<?php echo implode('","',str_replace('"','\"',$_G['module']['vote']["style"]));?>"];	
        
        function doStyle(s){        
            
			if( s == '0' ){
				Mo("textarea[name='config[GROUP_TEMPLATE]']").attr({'readOnly':false});
			}else{
				Mo("textarea[name='config[GROUP_TEMPLATE]']").value( styles[s] );
				Mo("textarea[name='config[GROUP_TEMPLATE]']").attr({'readOnly':true});				
			}
			
        }
		
		/******************/
		
		function doTag( obj ){
			if( Mo('textarea[name=\'config[GROUP_TEMPLATE]\']').attr('readOnly') == true ){
				Mo.Message("info", '<div class="s"></div><div class="c">自定义模式下才支持标签插入</div><div class="e"></div>' , 3, { "unique" : "message", "center":true } );
			}else{
				Mo('textarea[name=\'config[GROUP_TEMPLATE]\']').value( obj.innerHTML, true );
			}
		}
		
    </script>

	<form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-valid="true">
        
		<input name="fid" type="hidden" id="fid" value="<?php echo $fid;?>" />
        
        <table cellpadding="0" cellspacing="0" class="form">
        
        	<tr><td colspan="2" class="section"><strong>基本信息</strong></td></tr>
        
        	<tr>
            	<th>项目标题：</th>
                <td>
                	<input name="name" type="text" class="text" id="name" value="<?php echo $row['name'];?>" size="75" data-valid-name="项目标题" data-valid-empty="yes" />
                    <select id="config[GROUP_BREAK]" name="config[GROUP_BREAK]" data-valid-name="每行显示数量" data-valid-empty="yes">
                        <option value="">每行显示数量…</option> 
                        <option value=1>1个		(100%)</option> 
                        <option value=2>2个		(50%)</option> 
                        <option value=3>3个		(33%)</option> 
                        <option value=4>4个		(25%)</option> 
                        <option value=5>5个		(20%)</option> 
                        <option value=6>6个		(16%)</option> 
                        <option value=7>7个		(13%)</option> 
                        <option value=8>8个		(12%)</option> 
                        <option value=9>9个		(11%)</option>
                        <option value=10>10个	(10%)</option>
                    </select>                    
                </td>
                
            </tr>        
        
        	<tr>
            	<th>项目类型：</th>
                <td>
                    <label> <input type="radio" class="radio" name="type" value="radio" checked onclick='doChanged(this.value)'>	单选</label>
                    <label> <input type="radio" class="radio" name="type" value="checkbox" onclick='doChanged(this.value)'>	复选</label>
                    <?php if( $_G['licence']['type'] != 'free' ){ ?>
                    <label> <input type="radio" class="radio" name="type" value="button" onclick='doChanged(this.value)'>	按钮</label>
                    <?php } ?>
                </td>
                
            </tr>
            
        	<tr>
            	<th>项目属性：</th>
                <td>
                    
                    <table cellpadding="0" cellspacing="1" border="0" class="gird">
                        <tr>
                            <td>
                                起始票数<br />                                    
                                <input type="text" id="GROUP_STAT" name="config[GROUP_STAT]" value="<?php echo $config["GROUP_STAT"] ? $config["GROUP_STAT"] : 0;?>" data-valid-name="起始票数" data-valid-number="yes" />
                                <span style="color:red;">慎重设置！</span>
                                <var data-type="tip">为空则为0，仅针对新增子选项，此处一但设置，就写入数据库，不能撤消，请慎重！</var>
                            </td>
                            <td>
                            
                                <span id="extra_radio" class="none">               
                                	
                                </span>
                            
                                <span id="extra_button" class="none">               
                                	
                                </span>
                                
                                <span id="extra_checkbox" class="none">
                                    选择范围<br />                                    
                                    至少 <input type="text" name="config[GROUP_MIN]" value="<?php echo $config["GROUP_MIN"];?>" size="6" data-valid-name="选择范围" data-valid-number="no" />                            		~
                                    最多 <input type="text" name="config[GROUP_MAX]" value="<?php echo $config["GROUP_MAX"];?>" size="7" data-valid-name="选择范围" data-valid-number="no" />
                                    <var data-type="tip">至少选择多少个，最多选择多少个</var>
                                </span>
                                
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
							分页显示<br />
							<input name="config[GROUP_SIZE]" type="text" class="text" value="<?php echo $config["GROUP_SIZE"];?>" size="20" placeholder="每页显示数量" data-valid-name="分页显示" data-valid-number="no">
                            <label>
                                <input type="checkbox" class="checkbox" name="config[GROUP_MUST]" value="Y" />
                                必选项
                            </label>
                            </td>
                            <td>
                            显示排序<br />
                            <input name="sort" type="text" class="text" value="<?php echo $row["sort"];?>" size="20" data-valid-name="项目排序" data-valid-number="no">
                            <label>
                                <input name="config[GROUP_ALIGN]" type="checkbox" class="checkbox" value="left" />	
                                左对齐
                            </label>
                            <var data-type="tip">将以从小到大的顺序进行显示</var>
                            </td>
                        </tr>
                    </table>
                    
                </td>
                
            </tr>
            
            <?php if( $_G['licence']['type'] != 'free' ){ ?>
            
        	<tr>
            	<th>图片尺寸：</th>
                <td>
                    
                    <input name="config[GROUP_WIDTH]" type="text" class="text text-yes" size="10" value="<?php echo $config["GROUP_WIDTH"];?>" placeholder="图片宽" data-valid-number="no" />
                    ×
                    <input name="config[GROUP_HEIGHT]" type="text" class="text text-yes" size="10" value="<?php echo $config["GROUP_HEIGHT"];?>" placeholder="图片高" data-valid-number="no" />
					<var data-type="tip">将强制图片使用此尺寸，可能会有变形</var>
                </td>
                
            </tr>
            
            <?php } ?>
        	<tr>
            	<th>展示风格：</th>
                <td>
                	<ul id='group'>
                    <?php
					
					//遍历皮肤目录
					$base = "image/";
					
					foreach( $_G['module']['vote']["style"] as $key => $val ){
						echo '<li rel="'.$key.'" ><img src="'.$base."style.".$key.'.gif" /></li>';
					}
					
					//默认模板
					$template = fileparm( 'content/theme/'.$_CACHE[$appid]['form']['skin'].'/style.css', 'template' );
					
					//默认类型
					$type = fileparm( 'content/theme/'.$_CACHE[$appid]['form']['skin'].'/style.css', 'type' );

                    ?>
                    </ul>
                    <input type="hidden" value="<?php echo $config["GROUP_STYLE"] ? $config["GROUP_STYLE"] : 0;?>" data-valid-name="展示风格" data-valid-empty="yes" name="config[GROUP_STYLE]" />
                </td>
                
            </tr>
            
        	<tr>
            	<th>展示模板：</th>
                <td>
					<p>
						<a href="javascript:void(0);" onmouseover='Mo.Tips(this,event,"tips","链接地址，将会在插入的位置输出一个链接","mouseout",0,-50,{"unique":"tips"});' onclick="doTag( this );">{LINK}</a>
						
						<a href="javascript:void(0);" onmouseover='Mo.Tips(this,event,"tips","图片地址，将会在插入的位置显示一个图片","mouseout",0,-50,{"unique":"tips"});' onclick="doTag( this );">{IMAGE}</a>
						
						<a href="javascript:void(0);" onmouseover='Mo.Tips(this,event,"tips","选项标签，将会在插入的位置显示当前选项名称","mouseout",0,-50,{"unique":"tips"});' onclick="doTag( this );">{LABEL}</a>
						
						<a href="javascript:void(0);" onmouseover='Mo.Tips(this,event,"tips","内容描述，将会在插入的位置显示一段文字描述","mouseout",0,-50,{"unique":"tips"});' onclick="doTag( this );">{DESC}</a>
						
						<a href="javascript:void(0);" onmouseover='Mo.Tips(this,event,"tips","详细介绍，将会在插入的位置输出详细页的链接","mouseout",0,-50,{"unique":"tips"});' onclick="doTag( this );">{DETAIL}</a>
						
						<a href="javascript:void(0);" onmouseover='Mo.Tips(this,event,"tips","投票按钮，将会在插入的位置显示一个小按钮，用于选择或着提交","mouseout",0,-50,{"unique":"tips"});' onclick="doTag( this );">{INPUT}</a>
						
						<a href="javascript:void(0);" onmouseover='Mo.Tips(this,event,"tips","票数统计，将会在插入的位置显示当前选项的票数","mouseout",0,-50,{"unique":"tips"});' onclick="doTag( this );">{COUNT}</a>
					</p>
                	<textarea name="config[GROUP_TEMPLATE]" cols="82" rows="5"><?php echo $config["GROUP_TEMPLATE"] ? $config["GROUP_TEMPLATE"] : $template;?></textarea>
                </td>
                
            </tr>
            
            <tr><td colspan="2" class="section"><strong>高级信息</strong></td></tr>
        
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
            //setSelect("GROUP_MAX","<?php echo $config["GROUP_MAX"];?>");
            Mo("select[name='config[GROUP_BREAK]']").value("<?php echo $config["GROUP_BREAK"] ? $config["GROUP_BREAK"] : 4;?>");            
           	//setCheckBox("GROUP_INPUT","<?php echo $config["GROUP_INPUT"];?>");            
           
            Mo("input[name='config[GROUP_MUST]']").value("<?php echo isset($config) ? $config["GROUP_MUST"] : 'Y';?>"); 
            Mo("input[name='config[GROUP_ALIGN]']").value("<?php echo $config["GROUP_ALIGN"];?>");            
            //Mo("input[name='config[GROUP_VERIFY]']").value("<?php echo $config["GROUP_VERIFY"];?>");
			
            Mo("input[name=state]").value("<?php echo !empty($row['state']) ? $row['state'] : 1;?>");
            
            Mo("input[name=type]").value("<?php echo $row["type"]?$row["type"]:'radio';?>");
            
            doChanged("<?php echo $row["type"]?$row["type"]:'radio';?>");
			
			Mo("#group li").bind('click',function( ){
												   
				Mo("#group li").attr({"className":""});
												   
				Mo( this ).attr({"className":"active"});
				
				Mo("input[name='config[GROUP_STYLE]']").value( this.getAttribute("rel") );
				
				doStyle( this.getAttribute("rel") );
				
			}).each(function( ){
				
				if( this.getAttribute("rel") == "<?php echo $config["GROUP_STYLE"]?$config["GROUP_STYLE"]:'0';?>" ){
					
					Mo( this ).attr({"className":"active"});
					
				}else{
					Mo( this ).attr({"className":""});	
				}
			
			});
			
			doStyle( Mo("input[name='config[GROUP_STYLE]']").value() );
			            
        </script>
	 </form>
	
    <?php
	}elseif($do=="list"){
	?>
    
	<!--用于动作处理_开始-->
	<form name="post-form" id="post-form" method="post">
		<input name="action" id="action" type="hidden" value="" />        
		<input name="state" id="state" type="hidden" value="" />

		<input name="jump" type="hidden" value="" />
		<script>Mo('#post-form input[name=jump]').value( location.href );</script>
	
      <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table" id="table">
		
        <tr class="thead">
            <td width="10"><input type="checkbox" class="checkbox"></td>
            <td>ID</td>
            <td>排序</td>
            <td>项目名称</td>
            <td>子选项 <var data-type="tip"><strong>子选项</strong><br />本项目组供用户选择的选项</var></td>
            <td>类型</td>
		    <td>换行</td>
		    <td>分页</td>	    
		    <td>风格</td>
		    <td>尺寸</td>
            <td>有效性</td>
            <td>添加时间</td>
            <td>状态</td>
            <td width="80">操作</td>
        </tr>
        
        <?php
        $sql="SELECT * FROM `mod:form_group` WHERE fid=".$fid." ORDER BY sort ASC";
        
        $result = System :: $db -> getAll( $sql );
        
        foreach( $result as $row ){
            
		//配置
		$config = fix_json( $row['config'] );

		//统计页面数量
		$count = System :: $db -> getValue( "SELECT count(id) FROM `mod:form_option` WHERE gid=".$row['id'] );
            
            ?>
            <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" data-edit="<?php echo $appid;?>.group.php?do=new&action=edit&fid=<?php echo $fid;?>&gid=<?php echo $row['id'];?>&jump={self}">
		<td title="<?php echo $row['id'];?>">
			<input name="list[]" type="checkbox" class="checkbox" value="<?php echo $row['id'];?>">
		</td>
                <td><a href="content/?id=<?php echo $fid;?>&gid=<?php echo $row['id'];?>" target="_blank"><?php echo $row['id'];?></a></td>
                <td><?php echo $row["sort"];?></td>
                <td><strong class="text-yes"><?php echo $row['name'];?></strong></td>
                <td>                        
                <a id="mark-<?php echo $row['id'];?>" href="<?php echo $appid;?>.option.php?do=list&fid=<?php echo $row["fid"];?>&gid=<?php echo $row['id'];?>" target="_dialog" title="表单子选项" data-width="80%" data-height="60%" class="control">选项组 <?php echo $count;?></a>
                </td>
                <td><?php echo $_G['module']['vote']["group"][$row["type"]];?></td>		
	            <td class="text-key"><?php echo ($config["GROUP_BREAK"]?'每行'.$config["GROUP_BREAK"].'个':'无');?></td>
	            <td class="text-key"><?php echo ($config["GROUP_SIZE"]?'每页'.$config["GROUP_SIZE"].'个':'无');?></td>
	            <td class="text-key"><?php echo ($config["GROUP_STYLE"]?'内置（'.$config["GROUP_STYLE"].'）':'自定义');?></td>
	            <td class="text-yes"><?php echo ($config["GROUP_WIDTH"] && $config["GROUP_HEIGHT"]) ? $config["GROUP_WIDTH"].'×'.$config["GROUP_HEIGHT"] : '';?></td>
                <td><?php echo ($config["GROUP_MUST"]=='Y'?'必选项':'可以空');?></td>
                <td><?php echo date('y-m-d',$row['dateline']);?></td>
                <td><?php echo $_G['project']['state'][$row['state']];?></td>
                <td>
                <button type="button" class="editor" data-url="?do=new&action=edit&fid=<?php echo $row["fid"];?>&gid=<?php echo $row['id'];?>">修改</button>
                    <button type="button" class="normal" data-url="?do=list&action=delete&fid=<?php echo $row["fid"];?>&list=<?php echo $row['id'];?>">删除</button>
                </td>
            </tr>
            <?php
        
        }
		
		if( count( $result ) == 0 ){
			echo '<tr><td colspan="14" class="notice">没有检索到相关选项组，<a href="?fid='.$fid.'&do=new">创建一个？</a></td></tr>';
		}
	  
		?>
		<tr class="tfoot">
            <td colspan="14" class="first">
                <?php echo $_G['module']['vote']['tool'];?>
            </td>
		</tr>
    </table>
    </form>
	<!--用于动作处理_结束-->

    <?php 
	}
	
        //关闭数据库
        System :: connect();				
	
	?>
    
    </div>
    


<?php html_close();?>