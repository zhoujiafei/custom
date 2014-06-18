<?php
header('Content-Type:text/html; charset=utf-8');
if(!defined('ROOT_DIR'))
{
	define('ROOT_DIR', './');
}
if(!defined('CUR_CONF_PATH'))
{
	define('CUR_CONF_PATH', './');
}
define('ROOT_PATH', ($dir = @realpath(ROOT_DIR)) ? $dir . '/' : ROOT_DIR);

if (function_exists('date_default_timezone_set'))
{
    date_default_timezone_set('PRC');
}

// PHP 6 
if (PHP_VERSION < '6.0.0')
{
	@set_magic_quotes_runtime(0);

	define('MAGIC_QUOTES_GPC', @get_magic_quotes_gpc() ? true : false);
	if (MAGIC_QUOTES_GPC)
	{
		function stripslashes_vars(&$vars)
		{
			if (is_array($vars))
			{
				foreach ($vars as $k => $v)
				{
					stripslashes_vars($vars[$k]);
				}
			}
			else if (is_string($vars))
			{
				$vars = stripslashes($vars);
			}
		}

		if (is_array($_FILES))
		{
			foreach ($_FILES as $key => $val)
			{
				$_FILES[$key]['tmp_name'] = str_replace('\\', '\\\\', $val['tmp_name']);
			}
		}

		foreach (array('_REQUEST', '_GET', '_POST', '_COOKIE', '_FILES') as $v)
		{
			stripslashes_vars($$v);
		}
	}

	define('SAFE_MODE', (@ini_get('safe_mode') || @strtolower(ini_get('safe_mode')) == 'on') ? true : false);
}
else
{
	define('MAGIC_QUOTES_GPC', false);
	define('SAFE_MODE', false);
}

define('TIMENOW', isset($_SERVER['REQUEST_TIME']) ? (int) $_SERVER['REQUEST_TIME'] : time());
define('REFERRER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

require(ROOT_PATH . 'conf/config.php');
require(ROOT_PATH . 'lib/func/functions.php');
require(ROOT_PATH . 'conf/code.conf.php');
require(ROOT_PATH . 'lib/base/base.php');
require(ROOT_PATH . 'conf/template.conf.php');
require(ROOT_PATH . 'lib/templates/template.php');

//实例化模板引擎
$gTpl = new Templates();
//同一处理post与get数据
$_INPUT = hg_init_input();
//是否使用数据库
if (!defined('WITHOUT_DB') || !WITHOUT_DB)
{
	$gDB = hg_ConnectDB();
}

$gUser = array();
/***************************记录用户登陆信息*************************/
if (!defined('WITHOUT_LOGIN') || !WITHOUT_LOGIN)
{
	include(ROOT_PATH . 'lib/class/session.php');
	//用户登录session信息
	$session = new Session();
	//获取用户登录信息
	$gUser = $session->load_user();
	if (!$gUser['id'] && !in_array(SCRIPT_NAME, array('login', 'register')))
	{
		if (!$_INPUT['ajax'])
		{
			if ($_SERVER['QUERY_STRING'])
            {
                $query_string = '?' . $_SERVER['QUERY_STRING'];
            }
			header('Location:login.php' . $query_string);
			exit;
		}
		else
		{
			$data = array(
			    'login_error' => 1,
				'msg' => '请先登录',
				'callback' => "hg_ajax_post({href: 'login.php'}, '登录');",
			);
			echo json_encode($data);
			exit;
		}
	}
}
/***************************记录用户登陆信息*************************/

@date_default_timezone_set(TIMEZONE);

//创建db
function hg_ConnectDB()
{
	global $gDBconfig,$gDB;
	if (!$gDB)
	{
		include_once ROOT_PATH . 'lib/db/db_mysql.class.php';
		$gDB = new db();
		$gDB->connect($gDBconfig['host'], $gDBconfig['user'], $gDBconfig['pass'], $gDBconfig['database'], $gDBconfig['charset'], $gDBconfig['pconnect']);
	}
	return $gDB;
}

?>