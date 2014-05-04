<?php
/**
* @file pmc_member.php
* @synopsis  产品后台 会员控制
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-19 17:19:15
 */
!defined('PATH_ADMIN') && exit('Forbidden');
	class pmc_member
	{

		function __construct()
		{

		}

		public function member_list()
		{
			$start = (empty($_GET['start'])) ? 0 : (int)$_GET['start'];
			$level = (empty($_GET['level'])) ? 0 : (int)$_GET['level'];
			$list = mod_member::member_list($start,$level,PAGE_ROWS);
			pm_tpl::assign('page_url', "?c=member&a=member_list&level={$level}");
			pm_tpl::assign('pages', mod_pager::get_page_number_list($list['total'],$start, PAGE_ROWS));
			pm_tpl::assign('ur_here','管理员列表');
			$action_link = array('href' => '/','text' => '返回');
			pm_tpl::assign('action_link',$action_link);
			pm_tpl::assign('full_page',1);
			pm_tpl::assign('admin_list',$list['data']);
			pm_tpl::display('member_list');
		}

		public function usersearch()
		{
			$start = (empty($_GET['start'])) ? 0 : (int)$_GET['start'];
			$keyword = $_GET['keyword'] ? $_GET['keyword'] : '';
			if(!$keyword)
			{
				mod_login::message('请输入关键词','?c=member&a=member_list');
			}
			$list = mod_member::search($keyword,PAGE_ROWS,$type = 4);
			$action_link = array('href' => '?c=member&a=member_list','text' => '返回管理员列表');
			pm_tpl::assign('page_url', "?c=member&a=usersearch");
			pm_tpl::assign('pages', mod_pager::get_page_number_list($list['total'],$start, PAGE_ROWS));
			pm_tpl::assign('ur_here','搜索管理员');
			pm_tpl::assign('action_link',$action_link);
			pm_tpl::assign('keyword',$keyword);
			pm_tpl::assign('admin_list',$list['data']);
			pm_tpl::display('member_list');
		}

		public function edit()
		{

			$user_id = $_GET['user_id'];
			if($_POST)
			{
				$pdata = $_POST;
				$user_id = $pdata['user_id'];
				if(!$pdata['user_name'])
				{
					mod_login::message('用户名不能为空');
				}
				try
				{
					$password = '';
					mod_member::member_save($user_id,$password,$pdata['truename'],$pdata['remark']);
					//mod_member::save_user_level($user_id,$pdata['level']);
					//
                    if($pdata['action_code'])
					{
						$sright = r_serialize(forrightserialize($_POST['action_code']));
						mod_member::saverights($sright,$user_id);
					}
                    //添加应用
					if($pdata['applist'])
					{
						mod_product::insert_member_product($user_id,$pdata['applist']);
					}
                    //删除应用
                    if($pdata['delapplist']){
                        mod_product::del_member_product($user_id,$pdata['delapplist']);
                    }
					mod_login::message('编辑用户信息成功','?c=member&a=member_list');
				}
				catch(Exception $e)
				{
					mod_login::message($e->getMessage());
				}
			}else
			{
				$uinfo = mod_member::get_oneamdinbyuser_id($user_id);
				$rowlist = mod_menu::get_role_list();

				$rightlist = mod_menu::get_user_action();
				$haveright = mod_member::get_right($user_id);
				$userright = forunrightserialize(r_unserialize($haveright));
				pm_tpl::assign('priv_arr',$rightlist);
				pm_tpl::assign('userright',$userright);

				$plist = mod_product::get_product_list(0,1);
				$userproduct = mod_product::get_user_products($user_id);

				$action_link = array('href' => '?c=member&a=member_list','text' => '管理员列表');
				pm_tpl::assign('action_link',$action_link);
				pm_tpl::assign('userplist',$userproduct);
				pm_tpl::assign('plist',$plist);
				pm_tpl::assign('action','edit');
				pm_tpl::assign('ur_here','编辑管理员资料');
				pm_tpl::assign('user',$uinfo);
				pm_tpl::display('member_info');
			}
		}

		public function add()
		{
			if($_POST)
			{
				$data = $_POST;
				unset($data['user_id']);
				try{
						$user_id = mod_member::member_add($data['user_name'],$data['truename'],$data['password'],$data['email'],$data['level']);
						if($user_id)
						{
							mod_login::message('添加管理员成功','?c=member&a=member_list');
						}else
						{
							mod_login::message('添加失败');
						}
					}
				catch(Exception $e)
				{
					mod_login::message($e->getMessage());
				}
			}else
			{
				$action_link = array('href' => '?c=member&a=member_list','text' => '管理员列表');
				pm_tpl::assign('ur_here','添加管理员');
				pm_tpl::assign('action_link',$action_link);
				pm_tpl::assign('action','add');
				pm_tpl::display('member_info');
			}
		}

		public function modif()
		{
			$user_id = $_GET['user_id'];
			$user_id = $user_id ? $user_id : ADMINUSERID;
			$uinfo = mod_member::get_oneamdinbyuser_id($user_id);
			if(!$uinfo) mod_login::message('对不起，无此用户');
			if($_POST)
			{
				$user_id = $_POST['user_id'];
				$truename = $_POST['truename'];
				$oldpassword = $_POST['oldpassword'];
				$npwd = $_POST['new_password'];
				$cnpwd = $_POST['pwd_confirm'];
				if($oldpassword && md5($oldpassword) != $uinfo['password']) mod_login::message('原始密码错误');
				if($oldpassword && $npwd != $cnpwd) mod_login::message('两次不密码不一致');
				$password = $oldpassword && $npwd ? $npwd : '';
				if($password)
				{
					setcookie ( AUTH_KEY . '_admin_auth', '', time () - 35920000, PATH_COOKIE );
				}
				mod_menu::save_user_nav($_POST['nav_list'][0],$user_id);
				mod_member::member_save($user_id,$password,$truename);
				mod_login::message('修改成功');
			}
			else
			{
				$nav_arr = mod_menu::get_user_nav($user_id);
				$menus = mod_menu::getmenulist();
				$action_link = array('href' => '?c=member&a=loginhistory','text' => '登录日志');
				pm_tpl::assign('action_link',$action_link);
				pm_tpl::assign('action','modif');
				pm_tpl::assign('nav_arr',$nav_arr);
				pm_tpl::assign('menus',$menus);
				pm_tpl::assign('ur_here','更新个人信息');
				pm_tpl::assign('user',$uinfo);
				pm_tpl::display('member_info');
			}
		}

		///////////登录日志   @Author:Baiwg
		public function loginhistory()
		{
			$get = $_GET;
			$start = (empty($get['start'])) ? 0 : $get['start'];
			$ip = (empty($get['ip'])) ? '' : $get['ip'];
			$start_date= (empty($get['start_date'])) ? '' : date('Y-m-d', strtotime($get['start_date']));
			$end_date = (empty($get['end_date'])) ? '' : date('Y-m-d', strtotime($get['end_date']));
			$start_dateunix = (empty($get['start_date'])) ? '' : strtotime($get['start_date']);
			$end_dateunix = (empty($get['end_date'])) ? '' : strtotime($get['end_date']);
			$username = USERNAME;
			$loginhistorylist = mod_member::loginhistory_list($start, $ip, $start_dateunix, $end_dateunix, $username, 20);
			if($loginhistorylist)
			{
				pm_tpl::assign('loginhistorylist', $loginhistorylist['data']);
				pm_tpl::assign('page_url', "?c=member&a=loginhistory&start_date={$start_date}&end_date={$end_date}");
				pm_tpl::assign('pages', mod_pager::get_page_number_list($loginhistorylist['total'], $start, 20));
			}
			$action_link = array('href' => '?c=member&a=modif', 'text' => '返回更新个人信息');
			pm_tpl::assign('action_link', $action_link);
			pm_tpl::assign('username', $username);
			pm_tpl::assign('ip', $ip);
			pm_tpl::assign('start_date', $start_date);
			pm_tpl::assign('end_date', $end_date);
			pm_tpl::assign('ur_here', ' 会员管理 - 登录日志');
			pm_tpl::display('member_loginhistory');
		}


		public function addrole()
		{
			if($_POST['Submit'])
			{
				$data = array();
				$data['role_name'] = $_POST['role_name'];
				$data['role_describe'] = $_POST['role_describe'];
				$data['action_list'] = r_serialize(forrightserialize($_POST['action_code']));
				mod_menu::add_role($data);
				mod_login::message('添加新角色成功','?c=member&a=role');
			}
			else
			{
				$priv_arr = mod_menu::get_user_action();
    		pm_tpl::assign('ur_here','添加角色');
    		pm_tpl::assign('action_link', array('href'=>'?c=member&a=role', 'text' => '角色列表'));
    		pm_tpl::assign('form_act',    'insert');
    		pm_tpl::assign('action',      'add');
				pm_tpl::assign('priv_arr',    $priv_arr);
				pm_tpl::display('member_addrole');
			}
		}

		public static function editrole()
		{
			$role_id = $_GET['role_id'];
			if(!$role_id)
			{
				mod_login::message('请选择要编辑的角色');
			}
			if($_POST['submit'])
			{
				$data = array();
				$data['role_name'] = $_POST['role_name'];
				$data['action_list'] = r_serialize(forrightserialize($_POST['action_code']));
				$data['role_describe'] = $_POST['role_describe'];
				$data['role_id'] = $role_id;
				mod_menu::edit_role($data);
				mod_login::message('编辑角色成功','?c=member&a=role');
			}else
			{
				$role = mod_menu::get_one_role($role_id);
				$priv_arr = mod_menu::get_user_action();
				$role['action_list'] = r_unserialize($role['action_list']);
				$role['action_list'] = forunrightserialize($role['action_list']);
    		pm_tpl::assign('ur_here','编辑角色');
    		pm_tpl::assign('action_link', array('href'=>'?c=member&a=role', 'text' => '角色列表'));
				pm_tpl::assign('priv_arr',    $priv_arr);
				pm_tpl::assign('role',$role);
				pm_tpl::display('member_editrole');
			}
		}

		public function role()
		{
			$data = mod_menu::get_role_list();
    	pm_tpl::assign('ur_here','角色管理');
			pm_tpl::assign('action_link', array('href'=>'?c=member&a=addrole', 'text' => '添加新角色'));
    	pm_tpl::assign('full_page',   1);
    	pm_tpl::assign('admin_list',  $data);
    	pm_tpl::display('member_role');
		}

		public function lang()
		{
			include PATH_LANG.'/users.php';
			include PATH_LANG.'/common.php';
			include PATH_LANG.'/privilege.php';
			return $_LANG;
		}


		public function allot()
		{
			$username = $_GET['user'];
			$user_id = $_GET['user_id'];
			$uinfo = mod_member::get_oneamdinbyuser_id($user_id);
			if($uinfo['user_name'] != $username)
			{
				mod_login::message('对不起，非法操作');
			}
			if($_POST['submit'])
			{
				$sright = r_serialize(forrightserialize($_POST['action_code']));
				mod_member::saverights($sright,$user_id);
				mod_login::message('权限赋予成功');
			}else
			{
				$rightlist = mod_menu::get_user_action();
				$haveright = mod_member::get_right($user_id);
				$role['action_list'] = forunrightserialize(r_unserialize($haveright));
				pm_tpl::assign('priv_arr',$rightlist);
				pm_tpl::assign('role',$role);
				pm_tpl::assign('uinfo',$uinfo);
				pm_tpl::assign('ur_here','分派权限');
				$action_link = array('href' => '?c=member&a=member_list','text' => '管理员列表');
				pm_tpl::assign('action_link',$action_link);
				pm_tpl::display('member_allot');
			}
		}

		public function msglist()
		{
			$username = USERNAME;
			$start = $_GET['start'] ? $_GET['start'] : 0;
			$data = mod_pmsys::get_user_msg($username,$start,$perpage = 20);
			pm_tpl::assign('ur_here','管理员留言');
			pm_tpl::assign('page_url', "?c=member&a=msglist");
			pm_tpl::assign('pages', mod_pager::get_page_number_list($data['total'],$start, 20));
			pm_tpl::assign('lists',$data['data']);
			pm_tpl::display('member_msglist');
		}

		////////////菜单操作历史
		public function logs()
		{
			$get = $_GET;
			$start = $get['start'] ? (int)$get['start'] : 0;
			$username = $get['username'] ? $get['username'] : '';
			$ctr = $get['ctr'] ? $get['ctr'] : '';
			$act = $get['act'] ? $get['act'] : '';
			$start_date = $get['start_date'] ? $get['start_date'] : '';
			$start_dategsh = $get['start_date'] ? date('Ymd', strtotime($get['start_date'])) : '';
			$end_date = $get['end_date'] ? $get['end_date'] : '';
			$end_dategsh = $get['end_date'] ? date('Ymd', strtotime($get['end_date'])) : '';
			$logslist = mod_member::get_logslist($start, $username, $ctr, $act, $start_dategsh, $end_dategsh, 20);
			//debug($logslist);
			if($logslist)
			{
				pm_tpl::assign('logslist', $logslist['data']);
				pm_tpl::assign('page_url', "?c=member&a=logs&username={$username}&ctr={$ctr}&act={$act}&start_date={$start_date}&end_date={$end_date}");
				pm_tpl::assign('pages', mod_pager::get_page_number_list($logslist['total'], $start, 20));
			}
			pm_tpl::assign('ur_here', ' 权限系统 - 菜单操作历史 ');
			pm_tpl::assign('username', $username);
			pm_tpl::assign('ctr', $ctr);
			pm_tpl::assign('act', $act);
			pm_tpl::assign('start_date', $start_date);
			pm_tpl::assign('end_date', $end_date);
			pm_tpl::display('member_logs');

		}

		public function logsmgdb()
		{
			$get = $_GET;
			$col = mongoinit('productmanage','oplogs');
			$start = empty($_GET['start']) ? 0 : (int)$_GET['start'];
			$username = $get['username'] ? $get['username'] : '';
			$ctr = $get['ctr'] ? $get['ctr'] : '';
			$act = $get['act'] ? $get['act'] : '';
			$start_date = $get['start_date'] ? $get['start_date'] : '';
			$start_dategsh = $get['start_date'] ? date('Ymd', strtotime($get['start_date'])) : '';
			$end_date = $get['end_date'] ? $get['end_date'] : '';
			$end_dategsh = $get['end_date'] ? date('Ymd', strtotime($get['end_date'])) : '';
			$logslist = mod_member::get_logslistmgdb($start, $username, $ctr, $act,$start_dategsh, $end_dategsh,$col, 20);
			//debug($logslist);
			if($logslist)
			{
				pm_tpl::assign('logslist', $logslist['data']);
				pm_tpl::assign('page_url', "?c=member&a=logsmgdb&username={$username}&ctr={$ctr}&act={$act}&start_date={$start_date}&end_date={$end_date}");
				pm_tpl::assign('pages', mod_pager::get_page_number_list($logslist['total'], $start, 20));
			}
			pm_tpl::assign('ur_here', ' 权限系统 - 用户操作历史【Mongodb版】 ');
			pm_tpl::assign('username', $username);
			pm_tpl::assign('ctr', $ctr);
			pm_tpl::assign('act', $act);
			pm_tpl::assign('start_date', $start_date);
			pm_tpl::assign('end_date', $end_date);
			pm_tpl::display('member_logsmgdb');
		}

        public function insert()
            {
            $uid=$_GET['user_id'];
           if(mod_member::dealdata($uid))
				mod_login::message('入库成功');
            }

    }
