/*
*	(C)2009-2013 VeryIDE
*	Mo.js
*	author:
*			Wiki[MO]	gwikimo@gmail.com	
*			Lay 			veryide@qq.com
*			
*	#延时加载处理类#
*/

if(typeof Mo!="function"){var Mo={plugin:[]}}Mo.Lazy=(function(){var d=null;var f={};function e(h,i,g){if(h.addEventListener){h.addEventListener(i,g,false)}else{if(h.attachEvent){h.attachEvent("on"+i,g)}else{h["on"+i]=g}}}function c(){if(d){return""}d=setTimeout(function(){b();try{clearTimeout(d)}catch(g){}d=null},100)}function b(){var n={};var h={};n.Top=document.body.scrollTop+document.documentElement.scrollTop;n.Left=document.documentElement.scrollLeft;h.Top=n.Top+document.documentElement.clientHeight;h.Left=n.Left+document.documentElement.clientWidth;for(var m in f){if(f[m]){var l=f[m];var k=document.getElementById(m);if(!k){continue}var o=k.clientWidth;var g=k.clientHeight;var j=a(k);if((j.Top>=n.Top&&j.Top<=h.Top&&j.Left>=n.Left&&j.Left<=h.Left)||((j.Top+g)>=n.Top&&j.Top<=h.Top&&(j.Left+o)>=n.Left&&j.Left<=h.Left)){k.src=l.src;delete f[m]}}}}function a(h){var g={Top:0,Left:0};while(!!h){g.Top+=h.offsetTop;g.Left+=h.offsetLeft;h=h.offsetParent}return g}return{init:function(){for(var j=0;j<document.images.length;j++){var g=document.images[j];var h={};h.id=g.id;h.src=g.getAttribute("_src");if(h.src&&!h.id){h.id=encodeURIComponent(h.src)+Math.random();g.id=h.id}if(!h.id||!h.src){continue}Mo.Lazy.push(h)}},push:function(g){f[g.id]=g},run:function(){b();e(window,"scroll",c)}}})();