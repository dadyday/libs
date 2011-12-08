<?php 

	@define('DEBUG_INITIAL_DISPLAY', 0);
	
	$raiseObjectInit = 0;
	function initRaiseSection() {
		global $raiseObjectInit;
		if (!$raiseObjectInit) {
			return '
				<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
				<!--
					function switchSection(name) {
						with (document.getElementById(name)) style.display = style.display == \'none\' ? \'inline\' : \'none\';
					};
					function liteSection(name, on) {
						with (document.getElementById(name)) style.backgroundColor = on ? \'#cccccc\' : \'#ffffff\';
					};
					function gotoSection(name) {
						with (document.getElementById(name)) style.display = \'inline\';
					};
				//-->
				</SCRIPT>';
			$raiseObjectInit = 1;
		};
		return '';
	};

	function getTypeOf(&$value) {
		if (is_object($value)) {
			$type = 'class';
		}
		else if (is_array($value)) {
			$type = 'array';
		}
		else if (is_numeric($value) && $value > mktime(0,0,0,1,1,2000) && $value < mktime(0,0,0,1,1,2010)) {
			$type = 'date';
		}
		else if (is_string($value)) {
			$type = 'str';
		}
		else if (is_integer($value)) {
			$type = 'int';
		}
		else if (is_double($value)) {
			$type = 'double';
		}
		else if (is_bool($value)) {
			$type = 'bool';
		}
		else if ($value === NULL) {
			$type = 'NULL';
		}
		else {
			$type = 'other';
		};
		return $type;
	};
	
	$__recursion = array();
	function isRecursiv(&$value, $ref) {
		global $__recursion;
		
		$xref = (is_array($value) && count($value) > 10) || is_object($value) || (is_string($value) && strlen($value) > 100);
		if ($xref) {
			$md5 = md5(serialize($value));
			if (isset($__recursion[$md5])) {
				return $__recursion[$md5];
			}
			$__recursion[$md5] = $ref;
		};
		return false;
	}

	function getValueOf(&$value) {
		global $__indent;
		global $raiseObjectNr;
		
		$ret = '';
		
		$id = ++$raiseObjectNr;
		$ref = isRecursiv($value, $id);
		
		if ($ref) {
			if (is_object($value)) {
				$title = get_class($value);
			}
			else if (is_array($value)) {
				$title = '[' . count($value) . ']';
			}
			else {
				$title = substr($value, 0, 10);
			}
			$ret = '<span style="cursor:hand;color:#000000;background-color:white;" onmouseout="liteSection(\'title' . $ref . '\', 0)" onmouseover="liteSection(\'title' . $ref . '\', 1)" onclick="gotoSection(\'obj' . $ref . '\');">' . $title . '&nbsp;&rarr;</span>';
		}
		else if (is_object($value)) {
			$arrClassVars = get_class_vars(get_class($value));
			$arrObjVars = get_object_vars($value);
			$arr = $arrObjVars;// array_merge($arrObjVars, $arrClassVars);
			
			$title = get_class($value);
			
			$__indent++;
			foreach($arr as $name => $val) {
				$ret .= dumpLine('$'.$name, $val);
			};
			$__indent--;
			
			$ret = '<span id="title' . $id . '" style="cursor:hand;color:#000000;background-color:white;" onclick="switchSection(\'obj' . $id . '\');">' . $title . '&nbsp;&darr;</span><br>
				<div id="obj' . $id . '" style="display:' . (DEBUG_INITIAL_DISPLAY ? 'inline' : 'none') . ';">
				<table style="font-family:monospace; font-size:12px;">' . $ret . '</table></div>';
		}
		else if (is_array($value)) {
			$arr = $value;
			
			$title = '[' . count($value) . ']';
			
			$__indent++;
			foreach($arr as $name => $val) {
				$ret .= dumpLine('$'.$name, $val);
			};
			$__indent--;
			
			$ret = '<span id="title' . $id . '" style="cursor:hand;color:#000000;background-color:white;" onclick="switchSection(\'obj' . $id . '\');">' . $title . '&nbsp;&darr;</span><br>
				<div id="obj' . $id . '" style="display:' . (DEBUG_INITIAL_DISPLAY ? 'inline' : 'none') . ';">
				<table style="font-family:monospace; font-size:12px;">' . $ret . '</table></div>';
		}
		else if (is_numeric($value) && $value > mktime(0,0,0,1,1,2000) && $value < mktime(0,0,0,1,1,2010)) {
			$ret = date('Y-m-d H:i:s', $value);
		}
		else if (is_string($value)) {
			if (strpos($value, "\n") !== false) {
				$arrLines = explode("\n", $value);
				$ret = '';
				foreach($arrLines as $line) $ret .= '\'' . htmlentities($line) . '\'<br>';
				$ret = '<pre>' . substr($ret, 0, -4) . '</pre>';
			}
			else {
				$ret = '\'' . htmlentities($value) . '\'';
			};
		}
		else if (is_bool($value)) {
			$ret = $value ? 'true' : 'false';
		}
		else {
			$ret = $value;
		};
		return $ret;
	};

	function dumpLine($name, &$var) {
		$value = getValueOf($var);
		$type = getTypeOf($var);
		return '
			<tr>
				<td style="vertical-align:top; color:#000000; font-weight:bold;" nowrap>' . $name . '</td>
				<td style="vertical-align:top; color:#0000ff;" nowrap>' . $type . '</td>
				<td style="vertical-align:top; color:#000000;">' . $value . '</td>
			</tr>';
	};

	$__indent = 0;
	$raiseObjectNr = 0;
	$raiseObjectName = '';
	
	function dump($value, $name= '') {
		global $__recursion; $__recursion = array();
		$ret = initRaiseSection();
		if (!empty($name)) $name = '$' . $name;
		return $ret . '<div style="text-align:left;"><table style="font-family:monospace; font-size:12px; background-color:#ffffff; color:#000000;">' . dumpLine($name, $value) . '</table></div>';
	};


	function setTestSection($section) {
		echo '<br>' . $section . '<hr>';
	};

	function raiseError($isOk, $errMsg, $obj = NULL) {
		initRaiseSection();
		echo '<span style="font-weight:bold; color:' . ($isOk ? '#008000' : '#c00000') . ';">' . ($isOk ? 'ok' : 'failed') . '</span> ' . $errMsg . '';
		if ($obj === NULL) {
			$obj = $GLOBALS['raiseObjectName'];
		}
		if (is_string($obj) && isset($GLOBALS[$obj])) {
			$obj = $GLOBALS['obj'];
		}
		if ($obj !== NULL) {
			$data = dump($obj);
			global $raiseObjectNr; $raiseObjectNr++;
			echo '
				<span style="cursor:hand;" onclick="switchSection(\'obj' . $raiseObjectNr . '\');">*</span>
				<div id="obj' . $raiseObjectNr . '" style="display:' . (DEBUG_INITIAL_DISPLAY ? 'inline' : 'none') . ';"><pre>' . $data . '</pre><hr></div>';
		};
		echo '<br>';
		
		return !$isOk;
	};

?>