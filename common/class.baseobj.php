<?php
	define("E_FAIL", false);
	define("S_OK", true);
	
	//define("NULL", false);
	
	function Succeeded($result) {
		return $result;
	}
	
	class BaseObj {
		protected $id = 0;
		
		function BaseObj() {
		}
		
		function getName() {
			return get_class($this) . ' ' . $this -> id;
		}
		
		function dump() {
			echo '<pre>';
			var_dump($this);
			echo '</pre>';
		}
	};
?>