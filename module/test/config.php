<?php

$_G['module']['test'] = array();

//模块名称
$_G['module']['test']['name']	=	"试题";

//模块类型
$_G['module']['test']['model']	=	"module";

//模块版本
$_G['module']['test']['version']	=	1.7;

//模块作者
$_G['module']['test']['author']	=	"VeryIDE";

//模块网站
$_G['module']['test']['support']	=	"http://www.veryide.com/";

//模块网站
$_G['module']['test']['describe']	=	"任务";

//模块签名
$_G['module']['test']['signed']	=	"8ad8757baa8564dc136c1e07507f4a98";

/***************************/

//表单状态
$_G['module']['test']['state']	=	array('<span class="text-no">已停用</a>','<span class="text-yes">使用中</a>','<span class="text-no">已过期</a>');

//操作菜单
$_G['module']['test']['tool']	=	'<a href="javascript:if(confirm(\'确定要启用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(1); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit();}void(0);">启用所选</a> - <a href="javascript:if(confirm(\'确定要停用所选吗?\')){ Mo(\'#post-form input[name=state]\').value(0); Mo(\'#post-form input[name=action]\').value(\'state\'); Mo(\'#post-form\').submit(); }void(0);">停用所选</a> - <a href="javascript:if(confirm(\'确定要删除所选吗?\')){ Mo(\'#post-form input[name=action]\').value(\'delete\'); Mo(\'#post-form\').submit(); }void(0);">删除所选</a>';

//用户标识
$_G['module']['test']['sign']	=	array('<span class="text-no">未确认</a>','<span class="text-yes">已确认</a>');

//用户标识
$_G['module']['test']['select']	=	array('<span class="text-no">错误</a>','<span class="text-yes">正确</a>');

//表单模式
$_G['module']['test']['mode']	=	array('FORM'=>'表单','STEP'=>'渐进');

//用户数据扩展
$_G['module']['test']['value']	=	array('RESULT'=>'结果','SCORES'=>'得分');

//数据统计
$_G['module']['test']['statis'] = array(
	'person' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'test\' AND aid = {aid}',
	'public' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'test\'',
);

//权限配置
$_G['module']['test']['permit'] = array(
							  "test-list-add"=>"添加表单",
							  "test-list-mod"=>"修改表单",
							  "test-list-del"=>"删除表单",
							  "test-data-dow"=>"下载用户数据",
							  "test-data-del"=>"删除用户数据",
							  "test-other"=>"<span class='text-yes'>包含他人表单</span>"
							);

//选项组类型
$_G['module']['test']['group']	=	array(
	"radio"=>"单选",
	"checkbox"=>"复选",
	"select"=>"下拉选框",
	"compart"=>"分隔线"
);
	
//模块菜单
$_G['module']['test']['context'] = array(
	  "新增试题"=>"test.edit.php",
	  "管理试题"=>"test.list.php"
);
