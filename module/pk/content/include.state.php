<?php
	
	if(!defined('VI_BASE')) {
		ob_clean();
		exit('Access Denied');
	}
	
	/////////////////////////////////
	
	require VI_ROOT.'module/member/config.php';
		
	$sql="SELECT * FROM `mod:form_data` WHERE fid=".$fid." ";
	
    //排序
    $sql.=" ORDER BY id DESC";	
    
    //查询数据库_总记录数
    $row_count = System :: $db -> getCount( $sql );

    //分页参数
    $page=getpage("page");
    $page_start=$_G['setting']['global']['pagesize']*($page-1);
    $sql=$sql." limit $page_start,".$_G['setting']['global']['pagesize'];	
    
    //分页链接
    $url="?action=state&id=".$fid."&page=";
	
	$result = System :: $db->getAll( $sql );
	
	$tr = '';	

	//查询结果
	$smarty->assign("sign", $_G['module']['member']["sign"] );
	
	//查询结果
	$smarty->assign("result", $result );
	
	//分页链接
	$smarty->assign("multi", multipage($page,$row_count,$_G['setting']['global']['pagesize'],$url,"pp-page") );


?>