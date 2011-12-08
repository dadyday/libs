<?php
	require_once("init.tools_datetime.php");
	
	
	function asId($value) {
		if(is_object($value) && isset($value -> id)) {
			$value = $value -> id;
		};
		if(empty($value)) {
			return "NULL";
		};
		return (integer) $value;
	};
	function isId($value) {
		$value = asId($value);
		return ($value === 'NULL' ? 'IS ' : '= ') . $value;
	};
	function isNotId($value) {
		$value = asId($value);
		return ($value ==  'NULL' ? 'IS NOT ' : '!= ') . $value;
	};
	
	function asInt($value, $null = false) {
		if(empty($value)) {
			return $null ? 'NULL' : 0;
		};
		return (integer) $value;
	};
	function isInt($value) {
		$value = asInt($value);
		return ($value ==  'NULL' ? 'IS ' : '= ') . $value;
	};
	
	
	function asFloat($value, $null = false) {
		return asDouble($value, $null);
	};
	function isFloat($value) {
		return isDouble($value, $null);
	};
	function asDouble($value, $null = false) {
		if(empty($value)) {
			return $null ? 'NULL' : 0;
		};
		return (double) $value;
	};
	function isDouble($value) {
		$value = asDouble($value);
		return ($value ==  'NULL' ? 'IS ' : '= ') . $value;
	};
	
	function asStr($value, $null = false) {
		if(empty($value)) {
			return $null ? 'NULL' : '""';
		};
		return '"' . addslashes($value) . '"';
	};
	function isStr($value) {
		$value = asStr($value);
		return ($value ==  'NULL' ? 'IS ' : '= ') . $value;
	};
	
	function asLikeStr($value, $null = false) {
		if(empty($value)) {
			return $null ? 'NULL' : '""';
		};
		$value = str_replace('\\', '_', $value);
		return '"' . addslashes($value) . '"';
	};
	function isLikeStr($value) {
		$value = asLikeStr($value);
		return ($value ==  'NULL' ? 'IS ' : '= ') . $value;
	};
	
	function asDateTime($value, $null = false) {
		if(empty($value)) {
			return $null ? 'NULL' : '"0000-00-00 00:00:00"';
		};
		if (dateStr('Y-m-d H:i:s', $value) == '') return $null ? 'NULL' : '"0000-00-00 00:00:00"';
		return '"' . dateStr('Y-m-d H:i:s', $value) . '"';
	};
	function isDateTime($value) {
		$value = asDateTime($value);
		return ($value ==  'NULL' ? 'IS ' : '= ') . $value;
	};
	
	function asDate($value, $null = false) {
		if(empty($value)) {
			return $null ? 'NULL' : '"0000-00-00"';
		};
		return '"' . dateStr('Y-m-d', $value) . '"';
	};
	function isDate($value) {
		$value = asDate($value);
		return ($value ==  'NULL' ? 'IS ' : '= ') . $value;
	};
	
	function asTime($value, $null = false) {
		if(empty($value)) {
			return $null ? 'NULL' : '"00:00:00"';
		};
		return '"' . timeStr('H:i:s', $value) . '"';
	};
	function isTime($value) {
		$value = asTime($value);
		return ($value ==  'NULL' ? 'IS ' : '= ') . $value;
	};
	
	function toFloat($string) {
		$string = str_replace(',', '.', $string);
		return (double) $string;
	};
	
	function toBool($boolString) {
		return empty($boolString) ? false : true;
	};
	
	function toDateTime($timeString) {
		if (parseDateTime($timeString, $h, $i, $s, $d, $m, $y)) {
			return joinTime($h, $i, $s, $d, $m, $y);
		}
		return 0;
	};
	
	function toDate($timeString) {
		if (parseDateTime($timeString, $h, $i, $s, $d, $m, $y)) {
			return joinTime(0, 0, 0, $d, $m, $y);
		}
		return 0;
	};
	
	function toTime($timeString) {
		if (parseDateTime($timeString, $h, $i, $s, $d, $m, $y)) {
			return joinTime($h, $i, $s, 0, 0, 0);
		}
		return 0;
	};
	
	function toTimeOffset($timeString) {
		
		return empty($timeString) ? 0 : (integer) $timeString;
	};
	
	function arrToStr($array, $delimiter = ';') {
		$string = http_build_query($array);
		$string = str_replace('&', $delimiter, $string);
		return $string;
	}
	
	function strToArr($string, $delimiter=';') {
		$string = str_replace($delimiter, '&', $string);
		parse_str($string, $array);
		if (get_magic_quotes_gpc() || set_magic_quotes_runtime()) $array = stripAllSlashes($array);
		return $array;
	}
	
	function stripAllSlashes($value) {
	    $value = is_array($value) ? array_map('stripAllSlashes', $value) : stripslashes($value);
	    return $value;
	}

?>