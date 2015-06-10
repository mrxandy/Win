<?php

$_G['module']['vote'] = array();

//模块名称
$_G['module']['vote']['name']	=	"投票";

//模块类型
$_G['module']['vote']['model']	=	"module";

//模块版本
$_G['module']['vote']['version']	=	1.7;

//模块作者
$_G['module']['vote']['author']	=	"VeryIDE";

//模块网站
$_G['module']['vote']['support']	=	"http://www.veryide.com/";

//模块网站
$_G['module']['vote']['describe']	=	"图片投票十分钟搞定";

//模块签名
$_G['module']['vote']['signed']	=	"5223e099a916b65b54e2d5f7d8652e81";

/***************************/

//表单状态
$_G['module']['vote']['state']	=	array('<span class="text-no">已停用</a>','<span class="text-yes">使用中</a>','<span class="text-no">已过期</a>');

//操作菜单
$_G['module']['vote']['tool']	=	'<a href="javascript:if(confirm(\'确定要启用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(1); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit();}void(0);">启用所选</a> - <a href="javascript:if(confirm(\'确定要停用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(0); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit(); }void(0);">停用所选</a> - <a href="javascript:if(confirm(\'确定要删除所选吗?\')){ Mo(\'#post-form input[name=action]\').value(\'delete\'); Mo(\'#post-form\').submit(); }void(0);">删除所选</a>';

//用户标识
$_G['module']['vote']['sign']	=	array('<span class="text-no">未确认</a>','<span class="text-yes">已确认</a>');

//数据统计
$_G['module']['vote']['statis'] = array(
	'person' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'vote\' AND aid = {aid}',
	'public' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'vote\'',
);

//权限配置
$_G['module']['vote']['permit'] = array(
							  "vote-list-add"=>"添加表单",
							  "vote-list-mod"=>"修改表单",
							  "vote-list-del"=>"删除表单",
							  "vote-data-mod"=>"修改投票数据",
							  "vote-data-dow"=>"下载用户数据",
							  "vote-data-del"=>"删除用户数据",
							  "vote-other"=>"<span class='text-yes'>包含他人表单</span>"
							);
		
//选项风格
$_G['module']['vote']['style'] = array(
							  "0"=>"",
							  "1"=>"{INPUT} {LABEL}",
							  "2"=>"{INPUT} {LABEL} <span>{DESC}</span>",
							  "3"=>'<a href="{LINK}" target="_blank"><img src="{IMAGE}" alt="{LABEL}" width="{WIDTH}" height="{HEIGHT}" /></a> <br /> {INPUT}',
							  "4"=>'<a href="{LINK}" target="_blank"><img src="{IMAGE}" alt="{LABEL}" width="{WIDTH}" height="{HEIGHT}" /><br /> {LABEL} </a><br /> {INPUT}',
							  "5"=>'<a href="{LINK}" target="_blank"><img src="{IMAGE}" alt="{LABEL}" width="{WIDTH}" height="{HEIGHT}" /><br /> {LABEL} </a><br /> <span>{DESC}</span> <br /> {INPUT}',
							  "6"=>'<a href="{LINK}" target="_blank"><img src="{IMAGE}" alt="{LABEL}" width="{WIDTH}" height="{HEIGHT}" /><br /> {LABEL} </a><br /> <strong>{COUNT}</strong> <br /> {INPUT}'
							);

//选项组类型
$_G['module']['vote']['group']	=	array(
	"radio"=>"单选",
	"button"=>"按钮",
	"checkbox"=>"复选"
);

//模块菜单
$_G['module']['vote']['context'] = array(
	  "新增投票"=>"vote.edit.php",
	  "管理投票"=>"vote.list.php"
);
