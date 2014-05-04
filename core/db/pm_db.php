<?php
/**
* @file pm_db.php
* @synopsis  产品后台 数据库类
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 17:08:29
 */

!defined('PATH_ADMIN') && exit('Forbidden');
class pm_db
{
	protected static $sql = '';
	protected static $link_read = null;
	protected static $link_write = null;
	protected static $current_link = null; // 当前连接标识
	protected static $query;
	protected static $query_count = 0;
	protected static $dataconfig = null;

  protected static function init_mysql ($is_read = true, $is_master = false)
	{
		self::$dataconfig = $GLOBALS['database'];  //选择数据库配置
    if (empty(self::$$link))
    {
    	try
       	{
        	$link = 'link_read';
          self::$$link = mysql_connect(self::$dataconfig['db_host'], self::$dataconfig['db_user'], self::$dataconfig['db_pass']);
          if (empty(self::$$link))
          {
          	throw new Exception(mysql_error(), 10);
          }
          else
          {
          	if (mysql_get_server_info() > '4.1')
            {
            	$charset = str_replace('-', '', strtolower(self::$dataconfig['db_charset']));
              mysql_query("SET character_set_connection=" . $charset . ", character_set_results=" . $charset . ", character_set_client=binary");
            }
            if (mysql_get_server_info() > '5.0')
            {
            	mysql_query("SET sql_mode=''");
            }
            if (@mysql_select_db(self::$dataconfig['db_name']) === false)
            {
            	throw new Exception(mysql_error(), 11);
            }
					}
        }
        catch (Exception $e)
        {
        	if (TRUE)
          {
          	if ($e->getCode() == 10)
            {
            	echo '数据库连接失败，可能是数据库服务器地址、账号或密码错误';
            }
            elseif($e->getCode() == 11)
						{
            	echo '数据库' . self::$dataconfig['db_name'] . '不存在';
            }
            else
            {
            	echo 'Can\'t connect to MySQL server';
            }
          }
          else
         	{
          	echo $e->getMessage(), '<br/>', '<pre>', $e->getTraceAsString(), '</pre>';
          }

          self::log($e->getMessage(),'初始化');
          exit;
        }
			}
      return self::$$link;
    }



    public static function tran_query($sql)
    {
          self::$current_link = self::init_mysql();
          return mysql_query($sql, self::$current_link);
    }

    public static function query ($sql, $is_master = false)
    {
			$sql = trim($sql);
			if(!mod_sqlsafecheck::checkquery($sql))
			{
				throw new Exception('Sorry,Your SQL is bad', 444);
			}
      self::$current_link = self::init_mysql(true, $is_master);
      try
        {
        	self::$sql = $sql;
          self::$query = @mysql_query($sql, self::$current_link);
          if (self::$query === false)
          {
          	throw new Exception(mysql_error());
          }
          else
          {
          	self::$query_count ++;
            return self::$query;
          }
        }
        catch (Exception $e)
        {
        	if (!TRUE) ;
          else
          {
          	echo $e->getMessage(), '<br/>';
            echo '<pre>', $e->getTraceAsString(), '</pre>';
            echo '<strong>Query: </strong> ' . $sql;
          }
          self::log($e->getMessage());
          exit;
        }
    }




    public static function insert_id ()
    {
    	return mysql_insert_id(self::$current_link);
    }

    public static function affected_rows ()
    {
    	return mysql_affected_rows(self::$current_link);
    }

    public static function num_rows ($query = false)
    {
    	(empty($query)) && $query = self::$query;
      return mysql_num_rows($query);
    }

    public static function fetch_one ($query = false) #fetch
    {
    	(empty($query)) && $query = self::$query;
     	return mysql_fetch_array($query, MYSQL_ASSOC);
    }

		public static function fetch_result($sql)  #返回单条结果 SQL
		{
			return self::fetch_one(self::query($sql));
		}

		public static function result_first($sql)  #返回一个字段值 SQL
		{
			return @mysql_result(self::query($sql), 0);
		}

		public static function fetch_all_result($sql) #返回结果 SQL
		{
			return self::fetch_all(self::query($sql));
		}

    public static function fetch_all ($query = false)
    {
    	(empty($query)) && $query = self::$query;
      $row = $rows = array();
      while ($row = mysql_fetch_array($query, MYSQL_ASSOC))
      {
      	$rows[] = $row;
      }
      return (empty($rows)) ? false : $rows;
    }

    public static function select($table, $fields, $condition)
    {
    	try
      {
      	if (empty($table) || empty($fields) || empty($condition))
        {
        	throw new Exception('查询数据的表名，字段，条件不能为空', 444);
        }

        self::$sql = "SELECT {$fields} FROM `{$table}` WHERE {$condition}";
        $result = self::query(self::$sql, false);

        return self::fetch_all();
      }
      catch (Exception $e)
      {
      	if (!defined('DEBUG_LEVEL') || !DEBUG_LEVEL) ;
				else
				{
        	echo $e->getMessage(), '<br/>';
          echo '<pre>', $e->getTraceAsString(), '</pre>';
          echo '<strong>Query: </strong>[select] ', (!empty(self::$sql)) && self::$sql;
        }
        self::log($e->getMessage());
        exit;
      }
    }

