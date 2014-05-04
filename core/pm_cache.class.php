<?php

!defined('PATH_ADMIN') && exit('Forbidden');

if(!function_exists('_at'))
{
	function _at($fun)
	{
  	$arg = func_get_args();
  	unset($arg[0]);
  	restore_error_handler();
  	$ret_val = @call_user_func_array($fun, $arg);
  	reset_error_handler();
		return $ret_val;
	}

}

if(!function_exists('reset_error_handler'))
{
	function reset_error_handler()
	{
    @set_error_handler('exception_handler');
	}

}

class Object
{
    var $_errors = array();
    var $_errnum = 0;
    function __construct()
    {
        $this->Object();
    }
    function Object()
    {
        #TODO
    }
    /**
     *    触发错误
     *
     *    @author    RLK
     *    @param     string $errmsg
     *    @return    void
     */
    function _error($msg, $obj = '')
    {
        if(is_array($msg))
        {
            $this->_errors = array_merge($this->_errors, $msg);
            $this->_errnum += count($msg);
        }
        else
        {
            $this->_errors[] = compact('msg', 'obj');
            $this->_errnum++;
        }
    }

    /**
     *    检查是否存在错误
     *
     *    @author    RLK
     *    @return    int
     */
    function has_error()
    {
        return $this->_errnum;
    }

    /**
     *    获取错误列表
     *
     *    @author    RLK
     *    @return    array
     */
    function get_error()
    {
        return $this->_errors;
    }
}


define('CACHE_DIR_NUM', 500); // 缓存目录数量，根据预期缓存文件数调整，开根号即可

/**
 *    基础缓存类接口
 *
 *    @author    RLK
 *    @usage    none
 */

class CacheServer extends Object
{
    var $_options = null;
    function __construct($options = null)
    {
        $this->CacheServer($options);
		}

    function CacheServer($options = null)
    {
        $this->_options = $options;
    }

    /**
     *    获取缓存的数据
     *
     *    @author    RLK
     *    @param     string $key
     *    @return    mixed
     */
    function &get($key){}
    /**
     *    设置缓存
     *
     *    @author    RLK
     *    @param     string $key
     *    @param     mixed  $value
     *    @param     int    $ttl
     *    @return    bool
     */
    function set($key, $value, $ttl = 0){}
    /**
     *    清空缓存
     *
     *    @author    RLK
     *    @return    bool
     */
    function clear(){}

    /**
     *    删除一个缓存
     *
     *    @author    RLK
     *    @param     string $key
     *    @return    bool
     */
    function delete($key){}
}

/**
 *    普通PHP文件缓存
 *
 *    @author    RLK
 *    @usage    none
 */
class PhpCacheServer extends CacheServer
{
	/* 缓存目录 */
		public $_cache_dir;
    function set($key, $value, $ttl = 0)
    {
        if (!$key)
        {
            return false;
        }
        $cache_file = $this->_get_cache_path($key);
        $cache_data = "<?php\r\n/**\r\n *  @Created By FEILIU PhpFileCacheServer\r\n *  @Time:" . date('Y-m-d H:i:s') . "\r\n */";
        $cache_data .= $this->_get_expire_condition(intval($ttl));
        $cache_data .= "\r\nreturn " . ckarrayeval($value) .  ";\r\n";
				$cache_data .= "\r\n?>";
				@chmod($cache_file,0777);
        return file_put_contents($cache_file, $cache_data, LOCK_EX);
		}

    function &get($key)
    {
        $cache_file = $this->_get_cache_path($key);
        if (!is_file($cache_file))
        {
            return false;
        }
        $data = include($cache_file);

        return $data;
		}

    function clear()
    {
        $dir = dir($this->_cache_dir);
        while (false !== ($item = $dir->read()))
        {
            if ($item == '.' || $item == '..' || substr($item, 0, 1) == '.')
            {
                continue;
            }
            $item_path = $this->_cache_dir . '/' . $item;
            if (is_dir($item_path))
            {
                @ck_rmdir($item_path);
            }
            else
            {
                _at(unlink, $item_path);
            }
        }

        return true;
		}

    function delete($key)
    {
        $cache_file = $this->_get_cache_path($key);

        return _at(unlink, $cache_file);
		}

    function set_cache_dir($path)
    {
        $this->_cache_dir = $path;
		}

    function _get_expire_condition($ttl = 0)
    {
        if (!$ttl)
        {
            return '';
        }

        return "\r\n\r\n" . 'if(filemtime(__FILE__) + ' . $ttl . ' < time())return false;' . "\r\n";
		}

    function _get_cache_path($key)
    {
			$dir = str_pad(abs(crc32($key)) % CACHE_DIR_NUM, 4, '0', STR_PAD_LEFT);
      @mkdir_recursive($this->_cache_dir . '/' . $dir,0777);
      return $this->_cache_dir . '/' . $dir .  '/' . $this->_get_file_name($key);
		}

    function _get_file_name($key)
    {
        return md5($key) . '.cache.php';
    }
}

class MemcacheServer extends CacheServer
{
    var $_memcache = null;
    function __construct($options)
    {
        $this->MemcacheServer($options);
    }
    function MemcacheServer($options)
    {
        parent::__construct($options);

        /* 连接到缓存服务器 */
        $this->connect($this->_options);
    }

    /**
     *    连接到缓存服务器
     *
     *    @author    Garbin
     *    @param     array $options
     *    @return    bool
     */
    function connect($options)
    {
        if (empty($options))
        {
            return false;
        }
        $this->_memcache = new Memcache;

        return $this->_memcache->connect($options['host'], $options['port']);
    }

    /**
     *    写入缓存
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function set($key, $value, $ttl = null)
		{
				//writearr($key,$value);
        return $this->_memcache->set($key, $value, $ttl);
    }

    /**
     *    获取缓存
     *
     *    @author    Garbin
     *    @param     string $key
     *    @return    mixed
     */
    function &get($key)
		{
				$value = $this->_memcache->get($key);
        return $value;
    }

    /**
     *    清空缓存
     *
     *    @author    Garbin
     *    @return    bool
     */
    function clear()
    {
        return $this->_memcache->flush();
    }

    function delete($key)
    {
        return $this->_memcache->delete($key);
    }
}

class RedisServer extends CacheServer
{
    var $_redis = null;
    function __construct($options)
    {
        $this->RedisServer($options);
    }
    function RedisServer($options)
    {
        parent::__construct($options);

        /* 连接到缓存服务器 */
        $this->connect($this->_options);
    }

    /**
     *    连接到缓存服务器
     *
     *    @author    Garbin
     *    @param     array $options
     *    @return    bool
     */
    function connect($options)
    {
        if (empty($options))
        {
            return false;
        }
        $this->_redis = new Redis;

        return $this->_redis->connect($options['host'], $options['port']);
    }

    /**
     *    写入缓存
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function set($key, $value, $ttl = null)
		{
				$value = serialize($value);
        return $this->_redis->set($key, $value, $ttl);
    }

    /**
     *    获取缓存
     *
     *    @author    Garbin
     *    @param     string $key
     *    @return    mixed
     */
    function &get($key)
		{
				$valus = $this->_redis->get($key);
        return unserialize($valus);
    }

    /**
     *    清空缓存
     *
     *    @author    Garbin
     *    @return    bool
     */
    function clear()
    {
        return $this->_redis->flush();
    }

    function delete($key)
    {
        return $this->_redis->delete($key);
    }
}
?>
