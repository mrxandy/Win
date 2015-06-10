<?php
/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	$Id: veryide.action.php,v2 19:47 2008-10-08 Lay $
*/


//载入全局配置和函数包
require '../../../app.php';

//require("../veryide/module/pk/config.php");

//系统动作配置，修改时请小心
header('Content-type: text/xml');

echo ("<?xml version='1.0' encoding='".$_G['product']['charset']."'?>");
echo '<response>';
	
$action = getgpc('action');
$fid = getnum('fid',0);

echo '<action>'.$action.'</action>';

//连接数据库
System :: connect();

switch ($action){	

	//评论分页_Ajax
	case "message":
	
		$object = getgpc("object");
		//$object=($object=="posi-box"?1:2);
		
		if(!$object || !$fid){
			echo '<result>false</result>';
		}else{
	
			//读取配置
			//reader_setting();	
	
			//查询数据库
			$sql="SELECT * FROM `mod:form_data` WHERE fid='".$fid."' and config like '%\"OBJECT\":\"".$object."\"%' ORDER BY id DESC";
			
			//查询数据库_总记录数
			$row_count = $result = System :: $db -> getCount( $sql );
			
			if( $row_count ==0 ){
			
				echo ("<result>zero</result>");

			}else{
			
				//分页参数
				$page=getpage("page");
				$page_start=$_G['setting']['global']['pagesize']*($page-1);
				$sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];
				
				//echo $sql;
				$result = System :: $db -> getAll( $sql );
					
				echo ('<result rowcount="'.$row_count.'" pagecount="'.ceil($row_count/$_G['setting']['global']['pagesize']).'" pagecurrent="'.$page.'" pagesize="'.$_G['setting']['global']['pagesize'].'">true</result>');
				
				//查询数据
				foreach( $result as $row ){
					
					$config = fix_json($row['config']);
	
					echo "<message>";
						echo "<username>".( $row['account'] ? $row['account'] : $config["ACCOUNT"] )."</username>";
						echo "<dateline>".date("y年m月d日 H:i:s",$row['dateline'])."</dateline>";
						echo "<content><![CDATA[".str_replace(chr(13),'<br />',ClearHtml($config["MESSAGE"]))."]]></content>";
					echo "</message>";
				}
			}

		}

		break;

}

//关闭数据库
System :: connect();
	
echo '</response>';
?>