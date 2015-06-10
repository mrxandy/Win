<?php

if(!defined('VI_BASE')) {
	exit('Access Denied');
}

///////////////////////////////////

//圣诞节（共5天）
if( $xmas ){
	
	//圣诞帽
	if( $config["ui-head"] != "N" ){
		echo '<div id="newyear"><img id="lanternPoint" src="static/image/hat.png" alt="圣诞节快乐！" /></div>';
	}
	
	//下雪花
	if( $config["ui-snow"] != "N" ){
	
		echo '<script src="source/snow/jsized.snow.min.js" type="text/javascript"></script><script type="text/javascript">createSnow("source/snow/", 60);</script>';
		
		//积雪
		echo '<div id="snow"></div>';
		
	}
	
}

/***********************/

//农历春节（共30天）
$lunar = new Lunar();

//得到今年春节的日期
$date = $lunar->getLar(date("Y").'-12-31',1);

//15天
$day15 = 3600*24*10;

//前后15天，共30天
if( $date - $day15 <= time() && $date + $day15 >= time() ){
		
	//圣诞帽
	echo '<div id="newyear" class="spring"><img id="lanternPoint" src="static/images/spring.png" alt="农历春节！" /></div>';
	
}

?>

<script type="text/javascript">
Mo.reader(
function(){

    <?php
    //用户偏好设置_开始
    if( $config['ui-model'] == 'classic' && is_array( $config ) ){
    ?>
    
    /*顶部*/
    if( "<?php echo $config["ui-theme"];?>" == "cloud" || "<?php echo $config["ui-theme"];?>" == "moving" ){
        
        Mo("#header").attr({"className":"cloud"}).style({'backgroundPosition':'0 0'});
        Mo("#header-logo").attr({"className":"cloud"});
        
        if( "<?php echo $config["ui-theme"];?>" == "moving" ){
    
            /*动画*/
            setInterval(function(){
                var x = Mo( '#header' ).style("backgroundPosition");
                var x = x.split(' ')[0].replace('pt','').replace('px','');
                Mo( '#header' ).style({ 'backgroundPosition' : parseInt(x)+1+'px 0' });
            },100);
        
        }
    
    };
    
    /*菜单*/
    if( "<?php echo $config["ui-menu"];?>" != "auto" && Mo("#menu-link").size() ){
        
        if( "<?php echo $config["ui-menu"];?>" == "open" ){
	    Mo("#menu-link ul").show();
	    Mo("#menu-link div").attr({'className':'active'});
        }else{
	    Mo("#menu-link ul").hide();
	    Mo("#menu-link div").attr({'className':'none'});
        }
        
    };

    <?php    
    }
    //用户偏好设置_结束
    ?>
    
});
</script>