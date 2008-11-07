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
 
include_once('iapplicationrouter.php');
class ApplicationRouter implements IApplicationRouter {
	
	protected function not_found(){		
		if (!headers_sent()){
			header(' ', true, 404);
			header('Status: 404 Not Found');
			header('HTTP/1.0 404 Not Found');	
		}
		echo "Page not found";
		die();
	}
		
	public function delegate(){
		# indica si la salida es solo la de un componente o un controlador completo
		$component_output=false;
		
		# primero nos fijamos si hay que renderizar solamente un componente
		if (Application::$request_url->param_exists('_component')){
			
			$component_class_name=(Application::$request_url->get_param('_component'));
			Application::$request_url->remove_param('_component');			
			$original_url=clone Application::$request_url;
			#$component=new $component_class_name;			
			#$component->show();
			#return;
			$component_output=true;
			ob_start();		
		}
		
		# analizamos la ruta y extraemos el nombre del controlador
		$this->get_controller($file,$controller,$action,$args);
		
		# incluye el archivo del controller
		if (!is_readable($file)){ 			
			$this->not_found();			
		}
		else{			
			include_once($file);
		}
		
		# inicializa la clase
		$class = ucfirst($controller).'Controller';
		$controller = new $class();
		
		if (!is_callable(array($controller,$action))){
			$this->not_found();
		}
		
		$controller->$action();
		
		
		if ($component_output){
			ob_end_clean();
			Application::$request_url=$original_url;
			$component=new $component_class_name;
			$component->show();			
			return;
		}
	}	
		
	public function get_controller_name(){		
		$this->get_controller($file,$controller,$action,$args);
		return $controller;
	}
	
	public function get_method_name(){		
		$this->get_controller($file,$controller,$action,$args);
		return $action;
	}
	
	private function get_controller(&$file,&$controller,&$action,&$args){
			
		/*@var $url Url*/		
		$url=Application::$request_url;
		
		$route=$url->get_param('route');
		
		if (empty($route)) $route = 'index';
		$cmd_path = Application::get_site_path().Configuration::get('paths','controllers').'/';
		
		$route = trim($route,'/\\');
		$parts = explode('/',$route);		
		
		/*		
		foreach ($parts as $part){
			
			
			if (is_dir($fullpath)){
				$cmd_path .= $part . '/';
				array_shift($parts);
				continue;
			}
			
			if (is_file($fullpath.'.php')){
				$controller = $part;
				array_shift($parts);
				break;
			}
			
		}
		*/
		
		/*
		
		ejemplo:
		
		admin/memebers/add
		
		seria 
		dir:
		admin/
		
		controller:
		member.php
		
		method
		
		add
		
		*/
		
				
		$cmd_path = realpath(Application::get_site_path().Configuration::get('paths','controllers'));
		
		$controller='index';
		
		do{
			$action=$controller;
			$fullpath = $cmd_path .'/' . implode('/',$parts);
			
			$controller=array_pop($parts);			
		}
		while (!is_file($fullpath.'.php') and count($parts)>0);
		
		if (!is_file($fullpath.'.php') and count(explode('/',$route))==1){
			$action=$controller;
			$controller='index';
			$fullpath = $cmd_path .'/index'  ;
		}
		
		/*
		if (empty($controller)) $controller='index';
		
		
		$action=array_shift($parts);
		
		if(empty($action)) $action='index';
		*/
		
		$file = realpath($fullpath.'.php');
		
		
		
		$args=$parts;
	}
	
}
?>