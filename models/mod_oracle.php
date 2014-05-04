<?php
/**
* @file mod_oracle.php
* @synopsis  Oralce相关操作
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-19 13:37:27
 */

!defined('PATH_ADMIN') && exit('Forbidden');
class mod_oracle
{
	public static function bossuser($name = false)
	{
		$db = oralceinit(2);
		if(!$name)
		{
			$sql  = "SELECT * FROM ss_user WHERE (coopid='-10' OR coopid='-9')";
		}else
		{
			$sql  = "SELECT * FROM ss_user WHERE (coopid='-10' OR coopid='-9') AND LOGIN_NAME='$name'";		
		}
		$query = $db->Select($sql);
		$data = array();
		while($row = $db->FetchArray($query))
		{
			if(!$row)
			{
				return false;
			}
			$data[] = $row;
		}
		return $data;
	}

	public static function get_product()
	{
		$db = oralceinit(2);
		$sql = "SELECT id,name FROM tbl_product WHERE is_exist=1";
		$query = $db->Select($sql);
		$data = array();
		while($row = $db->FetchArray($query))
		{
			if(!$row)
			{
				return false;
			}
			$data[] = array_change_key_case($row);
		}
		return $data;
	}
}

?>
