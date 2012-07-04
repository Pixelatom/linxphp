<?php
class MemcacheEngine implements iCacheEngine{

    // Memcache object
    protected $connection;
    protected $prefix = '';

    public function __construct($settings) {
        if (!empty($settings['prefix']))
            $this->prefix = $settings['prefix'];
        
        $this->connection = new Memcache;
        foreach ($settings['servers'] as $host){
            $this->addServer($host);
        }
    }

    function store($key, $data, $ttl) {
        $key = md5($key);
        return $this->connection->set($this->prefix.$key,$data,0,$ttl);
    }

    function fetch($key) {
        $key = md5($key);
        return $this->connection->get($this->prefix.$key);
    }

    function delete($key) {
        $key = md5($key);
        return $this->connection->delete($this->prefix.$key);
    }

    function addServer($host,$port = 11211, $weight = 10) {
        $this->connection->addServer($host,$port,true,$weight);
    }

}