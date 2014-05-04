<?php
/**
* @file mod_file.php
* @synopsis  文件读写操作
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 17:20:27
 */

!defined('PATH_ADMIN') && exit('Forbidden');
class mod_file
{

	public static function write($filename, $data, $method = 'wb+', $iflock = 1, $check = 1, $chmod = 1)  //写
	{
		if (empty($filename))
		{
		    return false;
		}

		if ($check && strpos($filename, '..') !== false)
		{
			return false;
		}

    if (!is_dir(dirname($filename)) && !self::mkdir_recursive(dirname($filename), 0777))
    {
      return false;
    }

		if (false == ($handle = fopen($filename, $method)))
		{
			return false;
		}

		if($iflock)
		{
			flock($handle, LOCK_EX);
		}
		fwrite($handle, $data);
		touch($filename);

		if($method == "wb+")
		{
			ftruncate($handle, strlen($data));
		}
		fclose($handle);
		$chmod && @chmod($filename,0777);
		return true;
	}

	public static function read( $filename, $method = "rb" )  //读取文件
	{
		if (strpos( $filename, '..' ) !== false)
		{
			return false;
		}
		if( $handle = @fopen( $filename, $method ) )
		{
			flock( $handle, LOCK_SH );
			$filedata = @fread( $handle, filesize( $filename ) );
			fclose( $handle );
			return $filedata;
		}
		else
		{
			return false;
		}
	}

	public static function rm($filename)   //删除文件
	{
		if (strpos($filename, '..') !== false)
		{
			return false;
		}

		return @unlink($filename);
	}

	public static function mkdir_recursive($pathname,$mod = 0777)
	{
		if (strpos( $pathname, '..' ) !== false)
		{
			return false;
		}
		$pathname = rtrim(preg_replace(array('/\\{1,}/', '/\/{2,}/'), '/', $pathname), '/');
    if (is_dir($pathname))
    {
    	return true;
    }

		is_dir(dirname($pathname)) || self::mkdir_recursive(dirname($pathname), $mode);
		return is_dir($pathname) || @mkdir($pathname, $mode);
	}

	public static function rm_recurse($file)  //递归删除
	{
		if (strpos( $file, '..' ) !== false)
		{
			return false;
		}

		if (is_dir($file) && !is_link($file))
		{
			foreach(scandir($file) as $sf)
			{
			  if($sf === '..' || $sf === '.')
			  {
			  	continue;
			  }
				if (!self::rm_recurse($file . '/' . $sf))
				{
					return false;
				}
			}
			return @rmdir($file);
		}
		else
		{
			return unlink($file);
		}
	}

	public static function check_security($filename, $ifcheck=1)   //文件检查
	{
		if (strpos($filename, 'http://') !== false) return false;
		if (strpos($filename, 'https://') !== false) return false;
		if (strpos($filename, 'ftp://') !== false) return false;
		if (strpos($filename, 'ftps://') !== false) return false;
		if (strpos($filename, 'php://') !== false) return false;
		if (strpos($filename, '..') !== false) return false;

		return $filename;
	}
	public static function ls($path, $type = '')   //列表
	{
        if(!is_dir($path))
        {
            return false;
        }
        $files = scandir($path);
        array_shift($files);
        array_shift($files);
        if(!empty($type) && in_array($type, array('file', 'dir')))
        {
            $func = "is_" . $type;
            foreach($files as $k => $cur_file)
            {
                if(!$func($path . '/' . $cur_file) || $cur_file == '.svn')
                {
                    unset($files[$k]);
                }
            }
        }
        return $files;
    }
	}
