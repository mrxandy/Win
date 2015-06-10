/*
*	(C)2009-2013 VeryIDE
*	Mo.js
*	author:
*			Wiki[MO]	gwikimo@gmail.com	
*			Lay 		veryide@qq.com
*
*	#核心文件，包括选择器、内置函数扩展、浏览器识别等#
*/
var Mo=function(a,b){return new Mo.init(a,b)};Mo.version="1.2";Mo.build=20131016;Mo.store=new Object();Mo.plugin=[];Mo.time=new Date().getTime();Mo.start=function(){var a=document.getElementsByTagName("script");var f=a[a.length-1].getAttribute("src",2);var d=f?f.substring(0,f.lastIndexOf("/")+1):null;Mo.base=d;Mo.Browser={msie:false,opera:false,safari:false,chrome:false,firefox:false};var g=navigator;var c=g.userAgent;var b="";switch(g.appName){case"Microsoft Internet Explorer":Mo.Browser.name="ie";Mo.Browser.msie=true;b=/^.+MSIE (\d+\.\d+);.+$/;break;default:if(c.indexOf("Chrome")!=-1){Mo.Browser.name="chrome";Mo.Browser.chrome=true;b=/^.+Chrome\/([\d.]+?)([\s].*)$/ig}else{if(c.indexOf("Safari")!=-1){Mo.Browser.name="safari";Mo.Browser.safari=true;b=/^.+Version\/([\d\.]+?) (Mobile.)?Safari.+$/}else{if(c.indexOf("Opera")!=-1){Mo.Browser.name="opera";Mo.Browser.opera=true;b=/^.{0,}Opera\/(.+?) \(.+$/}else{Mo.Browser.name="firefox";Mo.Browser.firefox=true;b=/^.+Firefox\/([\d\.]+).{0,}$/}}}break}Mo.Browser.version=c.replace(b,"$1");Mo.Browser.lang=(!Mo.Browser.msie?g.language:g.browserLanguage).toLowerCase();Mo.Browser.mobile=/(iPhone|iPad|iPod|Android)/i.test(c);Mo.document=(document.compatMode&&document.compatMode!="BackCompat"&&!c.toLowerCase().indexOf("webkit")>-1)?document.documentElement:document.body;if(typeof HTMLElement!=="undefined"&&!("outerHTML" in HTMLElement.prototype)){HTMLElement.prototype.__defineGetter__("outerHTML",function(){var h=this.attributes,k="<"+this.tagName,j=0;for(;j<h.length;j++){if(h[j].specified){k+=" "+h[j].name+'="'+h[j].value+'"'}}if(!this.canHaveChildren){return k+" />"}return k+">"+this.innerHTML+"</"+this.tagName+">"});HTMLElement.prototype.__defineSetter__("outerHTML",function(h){var i=this.ownerDocument.createRange();i.setStartBefore(this);var j=i.createContextualFragment(h);this.parentNode.replaceChild(j,this);return h});HTMLElement.prototype.__defineGetter__("canHaveChildren",function(){return !/^(area|base|basefont|col|frame|hr|img|br|input|isindex|link|meta|param)$/.test(this.tagName.toLowerCase())})}if(window.Node){Node.prototype.replaceNode=function(h){this.parentNode.replaceChild(h,this)};Node.prototype.removeNode=function(i){if(i){return this.parentNode.removeChild(this)}else{var h=document.createRange();h.selectNodeContents(this);return this.parentNode.replaceChild(h.extractContents(),this)}};Node.prototype.swapNode=function(h){var k=this.parentNode;var i=this.nextSibling;var j=h.parentNode.replaceChild(this,h);if(j==i){k.insertBefore(i,this)}else{if(i){k.insertBefore(j,i)}else{k.appendChild(j)}}return this}}};Mo.start();Mo.$=function(a){return document.getElementById(a)};Mo.ready=function(b){if(Mo.Browser.msie){var a=Mo.random(10);if(!Mo.store.dri){Mo.store.dri=[]}Mo.store.dri[a]=setInterval(function(){try{document.documentElement.doScroll("left");clearInterval(Mo.store.dri[a]);Mo.store.dri[a]=null;b(new Date().getTime()-Mo.time)}catch(c){}},1)}else{document.addEventListener("DOMContentLoaded",function(){b(new Date().getTime()-Mo.time)},false)}};Mo.reader=function(a){Mo(window).bind("load",function(){a(new Date().getTime()-Mo.time)})};Mo.resize=function(a){Mo(window).bind("resize",a)};Mo.date=function(n,g){var k=n;var a=g?(Mo.Validate.Number(g)?new Date(parseInt(g)*1000):g):new Date();var l=a.getFullYear(),b=a.getMonth()+1,j=a.getDate(),f=a.getHours(),c=a.getMinutes(),o=a.getSeconds();k=k.replace("yy",l.toString().substr(l.toString().length-2));k=k.replace("y",l);k=k.replace("mm",("0"+b).substr(b.toString().length-1));k=k.replace("m",b);k=k.replace("dd",("0"+j).substr(j.toString().length-1));k=k.replace("d",j);k=k.replace("hh",("0"+f).substr(f.toString().length-1));k=k.replace("h",f);k=k.replace("ii",("0"+c).substr(c.toString().length-1));k=k.replace("i",c);k=k.replace("ss",("0"+o).substr(o.toString().length-1));k=k.replace("s",o);return k};Mo.random=function(l,j,g,k){if(!j&&!g&&!k){j=g=k=true}var f=[["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"],["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"],["0","1","2","3","4","5","6","7","8","9"]];var d=[];var m="";d=j?d.concat(f[0]):d;d=g?d.concat(f[1]):d;d=k?d.concat(f[2]):d;for(var h=0;h<l;h++){m+=d[Math.round(Math.random()*(d.length-1))]}return m};Mo.between=function(b,a){return Math.round(b+(Math.random()*(a-b)))};Mo.write=function(){for(var a=0;a<arguments.length;a++){document.write(arguments[a])}};Mo.url=function(c){var b=document.createElement("a");b.href=c;return{source:c,protocol:b.protocol.replace(":",""),host:b.hostname,port:b.port,query:b.search,params:(function(){var f={},d=b.search.replace(/^\?/,"").split("&"),a=d.length,g=0,h;for(;g<a;g++){if(!d[g]){continue}h=d[g].split("=");f[h[0]]=h[1]}return f})(),file:(b.pathname.match(/\/([^\/?#]+)$/i)||[,""])[1],hash:b.hash.replace("#",""),path:b.pathname.replace(/^([^\/])/,"/$1"),relative:(b.href.match(/tps?:\/\/[^\/]+(.+)/)||[,""])[1],segments:b.pathname.replace(/^\//,"").split("/")}};Mo.Cookie={get:function(b){var a=document.cookie.match((new RegExp(b+"=[a-zA-Z0-9.()=|%/]+($|;)","g")));if(!a||!a[0]){return null}else{return unescape(a[0].substring(b.length+1,a[0].length).replace(";",""))||null}},set:function(c,f,a,h,d,g){var b=[c+"="+escape(f),"path="+((!h||h=="")?"/":h),"domain="+((!d||d=="")?window.location.hostname:d)];if(a){b.push(Mo.Cookie.hoursToExpireDate(a))}if(g){b.push("secure")}return document.cookie=b.join("; ")},unset:function(a,c,b){c=(!c||typeof c!="string")?"":c;b=(!b||typeof b!="string")?"":b;if(Mo.Cookie.get(a)){Mo.Cookie.set(a,"","Thu, 01-Jan-70 00:00:01 GMT",c,b)}},hoursToExpireDate:function(a){if(parseInt(a)=="NaN"){return""}else{var b=new Date();b.setTime(b.getTime()+(parseInt(a)*60*60*1000));return b.toGMTString()}},dump:function(){if(typeof console!="undefined"){console.log(document.cookie.split(";"))}},clear:function(){var b=document.cookie.match(/[^ =;]+(?=\=)/g);if(b){for(var a=b.length;a--;){Mo.Cookie.unset(b[a])}}}};Mo.get=function(c,b){var b=b?b:location.href;var a="";var d=b.indexOf(c+"=");if(d!=-1){d+=c.length+1;e=b.indexOf("&",d);if(e==-1){e=b.length}a=b.substring(d,e)}return a};Mo.script=function(f,a,c,d){if(typeof c!="function"){var c=function(){}}if(f.indexOf(".")==-1){if(Mo.Array(Mo.plugin).indexOf(f)>-1){c&&c();return}else{f=Mo.base+"mo."+f+".js"}}var d=d?d:document.getElementsByTagName("head")[0];a=a?a:{};a.type="text/javascript";a.src=f;var b=Mo.create("script",a);d.appendChild(b);b.onreadystatechange=b.onload=function(){if((!this.readyState||this.readyState=="loaded"||this.readyState=="complete")&&!this.executed){this.executed=true;c&&c(this)}}};Mo.json=function(g,a,f){if(typeof f!="function"){var f=function(){}}var h="cross"+parseInt(Math.random()*1000);var d=document.getElementsByTagName("head")[0];g=g.substr(0,g.length-1)+h;a=a?a:{};a.type="text/javascript";a.src=g;var b=Mo(d).create("script",a,true);window[h]=window[h]||function(c){f(c);window[h]=undefined;try{delete window[h]}catch(i){}b.remove()}};Mo.Template=function(c,b){Mo.Template.cache=Mo.Template.cache||{};var a=function(g,f){var d=!/\W/.test(g)?Mo.Template.cache[g]=Mo.Template.cache[g]||a(document.getElementById(g).innerHTML):new Function("obj","var p=[],print=function(){p.push.apply(p,arguments);};with(obj){p.push('"+g.replace(/[\r\t\n]/g," ").split("<%").join("\t").replace(/((^|%>)[^\t]*)'/g,"$1\r").replace(/\t=(.*?)%>/g,"',$1,'").split("\t").join("');").split("%>").join("p.push('").split("\r").join("\\'")+"');}return p.join('');");return f?d(f):d};return a(c,b)};Mo.Toolkit={slice:([]).slice,is:function(a){return({}).toString.call(a).slice(8,-1)},isOnDom:function(b){if(!b||!b.nodeType||b.nodeType!==1){return}var a=document.body;while(b.parentNode){if(b===a){return true}b=b.parentNode}return false},contains:function(d,c){try{return d.contains?d!=c&&d.contains(c):!!(d.compareDocumentPosition(c))}catch(f){}},getViewportSize:function(){var a={width:0,height:0};undefined!==window.innerWidth?a={width:window.innerWidth,height:window.innerHeight}:a={width:document.documentElement.clientWidth,height:document.documentElement.clientHeight};return a},getClinetRect:function(a){var c=a.getBoundingClientRect(),b=(b={left:c.left,right:c.right,top:c.top,bottom:c.bottom,height:(c.height||(c.bottom-c.top)),width:(c.width||(c.right-c.left))});return b},getScrollPosition:function(){var a={left:0,top:0};if(window.pageYOffset){a={left:window.pageXOffset,top:window.pageYOffset}}else{if(typeof document.documentElement.scrollTop!="undefined"&&document.documentElement.scrollTop>0){a={left:document.documentElement.scrollLeft,top:document.documentElement.scrollTop}}else{if(typeof document.body.scrollTop!="undefined"){a={left:document.body.scrollLeft,top:document.body.scrollTop}}}}return a},getUrlBasic:function(a){var b=document.getElementsByTagName("script");var d=b[b.length-1].getAttribute("src");var c=d?d.substring(0,d.lastIndexOf("/")-(a?a.length:0)):null;return c}};Mo.Array=function(a){var b=function(c){this.self=c;return this};b.prototype={output:function(){return this.self},first:function(){return this.self[0]},last:function(){return this.self[this.self.length-1]},max:function(){return Math.max.apply(null,this.self)},min:function(){return Math.min.apply(null,this.self)},sum:function(){for(var c=0,d=0;c<this.self.length;d+=isNaN(parseInt(this.self[c]))?0:parseInt(this.self[c]),c++){}return d},clear:function(){this.self=[];return this},indexOf:function(f){var c=this.self.length;for(var d=0;d<=c;d++){if(this.self[d]==f){return d}}return -1}};return new b(a)};Mo.String=function(source){var inti=function(source){this.self=String(source||"");return this};inti.prototype={output:function(){return this.self},pad:function(l,s,t){var str=this.self.toString();return s||(s=" "),(l-=str.length)>0?(s=new Array(Math.ceil(l/s.length)+1).join(s)).substr(0,t=!t?l:t==1?0:Math.ceil(l/2))+str+s.substr(0,l-t):str},length:function(){return String(this.self).replace(/[^\x00-\xff]/g,"ci").length},trim:function(){this.self=this.self.replace(/(^\s*)|(\s*$)/g,"");return this},leftTrim:function(){this.self=this.self.replace(/(^\s*)/g,"");return this},rightTrim:function(){this.self=this.self.replace(/(\s*$)/g,"");return this},stripScript:function(){this.self=this.self.replace(/<script.*?>.*?<\/script>/ig,"");return this},stripTags:function(allowed){allowed=(((allowed||"")+"").toLowerCase().match(/<[a-z][a-z0-9]*>/g)||[]).join("");var tags=/<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,commentsAndPhpTags=/<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;this.self=this.self.replace(commentsAndPhpTags,"").replace(tags,function($0,$1){return allowed.indexOf("<"+$1.toLowerCase()+">")>-1?$0:""});return this},stripTags:function(){this.self=String(this.self).replace(/<[^>]+>/g,"");return this},unicode:function(){if(this.self){var result="";for(var i=0;i<this.self.length;i++){result+="&#"+this.self.charCodeAt(i)+";"}this.self=result}return this},ascii:function(){if(this.self){var code=this.self.match(/&#(\d+);/g);if(code!=null){var result="";for(var i=0;i<code.length;i++){result+=String.fromCharCode(code[i].replace(/[&#;]/g,""))}this.self=result}}return this},format:function(){var param=[];for(var i=0,l=arguments.length;i<l;i++){param.push(arguments[i])}this.self=this.self.replace(/\{(\d+)\}/g,function(m,n){return param[n]});return this},encodeHTML:function(){return this.self.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#39;")},decodeHTML:function(){var b=this.self.replace(/&quot;/g,'"').replace(/&lt;/g,"<").replace(/&gt;/g,">").replace(/&amp;/g,"&");return b.replace(/&#([\d]+);/g,function(d,c){return String.fromCharCode(parseInt(c,10))})},escapeSymbol:function(){return String(this.self).replace(/\%/g,"%25").replace(/&/g,"%26").replace(/\+/g,"%2B").replace(/\ /g,"%20").replace(/\//g,"%2F").replace(/\#/g,"%23").replace(/\=/g,"%3D")},toCamelCase:function(){var a=this.self;if(a.indexOf("-")<0&&a.indexOf("_")<0){return a}return a.replace(/[-_][^-_]/g,function(b){return b.charAt(1).toUpperCase()})},eval:function(){return(new Function("return ("+this.self.replace(/\r/gm,"").replace(/\n/gm,"\\n")+");"))()}};return new inti(source)};Mo.Number=function(a){var b=function(c){this.self=String(c||"");return this};b.prototype={test:function(){alert("");return},comma:function(f){var g=this.self;if(!f||f<1){f=3}g=String(g).split(".");g[0]=g[0].replace(new RegExp("(\\d)(?=(\\d{"+f+"})+$)","ig"),"$1,");return g.join(".")},isNumber:function(c){return/^[0-9]{1,20}$/.exec(c)}};return new b(a)};Mo.Function=function(a){var b=function(c){this.self=c||new Function();return this};b.prototype={execute:function(c){if(c){window.setTimeout(this.self,c)}else{this.self()}}};return new b(a)};Mo.Date=function(a){var b=function(c){this.self=(Mo.Validate.Number(c)?new Date(parseInt(c)*1000):c)||new Date();return this};b.prototype={leapyear:function(){var c=this.self.getFullYear();return(0==c%4&&((c%100!=0)||(c%400==0)))},days:function(){return(new Date(this.self.getFullYear(),this.self.getMonth()+1,0)).getDate()},time:function(){return Math.round(this.self.getTime()/1000)}};return new b(a)};Mo.Event=function(a){var b=function(c){this.self=c||window.event;return this};b.prototype={stop:function(c){if(!this.self){return}if(Mo.Browser.msie){c!==2&&(window.event.cancelBubble=true);c!==1&&(window.event.returnValue=false)}else{c!==2&&this.self.stopPropagation();c!==1&&this.self.preventDefault()}return this},element:function(){if(!this.self){return}if(Mo.Browser.msie){return window.event.srcElement}else{return this.self.currentTarget}},target:function(){if(!this.self){return}if(Mo.Browser.msie){return window.event.srcElement}else{return this.self.target}},mouse:function(){if(!this.self){return}if(Mo.Browser.msie){var c=this.self.x+Mo.document.scrollLeft;var d=this.self.y+Mo.document.scrollTop}else{var c=this.self.pageX;var d=this.self.pageY}return{x:c,y:d}},keyboard:function(d,c){if(!this.self){return}if((d>-1&&this.self.keyCode==d)||d==-1){c(this.self,this.self.keyCode)}}};return new b(a)};Mo.Validate={Array:function(a){return Object.prototype.toString.apply(a)==="[object Array]"},Function:function(a){return Object.prototype.toString.apply(a)==="[object Function]"},Object:function(a){return Object.prototype.toString.apply(a)==="[object Object]"},Date:function(a){if(typeof a=="string"){return a.match(/^(\d{4})(\-)(\d{1,2})(\-)(\d{1,2})(\s{1})(\d{1,2})(\:)(\d{1,2})/)!=null||a.match(/^(\d{4})(\-)(\d{1,2})(\-)(\d{1,2})/)!=null}else{return Object.prototype.toString.apply(a)==="[object Date]"}},Number:function(a){return !isNaN(parseFloat(a))&&isFinite(a)},String:function(a){return typeof a==="string"},Defined:function(a){return typeof a!="undefined"},Empty:function(a){return typeof a=="undefined"||a==""},Boolean:function(a){return typeof a==="boolean"},Window:function(a){return/\[object Window\]/.test(a)},Document:function(a){return/\[object HTMLDocument\]/.test(a)},Element:function(a){return a.tagName?true:false},Chinese:function(b,a){if(a){return(b.length*2==b.replace(/[^\x00-\xff]/g,"**").length)}else{return(b.length!=b.replace(/[^\x00-\xff]/g,"**").length)}},Safe:function(c){var b;var a;b="'*%@#^$`~!^&*()=+{}\\|{}[];:/?<>,.";for(a=0;a<c.length;a++){if(b.indexOf(c.charAt(a))!=-1){return false}}return true},Email:function(a){return/^\s*([A-Za-z0-9_-]+(\.\w+)*@(\w+\.)+\w{2,3})\s*$/.test(a)},URL:function(a){return/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\:+!]*([^<>])*$/.test(a)},IP:function(a){return/^[0-9.]{1,20}$/.test(a)},Password:function(a){return/^(\w){6,20}$/.test(a)},Color:function(a){return/^#(\w){6}$/.test(a)},ID:function(a){if(a.length==18){return Mo.Validate.Number(a.substring(0,17))}else{return false}},Phone:function(a){return/(?:^0{0,1}1\d{10}$)|(?:^[+](\d){1,3}1\d{10}$)|(?:^0[1-9]{1,2}\d{1}\-{0,1}[2-9]\d{6,7}$)|(?:^\d{7,8}$)|(?:^0[1-9]{1,2}\d{1}\-{0,1}[2-9]\d{6,7}[\-#]{1}\d{1,5}$)/.test(a)},Mobile:function(a){return/^[1][0-9]{10}$/.test(a)}};Mo.find=(function(){var c=/(?:[\*\w\-\\.#]+)+(?:\[(?:[\w\-_][^=]+)=(?:[\'\[\]\w\-_]+)\])*|\*|>/ig,i=/^(?:[\w\-_]+)?\.([\w\-_]+)/,g=/^(?:[\w\-_]+)?#([\w\-_]+)/,m=/^([\w\*\-_]+)/,k=[null,null,null];var j=/\[([\w\-_][^=]+)=([\'\[\]\w\-_]+)\]/;var h=/\[([\w\-_][^=]+)=(([\w\-_]+)\{([\w\-_]+)\})\]/;function d(C,p){p=p||document;var B=/^[\w\-_#]+$/.test(C);if(!B&&p.querySelectorAll){return b(p.querySelectorAll(C))}if(C.indexOf(",")>-1){var w=C.split(/,/g),F=[],A=0,D=w.length;for(;A<D;++A){F=F.concat(d(w[A],p))}return f(F)}var y=C.match(c),z=y.pop(),u=(z.match(g)||k)[1],o=!u&&(z.match(i)||k)[1],n=!u&&(z.match(m)||k)[1],t;var v=C.match(/\[(?:[\w\-_][^=]+)=(?:[\'\[\]\w\-_]+)\]/g);if(o&&!v&&!n&&p.getElementsByClassName){t=b(p.getElementsByClassName(o))}else{t=!u&&b(p.getElementsByTagName(n||"*"));if(o){t=l(t,"className",RegExp("(^|\\s)"+o+"(\\s|$)"))}if(u){var q=p.getElementById(u);return q?[q]:[]}if(v){for(var s=0;s<v.length;s++){var r=(v[s].match(j)||k)[1];var E=(v[s].match(j)||k)[2];E=E.replace(/\'/g,"").replace(/\-/g,"\\-").replace(/\[/g,"\\[").replace(/\]/g,"\\]");t=l(t,r,RegExp("(^"+E+"$)"))}}}return y[0]&&t[0]?a(y,t):t}function b(r){try{return Array.prototype.slice.call(r)}catch(q){var o=[],p=0,n=r.length;for(;p<n;++p){o[p]=r[p]}return o}}function a(z,t,q){var u=z.pop();if(u===">"){return a(z,t,true)}var v=[],n=-1,o=(u.match(g)||k)[1],w=!o&&(u.match(i)||k)[1],y=!o&&(u.match(m)||k)[1],x=-1,p,A,s;y=y&&y.toLowerCase();while((p=t[++x])){A=p.parentNode;do{s=!y||y==="*"||y===A.nodeName.toLowerCase();s=s&&(!o||A.id===o);s=s&&(!w||RegExp("(^|\\s)"+w+"(\\s|$)").test(A.className));if(q||s){break}}while((A=A.parentNode));if(s){v[++n]=p}}return z[0]&&v[0]?a(z,v):v}var f=(function(){var n=+new Date();var o=(function(){var p=1;return function(s){var r=s[n],q=p++;if(!r){s[n]=q;return true}return false}})();return function(p){var v=p.length,q=[],u=-1,s=0,t;for(;s<v;++s){t=p[s];if(o(t)){q[++u]=t}}n+=1;return q}})();function l(u,n,t){var p=-1,s,q=-1,o=[];while((s=u[++p])){if(t.test(s.getAttribute(n))){o[++q]=s}}return o}return d})();Mo.extend=function(b){for(var a in b){Mo.init.prototype[a]=b[a]}};Mo.create=function(b,a){var d=document.createElement(b);var a=a||{};for(var c in a){if(/[A-Z]/.test(c)){d[c]=a[c]}else{d.setAttribute(c,a[c])}}return d};Mo.init=function(a,b){this.self=typeof a=="string"?Mo.find(a,b):[a]};Mo.init.prototype={size:function(){return this.self.length},item:function(c,a){var b=this.size();var d=null;if(c>=0){d=c<=b?this.self[c]:null}else{d=Math.abs(c)<=b?this.self[(b+c)]:null}if(a){this.self=[d]}return a?this:d},hide:function(b,a){this.each(function(){this.style.display="none";typeof(a)=="function"&&a.call(this)});return this},show:function(b,a){this.each(function(){this.style.display="";typeof(a)=="function"&&a.call(this)});return this},toggle:function(b,a){this.each(function(){if(this.style.display=="none"||this.offsetHeight==0||this.offsetWidth==0){this.style.display=""}else{this.style.display="none"}typeof(a)=="function"&&a.call(this,this.style.display!="none")});return this},value:function(c,a){if(typeof c!="undefined"){this.each(function(){var d=this.length;switch(this.type){case"select-one":for(var f=0;f<d;f++){if(this[f].value==c){this.selectedIndex=f;break}}break;case"select-multiple":for(var f=0;f<d;f++){if(Mo.Array(c).indexOf(this[f].value)!==-1){this[f].selected=true}else{this[f].selected=false}}break;case"radio":case"checkbox":if((Mo.Validate.Array(c)&&Mo.Array(c).indexOf(this.value)!==-1)||this.value==c){this.checked=true}else{this.checked=false}break;case"text":case"hidden":case"textarea":case"password":if(a){this.value+=c}else{this.value=c}break}});return this}var b=[];this.each(function(){var d=this.length;switch(this.type){case"select-one":b.push(this.selectedIndex>-1?this[this.selectedIndex].value:null);break;case"select-multiple":for(var f=0;f<d;f++){this[f].selected&&b.push(this[f].value)}break;case"radio":case"checkbox":this.checked&&b.push(this.value);break;case"text":case"hidden":case"textarea":case"password":b.push(this.value);break}});return this.size()==1?b[0]:b},text:function(c,a){if(typeof c!="undefined"){this.each(function(){var d=this.length;switch(this.type){case"select-one":for(var f=0;f<d;f++){if(this[f].text==c){this.selectedIndex=f;if(typeof a!="undefined"){this[f].text=a}break}}break;case"select-multiple":for(var f=0;f<d;f++){if(Mo.Array(c).indexOf(this[f].text)!==-1){this[f].selected=true;if(typeof a!="undefined"){this[f].text=a}}else{this[f].selected=false}}break}});return this}var b=[];this.each(function(){var d=this.length;switch(this.type){case"select-one":if(d){b=this[this.selectedIndex].text}break;case"select-multiple":for(var f=0;f<d;f++){if(this[f].selected){b.push(this[f].text)}}break}});return b},html:function(a,c){if(typeof a!="undefined"){this.each(function(){if(c){this.innerHTML+=a}else{this.innerHTML=a}});return this}var b=this.self[0];return b.innerHTML},attr:function(a){if(typeof a=="string"){if(this.size()==0){return null}var b=this.self[0];if(/[A-Z]/.test(a)){return b[a]}else{return b.getAttribute(a)}}else{this.each(function(){for(var c in a){if(/[A-Z]/.test(c)){this[c]=a[c]}else{this.setAttribute(c,a[c])}}});return this}},style:function(a){if(typeof a=="string"){if(this.size()==0){return null}var b=this.self[0];var c=function(){var d=document.defaultView;return new Function("el","style",["style.indexOf('-')>-1 && (style=style.replace(/-(\\w)/g,function(m,a){return a.toUpperCase()}));","style=='float' && (style='",d?"cssFloat":"styleFloat","');return el.style[style] || ",d?"window.getComputedStyle(el, null)[style]":"el.currentStyle[style]"," || null;"].join(""))}();return c(b,a)}else{this.each(function(){for(var d in a){this.style[d]=a[d]}});return this}},position:function(b){if(this.size()==0){return null}var d=this.self[0];var c=d.offsetWidth;var a=d.offsetHeight;var g=d.offsetTop;var f=d.offsetLeft;while(d=d.offsetParent){g+=d.offsetTop;f+=d.offsetLeft}return{width:c,height:a,top:g,left:f}},each:function(c){var b=this.size();var d=this.self;for(var a=0;a<b;a++){if(c.call(d[a],a)===false){break}}return this},bind:function(a,b){this.each(function(d){var c=this;var f=function(g){return b.call(c,d,g)};!this.Listeners&&(this.Listeners=[]);this.Listeners.push({e:a,fn:f});if(this.addEventListener){this.addEventListener(a,f,false)}else{if(this.attachEvent){this.attachEvent("on"+a,f)}else{this["on"+a]=f}}});return this},unbind:function(a){this.each(function(b){if(this.Listeners){for(var c=0;c<this.Listeners.length;c++){if(this.removeEventListener){this.removeEventListener(this.Listeners[c].e,this.Listeners[c].fn,false)}else{if(this.detachEvent){this.detachEvent("on"+this.Listeners[c].e,this.Listeners[c].fn)}else{this["on"+a]=null}}}delete this.Listeners}});return this},event:function(a){this.each(function(){if(document.createEvent){var b=document.createEvent("MouseEvents");b.initEvent(a,true,true);this.dispatchEvent(b)}else{if(document.createEventObject){var b=document.createEventObject();this.fireEvent("on"+a,b)}else{this["on"+a]()}}});return this},focus:function(a,b){this.each(function(c){this.focus();if(typeof a=="function"){return a.call(this,c,b)}});return this},blur:function(a,b){this.each(function(c){this.blur();if(typeof a=="function"){return a.call(this,c,b)}});return this},submit:function(a,b){this.each(function(c){if((typeof a=="function"&&a.call(this,c,b))||typeof a=="undefined"){this.submit()}return false});return this},reset:function(a,b){this.each(function(c){if((typeof a=="function"&&a.call(this,c,b))||typeof a=="undefined"){this.reset()}return false});return this},disabled:function(){this.each(function(){this.disabled=true});return this},enabled:function(){this.each(function(){this.disabled=false});return this},checked:function(a){this.each(function(){if(Mo.Validate.Boolean(a)){this.checked=a}else{this.checked=(this.checked?false:true)}});return this},insert:function(b,a,c){var d=Mo.create(b,a);this.each(function(){this.parentNode.insertBefore(d,this)});if(c){this.self=[d]}return this},create:function(b,a,c){var d=Mo.create(b,a);this.append(d);if(c){this.self=[d]}return this},append:function(){for(var a=0;a<arguments.length;a++){var b=arguments[a];this.each(function(){this.appendChild(b)})}return this},remove:function(){this.each(function(){this.parentNode.removeChild(this)});return this},parent:function(c){var b=[];var a=Mo.Validate.Number(c)?parseInt(c):1;this.each(function(){var f=this;for(var d=0;d<a;d++){f=f.parentNode}b.push(f)});this.self=b;return this},prev:function(a){if(this.size()==0){return null}var b=this.self[0];var c=null;if(b.previousElementSibling){c=b.previousElementSibling}else{c=b.previousSibling}if(a){this.self=[c];return this}else{return c}},next:function(a){if(this.size()==0){return null}var b=this.self[0];var c=null;if(b.nextElementSibling){c=b.nextElementSibling}else{c=b.nextSibling}if(a){this.self=[c];return this}else{return c}},animation:function(i,f,j,b,k,g,h){if(!i){return}var c=10;g=(g&&g>100?g:100)/c;var d=[parseInt((f!=null?parseInt(f)-parseInt(i.offsetLeft):0)/g),parseInt((j!=null?parseInt(j)-parseInt(i.offsetTop):0)/g),parseInt((b!=null?parseInt(b)-parseInt(i.offsetWidth):0)/g),parseInt((k!=null?parseInt(k)-parseInt(i.offsetHeight):0)/g)];var l=0;var a=setInterval(function(){if(++l>g){clearInterval(a);if(h){h()}return}i.style.left=parseInt(i.offsetLeft)+parseInt(d[0])+"px";i.style.top=parseInt(i.offsetTop)+parseInt(d[1])+"px";i.style.width=parseInt(i.offsetWidth)+parseInt(d[2])+"px";i.style.height=parseInt(i.offsetHeight)+parseInt(d[3])+"px"},c);return this},motion:function(c,b){Mo.Logs.start("logs");for(var d in b){var f=this.position();if(!b[d].min&&!b[d].max){return this}if(!b[d].min){b[d].min=f[d]}if(!b[d].max){b[d].max=f[d]}if(b[d].min&&b[d].min!=f[d]){this.self.style[d]=b[d].min+"px"}var a=this;(function(){var g=d;var i=b;var h=a;var j=Mo.random(10,true);Mo.temp[j]=setInterval(function(){var l=h.position();var k=i[g].max&&l[g]+c>i[g].max?i[g].max:l[g]+c;h.self.style[g]=k+"px";if(k>=i[g].max){clearInterval(Mo.temp[j])}},10)})()}return this}};