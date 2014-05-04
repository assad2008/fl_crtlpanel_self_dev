<?php
/**
* @file pm_core_functions.php
* @synopsis  产品中心 函数中心
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-21 21:15:36
 */
	!defined('PATH_ADMIN') && exit('Forbidden');

	/* 路径是否存在? */
	function path_exists($path)
	{
		$pathinfo = pathinfo ( $path . '/tmp.txt' );
		if (!empty($pathinfo ['dirname']))
		{
			if (file_exists( $pathinfo['dirname']) === false)
			{
				if (mkdir($pathinfo['dirname'], 0777, true) === false)
				{
					return false;
				}
			}
		}
		return $path;
	}

	function covcharset($keys,$char)
	{
		$keys = ($contentscharset = mb_detect_encoding($keys, "ASCII, UTF-8, GB2312, GBK, BIG5")) == "$char" ? $keys : iconv($contentscharset, "$char", $keys);
	return $keys;
	}

	function getfilesize($url)  //远程获取文件长度
	{
		$url = parse_url($url);
 	 if($fp = @fsockopen($url['host'],empty($url['port'])?80:$url['port'],$error))
 	 {
			fputs($fp,"GET ".(empty($url['path'])?'/':$url['path'])." HTTP/1.1\r\n");
			fputs($fp,"Host:$url[host]\r\n\r\n");
			while(!feof($fp))
			{
				$tmp = fgets($fp);
				if(trim($tmp) == '')
				{
					break;
				}else if(preg_match('/Content-Length:(.*)/si',$tmp,$arr))
				{
					return trim($arr[1]);
				}
			}
			return FALSE;
		}else
		{
			return FALSE;
		}
	}

	function resizeimg($imgpath,$litd,$action = 'link')   //图片处理
	{
		$imgsizearr = array(
		'small' => array('x' => 57,'y' => 57),
		'middle' => array('x' => 72,'y' => 72),
		'big' => array('x' => 114,'y' => 114)
		);
		include PATH_LIB.'/./thumblib/ThumbLib.inc.php';
		$saveimg = PATH_ADMIN.'/data/resizeimg/';
		@mkdir($saveimg.'/'.$litd);
		$pathinfo = array();
		foreach($imgsizearr AS $key => $size)
		{
			$thumb = PhpThumbFactory::create($imgpath);
			$thumb->resize($size['x'], $size['y']);
			$savepath = $saveimg.'./'.$litd.'/'.$action.'_'.$size['x'].'x'.$size['y'].'.png';
			$thumb->save($savepath, 'png');
			$pathinfo[$key] = './data/resizeimg/'.$litd.'/'.$action.'_'.$size['x'].'x'.$size['y'].'.png';
		}
		return $pathinfo;
	}


	function fileext($filename)  //获取扩展名
	{
		return trim(substr(strrchr($filename, '.'), 1, 10));
	}

	function writearr($script,$cachedata,$prefix = 'cache_')  //写缓存
	{
	 	global $authkey;
		$dir = PATH_ADMIN.'/data/test/';
		$curdatas = "\$_DCACHE['$script'] = ".ckarrayeval($cachedata).";\n\n";
		$curdatas .= "\$_DCACHE['{$script}_time'] = ".time();
		if(!is_dir($dir))
		{
				@mkdir($dir, 0777);
		}
		if($fp = @fopen("$dir$prefix$script$page.php", 'wb')) {
			fwrite($fp, "<?php\n//TBOLE Cache File! DO NOT modify me!"."\n//Created: ".date("M j, Y, G:i")."\n//Identify: ".md5($prefix.$script.'.php'.$cachedata.$authkey)."\n\n$curdatas?>");
		fclose($fp);
		}else
		{
			exit('Can not write to cache files, please check directory ./data/ and ./data/cache/ .');
		}
	}

	function getcachevars($data, $type = 'VAR')
	{
		$evaluate = '';
		!is_array($data) && $data = array($data);
		foreach($data as $key => $val)
		{
			if(!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $key))
		 	{
				continue;
			}
			if(is_array($val))
			{
				$evaluate .= "\$$key = ".arrayeval($val).";\n";
			}else
			{
				$val = addcslashes($val, '\'\\');
				$evaluate .= $type == 'VAR' ? "\$$key = '$val';\n" : "define('".strtoupper($key)."', '$val');\n";
			}
		}
		return $evaluate;
	}

	function ckarrayeval($array, $level = 0)
	{
		if(!is_array($array))
		{
			return "\"".$array."\"";
		}
		if(is_array($array) && function_exists('var_export'))
	 	{
			return var_export($array, true);
		}
		$space = '';
		for($i = 0; $i <= $level; $i++)
		{
			$space .= "\t";
		}
		$evaluate = "Array\n$space(\n";
		$comma = $space;
		if(is_array($array))
		{
			foreach($array as $key => $val)
			{
				$key = is_string($key) ? '\''.addcslashes($key, '\'\\').'\'' : $key;  //转换
				$val = !is_array($val) && (!preg_match("/^\-?[1-9]\d*$/", $val) || strlen($val) > 12) ? '\''.addcslashes($val, '\'\\').'\'' : $val; //转换
				if(is_array($val))
				{
					$evaluate .= "$comma$key => ".ckarrayeval($val, $level + 1);
				}else
				{
					//$val = addslashes($val);
					$evaluate .= "$comma$key => \"$val\"";
				}
				$comma = ",\n$space";
			}
		}
		$evaluate .= "\n$space)";
		return $evaluate;
	}

	function arrtokeystr($arr)  //数组转成字符串
	{
		if(!$arr || count($arr) == 0)
		{
			return false;
		}

		$str = '';
		foreach($arr AS $k=>$v)
		{
			$str .= $k.',';
		}
		$str = substr($str,0,-1);
		return $str;
	}

	function admindebug($var,$exit = 1)
	{
		if(USERNAME == 'wangjiang')
		{
			error_reporting(E_ALL);
			if($var === NULL)
			{
				$var = $GLOBALS;
			}
			header("Content-type:text/html;charset=utf-8");
			echo '<pre style="background-color:black;color:white;font-size:13px; border: 2px solid green;padding: 5px;">变量跟踪信息：'."\n";
			if($type == 1)
			{
				var_dump($var);
			}elseif($type == 2)
			{
				print_r($var);
			}
			echo '</pre>';
			exit();
		}
	}

	function countfileline($filepath)   //检测文件有多少行
	{
		$fp = fopen($filepath, "r");
		$line = 0;
		while(fgets($fp)) $line++;
		fclose($fp);
		return $line;
	}

	function isemail($email) //检测是否为电子邮件地址
	{
		return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}

	function mailtxt($name,$pwd,$email)
	{
		$timenow = date('Y-m-d H:i:s',time());
		return "欢迎加入飞流游戏运作平台。<br />您的用户名为：{$name}， <br />您的密码为：{$pwd}，请登陆后尽快修改您的用户密码，以免泄露造成不良后果。<br />您注册账号的邮箱为：{$email} <br />登陆地址为：<a href=\"http://game.feiliu.com/admin/\" target=\"_blank\">点击登陆</a><br />飞流团队祝你工作顺利，身体健康O(∩_∩)O~<br />欢迎访问<a href=\"http://www.feiliu.com\" target=\"_blank\">飞流九天</a><br /><br />邮件发送时间：{$timenow}";
	}

	function pwmailtxt($name,$pwd)
	{
		$timenow = date('Y-m-d H:i:s',time());
		return "欢迎使用飞流游戏运作平台。<br />您的用户名为：{$name}， <br />您的新密码为：{$pwd}，请登陆后尽快修改您的用户密码，以免泄露造成不良后果。 <br />登陆地址为：<a href=\"http://game.feiliu.com/admin/\" target=\"_blank\">点击登陆</a><br />飞流团队祝你工作顺利，身体健康O(∩_∩)O~<br />欢迎访问<a href=\"http://www.feiliu.com\" target=\"_blank\">飞流九天</a><br /><br />邮件发送时间：{$timenow}";
	}

	function debug($var = null,$type = 2)
	{
		if($var === NULL)
		{
			$var = $GLOBALS;
		}
		header("Content-type:text/html;charset=utf-8");
		echo '<pre style="background-color:black;color:white;font-size:13px; border: 2px solid green;padding: 5px;">变量跟踪信息：'."\n";
		if($type == 1)
		{
			var_dump($var);
		}elseif($type == 2)
		{
			print_r($var);
		}
		echo '</pre>';
		exit();
	}


	function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
 	{
		$ck_auth_key = AUTH_KEY;
		$ckey_length = 4;
		$key = md5($key ? $key : $ck_auth_key);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++)
	 	{
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++)
	 	{
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++)
		{
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE')
	 	{
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))
		 	{
				return substr($result, 26);
			}else
		 	{
				return '';
			}
		}else
		{
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}

