/*
*	(C)2009-2013 VeryIDE
*	Mo.js
*	author:
*			Wiki[MO]	gwikimo@gmail.com	
*			Lay 		veryide@qq.com
*
*	#拖动扩展，包括对象拖动、限制范围、回调函数等#
*/
if(typeof Mo!="function"){var Mo={plugin:[]}}Mo.Drag=function(c){var d=this;this.obj=null;this.init=function(g,k,i,e,h,a,l,j,m,f){g.onmousedown=d.start;g.hmode=l?false:true;g.vmode=j?false:true;g.root=k&&k!=null?k:g;if(g.hmode&&isNaN(parseInt(g.root.style.left))){g.root.style.left="0px"}if(g.vmode&&isNaN(parseInt(g.root.style.top))){g.root.style.top="0px"}if(!g.hmode&&isNaN(parseInt(g.root.style.right))){g.root.style.right="0px"}if(!g.vmode&&isNaN(parseInt(g.root.style.bottom))){g.root.style.bottom="0px"}g.minX=typeof i!="undefined"?i:null;g.minY=typeof h!="undefined"?h:null;g.maxX=typeof e!="undefined"?e:null;g.maxY=typeof a!="undefined"?a:null;g.xMapper=m?m:null;g.yMapper=f?f:null;d.onStart=new Function();d.onEnd=new Function();d.onDrag=new Function()};this.start=function(f){var g=d.obj=this;f=d.fixE(f);var h=parseInt(g.vmode?g.root.style.top:g.root.style.bottom);var a=parseInt(g.hmode?g.root.style.left:g.root.style.right);d.onStart.call(g.root,a,h,f);g.lastMouseX=f.clientX;g.lastMouseY=f.clientY;if(g.hmode){if(g.minX!=null){g.minMouseX=f.clientX-a+g.minX}if(g.maxX!=null){g.maxMouseX=g.minMouseX+g.maxX-g.minX}}else{if(g.minX!=null){g.maxMouseX=-g.minX+f.clientX+a}if(g.maxX!=null){g.minMouseX=-g.maxX+f.clientX+a}}if(g.vmode){if(g.minY!=null){g.minMouseY=f.clientY-h+g.minY}if(g.maxY!=null){g.maxMouseY=g.minMouseY+g.maxY-g.minY}}else{if(g.minY!=null){g.maxMouseY=-g.minY+f.clientY+h}if(g.maxY!=null){g.minMouseY=-g.maxY+f.clientY+h}}document.onmousemove=d.drag;document.onmouseup=d.end;return false};this.drag=function(i){i=d.fixE(i);var j=d.obj;var g=i.clientY;var h=i.clientX;var l=parseInt(j.vmode?j.root.style.top:j.root.style.bottom);var f=parseInt(j.hmode?j.root.style.left:j.root.style.right);var a,k;if(j.minX!=null){h=j.hmode?Math.max(h,j.minMouseX):Math.min(h,j.maxMouseX)}if(j.maxX!=null){h=j.hmode?Math.min(h,j.maxMouseX):Math.max(h,j.minMouseX)}if(j.minY!=null){g=j.vmode?Math.max(g,j.minMouseY):Math.min(g,j.maxMouseY)}if(j.maxY!=null){g=j.vmode?Math.min(g,j.maxMouseY):Math.max(g,j.minMouseY)}a=f+((h-j.lastMouseX)*(j.hmode?1:-1));k=l+((g-j.lastMouseY)*(j.vmode?1:-1));if(j.xMapper){a=j.xMapper(l)}else{if(j.yMapper){k=j.yMapper(f)}}d.obj.root.style[j.hmode?"left":"right"]=a+"px";d.obj.root.style[j.vmode?"top":"bottom"]=k+"px";d.obj.lastMouseX=h;d.obj.lastMouseY=g;d.onDrag.call(d.obj.root,a,k,i);return false};this.end=function(a){a=d.fixE(a);document.onmousemove=null;document.onmouseup=null;d.onEnd.call(d.obj.root,parseInt(d.obj.root.style[d.obj.hmode?"left":"right"]),parseInt(d.obj.root.style[d.obj.vmode?"top":"bottom"]),a);d.obj=null};this.fixE=function(a){if(typeof a=="undefined"){a=window.event}if(typeof a.layerX=="undefined"){a.layerX=a.offsetX}if(typeof a.layerY=="undefined"){a.layerY=a.offsetY}return a};var b=arguments;if(b.length){this.init(b[0],b[1],b[2],b[3],b[4],b[5],b[6],b[7],b[8],b[9])}};Mo.plugin.push("drag");