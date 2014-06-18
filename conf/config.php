<?php
$gDBconfig = array(
	'host'     => 'localhost',
	'user'     => 'root',
	'pass'     => '123456',
	'database' => 'dev_custom',
	'charset'  => 'utf8',
	'pconncet' => 0,
);

define('DEBUG_MODE',true); //debug模式开关
define('DEVELOP_MODE',true); //开发模式开关
define('CREATE_DIR_MODE', 0777);//创建目录的权限
define('CACHE_DIR', ROOT_PATH . 'cache/');
define('DB_PREFIX','liv_');//定义数据库表前缀

//审核状态
$gGlobalConfig['status'] = array(
	'1' => '待审核',
	'2' => '已审核',
	'3' => '已打回',
);

//安装类型
$gGlobalConfig['install_type'] = array(
	'0' => '预发布',
	'1' => '发布',
);

//源码类型
$gGlobalConfig['source'] = array(
	'0' => '未加密',
	'1' => '加密',
);



?>
