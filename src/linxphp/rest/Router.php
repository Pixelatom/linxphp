<?php
namespace linxphp\rest;

class Router {
   /**
    * All of the routes that have been registered.
    *
    * @var array
    */
    protected static $routes = array();
   
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
    public static function register($methods,$route,callable $callback){
        if (!is_array($methods))
            $methods = array($methods);
        
        self::$routes[] = new Route($methods,$route,$callback);
    }
    
    
    public static function route(Request $request = null){
        
        if ($request === null){
            $request = new Request();
        }
        
        if (!in_array($request->method,self::$methods)) throw new \Exception("Method {$request->method} invalid or not supported by the router");
        
        foreach(self::$routes as $route){
            
            //$pattern = preg_quote($route->getRoute(),'#');
            $pattern = $route->getRoute();
            
            // generates regexp for required section wildcard
            $pattern = preg_replace('/\?/', '([^/]+)', $pattern);
            
            // generates regexp for optional section wildcard
            $pattern = preg_replace('#/\*#', '(?:/([^/]*)){0,1}', $pattern);
            
            // generates regexp for required rest of the path wildcard
            $pattern = preg_replace('/\?\+/', '(.+)', $pattern);
            
            // generates regexp for optional rest of the path wildcard
            $pattern = preg_replace('/\*\+/', '(?:/(.*)){0,1}', $pattern);
            
            // hacemos el ultimo / opcional
            $pattern .= '/{0,1}';
            
            $pattern = '#^'.$pattern.'$#i';
            
            // If we get a match we'll return the route and slice off the first
            // parameter match, as preg_match sets the first array item to the
            // full-text match of the pattern.
            if (preg_match($pattern, $request->route, $parameters))
            {
                $parameters = array_slice($parameters, 1);
                return call_user_func_array($route->getHandler(), $parameters);
            }
        }
        
        /*@TODO: response 404 status*/
        echo 'not found';
    }
}