    public static function update($table, $data, $condition,$tran = '')
    {
    	try
      {
      	if (empty($table) || empty($data) || empty($condition))
        	throw new Exception('更新数据的表名，数据，条件不能为空', 444);

  	    if(!is_array($data))
          throw new Exception('更新数据必须是数组', 444);

        $set = '';
				foreach ($data as $k => $v)
				{
        	$set .= empty($set) ? ("`{$k}` = '{$v}'") : (", `{$k}` = '{$v}'");
				}
        if (empty($set)) throw new Exception('更新数据格式化失败', 444);

        self::$sql = "UPDATE `{$table}` SET {$set} WHERE {$condition}";

        if ($tran == 'tran') {
            return self::tran_query(self::$sql);
        }else{
            $result = self::query(self::$sql, true);
            return self::affected_rows();
        }
      }
      catch (Exception $e)
      {
      	if (!defined('DEBUG_LEVEL') || !DEBUG_LEVEL) ;
				else
				{
        	echo $e->getMessage(), '<br/>';
          echo '<pre>', $e->getTraceAsString(), '</pre>';
          echo '<strong>Query: </strong>[update]' . (!empty(self::$sql)) && self::$sql;
        }
        self::log($e->getMessage());
        exit;
      }
    }

    public function insert($table,$datas,$callback = false)
		{
			$fields = array();
			$data = array();
			foreach($datas AS $k => $v)
			{
				$fields[] = $k;
				$data[] = $v;
			}
      try
      {
				if (empty($table) || empty($fields) || empty($data))
				{
        	throw new Exception('插入数据的表名，字段、数据不能为空', 444);
        }

        if (!is_array($fields) || !is_array($data))
        {
        	throw new Exception('插入数据的字段和数据必须是数组', 444);
        }

            // 格式化字段
        $_fields = '`' . implode('`, `', $fields) . '`';
            // 格式化需要插入的数据
        $_data = self::format_insert_data($data);

        if (empty($_fields) || empty($_data))
        {
        	throw new Exception('插入数据的字段和数据必须是数组', 444);
        }
        self::$sql = "INSERT INTO `{$table}` ({$_fields}) VALUES {$_data}";

        if ($callback == 'tran') {
            return self::tran_query(self::$sql);
        }else{
            $result = self::query(self::$sql, true);
        }

        if($callback)
            return self::affected_rows();
        else
            return self::insert_id ();
			}
      catch (Exception $e)
      {
      	if (TRUE) ;
        	else
          {
          	echo $e->getMessage(), '<br/>';
            echo '<pre>', $e->getTraceAsString(), '</pre>';
            echo '<strong>Query: </strong>[insert] ' . (!empty(self::$sql)) && self::$sql;
         	}
          self::log($e->getMessage());
          exit;
     	}
    }

    protected static function format_insert_data($data)
    {
    	if (!is_array($data) || empty($data))
      {
      	throw new Exception('数据的类型不是数组', 445);
      }

      $output = '';
      foreach ($data as $value)
      {
            // 如果是二维数组
      	if (is_array($value))
        {
        	$tmp = '(\'' . implode("', '", $value) . '\')';
                $output .= !empty($output) ? ", {$tmp}" : $tmp;
                unset($tmp);
            }
            else
            {
                $output = '(\'' . implode("', '", $data) . '\')';
            }
        } //foreach

        return $output;
    }

    public function delete($table, $condition)
    {
        try
        {
            if (empty($table) || empty($condition))
            {
                throw new Exception('表名和条件不能为空', 444);
            }

            self::$sql = "DELETE FROM `{$table}` WHERE {$condition}";
            $result = self::query(self::$sql, true);
            return self::affected_rows();
        }
        catch (Exception $e)
        {
            if (!defined('DEBUG_LEVEL') || !DEBUG_LEVEL) ;
            else
            {
                echo $e->getMessage(), '<br/>';
                echo '<pre>', $e->getTraceAsString(), '</pre>';
                echo '<strong>Query: </strong>[delete] ' . (!empty(self::$sql)) && self::$sql;
            }
            self::log($e->getMessage());
            exit;
        }
    }

    public static function get_rows_num($table, $condition)
    {
        try
        {
            if (empty($table) || empty($condition))
                throw new Exception('查询记录数的表名，字段，条件不能为空', 444);

            self::$sql = "SELECT count(*) AS total FROM {$table} WHERE {$condition}";
            $result = self::query(self::$sql);

            $tmp = self::fetch_one();
            return (empty($tmp)) ? false : $tmp['total'];
        }
        catch (Exception $e)
        {
            if (!defined('DEBUG_LEVEL') || !DEBUG_LEVEL) ;
            else
            {
                echo $e->getMessage(), '<br/>';
                echo '<pre>', $e->getTraceAsString(), '</pre>';
                echo '<strong>Query: </strong>[rows_num] ' . (!empty(self::$sql)) && self::$sql;
            }
            self::log($e->getMessage());
            exit;
        }
    }


    public static function server_info()
    {
        return mysql_get_server_info();
    }


    public static function select_db($dbname)
    {
        return mysql_select_db($dbname);
    }


    public static function get_sql()
    {
        return self::$sql;
    }

    private static function log($message,$sql_info = '')
    {
        if(empty($sql_info))$sql_info = self::$sql;
        mod_log::mysql_log($message,$sql_info, mysql_errno());
    }
}
?>
