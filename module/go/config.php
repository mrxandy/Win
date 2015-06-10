<?php

$_G['module']['go'] = array();

//模块名称
$_G['module']['go']['name']	=	"统计";

//模块类型
$_G['module']['go']['model']	=	"module";

//模块版本
$_G['module']['go']['version']	=	1.7;

//模块作者
$_G['module']['go']['author']	=	"VeryIDE";

//模块网站
$_G['module']['go']['support']	=	"http://www.veryide.com/";

//模块网站
$_G['module']['go']['describe']	=	"想知道一个活动页有多少点击？给他添加一个计数器吧";

//模块签名
$_G['module']['go']['signed']	=	"6227b69401733ebbd22468703af2de23";

/***************************/

//数据统计
$_G['module']['go']['statis'] = array(
	'person' => 'SELECT COUNT(*) FROM `mod:go_list` WHERE aid = {aid}',
	'public' => 'SELECT COUNT(*) FROM `mod:go_list`',
);

//权限配置
$_G['module']['go']['permit'] = array(
							  "go-add"=>"添加统计",
							  "go-mod"=>"修改统计",
							  "go-del"=>"删除统计"
							);

//数据类型
$_G['module']['go']['method']	=	array(
	"click"=>"点击",
	"view"=>"展示"
);
							
//模块菜单
$_G['module']['go']['context'] = array(
	  "新增统计"=>"go.edit.php",
	  "管理统计"=>"go.list.php",
	  "号码统计"=>"go.number.php"
);