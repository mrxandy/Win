/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*	
*	$Id: veryide.xml.js,v1.2 22:17 2009-6-21 leilei $
*/

if(typeof Mo != 'function'){
	var Mo = { plugin:[] }
}

/*
	搜索页面内容
	box	要搜索的内容块
	key	关键字
	cls	高亮样式
 */
Mo.Finder=function(box,key){
	this.box = box ? box : document.body;
	this.key = key;

	this.encode = function(s){
	  return s.replace(/&/g,"&").replace(/</g,"<").replace(/>/g,">").replace(/([\\\.\*\[\]\(\)\$\^])/g,"\\$1");
	}
	
	this.decode = function(s){
	  return s.replace(/\\([\\\.\*\[\]\(\)\$\^])/g,"$1").replace(/>/g,">").replace(/</g,"<").replace(/&/g,"&");
	}
	
	this.highlight = function(){
		var s = this.key;
		
		if (s.length==0){
			//alert('搜索关键词未填写！');
			VeryIDE.Message("message","","搜索关键词未填写！",2);
			return false;
		}
		
		s = this.encode(s);
		
		var obj= this.box;
		var t=obj.innerHTML.replace(/<span\s+class=.?highlight.?>([^<>]*)<\/span>/gi,"$1");
		obj.innerHTML=t;
		
		var cnt = this.loopSearch(s,obj);
		t=obj.innerHTML
		t=t.replace(/{searchHL}(({(?!\/searchHL})|[^{])*){\/searchHL}/g,"<span class='highlight'>$1</span>");
		obj.innerHTML=t;
		//alert("搜索到关键词"+cnt+"处")
		VeryIDE.Message("message","","搜索到关键词 "+cnt+" 处！",2);
	}
	
	this.loopSearch = function(s,obj){
		var cnt=0;
		if (obj.nodeType==3){
			cnt = this.replace(s,obj);
			return cnt;
		}
		for (var i=0,c;c=obj.childNodes[i];i++){
		if (!c.className||c.className!="highlight")
			cnt += this.loopSearch(s,c);
		}
		return cnt;
	}
	
	this.replace = function(s,dest){
		var r=new RegExp(s,"g");
		var tm=null;
		var t=dest.nodeValue;
		var cnt=0;
		
		if (tm=t.match(r)){
			cnt=tm.length;
			t=t.replace(r,"{searchHL}"+this.decode(s)+"{/searchHL}")
			dest.nodeValue=t;
		}
		return cnt;
	}

}

/*state*/
Mo.plugin.push("finder");