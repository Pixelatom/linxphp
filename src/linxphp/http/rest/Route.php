<?php
namespace linxphp\http\rest;

/**
 * A router is the group of conditions a Request must meet to be executed
 */
class Route {
    // methods this routes will work with
    protected $methods = array();
    protected $route;
    protected $handler;
    
    function __construct(array $methods, $route, callable $handler) {
        $this->setMethods($methods);
        $this->setRoute($route);
        $this->setHandler($handler);
    }
    
    public function getMethods(){
        return $this->methods;
    }
    
    public function setMethods(array $methods){
        // validates method
        foreach ($methods as &$method){
            $method = strtoupper($method);        
            if (!in_array($method,Router::$methods)) throw new \Exception("Method {$method} invalid or not supported to be registered");
        }
        $this->methods = $methods;        
        return $this;
    }
    
    public function getRoute(){
        return $this->route;
    }
    
    public function setRoute($route){
        if ($route[0]!='/') $route = '/'.$route;
        $this->route = $route;
        return $this;
    }
    
    /**
     * Return the route handler
     * @return callable
     */
    public function getHandler(){
        return $this->handler;
    }
    
    /**
     * Sets the handler function for this route
     * @param callable $handler
     * @return \linxphp\http\rest\Route
     */
    public function setHandler(callable $handler){
        $this->handler = $handler;
        return $this;
    }    
   
    /**
     * checks if the method passed is allowed for this route
     * @param string $method
     * @return boolean
     */
    public function allowMethod($method){
        return (count($this->getMethods())==0 or in_array($method, $this->getMethods()));
    }
    
    
}