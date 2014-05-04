<?php
/**
* @file app_tpl.php
* @synopsis  模板引擎类
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 14:09:14
 */

!defined('PATH_ADMIN') && exit('Forbidden');
class pm_tpl
{
	public static $instance = null;
	public static function init ($path = PATH_TPLS_MAIN)
	{
		if (empty(self::$instance->template_dir) || $path != self::$instance->template_dir)
		{
			self::$instance = new Smarty();

			self::$instance->template_dir = path_exists($path);
			self::$instance->compile_dir = path_exists(PATH_TPLS_COMPILE);
			self::$instance->cache_dir = path_exists(PATH_TPLS_CACHE);

			self::$instance->left_delimiter = '<{';
			self::$instance->right_delimiter = '}>';

			self::$instance->caching = FALSE;
			self::$instance->compile_check = TRUE;

			self::$instance->security = TRUE;
			self::$instance->security_settings['PHP_HANDLING'] = SMARTY_PHP_PASSTHRU;
			self::$instance->security_settings['ALLOW_CONSTANTS'] = TRUE;
			self::config();
			self::assignlang();

		}
		return self::$instance;
	}

	protected static function config ()
	{
		self::$instance->assign('URL', URL);
		self::$instance->assign('date_format', '%Y-%m-%d %H:%M:%S');
		self::$instance->assign('date_format_ymd_hm', '%Y-%m-%d %H:%M');
		self::$instance->assign('date_format_md_hm', '%m-%d %H:%M');
		self::$instance->assign('date_format_yymd_hm', '%y-%m-%d %H:%M');
		self::$instance->assign('date_format_ymd', '%Y-%m-%d');
		self::$instance->assign('date_format_ym', '%Y-%m');
	}

	public static function assignlang()
	{
		$lang = includelangfile();
		//debug($lang);
		self::$instance->assign('lang', $lang);
	}

	public static function assign ($tpl_var, $value, $path = PATH_TPLS_MAIN)
	{
		self::init($path);
    self::$instance->assign($tpl_var, $value);
	}

	public static function display($tpl, $path = PATH_TPLS_MAIN)
	{
		$instance = self::init($path);
		$instance->display($tpl.TPLS_TEMPLATE_EXT);
	}

	public static function fetch($tpl, $path = PATH_TPLS_MAIN)
	{
		$instance = self::init($path);
		return $instance->fetch($tpl.TPLS_TEMPLATE_EXT);
	}

}
?>
