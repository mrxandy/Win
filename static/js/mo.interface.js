/*
*	(C)2009-2013 VeryIDE
*	Mo.js
*	author:
*			Wiki[MO]	gwikimo@gmail.com	
*			Lay 		veryide@qq.com
*
*	$Id: mo.interface.js,v1.0 8:43 2011/2/11 Lay $
*/
if(typeof Mo!="function"){var Mo={plugin:[]}}Mo.Command=function(c,g,b){if(typeof b!="function"){var b=function(){}}if(typeof g!="object"){var g={title:document.title,href:location.href}}var f="";switch(c){case"favorite":try{if(window.sidebar&&"object"==typeof(window.sidebar)&&"function"==typeof(window.sidebar.addPanel)){window.sidebar.addPanel(g.title,g.href,"")}else{if(document.all&&"object"==typeof(window.external)){window.external.addFavorite(g.href,g.title)}else{alert("您使用的浏览器不支持此功能，请按 Ctrl + D 键加入收藏")}}}catch(d){alert("您使用的浏览器不支持此功能，请按 Ctrl + D 键加入收藏")}break;case"homepage":if(Mo.Browser.msie){obj=document.createElement("a");obj.setAttribute("href","javascript:void(0);");document.body.appendChild(obj);obj.style.behavior="url(#default#homepage)";obj.sethomepage(g.href)}else{try{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect")}catch(d){f="此操作被浏览器拒绝！<br />请在浏览器地址栏输入“about:config”并回车<br />然后将[signed.applets.codebase_principal_support]设置为'true'"}var a=Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefBranch);a.setCharPref("browser.startup.homepage",g.href)}break}b(f);return void (0)};Mo.Clipboard=function(i,c){if(typeof c!="function"){var c=function(){}}if(window.clipboardData){window.clipboardData.setData("Text",i)}else{if(window.netscape){try{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect")}catch(g){throw new SecurityException(SecurityException.ERROR,"")}var d=Components.classes["@mozilla.org/widget/clipboard;1"].createInstance(Components.interfaces.nsIClipboard);if(!d){return}var j=Components.classes["@mozilla.org/widget/transferable;1"].createInstance(Components.interfaces.nsITransferable);if(!j){return}j.addDataFlavor("text/unicode");var h=new Object();var f=new Object();var h=Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);var b=i;h.data=b;j.setTransferData("text/unicode",h,b.length*2);var a=Components.interfaces.nsIClipboard;if(!d){return false}d.setData(j,null,a.kGlobalClipboard)}}c(i);return false};Mo.plugin.push("interface");