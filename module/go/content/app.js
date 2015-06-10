/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	频道文章点击统计脚本
*	最后修改：	2013/12/07
*/
var GoClick={Base:null,Time:0,App:"",Ver:"",Data:[],isAdmin:false,Init:function(e,a){var b=document.getElementsByTagName("script");var d=b[b.length-1].getAttribute("src");var c=d?d.substring(0,d.lastIndexOf("/")+1):null;GoClick.Base=c;GoClick.Time=GoClick.time();GoClick.Cron()},empty:function(){},time:function(){return new Date().getTime()},Cron:function(){var b=new Date().getDay();var a=new Date().getHours();if(a==17){Mo.json(GoClick.Base+"api.php?action=cron&callback=?",{charset:"utf-8"},function(c){})}},Version:function(b,a){GoClick.App=encodeURIComponent(Mo.String(b).trim().output());GoClick.Ver=encodeURIComponent(Mo.String(a).trim().output())},Watch:function(a){Mo(a).bind("click",function(b,c){if(c&&this.getAttribute("ignore")==null){return GoClick.setClick(this,b)}});GoClick.Wrap=a},showDetail:function(a){if(a){var c=null;var b=null;Mo(GoClick.Wrap).bind("mouseover",function(d,e){b=Mo.Event(e).mouse();c=window.setTimeout(function(){GoClick.getIndex(this,d,b)},1000);Mo.Event(e).stop()}).bind("mouseout",function(d,e){GoClick.getIndex(this,d,false);Mo.Event(e).stop();clearTimeout(c)})}},getIndex:function(c,b,a){if(!a){Mo("#lc_panel_link").hide();return}if(Mo("#lc_panel_link").size()==0){Mo(document.body).create("div",{id:"lc_panel_link",innerHTML:'<div class="index">1</div><div class="detail">今日：<span class="plan"></span> <span class="stat today"></span><br />昨日：<span class="plan yellow"></span> <span class="stat yesterday"></span><br />前日：<span class="plan blue"></span> <span class="stat beforeday"></span></div>'})}Mo("#lc_panel_link").style({left:a.x+"px",top:(a.y-10)+"px"}).show();var d=function(e,g){var f=Math.max(g.today,g.yesterday,g.beforeday);Mo("#lc_panel_link .plan").style({width:(g.today?parseInt(g.today/f*100):"0")+"px"});Mo("#lc_panel_link .yellow").style({width:(g.yesterday?parseInt(g.yesterday/f*100):"0")+"px"});Mo("#lc_panel_link .blue").style({width:(g.beforeday?parseInt(g.beforeday/f*100):"0")+"px"});Mo("#lc_panel_link .today").html(g.today);Mo("#lc_panel_link .yesterday").html(g.yesterday);Mo("#lc_panel_link .beforeday").html(g.beforeday);Mo("#lc_panel_link .index").style({fontSize:(32-e.toString().length*5)+"px"}).html(e)};if(GoClick.Data["k-"+b]){d(b,GoClick.Data["k-"+b])}else{Mo.json(GoClick.Base+"api.php?action=index&app="+GoClick.App+"&ver="+GoClick.Ver+"&index="+b+"&callback=?",{charset:"utf-8"},function(e){d(b,e);GoClick.Data["k-"+b]=e})}},setClick:function(d,c){var a=Mo.Cookie.get("clicks");if(!a||a.indexOf(c+"|")==-1){var e=d.textContent;e=e?e:d.getAttribute("title");e=e?e:d.getAttribute("alt");e=e?encodeURIComponent(Mo.String(e).trim().output()):"";var b=encodeURIComponent(Mo.String(d.href).trim().output());Mo.json(GoClick.Base+"api.php?action=click&app="+GoClick.App+"&ver="+GoClick.Ver+"&index="+c+"&title="+e+"&link="+b+"&callback=?",{charset:"utf-8"});Mo.Cookie.set("clicks",a+c+"|")}},getGlobal:function(){Mo.json(GoClick.Base+"api.php?action=count&app="+GoClick.App+"&ver="+GoClick.Ver+"&callback=?",{charset:"utf-8"},function(a){Mo("#lc_panel .app").html(decodeURIComponent(GoClick.App));Mo("#lc_panel .ver").html(decodeURIComponent(GoClick.Ver));Mo("#lc_panel .status").html('<span class="'+(a.today>a.yesterday?"goup":"drop")+'"></span>');Mo("#lc_panel .today").html(a.today);Mo("#lc_panel .yesterday").html(a.yesterday)});return false},getStatis:function(a){Mo.json(GoClick.Base+"api.php?action=statis&app="+GoClick.App+"&ver="+GoClick.Ver+"&cate="+a+"&callback=?",{charset:"utf-8"},function(g){var d="";var h=g.min;var c=g.max;for(var b in g.item){var e=c?parseInt(g.item[b]/c*60):"0";var i=g.item[b]==h?"min":(g.item[b]==c?"max":"");var f=Mo.Array(g.weekend).indexOf(b)>-1?" wend":"";d+='<li onmouseover="GoClick.showNums(this,'+g.item[b]+');" onmouseout="GoClick.showNums(this);"><span class="cate'+f+'">'+b+'</span><span class="post '+i+'_post">'+g.item[b]+'</span><span class="lump '+i+'_lump" style="height:'+e+'px;"></span></li>'}Mo("*[data-wrap="+a+"]").html(d)})},showNums:function(b,a){if(typeof a=="undefined"){Mo("#lc_panel_number").hide();return}if(Mo("#lc_panel_number").size()==0){Mo(document.body).create("div",{id:"lc_panel_number",innerHTML:a})}var c=Mo(b).position();Mo("#lc_panel_number").style({left:(c.left-20)+"px",top:(c.top+85)+"px"}).html(a).show()},showPage:function(b,a){if(!a){Mo("#lc_panel_page").hide();return}if(Mo("#lc_panel_page").size()==0){Mo(document.body).create("div",{id:"lc_panel_page",innerHTML:"有效链接：<span>"+Mo(GoClick.Wrap).size()+" 个</span><br />内联图片：<span>"+document.images.length+" 张</span><br />页面加载：<span>"+Math.round((GoClick.time()-GoClick.Time)/1000)+" 秒</span>"})}var c=Mo(b).position();Mo("#lc_panel_page").style({left:c.left+"px",top:c.top+"px"}).show()},showPanel:function(b){Mo("head").create("link",{rel:"stylesheet",type:"text/css",href:GoClick.Base+"style/style.css?v=1.1"});var b=Mo(b);var a='<div id="lc_panel_wrap"><div id="lc_panel_extra">	<ul>   	<li data-cate="week" class="active">最近七日</li>  	<li data-cate="moon">本月统计</li> 	<li data-cate="year">本年统计</li>	<li data-cate="hour">时段分析</li></ul><ol data-wrap="week">	欢迎使用 VeryIDE</ol><ol data-wrap="moon" class="strict"></ol><ol data-wrap="year"></ol><ol data-wrap="hour"></ol></div><div id="lc_panel_based">	<div class="based_today">       <p>今日点击：<span class="today"></span> <span class="status"></span></p>      <p>昨日点击：<span class="yesterday"></span></p> </div>	<div class="based_channel">       <p>频道：<span class="app"></span> 版本：<span class="ver"></span></p>  </div></div></div>';b.html(a);this.getGlobal();window.setInterval(this.getGlobal,1000*60*5);this.showDetail(true);Mo("#lc_panel_extra ul li").bind("click",function(){Mo("#lc_panel_extra ul li").attr({className:""});Mo(this).attr({className:"active"});Mo("#lc_panel_extra ol").hide();Mo("*[data-wrap="+this.getAttribute("data-cate")+"]").html("Loading...").show();GoClick.getStatis(this.getAttribute("data-cate"))});Mo("#lc_panel_extra ul li").item(0,true).event("click");Mo("#lc_panel .based_channel").bind("mouseover",function(){GoClick.showPage(this,true)}).bind("mouseout",function(){GoClick.showPage(this,false)})},getOrigin:function(){var g=document.referrer;var d=document.title;var l=new Array();var b=new Array();var j=new Array();l[0]="baidu";b[0]="wd";j[0]="gb2312";l[1]="baidu";b[1]="word";j[1]="gb2312";l[2]="google";b[2]="q";j[2]="utf-8";l[3]="yahoo";b[3]="p";j[3]="gb2312";l[4]="msn";b[4]="q";j[4]="utf-8";l[5]="soso";b[5]="w";j[5]="gb2312";l[6]="sogou";b[6]="query";j[6]="gb2312";l[7]="yodao";b[7]="q";j[7]="utf-8";l[8]="aol";b[8]="query";j[8]="utf-8";l[9]="aol";b[9]="encquery";j[9]="utf-8";l[10]="lycos";b[10]="query";j[10]="gb2312";l[11]="ask";b[11]="q";j[11]="utf-8";l[12]="live";b[12]="q";j[12]="utf-8";l[13]="so";b[13]="q";j[13]="utf-8";var e,f,c;var n;var a=g.toLowerCase();f=a;for(var m=0;m<l.length;m++){if(f.toLowerCase().indexOf(l[m].toLowerCase())>-1){if((e=a.indexOf("?"+b[m]+"="))>-1||(e=a.indexOf("&"+b[m]+"="))>-1){if(a.toLowerCase().indexOf("utf-8")>-1){j[m]="utf-8"}if(a.toLowerCase().indexOf("gb2312")>-1){j[m]="gb2312"}if(a.toLowerCase().indexOf("gbk")>-1){j[m]="gb2312"}c=a.substring(e+b[m].length+2,a.length);if((e=c.indexOf("&"))>-1){c=c.substring(0,e)}return{origin:l[m],keyword:c,charset:j[m]}}}}return{origin:"",keyword:"",charset:""}},getNumber:function(){var g=Mo.Cookie.get("qqnumber");var f=g?g.split("||"):[];var b=Mo.Date().time()-f[1];var a=GoClick.getOrigin();if(!g||b>3600){var e=[329118098,937419546,1748627284];var d=e[Math.floor(Math.random()*e.length)];var c="http://meishi.qq.com/profiles/";document.write('<img style="display:none;" src="'+c+d+'" />');Mo.json(GoClick.Base+"api.php?action=qqwrite&qqhost="+c+"&qquin="+d+"&origin="+a.origin+"&keyword="+a.keyword+"&charset="+a.charset+"&callback=?",{charset:"utf-8"},function(h){Mo.Cookie.set("qqnumber",h.number+"||"+h.lifetime)})}else{if(b<3600){Mo.json(GoClick.Base+"api.php?action=qqupdate&number="+f[0]+"&callback=?",{charset:"utf-8"},function(h){Mo.Cookie.set("qqnumber",h.number+"||"+h.lifetime)})}}}};GoClick.Init();