<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias. *
 * Licensed under GNU General Public License.
 */
 
class Registry{
	private static $_vars=array();
	
	public static function set($key,$value){	
		self::$_vars[$key]=$value;
		return true;	
	}
	
	public static function get($key){
		if (!isset(self::$_vars[$key])) throw new Exception("Value '$key' doen't exists on the registry.");
		return self::$_vars[$key];
	}
	
	public static function remove($key){
		if (!isset(self::$_vars[$key])) return false;
		unset(self::$_vars[$key]);
		return true;
	}
	
	public static function clear(){
		self::$_vars=array();
	}
	
	public static function exists($key){
		return isset(self::$_vars[$key]);
	}
}
?>