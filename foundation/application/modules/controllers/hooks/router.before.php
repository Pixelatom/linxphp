<?php

use linxphp\common\Event;
use linxphp\common\Configuration;
use linxphp\common\ClassLoader;
    
use app\controllers\Controller;

Event::add('Router.before',function(linxphp\http\Response $response,linxphp\http\Request $request){
    $file=null;
    $controller=null;
    $action=null;
    $args=array();

    $route = $request->route;

    if (empty($route)) $route = 'index';
    
    $route = trim($route,'/\\');
    $parts = explode('/',$route);		

    $param=null;
    $controller='index';
    
    // build path to the controllers folder
    $controllers_path = \linxphp\implementation\Application::instance()->path();    
    $controllers_path .= \linxphp\implementation\Application::instance()->configuration()->get('paths','controllers','controllers');   
    $controllers_path = realpath($controllers_path);
    
    # find the file using the different parts of the route as path
    do{         
        $action = $controller; 
        
        $fullpath = $controllers_path .'/' . implode('/',$parts);
        
        $controller=array_pop($parts);
        $args[]=$controller;
    }
    while (!is_file($fullpath.'.php') and count($parts)>0);


    # if a file wasn't found then we'll try with the default controller called 'index'
    if (!is_file($fullpath.'.php')){
        $action=$controller;
        $controller='index';
        $fullpath = $controllers_path .'/index'  ;
        
        array_pop($args); // last element from route is the action
    }
    else{
        # last two elements from route are controller and action
        array_pop($args);
        array_pop($args);
    }

    $class  = Controller::mapController($fullpath);    
    $file   = $fullpath.'.php';
    $args   = array_reverse($args);
    
    //debbuging
    //echo "file: $file<br />";
    //echo "controller: $controller<br />";
    //echo "action: $action<br />";        
    
    /* end get controller */        

    // includes controller file
    if (!is_readable($file)) return; // 404 not found    
    include_once($file);
    
    // validates the class     
    if (!is_subclass_of($class, 'app\controllers\Controller'))  return; // 404 not found
    
    // validates the method    
    if (!method_exists($class,$action)) return; // 404 not found
    $method = new ReflectionMethod($class, $action);
    if (!$method->isPublic())  return; // 404 not found
    
    // we'll test that the action method has the correct number of arguments    
    $paramsinfo = $method->getParameters();
    $requiredparams = 0;
    foreach ($paramsinfo as $i => $param) { 
        if (!$param->isOptional()) $requiredparams++;
    }
    if (count($args)<$requiredparams or count($args)>count($paramsinfo)) return; // 404 not found

        
    /* Creates Controller and executes the action method */
    $response->setStatus(200);
        
    $controller = new $class();
    if (count($args)==0)
        $resp = $controller->$action();        
    else        
        $resp = call_user_func_array(array($controller, $action), $args);

    if (is_object($resp) and $resp instanceof \linxphp\http\Response){
        $response = $resp;
    }
});
