<?php
	require_once("class.baseobj.php");

	class BaseList extends BaseObj implements IteratorAggregate {
		var $arrItem;
		var $count;
		var $position;
		
		
		var $_sortProperty;
		
		function BaseList() {
			$this -> BaseObj();
			$this -> clear();
		}
		
	    public function getIterator() {
    	    return new ArrayObject($this -> arrItem);
	    }
		
		
		function clear() {
			$this -> arrItem = array();
			$this -> count = 0;
			$this -> position = -1;
			return true;
		}
		
		function insert($pos, $object) {
			$this -> count++;
			if ($pos < 0) $pos = 0;
			if ($pos >= $this -> count) $pos = $this -> count-1;
			
			for($n = $this -> count-1; $n > $pos; $n--) {
				$this -> arrItem[$n] = $this -> arrItem[$n-1];
			};
			$this -> arrItem[$pos] = $object;
			
			return $object;
		}
		
		function add($object) {
			$this -> count++;
			$this -> arrItem[$this -> count-1] = $object;
			return $object;
		}
		
		function remove($object) {
			$ret = NULL;
			for($n = 0; $n < $this -> count; $n++) {
				if ($this -> arrItem[$n]->id == $object->id) {
					$ret = $this -> arrItem[$n];
					array_slice($this -> arrItem, $n, 1);
					$this -> count--;
					break;
				};
			};
			return $ret;
		}
		
		function delete($pos) {
			$ret = NULL;
			if ($pos < $this -> count) {
				$ret = $this -> arrItem[$pos];
				array_slice($this -> arrItem, $pos, 1);
				$this -> count--;
			};
			return $ret;
		}
		
		function getItem($pos) {
			$ret = NULL;
			if ($pos < $this -> count) {
				$ret = $this -> arrItem[$pos];
			};
			return $ret;
		}
		
		function first() {
			$this->position = 0;
			return $this->getItem($this -> position);
		}
		
		function next() {
			$this->position++;
			if ($this->position > $this->count) $this->position = $this->count;
			return $this->getItem($this -> position);
		}
		
		function prev() {
			$this->position--;
			if ($this->position < -1) $this->position = -1;
			return $this->getItem($this -> position);
		}
		
		function last() {
			$this->position = $this->count-1;
			return $this->getItem($this -> position);
		}
		
		function _compareProperty($item1, $item2) {
			if (is_object($item1) && is_object($item2)) {
				$name = $this->_sortProperty;
				return 
					$item1->$name == $item2->$name ? 0 :
					$item1->$name > $item2->$name ? 1 : -1;
			};
			return 0;
		}
		
		function sortByProperty($name) {
			$this->_sortProperty = $name;
			usort($this->arrItem, array($this, "_compareProperty"));
		}
		
		function findByProperty($name, $value) {
			$ret = NULL;
			for($n = 0; $n < $this -> count; $n++) {
				$item = $this -> arrItem[$n];
				if (is_object($item) && $item->$name == $value) {
					$ret = $item;
					break;
				};
			};
			return $ret;
		}
		
		function asArray($key = 0, $value = '') {
			$ret = array();
			$ret = $this -> fillArray($ret, $key, $value);
			return $ret;
		}
		
		function fillArray(&$ret, $key = 0, $value = '') {
			for($n = 0; $n < $this -> count; $n++) {
				$item = $this -> arrItem[$n];
				$k = empty($key) ? $n : (method_exists($item, $key) ? call_user_method($key, $item) : $item -> $key);
				$v = empty($value) ? '' : (method_exists($item, $value) ? call_user_method($value, $item) : $item -> $value);
				$ret[$k] = $v;
				//eval('$ret[$item -> ' . $key . '] = $item -> ' . $value . ';');
			};
		/*
			//$ret = array();
			if (empty($value)) { 
				for($n = 0; $n < $this -> count; $n++) {
					$item =& $this -> arrItem[$n];
					$v = method_exists($item, $key) ? $item -> $key() : $item -> $key;
					$ret[$n] = $v;
					//eval('$ret[' . $n . '] = $item -> ' . $key . ';');
				};
			}
			else {
			}
		*/
			return $ret;
		}
	};

?>