<?php

 !defined('PATH_ADMIN') &&exit('Forbidden');
class Flredis 
{
	var $enable;
	var $obj;

	function __construct() 
	{
		$this->init();
	}

	function init() 
	{
			$this->obj = new Redis;
			$connect = @$this->obj->connect('127.0.0.1', 6379);
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
		return $this->obj->set($key, $value, $ttl);
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
