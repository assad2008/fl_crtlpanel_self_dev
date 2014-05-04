<?php
/**
* @file mod_ftp.php
* @synopsis  ftp功能模块
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-05-02 18:13:18
 */
	!defined('PATH_ADMIN') && exit('Forbidden');
	class mod_ftp
	{
		private	static $handle;
		public static function get_config()
		{
			return $GLOBALS['ftpinfo'];
		}

		public static function init()
		{
			self::connect();
		}

		public static function connect()
		{
			require_once(PATH_APPLICATION . '/pm_ftp.class.php');
			$config = self::get_config();
			self::$handle = @new FTP();
			self::$handle->connect($config['server']);
			$ret = self::$handle->login($config['user'],$config['password']);
			if(!$ret)
			{
				self::$handle = null;	
			}
		}

		public static function ls($path = null)
		{
			self::init();
			if(!$path)
			{
				$ret = self::$handle->nlist();
			}else
			{
				self::$handle->chdir($path);
				$ret = self::$handle->nlist();
			}
			return $ret;
		}

		public static function mkdir($dirname,$path = null)
		{
			self::init();
			if ($path) self::$handle->cd($path);
			self::$handle->mkdir($dirname);
		}

		public static function R_mkdir($path)
		{
			self::init();
			$dir = explode("/", $path);
    	$path = ""; 
    	$ret = true; 
    	for ($i = 0; $i < count($dir); $i++) 
    	{ 
        $path .= "/".$dir[$i];
        if(!@self::$handle->cd($path))
        { 
          if(!@self::$handle->mkdir($path))
          { 
           $ret = false; 
           break; 
          } 
        } 
    	} 
    	return $ret; 
		}

		public static function cd($path)
		{
			self::init();
			self::$handle->cd($path);
		}

		public static function rmdir($dirname,$path = null)
		{
			self::init();
			if ($path) self::$handle->cd($path);
			self::$handle->rmdir($dirname);		
		}

		public static function upload($file)
		{
			self::init();
			$dirname = randstr(6);
			self::$handle->mkdir($dirname);
			self::cd($dirname);
			$filename = $dirname.'jpg';
			self::$handle->put($filename,$file);
			return $dirname.'/'.$filename;
		}

		public static function uploadpath($file,$path = '')
		{
			self::init();
			/*
			$sub_dir = explode('/',$path);
			$num = count($sub_dir);
			$pevdir = '';
			$i = 1;
			foreach($sub_dir AS $v)
			{
				if($i <= $num - 1)
				{
					$pevdir .= $v.'/';
				}
				$i++;
			}
			$pevdir = substr($pevdir,0,-1);
			$ret = self::ls($pevdir);
			$curdir = $sub_dir[0];
			$direxist = in_array($curdir,$ret);
			if(!$direxist)
			{
				self::mkdir($curdir,$pevdir);
			}else
			{		
			}
			*/
			$fileext = fileext($file);
			self::R_mkdir($path);
			self::cd($path);
			$newfile_name = time().randstr(6).'.'.$fileext;
			$ret = self::$handle->put($newfile_name,$file);
			$newavrtarpath = $path.'/'.$newfile_name;
			return $newavrtarpath;
		}
	
	}

		

?>
