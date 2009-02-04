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
  * The Application class is at the center of the Framework.
  * It loads up the Router, dispatches to the controller and does the final output.
  */
class Application{
	/**
	 *@var Url current url of the application, Router will take this value to determine which controller should be executed
	 */
	static public $request_url;
	/*static public $base_url;*/
	
	static private $_initialized=false;
	static private $_router=false;
	
	static protected $_classes_paths=array();
	
    /**
     * returns the current controller name.
     * this function only returns a value once that the router was executed
     */
    static public function get_controller(){
        return self::$_router->controller;
    }
    /**
     * returns the current controller's function called
     * this function only returns a value once that the router was executed
     */
    static public function get_action(){
        return self::$_router->action;
    }
    /**
     * current arguments passed to the controller.
     * this function only returns a value once that the router was executed
     */
    static public function get_args(){
        return self::$_router->args;
    }
	/**
	 * PHP has functionality to automatically load files if a certain class has not been loaded yet. Linx employs this functionality.
	 * this function adds a new direcotry where to find classes to auto load.
	 *@param string $name_patern a regexp pattern for the class name. it must have a captouring group defined.
	 *@param string $filename it will set the name of the file which will be included. it should use the capturing group set in the previous parameter
	 *@param string $directory the directory where the file to include is located.
	 */
	static public function add_class_path($name_patern='/(.+)/',$filename='$1.php',$directory=''){
		self::$_classes_paths[]=array('name_patern'=>$name_patern,'filename'=>$filename,'directory'=>$directory);
	}
	/**
	 * Get an array containing all the directories where the framework should search for classes
	 */
	static public function get_classes_paths(){
		return self::$_classes_paths;
	}
	
	/**
	 * it returns the phyphisical path where the application is hosted at.
	 */
	static public function get_site_path(){
		# TODO: armar esto utilizando la configuracion.
		# Configuration::get		
		return realpath(dirname(__FILE__).'/../../').'/';
	}
	
	/**
	 * Use this function if you want to replace the default routing behavior
	 */
	static public function set_application_router(IApplicationRouter $router){
		self::$_router=$router;
	}
	
	/**
	 * makes the router search for a controller to execute depending on the current application url.
	 *@param boolean $redirect if true makes the browser redirect to the current application url
	 */
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