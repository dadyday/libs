<?php
/* ************************ interne helper
	function joinTime($h,$i,$s,$d,$m,$y) {
	function splitTime($time,&$h,&$i,&$s,&$d,&$m,&$y) {
	function dateEx($format, $dateTime) {
	function timeEx($format, $time) {

// ************************ Datumsanteile ermitteln
	function dateOf($time) :timeinteger
	function timeOf($time) :timeinteger
	function dowOf($time) :int 0-6
	function dowNameOf($time) :string
	function dowShortNameOf($time) :string
	function weekOf($time) :integer
	function monthOf($time) :integer 1-12
	function shortYearOf($time) :integer 0-99
	function yearOf($time) :integer
	function monthNameOf($time) :string
	function monthShortNameOf($time) :string
	function dayOf($time) :integer 1-31
	function hourOf($time) :integer 0-24
	function minuteOf($time) :integer 0-59

	function timeStr($format, $time) :string
	function dateStr($format, $date) :string
	function parseDateTime($string, &$h, &$i, &$s, &$d, &$m, &$y)

// ************************ Datums rechnung
	function dateOffsetTo($time, $offset = 0) :timeinteger
	function timeOffsetTo($time, $offset = 0) :timeinteger

// ************************ Datums transformation
	function yearBeginOf($time) :timeinteger
	function quarterBeginOf($time) :timeinteger
	function monthBeginOf($time) :timeinteger
	function weekBeginOf($time) :timeinteger
	function dayBeginOf($time) :timeinteger
	function hourBeginOf($time) :timeinteger

// ************************ Datumstexte
	function monthName($month) :string
	function monthShortName($month) :string
	function dowName($dow) :string
	function dowShortName($dow) :string
	function isLeap($year) {
	
// *********************** zinsrechnung
	function effectivInterest($dateFrom, $dateTo, $rate)
*/

	define('TIME_SECOUND', 1);
	define('TIME_MINUTE', 60 * TIME_SECOUND);
	define('TIME_HOUR', 60 * TIME_MINUTE);
	define('TIME_DAY', 24 * TIME_HOUR);
	/**
	* helper kapselung von mktime
	* achtung! $d und $m in params vertauscht!!
	*/
	function joinTime($h,$i,$s,$d,$m,$y) {
		if ($y == 0) {
			$time = $h*3600+$i*60+$s;
			//$time = mktime($h,$i,$s);
		}
		else {
			$time = @mktime($h,$i,$s,$m,$d,$y);
			
			if ($time < 0) {
				//if ($time < 0) printf("time: %d h:%d i:%d s:%d d:%d m:%d y:%d<br>",$time, $h,$i,$s,$d,$m,$y);
				//return 0;
			}
		}
		return $time;
	}

	/**
	* helper gegenstück zu jointime
	*/
	function splitTime($time,&$h,&$i,&$s,&$d,&$m,&$y) {
		$y = dateEx('Y', $time);
		$m = dateEx('m', $time);
		$d = dateEx('d', $time);
		$h = dateEx('H', $time);
		$i = dateEx('i', $time);
		$s = dateEx('s', $time);
	}

	/**
	* helper für date
	*/
	function dateEx($format, $dateTime) {
		if ($dateTime == 0) {
			return '';
		}
		return @date($format, $dateTime);
	}
	function timeEx($format, $time) {
		$h = floor(($time % (24*60*60)) / (60*60));
		$i = floor(($time % (60*60)) / (60));
		$s = floor(($time % (60)));
		return date($format, mktime($h,$i,$s));
	}

