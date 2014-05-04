<?php
/**
* @file pmc_pmsys.php
* @synopsis  PM系统功能
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2012-08-25 18:56:58
*/

	!defined('PATH_ADMIN') && exit('Forbidden');
	class pmc_pmsys
	{
		function __construct()
		{

		}

		public function adminmsg()
		{
			$keyword = $_GET['keyword'] ? $_GET['keyword'] : '';
			$start = $_GET['start'] ? $_GET['start'] : 0;
			$data = mod_pmsys::adminmsg_list($keyword = '',$start = 0,$perpage = 20);
			pm_tpl::assign('page_url', "?c=pmsys&a=adminmsg");
			pm_tpl::assign('keyword', "$keyword");
			pm_tpl::assign('pages', mod_pager::get_page_number_list($data['total'],$start, PAGE_ROWS));
			pm_tpl::assign('list',$data['data']);
			$action_link = array('href' => '?c=pmsys&a=addadminmsg','text' => '添加通知');
			pm_tpl::assign('action_link',$action_link);
			pm_tpl::assign('ur_here','权限管理 - 通知列表');
			pm_tpl::display('pmsys_adminmsg');
		}

		public function addadminmsg()
		{
			if($_POST['submit'])
			{
				$post = $_POST;
				$data = array();
				$data['content'] = $post['content'];
				$data['adduser'] = USERNAME;
				$data['addtime'] = time();
				$data['status'] = $post['status'];
				mod_pmsys::add_adminmsg($data);
				mod_login::message('添加通知成功','?c=pmsys&a=adminmsg');
			}else
			{
				$action_link = array('href' => '?c=pmsys&a=adminmsg','text' => '通知列表');
				pm_tpl::assign('action_link',$action_link);
				pm_tpl::assign('ur_here','权限管理 - 添加通知');
				pm_tpl::display('pmsys_addadminmsg');				
			}
		}

		public function editadminmsg()
		{
			$id = $_GET['id'];
			$info = mod_pmsys::get_one_adminmsg($id);
			if(!$info) mod_login::message('请选择要编辑的通知');
			if($_POST['submit'])
			{
				$post = $_POST;
				$data = array();
				$data['content'] = $post['content'];
				mod_pmsys::edit_adminmsg($data,$id);
				mod_login::message('编辑通知成功','?c=pmsys&a=adminmsg');
			}else
			{
				$action_link = array('href' => '?c=pmsys&a=adminmsg','text' => '通知列表');
				pm_tpl::assign('action_link',$action_link);
				pm_tpl::assign('info',$info);
				pm_tpl::assign('ur_here','权限管理 - 编辑通知');
				pm_tpl::display('pmsys_editadminmsg');				
			}
		}

		public function deladminmsg()
		{
			$id = $_GET['id'];
			$info = mod_pmsys::get_one_adminmsg($id);
			if(!$info) mod_login::message('请选择要删除的通知');
			mod_pmsys::del_adminmsg($id);
			mod_login::message('删除通知成功');
		}

		public function sendmsgtouser()
		{
			$username = $_GET['username'];
			if(!$username) mod_login::message('请选择要发送的对象');
			$uinfo = mod_member::get_oneamdinbyusername($username);
			if($_POST['submit'])
			{
				$post = $_POST;
				$data = array();
				$data['content'] = $post['content'];
				$data['touser'] = $post['username'];
				$data['addtime'] = time();
				mod_pmsys::sendmsg($data);
				mod_login::message('发送消息成功','?c=member&a=member_list');
			}else
			{
				pm_tpl::assign('user',$uinfo);
				pm_tpl::assign('ur_here','权限管理 - 发送消息');
				pm_tpl::display('pmsys_sendmsgtouser');
			}
		}

		public function getusermsgstatus()
		{
			$username = USERNAME;
			$t = mod_pmsys::get_user_msg_check($username);
			echo $t;exit;
		}

	}
