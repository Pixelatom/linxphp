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
    
    public function getHandler(){
        return $this->handler;
    }
    
    public function setHandler(callable $handler){
        $this->handler = $handler;
        return $this;
    }
    
    public $auth_realm;
    protected $auth_handler;
    
    /**
     * sets authentication handler for this route
     * must receive 2 params:
     * 1: username
     * 2: password
     * @param \linxphp\http\rest\callable $handler
     */    
    public function setAuthentication($realm,callable $handler){
        $this->auth_realm = $realm; 
        $this->auth_handler = $handler;
        return $this;
    }
    
    protected $last_modified_handler = null;
    
    public function setLastModified(callable $handler){
        $this->last_modified_handler = $handler;
        return $this;
    }
    
    public function getLastModified(){
        if (!is_callable($this->last_modified_handler)) return null;
        return call_user_func($this->last_modified_handler);
    }
    
    public function testMethod($method){
        return (count($this->getMethods())==0 or in_array($method, $this->getMethods()));
    }
    
    public function testAuthentication($username, $password){
        if (!is_callable($this->auth_handler)) return true;
        return call_user_func($this->auth_handler,$username,$password);
    }
    
    
}