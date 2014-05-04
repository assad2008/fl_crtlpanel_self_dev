<?php
/**
* @file mod_login.php
* @synopsis  登录模块
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-19 10:34:27
 */

!defined('PATH_ADMIN') && exit('Forbidden');

class mod_login
{

    protected $user = null;
    protected $securimage = null;
    protected static $instance;

    public function __construct()
    {

    }

    public static function instance()
    {
        if (self::$instance === null)
        {
            self::$instance = new mod_login();
        }
        return self::$instance;
    }

    public function authenticate($data,$type = null)
		{
    	$timestamp = time();					//当前时间戳
      $admin_recordfile = PATH_ADMIN . "/data/log/admin_log.php";		//用户登录log记录文件
      $F_count = F_L_count($admin_recordfile, 2000);
      $L_T = 3600 - ($timestamp - @filemtime($admin_recordfile)); //20分钟内
      $L_left = 15 - $F_count;
      if($F_count > 1500000000 && $L_T > 0)
      {// 登录失败次数过多检测
      	throw new Exception("连续登陆错误超过15次,请20分钟后再试.");
			}
			$s = new mod_captcha(PATH_ADMIN.'/data/captcha/');
			$s->session_word = 'flpm_login';
      $username = trim(array_var($data, "username"));
      $password = trim(array_var($data, "password"));
      $captcha = trim(array_var($data, "captcha"));
			$logintype = trim(array_var($data, "logintype"));
			$cpuinfo = mod_member::get_oneamdinbyusername($username);
			if(in_array($cpuinfo['level'],array(8,9)))
			{
				$logintype = 1;
			}
			$password = $password;
      if (VERIFY_CODE == 1 && $type == null)
      {
      	if ($captcha == '')
        {
        	self::log_error_login($username, $password);
          throw new Exception('请输入验证码！');
        }
        else//如果需要,再次添加关闭验证码功能
				{
					if($logintype != 3)
					{
          	if (!$s->check_word($captcha))
            {
            	throw new Exception('请输入正确的验证码！');
						}
					}
        }
			}
			if($type == 'sso')
			{
				$logintype = 2;
			}
			if($logintype == 1)
			{
				$username = $username;
				$password = $password;
				if(!in_array($cpuinfo['level'],array(8,9))) throw new Exception('不允许此方式登录！');
			}elseif($logintype == 2)
			{
				$bossuser = mod_oracle::bossuser($username);
				$bossuser = $bossuser[0];
				if($type == null)
				{
					if(md5($password) != $bossuser['PASSWORD'])
					{
						throw new Exception('密码不正确！');
					}
				}else
				{
					if($password != $bossuser['PASSWORD'])
					{
						throw new Exception('密码不正确！');
					}
				}
				$bossname = $bossuser['LOGIN_NAME'];
				$vid = mod_member::getnamebybossname($bossname);
				if(!$vid)
				{
					$localuserinfo = mod_member::get_oneamdinbyusername($bossname);
					if(!$localuserinfo)
					{
						$addname = $bossname;
						$addtruename = $bossuser['NAME'];
						$addpassword = '111111';
						$addemail = $bossuser['EMAIL'];
						$addadminlevel = '2';
						$user_id = mod_member::member_add($addname, $addtruename,$addpassword,$addemail,$addadminlevel);
						$adddata['bossname'] = $bossuser['LOGIN_NAME'];
						mod_member::addbossuser($adddata,$user_id);
						$addright = mod_member::get_right('1');
						mod_member::saverights($addright,$addname);
						$addsubject = '飞流九天产品后台账户创建通知';
						$mailtxt = mailtxt($addname, $addpassword,$addemail);
						//$addemail && @mod_mail::send($addemail,$addname,$addsubject,$mailtxt,'html');
						$username = $addname;
						$password = md5($addpassword);
					}else
					{
						$data = array();
						$data['bossname'] = $bossname;
						mod_member::addbossuser($data,$localuserinfo['user_id']);
						$username = $localuserinfo['name'];
						$password = md5($localuserinfo['password']);
					}
					}else
					{
						$username = $vid['user_name'];
						$password = $vid['password'];
						$user_id = $vid['user_id'];
					}
				}elseif($logintype == 3)
				{
					$bossuser = mod_oracle::bossuser($username);
					$bossuser = $bossuser[0];
					if($username != $bossuser['LOGIN_NAME'])
					{
						return false;
					}

					if($password != $bossuser['PASSWORD'])
					{
						return false;
					}
				}
        if(self::verify_login_in($username, $password,$logintype))
        {
					$auth_key = self::get_user_agent();
					if($logintype == 1)
					{
						$auth_password = md5($password);
					}elseif($logintype == 2)
					{
						$auth_password = $password;
					}
          $auth_username = $username;
          self::update_login($user_id);
          $cookie_value = authcode($auth_username . ':' . $auth_key . ':' . $auth_password,$operation = 'ENCODE');
          $cookie_expire = time () + 7200; //20分钟
          $cook_pre = AUTH_KEY . '_admin_auth';
          $_COOKIE[$cook_pre] = $cookie_value;
          setcookie(AUTH_KEY . '_admin_auth', $cookie_value, $cookie_expire, PATH_COOKIE);
          return true;
        }
    	}

