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
    
    public function __construct($url){
        $parsed_url = parse_url($url);
        $this->scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] : 'http'; 
        $this->host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
        $this->port     = isset($parsed_url['port']) ? $parsed_url['port'] : ''; 
        $this->user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
        $this->pass     = isset($parsed_url['pass']) ? $parsed_url['pass']  : '';         
        $this->path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
        $this->query    = isset($parsed_url['query']) ? parse_str($parsed_url['query']) : ''; 
        $this->fragment = isset($parsed_url['fragment']) ? $parsed_url['fragment'] : ''; 
        
    }
    
    public function __toString()
    {
        return $this->foo;
    }
}