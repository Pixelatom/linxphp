<?php
namespace linxphp\http\rest;

use linxphp\http\Request;
use linxphp\http\Response;

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
        
        if (empty($methods)) $methods = self::$methods;
        
        if (!is_array($methods))
            $methods = array($methods);
        
        $route = new Route($methods,$route,$callback);
        self::$routes[] = $route;
        return $route;
    }
    
    
    public static function route(Request $request = null){
        
        if ($request === null){
            $request = new Request();
        }
        
        // default response
        $response = Response::create('', Response::ST_NOT_FOUND);
        
        
        foreach(self::$routes as $route){
            /*@var $route Route*/

            $pattern = preg_quote($route->getRoute(),'#');

            // generates regexp for required rest of the path wildcard
            $pattern = str_replace('\?\+', '(.+)', $pattern);

            // generates regexp for optional rest of the path wildcard
            $pattern = str_replace('/\*\+', '(?:/(.*)){0,1}', $pattern);            

            // generates regexp for required section wildcard
            $pattern = str_replace('\?', '([^/]+)', $pattern);

            // generates regexp for optional section wildcard
            $pattern = str_replace('/\*', '(?:/([^/]*)){0,1}', $pattern);

            // hacemos el ultimo / opcional
            $pattern .= '/{0,1}';

            $pattern = '#^'.$pattern.'$#i';


            // check request url
            if (preg_match($pattern, $request->route, $parameters)){
                // check request method
                if ($route->supportMethod($request->method)){                        
                    $parameters = array_slice($parameters, 1);
                    $response = call_user_func_array($route->getHandler(), $parameters);
                }
                else{
                    $response = Response::create('', Response::ST_METHOD_NOT_ALLOWED);
                }
            }
        }
        
        
        
        if (is_object($response) and $response instanceof \linxphp\http\Response){
            
            // dispatch an event to give the system the possibility to modify the response
            \linxphp\common\Event::run('Response.'.$response->status, $response);
            
            $response->send();
        }

        return $response;
        
    }
}
