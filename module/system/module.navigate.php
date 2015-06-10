<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("系统设置 - VeryIDE");
?>



	<?php
	require_once(VI_ROOT."source/class/navigate.php");	
	
	//模块ID
	$appid = getgpc('appid');
	
	//配置地址
	$self = VI_ROOT.'module/'.$appid.'/config.php';
	
	//加载导航
	include('include/naver.setting.php');
	
	if( $appid && file_exists( $self ) && require( $self ) ){
	
		//模块配置
		$app = $_G['module'][$appid];
		
		//权限名称
		$func = 'system-'. $app['model'] .'-set';
	
		//检查权限
		System :: check_func( $func, FALSE );
		
		$_GalSet = new Navigate(VI_ROOT.'module/'.$appid.'/navigate.php' , VI_ROOT.'module/'.$appid.'/navigate.xml' , $appid);
		
		if( $_GET["action"]=="update" && !empty($_POST) ){
			
			if($_GalSet->save('POST')){
				
				//连接数据库
				System :: connect();
					
				//写入日志
				System :: insert_event($func,time(),time());
				
				//关闭数据库
				System :: connect();
				
				echo '<div id="state">成功修改模块配置，新配置将立即生效</div>';
				
			}else{
				
				echo "<div id='state' class='failure'>保存模块配置失败！请检查 ./module/".$appid."/navigate.php 是否有读写权限</div>";
				
			}
			
		}elseif( $_GalSet -> writable() == FALSE ){
			echo "<div id='state' class='failure'>请检查 ./module/".$appid."/navigate.php 是否有读写权限</div>";	
		}
		
		$form = $_GalSet->transform();
		
		?>
		
		<script type="text/javascript">
			
			var addrowdirect = 0;
			var rowallnum = new Array();
			if ( typeof rowmaxnum == 'undefined' ) {
			    rowmaxnum = new Array();
			}
			
			function delrow( obj ){
				
				//当前行
				var trow = obj.parentNode.parentNode;
				
				Mo( trow ).remove();
				
			}
			
			/*
				插入行
				obj		当前参照行
				style	行样式
				type		rowtypedata 索引值
				set		需要设置的值
			*/
			function addrow( obj, type, style, SET ) {
			
			/*
			    if (isUndefined(rowallnum[type])) {
				rowallnum[type] = 1;
			    } else {
				rowallnum[type]++;
			    }

			    if ( rowmaxnum[type] != null && rowallnum[type] > rowmaxnum[type]) {
				alert("对不起，已达到数量上限，无法再添加");
				return;
			    }
			    */
			    
			    	//当前行
			    	var trow = obj.parentNode.parentNode;
			    
			    	//当前表
				var table = trow.parentNode.parentNode;

				if(!addrowdirect) {
					var row = table.insertRow( trow.rowIndex );
				} else {
					var row = table.insertRow( trow.rowIndex + 1 );
				}
				
				row.className = style ? style : '';
				
				///////////
				
				var typedata = rowtypedata[type];
				
				/*
					0	colSpan		单元格所占数量
					1	tmp			内容
					2	className	样式名
				*/
				for(var i = 0; i <= typedata.length - 1; i++) {
					
					var cell = row.insertCell(i);
					cell.colSpan = typedata[i][0];
					var tmp = typedata[i][1];
					if(typedata[i][2]) {
						cell.className = typedata[i][2];
					}
					
					//为输入框写入值
					if( tmp && SET ){
						for( var k in set){
							if( tmp.indexOf( k ) > -1 ){
								tmp = tmp.replace( 'value=""' , 'value="'+ set[k] +'"' );
							}				
						}
					}
					
					//索引值+1
					tmp = tmp.replace(/\{(\d+)\}/g, function($1, $2) {alert($2);return addrow.arguments[parseInt($2) + 1];});
					cell.innerHTML = tmp;
				}
				
				addrowdirect = 0;				
			}

			var rowtypedata = [
				[
					[1,''],
					[1,'<input type="text" class="text" name="sort[]" size="1" value="">','center'],
					[1,'<input type="text" class="text" size="10" name="name[]" value="" />'],
					[1,'<input type="text" class="text" size="10" name="link[]" value="" />'],
					[1,'<select name="target[]"><option></option><option value="_blank">_blank</option><option value="_blank">_self</option></select>','center'],
					[1,'<input type="checkbox" class="checkbox" name="show[]" value="true" checked>','center'],
					[1,'<input type="text" class="text" style="width:85%" name="title[]" value="" />'],
					[1,'<input type="text" class="text" style="width:85%" name="keywords[]" value="" />'],
					[1,'<input type="text" class="text" style="width:85%" name="description[]" value="" />'],
					[1,'自定义'],
					[1,'','center']
				]
			];

			
		</script>
		
		<form method="post" action="?appid=<?php echo $appid;?>&action=update" data-mode="edit" data-valid="true">

		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table" id="table">			
			<tr class="thead">
			<td>Key</td>
			<td>排序</td>
			<td>名称</td>
			<td>链接</td>
			<td>打开方式</td>
			<td>可见</td>
			<td>页面标题</td>
			<td>关键字词</td>
			<td>描述信息</td>
			<td>类型</td>
			<td>操作</td>
			</tr>		
			<?php
	
			echo $form;		
	
			?>					
			<tr class="line">
                <td></td>
                <td colspan="10"><a href="javascript:;" onclick="addrow(this,0,'line');">添加新导航</a></td>
            </tr>
			<tr class="line">
				<td></td>
				<td colspan="10">
					<button type="submit" name="" class="submit">保存设置</button>
				</td>				
			</tr>	
		</table>

		</form>
	
	<?php
		
	}else{
	
		echo "<div id='state' class='failure'>模块不存在或已删除！请检查 module/".$appid."/ 目录是否有效</div>";
	
	}
	
    ?>

<?php html_close();?>