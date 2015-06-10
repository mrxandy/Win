<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/
require '../../source/dialog/loader.php';
html_start($c_name."文件管理 - VeryIDE");
?>


	<?php
	require(VI_ROOT."source/class/ftp.php");
   
	//loader
	require('include/naver.stats.php');

	$url = getgpc("url");
	$action = getgpc('action');

	//连接数据库
	System :: connect();

	if ($action !=""){
		$list = getgpc('list');

		switch ($action){

			case "delete":
			
				//检查权限
				$func = 'system-upload-del';
				System :: check_func( $func, FALSE );				
		
				//查询数据库
				$sql="SELECT id,name,remote FROM `sys:attach` WHERE id in(".$list.")";			
				
				//查询数据库_总记录数
				$row_count = System :: $db -> getCount( $sql );
				
				$result = System :: $db -> getAll( $sql );

       			foreach( $result as $row ){

					//删除数据_记录
					$sql="DELETE FROM `sys:attach` WHERE id=".$row['id'];
					
					System :: $db -> execute( $sql );
					
					//远程附件
					if( $row["remote"] ){
					
						//连接 FTP
						$ftp = new ClsFTP($_G['setting']["attach"]["FTP_USER"], $_G['setting']["attach"]["FTP_PASS"], $_G['setting']["attach"]["FTP_HOST"], $_G['setting']["attach"]["FTP_PORT"]);
						
						//FTP 模式
						$ftp->pasv( $_G['setting']['attach']['FTP_PASV'] == 'true' );
						
						//删除文件
						$ftp->delete($row['name']);
						
						//关闭 FTP
						$ftp->close();
			
					}else{
	
						//删除数据_文件
						if(file_exists(VI_ROOT.$row['name'])){
							unlink(VI_ROOT.$row['name']);
						}
						
					}
				}				
					
				//写入日志
				System :: insert_event($func,time(),time(),"删除文件：".$list);				
				
				//System :: redirect($url,"所选文件成功被删除！");
				$_G['project']['message']=$row_count."个文件成功被删除！";
				
			break;

		}

	}

	$s = getgpc("s");
	$q = getgpc('q');
	$f = getgpc("f");
	$r = getnum("r",-1);
	$a = getnum("a",0);
	
	//查看方式
	$v = getgpc("v");
	$v=$v?$v:"list";
	
	//exit($v);


	//日期筛选
	$start = getgpc('start');
	$end = getgpc('end');
	
	

	?>
    <div id="search">

        <!--用于文件操作-->
        <form name="post-form" id="post-form" method="post" action="?">
        <input name="list" id="list" type="hidden" value="">
        <input name="action" id="action" type="hidden" value="" />
        <input name="url" id="url" type="hidden" value="" />
        </form>
        <!--用于文件操作-->
            
        <form name="find-form" id="find-form" method="get" action="?">
			<span class="action">
				<select name="a" id="a" onchange="Mo(this.form).submit();" ignore="true">
					<optgroup label="所有者" />
					<option value="0">全部人员</option>
					<?php
						foreach( $_CACHE['system']['admin'] as $i => $a ){
                echo '<option value="'.$i.'">'.$a['account'].'</option>';
            }
					?>
				</select>
			
				<select name="f" id="f" onchange="Mo(this.form).submit();">
					<option value="">全部类型</option>
					<option value="image">图片</option>
					<option value="flash">动画</option>
					<option value="media">多媒体</option>
					<option value="files">其它类型</option>	         
				</select>
				<select name="r" id="r" onchange="Mo(this.form).submit();">
					<option value="-1">全部位置</option>
					<option value="0">本地</option>
					<option value="1">远程</option>
				</select>
            </span>
            
            <select name="s" id="s">
                <option value="name">文件名</option>
                <option value="account">用户</option>
                <option value="input">来源</option>
                <option value="type">类型</option>
            </select>
            <input name="q" type="text" class="text" id="q" value="<?php echo $q; ?>">
            
            <input name="start" type="text" class="date" id="start" value="<?php echo $start;?>" size="12" readonly="true" title="年月日">
            -
            <input name="end" type="text" class="date" id="end" value="<?php echo $end;?>" size="12" readonly="true" title="年月日">
            
            <input type="hidden" name="v" value="<?php echo $v;?>" />
            <button type="submit" class="go"></button>

        </form>
        <script type="text/javascript">
			Mo("#f").value("<?php echo $f;?>");
			Mo("select[name=s]").value("<?php echo $s;?>");
			Mo("#r").value("<?php echo $r;?>");
			Mo("#a").value("<?php echo $a;?>");
        </script>

    </div>
    
	<?php 
    
    $sql="SELECT * FROM `sys:attach` WHERE 1=1";

    if ($q && $s) $sql.=" and `".$s."` like '%".$q."%'";
	
	if ( $r != -1 ) $sql.=" and  remote = ".$r."";
	
	switch( $v ){
		
		case "album":
			$sql.=" and type in ('".implode("','",$_G['upload']['image'])."')";
		break;
		
		case "flash":
			$sql.=" and type = 'swf' ";
		break;
		
		case "list":
			$sql.=" and type in ('".implode("','",$_G['upload']['image'])."')";
		break;
		
	}

	if ($a){
		$sql.=" and  aid ='".$a."'";
	}
	
    if ($start && $end){
		$sql.=" and  dateline >=".strtotime($start)." and dateline <= ".strtotime($end.' 23:59:59');
    }

    $sql.=" ORDER BY id DESC";
    
    //查询数据库_总记录数
    $row_count = System :: $db -> getCount( $sql );
    
    $page=getpage("page");
    $page_start=$_G['setting']['global']['pagesize']*($page-1);
    $sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];

    $result = System :: $db -> getAll( $sql );
    
    /********************/
    
    //分页链接
    $url="?q=".$q."&s=".$s."&f=".$f."&a=".$a."&start=$start&end=$end&v=$v&page=";
    
    echo '
    <p id="viewer">
        <strong>查看分类：</strong> 
		<a href="'.preg_replace("/&v=(.*?)&/","&v=list&",$url).'" '.($v=="list"?'class="active"':'').'>全部</a>
		<a href="'.preg_replace("/&v=(.*?)&/","&v=album&",$url).'" '.($v=="album"?'class="active"':'').'>图片</a>
		<a href="'.preg_replace("/&v=(.*?)&/","&v=flash&",$url).'" '.($v=="flash"?'class="active"':'').'>动画</a>
    </p>
    ';
    
    /********************/
	
	$active = array('list','album','flash');
		
    if( in_array($v,$active) ){
    
    	require("include/attach.".$v.".php");
	
	}
    
    /********************/
	
	//关闭数据库
	System :: connect();	
	
	?>
    
    <div id="saving">
		<?php
            print multipage($page,$row_count,$_G['setting']['global']['pagesize'],$url,"page");
        ?>
    </div>

<?php html_close();?>