<?php
/**
* @file mod_menu.php
* @synopsis  产品后台 目录类
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-21 11:36:39
 */
	!defined('PATH_ADMIN') && exit('Forbidden');
	class mod_menu
	{

		public static function getmenulist()
		{
			$usermenu = mod_member::get_user_menu(ADMINUSERID);
			$data = array();
			$product_id ='';
			$sql_1 = "SELECT a.* FROM iosadm_menu a JOIN (SELECT m.parent_id AS menu_id FROM iosadm_menu m GROUP BY m.parent_id) b WHERE 1=1 AND a.parent_id=0 AND a.level=1 AND a.is_show=1 AND a.`status`=1 ORDER BY a.sort ASC";
			$query = pm_db::query($sql_1);
			while($row = pm_db::fetch_one($query))
			{
				if(ADMINLEVEL != 1 && !$usermenu[$row['menu_id']])
				{
					continue;
				}
				$data[$row['menu_id']]['label'] = $row['menu_name'];
				$sql_2 = "SELECT a.menu_id FROM iosadm_menu a WHERE a.parent_id='$row[menu_id]' AND a.level=2 AND a.is_show=1 ORDER BY a.sort ASC";
				$secondquery = pm_db::query($sql_2);
				$second = pm_db::fetch_all($secondquery);
				if(!$second)
				{
					//$sql1 = "SELECT * FROM iosadm_menu WHERE parent_id='$row[menu_id]' AND level=3 AND status=1 ORDER BY sort ASC";
					$query1 = pm_db::query("SELECT * FROM iosadm_menu WHERE parent_id='$row[menu_id]' AND level=3 AND status=1 AND is_show=1 ORDER BY sort ASC");
				}else
				{
					$string = '';
					$string .= "(";
					foreach($second AS $v)
					{
						$string .= "'{$v['menu_id']}',";
					}
					$string = substr($string,0,-1) . ')';
					$sql2 = "SELECT * FROM iosadm_menu WHERE parent_id IN $string AND level=3 AND status=1 ORDER BY sort ASC";
					$query1 = pm_db::query("SELECT * FROM iosadm_menu WHERE parent_id IN $string AND level=3 AND status=1 ORDER BY sort ASC");
				}
				while($row1 = pm_db::fetch_one($query1))
				{
					if(ADMINLEVEL != 1 && !$usermenu[$row1['menu_id']])
					{
						continue;
					}
					$data[$row['menu_id']]['children'][$row1['menu_id']]['label'] = $row1['menu_name'];
					$data[$row['menu_id']]['children'][$row1['menu_id']]['action'] = $row1['act_url'];
				}
			}
			return $data;
		}

		public static function get_user_menu_list()
		{
			$usermenu = mod_member::get_user_menu(ADMINUSERID);
			$data = array();
			$query = pm_db::query("SELECT * FROM iosadm_menu WHERE parent_id=0 AND level=1 AND is_show=1 AND status=1 ORDER BY sort ASC");
			$menu_str = '';
			$menu_str = '<ul id="sddm">';
			$mi = 1;
			while($row = pm_db::fetch_one($query))
			{
				if(ADMINLEVEL != 1 && !$usermenu[$row['menu_id']])
				{
					continue;
				}
				$menu_str .= '<li><a href="#" onmouseover="mopen(\'m'.$mi.'\')" onmouseout="mclosetime()">'.$row['menu_name'].'</a><div id="m'.$mi.'" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">';
				$secondquery = pm_db::query("SELECT menu_id FROM iosadm_menu WHERE parent_id='$row[menu_id]' AND level=2 ORDER BY sort ASC");
				$second = pm_db::fetch_all($secondquery);
				if(!$second)
				{
					$query1 = pm_db::query("SELECT * FROM iosadm_menu WHERE parent_id='$row[menu_id]' AND level=3 AND status=1 ORDER BY sort ASC");
				}else
				{
					$string = '';
					$string .= "(";
					foreach($second AS $v)
					{
						$string .= "'{$v['menu_id']}',";
					}
					$string = substr($string,0,-1) . ')';
					$query1 = pm_db::query("SELECT * FROM iosadm_menu WHERE parent_id IN $string AND level=3 AND status=1 ORDER BY sort ASC");
				}
				while($row1 = pm_db::fetch_one($query1))
				{
					if(ISSUPERADMIN != 1 && $row['menu_id'] == 3)
					{
						if(!in_array($row1['menu_id'],array(4,7)))
						continue;
					}
					if(ADMINLEVEL != 1 && !$usermenu[$row1['menu_id']])
					{
						continue;
					}
					$menu_str .= '<a href="'.$row1['act_url'].'">'.$row1['menu_name'].'</a>';
				}
				$menu_str .= '</div></li>';
				$mi++;
			}
			return $menu_str;
		}

		public static function get_role_list()
		{
			$data = array();
			$query = pm_db::query("SELECT * FROM iosadm_role WHERE status=1");
			while($row = pm_db::fetch_one($query))
			{
				$data[$row['role_id']] = $row;
			}
			return $data;
		}

		public static function del_role($id)
		{
			pm_db::query("UPDATE iosadm_role SET status=0 WHERE role_id='$id'");
		}

		public static function add_role($data)
		{
			pm_db::query("INSERT INTO iosadm_role (role_name,action_list,role_describe) VALUES ('$data[role_name]','$data[action_list]','$data[role_describe]')");
		}

		public static function edit_role($data)
		{
			pm_db::query("UPDATE iosadm_role SET role_name='$data[role_name]',action_list='$data[action_list]',role_describe='$data[role_describe]' WHERE role_id='$data[role_id]'");
		}

		public static function get_one_role($role_id)
		{
			return pm_db::fetch_result("SELECT * FROM iosadm_role WHERE role_id='$role_id'");
		}

		public static function get_user_action()
		{
			$data = array();
			$query = pm_db::query("SELECT * FROM iosadm_admin_action WHERE parent_id=0 AND status=1 ORDER BY sort ASC");
			while($row = pm_db::fetch_one($query))
			{
				$data[$row['action_id']] = $row;
				$query1 = pm_db::query("SELECT * FROM iosadm_admin_action WHERE parent_id='$row[action_id]' AND status=1 ORDER BY sort ASC");
				$priv_list = '';
				while($row1 = pm_db::fetch_one($query1))
				{
					$data[$row['action_id']]['priv'][$row1['action_id']] = $row1;
					$priv_list .= $row1['action_code'].',';
				}
				$data[$row['action_id']]['priv_list'] = substr($priv_list,0,-1);
			}
			return $data;
		}

		public static function get_user_nav($user_id)
		{
			$user_info = pm_db::fetch_one(pm_db::query("SELECT nav_list FROM iosadm_admin_user WHERE user_id='$user_id'"));
    	$nav_arr = (trim($user_info['nav_list']) == '') ? array() : explode(",", $user_info['nav_list']);
   	 	$nav_lst = array();
    	foreach ($nav_arr AS $val)
    	{
        $arr = explode('|', $val);
        $nav_lst[$arr[1]] = $arr[0];
			}
			return $nav_lst;
		}

		public static function save_user_nav($nav,$user_id)
		{
			pm_db::query("UPDATE iosadm_admin_user SET nav_list='$nav' WHERE user_id='$user_id'");
		}

		public static function get_menudd_list()
		{
			$data = array();
			$query = pm_db::query("SELECT * FROM iosadm_menu");
			$data = pm_db::fetch_all($query);
			return $data;
		}

		public static function get_menu_list()
		{
			$data = array();
			$query = pm_db::query("SELECT * FROM iosadm_menu WHERE parent_id=0 AND level=1 AND status=1 ORDER BY status DESC, is_show DESC,sort ASC");
			while($row = pm_db::fetch_one($query))
			{
				$data[] = $row;
				$secondquery = pm_db::query("SELECT menu_id FROM iosadm_menu WHERE parent_id='$row[menu_id]' AND level=2 ORDER BY status DESC, is_show DESC, sort ASC");
				$second = pm_db::fetch_all($secondquery);
				if(!$second)
				{
					$query1 = pm_db::query("SELECT * FROM iosadm_menu WHERE parent_id='$row[menu_id]' AND level=3 AND status=1 ORDER BY status DESC, is_show DESC, sort ASC");
				}else
				{
					$string = '';
					$string .= "(";
					foreach($second AS $v)
					{
						$string .= "'{$v['menu_id']}',";
					}
					$string = substr($string,0,-1) . ')';
					$query1 = pm_db::query("SELECT * FROM iosadm_menu WHERE parent_id IN $string AND level=3 AND status=1 ORDER BY status DESC, is_show DESC, sort ASC");
				}
				while($row1 = pm_db::fetch_one($query1))
				{
					$data[] = $row1;
				}
			}
			return $data;
		}

		public static function get_parent_menu()
		{
			$query = pm_db::query("SELECT * FROM iosadm_menu WHERE parent_id=0 AND status=1 ORDER BY sort ASC");
			$data = pm_db::fetch_all($query);
			return $data;
		}

        public function edit_pro_menu($mid,$pid,$type)
        {
        if ($type=='add') {
			$query = pm_db::query("INSERT into iosadm_menu_product set menu_id=$mid,product_id=$pid");
        }
        if ($type=='del') {
			$query = pm_db::query("DELETE from iosadm_menu_product where menu_id=$mid and product_id=$pid");
        }
        }

        public function get_proid($mid)
        {
			$query = pm_db::query("SELECT * FROM iosadm_menu_product WHERE menu_id=$mid");
			$data = pm_db::fetch_all($query);
			return $data;
        }

        public function get_products()
            {
			$query = pm_db::query("SELECT * FROM iosadm_products WHERE  status=1 ORDER BY addtime desc");
			$data = pm_db::fetch_all($query);
			return $data;
            }



		public static function get_one_menu($menu_id)
		{
			return pm_db::fetch_one(pm_db::query("SELECT * FROM iosadm_menu WHERE menu_id='$menu_id'"));
		}

		public static function get_menu_status($menu_id)
		{
			$ret = pm_db::fetch_one(pm_db::query("SELECT is_show FROM iosadm_menu WHERE menu_id='$menu_id'"));
			return $ret['is_show'];
		}

		public static function get_son_menu($menu_id)
		{
			$data = array();
			$data = pm_db::fetch_all_result("SELECT * FROM iosadm_menu WHERE parent_id=$menu_id");
			return $data ? $data : null;
		}

		public static function delmenu($menu_id)
		{
			pm_db::query("UPDATE iosadm_menu SET status=0 WHERE menu_id='$menu_id'");
		}

		public static function addmenu($data)
		{
			pm_db::insert('iosadm_menu',$data);
		}

		public static function editmenu($data,$menu_id)
		{
			return pm_db::update('iosadm_menu',$data,"menu_id='$menu_id'");
		}

		public static function editmenusort($sort,$menu_id)
		{
			return pm_db::query("UPDATE iosadm_menu SET sort='$sort' WHERE menu_id='$menu_id'");
		}

		public static function changemenustatus($menu_id)
		{
			$status = self::get_menu_status($menu_id);
			if($status == 1)
			{
				pm_db::query("UPDATE iosadm_menu SET is_show=0 WHERE menu_id='$menu_id'");
			}elseif($status == 0)
			{
				pm_db::query("UPDATE iosadm_menu SET is_show=1 WHERE menu_id='$menu_id'");
			}
		}

		public static function get_right_list($is_parent = false)
		{
			$data = array();
			$query = pm_db::query("SELECT * FROM iosadm_admin_action WHERE parent_id=0 AND status=1 ORDER BY sort ASC");
			while($row = pm_db::fetch_one($query))
			{
				$data[] = $row;
				if(!$is_parent)
				{
					$query1 = pm_db::query("SELECT * FROM iosadm_admin_action WHERE parent_id='$row[action_id]' AND status=1 ORDER BY action_id ASC, sort ASC ");
					while($row1 = pm_db::fetch_one($query1))
					{
						$data[] = $row1;
					}
				}
			}
			return $data;
		}

		public static function get_right_by_parentid($parent_id)
		{
			$data = array();
			$query1 = pm_db::query("SELECT * FROM iosadm_admin_action WHERE parent_id='$row[action_id]' AND status=1 ORDER BY action_id ASC, sort ASC");
			while($row1 = pm_db::fetch_one($query1))
			{
				$data[] = $row1;
			}
			return $data;
		}

		public static function get_right_name($act_id)
		{
			return pm_db::result_first("SELECT action_name FROM iosadm_admin_action WHERE action_id='$act_id'");
		}

		public static function get_one_right($act_id)
		{
			return pm_db::fetch_result("SELECT * FROM iosadm_admin_action WHERE action_id='$act_id' AND status=1");
		}

		public static function editright($data,$act_id)
		{
			return pm_db::update('iosadm_admin_action',$data,"action_id='$act_id'");
		}

		public static function delright($act_id)
		{
			return pm_db::query("UPDATE iosadm_admin_action SET status=0 WHERE action_id='$act_id'");
		}

		public static function addright($data)
		{
			pm_db::insert('iosadm_admin_action',$data);
		}

		public static function get_parent_right()
		{
			$query = pm_db::query("SELECT * FROM iosadm_admin_action WHERE parent_id=0 AND status=1 ORDER BY sort ASC");
			return pm_db::fetch_all($query);
		}

		public static function del_one_right($id)
		{
			pm_db::query("UPDATE iosadm_admin_action SET status=0 WHERE action_id='$id'");
		}

		public static function pldel_rights($data)
		{
			foreach($data AS $v)
			{
				self::del_one_right($v);
			}
		}




		public static function menulistbypandr()
		{
			$product_id =  mod_product::get_cur_pid();
			$sql_1 = "SELECT a.* FROM iosadm_menu a left JOIN iosadm_menu_product b on(a.menu_id=b.menu_id) WHERE  b.product_id=$product_id  and  a.status=1";
			$sql_1 .= " ORDER BY a.status DESC, a.is_show DESC, a.sort ASC";
			$list=pm_db::fetch_all(pm_db::query($sql_1));
            if(!$list) return null;
            if (ADMINLEVEL==1) {
                return  self::getparents($list);
            }else{
                $usermenu = mod_member::get_user_menus(ADMINUSERID); //权限菜单
                foreach ($list as $value) {
                    foreach ($usermenu as $val) {
                        if ($value['menu_id']==$val['menu_id']) {
                            $com[]=$val;
                        }
                    }
                }
                if(!$com) return null;
                return self::getparents($com);
            }
		}


   private function getparents($list)
   {
            foreach ($list as $val) {
                $p[]=$val['parent_id'];
            }
            $sql_2="SELECT * FROM iosadm_menu where menu_id in (".implode(',',array_unique($p)).")";
            $parentlist=pm_db::fetch_all(pm_db::query($sql_2));
            foreach ($parentlist as $value) {
                $parent[$value['menu_id']]=$value['menu_name'];
            }
                foreach ($parent as $key=> $value) {
                    $menu[$key]['label']=$value;
                    foreach ($list as $val) {
                        if ($val['parent_id']==$key) {
                            $menu[$key]['children'][$val['menu_id']]['label']=$val['menu_name'];
                            $menu[$key]['children'][$val['menu_id']]['action']=$val['act_url'];
                        }
                    }

                }
            return $menu;
   }







	}
?>
