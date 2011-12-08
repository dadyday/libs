<?php 

	class StateMachine {
		
		static $aStateDef = array();
		static $initState = 0;
		
		var $state;
		
		function __construct(&$property) {
			$this -> state =& $property;
			$this -> state = self::$initState;
		}
		
		static function initStateMachine() {
		}
		
		static function setInitState($state) {
			self::$initState = $state;
		}
		static function addTransition($state, $method, $goal) {
			if (empty(self::$aStateDef[$state])) self::$aStateDef[$state] = array();
			self::$aStateDef[$state][$method] = $goal;
		}
		
		function initState($state) {
			return $this -> state = $state;
		}
		
		function getTransitionGoal($method) {
			if (empty(self::$aStateDef[$this -> state])) return false;
			$aTransitionDef = self::$aStateDef[$this -> state];
			if (empty($aTransitionDef[$method])) return false;
			return $aTransitionDef[$method];
		}
		
		function checkTransition($method) {
			return $this -> getTransitionGoal($method) !== false; 
		}
		
		function doTransition($method) {
			$state = $this -> getTransitionGoal($method); 
			if ($state === false) return false;
			$this -> state = $state; 
			return true;
		}
	}
?>