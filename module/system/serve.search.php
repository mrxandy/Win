<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
require("../../function/client.php");

html_start("搜索引擎 - VeryIDE");
?>


	<?php
    //loader
	require('include/naver.start.php');
		
	$do = getgpc('do');
	$s = getgpc("s");
	$q = getgpc('q');

	//连接数据库
	System :: connect();
	
	?>
    
    <div id="search">
		
        <form name="form1" method="get" action="?">
            <input name="q" type="text" class="text" id="q" value="<?php echo $q; ?>" size="30" maxlength="30">
            <button type="submit" class="go"></button>
            <a href="http://www.google.com.hk/search?hl=zh-CN&source=hp&client=pub-7104481011578148&q=<?php echo $q; ?>" target="_blank">在 Google 上搜索</a>
            <input type="hidden" name="do" value="<?php echo $do;?>" />            
            <input type="hidden" name="s" value="<?php echo $s;?>" />            
            <?php
            if(!$s || !$q){
                echo '<div class="advanced">'.loader_image("icon/info.png","提示").' 请输入要搜索的关键字，例如：<a href="?q=活动">活动</a>、<a href="?q=调查">调查</a>、<a href="?q=报名 投票">报名 投票</a></div>';
            }
            ?>
        </form>
    
    </div>
    
	<?php

	//读取配置			

	foreach($_CACHE['system']['module'] as $appid => $val){

		if ($val["config"]) {
		
			//echo VI_ROOT.'module/'.$val["config"];
			
			$self = VI_ROOT.$val["config"];
			
			if( file_exists( $self ) ){
			
				require( $self );
				
				$sch = $_G['module'][$appid]['search'];
				
			}else{
			
				$sch = null;
			
			}

			if(  is_array( $sch ) ){
			
					//重定向到第一个搜索
					if(!$s){
						header("Location:?do=module&s=$appid&q=".urlencode($q));
						exit();
					}
					
					if( $s && $q ){
					
						//sql
						$sql="SELECT count(".$sch["order"].") as count FROM ".$sch["table"]." WHERE 1=1 ".$sch["where"]." ";
						
						//转换表前辍
						$sql = format_table($sql);
						
						//重组查询
						$arr = explode(" ",$q);						
						$txt = implode( "%' or ".$sch["keyword"]."  like '%",$arr);						
						$sql.=" and (".$sch["keyword"]." like '%".$txt."%')";
						
						//查询数据库_总记录数
						$count = System :: $db -> getValue( $sql );
						
						$_G['module'][$appid]['search']["result"] = $count;						
					
						$nav .= '<a href="?do=module&s='.$appid.'&q='.urlencode($q).'">'.$sch["title"].' ('.$count.')'.'</a> | ';
					}else{
						$nav .= '<a href="?do=module&s='.$appid.'&q='.urlencode($q).'">'.$sch["title"].'</a> | ';
					}
					
			}
		}
	}
	
	//$nav .= '<a href="?do=system&s='.$appid.'&q='.urlencode($q).'">系统</a> | ';
	
	//处理搜索导航	
	if($s && $q){		
		$nav = str_replace('href="?do='.$do.'&s='.$s.'&q='.urlencode($q).'"','class="active"',$nav);
		echo '<div id="viewer"><strong>分类查看：</strong> '.str_replace(array('a> | ','a><a'),array('a>','a> | <a'),$nav).'</div>';	
	}
	
	/******************************/


    function getMicrotime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    function getTitle($html) {
        $title = "";
        if (preg_match("'<title>'si", $html)) {
            $title = preg_replace("'^.*?<title>|</title>.*$'si", "", $html);
            if (empty($title)) {
                $title = "Untitled Page";
            }
        }
        return strip_tags($title);
    }

    function stripScriptTags($string) {
        $pattern = array("'\/\*.*\*\/'si", "'<\?.*?\?>'si", "'<%.*?%>'si", "'<script[^>]*?>.*?</script>'si");
        $replace = array("", "", "", "");
        return preg_replace($pattern, $replace, $string);
    }

    function clearSpaces($string, $clear_enters = true) {
        $pattern = ($clear_enters == true) ? ("/\s+/") : ("/[ \t]+/");
        return preg_replace($pattern, " ", trim($string));
    }
 
	//关键字
	if($s && $q){		
    
		/********************/
		
		//echo "include/search.".$do.".php";
    
		require("include/search.".$do.".php");		
    
		/********************/
    
	}
	?>



<?php html_close();?>