<?php
namespace linxphp\http;

class Request {
    public $method = '';
    public $https = false;
    public $port = 80;
    public $path = '';    
    public $route = '';
    public $params = array();
    public $headers = array();
    
    public $protocol;
    public $time;
    
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
        
        foreach ($_SERVER as $name => $value){ 
            switch($name){
                case "CONTENT_LENGTH":
                    $headers["Content-Length"] = $value; 
                    break;
                case "CONTENT_TYPE": 
                    $headers["Content-Type"] = $value; 
                    break;
                case 'IF_MODIFIED_SINCE':
                    $headers['If-Modified-Since'] = $value;
                    break;
                default:
                    if (substr($name, 0, 5) == 'HTTP_'){ 
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))); 
                        $headers[$name] = $value; 
                    }
            }
        }
 
        $this->headers = $headers;
        
        
        $this->protocol = isset($_SERVER['SERVER_PROTOCOL'])?$_SERVER['SERVER_PROTOCOL']:'HTTP/1.0';        
        
        // parses request time
        $t     =  isset($_SERVER['REQUEST_TIME_FLOAT'])?  $_SERVER['REQUEST_TIME_FLOAT']:microtime(true);        
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $this->time = new \DateTime( date('Y-m-d H:i:s.'.$micro,$t) );
        
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
    
    
    /**
     * returns an array of values with their quality taken from the accept header
     * @param type $accept_header
     * @return type 
     */
    protected function parseAccept($accept_header)
    {
        if (!isset($this->headers[$accept_header])) return array();
        
        $acceptHeader = $this->headers[$accept_header];
        $acceptParts = explode(',', $acceptHeader);
        $acceptList = array();
        foreach ($acceptParts as $k => &$acceptPart) {
            $parts = explode(';q=', trim($acceptPart));
            $provided = array_shift($parts);
            $quality = array_shift($parts) ? : (10000 - $k) / 10000;
            $acceptList[$provided] = $quality;
        }
        arsort($acceptList);

        return array_keys($acceptList);
    }
}
