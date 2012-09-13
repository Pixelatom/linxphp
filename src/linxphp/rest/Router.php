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
     */
    public static function register($method,$uri_pattern,$callback){

    } 
}
