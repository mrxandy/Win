/*
*	(C)2009-2013 VeryIDE
*	Mo.js
*	author:
*			Wiki[MO]	gwikimo@gmail.com	
*			Lay 		veryide@qq.com
*
*	#UBB 扩展，包括UBB编辑器、UBB解析等#
*/
if(typeof Mo!="function"){var Mo={plugin:[]}}Mo.UBB={version:"2.0",drag:null,config:null,get:function(a){return document.getElementById(a)},bind:function(a,c){var b=Mo(a).position();Mo("#"+c).bind("click",function(d){Mo(this).hide()}).style({position:"absolute",left:b.left+"px",top:b.top+"px"}).toggle()},show:function(b){var e=b.id;var a=b.toolbar?b.toolbar:[];this.config=a;var d='<div class="ubb">';var f=["字体","font","fontface","大小","size","format","颜色","color","color"];for(var c=0;c<f.length;c+=3){if(Mo.Array(a).indexOf(f[c+1])>-1||a.length==0){d+='<img src="'+Mo.store.host+'static/image/spacer.gif" alt="'+f[c]+'" class="ubb-img img-'+f[c+1]+'" unselectable="on" onclick="Mo.UBB.bind(this,\''+f[c+2]+"');\" />"}}d+='<div id=fontface class="ubb-menu" style="display:none;"><ul>';var f=["Arial","Arial Black","Impact","Verdana","宋体","黑体","楷体_GB2312","幼圆","Microsoft YaHei"];for(var c=0;c<f.length;c++){d+="<li onclick=\"Mo.UBB.face('"+e+"','"+f[c]+'\');" style="font-family:'+f[c]+'" unselectable="on" onfocus="this.blur();">'+f[c]+"</li>"}d+="</ul></div>";d+='<div id=format class="ubb-menu" style="display:none;"><ul>';for(var c=1;c<=6;c++){d+="<li onclick=\"Mo.UBB.size('"+e+"','"+c+'\');" unselectable="on" onfocus="this.blur();"><font size="'+c+'" unselectable="on">'+c+"</font></li>"}d+="</ul></div>";d+='<div id=color class="ubb-menu ubb-color" style="display:none;"><ul>';var f=["黑色","black","灰色","gray","茶色","maroon","红色","red","紫色","purple","紫红","fuchsia","绿色","green","亮绿","lime","橄榄","olive","黄色","yellow","深蓝","teal","蓝色","blue","浅绿","aqua","粉红","pink","橙色","orange","褐色","brown"];for(var c=0;c<f.length;c+=2){d+="<li onclick=\"Mo.UBB.color('"+e+"','"+f[c+1]+'\');" style="color:'+f[c+1]+'" unselectable="on">'+f[c]+"</li>"}d+="</ul></div>";d+='<div id="smile" class="ubb-menu ubb-smile" style="display:none;"><ul>';for(var c=0;c<=103;c++){d+="<li onclick=\"Mo.UBB.smile('"+e+"','"+c+'\');" unselectable="on"><img src="'+Mo.store.host+"static/image/smile/"+c+'.gif" alt="'+c+'" unselectable="on" /></li>'}d+="</ul></div>";d+='<div id="code" class="ubb-menu" style="display:none;"><ul>';var f=["JavaScript","js","XML","xml","VB","vb","SQL","sql","Java","java","CSS","css","PHP","php"];for(var c=0;c<f.length;c+=2){d+="<li onclick=\"Mo.UBB.code('"+e+"','"+f[c+1]+'\');" unselectable="on">'+f[c]+"</li>"}d+="</ul></div>";var f=["加粗","bold","斜体","italic","下划线","under","左对齐","left","居中","center","右对齐","right"];for(var c=0;c<f.length;c+=2){if(Mo.Array(a).indexOf(f[c+1])>-1||a.length==0){d+='<img src="'+Mo.store.host+'static/image/spacer.gif" alt="'+f[c]+'" class="ubb-img img-'+f[c+1]+'" onclick="Mo.UBB.'+f[c+1]+"('"+e+'\')" unselectable="on" />'}}var f=["表情","smile","smile","代码","code","code"];for(var c=0;c<f.length;c+=3){if(Mo.Array(a).indexOf(f[c+1])>-1||a.length==0){d+='<img src="'+Mo.store.host+'static/image/spacer.gif" alt="'+f[c]+'" class="ubb-img img-'+f[c+1]+'" unselectable="on" onclick="Mo.UBB.bind(this,\''+f[c+2]+"');\" />"}}var f=["链接","link","图片","image","FLASH","flash","视频","video","音乐","mp3","引用","quote","仅会员浏览","hidden","转换复制的HTML","html"];for(var c=0;c<f.length;c+=2){if(Mo.Array(a).indexOf(f[c+1])>-1||a.length==0){d+='<img src="'+Mo.store.host+'static/image/spacer.gif" alt="'+f[c]+'" class="ubb-img img-'+f[c+1]+'"  onclick="Mo.UBB.'+f[c+1]+"('"+e+'\')" unselectable="on" />'}}var f=["最佳尺寸/原始尺寸","zoom","放大输入框","zoomin","缩小输入框","zoomout","关于","about"];for(var c=0;c<f.length;c+=2){if(Mo.Array(a).indexOf(f[c+1])>-1||a.length==0){d+='<img src="'+Mo.store.host+'static/image/spacer.gif" alt="'+f[c]+'" class="ubb-img img-'+f[c+1]+'"  onclick="Mo.UBB.'+f[c+1]+"('"+e+'\')" unselectable="on" />'}}if(Mo.Array(a).indexOf("stat")>-1||a.length==0){d+='<span id="'+e+'_stat" class="ubb-stat"></span>';Mo.reader(function(){Mo("#"+e).bind("keyup",function(){Mo("#"+e+"_stat").html("字数:"+Mo.String(this.value).length())})})}d+="</div>";Mo.write(d)},face:function(d,b){var a="[face="+b+"]";var c="[/face]";Mo.UBB.insert(d,a,c)},size:function(d,b){var a="[size="+b+"]";var c="[/size]";Mo.UBB.insert(d,a,c)},code:function(d,b){var a="[code]";var c="[/code]";Mo.UBB.insert(d,a,c)},color:function(d,b){var a="[color="+b+"]";var c="[/color]";Mo.UBB.insert(d,a,c)},smile:function(d,b){var a="[smile]"+b;var c="[/smile]";Mo.UBB.insert(d,a,c,false)},code:function(d,b){var a="[code="+b+"]\n";var c="\n[/code]";Mo.UBB.insert(d,a,c)},bold:function(c){var a="[b]";var b="[/b]";Mo.UBB.insert(c,a,b)},italic:function(c){var a="[i]";var b="[/i]";Mo.UBB.insert(c,a,b)},under:function(c){var a="[u]";var b="[/u]";Mo.UBB.insert(c,a,b)},left:function(c){var a="[align=left]";var b="[/align]";Mo.UBB.insert(c,a,b)},center:function(c){var a="[align=center]";var b="[/align]";Mo.UBB.insert(c,a,b)},right:function(c){var a="[align=right]";var b="[/align]";Mo.UBB.insert(c,a,b)},link:function(c){var b=prompt("请输入链接要显示的文字,只能包含中文,英文字母,或中英文混合","请点击这里");if(!b){return}var a=prompt("请输入URL地址","http://");if(!a){return}Mo.UBB.get(c).value+=(!b)?"[url]"+a+"[/url]":"[url="+a+"]"+b+"[/url]";Mo.UBB.get(c).focus()},flash:function(f){var e=prompt("请输入Flash的URL地址","http://");if(!e){return}if(!/^http/.test(e)){alert("URL地址格式不对");return}var d=prompt("请输入Flash高度和宽度","350,200");var g="[flash="+d+"]"+e+"[/flash]";Mo.UBB.get(f).value+=g;Mo.UBB.get(f).focus()},mp3:function(f){var e=prompt("请输入音频文件的URL地址","http://");if(!e){return}if(!/^http/.test(e)){alert("URL地址格式不对");return}var d=prompt("请输入音频文件播放器高度和宽度","220,40");var g="[mp3="+d+"]"+e+"[/mp3]";Mo.UBB.get(f).value+=g;Mo.UBB.get(f).focus()},hidden:function(c){var a="[hidden]";var b="[/hidden]";Mo.UBB.insert(c,a,b)},image:function(d){var b=prompt("请输入图片的URL地址","http://");if(!b){return}if(!/^http/.test(b)){alert("URL地址格式不对");return}var e="[img]"+b+"[/img]";Mo.UBB.get(d).value+=e;Mo.UBB.get(d).focus()},video:function(h){var g="true";var e=prompt("请输入视频文件地址","");if(e==null||e==""||e==""){return}var d=prompt("请输入视频文件显示大小","400,250");if(d==null||d==""||d==""){d="400,250"}var i=prompt("请输入是否自动播放,默认为自动播放(yes自动，no不自动播放）","yes");if(i!="yes"){g="false"}var f="[embed="+d+","+g+"]"+e+"[/embed]";Mo.UBB.get(h).value+=f;Mo.UBB.get(h).focus()},quote:function(c){var a="[quote]";var b="[/quote]";Mo.UBB.insert(c,a,b)},zoom:function(a){var b=Mo("#"+a).item(0);if(b.scrollHeight>b.offsetHeight){b.style.height=b.scrollHeight+"px"}else{b.style.height="auto"}},zoomin:function(a){var b=Mo("#"+a).item(0);b.rows+=5},zoomout:function(a){var b=Mo("#"+a).item(0);if(b.rows>=10){b.rows-=5}},about:function(a){alert("VeryIDE UBBeditor "+Mo.UBB.version+" \n\nhttp://www.veryide.com/")},html:function(c){var f=c+"_iframe";if(!Mo.UBB.get(f)){var a=document.createElement("iframe");a.id=f;a.name=f;a.style.width="0px";a.style.height="0px";a.style.border="0";document.body.appendChild(a);var a=window.frames[f].document;a.designMode="On";a.open();a.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">				<html xmlns="http://www.w3.org/1999/xhtml">				<head>				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />				<title>测试</title>				</head>				<body ></body>				</html>');a.close()}var a=window.frames[f].document;a.execCommand("SelectAll",false,null);a.execCommand("Delete",false,null);try{a.execCommand("paste",false,null)}catch(b){alert("Sorry!\n\n当前浏览器暂不支持粘贴操作");return false}var d=a.body.innerHTML;if(d){d=d.replace(/\r/g,"");d=d.replace(/on(error|load|unload|resize|blur|change|click|dblclick|focus|keydown|keypress|keyup|mousewheel|mousemove|mousedown|mouseout|mouseover|mouseup|select)="[^"]+"/ig,"");d=d.replace(/<script[^>]*?>([\w\W]*?)<\/script>/ig,"");d=d.replace(/<a[^>]+href="([^"]+)"[^>]*>(.*?)<\/a>/ig,"\n[url=$1]$2[/url]\n");d=d.replace(/<font[^>]+color=([^ >]+)[^>]*>(.*?)<\/font>/ig,"\n[color=$1]$2[/color]\n");d=d.replace(/<img[^>]+src="([^"]+)"[^>]*>/ig,"\n[img]$1[/img]\n");d=d.replace(/<([\/]?)b>/ig,"[$1b]");d=d.replace(/<([\/]?)strong>/ig,"[$1b]");d=d.replace(/<([\/]?)u>/ig,"[$1u]");d=d.replace(/<([\/]?)i>/ig,"[$1i]");d=d.replace(/&nbsp;/g," ");d=d.replace(/&amp;/g,"&");d=d.replace(/&quot;/g,'"');d=d.replace(/&lt;/g,"<");d=d.replace(/&gt;/g,">");d=d.replace(/<br>/ig,"\n");d=d.replace(/<[^>]*?>/g,"");d=d.replace(/\[url=([^\]]+)\]\n(\[img\]\1\[\/img\])\n\[\/url\]/g,"$2");d=d.replace(/\n+/g,"\n");Mo.UBB.get(c).value+=d}else{alert("无需转换的HTML内容")}},getSel:function(){return window.getSelection?window.getSelection():document.selection},getRng:function(){var c=this.getSel(),a;try{a=c.rangeCount>0?c.getRangeAt(0):(c.createRange?c.createRange():document.createRange())}catch(b){}if(!a){a=document.all?document.body.createTextRange():document.createRange()}return a},insert:function(n,p,u,t){var n=Mo("#"+n).item(0);function l(e){return(document.all&&e.indexOf("\n")!=-1)?e.replace(/\r?\n/g,"_").length:e.length}function b(e){if(!e.hasfocus){e.focus()}}function c(e){return typeof e=="undefined"?true:false}n.focus();if(document.selection){var a=document.selection.createRange().getBookmark();var r=n.createTextRange();r.moveToBookmark(a);var d=n.createTextRange();d.collapse(true);d.setEndPoint("EndToStart",r);n.selectionStart=l(d.text);n.selectionEnd=d.text.length+r.text.length;n.selectedText=r.text;var m=document.selection.createRange();var i=l(m.text);m.text=p+m.text+u;var j=n.selectionStart+l(p);var g=i}else{if(window.getSelection&&n.selectionStart>-1){var q=n.selectionStart;var k=n.selectionEnd;n.value=n.value.substring(0,q)+p+n.value.substring(q,k)+u+n.value.slice(k);var j=q+l(p);var g=k+l(p)}else{n.value+=p+u;n.focus()}}if(t===false){return}if(n.createTextRange){var h=n.value.length;var o=n.createTextRange();o.moveStart("character",-h);o.moveEnd("character",-h);o.moveStart("character",j);o.moveEnd("character",g);o.select()}else{n.setSelectionRange(j,g);n.focus()}}};Mo.plugin.push("ubb");