// ************************ Datumsanteile ermitteln

	/**
	* liefert datumsanteil $time
	*/
	function dateOf($time) {
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$time = joinTime(0,0,0,$d,$m,$y);
		return $time;
	}

	/**
	* liefert zeitanteil $time
	*/
	function timeOf($time) {
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$time = joinTime($h,$i,$s,0,0,0);
		return $time;
	}

	function isLeap($year) {
		return dateEx('L', joinTime(0,0,0,1,1,$year));
	}
	
	/**
	* liefert wochentag von $time
	* mo. -> 0 ... so. -> 6
	*/
	function dowOf($time) {
		/* hack:
			$time = so ->  (0+13 % 7) ->  6;
			$time = sa ->  (6+13 % 7) ->  5;
			$time = di ->  (2+13 % 7) ->  1;
			$time = mo ->  (1+13 % 7) ->  0;
		*/
		return (dateEx('w', $time) + 13) % 7;
	}

	/**
	* liefert wochentagsnamen von $time
	*/
	function dowNameOf($time) {
		return dowName(dowOf($time));
	}

	/**
	* liefert kurze wochentagsnamen von $time
	*/
	function dowShortNameOf($time) {
		return dowShortName(dowOf($time));
	}

	/**
	* liefert wochennummer von $time
	* 1. KW ist die, mit min. 4 Januartagen
	*/
	function weekOf($time) {
		return dateEx('W', $time);
	}

	/**
	* liefert monat von $time
	* jan. -> 1 ...
	*/
	function monthOf($time) {
		return dateEx('n', $time);
	}

	/**
	* liefert zweistelliges jahr von $time
	* 06
	*/
	function shortYearOf($time) {
		return dateEx('y', $time);
	}

	/**
	* liefert jahr von $time
	* 2006
	*/
	function yearOf($time) {
		return dateEx('Y', $time);
	}

	/**
	* liefert monatsnamen von $time
	*/
	function monthNameOf($time) {
		return monthName(monthOf($time));
	}

	/**
	* liefert kurze monatsnamen von $time
	*/
	function monthShortNameOf($time) {
		return monthShortName(monthOf($time));
	}

	/**
	* liefert tag von $time
	* 1. -> 1 ...
	*/
	function dayOf($time) {
		return (integer) dateEx('j', $time);
	}


	/**
	* liefert stunde von $time
	* 1. -> 1 ...
	*/
	function hourOf($time) {
		return (integer) timeEx('H', $time);
	}

	/**
	* liefert stunde von $time
	* 1. -> 1 ...
	*/
	function minuteOf($time) {
		return (integer) timeEx('i', $time);
	}



// ************************ Datums rechnung

	/**
	* addiert zu $time tage monate und jahre hinzu
	* $offset im Format JJMMDD
	* ACHTUNG! dateOffsetTo(time(), 100 - 1) liefert nicht letzten Tag im Folgemonat!!!
	*/
	function dateOffsetTo($time, $offset = 0) {
		$sign = $offset ? $offset / abs($offset) : 0;
		$offset = abs($offset);
		
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$y += $sign * floor($offset / 10000);
		$m += $sign * floor(($offset % 10000) / 100);
		$d += $sign * floor($offset % 100);
		
		$time = joinTime($h,$i,$s,$d,$m,$y);
		return $time;
	}

	/**
	* addiert zu $time sekunden minuten und stunden hinzu
	* $offset im Format HHMMSS
	* ACHTUNG! addTimeRange(time(), 100 - 1) addiert nicht 59 sek.!!!
	*/
	function timeOffsetTo($time, $offset = 0) {
		$sign = $offset ? $offset / abs($offset) : 0;
		$offset = abs($offset);
		
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$h += $sign * floor($offset / 10000);
		$i += $sign * floor(($offset % 10000) / 100);
		$s += $sign * floor($offset % 100);
		
		$time = joinTime($h,$i,$s,$d,$m,$y);
		return $time;
	}

	/**
	* liefert tagesdifferenz für kaufmännische Zinsrechnung
	* jeder monat hat 30 tage, das Jahr 360
	*
	* @todo muss noch getestet werde !!!
	*/
	function commercialDayOffset($time1, $time2) {
		$y = yearOf($time2) - yearOf($time1);
		$m = monthOf($time2) - monthOf($time1);
		$d = dayOf($time2) - dayOf($time1);
		return $y*360 + $m*30 + $d;
	}

// ************************ Datums transformation

	/**
	* liefert den 1. Januar des Jahres
	*/
	function yearBeginOf($time) {
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$time = joinTime(0,0,0,1,1,$y);
		return $time;
	}

	/**
	* liefert den 1. des monats am Quartalanfang
	*/
	function quarterBeginOf($time) {
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$m = floor(($m-1)/3)*3+1;
		$time = joinTime(0,0,0,1,$m,$y);
		return $time;
	}

	/**
	* liefert den 1. des monats, 0 Uhr abh. von $time
	*/
	function monthBeginOf($time) {
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$time = joinTime(0,0,0,1,$m,$y);
		return $time;
	}

	/**
	* liefert den montag der woche, 0 Uhr abh. von $time
	*/
	function weekBeginOf($time) {
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$d -= dowOf($time);
		$time = joinTime(0,0,0,$d,$m,$y);
		return $time;
	}

	/**
	* liefert den tag, 0 Uhr abh. von $time
	*/
	function dayBeginOf($time) {
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$time = joinTime(0,0,0,$d,$m,$y);
		return $time;
	}

	/**
	* liefert tag und stundenbegin abh. von $time
	*/
	function hourBeginOf($time) {
		splitTime($time,$h,$i,$s,$d,$m,$y);
		$time = joinTime($h,0,0,$d,$m,$y);
		return $time;
	}




