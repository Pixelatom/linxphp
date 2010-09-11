<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias. *
 * Licensed under MIT License.
 */
 
include_once('iapplicationrouter.php');

/**
 * The Application Router is the default router of the Framework.
 * This class will determinate which controller should be exeuted based on the
 * value of the Application::$requested_url (which is based on the URL requested
 * by the browser).
 *
 * In case the router doesn't find a Controller will execute the error 404 event
 *
 */
class ApplicationRouter implements IApplicationRouter {
    
    public $controller=null;
    public $method=null;
    public $file=null;
    public $args=null;

    protected $router_param = 'route';
    
    public function get_router_param(){
        return $this->router_param;
    }
    public function set_router_param($name){
        $this->router_param = $name;
    }
    /**
     * This function search for a controller and execute it.
     *
     */
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
        
        $route=strtolower($url->get_param($this->router_param));
        
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
        
        /* calculamos el valor de controller */
        $icontroller=str_ireplace(realpath($cmd_path) ,'',$file);
        $icontroller=str_replace('\\','/',$icontroller);
        $icontroller=explode('/',$icontroller);
        array_pop($icontroller);
        $icontroller=implode('/',$icontroller);
        $icontroller.='/'.$controller;
        
        $this->args=implode('/',$args);
        $this->file=$file;
        $this->controller=$icontroller;
        $this->action=$action;
        
        
        /*
        die($icontroller);
        */
        /* Creamos el controlador y ejecutamos el metodo */
        
        Event::run('system.execute');
        
        $controller = new $class();
        if (count($args)==0)
        $controller->$action();        
        else        
        call_user_func_array(array($controller, $action), $args);
        
        Event::run('system.post_routing');
    }
    
    protected function not_found(){
        Event::run('system.404');
        die();
    }
}
?>