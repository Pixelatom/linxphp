<?php
namespace linxphp\rest;

class Request {
    public $method = '';
    public $https = false;
    public $uri = '';    
    public $route = '';
    public $params = array();
    
    public function __construct() {
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        
        $this->https = !(empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off');
        
        if (isset($_SERVER['REQUEST_URI'])){
            $this->uri =  $_SERVER['REQUEST_URI'];
        }
        else{
            if (isset($_SERVER['argv'])) {
                $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['argv'][0];
            }
            elseif (isset($_SERVER['QUERY_STRING'])) {
                $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
            }
            else {
                $uri = $_SERVER['SCRIPT_NAME'];
            }
            
            // Prevent multiple slashes to avoid cross site requests via the Form API.
            $uri = '/' . ltrim($uri, '/');

            $this->uri = $uri;
        }
        
        if (!isset($_SERVER['PATH_INFO'])){
            $path = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF']);
            $this->route = (empty($path))? '/': $path;
        }
        else{
            $this->route = $_SERVER['PATH_INFO'];
        }
        
        switch ($this->method){
            case 'POST':
                $this->params = $_POST;
                break;
            case 'GET':
                $this->params = $_GET;
                break;
            default:
                $this->params = isset($_SERVER['QUERY_STRING']) ? parse_str($_SERVER['QUERY_STRING']) : array();
        }
        
    }
    
}
