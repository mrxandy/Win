
Mo.reader(function(){
	
	//显示表情		
	Mo("#MESSAGE").bind('click',function(){

		//smile
		var box=Mo("#form-smile").item(0);
		if(!Mo("#form-smile").html()){
			var html="";
			for (i = 0; i <= 104; i++){
				html+='<img onclick="addSmile('+i+');" src="'+Mo.base+'../image/smile/'+i+'.gif" />';
			}
			box.innerHTML=html;
		}

	});
		
});

function getMessage(fid,object,page){

	//ajax
	var ajax = new Mo.Ajax("api.php");
	ajax.method = "GET";
	ajax.setVar({
		"new":Math.random(),"&action":"message","&fid":fid,"&object":object,"&page":(page)
	});
	ajax.onError = function (){
		Mo.Message('error','error',ajax.response)
	}
	
	ajax.onCompletion = function (){
		var response	= ajax.responseXML;
		var result		= response.getElementsByTagName("result")[0].firstChild;
		
		if(result){
			switch(result.data){
				case "true":

					var message	= response.getElementsByTagName("message");
					var html="";
						for(var i=0;i<message.length;i++){
							var msg = message[i];

							var user = msg.getElementsByTagName("username")[0].firstChild ? msg.getElementsByTagName("username")[0].firstChild.data : "匿名用户";
							var date = msg.getElementsByTagName("dateline")[0].firstChild.data;
							var cont = msg.getElementsByTagName("content")[0].firstChild.data ? msg.getElementsByTagName("content")[0].firstChild.data : "投Ta一票";
							
							html+='<dt><strong>'+user+'</strong><span>'+date+'</span></dt><dd>'+cont+'</dd>';					
						}
						
						//smile
						html=html.replace(/\[smile\]([\d]+)\[\/smile\]/ig, '<img src="'+Mo.base+'../image/smile/$1.gif" />');
						
						Mo("#"+object+"_BOX").html(html);

					/***********/
					
					var result		= response.getElementsByTagName("result")[0];
					var html="";
					
					var rowcoun=parseInt(result.getAttribute("rowcount"));
					var pagecur=parseInt(result.getAttribute("pagecurrent"));
					var pagecou=parseInt(result.getAttribute("pagecount"));
						
						if(pagecur>1){
							html="<a href='javascript:void(0);' onclick='getMessage("+fid+",\""+object+"\","+(pagecur-1)+")'>上一页</a>";
						}else{
							html+="<span>上一页</span>";
						}
						
						html+="<strong>"+pagecur+" / "+pagecou+" ["+rowcoun+"]</strong>";
						
						if(pagecur<pagecou){
							html+="<a href='javascript:void(0);' onclick='getMessage("+fid+",\""+object+"\","+(pagecur+1)+")'>下一页</a>";
						}else{
							html+="<span>下一页</span>";
						}
						
						Mo("#"+object+"_PAGE").html(html);
						
					//滚动窗口
					//$("header").scrollIntoView(true);
					
				break;
				
				case "zero":
					//VeryIDE.Message('error','error',"没有评论内容...");
				break;
				
				case "false":
					Mo.Message('error','error',"查询投票数失败...");
				break;
				
				case "mysql":
					Mo.Message('error','error',"MySQL错误,请联系管理员!");
				break;
			}
		}
	}
	ajax.send("");

}

function addSmile( o ){
	var smile="[smile]"+o+"[/smile]";
	
	var obj = Mo("#MESSAGE").item(0);
	
	if (document.selection) {
		obj.focus();
		sel = document.selection.createRange();
		sel.text = smile;
	}else if (obj.selectionStart || obj.selectionStart == '0') {
		var startPos = obj.selectionStart;
		var endPos = obj.selectionEnd;
		var cursorPos = endPos;
		obj.value = obj.value.substring(0, startPos)
					  + smile
					  + obj.value.substring(endPos,obj.value.length);
		cursorPos += smile.length;
		obj.focus();
		obj.selectionStart = cursorPos;
		obj.selectionEnd = cursorPos;
	}else{
		obj.value +=smile;
		obj.focus();
	}	
}
