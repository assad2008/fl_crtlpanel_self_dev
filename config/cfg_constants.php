<?php
/**
* @file cfg_constants.php
* @synopsis  产品后台 路径常量
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 15:45:55
 */

	!defined('PATH_ADMIN') && exit('Forbidden');

	//系统路径
	defined('PATH_APPLICATION') || define('PATH_APPLICATION', PATH_ADMIN . '/core');
	defined('PATH_DB') || define('PATH_DB', PATH_APPLICATION . '/db');
	defined('PATH_MODULE') || define('PATH_MODULE', PATH_ADMIN . '/models');
	defined('PATH_LIB') || define('PATH_LIB', PATH_ADMIN . '/lib');
	defined('PATH_CONTROLLER') || define('PATH_CONTROLLER', PATH_ADMIN . '/controllers');
	defined('PATH_CONFIG') || define('PATH_CONFIG', PATH_ADMIN . '/config');
	defined('PATH_DATA') || define('PATH_DATA', PATH_ADMIN . '/data');
	defined('PATH_LANG') || define('PATH_LANG', PATH_ADMIN . '/languages');
	defined('PATH_XML') || define('PATH_XML', PATH_ADMIN . '/xmlfile');
	defined('PATH_ADMIN_LOG_PATH') || define('PATH_XML', PATH_ADMIN . '/data/log/adminlogs');

	//模板路径
	defined('PATH_TPLS') || define('PATH_TPLS', PATH_ADMIN . '/template');
	defined('PATH_TPLS_COMPILE') || define('PATH_TPLS_COMPILE', PATH_TPLS . '/compile');
	defined('PATH_TPLS_CACHE') || define('PATH_TPLS_CACHE', PATH_TPLS . '/cache');
	defined('PATH_TPLS_MAIN') || define('PATH_TPLS_MAIN', PATH_TPLS . '/main');
	defined('TPLS_BACKUP_EXT') || define('TPLS_BACKUP_EXT', '.bak');
	defined('TPLS_TEMPLATEEXT') || define('TPLS_TEMPLATE_EXT', '.tpl');
	//其他设置
	defined('CUR_VERSION') || define('CUR_VERSION', '1.0');
	define('SYSFOUNDER','admin');
	define('SYSSTRKEY','dsjkfsdflds3248!!!%*&%');
	define('DESKURL','http://pm.feiliu.com/');
	defined('AUTH_KEY') || define('AUTH_KEY', 'flpm^%&&%%$kfc');
	defined('PATH_UPLOAD') || define('PATH_UPLOAD', '/data0/www/html/gamebi/data/upload/');
	defined('IMGURL_PREFIX') || define('IMGURL_PREFIX', 'http://image.feeliu.com/Upload/');
	defined('ALL_PRODUCTID') || define('ALL_PRODUCTID', 9999999);
	defined('BASE_URL') || define('BASE_URL','http://iosadm.feiliu.com/');
	defined('PATH_OPERALOG') || define('PATH_OPERALOG', PATH_ADMIN . '/data/opera_log');
