<?php

	function getAgent() {
		$agent = $_SERVER["HTTP_USER_AGENT"];
		preg_match('/.*(MSIE|FIREFOX)[ \/-]*([0-9]+)\.([0-9]+).*/i', $agent, $r);
		return $r[1]; //.$r[2].$r[3];
	}

// text tools

	function getElipsed($text, $count) {
		$c = $count-2;
		if (strlen($text) <= $count) return $text;
		while($c > 0 && $text{$c-1} != " ") {
			$c--;
		};
		if ($c == 0) return substr($text, 0, $count-2) . '...';
		return substr($text, 0, $c) . '...';
	};

	function capitalize($text) {
		return strtoupper(substr($text,0,1)).substr($text,1);
	}

// processing tools

	function isGetVar($name) {
		return isset($_GET[$name]);
	};
	function getGetVar($name, $default = "") {
		$ret = $default;
		if (isset($_GET[$name])) {
			$ret = $_GET[$name];
		};
		return $ret;
	};

	function isPostVar($name) {
		return isset($_POST[$name]);
	};
	function getPostVar($name, $default = "") {
		$ret = $default;
		if (isset($_POST[$name])) {
			$ret = $_POST[$name];
		};
		return $ret;
	};

	function &getSessionVar($name, $default = NULL) {
		$ret =& $default;
		if (isset($_SESSION[$name])) {
			$ret =& $_SESSION[$name];
		};
		return $ret;
	};

	function setSessionVar($name, $value) {
		$_SESSION[$name] =& $value;
	};

	function registerSessionVar($name, $init = NULL) {
		if (!isset($_SESSION[$name])) {
			$_SESSION[$name] =& $init;
		};
		//global $$name;
		//$$name =& $_SESSION[$name];
	};

	function &getSessVar($name, $default = NULL) {
		if (!isset($_SESSION[$name])) $_SESSION[$name] = $default;
		return $_SESSION[$name];
	};
	
	function &getSessVars() {
		return $_SESSION;
	};

	function setSessDefault($name, $default = NULL) {
		if (!isset($_SESSION[$name])) $_SESSION[$name] = $default;
	};

	function getThisPage($params) {
		$arrGet = $_GET;
		$arrParams = split("&", $params);
		foreach($arrParams as $param) {
			@list($name, $value) = split("=", $param);
			$arrGet[$name] = $value;
		};
		
		$queryString = "";
		foreach($arrGet as $name => $value) {
			$queryString .= "&" . $name . "=" . $value;
		};
		return $_SERVER["PHP_SELF"] . "?" . $queryString;
	};

// debug functions

	function trace() {
		$arrFuncs = debug_backtrace();
		echo '<table cellspacing="4" cellpadding="0">';
		foreach($arrFuncs as $arrFunc) {
			$file = isset($arrFunc["file"]) ? $arrFunc["file"] : "";
			$line = isset($arrFunc["line"]) ? $arrFunc["line"] : "";
			$class = isset($arrFunc["class"]) ? $arrFunc["class"] : "";
			$arrArgs = isset($arrFunc["args"]) ? $arrFunc["args"] : array();
			$args = "";
			foreach($arrArgs as $arg) {
				if (is_string($arg)) {
					$args .= '"' . $arg . '", ';
				}
				else if (is_array($arg)) {
					$args .= 'array(' . $arg . '), ';
				}
				else if (is_object($arg)) {
					$args .= get_class($arg) . ', ';
				}
				else {
					$args .= '' . $arg . ', ';
				};
			};
			$args = substr($args, 0, -2);
			$function = isset($arrFunc["function"]) ? $arrFunc["function"] . " (" . $args . ")" : "";
			echo '<tr>
					<td align="left">' . $file . '</td>
					<td align="left">#' . $line . '</td>
					<td>&nbsp;</td>
					<td align="right">' . $class . '</td>
					<td align="left">::' . $function . '</td>
				</tr>';
		};
		echo '</table>';
	};
?>