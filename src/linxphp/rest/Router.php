<?php
namespace linxphp\rest;

class Router {
   /**
    * All of the routes that have been registered.
    *
    * @var array
    */
    protected static $routes = array(
            'GET'    => array(),
            'POST'   => array(),
            'PUT'    => array(),
            'DELETE' => array(),
            'PATCH'  => array(),
            'HEAD'   => array(),
    );
   
    /**
     * An array of HTTP request methods.
     *
     * @var array
     */
    public static $methods = array('GET', 'POST', 'PUT', 'DELETE', 'PATCH','HEAD');
    
    /**
     *
     * @param string $method
     * @param string $route
     * @param callable $callback
     * @throws \Exception 
     */
    public static function register($method,$route,$callback){
        // validates method
        $method = strtoupper($method);        
        if (!in_array($method,self::$methods)) throw new \Exception("Method invalid or not supported to be registered");
        
        
        
        self::$routes[$method] = $route;
    } 
}