/* 转义 */
	function auto_addslashes(&$array)
	{
		if($array)
		{
			foreach($array as $key => $value)
			{
				if(! is_array ( $value ))
				{
					$array [$key] = addslashes($value);
				}
				else
				{
					auto_addslashes($array [$key]);
				}
			}
		}
	}


/* 反转义 */
	function auto_stripslashes(&$array)
	{
		if($array)
		{
			foreach($array as $key => $value)
			{
				if(!is_array($value))
				{
					$array[$key] = stripslashes($value);
				}
				else
				{
					auto_stripslashes($array[$key]);
				}
			}
		}
	}

/**
 * 过滤字符串
 * 当 $editor 为 true 时，则不会转换 '<' 和 '>'
 *
 * @param $data data
 * @param $editor 是否使用了编辑器
 *
 */
	function strip($data, $editor = false)
	{
		$data = strtr($data, '`', '');
		if($editor == true)
		{
		// 过滤 JavaScript
			$search = array ('#<script[^>]*?>.*?[</script>]*#si', '#<iframe[^>]*?>.*?[</iframe>]*#si', '#<input[^>]*?>#si', '#<button[^>]*?>.*?</button>#si', '#<form[^>]*?>#si', '#</form>#si',
		'#(<[\/\!]*?)?(\ class\=[\'|"].*?[\'|"])|(\ id\=[\'|"].*?[\'|"])([^<>]*?>)?#si');
			$replace = array('', '', '', '', '', '');
			$data = preg_replace($search, $replace, $data);
			if (get_magic_quotes_gpc())
			{
				$data = trim($data);
			}
			else
			{
				$data = addslashes(trim($data));
			}
		}
		else
		{
			if(get_magic_quotes_gpc())
			{
				$data = htmlspecialchars(trim(stripslashes($data)), ENT_QUOTES);
			}
			else
			{
				$data = htmlspecialchars(trim($data), ENT_QUOTES);
			}
		}
		return $data;
	}

	function Cookie($ck_Var,$ck_Value,$ck_Time='F')
	{
		global $cookietime;
		if($ck_Time == 'F') $ck_Time = $cookietime;
		$S=$_SERVER['SERVER_PORT']=='443' ? 1:0;
		setCookie(CookiePre().'_'.$ck_Var,$ck_Value,$ck_Time,'/','',$S);
	}

	function GetCookie($Var)
	{
		return $_COOKIE[CookiePre().'_'.$Var];
	}

	function CookiePre()
	{
		return substr(md5($GLOBALS['flpm_hash']),0,5);
	}

	function Add_S(&$array)
	{
		if($array)
		{
			foreach($array as $key=>$value)
			{
				if(!is_array($value))
				{
					$array[$key]=addslashes($value);
				}else
				{
					Add_S($array[$key]);
				}
			}
		}
	}

	function HtmlConvert(&$array)
	{
		if(is_array($array))
		{
			foreach($array as $key => $value)
			{
				if(!is_array($value))
				{
					$array[$key]=htmlspecialchars($value);
				}else
				{
					HtmlConvert($array[$key]);
				}
			}
		}else
		{
			$array=htmlspecialchars($array);
		}
	}

	function utf8_trim($str)
	{
		$len = strlen($str);
		for($i=strlen($str)-1;$i>=0;$i-=1)
		{
			$hex .= ' '.ord($str[$i]);
			$ch   = ord($str[$i]);
			if(($ch & 128) == 0)  return substr($str,0,$i);
			if(($ch & 192) == 192)return substr($str,0,$i);
		}
		return($str.$hex);
	}


	function PwdCode($pwd)
	{
		return md5($_SERVER["HTTP_USER_AGENT"].$pwd.PASSWDKEY);
	}

	function SafeCheck($CK,$PwdCode,$var = 'FLAdminUser',$expire = 1800)
	{// 验证密码
		global $timestamp;
		$t  = $timestamp - $CK[0];
		if($t > $expire || $CK[2] != md5($PwdCode.$CK[0]))
		{
			Cookie($var,'',0);
			return false;
		}else
		{
			$CK[0] = $timestamp;
			$CK[2] = md5($PwdCode.$timestamp);
			$Value = implode("\t",$CK);
			$$var  = StrCode($Value);
			Cookie($var,StrCode($Value));
			return true;
		}
	}

	function gets($filename,$value)
	{
		if($handle=@fopen($filename,"rb"))
		{
			flock($handle,LOCK_SH);
			$getcontent=fread($handle,$value);//fgets调试
			fclose($handle);
		}
		return $getcontent;
	}

	function P_unlink($filename)
	{
		strpos($filename,'..')!==false && exit('Forbidden');
		return @unlink($filename);
	}

	function readover($filename,$method="rb")
	{
		strpos($filename,'..')!==false && exit('Forbidden');
		if($handle=@fopen($filename,$method))
		{
			flock($handle,LOCK_SH);
			$filedata = @fread($handle,filesize($filename));
			fclose($handle);
		}
		return $filedata;
	}

	function writeover($filename,$data,$method = "rb+",$iflock=1,$check=1,$chmod=1)
	{
		$check && strpos($filename,'..') !== false && exit('Forbidden');
		touch($filename);
		$handle = fopen($filename,$method);
		if($iflock)
		{
			flock($handle,LOCK_EX);
		}
		fwrite($handle,$data);
		if($method == "rb+") ftruncate($handle,strlen($data));
		fclose($handle);
		$chmod && @chmod($filename,0777);
	}

	function Char_cv($msg)
	{
		$msg = str_replace("\t","",$msg);
		$msg = str_replace("<","&lt;",$msg);
		$msg = str_replace(">","&gt;",$msg);
		$msg = str_replace("\r","",$msg);
		$msg = str_replace("\n","<br />",$msg);
		$msg = str_replace("   "," &nbsp; ",$msg);#编辑时比较有效
		return $msg;
	}

	function ieconvert($msg)
	{
		$msg = str_replace("\t","",$msg);
		$msg = str_replace("\r","",$msg);
		$msg = str_replace("   "," &nbsp; ",$msg);#编辑时比较有效
		return $msg;
	}

	//删除目录
	function deldir($path)
	{
		if(file_exists($path))
		{
			if(is_file($path))
			{
				P_unlink($path);
			}else
			{
				$handle = opendir($path);
				while($file = readdir($handle))
			 	{
					if(($file != ".") && ($file != "..") && ($file != ""))
					{
						if (is_dir("$path/$file"))
						{
							deldir("$path/$file");
						}else
						{
							P_unlink("$path/$file");
						}
					}
				}
				closedir($handle);
			}
		}
	}

	function F_L_count($filename,$offset)
	{
		$count_F = '';
		$onlineip = get_client_ip();
		$count = 0;
		if($fp = @fopen($filename,"rb"))
		{
			flock($fp,LOCK_SH);
			fseek($fp,-$offset,SEEK_END);
			$readb=fread($fp,$offset);
			fclose($fp);
			$readb=trim($readb);
			$readb=explode("\n",$readb);
			$count=count($readb);$count_F=0;
			for($i=$count-1;$i>0;$i--)
			{
				if(strpos($readb[$i],"|Logging Failed|$onlineip|")===false)
				{
					break;
				}
				$count_F++;
			}
		}
		return $count_F;
	}

	function get_date($timestamp,$timeformat = '')
	{
		global $yl_datefm,$yl_timedf;
		$date_show = $timeformat ? $timeformat : $yl_datefm;
		$offset = $yl_timedf=='111' ? 0 : $yl_timedf;
		return gmdate($date_show,$timestamp+$offset*3600);
	}

	function readlog($filename,$offset = 1024000)
	{
		$readb = array();
		if($fp = @fopen($filename,"rb"))
		{
			flock($fp,LOCK_SH);
			$size = filesize($filename);
			$size > $offset ? fseek($fp,-$offset,SEEK_END): $offset = $size;
			$readb = fread($fp,$offset);
			fclose($fp);
			$readb = str_replace("\n","\n<:flpm:>",$readb);
			$readb = explode("<:flpm:>",$readb);
			$count = count($readb);
			if($readb[$count - 1] == ''||$readb[$count - 1] == "\r")
			{
				unset($readb[$count - 1]);
			}
			if(empty($readb)){$readb[0] = "";}
		}
		return $readb;
	}

	function ck_iconv($source_lang, $target_lang, $source_string = '')  //转码
	{
		static $chs = NULL;

		/* 如果字符串为空或者字符串不需要转换，直接返回 */
		if ($source_lang == $target_lang || $source_string == '' || preg_match("/[\x80-\xFF]+/", $source_string) == 0)
		{
			return $source_string;
		}

		if($chs === NULL)
		{
			require_once PATH_APPLICATION . '/pm_iconv.class.php';
			$chs = new Chinese;
			$chs->Chinese();
		}

		return strtolower($target_lang) == 'utf-8' ? addslashes(stripslashes($chs->Convert($source_lang, $target_lang, $source_string))) : $chs->Convert($source_lang, $target_lang, $source_string);
	}

	function ck_iconv_deep($source_lang, $target_lang, $value)
	{
		if(empty($value))
		{
			return $value;
		}
		else
		{
			if(is_array($value))
			{
				foreach ($value as $k=>$v)
				{
					$value[$k] = ck_iconv_deep($source_lang, $target_lang, $v);
				}
				return $value;
			}
			elseif (is_string($value))
			{
				return ck_iconv($source_lang, $target_lang, $value);
			}
			else
			{
				return $value;
			}
		}
	}

	function PostLog($log)
	{
		$data='';
		foreach($log as $key => $val)
		{
			if(is_array($val))
			{
				$data .= "$key=array(".PostLog($val).")";
			}else
			{
				$val = str_replace(array("\n","\r","|"),array('','','&#124;'),$val);
				if($key == 'password' || $key == 'check_pwd')
				{
					$data .= "$key=***, ";
				}else
				{
					$data .= "$key=$val, ";
				}
			}
		}
		return $data;
	}

	function randstr($lenth)
	{
		mt_srand((double)microtime() * 1000000);
		$randval = '';
		for($i = 0; $i < $lenth; $i++)
		{
			$randval .= mt_rand(0, 9);
		}
		$randval = substr(md5($randval), mt_rand(0, 32 - $lenth), $lenth);
		return $randval;
	}

	function num_rand($lenth)
	{
		mt_srand((double)microtime() * 1000000);
		for($i = 0;$i < $lenth; $i++)
		{
			$randval.= mt_rand(1,9);
		}
		return $randval;
	}


	function pmurlencode($url)
	{
		$url_a = substr($url, strrpos($url,'?') + 1);
		substr($url,-1) == '&' && $url = substr($url,0,-1);
		parse_str($url_a,$url_a);
		$source='';
		foreach($url_a as $key => $val)
		{
			$source .= $key.$val;
		}
		$posthash = substr(md5($source.USERNAME.ADMINUSERID.SYSSTRKEY),0,8);
		$url .= "&verify=$posthash";
		return $url;
	}

	function FormCheck($pre,$url,$add)
	{
		$pre = stripslashes($pre);
		$add = stripslashes($add);
		return "<form{$pre} action=\"".EncodeUrl($url)."&\"{$add}>";
	}

	function PostCheck($verify)
	{
		global $yl_hash,$admin_name,$admin_gid;
		$source='';
		foreach($_GET as $key => $val)
		{
			if($key!='verify')
			{
				$source .= $key.$val;
			}
		}
		if($verify!=substr(md5($source.USERNAME.ADMINUSERID.SYSSTRKEY),0,8))
		{
			adminmsg('illegal_request');
		}else
		{
			return true;
		}
	}

