<?php
	
	if(!defined('VI_BASE')) {
		ob_clean();
		exit('Access Denied');
	}
	
	/////////////////////////////////
	
	$gid = getnum("gid",0);	
	$oid = getnum("oid",0);	

	//分页链接
	$smarty->assign("gid", $gid );
	$smarty->assign("oid", $oid );
	
	//分页链接
	$smarty->assign("option", $_CACHE[$appid]['option'][$gid][$oid] );


?>