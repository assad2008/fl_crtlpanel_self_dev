<?php
/**
* @file init.php
* @synopsis  后台初始化
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 15:50:22
 */

defined('PATH_ROOT') || define('PATH_ROOT', rtrim(strtr(__FILE__, array('\\' => '/' , 'init.php' => '' , '\init.php' => '', '//' => '/')), '/'));
defined('PATH_ADMIN') || define('PATH_ADMIN', PATH_ROOT);
require PATH_ADMIN . '/core/pm_init.php';
