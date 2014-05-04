<?php

/**
* @file pmc_ajaxdo.php
* @synopsis  ajax处理
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2013-05-21 13:18:18
 */

!defined('PATH_ADMIN') && exit ('Forbidden');
class pmc_ajaxdo 
{
	private $_get;

	function __construct()
	{
		$this->_get = $_GET;
	}

		public function edit_menu_order()
		{
			$menu_id = $_GET['id'];
			if(!$menu_id)
			{
				make_json_error('未选择目录');
			}
			$sort = $_GET['val'] ? $_GET['val'] : 255;
			mod_menu::editmenusort($sort,$menu_id);
			make_json_result($sort);
		}

		public function menustatus()
		{
			$menu_id = $_GET['id'];
			if(!$menu_id)
			{
				make_json_error('未选择目录');
			}
			mod_menu::changemenustatus($menu_id);
			$menu_list = mod_menu::get_menu_list();
			pm_tpl::assign('menulist',$menu_list);
			make_json_result(pm_tpl::fetch('menu_menulist'));
		}
		
		public function delmenu()
		{
			$menu_id = $_GET['id'];
			if(!$menu_id)
			{
				make_json_error('未选择目录');
			}
			mod_menu::delmenu($menu_id);
			$list = mod_menu::get_menu_list();
			pm_tpl::assign('menulist',$list);
			make_json_result(pm_tpl::fetch('menu_menulist'));
		}

		public function delmember()
		{
			$user_id = $this->_get['id'];
			if(ADMINUSERID != 1)
			{
				make_json_error('对不起，您无权删除其他管理员');
			}
			if($user_id == ADMINUSERID)
			{
				make_json_error('您不能删除自己');
			}
			if($user_id)
			{
				mod_member::member_delete($user_id);
				$list = mod_member::member_list($start,$level,20);
				pm_tpl::assign('admin_list',$list['data']);
				make_json_result(pm_tpl::fetch('member_list'));
			}else
			{
				make_json_error('删除失败');
			}
		}

}