    public static function update_login($user_id)
    {
    	$ip = get_client_ip();
      $update = "UPDATE pm_admin_user SET last_login =".time()." ,last_ip='".$ip."'WHERE user_id='{$user_id}' LIMIT 1";
      pm_db::query($update);
    }

    public static function verify_login_in($username, $password,$logintype = 2)
    {
			if($logintype == 1)
			{
				$password_md5 = md5($password);
			}elseif($logintype == 2)
			{
				$password_md5 = $password;
			}elseif($logintype == 3)
			{
				$password_md5 = $password;
			}
      $select = "SELECT * FROM gamebi_admin_user WHERE `user_name`='$username' and `password`='$password_md5'";
      if(pm_db::query($select))
      {
      	$data = pm_db::num_rows();
        if ($data > 0)
        {
        	$auth_key = self::get_user_agent();
          $auth_password = $password;
          $auth_username = $username;
          $cookie_value = authcode($auth_username . ':' . $auth_key . ':' . $auth_password,$operation = 'ENCODE');
          $cookie_expire = time () + 7200; //20分钟
          $cook_pre = AUTH_KEY . '_admin_auth';
          $_COOKIE[$cook_pre] = $cookie_value;
          setcookie(AUTH_KEY . '_admin_auth', $cookie_value, $cookie_expire, PATH_COOKIE);
			defined('USERNAME') || define('USERNAME', $username);
			$row_info = pm_db::fetch_one();
			defined('TRUENAME') || define('TRUENAME', $row_info['truename']);
			defined('ADMINLEVEL') || define('ADMINLEVEL', $row_info['level']);
			defined('ADMINUSERID') || define('ADMINUSERID', $row_info['user_id']);
			defined('ISSUPERADMIN') || define('ISSUPERADMIN',$row_info['is_super']);
          if(ADMINLEVEL == 1)
          {
          	defined('If_manager') || define('If_manager', 1);
           	$rightset = array();
          }
          else
          {
          	defined('If_manager') || define('If_manager', 0);
            $rightset = array();
			$rightset = r_unserialize($row_info['rights']);
            $crmi = strpos($row_info['rights'],"crmhome_index");
            $summary = strpos($row_info['rights'],'summarybutton');
            defined('CRM') || define('CRM',$crmi);
            defined('SUMMARY') || define('SUMMARY',$summary);
            $sys_con = self::get_control();  //用户当前进行的操作
            if (empty($sys_con) || ($sys_con['c'] == 'login' )|| $sys_con['c'] == 'securimage'|| globalrt($sys_con) || $_GET['c'] == 'tweet')
                {

                }
                else //添加登陆模块
								{
									$if_auth = false;
                 	foreach ($rightset as $k => $v)
									{
										if(is_int(strrpos($k,'fl111')))
										{
											$ka = explode('fl111',$k);
											foreach($ka AS $v)
											{
												$rt = self::getrt($v);
												if($rt == $sys_con)
												{
													$if_auth = true;
												}
											}
										}else
										{
											$rt = self::getrt($k);
											if($rt == $sys_con)
											{
												$if_auth = true;
											}
										}
                 	}
                  if ($if_auth)
                  {
                   	return true;
                  }
                 	else
									{
										$sourceurl = $_SERVER['HTTP_REFERER'];
										if($sourceurl == 'http://gamebi.feiliu.com/?c=login&a=menu' || $sourceurl == '?c=login&a=menu') $sourceurl = '?c=login&a=welcome';
                   	$error = '抱歉，您没有对应的操作权限，如有所需，请联系管理员。';
                    $http = $sourceurl;
                    $stop_loop = 0; //没权限不跳转
                   	self::message($error, $http);
                    exit();
                  }
               }
						}
            $admin_recordfile = PATH_ADMIN . "/data/log/admin_log.php";
            $onlineip = get_client_ip();
            $new_record = "<?die;?>|$username|***|Logging Failed|$onlineip|" . time() . "|\n"; //登陆次数限制
            //writeover($admin_recordfile,$new_record,"ab");
						return true;
					}
          else
         	{
          	self::log_error_login($username, $password);
            setcookie(AUTH_KEY . '_admin_auth', 0, 100, '/', PATH_COOKIE);
            pm_tpl::assign('error', '账号或密码错误');
            pm_tpl::display('login');
            exit;
         }
       }
		}