// 引用文件安全检查
	function Pcv($filename,$ifcheck=1)
	{
		strpos($filename,'http://')!==false && exit('Forbidden');
		strpos($filename,'https://')!==false && exit('Forbidden');
		strpos($filename,'ftp://')!==false && exit('Forbidden');
		strpos($filename,'ftps://')!==false && exit('Forbidden');
		strpos($filename,'php://')!==false && exit('Forbidden');
		$ifcheck && strpos($filename,'..')!==false && exit('Forbidden');
		return $filename;
	}

	// 计算文件大小
	function bytes_to_string( $bytes )
	{
		if (!preg_match("/^[0-9]+$/", $bytes)) return 0;
		$sizes = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
		$extension = $sizes[0];
		for( $i = 1; ( ( $i < count( $sizes ) ) && ( $bytes >= 1024 ) ); $i++ )
		{
			$bytes /= 1024;
			$extension = $sizes[$i];
		}
		return round( $bytes, 2 ) . ' ' . $extension;
	}

	//计算目录大小

	function dirsize($dir)
	{
		$dh = opendir($dir);
		$size = 0;
		while($file = readdir($dh))
		{
			if($file != '.' and $file != '..')
		 	{
				$path = $dir."/".$file;
				if(@is_dir($path))
			 	{
					$size += dirsize($path);
				}else
			 	{
					$size += filesize($path);
				}
			}
		}
		@closedir($dh);
		return $size;
	}

	function getstrstr($str, $str1, $str2, $type = 0)
	{
		$len1 = strpos($str, $str1);
		$len2 = strpos($str, $str2);
		$str = substr($str, $len1 + strlen($str1), $len2 - $len1 - strlen($str1));
		if ($type == 0)
		{
			Return trim(strip_tags($str));
		}
		else
		{
			Return trim($str);
		}
	}

	function get_domain($url)
	{
		$tmp = @parse_url($url);
		return (!empty($tmp['host'])) ? strtolower($tmp['host']) : false;
	}

	function array_var (&$from, $name, $default = null, $and_unset = false)
	{
		if (is_array($from))
		{
			if ($and_unset)
			{
				if (array_key_exists($name, $from))
				{
					$result = $from[$name];
					unset($from[$name]);
					return $result;
				} // if
			}
			else
			{
				return array_key_exists($name, $from) ? $from[$name] : $default;
			} // if
		} // if
		return $default;
	}

	function get_client_ip ()
	{
		static $realip = NULL;
		if ($realip !== NULL)
		{
			return $realip;
		}
		if(isset($_SERVER))
		{
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			/* 取X-Forwarded-For中第?个非unknown的有效IP字符? */
				foreach ($arr as $ip)
				{
					$ip = trim($ip);
					if ($ip != 'unknown')
					{
						$realip = $ip;
						break;
					}
				}
			}
			elseif (isset($_SERVER['HTTP_CLIENT_IP']))
			{
				$realip = $_SERVER['HTTP_CLIENT_IP'];
			}
			else
			{
				if (isset($_SERVER['REMOTE_ADDR']))
				{
					$realip = $_SERVER['REMOTE_ADDR'];
				}
				else
				{
					$realip = '0.0.0.0';
				}
			}
		}
		else
		{
			if (getenv('HTTP_X_FORWARDED_FOR'))
			{
				$realip = getenv('HTTP_X_FORWARDED_FOR');
			}
			elseif (getenv('HTTP_CLIENT_IP'))
			{
				$realip = getenv('HTTP_CLIENT_IP');
			}
			else
			{
				$realip = getenv('REMOTE_ADDR');
			}
		}
		preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
		$realip = ! empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
		return $realip;
	}

	function cmp($a, $b)
	{
		if($a['order'] == $b['order'])
		{
			return 0;
		}
		return ($a['order'] < $b['order']) ? -1 : 1;
	}

	function get_key($keyName)
	{
		return do_command('sysctl', "-n $keyName");
	}

	function find_command($commandName)
	{
		$path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
		foreach($path as $p)
		{
			if (@is_executable("$p/$commandName")) return "$p/$commandName";
		}
		return false;
	}

	function do_command($commandName, $args)
	{
		$buffer = "";
		if (false === ($command = find_command($commandName))) return false;
		if ($fp = @popen("$command $args", 'r'))
		{
			while (!@feof($fp))
			{
				$buffer .= @fgets($fp, 4096);
			}
			return trim($buffer);
		}
		return false;
	}

	function cutstr($string, $length, $dot = ' ...')
	{
		global $charset;
		$charset = 'utf-8';
		if(strlen($string) <= $length)
		{
			return $string;
		}

		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

		$strcut = '';
		if(strtolower($charset) == 'utf-8')
		{
			$n = $tn = $noc = 0;
			while($n < strlen($string))
		 	{
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126))
				{
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223)
				{
					$tn = 2; $n += 2; $noc += 2;
				} elseif(224 <= $t && $t <= 239)
				{
					$tn = 3; $n += 3; $noc += 2;
				} elseif(240 <= $t && $t <= 247)
				{
					$tn = 4; $n += 4; $noc += 2;
				} elseif(248 <= $t && $t <= 251)
				{
					$tn = 5; $n += 5; $noc += 2;
				} elseif($t == 252 || $t == 253)
				{
					$tn = 6; $n += 6; $noc += 2;
				} else
				{
					$n++;
				}

				if($noc >= $length)
			 	{
					break;
				}

			}
			if($noc > $length)
			{
				$n -= $tn;
			}

			$strcut = substr($string, 0, $n);

		}else
		{
			for($i = 0; $i < $length; $i++)
			{
				$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			}
		}

		$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

		return $strcut.$dot;
	}

	function autokey($key,$foo)
	{
		$arrs = explode(' ',$key);
		foreach($arrs AS $v)
		{
			$foo = str_ireplace($v,"<font style=\"color:red\">$v</font>",$foo);
		}
		return $foo;
	}

	function anydaytime($num)
	{ //0为今天 1为昨天
		if($num < 0)
		{
			return false;
		}
		if(!$num)
		{
			$num = 0;
		}

		$gettimearr = getdate();
		$endtimea = mktime(23, 59, 59, $gettimearr['mon'], $gettimearr['mday'] - $num, $gettimearr['year']);
		$strattimea = mktime(0, 0, 0, $gettimearr['mon'], $gettimearr['mday'] - $num, $gettimearr['year']);
		$arrs = array();
		$arrs['s'] = $strattimea;
		$arrs['e'] = $endtimea;
		return $arrs;

	}

	function admintitle($id)
	{
		$data = array('1'=>'二级管理员','2'=>'普通管理员','8' => '特殊管理员','9'=>'CP管理员');
		return $data[$id];
	}

	function doqueryurl()
	{
		$REQUEST_URI = addslashes($_SERVER['QUERY_STRING']);
		$record_URI = str_replace('|', '&#124;', Char_cv($REQUEST_URI));
		$arr = explode('&',$record_URI);
		return $arr;
	}


  function strip_selected_tags($text, $tags = array())
  {
		$args = func_get_args();
		$text = array_shift($args);
		$tags = func_num_args() > 2 ? array_diff($args,array($text)) : (array)$tags;
		foreach ($tags as $tag)
		{
			if( preg_match_all( '/<'.$tag.'[^>]*>([^<]*)<\/'.$tag.'>/iu', $text, $found) )
			{
				$text = str_replace($found[0],$found[1],$text);
			}
		}

		return preg_replace( '/(<('.join('|',$tags).')(\n|\r|.)*\/>)/iu', '', $text);
	}

	function getlastweek()
	{
		$date = getdate();
		$arr = array();
		if($date['wday'] == 0)
		{
			$arr['s'] = strtotime('-7 days');
			$arr['e'] = strtotime('-1 days');
		}else
		{
			$diff = $date['wday'];
			$diff1 = 7 + $diff;
			$diff2 = 1 + $diff;
			$arr['s'] = strtotime("-$diff1 days");
			$arr['e'] = strtotime("-$diff2 days");
		}
		return $arr;
	}

	function getmonths($mon = false)
	{
		$arr =array();
		$date = getdate();
		if(!$mon)
		{
			$arr['s'] = mktime(0,0,0,$date['mon'],1,$date['year']);
			$arr['e'] = mktime(23,59,59,$date['mon'],30,$date['year']);
		}else
		{
			$arr['s'] = mktime(0,0,0,$mon,1,$date['year']);
			$arr['e'] = mktime(23,59,59,$mon,31,$date['year']);
		}
		return $arr;
	}

	function globalrt($sys_con)  //公共权限
	{
		$r =
		($sys_con['c'] == 'feedback' AND $_GET['a'] == 'addfeedback') ||
		($sys_con['c'] == 'member' AND $_GET['a'] == 'modif') ||
		($sys_con['c'] == 'ajaxdo' AND $_GET['a'] == 'save_todolist') ||
		($sys_con['c'] == 'member' AND $_GET['a'] == 'msglist') ||
		($sys_con['c'] == 'member' AND $_GET['a'] == 'loginhistory') ||
		($sys_con['c'] == 'pmsys' AND $_GET['a'] == 'getusermsgstatus') ||
		($sys_con['c'] == 'ajaxdo' AND $_GET['a'] == 'get_todolist');
		return $r;
	}


	function stric($k,$char ='UTF-8')
	{
		$nk = ($contentscharset = mb_detect_encoding($k, "ASCII, UTF-8, GB2312, GBK")) == "$char" ? $k : iconv($contentscharset, "$char", $k);
		return $nk;
	}

	function make_json_response($content='', $error="0", $message='', $append=array())
	{
		$res = array('error' => $error, 'message' => $message, 'content' => $content);
  	if (!empty($append))
  	{
  		foreach ($append AS $key => $val)
    	{
    		$res[$key] = $val;
    	}
  	}
  	$val = json_encode($res);
  	exit($val);
	}

	function make_json_result($content, $message='', $append=array())
	{
		make_json_response($content, 0, $message, $append);
	}

	function make_json_error($msg)
	{
		make_json_response('', 1, $msg);
	}

	function pm_header($string, $replace = true, $http_response_code = 0)
	{
		$string = str_replace(array("\r", "\n"), array('', ''), $string);
		if (preg_match('/^\s*location:/is', $string))
  	{
  		@header($string . "\n", $replace);
			exit();
  	}
		if (empty($http_response_code) || PHP_VERSION < '4.3')
  	{
  		@header($string, $replace);
  	}
  	else
  	{
  		@header($string, $replace, $http_response_code);
  	}
	}

	function r_unserialize($str, $array = array(), $i = 1)
  {
  	$str = explode("\n$i\n", $str);
    foreach ($str as $key => $value)
    {
    	$k = substr($value, 0, strpos($value, "\t"));
      $v = substr($value, strpos($value, "\t") + 1);
      if (strpos($v, "\n") !== false)
      {
      	$next = $i + 1;
       	$array[$k] = r_unserialize($v, $array[$k], $next);
			}elseif(strpos($v, "\t") !== false)
      {
      	$array[$k] = r_array($array[$k], $v);
      }
      else
      {
      	$array[$k] = $v;
      }
    }
    return $array;
	}

	function r_array($array, $string)
  {
  	$k = substr($string, 0, strpos($string, "\t"));
    $v = substr($string, strpos($string, "\t") + 1);
    if (strpos($v, "\t") !== false)
    {
    	$array[$k] = r_array($array[$k], $v);
    }
    else
    {
    	$array[$k] = $v;
    }
    return $array;
	}

	function r_serialize($array, $ret = '', $i = 1)
  {
  	if(!is_array($array))
    {
    	return null;
    }
    foreach($array as $k => $v)
    {
    	if (is_array($v))
      {
      	$next = $i + 1;
        $ret .= "$k\t";
        $ret = r_serialize($v, $ret, $next);
        $ret .= "\n$i\n";
      }
      else
      {
      	$ret .= "$k\t$v\n$i\n";
      }
    }
 		if (substr($ret, -3) == "\n$i\n")
    {
    	$ret = substr($ret, 0, -3);
    }
    return $ret;
	}

	function forrightserialize($data)
	{
		$ndata = array();
		if(is_array($data))
		{
			foreach($data AS $k => $v)
			{
				$ndata[$v] = 1;
			}
		}else
		{
			$ndata[$data] = 1;
		}
		return $ndata;
	}

	function forunrightserialize($data)
	{
		$ndata = array();
		foreach($data AS $k => $v)
		{
			$ndata[] = $k;
		}
		if(count($ndata) == 1 && $ndata[0] === 0)
		{
			return array();
		}
		return $ndata;
	}

	function oralceinit($type = 1)  //oralce数据库建立
	{
		require_once PATH_APPLICATION . '/pm_oracle.class.php';
		switch($type)
		{
			case 1:
				$dbdata = $GLOBALS['databaseo1'];  //统计库 243  数据仓库
				break;
			case 2:
				$dbdata = $GLOBALS['databaseo2'];   //业务主库 188
				break;
			case 3:
				$dbdata = $GLOBALS['databaseo3'];  //业务从库 189
				break;
			case 4:
				$dbdata = $GLOBALS['databaseo4'];  //业务从库 189
				break;
			case 5:
				$dbdata = $GLOBALS['databasegamebi']; //gamebi
				break;
			default:
				return null;
		}
		$db = new ORACLE;
		$db->Connect($host = $dbdata['db_host'], $user=$dbdata['db_user'], $pass=$dbdata['db_pass'], $mode = OCI_DEFAULT, $type = ORA_CONNECTION_TYPE_DEFAULT);
		$db->SetAutoCommit($mode = true);
		return $db;
	}

	function includesqlfile()   //载入数据库文件
	{
		$sqlfile = PATH_CONFIG . '/cfg_dbfile.php';
		$filestatus = file_exists($sqlfile) && $fileline = countfileline($sqlfile);
		$fileline =  $fileline ? $fileline - 1 : 0;
		$dirs = scandir(PATH_DB);
		$newline = count($dirs);
		$filestr = '';
		if($filestatus || $newline > $fileline)
		{
			$filestr .= '<?php'."\n";
			foreach($dirs AS $v)
			{
				if(($v !== '.') && ($v !== '..'))
				{
					$newdir = "PATH_DB . "."'/$v'";
					$filestr .= "\trequire_once($newdir);\n";
				}
			}
			$fp = @fopen($sqlfile, 'wb');
			fwrite($fp,$filestr);
			fclose($fp);
		}
		return $sqlfile;
	}

	function includelangfile($type = 'zh_cn',$langname = null)
	{
		$langpath = PATH_LANG . '/' . $type;
		if(!$langname)
		{
			include $langpath . '/privilege.php';
			include $langpath . '/common.php';
			include $langpath . '/index.php';
			include $langpath . '/users.php';
		}else
		{
			include $langpath . '/'.$langname.'.php';
		}
		return $_LANG;
	}

	function timediff($start,$end)
	{
		return ceil(($end - $start) / 86400);
	}

	function get_pwd_salt()
	{
		$salt = substr(uniqid(rand()), -6);
		return $salt;
	}

  function formatpassword ($password, $salt)
  {
 		return md5 (md5 ($password) . $salt);
	}

	function noticeaccessto($method)
	{
		$explode = explode('::',$method);
		$thisaction = $_GET['a'];
		if($thisaction == $explode[1])
		{
			mod_login::message('禁止访问');
		}
	}

	function sefunc($type = 'day')
	{
		$arr =array();
		$date = getdate();
		switch($type){
			case "day":
					$arr['s'] = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
					$arr['e'] = mktime(23,59,59,$date['mon'],$date['mday'],$date['year']);
					break;
			case "month":
					$arr['s'] = mktime(0,0,0,$date['mon'],1,$date['year']);
					$arr['e'] = mktime(23,59,59,$date['mon'],30,$date['year']);
					break;
			case "lday":
					$arr['s'] = mktime(0,0,0,$date['mon'],$date['mday'] - 1,$date['year']);
					$arr['e'] = mktime(23,59,59,$date['mon'],$date['mday'] - 1,$date['year']);
					break;
		}
		return $arr;
	}

	function ffile_get_contents($url)
	{
		$ctx = stream_context_create(
			array(
        'http' => array(
				'timeout' => 5 //设置一个超时时间，单位为秒
				)
			)
		);
		$r = file_get_contents($url, 0, $ctx);
		unset($ctx);
		return $r;
	}

	function initupload($path = '',$checktype = false,$filetype = '')
	{
		require_once PATH_APPLICATION . '/pm_ksupload.php';
		$path = $path ? $path : PATH_ADMIN . '/data/backup';
		if($checktype)
		{
			if(!$filetype) return 'input file type';
		}
		$filetype = $filetype ? $filetype : 'image';
		$_config = array
		(
			'path' => $path,
			'checkType' => $checktype,
			'fileType' => $filetype,
			'cutHeight' => 100,
			'cutWidth' => 100,
			'isCut' => FALSE,
		);
		$handle = new upload($_config);
		return $handle;
	}

	function my_exec($cmd, $input='')
	{
		$proc = proc_open($cmd, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
		fwrite($pipes[0], $input);
		fclose($pipes[0]);
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
  	$rtn = proc_close($proc);
 	 return array('stdout' => $stdout,
               'stderr' => $stderr,
               'return' => $rtn
               );
	}

	function readApkInfoFromFile($apk_file,$get_icon= true)  //APK解包
	{
		$aapt = '/usr/bin/aapt';
		if(substr($apk_file, 0,7)=='http://')
		{
			$tmp_apk = "/tmp/".md5(microtime()).".apk";
			exec('/usr/bin/wget '.$apk_file.' -O '.$tmp_apk." -t 1",$_out,$_return);
			if(filesize($tmp_apk)>0)
			{
				$apk_file = $tmp_apk;
			}
		}
		exec("{$aapt} d badging {$apk_file}",$out,$return);
		if($return == 0)
		{
			$str_out = implode("\n", $out);
			$out = null;
			#icon
			if($get_icon)
			{
				$pattern_icon = "/icon='(.+)'/isU";
				preg_match($pattern_icon, $str_out,$m);
				$info['icon']= $m[1];
				if($info['icon'])
				{
					if($tmp_apk)
					{
						$command = "unzip {$tmp_apk} {$info['icon']} -d /tmp";
					}
					else
					{
						$command = "unzip {$apk_file} {$info['icon']} -d /tmp";
					}
					//@mkdir("/data0/www/html/gonghui/admin/cplan/apkicon/");
					//exec("mv /tmp/{$info['icon']} /data0/www/html/gonghui/admin/cplan/apkicon/");
					//@file_put_contents("/data0/www/html/gonghui/admin/cplan/".$info['icon']);
					exec($command);
				}
			}
			#对外显示名称
			$pattern_name = "/application: label='(.*)'/isU";
			preg_match($pattern_name, $str_out,$m);
			$info['lable']=$m[1];
			#内部名称,软件唯一的
			$pattern_sys_name = "/package: name='(.*)'/isU";
			preg_match($pattern_sys_name, $str_out,$m);
			$info['sys_name']=$m[1];
			#内部版本名称,用于检查升级
			$pattern_version_code = "/versionCode='(.*)'/isU";
			preg_match($pattern_version_code, $str_out,$m);
			$info['version_code']=$m[1];
      #对外显示的版本名称
			$pattern_version = "/versionName='(.*)'/isU";
			preg_match($pattern_version, $str_out,$m);
      $info['version']=$m[1];
			#系统
			$pattern_sdk = "/sdkVersion:'(.*)'/isU";
			preg_match($pattern_sdk, $str_out,$m);
			$info['sdk_version']=$m[1];
			if($info['sdk_version'])
			{
				$sdk_names = array(3=>"1.5",4=>"1.6",7=>"2.1",8=>"2.2",10=>'2.3.3',11=>"3.0",12=>"3.1",13=>"3.2",14=>"4.0");
				if($sdk_names[$info['sdk_version']])
				{
					$info['os_req'] = "Android {$sdk_names[$info['sdk_version']]}";
				}
			}
			#权限
			$pattern_perm = "/uses-permission:'(.*)'/isU";
			preg_match_all($pattern_perm, $str_out,$m);
			if($m)
			{
				$cnt = count($m[1]);
				for($i=0;$i<$cnt;$i++)
				{
					$info['permissions'] .= $info['permissions']?"\n".$m[1][$i]:$m[1][$i];
				}
			}
			#需要的功能(硬件支持)
			$pattern_features = "/uses-feature:'(.*)'/isU";
			preg_match_all($pattern_features, $str_out,$m);
			if($m)
			{
				$cnt = count($m[1]);
				for($i=0;$i<$cnt;$i++)
				{
					$info['features'] .= $info['features']?"\n".$m[1][$i]:$m[1][$i];
				}
			}
			$info['apk_info'] = $str_out;
			if($tmp_apk)
			{
				unlink($tmp_apk);
			}
			return $info;
		}
		if($tmp_apk)
		{
			unlink($tmp_apk);
		}
	}

function mkdir_recursive($pathname, $mode)
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

	is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname), $mode);
	return is_dir($pathname) || @mkdir($pathname, $mode);
}

	function ck_rmdir($dir)
	{
		$dir = str_replace(array('..', "\n", "\r"), array('', '', ''), $dir);
   	$ret_val = false;
   	if (is_dir($dir))
    {
    	$d = @dir($dir);
      if($d)
      {
      	while (false !== ($entry = $d->read()))
				{
        	if($entry!='.' && $entry!='..')
          {
         		$entry = $dir.'/'.$entry;
            if(is_dir($entry))
            {
            	ck_rmdir($entry);
           	}
            else
            {
            	@unlink($entry);
            }
        	}
        }
				$d->close();
				$ret_val = rmdir($dir);
			}
		}else
    {
    	$ret_val = unlink($dir);
    }
    return $ret_val;
	}


	function &cache_server($cachetype = false)
	{
    	include PATH_APPLICATION.'/pm_cache.class.php';
    	static $CS = null;
    	if ($CS === null)
    	{
       	 switch ($cachetype)
        	{
           	 case '1':
               	 $CS = new MemcacheServer(array(
                    'host'  => MEMCACHE_HOST,
                    'port'  => MEMCACHE_PORT,
                ));
            break;
           	 case '3':
               	 $CS = new RedisServer(array(
                    'host'  => REDIS_HOST,
                    'port'  => REDIS_PORT,
                ));
            break;
						 default:
                $CS = new PhpCacheServer;
                $CS->set_cache_dir(PATH_DATA . '/cache');
            break;
      	  }
    	}

   	 return $CS;
	}

	function addoplogmg($data)  //添加管理员操作记录到mongodb
	{
		include(PATH_APPLICATION . '/pm_mgdb.php');
		$mongo = new Mongo("mongodb://192.168.101.212:27017/pmdbuser:pmdbuser");
		Db::addConnection($mongo, 'productmanage');
		Db::insert('oplogs',$data);
	}

	function mongoinit($dbname,$collection,$auth = false)
	{
		$mongourl = $auth ? "mongodb://192.168.101.212:27017/{$auth}" : "mongodb://192.168.101.212:27017";
		$mgdb = new Mongo($mongourl);
		$db = $mgdb->selectDB($dbname);
		$col =  new MongoCollection($db, $collection);
		return $col;
	}

	function get_weather()
	{
		$ret = ffile_get_contents('http://m.weather.com.cn/data/101010100.html');
		$wdata = json_decode($ret,1);
		$wdata = $wdata['weatherinfo'];
		$w = array();
		$w['today'] = array('t' => $wdata['temp1'],'text' => $wdata['weather1'],'wind' => $wdata['wind1'],'wg' => $wdata['fl1']);
		$w['next'] = array('t' => $wdata['temp2'],'text' => $wdata['weather2'],'wind' => $wdata['wind2'],'wg' => $wdata['fl2']);
		return $w;
	}

	function get_pagestart($page, $pagesize)
	{
		return (($page > 0 ) ? $page - 1 : 0) * $pagesize;
	}

