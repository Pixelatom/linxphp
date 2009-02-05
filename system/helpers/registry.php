<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias. *
 * Licensed under GNU General Public License.
 */
/**
 * Some people say global variables are a bad practice, insted of GLOBAL you could use this class
 * to store and get variables.
 * http://blog.case.edu/gps10/2006/07/22/why_global_variables_in_php_is_bad_programming_practice
 */
class Registry{
	private static $_vars=array();
	/**
	 * set a variable
	 *@param String $key the name of the variable
	 *@value Mixed $value the value of the variable
	 */
	public static function set($key,$value){	
		self::$_vars[$key]=$value;
		return true;	
	}
	
	/**
	 * get the value of a variable previously set
	 *@param String $key the name of the variable.
	 */
	public static function get($key){
		if (!isset(self::$_vars[$key])) throw new Exception("Value '$key' doen't exists on the registry.");
		return self::$_vars[$key];
	}
	
	/**
	 * unset a variable
	 *@param String $key the name of the variable to remove
	 */
	public static function remove($key){
		if (!isset(self::$_vars[$key])) return false;
		unset(self::$_vars[$key]);
		return true;
	}
	
	/**
	 * unset all the variables
	 */
	public static function clear(){
		self::$_vars=array();
	}
	
	/**
	 * returns true if a variable with that name is already set
	 *@param String $key the name of the variable to check
	 */
	public static function exists($key){
		return isset(self::$_vars[$key]);
	}
}
?>