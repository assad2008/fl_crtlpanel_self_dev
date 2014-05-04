<?php
/**
* @file mod_cache.php
* @synopsis  缓存模块
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 16:36:35
 */

!defined('PATH_ADMIN') && exit('Forbidden');

class mod_cache
{
    public static function get_cache($cache_name)
    {
        $file_path = PATH_DATA . '/cache/' . $cache_name . '.php';
        if (file_exists($file_path))
        {
            $data = unserialize(mod_file::read($file_path));
            //修复清空缓存后分类管理没有数据的bug
            if ($data != NULL)
            {
                return $data;
            }
        }
        return false;
    }

    public static function set_cache($cache_name, $data = array())
    {
        $file_path = PATH_DATA . '/cache/' . $cache_name . '.php';
        $cache_content = str_replace(array('<?', '?>'), '  ', serialize($data));
        mod_file::write($file_path, $cache_content);
    }

    public static function empty_all_cache()
    {
        if ($dh = opendir(PATH_DATA . '/cache'))
        {
            while (false !== ( $file = readdir() ))
            {
                if ($file != '.' && $file != '..' && $file != '.svn')
                {
                    $cache_content = serialize(array());
                    $file_path = PATH_DATA . '/cache/' . $file;
                    mod_file::write($file_path, $cache_content);
                }
            }
        }
    }

    public static function empty_some_cache($cache_name = '')
    {
        if (empty($cache_name))
        {
            return false;
        }
        if ($dh = opendir(PATH_DATA . '/cache'))
        {
            while (false !== ( $file = readdir() ))
            {
                if (strpos($file, $cache_name) !== false)
                {
                    $cache_content = serialize(array());
                    $file_path = PATH_DATA . '/cache/' . $file;
                    mod_file::write($file_path, $cache_content);
                }
            }
        }
    }
}

?>
