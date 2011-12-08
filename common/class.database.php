<?php
	@define('DEBUG_SQL', 0); // 0 = aus, 1 = fehler, 2 = sql
	@define('LOG_SQL', 0); // 0 = aus, 1 = an
	@define('PATH_SQLLOG', dirname(__FILE__).'/');
	
	
	function _databaseErrorDebug($sql, $text) {
		if (DEBUG_SQL > 0) {
			echo $text . '<pre>';
			debug_print_backtrace();
			echo '</pre>';
		}
		if (LOG_SQL > 0) {
			logSql($sql, $text);
		}
		//exit;
	}
	function _databaseSqlDebug($sql, $err, $text) {
		if ($err) return _databaseErrorDebug($sql, $text);
		if (DEBUG_SQL > 1) {
			echo $text;
		}
		if (LOG_SQL > 1) {
			logSql($sql, '');
		}
	}
	function logSql($sql, $error) {
		$fp = fopen(PATH_SQLLOG . 'sql' . date('ymd') . '.log', 'a+');
		fwrite($fp, date('Y-m-d H:i:s') . "\n");
		fwrite($fp, $sql . "\n");
		fwrite($fp, $error . "\n");
		fclose($fp);
	}
	
// template
	$default_db = array(
		"host" => "localhost",
		"user" => "",
		"pass" => "",
		"db" => ""
		);

// datensatz
	class RecordSet
		{
		var $rs;
		var $data;
		var $recNo;
		var $recCount;
		var $eof;
		
		function RecordSet($rs)
			{
			$this->rs = $rs;
			$this->recCount = mysql_num_rows($rs);
			$this->first();
			}
		
		function findRecord($no) {
			$this->eof = true;
			if ($no < $this->recCount && mysql_data_seek($this->rs, $no)) {
				$this->data = mysql_fetch_array($this->rs);
				$this->eof = false;
				$this->recNo = $no;
			};
		}
		
		function field($name) {
			if (isset($this->data[$name])) {
				return $this->data[$name];
			};
			return "";
		}
		
		function first()
			{
			$this->findRecord(0);
			}
		
		function prev()
			{
			$this->findRecord($this->recNo-1);
			}
		
		function next()
			{
			$this->findRecord($this->recNo+1);
			}
		
		function last()
			{
			$this->findRecord($this->recCount-1);
			}
		
		function hasData()
			{
			return !$this->eof;
			}
		
		function close()
			{
			mysql_free_result($this->rs);
			}
		}

// db connection
	class DBConnection
		{
		var $database;
		var $con;
		var $lastSql = '';
		
		function DBConnection($database = 0)
			{
			global $default_db;
			if (!empty($database))
				$this->database = $database;
			else
				$this->database = $default_db;
			return $this->open();
			}
		
		function open() 
			{
			$this->con = mysql_connect($this->database["host"], $this->database["user"], $this->database["pass"]); $err = mysql_errno();
			_databaseSqlDebug('open ' . $this->database["host"], $err, mysql_errno().' - '.mysql_error());
			if (!$err) {
				mysql_select_db($this->database["db"], $this->con); $err = mysql_errno();
				_databaseSqlDebug('select DB ' . $this->database["db"], $err, mysql_errno().' - '.mysql_error());
				}
			return $err ? false : true;
			}
		
		function query($query)
			{
			$rs = @mysql_query($query, $this->con); $err = mysql_errno();
			_databaseSqlDebug('query ' . $query, $err, mysql_errno().' - '.mysql_error());
			return $err ? false : new RecordSet($rs);
			}
			
		function exec($sql)
			{
			@mysql_query($sql, $this->con); $err = mysql_errno();
			_databaseSqlDebug('exec ' . $sql, $err, mysql_errno().' - '.mysql_error());
			return $err ? false : mysql_affected_rows($this->con);
			}
		
		function close()
			{
			mysql_close($this->con);
			}
		
		function lastInsertId()
			{
			return mysql_insert_id($this->con);
			}
		
		function queryDbList() {
			$rs = mysql_list_dbs($this->con); $err = mysql_errno();
			if ($err) _databaseErrorDebug('<p>error '.mysql_errno().' - '.mysql_error().'<br>in dblist<br>');
			return $err ? false : new RecordSet($rs);
		}
		
		function queryTableList($db) {
			$rs = mysql_list_tables($db, $this->con); $err = mysql_errno();
			if ($err) _databaseErrorDebug('<p>error '.mysql_errno().' - '.mysql_error().'<br>in tablelist<br>');
			return $err ? false : new RecordSet($rs);
		}
	}
	?>