// ************************ Datumstexte

	/**
	* liefert monatsnamen
	*/
	function monthName($month) {
		$arrMonthName = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'); 
		return $arrMonthName[($month-1) % 12];
	}

	/**
	* liefert kurze monatsnamen
	*/
	function monthShortName($month) {
		$arrMonthName = array('Jan','Feb','Mär','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez'); 
		return $arrMonthName[($month-1) % 12];
	}

	/**
	* liefert wochentagsnamen
	* dow = 0 -> Montag, ... 6 -> Sonntag
	*/
	function dowName($dow) {
		$arrDowName = array('Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag','Sonntag');
		return $arrDowName[$dow % 7];
	}

	/**
	* liefert wochentagsnamen
	* dow = 0 -> Mo, ... 6 -> So
	*/
	function dowShortName($dow) {
		$arrDowName = array('Mo','Di','Mi','Do','Fr','Sa','So');
		return $arrDowName[$dow % 7];
	}

	function timeStr($format, $time) {
		if (!is_numeric($time)) {
			parseDateTime($time, $h, $i, $s, $d, $m, $y);
			$time = joinTime($h,$i,$s,$d,$m,$y);
		};
		return timeEx($format, $time);
	}

	function dateStr($format, $date) {
		if (!is_numeric($date)) {
			parseDateTime($date, $h, $i, $s, $d, $m, $y);
			$date = joinTime($h,$i,$s,$d,$m,$y);
		};
		return dateEx($format, $date);
	}

	/*
	date
		2006-01-01 (time)?
		06-01-01 (time)?
		06-1-1 (time)?
		01.01.2006 (time)?
		01.01.06 (time)?
		1.1.06 (time)?
	time
		(date)? 01:01:01
		(date)? 1:1:1
		(date)? 01:01
		(date)? 1:1
		(date)? 01
		(date)? 1
	*/
	function parseDateTime($string, &$h, &$i, &$s, &$d, &$m, &$y) {
		$EREG_TIMESTR = '([0-9]{1,2})(:([0-9]{1,2})(:([0-9]{1,2}))?)?';
		$EREG_DATESTR = '(([0-9]{1,2}|[0-9]{4})-([0-9]{1,2})-([0-9]{1,2}))|(([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{1,2}|[0-9]{4}))';
		
		ereg('^('.$EREG_DATESTR.')? ?('.$EREG_TIMESTR.')?$', $string, $r);
		//echo '<pre>time' . $string . print_r($r,true) . '</pre>';
		
		if (!empty($r[2])) {
			$y = $r[3]; $m = $r[4]; $d = $r[5];
			if ($y < 100) $y += $y > 20 ? 1900 : 2000;
		}
		else if (!empty($r[6])) {
			$d = $r[7]; $m = $r[8]; $y = $r[9];
			if ($y < 100) $y += $y > 20 ? 1900 : 2000;
		}
		else {
			$y = $m = $d = 0;
		};
		
		if (!empty($r[10])) {
			$h = $r[11]; $i = $r[13]; $s = $r[15];
		}
		else {
			$h = $i = $s = 0;
		};
		
		return !empty($r[0]);
	}

// für tests
	function printDateTime($time) {
		return dateEx('Y-m-d H:i:s', $time);
	}
	
/* Effektivzinsmethode (http://zinsmethoden.de/)
	ACHTUNG! zinstage d sind dateFrom < d <= dateTo
	d.h: 1.1 - 31.1 sind 30 zinstage
*/
	function effectiveYield($dateFrom, $dateTo, $rate) {
		$result = 0;
		
		$dateFrom = dayBeginOf($dateFrom);
		$dateTo = dayBeginOf($dateTo);
		
		$yearFrom = yearOf($dateFrom);
		$yearTo = yearOf($dateTo);
		
		for($year = $yearFrom; $year < $yearTo; $year++) {
			$days = (joinTime(0,0,0,31,12,$year) - $dateFrom) / (24*60*60);
			$amount = ($days / (isLeap($year) ? 366 : 365)) * $rate;
			$result += $amount;
	//echo '<br>&nbsp;&nbsp;'.$days.' '.$amount * 10000;
			$dateFrom = joinTime(0,0,0,31,12,$year);
			
		}
		$days = ($dateTo - $dateFrom) / (24*60*60);
		$amount = ($days / (isLeap($year) ? 366 : 365)) * $rate;
		$result += $amount;
	//echo '<br>&nbsp;&nbsp;'.$days.' '.$amount * 10000;
		return $result;
	}

?>