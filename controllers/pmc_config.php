<?php
/**
* @file pmc_config.php
* @synopsis  产品后台 系统设置控制器
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-24 10:36:13
 */

!defined('PATH_ADMIN') && exit('Forbidden');

/**
 * 系统设置控制器
 */
class pmc_config
{
		
	function __construct()
	{

	}

	public function index()
	{
		$post = $_POST;
		if($post['submit'])
		{
			unset($post['submit']);
			mod_config::set_configs($post);
			mod_login::message('更新系统配置成功');
		}else
		{
			$configs = mod_config::get_configs(array('fl_sysname','fl_sysurl','fl_timedf','fl_verify_code','fl_starttitle','fl_startcontent'));
			$action_link = array('href' => '?c=config&a=mail','text' => '配置SMTP发送');
			pm_tpl::assign('action_link',$action_link);
			pm_tpl::assign('ur_here','系统配置');
			pm_tpl::assign('config',$configs);
			pm_tpl::display('config_index');
		}
	}

	public function mail()
	{
		$post = $_POST;
		if($post['submit'])
		{
			unset($post['submit']);
			mod_config::set_configs($post);
			mod_login::message('更新邮箱发送配置成功');
		}else
		{
			$configs = mod_config::get_configs(array('fl_sendemail','fl_sendemailtype','fl_fromemail','fl_smtpserver','fl_smtpport','fl_smtpssl','fl_smtpauth','fl_smtpid','fl_smtppass'));
			$action_link = array('href' => '?c=config&a=index','text' => '返回系统配置');
			pm_tpl::assign('action_link',$action_link);
			pm_tpl::assign('ur_here','配置SMTP发送');
			pm_tpl::assign('config',$configs);
			pm_tpl::display('config_mail');
		}
	}
}
