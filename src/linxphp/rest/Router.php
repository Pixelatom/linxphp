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
    public static function register($method,$route,callable $callback){
        // validates method
        $method = strtoupper($method);        
        if (!in_array($method,self::$methods)) throw new \Exception("Method {$method} invalid or not supported to be registered");
        
        self::$routes[$method][$route] = $callback;
    }
    
    
    public static function route(Request $request){
        
        if (!in_array($request->method,self::$methods)) throw new \Exception("Method {$request->method} invalid or not supported by the router");
        
        foreach(self::$routes[$request->method] as $route=>$handler){
            $pattern = '#^'.$route.'$#';

            // If we get a match we'll return the route and slice off the first
            // parameter match, as preg_match sets the first array item to the
            // full-text match of the pattern.
            if (preg_match($pattern, $request->route, $parameters))
            {
                $parameters = array_slice($parameters, 1);
                return call_user_func_array($handler, $parameters);
            }
        }
    }
}
