<?php
namespace linxphp\http;

class Request {
    public $method = '';
    public $https = false;
    public $port = 80;
    public $path = '';    
    public $route = '';
    public $params = array();
    
    public $protocol;
    public $time;
    
    public $accept;
    public $accept_charset;
    public $accept_encoding;
    public $accept_language;
    
    public $user_agent;
    public $connection;
    public $referer;
    
    public $auth_user;
    public $auth_password;
    
    public static function fromRoute($route){
        return new static(null,$route);
    }
    
    public function __construct( $method = null, $route = null, $params = null) {
        
        if (isset($_SERVER['HTTP_AUTHORIZATION'])){
            list($this->auth_user, $this->auth_password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));            
        }
        elseif (isset($_SERVER['PHP_AUTH_USER'])){
            $this->auth_user = $_SERVER['PHP_AUTH_USER'];
            $this->auth_password = $_SERVER['PHP_AUTH_PW'];
        }
        
        $this->protocol = isset($_SERVER['SERVER_PROTOCOL'])?$_SERVER['SERVER_PROTOCOL']:'HTTP/1.0';        
        $this->time     = isset($_SERVER['REQUEST_TIME_FLOAT'])?$_SERVER['REQUEST_TIME_FLOAT']:microtime(true);
        
        $this->accept           = isset($_SERVER['HTTP_ACCEPT'])?$_SERVER['HTTP_ACCEPT']:null;
        $this->accept_charset   = isset($_SERVER['HTTP_ACCEPT_CHARSET'])?$_SERVER['HTTP_ACCEPT_CHARSET']:null;
        $this->accept_encoding  = isset($_SERVER['HTTP_ACCEPT_ENCODING'])?$_SERVER['HTTP_ACCEPT_ENCODING']:null;
        $this->accept_language  = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])?$_SERVER['HTTP_ACCEPT_LANGUAGE']:null;
        
        $this->user_agent   = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
        $this->connection   = isset($_SERVER['HTTP_CONNECTION'])?$_SERVER['HTTP_CONNECTION']:null;
        $this->referer      = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
        
        
        
        if (!empty($method)){
            $this->method = $method;
        }
        else{
            $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        }
        
        if (!empty($route)){
            
            if ($route[0]!='/')
                $route = '/'.$route;
            
            $this->route = $route;
        }
        else{
            if (!isset($_SERVER['PATH_INFO'])){
                $path = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF']);
                $this->route = (empty($path))? '/': $path;
            }
            else{
                $this->route = $_SERVER['PATH_INFO'];
            }
        }
        
        $this->https = !(empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off');
        
        $this->port = $_SERVER['SERVER_PORT'];
        
        $this->path = $_SERVER['SCRIPT_NAME'];
        
        if ($params!==null){
            $this->params = $params;
        }
        else{
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
    
    public static function urlRewriteEnabled(){
        return (strpos($_SERVER["REQUEST_URI"],basename($_SERVER['SCRIPT_NAME']))===false);
    }
    
    public function url(){
        $scheme   = 'http'. (($this->https) ? 's':'') . '://' ; 
        $host     = $_SERVER["SERVER_NAME"];
        $port     = ($this->port!=80) ? ':' . $this->port : ''; 
        $path     = $this->path . (($this->route != '/')? $this->route : ''); 
        $query    = ($this->method == 'GET') ? '?' . http_build_query($this->params) : ''; 
        
        // detect if url was rewritten
        if (self::urlRewriteEnabled()){            
            $path = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $path);
            $path = (preg_replace('{(/)\1+}','$1',$path)); 
        }
        
        $url = "$scheme$host$port$path$query";
        return new \linxphp\common\URL($url);
    }
    
}
