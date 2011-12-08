<?php
// DEBUG-Mode (JA|NEIN)
	if(!defined("IS_DEBUG_MODE")) define("IS_DEBUG_MODE", true);

// keine Fehlerausgaben
	if(!defined("ERR_OUTPUT_MODE_NONE")) define("ERR_OUTPUT_MODE_NONE", 0);

// nur Info das ein Fehler aufgetreten ist
	if(!defined("ERR_OUTPUT_MODE_PUBLIC")) define("ERR_OUTPUT_MODE_PUBLIC", 1);

// Fehlerausgaben direkt im Web
	if(!defined("ERR_OUTPUT_MODE_WEB")) define("ERR_OUTPUT_MODE_WEB", 10);


	if(!defined("ERR_HANDLE_LAST")) define("ERR_HANDLE_LAST", 1);
	if(!defined("ERR_HANDLE_LIST")) define("ERR_HANDLE_LIST", 10);
	if(!defined("ERR_HANDLE_BOTH")) define("ERR_HANDLE_BOTH", 100);


	if(!defined("ERR_MSG_TEMPLATE")) define("ERR_MSG_TEMPLATE", "<span style=\"color:%s\">%s: </span> %s <i>%s</i><br>");

// ErrorLevel
	if(!defined("ERR_PRIORITY_WARNING")) define("ERR_PRIORITY_WARNING", 1);
	if(!defined("ERR_PRIORITY_ERROR")) define("ERR_PRIORITY_ERROR", 10);
	if(!defined("ERR_PRIORITY_EXCEPTION")) define("ERR_PRIORITY_EXCEPTION", 100);

	class TErr {
		var $ErrMsg;
		var $ErrDtm;
		var $ErrPriority;

	// Constructor
		function TErr() {
		}

		function setErr($ErrMsg, $ErrPriority) {
			$this -> ErrMsg			= $ErrMsg;
			$this -> ErrPriority	= $ErrPriority;
			$this -> ErrDtm 		= date("Y-m-d H:i:s");
			return true;
		}

		function clearErr() {
			$this -> ErrMsg			= "";
			$this -> ErrPriority	= "";
			$this -> ErrDtm			= "";
		}
	} // end of TErr

