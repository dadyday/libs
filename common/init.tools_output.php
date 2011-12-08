<?php
/*********************************************
	functionen für die formatierte Ausgabe interner Daten
	
		$outputValue = to...Str($internalValue);
		$internalValue = from...Str($outputValue);
	require_once('init.tools_datetime.php');
	
	function toAmountStr($value) {
		$value = round(100 * (double) $value);
		$sign = $value < 0 ? '-' : '';
		$value = abs($value);
		return sprintf('%s%d,%02d', $sign, $value/100, $value%100);
	}
	function fromAmountStr($string) {
		$string = str_replace(',', '.', $string);
		return (double) $string;
	}


	function toDateStr($value) {
		$value = (integer) $value;
		if (empty($value)) return '';
		return date('d.m.Y', $value);
	}
	function fromDateStr($string) {
		parseDateTime($string, $h, $i, $s, $d, $m, $y);
		return joinTime($h,$i,$s,$d,$m,$y);
	}

	function toDateTimeStr($value) {
		$value = (integer) $value;
		if (empty($value)) return '';
		return date('d.m.Y H:i:s', $value);
	}
	function fromDateTimeStr($string) {
		parseDateTime($string, $h, $i, $s, $d, $m, $y);
		return joinTime($h,$i,$s,$d,$m,$y);
	}

*/
	/**
     * formatiert String, wenn er nicht leer ist
	 * z.B.
	 *	formatNonEmpty($vorname, $name.', %s', $name); // "Silie" oder "Silie, Peter"
	 *	$vorname . formatNonEmpty($name) // "Peter" oder "Peter Silie"
     */
	function formatNonEmpty($text, $formatText = ' %s', $elseText = '') {
		return !empty($text) ? sprintf($formatText, $text) : $elseText;
	}
?>