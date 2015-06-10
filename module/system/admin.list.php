<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("系统用户 - VeryIDE");
?>


	<?php
	
	//loader
	require('include/naver.admin.php');
	
	//菜单处理
	$action = getgpc('action');
	$jump = getgpc('jump');
	
	//连接数据库
	System :: connect();	
	
	$id = getnum('id',0);
	$state = getnum('state',0);
	
	if($action){
	
		//动作处理
		switch ($action){
			
			case "state":
			
				//检查权限
				$func = 'system-admin-mod';
				System :: check_func( $func, FALSE );
				
				//更新数据
				$sql="UPDATE `sys:admin` SET `state`='".$state."',`modify`='".time()."' WHERE id=".$id;
				System :: $db -> execute( $sql );
				
				if( System :: $db -> getAffectedRows() ){
					
					//写入日志
					System :: insert_event($func,time(),time(),"更新用户：".$_CACHE['system']['admin'][$id]["name"]);				
					
					//$_G['project']['message']="成功更改用户状态!";
					System :: redirect($jump,"成功更改用户状态!");
					
				}else{
					//$_G['project']['message']="未找到指定用户!";
					System :: redirect($jump,"未找到指定用户!");
				}
				
				//$_G['project']['jump'] = '';
					
			break;
			
			case "delete":
			
				//检查权限
				$func = 'system-admin-del';
				System :: check_func( $func, FALSE );
					
				//删除数据
				$sql="DELETE FROM `sys:admin` WHERE id=".$id;
				System :: $db -> execute( $sql );
	
				if( System :: $db -> getAffectedRows() ){
					$_G['project']['message']="成功删除用户!";
				}else{
					$_G['project']['message']="未找到指定用户!";
				}
				
				$_G['project']['jump'] = '';				
					
				//写入日志
				System :: insert_event($func,time(),time(),"删除用户：".$_CACHE['system']['admin'][$id]["name"]);				
				
			break;
			
		}
		
		//缓存系统用户
		Cached :: table( 'system', 'sys:admin', array( 'jsonde' => array('config','weibo') ) );
	
	}
	

	//search
	$s = getgpc("s");
	$q = getgpc('q');
	$line = getgpc("line");	
	$gid = getnum('gid',0);
	
	//查看方式
	$v = getgpc("v");
	$v=$v?$v:"list";
	
	?>
    
    <div id="search">
        <form name="form1" method="get" action="?">
            <span class="action">
                <button type="button" class="button" onclick="location.href='admin.edit.php';">添加用户</button>
                <button type="button" class="cancel" onclick="location.href='<?php echo VI_BASE;?>serv.php?action=address';">数据下载</button>
                <button type="button" class="button" onclick="if(confirm('确定现在要更新缓存吗？')){location.href='?action=create';}">更新缓存</button>
            </span>
            
             <select name="s" id="s">
               <option value="account">用户名</option>
               <option value="blog">主页</option>
               <option value="email">电子邮件</option>
            </select>
            <input name="q" type="text" class="text" title="请输入关键字" id="q" value="<?php echo $q;?>">
             
             <select name="line" id="line">
               <option value="">全部状态</option>
               <option value="on">在线</option>
               <option value="off">离线</option>
            </select>
			
             <select name="gid" id="gid">
               <option value="">全部分组</option>
		<?php		
		foreach( $_CACHE['system']['group'] as $g => $cfg ){
		   echo '<option value="'.$g.'">'.$cfg["name"].'</option>';
		}		
		?>
            </select>
			
           <button class="go" type="submit"></button>
        </form>
        
        <script type="text/javascript">
		Mo("select[name=s]").value("<?php echo $s;?>");
		Mo("#line").value("<?php echo $line;?>");
		Mo("#gid").value("<?php echo $gid;?>");
        </script>
    </div>
    
		
	<?php
	
	$sql="SELECT * FROM `sys:admin` WHERE 1=1 ";
	
	if ($s && $q){
		$sql.=" and  $s like '%".$q."%'";
	}
	
	switch( $line ){
		
		case "on":			
			$sql.=" and ".time()." - last_active <= 30";
		break;
		
		case "off":			
			$sql.=" and ".time()." - last_active >= 30";
		break;
	}
	
	if ( $gid ) $sql.=" and gid = ".$gid;
	
	$sql.=" ORDER BY id DESC,state DESC";
	
	//查询数据库_总记录数
	$row_count = System :: $db -> getCount( $sql );

	//分页参数
	$page=getpage("page");
	$page_start=$_G['setting']['global']['pagesize']*($page-1);
	$sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];
	
	//分页链接
	$url="?s=".$s."&q=$q&line=$line&gid=$gid&v=$v&page=";	
	
	$result = System :: $db -> getAll( $sql );
	
    echo '
    <p id="viewer">
        <strong>查看方式：</strong> 
		<a href="'.preg_replace("/&v=(.*?)&/","&v=list&",$url).'" '.($v=="list"?'class="active"':'').'>通讯录</a>
		<a href="'.preg_replace("/&v=(.*?)&/","&v=show&",$url).'" '.($v=="show"?'class="active"':'').'>成员秀</a>
    </p>
    ';
    
    /********************/
	
	$active = array('list','show');
		
	if( in_array($v,$active) ){
    
   		require("include/admin.".$v.".php");
	
	}
    
    /********************/   
	
  
	//关闭数据库
	System :: connect();
    
    ?>
    
    <div id="saving">
		<?php
            echo multipage($page,$row_count,$_G['setting']['global']['pagesize'],$url,"page");
        ?>
    </div>
    


<?php html_close();?>