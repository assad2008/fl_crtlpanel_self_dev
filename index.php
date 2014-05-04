<?php
/**
* @file index.php
* @synopsis  后台入口
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 16:09:49
 */

	error_reporting(0);
	require 'init.php';
	$pmstarttime = explode(' ', microtime());
	$pm_starttime = $pmstarttime[1] + $pmstarttime[0];
	if(empty($_GET['c']) || ($_GET['c']=='login'  && (empty($_GET['a']) || in_array($_GET['a'],array('login','ssologin')) )) || $_GET['c']=='securimage')
	{

	}
	else
	{
		mod_auth::instance(); //权限
  	session_write_close();
	}
	load_controller();
