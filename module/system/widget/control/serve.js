
var Factor = {

	//应用描述
	App : { 'appid' : 'system', 'appname' : '设置中心' },

	 //唯一标识
	Hash : null,
	
	/*
		回调函数
		data	统计数据
	*/
	Call : function( data ){
	
		Factor.Badge( data, 'setting', data['status'] + data['upgrade'] );
		
		Factor.Badge( data, 'secure', data['filehash'] );
		
		Factor.Badge( data, 'module', data['install'] );
		
	},
	
	/*
		数标控制
		dataset	原始数据
		object	对象
		stat	数字
	*/
	Badge : function( dataset, object, stat ){
	
		var tag = '*[data-object='+ object +']';
		
		//当前 App 右上角创建数标
		if( Mo(tag+' em').size() == 0 ){
			Mo(tag).create( "em" , { "innerHTML" : "0", "className" : "badge" }, true ).hide();
		}
		
		//判断显示还是隐藏数标
		if( stat > 0 ){
			Mo(tag+' em').html( stat ).show();
		}else{
			Mo(tag+' em').hide();
		}
		
		var ifr = Mo('*[data-appname='+ Factor.App.appname +'] iframe').item(0);
		
		//当数据传递至 iframe
		for( var k in dataset ){
			ifr && ifr.contentWindow.Callback && ifr.contentWindow.Callback( k, dataset[k] );
		}
		/*
		if( object == 'setting' ){
			var ifr = Mo('*[data-appname='+ Factor.App.appname +'] iframe').item(0);
			ifr && ifr.contentWindow.Callback && ifr.contentWindow.Callback('system.server.php',stat);
		}
		*/
		
	},
	
	//读取消息数
	Fetch : function(){
		
		Mo.script( Serv.API+"?action=service&execute=status&callback=Factor.Call&rnd="+Math.random() );
		
	},
	
	//初始化
	Inti : function(){
		
		//轮询（15秒）
		window.setInterval(function(){
			
			//有控制面板才查询
			if( Mo("*[data-widget=control]").size() > 0 ){
				
				Factor.Fetch();
				
			}
									
		}, parseInt( Mo.store.heartbeat ) * 1000 );
		
		//主动执行
		Mo("*[data-widget=control]").size() && Factor.Fetch();
		
	}
	
};

Factor.Inti();
