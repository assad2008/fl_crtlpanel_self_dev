<?php
/**
* @file mod_auth.php
* @synopsis  验证模块
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 16:36:06
 */

 !defined('PATH_ADMIN') &&exit('Forbidden');
	class mod_auth
	{

		public static $instance = null;

		function __construct()
		{
			$auth = mod_login::instance();
			if(!$auth->is_login()) //不自动跳转
			{
				header("location: ./");
			}
			if(!$_GET['c'])
			{
				header("location: ./");
			}
		}

		public static function instance()
		{
			if(self::$instance === null)
			{
				self::$instance = new mod_auth;
			}
			return self::$instance;
		}
	}
