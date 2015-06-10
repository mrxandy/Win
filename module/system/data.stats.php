<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("统计信息 - VeryIDE");
?>

	<?php
    
    //loader
    require('include/naver.stats.php');
    
    echo loader_script(array(VI_BASE."source/jquery/jquery.min.js"),'utf-8',$_G['product']['version']);
    
    echo loader_script(array(VI_BASE."source/highcharts/highcharts.js",VI_BASE."source/highcharts/highcharts-more.js",VI_BASE."source/highcharts/modules/exporting.js"),'utf-8',$_G['product']['version']);
	
    ?>
    
    <?php

    //连接数据库
	System :: connect();
	
	$stat = array();
    
    foreach($_G['project']['stat'] as $table => $name ){
        
        $person = System :: $db -> getValue( parse_var( 'SELECT COUNT(*) FROM `sys:'.$table.'` WHERE '. ( $table == 'admin' ? 'id' : 'aid' ) .' = {aid}' ) );
		$public = System :: $db -> getValue( parse_var( 'SELECT COUNT(*) FROM `sys:'.$table.'`' ) );
		
		$stat['cate'][] = $name;
		
		$stat['data']['person'][] = $person;
		$stat['data']['public'][] = $public - $person;
        
        
    }
	
	?>
	
	<div class="item">系统数据（表前辍：<em><?php echo VI_DBMANPRE;?></em>）</div>
	
	<div id="bind_system" style="height:<?php echo 200 + count( $stat['cate'] ) * 30 ;?>px;"></div>
    
    <script>
    
    $(function () {
        $('#bind_system').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: ''
            },
            credits: {
	            text: '由 VeryIDE 提供技术支持',
	            href: 'http://www.veryide.com'
	        },
            xAxis: {
                categories: ['<?php echo implode("','", $stat['cate'] );?>']
            },
            yAxis: {
                min: 0,
                title: {
                    text: '仅统计主要数据量'
                }
            },
            legend: {
                backgroundColor: '#FFFFFF',
                reversed: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
                series: [{
                name: '个人占有',
                data: [<?php echo implode(',', $stat['data']['person'] );?>]
            }, {
                name: '他人占有',
                data: [<?php echo implode(',', $stat['data']['public'] );?>]
            }]
        });
    });
    
    </script>
    
    <div class="item">模块数据（表前辍：<em><?php echo VI_DBMODPRE;?></em>）</div>
    
    <?php
    
    //////////////////////////////
	
	function parse_var( $string ){
		global $_G;
		return str_replace( array('{aid}'), array($_G['manager']['id']), $string);
	}
	
	
	$stat = array();
	
	foreach($_CACHE['system']['module'] as $appid => $app ){
	
		if( $app['statis'] ){
			
			$person = System :: $db -> getValue( parse_var( $app['statis']['person'] ) );
			$public = System :: $db -> getValue( parse_var( $app['statis']['public'] ) );
			
			$stat['cate'][] = $app['name'];
			
			$stat['data']['person'][] = $person;
			$stat['data']['public'][] = $public - $person;
			
		}
	
	}
    
	//关闭数据库
	System :: connect();
        
    ?>
    
    <div id="bind_module" style="height:<?php echo 200 + count( $stat['cate'] ) * 30 ;?>px;"></div>
    
    <script>
    
    $(function () {
        $('#bind_module').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: ''
            },
            credits: {
	            text: '由 VeryIDE 提供技术支持',
	            href: 'http://www.veryide.com'
	        },
            xAxis: {
                categories: ['<?php echo implode("','", $stat['cate'] );?>']
            },
            yAxis: {
                min: 0,
                title: {
                    text: '仅统计主要数据量'
                }
            },
            legend: {
                backgroundColor: '#FFFFFF',
                reversed: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
                series: [{
                name: '个人占有',
                data: [<?php echo implode(',', $stat['data']['person'] );?>]
            }, {
                name: '他人占有',
                data: [<?php echo implode(',', $stat['data']['public'] );?>]
            }]
        });
    });
    
    </script>


<?php html_close();?>