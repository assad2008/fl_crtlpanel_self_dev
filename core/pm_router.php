<?php
/**
* @file pm_router.php
* @synopsis  控制器载入文件
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 14:14:37
 */

!defined('PATH_ADMIN') && exit('Forbidden');

function load_controller() 
{
	global $pm_starttime;
	try 
	{
  	$controller = 'pmc_' . ((empty($_GET['c'])) ? 'login' : $_GET['c']);
    $action = (empty($_GET['a'])) ? 'index' : $_GET['a'];
    $path = PATH_CONTROLLER . '/' . $controller . '.php';
    if (file_exists($path))
		{
			require $path;
		}
		else
		{
			throw new Exception("Sorry,the Controller \"{$controller}\" is not exist！<br />", 1);
		}

    if (method_exists($controller, $action) === true)
		{
			$instance = new $controller();
      if (method_exists($controller, 'pre') === true)
      {
      	$instance->pre();
      }
			$instance->$action();
      if (method_exists($controller, 'post') === true)
      {
      	$instance->post();
      }
		}
		else
		{
			throw new Exception("Sorry,this Method \"{$action}\" is not exist！<br />", 2);
		}

  }
  catch (Exception $e)
	{
		if (DEBUG_LEVEL === true)
		{
			echo '<pre>';
			echo $e->getMessage() . $e->getTraceAsString();
			echo '</pre>';
			exit;
		}
		else
		{
			header("HTTP/1.1 404 Not Found");
			exit;
		}
	}
}



/* 自动加载类库 */
if (!function_exists ( '__autoload' ))
{
	function __autoload($classname)
	{
		//debug($_GET);
		$classfile = $classname . '.php';
		try
		{
			if (!is_file(PATH_MODULE . '/' . $classfile) && ! class_exists($classname))
			{
				throw new Exception('Sorry, not fund this Models <br />' . $classname);
			}
			else
			{
				require PATH_MODULE . '/' . $classfile;
			}
		}
		catch (Exception $e)
		{
    		if (DEBUG_LEVEL === true)
    		{
    			echo '<pre>';
    			echo $e->getMessage() . $e->getTraceAsString();
    			echo '</pre>';
    			exit();
    		}
    		else
    		{
    			header("HTTP/1.1 404 Not Found");
    			exit;
    		}
		}
	}
}
?>
