// JavaScript Document

//加载百度地图API
//document.write("<scr"+"ipt type=\"text/javascript\" src=\"http://api.map.baidu.com/api?v=1.2\" charset=\"utf-8\"></scr"+"ipt>");

//百度地图类
var bmap = {
	
	//地图对象
	"map" : null,
	//坐标对象
	"point" : null,
	//浮动窗口对象
	"opts" : null,
	//缩放数值
	"zoom" : null,
	//单个标记
	"marker" : null,
	//标注
	"overLays" : [],
	//单个浮动窗
	"infoWindow" : null,
	
	//初始化地图
	"init" : function( json ) {
		
		//创建百度地图对象
		bmap.map = new BMap.Map(json.target);
		//缩放设置
		bmap.zoom = json.zoom;
		
	},
	
	//设置地图的坐标
	"setPoint" : function( lng , lat ) {
		
		bmap.point = new BMap.Point( lng , lat  );
		
	},
	
	//设置地图标记
	"setMarker" : function() {
		//创建标记对象
		bmap.marker = new BMap.Marker( bmap.point );
		
	},
	
	//设置浮动窗口
	"setOpts" : function( json ) {
		
		bmap.opts = { "width":json.width , "height":json.height , "title":json.title }
		bmap.infoWindow = new BMap.InfoWindow( json.name,bmap.opts );
		
	},
	
	//设置标注数组
	"setOverLays" : function( object ) {
		
		var json = {};
		
		var point = new BMap.Point( object.lng,object.lat );
		var marker = new BMap.Marker( point );
		var info = object.infoWindow;
				
		json.marker = marker;
		json.info = info;
		
		bmap.overLays.push( json );
		
	},
	
	//将标注数组打印到地图上
	printOverLays : function() {
		
		if( bmap.overLays.length == 0 ) {
			return false;
		}
		
		for( var i in bmap.overLays ) {
			
			bmap.map.addOverlay( bmap.overLays[i].marker );
			
			if( bmap.overLays[i].info != null ) {
				
				bmap.overLays[i].marker.addEventListener("click",function(){
					this.openInfoWindow(bmap.overLays[i].info);
				})
				
			}
			
		}
		
	},
	
	//设置居中和缩放程度
	"setCenterZoom" : function() {
		
		//如果地图标记不为空，则自动将其添加到地图中
		if( bmap.marker != null ) {
			bmap.map.addOverlay(bmap.marker);
		}
		
		//如果设置了单独的浮动窗，则添加到标记的事件中
		if( bmap.infoWindow != null && bmap.marker != null ) {
			bmap.marker.addEventListener("onmouseover", function(){          
				this.openInfoWindow(bmap.infoWindow);  
			});
		}
		
		//剧中并缩放
		bmap.map.centerAndZoom( bmap.point , bmap.zoom );
		
	},
	
	/***************以下为地图的工具*******************/
	//添加鱼骨头缩放
	"addToolFish" : function() {
		
		if( bmap.map != null ) 
			bmap.map.addControl(new BMap.NavigationControl());
		else 
			return false;
			
	},
	/**************************************************/
	
	/***************以下为开启地图功能*****************/
	//开启鼠标中建缩放
	"enableWheelZoom" : function() {
		
		if( bmap.map != null )
			bmap.map.enableScrollWheelZoom();
		else
			return false;
			
	},
	
	//开启标注可拖拽
	"enableMarkerDrag" : function() {
		
		if( bmap.marker != null ) 
			bmap.marker.enableDragging(true); 
		else
			return false;
		
	},
	/**************************************************/
	
	/***************以下为开启地图事件*****************/
	
	"getLatLngByMarker" : function( func ) {
		
		if( bmap.marker != null ) {
			
			var lng = null;
			var lat = null;
			
			bmap.map.addEventListener("mousemove",function(e) {
				
				bmap.marker.addEventListener("mouseup",function(){
					
					lng = e.point.lng;
					lat = e.point.lat;
					
					if( typeof(func) == "function" ) {
					
						func(lng,lat);
						
					}
					
				})
				
			})
			
		}
		
	},
	
	"getZoom" : function( func ) {  
	
		if( bmap.map != null ) {
			
			var zoom = null;
			
			bmap.map.addEventListener("zoomend", function(){ 
			  
				zoom = this.getZoom();
				
				if( typeof(func) == "function" ) {
				  
					func(zoom);
				  
				}
			     
			});
			 
		}
		
	}
	
	/**************************************************/
	
}