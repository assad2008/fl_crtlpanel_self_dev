<?php

!defined('PATH_ADMIN') && exit('Forbidden');

//产品后台数据库
$GLOBALS['database']['db_host'] = '127.0.0.1';
$GLOBALS['database']['db_user'] = '';
$GLOBALS['database']['db_pass'] = '';
$GLOBALS['database']['db_name'] = '';
$GLOBALS['database']['db_charset'] = 'utf8';
$GLOBALS['database']['table_prefix'] = '';

$GLOBALS['databaseo2']['db_host'] = '';
$GLOBALS['databaseo2']['db_user'] = '';
$GLOBALS['databaseo2']['db_pass'] = '';
$GLOBALS['databaseo2']['db_name'] = '';
$GLOBALS['databaseo2']['db_charset'] = 'utf8';

//SQL检查设置，是否开启 SQL检查  防止SQL注入
$GLOBALS['security']['querysafe']['status']	= 1;
$GLOBALS['security']['querysafe']['dfunction']	= array('load_file','hex','substring','if','ord','char');
$GLOBALS['security']['querysafe']['daction']	= array('@','intooutfile','intodumpfile','unionselect','(select', 'unionall', 'uniondistinct');
$GLOBALS['security']['querysafe']['dnote']	= array('/*','*/','#','--','"');
$GLOBALS['security']['querysafe']['dlikehex']	= 1;
$GLOBALS['security']['querysafe']['afullnote']	= 0;
