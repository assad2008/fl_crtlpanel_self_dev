<?php
/**
* @file pm_memcache.php
* @synopsis  产品后台 Memcache类
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 18:36:55
 */

 !defined('PATH_ADMIN') &&exit('Forbidden');
class Flmemcache 
{
	var $enable;
	var $obj;

	function __construct() 
	{
		$this->init();
	}

	function init() 
	{
			$this->obj = new Memcache;
			$connect = @$this->obj->connect('127.0.0.1', 12000);
			if($connect)
			{
				$this->enable = true;
			}else
			{
				$this->enable = false;
			}
	}

	function get($key) 
	{
		return $this->obj->get($key);
	}

	function set($key, $value, $ttl = 0) 
	{
		return $this->obj->set($key, $value, MEMCACHE_COMPRESSED, $ttl);
	}

	function rm($key) 
	{
		return $this->obj->delete($key);
	}

	function incr($key)
	{
		return $this->obj->increment($key);
	}

	function clear()
	{
		return $this->obj->flush();
	}

}

?>
