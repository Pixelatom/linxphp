<?php
namespace linxphp\common;

class URL {
    protected $scheme = 'http';
    protected $host = '';
    protected $port = '';    
    protected $user = '';
    protected $password = '';
    protected $path = '';
    protected $query = array();
    protected $fragment = '';
    
    public static function current(){
        
        $url = 'http';
        if (!(empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off')) {$url .= "s";}
        $url .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
        $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
        $url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        
        
        return new static($url);
    }
    
    public function __construct($url){
        $parsed_url = parse_url($url);
        
        $this->scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] : 'http'; 
        $this->host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
        $this->port     = isset($parsed_url['port']) ? $parsed_url['port'] : ''; 
        $this->user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
        $this->pass     = isset($parsed_url['pass']) ? $parsed_url['pass']  : '';         
        $this->path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
        if(isset($parsed_url['query'])) 
            parse_str($parsed_url['query'],$this->query); 
        else 
            $this->query = array(); 
        $this->fragment = isset($parsed_url['fragment']) ? $parsed_url['fragment'] : ''; 
    }
    
    public function __toString()
    {
        $scheme   = !empty($this->scheme) ? $this->scheme . '://' : ''; 
        $host     = !empty($this->host) ? $this->host : ''; 
        $port     = !empty($this->port) ? ':' . $this->port : ''; 
        $user     = !empty($this->user) ? $this->user : ''; 
        $pass     = !empty($this->pass) ? ':' . $this->pass  : ''; 
        $pass     = ($user || $pass) ? "$pass@" : ''; 
        $path     = !empty($this->path) ? $this->path : ''; 
        $query    = !empty($this->query) ? '?' . http_build_query($this->query) : ''; 
        $fragment = !empty($this->fragment) ? '#' . $this->fragment : ''; 
        return "$scheme$user$pass$host$port$path$query$fragment"; 
    }
    
    public function param($name,$value=null){
        if (!is_null($value)){
            $this->query[$name] = $value;
            return $this;
        }
        else{
            return $this->query[$name];
        }
    }
    
    public function clear(){
        $this->query = array();
        return $this;
    }
    
    public function scheme($scheme=null){
        if (!is_null($scheme)){
            $this->scheme = $scheme;
            return $this;
        }
        else{
            return $this->scheme;
        }
    }
    public function host($host=null){
        if (!is_null($host)){
            $this->host = $host;
            return $this;
        }
        else{
            return $this->host;
        }
    }
    public function port($port=null){
        if (!is_null($port)){
            $this->port = $port;
            return $this;
        }
        else{
            return $this->port;
        }
    }
    public function user($user=null){
        if (!is_null($user)){
            $this->user = $user;
            return $this;
        }
        else{
            return $this->user;
        }
    }
    public function password($password=null){
        if (!is_null($password)){
            $this->password = $password;
            return $this;
        }
        else{
            return $this->password;
        }
    }
    public function path($path=null){
        if (!is_null($path)){
            $this->path = $path;
            return $this;
        }
        else{
            return $this->path;
        }
    }
    public function query($query=null){
        if (!is_null($query)){
            $this->query = $query;
            return $this;
        }
        else{
            return $this->query;
        }
    }
    public function fragment($fragment=null){
        if (!is_null($fragment)){
            $this->fragment = $fragment;
            return $this;
        }
        else{
            return $this->fragment;
        }
    }
    
    
}