<?php
/***************************************************************************
* LivSNS 0.1
* (C)2004-2010 HOGE Software.
*
* $Id: db_mysql.class.php 25898 2013-07-17 10:12:36Z wangleyuan $
***************************************************************************/

class db
{
	var $querynum = 0;
	var $link;
	var $histories;

	var $dbhost;
	var $dbuser;
	var $dbpw;
	var $dbcharset;
	var $pconnect;
	var $tablepre;
	var $time;

	var $goneaway = 5;
	var $mErrorExit = true;

	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $dbcharset = '', $pconnect = 0, $tablepre='', $time = 0) 
	{
		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpw = $dbpw;
		$this->dbname = $dbname;
		$this->dbcharset = $dbcharset;
		$this->pconnect = $pconnect;
		$this->tablepre = $tablepre;
		$this->time = $time;

		if($pconnect) 
		{
			if(!$this->link = mysql_pconnect($dbhost, $dbuser, $dbpw)) 
			{
				$this->halt('Can not connect to MySQL server');
			}
		} 
		else 
		{
			if(!$this->link = mysql_connect($dbhost, $dbuser, $dbpw)) 
			{
				$this->halt('Can not connect to MySQL server');
			}
		}

		if($this->version() > '4.1') 
		{
			if($dbcharset) 
			{
				mysql_query("SET character_set_connection=".$dbcharset.", character_set_results=".$dbcharset.", character_set_client=binary", $this->link);
			}

			if($this->version() > '5.0.1') 
			{
				//mysql_query("SET sql_mode=''", $this->link); //关闭严格模式
			}
		}

		if($dbname) 
		{
			mysql_select_db($dbname, $this->link);
		}

	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) 
	{
		return @mysql_fetch_array($query, $result_type);
	}

	function result_first($sql) 
	{
		$query = $this->query($sql);
		return $this->result($query, 0);
	}

	function query_first($sql) 
	{
		$query = $this->query($sql);
		return $this->fetch_array($query);
	}

	function fetch_all($sql, $id = '') 
	{
		$arr = array();
		$query = $this->query($sql);
		while($data = $this->fetch_array($query)) 
		{
			$id ? $arr[$data[$id]] = $data : $arr[] = $data;
		}
		return $arr;
	}

	function query($sql, $type = '', $cachetime = FALSE) 
	{
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->link)) && $type != 'SILENT') 
		{
			$this->halt('MySQL Query Error', $sql);
		}
		$this->querynum++;
		$this->histories[] = $sql;
		return $query;
	}

	function affected_rows() 
	{
		return mysql_affected_rows($this->link);
	}

	function error() 
	{
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}

	function errno() 
	{
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}

	function result($query, $row) 
	{
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) 
	{
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) 
	{
		return mysql_num_fields($query);
	}

	function free_result($query) 
	{
		return mysql_free_result($query);
	}

	function insert_id() 
	{
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) 
	{
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) 
	{
		return mysql_fetch_field($query);
	}

	function version() 
	{
		return mysql_get_server_info($this->link);
	}

	function close() 
	{
		return mysql_close($this->link);
	}

	function halt($message = '', $sql = '') 
	{
		if (!$this->mErrorExit)
		{
			return;
		}
		$error = mysql_error();
		$errorno = mysql_errno();
		if($errorno == 2006 && $this->goneaway-- > 0) 
		{
			$this->connect($this->dbhost, $this->dbuser, $this->dbpw, $this->dbname, $this->dbcharset, $this->pconnect, $this->tablepre, $this->time);
			$this->query($sql);
		}
		else 
		{
			$entersplit = "\r\n<br />";
			$tmp_info = debug_backtrace();
			$str .= $entersplit;

			$debug_tree = "";
			$max = count($tmp_info);
			$i = 1;
			
			foreach ($tmp_info as $debug_info)
			{
				$space  = str_repeat('&nbsp;&nbsp;',$max - $i); 
				$debug_tree =  $entersplit . $space.$debug_info['file'] . " on line " . $debug_info['line'] . ":" . $debug_tree;  
				$i++;
			}
			$str = $entersplit . '[' . date('Y-m-d H:i:s') . ']' . $debug_tree.$str;
			$s = '<strong>version:</strong>' . $this->version() . '<br />';
			$s = '<strong>Error:</strong>' . $error . '<br />';
			$s .= '<strong>Errno:</strong>' . $errorno . '<br />';
			$s .= '<strong>SQL:</strong>:' . $sql;
			$trace = $str;
			exit($s . $trace);
		}
	}
	
	public function insert_data($data,$table,$replace = false)
	{
		if(!$table)
		{
			return false;
		}
		if(is_array($data))
		{
			$fields=array();
			foreach($data as $k => $v)
			{
				$fields[]= $k . "='" . $v . "'";
			}
			$fields = implode(',', $fields);
		}
		else
		{
			$fields .= $data;
		}
		$sql = $replace ? "REPLACE INTO " : "INSERT INTO ";
		$sql .= DB_PREFIX . $table ." SET " . $fields;	
		$this->query($sql);
		return $this->insert_id();		
	}	
	
	public function update_data($data, $table, $where = '') 
	{
		if($table == '' or $where == '') 
		{
			return false;
		}
		$where = ' WHERE '.$where;
		$field = '';
		if(is_string($data) && $data != '') 
		{
			$field = $data;
		} 
		elseif (is_array($data) && count($data) > 0) 
		{
			$fields = array();
			foreach($data as $k=>$v) 
			{
				$fields[] = $k."='".$v . "'";
			}
			$field = implode(',', $fields);
		} 
		else 
		{
			return false;
		}
		$sql = 'UPDATE '. DB_PREFIX . $table . ' SET ' . $field .  $where;
		$this->query($sql);
		return $this->affected_rows();
	}	
}

?>