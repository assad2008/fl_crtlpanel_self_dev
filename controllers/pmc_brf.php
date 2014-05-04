<?php
/**
* @file pmc_brf.php
* @synopsis  用户注册和找回密码
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2013-07-11 09:38:11
*/


class pmc_brf
{
	function __construct()
	{

	}

	public function reg()
	{
		$post = $_POST;
		if($post['submit'])
		{
			try
			{
				$username = $post['name'];
				$password = $post['password'];
				$cpassword = $post['cfm_password'];
				$email = $post['mail'];
				$truename = $post['truename'] ? $post['truename'] : '';
				if($password != $cpassword) mod_login::message('很抱歉，两次密码输入不一致');
				mod_member::member_add($username,$truename = '请设置',$password,$email,2);
				mod_login::message('注册成功','?c=login');
			}catch(Exception $e)
			{
				mod_login::message($e->getMessage());
			}
		}else
		{
			pm_tpl::display('registered');
		}
	}

	public function getpwd()
	{
		$post = $_POST;
		if($post['submit'])
		{
			try
			{
				$username = $post['name'];
				$email = $post['mail'];
				mod_member::getpwd($username,$email);
				mod_login::message('密码已发至您的邮箱，敬请查收','?c=login');
			}catch(Exception $e)
			{
				mod_login::message($e->getMessage());
			}
		}else
		{
			pm_tpl::display('getpwd');
		}
	}

}
