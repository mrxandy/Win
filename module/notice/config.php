<?php

$_G['module']['notice'] = array();

//模块名称
$_G['module']['notice']['name']	=	"通知";

//模块类型
$_G['module']['notice']['model']	=	"module";

//模块版本
$_G['module']['notice']['version']	=	1.7;

//模块作者
$_G['module']['notice']['author']	=	"VeryIDE";

//模块网站
$_G['module']['notice']['support']	=	"http://www.veryide.com/";

//模块网站
$_G['module']['notice']['describe']	=	"站内系统通知";

//模块签名
$_G['module']['notice']['signed']	=	"5223e099a916b65b54e2d5f7d8652e81";

/***************************/

//表单状态
$_G['module']['notice']['state']	=	array('<span class="text-no">已停用</a>','<span class="text-yes">使用中</a>','<span class="text-no">已过期</a>');

//操作菜单
$_G['module']['notice']['tool']	=	'<a href="javascript:if(confirm(\'确定要启用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(1); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit();}void(0);">启用所选</a> - <a href="javascript:if(confirm(\'确定要停用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(0); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit(); }void(0);">停用所选</a> - <a href="javascript:if(confirm(\'确定要删除所选吗?\')){ Mo(\'#post-form input[name=action]\').value(\'delete\'); Mo(\'#post-form\').submit(); }void(0);">删除所选</a>';

//用户标识
$_G['module']['notice']['sign']	=	array('<span class="text-no">未确认</a>','<span class="text-yes">已确认</a>');

//数据统计
$_G['module']['notice']['statis'] = array(
	'person' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'notice\' AND aid = {aid}',
	'public' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'notice\'',
);

//权限配置
$_G['module']['notice']['permit'] = array(
							  "notice-list-add"=>"添加表单",
							  "notice-list-mod"=>"修改表单",
							  "notice-list-del"=>"删除表单",
							  "notice-cate-add"=>"添加分类",
							  "notice-cate-mod"=>"修改分类",
							  "notice-cate-del"=>"删除分类",
							  "notice-data-mod"=>"修改投票数据",
							  "notice-data-dow"=>"下载用户数据",
							  "notice-data-del"=>"删除用户数据",
							  "notice-other"=>"<span class='text-yes'>包含他人表单</span>"
							);
		
//选项风格
$_G['module']['notice']['style'] = array(
							  "0"=>"",
							  "1"=>"{INPUT} {LABEL}",
							  "2"=>"{INPUT} {LABEL} <span>{DESC}</span>",
							  "3"=>'<a href="{LINK}" target="_blank"><img src="{IMAGE}" alt="{LABEL}" width="{WIDTH}" height="{HEIGHT}" /></a> <br /> {INPUT}',
							  "4"=>'<a href="{LINK}" target="_blank"><img src="{IMAGE}" alt="{LABEL}" width="{WIDTH}" height="{HEIGHT}" /><br /> {LABEL} </a><br /> {INPUT}',
							  "5"=>'<a href="{LINK}" target="_blank"><img src="{IMAGE}" alt="{LABEL}" width="{WIDTH}" height="{HEIGHT}" /><br /> {LABEL} </a><br /> <span>{DESC}</span> <br /> {INPUT}',
							  "6"=>'<a href="{LINK}" target="_blank"><img src="{IMAGE}" alt="{LABEL}" width="{WIDTH}" height="{HEIGHT}" /><br /> {LABEL} </a><br /> <strong>{COUNT}</strong> <br /> {INPUT}'
							);

//选项组类型
$_G['module']['notice']['group']	=	array(
	"radio"=>"单选",
	"button"=>"按钮",
	"checkbox"=>"复选"
);

//模块菜单
$_G['module']['notice']['context'] = array(
	  "新增通知"=>"notice.edit.php",
	  "管理通知"=>"notice.list.php",
	  "新增制度"=>"system.edit.php",
	  "管理制度"=>"system.list.php",
	  "分类添加"=>"cate.edit.php",
	  "分类列表"=>"cate.list.php"
);
