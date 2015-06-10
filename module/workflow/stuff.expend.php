<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("表单选项组 - VeryIDE");
?>


	<?php

	$appid = Module :: get_appid();

	//module
	require 'function.php';
	require 'config.php';

	//连接数据库
	System :: connect();

	$do = getgpc('do');
	$action = getgpc('action');
	$sid = getnum('sid',0);
	$eid = getnum('eid',0);
	
	//批处理列表
	$list = getgpc('list');
	$list = is_array($list) ? implode(",", $list) : $list;
	
	//上一页
	$jump = $_POST["jump"];
	
	if ($action){

		$quantity = getgpc('quantity');
		$summary = getgpc('summary');
		$quote = getgpc('quote');
		$account = getnum('account',0);
	
		switch($action){
				
			case "add":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );		
				
				$sql="INSERT INTO `mod:workflow_expend`(aid,account,sid,quantity,summary,quote,dateline) values(".$account.",'".$_CACHE['system']['admin'][$account]['account']."',".$sid.",".$quantity.",'".$summary."','".$quote."',".time().")";
			
				System :: $db -> execute($sql);
				
			break;
				
			case "update":
			
				//检查权限
				System :: check_func( 'workflow-stuff-mod', false );		
			
				$sql="UPDATE `mod:workflow_expend` SET quantity=".$quantity.",summary='".$summary."',quote='".$quote."',aid='".$account."',account='".$_CACHE['system']['admin'][$account]['account']."' WHERE id=".$eid;
				System :: $db -> execute($sql);
				
			break;
				
			case "edit":
						
				$sql="SELECT *  FROM `mod:workflow_expend` WHERE id=".$eid;				
				$row = System :: $db -> getOne( $sql );
				
			break;
				
			case "delete":
			
				//检查权限
				System :: check_func( 'workflow-stuff-del', false );		
			
				System :: $db -> execute("DELETE FROM `mod:workflow_expend` WHERE id in(".$list.")");
			
			break;
		}
		
		
		if( $action != "edit" ){
			
			//更新剩余数量
			$count = (int) System :: $db -> getValue( "SELECT sum(quantity) FROM `mod:workflow_expend` WHERE sid=".$sid );
			
			$sql="UPDATE `mod:workflow_stuff` SET surplus = quantity - $count WHERE id=".$sid;
			
			System :: $db -> execute( $sql );
			
			System :: redirect("?do=list&sid=".$sid,"操作处理成功!");
			
		}
		
	}
	  
	//加载缓存
	$sql="SELECT * FROM `mod:workflow_stuff` WHERE id=".$sid;
	$stuff = System :: $db -> getOne( $sql );
	?>
	

	<?php
	
	echo '
    <div class="item">
        <strong>'.$stuff["name"].'</strong>
		<span class="y">		
			总数量：<strong class="text-key">'.$stuff["quantity"].'</strong>
			剩余数量：<strong class="text-yes">'.$stuff["surplus"].'</strong>
		</span>
    </div>
    
    <ul id="naver">
        <li'.($do=='new'?' class="active"':'').'><a href="?do=new&sid='.$sid.'">'.($action=="edit"?'编辑':'新增').'记录</a></li>
        <li'.($do=='list'?' class="active"':'').'><a href="?do=list&sid='.$sid.'">管理记录</a></li>
    </ul>
	';
	?>
    
    <?php 
	if($do=="new"){
	?>
    
	<?php    
    echo loader_script(array(VI_BASE."static/js/mo.ubb.js"),'utf-8',$_G['product']['version']);
	echo loader_script(array(VI_BASE.'source/editor/kindeditor.js',VI_BASE.'source/editor/lang/zh_CN.js'),'utf-8',$_G['product']['version']);
    ?>
	
	<div id="box">

	<form action="?jump=<?php echo rawurlencode( $jump );?>" method="post" name="edit-form" data-valid="true">
		<input name="sid" type="hidden" value="<?php echo $sid;?>" />
        
        	<table cellpadding="0" cellspacing="0" class="form">
        
        	<tr>
            	<th>领用人员：</th>
                <td>
                <select name="account" onchange="Mo(this.form).submit();" ignore="true">
                    <optgroup label="所有者" />
                    <?php
                    foreach( $_CACHE['system']['admin'] as $i => $a ){
                        echo '<option value="'.$i.'">'.$a['account'].'</option>';
                    }
                    ?>
                </select>
                </td>                
            </tr>
        
        	<tr>
            	<th>领用数量：</th>
                <td>
                    <input name="quantity" type="text" value="<?php echo $row['quantity'];?>" size="20" data-valid-name="领用数量" data-valid-number="yes" />
                    当前剩余：<?php echo $stuff["surplus"];?>
                </td>
                
            </tr>
            
        	<tr>
            	<th>备注信息：</th>
                <td>
                	<textarea name="summary" cols="80" rows="4"><?php echo $row['summary'];?></textarea>
                </td>
                
            </tr>
            
            <tr>
                <th>引用地址：</th>
                <td>
                    <input name="quote" type="text" class="text" id="quote" value="<?php echo $row["quote"];?>" size="60" />
                </td>
            </tr>
            
        	<tr>
            	<td></td>
                <td>
                
					<?php
                    if ($action=="edit"){
                        echo '
                        <input name="action" type="hidden" id="action" value="update" />
                        <input name="eid" type="hidden" value="'.$row['id'].'" />
                        ';
                        echo '<input type="submit" name="submit" value="修改此项目" class="submit" />';
                    }else{
                    
                        echo '
                        <input name="action" type="hidden" id="action" value="add" />
                        ';
                        if ($sid){
                            echo '<input type="submit" name="submit" value="新增此项目" class="submit" />';
                        }
                    }
                    ?>
                </td>
                
            </tr>
        
        </table>
          
	</form>

	</div>
    
    <script type="text/javascript">
	Mo("select[name=account]").value("<?php echo isset($row['aid']) ? $row['aid'] : $_G['manager']['id'];?>");
	</script>
	
    <?php
	}elseif($do=="list"){
	?>
    
	<!--用于动作处理_开始-->
	<form name="post-form" id="post-form" method="post">
		<input name="action" id="action" type="hidden" value="" />        
		<input name="state" id="state" type="hidden" value="" />

		<input name="jump" type="hidden" value="" />
		<script>Mo('#post-form input[name=jump]').value( location.href );</script>
	
      <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table" id="table">
	  
		<?php
		if( count($result)> $_G['setting']['global']['pagesize']/2 ){
		?>
		<tr class="thead">
			<td colspan="9" class="first">
				<?php echo $_G['module']['form']['tool'];?>
			</td>
		</tr>
		<?php
		}
		?>
		
        <tr class="thead">
            <td>ID</td>
            <td>领用人员</td>
            <td>领用数量</td>
            <td>备注信息</td>
            <td>领用时间</td>
            <td width="80">操作</td>
        </tr>
        
        <?php
        $sql="SELECT * FROM `mod:workflow_expend` WHERE sid=".$sid." ORDER BY id ASC";
        
		$result = System :: $db -> getAll( $sql );

        foreach( $result as $row ){
            
            ?>
            <tr class="<?php echo zebra( $i, array( "line" , "band" ) );?>" data-mark="<?php echo $row['id'];?>" data-edit='?do=new&action=edit&sid=<?php echo $sid;?>&eid=<?php echo $row['id'];?>&jump={self}'>
                <td><?php echo $row['id'];?></td>
                <td><strong class="text-yes"><?php echo $row['account'];?></strong></td>
                <td><?php echo $row["quantity"];?></td>
                <td><?php echo $row["summary"];?></td>
                <td><?php echo date("Y-m-d H:i:s",$row['dateline']);?></td>
                <td>
                <button type="button" class="editor" data-url="?do=new&action=edit&sid=<?php echo $row["sid"];?>&eid=<?php echo $row['id'];?>">修改</button>
                    <button type="button" class="normal" data-url="?do=list&action=delete&sid=<?php echo $row["sid"];?>&list=<?php echo $row['id'];?>">删除</button>
                </td>
            </tr>
            <?php
        
        }
		
		if( count( $result ) == 0 ){
			echo '<tr><td colspan="6" class="notice">没有检索到相关记录，<a href="?do=new&sid='.$sid.'">创建一个？</a></td></tr>';
		}
	  
		?>
	</table>
    
    <?php 
	}
	
        //关闭数据库
        System :: connect();
	
	?>
    </form>
	<!--用于动作处理_结束-->
    


<?php html_close();?>