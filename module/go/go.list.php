<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("链接列表 - VeryIDE");
?>


<?php

	//载入模块配置并生成菜单
	$appid = Module :: get_appid();
	
	echo Module :: get_context( $appid );

	//////////////////////////////////

	//加载分析模块
	Module :: loader( 'analytic' );

	$q = getgpc('q');
	$s = getgpc("s");	
	
	//批处理列表
	$list = getgpc('list');
	$list = is_array($list) ? implode(",", $list) : $list;

	//上一页
	$jump = getgpc('jump');
	
	$action = getgpc('action');

	//连接数据库
	System :: connect();

	if ($action){
	
		switch($action){
			//删除
			case "delete":
				
				//检查权限
				System :: check_func( 'go-del', false );
				
				//删除记录
				System :: $db -> execute("DELETE FROM `mod:go_list` WHERE id in(".$list.")");
				
				//删除缓存
				$array = explode(",",$list);
				foreach( $array as $id ){
					Cached :: delete( $appid, 'go.'.$id.'.php' );
				}
				
				//清空统计
				Analytic :: clear( $appid, $list );				
				
				//记录日志
				System :: insert_event("system-recycle",time(),time(),"删除数据：$appid (".$list.")");
				
				System :: redirect("go.list.php","链接删除成功!");
				
			break;
		
			case "create":
			
				//缓存广告缓存
				Cached :: rows( $appid,"SELECT * FROM `mod:go_list`", array( 'alias' => 'go' ) );
				
				//生成关键字缓存
				Cached :: multi( $appid, "SELECT id,category,name,link FROM `mod:go_list` WHERE category <>'' and link<>'' ORDER BY id DESC", "table.go", array( 'alias' => 'go' ) );
				
				$_G['project']['message']="已生成所选数据！";
				
			break;
		}
	}
	
	$connect = getgpc('connect');
	$method = getgpc('method');
	
