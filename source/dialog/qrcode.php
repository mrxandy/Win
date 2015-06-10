<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

//载入依赖库以及配置
require_once dirname(__FILE__).'/../../app.php';
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_G['product']['charset'];?>" />
<title>二维码（QRcode）API</title>
<meta name="Description" content="qrcode encode program (QRcode cgi demo page.You can try to encode QRcode.)" />
<meta name="Keywords" content="qrcode,qr code,barcode" />
<?php
echo loader_style(array(VI_BASE."static/style/general.css",VI_BASE."static/style/share.css"),"utf-8",$_G['product']['version']);

echo loader_script(array(VI_BASE."static/js/mo.js",VI_BASE."static/js/mo.form.js"),"utf-8",$_G['product']['version']);
?>

<script>

Mo.reader(function(){
				   
	Mo('#qrcode').hide();
				   
	var data = Mo.get('data');
	
	/////////////////////////////
	
	var func = function( form ){
		if( form.chl.value ){
			var url = 'http://chart.apis.google.com/chart?cht=qr'+ Mo.Serialize( form, null, '&' );		
			Mo('#qrcode').html( '<a href="'+url+'" target="_blank"><img src="'+ url +'" /></a>' ).show();
		}else{
			Mo('#qrcode').hide();	
		}
		
	}	
	
	/////////////////////////////	

	//绑定表单事件
	Mo("#wrapper form").bind( 'submit', function( e ){
		Mo.Event( e ).stop();
		func( this );
	}).bind( 'reset', function( e ){
		Mo('#qrcode').hide();
	});
	
	/////////////////////////////
	
	if( data ){
		
		Mo("#wrapper form input[name=chl]").value( data ).focus();
		
		func( Mo("#wrapper form").item(0) );
	}

});

</script>

</head>
<body>


	<div id="wrapper">
    
    	<div id="header">
        	<h2>二维码（QRcode）API</h2>
        </div>
        
        <div id="main">
		
			<form method="get">
			
			<p>
				内容数据：
                <input name="chl" type="text" size="100" />
			</p>
			
			<p>
				容错级别：
					<select name="chld">
					<option value="L">lelvel L			</option>
					<option value="M" selected="selected">level M			</option>
					<option value="Q">level Q			</option>
					<option value="H">level H			</option>
				</select>

				数据编码：
				<select name="choe">
					<option value="UTF-8">UTF-8</option>
				</select>

				条码尺寸：
				<select name="chs">
					<option>60x60</option>
					<option>80x80</option>
					<option>100x100</option>
					<option>120x120</option>
					<option selected="selected">200x200</option>
					<option>300x300</option>
					<option>500x500</option>
				</select>
                
				<button type="submit" class="button">生成</button>
				<button type="reset" class="cancel">重置</button>			
			</p>
			
			</form>
            
			<p id="qrcode"></p>

        </div>
       
    	<div id="footer">
			<?php echo $_G['project']["powered"];?>
            <?php echo $_G['product']["appname"];?>
            <?php echo $_G['product']["version"];?>
        </div>
    
    
    </div>


</body>

</html>