function get_shorturl_charset()  //定义字符串
{
	static $charset = null;
	if( $charset !== null )
	{
		return $charset;
	}
	define('FL_URL_CONVERT',64);
	if( !defined('FL_URL_CONVERT') )
 	{
		$charset = '0123456789abcdefghijklmnopqrstuvwxyz';
	} else
	{
		switch( FL_URL_CONVERT )
		{
			case 36:
				$charset = '0123456789abcdefghijklmnopqrstuvwxyz';
				break;
			case 62:
			case 64:
				$charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
		}
	}
	return $charset;
}

function int2string( $num, $chars = null ) //数字转字符串
{
	if( $chars == null )
		$chars = get_shorturl_charset();
	$string = '';
	$len = strlen( $chars );
	while( $num >= $len )
 	{
		$mod = bcmod( $num, $len );
		$num = bcdiv( $num, $len );
		$string = $chars[$mod] . $string;
	}
	$string = $chars[$num] . $string;
	return $string;
}

function string2int( $string, $chars = null )  //字符串转为数字
{
	if( $chars == null )
		$chars = get_shorturl_charset();
	$integer = 0;
	$string = strrev( $string  );
	$baselen = strlen( $chars );
	$inputlen = strlen( $string );
	for ($i = 0; $i < $inputlen; $i++)
 	{
		$index = strpos( $chars, $string[$i] );
		$integer = bcadd( $integer, bcmul( $index, bcpow( $baselen, $i ) ) );
	}
	return $integer;
}

	function cws($str)
	{    //切词函数
		$cws = scws_new();
		$cws->set_multi(16);
		$cws->set_ignore(true);
		$cws->set_duality(true);
		$cws->send_text($str);
		$wod = array();
		while ($tmp = $cws->get_result())
		{
			foreach($tmp as $v)
			{
				$wod[] = $v;
			}
		}
		return $wod;
	}

	function c_xml($url)
	{
		include PATH_LIB . '/xml.class.php';
		$xmls = file_get_contents($url);
		$xml = xml2array($xmls);
		return $xml;
	}

	function xml2arrayss($url, $get_attributes = 1, $priority = 'tag')
	{
		$contents = "";
		if (!function_exists('xml_parser_create'))
		{
			return array ();
		}
		$parser = xml_parser_create('');
		if (!($fp = @ fopen($url, 'rb')))
		{
			return array ();
		}
		while (!feof($fp))
		{
			$contents .= fread($fp, 8192);
		}
		fclose($fp);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);
		xml_parser_free($parser);
		if (!$xml_values)
		{
			return array();
		}
		$xmlarray = array();
		$pre = '';
		foreach($xml_values AS $key => $value)
		{
			print_r($value);
		}
		debug($xmlarray);
	}

	function gensign($tokenLen = 60)
	{
		if (file_exists('/dev/urandom'))
		{
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 100) . uniqid(mt_rand(), true);
		}else
		{
           $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
		}
		return substr(hash('sha512', $randomData), 0, $tokenLen);
	}


     function dump($val)
    {
        echo "<pre>";
        print_r($val);
        echo "</pre>";
    }




    function genTree($items) {
        $tree = array(); //格式化好的树
        foreach ($items as $item)
            if (isset($items[$item['pid']]))
                $items[$item['pid']]['son'][] = &$items[$item['id']];
            else
                $tree[] = &$items[$item['id']];
        return $tree;
    }





    function  payApi($method,$data,$action){
        is_array($data)?krsort($data):'';
        $str='';
        if (!empty($data)) {
            foreach ($data as $key=> $value) {
                if($key == 'uname') continue;
                $str.=$value;
            }
        }
        $str.=SECURE_KEY;
        // if(USERNAME=='cptest'){echo $str.'<br/>';}
        //$data['sign']=md5(rawurlencode($str));
        $data['sign']=md5($str);
        $data =http_build_query($data);
        $ch = curl_init();
        if ($method == 'get') {
            //echo PAY_API.$action.'/?'.$data;
            curl_setopt($ch, CURLOPT_URL, PAY_API.$action.'/?'.$data);
        }
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_URL, PAY_API.$action.'/');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //true  返回值存到变量中。
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $respone = trim(curl_exec($ch));
        curl_close($ch);
        return $respone;
    }



    function dataformat($data)
    {
        $result=json_decode($data,true);
        $newkey=is_array($result['column'])?array_flip($result['column']):'';
        unset($result['column']);
        is_array($newkey)?ksort($newkey):'';
        if(empty($result['data'])) return FALSE;
        foreach ($result['data'] as $key=>&$value) {
            $value = array_combine($newkey,$value);
        }
        return $result;

    }
