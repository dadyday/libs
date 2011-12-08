<?php


	class Sql {
		var $aSelect = array();
		
		function select($field, $alias = null) {
			$this -> aSelect[$alias] = $param;
			return $this;
		}
		function from($table, $alias = null) {
			$this -> table = $table;
			$this -> alias = $alias;
			return $this;
		}
		function join($table, $alias = null) {
			$this -> aJoin[$alias] = $table;
			return $this;
		}
		function where($condition) {
			$this -> aJoin[$alias] = $table;
			return $this;
		}
		
		function query() {
			
		}
	}

?>