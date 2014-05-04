<?php
/**
* @file pm_init.php
* @synopsis  产品后台核心文件
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 14:10:27
 */

	error_reporting(E_ALL ^ E_NOTICE);
	!defined('PATH_ADMIN') && exit('Forbidden');
	function_exists('date_default_timezone_set') && date_default_timezone_set('Asia/Shanghai');

	session_start();
	ini_set('error_log', PATH_ADMIN.'/data/log/php_error.log');// PHP错误记录日志
	ini_set('log_errors', '1');
	ini_set('memory_limit', '64M');

	(empty($_SERVER['PHP_SELF'])) && $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];

	if(!isset($_SERVER['DOCUMENT_ROOT']))
	{
    if(isset($_SERVER['SCRIPT_FILENAME']))
    {
    	$_SERVER['DOCUMENT_ROOT'] = strtr(substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])), array('\\' => '/'));
    }
	}
	if(!isset($_SERVER['DOCUMENT_ROOT']))
	{
    if(isset($_SERVER['PATH_TRANSLATED']))
    {
    	$_SERVER['DOCUMENT_ROOT'] = strtr(substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF'])), array('\\' => '/'));
    }
	}

	$_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

	//防止变量覆盖
	foreach($_REQUEST as $_k => $_v)
	{
    if(strlen($_k) > 0 && preg_match('/^(cfg_|GLOBALS)/i', $_k) )
    {
    	exit('Request var not allow!');
    }
	}
	//加载常量定义库
	require_once PATH_ADMIN . '/config/cfg_constants.php';
	// 加载函数库
	require_once PATH_APPLICATION . '/pm_core_functions.php';
	// 自动转义
	if(@function_exists(auto_addslashes))
	{
		auto_addslashes($_POST);
		auto_addslashes($_GET);
		auto_addslashes($_COOKIE);
		auto_addslashes($_REQUEST);
	}
	//加载相关文件
	require_once PATH_CONFIG . '/cfg_database.php';
	require_once PATH_APPLICATION . '/pm_router.php';
	require_once includesqlfile();   //加载数据库文件
	require_once PATH_MODULE . '/smarty/Smarty.class.php';
	require_once PATH_APPLICATION . '/pm_tpl.php';
	defined('DEBUG_LEVEL') || define('DEBUG_LEVEL', TRUE);

	defined('HOST') || define('HOST', 'http://' . $_SERVER['HTTP_HOST']);
	$path_info = pathinfo($_SERVER['PHP_SELF']);
	$path_x = rtrim(strtr($path_info['dirname'], array('\\' => '/')), '/');
	//URL定义
	defined('URL') || define('URL', 'http://' . $_SERVER['HTTP_HOST'] . $path_x);
	defined('VERIFY_CODE')||define('VERIFY_CODE',mod_config::get_one_config('fl_verify_code'));
	// 分页
	defined('PAGE_ROWS') || define('PAGE_ROWS', 20);
	defined('PATH_COOKIE') || define('PATH_COOKIE',  '/');
	$global_config = mod_config::get_configs(array('fl_timedf','fl_sysname','fl_sysurl'));
	pm_tpl::assign('cp_home',$global_config['fl_sysname']);
