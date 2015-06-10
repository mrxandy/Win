<?php

$_G['module']['system'] = array();

//模块名称
$_G['module']['system']['name']	=	"系统";

//模块类型
$_G['module']['system']['model']	=	"system";

//模块版本
$_G['module']['system']['version']	=	1.7;

//模块作者
$_G['module']['system']['author']	=	"VeryIDE";

//模块网站
$_G['module']['system']['support']	=	"http://www.veryide.com/";

//模块网站
$_G['module']['system']['describe']	=	"VeryIDE 系统内核";

//模块签名
$_G['module']['system']['signed']	=	"d1180935429ad0f1e978eb62ac11c2a7";

/***************************/

//后台服务脚本
$_G['module']['system']['serve']	=	"serve.js?v=1";

//权限配置		
/*
命名规则：模块名称+操作名称(3位)
*/
$_G['module']['system']['permit'] = array(
				   '用户管理' => array('system-admin-add'=>'添加用户','system-admin-del'=>'删除用户','system-admin-mod'=>'修改用户','system-admin-pwd'=>'修改密码','system-admin-dld'=>'下载通讯录'),
				   '个人资料' => array('system-account-mod'=>'修改资料','system-account-pwd'=>'修改密码','system-account-gid'=>'更改权限'),
				   '用户分组' => array('system-group-add'=>'添加分组','system-group-del'=>'删除分组','system-group-mod'=>'修改分组'),
				   '文件管理' => array('system-upload-add'=>'上传文件','system-upload-del'=>'删除文件','system-upload-pcs'=>'处理文件','system-upload-bak'=>'最近文件'),
				   '系统更新' => array('system-update-add'=>'安装更新','system-update-del'=>'删除更新','system-update-sql'=>'执行更新语句<span class="text-no">（存在安全风险）</span>'),
				   '数据备份' => array('system-backup-add'=>'创建备份','system-backup-del'=>'删除备份','system-backup-exe'=>'恢复备份数据','system-backup-dow'=>'下载备份数据'),
				   '系统模块' => array('system-module-add'=>'安装模块','system-module-del'=>'模块卸载','system-module-ena'=>'启用模块','system-module-dis'=>'禁用模块'),
				   '系统功能' => array('system-cache'=>'缓存清理','system-recycle'=>'直接删除','system-event'=>'检索用户日志'),
				   '配置管理' => array('system-system-set'=>'配置系统','system-module-set'=>'配置模块','system-service-set'=>'配置服务')
				 );
