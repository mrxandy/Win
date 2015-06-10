<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("广告分析 - VeryIDE");
?>


<?php

	//module
	require 'function.php';	

	//连接数据库
	System :: connect();

	//***********************

	$do = getgpc('do');
	$appid = getgpc('appid');
	$type = getgpc("type");
	$id  = getgpc("id");
	$name  = stripcslashes( getgpc('name') );
	$multiple  = getnum("multiple",1);
	
	if( !$do ) $do = "Y";	
	
	//统计标识
	$appkey = $appid."-".$id;

?>

	<script>
	function doChange( ele ){		
		ele.innerHTML = ( ele.innerHTML == '图表展示' ) ? '详细数据' : '图表展示';
		Mo('#bind_data,#bind_chart').toggle();		
	}	
	</script>

	<!--settting-->
    <div class="item">
    	<span class="y">
		
		<?php
        if( function_exists("mcrypt_module_open") ){
			
			$appkey = $appid."-".$id;
			
			//这里很奇怪，只支持长度为4的数组，所以要注意
            $hash = array("appkey"=>$appkey,"name"=>$name,"type"=>$type, 'multiple'=> $multiple, "expire"=>time());	
            
            $keys = authcrypt( serialize($hash), VI_SECRET );
        ?>
			<a href="analytic.share.php?keys=<?php echo rawurlencode($keys);?>" target="_blank">分享统计 <?php echo loader_image("link.gif","详细信息");?></a>
        <?php
        }else{
		?>
			<a href="http://www.veryide.com/archive.php?id=45" target="_blank">启用 mcrypt 模块，使用分享功能</a>
        <?php
        
        }
        ?>
        </span>
        <strong><?php echo $name;?> 统计分析</strong> 	
    </div>
	<?php
	
	echo '
    <ul id="naver">
        <li class="menu"><a href="javascript:;" onclick="doChange( this );">图表展示</a></li>
        <li'.($do=='Y'?' class="active"':'').'><a href="?do=Y&appid='.$appid.'&id='.$id.'&type='.$type.'&name='.urlencode($name).'&multiple='.$multiple.'">年份统计</a></li>
        <li'.($do=='M'?' class="active"':'').'><a href="?do=M&appid='.$appid.'&id='.$id.'&type='.$type.'&name='.urlencode($name).'&multiple='.$multiple.'">月份统计</a></li>
        <li'.($do=='D'?' class="active"':'').'><a href="?do=D&appid='.$appid.'&id='.$id.'&type='.$type.'&name='.urlencode($name).'&multiple='.$multiple.'">每日统计</a></li>
        <li'.($do=='H'?' class="active"':'').'><a href="?do=H&appid='.$appid.'&id='.$id.'&type='.$type.'&name='.urlencode($name).'&multiple='.$multiple.'">时段统计</a></li>
    </ul>
	';
	?>
	<!--/settting-->
    
	<?php
	
	//广告名称
	if( $appkey && $do && $type ){
	
		$sql="SELECT date, ( views * $multiple ) as views, ( clicks * $multiple ) as clicks FROM `mod:common_analytic` WHERE appkey='".$appkey."' and category='".$do."'";
		
		if( in_array($type,array("views","clicks")) ){
			$sql .=" and $type>0";
		}
		
		$sql .=" ORDER BY date ASC";
		
		System :: $db -> query($sql);
		$array = System :: $db -> fetchAll();
	}
	
    //关闭数据库
    System :: connect();
    
    
    ///////////////////////////////
    
    echo loader_script(array(VI_BASE."source/jquery/jquery.min.js"),'utf-8',$_G['product']['version']);
    
    echo loader_script(array(VI_BASE."source/highcharts/highcharts.js",VI_BASE."source/highcharts/highcharts-more.js",VI_BASE."source/highcharts/modules/exporting.js"),'utf-8',$_G['product']['version']);
    
	?>

	<div id="box">
		<?php
		
		$active = array( "Y"=>"年份", "M"=>"月份", "D"=>"每日", "H"=>"时段" );
		
		$column = array( "clicks"=>"点击", "views"=>"展示" );
		
		///////////////////////////////
		
		echo '<table id="bind_data" class="table" width="100%" border="0" cellpadding="0" cellspacing="0">';
		echo '<tr class="thead"><td>'.$active[$do].'</td>'.( $do == 'D' ? '<td>星期</td>' : '' ).'<td>展示</td><td>点击</td><td>点击率</td></tr>';
		
		foreach ( $array as $val ){
			
			echo '<tr class="'.zebra( $i, array( "line" , "band" ) ).'"><td class="text-bold">' .$val["date"]. '</td>'.( $do == 'D' ? '<td>'.$_G['project']['weeks'][date("w",strtotime($val["date"]))].'</td>' : '' ).'<td class="text-yes text-bold">' . $val['views']*$multiple . '</td><td class="text-no text-bold">' . $val['clicks']*$multiple . '</td><td class="text-key text-bold">'.( $val['views'] ? round( $val['clicks'] / $val['views'] * 100, 2 ) . '%' : '' ).'</td></tr>';
			
		}
		
		if( count( $array ) == 0 ){
			echo '<tr><td colspan="5" class="notice">没有相关统计数据</td></tr>';
		}
		
		echo '</table>';
		
		///////////////////////////////
		
		?>
		
		<div id="bind_chart" style="display:none; height: 300px; width:96%; margin:10px auto"></div>
		
		<?php
		
		$date = getall_by_key( $array, 'date' );
		
		//简化日期
		if( $do == "D" ){
			
			function cube($n){
				return preg_replace("/[0-9]{4}-[0-9]{2}-/", "", $n);
			}
			
			$sdate = array_map("cube", $date);
			
		}else{
			$sdate = $date;
		}
		
		if( in_array($type,array("views","clicks")) ){
		
			$stat = getall_by_key( $array, $type );
		
			?>
			
			<script>
	
			$(function () {
		        $('#bind_chart').highcharts({
		            chart: {
		            },
		            title: {
		                text: '<?php echo $name;?>'
		            },
		            credits: {
			            text: '由 VeryIDE 提供技术支持',
			            href: 'http://www.veryide.com'
			        },
		            xAxis: {
		                categories: ['<?php echo implode("','", $sdate);?>']
		            },
		            tooltip: {
		                formatter: function() {
		                    var s;
		                    if (this.series.name) { // the pie chart
		                        s = ''+ this.series.name +': '+ this.y +' 次';
		                    } else {
		                        s = ''+ this.x  +': '+ this.y;
		                    }
		                    return s;
		                }
		            },
		            labels: {
		                items: [{
		                    html: '按<?php echo $active[$do].'统计，'.$date[0].' 至 '.end($date);?>',
		                    style: {
		                        left: '40px',
		                        top: '8px',
		                        color: 'black'
		                    }
		                }]
		            },
		            series: [{
		                type: 'column',
		                name: '<?php echo $column[$type];?>',
		                data: [<?php echo implode(",", $stat);?>]
		            }]
		        });
		    });
			
			</script>
			
			<?php
			
		}else{
		
			$vstat = getall_by_key( $array, 'views' );
			$cstat = getall_by_key( $array, 'clicks' );
			$crato = array();
			
			//计算百分比
			foreach ( $array as $val ){
				array_push($crato, ( $val['views'] ? round( $val['clicks'] / $val['views'] * 100, 2 )  : 0 ));
			}
			
			?>
			
			<script>
	
			$(function () {
		        $('#bind_chart').highcharts({
		            chart: {
		            },
		            title: {
		                text: '<?php echo $name;?>'
		            },
		            credits: {
			            text: '由 VeryIDE 提供技术支持',
			            href: 'http://www.veryide.com'
			        },
		            xAxis: {
		                categories: ['<?php echo implode("','", $sdate);?>']
		            },
		            tooltip: {
		                formatter: function() {
		                    var s;
		                    //console.log(this);
		                    
		                    if( this.series.name == '点击率' ){
			                    
			                    s = ''+ this.series.name +': '+ this.y +' %';
			                    
		                    }else{
			                    
			                    if (this.series.name) { // the pie chart
			                        s = ''+ this.series.name +': '+ this.y +' 次';
			                    } else {
			                        s = ''+ this.x  +': '+ this.y;
			                    }
			                    
		                    }
		                    
		                    return s;
		                }
		            },
		            labels: {
		                items: [{
		                    html: '按<?php echo $active[$do].'统计，'.$date[0].' 至 '.end($date);?>',
		                    style: {
		                        left: '40px',
		                        top: '8px',
		                        color: 'black'
		                    }
		                }]
		            },
		            series: [{
		                type: 'column',
		                name: '展示',
		                data: [<?php echo implode(",", $vstat);?>]
		            }, {
		                type: 'column',
		                name: '点击',
		                data: [<?php echo implode(",", $cstat);?>]
		            }, {
		                type: 'spline',
		                name: '点击率',
		                data: [<?php echo implode(",", $crato);?>],
		                marker: {
		                	lineWidth: 2,
		                	lineColor: Highcharts.getOptions().colors[3],
		                	fillColor: 'white'
		                }
		            }]
		        });
		    });
			
			</script>
			
			<?php
			
		}
		
		?>
		
	<?php
    if( $_G['licence']['type'] == 'full' ){
    ?>
    
	<form action="<?php echo GetCurUrl();?>" method="post" data-valid="true">
	
	<table width="100%" cellpadding="0" cellspacing="0" class="form">
	
		<tr class="section">
			<td colspan="2"><strong>高级选项</strong></td>
		</tr>
	    
		<tbody style="display:<?php echo $multiple>1 ? '' : 'none';?>;">
			<tr>
				<th>倍数关系：</th>
				<td>
					<p>
						<input name="multiple" value="<?php echo $multiple;?>" />
						<button class="go" type="submit"></button>
						注意: 此处为数据测试方法，仅供参考。
					</p>
				</td>
			</tr>
		</tbody>
	
	</table>
	
	</form>
    
	<?php
    }
    ?>
	
	</div>


<?php html_close();?>