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
 
class Configuration{
	static private $config=array();	
	public static function set_default($section,$key,$value){
		if (!isset(self::$config[strtolower($section)]) or !isset(self::$config[strtolower($section)][strtolower($key)]))
		self::set($section,$key,$value);
	}
	public static function set($section,$key,$value){		
		self::$config[strtolower($section)][strtolower($key)]=$value;
	}
	public static function load($filename){		
		if (file_exists('config.ini')){
			$array = parse_ini_file("config.ini", true);
			
			self::set_values($array);
		}
	}
	public static function set_values($array){
		foreach ($array as $section=>$values){
			foreach ($values as $key=>$value){
				self::set($section,$key,$value);
			}
		}
	}
	public static function get($section,$key,$default=false){
		if (isset(self::$config[strtolower($section)]) and isset(self::$config[strtolower($section)][strtolower($key)]))
		return self::$config[strtolower($section)][strtolower($key)];
		else 
		return $default;
	}
}
?>