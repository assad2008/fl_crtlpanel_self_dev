<?php
/**
* @file mod_member.php
* @synopsis  产品后台 管理员操作模块
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-20 10:22:05
 */

	!defined('PATH_ADMIN') &&exit('Forbidden');
	class mod_member
	{
/**
 * 列表
 * @return <type>
 */
    public static function member_list($start,$level,$page = 20)
		{

				if($level != 0)
				{
					$condition1 = "WHERE a.level='$level'";
				}else
				{
					$condition1 = '';
				}

	 	 		if ($start > -1 && $page > 0)
	  		{
					$condition2 = " ORDER BY a.user_id ASC LIMIT {$start}, {$page}";
				}
				$condition = $condition1.$condition2;
				$query = pm_db::query("SELECT a.*,r.role_name FROM iosadm_admin_user AS a LEFT JOIN pm_role AS r ON a.role_id=r.role_id $condition");

        $userlistdb=array();
        while($userlist = pm_db::fetch_one($query))
        {
						$userlist['levelshow'] = $userlist['is_super'] == 1 ? '一级管理员' : admintitle($userlist['level']);
            $userlistdb[]= $userlist;
				}
				if($userlistdb)
				{
	  			$output = array();
					$num = pm_db::fetch_one(pm_db::query("select COUNT(*) AS num from iosadm_admin_user $condition1"));
     		 	$output['total'] = $num['num'];
					$output['data'] = $userlistdb;
					return $output;
				}else
				{
					return false;
				}
    }

    /**
     * 编辑信息
     * @param <type> $id
     */
    public static function member_edit($name)
    {
        if(empty($name))
        {
            throw new Exception("姓名不能为空");
        }
        pm_db::query("select * from iosadm_admin_user where user_name='{$name}' limit 1");
        return pm_dbpmr::fetch_one();
    }

		public static function save_user_level($user_id,$level)
		{
			if(!$level) return;
			pm_db::query("UPDATE iosadm_admin_user SET level='$level' WHERE user_id='$user_id'");
		}


    public static function member_save($user_id,$password,$truename = false,$remark = '')
    {
        $query = pm_db::query("SELECT * FROM iosadm_admin_user WHERE user_id='$user_id'");
        $how = pm_db::fetch_one($query);
        if(!$how)
        {
            throw new Exception("没有这个用户.");//编辑超级管理员,也会提示改错误.
        }
        $sqladd = '';
        if ($password != '')
        {
            if(strlen($password) < 6)
            {
                throw new Exception("密码长度不够,最少6位.");
            }
            $S_key=array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#');
            foreach($S_key as $value)
            {
                if (strpos($password,$value) !== false)
                {
                    throw new Exception("密码不能包含特殊字符.");
                }
            }
            $password = md5($password);
            $sqladd .= ", password='$password' ";
				}
        pm_db::query("UPDATE iosadm_admin_user SET truename='$truename' $sqladd WHERE user_id='$user_id'");
        return true;
    }

		public static function member_save_role($user_id,$role_id,$role_rights)
		{
			$memberinfo = self::get_oneamdinbyuser_id($user_id);
			$rights = $memberinfo['rights'];
			$rights = forunrightserialize(r_unserialize($rights));
			$roles_right = forunrightserialize(r_unserialize($role_rights));
			$newright = array_merge($rights,$roles_right);
			$newright = r_serialize(forrightserialize($newright));
			pm_db::query("UPDATE iosadm_admin_user SET role_id=$role_id,rights='$newright' WHERE user_id=$user_id");

		}


    public static function member_password($user_id,$password,$type=1)
    {
        $sql_add='';
        if($type == 1) $sql_add=" AND level=!'1')";
        if(ADMINUSERID == $user_id)
        {
            $oldpassword=(empty($_POST['oldpassword']))?'':$_POST['oldpassword'];
            if(empty($oldpassword))
            {
            	throw new Exception("请输入原始密码.");
            }
						$oldpassword = md5($oldpassword);
            pm_db::query("SELECT name,adminright FROM iosadm_admin_user WHERE user_id='$user_id' and password='$oldpassword' $sql_add");
            $how = pm_db::num_rows();
            if($how == 0)
            {
            	throw new Exception("原始密码不正确.");
            }
            //修改自己密码
        }
        pm_db::query("SELECT name,adminright FROM iosadm_admin_user WHERE user_id='$user_id' $sql_add");
        $how = pm_db::num_rows();
        if($how == 0)
        {
        	throw new Exception("没有这个用户.");
        }


        if ($password!='')
        {
            if(strlen($password)<6)
            {
                throw new Exception("密码长度不够,最少6位.");
            }
            $S_key=array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#');
            foreach($S_key as $value)
            {
                if (strpos($password,$value)!==false)
                {
                    throw new Exception("密码不能包含特殊字符.");
                }
            }
            $password = md5($password);
            pm_db::query("UPDATE iosadm_admin_user SET password='$password' WHERE user_id='$user_id'");
            return true;
        }
        else
        {
        	throw new Exception("请输入新密码.");
        }
    }

		public static function getpwd($username,$email)
		{
			$name = trim($username);
			$mail = trim($email);
			if(!isemail($email))
			{
				throw new Exception("请输入的邮箱格式不正确.");
			}
			$uinfo = pm_db::fetch_result("SELECT * FROM iosadm_admin_user WHERE user_name='$name'");
			if(!$uinfo)
			{
				throw new Exception("该用户不存在.");
			}
			if($uinfo['email'] != $mail)
			{
				throw new Exception("邮箱和用户不符合.");
			}
			$newpassword = randstr(8);
			$npwd = md5($newpassword);
			pm_db::query("UPDATE iosadm_admin_user SET password='$npwd' WHERE user_id='$uinfo[user_id]'");
			$mailtxt = "您的新密码为:$newpassword";
			@mod_mail::send($mail,$name,'找回密码',$mailtxt,'html');
		}
    /**
     * 添加用户
     * @param <type> $name
     * @param <type> $password
     */
    public static function member_add($name,$truename,$password,$email,$level)
    {
        $name = trim($name);
				$password = trim($password);
				$time = time();
        if(empty($name))
        {
            throw new Exception("请输入用户名.");
				}
				if(empty($truename))
        {
            throw new Exception("请输入姓名.");
        }
        if(empty($password))
        {
            throw new Exception("请输入密码.");
				}
        if(empty($email))
        {
            throw new Exception("请输入邮箱.");
				}
				if(!isemail($email))
				{
					throw new Exception("请输入的邮箱格式不正确.");
				}
        $S_key=array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#');
        foreach($S_key as $value)
        {
            if (strpos($name,$value) !== false)
            {
            	throw new Exception("用户名含有非法字符.");
            }

        }
        $data = pm_db::query("SELECT count(*) AS sum FROM iosadm_admin_user WHERE user_name='$name'");
        $rs = pm_db::fetch_one();
        if($rs['sum'] > 0)
        {
        	throw new Exception("此用户名已有,请重新输入用户名.");
		}
		/**
        $data = pm_db::query("SELECT count(*) AS sum FROM iosadm_admin_user WHERE email='$email'");
        $rs = pm_db::fetch_one();
        if ($rs['sum'] > 0)
        {
        	throw new Exception("此邮箱已经被使用，请重新输入.");
		}
		*/
        $S_key=array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#');// 密码检查
        if(strlen($password) < 6)
        {
            throw new Exception("密码长度不够,最少6位.");
        }
        foreach($S_key as $value)
        {
            if (strpos($password,$value) !== false)
            {
            	throw new Exception("密码不能包含特殊字符.");
            }
        }
        $password = md5($password);
        pm_db::query("INSERT INTO iosadm_admin_user (user_name,password,truename,email,level,add_time) VALUES ('$name','$password','$truename','$email','$level','$time')");
        $user_id = pm_db::insert_id();
        //使用message跳转
        return $user_id;
    }

    /**
     * 删除
     * @param <type> $name
     * @return <type>
     */
    public static function member_delete($user_id)
    {
        if(is_array($user_id))
        {
        	$user_id =implode('\',\'',$user_id);
        }
        if(!$user_id)
        {
        	throw new Exception("没有选择用户");
        }
        $uinfo = self::get_oneamdinbyuser_id($user_id);
        if($uinfo['status'] == 1)
        {
        	pm_db::query("UPDATE iosadm_admin_user SET status=0 WHERE user_id in('$user_id') AND level!='1' ");
        }else
        {
					pm_db::query("UPDATE iosadm_admin_user SET status=1 WHERE user_id in('$user_id') AND level!='1' ");
				}
        return true;
    }

		public static function search($keyword,$start = 20,$type = 4)
		{
			$condition = '';
			$condition = "user_name LIKE '%$keyword%'";
			if($type != '4')
			{
				$condition .= " AND level='$type'";
			}
			if ($start > -1 && $num > 0)
			{
				$condition .= " LIMIT {$start}, {$num}";
			}

			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM iosadm_admin_user WHERE $condition";
			$query = pm_db::query($sql);
      $userlistdb = array();
      while ($userlist = pm_db::fetch_one($query))
      {
				$userlist['levelshow'] = admintitle($userlist['level']);
        $userlistdb[]= $userlist;
				}
				if($userlistdb)
				{
	  			$output = array();
					$num = pm_db::fetch_one(pm_db::query("select COUNT(*) AS num from iosadm_admin_user WHERE $condition"));
     		 	$output['total'] = $num['num'];
					$output['data'] = $userlistdb;
					return $output;
				}else
				{

                $sql="SELECT * from SS_USER where LOGIN_NAME like '%$keyword%'";
                $sql_n="SELECT count(*) as total from SS_USER where LOGIN_NAME like '%$keyword%'";
                $db =  oralceinit(2);
                $result=$db->FetchAll($db->query($sql));
                $num = $db->FetchRow($db->query($sql_n));
                $output['total']=$num[0];
                //print_r($result);
                if ($result) {
                    foreach ($result as $key=>$value) {
                            $output['data'][$key]['user_id']=$value['ID'];
                            $output['data'][$key]['user_name']=$value['LOGIN_NAME'];
                            $output['data'][$key]['truename']=$value['NAME'];
                            $output['data'][$key]['levelshow']='来源boss';
                            $output['data'][$key]['flag']='boss';
                        }
                    return $output;
                    //$sql="INSERT into iosadm_admin_user set truename=''";
                }
                //print_r($result);


					return false;
				}
		}


    public function dealdata($uid)
    {

                $sql="SELECT * from SS_USER where ID =".$uid;
                $db =  oralceinit(2);
                $result=$db->FetchRow($db->query($sql));
                $sql_insert="INSERT into iosadm_admin_user set user_name='$result[2]',truename='$result[3]',bossname='$result[2]',email='$result[1]',password='$result[4]'";
               return pm_db::query($sql_insert);
    }

		public static function get_oneamdinbyuser_id($user_id)
		{
			$data = array();
			$data = pm_db::fetch_one(pm_db::query("SELECT * FROM iosadm_admin_user WHERE user_id='$user_id'"));
			if($data)
			{
				return $data;
			}else
			{
				return false;
			}
		}

		public static function get_oneamdinbyusername($username)
		{
			$data = array();
			$data = pm_db::fetch_one(pm_db::query("SELECT * FROM iosadm_admin_user WHERE user_name='$username'"));
			if($data)
			{
				return $data;
			}else
			{
				return false;
			}
		}

		public static function addbossuser($data,$user_id = false)
		{
			if(!$name)
			{
				$name = USERNAME;
			}else
			{
				$name = $name;
			}
			pm_db::query("UPDATE iosadm_admin_user SET bossname='$data[bossname]' WHERE user_id='$user_id'");
		}

		public static function getnamebybossname($name)
		{
			$data = array();
			$data = pm_db::fetch_one(pm_db::query("SELECT * FROM iosadm_admin_user WHERE bossname='$name'"));
			if(!$data)
			{
				return false;
			}else
			{
				return $data;
			}
		}

		public static function ishavebossid()
		{
			$name = USERNAME;
			$data = pm_db::fetch_one(pm_db::query("SELECT * FROM iosadm_admin_user WHERE user_name='$name'"));
			$bossname = $data['bossname'];
			if($bossname)
			{
				return true;
			}else
			{
				return false;
			}
		}

		public static function get_right($user_id)
		{
			$data = array();
			$data = pm_db::fetch_one(pm_db::query("SELECT rights FROM iosadm_admin_user WHERE user_id='$user_id'"));
			return $data['rights'];
		}

		public static function get_user_menu($user_id,$r = false)
		{

			$right = forunrightserialize(r_unserialize(self::get_right($user_id)));
			$menu = array();
			foreach($right AS $v)
			{
				$nv = explode('fl111',$v);
				foreach($nv AS $nvv)
				{
					$menuinfo = pm_db::fetch_result("SELECT * FROM iosadm_menu WHERE actioncode='$nvv' AND status=1");
					if(!$menuinfo) continue;
					if($menuinfo['parent_id']) $menu[$menuinfo['parent_id']] = 1;
					$menu[$menuinfo['menu_id']] = 1;
				}
			}
			return $menu;
		}


		public static function get_user_menus($user_id,$r = false)
		{

			$right = forunrightserialize(r_unserialize(self::get_right($user_id)));
			$menu = array();
			foreach($right AS $v)
			{
				$nv = explode('fl111',$v);
				foreach($nv AS $nvv)
				{
					$menuinfo = pm_db::fetch_result("SELECT * FROM iosadm_menu WHERE actioncode='$nvv' AND status=1");
					if(!$menuinfo) continue;
					/*if($menuinfo['parent_id']) $menu[$menuinfo['parent_id']] = 1;
					$menu[$menuinfo['menu_id']] = 1;*/
                    $menu[] = $menuinfo;
				}
			}
			return $menu;
		}







		/*public static function get_user_new_menu($user_id,$r = false)
		{

			$right = forunrightserialize(r_unserialize(self::get_right($user_id)));
			$menu = array();
			foreach($right AS $v)
			{
				$nv = explode('fl111',$v);
				foreach($nv AS $nvv)
				{
					$menuinfo = pm_db::fetch_result("SELECT * FROM iosadm_menu_new WHERE actioncode='$nvv' AND status=1");
					if(!$menuinfo) continue;
					if($menuinfo['parent_id']) $menu[$menuinfo['parent_id']] = 1;
					$menu[$menuinfo['menu_id']] = 1;
				}
			}
			return $menu;
		}
*/
		public static function saverights($right,$user_id)
		{
			pm_db::query("UPDATE iosadm_admin_user SET rights='$right' WHERE user_id='$user_id'");
		}

		public static function saveusertodo($data,$user_id)
		{
			pm_db::query("UPDATE iosadm_admin_user SET todolist='$data' WHERE user_id='$user_id'");
		}

		public static function getusertodo($user_id)
		{
			return pm_db::fetch_one(pm_db::query("SELECT todolist FROM iosadm_admin_user WHERE user_id='$user_id'"));
		}

		public static function loginhistory($username,$err = '')
		{
			$ip = get_client_ip();
			$time = time();
			if($err)
			{
				$loginok = 0;
			}else
			{
				$loginok = 1;
			}
			pm_db::query("INSERT INTO iosadm_loginhistory (username,ip,logintime,loginok,errmsg) VALUES ('$username','$ip','$time','$loginok','$err')");
		}

		///////////登录日志   @Author:Baiwg
		////统计指定IP登录的次数
		public static function get_count_oneip($username, $ip)
		{
			$sql = "SELECT COUNT(id) FROM iosadm_loginhistory WHERE 1=1 AND username = '$username' AND ip = '$ip' ";
			$countip = pm_db::result_first($sql);
			return $countip;
		}

		public static function loginhistory_list($start, $ip, $start_dateunix, $end_dateunix, $username, $perpage = PAGE_ROWS)
		{
			$where = '';
			if(!empty($ip))
			{
				$where .= " AND ip LIKE '%$ip%' ";
			}
			if(!empty($start_dateunix))
			{
				$where .= " AND logintime >= '$start_dateunix' ";
			}
			if(!empty($end_dateunix))
			{
				$where .= " AND logintime <= '$end_dateunix' ";
			}
			if($start > -1 && $perpage > 0)
			{
				$where .= "ORDER BY logintime DESC LIMIT {$start}, {$perpage}";
			}
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM iosadm_loginhistory WHERE 1=1 AND username = '$username' $where ";
			$query = pm_db::query($sql);
			$total = pm_db::fetch_result("SELECT FOUND_ROWS() AS rows");
			$data = array();
			while($row = pm_db::fetch_one($query))
			{
				$ipcent = 0;
				$row['ip_count'] = self::get_count_oneip($username, $row['ip']);
				$ipcent = floatval(($row['ip_count']) / (float)$total['rows']);
				if($ipcent < 0.1)
				{
					$row['ip'] = "<font color = 'PINK' ><strong>".$row['ip']."</strong></font>";
				}
				$row['ipcent'] = $ipcent;
				$row['ipcent'] = number_format($row['ipcent'], 2, '.', ',');
				$data[$row['id']] = $row;
			}
			if($data)
			{
				$output = array();
				$output['data'] = $data;
				$output['total'] = $total['rows'];
				return $output;
			}else
			{
				return null;
			}
		}

		public static function get_logslist($start, $username, $ctr_new, $act_new, $start_dategsh, $end_dategsh, $perpage= 20)
		{
			$where = '';
			if(!empty($username))
			{
				$where .= " AND username = '$username' ";
			}
			if(!empty($ctr_new))
			{
				$where .= " AND ctrl = 'c={$ctr_new}' ";
			}
			if(!empty($act_new))
			{
				$where .= " AND act = 'a={$act_new}' ";
			}
			if(!empty($start_dategsh))
			{
				$where .= " AND aday >= $start_dategsh ";
			}
			if(!empty($end_dategsh))
			{
				$where .= " AND aday <= $end_dategsh ";
			}
			if($start > -1 && $perpage > 0)
			{
				$where .= " ORDER BY timestamp DESC LIMIT {$start}, {$perpage} ";
			}
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM iosadm_oplog WHERE 1=1 $where ";
			$query = pm_db::query($sql);
			$data = pm_db::fetch_all($query);
			$total = pm_db::result_first(" SELECT FOUND_ROWS() AS rows ");
			if($data)
			{
				$output = array();
				$output['data'] = $data;
				$output['total'] = $total;
				return $output;
			}else
			{
				return null;
			}
		}

	////////////////菜单操作历史
	public static function get_logslistmgdb($start, $username, $ctr, $act,$start_dategsh, $end_dategsh, $col,$limit = 20)
	{
		$arr = array();
		if(!empty($username))
		{
			$arr['username'] = "$username";
		}
		if(!empty($ctr))
		{
			$arr['ctrl'] = "c={$ctr}" ;
		}
		if(!empty($act))
		{
			$arr['act'] = "a={$act}";
		}
		if(!empty($start_dategsh) && !empty($end_dategsh))
		{
			$arr['aday'] = array('$gte' => "$start_dategsh", '$lte' => "$end_dategsh");
		}elseif(!empty($start_dategsh) && empty($end_dategsh))
		{
			$arr['aday'] = array('$gte' => "$start_dategsh");
		}elseif(!empty($end_dategsh) && empty($start_dategsh))
		{
			$arr['aday'] = array('$lte' => "$end_dategsh");
		}
		//$cursor = $col->find( $arr )->sort(array('timestamp' => -1))->skip($start)->limit($limit);
		$cursor = $col->find( $arr )->skip($start)->limit($limit);
		$data = iterator_to_array($cursor);
		$total = $col->find($arr)->count();
		if($data)
		{
			$output = array();
			$output['data'] = $data;
			$output['total'] = $total;
			return $output;
		}else
		{
			return null;
		}

	}

	}

?>