/**********************************************************************************************/

	class handleErr {
		var $isErr;					// Fehler vorhanden? (nur lastErr)
		var $lastErr;				// TErr
		var $arrErrList = array();	// Fehlerliste

		var $handleWhat;			// was soll berücksichtigt werden?	-> ERR_HANDLE_*
		var $outputMode;			// Fehlerausgabeziel -> ERR_OUTPUT_MODE_*

	// Constructor
		function handleErr() {
			$this -> isErr = false;
			$this -> lastErr = new TErr;
//			$this -> setHandlingMode(ERR_OUTPUT_MODE_WEB, ERR_HANDLE_LAST);
//			$this -> setHandlingMode(ERR_OUTPUT_MODE_PUBLIC, ERR_HANDLE_LAST);
			$this -> setHandlingMode(ERR_OUTPUT_MODE_NONE, ERR_HANDLE_LAST);

		}

	// setzt FehlerMessage, Priorität
		function setErr($ErrMsg, $ErrPriority) {
			if(!empty($ErrMsg) and !empty($ErrPriority)) {
				$this -> saveLastErr();
				$this -> clearLastErr();
				$this -> isErr = true;
				$this -> lastErr -> setErr($ErrMsg, $ErrPriority);
				return true;
			} else {
				return false;
			}
		}

	// gibt die Fehlerliste(array of TErr) zurück
		function getErrList() {
			return $this -> arrErrList;
		}

	// speichert den Letzten Fehler in der Fehlerliste
		function saveLastErr($saveOverRide=false) {
			if($this -> isErr or $saveOverRide)
				@array_push($this -> arrErrList, $this -> lastErr);
		}

	// setzt letzen Fehler zurück
		function clearLastErr() {
			$this -> isErr = false;
			return $this -> lastErr -> clearErr();
		}

	// löscht Fehlerliste
		function clearErrList() {
			$this -> arrErrList = array();
			return true;
		}

	// löscht Fehlerliste & setzt letzen Fehler zurück
		function clearErr() {
			if($this -> handleWhat == ERR_HANDLE_LIST or $this -> handleWhat == ERR_HANDLE_BOTH)
				$this -> clearErrList();

			if($this -> handleWhat == ERR_HANDLE_LAST or $this -> handleWhat == ERR_HANDLE_BOTH)
				$this -> clearLastErr();
			return true;
		}


	// setzt den Modus für doErrHandling()
		function setHandlingMode($outputMode, $handleWhat) {
			$this -> outputMode = $outputMode;
			$this -> handleWhat = $handleWhat;
		}


		function _handleWebErr($errObj) {
			switch($errObj -> ErrPriority) {
				case ERR_PRIORITY_WARNING:
					$errString = nl2br(sprintf(ERR_MSG_TEMPLATE, "#BB0000", "Warnung",  $errObj -> ErrMsg, $errObj -> ErrDtm));
					break;

				case ERR_PRIORITY_ERROR:
					$errString = nl2br(sprintf(ERR_MSG_TEMPLATE, "#DD0000", "Fehler",  $errObj -> ErrMsg, $errObj -> ErrDtm));
					break;

				case ERR_PRIORITY_EXCEPTION:
					$errString = nl2br(sprintf(ERR_MSG_TEMPLATE, "#FF0000", "schwerer Fehler",  $errObj -> ErrMsg, $errObj -> ErrDtm));
					break;

				default:
					$errString = nl2br(sprintf(ERR_MSG_TEMPLATE, "#0000CC", "unbekannter Fehlertype",  $errObj -> ErrMsg, $errObj -> ErrDtm));
			}

			return $errString;
		}

		function _handleWeb() {
			$errString = "<h3>FEHLER:</h3>";
			if($this -> isErr and ($this -> handleWhat == ERR_HANDLE_LAST or $this -> handleWhat == ERR_HANDLE_BOTH)) {
				$errString .= "zuletzt aufgetretener Fehler:<br><div style=\"margin-left:15px\">";
				$errString .= $this -> _handleWebErr($this -> lastErr);
				$errString .= "</div><br>";
			}

			if(count($this -> getErrList()) > 0 and ($this -> handleWhat == ERR_HANDLE_LIST or $this -> handleWhat == ERR_HANDLE_BOTH)) {
				$errString .= count($this -> getErrList()) . " Fehler oder Warnungen in der Liste: <br><div style=\"margin-left:15px\">";
				foreach($this -> getErrList() as $errObj) {
					$errString .= $this -> _handleWebErr($errObj);
				}
				$errString .= "</div><br>";
			}

			if(count($this -> getErrList()) > 0 or $this -> isErr)
				echo $errString;
		}

		function _handlePublic() {
			$errString .= "<div align=\"center\">";
			$errString .= "<h3>FEHLER/ERROR</h3>";
			$errString .= "Es ist ein unerwarteter Fehler aufgetreten!<br>";
			$errString .= "Bitte versuchen Sie es zu einem sp&auml;teren Zeitpunkt nocheinmal.<br>";
			$errString .= "<br><br>";
			$errString .= "An unexpected error has been occurred<br>";
			$errString .= "Please try again later.<br>";
			$errString .= "</div><br><br>";
			$errString .= "<br><br>";
			echo $errString;
		}


	// Behandlung des Fehlers
		function doErrHandling($clearErr = true) {
			switch($this -> outputMode) {
				case ERR_OUTPUT_MODE_PUBLIC:
					$this -> _handlePublic();
					break;

				case ERR_OUTPUT_MODE_WEB:
					$this -> _handleWeb();
					break;
			}

			if($clearErr)
				$this -> clearErr();

		} // function doErrHandling()




	} // End of class handleErr
?>