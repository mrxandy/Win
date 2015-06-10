<?php

//载入全局配置和函数包
require_once dirname(__FILE__).'/../../app.php';

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_G['product']['charset'];?>" />
<title>Editor - Powered By VeryIDE</title>
<?php
	echo loader_style(VI_BASE."static/style/general.css",VI_BASE."static/style/share.css",'utf-8',$_G['product']['version']);

    echo loader_script(array(VI_BASE."source/editor/kindeditor.js",VI_BASE."source/editor/lang/zh_CN.js"),'utf-8',$_G['product']['version']);
	
	//跨域支持（子域名必需）
	echo $_GET['crossdomain'] == 'true' ? '<script>'. System :: cross_domain() .'</script>' : '';
?>
</head>

<body>

<p style="text-align:right; overflow:hidden;">
    <textarea name="content" cols="50" rows="10" id="content" style="width:100%;height:320px; display:none;"></textarea>
</p>
<p style="text-align:right;">
    <button class="button" name="finish">完成</button>
    <button class="cancel" name="cancel">取消</button>
</p>

<script type="text/javascript">
var editor;
KindEditor.ready(function(K) {
						  
	editor = K.create('#content', {
		resizeType : 2,
		newlineTag : 'p',
		allowFileManager : false,
		filterMode : false,
		loadStyleMode : true,
		urlType : 'domain',
		items : [
			'source', '|', 'undo', 'redo', '|', 'template', 'cut', 'copy', 'paste',
			'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
			'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
			'superscript', 'clearhtml', 'quickformat', 'selectall','/',
			'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
			'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage',
			'flash', 'media', 'insertfile', 'table', 'hr', 'emoticons','link', 'unlink', '|', 'fullscreen'
		],
		// 相对于当前页面的路径
		uploadJson : '<?php echo VI_BASE;?>source/editor/upload.php?crossdomain=<?php echo $_GET['crossdomain'];?>&watermark=true'
	});
	
	editor.html( parent.Editor.getData() );
	
	K.query('button[name=finish]').onclick = function(){
		parent.Editor.setData( editor.html() );
	};
	
	K.query('button[name=cancel]').onclick = function(){
		parent.Editor.Remove();
	};

});

</script>


</body>
</html>
