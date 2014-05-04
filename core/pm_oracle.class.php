<?php

!defined('PATH_ADMIN') && exit('Forbidden');

  define('ORA_CHARSET_DEFAULT', 'UTF8');
  define('ORA_CONNECTION_TYPE_DEFAULT', 1);
  define('ORA_CONNECTION_TYPE_PERSISTENT', 2);
  define('ORA_CONNECTION_TYPE_NEW', 3);
  define('ORA_MESSAGES_NOT_CONNECTED', 'Not connected to Oracle instance');

	class ORACLE
	{
    private static $_instance;
    private $conn_handle;
    private $conn_data;
    private $errors_pool;
    private $statements = array();
    private $autocommit = TRUE;
    private $fetch_mode = OCI_BOTH;
    private $last_query;
    private $var_max_size = 10000;
    private $execute_status = false;
    private $charset;
    private $session_mode = OCI_DEFAULT;

		//fetch模式
		public function SetFetchMode($mode = OCI_BOTH)
		{
    	$this->fetch_mode = $mode;
    }

		//自动提交
		public function SetAutoCommit($mode = true)
		{
    	$this->autocommit = $mode;
    }

		//参数最大字符数
		public function SetVarMaxSize($size)
		{
    	$this->var_max_size = $size;
    }

		//错误
		public function GetError()
		{
    	return @oci_error($this->conn_handle);
    }

		//设置连接字符集
		public function SetNlsLang($charset = ORA_CHARSET_DEFAULT)
		{
    	$this->charset = $charset;
    }

		//初始
		public function __construct()
		{
    	$this->SetNlsLang('UTF8');
      $this->SetFetchMode(OCI_ASSOC);
      $this->SetAutoCommit(FALSE);
    }

		//连接数据库
		public function Connect($host, $user, $pass, $mode = OCI_DEFAULT, $type = ORA_CONNECTION_TYPE_DEFAULT)
		{
			switch ($type)
			{
				case ORA_CONNECTION_TYPE_PERSISTENT:
        	$this->conn_handle = oci_pconnect($user, $pass, $host, $this->charset, $mode);
					break;
				case ORA_CONNECTION_TYPE_NEW:
      		$this->conn_handle = oci_new_connect($user, $pass, $host, $this->charset, $mode);
					break;
      	default:
      		$this->conn_handle = oci_connect($user, $pass, $host, $this->charset, $mode);
    	}
      return is_resource($this->conn_handle) ? true : false;
    }

		//释放
		public function __destruct()
		{
			if (is_resource($this->conn_handle))
			{
        @oci_close($this->conn_handle);
      }
    }

		//得到执行状态
		public function GetExecuteStatus()
		{
        return $this->execute_status;
    }

		//binds参数
		private function GetBindingType($var)
		{
			if (is_a($var, "OCI-Collection"))
		 	{
      	$bind_type = SQLT_NTY;
        $this->SetVarMaxSize(-1);
			}elseif (is_a($var, "OCI-Lob"))
			{
      	$bind_type = SQLT_CLOB;
        $this->SetVarMaxSize(-1);
			}else
			{
      	$bind_type = SQLT_CHR;
      }
      return $bind_type;
    }

		//执行
		private function Execute($sql_text, &$bind = false)
		{
    	if(!is_resource($this->conn_handle)) return false;
      $this->last_query = $sql_text;
      $stid = @oci_parse($this->conn_handle, $sql_text);
      $this->statements[$stid]['text'] = $sql_text;
      $this->statements[$stid]['bind'] = $bind;
			if($bind && is_array($bind))
			{
				foreach($bind AS $k => $v)
				{
        	oci_bind_by_name($stid, $k, $bind[$k], -1);
        }
      }
      $com_mode = $this->autocommit ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT;
      $this->execute_status = oci_execute($stid, $com_mode);
      return $this->execute_status ? $stid : false;
    }

		//select
		public function Select($sql, $bind = false)
		{
    	return $this->Execute($sql, $bind);
    }

		public function query($sql,$binds = false)
		{
			return $this->Execute($sql,$binds);
		}

		public function fetch_first($sql)
		{
			$statement = $this->Execute($sql);
			//return @oci_fetch_array($statement, $this->fetch_mode + OCI_RETURN_LOBS);
			return @oci_fetch_array($statement);
		}

		public function fetch_all_first($sql, $skip = 0, $maxrows = -1)
		{
			$statement = $this->Execute($sql);
    	$rows = array();
      oci_fetch_all($statement, $rows, $skip, $maxrows, OCI_FETCHSTATEMENT_BY_ROW);
      return array_change_key_case($rows);
		}

		//结果以数组返回
		public function FetchArray($statement)
		{
    	//return oci_fetch_array($statement, $this->fetch_mode);
        return oci_fetch_array($statement);
    }

		public function FetchRow($statement)
		{
    	return oci_fetch_row($statement);
    }

		//得到全部结果
		public function FetchAll($statement, $skip = 0, $maxrows = -1)
		{
    	$rows = array();
        oci_fetch_all($statement, $rows, $skip, $maxrows, OCI_FETCHSTATEMENT_BY_ROW);
      return $rows;
    }

		//结果以对象返回
		public function FetchObject($statement)
		{
    	return oci_fetch_object($statement);
    }

		//得到下一行
		public function Fetch($statement)
		{
    	return oci_fetch($statement);
    }

		//获得字段值
		public function Result($statement, $field)
		{
    	return oci_result($statement, $field);
    }

		//绑定name到列
		public function DefineByName($statement , $column_name , &$variable, $type = SQLT_CHR)
		{
    	return oci_define_by_name($statement, $column_name, $variable, $type);
    }

		//检查字段是否为空
		public function FieldIsNull($statement, $field)
		{
    	return oci_field_is_null($statement, $field);
    }

		//返回字段名
		public function FieldName($statement, int $field)
		{
    	return oci_field_name($statement, $field);
    }

		//读取一行并过滤掉 HTML 标记
		public function FieldPrecition($statement, int $field)
		{
    	return oci_field_precision($statement, $field);
    }

		//返回字段范围
		public function FieldScale($statement, int $field)
		{
    	return oci_field_scale($statement, $field);
    }

		//返回字段大小
		public function FieldSize($statement, $field)
		{
    	return oci_field_size($statement, $field);
    }

		//返回字段的原始 Oracle 数据类型
		public function FieldTypeRaw($statement, int $field)
		{
    	return oci_field_type_raw($statement, $field);
    }

		//返回字段的数据类型
		public function FieldType($statement, int $field)
		{
    	return oci_field_type($statement, $field);
    }

		//插入
		public function Insert($table, $arrayFieldsValues, &$bind = false, $returning = false)
		{
    	if (empty($arrayFieldsValues)) return false;
      $fields = array();
      $values = array();
			foreach($arrayFieldsValues as $f=>$v)
			{
				$fields[] = $f;
				if(!is_numeric($v) && strpos($v,'NEXTVAL') == FALSE )
				{
				    $values[] = '\''.$v.'\'';
				}else
				{
					$values[] = $v;
				}
      }
      $fields = implode(",", $fields);
      $values = implode(",", $values);
      $ret = "";
			if ($returning)
		 	{
				foreach($returning as $f => $h)
				{
        	$ret_fields[] = $f;
          $ret_binds[] = ":$h";
          $bind[":$h"] = "";
        }
       	$ret = " returning ".(implode(",", $ret_fields))." into ".(implode(",",$ret_binds));
      }
      $sql = "insert into $table ($fields) values($values) $ret";//debug($sql);
      $result = $this->Execute($sql, $bind);
      if ($result === false) return false;
			if ($returning === false)
			{
      	return $result;
			}else
			{
      	$result = array();
				foreach($returning as $f => $h)
				{
        	$result[$f] = $bind[":$h"];
        }
        return $result;
      }
    }

		//更新
		public function Update($table, $arrayFieldsValues, $condition = false, &$bind = false, $returning = false)
		{
        if (empty($arrayFieldsValues)) return false;
        $fields = array();
        $values = array();
        foreach($arrayFieldsValues as $f=>$v){
            $fields[] = "$f = '$v'";
        }
        $fields = implode(",", $fields);
        if ($condition === false) { $condition = "true";}
        $ret = "";
        if ($returning) {
            foreach($returning as $f=>$h){
                $ret_fields[] = $f;
                $ret_binds[] = ":$h";
                $bind[":$h"] = "";
            }
            $ret = " returning ".(implode(",", $ret_fields))." into ".(implode(",",$ret_binds));
        }
        $sql = "update $table set $fields where $condition $ret";
        $result = $this->Execute($sql, $bind);
        if ($result === false) return false;
        if ($returning === false) {
            return $result;
        } else {
            $result = array();
            foreach($returning as $f=>$h){
                $result[$f] = $bind[":$h"];
            }
            return $result;
        }
    }

		public function Delete($table, $condition, &$bind = false, $returning = false)
		{
        if ($condition === false) { $condition = "true";}
        $ret = "";
        if ($returning) {
            foreach($returning as $f=>$h){
                $ret_fields[] = $f;
                $ret_binds[] = ":$h";
                $bind[":$h"] = "";
            }
            $ret = " returning ".(implode(",", $ret_fields))." into ".(implode(",",$ret_binds));
        }
        $sql = "delete from $table where $condition $ret";
        $result = $this->Execute($sql, $bind);
        if ($result === false) return false;
        if ($returning === false) {
            return $result;
        } else {
            $result = array();
            foreach($returning as $f=>$h){
                $result[$f] = $bind[":$h"];
            }
            return $result;
        }
    }

		//返回语句执行后受影响的行数
		public function NumRows($statement)
		{
    	return oci_num_rows($statement);
    }


		//影响行数
		public function RowsAffected($statement)
		{
    	return $this->NumRows($statement);
    }

		//返回结果列的数目
		public function NumFields($statement)
		{
    	return oci_num_fields($statement);
    }

		//返回结果列的数目
		public function FieldsCount($statement)
		{
    	return $this->NumFields($statement);
    }

		//初始化一个新的空 LOB 或 FILE 描述符
		public function NewDescriptor($type = OCI_DTYPE_LOB)
		{
    	return oci_new_descriptor($this->conn_handle, $type);
    }

		//分配新的 collection 对象
		public function NewCollection($typename, $schema = null)
		{
    	return oci_new_collection($this->conn_handle, $typename, $schema);
    }

		//存储过程
		public function StoredProc($name, $params = false, &$bind = false)
		{
			if($params)
		 	{
      	if (is_array($params)) $params = implode(",", $params);
        $sql = "begin $name($params); end;";
			}else
			{
      	$sql = "begin $name; end;";
      }
      return $this->Execute($sql, $bind);
    }

		//函数功能
		public function Func($name, $params = false, $bind = false)
		{
			if($params)
		 	{
      	if(is_array($params)) $params = implode(",", $params);
        $sql = "select $name($params) as RESULT from dual";
			}else
			{
      	$sql = "select $name from dual";
			}
			$h = $this->Execute($sql, $bind);
      $r = $this->FetchArray($h);
      return $r['RESULT'];
    }

		//分配游标
		public function Cursor($stored_proc, $bind)
		{
    	if(!is_resource($this->conn_handle)) return false;
      $sql = "begin $stored_proc(:$bind); end;";
      $curs = oci_new_cursor($this->conn_handle);
      $stmt = oci_parse($this->conn_handle, $sql);
      oci_bind_by_name($stmt, $bind, $curs, -1, OCI_B_CURSOR);
      oci_execute($stmt);
      oci_execute($curs);
      $this->FreeStatement($stmt);
      return $curs;
		}

		//取消从游标读取数据
		public function Cancel($statement)
		{
    	return oci_cancel($statement);
    }

		//释放关联于语句或游标的所有资源
		public function FreeStatement($stid)
		{
    	unset($this->statements[$stid]);
      return oci_free_statement($stid);
    }

		public function FreeStatements($array_stid)
		{
			if(is_array($array_stid)) foreach($array_stid as $stid)
		 	{
      	unset($this->statements[$stid]);
        oci_free_statement($stid);
      }
      return true;
    }


		//提交
		public function Commit()
		{
			if(is_resource($this->conn_handle))
			{
				return @oci_commit($this->conn_handle);
			}
			else
			{
				return false;
			}
    }

		//回滚
		public function Rollback()
		{
			if(is_resource($this->conn_handle))
			{
				return @oci_rollback($this->conn_handle);
			}
			else
			{
				return false;
			}
    }


		//打开或关闭内部调试输出
		public function InternalDebug($mode)
		{
    	oci_internal_debug($mode);
    }


		public function GetStatement($stid)
		{
    	return $this->statements[$stid] ? $this->statements[$stid] : false;
    }

		//获得快照
		public function QuerySnapshot($stid = false)
		{
    	if($stid) return $this->statements[$stid]['text']; else return $this->last_query;
    }

		public function ServerVer()
		{
			if(is_resource($this->conn_handle))
			{
      	return @oci_server_version($this->conn_handle);
			}else
			{
				return false;
			}
    }

		public function SetAction(string $action_name)
		{
    	return @oci_set_action($this->conn_handle, $action_name);
    }

		public function SetClientID(string $client_id)
		{
    	return @oci_set_client_identifier($this->conn_handle, $client_id);
    }

		public function SetClientInfo(string $client_info)
		{
    	return @oci_set_client_info($this->conn_handle, $client_info);
    }

		public function SepPrefetch(int $rows)
		{
    	return oci_set_prefetch($this->conn_handle, $rows);
    }

		public function StatementType($statement)
		{
    	return oci_statement_type($statement);
    }

		public function DumpQueriesStack()
		{
    	var_dump($this->statements);
    }

		public function Bye()
		{
    	$this->__destruct();
    }

		public function get_handle()
		{
    	return $this->conn_handle;
		}

		public function _limit($sql, $limit, $offset)
		{
			$limit = $offset + $limit;
			$newsql = "SELECT * FROM (select inner_query.*, rownum rnum FROM ($sql) inner_query WHERE rownum <= $limit)";

			if ($offset != 0)
			{
				$newsql .= " WHERE rnum > $offset";
			}

			// remember that we used limits
			$this->limit_used = TRUE;

			return $newsql;
		}
  }
?>