    	/**
     	* 验证是否登录
     	* @return boolean
     	*/
    	public function is_login()
    	{
      	$cookie = array_var($_COOKIE, AUTH_KEY . '_admin_auth');
        if(isset($cookie) and !empty($cookie))
        {
					$cookie_data = explode(':', authcode($cookie,$operation = 'DECODE'));
          if(count($cookie_data) == 3)
          {
          	$current_cookie_auth = $cookie_data [1];
            if($current_cookie_auth == $this->get_user_agent())
            {
            	$current_cookie_username = $cookie_data [0];
              $current_cookie_password = $cookie_data [2];
              if(self::verify_login_in($current_cookie_username, $current_cookie_password))
              {
              	$post = $_POST;
                if ($_GET['c'] == 'config' && $_GET['a'] == 'mail')
                {
                	unset($post['config']['smtppass']);
                }
                $_postdata = $post ? PostLog($post) : '';
                $REQUEST_URI = '?' . $_SERVER['QUERY_STRING'];
                $onlineip = get_client_ip();
                $timestamp = time();
                $admin_recordfile = PATH_ADMIN . "/data/log/admin_log.php";
                $record_name = str_replace('|', '&#124;', Char_cv($current_cookie_username));
                $record_URI = str_replace('|', '&#124;', Char_cv($REQUEST_URI));
								$new_record = "<?die;?>|$record_name|$record_URI|$onlineip|$timestamp|$_postdata|\n";
								if(USERNAME != 'admin')
								{
									writeover($admin_recordfile, $new_record, "ab");
									$oparr = doqueryurl();
									$opuser = USERNAME;
									$opaday = date('Ymd',time());
									$opctrl = 'c='. $_GET['c'];
									$opact = 'a='. $_GET['a'];
									$opstring = $oparr[2];
									$times = time();
									if($_GET['a'] == 'welcome' || $_GET['a'] == 'menu' || $_GET['a'] == 'top' || $_GET['a'] == 'getusermsgstatus')
									{

									}else
									{
										$oplogdata = array();
										$oplogdata['aday'] = $opaday;
										$oplogdata['username'] = $opuser;
										$oplogdata['ctrl'] = $opctrl;
										$oplogdata['act'] = $opact;
										$oplogdata['query'] = $opstring;
										$oplogdata['timestamp'] = $times;
										//addoplogmg($oplogdata);
									pm_db::query("INSERT INTO gamebi_oplog (aday,username,ctrl,act,`query`,timestamp) VALUES ('$opaday','$opuser','$opctrl','$opact','$opstring','$times')");
									}
								}
							}
              return true;
            } // username_exists( )
          } //$current_cookie_auth
        }
      return false;
    }

    /**
     * 获取加密串
     * @return <type>
     */
    public static function get_user_agent()
    {
    	return md5(AUTH_KEY . '_' . $_SERVER ['HTTP_USER_AGENT']);
    }

    /**
     * 写入登陆错误日志
     * @param <type> $username
     * @param <type> $password
     */
    public function log_error_login($username, $password)
    {
    	$admin_recordfile = PATH_ADMIN . "/data/log/admin_log.php";
      $onlineip = get_client_ip();
      $new_record = "<?die;?>|$username|$password|Logging Failed|$onlineip|" . time() . "|\n"; //登陆次数限制
      writeover($admin_recordfile, $new_record, "ab");
    }

