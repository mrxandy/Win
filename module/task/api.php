<?php

//载入全局配置和函数包
require '../../app.php';

///////////////////////////////////////

//载入配置模块
Module :: loader( 'workflow' );
Module :: loader( 'passport' );

$action = getgpc("action");

$callback = getgpc('callback');

///////////////////////////////////////

//文件编码
header('Content-type: text/javascript; charset=utf-8');
	
//开关判断
if( $_G['setting']['workflow']['apply'] == 'off' ){
	 echo $callback.'({"return":"off"});';	
	 exit;
}

//连接数据库
System :: connect();
	
//主动初始用户信息
Passport :: detect( FALSE, FALSE );

if( !$_G['manager']['id'] ){
	echo $callback.'({"return":"applogin","apphost":"'.VI_HOST.'"});';	
	exit;
}
	
//用户组判断
if( in_array( $action, array('push') ) && $_G['setting']['workflow']['usergroup'] ){
	$usergroup = explode( ',', $_G['setting']['workflow']['usergroup'] );
	
	if( !$_G['member']['id'] ){
		echo $callback.'({"return":"userlogin"});';	
		exit;
	}
	
	if( !in_array( $_G['member']['groupid'], $usergroup ) ){
		echo $callback.'({"return":"usergroup","groupid":"'.$_G['member']['groupid'].'"});';	
		exit;
	}
	
}

///////////////////////////////////////

//处理请求
switch ($action){
	
	//设置帖子信纸
	case "push":
	
		//$tid = getnum("tid",0);
		//$lid = getnum("lid",0);
		
		$data = array();
		
		if( $_G['product']['charset'] != "utf-8" ){			
			foreach( $_GET['data'] as $k => $v ){				
				$data[$k] = iconv( 'utf-8', $_G['product']['charset'], $v );
			}			
		}
		
		$sql = "SELECT * FROM `mod:workflow_thread` where tid = '".$data['tid']."' and pid = '".$data['pid']."'";
		$row = System :: $db -> getOne( $sql );
		
		/////////////////////////
		
		//新入过库
		if( !is_array( $row )){
		
			$sql = "insert into `mod:workflow_thread`(tid,pid,first,subject,authorid,author,posttime,dateline,ip,state,aid,account) values('".$data["tid"]."','".$data['pid']."','".$data['first']."','".$data["subject"]."','".$data['authorid']."','".$data['author']."','".$data["posttime"]."',".time().",'".GetIP()."',1,".$_G['manager']['id'].",'".$_G['manager']['account']."');";
					
			System :: $db -> execute($sql);
		
		}
		
		/////////////////////////
		
		//统计今日
		$start = strtotime('today');
		$final = strtotime('tomorrow');
		
		$today = System :: $db -> getOne( "SELECT count( CASE WHEN first=1 THEN 1 ELSE NULL END ) as `threads`,count( CASE WHEN first=0 THEN 1 ELSE NULL END ) as `replys` FROM `mod:workflow_thread` WHERE `state`>0 and `aid`=".$_G['manager']['id']." and dateline BETWEEN ".$start." AND ".$final );
		
		/////////////////////////
		
		//统计本月
		$start = strtotime( date("Y-m-1") );
		$final = strtotime( date("Y-m-t") );
		
		$month = System :: $db -> getOne( "SELECT count( CASE WHEN first=1 THEN 1 ELSE NULL END ) as `threads`,count( CASE WHEN first=0 THEN 1 ELSE NULL END ) as `replys` FROM `mod:workflow_thread` WHERE `state`>0 and `aid`=".$_G['manager']['id']." and dateline BETWEEN ".$start." AND ".$final );
		
		
		//已经入过库
		if( !is_array( $row )){
			
			echo $callback.'({ "return" : "succeed", "today" : '. json_encode( $today ) .', "month" : '. json_encode( $month ) .' });';
		
		}else{
		
			echo $callback.'({ "return" : "existing", "result" : '. json_encode( $row ) .', "today" : '. json_encode( $today ) .', "month" : '. json_encode( $month ) .' });';
		
		}
			
	break;
	
	//查询联系人
	case "query":
	
		/////////////////////////
		
		//统计今日
		$start = strtotime('today');
		$final = strtotime('tomorrow');
		
		$today = System :: $db -> getOne( "SELECT count( CASE WHEN first=1 THEN 1 ELSE NULL END ) as `threads`,count( CASE WHEN first=0 THEN 1 ELSE NULL END ) as `replys` FROM `mod:workflow_thread` WHERE `state`>0 and dateline BETWEEN ".$start." AND ".$final );
		
		var_dump( $today );
		
		var_dump( "SELECT count( CASE WHEN first=1 THEN 1 ELSE NULL END ) as `threads`,count( CASE WHEN first=0 THEN 1 ELSE NULL END ) as `replys` FROM `mod:workflow_thread` WHERE `state`>0 and dateline BETWEEN ".$start." AND ".$final );
		
		/////////////////////////
		
		//统计本月
		$start = strtotime( date("Y-m-1") );
		$final = strtotime( date("Y-m-t") );
		
		$month = System :: $db -> getOne( "SELECT count( CASE WHEN first=1 THEN 1 ELSE NULL END ) as `threads`,count( CASE WHEN first=0 THEN 1 ELSE NULL END ) as `replys` FROM `mod:workflow_thread` WHERE `state`>0 and dateline BETWEEN ".$start." AND ".$final );
		
		var_dump( $month );
		
		var_dump( "SELECT count( CASE WHEN first=1 THEN 1 ELSE NULL END ) as `threads`,count( CASE WHEN first=0 THEN 1 ELSE NULL END ) as `replys` FROM `mod:workflow_thread` WHERE `state`>0 and dateline BETWEEN ".$start." AND ".$final );

	break;
	
}

//关闭数据库
System :: connect();

?>