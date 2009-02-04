<?php
/*
 * Linx PHP Framework
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

/**
 * This is a global Configuration manager. You could access this class anywhere in the program
 * wihtout need of instancing it.
 * Default values are taken form the config.ini file located at the root of the program.
 */
class Configuration{
	static private $config=array();
	
	/**
	 * with this function you can set a default value for a configuration key when it is not set on the ini file.
	 */
	public static function set_default($section,$key,$value){
		if (!isset(self::$config[strtolower($section)]) or !isset(self::$config[strtolower($section)][strtolower($key)]))
		self::set($section,$key,$value);
	}
	
	/**
	 * this set or replace a value to a configuration key
	 */
	public static function set($section,$key,$value){		
		self::$config[strtolower($section)][strtolower($key)]=$value;
	}
	
	/**
	 * this function load the values from a ini file
	 */
	public static function load($filename){		
		if (file_exists($filename)){
			$array = parse_ini_file($filename, true);
			
			self::set_values($array);
		}
	}
	
	/**
	 * this function load the values from a multidimensional array
	 */
	public static function set_values($array){
		foreach ($array as $section=>$values){
			foreach ($values as $key=>$value){
				self::set($section,$key,$value);
			}
		}
	}
	
	/**
	 * this function allows you to get the value of a configuration key.
	 */
	public static function get($section,$key=false,$default=false){
		if(isset(self::$config[strtolower($section)]) and isset(self::$config[strtolower($section)]) and !$key)
			return self::$config[strtolower($section)];
		elseif(isset(self::$config[strtolower($section)]) and isset(self::$config[strtolower($section)][strtolower($key)]))
			return self::$config[strtolower($section)][strtolower($key)];
		else 
			return $default;
	}
}
?>