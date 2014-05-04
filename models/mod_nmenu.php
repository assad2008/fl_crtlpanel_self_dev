<?php
/**
* @file mod_nmenu.php
* @synopsis  目录处理模型
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2013-04-22 14:37:29
*/

class mod_nmenu
{
	public static function get_user_firstmenu()
	{
		$user_id = ADMINUSERID;
		if(ADMINLEVEL == 1)
		{
			return pm_db::fetch_all(pm_db::query("SELECT * FROM pm_menu WHERE level=1 AND parent_id=0"));
		}
		$usermenu = mod_member::get_user_menu($user_id);
		$menu = array();
		$query = pm_db::query("SELECT * FROM pm_menu WHERE level=3 AND is_show=1 AND status=1");
		while($row = pm_db::fetch_one($query))
		{
			$secondmenu = pm_db::fetch_result("SELECT * FROM pm_menu WHERE menu_id='$row[parent_id]' AND level=2 AND is_show=1 AND status=1");
			$firstmenu = pm_db::fetch_result("SELECT * FROM pm_menu WHERE menu_id='$secondmenu[parent_id]' AND level=1 AND is_show=1 AND status=1");
			$menu[$firstmenu['menu_id']] = $firstmenu;
		}
		return $menu;
	}

	public static function get_sec_thrid_menu()
	{
		$get = $_GET;
		$user_id = ADMINUSERID;
		$usermenu = mod_member::get_user_menu($user_id);
		$menu = array();
		$query = pm_db::query("SELECT * FROM pm_menu WHERE level=1 AND is_show=1 AND menu_id='$get[menu_id]' AND status=1");
		while($row = pm_db::fetch_one($query))
		{
			$secondmenu = array();
			$query1 = pm_db::query("SELECT menu_id,menu_name FROM pm_menu WHERE level=2 AND parent_id='$row[menu_id]' AND is_show=1 AND status=1");
			while($row1 = pm_db::fetch_one($query1))
			{
				$thirdmenu = array();
				$query2 = pm_db::query("SELECT menu_id,menu_name,act_url,actioncode FROM pm_menu WHERE level=3 AND parent_id='$row1[menu_id]' AND is_show=1 AND status=1 order by sort desc");
				while($row2 = pm_db::fetch_one($query2))
				{
					$menu[$row1['menu_id']]['f'] = $row1;
					$menu[$row1['menu_id']]['s'][] = $row2;
				}
			}
		}
		return $menu;
	}

		public static function get_menu_list()
		{
			$data = array();
			$query = pm_db::query("SELECT * FROM pm_menu WHERE parent_id=0 AND level=1 AND status=1 ORDER BY sort ASC");
			while($row = pm_db::fetch_one($query))
			{
				$data[] = $row;
				$secondquery = pm_db::query("SELECT * FROM pm_menu WHERE parent_id='$row[menu_id]' AND level=2 ORDER BY sort ASC");
				while($second = pm_db::fetch_one($secondquery))
				{
					if(!$second)
					{
						continue;
					}else
					{
						$data[] = $second;
						$query1 = pm_db::query("SELECT * FROM pm_menu WHERE parent_id='$second[menu_id]' AND level=3 AND status=1 ORDER BY sort ASC");
						while($row1 = pm_db::fetch_one($query1))
						{
							$data[] = $row1;
						}
					}
				}
			}
			return $data;
		}
}
