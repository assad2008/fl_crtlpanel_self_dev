<?php

	class mod_pmsys
	{
		public static function get_adminmsg()
		{
			$query = pm_db::query("SELECT * FROM gamebi_adminmsg WHERE status=1 ORDER BY id DESC");
			return pm_db::fetch_all($query);
		}

		public static function adminmsg_list($keyword = '',$start = 0,$perpage = 20)
		{
			$condition = '';
			if($keyword)
			{
				$condition .= "AND content LIKE '%$keyword%'";
			}
			if($start > -1 && $perpage > 0)
			{
				$condition .= "ORDER BY id DESC LIMIT $start, $perpage";
			}
			$query = pm_db::query("SELECT SQL_CALC_FOUND_ROWS * FROM gamebi_adminmsg WHERE 1=1 $condition");
			$data = pm_db::fetch_all($query);
			if($data)
			{
				$datas['data'] = $data;
				$datas['total'] = pm_db::result_first("SELECT FOUND_ROWS() AS rows");
				return $datas;
			}else
			{
				return null;
			}
		}

		public static function add_adminmsg($data)
		{
			pm_db::insert('gamebi_adminmsg',$data);
		}

		public static function edit_adminmsg($data,$id)
		{
			pm_db::update('gamebi_adminmsg',$data,"id='$id'");
		}

		public static function get_one_adminmsg($id)
		{
			return pm_db::fetch_result("SELECT * FROM gamebi_adminmsg WHERE id='$id'");
		}

		public static function del_adminmsg($id)
		{
			pm_db::query("UPDATE gamebi_adminmsg SET status=0 WHERE id='$id'");
		}

		public static function get_user_msg($username,$start,$perpage = 20)
		{
			$condition = '';
			if($start > -1 && $perpage > 0)
			{
				$condition = "ORDER BY id DESC LIMIT {$start}, {$perpage}";
			}
			$query = pm_db::query("SELECT SQL_CALC_FOUND_ROWS * FROM gamebi_usermsg WHERE touser='$username' $condition");
			$ndata = array();
			while($row = pm_db::fetch_one($query))
			{
				pm_db::query("UPDATE gamebi_usermsg SET is_read=1 WHERE id='$row[id]'");
				$ndata[] = $row;
			}
			$data = array();
			if($ndata)
			{
				$data['data'] = $ndata;
				$data['total'] = pm_db::result_first("SELECT FOUND_ROWS() AS rows");
				return $data;
			}else
			{
				return null;
			}
		}

		public static function get_user_msg_check($username)
		{
			$num = pm_db::result_first("SELECT COUNT(*) AS num FROM gamebi_usermsg WHERE touser='$username' AND is_read=0");
			return $num ? $num : 0;
		}

		public static function sendmsg($data)
		{
			pm_db::insert('gamebi_usermsg',$data);
		}
	}

?>
