<?php
/*
 * PHP Mini Framework
 * Copyright (C) 2008  Javier Arias
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
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