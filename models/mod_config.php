<?php
/**
* @file mod_config.php
* @synopsis  产品后台 配置模块
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-21 22:42:06
 */

 !defined('PATH_ADMIN') &&exit('Forbidden');
	class mod_config
	{
	/**
	 * 获取某个配置信息
	 */
		public static function get_one_config($key)
		{
			if (empty($key)) return false;
			$key = addslashes($key);

			$data = pm_db::select('pm_config', 'fl_value', "fl_name = '{$key}'");
			return (empty($data)) ? false : $data[0]['fl_value'];
		}


	/**
	 * 读配置
	 *
	 * @param  array
	 * @return array
	 * @throws none
	 */
		public static function get_configs( $keys = array() )
		{
			$values = array ();

			foreach( $keys as $current_key )
			{
				pm_db::query("select fl_value from `pm_config` where `fl_name` = '" . $current_key . "'");
				$current_row = pm_db::fetch_one();
				$values[$current_key] = $current_row['fl_value'];
			}
			return $values;
		}


	/**
	 * 写配置
	 *
	 * @param  array
	 * @return none
	 * @throws none
	 */
		public static function set_configs( $configs = array() )
		{
			foreach( $configs as $current_key => $current_value )
			{
				if( false === strpos( $current_key, 'fl_' ) )
				{
					$current_key = 'fl_' . $current_key;
				}
				pm_db::query("replace `pm_config` set `fl_value` = '" . $current_value . "', `fl_name` = '" . $current_key . "'");
			}
		}


	/**
	 * 获取基本设置
	 *
	 * @param  none
	 * @return array
	 * @throws none
	 */
		public static function get_basic()
		{
			$basic_keys = array ( 'fl_sysopen', 'fl_debug', 'fl_sysname', 'fl_sysurl', 'fl_path_html', 'fl_ceoconnect', 'fl_ceoemail', 'fl_icp', 'fl_icpurl', 'fl_ipstat', 'fl_lp', 'fl_obstart', 'fl_cvtime', 'fl_timedf', 'fl_datefm', 'fl_ifjump', 'fl_refreshtime', 'fl_ckpath', 'fl_ckdomain', 'fl_footertime', 'fl_metakeyword', 'fl_metadescrip', 'fl_mulindex', 'fl_ipstates', 'fl_isp', 'fl_enmemcache', 'fl_memcacheserver', 'fl_memcacheport', 'fl_sendemail', 'fl_sendemailtype', 'fl_formemail', 'fl_smtpserver', 'fl_smtpport', 'fl_smtpssl', 'fl_smtpauth', 'fl_smtpid', 'fl_smtppass', 'fl_display_update_info');
			return self::get_configs( $basic_keys );
		}
 

	/**
	 * 设置基本设置
	 *
	 * @param  array
	 * @return boolean
	 * @throws none
	 */
		public static function set_basic( $config )
		{
			self::set_configs( $config );
		}

		public static function get_info()
		{
			$basic_keys = array (  'fl_sysname', 'fl_sysurl', 'fl_ceoconnect', 'fl_ceoemail', 'fl_icp', 'fl_icpurl', 'fl_title', 'fl_metakeyword', 'fl_metadescrip', 'fl_ipstat' );
			return self::get_configs( $basic_keys );
		}

		public static function set_info( $config )
		{
			self::set_configs( $config );
		}

		public static function get_status()
		{
			$basic_keys = array ( 'fl_sysopen', 'fl_debug','fl_display_update_info','fl_oplock','fl_xhwbupdate','fl_sysvhumenwbupdate','fl_lockitems','fl_lockmessage','fl_pushstatus' );
			return self::get_configs( $basic_keys );
		}

		public static function get_qhstatus()
		{
			$basic_keys = array ( 'fl_qhstatus' );
			return self::get_configs( $basic_keys );
		} 

		public static function set_status( $config )
		{
			self::set_configs( $config );
		}

		public static function get_fn()
		{
			$basic_keys = array ( 'fl_lp', 'fl_obstart','fl_refreshtime', 'fl_ckpath', 'fl_ckdomain', 'fl_footertime', 'fl_display_update_info', 'fl_verify_code' ,'fl_pushdata','fl_cachetype');
			return self::get_configs( $basic_keys );
		}

		public static function set_fn( $config )
		{
			self::set_configs( $config );
		}

		public static function set_stat( $config )
		{
			self::set_configs( $config );
		}

		public static function get_mail()
		{
			$basic_keys = array ( 'fl_sendemail', 'fl_sendemailtype', 'fl_fromemail', 'fl_smtpserver', 'fl_smtpport', 'fl_smtpssl', 'fl_smtpauth', 'fl_smtpid', 'fl_smtppass');
			return self::get_configs( $basic_keys );
		}

		public static function set_mail( $config )
		{
			self::set_configs( $config );
		}

		public static function get_all( )
		{
			$basic_keys = array (  'fl_sysopen', 'fl_debug', 'fl_sysname', 'fl_sysurl', 'fl_path_html', 'fl_ceoconnect', 'fl_ceoemail', 'fl_icp', 'fl_icpurl', 'fl_ipstat', 'fl_lp', 'fl_obstart', 'fl_cvtime', 'fl_timedf', 'fl_datefm', 'fl_ifjump', 'fl_refreshtime', 'fl_ckpath', 'fl_ckdomain', 'fl_footertime', 'fl_title', 'fl_metakeyword', 'fl_metadescrip', 'fl_mulindex', 'fl_ipstates', 'fl_isp', 'fl_enmemcache', 'fl_memcacheserver', 'fl_memcacheport', 'fl_sendemail', 'fl_sendemailtype', 'fl_fromemail', 'fl_smtpserver', 'fl_smtpport', 'fl_smtpssl', 'fl_smtpauth', 'fl_smtpid', 'fl_smtppass', 'fl_display_update_info', 'fl_proxy', 'fl_loadavg', 'fl_cc', 'fl_verify_code');
			return self::get_configs( $basic_keys );
		}

		public static function set_all( $config )
		{
			self::set_configs( $config );
		}

		public static function set_cc( $config )
		{
			self::set_configs( $config );
		}
	/**
	 * 获取ip禁止列表
	 *
	 * @param  none
	 * @return string
	 * @throws none
	 */
		public static function get_ip_deny_list()
		{
			$configs = self::get_configs( array('fl_ipban') );
			$ip_deny_list = str_replace( ",", "\n", $configs['fl_ipban'] );
			return $ip_deny_list;
		}


	/**
	 * 设置ip禁止列表
	 *
	 * @param  string
	 * @return boolean
	 * @throws none
	 */
		public static function set_ip_deny_list( $ip_deny_list)
		{
			$ip_deny_list = str_replace( array("\r\n", "\n", "\r"), ",", $ip_deny_list );
			self::set_configs( array( 'fl_ipban' => $ip_deny_list) );
		}


	/**
	 * 获取安全设置
	 *
	 * @param  none
	 * @return array
	 * @throws none
	 */
		public static function get_security()
		{
			$security_keys = array ( 'fl_proxy', 'fl_loadavg', 'fl_cc' );
			return self::get_configs( $security_keys );
		}


	/**
	 * 设置安全设置
	 *
	 * @param  array
	 * @return boolean
	 * @throws none
	 */
		public static function set_security( $config )
		{
			self::set_configs( $config );
		}
	}
?>
