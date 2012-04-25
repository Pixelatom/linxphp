<?php
class MemcacheEngine implements iCacheEngine{

    // Memcache object
    protected $connection;

    public function __construct($settings) {
        $this->connection = new Memcache;
        foreach ($settings['servers'] as $host){
            $this->addServer($host);
        }
    }

    function store($key, $data, $ttl) {
        $key = md5($key);
        return $this->connection->set($key,$data,0,$ttl);
    }

    function fetch($key) {
        $key = md5($key);
        return $this->connection->get($key);
    }

    function delete($key) {
        $key = md5($key);
        return $this->connection->delete($key);
    }

    function addServer($host,$port = 11211, $weight = 10) {
        $this->connection->addServer($host,$port,true,$weight);
    }

}