    /**
     *  系统信息
     *
     */
    public static function system_info()
    {
    	define("YES", "<span class='resYes'>YES</span>");
      define("NO", "<span class='resNo'>NO</span>");
      // 系统基本信息
      $serverapi = strtoupper(php_sapi_name());
      $phpversion = PHP_VERSION;
      $systemversion = explode(" ", php_uname());
      $sysReShow = 'none';
      switch (PHP_OS)
      {
      	case "Linux":
       	 $sysReShow = (false !== ($sysInfo = self::sys_linux())) ? "show" : "none";
       	 $sysinfo = $systemversion[0] . '   ' . $systemversion[2];
       	 break;
        case "FreeBSD":
        	$sysReShow = (false !== ($sysInfo = self::sys_freebsd())) ? "show" : "none";
          $sysinfo = $systemversion[0] . '   ' . $systemversion[2];
          break;
        default:
        	$sysinfo = $systemversion[0] . '  ' . $systemversion[1] . ' ' . $systemversion[3] . $systemversion[4] . $systemversion[5];
          break;
        }
        if($sysReShow == 'show')
        {
        	$pmemory = '共' . $sysInfo['memTotal'] . 'M, 已使用' . $sysInfo['memUsed'] . 'M, 空闲' . $sysInfo['memFree'] . 'M, 使用率' . $sysInfo['memPercent'] . '%';
        	$pmemorybar = $sysInfo['memPercent'];
          $swapmomory = '共' . $sysInfo['swapTotal'] . 'M, 已使用' . $sysInfo['swapUsed'] . 'M, 空闲' . $sysInfo['swapFree'] . 'M, 使用率' . $sysInfo['swapPercent'] . '%';
          $swapmemorybar = $sysInfo['swapPercent'];
          $syslaodavg = $sysInfo['loadAvg'];
        }
        pm_db::query("SELECT VERSION() AS dbversion");
        $mysql = pm_db::fetch_one();
        $mysql = $mysql['dbversion'];
        $phpsafe = self::getcon("safe_mode");
        $dispalyerror = self::getcon("display_errors");
        $allowurlopen = self::getcon("allow_url_fopen");
        $registerglobal = self::getcon("register_globals");
        $maxpostsize = self::getcon("post_max_size");
        $maxupsize = self::getcon("upload_max_filesize");
        $maxexectime = self::getcon("max_execution_time") . 's';
        $mqqsp = get_magic_quotes_gpc() === 1 ? YES : NO;
        $mprsp = get_magic_quotes_runtime() === 1 ? YES : NO;
        $zendoptsp = (get_cfg_var("zend_optimizer.optimization_level") || get_cfg_var("zend_extension_manager.optimizer_ts") || get_cfg_var("zend_extension_ts")) ? YES : NO;
        $iconvsp = self::isfun('iconv');
        $curlsp = self::isfun('curl_init');
        $gdsp = self::isfun('gd_info');
        $zlibsp = self::isfun('gzclose');
        $eaccsp = self::isfun('eaccelerator_info');
        $xcachesp = extension_loaded('XCache') ? YES : NO;
        $sessionsp = self::isfun("session_start");
        $cookiesp = isset($_COOKIE) ? YES : NO;
        $serverip = @gethostbyname($_SERVER['SERVER_NAME']);
        $serverip = $serverip == '' ? '' : "  ($serverip)";
        $systime = gmdate("Y年n月j日 H:i:s", time() + 8 * 3600);
        $phpversionsp = $phpversion > '5.0' ? YES : NO;
        $mysqlversionsp = $mysql['dbversion'] > '4.1' ? YES : NO;
        $dbasp = extension_loaded('dba') ? YES : NO;
        // 数据库大小
        $databasesize = 0;
        pm_db::query("SHOW TABLE STATUS");
        while ($rs = pm_db::fetch_one())
        {
        	$databasesize +=$rs['Data_length'] + $rs['Index_length'];
        }
        $databasesize = bytes_to_string($databasesize);
        //站点统计
        pm_db::query("SELECT count(*) as sum FROM gh_ghinfo");
        $rt = pm_db::fetch_one();
				$ghsum = $rt['sum'];

        //系统日志大小超过限制提示
        $noticemsg = '';
        if (@filesize(PATH_DATA . '/log/admin_log.php') > 409600)
        {
        	$noticemsg = '后台记录日志';
        }
        if (@filesize(PATH_DATA . '/log/php_error.log') > 409600)
        {
        	$data['noticemsg'] = 'PHP错误日志';
        }
        if (@filesize(PATH_DATA . '/log/mysql_error.php') > 409600)
        {
        	$data['noticemsg'] = 'mysql日志';
        }

        $data['serverip'] = $serverip;
        $data['systime'] = $systime;
        $data['sysinfo'] = $sysinfo;
        $data['phpversion'] = $phpversion;
        $data['dbversion'] = $mysql;
        $data['dispalyerror'] = $dispalyerror;
        $data['serverapi'] = $serverapi;
        $data['phpsafe'] = $phpsafe;
        $data['sessionsp'] = $sessionsp;
        $data['cookiesp'] = $cookiesp;
        $data['phpsafe'] = $phpsafe;
        $data['zendoptsp'] = $zendoptsp;
        $data['eaccsp'] = $eaccsp;
        $data['xcachesp'] = $xcachesp;
        $data['registerglobal'] = $registerglobal;
        $data['mqqsp'] = $mqqsp;
        $data['mprsp'] = $mprsp;
        $data['maxupsize'] = $maxupsize;
        $data['maxpostsize'] = $maxpostsize;
        $data['maxexectime'] = $maxexectime;
        $data['allowurlopen'] = $allowurlopen;
        $data['curlsp'] = $curlsp;
        $data['iconvsp'] = $iconvsp;
        $data['zlibsp'] = $zlibsp;
        $data['gdsp'] = $gdsp;
        $data['dbasp'] = $dbasp;
        $data['datasize'] = $databasesize;
        $data['ghsum'] = $ghsum;

        return $data;
    }

