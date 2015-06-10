<?php /*2015-06-30 13:47:26*/ $_CACHE['system']['module']=array (
  'analytic' => 
  array (
    'name' => '分析',
    'appid' => 'analytic',
    'model' => 'service',
    'signed' => 'eab73f18bd03859893a349d0859aec53',
    'version' => 1.7,
    'serve' => NULL,
    'hooks' => NULL,
    'index' => 'http://oa.cc/module/analytic/content/',
    'domain' => NULL,
    'statis' => NULL,
    'permit' => NULL,
    'author' => 'VeryIDE',
    'support' => 'http://www.veryide.com/',
    'context' => NULL,
    'writable' => NULL,
    'describe' => '系统内置数据统计与分析中心',
    'external' => NULL,
    'state' => true,
  ),
  'go' => 
  array (
    'name' => '统计',
    'appid' => 'go',
    'model' => 'module',
    'signed' => '6227b69401733ebbd22468703af2de23',
    'version' => 1.7,
    'serve' => NULL,
    'hooks' => NULL,
    'index' => 'http://oa.cc/module/go/content/',
    'domain' => NULL,
    'statis' => 
    array (
      'person' => 'SELECT COUNT(*) FROM `mod:go_list` WHERE aid = {aid}',
      'public' => 'SELECT COUNT(*) FROM `mod:go_list`',
    ),
    'permit' => 
    array (
      'go-add' => '添加统计',
      'go-mod' => '修改统计',
      'go-del' => '删除统计',
    ),
    'author' => 'VeryIDE',
    'support' => 'http://www.veryide.com/',
    'context' => 
    array (
      '新增统计' => 'go.edit.php',
      '管理统计' => 'go.list.php',
      '号码统计' => 'go.number.php',
    ),
    'writable' => NULL,
    'describe' => '想知道一个活动页有多少点击？给他添加一个计数器吧',
    'external' => NULL,
    'state' => true,
  ),
  'notice' => 
  array (
    'name' => '通知',
    'appid' => 'notice',
    'model' => 'module',
    'signed' => '5223e099a916b65b54e2d5f7d8652e81',
    'version' => 1.7,
    'serve' => NULL,
    'hooks' => NULL,
    'index' => 'http://oa.cc/module/notice/content/',
    'domain' => NULL,
    'statis' => 
    array (
      'person' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'notice\' AND aid = {aid}',
      'public' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'notice\'',
    ),
    'permit' => 
    array (
      'notice-list-add' => '添加表单',
      'notice-list-mod' => '修改表单',
      'notice-list-del' => '删除表单',
      'notice-cate-add' => '添加分类',
      'notice-cate-mod' => '修改分类',
      'notice-cate-del' => '删除分类',
      'notice-data-mod' => '修改投票数据',
      'notice-data-dow' => '下载用户数据',
      'notice-data-del' => '删除用户数据',
      'notice-other' => '<span class=\'text-yes\'>包含他人表单</span>',
    ),
    'author' => 'VeryIDE',
    'support' => 'http://www.veryide.com/',
    'context' => 
    array (
      '新增通知' => 'notice.edit.php',
      '管理通知' => 'notice.list.php',
      '分类添加' => 'cate.edit.php',
      '分类列表' => 'cate.list.php',
    ),
    'writable' => NULL,
    'describe' => '站内系统通知',
    'external' => NULL,
    'state' => true,
  ),
  'pk' => 
  array (
    'name' => '辩论',
    'appid' => 'pk',
    'model' => 'module',
    'signed' => '7ed2bd6381d7e0f34f0ba3d383657a37',
    'version' => 1.7,
    'serve' => NULL,
    'hooks' => NULL,
    'index' => 'http://oa.cc/module/pk/content/',
    'domain' => NULL,
    'statis' => 
    array (
      'person' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'pk\' AND aid = {aid}',
      'public' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'pk\'',
    ),
    'permit' => 
    array (
      'pk-list-add' => '添加表单',
      'pk-list-mod' => '修改表单',
      'pk-list-del' => '删除表单',
      'pk-data-dow' => '下载用户数据',
      'pk-data-del' => '删除用户数据',
      'pk-other' => '<span class=\'text-yes\'>包含他人表单</span>',
    ),
    'author' => 'VeryIDE',
    'support' => 'http://www.veryide.com/',
    'context' => 
    array (
      '新增擂台' => 'pk.edit.php',
      '管理擂台' => 'pk.list.php',
    ),
    'writable' => NULL,
    'describe' => '正方还是反方，大家来PK一下吧',
    'external' => NULL,
    'state' => true,
  ),
  'system' => 
  array (
    'name' => '系统',
    'appid' => 'system',
    'model' => 'system',
    'signed' => 'd1180935429ad0f1e978eb62ac11c2a7',
    'version' => 1.7,
    'serve' => 'serve.js?v=1',
    'hooks' => NULL,
    'index' => NULL,
    'domain' => NULL,
    'statis' => NULL,
    'permit' => 
    array (
      '用户管理' => 
      array (
        'system-admin-add' => '添加用户',
        'system-admin-del' => '删除用户',
        'system-admin-mod' => '修改用户',
        'system-admin-pwd' => '修改密码',
        'system-admin-dld' => '下载通讯录',
      ),
      '个人资料' => 
      array (
        'system-account-mod' => '修改资料',
        'system-account-pwd' => '修改密码',
        'system-account-gid' => '更改权限',
      ),
      '用户分组' => 
      array (
        'system-group-add' => '添加分组',
        'system-group-del' => '删除分组',
        'system-group-mod' => '修改分组',
      ),
      '文件管理' => 
      array (
        'system-upload-add' => '上传文件',
        'system-upload-del' => '删除文件',
        'system-upload-pcs' => '处理文件',
        'system-upload-bak' => '最近文件',
      ),
      '系统更新' => 
      array (
        'system-update-add' => '安装更新',
        'system-update-del' => '删除更新',
        'system-update-sql' => '执行更新语句<span class="text-no">（存在安全风险）</span>',
      ),
      '数据备份' => 
      array (
        'system-backup-add' => '创建备份',
        'system-backup-del' => '删除备份',
        'system-backup-exe' => '恢复备份数据',
        'system-backup-dow' => '下载备份数据',
      ),
      '系统模块' => 
      array (
        'system-module-add' => '安装模块',
        'system-module-del' => '模块卸载',
        'system-module-ena' => '启用模块',
        'system-module-dis' => '禁用模块',
      ),
      '系统功能' => 
      array (
        'system-cache' => '缓存清理',
        'system-recycle' => '直接删除',
        'system-event' => '检索用户日志',
      ),
      '配置管理' => 
      array (
        'system-system-set' => '配置系统',
        'system-module-set' => '配置模块',
        'system-service-set' => '配置服务',
      ),
    ),
    'author' => 'VeryIDE',
    'support' => 'http://www.veryide.com/',
    'context' => NULL,
    'writable' => NULL,
    'describe' => 'VeryIDE 系统内核',
    'external' => NULL,
    'state' => true,
  ),
  'task' => 
  array (
    'name' => '任务',
    'appid' => 'task',
    'model' => 'module',
    'signed' => '553cb096730c4bd8e2ac105bdd9f68a5',
    'version' => 1.2,
    'serve' => NULL,
    'hooks' => NULL,
    'index' => 'http://oa.cc/module/task/content/',
    'domain' => NULL,
    'statis' => NULL,
    'permit' => 
    array (
      'workflow-thread-mod' => '修改帖子',
      'workflow-thread-del' => '删除帖子',
      'workflow-project-exa' => '审核项目',
      'workflow-project-mod' => '修改项目',
      'workflow-project-del' => '删除项目',
      'workflow-salary-chk' => '管理考勤',
      'workflow-salary-mod' => '修改工资',
      'workflow-salary-del' => '删除工资',
      'workflow-stuff-add' => '新增物品',
      'workflow-stuff-mod' => '修改物品',
      'workflow-stuff-del' => '删除物品',
      'workflow-merchant-add' => '新增商户',
      'workflow-merchant-mod' => '修改商户',
      'workflow-merchant-del' => '删除商户',
    ),
    'author' => 'VeryIDE',
    'support' => 'http://www.veryide.com/',
    'context' => 
    array (
      '任务管理' => 
      array (
        'link' => 'stuff.list.php',
        'menu' => 
        array (
          '新增任务' => 'task.edit.php',
          '任务列表' => 'task.list.php',
        ),
      ),
    ),
    'writable' => NULL,
    'describe' => NULL,
    'external' => NULL,
    'state' => true,
  ),
  'test' => 
  array (
    'name' => '试题',
    'appid' => 'test',
    'model' => 'module',
    'signed' => '8ad8757baa8564dc136c1e07507f4a98',
    'version' => 1.7,
    'serve' => NULL,
    'hooks' => NULL,
    'index' => 'http://oa.cc/module/test/content/',
    'domain' => NULL,
    'statis' => 
    array (
      'person' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'test\' AND aid = {aid}',
      'public' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'test\'',
    ),
    'permit' => 
    array (
      'test-list-add' => '添加表单',
      'test-list-mod' => '修改表单',
      'test-list-del' => '删除表单',
      'test-data-dow' => '下载用户数据',
      'test-data-del' => '删除用户数据',
      'test-other' => '<span class=\'text-yes\'>包含他人表单</span>',
    ),
    'author' => 'VeryIDE',
    'support' => 'http://www.veryide.com/',
    'context' => 
    array (
      '新增试题' => 'test.edit.php',
      '管理试题' => 'test.list.php',
    ),
    'writable' => NULL,
    'describe' => '想让网友做套试题？还是搞个问答活动？',
    'external' => NULL,
    'state' => true,
  ),
  'vote' => 
  array (
    'name' => '投票',
    'appid' => 'vote',
    'model' => 'module',
    'signed' => '5223e099a916b65b54e2d5f7d8652e81',
    'version' => 1.7,
    'serve' => NULL,
    'hooks' => NULL,
    'index' => 'http://oa.cc/module/vote/content/',
    'domain' => NULL,
    'statis' => 
    array (
      'person' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'vote\' AND aid = {aid}',
      'public' => 'SELECT COUNT(*) FROM `mod:form_form` WHERE appid = \'vote\'',
    ),
    'permit' => 
    array (
      'vote-list-add' => '添加表单',
      'vote-list-mod' => '修改表单',
      'vote-list-del' => '删除表单',
      'vote-data-mod' => '修改投票数据',
      'vote-data-dow' => '下载用户数据',
      'vote-data-del' => '删除用户数据',
      'vote-other' => '<span class=\'text-yes\'>包含他人表单</span>',
    ),
    'author' => 'VeryIDE',
    'support' => 'http://www.veryide.com/',
    'context' => 
    array (
      '新增投票' => 'vote.edit.php',
      '管理投票' => 'vote.list.php',
    ),
    'writable' => NULL,
    'describe' => '图片投票十分钟搞定',
    'external' => NULL,
    'state' => true,
  ),
  'workflow' => 
  array (
    'name' => '协作',
    'appid' => 'workflow',
    'model' => 'module',
    'signed' => '553cb096730c4bd8e2ac105bdd9f68a5',
    'version' => 1.2,
    'serve' => NULL,
    'hooks' => NULL,
    'index' => 'http://oa.cc/module/workflow/content/',
    'domain' => NULL,
    'statis' => NULL,
    'permit' => 
    array (
      'workflow-thread-mod' => '修改帖子',
      'workflow-thread-del' => '删除帖子',
      'workflow-project-exa' => '审核项目',
      'workflow-project-mod' => '修改项目',
      'workflow-project-del' => '删除项目',
      'workflow-salary-chk' => '管理考勤',
      'workflow-salary-mod' => '修改工资',
      'workflow-salary-del' => '删除工资',
      'workflow-stuff-add' => '新增物品',
      'workflow-stuff-mod' => '修改物品',
      'workflow-stuff-del' => '删除物品',
      'workflow-merchant-add' => '新增商户',
      'workflow-merchant-mod' => '修改商户',
      'workflow-merchant-del' => '删除商户',
    ),
    'author' => 'VeryIDE',
    'support' => 'http://www.veryide.com/',
    'context' => 
    array (
      '帖子管理' => 
      array (
        'link' => 'thread.list.php',
        'menu' => 
        array (
          '帖子列表' => 'thread.list.php',
          '统计报表' => 'thread.stat.php',
        ),
      ),
      '项目管理' => 
      array (
        'link' => 'project.list.php',
        'menu' => 
        array (
          '申请项目' => 'project.edit.php',
          '项目列表' => 'project.list.php',
        ),
      ),
      '考勤管理' => 
      array (
        'link' => 'daily.list.php',
        'menu' => 
        array (
          '新增考勤' => 'daily.edit.php',
          '考勤列表' => 'daily.list.php',
        ),
      ),
      '物品管理' => 
      array (
        'link' => 'stuff.list.php',
        'menu' => 
        array (
          '新增物品' => 'stuff.edit.php',
          '物品列表' => 'stuff.list.php',
        ),
      ),
      '固定资产' => 
      array (
        'link' => 'asset.list.php',
        'menu' => 
        array (
          '新增资产' => 'asset.edit.php',
          '资产列表' => 'asset.list.php',
        ),
      ),
      '工资结算' => 
      array (
        'link' => 'salary.list.php',
        'menu' => 
        array (
          '工资统计' => 'salary.list.php',
          '工资设定' => 'salary.sets.php',
        ),
      ),
      '商户管理' => 
      array (
        'link' => 'merchant.list.php',
        'menu' => 
        array (
          '新增客户' => 'merchant.edit.php',
          '客户列表' => 'merchant.list.php',
        ),
      ),
      '日志管理' => 
      array (
        'link' => 'journal.list.php',
        'menu' => 
        array (
          '新增日志' => 'journal.edit.php',
          '日志列表' => 'journal.list.php',
        ),
      ),
    ),
    'writable' => NULL,
    'describe' => '轻松工作，轻松生活！',
    'external' => NULL,
    'state' => true,
  ),
);