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
 
class Application{
	static public $request_url;
	static public $base_url;
	
	static private $_initialized=false;
	static private $_router=false;
	
	static protected $_classes_paths=array();
	
	
	static public function add_class_path($name_patern='/(.+)/',$filename='$1.php',$directory=''){
		self::$_classes_paths[]=array('name_patern'=>$name_patern,'filename'=>$filename,'directory'=>$directory);
	}
	static public function get_classes_paths(){
		return self::$_classes_paths;
	}
	static public function get_site_path(){
		# TODO: armar esto utilizando la configuracion.
		# Configuration::get		
		return realpath(dirname(__FILE__).'/../').'/';
	}
	
	static public function set_application_router(IApplicationRouter $router){
		self::$_router=$router;
	}
	
	static public function route($redirect=false){		
		if (!$redirect){
			
			
			self::$_router->delegate();
			
						
		}
		else{
			if (!headers_sent($filename, $linenum)) {
				header('Location: ' . Application::$request_url);
				exit;
			} else {
				throw new FatalErrorException("Headers already sent in $filename on line $linenum\n" .
					"Cannot redirect, for now please click this <a " .
					"href=\"http://www.example.com\">link</a> instead\n");								
			}
		}
	}
	
	function __construct(){
		if (!self::$_initialized){
			self::$request_url = new Url();	
			self::set_application_router(new ApplicationRouter());
			self::$_initialized = true;
		}		
	}
}
?>