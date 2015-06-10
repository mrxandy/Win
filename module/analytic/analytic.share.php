<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

//载入全局配置和函数包
require '../../app.php';

//module
Module :: loader( 'analytic' );

$keys = $_GET["keys"];

$code = rawurlencode($keys);

$hash = unserialize( authcrypt( $keys, VI_SECRET, 'decode' ) );

//exit(authcrypt( $keys, VI_SECRET, 'decode' ));

$pwd  = $_GET["password"];

$do = getgpc('do');

if( !$do ) $do = "Y";

//验证密码
if( $pwd && md5( $pwd ) == md5( $_G['setting']['global']['password'] ) ){
	
	$_SESSION['PASSWORD'] = md5( $pwd );
	
}elseif( $_G['setting']['global']['password'] && $_SESSION['PASSWORD'] != md5( $_G['setting']['global']['password'] ) ){
	
	$_SESSION['PASSWORD'] = NULL;
	
}

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_G['product']['charset'];?>" />
<title>分享统计 - Powered By VeryIDE</title>
<?php
echo loader_style(array(VI_BASE."static/style/general.css",VI_BASE."static/style/share.css"),'utf-8',$_G['product']['version']);
echo loader_script(array(VI_HOST."static/js/mo.js",VI_HOST."static/js/serv.share.js"),'utf-8',$_G['product']['version']);
echo '<script>'. System :: reader_config() .'</script>';
?>
</head>

<body>

	<div id="wrapper" class="<?php echo getcookie('toggle');?>">
    
    	<div id="header">
        	<div id="toggle"></div>
        	<h2>分享统计</h2>
        </div>
        
        <div id="main">
        	
            <?php
			
			if( !is_array($hash) ){
				
				echo '<p>无效链接！</p>';
				
			}elseif( time() - $hash['expire'] > $_G['setting']['global']['expire'] ){
			
				echo '<p>已过期链接！</p>';
			
			}elseif( $_G['setting']['global']['password'] && !$_SESSION['PASSWORD'] ){
			
				echo '<form method="post"><p><strong>查看密码：</strong><input name="password" type="text" size="20" /><button type="submit" class="button">确定</button> '.( isset($pwd) ? '<span class="text-no">请输入正确的查看密码</span>' : '' ).' </p></form>';
			
			}else{
            ?>
                
                <p>
                    <strong><?php echo urldecode($hash['name']);?></strong>
                    （有效查看时间：<strong><?php echo date("Y-m-d H:i:s",$hash['expire']+$_G['setting']['global']['expire']);?></strong>）
                </p>
                
                <ul id="naver">
                	<li <?php echo $do=='Y' ? 'class="active"' : ''?>><a href="?do=Y&keys=<?php echo $code;?>">年份统计</a></li>
                	<li <?php echo $do=='M' ? 'class="active"' : ''?>><a href="?do=M&keys=<?php echo $code;?>">月份统计</a></li>
                	<li <?php echo $do=='D' ? 'class="active"' : ''?>><a href="?do=D&keys=<?php echo $code;?>">每天统计</a></li>
                	<li <?php echo $do=='H' ? 'class="active"' : ''?>><a href="?do=H&keys=<?php echo $code;?>">时段统计</a></li>
                </ul>
                
                <?php
                
                echo loader_script(array(VI_BASE."source/jquery/jquery.min.js",VI_BASE."source/jquery/jquery.resize.min.js"),'utf-8',$_G['product']['version']);
    
				echo loader_script(array(VI_BASE."source/highcharts/highcharts.js",VI_BASE."source/highcharts/highcharts-more.js",VI_BASE."source/highcharts/modules/exporting.js"),'utf-8',$_G['product']['version']);
                
                ?>
                
                <div id="bind_chart" style=" width: 100%; overflow:hidden;"></div>
		
				<?php
                
                //统计类型
                $type = $hash["type"];
                
                //统计标识
                $appkey = $hash["appkey"];
            
                //连接数据库
				System :: connect();                

                //广告名称
                if( $appkey && $do && $type ){
                
                    $sql="SELECT date,".$type." FROM `mod:common_analytic` WHERE appkey='".$appkey."' and category='".$do."'";
                    
                    if( in_array($type,array("views","clicks")) ){
                        $sql .=" and $type>0";
                    }
                    
                    //echo $sql;
                    
                    System :: $db -> query($sql);
                    $array = System :: $db -> fetchAll();
                }                
                
                //关闭数据库
				System :: connect();
                
                /*********************/
				
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
				        
				        ///////////////////////////////
				       
						var chart = $('#bind_chart').highcharts();
						
						$('#bind_chart').resize(function() {
							chart.setSize( this.offsetWidth, this.offsetHeight );
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
					            href: 'http://www.veryide.com',
					            enabled: false
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
				        
				        ///////////////////////////////
				       
						var chart = $('#bind_chart').highcharts();
						
						$('#bind_chart').resize(function() {
							chart.setSize( this.offsetWidth, this.offsetHeight );
						});
				       
				       
				    });
					
					 
					
					</script>
					
					<?php
					
				}
				
				//exit;
				?>
            
                <?php
               
					
			}
			?>

        </div>
       
    	<div id="footer">
			<?php echo $_G['project']['powered'];?>
            <?php echo $_G['product']['appname'];?>
            <?php echo $_G['product']['version'];?>
        </div>
    
    
    </div>

</body>
</html>