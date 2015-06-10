<?php

$_G['module']['pk'] = array();

//模块名称
$_G['module']['pk']['name']	=	"辩论";

//模块类型
$_G['module']['pk']['model']	=	"module";

//模块版本
$_G['module']['pk']['version']	=	1.7;

//模块作者
$_G['module']['pk']['author']	=	"VeryIDE";

//模块网站
$_G['module']['pk']['support']	=	"http://www.veryide.com/";

//模块网站
$_G['module']['pk']['describe']	=	"正方还是反方，大家来PK一下吧";

//模块签名
$_G['module']['pk']['signed']	=	"7ed2bd6381d7e0f34f0ba3d383657a37";

/***************************/

//表单状态
$_G['module']['pk']['state']	=	array('<span class="text-no">已停用</a>','<span class="text-yes">使用中</a>','<span class="text-no">已过期</a>');

//操作菜单
$_G['module']['pk']['tool']	=	'<a href="javascript:if(confirm(\'确定要启用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(1); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit();}void(0);">启用所选</a> - <a href="javascript:if(confirm(\'确定要停用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(0); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit(); }void(0);">停用所选</a> - <a href="javascript:if(confirm(\'确定要删除所选吗?\')){ Mo(\'#post-form input[name=action]\').value(\'delete\'); Mo(\'#post-form\').submit(); }void(0);">删除所选</a>';

//用户标识
$_G['module']['pk']['sign']	=	array('<span class="text-no">未确认</a>','<span class="text-yes">已确认</a>');

//正反方颜色
$_G['module']['pk']['color']	=	array('','blue','green');

//评论每页数量
$_G['module']['pk']['pagesize']	=	5;

//用户数据扩展
$_G['module']['pk']['value']	=	array('OBJECT'=>'支持方','MESSAGE'=>'评论内容');

//数据统计
$_G['module']['pk']['statis'] = array(
	'person' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'pk\' AND aid = {aid}',
	'public' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'pk\'',
);

//权限配置
$_G['module']['pk']['permit'] = array(
							  "pk-list-add"=>"添加表单",
							  "pk-list-mod"=>"修改表单",
							  "pk-list-del"=>"删除表单",
							  "pk-data-dow"=>"下载用户数据",
							  "pk-data-del"=>"删除用户数据",
							  "pk-other"=>"<span class='text-yes'>包含他人表单</span>"
							);
							
//模块菜单
$_G['module']['pk']['context'] = array(
	  "新增擂台"=>"pk.edit.php",
	  "管理擂台"=>"pk.list.php"
);
