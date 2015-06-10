/*
*	(C)2009-2013 VeryIDE
*	app.js
*	投票模块前台验证脚本
*/

function setcopy(text, alertmsg){
	if( Mo.Browser.msie ) {
		clipboardData.setData('Text', text);
		alert(alertmsg);
	} else if(prompt('Press Ctrl+C Copy to Clipboard', text)) {
		alert(alertmsg);
	}
}

Mo.reader(function(){

	Mo('*[data-object=dialog]').hide();
	
	////////////////////

	Mo('*[data-object=\'captcha-image\']').bind('click',function(){
		
		this.src = this.src + '&rand='+Math.random();
		
		Mo('*[data-object=\'captcha-input\']').value('');
		
	});

	////////////////////
	
	//处理拉票
	if( Mo("form[data-appid=vote]").attr('data-pull') == 'Y' && Mo("*[data-mark=option]").size() ){
	
		//高亮当前
		Mo("*[data-mark=option]").each(function( index ){
			
			var id = location.hash.substr(1);
							   
			var mark = Mo( this ).attr("data-option");
			
			if( id && mark == id ){
				Mo( this ).attr({"className":"invite"}).create( 'img', {"src":"js/butterfly.gif"}, true ).style({"position":"absolute","marginTop":"-100px",'width':'auto','height':'auto'});
				Mo("input",this).checked( true );
				this.scrollIntoView(true);
				return false;
			}
							   
		});
		
		//生成拉票按钮
		Mo( document.body ).create( "div", {"id" : "favor"} );
		
		Mo('#favor').hide();
		
		Mo("*[data-mark=option]").bind("mouseover",function( index, e ){
				
			var pos = Mo( this ).position();
			
			var mark = Mo( this ).attr("data-option");
			
			var link = location.href.replace(location.hash,"");
			
			Mo("#favor").style({"top":pos.top+5+"px","left":pos.left+pos.width-40+"px"}).html('<a href="#'+mark+'" onclick="setcopy(\''+link+'#'+mark+'\', \'成功复制链接地址\\n\\n可将它发送给你的朋友，让他们参与投票！\');return false;" title="复制地址">拉票</a>').show();
							   
		});
	
	}
	
	////////////////////

	//表单验证方法
	var doValid = function( form ){
	
		//显示匿名信息框
		if( Mo('*[data-object=dialog]').size() && Mo('*[data-object=dialog]').style('display') == 'none' ){
			Mo('*[data-object=dialog]').style({'display':'block'});
			Mo('*[data-object=dialog] input').focus();
			return false;
		}
		
		///////////
	
		var result= Mo.ValidForm( form, function(i){
			if( typeof Passport == 'undefined' ){
				alert(i);
			}else{
				Mo.Message( 'error', i, 3, { "unique" : "pp-message", "center" : true } );
			}
		});
		
		///////////

		if( result == false ){
		
			//恢复按钮可用
			Mo("form[data-appid=vote] button[type=submit]").each(function( ){
				if( this.getAttributeNode("value") && this.getAttributeNode("value").nodeValue ) Mo( this ).attr({"name" : this.getAttribute('data-name') }).enabled();
			});
			
		};
		
		///////////
		
		result && form.submit();
	
	}
	
	//绑定表单提交
	Mo("form[data-appid=vote]").bind('submit',function( index, e ){

		Mo.Event( e ).stop();
		
		//doValid( this );
		
	});
	
	////////////////////		
	
	//修正 IE6 会提交全部 Button 的问题，并且 value 是 innerHTML
	Mo("form[data-appid=vote] button[type=submit]").bind('click',function( ){
	
		if( this.name && this.getAttributeNode("value").nodeValue ){
			
			//禁用全部按钮
			Mo("form[data-appid=vote] button[type=submit]").each(function(){
				if( this.getAttributeNode("value") && this.getAttributeNode("value").nodeValue ) Mo( this ).attr({"data-name" : this.name}).disabled();
			});
			
			//生成隐藏字段
			Mo("form[data-appid=vote]").create( "input" , { "type" : "hidden", "name" :  this.name, "value" :  this.getAttributeNode("value").nodeValue } );
						
			//启用当前按钮
			Mo( this ).attr({"name":""}).enabled();
			
		}
	
		//提交当前表单
		doValid( this.form );
		
	});
	
	////////////////////
	
	Mo("form[data-appid=vote] input").bind('click',function( index, e ){
	
		Mo.Event( e ).stop( 1 );
	
		var groupnm = this.getAttribute('data-valid-name');
		var minsize = parseInt( this.getAttribute('data-valid-minsize') );
		var maxsize = parseInt( this.getAttribute('data-valid-maxsize') );
	
		if( this.getAttribute('type') == 'checkbox' && ( minsize || maxsize ) ){
		
			var selected = 0;
			
			Mo("input[name='"+ this.name +"']").each(function(){
				if( this.checked ) selected ++;
			});
			
			//小于选择范围
			if( minsize && selected < minsize ){
				Mo.Message( 'error', groupnm + '至少要选择 '+ minsize +' 个哦～', 3, { "unique" : "pp-message", "center" : true } );
				//this.checked = true;
			}
			
			//超出选择范围
			if( maxsize && selected > maxsize ){
				Mo.Message( 'error', groupnm + '最多能选择 '+ maxsize +' 个哦～', 3, { "unique" : "pp-message", "center" : true } );
				this.checked = false;
			}
			
		}else{
			
			//this.checked = true;
			
		}
		
	});
	
	
	////////////////////
	
	Mo("form[data-appid=vote] li").bind('click',function( index, e ){	
	
		//停止冒泡
		Mo.Event( e ).stop( 1 );
		
		//模拟点击
		Mo( 'input', this ).event('click');
		
	});
	
	////////////////////
	
	//加载图片
	Mo.Lazy.init();
	Mo.Lazy.run();
	
});