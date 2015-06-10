<?php

$_G['module']['workflow'] = array();

//模块名称
$_G['module']['workflow']['name']	=	"协作";

//模块类型
$_G['module']['workflow']['model']	=	"module";

//模块版本
$_G['module']['workflow']['version']	=	1.2;

//模块作者
$_G['module']['workflow']['author']	=	"VeryIDE";

//模块网站
$_G['module']['workflow']['support']	=	"http://www.veryide.com/";

//模块网站
$_G['module']['workflow']['describe']	=	"轻松工作，轻松生活！";

//模块签名
$_G['module']['workflow']['signed']	=	"553cb096730c4bd8e2ac105bdd9f68a5";

/***********高级配置************/

//用户权限
$_G['module']['workflow']['permit'] = array(
							  "workflow-thread-mod"=>"修改帖子",
							  "workflow-thread-del"=>"删除帖子",
							  "workflow-project-exa"=>"审核项目",
							  "workflow-project-mod"=>"修改项目",
							  "workflow-project-del"=>"删除项目",
							  "workflow-salary-chk"=>"管理考勤",
							  "workflow-salary-mod"=>"修改工资",
							  "workflow-salary-del"=>"删除工资",
							  "workflow-stuff-add"=>"新增物品",
							  "workflow-stuff-mod"=>"修改物品",
							  "workflow-stuff-del"=>"删除物品",
							  "workflow-merchant-add"=>"新增商户",
							  "workflow-merchant-mod"=>"修改商户",
							  "workflow-merchant-del"=>"删除商户"
							);

//操作菜单
$_G['module']['workflow']['tool']	=	'<a href="javascript:if(confirm(\'确定要启用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(1); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit();}void(0);">启用所选</a> / <a href="javascript:if(confirm(\'确定要停用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(0); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit(); }void(0);">停用所选</a> - <a href="javascript:if(confirm(\'确定要启用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(1); Mo(\'#post-form input[name=action]\').value(\'digest\'); Mo(\'#post-form\').submit();}void(0);">选为精品</a> / <a href="javascript:if(confirm(\'确定要停用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(0); Mo(\'#post-form input[name=action]\').value(\'digest\'); Mo(\'#post-form\').submit(); }void(0);">取消精品</a> - <a href="javascript:if(confirm(\'确定要删除所选吗?\')){ Mo(\'#post-form input[name=action]\').value(\'delete\'); Mo(\'#post-form\').submit(); }void(0);">删除所选</a>';

// - <a href="javascript:if(confirm(\'确定要启用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(1); Mo(\'#post-form input[name=action]\').value(\'credit\'); Mo(\'#post-form\').submit();}void(0);">选为奖励</a> / <a href="javascript:if(confirm(\'确定要停用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(0); Mo(\'#post-form input[name=action]\').value(\'credit\'); Mo(\'#post-form\').submit(); }void(0);">取消奖励</a>


/***********订单信息************/

//付款状态
$_G['module']['workflow']['state']	=	array( '<span class="text-no">无效<span>', '<span class="text-key">有效</span>' );

//精品状态
$_G['module']['workflow']['digest']	=	array('无','<span class="text-key">精品</span>');

//奖励状态
$_G['module']['workflow']['credit']	=	array('无','<span class="text-key">奖励</span>');

//奖励状态
$_G['module']['workflow']['merchant']	=	array('普通','<span class="text-yes">潜力</span>','<b class="text-key">重要</b>','<b class="text-no">非常重要</b>');

//奖励状态
$_G['module']['workflow']['level']	=	array('一般','<span class="text-yes">优先</span>','<b class="text-key">紧急</b>','<b class="text-no">非常紧急</b>');

//奖励状态
$_G['module']['workflow']['product']	=	array('qrcode'=>'二维码','machine'=>'广告机','video'=>'影音');

//奖励状态
$_G['module']['workflow']['project']	=	array( -1 => '无效', 0 => '<span class="text-yes">申请中</span>', 1=>'<b class="text-key">进行中</b>',2=>'<b class="text-no">已结束</b>' );

//奖励状态
$_G['module']['workflow']['asset']	=	array( -1 => '废弃', 0 => '<span class="text-yes">闲置</span>', 1=>'<b class="text-key">占用</b>' );

							
//模块菜单
$_G['module']['workflow']['context'] = array(
	'帖子管理' => array(
	'link' => 'thread.list.php',
	'menu' => array(
		'帖子列表' => 'thread.list.php',
		'统计报表' => 'thread.stat.php'
	),
	),
	'项目管理' => array(
		'link' => 'project.list.php',
		'menu' => array(
			'申请项目' => 'project.edit.php',
			'项目列表' => 'project.list.php'
		),
	),
	'考勤管理' => array(
		'link' => 'daily.list.php',
		'menu' => array(
			'新增考勤' => 'daily.edit.php',
			'考勤列表' => 'daily.list.php'
		),
	),
	'物品管理' => array(
		'link' => 'stuff.list.php',
		'menu' => array(
			'新增物品' => 'stuff.edit.php',
			'物品列表' => 'stuff.list.php'
		),
	),
	'固定资产' => array(
		'link' => 'asset.list.php',
		'menu' => array(
			'新增资产' => 'asset.edit.php',
			'资产列表' => 'asset.list.php'
		),
	),
	'工资结算' => array(
		'link' => 'salary.list.php',
		'menu' => array(
			'工资统计' => 'salary.list.php',
			'工资设定' => 'salary.sets.php'
		),
	),
	'商户管理' => array(
		'link' => 'merchant.list.php',
		'menu' => array(
			'新增客户' => 'merchant.edit.php',
			'客户列表' => 'merchant.list.php'
		),
	),
	'日志管理' => array(
		'link' => 'journal.list.php',
		'menu' => array(
			'新增日志' => 'journal.edit.php',
			'日志列表' => 'journal.list.php'
		),
	)	

);
