/*
*	(C)2009-2013 VeryIDE
*	Mo.js
*	author:
*			Wiki[MO]	gwikimo@gmail.com	
*			Lay 		veryide@qq.com
*
*	#效果扩展，包括倒计时、进度条、星级评分等#
*/
if(typeof Mo!="function"){var Mo={plugin:[]}}Mo.Grade=function(f,d,a,g,h,b,e){this.Box=f;this.Min=d;this.Max=a;this.Def=g;this.Grey=h;this.Light=b;this.Func=e;var c=this;this.Inti=function(){for(var j=this.Min;j<=this.Max;j++){Mo(f).create("img",{"data-index":j,src:this.Grey})}if("ontouchstart" in window){Mo(f+" img").bind("touchmove",function(i){c.Play(this.getAttribute("data-index"));c.Return(this.getAttribute("data-index"))}).bind("touchend",function(i){c.Def=this.getAttribute("data-index");c.Return(c.Def)})}else{Mo(f+" img").bind("mouseover",function(i){c.Play(this.getAttribute("data-index"));c.Return(this.getAttribute("data-index"))}).bind("mouseout",function(i){c.Play(c.Def);c.Return(c.Def)}).bind("click",function(i){c.Def=this.getAttribute("data-index");c.Return(c.Def)})}};this.Play=function(i){Mo(f+" img").each(function(k,j){if(this.getAttribute("data-index")<=i){this.src=c.Light}else{this.src=c.Grey}})};this.Return=function(i){if(typeof this.Func=="function"){this.Func(i)}};this.Inti();this.Play(this.Def)};Mo.DateDiff=function(a,c){if(typeof c!="function"){var c=function(){}}if(!a){return false}var a=Math.round(parseInt(a)*1000);if(new Date().getTime()>=a){c(-1,{d:0,h:0,m:0,s:0})}else{var b=a;window.setInterval(function(){if(new Date().getTime()>a){c(-1,{d:0,h:0,m:0,s:0})}else{var t=-1;var u=-1;var s=-1;var r=24*60*60*1000;var m=60*60*1000;var p=60*1000;var j=1000;var h=new Date();var k=t;var i=u;var f=s;var g=Diffms=b-h.getTime();t=Math.floor(Diffms/r);Diffms-=t*r;u=Math.floor(Diffms/m);Diffms-=u*m;s=Math.floor(Diffms/p);Diffms-=s*p;var e=Math.floor(Diffms/j);if(k!=t){var q=t}if(i!=u){var o=u}if(f!=s){var n=s;var l=e}c(parseInt(g/1000),{d:q,h:o,m:n,s:l})}},1000)}};Mo.Process=function(){this.groups=[];var a=this;var b=function(c){return document.getElementById(c)};this.Group=function(c){a.groups[c]={GID:c,COUNT:0,OPTIONS:[]};this.gid=c;this.Option=function(d,e){e=(e!=0)?parseInt(e):0;a.groups[this.gid]["COUNT"]+=e;if(d!==null){a.groups[this.gid]["OPTIONS"][d]={OID:d,COUNT:e}}}};this.Show=function(){for(var c in a.groups){for(var e in a.groups[c]["OPTIONS"]){var d=Math.round(a.groups[c]["OPTIONS"][e]["COUNT"]/a.groups[c]["COUNT"]*100);(function(){var k=e;var h=d;var f=0;var g=function(){if(f<=h){b("option-"+k).style.width=f+"%";b("percent-"+k).innerHTML=f+"%";f++}else{clearInterval(j)}};var j=window.setInterval(g,2)})()}}}};Mo.plugin.push("effect");