    /**
     * 跳转方法(独立与login模块)
     * @param <type> $message
		 * @param <type> $url
		 * @param       int         msg_type        消息类型， 0消息，1错误，2询a问
     * @param <type> $timeout     默认:2秒跳转
     * @param <type> $auto_redirect
     */
    public static function message($message, $links = null, $msg_type = 0,$auto_redirect = true, $timeout = 3)
		{
			$nlink;
			if($links == null)
			{
      	$nlink[0]['text'] = '点击返回';
      	$nlink[0]['href'] = 'javascript:history.go(-1)';
			}else
			{
				if(!is_array($links))
				{
       		$nlink[0]['text'] = '点击返回';
       	 	$nlink[0]['href'] = $links;
				}else
				{
					$nlink = $links;
				}
			}
			include PATH_LANG.'/zh_cn/common.php';
			pm_tpl::assign('lang',$_LANG);
			pm_tpl::assign('default_url', $nlink[0]['href']);
			pm_tpl::assign('msg_detail', $message);
			pm_tpl::assign('msg_type',$msg_type);
			pm_tpl::assign('timeout', $timeout);
			pm_tpl::assign('links',$nlink);
			pm_tpl::assign('ur_here','系统消息');
			pm_tpl::assign('auto_redirect', $auto_redirect);
			pm_tpl::display('message');
      exit();
    }

    public static function message_login($url = null, $timeout = 2000)
    {
    	if($url == null)
      {
      	$url = $_SERVER['HTTP_REFERER'];
      }
      pm_tpl::assign('url_page', $url);
      pm_tpl::assign('timeout', $timeout);
      pm_tpl::display('init');
      exit();
    }

    /**
     * 获取管理员用户名
     * @return <type>
     */
    public static function get_username()
    {
    	if(defined('USERNAME'))
      {
      	return USERNAME;
      }
      else
      {
      	$error = '您没有登录.';
        $http = './?c=login';
        $stop_loop = 0; //没权限不跳转
        self::message($error, $http, 3, $stop_loop);
        exit();
      }
    }
    /*
     * 得到控制器名称
     */

    public static function get_control()
		{
			$arr = array();
			$c = empty($_GET['c']) ? 'login' : $_GET['c'];
			$a = empty($_GET['a']) ? '' : $_GET['a'];
			$arr = array('c' => $c,'a'=> $a);
      return $arr;
    }

    public static function getcon($varName)
    {
    	switch ($res = get_cfg_var($varName))
      {
      	case 0:
        	return NO;
          break;
        case 1:
        	return YES;
          break;
        default:
        	return $res;
          break;
       }
    }

		public static function getrt($str)
		{
			if(!$str)
			{
				return false;
			}

			$rt = explode('_',$str);
			$arr = array();
			$arr['c'] = $rt['0'];
			if(count($rt) == 2)
			{
				$arr['a'] = $rt[1];
			}elseif(count($rt) == 3)
			{
				$arr['a'] = $rt[1].'_'.$rt[2];
			}elseif(count($rt) == 4)
			{
				$arr['a'] = $rt[1].'_'.$rt[2].'_'.$rt[3];
			}elseif(count($rt) == 1)
			{
				$arr['a'] = '';
			}
			return $arr;
		}