?>
	<div id="search">
		
	    <form name="form1" method="get" action="?">
            <span class="action">	    
	    		    
		<select name="method" onchange="Mo(this.form).submit();" ignore="true">
		    <optgroup label="数据类型" />
		    <option value="">全部类型</option>
		    <?php            
		    foreach( $_G['module']['go']["method"] as $key => $val ){
			echo '<option value="'.$key.'">'.$val.'</option>';
		    }            
		    ?>
		</select>
	    
		<select name="connect" onchange="Mo(this.form).submit();" ignore="true">
		    <optgroup label="连接方式" />
		    <option value="">全部方式</option>
		    <option value="http">HTTP</a>
			<option value="mail">Mail</a>
			<option value="qq">QQ</a>
			<option value="msn">MSN</a>
			<option value="ww">旺旺</a>
		</select>
	    
            	<button type="button" class="cancel" onclick="location.href='?s=account&q=<?php echo urlencode($_G['manager']['account']);?>';">我的统计</button>
                <button type="button" class="button" onclick="location.href='go.edit.php';">新增统计</button>
                <button type="button" class="button" onclick="if(confirm('确定现在要更新缓存吗？')){location.href='?action=create';}">更新缓存</button>
            </span>      
			
            <select name="s" id="s">
		<option value="name" checked>标题</option>
		<option value="id"> ID</option>
		<option value="account">作者</option>
		<option value="mender"> 修改人</option>
		<option value="link">链接</option>
		<option value="category"> 分类</option>
            </select>
		<input name="q" type="text" class="text" id="q" value="<?php echo $q;?>">

		<button class="go" type="submit"></button>
	    </form>
		<script type="text/javascript">
			Mo("select[name=s]").value("<?php echo $s;?>");
			Mo("select[name=method]").value("<?php echo $method;?>");
			Mo("select[name=connect]").value("<?php echo $connect;?>");
			
			Mo.reader(function(){
				
				Mo("#table tr").bind( 'mousedown', function( index, e ){

					var target = Mo.Event( e ).target();
	
					//编号
					var mark = this.getAttribute("mark");
	
					//忽略标题等行
					if( !mark || target.tagName != "TD" ){ return false; };
	
					//选中第一个复选框
					var obj = Mo("input[type=checkbox]",this).item(0);
	
					if(obj.checked){
						Mo('#block-'+mark).show();
					}else{
						Mo('#block-'+mark).hide();
					}
	
				})
			});
		</script>
	</div>
    
    <!--用于动作处理_开始-->
    <form name="post-form" id="post-form" method="post">
        <input name="action" id="action" type="hidden" value="" />        
        <input name="state" id="state" type="hidden" value="" />
        
        <input name="jump" type="hidden" value="<?php echo $_G['runtime']['absolute'];?>" />
    
	<?php		
    
	$sql="SELECT * FROM `mod:go_list` WHERE 1=1 ";

	if( $q != '' && isset( $s ) ){    
		if( strpos($s,"id") !== false ){
			$sql.=" and  `".$s."` = '".$q."'";
		}else{
			$sql.=" and `".$s."` like '%".$q."%'";
		}        
	}
	
	if( $method ){
		$sql.=" and `method` = '".$method."'";
	}	
	
	if( $connect ){
		$sql.=" and `link` like '".$connect."://%'";
	}
    
    //排序
	$extra = getgpc('extra');
    if( $extra ){		
    	$sql.=" ORDER BY ". $extra['field'] ." ". $extra['order'] ."";		
    }else{
    	$sql.=" ORDER BY id DESC";
    }
    
	//查询数据库_总记录数
	$row_count = System :: $db -> getCount( $sql );   

	//分页参数
	$page=getpage("page");
	$page_start=$_G['setting']['global']['pagesize']*($page-1);
	$sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];	

	//分页链接
	$url="?q=".$q."&s=".$s."&method=".$method."&connect=".$connect."". format_get( 'extra', $extra ) ."&page=";

	$result = System :: $db -> getAll( $sql );
	
	?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
          
          <tr class="thead">
          	<td>分类</td>
            <td>链接名称</td>
            <td>原始链接</td>
            <td>统计地址 <var data-type="tip">用于统计点击量或浏览量的链接地址</var></td>
			<td data-sort-field="method" data-sort-screen='<?php echo fix_json( $_G['module']['go']["method"] );?>' data-sort-query="?method=$value">类型</td>
       		<td>数值</td>
       		<td>扩展</td>
            <td>创建者</td>
			<td>修改者</td>
            <td data-sort-field="modify">修改时间</td>
            <td data-sort-field="dateline">创建时间</td>
            <td width="80">操作</td>
          </tr>
        <?php
         
        foreach( $result as $row ){
			
			$link = Go :: getlink( $row['method'], $row['id'] );
			
			switch( $row['method'] ){
			
				case "view":				
					$code = "<!--GO--><img src='$link' /><!--/GO-->";			
				break;
			
				case "click":				
					$code = "<!--GO--><a href='$link'>".$row['name']."</a><!--/GO-->";
				break;
			
			}
			
            ?>
              <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" data-edit="go.edit.php?action=edit&id=<?php echo $row['id'];?>&jump={self}" onclick="Mo('#block-<?php echo $row['id'];?>').toggle();">
             	<td><a href="?s=category&q=<?php echo rawurlencode($row['category']);?>"><?php echo $row['category'];?></a></td>
                <td title="<?php echo $row['name'];?>">
                	<a href="<?php echo $row['link'];?>" target="_blank"><?php echo format_substr($row['name'],22,"...");?></a>
					<?php
					if($row['state']==-1){
						echo '<sup class="text-yes">[回收站]</sup>';
					}
					?>
                </td>
                <td>
                    <a href='<?php echo $row['link'];?>' target="_blank"><?php echo format_url($row['link'],30);?></a>
                </td>
                <td>
                    <a href='<?php echo $link;?>' target="_blank"><?php echo $link;?></a>
                </td>
				<td><a href='?method=<?php echo urlencode($row['method']);?>'><?php echo $_G['module']['go']["method"][$row['method']];?></a></td>
                <td class="text-yes" align="center">
                    <?php echo Analytic :: count( $appid, $row['id'], $row['method'] );?>
                </td>
                <td>
                    <?php
					
					echo '<a href="'.VI_BASE.'module/analytic/analytic.show.php?appid='.$appid.'&id='.$row['id'].'&type='.$row['method'].'s&name='.urlencode($row['name']).'" target="_dialog" title="统计分析" data-width="80%" data-height="60%">'.loader_image("icon/statistics.png","统计分析").'</a> ';
							
					if( $row['method'] == 'click' ){
					echo '<a href="'.VI_BASE.'source/dialog/qrcode.php?data='.$link.'" target="_dialog" title="二维码" data-width="930" data-height="510">'.loader_image("icon/qrcode.png","二维码").'</a> ';
					}
                    ?>
                </td>
				<td><a href='?s=account&q=<?php echo urlencode($row['account']);?>'><?php echo $row['account'];?></a></td>
				<td><a href='?s=mender&q=<?php echo urlencode($row['mender']);?>'><?php echo $row['mender'];?></a></td>
                <td>
                <?php
                echo date("y-m-d",$row['modify']);
                ?>
                </td>
                <td title="<?php echo date("Y-m-d H:i:s",$row['dateline']);?>"><?php echo date("y-m-d",$row['dateline']);?></td>
                <td>                
                <button type="button" class="editor" data-url="go.edit.php?action=edit&id=<?php echo $row['id'];?>">修改</button>
	                <button type="button" class="normal" data-url="?action=delete&list=<?php echo $row['id'];?>">删除</button>
                </td>
                </tr>
                <tr id="block-<?php echo $row['id'];?>" <?php echo $_GET['mark'] != $row['id'] ? 'style="display:none"' : '' ?>>
                    <td colspan="12" class="block">
                    <?php
			echo loader_image("icon/document.png");
			?>
                    <textarea onmouseover="this.select();" style="vertical-align:middle; width:90%;" id="mark-<?php echo $row['id'];?>"><?php echo $code;?></textarea>                     
                    </td>
                </tr>
            <?php
            }
	    
		    if( count( $result ) == 0 ){
				echo '<tr><td colspan="12" class="notice">没有检索到相关统计，<a href="'.$appid.'.edit.php">创建一个？</a></td></tr>';
			}
            ?>
        </table>
        
        <div id="saving">
	<?php
	//关闭数据库
	System :: connect();

	echo multipage($page,$row_count,$_G['setting']['global']['pagesize'],$url,"page");
	?>
        </div>

<?php html_close();?>