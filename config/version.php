<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	$Id: version.php,v2 11:46 2009-9-10 Lay $
*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);

//会话ID，不能正常会话时使用
//session_id(SID);

//开启会话
session_start();

//开启缓冲
ob_start();

//全局变量
$_G	= array();

//VeryIDE 产品配置
//************************************

	$_G['product'] = array();
	
		//产品名称
		$_G['product']['appname']	= 'Bee';
	
		//产品版本号
		$_G['product']['version']	= '2.4';
		
		//产品流水号
		$_G['product']['build']		= 20140806;
	
		//VeryIDE 编码（可选 gbk utf-8）
		$_G['product']['charset']	= 'utf-8';
		

//VeryIDE 系统配置
//************************************

	$_G['system'] = array();
	
		//相对地址
		$_G['system']['base']	= VI_BASE;
	
		//绝对地址
		$_G['system']['host']	= VI_HOST;
		
		//本地地址
		$_G['system']['root']	= VI_ROOT;

	$_G['project'] = array();
	
		//心跳频率（秒）
		$_G['project']['heartbeat']	=60;
	
		//站点主域名，自动获取值，不用设置
		$_G['project']["domain"]	= $_SERVER['HTTP_HOST'];
	
		//数据库查询次数
		$_G['project']['queries']	= 0 ;
		
		//需要检查目录权限的目录
		$_G['project']['checkin']	= array(
			'directory' => array( 'attach/', 'cache/', 'config/', 'static/', 'data/install/', 'data/update/', 'data/backup/', 'data/fixbug/', 'data/filehash/' ),
			'function' => array( 'mcrypt_module_open' => 'mcrypt', 'imageline' => 'GD', 'gzinflate' => 'Zlib', 'getcwd' => 'getcwd' ),
			'class' => array( 'DOMDocument' => 'XML' )
		);
	
		//附件位置
		$_G['project']['attach']	= array("本地","远程");
		
		//对象归属
		$_G['project']['object'] 	= array('sys'=>'系统','mod'=>'模块','diy'=>'自定义');
		
		//VeryIDE 支持中心
		$_G['project']['home']="http://www.veryide.com/";
		
		//Power By
		$_G['project']['powered'] = 'Powered by <a href="'.$_G['project']['home'].'?domain='.urlencode(VI_HOST).'" target="_blank" title="Powered by VeryIDE">VeryIDE</a>';
	
		//FAQ 链接
		$_G['project']['faq'] = '<a href="'.$_G['project']['home'].'guide.php?product='.$_G['product']['appname'].'&version='.$_G['product']['version'].'" target="_blank">FAQ</a>';
	
		//VeryIDE 产品全称
		$_G['project']['product']='VeryIDE '.$_G['product']['appname'].' '.$_G['product']['version'].' ( '.strtoupper($_G['product']['charset']).' )';
	
		//VeryIDE Email
		$_G['project']['email'] = 'veryide@qq.com';
		
		//数据统计
		$_G['project']['stat'] = array( 'attach' => '上传文件', 'event' => '系统日志', 'admin' => '系统用户', 'group' => '用户分组' );

		//性别信息
		$_G['project']['gender'] = array('女士','男士');

		//婚姻状态
		$_G['project']['marriage'] = array('未婚','已婚');

		//周数组
		$_G['project']['weeks'] = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
	
		//月数组
		$_G['project']['months'] = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月');
	
		//天数组
		$_G['project']['days'] = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	
		//数据状态
		$_G['project']['state'] = array(
			-1   => '<span class="text-no">未审核</span>',
			0    => '<span class="text-no">禁用</span>',
			1    => '<span class="text-yes">启用</span>',
			2    => '<span class="text-key">推荐</span>'
		);
		
		//页面权限
		$_G['project']['page'] = array(
								'system.module.php'=>'系统模块',
								'system.secure.php'=>'系统安全',
								'system.server.php'=>'系统信息',
								'serve.setting.php'=>'系统设置',
								'data.update.php'=>'系统更新',
								'data.tables.php'=>'数据结构',
								'data.backup.php'=>'数据备份',
								'admin.event.php'=>'系统日志',
								'data.attach.php'=>'文件管理',
								'group.edit.php'=>'编辑分组',
								'group.list.php'=>'分组列表',
								'admin.edit.php'=>'编辑用户',
								'admin.list.php'=>'用户列表'
								);
		
		//推荐链接
		$_G['project']['menu'] = array(
								'http://www.veryide.com/'=>'VeryIDE - 官方帮助支持中心',
								'http://www.deyi.la/'=>'等待，只为与你相遇 - 得意.la',
								'http://www.veryide.com/misc.php?action=service'=>'VeryIDE - 终端媒体广告投放',
								'http://www.veryide.com/misc.php?action=vps'=>'值得信赖的专业 VPS 提供商'
								);


//系统版本配置
//******************************

	$_G['version'] = array();

		//演示版
		$_G['version']['demo'] = array('name'=>'演示版','desc'=>'提供主要功能使用，主要用于本地测试。');
		
		//免费版
		$_G['version']['free'] = array('name'=>'免费版','desc'=>'拥有基本核心功能，免费提供给大家使用。');
		
		//商业版
		$_G['version']['full'] = array('name'=>'商业版','desc'=>'商业授权增强版，需要付费购买后才能使用。');					
		
		//特别版
		$_G['version']['only'] = array('name'=>'特别版','desc'=>'在商业版基础上定制了部分功能，需要付费。');	
						

//关键字词过滤
//******************************

	$_G['censor'] = array();

		//过滤动作
		$_G['censor']['replace'] = array('{BANNED}'=>'禁止关键词','{MOD}'=>'审核关键词','{REPLACE}'=>'替换关键词');
		
		//词语分类
		$_G['censor']['wordtype'] = array('0'=>'默认分类','1'=>'政治','2'=>'广告','3'=>'垃圾信息','4'=>'竞争对手');			
		

//上传文件类型
//******************************

	$_G['upload'] = array();
		$_G['upload']['image'] = array('jpg','jpeg','gif','png','bmp');
		$_G['upload']['media'] = array('mpg','mpeg','dat','avi','mp3','mp4','rm','rmvb','wmv','asf','vob','wma','wav','mid','mov','qt','3gp');
		$_G['upload']['flash'] = array('swf','flv','f4v');
		$_G['upload']['files'] = array('pdf','txt','zip','apk','gz','rar','tar','7z','iso','doc','docx','xls','xlsx','ppt','pps','pptx','csv','wps','torrent','fla');


//输出头文件
//******************************
header('Content-Type:text/html; charset='.$_G['product']['charset']);
