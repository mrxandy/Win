<script type="text/html" id="segment_notified">
<dl id="package_notified" class="vcloud">
	
	<% if( engine.version > Mo.store.version ){ %>
	<dt><button>&times;</button>可更新内核</dt>
	<dd class="engine">
	
		<p>
			<a href="http://www.veryide.com/member.php?action=engine" target="_blank">
				<img src="module/system/icon.png"><br /><em><%=engine.version%></em>
			</a>
		</p>
		
		<div>
			<%=Mo.String( engine.content).decodeHTML()%>
		</div>
	
	</dd>
	<% } %>
	
	<% if( statis.upgrade > 0 ){ %>
	<dt><button>&times;</button>可更新模块</dt>
	<dd>
	
		<ul>
		<% for( var appid in module ){ %>
		<% if ( module[appid].installed && module[appid].latest ){ %>
		<li>
			<a href="http://www.veryide.com/market.php?action=show&appid=<%=appid%>" target="_blank">
				<img src="module/<%=appid%>/icon.png"><%=module[appid].name%><br /><em><%=module[appid].latest%></em>
			</a>
		</li>
		<% } %>
		<% } %>
		</ul>
	
	</dd>
	<% } %>
	
	<% if( statis.install > 0 ){ %>
	<dt><button>&times;</button>可安装模块</dt>
	<dd>
	
		<ul>
		<% for( var appid in module ){ %>
		<% if ( module[appid].installed == false ){ %>
		<li>
			<a href="http://www.veryide.com/market.php?action=show&appid=<%=appid%>" target="_blank">
				<img src="<%=module[appid].icon%>"><%=module[appid].name%><br /><em><%=module[appid].latest%></em>
			</a>
		</li>
		<% } %>
		<% } %>
		</ul>
	
	</dd>
	<% } %>
	
	<% if( recommend.length ){ %>
	<dt><button>&times;</button>推荐新模块</dt>
	<dd>
	
		<ul>
		<% for( var index in recommend ){ %>
		<li>
			<a href="http://www.veryide.com/market.php?action=show&appid=<%=recommend[index].appid%>" target="_blank">
				<img src="<%=recommend[index].icon%>"><%=recommend[index].name%><br /><em><%=recommend[index].price > 0 ? '￥' + recommend[index].price : '免费'%></em>
			</a>
		</li>
		<% } %>
		</ul>
	
	</dd>
	<% } %>
	
	<% if( promotion.length ){ %>
	<dt><button>&times;</button>可用优惠码</dt>
	<dd>
	
		<% for( var index in promotion ){ %>
		<p>
			<strong class="code"><%=promotion[index].code%></strong>
			<em class="amount">￥<%=promotion[index].amount%></em>
			<em class="expire"><%=Mo.date( 'y-m-d', promotion[index].expire)%> 过期</em>
		</p>
		<% } %>
	
	</dd>
	<% } %>
	
	<% if( notified.length ){ %>
	<dt><button>&times;</button>VeryIDE 通知</dt>
	<dd>
	
		<% for( var index in notified ){ %>
		<p>
			<ins><%=parseInt(index)+1%></ins>
			<a href="<%=notified[index].link%>" class="title" target="_blank"><%=notified[index].name%></a>
			<em class="expire"><%=Mo.date( 'y-m-d', notified[index].dataline)%></em>
		</p>
		<% } %>
		
	</dd>
	<% } %>
	
	<dd class="ctrl">
		<a href="http://www.veryide.com/member.php" target="_blank">立即前往 VeryIDE 继续操作</a>
	</dd>
	
</dl>
</script>

<script>

Mo.reader( function(){ window.setTimeout( Serv.Cloud.notified, 10000 ); } );

</script>