    /**
     * 检测函数是否存在
     * @param <type> $funName
     * @return <type>
     */
    public static function isfun($funName)
    {
    	return (false !== function_exists($funName)) ? YES : NO;
    }

    /**
     *  linux 系统信息
     * @return <type>
     */
    public static function sys_linux()
    {
        // CPU
        if (false === ($str = @file("/proc/cpuinfo")))
            return false;
        $str = implode("", $str);
        @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(.@\.]+)[\r\n]+/", $str, $model);
        //@preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
        @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
        if (false !== is_array($model[1]))
        {
            $res['cpu']['num'] = sizeof($model[1]);
            for ($i = 0; $i < $res['cpu']['num']; $i++)
            {
                $res['cpu']['detail'][] = "类型：" . $model[1][$i] . " 缓存：" . $cache[1][$i];
            }
            if (false !== is_array($res['cpu']['detail']))
                $res['cpu']['detail'] = implode("<br />", $res['cpu']['detail']);
        }


        // UPTIME
        if (false === ($str = @file("/proc/uptime")))
            return false;
        $str = explode(" ", implode("", $str));
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0)
            $res['uptime'] = $days . "天";
        if ($hours !== 0)
            $res['uptime'] .= $hours . "小时";
        $res['uptime'] .= $min . "分钟";

        // MEMORY
        if (false === ($str = @file("/proc/meminfo")))
            return false;
        $str = implode("", $str);
        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);

        $res['memTotal'] = round($buf[1][0] / 1024, 2);
        $res['memFree'] = round($buf[2][0] / 1024, 2);
        $res['memUsed'] = ($res['memTotal'] - $res['memFree']);
        $res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;

        $res['swapTotal'] = round($buf[3][0] / 1024, 2);
        $res['swapFree'] = round($buf[4][0] / 1024, 2);
        $res['swapUsed'] = ($res['swapTotal'] - $res['swapFree']);
        $res['swapPercent'] = (floatval($res['swapTotal']) != 0) ? round($res['swapUsed'] / $res['swapTotal'] * 100, 2) : 0;

        // LOAD AVG
        if (false === ($str = @file("/proc/loadavg")))
            return false;
        $str = explode(" ", implode("", $str));
        $str = array_chunk($str, 3);
        $res['loadAvg'] = implode(" ", $str[0]);

        return $res;
    }

    // freebsd 系统信息
    public static function sys_freebsd()
    {
        //CPU
        if (false === ($res['cpu']['num'] = get_key("hw.ncpu")))
            return false;
        $res['cpu']['detail'] = get_key("hw.model");

        //LOAD AVG
        if (false === ($res['loadAvg'] = get_key("vm.loadavg")))
            return false;
        $res['loadAvg'] = str_replace("{", "", $res['loadAvg']);
        $res['loadAvg'] = str_replace("}", "", $res['loadAvg']);

        //UPTIME
        if (false === ($buf = get_key("kern.boottime")))
            return false;
        $buf = explode(' ', $buf);
        $sys_ticks = time() - intval($buf[3]);
        $min = $sys_ticks / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0)
            $res['uptime'] = $days . "天";
        if ($hours !== 0)
            $res['uptime'] .= $hours . "小时";
        $res['uptime'] .= $min . "分钟";

        //MEMORY
        if (false === ($buf = get_key("hw.physmem")))
            return false;
        $res['memTotal'] = round($buf / 1024 / 1024, 2);
        $buf = explode("\n", do_command("vmstat", ""));
        $buf = explode(" ", trim($buf[2]));

        $res['memFree'] = round($buf[5] / 1024, 2);
        $res['memUsed'] = ($res['memTotal'] - $res['memFree']);
        $res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;

        $buf = explode("\n", do_command("swapinfo", "-k"));
        $buf = $buf[1];
        preg_match_all("/([0-9]+)\s+([0-9]+)\s+([0-9]+)/", $buf, $bufArr);
        $res['swapTotal'] = round($bufArr[1][0] / 1024, 2);
        $res['swapUsed'] = round($bufArr[2][0] / 1024, 2);
        $res['swapFree'] = round($bufArr[3][0] / 1024, 2);
        $res['swapPercent'] = (floatval($res['swapTotal']) != 0) ? round($res['swapUsed'] / $res['swapTotal'] * 100, 2) : 0;


        return $res;
    }

}

?>
