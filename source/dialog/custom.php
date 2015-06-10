<?php

/*

		用户主题设置

*/

//载入全局配置和函数包
require_once dirname(__FILE__).'/../../app.php';

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_G['product']['charset'];?>" />
<title>更换主题 - Powered By VeryIDE</title>

<?php

echo loader_style(array(VI_BASE."static/style/general.css",VI_BASE."static/style/dialog.css"),$_G['product']['charset'],$_G['product']['version']);

echo loader_script(array(VI_BASE."static/js/mo.js",VI_BASE."static/js/mo.ui.js",VI_BASE."static/js/mo.ajax.js",VI_BASE."static/js/mo.form.js"),'utf-8',$_G['product']['version']);

echo '<script type="text/javascript">'.System :: reader_config().'</script>';

echo loader_script(array(VI_BASE."static/js/serv.dialog.js",VI_BASE."static/js/serv.upload.js"),'utf-8',$_G['product']['version']);

?>

</head>

<body>

	<div id="wrapper">
	
	<?php
	
	//未登录
	if( !$_G['manager']['id'] ){
		exit('<div id="state" class="failure">未登录，请先登录！</div>');
	}
	
	////////////////////////////
		
	//菜单处理
	$action = getgpc('action');
	$jump = getgpc('jump');
	
	//连接数据库
	System :: connect();
	
	//动作处理
	switch ($action){
		
		case "update":
			
			//配置
			$config = $_POST['config'];
			$config = format_json( fix_json( $config ) );
				
			//更新数据
			$sql="UPDATE `sys:admin` SET config='".$config."' WHERE id=".$_G['manager']['id'];			
			System :: $db -> execute( $sql );
			
			//缓存系统用户
			Cached :: table( 'system', 'sys:admin', array( 'jsonde' => array('config','extra') ) );
			
			$_G['project']['message']="成功修改用户偏好!";
			
			echo '<div id="state"><button type="button" class="submit y" onclick="parent.location.reload(true);">重新载入</button>你刚刚更改了个人偏好，请刷新以查看最新效果：</div>';
		
		break;
		
	}
	
	//
	$sql="SELECT * FROM `sys:admin` WHERE id=".$_G['manager']['id'];
	$row = System :: $db -> getOne( $sql );
	
	if( $row ){
		//配置
		$config = fix_json( $row['config'] );
	}else{
		$_G['project']['message']="未找到指定用户!";
	}
	
	//关闭数据库
	System :: connect();
	
	?>

	<form action="?" method="post">

	<table cellpadding="0" cellspacing="0" class="form">
		<tbody>
		<tr><td colspan="2" class="section"><strong>用户界面</strong></td></tr>
		
		<tr>
		<th>界面主题：</th>
		<td>
			<label><input type="radio" class="radio" name="config[ui-model]" value="modern">摩登现代</label>
			<label><input type="radio" class="radio" name="config[ui-model]" value="classic">怀旧经典</label>
		</td>
	    </tr>
	    </tbody>
	    
	    <tbody id="adv_classic">
	
		<tr>
		<th>菜单设置：</th>
		<td>
		    <label><input type="radio" class="radio" name="config[ui-menu]" value="open">全部展开</label>
		    <label><input type="radio" class="radio" name="config[ui-menu]" value="close">全部关闭</label>
		    <label><input type="radio" class="radio" name="config[ui-menu]" value="auto">自动处理</label>
		</td>
	    </tr>
	
		<tr>
		<th>默认风格：</th>
		<td>
		    <label><input type="radio" class="radio" name="config[ui-theme]" value="simple">简洁风格</label>
		    <label><input type="radio" class="radio" name="config[ui-theme]" value="cloud">蓝天白云</label>
		    <label><input type="radio" class="radio" name="config[ui-theme]" value="moving">云彩飘动（动画）</label>
		</td>
	    </tr>
	    
	    </tbody>
	    
	    <tbody id="adv_modern">

		<tr>
			<th>背景图片：</th>
			<td>
				<input name="config[bg-image]" type="hidden" class="text" size="65" value="<?php echo $config["bg-image"];?>" />
		<script type="text/javascript">
		new Serv.Upload("_file_","<?php echo $config["bg-image"];?>",{'format':['<?php echo implode("','",$_G['upload']['image']);?>'],'again':false,'recovery':true,'input':false,'callback':function(o){ Mo("input[name='config[bg-image]']").value(o["value"]); }});
		</script>
			</td>
		</tr>
		
		</tbody>
		
		<tbody>

		<tr><td colspan="2" class="section"><strong>圣诞气氛</strong></td></tr>

		<tr>
			<th>圣诞帽子：</th>
			<td>
				<label><input type="radio" class="radio" name="config[ui-head]" value="Y">带上帽子</label>
				<label><input type="radio" class="radio" name="config[ui-head]" value="N">不带帽子</label>
			</td>
		</tr>

		<tr>
			<th>雪花效果：</th>
			<td>
				<label><input type="radio" class="radio" name="config[ui-snow]" value="Y">雪花效果</label>
				<label><input type="radio" class="radio" name="config[ui-snow]" value="N">禁用效果</label>
			</td>
		</tr>

		<tr>
			<td></td>
			<td>
				<button type="submit" name="Submit" class="submit">保存更改</button>
				<input type="hidden" name="action" id="action" value="update" />
			</td>
		</tr>
		
		</tbody>

	</table>

	<script type='text/javascript'>
	
		//广告形式
		function doModel(){
			var val = Mo("input[name='config[ui-model]']").value();
			switch( val.toString() ){
				//代码广告
				case 'classic':	
					Mo("#adv_classic").show();
					Mo("#adv_modern").hide();
				break;
				
				//自定义
				case 'modern':
					Mo("#adv_modern").show();
					Mo("#adv_classic").hide();
				break;
			}			
		};		
		
		
		Mo("input[name='config[ui-model]']").bind('click', doModel ).value("<?php echo $config["ui-model"];?>");
		doModel();
		
		Mo("input[name='config[ui-menu]']").value("<?php echo $config["ui-menu"];?>");
		Mo("input[name='config[ui-theme]']").value("<?php echo $config["ui-theme"];?>");
		Mo("input[name='config[ui-effect]']").value("<?php echo $config["ui-effect"];?>");
		
		Mo("input[name='config[ui-head]']").value("<?php echo $config["ui-head"];?>");
		Mo("input[name='config[ui-snow]']").value("<?php echo $config["ui-snow"];?>");
		
		//主框架载入事件
		Serv.Manager.Loaded();
	
	</script>

	</form>

	</div>
	
</body>
</html>