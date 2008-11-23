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
    
    public $controller=null;
    public $method=null;
    public $file=null;
    public $args=null;
    
    public function delegate(){
	Event::run('system.routing');
	
        $file=null;
        $controller=null;
        $action=null;
        $args=array();
        
        $this->args=$args;
        $this->file=$file;
        $this->controller=$controller;
        $this->action=$action;
        
        /* start get controller */
        /*@var $url Url*/		
	$url=Application::$request_url;
	
	$route=$url->get_param('route');
	
	if (empty($route)) $route = 'index';
	$cmd_path = Application::get_site_path().Configuration::get('paths','controllers').'/';
	
	$route = trim($route,'/\\');
	$parts = explode('/',$route);		
	
	
	$cmd_path = realpath(Application::get_site_path().Configuration::get('paths','controllers'));
		
        
        $param=null;
	$controller='index';
		
        # recorre el route hasta que encuentra un archivo o se acaba el string.
	do{ 
            # controller pasa a ser action
            $action=$controller;
            
            # se arma el path al archivo
	    $fullpath = $cmd_path .'/' . implode('/',$parts);
            
	    # se extrae la ultima parte del route
	    $controller=array_pop($parts);
            $args[]=$controller;
	}
	while (!is_file($fullpath.'.php') and count($parts)>0);
        
		
        # si no se encuentra algun archivo, se va a llamar al archivo por defecto 'index.php'
	if (!is_file($fullpath.'.php') /*and count(explode('/',$route))==1*/){
	    $action=$controller;
	    $controller='index';
	    $fullpath = $cmd_path .'/index'  ;
            
            # el ultimo elemento de args es el action
            array_pop($args);
	}
        else{
            # los dos ultimos elementos de args son el action y el controller.
            array_pop($args);
            array_pop($args);
        }
		
	$file = realpath($fullpath.'.php');
        $args=array_reverse($args);
		
        /*
         //debbuging
        echo "file: $file<br />";
		echo "controller: $controller<br />";
        echo "action: $action<br />";        
        */
        /*
        var_dump($args);
        */
        
        /* end get controller */        
        
        # incluye el archivo del controller
	if (!is_readable($file)){ 			
	    $this->not_found();			
	}
	else{			
	    include_once($file);
	}
	
	# inicializa la clase
	$class = ucfirst($controller).'Controller';		
       
        
        
        if (!method_exists($class,$action)) $this->not_found();
       
        
        $method = new ReflectionMethod($class, $action);
        
        if (!$method->isPublic()){
            $this->not_found();
        }
        
        $paramsinfo = $method->getParameters();
        
        $requiredparams = 0;
        
        foreach ($paramsinfo as $i => $param) { 
            if (!$param->isOptional()){
                $requiredparams++;
            }         
        }
        
        if (count($args)<$requiredparams or count($args)>count($paramsinfo)){
            $this->not_found();
        }
        
        $this->args=$args;
        $this->file=$file;
        $this->controller=$controller;
        $this->action=$action;
        
        /* Creamos el controlador y ejecutamos el metodo */
        
	Event::run('system.execute');
	
        $controller = new $class();
        if (count($args)==0)
        $controller->$action();        
        else
        call_user_method_array($action,$controller,$args);	
	
	Event::run('system.post_routing');
    }
    
    protected function not_found(){
	Event::run('system.404');
	if (!headers_sent()){
	    header(' ', true, 404);
	    header('Status: 404 Not Found');
	    header('HTTP/1.0 404 Not Found');	
	}
	echo "Page not found";
	die();
    }
}
?>