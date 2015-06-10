<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';


html_start("日志查询 - VeryIDE");
?>


	<?php
    
	//loader
	require('include/naver.admin.php');
	
	//重组事件
	$Logbook = array();
	foreach($_CACHE['system']['module']['system']['permit'] as $item => $array){		
		foreach($array as $key => $value){
			$Logbook["sys-".$key] = $value;
		}
	}
	
	$Logbook["login"] = "登录系统";
	$Logbook["exit"] = "退出系统";
	
 
    //日期筛选
    $start = getgpc('start');
    $end = getgpc('end');
    
    //search
    $s = getgpc("s");
    $q = getgpc('q');
    $event = getgpc("event");
	$account = getnum("account",0);
    
    //sql
    $sql="SELECT * FROM `sys:event` WHERE 1=1 ";
    
    //权限检查
	
	/*
    if( $s && $q && System :: check_func( 'system-event' ) == false){
    
        $sql.=" and  event = 'login' and aid=".$_G['manager']['id'];
		
        $_G['project']['message']="<b>提示:</b> 没有权限查看其他用户的日志!";
        
    }else{
		
		*/
        if($s && $q){
            $sql.=" and `".$s."` like '%".$q."%'";
        }
        
        if($event){
            $sql.=" and  event = '".$event."'";
        }
        
        if($account){
            $sql.=" and  aid = '".$account."'";
        }
        
    //}
	
	//无权限查看其它用户日志
	if( System :: check_func( 'system-event' ) == false ){
		$sql.=" and  aid=".$_G['manager']['id'];
	}
    
    if ($start && $end){
        $sql.=" and  dateline >=".strtotime($start)." and modify <= ".strtotime($end.' 23:59:59');
    }
    
    $sql.=" ORDER BY id DESC ";
	
    ?>

    
    
        <div id="search">
            <!--用于日志操作-->
            <form name="post-form" id="post-form" method="post" action="?">
            <input name="list" id="list" type="hidden" value="">
            <input name="action" id="action" type="hidden" value="" />
            <input name="url" id="url" type="hidden" value="" />
            </form>
            <!--用于日志操作-->
                
            <form name="find-form" id="find-form" method="get" action="?">
                <span class="action">
                    
                    <?php
					if( System :: check_func( 'system-event' ) ){
					?>
                    <button class="button" type="button" onclick="Mo('#account').value(<?php echo $_G['manager']['id'];?>);Mo('#find-form').item(0).submit();">我的日志</button>
                    <select name="account" id="account" onchange="Mo('#find-form').item(0).submit();" ignore="true">
                        <optgroup label="选择用户" />                        
                        <option value="">所有用户</option>
						<?php
                        foreach($_CACHE['system']['admin'] as $aid => $row){
                            echo '<option value="'.$aid.'">'.$row['account'].'</option>';
                        }
                        ?>
                    </select>
                    <script> Mo("#account").value("<?php echo $account; ?>");</script>
                    <?php
					}
					?>
                    
                    <select name="event" id="event" onchange="Mo('#find-form').item(0).submit();" ignore="true">
                        <optgroup label="选择事件" />                        
                        <option value="">所有事件</option>
                        <option value="login">登录系统</option>
                        <option value="exit">退出系统</option>
						<?php
						
						/*
                        foreach($Logbook as $key => $value){
                            echo '<option value="'.$key.'">'.$value.'</option>';
                        }
						*/						
						
                        foreach($_CACHE['system']['module']['system']['permit'] as $item => $array){
							echo '<optgroup label="'.$item.'" />';							
							foreach($array as $key => $value){
								echo '<option value="'.$key.'">'.$value.'</option>';
							}
                        }
                        ?>
                    </select>
                    
                    <input name="start" type="text" class="date" id="start" value="<?php echo $start;?>" size="12" readonly="true" title="年月日">
                    -
                    <input name="end" type="text" class="date" id="end" value="<?php echo $end;?>" size="12" readonly="true" title="年月日">
                    <input name="_view" type="button" class='cancel' value="今天" onclick="location.href='?start=<?php echo date("Y-m-d");?>&end=<?php echo date("Y-m-d");?>'">
                </span>
        
                <select name="s" id="s">
                    <option value="ip">IP</option>
                </select>
                <input name="q" type="text" class="text" id="q" value="<?php echo $q; ?>" />
                
                <button class="go" type="submit"></button>
                
            </form>
            <script type="text/javascript">
				Mo("select[name=s]").value("<?php echo $s;?>");
				Mo("#event").value("<?php echo $event; ?>");				
            </script>
        </div>

        <?php
    
        //连接数据库
        System :: connect();
        
        //查询数据库_总记录数
        $row_count = System :: $db -> getCount( $sql );   
    
        $page=getpage("page");
        $page_start=$_G['setting']['global']['pagesize']*($page-1);
        $sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];
        
        //分页链接
        $url="?q=".$q."&s=".$s."&account=$account&event=$event&page=";
        ?>		
    
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
          <tr class="thead">
            <td>ID</td>
            <td>用户</td>
            <td>所作操作</td>
            <td>开始时间</td>
            <td>活动时间</td>					
            <td>停留时间</td>
            <td>距离现在</td>
            <td>IP地址</td>
            <td>地理位置</td>
          </tr>
        <?php
        
        $result = System :: $db -> getAll( $sql );

        foreach( $result as $row ){
            
            $city = convertip($row["ip"]);

			//转 GBK
            if( $_G['product']['charset']=="utf-8" ){
                $city = iconv("gbk",'utf-8',$city);
            }

            echo ("<tr class='".zebra( $i, array( "line" , "band" ) )."'>");
            echo ('<td>'.$row['id'].'</td>');
            echo ('<td><a href="?account='.$row["aid"].'&q='.urlencode($row['account']).'">'.$row['account'].'</a></td>');
            echo ('<td><a href="?event='.$row["event"].'">'.$Logbook[$row["event"]].'</a></td>');
            echo ("<td>".date("Y-m-d H:i:s",$row['dateline'])." <span class='text-key'>".$_G['project']['weeks'][gmdate("w",$row['dateline'])]."</span></td>");
            echo ("<td>".date("Y-m-d H:i:s",$row['modify'])." <span class='text-key'>".$_G['project']['weeks'][gmdate("w",$row['modify'])]."</span></td>");
            echo ("<td>".FormatDateDiff($row['dateline'],$row['modify'])."</td>");	
            echo ("<td>".FormatDateDiff($row['dateline'],time())."前</td>");
            echo ("<td>".hide_ip($row["ip"])."</td>");			
            echo ("<td>".$city."</td>");
            echo ("</tr>");
            
            if($row["description"]){
            	echo ('<tr><td colspan="9" class="choice">'.nl2br($row["description"]).'</td></tr>');
            }
        }
        
        if( count( $result ) == 0 ){
			echo '<tr><td colspan="9" class="notice">没有检索到相关日志记录</td></tr>';
		}

        //关闭数据库
        System :: connect();
        ?>
	</table>
    <?php
  
	//关闭数据库
	System :: connect();
    
    ?>
    
    <div id="saving">
		<?php
            echo multipage($page,$row_count,$_G['setting']['global']['pagesize'],$url,"page");
        ?>
    </div>
    


<?php html_close();?>