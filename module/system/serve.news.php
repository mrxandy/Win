<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("官方动态 - VeryIDE");
?>

	<?php
	
	echo loader_style(VI_BASE."static/style/feed.css",'utf-8',$_G['product']['version']);
	
	require VI_ROOT.'source/class/xml.lastrss.php';
	
    ?>
    
    <?php
	
	$action = getgpc('action');
	$action = $action ? $action : 'archive';
	
	$nav = array('archive'=>$_G['project']['home'].'misc.php?action=rss','thread'=>$_G['project']['home'].'misc.php?action=feed','comment'=>$_G['project']['home'].'misc.php?action=feed&execute=comment');

	?>
    
    <div id="box">


		<?php
		
		echo '
        <ul id="subtab">
			<li class="action"><a href="http://www.veryide.com/" target="_blank">官网</a> - <a href="http://www.veryide.com/bbs.php" target="_blank">论坛</a></li>
            <li'.($action=='archive'?' class="active"':'').'><a href="?action=archive" data-hash="true">最新文章</a></li>
            <li'.($action=='thread'?' class="active"':'').'><a href="?action=thread" data-hash="true">最新帖子</a></li>
            <li'.($action=='comment'?' class="active"':'').'><a href="?action=comment" data-hash="true">最新评论</a></li>
        </ul>		
		';
		
		?>
    
    	<div id="feed">

        	<?php			
            
			//日志RSS地址			
			$xml = $nav[$action];
			
			if( $xml ){
				
				//创建缓存目录
				$dir = create_dir(VI_ROOT.'cache/rss/');
				
				// on crée un objet lastRSS
				$rss = new lastRSS;
		
				// options lastRSS
				$rss->cache_dir   = $dir; // dossier pour le cache
				$rss->cache_time  = 3600;      	// fréquence de mise à jour du cache (en secondes)
				$rss->date_format = 'd/m';     // format de la date (voir fonction date() pour syntaxe)
				$rss->CDATA       = 'content'; // on retire les tags CDATA en conservant leur contenu
		
				//data
				$data='';
				
				if ($rs = $rss->get($xml)){
					
					for($i=0;$i<count($rs['items']);$i++){
						
						//转编码
						if( $_G['product']['charset'] == "utf-8" ){
							$rs['items'][$i]['title'] = iconv( "gbk", $_G['product']['charset'], $rs['items'][$i]['title'] );
							$rs['items'][$i]['category'] = iconv( "gbk", $_G['product']['charset'], $rs['items'][$i]['category'] );
							$rs['items'][$i]['description'] = iconv( "gbk", $_G['product']['charset'], $rs['items'][$i]['description'] );
							$rs['items'][$i]['author'] = iconv( "gbk", $_G['product']['charset'], $rs['items'][$i]['author'] );
						}
						
						$data .= '<div class="feed">
							<h2 class="title"><a href="'.$rs['items'][$i]['link'].'">'.ClearHtml($rs['items'][$i]['title']).'</a></h2>
							<p class="meta">
								<em>
								发布于：'.$rs['items'][$i]['pubDate'].' 作者：<a href="search.php?q=uid:'.$rs['items'][$i]['author'].'" target="_blank">'.$rs['items'][$i]['author'].'</a>
								分类：<a href="search.php?cat='.$rs['items'][$i]['category'].'" target="_blank">'.$rs['items'][$i]['category'].'</a>
								</em>
							</p>
							<div class="entry">
								'.$rs['items'][$i]['description'].'
								<div><a href="'.$rs['items'][$i]['link'].'" class="links" target="_blank">阅读全文</a></div>
							</div>
						</div>';						
						
					}
					
				}else{
					$data="<p>无法连接网络</p>";	
				}
			}
			
			echo $data;
			
			?>
        
        </div>    

    </div>	
    
    


<?php html_close();?>